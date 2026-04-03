<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant\Detail;

/**
 * ViewModel for the Practical Info card on the restaurant detail page.
 *
 * Groups pricing, rating, Michelin stars, and special requests.
 * Seats and duration belong to ReservationSectionData (the only section that renders them).
 */
final readonly class PracticalInfoSectionData
{
    /**
     * @param array{label: string, price: string}[] $priceCards
     */
    public function __construct(
        public string $cuisine,
        public int    $rating,
        public int    $michelinStars,
        public string $specialRequestsNote,
        public array  $priceCards,

        // Pre-formatted display strings (computed by RestaurantMapper)
        public string $ratingStars     = '',   // e.g. "★★★"
        public string $michelinDisplay = '',   // e.g. "2 Michelin-stars" or ""

        // CMS labels
        public string $labelTitle           = 'Practical Info',
        public string $labelPriceFood       = 'PRICE AND FOOD',
        public string $labelRating          = 'RESTAURANT RATING',
        public string $labelSpecialRequests = 'SPECIAL REQUESTS',
        public string $labelFestivalRated   = 'Festival-rated',
        public string $labelMichelin        = 'Michelin-star',
        public string $labelCuisineType     = 'Cuisine type:',
    ) {
    }
}
