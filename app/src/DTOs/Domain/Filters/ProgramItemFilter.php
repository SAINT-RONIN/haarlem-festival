<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Filters;

/**
 * Query parameters for ProgramRepository item queries.
 */
final readonly class ProgramItemFilter
{
    public function __construct(
        public ?int $programItemId = null,
        public ?int $programId = null,
        public ?int $eventSessionId = null,
        public ?int $priceTierId = null,
    ) {}
}
