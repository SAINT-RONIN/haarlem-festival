<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\JazzArtistDetailConstants;
use App\Constants\JazzPageConstants;
use App\Models\JazzArtistDetailEvent;
use App\Models\JazzArtistDetailPageData;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Jazz\ArtistCardData;
use App\ViewModels\Jazz\ArtistsData;
use App\ViewModels\Jazz\BookingCallToActionData;
use App\ViewModels\Jazz\HallData;
use App\ViewModels\Jazz\JazzArtistAlbumData;
use App\ViewModels\Jazz\JazzArtistDetailPageViewModel;
use App\ViewModels\Jazz\JazzArtistTrackData;
use App\ViewModels\Jazz\JazzPageViewModel;
use App\ViewModels\Jazz\PricingCardData;
use App\ViewModels\Jazz\PricingData;
use App\ViewModels\Jazz\ScheduleCallToActionData;
use App\ViewModels\Jazz\ScheduleDayData;
use App\ViewModels\Jazz\ScheduleData;
use App\ViewModels\Jazz\ScheduleEventData;
use App\ViewModels\Jazz\VenueData;
use App\ViewModels\Jazz\VenuesData;
use App\Mappers\ScheduleMapper;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

class JazzMapper
{
    /**
     * @param array<string, mixed> $domain
     */
    public static function toPageViewModel(array $domain, GlobalUiData $globalUi, ?ScheduleSectionViewModel $scheduleSection = null): JazzPageViewModel
    {
        $sections = is_array($domain['sections'] ?? null) ? $domain['sections'] : [];
        $scheduleSectionData = is_array($domain['scheduleSectionData'] ?? null)
            ? $domain['scheduleSectionData']
            : [];

        $mapped = [
            'heroData' => self::buildHeroData($sections),
            'gradientSection' => self::buildGradientSectionData($sections),
            'introSplitSection' => self::buildIntroData($sections),
            'venuesData' => self::buildVenuesData($sections),
            'pricingData' => self::buildPricingData($sections),
            'scheduleCtaData' => self::buildScheduleCtaData($sections),
            'artistsData' => self::buildArtistsData($sections),
            'scheduleData' => self::buildScheduleData($scheduleSectionData),
            'bookingCtaData' => self::buildBookingCtaData($sections),
        ];

        $hero = $mapped['heroData'] ?? [];
        $gradient = $mapped['gradientSection'] ?? [];
        $intro = $mapped['introSplitSection'] ?? [];
        $venues = $mapped['venuesData'] ?? [];
        $pricing = $mapped['pricingData'] ?? [];
        $scheduleCta = $mapped['scheduleCtaData'] ?? [];
        $artists = $mapped['artistsData'] ?? [];
        $schedule = $mapped['scheduleData'] ?? [];
        $booking = $mapped['bookingCtaData'] ?? [];

        $heroData = new HeroData(...$hero);

        return new JazzPageViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            cms: CmsMapper::toCmsData($heroData, $globalUi),
            gradientSection: new GradientSectionData(...$gradient),
            introSplitSection: new IntroSplitSectionData(...$intro),
            venuesData: self::mapVenuesData($venues),
            pricingData: self::mapPricingData($pricing),
            scheduleCtaData: new ScheduleCallToActionData(...$scheduleCta),
            artistsData: self::mapArtistsData($artists),
            scheduleData: self::mapScheduleData($schedule),
            bookingCtaData: new BookingCallToActionData(...$booking),
            scheduleSection: $scheduleSection,
        );
    }

    /**
     * @param array<array<string, mixed>> $performances
     */
    public static function toArtistDetailViewModel(JazzArtistDetailPageData $pageData, array $performances): JazzArtistDetailPageViewModel
    {
        $event = $pageData->event;
        $cms = $pageData->cms;

        $mappedData = [
            'heroTitle' => $event->title,
            'heroSubtitle' => self::coalesce(
                self::cmsValue($cms, 'hero_subtitle'),
                $event->shortDescription,
            ),
            'heroBackgroundImageUrl' => self::cmsValue($cms, 'hero_background_image'),
            'originText' => self::cmsValue($cms, 'origin_text'),
            'formedText' => self::cmsValue($cms, 'formed_text'),
            'performancesText' => self::cmsValue($cms, 'performances_text'),
            'heroBackButtonText' => self::cmsValue($cms, 'hero_back_button_text'),
            'heroBackButtonUrl' => self::cmsValue($cms, 'hero_back_button_url'),
            'heroReserveButtonText' => self::cmsValue($cms, 'hero_reserve_button_text'),
            'overviewHeading' => self::coalesce(self::cmsValue($cms, 'overview_heading'), $event->title),
            'overviewLead' => self::coalesce(
                self::cmsValue($cms, 'overview_lead'),
                $event->shortDescription,
            ),
            'overviewBodyPrimary' => self::coalesce(
                self::cmsValue($cms, 'overview_body_primary'),
                self::buildPrimaryOverviewFallbackFromModel($event),
            ),
            'overviewBodySecondary' => self::cmsValue($cms, 'overview_body_secondary'),
            'lineupHeading' => self::cmsValue($cms, 'lineup_heading'),
            'lineup' => self::collectTextList(
                $cms,
                JazzArtistDetailConstants::LINEUP_PREFIX,
                JazzArtistDetailConstants::MAX_LIST_ITEMS,
            ),
            'highlightsHeading' => self::cmsValue($cms, 'highlights_heading'),
            'highlights' => self::collectTextList(
                $cms,
                JazzArtistDetailConstants::HIGHLIGHT_PREFIX,
                JazzArtistDetailConstants::MAX_LIST_ITEMS,
            ),
            'photoGalleryHeading' => self::cmsValue($cms, 'photo_gallery_heading'),
            'photoGalleryDescription' => self::cmsValue($cms, 'photo_gallery_description'),
            'galleryImages' => self::collectTextList(
                $cms,
                JazzArtistDetailConstants::GALLERY_IMAGE_PREFIX,
                JazzArtistDetailConstants::MAX_LIST_ITEMS,
            ),
            'albumsHeading' => self::cmsValue($cms, 'albums_heading'),
            'albumsDescription' => self::cmsValue($cms, 'albums_description'),
            'albums' => self::buildAlbums($cms),
            'listenHeading' => self::cmsValue($cms, 'listen_heading'),
            'listenSubheading' => self::cmsValue($cms, 'listen_subheading'),
            'listenDescription' => self::cmsValue($cms, 'listen_description'),
            'listenPlayButtonLabel' => self::cmsValue($cms, 'listen_play_button_label'),
            'listenPlayExcerptText' => self::cmsValue($cms, 'listen_play_excerpt_text'),
            'listenTrackArtworkAltSuffix' => self::cmsValue(
                $cms,
                'listen_track_artwork_alt_suffix',
            ),
            'tracks' => self::buildTracks($cms),
            'liveCtaHeading' => self::cmsValue($cms, 'live_cta_heading'),
            'liveCtaDescription' => self::cmsValue($cms, 'live_cta_description'),
            'liveCtaBookButtonText' => self::cmsValue($cms, 'live_cta_book_button_text'),
            'liveCtaScheduleButtonText' => self::cmsValue($cms, 'live_cta_schedule_button_text'),
            'liveCtaScheduleButtonUrl' => self::cmsValue($cms, 'live_cta_schedule_button_url'),
            'performancesSectionId' => self::cmsValue($cms, 'performances_section_id'),
            'performancesHeading' => self::cmsValue($cms, 'performances_heading'),
            'performancesDescription' => self::cmsValue($cms, 'performances_description'),
            'performances' => $performances,
        ];

        return self::fromMappedArtistData($mappedData);
    }

    /** @param array<string, mixed> $sections */
    private static function buildHeroData(array $sections): array
    {
        $section = self::section($sections, JazzPageConstants::SECTION_HERO);

        return [
            'mainTitle' => self::value($section, 'hero_main_title', 'HAARLEM JAZZ'),
            'subtitle' => self::value($section, 'hero_subtitle', 'Experience world-class jazz performances'),
            'primaryButtonText' => self::value($section, 'hero_button_primary', 'Discover all performances'),
            'primaryButtonLink' => self::value($section, 'hero_button_primary_link', '#artists'),
            'secondaryButtonText' => self::value($section, 'hero_button_secondary', 'What is Haarlem Jazz?'),
            'secondaryButtonLink' => self::value($section, 'hero_button_secondary_link', '#intro'),
            'backgroundImageUrl' => self::value(
                $section,
                'hero_background_image',
                JazzPageConstants::DEFAULT_HERO_BACKGROUND_IMAGE,
            ),
            'currentPage' => JazzPageConstants::CURRENT_PAGE,
        ];
    }

    /** @param array<string, mixed> $sections */
    private static function buildGradientSectionData(array $sections): array
    {
        $section = self::section($sections, JazzPageConstants::SECTION_GRADIENT);

        return [
            'headingText' => self::value($section, 'gradient_heading', 'Every note carries emotion'),
            'subheadingText' => self::value($section, 'gradient_subheading', 'A place where jazz is experienced'),
            'backgroundImageUrl' => self::value(
                $section,
                'gradient_background_image',
                JazzPageConstants::DEFAULT_GRADIENT_BACKGROUND_IMAGE,
            ),
        ];
    }

    /** @param array<string, mixed> $sections */
    private static function buildIntroData(array $sections): array
    {
        $section = self::section($sections, JazzPageConstants::SECTION_INTRO);

        return [
            'headingText' => self::value($section, 'intro_heading', 'Haarlem moves to the rhythm of jazz'),
            'bodyText' => self::value($section, 'intro_body', 'Welcome to Haarlem Jazz 2026'),
            'imageUrl' => self::value($section, 'intro_image', JazzPageConstants::DEFAULT_INTRO_IMAGE),
            'imageAltText' => self::value(
                $section,
                'intro_image_alt',
                JazzPageConstants::DEFAULT_INTRO_IMAGE_ALT,
            ),
            'subsections' => null,
            'closingLine' => null,
        ];
    }

    /** @param array<string, mixed> $sections */
    private static function buildVenuesData(array $sections): array
    {
        $section = self::section($sections, JazzPageConstants::SECTION_VENUES);

        return [
            'headingText' => self::value($section, 'venues_heading', 'Festival venues'),
            'subheadingText' => self::value($section, 'venues_subheading', 'Performance Locations'),
            'descriptionText' => self::value(
                $section,
                'venues_description',
                'Haarlem Jazz 2026 takes place at two main locations',
            ),
            'venues' => [
                [
                    'name' => self::value($section, 'venue_patronaat_name', 'Patronaat'),
                    'addressLine1' => self::value($section, 'venue_patronaat_address1', 'Zijlsingel 2'),
                    'addressLine2' => self::value($section, 'venue_patronaat_address2', '2013 DN Haarlem'),
                    'contactInfo' => self::value($section, 'venue_patronaat_contact', 'E-mail/reception available'),
                    'halls' => [
                        self::hallData($section, 'venue_patronaat_hall1', 'First Hall'),
                        self::hallData($section, 'venue_patronaat_hall2', 'Second Hall'),
                        self::hallData($section, 'venue_patronaat_hall3', 'Third Hall'),
                    ],
                    'isDark' => false,
                ],
                [
                    'name' => self::value($section, 'venue_grotemarkt_name', 'Grote Markt'),
                    'addressLine1' => self::value($section, 'venue_grotemarkt_location1', 'Historic Market Square'),
                    'addressLine2' => self::value($section, 'venue_grotemarkt_location2', 'Haarlem City Center'),
                    'contactInfo' => '',
                    'halls' => [[
                        'name' => self::value($section, 'venue_grotemarkt_hall_name', 'Open Air Stage'),
                        'description' => self::value(
                            $section,
                            'venue_grotemarkt_hall_desc',
                            'Sunday performances are free',
                        ),
                        'price' => self::value($section, 'venue_grotemarkt_hall_price', 'FREE ENTRY'),
                        'capacity' => '',
                        'isFree' => true,
                    ]],
                    'isDark' => false,
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $section */
    private static function hallData(array $section, string $prefix, string $defaultName): array
    {
        return [
            'name' => self::value($section, $prefix . '_name', $defaultName),
            'description' => self::value($section, $prefix . '_desc', 'Intimate performances'),
            'price' => '€10.00',
            'capacity' => self::value($section, $prefix . '_capacity', '150 seats'),
            'isFree' => false,
        ];
    }

    /** @param array<string, mixed> $sections */
    private static function buildPricingData(array $sections): array
    {
        $section = self::section($sections, JazzPageConstants::SECTION_PRICING);

        return [
            'headingText' => self::value($section, 'pricing_heading', 'Pricing information'),
            'subheadingText' => self::value($section, 'pricing_subheading', 'Tickets & Passes'),
            'descriptionText' => self::value($section, 'pricing_description', 'We offer flexible ticketing options'),
            'pricingCards' => [
                [
                    'title' => self::value($section, 'pricing_individual_title', 'Individual Show Tickets'),
                    'price' => '',
                    'priceDescription' => '',
                    'items' => [
                        self::value($section, 'pricing_individual_item1', 'Main Hall Shows'),
                        self::value($section, 'pricing_individual_item2', 'Second Hall Shows'),
                        self::value($section, 'pricing_individual_item3', 'Third Hall Shows'),
                    ],
                    'includes' => [],
                    'additionalInfo' => '',
                    'isHighlighted' => false,
                ],
                [
                    'title' => self::value($section, 'pricing_daypass_title', 'All-Access Day Pass'),
                    'price' => self::value($section, 'pricing_daypass_price', '€35.00'),
                    'priceDescription' => self::value($section, 'pricing_daypass_desc', 'Per day'),
                    'items' => [],
                    'includes' => [
                        self::value($section, 'pricing_daypass_include1', 'Unlimited access'),
                        self::value($section, 'pricing_daypass_include2', 'All performances'),
                        self::value($section, 'pricing_daypass_include3', 'Thu, Fri, or Sat'),
                        self::value($section, 'pricing_daypass_include4', 'Best value'),
                    ],
                    'additionalInfo' => self::value($section, 'pricing_daypass_info', ''),
                    'isHighlighted' => false,
                ],
                [
                    'title' => self::value($section, 'pricing_3day_title', 'All-Access Day Pass'),
                    'price' => self::value($section, 'pricing_3day_price', '€80.00'),
                    'priceDescription' => self::value(
                        $section,
                        'pricing_3day_desc',
                        'Thursday + Friday + Saturday',
                    ),
                    'items' => [],
                    'includes' => [
                        self::value($section, 'pricing_3day_include1', 'Unlimited access all 3 days'),
                        self::value($section, 'pricing_3day_include2', 'All venues'),
                        self::value($section, 'pricing_3day_include3', '18+ performances'),
                        self::value($section, 'pricing_3day_include4', 'Save €25'),
                    ],
                    'additionalInfo' => self::value($section, 'pricing_3day_info', ''),
                    'isHighlighted' => true,
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $sections */
    private static function buildScheduleCtaData(array $sections): array
    {
        $section = self::section($sections, JazzPageConstants::SECTION_SCHEDULE_CTA);

        return [
            'headingText' => self::value($section, 'schedule_cta_heading', 'Ready to Plan Your Festival Experience?'),
            'descriptionText' => self::value($section, 'schedule_cta_description', 'Check out the complete schedule'),
            'buttonText' => self::value($section, 'schedule_cta_button', 'View complete schedule'),
            'buttonLink' => self::value($section, 'schedule_cta_button_link', '#schedule'),
        ];
    }

    /** @param array<string, mixed> $sections */
    private static function buildArtistsData(array $sections): array
    {
        $section = self::section($sections, JazzPageConstants::SECTION_ARTISTS);

        return [
            'headingText' => self::value($section, 'artists_heading', 'Discover our lineup'),
            'artists' => [
                self::artistData(
                    $section,
                    'artists_gumbokings',
                    'Gumbo Kings',
                    'New Orleans Jazz',
                    'High-energy New Orleans style jazz band bringing authentic Big Easy sound to Haarlem. '
                    . 'Known for infectious rhythms.',
                    JazzPageConstants::DEFAULT_GUMBO_KINGS_IMAGE,
                    'Thu 18:00 - Patronaat Main Hall',
                    '/jazz/gumbo-kings',
                ),
                self::artistData(
                    $section,
                    'artists_evolve',
                    'Evolve',
                    'Contemporary Jazz',
                    'Progressive jazz ensemble pushing boundaries with innovative compositions. '
                    . 'A fresh take on modern jazz traditions.',
                    JazzPageConstants::DEFAULT_EVOLVE_IMAGE,
                    'Thu 18:00 - Patronaat Main Hall',
                    null,
                ),
                self::artistData(
                    $section,
                    'artists_ntjam',
                    'Ntjam Rosie',
                    'Vocal Jazz',
                    'Sultry vocals meet classic jazz standards. Rosie brings timeless elegance '
                    . 'and powerful vocal performances to every show.',
                    JazzPageConstants::DEFAULT_NTJAM_IMAGE,
                    'Thu 21:00 - Patronaat Main Hall',
                    '/jazz/ntjam-rosie',
                ),
            ],
            'currentPage' => 1,
            'totalPages' => 4,
            'totalArtists' => 12,
        ];
    }

    /**
     * @param array<string, mixed> $section
     */
    private static function artistData(
        array $section,
        string $prefix,
        string $defaultName,
        string $defaultGenre,
        string $defaultDescription,
        string $defaultImage,
        string $defaultFirstPerformance,
        ?string $defaultProfileUrl,
    ): array {
        return [
            'name' => self::value($section, $prefix . '_name', $defaultName),
            'genre' => self::value($section, $prefix . '_genre', $defaultGenre),
            'description' => self::value($section, $prefix . '_description', $defaultDescription),
            'imageUrl' => self::value($section, $prefix . '_image', $defaultImage),
            'performanceCount' => self::intValue($section, $prefix . '_performance_count', 2),
            'firstPerformance' => self::value($section, $prefix . '_first_performance', $defaultFirstPerformance),
            'morePerformancesText' => self::value($section, $prefix . '_more_performances_text', '+1 more'),
            'profileUrl' => self::nullableValue($section, $prefix . '_profile_url', $defaultProfileUrl),
        ];
    }

    /** @param array<string, mixed> $scheduleSectionData */
    private static function buildScheduleData(array $scheduleSectionData): array
    {
        $days = [];
        $totalEvents = 0;

        foreach (($scheduleSectionData['days'] ?? []) as $day) {
            if (!is_array($day)) {
                continue;
            }

            $events = [];
            foreach (($day['events'] ?? []) as $event) {
                if (!is_array($event)) {
                    continue;
                }

                $events[] = [
                    'artistName' => (string)($event['artistName'] ?? $event['title'] ?? ''),
                    'genre' => (string)($event['eventTypeSlug'] ?? ''),
                    'venue' => (string)($event['locationName'] ?? ''),
                    'date' => (string)($event['dateDisplay'] ?? ''),
                    'time' => (string)($event['timeDisplay'] ?? ''),
                    'price' => (string)($event['priceDisplay'] ?? ''),
                    'isFree' => false,
                ];
            }

            $totalEvents += count($events);
            $days[] = [
                'dayName' => (string)($day['dayName'] ?? ''),
                'fullDate' => (string)($day['dateFormatted'] ?? ''),
                'events' => $events,
            ];
        }

        $year = (string)date('Y');
        $firstIsoDate = (string)($scheduleSectionData['days'][0]['isoDate'] ?? '');
        if ($firstIsoDate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $firstIsoDate) === 1) {
            $year = substr($firstIsoDate, 0, 4);
        }

        return [
            'headingText' => 'Performance schedule',
            'year' => $year,
            'filterLabel' => 'Filters',
            'totalEventsText' => $totalEvents . ' Events',
            'days' => $days,
        ];
    }

    /** @param array<string, mixed> $sections */
    private static function buildBookingCtaData(array $sections): array
    {
        $section = self::section($sections, JazzPageConstants::SECTION_BOOKING_CTA);

        return [
            'headingText' => self::value($section, 'booking_cta_heading', 'Book Your Experience'),
            'descriptionText' => self::value($section, 'booking_cta_description', 'Secure your tickets now'),
        ];
    }

    /** @param array<string, mixed> $sections */
    private static function section(array $sections, string $sectionKey): array
    {
        $section = $sections[$sectionKey] ?? null;
        return is_array($section) ? $section : [];
    }

    /** @param array<string, mixed> $section */
    private static function value(array $section, string $key, string $default): string
    {
        $value = $section[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }

    /** @param array<string, mixed> $section */
    private static function intValue(array $section, string $key, int $default): int
    {
        $value = $section[$key] ?? null;
        if (is_numeric($value)) {
            return (int)$value;
        }

        return $default;
    }

    /** @param array<string, mixed> $section */
    private static function nullableValue(array $section, string $key, ?string $default): ?string
    {
        $value = $section[$key] ?? null;
        if (is_string($value) && $value !== '') {
            return $value;
        }

        return $default;
    }

    /** @param array<string, mixed> $data */
    private static function mapVenuesData(array $data): VenuesData
    {
        $venues = [];
        foreach (($data['venues'] ?? []) as $venue) {
            $halls = [];
            foreach (($venue['halls'] ?? []) as $hall) {
                $halls[] = new HallData(...$hall);
            }

            $venue['halls'] = $halls;
            $venues[] = new VenueData(...$venue);
        }

        $data['venues'] = $venues;
        return new VenuesData(...$data);
    }

    /** @param array<string, mixed> $data */
    private static function mapPricingData(array $data): PricingData
    {
        $cards = [];
        foreach (($data['pricingCards'] ?? []) as $card) {
            $cards[] = new PricingCardData(...$card);
        }

        $data['pricingCards'] = $cards;
        return new PricingData(...$data);
    }

    /** @param array<string, mixed> $data */
    private static function mapArtistsData(array $data): ArtistsData
    {
        $artists = [];
        foreach (($data['artists'] ?? []) as $artist) {
            $artists[] = new ArtistCardData(...$artist);
        }

        $data['artists'] = $artists;
        return new ArtistsData(...$data);
    }

    /** @param array<string, mixed> $data */
    private static function mapScheduleData(array $data): ScheduleData
    {
        $days = [];
        foreach (($data['days'] ?? []) as $day) {
            $events = [];
            foreach (($day['events'] ?? []) as $event) {
                $events[] = new ScheduleEventData(...$event);
            }

            $day['events'] = $events;
            $days[] = new ScheduleDayData(...$day);
        }

        $data['days'] = $days;
        return new ScheduleData(...$data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function fromMappedArtistData(array $data): JazzArtistDetailPageViewModel
    {
        $albums = [];
        foreach (($data['albums'] ?? []) as $album) {
            $albums[] = new JazzArtistAlbumData(...$album);
        }

        $tracks = [];
        foreach (($data['tracks'] ?? []) as $track) {
            $tracks[] = new JazzArtistTrackData(...$track);
        }

        $performances = [];
        foreach (($data['performances'] ?? []) as $performance) {
            $performances[] = ScheduleMapper::toEventCardViewModel($performance, '', '', '');
        }

        $data['albums'] = $albums;
        $data['tracks'] = $tracks;
        $data['performances'] = $performances;

        return new JazzArtistDetailPageViewModel(...$data);
    }

    private static function buildAlbums(array $cms): array
    {
        $albums = [];

        for ($index = 1; $index <= JazzArtistDetailConstants::MAX_ALBUMS; $index++) {
            $title = self::cmsValue($cms, JazzArtistDetailConstants::ALBUM_PREFIX . $index . '_title');
            if ($title === '') {
                continue;
            }

            $albums[] = [
                'title' => $title,
                'description' => self::cmsValue(
                    $cms,
                    JazzArtistDetailConstants::ALBUM_PREFIX . $index . '_description',
                ),
                'year' => self::cmsValue($cms, JazzArtistDetailConstants::ALBUM_PREFIX . $index . '_year'),
                'tag' => self::cmsValue($cms, JazzArtistDetailConstants::ALBUM_PREFIX . $index . '_tag'),
                'imageUrl' => self::cmsValue($cms, JazzArtistDetailConstants::ALBUM_PREFIX . $index . '_image'),
            ];
        }

        return $albums;
    }

    private static function buildTracks(array $cms): array
    {
        $tracks = [];

        for ($index = 1; $index <= JazzArtistDetailConstants::MAX_TRACKS; $index++) {
            $title = self::cmsValue($cms, JazzArtistDetailConstants::TRACK_PREFIX . $index . '_title');
            if ($title === '') {
                continue;
            }

            $tracks[] = [
                'title' => $title,
                'album' => self::cmsValue($cms, JazzArtistDetailConstants::TRACK_PREFIX . $index . '_album'),
                'description' => self::cmsValue(
                    $cms,
                    JazzArtistDetailConstants::TRACK_PREFIX . $index . '_description',
                ),
                'duration' => self::cmsValue($cms, JazzArtistDetailConstants::TRACK_PREFIX . $index . '_duration'),
                'imageUrl' => self::cmsValue($cms, JazzArtistDetailConstants::TRACK_PREFIX . $index . '_image'),
                'progressClass' => self::cmsValue(
                    $cms,
                    JazzArtistDetailConstants::TRACK_PREFIX . $index . '_progress_class',
                ),
            ];
        }

        return $tracks;
    }

    private static function collectTextList(array $cms, string $prefix, int $maxItems): array
    {
        $values = [];

        for ($index = 1; $index <= $maxItems; $index++) {
            $value = self::cmsValue($cms, $prefix . $index);
            if ($value !== '') {
                $values[] = $value;
            }
        }

        return $values;
    }

    private static function buildPrimaryOverviewFallbackFromModel(JazzArtistDetailEvent $event): string
    {
        if ($event->longDescriptionHtml === '') {
            return '';
        }

        return trim(strip_tags($event->longDescriptionHtml));
    }

    private static function cmsValue(array $content, string $key): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) ? $value : '';
    }

    private static function coalesce(string $value, string $fallback): string
    {
        return $value !== '' ? $value : $fallback;
    }

}
