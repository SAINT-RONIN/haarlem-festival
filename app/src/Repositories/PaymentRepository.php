<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Infrastructure\Database;
use App\Repositories\Interfaces\IPaymentRepository;
use PDO;

class PaymentRepository implements IPaymentRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function create(int $orderId, PaymentMethod $method, PaymentStatus $status): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO Payment (OrderId, Method, Status)
            VALUES (:orderId, :method, :status)
        ');

        $stmt->execute([
            'orderId' => $orderId,
            'method' => $method->value,
            'status' => $status->value,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateStatus(int $paymentId, PaymentStatus $status, ?\DateTimeImmutable $paidAtUtc = null): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE Payment
            SET Status = :status,
                PaidAtUtc = :paidAtUtc
            WHERE PaymentId = :paymentId
        ');

        $stmt->execute([
            'status' => $status->value,
            'paidAtUtc' => $paidAtUtc?->format('Y-m-d H:i:s'),
            'paymentId' => $paymentId,
        ]);
    }

    public function updateStripeSessionId(int $paymentId, string $stripeSessionId): void
    {
        $stmt = $this->pdo->prepare('UPDATE Payment SET StripeCheckoutSessionId = :sessionId WHERE PaymentId = :paymentId');
        $stmt->execute([
            'sessionId' => $stripeSessionId,
            'paymentId' => $paymentId,
        ]);
    }

    public function updateStripePaymentIntentId(int $paymentId, string $stripePaymentIntentId): void
    {
        $stmt = $this->pdo->prepare('UPDATE Payment SET StripePaymentIntentId = :paymentIntentId WHERE PaymentId = :paymentId');
        $stmt->execute([
            'paymentIntentId' => $stripePaymentIntentId,
            'paymentId' => $paymentId,
        ]);
    }

    public function updateProviderRef(int $paymentId, string $providerRef): void
    {
        $stmt = $this->pdo->prepare('UPDATE Payment SET ProviderRef = :providerRef WHERE PaymentId = :paymentId');
        $stmt->execute([
            'providerRef' => $providerRef,
            'paymentId' => $paymentId,
        ]);
    }

    /**
     * @param PaymentStatus[] $allowedCurrentStatuses
     */
    public function updateStatusIfCurrentIn(int $paymentId, PaymentStatus $newStatus, array $allowedCurrentStatuses): void
    {
        if ($allowedCurrentStatuses === []) {
            return;
        }
        $paidAtUtc = $newStatus === PaymentStatus::Paid ? (new \DateTimeImmutable())->format('Y-m-d H:i:s') : null;
        $inPlaceholders = [];
        $params = [':newStatus' => $newStatus->value, ':paidAtUtc' => $paidAtUtc, ':paymentId' => $paymentId];
        foreach ($allowedCurrentStatuses as $i => $status) {
            $key = ':allowedStatus' . $i;
            $inPlaceholders[] = $key;
            $params[$key] = $status->value;
        }
        $in = implode(', ', $inPlaceholders);
        $stmt = $this->pdo->prepare("UPDATE Payment SET Status = :newStatus, PaidAtUtc = :paidAtUtc WHERE PaymentId = :paymentId AND Status IN ({$in})");
        $stmt->execute($params);
    }
}

