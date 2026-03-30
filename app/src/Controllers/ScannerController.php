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
 * Ticket scanner for employees and administrators at venue entrances.
 *
 * Extends BaseController (not CmsBaseController) because the CMS base
 * controller restricts access to administrators only, while the scanner
 * must also be accessible to employees.
 */
class ScannerController extends BaseController
{
    public function __construct(
        private readonly IScannerService $scannerService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
        $this->requireEmployeeOrAdmin();
    }

    public function index(): void
    {
        $this->handlePageRequest(function (): void {
            $currentView = 'scanner';
            require __DIR__ . '/../Views/pages/cms/scanner.php';
        });
    }

    public function scan(): void
    {
        $this->handleJsonRequest(function (): void {
            $body = $this->readJsonBody();
            $ticketCode = strtoupper(trim((string) ($body['ticketCode'] ?? '')));
            $userId = $this->requireSessionService()->getUserId();

            if ($userId === null) {
                throw new \RuntimeException('Authenticated user ID is required.');
            }

            try {
                $detail = $this->scannerService->scanTicket($ticketCode, $userId);
                $this->json(ScannerMapper::toScanSuccessResponse($detail));
            } catch (TicketNotFoundException $e) {
                $this->json(['success' => false, 'error' => $e->getMessage()], 404);
            } catch (TicketAlreadyScannedException $e) {
                $this->json(ScannerMapper::toAlreadyScannedResponse(
                    $e->detail,
                    $e->detail->scannedAtUtc ?? 'unknown',
                ), 409);
            }
        });
    }

    private function requireEmployeeOrAdmin(): void
    {
        try {
            $this->requireSessionService()->start();

            if (!$this->requireSessionService()->isLoggedIn()
                || !$this->requireSessionService()->isEmployeeOrAdmin()) {
                $this->redirectAndExit('/cms/login');
            }
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
