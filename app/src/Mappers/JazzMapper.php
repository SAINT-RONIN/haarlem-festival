<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\JazzPageConstants;
use App\Models\ArtistAlbum;
use App\Models\ArtistGalleryImage;
use App\Models\ArtistHighlight;
use App\Models\ArtistLineupMember;
use App\Models\ArtistTrack;
use App\Models\JazzArtistDetailEvent;
use App\Models\JazzArtistDetailPageData;
use App\Models\JazzArtistsSectionContent;
use App\Models\JazzBookingCtaSectionContent;
use App\Models\JazzGradientSectionContent;
use App\Models\JazzIntroSectionContent;
use App\Models\JazzPageData;
use App\Models\JazzPricingSectionContent;
use App\Models\JazzScheduleCtaSectionContent;
use App\Models\JazzVenuesSectionContent;
use App\Models\PassType;
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
use App\ViewModels\Jazz\ScheduleData;
use App\ViewModels\Jazz\VenueData;
use App\ViewModels\Jazz\VenuesData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

class JazzMapper
{
    public static function toPageViewModel(JazzPageData $domain, GlobalUiData $globalUi, ?ScheduleSectionViewModel $scheduleSection = null): JazzPageViewModel
    {
        $heroData = CmsMapper::toHeroData($domain->heroSection, JazzPageConstants::CURRENT_PAGE);

        return new JazzPageViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            cms: CmsMapper::toCmsData($heroData, $globalUi),
            gradientSection: self::buildGradientSection($domain->gradientSection),
            introSplitSection: self::buildIntroSection($domain->introSection),
            venuesData: self::buildVenuesData($domain->venuesSection),
            pricingData: self::buildPricingData($domain->pricingSection, $domain->passPrices),
            scheduleCtaData: self::buildScheduleCtaData($domain->scheduleCtaSection),
            artistsData: self::buildArtistsData($domain->artistsSection),
            scheduleData: self::buildEmptyScheduleData(),
            bookingCtaData: self::buildBookingCtaData($domain->bookingCtaSection),
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
            'heroSubtitle' => self::coalesce($cms->heroSubtitle ?? '', $event->shortDescription),
            'heroBackgroundImageUrl' => $cms->heroBackgroundImage ?? '',
            'originText' => $cms->originText ?? '',
            'formedText' => $cms->formedText ?? '',
            'performancesText' => $cms->performancesText ?? '',
            'heroBackButtonText' => $cms->heroBackButtonText ?? '',
            'heroBackButtonUrl' => $cms->heroBackButtonUrl ?? '',
            'heroReserveButtonText' => $cms->heroReserveButtonText ?? '',
            'overviewHeading' => self::coalesce($cms->overviewHeading ?? '', $event->title),
            'overviewLead' => self::coalesce($cms->overviewLead ?? '', $event->shortDescription),
            'overviewBodyPrimary' => self::coalesce(
                $cms->overviewBodyPrimary ?? '',
                self::buildPrimaryOverviewFallbackFromModel($event),
            ),
            'overviewBodySecondary' => $cms->overviewBodySecondary ?? '',
            'lineupHeading' => $cms->lineupHeading ?? '',
            'lineup' => array_map(fn(ArtistLineupMember $m) => $m->memberText, $pageData->lineupMembers),
            'highlightsHeading' => $cms->highlightsHeading ?? '',
            'highlights' => array_map(fn(ArtistHighlight $h) => $h->highlightText, $pageData->highlights),
            'photoGalleryHeading' => $cms->photoGalleryHeading ?? '',
            'photoGalleryDescription' => $cms->photoGalleryDescription ?? '',
            'galleryImages' => array_map(fn(ArtistGalleryImage $g) => $g->imagePath, $pageData->galleryImages),
            'albumsHeading' => $cms->albumsHeading ?? '',
            'albumsDescription' => $cms->albumsDescription ?? '',
            'albums' => self::buildAlbumsFromTable($pageData->albums),
            'listenHeading' => $cms->listenHeading ?? '',
            'listenSubheading' => $cms->listenSubheading ?? '',
            'listenDescription' => $cms->listenDescription ?? '',
            'listenPlayButtonLabel' => $cms->listenPlayButtonLabel ?? '',
            'listenPlayExcerptText' => $cms->listenPlayExcerptText ?? '',
            'listenTrackArtworkAltSuffix' => $cms->listenTrackArtworkAltSuffix ?? '',
            'tracks' => self::buildTracksFromTable($pageData->tracks),
            'liveCtaHeading' => $cms->liveCtaHeading ?? '',
            'liveCtaDescription' => $cms->liveCtaDescription ?? '',
            'liveCtaBookButtonText' => $cms->liveCtaBookButtonText ?? '',
            'liveCtaScheduleButtonText' => $cms->liveCtaScheduleButtonText ?? '',
            'liveCtaScheduleButtonUrl' => $cms->liveCtaScheduleButtonUrl ?? '',
            'performancesSectionId' => $cms->performancesSectionId ?? '',
            'performancesHeading' => $cms->performancesHeading ?? '',
            'performancesDescription' => $cms->performancesDescription ?? '',
            'performances' => $performances,
        ];

