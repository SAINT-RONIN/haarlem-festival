-- Normalize Jazz artist content ownership.
-- Goals:
-- 1. Move singleton Jazz artist-detail content out of CmsItem event sections into Artist.
-- 2. Re-key Jazz artist child tables from EventId to ArtistId.
-- 3. Remove redundant Jazz CMS records for static artist cards and per-event detail pages.

ALTER TABLE `Artist`
    ADD COLUMN `CardDescription` text NOT NULL DEFAULT '' AFTER `Style`,
    ADD COLUMN `HeroSubtitle` varchar(200) NOT NULL DEFAULT '' AFTER `CardDescription`,
    ADD COLUMN `HeroImagePath` varchar(500) NOT NULL DEFAULT '' AFTER `HeroSubtitle`,
    ADD COLUMN `OriginText` varchar(200) NOT NULL DEFAULT '' AFTER `HeroImagePath`,
    ADD COLUMN `FormedText` varchar(120) NOT NULL DEFAULT '' AFTER `OriginText`,
    ADD COLUMN `OverviewLead` text NOT NULL DEFAULT '' AFTER `BioHtml`,
    ADD COLUMN `OverviewBodySecondary` text NOT NULL DEFAULT '' AFTER `OverviewLead`,
    ADD COLUMN `LineupHeading` varchar(120) NOT NULL DEFAULT '' AFTER `OverviewBodySecondary`,
    ADD COLUMN `HighlightsHeading` varchar(120) NOT NULL DEFAULT '' AFTER `LineupHeading`,
    ADD COLUMN `PhotoGalleryHeading` varchar(120) NOT NULL DEFAULT '' AFTER `HighlightsHeading`,
    ADD COLUMN `PhotoGalleryDescription` text NOT NULL DEFAULT '' AFTER `PhotoGalleryHeading`,
    ADD COLUMN `AlbumsHeading` varchar(120) NOT NULL DEFAULT '' AFTER `PhotoGalleryDescription`,
    ADD COLUMN `AlbumsDescription` text NOT NULL DEFAULT '' AFTER `AlbumsHeading`,
    ADD COLUMN `ListenHeading` varchar(120) NOT NULL DEFAULT '' AFTER `AlbumsDescription`,
    ADD COLUMN `ListenSubheading` varchar(120) NOT NULL DEFAULT '' AFTER `ListenHeading`,
    ADD COLUMN `ListenDescription` text NOT NULL DEFAULT '' AFTER `ListenSubheading`,
    ADD COLUMN `LiveCtaHeading` varchar(160) NOT NULL DEFAULT '' AFTER `ListenDescription`,
    ADD COLUMN `LiveCtaDescription` text NOT NULL DEFAULT '' AFTER `LiveCtaHeading`,
    ADD COLUMN `PerformancesHeading` varchar(160) NOT NULL DEFAULT '' AFTER `LiveCtaDescription`,
    ADD COLUMN `PerformancesDescription` text NOT NULL DEFAULT '' AFTER `PerformancesHeading`,
    ADD COLUMN `CardSortOrder` int(11) NOT NULL DEFAULT 0 AFTER `PerformancesDescription`,
    ADD COLUMN `ShowOnJazzOverview` tinyint(1) NOT NULL DEFAULT 0 AFTER `CardSortOrder`;

ALTER TABLE `Artist`
    ADD KEY `IX_Artist_JazzOverview` (`ShowOnJazzOverview`, `CardSortOrder`, `Name`);

-- General fallback data from linked Jazz events.
UPDATE `Artist` a
INNER JOIN `Event` e
    ON e.`ArtistId` = a.`ArtistId`
   AND e.`EventTypeId` = 1
SET
    a.`CardDescription` = CASE
        WHEN a.`CardDescription` = '' AND e.`ShortDescription` <> '' THEN e.`ShortDescription`
        ELSE a.`CardDescription`
    END,
    a.`HeroSubtitle` = CASE
        WHEN a.`HeroSubtitle` = '' AND e.`ShortDescription` <> '' THEN e.`ShortDescription`
        ELSE a.`HeroSubtitle`
    END,
    a.`OverviewLead` = CASE
        WHEN a.`OverviewLead` = '' AND e.`ShortDescription` <> '' THEN e.`ShortDescription`
        ELSE a.`OverviewLead`
    END,
    a.`BioHtml` = CASE
        WHEN (a.`BioHtml` = '' OR a.`BioHtml` = '<p></p>') AND e.`LongDescriptionHtml` <> '' THEN e.`LongDescriptionHtml`
        ELSE a.`BioHtml`
    END;

