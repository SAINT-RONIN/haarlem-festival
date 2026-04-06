<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant;

/**
 * ViewModel for the restaurant cards grid section.
 */
final readonly class RestaurantCardsSectionData
{
    /**
     * @param string $title Section heading
     * @param string $subtitle Section description
     * @param string[] $filters Cuisine filter labels (first is always "All")
     * @param RestaurantCardData[] $cards Restaurant cards from domain
     * @param string $labelFilters Label for the filter bar heading
     * @param string $labelAboutBtn Label for the "About" card button
     * @param string $labelBookBtn Label for the "Book table" card button
     */
    public function __construct(
        public string $title,
        public string $subtitle,
        public array  $filters,
        public array  $cards,
        public string $labelFilters  = 'Filters',
        public string $labelAboutBtn = 'About it',
        public string $labelBookBtn  = 'Book table',
        public string $activeFilter  = '',
    ) {
    }
}
