-- =====================================================
-- Migration v15: Strip <p> wrappers from Jazz TEXT items
-- Purpose: Jazz page still shows literal <p>..</p> because those tags are stored in TEXT values.
-- This fixes the visible issue by removing the wrappers for Jazz page only.
-- =====================================================

START TRANSACTION;

UPDATE CmsItem ci
JOIN CmsSection cs ON cs.CmsSectionId = ci.CmsSectionId
JOIN CmsPage cp ON cp.CmsPageId = cs.CmsPageId
SET ci.TextValue = TRIM(
    REPLACE(
        REPLACE(ci.TextValue, '<p>', ''),
        '</p>', ''
    )
)
WHERE cp.Slug = 'jazz'
  AND ci.ItemType = 'TEXT'
  AND ci.TextValue IS NOT NULL
  AND ci.TextValue LIKE '%<p>%';

COMMIT;

