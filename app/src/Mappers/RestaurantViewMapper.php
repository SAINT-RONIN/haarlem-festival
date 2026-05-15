<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\RestaurantPageConstants;
use App\DTOs\Cms\RestaurantCardsSectionContent;
use App\DTOs\Cms\GradientSectionContent;
use App\DTOs\Cms\RestaurantInstructionsSectionContent;
use App\DTOs\Cms\RestaurantIntroSectionContent;
use App\DTOs\Cms\RestaurantIntroSplit2SectionContent;
use App\DTOs\Domain\Pages\RestaurantPageData;
use App\DTOs\Cms\GlobalUiContent;
use App\Models\Restaurant;
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

final class RestaurantViewMapper
{
    public static function toPageViewModel(RestaurantPageData $data, bool $isLoggedIn): RestaurantPageViewModel
    {
        $heroData = CmsMapper::toHeroData($data->heroContent, 'restaurant');
        $globalUi = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);
        $cuisines = self::extractCuisineFilters($data->restaurants);

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

    /**
     * @param array<string, ?string> $labels Shared CMS labels for section titles, buttons, etc.
     */
    public static function toDetailViewModel(
        Restaurant $restaurant,
        array $labels,
        GlobalUiContent $globalUiContent,
        bool $isLoggedIn,
    ): RestaurantDetailViewModel {
        $globalUi = CmsMapper::toGlobalUiData($globalUiContent, $isLoggedIn);
        $timeSlots = self::parseTimeSlots($restaurant->timeSlots);
        $priceCards = self::buildPriceCards($restaurant);
        $heroData = self::toDetailHeroData($restaurant, $labels);

        return new RestaurantDetailViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            slug: $restaurant->slug,
            name: $restaurant->name,
            contactSection: self::buildContactSection($restaurant, $labels, $timeSlots),
            aboutSection: self::buildAboutSection($restaurant, $labels),
            chefSection: self::buildChefSection($restaurant, $labels),
            menuSection: self::buildMenuSection($restaurant, $labels),
            locationSection: self::buildLocationSection($restaurant, $labels),
            practicalInfoSection: self::buildPracticalInfoSection($restaurant, $labels, $priceCards),
            gallerySection: self::buildGallerySection($restaurant, $labels),
            reservationSection: self::buildReservationSection($restaurant, $labels, $timeSlots, $priceCards),
        );
    }

    // ── Detail page section builders ──────────────────────────────────

    private static function toDetailHeroData(Restaurant $r, array $labels): HeroData
    {
        $subtitleTemplate = $labels['detail_hero_subtitle_template'] ?? '';
        $heroSubtitle = str_replace(
            ['{name}', '{cuisine}'],
            [$r->name, $r->cuisineType ?? ''],
            $subtitleTemplate,
        );

        return new HeroData(
            mainTitle: $r->name,
            subtitle: $heroSubtitle,
            primaryButtonText: $labels['detail_hero_btn_primary'] ?? '',
            primaryButtonLink: '#reservation',
            secondaryButtonText: $labels['detail_hero_btn_secondary'] ?? '',
            secondaryButtonLink: '/restaurant',
            backgroundImageUrl: $r->featuredImagePath ?? RestaurantPageConstants::DEFAULT_IMAGE,
            currentPage: 'restaurant',
        );
    }

    /** @param string[] $timeSlots */
    private static function buildContactSection(Restaurant $r, array $labels, array $timeSlots): ContactSectionData
    {
        return new ContactSectionData(
            address: self::formatAddress($r->addressLine, $r->city),
            phone: $r->phone ?? '',
            email: $r->email ?? '',
            website: $r->website ?? '',
            timeSlots: $timeSlots,
            labelTitle: $labels['detail_contact_title'] ?? 'Contact',
            labelAddress: $labels['detail_label_address'] ?? 'Address',
            labelContact: $labels['detail_label_contact'] ?? 'Contact',
            labelOpenHours: $labels['detail_label_open_hours'] ?? 'Opening Hours',
        );
    }

    private static function buildAboutSection(Restaurant $r, array $labels): AboutSectionData
    {
        return new AboutSectionData(
            text: $r->aboutText ?? $r->longDescriptionHtml,
            image: RestaurantContentParser::validateImagePath($r->aboutImage ?? ''),
            labelTitlePrefix: $labels['detail_about_title_prefix'] ?? 'About',
        );
    }

    private static function buildChefSection(Restaurant $r, array $labels): ChefSectionData
    {
        return new ChefSectionData(
            name: $r->chefName ?? '',
            text: $r->chefText ?? '',
            image: RestaurantContentParser::validateImagePath($r->chefImage ?? ''),
            labelTitle: $labels['detail_chef_title'] ?? 'The Chef',
        );
    }

    private static function buildMenuSection(Restaurant $r, array $labels): MenuSectionData
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
            labelTitle: $labels['detail_menu_title'] ?? 'Menu',
            labelCuisineType: $labels['detail_menu_cuisine_label'] ?? 'Cuisine',
        );
    }

    private static function buildLocationSection(Restaurant $r, array $labels): LocationSectionData
    {
        return new LocationSectionData(
            description: $r->locationDescription ?? '',
            address: self::formatAddress($r->addressLine, $r->city),
            mapEmbedUrl: $r->mapEmbedUrl ?? '',
            labelTitle: $labels['detail_location_title'] ?? 'Location',
            labelAddress: $labels['detail_location_address_label'] ?? 'Address',
            labelMapFallback: $labels['detail_map_fallback_text'] ?? 'Map coming soon',
        );
    }

    /** @param array{label: string, price: string}[] $priceCards */
    private static function buildPracticalInfoSection(Restaurant $r, array $labels, array $priceCards): PracticalInfoSectionData
    {
        return new PracticalInfoSectionData(
            cuisine: $r->cuisineType ?? '',
            rating: $r->stars,
            michelinStars: $r->michelinStars,
            specialRequestsNote: $r->specialRequestsNote ?? '',
            priceCards: $priceCards,
            labelTitle: $labels['detail_practical_title'] ?? 'Practical Information',
            labelPriceFood: $labels['detail_label_price_food'] ?? 'Price',
            labelRating: $labels['detail_label_rating'] ?? 'Restaurant Rating',
            labelSpecialRequests: $labels['detail_label_special_requests'] ?? 'Special Requests',
            labelFestivalRated: $labels['detail_label_festival_rated'] ?? 'Festival Rating',
            labelMichelin: $labels['detail_label_michelin'] ?? 'Michelin',
            labelCuisineType: $labels['detail_menu_cuisine_label'] ?? 'Cuisine',
        );
    }

    private static function buildGallerySection(Restaurant $r, array $labels): GallerySectionData
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
            labelTitle: $labels['detail_gallery_title'] ?? 'Gallery',
        );
    }

    /**
     * @param string[] $timeSlots
     * @param array{label: string, price: string}[] $priceCards
     */
    private static function buildReservationSection(Restaurant $r, array $labels, array $timeSlots, array $priceCards): ReservationSectionData
    {
        $reservationImage = RestaurantContentParser::validateImagePath($r->reservationImage ?? '');
        if ($reservationImage === RestaurantPageConstants::DEFAULT_IMAGE && $r->featuredImagePath !== null) {
            $reservationImage = RestaurantContentParser::validateImagePath($r->featuredImagePath);
        }

        return new ReservationSectionData(
            title: $labels['detail_reservation_title'] ?? 'Make a Reservation',
            description: $labels['detail_reservation_description'] ?? '',
            slotsLabel: $labels['detail_reservation_slots_label'] ?? 'Available Time Slots',
            note: $labels['detail_reservation_note'] ?? '',
            buttonText: $labels['detail_reservation_btn'] ?? 'Book Now',
            timeSlots: $timeSlots,
            priceCards: $priceCards,
            reservationImage: $reservationImage,
            reservationFee: RestaurantContentParser::parseReservationFee($labels['detail_reservation_fee'] ?? null),
            validDates: RestaurantContentParser::parseValidDates($labels['detail_valid_dates'] ?? null),
            durationMinutes: $r->durationMinutes,
            durationLabel: $labels['detail_label_duration'] ?? 'Duration',
            seatsPerSession: $r->seatsPerSession,
            seatsLabel: $labels['detail_label_seats'] ?? 'Seats',
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

    // ── Parsing helpers ────────────────────────────────────────────────

    /** @return string[] */
    private static function parseTimeSlots(?string $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    /** @return array{label: string, price: string}[] */
    private static function buildPriceCards(Restaurant $r): array
    {
        if ($r->priceAdult <= 0) {
            return [];
        }

        return [
            ['label' => 'Per adult', 'price' => 'EUR ' . number_format($r->priceAdult, 2)],
            ['label' => 'Under 12', 'price' => 'EUR ' . number_format($r->priceAdult / 2, 2)],
        ];
    }

    // ── Cuisine filters ────────────────────────────────────────────────

    /**
     * @param Restaurant[] $restaurants
     * @return string[]
     */
    private static function extractCuisineFilters(array $restaurants): array
    {
        $unique = [];
        foreach ($restaurants as $restaurant) {
            foreach ($restaurant->cuisineTags as $tag) {
                $key = mb_strtolower($tag);
                if (!isset($unique[$key])) {
                    $unique[$key] = mb_convert_case($key, MB_CASE_TITLE, 'UTF-8');
                }
            }
        }

        $labels = array_values($unique);
        sort($labels, SORT_NATURAL | SORT_FLAG_CASE);

        return ['All', ...$labels];
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
     * @param string[]     $cuisines
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
                rating: $r->stars,
                image: $r->featuredImagePath ?? RestaurantPageConstants::DEFAULT_IMAGE,
                slug: $r->slug,
                isVegan: str_contains(mb_strtolower($cuisine), 'vegan'),
            );
        }

        return $cards;
    }

    private static function buildCardDescription(Restaurant $r): string
    {
        $candidates = [$r->aboutText, $r->locationDescription, $r->longDescriptionHtml, $r->shortDescription];

        foreach ($candidates as $candidate) {
            $description = RestaurantContentParser::cleanDescription((string) ($candidate ?? ''));
            if ($description !== '') {
                return $description;
            }
        }

        return '';
    }
}
