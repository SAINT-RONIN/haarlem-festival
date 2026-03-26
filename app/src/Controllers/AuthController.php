<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\DTOs\Auth\RegistrationFormData;
use App\Services\Interfaces\IAuthService;
use App\Services\Interfaces\ICaptchaService;
use App\Services\Interfaces\ISessionService;

/**
 * Manages user authentication flows: login, registration, password reset, and logout.
 * All form submissions use flash messages for error/success feedback across redirects.
 */
class AuthController extends BaseController
{
    public function __construct(
        private readonly IAuthService $authService,
        private readonly ISessionService $sessionService,
        private readonly ICaptchaService $captchaService,
    ) {
    }

    /**
     * Renders the login page with any flash error/success messages.
     * GET /login
     */
    public function showLogin(): void
    {
        try {
            if ($this->sessionService->isLoggedIn()) {
                $this->redirect('/');
                exit;
            }

            $error = $this->sessionService->consumeFlash('login_error');
            $success = $this->sessionService->consumeFlash('login_success');

            require __DIR__ . '/../Views/pages/login.php';
        } catch (\Throwable $throwable) {
            ControllerErrorResponder::respond($throwable);
        }
    }

    /**
     * Authenticates the user with submitted credentials and starts a session on success.
     * POST /login
     */
    public function login(): void
    {
        try {
            $login = $this->readStringPostParam('login') ?? '';
            $password = $_POST['password'] ?? '';
            $result = $this->authService->attemptLogin($login, $password);

            if (!$result['success']) {
                $this->redirectWithError('/login', $result['error']);
                return;
            }

            $user = $result['user'];
            $this->sessionService->login($user->userAccountId, $user->userRoleId);
            $this->redirect('/');
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Destroys the user session and redirects to the homepage.
     * POST /logout
     */
    public function logout(): void
    {
        try {
            $this->sessionService->logout();
            $this->redirect('/');
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Renders the registration form with reCAPTCHA, repopulating fields from flash on validation failure.
     * GET /register
     */
    public function showRegister(): void
    {
        try {
            if ($this->sessionService->isLoggedIn()) {
                $this->redirect('/');
                exit;
            }

            $recaptchaSiteKey = $this->captchaService->getSiteKey();
            $errors = $this->sessionService->consumeFlash('register_errors') ?? [];
            $oldInput = $this->sessionService->consumeFlash('register_input') ?? [];

            require __DIR__ . '/../Views/pages/register.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Validates CAPTCHA and registration data, then creates a new user account.
     * POST /register
     */
    public function register(): void
    {
        try {
            // Extract form fields, then verify CAPTCHA before any further validation
            $data = $this->extractRegistrationData();
            if (!$this->captchaService->verify($this->readStringPostParam('g-recaptcha-response'), $this->readServerHeader('REMOTE_ADDR'))) {
                $this->redirectWithErrors('/register', ['captcha' => 'Please complete the CAPTCHA verification.'], $data->toArray());
                return;
            }
            $this->processRegistration($data);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Renders the forgot-password form with flash feedback.
     * GET /forgot-password
     */
    public function showForgotPassword(): void
    {
        try {
            $success = $this->sessionService->consumeFlash('forgot_success');
            $error = $this->sessionService->consumeFlash('forgot_error');

            require __DIR__ . '/../Views/pages/forgot-password.php';
        } catch (\Throwable $throwable) {
            ControllerErrorResponder::respond($throwable);
        }
    }

    /**
     * Triggers a password-reset email (always shows success to prevent user enumeration).
     * POST /forgot-password
     */
    public function forgotPassword(): void
    {
        try {
            $email = $this->readStringPostParam('email') ?? '';
            $this->authService->requestPasswordReset($email);
            $this->sessionService->setFlash('forgot_success', 'If an account exists with that email, you will receive a password reset link.');
            $this->redirect('/forgot-password');
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Validates the reset token from the query string and renders the password-reset form.
     * GET /reset-password?token=...
     */
    public function showResetPassword(): void
    {
        try {
            $token = $this->readStringQueryParam('token') ?? '';
            $result = $this->authService->validateResetToken($token);
            $this->sessionService->start();
            [$error, $validToken] = $this->resolveResetTokenState($result);
            require __DIR__ . '/../Views/pages/reset-password.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Processes the new password submission and redirects to login on success.
     * POST /reset-password
     */
    public function resetPassword(): void
    {
        try {
            $token = $this->readStringPostParam('token') ?? '';
            $result = $this->authService->resetPassword($token, $_POST['password'] ?? '', $_POST['confirm_password'] ?? '');
            $this->handleResetPasswordResult($result, $token);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    private function extractRegistrationData(): RegistrationFormData
    {
        return new RegistrationFormData(
            username: $this->readStringPostParam('username') ?? '',
            email: $this->readStringPostParam('email') ?? '',
            password: $_POST['password'] ?? '',
            confirmPassword: $_POST['confirm_password'] ?? '',
            firstName: $this->readStringPostParam('first_name') ?? '',
            lastName: $this->readStringPostParam('last_name') ?? '',
        );
    }

    private function processRegistration(RegistrationFormData $data): void
    {
        $dataArray = $data->toArray();
        $errors = $this->authService->validateRegistration($dataArray);
        if ($errors !== []) {
            $this->redirectWithErrors('/register', $errors, $dataArray);
            return;
        }
        $this->authService->register($dataArray);
        $this->sessionService->setFlash('login_success', 'Registration successful! Please log in.');
        $this->redirect('/login');
        exit;
    }

    private function resolveResetTokenState(array $result): array
    {
        if (!$result['valid']) {
            return [$result['error'], false];
        }
        $error = $this->sessionService->consumeFlash('reset_error');
        return [$error, true];
    }

    private function handleResetPasswordResult(array $result, string $token): void
    {
        if (!$result['success']) {
            $this->sessionService->setFlash('reset_error', $result['error']);
            $this->redirect('/reset-password?token=' . urlencode($token));
            exit;
        }
        $this->sessionService->setFlash('login_success', 'Your password has been reset. Please log in with your new password.');
        $this->redirect('/login');
        exit;
    }

    private function redirectWithError(string $url, string $error): void
    {
        $this->sessionService->setFlash('login_error', $error);
        $this->redirect($url);
        exit;
    }

    private function redirectWithErrors(string $url, array $errors, array $input): void
    {
        // Strip sensitive fields before storing input in flash for form repopulation
        unset($input['password'], $input['confirmPassword']);
        $this->sessionService->setFlash('register_errors', $errors);
        $this->sessionService->setFlash('register_input', $input);
        $this->redirect($url);
        exit;
    }
}
