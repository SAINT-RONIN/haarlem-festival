<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Carries CMS item values for the Jazz schedule_cta_section.
 */
final readonly class JazzScheduleCtaSectionContent
{
    public function __construct(
        public ?string $scheduleCtaHeading,
        public ?string $scheduleCtaDescription,
        public ?string $scheduleCtaButton,
        public ?string $scheduleCtaButtonLink,
    ) {
    }
}
