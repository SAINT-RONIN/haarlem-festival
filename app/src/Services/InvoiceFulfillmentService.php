<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\InvoiceGenerationException;
use App\Infrastructure\Interfaces\IEmailService;
use App\Infrastructure\PdfAssetStorage;
use App\Mappers\InvoiceMapper;
use App\Models\Order;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IInvoiceRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IOrderRepository;
use App\Services\Interfaces\IInvoiceFulfillmentService;
use App\Infrastructure\Interfaces\IInvoicePdfGenerator;

/**
 * Turns a paid order into an invoice record, a stored PDF, and an outgoing invoice email.
 *
 * This service exists because invoice fulfillment is a small workflow rather than one query:
 * it needs invoice numbering, line generation, PDF storage, and email delivery to happen
 * in a consistent order after payment succeeds.
 */
class InvoiceFulfillmentService implements IInvoiceFulfillmentService
{
    private const INVOICE_NUMBER_RANDOM_BYTE_COUNT = 3;
    private const DEFAULT_PASS_TYPE_NAME = 'Festival Pass';
    private const INVOICE_PDF_FILE_PREFIX = 'Haarlem-Festival-Invoice-';
    private const INVOICE_PDF_ALT_TEXT = 'Invoice PDF';

    /**
     * Stores the repositories and helpers used during invoice fulfillment.
     *
     * The constructor returns nothing because it only wires dependencies together once,
     * which keeps the actual invoice flow easy to follow.
     */
    public function __construct(
        private readonly IOrderRepository $orderRepository,
        private readonly IOrderItemRepository $orderItemRepository,
        private readonly IEventSessionRepository $eventSessionRepository,
        private readonly IInvoiceRepository $invoiceRepository,
        private readonly IInvoicePdfGenerator $invoicePdfGenerator,
        private readonly IEmailService $emailService,
        private readonly PdfAssetStorage $pdfAssetStorage,
    ) {
    }

    /**
     * Completes invoice fulfillment for one paid order.
     *
     * It returns nothing because the value is the side effect: after it runs,
     * the order should have an invoice record, a stored PDF, and an attempted email delivery.
     * It exits early when a PDF is already linked so the process stays idempotent.
     */
    public function fulfillPaidOrder(int $orderId): void
    {
        if ($this->isAlreadyFulfilled($orderId)) {
            return;
        }

        $order = $this->requireOrder($orderId);
        $orderItemRows = $this->loadOrderItemsWithDetails($orderId);
        $invoiceId = $this->createInvoiceRecord($order, $orderItemRows);
        $pdfPath = $this->generateAndStorePdf($orderId, $invoiceId, $order->orderNumber);

        $invoice = $this->invoiceRepository->findByOrderId($orderId);
        $this->sendEmail($order, $invoice->invoiceNumber, $pdfPath);
    }

    /**
     * Returns true when invoice fulfillment was already completed for this order.
     *
     * The PDF asset link is the check used here because a stored PDF means the invoice
     * was already generated and should not be duplicated on a retry.
     */
    private function isAlreadyFulfilled(int $orderId): bool
    {
        $existing = $this->invoiceRepository->findByOrderId($orderId);

        return $existing !== null && $existing->pdfAssetId !== null;
    }

    /**
     * Returns the paid order that the invoice belongs to.
     *
     * It throws instead of returning null because the rest of invoice fulfillment
     * cannot continue meaningfully without a real order.
     */
    private function requireOrder(int $orderId): Order
    {
        $order = $this->orderRepository->findById($orderId);
        if ($order === null) {
            throw new InvoiceGenerationException('Paid order could not be found for invoice generation.');
        }

        return $order;
    }

    /**
     * Generates a unique invoice number in INV-YYYYMMDD-XXXXXX format.
     *
     * The returned string is presentation-safe because invoice numbers are shown
     * to customers and staff, not just stored internally.
     */
    private function generateInvoiceNumber(): string
    {
        $date = (new \DateTimeImmutable())->format('Ymd');
        // random_bytes gives us cryptographically random bytes; bin2hex turns them into a
        // readable uppercase hex string for the invoice number suffix (e.g. "A3F2B1").
        $hex = strtoupper(bin2hex(random_bytes(self::INVOICE_NUMBER_RANDOM_BYTE_COUNT)));

        return 'INV-' . $date . '-' . $hex;
    }

