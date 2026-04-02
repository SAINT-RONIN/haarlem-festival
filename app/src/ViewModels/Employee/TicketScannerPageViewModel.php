<?php

declare(strict_types=1);

namespace App\ViewModels\Employee;

/**
 * View data for the employee ticket scanner page.
 */
final readonly class TicketScannerPageViewModel
{
    public function __construct(
        public string $scanEndpoint,
        public string $logoutUrl,
        public string $employeeLabel,
        public string $scriptVersion,
    ) {
    }
}
