<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the Restaurant instructions_section.
 */
final readonly class RestaurantInstructionsSectionContent
{
    public function __construct(
        public ?string $instructionsTitle,
        public ?string $instructionsCard1Title,
        public ?string $instructionsCard1Text,
        public ?string $instructionsCard2Title,
        public ?string $instructionsCard2Text,
        public ?string $instructionsCard3Title,
        public ?string $instructionsCard3Text,
    ) {}
}
