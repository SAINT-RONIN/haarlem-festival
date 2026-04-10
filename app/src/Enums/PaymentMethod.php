<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Supported payment methods: Credit Card, iDEAL, Stripe, Bank Transfer.
 *
 * Stored on Payment records.
 */
enum PaymentMethod: string
{
    case CreditCard = 'CreditCard';
    case Ideal = 'Ideal';
    case Stripe = 'Stripe';
    case BankTransfer = 'BankTransfer';
}
