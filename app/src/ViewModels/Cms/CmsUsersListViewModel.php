<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for the CMS users list page.
 */
final readonly class CmsUsersListViewModel
{
    /**
     * @param CmsUserListItemViewModel[] $users
     */
    public function __construct(
        public array   $users,
        public string  $selectedRole,
        public ?string $successMessage,
        public ?string $errorMessage,
    ) {}
}
