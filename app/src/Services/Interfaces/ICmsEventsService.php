<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Exceptions\ValidationException;

/**
 * Interface for CMS Events management service.
 */
interface ICmsEventsService
{
    /**
     * Gets all events with details for listing.
     *
     * @param int|null $eventTypeId Filter by event type
     * @param string|null $dayOfWeek Filter by day
     * @return array Events with details
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
     *
     * @return array{event: \App\Models\EventWithDetails, sessions: \App\Models\SessionWithEvent[], pricesMap: array, labelsMap: array}|null
     */
    public function getEventForEdit(int $eventId): ?array;

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
     * Deletes an event session.
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
    public function setSessionPrice(int $sessionId, int $priceTierId, float $price): bool;

    /**
     * Deletes an event (soft delete).
     *
     * @throws ValidationException
     */
    public function deleteEvent(int $eventId): void;

    /**
     * Gets all schedule day visibility configurations.
     */
    public function getScheduleDayConfigs(): array;

    /**
     * Gets schedule day configs grouped into 'global' and 'byType' buckets.
     *
     * @return array{global: array<int, mixed>, byType: array<int, array<int, mixed>>}
     */
    public function getGroupedScheduleDayConfigs(): array;

    /**
     * Sets the visibility of a schedule day.
     *
     * @throws ValidationException
     */
    public function setScheduleDayVisibility(?int $eventTypeId, int $dayOfWeek, bool $isVisible): void;

    /**
     * Gets visible days for an event type.
     */
    public function getVisibleDays(?int $eventTypeId = null): array;
}
