<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant\Detail;

/**
 * ViewModel for the "Make your Reservation" section on the restaurant detail page
 * and the standalone reservation form page.
 */
final readonly class ReservationSectionData
{
    /**
     * @param string[]                              $timeSlots            Available session times
     * @param array{label: string, price: string}[] $priceCards           Price info cards
     * @param string[]                              $festivalDates        Valid festival days for the date selector
     */
    public function __construct(
        public string $image,
        public array  $timeSlots,
        public array  $priceCards,
        public int    $durationMinutes,
        public int    $seatsPerSession,
        public ?float $priceAdult,
        public ?float $priceChild,
        public array  $festivalDates,
        public float  $reservationFeePerPerson,

        // Pre-formatted display strings (computed by RestaurantMapper)
        public string $durationFormatted = '',   // e.g. "2 hours"
        public string $seatsFormatted    = '',   // e.g. "40 per session"

        // CMS labels
        public string $labelTitle    = 'Make your Reservation',
        public string $labelDesc     = '',
        public string $labelSlots    = 'AVAILABLE TIME SLOTS',
        public string $labelNote     = '',
        public string $labelButton   = 'Continue to Reservation',
        public string $labelDuration = 'Duration',
        public string $labelSeats    = 'Seats',

        // Reservation form labels
        public string $labelSuccess                  = 'Your reservation has been submitted! We will confirm your booking shortly.',
        public string $labelErrorHeading             = 'Please fix the following:',
        public string $labelDate                     = 'Date',
        public string $labelSelectDay                = 'Select a day',
        public string $labelTime                     = 'Time',
        public string $labelSelectTime               = 'Select a time',
        public string $labelGuestsTitle              = 'Number of Guests',
        public string $labelAdult                    = 'Adult',
        public string $labelChildren                 = 'Children',
        public string $labelSpecialRequests          = 'Special requests',
        public string $labelSpecialRequestsDesc      = 'Diet, allergies, accessibility needs',
        public string $labelSpecialRequestsNote      = 'Let us know if you have any special requirements',
        public string $labelTotalTitle               = 'Total to be paid',
        public string $labelFeeNote                  = 'To complete your reservation, you pay a {fee} fee per person. This amount is deducted from your final bill at the restaurant, so you simply pay the remaining amount after your meal.',
        public string $labelBack                     = 'Back to Restaurant',
    ) {
    }
}
