-- =============================================================================
-- Migration v34: Restaurant CMS — complete setup for teammates
--
-- Run this on any DB that is based on the March 23 complete-db.sql dump.
-- Safe to re-run: INSERT IGNORE + ADD COLUMN IF NOT EXISTS throughout.
--
-- What this does:
--   1. Adds TimeSlots, PriceAdult, PriceChild columns to Restaurant
--   2. Seeds time slots and default prices
--   3. Copies card images: Restaurant.ImageAssetId → Event.FeaturedImageAssetId
--   4. Registers restaurant-specific CmsItemKey entries
--   5. Creates per-event CmsSection rows (restaurant_event_{EventId})
--   6. Seeds text / HTML / link / numeric CmsItem rows from Restaurant
--   7. Seeds image CmsItem rows from RestaurantImage + MediaAsset
-- =============================================================================

-- ── 1. Add columns to Restaurant ─────────────────────────────────────────────

ALTER TABLE Restaurant
    ADD COLUMN IF NOT EXISTS TimeSlots  VARCHAR(200)  NULL AFTER SpecialRequestsNote,
    ADD COLUMN IF NOT EXISTS PriceAdult DECIMAL(10,2) NULL AFTER TimeSlots,
    ADD COLUMN IF NOT EXISTS PriceChild DECIMAL(10,2) NULL AFTER PriceAdult;

-- ── 2. Seed time slots and prices ────────────────────────────────────────────

UPDATE Restaurant SET TimeSlots = '17:00, 19:15, 21:30' WHERE Name LIKE '%Ratatouille%';
UPDATE Restaurant SET TimeSlots = '17:30, 19:15, 21:00' WHERE Name LIKE '%Toujours%';
UPDATE Restaurant SET PriceAdult = 45.00, PriceChild = 22.50 WHERE Name LIKE '%Ratatouille%';
UPDATE Restaurant SET PriceAdult = 35.00, PriceChild = 17.50 WHERE Name LIKE '%Toujours%';

-- ── 3. Copy card images from Restaurant to Event ─────────────────────────────

UPDATE Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
SET e.FeaturedImageAssetId = r.ImageAssetId
WHERE e.EventTypeId = 5
  AND e.FeaturedImageAssetId IS NULL
  AND r.ImageAssetId IS NOT NULL;

-- ── 4. Register CmsItemKey entries ───────────────────────────────────────────

INSERT IGNORE INTO CmsItemKey (ItemKey, ExpectedItemType) VALUES
('address_line',          'TEXT'),
('city',                  'TEXT'),
('phone',                 'TEXT'),
('email',                 'TEXT'),
('chef_name',             'TEXT'),
('cuisine_type',          'TEXT'),
('special_requests_note', 'TEXT'),
('stars',                 'TEXT'),
('michelin_stars',        'TEXT'),
('seats_per_session',     'TEXT'),
('duration_minutes',      'TEXT'),
('time_slots',            'TEXT'),
('price_adult',           'TEXT'),
('website',               'LINK'),
('map_embed_url',         'LINK'),
('about_text',            'HTML'),
('chef_text',             'HTML'),
('menu_description',      'HTML'),
('location_description',  'HTML'),
('about_image',           'IMAGE_PATH'),
('chef_image',            'IMAGE_PATH'),
('menu_image_1',          'IMAGE_PATH'),
('menu_image_2',          'IMAGE_PATH'),
('gallery_image_1',       'IMAGE_PATH'),
('gallery_image_2',       'IMAGE_PATH'),
('gallery_image_3',       'IMAGE_PATH'),
('reservation_image',     'IMAGE_PATH');

-- ── 5. Create per-event CmsSection rows ──────────────────────────────────────

INSERT IGNORE INTO CmsSection (CmsPageId, SectionKey)
SELECT 6, CONCAT('restaurant_event_', e.EventId)
FROM Event e
WHERE e.EventTypeId = 5 AND e.IsActive = 1;

