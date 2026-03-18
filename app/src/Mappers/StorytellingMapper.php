<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\StorytellingPageConstants;
use App\Helpers\CmsOutputHelper;
use App\Helpers\ImageHelper;
use App\Models\StorytellingDetailPageData;
use App\Models\StorytellingPageData;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;
use App\ViewModels\Storytelling\MasonryImageData;
use App\ViewModels\Storytelling\MasonrySectionData;
use App\ViewModels\Storytelling\StoryGallerySectionData;
use App\ViewModels\Storytelling\StoryHighlightData;
use App\ViewModels\Storytelling\StoryHighlightsSectionData;
use App\ViewModels\Storytelling\StorytellingAboutSectionData;
use App\ViewModels\Storytelling\StorytellingDetailHeroData;
use App\ViewModels\Storytelling\StorytellingDetailNavLinkData;
use App\ViewModels\Storytelling\StorytellingDetailPageViewModel;
use App\ViewModels\Storytelling\StorytellingPageViewModel;
use App\ViewModels\Storytelling\StoryVideoSectionData;

class StorytellingMapper
{
    public static function toPageViewModel(
        StorytellingPageData $pageData,
        HeroData $heroData,
        GlobalUiData $globalUi,
        ScheduleSectionViewModel $scheduleSection,
    ): StorytellingPageViewModel {
        $sections = $pageData->sections;

        return new StorytellingPageViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            cms: CmsMapper::toCmsData($heroData, $globalUi),
            gradientSection: self::buildGradientSection($sections),
            introSplitSection: self::buildIntroSplitSection($sections),
            masonrySection: self::buildMasonrySection($sections[StorytellingPageConstants::SECTION_MASONRY] ?? []),
            scheduleSection: $scheduleSection,
        );
    }

    public static function toDetailPageViewModel(
        StorytellingDetailPageData $pageData,
        GlobalUiData $globalUi,
        ScheduleSectionViewModel $scheduleSection,
    ): StorytellingDetailPageViewModel {
        $detailHero = self::buildDetailHero($pageData, $globalUi, $scheduleSection);
        $heroData = self::buildShellHero($detailHero);

        return new StorytellingDetailPageViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            cms: CmsMapper::toCmsData($heroData, $globalUi),
            detailHero: $detailHero,
            aboutSection: self::buildAboutSection($pageData),
            highlightsSection: self::buildHighlightsSection($pageData->cms),
            gallerySection: self::buildGallerySection($pageData->cms),
            videoSection: self::buildVideoSection($pageData->cms),
            scheduleSection: $scheduleSection,
        );
    }

    private static function buildGradientSection(array $sections): GradientSectionData
    {
        $section = $sections[StorytellingPageConstants::SECTION_GRADIENT] ?? [];
        return new GradientSectionData(
            headingText: ImageHelper::getStringValue($section, 'gradient_heading', 'Every story opens a new world.'),
            subheadingText: ImageHelper::getStringValue($section, 'gradient_subheading', 'Discover voices, moments, and memories in Haarlem.'),
            backgroundImageUrl: ImageHelper::validatePath((string)($section['gradient_background_image'] ?? '')),
        );
    }

    private static function buildIntroSplitSection(array $sections): IntroSplitSectionData
    {
        $section = $sections[StorytellingPageConstants::SECTION_INTRO_SPLIT] ?? [];
        $heading = ImageHelper::getStringValue($section, 'intro_heading', 'Stories in Haarlem');
        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: ImageHelper::getStringValue($section, 'intro_body', 'Storytelling sessions connect people through culture, humor, and lived experiences.'),
            imageUrl: ImageHelper::validatePath((string)($section['intro_image'] ?? '')),
            imageAltText: ImageHelper::getStringValue($section, 'intro_image_alt', $heading),
        );
    }

    private static function buildMasonrySection(array $content): MasonrySectionData
    {
        $images = [];
        $sizeClasses = ['masonry-tall', 'masonry-short', 'masonry-medium', 'masonry-medium', 'masonry-tall', 'masonry-short', 'masonry-short', 'masonry-medium', 'masonry-tall', 'masonry-medium', 'masonry-short', 'masonry-tall'];
        for ($i = 1; $i <= 12; $i++) {
            $key = sprintf('masonry_image_%02d', $i);
            $path = (string)($content[$key] ?? '');
            $validPath = ImageHelper::validatePath($path);
            $altText = ImageHelper::altTextFromFilename(basename($path), 'Storytelling moment');
            $sizeClass = $sizeClasses[$i - 1] ?? 'masonry-medium';
            $images[] = new MasonryImageData(imageUrl: $validPath, altText: $altText, sizeClass: $sizeClass);
        }
        return new MasonrySectionData(
            headingText: ImageHelper::getStringValue($content, 'masonry_heading', 'Captured storytelling moments'),
            images: $images,
        );
    }

    private static function buildDetailHero(StorytellingDetailPageData $pageData, GlobalUiData $globalUi, ScheduleSectionViewModel $scheduleSection): StorytellingDetailHeroData
    {
        $navLinks = [];
        $navLinks[] = new StorytellingDetailNavLinkData('/', $globalUi->navHome, false);
        $navLinks[] = new StorytellingDetailNavLinkData('/jazz', $globalUi->navJazz, false);
        $navLinks[] = new StorytellingDetailNavLinkData('/dance', $globalUi->navDance, false);
        $navLinks[] = new StorytellingDetailNavLinkData('/history', $globalUi->navHistory, false);
        $navLinks[] = new StorytellingDetailNavLinkData('/restaurant', $globalUi->navRestaurant, false);
        $navLinks[] = new StorytellingDetailNavLinkData('/storytelling', $globalUi->navStorytelling, StorytellingPageConstants::CURRENT_PAGE === 'storytelling');

        return new StorytellingDetailHeroData(
            title: $pageData->event->title,
            subtitle: $pageData->event->shortDescription,
            heroImageUrl: ImageHelper::validatePath($pageData->featuredImagePath ?? ''),
            labels: $pageData->labels,
            navLinks: $navLinks,
            backButtonLabel: ImageHelper::getStringValue($pageData->cms, 'back_button_label', 'Back to storytelling'),
            backButtonUrl: '/storytelling',
            reserveButtonLabel: ImageHelper::getStringValue($pageData->cms, 'reserve_button_label', 'Reserve your spot'),
            reserveButtonUrl: '#' . $scheduleSection->sectionId,
        );
    }

    private static function buildAboutSection(StorytellingDetailPageData $pageData): StorytellingAboutSectionData
    {
        return new StorytellingAboutSectionData(
            heading: ImageHelper::getStringValue($pageData->cms, 'about_heading', $pageData->event->title),
            bodyHtml: CmsOutputHelper::html($pageData->aboutBody),
            image1Url: ImageHelper::validatePath((string)($pageData->cms['about_image_1'] ?? '')),
            image2Url: ImageHelper::validatePath((string)($pageData->cms['about_image_2'] ?? '')),
        );
    }

    private static function buildHighlightsSection(array $cms): StoryHighlightsSectionData
    {
        $items = [];
        for ($i = 1; $i <= 3; $i++) {
            $title = $cms["highlight_{$i}_title"] ?? '';
            if (empty($title)) {
                continue;
            }
            $items[] = new StoryHighlightData(
                imageUrl: ImageHelper::validatePath((string)($cms["highlight_{$i}_image"] ?? '')),
                title: $title,
                description: $cms["highlight_{$i}_description"] ?? '',
            );
        }
        return new StoryHighlightsSectionData(
            heading: ImageHelper::getStringValue($cms, 'highlights_heading', 'Story highlights'),
            items: $items,
        );
    }

    private static function buildGallerySection(array $cms): StoryGallerySectionData
    {
        $images = [];
        for ($i = 1; $i <= 5; $i++) {
            $images[] = ImageHelper::validatePath((string)($cms["gallery_image_{$i}"] ?? ''));
        }
        return new StoryGallerySectionData(
            heading: ImageHelper::getStringValue($cms, 'gallery_heading', 'Where stories come alive'),
            topRowImages: array_slice($images, 0, 3),
            bottomRowImages: array_slice($images, 3, 2),
        );
    }

    private static function buildVideoSection(array $cms): StoryVideoSectionData
    {
        return new StoryVideoSectionData(
            heading: ImageHelper::getStringValue($cms, 'video_heading', 'A moment from the show'),
            url: (string)($cms['video_url'] ?? ''),
            placeholderText: ImageHelper::getStringValue($cms, 'video_placeholder', 'Video coming soon'),
        );
    }

    private static function buildShellHero(StorytellingDetailHeroData $detailHero): HeroData
    {
        return new HeroData(
            mainTitle: $detailHero->title,
            subtitle: $detailHero->subtitle,
            primaryButtonText: '',
            primaryButtonLink: '#',
            secondaryButtonText: '',
            secondaryButtonLink: '#',
            backgroundImageUrl: $detailHero->heroImageUrl,
            currentPage: StorytellingPageConstants::CURRENT_PAGE,
        );
    }
}
