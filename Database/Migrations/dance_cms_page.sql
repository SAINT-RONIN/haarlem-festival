-- ============================================================
-- Dance page CMS content migration
-- Creates the CmsPage, its sections, and seed CMS items.
-- Run once against the haarlem_festival database.
-- ============================================================

-- 1. Dance CmsPage
INSERT INTO CmsPage (CmsPageId, Slug, Title) VALUES
(4, 'dance', 'Haarlem Dance Festival')
ON DUPLICATE KEY UPDATE Slug = VALUES(Slug), Title = VALUES(Title);

-- 2. CmsSections for the dance page
INSERT INTO CmsSection (CmsPageId, SectionKey) VALUES
(4, 'hero_section'),
(4, 'gradient_section'),
(4, 'intro_section'),
(4, 'headliners_section'),
(4, 'artists_section'),
(4, 'schedule_section')
ON DUPLICATE KEY UPDATE SectionKey = VALUES(SectionKey);

-- 3. Register new CmsItemKeys that don't exist yet
INSERT IGNORE INTO CmsItemKey (ItemKey, ExpectedItemType) VALUES
('headliners_heading', 'HEADING'),
('intro_label', 'TEXT');

-- 4. Hero section items
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'hero_main_title', 'HEADING', 'DANCE! FESTIVAL 2025', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'hero_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'hero_subtitle', 'TEXT', 'Haarlem • 3 Days • Music • Culture', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'hero_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'hero_button_primary', 'BUTTON_TEXT', 'Discover all events', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'hero_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'hero_button_primary_link', 'LINK', '#dance-schedule', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'hero_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'hero_button_secondary', 'BUTTON_TEXT', 'What is Haarlem DANCE! Festival?', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'hero_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/Image (Dance).png', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'hero_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

-- 5. Gradient section items
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'gradient_heading', 'HEADING', 'Every rhythm brings people together beyond the music', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'gradient_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'gradient_subheading', 'TEXT', 'Experience dance, culture, and connection at DANCE! Festival.', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'gradient_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'gradient_background_image', 'IMAGE_PATH', '/assets/Image/Image (Dance).png', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'gradient_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

-- 6. Intro section items
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'intro_heading', 'HEADING', 'ENJOY THE HOTTEST TIME THIS SUMMER', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'intro_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'intro_body', 'TEXT',
'This summer, Haarlem becomes the heart of music, dance, and unforgettable energy. Our festival brings people together to celebrate movement, culture, and sound in one powerful experience. Whether you come for the beats, the atmosphere, or the memories, this is where your summer truly begins.

Expect high-energy performances, vibrant crowds, and an atmosphere filled with freedom, rhythm, and connection. Haarlem Festival Dance is not just an event; it''s a feeling you''ll carry with you long after the music stops.',
NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'intro_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'intro_image', 'IMAGE_PATH', '/assets/Image/Image (Dance).png', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'intro_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'intro_label', 'TEXT', 'ABOUT HAARLEM FESTIVAL', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'intro_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

-- 7. Headliners section items
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'headliners_heading', 'HEADING', 'HEADLINERS', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'headliners_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

-- 8. Artists (supporting) section items
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'artists_heading', 'HEADING', 'Supporting Artists', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'artists_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

-- 9. Schedule section items
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_title', 'TEXT', 'Dance schedule', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_year', 'TEXT', '2026', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_filters_button_text', 'BUTTON_TEXT', 'Filters', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_show_filters', 'TEXT', '1', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_currency_symbol', 'TEXT', '€', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_no_events_text', 'TEXT', 'No events scheduled', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_filter_reset_text', 'TEXT', 'Reset all filters', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_event_count_label', 'TEXT', 'Events', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_show_event_count', 'TEXT', '1', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_filter_all_label', 'TEXT', 'All', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_filter_day_label', 'TEXT', 'Day', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_filter_price_type_label', 'TEXT', 'Price Type', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_filter_free_label', 'TEXT', 'Free', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, MediaAssetId)
SELECT s.CmsSectionId, 'schedule_filter_paid_label', 'TEXT', 'Paid', NULL
FROM CmsSection s WHERE s.CmsPageId = 4 AND s.SectionKey = 'schedule_section'
ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue);

