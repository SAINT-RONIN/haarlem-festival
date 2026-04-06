<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Interfaces\ICmsEventsService;
use App\Services\Interfaces\ISessionService;

/**
 * CMS controller for managing event venues.
 *
 * Venues are shared locations (name + address) that event sessions reference.
 * This controller handles the venue list page, the inline AJAX endpoint for
 * creating a venue without leaving the event-create form, and soft-deletion.
 *
 * All database operations delegate to ICmsEventsService; this controller owns
 * only HTTP flow (auth gating, JSON vs. page response, flash-message redirects).
 */
class CmsVenuesController extends CmsBaseController
{
    /**
     * @param ICmsEventsService $eventsService Provides venue CRUD and lookup operations.
     * @param ISessionService   $sessionService Session, CSRF, and flash-message support.
     */
    public function __construct(
        private readonly ICmsEventsService $eventsService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Displays the venues management page with the full venue list.
     * GET /cms/venues
     */
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

    /**
     * Creates a new venue via AJAX and returns the new venue ID as JSON.
     *
     * Called from the event-create form's inline "add venue" modal so the admin
     * can add a venue without leaving the page. Returns a JSON object with keys
     * `success`, `venueId`, and `name` on success, or a 400 error on failure.
     *
     * POST /cms/venues
     *
     * @throws \App\Exceptions\ValidationException Returns 400 JSON on validation failure.
     */
    public function create(): void
    {
        $this->handleCmsJsonRequest(function (): void {
            $name        = $this->readStringPostParam('VenueName') ?? '';
            $addressLine = $this->readStringPostParam('AddressLine') ?? '';
            $venueId     = $this->eventsService->createVenue($name, $addressLine);
            $this->json(['success' => true, 'venueId' => $venueId, 'name' => $name]);
        });
    }

    /**
     * Soft-deletes a venue by ID and redirects back to the venue list.
     *
     * Events that reference the deleted venue keep their existing venue assignment —
     * deletion only prevents the venue from appearing in the creation dropdown.
     *
     * POST /cms/venues/{id}/delete
     *
     * @param int $id The venue record ID.
     * @throws \App\Exceptions\ValidationException Redirects with error flash on failure.
     */
    public function delete(int $id): void
    {
        $this->handleCmsValidationRequest(function () use ($id): void {
            $this->eventsService->deleteVenue($id);
            $this->redirectWithFlash('Venue deleted successfully.', 'success', '/cms/venues');
        }, '/cms/venues');
    }
}
