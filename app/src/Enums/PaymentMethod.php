<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case Ideal = 'Ideal';
    case CreditCard = 'CreditCard';
    case Stripe = 'Stripe';
    case BankTransfer = 'BankTransfer';

    public static function tryFromValue(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom($value);
    }
}