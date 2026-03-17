<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\CmsService;
use App\Services\Interfaces\ICmsService;
use App\Services\Interfaces\IProgramService;
use App\Services\Interfaces\ISessionService;
use App\Services\ProgramService;
use App\Services\SessionService;
use App\ViewModels\Program\CheckoutPageViewModel;

class CheckoutController extends BaseController
{
    private IProgramService $programService;
    private ICmsService $cmsService;
    private ISessionService $sessionService;

    public function __construct(
        ?IProgramService $programService = null,
        ?ICmsService $cmsService = null,
        ?ISessionService $sessionService = null,
    ) {
        $this->programService = $programService ?? new ProgramService();
        $this->cmsService = $cmsService ?? new CmsService();
        $this->sessionService = $sessionService ?? new SessionService();
    }

    public function index(): void
    {
        try {
            $this->sessionService->start();
            $sessionKey = session_id();
            $isLoggedIn = $this->sessionService->isLoggedIn();
            $userId = $isLoggedIn ? $this->sessionService->getUserId() : null;

            $programData = $this->programService->getProgramData($sessionKey, $userId);

            if ($programData['items'] === []) {
                header('Location: /my-program');
                return;
            }

            $cmsContent = $this->cmsService->getSectionContent('checkout', 'main');

            $viewModel = CheckoutPageViewModel::fromServiceData($programData, $cmsContent, $isLoggedIn);

            $this->renderView(__DIR__ . '/../Views/pages/checkout.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
