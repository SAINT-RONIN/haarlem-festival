<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Carries CMS item values for the Jazz venues_section.
 */
final readonly class JazzVenuesSectionContent
{
    public function __construct(
        public ?string $venuesHeading,
        public ?string $venuesSubheading,
        public ?string $venuesDescription,
        public ?string $venuePatronaatName,
        public ?string $venuePatronaatAddress1,
        public ?string $venuePatronaatAddress2,
        public ?string $venuePatronaatContact,
        public ?string $venuePatronaatHall1Name,
        public ?string $venuePatronaatHall1Desc,
        public ?string $venuePatronaatHall1Price,
        public ?string $venuePatronaatHall1Capacity,
        public ?string $venuePatronaatHall2Name,
        public ?string $venuePatronaatHall2Desc,
        public ?string $venuePatronaatHall2Price,
        public ?string $venuePatronaatHall2Capacity,
        public ?string $venuePatronaatHall3Name,
        public ?string $venuePatronaatHall3Desc,
        public ?string $venuePatronaatHall3Price,
        public ?string $venuePatronaatHall3Capacity,
        public ?string $venueGrotemarktName,
        public ?string $venueGrotemarktLocation1,
        public ?string $venueGrotemarktLocation2,
        public ?string $venueGrotemarktContact,
        public ?string $venueGrotemarktHallName,
        public ?string $venueGrotemarktHallDesc,
        public ?string $venueGrotemarktHallPrice,
        public ?string $venueGrotemarktHallCapacity,
    ) {
    }
}
