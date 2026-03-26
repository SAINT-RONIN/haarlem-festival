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

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            instructionsTitle: $raw['instructions_title'] ?? null,
            instructionsCard1Title: $raw['instructions_card_1_title'] ?? null,
            instructionsCard1Text: $raw['instructions_card_1_text'] ?? null,
            instructionsCard2Title: $raw['instructions_card_2_title'] ?? null,
            instructionsCard2Text: $raw['instructions_card_2_text'] ?? null,
            instructionsCard3Title: $raw['instructions_card_3_title'] ?? null,
            instructionsCard3Text: $raw['instructions_card_3_text'] ?? null,
        );
    }
}
