<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Typed result returned by ProgramService::getProgramData().
 */
final readonly class ProgramData
{
    /**
     * @param ProgramItemData[] $items
     */
    public function __construct(
        public ?Program $program,
        public array $items,
        public float $subtotal,
        public float $taxAmount,
        public float $total,
    ) {
    }
}
