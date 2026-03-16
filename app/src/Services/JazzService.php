<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EventTypeId;
use App\Models\CmsItem;
use App\Models\CmsSection;
use App\Repositories\CmsRepository;
use App\Repositories\MediaAssetRepository;
use App\Services\Interfaces\IJazzService;

/**
 * Service for Jazz page data payload.
 * Returns plain arrays; mapping to ViewModels is done outside the service.
 */
class JazzService implements IJazzService
{
    private CmsRepository $cmsRepository;
    private MediaAssetRepository $mediaAssetRepository;
    private ScheduleService $scheduleService;
    private SessionService $sessionService;
    private ?int $jazzPageId = null;
    /** @var array<string, CmsSection> */
    private ?array $jazzSections = null;
    /** @var array<string, list<CmsItem>>|null */
    private ?array $jazzItemsBySection = null;

    public function __construct()
    {
        $this->cmsRepository = new CmsRepository();
        $this->mediaAssetRepository = new MediaAssetRepository();
        $this->scheduleService = new ScheduleService();
        $this->sessionService = new SessionService();
    }

    public function getJazzPageData(): array
    {
        $this->loadPageData();

        return [
            'heroData' => $this->buildHeroData(),
            'globalUi' => $this->buildGlobalUi(),
            'gradientSection' => $this->buildGradientSection(),
            'introSplitSection' => $this->buildIntroSplitSection(),
            'venuesData' => $this->buildVenuesData(),
            'pricingData' => $this->buildPricingData(),
            'scheduleCtaData' => $this->buildScheduleCtaData(),
            'artistsData' => $this->buildArtistsData(),
            'scheduleData' => $this->buildScheduleData(),
            'bookingCtaData' => $this->buildBookingCtaData(),
            'scheduleSectionData' => $this->scheduleService->getScheduleData('jazz', EventTypeId::Jazz->value, 7),
        ];
    }

    private function loadPageData(): void
    {
        if ($this->jazzPageId !== null) {
            return;
        }

        $pages = $this->cmsRepository->findPages(['slug' => 'jazz']);
        if ($pages === []) {
            return;
        }

        $this->jazzPageId = (int)$pages[0]['CmsPageId'];
        $sections = $this->cmsRepository->findSections(['cmsPageId' => $this->jazzPageId]);
        $this->jazzSections = [];
        foreach ($sections as $section) {
            /** @var CmsSection $section */
            $this->jazzSections[$section->sectionKey] = $section;
        }

        $items = $this->cmsRepository->findItems(['cmsPageId' => $this->jazzPageId]);
        $itemsBySectionId = [];
        foreach ($items as $item) {
            $itemsBySectionId[$item->cmsSectionId][] = $item;
        }

        $this->jazzItemsBySection = [];
        foreach ($this->jazzSections as $sectionKey => $section) {
            $this->jazzItemsBySection[$sectionKey] = $itemsBySectionId[$section->cmsSectionId] ?? [];
        }
    }

    private function getCmsItem(string $sectionKey, string $itemKey, string $default = ''): string
    {
        if (!isset($this->jazzSections[$sectionKey])) {
            return $default;
        }

        $items = $this->jazzItemsBySection[$sectionKey] ?? [];

        foreach ($items as $item) {
            /** @var CmsItem $item */
            if ($item->itemKey === $itemKey) {
                $value = $item->textValue ?? $item->htmlValue ?? $default;
                return is_string($value) ? $value : $default;
            }
        }

        return $default;
    }

    /**
     * Gets a CMS-managed image URL for the Jazz page.
     *
     * Supports both:
     * - MEDIA items (MediaAssetId)
     * - legacy IMAGE_PATH items (TextValue)
     */
    private function getCmsImage(string $sectionKey, string $itemKey, string $defaultUrl): string
    {
        if (!isset($this->jazzSections[$sectionKey])) {
            return $defaultUrl;
        }

        $items = $this->jazzItemsBySection[$sectionKey] ?? [];

        foreach ($items as $item) {
            /** @var CmsItem $item */
            if ($item->itemKey !== $itemKey) {
                continue;
            }

            if ($item->mediaAssetId !== null) {
                $media = $this->mediaAssetRepository->findById($item->mediaAssetId);
                if ($media !== null && $media->filePath !== '') {
                    return $media->filePath;
                }
            }

            $textPath = $item->textValue ?? null;
            if (is_string($textPath) && $textPath !== '') {
                return $textPath;
            }

            return $defaultUrl;
        }

        return $defaultUrl;
    }

