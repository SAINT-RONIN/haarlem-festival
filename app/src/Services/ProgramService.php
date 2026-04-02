<?php

declare(strict_types=1);

namespace App\Services;

use App\Content\ProgramMainContent;
use App\DTOs\Filters\EventSessionFilter;
use App\DTOs\Filters\ProgramFilter;
use App\DTOs\Filters\ProgramItemFilter;
use App\DTOs\Program\ProgramData;
use App\DTOs\Program\ProgramItemData;
use App\DTOs\Schedule\SessionWithEvent;
use App\Enums\EventTypeId;
use App\Enums\PriceTierId;
use App\Exceptions\PassPurchaseException;
use App\Exceptions\ProgramException;
use App\Models\EventSessionPrice;
use App\Models\Program;
use App\Models\ProgramItem;
use App\Repositories\Interfaces\ICheckoutContentRepository;
use App\Repositories\Interfaces\IEventSessionPriceRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IPassTypeRepository;
use App\Repositories\Interfaces\IPriceTierRepository;
use App\Repositories\Interfaces\IProgramRepository;
use App\Repositories\Interfaces\IReservationRepository;
use App\Services\Interfaces\IProgramService;

/**
 * Manages the user's personal festival program (shopping cart).
 *
 * A program is tied to either a session key (anonymous visitors) or a user account.
 * This service handles adding/removing event sessions, adjusting quantities and
 * donations, and enriching program items with session details and pricing for display.
 */
class ProgramService implements IProgramService
{
    private const VAT_RATE = 0.21;

    public function __construct(
        private readonly IProgramRepository $programRepository,
        private readonly IEventSessionRepository $sessionRepository,
        private readonly IEventSessionPriceRepository $priceRepository,
        private readonly ICheckoutContentRepository $checkoutContentRepository,
        private readonly IPassTypeRepository $passTypeRepository,
        private readonly IPriceTierRepository $priceTierRepository,
        private readonly IReservationRepository $reservationRepository,
    ) {
    }

    /**
     * Returns the user's active (non-checked-out) program, creating one if none exists.
     */
    public function getOrCreateProgram(string $sessionKey, ?int $userAccountId): Program
    {
        $program = $this->findActiveProgram($sessionKey, $userAccountId);

        if ($program !== null) {
            return $program;
        }

        return $this->programRepository->createProgram($sessionKey, $userAccountId);
    }

    /**
     * Adds an event session to the program, or increases quantity if already present.
     *
     * @throws \InvalidArgumentException When eventSessionId or quantity is invalid
     * @throws ProgramException When the database write fails
     */
    public function addToProgram(string $sessionKey, ?int $userAccountId, int $eventSessionId, int $quantity, int $groupTicketQuantity, float $donationAmount): void
    {
        $this->validateAddInput($eventSessionId, $quantity, $groupTicketQuantity);

        try {
            $program = $this->getOrCreateProgram($sessionKey, $userAccountId);
            $prices = $this->priceRepository->findPricesBySessionIds([$eventSessionId])[$eventSessionId] ?? [];
            $priceTierId = $this->resolveSinglePriceTierId($prices);
            $groupPriceTierId = $this->resolveGroupPriceTierId($prices, $priceTierId);

            if ($quantity > 0) {
                if ($priceTierId === null) {
                    throw new \InvalidArgumentException('No ticket price is configured for this session.');
                }

                $existingItem = $this->findExistingItem($program->programId, $eventSessionId, $priceTierId);

                if ($existingItem !== null) {
                    $this->incrementExistingItem($existingItem, $quantity);
                } else {
                    $this->programRepository->addItem($program->programId, $eventSessionId, $quantity, $priceTierId, $donationAmount);
                }
            }

            if ($groupTicketQuantity > 0) {
                if ($groupPriceTierId === null) {
                    throw new \InvalidArgumentException('No group ticket price is configured for this session.');
                }

                $existingItem = $this->findExistingItem($program->programId, $eventSessionId, $groupPriceTierId);

                if ($existingItem !== null) {
                    $this->incrementExistingItem($existingItem, $groupTicketQuantity);
                } else {
                    $this->programRepository->addItem($program->programId, $eventSessionId, $groupTicketQuantity, $groupPriceTierId, $donationAmount);
                }
            }
        } catch (\InvalidArgumentException $error) {
            throw $error;
        } catch (\Throwable $error) {
            throw new ProgramException('Failed to add item to program.', 0, $error);
        }
    }

