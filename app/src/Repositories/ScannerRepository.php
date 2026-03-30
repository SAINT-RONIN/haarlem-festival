<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Scanner\TicketScanDetail;
use App\Repositories\Interfaces\IScannerRepository;

/**
 * Fetches ticket data enriched with event, session, and venue details for scanning.
 */
class ScannerRepository extends BaseRepository implements IScannerRepository
{
    public function findTicketWithDetails(string $ticketCode): ?TicketScanDetail
    {
        return $this->fetchOne(
            'SELECT
                t.TicketId,
                t.TicketCode,
                t.IsScanned,
                t.ScannedAtUtc,
                e.EventTitle,
                es.SessionDateTime,
                es.DurationMinutes,
                v.VenueName,
                o.OrderNumber
            FROM Ticket t
            JOIN OrderItem oi ON t.OrderItemId = oi.OrderItemId
            JOIN `Order` o ON oi.OrderId = o.OrderId
            LEFT JOIN EventSession es ON oi.EventSessionId = es.EventSessionId
            LEFT JOIN Event e ON es.EventId = e.EventId
            LEFT JOIN Venue v ON es.VenueId = v.VenueId
            WHERE t.TicketCode = :ticketCode
            LIMIT 1',
            ['ticketCode' => $ticketCode],
            fn(array $row) => $this->mapToDetail($row),
        );
    }

    private function mapToDetail(array $row): TicketScanDetail
    {
        return new TicketScanDetail(
            ticketId: (int) $row['TicketId'],
            ticketCode: (string) $row['TicketCode'],
            isScanned: (bool) $row['IsScanned'],
            scannedAtUtc: $row['ScannedAtUtc'] !== null ? (string) $row['ScannedAtUtc'] : null,
            eventTitle: (string) ($row['EventTitle'] ?? 'Unknown Event'),
            sessionDateTime: (string) ($row['SessionDateTime'] ?? ''),
            durationMinutes: (int) ($row['DurationMinutes'] ?? 0),
            venueName: (string) ($row['VenueName'] ?? 'Unknown Venue'),
            orderNumber: (string) $row['OrderNumber'],
        );
    }
}
