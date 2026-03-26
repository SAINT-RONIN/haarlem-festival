<?php

declare(strict_types=1);

namespace App\Models;

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
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            scheduleCtaHeading: $raw['schedule_cta_heading'] ?? null,
            scheduleCtaDescription: $raw['schedule_cta_description'] ?? null,
            scheduleCtaButton: $raw['schedule_cta_button'] ?? null,
            scheduleCtaButtonLink: $raw['schedule_cta_button_link'] ?? null,
        );
    }
}
