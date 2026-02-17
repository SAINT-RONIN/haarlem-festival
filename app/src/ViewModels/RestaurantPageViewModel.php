<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * ViewModel for the Restaurant page.
 *
 * Contains all pre-formatted data needed by the restaurant page view.
 * The service prepares this data so the view only needs to render.
 */
final readonly class RestaurantPageViewModel extends BaseViewModel
{
    public function __construct(
        public HeroData              $heroData,
        GlobalUiData $globalUi,
        public GradientSectionData   $gradientSection,
        public IntroSplitSectionData $introSplitSection,
        public ?IntroSplitSectionData $introSplit2Section = null,
        public ?array                $instructionsSection = null,
        public ?array                $restaurantCardsSection = null,
    ) {
        parent::__construct($globalUi);
    }
}
