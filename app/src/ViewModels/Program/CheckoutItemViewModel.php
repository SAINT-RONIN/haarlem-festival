<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

/**
 * A single line item in the checkout order summary — quantity, title, and formatted price.
 */
final readonly class CheckoutItemViewModel
{
    public function __construct(
        public string $quantityDisplay,
        public string $eventTitle,
        public string $priceDisplay,
    ) {}
}
