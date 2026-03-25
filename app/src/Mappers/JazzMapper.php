<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\JazzPageConstants;
use App\Helpers\FormatHelper;
use App\Enums\PassScope;
use App\Models\ArtistAlbum;
use App\Models\ArtistGalleryImage;
use App\Models\ArtistHighlight;
use App\Models\ArtistLineupMember;
use App\Models\ArtistTrack;
use App\DTOs\Events\JazzArtistDetailEvent;
use App\DTOs\Pages\JazzArtistDetailPageData;
use App\Models\JazzArtistsSectionContent;
use App\Models\JazzBookingCtaSectionContent;
use App\Models\GradientSectionContent;
use App\Models\IntroSectionContent;
use App\DTOs\Pages\JazzPageData;
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
use App\ViewModels\Jazz\JazzArtistCtaData;
use App\ViewModels\Jazz\JazzArtistDetailPageViewModel;
use App\ViewModels\Jazz\JazzArtistHeroData;
use App\ViewModels\Jazz\JazzArtistLineupData;
use App\ViewModels\Jazz\JazzArtistMediaData;
use App\ViewModels\Jazz\JazzArtistOverviewData;
use App\ViewModels\Jazz\JazzArtistTrackData;
use App\ViewModels\Jazz\JazzPageViewModel;
use App\ViewModels\Jazz\PricingCardData;
use App\ViewModels\Jazz\PricingCardItemData;
use App\ViewModels\Jazz\PricingData;
use App\ViewModels\Jazz\ScheduleCallToActionData;
use App\ViewModels\Jazz\VenueData;
use App\ViewModels\Jazz\VenuesData;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Transforms JazzPageData and JazzArtistDetailPageData domain models into ViewModels
 * for the public Jazz landing page and individual artist detail pages, assembling
 * hero, gradient, venues, pricing cards, artist cards, and schedule sections.
 */
final class JazzMapper
{
    /**
     * Builds the full Jazz landing page ViewModel from CMS section models, pass prices,
     * and an optional pre-built schedule section.
     */
    public static function toPageViewModel(JazzPageData $domain, ?ScheduleSectionViewModel $scheduleSection = null, bool $isLoggedIn = false): JazzPageViewModel
    {
        $heroData = CmsMapper::toHeroData($domain->heroSection, JazzPageConstants::CURRENT_PAGE);
        $globalUi = CmsMapper::toGlobalUiData($domain->globalUiContent, $isLoggedIn);
        $cms      = CmsMapper::toCmsData($heroData, $globalUi);

        return self::buildPageViewModel($domain, $heroData, $globalUi, $cms, $scheduleSection);
    }

    /**
     * @param array<string, mixed> $cms
     */
    private static function buildPageViewModel(
        JazzPageData $domain,
        HeroData $heroData,
        GlobalUiData $globalUi,
        array $cms,
        ?ScheduleSectionViewModel $scheduleSection,
    ): JazzPageViewModel {
        return new JazzPageViewModel(
            heroData: $heroData, globalUi: $globalUi, cms: $cms,
            gradientSection: self::buildGradientSection($domain->gradientSection),
            introSplitSection: self::buildIntroSection($domain->introSection),
            venuesData: self::buildVenuesData($domain->venuesSection),
            pricingData: self::buildPricingData($domain->pricingSection, $domain->passPrices),
            scheduleCtaData: self::buildScheduleCtaData($domain->scheduleCtaSection),
            artistsData: self::buildArtistsData($domain->artistsSection),
            bookingCtaData: self::buildBookingCtaData($domain->bookingCtaSection), scheduleSection: $scheduleSection,
        );
    }

    /**
     * Builds the Jazz artist detail page ViewModel from CMS content, event data,
     * lineup members, albums, tracks, and pre-mapped performance cards.
     *
     * @param ScheduleEventCardViewModel[] $performances
     */
    public static function toArtistDetailViewModel(JazzArtistDetailPageData $pageData, array $performances): JazzArtistDetailPageViewModel
    {
        return new JazzArtistDetailPageViewModel(
            hero: self::buildArtistHeroData($pageData),
            overview: self::buildArtistOverviewData($pageData),
            lineup: self::buildArtistLineupData($pageData),
            media: self::buildArtistMediaData($pageData),
            cta: self::buildArtistCtaData($pageData),
            performances: $performances,
        );
    }

