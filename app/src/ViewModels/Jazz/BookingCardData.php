<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * A single card inside the jazz booking section.
 *
 * @param BookingCardRowData[] $rows
 */
final readonly class BookingCardData
{
    public function __construct(
        public string $eyebrowText,
        public string $titleText,
        public string $descriptionText,
        public array $rows,
        public bool $isHighlighted = false,
    ) {}
}
