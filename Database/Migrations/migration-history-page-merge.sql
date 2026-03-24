-- ============================================================================
-- Migration: History Detail Pages Merge
-- Source: complete-database-history-22-03-2026.sql (History-page branch)
-- Target: haarlem_festival_db (15).sql (Story-page branch)
-- Date: 2026-03-22
--
-- Adds:
--   1. New Venue (id 15) for history tour starting point
--   2. Update Event 33 (history walking tour) title, slug, and venue
--   3. CmsItemKey lookup table + FK constraints
--   4. 3 new CmsPages for history detail pages (grote-markt, amsterdamse-poort, molen-de-adriaan)
--   5. 12 new CmsSections for history detail pages (hero, intro, facts, significance per page)
--   6. CmsItem data for all 3 history detail pages
--   7. CmsItem link data for historical_locations_section (linking to detail pages)
--
-- Does NOT:
--   - Drop or recreate existing tables
--   - Delete existing data
--   - Re-add Event 41 (Meneer Anansi) which was intentionally removed in migration-v37
--   - Modify Restaurant table columns
--   - Rename StripeWebhookEvent columns
--
-- ID Remapping (to avoid conflicts with existing data):
--   CmsSection IDs: remapped to 81-92 (our max was 80)
--   CmsItem IDs: remapped to 1266+ (our max was 1265)
--   CmsPage IDs: 11-13 (our max was 10, no conflict)
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- ============================================================================
-- 1. New Venue for history tour starting point
-- ============================================================================
INSERT IGNORE INTO `Venue` (`VenueId`, `Name`, `AddressLine`, `City`, `CreatedAtUtc`, `IsActive`) VALUES
(15, 'A giant flag near Church of St. Bavo at Grote Markt', '', 'Haarlem', '2026-03-18 11:24:34', 1);

-- ============================================================================
-- 2. Update Event 33 (History Walking Tour) — title, slug, and venue
-- ============================================================================
UPDATE `Event`
SET `Title` = 'A Stroll Through History',
    `Slug` = 'a-stroll-through-history',
    `VenueId` = 15
WHERE `EventId` = 33;

