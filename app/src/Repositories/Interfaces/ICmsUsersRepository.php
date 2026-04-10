<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\UserAccount;
use App\DTOs\Domain\User\UserWithRole;

/**
 * Defines read-only persistence operations for CMS user account management.
 * Write operations live in IUserAccountRepository.
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
     * Finds a single user account by its primary key (includes inactive users, for admin editing).
     */
    public function findById(int $id): ?UserAccount;

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
