-- =====================================================
-- Migration v29: Jazz detail links and alt text keys
-- Purpose: Remove remaining hardcoded URL/alt text fragments from Jazz detail views.
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

-- Gumbo Kings
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'hero_back_button_url', 'URL', '/jazz', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'live_cta_schedule_button_url', 'URL', '/jazz#jazz-schedule', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gumboSectionId, 'listen_track_artwork_alt_suffix', 'TEXT', 'track artwork', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @gumboSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

-- Ntjam Rosie
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'hero_back_button_url', 'URL', '/jazz', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'live_cta_schedule_button_url', 'URL', '/jazz#jazz-schedule', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @ntjamSectionId, 'listen_track_artwork_alt_suffix', 'TEXT', 'track artwork', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL WHERE @ntjamSectionId IS NOT NULL
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue), UpdatedAtUtc = UTC_TIMESTAMP();

COMMIT;

SELECT 'Jazz detail URL and alt text CMS keys ensured.' AS Status;

