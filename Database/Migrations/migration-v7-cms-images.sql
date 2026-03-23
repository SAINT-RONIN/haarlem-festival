-- =====================================================
-- Migration v7: Add MEDIA type CMS items for homepage images
-- This allows CMS admins to update images through the admin panel
-- Safe & Idempotent: Uses INSERT IGNORE and WHERE NOT EXISTS
-- =====================================================

-- First, insert MediaAsset records for existing images (IGNORE duplicates)
INSERT IGNORE INTO `MediaAsset` (`FilePath`, `OriginalFileName`, `MimeType`, `FileSizeBytes`, `AltText`) VALUES
('/assets/Image/HeroImageHome.png', 'HeroImageHome.png', 'image/png', 0, 'Haarlem Festival hero background'),
('/assets/Image/explore-incoming-events.png', 'explore-incoming-events.png', 'image/png', 0, 'Explore upcoming events banner'),
('/assets/Image/what-is-haarlem.png', 'what-is-haarlem.png', 'image/png', 0, 'Aerial view of Haarlem city center'),
('/assets/Image/Image (Jazz).png', 'Image (Jazz).png', 'image/png', 0, 'Jazz musicians performing'),
('/assets/Image/Image (Dance).png', 'Image (Dance).png', 'image/png', 0, 'Dancers at festival'),
('/assets/Image/Image (History).png', 'Image (History).png', 'image/png', 0, 'Historic Haarlem buildings'),
('/assets/Image/Image (Yummy).png', 'Image (Yummy).png', 'image/png', 0, 'Festival restaurant food'),
('/assets/Image/Image (Story).png', 'Image (Story).png', 'image/png', 0, 'Storytelling performance');

-- =====================================================
-- Add MEDIA items to hero_section (CmsSectionId = 1)
-- Only insert if not exists
-- =====================================================
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`)
SELECT 1, 'hero_background_image', 'MEDIA', NULL, NULL,
       (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/HeroImageHome.png' LIMIT 1)
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem` WHERE `CmsSectionId` = 1 AND `ItemKey` = 'hero_background_image'
);

-- =====================================================
-- Add MEDIA items to banner_section (CmsSectionId = 2)
-- =====================================================
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`)
SELECT 2, 'banner_background_image', 'MEDIA', NULL, NULL,
       (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/explore-incoming-events.png' LIMIT 1)
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem` WHERE `CmsSectionId` = 2 AND `ItemKey` = 'banner_background_image'
);

-- =====================================================
-- Add MEDIA items to about_section (CmsSectionId = 3)
-- =====================================================
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`)
SELECT 3, 'about_image', 'MEDIA', NULL, NULL,
       (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/what-is-haarlem.png' LIMIT 1)
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem` WHERE `CmsSectionId` = 3 AND `ItemKey` = 'about_image'
);

-- =====================================================
-- Add MEDIA items to event sections (CmsSectionId = 5-9)
-- =====================================================

-- Jazz (CmsSectionId = 5)
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`)
SELECT 5, 'jazz_image', 'MEDIA', NULL, NULL,
       (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/Image (Jazz).png' LIMIT 1)
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem` WHERE `CmsSectionId` = 5 AND `ItemKey` = 'jazz_image'
);

-- Dance (CmsSectionId = 6)
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`)
SELECT 6, 'dance_image', 'MEDIA', NULL, NULL,
       (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/Image (Dance).png' LIMIT 1)
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem` WHERE `CmsSectionId` = 6 AND `ItemKey` = 'dance_image'
);

-- History (CmsSectionId = 7)
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`)
SELECT 7, 'history_image', 'MEDIA', NULL, NULL,
       (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/Image (History).png' LIMIT 1)
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem` WHERE `CmsSectionId` = 7 AND `ItemKey` = 'history_image'
);

-- Restaurant (CmsSectionId = 8)
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`)
SELECT 8, 'restaurant_image', 'MEDIA', NULL, NULL,
       (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/Image (Yummy).png' LIMIT 1)
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem` WHERE `CmsSectionId` = 8 AND `ItemKey` = 'restaurant_image'
);

-- Storytelling (CmsSectionId = 9)
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`)
SELECT 9, 'storytelling_image', 'MEDIA', NULL, NULL,
       (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/Image (Story).png' LIMIT 1)
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem` WHERE `CmsSectionId` = 9 AND `ItemKey` = 'storytelling_image'
);

