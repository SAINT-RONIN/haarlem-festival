<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the EmailConfirmationToken table.
 *
 * Used for email verification during the registration flow.
 */
final readonly class EmailConfirmationToken
{
    /*
     * Purpose: Stores tokens for email verification during user registration,
     * tracking expiration and usage status.
     */

    public function __construct(
        public int                 $emailConfirmationTokenId,
        public int                 $userAccountId,
        public string              $token,
        public \DateTimeImmutable  $expiresAtUtc,
        public ?\DateTimeImmutable $usedAtUtc,
    ) {
    }

    /**
     * Creates an EmailConfirmationToken instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            emailConfirmationTokenId: (int)$row['EmailConfirmationTokenId'],
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
            'EmailConfirmationTokenId' => $this->emailConfirmationTokenId,
            'UserAccountId' => $this->userAccountId,
            'Token' => $this->token,
            'ExpiresAtUtc' => $this->expiresAtUtc->format('Y-m-d H:i:s'),
            'UsedAtUtc' => $this->usedAtUtc?->format('Y-m-d H:i:s'),
        ];
    }
}
