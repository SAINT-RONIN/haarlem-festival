<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\ProgramMapper;
use App\Models\ProgramMainContent;
use App\Services\Interfaces\ICmsPageContentService;
use App\Services\Interfaces\IProgramService;
use App\Services\Interfaces\ISessionService;

/**
 * Manages the user's personal festival program (cart): viewing, adding, updating,
 * removing, and clearing event items before checkout.
 */
class ProgramController extends BaseController
{
    public function __construct(
        private readonly IProgramService $programService,
        private readonly ICmsPageContentService $cmsService,
        private readonly ISessionService $sessionService,
    ) {
    }

    /**
     * Displays the "My Program" page listing all items the user has added.
     * GET /my-program
     */
    public function index(): void
    {
        try {
            $sessionKey = $this->getSessionKey();
            $userId = $this->getLoggedInUserId();
            $isLoggedIn = $this->sessionService->isLoggedIn();

            $programData = $this->programService->getProgramData($sessionKey, $userId);
            $cmsContent = ProgramMainContent::fromRawArray(
                $this->cmsService->getSectionContent('my-program', 'main'),
            );
            $viewModel = ProgramMapper::toMyProgramViewModel($programData, $cmsContent, $isLoggedIn);

            $this->renderView(__DIR__ . '/../Views/pages/my-program.php', $viewModel);
        } catch (\InvalidArgumentException $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Adds an event session to the user's program with a given quantity and optional donation.
     * POST /my-program/add (JSON)
     */
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
        } catch (\InvalidArgumentException $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    /**
     * Updates the ticket quantity for a program item and returns recalculated totals.
     * PATCH /my-program/update-quantity (JSON)
     */
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
        } catch (\InvalidArgumentException $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    /**
     * Updates the donation amount for a program item and returns recalculated totals.
     * PATCH /my-program/update-donation (JSON)
     */
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
        } catch (\InvalidArgumentException $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    /**
     * Removes a single item from the program and returns recalculated totals.
     * DELETE /my-program/remove (JSON)
     */
    public function remove(): void
    {
        try {
            $body = $this->readJsonBody();
            $sessionKey = $this->getSessionKey();
            $userId = $this->getLoggedInUserId();

            $programItemId = (int)($body['programItemId'] ?? 0);

            $this->programService->removeItem($sessionKey, $userId, $programItemId);

            $this->respondJsonWithTotals($sessionKey, $userId);
        } catch (\InvalidArgumentException $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    /**
     * Removes all items from the user's program.
     * DELETE /my-program/clear (JSON)
     */
    public function clear(): void
    {
        try {
            $sessionKey = $this->getSessionKey();
            $userId = $this->getLoggedInUserId();

            $this->programService->clearProgram($sessionKey, $userId);

            $this->json(['success' => true]);
        } catch (\InvalidArgumentException $error) {
            ControllerErrorResponder::respondJson($error, 400);
        }
    }

    /** Re-fetches program data to return freshly calculated subtotal, tax, and total after a mutation. */
    private function respondJsonWithTotals(string $sessionKey, ?int $userId): void
    {
        $programData = $this->programService->getProgramData($sessionKey, $userId);
        $totals = ProgramMapper::formatTotals($programData);

        $this->json([
            'success' => true,
            'subtotal' => $totals['subtotal'],
            'taxAmount' => $totals['taxAmount'],
            'total' => $totals['total'],
            'canCheckout' => $programData->items !== [],
        ]);
    }

    /** Ensures a PHP session exists and returns its ID, used as the anonymous cart key. */
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
