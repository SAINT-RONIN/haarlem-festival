<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * DTO for single venue card data.
 */
final readonly class VenueCardData
{
    public function __construct(
        public string $name,
        public string $description,
        public string $imageUrl,
    ) {
    }
}
