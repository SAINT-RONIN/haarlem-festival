-- ============================================================
-- Migration v26: Storytelling Detail Page CMS
--
-- Adds a dedicated CMS page (storytelling-detail) with one
-- section per storytelling event. Editors can fill in the
-- about text, highlights, gallery images, and video URL
-- for each event's detail page at /storytelling/{id}.
--
-- Storytelling events (EventTypeId = 4, IsActive = 1):
--   34  Winnie de Poeh (4+)
--   35  Omdenken Podcast
--   36  The story of Buurderij Haarlem
--   37  Corrie voor kinderen
--   38  Winnaars van verhalenvertel wedstrijd, verhalen voor Haarlem
--   39  Het verhaal van de Oeserzwammerij
--   40  Flip Thinking Podcast
--   41  Meneer Anansi
--   42  Mister Anansi
--   43  Podcastlast Haarlem Special
--   44  De geschiedenis van familie ten Boom
--   45  The history of the Ten Boom Family
--   46  Winners of story telling competition, stories for Haarlem
-- ============================================================

-- ------------------------------------------------------------
-- 1. CMS Page
-- ------------------------------------------------------------
INSERT INTO `CmsPage` (`CmsPageId`, `Slug`, `Title`) VALUES
(7, 'storytelling-detail', 'Storytelling Detail Pages');

-- ------------------------------------------------------------
-- 2. CMS Sections (one per event, SectionKey = event_{id})
-- ------------------------------------------------------------
INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
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

-- ------------------------------------------------------------
-- 3. CMS Items — 19 items per event section
--    Items IDs 390-641
--    Format: (CmsItemId, CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAt)
-- ------------------------------------------------------------

