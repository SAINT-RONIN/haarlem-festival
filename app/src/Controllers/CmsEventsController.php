<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Services\CmsEventsService;
use App\ViewModels\Cms\CmsEventsListViewModel;

/**
 * Controller for CMS Events management.
 *
 * Handles HTTP requests for event and session CRUD operations.
 * Thin controller - delegates business logic to CmsEventsService.
 */
class CmsEventsController
{
    private CmsEventsService $eventsService;

    public function __construct()
    {
        $this->eventsService = new CmsEventsService();
    }

    /**
     * Lists all events with weekly schedule overview.
     * GET /cms/events
     */
    public function index(): void
    {
        CmsAuthController::requireAdmin();

        $currentView = 'events';

        // Get filter parameters
        $eventTypeId = isset($_GET['type']) && is_numeric($_GET['type']) ? (int)$_GET['type'] : null;
        $dayOfWeek = isset($_GET['day']) && !empty($_GET['day']) ? $_GET['day'] : null;

        // Get data from service
        $events = $this->eventsService->getAllEventsWithDetails($eventTypeId, $dayOfWeek);
        $eventTypes = $this->eventsService->getEventTypes();
        $weeklySchedule = $this->eventsService->getWeeklyScheduleOverview($eventTypeId);
        $venues = $this->eventsService->getVenues();

        // Build ViewModel
        $viewModel = new CmsEventsListViewModel(
            events: $events,
            eventTypes: $eventTypes,
            venues: $venues,
            weeklySchedule: $weeklySchedule,
            selectedType: $_GET['type'] ?? '',
            selectedDay: $_GET['day'] ?? '',
            successMessage: $_GET['success'] ?? null,
            errorMessage: $_GET['error'] ?? null,
        );

        require __DIR__ . '/../Views/pages/cms/events.php';
    }

    /**
     * Shows create event form.
     * GET /cms/events/create
     */
    public function create(): void
    {
        CmsAuthController::requireAdmin();

        $currentView = 'events';
        $eventTypes = $this->eventsService->getEventTypes();
        $venues = $this->eventsService->getVenues();
        $errorMessage = $_GET['error'] ?? null;
        $preselectedDay = $_GET['day'] ?? '';

        require __DIR__ . '/../Views/pages/cms/event-create.php';
    }

    /**
     * Stores a new event.
     * POST /cms/events
     */
    public function store(): void
    {
        CmsAuthController::requireAdmin();

        try {
            $eventId = $this->eventsService->createEvent($_POST);
            $this->redirect("/cms/events/{$eventId}/edit?success=Event+created+successfully");
        } catch (ValidationException $e) {
            $this->redirect("/cms/events/create?error=" . urlencode(implode(', ', $e->getErrors())));
        }
    }

    /**
     * Shows edit form for an event.
     * GET /cms/events/{id}/edit
     */
    public function edit(string $id): void
    {
        CmsAuthController::requireAdmin();

        $eventId = (int)$id;
        $currentView = 'events';
        $viewModel = $this->eventsService->getEventForEdit($eventId);

        if (!$viewModel) {
            http_response_code(404);
            echo 'Event not found';
            return;
        }

        $priceTiers = $this->eventsService->getPriceTiers();
        $successMessage = $_GET['success'] ?? null;
        $errorMessage = $_GET['error'] ?? null;

        require __DIR__ . '/../Views/pages/cms/event-edit.php';
    }

    /**
     * Updates an event.
     * POST /cms/events/{id}/edit
     */
    public function update(string $id): void
    {
        CmsAuthController::requireAdmin();

        $eventId = (int)$id;

        try {
            $this->eventsService->updateEvent($eventId, $_POST);
            $this->redirect("/cms/events/{$eventId}/edit?success=Event+updated+successfully");
        } catch (ValidationException $e) {
            $this->redirect("/cms/events/{$eventId}/edit?error=" . urlencode(implode(', ', $e->getErrors())));
        }
    }

    /**
     * Creates a new session for an event.
     * POST /cms/events/{eventId}/sessions
     */
    public function createSession(string $eventId): void
    {
        CmsAuthController::requireAdmin();

        $eventIdInt = (int)$eventId;

        try {
            $this->eventsService->createSession($eventIdInt, $_POST);
            $this->redirect("/cms/events/{$eventIdInt}/edit?success=Session+created+successfully");
        } catch (ValidationException $e) {
            $this->redirect("/cms/events/{$eventIdInt}/edit?error=" . urlencode(implode(', ', $e->getErrors())));
        }
    }

    /**
     * Updates a session.
     * POST /cms/sessions/{id}
     */
    public function updateSession(string $id): void
    {
        CmsAuthController::requireAdmin();

        $sessionId = (int)$id;
        $eventId = (int)($_POST['EventId'] ?? 0);

        try {
            $this->eventsService->updateSession($sessionId, $_POST);
            $this->redirect("/cms/events/{$eventId}/edit?success=Session+updated+successfully");
        } catch (ValidationException $e) {
            $this->redirect("/cms/events/{$eventId}/edit?error=" . urlencode(implode(', ', $e->getErrors())));
        }
    }

