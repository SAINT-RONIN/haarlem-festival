-- =====================================================
-- CMS additions for missing hardcoded text elements
-- Run this after migration-v3.sql
-- =====================================================

-- Add a new section for global UI elements (navigation, common buttons, labels)
INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(12, 1, 'global_ui');

-- Add missing CMS items for global UI elements
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
-- Site branding
(40, 12, 'site_name', 'TEXT', 'Haarlem Festival', NULL, NULL, NOW()),

-- Navigation labels
(41, 12, 'nav_home', 'TEXT', 'Home', NULL, NULL, NOW()),
(42, 12, 'nav_jazz', 'TEXT', 'Jazz', NULL, NULL, NOW()),
(43, 12, 'nav_dance', 'TEXT', 'Dance', NULL, NULL, NOW()),
(44, 12, 'nav_history', 'TEXT', 'History', NULL, NULL, NOW()),
(45, 12, 'nav_restaurant', 'TEXT', 'Restaurant', NULL, NULL, NOW()),
(46, 12, 'nav_storytelling', 'TEXT', 'Storytelling', NULL, NULL, NOW()),

-- Common button labels
(47, 12, 'btn_my_program', 'BUTTON_TEXT', 'My Program', NULL, NULL, NOW()),

-- Schedule section labels
(48, 12, 'label_events_count', 'TEXT', 'events', NULL, NULL, NOW()),
(49, 12, 'label_no_events', 'TEXT', 'No events scheduled', NULL, NULL, NOW()),

-- Explore button template (use {title} as placeholder)
(50, 12, 'btn_explore_template', 'TEXT', 'Explore {title} Events', NULL, NULL, NOW());

