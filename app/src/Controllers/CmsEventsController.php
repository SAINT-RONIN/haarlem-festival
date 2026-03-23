<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Enums\PriceTierId;
use App\Exceptions\ValidationException;
use App\Mappers\CmsEventsMapper;
use App\Models\EventEditBundle;
use App\Services\Interfaces\ICmsArtistsService;
use App\Services\Interfaces\ICmsEventsService;
use App\Services\Interfaces\ICmsRestaurantsService;
use App\Services\Interfaces\ISessionService;

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

    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $currentView = 'events';
            $viewModel = $this->buildEventsListViewModel();
            require __DIR__ . '/../Views/pages/cms/events.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function create(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);

            $currentView    = 'events';
            $eventTypes     = $this->eventsService->getEventTypes();
            $venues         = $this->eventsService->getVenues();
            $artists        = $this->artistsService->getArtists(null);
            $restaurants    = $this->restaurantsService->getRestaurants(null);
            $errorMessage   = $this->sessionService->consumeFlash('error');
            $preselectedDay = $_GET['day'] ?? '';

            require __DIR__ . '/../Views/pages/cms/event-create.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function store(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $eventId = $this->eventsService->createEvent($_POST);
            $this->redirectWithFlash('Event created successfully.', 'success', "/cms/events/{$eventId}/edit");
        } catch (ValidationException $error) {
            $this->redirectWithFlash(implode(', ', $error->getErrors()), 'error', '/cms/events/create');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function edit(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
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

    public function update(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $eventId = (int)$id;
            $this->eventsService->updateEvent($eventId, $_POST);
            $this->redirectWithFlash('Event updated successfully.', 'success', "/cms/events/{$eventId}/edit");
        } catch (ValidationException $error) {
            $this->redirectWithFlash(implode(', ', $error->getErrors()), 'error', '/cms/events/' . (int)$id . '/edit');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function createSession(string $eventId): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $eventIdInt = (int)$eventId;
            $this->eventsService->createSession($eventIdInt, $_POST);
            $this->redirectWithFlash('Session created successfully.', 'success', "/cms/events/{$eventIdInt}/edit");
        } catch (ValidationException $error) {
            $this->redirectWithFlash(implode(', ', $error->getErrors()), 'error', '/cms/events/' . (int)$eventId . '/edit');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function updateSession(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $eventId = (int)($_POST['EventId'] ?? 0);
            $this->eventsService->updateSession((int)$id, $_POST);
            $this->redirectWithFlash('Session updated successfully.', 'success', "/cms/events/{$eventId}/edit");
        } catch (ValidationException $error) {
            $this->redirectWithFlash(implode(', ', $error->getErrors()), 'error', '/cms/events/' . (int)($_POST['EventId'] ?? 0) . '/edit');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function deleteSession(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $eventId = (int)($_POST['EventId'] ?? 0);
            $this->eventsService->deleteSession((int)$id);
            $this->redirectWithFlash('Session deleted successfully.', 'success', "/cms/events/{$eventId}/edit");
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function addLabel(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $eventId = (int)($_POST['EventId'] ?? 0);
            $this->eventsService->addLabel((int)$id, trim($_POST['LabelText'] ?? ''));
            $this->redirectWithFlash('Label added successfully.', 'success', "/cms/events/{$eventId}/edit");
        } catch (ValidationException $error) {
            $this->redirectWithFlash(implode(', ', $error->getErrors()), 'error', '/cms/events/' . (int)($_POST['EventId'] ?? 0) . '/edit');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function deleteLabel(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $eventId = (int)($_POST['EventId'] ?? 0);
            $this->eventsService->deleteLabel((int)$id);
            $this->redirectWithFlash('Label deleted successfully.', 'success', "/cms/events/{$eventId}/edit");
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function setPrice(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $eventId = (int)($_POST['EventId'] ?? 0);
            $this->handleSetPrice((int)$id, $eventId);
        } catch (ValidationException $error) {
            $this->redirectWithFlash(implode(', ', $error->getErrors()), 'error', '/cms/events/' . (int)($_POST['EventId'] ?? 0) . '/edit');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function createVenue(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
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

    public function delete(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $this->eventsService->deleteEvent((int)$id);
            $this->redirectWithFlash('Event deleted successfully.', 'success', '/cms/events');
        } catch (ValidationException $error) {
            $this->redirectWithFlash(implode(', ', $error->getErrors()), 'error', '/cms/events');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function scheduleDays(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $currentView = 'schedule-days';
            $this->renderScheduleDaysPage();
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function toggleScheduleDay(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $this->handleToggleScheduleDay();
        } catch (ValidationException $error) {
            $this->redirectWithFlash(implode(', ', $error->getErrors()), 'error', '/cms/schedule-days');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    private function renderScheduleDaysPage(): void
    {
        $pageData       = $this->eventsService->getScheduleDaysPageData();
        $eventTypes     = $pageData->eventTypes;
        $globalConfigs  = $pageData->grouped->global;
        $typeConfigs    = $pageData->grouped->byType;
        $successMessage = $this->sessionService->consumeFlash('success');
        $errorMessage   = $this->sessionService->consumeFlash('error');
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

    private function handleSetPrice(int $sessionId, int $eventId): void
    {
        $priceTierId = (int)($_POST['PriceTierId'] ?? PriceTierId::Adult->value);
        $priceInput = str_replace(',', '.', $_POST['Price'] ?? '0');
        $this->eventsService->setSessionPrice($sessionId, $priceTierId, (float)$priceInput);
        $this->redirectWithFlash('Price updated successfully.', 'success', "/cms/events/{$eventId}/edit");
    }

    private function handleToggleScheduleDay(): void
    {
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
