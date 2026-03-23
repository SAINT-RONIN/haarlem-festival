<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DayOfWeek;
use App\Enums\PriceTierId;
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
use App\Models\EventEditBundle;
use App\Models\EventFilter;
use App\Models\EventSessionFilter;
use App\Models\EventsListPageData;
use App\Models\GroupedScheduleDayConfigs;
use App\Models\EventTypeFilter;
use App\Models\ScheduleDaysPageData;
use App\Models\ScheduleDayConfigFilter;
use App\Models\VenueFilter;
use App\Services\Interfaces\ICmsEventsService;
use App\Utils\CmsEventConstraints;

/**
 * Service for CMS Events management.
 *
 * Contains business logic and validation for event/session CRUD operations.
 */
class CmsEventsService implements ICmsEventsService
{
    public function __construct(
        private readonly IEventRepository $eventRepository,
        private readonly IEventSessionRepository $sessionRepository,
        private readonly IEventSessionLabelRepository $labelRepository,
        private readonly IEventSessionPriceRepository $priceRepository,
        private readonly IEventTypeRepository $eventTypeRepository,
        private readonly IVenueRepository $venueRepository,
        private readonly IPriceTierRepository $priceTierRepository,
        private readonly IScheduleDayConfigRepository $scheduleDayConfigRepository,
        private readonly IOrderItemRepository $orderItemRepository,
    ) {
    }

    /**
     * Gets all events with details for listing, with optional filtering.
     *
     * @param int|null $eventTypeId Filter by event type
     * @param string|null $dayOfWeek Filter by day (e.g., 'Monday')
     */
    public function getAllEventsWithDetails(?int $eventTypeId = null, ?string $dayOfWeek = null): array
    {
        return $this->eventRepository->findEvents(new EventFilter(
            isActive: true,
            includeSessionCount: true,
            eventTypeId: $eventTypeId,
            dayOfWeek: $dayOfWeek,
        ));
    }

    /**
     * Gets all event types for dropdown.
     *
     * @return \App\Models\EventType[]
     */
    public function getEventTypes(): array
    {
        return $this->eventTypeRepository->findEventTypes(new EventTypeFilter(orderBy: 'name'));
    }

    /**
     * Gets all venues for dropdown.
     *
     * @return \App\Models\Venue[]
     */
    public function getVenues(): array
    {
        return $this->venueRepository->findVenues(new VenueFilter(isActive: true));
    }

    /**
     * Assembles all data needed for the CMS events list page.
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
     * Gets weekly schedule overview for CMS.
     * Returns SessionWithEvent models grouped by day of week across all 7 days.
     *
     * @return array<string, \App\Models\SessionWithEvent[]>
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
     * @return array<string, \App\Models\SessionWithEvent[]>
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
     * @return array<string, \App\Models\SessionWithEvent[]>
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
    public function createEvent(array $data): int
    {
        $errors = $this->validateEventCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->eventRepository->create($data);
    }

    /**
     * Gets a single event with all related data for editing.
     * Returns null when the event does not exist.
     */
    public function getEventForEdit(int $eventId): ?EventEditBundle
    {
        $event = $this->eventRepository->findEvents(new EventFilter(
            eventId: $eventId,
            includeSessionCount: true,
        ))[0] ?? null;
        if (!$event) {
            return null;
        }

        $sessions = $this->sessionRepository->findSessions(new EventSessionFilter(
            eventId: $eventId,
            includeCancelled: true,
            orderBy: 'es.StartDateTime ASC',
        ))->sessions;

        [$pricesMap, $labelsMap] = $this->loadSessionPricesAndLabels($sessions);

        return new EventEditBundle(
            event: $event,
            sessions: $sessions,
            pricesMap: $pricesMap,
            labelsMap: $labelsMap,
        );
    }

