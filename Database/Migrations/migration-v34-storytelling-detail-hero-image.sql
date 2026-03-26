-- migration-v34-storytelling-detail-hero-image.sql
--
-- Adds a hero_image CMS item to each storytelling detail event section.
-- This allows editors to customise the hero background per event via the CMS.
--
-- Sections covered (CmsPageId = 7, slug 'storytelling-detail'):
--   CmsSectionId 61 = event_34  (Winnie de Poeh)
--   CmsSectionId 62 = event_35  (Omdenken Podcast)
--   CmsSectionId 63 = event_36  (Buurderij Haarlem)
--   CmsSectionId 64 = event_37  (Corrie voor kinderen)
--   CmsSectionId 65 = event_38  (Winnaars verhalenvertel wedstrijd)
--   CmsSectionId 66 = event_39  (Het verhaal van de Oeserzwammerij)
--   CmsSectionId 67 = event_40  (Flip Thinking Podcast)
--   CmsSectionId 68 = event_41  (Meneer Anansi)
--   CmsSectionId 69 = event_42  (Mister Anansi)
--   CmsSectionId 70 = event_43  (Podcastlast Haarlem Special)
--   CmsSectionId 71 = event_44  (De geschiedenis van familie ten Boom)
--   CmsSectionId 72 = event_45  (The History of the Ten Boom Family)
--   CmsSectionId 73 = event_46  (Winners of the Storytelling Competition)
--
-- TextValue is left empty so the mapper falls back to Event.FeaturedImageAssetId
-- until an editor uploads a custom image via the CMS.
--
-- Safe to run multiple times: uses INSERT IGNORE.
-- ---------------------------------------------------------------

INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(61, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, NOW()),
(62, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, NOW()),
(63, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, NOW()),
(64, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, NOW()),
(65, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, NOW()),
(66, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, NOW()),
(67, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, NOW()),
(68, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, NOW()),
(69, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, NOW()),
(70, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, NOW()),
(71, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, NOW()),
(72, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, NOW()),
(73, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, NOW());

-- Add hero button labels to storytelling detail event sections
-- These drive the "Back to storytelling" and "Reserve your spot" buttons in the detail hero

INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(61, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, NOW()),
(61, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, NOW()),
(62, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, NOW()),
(62, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, NOW()),
(63, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, NOW()),
(63, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, NOW()),
(64, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, NOW()),
(64, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, NOW()),
(65, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, NOW()),
(65, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, NOW()),
(66, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, NOW()),
(66, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, NOW()),
(67, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, NOW()),
(67, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, NOW()),
(68, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, NOW()),
(68, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, NOW()),
(69, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, NOW()),
(69, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, NOW()),
(70, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, NOW()),
(70, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, NOW()),
(71, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, NOW()),
(71, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, NOW()),
(72, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, NOW()),
(72, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, NOW()),
(73, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, NOW()),
(73, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, NOW());

-- Add schedule CTA button text for detail pages (overrides overview page "Discover")
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(61, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, NOW()),
(62, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, NOW()),
(63, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, NOW()),
(64, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, NOW()),
(65, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, NOW()),
(66, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, NOW()),
(67, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, NOW()),
(68, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, NOW()),
(69, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, NOW()),
(70, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, NOW()),
(71, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, NOW()),
(72, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, NOW()),
(73, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, NOW());
