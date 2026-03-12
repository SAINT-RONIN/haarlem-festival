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
 * ViewModel for the Restaurant Detail page (/restaurant/{id}).
 *
 * Follows the same pattern as HistoryPageViewModel:
 * the main ViewModel holds small section ViewModels as properties,
 * each one grouping related data for one section of the page.
 *
 * Data sources:
 *  - Domain data (Restaurant table): name, address, images, etc.
 *  - CMS labels (CmsItem table): section titles that admin can edit.
 */
final readonly class RestaurantDetailViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,

        // Restaurant identity (used across sections for alt texts, page title, etc.)
        public int    $id,
        public string $name,

        // Section-specific ViewModels (like History uses RouteData, VenuesData, etc.)
        public ContactSectionData       $contactSection,
        public PracticalInfoSectionData  $practicalInfoSection,
        public GallerySectionData        $gallerySection,
        public AboutSectionData          $aboutSection,
        public ChefSectionData           $chefSection,
        public MenuSectionData           $menuSection,
        public LocationSectionData       $locationSection,
        public ReservationSectionData    $reservationSection,
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: 'restaurant',
            includeNav: false,
        );
    }
}
