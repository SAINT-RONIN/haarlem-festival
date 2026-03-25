<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\GlobalUiConstants;
use App\Models\GlobalUiContent;
use App\Repositories\GlobalContentRepository;

/**
 * Centralises the loading of GlobalUiContent so every service
 * that needs it delegates here instead of duplicating the call.
 */
class GlobalUiContentLoader
{
    public function __construct(
        private readonly GlobalContentRepository $globalContentRepo,
    ) {
    }

    /**
     * Fetches and hydrates the shared global-UI CMS section.
     */
    public function load(): GlobalUiContent
    {
        return $this->globalContentRepo->findGlobalUiContent(
            GlobalUiConstants::PAGE_SLUG,
            GlobalUiConstants::SECTION_KEY,
        );
    }
}
