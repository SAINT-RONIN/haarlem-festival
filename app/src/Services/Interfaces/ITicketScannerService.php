<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\Tickets\TicketScanResult;

/**
 * Validates and marks scanned tickets for employee check-in.
 */
interface ITicketScannerService
{
    public function scanTicket(string $rawCode, int $employeeUserId): TicketScanResult;
}
