<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Scanner\TicketScanDetail;

/**
 * Validates and processes ticket scans at venue entrances.
 */
interface IScannerService
{
    /**
     * Looks up a ticket by code, validates it, and marks it as scanned.
     *
     * @throws \App\Exceptions\TicketNotFoundException When the code doesn't match any ticket.
     * @throws \App\Exceptions\TicketAlreadyScannedException When the ticket was already scanned.
     */
    public function scanTicket(string $ticketCode, int $scannedByUserId): TicketScanDetail;
}
