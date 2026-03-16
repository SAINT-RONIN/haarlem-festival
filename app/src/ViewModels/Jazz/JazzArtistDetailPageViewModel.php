<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

use App\Constants\JazzArtistDetailConstants;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;

/**
 * ViewModel for a Jazz artist detail page.
 */
final readonly class JazzArtistDetailPageViewModel
{
    /**
     * @param array<string> $lineup
     * @param array<string> $highlights
     * @param array<string> $galleryImages
     * @param array<JazzArtistAlbumData> $albums
     * @param array<JazzArtistTrackData> $tracks
     * @param array<ScheduleEventCardViewModel> $performances
     */
    public function __construct(
        public string $heroTitle,
        public string $heroSubtitle,
        public string $heroBackgroundImageUrl,
        public string $originText,
        public string $formedText,
        public string $performancesText,
        public string $heroBackButtonText,
        public string $heroBackButtonUrl,
        public string $heroReserveButtonText,
        public string $overviewHeading,
        public string $overviewLead,
        public string $overviewBodyPrimary,
        public string $overviewBodySecondary,
        public string $lineupHeading,
        public array $lineup,
        public string $highlightsHeading,
        public array $highlights,
        public string $photoGalleryHeading,
        public string $photoGalleryDescription,
        public array $galleryImages,
        public string $albumsHeading,
        public string $albumsDescription,
        public array $albums,
        public string $listenHeading,
        public string $listenSubheading,
        public string $listenDescription,
        public string $listenPlayButtonLabel,
        public string $listenPlayExcerptText,
        public string $listenTrackArtworkAltSuffix,
        public array $tracks,
        public string $liveCtaHeading,
        public string $liveCtaDescription,
        public string $liveCtaBookButtonText,
        public string $liveCtaScheduleButtonText,
        public string $liveCtaScheduleButtonUrl,
        public string $performancesSectionId,
        public string $performancesHeading,
        public string $performancesDescription,
        public array $performances,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromData(array $data): self
    {
        return self::fromMappedData($data);
    }

    /**
     * @param array<string, mixed> $domain
     */
    public static function fromDomainData(array $domain): self
    {
        $event = is_array($domain['event'] ?? null) ? $domain['event'] : [];
        $cms = is_array($domain['cms'] ?? null) ? $domain['cms'] : [];

        $eventTitle = self::eventString($event, 'title', 'Title');
        $eventShortDescription = self::eventString($event, 'shortDescription', 'ShortDescription');
        $mappedData = [
            'heroTitle' => $eventTitle,
            'heroSubtitle' => self::coalesce(
                self::cmsValue($cms, 'hero_subtitle'),
                $eventShortDescription,
            ),
            'heroBackgroundImageUrl' => self::cmsValue($cms, 'hero_background_image'),
            'originText' => self::cmsValue($cms, 'origin_text'),
            'formedText' => self::cmsValue($cms, 'formed_text'),
            'performancesText' => self::cmsValue($cms, 'performances_text'),
            'heroBackButtonText' => self::cmsValue($cms, 'hero_back_button_text'),
            'heroBackButtonUrl' => self::cmsValue($cms, 'hero_back_button_url'),
            'heroReserveButtonText' => self::cmsValue($cms, 'hero_reserve_button_text'),
            'overviewHeading' => self::coalesce(self::cmsValue($cms, 'overview_heading'), $eventTitle),
            'overviewLead' => self::coalesce(
                self::cmsValue($cms, 'overview_lead'),
                $eventShortDescription,
            ),
            'overviewBodyPrimary' => self::coalesce(
                self::cmsValue($cms, 'overview_body_primary'),
                self::buildPrimaryOverviewFallback($event),
            ),
            'overviewBodySecondary' => self::cmsValue($cms, 'overview_body_secondary'),
            'lineupHeading' => self::cmsValue($cms, 'lineup_heading'),
            'lineup' => self::collectTextList(
                $cms,
                JazzArtistDetailConstants::LINEUP_PREFIX,
                JazzArtistDetailConstants::MAX_LIST_ITEMS,
            ),
            'highlightsHeading' => self::cmsValue($cms, 'highlights_heading'),
            'highlights' => self::collectTextList(
                $cms,
                JazzArtistDetailConstants::HIGHLIGHT_PREFIX,
                JazzArtistDetailConstants::MAX_LIST_ITEMS,
            ),
            'photoGalleryHeading' => self::cmsValue($cms, 'photo_gallery_heading'),
            'photoGalleryDescription' => self::cmsValue($cms, 'photo_gallery_description'),
            'galleryImages' => self::collectTextList(
                $cms,
                JazzArtistDetailConstants::GALLERY_IMAGE_PREFIX,
                JazzArtistDetailConstants::MAX_LIST_ITEMS,
            ),
            'albumsHeading' => self::cmsValue($cms, 'albums_heading'),
            'albumsDescription' => self::cmsValue($cms, 'albums_description'),
            'albums' => self::buildAlbums($cms),
            'listenHeading' => self::cmsValue($cms, 'listen_heading'),
            'listenSubheading' => self::cmsValue($cms, 'listen_subheading'),
            'listenDescription' => self::cmsValue($cms, 'listen_description'),
            'listenPlayButtonLabel' => self::cmsValue($cms, 'listen_play_button_label'),
            'listenPlayExcerptText' => self::cmsValue($cms, 'listen_play_excerpt_text'),
            'listenTrackArtworkAltSuffix' => self::cmsValue(
                $cms,
                'listen_track_artwork_alt_suffix',
            ),
            'tracks' => self::buildTracks($cms),
            'liveCtaHeading' => self::cmsValue($cms, 'live_cta_heading'),
            'liveCtaDescription' => self::cmsValue($cms, 'live_cta_description'),
            'liveCtaBookButtonText' => self::cmsValue($cms, 'live_cta_book_button_text'),
            'liveCtaScheduleButtonText' => self::cmsValue($cms, 'live_cta_schedule_button_text'),
            'liveCtaScheduleButtonUrl' => self::cmsValue($cms, 'live_cta_schedule_button_url'),
            'performancesSectionId' => self::cmsValue($cms, 'performances_section_id'),
            'performancesHeading' => self::cmsValue($cms, 'performances_heading'),
            'performancesDescription' => self::cmsValue($cms, 'performances_description'),
            'performances' => is_array($domain['performances'] ?? null) ? $domain['performances'] : [],
        ];

        return self::fromMappedData($mappedData);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function fromMappedData(array $data): self
    {
        $albums = [];
        foreach (($data['albums'] ?? []) as $album) {
            $albums[] = new JazzArtistAlbumData(...$album);
        }

        $tracks = [];
        foreach (($data['tracks'] ?? []) as $track) {
            $tracks[] = new JazzArtistTrackData(...$track);
        }

        $performances = [];
        foreach (($data['performances'] ?? []) as $performance) {
            $performances[] = new ScheduleEventCardViewModel(...$performance);
        }

        $data['albums'] = $albums;
        $data['tracks'] = $tracks;
        $data['performances'] = $performances;

        return new self(...$data);
    }

    private static function buildAlbums(array $cms): array
    {
        $albums = [];

        for ($index = 1; $index <= JazzArtistDetailConstants::MAX_ALBUMS; $index++) {
            $title = self::cmsValue($cms, JazzArtistDetailConstants::ALBUM_PREFIX . $index . '_title');
            if ($title === '') {
                continue;
            }

            $albums[] = [
                'title' => $title,
                'description' => self::cmsValue(
                    $cms,
                    JazzArtistDetailConstants::ALBUM_PREFIX . $index . '_description',
                ),
                'year' => self::cmsValue($cms, JazzArtistDetailConstants::ALBUM_PREFIX . $index . '_year'),
                'tag' => self::cmsValue($cms, JazzArtistDetailConstants::ALBUM_PREFIX . $index . '_tag'),
                'imageUrl' => self::cmsValue($cms, JazzArtistDetailConstants::ALBUM_PREFIX . $index . '_image'),
            ];
        }

        return $albums;
    }

    private static function buildTracks(array $cms): array
    {
        $tracks = [];

        for ($index = 1; $index <= JazzArtistDetailConstants::MAX_TRACKS; $index++) {
            $title = self::cmsValue($cms, JazzArtistDetailConstants::TRACK_PREFIX . $index . '_title');
            if ($title === '') {
                continue;
            }

            $tracks[] = [
                'title' => $title,
                'album' => self::cmsValue($cms, JazzArtistDetailConstants::TRACK_PREFIX . $index . '_album'),
                'description' => self::cmsValue(
                    $cms,
                    JazzArtistDetailConstants::TRACK_PREFIX . $index . '_description',
                ),
                'duration' => self::cmsValue($cms, JazzArtistDetailConstants::TRACK_PREFIX . $index . '_duration'),
                'imageUrl' => self::cmsValue($cms, JazzArtistDetailConstants::TRACK_PREFIX . $index . '_image'),
                'progressClass' => self::cmsValue(
                    $cms,
                    JazzArtistDetailConstants::TRACK_PREFIX . $index . '_progress_class',
                ),
            ];
        }

        return $tracks;
    }

    private static function collectTextList(array $cms, string $prefix, int $maxItems): array
    {
        $values = [];

        for ($index = 1; $index <= $maxItems; $index++) {
            $value = self::cmsValue($cms, $prefix . $index);
            if ($value !== '') {
                $values[] = $value;
            }
        }

        return $values;
    }

    private static function buildPrimaryOverviewFallback(array $event): string
    {
        $descriptionHtml = self::eventString($event, 'longDescriptionHtml', 'LongDescriptionHtml');
        if ($descriptionHtml === '') {
            return '';
        }

        return trim(strip_tags($descriptionHtml));
    }

    private static function cmsValue(array $content, string $key): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) ? $value : '';
    }

    private static function coalesce(string $value, string $fallback): string
    {
        return $value !== '' ? $value : $fallback;
    }

    private static function eventString(array $event, string $camelCaseKey, string $legacyKey): string
    {
        $value = $event[$camelCaseKey] ?? $event[$legacyKey] ?? null;
        return is_string($value) ? $value : '';
    }
}
