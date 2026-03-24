<?php

declare(strict_types=1);

namespace App\Models;

final readonly class ActivityData
{
    public function __construct(
        public string $icon,
        public string $text,
        public string $time,
        public string $color,
    ) {}
}
