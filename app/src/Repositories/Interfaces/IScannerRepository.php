<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Scanner\TicketScanDetail;

/**
 * Read-only access to ticket data enriched with event/session details for scanning.
 */
interface IScannerRepository
{
    public function findTicketWithDetails(string $ticketCode): ?TicketScanDetail;
}
