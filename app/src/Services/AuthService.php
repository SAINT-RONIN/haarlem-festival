<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRoleId;
use App\Helpers\UserValidationHelper;
use App\Infrastructure\Interfaces\IEmailService;
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
    private const RESET_TOKEN_EXPIRY_HOURS = 1;

    public function __construct(
        private readonly UserAccountRepository $userRepository,
        private readonly PasswordResetTokenRepository $resetTokenRepository,
        private readonly IEmailService $emailService,
    ) {
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
     * Attempts login and additionally checks that the user has Administrator role.
     *
     * @param string $login Username or email
     * @param string $password Plain text password
     * @return array{success: bool, user?: \App\Models\UserAccount, error?: string}
     */
    public function attemptAdminLogin(string $login, string $password): array
    {
        $result = $this->attemptLogin($login, $password);

        if (!$result['success']) {
            return $result;
        }

        if ($result['user']->userRoleId !== UserRoleId::Administrator->value) {
            return $this->loginFailure();
        }

        return $result;
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

    private function validateNames(array $data, array $errors): array
    {
        return array_merge($errors, UserValidationHelper::checkNames($data['firstName'] ?? '', $data['lastName'] ?? ''));
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

    private function checkUsernameFormat(string $username): ?string
    {
        return UserValidationHelper::checkUsernameFormat($username);
    }

    private function validateEmail(string $email, array $errors): array
    {
        $email = trim($email);
        $formatError = UserValidationHelper::checkEmail($email);

        if ($formatError !== null) {
            $errors['email'] = $formatError;
            return $errors;
        }

        if ($this->userRepository->existsByEmail($email)) {
            $errors['email'] = 'This email is already registered.';
        }

        return $errors;
    }

    private function validatePasswords(string $password, string $confirm, array $errors): array
    {
        $lengthError = UserValidationHelper::checkPasswordLength($password);
        if ($lengthError !== null) {
            $errors['password'] = $lengthError;
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
            'roleId' => UserRoleId::Customer->value,
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
        $token = $this->resetTokenRepository->findValidByTokenHash($tokenHash);

        if ($token === null) {
            return [
                'valid' => false,
                'error' => 'This password reset link is invalid or has expired.',
            ];
        }

        return [
            'valid' => true,
            'tokenId' => $token->passwordResetTokenId,
            'userId' => $token->userAccountId,
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
        $passwordError = UserValidationHelper::checkPasswordLength($newPassword);
        if ($passwordError !== null) {
            return ['success' => false, 'error' => $passwordError];
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