-- ============================================================================
-- 3. CmsItemKey lookup table
-- ============================================================================
CREATE TABLE IF NOT EXISTS `CmsItemKey` (
  `ItemKey` varchar(80) NOT NULL,
  `ExpectedItemType` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

ALTER TABLE `CmsItemKey`
  ADD PRIMARY KEY IF NOT EXISTS (`ItemKey`),
  ADD KEY IF NOT EXISTS `FK_CmsItemKey_Type` (`ExpectedItemType`);

-- Insert all CmsItemKey rows (IGNORE to skip any that already exist)
INSERT IGNORE INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`) VALUES
('about_button', 'BUTTON_TEXT'),
('btn_my_program', 'BUTTON_TEXT'),
('dance_button', 'BUTTON_TEXT'),
('detail_hero_btn_primary', 'BUTTON_TEXT'),
('detail_hero_btn_secondary', 'BUTTON_TEXT'),
('detail_reservation_btn', 'BUTTON_TEXT'),
('filter_all', 'BUTTON_TEXT'),
('filter_dutch', 'BUTTON_TEXT'),
('filter_european', 'BUTTON_TEXT'),
('filter_fish_seafood', 'BUTTON_TEXT'),
('filter_french', 'BUTTON_TEXT'),
('filter_modern', 'BUTTON_TEXT'),
('filter_vegetarian', 'BUTTON_TEXT'),
('hero_button', 'BUTTON_TEXT'),
('hero_button_primary', 'BUTTON_TEXT'),
('hero_button_secondary', 'BUTTON_TEXT'),
('history_button', 'BUTTON_TEXT'),
('jazz_button', 'BUTTON_TEXT'),
('restaurant_1_about_label', 'BUTTON_TEXT'),
('restaurant_2_about_label', 'BUTTON_TEXT'),
('restaurant_2_image', 'BUTTON_TEXT'),
('restaurant_4_about_label', 'BUTTON_TEXT'),
('restaurant_5_about_label', 'BUTTON_TEXT'),
('restaurant_5_book_label', 'BUTTON_TEXT'),
('restaurant_6_about_label', 'BUTTON_TEXT'),
('restaurant_6_book_label', 'BUTTON_TEXT'),
('restaurant_7_about_label', 'BUTTON_TEXT'),
('restaurant_7_book_label', 'BUTTON_TEXT'),
('restaurant_button', 'BUTTON_TEXT'),
('schedule_cta_button', 'BUTTON_TEXT'),
('schedule_cta_button_text', 'BUTTON_TEXT'),
('schedule_filters_button_text', 'BUTTON_TEXT'),
('storytelling_button', 'BUTTON_TEXT'),
('venue_filter_all', 'BUTTON_TEXT'),
('venue_filter_dance', 'BUTTON_TEXT'),
('venue_filter_history', 'BUTTON_TEXT'),
('venue_filter_jazz', 'BUTTON_TEXT'),
('venue_filter_restaurants', 'BUTTON_TEXT'),
('venue_filter_stories', 'BUTTON_TEXT'),
('about_heading', 'HEADING'),
('about_main_title', 'HEADING'),
('architectural_significance_heading', 'HEADING'),
('artists_heading', 'HEADING'),
('banner_main_title', 'HEADING'),
('booking_cta_heading', 'HEADING'),
('cards_title', 'HEADING'),
('dance_title', 'HEADING'),
('detail_about_title_prefix', 'HEADING'),
('detail_chef_title', 'HEADING'),
('detail_contact_title', 'HEADING'),
('detail_gallery_title', 'HEADING'),
('detail_location_title', 'HEADING'),
('detail_menu_title', 'HEADING'),
('detail_practical_title', 'HEADING'),
('detail_reservation_title', 'HEADING'),
('events_main_title', 'HEADING'),
('facts_heading', 'HEADING'),
('gradient_heading', 'HEADING'),
('hero_main_title', 'HEADING'),
('historical_locations_heading', 'HEADING'),
('historical_significance_heading', 'HEADING'),
('history_title', 'HEADING'),
('instructions_card_1_title', 'HEADING'),
('instructions_card_2_title', 'HEADING'),
('instructions_card_3_title', 'HEADING'),
('instructions_title', 'HEADING'),
('intro_heading', 'HEADING'),
('intro2_heading', 'HEADING'),
('jazz_title', 'HEADING'),
('masonry_heading', 'HEADING'),
('overview_heading', 'HEADING'),
('pricing_heading', 'HEADING'),
('restaurant_1_name', 'HEADING'),
('restaurant_2_book_label', 'HEADING'),
('restaurant_3_about_label', 'HEADING'),
('restaurant_3_book_label', 'HEADING'),
('restaurant_3_description', 'HEADING'),
('restaurant_3_image', 'HEADING'),
('restaurant_4_address', 'HEADING'),
('restaurant_4_distance_text', 'HEADING'),
('restaurant_4_name', 'HEADING'),
('restaurant_6_name', 'HEADING'),
('restaurant_7_name', 'HEADING'),
('restaurant_title', 'HEADING'),
('route_heading', 'HEADING'),
('schedule_cta_heading', 'HEADING'),
('schedule_main_title', 'HEADING'),
('schedule_title', 'HEADING'),
('storytelling_title', 'HEADING'),
('ticket_options_heading', 'HEADING'),
('venue_main_title', 'HEADING'),
('venues_heading', 'HEADING'),
('about_body', 'HTML'),
('about_description', 'HTML'),
('schedule_additional_info_body', 'HTML'),
('about_image_1', 'IMAGE_PATH'),
('about_image_2', 'IMAGE_PATH'),
('album_1_image', 'IMAGE_PATH'),
('album_2_image', 'IMAGE_PATH'),
('album_3_image', 'IMAGE_PATH'),
('gallery_image_1', 'IMAGE_PATH'),
('gallery_image_2', 'IMAGE_PATH'),
('gallery_image_3', 'IMAGE_PATH'),
('gallery_image_4', 'IMAGE_PATH'),
('gallery_image_5', 'IMAGE_PATH'),
('gradient_background_image', 'IMAGE_PATH'),
('hero_background_image', 'IMAGE_PATH'),
('hero_map_image', 'IMAGE_PATH'),
('highlight_1_image', 'IMAGE_PATH'),
('highlight_2_image', 'IMAGE_PATH'),
('highlight_3_image', 'IMAGE_PATH'),
('history_amsterdamsepoort_image', 'IMAGE_PATH'),
('history_grotemarkt_image', 'IMAGE_PATH'),
('history_molendeadriaan_image', 'IMAGE_PATH'),
('intro_image', 'IMAGE_PATH'),
('intro2_image', 'IMAGE_PATH'),
('masonry_image_01', 'IMAGE_PATH'),
('masonry_image_02', 'IMAGE_PATH'),
('masonry_image_03', 'IMAGE_PATH'),
('masonry_image_04', 'IMAGE_PATH'),
('masonry_image_05', 'IMAGE_PATH'),
('masonry_image_06', 'IMAGE_PATH'),
('masonry_image_07', 'IMAGE_PATH'),
('masonry_image_08', 'IMAGE_PATH'),
('masonry_image_09', 'IMAGE_PATH'),
('masonry_image_10', 'IMAGE_PATH'),
('masonry_image_11', 'IMAGE_PATH'),
('masonry_image_12', 'IMAGE_PATH'),
('restaurant_1_image', 'IMAGE_PATH'),
('restaurant_5_image', 'IMAGE_PATH'),
('restaurant_6_image', 'IMAGE_PATH'),
('restaurant_7_image', 'IMAGE_PATH'),
('route_map_image', 'IMAGE_PATH'),
('significance_image', 'IMAGE_PATH'),
('track_1_image', 'IMAGE_PATH'),
('track_2_image', 'IMAGE_PATH'),
('track_3_image', 'IMAGE_PATH'),
('track_4_image', 'IMAGE_PATH'),
('hero_back_button_url', 'LINK'),
('hero_button_link', 'LINK'),
('hero_button_primary_link', 'LINK'),
('hero_button_secondary_link', 'LINK'),
('history_amsterdamsepoort_link', 'LINK'),
('history_grotemarkt_link', 'LINK'),
('history_group_ticket_icon', 'LINK'),
('history_molendeadriaan_link', 'LINK'),
('history_single_ticket_icon', 'LINK'),
('live_cta_schedule_button_url', 'LINK'),
('schedule_cta_button_link', 'LINK'),
('about_image', 'MEDIA'),
('artists_evolve_image', 'MEDIA'),
('artists_gumbokings_image', 'MEDIA'),
('artists_ntjam_image', 'MEDIA'),
('banner_background_image', 'MEDIA'),
('dance_image', 'MEDIA'),
('history_image', 'MEDIA'),
('jazz_gradient_background_image', 'MEDIA'),
('jazz_hero_background_image', 'MEDIA'),
('jazz_image', 'MEDIA'),
('jazz_intro_image', 'MEDIA'),
('restaurant_image', 'MEDIA'),
('storytelling_image', 'MEDIA'),
('about_tagline', 'TEXT'),
('album_1_description', 'TEXT'),
('album_1_tag', 'TEXT'),
('album_1_title', 'TEXT'),
('album_1_year', 'TEXT'),
('album_2_description', 'TEXT'),
('album_2_tag', 'TEXT'),
('album_2_title', 'TEXT'),
('album_2_year', 'TEXT'),
('album_3_description', 'TEXT'),
('album_3_tag', 'TEXT'),
('album_3_title', 'TEXT'),
('album_3_year', 'TEXT'),
('albums_description', 'TEXT'),
('albums_heading', 'TEXT'),
('architectural_significance_text', 'TEXT'),
('artists_evolve_description', 'TEXT'),
('artists_evolve_first_performance', 'TEXT'),
('artists_evolve_genre', 'TEXT'),
('artists_evolve_more_performances_text', 'TEXT'),
('artists_evolve_name', 'TEXT'),
('artists_evolve_performance_count', 'TEXT'),
('artists_gumbokings_description', 'TEXT'),
('artists_gumbokings_first_performance', 'TEXT'),
('artists_gumbokings_genre', 'TEXT'),
('artists_gumbokings_more_performances_text', 'TEXT'),
('artists_gumbokings_name', 'TEXT'),
('artists_gumbokings_performance_count', 'TEXT'),
('artists_ntjam_description', 'TEXT'),
('artists_ntjam_first_performance', 'TEXT'),
('artists_ntjam_genre', 'TEXT'),
('artists_ntjam_more_performances_text', 'TEXT'),
('artists_ntjam_name', 'TEXT'),
('artists_ntjam_performance_count', 'TEXT'),
('banner_subtitle', 'TEXT'),
('booking_cta_description', 'TEXT'),
('btn_explore_template', 'TEXT'),
('cards_subtitle', 'TEXT'),
('dance_description', 'TEXT'),
('detail_hero_subtitle_template', 'TEXT'),
('detail_label_address', 'TEXT'),
('detail_label_contact', 'TEXT'),
('detail_label_duration', 'TEXT'),
('detail_label_festival_rated', 'TEXT'),
('detail_label_michelin', 'TEXT'),
('detail_label_open_hours', 'TEXT'),
('detail_label_price_food', 'TEXT'),
('detail_label_rating', 'TEXT'),
('detail_label_seats', 'TEXT'),
('detail_label_special_requests', 'TEXT'),
('detail_location_address_label', 'TEXT'),
('detail_map_fallback_text', 'TEXT'),
('detail_menu_cuisine_label', 'TEXT'),
('detail_reservation_description', 'TEXT'),
('detail_reservation_note', 'TEXT'),
('detail_reservation_slots_label', 'TEXT'),
('events_subtitle', 'TEXT'),
('fact1', 'TEXT'),
('fact2', 'TEXT'),
('fact3', 'TEXT'),
('formed_text', 'TEXT'),
('gradient_subheading', 'TEXT'),
('hero_back_button_text', 'TEXT'),
('hero_reserve_button_text', 'TEXT'),
('hero_subtitle', 'TEXT'),
('highlight_1', 'TEXT'),
('highlight_1_description', 'TEXT'),
('highlight_1_title', 'TEXT'),
('highlight_2', 'TEXT'),
('highlight_2_description', 'TEXT'),
('highlight_2_title', 'TEXT'),
('highlight_3', 'TEXT'),
('highlight_3_description', 'TEXT'),
('highlight_3_title', 'TEXT'),
('highlight_4', 'TEXT'),
('highlight_5', 'TEXT'),
('highlight_6', 'TEXT'),
('highlights_heading', 'TEXT'),
('historical_significance_text', 'TEXT'),
('history_amsterdamsepoort_description', 'TEXT'),
('history_amsterdamsepoort_name', 'TEXT'),
('history_description', 'TEXT'),
('history_grotemarkt_description', 'TEXT'),
('history_grotemarkt_name', 'TEXT'),
('history_important_tour_info_heading', 'TEXT'),
('history_molendeadriaan_description', 'TEXT'),
('history_molendeadriaan_name', 'TEXT'),
('history_pricing_group_include1', 'TEXT'),
('history_pricing_group_include2', 'TEXT'),
('history_pricing_group_include3', 'TEXT'),
('history_pricing_group_price', 'TEXT'),
('history_pricing_group_title', 'TEXT'),
('history_pricing_single_include1', 'TEXT'),
('history_pricing_single_include2', 'TEXT'),
('history_pricing_single_include3', 'TEXT'),
('history_pricing_single_price', 'TEXT'),
('history_pricing_single_title', 'TEXT'),
('important_info_item1', 'TEXT'),
('important_info_item2', 'TEXT'),
('important_info_item3', 'TEXT'),
('important_info_item4', 'TEXT'),
('important_info_item5', 'TEXT'),
('important_info_item6', 'TEXT'),
('important_info_item7', 'TEXT'),
('important_info_item8', 'TEXT'),
('instructions_card_1_text', 'TEXT'),
('instructions_card_2_text', 'TEXT'),
('instructions_card_3_text', 'TEXT'),
('intro_body', 'TEXT'),
('intro_fact', 'TEXT'),
('intro_image_alt', 'TEXT'),
('intro_text', 'TEXT'),
('intro2_body', 'TEXT'),
('intro2_image_alt', 'TEXT'),
('jazz_description', 'TEXT'),
('label_events_count', 'TEXT'),
('label_no_events', 'TEXT'),
('lineup_1', 'TEXT'),
('lineup_2', 'TEXT'),
('lineup_3', 'TEXT'),
('lineup_4', 'TEXT'),
('lineup_5', 'TEXT'),
('lineup_6', 'TEXT'),
('lineup_heading', 'TEXT'),
('listen_description', 'TEXT'),
('listen_heading', 'TEXT'),
('listen_play_button_label', 'TEXT'),
('listen_play_excerpt_text', 'TEXT'),
('listen_subheading', 'TEXT'),
('listen_track_artwork_alt_suffix', 'TEXT'),
('live_cta_book_button_text', 'TEXT'),
('live_cta_description', 'TEXT'),
('live_cta_heading', 'TEXT'),
('live_cta_schedule_button_text', 'TEXT'),
('nav_dance', 'TEXT'),
('nav_history', 'TEXT'),
('nav_home', 'TEXT'),
('nav_jazz', 'TEXT'),
('nav_restaurant', 'TEXT'),
('nav_storytelling', 'TEXT'),
('origin_text', 'TEXT'),
('overview_body_primary', 'TEXT'),
('overview_body_secondary', 'TEXT'),
('overview_lead', 'TEXT'),
('performances_description', 'TEXT'),
('performances_heading', 'TEXT'),
('performances_section_id', 'TEXT'),
('performances_text', 'TEXT'),
('photo_gallery_description', 'TEXT'),
('photo_gallery_heading', 'TEXT'),
('pricing_3day_desc', 'TEXT'),
('pricing_3day_include1', 'TEXT'),
('pricing_3day_include2', 'TEXT'),
('pricing_3day_include3', 'TEXT'),
('pricing_3day_include4', 'TEXT'),
('pricing_3day_info', 'TEXT'),
('pricing_3day_price', 'TEXT'),
('pricing_3day_title', 'TEXT'),
('pricing_daypass_desc', 'TEXT'),
('pricing_daypass_include1', 'TEXT'),
('pricing_daypass_include2', 'TEXT'),
('pricing_daypass_include3', 'TEXT'),
('pricing_daypass_include4', 'TEXT'),
('pricing_daypass_info', 'TEXT'),
('pricing_daypass_price', 'TEXT'),
('pricing_daypass_title', 'TEXT'),
('pricing_description', 'TEXT'),
('pricing_individual_item1', 'TEXT'),
('pricing_individual_item2', 'TEXT'),
('pricing_individual_item3', 'TEXT'),
('pricing_individual_title', 'TEXT'),
('pricing_subheading', 'TEXT'),
('restaurant_1_address', 'TEXT'),
('restaurant_1_book_label', 'TEXT'),
('restaurant_1_cuisine', 'TEXT'),
('restaurant_1_description', 'TEXT'),
('restaurant_1_distance_text', 'TEXT'),
('restaurant_1_price', 'TEXT'),
('restaurant_1_rating', 'TEXT'),
('restaurant_2_address', 'TEXT'),
('restaurant_2_cuisine', 'TEXT'),
('restaurant_2_description', 'TEXT'),
('restaurant_2_distance_text', 'TEXT'),
('restaurant_2_name', 'TEXT'),
('restaurant_2_price', 'TEXT'),
('restaurant_2_rating', 'TEXT'),
('restaurant_3_address', 'TEXT'),
('restaurant_3_cuisine', 'TEXT'),
('restaurant_3_distance_text', 'TEXT'),
('restaurant_3_name', 'TEXT'),
('restaurant_3_price', 'TEXT'),
('restaurant_3_rating', 'TEXT'),
('restaurant_4_book_label', 'TEXT'),
('restaurant_4_cuisine', 'TEXT'),
('restaurant_4_description', 'TEXT'),
('restaurant_4_image', 'TEXT'),
('restaurant_4_price', 'TEXT'),
('restaurant_4_rating', 'TEXT'),
('restaurant_5_address', 'TEXT'),
('restaurant_5_cuisine', 'TEXT'),
('restaurant_5_description', 'TEXT'),
('restaurant_5_distance_text', 'TEXT'),
('restaurant_5_name', 'TEXT'),
('restaurant_5_price', 'TEXT'),
('restaurant_5_rating', 'TEXT'),
('restaurant_6_address', 'TEXT'),
('restaurant_6_cuisine', 'TEXT'),
('restaurant_6_description', 'TEXT'),
('restaurant_6_distance_text', 'TEXT'),
('restaurant_6_price', 'TEXT'),
('restaurant_6_rating', 'TEXT'),
('restaurant_7_address', 'TEXT'),
('restaurant_7_cuisine', 'TEXT'),
('restaurant_7_description', 'TEXT'),
('restaurant_7_distance_text', 'TEXT'),
('restaurant_7_price', 'TEXT'),
('restaurant_7_rating', 'TEXT'),
('restaurant_description', 'TEXT'),
('route_location1_description', 'TEXT'),
('route_location1_name', 'TEXT'),
('route_location2_description', 'TEXT'),
('route_location2_name', 'TEXT'),
('route_location3_description', 'TEXT'),
('route_location3_name', 'TEXT'),
('route_location4_description', 'TEXT'),
('route_location4_name', 'TEXT'),
('route_location5_description', 'TEXT'),
('route_location5_name', 'TEXT'),
('route_location6_description', 'TEXT'),
('route_location6_name', 'TEXT'),
('route_subheading', 'TEXT'),
('schedule_additional_info_heading', 'TEXT'),
('schedule_cta_description', 'TEXT'),
('schedule_filters_label', 'TEXT'),
('schedule_subtitle_1', 'TEXT'),
('schedule_subtitle_2', 'TEXT'),
('site_name', 'TEXT'),
('storytelling_description', 'TEXT'),
('ticket_options_subheading', 'TEXT'),
('track_1_album', 'TEXT'),
('track_1_description', 'TEXT'),
('track_1_duration', 'TEXT'),
('track_1_progress_class', 'TEXT'),
('track_1_title', 'TEXT'),
('track_2_album', 'TEXT'),
('track_2_description', 'TEXT'),
('track_2_duration', 'TEXT'),
('track_2_progress_class', 'TEXT'),
('track_2_title', 'TEXT'),
('track_3_album', 'TEXT'),
('track_3_description', 'TEXT'),
('track_3_duration', 'TEXT'),
('track_3_progress_class', 'TEXT'),
('track_3_title', 'TEXT'),
('track_4_album', 'TEXT'),
('track_4_description', 'TEXT'),
('track_4_duration', 'TEXT'),
('track_4_progress_class', 'TEXT'),
('track_4_title', 'TEXT'),
('venue_filter_label', 'TEXT'),
('venue_filter_title', 'TEXT'),
('venue_patronaat_address1', 'TEXT'),
('venue_patronaat_address2', 'TEXT'),
('venue_patronaat_contact', 'TEXT'),
('venue_patronaat_hall1_capacity', 'TEXT'),
('venue_patronaat_hall1_desc', 'TEXT'),
('venue_patronaat_hall1_name', 'TEXT'),
('venue_patronaat_hall2_capacity', 'TEXT'),
('venue_patronaat_hall2_desc', 'TEXT'),
('venue_patronaat_hall2_name', 'TEXT'),
('venue_patronaat_hall3_capacity', 'TEXT'),
('venue_patronaat_hall3_desc', 'TEXT'),
('venue_patronaat_hall3_name', 'TEXT'),
('venue_patronaat_name', 'TEXT'),
('venues_description', 'TEXT'),
('venues_subheading', 'TEXT'),
('video_url', 'TEXT');

-- Add FK constraint for CmsItemKey -> CmsItemType (skip if already exists)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME = 'FK_CmsItemKey_Type' AND TABLE_SCHEMA = DATABASE());
SET @sql = IF(@fk_exists = 0, 'ALTER TABLE `CmsItemKey` ADD CONSTRAINT `FK_CmsItemKey_Type` FOREIGN KEY (`ExpectedItemType`) REFERENCES `CmsItemType` (`ItemType`)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index from CmsItem.ItemKey -> CmsItemKey.ItemKey (skip if already exists)
SET @idx_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE INDEX_NAME = 'FK_CmsItem_ItemKey' AND TABLE_SCHEMA = DATABASE());
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE `CmsItem` ADD KEY `FK_CmsItem_ItemKey` (`ItemKey`)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure all existing CmsItem keys are in CmsItemKey before adding FK
INSERT IGNORE INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`)
SELECT DISTINCT ci.ItemKey, 'TEXT'
FROM `CmsItem` ci
LEFT JOIN `CmsItemKey` cik ON ci.ItemKey = cik.ItemKey
WHERE cik.ItemKey IS NULL;

SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME = 'FK_CmsItem_ItemKey' AND TABLE_SCHEMA = DATABASE());
SET @sql = IF(@fk_exists = 0, 'ALTER TABLE `CmsItem` ADD CONSTRAINT `FK_CmsItem_ItemKey` FOREIGN KEY (`ItemKey`) REFERENCES `CmsItemKey` (`ItemKey`)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- 4. New CmsPages for history detail pages
--    Using IDs 11, 12, 13 (our max CmsPageId was 10)
-- ============================================================================
INSERT IGNORE INTO `CmsPage` (`CmsPageId`, `Slug`, `Title`) VALUES
(11, 'grote-markt', 'Grote Markt'),
(12, 'amsterdamse-poort', 'Amsterdaamse Poort'),
(13, 'molen-de-adriaan', 'Molen De Adriaan');

-- ============================================================================
-- 5. New CmsSections for history detail pages
--    Using IDs 81-92 (our max CmsSectionId was 80)
-- ============================================================================
INSERT IGNORE INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
-- Grote Markt (page 11)
(81, 11, 'hero_section'),
(82, 11, 'intro_section'),
(83, 11, 'facts_section'),
(84, 11, 'significance_section'),
-- Amsterdamse Poort (page 12)
(85, 12, 'hero_section'),
(86, 12, 'intro_section'),
(87, 12, 'facts_section'),
(88, 12, 'significance_section'),
-- Molen De Adriaan (page 13)
(89, 13, 'hero_section'),
(90, 13, 'intro_section'),
(91, 13, 'facts_section'),
(92, 13, 'significance_section');

-- ============================================================================
-- 6. CmsItem data for history detail pages
--    All IDs start at 1266 (our max CmsItemId was 1265)
-- ============================================================================

-- --- Grote Markt: Hero Section (section 81) ---
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1266, 81, 'hero_main_title', 'HEADING', 'GROTE MARKT', NULL, NULL, '2026-03-16 15:09:14'),
(1267, 81, 'hero_subtitle', 'TEXT', 'A vibrant central square, where centuries of culture, trade, and community life come together', NULL, NULL, '2026-03-16 15:04:56'),
(1268, 81, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/History/GroteMarkt-hero.png', NULL, NULL, '2026-03-16 15:04:56'),
(1269, 81, 'hero_map_image', 'IMAGE_PATH', '/assets/Image/History/GroteMarkt-map.png', NULL, NULL, '2026-03-16 15:04:56'),
(1270, 81, 'hero_button', 'BUTTON_TEXT', 'Back to history', NULL, NULL, '2026-03-16 15:04:56'),
(1271, 81, 'hero_button_link', 'LINK', '/history', NULL, NULL, '2026-03-16 15:04:56');

-- --- Grote Markt: Intro Section (section 82) ---
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1272, 82, 'intro_heading', 'HEADING', 'Explore the heart of the historic center of Haarlem.', NULL, NULL, '2026-03-16 15:47:48'),
(1273, 82, 'intro_text', 'TEXT', 'Step into the beating heart of the center of Haarlem as you arrive at the iconic Grote Markt. Surrounded by centuries-old architecture, lively cafes, and landmarks that shaped the city''s identity, this vibrant square is where Haarlem''s past and present come together. Grote Markt is your gateway to the stories, craftsmanship, and cultural moments that have defined the city for generations. From grand celebrations to Saturday market life, every corner of this square holds a piece of Haarlem''s enduring spirit. Begin your journey here and discover how history continues to echo through its bustling streets.', NULL, NULL, '2026-03-16 15:47:48'),
(1274, 82, 'intro_fact', 'TEXT', 'For centuries, Grote Markt has been the stage for the most important celebrations and gatherings in Haarlem. From medieval fairs and market days to modern cultural festivals, the square consistently brings the city to life. Its enduring role as a public meeting point reflects the spirit and resilience of the community of the city.', NULL, NULL, '2026-03-16 15:47:48'),
(1275, 82, 'intro_image', 'IMAGE_PATH', '/assets/Image/History/GroteMarkt-intro.png', NULL, NULL, '2026-03-16 15:47:48');

