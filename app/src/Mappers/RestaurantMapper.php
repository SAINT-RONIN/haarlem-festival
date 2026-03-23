<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\RestaurantPageConstants;
use App\Models\Restaurant;
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

    public static function toPageViewModel(array $data, bool $isLoggedIn): RestaurantPageViewModel
    {
        $restaurants = $data['restaurants'];
        $heroData = CmsMapper::toHeroData($data['heroContent'], 'restaurant');
        $globalUi = CmsMapper::toGlobalUiData($data['globalUiContent'], $isLoggedIn);

        return new RestaurantPageViewModel(
            heroData:               $heroData,
            globalUi:               $globalUi,
            cms:                    CmsMapper::toCmsData($heroData, $globalUi),
            gradientSection:        self::toGradientSection($data['gradientSection']),
            introSplitSection:      self::toIntroSplitSection($data['introSplitSection']),
            introSplit2Section:     self::toIntroSplit2Section($data['introSplit2Section']),
            instructionsSection:    self::toInstructionsSection($data['instructionsSection']),
            restaurantCardsSection: self::toRestaurantCardsSection($data['cardsSection'], $restaurants),
        );
    }

    /**
     * Builds the ViewModel for the reservation form page (/restaurant/{id}/reservation).
     * Re-uses the detail ViewModel but points the hero buttons at the form and back to the detail page.
     */
    public static function toReservationViewModel(array $data, bool $isLoggedIn): RestaurantDetailViewModel
    {
        $vm = self::toDetailViewModel($data, $isLoggedIn);
        // Rebuild HeroData so the primary button scrolls to the form
        // and the secondary button goes back to this restaurant's detail page.
        $restaurant = $data['restaurant'];
        $cms        = $data['cms'];

        $heroData = new HeroData(
            mainTitle:           $restaurant->name,
            subtitle:            self::text($cms, 'detail_hero_subtitle_template'),
            primaryButtonText:   self::text($cms, 'detail_hero_btn_primary'),
            primaryButtonLink:   '#reservation-form',
            secondaryButtonText: self::text($cms, 'detail_hero_btn_secondary'),
            secondaryButtonLink: '/restaurant/' . $restaurant->restaurantId,
            backgroundImageUrl:  $restaurant->imagePath ?? self::DEFAULT_IMAGE,
            currentPage:         'restaurant',
        );

        $globalUi = CmsMapper::toGlobalUiData($data['globalUiContent'], $isLoggedIn);

        return new RestaurantDetailViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            cms:      CmsMapper::toCmsData($heroData, $globalUi),

            id:          $vm->id,
            name:        $vm->name,
            cuisine:     $vm->cuisine,
            address:     $vm->address,
            description: $vm->description,
            rating:      $vm->rating,
            image:       $vm->image,

            phone:   $vm->phone,
            email:   $vm->email,
            website: $vm->website,

            aboutText:  $vm->aboutText,
            aboutImage: $vm->aboutImage,

            chefName:  $vm->chefName,
            chefText:  $vm->chefText,
            chefImage: $vm->chefImage,

            menuDescription: $vm->menuDescription,
            cuisineTags:     $vm->cuisineTags,
            menuImages:      $vm->menuImages,

            locationDescription: $vm->locationDescription,
            mapEmbedUrl:         $vm->mapEmbedUrl,

            michelinStars:       $vm->michelinStars,
            seatsPerSession:     $vm->seatsPerSession,
            durationMinutes:     $vm->durationMinutes,
            specialRequestsNote: $vm->specialRequestsNote,

            galleryImages:    $vm->galleryImages,
            reservationImage: $vm->reservationImage,
            timeSlots:        $vm->timeSlots,
            priceCards:       $vm->priceCards,

            labelContactTitle:    $vm->labelContactTitle,
            labelAddress:         $vm->labelAddress,
            labelContact:         $vm->labelContact,
            labelOpenHours:       $vm->labelOpenHours,
            labelPracticalTitle:  $vm->labelPracticalTitle,
            labelPriceFood:       $vm->labelPriceFood,
            labelRating:          $vm->labelRating,
            labelSpecialRequests: $vm->labelSpecialRequests,
            labelGalleryTitle:    $vm->labelGalleryTitle,
            labelAboutPrefix:     $vm->labelAboutPrefix,
            labelChefTitle:       $vm->labelChefTitle,
            labelMenuTitle:       $vm->labelMenuTitle,
            labelCuisineType:     $vm->labelCuisineType,
            labelLocationTitle:   $vm->labelLocationTitle,
            labelLocationAddress: $vm->labelLocationAddress,
            labelReservationTitle: $vm->labelReservationTitle,
            labelReservationDesc: $vm->labelReservationDesc,
            labelSlotsLabel:      $vm->labelSlotsLabel,
            labelReservationNote: $vm->labelReservationNote,
            labelReservationBtn:  $vm->labelReservationBtn,
            labelDuration:        $vm->labelDuration,
            labelSeats:           $vm->labelSeats,
            labelFestivalRated:   $vm->labelFestivalRated,
            labelMichelin:        $vm->labelMichelin,
            labelMapFallback:     $vm->labelMapFallback,
            priceAdult:           $vm->priceAdult,
            priceChild:           $vm->priceChild,
        );
    }

    public static function toDetailViewModel(array $data, bool $isLoggedIn): RestaurantDetailViewModel
    {
        $restaurant  = $data['restaurant'];
        $imagesByType = $data['imagesByType'];
        $cms         = $data['cms'];

        $heroData    = self::toDetailHeroData($restaurant, $cms);
        $globalUi    = CmsMapper::toGlobalUiData($data['globalUiContent'], $isLoggedIn);
        $cuisineTags = self::parseCuisineTags($restaurant->cuisineType);

        return new RestaurantDetailViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            cms:      CmsMapper::toCmsData($heroData, $globalUi),

            id:          $restaurant->restaurantId,
            name:        $restaurant->name,
            cuisine:     $restaurant->cuisineType,
            address:     self::buildAddress($restaurant),
            description: self::cleanDescription($restaurant->descriptionHtml),
            rating:      $restaurant->stars ?? 0,
            image:       $restaurant->imagePath ?? self::DEFAULT_IMAGE,

            phone:   $restaurant->phone ?? '',
            email:   $restaurant->email ?? '',
            website: $restaurant->website ?? '',

            aboutText:  str_replace('\n', "\n", $restaurant->aboutText ?? ''),
            aboutImage: ($imagesByType[RestaurantPageConstants::IMAGE_TYPE_ABOUT] ?? [])[0] ?? self::DEFAULT_IMAGE,

            chefName:  $restaurant->chefName ?? '',
            chefText:  str_replace('\n', "\n", $restaurant->chefText ?? ''),
            chefImage: ($imagesByType[RestaurantPageConstants::IMAGE_TYPE_CHEF] ?? [])[0] ?? self::DEFAULT_IMAGE,

            menuDescription: $restaurant->menuDescription ?? '',
            cuisineTags:     $cuisineTags,
            menuImages:      $imagesByType[RestaurantPageConstants::IMAGE_TYPE_MENU] ?? [self::DEFAULT_IMAGE, self::DEFAULT_IMAGE],

            locationDescription: str_replace('\n', "\n", $restaurant->locationDescription ?? ''),
            mapEmbedUrl:         $restaurant->mapEmbedUrl ?? '',

            michelinStars:       $restaurant->michelinStars ?? 0,
            seatsPerSession:     $restaurant->seatsPerSession ?? 0,
            durationMinutes:     $restaurant->durationMinutes ?? 0,
            specialRequestsNote: $restaurant->specialRequestsNote ?? '',

            galleryImages: $imagesByType[RestaurantPageConstants::IMAGE_TYPE_GALLERY] ?? [self::DEFAULT_IMAGE],

            reservationImage: ($imagesByType[RestaurantPageConstants::IMAGE_TYPE_RESERVATION] ?? [])[0] ?? self::DEFAULT_IMAGE,
            timeSlots:        $data['timeSlots'],
            priceCards:       $data['priceCards'],

            labelContactTitle:    self::text($cms, 'detail_contact_title'),
            labelAddress:         self::text($cms, 'detail_label_address'),
            labelContact:         self::text($cms, 'detail_label_contact'),
            labelOpenHours:       self::text($cms, 'detail_label_open_hours'),
            labelPracticalTitle:  self::text($cms, 'detail_practical_title'),
            labelPriceFood:       self::text($cms, 'detail_label_price_food'),
            labelRating:          self::text($cms, 'detail_label_rating'),
            labelSpecialRequests: self::text($cms, 'detail_label_special_requests'),
            labelGalleryTitle:    self::text($cms, 'detail_gallery_title'),
            labelAboutPrefix:     self::text($cms, 'detail_about_title_prefix'),
            labelChefTitle:       self::text($cms, 'detail_chef_title'),
            labelMenuTitle:       self::text($cms, 'detail_menu_title'),
            labelCuisineType:     self::text($cms, 'detail_menu_cuisine_label'),
            labelLocationTitle:   self::text($cms, 'detail_location_title'),
            labelLocationAddress: self::text($cms, 'detail_location_address_label'),
            labelReservationTitle: self::text($cms, 'detail_reservation_title'),
            labelReservationDesc: self::text($cms, 'detail_reservation_description'),
            labelSlotsLabel:      self::text($cms, 'detail_reservation_slots_label'),
            labelReservationNote: self::text($cms, 'detail_reservation_note'),
            labelReservationBtn:  self::text($cms, 'detail_reservation_btn'),
            labelDuration:        self::text($cms, 'detail_label_duration'),
            labelSeats:           self::text($cms, 'detail_label_seats'),
            labelFestivalRated:   self::text($cms, 'detail_label_festival_rated'),
            labelMichelin:        self::text($cms, 'detail_label_michelin'),
            labelMapFallback:     self::text($cms, 'detail_map_fallback_text'),
            priceAdult:           $restaurant->priceAdult,
            priceChild:           $restaurant->priceChild,
        );
    }

    private static function toDetailHeroData(Restaurant $restaurant, array $cms): HeroData
    {
        $subtitleTemplate = self::text($cms, 'detail_hero_subtitle_template');
        $heroSubtitle = str_replace(
            ['{name}', '{cuisine}'],
            [$restaurant->name, $restaurant->cuisineType],
            $subtitleTemplate
        );

        return new HeroData(
            mainTitle:           $restaurant->name,
            subtitle:            $heroSubtitle,
            primaryButtonText:   self::text($cms, 'detail_hero_btn_primary'),
            primaryButtonLink:   '#reservation',
            secondaryButtonText: self::text($cms, 'detail_hero_btn_secondary'),
            secondaryButtonLink: '/restaurant',
            backgroundImageUrl:  $restaurant->imagePath ?? self::DEFAULT_IMAGE,
            currentPage:         'restaurant',
        );
    }

    private static function toGradientSection(array $cms): GradientSectionData
    {
        return new GradientSectionData(
            headingText:        self::text($cms, 'gradient_heading'),
            subheadingText:     self::text($cms, 'gradient_subheading'),
            backgroundImageUrl: self::imagePath($cms, 'gradient_background_image'),
        );
    }

    private static function toIntroSplitSection(array $cms): IntroSplitSectionData
    {
        $heading = self::text($cms, 'intro_heading');
        $rawBody = self::text($cms, 'intro_body');
        $parsed  = self::parseIntroBody($rawBody);
        $closing = self::text($cms, 'intro_closing');

        return new IntroSplitSectionData(
            headingText:  $heading,
            bodyText:     $parsed['bodyText'],
            imageUrl:     self::imagePath($cms, 'intro_image'),
            imageAltText: self::text($cms, 'intro_image_alt', $heading),
            subsections:  $parsed['subsections'],
            closingLine:  $closing !== '' ? $closing : $parsed['closingLine'],
        );
    }

    private static function toIntroSplit2Section(array $cms): ?IntroSplitSectionData
    {
        if ($cms === []) {
            return null;
        }

        $heading = self::text($cms, 'intro2_heading');

        return new IntroSplitSectionData(
            headingText:  $heading,
            bodyText:     self::text($cms, 'intro2_body'),
            imageUrl:     self::imagePath($cms, 'intro2_image'),
            imageAltText: self::text($cms, 'intro2_image_alt', $heading),
        );
    }

    private static function toInstructionsSection(array $cms): ?InstructionsSectionData
    {
        if ($cms === []) {
            return null;
        }

        return new InstructionsSectionData(
            title: self::text($cms, 'instructions_title'),
            cards: [
                new InstructionCardData('1', self::text($cms, 'instructions_card_1_title'), self::text($cms, 'instructions_card_1_text'), 'search'),
                new InstructionCardData('2', self::text($cms, 'instructions_card_2_title'), self::text($cms, 'instructions_card_2_text'), 'calendar'),
                new InstructionCardData('3', self::text($cms, 'instructions_card_3_title'), self::text($cms, 'instructions_card_3_text'), 'check'),
            ],
        );
    }

    /**
     * @param Restaurant[] $restaurants
     */
    private static function toRestaurantCardsSection(array $cms, array $restaurants): RestaurantCardsSectionData
    {
        return new RestaurantCardsSectionData(
            title:    self::text($cms, 'cards_title'),
            subtitle: self::text($cms, 'cards_subtitle'),
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
            foreach (explode(',', $restaurant->cuisineType) as $cuisine) {
                $cuisine = trim($cuisine);
                $key     = mb_strtolower($cuisine);

                if ($key !== '' && !isset($unique[$key])) {
                    $unique[$key] = $cuisine;
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

    private static function parseCuisineTags(string $cuisineType): array
    {
        $tags = array_map('trim', explode(',', $cuisineType));
        return array_values(array_filter($tags, fn(string $tag) => $tag !== ''));
    }

    private static function text(array $cms, string $key, string $default = ''): string
    {
        $value  = $cms[$key] ?? null;
        $result = is_string($value) && $value !== '' ? $value : $default;

        return str_replace('\n', "\n", $result);
    }

    private static function imagePath(array $cms, string $key): string
    {
        $path = (string)($cms[$key] ?? '');

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
