<?php

declare(strict_types=1);

namespace App\Mappers\Restaurant;

use App\Models\Restaurant;
use App\Services\CmsService;
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
 * Maps plain business data (arrays) into Restaurant ViewModels.
 *
 * WHERE THIS FITS IN THE ARCHITECTURE
 * ------------------------------------
 *   Service    → business logic only, returns plain arrays / domain models.
 *   Mapper     → presentation layer, converts plain data into ViewModels.
 *   Controller → thin orchestrator: calls the service, calls the mapper, renders the view.
 *
 * WHY NOT IN ViewModels/?
 *   ViewModels are simple data containers (DTOs).
 *   This class contains mapping *logic* — it decides how to transform
 *   raw data into those DTOs.  That is a separate responsibility,
 *   so it lives in its own Mappers namespace.
 */
final class RestaurantViewModelMapper
{
    private const PAGE_SLUG     = 'restaurant';
    private const DEFAULT_IMAGE = '/assets/Image/Image (Yummy).png';

    private CmsService $cmsService;

    /**
     * @param CmsService|null $cmsService  Pass null to use the default instance.
     *                                     Pass a real instance in tests or when
     *                                     the controller already has one.
     */
    public function __construct(?CmsService $cmsService = null)
    {
        $this->cmsService = $cmsService ?? new CmsService();
    }

    // =====================================================================
    //  PUBLIC — one method per page
    // =====================================================================

    /**
     * Builds the ViewModel for the restaurant listing page (/restaurant).
     *
     * @param array $data Plain business data from RestaurantService::getRestaurantPageData()
     */
    public function toPageViewModel(array $data): RestaurantPageViewModel
    {
        return new RestaurantPageViewModel(
            heroData:               $this->cmsService->buildHeroData(self::PAGE_SLUG, self::PAGE_SLUG),
            globalUi:               $this->cmsService->buildGlobalUiData(),
            gradientSection:        $this->mapGradient($data['gradientCms']),
            introSplitSection:      $this->mapIntroSplit($data['introCms']),
            introSplit2Section:     $this->mapIntroSplit2($data['intro2Cms']),
            instructionsSection:    $this->mapInstructions($data['instructionsCms']),
            restaurantCardsSection: $this->mapCardsSection($data),
        );
    }

    /**
     * Builds the ViewModel for a single restaurant detail page (/restaurant/{id}).
     *
     * @param array $data Plain business data from RestaurantService::getRestaurantDetailData()
     */
    public function toDetailViewModel(array $data): RestaurantDetailViewModel
    {
        $restaurant = $data['restaurant'];
        $labels     = $data['cmsLabels'];

        return new RestaurantDetailViewModel(
            heroData: $this->mapDetailHero($restaurant, $data['heroSubtitle'], $labels),
            globalUi: $this->cmsService->buildGlobalUiData(),

            // Restaurant identity
            id:          $restaurant->restaurantId,
            name:        $restaurant->name,

            // Section ViewModels — each groups related data for one page section
            contactSection:       $this->mapContactSection($restaurant, $data, $labels),
            practicalInfoSection: $this->mapPracticalInfoSection($restaurant, $data, $labels),
            gallerySection:       $this->mapGallerySection($restaurant, $labels),
            aboutSection:         $this->mapAboutSection($restaurant, $labels),
            chefSection:          $this->mapChefSection($restaurant, $labels),
            menuSection:          $this->mapMenuSection($restaurant, $data, $labels),
            locationSection:      $this->mapLocationSection($restaurant, $data, $labels),
            reservationSection:   $this->mapReservationSection($restaurant, $data, $labels),
        );
    }

    // =====================================================================
    //  PRIVATE — one helper per section, keeps the public methods readable
    // =====================================================================

    private function mapDetailHero(Restaurant $restaurant, string $subtitle, array $labels): HeroData
    {
        return new HeroData(
            mainTitle:           $restaurant->name,
            subtitle:            $subtitle,
            primaryButtonText:   $labels['heroBtnPrimary'],
            primaryButtonLink:   '#reservation',
            secondaryButtonText: $labels['heroBtnSecondary'],
            secondaryButtonLink: '/restaurant',
            backgroundImageUrl:  $restaurant->imagePath ?? self::DEFAULT_IMAGE,
            currentPage:         self::PAGE_SLUG,
        );
    }

    private function mapGradient(array $cms): GradientSectionData
    {
        return new GradientSectionData(
            headingText:        $cms['heading'],
            subheadingText:     $cms['subheading'],
            backgroundImageUrl: $cms['backgroundImage'],
        );
    }

    private function mapIntroSplit(array $cms): IntroSplitSectionData
    {
        return new IntroSplitSectionData(
            headingText:  $cms['heading'],
            bodyText:     $cms['body'],
            imageUrl:     $cms['image'],
            imageAltText: $cms['imageAlt'],
            subsections:  $cms['subsections'],
            closingLine:  $cms['closingLine'],
        );
    }

    private function mapIntroSplit2(?array $cms): ?IntroSplitSectionData
    {
        if ($cms === null) {
            return null;
        }

        return new IntroSplitSectionData(
            headingText:  $cms['heading'],
            bodyText:     $cms['body'],
            imageUrl:     $cms['image'],
            imageAltText: $cms['imageAlt'],
        );
    }

    private function mapInstructions(?array $cms): ?InstructionsSectionData
    {
        if ($cms === null) {
            return null;
        }

        return new InstructionsSectionData(
            title: $cms['title'],
            cards: array_map(
                fn(array $c) => new InstructionCardData($c['number'], $c['title'], $c['text'], $c['icon']),
                $cms['cards']
            ),
        );
    }

