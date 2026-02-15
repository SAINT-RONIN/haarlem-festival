<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRoleName;

/**
 * Represents a single row from the `UserRole` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class UserRole
{
    /*
     * Purpose: Defines user roles (Customer, Employee, Administrator)
     * for authorization and access control.
     */

    public function __construct(
        public readonly int          $userRoleId,
        public readonly UserRoleName $roleName,
    )
    {
    }

    /**
     * Creates a UserRole instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            userRoleId: (int)$row['UserRoleId'],
            roleName: UserRoleName::from($row['RoleName']),
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'UserRoleId' => $this->userRoleId,
            'RoleName' => $this->roleName->value,
        ];
    }
}
