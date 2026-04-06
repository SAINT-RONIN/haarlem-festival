<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Files\StoredPdfFile;
use App\DTOs\Filters\EventSessionFilter;
use App\DTOs\Schedule\SessionWithEvent;
use App\DTOs\Tickets\TicketDocumentData;
use App\DTOs\Tickets\TicketEmailAttachment;
use App\DTOs\Tickets\TicketEmailMessage;
use App\DTOs\Tickets\TicketRecipient;
use App\Exceptions\TicketDeliveryException;
use App\Exceptions\TicketEmailDeliveryException;
use App\Exceptions\RepositoryException;
use App\Infrastructure\Interfaces\IEmailService;
use App\Infrastructure\PdfAssetStorage;
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
use App\Tickets\Interfaces\ITicketCodeGenerator;
use App\Tickets\Interfaces\IQrCodeGenerator;
use App\Tickets\Interfaces\ITicketPdfGenerator;

/**
 * Turns a paid order into real ticket records, PDF files, and one outgoing email.
 *
 * This service exists because ticket fulfillment is more than one database write:
 * it has to resolve the recipient, reuse existing tickets when possible, generate
 * missing PDFs, and record delivery failures in a way support staff can inspect later.
 */
class TicketFulfillmentService implements ITicketFulfillmentService
{
    private const MAX_TICKET_CODE_ATTEMPTS = 5;
    private const DEFAULT_VENUE_NAME = 'Venue to be announced';
    private const TICKET_PDF_FILE_PREFIX = 'Haarlem-Festival-Ticket-';
    private const TICKET_PDF_ALT_TEXT = 'Festival ticket PDF';
    private const DUPLICATE_ERROR_KEYWORD = 'duplicate';
    private const MAX_STORED_ERROR_MESSAGE_LENGTH = 500;

    /**
     * Stores the repositories and generators used during ticket fulfillment.
     *
     * The constructor returns nothing because its only job is to receive dependencies
     * up front, which keeps the actual fulfillment methods easier to test and explain.
     */
    public function __construct(
        private readonly IOrderRepository $orderRepository,
        private readonly IOrderItemRepository $orderItemRepository,
        private readonly IEventSessionRepository $eventSessionRepository,
        private readonly ITicketRepository $ticketRepository,
        private readonly IMediaAssetRepository $mediaAssetRepository,
        private readonly IUserAccountRepository $userAccountRepository,
        private readonly IEmailService $emailService,
        private readonly PdfAssetStorage $pdfAssetStorage,
        private readonly IQrCodeGenerator $qrCodeGenerator,
        private readonly ITicketPdfGenerator $ticketPdfGenerator,
        private readonly ITicketCodeGenerator $ticketCodeGenerator,
    ) {
    }

    /**
     * Completes the full ticket-delivery flow for one paid order.
     *
     * It returns nothing because the value of this method is the side effect:
     * after it finishes, the order should have durable ticket rows, stored PDF files,
     * and either a sent email or a recorded failure reason.
     * It exits early when the email was already sent so the operation stays idempotent.
     */
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

    /**
     * Regenerates all stored PDFs for the order that owns the given ticket code.
     *
     * Looking the order up from a ticket code lets support refresh documents even when they
     * only have one ticket from the customer, while still rebuilding the full order set.
     */
    public function regenerateTicketDocumentsByTicketCode(string $ticketCode): void
    {
        $normalizedTicketCode = strtoupper(trim($ticketCode));
        if ($normalizedTicketCode === '') {
            throw new TicketDeliveryException('Ticket code is required to regenerate ticket documents.');
        }

        $ticket = $this->ticketRepository->findByCode($normalizedTicketCode);
        if ($ticket === null) {
            throw new TicketDeliveryException("Ticket {$normalizedTicketCode} could not be found.");
        }

        $orderItem = $this->orderItemRepository->findById($ticket->orderItemId);
        if ($orderItem === null) {
            throw new TicketDeliveryException("Order item {$ticket->orderItemId} for ticket {$normalizedTicketCode} could not be found.");
        }

        $this->regenerateTicketDocumentsForOrder($orderItem->orderId);
    }

    /**
     * Returns the Order model for the paid order we are fulfilling.
     *
     * It throws instead of returning null because every later step depends on a real order,
     * so failing fast here keeps the rest of the flow simple and safe.
     */
    private function requireOrder(int $orderId): Order
    {
        $order = $this->orderRepository->findById($orderId);
        if ($order === null) {
            throw new TicketDeliveryException('Paid order could not be found.');
        }

        return $order;
    }

    /**
     * Returns the best ticket recipient details we can build for this order.
     *
     * The method checks the order first, then fallback values, then the user account
     * because the order is the most specific source of truth for who should receive the tickets.
     */
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

