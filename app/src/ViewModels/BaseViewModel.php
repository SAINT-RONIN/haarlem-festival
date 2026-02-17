<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\ViewModels\Age\AgeLabelFormatter;

abstract readonly class BaseViewModel
{
    /**
     * @var array{hero_section: array<string, string>, global_ui: array<string, string|bool>}
     */
    public array $cms;

    public function __construct(
        public HeroData $heroData,
        public GlobalUiData $globalUi,
        public string $currentPage,
        public bool $includeNav = false
    ) {
        $this->cms = self::buildCms($heroData, $globalUi);
    }

    public function formatAgeLabel(?int $minAge, ?int $maxAge): ?string
    {
        return AgeLabelFormatter::format($minAge, $maxAge);
    }

    public function formatAgeRequirement(?int $minAge, ?int $maxAge): string
    {
        return AgeLabelFormatter::formatRequirement($minAge, $maxAge);
    }

    /**
     * @param array<int, string> $labels
     * @return array<int, string>
     */
    public function withAgeLabel(array $labels, ?int $minAge, ?int $maxAge): array
    {
        return AgeLabelFormatter::appendToLabels($labels, $minAge, $maxAge);
    }

    /**
     * @return array{
     *   heroData: HeroData,
     *   globalUi: GlobalUiData,
     *   cms: array{hero_section: array<string, string>, global_ui: array<string, string|bool>},
     *   currentPage: string,
     *   includeNav: bool
     * }
     */
    public function getGlobalData(): array
    {
        return [
            'heroData' => $this->heroData,
            'globalUi' => $this->globalUi,
            'cms' => $this->cms,
            'currentPage' => $this->currentPage,
            'includeNav' => $this->includeNav,
        ];
    }

    /**
     * @return array{hero_section: array<string, string>, global_ui: array<string, string|bool>}
     */
    private static function buildCms(HeroData $heroData, GlobalUiData $globalUi): array
    {
        return [
            'hero_section' => [
                'hero_main_title' => $heroData->mainTitle,
                'hero_subtitle' => $heroData->subtitle,
                'hero_button_primary' => $heroData->primaryButtonText,
                'hero_button_primary_link' => $heroData->primaryButtonLink,
                'hero_button_secondary' => $heroData->secondaryButtonText,
                'hero_button_secondary_link' => $heroData->secondaryButtonLink,
                'hero_background_image' => $heroData->backgroundImageUrl,
            ],
            'global_ui' => [
                'site_name' => $globalUi->siteName,
                'nav_home' => $globalUi->navHome,
                'nav_jazz' => $globalUi->navJazz,
                'nav_dance' => $globalUi->navDance,
                'nav_history' => $globalUi->navHistory,
                'nav_restaurant' => $globalUi->navRestaurant,
                'nav_storytelling' => $globalUi->navStorytelling,
                'btn_my_program' => $globalUi->btnMyProgram,
                'is_logged_in' => $globalUi->isLoggedIn,
            ],
        ];
    }
}
