<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IStorytellingService
{
    public function getStorytellingPageData(): array;

    /**
     * @throws \RuntimeException if the event is not found or not a storytelling event
     */
    public function getStorytellingDetailPageData(int $eventId): array;
}
