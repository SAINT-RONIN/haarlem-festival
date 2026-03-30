<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UserAccount;
use App\DTOs\User\UserWithRole;
use App\Repositories\Interfaces\ICmsUsersRepository;

/**
 * Handles read-only UserAccount operations for the CMS admin users section.
 *
 * Queries the UserAccount table joined with UserRole for listing and filtering,
 * and provides uniqueness checks that can exclude a given user (for edit forms).
 * Write operations (create, update, delete) live in UserAccountRepository.
 */
class CmsUsersRepository extends BaseRepository implements ICmsUsersRepository
{
    /** @var array<string, string> Maps front-end sort keys to safe SQL column references */
    private const SORT_COLUMNS = [
        'username'   => 'ua.Username',
        'email'      => 'ua.Email',
        'name'       => 'ua.FirstName',
        'role'       => 'ur.RoleName',
        'registered' => 'ua.RegisteredAtUtc',
    ];

    private const SORT_DIRS = ['asc', 'desc'];

    /**
     * Returns all users with their role name, with optional filtering, search, and sort.
     *
     * @return UserWithRole[]
     */
    public function findUsersWithRoles(
        ?int $roleFilter = null,
        ?string $search = null,
        string $sortBy = 'registered',
        string $sortDir = 'desc',
    ): array {
        [$sql, $params] = $this->buildListQuery($roleFilter, $search, $sortBy, $sortDir);

        return $this->fetchAll($sql, $params, fn(array $row) => UserWithRole::fromRow($row));
    }

    /**
     * Retrieves a single user by primary key (includes inactive users, for admin editing).
     */
    public function findById(int $id): ?UserAccount
    {
        return $this->fetchOne(
            'SELECT * FROM UserAccount WHERE UserAccountId = :id',
            [':id' => $id],
            fn(array $row) => UserAccount::fromRow($row),
        );
    }

    /**
     * Checks whether a username is already taken (for create validation).
     */
    public function existsByUsername(string $username): bool
    {
        $stmt = $this->execute(
            'SELECT 1 FROM UserAccount WHERE Username = :username LIMIT 1',
            [':username' => $username],
        );

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Checks whether an email is already registered (for create validation).
     */
    public function existsByEmail(string $email): bool
    {
        $stmt = $this->execute(
            'SELECT 1 FROM UserAccount WHERE Email = :email LIMIT 1',
            [':email' => $email],
        );

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Same as existsByUsername but excludes a specific user (for edit-form uniqueness checks).
     */
    public function existsByUsernameExcluding(string $username, int $excludeId): bool
    {
        $stmt = $this->execute(
            'SELECT 1 FROM UserAccount WHERE Username = :username AND UserAccountId != :excludeId LIMIT 1',
            [':username' => $username, ':excludeId' => $excludeId],
        );

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Same as existsByEmail but excludes a specific user (for edit-form uniqueness checks).
     */
    public function existsByEmailExcluding(string $email, int $excludeId): bool
    {
        $stmt = $this->execute(
            'SELECT 1 FROM UserAccount WHERE Email = :email AND UserAccountId != :excludeId LIMIT 1',
            [':email' => $email, ':excludeId' => $excludeId],
        );

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Assembles the SQL and parameter array for the user list, applying
     * optional role filter, search term (across username/email/name), and sort.
     *
     * @return array{0: string, 1: array<string, mixed>}
     */
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

    /**
     * Base SELECT for the user listing -- joins UserRole to include the role name.
     * Ends with "WHERE 1 = 1" so callers can append AND clauses directly.
     */
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

    /** Maps a front-end sort key to a safe column reference, falling back to registered date. */
    private function resolveSortColumn(string $sortBy): string
    {
        return self::SORT_COLUMNS[$sortBy] ?? 'ua.RegisteredAtUtc';
    }

    /** Validates and normalises sort direction, defaulting to DESC. */
    private function resolveSortDir(string $sortDir): string
    {
        return in_array(strtolower($sortDir), self::SORT_DIRS, true) ? strtoupper($sortDir) : 'DESC';
    }
}
