-- =============================================================================
-- migration-yummy-complete-v2.sql
-- Complete Yummy! (Restaurant) feature migration — run this ONE file.
--
-- Safe to run on any DB that has the base schema (Restaurant, Event, CmsPage,
-- CmsSection, CmsItem, MediaAsset tables). All statements are idempotent:
--   ADD COLUMN IF NOT EXISTS, CREATE TABLE IF NOT EXISTS,
--   INSERT IGNORE, WHERE NOT EXISTS, ON DUPLICATE KEY UPDATE.
--
-- What this covers:
--   A. Restaurant table column additions (detail fields, image paths, prices)
--   A2. Event table: Slug column + 7 restaurant event rows seed
--   B. Reservation table
--   C. MediaAsset entries for restaurant images
--   D. Restaurant domain data (detail content + image paths + prices)
--   E. Event updates (FeaturedImageAssetId + ShortDescription)
--   F. CMS page, sections, and listing-page items
--   G. CmsItemKey registrations + per-event CMS sections and items
--
-- Replaces: migration-restaurant-complete.sql, migration-v10-restaurant-page.sql,
--           migration-v30-event-slug-column.sql (Slug column + backfill),
--           migration-v31-restaurant-hero-scroll-link.sql,
--           migration-v31-yummy-reservation-setup.sql,
--           migration-v32-reservation-table.sql,
--           migration-v32-restaurant-direct-image-paths.sql,
--           migration-v32-restaurant-image-table.sql,
--           migration-v33-restaurant-events.sql,
--           migration-v33-restaurant-prices-and-timeslots.sql,
--           migration-v34-restaurant-cms-complete.sql,
--           migration-2026-03-23-yummy-complete.sql,
--           migration-v39-restaurant-short-description-and-images.sql
-- =============================================================================

-- ═══════════════════════════════════════════════════════════════════════════
-- PART A: Restaurant table column additions
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
    ADD COLUMN IF NOT EXISTS AboutImagePath       VARCHAR(500)  NULL AFTER SpecialRequestsNote,
    ADD COLUMN IF NOT EXISTS ChefImagePath        VARCHAR(500)  NULL AFTER AboutImagePath,
    ADD COLUMN IF NOT EXISTS MenuImage1Path       VARCHAR(500)  NULL AFTER ChefImagePath,
    ADD COLUMN IF NOT EXISTS MenuImage2Path       VARCHAR(500)  NULL AFTER MenuImage1Path,
    ADD COLUMN IF NOT EXISTS GalleryImage1Path    VARCHAR(500)  NULL AFTER MenuImage2Path,
    ADD COLUMN IF NOT EXISTS GalleryImage2Path    VARCHAR(500)  NULL AFTER GalleryImage1Path,
    ADD COLUMN IF NOT EXISTS GalleryImage3Path    VARCHAR(500)  NULL AFTER GalleryImage2Path,
    ADD COLUMN IF NOT EXISTS ReservationImagePath VARCHAR(500)  NULL AFTER GalleryImage3Path,
    ADD COLUMN IF NOT EXISTS TimeSlots            TEXT          NULL AFTER ReservationImagePath,
    ADD COLUMN IF NOT EXISTS PriceAdult           DECIMAL(10,2) NULL AFTER TimeSlots,
    ADD COLUMN IF NOT EXISTS PriceChild           DECIMAL(10,2) NULL AFTER PriceAdult;

-- ═══════════════════════════════════════════════════════════════════════════
-- PART A2: Event table prerequisites
-- ═══════════════════════════════════════════════════════════════════════════

-- Add Slug column if migration-v30 has not yet run on this DB
ALTER TABLE Event
    ADD COLUMN IF NOT EXISTS Slug VARCHAR(180) NULL AFTER Title;

-- Seed the 7 restaurant Event rows (skipped if they already exist).
-- RestaurantId is looked up by name so it works regardless of ID reordering.

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

-- Backfill Slug for any restaurant event that doesn't have one yet
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
    `TotalFee`        DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '€10 per person reservation deposit',
    `CreatedAt`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ReservationId`),
    INDEX `idx_reservation_restaurant` (`RestaurantId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ═══════════════════════════════════════════════════════════════════════════
-- PART C: MediaAsset entries for restaurant images
-- ═══════════════════════════════════════════════════════════════════════════

-- Card images
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

-- Ratatouille detail images
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

-- Toujours detail images
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
-- PART D: Restaurant domain data
-- ═══════════════════════════════════════════════════════════════════════════

-- ── Card image links ──────────────────────────────────────────────────────

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

-- ── Ratatouille detail content ────────────────────────────────────────────