    /**
     * @param \App\Models\SessionWithEvent[] $sessions
     * @return array{0: array, 1: array}
     */
    private function loadSessionPricesAndLabels(array $sessions): array
    {
        $sessionIds = array_map(
            static fn (\App\Models\SessionWithEvent $s): int => $s->eventSessionId,
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
    public function updateEvent(int $eventId, array $data): bool
    {
        $errors = $this->validateEvent($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->eventRepository->update($eventId, $data);
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

        return $this->sessionRepository->update($sessionId, $data);
    }

    /**
     * Deletes an event session.
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
    public function setSessionPrice(int $sessionId, int $priceTierId, float $price): bool
    {
        $errors = $this->validatePrice($priceTierId, $price);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->priceRepository->upsert($sessionId, $priceTierId, $price);
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
     * Validates event data for creation.
     */
    private function validateEventCreate(array $data): array
    {
        $errors = [];

        if (empty($data['Title'])) {
            $errors[] = 'Event title is required';
        }

        if (empty($data['EventTypeId'])) {
            $errors[] = 'Event type is required';
        }

        return $errors;
    }

    /**
     * Validates session data.
     */
    private function validateSession(array $data): array
    {
        $errors = [];

        // StartDateTime required
        if (empty($data['StartDateTime'])) {
            $errors[] = 'Start date/time is required';
        }

        // EndDateTime required
        if (empty($data['EndDateTime'])) {
            $errors[] = 'End date/time is required';
        }

        // EndDateTime must be after StartDateTime
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

        // CtaUrl validation
        if (!empty($data['CtaUrl'])) {
            $url = trim($data['CtaUrl']);
            $isAbsolute = filter_var($url, FILTER_VALIDATE_URL) !== false;
            $isRelative = str_starts_with($url, '/') || str_starts_with($url, '#');
            if (!$isAbsolute && !$isRelative) {
                $errors[] = 'CTA URL must be a valid URL or relative path (starting with / or #)';
            }
        }

        $errors = array_merge($errors, $this->validateCapacityTotal($data));
        $errors = array_merge($errors, $this->validateCapacitySingleTicketLimit($data));
        $errors = array_merge($errors, $this->validateDurationMinutes($data));
        $errors = array_merge($errors, $this->validateAgeRange($data));

        return $errors;
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
     * Validates a label.
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
     * Validates price data.
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
     * Deletes an event (soft delete - sets IsActive = 0).
     *
     * @throws ValidationException
     */
    public function deleteEvent(int $eventId): void
    {
        if (!$this->eventRepository->exists($eventId)) {
            throw new ValidationException(['Event not found']);
        }

        $this->eventRepository->softDelete($eventId);
        $this->eventRepository->deactivateSessions($eventId);
    }

    /**
     * Gets all schedule day visibility configurations.
     */
    public function getScheduleDayConfigs(): array
    {
        return $this->scheduleDayConfigRepository->findConfigs(new ScheduleDayConfigFilter(includeEventTypeName: true, orderBy: 'scope'));
    }

    /**
     * Gets schedule day configs grouped into global and type-specific buckets.
     */
    public function getGroupedScheduleDayConfigs(): GroupedScheduleDayConfigs
    {
        $dayConfigs = $this->getScheduleDayConfigs();
        $globalConfigs = [];
        $typeConfigs   = [];
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
     * Gets visible days for an event type.
     * Returns array of day numbers (0-6) that are visible.
     */
    public function getVisibleDays(?int $eventTypeId = null): array
    {
        $globalSettings = $this->loadGlobalDaySettings();
        $typeSettings = $this->loadTypeDaySettings($eventTypeId);

        return $this->mergeVisibilitySettings($globalSettings, $typeSettings);
    }

    /**
     * Loads global day visibility settings.
     */
    private function loadGlobalDaySettings(): array
    {
        $settings = [];
        foreach ($this->scheduleDayConfigRepository->findConfigs(new ScheduleDayConfigFilter(eventTypeId: 0, orderBy: 'day')) as $row) {
            $settings[$row->dayOfWeek] = $row->isVisible;
        }
        return $settings;
    }

    /**
     * Loads type-specific day visibility settings.
     */
    private function loadTypeDaySettings(?int $eventTypeId): array
    {
        if ($eventTypeId === null) {
            return [];
        }

        $settings = [];
        foreach ($this->scheduleDayConfigRepository->findConfigs(new ScheduleDayConfigFilter(eventTypeId: $eventTypeId, orderBy: 'day')) as $row) {
            $settings[$row->dayOfWeek] = $row->isVisible;
        }
        return $settings;
    }

    /**
     * Merges global and type settings to get visible days.
     */
    private function mergeVisibilitySettings(array $globalSettings, array $typeSettings): array
    {
        $visibleDays = [];
        foreach (DayOfWeek::cases() as $day) {
            $dayValue = $day->value;
            $isVisible = $typeSettings[$dayValue] ?? $globalSettings[$dayValue] ?? true;
            if ($isVisible) {
                $visibleDays[] = $dayValue;
            }
        }

        return $visibleDays;
    }
}