    private static function buildArtistHeroData(JazzArtistDetailPageData $pageData): JazzArtistHeroData
    {
        $event = $pageData->event;
        $cms   = $pageData->cms;

        return new JazzArtistHeroData(
            heroTitle: $event->title, heroSubtitle: self::firstNonEmpty($cms->heroSubtitle ?? '', $event->shortDescription),
            heroBackgroundImageUrl: $cms->heroBackgroundImage ?? '',
            originText: $cms->originText ?? '', formedText: $cms->formedText ?? '', performancesText: $cms->performancesText ?? '',
            heroBackButtonText: $cms->heroBackButtonText ?? '', heroBackButtonUrl: $cms->heroBackButtonUrl ?? '',
            heroReserveButtonText: $cms->heroReserveButtonText ?? '',
        );
    }

    private static function buildArtistOverviewData(JazzArtistDetailPageData $pageData): JazzArtistOverviewData
    {
        $event   = $pageData->event;
        $cms     = $pageData->cms;
        $primary = self::firstNonEmpty($cms->overviewBodyPrimary ?? '', self::buildPrimaryOverviewFallbackFromModel($event));

        return new JazzArtistOverviewData(
            overviewHeading: self::firstNonEmpty($cms->overviewHeading ?? '', $event->title),
            overviewLead: self::firstNonEmpty($cms->overviewLead ?? '', $event->shortDescription),
            overviewBodyPrimary: $primary,
            overviewBodySecondary: $cms->overviewBodySecondary ?? '',
        );
    }

    private static function buildArtistLineupData(JazzArtistDetailPageData $pageData): JazzArtistLineupData
    {
        $cms = $pageData->cms;

        return new JazzArtistLineupData(
            lineupHeading: $cms->lineupHeading ?? '',
            lineup: array_map(fn(ArtistLineupMember $m) => $m->memberText, $pageData->lineupMembers),
            highlightsHeading: $cms->highlightsHeading ?? '',
            highlights: array_map(fn(ArtistHighlight $h) => $h->highlightText, $pageData->highlights),
            photoGalleryHeading: $cms->photoGalleryHeading ?? '',
            photoGalleryDescription: $cms->photoGalleryDescription ?? '',
            galleryImages: array_map(fn(ArtistGalleryImage $g) => $g->imagePath, $pageData->galleryImages),
        );
    }

    private static function buildArtistMediaData(JazzArtistDetailPageData $pageData): JazzArtistMediaData
    {
        $cms = $pageData->cms;

        return new JazzArtistMediaData(
            albumsHeading: $cms->albumsHeading ?? '', albumsDescription: $cms->albumsDescription ?? '',
            albums: self::buildAlbumsFromTable($pageData->albums),
            listenHeading: $cms->listenHeading ?? '', listenSubheading: $cms->listenSubheading ?? '',
            listenDescription: $cms->listenDescription ?? '',
            listenPlayButtonLabel: $cms->listenPlayButtonLabel ?? '', listenPlayExcerptText: $cms->listenPlayExcerptText ?? '',
            listenTrackArtworkAltSuffix: $cms->listenTrackArtworkAltSuffix ?? '',
            tracks: self::buildTracksFromTable($pageData->tracks),
        );
    }

    private static function buildArtistCtaData(JazzArtistDetailPageData $pageData): JazzArtistCtaData
    {
        $cms = $pageData->cms;

        return new JazzArtistCtaData(
            liveCtaHeading: $cms->liveCtaHeading ?? '', liveCtaDescription: $cms->liveCtaDescription ?? '',
            liveCtaBookButtonText: $cms->liveCtaBookButtonText ?? '',
            liveCtaScheduleButtonText: $cms->liveCtaScheduleButtonText ?? '', liveCtaScheduleButtonUrl: $cms->liveCtaScheduleButtonUrl ?? '',
            performancesSectionId: $cms->performancesSectionId ?? '',
            performancesHeading: $cms->performancesHeading ?? '',
            performancesDescription: $cms->performancesDescription ?? '',
        );
    }

    private static function buildGradientSection(GradientSectionContent $section): GradientSectionData
    {
        return new GradientSectionData(
            headingText: $section->gradientHeading ?? '',
            subheadingText: $section->gradientSubheading ?? '',
            backgroundImageUrl: $section->gradientBackgroundImage ?? JazzPageConstants::DEFAULT_GRADIENT_BACKGROUND_IMAGE,
        );
    }

