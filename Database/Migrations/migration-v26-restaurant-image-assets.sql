-- Migration v26: Link restaurant card images to MediaAsset table
-- This follows the same pattern used by Artist.ImageAssetId in Jazz.
--
-- What this migration does:
-- 1. Inserts one MediaAsset row for each restaurant card image file
-- 2. Sets Restaurant.ImageAssetId to point to the correct MediaAsset row
--
-- After this migration, the code can JOIN Restaurant + MediaAsset
-- to get the image path directly from the database (no more filename guessing).

-- ── Step 1: Insert MediaAsset rows for each restaurant card image ────

INSERT INTO `MediaAsset` (`FilePath`, `OriginalFileName`, `MimeType`, `FileSizeBytes`, `AltText`)
SELECT '/assets/Image/restaurants/Restaurant-CafeDeRoemer-card.png',
       'Restaurant-CafeDeRoemer-card.png', 'image/png', 0, 'Café de Roemer restaurant'
WHERE NOT EXISTS (
    SELECT 1 FROM `MediaAsset` WHERE `FilePath` = '/assets/Image/restaurants/Restaurant-CafeDeRoemer-card.png'
);

INSERT INTO `MediaAsset` (`FilePath`, `OriginalFileName`, `MimeType`, `FileSizeBytes`, `AltText`)
SELECT '/assets/Image/restaurants/Restaurant-Ratatouille-card.png',
       'Restaurant-Ratatouille-card.png', 'image/png', 0, 'Ratatouille restaurant'
WHERE NOT EXISTS (
    SELECT 1 FROM `MediaAsset` WHERE `FilePath` = '/assets/Image/restaurants/Restaurant-Ratatouille-card.png'
);

INSERT INTO `MediaAsset` (`FilePath`, `OriginalFileName`, `MimeType`, `FileSizeBytes`, `AltText`)
SELECT '/assets/Image/restaurants/Restaurant-RestaurantML-card.png',
       'Restaurant-RestaurantML-card.png', 'image/png', 0, 'Restaurant ML'
WHERE NOT EXISTS (
    SELECT 1 FROM `MediaAsset` WHERE `FilePath` = '/assets/Image/restaurants/Restaurant-RestaurantML-card.png'
);

INSERT INTO `MediaAsset` (`FilePath`, `OriginalFileName`, `MimeType`, `FileSizeBytes`, `AltText`)
SELECT '/assets/Image/restaurants/Restaurant-RestaurantFris-card.png',
       'Restaurant-RestaurantFris-card.png', 'image/png', 0, 'Restaurant Fris'
WHERE NOT EXISTS (
    SELECT 1 FROM `MediaAsset` WHERE `FilePath` = '/assets/Image/restaurants/Restaurant-RestaurantFris-card.png'
);

INSERT INTO `MediaAsset` (`FilePath`, `OriginalFileName`, `MimeType`, `FileSizeBytes`, `AltText`)
SELECT '/assets/Image/restaurants/Restaurant-NewVegas-card.jpg',
       'Restaurant-NewVegas-card.jpg', 'image/jpeg', 0, 'New Vegas restaurant'
WHERE NOT EXISTS (
    SELECT 1 FROM `MediaAsset` WHERE `FilePath` = '/assets/Image/restaurants/Restaurant-NewVegas-card.jpg'
);

INSERT INTO `MediaAsset` (`FilePath`, `OriginalFileName`, `MimeType`, `FileSizeBytes`, `AltText`)
SELECT '/assets/Image/restaurants/Restaurant-GrandCafeBrinkman-card.png',
       'Restaurant-GrandCafeBrinkman-card.png', 'image/png', 0, 'Grand Cafe Brinkman'
WHERE NOT EXISTS (
    SELECT 1 FROM `MediaAsset` WHERE `FilePath` = '/assets/Image/restaurants/Restaurant-GrandCafeBrinkman-card.png'
);

INSERT INTO `MediaAsset` (`FilePath`, `OriginalFileName`, `MimeType`, `FileSizeBytes`, `AltText`)
SELECT '/assets/Image/restaurants/Restaurant-UrbanFrenchyBistroToujours-card.png',
       'Restaurant-UrbanFrenchyBistroToujours-card.png', 'image/png', 0, 'Urban Frenchy Bistro Toujours'
WHERE NOT EXISTS (
    SELECT 1 FROM `MediaAsset` WHERE `FilePath` = '/assets/Image/restaurants/Restaurant-UrbanFrenchyBistroToujours-card.png'
);

-- ── Step 2: Link each Restaurant to its MediaAsset ──────────────────
-- Uses the restaurant name + media file path to find the correct match.
-- This avoids using hardcoded IDs.

UPDATE `Restaurant` r
    JOIN `MediaAsset` ma ON ma.`FilePath` = '/assets/Image/restaurants/Restaurant-CafeDeRoemer-card.png'
SET r.`ImageAssetId` = ma.`MediaAssetId`
WHERE r.`Name` = 'Café de Roemer';

UPDATE `Restaurant` r
    JOIN `MediaAsset` ma ON ma.`FilePath` = '/assets/Image/restaurants/Restaurant-Ratatouille-card.png'
SET r.`ImageAssetId` = ma.`MediaAssetId`
WHERE r.`Name` = 'Ratatouille';

UPDATE `Restaurant` r
    JOIN `MediaAsset` ma ON ma.`FilePath` = '/assets/Image/restaurants/Restaurant-RestaurantML-card.png'
SET r.`ImageAssetId` = ma.`MediaAssetId`
WHERE r.`Name` = 'Restaurant ML';

UPDATE `Restaurant` r
    JOIN `MediaAsset` ma ON ma.`FilePath` = '/assets/Image/restaurants/Restaurant-RestaurantFris-card.png'
SET r.`ImageAssetId` = ma.`MediaAssetId`
WHERE r.`Name` = 'Restaurant Fris';

UPDATE `Restaurant` r
    JOIN `MediaAsset` ma ON ma.`FilePath` = '/assets/Image/restaurants/Restaurant-NewVegas-card.jpg'
SET r.`ImageAssetId` = ma.`MediaAssetId`
WHERE r.`Name` = 'New Vegas';

UPDATE `Restaurant` r
    JOIN `MediaAsset` ma ON ma.`FilePath` = '/assets/Image/restaurants/Restaurant-GrandCafeBrinkman-card.png'
SET r.`ImageAssetId` = ma.`MediaAssetId`
WHERE r.`Name` = 'Grand Cafe Brinkman';

UPDATE `Restaurant` r
    JOIN `MediaAsset` ma ON ma.`FilePath` = '/assets/Image/restaurants/Restaurant-UrbanFrenchyBistroToujours-card.png'
SET r.`ImageAssetId` = ma.`MediaAssetId`
WHERE r.`Name` = 'Urban Frenchy Bistro Toujours';

-- ── Step 3: Verification queries ────────────────────────────────────
-- Run these after the migration to confirm everything is linked:
--
-- SELECT r.RestaurantId, r.Name, r.ImageAssetId, ma.FilePath
-- FROM Restaurant r
-- LEFT JOIN MediaAsset ma ON r.ImageAssetId = ma.MediaAssetId
-- WHERE r.IsActive = 1
-- ORDER BY r.Name;
