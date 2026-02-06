<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `EventSessionPrice` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class EventSessionPrice
{
    /*
     * Purpose: Links a price tier to an event session with specific
     * price, currency, and VAT rate for ticket sales.
     */

    public function __construct(
        public int $eventSessionPriceId,
        public int $eventSessionId,
        public int $priceTierId,
        public string $price,
        public string $currencyCode,
        public string $vatRate,
    ) {
    }

    /**
     * Creates an EventSessionPrice instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventSessionPriceId: (int) $row['EventSessionPriceId'],
            eventSessionId: (int) $row['EventSessionId'],
            priceTierId: (int) $row['PriceTierId'],
            price: (string) $row['Price'],
            currencyCode: (string) $row['CurrencyCode'],
            vatRate: (string) $row['VatRate'],
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'EventSessionPriceId' => $this->eventSessionPriceId,
            'EventSessionId' => $this->eventSessionId,
            'PriceTierId' => $this->priceTierId,
            'Price' => $this->price,
            'CurrencyCode' => $this->currencyCode,
            'VatRate' => $this->vatRate,
        ];
    }
}

