<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DayOfWeek;
use App\Enums\PriceTierId;
use App\Helpers\FormatHelper;
use App\DTOs\Cms\EventSessionUpsertData;
use App\Exceptions\CmsOperationException;
use App\Exceptions\ValidationException;
use App\Repositories\Interfaces\ICmsRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IEventSessionLabelRepository;
use App\Repositories\Interfaces\IEventSessionPriceRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IEventTypeRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Repositories\Interfaces\IPriceTierRepository;
use App\Repositories\Interfaces\IVenueRepository;
use App\DTOs\Cms\EventUpsertData;
use App\DTOs\Events\EventEditBundle;
use App\DTOs\Filters\EventFilter;
use App\DTOs\Filters\EventSessionFilter;
use App\DTOs\Events\EventsListPageData;
use App\DTOs\Filters\EventTypeFilter;
use App\DTOs\Filters\VenueFilter;
use App\Services\Interfaces\ICmsEventsService;
use App\Constants\CmsEventConstraints;

/**
 * CMS-side event and session management: CRUD, pricing, and labels.
 *
 * Extends BaseCmsEventsService which holds the restaurant CMS integration helpers
 * (per-event CMS sections, restaurant metadata items, detail-page editor URLs, and
 * slug uniqueness). This class owns all public interface methods and the session,
 * label, price, and event validation logic.
 *
 * Key design decisions:
 * - Events are soft-deleted (IsActive = 0) so historical order references remain valid.
 * - Sessions with sold tickets cannot be hard-deleted; they must be cancelled instead.
 * - Price input accepts European comma-decimal format (e.g. "12,50") and normalises to float.
 * - Schedule day visibility is handled separately by CmsScheduleDayService.
 */
