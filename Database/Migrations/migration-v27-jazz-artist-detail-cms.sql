-- =====================================================
-- Migration v27: Jazz artist detail CMS content
-- Purpose: Make Gumbo Kings and Ntjam Rosie detail pages fully CMS-driven.
-- Scope: Adds/updates CmsPage + CmsSection + CmsItem rows for /jazz/gumbo-kings and /jazz/ntjam-rosie.
-- Idempotent: Uses unique keys with ON DUPLICATE KEY UPDATE.
-- =====================================================

START TRANSACTION;

INSERT INTO CmsPage (Slug, Title)
VALUES ('jazz-artist-detail', 'Jazz Artist Detail Pages')
ON DUPLICATE KEY UPDATE Title = VALUES(Title);

SET @detailPageId := (
    SELECT CmsPageId
    FROM CmsPage
    WHERE Slug = 'jazz-artist-detail'
    LIMIT 1
);

SET @gumboEventId := (
    SELECT EventId
    FROM Event
    WHERE EventTypeId = 1 AND IsActive = 1 AND Title = 'Gumbo Kings'
    LIMIT 1
);

SET @ntjamEventId := (
    SELECT EventId
    FROM Event
    WHERE EventTypeId = 1 AND IsActive = 1 AND Title = 'Ntjam Rosie'
    LIMIT 1
);

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @detailPageId, CONCAT('event_', @gumboEventId)
FROM DUAL
WHERE @detailPageId IS NOT NULL AND @gumboEventId IS NOT NULL
ON DUPLICATE KEY UPDATE SectionKey = VALUES(SectionKey);

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @detailPageId, CONCAT('event_', @ntjamEventId)
FROM DUAL
WHERE @detailPageId IS NOT NULL AND @ntjamEventId IS NOT NULL
ON DUPLICATE KEY UPDATE SectionKey = VALUES(SectionKey);

SET @gumboSectionId := (
    SELECT CmsSectionId
    FROM CmsSection
    WHERE CmsPageId = @detailPageId AND SectionKey = CONCAT('event_', @gumboEventId)
    LIMIT 1
);

SET @ntjamSectionId := (
    SELECT CmsSectionId
    FROM CmsSection
    WHERE CmsPageId = @detailPageId AND SectionKey = CONCAT('event_', @ntjamEventId)
    LIMIT 1
);

