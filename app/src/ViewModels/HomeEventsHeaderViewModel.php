<?php

declare(strict_types=1);

namespace App\ViewModels;

final readonly class HomeEventsHeaderViewModel
{
    public function __construct(
        public string $title,
        public string $subtitle,
    ) {
    }
}
