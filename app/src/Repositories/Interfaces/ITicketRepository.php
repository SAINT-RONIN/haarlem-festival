<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Ticket;

/**
 * Contract for persistent ticket records and scan state.
 */
interface ITicketRepository
{
    public function create(int $orderItemId, string $ticketCode): int;

    /**
     * @param int[] $orderItemIds
     * @return Ticket[]
     */
    public function findByOrderItemIds(array $orderItemIds): array;

    public function findByCode(string $ticketCode): ?Ticket;

    public function updatePdfAssetId(int $ticketId, ?int $pdfAssetId): void;

    public function markScanned(int $ticketId, int $scannedByUserId, ?\DateTimeImmutable $scannedAtUtc = null): void;
}
