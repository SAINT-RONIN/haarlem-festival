<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PasswordResetToken;
use App\Repositories\Interfaces\IPasswordResetTokenRepository;

/**
 * Repository for PasswordResetToken database operations.
 *
 * Handles creation, lookup, and status updates for password reset tokens.
 * Note: The Token column stores SHA-256 hashes, never raw tokens.
 */
class PasswordResetTokenRepository extends BaseRepository implements IPasswordResetTokenRepository
{
    /**
     * Creates a new password reset token.
     *
     * @param int $userId The user requesting the reset
     * @param string $tokenHash SHA-256 hash of the raw token (raw token goes in email)
     * @param \DateTimeImmutable $expiresAt When the token expires
     * @return int The new token record ID
     */
    public function create(int $userId, string $tokenHash, \DateTimeImmutable $expiresAt): int
    {
        return $this->executeInsert(
            'INSERT INTO PasswordResetToken
            (UserAccountId, Token, ExpiresAtUtc, UsedAtUtc)
            VALUES
            (:userId, :token, :expiresAt, NULL)',
            [
                'userId' => $userId,
                'token' => $tokenHash,
                'expiresAt' => $expiresAt->format('Y-m-d H:i:s'),
            ],
        );
    }

    /**
     * Finds a valid (not expired, not used) token by its hash.
     *
     * @param string $tokenHash SHA-256 hash of the token from the URL
     * @return PasswordResetToken|null Token or null if invalid
     */
    public function findValidByTokenHash(string $tokenHash): ?PasswordResetToken
    {
        return $this->fetchOne(
            'SELECT * FROM PasswordResetToken
            WHERE Token = :token
            AND ExpiresAtUtc > NOW()
            AND UsedAtUtc IS NULL',
            ['token' => $tokenHash],
            fn(array $row) => PasswordResetToken::fromRow($row),
        );
    }

    /**
     * Marks a token as used (prevents reuse).
     */
    public function markAsUsed(int $tokenId): void
    {
        $this->execute(
            'UPDATE PasswordResetToken SET UsedAtUtc = NOW() WHERE PasswordResetTokenId = :id',
            ['id' => $tokenId],
        );
    }

    /**
     * Deletes expired tokens (cleanup utility). Not part of the interface contract
     * because it is only called by maintenance/cron tasks, not by the reset flow.
     *
     * @return int Number of deleted rows.
     */
    public function deleteExpiredTokens(): int
    {
        $stmt = $this->execute(
            'DELETE FROM PasswordResetToken WHERE ExpiresAtUtc < NOW()',
            [],
        );

        return $stmt->rowCount();
    }
}
