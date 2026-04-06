<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PassScope;

/**
 * Represents a row in the PassType table.
 *
 * Defines multi-event passes for Jazz (day pass, full festival pass) with scope and pricing.
 */
final readonly class PassType
{
    /*
     * Purpose: Defines available festival pass types (day pass, all-access)
     * with pricing and scope configuration.
     */

    public function __construct(
        public int       $passTypeId,
        public int       $eventTypeId,
        public string    $passName,
        public PassScope $passScope,
        public string    $price,
        public string    $vatRate,
        public string    $currencyCode,
        public bool      $isActive,
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
