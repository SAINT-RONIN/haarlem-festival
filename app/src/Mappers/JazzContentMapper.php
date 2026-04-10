<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Cms\JazzArtistsSectionContent;
use App\DTOs\Cms\JazzBookingCtaSectionContent;
use App\DTOs\Cms\JazzPricingSectionContent;
use App\DTOs\Cms\JazzScheduleCtaSectionContent;
use App\DTOs\Cms\JazzVenuesSectionContent;

/**
 * Maps raw CMS arrays into Jazz page content models.
 */
final class JazzContentMapper
{
    /** Maps raw CMS data to a JazzVenuesSectionContent model. */
    public static function mapVenues(array $raw): JazzVenuesSectionContent
    {
        return new JazzVenuesSectionContent(
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
            venueGrotemarktContact: $raw['venue_grotemarkt_contact'] ?? null,
            venueGrotemarktHallName: $raw['venue_grotemarkt_hall_name'] ?? null,
            venueGrotemarktHallDesc: $raw['venue_grotemarkt_hall_desc'] ?? null,
            venueGrotemarktHallPrice: $raw['venue_grotemarkt_hall_price'] ?? null,
            venueGrotemarktHallCapacity: $raw['venue_grotemarkt_hall_capacity'] ?? null,
        );
    }

    /** Maps raw CMS data to a JazzPricingSectionContent model. */
    public static function mapPricing(array $raw): JazzPricingSectionContent
    {
        return new JazzPricingSectionContent(
            pricingHeading: $raw['pricing_heading'] ?? null,
            pricingSubheading: $raw['pricing_subheading'] ?? null,
            pricingDescription: $raw['pricing_description'] ?? null,
            pricingIndividualTitle: $raw['pricing_individual_title'] ?? null,
            pricingIndividualItem1: $raw['pricing_individual_item1'] ?? null,
            pricingIndividualItem2: $raw['pricing_individual_item2'] ?? null,
            pricingIndividualItem3: $raw['pricing_individual_item3'] ?? null,
            pricingDaypassTitle: $raw['pricing_daypass_title'] ?? null,
            pricingDaypassPrice: $raw['pricing_daypass_price'] ?? null,
            pricingDaypassDesc: $raw['pricing_daypass_desc'] ?? null,
            pricingDaypassInclude1: $raw['pricing_daypass_include1'] ?? null,
            pricingDaypassInclude2: $raw['pricing_daypass_include2'] ?? null,
            pricingDaypassInclude3: $raw['pricing_daypass_include3'] ?? null,
            pricingDaypassInclude4: $raw['pricing_daypass_include4'] ?? null,
            pricingDaypassInfo: $raw['pricing_daypass_info'] ?? null,
            pricing3dayTitle: $raw['pricing_3day_title'] ?? null,
            pricing3dayPrice: $raw['pricing_3day_price'] ?? null,
            pricing3dayDesc: $raw['pricing_3day_desc'] ?? null,
            pricing3dayInclude1: $raw['pricing_3day_include1'] ?? null,
            pricing3dayInclude2: $raw['pricing_3day_include2'] ?? null,
            pricing3dayInclude3: $raw['pricing_3day_include3'] ?? null,
            pricing3dayInclude4: $raw['pricing_3day_include4'] ?? null,
            pricing3dayInfo: $raw['pricing_3day_info'] ?? null,
        );
    }

    /** Maps raw CMS data to a JazzScheduleCtaSectionContent model. */
    public static function mapScheduleCta(array $raw): JazzScheduleCtaSectionContent
    {
        return new JazzScheduleCtaSectionContent(
            scheduleCtaHeading: $raw['schedule_cta_heading'] ?? null,
            scheduleCtaDescription: $raw['schedule_cta_description'] ?? null,
            scheduleCtaButton: $raw['schedule_cta_button'] ?? null,
            scheduleCtaButtonLink: $raw['schedule_cta_button_link'] ?? null,
        );
    }

    /** Maps raw CMS data to a JazzArtistsSectionContent model. */
    public static function mapArtists(array $raw): JazzArtistsSectionContent
    {
        return new JazzArtistsSectionContent(
            artistsHeading: $raw['artists_heading'] ?? null,
        );
    }

    /** Maps raw CMS data to a JazzBookingCtaSectionContent model. */
    public static function mapBookingCta(array $raw): JazzBookingCtaSectionContent
    {
        return new JazzBookingCtaSectionContent(
            bookingCtaHeading: $raw['booking_cta_heading'] ?? null,
            bookingCtaDescription: $raw['booking_cta_description'] ?? null,
            bookingContactEyebrow: $raw['booking_contact_eyebrow'] ?? null,
            bookingContactTitle: $raw['booking_contact_title'] ?? null,
            bookingContactDescription: $raw['booking_contact_description'] ?? null,
            bookingContactPhoneOffice: $raw['booking_contact_phone_office'] ?? null,
            bookingContactPhoneCashDesk: $raw['booking_contact_phone_cash_desk'] ?? null,
            bookingContactHours: $raw['booking_contact_hours'] ?? null,
            bookingVenueEyebrow: $raw['booking_venue_eyebrow'] ?? null,
            bookingVenueTitle: $raw['booking_venue_title'] ?? null,
            bookingVenueDescription: $raw['booking_venue_description'] ?? null,
            bookingTicketsEyebrow: $raw['booking_tickets_eyebrow'] ?? null,
            bookingTicketsTitle: $raw['booking_tickets_title'] ?? null,
            bookingTicketsDescription: $raw['booking_tickets_description'] ?? null,
        );
    }
}
