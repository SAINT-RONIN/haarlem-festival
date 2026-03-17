<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for a single order row in the CMS orders list.
 *
 * Transforms raw joined query data into a typed, view-ready object.
 */
final readonly class CmsOrderListItemViewModel
{
    public function __construct(
        public int    $orderId,
        public string $orderNumber,
        public int    $userAccountId,
        public string $userEmail,
        public string $itemsSummary,
        public string $orderStatus,
        public string $paymentStatus,
        public string $totalAmount,
        public string $createdAt,
        public string $statusBadgeClass,
        public string $paymentBadgeClass,
    ) {}

    /**
     * Creates a ViewModel from a joined query result row.
     */
    public static function fromRow(array $row): self
    {
        $orderStatus   = (string)($row['Status'] ?? 'Unknown');
        $paymentStatus = (string)($row['PaymentStatus'] ?? 'No payment');

        return new self(
            orderId:           (int)$row['OrderId'],
            orderNumber:       (string)$row['OrderNumber'],
            userAccountId:     (int)$row['UserAccountId'],
            userEmail:         (string)($row['Email'] ?? 'Unknown'),
            itemsSummary:      (string)($row['ItemsSummary'] ?? 'No items'),
            orderStatus:       $orderStatus,
            paymentStatus:     $paymentStatus,
            totalAmount:       '€ ' . number_format((float)($row['TotalAmount'] ?? 0), 2),
            createdAt:         !empty($row['CreatedAtUtc'])
                                   ? (new \DateTimeImmutable($row['CreatedAtUtc']))->format('d M Y, H:i')
                                   : '',
            statusBadgeClass:  self::resolveStatusBadgeClass($orderStatus),
            paymentBadgeClass: self::resolvePaymentBadgeClass($paymentStatus),
        );
    }

    private static function resolveStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'Paid'      => 'bg-green-100 text-green-800',
            'Pending'   => 'bg-yellow-100 text-yellow-800',
            'Cancelled' => 'bg-red-100 text-red-800',
            'Expired'   => 'bg-gray-100 text-gray-800',
            'Refunded'  => 'bg-purple-100 text-purple-800',
            default     => 'bg-gray-100 text-gray-800',
        };
    }

    private static function resolvePaymentBadgeClass(string $status): string
    {
        return match ($status) {
            'Paid'       => 'bg-green-100 text-green-800',
            'Pending'    => 'bg-yellow-100 text-yellow-800',
            'Failed'     => 'bg-red-100 text-red-800',
            'Cancelled'  => 'bg-red-100 text-red-800',
            'No payment' => 'bg-gray-100 text-gray-500',
            default      => 'bg-gray-100 text-gray-800',
        };
    }
}
