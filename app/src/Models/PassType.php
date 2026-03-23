<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PassScope;

/**
 * Represents a single row from the `PassType` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class PassType
{
    /*
     * Purpose: Defines available festival pass types (day pass, all-access)
     * with pricing and scope configuration.
     */

    public function __construct(
        public readonly int       $passTypeId,
        public readonly int       $eventTypeId,
        public readonly string    $passName,
        public readonly PassScope $passScope,
        public readonly string    $price,
        public readonly string    $vatRate,
        public readonly string    $currencyCode,
        public readonly bool      $isActive,
    ) {
    }

    /**
     * Creates a PassType instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            passTypeId: (int)$row['PassTypeId'],
            eventTypeId: (int)$row['EventTypeId'],
            passName: (string)$row['PassName'],
            passScope: PassScope::from($row['PassScope']),
            price: (string)$row['Price'],
            vatRate: (string)$row['VatRate'],
            currencyCode: (string)$row['CurrencyCode'],
            isActive: (bool)$row['IsActive'],
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'PassTypeId' => $this->passTypeId,
            'EventTypeId' => $this->eventTypeId,
            'PassName' => $this->passName,
            'PassScope' => $this->passScope->value,
            'Price' => $this->price,
            'VatRate' => $this->vatRate,
            'CurrencyCode' => $this->currencyCode,
            'IsActive' => $this->isActive,
        ];
    }
}
