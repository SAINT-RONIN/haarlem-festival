<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Content\HistoricalLocationFactsContent;
use App\Content\HistoricalLocationHeroContent;
use App\Content\HistoricalLocationIntroContent;
use App\Content\HistoricalLocationSignificanceContent;

/**
 * Typed access to HistoricalLocation CMS content sections.
 */
interface IHistoricalLocationContentRepository
{
    /** Fetches the location-specific hero content. */
    public function findHeroContent(string $pageSlug, string $sectionKey): HistoricalLocationHeroContent;

    /**
     * Maps an already-fetched raw hero array to a location hero model.
     *
     * @param array<string, mixed> $raw
     */
    public function mapHeroFromRaw(array $raw): HistoricalLocationHeroContent;

    /** Fetches the location intro content. */
    public function findIntroContent(string $pageSlug, string $sectionKey): HistoricalLocationIntroContent;

    /** Fetches the location facts content. */
    public function findFactsContent(string $pageSlug, string $sectionKey): HistoricalLocationFactsContent;

    /** Fetches the location significance content. */
    public function findSignificanceContent(string $pageSlug, string $sectionKey): HistoricalLocationSignificanceContent;
}
