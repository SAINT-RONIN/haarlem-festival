<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\UserAccount;
use App\Models\UserWithRole;

interface ICmsUsersService
{
    /**
     * @return UserWithRole[]
     */
    public function getUsersWithRoles(
        ?int $roleFilter = null,
        ?string $search = null,
        string $sortBy = 'registered',
        string $sortDir = 'desc',
    ): array;

    public function findById(int $id): ?UserAccount;

    /**
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

    public function createUser(
        string $username,
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        int $roleId,
    ): int;

    public function updateUser(
        int $id,
        string $username,
        string $email,
        ?string $password,
        string $firstName,
        string $lastName,
        int $roleId,
    ): void;

    public function deleteUser(int $id): void;
}
