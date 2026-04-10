<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * A single session entry in the homepage schedule preview.
 */
final readonly class HomeScheduleSessionViewModel
{
    public function __construct(
        public string $timeLabel,
        public string $title,
        public string $categoryLabel,
        public string $borderClass,
    ) {}
}
