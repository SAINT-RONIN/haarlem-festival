<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Helpers\ImageHelper;
use App\ViewModels\GlobalUiData;

final readonly class StorytellingDetailHeroData
{
    private const DEFAULT_BACK_BUTTON_LABEL = 'Back to storytelling';
    private const DEFAULT_RESERVE_BUTTON_LABEL = 'Reserve your spot';

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

    /**
     * @param string[] $labels
     */
    public static function fromData(
        string $title,
        string $subtitle,
        ?string $featuredImagePath,
        array $labels,
        array $cms,
        GlobalUiData $globalUi,
        string $currentPage,
        string $reserveButtonUrl,
    ): self {
        return new self(
            title: $title,
            subtitle: $subtitle,
            heroImageUrl: ImageHelper::validatePath($featuredImagePath ?? ''),
            labels: $labels,
            navLinks: StorytellingDetailNavLinkData::buildDefaultLinks($globalUi, $currentPage),
            backButtonLabel: ImageHelper::getStringValue($cms, 'back_button_label', self::DEFAULT_BACK_BUTTON_LABEL),
            backButtonUrl: '/storytelling',
            reserveButtonLabel: ImageHelper::getStringValue($cms, 'reserve_button_label', self::DEFAULT_RESERVE_BUTTON_LABEL),
            reserveButtonUrl: $reserveButtonUrl,
        );
    }
}
