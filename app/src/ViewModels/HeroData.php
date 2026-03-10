<?php

declare(strict_types=1);

namespace App\ViewModels;

final readonly class HeroData
{
    private const DEFAULT_TITLE = 'Welcome';
    private const DEFAULT_SUBTITLE = '';
    private const DEFAULT_BUTTON_PRIMARY = 'Explore';
    private const DEFAULT_BUTTON_SECONDARY = 'Learn More';
    private const DEFAULT_LINK_PRIMARY = '#';
    private const DEFAULT_LINK_SECONDARY = '#';
    private const DEFAULT_IMAGE = '/assets/Image/HeroImageHome.png';

    public function __construct(
        public string $mainTitle,
        public string $subtitle,
        public string $primaryButtonText,
        public string $primaryButtonLink,
        public string $secondaryButtonText,
        public string $secondaryButtonLink,
        public string $backgroundImageUrl,
        public string $currentPage,
    ) {
    }

    public static function fromCms(array $content, string $currentPage): self
    {
        return new self(
            mainTitle: $content['hero_main_title'] ?? self::DEFAULT_TITLE,
            subtitle: $content['hero_subtitle'] ?? self::DEFAULT_SUBTITLE,
            primaryButtonText: $content['hero_button_primary'] ?? self::DEFAULT_BUTTON_PRIMARY,
            primaryButtonLink: $content['hero_button_primary_link'] ?? self::DEFAULT_LINK_PRIMARY,
            secondaryButtonText: $content['hero_button_secondary'] ?? self::DEFAULT_BUTTON_SECONDARY,
            secondaryButtonLink: $content['hero_button_secondary_link'] ?? self::DEFAULT_LINK_SECONDARY,
            backgroundImageUrl: $content['hero_background_image'] ?? self::DEFAULT_IMAGE,
            currentPage: $currentPage,
        );
    }
}