-- Backfill the featured artist cards that were previously stored as Jazz CMS items.
UPDATE `Artist`
SET
    `CardSortOrder` = 1,
    `ShowOnJazzOverview` = 1,
    `CardDescription` = COALESCE(
        NULLIF((
            SELECT i.`TextValue`
            FROM `CmsPage` p
            INNER JOIN `CmsSection` s ON s.`CmsPageId` = p.`CmsPageId`
            INNER JOIN `CmsItem` i ON i.`CmsSectionId` = s.`CmsSectionId`
            WHERE p.`Slug` = 'jazz'
              AND s.`SectionKey` = 'artists_section'
              AND i.`ItemKey` = 'artists_gumbokings_description'
            LIMIT 1
        ), ''),
        `CardDescription`
    ),
    `ImageAssetId` = COALESCE(
        `ImageAssetId`,
        (
            SELECT i.`MediaAssetId`
            FROM `CmsPage` p
            INNER JOIN `CmsSection` s ON s.`CmsPageId` = p.`CmsPageId`
            INNER JOIN `CmsItem` i ON i.`CmsSectionId` = s.`CmsSectionId`
            WHERE p.`Slug` = 'jazz'
              AND s.`SectionKey` = 'artists_section'
              AND i.`ItemKey` = 'artists_gumbokings_image'
            LIMIT 1
        )
    )
WHERE `Name` = 'Gumbo Kings';

UPDATE `Artist`
SET
    `CardSortOrder` = 2,
    `ShowOnJazzOverview` = 1,
    `CardDescription` = COALESCE(
        NULLIF((
            SELECT i.`TextValue`
            FROM `CmsPage` p
            INNER JOIN `CmsSection` s ON s.`CmsPageId` = p.`CmsPageId`
            INNER JOIN `CmsItem` i ON i.`CmsSectionId` = s.`CmsSectionId`
            WHERE p.`Slug` = 'jazz'
              AND s.`SectionKey` = 'artists_section'
              AND i.`ItemKey` = 'artists_evolve_description'
            LIMIT 1
        ), ''),
        `CardDescription`
    ),
    `ImageAssetId` = COALESCE(
        `ImageAssetId`,
        (
            SELECT i.`MediaAssetId`
            FROM `CmsPage` p
            INNER JOIN `CmsSection` s ON s.`CmsPageId` = p.`CmsPageId`
            INNER JOIN `CmsItem` i ON i.`CmsSectionId` = s.`CmsSectionId`
            WHERE p.`Slug` = 'jazz'
              AND s.`SectionKey` = 'artists_section'
              AND i.`ItemKey` = 'artists_evolve_image'
            LIMIT 1
        )
    )
WHERE `Name` = 'Evolve';

UPDATE `Artist`
SET
    `CardSortOrder` = 3,
    `ShowOnJazzOverview` = 1,
    `CardDescription` = COALESCE(
        NULLIF((
            SELECT i.`TextValue`
            FROM `CmsPage` p
            INNER JOIN `CmsSection` s ON s.`CmsPageId` = p.`CmsPageId`
            INNER JOIN `CmsItem` i ON i.`CmsSectionId` = s.`CmsSectionId`
            WHERE p.`Slug` = 'jazz'
              AND s.`SectionKey` = 'artists_section'
              AND i.`ItemKey` = 'artists_ntjam_description'
            LIMIT 1
        ), ''),
        `CardDescription`
    ),
    `ImageAssetId` = COALESCE(
        `ImageAssetId`,
        (
            SELECT i.`MediaAssetId`
            FROM `CmsPage` p
            INNER JOIN `CmsSection` s ON s.`CmsPageId` = p.`CmsPageId`
            INNER JOIN `CmsItem` i ON i.`CmsSectionId` = s.`CmsSectionId`
            WHERE p.`Slug` = 'jazz'
              AND s.`SectionKey` = 'artists_section'
              AND i.`ItemKey` = 'artists_ntjam_image'
            LIMIT 1
        )
    )
WHERE `Name` = 'Ntjam Rosie';

