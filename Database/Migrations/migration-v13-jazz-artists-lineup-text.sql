-- =====================================================
-- Migration v13: Add CMS items for Jazz artist lineup text fields
-- Purpose: Make "Discover our lineup" artist cards fully CMS-driven.
-- Adds TEXT items (name/genre/description/performance strings) for 3 artists.
-- =====================================================

START TRANSACTION;

SET @jazzPageId := (SELECT CmsPageId FROM CmsPage WHERE Slug = 'jazz' LIMIT 1);
SET @artistsSectionId := (
    SELECT CmsSectionId
    FROM CmsSection
    WHERE CmsPageId = @jazzPageId AND SectionKey = 'artists_section'
    LIMIT 1
);

-- Helper pattern: insert if missing

-- Gumbo Kings
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_gumbokings_name', 'TEXT', 'Gumbo Kings', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_gumbokings_name');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_gumbokings_genre', 'TEXT', 'New Orleans Jazz', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_gumbokings_genre');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_gumbokings_description', 'TEXT', 'High-energy New Orleans style jazz band bringing authentic Big Easy sound to Haarlem. Known for infectious rhythms.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_gumbokings_description');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_gumbokings_performance_count', 'TEXT', '2', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_gumbokings_performance_count');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_gumbokings_first_performance', 'TEXT', 'Thu 18:00 - Patronaat Main Hall', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_gumbokings_first_performance');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_gumbokings_more_performances_text', 'TEXT', '+1 more', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_gumbokings_more_performances_text');

-- Evolve
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_evolve_name', 'TEXT', 'Evolve', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_evolve_name');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_evolve_genre', 'TEXT', 'Contemporary Jazz', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_evolve_genre');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_evolve_description', 'TEXT', 'Progressive jazz ensemble pushing boundaries with innovative compositions. A fresh take on modern jazz traditions.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_evolve_description');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_evolve_performance_count', 'TEXT', '2', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_evolve_performance_count');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_evolve_first_performance', 'TEXT', 'Thu 18:00 - Patronaat Main Hall', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_evolve_first_performance');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_evolve_more_performances_text', 'TEXT', '+1 more', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_evolve_more_performances_text');

-- Ntjam Rosie
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_ntjam_name', 'TEXT', 'Ntjam Rosie', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_ntjam_name');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_ntjam_genre', 'TEXT', 'Vocal Jazz', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_ntjam_genre');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_ntjam_description', 'TEXT', 'Sultry vocals meet classic jazz standards. Rosie brings timeless elegance and powerful vocal performances to every show.', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_ntjam_description');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_ntjam_performance_count', 'TEXT', '2', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_ntjam_performance_count');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_ntjam_first_performance', 'TEXT', 'Thu 21:00 - Patronaat Main Hall', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_ntjam_first_performance');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @artistsSectionId, 'artists_ntjam_more_performances_text', 'TEXT', '', NULL, NULL, UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId=@artistsSectionId AND ItemKey='artists_ntjam_more_performances_text');

COMMIT;

