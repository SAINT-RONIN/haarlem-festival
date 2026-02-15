<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Backed enum for price tier IDs matching database PriceTier table.
 *
 * Values verified against complete-database-11-02-2026.sql:
 * - 1 = Adult
 * - 2 = ChildU12
 * - 3 = Family
 * - 4 = ReservationFee
 * - 5 = PayWhatYouLike
 */
enum PriceTierId: int
{
    case Adult = 1;
    case ChildU12 = 2;
    case Family = 3;
    case ReservationFee = 4;
    case PayWhatYouLike = 5;

    /**
     * Attempts to create an enum from a nullable value.
     */
    public static function tryFromValue(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom($value);
    }
}


