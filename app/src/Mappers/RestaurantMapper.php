<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\RestaurantPageConstants;
use App\Models\RestaurantDetailPageData;
use App\Models\RestaurantDetailSectionContent;
use App\Models\RestaurantEventCmsData;
use App\Models\RestaurantCardsSectionContent;
use App\Models\RestaurantGradientSectionContent;
use App\Models\RestaurantInstructionsSectionContent;
use App\Models\RestaurantIntroSectionContent;
use App\Models\RestaurantIntroSplit2SectionContent;
use App\Models\RestaurantListingData;
use App\Models\RestaurantPageData;
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

final class RestaurantMapper
{
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    // ─────────────────────────────────────────────────────────────────────────
    // Public entry points
    // ─────────────────────────────────────────────────────────────────────────

    public static function toPageViewModel(RestaurantPageData $data, bool $isLoggedIn): RestaurantPageViewModel
    {
        $heroData = CmsMapper::toHeroData($data->heroContent, RestaurantPageConstants::CURRENT_PAGE);
        $globalUi = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);

        return new RestaurantPageViewModel(
            heroData:               $heroData,
            globalUi:               $globalUi,
            cms:                    CmsMapper::toCmsData($heroData, $globalUi),
            gradientSection:        self::buildGradientSection($data->gradientSection),
            introSplitSection:      self::buildIntroSplitSection($data->introSplitSection),
            introSplit2Section:     self::buildIntroSplit2Section($data->introSplit2Section),
            instructionsSection:    self::buildInstructionsSection($data->instructionsSection),
            restaurantCardsSection: self::buildRestaurantCardsSection($data->cardsSection, $data->restaurants),
        );
    }

    public static function toDetailViewModel(RestaurantDetailPageData $data, bool $isLoggedIn): RestaurantDetailViewModel
    {
        $heroData = self::buildDetailHeroData($data);
        $globalUi = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);

        return new RestaurantDetailViewModel(
            heroData:      $heroData,
            globalUi:      $globalUi,
            cms:           CmsMapper::toCmsData($heroData, $globalUi),
            slug:          $data->event->slug,
            name:          $data->event->title,
            contact:       self::buildContactSection($data),
            about:         self::buildAboutSection($data),
            chef:          self::buildChefSection($data),
            menu:          self::buildMenuSection($data),
            location:      self::buildLocationSection($data),
            practicalInfo: self::buildPracticalInfoSection($data),
            gallery:       self::buildGallerySection($data),
            reservation:   self::buildReservationSection($data, [], 0.0),
        );
    }

    public static function toReservationViewModel(RestaurantDetailPageData $data, bool $isLoggedIn): RestaurantDetailViewModel
    {
        $cms   = $data->cms;
        $event = $data->event;

        $heroSubtitle = str_replace(
            ['{name}', '{cuisine}'],
            [$event->title, $cms->cuisineType ?? ''],
            $data->sharedCms->detailHeroSubtitleTemplate ?? '',
        );

        $heroData = new HeroData(
            mainTitle:           $event->title,
            subtitle:            $heroSubtitle,
            primaryButtonText:   $data->sharedCms->detailHeroBtnPrimary ?? '',
            primaryButtonLink:   '#reservation-form',
            secondaryButtonText: '',
            secondaryButtonLink: '/restaurant/' . $event->slug,
            backgroundImageUrl:  $data->featuredImagePath ?? RestaurantPageConstants::DEFAULT_IMAGE,
            currentPage:         RestaurantPageConstants::CURRENT_PAGE,
        );

        $globalUi = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);

        return new RestaurantDetailViewModel(
            heroData:      $heroData,
            globalUi:      $globalUi,
            cms:           CmsMapper::toCmsData($heroData, $globalUi),
            slug:          $event->slug,
            name:          $event->title,
            contact:       self::buildContactSection($data),
            about:         self::buildAboutSection($data),
            chef:          self::buildChefSection($data),
            menu:          self::buildMenuSection($data),
            location:      self::buildLocationSection($data),
            practicalInfo: self::buildPracticalInfoSection($data),
            gallery:       self::buildGallerySection($data),
            reservation:   self::buildReservationSection(
                $data,
                RestaurantPageConstants::VALID_DATES,
                RestaurantPageConstants::RESERVATION_FEE,
            ),
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Detail section builders
    // ─────────────────────────────────────────────────────────────────────────

    private static function buildContactSection(RestaurantDetailPageData $data): ContactSectionData
    {
        $cms       = $data->cms;
        $sharedCms = $data->sharedCms;

        return new ContactSectionData(
            address:        self::buildAddress($cms),
            phone:          $cms->phone    ?? '',
            email:          $cms->email    ?? '',
            website:        $cms->website  ?? '',
            timeSlots:      $data->timeSlots,
            labelTitle:     $sharedCms->detailContactTitle    ?? '',
            labelAddress:   $sharedCms->detailLabelAddress    ?? '',
            labelContact:   $sharedCms->detailLabelContact    ?? '',
            labelOpenHours: $sharedCms->detailLabelOpenHours  ?? '',
        );
    }

    private static function buildAboutSection(RestaurantDetailPageData $data): AboutSectionData
    {
        $cms = $data->cms;

        return new AboutSectionData(
            text:             str_replace('\n', "\n", $cms->aboutText ?? ''),
            image:            self::validateImagePath($cms->aboutImage ?? ''),
            labelTitlePrefix: $data->sharedCms->detailAboutTitlePrefix ?? '',
        );
    }

    private static function buildChefSection(RestaurantDetailPageData $data): ChefSectionData
    {
        $cms = $data->cms;

        return new ChefSectionData(
            name:       $cms->chefName ?? '',
            text:       str_replace('\n', "\n", $cms->chefText ?? ''),
            image:      self::validateImagePath($cms->chefImage ?? ''),
            labelTitle: $data->sharedCms->detailChefTitle ?? '',
        );
    }

    private static function buildMenuSection(RestaurantDetailPageData $data): MenuSectionData
    {
        $cms         = $data->cms;
        $cuisineTags = array_values(array_filter(array_map('trim', explode(',', $cms->cuisineType ?? ''))));
        $images      = array_values(array_filter([
            self::validateImagePath($cms->menuImage1 ?? ''),
            self::validateImagePath($cms->menuImage2 ?? ''),
        ], fn(string $p) => $p !== RestaurantPageConstants::DEFAULT_IMAGE || ($cms->menuImage1 ?? '') !== ''));

        return new MenuSectionData(
            description:      $cms->menuDescription ?? '',
            cuisineTags:      $cuisineTags,
            images:           $images,
            labelTitle:       $data->sharedCms->detailMenuTitle       ?? '',
            labelCuisineType: $data->sharedCms->detailMenuCuisineLabel ?? '',
        );
    }

    private static function buildLocationSection(RestaurantDetailPageData $data): LocationSectionData
    {
        $cms = $data->cms;

        return new LocationSectionData(
            description:      str_replace('\n', "\n", $cms->locationDescription ?? ''),
            address:          self::buildAddress($cms),
            mapEmbedUrl:      $cms->mapEmbedUrl ?? '',
            labelTitle:       $data->sharedCms->detailLocationTitle        ?? '',
            labelAddress:     $data->sharedCms->detailLocationAddressLabel  ?? '',
            labelMapFallback: $data->sharedCms->detailMapFallbackText       ?? '',
        );
    }

    private static function buildPracticalInfoSection(RestaurantDetailPageData $data): PracticalInfoSectionData
    {
        $cms = $data->cms;

        return new PracticalInfoSectionData(
            cuisine:              $cms->cuisineType          ?? '',
            rating:               (int) ($cms->stars         ?? 0),
            michelinStars:        (int) ($cms->michelinStars ?? 0),
            specialRequestsNote:  $cms->specialRequestsNote  ?? '',
            priceCards:           $data->priceCards,
            labelTitle:           $data->sharedCms->detailPracticalTitle       ?? '',
            labelPriceFood:       $data->sharedCms->detailLabelPriceFood       ?? '',
            labelRating:          $data->sharedCms->detailLabelRating          ?? '',
            labelSpecialRequests: $data->sharedCms->detailLabelSpecialRequests ?? '',
            labelFestivalRated:   $data->sharedCms->detailLabelFestivalRated   ?? '',
            labelMichelin:        $data->sharedCms->detailLabelMichelin        ?? '',
            labelCuisineType:     $data->sharedCms->detailMenuCuisineLabel     ?? '',
        );
    }

    private static function buildGallerySection(RestaurantDetailPageData $data): GallerySectionData
    {
        $cms    = $data->cms;
        $images = array_values(array_filter([
            self::validateImagePath($cms->galleryImage1 ?? ''),
            self::validateImagePath($cms->galleryImage2 ?? ''),
            self::validateImagePath($cms->galleryImage3 ?? ''),
        ], fn(string $p) => $p !== RestaurantPageConstants::DEFAULT_IMAGE));

        if ($images === []) {
            $images = [RestaurantPageConstants::DEFAULT_IMAGE];
        }

        return new GallerySectionData(
            images:     $images,
            labelTitle: $data->sharedCms->detailGalleryTitle ?? '',
        );
    }

    private static function buildReservationSection(
        RestaurantDetailPageData $data,
        array $festivalDates,
        float $reservationFeePerPerson,
    ): ReservationSectionData {
        $cms        = $data->cms;
        $sharedCms  = $data->sharedCms;
        $priceAdult = $cms->priceAdult !== null ? (float) $cms->priceAdult : null;
        $priceChild = $priceAdult !== null ? $priceAdult / 2 : null;

        return new ReservationSectionData(
            image:                   self::validateImagePath($cms->reservationImage ?? ''),
            timeSlots:               $data->timeSlots,
            priceCards:              $data->priceCards,
            durationMinutes:         (int) ($cms->durationMinutes  ?? 0),
            seatsPerSession:         (int) ($cms->seatsPerSession   ?? 0),
            priceAdult:              $priceAdult,
            priceChild:              $priceChild,
            festivalDates:           $festivalDates,
            reservationFeePerPerson: $reservationFeePerPerson,
            labelTitle:              $sharedCms->detailReservationTitle       ?? '',
            labelDesc:               $sharedCms->detailReservationDescription ?? '',
            labelSlots:              $sharedCms->detailReservationSlotsLabel  ?? '',
            labelNote:               $sharedCms->detailReservationNote        ?? '',
            labelButton:             $sharedCms->detailReservationBtn         ?? '',
            labelDuration:           $sharedCms->detailLabelDuration          ?? '',
            labelSeats:              $sharedCms->detailLabelSeats             ?? '',
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Listing page section builders
    // ─────────────────────────────────────────────────────────────────────────

    private static function buildGradientSection(RestaurantGradientSectionContent $cms): GradientSectionData
    {
        return new GradientSectionData(
            headingText:        $cms->gradientHeading ?? '',
            subheadingText:     $cms->gradientSubheading ?? '',
            backgroundImageUrl: self::validateImagePath($cms->gradientBackgroundImage ?? ''),
        );
    }

    private static function buildIntroSplitSection(RestaurantIntroSectionContent $cms): IntroSplitSectionData
    {
        $heading = $cms->introHeading ?? '';
        $parsed  = self::parseIntroBody($cms->introBody ?? '');
        $closing = $cms->introClosing ?? '';

        return new IntroSplitSectionData(
            headingText:  $heading,
            bodyText:     $parsed['bodyText'],
            imageUrl:     self::validateImagePath($cms->introImage ?? ''),
            imageAltText: $cms->introImageAlt ?? $heading,
            subsections:  $parsed['subsections'],
            closingLine:  $closing !== '' ? $closing : $parsed['closingLine'],
        );
    }

    private static function buildIntroSplit2Section(RestaurantIntroSplit2SectionContent $cms): ?IntroSplitSectionData
    {
        if ($cms->intro2Heading === null && $cms->intro2Body === null) {
            return null;
        }

        $heading = $cms->intro2Heading ?? '';

        return new IntroSplitSectionData(
            headingText:  $heading,
            bodyText:     $cms->intro2Body ?? '',
            imageUrl:     self::validateImagePath($cms->intro2Image ?? ''),
            imageAltText: $cms->intro2ImageAlt ?? $heading,
        );
    }

    private static function buildInstructionsSection(RestaurantInstructionsSectionContent $cms): ?InstructionsSectionData
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
     * @param RestaurantListingData[] $restaurants
     */
    private static function buildRestaurantCardsSection(
        RestaurantCardsSectionContent $cms,
        array $restaurants,
    ): RestaurantCardsSectionData {
        return new RestaurantCardsSectionData(
            title:         $cms->cardsTitle    ?? '',
            subtitle:      $cms->cardsSubtitle ?? '',
            filters:       self::buildCuisineFilters($restaurants),
            cards:         self::buildCards($restaurants),
            labelFilters:  $cms->cardsLabelFilters  ?? 'Filters',
            labelAboutBtn: $cms->cardsLabelAboutBtn ?? 'About it',
            labelBookBtn:  $cms->cardsLabelBookBtn  ?? 'Book table',
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Hero builder for detail / reservation pages
    // ─────────────────────────────────────────────────────────────────────────

    private static function buildDetailHeroData(RestaurantDetailPageData $data): HeroData
    {
        $sharedCms = $data->sharedCms;
        $heroSubtitle = str_replace(
            ['{name}', '{cuisine}'],
            [$data->event->title, $data->cms->cuisineType ?? ''],
            $sharedCms->detailHeroSubtitleTemplate ?? '',
        );

        return new HeroData(
            mainTitle:           $data->event->title,
            subtitle:            $heroSubtitle,
            primaryButtonText:   $sharedCms->detailHeroBtnPrimary ?? '',
            primaryButtonLink:   '/restaurant/' . $data->event->slug . '/reservation',
            secondaryButtonText: '',
            secondaryButtonLink: '',
            backgroundImageUrl:  $data->featuredImagePath ?? RestaurantPageConstants::DEFAULT_IMAGE,
            currentPage:         RestaurantPageConstants::CURRENT_PAGE,
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Card / filter helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * @param RestaurantListingData[] $restaurants
     */
    private static function buildCuisineFilters(array $restaurants): array
    {
        $unique = [];

        foreach ($restaurants as $listing) {
            foreach (array_filter(array_map('trim', explode(',', $listing->cms->cuisineType ?? ''))) as $part) {
                $key = mb_strtolower($part);
                if ($key !== '' && !isset($unique[$key])) {
                    $unique[$key] = $part;
                }
            }
        }

        $labels = array_values($unique);
        sort($labels, SORT_NATURAL | SORT_FLAG_CASE);

        return ['All', ...$labels];
    }

    /**
     * @param RestaurantListingData[] $restaurants
     * @return RestaurantCardData[]
     */
    private static function buildCards(array $restaurants): array
    {
        return array_map(fn(RestaurantListingData $listing) => new RestaurantCardData(
            slug:        $listing->event->slug,
            name:        $listing->event->title,
            cuisine:     $listing->cms->cuisineType ?? '',
            address:     self::buildAddress($listing->cms),
            description: self::cleanDescription($listing->event->shortDescription),
            rating:      (int) ($listing->cms->stars ?? 0),
            image:       $listing->imagePath ?? RestaurantPageConstants::DEFAULT_IMAGE,
            isVegan:     stripos($listing->cms->cuisineType ?? '', 'vegan') !== false,
        ), $restaurants);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Shared helpers
    // ─────────────────────────────────────────────────────────────────────────

    private static function buildAddress(RestaurantEventCmsData $cms): string
    {
        $address = trim($cms->addressLine ?? '');

        if (($cms->city ?? '') !== '') {
            $address .= ', ' . $cms->city;
        }

        return $address;
    }

    private static function cleanDescription(string $text): string
    {
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }

    private static function validateImagePath(string $path): string
    {
        if ($path === '' || !str_starts_with($path, '/assets/')) {
            return RestaurantPageConstants::DEFAULT_IMAGE;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::VALID_IMAGE_EXTENSIONS, true)) {
            return RestaurantPageConstants::DEFAULT_IMAGE;
        }

        return $path;
    }

    private static function parseIntroBody(string $rawBody): array
    {
        $result = ['bodyText' => '', 'subsections' => null, 'closingLine' => null];

        $rawBody = trim($rawBody);
        if ($rawBody === '') {
            return $result;
        }

        $rawBody = str_replace(["\r\n", "\r"], "\n", $rawBody);
        $blocks  = preg_split("/\n\n+/", $rawBody);

        if ($blocks === false || $blocks === []) {
            $result['bodyText'] = $rawBody;
            return $result;
        }

        $bodyParts   = [];
        $subsections = [];
        $i = 0;

        for (; $i < count($blocks); $i++) {
            $b = trim((string)$blocks[$i]);
            if (str_starts_with($b, '## ')) {
                break;
            }
            if ($b !== '') {
                $bodyParts[] = $b;
            }
        }
        $result['bodyText'] = implode("\n\n", $bodyParts);

        for (; $i < count($blocks); $i++) {
            $b = trim((string)$blocks[$i]);
            if ($b === '') {
                continue;
            }

            if (str_starts_with($b, '## ')) {
                $heading = trim(substr($b, 3));
                $text    = '';

                if (($i + 1) < count($blocks)) {
                    $next = trim((string)$blocks[$i + 1]);
                    if ($next !== '' && !str_starts_with($next, '## ')) {
                        $text = $next;
                        $i++;
                    }
                }

                $subsections[] = ['heading' => $heading, 'text' => $text];
                continue;
            }

            $result['closingLine'] = $b;
        }

        if ($subsections !== []) {
            $result['subsections'] = $subsections;
        }

        return $result;
    }
}
