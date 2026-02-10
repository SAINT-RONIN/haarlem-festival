-- =====================================================
-- Migration v16: Decode common HTML entities in Jazz TEXT items
-- Purpose: Some TEXT items contain escaped entities like &euro; and &amp;.
-- TEXT fields are rendered with htmlspecialchars(), so entities show up literally.
-- This migration converts them to plain characters for the Jazz page only.
-- =====================================================

START TRANSACTION;

UPDATE CmsItem ci
JOIN CmsSection cs ON cs.CmsSectionId = ci.CmsSectionId
JOIN CmsPage cp ON cp.CmsPageId = cs.CmsPageId
SET ci.TextValue = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(ci.TextValue,
    '&euro;', '€'),
    '&amp;', '&'),
    '&nbsp;', ' '),
    '&quot;', '"'),
    '&#39;', "'")
WHERE cp.Slug = 'jazz'
  AND ci.ItemType = 'TEXT'
  AND ci.TextValue IS NOT NULL
  AND (
      ci.TextValue LIKE '%&euro;%' OR
      ci.TextValue LIKE '%&amp;%' OR
      ci.TextValue LIKE '%&nbsp;%' OR
      ci.TextValue LIKE '%&quot;%' OR
      ci.TextValue LIKE '%&#39;%'
  );

COMMIT;

