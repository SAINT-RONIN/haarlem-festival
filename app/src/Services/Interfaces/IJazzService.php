<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Interface for Jazz page service.
 */
interface IJazzService
{
    /**
     * Builds the Jazz page domain payload.
     *
     * @return array<string, mixed> Raw Jazz section content and schedule data
     */
    public function getJazzPageData(): array;
}
