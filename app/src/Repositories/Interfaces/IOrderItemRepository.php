<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface IOrderItemRepository
{
    public function create(
        int $orderId,
        ?int $eventSessionId,
        ?int $historyTourId,
        ?int $passPurchaseId,
        int $quantity,
        string $unitPrice,
        string $vatRate,
        ?string $donationAmount,
        string $specialRequest = '',
    ): void;

    public function existsForSession(int $sessionId): bool;
}

