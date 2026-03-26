-- migration-v32-storytelling-detail-cms.sql
--
-- Imports the CMS data for the storytelling-detail page (CmsPageId = 7).
-- Covers 13 event sections (event_34 through event_46):
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
-- Each section contains: about_heading, about_body, about_image_1, about_image_2,
-- highlight_1_*, highlight_2_*, highlight_3_*, gallery_image_1-5, video_url.
--
-- Safe to run multiple times: all statements use INSERT IGNORE.
-- CmsItem rows omit CmsItemId so the database auto-generates new IDs.
-- The UNIQUE constraint on (CmsSectionId, ItemKey) prevents duplicate inserts.
-- ---------------------------------------------------------------

-- CmsPage
INSERT IGNORE INTO `CmsPage` (`CmsPageId`, `Slug`, `Title`) VALUES
(7, 'storytelling-detail', 'Storytelling Detail Pages');

-- CmsSection
INSERT IGNORE INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(61, 7, 'event_34'),
(62, 7, 'event_35'),
(63, 7, 'event_36'),
(64, 7, 'event_37'),
(65, 7, 'event_38'),
(66, 7, 'event_39'),
(67, 7, 'event_40'),
(68, 7, 'event_41'),
(69, 7, 'event_42'),
(70, 7, 'event_43'),
(71, 7, 'event_44'),
(72, 7, 'event_45'),
(73, 7, 'event_46');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 61 — event_34: Winnie de Poeh
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(61, 'about_heading', 'HEADING', 'Winnie de Poeh (4+)', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'about_body', 'HTML', NULL, '<p>An enchanting storytelling experience for children aged 4 and above, bringing the beloved story of Winnie the Pooh to life in the heart of Haarlem.</p>', NULL, '2026-03-09 15:46:45'),
(61, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/WinnieThePoohHeader.png', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'highlight_1_title', 'TEXT', 'A Story for All Ages', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'highlight_1_description', 'TEXT', 'A classic tale reimagined for young and old alike.', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, '2026-03-14 16:25:32'),
(61, 'highlight_2_title', 'TEXT', 'Live Storytelling', NULL, NULL, '2026-03-14 16:25:32'),
(61, 'highlight_2_description', 'TEXT', 'Experience the magic of oral storytelling in an intimate setting.', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'highlight_3_title', 'TEXT', 'Interactive & Fun', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'highlight_3_description', 'TEXT', 'Children are invited to participate and use their imagination.', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/pig.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/WinnieThePoohHeader.png', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/pig.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(61, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:46:45');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 62 — event_35: Omdenken Podcast
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(62, 'about_heading', 'HEADING', 'Omdenken Podcast', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'about_body', 'HTML', NULL, '<p>A live podcast recording with audience participation. Thought-provoking conversations that challenge conventional thinking and inspire new perspectives.</p>', NULL, '2026-03-09 15:51:55'),
(62, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'highlight_1_title', 'TEXT', 'Live Recording', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'highlight_1_description', 'TEXT', 'Be part of a live podcast recording in front of an audience.', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'highlight_2_title', 'TEXT', 'Audience Participation', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'highlight_2_description', 'TEXT', 'Your questions and insights shape the conversation.', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'highlight_3_title', 'TEXT', 'New Perspectives', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'highlight_3_description', 'TEXT', 'Discover how reframing challenges can lead to creative solutions.', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(62, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 63 — event_36: Buurderij Haarlem
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(63, 'about_heading', 'HEADING', 'About Buurderij Haarlem', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'about_body', 'HTML', NULL, '<p>Buurderij Haarlem is a local food community where neighbours order directly from farmers and small producers in the region. What started as a simple idea grew into a place where food is not just bought, but shared as a story.</p><p>During this storytelling session, the audience is taken behind the scenes of Buurderij Haarlem. The story explains how local entrepreneurs, volunteers, and residents work together to create a system based on trust, transparency, and respect for nature.</p>', NULL, '2026-03-09 15:51:55'),
(63, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'highlight_1_title', 'TEXT', 'From Farmer to Neighbour', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'highlight_1_description', 'TEXT', 'A personal story about how local farmers connect directly with Haarlem residents, focusing on trust, fair pricing, and the journey food takes from land to table.', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'highlight_2_title', 'TEXT', 'Building a Local Food Community', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'highlight_2_description', 'TEXT', 'How Buurderij Haarlem became more than a marketplace - weekly meetups created friendships and a shared sense of responsibility for local food.', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'highlight_3_title', 'TEXT', 'Food with a Future', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'highlight_3_description', 'TEXT', 'How Buurderij Haarlem supports seasonal food, reduces waste, and encourages conscious consumption for future generations.', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/where-stories-come-alive.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(63, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 64 — event_37: Corrie voor kinderen
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(64, 'about_heading', 'HEADING', 'Corrie voor kinderen', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'about_body', 'HTML', NULL, '<p>A family storytelling experience that brings the story of Corrie to life for young audiences with engaging performances and vivid imagery.</p>', NULL, '2026-03-09 15:51:55'),
(64, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'highlight_1_title', 'TEXT', 'Stories for Every Child', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'highlight_1_description', 'TEXT', 'An accessible and joyful storytelling session designed with children in mind.', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'highlight_2_title', 'TEXT', 'Imagination in Action', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'highlight_2_description', 'TEXT', 'Watch as the performer brings characters to life through voice, gesture, and storytelling.', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'highlight_3_title', 'TEXT', 'A Shared Experience', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'highlight_3_description', 'TEXT', 'A session that brings families and communities together around shared stories.', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(64, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 65 — event_38: Winnaars verhalenvertel wedstrijd
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(65, 'about_heading', 'HEADING', 'Winnaars verhalenvertel wedstrijd', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'about_body', 'HTML', NULL, '<p>The winners of this year verhalenvertel competition share their winning stories about Haarlem, its people, and its spirit.</p>', NULL, '2026-03-09 15:51:55'),
(65, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'highlight_1_title', 'TEXT', 'Competition Winners', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'highlight_1_description', 'TEXT', 'Hear from the storytellers who won the Haarlem storytelling competition.', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'highlight_2_title', 'TEXT', 'Stories About Haarlem', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'highlight_2_description', 'TEXT', 'Each winner tells a story rooted in the city culture and everyday life.', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'highlight_3_title', 'TEXT', 'Community Voice', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'highlight_3_description', 'TEXT', 'The competition celebrates storytelling as a way to strengthen community bonds.', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(65, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 66 — event_39: Het verhaal van de Oeserzwammerij
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(66, 'about_heading', 'HEADING', 'Het verhaal van de Oeserzwammerij', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'about_body', 'HTML', NULL, '<p>An intimate storytelling session about the Oeserzwammerij, a local initiative growing oyster mushrooms sustainably in Haarlem.</p>', NULL, '2026-03-09 15:51:55'),
(66, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'highlight_1_title', 'TEXT', 'Sustainable Innovation', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'highlight_1_description', 'TEXT', 'Learn how local entrepreneurs are growing food sustainably in the city.', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'highlight_2_title', 'TEXT', 'Community Impact', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'highlight_2_description', 'TEXT', 'The Oeserzwammerij connects growers, volunteers, and consumers.', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'highlight_3_title', 'TEXT', 'From Waste to Food', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'highlight_3_description', 'TEXT', 'Using coffee grounds and organic waste to grow nutritious mushrooms.', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(66, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 67 — event_40: Flip Thinking Podcast
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(67, 'about_heading', 'HEADING', 'Flip Thinking Podcast', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'about_body', 'HTML', NULL, '<p>A live recording of the popular Flip Thinking podcast, where hosts and guests explore the art of reframing problems into opportunities.</p>', NULL, '2026-03-09 15:51:55'),
(67, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'highlight_1_title', 'TEXT', 'Reframe Your World', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'highlight_1_description', 'TEXT', 'Learn practical techniques for turning obstacles into opportunities.', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'highlight_2_title', 'TEXT', 'Live Podcast Recording', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'highlight_2_description', 'TEXT', 'Experience the magic of a podcast recorded before a live audience.', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'highlight_3_title', 'TEXT', 'Q and A with Audience', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'highlight_3_description', 'TEXT', 'After the recording, guests take questions from the audience.', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(67, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 68 — event_41: Meneer Anansi
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(68, 'about_heading', 'HEADING', 'Meneer Anansi', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'about_body', 'HTML', NULL, '<p>Meneer Anansi brings the West African trickster spider to life in Dutch, weaving together folklore, humour, and wisdom in a totally original way.</p>', NULL, '2026-03-09 15:51:55'),
(68, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'highlight_1_title', 'TEXT', 'Anansi the Trickster', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'highlight_1_description', 'TEXT', 'The beloved spider of West African folklore comes alive on stage.', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'highlight_2_title', 'TEXT', 'Dutch Folk and World Heritage', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'highlight_2_description', 'TEXT', 'Ancient Anansi stories told in Dutch, bridging cultures and generations.', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'highlight_3_title', 'TEXT', 'Humour and Wisdom', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'highlight_3_description', 'TEXT', 'Each Anansi story carries a lesson wrapped in laughter and surprise.', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(68, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 69 — event_42: Mister Anansi
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(69, 'about_heading', 'HEADING', 'Mister Anansi', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'about_body', 'HTML', NULL, '<p>Mister Anansi is the English-language version of the beloved spider trickster story, perfect for international visitors and English-speaking families.</p>', NULL, '2026-03-09 15:51:55'),
(69, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'highlight_1_title', 'TEXT', 'In English', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'highlight_1_description', 'TEXT', 'The Anansi story performed in English for international audiences.', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'highlight_2_title', 'TEXT', 'Universal Themes', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'highlight_2_description', 'TEXT', 'Stories of cunning, community, and creativity that transcend cultures.', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'highlight_3_title', 'TEXT', 'World Folklore on Stage', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'highlight_3_description', 'TEXT', 'Experience the rich tradition of West African oral storytelling at Haarlem Festival.', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(69, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 70 — event_43: Podcastlast Haarlem Special
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(70, 'about_heading', 'HEADING', 'Podcastlast Haarlem Special', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'about_body', 'HTML', NULL, '<p>A special live edition of the Podcastlast podcast, recorded at the Haarlem Festival with candid conversations and local insights.</p>', NULL, '2026-03-09 15:51:55'),
(70, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'highlight_1_title', 'TEXT', 'Haarlem Special Edition', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'highlight_1_description', 'TEXT', 'A one-of-a-kind live episode recorded exclusively at the festival.', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'highlight_2_title', 'TEXT', 'Local Conversations', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'highlight_2_description', 'TEXT', 'Featuring guests from Haarlem creative and cultural scene.', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'highlight_3_title', 'TEXT', 'Live Audience Experience', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'highlight_3_description', 'TEXT', 'Be part of the recording and feel the energy of a live podcast taping.', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(70, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 71 — event_44: De geschiedenis van familie ten Boom
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(71, 'about_heading', 'HEADING', 'De geschiedenis van familie ten Boom', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'about_body', 'HTML', NULL, '<p>The remarkable story of the ten Boom family, whose Haarlem home became a refuge during World War II, sheltering Jewish families at great personal risk.</p>', NULL, '2026-03-09 15:51:55'),
(71, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'highlight_1_title', 'TEXT', 'A Story of Courage', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'highlight_1_description', 'TEXT', 'The ten Boom family risked everything to shelter Jews during the occupation.', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'highlight_2_title', 'TEXT', 'Haarlem in History', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'highlight_2_description', 'TEXT', 'Explore how one family home became a symbol of resistance and compassion.', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'highlight_3_title', 'TEXT', 'Legacy and Memory', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'highlight_3_description', 'TEXT', 'Their story lives on as an inspiration for future generations.', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(71, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 72 — event_45: The History of the Ten Boom Family
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(72, 'about_heading', 'HEADING', 'The History of the Ten Boom Family', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'about_body', 'HTML', NULL, '<p>The story of the ten Boom family told in English. During WWII they used their Haarlem shop to hide Jewish families, leaving a lasting legacy on the city.</p>', NULL, '2026-03-09 15:51:55'),
(72, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'highlight_1_title', 'TEXT', 'In English', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'highlight_1_description', 'TEXT', 'The ten Boom family story told for international visitors.', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'highlight_2_title', 'TEXT', 'Wartime Haarlem', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'highlight_2_description', 'TEXT', 'Step back in time to a Haarlem that faced unimaginable choices.', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'highlight_3_title', 'TEXT', 'Hope and Resilience', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'highlight_3_description', 'TEXT', 'A story about human courage that resonates across generations and borders.', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(72, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 73 — event_46: Winners of the Storytelling Competition
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(73, 'about_heading', 'HEADING', 'Winners of the Storytelling Competition', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'about_body', 'HTML', NULL, '<p>The best storytellers from this year competition take the stage to share their winning tales about Haarlem in English, selected for creativity, emotional depth, and unique perspective.</p>', NULL, '2026-03-09 15:51:55'),
(73, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'highlight_1_title', 'TEXT', 'Competition Winners', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'highlight_1_description', 'TEXT', 'The English-language winners of the storytelling competition share their best work.', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'highlight_2_title', 'TEXT', 'Stories About Haarlem', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'highlight_2_description', 'TEXT', 'Each story is rooted in the life and character of the city.', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'highlight_3_title', 'TEXT', 'New Voices', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'highlight_3_description', 'TEXT', 'Discover emerging storytelling talent from Haarlem and beyond.', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(73, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55');