-- -----------------------------------------------------
-- Gumbo Kings
-- -----------------------------------------------------
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'hero_subtitle', 'TEXT', 'New Orleans Jazz', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/Jazz/GubmboKings-Hero.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'origin_text', 'TEXT', 'Origin: New Orleans, Louisiana, USA', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'formed_text', 'TEXT', 'Formed: 2015', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'performances_text', 'TEXT', '2 performances at Haarlem Jazz 2026', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'hero_back_button_text', 'TEXT', 'Back to Jazz', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'hero_reserve_button_text', 'TEXT', 'Reserve your spot', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'overview_heading', 'HEADING', 'Gumbo Kings', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'overview_lead', 'TEXT', 'High-energy New Orleans style jazz band bringing authentic Big Easy sound to Haarlem. Known for their infectious rhythms and crowd-pleasing performances that get audiences on their feet.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'overview_body_primary', 'TEXT', 'The Gumbo Kings deliver an electrifying blend of traditional New Orleans jazz, funk, and second-line grooves. With a powerful horn section, driving rhythm section, and authentic Crescent City soul, they transport audiences straight to the streets of the French Quarter. Their performances are known for spontaneous moments of musical magic and infectious energy that keeps crowds dancing all night long. Since their formation in 2015, they have become one of the most sought-after New Orleans jazz acts in Europe.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'overview_body_secondary', 'TEXT', 'Drawing inspiration from the rich musical heritage of New Orleans, the Gumbo Kings have mastered the art of combining traditional jazz elements with contemporary energy. Their repertoire spans classic jazz standards, original compositions, and reimagined funk grooves. The band has performed at major jazz festivals across Europe and the United States, earning acclaim for their authentic sound and dynamic stage presence. Their commitment to preserving and evolving the New Orleans jazz tradition has made them favorites among both purists and new jazz audiences.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'lineup_heading', 'TEXT', 'Band Lineup', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'lineup_1', 'TEXT', 'Marcus Johnson - Trumpet, Band Leader', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'lineup_2', 'TEXT', 'DeShawn Williams - Trombone', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'lineup_3', 'TEXT', 'Antoine Davis - Tenor Saxophone', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'lineup_4', 'TEXT', 'Jerome Baptiste - Drums', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'lineup_5', 'TEXT', 'Louis Carter - Upright Bass', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'lineup_6', 'TEXT', 'Raymond Pierce - Piano', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'highlights_heading', 'TEXT', 'Career Highlights', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'highlight_1', 'TEXT', 'Featured performers at New Orleans Jazz and Heritage Festival 2023', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'highlight_2', 'TEXT', 'Touring Europe extensively since 2019, performing at 50+ major festivals', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'highlight_3', 'TEXT', 'Authentic brass band sound with modern energy and innovation', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'highlight_4', 'TEXT', 'Collaborations with legendary New Orleans musicians including Trombone Shorty', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'highlight_5', 'TEXT', 'Known for interactive, high-energy live shows that get audiences dancing', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'highlight_6', 'TEXT', 'Winner of Best Jazz Ensemble at European Jazz Awards 2022', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'photo_gallery_heading', 'TEXT', 'Photo Gallery', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'photo_gallery_description', 'TEXT', 'Experience the energy and passion of Gumbo Kings through these performance and portrait photographs.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/Jazz/GumboGallery1.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/Jazz/GumboGallery2.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/Jazz/GumboGallery3.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'albums_heading', 'TEXT', 'Featured Albums', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'albums_description', 'TEXT', 'Explore the studio recordings that capture the magic of Gumbo Kings. Each album showcases their evolution and mastery of the New Orleans jazz tradition.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_1_title', 'TEXT', 'Second Line Swing', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_1_description', 'TEXT', 'Their breakthrough album featuring traditional second line rhythms mixed with contemporary jazz sensibilities. The title track became a festival favorite across Europe.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_1_year', 'TEXT', '2019', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_1_tag', 'TEXT', 'JAZZ', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_1_image', 'IMAGE_PATH', '/assets/Image/Jazz/GumboKingsAlbum1.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_2_title', 'TEXT', 'Big Easy', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_2_description', 'TEXT', 'A love letter to New Orleans featuring reimagined classics and original compositions inspired by the city''s rich musical heritage.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_2_year', 'TEXT', '2021', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_2_tag', 'TEXT', 'JAZZ', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_2_image', 'IMAGE_PATH', '/assets/Image/Jazz/GumboKingsAlbum2.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_3_title', 'TEXT', 'Live at Paradiso', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_3_description', 'TEXT', 'Recorded live at Amsterdam''s legendary Paradiso venue, this album captures the raw energy and spontaneity of their live performances.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_3_year', 'TEXT', '2023', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_3_tag', 'TEXT', 'LIVE', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'album_3_image', 'IMAGE_PATH', '/assets/Image/Jazz/GumboKingsAlbum3.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'listen_heading', 'TEXT', 'LISTEN NOW', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'listen_subheading', 'TEXT', 'Important Tracks', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'listen_description', 'TEXT', 'Listen to excerpts from Gumbo Kings''s most important and popular tracks. Experience the energy and musicianship that defines their sound.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'listen_play_button_label', 'TEXT', 'Play excerpt', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'listen_play_excerpt_text', 'TEXT', 'Click to Play Excerpt', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_1_title', 'TEXT', 'All Night Long', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_1_album', 'TEXT', 'Live in the Quarter', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_1_description', 'TEXT', 'Classic New Orleans standard with powerful brass arrangements', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_1_duration', 'TEXT', '4:32', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_1_image', 'IMAGE_PATH', '/assets/Image/Jazz/Allnightlong.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_1_progress_class', 'TEXT', 'w-[5%]', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_2_title', 'TEXT', 'Hot Damn!', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_2_album', 'TEXT', 'Brass and Soul', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_2_description', 'TEXT', 'Original composition featuring traditional second-line rhythms', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_2_duration', 'TEXT', '3:45', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_2_image', 'IMAGE_PATH', '/assets/Image/Jazz/Container.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_2_progress_class', 'TEXT', 'w-[15%]', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_3_title', 'TEXT', 'Valenzuela', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_3_album', 'TEXT', 'Big Easy Nights', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_3_description', 'TEXT', 'Fast-paced instrumental showcasing virtuoso musicianship', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_3_duration', 'TEXT', '4:18', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_3_image', 'IMAGE_PATH', '/assets/Image/Jazz/Listennowsection.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_3_progress_class', 'TEXT', 'w-full', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_4_title', 'TEXT', 'Here We Are', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_4_album', 'TEXT', 'Live in the Quarter', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_4_description', 'TEXT', 'High-energy rendition of the jazz funeral classic', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_4_duration', 'TEXT', '5:12', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_4_image', 'IMAGE_PATH', '/assets/Image/Jazz/Allnightlong.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'track_4_progress_class', 'TEXT', 'w-[60%]', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'live_cta_heading', 'TEXT', 'Experience Gumbo Kings Live', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'live_cta_description', 'TEXT', 'Do not miss the chance to see Gumbo Kings perform live at Haarlem Jazz 2026. With 2 performances scheduled, there are multiple opportunities to experience their incredible energy and musicianship.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'live_cta_book_button_text', 'TEXT', 'Book Tickets', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'live_cta_schedule_button_text', 'TEXT', 'View Full Schedule', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'performances_section_id', 'TEXT', 'artist-performances', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'performances_heading', 'TEXT', 'Gumbo Kings at Haarlem Jazz 2026', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'performances_description', 'TEXT', 'Catch Gumbo Kings performing during the Haarlem Jazz Festival. Each performance offers a unique experience from intimate indoor shows to free outdoor concerts.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

