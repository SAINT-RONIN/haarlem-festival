<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the HistoricalLocation facts_section.
 */
final readonly class HistoricalLocationFactsContent
{
    public function __construct(
        public ?string $factsHeading,
        public ?string $fact1,
        public ?string $fact2,
        public ?string $fact3,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            factsHeading: $raw['facts_heading'] ?? null,
            fact1: $raw['fact1'] ?? null,
            fact2: $raw['fact2'] ?? null,
            fact3: $raw['fact3'] ?? null,
        );
    }
}
