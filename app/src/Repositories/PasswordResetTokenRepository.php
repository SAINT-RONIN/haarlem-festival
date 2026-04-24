<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PasswordResetToken;
use App\Repositories\Interfaces\IPasswordResetTokenRepository;

// The Token column stores SHA-256 hashes, never raw tokens.
// Raw tokens are sent in the reset email; only the hash is persisted.
class PasswordResetTokenRepository extends BaseRepository implements IPasswordResetTokenRepository
{
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

    // Not expired (ExpiresAtUtc > NOW) and not used (UsedAtUtc IS NULL).
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

    public function markAsUsed(int $tokenId): void
    {
        $this->execute(
            'UPDATE PasswordResetToken SET UsedAtUtc = NOW() WHERE PasswordResetTokenId = :id',
            ['id' => $tokenId],
        );
    }

    // For cron/maintenance cleanup -- not part of the normal reset flow.
    public function deleteExpiredTokens(): int
    {
        $stmt = $this->execute(
            'DELETE FROM PasswordResetToken WHERE ExpiresAtUtc < NOW()',
            [],
        );

        return $stmt->rowCount();
    }
}
