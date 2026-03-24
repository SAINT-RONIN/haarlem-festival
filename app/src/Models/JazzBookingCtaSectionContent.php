<?php

declare(strict_types=1);

namespace App\Models;

/**
 * CMS content for the jazz page booking call-to-action section.
 * Hydrated from CMS key-value pairs.
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
