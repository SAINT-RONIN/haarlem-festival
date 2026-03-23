<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * A single event in the jazz schedule section — time, title, venue, and free/paid indicator.
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
