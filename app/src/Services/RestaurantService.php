<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\RestaurantPageConstants;
use App\Constants\SharedSectionKeys;
use App\DTOs\Domain\Pages\RestaurantPageData;
use App\DTOs\Domain\Restaurant\ReservationFormData;
use App\DTOs\Domain\Restaurant\RestaurantDetailPageData;
use App\Models\Restaurant;
use App\Exceptions\RestaurantEventNotFoundException;
use App\Mappers\GlobalContentMapper;
use App\Exceptions\ValidationException;
use App\Models\Reservation;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IGlobalContentRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Repositories\Interfaces\IReservationRepository;
use App\Services\Interfaces\IRestaurantService;

class RestaurantService extends BaseContentService implements IRestaurantService
{
    public function __construct(
        private ICmsContentRepository $cmsContent,
        IGlobalContentRepository $globalContentRepo,
        private IEventRepository $eventRepository,
        private IMediaAssetRepository $mediaAssetRepository,
        private IReservationRepository $reservationRepository,
    ) {
        parent::__construct($globalContentRepo);
    }

    // ── Listing page ────────────────────────────────────────────────────

    public function getRestaurantPageData(string $cuisineFilter = ''): RestaurantPageData
    {
        return $this->guardPageLoad(
            fn(): RestaurantPageData => $this->buildPageData($cuisineFilter),
            'Failed to load the Restaurant page.',
        );
    }

    private function buildPageData(string $cuisineFilter): RestaurantPageData
    {
        $rawContent = $this->cmsContent->getPageContent(RestaurantPageConstants::PAGE_SLUG);
        $allRestaurants = $this->loadAllRestaurants();
        $allCuisines = $this->extractCuisineFilters($allRestaurants);

        if ($cuisineFilter !== '') {
            $filtered = array_values(array_filter(
                $allRestaurants,
                fn(Restaurant $r) => in_array(
                    mb_strtolower($cuisineFilter),
                    array_map('mb_strtolower', $r->cuisineTags),
                    true,
                ),
            ));
        } else {
            $filtered = $allRestaurants;
        }

        return new RestaurantPageData(
            heroContent: GlobalContentMapper::mapHero($rawContent[SharedSectionKeys::SECTION_HERO] ?? []),
            globalUiContent: $this->loadGlobalUi(),
            gradientSection: GlobalContentMapper::mapGradient($rawContent[SharedSectionKeys::SECTION_GRADIENT] ?? []),
            introSplitContent: $rawContent[SharedSectionKeys::SECTION_INTRO_SPLIT] ?? [],
            introSplit2Content: $rawContent[RestaurantPageConstants::SECTION_INTRO_SPLIT2] ?? [],
            instructionsContent: $rawContent[RestaurantPageConstants::SECTION_INSTRUCTIONS] ?? [],
            cardsContent: $rawContent[RestaurantPageConstants::SECTION_CARDS] ?? [],
            restaurants: $filtered,
            allCuisines: $allCuisines,
        );
    }

    /**
     * @param Restaurant[] $restaurants
     * @return string[]
     */
    private function extractCuisineFilters(array $restaurants): array
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

    // ── Detail page ─────────────────────────────────────────────────────

    private function getRestaurant(string $slug): Restaurant
    {
        $normalized = $this->normalizeSlug($slug);
        $row = $this->eventRepository->findActiveRestaurantBySlug($normalized);

        if ($row === null) {
            throw new RestaurantEventNotFoundException($slug);
        }

        return $this->buildRestaurant($row);
    }

    public function getDetailPageData(string $slug): RestaurantDetailPageData
    {
        return new RestaurantDetailPageData(
            restaurant: $this->getRestaurant($slug),
            detailLabels: $this->getDetailLabels(),
            globalUi: $this->loadGlobalUi(),
            validDates: $this->eventRepository->findRestaurantDates(),
        );
    }

    /** @return array<string, ?string> Shared detail-page labels from CMS */
    private function getDetailLabels(): array
    {
        $rawContent = $this->cmsContent->getPageContent(RestaurantPageConstants::PAGE_SLUG);
        return $rawContent[RestaurantPageConstants::SECTION_DETAIL] ?? [];
    }