    private function buildHeroData(): array
    {
        return [
            'mainTitle' => $this->getCmsItem('hero_section', 'hero_main_title', 'HAARLEM JAZZ'),
            'subtitle' => $this->getCmsItem('hero_section', 'hero_subtitle', 'Experience world-class jazz performances'),
            'primaryButtonText' => $this->getCmsItem('hero_section', 'hero_button_primary', 'Discover all performances'),
            'primaryButtonLink' => $this->getCmsItem('hero_section', 'hero_button_primary_link', '#artists'),
            'secondaryButtonText' => $this->getCmsItem('hero_section', 'hero_button_secondary', 'What is Haarlem Jazz?'),
            'secondaryButtonLink' => $this->getCmsItem('hero_section', 'hero_button_secondary_link', '#intro'),
            'backgroundImageUrl' => '/assets/Image/Jazz/Jazz-hero.png',
            'currentPage' => 'jazz',
        ];
    }

    private function buildGlobalUi(): array
    {
        return [
            'siteName' => 'Haarlem Festival',
            'navHome' => 'Home',
            'navJazz' => 'Jazz',
            'navDance' => 'Dance',
            'navHistory' => 'History',
            'navRestaurant' => 'Restaurant',
            'navStorytelling' => 'Storytelling',
            'btnMyProgram' => 'My Program',
            'loginLabel' => 'Login',
            'logoutLabel' => 'Logout',
            'labelEventsCount' => 'events',
            'labelNoEvents' => 'No events scheduled',
            'btnExploreTemplate' => 'Explore {title} Events',
            'isLoggedIn' => $this->sessionService->isLoggedIn(),
        ];
    }

    private function buildGradientSection(): array
    {
        return [
            'headingText' => $this->getCmsItem('gradient_section', 'gradient_heading', 'Every note carries emotion'),
            'subheadingText' => $this->getCmsItem('gradient_section', 'gradient_subheading', 'A place where jazz is experienced'),
            'backgroundImageUrl' => '/assets/Image/Jazz/Jazz-second-section.png',
        ];
    }

    private function buildIntroSplitSection(): array
    {
        return [
            'headingText' => $this->getCmsItem('intro_section', 'intro_heading', 'Haarlem moves to the rhythm of jazz'),
            'bodyText' => $this->getCmsItem('intro_section', 'intro_body', 'Welcome to Haarlem Jazz 2026'),
            'imageUrl' => '/assets/Image/Jazz/Jazz-third-section.png',
            'imageAltText' => 'Jazz musicians performing at Haarlem Festival',
            'subsections' => null,
            'closingLine' => null,
        ];
    }

    private function buildVenuesData(): array
    {
        return [
            'headingText' => $this->getCmsItem('venues_section', 'venues_heading', 'Festival venues'),
            'subheadingText' => $this->getCmsItem('venues_section', 'venues_subheading', 'Performance Locations'),
            'descriptionText' => $this->getCmsItem('venues_section', 'venues_description', 'Haarlem Jazz 2026 takes place at two main locations'),
            'venues' => [$this->buildPatronaatVenue(), $this->buildGroteMarktVenue()],
        ];
    }

    private function buildPatronaatVenue(): array
    {
        return [
            'name' => $this->getCmsItem('venues_section', 'venue_patronaat_name', 'Patronaat'),
            'addressLine1' => $this->getCmsItem('venues_section', 'venue_patronaat_address1', 'Zijlsingel 2'),
            'addressLine2' => $this->getCmsItem('venues_section', 'venue_patronaat_address2', '2013 DN Haarlem'),
            'contactInfo' => $this->getCmsItem('venues_section', 'venue_patronaat_contact', 'E-mail/reception available'),
            'halls' => [
                $this->buildHall('venue_patronaat_hall1', 'First Hall'),
                $this->buildHall('venue_patronaat_hall2', 'Second Hall'),
                $this->buildHall('venue_patronaat_hall3', 'Third Hall'),
            ],
            'isDark' => false,
        ];
    }

