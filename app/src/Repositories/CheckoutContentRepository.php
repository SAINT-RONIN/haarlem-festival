<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\CheckoutContentMapper;
use App\DTOs\Cms\CheckoutMainContent;
use App\DTOs\Cms\ProgramMainContent;

/**
 * Provides typed access to Checkout and Program CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to CheckoutContentMapper.
 */
class CheckoutContentRepository extends BaseContentRepository implements Interfaces\ICheckoutContentRepository
{
    /** Fetches the checkout page main content. */
    public function findCheckoutMainContent(string $pageSlug, string $sectionKey): CheckoutMainContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return CheckoutContentMapper::mapCheckout($raw);
    }

    /** Fetches the program page main content. */
    public function findProgramMainContent(string $pageSlug, string $sectionKey): ProgramMainContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return CheckoutContentMapper::mapProgram($raw);
    }
}
