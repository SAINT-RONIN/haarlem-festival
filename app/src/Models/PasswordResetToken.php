<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `PasswordResetToken` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class PasswordResetToken
{
    /*
     * Purpose: Stores tokens for password reset requests,
     * tracking expiration and usage status.
     */

    public function __construct(
        public readonly int                 $passwordResetTokenId,
        public readonly int                 $userAccountId,
        public readonly string              $token,
        public readonly \DateTimeImmutable  $expiresAtUtc,
        public readonly ?\DateTimeImmutable $usedAtUtc,
    )
    {
    }

    /**
     * Creates a PasswordResetToken instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            passwordResetTokenId: (int)$row['PasswordResetTokenId'],
            userAccountId: (int)$row['UserAccountId'],
            token: (string)$row['Token'],
            expiresAtUtc: new \DateTimeImmutable($row['ExpiresAtUtc']),
            usedAtUtc: isset($row['UsedAtUtc']) ? new \DateTimeImmutable($row['UsedAtUtc']) : null,
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'PasswordResetTokenId' => $this->passwordResetTokenId,
            'UserAccountId' => $this->userAccountId,
            'Token' => $this->token,
            'ExpiresAtUtc' => $this->expiresAtUtc->format('Y-m-d H:i:s'),
            'UsedAtUtc' => $this->usedAtUtc?->format('Y-m-d H:i:s'),
        ];
    }
}
