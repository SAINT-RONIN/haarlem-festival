<?php

declare(strict_types=1);

namespace App\Controllers;

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
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Displays the "My Program" page listing all items the user has added.
     * GET /my-program
     */
    public function index(): void
    {
        $this->handlePageRequest(function (): void {
            $this->renderProgramPage();
        });
    }

    /** Loads session context, program data, CMS content, and renders the My Program page. */
    private function renderProgramPage(): void
    {
        $context = $this->resolveSessionContext();

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
        $this->handleJsonRequest(function (): void {
            $this->processAdd();
        });
    }

    /** Reads JSON body, resolves session context, casts input fields, and adds a session to the program. */
    private function processAdd(): void
    {
        $body = $this->readJsonBody();
        $context = $this->resolveSessionContext();

        $eventSessionId = (int)($body['eventSessionId'] ?? 0);
        $quantity = (int)($body['quantity'] ?? 1);
        $groupTicketQuantity = (int)($body['groupTicketQuantity'] ?? 1);
        $donationAmount = (float)($body['donationAmount'] ?? 0.0);

        $item = $this->programService->addToProgram($context->sessionKey, $context->userId, $eventSessionId, $quantity, $groupTicketQuantity, $donationAmount);

        $this->json(['success' => true, 'programItemId' => $item->programItemId]);
    }

    /**
     * Adds a festival pass to the user's program with a given quantity.
     * POST /api/program/add-pass (JSON)
     */
    public function addPass(): void
    {
        $this->handleJsonRequest(function (): void {
            $this->processAddPass();
        });
    }

    /** Reads JSON body, resolves session context, and adds a pass to the program. */
    private function processAddPass(): void
    {
        $body = $this->readJsonBody();
        $context = $this->resolveSessionContext();

        $passTypeId = (int)($body['passTypeId'] ?? 0);
        $quantity = (int)($body['quantity'] ?? 1);
        $validDate = isset($body['validDate']) ? (string)$body['validDate'] : null;

        $item = $this->programService->addPassToProgram($context->sessionKey, $context->userId, $passTypeId, $validDate, $quantity);

        $this->json(['success' => true, 'programItemId' => $item->programItemId]);
    }

    /**
     * Updates the ticket quantity for a program item and returns recalculated totals.
     * PATCH /my-program/update-quantity (JSON)
     */
    public function updateQuantity(): void
    {
        $this->handleJsonRequest(function (): void {
            $this->processUpdateQuantity();
        });
    }

    /** Reads JSON body, casts quantity fields, updates the item quantity, and responds with recalculated totals. */
    private function processUpdateQuantity(): void
    {
        $body = $this->readJsonBody();
        $context = $this->resolveSessionContext();

        $programItemId = (int)($body['programItemId'] ?? 0);
        $quantity = (int)($body['quantity'] ?? 0);
        $groupTicketQuantity = (int)($body['groupTicketQuantity'] ?? 0);

        $this->programService->updateQuantity($context->sessionKey, $context->userId, $programItemId, $quantity, $groupTicketQuantity);

        $this->respondJsonWithTotals($context->sessionKey, $context->userId);
    }

    /**
     * Updates the donation amount for a program item and returns recalculated totals.
     * PATCH /my-program/update-donation (JSON)
     */
    public function updateDonation(): void
    {
        $this->handleJsonRequest(function (): void {
            $this->processUpdateDonation();
        });
    }

    /** Reads JSON body, casts donation fields, updates the donation amount, and responds with recalculated totals. */
    private function processUpdateDonation(): void
    {
        $body = $this->readJsonBody();
        $context = $this->resolveSessionContext();

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
        $this->handleJsonRequest(function (): void {
            $this->processRemove();
        });
    }

    /** Reads JSON body, casts the item ID, removes the item, and responds with recalculated totals. */
    private function processRemove(): void
    {
        $body = $this->readJsonBody();
        $context = $this->resolveSessionContext();

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
        $this->handleJsonRequest(function (): void {
            $context = $this->resolveSessionContext();

            $this->programService->clearProgram($context->sessionKey, $context->userId);

            $this->json(['success' => true]);
        });
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
