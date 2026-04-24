<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Domain\Schedule\SessionWithEvent;
use App\DTOs\Domain\Tickets\TicketDocumentData;
use App\DTOs\Domain\Tickets\TicketEmailAttachment;
use App\DTOs\Domain\Tickets\TicketEmailMessage;
use App\DTOs\Domain\Tickets\TicketRecipient;
use App\Exceptions\TicketDeliveryException;
use App\Exceptions\TicketEmailDeliveryException;
use App\Infrastructure\Interfaces\IEmailService;
use App\Infrastructure\PdfAssetStorage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Repositories\Interfaces\ITicketFulfillmentRepository;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\Infrastructure\Interfaces\IQrCodeGenerator;
use App\Infrastructure\Interfaces\ITicketPdfGenerator;

class TicketFulfillmentService implements ITicketFulfillmentService
{
    private const MAX_TICKET_CODE_ATTEMPTS = 5;
    private const DEFAULT_VENUE_NAME = 'Venue to be announced';
    private const TICKET_PDF_FILE_PREFIX = 'Haarlem-Festival-Ticket-';
    private const TICKET_PDF_ALT_TEXT = 'Festival ticket PDF';
    private const MAX_STORED_ERROR_MESSAGE_LENGTH = 500;

    public function __construct(
        private readonly ITicketFulfillmentRepository $repository,
        private readonly IEmailService $emailService,
        private readonly PdfAssetStorage $pdfAssetStorage,
        private readonly IQrCodeGenerator $qrCodeGenerator,
        private readonly ITicketPdfGenerator $ticketPdfGenerator,
    ) {}

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
            $recipient = $this->repository->resolveRecipient($order, $fallbackEmail, $fallbackFirstName, $fallbackLastName);
            $this->syncRecipientIfNeeded($order, $recipient);

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
            $this->repository->markTicketEmailFailed($order->orderId, $this->truncateErrorMessage($error->getMessage()));

            if ($error instanceof TicketDeliveryException) {
                throw $error;
            }

