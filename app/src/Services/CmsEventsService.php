<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DayOfWeek;
use App\Enums\PriceTierId;
use App\Helpers\FormatHelper;
use App\Exceptions\CmsOperationException;
use App\Exceptions\ValidationException;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IEventSessionLabelRepository;
use App\Repositories\Interfaces\IEventSessionPriceRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IEventTypeRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IPriceTierRepository;
use App\Repositories\Interfaces\IScheduleDayConfigRepository;
use App\Repositories\Interfaces\IVenueRepository;
use App\DTOs\Cms\EventUpsertData;
use App\DTOs\Events\EventEditBundle;
use App\DTOs\Filters\EventFilter;
use App\DTOs\Filters\EventSessionFilter;
use App\DTOs\Events\EventsListPageData;
use App\DTOs\Schedule\GroupedScheduleDayConfigs;
use App\DTOs\Filters\EventTypeFilter;
use App\DTOs\Pages\ScheduleDaysPageData;
use App\DTOs\Filters\ScheduleDayConfigFilter;
use App\DTOs\Filters\VenueFilter;
use App\Services\Interfaces\ICmsEventsService;
use App\Constants\CmsEventConstraints;

/**
 * CMS-side event and session management: CRUD, pricing, labels, and schedule visibility.
 *
 * Orchestrates ten+ repositories to provide event lifecycle operations. Key design decisions:
 * - Events are soft-deleted (IsActive = 0) so historical order references remain valid.
 * - Sessions with sold tickets cannot be hard-deleted; they must be cancelled instead.
 * - Schedule day visibility uses a two-tier system: global defaults that can be overridden per event type.
 * - Price input accepts European comma-decimal format (e.g. "12,50") and normalizes to float.
 */
class CmsEventsService implements ICmsEventsService
{
    public function __construct(
        private readonly \PDO $pdo,
        private readonly IEventRepository $eventRepository,
        private readonly IEventSessionRepository $sessionRepository,
        private readonly IEventSessionLabelRepository $labelRepository,
        private readonly IEventSessionPriceRepository $priceRepository,
        private readonly IEventTypeRepository $eventTypeRepository,
        private readonly IVenueRepository $venueRepository,
        private readonly IPriceTierRepository $priceTierRepository,
        private readonly IScheduleDayConfigRepository $scheduleDayConfigRepository,
        private readonly IOrderItemRepository $orderItemRepository,
        private readonly ScheduleDayVisibilityResolver $visibilityResolver,
    ) {
    }

    /**
     * Returns active events enriched with session counts, for the CMS events list.
     *
     * @return \App\Models\Event[]
     */
    public function getAllEventsWithDetails(?int $eventTypeId = null, ?string $dayOfWeek = null): array
    {
        $dayNumber = ($dayOfWeek !== null && $dayOfWeek !== '')
            ? FormatHelper::dayNameToMysqlDayOfWeek($dayOfWeek)
            : null;

        return $this->eventRepository->findEvents(new EventFilter(
            isActive: true,
            includeSessionCount: true,
            eventTypeId: $eventTypeId,
            dayOfWeekNumber: $dayNumber,
        ));
    }

    /**
     * @return \App\Models\EventType[] Ordered by name, for populating filter/form dropdowns.
     */
    public function getEventTypes(): array
    {
        return $this->eventTypeRepository->findEventTypes(new EventTypeFilter(orderBy: 'name'));
    }

    /**
     * @return \App\Models\Venue[] Active venues only, for populating form dropdowns.
     */
    public function getVenues(): array
    {
        return $this->venueRepository->findVenues(new VenueFilter(isActive: true));
    }

    /**
     * Assembles events, event types, venues, and weekly schedule into a single page payload,
     * so the controller only needs one service call to render the events list view.
     */
    public function getEventsListPageData(?int $eventTypeId = null, ?string $dayOfWeek = null): EventsListPageData
    {
        return new EventsListPageData(
            events: $this->getAllEventsWithDetails($eventTypeId, $dayOfWeek),
            eventTypes: $this->getEventTypes(),
            venues: $this->getVenues(),
            weeklySchedule: $this->getWeeklyScheduleOverview($eventTypeId),
        );
    }

