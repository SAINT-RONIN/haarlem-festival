<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant;

/**
 * ViewModel for a single instruction card in the "How reservations work" section.
 */
final readonly class InstructionCardData
{
    public function __construct(
        public string $number,
        public string $title,
        public string $text,
        public string $icon,
    ) {}
}
