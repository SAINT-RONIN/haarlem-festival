<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Interfaces\IStorytellingService;
use App\ViewModels\GradientSectionData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\MasonryImageData;
use App\ViewModels\MasonrySectionData;
use App\ViewModels\StorytellingPageViewModel;

/**
 * Service for preparing storytelling page data.
 *
 * Assembles all data needed for the storytelling page view.
 */
class StorytellingService implements IStorytellingService
{
    private CmsService $cmsService;

    private const DEFAULT_IMAGE = '/assets/Image/Image (Story).png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'heic'];

    // Masonry layout constants
    private const MASONRY_COLUMNS = 4;
    private const MASONRY_IMAGES_PER_COLUMN = 3;

    // Deterministic shuffle seed for stable ordering
    private const MASONRY_SHUFFLE_SEED = 'storytelling-masonry-v1';

    // Hardcoded image paths for storytelling page sections
    private const HERO_IMAGE = '/assets/Image/storytelling/hero-storytelling.jpg';
    private const GRADIENT_BG_IMAGE = '/assets/Image/storytelling/picture-looking-text.jpg';
    private const INTRO_IMAGE = '/assets/Image/storytelling/where-stories-come-alive.jpg';

    // All 12 masonry images (filenames without base path)
    private const MASONRY_IMAGE_FILES = [
        'd-student.jpg',
        'd-student2.jpg',
        'm-student.jpg',
        'winnie-the-pooh.jpg',
        'pig.jpg',
        'entrance-kweek.jpg',
        'building.jpg',
        'anansi-pointing.png',
        'anansi-conversation.jpg',
        'anansi-drip.jpg',
        'anansi-visser.jpg',
        'WinnieThePoohHeader.png',
    ];

    public function __construct()
    {
        $this->cmsService = new CmsService();
    }

    /**
     * Builds the storytelling page view model with all required data.
     */
    public function getStorytellingPageData(): StorytellingPageViewModel
    {
        return new StorytellingPageViewModel(
            heroData: $this->cmsService->buildHeroDataWithImage(
                'storytelling',
                'storytelling',
                self::HERO_IMAGE
            ),
            globalUi: $this->cmsService->buildGlobalUiData(),
            gradientSection: $this->buildGradientSection(),
            introSplitSection: $this->buildIntroSplitSection(),
            masonrySection: $this->buildMasonrySection(),
        );
    }

    /**
     * Builds the gradient section data with background image.
     */
    private function buildGradientSection(): GradientSectionData
    {
        $content = $this->cmsService->getSectionContent('storytelling', 'gradient_section');

        return new GradientSectionData(
            headingText: $this->getStringValue($content, 'gradient_heading', ''),
            subheadingText: $this->getStringValue($content, 'gradient_subheading', ''),
            backgroundImageUrl: self::GRADIENT_BG_IMAGE,
        );
    }

    /**
     * Builds the intro split section data with image.
     */
    private function buildIntroSplitSection(): IntroSplitSectionData
    {
        $content = $this->cmsService->getSectionContent('storytelling', 'intro_split_section');

        return new IntroSplitSectionData(
            headingText: $this->getStringValue($content, 'intro_heading', ''),
            bodyText: $this->getStringValue($content, 'intro_body', ''),
            imageUrl: self::INTRO_IMAGE,
        );
    }

    /**
     * Builds the masonry section data with exactly 4 columns × 3 images.
     * Uses deterministic shuffle for stable ordering.
     */
    private function buildMasonrySection(): MasonrySectionData
    {
        $content = $this->cmsService->getSectionContent('storytelling', 'masonry_section');

        return new MasonrySectionData(
            headingText: $this->getStringValue($content, 'masonry_heading', ''),
            columns: $this->buildMasonryColumns(),
        );
    }

    /**
     * Builds exactly 4 columns with 3 images each.
     * Applies deterministic shuffle to the image list for visual variety.
     */
    private function buildMasonryColumns(): array
    {
        $images = $this->buildShuffledMasonryImages();

        // Distribute into 4 columns × 3 images
        $columns = [];
        $index = 0;

        for ($col = 0; $col < self::MASONRY_COLUMNS; $col++) {
            $columns[$col] = [];
            for ($row = 0; $row < self::MASONRY_IMAGES_PER_COLUMN; $row++) {
                $columns[$col][] = $images[$index] ?? $this->createPlaceholderImage();
                $index++;
            }
        }

        return $columns;
    }

    /**
     * Builds masonry images with deterministic shuffle.
     * Sort filenames first, then apply fixed shuffle based on seed.
     * Assigns varying size classes for true masonry effect.
     */
    private function buildShuffledMasonryImages(): array
    {
        $basePath = '/assets/Image/storytelling/';

        // Sort filenames for consistent starting order
        $files = self::MASONRY_IMAGE_FILES;
        sort($files);

        // Apply deterministic shuffle using seed
        $files = $this->deterministicShuffle($files, self::MASONRY_SHUFFLE_SEED);

        // Size class pattern for varied heights (repeating pattern)
        $sizeClasses = [
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

        // Build image DTOs with size classes
        $images = [];
        foreach ($files as $index => $filename) {
            $path = $basePath . $filename;
            $images[] = new MasonryImageData(
                imageUrl: $this->validateImagePath($path),
                altText: $this->generateAltText($filename),
                sizeClass: $sizeClasses[$index] ?? 'masonry-medium',
            );
        }

        return $images;
    }

    /**
     * Applies a deterministic shuffle to an array using a seed string.
     * Same seed always produces the same shuffle result.
     */
    private function deterministicShuffle(array $items, string $seed): array
    {
        // Generate numeric seed from string
        $numericSeed = crc32($seed);

        // Fisher-Yates shuffle with seeded random
        mt_srand($numericSeed);

        $count = count($items);
        for ($i = $count - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
        }

        // Reset random seed to avoid affecting other code
        mt_srand();

        return $items;
    }

    /**
     * Generates alt text from filename.
     */
    private function generateAltText(string $filename): string
    {
        // Remove extension and convert to readable text
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace(['-', '_'], ' ', $name);
        return ucfirst($name) . ' - Storytelling moment';
    }

    /**
     * Creates a placeholder image for empty slots.
     */
    private function createPlaceholderImage(): MasonryImageData
    {
        return new MasonryImageData(
            imageUrl: self::DEFAULT_IMAGE,
            altText: 'Storytelling moment',
            sizeClass: 'masonry-medium',
        );
    }


    /**
     * Validates an image path. Returns default if invalid.
     */
    private function validateImagePath(string $path): string
    {
        if (empty($path)) {
            return self::DEFAULT_IMAGE;
        }

        if (!str_starts_with($path, '/assets/')) {
            return self::DEFAULT_IMAGE;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::VALID_IMAGE_EXTENSIONS, true)) {
            return self::DEFAULT_IMAGE;
        }

        return $path;
    }

    /**
     * Gets a string value from content array with default fallback.
     */
    private function getStringValue(array $content, string $key, string $default): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }
}

