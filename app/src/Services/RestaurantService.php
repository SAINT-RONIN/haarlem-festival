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
use App\Helpers\SlugHelper;
use App\Mappers\GlobalContentMapper;
use App\Exceptions\ValidationException;
use App\Models\Reservation;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IGlobalContentRepository;
use App\Repositories\Interfaces\IReservationRepository;
use App\Services\Interfaces\IRestaurantService;

class RestaurantService extends BaseContentService implements IRestaurantService
{
    public function __construct(
        private ICmsContentRepository $cmsContent,
        IGlobalContentRepository $globalContentRepo,
        private IEventRepository $eventRepository,
        private IReservationRepository $reservationRepository,
    ) {
        parent::__construct($globalContentRepo);
    }

    // ── Listing page ────────────────────────────────────────────────────

    public function getRestaurantPageData(): RestaurantPageData
    {
        return $this->guardPageLoad(
            fn(): RestaurantPageData => $this->buildPageData(),
            'Failed to load the Restaurant page.',
        );
    }

    private function buildPageData(): RestaurantPageData
    {
        $rawContent = $this->cmsContent->getPageContent(RestaurantPageConstants::PAGE_SLUG);
        $allRestaurants = $this->loadAllRestaurants();
        $allCuisines = $this->extractCuisineFilters($allRestaurants);

        return new RestaurantPageData(
            heroContent: GlobalContentMapper::mapHero($rawContent[SharedSectionKeys::SECTION_HERO] ?? []),
            globalUiContent: $this->loadGlobalUi(),
            gradientSection: GlobalContentMapper::mapGradient($rawContent[SharedSectionKeys::SECTION_GRADIENT] ?? []),
            introSplitContent: $rawContent[SharedSectionKeys::SECTION_INTRO_SPLIT] ?? [],
            introSplit2Content: $rawContent[RestaurantPageConstants::SECTION_INTRO_SPLIT2] ?? [],
            instructionsContent: $rawContent[RestaurantPageConstants::SECTION_INSTRUCTIONS] ?? [],
            cardsContent: $rawContent[RestaurantPageConstants::SECTION_CARDS] ?? [],
            restaurants: $allRestaurants,
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

        $allDetailContent = $this->cmsContent->getPageContent(RestaurantPageConstants::DETAIL_PAGE_SLUG);

        return $this->assembleRestaurant($row, $allDetailContent);
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
    private function loadAllRestaurants(): array// Indexed array (list) of Restaurant objects, each built from a DB row + CMS content
    {
        $rows = $this->eventRepository->findActiveRestaurantEvents();

        $restaurants = [];
        foreach ($rows as $row) {
            $restaurants[] = $this->assembleRestaurant($row, []);
        }

        return $restaurants;
    }

    /**
     * Slices the CMS content for this event and builds the Restaurant model.
     *
     * @param array<string, mixed>   $row              Raw Event + Venue + MediaAsset JOIN row
     * @param array<string, mixed>   $allDetailContent All restaurant-detail CMS sections, keyed by section
     */
    private function assembleRestaurant(array $row, array $allDetailContent): Restaurant
    {
        $eventId = (int) ($row['EventId'] ?? 0);
        $sectionKey = SharedSectionKeys::eventSectionKey($eventId);
        $cms = $allDetailContent[$sectionKey] ?? [];

        return Restaurant::fromDbRow($row, $cms);
    }

    private function normalizeSlug(string $slug): string
    {
        return SlugHelper::normalize($slug)
            ?? throw new RestaurantEventNotFoundException($slug);
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