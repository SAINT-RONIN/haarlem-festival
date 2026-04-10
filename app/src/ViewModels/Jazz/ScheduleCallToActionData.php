<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * CTA banner data for the jazz page schedule section.
 */
final readonly class ScheduleCallToActionData
{
    public function __construct(
        public string $headingText,
        public string $descriptionText,
        public string $buttonText,
        public string $buttonLink,
    ) {}
}