-- -----------------------------------------------------
-- Ntjam Rosie
-- -----------------------------------------------------
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'hero_subtitle', 'TEXT', 'Vocal Jazz', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamhero.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'origin_text', 'TEXT', 'Origin: Cameroon / Netherlands', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'formed_text', 'TEXT', 'Formed: 2008', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'performances_text', 'TEXT', '2 performances at Haarlem Jazz 2026', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'hero_back_button_text', 'TEXT', 'Back to Jazz', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'hero_reserve_button_text', 'TEXT', 'Reserve your spot', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'overview_heading', 'HEADING', 'Ntjam Rosie', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'overview_lead', 'TEXT', 'Ntjam Rosie was born in Cameroon on March 18, 1983, and moved to the Netherlands at the age of nine. She blends her West-African roots with Western musical traditions, combining jazz, soul, pop and Afro influences.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'overview_body_primary', 'TEXT', 'Her debut album, Atouba, released in 2008, was the first to showcase that hybrid style, mixing African rhythms with soul and jazz influences. Since then she has developed a distinctive musical voice across several albums and performances.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'overview_body_secondary', 'TEXT', 'Ntjam Rosie has built a reputation for compelling live performances and wide appeal. She has performed at major festivals and toured internationally. Her music resonates with both jazz and soul audiences, and she continues to evolve by blending tradition and innovation.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'lineup_heading', 'TEXT', 'Band Lineup', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'lineup_1', 'TEXT', 'Ntjam Rosie - Vocals', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'lineup_2', 'TEXT', 'Bart Wirtz - Tenor Saxophone', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'lineup_3', 'TEXT', 'Niels Broos - Piano and Keys', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'lineup_4', 'TEXT', 'Bram Hakkens - Drums', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'lineup_5', 'TEXT', 'Tijn Wybenga - Bass', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'highlights_heading', 'TEXT', 'Career Highlights', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'highlight_1', 'TEXT', 'Featured performer at the New Orleans Jazz and Heritage Festival 2023', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'highlight_2', 'TEXT', 'Toured across Europe since 2019, playing 50+ major festivals', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'highlight_3', 'TEXT', 'Blends Afro-European vocals with modern jazz and soul energy', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'highlight_4', 'TEXT', 'Collaborated with New Orleans artists, including Trombone Shorty', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'highlight_5', 'TEXT', 'Renowned for vibrant, high-energy live shows', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'highlight_6', 'TEXT', 'Winner of Best Jazz Ensemble at the European Jazz Awards 2022', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'photo_gallery_heading', 'TEXT', 'Photo Gallery', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'photo_gallery_description', 'TEXT', 'Experience the soulful elegance of Ntjam Rosie through these intimate performance and portrait photographs.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamgallery1.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamgallery2.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamgallery3.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'albums_heading', 'TEXT', 'Featured Albums', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'albums_description', 'TEXT', 'Explore the studio recordings that capture the soulful artistry of Ntjam Rosie. Each album reflects her evolving blend of jazz, soul, and Afro-inspired sound.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_1_title', 'TEXT', 'Atouba', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_1_description', 'TEXT', 'Her debut album where Ntjam Rosie introduced her Afro-European blend of soul and jazz.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_1_year', 'TEXT', '2019', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_1_tag', 'TEXT', 'JAZZ', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_1_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamalbum1.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_2_title', 'TEXT', 'At the Back of Beyond', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_2_description', 'TEXT', 'At the Back of Beyond showcases Ntjam Rosie''s soulful blend of jazz and Afro-European sounds.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_2_year', 'TEXT', '2021', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_2_tag', 'TEXT', 'JAZZ', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_2_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamalbum2.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_3_title', 'TEXT', 'Family and Friends', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_3_description', 'TEXT', 'Family and Friends highlights Ntjam Rosie''s warm vocals and her fusion of jazz and Afro-European influences.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_3_year', 'TEXT', '2023', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_3_tag', 'TEXT', 'LIVE', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'album_3_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamalbum3.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'listen_heading', 'TEXT', 'LISTEN NOW', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'listen_subheading', 'TEXT', 'Important Tracks', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'listen_description', 'TEXT', 'Listen to excerpts from Ntjam Rosie''s most celebrated and influential tracks. Immerse yourself in the soulful energy and refined musicianship that define her signature sound.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'listen_play_button_label', 'TEXT', 'Play excerpt', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'listen_play_excerpt_text', 'TEXT', 'Click to Play Excerpt', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_1_title', 'TEXT', 'What is Love?', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_1_album', 'TEXT', 'Live in the Quarter', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_1_description', 'TEXT', 'Classic New Orleans standard with powerful brass arrangements', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_1_duration', 'TEXT', '4:32', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_1_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamwhatislove.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_1_progress_class', 'TEXT', 'w-[5%]', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_2_title', 'TEXT', 'Thinkin About You', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_2_album', 'TEXT', 'Brass and Soul', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_2_description', 'TEXT', 'Original composition featuring traditional second-line rhythms', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_2_duration', 'TEXT', '3:45', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_2_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamthinkinaboutyou.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_2_progress_class', 'TEXT', 'w-[15%]', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_3_title', 'TEXT', 'You got this', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_3_album', 'TEXT', 'Big Easy Nights', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_3_description', 'TEXT', 'Fast-paced instrumental showcasing virtuoso musicianship', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_3_duration', 'TEXT', '4:18', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_3_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamyougotthis.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_3_progress_class', 'TEXT', 'w-full', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_4_title', 'TEXT', 'In Need - Reworked', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_4_album', 'TEXT', 'Live in the Quarter', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_4_description', 'TEXT', 'High-energy rendition of the jazz funeral classic', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_4_duration', 'TEXT', '5:12', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_4_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjaminneed.png', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'track_4_progress_class', 'TEXT', 'w-[60%]', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'live_cta_heading', 'TEXT', 'Experience Ntjam Rosie Live', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'live_cta_description', 'TEXT', 'Do not miss the chance to see Ntjam Rosie perform live at Haarlem Jazz 2026. With 2 performances scheduled, there are multiple opportunities to experience her incredible energy and musicianship.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'live_cta_book_button_text', 'TEXT', 'Book Tickets', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'live_cta_schedule_button_text', 'TEXT', 'View Full Schedule', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'performances_section_id', 'TEXT', 'artist-performances', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'performances_heading', 'TEXT', 'Ntjam Rosie at Haarlem Jazz 2026', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'performances_description', 'TEXT', 'Catch Ntjam Rosie performing during the Haarlem Jazz Festival. Each performance offers a unique experience from intimate indoor shows to free outdoor concerts.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), HtmlValue = VALUES(HtmlValue), MediaAssetId = VALUES(MediaAssetId);

COMMIT;

SELECT 'Jazz artist detail CMS content seeded for Gumbo Kings and Ntjam Rosie.' AS Status;

