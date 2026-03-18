<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\CmsUsersMapper;
use App\Services\Interfaces\ICmsUsersService;
use App\Services\Interfaces\ISessionService;

class CmsUsersController
{
    public function __construct(
        private readonly ICmsUsersService $usersService,
        private readonly ISessionService $sessionService,
    ) {
    }

    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);

            $currentView = 'users';
            $roleFilter  = isset($_GET['role']) && is_numeric($_GET['role']) ? (int)$_GET['role'] : null;

            $usersData = $this->usersService->getUsersWithRoles($roleFilter);

            $viewModel = CmsUsersMapper::toListViewModel(
                $usersData,
                $_GET['role'] ?? '',
                $_GET['success'] ?? null,
                $_GET['error'] ?? null,
            );

            require __DIR__ . '/../Views/pages/cms/users.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