-- --- Grote Markt: Facts Section (section 83) ---
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1276, 83, 'facts_heading', 'HEADING', 'Did you know that...', NULL, NULL, '2026-03-21 23:35:14'),
(1277, 83, 'fact1', 'TEXT', 'The Grote Markt has been Haarlem''s main marketplace since the Middle Ages.', NULL, NULL, '2026-03-16 15:49:46'),
(1278, 83, 'fact2', 'TEXT', 'The square hosts the annual Flower Parade finale, one of the region''s most beloved traditions.', NULL, NULL, '2026-03-16 15:49:46'),
(1279, 83, 'fact3', 'TEXT', 'Grote Markt once hosted lively weekly cheese markets, attracting traders from across the region and making Haarlem an important commercial hub.', NULL, NULL, '2026-03-16 15:49:46');

-- --- Grote Markt: Significance Section (section 84) ---
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1280, 84, 'architectural_significance_heading', 'HEADING', 'Architectural significance', NULL, NULL, '2026-03-16 15:55:51'),
(1281, 84, 'architectural_significance_text', 'TEXT', 'The Grote Markt is surrounded by some of Haarlem''s most defining architectural treasures, creating a visually striking and cohesive cityscape. The grandeur of St. Bavo Church contrasts beautifully with the refined Gothic elements of the City Hall. Historic merchant houses, each with their own distinct facades, illustrate centuries of urban development. Together, these buildings tell a story of Haarlem''s architectural evolution through time. The square remains a living example of harmonious civic design in the heart of the city.', NULL, NULL, '2026-03-16 15:55:51'),
(1282, 84, 'historical_significance_heading', 'HEADING', 'Historical significance', NULL, NULL, '2026-03-16 15:55:51'),
(1283, 84, 'historical_significance_text', 'TEXT', 'As Haarlem''s central square, the Grote Markt has witnessed pivotal moments in the city''s economic, political, and cultural history. It served as the primary marketplace where trade fueled Haarlem''s prosperity. The square was also a gathering place during major civic events, from public announcements to celebrations. Many surrounding landmarks played essential roles in shaping the city''s identity. Today, the Grote Markt stands as a testament to Haarlem''s enduring heritage and vibrant public life.', NULL, NULL, '2026-03-16 15:55:51'),
(1284, 84, 'significance_image', 'IMAGE_PATH', '/assets/Image/History/GroteMarkt-significance.png', NULL, NULL, '2026-03-16 15:55:51');

