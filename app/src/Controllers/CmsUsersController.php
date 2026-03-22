<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Enums\UserRoleId;
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
            $f           = $this->extractListFilters();
            $usersData   = $this->usersService->getUsersWithRoles($f['roleFilter'], $f['search'] ?: null, $f['sortBy'], $f['sortDir']);
            $viewModel   = $this->buildListViewModel($f, $usersData);
            require __DIR__ . '/../Views/pages/cms/users.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function create(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $currentView = 'users';
            $viewModel   = CmsUsersMapper::toFormViewModel(
                null, '', '', '', '', UserRoleId::Customer->value,
                $this->sessionService->getCsrfToken('cms_user_create'),
                '/cms/users', 'Create User', [],
            );
            require __DIR__ . '/../Views/pages/cms/user-create.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function store(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $this->validateCsrf('cms_user_create', '/cms/users/create');
            $data   = $this->extractUserFormData();
            $errors = $this->usersService->validateForCreate($data['username'], $data['email'], $data['password'], $data['firstName'], $data['lastName']);
            if (!empty($errors)) {
                $this->renderCreateForm($data['username'], $data['email'], $data['firstName'], $data['lastName'], $data['roleId'], $errors);
                return;
            }
            $this->usersService->createUser($data['username'], $data['email'], $data['password'], $data['firstName'], $data['lastName'], $data['roleId']);
            $this->redirectWithFlash('User created successfully.', 'success', '/cms/users');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function edit(int $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $user = $this->usersService->findById($id);
            if ($user === null) {
                http_response_code(404);
                require __DIR__ . '/../Views/pages/errors/404.php';
                return;
            }
            $this->renderEditForm($id, $user->username, $user->email, $user->firstName, $user->lastName, $user->userRoleId, []);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function update(int $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $this->validateCsrf('cms_user_edit_' . $id, '/cms/users/' . $id . '/edit');
            $data   = $this->extractUserFormData();
            $errors = $this->usersService->validateForUpdate($id, $data['username'], $data['email'], $data['password'] ?: null, $data['firstName'], $data['lastName']);
            if (!empty($errors)) {
                $this->renderEditForm($id, $data['username'], $data['email'], $data['firstName'], $data['lastName'], $data['roleId'], $errors);
                return;
            }
            $this->usersService->updateUser($id, $data['username'], $data['email'], $data['password'] ?: null, $data['firstName'], $data['lastName'], $data['roleId']);
            $this->redirectWithFlash('User updated successfully.', 'success', '/cms/users');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function delete(int $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $this->validateCsrf('cms_user_delete', '/cms/users');
            if ($this->sessionService->getUserId() === $id) {
                $this->redirectWithFlash('You cannot deactivate your own account.', 'error', '/cms/users');
            }
            $this->usersService->deleteUser($id);
            $this->redirectWithFlash('User deactivated successfully.', 'success', '/cms/users');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /** Validates CSRF or redirects with error flash. */
    private function validateCsrf(string $scope, string $redirectUrl): void
    {
        if (!$this->sessionService->isValidCsrfToken($scope, $_POST['_csrf'] ?? null)) {
            $this->sessionService->setFlash('error', 'Invalid CSRF token. Please try again.');
            header('Location: ' . $redirectUrl);
            exit;
        }
    }

    /** Flashes a message and redirects. */
    private function redirectWithFlash(string $message, string $type, string $url): void
    {
        $this->sessionService->setFlash($type, $message);
        header('Location: ' . $url);
        exit;
    }

    /** Extracts and returns list filter params from GET. */
    private function extractListFilters(): array
    {
        return [
            'roleFilter' => isset($_GET['role']) && is_numeric($_GET['role']) ? (int)$_GET['role'] : null,
            'search'     => isset($_GET['search']) ? trim($_GET['search']) : null,
            'sortBy'     => $_GET['sort'] ?? 'registered',
            'sortDir'    => $_GET['dir'] ?? 'desc',
        ];
    }

    /** Builds the list ViewModel from filters and user data. */
    private function buildListViewModel(array $filters, array $users): \App\ViewModels\Cms\CmsUsersListViewModel
    {
        return \App\Mappers\CmsUsersMapper::toListViewModel(
            $users,
            $_GET['role'] ?? '',
            $this->sessionService->consumeFlash('success'),
            $this->sessionService->consumeFlash('error'),
            $filters['search'] ?? '',
            $filters['sortBy'],
            $filters['sortDir'],
            $this->sessionService->getCsrfToken('cms_user_delete'),
        );
    }

    private function extractUserFormData(): array
    {
        return [
            'username'  => trim($_POST['username'] ?? ''),
            'email'     => trim($_POST['email'] ?? ''),
            'password'  => $_POST['password'] ?? '',
            'firstName' => trim($_POST['firstName'] ?? ''),
            'lastName'  => trim($_POST['lastName'] ?? ''),
            'roleId'    => (int)($_POST['roleId'] ?? UserRoleId::Customer->value),
        ];
    }

    /**
     * @param array<string, string> $errors
     */
    private function renderCreateForm(string $username, string $email, string $firstName, string $lastName, int $roleId, array $errors): void
    {
        $currentView = 'users';
        $viewModel   = CmsUsersMapper::toFormViewModel(
            null, $username, $email, $firstName, $lastName, $roleId,
            $this->sessionService->getCsrfToken('cms_user_create'),
            '/cms/users', 'Create User', $errors,
        );
        require __DIR__ . '/../Views/pages/cms/user-create.php';
    }

    /**
     * @param array<string, string> $errors
     */
    private function renderEditForm(int $id, string $username, string $email, string $firstName, string $lastName, int $roleId, array $errors): void
    {
        $currentView = 'users';
        $viewModel   = CmsUsersMapper::toFormViewModel(
            $this->usersService->findById($id),
            $username, $email, $firstName, $lastName, $roleId,
            $this->sessionService->getCsrfToken('cms_user_edit_' . $id),
            '/cms/users/' . $id . '/edit', 'Edit User', $errors,
        );
        require __DIR__ . '/../Views/pages/cms/user-edit.php';
    }
}
