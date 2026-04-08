<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Mappers\CmsEventsInputMapper;
use App\Mappers\CmsEventsViewMapper;
use App\DTOs\Cms\EventSessionUpsertData;
use App\DTOs\Cms\EventUpsertData;
use App\DTOs\Domain\Events\EventEditPageData;
use App\Services\Interfaces\ICmsArtistsService;
use App\Services\Interfaces\ICmsEventsService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Cms\CmsEventCreateViewModel;

/**
 * CMS controller for managing festival events and their sessions.
 *
 * Handles full event CRUD, session (time-slot) management, label assignment,
 * and per-session pricing. Each event owns one or more Sessions (bookable
 * time-slots); each session can carry Labels (e.g. "Sold Out") and per-tier
 * Prices (Adult, Child, etc.).
 *
 * Venue management is handled by CmsVenuesController.
 * Schedule-day visibility is handled by CmsScheduleDaysController.
 */
class CmsEventsController extends CmsBaseController
{
    /**
     * @param ICmsEventsService  $eventsService  Provides event, session, label, and price operations.
     * @param ISessionService    $sessionService Session, CSRF, and flash-message support.
     * @param ICmsArtistsService $artistsService Used to populate the artist dropdown on event forms.
     */
    public function __construct(
        private readonly ICmsEventsService $eventsService,
        ISessionService $sessionService,
        private readonly ICmsArtistsService $artistsService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Displays the events list with optional event-type and day-of-week filters.
     * GET /cms/events
     */
    public function index(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'events';
            $viewModel = $this->buildEventsListViewModel();
            require __DIR__ . '/../Views/pages/cms/events.php';
        });
    }

    /**
     * Renders the event creation form, pre-loading available types, venues, artists, and restaurants.
     * GET /cms/events/create
     */
    public function create(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'events';
            $viewModel = $this->buildCreateViewModel();
            require __DIR__ . '/../Views/pages/cms/event-create.php';
        });
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
        $this->handleCmsValidationRequest(function (): void {
            $formData = $this->extractEventFormData(); // Form data extracted via BaseController helpers; service validates internally
            $eventId = $this->eventsService->createEvent($formData);
            $this->redirectWithFlash('Event created successfully.', 'success', "/cms/events/{$eventId}/edit");
        }, '/cms/events/create');
    }

    /**
     * Renders the event edit page with sessions, prices, and labels.
     * GET /cms/events/{id}/edit
     */
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

    /**
     * Validates and applies updates to an existing event.
     * POST /cms/events/{id}/edit
     */
    public function update(int $id): void
    {
        $this->handleCmsValidationRequest(function () use ($id): void {
            $eventId = $id;
            $formData = $this->extractEventFormData(); // Form data extracted via BaseController helpers; service validates internally
            $this->eventsService->updateEvent($eventId, $formData);
            $this->redirectWithFlash('Event updated successfully.', 'success', "/cms/events/{$eventId}/edit");
        }, '/cms/events/' . $id . '/edit');
    }

    /**
     * Adds a new time-slot session to an event.
     * POST /cms/events/{eventId}/sessions
     */
    public function createSession(int $eventId): void
    {
        $this->handleCmsValidationRequest(function () use ($eventId): void {
            $eventIdInt = $eventId;
            $formData = $this->extractSessionFormData($eventIdInt); // Session fields extracted via BaseController helpers; service validates internally
            $this->eventsService->createSession($eventIdInt, $formData);
            $this->redirectWithFlash('Session created successfully.', 'success', "/cms/events/{$eventIdInt}/edit");
        }, '/cms/events/' . $eventId . '/edit');
    }

    /**
     * Updates an existing session's details (capacity, times, etc.).
     * POST /cms/sessions/{id}/edit
     */
    public function updateSession(int $id): void
    {
        $this->handleCmsValidationRequest(function () use ($id): void {
            $eventId = $this->getEventIdFromPost();
            $formData = $this->extractSessionFormData(); // Session fields extracted via BaseController helpers; service validates internally
            $this->eventsService->updateSession($id, $formData);
            $this->redirectWithFlash('Session updated successfully.', 'success', "/cms/events/{$eventId}/edit");
        }, fn (): string => '/cms/events/' . $this->getEventIdFromPost() . '/edit');
    }

    /**
     * Removes a session from its parent event.
     * POST /cms/sessions/{id}/delete
     */
    public function deleteSession(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $eventId = $this->getEventIdFromPost();
            $this->eventsService->deleteSession($id);
            $this->redirectWithFlash('Session deleted successfully.', 'success', "/cms/events/{$eventId}/edit");
        });
    }

    /**
     * Attaches a text label to a session (e.g. "Sold Out", "VIP").
     * POST /cms/sessions/{id}/labels
     */
    public function addLabel(int $id): void
    {
        $this->handleCmsValidationRequest(function () use ($id): void {
            $eventId = $this->getEventIdFromPost();
            $this->eventsService->addLabel($id, $this->readStringPostParam('LabelText') ?? '');
            $this->redirectWithFlash('Label added successfully.', 'success', "/cms/events/{$eventId}/edit");
        }, fn (): string => '/cms/events/' . $this->getEventIdFromPost() . '/edit');
    }

    /**
     * Removes a label from a session.
     * POST /cms/labels/{id}/delete
     */
    public function deleteLabel(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $eventId = $this->getEventIdFromPost();
            $this->eventsService->deleteLabel($id);
            $this->redirectWithFlash('Label deleted successfully.', 'success', "/cms/events/{$eventId}/edit");
        });
    }

    /**
     * Sets or updates the ticket price for a session at a given price tier.
     * POST /cms/sessions/{id}/price
     */
    public function setPrice(int $id): void
    {
        $this->handleCmsValidationRequest(function () use ($id): void {
            $eventId = $this->getEventIdFromPost();
            $this->handleSetPrice($id, $eventId);
        }, fn (): string => '/cms/events/' . $this->getEventIdFromPost() . '/edit');
    }

    /**
     * Deletes an event and all its associated sessions.
     * POST /cms/events/{id}/delete
     */
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

    private function buildEventsListViewModel(): \App\ViewModels\Cms\CmsEventsListViewModel
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

    private function buildEventEditViewModel(EventEditPageData $editData, array $priceTiers = []): \App\ViewModels\Cms\CmsEventEditViewModel
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

    /**
     * Session/label/price forms include a hidden EventId field so the controller
     * can redirect back to the correct event edit page after the action.
     */
    private function getEventIdFromPost(): int
    {
        return $this->readOptionalIntPostParam('EventId') ?? 0;
    }

    /** Reads event form fields from POST using BaseController helpers. */
    private function extractEventFormData(): EventUpsertData
    {
        return CmsEventsInputMapper::fromEventFormInput([
            'EventTypeId'                => $this->readOptionalIntPostParam('EventTypeId'),
            'Title'                      => $this->readStringPostParam('Title'),
            'ShortDescription'           => $this->readStringPostParam('ShortDescription', 1000),
            'LongDescriptionHtml'        => $this->readStringPostParam('LongDescriptionHtml', 65535),
            'FeaturedImageAssetId'       => $this->readOptionalIntPostParam('FeaturedImageAssetId'),
            'VenueId'                    => $this->readOptionalIntPostParam('VenueId'),
            'ArtistId'                   => $this->readOptionalIntPostParam('ArtistId'),
            'IsActive'                   => $this->readBoolPostParam('IsActive'),
            'RestaurantStars'            => $this->readOptionalIntPostParam('RestaurantStars'),
            'RestaurantCuisine'          => $this->readStringPostParam('RestaurantCuisine'),
            'RestaurantShortDescription' => $this->readStringPostParam('RestaurantShortDescription'),
        ]);
    }

    /** Reads session form fields from POST using BaseController helpers. */
    private function extractSessionFormData(?int $eventIdOverride = null): EventSessionUpsertData
    {
        return CmsEventsInputMapper::fromSessionFormInput([
            'EventId'                   => $this->readOptionalIntPostParam('EventId'),
            'StartDateTime'             => $this->readStringPostParam('StartDateTime'),
            'EndDateTime'               => $this->readStringPostParam('EndDateTime'),
            'CapacityTotal'             => $this->readOptionalIntPostParam('CapacityTotal'),
            'CapacitySingleTicketLimit' => $this->readOptionalIntPostParam('CapacitySingleTicketLimit'),
            'HallName'                  => $this->readStringPostParam('HallName'),
            'SessionType'               => $this->readStringPostParam('SessionType'),
            'DurationMinutes'           => $this->readOptionalIntPostParam('DurationMinutes'),
            'LanguageCode'              => $this->readStringPostParam('LanguageCode'),
            'MinAge'                    => $this->readOptionalIntPostParam('MinAge'),
            'MaxAge'                    => $this->readOptionalIntPostParam('MaxAge'),
            'ReservationRequired'       => $this->readBoolPostParam('ReservationRequired'),
            'IsFree'                    => $this->readBoolPostParam('IsFree'),
            'Notes'                     => $this->readStringPostParam('Notes', 2000),
            'HistoryTicketLabel'        => $this->readStringPostParam('HistoryTicketLabel'),
            'CtaLabel'                  => $this->readStringPostParam('CtaLabel'),
            'CtaUrl'                    => $this->readStringPostParam('CtaUrl', 2048),
            'IsCancelled'               => $this->readBoolPostParam('IsCancelled'),
            'IsActive'                  => $this->readBoolPostParam('IsActive'),
        ], $eventIdOverride);
    }

}
