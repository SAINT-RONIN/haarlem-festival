<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

/**
 * View data for the checkout success page — order number, confirmation details.
 */
final readonly class CheckoutSuccessPageViewModel
{
    public function __construct(
        public bool $isLoggedIn,
        public bool $hasSessionData,
        public string $sessionId,
        public string $paymentStatus,
        public string $checkoutStatus,
    ) {
    }
}