        return self::fromMappedArtistData($mappedData);
    }

    private static function buildGradientSection(JazzGradientSectionContent $section): GradientSectionData
    {
        return new GradientSectionData(
            headingText: $section->gradientHeading ?? 'Every note carries emotion',
            subheadingText: $section->gradientSubheading ?? 'A place where jazz is experienced',
            backgroundImageUrl: $section->gradientBackgroundImage ?? JazzPageConstants::DEFAULT_GRADIENT_BACKGROUND_IMAGE,
        );
    }

    private static function buildIntroSection(JazzIntroSectionContent $section): IntroSplitSectionData
    {
        return new IntroSplitSectionData(
            headingText: $section->introHeading ?? 'Haarlem moves to the rhythm of jazz',
            bodyText: $section->introBody ?? 'Welcome to Haarlem Jazz 2026',
            imageUrl: $section->introImage ?? JazzPageConstants::DEFAULT_INTRO_IMAGE,
            imageAltText: $section->introImageAlt ?? JazzPageConstants::DEFAULT_INTRO_IMAGE_ALT,
            subsections: null,
            closingLine: null,
        );
    }

    private static function buildVenuesData(JazzVenuesSectionContent $section): VenuesData
    {
        return new VenuesData(
            headingText: $section->venuesHeading ?? 'Festival venues',
            subheadingText: $section->venuesSubheading ?? 'Performance Locations',
            descriptionText: $section->venuesDescription ?? 'Haarlem Jazz 2026 takes place at two main locations',
            venues: [
                self::buildPatronaatVenue($section),
                self::buildGrotemarktVenue($section),
            ],
        );
    }

    private static function buildPatronaatVenue(JazzVenuesSectionContent $section): VenueData
    {
        return new VenueData(
            name: $section->venuePatronaatName ?? 'Patronaat',
            addressLine1: $section->venuePatronaatAddress1 ?? 'Zijlsingel 2',
            addressLine2: $section->venuePatronaatAddress2 ?? '2013 DN Haarlem',
            contactInfo: $section->venuePatronaatContact ?? 'E-mail/reception available',
            halls: [
                self::buildPatronaatHall1($section),
                self::buildPatronaatHall2($section),
                self::buildPatronaatHall3($section),
            ],
            isDark: false,
        );
    }

    private static function buildPatronaatHall1(JazzVenuesSectionContent $section): HallData
    {
        return new HallData(
            name: $section->venuePatronaatHall1Name ?? 'First Hall',
            description: $section->venuePatronaatHall1Desc ?? 'Intimate performances',
            price: $section->venuePatronaatHall1Price ?? '',
            capacity: $section->venuePatronaatHall1Capacity ?? '150 seats',
            isFree: false,
        );
    }

    private static function buildPatronaatHall2(JazzVenuesSectionContent $section): HallData
    {
        return new HallData(
            name: $section->venuePatronaatHall2Name ?? 'Second Hall',
            description: $section->venuePatronaatHall2Desc ?? 'Intimate performances',
            price: $section->venuePatronaatHall2Price ?? '',
            capacity: $section->venuePatronaatHall2Capacity ?? '150 seats',
            isFree: false,
        );
    }

    private static function buildPatronaatHall3(JazzVenuesSectionContent $section): HallData
    {
        return new HallData(
            name: $section->venuePatronaatHall3Name ?? 'Third Hall',
            description: $section->venuePatronaatHall3Desc ?? 'Intimate performances',
            price: $section->venuePatronaatHall3Price ?? '',
            capacity: $section->venuePatronaatHall3Capacity ?? '150 seats',
            isFree: false,
        );
    }

    private static function buildGrotemarktVenue(JazzVenuesSectionContent $section): VenueData
    {
        return new VenueData(
            name: $section->venueGrotemarktName ?? 'Grote Markt',
            addressLine1: $section->venueGrotemarktLocation1 ?? 'Historic Market Square',
            addressLine2: $section->venueGrotemarktLocation2 ?? 'Haarlem City Center',
            contactInfo: '',
            halls: [new HallData(
                name: $section->venueGrotemarktHallName ?? 'Open Air Stage',
                description: $section->venueGrotemarktHallDesc ?? 'Sunday performances are free',
                price: $section->venueGrotemarktHallPrice ?? 'FREE ENTRY',
                capacity: '',
                isFree: true,
            )],
            isDark: false,
        );
    }

    /**
     * @param PassType[] $passPrices
     */
    private static function buildPricingData(JazzPricingSectionContent $section, array $passPrices): PricingData
    {
        return new PricingData(
            headingText: $section->pricingHeading ?? 'Pricing information',
            subheadingText: $section->pricingSubheading ?? 'Tickets & Passes',
            descriptionText: $section->pricingDescription ?? 'We offer flexible ticketing options',
            pricingCards: self::buildPricingCards($section, $passPrices),
        );
    }

    /**
     * @param PassType[] $passPrices
     * @return PricingCardData[]
     */
    private static function buildPricingCards(JazzPricingSectionContent $section, array $passPrices): array
    {
        $dayPassPrice = self::findPassPrice($passPrices, 'Day', $section->pricingDaypassPrice);
        $allAccessPrice = self::findPassPrice($passPrices, 'Range', $section->pricing3dayPrice);

        return [
            self::buildIndividualTicketCard($section),
            self::buildDayPassCard($section, $dayPassPrice),
            self::buildAllAccessCard($section, $allAccessPrice),
        ];
    }

    private static function buildIndividualTicketCard(JazzPricingSectionContent $section): PricingCardData
    {
        return new PricingCardData(
            title: $section->pricingIndividualTitle ?? 'Individual Show Tickets',
            price: '',
            priceDescription: '',
            items: [
                $section->pricingIndividualItem1 ?? 'Main Hall Shows',
                $section->pricingIndividualItem2 ?? 'Second Hall Shows',
                $section->pricingIndividualItem3 ?? 'Third Hall Shows',
            ],
            includes: [],
            additionalInfo: '',
            isHighlighted: false,
        );
    }

    private static function buildDayPassCard(JazzPricingSectionContent $section, string $price): PricingCardData
    {
        return new PricingCardData(
            title: $section->pricingDaypassTitle ?? 'All-Access Day Pass',
            price: $price,
            priceDescription: $section->pricingDaypassDesc ?? 'Per day',
            items: [],
            includes: [
                $section->pricingDaypassInclude1 ?? 'Unlimited access',
                $section->pricingDaypassInclude2 ?? 'All performances',
                $section->pricingDaypassInclude3 ?? 'Thu, Fri, or Sat',
                $section->pricingDaypassInclude4 ?? 'Best value',
            ],
            additionalInfo: $section->pricingDaypassInfo ?? '',
            isHighlighted: false,
        );
    }

    private static function buildAllAccessCard(JazzPricingSectionContent $section, string $price): PricingCardData
    {
        return new PricingCardData(
            title: $section->pricing3dayTitle ?? 'All-Access Day Pass',
            price: $price,
            priceDescription: $section->pricing3dayDesc ?? 'Thursday + Friday + Saturday',
            items: [],
            includes: [
                $section->pricing3dayInclude1 ?? 'Unlimited access all 3 days',
                $section->pricing3dayInclude2 ?? 'All venues',
                $section->pricing3dayInclude3 ?? '18+ performances',
                $section->pricing3dayInclude4 ?? 'Save €25',
            ],
            additionalInfo: $section->pricing3dayInfo ?? '',
            isHighlighted: true,
        );
    }

    /**
     * @param PassType[] $passPrices
     */
    private static function findPassPrice(array $passPrices, string $scope, ?string $cmsFallback): string
    {
        foreach ($passPrices as $pass) {
            if ($pass->passScope->value === $scope) {
                return '€' . number_format((float) $pass->price, 2);
            }
        }

        return $cmsFallback ?? '';
    }

    private static function buildScheduleCtaData(JazzScheduleCtaSectionContent $section): ScheduleCallToActionData
    {
        return new ScheduleCallToActionData(
            headingText: $section->scheduleCtaHeading ?? 'Ready to Plan Your Festival Experience?',
            descriptionText: $section->scheduleCtaDescription ?? 'Check out the complete schedule',
            buttonText: $section->scheduleCtaButton ?? 'View complete schedule',
            buttonLink: $section->scheduleCtaButtonLink ?? '#schedule',
        );
    }

    private static function buildArtistsData(JazzArtistsSectionContent $section): ArtistsData
    {
        return new ArtistsData(
            headingText: $section->artistsHeading ?? 'Discover our lineup',
            artists: self::buildArtistCards($section),
            currentPage: 1,
            totalPages: 4,
            totalArtists: 12,
        );
    }

    /**
     * @return ArtistCardData[]
     */
    private static function buildArtistCards(JazzArtistsSectionContent $section): array
    {
        return [
            self::buildGumboKingsCard($section),
            self::buildEvolveCard($section),
            self::buildNtjamCard($section),
        ];
    }

    private static function buildGumboKingsCard(JazzArtistsSectionContent $section): ArtistCardData
    {
        return new ArtistCardData(
            name: $section->artistsGumboKingsName ?? 'Gumbo Kings',
            genre: $section->artistsGumboKingsGenre ?? 'New Orleans Jazz',
            description: $section->artistsGumboKingsDescription ?? 'High-energy New Orleans style jazz band bringing authentic Big Easy sound to Haarlem. Known for infectious rhythms.',
            imageUrl: $section->artistsGumboKingsImage ?? JazzPageConstants::DEFAULT_GUMBO_KINGS_IMAGE,
            performanceCount: (int) ($section->artistsGumboKingsPerformanceCount ?? 2),
            firstPerformance: $section->artistsGumboKingsFirstPerformance ?? 'Thu 18:00 - Patronaat Main Hall',
            morePerformancesText: $section->artistsGumboKingsMorePerformancesText ?? '+1 more',
            profileUrl: $section->artistsGumboKingsProfileUrl ?? '/jazz/gumbo-kings',
        );
    }

    private static function buildEvolveCard(JazzArtistsSectionContent $section): ArtistCardData
    {
        return new ArtistCardData(
            name: $section->artistsEvolveName ?? 'Evolve',
            genre: $section->artistsEvolveGenre ?? 'Contemporary Jazz',
            description: $section->artistsEvolveDescription ?? 'Progressive jazz ensemble pushing boundaries with innovative compositions. A fresh take on modern jazz traditions.',
            imageUrl: $section->artistsEvolveImage ?? JazzPageConstants::DEFAULT_EVOLVE_IMAGE,
            performanceCount: (int) ($section->artistsEvolvePerformanceCount ?? 2),
            firstPerformance: $section->artistsEvolveFirstPerformance ?? 'Thu 18:00 - Patronaat Main Hall',
            morePerformancesText: $section->artistsEvolveMorePerformancesText ?? '+1 more',
            profileUrl: ($section->artistsEvolveProfileUrl !== '' && $section->artistsEvolveProfileUrl !== null) ? $section->artistsEvolveProfileUrl : null,
        );
    }

    private static function buildNtjamCard(JazzArtistsSectionContent $section): ArtistCardData
    {
        return new ArtistCardData(
            name: $section->artistsNtjamName ?? 'Ntjam Rosie',
            genre: $section->artistsNtjamGenre ?? 'Vocal Jazz',
            description: $section->artistsNtjamDescription ?? 'Sultry vocals meet classic jazz standards. Rosie brings timeless elegance and powerful vocal performances to every show.',
            imageUrl: $section->artistsNtjamImage ?? JazzPageConstants::DEFAULT_NTJAM_IMAGE,
            performanceCount: (int) ($section->artistsNtjamPerformanceCount ?? 2),
            firstPerformance: $section->artistsNtjamFirstPerformance ?? 'Thu 21:00 - Patronaat Main Hall',
            morePerformancesText: $section->artistsNtjamMorePerformancesText ?? '+1 more',
            profileUrl: $section->artistsNtjamProfileUrl ?? '/jazz/ntjam-rosie',
        );
    }

    private static function buildBookingCtaData(JazzBookingCtaSectionContent $section): BookingCallToActionData
    {
        return new BookingCallToActionData(
            headingText: $section->bookingCtaHeading ?? 'Book Your Experience',
            descriptionText: $section->bookingCtaDescription ?? 'Secure your tickets now',
        );
    }

    private static function buildEmptyScheduleData(): ScheduleData
    {
        return new ScheduleData(
            headingText: 'Performance schedule',
            year: (string) date('Y'),
            filterLabel: 'Filters',
            totalEventsText: '0 Events',
            days: [],
        );
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

    /** @param ArtistAlbum[] $albums */
    private static function buildAlbumsFromTable(array $albums): array
    {
        return array_map(fn(ArtistAlbum $a) => [
            'title' => $a->title,
            'description' => $a->description,
            'year' => $a->year,
            'tag' => $a->tag,
            'imageUrl' => $a->imagePath,
        ], $albums);
    }

    /** @param ArtistTrack[] $tracks */
    private static function buildTracksFromTable(array $tracks): array
    {
        return array_map(fn(ArtistTrack $t) => [
            'title' => $t->title,
            'album' => $t->album,
            'description' => $t->description,
            'duration' => $t->duration,
            'imageUrl' => $t->imagePath,
            'progressClass' => $t->progressClass,
        ], $tracks);
    }

    private static function buildPrimaryOverviewFallbackFromModel(JazzArtistDetailEvent $event): string
    {
        if ($event->longDescriptionHtml === '') {
            return '';
        }

        return trim(strip_tags($event->longDescriptionHtml));
    }

    private static function coalesce(string $value, string $fallback): string
    {
        return $value !== '' ? $value : $fallback;
    }
}
