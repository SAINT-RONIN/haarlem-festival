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
('headliners_heading', 'HEADING');

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

-- 9. Activate Armin van Buuren so he appears on the Dance page
UPDATE Artist SET IsActive = 1 WHERE ArtistId = 25;
