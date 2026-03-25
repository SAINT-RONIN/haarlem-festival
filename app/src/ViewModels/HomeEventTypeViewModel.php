<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * A single event type card on the homepage — name, description, image, and detail page link.
 */
final readonly class HomeEventTypeViewModel
{
    public function __construct(
        public string $slug,
        public string $title,
        public string $description,
        public string $button,
        public ?string $image,
        public bool $darkBg,
        public string $badgeClass,
        public string $imageSrc = '',
        public string $imageAlt = '',
    ) {
    }
}
