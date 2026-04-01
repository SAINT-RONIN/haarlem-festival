-- =============================================================================
-- migration-yummy-adapted-for-group-db.sql
-- Adapted Yummy! migration for the group DB (haarlem_festival_db (19).sql).
--
-- Differences from migration-yummy-complete-v2.sql:
--   • Image path columns (AboutImagePath, ChefImagePath, etc.) are NOT added
--     because the group DB already uses AssetId FK columns on Restaurant
--     (AboutImageAssetId, ChefImageAssetId, GalleryImage1AssetId, etc.)
--   • RestaurantImage table is NOT dropped or recreated — it already exists
--     with the MediaAssetId FK approach and 16 populated rows
--   • Ratatouille / Toujours UPDATE only sets the three missing columns
--     (TimeSlots, PriceAdult, PriceChild); all other text content is already
--     present in the group DB
--   • Per-event CmsItem image rows in Part G use a MediaAsset JOIN instead of
--     r.AboutImagePath (which does not exist in the group DB)
--   • Everything else is idempotent: ADD COLUMN IF NOT EXISTS, CREATE TABLE IF
--     NOT EXISTS, WHERE NOT EXISTS, INSERT IGNORE
-- =============================================================================

-- ═══════════════════════════════════════════════════════════════════════════
-- PART A: Restaurant column additions
--   Detail columns (Phone … SpecialRequestsNote) already exist → skipped.
--   TimeSlots / PriceAdult / PriceChild are new.
-- ═══════════════════════════════════════════════════════════════════════════

ALTER TABLE Restaurant
    ADD COLUMN IF NOT EXISTS Phone                VARCHAR(50)   NULL AFTER DescriptionHtml,
    ADD COLUMN IF NOT EXISTS Email                VARCHAR(150)  NULL AFTER Phone,
    ADD COLUMN IF NOT EXISTS Website              VARCHAR(255)  NULL AFTER Email,
    ADD COLUMN IF NOT EXISTS AboutText            TEXT          NULL AFTER Website,
    ADD COLUMN IF NOT EXISTS ChefName             VARCHAR(150)  NULL AFTER AboutText,
    ADD COLUMN IF NOT EXISTS ChefText             TEXT          NULL AFTER ChefName,
    ADD COLUMN IF NOT EXISTS MenuDescription      TEXT          NULL AFTER ChefText,
    ADD COLUMN IF NOT EXISTS LocationDescription  TEXT          NULL AFTER MenuDescription,
    ADD COLUMN IF NOT EXISTS MapEmbedUrl          VARCHAR(1024) NULL AFTER LocationDescription,
    ADD COLUMN IF NOT EXISTS MichelinStars        INT           NULL AFTER MapEmbedUrl,
    ADD COLUMN IF NOT EXISTS SeatsPerSession      INT           NULL AFTER MichelinStars,
    ADD COLUMN IF NOT EXISTS DurationMinutes      INT           NULL AFTER SeatsPerSession,
    ADD COLUMN IF NOT EXISTS SpecialRequestsNote  VARCHAR(500)  NULL AFTER DurationMinutes,
    ADD COLUMN IF NOT EXISTS TimeSlots            TEXT          NULL AFTER SpecialRequestsNote,
    ADD COLUMN IF NOT EXISTS PriceAdult           DECIMAL(10,2) NULL AFTER TimeSlots,
    ADD COLUMN IF NOT EXISTS PriceChild           DECIMAL(10,2) NULL AFTER PriceAdult;

-- ═══════════════════════════════════════════════════════════════════════════
-- PART A2: Event prerequisites (Slug column + 7 restaurant event rows)
--   All 7 rows already exist in the group DB; guards prevent re-insertion.
-- ═══════════════════════════════════════════════════════════════════════════

