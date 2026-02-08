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
    private const MASONRY_TOTAL_IMAGES = 12;

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
            heroData: $this->cmsService->buildHeroData('storytelling', 'storytelling'),
            globalUi: $this->cmsService->buildGlobalUiData(),
            gradientSection: $this->buildGradientSection(),
            introSplitSection: $this->buildIntroSplitSection(),
            masonrySection: $this->buildMasonrySection(),
        );
    }

    /**
     * Builds the gradient section data with background image from CMS.
     */
    private function buildGradientSection(): GradientSectionData
    {
        $content = $this->cmsService->getSectionContent('storytelling', 'gradient_section');

        return new GradientSectionData(
            headingText: $this->getStringValue($content, 'gradient_heading', ''),
            subheadingText: $this->getStringValue($content, 'gradient_subheading', ''),
            backgroundImageUrl: $this->validateImagePath($content['gradient_background_image'] ?? ''),
        );
    }

    /**
     * Builds the intro split section data with image from CMS.
     */
    private function buildIntroSplitSection(): IntroSplitSectionData
    {
        $content = $this->cmsService->getSectionContent('storytelling', 'intro_split_section');
        $heading = $this->getStringValue($content, 'intro_heading', 'Stories in Haarlem');

        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: $this->getStringValue($content, 'intro_body', ''),
            imageUrl: $this->validateImagePath($content['intro_image'] ?? ''),
            imageAltText: $heading, // Use heading as alt text
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
     * Builds masonry images from CMS content.
     * Reads image paths from CMS masonry_image_01 through masonry_image_12.
     * Assigns varying size classes for true masonry effect.
     */
    private function buildShuffledMasonryImages(): array
    {
        $content = $this->cmsService->getSectionContent('storytelling', 'masonry_section');

        // Collect image paths from CMS (masonry_image_01 through masonry_image_12)
        $imagePaths = [];
        for ($i = 1; $i <= self::MASONRY_TOTAL_IMAGES; $i++) {
            $key = sprintf('masonry_image_%02d', $i);
            $path = $content[$key] ?? null;
            if (!empty($path)) {
                $imagePaths[] = $path;
            }
        }

        // If no images found in CMS, return empty array
        if (empty($imagePaths)) {
            return [];
        }

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
        foreach ($imagePaths as $index => $path) {
            $images[] = new MasonryImageData(
                imageUrl: $this->validateImagePath($path),
                altText: $this->generateAltText(basename($path)),
                sizeClass: $sizeClasses[$index] ?? 'masonry-medium',
            );
        }

        return $images;
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

