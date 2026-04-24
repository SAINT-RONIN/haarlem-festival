<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\DTOs\Domain\Schedule\SessionWithEvent;
use App\DTOs\Domain\Tickets\TicketRecipient;

/**
 * Data access layer for ticket fulfillment.
 *
 * Encapsulates all reads and writes needed to turn a paid order into
 * ticket records, so the fulfillment service stays free of raw DB calls.
 */
interface ITicketFulfillmentRepository
{
    public function findOrder(int $orderId): ?Order;

    public function updateTicketRecipient(int $orderId, string $firstName, string $lastName, string $email): void;

    public function markTicketEmailSent(int $orderId, \DateTimeImmutable $sentAtUtc): void;

    public function markTicketEmailFailed(int $orderId, string $errorMessage): void;

    public function resetTicketEmailState(int $orderId): void;

    /** @return OrderItem[] Only items with a valid eventSessionId and quantity > 0. */
    public function findTicketableOrderItems(int $orderId): array;

    public function findOrderItemById(int $orderItemId): ?OrderItem;

    /**
     * Loads sessions for the given IDs and returns them keyed by EventSessionId.
     *
     * @param int[] $sessionIds
     * @return array<int, SessionWithEvent>
     */
    public function findSessionsByIds(array $sessionIds): array;

    /**
     * Resolves the best ticket recipient for an order, checking order fields,
     * then the user account as fallback.
     */
    public function resolveRecipient(Order $order, ?string $fallbackEmail, ?string $fallbackFirstName, ?string $fallbackLastName): TicketRecipient;

    /**
     * Returns existing tickets grouped by OrderItemId.
     *
     * @param int[] $orderItemIds
     * @return array<int, Ticket[]>
     */
    public function findTicketsByOrderItemIds(array $orderItemIds): array;

    public function findTicketByCode(string $ticketCode): ?Ticket;

    /**
     * Creates a ticket record. Retries on duplicate-code collisions up to $maxAttempts.
     *
     * @throws \App\Exceptions\TicketDeliveryException When a unique code cannot be generated.
     */
    public function createTicket(int $orderItemId, int $maxAttempts): Ticket;

    public function updateTicketPdfAssetId(int $ticketId, int $assetId): void;
}
