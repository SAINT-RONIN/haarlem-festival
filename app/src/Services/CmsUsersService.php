<?php

declare(strict_types=1);

namespace App\Services;

use App\Infrastructure\Database;
use PDO;

/**
 * Service for the CMS Users list page.
 *
 * Fetches users joined with their role name.
 * Returns raw rows — the controller maps them to ViewModels.
 */
class CmsUsersService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns all users with their role name.
     * Optionally filtered by role ID.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getUsersWithRoles(?int $roleFilter = null): array
    {
        $sql    = $this->buildUsersQuery($roleFilter !== null);
        $params = $this->buildQueryParams($roleFilter);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Builds the SQL query for the users list.
     */
    private function buildUsersQuery(bool $withRoleFilter): string
    {
        $roleClause = $withRoleFilter ? ' AND ua.UserRoleId = :roleId' : '';

        return "
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
            WHERE 1 = 1{$roleClause}
            ORDER BY ua.RegisteredAtUtc DESC
        ";
    }

    /**
     * Builds the named parameter array for the query.
     *
     * @return array<string, mixed>
     */
    private function buildQueryParams(?int $roleFilter): array
    {
        if ($roleFilter === null) {
            return [];
        }

        return [':roleId' => $roleFilter];
    }
}
