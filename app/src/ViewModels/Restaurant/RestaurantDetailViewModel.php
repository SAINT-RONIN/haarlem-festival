<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant;

use App\Models\Restaurant;
use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;

/**
 * ViewModel for the Restaurant Detail page (/restaurant/{slug}) and reservation form.
 */
final readonly class RestaurantDetailViewModel extends BaseViewModel
{
    /**
     * @param string[] $timeSlots Parsed time slot strings
     * @param array{label: string, price: string}[] $priceCards Derived price info cards
     * @param string[] $validDates Festival dates for reservation date picker
     * @param string[] $menuImages Validated menu image paths
     * @param string[] $galleryImages Validated gallery image paths
     * @param array<string, string> $labels CMS labels for section titles, buttons, etc.
     */
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        public Restaurant $restaurant,
        public array $labels,
        public array $timeSlots,
        public array $priceCards,
        public array $validDates,
        public array $menuImages,
        public array $galleryImages,
        public string $address,
        public string $reservationImage,
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: 'restaurant',
            includeNav: false,
        );
    }
}
