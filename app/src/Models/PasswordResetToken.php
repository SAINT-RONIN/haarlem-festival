<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the PasswordResetToken table.
 *
 * Stores hashed tokens with expiry timestamps for the forgot-password flow.
 */
final readonly class PasswordResetToken
{
    /*
     * Purpose: Stores tokens for password reset requests,
     * tracking expiration and usage status.
     */

    public function __construct(
        public int                 $passwordResetTokenId,
        public int                 $userAccountId,
        public string              $token,
        public \DateTimeImmutable  $expiresAtUtc,
        public ?\DateTimeImmutable $usedAtUtc,
    ) {}

    /**
     * Creates a PasswordResetToken instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            passwordResetTokenId: (int) $row['PasswordResetTokenId'],
            userAccountId: (int) $row['UserAccountId'],
            token: (string) $row['Token'],
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