-- --- Amsterdamse Poort: Hero Section (section 85) ---
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1285, 85, 'hero_main_title', 'HEADING', 'AMSTERDAMSE POORT', NULL, NULL, '2026-03-16 15:08:32'),
(1286, 85, 'hero_subtitle', 'TEXT', 'An iconic medieval gateway of Haarlem, standing as a striking reminder of the city''s fortified past.', NULL, NULL, '2026-03-16 15:08:32'),
(1287, 85, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/History/AmsterdamsePoort-hero.png', NULL, NULL, '2026-03-16 15:08:32'),
(1288, 85, 'hero_map_image', 'IMAGE_PATH', '/assets/Image/History/AmsterdamsePoort-map.png', NULL, NULL, '2026-03-16 15:08:32'),
(1289, 85, 'hero_button', 'BUTTON_TEXT', 'Back to history', NULL, NULL, '2026-03-16 15:08:32'),
(1290, 85, 'hero_button_link', 'LINK', '/history', NULL, NULL, '2026-03-16 15:08:32');

-- --- Amsterdamse Poort: Intro Section (section 86) ---
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1291, 86, 'intro_heading', 'HEADING', 'Explore the last surviving city gate of Haarlem.', NULL, NULL, '2026-03-16 15:47:48'),
(1292, 86, 'intro_text', 'TEXT', 'Amsterdamse Poort marks the threshold between historic Haarlem and the ancient road that once connected the city to Amsterdam. As the only remaining city gate, it offers a rare glimpse into the defensive structures that once protected citizens of Haarlem from outside threats. Today, its towers, arches, and brickwork tell stories of trade, travel, and transformation throughout the centuries. During the tour, this landmark invites visitors to slow down, look closer, and imagine the many journeys that began and ended at its impressive walls. Let this gateway introduce you to a chapter of the history of Haarlem where architecture, strategy, and daily life intertwined.', NULL, NULL, '2026-03-16 15:47:48'),
(1293, 86, 'intro_fact', 'TEXT', 'In the late Middle Ages, Amsterdamse Poort played a crucial role during periods of conflict, serving as both a defensive stronghold and a primary checkpoint for goods entering the city. During times of heightened tension, the gate was heavily fortified, illustrating its strategic importance to Haarlem''s security. Its presence helped shape the flow of commerce and movement, making it central to the city''s resilience and growth.', NULL, NULL, '2026-03-16 15:47:48'),
(1294, 86, 'intro_image', 'IMAGE_PATH', '/assets/Image/History/AmsterdamsePoort-intro.png', NULL, NULL, '2026-03-16 15:47:48');

