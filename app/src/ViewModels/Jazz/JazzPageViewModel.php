<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

use App\Constants\JazzPageConstants;
use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * ViewModel for the Jazz page.
 */
final readonly class JazzPageViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        public GradientSectionData $gradientSection,
        public IntroSplitSectionData $introSplitSection,
        public VenuesData $venuesData,
        public PricingData $pricingData,
        public ScheduleCallToActionData $scheduleCtaData,
        public ArtistsData $artistsData,
        public ScheduleData $scheduleData,
        public BookingCallToActionData $bookingCtaData,
        public ?ScheduleSectionViewModel $scheduleSection = null,
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: $heroData->currentPage,
            includeNav: false,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromData(array $data, GlobalUiData $globalUi): self
    {
        $hero = $data['heroData'] ?? [];
        $gradient = $data['gradientSection'] ?? [];
        $intro = $data['introSplitSection'] ?? [];
        $venues = $data['venuesData'] ?? [];
        $pricing = $data['pricingData'] ?? [];
        $scheduleCta = $data['scheduleCtaData'] ?? [];
        $artists = $data['artistsData'] ?? [];
        $schedule = $data['scheduleData'] ?? [];
        $booking = $data['bookingCtaData'] ?? [];
        $scheduleSectionData = $data['scheduleSectionData'] ?? null;

        return new self(
            heroData: new HeroData(...$hero),
            globalUi: $globalUi,
            gradientSection: new GradientSectionData(...$gradient),
            introSplitSection: new IntroSplitSectionData(...$intro),
            venuesData: self::mapVenuesData($venues),
            pricingData: self::mapPricingData($pricing),
            scheduleCtaData: new ScheduleCallToActionData(...$scheduleCta),
            artistsData: self::mapArtistsData($artists),
            scheduleData: self::mapScheduleData($schedule),
            bookingCtaData: new BookingCallToActionData(...$booking),
            scheduleSection: is_array($scheduleSectionData)
                ? ScheduleSectionViewModel::fromData($scheduleSectionData)
                : null,
        );
    }

    /**
     * @param array<string, mixed> $domain
     */
    public static function fromDomainData(array $domain, GlobalUiData $globalUi): self
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
            'scheduleSectionData' => $scheduleSectionData,
        ];

        return self::fromData($mapped, $globalUi);
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
}
