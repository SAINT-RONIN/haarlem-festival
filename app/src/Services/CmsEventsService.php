<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PriceTierId;
use App\Exceptions\ValidationException;
use App\Repositories\EventRepository;
use App\Repositories\EventSessionLabelRepository;
use App\Repositories\EventSessionPriceRepository;
use App\Repositories\EventSessionRepository;
use App\Repositories\EventTypeRepository;
use App\Repositories\PriceTierRepository;
use App\Repositories\ScheduleDayConfigRepository;
use App\Repositories\VenueRepository;
use App\Services\Interfaces\ICmsEventsService;
use App\ViewModels\Cms\CmsEventEditViewModel;
use App\ViewModels\Cms\CmsEventSessionViewModel;

/**
 * Service for CMS Events management.
 *
 * Contains business logic and validation for event/session CRUD operations.
 */
class CmsEventsService implements ICmsEventsService
{
    private EventRepository $eventRepository;
    private EventSessionRepository $sessionRepository;
    private EventSessionLabelRepository $labelRepository;
    private EventSessionPriceRepository $priceRepository;
    private EventTypeRepository $eventTypeRepository;
    private VenueRepository $venueRepository;
    private PriceTierRepository $priceTierRepository;
    private ScheduleDayConfigRepository $scheduleDayConfigRepository;

    private const MAX_LABELS_PER_SESSION = 6;
    private const MAX_LABEL_LENGTH = 60;

    public function __construct()
    {
        $this->eventRepository = new EventRepository();
        $this->sessionRepository = new EventSessionRepository();
        $this->labelRepository = new EventSessionLabelRepository();
        $this->priceRepository = new EventSessionPriceRepository();
        $this->eventTypeRepository = new EventTypeRepository();
        $this->venueRepository = new VenueRepository();
        $this->priceTierRepository = new PriceTierRepository();
        $this->scheduleDayConfigRepository = new ScheduleDayConfigRepository();
    }

    /**
     * Gets all events with details for listing, with optional filtering.
     *
     * @param int|null $eventTypeId Filter by event type
     * @param string|null $dayOfWeek Filter by day (e.g., 'Monday')
     */
    public function getAllEventsWithDetails(?int $eventTypeId = null, ?string $dayOfWeek = null): array
    {
        return $this->eventRepository->findAllWithDetailsFiltered($eventTypeId, $dayOfWeek);
    }

    /**
     * Gets all event types for dropdown.
     *
     * @return \App\Models\EventType[]
     */
    public function getEventTypes(): array
    {
        return $this->eventTypeRepository->findAllForDropdown();
    }

    /**
     * Gets all venues for dropdown.
     *
     * @return \App\Models\Venue[]
     */
    public function getVenues(): array
    {
        return $this->venueRepository->findAllForDropdown();
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
     * Returns sessions as ViewModels grouped by day of week across all 7 days.
     *
     * @return array<string, CmsEventSessionViewModel[]>
     */
    public function getWeeklyScheduleOverview(?int $eventTypeId = null): array
    {
        $schedule = $this->initializeWeekSchedule();
        $sessions = $this->sessionRepository->findWeeklyScheduleOverview($eventTypeId);

        return $this->groupSessionsByDayAsViewModels($sessions, $schedule);
    }

    /**
     * Initializes empty schedule array for all week days.
     *
     * @return array<string, CmsEventSessionViewModel[]>
     */
    private function initializeWeekSchedule(): array
    {
        $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        return array_fill_keys($weekDays, []);
    }

    /**
     * Groups sessions by their day of week as ViewModels.
     *
     * @return array<string, CmsEventSessionViewModel[]>
     */
    private function groupSessionsByDayAsViewModels(array $sessions, array $schedule): array
    {
        foreach ($sessions as $session) {
            $dayName = $session['DayOfWeek'];
            if (isset($schedule[$dayName])) {
                $schedule[$dayName][] = CmsEventSessionViewModel::fromArray($session);
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
     *
     * @return CmsEventEditViewModel|null
     */
    public function getEventForEdit(int $eventId): ?CmsEventEditViewModel
    {
        $event = $this->eventRepository->findByIdWithDetails($eventId);
        if (!$event) {
            return null;
        }

        $sessions = $this->sessionRepository->findByEventId($eventId);
        $sessionIds = array_column($sessions, 'EventSessionId');

        $labelsMap = [];
        $pricesMap = [];

        if (!empty($sessionIds)) {
            $labelsMap = $this->labelRepository->findBySessionIds($sessionIds);
            $pricesMap = $this->priceRepository->findBySessionIds($sessionIds);
        }

        return CmsEventEditViewModel::fromData($event, $sessions, $pricesMap, $labelsMap);
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
     */
    public function deleteSession(int $sessionId): bool
    {
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

        return $errors;
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

        if (strlen($labelText) > self::MAX_LABEL_LENGTH) {
            $errors[] = 'Label text must be ' . self::MAX_LABEL_LENGTH . ' characters or less';
        }

        // Check max labels per session
        $currentCount = $this->labelRepository->countBySession($sessionId);
        if ($currentCount >= self::MAX_LABELS_PER_SESSION) {
            $errors[] = 'Maximum ' . self::MAX_LABELS_PER_SESSION . ' labels per session';
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
        return $this->scheduleDayConfigRepository->findAll();
    }

    /**
     * Sets the visibility of a schedule day.
     *
     * @param int $eventTypeId 0 for global setting, >0 for specific event type
     * @param int $dayOfWeek 0=Sunday, 1=Monday, ..., 6=Saturday
     * @param bool $isVisible
     * @throws ValidationException
     */
    public function setScheduleDayVisibility(int $eventTypeId, int $dayOfWeek, bool $isVisible): void
    {
        if ($dayOfWeek < 0 || $dayOfWeek > 6) {
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
        foreach ($this->scheduleDayConfigRepository->findGlobalSettings() as $row) {
            $settings[$row['DayOfWeek']] = (bool)$row['IsVisible'];
        }
        return $settings;
    }

    /**
     * Loads type-specific day visibility settings.
     */
    private function loadTypeDaySettings(?int $eventTypeId): array
    {
        if ($eventTypeId === null || $eventTypeId <= 0) {
            return [];
        }

        $settings = [];
        foreach ($this->scheduleDayConfigRepository->findByEventTypeId($eventTypeId) as $row) {
            $settings[$row['DayOfWeek']] = (bool)$row['IsVisible'];
        }
        return $settings;
    }

    /**
     * Merges global and type settings to get visible days.
     */
    private function mergeVisibilitySettings(array $globalSettings, array $typeSettings): array
    {
        $visibleDays = [];
        for ($day = 0; $day <= 6; $day++) {
            $isVisible = $typeSettings[$day] ?? $globalSettings[$day] ?? true;
            if ($isVisible) {
                $visibleDays[] = $day;
            }
        }

        return $visibleDays;
    }
}
