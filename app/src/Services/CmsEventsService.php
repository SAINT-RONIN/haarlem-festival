<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\CmsEventConstraints;
use App\DTOs\Cms\EventSessionUpsertData;
use App\DTOs\Cms\EventUpsertData;
use App\DTOs\Domain\Events\EventEditBundle;
use App\DTOs\Domain\Events\EventsListPageData;
use App\DTOs\Domain\Events\EventWithDetails;
use App\DTOs\Domain\Filters\EventFilter;
use App\DTOs\Domain\Filters\EventSessionFilter;
use App\DTOs\Domain\Filters\EventTypeFilter;
use App\DTOs\Domain\Filters\VenueFilter;
use App\DTOs\Domain\Schedule\SessionWithEvent;
use App\Enums\DayOfWeek;
use App\Enums\PriceTierId;
use App\Exceptions\CmsOperationException;
use App\Exceptions\ValidationException;
use App\Helpers\FormatHelper;
use App\Models\EventType;
use App\Models\PriceTier;
use App\Models\Venue;
use App\Repositories\Interfaces\ICmsRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IEventSessionLabelRepository;
use App\Repositories\Interfaces\IEventSessionPriceRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IEventTypeRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IPriceTierRepository;
use App\Repositories\Interfaces\IVenueRepository;
use App\Services\Interfaces\ICmsEventsService;

/**
 * CMS-side event and session management: CRUD, pricing, and labels.
 *
 * Events are soft-deleted (IsActive = 0) so historical order references remain valid.
 * Sessions with sold tickets cannot be hard-deleted; cancel them instead.
 * Price input accepts European comma-decimal format (e.g. "12,50") and normalises to float.
 */
class CmsEventsService extends BaseCmsEventsService implements ICmsEventsService
{
    public function __construct(
        private readonly \PDO $pdo,
        IEventRepository $eventRepository,
        private readonly IEventSessionRepository $sessionRepository,
        private readonly IEventSessionLabelRepository $labelRepository,
        private readonly IEventSessionPriceRepository $priceRepository,
        private readonly IEventTypeRepository $eventTypeRepository,
        IVenueRepository $venueRepository,
        private readonly IPriceTierRepository $priceTierRepository,
        private readonly IOrderItemRepository $orderItemRepository,
        ICmsRepository $cmsRepository,
        private readonly IMediaAssetRepository $mediaAssetRepository,
    ) {
        parent::__construct($eventRepository, $venueRepository, $cmsRepository);
    }

    /**
     * @return EventWithDetails[]
     */
    public function getAllEventsWithDetails(?int $eventTypeId = null, ?string $dayOfWeek = null): array
    {
        // Apprently mySQL day numbering differs from PHP: MySQL uses 1=Sunday, PHP uses 1=Monday, I think it's like an ISO. Like 1 stands for Monday.
        $dayNumber = ($dayOfWeek !== null && $dayOfWeek !== '')
            ? FormatHelper::dayNameToMysqlDayOfWeek($dayOfWeek)
            : null;

        return $this->eventRepository->findEvents(new EventFilter(
            isActive:            true,
            includeSessionCount: true,
            eventTypeId:         $eventTypeId,
            dayOfWeekNumber:     $dayNumber,
        ));
    }

    /** @return EventType[] */
    public function getEventTypes(): array
    {
        return $this->eventTypeRepository->findEventTypes(new EventTypeFilter(orderBy: 'name'));
    }

    /** @return Venue[] */
    public function getVenues(): array
    {
        return $this->venueRepository->findVenues(new VenueFilter(isActive: true));
    }

    public function getEventsListPageData(?int $eventTypeId = null, ?string $dayOfWeek = null): EventsListPageData
    {
        return new EventsListPageData(
            events:         $this->getAllEventsWithDetails($eventTypeId, $dayOfWeek),
            eventTypes:     $this->getEventTypes(),
            venues:         $this->getVenues(),
            weeklySchedule: $this->getWeeklyScheduleOverview($eventTypeId),
        );
    }

    /** @throws ValidationException */
    public function createVenue(string $name, string $addressLine): int
    {
        $errors = $this->validateVenueName($name);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->venueRepository->create($name, $addressLine);
    }

    /**
     * The 120-character limit matches the DB column length so the error is caught here with a clear message.
     *
     * @return string[]
     */
    private function validateVenueName(string $name): array
    {
        $errors = [];

        if (trim($name) === '') {
            $errors[] = 'Venue name is required';
        }

        if (strlen($name) > 120) {
            $errors[] = 'Venue name must be 120 characters or less';
        }

        return $errors;
    }

    /** @return PriceTier[] */
    public function getPriceTiers(): array
    {
        return $this->priceTierRepository->findAll();
    }