            throw new TicketDeliveryException('Paid order tickets could not be fulfilled.', 0, $error);
        }
    }

    public function regenerateTicketDocumentsByTicketCode(string $ticketCode): void
    {
        $normalizedTicketCode = strtoupper(trim($ticketCode));
        if ($normalizedTicketCode === '') {
            throw new TicketDeliveryException('Ticket code is required to regenerate ticket documents.');
        }

        $ticket = $this->repository->findTicketByCode($normalizedTicketCode);
        if ($ticket === null) {
            throw new TicketDeliveryException("Ticket {$normalizedTicketCode} could not be found.");
        }

        $orderItem = $this->repository->findOrderItemById($ticket->orderItemId);
        if ($orderItem === null) {
            throw new TicketDeliveryException("Order item {$ticket->orderItemId} for ticket {$normalizedTicketCode} could not be found.");
        }

        $this->regenerateTicketDocumentsForOrder($orderItem->orderId);
    }

    public function resendTicketEmailForOrder(int $orderId): void
    {
        $this->repository->resetTicketEmailState($orderId);
        $this->fulfillPaidOrder($orderId);
    }

    // --- Order / recipient ---

    private function requireOrder(int $orderId): Order
    {
        $order = $this->repository->findOrder($orderId);
        if ($order === null) {
            throw new TicketDeliveryException('Paid order could not be found.');
        }

        return $order;
    }

    private function syncRecipientIfNeeded(Order $order, TicketRecipient $recipient): void
    {
        if (
            $order->ticketRecipientFirstName === $recipient->firstName
            && $order->ticketRecipientLastName === $recipient->lastName
            && $order->ticketRecipientEmail === $recipient->email
        ) {
            return;
        }

        $this->repository->updateTicketRecipient($order->orderId, $recipient->firstName, $recipient->lastName, $recipient->email);
    }

    // --- Data loading ---

    /** @return OrderItem[] */
    private function loadTicketableOrderItems(int $orderId): array
    {
        $items = $this->repository->findTicketableOrderItems($orderId);

        if ($items === []) {
            throw new TicketDeliveryException('Paid order does not contain any ticketable sessions.');
        }

        return $items;
    }

    /** @param OrderItem[] $orderItems @return array<int, SessionWithEvent> */
    private function loadSessionsById(array $orderItems): array
    {
        $sessionIds = array_values(array_unique(array_map(
            static fn(OrderItem $item) => (int) $item->eventSessionId,
            $orderItems,
        )));

        $sessionsById = $this->repository->findSessionsByIds($sessionIds);

        foreach ($sessionIds as $sessionId) {
            if (!isset($sessionsById[$sessionId])) {
                throw new TicketDeliveryException("Ticket session {$sessionId} could not be loaded.");
            }
        }

        return $sessionsById;
    }

    // --- Ticket creation ---

    /** @param OrderItem[] $orderItems @return array<int, Ticket[]> */
    private function ensureTicketsForOrderItems(array $orderItems): array
    {
        $ticketsByItemId = $this->repository->findTicketsByOrderItemIds(array_map(
            static fn(OrderItem $item) => $item->orderItemId,
            $orderItems,
        ));

        foreach ($orderItems as $orderItem) {
            $currentTickets = $ticketsByItemId[$orderItem->orderItemId] ?? [];

            while (count($currentTickets) < $orderItem->quantity) {
                $currentTickets[] = $this->repository->createTicket($orderItem->orderItemId, self::MAX_TICKET_CODE_ATTEMPTS);
            }

            $ticketsByItemId[$orderItem->orderItemId] = array_slice($currentTickets, 0, $orderItem->quantity);
        }

        return $ticketsByItemId;
    }

    // --- PDF generation ---

    /** @param OrderItem[] $orderItems @param array<int, SessionWithEvent> $sessionsById @param array<int, Ticket[]> $ticketsByItemId @return TicketEmailAttachment[] */
    private function ensurePdfAttachments(
        Order $order,
        TicketRecipient $recipient,
        array $orderItems,
        array $sessionsById,
        array $ticketsByItemId,
        bool $forceRegeneration = false,
    ): array {
        $attachments = [];

        foreach ($orderItems as $orderItem) {
            $session = $sessionsById[(int) $orderItem->eventSessionId];
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
                    $forceRegeneration,
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
        bool $forceRegeneration = false,
    ): TicketEmailAttachment {
        $existingAttachment = $forceRegeneration ? null : $this->loadExistingPdfAttachment($ticket);
        if ($existingAttachment !== null) {
            return $existingAttachment;
        }

        $document = $this->buildTicketDocument($order, $recipient, $session, $ticket, $position, $totalForItem);
        $qrCode = $this->qrCodeGenerator->generate($ticket->ticketCode);
        $pdfBinary = $this->ticketPdfGenerator->generatePdf($document, $qrCode);
        $fileName = self::TICKET_PDF_FILE_PREFIX . $ticket->ticketCode . '.pdf';
        $storedPdfFile = $this->pdfAssetStorage->storeTicketPdfFile($fileName, $pdfBinary);
        $assetId = $this->pdfAssetStorage->upsertPdfAsset($ticket->pdfAssetId, $storedPdfFile, self::TICKET_PDF_ALT_TEXT);
        $this->repository->updateTicketPdfAssetId($ticket->ticketId, $assetId);

        return new TicketEmailAttachment($storedPdfFile->absolutePath, $fileName);
    }

    private function loadExistingPdfAttachment(Ticket $ticket): ?TicketEmailAttachment
    {
        if ($ticket->pdfAssetId === null) {
            return null;
        }

        $asset = $this->pdfAssetStorage->resolvePdfAttachment($ticket->pdfAssetId);
        return $asset;
    }

    // --- Email sending ---

    /** @param OrderItem[] $orderItems @param array<int, SessionWithEvent> $sessionsById @param TicketEmailAttachment[] $attachments */
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
            $this->repository->markTicketEmailSent($order->orderId, new \DateTimeImmutable());
        } catch (\Throwable $error) {
            $this->repository->markTicketEmailFailed($order->orderId, $this->truncateErrorMessage($error->getMessage()));
            throw new TicketEmailDeliveryException('Ticket email could not be sent.', 0, $error);
        }
    }

    // --- Document / email data building ---

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
            venueName: $session->venueName ?? self::DEFAULT_VENUE_NAME,
            sessionDateLabel: $session->startDateTime->format('l j F Y'),
            sessionTimeLabel: $this->formatSessionTime($session),
        );
    }

    /** @param OrderItem[] $orderItems @param array<int, SessionWithEvent> $sessionsById @return string[] */
    private function buildEventSummaryLines(array $orderItems, array $sessionsById): array
    {
        $lines = [];

        foreach ($orderItems as $orderItem) {
            $session = $sessionsById[(int) $orderItem->eventSessionId];
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

    private function formatSessionTime(SessionWithEvent $session): string
    {
        $start = $session->startDateTime->format('H:i');

        if ($session->endDateTime === null) {
            return $start;
        }

        return $start . ' - ' . $session->endDateTime->format('H:i');
    }

    // --- Helpers ---

    private function regenerateTicketDocumentsForOrder(int $orderId): void
    {
        $order = $this->requireOrder($orderId);
        $recipient = $this->repository->resolveRecipient($order, null, null, null);
        $orderItems = $this->loadTicketableOrderItems($orderId);
        $sessionsById = $this->loadSessionsById($orderItems);
        $ticketsByItemId = $this->ensureTicketsForOrderItems($orderItems);
        $this->ensurePdfAttachments($order, $recipient, $orderItems, $sessionsById, $ticketsByItemId, true);
    }

    private function truncateErrorMessage(string $message): string
    {
        return mb_substr($message, 0, self::MAX_STORED_ERROR_MESSAGE_LENGTH);
    }
}