ALTER TABLE Event
    ADD COLUMN IF NOT EXISTS Slug VARCHAR(180) NULL AFTER Title;

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, RestaurantId, IsActive, CreatedAtUtc)
SELECT 47, 5, 'Café de Roemer - Festival Dinner', 'Dutch, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', r.RestaurantId, 1, '2026-02-06 15:30:32'
FROM Restaurant r WHERE r.Name = 'Café de Roemer'
AND NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 47);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, RestaurantId, IsActive, CreatedAtUtc)
SELECT 48, 5, 'Ratatouille - Festival Dinner', 'French, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', r.RestaurantId, 1, '2026-02-06 15:30:32'
FROM Restaurant r WHERE r.Name = 'Ratatouille'
AND NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 48);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, RestaurantId, IsActive, CreatedAtUtc)
SELECT 49, 5, 'Restaurant ML - Festival Dinner', 'Dutch, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', r.RestaurantId, 1, '2026-02-06 15:30:32'
FROM Restaurant r WHERE r.Name = 'Restaurant ML'
AND NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 49);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, RestaurantId, IsActive, CreatedAtUtc)
SELECT 50, 5, 'Restaurant Fris - Festival Dinner', 'Dutch, French, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', r.RestaurantId, 1, '2026-02-06 15:30:32'
FROM Restaurant r WHERE r.Name = 'Restaurant Fris'
AND NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 50);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, RestaurantId, IsActive, CreatedAtUtc)
SELECT 51, 5, 'New Vegas - Festival Dinner', 'Vegan cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', r.RestaurantId, 1, '2026-02-06 15:30:32'
FROM Restaurant r WHERE r.Name = 'New Vegas'
AND NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 51);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, RestaurantId, IsActive, CreatedAtUtc)
SELECT 52, 5, 'Grand Cafe Brinkman - Festival Dinner', 'Dutch, European, Modern cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', r.RestaurantId, 1, '2026-02-06 15:30:32'
FROM Restaurant r WHERE r.Name = 'Grand Cafe Brinkman'
AND NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 52);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, RestaurantId, IsActive, CreatedAtUtc)
SELECT 53, 5, 'Urban Frenchy Bistro Toujours - Festival Dinner', 'Dutch, fish and seafood, European cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', r.RestaurantId, 1, '2026-02-06 15:30:32'
FROM Restaurant r WHERE r.Name = 'Urban Frenchy Bistro Toujours'
AND NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 53);

-- Backfill Slug for any restaurant event that does not have one yet
UPDATE Event
SET Slug = LOWER(TRIM(BOTH '-' FROM REGEXP_REPLACE(Title, '[^a-z0-9]+', '-')))
WHERE EventTypeId = 5 AND (Slug IS NULL OR Slug = '');

