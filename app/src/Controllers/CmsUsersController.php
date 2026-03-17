<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\CmsUsersService;
use App\ViewModels\Cms\CmsUserListItemViewModel;
use App\ViewModels\Cms\CmsUsersListViewModel;

class CmsUsersController
{
    private CmsUsersService $usersService;

    public function __construct()
    {
        $this->usersService = new CmsUsersService();
    }

    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin();

            $currentView = 'users';
            $roleFilter  = isset($_GET['role']) && is_numeric($_GET['role']) ? (int)$_GET['role'] : null;

            $usersData = $this->usersService->getUsersWithRoles($roleFilter);

            $users = array_map(
                static fn(array $row): CmsUserListItemViewModel => CmsUserListItemViewModel::fromRow($row),
                $usersData
            );

            $viewModel = new CmsUsersListViewModel(
                users:          $users,
                selectedRole:   $_GET['role'] ?? '',
                successMessage: $_GET['success'] ?? null,
                errorMessage:   $_GET['error'] ?? null,
            );

            require __DIR__ . '/../Views/pages/cms/users.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
