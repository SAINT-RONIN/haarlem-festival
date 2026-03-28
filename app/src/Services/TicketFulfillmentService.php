<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Filters\EventSessionFilter;
use App\DTOs\Schedule\SessionWithEvent;
use App\DTOs\Tickets\TicketDocumentData;
use App\DTOs\Tickets\TicketEmailAttachment;
use App\DTOs\Tickets\TicketEmailMessage;
use App\DTOs\Tickets\TicketRecipient;
use App\Exceptions\TicketDeliveryException;
use App\Exceptions\TicketEmailDeliveryException;
use App\Exceptions\TicketPdfGenerationException;
use App\Exceptions\RepositoryException;
use App\Infrastructure\Interfaces\IEmailService;
use App\Infrastructure\PathResolver;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\Interfaces\ITicketRepository;
use App\Repositories\Interfaces\IUserAccountRepository;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\Tickets\Interfaces\IQrCodeGenerator;
use App\Tickets\Interfaces\ITicketPdfGenerator;
use App\Tickets\TicketCodeGenerator;

/**
 * Creates durable ticket records and delivers them after successful payment.
 */
class TicketFulfillmentService implements ITicketFulfillmentService
{
    public function __construct(
        private readonly IOrderRepository $orderRepository,
        private readonly IOrderItemRepository $orderItemRepository,
        private readonly IEventSessionRepository $eventSessionRepository,
        private readonly ITicketRepository $ticketRepository,
        private readonly IMediaAssetRepository $mediaAssetRepository,
        private readonly IUserAccountRepository $userAccountRepository,
        private readonly IEmailService $emailService,
        private readonly IQrCodeGenerator $qrCodeGenerator,
        private readonly ITicketPdfGenerator $ticketPdfGenerator,
        private readonly TicketCodeGenerator $ticketCodeGenerator,
    ) {
    }

    public function fulfillPaidOrder(
        int $orderId,
        ?string $fallbackEmail = null,
        ?string $fallbackFirstName = null,
        ?string $fallbackLastName = null,
    ): void {
        $order = $this->requireOrder($orderId);
        if ($order->ticketEmailSentAtUtc !== null) {
            return;
        }

        try {
            $recipient = $this->resolveRecipient($order, $fallbackEmail, $fallbackFirstName, $fallbackLastName);
            $this->persistRecipientIfNeeded($order, $recipient);

            $orderItems = $this->loadTicketableOrderItems($orderId);
            $sessionsById = $this->loadSessionsById($orderItems);
            $ticketsByItemId = $this->ensureTicketsForOrderItems($orderItems);
            $attachments = $this->ensurePdfAttachments($order, $recipient, $orderItems, $sessionsById, $ticketsByItemId);

            if ($attachments === []) {
                throw new TicketDeliveryException('No ticket attachments were generated for the paid order.');
            }

            $this->sendTicketEmail($order, $recipient, $orderItems, $sessionsById, $attachments);
        } catch (TicketEmailDeliveryException $error) {
            throw $error;
        } catch (\Throwable $error) {
            $this->orderRepository->markTicketEmailFailed($order->orderId, $this->truncateErrorMessage($error->getMessage()));

            if ($error instanceof TicketDeliveryException) {
                throw $error;
            }

            throw new TicketDeliveryException('Paid order tickets could not be fulfilled.', 0, $error);
        }
    }

    private function requireOrder(int $orderId): Order
    {
        $order = $this->orderRepository->findById($orderId);
        if ($order === null) {
            throw new TicketDeliveryException('Paid order could not be found.');
        }

        return $order;
    }

