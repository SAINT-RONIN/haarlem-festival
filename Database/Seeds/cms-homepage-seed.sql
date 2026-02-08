-- =====================================================
-- CMS Homepage Content Seed (English)
-- =====================================================
-- Description: Inserts all text content, headings, and buttons for the homepage
-- Language: English (EN)
-- Page: Home
-- Date: February 6, 2026
-- Note: Images are NOT included - only text content
-- =====================================================

-- Step 1: Create the Homepage CmsPage
INSERT INTO `CmsPage` (`CmsPageId`, `Slug`, `Title`) VALUES
(1, 'home', 'Haarlem Festival Homepage');

-- =====================================================
-- HERO SECTION
-- =====================================================

-- Step 2: Create Hero Section
INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(1, 1, 'hero_section');

-- Hero Section Content Items
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1, 1, 'hero_main_title', 'HEADING', 'Haarlem Festivals', NULL, NULL, NOW()),
(2, 1, 'hero_subtitle', 'TEXT', 'Four July Days Bringing People Together', NULL, NULL, NOW()),
(3, 1, 'hero_button_primary', 'BUTTON_TEXT', 'Discover all types of events', NULL, NULL, NOW()),
(4, 1, 'hero_button_secondary', 'BUTTON_TEXT', 'Events schedule', NULL, NULL, NOW());

-- =====================================================
-- BANNER SECTION (Explore all upcoming events)
-- =====================================================

-- Step 3: Create Banner Section
INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(2, 1, 'banner_section');

-- Banner Section Content Items
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(5, 2, 'banner_main_title', 'HEADING', 'Explore all upcoming events', NULL, NULL, NOW()),
(6, 2, 'banner_subtitle', 'TEXT', 'See every theme and activity happening during the festival weekend.', NULL, NULL, NOW());

-- =====================================================
-- ABOUT SECTION (What is Haarlem Festival?)
-- =====================================================

-- Step 4: Create About Section
INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(3, 1, 'about_section');

-- About Section Content Items
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(7, 3, 'about_main_title', 'HEADING', 'What is Haarlem Festival?', NULL, NULL, NOW()),
(8, 3, 'about_tagline', 'TEXT', 'A celebration of culture and creativity', NULL, NULL, NOW()),
(9, 3, 'about_description', 'HTML', NULL, '<p>Haarlem Festival is a four-day celebration that brings together the city''s most inspiring music, stories, food, and cultural experiences. During the last weekend of July, Haarlem transforms into a lively meeting place where residents and visitors explore jazz performances, storytelling sessions, gourmet specials, historic routes, children''s adventures at Teylers, and energetic DJ sets.</p><p>The festival highlights the diversity and creativity of Haarlem, offering something for every age group and interest. Whether you want to discover new artists, enjoy local flavors, learn about the city''s history, or simply experience Haarlem in a new way, the festival invites you to take part and enjoy the atmosphere.</p>', NULL, NOW()),
(10, 3, 'about_button', 'BUTTON_TEXT', 'Events schedule', NULL, NULL, NOW());

-- =====================================================
-- EVENTS OVERVIEW SECTION HEADER
-- =====================================================

-- Step 5: Create Events Overview Header Section
INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(4, 1, 'events_overview_header');

-- Events Overview Header Content Items
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(11, 4, 'events_main_title', 'HEADING', 'Explore Our Events', NULL, NULL, NOW()),
(12, 4, 'events_subtitle', 'TEXT', 'Discover what''s happening each day', NULL, NULL, NOW());

-- =====================================================
-- JAZZ EVENT SECTION
-- =====================================================

-- Step 6: Create Jazz Event Section
INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(5, 1, 'event_jazz');

