<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\JazzPageData;

/**
 * Interface for Jazz page service.
 */
interface IJazzService
{
    public function getJazzPageData(): JazzPageData;
}
