<?php

declare(strict_types=1);

namespace App\Infrastructure\Interfaces;

use App\DTOs\Domain\Tickets\QrCodeMatrix;
use App\DTOs\Domain\Tickets\TicketDocumentData;

/**
 * Generates a PDF document for a single ticket.
 */
interface ITicketPdfGenerator
{
    public function generatePdf(TicketDocumentData $document, QrCodeMatrix $qrCode): string;
}
