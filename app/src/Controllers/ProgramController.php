<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\ProgramMapper;
use App\Services\Interfaces\ICmsPageContentService;
use App\Services\Interfaces\IProgramService;
use App\Services\Interfaces\ISessionService;

class ProgramController extends BaseController
{
    private IProgramService $programService;
    private ICmsPageContentService $cmsService;
    private ISessionService $sessionService;

    public function __construct(
        IProgramService $programService,
        ICmsPageContentService $cmsService,
        ISessionService $sessionService,
    ) {
        $this->programService = $programService;
        $this->cmsService = $cmsService;
        $this->sessionService = $sessionService;
    }

    public function index(): void
    {
        try {
            $sessionKey = $this->getSessionKey();
            $userId = $this->getLoggedInUserId();
            $isLoggedIn = $this->sessionService->isLoggedIn();

            $programData = $this->programService->getProgramData($sessionKey, $userId);
            $cmsContent = $this->cmsService->getSectionContent('my-program', 'main');
            $viewModel = ProgramMapper::toMyProgramViewModel($programData, $cmsContent, $isLoggedIn);

            $this->renderView(__DIR__ . '/../Views/pages/my-program.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function add(): void
    {
        try {
            $body = $this->readJsonBody();
            $sessionKey = $this->getSessionKey();
            $userId = $this->getLoggedInUserId();

            $eventSessionId = (int)($body['eventSessionId'] ?? 0);
            $quantity = (int)($body['quantity'] ?? 1);
            $donationAmount = (float)($body['donationAmount'] ?? 0.0);

            $item = $this->programService->addToProgram($sessionKey, $userId, $eventSessionId, $quantity, $donationAmount);

            $this->json(['success' => true, 'programItemId' => $item->programItemId]);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    public function updateQuantity(): void
    {
        try {
            $body = $this->readJsonBody();
            $sessionKey = $this->getSessionKey();
            $userId = $this->getLoggedInUserId();

            $programItemId = (int)($body['programItemId'] ?? 0);
            $quantity = (int)($body['quantity'] ?? 0);

            $this->programService->updateQuantity($sessionKey, $userId, $programItemId, $quantity);

            $this->respondJsonWithTotals($sessionKey, $userId);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    public function updateDonation(): void
    {
        try {
            $body = $this->readJsonBody();
            $sessionKey = $this->getSessionKey();
            $userId = $this->getLoggedInUserId();

            $programItemId = (int)($body['programItemId'] ?? 0);
            $donationAmount = (float)($body['donationAmount'] ?? 0.0);

            $this->programService->updateDonation($sessionKey, $userId, $programItemId, $donationAmount);

            $this->respondJsonWithTotals($sessionKey, $userId);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    public function remove(): void
    {
        try {
            $body = $this->readJsonBody();
            $sessionKey = $this->getSessionKey();
            $userId = $this->getLoggedInUserId();

            $programItemId = (int)($body['programItemId'] ?? 0);

            $this->programService->removeItem($sessionKey, $userId, $programItemId);

            $this->respondJsonWithTotals($sessionKey, $userId);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    public function clear(): void
    {
        try {
            $sessionKey = $this->getSessionKey();
            $userId = $this->getLoggedInUserId();

            $this->programService->clearProgram($sessionKey, $userId);

            $this->json(['success' => true]);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    private function respondJsonWithTotals(string $sessionKey, ?int $userId): void
    {
        $programData = $this->programService->getProgramData($sessionKey, $userId);
        $totals = ProgramMapper::formatTotals($programData);

        $this->json([
            'success' => true,
            'subtotal' => $totals['subtotal'],
            'taxAmount' => $totals['taxAmount'],
            'total' => $totals['total'],
            'canCheckout' => $programData['items'] !== [],
        ]);
    }

    private function getSessionKey(): string
    {
        $this->sessionService->start();
        return session_id();
    }

    private function getLoggedInUserId(): ?int
    {
        return $this->sessionService->isLoggedIn() ? $this->sessionService->getUserId() : null;
    }

}
