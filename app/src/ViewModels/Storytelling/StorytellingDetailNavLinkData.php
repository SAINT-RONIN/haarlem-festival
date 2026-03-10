<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\ViewModels\GlobalUiData;

final readonly class StorytellingDetailNavLinkData
{
    public function __construct(
        public string $href,
        public string $label,
        public bool $isActive,
    ) {
    }

    /**
     * @return self[]
     */
    public static function buildDefaultLinks(GlobalUiData $globalUi, string $currentPage): array
    {
        return [
            new self('/', $globalUi->navHome, $currentPage === 'home'),
            new self('/jazz', $globalUi->navJazz, $currentPage === 'jazz'),
            new self('/dance', $globalUi->navDance, $currentPage === 'dance'),
            new self('/history', $globalUi->navHistory, $currentPage === 'history'),
            new self('/restaurant', $globalUi->navRestaurant, $currentPage === 'restaurant'),
            new self('/storytelling', $globalUi->navStorytelling, $currentPage === 'storytelling'),
        ];
    }
}
