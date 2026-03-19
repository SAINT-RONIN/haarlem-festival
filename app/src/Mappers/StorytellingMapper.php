<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\StorytellingPageConstants;
use App\Helpers\CmsOutputHelper;
use App\Helpers\ImageHelper;
use App\Models\EventGalleryImage;
use App\Models\EventHighlight;
use App\Models\PageGalleryImage;
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
    private const ROUTE_HOME = '/';
    private const ROUTE_JAZZ = '/jazz';
    private const ROUTE_DANCE = '/dance';
    private const ROUTE_HISTORY = '/history';
    private const ROUTE_RESTAURANT = '/restaurant';
    private const ROUTE_STORYTELLING = '/storytelling';

    private const MASONRY_IMAGE_COUNT = 12;

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

    /**
     * Converts the storytelling page domain payload into a fully display-ready ViewModel.
     * The reason for this is because the mapper is the only layer allowed to format raw model data into strings and typed ViewModels the view can consume directly.
     */
    public static function toPageViewModel(
        StorytellingPageData $pageData,
        ScheduleSectionViewModel $scheduleSection,
        bool $isLoggedIn,
    ): StorytellingPageViewModel {
        $heroContent = $pageData->sections[StorytellingPageConstants::SECTION_HERO] ?? [];
        $heroData = CmsMapper::toHeroData($heroContent, StorytellingPageConstants::CURRENT_PAGE);
        $globalUi = CmsMapper::toGlobalUiData($pageData->globalUiContent, $isLoggedIn);
        $sections = $pageData->sections;

        return new StorytellingPageViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            cms: CmsMapper::toCmsData($heroData, $globalUi),
            gradientSection: self::buildGradientSection($sections),
            introSplitSection: self::buildIntroSplitSection($sections),
            masonrySection: self::buildMasonrySection(
                $sections[StorytellingPageConstants::SECTION_MASONRY] ?? [],
                $pageData->masonryImages,
            ),
            scheduleSection: $scheduleSection,
        );
    }

    /**
     * Converts the storytelling detail page domain payload into a display-ready ViewModel.
     * The reason for this is because the detail page has more sections than the listing page, each requiring its own sub-ViewModel built from CMS and event data.
     */
    public static function toDetailPageViewModel(
        StorytellingDetailPageData $pageData,
        ScheduleSectionViewModel $scheduleSection,
        bool $isLoggedIn,
    ): StorytellingDetailPageViewModel {
        $globalUi = CmsMapper::toGlobalUiData($pageData->globalUiContent, $isLoggedIn);
        $detailHero = self::buildDetailHero($pageData, $globalUi, $scheduleSection);
        $heroData = self::buildShellHero($detailHero);

        return new StorytellingDetailPageViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            cms: CmsMapper::toCmsData($heroData, $globalUi),
            detailHero: $detailHero,
            aboutSection: self::buildAboutSection($pageData),
            highlightsSection: self::buildHighlightsSection($pageData->cms, $pageData->highlights),
            gallerySection: self::buildGallerySection($pageData->cms, $pageData->galleryImages),
            videoSection: self::buildVideoSection($pageData->cms),
            scheduleSection: $scheduleSection,
        );
    }

    /**
     * Builds the gradient banner section ViewModel from raw CMS content.
     * The reason for this is because the gradient section needs its own ViewModel shape and the mapping is extracted here to keep toPageViewModel under 10 lines.
     */
    private static function buildGradientSection(array $sections): GradientSectionData
    {
        $section = $sections[StorytellingPageConstants::SECTION_GRADIENT] ?? [];
        return new GradientSectionData(
            headingText: ImageHelper::getStringValue($section, 'gradient_heading', ''),
            subheadingText: ImageHelper::getStringValue($section, 'gradient_subheading', ''),
            backgroundImageUrl: ImageHelper::validatePath((string)($section['gradient_background_image'] ?? '')),
        );
    }

    /**
     * Builds the two-column intro split section ViewModel from raw CMS content.
     * The reason for this is because the intro split section has its own image and text fields that must be validated and assembled into a typed ViewModel before reaching the view.
     */
    private static function buildIntroSplitSection(array $sections): IntroSplitSectionData
    {
        $section = $sections[StorytellingPageConstants::SECTION_INTRO_SPLIT] ?? [];
        $heading = ImageHelper::getStringValue($section, 'intro_heading', '');
        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: ImageHelper::getStringValue($section, 'intro_body', ''),
            imageUrl: ImageHelper::validatePath((string)($section['intro_image'] ?? '')),
            imageAltText: ImageHelper::getStringValue($section, 'intro_image_alt', $heading),
        );
    }

    /**
     * Builds the masonry photo grid section from relational images and CMS heading.
     * The reason for this is because the masonry layout assigns a predetermined size class per position so the CSS grid renders correctly.
     *
     * @param PageGalleryImage[] $masonryImages
     */
    private static function buildMasonrySection(array $content, array $masonryImages): MasonrySectionData
    {
        $images = [];

        if ($masonryImages !== []) {
            foreach ($masonryImages as $index => $img) {
                $path = $img->imagePath;
                $validPath = ImageHelper::validatePath($path);
                $altText = ImageHelper::altTextFromFilename(basename($path), 'Storytelling moment');
                $sizeClass = self::MASONRY_SIZE_CLASSES[$index] ?? 'masonry-medium';
                $images[] = new MasonryImageData(imageUrl: $validPath, altText: $altText, sizeClass: $sizeClass);
            }
        } else {
            for ($i = 1; $i <= self::MASONRY_IMAGE_COUNT; $i++) {
                $key = sprintf('masonry_image_%02d', $i);
                $path = (string)($content[$key] ?? '');
                $validPath = ImageHelper::validatePath($path);
                $altText = ImageHelper::altTextFromFilename(basename($path), 'Storytelling moment');
                $sizeClass = self::MASONRY_SIZE_CLASSES[$i - 1] ?? 'masonry-medium';
                $images[] = new MasonryImageData(imageUrl: $validPath, altText: $altText, sizeClass: $sizeClass);
            }
        }

        return new MasonrySectionData(
            headingText: ImageHelper::getStringValue($content, 'masonry_heading', ''),
            images: $images,
        );
    }

    /**
     * Builds the ordered list of nav links for the detail page hero overlay.
     * The reason for this is because the detail hero renders its own inline nav that must mark the current page as active, which requires the mapper to set the active flag rather than the view.
     */
    private static function buildDetailNavLinks(GlobalUiData $globalUi): array
    {
        return [
            new StorytellingDetailNavLinkData(self::ROUTE_HOME, $globalUi->navHome, false),
            new StorytellingDetailNavLinkData(self::ROUTE_JAZZ, $globalUi->navJazz, false),
            new StorytellingDetailNavLinkData(self::ROUTE_DANCE, $globalUi->navDance, false),
            new StorytellingDetailNavLinkData(self::ROUTE_HISTORY, $globalUi->navHistory, false),
            new StorytellingDetailNavLinkData(self::ROUTE_RESTAURANT, $globalUi->navRestaurant, false),
            new StorytellingDetailNavLinkData(self::ROUTE_STORYTELLING, $globalUi->navStorytelling, StorytellingPageConstants::CURRENT_PAGE === 'storytelling'),
        ];
    }

    /**
     * Builds the custom hero ViewModel specific to the storytelling detail page.
     * The reason for this is because the detail hero differs from the standard hero — it includes nav links, labels, and a reserve button that anchors to the schedule section.
     */
    private static function buildDetailHero(StorytellingDetailPageData $pageData, GlobalUiData $globalUi, ScheduleSectionViewModel $scheduleSection): StorytellingDetailHeroData
    {
        $buttons = self::buildDetailHeroButtons($pageData->cms, $scheduleSection);

        return new StorytellingDetailHeroData(
            title: $pageData->event->title,
            subtitle: $pageData->event->shortDescription,
            heroImageUrl: self::resolveDetailHeroImage($pageData),
            labels: $pageData->labels,
            navLinks: self::buildDetailNavLinks($globalUi),
            backButtonLabel: $buttons['backButtonLabel'],
            backButtonUrl: $buttons['backButtonUrl'],
            reserveButtonLabel: $buttons['reserveButtonLabel'],
            reserveButtonUrl: $buttons['reserveButtonUrl'],
        );
    }

    /**
     * Assembles the four button fields (labels and URLs) for the detail page hero.
     * The reason for this is because extracting these four CMS lookups into their own method keeps buildDetailHero() under 10 lines while grouping the button fields that belong together logically.
     */
    private static function buildDetailHeroButtons(array $cms, ScheduleSectionViewModel $scheduleSection): array
    {
        return [
            'backButtonLabel'    => ImageHelper::getStringValue($cms, 'back_button_label', ''),
            'backButtonUrl'      => self::ROUTE_STORYTELLING,
            'reserveButtonLabel' => ImageHelper::getStringValue($cms, 'reserve_button_label', ''),
            'reserveButtonUrl'   => '#' . $scheduleSection->sectionId,
        ];
    }

    /**
     * Returns the hero image URL, preferring the CMS hero_image over the event's featured image.
     * The reason for this is because editors must be able to override the hero per event without changing the event record itself.
     */
    private static function resolveDetailHeroImage(StorytellingDetailPageData $pageData): string
    {
        $cmsHeroImage = (string)($pageData->cms['hero_image'] ?? '');
        if ($cmsHeroImage !== '') {
            return ImageHelper::validatePath($cmsHeroImage);
        }

        return ImageHelper::validatePath($pageData->featuredImagePath ?? '');
    }

    /**
     * Builds the about section ViewModel combining the CMS heading and the pre-resolved body text.
     * The reason for this is because the body text fallback logic runs in the service, so by mapping time the aboutBody field is already the correct string to display.
     */
    private static function buildAboutSection(StorytellingDetailPageData $pageData): StorytellingAboutSectionData
    {
        if ($pageData->aboutImages !== []) {
            $image1 = ($pageData->aboutImages[0] ?? null)?->imagePath ?? '';
            $image2 = ($pageData->aboutImages[1] ?? null)?->imagePath ?? '';
        } else {
            $image1 = (string)($pageData->cms['about_image_1'] ?? '');
            $image2 = (string)($pageData->cms['about_image_2'] ?? '');
        }

        return new StorytellingAboutSectionData(
            heading: ImageHelper::getStringValue($pageData->cms, 'about_heading', $pageData->event->title),
            bodyHtml: CmsOutputHelper::html($pageData->aboutBody),
            image1Url: ImageHelper::validatePath($image1),
            image2Url: ImageHelper::validatePath($image2),
        );
    }

    /**
     * Builds the highlights section ViewModel from relational EventHighlight rows.
     * The reason for this is because highlights are now stored in their own table; rows with an empty title are silently skipped rather than rendered as blank cards.
     *
     * @param EventHighlight[] $highlights
     */
    private static function buildHighlightsSection(array $cms, array $highlights): StoryHighlightsSectionData
    {
        $items = [];

        if ($highlights !== []) {
            foreach ($highlights as $highlight) {
                if ($highlight->title === '') {
                    continue;
                }
                $items[] = new StoryHighlightData(
                    imageUrl: ImageHelper::validatePath($highlight->imagePath),
                    title: $highlight->title,
                    description: $highlight->description,
                );
            }
        } else {
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
        }

        return new StoryHighlightsSectionData(
            heading: ImageHelper::getStringValue($cms, 'highlights_heading', ''),
            items: $items,
        );
    }

    /**
     * Builds the gallery section ViewModel splitting relational images into two display rows.
     * The reason for this is because the gallery template renders a top row of three and a bottom row of two, so the split must happen here rather than in the view.
     *
     * @param EventGalleryImage[] $galleryImages
     */
    private static function buildGallerySection(array $cms, array $galleryImages): StoryGallerySectionData
    {
        if ($galleryImages !== []) {
            $images = array_map(
                fn(EventGalleryImage $g) => ImageHelper::validatePath($g->imagePath),
                $galleryImages,
            );
        } else {
            $images = [];
            for ($i = 1; $i <= 5; $i++) {
                $images[] = ImageHelper::validatePath((string)($cms["gallery_image_{$i}"] ?? ''));
            }
        }

        return new StoryGallerySectionData(
            heading: ImageHelper::getStringValue($cms, 'gallery_heading', ''),
            topRowImages: array_slice($images, 0, 3),
            bottomRowImages: array_slice($images, 3, 2),
        );
    }

    /**
     * Builds the video embed section ViewModel from CMS content.
     * The reason for this is because the video URL and placeholder text are CMS-driven and must be wrapped in a typed ViewModel before the view can safely access them.
     */
    private static function buildVideoSection(array $cms): StoryVideoSectionData
    {
        return new StoryVideoSectionData(
            heading: ImageHelper::getStringValue($cms, 'video_heading', ''),
            url: (string)($cms['video_url'] ?? ''),
            placeholderText: ImageHelper::getStringValue($cms, 'video_placeholder', ''),
        );
    }

    /**
     * Produces a minimal HeroData used only by the shared shell partial on the detail page.
     * The reason for this is because the shell partial always expects a HeroData object, but the detail page uses its own custom hero, so this stub provides the required fields without duplicating real content.
     */
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
