<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Carries CMS item values for the Jazz booking_cta_section.
 */
final readonly class JazzBookingCtaSectionContent
{
    public function __construct(
        public ?string $bookingCtaHeading,
        public ?string $bookingCtaDescription,
        public ?string $bookingContactEyebrow,
        public ?string $bookingContactTitle,
        public ?string $bookingContactDescription,
        public ?string $bookingContactPhoneOffice,
        public ?string $bookingContactPhoneCashDesk,
        public ?string $bookingContactHours,
        public ?string $bookingVenueEyebrow,
        public ?string $bookingVenueTitle,
        public ?string $bookingVenueDescription,
        public ?string $bookingTicketsEyebrow,
        public ?string $bookingTicketsTitle,
        public ?string $bookingTicketsDescription,
    ) {
    }
}