    // ── Reservation ─────────────────────────────────────────────────────

    public function submitReservation(string $slug, ReservationFormData $formData): int
    {
        $restaurant = $this->getRestaurant($slug);
        $validDates = $this->eventRepository->findRestaurantDates();

        $this->validateReservation($formData, $validDates);
        $this->validateSeatAvailability($restaurant, $formData);

        $reservation = new Reservation(
            eventId: $restaurant->id,
            diningDate: $formData->diningDate,
            timeSlot: $formData->timeSlot,
            adultsCount: $formData->adultsCount,
            childrenCount: $formData->childrenCount,
            specialRequests: $formData->specialRequests,
            totalFee: $formData->totalGuests() * $restaurant->reservationFee,
        );

        return $this->reservationRepository->insert($reservation);
    }

    // ── Shared helpers ──────────────────────────────────────────────────

    /** @return Restaurant[] */
    private function loadAllRestaurants(): array
    {
        $rows = $this->eventRepository->findActiveRestaurantEvents();

        $imageAssetIds = array_filter(
            array_map(fn(array $row) => isset($row['FeaturedImageAssetId']) ? (int) $row['FeaturedImageAssetId'] : null, $rows),
        );
        $imageMap = $imageAssetIds !== []
            ? $this->mediaAssetRepository->findByIds($imageAssetIds)
            : [];

        $allDetailContent = $this->cmsContent->getPageContent(RestaurantPageConstants::DETAIL_PAGE_SLUG);

        $restaurants = [];
        foreach ($rows as $row) {
            $eventId = (int) ($row['EventId'] ?? 0);
            $sectionKey = SharedSectionKeys::eventSectionKey($eventId);
            $cms = $allDetailContent[$sectionKey] ?? [];

            $assetId = isset($row['FeaturedImageAssetId']) ? (int) $row['FeaturedImageAssetId'] : null;
            $imagePath = $assetId !== null && isset($imageMap[$assetId])
                ? $imageMap[$assetId]->filePath
                : null;

            $restaurants[] = Restaurant::fromDbRow($row, $cms, $imagePath);
        }

        return $restaurants;
    }

    /** @param array<string, mixed> $row */
    private function buildRestaurant(array $row): Restaurant
    {
        $allDetailContent = $this->cmsContent->getPageContent(RestaurantPageConstants::DETAIL_PAGE_SLUG);
        $eventId = (int) ($row['EventId'] ?? 0);
        $sectionKey = SharedSectionKeys::eventSectionKey($eventId);
        $cms = $allDetailContent[$sectionKey] ?? [];

        $assetId = isset($row['FeaturedImageAssetId']) ? (int) $row['FeaturedImageAssetId'] : null;
        $imagePath = $assetId !== null
            ? $this->mediaAssetRepository->findById($assetId)?->filePath
            : null;

        return Restaurant::fromDbRow($row, $cms, $imagePath);
    }

    private function normalizeSlug(string $slug): string
    {
        $normalized = trim(strtolower(rawurldecode($slug)));

        if ($normalized === '' || str_contains($normalized, '/')) {
            throw new RestaurantEventNotFoundException($slug);
        }

        return trim($normalized, '-');
    }

    private function validateSeatAvailability(Restaurant $restaurant, ReservationFormData $formData): void
    {
        if ($restaurant->seatsPerSession <= 0) {
            return;
        }

        $bookedGuests = $this->reservationRepository->countBookedGuests(
            $restaurant->id,
            $formData->diningDate,
            $formData->timeSlot,
        );

        $seatsRemaining = $restaurant->seatsPerSession - $bookedGuests;

        if ($formData->totalGuests() > $seatsRemaining) {
            $message = $seatsRemaining <= 0
                ? 'This time slot is fully booked. Please choose a different time or date.'
                : "Only {$seatsRemaining} seats remaining for this time slot.";

            throw new ValidationException([$message]);
        }
    }

    private function validateReservation(ReservationFormData $data, array $validDates): void
    {
        $errors = [];

        if (!in_array($data->diningDate, $validDates, true)) {
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
}