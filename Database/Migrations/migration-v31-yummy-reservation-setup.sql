-- migration-v31-yummy-reservation-setup.sql
-- Combines all reservation-related changes for the Yummy restaurant feature:
--   1. Fix hero "Discover restaurants" button to scroll to the correct section
--   2. Add TimeSlots, PriceAdult, PriceChild columns to Restaurant
--   3. Seed per-restaurant time slots and default prices
--   4. Create the Reservation table

-- ─────────────────────────────────────────────────────────────
-- 1. Fix hero primary button link to point at the cards grid
-- ─────────────────────────────────────────────────────────────
UPDATE CmsItem ci
INNER JOIN CmsSection cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '#restaurants-grid'
WHERE ci.ItemKey = 'hero_button_primary_link'
  AND ci.TextValue = '#restaurants'
  AND cp.Slug = 'restaurant'
  AND cs.SectionKey = 'hero_section';

-- ─────────────────────────────────────────────────────────────
-- 2. Add new columns to Restaurant
-- ─────────────────────────────────────────────────────────────
ALTER TABLE Restaurant
    ADD COLUMN IF NOT EXISTS TimeSlots  TEXT          NULL AFTER SpecialRequestsNote,
    ADD COLUMN IF NOT EXISTS PriceAdult DECIMAL(10,2) NULL AFTER TimeSlots,
    ADD COLUMN IF NOT EXISTS PriceChild DECIMAL(10,2) NULL AFTER PriceAdult;

-- ─────────────────────────────────────────────────────────────
-- 3. Seed time slots per restaurant, then default prices
-- ─────────────────────────────────────────────────────────────

-- Restaurant-specific time slots
UPDATE Restaurant SET TimeSlots = '17:00, 19:15, 21:30' WHERE Name LIKE '%Ratatouille%';
UPDATE Restaurant SET TimeSlots = '17:30, 19:15, 21:00' WHERE Name LIKE '%Toujours%';

-- Generic fallback for any restaurant that still has no time slots
UPDATE Restaurant SET TimeSlots = '16:30, 18:30, 20:30'
WHERE IsActive = 1 AND (TimeSlots IS NULL OR TimeSlots = '');

-- Default prices for all active restaurants (edit per restaurant via CMS or a later migration)
UPDATE Restaurant SET PriceAdult = 45.00, PriceChild = 22.50
WHERE IsActive = 1 AND PriceAdult IS NULL;

-- ─────────────────────────────────────────────────────────────
-- 4. Create Reservation table
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `Reservation` (
    `ReservationId`   INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `RestaurantId`    INT           NOT NULL,
    `DiningDate`      VARCHAR(20)   NOT NULL COMMENT 'Thursday / Friday / Saturday / Sunday',
    `TimeSlot`        VARCHAR(20)   NOT NULL COMMENT 'e.g. 17:00',
    `AdultsCount`     INT UNSIGNED  NOT NULL DEFAULT 0,
    `ChildrenCount`   INT UNSIGNED  NOT NULL DEFAULT 0,
    `SpecialRequests` TEXT          NULL,
    `TotalFee`        DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '€10 per person reservation deposit',
    `CreatedAt`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ReservationId`),
    INDEX `idx_reservation_restaurant` (`RestaurantId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;