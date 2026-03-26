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
 * ViewModel for the Restaurant Detail page (/restaurant/{id}) and the
 * reservation form page (/restaurant/{id}/reservation).
 *
 * Composed of focused section sub-ViewModels, following the same pattern
 * used by JazzArtistDetailPageViewModel and the History/Storytelling pages.
 */
final readonly class RestaurantDetailViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        array $cms,

        public string $slug,
        public string $name,

        public ContactSectionData      $contact,
        public AboutSectionData        $about,
        public ChefSectionData         $chef,
        public MenuSectionData         $menu,
        public LocationSectionData     $location,
        public PracticalInfoSectionData $practicalInfo,
        public GallerySectionData      $gallery,
        public ReservationSectionData  $reservation,
    ) {
        parent::__construct(
            heroData:    $heroData,
            globalUi:    $globalUi,
            currentPage: 'restaurant',
            cms:         $cms,
            includeNav:  false,
        );
    }
}
