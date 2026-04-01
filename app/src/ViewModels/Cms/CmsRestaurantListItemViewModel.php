<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsRestaurantListItemViewModel
{
    public function __construct(
        public int    $eventId,
        public string $title,
        public string $slug,
        public bool   $isActive,
    ) {}
}