    private function resolveRecipient(
        Order $order,
        ?string $fallbackEmail,
        ?string $fallbackFirstName,
        ?string $fallbackLastName,
    ): TicketRecipient {
        $user = $this->userAccountRepository->findById($order->userAccountId);
        $email = $this->firstNonEmpty($order->ticketRecipientEmail, $fallbackEmail, $user?->email);

        if ($email === null) {
            throw new TicketDeliveryException('Paid order is missing a ticket recipient email address.');
        }

        $firstName = $this->firstNonEmpty($order->ticketRecipientFirstName, $fallbackFirstName, $user?->firstName) ?? '';
        $lastName = $this->firstNonEmpty($order->ticketRecipientLastName, $fallbackLastName, $user?->lastName) ?? '';
        $displayName = trim($firstName . ' ' . $lastName);

        return new TicketRecipient(
            email: $email,
            firstName: $firstName,
            lastName: $lastName,
            displayName: $displayName !== '' ? $displayName : $email,
        );
    }

    private function persistRecipientIfNeeded(Order $order, TicketRecipient $recipient): void
    {
        if (
            $order->ticketRecipientFirstName === $recipient->firstName
            && $order->ticketRecipientLastName === $recipient->lastName
            && $order->ticketRecipientEmail === $recipient->email
        ) {
            return;
        }

        $this->orderRepository->updateTicketRecipient(
            $order->orderId,
            $recipient->firstName,
            $recipient->lastName,
            $recipient->email,
        );
    }

    /**
     * @return OrderItem[]
     */
    private function loadTicketableOrderItems(int $orderId): array
    {
        $items = array_values(array_filter(
            $this->orderItemRepository->findByOrderId($orderId),
            static fn(OrderItem $item) => $item->eventSessionId !== null && $item->eventSessionId > 0 && $item->quantity > 0,
        ));

        if ($items === []) {
            throw new TicketDeliveryException('Paid order does not contain any ticketable sessions.');
        }

        return $items;
    }

    /**
     * @param OrderItem[] $orderItems
     * @return array<int, SessionWithEvent>
     */
    private function loadSessionsById(array $orderItems): array
    {
        $sessionIds = array_values(array_unique(array_map(
            static fn(OrderItem $item) => (int)$item->eventSessionId,
            $orderItems,
        )));

        $result = $this->eventSessionRepository->findSessions(new EventSessionFilter(
            sessionIds: $sessionIds,
            includeCancelled: true,
            limit: count($sessionIds),
        ));

        $sessionsById = [];
        foreach ($result->sessions as $session) {
            $sessionsById[$session->eventSessionId] = $session;
        }

        foreach ($sessionIds as $sessionId) {
            if (!isset($sessionsById[$sessionId])) {
                throw new TicketDeliveryException("Ticket session {$sessionId} could not be loaded.");
            }
        }

        return $sessionsById;
    }

    /**
     * @param OrderItem[] $orderItems
     * @return array<int, Ticket[]>
     */
    private function ensureTicketsForOrderItems(array $orderItems): array
    {
        $existingTickets = $this->ticketRepository->findByOrderItemIds(array_map(
            static fn(OrderItem $item) => $item->orderItemId,
            $orderItems,
        ));

        $ticketsByItemId = $this->groupTicketsByOrderItemId($existingTickets);

        foreach ($orderItems as $orderItem) {
            $currentTickets = $ticketsByItemId[$orderItem->orderItemId] ?? [];

            while (count($currentTickets) < $orderItem->quantity) {
                $currentTickets[] = $this->createTicket($orderItem->orderItemId);
            }

            $ticketsByItemId[$orderItem->orderItemId] = array_slice($currentTickets, 0, $orderItem->quantity);
        }

        return $ticketsByItemId;
    }

    /**
     * @param Ticket[] $tickets
     * @return array<int, Ticket[]>
     */
    private function groupTicketsByOrderItemId(array $tickets): array
    {
        $grouped = [];

        foreach ($tickets as $ticket) {
            $grouped[$ticket->orderItemId][] = $ticket;
        }

        return $grouped;
    }

    private function createTicket(int $orderItemId): Ticket
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $ticketCode = $this->ticketCodeGenerator->generate();

