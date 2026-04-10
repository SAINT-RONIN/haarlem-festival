<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\Restaurant\ReservationFormData;
use App\DTOs\Domain\Restaurant\ReservationSubmissionResult;

interface IRestaurantReservationService
{
    /**
     * Validates the reservation form input and persists the reservation.
     *
     * @throws \App\Exceptions\RestaurantEventNotFoundException if the restaurant event is not found
     * @throws \App\Exceptions\ValidationException if the submitted data fails validation
     */
    public function submitReservation(string $slug, ReservationFormData $formData): ReservationSubmissionResult;
}
