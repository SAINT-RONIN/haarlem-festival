<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\DTOs\Domain\Scanner\TicketScanDetail;

/**
 * Thrown when a ticket has already been scanned at the venue.
 * Carries the scan detail so the controller can build a rich error response.
 */
class TicketAlreadyScannedException extends ScannerException
{
    public function __construct(
        public readonly TicketScanDetail $detail,
        string $message = 'This ticket has already been scanned.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
