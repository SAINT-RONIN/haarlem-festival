<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\UserAccount;

/**
 * Interface for UserAccount repository.
 */
interface IUserAccountRepository
{
    /**
     * Finds a user by username OR email (for login).
     *
     * @param string $login Username or email
     * @return UserAccount|null User model or null if not found
     */
    public function findByUsernameOrEmail(string $login): ?UserAccount;

    /**
     * Finds a user by email address.
     *
     * @param string $email Email address
     * @return UserAccount|null User model or null if not found
     */
    public function findByEmail(string $email): ?UserAccount;

    /**
     * Checks if a username already exists.
     *
     * @param string $username Username to check
     * @return bool True if username exists
     */
    public function existsByUsername(string $username): bool;

    /**
     * Checks if an email already exists.
     *
     * @param string $email Email to check
     * @return bool True if email exists
     */
    public function existsByEmail(string $email): bool;

    /**
     * Creates a new user account and returns the generated ID.
     */
    public function createUser(
        string $username,
        string $email,
        string $passwordHash,
        string $firstName,
        string $lastName,
        int $roleId,
    ): int;

    /**
     * Finds a user by ID.
     */
    public function findById(int $id): ?UserAccount;

    /**
     * Updates a user's password hash.
     */
    public function updatePasswordHash(int $userId, string $passwordHash): void;

    /**
     * Does not change password
     */
    public function updateProfileInfo(
        int $userId,
        string $email,
        string $firstName,
        string $lastName,
        ?int $profilePictureAssetId = null,
    ): void;


    /**
     * Checks if email exists for another user (excluding given user ID).
     *
     * @param string $email Email to check
     * @param int $excludeUserId User ID to exclude from check
     * @return bool True if email exists for another user
     */
    public function emailExistsForOtherUser(string $email, int $excludeUserId): bool;

    /**
     * Soft-deletes a user by setting IsActive = 0.
     */
    public function deleteUser(int $id): void;

    /**
     * Reactivates a soft-deleted user by setting IsActive = 1.
     */
    public function reactivateUser(int $id): void;
}