-- Jazz Event Content Items
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(13, 5, 'jazz_title', 'HEADING', 'Jazz', NULL, NULL, NOW()),
(14, 5, 'jazz_description', 'TEXT', 'Experience world-class jazz performances at Haarlem''s most iconic venues, where every note feels close, alive, and full of character. From intimate late night sessions to grand concerts featuring internationally acclaimed artists, the festival offers something for every jazz lover, whether you are discovering the genre or already know what you like. Expect smooth melodies, bold improvisation, and those rare moments when the whole room holds its breath together.', NULL, NULL, NOW()),
(15, 5, 'jazz_button', 'BUTTON_TEXT', 'Explore Jazz Events', NULL, NULL, NOW());

-- =====================================================
-- DANCE EVENT SECTION
-- =====================================================

-- Step 7: Create Dance Event Section
INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(6, 1, 'event_dance');

-- Dance Event Content Items
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(16, 6, 'dance_title', 'HEADING', 'Dance', NULL, NULL, NOW()),
(17, 6, 'dance_description', 'TEXT', 'Feel the rhythm at our dance events with electronic beats, live DJs, and vibrant club nights across multiple venues throughout the city. From pulsing dance floors to immersive light and sound, each night is designed to build energy, spark connection, and keep you moving. Come with friends or show up solo, you will be surrounded by people who are there for the same reason: great music and a real night out. Lose yourself in the vibe, discover new DJs, and celebrate Haarlem until the early hours.', NULL, NULL, NOW()),
(18, 6, 'dance_button', 'BUTTON_TEXT', 'Explore Dance Events', NULL, NULL, NOW());

-- =====================================================
-- HISTORY EVENT SECTION
-- =====================================================

-- Step 8: Create History Event Section
INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(7, 1, 'event_history');

-- History Event Content Items
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(19, 7, 'history_title', 'HEADING', 'History', NULL, NULL, NOW()),
(20, 7, 'history_description', 'TEXT', 'Discover Haarlem''s rich heritage through guided walking tours, museum visits, and historical storytelling that brings the past to life. Walk in the footsteps of centuries as expert guides reveal hidden details, iconic landmarks, and the surprising moments that shaped this remarkable city. Whether you are new to Haarlem or think you already know it, you will see familiar streets with fresh eyes and leave with stories worth sharing.', NULL, NULL, NOW()),
(21, 7, 'history_button', 'BUTTON_TEXT', 'Explore History Events', NULL, NULL, NOW());

-- =====================================================
-- RESTAURANT EVENT SECTION
-- =====================================================

-- Step 9: Create Restaurant Event Section
INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(8, 1, 'event_restaurant');

-- Restaurant Event Content Items
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(22, 8, 'restaurant_title', 'HEADING', 'Restaurants', NULL, NULL, NOW()),
(23, 8, 'restaurant_description', 'TEXT', 'Savor exceptional cuisine at Haarlem''s finest restaurants and discover why the city is becoming a true food destination. Enjoy exclusive dining experiences, special chef collaborations, and hands-on culinary workshops designed for both curious beginners and passionate food lovers. From locally sourced ingredients to bold international inspiration, every dish is crafted to surprise and delight. Bring your appetite, treat yourself, and join fellow food lovers for unforgettable flavors, great company, and a festival experience you will be talking about long after the last bite.', NULL, NULL, NOW()),
(24, 8, 'restaurant_button', 'BUTTON_TEXT', 'Explore Restaurant Events', NULL, NULL, NOW());

-- =====================================================
-- STORYTELLING EVENT SECTION
-- =====================================================

-- Step 10: Create Storytelling Event Section
INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(9, 1, 'event_storytelling');

-- Storytelling Event Content Items
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(25, 9, 'storytelling_title', 'HEADING', 'Storytelling', NULL, NULL, NOW()),
(26, 9, 'storytelling_description', 'TEXT', 'Immerse yourself in captivating narratives from local storytellers as they bring Haarlem''s culture, legends, and present day life to vivid reality. Step into intimate festival settings where the art of oral tradition comes alive, and every story feels personal, surprising, and unforgettable. Come with friends or meet new people as you listen, laugh, and discover Haarlem through voices that know it best.', NULL, NULL, NOW()),
(27, 9, 'storytelling_button', 'BUTTON_TEXT', 'Explore Storytelling Events', NULL, NULL, NOW());

