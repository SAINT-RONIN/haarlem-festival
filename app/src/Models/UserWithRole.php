<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a joined row from the CMS users query:
 * UserAccount + RoleName from UserRole.
 *
 * Not a direct table row — used only for the CMS users list.
 */
final readonly class UserWithRole
{
    public function __construct(
        public readonly int     $userAccountId,
        public readonly string  $username,
        public readonly string  $email,
        public readonly string  $firstName,
        public readonly string  $lastName,
        public readonly bool    $isActive,
        public readonly string  $registeredAtUtc,
        public readonly ?string $roleName,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            userAccountId:   (int)$row['UserAccountId'],
            username:        (string)$row['Username'],
            email:           (string)$row['Email'],
            firstName:       (string)($row['FirstName'] ?? ''),
            lastName:        (string)($row['LastName'] ?? ''),
            isActive:        (bool)$row['IsActive'],
            registeredAtUtc: (string)$row['RegisteredAtUtc'],
            roleName:        isset($row['RoleName']) ? (string)$row['RoleName'] : null,
        );
    }

}
