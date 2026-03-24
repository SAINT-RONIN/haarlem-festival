<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\OrderWithDetails;
use App\ViewModels\Cms\CmsOrderListItemViewModel;
use App\ViewModels\Cms\CmsOrdersListViewModel;

class CmsOrdersMapper
{
    public static function toListViewModel(
        array $orders,
        string $selectedStatus,
        ?string $successMessage,
        ?string $errorMessage
    ): CmsOrdersListViewModel {
        return new CmsOrdersListViewModel(
            orders: array_map([self::class, 'toListItem'], $orders),
            selectedStatus: $selectedStatus,
            successMessage: $successMessage,
            errorMessage: $errorMessage,
        );
    }


    public static function toListItem(OrderWithDetails $order): CmsOrderListItemViewModel
    {
        $orderStatus   = $order->status;
        $paymentStatus = $order->paymentStatus ?? 'No payment';

        return new CmsOrderListItemViewModel(
            orderId:           $order->orderId,
            orderNumber:       $order->orderNumber,
            userAccountId:     $order->userAccountId,
            userEmail:         $order->email !== '' ? $order->email : 'Unknown',
            itemsSummary:      $order->itemsSummary ?? 'No items',
            orderStatus:       $orderStatus,
            paymentStatus:     $paymentStatus,
            totalAmount:       '€ ' . number_format((float)$order->totalAmount, 2),
            createdAt:         $order->createdAtUtc !== ''
                                   ? (new \DateTimeImmutable($order->createdAtUtc))->format('d M Y, H:i')
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
