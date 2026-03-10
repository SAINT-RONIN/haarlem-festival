<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * DTO for schedule call-to-action section.
 */
final readonly class ScheduleCallToActionData
{
    public function __construct(
        public string $headingText,
        public string $descriptionText,
        public string $buttonText,
        public string $buttonLink,
    ) {
    }
}
