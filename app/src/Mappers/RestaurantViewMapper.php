<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\RestaurantPageConstants;
use App\Content\RestaurantCardsSectionContent;
use App\Content\RestaurantDetailSectionContent;
use App\Content\RestaurantEventCmsData;
use App\Content\GradientSectionContent;
use App\Content\RestaurantInstructionsSectionContent;
use App\Content\RestaurantIntroSectionContent;
use App\Content\RestaurantIntroSplit2SectionContent;
use App\DTOs\Events\RestaurantDetailEvent;
use App\DTOs\Pages\RestaurantDetailPageData;
use App\DTOs\Pages\RestaurantListingData;
use App\DTOs\Pages\RestaurantPageData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Restaurant\Detail\AboutSectionData;
use App\ViewModels\Restaurant\Detail\ChefSectionData;
use App\ViewModels\Restaurant\Detail\ContactSectionData;
use App\ViewModels\Restaurant\Detail\GallerySectionData;
use App\ViewModels\Restaurant\Detail\LocationSectionData;
use App\ViewModels\Restaurant\Detail\MenuSectionData;
use App\ViewModels\Restaurant\Detail\PracticalInfoSectionData;
use App\ViewModels\Restaurant\Detail\ReservationSectionData;
use App\ViewModels\Restaurant\InstructionCardData;
use App\ViewModels\Restaurant\InstructionsSectionData;
use App\ViewModels\Restaurant\RestaurantCardData;
use App\ViewModels\Restaurant\RestaurantCardsSectionData;
use App\ViewModels\Restaurant\RestaurantDetailViewModel;
use App\ViewModels\Restaurant\RestaurantPageViewModel;

/**
 * Transforms restaurant domain data into page-level ViewModels.
 * Delegates content extraction to RestaurantContentParser.
 */
final class RestaurantViewMapper
{
    /** Builds the restaurant listing page ViewModel. */
    public static function toPageViewModel(RestaurantPageData $data, bool $isLoggedIn): RestaurantPageViewModel
    {
        $heroData = CmsMapper::toHeroData($data->heroContent, 'restaurant');
        $globalUi = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);

