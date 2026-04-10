<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Exceptions\TicketAlreadyScannedException;
use App\Exceptions\TicketNotFoundException;
use App\Mappers\ScannerMapper;
use App\Services\Interfaces\IScannerService;
use App\Services\Interfaces\ISessionService;

/**
 * Ticket scanner for employees and administrators.
 *
 * Extends BaseController (not CmsBaseController) so employees can access it too.
 */
class ScannerController extends BaseController
{
    public function __construct(
        private readonly IScannerService $scannerService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /** Renders the scanner page for logged-in employees and administrators. */
    public function index(): void
    {
        $this->requireEmployeeOrAdmin();

        $this->handlePageRequest(function (): void {
            $currentView = 'scanner';
            require __DIR__ . '/../Views/pages/cms/scanner.php';
        });
    }

    public function scan(): void
    {
        $this->requireEmployeeOrAdmin();

        $this->handleJsonRequest(function (): void {
            $ticketCode = $this->resolveTicketCode($this->readJsonBody());
            $userId     = $this->requireAuthenticatedUserId();

            if ($ticketCode === '') {
                $this->json(['success' => false, 'error' => 'Ticket code is required.'], 400);
                return;
            }

            try {
                $detail = $this->scannerService->scanTicket($ticketCode, $userId);
                $this->json(ScannerMapper::toScanSuccessResponse($detail));
            } catch (TicketNotFoundException $e) {
                $this->json(['success' => false, 'error' => $e->getMessage()], 404);
            } catch (TicketAlreadyScannedException $e) {
                $this->json(ScannerMapper::toAlreadyScannedResponse($e->detail, $e->detail->scannedAtUtc ?? 'unknown'), 409);
            }
        });
    }

    // Normalises to uppercase/trimmed so physical scanners with trailing newlines still match.
    private function resolveTicketCode(array $body): string
    {
        return strtoupper(trim((string) ($body['ticketCode'] ?? '')));
    }

    private function requireAuthenticatedUserId(): int
    {
        $userId = $this->requireSessionService()->getUserId();

        if ($userId === null) {
            throw new \RuntimeException('Authenticated user ID is required.');
        }

        return $userId;
    }

    /** Blocks access unless the current session belongs to an employee or administrator. */
    private function requireEmployeeOrAdmin(): void
    {
        try {
            $sessionService = $this->requireSessionService();
            $sessionService->start();

            if (!$sessionService->isLoggedIn() || !$sessionService->isEmployeeOrAdmin()) {
                $this->redirectAndExit('/cms/login');
            }
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
