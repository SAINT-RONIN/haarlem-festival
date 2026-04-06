-- =============================================================================
-- migration-yummy-adapted-for-group-db.sql
-- Adapted Yummy! migration for the group DB (haarlem_festival_db (19).sql).
--
-- This is the single, combined migration file that:
--   1. Seeds all Yummy! CMS content (Parts A–H, previously separate)
--   2. Adds Yummy event/session/CMS data without breaking Story-page schema
--
-- Run order:
--   A1. Restore/preserve Story-page restaurant schema if it was dropped earlier
--   A2. Add Slug to Event; insert/guard 7 restaurant Event rows
--   B.  Create / repair Reservation table on RestaurantId
--   C.  MediaAsset seed rows
--   E.  Set Event.FeaturedImageAssetId via direct MediaAsset lookups
--   F-PRE. Register all CmsItemKey rows
--   F.  Seed CMS listing page (CmsPage, sections, items)
--   G.  Seed per-event CMS sections and items (hardcoded known values)
--   H.  Add ReservationId to ProgramItem
--   I.  Preserve Story-page restaurant schema (no destructive drops)
--
-- NOTE: Story-page is still the source of truth for restaurant architecture.
-- It still uses Restaurant, Event.RestaurantId, Reservation.RestaurantId,
-- RestaurantCuisine, and RestaurantImage. This migration therefore restores
-- those tables/columns when needed and must not drop them.
--
-- Everything is idempotent: ADD COLUMN IF NOT EXISTS, CREATE TABLE IF NOT EXISTS,
-- WHERE NOT EXISTS, INSERT IGNORE, DROP TABLE IF EXISTS, DROP COLUMN IF EXISTS.
-- =============================================================================

-- ═══════════════════════════════════════════════════════════════════════════
-- PART A1: Restore / preserve Story-page restaurant schema if missing
--   Needed by HomeService, restaurant CMS CRUD, reservation persistence,
--   and Program reservation enrichment on Story-page.
-- ═══════════════════════════════════════════════════════════════════════════

ALTER TABLE Event
    ADD COLUMN IF NOT EXISTS RestaurantId INT NULL AFTER ArtistId;

