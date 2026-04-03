<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\RestaurantPageConstants;
use App\Exceptions\RestaurantEventNotFoundException;
use App\Exceptions\ValidationException;
use App\Models\Reservation;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\ReservationRepository;
use App\Services\Interfaces\IProgramService;
use App\Services\Interfaces\IRestaurantReservationService;

class RestaurantReservationService implements IRestaurantReservationService
{
    private const ERR_INVALID_DATE  = 'Please select a valid dining date.';
    private const ERR_NO_TIME_SLOT  = 'Please select a time slot.';
    private const ERR_NO_GUESTS     = 'Please add at least one guest.';

    public function __construct(
        private readonly IEventRepository        $eventRepository,
        private readonly ReservationRepository   $reservationRepository,
        private readonly IProgramService         $programService,
        private readonly IEventSessionRepository $eventSessionRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $postData
     * @throws RestaurantEventNotFoundException if the event is not found
     * @throws ValidationException if the submitted data fails validation
     */
    public function submitReservation(string $slug, array $postData, string $sessionKey, ?int $userAccountId): void
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
            $errors[] = self::ERR_INVALID_DATE;
        }
        if ($timeSlot === '') {
            $errors[] = self::ERR_NO_TIME_SLOT;
        }
        if ($adultsCount + $childrenCount < 1) {
            $errors[] = self::ERR_NO_GUESTS;
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $reservationId = $this->reservationRepository->insert(new Reservation(
            eventId:         $event->eventId,
            diningDate:      $date,
            timeSlot:        $timeSlot,
            adultsCount:     $adultsCount,
            childrenCount:   $childrenCount,
            specialRequests: $specialRequests,
            totalFee:        ($adultsCount + $childrenCount) * RestaurantPageConstants::RESERVATION_FEE,
        ));

        $isoDate = RestaurantPageConstants::FESTIVAL_DATE_MAP[$date] ?? null;
        if ($isoDate !== null) {
            $this->eventSessionRepository->incrementSoldReservedSeats(
                $event->eventId,
                $isoDate,
                $timeSlot,
                $adultsCount + $childrenCount,
            );
        }

        $this->programService->addReservationToProgram($sessionKey, $userAccountId, $reservationId);
    }
}
