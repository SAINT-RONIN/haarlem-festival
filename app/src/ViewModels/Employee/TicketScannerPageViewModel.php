<?php

declare(strict_types=1);

namespace App\ViewModels\Employee;

/**
 * View data for the ticket scanner page (employee and admin variants).
 */
final readonly class TicketScannerPageViewModel
{
    public function __construct(
        public string $scanEndpoint,
        public string $logoutUrl,
        public string $logoutCsrfToken,
        public string $roleLabel,
        public string $scriptVersion,
        public string $pageTitle,
        public string $layoutVariant,
        public string $currentView,
    ) {}
}
