<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\Interfaces\ICmsService;
use App\Services\Interfaces\IProgramService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Program\MyProgramPageViewModel;
use App\ViewModels\Program\ProgramItemViewModel;

class ProgramController extends BaseController
{
    private IProgramService $programService;
    private ICmsService $cmsService;
    private ISessionService $sessionService;

    public function __construct(
        IProgramService $programService,
        ICmsService $cmsService,
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
            $viewModel = MyProgramPageViewModel::fromServiceData($programData, $cmsContent, $isLoggedIn);

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

            $this->validateAddInput($eventSessionId, $quantity);

            $item = $this->programService->addToProgram($sessionKey, $userId, $eventSessionId, $quantity, $donationAmount);

            $this->respondJson(['success' => true, 'programItemId' => $item->programItemId]);
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

            $this->validatePositiveId($programItemId, 'programItemId');

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

            $this->validatePositiveId($programItemId, 'programItemId');

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

            $this->validatePositiveId($programItemId, 'programItemId');

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

            $this->respondJson(['success' => true]);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    private function respondJsonWithTotals(string $sessionKey, ?int $userId): void
    {
        $programData = $this->programService->getProgramData($sessionKey, $userId);
        $totals = ProgramItemViewModel::formatTotals($programData);

        $this->respondJson([
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

    protected function readJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        $body = json_decode($raw ?: '', true);

        if (!is_array($body)) {
            throw new \InvalidArgumentException('Invalid JSON body');
        }

        return $body;
    }

    private function validateAddInput(int $eventSessionId, int $quantity): void
    {
        if ($eventSessionId <= 0) {
            throw new \InvalidArgumentException('eventSessionId is required');
        }

        if ($quantity <= 0) {
            throw new \InvalidArgumentException('quantity must be at least 1');
        }
    }

    private function validatePositiveId(int $id, string $fieldName): void
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("{$fieldName} is required");
        }
    }

    private function respondJson(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
