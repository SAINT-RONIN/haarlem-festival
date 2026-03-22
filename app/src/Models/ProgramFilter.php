<?php

declare(strict_types=1);

namespace App\Models;

final readonly class ProgramFilter
{
    public function __construct(
        public ?int $programId = null,
        public ?int $userAccountId = null,
        public ?string $sessionKey = null,
        public ?bool $isCheckedOut = null,
    ) {}
}
