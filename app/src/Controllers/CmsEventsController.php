<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Enums\PriceTierId;
use App\Exceptions\ValidationException;
use App\Mappers\CmsEventsMapper;
use App\Services\Interfaces\ICmsArtistsService;
use App\Services\Interfaces\ICmsEventsService;
use App\Services\Interfaces\ISessionService;

class CmsEventsController
{
    public function __construct(
        private ICmsEventsService $eventsService,
        private readonly ISessionService $sessionService,
        private readonly ICmsArtistsService $artistsService,
    ) {
    }

    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);

            $currentView = 'events';
            $eventTypeId = isset($_GET['type']) && is_numeric($_GET['type']) ? (int)$_GET['type'] : null;
            $dayOfWeek = isset($_GET['day']) && $_GET['day'] !== '' ? $_GET['day'] : null;

            $eventsData = $this->eventsService->getAllEventsWithDetails($eventTypeId, $dayOfWeek);
            $eventTypes = $this->eventsService->getEventTypes();
            $weeklyScheduleDomain = $this->eventsService->getWeeklyScheduleOverview($eventTypeId);
            $venues = $this->eventsService->getVenues();

            $viewModel = CmsEventsMapper::toEventsListViewModel(
                $eventsData,
                $eventTypes,
                $venues,
                $weeklyScheduleDomain,
                $_GET['type'] ?? '',
                $_GET['day'] ?? '',
                $_GET['success'] ?? null,
                $_GET['error'] ?? null,
            );

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
            $errorMessage   = $_GET['error'] ?? null;
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
            $this->redirect("/cms/events/{$eventId}/edit?success=Event+created+successfully");
        } catch (ValidationException $error) {
            $this->redirect('/cms/events/create?error=' . urlencode(implode(', ', $error->getErrors())));
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function edit(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);

            $eventId = (int)$id;
            $currentView = 'events';
            $editData = $this->eventsService->getEventForEdit($eventId);

            if ($editData === null) {
                http_response_code(404);
                require __DIR__ . '/../Views/pages/errors/404.php';
                return;
            }

            $viewModel = CmsEventsMapper::toEventEditViewModel(
                $editData['event'],
                $editData['sessions'],
                $editData['pricesMap'],
                $editData['labelsMap'],
            );

            $priceTiers  = $this->eventsService->getPriceTiers();
            $artists     = $this->artistsService->getArtists(null);
            $successMessage = $_GET['success'] ?? null;
            $errorMessage = $_GET['error'] ?? null;

            require __DIR__ . '/../Views/pages/cms/event-edit.php';
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
            $this->redirect("/cms/events/{$eventId}/edit?success=Event+updated+successfully");
        } catch (ValidationException $error) {
            $eventId = (int)$id;
            $this->redirect("/cms/events/{$eventId}/edit?error=" . urlencode(implode(', ', $error->getErrors())));
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
            $this->redirect("/cms/events/{$eventIdInt}/edit?success=Session+created+successfully");
        } catch (ValidationException $error) {
            $eventIdInt = (int)$eventId;
            $this->redirect("/cms/events/{$eventIdInt}/edit?error=" . urlencode(implode(', ', $error->getErrors())));
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function updateSession(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $sessionId = (int)$id;
            $eventId = (int)($_POST['EventId'] ?? 0);
            $this->eventsService->updateSession($sessionId, $_POST);
            $this->redirect("/cms/events/{$eventId}/edit?success=Session+updated+successfully");
        } catch (ValidationException $error) {
            $eventId = (int)($_POST['EventId'] ?? 0);
            $this->redirect("/cms/events/{$eventId}/edit?error=" . urlencode(implode(', ', $error->getErrors())));
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function deleteSession(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $sessionId = (int)$id;
            $eventId = (int)($_POST['EventId'] ?? 0);
            $this->eventsService->deleteSession($sessionId);
            $this->redirect("/cms/events/{$eventId}/edit?success=Session+deleted+successfully");
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function addLabel(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $sessionId = (int)$id;
            $eventId = (int)($_POST['EventId'] ?? 0);
            $labelText = trim($_POST['LabelText'] ?? '');
            $this->eventsService->addLabel($sessionId, $labelText);
            $this->redirect("/cms/events/{$eventId}/edit?success=Label+added+successfully");
        } catch (ValidationException $error) {
            $eventId = (int)($_POST['EventId'] ?? 0);
            $this->redirect("/cms/events/{$eventId}/edit?error=" . urlencode(implode(', ', $error->getErrors())));
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function deleteLabel(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $labelId = (int)$id;
            $eventId = (int)($_POST['EventId'] ?? 0);
            $this->eventsService->deleteLabel($labelId);
            $this->redirect("/cms/events/{$eventId}/edit?success=Label+deleted+successfully");
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function setPrice(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $sessionId = (int)$id;
            $eventId = (int)($_POST['EventId'] ?? 0);
            $priceTierId = (int)($_POST['PriceTierId'] ?? PriceTierId::Adult->value);
            $priceInput = str_replace(',', '.', $_POST['Price'] ?? '0');
            $price = (float)$priceInput;
            $this->eventsService->setSessionPrice($sessionId, $priceTierId, $price);
            $this->redirect("/cms/events/{$eventId}/edit?success=Price+updated+successfully");
        } catch (ValidationException $error) {
            $eventId = (int)($_POST['EventId'] ?? 0);
            $this->redirect("/cms/events/{$eventId}/edit?error=" . urlencode(implode(', ', $error->getErrors())));
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
            $eventId = (int)$id;
            $this->eventsService->deleteEvent($eventId);
            $this->redirect('/cms/events?success=Event+deleted+successfully');
        } catch (ValidationException $error) {
            $this->redirect('/cms/events?error=' . urlencode(implode(', ', $error->getErrors())));
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function scheduleDays(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $currentView = 'schedule-days';
            $eventTypes = $this->eventsService->getEventTypes();
            $grouped      = $this->eventsService->getGroupedScheduleDayConfigs();
            $globalConfigs = $grouped['global'];
            $typeConfigs   = $grouped['byType'];

            $successMessage = $_GET['success'] ?? null;
            $errorMessage = $_GET['error'] ?? null;
            require __DIR__ . '/../Views/pages/cms/schedule-days.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function toggleScheduleDay(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $rawEventTypeId = $_POST['EventTypeId'] ?? null;
            $eventTypeId = ($rawEventTypeId !== null && $rawEventTypeId !== '' && $rawEventTypeId !== '0')
                ? (int)$rawEventTypeId
                : null;
            $dayOfWeek = (int)($_POST['DayOfWeek'] ?? 0);
            $isVisible = (int)($_POST['IsVisible'] ?? 1);
            $this->eventsService->setScheduleDayVisibility($eventTypeId, $dayOfWeek, $isVisible === 1);
            $this->redirect('/cms/schedule-days?success=Day+visibility+updated');
        } catch (ValidationException $error) {
            $this->redirect('/cms/schedule-days?error=' . urlencode(implode(', ', $error->getErrors())));
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    private function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}