    /**
     * All seven days are pre-seeded so the schedule grid always renders every day,
     * even when a day has no sessions.
     *
     * @return array<string, SessionWithEvent[]>
     */
    public function getWeeklyScheduleOverview(?int $eventTypeId = null): array
    {
        $schedule = $this->initializeWeekSchedule();
        $sessions = $this->sessionRepository->findSessions(new EventSessionFilter(
            eventTypeId:      $eventTypeId,
            isActive:         true,
            includeCancelled: false,
            eventIsActive:    true,
            orderBy:          'es.StartDateTime ASC',
        ))->sessions;

        return $this->groupSessionsByDay($sessions, $schedule);
    }

    /** @return array<string, SessionWithEvent[]> */
    private function initializeWeekSchedule(): array
    {
        $weekDays = [
            DayOfWeek::Monday->name,
            DayOfWeek::Tuesday->name,
            DayOfWeek::Wednesday->name,
            DayOfWeek::Thursday->name,
            DayOfWeek::Friday->name,
            DayOfWeek::Saturday->name,
            DayOfWeek::Sunday->name,
        ];
        return array_fill_keys($weekDays, []);
    }

    /** @return array<string, SessionWithEvent[]> */
    private function groupSessionsByDay(array $sessions, array $schedule): array
    {
        foreach ($sessions as $session) {
            $dayName = $session->dayOfWeek;
            if (isset($schedule[$dayName])) {
                $schedule[$dayName][] = $session;
            }
        }
        return $schedule;
    }

    /**
     * @throws ValidationException
     * @throws CmsOperationException
     */
    public function createEvent(EventUpsertData $data): int
    {
        $errors = $this->validateEventCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $data = $data->withSlug($this->resolveUniqueSlug($data->slug ?? ''));

        try {
            $newEventId = $this->eventRepository->create($data);
            $this->autoCreateCmsSection($data->eventTypeId, $newEventId);
            $this->saveRestaurantCmsItems($data->eventTypeId, $newEventId, $data);
            return $newEventId;
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to create event.', 0, $error);
        }
    }

    /**
     * Prices and labels are bulk-loaded (one query each) to avoid N+1 per session.
     *
     * @return EventEditBundle|null
     */
    public function getEventForEdit(int $eventId): ?EventEditBundle
    {
        $event = $this->loadEventWithSessionCount($eventId);
        if ($event === null) {
            return null;
        }

        $sessions                = $this->loadSessionsForEdit($eventId);
        [$pricesMap, $labelsMap] = $this->loadSessionPricesAndLabels($sessions);
        $restaurantCms           = $this->loadRestaurantCmsItems($event->eventTypeId, $eventId);

        $featuredImagePath = $event->featuredImageAssetId !== null
            ? $this->mediaAssetRepository->findById($event->featuredImageAssetId)?->filePath
            : null;

        return new EventEditBundle(
            event:                      $event,
            sessions:                   $sessions,
            pricesMap:                  $pricesMap,
            labelsMap:                  $labelsMap,
            cmsDetailEditUrl:           $this->resolveCmsDetailEditUrl($event->eventTypeId),
            restaurantStars:            $restaurantCms['stars'],
            restaurantCuisine:          $restaurantCms['cuisine'],
            restaurantShortDescription: $restaurantCms['shortDescription'],
            featuredImagePath:          $featuredImagePath,
        );
    }

    /** @return EventWithDetails|null */
    private function loadEventWithSessionCount(int $eventId): ?EventWithDetails
    {
        return $this->eventRepository->findEvents(new EventFilter(
            eventId:             $eventId,
            includeSessionCount: true,
        ))[0] ?? null;
    }

    /**
     * Cancelled sessions are included so editors can review or reactivate them.
     *
     * @return SessionWithEvent[]
     */
    private function loadSessionsForEdit(int $eventId): array
    {
        return $this->sessionRepository->findSessions(new EventSessionFilter(
            eventId:          $eventId,
            includeCancelled: true,
            orderBy:          'es.StartDateTime ASC',
        ))->sessions;
    }

    /**
     * Fetches prices and labels in two bulk queries rather than per-session to avoid N+1.
     *
     * @param SessionWithEvent[] $sessions
     * @return array{0: array, 1: array}
     */
    private function loadSessionPricesAndLabels(array $sessions): array
    {
        $sessionIds = array_map(
            static fn(SessionWithEvent $s): int => $s->eventSessionId,
            $sessions,
        );

        if ($sessionIds === []) {
            return [[], []];
        }

        return [
            $this->priceRepository->findPricesBySessionIds($sessionIds),
            $this->labelRepository->findLabelsBySessionIds($sessionIds),
        ];
    }

