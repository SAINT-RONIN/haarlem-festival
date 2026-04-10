<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Mappers\CmsUsersMapper;
use App\Services\Interfaces\ICmsUsersService;
use App\Services\Interfaces\ISessionService;

/**
 * CMS controller for managing user accounts.
 * Includes a safety guard preventing admins from deactivating their own account.
 */
class CmsUsersController extends CmsBaseController
{
    public function __construct(
        private readonly ICmsUsersService $usersService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'users';
            $f           = $this->extractListFilters();
            $usersData   = $this->usersService->getUsersWithRoles($f['roleFilter'], $f['search'] ?: null, $f['sortBy'], $f['sortDir']);
            $viewModel   = $this->buildListViewModel($f, $usersData);
            require __DIR__ . '/../Views/pages/cms/users.php';
        });
    }

    public function create(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $this->renderCreateUserForm();
        });
    }

    public function store(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $this->processUserStore();
        });
    }

    public function edit(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->renderUserEditPage($id);
        });
    }

    public function update(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->processUserUpdate($id);
        });
    }

    public function delete(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->processUserDelete($id);
        });
    }

    public function activate(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->validateCsrf('cms_user_delete', '/cms/users');
            $this->usersService->reactivateUser($id);
            $this->redirectWithFlash('User activated successfully.', 'success', '/cms/users');
        });
    }

    /** Builds the empty create-user form ViewModel with default role and renders it. */
    private function renderCreateUserForm(): void
    {
        $this->renderCreateForm(CmsUsersMapper::emptyFormData(), []);
    }

    /** Handles CSRF validation, form extraction, validation, and persistence for a new user. */
    private function processUserStore(): void
    {
        $this->validateCsrf('cms_user_create', '/cms/users/create');
        $data   = $this->extractUserFormData();
        // Re-render the form with errors if validation fails
        $errors = $this->usersService->validateForCreate($data['username'], $data['email'], $data['password'], $data['firstName'], $data['lastName'], $data['roleId']);
        if (!empty($errors)) {
            $this->renderCreateForm($data, $errors);
            return;
        }
        $this->usersService->createUser($data['username'], $data['email'], $data['password'], $data['firstName'], $data['lastName'], $data['roleId']);
        $this->redirectWithFlash('User created successfully.', 'success', '/cms/users');
    }

    /** Loads the user by ID, renders 404 if missing, otherwise renders the edit form. */
    private function renderUserEditPage(int $id): void
    {
        $user = $this->usersService->findById($id);
        if ($user === null) {
            $this->renderNotFoundPage();
            return;
        }
        $this->renderEditForm($id, CmsUsersMapper::fromUser($user), []);
    }

    /** Handles CSRF validation, form extraction, validation, and persistence for updating a user. */
    private function processUserUpdate(int $id): void
    {
        // Per-user CSRF scope prevents token reuse across different edit forms
        $this->validateCsrf('cms_user_edit_' . $id, '/cms/users/' . $id . '/edit');
        $data   = $this->extractUserFormData();
        // Empty password string is coerced to null so the service preserves the existing hash
        $errors = $this->usersService->validateForUpdate($id, $data['username'], $data['email'], $data['password'] ?: null, $data['firstName'], $data['lastName'], $data['roleId']);
        if (!empty($errors)) {
            $this->renderEditForm($id, $data, $errors);
            return;
        }
        $this->usersService->updateUser($id, $data['username'], $data['email'], $data['password'] ?: null, $data['firstName'], $data['lastName'], $data['roleId']);
        $this->redirectWithFlash('User updated successfully.', 'success', '/cms/users');
    }

    /** Validates CSRF, guards against self-deactivation, then soft-deletes the user. */
    private function processUserDelete(int $id): void
    {
        $this->validateCsrf('cms_user_delete', '/cms/users');
        // Guard: prevent self-deactivation
        if ($this->sessionService->getUserId() === $id) {
            $this->redirectWithFlash('You cannot deactivate your own account.', 'error', '/cms/users');
            return;
        }
        $this->usersService->deleteUser($id);
        $this->redirectWithFlash('User deactivated successfully.', 'success', '/cms/users');
    }

    /** Extracts and returns list filter params from GET. */
    private function extractListFilters(): array
    {
        return [
            'roleFilter' => $this->readPositiveIntQueryParam('role'),
            'search'     => $this->readStringQueryParam('search'),
            'sortBy'     => $this->readStringQueryParam('sort') ?? 'registered',
            'sortDir'    => $this->readStringQueryParam('dir') ?? 'desc',
        ];
    }

    /** Builds the list ViewModel from filters and user data. */
    private function buildListViewModel(array $filters, array $users): \App\ViewModels\Cms\CmsUsersListViewModel
    {
        return CmsUsersMapper::toListViewModel(
            $users,
            $this->readStringQueryParam('role') ?? '',
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
        return CmsUsersMapper::fromFormInput([
            'username'  => $this->readStringPostParam('username') ?? '',
            'email'     => $this->readStringPostParam('email') ?? '',
            'password'  => $_POST['password'] ?? '',
            'firstName' => $this->readStringPostParam('firstName') ?? '',
            'lastName'  => $this->readStringPostParam('lastName') ?? '',
            'roleId'    => $this->readOptionalIntPostParam('roleId'),
        ]);
    }

    /**
     * @param array<string, string> $errors
     */
    private function renderCreateForm(array $data, array $errors): void
    {
        $currentView = 'users';
        $viewModel   = CmsUsersMapper::toFormViewModel(
            null,
            $data['username'],
            $data['email'],
            $data['firstName'],
            $data['lastName'],
            $data['roleId'],
            $this->sessionService->getCsrfToken('cms_user_create'),
            '/cms/users',
            'Create User',
            $errors,
        );
        require __DIR__ . '/../Views/pages/cms/user-create.php';
    }

    /**
     * @param array<string, string> $errors
     */
    private function renderEditForm(int $id, array $data, array $errors): void
    {
        $user = $this->usersService->findById($id);
        if ($user === null) {
            $this->renderNotFoundPage();
            return;
        }

        $currentView = 'users';
        $viewModel   = CmsUsersMapper::toFormViewModel(
            $user,
            $data['username'],
            $data['email'],
            $data['firstName'],
            $data['lastName'],
            $data['roleId'],
            $this->sessionService->getCsrfToken('cms_user_edit_' . $id),
            '/cms/users/' . $id . '/edit',
            'Edit User',
            $errors,
        );
        require __DIR__ . '/../Views/pages/cms/user-edit.php';
    }
}