-- =====================================================
-- VENUE MAP SECTION
-- =====================================================

-- Step 11: Create Venue Map Section
INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(10, 1, 'venue_map_section');

-- Venue Map Section Content Items
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(28, 10, 'venue_main_title', 'HEADING', 'Overview of event locations', NULL, NULL, NOW()),
(29, 10, 'venue_filter_label', 'TEXT', 'Showing all location', NULL, NULL, NOW()),
(30, 10, 'venue_filter_title', 'TEXT', 'Filter event:', NULL, NULL, NOW()),
(31, 10, 'venue_filter_all', 'BUTTON_TEXT', 'All', NULL, NULL, NOW()),
(32, 10, 'venue_filter_jazz', 'BUTTON_TEXT', 'Jazz', NULL, NULL, NOW()),
(33, 10, 'venue_filter_dance', 'BUTTON_TEXT', 'Dance', NULL, NULL, NOW()),
(34, 10, 'venue_filter_history', 'BUTTON_TEXT', 'History', NULL, NULL, NOW()),
(35, 10, 'venue_filter_restaurants', 'BUTTON_TEXT', 'Restaurants', NULL, NULL, NOW()),
(36, 10, 'venue_filter_stories', 'BUTTON_TEXT', 'Stories', NULL, NULL, NOW());

-- =====================================================
-- SCHEDULE SECTION
-- =====================================================
-- NOTE: Schedule days and events are NOT stored in CMS.
-- They should be rendered dynamically from the database tables:
--   - ScheduleDay (stores festival dates)
--   - Event (stores event details)
--   - EventSession (stores session times)
--   - EventType (Jazz, Dance, History, Food, Storytelling)
--
-- The CMS only stores the section header texts.
-- =====================================================

-- Step 12: Create Schedule Section Header
INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(11, 1, 'schedule_section');

-- Schedule Section Header Content Items
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(37, 11, 'schedule_main_title', 'HEADING', 'Events schedule', NULL, NULL, NOW()),
(38, 11, 'schedule_subtitle_1', 'TEXT', 'Discover what''s happening each day', NULL, NULL, NOW()),
(39, 11, 'schedule_subtitle_2', 'TEXT', 'We invite you to join us for music, stories, tour, and great food.', NULL, NULL, NOW());


-- =====================================================
-- SUMMARY
-- =====================================================
-- Total CmsPages: 1
-- Total CmsSections: 11
-- Total CmsItems: 39
-- 
-- Structure:
-- - Hero Section (4 items)
-- - Banner Section (2 items)
-- - About Section (4 items)
-- - Events Overview Header (2 items)
-- - Jazz Event Section (3 items)
-- - Dance Event Section (3 items)
-- - History Event Section (3 items)
-- - Restaurant Event Section (3 items)
-- - Storytelling Event Section (3 items)
-- - Venue Map Section (9 items)
-- - Schedule Section Header (3 items)
-- 
-- =====================================================
-- IMPORTANT NOTES
-- =====================================================
-- 1. Navbar and Footer are NOT included - they will be hardcoded in partials
-- 
-- 2. Schedule Days and Events are NOT stored in CMS
--    They are rendered dynamically from these database tables:
--    - ScheduleDay (stores festival dates by EventType)
--    - Event (stores event details)
--    - EventSession (stores session start/end times)
--    - EventType (Jazz=1, Dance=2, History=3, Storytelling=4, Food=5)
-- 
-- 3. Admin can add new schedule days/events by inserting into:
--    - ScheduleDay table (to add a new festival day)
--    - Event table (to add a new event)
--    - EventSession table (to add session times for that event)
-- 
-- 4. The homepage will dynamically query and display all schedule data
--    No CMS editing needed for schedule changes
-- =====================================================

