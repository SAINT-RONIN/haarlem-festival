<?php

declare(strict_types=1);

namespace App\ViewModels\Dance;

/**
 * Data for a single headliner artist card on the Dance page.
 */
final readonly class HeadlinerCardData
{
    public function __construct(
        public string $name,
        public string $genre,
        public string $imageUrl,
        public string $profileUrl,
    ) {}
}
