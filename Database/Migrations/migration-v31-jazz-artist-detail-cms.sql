-- migration-v31-jazz-artist-detail-cms.sql
--
-- Imports the CMS data for the jazz-artist-detail page (CmsPageId = 8).
-- Covers two artist sections:
--   CmsSectionId 77 = event_1  (Gumbo Kings)
--   CmsSectionId 78 = event_3  (Ntjam Rosie)
--
-- Safe to run multiple times: all statements use INSERT IGNORE.
-- ---------------------------------------------------------------

-- CmsPage
INSERT IGNORE INTO `CmsPage` (`CmsPageId`, `Slug`, `Title`) VALUES
(8, 'jazz-artist-detail', 'Jazz Artist Detail Pages');

-- CmsSection
INSERT IGNORE INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(77, 8, 'event_1'),
(78, 8, 'event_3');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 77 — Gumbo Kings (IDs 682–758)
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(682, 77, 'hero_subtitle', 'TEXT', 'New Orleans Jazz', NULL, NULL, '2026-03-15 16:01:52'),
(683, 77, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/Jazz/GubmboKings-Hero.png', NULL, NULL, '2026-03-15 16:01:52'),
(684, 77, 'origin_text', 'TEXT', 'Origin: New Orleans, Louisiana, USA', NULL, NULL, '2026-03-16 14:51:53'),
(685, 77, 'formed_text', 'TEXT', 'Formed: 2015', NULL, NULL, '2026-03-15 16:01:52'),
(686, 77, 'performances_text', 'TEXT', '2 performances at Haarlem Jazz 2026', NULL, NULL, '2026-03-15 16:01:52'),
(687, 77, 'overview_heading', 'HEADING', 'Gumbo Kings', NULL, NULL, '2026-03-15 16:01:52'),
(688, 77, 'overview_lead', 'TEXT', 'High-energy New Orleans style jazz band bringing authentic Big Easy sound to Haarlem. Known for their infectious rhythms and crowd-pleasing performances that get audiences on their feet.', NULL, NULL, '2026-03-15 16:01:52'),
(689, 77, 'overview_body_primary', 'TEXT', 'The Gumbo Kings deliver an electrifying blend of traditional New Orleans jazz, funk, and second-line grooves. With a powerful horn section, driving rhythm section, and authentic Crescent City soul, they transport audiences straight to the streets of the French Quarter. Their performances are known for spontaneous moments of musical magic and infectious energy that keeps crowds dancing all night long. Since their formation in 2015, they have become one of the most sought-after New Orleans jazz acts in Europe.', NULL, NULL, '2026-03-15 16:01:52'),
(690, 77, 'overview_body_secondary', 'TEXT', 'Drawing inspiration from the rich musical heritage of New Orleans, the Gumbo Kings have mastered the art of combining traditional jazz elements with contemporary energy. Their repertoire spans classic jazz standards, original compositions, and reimagined funk grooves. The band has performed at major jazz festivals across Europe and the United States, earning acclaim for their authentic sound and dynamic stage presence. Their commitment to preserving and evolving the New Orleans jazz tradition has made them favorites among both purists and new jazz audiences.', NULL, NULL, '2026-03-15 16:01:52'),
(691, 77, 'lineup_heading', 'TEXT', 'Band Lineup', NULL, NULL, '2026-03-15 16:01:52'),
(692, 77, 'lineup_1', 'TEXT', 'Marcus Johnson - Trumpet, Band Leader', NULL, NULL, '2026-03-15 16:01:52'),
(693, 77, 'lineup_2', 'TEXT', 'DeShawn Williams - Trombone', NULL, NULL, '2026-03-15 16:01:52'),
(694, 77, 'lineup_3', 'TEXT', 'Antoine Davis - Tenor Saxophone', NULL, NULL, '2026-03-15 16:01:52'),
(695, 77, 'lineup_4', 'TEXT', 'Jerome Baptiste - Drums', NULL, NULL, '2026-03-15 16:01:52'),
(696, 77, 'lineup_5', 'TEXT', 'Louis Carter - Upright Bass', NULL, NULL, '2026-03-15 16:01:52'),
(697, 77, 'lineup_6', 'TEXT', 'Raymond Pierce - Piano', NULL, NULL, '2026-03-15 16:01:52'),
(698, 77, 'highlights_heading', 'TEXT', 'Career Highlights', NULL, NULL, '2026-03-15 16:01:52'),
(699, 77, 'highlight_1', 'TEXT', 'Featured performers at New Orleans Jazz and Heritage Festival 2023', NULL, NULL, '2026-03-15 16:01:52'),
(700, 77, 'highlight_2', 'TEXT', 'Touring Europe extensively since 2019, performing at 50+ major festivals', NULL, NULL, '2026-03-15 16:01:52'),
(701, 77, 'highlight_3', 'TEXT', 'Authentic brass band sound with modern energy and innovation', NULL, NULL, '2026-03-15 16:01:52'),
(702, 77, 'highlight_4', 'TEXT', 'Collaborations with legendary New Orleans musicians including Trombone Shorty', NULL, NULL, '2026-03-15 16:01:52'),
(703, 77, 'highlight_5', 'TEXT', 'Known for interactive, high-energy live shows that get audiences dancing', NULL, NULL, '2026-03-15 16:01:52'),
(704, 77, 'highlight_6', 'TEXT', 'Winner of Best Jazz Ensemble at European Jazz Awards 2022', NULL, NULL, '2026-03-15 16:01:52'),
(705, 77, 'photo_gallery_heading', 'TEXT', 'Photo Gallery', NULL, NULL, '2026-03-15 16:01:52'),
(706, 77, 'photo_gallery_description', 'TEXT', 'Experience the energy and passion of Gumbo Kings through these performance and portrait photographs.', NULL, NULL, '2026-03-15 16:01:52'),
(707, 77, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/Jazz/GumboGallery1.png', NULL, NULL, '2026-03-15 16:01:52'),
(708, 77, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/Jazz/GumboGallery2.png', NULL, NULL, '2026-03-15 16:01:52'),
(709, 77, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/Jazz/GumboGallery3.png', NULL, NULL, '2026-03-15 16:01:52'),
(710, 77, 'albums_heading', 'TEXT', 'Featured Albums', NULL, NULL, '2026-03-15 16:01:52'),
(711, 77, 'albums_description', 'TEXT', 'Explore the studio recordings that capture the magic of Gumbo Kings. Each album showcases their evolution and mastery of the New Orleans jazz tradition.', NULL, NULL, '2026-03-15 16:01:52'),
(712, 77, 'album_1_title', 'TEXT', 'Second Line Swing', NULL, NULL, '2026-03-15 16:01:52'),
(713, 77, 'album_1_description', 'TEXT', 'Their breakthrough album featuring traditional second line rhythms mixed with contemporary jazz sensibilities. The title track became a festival favorite across Europe.', NULL, NULL, '2026-03-15 16:01:52'),
(714, 77, 'album_1_year', 'TEXT', '2019', NULL, NULL, '2026-03-15 16:01:52'),
(715, 77, 'album_1_tag', 'TEXT', 'JAZZ', NULL, NULL, '2026-03-15 16:01:52'),
(716, 77, 'album_1_image', 'IMAGE_PATH', '/assets/Image/Jazz/GumboKingsAlbum1.png', NULL, NULL, '2026-03-15 16:01:52'),
(717, 77, 'album_2_title', 'TEXT', 'Big Easy', NULL, NULL, '2026-03-15 16:01:52'),
(718, 77, 'album_2_description', 'TEXT', 'A love letter to New Orleans featuring reimagined classics and original compositions inspired by the city\'s rich musical heritage.', NULL, NULL, '2026-03-15 16:01:52'),
(719, 77, 'album_2_year', 'TEXT', '2021', NULL, NULL, '2026-03-15 16:01:52'),
(720, 77, 'album_2_tag', 'TEXT', 'JAZZ', NULL, NULL, '2026-03-15 16:01:52'),
(721, 77, 'album_2_image', 'IMAGE_PATH', '/assets/Image/Jazz/GumboKingsAlbum2.png', NULL, NULL, '2026-03-15 16:01:52'),
(722, 77, 'album_3_title', 'TEXT', 'Live at Paradiso', NULL, NULL, '2026-03-15 16:01:52'),
(723, 77, 'album_3_description', 'TEXT', 'Recorded live at Amsterdam\'s legendary Paradiso venue, this album captures the raw energy and spontaneity of their live performances.', NULL, NULL, '2026-03-15 16:01:52'),
(724, 77, 'album_3_year', 'TEXT', '2023', NULL, NULL, '2026-03-15 16:01:52'),
(725, 77, 'album_3_tag', 'TEXT', 'LIVE', NULL, NULL, '2026-03-15 16:01:52'),
(726, 77, 'album_3_image', 'IMAGE_PATH', '/assets/Image/Jazz/GumboKingsAlbum3.png', NULL, NULL, '2026-03-15 16:01:52'),
(727, 77, 'listen_heading', 'TEXT', 'LISTEN NOW', NULL, NULL, '2026-03-15 16:01:52'),
(728, 77, 'listen_subheading', 'TEXT', 'Important Tracks', NULL, NULL, '2026-03-15 16:01:52'),
(729, 77, 'listen_description', 'TEXT', 'Listen to excerpts from Gumbo Kings\'s most important and popular tracks. Experience the energy and musicianship that defines their sound.', NULL, NULL, '2026-03-15 16:01:52'),
(730, 77, 'track_1_title', 'TEXT', 'All Night Long', NULL, NULL, '2026-03-15 16:01:52'),
(731, 77, 'track_1_album', 'TEXT', 'Live in the Quarter', NULL, NULL, '2026-03-15 16:01:52'),
(732, 77, 'track_1_description', 'TEXT', 'Classic New Orleans standard with powerful brass arrangements', NULL, NULL, '2026-03-15 16:01:52'),
(733, 77, 'track_1_duration', 'TEXT', '4:32', NULL, NULL, '2026-03-15 16:01:52'),
(734, 77, 'track_1_image', 'IMAGE_PATH', '/assets/Image/Jazz/Allnightlong.png', NULL, NULL, '2026-03-15 16:01:52'),
(735, 77, 'track_1_progress_class', 'TEXT', 'w-[5%]', NULL, NULL, '2026-03-15 16:01:52'),
(736, 77, 'track_2_title', 'TEXT', 'Hot Damn!', NULL, NULL, '2026-03-15 16:01:52'),
(737, 77, 'track_2_album', 'TEXT', 'Brass and Soul', NULL, NULL, '2026-03-15 16:01:52'),
(738, 77, 'track_2_description', 'TEXT', 'Original composition featuring traditional second-line rhythms', NULL, NULL, '2026-03-15 16:01:52'),
(739, 77, 'track_2_duration', 'TEXT', '3:45', NULL, NULL, '2026-03-15 16:01:52'),
(740, 77, 'track_2_image', 'IMAGE_PATH', '/assets/Image/Jazz/Container.png', NULL, NULL, '2026-03-15 16:01:52'),
(741, 77, 'track_2_progress_class', 'TEXT', 'w-[15%]', NULL, NULL, '2026-03-15 16:01:52'),
(742, 77, 'track_3_title', 'TEXT', 'Valenzuela', NULL, NULL, '2026-03-15 16:01:52'),
(743, 77, 'track_3_album', 'TEXT', 'Big Easy Nights', NULL, NULL, '2026-03-15 16:01:52'),
(744, 77, 'track_3_description', 'TEXT', 'Fast-paced instrumental showcasing virtuoso musicianship', NULL, NULL, '2026-03-15 16:01:52'),
(745, 77, 'track_3_duration', 'TEXT', '4:18', NULL, NULL, '2026-03-15 16:01:52'),
(746, 77, 'track_3_image', 'IMAGE_PATH', '/assets/Image/Jazz/Listennowsection.png', NULL, NULL, '2026-03-15 16:01:52'),
(747, 77, 'track_3_progress_class', 'TEXT', 'w-full', NULL, NULL, '2026-03-15 16:01:52'),
(748, 77, 'track_4_title', 'TEXT', 'Here We Are', NULL, NULL, '2026-03-16 14:25:39'),
(749, 77, 'track_4_album', 'TEXT', 'Live in the Quarter', NULL, NULL, '2026-03-15 16:01:52'),
(750, 77, 'track_4_description', 'TEXT', 'High-energy rendition of the jazz funeral classic', NULL, NULL, '2026-03-15 16:01:52'),
(751, 77, 'track_4_duration', 'TEXT', '5:12', NULL, NULL, '2026-03-15 16:01:52'),
(752, 77, 'track_4_image', 'IMAGE_PATH', '/assets/Image/Jazz/Allnightlong.png', NULL, NULL, '2026-03-15 16:01:52'),
(753, 77, 'track_4_progress_class', 'TEXT', 'w-[60%]', NULL, NULL, '2026-03-15 16:01:52'),
(754, 77, 'live_cta_heading', 'TEXT', 'Experience Gumbo Kings Live', NULL, NULL, '2026-03-15 16:01:52'),
(755, 77, 'live_cta_description', 'TEXT', 'Do not miss the chance to see Gumbo Kings perform live at Haarlem Jazz 2026. With 2 performances scheduled, there are multiple opportunities to experience their incredible energy and musicianship.', NULL, NULL, '2026-03-15 16:01:52'),
(756, 77, 'performances_section_id', 'TEXT', 'artist-performances', NULL, NULL, '2026-03-15 16:15:19'),
(757, 77, 'performances_heading', 'TEXT', 'Gumbo Kings at Haarlem Jazz 2026', NULL, NULL, '2026-03-15 16:01:52'),
(758, 77, 'performances_description', 'TEXT', 'Catch Gumbo Kings performing during the Haarlem Jazz Festival. Each performance offers a unique experience from intimate indoor shows to free outdoor concerts.', NULL, NULL, '2026-03-15 16:01:52');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 78 — Ntjam Rosie (IDs 759–834)
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(759, 78, 'hero_subtitle', 'TEXT', 'Vocal Jazz', NULL, NULL, '2026-03-15 16:01:52'),
(760, 78, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamhero.png', NULL, NULL, '2026-03-15 16:01:52'),
(761, 78, 'origin_text', 'TEXT', 'Origin: Cameroon / Netherlands', NULL, NULL, '2026-03-15 16:01:52'),
(762, 78, 'formed_text', 'TEXT', 'Formed: 2008', NULL, NULL, '2026-03-15 16:01:52'),
(763, 78, 'performances_text', 'TEXT', '2 performances at Haarlem Jazz 2026', NULL, NULL, '2026-03-15 16:01:52'),
(764, 78, 'overview_heading', 'HEADING', 'Ntjam Rosie', NULL, NULL, '2026-03-15 16:01:52'),
(765, 78, 'overview_lead', 'TEXT', 'Ntjam Rosie was born in Cameroon on March 18, 1983, and moved to the Netherlands at the age of nine. She blends her West-African roots with Western musical traditions, combining jazz, soul, pop and Afro influences.', NULL, NULL, '2026-03-15 16:01:52'),
(766, 78, 'overview_body_primary', 'TEXT', 'Her debut album, Atouba, released in 2008, was the first to showcase that hybrid style, mixing African rhythms with soul and jazz influences. Since then she has developed a distinctive musical voice across several albums and performances.', NULL, NULL, '2026-03-15 16:01:52'),
(767, 78, 'overview_body_secondary', 'TEXT', 'Ntjam Rosie has built a reputation for compelling live performances and wide appeal. She has performed at major festivals and toured internationally. Her music resonates with both jazz and soul audiences, and she continues to evolve by blending tradition and innovation.', NULL, NULL, '2026-03-15 16:01:52'),
(768, 78, 'lineup_heading', 'TEXT', 'Band Lineup', NULL, NULL, '2026-03-15 16:01:52'),
(769, 78, 'lineup_1', 'TEXT', 'Ntjam Rosie - Vocals', NULL, NULL, '2026-03-15 16:01:52'),
(770, 78, 'lineup_2', 'TEXT', 'Bart Wirtz - Tenor Saxophone', NULL, NULL, '2026-03-15 16:01:52'),
(771, 78, 'lineup_3', 'TEXT', 'Niels Broos - Piano and Keys', NULL, NULL, '2026-03-15 16:01:52'),
(772, 78, 'lineup_4', 'TEXT', 'Bram Hakkens - Drums', NULL, NULL, '2026-03-15 16:01:52'),
(773, 78, 'lineup_5', 'TEXT', 'Tijn Wybenga - Bass', NULL, NULL, '2026-03-15 16:01:52'),
(774, 78, 'highlights_heading', 'TEXT', 'Career Highlights', NULL, NULL, '2026-03-15 16:01:52'),
(775, 78, 'highlight_1', 'TEXT', 'Featured performer at the New Orleans Jazz and Heritage Festival 2023', NULL, NULL, '2026-03-15 16:01:52'),
(776, 78, 'highlight_2', 'TEXT', 'Toured across Europe since 2019, playing 50+ major festivals', NULL, NULL, '2026-03-15 16:01:52'),
(777, 78, 'highlight_3', 'TEXT', 'Blends Afro-European vocals with modern jazz and soul energy', NULL, NULL, '2026-03-15 16:01:52'),
(778, 78, 'highlight_4', 'TEXT', 'Collaborated with New Orleans artists, including Trombone Shorty', NULL, NULL, '2026-03-15 16:01:52'),
(779, 78, 'highlight_5', 'TEXT', 'Renowned for vibrant, high-energy live shows', NULL, NULL, '2026-03-15 16:01:52'),
(780, 78, 'highlight_6', 'TEXT', 'Winner of Best Jazz Ensemble at the European Jazz Awards 2022', NULL, NULL, '2026-03-15 16:01:52'),
(781, 78, 'photo_gallery_heading', 'TEXT', 'Photo Gallery', NULL, NULL, '2026-03-15 16:01:52'),
(782, 78, 'photo_gallery_description', 'TEXT', 'Experience the soulful elegance of Ntjam Rosie through these intimate performance and portrait photographs.', NULL, NULL, '2026-03-15 16:01:52'),
(783, 78, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamgallery1.png', NULL, NULL, '2026-03-15 16:01:52'),
(784, 78, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamgallery2.png', NULL, NULL, '2026-03-15 16:01:52'),
(785, 78, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamgallery3.png', NULL, NULL, '2026-03-15 16:01:52'),
(786, 78, 'albums_heading', 'TEXT', 'Featured Albums', NULL, NULL, '2026-03-15 16:01:52'),
(787, 78, 'albums_description', 'TEXT', 'Explore the studio recordings that capture the soulful artistry of Ntjam Rosie. Each album reflects her evolving blend of jazz, soul, and Afro-inspired sound.', NULL, NULL, '2026-03-15 16:01:53'),
(788, 78, 'album_1_title', 'TEXT', 'Atouba', NULL, NULL, '2026-03-15 16:01:53'),
(789, 78, 'album_1_description', 'TEXT', 'Her debut album where Ntjam Rosie introduced her Afro-European blend of soul and jazz.', NULL, NULL, '2026-03-15 16:01:53'),
(790, 78, 'album_1_year', 'TEXT', '2019', NULL, NULL, '2026-03-15 16:01:53'),
(791, 78, 'album_1_tag', 'TEXT', 'JAZZ', NULL, NULL, '2026-03-15 16:01:53'),
(792, 78, 'album_1_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamalbum1.png', NULL, NULL, '2026-03-15 16:01:53'),
(793, 78, 'album_2_title', 'TEXT', 'At the Back of Beyond', NULL, NULL, '2026-03-15 16:01:53'),
(794, 78, 'album_2_description', 'TEXT', 'At the Back of Beyond showcases Ntjam Rosie\'s soulful blend of jazz and Afro-European sounds.', NULL, NULL, '2026-03-15 16:01:53'),
(795, 78, 'album_2_year', 'TEXT', '2021', NULL, NULL, '2026-03-15 16:01:53'),
(796, 78, 'album_2_tag', 'TEXT', 'JAZZ', NULL, NULL, '2026-03-15 16:01:53'),
(797, 78, 'album_2_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamalbum2.png', NULL, NULL, '2026-03-15 16:01:53'),
(798, 78, 'album_3_title', 'TEXT', 'Family and Friends', NULL, NULL, '2026-03-15 16:01:53'),
(799, 78, 'album_3_description', 'TEXT', 'Family and Friends highlights Ntjam Rosie\'s warm vocals and her fusion of jazz and Afro-European influences.', NULL, NULL, '2026-03-15 16:01:53'),
(800, 78, 'album_3_year', 'TEXT', '2023', NULL, NULL, '2026-03-15 16:01:53'),
(801, 78, 'album_3_tag', 'TEXT', 'LIVE', NULL, NULL, '2026-03-15 16:01:53'),
(802, 78, 'album_3_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamalbum3.png', NULL, NULL, '2026-03-15 16:01:53'),
(803, 78, 'listen_heading', 'TEXT', 'LISTEN NOW', NULL, NULL, '2026-03-15 16:01:53'),
(804, 78, 'listen_subheading', 'TEXT', 'Important Tracks', NULL, NULL, '2026-03-15 16:01:53'),
(805, 78, 'listen_description', 'TEXT', 'Listen to excerpts from Ntjam Rosie\'s most celebrated and influential tracks. Immerse yourself in the soulful energy and refined musicianship that define her signature sound.', NULL, NULL, '2026-03-15 16:01:53'),
(806, 78, 'track_1_title', 'TEXT', 'What is Love?', NULL, NULL, '2026-03-15 16:01:53'),
(807, 78, 'track_1_album', 'TEXT', 'Live in the Quarter', NULL, NULL, '2026-03-15 16:01:53'),
(808, 78, 'track_1_description', 'TEXT', 'Classic New Orleans standard with powerful brass arrangements', NULL, NULL, '2026-03-15 16:01:53'),
(809, 78, 'track_1_duration', 'TEXT', '4:32', NULL, NULL, '2026-03-15 16:01:53'),
(810, 78, 'track_1_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamwhatislove.png', NULL, NULL, '2026-03-15 16:01:53'),
(811, 78, 'track_1_progress_class', 'TEXT', 'w-[5%]', NULL, NULL, '2026-03-15 16:01:53'),
(812, 78, 'track_2_title', 'TEXT', 'Thinkin About You', NULL, NULL, '2026-03-15 16:01:53'),
(813, 78, 'track_2_album', 'TEXT', 'Brass and Soul', NULL, NULL, '2026-03-15 16:01:53'),
(814, 78, 'track_2_description', 'TEXT', 'Original composition featuring traditional second-line rhythms', NULL, NULL, '2026-03-15 16:01:53'),
(815, 78, 'track_2_duration', 'TEXT', '3:45', NULL, NULL, '2026-03-15 16:01:53'),
(816, 78, 'track_2_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamthinkinaboutyou.png', NULL, NULL, '2026-03-15 16:01:53'),
(817, 78, 'track_2_progress_class', 'TEXT', 'w-[15%]', NULL, NULL, '2026-03-15 16:01:53'),
(818, 78, 'track_3_title', 'TEXT', 'You got this', NULL, NULL, '2026-03-15 16:01:53'),
(819, 78, 'track_3_album', 'TEXT', 'Big Easy Nights', NULL, NULL, '2026-03-15 16:01:53'),
(820, 78, 'track_3_description', 'TEXT', 'Fast-paced instrumental showcasing virtuoso musicianship', NULL, NULL, '2026-03-15 16:01:53'),
(821, 78, 'track_3_duration', 'TEXT', '4:18', NULL, NULL, '2026-03-15 16:01:53'),
(822, 78, 'track_3_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjamyougotthis.png', NULL, NULL, '2026-03-15 16:01:53'),
(823, 78, 'track_3_progress_class', 'TEXT', 'w-full', NULL, NULL, '2026-03-15 16:01:53'),
(824, 78, 'track_4_title', 'TEXT', 'In Need - Reworked', NULL, NULL, '2026-03-15 16:01:53'),
(825, 78, 'track_4_album', 'TEXT', 'Live in the Quarter', NULL, NULL, '2026-03-15 16:01:53'),
(826, 78, 'track_4_description', 'TEXT', 'High-energy rendition of the jazz funeral classic', NULL, NULL, '2026-03-15 16:01:53'),
(827, 78, 'track_4_duration', 'TEXT', '5:12', NULL, NULL, '2026-03-15 16:01:53'),
(828, 78, 'track_4_image', 'IMAGE_PATH', '/assets/Image/Jazz/Ntjaminneed.png', NULL, NULL, '2026-03-15 16:01:53'),
(829, 78, 'track_4_progress_class', 'TEXT', 'w-[60%]', NULL, NULL, '2026-03-15 16:01:53'),
(830, 78, 'live_cta_heading', 'TEXT', 'Experience Ntjam Rosie Live', NULL, NULL, '2026-03-15 16:01:53'),
(831, 78, 'live_cta_description', 'TEXT', 'Do not miss the chance to see Ntjam Rosie perform live at Haarlem Jazz 2026. With 2 performances scheduled, there are multiple opportunities to experience her incredible energy and musicianship.', NULL, NULL, '2026-03-15 16:01:53'),
(832, 78, 'performances_section_id', 'TEXT', 'artist-performances', NULL, NULL, '2026-03-15 16:15:19'),
(833, 78, 'performances_heading', 'TEXT', 'Ntjam Rosie at Haarlem Jazz 2026', NULL, NULL, '2026-03-15 16:01:53'),
(834, 78, 'performances_description', 'TEXT', 'Catch Ntjam Rosie performing during the Haarlem Jazz Festival. Each performance offers a unique experience from intimate indoor shows to free outdoor concerts.', NULL, NULL, '2026-03-15 16:01:53');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 77 — Gumbo Kings button labels (IDs 835–840)
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(835, 77, 'hero_back_button_text', 'TEXT', 'Back to Jazz', NULL, NULL, '2026-03-15 16:15:19'),
(836, 77, 'hero_reserve_button_text', 'TEXT', 'Reserve your spot', NULL, NULL, '2026-03-15 16:15:19'),
(837, 77, 'live_cta_book_button_text', 'TEXT', 'Book Tickets', NULL, NULL, '2026-03-15 16:15:19'),
(838, 77, 'live_cta_schedule_button_text', 'TEXT', 'View Full Schedule', NULL, NULL, '2026-03-15 16:15:19'),
(839, 77, 'listen_play_button_label', 'TEXT', 'Play excerpt', NULL, NULL, '2026-03-15 16:15:19'),
(840, 77, 'listen_play_excerpt_text', 'TEXT', 'Click to Play Excerpt', NULL, NULL, '2026-03-15 16:15:19');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 78 — Ntjam Rosie button labels (IDs 842–847)
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(842, 78, 'hero_back_button_text', 'TEXT', 'Back to Jazz', NULL, NULL, '2026-03-15 16:15:19'),
(843, 78, 'hero_reserve_button_text', 'TEXT', 'Reserve your spot', NULL, NULL, '2026-03-15 16:15:19'),
(844, 78, 'live_cta_book_button_text', 'TEXT', 'Book Tickets', NULL, NULL, '2026-03-15 16:15:19'),
(845, 78, 'live_cta_schedule_button_text', 'TEXT', 'View Full Schedule', NULL, NULL, '2026-03-15 16:15:19'),
(846, 78, 'listen_play_button_label', 'TEXT', 'Play excerpt', NULL, NULL, '2026-03-15 16:15:19'),
(847, 78, 'listen_play_excerpt_text', 'TEXT', 'Click to Play Excerpt', NULL, NULL, '2026-03-15 16:15:19');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 77 — Gumbo Kings URLs and alt text (IDs 849–851)
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(849, 77, 'hero_back_button_url', 'URL', '/jazz', NULL, NULL, '2026-03-16 14:06:12'),
(850, 77, 'live_cta_schedule_button_url', 'URL', '/jazz#jazz-schedule', NULL, NULL, '2026-03-16 14:06:12'),
(851, 77, 'listen_track_artwork_alt_suffix', 'TEXT', 'track artwork', NULL, NULL, '2026-03-16 14:06:12');

-- ---------------------------------------------------------------
-- CmsItem: CmsSectionId 78 — Ntjam Rosie URLs and alt text (IDs 852–854)
-- ---------------------------------------------------------------
INSERT IGNORE INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(852, 78, 'hero_back_button_url', 'URL', '/jazz', NULL, NULL, '2026-03-16 14:06:12'),
(853, 78, 'live_cta_schedule_button_url', 'URL', '/jazz#jazz-schedule', NULL, NULL, '2026-03-16 14:06:12'),
(854, 78, 'listen_track_artwork_alt_suffix', 'TEXT', 'track artwork', NULL, NULL, '2026-03-16 14:06:12');
