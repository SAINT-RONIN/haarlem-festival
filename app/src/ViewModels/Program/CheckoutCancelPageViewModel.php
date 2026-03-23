<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

/**
 * View data for the checkout cancellation page — order number and status message.
 */
final readonly class CheckoutCancelPageViewModel
{
    public function __construct(
        public bool $isLoggedIn,
        public bool $hasCancelData,
        public string $orderId,
        public string $paymentId,
    ) {
    }
}