    /**
     * Adds a festival pass to the program with the given quantity.
     *
     * @throws \InvalidArgumentException When passTypeId or quantity is invalid
     * @throws PassPurchaseException When the database write fails
     */
    public function addPassToProgram(string $sessionKey, ?int $userAccountId, int $passTypeId, ?string $validDate, int $quantity): ProgramItem
    {
        $this->validatePassInput($passTypeId, $quantity);

        try {
            $program = $this->getOrCreateProgram($sessionKey, $userAccountId);

            return $this->programRepository->addPassItem($program->programId, $passTypeId, $validDate, $quantity, 0.0);
        } catch (\InvalidArgumentException $error) {
            throw $error;
        } catch (\Throwable $error) {
            throw new PassPurchaseException('Failed to add pass to program.', 0, $error);
        }
    }

    /**
     * Adds a restaurant reservation to the program.
     *
     * @throws ProgramException When the database write fails
     */
    public function addReservationToProgram(string $sessionKey, ?int $userAccountId, int $reservationId): ProgramItem
    {
        try {
            $program = $this->getOrCreateProgram($sessionKey, $userAccountId);

            return $this->programRepository->addReservationItem($program->programId, $reservationId, 1);
        } catch (\Throwable $error) {
            throw new ProgramException('Failed to add reservation to program.', 0, $error);
        }
    }

