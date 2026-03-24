<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Lifecycle states for orders: Pending, Paid, Cancelled, Expired, or Refunded.
 *
 * Used in status badge rendering and payment flow transitions.
 */
enum OrderStatus: string
{
    case Pending = 'Pending';
    case Paid = 'Paid';
    case Cancelled = 'Cancelled';
    case Expired = 'Expired';
    case Refunded = 'Refunded';

    public static function tryFromValue(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom($value);
    }
}
