<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

/** CMS-resolved button labels for the add-to-program interaction on schedule cards. */
final readonly class ScheduleButtonTexts
{
    public function __construct(
        public string $confirm,
        public string $adding,
        public string $success,
    ) {}
}
