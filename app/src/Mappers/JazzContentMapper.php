<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Content\JazzArtistDetailCmsData;
use App\Content\JazzArtistsSectionContent;
use App\Content\JazzBookingCtaSectionContent;
use App\Content\JazzPricingSectionContent;
use App\Content\JazzScheduleCtaSectionContent;
use App\Content\JazzVenuesSectionContent;

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
            artistsGumboKingsName: $raw['artists_gumbokings_name'] ?? null,
            artistsGumboKingsGenre: $raw['artists_gumbokings_genre'] ?? null,
            artistsGumboKingsDescription: $raw['artists_gumbokings_description'] ?? null,
            artistsGumboKingsImage: $raw['artists_gumbokings_image'] ?? null,
            artistsGumboKingsPerformanceCount: $raw['artists_gumbokings_performance_count'] ?? null,
            artistsGumboKingsFirstPerformance: $raw['artists_gumbokings_first_performance'] ?? null,
            artistsGumboKingsMorePerformancesText: $raw['artists_gumbokings_more_performances_text'] ?? null,
            artistsGumboKingsProfileUrl: $raw['artists_gumbokings_profile_url'] ?? null,
            artistsEvolveName: $raw['artists_evolve_name'] ?? null,
            artistsEvolveGenre: $raw['artists_evolve_genre'] ?? null,
            artistsEvolveDescription: $raw['artists_evolve_description'] ?? null,
            artistsEvolveImage: $raw['artists_evolve_image'] ?? null,
            artistsEvolvePerformanceCount: $raw['artists_evolve_performance_count'] ?? null,
            artistsEvolveFirstPerformance: $raw['artists_evolve_first_performance'] ?? null,
            artistsEvolveMorePerformancesText: $raw['artists_evolve_more_performances_text'] ?? null,
            artistsEvolveProfileUrl: $raw['artists_evolve_profile_url'] ?? null,
            artistsNtjamName: $raw['artists_ntjam_name'] ?? null,
            artistsNtjamGenre: $raw['artists_ntjam_genre'] ?? null,
            artistsNtjamDescription: $raw['artists_ntjam_description'] ?? null,
            artistsNtjamImage: $raw['artists_ntjam_image'] ?? null,
            artistsNtjamPerformanceCount: $raw['artists_ntjam_performance_count'] ?? null,
            artistsNtjamFirstPerformance: $raw['artists_ntjam_first_performance'] ?? null,
            artistsNtjamMorePerformancesText: $raw['artists_ntjam_more_performances_text'] ?? null,
            artistsNtjamProfileUrl: $raw['artists_ntjam_profile_url'] ?? null,
        );
    }

    /** Maps raw CMS data to a JazzBookingCtaSectionContent model. */
    public static function mapBookingCta(array $raw): JazzBookingCtaSectionContent
    {
        return new JazzBookingCtaSectionContent(
            bookingCtaHeading: $raw['booking_cta_heading'] ?? null,
            bookingCtaDescription: $raw['booking_cta_description'] ?? null,
        );
    }

    /** Maps raw CMS data to a JazzArtistDetailCmsData model. */
    public static function mapArtistDetail(array $raw): JazzArtistDetailCmsData
    {
        return new JazzArtistDetailCmsData(
            heroSubtitle: $raw['hero_subtitle'] ?? null,
            heroBackgroundImage: $raw['hero_background_image'] ?? null,
            originText: $raw['origin_text'] ?? null,
            formedText: $raw['formed_text'] ?? null,
            performancesText: $raw['performances_text'] ?? null,
            heroBackButtonText: $raw['hero_back_button_text'] ?? null,
            heroBackButtonUrl: $raw['hero_back_button_url'] ?? null,
            heroReserveButtonText: $raw['hero_reserve_button_text'] ?? null,
            overviewHeading: $raw['overview_heading'] ?? null,
            overviewLead: $raw['overview_lead'] ?? null,
            overviewBodyPrimary: $raw['overview_body_primary'] ?? null,
            overviewBodySecondary: $raw['overview_body_secondary'] ?? null,
            lineupHeading: $raw['lineup_heading'] ?? null,
            highlightsHeading: $raw['highlights_heading'] ?? null,
            photoGalleryHeading: $raw['photo_gallery_heading'] ?? null,
            photoGalleryDescription: $raw['photo_gallery_description'] ?? null,
            albumsHeading: $raw['albums_heading'] ?? null,
            albumsDescription: $raw['albums_description'] ?? null,
            listenHeading: $raw['listen_heading'] ?? null,
            listenSubheading: $raw['listen_subheading'] ?? null,
            listenDescription: $raw['listen_description'] ?? null,
            listenPlayButtonLabel: $raw['listen_play_button_label'] ?? null,
            listenPlayExcerptText: $raw['listen_play_excerpt_text'] ?? null,
            listenTrackArtworkAltSuffix: $raw['listen_track_artwork_alt_suffix'] ?? null,
            liveCtaHeading: $raw['live_cta_heading'] ?? null,
            liveCtaDescription: $raw['live_cta_description'] ?? null,
            liveCtaBookButtonText: $raw['live_cta_book_button_text'] ?? null,
            liveCtaScheduleButtonText: $raw['live_cta_schedule_button_text'] ?? null,
            liveCtaScheduleButtonUrl: $raw['live_cta_schedule_button_url'] ?? null,
            performancesSectionId: $raw['performances_section_id'] ?? null,
            performancesHeading: $raw['performances_heading'] ?? null,
            performancesDescription: $raw['performances_description'] ?? null,
        );
    }
}
