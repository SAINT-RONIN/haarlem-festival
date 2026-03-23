<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Infrastructure\Database;
use App\Repositories\Interfaces\IOrderRepository;
use PDO;

class OrderRepository implements IOrderRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function create(
        int $userAccountId,
        int $programId,
        string $orderNumber,
        string $subtotal,
        string $vatTotal,
        string $totalAmount,
        ?\DateTimeImmutable $payBeforeUtc,
    ): int {
        $stmt = $this->pdo->prepare('
            INSERT INTO `Order` (
                OrderNumber,
                UserAccountId,
                ProgramId,
                Status,
                PayBeforeUtc,
                Subtotal,
                VatTotal,
                TotalAmount
            ) VALUES (
                :orderNumber,
                :userAccountId,
                :programId,
                :status,
                :payBeforeUtc,
                :subtotal,
                :vatTotal,
                :totalAmount
            )
        ');

        $stmt->execute([
            'orderNumber' => $orderNumber,
            'userAccountId' => $userAccountId,
            'programId' => $programId,
            'status' => OrderStatus::Pending->value,
            'payBeforeUtc' => $payBeforeUtc?->format('Y-m-d H:i:s'),
            'subtotal' => $subtotal,
            'vatTotal' => $vatTotal,
            'totalAmount' => $totalAmount,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateStatus(int $orderId, OrderStatus $status): void
    {
        $stmt = $this->pdo->prepare('UPDATE `Order` SET Status = :status WHERE OrderId = :orderId');
        $stmt->execute([
            'status' => $status->value,
            'orderId' => $orderId,
        ]);
    }

    /**
     * @param OrderStatus[] $allowedCurrentStatuses
     */
    public function updateStatusIfCurrentIn(int $orderId, OrderStatus $newStatus, array $allowedCurrentStatuses): void
    {
        if ($allowedCurrentStatuses === []) {
            return;
        }
        $inPlaceholders = [];
        $params = [':newStatus' => $newStatus->value, ':orderId' => $orderId];
        foreach ($allowedCurrentStatuses as $i => $status) {
            $key = ':allowedStatus' . $i;
            $inPlaceholders[] = $key;
            $params[$key] = $status->value;
        }
        $in = implode(', ', $inPlaceholders);
        $stmt = $this->pdo->prepare("UPDATE `Order` SET Status = :newStatus WHERE OrderId = :orderId AND Status IN ({$in})");
        $stmt->execute($params);
    }
}

