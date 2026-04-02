<?php

declare(strict_types=1);

namespace App\DTOs\Program;

use App\Models\Program;

/**
 * A visitor's program (cart) with resolved item details.
 * Assembled by ProgramService for the my-program page.
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
        public bool $canCheckout,
    ) {
    }
}