-- Event 34: Winnie de Poeh (4+)  [section 61, items 390-408]
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAt`) VALUES
(390, 61, 'about_heading',        'HEADING',    'Winnie de Poeh (4+)',  NULL, NULL, NOW()),
(391, 61, 'about_body',           'HTML',       NULL, '<p>An enchanting storytelling experience for children aged 4 and above, bringing the beloved story of Winnie the Pooh to life in the heart of Haarlem.</p>', NULL, NOW()),
(392, 61, 'about_image_1',        'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, NOW()),
(393, 61, 'about_image_2',        'IMAGE_PATH', '/assets/Image/storytelling/WinnieThePoohHeader.png', NULL, NULL, NOW()),
(394, 61, 'highlight_1_title',    'TEXT',       'A Story for All Ages', NULL, NULL, NOW()),
(395, 61, 'highlight_1_description', 'TEXT',    'A classic tale reimagined for young and old alike.', NULL, NULL, NOW()),
(396, 61, 'highlight_1_image',    'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, NOW()),
(397, 61, 'highlight_2_title',    'TEXT',       'Live Storytelling', NULL, NULL, NOW()),
(398, 61, 'highlight_2_description', 'TEXT',    'Experience the magic of oral storytelling in an intimate setting.', NULL, NULL, NOW()),
(399, 61, 'highlight_2_image',    'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(400, 61, 'highlight_3_title',    'TEXT',       'Interactive & Fun', NULL, NULL, NOW()),
(401, 61, 'highlight_3_description', 'TEXT',    'Children are invited to participate and use their imagination.', NULL, NULL, NOW()),
(402, 61, 'highlight_3_image',    'IMAGE_PATH', '/assets/Image/storytelling/pig.jpg', NULL, NULL, NOW()),
(403, 61, 'gallery_image_1',      'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, NOW()),
(404, 61, 'gallery_image_2',      'IMAGE_PATH', '/assets/Image/storytelling/WinnieThePoohHeader.png', NULL, NULL, NOW()),
(405, 61, 'gallery_image_3',      'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(406, 61, 'gallery_image_4',      'IMAGE_PATH', '/assets/Image/storytelling/pig.jpg', NULL, NULL, NOW()),
(407, 61, 'gallery_image_5',      'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(408, 61, 'video_url',            'TEXT',       '', NULL, NULL, NOW());

-- Event 35: Omdenken Podcast  [section 62, items 409-427]
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAt`) VALUES
(409, 62, 'about_heading',        'HEADING',    'Omdenken Podcast', NULL, NULL, NOW()),
(410, 62, 'about_body',           'HTML',       NULL, '<p>A live podcast recording with audience participation. Experience thought-provoking conversations that challenge conventional thinking and inspire new perspectives.</p>', NULL, NOW()),
(411, 62, 'about_image_1',        'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(412, 62, 'about_image_2',        'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(413, 62, 'highlight_1_title',    'TEXT',       'Live Recording', NULL, NULL, NOW()),
(414, 62, 'highlight_1_description', 'TEXT',    'Be part of a live podcast recording in front of an audience.', NULL, NULL, NOW()),
(415, 62, 'highlight_1_image',    'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(416, 62, 'highlight_2_title',    'TEXT',       'Audience Participation', NULL, NULL, NOW()),
(417, 62, 'highlight_2_description', 'TEXT',    'Your questions and insights shape the conversation.', NULL, NULL, NOW()),
(418, 62, 'highlight_2_image',    'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, NOW()),
(419, 62, 'highlight_3_title',    'TEXT',       'New Perspectives', NULL, NULL, NOW()),
(420, 62, 'highlight_3_description', 'TEXT',    'Discover how reframing challenges can lead to creative solutions.', NULL, NULL, NOW()),
(421, 62, 'highlight_3_image',    'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(422, 62, 'gallery_image_1',      'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(423, 62, 'gallery_image_2',      'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(424, 62, 'gallery_image_3',      'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(425, 62, 'gallery_image_4',      'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, NOW()),
(426, 62, 'gallery_image_5',      'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(427, 62, 'video_url',            'TEXT',       '', NULL, NULL, NOW());

-- Event 36: The story of Buurderij Haarlem  [section 63, items 428-446]
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAt`) VALUES
(428, 63, 'about_heading',        'HEADING',    'About Buurderij Haarlem', NULL, NULL, NOW()),
(429, 63, 'about_body',           'HTML',       NULL, '<p>Buurderij Haarlem is a local food community where neighbours order directly from farmers and small producers in the region. What started as a simple idea grew into a place where food is not just bought, but shared as a story. Each product represents a person, a process, and a belief in fair trade and sustainability.</p><p>During this storytelling session, the audience is taken behind the scenes of Buurderij Haarlem. The story explains how local entrepreneurs, volunteers, and residents work together to create a system based on trust, transparency, and respect for nature.</p>', NULL, NOW()),
(430, 63, 'about_image_1',        'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(431, 63, 'about_image_2',        'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(432, 63, 'highlight_1_title',    'TEXT',       'From Farmer to Neighbour', NULL, NULL, NOW()),
(433, 63, 'highlight_1_description', 'TEXT',    'A personal story about how local farmers connect directly with Haarlem residents. This highlight focuses on trust, fair pricing, and the journey food takes from the land to the table.', NULL, NULL, NOW()),
(434, 63, 'highlight_1_image',    'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(435, 63, 'highlight_2_title',    'TEXT',       'Building a Local Food Community', NULL, NULL, NOW()),
(436, 63, 'highlight_2_description', 'TEXT',    'This story explains how Buurderij Haarlem became more than a marketplace. It shows how weekly meetups created friendships, conversations, and a shared sense of responsibility for local food.', NULL, NULL, NOW()),
(437, 63, 'highlight_2_image',    'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(438, 63, 'highlight_3_title',    'TEXT',       'Food with a Future', NULL, NULL, NOW()),
(439, 63, 'highlight_3_description', 'TEXT',    'This highlight focuses on sustainability and circular thinking. It tells how Buurderij Haarlem supports seasonal food, reduces waste, and encourages conscious consumption for future generations.', NULL, NULL, NOW()),
(440, 63, 'highlight_3_image',    'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, NOW()),
(441, 63, 'gallery_image_1',      'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(442, 63, 'gallery_image_2',      'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(443, 63, 'gallery_image_3',      'IMAGE_PATH', '/assets/Image/storytelling/where-stories-come-alive.jpg', NULL, NULL, NOW()),
(444, 63, 'gallery_image_4',      'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, NOW()),
(445, 63, 'gallery_image_5',      'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(446, 63, 'video_url',            'TEXT',       '', NULL, NULL, NOW());

-- Event 37: Corrie voor kinderen  [section 64, items 447-465]
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAt`) VALUES
(447, 64, 'about_heading',        'HEADING',    'Corrie voor kinderen', NULL, NULL, NOW()),
(448, 64, 'about_body',           'HTML',       NULL, '<p>A family storytelling experience that brings the story of Corrie to life for young audiences. With engaging performances and vivid imagery, this session is perfect for children and families.</p>', NULL, NOW()),
(449, 64, 'about_image_1',        'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(450, 64, 'about_image_2',        'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, NOW()),
(451, 64, 'highlight_1_title',    'TEXT',       'Stories for Every Child', NULL, NULL, NOW()),
(452, 64, 'highlight_1_description', 'TEXT',    'An accessible and joyful storytelling session designed with children in mind.', NULL, NULL, NOW()),
(453, 64, 'highlight_1_image',    'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(454, 64, 'highlight_2_title',    'TEXT',       'Imagination in Action', NULL, NULL, NOW()),
(455, 64, 'highlight_2_description', 'TEXT',    'Watch as the performer brings characters to life through voice, gesture, and storytelling.', NULL, NULL, NOW()),
(456, 64, 'highlight_2_image',    'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, NOW()),
(457, 64, 'highlight_3_title',    'TEXT',       'A Shared Experience', NULL, NULL, NOW()),
(458, 64, 'highlight_3_description', 'TEXT',    'A session that brings families and communities together around shared stories.', NULL, NULL, NOW()),
(459, 64, 'highlight_3_image',    'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(460, 64, 'gallery_image_1',      'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(461, 64, 'gallery_image_2',      'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, NOW()),
(462, 64, 'gallery_image_3',      'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(463, 64, 'gallery_image_4',      'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(464, 64, 'gallery_image_5',      'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(465, 64, 'video_url',            'TEXT',       '', NULL, NULL, NOW());

-- Events 38-46: remaining events with minimal placeholder content
-- Using shared storytelling images as placeholders until editors update them via CMS

-- Event 38: Winnaars van verhalenvertel wedstrijd  [section 65, items 466-484]
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAt`) VALUES
(466, 65, 'about_heading',        'HEADING',    'Winnaars van verhalenvertel wedstrijd', NULL, NULL, NOW()),
(467, 65, 'about_body',           'HTML',       NULL, '<p>The winners of this year''s storytelling competition take the stage to share their winning stories — tales about Haarlem, its people, and its spirit. Each story was selected for its originality, emotional depth, and power to connect.</p>', NULL, NOW()),
(468, 65, 'about_image_1',        'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(469, 65, 'about_image_2',        'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(470, 65, 'highlight_1_title',    'TEXT',       'Competition Winners', NULL, NULL, NOW()),
(471, 65, 'highlight_1_description', 'TEXT',    'Hear from the storytellers who won the Haarlem storytelling competition.', NULL, NULL, NOW()),
(472, 65, 'highlight_1_image',    'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(473, 65, 'highlight_2_title',    'TEXT',       'Stories About Haarlem', NULL, NULL, NOW()),
(474, 65, 'highlight_2_description', 'TEXT',    'Each winner tells a story rooted in the city''s culture and everyday life.', NULL, NULL, NOW()),
(475, 65, 'highlight_2_image',    'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(476, 65, 'highlight_3_title',    'TEXT',       'Community Voice', NULL, NULL, NOW()),
(477, 65, 'highlight_3_description', 'TEXT',    'The competition celebrates storytelling as a way to strengthen community bonds.', NULL, NULL, NOW()),
(478, 65, 'highlight_3_image',    'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, NOW()),
(479, 65, 'gallery_image_1',      'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(480, 65, 'gallery_image_2',      'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, NOW()),
(481, 65, 'gallery_image_3',      'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(482, 65, 'gallery_image_4',      'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(483, 65, 'gallery_image_5',      'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(484, 65, 'video_url',            'TEXT',       '', NULL, NULL, NOW());

-- Event 39: Het verhaal van de Oeserzwammerij  [section 66, items 485-503]
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAt`) VALUES
(485, 66, 'about_heading',        'HEADING',    'Het verhaal van de Oeserzwammerij', NULL, NULL, NOW()),
(486, 66, 'about_body',           'HTML',       NULL, '<p>An intimate storytelling session about the Oeserzwammerij — a local initiative that grows oyster mushrooms sustainably in the heart of Haarlem. Discover how this small project creates big impact for people, ecology, and community.</p>', NULL, NOW()),
(487, 66, 'about_image_1',        'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(488, 66, 'about_image_2',        'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(489, 66, 'highlight_1_title',    'TEXT',       'Sustainable Innovation', NULL, NULL, NOW()),
(490, 66, 'highlight_1_description', 'TEXT',    'Learn how local entrepreneurs are growing food sustainably in the city.', NULL, NULL, NOW()),
(491, 66, 'highlight_1_image',    'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(492, 66, 'highlight_2_title',    'TEXT',       'Community Impact', NULL, NULL, NOW()),
(493, 66, 'highlight_2_description', 'TEXT',    'The Oeserzwammerij connects growers, volunteers, and consumers.', NULL, NULL, NOW()),
(494, 66, 'highlight_2_image',    'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(495, 66, 'highlight_3_title',    'TEXT',       'From Waste to Food', NULL, NULL, NOW()),
(496, 66, 'highlight_3_description', 'TEXT',    'Using coffee grounds and organic waste to grow nutritious mushrooms.', NULL, NULL, NOW()),
(497, 66, 'highlight_3_image',    'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, NOW()),
(498, 66, 'gallery_image_1',      'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(499, 66, 'gallery_image_2',      'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(500, 66, 'gallery_image_3',      'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, NOW()),
(501, 66, 'gallery_image_4',      'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(502, 66, 'gallery_image_5',      'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(503, 66, 'video_url',            'TEXT',       '', NULL, NULL, NOW());

-- Event 40: Flip Thinking Podcast  [section 67, items 504-522]
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAt`) VALUES
(504, 67, 'about_heading',        'HEADING',    'Flip Thinking Podcast', NULL, NULL, NOW()),
(505, 67, 'about_body',           'HTML',       NULL, '<p>A live recording of the popular Flip Thinking podcast, where hosts and guests explore the art of reframing problems into opportunities. Recorded in front of a live audience at the Haarlem Festival.</p>', NULL, NOW()),
(506, 67, 'about_image_1',        'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(507, 67, 'about_image_2',        'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, NOW()),
(508, 67, 'highlight_1_title',    'TEXT',       'Reframe Your World', NULL, NULL, NOW()),
(509, 67, 'highlight_1_description', 'TEXT',    'Learn practical techniques for turning obstacles into opportunities.', NULL, NULL, NOW()),
(510, 67, 'highlight_1_image',    'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(511, 67, 'highlight_2_title',    'TEXT',       'Live Podcast Recording', NULL, NULL, NOW()),
(512, 67, 'highlight_2_description', 'TEXT',    'Experience the magic of a podcast recorded before a live audience.', NULL, NULL, NOW()),
(513, 67, 'highlight_2_image',    'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(514, 67, 'highlight_3_title',    'TEXT',       'Q&A with Audience', NULL, NULL, NOW()),
(515, 67, 'highlight_3_description', 'TEXT',    'After the recording, guests take questions from the audience.', NULL, NULL, NOW()),
(516, 67, 'highlight_3_image',    'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(517, 67, 'gallery_image_1',      'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(518, 67, 'gallery_image_2',      'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(519, 67, 'gallery_image_3',      'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(520, 67, 'gallery_image_4',      'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, NOW()),
(521, 67, 'gallery_image_5',      'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(522, 67, 'video_url',            'TEXT',       '', NULL, NULL, NOW());

-- Event 41: Meneer Anansi  [section 68, items 523-541]
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAt`) VALUES
(523, 68, 'about_heading',        'HEADING',    'Meneer Anansi', NULL, NULL, NOW()),
(524, 68, 'about_body',           'HTML',       NULL, '<p>Meneer Anansi brings the West African trickster spider to life in Dutch. A spellbinding performance for the whole family that weaves together folklore, humour, and wisdom in a totally original way.</p>', NULL, NOW()),
(525, 68, 'about_image_1',        'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, NOW()),
(526, 68, 'about_image_2',        'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, NOW()),
(527, 68, 'highlight_1_title',    'TEXT',       'Anansi the Trickster', NULL, NULL, NOW()),
(528, 68, 'highlight_1_description', 'TEXT',    'The beloved spider of West African folklore comes alive on stage.', NULL, NULL, NOW()),
(529, 68, 'highlight_1_image',    'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, NOW()),
(530, 68, 'highlight_2_title',    'TEXT',       'Dutch Folk & World Heritage', NULL, NULL, NOW()),
(531, 68, 'highlight_2_description', 'TEXT',    'Ancient Anansi stories told in Dutch, bridging cultures and generations.', NULL, NULL, NOW()),
(532, 68, 'highlight_2_image',    'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, NOW()),
(533, 68, 'highlight_3_title',    'TEXT',       'Humour & Wisdom', NULL, NULL, NOW()),
(534, 68, 'highlight_3_description', 'TEXT',    'Each Anansi story carries a lesson wrapped in laughter and surprise.', NULL, NULL, NOW()),
(535, 68, 'highlight_3_image',    'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, NOW()),
(536, 68, 'gallery_image_1',      'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, NOW()),
(537, 68, 'gallery_image_2',      'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, NOW()),
(538, 68, 'gallery_image_3',      'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, NOW()),
(539, 68, 'gallery_image_4',      'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, NULL, NOW()),
(540, 68, 'gallery_image_5',      'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(541, 68, 'video_url',            'TEXT',       '', NULL, NULL, NOW());

-- Event 42: Mister Anansi  [section 69, items 542-560]
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAt`) VALUES
(542, 69, 'about_heading',        'HEADING',    'Mister Anansi', NULL, NULL, NOW()),
(543, 69, 'about_body',           'HTML',       NULL, '<p>Mister Anansi is the English-language version of the beloved spider trickster story. Perfect for international visitors and English-speaking families, this session brings West African folklore to Haarlem with humour, magic, and insight.</p>', NULL, NOW()),
(544, 69, 'about_image_1',        'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, NOW()),
(545, 69, 'about_image_2',        'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, NULL, NOW()),
(546, 69, 'highlight_1_title',    'TEXT',       'In English', NULL, NULL, NOW()),
(547, 69, 'highlight_1_description', 'TEXT',    'The Anansi story performed in English for international audiences.', NULL, NULL, NOW()),
(548, 69, 'highlight_1_image',    'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, NOW()),
(549, 69, 'highlight_2_title',    'TEXT',       'Universal Themes', NULL, NULL, NOW()),
(550, 69, 'highlight_2_description', 'TEXT',    'Stories of cunning, community, and creativity that transcend cultures.', NULL, NULL, NOW()),
(551, 69, 'highlight_2_image',    'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, NOW()),
(552, 69, 'highlight_3_title',    'TEXT',       'World Folklore on Stage', NULL, NULL, NOW()),
(553, 69, 'highlight_3_description', 'TEXT',    'Experience the rich tradition of West African oral storytelling at Haarlem Festival.', NULL, NULL, NOW()),
(554, 69, 'highlight_3_image',    'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, NOW()),
(555, 69, 'gallery_image_1',      'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, NOW()),
(556, 69, 'gallery_image_2',      'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, NULL, NOW()),
(557, 69, 'gallery_image_3',      'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, NOW()),
(558, 69, 'gallery_image_4',      'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, NOW()),
(559, 69, 'gallery_image_5',      'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(560, 69, 'video_url',            'TEXT',       '', NULL, NULL, NOW());

-- Event 43: Podcastlast Haarlem Special  [section 70, items 561-579]
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAt`) VALUES
(561, 70, 'about_heading',        'HEADING',    'Podcastlast Haarlem Special', NULL, NULL, NOW()),
(562, 70, 'about_body',           'HTML',       NULL, '<p>A special live edition of the Podcastlast podcast, recorded at the Haarlem Festival. Expect candid conversations, local insights, and a dose of Haarlem humour — all in front of a live audience.</p>', NULL, NOW()),
(563, 70, 'about_image_1',        'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(564, 70, 'about_image_2',        'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(565, 70, 'highlight_1_title',    'TEXT',       'Haarlem Special Edition', NULL, NULL, NOW()),
(566, 70, 'highlight_1_description', 'TEXT',    'A one-of-a-kind live episode recorded exclusively at the festival.', NULL, NULL, NOW()),
(567, 70, 'highlight_1_image',    'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(568, 70, 'highlight_2_title',    'TEXT',       'Local Conversations', NULL, NULL, NOW()),
(569, 70, 'highlight_2_description', 'TEXT',    'Featuring guests from Haarlem''s creative and cultural scene.', NULL, NULL, NOW()),
(570, 70, 'highlight_2_image',    'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(571, 70, 'highlight_3_title',    'TEXT',       'Live Audience Experience', NULL, NULL, NOW()),
(572, 70, 'highlight_3_description', 'TEXT',    'Be part of the recording and feel the energy of a live podcast taping.', NULL, NULL, NOW()),
(573, 70, 'highlight_3_image',    'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(574, 70, 'gallery_image_1',      'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(575, 70, 'gallery_image_2',      'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(576, 70, 'gallery_image_3',      'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(577, 70, 'gallery_image_4',      'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, NOW()),
(578, 70, 'gallery_image_5',      'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(579, 70, 'video_url',            'TEXT',       '', NULL, NULL, NOW());

-- Event 44: De geschiedenis van familie ten Boom  [section 71, items 580-598]
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAt`) VALUES
(580, 71, 'about_heading',        'HEADING',    'De geschiedenis van familie ten Boom', NULL, NULL, NOW()),
(581, 71, 'about_body',           'HTML',       NULL, '<p>The remarkable story of the ten Boom family, whose Haarlem home became a refuge during World War II. This storytelling session honours their courage, faith, and the extraordinary people they helped.</p>', NULL, NOW()),
(582, 71, 'about_image_1',        'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(583, 71, 'about_image_2',        'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(584, 71, 'highlight_1_title',    'TEXT',       'A Story of Courage', NULL, NULL, NOW()),
(585, 71, 'highlight_1_description', 'TEXT',    'The ten Boom family risked everything to shelter Jews during the occupation.', NULL, NULL, NOW()),
(586, 71, 'highlight_1_image',    'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(587, 71, 'highlight_2_title',    'TEXT',       'Haarlem in History', NULL, NULL, NOW()),
(588, 71, 'highlight_2_description', 'TEXT',    'Explore how one family''s home became a symbol of resistance and compassion.', NULL, NULL, NOW()),
(589, 71, 'highlight_2_image',    'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, NOW()),
(590, 71, 'highlight_3_title',    'TEXT',       'Legacy & Memory', NULL, NULL, NOW()),
(591, 71, 'highlight_3_description', 'TEXT',    'Their story lives on as an inspiration for future generations in Haarlem and beyond.', NULL, NULL, NOW()),
(592, 71, 'highlight_3_image',    'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(593, 71, 'gallery_image_1',      'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(594, 71, 'gallery_image_2',      'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(595, 71, 'gallery_image_3',      'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, NOW()),
(596, 71, 'gallery_image_4',      'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(597, 71, 'gallery_image_5',      'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(598, 71, 'video_url',            'TEXT',       '', NULL, NULL, NOW());

-- Event 45: The history of the Ten Boom Family (English)  [section 72, items 599-617]
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAt`) VALUES
(599, 72, 'about_heading',        'HEADING',    'The History of the Ten Boom Family', NULL, NULL, NOW()),
(600, 72, 'about_body',           'HTML',       NULL, '<p>The story of the ten Boom family told in English. During World War II, the family used their Haarlem watchmaker''s shop to hide Jewish families. This session explores their extraordinary bravery and the lasting legacy they left on the city.</p>', NULL, NOW()),
(601, 72, 'about_image_1',        'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(602, 72, 'about_image_2',        'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, NOW()),
(603, 72, 'highlight_1_title',    'TEXT',       'In English', NULL, NULL, NOW()),
(604, 72, 'highlight_1_description', 'TEXT',    'The ten Boom family story told for international visitors.', NULL, NULL, NOW()),
(605, 72, 'highlight_1_image',    'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(606, 72, 'highlight_2_title',    'TEXT',       'Wartime Haarlem', NULL, NULL, NOW()),
(607, 72, 'highlight_2_description', 'TEXT',    'Step back in time to a Haarlem that faced unimaginable choices.', NULL, NULL, NOW()),
(608, 72, 'highlight_2_image',    'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, NOW()),
(609, 72, 'highlight_3_title',    'TEXT',       'Hope & Resilience', NULL, NULL, NOW()),
(610, 72, 'highlight_3_description', 'TEXT',    'A story about human courage that resonates across generations and borders.', NULL, NULL, NOW()),
(611, 72, 'highlight_3_image',    'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(612, 72, 'gallery_image_1',      'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(613, 72, 'gallery_image_2',      'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, NOW()),
(614, 72, 'gallery_image_3',      'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(615, 72, 'gallery_image_4',      'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(616, 72, 'gallery_image_5',      'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(617, 72, 'video_url',            'TEXT',       '', NULL, NULL, NOW());

-- Event 46: Winners of story telling competition (English)  [section 73, items 618-636]
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAt`) VALUES
(618, 73, 'about_heading',        'HEADING',    'Winners of the Storytelling Competition', NULL, NULL, NOW()),
(619, 73, 'about_body',           'HTML',       NULL, '<p>The best storytellers from this year''s competition take the stage to share their winning tales about Haarlem in English. Each story was selected for its creativity, emotional depth, and unique perspective on city life.</p>', NULL, NOW()),
(620, 73, 'about_image_1',        'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(621, 73, 'about_image_2',        'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(622, 73, 'highlight_1_title',    'TEXT',       'Competition Winners', NULL, NULL, NOW()),
(623, 73, 'highlight_1_description', 'TEXT',    'The English-language winners of the storytelling competition share their best work.', NULL, NULL, NOW()),
(624, 73, 'highlight_1_image',    'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(625, 73, 'highlight_2_title',    'TEXT',       'Stories About Haarlem', NULL, NULL, NOW()),
(626, 73, 'highlight_2_description', 'TEXT',    'Each story is rooted in the life and character of the city.', NULL, NULL, NOW()),
(627, 73, 'highlight_2_image',    'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, NOW()),
(628, 73, 'highlight_3_title',    'TEXT',       'New Voices', NULL, NULL, NOW()),
(629, 73, 'highlight_3_description', 'TEXT',    'Discover emerging storytelling talent from Haarlem and beyond.', NULL, NULL, NOW()),
(630, 73, 'highlight_3_image',    'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(631, 73, 'gallery_image_1',      'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, NOW()),
(632, 73, 'gallery_image_2',      'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, NOW()),
(633, 73, 'gallery_image_3',      'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, NOW()),
(634, 73, 'gallery_image_4',      'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, NOW()),
(635, 73, 'gallery_image_5',      'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, NOW()),
(636, 73, 'video_url',            'TEXT',       '', NULL, NULL, NOW());

-- Update AUTO_INCREMENT values
ALTER TABLE `CmsPage`    AUTO_INCREMENT = 8;
ALTER TABLE `CmsSection` AUTO_INCREMENT = 74;
ALTER TABLE `CmsItem`    AUTO_INCREMENT = 637;