    private static function buildIntroSection(IntroSectionContent $section): IntroSplitSectionData
    {
        return new IntroSplitSectionData(
            headingText: $section->introHeading ?? '',
            bodyText: $section->introBody ?? '',
            imageUrl: $section->introImage ?? JazzPageConstants::DEFAULT_INTRO_IMAGE,
            imageAltText: $section->introImageAlt ?? JazzPageConstants::DEFAULT_INTRO_IMAGE_ALT,
            subsections: null,
            closingLine: null,
        );
    }

    private static function buildVenuesData(JazzVenuesSectionContent $section): VenuesData
    {
        return new VenuesData(
            headingText: $section->venuesHeading ?? '',
            subheadingText: $section->venuesSubheading ?? '',
            descriptionText: $section->venuesDescription ?? '',
            venues: [
                self::buildPatronaatVenue($section),
                self::buildGrotemarktVenue($section),
            ],
        );
    }

    private static function buildPatronaatVenue(JazzVenuesSectionContent $section): VenueData
    {
        return new VenueData(
            name: $section->venuePatronaatName ?? '',
            addressLine1: $section->venuePatronaatAddress1 ?? '', addressLine2: $section->venuePatronaatAddress2 ?? '',
            contactInfo: $section->venuePatronaatContact ?? '',
            halls: self::buildPatronaatHalls($section),
            isDark: false,
        );
    }

    /**
     * @return HallData[]
     */
    /** @return HallData[] */
    private static function buildPatronaatHalls(JazzVenuesSectionContent $section): array
    {
        return [
            self::buildPatronaatHall($section, 1),
            self::buildPatronaatHall($section, 2),
            self::buildPatronaatHall($section, 3),
        ];
    }

    /** Builds a single Patronaat hall by index (1, 2, or 3). */
    private static function buildPatronaatHall(JazzVenuesSectionContent $section, int $index): HallData
    {
        $name = 'venuePatronaatHall' . $index . 'Name';
        $desc = 'venuePatronaatHall' . $index . 'Desc';
        $price = 'venuePatronaatHall' . $index . 'Price';
        $capacity = 'venuePatronaatHall' . $index . 'Capacity';

        return new HallData(
            name: $section->$name ?? '',
            description: $section->$desc ?? '',
            price: $section->$price ?? '',
            capacity: $section->$capacity ?? '',
            isFree: false,
        );
    }

    private static function buildGrotemarktVenue(JazzVenuesSectionContent $section): VenueData
    {
        return new VenueData(
            name: $section->venueGrotemarktName ?? '',
            addressLine1: $section->venueGrotemarktLocation1 ?? '',
            addressLine2: $section->venueGrotemarktLocation2 ?? '',
            contactInfo: $section->venueGrotemarktContact ?? '',
            halls: [self::buildGrotemarktHall($section)],
            isDark: false,
        );
    }

    private static function buildGrotemarktHall(JazzVenuesSectionContent $section): HallData
    {
        return new HallData(
            name: $section->venueGrotemarktHallName ?? '',
            description: $section->venueGrotemarktHallDesc ?? '',
            price: $section->venueGrotemarktHallPrice ?? '',
            capacity: $section->venueGrotemarktHallCapacity ?? '',
            isFree: true,
        );
    }

    /**
     * Builds the pricing section with three cards (individual, day pass, all-access).
     * Pass prices from the DB override CMS fallback values when available.
     *
     * @param PassType[] $passPrices
     */
    private static function buildPricingData(JazzPricingSectionContent $section, array $passPrices): PricingData
    {
        return new PricingData(
            headingText: $section->pricingHeading ?? '',
            subheadingText: $section->pricingSubheading ?? '',
            descriptionText: $section->pricingDescription ?? '',
            pricingCards: self::buildPricingCards($section, $passPrices),
        );
    }

    /**
     * @param PassType[] $passPrices
     * @return PricingCardData[]
     */
    private static function buildPricingCards(JazzPricingSectionContent $section, array $passPrices): array
    {
        $dayPassPrice = self::findPassPrice($passPrices, PassScope::Day->value, $section->pricingDaypassPrice);
        $allAccessPrice = self::findPassPrice($passPrices, PassScope::Range->value, $section->pricing3dayPrice);

        return [
            self::buildIndividualTicketCard($section),
            self::buildDayPassCard($section, $dayPassPrice),
            self::buildAllAccessCard($section, $allAccessPrice),
        ];
    }

