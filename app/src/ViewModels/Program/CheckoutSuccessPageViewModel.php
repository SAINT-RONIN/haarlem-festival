<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

/**
 * View data for the checkout success page.
 */
final readonly class CheckoutSuccessPageViewModel
{
    public function __construct(
        public bool $isLoggedIn,
        public bool $hasSessionData,
        public ?string $orderReference,
        public ?string $totalLabel,
    ) {}
}
