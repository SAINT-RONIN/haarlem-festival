<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\RestaurantPageConstants;
use App\Exceptions\RestaurantEventNotFoundException;
use App\Exceptions\ValidationException;
use App\Models\Reservation;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\ReservationRepository;
use App\Services\Interfaces\IRestaurantReservationService;

class RestaurantReservationService implements IRestaurantReservationService
{
    public function __construct(
        private readonly IEventRepository      $eventRepository,
        private readonly ReservationRepository $reservationRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $postData
     * @throws RestaurantEventNotFoundException if the event is not found
     * @throws ValidationException if the submitted data fails validation
     */
    public function submitReservation(string $slug, array $postData): void
    {
        $event = $this->eventRepository->findActiveRestaurantBySlug(
            trim(strtolower(rawurldecode($slug)), '-'),
        );

        if ($event === null) {
            throw new RestaurantEventNotFoundException($slug);
        }

        $date            = trim((string) ($postData['dining_date']      ?? ''));
        $timeSlot        = trim((string) ($postData['time_slot']        ?? ''));
        $adultsCount     = max(0, (int) ($postData['adults_count']      ?? 0));
        $childrenCount   = max(0, (int) ($postData['children_count']    ?? 0));
        $specialRequests = trim((string) ($postData['special_requests'] ?? ''));

        $errors = [];

        if (!in_array($date, RestaurantPageConstants::VALID_DATES, true)) {
            $errors[] = 'Please select a valid dining date.';
        }
        if ($timeSlot === '') {
            $errors[] = 'Please select a time slot.';
        }
        if ($adultsCount + $childrenCount < 1) {
            $errors[] = 'Please add at least one guest.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $this->reservationRepository->insert(new Reservation(
            restaurantId:    $event->restaurantId,
            diningDate:      $date,
            timeSlot:        $timeSlot,
            adultsCount:     $adultsCount,
            childrenCount:   $childrenCount,
            specialRequests: $specialRequests,
            totalFee:        ($adultsCount + $childrenCount) * RestaurantPageConstants::RESERVATION_FEE,
        ));
    }
}
