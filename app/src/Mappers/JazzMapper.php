<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\JazzPageConstants;
use App\DTOs\Domain\Events\JazzArtistCardRecord;
use App\Helpers\FormatHelper;
use App\Helpers\TextHelper;
use App\Enums\PassScope;
use App\Models\ArtistAlbum;
use App\Models\ArtistGalleryImage;
use App\Models\ArtistHighlight;
use App\Models\ArtistLineupMember;
use App\Models\ArtistTrack;
use App\DTOs\Domain\Pages\JazzArtistDetailPageData;
use App\DTOs\Cms\JazzArtistsSectionContent;
use App\DTOs\Cms\JazzBookingCtaSectionContent;
use App\DTOs\Domain\Pages\JazzPageData;
use App\DTOs\Cms\JazzPricingSectionContent;
use App\DTOs\Cms\JazzScheduleCtaSectionContent;
use App\DTOs\Cms\JazzVenuesSectionContent;
use App\Models\PassType;
use App\ViewModels\Jazz\ArtistCardData;
use App\ViewModels\Jazz\ArtistsData;
use App\ViewModels\Jazz\BookingCardData;
use App\ViewModels\Jazz\BookingCardRowData;
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
        $venuesData = self::buildVenuesData($domain->venuesSection);
        $pricingData = self::buildPricingData($domain->pricingSection, $domain->passPrices);

        return new JazzPageViewModel(
            heroData: $heroData, globalUi: $globalUi,
            gradientSection: CmsMapper::toGradientSection($domain->gradientSection, JazzPageConstants::DEFAULT_GRADIENT_BACKGROUND_IMAGE),
            introSplitSection: CmsMapper::toIntroSplitSection($domain->introSection, JazzPageConstants::DEFAULT_INTRO_IMAGE, JazzPageConstants::DEFAULT_INTRO_IMAGE_ALT),
            venuesData: $venuesData,
            pricingData: $pricingData,
            scheduleCtaData: self::buildScheduleCtaData($domain->scheduleCtaSection),
            artistsData: self::buildArtistsData($domain->artistsSection, $domain->featuredArtists),
            bookingCtaData: self::buildBookingCtaData($domain->bookingCtaSection, $venuesData, $pricingData),
            scheduleSection: $scheduleSection,
        );
    }

    /**
     * Builds the Jazz artist detail page ViewModel from CMS content, event data,
     * lineup members, albums, tracks, and pre-mapped performance cards.
     *
     * @param ScheduleEventCardViewModel[] $performances
     */
    public static function toArtistDetailViewModel(
        JazzArtistDetailPageData $pageData,
        array $performances,
        string $currentUri,
        string $appUrl,
    ): JazzArtistDetailPageViewModel {
        return new JazzArtistDetailPageViewModel(
            hero: self::buildArtistHeroData($pageData, $performances),
            overview: self::buildArtistOverviewData($pageData),
            lineup: self::buildArtistLineupData($pageData),
            media: self::buildArtistMediaData($pageData),
            cta: self::buildArtistCtaData($pageData, $performances),
            performances: $performances,
        );
    }

    /**
     * @param ScheduleEventCardViewModel[] $performances
     */
    private static function buildArtistHeroData(JazzArtistDetailPageData $pageData, array $performances): JazzArtistHeroData
    {
        $event = $pageData->event;
        $artist = $pageData->artist;

        return new JazzArtistHeroData(
            heroTitle: TextHelper::firstNonEmpty($artist->name, $event->title),
            heroSubtitle: TextHelper::firstNonEmpty($artist->heroSubtitle, $event->shortDescription),
            heroBackgroundImageUrl: TextHelper::firstNonEmpty(
                $artist->heroImagePath,
                $event->featuredImageUrl,
                $artist->imagePath ?? '',
                JazzPageConstants::DEFAULT_HERO_BACKGROUND_IMAGE,
            ),
            originText: $artist->originText,
            formedText: $artist->formedText,
            performancesText: self::buildPerformancesText(count($performances)),
            heroBackButtonText: 'Back to Jazz',
            heroBackButtonUrl: '/jazz',
            heroReserveButtonText: 'Reserve your spot',
        );
    }

    private static function buildArtistOverviewData(JazzArtistDetailPageData $pageData): JazzArtistOverviewData
    {
        $event   = $pageData->event;
        $artist  = $pageData->artist;
        $primary = TextHelper::firstNonEmpty(
            TextHelper::stripHtmlToText($artist->bioHtml),
            self::buildPrimaryOverviewFallbackFromModel($event),
        );

        return new JazzArtistOverviewData(
            overviewHeading: TextHelper::firstNonEmpty($artist->name, $event->title),
            overviewLead: TextHelper::firstNonEmpty($artist->overviewLead, $event->shortDescription),
            overviewBodyPrimary: $primary,
            overviewBodySecondary: $artist->overviewBodySecondary,
        );
    }

    private static function buildArtistLineupData(JazzArtistDetailPageData $pageData): JazzArtistLineupData
    {
        $artist = $pageData->artist;

        return new JazzArtistLineupData(
            lineupHeading: TextHelper::firstNonEmpty($artist->lineupHeading, 'Band Lineup'),
            lineup: array_map(fn(ArtistLineupMember $m) => $m->memberText, $pageData->lineupMembers),
            highlightsHeading: TextHelper::firstNonEmpty($artist->highlightsHeading, 'Career Highlights'),
            highlights: array_map(fn(ArtistHighlight $h) => $h->highlightText, $pageData->highlights),
            photoGalleryHeading: TextHelper::firstNonEmpty($artist->photoGalleryHeading, 'Photo Gallery'),
            photoGalleryDescription: $artist->photoGalleryDescription,
            galleryImages: array_map(fn(ArtistGalleryImage $g) => $g->imagePath, $pageData->galleryImages),
        );
    }

    private static function buildArtistMediaData(JazzArtistDetailPageData $pageData): JazzArtistMediaData
    {
        $artist = $pageData->artist;

        return new JazzArtistMediaData(
            albumsHeading: TextHelper::firstNonEmpty($artist->albumsHeading, 'Featured Albums'),
            albumsDescription: $artist->albumsDescription,
            albums: self::buildAlbumsFromTable($pageData->albums),
            listenHeading: TextHelper::firstNonEmpty($artist->listenHeading, 'LISTEN NOW'),
            listenSubheading: TextHelper::firstNonEmpty($artist->listenSubheading, 'Important Tracks'),
            listenDescription: $artist->listenDescription,
            listenPlayButtonLabel: 'Play excerpt',
            listenPlayExcerptText: 'Click to Play Excerpt',
            listenTrackArtworkAltSuffix: 'track artwork',
            tracks: self::buildTracksFromTable($pageData->tracks),
        );
    }

    /**
     * @param ScheduleEventCardViewModel[] $performances
     */
    private static function buildArtistCtaData(JazzArtistDetailPageData $pageData, array $performances): JazzArtistCtaData
    {
        $artist = $pageData->artist;
        $artistName = TextHelper::firstNonEmpty($artist->name, $pageData->event->title);
        $performanceCount = count($performances);

        return new JazzArtistCtaData(
            liveCtaHeading: TextHelper::firstNonEmpty($artist->liveCtaHeading, 'Experience ' . $artistName . ' Live'),
            liveCtaDescription: TextHelper::firstNonEmpty(
                $artist->liveCtaDescription,
                self::buildLiveCtaDescription($artistName, $performanceCount),
            ),
            liveCtaBookButtonText: 'Book Tickets',
            liveCtaScheduleButtonText: 'View Full Schedule',
            liveCtaScheduleButtonUrl: '/jazz#jazz-schedule',
            performancesSectionId: 'artist-performances',
            performancesHeading: TextHelper::firstNonEmpty($artist->performancesHeading, $artistName . ' at Haarlem Jazz 2026'),
            performancesDescription: TextHelper::firstNonEmpty(
                $artist->performancesDescription,
                self::buildPerformancesDescription($artistName),
            ),
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
        $dayPassType = self::findPassType($passPrices, PassScope::Day->value);
        $allAccessType = self::findPassType($passPrices, PassScope::Range->value);

        return [
            self::buildIndividualTicketCard($section),
            self::buildDayPassCard($section, $dayPassType),
            self::buildAllAccessCard($section, $allAccessType),
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

    private static function buildDayPassCard(JazzPricingSectionContent $section, ?PassType $passType): PricingCardData
    {
        $price = $passType !== null
            ? FormatHelper::price((float) $passType->price)
            : ($section->pricingDaypassPrice ?? '');

        return new PricingCardData(
            title: $section->pricingDaypassTitle ?? '',
            price: $price,
            priceDescription: $section->pricingDaypassDesc ?? '',
            items: [],
            includes: self::buildDayPassIncludes($section),
            additionalInfo: $section->pricingDaypassInfo ?? '',
            isHighlighted: false,
            passTypeId: $passType?->passTypeId,
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

    private static function buildAllAccessCard(JazzPricingSectionContent $section, ?PassType $passType): PricingCardData
    {
        $price = $passType !== null
            ? FormatHelper::price((float) $passType->price)
            : ($section->pricing3dayPrice ?? '');

        return new PricingCardData(
            title: $section->pricing3dayTitle ?? '',
            price: $price,
            priceDescription: $section->pricing3dayDesc ?? '',
            items: [],
            includes: self::buildAllAccessIncludes($section),
            additionalInfo: $section->pricing3dayInfo ?? '',
            isHighlighted: true,
            passTypeId: $passType?->passTypeId,
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
     * Searches the PassType array for the first matching scope (Day or Range).
     * Returns the full PassType so callers can extract both price and ID.
     *
     * @param PassType[] $passPrices
     */
    private static function findPassType(array $passPrices, string $scope): ?PassType
    {
        foreach ($passPrices as $pass) {
            if ($pass->passScope->value === $scope) {
                return $pass;
            }
        }

        return null;
    }

    private static function buildScheduleCtaData(JazzScheduleCtaSectionContent $section): ScheduleCallToActionData
    {
        return new ScheduleCallToActionData(
            headingText: $section->scheduleCtaHeading ?? '',
            descriptionText: $section->scheduleCtaDescription ?? '',
            buttonText: $section->scheduleCtaButton ?? '',
            buttonLink: self::normalizeJazzScheduleLink($section->scheduleCtaButtonLink),
        );
    }

    /**
     * @param JazzArtistCardRecord[] $featuredArtists
     */
    private static function buildArtistsData(JazzArtistsSectionContent $section, array $featuredArtists): ArtistsData
    {
        $artistCount = count($featuredArtists);

        return new ArtistsData(
            headingText: $section->artistsHeading ?? '',
            artists: self::buildArtistCards($featuredArtists),
            currentPage: 1,
            totalPages: max(1, (int)ceil(max($artistCount, 1) / 3)),
            totalArtists: $artistCount,
        );
    }

    /**
     * @param JazzArtistCardRecord[] $featuredArtists
     * @return ArtistCardData[]
     */
    private static function buildArtistCards(array $featuredArtists): array
    {
        return array_map(
            static function (JazzArtistCardRecord $artist): ArtistCardData {
                $morePerformances = max(0, $artist->performanceCount - 1);

                return new ArtistCardData(
                    name: $artist->artistName,
                    genre: $artist->artistStyle,
                    description: $artist->cardDescription,
                    imageUrl: TextHelper::firstNonEmpty($artist->imageUrl, JazzPageConstants::DEFAULT_HERO_BACKGROUND_IMAGE),
                    performanceCount: $artist->performanceCount,
                    firstPerformance: self::formatFirstPerformance($artist),
                    morePerformancesText: $morePerformances > 0 ? '+' . $morePerformances . ' more' : '',
                    profileUrl: '/jazz/' . $artist->eventSlug,
                );
            },
            $featuredArtists,
        );
    }

    private static function buildBookingCtaData(
        JazzBookingCtaSectionContent $section,
        VenuesData $venuesData,
        PricingData $pricingData,
    ): BookingCallToActionData
    {
        return new BookingCallToActionData(
            headingText: $section->bookingCtaHeading ?? '',
            descriptionText: $section->bookingCtaDescription ?? '',
            cards: [
                self::buildBookingContactCard($section),
                self::buildBookingVenueCard($section, $venuesData),
                self::buildBookingTicketsCard($section, $pricingData),
            ],
        );
    }

    private static function buildBookingContactCard(JazzBookingCtaSectionContent $section): BookingCardData
    {
        return new BookingCardData(
            eyebrowText: $section->bookingContactEyebrow ?? 'CONTACT US',
            titleText: $section->bookingContactTitle ?? 'Get Information',
            descriptionText: $section->bookingContactDescription ?? '',
            rows: array_values(array_filter([
                self::buildBookingRow('phone', [$section->bookingContactPhoneOffice ?? '']),
                self::buildBookingRow('phone', [$section->bookingContactPhoneCashDesk ?? '']),
                self::buildBookingRow('clock', [$section->bookingContactHours ?? '']),
            ], static fn(?BookingCardRowData $row): bool => $row !== null)),
        );
    }

    private static function buildBookingVenueCard(JazzBookingCtaSectionContent $section, VenuesData $venuesData): BookingCardData
    {
        $venue = self::findVenueByName($venuesData, 'Patronaat') ?? ($venuesData->venues[0] ?? null);
        $addressLines = $venue instanceof VenueData
            ? array_values(array_filter([$venue->addressLine1, $venue->addressLine2], static fn(string $line): bool => $line !== ''))
            : [];

        return new BookingCardData(
            eyebrowText: $section->bookingVenueEyebrow ?? 'VENUE DETAILS',
            titleText: $section->bookingVenueTitle ?? 'Visit Patronaat',
            descriptionText: $section->bookingVenueDescription ?? '',
            rows: array_values(array_filter([
                self::buildBookingRow('location', $addressLines),
            ], static fn(?BookingCardRowData $row): bool => $row !== null)),
        );
    }

    private static function buildBookingTicketsCard(JazzBookingCtaSectionContent $section, PricingData $pricingData): BookingCardData
    {
        return new BookingCardData(
            eyebrowText: $section->bookingTicketsEyebrow ?? 'TICKETS',
            titleText: $section->bookingTicketsTitle ?? 'Purchase Tickets',
            descriptionText: $section->bookingTicketsDescription ?? '',
            rows: self::buildTicketSummaryRows($pricingData),
            isHighlighted: true,
        );
    }

    private static function findVenueByName(VenuesData $venuesData, string $name): ?VenueData
    {
        foreach ($venuesData->venues as $venue) {
            if (strcasecmp($venue->name, $name) === 0) {
                return $venue;
            }
        }

        return null;
    }

    /**
     * @return BookingCardRowData[]
     */
    private static function buildTicketSummaryRows(PricingData $pricingData): array
    {
        $rows = [];

        foreach ($pricingData->pricingCards as $card) {
            if ($card->items !== []) {
                $summary = self::buildIndividualTicketSummary($card);
                if ($summary !== '') {
                    $rows[] = new BookingCardRowData('', [$summary]);
                }

                continue;
            }

            $title = strtolower($card->title);
            if (str_contains($title, 'day pass')) {
                $rows[] = new BookingCardRowData('', ['Day Pass: ' . $card->price]);
                continue;
            }

            if (str_contains($title, '3-day') || str_contains($title, 'all-access')) {
                $rows[] = new BookingCardRowData('', ['3-Day Pass: ' . $card->price]);
            }
        }

        return $rows;
    }

    private static function buildIndividualTicketSummary(PricingCardData $card): string
    {
        $prices = [];

        foreach ($card->items as $item) {
            $amount = self::parsePriceAmount($item->price);
            if ($amount !== null) {
                $prices[] = $amount;
            }
        }

        if ($prices === []) {
            return '';
        }

        $min = min($prices);
        $max = max($prices);

        if ($min === $max) {
            return 'Single Shows: ' . self::formatCompactPrice($min);
        }

        return 'Single Shows: ' . self::formatCompactPrice($min) . '-' . ltrim(self::formatCompactPrice($max), '€');
    }

    private static function parsePriceAmount(string $price): ?float
    {
        if (preg_match('/(\d+(?:\.\d+)?)/', $price, $matches) !== 1) {
            return null;
        }

        return (float) $matches[1];
    }

    private static function formatCompactPrice(float $amount): string
    {
        if (fmod($amount, 1.0) === 0.0) {
            return '€' . (string)((int)$amount);
        }

        return FormatHelper::price($amount);
    }

    /**
     * @param string[] $lines
     */
    private static function buildBookingRow(string $icon, array $lines): ?BookingCardRowData
    {
        $filteredLines = array_values(array_filter($lines, static fn(string $line): bool => trim($line) !== ''));
        if ($filteredLines === []) {
            return null;
        }

        return new BookingCardRowData(
            icon: $icon,
            lines: $filteredLines,
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

    private static function buildPrimaryOverviewFallbackFromModel(\App\DTOs\Events\JazzArtistDetailEvent $event): string
    {
        if ($event->longDescriptionHtml === '') {
            return '';
        }

        return TextHelper::stripHtmlToText($event->longDescriptionHtml);
    }

    private static function buildPerformancesText(int $performanceCount): string
    {
        if ($performanceCount === 0) {
            return 'No performances currently scheduled';
        }

        return sprintf(
            '%d performance%s at Haarlem Jazz 2026',
            $performanceCount,
            $performanceCount === 1 ? '' : 's',
        );
    }

    private static function buildLiveCtaDescription(string $artistName, int $performanceCount): string
    {
        if ($performanceCount === 0) {
            return 'Do not miss the chance to see ' . $artistName . ' at Haarlem Jazz 2026. Check the schedule for upcoming appearances.';
        }

        $opportunities = $performanceCount <= 1
            ? 'there is one opportunity'
            : 'there are multiple opportunities';

        return 'Do not miss the chance to see ' . $artistName . ' perform live at Haarlem Jazz 2026. With '
            . $performanceCount . ' performance' . ($performanceCount === 1 ? '' : 's')
            . ' scheduled, ' . $opportunities . ' to experience the show.';
    }

    private static function buildPerformancesDescription(string $artistName): string
    {
        return 'Catch ' . $artistName . ' performing during the Haarlem Jazz Festival. Each performance offers a unique experience.';
    }

    private static function formatFirstPerformance(JazzArtistCardRecord $artist): string
    {
        if ($artist->firstPerformanceAt === null) {
            return '';
        }

        $prefix = $artist->firstPerformanceAt->format('D H:i');
        $location = trim($artist->firstPerformanceLocation);

        return $location !== '' ? $prefix . ' - ' . $location : $prefix;
    }

    private static function normalizeJazzScheduleLink(?string $link): string
    {
        $trimmed = trim((string)$link);

        if ($trimmed === '' || $trimmed === '#schedule' || $trimmed === '/jazz#schedule') {
            return '#jazz-schedule';
        }

        if ($trimmed === '/jazz#jazz-schedule') {
            return '#jazz-schedule';
        }

        return $trimmed;
    }
}
