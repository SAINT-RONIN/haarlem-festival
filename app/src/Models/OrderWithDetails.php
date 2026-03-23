<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a joined row from the CMS orders query:
 * Order + user Email + ItemsSummary + latest PaymentStatus.
 *
 * Not a direct table row — used only for the CMS orders list.
 */
final readonly class OrderWithDetails
{
    public function __construct(
        public readonly int     $orderId,
        public readonly string  $orderNumber,
        public readonly int     $userAccountId,
        public readonly string  $status,
        public readonly string  $totalAmount,
        public readonly string  $createdAtUtc,
        public readonly string  $email,
        public readonly ?string $itemsSummary,
        public readonly ?string $paymentStatus,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            orderId:       (int)$row['OrderId'],
            orderNumber:   (string)$row['OrderNumber'],
            userAccountId: (int)$row['UserAccountId'],
            status:        (string)$row['Status'],
            totalAmount:   (string)$row['TotalAmount'],
            createdAtUtc:  (string)$row['CreatedAtUtc'],
            email:         (string)($row['Email'] ?? ''),
            itemsSummary:  isset($row['ItemsSummary']) ? (string)$row['ItemsSummary'] : null,
            paymentStatus: isset($row['PaymentStatus']) ? (string)$row['PaymentStatus'] : null,
        );
    }

}
