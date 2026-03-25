<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * String names for user roles.
 *
 * Used in display contexts where the human-readable name is needed.
 */
enum UserRoleName: string
{
    case Customer = 'Customer';
    case Employee = 'Employee';
    case Administrator = 'Administrator';
}
