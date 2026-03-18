<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

final readonly class StorytellingDetailHeroData
{
    /**
     * @param string[] $labels
     * @param StorytellingDetailNavLinkData[] $navLinks
     */
    public function __construct(
        public string $title,
        public string $subtitle,
        public string $heroImageUrl,
        public array $labels,
        public array $navLinks,
        public string $backButtonLabel,
        public string $backButtonUrl,
        public string $reserveButtonLabel,
        public string $reserveButtonUrl,
    ) {
    }
}
