<?php

declare(strict_types=1);

namespace App\Enums;

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

