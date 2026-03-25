<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\CheckoutContentMapper;
use App\Models\CheckoutMainContent;
use App\Models\ProgramMainContent;
use App\Repositories\Interfaces\ICmsContentRepository;

/**
 * Provides typed access to Checkout and Program CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to CheckoutContentMapper.
 */
class CheckoutContentRepository
{
    public function __construct(
        private readonly ICmsContentRepository $cmsContent,
    ) {
    }

    /** Fetches the checkout page main content. */
    public function findCheckoutMainContent(string $pageSlug, string $sectionKey): CheckoutMainContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return CheckoutContentMapper::mapCheckout($raw);
    }

    /** Fetches the program page main content. */
    public function findProgramMainContent(string $pageSlug, string $sectionKey): ProgramMainContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return CheckoutContentMapper::mapProgram($raw);
    }
}