class CmsEventsService extends BaseCmsEventsService implements ICmsEventsService
{
    /**
     * @param \PDO                          $pdo               Used only by deleteEvent's transaction.
     * @param IEventRepository              $eventRepository   Passed to parent; also used locally.
     * @param IEventSessionRepository       $sessionRepository Session CRUD and filtering.
     * @param IEventSessionLabelRepository  $labelRepository   Label CRUD per session.
     * @param IEventSessionPriceRepository  $priceRepository   Price upsert per session.
     * @param IEventTypeRepository          $eventTypeRepository Event type lookups for dropdowns.
     * @param IVenueRepository              $venueRepository   Passed to parent; also used locally.
     * @param IPriceTierRepository          $priceTierRepository Price tier lookups for dropdowns.
     * @param IOrderItemRepository          $orderItemRepository Used to guard session deletion.
     * @param ICmsRepository                $cmsRepository     Passed to parent for CMS section helpers.
     */
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
    ) {
        // Pass the three repositories that the base class helpers use.
        parent::__construct($eventRepository, $venueRepository, $cmsRepository);
    }

    /**
     * Returns active events enriched with session counts, for the CMS events list.
     *
     * Both filters are optional. When $eventTypeId is set, only events of that category are returned.
     * When $dayOfWeek is a day name (e.g. "Monday"), only events with sessions on that day are included.
     * Both filters can be combined to narrow results further.
     *
     * @return \App\Models\Event[]
     */
    public function getAllEventsWithDetails(?int $eventTypeId = null, ?string $dayOfWeek = null): array
    {
        $dayNumber = ($dayOfWeek !== null && $dayOfWeek !== '')
            // MySQL uses 1=Sunday, 2=Monday... which differs from PHP's date('N'). This helper converts a day name like "Monday" to the right MySQL number.
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
     * Returns all event types ordered alphabetically by name.
     *
     * Ordered by name for consistent display in dropdowns and filter menus.
     *
     * @return \App\Models\EventType[]
     */
    public function getEventTypes(): array
    {
        return $this->eventTypeRepository->findEventTypes(new EventTypeFilter(orderBy: 'name'));
    }

    /**
     * Returns all active venues for use in the event edit form.
     *
     * Only active venues are returned so deleted venues don't appear in the event edit form.
     *
     * @return \App\Models\Venue[]
     */
    public function getVenues(): array
    {
        return $this->venueRepository->findVenues(new VenueFilter(isActive: true));
    }

    /**
     * Assembles events, event types, venues, and weekly schedule into a single page payload,
     * so the controller only needs one service call to render the events list view.
     *
     * The weekly schedule is also filtered by event type when a filter is active, so the
     * grid only shows sessions relevant to the currently selected category.
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
     * Creates a new venue and returns its new ID.
     *
     * $addressLine is optional on the form but stored as empty string (never null)
     * so SQL comparisons on the address column stay consistent.
     *
     * @throws ValidationException When name validation fails
     */
    public function createVenue(string $name, string $addressLine): int
    {
        $errors = $this->validateVenueName($name);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        // The form may omit the address; store empty string instead of null to keep DB consistent.
        return $this->venueRepository->create($name, $addressLine ?: '');
    }

    /**
     * Validates the venue name and returns any error messages.
     *
     * The 120-character limit matches the DB column length so the error is caught here,
     * not at the database level with a cryptic truncation or constraint error.
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
     * Returns all price tiers (adult, group, pay-what-you-like, etc.) for the session price form.
     */
    public function getPriceTiers(): array
    {
        return $this->priceTierRepository->findAll();
    }

    /**
     * Builds a Monday-to-Sunday schedule grid keyed by day name.
     *
     * Each value is a list of sessions on that day. Days with no sessions have an empty array
     * so the grid always shows all seven days without null checks on the frontend.
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
     * Returns an array keyed by each day name (Monday through Sunday) with an empty list as the value.
     *
     * Initialising all seven keys upfront means days with no sessions still appear in the output
     * rather than being missing from the grid entirely.
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
     * Distributes sessions into the pre-filled schedule array, grouped by their day of week name.
     *
     * $schedule comes in with all seven keys pre-filled, so the result always contains all days
     * even if some remain empty after all sessions are assigned.
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
     * Creates a new event, assigns a unique slug, registers its CMS detail section,
     * and saves any restaurant-specific CMS fields. Returns the new event's ID.
     *
     * The slug is resolved to a unique value before the insert, and the EventUpsertData DTO is
     * rebuilt with the final slug so the correct value is stored. CMS section and restaurant items
     * are created as a follow-up to the event row so they can reference the new event ID.
     *
     * @throws ValidationException When validation fails
     * @throws CmsOperationException When the database write fails
     */
    public function createEvent(EventUpsertData $data): int
    {
        $errors = $this->validateEventCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $slug = $this->resolveUniqueSlug($data->slug ?? '');
        $data = $this->applyResolvedSlug($data, $slug);

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
     * Assembles a single event together with its sessions, prices, and labels
     * into an EventEditBundle for the CMS edit form. Returns null when the event does not exist.
     *
     * Sessions include cancelled ones so they can be re-activated from the edit view.
     * Prices and labels are loaded in bulk (two queries total) using all session IDs at once,
     * not one query per session.
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

        $restaurantCms = $this->loadRestaurantCmsItems($event->eventTypeId, $eventId);

        return new EventEditBundle(
            event: $event,
            sessions: $sessions,
            pricesMap: $pricesMap,
            labelsMap: $labelsMap,
            cmsDetailEditUrl: $this->resolveCmsDetailEditUrl($event->eventTypeId),
            restaurantStars: $restaurantCms['stars'],
            restaurantCuisine: $restaurantCms['cuisine'],
            restaurantShortDescription: $restaurantCms['shortDescription'],
        );
    }

    /**
     * Fetches prices and labels for all given sessions in two bulk queries and returns them as a pair.
     *
     * Prices and labels are fetched using all session IDs at once (one query each) rather than per
     * session, so the caller gets both maps without triggering N+1 queries.
     *
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
     * Updates an event's basic information and saves restaurant CMS fields.
     *
     * EventTypeId and slug are always taken from the existing record — the form doesn't post them,
     * and the slug is immutable once set. ArtistId falls back to the existing value if the form
     * didn't include one. Restaurant CMS items are saved after the main update.
     *
     * @throws ValidationException When validation fails or the event is not found
     * @throws CmsOperationException When the database write fails
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

        // Preserve type-specific FK columns that the edit form may not include,
        // and never overwrite the slug (it is set once at creation and then immutable).
        // The edit form does not post EventTypeId, so always use the existing value.
        $merged = new EventUpsertData(
            eventTypeId: $existing->eventTypeId,
            title: $data->title,
            shortDescription: $data->shortDescription,
            longDescriptionHtml: $data->longDescriptionHtml,
            featuredImageAssetId: $data->featuredImageAssetId,
            venueId: $data->venueId,
            artistId: $data->artistId ?? $existing->artistId,
            isActive: $data->isActive,
            slug: $existing->slug,
        );

        try {
            $result = $this->eventRepository->update($eventId, $merged);
            $this->saveRestaurantCmsItems($existing->eventTypeId, $eventId, $data);
            return $result;
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update event.', 0, $error);
        }
    }

    /**
     * Creates a new session for an event, applying capacity defaults after validation.
     *
     * Capacity fields default to 100 when not provided. The new session's ID is returned
     * so the caller can reference it immediately (e.g. for a redirect to the session edit page).
     *
     * @throws ValidationException When validation fails
     */
    public function createSession(int $eventId, EventSessionUpsertData $data): int
    {
        $errors = $this->validateSession($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $data = $this->applySessionDefaults($data, $eventId);

        return $this->sessionRepository->create($data);
    }

    /**
     * Updates an existing event session after validating the submitted changes.
     *
     * Follows the same validate → apply defaults → save pattern as createSession.
     *
     * @throws ValidationException When validation fails
     */
    public function updateSession(int $sessionId, EventSessionUpsertData $data): bool
    {
        $errors = $this->validateSession($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $data = $this->applySessionDefaults($data);

        return $this->sessionRepository->update($sessionId, $data);
    }

    /**
     * Hard-deletes an event session. Blocked if any order items reference this session.
     *
     * Sessions with sold tickets cannot be deleted because that would orphan the customer's
     * order history. The caller should cancel the session instead of deleting it.
     *
     * @throws ValidationException When tickets have already been sold for this session
     */
    public function deleteSession(int $sessionId): bool
    {
        // If any tickets have been sold for this session, deleting it would break the customer's order history.
        if ($this->orderItemRepository->existsForSession($sessionId)) {
            throw new ValidationException(['This session has sold tickets and cannot be deleted.']);
        }
        return $this->sessionRepository->delete($sessionId);
    }

    /**
     * Adds a short label tag to a session (e.g. "English", "Sold Out").
     *
     * Both the character length (CmsEventConstraints::MAX_LABEL_LENGTH) and the count per session
     * (CmsEventConstraints::MAX_LABELS_PER_SESSION) are capped. Both constants live in one place
     * so they are easy to adjust without hunting through validation code.
     *
     * @throws ValidationException When validation fails
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
     * Removes a label from a session. This is immediate and permanent — there is no undo.
     */
    public function deleteLabel(int $labelId): bool
    {
        return $this->labelRepository->delete($labelId);
    }

    /**
     * Sets the price for a session under a given price tier.
     *
     * The price tier defaults to Adult when none is provided, because most sessions only
     * have one price and it is registered under Adult. European comma notation (e.g. "12,50")
     * is normalised to a dot-decimal float before validation.
     *
     * @throws ValidationException When validation fails
     */
    public function setSessionPrice(int $sessionId, ?int $priceTierId, string $rawPrice): bool
    {
        $resolvedPriceTierId = $this->resolvePriceTierId($priceTierId);
        // Normalize comma decimal separators (e.g. "12,50" -> "12.50") for European input
        $price = (float) str_replace(',', '.', $rawPrice);
        $errors = $this->validatePrice($resolvedPriceTierId, $price);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->priceRepository->upsert($sessionId, $resolvedPriceTierId, $price);
    }

    /**
     * Returns the given price tier ID, or Adult as the default when none is provided.
     *
     * Adult is the default tier because most sessions only have one price and that price
     * is always registered under Adult. This avoids the caller having to know the default.
     */
    private function resolvePriceTierId(?int $priceTierId): int
    {
        return $priceTierId ?? PriceTierId::Adult->value;
    }

    /**
     * Validates the minimal fields required for both event create and update.
     *
     * Only the title is checked here because it is the only field that is always required.
     * Type-specific rules (like requiring an event type ID) are added by the caller.
     */
    private function validateEvent(EventUpsertData $data): array
    {
        $errors = [];

        if (trim($data->title) === '') {
            $errors[] = 'Event title is required';
        }

        return $errors;
    }

    /**
     * Validates event data for creation, extending the shared event rules with a type requirement.
     *
     * EventTypeId is only required at creation — updates preserve the existing type from the DB,
     * so this extra check is not needed in validateEvent().
     */
    private function validateEventCreate(EventUpsertData $data): array
    {
        $errors = $this->validateEvent($data);

        if ($data->eventTypeId <= 0) {
            $errors[] = 'Event type is required';
        }

        return $errors;
    }

    /**
     * Validates all session fields by delegating to focused validators for each concern.
     *
     * Validation is split into four groups: dates, CTA URL, numeric fields (capacity, duration),
     * and age range. Each group returns its own error array. All errors are collected together
     * rather than stopping at the first failure, so the user sees everything at once.
     */
    private function validateSession(EventSessionUpsertData $data): array
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

    /**
     * Validates that both date/time fields are present and that the end time is after the start.
     *
     * Both fields are required. End must be strictly after start — equal times are rejected
     * because a zero-duration session is almost certainly a data entry mistake.
     */
    private function validateSessionDates(EventSessionUpsertData $data): array
    {
        $errors = [];

        if ($data->startDateTime === '') {
            $errors[] = 'Start date/time is required';
        }

        if ($data->endDateTime === '') {
            $errors[] = 'End date/time is required';
        }

        if ($data->startDateTime !== '' && $data->endDateTime !== '') {
            $start = $this->parseSessionDateTime($data->startDateTime);
            $end = $this->parseSessionDateTime($data->endDateTime);

            if ($start === null || $end === null) {
                $errors[] = 'Invalid date/time format';
            } elseif ($end <= $start) {
                $errors[] = 'End time must be after start time';
            }
        }

        return $errors;
    }

    /**
     * Tries multiple datetime formats and returns a DateTimeImmutable, or null if none match.
     *
     * Three formats are tried in order because the HTML datetime-local input and the database
     * both use slightly different formats. The error-count check is needed because createFromFormat
     * can succeed but still have warnings for partial matches.
     */
    private function parseSessionDateTime(string $value): ?\DateTimeImmutable
    {
        foreach (['Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d H:i'] as $format) {
            // createFromFormat can partially parse invalid input, so we also inspect the parser errors.
            $dateTime = \DateTimeImmutable::createFromFormat($format, $value);
            $errors = \DateTimeImmutable::getLastErrors();

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
     * Validates that the CTA URL is either an absolute URL or a site-relative path.
     *
     * Absolute URLs (https://...) and relative paths starting with / or # are accepted.
     * A bare word or relative path without a leading slash would silently become a broken link,
     * so those are rejected with a clear error message.
     */
    private function validateSessionCtaUrl(EventSessionUpsertData $data): array
    {
        if ($data->ctaUrl === null || $data->ctaUrl === '') {
            return [];
        }

        $url = trim($data->ctaUrl);
        $isAbsolute = filter_var($url, FILTER_VALIDATE_URL) !== false;
        $isRelative = str_starts_with($url, '/') || str_starts_with($url, '#');

        if (!$isAbsolute && !$isRelative) {
            return ['CTA URL must be a valid URL or relative path (starting with / or #)'];
        }

        return [];
    }

    /**
     * Validates that CapacityTotal is a positive integer when provided.
     *
     * Null is allowed and means unlimited capacity. Zero is rejected because a session
     * that nobody can attend is almost certainly a data entry error.
     */
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

    /**
     * Validates that the per-booking ticket limit does not exceed the total capacity.
     *
     * A single-ticket limit that exceeds the total capacity is meaningless — a customer
     * could never actually buy that many tickets. Both fields must be set for this check to run.
     */
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

    /**
     * Validates that DurationMinutes is at least 1 minute when provided.
     *
     * Duration is optional — null means the duration is unknown and that is fine.
     * If a value is provided it must be at least 1 minute because a zero-duration session
     * would confuse the schedule display.
     */
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

    /**
     * Validates that the maximum age is not lower than the minimum age when both are set.
     *
     * Both bounds are individually optional. When both are provided, max cannot be lower
     * than min — an age range like "18 to 10" is a data entry mistake.
     */
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

    /**
     * Returns a new EventSessionUpsertData DTO with default values applied for unset fields.
     *
     * Capacity fields default to 100 when not provided — editors can change this, it is just
     * a reasonable starting point for a typical session. On create, the event ID comes from the
     * route parameter ($eventIdOverride), not the form. On update, it stays as-is from the DTO.
     */
    private function applySessionDefaults(EventSessionUpsertData $data, ?int $eventIdOverride = null): EventSessionUpsertData
    {
        return new EventSessionUpsertData(
            // On create, the event id comes from the route, not the form. On update, it stays as-is.
            eventId: $eventIdOverride ?? $data->eventId,
            startDateTime: $data->startDateTime,
            endDateTime: $data->endDateTime,
            // Default capacity to 100 — editors can change this; it's just a reasonable starting point.
            capacityTotal: $data->capacityTotal ?? 100,
            capacitySingleTicketLimit: $data->capacitySingleTicketLimit ?? 100,
            hallName: $data->hallName,
            sessionType: $data->sessionType,
            durationMinutes: $data->durationMinutes,
            languageCode: $data->languageCode,
            minAge: $data->minAge,
            maxAge: $data->maxAge,
            reservationRequired: $data->reservationRequired,
            isFree: $data->isFree,
            notes: $data->notes,
            historyTicketLabel: $data->historyTicketLabel,
            ctaLabel: $data->ctaLabel,
            ctaUrl: $data->ctaUrl,
            isCancelled: $data->isCancelled,
            isActive: $data->isActive,
        );
    }

    /**
     * Validates the label text and checks that the session has not reached its label cap.
     *
     * Two limits apply: character length (CmsEventConstraints::MAX_LABEL_LENGTH) and count per
     * session (CmsEventConstraints::MAX_LABELS_PER_SESSION). Both constants live in one place
     * so they are easy to adjust.
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
     * Validates the price: must be non-negative, and PayWhatYouLike tier must have a zero price.
     *
     * Negative prices are not allowed. "Pay what you like" sessions must be stored as 0 because
     * the actual amount is collected separately at checkout, not set by the event manager.
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
     * Soft-deletes an event and deactivates all its sessions in a single transaction.
     *
     * The event is set to IsActive = 0 (not removed) so historical order references remain valid.
     * Both the event and its sessions must be updated together, which is why a transaction is used —
     * if either write fails, neither is committed.
     *
     * @throws ValidationException When the event does not exist
     * @throws CmsOperationException When the database write fails
     */
    public function deleteEvent(int $eventId): void
    {
        try {
            if (!$this->eventRepository->exists($eventId)) {
                throw new ValidationException(['Event not found']);
            }

            // Wrap in transaction — both writes must succeed or neither does
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

    /**
     * Soft-deletes a venue by setting its IsActive flag to 0.
     *
     * This is a soft delete — the venue record is hidden from dropdowns but not removed from the DB.
     * This preserves referential integrity for events that are already linked to this venue.
     */
    public function deleteVenue(int $venueId): bool
    {
        return $this->venueRepository->softDelete($venueId);
    }

}
