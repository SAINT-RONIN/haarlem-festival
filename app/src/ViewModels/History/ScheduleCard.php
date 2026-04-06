<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * DTO for single schedule event data.
 */
final readonly class ScheduleCard
{
    public function __construct(
        public string $time,
        public array $languages,
        public string $venue,
        public string $date,
        public string $groupTicketInfo,
        public string $fromPrice,
    ) {
    }
}
