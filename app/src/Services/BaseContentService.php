<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\GlobalUiConstants;
use App\Models\GlobalUiContent;
use App\Repositories\Interfaces\IGlobalContentRepository;

/**
 * Base service for page services that need the shared global-UI CMS section.
 *
 * Provides a shared loadGlobalUi() method so child services don't each inject
 * and call IGlobalContentRepository separately. Replaces the old GlobalUiContentLoader
 * service, eliminating a service-to-service dependency.
 */
abstract class BaseContentService
{
    public function __construct(
        protected readonly IGlobalContentRepository $globalContentRepo,
    ) {
    }

    /** Loads the shared global-UI CMS section used by all page headers/footers. */
    protected function loadGlobalUi(): GlobalUiContent
    {
        return $this->globalContentRepo->findGlobalUiContent(
            GlobalUiConstants::PAGE_SLUG,
            GlobalUiConstants::SECTION_KEY,
        );
    }
}
