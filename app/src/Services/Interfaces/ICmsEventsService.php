<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Cms\EventSessionUpsertData;
use App\DTOs\Cms\EventUpsertData;
use App\DTOs\Domain\Events\EventEditPageData;
use App\DTOs\Domain\Events\EventsListPageData;
use App\DTOs\Domain\Events\EventWithDetails;
use App\DTOs\Domain\Schedule\SessionWithEvent;
use App\Exceptions\ValidationException;
use App\Models\EventType;
use App\Models\PriceTier;
use App\Models\Venue;

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
     * @return EventWithDetails[]
     */
    public function getAllEventsWithDetails(?int $eventTypeId = null, ?string $dayOfWeek = null): array;

    /**
     * Returns all event types ordered by name, for use in dropdowns.
     *
     * @return EventType[]
     */
    public function getEventTypes(): array;

    /**
     * Returns all active venues, for use in dropdowns.
     *
     * @return Venue[]
     */
    public function getVenues(): array;

    /**
     * Assembles all data needed to render the CMS events list page.
     */
    public function getEventsListPageData(?int $eventTypeId = null, ?string $dayOfWeek = null): EventsListPageData;

    /**
     * Creates a new venue and returns its ID.
     *
     * @throws ValidationException
     */
    public function createVenue(string $name, string $addressLine): int;

    /**
     * Returns all price tiers, for use in the session price form.
     *
     * @return PriceTier[]
     */
    public function getPriceTiers(): array;

    /**
     * Returns active sessions grouped by day name (Monday–Sunday) for the weekly schedule grid.
     *
     * @return array<string, SessionWithEvent[]>
     */
    public function getWeeklyScheduleOverview(?int $eventTypeId = null): array;

    /**
     * Creates a new event and returns its ID.
     *
     * @throws ValidationException
     */
    public function createEvent(EventUpsertData $data): int;

    /**
     * Returns a single event with all related data for the CMS edit form.
     * Returns null when the event does not exist.
     */
    public function getEventForEdit(int $eventId): ?EventEditPageData;

    /**
     * Updates an event's basic information.
     *
     * @throws ValidationException
     */
    public function updateEvent(int $eventId, EventUpsertData $data): bool;

    /**
     * Creates a new session for an event and returns its ID.
     *
     * @throws ValidationException
     */
    public function createSession(int $eventId, EventSessionUpsertData $data): int;

    /**
     * Updates an existing session.
     *
     * @throws ValidationException
     */
    public function updateSession(int $sessionId, EventSessionUpsertData $data): bool;

    /**
     * Hard-deletes a session. Blocked when tickets have already been sold for it.
     *
     * @throws ValidationException
     */
    public function deleteSession(int $sessionId): bool;

    /**
     * Adds a text label to a session (e.g. "English", "Sold Out").
     *
     * @throws ValidationException
     */
    public function addLabel(int $sessionId, string $labelText): int;

    /** Removes a label. This is immediate and permanent. */
    public function deleteLabel(int $labelId): bool;

    /**
     * Sets the price for a session under a given price tier.
     *
     * @throws ValidationException
     */
    public function setSessionPrice(int $sessionId, ?int $priceTierId, string $rawPrice): bool;

    /**
     * Soft-deletes an event and deactivates all its sessions in a single transaction.
     *
     * @throws ValidationException
     */
    public function deleteEvent(int $eventId): void;

    /** Soft-deletes a venue. */
    public function deleteVenue(int $venueId): bool;
}
