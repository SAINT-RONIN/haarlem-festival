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

    /**
     * Adds an event session to the program, or increases quantity if already present.
     *
     * Resolves price tiers for both single and group tickets from the session's configured
     * prices, then persists each ticket type that has a positive quantity. If a matching
     * program item already exists for that session + price tier, its quantity is incremented
     * instead of inserting a duplicate row.
     *
     * @param string   $sessionKey         Browser session key used to identify the program.
     * @param int|null $userAccountId      Logged-in user's account ID, or null for guests.
     * @param int      $eventSessionId     The session being added to the program.
     * @param int      $quantity           Number of single (adult) tickets to add. 0 to skip.
     * @param int      $groupTicketQuantity Number of group tickets to add. 0 to skip.
     * @param float    $donationAmount     Optional donation amount attached to the item.
     * @throws \InvalidArgumentException When $eventSessionId is invalid or both quantities are zero.
     * @throws ProgramException          When the database write fails or validation fails.
     */
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

    /**
     * Adds a ticket to the program or increments its quantity if an identical item already exists.
     *
     * "Identical" means the same session ID and price tier. If a row already exists,
     * its quantity is incremented. If no row exists, a new program item is inserted.
     *
     * @param int    $programId      The program to add the item to.
     * @param int    $eventSessionId The session being booked.
     * @param int    $quantity       Number of tickets to add (must be > 0).
     * @param int|null $priceTierId  The price tier to use. Null means the session has no price configured.
     * @param float  $donationAmount Donation amount attached to this item.
     * @param string $ticketKind     Human-readable label for error messages ("single" or "group").
     * @throws \InvalidArgumentException When no price tier is configured for this session and ticket kind.
     */
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

    /**
     * Validates the inputs before adding a pass to the program.
     *
     * Throws \InvalidArgumentException (not a custom exception) because an invalid
     * passTypeId or quantity is a programming error — the caller sent bad data,
     * not a user-facing validation failure that needs to be shown in a form.
     */
    private function validatePassInput(int $passTypeId, int $quantity): void
    {
        if ($passTypeId <= 0) {
            throw new \InvalidArgumentException('passTypeId is required');
        }
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('quantity must be at least 1');
        }
    }

    /**
     * Validates the inputs before adding a session item to the program.
     *
     * Same reasoning as validatePassInput — invalid ids or quantities are a programming
     * error, not a form-level validation failure, so \InvalidArgumentException is used.
     * At least one of quantity or groupTicketQuantity must be positive (both can be).
     */
    private function validateAddInput(int $eventSessionId, int $quantity, int $groupTicketQuantity): void
    {
        if ($eventSessionId <= 0) {
            throw new \InvalidArgumentException('eventSessionId is required');
        }
        if ($quantity <= 0 && $groupTicketQuantity <= 0) {
            throw new \InvalidArgumentException('quantity must be at least 1');
        }
    }

    /**
     * Validates that the donation amount is non-negative.
     */
    private function validateDonationAmount(float $donationAmount): void
    {
        if ($donationAmount < 0) {
            throw new \InvalidArgumentException('Donation amount cannot be negative');
        }
    }

    /**
     * Validates that a session exists before attempting to add it to the program.
     *
     * @throws ProgramException When the session does not exist.
     */
    private function validateSessionExists(int $eventSessionId, ?SessionCapacityInfo $capacity): void
    {
        if ($capacity === null) {
            throw new ProgramException("Session with ID {$eventSessionId} does not exist.");
        }
    }

    /**
     * Counts the seats the user already has in their cart for a given session.
     *
     * Group tickets (Family, Group) count as 4 seats each because a group ticket
     * represents 4 people. Single tickets count as 1 seat each.
     *
     * @return array{seats: int, singles: int}
     */
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

    /**
     * Validates that the requested seats fit within the session's available capacity,
     * accounting for seats already reserved in the user's cart.
     *
     * @throws ProgramException When there is insufficient capacity.
     */
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

    /**
     * Enforces the single-ticket cap when the CMS has configured one on the session.
     *
     * The check accounts for:
     * - soldSingleTickets: tickets already purchased by all users (persisted in DB)
     * - singlesAlreadyInCart: tickets this user has in their cart but not yet purchased
     * - requestedSingles: tickets the user is trying to add right now
     *
     * All three together must not exceed CapacitySingleTicketLimit.
     *
     * @throws ProgramException When the single-ticket cap has been reached.
     */
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

    /**
     * Enforces the group ticket cap for a session.
     *
     * A group ticket represents GROUP_TICKET_SEAT_COUNT participants, so the maximum
     * number of group tickets is floor(remainingSeats / GROUP_TICKET_SEAT_COUNT).
     * This accounts for single tickets already in the cart because those seats are
     * no longer available to be filled by a group.
     *
     * Example with capacity 12:
     *   - 0 in cart → max 3 group tickets (3 × 4 = 12)
     *   - 2 singles in cart → remaining = 10, max 2 group tickets (2 × 4 = 8, +2 singles = 10)
     *   - 2 groups in cart (8 seats) → remaining = 4, max 1 more group ticket
     *
     * @throws ProgramException When the requested group quantity exceeds capacity.
     */
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

    /**
     * Validates that all prices for a session are non-negative.
     *
     * @param EventSessionPrice[] $prices
     * @throws ProgramException When any price is negative.
     */
    private function validatePricesNonNegative(array $prices): void
    {
        foreach ($prices as $price) {
            if ((float) $price->price < 0) {
                throw new ProgramException('Invalid ticket price detected. Please contact support.');
            }
        }
    }

    /**
     * Adds to the quantity of an existing program item and returns the refreshed row.
     *
     * The item is re-fetched from the database after the update so the returned object
     * has the actual stored quantity, not just the locally calculated value.
     */
    private function incrementExistingItem(ProgramItem $existingItem, int $additionalQuantity): ProgramItem
    {
        $newQuantity = $existingItem->quantity + $additionalQuantity;
        $this->programRepository->updateItemQuantity($existingItem->programItemId, $newQuantity);

        $items = $this->programRepository->findProgramItems(new ProgramItemFilter(programItemId: $existingItem->programItemId));

        return $items[0];
    }

    /**
     * Updates the ticket quantity for a program item. Removes the item if quantity drops to zero.
     *
     * Capacity is re-validated against the new quantity so users cannot bypass the limits
     * enforced in addToProgram by incrementing an existing item on the My Program page.
     *
     * @throws \InvalidArgumentException When the item does not belong to the user's program
     * @throws ProgramException          When the new quantity exceeds the session's capacity
     */
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

    /**
     * Validates that a new quantity for an existing program item does not exceed the session's
     * remaining capacity or group/single-ticket limits.
     *
     * The item's current contribution is excluded from the cart totals so the check treats the
     * operation as "remove old quantity, add new quantity" — identical semantics to addToProgram.
     * All enforcement is delegated to the three canonical validation methods to avoid duplication.
     *
     * @throws ProgramException When the new quantity would exceed capacity or ticket limits.
     */
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

        // Clamp negative donations to zero — a negative donation amount has no meaning.
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

    /**
     * Builds a ProgramData with no items and all financial totals set to zero.
     *
     * canCheckout is always false when there are no items — there is nothing to pay for.
     */
    private function buildEmptyProgramData(?Program $program): ProgramData
    {
        return new ProgramData(program: $program, items: [], subtotal: 0.0, taxAmount: 0.0, total: 0.0, canCheckout: false);
    }

    /**
     * Enriches all items with session data and calculates the full financial summary.
     *
     * VAT is calculated on the subtotal, which already includes any donations.
     * canCheckout is true only when at least one item was successfully enriched —
     * items that have lost their session data are dropped from the display.
     */
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

    /**
     * Looks up an existing program item matching both the session and the price tier.
     *
     * Both dimensions are needed because the same session can appear twice in one program —
     * once as a single-ticket item and once as a group-ticket item with a different price tier.
     */
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
     * Converts raw program items into enriched display objects with session data and pricing.
     *
     * There are three item types, each with its own builder:
     * - Reservation items (restaurant bookings) — no session id
     * - Pass items (Jazz day/evening passes) — no session id
     * - Session items (all other events) — linked to a session id
     *
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

    /**
     * Collects the unique session ids from a list of program items.
     *
     * Pass and reservation items don't have session ids and are skipped. Duplicates are
     * removed so the session and price queries don't return duplicate rows.
     *
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
     * Loads sessions by their ids and returns them keyed by session id.
     *
     * The map is keyed by session id so each buildProgramItemData call can find
     * its session in O(1) without looping through all sessions.
     *
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
     * Loads prices for a list of sessions, keyed by session id.
     *
     * The repository already returns a map keyed by session id, so this method
     * is a thin guard that avoids calling the repository with an empty list.
     *
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
     * Sums up the total cost of all items in the program, including optional donations.
     *
     * Donations are included in the subtotal because customers see one total and donations
     * are part of what they pay. VAT is calculated on top of this subtotal, not inside it.
     *
     * @param ProgramItemData[] $items
     */
    private function calculateSubtotal(array $items): float
    {
        $subtotal = 0.0;

        foreach ($items as $item) {
            // base price × quantity + optional donation. The donation is per-item, not per-ticket.
            $subtotal += ($item->basePrice * $item->quantity) + $item->donationAmount;
        }

        return $subtotal;
    }

    /**
     * Builds the display object for a session-based program item.
     *
     * Returns null when the session is missing from the map — this can happen when
     * a session was deleted after the user added it to their program. The caller skips
     * nulls so the item silently disappears from the program view.
     *
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

    /**
     * Builds the display object for a Jazz festival pass item.
     *
     * Returns null when the pass type no longer exists in the database.
     * Fields like venueName, hallName, and startDateTime are not set because
     * a pass is not tied to a specific venue or time slot.
     */
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

    /**
     * Converts a CamelCase database pass name into a human-readable display name.
     *
     * Examples: "DayPass" → "Day Pass", "EveningPass" → "Evening Pass".
     * Pass names are stored in the DB as CamelCase strings; this makes them readable
     * in the program view without needing a separate display-name column.
     */
    private static function formatPassDisplayName(string $rawName): string
    {
        // Insert a space before each uppercase letter that follows a lowercase letter — this splits CamelCase words.
        return trim((string) preg_replace('/([a-z])([A-Z])/', '$1 $2', $rawName));
    }

    /**
     * Returns true when the session has a "pay what you like" price tier.
     *
     * This is used to tell the frontend to show a donation amount input instead of
     * a fixed price, so the user knows they can choose what they want to pay.
     *
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
     * Finds the price for a program item using a three-pass fallback chain.
     *
     * Pass 1: use the exact tier stored on the program item (most common case).
     * Pass 2: fall back to Adult or Single tier — the standard single-ticket tiers.
     * Pass 3: last resort — take any price that isn't pay-what-you-like, so at least
     *         something is shown. Returns 0.0 only when there are no prices at all.
     *
     * @param EventSessionPrice[] $prices
     */
    private function resolveBasePrice(array $prices, ?int $priceTierId): float
    {
        // Pass 1: find the price for the exact tier attached to this program item.
        if ($priceTierId !== null) {
            foreach ($prices as $price) {
                if ($price->priceTierId === $priceTierId) {
                    return (float) $price->price;
                }
            }
        }

        // Pass 2: fall back to Adult or Single, which are the most common single-ticket tiers.
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::Adult->value || $price->priceTierId === PriceTierId::Single->value) {
                return (float) $price->price;
            }
        }

        // Pass 3: last resort — any price that isn't pay-what-you-like, so at least we show something.
        foreach ($prices as $price) {
            if ($price->priceTierId !== PriceTierId::PayWhatYouLike->value) {
                return (float) $price->price;
            }
        }

        return 0.0;
    }

    /**
     * Builds the display object for a restaurant reservation item.
     *
     * quantity is the total guest count (adults + children) because pricing is per person.
     * basePrice is the total fee divided by the number of guests so the program shows the
     * per-person price, which is what the customer expects to see on each line.
     */
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

    /**
     * Finds the price tier id to use for a single ticket purchase.
     *
     * First pass: look for a Single or Adult tier — that is the expected case for most events.
     * Fallback: if neither exists, sort by price ascending and pick the cheapest tier, which is
     * the most reasonable default for unusual event configurations.
     *
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

        // Fallback: no Single or Adult tier found — sort by price and pick the cheapest one.
        usort($prices, fn(EventSessionPrice $a, EventSessionPrice $b) => (float) $a->price <=> (float) $b->price);

        return $prices[0]->priceTierId ?? null;
    }

    /**
     * Finds the price tier id to use for a group ticket purchase.
     *
     * First pass: look for an explicit Group tier — the straightforward case.
     * Fallback: sort by price descending and find the most expensive tier that is not
     * the single-ticket tier, since group prices are typically higher than single prices.
     * If everything is the same tier as single, return the single tier as a last resort.
     *
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

    /**
     * Returns session info for all History tour sessions at a specific event and start time.
     *
     * $dateTime must be a plain Unix timestamp string (e.g. "1717200000"). Multiple sessions
     * can share the same time slot when they are offered in different languages — those are
     * collapsed so each language appears only once in the result. Prices are merged across
     * all sessions at the same time slot so the booking widget shows one combined price list.
     *
     * Returns an array keyed by eventSessionId, each value containing dateTime, language,
     * seatsAvailable, and prices.
     *
     * @param array<int, array> $infoByEventSessionId
     */
    /**
     * Returns booking info for all History tour sessions at a given event and start time.
     *
     * Looks up every session for $eventId that starts at the moment encoded in $dateTime
     * (a plain Unix timestamp string, e.g. "1717200000"). Multiple sessions can share the
     * same time slot when offered in different languages — duplicates are collapsed so each
     * language appears only once. Prices are merged across sessions at the same slot so the
     * booking widget shows one combined price list.
     *
     * Returns an array keyed by eventSessionId. Each value contains:
     *   - dateTime       — "Y-m-d H:i:s" start time
     *   - language       — human-readable language label
     *   - seatsAvailable — remaining seat count
     *   - prices         — list of [ priceTierId, price ] maps
     *
     * Returns an empty array when $eventId is invalid, $dateTime cannot be parsed,
     * or no sessions are found.
     *
     * @param int    $eventId  The event to look up sessions for.
     * @param string $dateTime Unix timestamp string (digits only, e.g. "1717200000").
     * @return array<int, array{dateTime: string, language: string, seatsAvailable: int, prices: array}>
     */
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

    /**
     * Fetches all sessions for $eventId that start on the date and time of $when.
     *
     * Uses only the date ("Y-m-d") and time ("H:i:s") parts of $when to query the
     * session repository — timezone conversions happen before this call.
     *
     * @param int                $eventId The event whose sessions to load.
     * @param \DateTimeImmutable $when    The exact start moment to match (date + time).
     * @return SessionWithEvent[]         Empty array when no sessions match.
     */
    private function loadTourSessionsForEventAt(int $eventId, \DateTimeImmutable $when): array
    {
        $startDate = $when->format('Y-m-d');
        $startTime = $when->format('H:i:s');

        $result = $this->sessionRepository->findSessions(
            new EventSessionFilter(eventId: $eventId, startDate: $startDate, endDate: $startDate, startTime: $startTime)
        );

        return $result->sessions;
    }

    /**
     * Builds the tour-info map from a list of sessions, collapsing duplicate languages.
     *
     * Iterates sessions and skips any whose language key has already been seen in this
     * time slot — this prevents the same tour appearing twice in the booking widget when
     * multiple DB sessions represent the same language offering.
     *
     * For each unique language, prices are taken from the shared time-slot pool first
     * (all sessions at the same start time share one merged price list). If no shared
     * prices exist, the session's own prices are used as a fallback.
     *
     * @param SessionWithEvent[]                     $sessions             All sessions for the time slot.
     * @param array<int, array>                      $labelsBySessionId    Labels keyed by session ID.
     * @param array<int, EventSessionPrice[]>        $pricesBySessionId    Prices keyed by session ID.
     * @param array<string, EventSessionPrice[]>     $sharedPricesByTimeKey Merged prices keyed by "Y-m-d H:i:s".
     * @return array<int, array{dateTime: string, language: string, seatsAvailable: int, prices: array}>
     */
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

    /**
     * Builds the info array for one tour session entry.
     *
     * Formats the start time as a "Y-m-d H:i:s" string, resolves available seats,
     * and maps each price to the [ priceTierId, price ] shape expected by the booking widget.
     *
     * @param SessionWithEvent   $session       The session to describe.
     * @param string             $languageLabel Human-readable language label (e.g. "English").
     * @param EventSessionPrice[] $prices        Prices to include (may be a shared time-slot pool).
     * @return array{dateTime: string, language: string, seatsAvailable: int, prices: array}
     */
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

    /**
     * Validates and converts a Unix timestamp string to a DateTimeImmutable in UTC.
     *
     * Only plain integer strings are accepted — dots, dashes, or letters mean the caller
     * sent something other than a Unix timestamp, which is rejected by returning null.
     * The @ prefix tells PHP to treat the value as a Unix timestamp, then we convert to
     * UTC to match how sessions are stored in the database.
     */
    private function parseTourTimestamp(string $dateTime): ?\DateTimeImmutable
    {
        $timestamp = trim($dateTime);
        // We only accept a plain integer Unix timestamp — reject anything with dots, dashes, or letters.
        if ($timestamp === '' || !ctype_digit($timestamp)) {
            return null;
        }

        // @ prefix tells PHP the value is a Unix timestamp. We then convert to UTC to match session storage.
        return new \DateTimeImmutable('@' . $timestamp)
            ->setTimezone(new \DateTimeZone(self::HISTORY_QUERY_TIMEZONE));
    }

    /**
     * Builds a map of session start time to a merged price list for History tours.
     *
     * History tours often run the same slot in multiple languages, each as a separate session.
     * Pricing should be shown once per time slot (not once per session), so this method
     * merges all prices for each time slot, keeping the highest price for each tier across
     * all sessions that share that start time.
     *
     * @param SessionWithEvent[] $sessions
     * @param array<int, EventSessionPrice[]> $pricesBySessionId
     * @return array<string, EventSessionPrice[]>
     */
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

        // Re-index each price group as a plain list (0, 1, 2...) after the merge,
        // so the caller doesn't have to deal with associative keys.
        return array_map(static fn(array $prices): array => array_values($prices), $sharedPricesByTimeKey);
    }
}
