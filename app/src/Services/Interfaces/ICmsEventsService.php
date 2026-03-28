<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Cms\EventSessionUpsertData;
use App\DTOs\Cms\EventUpsertData;
use App\Exceptions\ValidationException;
use App\DTOs\Events\EventEditBundle;
use App\DTOs\Events\EventsListPageData;

/**
 * Contract for CMS event lifecycle: CRUD for events, sessions, labels, and prices,
 * plus composite page-data assembly.
 *
 * Schedule day visibility is handled by ICmsScheduleDayService.
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
     * @return array<string, \App\DTOs\Schedule\SessionWithEvent[]>
     */
    public function getWeeklyScheduleOverview(?int $eventTypeId = null): array;

    /**
     * Creates a new event.
     *
     * @throws ValidationException
     */
    public function createEvent(EventUpsertData $data): int;

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
    public function updateEvent(int $eventId, EventUpsertData $data): bool;

    /**
     * Creates a new event session.
     *
     * @throws ValidationException
     */
    public function createSession(int $eventId, EventSessionUpsertData $data): int;

    /**
     * Updates an event session.
     *
     * @throws ValidationException
     */
    public function updateSession(int $sessionId, EventSessionUpsertData $data): bool;

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

}
