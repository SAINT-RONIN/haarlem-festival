<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

use App\Models\Artist;
use App\Models\EventType;
use App\Models\Venue;

/**
 * View data for the CMS event create page (event-create.php).
 *
 * Carries dropdown options for type, venue, artist, and restaurant.
 */
final readonly class CmsEventCreateViewModel
{
    /**
     * @param EventType[] $eventTypes
     * @param Venue[] $venues
     * @param Artist[] $artists
     * @param array $restaurants
     */
    public function __construct(
        public array   $eventTypes,
        public array   $venues,
        public array   $artists,
        public array   $restaurants,
        public ?string $errorMessage,
        public ?string $successMessage,
        public string  $preselectedDay,
    ) {
    }
}
