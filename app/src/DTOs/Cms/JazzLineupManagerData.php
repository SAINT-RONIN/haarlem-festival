<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

use App\DTOs\Domain\Events\ArtistCardRecord;
use App\Models\Artist;

/**
 * Extra Jazz-only data used by the CMS page editor to manage lineup cards
 * alongside the generic CmsItem section fields.
 */
final readonly class JazzLineupManagerData
{
    /**
     * @param ArtistCardRecord[] $visibleArtists
     * @param Artist[] $availableArtists
     */
    public function __construct(
        public array $visibleArtists,
        public array $availableArtists,
    ) {}
}
