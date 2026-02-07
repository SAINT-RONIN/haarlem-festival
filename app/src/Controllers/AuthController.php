<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\CaptchaService;
use App\Services\SessionService;

/**
 * Controller for website authentication pages.
 *
 * Handles login, logout, registration, and password reset for website visitors.
 * Keeps logic thin - delegates to services for business rules.
 */
class AuthController
{
    private AuthService $authService;
    private SessionService $sessionService;
    private CaptchaService $captchaService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->sessionService = new SessionService();
        $this->captchaService = new CaptchaService();
    }

    /**
     * Shows the login form.
     * GET /login
     */
    public function showLogin(): void
    {
        // Redirect if already logged in
        if ($this->sessionService->isLoggedIn()) {
            header('Location: /');
            exit;
        }

        $this->sessionService->start();
        $error = $_SESSION['login_error'] ?? null;
        $success = $_SESSION['login_success'] ?? null;
        unset($_SESSION['login_error'], $_SESSION['login_success']);

        require __DIR__ . '/../Views/pages/login.php';
    }

    /**
     * Processes login form submission.
     * POST /login
     */
    public function login(): void
    {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        $result = $this->authService->attemptLogin($login, $password);

        if (!$result['success']) {
            $this->redirectWithError('/login', $result['error']);
            return;
        }

        // Login successful - create session
        $user = $result['user'];
        $this->sessionService->login($user->userAccountId, $user->userRoleId);

        header('Location: /');
        exit;
    }

    /**
     * Logs out the current user.
     * GET /logout
     */
    public function logout(): void
    {
        $this->sessionService->logout();
        header('Location: /');
        exit;
    }

    /**
     * Shows the registration form.
     * GET /register
     */
    public function showRegister(): void
    {
        // Redirect if already logged in
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
    }

    /**
     * Processes registration form submission.
     * POST /register
     */
    public function register(): void
    {
        $data = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'confirmPassword' => $_POST['confirm_password'] ?? '',
            'firstName' => $_POST['first_name'] ?? '',
            'lastName' => $_POST['last_name'] ?? '',
        ];

        // Verify reCAPTCHA
        if (!$this->captchaService->verify($_POST['g-recaptcha-response'] ?? null)) {
            $this->redirectWithErrors('/register', ['captcha' => 'Please complete the CAPTCHA verification.'], $data);
            return;
        }

        // Validate registration data
        $errors = $this->authService->validateRegistration($data);

        if (!empty($errors)) {
            $this->redirectWithErrors('/register', $errors, $data);
            return;
        }

        // Register the user
        $this->authService->register($data);

        // Redirect to login with success message
        $this->sessionService->start();
        $_SESSION['login_success'] = 'Registration successful! Please log in.';
        header('Location: /login');
        exit;
    }

    /**
     * Shows the forgot password form.
     * GET /forgot-password
     */
    public function showForgotPassword(): void
    {
        $this->sessionService->start();
        $success = $_SESSION['forgot_success'] ?? null;
        $error = $_SESSION['forgot_error'] ?? null;
        unset($_SESSION['forgot_success'], $_SESSION['forgot_error']);

        require __DIR__ . '/../Views/pages/forgot-password.php';
    }

    /**
     * Processes forgot password form submission.
     * POST /forgot-password
     */
    public function forgotPassword(): void
    {
        $email = trim($_POST['email'] ?? '');

        // Always show success message (prevents account enumeration)
        $this->authService->requestPasswordReset($email);

        $this->sessionService->start();
        $_SESSION['forgot_success'] = 'If an account exists with that email, you will receive a password reset link.';
        header('Location: /forgot-password');
        exit;
    }

    /**
     * Shows the reset password form.
     * GET /reset-password?token=xxx
     */
    public function showResetPassword(): void
    {
        $token = $_GET['token'] ?? '';

        // Validate token
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
    }

    /**
     * Processes reset password form submission.
     * POST /reset-password
     */
    public function resetPassword(): void
    {
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

        // Success - redirect to login
        $this->sessionService->start();
        $_SESSION['login_success'] = 'Your password has been reset. Please log in with your new password.';
        header('Location: /login');
        exit;
    }

    /**
     * Helper to redirect with a single error message.
     */
    private function redirectWithError(string $url, string $error): void
    {
        $this->sessionService->start();
        $_SESSION['login_error'] = $error;
        header('Location: ' . $url);
        exit;
    }

    /**
     * Helper to redirect with multiple errors and preserve input.
     */
    private function redirectWithErrors(string $url, array $errors, array $input): void
    {
        $this->sessionService->start();
        $_SESSION['register_errors'] = $errors;
        // Don't preserve password in session
        unset($input['password'], $input['confirmPassword']);
        $_SESSION['register_input'] = $input;
        header('Location: ' . $url);
        exit;
    }
}
