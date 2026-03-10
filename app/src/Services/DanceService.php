<?php

declare(strict_types=1);

namespace App\Services;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;
use App\ViewModels\Dance\DancePageViewModel;
use App\ViewModels\Dance\ExperienceData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\IntroSplitSectionData;

/**
 * Service for preparing Dance page data.
 *
 * Mirrors the structure of Jazz/Storytelling for the first two sections,
 * and adds a Festival Experience section.
 */
class DanceService
{
    private CmsService $cmsService;

    private const DEFAULT_IMAGE = '/assets/Image/Image (Dance).png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'heic'];

    public function __construct()
    {
        $this->cmsService = new CmsService();
    }

    public function getDancePageData(): DancePageViewModel
    {
        $heroData = new HeroData(
            mainTitle: 'DANCE! FESTIVAL 2025',
            subtitle: 'Haarlem · 3 Days · Music · Culture',
            primaryButtonText: 'Discover all events',
            primaryButtonLink: '/dance#headliners',
            secondaryButtonText: 'What is Haarlem DANCE! Festival?',
            secondaryButtonLink: '/dance#about',
            backgroundImageUrl: '/assets/Image/Image (Dance).png',
            currentPage: 'dance',
        );

        $globalUi = $this->cmsService->buildGlobalUiData();

        return new DancePageViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            gradientSection: $this->buildGradientSection(),
            introSplitSection: $this->buildIntroSplitSection(),
            experienceData: $this->buildExperienceSection(),
        );
    }

    private function buildGradientSection(): GradientSectionData
    {
        return new GradientSectionData(
            headingText: 'Every rhythm brings people together beyond the music',
            subheadingText: 'Experience dance, culture, and connection at DANCE! Festival.',
            backgroundImageUrl: '/assets/Image/dance/banner.jpg',
        );
    }

    private function buildIntroSplitSection(): IntroSplitSectionData
    {
        $content = $this->cmsService->getSectionContent('dance', 'intro_split_section');

        return new IntroSplitSectionData(
            headingText: $this->getStringValue($content, 'intro_heading', 'ENJOY THE HOTTEST TIME THIS SUMMER'),
            bodyText: $this->getStringValue(
                $content,
                'intro_body',
                'This summer, Haarlem becomes the heart of music, dance, and unforgettable energy. Our festival brings people together to celebrate movement, culture, and sound in one powerful experience. Whether you come for the beats, the atmosphere, or the memories, this is where your summer truly begins.

Expect high-energy performances, vibrant crowds, and an atmosphere filled with freedom, rhythm, and connection. Haarlem Festival Dance is not just an event; it’s a feeling you’ll carry with you long after the music stops.'
            ),
            imageUrl: $this->validateImagePath($content['intro_image'] ?? '/assets/Image/dance-crowd-stage.jpg'),
            imageAltText: $this->getStringValue($content, 'intro_image_alt', 'Dance festival crowd in front of the stage'),
        );
    }

    private function buildExperienceSection(): ExperienceData
    {
        $content = $this->cmsService->getSectionContent('dance', 'experience_section');

        // If CMS doesn't have images yet, repeat the default image (so the section still renders).
        $images = $this->getArrayValue($content, 'experience_images', []);
        if (count($images) === 0) {
            $images = [self::DEFAULT_IMAGE, self::DEFAULT_IMAGE, self::DEFAULT_IMAGE];
        }

        $validated = [];
        foreach ($images as $img) {
            $validated[] = $this->validateImagePath((string) $img);
        }

        return new ExperienceData(
            title: $this->getStringValue($content, 'experience_title', 'The Festival Experience'),
            imageUrls: $validated,
        );
    }

    private function getStringValue(array $data, string $key, string $default): string
    {
        $value = $data[$key] ?? $default;
        return is_string($value) ? $value : $default;
    }

    /**
     * @return array<int, mixed>
     */
    private function getArrayValue(array $data, string $key, array $default): array
    {
        $value = $data[$key] ?? $default;
        return is_array($value) ? $value : $default;
    }

    private function validateImagePath(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return self::DEFAULT_IMAGE;
        }

        // Allow URLs or local asset paths
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            $ext = strtolower(pathinfo(parse_url($path, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
            if ($ext === '' || in_array($ext, self::VALID_IMAGE_EXTENSIONS, true)) {
                return $path;
            }
        }

        return self::DEFAULT_IMAGE;
    }
}

