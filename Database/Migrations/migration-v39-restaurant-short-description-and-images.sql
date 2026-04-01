-- =============================================================================
-- Migration v39: Populate Event.ShortDescription + re-seed CMS image items
--                for restaurant events from Restaurant direct path columns.
--
-- Why this is needed:
--   - Event.ShortDescription was never populated for restaurant events;
--     the restaurant listing cards read from this column.
--   - migration-v34 seeded images from the RestaurantImage table, but the
--     actual image paths live in direct Restaurant columns (AboutImagePath,
--     ChefImagePath, etc.) added by migration-v32.
--
-- Safe to re-run:
--   - UPDATE on Event uses WHERE ShortDescription IS NULL OR = '' guard.
--   - CmsItem inserts use ON DUPLICATE KEY UPDATE so they overwrite stale
--     empty values left by v33/v34 without creating duplicate rows.
-- =============================================================================

-- ── 1. Populate Event.ShortDescription from Restaurant.DescriptionHtml ────────
--       REGEXP_REPLACE strips all HTML tags, leaving plain text for the card.

UPDATE Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
SET e.ShortDescription = TRIM(REGEXP_REPLACE(r.DescriptionHtml, '<[^>]+>', ''))
WHERE e.EventTypeId = 5
  AND r.DescriptionHtml IS NOT NULL
  AND r.DescriptionHtml <> '';

-- ── 2. Re-seed CMS image items from Restaurant direct path columns ─────────────
--       Uses ON DUPLICATE KEY UPDATE to overwrite any empty rows left by v33/v34.

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'about_image', 'IMAGE_PATH', r.AboutImagePath
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.AboutImagePath IS NOT NULL AND r.AboutImagePath <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'chef_image', 'IMAGE_PATH', r.ChefImagePath
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.ChefImagePath IS NOT NULL AND r.ChefImagePath <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'menu_image_1', 'IMAGE_PATH', r.MenuImage1Path
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.MenuImage1Path IS NOT NULL AND r.MenuImage1Path <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'menu_image_2', 'IMAGE_PATH', r.MenuImage2Path
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.MenuImage2Path IS NOT NULL AND r.MenuImage2Path <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gallery_image_1', 'IMAGE_PATH', r.GalleryImage1Path
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.GalleryImage1Path IS NOT NULL AND r.GalleryImage1Path <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gallery_image_2', 'IMAGE_PATH', r.GalleryImage2Path
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.GalleryImage2Path IS NOT NULL AND r.GalleryImage2Path <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gallery_image_3', 'IMAGE_PATH', r.GalleryImage3Path
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.GalleryImage3Path IS NOT NULL AND r.GalleryImage3Path <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'reservation_image', 'IMAGE_PATH', r.ReservationImagePath
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.ReservationImagePath IS NOT NULL AND r.ReservationImagePath <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);
