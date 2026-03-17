<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PriceTierId;
use App\Models\EventSessionPrice;
use App\Models\Program;
use App\Models\ProgramItem;
use App\Repositories\EventSessionPriceRepository;
use App\Repositories\EventSessionRepository;
use App\Repositories\ProgramRepository;
use App\Repositories\Interfaces\IProgramRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IEventSessionPriceRepository;
use App\Services\Interfaces\IProgramService;

class ProgramService implements IProgramService
{
    private const VAT_RATE = 0.21;

    private IProgramRepository $programRepository;
    private IEventSessionRepository $sessionRepository;
    private IEventSessionPriceRepository $priceRepository;

    public function __construct(
        ?IProgramRepository $programRepository = null,
        ?IEventSessionRepository $sessionRepository = null,
        ?IEventSessionPriceRepository $priceRepository = null,
    ) {
        $this->programRepository = $programRepository ?? new ProgramRepository();
        $this->sessionRepository = $sessionRepository ?? new EventSessionRepository();
        $this->priceRepository = $priceRepository ?? new EventSessionPriceRepository();
    }

    public function getOrCreateProgram(string $sessionKey, ?int $userAccountId): Program
    {
        $program = $this->findActiveProgram($sessionKey, $userAccountId);

        if ($program !== null) {
            return $program;
        }

        return $this->programRepository->createProgram($sessionKey, $userAccountId);
    }

    public function addToProgram(string $sessionKey, ?int $userAccountId, int $eventSessionId, int $quantity, float $donationAmount): ProgramItem
    {
        $program = $this->getOrCreateProgram($sessionKey, $userAccountId);

        $existingItem = $this->findExistingItem($program->programId, $eventSessionId);

        if ($existingItem !== null) {
            $newQuantity = $existingItem->quantity + $quantity;
            $this->programRepository->updateItemQuantity($existingItem->programItemId, $newQuantity);

            $items = $this->programRepository->findProgramItems(['programItemId' => $existingItem->programItemId]);
            return $items[0];
        }

        return $this->programRepository->addItem($program->programId, $eventSessionId, $quantity, $donationAmount);
    }

    public function updateQuantity(string $sessionKey, ?int $userAccountId, int $programItemId, int $quantity): void
    {
        $this->verifyItemOwnership($sessionKey, $userAccountId, $programItemId);

        if ($quantity <= 0) {
            $this->programRepository->removeItem($programItemId);
            return;
        }

        $this->programRepository->updateItemQuantity($programItemId, $quantity);
    }

    public function updateDonation(string $sessionKey, ?int $userAccountId, int $programItemId, float $donationAmount): void
    {
        $this->verifyItemOwnership($sessionKey, $userAccountId, $programItemId);

        if ($donationAmount < 0) {
            $donationAmount = 0.0;
        }

        $this->programRepository->updateItemDonation($programItemId, $donationAmount);
    }

