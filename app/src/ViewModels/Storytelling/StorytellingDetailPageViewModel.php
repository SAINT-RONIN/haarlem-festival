<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

use App\Helpers\ImageHelper;
use App\ViewModels\GlobalUiData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

final readonly class StorytellingDetailPageViewModel
{
    private const DEFAULT_BACK_BUTTON_LABEL = 'Back to storytelling';
    private const DEFAULT_RESERVE_BUTTON_LABEL = 'Reserve your spot';
    private const DEFAULT_HIGHLIGHTS_HEADING = 'Story highlights';
    private const DEFAULT_GALLERY_HEADING = 'Where stories come alive';
    private const DEFAULT_VIDEO_HEADING = 'A moment from the show';
    private const DEFAULT_VIDEO_PLACEHOLDER = 'Video coming soon';
    private const GALLERY_IMAGE_COUNT = 5;

    /**
     * @param StoryHighlightData[] $highlights
     * @param string[]             $galleryImages
     * @param string[]             $labels
     */
    public function __construct(
        public GlobalUiData             $globalUi,
        public int                      $eventId,
        public string                   $title,
        public string                   $subtitle,
        public string                   $heroImageUrl,
        public array                    $labels,
        public string                   $backButtonLabel,
        public string                   $reserveButtonLabel,
        public string                   $aboutHeading,
        public string                   $aboutBodyHtml,
        public string                   $aboutImage1Url,
        public string                   $aboutImage2Url,
        public array                    $highlights,
        public string                   $highlightsSectionHeading,
        public array                    $galleryImages,
        public string                   $gallerySectionHeading,
        public string                   $videoSectionHeading,
        public string                   $videoUrl,
        public string                   $videoPlaceholderText,
        public ScheduleSectionViewModel $scheduleSection,
    ) {
    }

    /**
     * @param string[] $labels
     */
    public static function fromEventData(
        array $globalUiContent,
        bool $isLoggedIn,
        int $eventId,
        array $event,
        ?string $featuredImagePath,
        array $labels,
        string $aboutBodyHtml,
        array $cms,
        array $scheduleData,
    ): self {
        return new self(
            globalUi: GlobalUiData::fromCms($globalUiContent, $isLoggedIn),
            eventId: $eventId,
            title: $event['Title'],
            subtitle: $event['ShortDescription'] ?? '',
            heroImageUrl: ImageHelper::validatePath($featuredImagePath ?? ''),
            labels: $labels,
            backButtonLabel: ImageHelper::getStringValue($cms, 'back_button_label', self::DEFAULT_BACK_BUTTON_LABEL),
            reserveButtonLabel: ImageHelper::getStringValue($cms, 'reserve_button_label', self::DEFAULT_RESERVE_BUTTON_LABEL),
            aboutHeading: ImageHelper::getStringValue($cms, 'about_heading', $event['Title']),
            aboutBodyHtml: $aboutBodyHtml,
            aboutImage1Url: ImageHelper::validatePath((string)($cms['about_image_1'] ?? '')),
            aboutImage2Url: ImageHelper::validatePath((string)($cms['about_image_2'] ?? '')),
            highlights: StoryHighlightData::fromCmsArray($cms),
            highlightsSectionHeading: ImageHelper::getStringValue($cms, 'highlights_heading', self::DEFAULT_HIGHLIGHTS_HEADING),
            galleryImages: self::buildGalleryImages($cms),
            gallerySectionHeading: ImageHelper::getStringValue($cms, 'gallery_heading', self::DEFAULT_GALLERY_HEADING),
            videoSectionHeading: ImageHelper::getStringValue($cms, 'video_heading', self::DEFAULT_VIDEO_HEADING),
            videoUrl: $cms['video_url'] ?? '',
            videoPlaceholderText: ImageHelper::getStringValue($cms, 'video_placeholder', self::DEFAULT_VIDEO_PLACEHOLDER),
            scheduleSection: ScheduleSectionViewModel::fromData($scheduleData),
        );
    }

    /**
     * @return string[]
     */
    private static function buildGalleryImages(array $cms): array
    {
        $images = [];
        for ($i = 1; $i <= self::GALLERY_IMAGE_COUNT; $i++) {
            $images[] = ImageHelper::validatePath((string)($cms["gallery_image_{$i}"] ?? ''));
        }
        return $images;
    }
}
