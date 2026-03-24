-- =====================================================
-- Migration v28: Jazz detail page button labels
-- Purpose: Ensure required button/accessibility labels exist for
--          Gumbo Kings and Ntjam Rosie detail pages.
-- Idempotent: Uses ON DUPLICATE KEY UPDATE.
-- =====================================================

START TRANSACTION;

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

-- Gumbo Kings labels
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'hero_back_button_text', 'TEXT', 'Back to Jazz', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'hero_reserve_button_text', 'TEXT', 'Reserve your spot', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'live_cta_book_button_text', 'TEXT', 'Book Tickets', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'live_cta_schedule_button_text', 'TEXT', 'View Full Schedule', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'listen_play_button_label', 'TEXT', 'Play excerpt', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'listen_play_excerpt_text', 'TEXT', 'Click to Play Excerpt', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'performances_section_id', 'TEXT', 'artist-performances', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

-- Ntjam Rosie labels
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'hero_back_button_text', 'TEXT', 'Back to Jazz', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'hero_reserve_button_text', 'TEXT', 'Reserve your spot', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'live_cta_book_button_text', 'TEXT', 'Book Tickets', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'live_cta_schedule_button_text', 'TEXT', 'View Full Schedule', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'listen_play_button_label', 'TEXT', 'Play excerpt', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'listen_play_excerpt_text', 'TEXT', 'Click to Play Excerpt', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'performances_section_id', 'TEXT', 'artist-performances', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

COMMIT;

SELECT 'Jazz detail button labels ensured for Gumbo Kings and Ntjam Rosie.' AS Status;