    /**
     * Deletes a session.
     * POST /cms/sessions/{id}/delete
     */
    public function deleteSession(string $id): void
    {
        CmsAuthController::requireAdmin();

        $sessionId = (int)$id;
        $eventId = (int)($_POST['EventId'] ?? 0);

        $this->eventsService->deleteSession($sessionId);
        $this->redirect("/cms/events/{$eventId}/edit?success=Session+deleted+successfully");
    }

    /**
     * Adds a label to a session.
     * POST /cms/sessions/{id}/labels
     */
    public function addLabel(string $id): void
    {
        CmsAuthController::requireAdmin();

        $sessionId = (int)$id;
        $eventId = (int)($_POST['EventId'] ?? 0);
        $labelText = trim($_POST['LabelText'] ?? '');

        try {
            $this->eventsService->addLabel($sessionId, $labelText);
            $this->redirect("/cms/events/{$eventId}/edit?success=Label+added+successfully");
        } catch (ValidationException $e) {
            $this->redirect("/cms/events/{$eventId}/edit?error=" . urlencode(implode(', ', $e->getErrors())));
        }
    }

    /**
     * Deletes a label.
     * POST /cms/labels/{id}/delete
     */
    public function deleteLabel(string $id): void
    {
        CmsAuthController::requireAdmin();

        $labelId = (int)$id;
        $eventId = (int)($_POST['EventId'] ?? 0);

        $this->eventsService->deleteLabel($labelId);
        $this->redirect("/cms/events/{$eventId}/edit?success=Label+deleted+successfully");
    }

    /**
     * Sets/updates a session price.
     * POST /cms/sessions/{id}/price
     */
    public function setPrice(string $id): void
    {
        CmsAuthController::requireAdmin();

        $sessionId = (int)$id;
        $eventId = (int)($_POST['EventId'] ?? 0);
        $priceTierId = (int)($_POST['PriceTierId'] ?? 1);

        // Handle both comma and dot as decimal separator
        $priceInput = $_POST['Price'] ?? '0';
        $priceInput = str_replace(',', '.', $priceInput);
        $price = (float)$priceInput;

        try {
            $this->eventsService->setSessionPrice($sessionId, $priceTierId, $price);
            $this->redirect("/cms/events/{$eventId}/edit?success=Price+updated+successfully");
        } catch (ValidationException $e) {
            $this->redirect("/cms/events/{$eventId}/edit?error=" . urlencode(implode(', ', $e->getErrors())));
        }
    }

    /**
     * Creates a new venue.
     * POST /cms/venues
     */
    public function createVenue(): void
    {
        CmsAuthController::requireAdmin();

        $name = trim($_POST['VenueName'] ?? '');
        $addressLine = trim($_POST['AddressLine'] ?? '');

        try {
            $venueId = $this->eventsService->createVenue($name, $addressLine);
            // Return JSON for AJAX requests
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'venueId' => $venueId, 'name' => $name]);
            exit;
        } catch (ValidationException $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['success' => false, 'errors' => $e->getErrors()]);
            exit;
        }
    }

    /**
     * Deletes an event (soft delete - sets IsActive = 0).
     * POST /cms/events/{id}/delete
     */
    public function delete(string $id): void
    {
        CmsAuthController::requireAdmin();

        $eventId = (int)$id;

        try {
            $this->eventsService->deleteEvent($eventId);
            $this->redirect('/cms/events?success=Event+deleted+successfully');
        } catch (ValidationException $e) {
            $this->redirect('/cms/events?error=' . urlencode(implode(', ', $e->getErrors())));
        }
    }

    /**
     * Shows schedule day visibility management page.
     * GET /cms/schedule-days
     */
    public function scheduleDays(): void
    {
        CmsAuthController::requireAdmin();

        $currentView = 'schedule-days';
        $eventTypes = $this->eventsService->getEventTypes();
        $dayConfigs = $this->eventsService->getScheduleDayConfigs();
        $successMessage = $_GET['success'] ?? null;
        $errorMessage = $_GET['error'] ?? null;

        require __DIR__ . '/../Views/pages/cms/schedule-days.php';
    }

    /**
     * Toggles a schedule day visibility.
     * POST /cms/schedule-days/toggle
     */
    public function toggleScheduleDay(): void
    {
        CmsAuthController::requireAdmin();

        // EventTypeId: 0 = global, >0 = specific event type
        $eventTypeId = (int)($_POST['EventTypeId'] ?? 0);
        $dayOfWeek = (int)($_POST['DayOfWeek'] ?? 0);
        $isVisible = (int)($_POST['IsVisible'] ?? 1);

        try {
            $this->eventsService->setScheduleDayVisibility($eventTypeId, $dayOfWeek, $isVisible === 1);
            $this->redirect('/cms/schedule-days?success=Day+visibility+updated');
        } catch (ValidationException $e) {
            $this->redirect('/cms/schedule-days?error=' . urlencode(implode(', ', $e->getErrors())));
        }
    }

    /**
     * Redirects to a URL.
     */
    private function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}

