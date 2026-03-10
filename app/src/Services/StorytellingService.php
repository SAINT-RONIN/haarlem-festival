<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EventTypeId;
use App\Repositories\EventRepository;
use App\Repositories\EventSessionLabelRepository;
use App\Repositories\EventSessionRepository;
use App\Repositories\MediaAssetRepository;
use App\Services\Interfaces\IStorytellingService;
use App\ViewModels\GradientSectionData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;
use App\ViewModels\Storytelling\MasonryImageData;
use App\ViewModels\Storytelling\MasonrySectionData;
use App\ViewModels\Storytelling\StoryHighlightData;
use App\ViewModels\Storytelling\StorytellingDetailPageViewModel;
use App\ViewModels\Storytelling\StorytellingPageViewModel;

/**
 * Service for preparing storytelling page data.
 */
class StorytellingService implements IStorytellingService
{
    private const PAGE_SLUG = 'storytelling';

    private const SECTION_GRADIENT = 'gradient_section';
    private const SECTION_INTRO_SPLIT = 'intro_split_section';
    private const SECTION_MASONRY = 'masonry_section';

    private const DEFAULT_IMAGE_PATH = '/assets/Image/Image (Story).png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'heic'];

    private const DEFAULT_GRADIENT_HEADING = 'Every story opens a new world.';
    private const DEFAULT_GRADIENT_SUBHEADING = 'Discover voices, moments, and memories in Haarlem.';
    private const DEFAULT_INTRO_HEADING = 'Stories in Haarlem';
    private const DEFAULT_INTRO_BODY = 'Storytelling sessions connect people through culture, humor, and lived experiences.';
    private const DEFAULT_MASONRY_HEADING = 'Captured storytelling moments';
    private const DEFAULT_IMAGE_ALT_TEXT = 'Storytelling moment';

    private const DEFAULT_BACK_BUTTON_LABEL = 'Back to storytelling';
    private const DEFAULT_RESERVE_BUTTON_LABEL = 'Reserve your spot';
    private const DEFAULT_HIGHLIGHTS_HEADING = 'Story highlights';
    private const DEFAULT_GALLERY_HEADING = 'Where stories come alive';
    private const DEFAULT_VIDEO_HEADING = 'A moment from the show';
    private const DEFAULT_VIDEO_PLACEHOLDER = 'Video coming soon';

    private const MASONRY_COLUMNS = 4;
    private const MASONRY_IMAGES_PER_COLUMN = 3;
    private const MASONRY_TOTAL_IMAGES = 12;
    private const MASONRY_IMAGE_KEY_PATTERN = 'masonry_image_%02d';
    private const MASONRY_DEFAULT_SIZE_CLASS = 'masonry-medium';
    private const MASONRY_SIZE_CLASSES = [
        'masonry-tall',
        'masonry-short',
        'masonry-medium',
        'masonry-medium',
        'masonry-tall',
        'masonry-short',
        'masonry-short',
        'masonry-medium',
        'masonry-tall',
        'masonry-medium',
        'masonry-short',
        'masonry-tall',
    ];

    private const SCHEDULE_MAX_DAYS = 7;

    private CmsService $cmsService;
    private ScheduleService $scheduleService;
    private EventRepository $eventRepository;
    private EventSessionRepository $sessionRepository;
    private EventSessionLabelRepository $labelRepository;
    private MediaAssetRepository $mediaAssetRepository;

    public function __construct()
    {
        $this->cmsService = new CmsService();
        $this->scheduleService = new ScheduleService();
        $this->eventRepository = new EventRepository();
        $this->sessionRepository = new EventSessionRepository();
        $this->labelRepository = new EventSessionLabelRepository();
        $this->mediaAssetRepository = new MediaAssetRepository();
    }

    /**
     * Builds the complete page ViewModel consumed by the storytelling view.
     */
    public function getStorytellingPageData(): StorytellingPageViewModel
    {
        return new StorytellingPageViewModel(
            heroData: $this->cmsService->buildHeroData(self::PAGE_SLUG, self::PAGE_SLUG),
            globalUi: $this->cmsService->buildGlobalUiData(),
            gradientSection: $this->buildGradientSection(),
            introSplitSection: $this->buildIntroSplitSection(),
            masonrySection: $this->buildMasonrySection(),
            scheduleSection: $this->buildScheduleSection(),
        );
    }