-- ── 6. Seed text / HTML / link / numeric CmsItem rows ────────────────────────

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'address_line', 'TEXT', r.AddressLine
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.AddressLine IS NOT NULL AND r.AddressLine <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'city', 'TEXT', r.City
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.City IS NOT NULL AND r.City <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'phone', 'TEXT', r.Phone
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.Phone IS NOT NULL AND r.Phone <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'email', 'TEXT', r.Email
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.Email IS NOT NULL AND r.Email <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'chef_name', 'TEXT', r.ChefName
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.ChefName IS NOT NULL AND r.ChefName <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'cuisine_type', 'TEXT', r.CuisineType
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.CuisineType IS NOT NULL AND r.CuisineType <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'special_requests_note', 'TEXT', r.SpecialRequestsNote
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.SpecialRequestsNote IS NOT NULL AND r.SpecialRequestsNote <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'time_slots', 'TEXT', r.TimeSlots
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.TimeSlots IS NOT NULL AND r.TimeSlots <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'website', 'LINK', r.Website
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.Website IS NOT NULL AND r.Website <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'map_embed_url', 'LINK', r.MapEmbedUrl
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.MapEmbedUrl IS NOT NULL AND r.MapEmbedUrl <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'stars', 'TEXT', CAST(r.Stars AS CHAR)
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.Stars IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'michelin_stars', 'TEXT', CAST(r.MichelinStars AS CHAR)
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.MichelinStars IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'seats_per_session', 'TEXT', CAST(r.SeatsPerSession AS CHAR)
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.SeatsPerSession IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'duration_minutes', 'TEXT', CAST(r.DurationMinutes AS CHAR)
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.DurationMinutes IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'price_adult', 'TEXT', CAST(r.PriceAdult AS CHAR)
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.PriceAdult IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, HtmlValue)
SELECT cs.CmsSectionId, 'about_text', 'HTML', r.AboutText
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.AboutText IS NOT NULL AND r.AboutText <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, HtmlValue)
SELECT cs.CmsSectionId, 'chef_text', 'HTML', r.ChefText
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.ChefText IS NOT NULL AND r.ChefText <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, HtmlValue)
SELECT cs.CmsSectionId, 'menu_description', 'HTML', r.MenuDescription
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.MenuDescription IS NOT NULL AND r.MenuDescription <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, HtmlValue)
SELECT cs.CmsSectionId, 'location_description', 'HTML', r.LocationDescription
FROM Event e
JOIN Restaurant r  ON e.RestaurantId = r.RestaurantId
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.LocationDescription IS NOT NULL AND r.LocationDescription <> '';

-- ── 7. Seed image CmsItem rows ────────────────────────────────────────────────
-- RestaurantImage uses MediaAssetId → joined with MediaAsset for the file path.
-- Toujours (RestaurantId=2) has no reservation image → uses Ratatouille's.

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'about_image', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN RestaurantImage ri ON e.RestaurantId = ri.RestaurantId AND ri.ImageType = 'about'     AND ri.SortOrder = 1
JOIN MediaAsset ma      ON ri.MediaAssetId = ma.MediaAssetId
JOIN CmsSection cs      ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'chef_image', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN RestaurantImage ri ON e.RestaurantId = ri.RestaurantId AND ri.ImageType = 'chef'      AND ri.SortOrder = 1
JOIN MediaAsset ma      ON ri.MediaAssetId = ma.MediaAssetId
JOIN CmsSection cs      ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'menu_image_1', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN RestaurantImage ri ON e.RestaurantId = ri.RestaurantId AND ri.ImageType = 'menu'      AND ri.SortOrder = 1
JOIN MediaAsset ma      ON ri.MediaAssetId = ma.MediaAssetId
JOIN CmsSection cs      ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'menu_image_2', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN RestaurantImage ri ON e.RestaurantId = ri.RestaurantId AND ri.ImageType = 'menu'      AND ri.SortOrder = 2
JOIN MediaAsset ma      ON ri.MediaAssetId = ma.MediaAssetId
JOIN CmsSection cs      ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gallery_image_1', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN RestaurantImage ri ON e.RestaurantId = ri.RestaurantId AND ri.ImageType = 'gallery'   AND ri.SortOrder = 1
JOIN MediaAsset ma      ON ri.MediaAssetId = ma.MediaAssetId
JOIN CmsSection cs      ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gallery_image_2', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN RestaurantImage ri ON e.RestaurantId = ri.RestaurantId AND ri.ImageType = 'gallery'   AND ri.SortOrder = 2
JOIN MediaAsset ma      ON ri.MediaAssetId = ma.MediaAssetId
JOIN CmsSection cs      ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gallery_image_3', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN RestaurantImage ri ON e.RestaurantId = ri.RestaurantId AND ri.ImageType = 'gallery'   AND ri.SortOrder = 3
JOIN MediaAsset ma      ON ri.MediaAssetId = ma.MediaAssetId
JOIN CmsSection cs      ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5;

-- Reservation image — Ratatouille (RestaurantId=1) only; Toujours uses same image.
INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'reservation_image', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN RestaurantImage ri ON e.RestaurantId = ri.RestaurantId AND ri.ImageType = 'reservation' AND ri.SortOrder = 1
JOIN MediaAsset ma      ON ri.MediaAssetId = ma.MediaAssetId
JOIN CmsSection cs      ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5;

-- Toujours: no reservation image in RestaurantImage, so reuse Ratatouille's.
INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT
    cs.CmsSectionId,
    'reservation_image',
    'IMAGE_PATH',
    (SELECT ma2.FilePath
     FROM RestaurantImage ri2
     JOIN MediaAsset ma2 ON ri2.MediaAssetId = ma2.MediaAssetId
     WHERE ri2.RestaurantId = 1 AND ri2.ImageType = 'reservation'
     LIMIT 1)
FROM Event e
JOIN CmsSection cs ON cs.CmsPageId = 6 AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND e.RestaurantId = 2;
