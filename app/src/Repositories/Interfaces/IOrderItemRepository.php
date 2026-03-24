<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Contract for managing order line items. Each item links to exactly one purchasable
 * entity (event session, history tour, or pass) via mutually exclusive nullable foreign keys.
 */
interface IOrderItemRepository
{
    /**
     * Inserts a new order line item linked to an order and optionally to a session, tour, or pass.
     */
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

    /**
     * Checks whether any order item references the given event session (used to guard session deletion).
     */
    public function existsForSession(int $sessionId): bool;
}

