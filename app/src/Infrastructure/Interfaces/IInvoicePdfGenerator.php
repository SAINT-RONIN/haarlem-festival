<?php

declare(strict_types=1);

namespace App\Infrastructure\Interfaces;

use App\DTOs\Domain\Invoice\InvoiceDocumentData;

/**
 * Generates a PDF document for an invoice.
 */
interface IInvoicePdfGenerator
{
    public function generatePdf(InvoiceDocumentData $data): string;
}
