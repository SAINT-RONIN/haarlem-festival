<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Content\StorytellingEventCmsData;
use App\Content\StorytellingMasonrySectionContent;

/**
 * Maps raw CMS arrays into Storytelling page content models.
 */
final class StorytellingContentMapper
{
    /** Maps raw CMS data to a StorytellingMasonrySectionContent model. */
    public static function mapMasonry(array $raw): StorytellingMasonrySectionContent
    {
        $paths = [];
        for ($i = 1; $i <= StorytellingMasonrySectionContent::IMAGE_COUNT; $i++) {
            $paths[] = $raw[sprintf('masonry_image_%02d', $i)] ?? '';
        }

        return new StorytellingMasonrySectionContent(
            masonryHeading: $raw['masonry_heading'] ?? null,
            imagePaths: $paths,
        );
    }

    /** Maps raw CMS data to a StorytellingEventCmsData model. */
    public static function mapEventCmsData(array $raw): StorytellingEventCmsData
    {
        return new StorytellingEventCmsData(
            heroImage: $raw['hero_image'] ?? null,
            backButtonLabel: $raw['back_button_label'] ?? null,
            reserveButtonLabel: $raw['reserve_button_label'] ?? null,
            aboutHeading: $raw['about_heading'] ?? null,
            aboutBody: $raw['about_body'] ?? null,
            aboutImage1: $raw['about_image_1'] ?? null,
            aboutImage2: $raw['about_image_2'] ?? null,
            highlightsHeading: $raw['highlights_heading'] ?? null,
            highlight1Title: $raw['highlight_1_title'] ?? null,
            highlight1Image: $raw['highlight_1_image'] ?? null,
            highlight1Description: $raw['highlight_1_description'] ?? null,
            highlight2Title: $raw['highlight_2_title'] ?? null,
            highlight2Image: $raw['highlight_2_image'] ?? null,
            highlight2Description: $raw['highlight_2_description'] ?? null,
            highlight3Title: $raw['highlight_3_title'] ?? null,
            highlight3Image: $raw['highlight_3_image'] ?? null,
            highlight3Description: $raw['highlight_3_description'] ?? null,
            galleryHeading: $raw['gallery_heading'] ?? null,
            gallery1Image: $raw['gallery_image_1'] ?? null,
            gallery2Image: $raw['gallery_image_2'] ?? null,
            gallery3Image: $raw['gallery_image_3'] ?? null,
            gallery4Image: $raw['gallery_image_4'] ?? null,
            gallery5Image: $raw['gallery_image_5'] ?? null,
            videoHeading: $raw['video_heading'] ?? null,
            videoUrl: $raw['video_url'] ?? null,
            videoPlaceholder: $raw['video_placeholder'] ?? null,
            scheduleCtaButtonText: $raw['schedule_cta_button_text'] ?? null,
        );
    }
}