            try {
                $ticketId = $this->ticketRepository->create($orderItemId, $ticketCode);

                return new Ticket(
                    ticketId: $ticketId,
                    orderItemId: $orderItemId,
                    ticketCode: $ticketCode,
                    isScanned: false,
                    scannedAtUtc: null,
                    scannedByUserId: null,
                    pdfAssetId: null,
                );
            } catch (RepositoryException $error) {
                if (!$this->isDuplicateTicketCodeError($error)) {
                    throw $error;
                }
            }
        }

        throw new TicketDeliveryException('A unique ticket code could not be generated.');
    }

    private function isDuplicateTicketCodeError(RepositoryException $error): bool
    {
        return str_contains(strtolower($error->getMessage()), 'duplicate');
    }

    /**
     * @param OrderItem[] $orderItems
     * @param array<int, SessionWithEvent> $sessionsById
     * @param array<int, Ticket[]> $ticketsByItemId
     * @return TicketEmailAttachment[]
     */
    private function ensurePdfAttachments(
        Order $order,
        TicketRecipient $recipient,
        array $orderItems,
        array $sessionsById,
        array $ticketsByItemId,
    ): array {
        $attachments = [];

        foreach ($orderItems as $orderItem) {
            $session = $sessionsById[(int)$orderItem->eventSessionId];
            $tickets = $ticketsByItemId[$orderItem->orderItemId] ?? [];
            $ticketCount = count($tickets);

            foreach (array_values($tickets) as $index => $ticket) {
                $attachments[] = $this->ensureTicketPdfAttachment(
                    $order,
                    $recipient,
                    $session,
                    $ticket,
                    $index + 1,
                    $ticketCount,
                );
            }
        }

        return $attachments;
    }

    private function ensureTicketPdfAttachment(
        Order $order,
        TicketRecipient $recipient,
        SessionWithEvent $session,
        Ticket $ticket,
        int $position,
        int $totalForItem,
    ): TicketEmailAttachment {
        $existingAttachment = $this->loadExistingPdfAttachment($ticket);
        if ($existingAttachment !== null) {
            return $existingAttachment;
        }

        $document = $this->buildTicketDocument($order, $recipient, $session, $ticket, $position, $totalForItem);
        $qrCode = $this->qrCodeGenerator->generate($ticket->ticketCode);
        $pdfBinary = $this->ticketPdfGenerator->generatePdf($document, $qrCode);
        $fileName = 'Haarlem-Festival-Ticket-' . $ticket->ticketCode . '.pdf';
        $absolutePath = $this->writePdfFile($fileName, $pdfBinary);
        $relativePath = PathResolver::getTicketAssetRelativePath($fileName);
        $assetId = $this->storePdfAsset($ticket, $relativePath, $fileName, $absolutePath);
        $this->ticketRepository->updatePdfAssetId($ticket->ticketId, $assetId);

        return new TicketEmailAttachment($absolutePath, $fileName);
    }

    private function loadExistingPdfAttachment(Ticket $ticket): ?TicketEmailAttachment
    {
        if ($ticket->pdfAssetId === null) {
            return null;
        }

        $asset = $this->mediaAssetRepository->findById($ticket->pdfAssetId);
        if ($asset === null) {
            return null;
        }

        $absolutePath = $this->resolveAbsolutePublicPath($asset->filePath);
        if (!is_file($absolutePath)) {
            return null;
        }

        $displayName = $asset->originalFileName !== '' ? $asset->originalFileName : basename($absolutePath);

        return new TicketEmailAttachment($absolutePath, $displayName);
    }

    private function buildTicketDocument(
        Order $order,
        TicketRecipient $recipient,
        SessionWithEvent $session,
        Ticket $ticket,
        int $position,
        int $totalForItem,
    ): TicketDocumentData {
        $ticketLabel = $totalForItem > 1 ? "Ticket {$position} of {$totalForItem}" : 'Entry Ticket';

        return new TicketDocumentData(
            ticketCode: $ticket->ticketCode,
            ticketLabel: $ticketLabel,
            orderReference: $order->orderNumber,
            recipientName: $recipient->displayName,
            eventTitle: $session->eventTitle,
            eventTypeName: $session->eventTypeName,
            venueName: $session->venueName ?? 'Venue to be announced',
            sessionDateLabel: $session->startDateTime->format('l j F Y'),
            sessionTimeLabel: $this->formatSessionTime($session),
        );
    }

    private function formatSessionTime(SessionWithEvent $session): string
    {
        $start = $session->startDateTime->format('H:i');

        if ($session->endDateTime === null) {
            return $start;
        }

        return $start . ' - ' . $session->endDateTime->format('H:i');
    }

    private function writePdfFile(string $fileName, string $pdfBinary): string
    {
        $directory = PathResolver::getTicketAssetPath();
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new TicketPdfGenerationException('Ticket PDF directory could not be created.');
        }

        $absolutePath = $directory . '/' . $fileName;
        if (file_put_contents($absolutePath, $pdfBinary) === false) {
            throw new TicketPdfGenerationException('Ticket PDF could not be written to disk.');
        }

        return $absolutePath;
    }

    private function storePdfAsset(Ticket $ticket, string $relativePath, string $fileName, string $absolutePath): int
    {
        $data = [
            'FilePath' => $relativePath,
            'OriginalFileName' => $fileName,
            'MimeType' => 'application/pdf',
            'FileSizeBytes' => (int)filesize($absolutePath),
            'AltText' => 'Festival ticket PDF',
        ];

        if ($ticket->pdfAssetId !== null) {
            $this->mediaAssetRepository->update($ticket->pdfAssetId, $data);
            return $ticket->pdfAssetId;
        }

        return $this->mediaAssetRepository->create($data);
    }

    private function resolveAbsolutePublicPath(string $filePath): string
    {
        if (str_starts_with($filePath, '/assets/')) {
            return PathResolver::getPublicPath() . $filePath;
        }

        return $filePath;
    }

    /**
     * @param OrderItem[] $orderItems
     * @param array<int, SessionWithEvent> $sessionsById
     * @param TicketEmailAttachment[] $attachments
     */
    private function sendTicketEmail(
        Order $order,
        TicketRecipient $recipient,
        array $orderItems,
        array $sessionsById,
        array $attachments,
    ): void {
        $message = new TicketEmailMessage(
            toEmail: $recipient->email,
            recipientName: $recipient->displayName,
            orderReference: $order->orderNumber,
            ticketCount: count($attachments),
            eventSummaryLines: $this->buildEventSummaryLines($orderItems, $sessionsById),
            attachments: $attachments,
        );

        try {
            $this->emailService->sendOrderTicketsEmail($message);
            $this->orderRepository->markTicketEmailSent($order->orderId, new \DateTimeImmutable());
        } catch (\Throwable $error) {
            $this->orderRepository->markTicketEmailFailed($order->orderId, $this->truncateErrorMessage($error->getMessage()));
            throw new TicketEmailDeliveryException('Ticket email could not be sent.', 0, $error);
        }
    }

    /**
     * @param OrderItem[] $orderItems
     * @param array<int, SessionWithEvent> $sessionsById
     * @return string[]
     */
    private function buildEventSummaryLines(array $orderItems, array $sessionsById): array
    {
        $lines = [];

        foreach ($orderItems as $orderItem) {
            $session = $sessionsById[(int)$orderItem->eventSessionId];
            $lines[] = sprintf(
                '%s on %s at %s (%d ticket%s)',
                $session->eventTitle,
                $session->startDateTime->format('d M Y'),
                $this->formatSessionTime($session),
                $orderItem->quantity,
                $orderItem->quantity === 1 ? '' : 's',
            );
        }

        return $lines;
    }

    private function truncateErrorMessage(string $message): string
    {
        return mb_substr($message, 0, 500);
    }

    private function firstNonEmpty(?string ...$values): ?string
    {
        foreach ($values as $value) {
            if ($value === null) {
                continue;
            }

            $trimmed = trim($value);
            if ($trimmed !== '') {
                return $trimmed;
            }
        }

        return null;
    }
}
