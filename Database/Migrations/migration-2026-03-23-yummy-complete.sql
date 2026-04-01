-- =============================================================
-- migration-yummy-complete.sql
-- Complete Yummy (Restaurant) feature migration.
-- Run this ONE file — it replaces all previous yummy migrations.
--
-- What this does:
--   1. Add TimeSlots, PriceAdult, PriceChild columns to Restaurant
--   2. Seed time slots and prices per restaurant
--   3. Create Reservation table
--   4. Create RestaurantImage table
--   5. Insert images for Ratatouille and Urban Frenchy Bistro Toujours
--   6. Fix hero button CMS link
-- =============================================================

-- ─────────────────────────────────────────────────────────────
-- 1. Add columns to Restaurant
-- ─────────────────────────────────────────────────────────────
ALTER TABLE Restaurant
    ADD COLUMN IF NOT EXISTS TimeSlots  TEXT          NULL AFTER SpecialRequestsNote,
    ADD COLUMN IF NOT EXISTS PriceAdult DECIMAL(10,2) NULL AFTER TimeSlots,
    ADD COLUMN IF NOT EXISTS PriceChild DECIMAL(10,2) NULL AFTER PriceAdult;

-- ─────────────────────────────────────────────────────────────
-- 2. Seed time slots and prices
-- ─────────────────────────────────────────────────────────────
UPDATE Restaurant SET TimeSlots = '17:00, 19:15, 21:30' WHERE Name LIKE '%Ratatouille%';
UPDATE Restaurant SET TimeSlots = '17:30, 19:15, 21:00' WHERE Name LIKE '%Toujours%';

-- Fallback for any remaining active restaurants
UPDATE Restaurant SET TimeSlots = '16:30, 18:30, 20:30'
WHERE IsActive = 1 AND (TimeSlots IS NULL OR TimeSlots = '');

-- Default prices for all active restaurants
UPDATE Restaurant SET PriceAdult = 45.00, PriceChild = 22.50
WHERE IsActive = 1 AND PriceAdult IS NULL;

-- ─────────────────────────────────────────────────────────────
-- 3. Create Reservation table
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Reservation (
    ReservationId   INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    RestaurantId    INT           NOT NULL,
    DiningDate      VARCHAR(20)   NOT NULL COMMENT 'Thursday / Friday / Saturday / Sunday',
    TimeSlot        VARCHAR(20)   NOT NULL COMMENT 'e.g. 17:00',
    AdultsCount     INT UNSIGNED  NOT NULL DEFAULT 0,
    ChildrenCount   INT UNSIGNED  NOT NULL DEFAULT 0,
    SpecialRequests TEXT          NULL,
    TotalFee        DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '10 per person reservation deposit',
    CreatedAt       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ReservationId),
    INDEX idx_reservation_restaurant (RestaurantId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────────
-- 4. Create RestaurantImage table
--    Stores per-restaurant images by type: about, chef, menu,
--    gallery, reservation. Same pattern as ArtistGalleryImage.
-- ─────────────────────────────────────────────────────────────
DROP TABLE IF EXISTS RestaurantImage;

CREATE TABLE RestaurantImage (
    RestaurantImageId INT          NOT NULL AUTO_INCREMENT,
    RestaurantId      INT          NOT NULL,
    ImagePath         VARCHAR(500) NOT NULL,
    ImageType         VARCHAR(50)  NOT NULL,
    SortOrder         INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (RestaurantImageId),
    CONSTRAINT FK_RestaurantImage_Restaurant
        FOREIGN KEY (RestaurantId) REFERENCES Restaurant(RestaurantId)
        ON DELETE CASCADE
);

-- ─────────────────────────────────────────────────────────────
-- 5. Insert images
--    Uses name-based lookup so IDs don't need to match exactly.
-- ─────────────────────────────────────────────────────────────

-- Ratatouille
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/ratatouille-about.png',       'about',       1 FROM Restaurant WHERE Name LIKE '%Ratatouille%' LIMIT 1;
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/ratatouille-chef.png',        'chef',        1 FROM Restaurant WHERE Name LIKE '%Ratatouille%' LIMIT 1;
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/ratatouille-menu-1.png',      'menu',        1 FROM Restaurant WHERE Name LIKE '%Ratatouille%' LIMIT 1;
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/ratatouille-menu-2.png',      'menu',        2 FROM Restaurant WHERE Name LIKE '%Ratatouille%' LIMIT 1;
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/ratatouille-gallery-1.png',   'gallery',     1 FROM Restaurant WHERE Name LIKE '%Ratatouille%' LIMIT 1;
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/ratatouille-gallery-2.png',   'gallery',     2 FROM Restaurant WHERE Name LIKE '%Ratatouille%' LIMIT 1;
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/ratatouille-gallery-3.png',   'gallery',     3 FROM Restaurant WHERE Name LIKE '%Ratatouille%' LIMIT 1;
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/ratatouille-reservation.jpg', 'reservation', 1 FROM Restaurant WHERE Name LIKE '%Ratatouille%' LIMIT 1;

-- Urban Frenchy Bistro Toujours
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/toujours-about.png',     'about',   1 FROM Restaurant WHERE Name LIKE '%Toujours%' LIMIT 1;
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/toujours-chef.jpg',      'chef',    1 FROM Restaurant WHERE Name LIKE '%Toujours%' LIMIT 1;
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/toujours-menu-1.png',    'menu',    1 FROM Restaurant WHERE Name LIKE '%Toujours%' LIMIT 1;
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/toujours-menu-2.png',    'menu',    2 FROM Restaurant WHERE Name LIKE '%Toujours%' LIMIT 1;
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/toujours-gallery-1.png', 'gallery', 1 FROM Restaurant WHERE Name LIKE '%Toujours%' LIMIT 1;
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/toujours-gallery-2.png', 'gallery', 2 FROM Restaurant WHERE Name LIKE '%Toujours%' LIMIT 1;
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder)
SELECT RestaurantId, '/assets/Image/restaurants/toujours-gallery-3.png', 'gallery', 3 FROM Restaurant WHERE Name LIKE '%Toujours%' LIMIT 1;

-- ─────────────────────────────────────────────────────────────
-- 6. Fix hero button CMS link
-- ─────────────────────────────────────────────────────────────
UPDATE CmsItem ci
INNER JOIN CmsSection cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '#restaurants-grid'
WHERE ci.ItemKey = 'hero_button_primary_link'
  AND ci.TextValue = '#restaurants'
  AND cp.Slug = 'restaurant'
  AND cs.SectionKey = 'hero_section';