    /**
     * Synchronizes the resolved recipient details back onto the order when needed.
     *
     * It returns nothing because the goal is to keep the stored order data complete,
     * which helps later retries and support tooling use the same recipient information.
     */
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
     * Returns only the order items that should create ticket PDFs.
     *
     * Passes and non-session items are filtered out here because this service is only
     * responsible for session tickets, not every kind of product in an order.
     *
     * @return OrderItem[]
     */
    private function loadTicketableOrderItems(int $orderId): array
    {
        $items = array_values(array_filter(
            $this->orderItemRepository->findByOrderId($orderId),
            // Passes and zero-quantity items do not create ticket PDFs in this service.
            static fn(OrderItem $item) => $item->eventSessionId !== null && $item->eventSessionId > 0 && $item->quantity > 0,
        ));

        if ($items === []) {
            throw new TicketDeliveryException('Paid order does not contain any ticketable sessions.');
        }

        return $items;
    }

    private function regenerateTicketDocumentsForOrder(int $orderId): void
    {
        $order = $this->requireOrder($orderId);
        $recipient = $this->resolveRecipient($order, null, null, null);
        $orderItems = $this->loadTicketableOrderItems($orderId);
        $sessionsById = $this->loadSessionsById($orderItems);
        $ticketsByItemId = $this->ensureTicketsForOrderItems($orderItems);
        $this->ensurePdfAttachments($order, $recipient, $orderItems, $sessionsById, $ticketsByItemId, true);
    }