-- --- Amsterdamse Poort: Facts Section (section 87) ---
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1295, 87, 'facts_heading', 'HEADING', 'Did you know that...', NULL, NULL, '2026-03-21 23:35:14'),
(1296, 87, 'fact1', 'TEXT', 'The Amsterdamse Poort dates back to the 14th century and is the only surviving gate of Haarlem''s original twelve city gates.', NULL, NULL, '2026-03-16 15:51:25'),
(1297, 87, 'fact2', 'TEXT', 'Its distinctive mix of towers and pointed arches makes it one of the city''s most photographed historic structures.', NULL, NULL, '2026-03-16 15:51:25'),
(1298, 87, 'fact3', 'TEXT', 'The gateway once formed part of the main trade route between Haarlem and Amsterdam, influencing daily commerce for centuries.', NULL, NULL, '2026-03-16 15:51:25');

-- --- Amsterdamse Poort: Significance Section (section 88) ---
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1299, 88, 'architectural_significance_heading', 'HEADING', 'Architectural significance', NULL, NULL, '2026-03-16 15:59:30'),
(1300, 88, 'architectural_significance_text', 'TEXT', 'Amsterdamse Poort showcases classic medieval fortification design with its twin round towers, pointed archways, and robust brick construction. Its asymmetrical layout hints at centuries of renovations and adaptations to shifting defensive needs. Decorative stonework and brick patterns add visual richness while highlighting the craftsmanship of the era. The gate''s placement at a former moat crossing demonstrates the architectural balance between aesthetics and military function. Together, these features make Amsterdamse Poort an exceptional example of the medieval architectural heritage of Haarlem.', NULL, NULL, '2026-03-16 15:59:30'),
(1301, 88, 'historical_significance_heading', 'HEADING', 'Historical significance', NULL, NULL, '2026-03-16 15:59:30'),
(1302, 88, 'historical_significance_text', 'TEXT', 'Historically, Amsterdamse Poort served as a vital entry point for travelers, merchants, and goods arriving from Amsterdam. It played a central role in Haarlem''s defense system, helping guard the city during numerous conflicts. As trade flourished, the gate became an economic lifeline, shaping the growth of urban landscape in Haarlem. Over time, it transitioned from a military necessity to a cherished cultural monument. Today, it stands as a powerful symbol of Haarlem''s resilience and centuries-long connection to the wider region.', NULL, NULL, '2026-03-16 15:59:30'),
(1303, 88, 'significance_image', 'IMAGE_PATH', '/assets/Image/History/AmsterdamsePoort-significance.png', NULL, NULL, '2026-03-16 15:59:30');