    private function buildHall(string $prefix, string $defaultName): array
    {
        return [
            'name' => $this->getCmsItem('venues_section', $prefix . '_name', $defaultName),
            'description' => $this->getCmsItem('venues_section', $prefix . '_desc', 'Intimate performances'),
            'price' => '€10.00',
            'capacity' => $this->getCmsItem('venues_section', $prefix . '_capacity', '150 seats'),
            'isFree' => false,
        ];
    }

    private function buildGroteMarktVenue(): array
    {
        return [
            'name' => $this->getCmsItem('venues_section', 'venue_grotemarkt_name', 'Grote Markt'),
            'addressLine1' => $this->getCmsItem('venues_section', 'venue_grotemarkt_location1', 'Historic Market Square'),
            'addressLine2' => $this->getCmsItem('venues_section', 'venue_grotemarkt_location2', 'Haarlem City Center'),
            'contactInfo' => '',
            'halls' => [[
                'name' => $this->getCmsItem('venues_section', 'venue_grotemarkt_hall_name', 'Open Air Stage'),
                'description' => $this->getCmsItem('venues_section', 'venue_grotemarkt_hall_desc', 'Sunday performances are free'),
                'price' => $this->getCmsItem('venues_section', 'venue_grotemarkt_hall_price', 'FREE ENTRY'),
                'capacity' => '',
                'isFree' => true,
            ]],
            'isDark' => false,
        ];
    }

    private function buildPricingData(): array
    {
        return [
            'headingText' => $this->getCmsItem('pricing_section', 'pricing_heading', 'Pricing information'),
            'subheadingText' => $this->getCmsItem('pricing_section', 'pricing_subheading', 'Tickets & Passes'),
            'descriptionText' => $this->getCmsItem('pricing_section', 'pricing_description', 'We offer flexible ticketing options'),
            'pricingCards' => [
                [
                    'title' => $this->getCmsItem('pricing_section', 'pricing_individual_title', 'Individual Show Tickets'),
                    'price' => '',
                    'priceDescription' => '',
                    'items' => [
                        $this->getCmsItem('pricing_section', 'pricing_individual_item1', 'Main Hall Shows'),
                        $this->getCmsItem('pricing_section', 'pricing_individual_item2', 'Second Hall Shows'),
                        $this->getCmsItem('pricing_section', 'pricing_individual_item3', 'Third Hall Shows'),
                    ],
                    'includes' => [],
                    'additionalInfo' => '',
                    'isHighlighted' => false,
                ],
                [
                    'title' => $this->getCmsItem('pricing_section', 'pricing_daypass_title', 'All-Access Day Pass'),
                    'price' => $this->getCmsItem('pricing_section', 'pricing_daypass_price', '€35.00'),
                    'priceDescription' => $this->getCmsItem('pricing_section', 'pricing_daypass_desc', 'Per day'),
                    'items' => [],
                    'includes' => [
                        $this->getCmsItem('pricing_section', 'pricing_daypass_include1', 'Unlimited access'),
                        $this->getCmsItem('pricing_section', 'pricing_daypass_include2', 'All performances'),
                        $this->getCmsItem('pricing_section', 'pricing_daypass_include3', 'Thu, Fri, or Sat'),
                        $this->getCmsItem('pricing_section', 'pricing_daypass_include4', 'Best value'),
                    ],
                    'additionalInfo' => $this->getCmsItem('pricing_section', 'pricing_daypass_info', ''),
                    'isHighlighted' => false,
                ],
                [
                    'title' => $this->getCmsItem('pricing_section', 'pricing_3day_title', 'All-Access Day Pass'),
                    'price' => $this->getCmsItem('pricing_section', 'pricing_3day_price', '€80.00'),
                    'priceDescription' => $this->getCmsItem('pricing_section', 'pricing_3day_desc', 'Thursday + Friday + Saturday'),
                    'items' => [],
                    'includes' => [
                        $this->getCmsItem('pricing_section', 'pricing_3day_include1', 'Unlimited access all 3 days'),
                        $this->getCmsItem('pricing_section', 'pricing_3day_include2', 'All venues'),
                        $this->getCmsItem('pricing_section', 'pricing_3day_include3', '18+ performances'),
                        $this->getCmsItem('pricing_section', 'pricing_3day_include4', 'Save €25'),
                    ],
                    'additionalInfo' => $this->getCmsItem('pricing_section', 'pricing_3day_info', ''),
                    'isHighlighted' => true,
                ],
            ],
        ];
    }

