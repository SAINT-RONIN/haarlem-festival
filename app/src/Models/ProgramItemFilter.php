<?php

declare(strict_types=1);

namespace App\Models;

final readonly class ProgramItemFilter
{
    public function __construct(
        public ?int $programItemId = null,
        public ?int $programId = null,
        public ?int $eventSessionId = null,
    ) {}
}
