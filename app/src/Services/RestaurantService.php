<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\RestaurantPageConstants;
use App\Constants\SharedSectionKeys;
use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Domain\Pages\RestaurantPageData;
use App\DTOs\Domain\Restaurant\ReservationFormData;
use App\DTOs\Domain\Events\RestaurantRow;
use App\Models\Restaurant;
use App\Exceptions\RestaurantEventNotFoundException;
use App\Mappers\GlobalContentMapper;
use App\Mappers\RestaurantContentMapper;
use App\Mappers\RestaurantContentParser;
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
        return new RestaurantPageData(
            heroContent: GlobalContentMapper::mapHero($rawContent[SharedSectionKeys::SECTION_HERO] ?? []),
            globalUiContent: $this->loadGlobalUi(),
            gradientSection: GlobalContentMapper::mapGradient($rawContent[SharedSectionKeys::SECTION_GRADIENT] ?? []),
            introSplitSection: RestaurantContentMapper::mapIntro($rawContent[SharedSectionKeys::SECTION_INTRO_SPLIT] ?? []),
            introSplit2Section: RestaurantContentMapper::mapIntroSplit2($rawContent[RestaurantPageConstants::SECTION_INTRO_SPLIT2] ?? []),
            instructionsSection: RestaurantContentMapper::mapInstructions($rawContent[RestaurantPageConstants::SECTION_INSTRUCTIONS] ?? []),
            cardsSection: RestaurantContentMapper::mapCards($rawContent[RestaurantPageConstants::SECTION_CARDS] ?? []),
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

    /** @return array<string, ?string> Shared detail-page labels from CMS */
    public function getDetailLabels(): array
    {
        $rawContent = $this->cmsContent->getPageContent(RestaurantPageConstants::DETAIL_PAGE_SLUG);
        return $rawContent[RestaurantPageConstants::SECTION_DETAIL] ?? [];
    }

    public function getGlobalUi(): GlobalUiContent
    {
        return $this->loadGlobalUi();
    }

    // ── Reservation ─────────────────────────────────────────────────────

    public function submitReservation(string $slug, ReservationFormData $formData): int
    {
        $normalized = $this->normalizeSlug($slug);
        $row = $this->eventRepository->findActiveRestaurantBySlug($normalized);

        if ($row === null) {
            throw new RestaurantEventNotFoundException($slug);
        }

        $labels = $this->getDetailLabels();
        $reservationFee = RestaurantContentParser::parseReservationFee($labels['detail_reservation_fee'] ?? null);
        $validDates = RestaurantContentParser::parseValidDates($labels['detail_valid_dates'] ?? null);

        $this->validateReservation($formData, $validDates);

        $reservation = new Reservation(
            eventId: $row->eventId,
            diningDate: $formData->diningDate,
            timeSlot: $formData->timeSlot,
            adultsCount: $formData->adultsCount,
            childrenCount: $formData->childrenCount,
            specialRequests: $formData->specialRequests,
            totalFee: $formData->totalGuests() * $reservationFee,
        );

        return $this->reservationRepository->insert($reservation);
    }

    // ── Shared helpers ──────────────────────────────────────────────────

    /** @return Restaurant[] */
    private function loadAllRestaurants(): array
    {
        $rows = $this->eventRepository->findActiveRestaurantEvents();

        $imageAssetIds = array_filter(
            array_map(fn(RestaurantRow $row) => $row->featuredImageAssetId, $rows),
        );
        $imageMap = $imageAssetIds !== []
            ? $this->mediaAssetRepository->findByIds($imageAssetIds)
            : [];

        $allDetailContent = $this->cmsContent->getPageContent(RestaurantPageConstants::DETAIL_PAGE_SLUG);

        $restaurants = [];
        foreach ($rows as $row) {
            $sectionKey = SharedSectionKeys::eventSectionKey($row->eventId);
            $cms = $allDetailContent[$sectionKey] ?? [];

            $imagePath = isset($imageMap[$row->featuredImageAssetId])
                ? $imageMap[$row->featuredImageAssetId]->filePath
                : null;

            $restaurants[] = RestaurantContentMapper::mapRestaurant($row, $cms, $imagePath);
        }

        return $restaurants;
    }

    private function buildRestaurant(RestaurantRow $row): Restaurant
    {
        $allDetailContent = $this->cmsContent->getPageContent(RestaurantPageConstants::DETAIL_PAGE_SLUG);
        $sectionKey = SharedSectionKeys::eventSectionKey($row->eventId);
        $cms = $allDetailContent[$sectionKey] ?? [];

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