    private function buildScheduleCtaData(): array
    {
        return [
            'headingText' => $this->getCmsItem('schedule_cta_section', 'schedule_cta_heading', 'Ready to Plan Your Festival Experience?'),
            'descriptionText' => $this->getCmsItem('schedule_cta_section', 'schedule_cta_description', 'Check out the complete schedule'),
            'buttonText' => $this->getCmsItem('schedule_cta_section', 'schedule_cta_button', 'View complete schedule'),
            'buttonLink' => $this->getCmsItem('schedule_cta_section', 'schedule_cta_button_link', '#schedule'),
        ];
    }

    private function buildArtistsData(): array
    {
        return [
            'headingText' => $this->getCmsItem('artists_section', 'artists_heading', 'Discover our lineup'),
            'artists' => [
                [
                    'name' => $this->getCmsItem('artists_section', 'artists_gumbokings_name', 'Gumbo Kings'),
                    'genre' => $this->getCmsItem('artists_section', 'artists_gumbokings_genre', 'New Orleans Jazz'),
                    'description' => $this->getCmsItem('artists_section', 'artists_gumbokings_description', 'High-energy New Orleans style jazz band bringing authentic Big Easy sound to Haarlem. Known for infectious rhythms.'),
                    'imageUrl' => $this->getCmsImage('artists_section', 'artists_gumbokings_image', '/assets/Image/Jazz/Jazz-Gumbokings.png'),
                    'performanceCount' => (int)$this->getCmsItem('artists_section', 'artists_gumbokings_performance_count', '2'),
                    'firstPerformance' => $this->getCmsItem('artists_section', 'artists_gumbokings_first_performance', 'Thu 18:00 - Patronaat Main Hall'),
                    'morePerformancesText' => $this->getCmsItem('artists_section', 'artists_gumbokings_more_performances_text', '+1 more'),
                    'profileUrl' => '/jazz/gumbo-kings',
                ],
                [
                    'name' => $this->getCmsItem('artists_section', 'artists_evolve_name', 'Evolve'),
                    'genre' => $this->getCmsItem('artists_section', 'artists_evolve_genre', 'Contemporary Jazz'),
                    'description' => $this->getCmsItem('artists_section', 'artists_evolve_description', 'Progressive jazz ensemble pushing boundaries with innovative compositions. A fresh take on modern jazz traditions.'),
                    'imageUrl' => $this->getCmsImage('artists_section', 'artists_evolve_image', '/assets/Image/Jazz/Jazz-evolve.png'),
                    'performanceCount' => (int)$this->getCmsItem('artists_section', 'artists_evolve_performance_count', '2'),
                    'firstPerformance' => $this->getCmsItem('artists_section', 'artists_evolve_first_performance', 'Thu 18:00 - Patronaat Main Hall'),
                    'morePerformancesText' => $this->getCmsItem('artists_section', 'artists_evolve_more_performances_text', '+1 more'),
                    'profileUrl' => null,
                ],
                [
                    'name' => $this->getCmsItem('artists_section', 'artists_ntjam_name', 'Ntjam Rosie'),
                    'genre' => $this->getCmsItem('artists_section', 'artists_ntjam_genre', 'Vocal Jazz'),
                    'description' => $this->getCmsItem('artists_section', 'artists_ntjam_description', 'Sultry vocals meet classic jazz standards. Rosie brings timeless elegance and powerful vocal performances to every show.'),
                    'imageUrl' => $this->getCmsImage('artists_section', 'artists_ntjam_image', '/assets/Image/Jazz/Jazz-Ntjam.png'),
                    'performanceCount' => (int)$this->getCmsItem('artists_section', 'artists_ntjam_performance_count', '2'),
                    'firstPerformance' => $this->getCmsItem('artists_section', 'artists_ntjam_first_performance', 'Thu 21:00 - Patronaat Main Hall'),
                    'morePerformancesText' => $this->getCmsItem('artists_section', 'artists_ntjam_more_performances_text', ''),
                    'profileUrl' => '/jazz/ntjam-rosie',
                ],
            ],
            'currentPage' => 1,
            'totalPages' => 4,
            'totalArtists' => 12,
        ];
    }

