<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\UserAccount;
use App\DTOs\User\UserWithRole;

/**
 * Defines persistence operations for CMS user account management.
 */
interface ICmsUsersRepository
{
    /**
     * Returns all users joined with their role, with optional filtering and sorting.
     *
     * @return UserWithRole[]
     */
    public function findUsersWithRoles(
        ?int $roleFilter = null,
        ?string $search = null,
        string $sortBy = 'registered',
        string $sortDir = 'desc',
    ): array;

    /**
     * Finds a single user account by its primary key.
     */
    public function findById(int $id): ?UserAccount;

    /**
     * Inserts a new user account and returns the generated ID.
     */
    public function createUser(
        string $username,
        string $email,
        string $passwordHash,
        string $firstName,
        string $lastName,
        int $roleId,
    ): int;

    /**
     * Updates a user account's profile fields (does not change the password).
     */
    public function updateUser(
        int $id,
        string $username,
        string $email,
        string $firstName,
        string $lastName,
        int $roleId,
    ): void;

    /**
     * Updates only the password hash for a user.
     */
    public function updateUserPassword(int $id, string $passwordHash): void;

    /**
     * Deletes a user account by its ID.
     */
    public function deleteUser(int $id): void;

    /**
     * Checks whether a username is already taken.
     */
    public function existsByUsername(string $username): bool;

    /**
     * Checks whether an email address is already taken.
     */
    public function existsByEmail(string $email): bool;

    /**
     * Checks whether a username is taken by any user other than the excluded one.
     */
    public function existsByUsernameExcluding(string $username, int $excludeId): bool;

    /**
     * Checks whether an email is taken by any user other than the excluded one.
     */
    public function existsByEmailExcluding(string $email, int $excludeId): bool;
}
