<?php

declare(strict_types=1);

namespace App\ViewModels;

final readonly class HomeScheduleSessionViewModel
{
    public function __construct(
        public string $timeLabel,
        public string $title,
        public string $categoryLabel,
        public string $borderClass,
    ) {
    }
}
