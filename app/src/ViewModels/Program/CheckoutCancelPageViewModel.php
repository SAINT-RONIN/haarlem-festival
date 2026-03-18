<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

final readonly class CheckoutCancelPageViewModel
{
    /**
     * @param array<string,mixed> $cancelResult
     */
    public function __construct(
        public array $cancelResult,
        public bool $isLoggedIn,
    ) {
    }
}

