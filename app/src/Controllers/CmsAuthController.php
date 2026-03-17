<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Enums\UserRoleId;
use App\Services\AuthService;
use App\Services\SessionService;

class CmsAuthController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly SessionService $sessionService,
    ) {
    }

    public function showLogin(): void
    {
        try {
            if ($this->sessionService->isLoggedIn() && $this->sessionService->isAdmin()) {
                header('Location: /cms');
                exit;
            }

            $this->sessionService->start();
            $error = $_SESSION['cms_login_error'] ?? null;
            unset($_SESSION['cms_login_error']);
            require __DIR__ . '/../Views/pages/cms/login.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function login(): void
    {
        try {
            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';
            $result = $this->authService->attemptLogin($login, $password);

            if (!$result['success']) {
                $this->redirectWithError($result['error']);
                return;
            }

            $user = $result['user'];
            if ($user->userRoleId !== UserRoleId::Administrator->value) {
                $this->redirectWithError('Invalid username/email or password.');
                return;
            }

            $this->sessionService->login($user->userAccountId, $user->userRoleId);
            header('Location: /cms');
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

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
        $this->sessionService->start();
        $_SESSION['cms_login_error'] = $error;
        header('Location: /cms/login');
        exit;
    }

    public static function requireAdmin(): void
    {
        try {
            $sessionService = new SessionService();
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