-- --- Molen De Adriaan: Hero Section (section 89) ---
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1304, 89, 'hero_main_title', 'HEADING', 'MOLEN DE ADRIAAN', NULL, NULL, '2026-03-16 15:08:32'),
(1305, 89, 'hero_subtitle', 'TEXT', 'A striking riverside windmill that reflects rich industrial and cultural heritage of the city.', NULL, NULL, '2026-03-16 15:08:32'),
(1306, 89, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/History/MolenDeAdriaan-hero.png', NULL, NULL, '2026-03-16 15:08:32'),
(1307, 89, 'hero_map_image', 'IMAGE_PATH', '/assets/Image/History/MolenDeAdriaan-map.png', NULL, NULL, '2026-03-16 15:08:32'),
(1308, 89, 'hero_button', 'BUTTON_TEXT', 'Back to history', NULL, NULL, '2026-03-16 15:08:32'),
(1309, 89, 'hero_button_link', 'LINK', '/history', NULL, NULL, '2026-03-16 15:08:32');

-- --- Molen De Adriaan: Intro Section (section 90) ---
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1310, 90, 'intro_heading', 'HEADING', 'Explore one of the most iconic windmills of Haarlem.', NULL, NULL, '2026-03-16 15:47:48'),
(1311, 90, 'intro_text', 'TEXT', 'Standing proudly along the River Spaarne, Molen De Adriaan is one of the most recognizable landmarks of Haarlem. Its lofty silhouette has welcomed visitors for centuries, offering a glimpse into the city''s craftsmanship, innovation, and relationship with the wind-powered industries that shaped the region. Today, the mill is not only a historic monument but also a vibrant learning space where history comes alive through demonstrations and storytelling. During the tour, this beloved windmill invites you to discover how technology, trade, and daily life intertwined in Haarlem''s past. Let Molen De Adriaan show you how a simple force of nature powered an era of ingenuity.', NULL, NULL, '2026-03-16 15:47:48'),
(1312, 90, 'intro_fact', 'TEXT', 'After a devastating fire destroyed the original structure in 1932, the community of Haarlem rallied for decades to rebuild Molen De Adriaan. Their dedication culminated in its grand reopening in 2002, restoring the windmill to its former glory. This event marked not only the rebirth of a historic icon but also the strength of local pride and commitment to preserving heritage.', NULL, NULL, '2026-03-16 15:47:48'),
(1313, 90, 'intro_image', 'IMAGE_PATH', '/assets/Image/History/MolenDeAdriaan-intro.png', NULL, NULL, '2026-03-16 15:47:48');

