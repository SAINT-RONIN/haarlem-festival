-- =====================================================
-- Migration v18: Global Schedule Columns
-- Adds SeatsAvailable (Jazz), HistoryTicketLabel (History)
-- Safe & Idempotent
-- Date: February 10, 2026
-- =====================================================

-- =====================================================
-- PART 1: Add SeatsAvailable column to EventSession (Jazz requirement)
-- =====================================================

SET @colExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'EventSession' AND COLUMN_NAME = 'SeatsAvailable');
SET @sqlStmt = IF(@colExists = 0, 'ALTER TABLE EventSession ADD COLUMN SeatsAvailable INT DEFAULT NULL AFTER CapacitySingleTicketLimit', 'SELECT 1');
PREPARE stmt FROM @sqlStmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- PART 2: Add HistoryTicketLabel column to EventSession (History requirement)
-- =====================================================

SET @colExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'EventSession' AND COLUMN_NAME = 'HistoryTicketLabel');
SET @sqlStmt = IF(@colExists = 0, 'ALTER TABLE EventSession ADD COLUMN HistoryTicketLabel VARCHAR(120) DEFAULT NULL AFTER CtaUrl', 'SELECT 1');
PREPARE stmt FROM @sqlStmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- PART 3: Create global schedule_section for each event type page
-- (Storytelling already has one from v17, add others)
-- =====================================================

-- Jazz schedule section
INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'schedule_section'
FROM CmsPage cp WHERE cp.Slug = 'jazz'
AND NOT EXISTS (
    SELECT 1 FROM CmsSection cs
    WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'schedule_section'
);

-- Jazz schedule CMS items
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_title', 'HEADING', 'Jazz schedule', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_year', 'TEXT', '2026', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_year');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filters_button_text', 'BUTTON_TEXT', 'Filters', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filters_button_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_show_filters', 'TEXT', '1', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_show_filters');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_show_additional_info', 'TEXT', '0', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_show_additional_info');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_show_event_count', 'TEXT', '1', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_show_event_count');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_event_count_label', 'TEXT', 'Performances', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_event_count_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Book Now', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_cta_button_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_pay_what_you_like_text', 'TEXT', 'Pay as you like', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_pay_what_you_like_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_currency_symbol', 'TEXT', '€', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_currency_symbol');

