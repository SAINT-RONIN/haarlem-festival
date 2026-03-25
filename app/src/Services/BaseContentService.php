<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Interfaces\ICmsContentRepository;

/**
 * Base service for services that depend on the CMS content repository.
 *
 * Provides a shared protected accessor so child services don't each
 * declare their own ICmsContentRepository dependency.
 */
abstract class BaseContentService
{
    public function __construct(
        protected readonly ICmsContentRepository $cmsContentRepository,
    ) {
    }
}
