<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

final readonly class CheckoutItemViewModel
{
    public function __construct(
        public string $quantityDisplay,
        public string $eventTitle,
        public string $priceDisplay,
    ) {
    }
}
