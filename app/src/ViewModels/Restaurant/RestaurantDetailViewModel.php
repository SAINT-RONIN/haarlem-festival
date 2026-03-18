<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant;

use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;

/**
 * ViewModel for the Restaurant Detail page (/restaurant/{id}).
 *
 * Contains TWO kinds of data:
 * 1. Domain data (from Restaurant table): name, address, about text, chef, images, etc.
 * 2. CMS labels (from CmsItem table): section titles, labels, button texts
 *    → admin can edit these via the CMS dashboard.
 */
final readonly class RestaurantDetailViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        array $cms,

        // Basic restaurant info (domain)
        public int    $id,
        public string $name,
        public string $cuisine,
        public string $address,
        public string $description,
        public int    $rating,
        public string $image,

        // Contact info (domain)
        public string $phone,
        public string $email,
        public string $website,

        // About section (domain)
        public string $aboutText,
        public string $aboutImage,

        // Chef section (domain)
        public string $chefName,
        public string $chefText,
        public string $chefImage,

        // Menu section (domain)
        public string $menuDescription,
        /** @var string[] cuisine type tags */
        public array  $cuisineTags,
        /** @var string[] menu image paths */
        public array  $menuImages,

        // Location section (domain)
        public string $locationDescription,
        public string $mapEmbedUrl,

        // Practical info (domain)
        public int    $michelinStars,
        public int    $seatsPerSession,
        public int    $durationMinutes,
        public string $specialRequestsNote,

        // Gallery images (domain)
        /** @var string[] gallery image paths */
        public array  $galleryImages,

        // Reservation section (domain + hardcoded for now)
        public string $reservationImage,
        /** @var string[] available time slots */
        public array  $timeSlots,
        /** @var array{label: string, price: string}[] price cards */
        public array  $priceCards,

        // ── CMS labels (admin-editable section titles & labels) ──
        public string $labelContactTitle       = '',
        public string $labelAddress             = '',
        public string $labelContact             = '',
        public string $labelOpenHours           = '',
        public string $labelPracticalTitle      = '',
        public string $labelPriceFood           = '',
        public string $labelRating              = '',
        public string $labelSpecialRequests     = '',
        public string $labelGalleryTitle        = '',
        public string $labelAboutPrefix         = '',
        public string $labelChefTitle           = '',
        public string $labelMenuTitle           = '',
        public string $labelCuisineType         = '',
        public string $labelLocationTitle       = '',
        public string $labelLocationAddress     = '',
        public string $labelReservationTitle    = '',
        public string $labelReservationDesc     = '',
        public string $labelSlotsLabel          = '',
        public string $labelReservationNote     = '',
        public string $labelReservationBtn      = '',
        public string $labelDuration            = '',
        public string $labelSeats               = '',
        public string $labelFestivalRated       = '',
        public string $labelMichelin            = '',
        public string $labelMapFallback         = '',
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: 'restaurant',
            cms: $cms,
            includeNav: false,
        );
    }
}
