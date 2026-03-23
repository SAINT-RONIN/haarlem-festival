<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Helpers\FormatHelper;
use App\Models\OrderWithDetails;
use App\ViewModels\Cms\CmsOrderListItemViewModel;
use App\ViewModels\Cms\CmsOrdersListViewModel;

final class CmsOrdersMapper
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
            totalAmount:       FormatHelper::price((float)$order->totalAmount),
            createdAt:         $order->createdAtUtc !== ''
                                   ? (new \DateTimeImmutable($order->createdAtUtc))->format(FormatHelper::CMS_DATE_FORMAT)
                                   : '',
            statusBadgeClass:  self::resolveStatusBadgeClass($orderStatus),
            paymentBadgeClass: self::resolvePaymentBadgeClass($paymentStatus),
        );
    }

    private static function resolveStatusBadgeClass(string $status): string
    {
        return match ($status) {
            OrderStatus::Paid->value      => 'bg-green-100 text-green-800',
            OrderStatus::Pending->value   => 'bg-yellow-100 text-yellow-800',
            OrderStatus::Cancelled->value => 'bg-red-100 text-red-800',
            OrderStatus::Expired->value   => 'bg-gray-100 text-gray-800',
            OrderStatus::Refunded->value  => 'bg-purple-100 text-purple-800',
            default                       => 'bg-gray-100 text-gray-800',
        };
    }

    private static function resolvePaymentBadgeClass(string $status): string
    {
        return match ($status) {
            PaymentStatus::Paid->value      => 'bg-green-100 text-green-800',
            PaymentStatus::Pending->value   => 'bg-yellow-100 text-yellow-800',
            PaymentStatus::Failed->value    => 'bg-red-100 text-red-800',
            PaymentStatus::Cancelled->value => 'bg-red-100 text-red-800',
            'No payment'                    => 'bg-gray-100 text-gray-500',
            default                         => 'bg-gray-100 text-gray-800',
        };
    }
}