    public function removeItem(string $sessionKey, ?int $userAccountId, int $programItemId): void
    {
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

    public function getProgramData(string $sessionKey, ?int $userAccountId): array
    {
        $program = $this->findActiveProgram($sessionKey, $userAccountId);
        $emptyResult = ['program' => null, 'items' => [], 'subtotal' => 0.0, 'taxAmount' => 0.0, 'total' => 0.0];

        if ($program === null) {
            return $emptyResult;
        }

        $programItems = $this->programRepository->findProgramItems(['programId' => $program->programId]);

        if ($programItems === []) {
            $emptyResult['program'] = $program;
            return $emptyResult;
        }

        $enrichedItems = $this->enrichItemsWithSessionData($programItems);
        $subtotal = $this->calculateSubtotal($enrichedItems);
        $taxAmount = $subtotal * self::VAT_RATE;
        $total = $subtotal + $taxAmount;

        return [
            'program' => $program,
            'items' => $enrichedItems,
            'subtotal' => $subtotal,
            'taxAmount' => $taxAmount,
            'total' => $total,
        ];
    }

    private function verifyItemOwnership(string $sessionKey, ?int $userAccountId, int $programItemId): void
    {
        $program = $this->findActiveProgram($sessionKey, $userAccountId);

        if ($program === null) {
            throw new \RuntimeException('Program not found');
        }

        $items = $this->programRepository->findProgramItems([
            'programItemId' => $programItemId,
            'programId' => $program->programId,
        ]);

        if ($items === []) {
            throw new \RuntimeException('Item does not belong to your program');
        }
    }

    private function findActiveProgram(string $sessionKey, ?int $userAccountId): ?Program
    {
        if ($userAccountId !== null) {
            $programs = $this->programRepository->findPrograms([
                'userAccountId' => $userAccountId,
                'isCheckedOut' => false,
            ]);

            if ($programs !== []) {
                return $programs[0];
            }
        }

        $programs = $this->programRepository->findPrograms([
            'sessionKey' => $sessionKey,
            'isCheckedOut' => false,
        ]);

        return $programs !== [] ? $programs[0] : null;
    }

    private function findExistingItem(int $programId, int $eventSessionId): ?ProgramItem
    {
        $items = $this->programRepository->findProgramItems([
            'programId' => $programId,
            'eventSessionId' => $eventSessionId,
        ]);

        return $items !== [] ? $items[0] : null;
    }

    /**
     * @param ProgramItem[] $programItems
     * @return array<int, array<string, mixed>>
     */
    private function enrichItemsWithSessionData(array $programItems): array
    {
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
            $isPayWhatYouLike = $this->hasPayWhatYouLikeTier($prices);
            $basePrice = $this->getBasePrice($prices);

            $enrichedItems[] = [
                'programItemId' => $item->programItemId,
                'eventSessionId' => $item->eventSessionId,
                'quantity' => $item->quantity,
                'donationAmount' => (float)($item->donationAmount ?? '0.00'),
                'eventTitle' => $session['EventTitle'] ?? '',
                'venueName' => $session['VenueName'] ?? '',
                'hallName' => $session['HallName'] ?? null,
                'startDateTime' => $session['StartDateTime'] ?? '',
                'endDateTime' => $session['EndDateTime'] ?? null,
                'eventTypeId' => isset($session['EventTypeId']) ? (int)$session['EventTypeId'] : null,
                'eventTypeName' => $session['EventTypeName'] ?? '',
                'eventTypeSlug' => $session['EventTypeSlug'] ?? '',
                'languageCode' => $session['LanguageCode'] ?? null,
                'minAge' => isset($session['MinAge']) ? (int)$session['MinAge'] : null,
                'maxAge' => isset($session['MaxAge']) ? (int)$session['MaxAge'] : null,
                'isPayWhatYouLike' => $isPayWhatYouLike,
                'basePrice' => $basePrice,
            ];
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
     * @return array<int, array<string, mixed>>
     */
    private function fetchSessionsById(array $sessionIds): array
    {
        if ($sessionIds === []) {
            return [];
        }

        $result = $this->sessionRepository->findSessions(['sessionIds' => $sessionIds]);
        $sessions = $result['sessions'] ?? [];

        $sessionsById = [];
        foreach ($sessions as $session) {
            $id = (int)($session['EventSessionId'] ?? 0);
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

        return $this->priceRepository->findPrices([
            'sessionIds' => $sessionIds,
            'groupBySession' => true,
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    private function calculateSubtotal(array $items): float
    {
        $subtotal = 0.0;

        foreach ($items as $item) {
            $basePrice = (float)$item['basePrice'];
            $quantity = (int)$item['quantity'];
            $donationAmount = (float)$item['donationAmount'];
            $subtotal += ($basePrice * $quantity) + $donationAmount;
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

        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::PayWhatYouLike->value) {
                return 0.0;
            }
        }

        if ($prices !== []) {
            return (float)$prices[0]->price;
        }

        return 0.0;
    }
}
