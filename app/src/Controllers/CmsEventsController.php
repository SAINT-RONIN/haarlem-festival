<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Enums\PriceTierId;
use App\Exceptions\ValidationException;
use App\Services\CmsEventsService;
use App\ViewModels\Cms\CmsEventListItemViewModel;
use App\ViewModels\Cms\CmsEventsListViewModel;

class CmsEventsController
{
    private CmsEventsService $eventsService;

    public function __construct()
    {
        $this->eventsService = new CmsEventsService();
    }

    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin();

            $currentView = 'events';
            $eventTypeId = isset($_GET['type']) && is_numeric($_GET['type']) ? (int)$_GET['type'] : null;
            $dayOfWeek = isset($_GET['day']) && $_GET['day'] !== '' ? $_GET['day'] : null;

            $eventsData = $this->eventsService->getAllEventsWithDetails($eventTypeId, $dayOfWeek);
            $eventTypes = $this->eventsService->getEventTypes();
            $weeklySchedule = $this->eventsService->getWeeklyScheduleOverview($eventTypeId);
            $venues = $this->eventsService->getVenues();

            $events = array_map(
                static fn (array $event): CmsEventListItemViewModel => CmsEventListItemViewModel::fromArray($event),
                $eventsData
            );

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
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function create(): void
    {
        try {
            CmsAuthController::requireAdmin();

            $currentView = 'events';
            $eventTypes = $this->eventsService->getEventTypes();
            $venues = $this->eventsService->getVenues();
            $errorMessage = $_GET['error'] ?? null;
            $preselectedDay = $_GET['day'] ?? '';

            require __DIR__ . '/../Views/pages/cms/event-create.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function store(): void
    {
        try {
            CmsAuthController::requireAdmin();
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
            CmsAuthController::requireAdmin();

            $eventId = (int)$id;
            $currentView = 'events';
            $viewModel = $this->eventsService->getEventForEdit($eventId);

            if ($viewModel === null) {
                http_response_code(404);
                require __DIR__ . '/../Views/pages/errors/404.php';
                return;
            }

            $priceTiers = $this->eventsService->getPriceTiers();
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
            CmsAuthController::requireAdmin();
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
            CmsAuthController::requireAdmin();
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
            CmsAuthController::requireAdmin();
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
            CmsAuthController::requireAdmin();
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
            CmsAuthController::requireAdmin();
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
            CmsAuthController::requireAdmin();
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
            CmsAuthController::requireAdmin();
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
            CmsAuthController::requireAdmin();
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
            CmsAuthController::requireAdmin();
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
            CmsAuthController::requireAdmin();
            $currentView = 'schedule-days';
            $eventTypes = $this->eventsService->getEventTypes();
            $dayConfigs = $this->eventsService->getScheduleDayConfigs();
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
            CmsAuthController::requireAdmin();
            $eventTypeId = (int)($_POST['EventTypeId'] ?? 0);
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