UPDATE Restaurant SET
    Phone                = '+31 (0)23 123 4567',
    Email                = 'info@ratatouille.nl',
    Website              = 'ratatouillefoodandwine.nl',
    AboutText            = 'Welcome to <strong>Ratatouille</strong>, a Michelin-starred French restaurant on the Spaarne, known for turning fine dining into something <strong>warm, creative, and surprisingly accessible</strong>.\n\nEvery dish is prepared with <strong>seasonal ingredients, bright flavors,</strong> and a touch of <strong>French elegance.</strong> For the Yummy! festival, Ratatouille offers one special menu.\n\n<strong>Expect plates that feel refined,</strong> crafted with the same <strong>creativity</strong> that makes Ratatouille so loved.',
    ChefName             = 'Jozua Jaring',
    ChefText             = '<strong>Jozua Jaring</strong> leads the kitchen at Ratatouille. Under his leadership, the restaurant earned a <strong>Michelin star</strong>.\n\nHis cooking style is simple at its core. He starts with good ingredients and then adds creativity.\n\nFor the <strong>Yummy! festival</strong>, Chef Jaring designed a special menu.',
    MenuDescription      = 'For the Yummy! festival, guests enjoy a set menu specially created by Ratatouille.',
    LocationDescription  = 'Ratatouille is located by the river Spaarne, right in the center of Haarlem.\n\nThe <strong>Patronaat</strong> is just a 5-minute walk away.\n\nWhether you want to explore the city, enjoy a show, or continue your festival evening, Ratatouille is in a perfect location.',
    MapEmbedUrl          = 'https://maps.google.com/maps?q=Spaarne+96,+2011+CL+Haarlem,+Netherlands&t=&z=16&ie=UTF8&iwloc=&output=embed',
    MichelinStars        = 1,
    SeatsPerSession      = 35,
    DurationMinutes      = 120,
    SpecialRequestsNote  = 'Dietary needs, allergies, or accessibility requests can be added during the reservation.',
    AboutImagePath       = '/assets/Image/restaurants/ratatouille-about.png',
    ChefImagePath        = '/assets/Image/restaurants/ratatouille-chef.png',
    MenuImage1Path       = '/assets/Image/restaurants/ratatouille-menu-1.png',
    MenuImage2Path       = '/assets/Image/restaurants/ratatouille-menu-2.png',
    GalleryImage1Path    = '/assets/Image/restaurants/ratatouille-gallery-1.png',
    GalleryImage2Path    = '/assets/Image/restaurants/ratatouille-gallery-2.png',
    GalleryImage3Path    = '/assets/Image/restaurants/ratatouille-gallery-3.png',
    ReservationImagePath = '/assets/Image/restaurants/ratatouille-reservation.jpg',
    TimeSlots            = '17:00, 19:15, 21:30',
    PriceAdult           = 45.00,
    PriceChild           = 22.50
WHERE Name = 'Ratatouille';

-- ── Toujours detail content ───────────────────────────────────────────────

UPDATE Restaurant SET
    Phone                = '+31 023 532 1699',
    Email                = 'info@toujours.nl',
    Website              = 'restauranttoujours.nl',
    AboutText            = '<strong>Urban Frenchy Bistro Toujours</strong> is a stylish and welcoming spot in the heart of Haarlem. The restaurant is known for its <strong>cozy boudoir-style interior, warm lighting, and relaxed atmosphere</strong> that makes every visit feel special. Toujours brings together Dutch freshness, European comfort, and a strong focus on seafood, creating a menu that feels both modern and approachable.\n\nFor the Yummy! festival, Toujours offers a special menu built around the dishes they do best: <strong>bright seafood plates, familiar European flavors,</strong> and <strong>ingredients that celebrate local Dutch products</strong>. It''s a perfect stop for anyone who wants a relaxed, flavorful meal before exploring the rest of the festival.\n\nWhether you''re visiting with friends, family, or enjoying the evening on your own, Toujours brings together great food and a warm setting to make your festival night feel complete.',
    ChefName             = 'Georgiana Viou',
    ChefText             = 'Chef <strong>Georgiana Viou</strong> guides the Toujours kitchen with a focus on fresh seafood, seasonal produce, and relaxed European flavors. His cooking is warm, welcoming, and built around good ingredients.\n\n<strong>For Yummy!, the chef created a menu</strong> that reflects what Toujours is known for. Every dish is prepared with care, keeping the <strong>experience warm, relaxed, and enjoyable for everyone.</strong>',
    MenuDescription      = 'Toujours focuses on fresh seafood, Dutch ingredients, and European bistro-style cooking. Their plates often feature fish, seasonal produce, and comforting flavors that feel both elevated and easy to enjoy.\n\nExpect dishes that highlight seafood, local produce, and warm European influences. It''s perfect for a relaxed and tasty festival dinner.',
    LocationDescription  = 'Toujours is located at <strong>Oude Groenmarkt 10-12</strong>, a lively square in the center of Haarlem and only steps away from the <strong>Grote Markt</strong>, one of the festival''s busiest spots. Its central location makes it an easy stop before or after concerts, events, and evening activities.\n\nThe <strong>Jopenkerk</strong> is only 2 minutes walk away and the area is full of energy, history, and festival life. A great combination for anyone wanting to combine great food with a memorable night out.\n\n<strong>Address:</strong> Oude Groenmarkt 10-12, 2011 HL Haarlem, Nederland',
    MapEmbedUrl          = 'https://maps.google.com/maps?q=Oude+Groenmarkt+10,+2011+HL+Haarlem,+Netherlands&t=&z=16&ie=UTF8&iwloc=&output=embed',
    MichelinStars        = 0,
    SeatsPerSession      = 48,
    DurationMinutes      = 90,
    SpecialRequestsNote  = 'Dietary needs, allergies, or accessibility requests can be added during the reservation.',
    AboutImagePath       = '/assets/Image/restaurants/toujours-about.png',
    ChefImagePath        = '/assets/Image/restaurants/toujours-chef.jpg',
    MenuImage1Path       = '/assets/Image/restaurants/toujours-menu-1.png',
    MenuImage2Path       = '/assets/Image/restaurants/toujours-menu-2.png',
    GalleryImage1Path    = '/assets/Image/restaurants/toujours-gallery-1.png',
    GalleryImage2Path    = '/assets/Image/restaurants/toujours-gallery-2.png',
    GalleryImage3Path    = '/assets/Image/restaurants/toujours-gallery-3.png',
    ReservationImagePath = NULL, -- no reservation image file exists for Toujours
    TimeSlots            = '17:30, 19:15, 21:00',
    PriceAdult           = 45.00,
    PriceChild           = 22.50
