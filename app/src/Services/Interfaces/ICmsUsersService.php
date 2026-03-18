<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\UserWithRole;

interface ICmsUsersService
{
    /**
     * @return UserWithRole[]
     */
    public function getUsersWithRoles(?int $roleFilter = null): array;
}
