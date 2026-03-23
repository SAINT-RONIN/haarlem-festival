-- =====================================================
-- Migration v17: Storytelling Schedule Section
-- Run after migration-v16
-- Safe & Idempotent
-- Date: February 10, 2026
-- =====================================================

-- =====================================================
-- PART 1: Add CtaLabel and CtaUrl columns to EventSession
-- =====================================================

SET @colExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'EventSession' AND COLUMN_NAME = 'CtaLabel');
SET @sqlStmt = IF(@colExists = 0, 'ALTER TABLE EventSession ADD COLUMN CtaLabel VARCHAR(60) DEFAULT NULL AFTER Notes', 'SELECT 1');
PREPARE stmt FROM @sqlStmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @colExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'EventSession' AND COLUMN_NAME = 'CtaUrl');
SET @sqlStmt = IF(@colExists = 0, 'ALTER TABLE EventSession ADD COLUMN CtaUrl VARCHAR(255) DEFAULT NULL AFTER CtaLabel', 'SELECT 1');
PREPARE stmt FROM @sqlStmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- PART 2: Create schedule_section for Storytelling page
-- =====================================================

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'schedule_section'
FROM CmsPage cp WHERE cp.Slug = 'storytelling'
AND NOT EXISTS (
    SELECT 1 FROM CmsSection cs
    WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'schedule_section'
);

-- =====================================================
-- PART 3: Insert Schedule Section CMS Items
-- =====================================================

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_title', 'HEADING', 'Storytelling schedule', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_year', 'TEXT', '2026', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_year');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filters_button_text', 'BUTTON_TEXT', 'Filters', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filters_button_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_additional_info_title', 'TEXT', 'Additional Information:', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_additional_info_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, HtmlValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_additional_info_body', 'HTML', 'Some storytelling events are pay-what-you-like, meaning they are free to attend, with optional contributions that support the actor.<br/>After reserving your spot, visit your My Program page to choose how much you would like to contribute to each pay-what-you-like event.', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_additional_info_body');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_show_filters', 'TEXT', '1', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_show_filters');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_show_additional_info', 'TEXT', '1', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_show_additional_info');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_show_story_count', 'TEXT', '1', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_show_story_count');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_story_count_label', 'TEXT', 'Stories', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_story_count_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Discover', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_cta_button_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_pay_what_you_like_text', 'TEXT', 'Pay as you like', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_pay_what_you_like_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_currency_symbol', 'TEXT', '€', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_currency_symbol');

-- =====================================================
-- PART 4: EventSessionLabel - NOT SEEDED
-- (Matches repo convention - editors manage via CMS)
-- =====================================================

