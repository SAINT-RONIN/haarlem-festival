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

    /**
     * @param array<string, mixed> $item
     */
    public static function fromItemData(array $item): self
    {
        $quantity = (int)$item['quantity'];
        $basePrice = (float)$item['basePrice'];
        $donationAmount = (float)$item['donationAmount'];
        $lineTotal = ($basePrice * $quantity) + $donationAmount;

        return new self(
            quantityDisplay: $quantity . '×',
            eventTitle: (string)$item['eventTitle'],
            priceDisplay: ProgramItemViewModel::formatPrice($lineTotal),
        );
    }
}
