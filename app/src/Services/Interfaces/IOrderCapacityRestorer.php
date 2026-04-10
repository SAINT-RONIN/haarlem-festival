<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IOrderCapacityRestorer
{
    public function restore(int $orderId): void;
}
