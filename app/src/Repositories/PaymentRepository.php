<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Repositories\Interfaces\IPaymentRepository;

/**
 * Manages the Payment table, which tracks payment attempts for orders. Each order
 * can have one payment record linking to a Stripe checkout session/payment intent.
 * Supports conditional status transitions to safely handle concurrent webhook deliveries.
 */
class PaymentRepository extends BaseRepository implements IPaymentRepository
{
    /**
     * Creates a payment record when checkout begins. The Stripe session/intent IDs
     * are stored later via dedicated update methods once Stripe returns them.
     *
     * @return int The auto-incremented PaymentId.
     */
    public function create(int $orderId, PaymentMethod $method, PaymentStatus $status): int
    {
        $this->execute(
            'INSERT INTO Payment (OrderId, Method, Status)
            VALUES (:orderId, :method, :status)',
            [
                'orderId' => $orderId,
                'method' => $method->value,
                'status' => $status->value,
            ],
        );

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Unconditionally sets payment status and optional paid-at timestamp.
     * Use updateStatusIfCurrentIn() when guarding against concurrent webhook race conditions.
     */
    public function updateStatus(int $paymentId, PaymentStatus $status, ?\DateTimeImmutable $paidAtUtc = null): void
    {
        $this->execute(
            'UPDATE Payment SET Status = :status, PaidAtUtc = :paidAtUtc WHERE PaymentId = :paymentId',
            [
                'status' => $status->value,
                'paidAtUtc' => $paidAtUtc?->format('Y-m-d H:i:s'),
                'paymentId' => $paymentId,
            ],
        );
    }

    /**
     * Stores the Stripe Checkout Session ID after creating the checkout session,
     * enabling later retrieval/verification of the session via the Stripe API.
     */
    public function updateStripeSessionId(int $paymentId, string $stripeSessionId): void
    {
        $this->execute(
            'UPDATE Payment SET StripeCheckoutSessionId = :sessionId WHERE PaymentId = :paymentId',
            ['sessionId' => $stripeSessionId, 'paymentId' => $paymentId],
        );
    }

    /**
     * Stores the Stripe PaymentIntent ID received from a webhook, used for
     * refund processing and payment reconciliation.
     */
    public function updateStripePaymentIntentId(int $paymentId, string $stripePaymentIntentId): void
    {
        $this->execute(
            'UPDATE Payment SET StripePaymentIntentId = :paymentIntentId WHERE PaymentId = :paymentId',
            ['paymentIntentId' => $stripePaymentIntentId, 'paymentId' => $paymentId],
        );
    }

    /**
     * Stores a generic payment provider reference (e.g. transaction ID from non-Stripe providers).
     */
    public function updateProviderRef(int $paymentId, string $providerRef): void
    {
        $this->execute(
            'UPDATE Payment SET ProviderRef = :providerRef WHERE PaymentId = :paymentId',
            ['providerRef' => $providerRef, 'paymentId' => $paymentId],
        );
    }

    /**
     * Atomically transitions payment status only if the current status is in the allowed set.
     * The caller supplies PaidAtUtc when transitioning to Paid. No-op if
     * allowedCurrentStatuses is empty or the current status is not in the allowed set.
     *
     * @param PaymentStatus[] $allowedCurrentStatuses
     */
    public function updateStatusIfCurrentIn(
        int $paymentId,
        PaymentStatus $newStatus,
        array $allowedCurrentStatuses,
        ?\DateTimeImmutable $paidAtUtc = null,
    ): void {
        if ($allowedCurrentStatuses === []) {
            return;
        }
        // Build dynamic IN clause for the allowed-status guard condition
        $formattedPaidAt = $paidAtUtc?->format('Y-m-d H:i:s');
        $inPlaceholders = [];
        $params = [':newStatus' => $newStatus->value, ':paidAtUtc' => $formattedPaidAt, ':paymentId' => $paymentId];
        foreach ($allowedCurrentStatuses as $i => $status) {
            $key = ':allowedStatus' . $i;
            $inPlaceholders[] = $key;
            $params[$key] = $status->value;
        }
        $in = implode(', ', $inPlaceholders);
        $this->execute(
            "UPDATE Payment SET Status = :newStatus, PaidAtUtc = :paidAtUtc WHERE PaymentId = :paymentId AND Status IN ({$in})",
            $params,
        );
    }
}

