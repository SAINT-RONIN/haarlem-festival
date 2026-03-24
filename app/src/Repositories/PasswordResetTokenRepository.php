<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\PasswordResetToken;
use App\Repositories\Interfaces\IPasswordResetTokenRepository;
use PDO;

/**
 * Repository for PasswordResetToken database operations.
 *
 * Handles creation, lookup, and status updates for password reset tokens.
 * Note: The Token column stores SHA-256 hashes, never raw tokens.
 */
class PasswordResetTokenRepository implements IPasswordResetTokenRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

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
        $sql = '
            INSERT INTO PasswordResetToken 
            (UserAccountId, Token, ExpiresAtUtc, UsedAtUtc)
            VALUES 
            (:userId, :token, :expiresAt, NULL)
        ';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'userId' => $userId,
            'token' => $tokenHash,
            'expiresAt' => $expiresAt->format('Y-m-d H:i:s'),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Finds a valid (not expired, not used) token by its hash.
     *
     * @param string $tokenHash SHA-256 hash of the token from the URL
     * @return PasswordResetToken|null Token or null if invalid
     */
    public function findValidByTokenHash(string $tokenHash): ?PasswordResetToken
    {
        $sql = '
            SELECT * FROM PasswordResetToken 
            WHERE Token = :token 
            AND ExpiresAtUtc > NOW() 
            AND UsedAtUtc IS NULL
        ';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => $tokenHash]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? PasswordResetToken::fromRow($row) : null;
    }

    /**
     * Marks a token as used (prevents reuse).
     */
    public function markAsUsed(int $tokenId): void
    {
        $sql = 'UPDATE PasswordResetToken SET UsedAtUtc = NOW() WHERE PasswordResetTokenId = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $tokenId]);
    }

    /**
     * Deletes expired tokens (cleanup utility). Not part of the interface contract
     * because it is only called by maintenance/cron tasks, not by the reset flow.
     *
     * @return int Number of deleted rows.
     */
    public function deleteExpiredTokens(): int
    {
        $sql = 'DELETE FROM PasswordResetToken WHERE ExpiresAtUtc < NOW()';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
