<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `SessionDiscountRule` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class SessionDiscountRule
{
    /*
     * Purpose: Defines discount rules for event sessions,
     * optionally targeting specific price tiers.
     */

    public function __construct(
        public int $sessionDiscountRuleId,
        public int $eventSessionId,
        public string $ruleName,
        public string $discountPercent,
        public ?int $appliesToPriceTierId,
    ) {
    }

    /**
     * Creates a SessionDiscountRule instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            sessionDiscountRuleId: (int) $row['SessionDiscountRuleId'],
            eventSessionId: (int) $row['EventSessionId'],
            ruleName: (string) $row['RuleName'],
            discountPercent: (string) $row['DiscountPercent'],
            appliesToPriceTierId: isset($row['AppliesToPriceTierId']) ? (int) $row['AppliesToPriceTierId'] : null,
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'SessionDiscountRuleId' => $this->sessionDiscountRuleId,
            'EventSessionId' => $this->eventSessionId,
            'RuleName' => $this->ruleName,
            'DiscountPercent' => $this->discountPercent,
            'AppliesToPriceTierId' => $this->appliesToPriceTierId,
        ];
    }
}