CREATE TABLE IF NOT EXISTS `CuisineType` (
    `CuisineTypeId` INT(11) NOT NULL AUTO_INCREMENT,
    `Name`          VARCHAR(60) NOT NULL,
    PRIMARY KEY (`CuisineTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO CuisineType (CuisineTypeId, Name) VALUES
    (1, 'Dutch'),
    (2, 'European'),
    (3, 'Fish & Seafood'),
    (4, 'French'),
    (5, 'Modern'),
    (6, 'Vegan');

CREATE TABLE IF NOT EXISTS `Restaurant` (
    `RestaurantId`            INT(11) NOT NULL AUTO_INCREMENT,
    `Name`                    VARCHAR(120) NOT NULL,
    `AddressLine`             VARCHAR(200) NOT NULL DEFAULT '',
    `City`                    VARCHAR(80) NOT NULL DEFAULT 'Haarlem',
    `Stars`                   INT(11) DEFAULT NULL,
    `CuisineType`             VARCHAR(160) NOT NULL DEFAULT '',
    `DescriptionHtml`         TEXT NOT NULL,
    `ImageAssetId`            INT(11) DEFAULT NULL,
    `IsActive`                TINYINT(1) NOT NULL DEFAULT 1,
    `CreatedAtUtc`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `Phone`                   VARCHAR(50) DEFAULT NULL,
    `Email`                   VARCHAR(150) DEFAULT NULL,
    `Website`                 VARCHAR(255) DEFAULT NULL,
    `AboutText`               TEXT DEFAULT NULL,
    `ChefName`                VARCHAR(120) DEFAULT NULL,
    `ChefText`                TEXT DEFAULT NULL,
    `MenuDescription`         TEXT DEFAULT NULL,
    `LocationDescription`     TEXT DEFAULT NULL,
    `MapEmbedUrl`             TEXT DEFAULT NULL,
    `MichelinStars`           INT(11) DEFAULT NULL,
    `SeatsPerSession`         INT(11) DEFAULT NULL,
    `DurationMinutes`         INT(11) DEFAULT NULL,
    `SpecialRequestsNote`     TEXT DEFAULT NULL,
    `GalleryImage1AssetId`    INT(11) DEFAULT NULL,
    `GalleryImage2AssetId`    INT(11) DEFAULT NULL,
    `GalleryImage3AssetId`    INT(11) DEFAULT NULL,
    `AboutImageAssetId`       INT(11) DEFAULT NULL,
    `ChefImageAssetId`        INT(11) DEFAULT NULL,
    `MenuImage1AssetId`       INT(11) DEFAULT NULL,
    `MenuImage2AssetId`       INT(11) DEFAULT NULL,
    `ReservationImageAssetId` INT(11) DEFAULT NULL,
    PRIMARY KEY (`RestaurantId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO Restaurant (
    RestaurantId, Name, AddressLine, City, Stars, CuisineType, DescriptionHtml,
    ImageAssetId, IsActive, CreatedAtUtc, Phone, Email, Website, AboutText,
    ChefName, ChefText, MenuDescription, LocationDescription, MapEmbedUrl,
    MichelinStars, SeatsPerSession, DurationMinutes, SpecialRequestsNote,
    GalleryImage1AssetId, GalleryImage2AssetId, GalleryImage3AssetId,
    AboutImageAssetId, ChefImageAssetId, MenuImage1AssetId, MenuImage2AssetId,
    ReservationImageAssetId
) VALUES
    (1, 'Ratatouille', 'Spaarne 96, 2011 CL Haarlem', 'Haarlem', 4, 'French, fish and seafood, European', 'Refined dining with a warm touch, where seasonal ingredients and creative flavors come together for an elegant experience.', 76, 1, '2026-02-06 15:30:32', '+31 (0)23 123 4567', 'info@ratatouille.nl', 'ratatouillefoodandwine.nl', NULL, 'Jozua Jaring', NULL, 'For the Yummy! festival, guests enjoy a set menu specially created by Ratatouille.', 'Ratatouille is located by the river Spaarne, right in the center of Haarlem.', 'https://maps.google.com/maps?q=Spaarne+96,+2011+CL+Haarlem,+Netherlands&t=&z=16&ie=UTF8&iwloc=&output=embed', 1, 35, 120, 'Dietary needs, allergies, or accessibility requests can be added during the reservation.', 82, 83, 84, 85, 86, 87, 88, 89),
    (2, 'Urban Frenchy Bistro Toujours', 'Oude Groenmarkt 10-12, 2011 HL Haarlem', 'Haarlem', 3, 'Dutch, fish and seafood, European', 'A cozy city bistro focused on seafood and comforting dishes in a lively central setting.', 81, 1, '2026-02-06 15:30:32', '+31 023 532 1699', 'info@toujours.nl', 'restauranttoujours.nl', NULL, 'Georgiana Viou', NULL, 'Toujours focuses on fresh seafood, Dutch ingredients, and European bistro-style cooking.', 'Toujours is located at Oude Groenmarkt 10-12 in the center of Haarlem.', 'https://maps.google.com/maps?q=Oude+Groenmarkt+10,+2011+HL+Haarlem,+Netherlands&t=&z=16&ie=UTF8&iwloc=&output=embed', 0, 48, 90, 'Dietary needs, allergies, or accessibility requests can be added during the reservation.', 90, 91, 92, 93, 98, 95, 96, 89),
    (3, 'Café de Roemer', 'Botermarkt 17, 2011 XL Haarlem', 'Haarlem', 4, 'Dutch, fish and seafood, European', 'A cozy neighborhood cafe serving honest food and classic flavors in a relaxed setting.', 75, 1, '2026-02-06 15:30:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    (4, 'Grand Cafe Brinkman', 'Grote Markt 13, 2011 RC Haarlem', 'Haarlem', 3, 'Dutch, European, Modern', 'A classic grand cafe on Haarlem''s main square, serving familiar European dishes in the heart of the festival buzz.', 80, 1, '2026-02-06 15:30:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    (5, 'New Vegas', 'Koningstraat 5, 2011 TB Haarlem', 'Haarlem', 3, 'Vegan', 'A casual spot with an international feel, offering familiar dishes and vegetarian options right in the city center.', 79, 1, '2026-02-06 15:30:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    (6, 'Restaurant Fris', 'Twijnderslaan 7, 2012 BG Haarlem', 'Haarlem', 4, 'Dutch, French, European', 'A contemporary restaurant focused on seasonal ingredients, thoughtful cooking, and elegant flavors without the formality.', 78, 1, '2026-02-06 15:30:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    (7, 'Restaurant ML', 'Kleine Houtstraat 70, 2011 DR Haarlem', 'Haarlem', 4, 'Dutch, fish and seafood, European', 'A modern fine-dining restaurant known for a refined yet welcoming atmosphere.', 77, 1, '2026-02-06 15:30:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

CREATE TABLE IF NOT EXISTS `RestaurantCuisine` (
    `RestaurantId`  INT(11) NOT NULL,
    `CuisineTypeId` INT(11) NOT NULL,
    PRIMARY KEY (`RestaurantId`, `CuisineTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO RestaurantCuisine (RestaurantId, CuisineTypeId) VALUES
    (2, 1), (3, 1), (4, 1), (6, 1), (7, 1),
    (1, 2), (2, 2), (3, 2), (4, 2), (6, 2), (7, 2),
    (1, 3), (2, 3), (3, 3), (7, 3),
    (1, 4), (6, 4),
    (4, 5),
    (5, 6);

CREATE TABLE IF NOT EXISTS `RestaurantImage` (
    `RestaurantImageId` INT(11) NOT NULL AUTO_INCREMENT,
    `RestaurantId`      INT(11) NOT NULL,
    `MediaAssetId`      INT(11) NOT NULL,
    `ImageType`         VARCHAR(30) NOT NULL,
    `SortOrder`         INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`RestaurantImageId`),
    UNIQUE KEY `UQ_RestaurantImage` (`RestaurantId`, `ImageType`, `SortOrder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO RestaurantImage (RestaurantImageId, RestaurantId, MediaAssetId, ImageType, SortOrder) VALUES
    (1, 1, 82, 'gallery', 1),
    (2, 2, 90, 'gallery', 1),
    (3, 1, 83, 'gallery', 2),
    (4, 2, 91, 'gallery', 2),
    (5, 1, 84, 'gallery', 3),
    (6, 2, 92, 'gallery', 3),
    (7, 1, 85, 'about', 1),
    (8, 2, 93, 'about', 1),
    (9, 1, 86, 'chef', 1),
    (10, 2, 98, 'chef', 1),
    (11, 1, 87, 'menu', 1),
    (12, 2, 95, 'menu', 1),
    (13, 1, 88, 'menu', 2),
    (14, 2, 96, 'menu', 2),
    (15, 1, 89, 'reservation', 1),
    (16, 2, 89, 'reservation', 1);

UPDATE Event SET RestaurantId = 3 WHERE EventId = 47 AND EventTypeId = 5;
UPDATE Event SET RestaurantId = 1 WHERE EventId = 48 AND EventTypeId = 5;
UPDATE Event SET RestaurantId = 7 WHERE EventId = 49 AND EventTypeId = 5;
UPDATE Event SET RestaurantId = 6 WHERE EventId = 50 AND EventTypeId = 5;
UPDATE Event SET RestaurantId = 5 WHERE EventId = 51 AND EventTypeId = 5;
UPDATE Event SET RestaurantId = 4 WHERE EventId = 52 AND EventTypeId = 5;
UPDATE Event SET RestaurantId = 2 WHERE EventId = 53 AND EventTypeId = 5;

SET @fk_event_rest = (
    SELECT CONSTRAINT_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'Event'
      AND COLUMN_NAME  = 'RestaurantId'
      AND REFERENCED_TABLE_NAME = 'Restaurant'
    LIMIT 1
);
SET @add_event_rest_fk = IF(
    @fk_event_rest IS NULL,
    'ALTER TABLE Event ADD CONSTRAINT FK_Event_Restaurant FOREIGN KEY (RestaurantId) REFERENCES Restaurant (RestaurantId)',
    'SELECT 1'
);
PREPARE stmt FROM @add_event_rest_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ═══════════════════════════════════════════════════════════════════════════
-- PART A2: Event prerequisites (Slug column + 7 restaurant event rows)
--   Restaurant rows are preserved on Story-page; these Event rows remain the
--   public-facing restaurant pages and reservation session owners.
-- ═══════════════════════════════════════════════════════════════════════════

ALTER TABLE Event
    ADD COLUMN IF NOT EXISTS Slug         VARCHAR(180) NULL AFTER Title,
    ADD COLUMN IF NOT EXISTS DisplayOrder INT          NULL AFTER Slug;

-- Display order for restaurant cards (1 = first)
UPDATE Event SET DisplayOrder = 1 WHERE EventId = 48 AND EventTypeId = 5; -- Ratatouille
UPDATE Event SET DisplayOrder = 2 WHERE EventId = 53 AND EventTypeId = 5; -- Urban Frenchy Bistro Toujours
UPDATE Event SET DisplayOrder = 3 WHERE EventId = 47 AND EventTypeId = 5; -- Café de Roemer
UPDATE Event SET DisplayOrder = 4 WHERE EventId = 52 AND EventTypeId = 5; -- Grand Cafe Brinkman
UPDATE Event SET DisplayOrder = 5 WHERE EventId = 51 AND EventTypeId = 5; -- New Vegas
UPDATE Event SET DisplayOrder = 6 WHERE EventId = 50 AND EventTypeId = 5; -- Restaurant Fris
UPDATE Event SET DisplayOrder = 7 WHERE EventId = 49 AND EventTypeId = 5; -- Restaurant ML

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, IsActive, CreatedAtUtc)
SELECT 47, 5, 'Café de Roemer - Festival Dinner', 'Dutch, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', 1, '2026-02-06 15:30:32'
WHERE NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 47);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, IsActive, CreatedAtUtc)
SELECT 48, 5, 'Ratatouille - Festival Dinner', 'French, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', 1, '2026-02-06 15:30:32'
WHERE NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 48);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, IsActive, CreatedAtUtc)
SELECT 49, 5, 'Restaurant ML - Festival Dinner', 'Dutch, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', 1, '2026-02-06 15:30:32'
WHERE NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 49);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, IsActive, CreatedAtUtc)
SELECT 50, 5, 'Restaurant Fris - Festival Dinner', 'Dutch, French, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', 1, '2026-02-06 15:30:32'
WHERE NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 50);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, IsActive, CreatedAtUtc)
SELECT 51, 5, 'New Vegas - Festival Dinner', 'Vegan cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', 1, '2026-02-06 15:30:32'
WHERE NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 51);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, IsActive, CreatedAtUtc)
SELECT 52, 5, 'Grand Cafe Brinkman - Festival Dinner', 'Dutch, European, Modern cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', 1, '2026-02-06 15:30:32'
WHERE NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 52);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, IsActive, CreatedAtUtc)
SELECT 53, 5, 'Urban Frenchy Bistro Toujours - Festival Dinner', 'Dutch, fish and seafood, European cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', 1, '2026-02-06 15:30:32'
WHERE NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 53);

