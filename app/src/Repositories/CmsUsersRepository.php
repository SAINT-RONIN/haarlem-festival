<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UserWithRole;
use PDO;

/**
 * Fetches users joined with their role name for the CMS users list page.
 */
class CmsUsersRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    /**
     * Returns all users with their role name, optionally filtered by role ID.
     *
     * @return UserWithRole[]
     */
    public function findUsersWithRoles(?int $roleFilter = null): array
    {
        $sql    = $this->buildQuery($roleFilter !== null);
        $params = $roleFilter !== null ? [':roleId' => $roleFilter] : [];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map(
            fn(array $row) => UserWithRole::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC),
        );
    }

    private function buildQuery(bool $withRoleFilter): string
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
}
