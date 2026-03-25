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
}