    /**
     * Assembles all data needed for the CMS schedule days management page.
     */
    public function getScheduleDaysPageData(): ScheduleDaysPageData
    {
        return new ScheduleDaysPageData(
            eventTypes: $this->getEventTypes(),
            grouped: $this->getGroupedScheduleDayConfigs(),
        );
    }

    /**
     * Creates a new venue.
     *
     * @throws ValidationException
     */
    public function createVenue(string $name, string $addressLine): int
    {
        $errors = $this->validateVenueName($name);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->venueRepository->create($name, $addressLine ?: '');
    }

    /**
     * Validates venue name.
     */
    private function validateVenueName(string $name): array
    {
        $errors = [];
        if (empty($name)) {
            $errors[] = 'Venue name is required';
        }
        if (strlen($name) > 120) {
            $errors[] = 'Venue name must be 120 characters or less';
        }
        return $errors;
    }

    /**
     * Gets all price tiers for dropdown.
     */
    public function getPriceTiers(): array
    {
        return $this->priceTierRepository->findAll();
    }

    /**
     * Builds a Mon-Sun schedule grid by fetching active, non-cancelled sessions
     * and bucketing them by day name. Empty days are included so the UI can render
     * a full 7-day grid without null checks.
     *
     * @return array<string, \App\DTOs\Schedule\SessionWithEvent[]>
     */
    public function getWeeklyScheduleOverview(?int $eventTypeId = null): array
    {
        $schedule = $this->initializeWeekSchedule();
        $sessions = $this->sessionRepository->findSessions(new EventSessionFilter(
            eventTypeId: $eventTypeId,
            isActive: true,
            includeCancelled: false,
            eventIsActive: true,
            orderBy: 'es.StartDateTime ASC',
        ))->sessions;

        return $this->groupSessionsByDay($sessions, $schedule);
    }

    /**
     * Initializes empty schedule array for all week days.
     *
     * @return array<string, \App\DTOs\Schedule\SessionWithEvent[]>
     */
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

    /**
     * Groups SessionWithEvent models by their day of week name.
     *
     * @return array<string, \App\DTOs\Schedule\SessionWithEvent[]>
     */
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
     * Creates a new event.
     *
     * @throws ValidationException
     */
    /**
     * @throws ValidationException When validation fails
     * @throws CmsOperationException When the database write fails
     */
    public function createEvent(array $data): int
    {
        $errors = $this->validateEventCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $upsertData = $this->buildEventUpsertData($data, isActive: true);

        try {
            return $this->eventRepository->create($upsertData);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to create event.', 0, $error);
        }
    }

    /**
     * Assembles a single event together with its sessions, prices, and labels
     * into an EventEditBundle for the CMS edit form. Returns null when the event does not exist.
     */
    public function getEventForEdit(int $eventId): ?EventEditBundle
    {
        // Load the event with its session count
        $event = $this->eventRepository->findEvents(new EventFilter(
            eventId: $eventId,
            includeSessionCount: true,
        ))[0] ?? null;
        if (!$event) {
            return null;
        }

        // Load all sessions (including cancelled) for the edit view
        $sessions = $this->sessionRepository->findSessions(new EventSessionFilter(
            eventId: $eventId,
            includeCancelled: true,
            orderBy: 'es.StartDateTime ASC',
        ))->sessions;

        // Batch-load prices and labels for all sessions
        [$pricesMap, $labelsMap] = $this->loadSessionPricesAndLabels($sessions);

        return new EventEditBundle(
            event: $event,
            sessions: $sessions,
            pricesMap: $pricesMap,
            labelsMap: $labelsMap,
        );
    }

    /**
     * @param \App\DTOs\Schedule\SessionWithEvent[] $sessions
     * @return array{0: array, 1: array}
     */
    private function loadSessionPricesAndLabels(array $sessions): array
    {
        $sessionIds = array_map(
            static fn (\App\DTOs\Schedule\SessionWithEvent $s): int => $s->eventSessionId,
            $sessions,
        );

        if (empty($sessionIds)) {
            return [[], []];
        }

        return [
            $this->priceRepository->findPricesBySessionIds($sessionIds),
            $this->labelRepository->findLabelsBySessionIds($sessionIds),
        ];
    }