        return new RestaurantPageViewModel(
            heroData:               $heroData,
            globalUi:               $globalUi,
            gradientSection:        self::toGradientSection($data->gradientSection),
            introSplitSection:      self::toIntroSplitSection($data->introSplitSection),
            introSplit2Section:     self::toIntroSplit2Section($data->introSplit2Section),
            instructionsSection:    self::toInstructionsSection($data->instructionsSection),
            restaurantCardsSection: self::toRestaurantCardsSection($data->cardsSection, $data->listings),
        );
    }

    /** Builds the restaurant detail page ViewModel from event-based data. */
    public static function toDetailViewModel(RestaurantDetailPageData $data, bool $isLoggedIn): RestaurantDetailViewModel
    {
        $event = $data->event;
        $cms = $data->cms;
        $sharedCms = $data->sharedCms;
        $heroData = self::toEventDetailHeroData($event, $sharedCms, $cms, $data->featuredImagePath);
        $globalUi = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);

        return new RestaurantDetailViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            slug: $event->slug,
            name: $event->title,
            cms: self::buildCmsArray($cms, $sharedCms),
            contactSection: self::buildContactSection($cms, $sharedCms, $data->timeSlots),
            aboutSection: self::buildAboutSection($cms, $sharedCms, $event),
            chefSection: self::buildChefSection($cms, $sharedCms),
            menuSection: self::buildMenuSection($cms, $sharedCms),
            locationSection: self::buildLocationSection($cms, $sharedCms),
            practicalInfoSection: self::buildPracticalInfoSection($cms, $sharedCms, $data->priceCards),
            gallerySection: self::buildGallerySection($cms, $sharedCms),
            reservationSection: self::buildReservationSection($cms, $sharedCms, $data->timeSlots, $data->priceCards),
        );
    }

    /** Builds the reservation page ViewModel (same data, different page template). */
    public static function toReservationViewModel(RestaurantDetailPageData $data, bool $isLoggedIn): RestaurantDetailViewModel
    {
        return self::toDetailViewModel($data, $isLoggedIn);
    }

    // ── Detail page section builders ──────────────────────────────────

    private static function toEventDetailHeroData(
        RestaurantDetailEvent $event,
        RestaurantDetailSectionContent $sharedCms,
        RestaurantEventCmsData $cms,
        ?string $featuredImagePath,
    ): HeroData {
        $subtitleTemplate = $sharedCms->detailHeroSubtitleTemplate ?? '';
        $heroSubtitle = str_replace(
            ['{name}', '{cuisine}'],
            [$event->title, $cms->cuisineType ?? ''],
            $subtitleTemplate,
        );

        return new HeroData(
            mainTitle: $event->title,
            subtitle: $heroSubtitle,
            primaryButtonText: $sharedCms->detailHeroBtnPrimary ?? '',
            primaryButtonLink: '#reservation',
            secondaryButtonText: $sharedCms->detailHeroBtnSecondary ?? '',
            secondaryButtonLink: '/restaurant',
            backgroundImageUrl: $featuredImagePath ?? RestaurantPageConstants::DEFAULT_IMAGE,
            currentPage: 'restaurant',
        );
    }

    private static function buildCmsArray(RestaurantEventCmsData $cms, RestaurantDetailSectionContent $sharedCms): array
    {
        return [
            'cuisineType' => $cms->cuisineType,
            'stars' => $cms->stars,
            'michelinStars' => $cms->michelinStars,
            'seatsPerSession' => $cms->seatsPerSession,
            'durationMinutes' => $cms->durationMinutes,
            'priceAdult' => $cms->priceAdult,
            'durationLabel' => $sharedCms->detailLabelDuration,
            'seatsLabel' => $sharedCms->detailLabelSeats,
            'reservationTitle' => $sharedCms->detailReservationTitle,
            'reservationDescription' => $sharedCms->detailReservationDescription,
        ];
    }

    /**
     * @param string[] $timeSlots
     */
    private static function buildContactSection(
        RestaurantEventCmsData $cms,
        RestaurantDetailSectionContent $sharedCms,
        array $timeSlots,
    ): ContactSectionData
    {
        return new ContactSectionData(
            address: self::formatAddress($cms->addressLine, $cms->city),
            phone: $cms->phone ?? '',
            email: $cms->email ?? '',
            website: $cms->website ?? '',
            timeSlots: $timeSlots,
            labelTitle: $sharedCms->detailContactTitle ?? 'Contact',
            labelAddress: $sharedCms->detailLabelAddress ?? 'Address',
            labelContact: $sharedCms->detailLabelContact ?? 'Contact',
            labelOpenHours: $sharedCms->detailLabelOpenHours ?? 'Opening Hours',
        );
    }

    private static function buildAboutSection(RestaurantEventCmsData $cms, RestaurantDetailSectionContent $sharedCms, RestaurantDetailEvent $event): AboutSectionData
    {
        return new AboutSectionData(
            text: $cms->aboutText ?? $event->longDescriptionHtml,
            image: RestaurantContentParser::validateImagePath($cms->aboutImage ?? ''),
            labelTitlePrefix: $sharedCms->detailAboutTitlePrefix ?? 'About',
        );
    }

    private static function buildChefSection(RestaurantEventCmsData $cms, RestaurantDetailSectionContent $sharedCms): ChefSectionData
    {
        return new ChefSectionData(
            name: $cms->chefName ?? '',
            text: $cms->chefText ?? '',
            image: RestaurantContentParser::validateImagePath($cms->chefImage ?? ''),
            labelTitle: $sharedCms->detailChefTitle ?? 'The Chef',
        );
    }

    private static function buildMenuSection(RestaurantEventCmsData $cms, RestaurantDetailSectionContent $sharedCms): MenuSectionData
    {
        $images = array_values(array_map(
            [RestaurantContentParser::class, 'validateImagePath'],
            array_filter(
                [$cms->menuImage1 ?? '', $cms->menuImage2 ?? ''],
                static fn(string $image): bool => $image !== '',
            ),
        ));

        return new MenuSectionData(
            description: $cms->menuDescription ?? '',
            cuisineTags: self::parseCuisineTags($cms->cuisineType),
            images: $images,
            labelTitle: $sharedCms->detailMenuTitle ?? 'Menu',
            labelCuisineType: $sharedCms->detailMenuCuisineLabel ?? 'Cuisine',
        );
    }

    private static function buildLocationSection(RestaurantEventCmsData $cms, RestaurantDetailSectionContent $sharedCms): LocationSectionData
    {
        return new LocationSectionData(
            description: $cms->locationDescription ?? '',
            address: self::formatAddress($cms->addressLine, $cms->city),
            mapEmbedUrl: $cms->mapEmbedUrl ?? '',
            labelTitle: $sharedCms->detailLocationTitle ?? 'Location',
            labelAddress: $sharedCms->detailLocationAddressLabel ?? 'Address',
            labelMapFallback: $sharedCms->detailMapFallbackText ?? 'Map coming soon',
        );
    }

    /**
     * @param array{label: string, price: string}[] $priceCards
     */
    private static function buildPracticalInfoSection(
        RestaurantEventCmsData $cms,
        RestaurantDetailSectionContent $sharedCms,
        array $priceCards,
    ): PracticalInfoSectionData
    {
        return new PracticalInfoSectionData(
            cuisine: $cms->cuisineType ?? '',
            rating: (int)($cms->stars ?? 0),
            michelinStars: (int)($cms->michelinStars ?? 0),
            specialRequestsNote: $cms->specialRequestsNote ?? '',
            priceCards: $priceCards,
            labelTitle: $sharedCms->detailPracticalTitle ?? 'Practical Information',
            labelPriceFood: $sharedCms->detailLabelPriceFood ?? 'Price',
            labelRating: $sharedCms->detailLabelRating ?? 'Restaurant Rating',
            labelSpecialRequests: $sharedCms->detailLabelSpecialRequests ?? 'Special Requests',
            labelFestivalRated: $sharedCms->detailLabelFestivalRated ?? 'Festival Rating',
            labelMichelin: $sharedCms->detailLabelMichelin ?? 'Michelin',
            labelCuisineType: $sharedCms->detailMenuCuisineLabel ?? 'Cuisine',
        );
    }

    private static function buildGallerySection(RestaurantEventCmsData $cms, RestaurantDetailSectionContent $sharedCms): GallerySectionData
    {
        $images = array_filter([
            $cms->galleryImage1 ?? '',
            $cms->galleryImage2 ?? '',
            $cms->galleryImage3 ?? '',
        ], fn(string $url) => $url !== '');

        return new GallerySectionData(
            images: array_values(array_map(
                [RestaurantContentParser::class, 'validateImagePath'],
                $images,
            )),
            labelTitle: $sharedCms->detailGalleryTitle ?? 'Gallery',
        );
    }

    /**
     * @param string[] $timeSlots
     * @param array{label: string, price: string}[] $priceCards
     */
    private static function buildReservationSection(
        RestaurantEventCmsData $cms,
        RestaurantDetailSectionContent $sharedCms,
        array $timeSlots,
        array $priceCards,
    ): ReservationSectionData {
        return new ReservationSectionData(
            title: $sharedCms->detailReservationTitle ?? 'Make a Reservation',
            description: $sharedCms->detailReservationDescription ?? '',
            slotsLabel: $sharedCms->detailReservationSlotsLabel ?? 'Available Time Slots',
            note: $sharedCms->detailReservationNote ?? '',
            buttonText: $sharedCms->detailReservationBtn ?? 'Book Now',
            timeSlots: $timeSlots,
            priceCards: $priceCards,
            reservationImage: RestaurantContentParser::validateImagePath($cms->reservationImage ?? ''),
            reservationFee: RestaurantPageConstants::RESERVATION_FEE,
            validDates: RestaurantPageConstants::VALID_DATES,
        );
    }

    private static function formatAddress(?string $addressLine, ?string $city): string
    {
        $parts = array_filter(
            [trim((string)$addressLine), trim((string)$city)],
            static fn(string $part): bool => $part !== '',
        );

        return implode(', ', $parts);
    }

    /**
     * @return string[]
     */
    private static function parseCuisineTags(?string $cuisineType): array
    {
        if ($cuisineType === null || trim($cuisineType) === '') {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode(',', $cuisineType)),
            static fn(string $tag): bool => $tag !== '',
        ));
    }

    // ── Listing page section builders ──────────────────────────────────

    private static function toGradientSection(GradientSectionContent $cms): GradientSectionData
    {
        return new GradientSectionData(
            headingText: $cms->gradientHeading ?? '',
            subheadingText: $cms->gradientSubheading ?? '',
            backgroundImageUrl: RestaurantContentParser::validateImagePath($cms->gradientBackgroundImage ?? ''),
        );
    }

    private static function toIntroSplitSection(RestaurantIntroSectionContent $cms): IntroSplitSectionData
    {
        $heading = $cms->introHeading ?? '';
        $parsed = RestaurantContentParser::parseIntroBody($cms->introBody ?? '');
        $closing = $cms->introClosing ?? '';

        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: $parsed['bodyText'],
            imageUrl: RestaurantContentParser::validateImagePath($cms->introImage ?? ''),
            imageAltText: $cms->introImageAlt ?? $heading,
            subsections: $parsed['subsections'],
            closingLine: $closing !== '' ? $closing : $parsed['closingLine'],
        );
    }

    private static function toIntroSplit2Section(RestaurantIntroSplit2SectionContent $cms): ?IntroSplitSectionData
    {
        if ($cms->intro2Heading === null && $cms->intro2Body === null) {
            return null;
        }

        $heading = $cms->intro2Heading ?? '';

        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: $cms->intro2Body ?? '',
            imageUrl: RestaurantContentParser::validateImagePath($cms->intro2Image ?? ''),
            imageAltText: $cms->intro2ImageAlt ?? $heading,
        );
    }

    private static function toInstructionsSection(RestaurantInstructionsSectionContent $cms): ?InstructionsSectionData
    {
        if ($cms->instructionsTitle === null) {
            return null;
        }

        return new InstructionsSectionData(
            title: $cms->instructionsTitle,
            cards: [
                new InstructionCardData('1', $cms->instructionsCard1Title ?? '', $cms->instructionsCard1Text ?? '', 'search'),
                new InstructionCardData('2', $cms->instructionsCard2Title ?? '', $cms->instructionsCard2Text ?? '', 'calendar'),
                new InstructionCardData('3', $cms->instructionsCard3Title ?? '', $cms->instructionsCard3Text ?? '', 'check'),
            ],
        );
    }

    /**
     * @param RestaurantListingData[] $listings
     */
    private static function toRestaurantCardsSection(RestaurantCardsSectionContent $cms, array $listings): RestaurantCardsSectionData
    {
        return new RestaurantCardsSectionData(
            title: $cms->cardsTitle ?? '',
            subtitle: $cms->cardsSubtitle ?? '',
            filters: self::buildCuisineFilters($listings),
            cards: self::buildCards($listings),
        );
    }

    /**
     * @param RestaurantListingData[] $listings
     * @return string[]
     */
    private static function buildCuisineFilters(array $listings): array
    {
        $unique = [];
        foreach ($listings as $listing) {
            $cuisine = $listing->cms->cuisineType ?? '';
            $key = mb_strtolower($cuisine);
            if ($key !== '' && !isset($unique[$key])) {
                $unique[$key] = $cuisine;
            }
        }

        $labels = array_values($unique);
        sort($labels, SORT_NATURAL | SORT_FLAG_CASE);

        return ['All', ...$labels];
    }

    /**
     * @param RestaurantListingData[] $listings
     * @return RestaurantCardData[]
     */
    private static function buildCards(array $listings): array
    {
        $cards = [];
        foreach ($listings as $listing) {
            $cuisine = $listing->cms->cuisineType ?? '';
            $cards[] = new RestaurantCardData(
                id: $listing->event->eventId,
                name: $listing->event->title,
                cuisine: $cuisine,
                address: trim(($listing->cms->addressLine ?? '') . ', ' . ($listing->cms->city ?? ''), ', '),
                description: RestaurantContentParser::cleanDescription($listing->event->shortDescription),
                rating: (int)($listing->cms->stars ?? 0),
                image: $listing->imagePath ?? RestaurantPageConstants::DEFAULT_IMAGE,
                slug: $listing->event->slug,
                isVegan: str_contains(mb_strtolower($cuisine), 'vegan'),
            );
        }

        return $cards;
    }
}
