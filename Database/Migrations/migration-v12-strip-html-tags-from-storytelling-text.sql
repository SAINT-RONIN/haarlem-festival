-- =====================================================
-- Migration v12: Strip HTML tags from TEXT CmsItem.TextValue (Storytelling page only)
-- Purpose: Some original Storytelling TEXT items contain HTML like <p>...</p>.
-- TEXT items should be plain text; HTML formatting belongs in ItemType=HTML.
--
-- Strategy:
-- - Only affects CmsPage.Slug = 'storytelling'
-- - Only affects ItemType = 'TEXT'
-- - Removes common formatting tags that frequently appear from rich text editors
-- =====================================================

START TRANSACTION;

UPDATE CmsItem ci
JOIN CmsSection cs ON cs.CmsSectionId = ci.CmsSectionId
JOIN CmsPage cp ON cp.CmsPageId = cs.CmsPageId
SET ci.TextValue = TRIM(
    REPLACE(
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(ci.TextValue, '<p>', ''),
                                    '</p>', ''),
                                '<strong>', ''),
                            '</strong>', ''),
                        '<em>', ''),
                    '</em>', ''),
                '<u>', ''),
            '</u>', ''),
        '<br>', ''),
    '<br/>', '')
)
WHERE cp.Slug = 'storytelling'
  AND ci.ItemType = 'TEXT'
  AND ci.TextValue IS NOT NULL
  AND ci.TextValue LIKE '%<%>%';

-- Remove <span ...>...</span> (best-effort without regex by removing opening/closing markers)
UPDATE CmsItem ci
JOIN CmsSection cs ON cs.CmsSectionId = ci.CmsSectionId
JOIN CmsPage cp ON cp.CmsPageId = cs.CmsPageId
SET ci.TextValue = TRIM(
    REPLACE(
        REPLACE(ci.TextValue, '</span>', ''),
        '<span', ''
    )
)
WHERE cp.Slug = 'storytelling'
  AND ci.ItemType = 'TEXT'
  AND ci.TextValue IS NOT NULL
  AND (ci.TextValue LIKE '%<span%' OR ci.TextValue LIKE '%</span>%');

COMMIT;

