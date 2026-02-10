-- =====================================================
-- Migration v14: Strip HTML tags from TEXT CmsItem.TextValue globally
-- Purpose: Cleanup legacy HTML markup that was accidentally stored in TEXT fields
-- (e.g. <p> wrappers from TinyMCE). TEXT fields must be plain.
-- =====================================================

START TRANSACTION;

UPDATE CmsItem
SET TextValue = TRIM(strip_tags(TextValue))
WHERE ItemType = 'TEXT'
  AND TextValue IS NOT NULL
  AND TextValue LIKE '%<%>%';

COMMIT;

