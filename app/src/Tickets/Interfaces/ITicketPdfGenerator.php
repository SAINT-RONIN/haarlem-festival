<?php

declare(strict_types=1);

namespace App\Tickets\Interfaces;

use App\DTOs\Tickets\QrCodeMatrix;
use App\DTOs\Tickets\TicketDocumentData;

/**
 * Generates a PDF document for a single ticket.
 */
interface ITicketPdfGenerator
{
    public function generatePdf(TicketDocumentData $document, QrCodeMatrix $qrCode): string;
}
