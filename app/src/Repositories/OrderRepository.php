<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Repositories\Interfaces\IOrderRepository;

// "Order" is a MySQL reserved word -- table name is backtick-quoted throughout.
// Orders are created in Pending status and transition through Paid/Cancelled/Expired.
class OrderRepository extends BaseRepository implements IOrderRepository
{
    // Monetary amounts are strings to preserve decimal precision.
    public function create(
        int $userAccountId,
        int $programId,
        string $orderNumber,
        string $subtotal,
        string $vatTotal,
        string $totalAmount,
        ?string $ticketRecipientFirstName,
        ?string $ticketRecipientLastName,
        ?string $ticketRecipientEmail,
        ?\DateTimeImmutable $payBeforeUtc,
    ): int {
        return $this->executeInsert(
            'INSERT INTO `Order` (
                OrderNumber, UserAccountId, ProgramId, Status,
                PayBeforeUtc, Subtotal, VatTotal, TotalAmount,
                TicketRecipientFirstName, TicketRecipientLastName, TicketRecipientEmail
            ) VALUES (
                :orderNumber, :userAccountId, :programId, :status,
                :payBeforeUtc, :subtotal, :vatTotal, :totalAmount,
                :ticketRecipientFirstName, :ticketRecipientLastName, :ticketRecipientEmail
            )',
            [
                'orderNumber' => $orderNumber,
                'userAccountId' => $userAccountId,
                'programId' => $programId,
                'status' => OrderStatus::Pending->value,
                'payBeforeUtc' => $payBeforeUtc?->format('Y-m-d H:i:s'),
                'subtotal' => $subtotal,
                'vatTotal' => $vatTotal,
                'totalAmount' => $totalAmount,
                'ticketRecipientFirstName' => $ticketRecipientFirstName,
                'ticketRecipientLastName' => $ticketRecipientLastName,
                'ticketRecipientEmail' => $ticketRecipientEmail,
            ],
        );
    }

    public function findById(int $orderId): ?Order
    {
        return $this->fetchOne(
            'SELECT * FROM `Order` WHERE OrderId = :orderId',
            ['orderId' => $orderId],
            fn(array $row) => Order::fromRow($row),
        );
    }

    // Ownership check for customer-facing order views.
    public function findByIdAndUserId(int $orderId, int $userId): ?Order
    {
        return $this->fetchOne(
            'SELECT * FROM `Order` WHERE OrderId = :orderId AND UserAccountId = :userId',
            ['orderId' => $orderId, 'userId' => $userId],
            fn(array $row) => Order::fromRow($row),
        );
    }

    // Unconditional -- use updateStatusIfCurrentIn() for race-condition safety.
    public function updateStatus(int $orderId, OrderStatus $status): void
    {
        $this->execute(
            'UPDATE `Order` SET Status = :status WHERE OrderId = :orderId',
            ['status' => $status->value, 'orderId' => $orderId],
        );
    }

    // Atomic transition: only fires when current status is in $allowedCurrentStatuses.
    // Prevents invalid transitions when webhooks and expiration jobs race.
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
        $this->execute(
            "UPDATE `Order` SET Status = :newStatus WHERE OrderId = :orderId AND Status IN ({$in})",
            $params,
        );
    }

    public function updateTicketRecipient(
        int $orderId,
        string $firstName,
        string $lastName,
        string $email,
    ): void {
        $this->execute(
            'UPDATE `Order`
            SET TicketRecipientFirstName = :firstName,
                TicketRecipientLastName = :lastName,
                TicketRecipientEmail = :email
            WHERE OrderId = :orderId',
            [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'orderId' => $orderId,
            ],
        );
    }

    public function markTicketEmailSent(int $orderId, \DateTimeImmutable $sentAtUtc): void
    {
        $this->execute(
            'UPDATE `Order`
            SET TicketEmailSentAtUtc = :sentAtUtc,
                TicketEmailLastError = NULL
            WHERE OrderId = :orderId',
            [
                'sentAtUtc' => $sentAtUtc->format('Y-m-d H:i:s'),
                'orderId' => $orderId,
            ],
        );
    }

    public function markTicketEmailFailed(int $orderId, string $errorMessage): void
    {
        $this->execute(
            'UPDATE `Order`
            SET TicketEmailSentAtUtc = NULL,
                TicketEmailLastError = :errorMessage
            WHERE OrderId = :orderId',
            [
                'errorMessage' => $errorMessage,
                'orderId' => $orderId,
            ],
        );
    }

    // Clears ticket email state so the fulfillment service can re-run.
    public function resetTicketEmailState(int $orderId): void
    {
        $this->execute(
            'UPDATE `Order`
            SET TicketEmailSentAtUtc = NULL,
                TicketEmailLastError = NULL
            WHERE OrderId = :orderId',
            ['orderId' => $orderId],
        );
    }
}
