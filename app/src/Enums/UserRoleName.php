<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRoleName: string
{
    case Customer = 'Customer';
    case Employee = 'Employee';
    case Administrator = 'Administrator';

    public static function tryFromValue(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom($value);
    }
}
