<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\AuthenticationException;
use App\Services\Interfaces\IAuthService;
use App\Services\Interfaces\ISessionService;

/**
 * Handles CMS admin authentication: login, logout, and access gating.
 */
class CmsAuthController extends BaseController
{
    private const CSRF_SCOPE_LOGOUT = 'cms_logout';

    public function __construct(
        private readonly IAuthService $authService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Renders the CMS login page, or redirects to the dashboard if already authenticated.
     * GET /cms/login
     */
    public function showLogin(): void
    {
        $this->handlePageRequest(function (): void {
            // Skip the login form if the admin is already authenticated
            if ($this->sessionService->isLoggedIn() && $this->sessionService->isAdmin()) {
                $this->redirectAndExit('/cms');
            }

            $error = $this->sessionService->consumeFlash('cms_login_error');
            require __DIR__ . '/../Views/pages/cms/login.php';
        });
    }

    /**
     * Authenticates admin credentials and starts a session on success.
     * POST /cms/login
     */
    public function login(): void
    {
        $this->handlePageRequest(function (): void {
            $this->processLogin();
        });
    }

    /**
     * Destroys the admin session and redirects to the login page.
     * POST /cms/logout
     */
    public function logout(): void
    {
        $this->handlePageRequest(function (): void {
            if (!$this->sessionService->isValidCsrfToken(self::CSRF_SCOPE_LOGOUT, $this->readStringPostParam('_csrf'))) {
                $this->redirectAndExit('/cms');
            }

            $this->sessionService->logout();
            $this->redirectAndExit('/cms/login');
        });
    }

    /** Reads admin credentials, authenticates, and redirects on success or failure. */
    private function processLogin(): void
    {
        $login = $this->readStringPostParam('login') ?? '';
        $password = $_POST['password'] ?? '';

        try {
            $user = $this->authService->attemptAdminLogin($login, $password);
        } catch (AuthenticationException $e) {
            $this->redirectWithError($e->getMessage());
            return;
        }

        $this->sessionService->login($user->userAccountId, $user->userRoleId);
        $this->sessionService->set('first_name', $user->firstName);
        $this->redirectAndExit('/cms');
    }

    private function redirectWithError(string $error): void
    {
        $this->sessionService->setFlash('cms_login_error', $error);
        $this->redirectAndExit('/cms/login');
    }
}
