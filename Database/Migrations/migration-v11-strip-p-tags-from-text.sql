-- =====================================================
-- Migration v11: Strip TinyMCE-inserted <p> wrappers from TEXT CmsItem.TextValue
-- Background: TEXT fields should be plain text. A misconfiguration enabled TinyMCE for TEXT
-- which wrapped values in <p>..</p>. This migration cleans existing TEXT values.
-- =====================================================

START TRANSACTION;

-- Only touch TEXT items whose TextValue looks like a single <p>...</p> wrapper.
UPDATE CmsItem
SET TextValue = TRIM(BOTH '\n' FROM TRIM(BOTH '\r' FROM TRIM(BOTH ' ' FROM
    REPLACE(
        REPLACE(TextValue, '<p>', ''),
        '</p>', ''
    )
)))
WHERE ItemType = 'TEXT'
  AND TextValue IS NOT NULL
  AND TextValue LIKE '<p>%'
  AND TextValue LIKE '%</p>';

COMMIT;