-- Backfill Slug for any restaurant event that does not have one yet
UPDATE Event
SET Slug = LOWER(TRIM(BOTH '-' FROM REGEXP_REPLACE(Title, '[^a-z0-9]+', '-')))
WHERE EventTypeId = 5 AND (Slug IS NULL OR Slug = '');

-- ═══════════════════════════════════════════════════════════════════════════
-- PART B: Reservation table
--   Story-page stores reservations against RestaurantId.
--   If an earlier Yummy migration created EventId instead, convert it back.
-- ═══════════════════════════════════════════════════════════════════════════

-- Fresh-install path: create with RestaurantId directly
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
            ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Existing-install repair path: add RestaurantId if a prior run removed it
ALTER TABLE Reservation
    ADD COLUMN IF NOT EXISTS RestaurantId INT NULL;

-- Backfill RestaurantId when a prior run converted Reservation to EventId
SET @has_res_event_id = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'Reservation'
      AND COLUMN_NAME  = 'EventId'
);
SET @backfill_res_restaurant_sql = IF(
    @has_res_event_id > 0,
    'UPDATE Reservation r JOIN Event e ON e.EventId = r.EventId SET r.RestaurantId = e.RestaurantId WHERE r.RestaurantId IS NULL',
    'SELECT 1'
);
PREPARE stmt FROM @backfill_res_restaurant_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Make RestaurantId authoritative again
ALTER TABLE Reservation
    MODIFY COLUMN RestaurantId INT NOT NULL;

