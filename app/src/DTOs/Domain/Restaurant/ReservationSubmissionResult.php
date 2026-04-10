<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Restaurant;

final readonly class ReservationSubmissionResult
{
    public function __construct(
        public int $reservationId,
    ) {}
}
