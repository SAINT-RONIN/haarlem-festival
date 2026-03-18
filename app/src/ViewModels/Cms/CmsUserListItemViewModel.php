<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for a single user row in the CMS users list.
 *
 * All values are display-ready. Created by CmsUsersMapper.
 */
final readonly class CmsUserListItemViewModel
{
    public function __construct(
        public int    $userAccountId,
        public string $username,
        public string $email,
        public string $fullName,
        public string $roleName,
        public string $roleBadgeClass,
        public bool   $isActive,
        public string $statusText,
        public string $statusBadgeClass,
        public string $registeredAt,
    ) {}
}