-- --- Molen De Adriaan: Facts Section (section 91) ---
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1314, 91, 'facts_heading', 'HEADING', 'Did you know that...', NULL, NULL, '2026-03-21 23:35:14'),
(1315, 91, 'fact1', 'TEXT', 'Molen De Adriaan was originally built in 1779 and served as a mill for producing materials like cement, tobacco, and spices.', NULL, NULL, '2026-03-16 15:51:25'),
(1316, 91, 'fact2', 'TEXT', 'The mill''s elevated base was designed to capture stronger winds from above nearby buildings.', NULL, NULL, '2026-03-16 15:51:25'),
(1317, 91, 'fact3', 'TEXT', 'Visitors can climb inside to see the mill''s wooden gears and machinery in action during demonstrations.', NULL, NULL, '2026-03-16 15:51:25');

-- --- Molen De Adriaan: Significance Section (section 92) ---
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1318, 92, 'architectural_significance_heading', 'HEADING', 'Architectural significance', NULL, NULL, '2026-03-16 15:59:30'),
(1319, 92, 'architectural_significance_text', 'TEXT', 'Molen De Adriaan is a classic example of a Dutch tower mill, characterized by its tall brick base and elegant wooden superstructure. The elevated construction not only improves wind capture but also adds to its dramatic presence along the river. Its rotating cap, sails, and internal mechanisms demonstrate the engineering brilliance of 18th-century millwrights. The reconstruction in 2002 faithfully replicated traditional building techniques, blending historical accuracy with modern safety standards. Together, these architectural elements create a visually captivating landmark that embodies both beauty and functionality.', NULL, NULL, '2026-03-16 15:59:30'),
(1320, 92, 'historical_significance_heading', 'HEADING', 'Historical significance', NULL, NULL, '2026-03-16 15:59:30'),
(1321, 92, 'historical_significance_text', 'TEXT', 'Throughout its history, Molen De Adriaan played a crucial role in Haarlem''s industrial development by grinding various materials essential for trade and craftsmanship. It served as a testament to the economic importance of wind power in the Netherlands. The mill''s destruction and subsequent restoration reflect Haarlem''s deep respect for its cultural heritage. Over time, it has transformed from an industrial site into a symbol of community identity and historical continuity. Today, Molen De Adriaan continues to share its story with visitors from around the world, preserving the legacy of working past of the city.', NULL, NULL, '2026-03-16 15:59:30'),
(1322, 92, 'significance_image', 'IMAGE_PATH', '/assets/Image/History/MolenDeAdriaan-significance.png', NULL, NULL, '2026-03-16 15:59:30');

-- ============================================================================
-- 7. CmsItem links for historical_locations_section (section 38)
--    These link the location cards on the history page to the new detail pages
-- ============================================================================
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1323, 38, 'history_grotemarkt_link', 'LINK', '/history/grote-markt', NULL, NULL, '2026-03-20 18:43:56'),
(1324, 38, 'history_amsterdamsepoort_link', 'LINK', '/history/amsterdamse-poort', NULL, NULL, '2026-03-20 18:43:56'),
(1325, 38, 'history_molendeadriaan_link', 'LINK', '/history/molen-de-adriaan', NULL, NULL, '2026-03-20 18:43:56');

COMMIT;
