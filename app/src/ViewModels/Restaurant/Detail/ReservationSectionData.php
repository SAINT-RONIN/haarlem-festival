<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant\Detail;

/**
 * ViewModel for the "Make your Reservation" section on the restaurant detail page.
 */
final readonly class ReservationSectionData
{
    /**
     * @param string[]                          $timeSlots  Available session times
     * @param array{label: string, price: string}[] $priceCards Price info cards
     */
    public function __construct(
        public string $image,
        public array  $timeSlots,
        public array  $priceCards,
        public int    $durationMinutes,
        public int    $seatsPerSession,

        // CMS labels
        public string $labelTitle    = 'Make your Reservation',
        public string $labelDesc     = '',
        public string $labelSlots    = 'AVAILABLE TIME SLOTS',
        public string $labelNote     = '',
        public string $labelButton   = 'Continue to Reservation',
        public string $labelDuration = 'Duration',
        public string $labelSeats    = 'Seats',
    ) {
    }
}
