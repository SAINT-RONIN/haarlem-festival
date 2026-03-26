<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\DTOs\Session\SessionContext;
use App\Mappers\ProgramMapper;
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
            $this->renderProgramPage();
        } catch (\InvalidArgumentException $error) {
            ControllerErrorResponder::respond($error);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /** Loads session context, program data, CMS content, and renders the My Program page. */
    private function renderProgramPage(): void
    {
        $context = $this->resolveSessionContext($this->sessionService);

        $programData = $this->programService->getProgramData($context->sessionKey, $context->userId);
        $cmsContent = $this->programService->getProgramMainContent();
        $viewModel = ProgramMapper::toMyProgramViewModel($programData, $cmsContent, $context->isLoggedIn);

        $this->renderView(__DIR__ . '/../Views/pages/my-program.php', $viewModel);
    }

    /**
     * Adds an event session to the user's program with a given quantity and optional donation.
     * POST /my-program/add (JSON)
     */
    public function add(): void
    {
        try {
            $this->processAdd();
        } catch (\InvalidArgumentException $error) {
            ControllerErrorResponder::respondJson($error, 400);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error);
        }
    }

    /** Reads JSON body, resolves session context, casts input fields, and adds a session to the program. */
    private function processAdd(): void
    {
        $body = $this->readJsonBody();
        $context = $this->resolveSessionContext($this->sessionService);

        $eventSessionId = (int)($body['eventSessionId'] ?? 0);
        $quantity = (int)($body['quantity'] ?? 1);
        $donationAmount = (float)($body['donationAmount'] ?? 0.0);

        $item = $this->programService->addToProgram($context->sessionKey, $context->userId, $eventSessionId, $quantity, $donationAmount);

        $this->json(['success' => true, 'programItemId' => $item->programItemId]);
    }

    /**
     * Updates the ticket quantity for a program item and returns recalculated totals.
     * PATCH /my-program/update-quantity (JSON)
     */
    public function updateQuantity(): void
    {
        try {
            $this->processUpdateQuantity();
        } catch (\InvalidArgumentException $error) {
            ControllerErrorResponder::respondJson($error, 400);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error);
        }
    }

    /** Reads JSON body, casts quantity fields, updates the item quantity, and responds with recalculated totals. */
    private function processUpdateQuantity(): void
    {
        $body = $this->readJsonBody();
        $context = $this->resolveSessionContext($this->sessionService);

        $programItemId = (int)($body['programItemId'] ?? 0);
        $quantity = (int)($body['quantity'] ?? 0);

        $this->programService->updateQuantity($context->sessionKey, $context->userId, $programItemId, $quantity);

        $this->respondJsonWithTotals($context->sessionKey, $context->userId);
    }

    /**
     * Updates the donation amount for a program item and returns recalculated totals.
     * PATCH /my-program/update-donation (JSON)
     */
    public function updateDonation(): void
    {
        try {
            $this->processUpdateDonation();
        } catch (\InvalidArgumentException $error) {
            ControllerErrorResponder::respondJson($error, 400);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error);
        }
    }

    /** Reads JSON body, casts donation fields, updates the donation amount, and responds with recalculated totals. */
    private function processUpdateDonation(): void
    {
        $body = $this->readJsonBody();
        $context = $this->resolveSessionContext($this->sessionService);

        $programItemId = (int)($body['programItemId'] ?? 0);
        $donationAmount = (float)($body['donationAmount'] ?? 0.0);

        $this->programService->updateDonation($context->sessionKey, $context->userId, $programItemId, $donationAmount);

        $this->respondJsonWithTotals($context->sessionKey, $context->userId);
    }

    /**
     * Removes a single item from the program and returns recalculated totals.
     * DELETE /my-program/remove (JSON)
     */
    public function remove(): void
    {
        try {
            $this->processRemove();
        } catch (\InvalidArgumentException $error) {
            ControllerErrorResponder::respondJson($error, 400);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error);
        }
    }

    /** Reads JSON body, casts the item ID, removes the item, and responds with recalculated totals. */
    private function processRemove(): void
    {
        $body = $this->readJsonBody();
        $context = $this->resolveSessionContext($this->sessionService);

        $programItemId = (int)($body['programItemId'] ?? 0);

        $this->programService->removeItem($context->sessionKey, $context->userId, $programItemId);

        $this->respondJsonWithTotals($context->sessionKey, $context->userId);
    }

    /**
     * Removes all items from the user's program.
     * DELETE /my-program/clear (JSON)
     */
    public function clear(): void
    {
        try {
            $context = $this->resolveSessionContext($this->sessionService);

            $this->programService->clearProgram($context->sessionKey, $context->userId);

            $this->json(['success' => true]);
        } catch (\InvalidArgumentException $error) {
            ControllerErrorResponder::respondJson($error, 400);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error);
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
}
