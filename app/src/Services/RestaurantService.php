<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\RestaurantDetailConstants;
use App\Constants\RestaurantPageConstants;
use App\Constants\SharedSectionKeys;
use App\DTOs\Domain\Pages\RestaurantPageData;
use App\DTOs\Domain\Restaurant\ReservationFormData;
use App\DTOs\Domain\Restaurant\Restaurant;
use App\Exceptions\RestaurantEventNotFoundException;
use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\RestaurantDetailSectionContent;
use App\DTOs\Domain\Events\RestaurantDetailEvent;
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
        private readonly IRestaurantContentRepository $restaurantContentRepo,
        private readonly IEventRepository $eventRepository,
        private readonly IMediaAssetRepository $mediaAssetRepository,
        private readonly IReservationRepository $reservationRepository,
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
        $event = $this->eventRepository->findActiveRestaurantBySlug($normalized);

        if ($event === null) {
            throw new RestaurantEventNotFoundException($slug);
        }

        return $this->buildRestaurant($event);
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

    // ── Filters (domain logic, moved from mapper) ───────────────────────

    public function getActiveCuisines(): array
    {
        $restaurants = $this->loadAllRestaurants();

        $unique = [];
        foreach ($restaurants as $restaurant) {
            foreach ($this->parseCuisineTags($restaurant->cuisineType) as $tag) {
                $key = mb_strtolower($tag);
                if ($key !== '' && !isset($unique[$key])) {
                    $unique[$key] = $this->formatCuisineLabel($tag);
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
        $event = $this->eventRepository->findActiveRestaurantBySlug(
            $this->normalizeSlug($slug),
        );

        if ($event === null) {
            throw new RestaurantEventNotFoundException($slug);
        }

        $this->validateReservation($formData);

        $reservation = new Reservation(
            eventId: $event->eventId,
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
     * @return Restaurant[]
     */
    private function loadAllRestaurants(): array
    {
        $restaurants = [];

        foreach ($this->eventRepository->findActiveRestaurantEvents() as $event) {
            $restaurants[] = $this->buildRestaurant($event);
        }

        return $restaurants;
    }

    /**
     * Fetches CMS content and image for an event, then delegates mapping to RestaurantContentMapper.
     */
    private function buildRestaurant(RestaurantDetailEvent $event): Restaurant
    {
        $cms = $this->restaurantContentRepo->findEventCmsData(
            RestaurantDetailConstants::PAGE_SLUG,
            RestaurantDetailConstants::eventSectionKey($event->eventId),
        );

        $imagePath = $event->featuredImageAssetId !== null
            ? $this->mediaAssetRepository->findById($event->featuredImageAssetId)?->filePath
            : null;

        return RestaurantContentMapper::mapRestaurant($event, $cms, $imagePath);
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

    /**
     * @return string[]
     */
    private function parseCuisineTags(?string $cuisineType): array
    {
        if ($cuisineType === null || trim($cuisineType) === '') {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode(',', $cuisineType)),
            static fn(string $tag): bool => $tag !== '',
        ));
    }

    private function formatCuisineLabel(string $tag): string
    {
        $normalized = mb_strtolower(trim($tag));

        return match ($normalized) {
            'fish and seafood' => 'fish and seafood',
            default => mb_convert_case($normalized, MB_CASE_TITLE, 'UTF-8'),
        };
    }

    /**
     * Parses a comma-separated time slots string into an array.
     *
     * @return string[]
     */
    public function parseTimeSlots(?string $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    /**
     * Builds price cards from the adult price. Under-12 is always half.
     *
     * @return array{label: string, price: string}[]
     */
    public function buildPriceCards(?string $priceAdultStr): array
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
}
