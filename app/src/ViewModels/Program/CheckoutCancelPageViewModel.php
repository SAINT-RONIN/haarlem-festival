<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

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
