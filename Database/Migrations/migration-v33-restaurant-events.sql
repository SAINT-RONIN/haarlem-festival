-- =============================================================================
-- Migration v33: Seed per-event CMS sections for restaurant events
--
-- Restaurant Event records (EventTypeId = 5) already exist in the Event table
-- (EventIds 47-53). This migration seeds CmsSection + CmsItem rows for each
-- event, migrating data from the Restaurant table columns into the CMS.
--
-- Prerequisites:
--   migration-v33-restaurant-prices-and-timeslots.sql must have run first
--   (adds TimeSlots and PriceAdult columns to Restaurant).
--
-- Safe to re-run: all inserts use INSERT IGNORE backed by unique constraints:
--   UQ_CmsSection (CmsPageId, SectionKey)
--   UQ_CmsItem    (CmsSectionId, ItemKey)
-- =============================================================================

-- Step 1: Register item keys in CmsItemKey (FK constraint on CmsItem.ItemKey).
-- INSERT IGNORE is safe — keys already registered are skipped.

INSERT IGNORE INTO CmsItemKey (ItemKey, ExpectedItemType) VALUES
-- Text fields
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
-- Link fields
('website',               'LINK'),
('map_embed_url',         'LINK'),
-- HTML fields
('about_text',            'HTML'),
('chef_text',             'HTML'),
('menu_description',      'HTML'),
('location_description',  'HTML'),
-- Image fields (for future use via CMS admin)
('about_image',           'IMAGE_PATH'),
('chef_image',            'IMAGE_PATH'),
('menu_image_1',          'IMAGE_PATH'),
('menu_image_2',          'IMAGE_PATH'),
('reservation_image',     'IMAGE_PATH');

-- Step 2: Seed CmsSection rows — one per restaurant event, under CmsPageId=6 ('restaurant').
-- Existing events: EventIds 47-53 (Café de Roemer, Ratatouille, ML, Fris, New Vegas, Brinkman, Toujours).

INSERT IGNORE INTO CmsSection (CmsPageId, SectionKey)
SELECT 6, CONCAT('restaurant_event_', e.EventId)
FROM Event e
WHERE e.EventTypeId = 5 AND e.IsActive = 1;

-- Step 2: Seed CmsItem rows for each restaurant event.
-- Join pattern: Event → Restaurant (for data) → CmsSection (for CmsSectionId FK).

-- ── Plain text fields ────────────────────────────────────────────────────────

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

-- time_slots and price_adult columns do not exist in the current DB schema.
-- Run migration-v33-restaurant-prices-and-timeslots.sql separately to add them,
-- then insert those CmsItem rows manually or via the CMS admin.

-- ── Link fields ──────────────────────────────────────────────────────────────

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

-- ── Numeric fields (stored as TEXT) ─────────────────────────────────────────

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

-- price_adult: seeded after migration-v33-restaurant-prices-and-timeslots.sql runs

-- ── HTML fields ──────────────────────────────────────────────────────────────

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

-- ── Image fields ─────────────────────────────────────────────────────────────
-- Images are not seeded here — add them via the CMS admin after migration.
-- Only Ratatouille and Toujours have images; the other 5 restaurants have none.
-- (The image column additions from migration-v32 were never run on this DB.)
