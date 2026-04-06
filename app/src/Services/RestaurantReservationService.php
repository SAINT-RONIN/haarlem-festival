<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\RestaurantPageConstants;
use App\DTOs\Domain\Events\RestaurantDetailEvent;
use App\DTOs\Domain\Restaurant\ReservationSubmissionResult;
use App\Exceptions\RestaurantEventNotFoundException;
use App\Exceptions\ValidationException;
use App\Models\Reservation;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IReservationRepository;
use App\Services\Interfaces\IRestaurantReservationService;

/**
 * Converts raw restaurant reservation form input into one saved reservation record.
 *
 * This service exists to keep the controller small: it knows how to normalize posted values,
 * validate the restaurant-specific rules, build the Reservation model, and save it.
 */
class RestaurantReservationService implements IRestaurantReservationService
{
    /**
     * Stores the repositories needed for reservation submission.
     *
     * The constructor returns nothing because it only prepares the collaborators
     * used by the actual reservation workflow methods.
     */
    public function __construct(
        private readonly IEventRepository $eventRepository,
        private readonly IReservationRepository $reservationRepository,
    ) {
    }

    /**
     * Validates the submitted reservation form, creates the Reservation model, and saves it.
     *
     * It returns ReservationSubmissionResult because the caller still needs the new reservation id
     * for the next step of the flow, which is adding the reservation to the visitor's program.
     *
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

    /**
     * Returns the active restaurant event behind the current page slug.
     *
     * It throws a domain-specific exception instead of returning null because reservation
     * submission cannot continue without a real restaurant event to attach the booking to.
     */
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
     * Converts raw form input into one clean and predictable reservation data array.
     * The returned array is normalized so later validation and model construction
     * do not have to deal with loose POST values or missing keys.
     *
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
     * Checks the normalized reservation data against the business rules used by the form.
     *
     * It returns nothing because validation failures should stop the flow immediately
     * by throwing one ValidationException that contains every collected error.
     *
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
     * Builds the Reservation model that will be inserted into the database.
     *
     * Returning a Reservation object here keeps the repository layer simple:
     * it receives one well-formed model instead of having to rebuild form data itself.
     *
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
            eventId: $event->eventId,
            diningDate: $reservationData['date'],
            timeSlot: $reservationData['timeSlot'],
            adultsCount: $reservationData['adultsCount'],
            childrenCount: $reservationData['childrenCount'],
            specialRequests: $reservationData['specialRequests'],
            totalFee: $this->calculateReservationFee($reservationData),
        );
    }

    /**
     * Calculates the fixed reservation fee based on the total number of guests.
     *
     * It returns an integer because the reservation fee is stored as a whole-number amount
     * in the current domain model, not as a floating-point currency value.
     *
     * @param array{adultsCount: int, childrenCount: int} $reservationData
     */
    private function calculateReservationFee(array $reservationData): int
    {
        return ($reservationData['adultsCount'] + $reservationData['childrenCount'])
            * RestaurantPageConstants::RESERVATION_FEE;
    }
}
