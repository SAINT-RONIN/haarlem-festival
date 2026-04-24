<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Interfaces\ICmsScheduleDayService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Cms\CmsScheduleDaysViewModel;

/**
 * CMS controller for toggling which days appear on the public event schedule.
 */
class CmsScheduleDaysController extends CmsBaseController
{
    public function __construct(
        private readonly ICmsScheduleDayService $scheduleDayService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'schedule-days';
            $this->renderScheduleDaysPage();
        });
    }

    // null EventTypeId = global toggle (all event types); 0 or absent = global.
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

    private function renderScheduleDaysPage(): void
    {
        $pageData  = $this->scheduleDayService->getScheduleDaysPageData();
        $viewModel = new CmsScheduleDaysViewModel(
            eventTypes: $pageData->eventTypes,
            globalConfigs: $pageData->grouped->global,
            typeConfigs: $pageData->grouped->byType,
            successMessage: $this->sessionService->consumeFlash('success'),
            errorMessage: $this->sessionService->consumeFlash('error'),
        );
        require __DIR__ . '/../Views/pages/cms/schedule-days.php';
    }
}
