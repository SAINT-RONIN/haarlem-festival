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
     * @param string[] $timeSlots Available session times
     * @param array{label: string, price: string}[] $priceCards Price info cards
     * @param string[] $validDates Valid festival days for the date selector
     */
    public function __construct(
        public string $title,
        public string $description,
        public string $slotsLabel,
        public string $note,
        public string $buttonText,
        public array $timeSlots,
        public array $priceCards,
        public string $reservationImage,
        public float $reservationFee,
        public array $validDates,
    ) {
    }
}
