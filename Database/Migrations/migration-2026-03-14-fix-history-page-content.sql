-- Fix history page CMS content using baseline from complete-database-history-10-03-2026.sql
-- Date: 2026-03-14

START TRANSACTION;

-- 1) Ensure all required history sections exist.
INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT p.CmsPageId, req.SectionKey
FROM CmsPage p
JOIN (
  SELECT 'hero_section' AS SectionKey
  UNION ALL SELECT 'gradient_section'
  UNION ALL SELECT 'intro_section'
  UNION ALL SELECT 'route_section'
  UNION ALL SELECT 'historical_locations_section'
  UNION ALL SELECT 'ticket_options_section'
  UNION ALL SELECT 'history_pricing_section'
  UNION ALL SELECT 'history_important_tour_info_section'
  UNION ALL SELECT 'schedule_section'
) req
WHERE p.Slug = CONVERT('history' USING utf8mb4) COLLATE utf8mb4_uca1400_ai_ci
  AND NOT EXISTS (
    SELECT 1
    FROM CmsSection s
    WHERE s.CmsPageId = p.CmsPageId
      AND s.SectionKey = CONVERT(req.SectionKey USING utf8mb4) COLLATE utf8mb4_uca1400_ai_ci
  );

-- 2) Stage canonical history CMS items from 2026-03-10 dump.
DROP TEMPORARY TABLE IF EXISTS tmp_history_fix_items;
CREATE TEMPORARY TABLE tmp_history_fix_items (
  SectionKey varchar(80) COLLATE utf8mb4_uca1400_ai_ci NOT NULL,
  ItemKey varchar(80) COLLATE utf8mb4_uca1400_ai_ci NOT NULL,
  ItemType varchar(20) NOT NULL,
  TextValue text NULL,
  HtmlValue text NULL,
  MediaAssetId int NULL,
  UpdatedAtUtc datetime NOT NULL,
  PRIMARY KEY (SectionKey, ItemKey)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO tmp_history_fix_items
  (SectionKey, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
VALUES
  ('intro_section', 'intro_body', 'TEXT', 'A Stroll Through History invites visitors to explore rich past of Haarlem on foot. Guided tour leads participants through historic streets and landmarks, including locations that played an important role in the city''s cultural, social, and architectural development. The walks are offered in Dutch, English, and Chinese and are suitable for a wide audience. The route has been carefully curated and prepared by local historians and guides to ensure an engaging, informative, and memorable experience. By combining historical facts with stories from the past, the event helps visitors better understand how Haarlem grew into the city it is today. Multiple time slots are available throughout the festival, with different ticket options to keep the event accessible for individuals and families. By joining A Stroll Through History, visitors not only discover Haarlem''s landmarks but also connect with the city through the people, places, and moments that shaped it.', NULL, NULL, '2026-02-16 17:53:02'),
  ('hero_section', 'hero_main_title', 'HEADING', 'A STROLL THROUGH HISTORY', NULL, NULL, '2026-02-16 12:19:16'),
  ('hero_section', 'hero_button_primary', 'BUTTON_TEXT', 'Explore the tour', NULL, NULL, '2026-02-16 12:19:16'),
  ('hero_section', 'hero_button_primary_link', 'LINK', '#route', NULL, NULL, '2026-03-09 23:06:39'),
  ('hero_section', 'hero_button_secondary', 'BUTTON_TEXT', 'Get tickets', NULL, NULL, '2026-02-16 17:54:19'),
  ('hero_section', 'hero_button_secondary_link', 'LINK', '#tickets', NULL, NULL, '2026-03-09 23:06:39'),
  ('gradient_section', 'gradient_heading', 'HEADING', 'Every street holds echoes of the past, shaped by the people who once walked there.', NULL, NULL, '2026-02-16 12:52:54'),
  ('gradient_section', 'gradient_subheading', 'TEXT', 'Where history comes alive through places, paths, and people.', NULL, NULL, '2026-02-16 12:52:54'),
  ('intro_section', 'intro_heading', 'HEADING', 'Experience the living history of Haarlem', NULL, NULL, '2026-02-16 13:01:35'),
  ('route_section', 'route_location1_name', 'TEXT', 'Church of St.Bavo', NULL, NULL, '2026-02-16 13:04:58'),
  ('route_section', 'route_location1_description', 'TEXT', 'A monumental Gothic church famed for its towering nave and historic Müller organ once played by Mozart.', NULL, NULL, '2026-02-16 13:04:58'),
  ('route_section', 'route_location2_name', 'TEXT', 'Grote Markt', NULL, NULL, '2026-02-16 13:06:02'),
  ('route_section', 'route_location2_description', 'TEXT', 'A vibrant central square surrounded by landmark buildings and lively cafés; the city''s cultural heart.', NULL, NULL, '2026-02-16 17:53:02'),
  ('route_section', 'route_location3_name', 'TEXT', 'De Hallen', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_location3_description', 'TEXT', 'A former meat hall turned into an art and photography museum space that hosts exhibitions as part of the Frans Hals Museum.', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_location4_name', 'TEXT', 'Proveniershof', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_location4_description', 'TEXT', 'A peaceful 18th-century hofje (courtyard community) offering a quiet oasis with historic almshouses.', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_location5_name', 'TEXT', 'Jopenkerk', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_location5_description', 'TEXT', 'A former church transformed into Haarlem''s iconic craft brewery and restaurant, blending tradition with modern beer culture.', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_location6_name', 'TEXT', 'Waalse Kerk', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_location6_description', 'TEXT', 'An intimate 17th-century Walloon church known for its serene atmosphere and historic interior.', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_location7_name', 'TEXT', 'Molen de Adriaan', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_location7_description', 'TEXT', 'A reconstructed 18th-century riverside windmill offering tours and panoramic views over the Spaarne.', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_location8_name', 'TEXT', 'Amsterdamse Poort', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_location8_description', 'TEXT', 'Haarlem''s last surviving medieval city gate, showcasing impressive brickwork and centuries of history.', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_location9_name', 'TEXT', 'Hof van Bakenes', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_location9_description', 'TEXT', 'The oldest hofje in the Netherlands, featuring charming gardens and classic courtyard architecture dating back to 1395.', NULL, NULL, '2026-02-16 13:16:21'),
  ('route_section', 'route_heading', 'HEADING', 'The Route', NULL, NULL, '2026-02-16 13:18:19'),
  ('route_section', 'route_map_image', 'IMAGE_PATH', '/assets/Image/History/History-RouteMap.png', NULL, NULL, '2026-03-09 23:08:47'),
  ('historical_locations_section', 'history_grotemarkt_name', 'TEXT', 'Grote Markt', NULL, NULL, '2026-02-16 13:21:46'),
  ('historical_locations_section', 'history_grotemarkt_description', 'TEXT', 'The heart of the historic center of Haarlem.', NULL, NULL, '2026-02-16 13:21:46'),
  ('historical_locations_section', 'history_grotemarkt_image', 'IMAGE_PATH', '/assets/Image/History/History-GroteMarkt.png', NULL, NULL, '2026-03-09 23:08:47'),
  ('historical_locations_section', 'history_amsterdamsepoort_name', 'TEXT', 'Amsterdamse Poort', NULL, NULL, '2026-02-16 13:31:17'),
  ('historical_locations_section', 'history_amsterdamsepoort_description', 'TEXT', 'As the only remaining city gate.', NULL, NULL, '2026-02-16 13:31:17'),
  ('historical_locations_section', 'history_amsterdamsepoort_image', 'IMAGE_PATH', '/assets/Image/History/History-AmsterdamsePoort.png', NULL, NULL, '2026-03-09 23:08:47'),
  ('historical_locations_section', 'history_molendeadriaan_name', 'TEXT', 'Molen De Adriaan', NULL, NULL, '2026-02-16 13:31:17'),
  ('historical_locations_section', 'history_molendeadriaan_description', 'TEXT', 'A striking riverside windmill.', NULL, NULL, '2026-02-16 13:31:17'),
  ('historical_locations_section', 'history_molendeadriaan_image', 'IMAGE_PATH', '/assets/Image/History/History-MolenDeAdriaan.png', NULL, NULL, '2026-03-09 23:08:47'),
  ('historical_locations_section', 'historical_locations_heading', 'HEADING', 'Read more about these locations', NULL, NULL, '2026-02-16 13:36:54'),
  ('ticket_options_section', 'ticket_options_heading', 'HEADING', 'Your ticket options to join the experience', NULL, NULL, '2026-02-16 13:38:08'),
  ('ticket_options_section', 'history_single_ticket_icon', 'URL', '/assets/Icons/History/single-ticket-icon.svg', NULL, NULL, '2026-02-16 13:49:05'),
  ('ticket_options_section', 'history_pricing_single_title', 'TEXT', 'Single Ticket', NULL, NULL, '2026-02-16 13:49:05'),
  ('history_pricing_section', 'history_pricing_single_price', 'TEXT', '€17.50', NULL, NULL, '2026-02-16 13:49:05'),
  ('history_pricing_section', 'history_pricing_single_include1', 'TEXT', 'Per person', NULL, NULL, '2026-02-16 13:49:05'),
  ('history_pricing_section', 'history_pricing_single_include2', 'TEXT', 'Includes one complimentary drink', NULL, NULL, '2026-02-16 13:49:05'),
  ('history_pricing_section', 'history_pricing_single_include3', 'TEXT', '2.5 hour guided tour', NULL, NULL, '2026-02-16 13:49:05'),
  ('ticket_options_section', 'history_group_ticket_icon', 'URL', '/assets/Icons/History/group-ticket-icon.svg', NULL, NULL, '2026-02-16 13:49:05'),
  ('ticket_options_section', 'history_pricing_group_title', 'TEXT', 'Group Ticket', NULL, NULL, '2026-02-16 13:49:05'),
  ('history_pricing_section', 'history_pricing_group_price', 'TEXT', '€60.00', NULL, NULL, '2026-02-16 13:49:05'),
  ('history_pricing_section', 'history_pricing_group_include1', 'TEXT', 'For up to 4 people', NULL, NULL, '2026-02-16 13:49:05'),
  ('history_pricing_section', 'history_pricing_group_include2', 'TEXT', 'Includes four complimentary drinks', NULL, NULL, '2026-02-16 13:49:05'),
  ('history_pricing_section', 'history_pricing_group_include3', 'TEXT', 'Best value for families!', NULL, NULL, '2026-02-16 13:49:05'),
  ('history_important_tour_info_section', 'history_important_tour_info_heading', 'TEXT', 'Important information about the tour', NULL, NULL, '2026-02-16 13:45:24'),
  ('history_important_tour_info_section', 'important_info_item1', 'TEXT', 'Minimum age requirement: 12 years old', NULL, NULL, '2026-02-16 13:45:24'),
  ('history_important_tour_info_section', 'important_info_item2', 'TEXT', 'No strollers allowed due to the nature of the walking route', NULL, NULL, '2026-02-16 13:45:24'),
  ('history_important_tour_info_section', 'important_info_item3', 'TEXT', 'Tour duration: Approximately 2.5 hours including 15-minute break', NULL, NULL, '2026-02-16 13:45:24'),
  ('history_important_tour_info_section', 'important_info_item4', 'TEXT', 'Group ticket is the best value for a group of 4 or for a family', NULL, NULL, '2026-02-16 13:45:24'),
  ('history_important_tour_info_section', 'important_info_item5', 'TEXT', 'Starting point: Look for the giant flag near Church of St. Bavo at Grote Markt', NULL, NULL, '2026-02-16 13:45:24'),
  ('history_important_tour_info_section', 'important_info_item6', 'TEXT', 'Group size: Maximum 12 participants per guide', NULL, NULL, '2026-02-16 13:45:24'),
  ('history_important_tour_info_section', 'important_info_item7', 'TEXT', 'Comfortable walking shoes recommended', NULL, NULL, '2026-02-16 13:45:24'),
  ('history_important_tour_info_section', 'important_info_item8', 'TEXT', 'Tours run in light rain; severe weather cancellations will be communicated via email', NULL, NULL, '2026-02-16 13:45:24'),
  ('schedule_section', 'schedule_title', 'HEADING', 'Tour Schedule', NULL, NULL, '2026-02-18 14:37:27'),
  ('schedule_section', 'schedule_filters_button_text', 'BUTTON_TEXT', 'Filters', NULL, NULL, '2026-02-18 14:37:27'),
  ('schedule_section', 'schedule_show_filters', 'TEXT', '1', NULL, NULL, '2026-02-18 14:37:27'),
  ('schedule_section', 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-02-18 14:37:27'),
  ('schedule_section', 'schedule_currency_symbol', 'TEXT', '€', NULL, NULL, '2026-02-18 14:37:27'),
  ('schedule_section', 'schedule_no_events_text', 'TEXT', 'No events scheduled', NULL, NULL, '2026-02-18 14:37:27'),
  ('schedule_section', 'schedule_start_point', 'TEXT', 'A giant flag near Church of St. Bavo at Grote Markt', NULL, NULL, '2026-03-09 20:41:13');

-- 3) Upsert by (history page slug + section key + item key).
INSERT INTO CmsItem
  (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT
  s.CmsSectionId,
  t.ItemKey,
  t.ItemType,
  t.TextValue,
  t.HtmlValue,
  t.MediaAssetId,
  t.UpdatedAtUtc
FROM tmp_history_fix_items t
JOIN CmsPage p
  ON p.Slug = CONVERT('history' USING utf8mb4) COLLATE utf8mb4_uca1400_ai_ci
JOIN CmsSection s
  ON s.CmsPageId = p.CmsPageId
 AND s.SectionKey = t.SectionKey COLLATE utf8mb4_uca1400_ai_ci
ON DUPLICATE KEY UPDATE
  ItemType = VALUES(ItemType),
  TextValue = VALUES(TextValue),
  HtmlValue = VALUES(HtmlValue),
  MediaAssetId = VALUES(MediaAssetId),
  UpdatedAtUtc = VALUES(UpdatedAtUtc);

DROP TEMPORARY TABLE IF EXISTS tmp_history_fix_items;

COMMIT;
