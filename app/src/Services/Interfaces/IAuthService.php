<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Auth\RegistrationFormData;
use App\Models\PasswordResetToken;
use App\Models\UserAccount;

/**
 * Contract for user authentication and credential management.
 * Covers login (customer and admin), registration with validation,
 * and the full password-reset flow (request, validate token, reset).
 */
interface IAuthService
{
    /**
     * Attempts to authenticate a user with username/email and password.
     *
     * @throws \App\Exceptions\AuthenticationException When credentials are invalid
     */
    public function attemptLogin(string $login, string $password): UserAccount;

    /**
     * Attempts to authenticate and verifies the user has Administrator role.
     *
     * @throws \App\Exceptions\AuthenticationException When credentials are invalid or user is not an administrator
     */
    public function attemptAdminLogin(string $login, string $password): UserAccount;

    /**
     * Validates registration data and returns any errors.
     *
     * @return array<string, string> Field name => error message
     */
    public function validateRegistration(RegistrationFormData $data): array;

    /**
     * Registers a new user account.
     *
     * @return int The new user's ID
     */
    public function register(RegistrationFormData $data): int;

    /**
     * Initiates password reset flow.
     *
     * @param string $email User's email
     * @return bool Always returns true to prevent account enumeration
     */
    public function requestPasswordReset(string $email): bool;

    /**
     * Validates a password reset token from the URL.
     *
     * @throws \App\Exceptions\AuthenticationException When the token is invalid or has expired
     */
    public function validateResetToken(string $rawToken): PasswordResetToken;

    /**
     * Resets a user's password using a valid reset token.
     *
     * @throws \App\Exceptions\AuthenticationException When the token is invalid or the password update fails
     * @throws \App\Exceptions\ValidationException When the new password fails validation
     */
    public function resetPassword(string $rawToken, string $newPassword, string $confirmPassword): void;

    /**
     * Resolves the correct post-login redirect path for the given role.
     */
    public function resolvePostLoginRedirect(?int $roleId): string;
}
