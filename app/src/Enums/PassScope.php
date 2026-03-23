<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Jazz pass coverage: Day (single festival day) or Range (multiple days / full festival).
 */
enum PassScope: string
{
    case Day = 'Day';
    case Range = 'Range';

    public static function tryFromValue(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom($value);
    }
}