WHERE Name = 'Urban Frenchy Bistro Toujours';

-- ── Default time slots and prices for all other active restaurants ─────────

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

-- Populate ShortDescription from Restaurant.DescriptionHtml (strip HTML tags)
UPDATE Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
SET e.ShortDescription = TRIM(REGEXP_REPLACE(r.DescriptionHtml, '<[^>]+>', ''))
WHERE e.EventTypeId = 5
  AND r.DescriptionHtml IS NOT NULL
  AND r.DescriptionHtml <> '';

-- ═══════════════════════════════════════════════════════════════════════════
-- PART F: CMS page, sections, and listing-page items
-- ═══════════════════════════════════════════════════════════════════════════

-- ── Page ──────────────────────────────────────────────────────────────────

INSERT INTO CmsPage (Slug, Title)
SELECT 'restaurant', 'Yummy! Restaurant Experience'
WHERE NOT EXISTS (SELECT 1 FROM CmsPage WHERE Slug = 'restaurant');

-- ── Sections ──────────────────────────────────────────────────────────────

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

-- ── Hero section ──────────────────────────────────────────────────────────

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

-- Always use #restaurants-grid (correct anchor)
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'hero_button_primary_link', 'LINK', '#restaurants-grid'
FROM CmsSection cs JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_primary_link');

-- Fix if it was seeded with the old value
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

-- ── Gradient section ──────────────────────────────────────────────────────

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

-- ── Intro split section ───────────────────────────────────────────────────

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

-- ── Intro split 2 section ─────────────────────────────────────────────────

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

-- ── Instructions section ──────────────────────────────────────────────────

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

-- ── Restaurant cards section ──────────────────────────────────────────────

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

-- ── Detail section (shared labels across all restaurant detail pages) ─────

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

-- ── Register item keys ────────────────────────────────────────────────────

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

-- ── Per-event CmsSection rows (one per active restaurant event) ───────────

INSERT IGNORE INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, CONCAT('restaurant_event_', e.EventId)
FROM Event e
JOIN CmsPage cp ON cp.Slug = 'restaurant'
WHERE e.EventTypeId = 5 AND e.IsActive = 1;

-- ── Per-event CmsItem rows ────────────────────────────────────────────────

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

-- Images: use ON DUPLICATE KEY UPDATE to overwrite any empty/stale rows from prior migrations

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'about_image', 'IMAGE_PATH', r.AboutImagePath
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.AboutImagePath IS NOT NULL AND r.AboutImagePath <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'chef_image', 'IMAGE_PATH', r.ChefImagePath
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.ChefImagePath IS NOT NULL AND r.ChefImagePath <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'menu_image_1', 'IMAGE_PATH', r.MenuImage1Path
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.MenuImage1Path IS NOT NULL AND r.MenuImage1Path <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'menu_image_2', 'IMAGE_PATH', r.MenuImage2Path
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.MenuImage2Path IS NOT NULL AND r.MenuImage2Path <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gallery_image_1', 'IMAGE_PATH', r.GalleryImage1Path
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.GalleryImage1Path IS NOT NULL AND r.GalleryImage1Path <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gallery_image_2', 'IMAGE_PATH', r.GalleryImage2Path
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.GalleryImage2Path IS NOT NULL AND r.GalleryImage2Path <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'gallery_image_3', 'IMAGE_PATH', r.GalleryImage3Path
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.GalleryImage3Path IS NOT NULL AND r.GalleryImage3Path <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'reservation_image', 'IMAGE_PATH', r.ReservationImagePath
FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
JOIN CmsPage cp ON cp.Slug = 'restaurant'
JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = CONCAT('restaurant_event_', e.EventId)
WHERE e.EventTypeId = 5 AND r.ReservationImagePath IS NOT NULL AND r.ReservationImagePath <> ''
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

-- =============================================================================
SELECT 'Yummy! migration complete.' AS Status;
