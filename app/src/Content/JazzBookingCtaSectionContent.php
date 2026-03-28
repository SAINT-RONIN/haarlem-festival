<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Carries CMS item values for the Jazz booking_cta_section.
 */
final readonly class JazzBookingCtaSectionContent
{
    public function __construct(
        public ?string $bookingCtaHeading,
        public ?string $bookingCtaDescription,
    ) {
    }
}