    /**
     * Loads order items and enriches them with the extra fields needed to build invoice lines.
     * The returned array is intentionally richer than the raw order items because invoice
     * descriptions need human-readable session and pass information.
     *
     * @return array<int, array<string, mixed>>
     */
    private function loadOrderItemsWithDetails(int $orderId): array
    {
        $orderItems = $this->orderItemRepository->findByOrderId($orderId);
        $rows = [];

        foreach ($orderItems as $item) {
            $row = $item->toArray();

            if ($item->eventSessionId !== null && $item->eventSessionId > 0) {
                $row = $this->enrichWithSessionDetails($row, $item->eventSessionId);
            }

            if ($item->passPurchaseId !== null && $item->passPurchaseId > 0) {
                $row = $this->enrichWithPassDetails($row, $item->passPurchaseId);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Returns the order-item row with session details added to it.
     *
     * Event title and start time are added here because the invoice line should explain
     * what the customer actually booked, not just show internal ids.
     *
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function enrichWithSessionDetails(array $row, int $eventSessionId): array
    {
        $filter = new \App\DTOs\Filters\EventSessionFilter(
            sessionIds: [$eventSessionId],
            includeCancelled: true,
            limit: 1,
        );
        $result = $this->eventSessionRepository->findSessions($filter);

        if ($result->sessions !== []) {
            $session = $result->sessions[0];
            $row['EventTitle'] = $session->eventTitle;
            $row['StartDateTime'] = $session->startDateTime->format('Y-m-d H:i:s');
        }

        return $row;
    }

    /**
     * Returns the order-item row with pass display information added.
     *
     * Pass purchases do not point to a session, so this method fills in the descriptive
     * text the invoice mapper expects later.
     *
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function enrichWithPassDetails(array $row, int $passPurchaseId): array
    {
        // Pass purchases currently use one shared display label in the invoice output.
        $row['PassTypeName'] = self::DEFAULT_PASS_TYPE_NAME;

        return $row;
    }

    /**
     * Creates the invoice header and all invoice line rows, then returns the new invoice id.
     * The returned id matters because the PDF and asset-linking steps need to know
     * exactly which invoice row they belong to.
     *
     * @param array<int, array<string, mixed>> $orderItemRows
     */
    private function createInvoiceRecord(Order $order, array $orderItemRows): int
    {
        $invoiceNumber = $this->generateInvoiceNumber();
        $now = new \DateTimeImmutable();

        $recipientEmail = $order->ticketRecipientEmail ?? '';
        $recipientName = trim(($order->ticketRecipientFirstName ?? '') . ' ' . ($order->ticketRecipientLastName ?? ''));
        // If the customer name is missing, the email still gives us a usable invoice recipient value.
        if ($recipientName === '') {
            $recipientName = $recipientEmail;
        }

        $invoiceId = $this->invoiceRepository->createInvoice(
            orderId: $order->orderId,
            invoiceNumber: $invoiceNumber,
            invoiceDateUtc: $now,
            clientName: $recipientName,
            phoneNumber: '',
            addressLine: '',
            emailAddress: $recipientEmail,
            subtotalAmount: $order->subtotal,
            totalVatAmount: $order->vatTotal,
            totalAmount: $order->totalAmount,
            paymentDateUtc: $now,
        );

        $this->createInvoiceLines($invoiceId, $orderItemRows);

        return $invoiceId;
    }

    /**
     * Creates one invoice line row for every item in the order.
     *
     * The line subtotal is stored as a fixed-precision decimal string rather than a float
     * to avoid floating-point rounding artifacts on invoice documents that customers see.
     *
     * @param array<int, array<string, mixed>> $orderItemRows
     */
    private function createInvoiceLines(int $invoiceId, array $orderItemRows): void
    {
        foreach ($orderItemRows as $row) {
            $description = InvoiceMapper::buildLineDescription($row);
            $quantity = (int)$row['Quantity'];
            $unitPrice = (string)$row['UnitPrice'];
            $vatRate = (string)$row['VatRate'];
            // Calculate the line subtotal as a fixed-precision decimal string — invoices must show exact amounts.
            $lineSubtotal = number_format($quantity * (float)$unitPrice, 2, '.', '');

            $this->invoiceRepository->createInvoiceLine(
                invoiceId: $invoiceId,
                lineDescription: $description,
                quantity: $quantity,
                unitPrice: $unitPrice,
                vatRate: $vatRate,
                lineSubtotal: $lineSubtotal,
            );
        }
    }

    /**
     * Builds the invoice PDF, stores it as a media asset, and links that asset back to the invoice.
     * It returns the absolute file path because the next step, email delivery,
     * needs the real file location for the attachment.
     */
    private function generateAndStorePdf(int $orderId, int $invoiceId, string $orderNumber): string
    {
        $invoice = $this->invoiceRepository->findByOrderId($orderId);
        if ($invoice === null) {
            throw new InvoiceGenerationException('Invoice record could not be loaded after creation.');
        }

        $lines = $this->invoiceRepository->findLinesByInvoiceId($invoiceId);
        $documentData = InvoiceMapper::toDocumentData($invoice, $lines, $orderNumber);

        $pdfBinary = $this->invoicePdfGenerator->generatePdf($documentData);
        $fileName = self::INVOICE_PDF_FILE_PREFIX . $invoice->invoiceNumber . '.pdf';
        $storedPdfFile = $this->pdfAssetStorage->storeInvoicePdfFile($fileName, $pdfBinary);
        $assetId = $this->pdfAssetStorage->upsertPdfAsset(null, $storedPdfFile, self::INVOICE_PDF_ALT_TEXT);
        $this->invoiceRepository->updatePdfAssetId($invoiceId, $assetId);

        return $storedPdfFile->absolutePath;
    }

    /**
     * Sends the invoice email when the order contains a recipient address.
     *
     * It returns nothing because the useful outcome is delivery itself.
     * Missing email is treated as a no-op here because there is nowhere valid to send the file.
     */
    private function sendEmail(Order $order, string $invoiceNumber, string $pdfPath): void
    {
        $recipientEmail = $order->ticketRecipientEmail ?? '';
        if ($recipientEmail === '') {
            return;
        }

        $recipientName = trim(($order->ticketRecipientFirstName ?? '') . ' ' . ($order->ticketRecipientLastName ?? ''));
        $fileName = basename($pdfPath);

        $message = InvoiceMapper::toEmailMessage(
            $recipientEmail,
            // When the customer has no name on record, show their email as the display name in the email.
            $recipientName !== '' ? $recipientName : $recipientEmail,
            $order->orderNumber,
            $invoiceNumber,
            $pdfPath,
            $fileName,
        );

        $this->emailService->sendInvoiceEmail($message);
    }
}