-- Backfill per-artist detail content from the old Jazz detail CmsItem sections.
UPDATE `Artist` a
INNER JOIN `Event` e
    ON e.`ArtistId` = a.`ArtistId`
   AND e.`EventTypeId` = 1
INNER JOIN `CmsPage` p
    ON p.`Slug` = 'jazz-artist-detail'
INNER JOIN `CmsSection` s
    ON s.`CmsPageId` = p.`CmsPageId`
   AND s.`SectionKey` = CONCAT('event_', e.`EventId`)
LEFT JOIN `CmsItem` hero_subtitle
    ON hero_subtitle.`CmsSectionId` = s.`CmsSectionId`
   AND hero_subtitle.`ItemKey` = 'hero_subtitle'
LEFT JOIN `CmsItem` hero_image
    ON hero_image.`CmsSectionId` = s.`CmsSectionId`
   AND hero_image.`ItemKey` = 'hero_background_image'
LEFT JOIN `CmsItem` origin_text
    ON origin_text.`CmsSectionId` = s.`CmsSectionId`
   AND origin_text.`ItemKey` = 'origin_text'
LEFT JOIN `CmsItem` formed_text
    ON formed_text.`CmsSectionId` = s.`CmsSectionId`
   AND formed_text.`ItemKey` = 'formed_text'
LEFT JOIN `CmsItem` overview_lead
    ON overview_lead.`CmsSectionId` = s.`CmsSectionId`
   AND overview_lead.`ItemKey` = 'overview_lead'
LEFT JOIN `CmsItem` overview_primary
    ON overview_primary.`CmsSectionId` = s.`CmsSectionId`
   AND overview_primary.`ItemKey` = 'overview_body_primary'
LEFT JOIN `CmsItem` overview_secondary
    ON overview_secondary.`CmsSectionId` = s.`CmsSectionId`
   AND overview_secondary.`ItemKey` = 'overview_body_secondary'
LEFT JOIN `CmsItem` lineup_heading
    ON lineup_heading.`CmsSectionId` = s.`CmsSectionId`
   AND lineup_heading.`ItemKey` = 'lineup_heading'
LEFT JOIN `CmsItem` highlights_heading
    ON highlights_heading.`CmsSectionId` = s.`CmsSectionId`
   AND highlights_heading.`ItemKey` = 'highlights_heading'
LEFT JOIN `CmsItem` gallery_heading
    ON gallery_heading.`CmsSectionId` = s.`CmsSectionId`
   AND gallery_heading.`ItemKey` = 'photo_gallery_heading'
LEFT JOIN `CmsItem` gallery_description
    ON gallery_description.`CmsSectionId` = s.`CmsSectionId`
   AND gallery_description.`ItemKey` = 'photo_gallery_description'
LEFT JOIN `CmsItem` albums_heading
    ON albums_heading.`CmsSectionId` = s.`CmsSectionId`
   AND albums_heading.`ItemKey` = 'albums_heading'
LEFT JOIN `CmsItem` albums_description
    ON albums_description.`CmsSectionId` = s.`CmsSectionId`
   AND albums_description.`ItemKey` = 'albums_description'
LEFT JOIN `CmsItem` listen_heading
    ON listen_heading.`CmsSectionId` = s.`CmsSectionId`
   AND listen_heading.`ItemKey` = 'listen_heading'
LEFT JOIN `CmsItem` listen_subheading
    ON listen_subheading.`CmsSectionId` = s.`CmsSectionId`
   AND listen_subheading.`ItemKey` = 'listen_subheading'
LEFT JOIN `CmsItem` listen_description
    ON listen_description.`CmsSectionId` = s.`CmsSectionId`
   AND listen_description.`ItemKey` = 'listen_description'
LEFT JOIN `CmsItem` live_cta_heading
    ON live_cta_heading.`CmsSectionId` = s.`CmsSectionId`
   AND live_cta_heading.`ItemKey` = 'live_cta_heading'
LEFT JOIN `CmsItem` live_cta_description
    ON live_cta_description.`CmsSectionId` = s.`CmsSectionId`
   AND live_cta_description.`ItemKey` = 'live_cta_description'
LEFT JOIN `CmsItem` performances_heading
    ON performances_heading.`CmsSectionId` = s.`CmsSectionId`
   AND performances_heading.`ItemKey` = 'performances_heading'
LEFT JOIN `CmsItem` performances_description
    ON performances_description.`CmsSectionId` = s.`CmsSectionId`
   AND performances_description.`ItemKey` = 'performances_description'
