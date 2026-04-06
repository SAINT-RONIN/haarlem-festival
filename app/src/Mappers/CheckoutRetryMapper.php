<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Helpers\FormatHelper;
use App\Models\Order;
use App\ViewModels\Program\CheckoutRetryViewModel;

/**
 * Maps an Order model to the retry-payment page ViewModel.
 */
final class CheckoutRetryMapper
{
    public static function toRetryViewModel(Order $order, bool $isLoggedIn): CheckoutRetryViewModel
    {
        $isExpired = self::isOrderExpired($order);

        return new CheckoutRetryViewModel(
            orderId: $order->orderId,
            orderNumber: $order->orderNumber,
            totalAmountFormatted: FormatHelper::price((float) $order->totalAmount),
            payBeforeFormatted: self::formatPayBefore($order),
            isExpired: $isExpired,
            isLoggedIn: $isLoggedIn,
        );
    }

    private static function isOrderExpired(Order $order): bool
    {
        if ($order->payBeforeUtc === null) {
            return false;
        }

        return $order->payBeforeUtc < new \DateTimeImmutable('now');
    }

    private static function formatPayBefore(Order $order): string
    {
        if ($order->payBeforeUtc === null) {
            return 'No deadline';
        }

        return $order->payBeforeUtc->format('j M Y, H:i');
    }
}