-- ═══════════════════════════════════════════════════════════════════════════
-- PART B: Reservation table
-- ═══════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `Reservation` (
    `ReservationId`   INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `RestaurantId`    INT           NOT NULL,
    `DiningDate`      VARCHAR(20)   NOT NULL COMMENT 'Thursday / Friday / Saturday / Sunday',
    `TimeSlot`        VARCHAR(20)   NOT NULL COMMENT 'e.g. 16:30',
    `AdultsCount`     INT UNSIGNED  NOT NULL DEFAULT 0,
    `ChildrenCount`   INT UNSIGNED  NOT NULL DEFAULT 0,
    `SpecialRequests` TEXT          NULL,
    `TotalFee`        DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '10 per person reservation deposit',
    `CreatedAt`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ReservationId`),
    INDEX `idx_reservation_restaurant` (`RestaurantId`),
    CONSTRAINT `FK_Reservation_Restaurant`
        FOREIGN KEY (`RestaurantId`) REFERENCES `Restaurant` (`RestaurantId`)
            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ═══════════════════════════════════════════════════════════════════════════
-- PART C: MediaAsset entries (all already present in group DB; guards ensure
--         no duplicates are inserted)
-- ═══════════════════════════════════════════════════════════════════════════

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/Restaurant-CafeDeRoemer-card.png', 'Restaurant-CafeDeRoemer-card.png', 'image/png', 0, 'Cafe de Roemer'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-CafeDeRoemer-card.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/Restaurant-Ratatouille-card.png', 'Restaurant-Ratatouille-card.png', 'image/png', 0, 'Ratatouille'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-Ratatouille-card.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/Restaurant-RestaurantML-card.png', 'Restaurant-RestaurantML-card.png', 'image/png', 0, 'Restaurant ML'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-RestaurantML-card.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/Restaurant-RestaurantFris-card.png', 'Restaurant-RestaurantFris-card.png', 'image/png', 0, 'Restaurant Fris'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-RestaurantFris-card.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/Restaurant-NewVegas-card.jpg', 'Restaurant-NewVegas-card.jpg', 'image/jpeg', 0, 'New Vegas'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-NewVegas-card.jpg');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/Restaurant-GrandCafeBrinkman-card.png', 'Restaurant-GrandCafeBrinkman-card.png', 'image/png', 0, 'Grand Cafe Brinkman'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-GrandCafeBrinkman-card.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/Restaurant-UrbanFrenchyBistroToujours-card.png', 'Restaurant-UrbanFrenchyBistroToujours-card.png', 'image/png', 0, 'Urban Frenchy Bistro Toujours'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-UrbanFrenchyBistroToujours-card.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/ratatouille-about.png', 'ratatouille-about.png', 'image/png', 0, 'Ratatouille About'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-about.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/ratatouille-chef.png', 'ratatouille-chef.png', 'image/png', 0, 'Ratatouille Chef'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-chef.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/ratatouille-menu-1.png', 'ratatouille-menu-1.png', 'image/png', 0, 'Ratatouille Menu 1'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-menu-1.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/ratatouille-menu-2.png', 'ratatouille-menu-2.png', 'image/png', 0, 'Ratatouille Menu 2'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-menu-2.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/ratatouille-gallery-1.png', 'ratatouille-gallery-1.png', 'image/png', 0, 'Ratatouille Gallery 1'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-gallery-1.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/ratatouille-gallery-2.png', 'ratatouille-gallery-2.png', 'image/png', 0, 'Ratatouille Gallery 2'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-gallery-2.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/ratatouille-gallery-3.png', 'ratatouille-gallery-3.png', 'image/png', 0, 'Ratatouille Gallery 3'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-gallery-3.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/ratatouille-reservation.jpg', 'ratatouille-reservation.jpg', 'image/jpeg', 0, 'Ratatouille Reservation'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-reservation.jpg');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-about.png', 'toujours-about.png', 'image/png', 0, 'Toujours About'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-about.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-chef.jpg', 'toujours-chef.jpg', 'image/jpeg', 0, 'Toujours Chef'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-chef.jpg');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-menu-1.png', 'toujours-menu-1.png', 'image/png', 0, 'Toujours Menu 1'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-menu-1.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-menu-2.png', 'toujours-menu-2.png', 'image/png', 0, 'Toujours Menu 2'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-menu-2.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-gallery-1.png', 'toujours-gallery-1.png', 'image/png', 0, 'Toujours Gallery 1'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-gallery-1.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-gallery-2.png', 'toujours-gallery-2.png', 'image/png', 0, 'Toujours Gallery 2'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-gallery-2.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-gallery-3.png', 'toujours-gallery-3.png', 'image/png', 0, 'Toujours Gallery 3'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-gallery-3.png');

-- ═══════════════════════════════════════════════════════════════════════════
-- PART D: Restaurant data
-- ═══════════════════════════════════════════════════════════════════════════

-- Card image links (already set in group DB; guard prevents overwrite)
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-CafeDeRoemer-card.png'
SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'Café de Roemer' AND r.ImageAssetId IS NULL;

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-Ratatouille-card.png'
SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'Ratatouille' AND r.ImageAssetId IS NULL;

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-RestaurantML-card.png'
SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'Restaurant ML' AND r.ImageAssetId IS NULL;

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-RestaurantFris-card.png'
SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'Restaurant Fris' AND r.ImageAssetId IS NULL;

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-NewVegas-card.jpg'
SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'New Vegas' AND r.ImageAssetId IS NULL;

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-GrandCafeBrinkman-card.png'
SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'Grand Cafe Brinkman' AND r.ImageAssetId IS NULL;

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-UrbanFrenchyBistroToujours-card.png'
SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours' AND r.ImageAssetId IS NULL;

-- TimeSlots / PriceAdult / PriceChild for Ratatouille
-- (all other text content already present in group DB)
UPDATE Restaurant SET
    TimeSlots  = '17:00, 19:15, 21:30',
    PriceAdult = 45.00,
    PriceChild = 22.50
WHERE Name = 'Ratatouille';

-- TimeSlots / PriceAdult / PriceChild for Toujours
UPDATE Restaurant SET
    TimeSlots  = '17:30, 19:15, 21:00',
    PriceAdult = 45.00,
    PriceChild = 22.50
WHERE Name = 'Urban Frenchy Bistro Toujours';

-- Default time slots and prices for all other active restaurants
UPDATE Restaurant SET TimeSlots = '16:30, 18:30, 20:30'
WHERE IsActive = 1 AND (TimeSlots IS NULL OR TimeSlots = '');

UPDATE Restaurant SET PriceAdult = 45.00, PriceChild = 22.50
WHERE IsActive = 1 AND PriceAdult IS NULL;

-- ═══════════════════════════════════════════════════════════════════════════
-- PART E: Event updates
-- ═══════════════════════════════════════════════════════════════════════════

-- Copy card image from Restaurant to Event.FeaturedImageAssetId
UPDATE Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
SET e.FeaturedImageAssetId = r.ImageAssetId
WHERE e.EventTypeId = 5
  AND e.FeaturedImageAssetId IS NULL
  AND r.ImageAssetId IS NOT NULL;

-- Populate ShortDescription from Restaurant.DescriptionHtml (strips HTML tags)
UPDATE Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
SET e.ShortDescription = TRIM(REGEXP_REPLACE(r.DescriptionHtml, '<[^>]+>', ''))
WHERE e.EventTypeId = 5
  AND r.DescriptionHtml IS NOT NULL
  AND r.DescriptionHtml <> '';

-- ═══════════════════════════════════════════════════════════════════════════
-- PART F-PRE: Register ALL CmsItemKey entries used in Parts F and G
--   Must run before any CmsItem INSERT due to FK_CmsItem_ItemKey constraint.
-- ═══════════════════════════════════════════════════════════════════════════

INSERT IGNORE INTO CmsItemKey (ItemKey, ExpectedItemType) VALUES
-- Hero section keys
('hero_main_title',               'HEADING'),
('hero_subtitle',                 'TEXT'),
('hero_button_primary',           'BUTTON_TEXT'),
('hero_button_primary_link',      'LINK'),
('hero_button_secondary',         'BUTTON_TEXT'),
('hero_button_secondary_link',    'LINK'),
('hero_background_image',         'IMAGE_PATH'),
-- Gradient section keys
('gradient_heading',              'HEADING'),
('gradient_subheading',           'TEXT'),
('gradient_background_image',     'IMAGE_PATH'),
-- Intro split section keys
('intro_heading',                 'HEADING'),
('intro_body',                    'TEXT'),
('intro_sub1_heading',            'TEXT'),
('intro_sub1_text',               'TEXT'),
('intro_sub2_heading',            'TEXT'),
('intro_sub2_text',               'TEXT'),
('intro_sub3_heading',            'TEXT'),
('intro_sub3_text',               'TEXT'),
('intro_closing',                 'TEXT'),
('intro_image',                   'IMAGE_PATH'),
('intro_image_alt',               'TEXT'),
-- Intro split 2 section keys
('intro2_heading',                'HEADING'),
('intro2_body',                   'TEXT'),
('intro2_image',                  'IMAGE_PATH'),
('intro2_image_alt',              'TEXT'),
-- Instructions section keys
('instructions_title',            'HEADING'),
('instructions_card_1_title',     'HEADING'),
('instructions_card_1_text',      'TEXT'),
('instructions_card_2_title',     'HEADING'),
('instructions_card_2_text',      'TEXT'),
('instructions_card_3_title',     'HEADING'),
('instructions_card_3_text',      'TEXT'),
-- Restaurant cards section keys
('cards_title',                   'HEADING'),
('cards_subtitle',                'TEXT'),
-- Detail section shared label keys
('detail_hero_subtitle_template', 'TEXT'),
('detail_hero_btn_primary',       'BUTTON_TEXT'),
('detail_hero_btn_secondary',     'BUTTON_TEXT'),
('detail_contact_title',          'HEADING'),
('detail_label_address',          'TEXT'),
('detail_label_contact',          'TEXT'),
('detail_label_open_hours',       'TEXT'),
('detail_practical_title',        'HEADING'),
('detail_label_price_food',       'TEXT'),
('detail_label_rating',           'TEXT'),
('detail_label_special_requests', 'TEXT'),
('detail_label_festival_rated',   'TEXT'),
('detail_label_michelin',         'TEXT'),
('detail_gallery_title',          'HEADING'),
('detail_about_title_prefix',     'HEADING'),
('detail_chef_title',             'HEADING'),
('detail_menu_title',             'HEADING'),
('detail_menu_cuisine_label',     'TEXT'),
('detail_location_title',         'HEADING'),
('detail_location_address_label', 'TEXT'),
('detail_map_fallback_text',      'TEXT'),
('detail_reservation_title',      'HEADING'),
('detail_reservation_description','TEXT'),
('detail_reservation_slots_label','TEXT'),
('detail_reservation_note',       'TEXT'),
('detail_reservation_btn',        'BUTTON_TEXT'),
('detail_label_duration',         'TEXT'),
('detail_label_seats',            'TEXT'),
-- Per-event item keys (Part G)
('address_line',                  'TEXT'),
('city',                          'TEXT'),
('phone',                         'TEXT'),
('email',                         'TEXT'),
('chef_name',                     'TEXT'),
('cuisine_type',                  'TEXT'),
('special_requests_note',         'TEXT'),
('stars',                         'TEXT'),
('michelin_stars',                'TEXT'),
('seats_per_session',             'TEXT'),
('duration_minutes',              'TEXT'),
('time_slots',                    'TEXT'),
('price_adult',                   'TEXT'),
('website',                       'LINK'),
('map_embed_url',                 'LINK'),
('about_text',                    'HTML'),
('chef_text',                     'HTML'),
('menu_description',              'HTML'),
('location_description',          'HTML'),
('about_image',                   'IMAGE_PATH'),
('chef_image',                    'IMAGE_PATH'),
('menu_image_1',                  'IMAGE_PATH'),
('menu_image_2',                  'IMAGE_PATH'),
('gallery_image_1',               'IMAGE_PATH'),
('gallery_image_2',               'IMAGE_PATH'),
('gallery_image_3',               'IMAGE_PATH'),
('reservation_image',             'IMAGE_PATH');

-- ═══════════════════════════════════════════════════════════════════════════
-- PART F: CMS listing page
--   CmsPage, CmsSections, and all CmsItems for page slug 'restaurant'
--   already exist in the group DB; WHERE NOT EXISTS guards ensure safety.
-- ═══════════════════════════════════════════════════════════════════════════

INSERT INTO CmsPage (Slug, Title)
SELECT 'restaurant', 'Yummy! Restaurant Experience'
WHERE NOT EXISTS (SELECT 1 FROM CmsPage WHERE Slug = 'restaurant');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'hero_section' FROM CmsPage cp WHERE cp.Slug = 'restaurant'
AND NOT EXISTS (SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'hero_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'gradient_section' FROM CmsPage cp WHERE cp.Slug = 'restaurant'
AND NOT EXISTS (SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'gradient_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'intro_split_section' FROM CmsPage cp WHERE cp.Slug = 'restaurant'
AND NOT EXISTS (SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'intro_split_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'intro_split2_section' FROM CmsPage cp WHERE cp.Slug = 'restaurant'
AND NOT EXISTS (SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'intro_split2_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'instructions_section' FROM CmsPage cp WHERE cp.Slug = 'restaurant'
AND NOT EXISTS (SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'instructions_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'restaurant_cards_section' FROM CmsPage cp WHERE cp.Slug = 'restaurant'
AND NOT EXISTS (SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_cards_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'detail_section' FROM CmsPage cp WHERE cp.Slug = 'restaurant'
AND NOT EXISTS (SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'detail_section');

-- Hero section items
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'hero_main_title', 'HEADING', 'Yummy Gourmet with a Twist'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_main_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'hero_subtitle', 'TEXT', 'Discover 7 gourmet restaurants offering exclusive festival
menus crafted by top local chefs.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_subtitle');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'hero_button_primary', 'BUTTON_TEXT', 'Discover restaurants'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_primary');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'hero_button_primary_link', 'LINK', '#restaurants-grid'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_primary_link');

-- Fix old '#restaurants' value if present
UPDATE CmsItem ci
JOIN CmsSection cs ON ci.CmsSectionId = cs.CmsSectionId
JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '#restaurants-grid'
WHERE ci.ItemKey = 'hero_button_primary_link' AND ci.TextValue = '#restaurants'
  AND cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section';

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'hero_button_secondary', 'BUTTON_TEXT', 'About Yummy'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_secondary');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'hero_button_secondary_link', 'LINK', '#about'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_secondary_link');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/restaurants/hero-picture.png'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_background_image');

-- Gradient section items
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gradient_heading', 'HEADING', 'Good food tastes better when shared.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'gradient_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'gradient_heading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gradient_subheading', 'TEXT', 'Food, stories, and shared moments across Haarlem.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'gradient_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'gradient_subheading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gradient_background_image', 'IMAGE_PATH', '/assets/Image/restaurants/chef-preparing-food.png'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'gradient_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'gradient_background_image');

-- Intro split section items
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro_heading', 'HEADING', 'Yummy! at the Heart of the Festival'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_heading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro_body', 'TEXT', 'Welcome to Yummy!, the food experience of the Haarlem Festival.
Four days where some of the city''s favorite restaurants open their doors with special menus made just for this event.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_body');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro_sub1_heading', 'TEXT', 'What is Yummy?'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_sub1_heading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro_sub1_text', 'TEXT', 'A festival of food where each restaurant offers one unique menu, set time slots, and special prices.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_sub1_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro_sub2_heading', 'TEXT', 'Who takes part?'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_sub2_heading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro_sub2_text', 'TEXT', 'Local chefs and restaurants from all around Haarlem, prepare with their own style a great variety of dishes, such as: Dutch-French-European-Fish & Seafood-Modern Vegan.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_sub2_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro_sub3_heading', 'TEXT', 'How does it work?'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_sub3_heading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro_sub3_text', 'TEXT', 'Choose a restaurant, pick a time slot, and make a reservation. Seats are limited, so booking ahead is important.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_sub3_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro_closing', 'TEXT', 'Come enjoy great food, good company, and a warm festival atmosphere.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_closing');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro_image', 'IMAGE_PATH', '/assets/Image/restaurants/table-with-food-and-drink.png'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_image');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro_image_alt', 'TEXT', 'Yummy! at the Heart of the Festival'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_image_alt');

-- Intro split 2 section items
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro2_heading', 'HEADING', 'When Haarlem Becomes a Dining Room'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split2_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro2_heading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro2_body', 'TEXT', 'As the sun sets over Haarlem''s historic streets, the city slowly turns into one big dining room.

From Thursday to Sunday, each restaurant offers 2 to 3 sessions later afternoon, starting from 16:30 and lasting around 1.5 to 2 hours.

Just enough time to enjoy your plate, share a toast, and wander to the next event or performance nearby.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split2_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro2_body');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro2_image', 'IMAGE_PATH', '/assets/Image/restaurants/food-in-canal.png'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split2_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro2_image');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'intro2_image_alt', 'TEXT', 'When Haarlem Becomes a Dining Room'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split2_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro2_image_alt');

-- Instructions section items
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'instructions_title', 'HEADING', 'How reservations work'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'instructions_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'instructions_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'instructions_card_1_title', 'HEADING', 'Browse'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'instructions_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'instructions_card_1_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'instructions_card_1_text', 'TEXT', 'Explore participating restaurants and their exclusive festival menus.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'instructions_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'instructions_card_1_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'instructions_card_2_title', 'HEADING', 'Choose'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'instructions_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'instructions_card_2_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'instructions_card_2_text', 'TEXT', 'Pick a date and time slot that fits your schedule.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'instructions_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'instructions_card_2_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'instructions_card_3_title', 'HEADING', 'Reserve'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'instructions_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'instructions_card_3_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'instructions_card_3_text', 'TEXT', 'Complete your booking and receive a confirmation. Done!'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'instructions_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'instructions_card_3_text');

-- Restaurant cards section items
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'cards_title', 'HEADING', 'Explore the participant restaurants'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'restaurant_cards_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'cards_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'cards_subtitle', 'TEXT', 'Discover all restaurants participating in Yummy! Each one offers a special festival menu, unique flavors, and limited time slots throughout the weekend.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'restaurant_cards_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'cards_subtitle');

-- Detail section items (shared labels)
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_hero_subtitle_template', 'TEXT', '{cuisine}\nRelax, explore, and let {name} make your evening something truly special.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_hero_subtitle_template');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_hero_btn_primary', 'BUTTON_TEXT', 'Make a reservation'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_hero_btn_primary');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_hero_btn_secondary', 'BUTTON_TEXT', 'Back to restaurant'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_hero_btn_secondary');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_contact_title', 'HEADING', 'Contact'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_contact_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_label_address', 'TEXT', 'ADDRESS'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_label_address');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_label_contact', 'TEXT', 'CONTACT'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_label_contact');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_label_open_hours', 'TEXT', 'OPEN HOURS FOR YUMMY'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_label_open_hours');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_practical_title', 'HEADING', 'Practical Info'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_practical_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_label_price_food', 'TEXT', 'PRICE AND FOOD'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_label_price_food');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_label_rating', 'TEXT', 'RESTAURANT RATING'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_label_rating');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_label_special_requests', 'TEXT', 'SPECIAL REQUESTS'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_label_special_requests');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_label_festival_rated', 'TEXT', 'Festival-rated'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_label_festival_rated');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_label_michelin', 'TEXT', 'Michelin-star'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_label_michelin');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_gallery_title', 'HEADING', 'Restaurant Gallery'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_gallery_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_about_title_prefix', 'HEADING', 'About'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_about_title_prefix');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_chef_title', 'HEADING', 'Chef & Philosophy'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_chef_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_menu_title', 'HEADING', 'Menu Style'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_menu_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_menu_cuisine_label', 'TEXT', 'Cuisine type:'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_menu_cuisine_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_location_title', 'HEADING', 'Location'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_location_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_location_address_label', 'TEXT', 'Address'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_location_address_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_map_fallback_text', 'TEXT', 'Map coming soon'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_map_fallback_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_reservation_title', 'HEADING', 'Make your Reservation'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_reservation_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_reservation_description', 'TEXT', 'Choose a time slot that suits your evening. When you''re ready to book, continue to the next screen to confirm your guests and add any special requests.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_reservation_description');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_reservation_slots_label', 'TEXT', 'AVAILABLE TIME SLOTS'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_reservation_slots_label');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_reservation_note', 'TEXT', 'To make your reservation, please continue to the next screen.'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_reservation_note');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_reservation_btn', 'BUTTON_TEXT', 'Continue to Reservation'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_reservation_btn');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_label_duration', 'TEXT', 'Duration'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_label_duration');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'detail_label_seats', 'TEXT', 'Seats'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'detail_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'detail_label_seats');

-- ═══════════════════════════════════════════════════════════════════════════
-- PART G: CmsItemKey registrations + per-event CMS sections and items
-- ═══════════════════════════════════════════════════════════════════════════

-- Per-event CmsSection rows (one per active restaurant event)
INSERT IGNORE INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, CONCAT('restaurant_event_', e.EventId)
FROM Event e
JOIN CmsPage cp ON cp.Slug = 'restaurant'
WHERE e.EventTypeId = 5 AND e.IsActive = 1;

-- Per-event text / html items
INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'address_line', 'TEXT', r.AddressLine
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.AddressLine IS NOT NULL AND r.AddressLine <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'city', 'TEXT', r.City
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.City IS NOT NULL AND r.City <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'phone', 'TEXT', r.Phone
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.Phone IS NOT NULL AND r.Phone <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'email', 'TEXT', r.Email
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.Email IS NOT NULL AND r.Email <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'chef_name', 'TEXT', r.ChefName
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.ChefName IS NOT NULL AND r.ChefName <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'cuisine_type', 'TEXT', r.CuisineType
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.CuisineType IS NOT NULL AND r.CuisineType <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'special_requests_note', 'TEXT', r.SpecialRequestsNote
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.SpecialRequestsNote IS NOT NULL AND r.SpecialRequestsNote <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'stars', 'TEXT', CAST(r.Stars AS CHAR)
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.Stars IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'michelin_stars', 'TEXT', CAST(r.MichelinStars AS CHAR)
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.MichelinStars IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'seats_per_session', 'TEXT', CAST(r.SeatsPerSession AS CHAR)
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.SeatsPerSession IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'duration_minutes', 'TEXT', CAST(r.DurationMinutes AS CHAR)
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.DurationMinutes IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'time_slots', 'TEXT', r.TimeSlots
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.TimeSlots IS NOT NULL AND r.TimeSlots <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'price_adult', 'TEXT', CAST(r.PriceAdult AS CHAR)
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.PriceAdult IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'website', 'LINK', r.Website
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.Website IS NOT NULL AND r.Website <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'map_embed_url', 'LINK', r.MapEmbedUrl
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.MapEmbedUrl IS NOT NULL AND r.MapEmbedUrl <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, HtmlValue)
SELECT cs.CmsSectionId, 'about_text', 'HTML', r.AboutText
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.AboutText IS NOT NULL AND r.AboutText <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, HtmlValue)
SELECT cs.CmsSectionId, 'chef_text', 'HTML', r.ChefText
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.ChefText IS NOT NULL AND r.ChefText <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, HtmlValue)
SELECT cs.CmsSectionId, 'menu_description', 'HTML', r.MenuDescription
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.MenuDescription IS NOT NULL AND r.MenuDescription <> '';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, HtmlValue)
SELECT cs.CmsSectionId, 'location_description', 'HTML', r.LocationDescription
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.LocationDescription IS NOT NULL AND r.LocationDescription <> '';

-- ── Per-event image items ─────────────────────────────────────────────────
-- ADAPTED: group DB uses AssetId FK columns on Restaurant (not ImagePath
-- varchar columns). We JOIN MediaAsset to get the FilePath.

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'about_image', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN MediaAsset ma ON ma.MediaAssetId = r.AboutImageAssetId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.AboutImageAssetId IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'chef_image', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN MediaAsset ma ON ma.MediaAssetId = r.ChefImageAssetId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.ChefImageAssetId IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'menu_image_1', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN MediaAsset ma ON ma.MediaAssetId = r.MenuImage1AssetId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.MenuImage1AssetId IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'menu_image_2', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN MediaAsset ma ON ma.MediaAssetId = r.MenuImage2AssetId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.MenuImage2AssetId IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gallery_image_1', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN MediaAsset ma ON ma.MediaAssetId = r.GalleryImage1AssetId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.GalleryImage1AssetId IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gallery_image_2', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN MediaAsset ma ON ma.MediaAssetId = r.GalleryImage2AssetId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.GalleryImage2AssetId IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gallery_image_3', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN MediaAsset ma ON ma.MediaAssetId = r.GalleryImage3AssetId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.GalleryImage3AssetId IS NOT NULL;

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'reservation_image', 'IMAGE_PATH', ma.FilePath
FROM Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN MediaAsset ma ON ma.MediaAssetId = r.ReservationImageAssetId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.ReservationImageAssetId IS NOT NULL;

-- ═══════════════════════════════════════════════════════════════════════════
-- PART H: Add ReservationId to ProgramItem
--   Allows restaurant reservations to appear as cards in "My Program",
--   matching the Jazz/Storytelling/History ticket structure.
-- ═══════════════════════════════════════════════════════════════════════════

ALTER TABLE ProgramItem
    ADD COLUMN IF NOT EXISTS ReservationId INT UNSIGNED NULL DEFAULT NULL
        AFTER HistoryTourId;

-- Ensure correct type in case the column was added with wrong type in a prior partial run
ALTER TABLE ProgramItem
    MODIFY COLUMN ReservationId INT UNSIGNED NULL DEFAULT NULL;

ALTER TABLE ProgramItem
    ADD CONSTRAINT FK_ProgramItem_Reservation
        FOREIGN KEY (ReservationId) REFERENCES Reservation (ReservationId)
            ON DELETE SET NULL;

-- =============================================================================
SELECT 'Yummy! adapted migration complete.' AS Status;
