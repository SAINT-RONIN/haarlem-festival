<?php

declare(strict_types=1);

namespace App\DTOs\Domain\User;

/**
 * Read-only projection from a JOIN of UserAccount and UserAccountRole.
 *
 * Used by the CMS user management list to display users with their role names.
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
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            userAccountId: (int) ($row['UserAccountId'] ?? throw new \InvalidArgumentException('Missing required field: UserAccountId')),
            username: (string) ($row['Username'] ?? throw new \InvalidArgumentException('Missing required field: Username')),
            email: (string) ($row['Email'] ?? throw new \InvalidArgumentException('Missing required field: Email')),
            firstName: (string) ($row['FirstName'] ?? throw new \InvalidArgumentException('Missing required field: FirstName')),
            lastName: (string) ($row['LastName'] ?? throw new \InvalidArgumentException('Missing required field: LastName')),
            isActive: (bool) ($row['IsActive'] ?? throw new \InvalidArgumentException('Missing required field: IsActive')),
            registeredAtUtc: (string) ($row['RegisteredAtUtc'] ?? throw new \InvalidArgumentException('Missing required field: RegisteredAtUtc')),
            roleName: isset($row['RoleName']) ? (string) $row['RoleName'] : null,
        );
    }

}