-- Remove the EventId FK/column from earlier event-only migrations
SET @fk_res_event = (
    SELECT CONSTRAINT_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'Reservation'
      AND COLUMN_NAME  = 'EventId'
      AND REFERENCED_TABLE_NAME = 'Event'
    LIMIT 1
);
SET @drop_res_event_fk = IF(
    @fk_res_event IS NOT NULL,
    CONCAT('ALTER TABLE Reservation DROP FOREIGN KEY `', @fk_res_event, '`'),
    'SELECT 1'
);
PREPARE stmt FROM @drop_res_event_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE Reservation
    DROP COLUMN IF EXISTS EventId;

SET @fk_res_rest = (
    SELECT CONSTRAINT_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'Reservation'
      AND COLUMN_NAME  = 'RestaurantId'
      AND REFERENCED_TABLE_NAME = 'Restaurant'
    LIMIT 1
);
SET @add_res_rest_fk = IF(
    @fk_res_rest IS NULL,
    'ALTER TABLE Reservation ADD CONSTRAINT FK_Reservation_Restaurant FOREIGN KEY (RestaurantId) REFERENCES Restaurant (RestaurantId) ON DELETE RESTRICT ON UPDATE CASCADE',
    'SELECT 1'
);
PREPARE stmt FROM @add_res_rest_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

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
-- PART E: Event.FeaturedImageAssetId — direct MediaAsset lookups
--   No Restaurant table. Each event is updated directly by its known image path.
--   ShortDescription was already set in the Part A2 INSERT literals above.
-- ═══════════════════════════════════════════════════════════════════════════

-- EventId 47: Café de Roemer
UPDATE Event e JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-CafeDeRoemer-card.png'
SET e.FeaturedImageAssetId = ma.MediaAssetId WHERE e.EventId = 47 AND e.FeaturedImageAssetId IS NULL;

-- EventId 48: Ratatouille
UPDATE Event e JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-Ratatouille-card.png'
SET e.FeaturedImageAssetId = ma.MediaAssetId WHERE e.EventId = 48 AND e.FeaturedImageAssetId IS NULL;

-- EventId 49: Restaurant ML
UPDATE Event e JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-RestaurantML-card.png'
SET e.FeaturedImageAssetId = ma.MediaAssetId WHERE e.EventId = 49 AND e.FeaturedImageAssetId IS NULL;

