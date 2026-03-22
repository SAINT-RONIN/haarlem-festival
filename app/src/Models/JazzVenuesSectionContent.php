<?php

declare(strict_types=1);

namespace App\Models;

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
        public ?string $venueGrotemarktHallName,
        public ?string $venueGrotemarktHallDesc,
        public ?string $venueGrotemarktHallPrice,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            venuesHeading: $raw['venues_heading'] ?? null,
            venuesSubheading: $raw['venues_subheading'] ?? null,
            venuesDescription: $raw['venues_description'] ?? null,
            venuePatronaatName: $raw['venue_patronaat_name'] ?? null,
            venuePatronaatAddress1: $raw['venue_patronaat_address1'] ?? null,
            venuePatronaatAddress2: $raw['venue_patronaat_address2'] ?? null,
            venuePatronaatContact: $raw['venue_patronaat_contact'] ?? null,
            venuePatronaatHall1Name: $raw['venue_patronaat_hall1_name'] ?? null,
            venuePatronaatHall1Desc: $raw['venue_patronaat_hall1_desc'] ?? null,
            venuePatronaatHall1Price: $raw['venue_patronaat_hall1_price'] ?? null,
            venuePatronaatHall1Capacity: $raw['venue_patronaat_hall1_capacity'] ?? null,
            venuePatronaatHall2Name: $raw['venue_patronaat_hall2_name'] ?? null,
            venuePatronaatHall2Desc: $raw['venue_patronaat_hall2_desc'] ?? null,
            venuePatronaatHall2Price: $raw['venue_patronaat_hall2_price'] ?? null,
            venuePatronaatHall2Capacity: $raw['venue_patronaat_hall2_capacity'] ?? null,
            venuePatronaatHall3Name: $raw['venue_patronaat_hall3_name'] ?? null,
            venuePatronaatHall3Desc: $raw['venue_patronaat_hall3_desc'] ?? null,
            venuePatronaatHall3Price: $raw['venue_patronaat_hall3_price'] ?? null,
            venuePatronaatHall3Capacity: $raw['venue_patronaat_hall3_capacity'] ?? null,
            venueGrotemarktName: $raw['venue_grotemarkt_name'] ?? null,
            venueGrotemarktLocation1: $raw['venue_grotemarkt_location1'] ?? null,
            venueGrotemarktLocation2: $raw['venue_grotemarkt_location2'] ?? null,
            venueGrotemarktHallName: $raw['venue_grotemarkt_hall_name'] ?? null,
            venueGrotemarktHallDesc: $raw['venue_grotemarkt_hall_desc'] ?? null,
            venueGrotemarktHallPrice: $raw['venue_grotemarkt_hall_price'] ?? null,
        );
    }
}
