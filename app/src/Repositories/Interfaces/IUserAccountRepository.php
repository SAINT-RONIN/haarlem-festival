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
     * Creates a new user account.
     *
     * @param array $data User data
     * @return int The new user's ID
     */
    public function create(array $data): int;

    /**
     * Updates a user's password hash.
     *
     * @param int $userId User ID
     * @param string $passwordHash New password hash
     */
    public function updatePasswordHash(int $userId, string $passwordHash): void;
}
