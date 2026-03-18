<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

/**
 * Carries display-ready data for the custom hero overlay on a Storytelling detail page.
 * The reason for this is because the detail hero includes inline navigation, session labels, and action buttons that do not exist on the standard HeroData, so it needs its own typed container.
 */
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
