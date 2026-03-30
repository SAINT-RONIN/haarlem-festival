<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Cms\CmsOrderDetailDto;
use App\DTOs\Cms\CmsOrderItemDto;
use App\DTOs\Cms\CmsOrderPaymentDto;
use App\DTOs\Cms\CmsOrderTicketDto;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Helpers\FormatHelper;
use App\DTOs\Checkout\OrderWithDetails;
use App\ViewModels\Cms\CmsOrderDetailViewModel;
use App\ViewModels\Cms\CmsOrderListItemViewModel;
use App\ViewModels\Cms\CmsOrdersListViewModel;

/**
 * Transforms order domain models into ViewModels for the CMS orders-list page,
 * resolving status/payment badge colors and formatting monetary amounts.
 */
final class CmsOrdersMapper
{
    /**
     * Builds the full CMS orders-list page ViewModel from an array of OrderWithDetails models.
     */
    public static function toListViewModel(
        array $orders,
        string $selectedStatus,
        ?string $successMessage,
        ?string $errorMessage
    ): CmsOrdersListViewModel {
        return new CmsOrdersListViewModel(
            orders: array_map([self::class, 'toListItem'], $orders),
            statusOptions: array_map(fn (OrderStatus $s) => $s->value, OrderStatus::cases()),
            selectedStatus: $selectedStatus,
            successMessage: $successMessage,
            errorMessage: $errorMessage,
        );
    }


    /**
     * Converts a single OrderWithDetails into a list-row ViewModel, formatting the
     * total amount as currency and mapping order/payment statuses to badge color classes.
     */
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

    /** Maps an OrderStatus value to its Tailwind badge color classes. */
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

    /**
     * Builds the CMS order detail page ViewModel from raw repository data arrays.
     *
     * @param array{order: array, items: array, payments: array, tickets: array} $data
     */
    public static function toDetailViewModel(
        array $data,
        ?string $successMessage,
        ?string $errorMessage,
    ): CmsOrderDetailViewModel {
        return new CmsOrderDetailViewModel(
            order:          CmsOrderDetailDto::fromRow($data['order']),
            items:          array_map([CmsOrderItemDto::class, 'fromRow'], $data['items']),
            payments:       array_map([CmsOrderPaymentDto::class, 'fromRow'], $data['payments']),
            tickets:        array_map([CmsOrderTicketDto::class, 'fromRow'], $data['tickets']),
            successMessage: $successMessage,
            errorMessage:   $errorMessage,
        );
    }

    /** Maps a PaymentStatus value to its Tailwind badge color classes. */
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
