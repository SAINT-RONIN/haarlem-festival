<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Exceptions\ValidationException;
use App\Models\EventEditBundle;
use App\Models\EventsListPageData;
use App\Models\GroupedScheduleDayConfigs;
use App\Models\ScheduleDaysPageData;

/**
 * Contract for CMS event lifecycle: CRUD for events, sessions, labels, and prices,
 * plus schedule day visibility management and composite page-data assembly.
 */
interface ICmsEventsService
{
    /**
     * Returns active events enriched with session counts, for the CMS events list.
     *
     * @return \App\Models\Event[]
     */
    public function getAllEventsWithDetails(?int $eventTypeId = null, ?string $dayOfWeek = null): array;

    /**
     * Gets all event types for dropdown.
     */
    public function getEventTypes(): array;

    /**
     * Gets all venues for dropdown.
     */
    public function getVenues(): array;

    /**
     * Assembles all data needed for the CMS events list page.
     */
    public function getEventsListPageData(?int $eventTypeId = null, ?string $dayOfWeek = null): EventsListPageData;

    /**
     * Assembles all data needed for the CMS schedule days management page.
     */
    public function getScheduleDaysPageData(): ScheduleDaysPageData;

    /**
     * Creates a new venue.
     *
     * @throws ValidationException
     */
    public function createVenue(string $name, string $addressLine): int;

    /**
     * Gets all price tiers for dropdown.
     */
    public function getPriceTiers(): array;

    /**
     * Gets weekly schedule overview for CMS.
     * Returns SessionWithEvent models grouped by day name.
     *
     * @return array<string, \App\Models\SessionWithEvent[]>
     */
    public function getWeeklyScheduleOverview(?int $eventTypeId = null): array;

    /**
     * Creates a new event.
     *
     * @throws ValidationException
     */
    public function createEvent(array $data): int;

    /**
     * Gets a single event with all related data for editing.
     * Returns null when the event does not exist.
     */
    public function getEventForEdit(int $eventId): ?EventEditBundle;

    /**
     * Updates an event's basic information.
     *
     * @throws ValidationException
     */
    public function updateEvent(int $eventId, array $data): bool;

    /**
     * Creates a new event session.
     *
     * @throws ValidationException
     */
    public function createSession(int $eventId, array $data): int;

    /**
     * Updates an event session.
     *
     * @throws ValidationException
     */
    public function updateSession(int $sessionId, array $data): bool;

    /**
     * Hard-deletes an event session. Blocked if tickets have been sold for this session.
     *
     * @throws ValidationException
     */
    public function deleteSession(int $sessionId): bool;

    /**
     * Adds a label to a session.
     *
     * @throws ValidationException
     */
    public function addLabel(int $sessionId, string $labelText): int;

    /**
     * Deletes a label.
     */
    public function deleteLabel(int $labelId): bool;

    /**
     * Sets the price for a session.
     *
     * @throws ValidationException
     */
    public function setSessionPrice(int $sessionId, int $priceTierId, string $rawPrice): bool;

    /**
     * Soft-deletes an event and cascades deactivation to all its sessions.
     *
     * @throws ValidationException
     */
    public function deleteEvent(int $eventId): void;

    /**
     * Gets all schedule day visibility configurations.
     */
    public function getScheduleDayConfigs(): array;

    /**
     * Gets schedule day configs grouped into global and type-specific buckets.
     */
    public function getGroupedScheduleDayConfigs(): GroupedScheduleDayConfigs;

    /**
     * Sets the visibility of a schedule day.
     *
     * @throws ValidationException
     */
    public function setScheduleDayVisibility(?int $eventTypeId, int $dayOfWeek, bool $isVisible): void;

    /**
     * Returns the day-of-week numbers visible for a given event type,
     * merging global defaults with type-specific overrides.
     *
     * @return int[] Day numbers (0=Sunday through 6=Saturday)
     */
    public function getVisibleDays(?int $eventTypeId = null): array;
}
