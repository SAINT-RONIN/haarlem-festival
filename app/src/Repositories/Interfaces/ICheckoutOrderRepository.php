<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;

/**
 * Transactional persistence operations for the checkout flow.
 *
 * Encapsulates multi-table mutations (Order + OrderItem + Payment + PassPurchase + capacity)
 * behind single methods so the service layer stays free of raw DB calls.
 */
interface ICheckoutOrderRepository
{
    /**
     * Creates the full order inside a transaction: Order row, OrderItem rows
     * (including PassPurchase rows for passes), seat reservation, and Payment row.
     *
     * @param array{eventSessionId: ?int, passTypeId: ?int, quantity: int, seatCount: int, basePrice: float, donationAmount: float, passValidDate: ?string}[] $items
     * @return array{orderId: int, paymentId: int}
     */
    public function createOrder(
        int $userId,
        int $programId,
        string $orderNumber,
        string $subtotal,
        string $vatTotal,
        string $totalAmount,
        string $firstName,
        string $lastName,
        string $email,
        \DateTimeImmutable $payBeforeUtc,
        PaymentMethod $paymentMethod,
        string $vatRate,
        array $items,
    ): array;

    /**
     * Links Stripe session/payment-intent IDs to an existing Payment row.
     */
    public function linkStripeIds(int $paymentId, string $stripeSessionId, string $paymentIntentId): void;

    /**
     * Creates a new Payment row for a retry attempt inside a transaction.
     *
     * @return int The new payment ID.
     */
    public function createRetryPayment(int $orderId, PaymentMethod $method): int;

    /**
     * Cancels an order and its payment: restores seat capacity and transitions
     * both the order and payment to Cancelled.
     */
    public function cancelOrder(int $orderId, int $paymentId): void;

    /**
     * Marks an order as paid (fallback for local/dev when webhooks aren't available).
     * Transitions order, payment, and program status idempotently (only from Pending).
     */
    public function markOrderPaid(int $orderId, int $paymentId, int $programId, \DateTimeImmutable $paidAtUtc): void;

    /**
     * Finds an order by ID and user ID for ownership validation.
     */
    public function findOrderByIdAndUserId(int $orderId, int $userId): ?Order;
}
