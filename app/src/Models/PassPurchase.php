<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the PassPurchase table.
 *
 * Records the purchase of a festival pass, linking a pass type to a user with validity dates.
 */
final readonly class PassPurchase
{
    /*
     * Purpose: Stores completed pass purchases created during checkout,
     * referenced by OrderItem via PassPurchaseId.
     */

    public function __construct(
        public int                 $passPurchaseId,
        public int                 $passTypeId,
        public int                 $userAccountId,
        public ?\DateTimeImmutable $validDate,
        public ?\DateTimeImmutable $validFromDate,
        public ?\DateTimeImmutable $validToDate,
        public \DateTimeImmutable  $createdAtUtc,
    ) {}

    /**
     * Creates a PassPurchase instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            passPurchaseId: (int) $row['PassPurchaseId'],
            passTypeId: (int) $row['PassTypeId'],
            userAccountId: (int) $row['UserAccountId'],
            validDate: isset($row['ValidDate']) ? new \DateTimeImmutable($row['ValidDate']) : null,
            validFromDate: isset($row['ValidFromDate']) ? new \DateTimeImmutable($row['ValidFromDate']) : null,
            validToDate: isset($row['ValidToDate']) ? new \DateTimeImmutable($row['ValidToDate']) : null,
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc']),
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'PassPurchaseId' => $this->passPurchaseId,
            'PassTypeId' => $this->passTypeId,
            'UserAccountId' => $this->userAccountId,
            'ValidDate' => $this->validDate?->format('Y-m-d'),
            'ValidFromDate' => $this->validFromDate?->format('Y-m-d'),
            'ValidToDate' => $this->validToDate?->format('Y-m-d'),
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
        ];
    }
}
