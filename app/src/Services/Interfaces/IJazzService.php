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
     * @return array{sections: array<string, mixed>, passPrices: \App\Models\PassType[]}
     */
    public function getJazzPageData(): array;
}
