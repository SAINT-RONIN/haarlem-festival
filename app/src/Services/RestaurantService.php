<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\RestaurantPageConstants;
use App\Constants\SharedSectionKeys;
use App\DTOs\Domain\Pages\RestaurantPageData;
use App\DTOs\Domain\Restaurant\ReservationFormData;
use App\DTOs\Domain\Events\RestaurantRow;
use App\Models\Restaurant;
use App\Exceptions\RestaurantEventNotFoundException;
use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\RestaurantDetailSectionContent;
use App\Mappers\RestaurantContentMapper;
use App\Exceptions\ValidationException;
use App\Models\Reservation;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IGlobalContentRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Repositories\Interfaces\IReservationRepository;
use App\Repositories\Interfaces\IRestaurantContentRepository;
use App\Services\Interfaces\IRestaurantService;

class RestaurantService extends BaseContentService implements IRestaurantService
{
    public function __construct(
        IGlobalContentRepository $globalContentRepo,
        private IRestaurantContentRepository $restaurantContentRepo,
        private IEventRepository $eventRepository,
        private IMediaAssetRepository $mediaAssetRepository,
        private IReservationRepository $reservationRepository,
    ) {
        parent::__construct($globalContentRepo);
    }

    // ── Listing page ────────────────────────────────────────────────────

    public function getRestaurantPageData(): RestaurantPageData
    {
        return $this->guardPageLoad(
            fn(): RestaurantPageData => $this->assembleRestaurantPageData(),
            'Failed to load the Restaurant page.',
        );
    }

    private function assembleRestaurantPageData(): RestaurantPageData
    {
        return new RestaurantPageData(
            heroContent: $this->globalContentRepo->findHeroContent(RestaurantPageConstants::PAGE_SLUG),
            globalUiContent: $this->loadGlobalUi(),
            gradientSection: $this->globalContentRepo->findGradientContent(
                RestaurantPageConstants::PAGE_SLUG,
                SharedSectionKeys::SECTION_GRADIENT,
            ),
            introSplitSection: $this->restaurantContentRepo->findIntroContent(
                RestaurantPageConstants::PAGE_SLUG,
                SharedSectionKeys::SECTION_INTRO_SPLIT,
            ),
            introSplit2Section: $this->restaurantContentRepo->findIntroSplit2Content(
                RestaurantPageConstants::PAGE_SLUG,
                RestaurantPageConstants::SECTION_INTRO_SPLIT2,
            ),
            instructionsSection: $this->restaurantContentRepo->findInstructionsContent(
                RestaurantPageConstants::PAGE_SLUG,
                RestaurantPageConstants::SECTION_INSTRUCTIONS,
            ),
            cardsSection: $this->restaurantContentRepo->findCardsContent(
                RestaurantPageConstants::PAGE_SLUG,
                RestaurantPageConstants::SECTION_CARDS,
            ),
            restaurants: $this->loadAllRestaurants(),
        );
    }

    // ── Detail page ─────────────────────────────────────────────────────

    public function getRestaurant(string $slug): Restaurant
    {
        $normalized = $this->normalizeSlug($slug);
        $row = $this->eventRepository->findActiveRestaurantBySlug($normalized);

        if ($row === null) {
            throw new RestaurantEventNotFoundException($slug);
        }

        return $this->buildRestaurant($row);
    }

    public function getDetailLabels(): RestaurantDetailSectionContent
    {
        return $this->restaurantContentRepo->findDetailContent(
            RestaurantPageConstants::PAGE_SLUG,
            RestaurantPageConstants::SECTION_DETAIL,
        );
    }

    public function getGlobalUi(): GlobalUiContent
    {
        return $this->loadGlobalUi();
    }

    // ── Filters ──────────────────────────────────────────────────────────

    /** @param Restaurant[] $restaurants */
    public function getActiveCuisines(array $restaurants): array
    {
        $unique = [];
        foreach ($restaurants as $restaurant) {
            foreach ($restaurant->cuisineTags as $tag) {
                $key = mb_strtolower($tag);
                if (!isset($unique[$key])) {
                    $unique[$key] = self::formatCuisineLabel($tag);
                }
            }
        }

        $labels = array_values($unique);
        sort($labels, SORT_NATURAL | SORT_FLAG_CASE);

        return ['All', ...$labels];
    }

    // ── Reservation ─────────────────────────────────────────────────────

