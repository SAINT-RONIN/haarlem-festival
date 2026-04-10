<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Domain\Invoice\InvoiceDocumentData;
use App\DTOs\Domain\Invoice\InvoiceEmailMessage;
use App\DTOs\Domain\Invoice\InvoiceLineData;
use App\Exceptions\InvoiceGenerationException;
use App\Infrastructure\Interfaces\IEmailService;
use App\Infrastructure\PdfAssetStorage;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Order;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IInvoiceRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IOrderRepository;
use App\Services\Interfaces\IInvoiceFulfillmentService;
use App\Infrastructure\Interfaces\IInvoicePdfGenerator;

// Turns a paid order into an invoice record, a stored PDF, and an outgoing invoice email.
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
    ) {}

    // Idempotent — exits early when a PDF is already linked.
    public function fulfillPaidOrder(int $orderId): void
    {
        if ($this->isAlreadyFulfilled($orderId)) {
            return;
        }

        try {
            $order = $this->requireOrder($orderId);
            $orderItemRows = $this->loadOrderItemsWithDetails($orderId);
            $invoiceId = $this->createInvoiceRecord($order, $orderItemRows);
            $pdfPath = $this->generateAndStorePdf($orderId, $invoiceId, $order->orderNumber);

            $invoice = $this->invoiceRepository->findByOrderId($orderId);
            $this->sendEmail($order, $invoice->invoiceNumber, $pdfPath);
        } catch (InvoiceGenerationException $error) {
            throw $error;
        } catch (\Throwable $error) {
            throw new InvoiceGenerationException(
                'Invoice fulfillment failed for order ' . $orderId . '.',
                0,
                $error,
            );
        }
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

    // Format: INV-YYYYMMDD-XXXXXX
    private function generateInvoiceNumber(): string
    {
        $date = new \DateTimeImmutable()->format('Ymd');
        // random_bytes gives us cryptographically random bytes; bin2hex turns them into a
        // readable uppercase hex string for the invoice number suffix (e.g. "A3F2B1").
        $hex = strtoupper(bin2hex(random_bytes(self::INVOICE_NUMBER_RANDOM_BYTE_COUNT)));

        return 'INV-' . $date . '-' . $hex;
    }

    /** @return array<int, array<string, mixed>> */
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

    /** @param array<string, mixed> $row @return array<string, mixed> */
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

    /** @param array<string, mixed> $row @return array<string, mixed> */
    private function enrichWithPassDetails(array $row, int $passPurchaseId): array
    {
        // Pass purchases currently use one shared display label in the invoice output.
        $row['PassTypeName'] = self::DEFAULT_PASS_TYPE_NAME;

        return $row;
    }

    /** @param array<int, array<string, mixed>> $orderItemRows */
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

    // Line subtotal stored as fixed-precision decimal to avoid floating-point rounding on invoices.
    /** @param array<int, array<string, mixed>> $orderItemRows */
    private function createInvoiceLines(int $invoiceId, array $orderItemRows): void
    {
        foreach ($orderItemRows as $row) {
            $description = $this->buildLineDescription($row);
            $quantity = (int) $row['Quantity'];
            $unitPrice = (string) $row['UnitPrice'];
            $vatRate = (string) $row['VatRate'];
            // Calculate the line subtotal as a fixed-precision decimal string — invoices must show exact amounts.
            $lineSubtotal = number_format($quantity * (float) $unitPrice, 2, '.', '');

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

    // Returns absolute file path needed for email attachment.
    private function generateAndStorePdf(int $orderId, int $invoiceId, string $orderNumber): string
    {
        $invoice = $this->invoiceRepository->findByOrderId($orderId);
        if ($invoice === null) {
            throw new InvoiceGenerationException('Invoice record could not be loaded after creation.');
        }

        $lines = $this->invoiceRepository->findLinesByInvoiceId($invoiceId);
        $documentData = $this->buildDocumentData($invoice, $lines, $orderNumber);

        $pdfBinary = $this->invoicePdfGenerator->generatePdf($documentData);
        $fileName = self::INVOICE_PDF_FILE_PREFIX . $invoice->invoiceNumber . '.pdf';
        $storedPdfFile = $this->pdfAssetStorage->storeInvoicePdfFile($fileName, $pdfBinary);
        $assetId = $this->pdfAssetStorage->upsertPdfAsset(null, $storedPdfFile, self::INVOICE_PDF_ALT_TEXT);
        $this->invoiceRepository->updatePdfAssetId($invoiceId, $assetId);

        return $storedPdfFile->absolutePath;
    }

    // No-op when the order has no recipient email.
    private function sendEmail(Order $order, string $invoiceNumber, string $pdfPath): void
    {
        $recipientEmail = $order->ticketRecipientEmail ?? '';
        if ($recipientEmail === '') {
            return;
        }

        $recipientName = trim(($order->ticketRecipientFirstName ?? '') . ' ' . ($order->ticketRecipientLastName ?? ''));
        $fileName = basename($pdfPath);

        $message = new InvoiceEmailMessage(
            recipientEmail: $recipientEmail,
            recipientName: $recipientName !== '' ? $recipientName : $recipientEmail,
            orderNumber: $order->orderNumber,
            invoiceNumber: $invoiceNumber,
            attachments: [(object) ['absolutePath' => $pdfPath, 'displayName' => $fileName]],
        );

        $this->emailService->sendInvoiceEmail($message);
    }

    private function buildLineDescription(array $orderItemRow): string
    {
        if (!empty($orderItemRow['PassTypeName'])) {
            return (string) $orderItemRow['PassTypeName'];
        }

        $title = (string) ($orderItemRow['EventTitle'] ?? 'Event');
        $date  = isset($orderItemRow['StartDateTime'])
            ? new \DateTimeImmutable($orderItemRow['StartDateTime'])->format('d M Y')
            : '';

        return $date !== '' ? $title . ' - ' . $date : $title;
    }

    /** @param InvoiceLine[] $lines */
    private function buildDocumentData(Invoice $invoice, array $lines, string $orderNumber): InvoiceDocumentData
    {
        return new InvoiceDocumentData(
            invoiceNumber: $invoice->invoiceNumber,
            invoiceDateFormatted: $invoice->invoiceDateUtc->format('d M Y'),
            clientName: $invoice->clientName,
            clientEmail: $invoice->emailAddress,
            clientAddress: $invoice->addressLine,
            clientPhone: $invoice->phoneNumber,
            lines: array_map(
                fn(InvoiceLine $line) => new InvoiceLineData(
                    description: $line->lineDescription,
                    quantity: $line->quantity,
                    unitPrice: $line->unitPrice,
                    vatRate: $line->vatRate,
                    lineSubtotal: $line->lineSubtotal,
                ),
                $lines,
            ),
            subtotal: $invoice->subtotalAmount,
            totalVat: $invoice->totalVatAmount,
            totalAmount: $invoice->totalAmount,
            paymentDateFormatted: $invoice->paymentDateUtc !== null
                ? $invoice->paymentDateUtc->format('d M Y')
                : $invoice->invoiceDateUtc->format('d M Y'),
            orderNumber: $orderNumber,
        );
    }
}
