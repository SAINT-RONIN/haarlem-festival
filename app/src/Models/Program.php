<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `Program` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class Program
{
    /*
     * Purpose: Represents a user's shopping cart/program containing
     * selected items before checkout.
     */

    public function __construct(
        public readonly int                $programId,
        public readonly ?int               $userAccountId,
        public readonly ?string            $sessionKey,
        public readonly \DateTimeImmutable $createdAtUtc,
        public readonly bool               $isCheckedOut,
    )
    {
    }

    /**
     * Creates a Program instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            programId: (int)$row['ProgramId'],
            userAccountId: isset($row['UserAccountId']) ? (int)$row['UserAccountId'] : null,
            sessionKey: $row['SessionKey'] ?? null,
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc']),
            isCheckedOut: (bool)$row['IsCheckedOut'],
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'ProgramId' => $this->programId,
            'UserAccountId' => $this->userAccountId,
            'SessionKey' => $this->sessionKey,
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
            'IsCheckedOut' => $this->isCheckedOut,
        ];
    }
}
