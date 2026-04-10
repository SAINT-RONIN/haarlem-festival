<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UserAccount;
use App\DTOs\Domain\User\UserWithRole;
use App\Repositories\Interfaces\ICmsUsersRepository;

// Read-only user queries for the CMS admin panel. Write ops live in UserAccountRepository.
class CmsUsersRepository extends BaseRepository implements ICmsUsersRepository
{
    /** @var array<string, string> front-end sort keys -> safe SQL columns */
    private const SORT_COLUMNS = [
        'username'   => 'ua.Username',
        'email'      => 'ua.Email',
        'name'       => 'ua.FirstName',
        'role'       => 'ur.RoleName',
        'registered' => 'ua.RegisteredAtUtc',
    ];

    private const SORT_DIRS = ['asc', 'desc'];

    public function findUsersWithRoles(
        ?int $roleFilter = null,
        ?string $search = null,
        string $sortBy = 'registered',
        string $sortDir = 'desc',
    ): array {
        [$sql, $params] = $this->buildListQuery($roleFilter, $search, $sortBy, $sortDir);

        return $this->fetchAll($sql, $params, fn(array $row) => UserWithRole::fromRow($row));
    }

    // Includes inactive users (admin editing needs access to all accounts).
    public function findById(int $id): ?UserAccount
    {
        return $this->fetchOne(
            'SELECT * FROM UserAccount WHERE UserAccountId = :id',
            [':id' => $id],
            fn(array $row) => UserAccount::fromRow($row),
        );
    }

    public function existsByUsername(string $username): bool
    {
        $stmt = $this->execute(
            'SELECT 1 FROM UserAccount WHERE Username = :username LIMIT 1',
            [':username' => $username],
        );

        return $stmt->fetchColumn() !== false;
    }

    public function existsByEmail(string $email): bool
    {
        $stmt = $this->execute(
            'SELECT 1 FROM UserAccount WHERE Email = :email LIMIT 1',
            [':email' => $email],
        );

        return $stmt->fetchColumn() !== false;
    }

    // For edit-form uniqueness checks (excludes the user being edited).
    public function existsByUsernameExcluding(string $username, int $excludeId): bool
    {
        $stmt = $this->execute(
            'SELECT 1 FROM UserAccount WHERE Username = :username AND UserAccountId != :excludeId LIMIT 1',
            [':username' => $username, ':excludeId' => $excludeId],
        );

        return $stmt->fetchColumn() !== false;
    }

    public function existsByEmailExcluding(string $email, int $excludeId): bool
    {
        $stmt = $this->execute(
            'SELECT 1 FROM UserAccount WHERE Email = :email AND UserAccountId != :excludeId LIMIT 1',
            [':email' => $email, ':excludeId' => $excludeId],
        );

        return $stmt->fetchColumn() !== false;
    }

    /** @return array{0: string, 1: array<string, mixed>} */
    private function buildListQuery(
        ?int $roleFilter,
        ?string $search,
        string $sortBy,
        string $sortDir,
    ): array {
        $sql    = $this->buildListSelect();
        $params = [];

        if ($roleFilter !== null) {
            $sql .= ' AND ua.UserRoleId = :roleId';
            $params[':roleId'] = $roleFilter;
        }

        if ($search !== null && $search !== '') {
            $sql .= ' AND (ua.Username LIKE :searchUser OR ua.Email LIKE :searchEmail'
                  . ' OR ua.FirstName LIKE :searchFirst OR ua.LastName LIKE :searchLast)';
            $searchTerm = '%' . $search . '%';
            $params[':searchUser'] = $searchTerm;
            $params[':searchEmail'] = $searchTerm;
            $params[':searchFirst'] = $searchTerm;
            $params[':searchLast'] = $searchTerm;
        }

        $sql .= ' ORDER BY ' . $this->resolveSortColumn($sortBy) . ' ' . $this->resolveSortDir($sortDir);

        return [$sql, $params];
    }

    // Base SELECT joins UserRole; ends with WHERE 1 = 1 for easy AND appending.
    private function buildListSelect(): string
    {
        return '
            SELECT
                ua.UserAccountId,
                ua.Username,
                ua.Email,
                ua.FirstName,
                ua.LastName,
                ua.IsActive,
                ua.RegisteredAtUtc,
                ur.RoleName
            FROM UserAccount ua
            LEFT JOIN UserRole ur ON ua.UserRoleId = ur.UserRoleId
            WHERE 1 = 1
        ';
    }

    private function resolveSortColumn(string $sortBy): string
    {
        return self::SORT_COLUMNS[$sortBy] ?? 'ua.RegisteredAtUtc';
    }

    private function resolveSortDir(string $sortDir): string
    {
        return in_array(strtolower($sortDir), self::SORT_DIRS, true) ? strtoupper($sortDir) : 'DESC';
    }
}