    /**
     * Returns every required session indexed by EventSessionId.
     *
     * The indexed result matters because later steps repeatedly look up session details
     * while building tickets and email summaries, so one lookup map is clearer than
     * repeatedly searching a flat array.
     *
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

        // We fail fast here so later document-building code can assume every session exists.
        foreach ($sessionIds as $sessionId) {
            if (!isset($sessionsById[$sessionId])) {
                throw new TicketDeliveryException("Ticket session {$sessionId} could not be loaded.");
            }
        }

        return $sessionsById;
    }

    /**
     * Returns a complete ticket list for each order item.
     *
     * Existing tickets are reused because a paid order may be fulfilled more than once
     * after retries or partial failures, and we do not want duplicate ticket rows.
     *
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

            // Keep creating tickets until the stored count matches the purchased quantity.
            while (count($currentTickets) < $orderItem->quantity) {
                $currentTickets[] = $this->createTicket($orderItem->orderItemId);
            }

            $ticketsByItemId[$orderItem->orderItemId] = array_slice($currentTickets, 0, $orderItem->quantity);
        }

        return $ticketsByItemId;
    }

    /**
     * Returns the given tickets grouped by OrderItemId.
     *
     * This grouping exists because the next steps work per order item, not per raw ticket row,
     * especially when one order item produced multiple tickets.
     *
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

    /**
     * Returns one newly created Ticket model for the given order item.
     *
     * It retries a few times because ticket codes are randomly generated, so collisions are rare
     * but still possible, and handling them here keeps the rest of the flow straightforward.
     */
    private function createTicket(int $orderItemId): Ticket
    {
        for ($attempt = 0; $attempt < self::MAX_TICKET_CODE_ATTEMPTS; $attempt++) {
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

    /**
     * Returns true when a repository error looks like a ticket-code collision.
     *
     * This helper exists so createTicket() can retry only the expected duplicate-code case
     * and immediately rethrow unexpected repository problems.
     */
    private function isDuplicateTicketCodeError(RepositoryException $error): bool
    {
        return str_contains(strtolower($error->getMessage()), self::DUPLICATE_ERROR_KEYWORD);
    }

    /**
     * Returns the full list of PDF attachments that should be sent for this order.
     *
     * The method builds one flat attachment list because the email layer only needs ready-to-send
     * files, not the original order-item structure.
     *
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
        bool $forcePdfRegeneration = false,
    ): array {
        $attachments = [];

        foreach ($orderItems as $orderItem) {
            $session = $sessionsById[(int)$orderItem->eventSessionId];
            $tickets = $ticketsByItemId[$orderItem->orderItemId] ?? [];
            $ticketCount = count($tickets);

            // The position is used to label multiple tickets for the same order item.
            foreach (array_values($tickets) as $index => $ticket) {
                $attachments[] = $this->ensureTicketPdfAttachment(
                    $order,
                    $recipient,
                    $session,
                    $ticket,
                    $index + 1,
                    $ticketCount,
                    $forcePdfRegeneration,
                );
            }
        }

        return $attachments;
    }

    /**
     * Returns one email attachment for the given ticket.
     *
     * It reuses an existing PDF first because fulfilled tickets should stay stable across retries;
     * only when no usable PDF exists do we generate a new file and link it back to the ticket.
     */
    private function ensureTicketPdfAttachment(
        Order $order,
        TicketRecipient $recipient,
        SessionWithEvent $session,
        Ticket $ticket,
        int $position,
        int $totalForItem,
        bool $forcePdfRegeneration = false,
    ): TicketEmailAttachment {
        $existingAttachment = $forcePdfRegeneration ? null : $this->loadExistingPdfAttachment($ticket);
        if ($existingAttachment !== null) {
            return $existingAttachment;
        }

        $document = $this->buildTicketDocument($order, $recipient, $session, $ticket, $position, $totalForItem);
        $qrCode = $this->qrCodeGenerator->generate($ticket->ticketCode);
        $pdfBinary = $this->ticketPdfGenerator->generatePdf($document, $qrCode);
        $fileName = self::TICKET_PDF_FILE_PREFIX . $ticket->ticketCode . '.pdf';
        $storedPdfFile = $this->pdfAssetStorage->storeTicketPdfFile($fileName, $pdfBinary);
        $assetId = $this->storeTicketPdfAsset($ticket, $storedPdfFile);
        $this->ticketRepository->updatePdfAssetId($ticket->ticketId, $assetId);

        return new TicketEmailAttachment($storedPdfFile->absolutePath, $fileName);
    }

    /**
     * Returns the previously stored PDF attachment for a ticket, or null when it is missing.
     *
     * Null is used here on purpose because a missing PDF is not always an error:
     * it simply means the caller should generate a fresh one.
     */
    private function loadExistingPdfAttachment(Ticket $ticket): ?TicketEmailAttachment
    {
        if ($ticket->pdfAssetId === null) {
            return null;
        }

        $asset = $this->mediaAssetRepository->findById($ticket->pdfAssetId);
        if ($asset === null) {
            return null;
        }

        $absolutePath = $this->pdfAssetStorage->resolveAbsolutePublicPath($asset->filePath);
        if (!is_file($absolutePath)) {
            return null;
        }

        $displayName = $asset->originalFileName !== '' ? $asset->originalFileName : basename($absolutePath);

        return new TicketEmailAttachment($absolutePath, $displayName);
    }

    /**
     * Returns the TicketDocumentData used by the PDF generator.
     *
     * This method exists to separate presentation-ready ticket text from the lower-level
     * database models, which makes the PDF step easier to reason about and test.
     */
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

    /**
     * Returns a human-readable time label for the ticket PDF and email summary.
     *
     * A single start time is returned when no end time exists because that is the most honest
     * representation of the schedule data we currently have.
     */
    private function formatSessionTime(SessionWithEvent $session): string
    {
        $start = $session->startDateTime->format('H:i');

        if ($session->endDateTime === null) {
            return $start;
        }

        return $start . ' - ' . $session->endDateTime->format('H:i');
    }

    /**
     * Returns the MediaAsset id linked to the stored ticket PDF.
     *
     * Reusing the existing asset id when possible keeps the database relationship stable
     * even if the file has to be regenerated later.
     */
    private function storeTicketPdfAsset(Ticket $ticket, StoredPdfFile $storedPdfFile): int
    {
        return $this->pdfAssetStorage->upsertPdfAsset(
            $ticket->pdfAssetId,
            $storedPdfFile,
            self::TICKET_PDF_ALT_TEXT,
        );
    }

    /**
     * Sends one email containing all ticket PDFs and records whether delivery succeeded.
     *
     * It returns nothing because the outcome is written back to the order record:
     * either a sent timestamp or a trimmed error message for later troubleshooting.
     *
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
     * Returns the short event summary lines shown inside the ticket email.
     *
     * The result is an array of strings because the email template renders a list of events,
     * and each line should already be presentation-ready when it reaches that template.
     *
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

    /**
     * Returns a shortened version of an exception message for database storage.
     *
     * The message is trimmed because database error fields should stay readable and bounded
     * instead of storing extremely long stack-related text.
     */
    private function truncateErrorMessage(string $message): string
    {
        return mb_substr($message, 0, self::MAX_STORED_ERROR_MESSAGE_LENGTH);
    }

    /**
     * Clears any prior send state and re-runs the full fulfillment flow.
     *
     * Resets TicketEmailSentAtUtc and TicketEmailLastError first so the
     * idempotency guard inside fulfillPaidOrder() does not skip the send.
     * Existing ticket rows and PDFs are reused — only the email is re-sent.
     *
     * @throws \App\Exceptions\TicketDeliveryException When the order cannot be found or has no ticketable items.
     * @throws \App\Exceptions\TicketEmailDeliveryException When SMTP delivery fails.
     */
    public function resendTicketEmailForOrder(int $orderId): void
    {
        // Why: fulfillPaidOrder() exits early when ticketEmailSentAtUtc is set.
        // Clearing it here lets the admin force a resend regardless of prior state.
        $this->orderRepository->resetTicketEmailState($orderId);
        $this->fulfillPaidOrder($orderId);
    }

    /**
     * Returns the first non-empty string from the given fallback values, or null if none exist.
     *
     * This helper keeps the recipient-resolution code readable by centralizing the
     * "prefer the first meaningful value" rule in one place.
     */
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
