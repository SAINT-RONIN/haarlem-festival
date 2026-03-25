<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the HistoricalLocation intro_section.
 */
final readonly class HistoricalLocationIntroContent
{
    public function __construct(
        public ?string $introHeading,
        public ?string $introText,
        public ?string $introFact,
        public ?string $introImage,
    ) {}
}