SET
    a.`HeroSubtitle` = COALESCE(NULLIF(hero_subtitle.`TextValue`, ''), a.`HeroSubtitle`),
    a.`HeroImagePath` = COALESCE(NULLIF(hero_image.`TextValue`, ''), a.`HeroImagePath`),
    a.`OriginText` = COALESCE(NULLIF(origin_text.`TextValue`, ''), a.`OriginText`),
    a.`FormedText` = COALESCE(NULLIF(formed_text.`TextValue`, ''), a.`FormedText`),
    a.`OverviewLead` = COALESCE(NULLIF(overview_lead.`TextValue`, ''), a.`OverviewLead`),
    a.`BioHtml` = COALESCE(NULLIF(overview_primary.`TextValue`, ''), a.`BioHtml`),
    a.`OverviewBodySecondary` = COALESCE(NULLIF(overview_secondary.`TextValue`, ''), a.`OverviewBodySecondary`),
    a.`LineupHeading` = COALESCE(NULLIF(lineup_heading.`TextValue`, ''), a.`LineupHeading`),
    a.`HighlightsHeading` = COALESCE(NULLIF(highlights_heading.`TextValue`, ''), a.`HighlightsHeading`),
    a.`PhotoGalleryHeading` = COALESCE(NULLIF(gallery_heading.`TextValue`, ''), a.`PhotoGalleryHeading`),
    a.`PhotoGalleryDescription` = COALESCE(NULLIF(gallery_description.`TextValue`, ''), a.`PhotoGalleryDescription`),
    a.`AlbumsHeading` = COALESCE(NULLIF(albums_heading.`TextValue`, ''), a.`AlbumsHeading`),
    a.`AlbumsDescription` = COALESCE(NULLIF(albums_description.`TextValue`, ''), a.`AlbumsDescription`),
    a.`ListenHeading` = COALESCE(NULLIF(listen_heading.`TextValue`, ''), a.`ListenHeading`),
    a.`ListenSubheading` = COALESCE(NULLIF(listen_subheading.`TextValue`, ''), a.`ListenSubheading`),
    a.`ListenDescription` = COALESCE(NULLIF(listen_description.`TextValue`, ''), a.`ListenDescription`),
    a.`LiveCtaHeading` = COALESCE(NULLIF(live_cta_heading.`TextValue`, ''), a.`LiveCtaHeading`),
    a.`LiveCtaDescription` = COALESCE(NULLIF(live_cta_description.`TextValue`, ''), a.`LiveCtaDescription`),
    a.`PerformancesHeading` = COALESCE(NULLIF(performances_heading.`TextValue`, ''), a.`PerformancesHeading`),
    a.`PerformancesDescription` = COALESCE(NULLIF(performances_description.`TextValue`, ''), a.`PerformancesDescription`);

-- Re-key artist child tables from EventId to ArtistId.
ALTER TABLE `ArtistAlbum`
    ADD COLUMN `ArtistId` int(11) NULL AFTER `ArtistAlbumId`;
UPDATE `ArtistAlbum` aa
INNER JOIN `Event` e ON e.`EventId` = aa.`EventId`
SET aa.`ArtistId` = e.`ArtistId`;
ALTER TABLE `ArtistAlbum`
    DROP FOREIGN KEY `FK_ArtistAlbum_Event`,
    DROP KEY `IX_ArtistAlbum_Event`,
    DROP COLUMN `EventId`,
    MODIFY COLUMN `ArtistId` int(11) NOT NULL,
    ADD KEY `IX_ArtistAlbum_Artist` (`ArtistId`, `SortOrder`),
    ADD CONSTRAINT `FK_ArtistAlbum_Artist` FOREIGN KEY (`ArtistId`) REFERENCES `Artist` (`ArtistId`) ON DELETE CASCADE;

ALTER TABLE `ArtistTrack`
    ADD COLUMN `ArtistId` int(11) NULL AFTER `ArtistTrackId`;
UPDATE `ArtistTrack` at
INNER JOIN `Event` e ON e.`EventId` = at.`EventId`
SET at.`ArtistId` = e.`ArtistId`;
ALTER TABLE `ArtistTrack`
    DROP FOREIGN KEY `FK_ArtistTrack_Event`,
    DROP KEY `IX_ArtistTrack_Event`,
    DROP COLUMN `EventId`,
    MODIFY COLUMN `ArtistId` int(11) NOT NULL,
    ADD KEY `IX_ArtistTrack_Artist` (`ArtistId`, `SortOrder`),
    ADD CONSTRAINT `FK_ArtistTrack_Artist` FOREIGN KEY (`ArtistId`) REFERENCES `Artist` (`ArtistId`) ON DELETE CASCADE;