    private function mapCardsSection(array $data): RestaurantCardsSectionData
    {
        return new RestaurantCardsSectionData(
            title:   $data['cardsCms']['title'],
            subtitle: $data['cardsCms']['subtitle'],
            filters: $data['cuisineFilters'],
            cards:   array_map(
                fn(array $c) => new RestaurantCardData(
                    id:          $c['id'],
                    name:        $c['name'],
                    cuisine:     $c['cuisine'],
                    address:     $c['address'],
                    description: $c['description'],
                    rating:      $c['rating'],
                    image:       $c['image'],
                ),
                $data['cards']
            ),
        );
    }

    // -----------------------------------------------------------------
    //  Detail page section mappers
    // -----------------------------------------------------------------

    private function mapContactSection(Restaurant $restaurant, array $data, array $labels): ContactSectionData
    {
        return new ContactSectionData(
            address:        $data['address'],
            phone:          $restaurant->phone ?? '',
            email:          $restaurant->email ?? '',
            website:        $restaurant->website ?? '',
            timeSlots:      $data['timeSlots'],
            labelTitle:     $labels['contactTitle'],
            labelAddress:   $labels['labelAddress'],
            labelContact:   $labels['labelContact'],
            labelOpenHours: $labels['labelOpenHours'],
        );
    }

    private function mapPracticalInfoSection(Restaurant $restaurant, array $data, array $labels): PracticalInfoSectionData
    {
        return new PracticalInfoSectionData(
            cuisine:             $restaurant->cuisineType,
            rating:              $restaurant->stars ?? 0,
            michelinStars:       $restaurant->michelinStars ?? 0,
            specialRequestsNote: $restaurant->specialRequestsNote ?? '',
            priceCards:          $data['priceCards'],
            labelTitle:          $labels['practicalTitle'],
            labelPriceFood:      $labels['labelPriceFood'],
            labelRating:         $labels['labelRating'],
            labelSpecialRequests: $labels['labelSpecialReqs'],
            labelFestivalRated:  $labels['festivalRated'],
            labelMichelin:       $labels['labelMichelin'],
            labelCuisineType:    $labels['cuisineLabel'],
        );
    }

    private function mapGallerySection(Restaurant $restaurant, array $labels): GallerySectionData
    {
        return new GallerySectionData(
            image1:     $restaurant->galleryImage1Path ?? self::DEFAULT_IMAGE,
            image2:     $restaurant->galleryImage2Path ?? self::DEFAULT_IMAGE,
            image3:     $restaurant->galleryImage3Path ?? self::DEFAULT_IMAGE,
            labelTitle: $labels['galleryTitle'],
        );
    }

    private function mapAboutSection(Restaurant $restaurant, array $labels): AboutSectionData
    {
        return new AboutSectionData(
            text:             $this->convertNewlines($restaurant->aboutText ?? ''),
            image:            $restaurant->aboutImagePath ?? self::DEFAULT_IMAGE,
            labelTitlePrefix: $labels['aboutTitlePrefix'],
        );
    }

    private function mapChefSection(Restaurant $restaurant, array $labels): ChefSectionData
    {
        return new ChefSectionData(
            name:       $restaurant->chefName ?? '',
            text:       $this->convertNewlines($restaurant->chefText ?? ''),
            image:      $restaurant->chefImagePath ?? self::DEFAULT_IMAGE,
            labelTitle: $labels['chefTitle'],
        );
    }

    private function mapMenuSection(Restaurant $restaurant, array $data, array $labels): MenuSectionData
    {
        return new MenuSectionData(
            description:    $restaurant->menuDescription ?? '',
            cuisineTags:    $data['cuisineTags'],
            image1:         $restaurant->menuImage1Path ?? self::DEFAULT_IMAGE,
            image2:         $restaurant->menuImage2Path ?? self::DEFAULT_IMAGE,
            labelTitle:     $labels['menuTitle'],
            labelCuisineType: $labels['cuisineLabel'],
        );
    }

    private function mapLocationSection(Restaurant $restaurant, array $data, array $labels): LocationSectionData
    {
        return new LocationSectionData(
            description:    $this->convertNewlines($restaurant->locationDescription ?? ''),
            address:        $data['address'],
            mapEmbedUrl:    $restaurant->mapEmbedUrl ?? '',
            labelTitle:     $labels['locationTitle'],
            labelAddress:   $labels['locationAddrLabel'],
            labelMapFallback: $labels['mapFallback'],
        );
    }

    private function mapReservationSection(Restaurant $restaurant, array $data, array $labels): ReservationSectionData
    {
        return new ReservationSectionData(
            image:           $restaurant->reservationImagePath ?? self::DEFAULT_IMAGE,
            timeSlots:       $data['timeSlots'],
            priceCards:      $data['priceCards'],
            durationMinutes: $restaurant->durationMinutes ?? 0,
            seatsPerSession: $restaurant->seatsPerSession ?? 0,
            labelTitle:      $labels['reservationTitle'],
            labelDesc:       $labels['reservationDesc'],
            labelSlots:      $labels['slotsLabel'],
            labelNote:       $labels['reservationNote'],
            labelButton:     $labels['reservationBtn'],
            labelDuration:   $labels['labelDuration'],
            labelSeats:      $labels['labelSeats'],
        );
    }

    /**
     * Converts literal '\n' (stored in DB) into real newlines for display.
     */
    private function convertNewlines(string $text): string
    {
        return str_replace('\n', "\n", $text);
    }
}