    private function buildScheduleData(): array
    {
        return [
            'headingText' => 'Performance schedule',
            'year' => '2026',
            'filterLabel' => 'Filters',
            'totalEventsText' => '24 Events',
            'days' => [
                [
                    'dayName' => 'Thursday',
                    'fullDate' => 'Thursday, July 25',
                    'events' => [
                        ['artistName' => 'Gumbo Kings', 'genre' => 'Jazz', 'venue' => 'Patronaat • Main Hall • 300 seats', 'date' => 'Thursday, July 25', 'time' => '18:00 - 19:00', 'price' => '€15.00', 'isFree' => false],
                        ['artistName' => 'Evolve', 'genre' => 'Electronic', 'venue' => 'Patronaat • Main Hall • 300 seats', 'date' => 'Thursday, July 25', 'time' => '19:30 - 20:30', 'price' => '€15.00', 'isFree' => false],
                        ['artistName' => 'Ntjam Rosie', 'genre' => 'Soul', 'venue' => 'Patronaat • Main Hall • 300 seats', 'date' => 'Thursday, July 25', 'time' => '21:00 - 22:00', 'price' => '€15.00', 'isFree' => false],
                        ['artistName' => 'Wicked Jazz Sounds', 'genre' => 'Jazz', 'venue' => 'Patronaat • Main Hall • 200 seats', 'date' => 'Thursday, July 25', 'time' => '18:00 - 19:00', 'price' => '€10.00', 'isFree' => false],
                        ['artistName' => 'Wouter Hamel', 'genre' => 'Jazz', 'venue' => 'Patronaat • Main Hall • 200 seats', 'date' => 'Thursday, July 25', 'time' => '19:30 - 20:30', 'price' => '€10.00', 'isFree' => false],
                        ['artistName' => 'Joram Frazer', 'genre' => 'Soul', 'venue' => 'Patronaat • Main Hall • 200 seats', 'date' => 'Thursday, July 25', 'time' => '21:00 - 22:00', 'price' => '€10.00', 'isFree' => false],
                    ],
                ],
                [
                    'dayName' => 'Friday',
                    'fullDate' => 'Friday, July 26',
                    'events' => [
                        ['artistName' => 'Karsu', 'genre' => 'Jazz', 'venue' => 'Patronaat • Main Hall • 300 seats', 'date' => 'Friday, July 26', 'time' => '18:00 - 19:00', 'price' => '€15.00', 'isFree' => false],
                        ['artistName' => 'New Cool Collective', 'genre' => 'Jazz', 'venue' => 'Patronaat • Main Hall • 300 seats', 'date' => 'Friday, July 26', 'time' => '19:30 - 20:30', 'price' => '€15.00', 'isFree' => false],
                        ['artistName' => 'Chris Allen', 'genre' => 'Rock', 'venue' => 'Patronaat • Main Hall • 300 seats', 'date' => 'Friday, July 26', 'time' => '21:00 - 22:00', 'price' => '€15.00', 'isFree' => false],
                        ['artistName' => 'Eric Sanko', 'genre' => 'Alternative', 'venue' => 'Patronaat • Main Hall • 200 seats', 'date' => 'Friday, July 26', 'time' => '18:00 - 19:00', 'price' => '€10.00', 'isFree' => false],
                        ['artistName' => 'Ilse Huizinga', 'genre' => 'Jazz', 'venue' => 'Patronaat • Main Hall • 200 seats', 'date' => 'Friday, July 26', 'time' => '19:30 - 20:30', 'price' => '€10.00', 'isFree' => false],
                        ['artistName' => 'Eric Vloeimans and Hotspot', 'genre' => 'Jazz', 'venue' => 'Patronaat • Main Hall • 200 seats', 'date' => 'Friday, July 26', 'time' => '21:00 - 22:00', 'price' => '€10.00', 'isFree' => false],
                    ],
                ],
                [
                    'dayName' => 'Saturday',
                    'fullDate' => 'Saturday, July 27',
                    'events' => [
                        ['artistName' => 'Gare du Nord', 'genre' => 'Jazz', 'venue' => 'Patronaat • Main Hall • 300 seats', 'date' => 'Saturday, July 27', 'time' => '18:00 - 19:00', 'price' => '€15.00', 'isFree' => false],
                        ['artistName' => 'Rilan & The Bombardiers', 'genre' => 'Soul', 'venue' => 'Patronaat • Main Hall • 300 seats', 'date' => 'Saturday, July 27', 'time' => '19:30 - 20:30', 'price' => '€15.00', 'isFree' => false],
                        ['artistName' => 'Soul Six', 'genre' => 'Soul', 'venue' => 'Patronaat • Main Hall • 300 seats', 'date' => 'Saturday, July 27', 'time' => '21:00 - 22:00', 'price' => '€15.00', 'isFree' => false],
                        ['artistName' => 'Han Bennink', 'genre' => 'Jazz', 'venue' => 'Patronaat • Main Hall • 150 seats', 'date' => 'Saturday, July 27', 'time' => '18:00 - 19:00', 'price' => '€10.00', 'isFree' => false],
                        ['artistName' => 'The Nordanians', 'genre' => 'Folk', 'venue' => 'Patronaat • Main Hall • 150 seats', 'date' => 'Saturday, July 27', 'time' => '19:30 - 20:30', 'price' => '€10.00', 'isFree' => false],
                        ['artistName' => 'Lilith Merlot', 'genre' => 'Alternative', 'venue' => 'Patronaat • Main Hall • 150 seats', 'date' => 'Saturday, July 27', 'time' => '21:00 - 22:00', 'price' => '€10.00', 'isFree' => false],
                    ],
                ],
                [
                    'dayName' => 'Sunday',
                    'fullDate' => 'Sunday, July 28',
                    'events' => [
                        ['artistName' => 'Husk Soundsystem', 'genre' => 'In Dutch', 'venue' => 'Grote Markt • Outdoor Stage • Open Air', 'date' => 'Sunday, July 28', 'time' => '15:00 - 16:00', 'price' => 'Free', 'isFree' => true],
                        ['artistName' => 'Dolvee', 'genre' => 'Pop', 'venue' => 'Grote Markt • Outdoor Stage • Open Air', 'date' => 'Sunday, July 28', 'time' => '17:00 - 18:00', 'price' => 'Free', 'isFree' => true],
                        ['artistName' => 'Wicked Jazz Sounds', 'genre' => 'Jazz', 'venue' => 'Grote Markt • Outdoor Stage • Open Air', 'date' => 'Sunday, July 28', 'time' => '16:00 - 17:00', 'price' => 'Free', 'isFree' => true],
                        ['artistName' => 'The Nordanians', 'genre' => 'Folk', 'venue' => 'Grote Markt • Outdoor Stage • Open Air', 'date' => 'Sunday, July 28', 'time' => '18:00 - 19:00', 'price' => 'Free', 'isFree' => true],
                        ['artistName' => 'Gumbo Kings', 'genre' => 'Jazz', 'venue' => 'Grote Markt • Outdoor Stage • Open Air', 'date' => 'Sunday, July 28', 'time' => '19:00 - 20:00', 'price' => 'Free', 'isFree' => true],
                        ['artistName' => 'Gare du Nord', 'genre' => 'Jazz', 'venue' => 'Grote Markt • Outdoor Stage • Open Air', 'date' => 'Sunday, July 28', 'time' => '20:00 - 21:00', 'price' => 'Free', 'isFree' => true],
                    ],
                ],
            ],
        ];
    }

    private function buildBookingCtaData(): array
    {
        return [
            'headingText' => $this->getCmsItem('booking_cta_section', 'booking_cta_heading', 'Book Your Experience'),
            'descriptionText' => $this->getCmsItem('booking_cta_section', 'booking_cta_description', 'Secure your tickets now'),
        ];
    }
}
