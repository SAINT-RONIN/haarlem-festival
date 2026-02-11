<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PasswordResetTokenRepository;
use App\Repositories\UserAccountRepository;
use App\Services\Interfaces\IAuthService;
use App\Utils\PasswordHasher;

/**
 * Service for authentication business logic.
 *
 * Handles login validation, registration, and password reset flows.
 * All password hashing uses Argon2id via PasswordHasher utility.
 */
class AuthService implements IAuthService
{
    private UserAccountRepository $userRepository;
    private PasswordResetTokenRepository $resetTokenRepository;
    private EmailService $emailService;

    private const PASSWORD_MIN_LENGTH = 8;
    private const USERNAME_MIN_LENGTH = 3;
    private const USERNAME_MAX_LENGTH = 60;
    private const RESET_TOKEN_EXPIRY_HOURS = 1;
    private const CUSTOMER_ROLE_ID = 1;

    public function __construct()
    {
        $this->userRepository = new UserAccountRepository();
        $this->resetTokenRepository = new PasswordResetTokenRepository();
        $this->emailService = new EmailService();
    }

    /**
     * Attempts to authenticate a user with username/email and password.
     *
     * @param string $login Username or email
     * @param string $password Plain text password
     * @return array{success: bool, user?: \App\Models\UserAccount, error?: string}
     */
    public function attemptLogin(string $login, string $password): array
    {
        $user = $this->userRepository->findByUsernameOrEmail($login);

        if ($user === null) {
            return $this->loginFailure();
        }

        if (!PasswordHasher::verify($password, $user->passwordHash)) {
            return $this->loginFailure();
        }

        return ['success' => true, 'user' => $user];
    }

    /**
     * Returns a generic login failure response (prevents account enumeration).
     */
    private function loginFailure(): array
    {
        return [
            'success' => false,
            'error' => 'Invalid username/email or password.',
        ];
    }

    /**
     * Validates registration data and returns any errors.
     *
     * @return array<string, string> Field name => error message
     */
    public function validateRegistration(array $data): array
    {
        $errors = [];
        $errors = $this->validateUsername($data['username'] ?? '', $errors);
        $errors = $this->validateEmail($data['email'] ?? '', $errors);
        $errors = $this->validatePasswords($data['password'] ?? '', $data['confirmPassword'] ?? '', $errors);
        $errors = $this->validateNames($data, $errors);

        return $errors;
    }

    /**
     * Validates first name and last name.
     */
    private function validateNames(array $data, array $errors): array
    {
        if (empty(trim($data['firstName'] ?? ''))) {
            $errors['firstName'] = 'First name is required.';
        }
        if (empty(trim($data['lastName'] ?? ''))) {
            $errors['lastName'] = 'Last name is required.';
        }

        return $errors;
    }

    /**
     * Validates username format and uniqueness.
     */
    private function validateUsername(string $username, array $errors): array
    {
        $username = trim($username);
        $formatError = $this->checkUsernameFormat($username);

        if ($formatError !== null) {
            $errors['username'] = $formatError;
            return $errors;
        }

        if ($this->userRepository->existsByUsername($username)) {
            $errors['username'] = 'This username is already taken.';
        }

        return $errors;
    }

    /**
     * Checks username format requirements.
     */
    private function checkUsernameFormat(string $username): ?string
    {
        if (empty($username)) {
            return 'Username is required.';
        }
        if (strlen($username) < self::USERNAME_MIN_LENGTH) {
            return 'Username must be at least ' . self::USERNAME_MIN_LENGTH . ' characters.';
        }
        if (strlen($username) > self::USERNAME_MAX_LENGTH) {
            return 'Username must be no more than ' . self::USERNAME_MAX_LENGTH . ' characters.';
        }
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            return 'Username can only contain letters, numbers, underscores, and hyphens.';
        }

