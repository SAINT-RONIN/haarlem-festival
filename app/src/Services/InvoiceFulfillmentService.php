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
use App\Tickets\Interfaces\IInvoicePdfGenerator;

/**
 * Creates an invoice record, generates a PDF, and emails it after successful payment.
 */
class InvoiceFulfillmentService implements IInvoiceFulfillmentService
{
    private const INVOICE_NUMBER_RANDOM_BYTE_COUNT = 3;
    private const DEFAULT_PASS_TYPE_NAME = 'Festival Pass';
    private const INVOICE_PDF_FILE_PREFIX = 'Haarlem-Festival-Invoice-';
    private const INVOICE_PDF_ALT_TEXT = 'Invoice PDF';

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

    private function isAlreadyFulfilled(int $orderId): bool
    {
        $existing = $this->invoiceRepository->findByOrderId($orderId);

        return $existing !== null && $existing->pdfAssetId !== null;
    }

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
     */
    private function generateInvoiceNumber(): string
    {
        $date = (new \DateTimeImmutable())->format('Ymd');
        $hex = strtoupper(bin2hex(random_bytes(self::INVOICE_NUMBER_RANDOM_BYTE_COUNT)));

        return 'INV-' . $date . '-' . $hex;
    }

    /**
     * Loads order items with joined event/pass details for building invoice line descriptions.
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
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function enrichWithPassDetails(array $row, int $passPurchaseId): array
    {
        $row['PassTypeName'] = self::DEFAULT_PASS_TYPE_NAME;

        return $row;
    }

    /**
     * Persists Invoice and InvoiceLine rows, returns the invoice ID.
     *
     * @param array<int, array<string, mixed>> $orderItemRows
     */
    private function createInvoiceRecord(Order $order, array $orderItemRows): int
    {
        $invoiceNumber = $this->generateInvoiceNumber();
        $now = new \DateTimeImmutable();

        $recipientEmail = $order->ticketRecipientEmail ?? '';
        $recipientName = trim(($order->ticketRecipientFirstName ?? '') . ' ' . ($order->ticketRecipientLastName ?? ''));
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

        foreach ($orderItemRows as $row) {
            $description = InvoiceMapper::buildLineDescription($row);
            $quantity = (int)$row['Quantity'];
            $unitPrice = (string)$row['UnitPrice'];
            $vatRate = (string)$row['VatRate'];
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

        return $invoiceId;
    }

    /**
     * Generates the PDF, stores it, creates a media asset, and links it to the invoice.
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
            $recipientName !== '' ? $recipientName : $recipientEmail,
            $order->orderNumber,
            $invoiceNumber,
            $pdfPath,
            $fileName,
        );

        $this->emailService->sendInvoiceEmail($message);
    }
}