    /**
     * Updates an event's basic information.
     *
     * @throws ValidationException
     */
    /** @throws CmsOperationException When the database write fails */
    public function updateEvent(int $eventId, array $data): bool
    {
        $errors = $this->validateEvent($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $isActive = isset($data['IsActive']) ? (bool)$data['IsActive'] : true;
        $upsertData = $this->buildEventUpsertData($data, isActive: $isActive);

        try {
            return $this->eventRepository->update($eventId, $upsertData);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update event.', 0, $error);
        }
    }

    /**
     * Creates a new event session.
     *
     * @throws ValidationException
     */
    public function createSession(int $eventId, array $data): int
    {
        $data['EventId'] = $eventId;
        $errors = $this->validateSession($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $data = $this->applySessionDefaults($data);

        return $this->sessionRepository->create($data);
    }

    /**
     * Updates an event session.
     *
     * @throws ValidationException
     */
    public function updateSession(int $sessionId, array $data): bool
    {
        $errors = $this->validateSession($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $data = $this->applySessionDefaults($data);

        return $this->sessionRepository->update($sessionId, $data);
    }

    /**
     * Hard-deletes an event session. Blocked if any order items reference this session
     * (sessions with sold tickets must be cancelled instead).
     *
     * @throws ValidationException When tickets have already been sold for this session
     */
    public function deleteSession(int $sessionId): bool
    {
        if ($this->orderItemRepository->existsForSession($sessionId)) {
            throw new ValidationException(['This session has sold tickets and cannot be deleted.']);
        }
        return $this->sessionRepository->delete($sessionId);
    }

    /**
     * Adds a label to a session.
     *
     * @throws ValidationException
     */
    public function addLabel(int $sessionId, string $labelText): int
    {
        $errors = $this->validateLabel($sessionId, $labelText);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->labelRepository->create($sessionId, $labelText);
    }

    /**
     * Deletes a label.
     */
    public function deleteLabel(int $labelId): bool
    {
        return $this->labelRepository->delete($labelId);
    }

    /**
     * Sets the price for a session.
     *
     * @throws ValidationException
     */
    public function setSessionPrice(int $sessionId, int $priceTierId, string $rawPrice): bool
    {
        // Normalize comma decimal separators (e.g. "12,50" -> "12.50") for European input
        $price = (float) str_replace(',', '.', $rawPrice);
        $errors = $this->validatePrice($priceTierId, $price);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->priceRepository->upsert($sessionId, $priceTierId, $price);
    }

    /**
     * Constructs a typed EventUpsertData from raw form data with proper defaults.
     * Business-logic defaults (empty description, placeholder HTML) belong here, not in the repo.
     */
    private function buildEventUpsertData(array $data, bool $isActive): EventUpsertData
    {
        return new EventUpsertData(
            eventTypeId: (int)$data['EventTypeId'],
            title: (string)$data['Title'],
            shortDescription: (string)($data['ShortDescription'] ?? ''),
            longDescriptionHtml: (string)($data['LongDescriptionHtml'] ?? '<p></p>'),
            featuredImageAssetId: isset($data['FeaturedImageAssetId']) && is_numeric($data['FeaturedImageAssetId'])
                ? (int)$data['FeaturedImageAssetId'] : null,
            venueId: isset($data['VenueId']) && is_numeric($data['VenueId'])
                ? (int)$data['VenueId'] : null,
            artistId: isset($data['ArtistId']) && is_numeric($data['ArtistId'])
                ? (int)$data['ArtistId'] : null,
            restaurantId: isset($data['RestaurantId']) && is_numeric($data['RestaurantId'])
                ? (int)$data['RestaurantId'] : null,
            isActive: $isActive,
        );
    }

    /**
     * Validates event data for update.
     */
    private function validateEvent(array $data): array
    {
        $errors = [];

        if (empty($data['Title'])) {
            $errors[] = 'Event title is required';
        }

        return $errors;
    }

    /**
     * Validates event data for creation (includes base event rules plus event-type requirement).
     */
    private function validateEventCreate(array $data): array
    {
        $errors = $this->validateEvent($data);

        if (empty($data['EventTypeId'])) {
            $errors[] = 'Event type is required';
        }

        return $errors;
    }

    /**
     * Validates session data: delegates to focused validators for dates, URLs, and numeric constraints.
     */
    private function validateSession(array $data): array
    {
        $dateErrors = $this->validateSessionDates($data);
        $urlErrors = $this->validateSessionCtaUrl($data);
        $numericErrors = array_merge(
            $this->validateCapacityTotal($data),
            $this->validateCapacitySingleTicketLimit($data),
            $this->validateDurationMinutes($data),
            $this->validateAgeRange($data),
        );

        return array_merge($dateErrors, $urlErrors, $numericErrors);
    }

    /** Checks that both dates are present and that end is after start. */
    private function validateSessionDates(array $data): array
    {
        $errors = [];

        if (empty($data['StartDateTime'])) {
            $errors[] = 'Start date/time is required';
        }

        if (empty($data['EndDateTime'])) {
            $errors[] = 'End date/time is required';
        }

        if (!empty($data['StartDateTime']) && !empty($data['EndDateTime'])) {
            try {
                $start = new \DateTimeImmutable($data['StartDateTime']);
                $end = new \DateTimeImmutable($data['EndDateTime']);
                if ($end <= $start) {
                    $errors[] = 'End time must be after start time';
                }
            } catch (\Exception $e) {
                $errors[] = 'Invalid date/time format';
            }
        }

        return $errors;
    }

    /** Validates CTA URL is absolute or site-relative. */
    private function validateSessionCtaUrl(array $data): array
    {
        if (empty($data['CtaUrl'])) {
            return [];
        }

        $url = trim($data['CtaUrl']);
        $isAbsolute = filter_var($url, FILTER_VALIDATE_URL) !== false;
        $isRelative = str_starts_with($url, '/') || str_starts_with($url, '#');

        if (!$isAbsolute && !$isRelative) {
            return ['CTA URL must be a valid URL or relative path (starting with / or #)'];
        }

        return [];
    }

    /**
     * Validates that CapacityTotal is a positive integer, if provided.
     */
    private function validateCapacityTotal(array $data): array
    {
        if (!array_key_exists('CapacityTotal', $data)) {
            return [];
        }
        if ((int) $data['CapacityTotal'] <= 0) {
            return ['Capacity must be a positive integer'];
        }
        return [];
    }

    /**
     * Validates that CapacitySingleTicketLimit does not exceed CapacityTotal, if both are provided.
     */
    private function validateCapacitySingleTicketLimit(array $data): array
    {
        if (!array_key_exists('CapacityTotal', $data) || !array_key_exists('CapacitySingleTicketLimit', $data)) {
            return [];
        }
        if ((int) $data['CapacitySingleTicketLimit'] > (int) $data['CapacityTotal']) {
            return ['Single ticket limit cannot exceed total capacity'];
        }
        return [];
    }

    /**
     * Validates that DurationMinutes is a positive integer, if provided and not empty.
     */
    private function validateDurationMinutes(array $data): array
    {
        if (!array_key_exists('DurationMinutes', $data) || $data['DurationMinutes'] === '' || $data['DurationMinutes'] === null) {
            return [];
        }
        if ((int) $data['DurationMinutes'] <= 0) {
            return ['Duration must be a positive integer'];
        }
        return [];
    }

    /**
     * Validates that MaxAge is at least MinAge, if both are provided and not empty.
     */
    private function validateAgeRange(array $data): array
    {
        $hasMin = array_key_exists('MinAge', $data) && $data['MinAge'] !== '' && $data['MinAge'] !== null;
        $hasMax = array_key_exists('MaxAge', $data) && $data['MaxAge'] !== '' && $data['MaxAge'] !== null;
        if (!$hasMin || !$hasMax) {
            return [];
        }
        if ((int) $data['MaxAge'] < (int) $data['MinAge']) {
            return ['Maximum age cannot be less than minimum age'];
        }
        return [];
    }

    /**
     * Applies business-logic defaults for session fields before persisting.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function applySessionDefaults(array $data): array
    {
        $data['CapacityTotal'] = (int)($data['CapacityTotal'] ?? 100);
        $data['CapacitySingleTicketLimit'] = (int)($data['CapacitySingleTicketLimit'] ?? 100);
        $data['ReservationRequired'] = $data['ReservationRequired'] ?? 0;
        $data['IsFree'] = $data['IsFree'] ?? 0;
        $data['Notes'] = $data['Notes'] ?? '';

        return $data;
    }

    /**
     * Validates label text (non-empty, length limit) and enforces a maximum labels-per-session cap.
     */
    private function validateLabel(int $sessionId, string $labelText): array
    {
        $errors = [];

        $labelText = trim($labelText);

        if (empty($labelText)) {
            $errors[] = 'Label text cannot be empty';
        }

        if (strlen($labelText) > CmsEventConstraints::MAX_LABEL_LENGTH) {
            $errors[] = 'Label text must be ' . CmsEventConstraints::MAX_LABEL_LENGTH . ' characters or less';
        }

        // Check max labels per session
        $currentCount = $this->labelRepository->countBySession($sessionId);
        if ($currentCount >= CmsEventConstraints::MAX_LABELS_PER_SESSION) {
            $errors[] = 'Maximum ' . CmsEventConstraints::MAX_LABELS_PER_SESSION . ' labels per session';
        }

        return $errors;
    }

    /**
     * Validates price: must be non-negative, and PayWhatYouLike tier must have a zero price.
     */
    private function validatePrice(int $priceTierId, float $price): array
    {
        $errors = [];

        if ($price < 0) {
            $errors[] = 'Price must be zero or positive';
        }

        // PayWhatYouLike must have price = 0
        if ($priceTierId === PriceTierId::PayWhatYouLike->value && $price > 0) {
            $errors[] = 'Pay-what-you-like events must have price 0';
        }

        return $errors;
    }

    /**
     * Soft-deletes an event and deactivates all its sessions.
     * The event remains in the database (IsActive = 0) for historical order references.
     *
     * @throws ValidationException When the event does not exist
     */
    /** @throws CmsOperationException When the database write fails */
    public function deleteEvent(int $eventId): void
    {
        if (!$this->eventRepository->exists($eventId)) {
            throw new ValidationException(['Event not found']);
        }

        try {
            // Wrap in transaction — both writes must succeed or neither does
            $this->pdo->beginTransaction();
            $this->eventRepository->softDelete($eventId);
            $this->sessionRepository->deactivateByEventId($eventId);
            $this->pdo->commit();
        } catch (\Throwable $error) {
            $this->pdo->rollBack();
            throw new CmsOperationException('Failed to delete event.', 0, $error);
        }
    }

    /**
     * Returns all schedule day configs (both global and per-event-type), sorted by scope.
     */
    public function getScheduleDayConfigs(): array
    {
        return $this->scheduleDayConfigRepository->findConfigs(new ScheduleDayConfigFilter(includeEventTypeName: true, orderBy: 'scope'));
    }

    /**
     * Partitions schedule day configs into global defaults vs. per-event-type overrides,
     * keyed by day-of-week number, for rendering the two-tier visibility grid in the CMS.
     */
    public function getGroupedScheduleDayConfigs(): GroupedScheduleDayConfigs
    {
        $dayConfigs = $this->getScheduleDayConfigs();
        $globalConfigs = [];
        $typeConfigs   = [];
        // Configs without an eventTypeId are global defaults; those with one are type-specific overrides
        foreach ($dayConfigs as $config) {
            if (!$config->eventTypeId) {
                $globalConfigs[(int)$config->dayOfWeek] = $config;
            } else {
                $typeConfigs[(int)$config->eventTypeId][(int)$config->dayOfWeek] = $config;
            }
        }
        return new GroupedScheduleDayConfigs(global: $globalConfigs, byType: $typeConfigs);
    }

    /**
     * Sets the visibility of a schedule day.
     *
     * @param ?int $eventTypeId null for global setting, >0 for specific event type
     * @param int $dayOfWeek 0=Sunday, 1=Monday, ..., 6=Saturday
     * @param bool $isVisible
     * @throws ValidationException
     */
    public function setScheduleDayVisibility(?int $eventTypeId, int $dayOfWeek, bool $isVisible): void
    {
        $dayValues = array_map(static fn (DayOfWeek $day): int => $day->value, DayOfWeek::cases());
        if (!in_array($dayOfWeek, $dayValues, true)) {
            throw new ValidationException(['Invalid day of week']);
        }

        $this->scheduleDayConfigRepository->upsert($eventTypeId, $dayOfWeek, $isVisible);
    }

    /**
     * Determines which days of the week are visible for a given event type by merging
     * global defaults with type-specific overrides. Type settings take precedence.
     *
     * @return int[] Day numbers (0=Sunday through 6=Saturday) that should be shown
     */
    public function getVisibleDays(?int $eventTypeId = null): array
    {
        return $this->visibilityResolver->getVisibleDays($eventTypeId);
    }
}
