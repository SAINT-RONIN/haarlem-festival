<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Domain\Auth\RegistrationFormData;
use App\Exceptions\AuthenticationException;
use App\Exceptions\ValidationException;
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

    public function showLogin(): void
    {
        $this->handlePageRequest(function (): void {
            $this->redirectIfLoggedIn();

            $error = $this->sessionService->consumeFlash('login_error');
            $success = $this->sessionService->consumeFlash('login_success');

            require __DIR__ . '/../Views/pages/login.php';
        });
    }

    public function login(): void
    {
        $this->handlePageRequest(function (): void {
            $this->processLogin();
        });
    }

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

    public function showForgotPassword(): void
    {
        $this->handlePageRequest(function (): void {
            $success = $this->sessionService->consumeFlash('forgot_success');
            $error = $this->sessionService->consumeFlash('forgot_error');

            require __DIR__ . '/../Views/pages/forgot-password.php';
        });
    }

    // Always shows success to prevent user enumeration
    public function forgotPassword(): void
    {
        $this->handlePageRequest(function (): void {
            $email = $this->readStringPostParam('email') ?? '';
            $this->authService->requestPasswordReset($email);
            $this->sessionService->setFlash('forgot_success', 'If an account exists with that email, you will receive a password reset link.');
            $this->redirectAndExit('/forgot-password');
        });
    }

    public function showResetPassword(): void
    {
        $this->handlePageRequest(function (): void {
            $token = $this->readStringQueryParam('token') ?? '';
            $error = null;
            $validToken = false;

            try {
                $this->authService->validateResetToken($token);
                $validToken = true;
                $error = $this->sessionService->consumeFlash('reset_error');
            } catch (AuthenticationException $e) {
                $error = $e->getMessage();
            }

            $this->sessionService->start();
            require __DIR__ . '/../Views/pages/reset-password.php';
        });
    }

    public function resetPassword(): void
    {
        $this->handlePageRequest(function (): void {
            $token = $this->readStringPostParam('token') ?? '';

            try {
                $this->authService->resetPassword($token, $_POST['password'] ?? '', $_POST['confirm_password'] ?? '');
            } catch (AuthenticationException|ValidationException $e) {
                $this->sessionService->setFlash('reset_error', $e->getMessage());
                $this->redirectAndExit('/reset-password?token=' . urlencode($token));
                return;
            }

            $this->sessionService->setFlash('login_success', 'Your password has been reset. Please log in with your new password.');
            $this->redirectAndExit('/login');
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

        try {
            $user = $this->authService->attemptLogin($login, $password);
        } catch (AuthenticationException $e) {
            $this->redirectWithError('/login', $e->getMessage());
            return;
        }

        $this->sessionService->login($user->userAccountId, $user->userRoleId);
        $this->sessionService->set('first_name', $user->firstName);
        $this->redirectAndExit($this->authService->resolvePostLoginRedirect($user->userRoleId));
    }
}
