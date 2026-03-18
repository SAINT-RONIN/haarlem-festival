<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\Interfaces\IAuthService;
use App\Services\Interfaces\ICaptchaService;
use App\Services\Interfaces\ISessionService;

class AuthController
{
    public function __construct(
        private readonly IAuthService $authService,
        private readonly ISessionService $sessionService,
        private readonly ICaptchaService $captchaService,
    ) {
    }

    public function showLogin(): void
    {
        try {
            if ($this->sessionService->isLoggedIn()) {
                header('Location: /');
                exit;
            }

            $this->sessionService->start();
            $error = $_SESSION['login_error'] ?? null;
            $success = $_SESSION['login_success'] ?? null;
            unset($_SESSION['login_error'], $_SESSION['login_success']);

            require __DIR__ . '/../Views/pages/login.php';
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
                $this->redirectWithError('/login', $result['error']);
                return;
            }

            $user = $result['user'];
            $this->sessionService->login($user->userAccountId, $user->userRoleId);
            header('Location: /');
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function logout(): void
    {
        try {
            $this->sessionService->logout();
            header('Location: /');
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function showRegister(): void
    {
        try {
            if ($this->sessionService->isLoggedIn()) {
                header('Location: /');
                exit;
            }

            $this->sessionService->start();
            $recaptchaSiteKey = $this->captchaService->getSiteKey();
            $errors = $_SESSION['register_errors'] ?? [];
            $oldInput = $_SESSION['register_input'] ?? [];
            unset($_SESSION['register_errors'], $_SESSION['register_input']);

            require __DIR__ . '/../Views/pages/register.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function register(): void
    {
        try {
            $data = [
                'username' => $_POST['username'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'confirmPassword' => $_POST['confirm_password'] ?? '',
                'firstName' => $_POST['first_name'] ?? '',
                'lastName' => $_POST['last_name'] ?? '',
            ];

            if (!$this->captchaService->verify($_POST['g-recaptcha-response'] ?? null, $_SERVER['REMOTE_ADDR'] ?? null)) {
                $this->redirectWithErrors('/register', ['captcha' => 'Please complete the CAPTCHA verification.'], $data);
                return;
            }

            $errors = $this->authService->validateRegistration($data);
            if ($errors !== []) {
                $this->redirectWithErrors('/register', $errors, $data);
                return;
            }

            $this->authService->register($data);
            $this->sessionService->start();
            $_SESSION['login_success'] = 'Registration successful! Please log in.';
            header('Location: /login');
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function showForgotPassword(): void
    {
        try {
            $this->sessionService->start();
            $success = $_SESSION['forgot_success'] ?? null;
            $error = $_SESSION['forgot_error'] ?? null;
            unset($_SESSION['forgot_success'], $_SESSION['forgot_error']);

            require __DIR__ . '/../Views/pages/forgot-password.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function forgotPassword(): void
    {
        try {
            $email = trim($_POST['email'] ?? '');
            $this->authService->requestPasswordReset($email);
            $this->sessionService->start();
            $_SESSION['forgot_success'] = 'If an account exists with that email, you will receive a password reset link.';
            header('Location: /forgot-password');
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function showResetPassword(): void
    {
        try {
            $token = $_GET['token'] ?? '';
            $result = $this->authService->validateResetToken($token);

            $this->sessionService->start();
            if (!$result['valid']) {
                $error = $result['error'];
                $validToken = false;
            } else {
                $error = $_SESSION['reset_error'] ?? null;
                $validToken = true;
                unset($_SESSION['reset_error']);
            }

            require __DIR__ . '/../Views/pages/reset-password.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function resetPassword(): void
    {
        try {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $result = $this->authService->resetPassword($token, $password, $confirmPassword);

            if (!$result['success']) {
                $this->sessionService->start();
                $_SESSION['reset_error'] = $result['error'];
                header('Location: /reset-password?token=' . urlencode($token));
                exit;
            }

            $this->sessionService->start();
            $_SESSION['login_success'] = 'Your password has been reset. Please log in with your new password.';
            header('Location: /login');
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    private function redirectWithError(string $url, string $error): void
    {
        $this->sessionService->start();
        $_SESSION['login_error'] = $error;
        header('Location: ' . $url);
        exit;
    }

    private function redirectWithErrors(string $url, array $errors, array $input): void
    {
        $this->sessionService->start();
        $_SESSION['register_errors'] = $errors;
        unset($input['password'], $input['confirmPassword']);
        $_SESSION['register_input'] = $input;
        header('Location: ' . $url);
        exit;
    }
}
