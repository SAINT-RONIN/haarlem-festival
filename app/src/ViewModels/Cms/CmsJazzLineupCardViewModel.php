<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsJazzLineupCardViewModel
{
    public function __construct(
        public int $artistId,
        public string $name,
        public string $style,
        public string $description,
        public string $imageUrl,
        public string $performanceSummary,
        public int $sortOrder,
        public ?string $profileUrl,
        public string $editUrl,
        public string $removeAction,
    ) {}
}
