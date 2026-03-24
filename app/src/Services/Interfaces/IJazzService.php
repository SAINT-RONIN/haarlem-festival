<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\JazzPageData;

/**
 * Interface for Jazz page service.
 */
interface IJazzService
{
    /**
     * Assembles the complete domain payload for the Jazz overview page, including CMS content, artists, and sessions.
     */
    public function getJazzPageData(): JazzPageData;
}