        return null;
    }

    /**
     * Validates email format and uniqueness.
     */
    private function validateEmail(string $email, array $errors): array
    {
        $email = trim($email);

        if (empty($email)) {
            $errors['email'] = 'Email is required.';
            return $errors;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
            return $errors;
        }

        if ($this->userRepository->existsByEmail($email)) {
            $errors['email'] = 'This email is already registered.';
        }

        return $errors;
    }

    /**
     * Validates password strength and confirmation match.
     */
    private function validatePasswords(string $password, string $confirm, array $errors): array
    {
        if (empty($password)) {
            $errors['password'] = 'Password is required.';
            return $errors;
        }

        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            $errors['password'] = 'Password must be at least ' . self::PASSWORD_MIN_LENGTH . ' characters.';
            return $errors;
        }

        if ($password !== $confirm) {
            $errors['confirmPassword'] = 'Passwords do not match.';
        }

        return $errors;
    }

    /**
     * Registers a new user account.
     *
     * @return int The new user's ID
     */
    public function register(array $data): int
    {
        $passwordHash = PasswordHasher::hash($data['password']);

        return $this->userRepository->create([
            'roleId' => self::CUSTOMER_ROLE_ID,
            'username' => trim($data['username']),
            'email' => trim($data['email']),
            'passwordHash' => $passwordHash,
            'firstName' => trim($data['firstName']),
            'lastName' => trim($data['lastName']),
        ]);
    }

    /**
     * Initiates password reset flow.
     * Always returns true to prevent account enumeration.
     */
    public function requestPasswordReset(string $email): bool
    {
        $user = $this->userRepository->findByEmail(trim($email));

        // If user exists, create token and send email
        if ($user !== null) {
            $rawToken = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);
            $expiresAt = new \DateTimeImmutable('+' . self::RESET_TOKEN_EXPIRY_HOURS . ' hour');

            $this->resetTokenRepository->create($user->userAccountId, $tokenHash, $expiresAt);
            $this->emailService->sendPasswordResetEmail($user->email, $rawToken);
        }

        // Always return true (don't reveal if email exists)
        return true;
    }

    /**
     * Validates a password reset token from the URL.
     *
     * @param string $rawToken The raw token from the URL
     * @return array{valid: bool, tokenId?: int, userId?: int, error?: string}
     */
    public function validateResetToken(string $rawToken): array
    {
        $tokenHash = hash('sha256', $rawToken);
        $tokenData = $this->resetTokenRepository->findValidByTokenHash($tokenHash);

        if ($tokenData === null) {
            return [
                'valid' => false,
                'error' => 'This password reset link is invalid or has expired.',
            ];
        }

        return [
            'valid' => true,
            'tokenId' => (int)$tokenData['PasswordResetTokenId'],
            'userId' => (int)$tokenData['UserAccountId'],
        ];
    }

    /**
     * Resets a user's password using a valid reset token.
     *
     * @return array{success: bool, error?: string}
     */
    public function resetPassword(string $rawToken, string $newPassword, string $confirmPassword): array
    {
        // Validate the token first
        $tokenResult = $this->validateResetToken($rawToken);
        if (!$tokenResult['valid']) {
            return ['success' => false, 'error' => $tokenResult['error']];
        }

        // Validate new password
        if (strlen($newPassword) < self::PASSWORD_MIN_LENGTH) {
            return [
                'success' => false,
                'error' => 'Password must be at least ' . self::PASSWORD_MIN_LENGTH . ' characters.',
            ];
        }

        if ($newPassword !== $confirmPassword) {
            return ['success' => false, 'error' => 'Passwords do not match.'];
        }

        // Update password
        $passwordHash = PasswordHasher::hash($newPassword);
        $this->userRepository->updatePasswordHash($tokenResult['userId'], $passwordHash);

        // Mark token as used
        $this->resetTokenRepository->markAsUsed($tokenResult['tokenId']);

        return ['success' => true];
    }
}
