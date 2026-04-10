<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\CheckoutConstraints;
use App\DTOs\Cms\ProgramMainContent;
use App\DTOs\Domain\Events\SessionCapacityInfo;
use App\DTOs\Domain\Filters\EventSessionFilter;
use App\DTOs\Domain\Filters\ProgramFilter;
use App\DTOs\Domain\Filters\ProgramItemFilter;
use App\DTOs\Domain\Program\ProgramData;
use App\DTOs\Domain\Program\ProgramItemData;
use App\DTOs\Domain\Schedule\SessionWithEvent;
use App\Enums\EventTypeId;
use App\Enums\PriceTierId;
use App\Exceptions\PassPurchaseException;
use App\Exceptions\ProgramException;
use App\Helpers\HistorySessionHelper;
use App\Models\EventSessionPrice;
use App\Models\Program;
use App\Models\ProgramItem;
use App\Repositories\Interfaces\ICheckoutContentRepository;
use App\Repositories\Interfaces\IEventSessionLabelRepository;
use App\Repositories\Interfaces\IEventSessionPriceRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IPassTypeRepository;
use App\Repositories\Interfaces\IPriceTierRepository;
use App\Repositories\Interfaces\IProgramRepository;
use App\Repositories\Interfaces\IReservationRepository;
use App\Services\Interfaces\IProgramService;

class ProgramService implements IProgramService
{
    private const VAT_RATE = 0.21;
    private const HISTORY_QUERY_TIMEZONE = 'UTC';

    public function __construct(
        private readonly IProgramRepository $programRepository,
        private readonly IEventSessionRepository $sessionRepository,
        private readonly IEventSessionLabelRepository $labelRepository,
        private readonly IEventSessionPriceRepository $priceRepository,
        private readonly ICheckoutContentRepository $checkoutContentRepository,
        private readonly IPassTypeRepository $passTypeRepository,
        private readonly IPriceTierRepository $priceTierRepository,
        private readonly IReservationRepository $reservationRepository,
    ) {}

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

    public function addToProgram(string $sessionKey, ?int $userAccountId, int $eventSessionId, int $quantity, int $groupTicketQuantity, float $donationAmount): void
    {
        $this->validateAddInput($eventSessionId, $quantity, $groupTicketQuantity);
        $this->validateDonationAmount($donationAmount);

        try {
            $program            = $this->getOrCreateProgram($sessionKey, $userAccountId);
            $capacity           = $this->sessionRepository->getCapacityInfo($eventSessionId);
            $this->validateSessionExists($eventSessionId, $capacity);

            $cartTotals         = $this->countTicketsInCartForSession($program->programId, $eventSessionId);
            $requestedSeats     = $quantity + ($groupTicketQuantity * CheckoutConstraints::GROUP_TICKET_SEAT_COUNT);
            $this->validateGroupTicketLimit($capacity, $cartTotals['seats'], $cartTotals['groups'], $groupTicketQuantity);
            $this->validateCapacityForBooking($capacity, $cartTotals['seats'], $requestedSeats);
            $this->validateSingleTicketLimit($capacity, $cartTotals['singles'], $quantity);

            $prices             = $this->priceRepository->findPricesBySessionIds([$eventSessionId])[$eventSessionId] ?? [];
            $this->validatePricesNonNegative($prices);
            $priceTierId        = $this->resolveSinglePriceTierId($prices);
            $groupPriceTierId   = $this->resolveGroupPriceTierId($prices, $priceTierId);

            if ($quantity > 0) {
                $this->upsertProgramTicket($program->programId, $eventSessionId, $quantity, $priceTierId, $donationAmount, 'single');
            }

            if ($groupTicketQuantity > 0) {
                $this->upsertProgramTicket($program->programId, $eventSessionId, $groupTicketQuantity, $groupPriceTierId, $donationAmount, 'group');
            }
        } catch (\InvalidArgumentException $error) {
            throw $error;
        } catch (ProgramException $error) {
            throw $error;
        } catch (\Throwable $error) {
            throw new ProgramException('Failed to add item to program.', 0, $error);
        }
    }

