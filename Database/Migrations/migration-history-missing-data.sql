-- Migration: Add missing History data lost during previous merges
-- Date: 2026-03-22
-- Adds: PriceTier 'Single', prices for Dutch/Chinese sessions, CMS image items

-- 1. Add missing PriceTier
INSERT IGNORE INTO PriceTier (PriceTierId, Name) VALUES (6, 'Single');

-- 2. Add missing prices for Dutch/Chinese History sessions (90-109)
INSERT IGNORE INTO EventSessionPrice (EventSessionPriceId, EventSessionId, PriceTierId, Price, CurrencyCode, VatRate) VALUES
(180, 90, 6, 17.50, 'EUR', 21.00),
(182, 91, 6, 17.50, 'EUR', 21.00),
(184, 92, 6, 17.50, 'EUR', 21.00),
(186, 93, 6, 17.50, 'EUR', 21.00),
(188, 94, 6, 17.50, 'EUR', 21.00),
(189, 95, 6, 17.50, 'EUR', 21.00),
(191, 96, 6, 17.50, 'EUR', 21.00),
(193, 97, 6, 17.50, 'EUR', 21.00),
(194, 100, 6, 17.50, 'EUR', 21.00),
(196, 98, 6, 17.50, 'EUR', 21.00),
(197, 101, 6, 17.50, 'EUR', 21.00),
(198, 102, 6, 17.50, 'EUR', 21.00),
(200, 99, 6, 17.50, 'EUR', 21.00),
(201, 103, 6, 17.50, 'EUR', 21.00),
(202, 104, 6, 17.50, 'EUR', 21.00),
(204, 105, 6, 17.50, 'EUR', 21.00),
(205, 106, 6, 17.50, 'EUR', 21.00),
(207, 107, 6, 17.50, 'EUR', 21.00),
(208, 108, 6, 17.50, 'EUR', 21.00),
(210, 109, 6, 17.50, 'EUR', 21.00);

-- 3. Add missing CMS image and text items for history page
-- Section IDs: hero_section=42, gradient_section=45, intro_section=40
INSERT IGNORE INTO CmsItem (CmsItemId, CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc) VALUES
(1326, 42, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/History/History-hero.png', NOW()),
(1327, 42, 'hero_subtitle', 'TEXT', 'Explore nine centuries of turbulent history, magnificent architecture, and cultural treasures', NOW()),
(1328, 45, 'gradient_background_image', 'IMAGE_PATH', '/assets/Image/History/History-second-section.png', NOW()),
(1329, 40, 'intro_image', 'IMAGE_PATH', '/assets/Image/History/History-third-section.png', NOW()),
(1330, 40, 'intro_image_alt', 'TEXT', 'A corner of a historic building in Haarlem', NOW());
