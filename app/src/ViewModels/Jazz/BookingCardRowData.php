<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * A single row inside a jazz booking card.
 *
 * @param string[] $lines
 */
final readonly class BookingCardRowData
{
    public function __construct(
        public string $icon,
        public array $lines,
    ) {
    }
}
