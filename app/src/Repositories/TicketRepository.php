<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Ticket;
use App\Repositories\Interfaces\ITicketRepository;

class TicketRepository extends BaseRepository implements ITicketRepository
{
    public function create(int $orderItemId, string $ticketCode): int
    {
        return $this->executeInsert(
            'INSERT INTO Ticket (OrderItemId, TicketCode, IsScanned) VALUES (:orderItemId, :ticketCode, 0)',
            [
                'orderItemId' => $orderItemId,
                'ticketCode' => $ticketCode,
            ],
        );
    }

    /** @param int[] $orderItemIds */
    public function findByOrderItemIds(array $orderItemIds): array
    {
        $orderItemIds = array_values(array_filter(array_map('intval', $orderItemIds), fn(int $id) => $id > 0));
        if ($orderItemIds === []) {
            return [];
        }

        $inClause = $this->buildInClause($orderItemIds, 'orderItemId');

        return $this->fetchAll(
            "SELECT * FROM Ticket WHERE OrderItemId IN ({$inClause['placeholders']}) ORDER BY TicketId ASC",
            $inClause['params'],
            fn(array $row) => Ticket::fromRow($row),
        );
    }

    public function findByCode(string $ticketCode): ?Ticket
    {
        return $this->fetchOne(
            'SELECT * FROM Ticket WHERE TicketCode = :ticketCode LIMIT 1',
            ['ticketCode' => $ticketCode],
            fn(array $row) => Ticket::fromRow($row),
        );
    }

    public function updatePdfAssetId(int $ticketId, ?int $pdfAssetId): void
    {
        $this->execute(
            'UPDATE Ticket SET PdfAssetId = :pdfAssetId WHERE TicketId = :ticketId',
            [
                'pdfAssetId' => $pdfAssetId,
                'ticketId' => $ticketId,
            ],
        );
    }

    // Only succeeds when IsScanned = 0 (prevents double-scans).
    public function markScanned(int $ticketId, int $scannedByUserId, ?\DateTimeImmutable $scannedAtUtc = null): bool
    {
        $statement = $this->execute(
            'UPDATE Ticket
            SET IsScanned = 1,
                ScannedAtUtc = :scannedAtUtc,
                ScannedByUserId = :scannedByUserId
            WHERE TicketId = :ticketId AND IsScanned = 0',
            [
                'scannedAtUtc' => ($scannedAtUtc ?? new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'scannedByUserId' => $scannedByUserId,
                'ticketId' => $ticketId,
            ],
        );

        return $statement->rowCount() > 0;
    }
}