ALTER TABLE `ArtistHighlight`
    ADD COLUMN `ArtistId` int(11) NULL AFTER `ArtistHighlightId`;
UPDATE `ArtistHighlight` ah
INNER JOIN `Event` e ON e.`EventId` = ah.`EventId`
SET ah.`ArtistId` = e.`ArtistId`;
ALTER TABLE `ArtistHighlight`
    DROP FOREIGN KEY `FK_ArtistHighlight_Event`,
    DROP KEY `IX_ArtistHighlight_Event`,
    DROP COLUMN `EventId`,
    MODIFY COLUMN `ArtistId` int(11) NOT NULL,
    ADD KEY `IX_ArtistHighlight_Artist` (`ArtistId`, `SortOrder`),
    ADD CONSTRAINT `FK_ArtistHighlight_Artist` FOREIGN KEY (`ArtistId`) REFERENCES `Artist` (`ArtistId`) ON DELETE CASCADE;

ALTER TABLE `ArtistGalleryImage`
    ADD COLUMN `ArtistId` int(11) NULL AFTER `ArtistGalleryImageId`;
UPDATE `ArtistGalleryImage` ag
INNER JOIN `Event` e ON e.`EventId` = ag.`EventId`
SET ag.`ArtistId` = e.`ArtistId`;
ALTER TABLE `ArtistGalleryImage`
    DROP FOREIGN KEY `FK_ArtistGalleryImage_Event`,
    DROP KEY `IX_ArtistGalleryImage_Event`,
    DROP COLUMN `EventId`,
    MODIFY COLUMN `ArtistId` int(11) NOT NULL,
    ADD KEY `IX_ArtistGalleryImage_Artist` (`ArtistId`, `SortOrder`),
    ADD CONSTRAINT `FK_ArtistGalleryImage_Artist` FOREIGN KEY (`ArtistId`) REFERENCES `Artist` (`ArtistId`) ON DELETE CASCADE;

ALTER TABLE `ArtistLineupMember`
    ADD COLUMN `ArtistId` int(11) NULL AFTER `ArtistLineupMemberId`;
UPDATE `ArtistLineupMember` al
INNER JOIN `Event` e ON e.`EventId` = al.`EventId`
SET al.`ArtistId` = e.`ArtistId`;
ALTER TABLE `ArtistLineupMember`
    DROP FOREIGN KEY `FK_ArtistLineupMember_Event`,
    DROP KEY `IX_ArtistLineupMember_Event`,
    DROP COLUMN `EventId`,
    MODIFY COLUMN `ArtistId` int(11) NOT NULL,
    ADD KEY `IX_ArtistLineupMember_Artist` (`ArtistId`, `SortOrder`),
    ADD CONSTRAINT `FK_ArtistLineupMember_Artist` FOREIGN KEY (`ArtistId`) REFERENCES `Artist` (`ArtistId`) ON DELETE CASCADE;

-- Remove the redundant CMS items that used to mirror artist cards and artist detail pages.
DELETE i
FROM `CmsItem` i
INNER JOIN `CmsSection` s ON s.`CmsSectionId` = i.`CmsSectionId`
INNER JOIN `CmsPage` p ON p.`CmsPageId` = s.`CmsPageId`
WHERE p.`Slug` = 'jazz'
  AND s.`SectionKey` = 'artists_section'
  AND i.`ItemKey` <> 'artists_heading';

DELETE i
FROM `CmsItem` i
INNER JOIN `CmsSection` s ON s.`CmsSectionId` = i.`CmsSectionId`
INNER JOIN `CmsPage` p ON p.`CmsPageId` = s.`CmsPageId`
WHERE p.`Slug` = 'jazz-artist-detail';

DELETE s
FROM `CmsSection` s
INNER JOIN `CmsPage` p ON p.`CmsPageId` = s.`CmsPageId`
WHERE p.`Slug` = 'jazz-artist-detail';

DELETE FROM `CmsPage`
WHERE `Slug` = 'jazz-artist-detail';
