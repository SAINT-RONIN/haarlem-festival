<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

final readonly class CheckoutSuccessPageViewModel
{
    /**
     * @param array<string,mixed>|null $sessionSummary
     */
    public function __construct(
        public ?array $sessionSummary,
        public bool $isLoggedIn,
    ) {
    }
}

