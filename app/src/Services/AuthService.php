<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Domain\Auth\RegistrationFormData;
use App\Enums\UserRoleId;
use App\Exceptions\AuthenticationException;
use App\Exceptions\ValidationException;
use App\Helpers\UserValidationHelper;
use App\Infrastructure\Interfaces\IEmailService;
use App\Models\PasswordResetToken;
use App\Models\UserAccount;
use App\Repositories\Interfaces\IPasswordResetTokenRepository;
use App\Repositories\Interfaces\IUserAccountRepository;
use App\Services\Interfaces\IAuthService;
use App\Utils\PasswordHasher;

/**
 * Owns all credential-related business logic: login (customer + admin), new-account
 * registration with field-level validation, and the full password-reset flow
 * (token generation, token validation, password update).
 *
 * Security notes:
 * - All password hashing uses Argon2id via PasswordHasher.
 * - Login and reset endpoints return generic errors to prevent account enumeration.
 * - Reset tokens are SHA-256 hashed before storage; only the raw token travels via email.
 */
class AuthService implements IAuthService
{
    private const RESET_TOKEN_EXPIRY_HOURS = 1;

    public function __construct(
        private readonly \PDO $pdo,
        private readonly IUserAccountRepository $userRepository,
        private readonly IPasswordResetTokenRepository $resetTokenRepository,
        private readonly IEmailService $emailService,
    ) {}

    /**
     * Attempts to authenticate a user with username/email and password.
     *
     * @throws AuthenticationException When credentials are invalid
     */
    public function attemptLogin(string $login, string $password): UserAccount
    {
        $user = $this->userRepository->findByUsernameOrEmail($login);

        if ($user === null || !PasswordHasher::verify($password, $user->passwordHash)) {
            throw new AuthenticationException('Invalid username/email or password.');
        }

        return $user;
    }

    /**
     * Attempts login and additionally checks that the user has Administrator role.
     *
     * @throws AuthenticationException When credentials are invalid or user is not an administrator
     */
    public function attemptAdminLogin(string $login, string $password): UserAccount
    {
        $user = $this->attemptLogin($login, $password);

        if ($user->userRoleId !== UserRoleId::Administrator->value) {
            throw new AuthenticationException('Invalid username/email or password.');
        }

        return $user;
    }

    /**
     * Validates registration data and returns any errors.
     *
     * @return array<string, string> Field name => error message
     */
    public function validateRegistration(RegistrationFormData $data): array
    {
        $errors = [];
        $errors = $this->validateUsername($data->username, $errors);
        $errors = $this->validateEmail($data->email, $errors);
        $errors = $this->validatePasswords($data->password, $data->confirmPassword, $errors);
        $errors = $this->validateNames($data, $errors);

        return $errors;
    }

    private function validateNames(RegistrationFormData $data, array $errors): array
    {
        return array_merge($errors, UserValidationHelper::checkNames($data->firstName, $data->lastName));
    }

    /**
     * Validates username format and uniqueness.
     */
    private function validateUsername(string $username, array $errors): array
    {
        $username = trim($username);
        $formatError = UserValidationHelper::checkUsernameFormat($username);

        if ($formatError !== null) {
            $errors['username'] = $formatError;
            return $errors;
        }

        if ($this->userRepository->existsByUsername($username)) {
            $errors['username'] = 'This username is already taken.';
        }

        return $errors;
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
     * @throws AuthenticationException When registration persistence fails
     */
    public function register(RegistrationFormData $data): int
    {
        try {
            $passwordHash = PasswordHasher::hash($data->password);

            return $this->userRepository->createUser(
                username: trim($data->username),
                email: trim($data->email),
                passwordHash: $passwordHash,
                firstName: trim($data->firstName),
                lastName: trim($data->lastName),
                roleId: UserRoleId::Customer->value,
            );
        } catch (\Throwable $error) {
            throw new AuthenticationException('Failed to register user account.', 0, $error);
        }
    }

    /**
     * Initiates password reset flow.
     * Always returns true to prevent account enumeration.
     */
    /**
     * @throws AuthenticationException When an unexpected failure occurs during the reset flow
     */
    public function requestPasswordReset(string $email): bool
    {
        $user = $this->userRepository->findByEmail(trim($email));

        // Defensive: if user doesn't exist, return true to prevent account enumeration
        if ($user === null) {
            return true;
        }

        try {
            // Generate a CSPRNG token; only the SHA-256 hash is persisted (the raw token travels via email)
            $rawToken = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);
            $expiresAt = new \DateTimeImmutable('+' . self::RESET_TOKEN_EXPIRY_HOURS . ' hour');

            $this->resetTokenRepository->create($user->userAccountId, $tokenHash, $expiresAt);
            $this->emailService->sendPasswordResetEmail($user->email, $rawToken);
        } catch (\Throwable $error) {
            throw new AuthenticationException('Failed to process password reset request.', 0, $error);
        }

        return true;
    }

    /**
     * Validates a password reset token from the URL.
     *
     * @throws AuthenticationException When the token is invalid or has expired
     */
    public function validateResetToken(string $rawToken): PasswordResetToken
    {
        $tokenHash = hash('sha256', $rawToken);
        $token = $this->resetTokenRepository->findValidByTokenHash($tokenHash);

        if ($token === null) {
            throw new AuthenticationException('This password reset link is invalid or has expired.');
        }

        return $token;
    }

    /**
     * Resets a user's password using a valid reset token.
     *
     * @throws AuthenticationException When the token is invalid or the password update fails
     * @throws ValidationException When the new password fails validation
     */
    public function resetPassword(string $rawToken, string $newPassword, string $confirmPassword): void
    {
        $token = $this->validateResetToken($rawToken);

        $passwordError = UserValidationHelper::checkPasswordLength($newPassword);
        if ($passwordError !== null) {
            throw new ValidationException($passwordError);
        }

        if ($newPassword !== $confirmPassword) {
            throw new ValidationException('Passwords do not match.');
        }

        // Wrap both writes in a transaction — password update + token invalidation must both succeed
        try {
            $this->pdo->beginTransaction();
            $passwordHash = PasswordHasher::hash($newPassword);
            $this->userRepository->updatePasswordHash($token->userAccountId, $passwordHash);
            $this->resetTokenRepository->markAsUsed($token->passwordResetTokenId);
            $this->pdo->commit();
        } catch (\Throwable $error) {
            $this->pdo->rollBack();
            throw new AuthenticationException('Failed to reset password.', 0, $error);
        }
    }

    /**
     * Returns the landing page that matches the authenticated user's role.
     */
    public function resolvePostLoginRedirect(?int $roleId): string
    {
        return match ($roleId) {
            UserRoleId::Employee->value => '/employee/scanner',
            UserRoleId::Administrator->value => '/cms',
            default => '/',
        };
    }
}
