<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Repositories\Interfaces\IPaymentRepository;

// Tracks payment attempts per order. Supports conditional status transitions
// for safe concurrent webhook handling.
class PaymentRepository extends BaseRepository implements IPaymentRepository
{
    // Stripe session/intent IDs are stored later via dedicated update methods.
    public function create(int $orderId, PaymentMethod $method, PaymentStatus $status): int
    {
        return $this->executeInsert(
            'INSERT INTO Payment (OrderId, Method, Status)
            VALUES (:orderId, :method, :status)',
            [
                'orderId' => $orderId,
                'method' => $method->value,
                'status' => $status->value,
            ],
        );
    }

    // Unconditional -- use updateStatusIfCurrentIn() for race-condition safety.
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

    public function updateStripeSessionId(int $paymentId, string $stripeSessionId): void
    {
        $this->execute(
            'UPDATE Payment SET StripeCheckoutSessionId = :sessionId WHERE PaymentId = :paymentId',
            ['sessionId' => $stripeSessionId, 'paymentId' => $paymentId],
        );
    }

    // Used for refund processing and payment reconciliation.
    public function updateStripePaymentIntentId(int $paymentId, string $stripePaymentIntentId): void
    {
        $this->execute(
            'UPDATE Payment SET StripePaymentIntentId = :paymentIntentId WHERE PaymentId = :paymentId',
            ['paymentIntentId' => $stripePaymentIntentId, 'paymentId' => $paymentId],
        );
    }

    public function updateProviderRef(int $paymentId, string $providerRef): void
    {
        $this->execute(
            'UPDATE Payment SET ProviderRef = :providerRef WHERE PaymentId = :paymentId',
            ['providerRef' => $providerRef, 'paymentId' => $paymentId],
        );
    }

    // Atomic transition: only fires when current status is in $allowedCurrentStatuses.
    // No-op if the set is empty or the current status is not allowed.
    public function updateStatusIfCurrentIn(
        int $paymentId,
        PaymentStatus $newStatus,
        array $allowedCurrentStatuses,
        ?\DateTimeImmutable $paidAtUtc = null,
    ): void {
        if ($allowedCurrentStatuses === []) {
            return;
        }
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
