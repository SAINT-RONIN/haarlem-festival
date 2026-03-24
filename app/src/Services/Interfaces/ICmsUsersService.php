<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\UserAccount;
use App\Models\UserWithRole;

/**
 * Defines the contract for CMS user account management (CRUD and validation).
 */
interface ICmsUsersService
{
    /**
     * Returns all users with their role information, with optional filtering and sorting.
     *
     * @return UserWithRole[]
     */
    public function getUsersWithRoles(
        ?int $roleFilter = null,
        ?string $search = null,
        string $sortBy = 'registered',
        string $sortDir = 'desc',
    ): array;

    /**
     * Finds a single user account by its ID, or null if not found.
     */
    public function findById(int $id): ?UserAccount;

    /**
     * Validates fields for creating a new user, returning a map of field names to error messages.
     *
     * @return array<string, string>
     */
    public function validateForCreate(
        string $username,
        string $email,
        string $password,
        string $firstName,
        string $lastName,
    ): array;

    /**
     * Validates fields for updating an existing user, returning a map of field names to error messages.
     *
     * @return array<string, string>
     */
    public function validateForUpdate(
        int $id,
        string $username,
        string $email,
        ?string $password,
        string $firstName,
        string $lastName,
    ): array;

    /**
     * Creates a new user account with the given details and returns the new user ID.
     */
    public function createUser(
        string $username,
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        int $roleId,
    ): int;

    /**
     * Updates an existing user account. If password is null, the password is left unchanged.
     */
    public function updateUser(
        int $id,
        string $username,
        string $email,
        ?string $password,
        string $firstName,
        string $lastName,
        int $roleId,
    ): void;

    /**
     * Deletes a user account by its ID.
     */
    public function deleteUser(int $id): void;
}
