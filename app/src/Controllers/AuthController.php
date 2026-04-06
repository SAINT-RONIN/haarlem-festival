<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Auth\RegistrationFormData;
use App\Mappers\AuthMapper;
use App\Services\Interfaces\IAuthService;
use App\Services\Interfaces\ICaptchaService;
use App\Services\Interfaces\ISessionService;

/**
 * Manages user authentication flows: login, registration, password reset, and logout.
 * All form submissions use flash messages for error/success feedback across redirects.
 */
class AuthController extends BaseController
{
    private const CSRF_SCOPE_LOGOUT = 'logout';

    public function __construct(
        private readonly IAuthService $authService,
        ISessionService $sessionService,
        private readonly ICaptchaService $captchaService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Renders the login page with any flash error/success messages.
     * GET /login
     */
    public function showLogin(): void
    {
        $this->handlePageRequest(function (): void {
            $this->redirectIfLoggedIn();

            $error = $this->sessionService->consumeFlash('login_error');
            $success = $this->sessionService->consumeFlash('login_success');

            require __DIR__ . '/../Views/pages/login.php';
        });
    }

    /**
     * Authenticates the user with submitted credentials and starts a session on success.
     * POST /login
     */
    public function login(): void
    {
        $this->handlePageRequest(function (): void {
            $this->processLogin();
        });
    }

    /**
     * Destroys the user session and redirects to the homepage.
     * POST /logout
     */
    public function logout(): void
    {
        $this->handlePageRequest(function (): void {
            if (!$this->sessionService->isValidCsrfToken(self::CSRF_SCOPE_LOGOUT, $this->readStringPostParam('_csrf'))) {
                $this->redirectAndExit('/');
            }

            $this->sessionService->logout();
            $this->redirectAndExit('/');
        });
    }

    /**
     * Renders the registration form with reCAPTCHA, repopulating fields from flash on validation failure.
     * GET /register
     */
    public function showRegister(): void
    {
        $this->handlePageRequest(function (): void {
            $this->redirectIfLoggedIn();

            $recaptchaSiteKey = $this->captchaService->getSiteKey();
            $errors = $this->sessionService->consumeFlash('register_errors') ?? [];
            $oldInput = $this->sessionService->consumeFlash('register_input') ?? [];

            require __DIR__ . '/../Views/pages/register.php';
        });
    }

    /**
     * Validates CAPTCHA and registration data, then creates a new user account.
     * POST /register
     */
    public function register(): void
    {
        $this->handlePageRequest(function (): void {
            // Extract form fields, then verify CAPTCHA before any further validation
            $data = $this->extractRegistrationData();
            if (!$this->captchaService->verify($this->readStringPostParam('g-recaptcha-response'), $this->readServerHeader('REMOTE_ADDR'))) {
                $this->redirectWithErrors('/register', ['captcha' => 'Please complete the CAPTCHA verification.'], $data->toArray());
                return;
            }
            $this->processRegistration($data);
        });
    }

    /**
     * Renders the forgot-password form with flash feedback.
     * GET /forgot-password
     */
    public function showForgotPassword(): void
    {
        $this->handlePageRequest(function (): void {
            $success = $this->sessionService->consumeFlash('forgot_success');
            $error = $this->sessionService->consumeFlash('forgot_error');

            require __DIR__ . '/../Views/pages/forgot-password.php';
        });
    }

    /**
     * Triggers a password-reset email (always shows success to prevent user enumeration).
     * POST /forgot-password
     */
    public function forgotPassword(): void
    {
        $this->handlePageRequest(function (): void {
            $email = $this->readStringPostParam('email') ?? '';
            $this->authService->requestPasswordReset($email);
            $this->sessionService->setFlash('forgot_success', 'If an account exists with that email, you will receive a password reset link.');
            $this->redirectAndExit('/forgot-password');
        });
    }

    /**
     * Validates the reset token from the query string and renders the password-reset form.
     * GET /reset-password?token=...
     */
    public function showResetPassword(): void
    {
        $this->handlePageRequest(function (): void {
            $token = $this->readStringQueryParam('token') ?? '';
            $result = $this->authService->validateResetToken($token);
            $this->sessionService->start();
            [$error, $validToken] = $this->resolveResetTokenState($result);
            require __DIR__ . '/../Views/pages/reset-password.php';
        });
    }

    /**
     * Processes the new password submission and redirects to login on success.
     * POST /reset-password
     */
    public function resetPassword(): void
    {
        $this->handlePageRequest(function (): void {
            $token = $this->readStringPostParam('token') ?? '';
            $result = $this->authService->resetPassword($token, $_POST['password'] ?? '', $_POST['confirm_password'] ?? '');
            $this->handleResetPasswordResult($result, $token);
        });
    }

    private function extractRegistrationData(): RegistrationFormData
    {
        return AuthMapper::fromRegistrationInput([
            'username' => $this->readStringPostParam('username') ?? '',
            'email' => $this->readStringPostParam('email') ?? '',
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'first_name' => $this->readStringPostParam('first_name') ?? '',
            'last_name' => $this->readStringPostParam('last_name') ?? '',
        ]);
    }

    private function processRegistration(RegistrationFormData $data): void
    {
        $errors = $this->authService->validateRegistration($data);
        if ($errors !== []) {
            $this->redirectWithErrors('/register', $errors, $data->toArray());
            return;
        }
        $this->authService->register($data);
        $this->sessionService->setFlash('login_success', 'Registration successful! Please log in.');
        $this->redirectAndExit('/login');
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
            $this->redirectAndExit('/reset-password?token=' . urlencode($token));
        }
        $this->sessionService->setFlash('login_success', 'Your password has been reset. Please log in with your new password.');
        $this->redirectAndExit('/login');
    }

    private function redirectWithError(string $url, string $error): void
    {
        $this->sessionService->setFlash('login_error', $error);
        $this->redirectAndExit($url);
    }

    private function redirectWithErrors(string $url, array $errors, array $input): void
    {
        // Strip sensitive fields before storing input in flash for form repopulation
        unset($input['password'], $input['confirmPassword']);
        $this->sessionService->setFlash('register_errors', $errors);
        $this->sessionService->setFlash('register_input', $input);
        $this->redirectAndExit($url);
    }

    /** Redirects already-authenticated users away from login/register pages. */
    private function redirectIfLoggedIn(): void
    {
        if ($this->sessionService->isLoggedIn()) {
            $this->redirectAndExit($this->authService->resolvePostLoginRedirect($this->sessionService->getRoleId()));
        }
    }

    /** Reads login credentials, authenticates, and redirects on success or failure. */
    private function processLogin(): void
    {
        $login = $this->readStringPostParam('login') ?? '';
        $password = $_POST['password'] ?? '';
        $result = $this->authService->attemptLogin($login, $password);

        if (!$result['success']) {
            $this->redirectWithError('/login', $result['error']);
            return;
        }

        $user = $result['user'];
        $this->sessionService->login($user->userAccountId, $user->userRoleId);
        $this->redirectAndExit($this->authService->resolvePostLoginRedirect($user->userRoleId));
    }
}
