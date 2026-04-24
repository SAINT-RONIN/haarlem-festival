<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Interfaces\ICmsEventsService;
use App\Services\Interfaces\ISessionService;

/**
 * CMS controller for managing event venues (list, AJAX create, soft-delete).
 */
class CmsVenuesController extends CmsBaseController
{
    public function __construct(
        private readonly ICmsEventsService $eventsService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView    = 'venues';
            $venues         = $this->eventsService->getVenues();
            $successMessage = $this->sessionService->consumeFlash('success');
            $errorMessage   = $this->sessionService->consumeFlash('error');
            require __DIR__ . '/../Views/pages/cms/venues.php';
        });
    }

    // Called from the event-create form's inline "add venue" modal.
    public function create(): void
    {
        $this->handleCmsJsonRequest(function (): void {
            $name        = $this->readStringPostParam('VenueName') ?? '';
            $addressLine = $this->readStringPostParam('AddressLine') ?? '';
            $venueId     = $this->eventsService->createVenue($name, $addressLine);
            $this->json(['success' => true, 'venueId' => $venueId, 'name' => $name]);
        });
    }

    // Deletion only hides the venue from the creation dropdown; existing event references are kept.
    public function delete(int $id): void
    {
        $this->handleCmsValidationRequest(function () use ($id): void {
            $this->eventsService->deleteVenue($id);
            $this->redirectWithFlash('Venue deleted successfully.', 'success', '/cms/venues');
        }, '/cms/venues');
    }
}
