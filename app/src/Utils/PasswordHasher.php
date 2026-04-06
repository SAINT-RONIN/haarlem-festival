<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Password hashing utility.
 *
 * Provides secure password hashing and verification using Argon2id.
 * Services decide WHEN to hash, this utility only performs the hash/verify.
 *
 * Uses PHP's built-in password_hash with Argon2id algorithm for:
 * - Memory-hard defense against GPU attacks
 * - Time-hard defense against brute force
 * - Built-in salt generation
 */
class PasswordHasher
{
    /**
     * Default options for Argon2id.
     * These can be adjusted based on server capabilities.
     */
    private const DEFAULT_OPTIONS = [
        'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
        'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
        'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
    ];

    /**
     * Hashes a plain-text password using Argon2id.
     *
     * @param string $password Plain-text password
     * @param array $options Optional Argon2id options
     * @return string Hashed password
     */
    public static function hash(string $password, array $options = []): string
    {
        $mergedOptions = array_merge(self::DEFAULT_OPTIONS, $options);
        return password_hash($password, PASSWORD_ARGON2ID, $mergedOptions);
    }

    /**
     * Verifies a password against a hash.
     *
     * @param string $password Plain-text password to verify
     * @param string $hash Stored password hash
     * @return bool True if password matches
     */
    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

}
