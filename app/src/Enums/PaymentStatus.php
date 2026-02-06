<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'Pending';
    case Paid = 'Paid';
    case Failed = 'Failed';
    case Cancelled = 'Cancelled';
    case Refunded = 'Refunded';

    public static function tryFromValue(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom($value);
    }
}

