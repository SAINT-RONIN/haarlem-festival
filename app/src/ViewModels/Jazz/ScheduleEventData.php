<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * DTO for single schedule event data.
 */
final readonly class ScheduleEventData
{
    public function __construct(
        public string $artistName,
        public string $genre,
        public string $venue,
        public string $date,
        public string $time,
        public string $price,
        public bool $isFree = false,
    ) {
    }
}
