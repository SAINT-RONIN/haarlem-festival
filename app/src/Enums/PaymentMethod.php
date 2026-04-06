<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Supported payment methods: Credit Card, Stripe, Bank Transfer.
 *
 * Stored on Payment records.
 */
enum PaymentMethod: string
{
    case CreditCard = 'CreditCard';
    case Stripe = 'Stripe';
    case BankTransfer = 'BankTransfer';
}
