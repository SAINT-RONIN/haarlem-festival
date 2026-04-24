<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\RestaurantPageConstants;
use App\DTOs\Cms\RestaurantCardsSectionContent;
use App\DTOs\Cms\RestaurantDetailSectionContent;
use App\DTOs\Cms\GradientSectionContent;
use App\DTOs\Cms\RestaurantInstructionsSectionContent;
use App\DTOs\Cms\RestaurantIntroSectionContent;
use App\DTOs\Cms\RestaurantIntroSplit2SectionContent;
use App\DTOs\Domain\Pages\RestaurantPageData;
use App\Models\Restaurant;
use App\Services\Interfaces\IRestaurantService;
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
    /**
     * Builds the restaurant listing page ViewModel.
     *
     * @param string[] $cuisines Pre-computed cuisine filter labels from the service
     */
    public static function toPageViewModel(RestaurantPageData $data, array $cuisines, bool $isLoggedIn): RestaurantPageViewModel
    {
        $heroData = CmsMapper::toHeroData($data->heroContent, 'restaurant');
        $globalUi = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);

        return new RestaurantPageViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            gradientSection: self::toGradientSection($data->gradientSection),
            introSplitSection: self::toIntroSplitSection($data->introSplitSection),
            introSplit2Section: self::toIntroSplit2Section($data->introSplit2Section),
            instructionsSection: self::toInstructionsSection($data->instructionsSection),
            restaurantCardsSection: self::toRestaurantCardsSection($data->cardsSection, $data->restaurants, $cuisines),
        );
    }

    /** Builds the restaurant detail page ViewModel from a Restaurant domain object. */
    public static function toDetailViewModel(Restaurant $restaurant, IRestaurantService $service, bool $isLoggedIn): RestaurantDetailViewModel
    {
        $sharedCms = $service->getDetailLabels();
        $globalUi = CmsMapper::toGlobalUiData($service->getGlobalUi(), $isLoggedIn);
        $timeSlots = self::parseTimeSlots($restaurant->timeSlots);
        $priceCards = self::buildPriceCards($restaurant->priceAdult);
        $heroData = self::toDetailHeroData($restaurant, $sharedCms);

        return new RestaurantDetailViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            slug: $restaurant->slug,
            name: $restaurant->name,
            cms: self::buildCmsArray($restaurant, $sharedCms),
            contactSection: self::buildContactSection($restaurant, $sharedCms, $timeSlots),
            aboutSection: self::buildAboutSection($restaurant, $sharedCms),
            chefSection: self::buildChefSection($restaurant, $sharedCms),
            menuSection: self::buildMenuSection($restaurant, $sharedCms),
            locationSection: self::buildLocationSection($restaurant, $sharedCms),
            practicalInfoSection: self::buildPracticalInfoSection($restaurant, $sharedCms, $priceCards),
            gallerySection: self::buildGallerySection($restaurant, $sharedCms),
            reservationSection: self::buildReservationSection($restaurant, $sharedCms, $timeSlots, $priceCards),
        );
    }

    /** Builds the reservation page ViewModel (same data, different page template). */
    public static function toReservationViewModel(Restaurant $restaurant, IRestaurantService $service, bool $isLoggedIn): RestaurantDetailViewModel
    {
        return self::toDetailViewModel($restaurant, $service, $isLoggedIn);
    }

    // ── Detail page section builders ──────────────────────────────────

    private static function toDetailHeroData(Restaurant $r, RestaurantDetailSectionContent $sharedCms): HeroData
    {
        $subtitleTemplate = $sharedCms->detailHeroSubtitleTemplate ?? '';
        $heroSubtitle = str_replace(
            ['{name}', '{cuisine}'],
            [$r->name, $r->cuisineType ?? ''],
            $subtitleTemplate,
        );

        return new HeroData(
            mainTitle: $r->name,
            subtitle: $heroSubtitle,
            primaryButtonText: $sharedCms->detailHeroBtnPrimary ?? '',
            primaryButtonLink: '#reservation',
            secondaryButtonText: $sharedCms->detailHeroBtnSecondary ?? '',
            secondaryButtonLink: '/restaurant',
            backgroundImageUrl: $r->featuredImagePath ?? RestaurantPageConstants::DEFAULT_IMAGE,
            currentPage: 'restaurant',
        );
    }

    private static function buildCmsArray(Restaurant $r, RestaurantDetailSectionContent $sharedCms): array
    {
        return [
            'cuisineType' => $r->cuisineType,
            'stars' => $r->stars,
            'michelinStars' => $r->michelinStars,
            'seatsPerSession' => $r->seatsPerSession,
            'durationMinutes' => $r->durationMinutes,
            'priceAdult' => $r->priceAdult,
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
        Restaurant $r,
        RestaurantDetailSectionContent $sharedCms,
        array $timeSlots,
    ): ContactSectionData {
        return new ContactSectionData(
            address: self::formatAddress($r->addressLine, $r->city),
            phone: $r->phone ?? '',
            email: $r->email ?? '',
            website: $r->website ?? '',
            timeSlots: $timeSlots,
            labelTitle: $sharedCms->detailContactTitle ?? 'Contact',
            labelAddress: $sharedCms->detailLabelAddress ?? 'Address',
            labelContact: $sharedCms->detailLabelContact ?? 'Contact',
            labelOpenHours: $sharedCms->detailLabelOpenHours ?? 'Opening Hours',
        );
    }

    private static function buildAboutSection(Restaurant $r, RestaurantDetailSectionContent $sharedCms): AboutSectionData
    {
        return new AboutSectionData(
            text: $r->aboutText ?? $r->longDescriptionHtml,
            image: RestaurantContentParser::validateImagePath($r->aboutImage ?? ''),
            labelTitlePrefix: $sharedCms->detailAboutTitlePrefix ?? 'About',
        );
    }

    private static function buildChefSection(Restaurant $r, RestaurantDetailSectionContent $sharedCms): ChefSectionData
    {
        return new ChefSectionData(
            name: $r->chefName ?? '',
            text: $r->chefText ?? '',
            image: RestaurantContentParser::validateImagePath($r->chefImage ?? ''),
            labelTitle: $sharedCms->detailChefTitle ?? 'The Chef',
        );
    }

    private static function buildMenuSection(Restaurant $r, RestaurantDetailSectionContent $sharedCms): MenuSectionData
    {
        $images = array_values(array_map(
            [RestaurantContentParser::class, 'validateImagePath'],
            array_filter(
                [$r->menuImage1 ?? '', $r->menuImage2 ?? ''],
                static fn(string $image): bool => $image !== '',
            ),
        ));

        return new MenuSectionData(
            description: $r->menuDescription ?? '',
            cuisineTags: $r->cuisineTags,
            images: $images,
            labelTitle: $sharedCms->detailMenuTitle ?? 'Menu',
            labelCuisineType: $sharedCms->detailMenuCuisineLabel ?? 'Cuisine',
        );
    }

    private static function buildLocationSection(Restaurant $r, RestaurantDetailSectionContent $sharedCms): LocationSectionData
    {
        return new LocationSectionData(
            description: $r->locationDescription ?? '',
            address: self::formatAddress($r->addressLine, $r->city),
            mapEmbedUrl: $r->mapEmbedUrl ?? '',
            labelTitle: $sharedCms->detailLocationTitle ?? 'Location',
            labelAddress: $sharedCms->detailLocationAddressLabel ?? 'Address',
            labelMapFallback: $sharedCms->detailMapFallbackText ?? 'Map coming soon',
        );
    }

    /**
     * @param array{label: string, price: string}[] $priceCards
     */
    private static function buildPracticalInfoSection(
        Restaurant $r,
        RestaurantDetailSectionContent $sharedCms,
        array $priceCards,
    ): PracticalInfoSectionData {
        return new PracticalInfoSectionData(
            cuisine: $r->cuisineType ?? '',
            rating: (int) ($r->stars ?? 0),
            michelinStars: (int) ($r->michelinStars ?? 0),
            specialRequestsNote: $r->specialRequestsNote ?? '',
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

    private static function buildGallerySection(Restaurant $r, RestaurantDetailSectionContent $sharedCms): GallerySectionData
    {
        $images = array_filter([
            $r->galleryImage1 ?? '',
            $r->galleryImage2 ?? '',
            $r->galleryImage3 ?? '',
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
        Restaurant $r,
        RestaurantDetailSectionContent $sharedCms,
        array $timeSlots,
        array $priceCards,
    ): ReservationSectionData {
        $reservationImage = RestaurantContentParser::validateImagePath($r->reservationImage ?? '');
        if ($reservationImage === RestaurantContentParser::DEFAULT_IMAGE && $r->featuredImagePath !== null) {
            $reservationImage = RestaurantContentParser::validateImagePath($r->featuredImagePath);
        }

        return new ReservationSectionData(
            title: $sharedCms->detailReservationTitle ?? 'Make a Reservation',
            description: $sharedCms->detailReservationDescription ?? '',
            slotsLabel: $sharedCms->detailReservationSlotsLabel ?? 'Available Time Slots',
            note: $sharedCms->detailReservationNote ?? '',
            buttonText: $sharedCms->detailReservationBtn ?? 'Book Now',
            timeSlots: $timeSlots,
            priceCards: $priceCards,
            reservationImage: $reservationImage,
            reservationFee: RestaurantPageConstants::RESERVATION_FEE,
            validDates: RestaurantPageConstants::VALID_DATES,
        );
    }

    private static function formatAddress(?string $addressLine, ?string $city): string
    {
        $parts = array_filter(
            [trim((string) $addressLine), trim((string) $city)],
            static fn(string $part): bool => $part !== '',
        );

        return implode(', ', $parts);
    }

    // ── View-specific formatting helpers ────────────────────────────────

    /**
     * Splits a comma-separated time slots string into an array for display.
     *
     * @return string[]
     */
    private static function parseTimeSlots(?string $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    /**
     * Formats the adult price into display-ready price cards.
     * Under-12 is always half the adult price.
     *
     * @return array{label: string, price: string}[]
     */
    private static function buildPriceCards(?string $priceAdultStr): array
    {
        if ($priceAdultStr === null || $priceAdultStr === '') {
            return [];
        }

        $adult = max(0.0, (float) $priceAdultStr);

        return [
            ['label' => 'Per adult', 'price' => 'EUR ' . number_format($adult, 2)],
            ['label' => 'Under 12', 'price' => 'EUR ' . number_format($adult / 2, 2)],
        ];
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
     * @param Restaurant[] $restaurants
     * @param string[]     $cuisines Pre-computed cuisine filter labels
     */
    private static function toRestaurantCardsSection(RestaurantCardsSectionContent $cms, array $restaurants, array $cuisines): RestaurantCardsSectionData
    {
        return new RestaurantCardsSectionData(
            title: $cms->cardsTitle ?? '',
            subtitle: $cms->cardsSubtitle ?? '',
            filters: $cuisines,
            cards: self::buildCards($restaurants),
        );
    }

    /**
     * @param Restaurant[] $restaurants
     * @return RestaurantCardData[]
     */
    private static function buildCards(array $restaurants): array
    {
        $cards = [];
        foreach ($restaurants as $r) {
            $cuisine = trim($r->cuisineType ?? '');
            $cards[] = new RestaurantCardData(
                id: $r->id,
                name: $r->name,
                cuisine: $cuisine,
                address: self::formatAddress($r->addressLine, $r->city),
                description: self::buildCardDescription($r),
                rating: self::buildCardRating($r),
                image: $r->featuredImagePath ?? RestaurantPageConstants::DEFAULT_IMAGE,
                slug: $r->slug,
                isVegan: str_contains(mb_strtolower($cuisine), 'vegan'),
            );
        }

        return $cards;
    }

    private static function buildCardDescription(Restaurant $r): string
    {
        $candidates = [
            $r->aboutText,
            $r->locationDescription,
            $r->longDescriptionHtml,
            $r->shortDescription,
        ];

        foreach ($candidates as $candidate) {
            $description = RestaurantContentParser::cleanDescription((string) ($candidate ?? ''));
            if ($description !== '') {
                return $description;
            }
        }

        return '';
    }

    private static function buildCardRating(Restaurant $r): int
    {
        $cmsStars = trim($r->stars ?? '');
        if ($cmsStars !== '' && is_numeric($cmsStars)) {
            return (int) $cmsStars;
        }

        return 0;
    }
}
