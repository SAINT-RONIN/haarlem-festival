<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\RestaurantPageConstants;
use App\DTOs\Domain\Events\RestaurantDetailEvent;
use App\DTOs\Domain\Restaurant\ReservationFormData;
use App\DTOs\Domain\Restaurant\ReservationSubmissionResult;
use App\Exceptions\RestaurantEventNotFoundException;
use App\Exceptions\ValidationException;
use App\Models\Reservation;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IReservationRepository;
use App\Services\Interfaces\IRestaurantReservationService;

class RestaurantReservationService implements IRestaurantReservationService
{
    public function __construct(
        private readonly IEventRepository $eventRepository,
        private readonly IReservationRepository $reservationRepository,
    ) {}

    /** @throws RestaurantEventNotFoundException|ValidationException */
    public function submitReservation(string $slug, ReservationFormData $formData): ReservationSubmissionResult
    {
        $event = $this->loadRestaurantEvent($slug);
        $this->validateReservationData($formData);
        $reservation = $this->buildReservation($event, $formData);
        $reservationId = $this->reservationRepository->insert($reservation);

        return new ReservationSubmissionResult($reservationId);
    }

    private function loadRestaurantEvent(string $slug): RestaurantDetailEvent
    {
        $event = $this->eventRepository->findActiveRestaurantBySlug(
            trim(strtolower(rawurldecode($slug)), '-'),
        );

        if ($event === null) {
            throw new RestaurantEventNotFoundException($slug);
        }

        return $event;
    }

    private function validateReservationData(ReservationFormData $data): void
    {
        $errors = [];

        if (!in_array($data->diningDate, RestaurantPageConstants::VALID_DATES, true)) {
            $errors[] = 'Please select a valid dining date.';
        }
        if ($data->timeSlot === '') {
            $errors[] = 'Please select a time slot.';
        }
        if ($data->totalGuests() < 1) {
            $errors[] = 'Please add at least one guest.';
        }
        if ($data->totalGuests() > RestaurantPageConstants::MAX_GUEST_COUNT) {
            $errors[] = 'Maximum ' . RestaurantPageConstants::MAX_GUEST_COUNT . ' guests per reservation.';
        }
        if (strlen($data->specialRequests) > RestaurantPageConstants::MAX_SPECIAL_REQUESTS_LENGTH) {
            $errors[] = 'Special requests may not exceed ' . RestaurantPageConstants::MAX_SPECIAL_REQUESTS_LENGTH . ' characters.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }

    private function buildReservation(RestaurantDetailEvent $event, ReservationFormData $data): Reservation
    {
        return new Reservation(
            eventId: $event->eventId,
            diningDate: $data->diningDate,
            timeSlot: $data->timeSlot,
            adultsCount: $data->adultsCount,
            childrenCount: $data->childrenCount,
            specialRequests: $data->specialRequests,
            totalFee: $this->calculateReservationFee($data),
        );
    }

    private function calculateReservationFee(ReservationFormData $data): float
    {
        return $data->totalGuests() * RestaurantPageConstants::RESERVATION_FEE;
    }
}
