<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderItem;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IOrderItemRepository;
use App\Services\Interfaces\IOrderCapacityRestorer;

/**
 * Restores reserved session capacity for all event-session items in an order.
 */
final class OrderCapacityRestorer implements IOrderCapacityRestorer
{
    public function __construct(
        private readonly IOrderItemRepository $orderItemRepository,
        private readonly IEventSessionRepository $eventSessionRepository,
    ) {}

    public function restore(int $orderId): void
    {
        foreach ($this->orderItemRepository->findByOrderId($orderId) as $item) {
            $this->restoreItem($item);
        }
    }

    private function restoreItem(OrderItem $item): void
    {
        if ($item->eventSessionId === null || $item->eventSessionId <= 0) {
            return;
        }

        // Guard against corrupted quantity values — a negative quantity would reduce capacity
        // instead of restoring it, which is the opposite of what we want here.
        if ($item->quantity <= 0) {
            return;
        }

        $this->eventSessionRepository->restoreCapacity($item->eventSessionId, $item->quantity);
    }
}