-- EventId 50: Restaurant Fris
UPDATE Event e JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-RestaurantFris-card.png'
SET e.FeaturedImageAssetId = ma.MediaAssetId WHERE e.EventId = 50 AND e.FeaturedImageAssetId IS NULL;

-- EventId 51: New Vegas
UPDATE Event e JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-NewVegas-card.jpg'
SET e.FeaturedImageAssetId = ma.MediaAssetId WHERE e.EventId = 51 AND e.FeaturedImageAssetId IS NULL;

-- EventId 52: Grand Cafe Brinkman
UPDATE Event e JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-GrandCafeBrinkman-card.png'
SET e.FeaturedImageAssetId = ma.MediaAssetId WHERE e.EventId = 52 AND e.FeaturedImageAssetId IS NULL;

-- EventId 53: Urban Frenchy Bistro Toujours
UPDATE Event e JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-UrbanFrenchyBistroToujours-card.png'
SET e.FeaturedImageAssetId = ma.MediaAssetId WHERE e.EventId = 53 AND e.FeaturedImageAssetId IS NULL;

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
-- PART G: Per-event CMS sections and items
--   No Restaurant table. Known values are hardcoded per EventId.
--   Data we don't have (addresses, phone, chef bios, images) is omitted —
--   CMS admin fills those in via the UI.
-- ═══════════════════════════════════════════════════════════════════════════

-- Per-event CmsSection rows (one per active restaurant event)
INSERT IGNORE INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, CONCAT('restaurant_event_', e.EventId)
FROM Event e
JOIN CmsPage cp ON cp.Slug = 'restaurant'
WHERE e.EventTypeId = 5 AND e.IsActive = 1;

-- ── EventId 47: Café de Roemer ────────────────────────────────────────────
INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'cuisine_type', 'TEXT', 'Dutch, Fish and Seafood, European'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_47'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'time_slots', 'TEXT', '16:30, 18:30, 20:30'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_47'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'price_adult', 'TEXT', '45.00'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_47'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'duration_minutes', 'TEXT', '120'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_47'
WHERE cp.Slug = 'restaurant';

-- ── EventId 48: Ratatouille ───────────────────────────────────────────────
INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'cuisine_type', 'TEXT', 'French, Fish and Seafood, European'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_48'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'time_slots', 'TEXT', '17:00, 19:15, 21:30'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_48'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'price_adult', 'TEXT', '45.00'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_48'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'duration_minutes', 'TEXT', '120'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_48'
WHERE cp.Slug = 'restaurant';

-- ── EventId 49: Restaurant ML ─────────────────────────────────────────────
INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'cuisine_type', 'TEXT', 'Dutch, Fish and Seafood, European'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_49'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'time_slots', 'TEXT', '16:30, 18:30, 20:30'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_49'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'price_adult', 'TEXT', '45.00'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_49'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'duration_minutes', 'TEXT', '120'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_49'
WHERE cp.Slug = 'restaurant';

-- ── EventId 50: Restaurant Fris ───────────────────────────────────────────
INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'cuisine_type', 'TEXT', 'Dutch, French, European'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_50'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'time_slots', 'TEXT', '16:30, 18:30, 20:30'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_50'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'price_adult', 'TEXT', '45.00'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_50'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'duration_minutes', 'TEXT', '120'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_50'
WHERE cp.Slug = 'restaurant';

-- ── EventId 51: New Vegas ─────────────────────────────────────────────────
INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'cuisine_type', 'TEXT', 'Vegan'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_51'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'time_slots', 'TEXT', '16:30, 18:30, 20:30'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_51'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'price_adult', 'TEXT', '35.00'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_51'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'duration_minutes', 'TEXT', '120'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_51'
WHERE cp.Slug = 'restaurant';

-- ── EventId 52: Grand Cafe Brinkman ──────────────────────────────────────
INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'cuisine_type', 'TEXT', 'Dutch, European, Modern'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_52'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'time_slots', 'TEXT', '16:30, 18:30, 20:30'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_52'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'price_adult', 'TEXT', '35.00'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_52'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'duration_minutes', 'TEXT', '120'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_52'
WHERE cp.Slug = 'restaurant';

-- ── EventId 53: Urban Frenchy Bistro Toujours ─────────────────────────────
INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'cuisine_type', 'TEXT', 'Dutch, Fish and Seafood, European'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_53'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'time_slots', 'TEXT', '17:30, 19:15, 21:00'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_53'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'price_adult', 'TEXT', '35.00'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_53'
WHERE cp.Slug = 'restaurant';

INSERT IGNORE INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT cs.CmsSectionId, 'duration_minutes', 'TEXT', '120'
FROM CmsPage cp JOIN CmsSection cs ON cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'restaurant_event_53'
WHERE cp.Slug = 'restaurant';

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

