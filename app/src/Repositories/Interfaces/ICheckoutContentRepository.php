<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Cms\CheckoutMainContent;
use App\DTOs\Cms\ProgramMainContent;

/**
 * Typed access to Checkout and Program CMS content sections.
 */
interface ICheckoutContentRepository
{
    /** Fetches the checkout page main content. */
    public function findCheckoutMainContent(string $pageSlug, string $sectionKey): CheckoutMainContent;

    /** Fetches the program page main content. */
    public function findProgramMainContent(string $pageSlug, string $sectionKey): ProgramMainContent;
}
