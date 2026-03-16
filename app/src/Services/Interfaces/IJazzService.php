<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Interface for Jazz page service.
 */
interface IJazzService
{
    /**
     * Builds the Jazz page payload with all required data.
     *
     * @return array<string, mixed> Prepared data for Jazz view-model mapping
     */
    public function getJazzPageData(): array;
}
