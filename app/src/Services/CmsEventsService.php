<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Infrastructure\Database;
use App\Repositories\EventRepository;
use App\Repositories\EventSessionLabelRepository;
use App\Repositories\EventSessionPriceRepository;
use App\Repositories\EventSessionRepository;
use PDO;

/**
 * Service for CMS Events management.
 *
 * Contains business logic and validation for event/session CRUD operations.
 */
class CmsEventsService
{
    private EventRepository $eventRepository;
    private EventSessionRepository $sessionRepository;
    private EventSessionLabelRepository $labelRepository;
    private EventSessionPriceRepository $priceRepository;
    private PDO $pdo;

    private const MAX_LABELS_PER_SESSION = 6;
    private const MAX_LABEL_LENGTH = 60;
    private const PRICE_TIER_PAY_WHAT_YOU_LIKE = 5;

    public function __construct()
    {
        $this->eventRepository = new EventRepository();
        $this->sessionRepository = new EventSessionRepository();
        $this->labelRepository = new EventSessionLabelRepository();
        $this->priceRepository = new EventSessionPriceRepository();
        $this->pdo = Database::getConnection();
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
     */
    public function getEventTypes(): array
    {
        $stmt = $this->pdo->query('SELECT EventTypeId, Name, Slug FROM EventType ORDER BY Name ASC');
        return $stmt->fetchAll();
    }

    /**
     * Gets all venues for dropdown.
     */
    public function getVenues(): array
    {
        $stmt = $this->pdo->query('SELECT VenueId, Name, AddressLine FROM Venue WHERE IsActive = 1 ORDER BY Name ASC');
        return $stmt->fetchAll();
    }

    /**
     * Creates a new venue.
     *
     * @throws ValidationException
     */
    public function createVenue(string $name, string $addressLine): int
    {
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Venue name is required';
        }

        if (strlen($name) > 120) {
            $errors[] = 'Venue name must be 120 characters or less';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO Venue (Name, AddressLine, City, IsActive)
            VALUES (:name, :addressLine, :city, 1)
        ');
        $stmt->execute([
            'name' => $name,
            'addressLine' => $addressLine ?: '',
            'city' => 'Haarlem',
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Gets all price tiers for dropdown.
     */
    public function getPriceTiers(): array
    {
        $stmt = $this->pdo->query('SELECT PriceTierId, Name FROM PriceTier ORDER BY PriceTierId ASC');
        return $stmt->fetchAll();
    }

    /**
     * Gets weekly schedule overview for CMS.
     * Returns events grouped by day of week across all 7 days.
     */
    public function getWeeklyScheduleOverview(?int $eventTypeId = null): array
    {
        $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $schedule = [];

        foreach ($weekDays as $day) {
            $schedule[$day] = [];
        }

        $typeFilter = $eventTypeId ? 'AND e.EventTypeId = :eventTypeId' : '';

        $stmt = $this->pdo->prepare("
            SELECT 
                es.EventSessionId,
                es.EventId,
                es.StartDateTime,
                es.EndDateTime,
                es.CapacityTotal,
                es.SoldSingleTickets,
                es.SoldReservedSeats,
                DAYNAME(es.StartDateTime) AS DayOfWeek,
                DATE(es.StartDateTime) AS SessionDate,
                e.Title AS EventTitle,
                e.EventTypeId,
                et.Name AS EventTypeName,
                et.Slug AS EventTypeSlug,
                v.Name AS VenueName
            FROM EventSession es
            INNER JOIN Event e ON es.EventId = e.EventId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            WHERE es.IsActive = 1
              AND es.IsCancelled = 0
              AND e.IsActive = 1
              {$typeFilter}
            ORDER BY es.StartDateTime ASC
        ");

        if ($eventTypeId) {
            $stmt->execute(['eventTypeId' => $eventTypeId]);
        } else {
            $stmt->execute();
        }

        $sessions = $stmt->fetchAll();

        foreach ($sessions as $session) {
            $dayName = $session['DayOfWeek'];
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
     */
    public function getEventForEdit(int $eventId): ?array
    {
        $event = $this->eventRepository->findByIdWithDetails($eventId);
        if (!$event) {
            return null;
        }

        $sessions = $this->sessionRepository->findByEventId($eventId);
        $sessionIds = array_column($sessions, 'EventSessionId');

        $labelsMap = !empty($sessionIds)
            ? $this->labelRepository->findBySessionIds($sessionIds)
            : [];
        $pricesMap = !empty($sessionIds)
            ? $this->priceRepository->findBySessionIds($sessionIds)
            : [];

        // Attach labels and prices to each session
        foreach ($sessions as &$session) {
            $sid = (int)$session['EventSessionId'];
            $session['labels'] = $labelsMap[$sid] ?? [];
            $session['prices'] = $pricesMap[$sid] ?? [];
        }

        return [
            'event' => $event,
            'sessions' => $sessions,
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
        if ($priceTierId === self::PRICE_TIER_PAY_WHAT_YOU_LIKE && $price > 0) {
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
        // Verify event exists
        $stmt = $this->pdo->prepare('SELECT EventId FROM Event WHERE EventId = :eventId');
        $stmt->execute(['eventId' => $eventId]);
        if (!$stmt->fetch()) {
            throw new ValidationException(['Event not found']);
        }

        // Soft delete the event
        $stmt = $this->pdo->prepare('UPDATE Event SET IsActive = 0 WHERE EventId = :eventId');
        $stmt->execute(['eventId' => $eventId]);

        // Also deactivate all sessions for this event
        $stmt = $this->pdo->prepare('UPDATE EventSession SET IsActive = 0 WHERE EventId = :eventId');
        $stmt->execute(['eventId' => $eventId]);
    }

    /**
     * Gets all schedule day visibility configurations.
     */
    public function getScheduleDayConfigs(): array
    {
        $stmt = $this->pdo->query('
            SELECT 
                sdc.ScheduleDayConfigId,
                sdc.EventTypeId,
                sdc.DayOfWeek,
                sdc.IsVisible,
                et.Name AS EventTypeName
            FROM ScheduleDayConfig sdc
            LEFT JOIN EventType et ON sdc.EventTypeId = et.EventTypeId
            ORDER BY sdc.EventTypeId IS NULL DESC, sdc.EventTypeId, sdc.DayOfWeek
        ');
        return $stmt->fetchAll();
    }

    /**
     * Sets the visibility of a schedule day.
     *
     * @param int|null $eventTypeId NULL for global setting
     * @param int $dayOfWeek 0=Sunday, 1=Monday, ..., 6=Saturday
     * @param bool $isVisible
     * @throws ValidationException
     */
    public function setScheduleDayVisibility(?int $eventTypeId, int $dayOfWeek, bool $isVisible): void
    {
        if ($dayOfWeek < 0 || $dayOfWeek > 6) {
            throw new ValidationException(['Invalid day of week']);
        }

        // Upsert the visibility setting
        $stmt = $this->pdo->prepare('
            INSERT INTO ScheduleDayConfig (EventTypeId, DayOfWeek, IsVisible)
            VALUES (:eventTypeId, :dayOfWeek, :isVisible)
            ON DUPLICATE KEY UPDATE IsVisible = :isVisible2
        ');
        $stmt->execute([
            'eventTypeId' => $eventTypeId,
            'dayOfWeek' => $dayOfWeek,
            'isVisible' => $isVisible ? 1 : 0,
            'isVisible2' => $isVisible ? 1 : 0,
        ]);
    }

    /**
     * Gets visible days for an event type.
     * Returns array of day numbers (0-6) that are visible.
     */
    public function getVisibleDays(?int $eventTypeId = null): array
    {
        // Get global settings first
        $stmt = $this->pdo->prepare('
            SELECT DayOfWeek, IsVisible
            FROM ScheduleDayConfig
            WHERE EventTypeId IS NULL
        ');
        $stmt->execute();
        $globalSettings = [];
        foreach ($stmt->fetchAll() as $row) {
            $globalSettings[$row['DayOfWeek']] = (bool)$row['IsVisible'];
        }

        // If event type specified, get type-specific overrides
        $typeSettings = [];
        if ($eventTypeId !== null) {
            $stmt = $this->pdo->prepare('
                SELECT DayOfWeek, IsVisible
                FROM ScheduleDayConfig
                WHERE EventTypeId = :eventTypeId
            ');
            $stmt->execute(['eventTypeId' => $eventTypeId]);
            foreach ($stmt->fetchAll() as $row) {
                $typeSettings[$row['DayOfWeek']] = (bool)$row['IsVisible'];
            }
        }

        // Merge: type-specific overrides global
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

