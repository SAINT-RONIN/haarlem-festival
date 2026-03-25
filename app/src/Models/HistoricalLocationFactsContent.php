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
}