    private static function buildIndividualTicketCard(JazzPricingSectionContent $section): PricingCardData
    {
        return new PricingCardData(
            title: $section->pricingIndividualTitle ?? '',
            price: '',
            priceDescription: '',
            items: self::buildIndividualTicketItems($section),
            includes: [],
            additionalInfo: '',
            isHighlighted: false,
        );
    }

    /**
     * Parses individual ticket item strings into structured data.
     *
     * @return PricingCardItemData[]
     */
    private static function buildIndividualTicketItems(JazzPricingSectionContent $section): array
    {
        $rawItems = [
            $section->pricingIndividualItem1 ?? '',
            $section->pricingIndividualItem2 ?? '',
            $section->pricingIndividualItem3 ?? '',
        ];

        return array_map([self::class, 'parseTicketItem'], $rawItems);
    }

    /** Parses a ticket item string like "Main Hall Shows - €15.00 - 300 seats" into structured data. */
    private static function parseTicketItem(string $rawItem): PricingCardItemData
    {
        $parts = explode(' - ', $rawItem);

        return new PricingCardItemData(
            name:     $parts[0] ?? '',
            price:    $parts[1] ?? '',
            capacity: $parts[2] ?? '',
        );
    }

    private static function buildDayPassCard(JazzPricingSectionContent $section, string $price): PricingCardData
    {
        return new PricingCardData(
            title: $section->pricingDaypassTitle ?? '',
            price: $price,
            priceDescription: $section->pricingDaypassDesc ?? '',
            items: [],
            includes: self::buildDayPassIncludes($section),
            additionalInfo: $section->pricingDaypassInfo ?? '',
            isHighlighted: false,
        );
    }

    /**
     * @return string[]
     */
    private static function buildDayPassIncludes(JazzPricingSectionContent $section): array
    {
        return [
            $section->pricingDaypassInclude1 ?? '',
            $section->pricingDaypassInclude2 ?? '',
            $section->pricingDaypassInclude3 ?? '',
            $section->pricingDaypassInclude4 ?? '',
        ];
    }

    private static function buildAllAccessCard(JazzPricingSectionContent $section, string $price): PricingCardData
    {
        return new PricingCardData(
            title: $section->pricing3dayTitle ?? '',
            price: $price,
            priceDescription: $section->pricing3dayDesc ?? '',
            items: [],
            includes: self::buildAllAccessIncludes($section),
            additionalInfo: $section->pricing3dayInfo ?? '',
            isHighlighted: true,
        );
    }

    /**
     * @return string[]
     */
    private static function buildAllAccessIncludes(JazzPricingSectionContent $section): array
    {
        return [
            $section->pricing3dayInclude1 ?? '',
            $section->pricing3dayInclude2 ?? '',
            $section->pricing3dayInclude3 ?? '',
            $section->pricing3dayInclude4 ?? '',
        ];
    }

    /**
     * Searches the PassType array for a matching scope (Day or Range) and formats
     * its price; falls back to the CMS-authored string if no DB record exists.
     *
     * @param PassType[] $passPrices
     */
    private static function findPassPrice(array $passPrices, string $scope, ?string $cmsFallback): string
    {
        foreach ($passPrices as $pass) {
            if ($pass->passScope->value === $scope) {
                return FormatHelper::price((float) $pass->price);
            }
        }

        return $cmsFallback ?? '';
    }

    private static function buildScheduleCtaData(JazzScheduleCtaSectionContent $section): ScheduleCallToActionData
    {
        return new ScheduleCallToActionData(
            headingText: $section->scheduleCtaHeading ?? '',
            descriptionText: $section->scheduleCtaDescription ?? '',
            buttonText: $section->scheduleCtaButton ?? '',
            buttonLink: $section->scheduleCtaButtonLink ?? '#schedule',
        );
    }