    public function submitReservation(string $slug, ReservationFormData $formData): int
    {
        $row = $this->eventRepository->findActiveRestaurantBySlug(
            $this->normalizeSlug($slug),
        );

        if ($row === null) {
            throw new RestaurantEventNotFoundException($slug);
        }

        $this->validateReservation($formData);

        $reservation = new Reservation(
            eventId: $row->eventId,
            diningDate: $formData->diningDate,
            timeSlot: $formData->timeSlot,
            adultsCount: $formData->adultsCount,
            childrenCount: $formData->childrenCount,
            specialRequests: $formData->specialRequests,
            totalFee: $formData->totalGuests() * RestaurantPageConstants::RESERVATION_FEE,
        );

        return $this->reservationRepository->insert($reservation);
    }

    // ── Shared helpers ──────────────────────────────────────────────────

    /**
     * Loads all active restaurants with their CMS data and resolved images.
     *
     * Uses batch image loading to avoid N+1 queries on the MediaAsset table.
     * CMS content is already cached per-page by CmsContentRepository, so
     * repeated findEventCmsData() calls don't cause extra queries.
     *
     * @return Restaurant[]
     */
    private function loadAllRestaurants(): array
    {
        $rows = $this->eventRepository->findActiveRestaurantEvents();

        // Batch-load all featured images in one query instead of one per restaurant.
        $imageAssetIds = array_filter(
            array_map(fn(RestaurantRow $row) => $row->featuredImageAssetId, $rows),
        );
        $imageMap = $imageAssetIds !== []
            ? $this->mediaAssetRepository->findByIds($imageAssetIds)
            : [];

        $restaurants = [];
        foreach ($rows as $row) {
            $cms = $this->restaurantContentRepo->findEventCmsData(
                RestaurantPageConstants::DETAIL_PAGE_SLUG,
                RestaurantPageConstants::eventSectionKey($row->eventId),
            );

            $imagePath = isset($imageMap[$row->featuredImageAssetId])
                ? $imageMap[$row->featuredImageAssetId]->filePath
                : null;

            $restaurants[] = RestaurantContentMapper::mapRestaurant($row, $cms, $imagePath);
        }

        return $restaurants;
    }

    /**
     * Fetches CMS content and image for a single RestaurantRow.
     * Used for detail/reservation pages where we only load one restaurant.
     */
    private function buildRestaurant(RestaurantRow $row): Restaurant
    {
        $cms = $this->restaurantContentRepo->findEventCmsData(
            RestaurantPageConstants::DETAIL_PAGE_SLUG,
            RestaurantPageConstants::eventSectionKey($row->eventId),
        );

        $imagePath = $row->featuredImageAssetId !== null
            ? $this->mediaAssetRepository->findById($row->featuredImageAssetId)?->filePath
            : null;

        return RestaurantContentMapper::mapRestaurant($row, $cms, $imagePath);
    }

    private function normalizeSlug(string $slug): string
    {
        $normalized = trim(strtolower(rawurldecode($slug)));

        if ($normalized === '' || str_contains($normalized, '/')) {
            throw new RestaurantEventNotFoundException($slug);
        }

        return trim($normalized, '-');
    }

    private function validateReservation(ReservationFormData $data): void
    {
        $errors = [];

        if (!in_array($data->diningDate, RestaurantPageConstants::VALID_DATES, true)) {
            $errors[] = 'Please select a valid dining date.';
        }
        if ($data->timeSlot === '') {
            $errors[] = 'Please select a time slot.';
        }
        if ($data->totalGuests() < 1) {
            $errors[] = 'Please add at least one guest.';
        }
        if ($data->totalGuests() > RestaurantPageConstants::MAX_GUEST_COUNT) {
            $errors[] = 'Maximum ' . RestaurantPageConstants::MAX_GUEST_COUNT . ' guests per reservation.';
        }
        if (strlen($data->specialRequests) > RestaurantPageConstants::MAX_SPECIAL_REQUESTS_LENGTH) {
            $errors[] = 'Special requests may not exceed ' . RestaurantPageConstants::MAX_SPECIAL_REQUESTS_LENGTH . ' characters.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }

    private static function formatCuisineLabel(string $tag): string
    {
        $normalized = mb_strtolower(trim($tag));

        return match ($normalized) {
            'fish and seafood' => 'fish and seafood',
            default => mb_convert_case($normalized, MB_CASE_TITLE, 'UTF-8'),
        };
    }
}
