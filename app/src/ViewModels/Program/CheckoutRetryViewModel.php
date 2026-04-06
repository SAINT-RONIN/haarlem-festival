<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

/**
 * ViewModel for the retry payment page showing order summary and payment options.
 */
final readonly class CheckoutRetryViewModel
{
    public function __construct(
        public int $orderId,
        public string $orderNumber,
        public string $totalAmountFormatted,
        public string $payBeforeFormatted,
        public bool $isExpired,
        public bool $isLoggedIn,
        public string $currentPage = 'checkout',
    ) {
    }
}
