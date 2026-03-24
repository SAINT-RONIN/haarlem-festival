<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Infrastructure\Database;
use App\Repositories\Interfaces\IOrderRepository;
use PDO;

/**
 * Manages rows in the `Order` table. Orders are created in Pending status during checkout
 * and transition through statuses (e.g. Paid, Cancelled, Expired) as payment completes or
 * times out. The table name is backtick-quoted because "Order" is a MySQL reserved word.
 */
class OrderRepository implements IOrderRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Creates a new order in Pending status. The payBeforeUtc deadline controls when
     * unpaid orders become eligible for automatic expiration.
     *
     * @param string $subtotal Monetary amounts passed as strings to preserve decimal precision.
     * @return int The auto-incremented OrderId.
     */
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

    /**
     * Unconditionally sets the order status. Use updateStatusIfCurrentIn() when you need
     * to guard against race conditions during concurrent payment callbacks.
     */
    public function updateStatus(int $orderId, OrderStatus $status): void
    {
        $stmt = $this->pdo->prepare('UPDATE `Order` SET Status = :status WHERE OrderId = :orderId');
        $stmt->execute([
            'status' => $status->value,
            'orderId' => $orderId,
        ]);
    }

    /**
     * Atomically transitions the order status only if the current status is in the allowed set.
     * Prevents invalid transitions (e.g. marking a Cancelled order as Paid) that could occur
     * when webhooks and expiration jobs race against each other. No-op if allowedCurrentStatuses is empty.
     *
     * @param OrderStatus[] $allowedCurrentStatuses
     */
    public function updateStatusIfCurrentIn(int $orderId, OrderStatus $newStatus, array $allowedCurrentStatuses): void
    {
        if ($allowedCurrentStatuses === []) {
            return;
        }
        // Build dynamic IN clause for the allowed-status guard condition
        $inPlaceholders = [];
        $params = [':newStatus' => $newStatus->value, ':orderId' => $orderId];
        foreach ($allowedCurrentStatuses as $i => $status) {
            $key = ':allowedStatus' . $i;
            $inPlaceholders[] = $key;
            $params[$key] = $status->value;
        }
        $in = implode(', ', $inPlaceholders);
        // UPDATE only fires if the current status matches one of the allowed values
        $stmt = $this->pdo->prepare("UPDATE `Order` SET Status = :newStatus WHERE OrderId = :orderId AND Status IN ({$in})");
        $stmt->execute($params);
    }
}

