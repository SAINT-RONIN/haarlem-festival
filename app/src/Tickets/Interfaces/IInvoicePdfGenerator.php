<?php

declare(strict_types=1);

namespace App\Tickets\Interfaces;

use App\DTOs\Invoice\InvoiceDocumentData;

/**
 * Generates a PDF document for an invoice.
 */
interface IInvoicePdfGenerator
{
    public function generatePdf(InvoiceDocumentData $data): string;
}
