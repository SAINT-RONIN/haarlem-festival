<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Backed enum for user role IDs matching database values.
 */
enum UserRoleId: int
{
    case Customer = 1;
    case Employee = 2;
    case Administrator = 3;

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

    /**
     * Gets the display name for the role.
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::Customer => 'Customer',
            self::Employee => 'Employee',
            self::Administrator => 'Administrator',
        };
    }
}
