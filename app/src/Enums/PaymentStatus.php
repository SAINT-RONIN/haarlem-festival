<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Lifecycle states for payments: Pending, Paid, Failed, Cancelled, or Refunded.
 *
 * Mirrors Stripe payment intent states.
 */
enum PaymentStatus: string
{
    case Pending = 'Pending';
    case Paid = 'Paid';
    case Failed = 'Failed';
    case Cancelled = 'Cancelled';
    case Refunded = 'Refunded';
}
