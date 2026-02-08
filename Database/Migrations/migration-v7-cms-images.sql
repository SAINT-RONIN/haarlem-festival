-- Migration: Add MEDIA type CMS items for homepage images
-- This allows CMS admins to update images through the admin panel

-- First, insert MediaAsset records for existing images
INSERT INTO `MediaAsset` (`FilePath`, `OriginalFileName`, `MimeType`, `FileSizeBytes`, `AltText`) VALUES
('/assets/Image/HeroImageHome.png', 'HeroImageHome.png', 'image/png', 0, 'Haarlem Festival hero background'),
('/assets/Image/explore-incoming-events.png', 'explore-incoming-events.png', 'image/png', 0, 'Explore upcoming events banner'),
('/assets/Image/what-is-haarlem.png', 'what-is-haarlem.png', 'image/png', 0, 'Aerial view of Haarlem city center'),
('/assets/Image/Image (Jazz).png', 'Image (Jazz).png', 'image/png', 0, 'Jazz musicians performing'),
('/assets/Image/Image (Dance).png', 'Image (Dance).png', 'image/png', 0, 'Dancers at festival'),
('/assets/Image/Image (History).png', 'Image (History).png', 'image/png', 0, 'Historic Haarlem buildings'),
('/assets/Image/Image (Yummy).png', 'Image (Yummy).png', 'image/png', 0, 'Festival restaurant food'),
('/assets/Image/Image (Story).png', 'Image (Story).png', 'image/png', 0, 'Storytelling performance');

-- Get the MediaAsset IDs (these will vary based on your database state)
-- For a fresh install, they would be 1-8

-- Add MEDIA items to hero_section (CmsSectionId = 1)
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`) VALUES
(1, 'hero_background_image', 'MEDIA', NULL, NULL, (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/HeroImageHome.png' LIMIT 1));

-- Add MEDIA items to banner_section (CmsSectionId = 2)
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`) VALUES
(2, 'banner_background_image', 'MEDIA', NULL, NULL, (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/explore-incoming-events.png' LIMIT 1));

-- Add MEDIA items to about_section (CmsSectionId = 3)
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`) VALUES
(3, 'about_image', 'MEDIA', NULL, NULL, (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/what-is-haarlem.png' LIMIT 1));

-- Add MEDIA items to event sections (CmsSectionId = 5-9)
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`) VALUES
(5, 'jazz_image', 'MEDIA', NULL, NULL, (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/Image (Jazz).png' LIMIT 1)),
(6, 'dance_image', 'MEDIA', NULL, NULL, (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/Image (Dance).png' LIMIT 1)),
(7, 'history_image', 'MEDIA', NULL, NULL, (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/Image (History).png' LIMIT 1)),
(8, 'restaurant_image', 'MEDIA', NULL, NULL, (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/Image (Yummy).png' LIMIT 1)),
(9, 'storytelling_image', 'MEDIA', NULL, NULL, (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/Image (Story).png' LIMIT 1));

