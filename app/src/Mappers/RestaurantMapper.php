<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantCardsSectionContent;
use App\Models\RestaurantDetailData;
use App\Models\RestaurantDetailSectionContent;
use App\Models\RestaurantGradientSectionContent;
use App\Models\RestaurantInstructionsSectionContent;
use App\Models\RestaurantIntroSectionContent;
use App\Models\RestaurantIntroSplit2SectionContent;
use App\Models\RestaurantPageData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Restaurant\InstructionCardData;
use App\ViewModels\Restaurant\InstructionsSectionData;
use App\ViewModels\Restaurant\RestaurantCardData;
use App\ViewModels\Restaurant\RestaurantCardsSectionData;
use App\ViewModels\Restaurant\RestaurantDetailViewModel;
use App\ViewModels\Restaurant\RestaurantPageViewModel;

final class RestaurantMapper
{
    private const DEFAULT_IMAGE = '/assets/Image/Image (Yummy).png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    public static function toPageViewModel(RestaurantPageData $data, bool $isLoggedIn): RestaurantPageViewModel
    {
        $heroData = CmsMapper::toHeroData($data->heroContent, 'restaurant');
        $globalUi = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);

        return new RestaurantPageViewModel(
            heroData:               $heroData,
            globalUi:               $globalUi,
            cms:                    CmsMapper::toCmsData($heroData, $globalUi),
            gradientSection:        self::toGradientSection($data->gradientSection),
            introSplitSection:      self::toIntroSplitSection($data->introSplitSection),
            introSplit2Section:     self::toIntroSplit2Section($data->introSplit2Section),
            instructionsSection:    self::toInstructionsSection($data->instructionsSection),
            restaurantCardsSection: self::toRestaurantCardsSection($data->cardsSection, $data->restaurants),
        );
    }

    public static function toDetailViewModel(RestaurantDetailData $data, bool $isLoggedIn): RestaurantDetailViewModel
    {
        $restaurant  = $data->restaurant;
        $cms         = $data->cms;
        $cuisineString = $restaurant->cuisineType;
        $heroData    = self::toDetailHeroData($restaurant, $cms, $cuisineString);
        $globalUi    = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);

        return new RestaurantDetailViewModel(...array_merge(
            ['heroData' => $heroData, 'globalUi' => $globalUi, 'cms' => CmsMapper::toCmsData($heroData, $globalUi)],
            self::buildDetailDomainFields($restaurant, $data),
            self::buildDetailCmsLabels($cms),
        ));
    }

    /**
     * @return array<string, mixed>
     */
    private static function buildDetailDomainFields(Restaurant $restaurant, RestaurantDetailData $data): array
    {
        $cuisineTags = array_values(array_filter(array_map('trim', explode(',', $restaurant->cuisineType))));
        $imagesByType = self::groupImagesByType($data->images);

        return [
            'id'          => $restaurant->restaurantId,
            'name'        => $restaurant->name,
            'cuisine'     => $restaurant->cuisineType,
            'address'     => self::buildAddress($restaurant),
            'description' => self::cleanDescription($restaurant->descriptionHtml),
            'rating'      => $restaurant->stars ?? 0,
            'image'       => $restaurant->imagePath ?? self::DEFAULT_IMAGE,
            'phone'       => $restaurant->phone ?? '',
            'email'       => $restaurant->email ?? '',
            'website'     => $restaurant->website ?? '',
            'aboutText'   => str_replace('\n', "\n", $restaurant->aboutText ?? ''),
            'aboutImage'  => $imagesByType['about'][0] ?? self::DEFAULT_IMAGE,
            'chefName'    => $restaurant->chefName ?? '',
            'chefText'    => str_replace('\n', "\n", $restaurant->chefText ?? ''),
            'chefImage'   => $imagesByType['chef'][0] ?? self::DEFAULT_IMAGE,
            'menuDescription' => $restaurant->menuDescription ?? '',
            'cuisineTags'     => $cuisineTags,
            'menuImages'      => $imagesByType['menu'] ?? [],
            'locationDescription' => str_replace('\n', "\n", $restaurant->locationDescription ?? ''),
            'mapEmbedUrl'     => $restaurant->mapEmbedUrl ?? '',
            'michelinStars'   => $restaurant->michelinStars ?? 0,
            'seatsPerSession' => $restaurant->seatsPerSession ?? 0,
            'durationMinutes' => $restaurant->durationMinutes ?? 0,
            'specialRequestsNote' => $restaurant->specialRequestsNote ?? '',
            'galleryImages'   => $imagesByType['gallery'] ?? [self::DEFAULT_IMAGE],
            'reservationImage' => $imagesByType['reservation'][0] ?? self::DEFAULT_IMAGE,
            'timeSlots'       => $data->timeSlots,
            'priceCards'      => $data->priceCards,
            'priceAdult'      => $restaurant->priceAdult,
            'priceChild'      => $restaurant->priceChild,
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function buildDetailCmsLabels(RestaurantDetailSectionContent $cms): array
    {
        return [
            'labelContactTitle'    => $cms->detailContactTitle ?? '',
            'labelAddress'         => $cms->detailLabelAddress ?? '',
            'labelContact'         => $cms->detailLabelContact ?? '',
            'labelOpenHours'       => $cms->detailLabelOpenHours ?? '',
            'labelPracticalTitle'  => $cms->detailPracticalTitle ?? '',
            'labelPriceFood'       => $cms->detailLabelPriceFood ?? '',
            'labelRating'          => $cms->detailLabelRating ?? '',
            'labelSpecialRequests' => $cms->detailLabelSpecialRequests ?? '',
            'labelGalleryTitle'    => $cms->detailGalleryTitle ?? '',
            'labelAboutPrefix'     => $cms->detailAboutTitlePrefix ?? '',
            'labelChefTitle'       => $cms->detailChefTitle ?? '',
            'labelMenuTitle'       => $cms->detailMenuTitle ?? '',
            'labelCuisineType'     => $cms->detailMenuCuisineLabel ?? '',
            'labelLocationTitle'   => $cms->detailLocationTitle ?? '',
            'labelLocationAddress' => $cms->detailLocationAddressLabel ?? '',
            'labelReservationTitle' => $cms->detailReservationTitle ?? '',
            'labelReservationDesc' => $cms->detailReservationDescription ?? '',
            'labelSlotsLabel'      => $cms->detailReservationSlotsLabel ?? '',
            'labelReservationNote' => $cms->detailReservationNote ?? '',
            'labelReservationBtn'  => $cms->detailReservationBtn ?? '',
            'labelDuration'        => $cms->detailLabelDuration ?? '',
            'labelSeats'           => $cms->detailLabelSeats ?? '',
            'labelFestivalRated'   => $cms->detailLabelFestivalRated ?? '',
            'labelMichelin'        => $cms->detailLabelMichelin ?? '',
            'labelMapFallback'     => $cms->detailMapFallbackText ?? '',
        ];
    }

    private static function toDetailHeroData(Restaurant $restaurant, RestaurantDetailSectionContent $cms, string $cuisineString): HeroData
    {
        $subtitleTemplate = $cms->detailHeroSubtitleTemplate ?? '';
        $heroSubtitle = str_replace(
            ['{name}', '{cuisine}'],
            [$restaurant->name, $cuisineString],
            $subtitleTemplate
        );

        return new HeroData(
            mainTitle:           $restaurant->name,
            subtitle:            $heroSubtitle,
            primaryButtonText:   $cms->detailHeroBtnPrimary ?? '',
            primaryButtonLink:   '/restaurant/' . $restaurant->restaurantId . '/reservation',
            secondaryButtonText: '',
            secondaryButtonLink: '',
            backgroundImageUrl:  $restaurant->imagePath ?? self::DEFAULT_IMAGE,
            currentPage:         'restaurant',
        );
    }

    private static function toGradientSection(RestaurantGradientSectionContent $cms): GradientSectionData
    {
        return new GradientSectionData(
            headingText:        $cms->gradientHeading ?? '',
            subheadingText:     $cms->gradientSubheading ?? '',
            backgroundImageUrl: self::validateImagePath($cms->gradientBackgroundImage ?? ''),
        );
    }

    private static function toIntroSplitSection(RestaurantIntroSectionContent $cms): IntroSplitSectionData
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

    private static function toIntroSplit2Section(RestaurantIntroSplit2SectionContent $cms): ?IntroSplitSectionData
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
     */
    private static function toRestaurantCardsSection(RestaurantCardsSectionContent $cms, array $restaurants): RestaurantCardsSectionData
    {
        return new RestaurantCardsSectionData(
            title:    $cms->cardsTitle ?? '',
            subtitle: $cms->cardsSubtitle ?? '',
            filters:  self::buildCuisineFilters($restaurants),
            cards:    self::buildCards($restaurants),
        );
    }

    /**
     * @param Restaurant[] $restaurants
     */
    private static function buildCuisineFilters(array $restaurants): array
    {
        $unique = [];

        foreach ($restaurants as $restaurant) {
            foreach (array_filter(array_map('trim', explode(',', $restaurant->cuisineType))) as $part) {
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
     * @param Restaurant[] $restaurants
     * @return RestaurantCardData[]
     */
    private static function buildCards(array $restaurants): array
    {
        $cards = [];

        foreach ($restaurants as $restaurant) {
            $cards[] = new RestaurantCardData(
                id:          $restaurant->restaurantId,
                name:        $restaurant->name,
                cuisine:     $restaurant->cuisineType,
                address:     self::buildAddress($restaurant),
                description: self::cleanDescription($restaurant->descriptionHtml),
                rating:      $restaurant->stars ?? 0,
                image:       $restaurant->imagePath ?? self::DEFAULT_IMAGE,
            );
        }

        return $cards;
    }

    private static function buildAddress(Restaurant $restaurant): string
    {
        $address = trim($restaurant->addressLine);

        if ($restaurant->city !== '') {
            $address .= ', ' . $restaurant->city;
        }

        return $address;
    }

    private static function cleanDescription(string $html): string
    {
        $html = trim($html);

        if ($html === '' || $html === '<p></p>') {
            return '';
        }

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }

    /**
     * Groups RestaurantImage objects by their ImageType.
     * Returns an array keyed by type, each value being an array of image paths.
     *
     * @param \App\Models\RestaurantImage[] $images
     * @return array<string, string[]>
     */
    private static function groupImagesByType(array $images): array
    {
        $grouped = [];
        foreach ($images as $image) {
            $grouped[$image->imageType][] = $image->imagePath;
        }
        return $grouped;
    }

    public static function toReservation(array $post, int $restaurantId, float $feePerPerson): Reservation
    {
        $adultsCount   = (int) ($post['adults_count']   ?? 0);
        $childrenCount = (int) ($post['children_count'] ?? 0);

        return new Reservation(
            restaurantId:    $restaurantId,
            diningDate:      trim($post['dining_date']    ?? ''),
            timeSlot:        trim($post['time_slot']      ?? ''),
            adultsCount:     $adultsCount,
            childrenCount:   $childrenCount,
            specialRequests: trim($post['special_requests'] ?? ''),
            totalFee:        ($adultsCount + $childrenCount) * $feePerPerson,
        );
    }

    private static function validateImagePath(string $path): string
    {
        if ($path === '' || !str_starts_with($path, '/assets/')) {
            return self::DEFAULT_IMAGE;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::VALID_IMAGE_EXTENSIONS, true)) {
            return self::DEFAULT_IMAGE;
        }

        return $path;
    }

    /**
     * Parses the intro_body blob into structured components.
     *
     * Restaurant convention:
     * - First block before any "##" is bodyText
     * - Each "## Heading" becomes a subsection
     * - Final paragraph after the last subsection becomes closingLine
     */
    private static function parseIntroBody(string $rawBody): array
    {
        $result = [
            'bodyText'    => '',
            'subsections' => null,
            'closingLine' => null,
        ];

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
