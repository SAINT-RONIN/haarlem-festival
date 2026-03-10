<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant;

/**
 * ViewModel for the "How reservations work" section.
 */
final readonly class InstructionsSectionData
{
    /**
     * @param string $title Section heading
     * @param InstructionCardData[] $cards Instruction step cards
     */
    public function __construct(
        public string $title,
        public array $cards,
    ) {
    }
}
