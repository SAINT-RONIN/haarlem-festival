<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for a single user row in the CMS users list.
 *
 * Transforms raw joined query data into a typed, view-ready object.
 */
final readonly class CmsUserListItemViewModel
{
    public function __construct(
        public int    $userAccountId,
        public string $username,
        public string $email,
        public string $fullName,
        public string $roleName,
        public string $roleBadgeClass,
        public bool   $isActive,
        public string $statusText,
        public string $statusBadgeClass,
        public string $registeredAt,
    ) {}

    /**
     * Creates a ViewModel from a joined query result row.
     */
    public static function fromRow(array $row): self
    {
        $roleName = (string)($row['RoleName'] ?? 'Unknown');
        $isActive = (bool)($row['IsActive'] ?? false);

        return new self(
            userAccountId:  (int)$row['UserAccountId'],
            username:       (string)$row['Username'],
            email:          (string)$row['Email'],
            fullName:       trim(($row['FirstName'] ?? '') . ' ' . ($row['LastName'] ?? '')),
            roleName:       $roleName,
            roleBadgeClass: self::resolveRoleBadgeClass($roleName),
            isActive:       $isActive,
            statusText:     $isActive ? 'Active' : 'Inactive',
            statusBadgeClass: $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800',
            registeredAt:   !empty($row['RegisteredAtUtc'])
                                ? (new \DateTimeImmutable($row['RegisteredAtUtc']))->format('d M Y, H:i')
                                : '',
        );
    }

    private static function resolveRoleBadgeClass(string $roleName): string
    {
        return match ($roleName) {
            'Administrator' => 'bg-purple-100 text-purple-800',
            'Employee'      => 'bg-blue-100 text-blue-800',
            'Customer'      => 'bg-gray-100 text-gray-800',
            default         => 'bg-gray-100 text-gray-800',
        };
    }
}
