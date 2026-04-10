<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Mappers\CmsEventsInputMapper;
use App\Mappers\CmsEventsViewMapper;
use App\DTOs\Domain\Events\EventEditPageData;
use App\Services\Interfaces\ICmsArtistsService;
use App\Services\Interfaces\ICmsEventsService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Cms\CmsEventCreateViewModel;
use App\ViewModels\Cms\CmsEventsListViewModel;
use App\ViewModels\Cms\CmsEventEditViewModel;

/**
 * CMS controller for managing festival events, sessions, labels, and per-session pricing.
 *
 * Venue management is handled by CmsVenuesController.
 * Schedule-day visibility is handled by CmsScheduleDaysController.
 */
class CmsEventsController extends CmsBaseController
{
    public function __construct(
        private readonly ICmsEventsService $eventsService,
        ISessionService $sessionService,
        private readonly ICmsArtistsService $artistsService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'events';
            $viewModel = $this->buildEventsListViewModel();
            require __DIR__ . '/../Views/pages/cms/events.php';
        });
    }

    public function create(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'events';
            $viewModel = $this->buildCreateViewModel();
            require __DIR__ . '/../Views/pages/cms/event-create.php';
        });
    }

    // Redirects to the edit page so the admin can immediately add sessions and pricing.
    public function store(): void
    {
        $this->handleCmsValidationRequest(function (): void {
            $eventId = $this->eventsService->createEvent(CmsEventsInputMapper::fromEventPostRequest());
            $this->redirectWithFlash('Event created successfully.', 'success', "/cms/events/{$eventId}/edit");
        }, '/cms/events/create');
    }

    public function edit(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $currentView = 'events';
            $editData = $this->loadEventEditData($id);
            if ($editData === null) {
                return;
            }
            $this->renderEventEditPage($editData);
        });
    }

    public function update(int $id): void
    {
        $this->handleCmsValidationRequest(function () use ($id): void {
            $this->eventsService->updateEvent($id, CmsEventsInputMapper::fromEventPostRequest());
            $this->redirectWithFlash('Event updated successfully.', 'success', "/cms/events/{$id}/edit");
        }, '/cms/events/' . $id . '/edit');
    }

    public function createSession(int $eventId): void
    {
        $this->handleCmsValidationRequest(function () use ($eventId): void {
            $this->eventsService->createSession($eventId, CmsEventsInputMapper::fromSessionPostRequest($eventId));
            $this->redirectWithFlash('Session created successfully.', 'success', "/cms/events/{$eventId}/edit");
        }, '/cms/events/' . $eventId . '/edit');
    }

    public function updateSession(int $id): void
    {
        $this->handleCmsValidationRequest(function () use ($id): void {
            $eventId = $this->getEventIdFromPost();
            $this->eventsService->updateSession($id, CmsEventsInputMapper::fromSessionPostRequest());
            $this->redirectWithFlash('Session updated successfully.', 'success', "/cms/events/{$eventId}/edit");
        }, fn(): string => '/cms/events/' . $this->getEventIdFromPost() . '/edit');
    }

    public function deleteSession(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $eventId = $this->getEventIdFromPost();
            $this->eventsService->deleteSession($id);
            $this->redirectWithFlash('Session deleted successfully.', 'success', "/cms/events/{$eventId}/edit");
        });
    }

    public function addLabel(int $id): void
    {
        $this->handleCmsValidationRequest(function () use ($id): void {
            $eventId = $this->getEventIdFromPost();
            $this->eventsService->addLabel($id, $this->readStringPostParam('LabelText') ?? '');
            $this->redirectWithFlash('Label added successfully.', 'success', "/cms/events/{$eventId}/edit");
        }, fn(): string => '/cms/events/' . $this->getEventIdFromPost() . '/edit');
    }

    public function deleteLabel(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $eventId = $this->getEventIdFromPost();
            $this->eventsService->deleteLabel($id);
            $this->redirectWithFlash('Label deleted successfully.', 'success', "/cms/events/{$eventId}/edit");
        });
    }

    public function setPrice(int $id): void
    {
        $this->handleCmsValidationRequest(function () use ($id): void {
            $eventId = $this->getEventIdFromPost();
            $this->handleSetPrice($id, $eventId);
        }, fn(): string => '/cms/events/' . $this->getEventIdFromPost() . '/edit');
    }

    public function delete(int $id): void
    {
        $this->handleCmsValidationRequest(function () use ($id): void {
            $this->eventsService->deleteEvent($id);
            $this->redirectWithFlash('Event deleted successfully.', 'success', '/cms/events');
        }, '/cms/events');
    }

    /** Loads all dropdown data for the event creation form and maps it to a view model. */
    private function buildCreateViewModel(): CmsEventCreateViewModel
    {
        return CmsEventsViewMapper::toCreateViewModel(
            $this->eventsService->getEventTypes(),
            $this->eventsService->getVenues(),
            $this->artistsService->getArtists(null),
            $this->sessionService->consumeFlash('error'),
            $this->sessionService->consumeFlash('success'),
            $this->readStringQueryParam('day') ?? '',
        );
    }

    private function buildEventsListViewModel(): CmsEventsListViewModel
    {
        $eventTypeId = $this->readPositiveIntQueryParam('type');
        $dayOfWeek = $this->readStringQueryParam('day');

        return CmsEventsViewMapper::toEventsListViewModel(
            $this->eventsService->getEventsListPageData($eventTypeId, $dayOfWeek),
            $this->readStringQueryParam('type') ?? '',
            $this->readStringQueryParam('day') ?? '',
            $this->sessionService->consumeFlash('success'),
            $this->sessionService->consumeFlash('error'),
        );
    }

    private function loadEventEditData(int $eventId): ?EventEditPageData
    {
        $editData = $this->eventsService->getEventForEdit($eventId);
        if ($editData === null) {
            $this->renderNotFoundPage();
            return null;
        }
        return $editData;
    }

    private function renderEventEditPage(EventEditPageData $editData): void
    {
        $priceTiers = $this->eventsService->getPriceTiers();
        $viewModel  = $this->buildEventEditViewModel($editData, $priceTiers);
        $artists    = $this->artistsService->getArtists(null);
        require __DIR__ . '/../Views/pages/cms/event-edit.php';
    }

    private function buildEventEditViewModel(EventEditPageData $editData, array $priceTiers = []): CmsEventEditViewModel
    {
        return CmsEventsViewMapper::toEventEditViewModel(
            $editData,
            $this->sessionService->consumeFlash('success'),
            $this->sessionService->consumeFlash('error'),
            $priceTiers,
            $this->eventsService->getVenues(),
        );
    }

    /** Forwards the posted session price to the service layer. */
    private function handleSetPrice(int $sessionId, int $eventId): void
    {
        $priceTierId = $this->readOptionalIntPostParam('PriceTierId');
        $this->eventsService->setSessionPrice($sessionId, $priceTierId, $this->readStringPostParam('Price') ?? '0');
        $this->redirectWithFlash('Price updated successfully.', 'success', "/cms/events/{$eventId}/edit");
    }

    // Session/label/price forms include a hidden EventId for redirecting back to the correct edit page.
    private function getEventIdFromPost(): int
    {
        return $this->readOptionalIntPostParam('EventId') ?? 0;
    }


}
