<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IRestaurantReservationService
{
    /**
     * Validates the reservation form input and persists the reservation.
     *
     * @param array<string, mixed> $postData Raw POST data from the reservation form
     * @throws \App\Exceptions\RestaurantEventNotFoundException if the restaurant event is not found
     * @throws \App\Exceptions\ValidationException if the submitted data fails validation
     */
    public function submitReservation(string $slug, array $postData): void;
}