    /**
     * Builds the detail page ViewModel for a single storytelling event.
     *
     * @throws \RuntimeException if the event is not found or not a storytelling event
     */
    public function getStorytellingDetailPageData(int $eventId): StorytellingDetailPageViewModel
    {
        $event = $this->findStorytellingEvent($eventId);
        $cms = $this->getDetailSectionContent($eventId);

        return new StorytellingDetailPageViewModel(
            globalUi: $this->cmsService->buildGlobalUiData(),
            eventId: $eventId,
            title: $event['Title'],
            subtitle: $event['ShortDescription'] ?? '',
            heroImageUrl: $this->resolveHeroImage($event),
            labels: $this->buildEventLabels($eventId),
            backButtonLabel: $this->getStringValue($cms, 'back_button_label', self::DEFAULT_BACK_BUTTON_LABEL),
            reserveButtonLabel: $this->getStringValue($cms, 'reserve_button_label', self::DEFAULT_RESERVE_BUTTON_LABEL),
            aboutHeading: $this->getStringValue($cms, 'about_heading', $event['Title']),
            aboutBodyHtml: $this->resolveAboutBody($cms, $event),
            aboutImage1Url: $this->validateImagePath((string)($cms['about_image_1'] ?? '')),
            aboutImage2Url: $this->validateImagePath((string)($cms['about_image_2'] ?? '')),
            highlights: $this->buildHighlights($cms),
            highlightsSectionHeading: $this->getStringValue($cms, 'highlights_heading', self::DEFAULT_HIGHLIGHTS_HEADING),
            galleryImages: $this->buildGalleryImages($cms),
            gallerySectionHeading: $this->getStringValue($cms, 'gallery_heading', self::DEFAULT_GALLERY_HEADING),
            videoSectionHeading: $this->getStringValue($cms, 'video_heading', self::DEFAULT_VIDEO_HEADING),
            videoUrl: $cms['video_url'] ?? '',
            videoPlaceholderText: $this->getStringValue($cms, 'video_placeholder', self::DEFAULT_VIDEO_PLACEHOLDER),
            scheduleSection: $this->buildScheduleSection(),
        );
    }

    /**
     * @throws \RuntimeException if the event does not exist or is not a storytelling event
     */
    private function findStorytellingEvent(int $eventId): array
    {
        $events = $this->eventRepository->findEvents(['eventId' => $eventId]);
        $event = $events[0] ?? null;

        if (!$event || (int)$event['EventTypeId'] !== EventTypeId::Storytelling->value) {
            throw new \RuntimeException("Storytelling event {$eventId} not found.");
        }

        return $event;
    }

    private function getDetailSectionContent(int $eventId): array
    {
        return $this->cmsService->getSectionContent('storytelling-detail', 'event_' . $eventId);
    }

    private function resolveHeroImage(array $event): string
    {
        if (!empty($event['FeaturedImageAssetId'])) {
            $asset = $this->mediaAssetRepository->findById((int)$event['FeaturedImageAssetId']);
            if ($asset) {
                return $this->validateImagePath($asset->filePath);
            }
        }
        return self::DEFAULT_IMAGE_PATH;
    }

    private function resolveAboutBody(array $cms, array $event): string
    {
        if (!empty($cms['about_body'])) {
            return $cms['about_body'];
        }
        return $event['LongDescriptionHtml'] ?? ($event['ShortDescription'] ?? '');
    }

    /**
     * @return StoryHighlightData[]
     */
    private function buildHighlights(array $cms): array
    {
        $highlights = [];
        for ($i = 1; $i <= 3; $i++) {
            $title = $cms["highlight_{$i}_title"] ?? '';
            if (empty($title)) {
                continue;
            }
            $highlights[] = new StoryHighlightData(
                imageUrl: $this->validateImagePath((string)($cms["highlight_{$i}_image"] ?? '')),
                title: $title,
                description: $cms["highlight_{$i}_description"] ?? '',
            );
        }
        return $highlights;
    }

    /**
     * @return string[]
     */
    private function buildGalleryImages(array $cms): array
    {
        $images = [];
        for ($i = 1; $i <= 5; $i++) {
            $images[] = $this->validateImagePath((string)($cms["gallery_image_{$i}"] ?? ''));
        }
        return $images;
    }

    /**
     * @return string[]
     */
    private function buildEventLabels(int $eventId): array
    {
        $sessions = $this->sessionRepository->findSessions([
            'eventId' => $eventId,
            'isActive' => true,
        ]);
        $sessionList = $sessions['sessions'] ?? [];

        if (empty($sessionList)) {
            return [];
        }

        $sessionId = (int)$sessionList[0]['EventSessionId'];
        $labelsMap = $this->labelRepository->findLabels([
            'sessionIds' => [$sessionId],
            'groupBySession' => true,
        ]);

        return array_map(fn ($l) => $l->labelText, $labelsMap[$sessionId] ?? []);
    }

    /**
     * Builds the gradient section from CMS content with sensible fallbacks.
     */
    private function buildGradientSection(): GradientSectionData
    {
        $content = $this->getSectionContent(self::SECTION_GRADIENT);

        return new GradientSectionData(
            headingText: $this->getStringValue(
                $content,
                'gradient_heading',
                self::DEFAULT_GRADIENT_HEADING
            ),
            subheadingText: $this->getStringValue(
                $content,
                'gradient_subheading',
                self::DEFAULT_GRADIENT_SUBHEADING
            ),
            backgroundImageUrl: $this->validateImagePath((string)($content['gradient_background_image'] ?? '')),
        );
    }

