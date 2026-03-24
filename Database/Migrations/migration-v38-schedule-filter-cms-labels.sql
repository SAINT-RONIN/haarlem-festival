-- migration-v38-schedule-filter-cms-labels.sql
--
-- Adds CMS items for schedule filter labels in the schedule_section of
-- the 'storytelling' and 'jazz' pages.
--
-- Shared filter labels (both pages):
--   schedule_filter_day_label, schedule_filter_all_label,
--   schedule_filter_reset_text
--
-- Storytelling-only filter labels:
--   schedule_filter_time_range_label, schedule_filter_morning_label,
--   schedule_filter_afternoon_label, schedule_filter_evening_label,
--   schedule_filter_price_type_label, schedule_filter_pay_as_you_like_label,
--   schedule_filter_fixed_price_label, schedule_filter_language_label,
--   schedule_filter_english_label, schedule_filter_dutch_label,
--   schedule_filter_age_group_label, schedule_filter_all_ages_label
--
-- Jazz-only filter labels:
--   schedule_filter_venue_label, schedule_filter_price_type_label,
--   schedule_filter_free_label, schedule_filter_paid_label
--
-- Safe to run multiple times: all INSERTs use NOT EXISTS guards.
-- ---------------------------------------------------------------

-- ---------------------------------------------------------------
-- Storytelling schedule_section filter labels
-- ---------------------------------------------------------------

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_day_label', 'TEXT', 'Day', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_day_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_all_label', 'TEXT', 'All', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_all_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_reset_text', 'TEXT', 'Reset all filters', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_reset_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_time_range_label', 'TEXT', 'Time Range', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_time_range_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_morning_label', 'TEXT', 'Morning (before 12:00)', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_morning_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_afternoon_label', 'TEXT', 'Afternoon (12:00 to 17:00)', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_afternoon_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_evening_label', 'TEXT', 'Evening (after 17:00)', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_evening_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_price_type_label', 'TEXT', 'Price Type', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_price_type_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_pay_as_you_like_label', 'TEXT', 'Pay as you like', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_pay_as_you_like_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_fixed_price_label', 'TEXT', 'Fixed Price', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_fixed_price_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_language_label', 'TEXT', 'Language', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_language_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_english_label', 'TEXT', 'English', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_english_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_dutch_label', 'TEXT', 'Dutch', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_dutch_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_age_group_label', 'TEXT', 'Age Group', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_age_group_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_all_ages_label', 'TEXT', 'All ages', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_all_ages_label');

-- ---------------------------------------------------------------
-- Jazz schedule_section filter labels
-- ---------------------------------------------------------------

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_day_label', 'TEXT', 'Day', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_day_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_all_label', 'TEXT', 'All', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_all_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_reset_text', 'TEXT', 'Reset all filters', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_reset_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_venue_label', 'TEXT', 'Venue', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_venue_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_price_type_label', 'TEXT', 'Price Type', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_price_type_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_free_label', 'TEXT', 'Free', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_free_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'schedule_filter_paid_label', 'TEXT', 'Paid', NOW()
FROM CmsSection cs INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'jazz' AND cs.SectionKey = 'schedule_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'schedule_filter_paid_label');
