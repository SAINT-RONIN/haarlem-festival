<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\SessionService;

/**
 * Controller for CMS authentication.
 *
 * Handles admin login and logout for the CMS backend.
 * Only administrators (RoleId=3) can access the CMS.
 */
class CmsAuthController
{
    private AuthService $authService;
    private SessionService $sessionService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->sessionService = new SessionService();
    }

    /**
     * Shows the CMS login form.
     * GET /cms/login
     */
    public function showLogin(): void
    {
        // Redirect if already logged in as admin
        if ($this->sessionService->isLoggedIn() && $this->sessionService->isAdmin()) {
            header('Location: /cms');
            exit;
        }

        $this->sessionService->start();
        $error = $_SESSION['cms_login_error'] ?? null;
        unset($_SESSION['cms_login_error']);

        require __DIR__ . '/../Views/pages/cms/login.php';
    }

    /**
     * Processes CMS login form submission.
     * POST /cms/login
     */
    public function login(): void
    {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        $result = $this->authService->attemptLogin($login, $password);

        if (!$result['success']) {
            $this->redirectWithError($result['error']);
            return;
        }

        $user = $result['user'];

        // Check if user is an administrator
        if ($user->userRoleId !== 3) {
            // Silent redirect - don't reveal that login worked but access denied
            $this->redirectWithError('Invalid username/email or password.');
            return;
        }

        // Login successful - create session
        $this->sessionService->login($user->userAccountId, $user->userRoleId);

        header('Location: /cms');
        exit;
    }

    /**
     * Logs out the admin user.
     * GET /cms/logout
     */
    public function logout(): void
    {
        $this->sessionService->logout();
        header('Location: /cms/login');
        exit;
    }

    /**
     * Helper to redirect with error message.
     */
    private function redirectWithError(string $error): void
    {
        $this->sessionService->start();
        $_SESSION['cms_login_error'] = $error;
        header('Location: /cms/login');
        exit;
    }

    /**
     * Static method to check admin access and redirect if not authorized.
     * Call this at the start of protected CMS controllers/routes.
     *
     * Silently redirects to CMS login - no 403 or error messages shown.
     */
    public static function requireAdmin(): void
    {
        $sessionService = new SessionService();
        $sessionService->start();

        if (!$sessionService->isLoggedIn() || !$sessionService->isAdmin()) {
            header('Location: /cms/login');
            exit;
        }
    }
}

