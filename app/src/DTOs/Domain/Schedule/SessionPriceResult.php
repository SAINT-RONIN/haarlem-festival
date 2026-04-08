<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

/** Resolved display price for a single session. */
final readonly class SessionPriceResult
{
    public function __construct(
        public ?float $amount,
        public bool $isPayWhatYouLike,
    ) {}
}
