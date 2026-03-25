<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PriceTierId;
use App\DTOs\Filters\EventSessionFilter;
use App\Models\EventSessionPrice;
use App\Models\Program;
use App\DTOs\Program\ProgramData;
use App\DTOs\Filters\ProgramFilter;
use App\Models\ProgramItem;
use App\DTOs\Program\ProgramItemData;
use App\DTOs\Filters\ProgramItemFilter;
use App\Repositories\Interfaces\IProgramRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IEventSessionPriceRepository;
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
     */
    public function addToProgram(string $sessionKey, ?int $userAccountId, int $eventSessionId, int $quantity, float $donationAmount): ProgramItem
    {
        if ($eventSessionId <= 0) {
            throw new \InvalidArgumentException('eventSessionId is required');
        }
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('quantity must be at least 1');
        }
        $program = $this->getOrCreateProgram($sessionKey, $userAccountId);

        $existingItem = $this->findExistingItem($program->programId, $eventSessionId);

        if ($existingItem !== null) {
            $newQuantity = $existingItem->quantity + $quantity;
            $this->programRepository->updateItemQuantity($existingItem->programItemId, $newQuantity);

            $items = $this->programRepository->findProgramItems(new ProgramItemFilter(programItemId: $existingItem->programItemId));
            return $items[0];
        }

        return $this->programRepository->addItem($program->programId, $eventSessionId, $quantity, $donationAmount);
    }

    /**
     * Updates the ticket quantity for a program item. Removes the item if quantity drops to zero.
     *
     * @throws \InvalidArgumentException When the item does not belong to the user's program
     */
    public function updateQuantity(string $sessionKey, ?int $userAccountId, int $programItemId, int $quantity): void
    {
        if ($programItemId <= 0) {
            throw new \InvalidArgumentException('programItemId is required');
        }
        $this->verifyItemOwnership($sessionKey, $userAccountId, $programItemId);

        if ($quantity <= 0) {
            $this->programRepository->removeItem($programItemId);
            return;
        }

        $this->programRepository->updateItemQuantity($programItemId, $quantity);
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
            return new ProgramData(program: null, items: [], subtotal: 0.0, taxAmount: 0.0, total: 0.0);
        }

        // Load raw program items from the database
        $programItems = $this->programRepository->findProgramItems(new ProgramItemFilter(programId: $program->programId));

        if ($programItems === []) {
            return new ProgramData(program: $program, items: [], subtotal: 0.0, taxAmount: 0.0, total: 0.0);
        }

        // Enrich each item with event session metadata and pricing
        $enrichedItems = $this->enrichItemsWithSessionData($programItems);

        // Calculate financial totals
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

    private function findExistingItem(int $programId, int $eventSessionId): ?ProgramItem
    {
        $items = $this->programRepository->findProgramItems(new ProgramItemFilter(
            programId: $programId,
            eventSessionId: $eventSessionId,
        ));

        return $items !== [] ? $items[0] : null;
    }

    /**
     * @param ProgramItem[] $programItems
     * @return ProgramItemData[]
     */
    private function enrichItemsWithSessionData(array $programItems): array
    {
        // Batch-load session details and prices to avoid N+1 queries
        $sessionIds = $this->extractSessionIds($programItems);
        $sessionsById = $this->fetchSessionsById($sessionIds);
        $pricesBySession = $this->fetchPricesBySession($sessionIds);

        $enrichedItems = [];
        foreach ($programItems as $item) {
            if ($item->eventSessionId === null) {
                continue;
            }

            $session = $sessionsById[$item->eventSessionId] ?? null;
            if ($session === null) {
                continue;
            }

            $prices = $pricesBySession[$item->eventSessionId] ?? [];
            $enrichedItems[] = new ProgramItemData(
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
                basePrice: $this->getBasePrice($prices),
            );
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
     * @return array<int, \App\Models\EventSession>
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
            $id = $session->eventSessionId;
            $sessionsById[$id] = $session;
        }

        return $sessionsById;
    }

    /**
     * @param int[] $sessionIds
     * @return array<int, \App\Models\EventSessionPrice[]>
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
    private function getBasePrice(array $prices): float
    {
        // Prefer fixed adult pricing when available; fall back to pay-what-you-like only if needed.
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::Adult->value) {
                return (float)$price->price;
            }
        }

        // If there's any non-PWYL tier (e.g. reservation fee), use it as the base ticket price.
        foreach ($prices as $price) {
            if ($price->priceTierId !== PriceTierId::PayWhatYouLike->value) {
                return (float)$price->price;
            }
        }

        // PWYL events have no fixed base price; donation covers the amount
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::PayWhatYouLike->value) {
                return 0.0;
            }
        }

        return 0.0;
    }
}
