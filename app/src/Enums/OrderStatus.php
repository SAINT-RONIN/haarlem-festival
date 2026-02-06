<?php

declare(strict_types=1);

namespace App\Enums;

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