-- Add FK only if it does not already exist
SET @fk_pi_res = (
    SELECT CONSTRAINT_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'ProgramItem'
      AND COLUMN_NAME  = 'ReservationId'
      AND REFERENCED_TABLE_NAME = 'Reservation'
    LIMIT 1
);
SET @add_pi_fk = IF(
    @fk_pi_res IS NULL,
    'ALTER TABLE ProgramItem ADD CONSTRAINT FK_ProgramItem_Reservation FOREIGN KEY (ReservationId) REFERENCES Reservation (ReservationId) ON DELETE SET NULL',
    'SELECT 1'
);
PREPARE stmt FROM @add_pi_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ═══════════════════════════════════════════════════════════════════════════
-- PART I: Preserve Story-page restaurant schema
--   Intentionally no-op.
--
--   Story-page still depends on:
--     - Restaurant
--     - Event.RestaurantId
--     - Reservation.RestaurantId
--     - RestaurantCuisine
--     - CuisineType
--     - RestaurantImage
--
--   The destructive event-only conversion from Yummy-page must not run here.
-- ═══════════════════════════════════════════════════════════════════════════

-- ═══════════════════════════════════════════════════════════════════════════
-- PART J: EventSession rows for restaurant events
--   7 restaurants × 4 festival days × 3 time slots = 84 sessions.
--   Each session also gets two EventSessionPrice rows (Adult + Child U12).
--
--   Festival dates:
--     Thursday  2026-07-23
--     Friday    2026-07-24
--     Saturday  2026-07-25
--     Sunday    2026-07-26
--
--   All inserts are idempotent (WHERE NOT EXISTS on EventId + StartDateTime).
--   CtaUrl is built from Event.Slug so it always matches the live slug.
-- ═══════════════════════════════════════════════════════════════════════════

-- ── EventId 47: Café de Roemer — slots 16:30 / 18:30 / 20:30 ─────────────
INSERT INTO EventSession (
    EventId, StartDateTime, EndDateTime,
    CapacityTotal, CapacitySingleTicketLimit,
    HallName, SessionType, DurationMinutes,
    LanguageCode, MinAge, MaxAge,
    ReservationRequired, IsFree, Notes, HistoryTicketLabel,
    CtaLabel, CtaUrl, IsCancelled, IsActive
)
SELECT
    47,
    CONCAT(d.festival_date, ' ', s.start_time, ':00'),
    CONCAT(d.festival_date, ' ', s.end_time,   ':00'),
    40, 40, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL,
    'Reserve a Table', CONCAT('/restaurant/', e.Slug, '/reservation'), 0, 1
FROM (
    SELECT '2026-07-23' AS festival_date UNION ALL
    SELECT '2026-07-24' UNION ALL
    SELECT '2026-07-25' UNION ALL
    SELECT '2026-07-26'
) AS d
CROSS JOIN (
    SELECT '16:30' AS start_time, '18:30' AS end_time UNION ALL
    SELECT '18:30', '20:30' UNION ALL
    SELECT '20:30', '22:30'
) AS s
JOIN Event e ON e.EventId = 47
WHERE NOT EXISTS (
    SELECT 1 FROM EventSession
    WHERE EventId = 47 AND StartDateTime = CONCAT(d.festival_date, ' ', s.start_time, ':00')
);

-- ── EventId 48: Ratatouille — slots 17:00 / 19:15 / 21:30 ────────────────
INSERT INTO EventSession (
    EventId, StartDateTime, EndDateTime,
    CapacityTotal, CapacitySingleTicketLimit,
    HallName, SessionType, DurationMinutes,
    LanguageCode, MinAge, MaxAge,
    ReservationRequired, IsFree, Notes, HistoryTicketLabel,
    CtaLabel, CtaUrl, IsCancelled, IsActive
)
SELECT
    48,
    CONCAT(d.festival_date, ' ', s.start_time, ':00'),
    CONCAT(d.festival_date, ' ', s.end_time,   ':00'),
    40, 40, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL,
    'Reserve a Table', CONCAT('/restaurant/', e.Slug, '/reservation'), 0, 1
FROM (
    SELECT '2026-07-23' AS festival_date UNION ALL
    SELECT '2026-07-24' UNION ALL
    SELECT '2026-07-25' UNION ALL
    SELECT '2026-07-26'
) AS d
CROSS JOIN (
    SELECT '17:00' AS start_time, '19:00' AS end_time UNION ALL
    SELECT '19:15', '21:15' UNION ALL
    SELECT '21:30', '23:30'
) AS s
JOIN Event e ON e.EventId = 48
WHERE NOT EXISTS (
    SELECT 1 FROM EventSession
    WHERE EventId = 48 AND StartDateTime = CONCAT(d.festival_date, ' ', s.start_time, ':00')
);

