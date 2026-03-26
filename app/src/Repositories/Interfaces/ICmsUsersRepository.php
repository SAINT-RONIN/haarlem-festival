<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\UserAccount;
use App\Models\UserWithRole;

interface ICmsUsersRepository
{
    /** @return UserWithRole[] */
    public function findUsersWithRoles(
        ?int $roleFilter = null,
        ?string $search = null,
        string $sortBy = 'registered',
        string $sortDir = 'desc',
    ): array;

    public function findById(int $id): ?UserAccount;

    public function createUser(
        string $username,
        string $email,
        string $passwordHash,
        string $firstName,
        string $lastName,
        int $roleId,
    ): int;

    public function updateUser(
        int $id,
        string $username,
        string $email,
        string $firstName,
        string $lastName,
        int $roleId,
    ): void;

    public function updateUserPassword(int $id, string $passwordHash): void;

    public function deleteUser(int $id): void;

    public function existsByUsername(string $username): bool;

    public function existsByEmail(string $email): bool;

    public function existsByUsernameExcluding(string $username, int $excludeId): bool;

    public function existsByEmailExcluding(string $email, int $excludeId): bool;
}
