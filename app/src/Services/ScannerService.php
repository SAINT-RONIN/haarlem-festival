<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Scanner\TicketScanDetail;
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
    public function __construct(
        private readonly IScannerRepository $scannerRepository,
        private readonly ITicketRepository $ticketRepository,
    ) {
    }

    public function scanTicket(string $ticketCode, int $scannedByUserId): TicketScanDetail
    {
        $detail = $this->findTicketOrFail($ticketCode);
        $this->validateNotAlreadyScanned($detail);
        $this->markAsScanned($detail->ticketId, $scannedByUserId);

        return $detail;
    }

    private function findTicketOrFail(string $ticketCode): TicketScanDetail
    {
        $detail = $this->scannerRepository->findTicketWithDetails($ticketCode);

        if ($detail === null) {
            throw new TicketNotFoundException("Ticket not found: {$ticketCode}");
        }

        return $detail;
    }

    private function validateNotAlreadyScanned(TicketScanDetail $detail): void
    {
        if ($detail->isScanned) {
            throw new TicketAlreadyScannedException(detail: $detail);
        }
    }

    private function markAsScanned(int $ticketId, int $userId): void
    {
        $this->ticketRepository->markScanned($ticketId, $userId);
    }
}