    /**
     * The slug is immutable once set; updates always preserve the original from the DB.
     *
     * @throws ValidationException
     * @throws CmsOperationException
     */
    public function updateEvent(int $eventId, EventUpsertData $data): bool
    {
        $errors = $this->validateEvent($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $existing = $this->eventRepository->findById($eventId);
        if ($existing === null) {
            throw new ValidationException(['Event not found']);
        }

        try {
            $result = $this->eventRepository->update($eventId, $data->forUpdate($existing));
            $this->saveRestaurantCmsItems($existing->eventTypeId, $eventId, $data);
            return $result;
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update event.', 0, $error);
        }
    }

    /**
     * The event ID comes from the route parameter, not the form — forEvent() binds it to the DTO.
     *
     * @throws ValidationException
     */
    public function createSession(int $eventId, EventSessionUpsertData $data): int
    {
        $errors = $this->validateSession($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->sessionRepository->create($data->forEvent($eventId));
    }

    /** @throws ValidationException */
    public function updateSession(int $sessionId, EventSessionUpsertData $data): bool
    {
        $errors = $this->validateSession($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->sessionRepository->update($sessionId, $data);
    }

    /**
     * Deleting a session with sold tickets would orphan order history; cancel it instead.
     *
     * @throws ValidationException
     */
    public function deleteSession(int $sessionId): bool
    {
        if ($this->orderItemRepository->existsForSession($sessionId)) {
            throw new ValidationException(['This session has sold tickets and cannot be deleted.']);
        }

        return $this->sessionRepository->delete($sessionId);
    }

    /** @throws ValidationException */
    public function addLabel(int $sessionId, string $labelText): int
    {
        $errors = $this->validateLabel($sessionId, $labelText);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->labelRepository->create($sessionId, $labelText);
    }

    public function deleteLabel(int $labelId): bool
    {
        return $this->labelRepository->delete($labelId);
    }

    /**
     * European comma notation (e.g. "12,50") is normalised to dot-decimal before validation.
     *
     * @throws ValidationException
     */
    public function setSessionPrice(int $sessionId, ?int $priceTierId, string $rawPrice): bool
    {
        $resolvedPriceTierId = $this->resolvePriceTierId($priceTierId);
        $price               = (float) str_replace(',', '.', $rawPrice);

        $errors = $this->validatePrice($resolvedPriceTierId, $price);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->priceRepository->upsert($sessionId, $resolvedPriceTierId, $price);
    }

    /**
     * Soft-deletes the event and deactivates its sessions in a single transaction.
     *
     * @throws ValidationException
     * @throws CmsOperationException
     */
    public function deleteEvent(int $eventId): void
    {
        try {
            if (!$this->eventRepository->exists($eventId)) {
                throw new ValidationException(['Event not found']);
            }

            $this->pdo->beginTransaction();
            $this->eventRepository->softDelete($eventId);
            $this->sessionRepository->deactivateByEventId($eventId);
            $this->pdo->commit();
        } catch (ValidationException $error) {
            throw $error;
        } catch (\Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new CmsOperationException('Failed to delete event.', 0, $error);
        }
    }

    public function deleteVenue(int $venueId): bool
    {
        return $this->venueRepository->softDelete($venueId);
    }

    // -------------------------------------------------------------------------
    // Private: validation helpers
    // -------------------------------------------------------------------------

    // Adult is the default tier because most sessions only have one price.
    private function resolvePriceTierId(?int $priceTierId): int
    {
        return $priceTierId ?? PriceTierId::Adult->value;
    }

    /** @return string[] */
    private function validateEvent(EventUpsertData $data): array
    {
        $errors = [];

        if (trim($data->title) === '') {
            $errors[] = 'Event title is required';
        }

        return $errors;
    }

    /**
     * EventTypeId is only required at creation; updates preserve the existing type from the DB.
     *
     * @return string[]
     */
    private function validateEventCreate(EventUpsertData $data): array
    {
        $errors = $this->validateEvent($data);

        if ($data->eventTypeId <= 0) {
            $errors[] = 'Event type is required';
        }

        return $errors;
    }

    /** @return string[] */
    private function validateSession(EventSessionUpsertData $data): array
    {
        return array_merge(
            $this->validateSessionDates($data),
            $this->validateSessionCtaUrl($data),
            $this->validateCapacityTotal($data),
            $this->validateCapacitySingleTicketLimit($data),
            $this->validateDurationMinutes($data),
            $this->validateAgeRange($data),
        );
    }

    /** @return string[] */
    private function validateSessionDates(EventSessionUpsertData $data): array
    {
        return array_merge(
            $this->validateSessionStartTime($data),
            $this->validateSessionEndTime($data),
            $this->validateSessionDateRange($data),
        );
    }

    /** @return string[] */
    private function validateSessionStartTime(EventSessionUpsertData $data): array
    {
        if ($data->startDateTime === '') {
            return ['Start date/time is required'];
        }

        return [];
    }

    /** @return string[] */
    private function validateSessionEndTime(EventSessionUpsertData $data): array
    {
        if ($data->endDateTime === '') {
            return ['End date/time is required'];
        }

        return [];
    }

    /**
     * Skips the range check when either field is blank; the presence checks above already report those errors.
     *
     * @return string[]
     */
    private function validateSessionDateRange(EventSessionUpsertData $data): array
    {
        if ($data->startDateTime === '' || $data->endDateTime === '') {
            return [];
        }

        $start = $this->parseSessionDateTime($data->startDateTime);
        $end   = $this->parseSessionDateTime($data->endDateTime);

        if ($start === null || $end === null) {
            return ['Invalid date/time format'];
        }

        if ($end <= $start) {
            return ['End time must be after start time'];
        }

        return [];
    }

    /**
     * Multiple formats are tried because the HTML datetime-local input and the DB use different ones.
     * getLastErrors() is checked because createFromFormat can return a result but still log warnings on partial matches.
     */
    private function parseSessionDateTime(string $value): ?\DateTimeImmutable
    {
        foreach (['Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d H:i'] as $format) {
            $dateTime = \DateTimeImmutable::createFromFormat($format, $value);
            $errors   = \DateTimeImmutable::getLastErrors();

            if (
                $dateTime !== false
                && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
            ) {
                return $dateTime;
            }
        }

        return null;
    }

    /**
     * A bare word without a leading slash would silently become a broken relative link.
     *
     * @return string[]
     */
    private function validateSessionCtaUrl(EventSessionUpsertData $data): array
    {
        if ($data->ctaUrl === null || $data->ctaUrl === '') {
            return [];
        }

        $url        = trim($data->ctaUrl);
        $isAbsolute = filter_var($url, FILTER_VALIDATE_URL) !== false;
        $isRelative = str_starts_with($url, '/') || str_starts_with($url, '#');

        if (!$isAbsolute && !$isRelative) {
            return ['CTA URL must be a valid URL or relative path (starting with / or #)'];
        }

        return [];
    }

    /** @return string[] */
    private function validateCapacityTotal(EventSessionUpsertData $data): array
    {
        if ($data->capacityTotal === null) {
            return [];
        }

        if ($data->capacityTotal <= 0) {
            return ['Capacity must be a positive integer'];
        }

        return [];
    }

    /** @return string[] */
    private function validateCapacitySingleTicketLimit(EventSessionUpsertData $data): array
    {
        if ($data->capacityTotal === null || $data->capacitySingleTicketLimit === null) {
            return [];
        }

        if ($data->capacitySingleTicketLimit > $data->capacityTotal) {
            return ['Single ticket limit cannot exceed total capacity'];
        }

        return [];
    }

    /** @return string[] */
    private function validateDurationMinutes(EventSessionUpsertData $data): array
    {
        if ($data->durationMinutes === null) {
            return [];
        }

        if ($data->durationMinutes <= 0) {
            return ['Duration must be a positive integer'];
        }

        return [];
    }

    /** @return string[] */
    private function validateAgeRange(EventSessionUpsertData $data): array
    {
        if ($data->minAge === null || $data->maxAge === null) {
            return [];
        }

        if ($data->maxAge < $data->minAge) {
            return ['Maximum age cannot be less than minimum age'];
        }

        return [];
    }

    /** @return string[] */
    private function validateLabel(int $sessionId, string $labelText): array
    {
        $errors    = [];
        $labelText = trim($labelText);

        if ($labelText === '') {
            $errors[] = 'Label text cannot be empty';
        }

        if (strlen($labelText) > CmsEventConstraints::MAX_LABEL_LENGTH) {
            $errors[] = 'Label text must be ' . CmsEventConstraints::MAX_LABEL_LENGTH . ' characters or less';
        }

        $currentCount = $this->labelRepository->countBySession($sessionId);
        if ($currentCount >= CmsEventConstraints::MAX_LABELS_PER_SESSION) {
            $errors[] = 'Maximum ' . CmsEventConstraints::MAX_LABELS_PER_SESSION . ' labels per session';
        }

        return $errors;
    }

    /**
     * Pay What YouLike price has to be 0 because the actual amount is collected separately at checkout.
     *
     * @return string[]
     */
    private function validatePrice(int $priceTierId, float $price): array
    {
        $errors = [];

        if ($price < 0) {
            $errors[] = 'Price must be zero or positive';
        }

        if ($priceTierId === PriceTierId::PayWhatYouLike->value && $price > 0) {
            $errors[] = 'Pay-what-you-like events must have price 0';
        }

        return $errors;
    }
}
