<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * Represents a single "Read more about these locations" card.
 */
final readonly class VenueCardData
{
    public function __construct(
        public string $name,
        public string $description,
        public string $imageUrl,
        public string $venueUrl,
    ) {
    }
}