    /** Validates input parameters for adding a pass to the program. */
    private function validatePassInput(int $passTypeId, int $quantity): void
    {
        if ($passTypeId <= 0) {
            throw new \InvalidArgumentException('passTypeId is required');
        }
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('quantity must be at least 1');
        }
    }

    /** Validates input parameters for adding an item to the program. */
    private function validateAddInput(int $eventSessionId, int $quantity, int $groupTicketQuantity): void
    {
        if ($eventSessionId <= 0) {
            throw new \InvalidArgumentException('eventSessionId is required');
        }
        if ($quantity <= 0 && $groupTicketQuantity <= 0) {
            throw new \InvalidArgumentException('quantity must be at least 1');
        }
    }

    /** Increases quantity on an existing program item and returns the updated item. */
    private function incrementExistingItem(ProgramItem $existingItem, int $additionalQuantity): ProgramItem
    {
        $newQuantity = $existingItem->quantity + $additionalQuantity;
        $this->programRepository->updateItemQuantity($existingItem->programItemId, $newQuantity, 0);

        $items = $this->programRepository->findProgramItems(new ProgramItemFilter(programItemId: $existingItem->programItemId));

        return $items[0];
    }

    /**
     * Updates the ticket quantity for a program item. Removes the item if quantity drops to zero.
     *
     * @throws \InvalidArgumentException When the item does not belong to the user's program
     */
    public function updateQuantity(string $sessionKey, ?int $userAccountId, int $programItemId, int $quantity, int $groupTicketQuantity): void
    {
        if ($programItemId <= 0) {
            throw new \InvalidArgumentException('programItemId is required');
        }
        $this->verifyItemOwnership($sessionKey, $userAccountId, $programItemId);

        if ($quantity <= 0) {
            $this->programRepository->removeItem($programItemId);
            return;
        }

        $this->programRepository->updateItemQuantity($programItemId, $quantity, $groupTicketQuantity);
    }

    /**
     * Updates the optional donation amount on a program item. Negative values are clamped to zero.
     *
     * @throws \InvalidArgumentException When the item does not belong to the user's program
     */
    public function updateDonation(string $sessionKey, ?int $userAccountId, int $programItemId, float $donationAmount): void
    {
        if ($programItemId <= 0) {
            throw new \InvalidArgumentException('programItemId is required');
        }
        $this->verifyItemOwnership($sessionKey, $userAccountId, $programItemId);

        if ($donationAmount < 0) {
            $donationAmount = 0.0;
        }

        $this->programRepository->updateItemDonation($programItemId, $donationAmount);
    }

    /**
     * Removes a single item from the user's program after verifying ownership.
     *
     * @throws \InvalidArgumentException When the item does not belong to the user's program
     */
    public function removeItem(string $sessionKey, ?int $userAccountId, int $programItemId): void
    {
        if ($programItemId <= 0) {
            throw new \InvalidArgumentException('programItemId is required');
        }
        $this->verifyItemOwnership($sessionKey, $userAccountId, $programItemId);

        $this->programRepository->removeItem($programItemId);
    }

    /**
     * Removes all items from the user's active program, effectively emptying the cart.
     */
    public function clearProgram(string $sessionKey, ?int $userAccountId): void
    {
        $program = $this->findActiveProgram($sessionKey, $userAccountId);

        if ($program === null) {
            return;
        }

        $this->programRepository->clearProgram($program->programId);
    }

    /**
     * Builds the full program view model with enriched item details, subtotal, VAT, and total.
     * Returns an empty ProgramData when no active program exists.
     */
    public function getProgramData(string $sessionKey, ?int $userAccountId): ProgramData
    {
        $program = $this->findActiveProgram($sessionKey, $userAccountId);

        if ($program === null) {
            return $this->buildEmptyProgramData(null);
        }

        $programItems = $this->programRepository->findProgramItems(new ProgramItemFilter(programId: $program->programId));

        if ($programItems === []) {
            return $this->buildEmptyProgramData($program);
        }

        return $this->buildEnrichedProgramData($program, $programItems);
    }

    /**
     * Returns the CMS content for the "My Program" page.
     */
    public function getProgramMainContent(): ProgramMainContent
    {
        return $this->checkoutContentRepository->findProgramMainContent('my-program', 'main');
    }

    /** Builds a ProgramData with no items and zero totals. */
    private function buildEmptyProgramData(?Program $program): ProgramData
    {
        return new ProgramData(program: $program, items: [], subtotal: 0.0, taxAmount: 0.0, total: 0.0);
    }

    /** Enriches items and calculates financial totals for a non-empty program. */
    private function buildEnrichedProgramData(Program $program, array $programItems): ProgramData
    {
        $enrichedItems = $this->enrichItemsWithSessionData($programItems);
        $subtotal = $this->calculateSubtotal($enrichedItems);
        $taxAmount = $subtotal * self::VAT_RATE;

        return new ProgramData(
            program: $program,
            items: $enrichedItems,
            subtotal: $subtotal,
            taxAmount: $taxAmount,
            total: $subtotal + $taxAmount,
        );
    }

    /**
     * Guards against cross-user item manipulation by confirming the item belongs to the caller's active program.
     *
     * @throws \InvalidArgumentException When the program doesn't exist or the item isn't in it
     */
    private function verifyItemOwnership(string $sessionKey, ?int $userAccountId, int $programItemId): void
    {
        $program = $this->findActiveProgram($sessionKey, $userAccountId);

        if ($program === null) {
            throw new \InvalidArgumentException('Program not found');
        }

        $items = $this->programRepository->findProgramItems(new ProgramItemFilter(
            programItemId: $programItemId,
            programId: $program->programId,
        ));

        if ($items === []) {
            throw new \InvalidArgumentException('Item does not belong to your program');
        }
    }

    /**
     * Looks up the user's active program, preferring user-account match over session-key match.
     * This allows programs to survive across devices once the user logs in.
     */
    private function findActiveProgram(string $sessionKey, ?int $userAccountId): ?Program
    {
        if ($userAccountId !== null) {
            $programs = $this->programRepository->findPrograms(new ProgramFilter(
                userAccountId: $userAccountId,
                isCheckedOut: false,
            ));

            if ($programs !== []) {
                return $programs[0];
            }
        }

        $programs = $this->programRepository->findPrograms(new ProgramFilter(
            sessionKey: $sessionKey,
            isCheckedOut: false,
        ));

        return $programs !== [] ? $programs[0] : null;
    }

    private function findExistingItem(int $programId, int $eventSessionId, int $priceTierId): ?ProgramItem
    {
        $items = $this->programRepository->findProgramItems(new ProgramItemFilter(
            programId: $programId,
            eventSessionId: $eventSessionId,
            priceTierId: $priceTierId,
        ));

        return $items !== [] ? $items[0] : null;
    }

    /**
     * @param ProgramItem[] $programItems
     * @return ProgramItemData[]
     */
    private function enrichItemsWithSessionData(array $programItems): array
    {
        $sessionIds = $this->extractSessionIds($programItems);
        $sessionsById = $this->fetchSessionsById($sessionIds);
        $pricesBySession = $this->fetchPricesBySession($sessionIds);

        $enrichedItems = [];
        foreach ($programItems as $item) {
            if ($item->reservationId !== null) {
                $enriched = $this->buildReservationItemData($item);
            } elseif ($item->passTypeId !== null) {
                $enriched = $this->buildPassItemData($item);
            } else {
                $enriched = $this->buildProgramItemData($item, $sessionsById, $pricesBySession);
            }

            if ($enriched !== null) {
                $enrichedItems[] = $enriched;
            }
        }

        return $enrichedItems;
    }

    /**
     * @param ProgramItem[] $programItems
     * @return int[]
     */
    private function extractSessionIds(array $programItems): array
    {
        $sessionIds = [];
        foreach ($programItems as $item) {
            if ($item->eventSessionId !== null) {
                $sessionIds[] = $item->eventSessionId;
            }
        }

        return array_values(array_unique($sessionIds));
    }

    /**
     * @param int[] $sessionIds
     * @return array<int, SessionWithEvent>
     */
    private function fetchSessionsById(array $sessionIds): array
    {
        if ($sessionIds === []) {
            return [];
        }

        $result = $this->sessionRepository->findSessions(new EventSessionFilter(sessionIds: $sessionIds));
        $sessions = $result->sessions;

        $sessionsById = [];
        foreach ($sessions as $session) {
            $sessionsById[$session->eventSessionId] = $session;
        }

        return $sessionsById;
    }

    /**
     * @param int[] $sessionIds
     * @return array<int, EventSessionPrice[]>
     */
    private function fetchPricesBySession(array $sessionIds): array
    {
        if ($sessionIds === []) {
            return [];
        }

        return $this->priceRepository->findPricesBySessionIds($sessionIds);
    }

    /**
     * @param ProgramItemData[] $items
     */
    private function calculateSubtotal(array $items): float
    {
        $subtotal = 0.0;

        foreach ($items as $item) {
            $subtotal += ($item->basePrice * $item->quantity) + $item->donationAmount;
        }

        return $subtotal;
    }

    /**
     * @param array<int, SessionWithEvent> $sessionsById
     * @param array<int, EventSessionPrice[]> $pricesBySession
     */
    private function buildProgramItemData(ProgramItem $item, array $sessionsById, array $pricesBySession): ?ProgramItemData
    {
        if ($item->eventSessionId === null) {
            return null;
        }

        $session = $sessionsById[$item->eventSessionId] ?? null;
        if ($session === null) {
            return null;
        }

        $prices = $pricesBySession[$item->eventSessionId] ?? [];

        return new ProgramItemData(
            programItemId: $item->programItemId,
            eventSessionId: $item->eventSessionId,
            quantity: $item->quantity,
            donationAmount: (float)($item->donationAmount ?? '0.00'),
            eventTitle: $session->eventTitle,
            venueName: $session->venueName,
            hallName: $session->hallName,
            startDateTime: $session->startDateTime->format('Y-m-d H:i:s'),
            endDateTime: $session->endDateTime?->format('Y-m-d H:i:s'),
            eventTypeId: $session->eventTypeId,
            eventTypeName: $session->eventTypeName,
            eventTypeSlug: $session->eventTypeSlug,
            languageCode: $session->languageCode,
            minAge: $session->minAge,
            maxAge: $session->maxAge,
            isPayWhatYouLike: $this->hasPayWhatYouLikeTier($prices),
            basePrice: $this->resolveBasePrice($prices, $item->priceTierId),
            priceTier: $item->priceTierId !== null ? $this->priceTierRepository->findById($item->priceTierId)?->name : null,
        );
    }

    /** Builds a ProgramItemData for a pass item by looking up the PassType. */
    private function buildPassItemData(ProgramItem $item): ?ProgramItemData
    {
        $passType = $this->passTypeRepository->findById($item->passTypeId);

        if ($passType === null) {
            return null;
        }

        $displayName = self::formatPassDisplayName($passType->passName);

        return new ProgramItemData(
            programItemId: $item->programItemId,
            eventSessionId: null,
            quantity: $item->quantity,
            donationAmount: (float)($item->donationAmount ?? '0.00'),
            eventTitle: $displayName,
            eventTypeId: $passType->eventTypeId,
            eventTypeName: 'Jazz',
            eventTypeSlug: 'jazz',
            basePrice: (float)$passType->price,
            passTypeId: $passType->passTypeId,
            passName: $displayName,
            passScope: $passType->passScope->value,
            passValidDate: $item->passValidDate?->format('Y-m-d'),
        );
    }

    /** Converts a raw DB pass name like "DayPass" into "Day Pass". */
    private static function formatPassDisplayName(string $rawName): string
    {
        return trim((string) preg_replace('/([a-z])([A-Z])/', '$1 $2', $rawName));
    }

    /**
     * @param EventSessionPrice[] $prices
     */
    private function hasPayWhatYouLikeTier(array $prices): bool
    {
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::PayWhatYouLike->value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param EventSessionPrice[] $prices
     */
    private function resolveBasePrice(array $prices, ?int $priceTierId): float
    {
        if ($priceTierId !== null) {
            foreach ($prices as $price) {
                if ($price->priceTierId === $priceTierId) {
                    return (float)$price->price;
                }
            }
        }

        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::Adult->value || $price->priceTierId === PriceTierId::Single->value) {
                return (float)$price->price;
            }
        }

        foreach ($prices as $price) {
            if ($price->priceTierId !== PriceTierId::PayWhatYouLike->value) {
                return (float)$price->price;
            }
        }

        return 0.0;
    }

    /** Builds a ProgramItemData for a reservation item by looking up the Reservation. */
    private function buildReservationItemData(ProgramItem $item): ?ProgramItemData
    {
        $reservation = $this->reservationRepository->findWithRestaurant($item->reservationId);

        if ($reservation === null) {
            return null;
        }

        $totalGuests = $reservation->adultsCount + $reservation->childrenCount;
        $displayTitle = $reservation->restaurantName ?? 'Restaurant Reservation';

        return new ProgramItemData(
            programItemId: $item->programItemId,
            eventSessionId: null,
            quantity: $totalGuests,
            donationAmount: 0.0,
            eventTitle: $displayTitle,
            venueName: $reservation->restaurantAddress,
            startDateTime: $reservation->diningDate,
            eventTypeId: EventTypeId::Restaurant->value,
            eventTypeName: 'Restaurant',
            eventTypeSlug: 'restaurant',
            basePrice: $reservation->totalFee / max($totalGuests, 1),
            reservationId: $reservation->reservationId,
            diningDate: $reservation->diningDate,
            timeSlot: $reservation->timeSlot,
            guestCount: $totalGuests,
        );
    }

    /**
     * @param EventSessionPrice[] $prices
     */
    private function resolveSinglePriceTierId(array $prices): ?int
    {
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::Single->value || $price->priceTierId === PriceTierId::Adult->value) {
                return $price->priceTierId;
            }
        }

        if ($prices === []) {
            return null;
        }

        usort($prices, fn(EventSessionPrice $a, EventSessionPrice $b) => (float)$a->price <=> (float)$b->price);

        return $prices[0]->priceTierId ?? null;
    }

    /**
     * @param EventSessionPrice[] $prices
     */
    private function resolveGroupPriceTierId(array $prices, ?int $singlePriceTierId): ?int
    {
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::Group->value) {
                return $price->priceTierId;
            }
        }

        if ($prices === []) {
            return null;
        }

        usort($prices, fn(EventSessionPrice $a, EventSessionPrice $b) => (float)$a->price <=> (float)$b->price);

        for ($index = count($prices) - 1; $index >= 0; $index--) {
            $priceTierId = $prices[$index]->priceTierId;
            if ($priceTierId !== $singlePriceTierId) {
                return $priceTierId;
            }
        }

        return $singlePriceTierId;
    }

    /**
     * @param array<int, array> $infoByEventSessionId
     */
    public function getTourInfo(int $eventId, string $dateTime): array
    {
        $infoByEventSessionId = [];

        if ($eventId <= 0 || $dateTime === '') {
            return $infoByEventSessionId;
        }

        $parsedDateTime = \DateTimeImmutable::createFromFormat('U', $dateTime);
        if ($parsedDateTime === false) {
            return $infoByEventSessionId;
        }

        $startDate = $parsedDateTime->format('Y-m-d');
        $startTime = $parsedDateTime->format('H:i:s');

        $sessions = $this->sessionRepository->findSessions(
            new EventSessionFilter(eventId: $eventId, startDate: $startDate, endDate: $startDate, startTime: $startTime)
        );

        foreach ($sessions->sessions as $session) {
            $prices = $this->priceRepository->findPricesBySessionIds([$session->eventSessionId])[$session->eventSessionId] ?? [];

            $infoByEventSessionId[$session->eventSessionId] = [
                'dateTime' => $session->startDateTime->format('Y-m-d H:i:s'),
                'language' => $session->languageCode,
                'seatsAvailable' => $session->seatsAvailable,
                'prices' => array_map(
                    static fn(EventSessionPrice $price) => [
                        'priceTierId' => $price->priceTierId,
                        'price' => $price->price,
                    ],
                    $prices,
                ),
            ];
        }

        return $infoByEventSessionId;
    }
}
