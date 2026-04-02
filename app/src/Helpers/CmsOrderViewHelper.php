<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;

/**
 * Shared CMS order-detail display helpers for status badges and UTC date labels.
 */
final class CmsOrderViewHelper
{
    public static function formatUtcDate(?string $dateString): string
    {
        if ($dateString === null || $dateString === '') {
            return '';
        }

        return (new \DateTimeImmutable($dateString))->format(FormatHelper::CMS_DATE_FORMAT);
    }

    public static function resolveOrderBadgeClass(string $status): string
    {
        return match ($status) {
            OrderStatus::Paid->value => 'bg-green-100 text-green-800',
            OrderStatus::Pending->value => 'bg-yellow-100 text-yellow-800',
            OrderStatus::Cancelled->value => 'bg-red-100 text-red-800',
            OrderStatus::Expired->value => 'bg-gray-100 text-gray-800',
            OrderStatus::Refunded->value => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public static function resolvePaymentBadgeClass(?string $status): string
    {
        return match ($status) {
            PaymentStatus::Paid->value => 'bg-green-100 text-green-800',
            PaymentStatus::Pending->value => 'bg-yellow-100 text-yellow-800',
            PaymentStatus::Failed->value => 'bg-red-100 text-red-800',
            PaymentStatus::Cancelled->value => 'bg-red-100 text-red-800',
            'No payment' => 'bg-gray-100 text-gray-500',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
