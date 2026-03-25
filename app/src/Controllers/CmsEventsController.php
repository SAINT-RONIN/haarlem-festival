<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Enums\PriceTierId;
use App\Exceptions\ValidationException;
use App\Mappers\CmsEventsMapper;
use App\DTOs\Events\EventEditBundle;
use App\Services\Interfaces\ICmsArtistsService;
use App\Services\Interfaces\ICmsEventsService;
use App\Services\Interfaces\ICmsRestaurantsService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Cms\CmsEventCreateViewModel;
use App\ViewModels\Cms\CmsScheduleDaysViewModel;

/**
 * CMS controller for managing festival events.
 *
 * Handles full event CRUD, session (time-slot) management, label assignment,
 * per-session pricing, venue creation, and schedule-day visibility toggles.
 *
 * Events own Sessions (bookable time-slots), each session can carry Labels
 * (e.g. "Sold Out") and per-tier Prices (Adult, Child, etc.). The schedule-day
 * feature controls which days appear on the public schedule for each event type.
 *
 * Collaborates with ICmsArtistsService and ICmsRestaurantsService because the
 * event edit form needs artist/restaurant dropdowns for Jazz and Dining events.
 */
class CmsEventsController extends CmsBaseController
{
    public function __construct(
        private readonly ICmsEventsService $eventsService,
        ISessionService $sessionService,
        private readonly ICmsArtistsService $artistsService,
        private readonly ICmsRestaurantsService $restaurantsService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Displays the events list with optional event-type and day-of-week filters.
     * GET /cms/events
     */
    public function index(): void
    {
        try {
            $currentView = 'events';
            $viewModel = $this->buildEventsListViewModel();
            require __DIR__ . '/../Views/pages/cms/events.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Renders the event creation form, pre-loading available types, venues, artists, and restaurants.
     * GET /cms/events/create
     */
    public function create(): void
    {
        try {

            $currentView = 'events';
            $viewModel = new CmsEventCreateViewModel(
                eventTypes: $this->eventsService->getEventTypes(),
                venues: $this->eventsService->getVenues(),
                artists: $this->artistsService->getArtists(null),
                restaurants: $this->restaurantsService->getRestaurants(null),
                errorMessage: $this->sessionService->consumeFlash('error'),
                successMessage: $this->sessionService->consumeFlash('success'),
                preselectedDay: $_GET['day'] ?? '',
            );

            require __DIR__ . '/../Views/pages/cms/event-create.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Validates and persists a new event, then redirects to its edit page
     * so the admin can immediately add sessions and pricing.
     * POST /cms/events
     *
     * @throws ValidationException Redirects with error flash on validation failure.
     */
    public function store(): void
    {
        try {
            // Raw $_POST is passed to the service which handles extraction and validation
            $eventId = $this->eventsService->createEvent($_POST);
            $this->redirectWithFlash('Event created successfully.', 'success', "/cms/events/{$eventId}/edit");
        } catch (ValidationException $error) {
            $this->redirectWithValidationErrors($error, '/cms/events/create');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Renders the event edit page with sessions, prices, and labels.
     * GET /cms/events/{id}/edit
     */
    public function edit(string $id): void
    {
        try {
            $currentView = 'events';
            $editData = $this->loadEventEditData((int)$id);
            if ($editData === null) {
                return;
            }
            $this->renderEventEditPage($editData);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Validates and applies updates to an existing event.
     * POST /cms/events/{id}/edit
     */
    public function update(string $id): void
    {
        try {
            $eventId = (int)$id;
            $this->eventsService->updateEvent($eventId, $_POST);
            $this->redirectWithFlash('Event updated successfully.', 'success', "/cms/events/{$eventId}/edit");
        } catch (ValidationException $error) {
            $this->redirectWithValidationErrors($error, '/cms/events/' . (int)$id . '/edit');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Adds a new time-slot session to an event.
     * POST /cms/events/{eventId}/sessions
     */
    public function createSession(string $eventId): void
    {
        try {
            $eventIdInt = (int)$eventId;
            $this->eventsService->createSession($eventIdInt, $_POST);
            $this->redirectWithFlash('Session created successfully.', 'success', "/cms/events/{$eventIdInt}/edit");
        } catch (ValidationException $error) {
            $this->redirectWithValidationErrors($error, '/cms/events/' . (int)$eventId . '/edit');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Updates an existing session's details (capacity, times, etc.).
     * POST /cms/sessions/{id}/edit
     */
    public function updateSession(string $id): void
    {
        try {
            $eventId = $this->getEventIdFromPost();
            $this->eventsService->updateSession((int)$id, $_POST);
            $this->redirectWithFlash('Session updated successfully.', 'success', "/cms/events/{$eventId}/edit");
        } catch (ValidationException $error) {
            $this->redirectWithValidationErrors($error, '/cms/events/' . $this->getEventIdFromPost() . '/edit');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Removes a session from its parent event.
     * POST /cms/sessions/{id}/delete
     */
    public function deleteSession(string $id): void
    {
        try {
            $eventId = $this->getEventIdFromPost();
            $this->eventsService->deleteSession((int)$id);
            $this->redirectWithFlash('Session deleted successfully.', 'success', "/cms/events/{$eventId}/edit");
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Attaches a text label to a session (e.g. "Sold Out", "VIP").
     * POST /cms/sessions/{id}/labels
     */
    public function addLabel(string $id): void
    {
        try {
            $eventId = $this->getEventIdFromPost();
            $this->eventsService->addLabel((int)$id, trim($_POST['LabelText'] ?? ''));
            $this->redirectWithFlash('Label added successfully.', 'success', "/cms/events/{$eventId}/edit");
        } catch (ValidationException $error) {
            $this->redirectWithValidationErrors($error, '/cms/events/' . $this->getEventIdFromPost() . '/edit');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Removes a label from a session.
     * POST /cms/labels/{id}/delete
     */
    public function deleteLabel(string $id): void
    {
        try {
            $eventId = $this->getEventIdFromPost();
            $this->eventsService->deleteLabel((int)$id);
            $this->redirectWithFlash('Label deleted successfully.', 'success', "/cms/events/{$eventId}/edit");
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Sets or updates the ticket price for a session at a given price tier.
     * POST /cms/sessions/{id}/price
     */
    public function setPrice(string $id): void
    {
        try {
            $eventId = $this->getEventIdFromPost();
            $this->handleSetPrice((int)$id, $eventId);
        } catch (ValidationException $error) {
            $this->redirectWithValidationErrors($error, '/cms/events/' . $this->getEventIdFromPost() . '/edit');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Creates a new venue via AJAX and returns the new venue ID as JSON.
     * Called from the event-create form's inline "add venue" modal so the admin
     * doesn't have to leave the page.
     * POST /cms/venues
     *
     * @throws ValidationException Returns 400 JSON on validation failure.
     */
    public function createVenue(): void
    {
        try {
            $name = trim($_POST['VenueName'] ?? '');
            $addressLine = trim($_POST['AddressLine'] ?? '');
            $venueId = $this->eventsService->createVenue($name, $addressLine);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'venueId' => $venueId, 'name' => $name]);
            exit;
        } catch (ValidationException $error) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'errors' => $error->getErrors()]);
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error);
        }
    }

    /**
     * Deletes an event and all its associated sessions.
     * POST /cms/events/{id}/delete
     */
    public function delete(string $id): void
    {
        try {
            $this->eventsService->deleteEvent((int)$id);
            $this->redirectWithFlash('Event deleted successfully.', 'success', '/cms/events');
        } catch (ValidationException $error) {
            $this->redirectWithValidationErrors($error, '/cms/events');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Displays the schedule-day visibility configuration page.
     * GET /cms/schedule-days
     */
    public function scheduleDays(): void
    {
        try {
            $currentView = 'schedule-days';
            $this->renderScheduleDaysPage();
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Toggles a specific day's visibility on the public schedule.
     * Can be global (all event types) or scoped to a single event type,
     * depending on whether EventTypeId is posted as 0/null or a real ID.
     * POST /cms/schedule-days
     *
     * @throws ValidationException Redirects with error flash on validation failure.
     */
    public function toggleScheduleDay(): void
    {
        try {
            $this->handleToggleScheduleDay();
        } catch (ValidationException $error) {
            $this->redirectWithValidationErrors($error, '/cms/schedule-days');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    private function renderScheduleDaysPage(): void
    {
        $pageData  = $this->eventsService->getScheduleDaysPageData();
        $viewModel = new CmsScheduleDaysViewModel(
            eventTypes: $pageData->eventTypes,
            globalConfigs: $pageData->grouped->global,
            typeConfigs: $pageData->grouped->byType,
            successMessage: $this->sessionService->consumeFlash('success'),
            errorMessage: $this->sessionService->consumeFlash('error'),
        );
        require __DIR__ . '/../Views/pages/cms/schedule-days.php';
    }

    private function buildEventsListViewModel(): \App\ViewModels\Cms\CmsEventsListViewModel
    {
        $eventTypeId = isset($_GET['type']) && is_numeric($_GET['type']) ? (int)$_GET['type'] : null;
        $dayOfWeek = isset($_GET['day']) && $_GET['day'] !== '' ? $_GET['day'] : null;

        return CmsEventsMapper::toEventsListViewModel(
            $this->eventsService->getEventsListPageData($eventTypeId, $dayOfWeek),
            $_GET['type'] ?? '',
            $_GET['day'] ?? '',
            $this->sessionService->consumeFlash('success'),
            $this->sessionService->consumeFlash('error'),
        );
    }

    private function loadEventEditData(int $eventId): ?EventEditBundle
    {
        $editData = $this->eventsService->getEventForEdit($eventId);
        if ($editData === null) {
            http_response_code(404);
            require __DIR__ . '/../Views/pages/errors/404.php';
            return null;
        }
        return $editData;
    }

    private function renderEventEditPage(EventEditBundle $editData): void
    {
        $priceTiers  = $this->eventsService->getPriceTiers();
        $viewModel   = $this->buildEventEditViewModel($editData, $priceTiers);
        $artists     = $this->artistsService->getArtists(null);
        $restaurants = $this->restaurantsService->getRestaurants(null);
        require __DIR__ . '/../Views/pages/cms/event-edit.php';
    }

    private function buildEventEditViewModel(EventEditBundle $editData, array $priceTiers = []): \App\ViewModels\Cms\CmsEventEditViewModel
    {
        return CmsEventsMapper::toEventEditViewModel(
            $editData->event,
            $editData->sessions,
            $editData->pricesMap,
            $editData->labelsMap,
            $this->sessionService->consumeFlash('success'),
            $this->sessionService->consumeFlash('error'),
            $priceTiers,
        );
    }

    /** Defaults to the Adult price tier if none is specified in the form. */
    private function handleSetPrice(int $sessionId, int $eventId): void
    {
        $priceTierId = (int)($_POST['PriceTierId'] ?? PriceTierId::Adult->value);
        $this->eventsService->setSessionPrice($sessionId, $priceTierId, $_POST['Price'] ?? '0');
        $this->redirectWithFlash('Price updated successfully.', 'success', "/cms/events/{$eventId}/edit");
    }

    /**
     * Session/label/price forms include a hidden EventId field so the controller
     * can redirect back to the correct event edit page after the action.
     */
    private function getEventIdFromPost(): int
    {
        return (int)($_POST['EventId'] ?? 0);
    }

    private function handleToggleScheduleDay(): void
    {
        // null event type ID means this is a global (all-types) visibility toggle
        $rawEventTypeId = $_POST['EventTypeId'] ?? null;
        $eventTypeId = ($rawEventTypeId !== null && $rawEventTypeId !== '' && $rawEventTypeId !== '0')
            ? (int)$rawEventTypeId
            : null;
        $dayOfWeek = (int)($_POST['DayOfWeek'] ?? 0);
        $isVisible = (int)($_POST['IsVisible'] ?? 1);
        $this->eventsService->setScheduleDayVisibility($eventTypeId, $dayOfWeek, $isVisible === 1);
        $this->redirectWithFlash('Day visibility updated.', 'success', '/cms/schedule-days');
    }
}
