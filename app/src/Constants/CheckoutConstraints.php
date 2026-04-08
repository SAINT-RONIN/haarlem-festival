<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Business rules enforced during the checkout and ticket reservation process.
 *
 * Capacity values (total seats, single-ticket limits, etc.) are configured per-session
 * in the CMS and stored in the EventSession table — they are NOT hardcoded here.
 * This class only contains rules that are truly universal and not editable by CMS.
 */
final class CheckoutConstraints
{
    /**
     * Fraction of total session capacity that may be sold as single tickets
     * when no explicit CapacitySingleTicketLimit is configured on the session.
     * The remaining capacity is reserved for pass holders.
     *
     * Only used as a fallback — the CMS-set CapacitySingleTicketLimit takes priority.
     */
    public const SINGLE_TICKET_CAPACITY_RATIO = 0.9;

    /**
     * Number of seats a group ticket represents.
     * Applies to Family (3) and Group (7) price tiers.
     */
    public const GROUP_TICKET_SEAT_COUNT = 4;

    private function __construct()
    {
    }
}
