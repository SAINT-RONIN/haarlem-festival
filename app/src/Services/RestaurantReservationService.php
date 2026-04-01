<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\RestaurantPageConstants;
use App\DTOs\Events\RestaurantDetailEvent;
use App\DTOs\Restaurant\ReservationSubmissionResult;
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
    ) {
    }

    /**
     * @param array<string, mixed> $postData
     * @throws RestaurantEventNotFoundException if the event is not found
     * @throws ValidationException if the submitted data fails validation
     */
    public function submitReservation(string $slug, array $postData): ReservationSubmissionResult
    {
        $event = $this->loadRestaurantEvent($slug);
        $reservationData = $this->normalizeReservationInput($postData);
        $this->validateReservationData($reservationData);
        $reservation = $this->buildReservation($event, $reservationData);
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

    /**
     * @param array<string, mixed> $postData
     * @return array{
     *     date: string,
     *     timeSlot: string,
     *     adultsCount: int,
     *     childrenCount: int,
     *     specialRequests: string
     * }
     */
    private function normalizeReservationInput(array $postData): array
    {
        return [
            'date' => trim((string)($postData['dining_date'] ?? '')),
            'timeSlot' => trim((string)($postData['time_slot'] ?? '')),
            'adultsCount' => max(0, (int)($postData['adults_count'] ?? 0)),
            'childrenCount' => max(0, (int)($postData['children_count'] ?? 0)),
            'specialRequests' => trim((string)($postData['special_requests'] ?? '')),
        ];
    }

    /**
     * @param array{
     *     date: string,
     *     timeSlot: string,
     *     adultsCount: int,
     *     childrenCount: int,
     *     specialRequests: string
     * } $reservationData
     */
    private function validateReservationData(array $reservationData): void
    {
        $errors = [];

        if (!in_array($reservationData['date'], RestaurantPageConstants::VALID_DATES, true)) {
            $errors[] = 'Please select a valid dining date.';
        }
        if ($reservationData['timeSlot'] === '') {
            $errors[] = 'Please select a time slot.';
        }
        if (($reservationData['adultsCount'] + $reservationData['childrenCount']) < 1) {
            $errors[] = 'Please add at least one guest.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }

    /**
     * @param array{
     *     date: string,
     *     timeSlot: string,
     *     adultsCount: int,
     *     childrenCount: int,
     *     specialRequests: string
     * } $reservationData
     */
    private function buildReservation(RestaurantDetailEvent $event, array $reservationData): Reservation
    {
        return new Reservation(
            restaurantId: $event->restaurantId,
            diningDate: $reservationData['date'],
            timeSlot: $reservationData['timeSlot'],
            adultsCount: $reservationData['adultsCount'],
            childrenCount: $reservationData['childrenCount'],
            specialRequests: $reservationData['specialRequests'],
            totalFee: $this->calculateReservationFee($reservationData),
        );
    }

    /**
     * @param array{adultsCount: int, childrenCount: int} $reservationData
     */
    private function calculateReservationFee(array $reservationData): int
    {
        return ($reservationData['adultsCount'] + $reservationData['childrenCount'])
            * RestaurantPageConstants::RESERVATION_FEE;
    }
}
