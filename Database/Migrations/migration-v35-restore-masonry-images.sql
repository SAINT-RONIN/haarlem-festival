-- Restore masonry images for storytelling overview page
-- CmsSection 16 = masonry_section, CmsPage 2 = storytelling

INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(16, 'masonry_heading', 'HEADING', 'Moments you are about to discover', NULL, NULL, NOW()),
(16, 'masonry_image_01', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(16, 'masonry_image_02', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, NOW()),
(16, 'masonry_image_03', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(16, 'masonry_image_04', 'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, NOW()),
(16, 'masonry_image_05', 'IMAGE_PATH', '/assets/Image/storytelling/pig.jpg', NULL, NULL, NOW()),
(16, 'masonry_image_06', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(16, 'masonry_image_07', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(16, 'masonry_image_08', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, NOW()),
(16, 'masonry_image_09', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, NOW()),
(16, 'masonry_image_10', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, NOW()),
(16, 'masonry_image_11', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, NULL, NOW()),
(16, 'masonry_image_12', 'IMAGE_PATH', '/assets/Image/storytelling/WinnieThePoohHeader.png', NULL, NULL, NOW());
