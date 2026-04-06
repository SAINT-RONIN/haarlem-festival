<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Domain\Tickets\TicketScanResult;
use App\Exceptions\ValidationException;
use App\Repositories\Interfaces\ITicketRepository;
use App\Services\Interfaces\ITicketScannerService;

/**
 * Validates ticket codes and marks them as scanned once.
 */
class TicketScannerService implements ITicketScannerService
{
    private const LOCAL_TIMEZONE = 'Europe/Amsterdam';
    private const TICKET_CODE_PATTERN = '/HF-[A-Z0-9]+/';

    public function __construct(
        private readonly ITicketRepository $ticketRepository,
    ) {
    }

    public function scanTicket(string $rawCode, int $employeeUserId): TicketScanResult
    {
        $ticketCode = $this->normalizeTicketCode($rawCode);
        $ticket = $this->ticketRepository->findByCode($ticketCode);

        if ($ticket === null) {
            throw new ValidationException(sprintf('Ticket code %s is not recognised.', $ticketCode));
        }

        $this->assertTicketCanBeScanned($ticket);

        $scannedAtUtc = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        if (!$this->ticketRepository->markScanned($ticket->ticketId, $employeeUserId, $scannedAtUtc)) {
            $latestTicket = $this->ticketRepository->findByCode($ticketCode) ?? $ticket;
            $this->assertTicketCanBeScanned($latestTicket);
            throw new ValidationException(sprintf('Ticket %s could not be scanned. Please try again.', $ticket->ticketCode));
        }

        return new TicketScanResult(
            ticketCode: $ticket->ticketCode,
            isScanned: true,
            message: sprintf('Ticket %s scanned successfully.', $ticket->ticketCode),
            scannedAtLabel: $this->formatScanLabel($scannedAtUtc),
        );
    }

    private function normalizeTicketCode(string $rawCode): string
    {
        $ticketCode = strtoupper(trim($rawCode));
        if ($ticketCode === '') {
            throw new ValidationException('Scan a QR code or enter a ticket code first.');
        }

        if (preg_match(self::TICKET_CODE_PATTERN, $ticketCode, $matches) === 1) {
            return $matches[0];
        }

        return $ticketCode;
    }

    private function assertTicketCanBeScanned(\App\Models\Ticket $ticket): void
    {
        if (!$ticket->isScanned) {
            return;
        }

        throw new ValidationException(
            sprintf(
                'Ticket %s was already scanned%s.',
                $ticket->ticketCode,
                $ticket->scannedAtUtc !== null ? ' at ' . $this->formatScanLabel($ticket->scannedAtUtc) : '',
            ),
        );
    }

    private function formatScanLabel(\DateTimeImmutable $scannedAtUtc): string
    {
        return $scannedAtUtc
            ->setTimezone(new \DateTimeZone(self::LOCAL_TIMEZONE))
            ->format('d-m-Y H:i');
    }
}
