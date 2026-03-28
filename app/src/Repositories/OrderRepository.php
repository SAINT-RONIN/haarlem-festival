<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Repositories\Interfaces\IOrderRepository;

/**
 * Manages rows in the `Order` table. Orders are created in Pending status during checkout
 * and transition through statuses (e.g. Paid, Cancelled, Expired) as payment completes or
 * times out. The table name is backtick-quoted because "Order" is a MySQL reserved word.
 */
class OrderRepository extends BaseRepository implements IOrderRepository
{
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

    /**
     * Unconditionally sets the order status. Use updateStatusIfCurrentIn() when you need
     * to guard against race conditions during concurrent payment callbacks.
     */
    public function updateStatus(int $orderId, OrderStatus $status): void
    {
        $this->execute(
            'UPDATE `Order` SET Status = :status WHERE OrderId = :orderId',
            ['status' => $status->value, 'orderId' => $orderId],
        );
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
}