-- ── EventId 49: Restaurant ML — slots 16:30 / 18:30 / 20:30 ─────────────
INSERT INTO EventSession (
    EventId, StartDateTime, EndDateTime,
    CapacityTotal, CapacitySingleTicketLimit,
    HallName, SessionType, DurationMinutes,
    LanguageCode, MinAge, MaxAge,
    ReservationRequired, IsFree, Notes, HistoryTicketLabel,
    CtaLabel, CtaUrl, IsCancelled, IsActive
)
SELECT
    49,
    CONCAT(d.festival_date, ' ', s.start_time, ':00'),
    CONCAT(d.festival_date, ' ', s.end_time,   ':00'),
    40, 40, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL,
    'Reserve a Table', CONCAT('/restaurant/', e.Slug, '/reservation'), 0, 1
FROM (
    SELECT '2026-07-23' AS festival_date UNION ALL
    SELECT '2026-07-24' UNION ALL
    SELECT '2026-07-25' UNION ALL
    SELECT '2026-07-26'
) AS d
CROSS JOIN (
    SELECT '16:30' AS start_time, '18:30' AS end_time UNION ALL
    SELECT '18:30', '20:30' UNION ALL
    SELECT '20:30', '22:30'
) AS s
JOIN Event e ON e.EventId = 49
WHERE NOT EXISTS (
    SELECT 1 FROM EventSession
    WHERE EventId = 49 AND StartDateTime = CONCAT(d.festival_date, ' ', s.start_time, ':00')
);

-- ── EventId 50: Restaurant Fris — slots 16:30 / 18:30 / 20:30 ───────────
INSERT INTO EventSession (
    EventId, StartDateTime, EndDateTime,
    CapacityTotal, CapacitySingleTicketLimit,
    HallName, SessionType, DurationMinutes,
    LanguageCode, MinAge, MaxAge,
    ReservationRequired, IsFree, Notes, HistoryTicketLabel,
    CtaLabel, CtaUrl, IsCancelled, IsActive
)
SELECT
    50,
    CONCAT(d.festival_date, ' ', s.start_time, ':00'),
    CONCAT(d.festival_date, ' ', s.end_time,   ':00'),
    40, 40, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL,
    'Reserve a Table', CONCAT('/restaurant/', e.Slug, '/reservation'), 0, 1
FROM (
    SELECT '2026-07-23' AS festival_date UNION ALL
    SELECT '2026-07-24' UNION ALL
    SELECT '2026-07-25' UNION ALL
    SELECT '2026-07-26'
) AS d
CROSS JOIN (
    SELECT '16:30' AS start_time, '18:30' AS end_time UNION ALL
    SELECT '18:30', '20:30' UNION ALL
    SELECT '20:30', '22:30'
) AS s
JOIN Event e ON e.EventId = 50
WHERE NOT EXISTS (
    SELECT 1 FROM EventSession
    WHERE EventId = 50 AND StartDateTime = CONCAT(d.festival_date, ' ', s.start_time, ':00')
);

-- ── EventId 51: New Vegas — slots 16:30 / 18:30 / 20:30 ─────────────────
INSERT INTO EventSession (
    EventId, StartDateTime, EndDateTime,
    CapacityTotal, CapacitySingleTicketLimit,
    HallName, SessionType, DurationMinutes,
    LanguageCode, MinAge, MaxAge,
    ReservationRequired, IsFree, Notes, HistoryTicketLabel,
    CtaLabel, CtaUrl, IsCancelled, IsActive
)
SELECT
    51,
    CONCAT(d.festival_date, ' ', s.start_time, ':00'),
    CONCAT(d.festival_date, ' ', s.end_time,   ':00'),
    40, 40, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL,
    'Reserve a Table', CONCAT('/restaurant/', e.Slug, '/reservation'), 0, 1
FROM (
    SELECT '2026-07-23' AS festival_date UNION ALL
    SELECT '2026-07-24' UNION ALL
    SELECT '2026-07-25' UNION ALL
    SELECT '2026-07-26'
) AS d
CROSS JOIN (
    SELECT '16:30' AS start_time, '18:30' AS end_time UNION ALL
    SELECT '18:30', '20:30' UNION ALL
    SELECT '20:30', '22:30'
) AS s
JOIN Event e ON e.EventId = 51
WHERE NOT EXISTS (
    SELECT 1 FROM EventSession
    WHERE EventId = 51 AND StartDateTime = CONCAT(d.festival_date, ' ', s.start_time, ':00')
);

-- ── EventId 52: Grand Cafe Brinkman — slots 16:30 / 18:30 / 20:30 ────────
INSERT INTO EventSession (
    EventId, StartDateTime, EndDateTime,
    CapacityTotal, CapacitySingleTicketLimit,
    HallName, SessionType, DurationMinutes,
    LanguageCode, MinAge, MaxAge,
    ReservationRequired, IsFree, Notes, HistoryTicketLabel,
    CtaLabel, CtaUrl, IsCancelled, IsActive
)
SELECT
    52,
    CONCAT(d.festival_date, ' ', s.start_time, ':00'),
    CONCAT(d.festival_date, ' ', s.end_time,   ':00'),
    40, 40, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL,
    'Reserve a Table', CONCAT('/restaurant/', e.Slug, '/reservation'), 0, 1