-- 10. Activate Armin van Buuren so he appears on the Dance page
UPDATE Artist SET IsActive = 1 WHERE ArtistId = 25;

-- 10b. Create Afrojack artist if not exists, link to his solo event, set sort orders
INSERT INTO Artist (Name, Style, CardSortOrder, ShowOnJazzOverview, IsActive)
SELECT 'Afrojack', 'Dance/EDM', 5, 0, 1
WHERE NOT EXISTS (SELECT 1 FROM Artist WHERE Name = 'Afrojack');

UPDATE Event SET ArtistId = (SELECT ArtistId FROM Artist WHERE Name = 'Afrojack' LIMIT 1), IsActive = 1
WHERE EventId = 26;

-- Set CardSortOrder so headliners = Hardwell(1), Armin van Buuren(2); supporting = Tiësto(3), Martin Garrix(4), Afrojack(5), Nicky Romero(6)
UPDATE Artist SET CardSortOrder = 1 WHERE ArtistId = 23;
UPDATE Artist SET CardSortOrder = 2 WHERE ArtistId = 25;
UPDATE Artist SET CardSortOrder = 3 WHERE ArtistId = 22;
UPDATE Artist SET CardSortOrder = 4 WHERE ArtistId = 24;
UPDATE Artist SET CardSortOrder = 5 WHERE Name = 'Afrojack';
UPDATE Artist SET CardSortOrder = 6 WHERE ArtistId = 20;

-- 11. Fix session 110 (Armin van Buuren Club) and add Monday Jul 27 sessions for a 4th day
UPDATE EventSession
SET StartDateTime = '2026-07-27 20:00:00', EndDateTime = '2026-07-27 21:30:00', VenueId = 5, IsActive = 1
WHERE EventSessionId = 110;

-- Tiësto Club on Monday Jul 27
INSERT INTO EventSession (EventId, VenueId, StartDateTime, EndDateTime, CapacityTotal, CapacitySingleTicketLimit, IsActive)
SELECT 21, 4, '2026-07-27 22:00:00', '2026-07-27 23:30:00', 500, 4, 1
WHERE NOT EXISTS (SELECT 1 FROM EventSession WHERE EventId = 21 AND DATE(StartDateTime) = '2026-07-27');

-- Hardwell Club on Monday Jul 27
INSERT INTO EventSession (EventId, VenueId, StartDateTime, EndDateTime, CapacityTotal, CapacitySingleTicketLimit, IsActive)
SELECT 22, 6, '2026-07-27 23:00:00', '2026-07-28 00:30:00', 400, 4, 1
WHERE NOT EXISTS (SELECT 1 FROM EventSession WHERE EventId = 22 AND DATE(StartDateTime) = '2026-07-27');

-- Prices for Monday sessions
INSERT IGNORE INTO EventSessionPrice (EventSessionId, PriceTierId, Price)
SELECT es.EventSessionId, 1, 60.00
FROM EventSession es
WHERE es.EventId IN (21, 22) AND DATE(es.StartDateTime) = '2026-07-27'
  AND NOT EXISTS (SELECT 1 FROM EventSessionPrice WHERE EventSessionId = es.EventSessionId AND PriceTierId = 1);

-- 12. Labels for Dance event sessions (derived from event title convention)
INSERT IGNORE INTO EventSessionLabel (EventSessionId, LabelText) VALUES
(25,  'Back2Back'),
(26,  'Club'),
(27,  'Club'),
(28,  'Club'),
(29,  'Club'),
(30,  'Back2Back'),
(31,  'Club'),
(32,  'TiëstoWorld'),
(33,  'Club'),
(34,  'Back2Back'),
(35,  'Club'),
(36,  'Club'),
(37,  'Club'),
(110, 'Club');

-- Labels for the new Monday Jul 27 sessions
INSERT IGNORE INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Club'
FROM EventSession es
WHERE es.EventId IN (21, 22) AND DATE(es.StartDateTime) = '2026-07-27';
