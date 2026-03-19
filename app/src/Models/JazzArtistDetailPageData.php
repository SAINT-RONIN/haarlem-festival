<?php

declare(strict_types=1);

namespace App\Models;

final readonly class JazzArtistDetailPageData
{
    /**
     * @param array<string, string> $cms
     */
    public function __construct(
        public JazzArtistDetailEvent $event,
        public array $cms,
        public int $eventId,
    ) {
    }
}