FROM (
    SELECT '2026-07-23' AS festival_date UNION ALL
    SELECT '2026-07-24' UNION ALL
    SELECT '2026-07-25' UNION ALL
    SELECT '2026-07-26'
) AS d
CROSS JOIN (
    SELECT '16:30' AS start_time, '18:30' AS end_time UNION ALL
    SELECT '18:30', '20:30' UNION ALL
    SELECT '20:30', '22:30'
) AS s
JOIN Event e ON e.EventId = 52
WHERE NOT EXISTS (
    SELECT 1 FROM EventSession
    WHERE EventId = 52 AND StartDateTime = CONCAT(d.festival_date, ' ', s.start_time, ':00')
);

-- ── EventId 53: Urban Frenchy Bistro Toujours — slots 17:30 / 19:15 / 21:00
INSERT INTO EventSession (
    EventId, StartDateTime, EndDateTime,
    CapacityTotal, CapacitySingleTicketLimit,
    HallName, SessionType, DurationMinutes,
    LanguageCode, MinAge, MaxAge,
    ReservationRequired, IsFree, Notes, HistoryTicketLabel,
    CtaLabel, CtaUrl, IsCancelled, IsActive
)
SELECT
    53,
    CONCAT(d.festival_date, ' ', s.start_time, ':00'),
    CONCAT(d.festival_date, ' ', s.end_time,   ':00'),
    40, 40, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL,
    'Reserve a Table', CONCAT('/restaurant/', e.Slug, '/reservation'), 0, 1
FROM (
    SELECT '2026-07-23' AS festival_date UNION ALL
    SELECT '2026-07-24' UNION ALL
    SELECT '2026-07-25' UNION ALL
    SELECT '2026-07-26'
) AS d
CROSS JOIN (
    SELECT '17:30' AS start_time, '19:30' AS end_time UNION ALL
    SELECT '19:15', '21:15' UNION ALL
    SELECT '21:00', '23:00'
) AS s
JOIN Event e ON e.EventId = 53
WHERE NOT EXISTS (
    SELECT 1 FROM EventSession
    WHERE EventId = 53 AND StartDateTime = CONCAT(d.festival_date, ' ', s.start_time, ':00')
);

-- ── EventSessionPrice: Adult €45 — Café de Roemer, Ratatouille, Restaurant ML, Restaurant Fris
INSERT INTO EventSessionPrice (EventSessionId, PriceTierId, Price, CurrencyCode, VatRate)
SELECT es.EventSessionId, 1, 45.00, 'EUR', 21.00
FROM EventSession es
WHERE es.EventId IN (47, 48, 49, 50)
AND NOT EXISTS (
    SELECT 1 FROM EventSessionPrice esp
    WHERE esp.EventSessionId = es.EventSessionId AND esp.PriceTierId = 1
);

-- ── EventSessionPrice: Child U12 €22.50 — same four restaurants
INSERT INTO EventSessionPrice (EventSessionId, PriceTierId, Price, CurrencyCode, VatRate)
SELECT es.EventSessionId, 2, 22.50, 'EUR', 21.00
FROM EventSession es
WHERE es.EventId IN (47, 48, 49, 50)
AND NOT EXISTS (
    SELECT 1 FROM EventSessionPrice esp
    WHERE esp.EventSessionId = es.EventSessionId AND esp.PriceTierId = 2
);

-- ── EventSessionPrice: Adult €35 — New Vegas, Grand Cafe Brinkman, Urban Frenchy Bistro Toujours
INSERT INTO EventSessionPrice (EventSessionId, PriceTierId, Price, CurrencyCode, VatRate)
SELECT es.EventSessionId, 1, 35.00, 'EUR', 21.00
FROM EventSession es
WHERE es.EventId IN (51, 52, 53)
AND NOT EXISTS (
    SELECT 1 FROM EventSessionPrice esp
    WHERE esp.EventSessionId = es.EventSessionId AND esp.PriceTierId = 1
);

-- ── EventSessionPrice: Child U12 €17.50 — same three restaurants
INSERT INTO EventSessionPrice (EventSessionId, PriceTierId, Price, CurrencyCode, VatRate)
SELECT es.EventSessionId, 2, 17.50, 'EUR', 21.00
FROM EventSession es
WHERE es.EventId IN (51, 52, 53)
AND NOT EXISTS (
    SELECT 1 FROM EventSessionPrice esp
    WHERE esp.EventSessionId = es.EventSessionId AND esp.PriceTierId = 2
);

-- =============================================================================
SELECT 'Yummy! migration complete — Story-page restaurant schema preserved.' AS Status;
