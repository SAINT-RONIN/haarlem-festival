<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Data for a single event type card on the homepage -- name, description, image, and link.
 */
final readonly class HomeEventTypeData
{
    public function __construct(
        public string $slug,
        public string $title,
        public string $description,
        public string $button,
        public ?string $image,
        public bool $darkBg,
        public string $badgeClass,
    ) {}
}