    private function upsertProgramTicket(int $programId, int $eventSessionId, int $quantity, ?int $priceTierId, float $donationAmount, string $ticketKind): void
    {
        if ($priceTierId === null) {
            throw new \InvalidArgumentException("No {$ticketKind} ticket price is configured for this session.");
        }

        $existingItem = $this->findExistingItem($programId, $eventSessionId, $priceTierId);

        if ($existingItem !== null) {
            $this->incrementExistingItem($existingItem, $quantity);
        } else {
            $this->programRepository->addItem($programId, $eventSessionId, $quantity, $priceTierId, $donationAmount);
        }
    }

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

    public function addReservationToProgram(string $sessionKey, ?int $userAccountId, int $reservationId): ProgramItem
    {
        try {
            $program = $this->getOrCreateProgram($sessionKey, $userAccountId);

            return $this->programRepository->addReservationItem($program->programId, $reservationId, 1);
        } catch (\Throwable $error) {
            throw new ProgramException('Failed to add reservation to program.', 0, $error);
        }
    }

    private function validatePassInput(int $passTypeId, int $quantity): void
    {
        if ($passTypeId <= 0) {
            throw new \InvalidArgumentException('passTypeId is required');
        }
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('quantity must be at least 1');
        }
    }

    private function validateAddInput(int $eventSessionId, int $quantity, int $groupTicketQuantity): void
    {
        if ($eventSessionId <= 0) {
            throw new \InvalidArgumentException('eventSessionId is required');
        }
        if ($quantity <= 0 && $groupTicketQuantity <= 0) {
            throw new \InvalidArgumentException('quantity must be at least 1');
        }
    }

    private function validateDonationAmount(float $donationAmount): void
    {
        if ($donationAmount < 0) {
            throw new \InvalidArgumentException('Donation amount cannot be negative');
        }
    }

    private function validateSessionExists(int $eventSessionId, ?SessionCapacityInfo $capacity): void
    {
        if ($capacity === null) {
            throw new ProgramException("Session with ID {$eventSessionId} does not exist.");
        }
    }

    // Group tickets (Family, Group) count as GROUP_TICKET_SEAT_COUNT seats each.
    /** @return array{seats: int, singles: int, groups: int} */
    private function countTicketsInCartForSession(int $programId, int $eventSessionId): array
    {
        $existingItems = $this->programRepository->findProgramItems(new ProgramItemFilter(
            programId: $programId,
            eventSessionId: $eventSessionId,
        ));

        $seats = 0;
        $singles = 0;
        $groups = 0;
        $groupTierIds = [PriceTierId::Family->value, PriceTierId::Group->value];

        foreach ($existingItems as $item) {
            $isGroup = $item->priceTierId !== null && in_array($item->priceTierId, $groupTierIds, true);
            $itemSeats = $isGroup ? $item->quantity * CheckoutConstraints::GROUP_TICKET_SEAT_COUNT : $item->quantity;
            $seats += $itemSeats;

            if ($isGroup) {
                $groups += $item->quantity;
            } else {
                $singles += $item->quantity;
            }
        }

        return ['seats' => $seats, 'singles' => $singles, 'groups' => $groups];
    }

    private function validateCapacityForBooking(SessionCapacityInfo $capacity, int $seatsInCart, int $requestedSeats): void
    {
        $available = $capacity->availableSeats();
        $combined  = $seatsInCart + $requestedSeats;

        if ($available <= 0) {
            throw new ProgramException('This session is sold out.');
        }

        if ($combined > $available) {
            $canStillAdd = max(0, $available - $seatsInCart);
            throw new ProgramException(
                $canStillAdd > 0
                    ? "You already have {$seatsInCart} seat(s) in your program. You can add {$canStillAdd} more."
                    : "You already have {$seatsInCart} seat(s) in your program. No more seats available."
            );
        }
    }

    // soldSingleTickets + singlesAlreadyInCart + requestedSingles must not exceed capacitySingleTicketLimit.
    private function validateSingleTicketLimit(SessionCapacityInfo $capacity, int $singlesAlreadyInCart, int $requestedSingles): void
    {
        if ($capacity->capacitySingleTicketLimit <= 0) {
            return;
        }

        $totalAfterAdd = $capacity->soldSingleTickets + $singlesAlreadyInCart + $requestedSingles;

        if ($totalAfterAdd > $capacity->capacitySingleTicketLimit) {
            $remaining = max(0, $capacity->capacitySingleTicketLimit - $capacity->soldSingleTickets - $singlesAlreadyInCart);
            throw new ProgramException(
                'Single-ticket limit reached for this session.'
                . ($remaining > 0 ? " You can add {$remaining} more single ticket(s)." : ' No single tickets remaining.')
            );
        }
    }

    // Max groups = floor(remainingSeats / GROUP_TICKET_SEAT_COUNT), accounting for singles already in cart.
    private function validateGroupTicketLimit(SessionCapacityInfo $capacity, int $seatsInCart, int $groupsInCart, int $requestedGroups): void
    {
        if ($requestedGroups <= 0) {
            return;
        }

        $available       = $capacity->availableSeats();
        $remainingSeats  = max(0, $available - $seatsInCart);
        $maxMoreGroups   = (int) floor($remainingSeats / CheckoutConstraints::GROUP_TICKET_SEAT_COUNT);
        $maxTotalGroups  = $groupsInCart + $maxMoreGroups;

        if ($requestedGroups > $maxMoreGroups) {
            throw new ProgramException(
                $maxMoreGroups > 0
                    ? "You can add at most {$maxMoreGroups} more group ticket(s) to this session (max {$maxTotalGroups} total). Each group ticket covers " . CheckoutConstraints::GROUP_TICKET_SEAT_COUNT . ' participants.'
                    : 'No more group tickets can be added. The remaining capacity does not fit a group of ' . CheckoutConstraints::GROUP_TICKET_SEAT_COUNT . '.'
            );
        }
    }

    /** @param EventSessionPrice[] $prices */
    private function validatePricesNonNegative(array $prices): void
    {
        foreach ($prices as $price) {
            if ((float) $price->price < 0) {
                throw new ProgramException('Invalid ticket price detected. Please contact support.');
            }
        }
    }

    private function incrementExistingItem(ProgramItem $existingItem, int $additionalQuantity): ProgramItem
    {
        $newQuantity = $existingItem->quantity + $additionalQuantity;
        $this->programRepository->updateItemQuantity($existingItem->programItemId, $newQuantity);

        $items = $this->programRepository->findProgramItems(new ProgramItemFilter(programItemId: $existingItem->programItemId));

        return $items[0];
    }

    public function updateQuantity(string $sessionKey, ?int $userAccountId, int $programItemId, int $quantity): void
    {
        if ($programItemId <= 0) {
            throw new \InvalidArgumentException('programItemId is required');
        }
        if ($quantity < 0) {
            throw new \InvalidArgumentException('quantity cannot be negative');
        }
        $this->verifyItemOwnership($sessionKey, $userAccountId, $programItemId);

        if ($quantity <= 0) {
            $this->programRepository->removeItem($programItemId);
            return;
        }

        $this->validateUpdateCapacity($sessionKey, $userAccountId, $programItemId, $quantity);

        $this->programRepository->updateItemQuantity($programItemId, $quantity);
    }

    // Treats the update as "remove old quantity, add new quantity" to reuse canonical validation.
    private function validateUpdateCapacity(string $sessionKey, ?int $userAccountId, int $programItemId, int $newQuantity): void
    {
        $program = $this->findActiveProgram($sessionKey, $userAccountId);
        if ($program === null) {
            return;
        }

        $items = $this->programRepository->findProgramItems(new ProgramItemFilter(programItemId: $programItemId));
        if ($items === [] || $items[0]->eventSessionId === null) {
            return; // Pass or reservation item — no session capacity to enforce.
        }

        $item     = $items[0];
        $capacity = $this->sessionRepository->getCapacityInfo($item->eventSessionId);
        if ($capacity === null) {
            return;
        }

        $groupTierIds    = [PriceTierId::Family->value, PriceTierId::Group->value];
        $isGroup         = $item->priceTierId !== null && in_array($item->priceTierId, $groupTierIds, true);
        $cartTotals      = $this->countTicketsInCartForSession($program->programId, $item->eventSessionId);

        // Subtract this item's current contribution so we can validate the new quantity in isolation.
        $currentItemSeats    = $isGroup ? $item->quantity * CheckoutConstraints::GROUP_TICKET_SEAT_COUNT : $item->quantity;
        $seatsExcludingItem  = $cartTotals['seats']   - $currentItemSeats;
        $groupsExcludingItem = $isGroup ? $cartTotals['groups']  - $item->quantity : $cartTotals['groups'];
        $singlesExcludingItem = $isGroup ? $cartTotals['singles'] : $cartTotals['singles'] - $item->quantity;

        $newItemSeats = $isGroup ? $newQuantity * CheckoutConstraints::GROUP_TICKET_SEAT_COUNT : $newQuantity;

        if ($isGroup) {
            $this->validateGroupTicketLimit($capacity, $seatsExcludingItem, $groupsExcludingItem, $newQuantity);
        } else {
            $this->validateSingleTicketLimit($capacity, $singlesExcludingItem, $newQuantity);
        }

        $this->validateCapacityForBooking($capacity, $seatsExcludingItem, $newItemSeats);
    }

    public function updateDonation(string $sessionKey, ?int $userAccountId, int $programItemId, float $donationAmount): void
    {
        if ($programItemId <= 0) {
            throw new \InvalidArgumentException('programItemId is required');
        }
        $this->verifyItemOwnership($sessionKey, $userAccountId, $programItemId);

        // Clamp negative donations to zero — a negative donation amount has no meaning.
        if ($donationAmount < 0) {
            $donationAmount = 0.0;
        }

        $this->programRepository->updateItemDonation($programItemId, $donationAmount);
    }

    public function removeItem(string $sessionKey, ?int $userAccountId, int $programItemId): void
    {
        if ($programItemId <= 0) {
            throw new \InvalidArgumentException('programItemId is required');
        }
        $this->verifyItemOwnership($sessionKey, $userAccountId, $programItemId);

        $this->programRepository->removeItem($programItemId);
    }

    public function clearProgram(string $sessionKey, ?int $userAccountId): void
    {
        $program = $this->findActiveProgram($sessionKey, $userAccountId);

        if ($program === null) {
            return;
        }

        $this->programRepository->clearProgram($program->programId);
    }

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

    public function getProgramMainContent(): ProgramMainContent
    {
        return $this->checkoutContentRepository->findProgramMainContent('my-program', 'main');
    }

    private function buildEmptyProgramData(?Program $program): ProgramData
    {
        return new ProgramData(program: $program, items: [], subtotal: 0.0, taxAmount: 0.0, total: 0.0, canCheckout: false);
    }

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
            canCheckout: $enrichedItems !== [],
        );
    }

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

    // Prefers user-account match over session-key so programs survive across devices after login.
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

    // Same session can appear twice (single + group tier), so both dimensions are needed.
    private function findExistingItem(int $programId, int $eventSessionId, int $priceTierId): ?ProgramItem
    {
        $items = $this->programRepository->findProgramItems(new ProgramItemFilter(
            programId: $programId,
            eventSessionId: $eventSessionId,
            priceTierId: $priceTierId,
        ));

        return $items !== [] ? $items[0] : null;
    }

    /** @param ProgramItem[] $programItems @return ProgramItemData[] */
    private function enrichItemsWithSessionData(array $programItems): array
    {
        $sessionIds = $this->extractSessionIds($programItems);
        $sessionsById = $this->fetchSessionsById($sessionIds);
        $pricesBySession = $this->fetchPricesBySession($sessionIds);

        $enrichedItems = [];
        foreach ($programItems as $item) {
            // Items are checked in this order: reservation first, then pass, then session.
            // An item can only be one type at a time.
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

    /** @param ProgramItem[] $programItems @return int[] */
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

    /** @param int[] $sessionIds @return array<int, SessionWithEvent> */
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

    /** @param int[] $sessionIds @return array<int, EventSessionPrice[]> */
    private function fetchPricesBySession(array $sessionIds): array
    {
        if ($sessionIds === []) {
            return [];
        }

        return $this->priceRepository->findPricesBySessionIds($sessionIds);
    }

    // base price x quantity + optional donation (per-item, not per-ticket).
    /** @param ProgramItemData[] $items */
    private function calculateSubtotal(array $items): float
    {
        $subtotal = 0.0;

        foreach ($items as $item) {
            // base price × quantity + optional donation. The donation is per-item, not per-ticket.
            $subtotal += ($item->basePrice * $item->quantity) + $item->donationAmount;
        }

        return $subtotal;
    }

    // Returns null when session was deleted after being added — caller skips nulls.
    /** @param array<int, SessionWithEvent> $sessionsById @param array<int, EventSessionPrice[]> $pricesBySession */
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
            priceTierId: $item->priceTierId,
            donationAmount: (float) ($item->donationAmount ?? '0.00'),
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
            donationAmount: (float) ($item->donationAmount ?? '0.00'),
            eventTitle: $displayName,
            eventTypeId: $passType->eventTypeId,
            eventTypeName: 'Jazz',
            eventTypeSlug: 'jazz',
            basePrice: (float) $passType->price,
            passTypeId: $passType->passTypeId,
            passName: $displayName,
            passScope: $passType->passScope->value,
            passValidDate: $item->passValidDate?->format('Y-m-d'),
        );
    }

    // "DayPass" -> "Day Pass", "EveningPass" -> "Evening Pass"
    private static function formatPassDisplayName(string $rawName): string
    {
        return trim((string) preg_replace('/([a-z])([A-Z])/', '$1 $2', $rawName));
    }

    /** @param EventSessionPrice[] $prices */
    private function hasPayWhatYouLikeTier(array $prices): bool
    {
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::PayWhatYouLike->value) {
                return true;
            }
        }

        return false;
    }

    // Fallback chain: exact tier -> Adult/Single -> any non-PWYL price -> 0.0
    /** @param EventSessionPrice[] $prices */
    private function resolveBasePrice(array $prices, ?int $priceTierId): float
    {
        if ($priceTierId !== null) {
            foreach ($prices as $price) {
                if ($price->priceTierId === $priceTierId) {
                    return (float) $price->price;
                }
            }
        }

        // Fallback: Adult or Single tier
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::Adult->value || $price->priceTierId === PriceTierId::Single->value) {
                return (float) $price->price;
            }
        }

        // Last resort: any non-PWYL price
        foreach ($prices as $price) {
            if ($price->priceTierId !== PriceTierId::PayWhatYouLike->value) {
                return (float) $price->price;
            }
        }

        return 0.0;
    }

    // basePrice is per-person (totalFee / guests), which is what customers expect to see.
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
            basePrice: $reservation->totalFee / max($totalGuests, 1), // max(..., 1) guards against division by zero
            reservationId: $reservation->reservationId,
            diningDate: $reservation->diningDate,
            timeSlot: $reservation->timeSlot,
            guestCount: $totalGuests,
        );
    }

    // Fallback: no Single/Adult tier found — pick the cheapest tier.
    /** @param EventSessionPrice[] $prices */
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

        // Fallback: no Single or Adult tier found — sort by price and pick the cheapest one.
        usort($prices, fn(EventSessionPrice $a, EventSessionPrice $b) => (float) $a->price <=> (float) $b->price);

        return $prices[0]->priceTierId ?? null;
    }

    // Fallback: no Group tier — walk from most expensive down to find any non-single tier.
    /** @param EventSessionPrice[] $prices */
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

        usort($prices, fn(EventSessionPrice $a, EventSessionPrice $b) => (float) $a->price <=> (float) $b->price);

        // Fallback: no Group tier found — walk from the most expensive price down to find
        // any tier that isn't the single-ticket tier.
        for ($index = count($prices) - 1; $index >= 0; $index--) {
            $priceTierId = $prices[$index]->priceTierId;
            if ($priceTierId !== $singlePriceTierId) {
                return $priceTierId;
            }
        }

        return $singlePriceTierId;
    }

    // $dateTime is a plain Unix timestamp string (e.g. "1717200000").
    /** @return array<int, array{dateTime: string, language: string, seatsAvailable: int, prices: array}> */
    public function getTourInfo(int $eventId, string $dateTime): array
    {
        if ($eventId <= 0 || $dateTime === '') {
            return [];
        }

        $parsedDateTime = $this->parseTourTimestamp($dateTime);
        if ($parsedDateTime === null) {
            return [];
        }

        $sessions = $this->loadTourSessionsForEventAt($eventId, $parsedDateTime);
        if ($sessions === []) {
            return [];
        }

        $sessionIds          = array_map(static fn(SessionWithEvent $s): int => $s->eventSessionId, $sessions);
        $labelsBySessionId   = $this->labelRepository->findLabelsBySessionIds($sessionIds);
        $pricesBySessionId   = $this->priceRepository->findPricesBySessionIds($sessionIds);
        $sharedPricesByTimeKey = $this->buildSharedHistoryPricesByTimeKey($sessions, $pricesBySessionId);

        return $this->buildTourInfoByLanguage($sessions, $labelsBySessionId, $pricesBySessionId, $sharedPricesByTimeKey);
    }

    /** @return SessionWithEvent[] */
    private function loadTourSessionsForEventAt(int $eventId, \DateTimeImmutable $when): array
    {
        $startDate = $when->format('Y-m-d');
        $startTime = $when->format('H:i:s');

        $result = $this->sessionRepository->findSessions(
            new EventSessionFilter(eventId: $eventId, startDate: $startDate, endDate: $startDate, startTime: $startTime)
        );

        return $result->sessions;
    }

    /** @return array<int, array{dateTime: string, language: string, seatsAvailable: int, prices: array}> */
    private function buildTourInfoByLanguage(
        array $sessions,
        array $labelsBySessionId,
        array $pricesBySessionId,
        array $sharedPricesByTimeKey,
    ): array {
        $infoByEventSessionId = [];
        $seenLanguageKeys     = [];

        foreach ($sessions as $session) {
            $labels       = $labelsBySessionId[$session->eventSessionId] ?? [];
            $languageLabel = HistorySessionHelper::resolveLanguageLabel($session->languageCode, $labels);
            $languageKey   = HistorySessionHelper::resolveLanguageKey($session->languageCode, $labels);

            // When multiple sessions share the same language key, only the first is kept.
            // Why: the booking widget must show each language once, not once per DB session.
            if ($languageKey !== null && isset($seenLanguageKeys[$languageKey])) {
                continue;
            }

            if ($languageKey !== null) {
                $seenLanguageKeys[$languageKey] = true;
            }

            $timeKey = $session->startDateTime->format('Y-m-d H:i:s');
            // Why shared prices: History tours at the same time share pricing — show one list.
            $prices = $sharedPricesByTimeKey[$timeKey] ?? ($pricesBySessionId[$session->eventSessionId] ?? []);

            $infoByEventSessionId[$session->eventSessionId] = $this->buildSingleTourInfoEntry($session, $languageLabel, $prices);
        }

        return $infoByEventSessionId;
    }

    /** @return array{dateTime: string, language: string, seatsAvailable: int, prices: array} */
    private function buildSingleTourInfoEntry(SessionWithEvent $session, string $languageLabel, array $prices): array
    {
        return [
            'dateTime'       => $session->startDateTime->format('Y-m-d H:i:s'),
            'language'       => $languageLabel,
            'seatsAvailable' => HistorySessionHelper::resolveSeatsAvailable($session),
            'prices'         => array_map(
                static fn(EventSessionPrice $price): array => [
                    'priceTierId' => $price->priceTierId,
                    'price'       => $price->price,
                ],
                $prices,
            ),
        ];
    }

    // Only plain integer strings accepted; @ prefix treats the value as a Unix timestamp.
    private function parseTourTimestamp(string $dateTime): ?\DateTimeImmutable
    {
        $timestamp = trim($dateTime);
        if ($timestamp === '' || !ctype_digit($timestamp)) {
            return null;
        }

        return new \DateTimeImmutable('@' . $timestamp)
            ->setTimezone(new \DateTimeZone(self::HISTORY_QUERY_TIMEZONE));
    }

    // Merges prices per time slot so tours in multiple languages show one combined price list.
    /** @param SessionWithEvent[] $sessions @param array<int, EventSessionPrice[]> $pricesBySessionId @return array<string, EventSessionPrice[]> */
    private function buildSharedHistoryPricesByTimeKey(array $sessions, array $pricesBySessionId): array
    {
        $sharedPricesByTimeKey = [];

        foreach ($sessions as $session) {
            $timeKey = $session->startDateTime->format('Y-m-d H:i:s');
            $sharedPricesByTimeKey[$timeKey] = HistorySessionHelper::mergeHighestPricesByKey(
                $sharedPricesByTimeKey[$timeKey] ?? [],
                $pricesBySessionId[$session->eventSessionId] ?? [],
            );
        }

        return array_map(static fn(array $prices): array => array_values($prices), $sharedPricesByTimeKey);
    }
}
