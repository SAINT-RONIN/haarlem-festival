<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\PasswordResetToken;

/**
 * Contract for managing password reset tokens.
 * Tokens are stored as SHA-256 hashes; the raw token is only sent via email.
 */
interface IPasswordResetTokenRepository
{
    /**
     * Creates a new password reset token.
     *
     * @param int $userId The user requesting the reset
     * @param string $tokenHash SHA-256 hash of the raw token
     * @param \DateTimeImmutable $expiresAt When the token expires
     * @return int The new token record ID
     */
    public function create(int $userId, string $tokenHash, \DateTimeImmutable $expiresAt): int;

    /**
     * Finds a valid (not expired, not used) token by its hash.
     *
     * @param string $tokenHash SHA-256 hash of the raw token
     * @return PasswordResetToken|null Token or null if not found/invalid
     */
    public function findValidByTokenHash(string $tokenHash): ?PasswordResetToken;

    /**
     * Marks a token as used.
     *
     * @param int $tokenId The token record ID
     */
    public function markAsUsed(int $tokenId): void;
}
