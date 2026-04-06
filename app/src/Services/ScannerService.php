<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Domain\Scanner\TicketScanDetail;
use App\Exceptions\TicketAlreadyScannedException;
use App\Exceptions\TicketNotFoundException;
use App\Repositories\Interfaces\IScannerRepository;
use App\Repositories\Interfaces\ITicketRepository;
use App\Services\Interfaces\IScannerService;

/**
 * Validates and processes ticket scans at venue entrances.
 */
class ScannerService implements IScannerService
{
    private const TICKET_CODE_PATTERN = '/HF-[A-Z0-9]+/';

    public function __construct(
        private readonly IScannerRepository $scannerRepository,
        private readonly ITicketRepository $ticketRepository,
    ) {
    }

    /** Validates a ticket code, loads its details, rejects duplicates, and marks it as scanned. */
    public function scanTicket(string $ticketCode, int $scannedByUserId): TicketScanDetail
    {
        $normalizedTicketCode = $this->normalizeTicketCode($ticketCode);
        $this->validateTicketCode($normalizedTicketCode);
        $detail = $this->findTicketOrFail($normalizedTicketCode);
        $this->validateNotAlreadyScanned($detail);
        $this->markAsScanned($detail->ticketId, $scannedByUserId);

        return $detail;
    }

    private function normalizeTicketCode(string $ticketCode): string
    {
        $normalizedTicketCode = strtoupper(trim($ticketCode));

        if ($normalizedTicketCode === '') {
            return '';
        }

        if (preg_match(self::TICKET_CODE_PATTERN, $normalizedTicketCode, $matches) === 1) {
            return $matches[0];
        }

        return $normalizedTicketCode;
    }

    /** Rejects empty ticket codes before we hit the database. */
    private function validateTicketCode(string $ticketCode): void
    {
        if ($ticketCode === '') {
            throw new TicketNotFoundException('Please enter a ticket code.');
        }
    }

    /** Loads the ticket scan details needed by the scanner screen or throws when no ticket matches. */
    private function findTicketOrFail(string $ticketCode): TicketScanDetail
    {
        $detail = $this->scannerRepository->findTicketWithDetails($ticketCode);

        if ($detail === null) {
            throw new TicketNotFoundException("Ticket not found: {$ticketCode}");
        }

        return $detail;
    }

    /** Stops the scan flow when the ticket has already been scanned earlier. */
    private function validateNotAlreadyScanned(TicketScanDetail $detail): void
    {
        if ($detail->isScanned) {
            throw new TicketAlreadyScannedException(detail: $detail);
        }
    }

    /** Persists the successful scan together with the user who performed it. */
    private function markAsScanned(int $ticketId, int $userId): void
    {
        $this->ticketRepository->markScanned($ticketId, $userId);
    }
}
