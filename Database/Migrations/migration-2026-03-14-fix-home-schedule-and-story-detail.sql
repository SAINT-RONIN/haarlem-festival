-- Fix homepage schedule labels + storytelling detail event_34 corrupted fields
-- Date: 2026-03-14

START TRANSACTION;

-- 1) Ensure home/global_ui has the schedule labels used by schedule-day-column.php.
INSERT INTO CmsItem
  (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT
  s.CmsSectionId,
  src.ItemKey,
  src.ItemType,
  src.TextValue,
  NULL AS HtmlValue,
  NULL AS MediaAssetId,
  NOW() AS UpdatedAtUtc
FROM (
  SELECT 'label_events_count' AS ItemKey, 'TEXT' AS ItemType, 'events' AS TextValue
  UNION ALL SELECT 'label_no_events', 'TEXT', 'No events scheduled'
  UNION ALL SELECT 'btn_explore_template', 'TEXT', 'Explore {title} Events'
) src
JOIN CmsPage p
  ON p.Slug = CONVERT('home' USING utf8mb4) COLLATE utf8mb4_uca1400_ai_ci
JOIN CmsSection s
  ON s.CmsPageId = p.CmsPageId
 AND s.SectionKey = CONVERT('global_ui' USING utf8mb4) COLLATE utf8mb4_uca1400_ai_ci
ON DUPLICATE KEY UPDATE
  ItemType = VALUES(ItemType),
  TextValue = VALUES(TextValue),
  HtmlValue = VALUES(HtmlValue),
  MediaAssetId = VALUES(MediaAssetId),
  UpdatedAtUtc = VALUES(UpdatedAtUtc);

-- 2) Fix corrupted storytelling detail values for event_34.
--    Symptoms observed:
--      - highlight_1_image contained history start-point text
--      - highlight_2_title contained /assets/Image/History/History-hero.png
INSERT INTO CmsItem
  (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT
  s.CmsSectionId,
  src.ItemKey,
  src.ItemType,
  src.TextValue,
  NULL AS HtmlValue,
  NULL AS MediaAssetId,
  NOW() AS UpdatedAtUtc
FROM (
  SELECT 'highlight_1_image' AS ItemKey, 'IMAGE_PATH' AS ItemType, '/assets/Image/storytelling/winnie-the-pooh.jpg' AS TextValue
  UNION ALL SELECT 'highlight_2_title', 'TEXT', 'Live Storytelling'
) src
JOIN CmsPage p
  ON p.Slug = CONVERT('storytelling-detail' USING utf8mb4) COLLATE utf8mb4_uca1400_ai_ci
JOIN CmsSection s
  ON s.CmsPageId = p.CmsPageId
 AND s.SectionKey = CONVERT('event_34' USING utf8mb4) COLLATE utf8mb4_uca1400_ai_ci
ON DUPLICATE KEY UPDATE
  ItemType = VALUES(ItemType),
  TextValue = VALUES(TextValue),
  HtmlValue = VALUES(HtmlValue),
  MediaAssetId = VALUES(MediaAssetId),
  UpdatedAtUtc = VALUES(UpdatedAtUtc);

COMMIT;
