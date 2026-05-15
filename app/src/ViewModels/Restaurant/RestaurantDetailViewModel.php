<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant;

use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;
use App\ViewModels\Restaurant\Detail\AboutSectionData;
use App\ViewModels\Restaurant\Detail\ChefSectionData;
use App\ViewModels\Restaurant\Detail\ContactSectionData;
use App\ViewModels\Restaurant\Detail\GallerySectionData;
use App\ViewModels\Restaurant\Detail\LocationSectionData;
use App\ViewModels\Restaurant\Detail\MenuSectionData;
use App\ViewModels\Restaurant\Detail\PracticalInfoSectionData;
use App\ViewModels\Restaurant\Detail\ReservationSectionData;

/**
 * ViewModel for the Restaurant Detail page (/restaurant/{slug}).
 */
final readonly class RestaurantDetailViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        public string $slug,
        public string $name,
        public ?ContactSectionData $contactSection = null,
        public ?AboutSectionData $aboutSection = null,
        public ?ChefSectionData $chefSection = null,
        public ?MenuSectionData $menuSection = null,
        public ?LocationSectionData $locationSection = null,
        public ?PracticalInfoSectionData $practicalInfoSection = null,
        public ?GallerySectionData $gallerySection = null,
        public ?ReservationSectionData $reservationSection = null,
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: 'restaurant',
            includeNav: false,
        );
    }
}
