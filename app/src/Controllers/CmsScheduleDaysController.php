<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Interfaces\ICmsScheduleDayService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Cms\CmsScheduleDaysViewModel;

/**
 * CMS controller for managing which days appear on the public event schedule.
 *
 * Each event type (Jazz, History, etc.) can have its schedule days toggled
 * on or off independently, or globally for all types at once. This controller
 * renders the configuration page and handles the toggle POST action.
 *
 * All persistence delegates to ICmsScheduleDayService; this controller owns
 * only HTTP flow (auth gating, form reading, flash-message redirects via PRG).
 */
class CmsScheduleDaysController extends CmsBaseController
{
    /**
     * @param ICmsScheduleDayService $scheduleDayService Provides schedule-day visibility operations.
     * @param ISessionService        $sessionService     Session, CSRF, and flash-message support.
     */
    public function __construct(
        private readonly ICmsScheduleDayService $scheduleDayService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Displays the schedule-day visibility configuration page.
     *
     * Shows a grid of days for each event type so admins can enable or disable
     * individual days on the public schedule without a code deploy.
     *
     * GET /cms/schedule-days
     */
    public function index(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'schedule-days';
            $this->renderScheduleDaysPage();
        });
    }

    /**
     * Toggles a specific day's visibility on the public schedule.
     *
     * The toggle can be global (affects all event types) or scoped to a single
     * event type — determined by the EventTypeId POST field (0 or absent = global).
     *
     * POST /cms/schedule-days/toggle
     *
     * @throws \App\Exceptions\ValidationException Redirects with error flash on failure.
     */
    public function toggle(): void
    {
        $this->handleCmsValidationRequest(function (): void {
            // null event type ID means this is a global (all-types) visibility toggle
            $eventTypeId = $this->readOptionalIntPostParam('EventTypeId');
            $dayOfWeek   = $this->readOptionalIntPostParam('DayOfWeek') ?? 0;
            $isVisible   = $this->readBoolPostParam('IsVisible');

            $this->scheduleDayService->setScheduleDayVisibility($eventTypeId, $dayOfWeek, $isVisible);
            $this->redirectWithFlash('Day visibility updated.', 'success', '/cms/schedule-days');
        }, '/cms/schedule-days');
    }

    /**
     * Loads page data and renders the schedule-days view.
     *
     * Separated from index() so the method body stays short and the rendering
     * logic can be read in isolation without scrolling through the page request wrapper.
     */
    private function renderScheduleDaysPage(): void
    {
        $pageData  = $this->scheduleDayService->getScheduleDaysPageData();
        $viewModel = new CmsScheduleDaysViewModel(
            eventTypes:     $pageData->eventTypes,
            globalConfigs:  $pageData->grouped->global,
            typeConfigs:    $pageData->grouped->byType,
            successMessage: $this->sessionService->consumeFlash('success'),
            errorMessage:   $this->sessionService->consumeFlash('error'),
        );
        require __DIR__ . '/../Views/pages/cms/schedule-days.php';
    }
}
