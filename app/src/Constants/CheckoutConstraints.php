<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Business rules enforced during the checkout and ticket reservation process.
 */
final class CheckoutConstraints
{
    /**
     * Fraction of total session capacity that may be sold as single tickets.
     * The remaining capacity is reserved for pass holders.
     */
    public const SINGLE_TICKET_CAPACITY_RATIO = 0.9;

    private function __construct()
    {
    }
}
