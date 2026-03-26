<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the Jazz booking_cta_section.
 */
final readonly class JazzBookingCtaSectionContent
{
    public function __construct(
        public ?string $bookingCtaHeading,
        public ?string $bookingCtaDescription,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            bookingCtaHeading: $raw['booking_cta_heading'] ?? null,
            bookingCtaDescription: $raw['booking_cta_description'] ?? null,
        );
    }
}
