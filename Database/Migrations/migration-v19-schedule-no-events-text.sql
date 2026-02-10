-- Migration v19: Add schedule_no_events_text CMS item for storytelling schedule section
-- This removes hardcoded "No events scheduled" text and makes it CMS-editable

-- Add CMS item for no events text (CmsSectionId 36 = storytelling schedule_section)
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT 36, 'schedule_no_events_text', 'TEXT', 'No events scheduled'
WHERE NOT EXISTS (
    SELECT 1 FROM CmsItem WHERE CmsSectionId = 36 AND ItemKey = 'schedule_no_events_text'
);

-- ROLLBACK:
-- DELETE FROM CmsItem WHERE CmsSectionId = 36 AND ItemKey = 'schedule_no_events_text';

