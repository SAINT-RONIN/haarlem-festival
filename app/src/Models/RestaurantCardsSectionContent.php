<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the Restaurant restaurant_cards_section.
 */
final readonly class RestaurantCardsSectionContent
{
    public function __construct(
        public ?string $cardsTitle,
        public ?string $cardsSubtitle,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            cardsTitle: $raw['cards_title'] ?? null,
            cardsSubtitle: $raw['cards_subtitle'] ?? null,
        );
    }
}