    private static function buildArtistsData(JazzArtistsSectionContent $section): ArtistsData
    {
        return new ArtistsData(
            headingText: $section->artistsHeading ?? '',
            artists: self::buildArtistCards($section),
            currentPage: JazzPageConstants::ARTISTS_CURRENT_PAGE,
            totalPages: JazzPageConstants::ARTISTS_TOTAL_PAGES,
            totalArtists: JazzPageConstants::ARTISTS_TOTAL_COUNT,
        );
    }

    /** @return ArtistCardData[] */
    private static function buildArtistCards(JazzArtistsSectionContent $section): array
    {
        $artists = [
            ['prefix' => 'GumboKings', 'slug' => 'gumbo-kings', 'defaultImage' => JazzPageConstants::DEFAULT_GUMBO_KINGS_IMAGE],
            ['prefix' => 'Evolve',     'slug' => 'evolve',      'defaultImage' => JazzPageConstants::DEFAULT_EVOLVE_IMAGE],
            ['prefix' => 'Ntjam',      'slug' => 'ntjam-rosie', 'defaultImage' => JazzPageConstants::DEFAULT_NTJAM_IMAGE],
        ];

        return array_map(
            fn(array $a) => self::buildArtistCard($section, $a['prefix'], $a['slug'], $a['defaultImage']),
            $artists,
        );
    }

    /** Builds a single artist card from CMS content using the property prefix pattern. */
    private static function buildArtistCard(JazzArtistsSectionContent $section, string $prefix, string $slug, string $defaultImage): ArtistCardData
    {
        $name = 'artists' . $prefix . 'Name';
        $genre = 'artists' . $prefix . 'Genre';
        $desc = 'artists' . $prefix . 'Description';
        $image = 'artists' . $prefix . 'Image';
        $count = 'artists' . $prefix . 'PerformanceCount';
        $first = 'artists' . $prefix . 'FirstPerformance';
        $more = 'artists' . $prefix . 'MorePerformancesText';
        $url = 'artists' . $prefix . 'ProfileUrl';

        return new ArtistCardData(
            name: $section->$name ?? '',
            genre: $section->$genre ?? '',
            description: $section->$desc ?? '',
            imageUrl: $section->$image ?? $defaultImage,
            performanceCount: (int)($section->$count ?? 0),
            firstPerformance: $section->$first ?? '',
            morePerformancesText: $section->$more ?? '',
            profileUrl: self::resolveProfileUrl($section->$url, $slug),
        );
    }

    /**
     * Returns the CMS-provided profile URL if set, otherwise generates
     * the default detail page URL from the event slug (e.g., /jazz/gumbo-kings).
     */
    private static function resolveProfileUrl(?string $cmsUrl, string $eventSlug): string
    {
        if ($cmsUrl !== null && $cmsUrl !== '') {
            return $cmsUrl;
        }
        return '/jazz/' . $eventSlug;
    }

    private static function buildBookingCtaData(JazzBookingCtaSectionContent $section): BookingCallToActionData
    {
        return new BookingCallToActionData(
            headingText: $section->bookingCtaHeading ?? '',
            descriptionText: $section->bookingCtaDescription ?? '',
        );
    }

    /**
     * @param ArtistAlbum[] $albums
     * @return JazzArtistAlbumData[]
     */
    private static function buildAlbumsFromTable(array $albums): array
    {
        return array_map(fn(ArtistAlbum $a) => new JazzArtistAlbumData(
            title: $a->title,
            description: $a->description,
            year: $a->year,
            tag: $a->tag,
            imageUrl: $a->imagePath,
        ), $albums);
    }

    /**
     * @param ArtistTrack[] $tracks
     * @return JazzArtistTrackData[]
     */
    private static function buildTracksFromTable(array $tracks): array
    {
        return array_map(fn(ArtistTrack $t) => new JazzArtistTrackData(
            title: $t->title,
            album: $t->album,
            description: $t->description,
            duration: $t->duration,
            imageUrl: $t->imagePath,
            progressClass: $t->progressClass,
        ), $tracks);
    }

    private static function buildPrimaryOverviewFallbackFromModel(JazzArtistDetailEvent $event): string
    {
        if ($event->longDescriptionHtml === '') {
            return '';
        }

        return trim(strip_tags($event->longDescriptionHtml));
    }

    /** Returns the first non-empty string, used to prefer CMS content over model fallbacks. */
    private static function firstNonEmpty(string $value, string $fallback): string
    {
        return $value ?: $fallback;
    }
}
