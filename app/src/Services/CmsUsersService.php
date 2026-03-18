<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserWithRole;
use App\Repositories\CmsUsersRepository;
use App\Services\Interfaces\ICmsUsersService;

/**
 * Service for the CMS Users list page.
 */
class CmsUsersService implements ICmsUsersService
{
    public function __construct(
        private readonly CmsUsersRepository $usersRepository,
    ) {
    }

    /**
     * Returns all users with their role name.
     * Optionally filtered by role ID.
     *
     * @return UserWithRole[]
     */
    public function getUsersWithRoles(?int $roleFilter = null): array
    {
        return $this->usersRepository->findUsersWithRoles($roleFilter);
    }
}
