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
    /** Injects the scanner service and the shared session service used for access control. */
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

    /**
     * Accepts a ticket code, attempts a scan, and returns a JSON result for the scanner UI.
     *
     * Reads `ticketCode` from the JSON request body, normalises it to uppercase/trimmed,
     * and delegates to the scanner service. On success, returns a scan-success payload.
     * On failure, returns a typed error payload with the appropriate HTTP status code.
     *
     * @throws \RuntimeException When no authenticated user ID is available in the session.
     */
    public function scan(): void
    {
        $this->requireEmployeeOrAdmin();

        $this->handleJsonRequest(function (): void {
            $ticketCode = $this->resolveTicketCode($this->readJsonBody());
            $userId     = $this->requireAuthenticatedUserId();

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

    /**
     * Extracts and normalises the ticket code from the decoded JSON request body.
     *
     * Trims whitespace and converts to uppercase so that physical scanners that add
     * trailing newlines or mixed-case codes still match the stored ticket codes correctly.
     *
     * @param array $body Decoded JSON body from the scan request.
     * @return string Normalised ticket code (may be empty string if not provided).
     */
    private function resolveTicketCode(array $body): string
    {
        return strtoupper(trim((string) ($body['ticketCode'] ?? '')));
    }

    /**
     * Returns the authenticated user's account ID, or throws if no user is logged in.
     *
     * The scanner requires a user ID to record who performed the scan in the audit log.
     * A null ID should never happen because requireEmployeeOrAdmin() already gates the
     * request, but this guard prevents silent data corruption if that check is bypassed.
     *
     * @return int The logged-in user's account ID.
     * @throws \RuntimeException When the session contains no user ID.
     */
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
