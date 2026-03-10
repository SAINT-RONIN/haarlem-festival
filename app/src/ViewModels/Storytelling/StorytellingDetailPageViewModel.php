<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\ViewModels\GlobalUiData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * ViewModel for the Storytelling Detail page (single story/event).
 */
final readonly class StorytellingDetailPageViewModel
{
    /**
     * @param StoryHighlightData[] $highlights
     * @param string[]             $galleryImages  Web-safe URL paths
     * @param string[]             $labels         E.g. ['In English', 'For ages 16+']
     */
    public function __construct(
        public GlobalUiData             $globalUi,
        public int                      $eventId,
        public string                   $title,
        public string                   $subtitle,
        public string                   $heroImageUrl,
        public array                    $labels,
        public string                   $aboutHeading,
        public string                   $aboutBodyHtml,
        public string                   $aboutImage1Url,
        public string                   $aboutImage2Url,
        public array                    $highlights,
        public array                    $galleryImages,
        public string                   $videoUrl,
        public ScheduleSectionViewModel $scheduleSection,
    ) {
    }
}
