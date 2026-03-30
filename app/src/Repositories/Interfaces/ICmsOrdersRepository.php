<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Checkout\OrderWithDetails;

/**
 * Defines read-only queries for retrieving orders with joined details for CMS display.
 */
interface ICmsOrdersRepository
{
    /**
     * Returns all orders with joined details, optionally filtered by status.
     *
     * @return OrderWithDetails[]
     */
    public function findOrdersWithDetails(?string $statusFilter = null): array;

    /**
     * Returns a single order row with user email, or null if not found.
     *
     * @return array<string, mixed>|null
     */
    public function findOrderById(int $orderId): ?array;

    /**
     * Returns all line items for a given order.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findOrderItems(int $orderId): array;

    /**
     * Returns all payment records for a given order.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findOrderPayments(int $orderId): array;

    /**
     * Returns all tickets for a given order.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findOrderTickets(int $orderId): array;
}
