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
     * @throws RestaurantEventNotFoundException if the event is not found
     * @throws ValidationException if the submitted data fails validation
     */
    public function submitReservation(string $slug, ReservationFormData $formData): ReservationSubmissionResult
    {
        $event = $this->loadRestaurantEvent($slug);
        $this->validateReservationData($formData);
        $reservation = $this->buildReservation($event, $formData);
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
     * Checks the reservation form data against the business rules used by the form.
     *
     * It returns nothing because validation failures should stop the flow immediately
     * by throwing one ValidationException that contains every collected error.
     */
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

    /**
     * Builds the Reservation model that will be inserted into the database.
     *
     * Returning a Reservation object here keeps the repository layer simple:
     * it receives one well-formed model instead of having to rebuild form data itself.
     */
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

    /**
     * Calculates the fixed reservation fee based on the total number of guests.
     *
     * It returns an integer because the reservation fee is stored as a whole-number amount
     * in the current domain model, not as a floating-point currency value.
     */
    private function calculateReservationFee(ReservationFormData $data): int
    {
        return $data->totalGuests() * RestaurantPageConstants::RESERVATION_FEE;
    }
}
