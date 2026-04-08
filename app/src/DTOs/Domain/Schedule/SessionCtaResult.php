<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

/** Resolved call-to-action label and URL for a single session card. */
final readonly class SessionCtaResult
{
    public function __construct(
        public string $label,
        public string $url,
    ) {}
}
