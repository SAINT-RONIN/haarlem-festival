<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Tickets;

/**
 * Immutable QR matrix used by PDF generation.
 *
 * @param bool[][] $modules Indexed as [row][column]
 */
final readonly class QrCodeMatrix
{
    /**
     * @param bool[][] $modules
     */
    public function __construct(
        public int $size,
        public array $modules,
    ) {}
}
