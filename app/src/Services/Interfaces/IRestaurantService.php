<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\RestaurantDetailSectionContent;
use App\DTOs\Domain\Pages\RestaurantPageData;
use App\DTOs\Domain\Restaurant\ReservationFormData;
use App\DTOs\Domain\Restaurant\Restaurant;

/**
 * Single interface for all restaurant operations: listing, detail, and reservation.
 */
interface IRestaurantService
{
    /** Loads all CMS sections and restaurant listings for the overview page. */
    public function getRestaurantPageData(): RestaurantPageData;

    /**
     * Loads a single restaurant by its URL slug.
     *
     * @throws \App\Exceptions\RestaurantEventNotFoundException
     */
    public function getRestaurant(string $slug): Restaurant;

    /** Loads the shared detail-page labels (section titles, button text, etc.). */
    public function getDetailLabels(): RestaurantDetailSectionContent;

    /** Loads the global UI content (nav, footer) shared across all pages. */
    public function getGlobalUi(): GlobalUiContent;

    /**
     * Extracts the sorted list of unique cuisine labels from all active restaurants.
     *
     * @return string[] e.g. ['All', 'French', 'Italian', 'Vegan']
     */
    public function getActiveCuisines(): array;

    /**
     * Parses a comma-separated time slots string into an array.
     *
     * @return string[]
     */
    public function parseTimeSlots(?string $raw): array;

    /**
     * Builds price cards from the adult price.
     *
     * @return array{label: string, price: string}[]
     */
    public function buildPriceCards(?string $priceAdultStr): array;

    /**
     * Validates and persists a reservation for the restaurant identified by $slug.
     *
     * @throws \App\Exceptions\RestaurantEventNotFoundException
     * @throws \App\Exceptions\ValidationException
     * @return int The new reservation ID
     */
    public function submitReservation(string $slug, ReservationFormData $formData): int;
}
