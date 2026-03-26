<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\Interfaces\IAuthService;
use App\Services\Interfaces\ISessionService;

/**
 * Handles CMS admin authentication: login, logout, and access gating.
 *
 * Also exposes the static requireAdmin() guard used by all other CMS
 * controllers to enforce admin-only access before processing requests.
 */
class CmsAuthController extends BaseController
{
    public function __construct(
        private readonly IAuthService $authService,
        private readonly ISessionService $sessionService,
    ) {
    }

    /**
     * Renders the CMS login page, or redirects to the dashboard if already authenticated.
     * GET /cms/login
     */
    public function showLogin(): void
    {
        try {
            // Skip the login form if the admin is already authenticated
            if ($this->sessionService->isLoggedIn() && $this->sessionService->isAdmin()) {
                header('Location: /cms');
                exit;
            }

            $error = $this->sessionService->consumeFlash('cms_login_error');
            require __DIR__ . '/../Views/pages/cms/login.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Authenticates admin credentials and starts a session on success.
     * POST /cms/login
     */
    public function login(): void
    {
        try {
            $login = $this->readStringPostParam('login') ?? '';
            $password = $_POST['password'] ?? '';
            // Delegate credential verification to the auth service
            $result = $this->authService->attemptAdminLogin($login, $password);

            if (!$result['success']) {
                $this->redirectWithError($result['error']);
                return;
            }

            $user = $result['user'];
            $this->sessionService->login($user->userAccountId, $user->userRoleId);
            header('Location: /cms');
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Destroys the admin session and redirects to the login page.
     * POST /cms/logout
     */
    public function logout(): void
    {
        try {
            $this->sessionService->logout();
            header('Location: /cms/login');
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    private function redirectWithError(string $error): void
    {
        $this->sessionService->setFlash('cms_login_error', $error);
        header('Location: /cms/login');
        exit;
    }

    /**
     * Guard that ensures the current session belongs to an admin user.
     * Redirects to the login page if the session is missing or non-admin.
     * Called at the top of every CMS controller action.
     */
    public static function requireAdmin(ISessionService $sessionService): void
    {
        try {
            $sessionService->start();

            if (!$sessionService->isLoggedIn() || !$sessionService->isAdmin()) {
                header('Location: /cms/login');
                exit;
            }
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
