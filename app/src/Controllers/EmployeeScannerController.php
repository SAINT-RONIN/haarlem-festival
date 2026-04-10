<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\AssetVersionHelper;
use App\Exceptions\ValidationException;
use App\Services\Interfaces\ISessionService;
use App\Services\Interfaces\ITicketScannerService;
use App\ViewModels\Employee\TicketScannerPageViewModel;

/**
 * Employee-only ticket scanner page and scan endpoint.
 */
class EmployeeScannerController extends BaseController
{
    public function __construct(
        ISessionService $sessionService,
        private readonly ITicketScannerService $ticketScannerService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handlePageRequest(function (): void {
            $this->requireEmployee();

            $viewModel = new TicketScannerPageViewModel(
                scanEndpoint: '/api/employee/scanner/scan',
                logoutUrl: '/logout',
                logoutCsrfToken: $this->sessionService->getCsrfToken('logout'),
                roleLabel: 'Employee Scanner',
                scriptVersion: AssetVersionHelper::resolveJsVersion(__DIR__ . '/../../public/assets/js/employee-ticket-scanner.js'),
                pageTitle: 'Ticket Scanner',
                layoutVariant: 'employee',
                currentView: 'scanner',
            );

            $this->renderView(__DIR__ . '/../Views/pages/employee-ticket-scanner.php', $viewModel);
        });
    }

    public function scan(): void
    {
        $this->handleJsonRequest(function (): void {
            $employeeUserId = $this->requireEmployee();
            $payload = $this->readJsonBody();
            $ticketCode = strtoupper(trim((string) ($payload['ticketCode'] ?? '')));

            if ($ticketCode === '') {
                $this->json(['success' => false, 'error' => 'Ticket code is required.'], 400);
                return;
            }

            $result = $this->ticketScannerService->scanTicket($ticketCode, $employeeUserId);

            $this->json([
                'success' => true,
                'ticketCode' => $result->ticketCode,
                'message' => $result->message,
                'scannedAt' => $result->scannedAtLabel,
            ]);
        }, [\InvalidArgumentException::class, ValidationException::class]);
    }

    private function requireEmployee(): int
    {
        $this->sessionService->start();

        if (!$this->sessionService->isLoggedIn()) {
            $this->redirectAndExit('/login');
        }

        if (!$this->sessionService->isEmployee()) {
            $this->redirectAndExit($this->sessionService->isAdmin() ? '/cms' : '/');
        }

        $userId = $this->sessionService->getUserId();
        if ($userId === null) {
            $this->redirectAndExit('/login');
        }

        return $userId;
    }
}