    /**
     * Builds the intro split section from CMS content with safe defaults.
     */
    private function buildIntroSplitSection(): IntroSplitSectionData
    {
        $content = $this->getSectionContent(self::SECTION_INTRO_SPLIT);
        $heading = $this->getStringValue($content, 'intro_heading', self::DEFAULT_INTRO_HEADING);

        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: $this->getStringValue($content, 'intro_body', self::DEFAULT_INTRO_BODY),
            imageUrl: $this->validateImagePath((string)($content['intro_image'] ?? '')),
            imageAltText: $this->getStringValue($content, 'intro_image_alt', $heading),
        );
    }

    /**
     * Builds the storytelling masonry section and image columns.
     */
    private function buildMasonrySection(): MasonrySectionData
    {
        $content = $this->getSectionContent(self::SECTION_MASONRY);

        return new MasonrySectionData(
            headingText: $this->getStringValue($content, 'masonry_heading', self::DEFAULT_MASONRY_HEADING),
            columns: $this->buildMasonryColumns($content),
        );
    }

    /**
     * Distributes masonry images into a fixed grid of columns and rows.
     *
     * @return array<int, array<int, MasonryImageData>>
     */
    private function buildMasonryColumns(array $content): array
    {
        $images = $this->buildMasonryImages($content);
        $columns = [];
        $imageIndex = 0;

        for ($columnIndex = 0; $columnIndex < self::MASONRY_COLUMNS; $columnIndex++) {
            $columns[$columnIndex] = [];

            for ($rowIndex = 0; $rowIndex < self::MASONRY_IMAGES_PER_COLUMN; $rowIndex++) {
                $columns[$columnIndex][] = $images[$imageIndex] ?? $this->createPlaceholderImage();
                $imageIndex++;
            }
        }

        return $columns;
    }

    /**
     * Converts masonry image paths into ViewModels with predefined size classes.
     *
     * @return array<int, MasonryImageData>
     */
    private function buildMasonryImages(array $content): array
    {
        $imagePaths = $this->collectMasonryImagePaths($content);
        if ($imagePaths === []) {
            return [];
        }

        $images = [];
        foreach ($imagePaths as $index => $path) {
            $images[] = new MasonryImageData(
                imageUrl: $this->validateImagePath($path),
                altText: $this->generateAltText(basename($path)),
                sizeClass: self::MASONRY_SIZE_CLASSES[$index] ?? self::MASONRY_DEFAULT_SIZE_CLASS,
            );
        }

        return $images;
    }

    /**
     * Collects non-empty masonry image paths from the CMS section.
     *
     * @return list<string>
     */
    private function collectMasonryImagePaths(array $content): array
    {
        $paths = [];

        for ($index = 1; $index <= self::MASONRY_TOTAL_IMAGES; $index++) {
            $key = sprintf(self::MASONRY_IMAGE_KEY_PATTERN, $index);
            $path = $content[$key] ?? null;

            if (is_string($path) && $path !== '') {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * Builds the shared schedule section for storytelling events.
     */
    private function buildScheduleSection(): ScheduleSectionViewModel
    {
        return $this->scheduleService->buildScheduleSection(
            pageSlug: self::PAGE_SLUG,
            eventTypeId: EventTypeId::Storytelling->value,
            maxDays: self::SCHEDULE_MAX_DAYS,
        );
    }

    /**
     * Fetches one storytelling CMS section by key.
     */
    private function getSectionContent(string $sectionKey): array
    {
        return $this->cmsService->getSectionContent(self::PAGE_SLUG, $sectionKey);
    }

    /**
     * Generates a readable alt text from an image filename.
     */
    private function generateAltText(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace(['-', '_'], ' ', $name);

        return ucfirst($name) . ' - ' . self::DEFAULT_IMAGE_ALT_TEXT;
    }

    /**
     * Creates a fallback masonry image when CMS content is missing.
     */
    private function createPlaceholderImage(): MasonryImageData
    {
        return new MasonryImageData(
            imageUrl: self::DEFAULT_IMAGE_PATH,
            altText: self::DEFAULT_IMAGE_ALT_TEXT,
            sizeClass: self::MASONRY_DEFAULT_SIZE_CLASS,
        );
    }

    /**
     * Validates an image path and returns a safe fallback when invalid.
     */
    private function validateImagePath(string $path): string
    {
        if ($path === '') {
            return self::DEFAULT_IMAGE_PATH;
        }

        if (!str_starts_with($path, '/assets/')) {
            return self::DEFAULT_IMAGE_PATH;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::VALID_IMAGE_EXTENSIONS, true)) {
            return self::DEFAULT_IMAGE_PATH;
        }

        return $path;
    }

    /**
     * Returns a non-empty string value from CMS content or a default value.
     */
    private function getStringValue(array $content, string $key, string $default): string
    {
        $value = $content[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : $default;
    }
}
