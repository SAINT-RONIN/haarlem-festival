<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Interface for Authentication service.
 */
interface IAuthService
{
    /**
     * Attempts to authenticate a user with username/email and password.
     *
     * @param string $login Username or email
     * @param string $password Plain text password
     * @return array{success: bool, user?: \App\Models\UserAccount, error?: string}
     */
    public function attemptLogin(string $login, string $password): array;

    /**
     * Validates registration data and returns any errors.
     *
     * @param array $data Registration data
     * @return array<string, string> Field name => error message
     */
    public function validateRegistration(array $data): array;

    /**
     * Registers a new user account.
     *
     * @param array $data User data
     * @return int The new user's ID
     */
    public function register(array $data): int;

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
     * @param string $rawToken The raw token from the URL
     * @return array{valid: bool, tokenId?: int, userId?: int, error?: string}
     */
    public function validateResetToken(string $rawToken): array;

    /**
     * Resets a user's password using a valid reset token.
     *
     * @param string $rawToken Reset token
     * @param string $newPassword New password
     * @param string $confirmPassword Password confirmation
     * @return array{success: bool, error?: string}
     */
    public function resetPassword(string $rawToken, string $newPassword, string $confirmPassword): array;
}

