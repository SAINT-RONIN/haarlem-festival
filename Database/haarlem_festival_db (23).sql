-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Apr 06, 2026 at 11:42 AM
-- Server version: 12.0.2-MariaDB-ubu2404
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `haarlem_festival_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `Artist`
--

CREATE TABLE `Artist` (
  `ArtistId` int(11) NOT NULL,
  `Name` varchar(120) NOT NULL,
  `Style` varchar(120) NOT NULL DEFAULT '',
  `CardDescription` text NOT NULL DEFAULT '',
  `HeroSubtitle` varchar(200) NOT NULL DEFAULT '',
  `HeroImagePath` varchar(500) NOT NULL DEFAULT '',
  `OriginText` varchar(200) NOT NULL DEFAULT '',
  `FormedText` varchar(120) NOT NULL DEFAULT '',
  `BioHtml` text NOT NULL DEFAULT '<p></p>',
  `OverviewLead` text NOT NULL DEFAULT '',
  `OverviewBodySecondary` text NOT NULL DEFAULT '',
  `LineupHeading` varchar(120) NOT NULL DEFAULT '',
  `HighlightsHeading` varchar(120) NOT NULL DEFAULT '',
  `PhotoGalleryHeading` varchar(120) NOT NULL DEFAULT '',
  `PhotoGalleryDescription` text NOT NULL DEFAULT '',
  `AlbumsHeading` varchar(120) NOT NULL DEFAULT '',
  `AlbumsDescription` text NOT NULL DEFAULT '',
  `ListenHeading` varchar(120) NOT NULL DEFAULT '',
  `ListenSubheading` varchar(120) NOT NULL DEFAULT '',
  `ListenDescription` text NOT NULL DEFAULT '',
  `LiveCtaHeading` varchar(160) NOT NULL DEFAULT '',
  `LiveCtaDescription` text NOT NULL DEFAULT '',
  `PerformancesHeading` varchar(160) NOT NULL DEFAULT '',
  `PerformancesDescription` text NOT NULL DEFAULT '',
  `CardSortOrder` int(11) NOT NULL DEFAULT 0,
  `ShowOnJazzOverview` tinyint(1) NOT NULL DEFAULT 0,
  `ImageAssetId` int(11) DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `Artist`
--

INSERT INTO `Artist` (`ArtistId`, `Name`, `Style`, `CardDescription`, `HeroSubtitle`, `HeroImagePath`, `OriginText`, `FormedText`, `BioHtml`, `OverviewLead`, `OverviewBodySecondary`, `LineupHeading`, `HighlightsHeading`, `PhotoGalleryHeading`, `PhotoGalleryDescription`, `AlbumsHeading`, `AlbumsDescription`, `ListenHeading`, `ListenSubheading`, `ListenDescription`, `LiveCtaHeading`, `LiveCtaDescription`, `PerformancesHeading`, `PerformancesDescription`, `CardSortOrder`, `ShowOnJazzOverview`, `ImageAssetId`, `IsActive`, `CreatedAtUtc`) VALUES
(1, 'Gumbo Kings', 'Soul', 'High-energy New Orleans style jazz band bringing authentic Big Easy sound to Haarlem. Known for infectious rhythms.', 'New Orleans Jazz', '/assets/Image/Jazz/GubmboKings-Hero.png', 'Origin: New Orleans, Louisiana, USA', 'Formed: 2015', 'The Gumbo Kings deliver an electrifying blend of traditional New Orleans jazz, funk, and second-line grooves. With a powerful horn section, driving rhythm section, and authentic Crescent City soul, they transport audiences straight to the streets of the French Quarter. Their performances are known for spontaneous moments of musical magic and infectious energy that keeps crowds dancing all night long. Since their formation in 2015, they have become one of the most sought-after New Orleans jazz acts in Europe.', 'High-energy New Orleans style jazz band bringing authentic Big Easy sound to Haarlem. Known for their infectious rhythms and crowd-pleasing performances that get audiences on their feet.', 'Drawing inspiration from the rich musical heritage of New Orleans, the Gumbo Kings have mastered the art of combining traditional jazz elements with contemporary energy. Their repertoire spans classic jazz standards, original compositions, and reimagined funk grooves. The band has performed at major jazz festivals across Europe and the United States, earning acclaim for their authentic sound and dynamic stage presence. Their commitment to preserving and evolving the New Orleans jazz tradition has made them favorites among both purists and new jazz audiences.', 'Band Lineup', 'Career Highlights', 'Photo Gallery', 'Experience the energy and passion of Gumbo Kings through these performance and portrait photographs.', 'Featured Albums', 'Explore the studio recordings that capture the magic of Gumbo Kings. Each album showcases their evolution and mastery of the New Orleans jazz tradition.', 'LISTEN NOW', 'Important Tracks', 'Listen to excerpts from Gumbo Kings\'s most important and popular tracks. Experience the energy and musicianship that defines their sound.', 'Experience Gumbo Kings Live', 'Do not miss the chance to see Gumbo Kings perform live at Haarlem Jazz 2026. With 2 performances scheduled, there are multiple opportunities to experience their incredible energy and musicianship.', 'Gumbo Kings at Haarlem Jazz 2026', 'Catch Gumbo Kings performing during the Haarlem Jazz Festival. Each performance offers a unique experience from intimate indoor shows to free outdoor concerts.', 1, 1, 72, 1, '2026-02-06 15:30:32'),
(2, 'Evolve', 'Alternative', 'Progressive jazz ensemble pushing boundaries with innovative compositions. A fresh take on modern jazz traditions.', 'Live jazz performance by Evolve', '', '', '', '<p></p>', 'Live jazz performance by Evolve', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 2, 1, 73, 1, '2026-02-06 15:30:32'),
(3, 'Ntjam Rosie', 'Soul', 'Sultry vocals meet classic jazz standards. Rosie brings timeless elegance and powerful vocal performances to every show.', 'Vocal Jazz', '/assets/Image/Jazz/Ntjamhero.png', 'Origin: Cameroon / Netherlands', 'Formed: 2008', 'Her debut album, Atouba, released in 2008, was the first to showcase that hybrid style, mixing African rhythms with soul and jazz influences. Since then she has developed a distinctive musical voice across several albums and performances.', 'Ntjam Rosie was born in Cameroon on March 18, 1983, and moved to the Netherlands at the age of nine. She blends her West-African roots with Western musical traditions, combining jazz, soul, pop and Afro influences.', 'Ntjam Rosie has built a reputation for compelling live performances and wide appeal. She has performed at major festivals and toured internationally. Her music resonates with both jazz and soul audiences, and she continues to evolve by blending tradition and innovation.', 'Band Lineup', 'Career Highlights', 'Photo Gallery', 'Experience the soulful elegance of Ntjam Rosie through these intimate performance and portrait photographs.', 'Featured Albums', 'Explore the studio recordings that capture the soulful artistry of Ntjam Rosie. Each album reflects her evolving blend of jazz, soul, and Afro-inspired sound.', 'LISTEN NOW', 'Important Tracks', 'Listen to excerpts from Ntjam Rosie\'s most celebrated and influential tracks. Immerse yourself in the soulful energy and refined musicianship that define her signature sound.', 'Experience Ntjam Rosie Live', 'Do not miss the chance to see Ntjam Rosie perform live at Haarlem Jazz 2026. With 2 performances scheduled, there are multiple opportunities to experience her incredible energy and musicianship.', 'Ntjam Rosie at Haarlem Jazz 2026', 'Catch Ntjam Rosie performing during the Haarlem Jazz Festival. Each performance offers a unique experience from intimate indoor shows to free outdoor concerts.', 3, 1, 74, 1, '2026-02-06 15:30:32'),
(4, 'Wicked Jazz Sounds', 'Alternative', 'Live jazz performance by Wicked Jazz Sounds', 'Live jazz performance by Wicked Jazz Sounds', '', '', '', '<p></p>', 'Live jazz performance by Wicked Jazz Sounds', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(5, 'Wouter Hamel', 'Pop', 'Live jazz performance by Wouter Hamel', 'Live jazz performance by Wouter Hamel', '', '', '', '<p></p>', 'Live jazz performance by Wouter Hamel', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(6, 'Jonna Frazer', 'Soul', 'Live jazz performance by Jonna Frazer', 'Live jazz performance by Jonna Frazer', '', '', '', '<p></p>', 'Live jazz performance by Jonna Frazer', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(7, 'Karsu', 'Folk', 'Live jazz performance by Karsu', 'Live jazz performance by Karsu', '', '', '', '<p></p>', 'Live jazz performance by Karsu', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(8, 'Uncle Sue', 'Rock', 'Live jazz performance by Uncle Sue', 'Live jazz performance by Uncle Sue', '', '', '', '<p></p>', 'Live jazz performance by Uncle Sue', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(9, 'Chris Allen', 'Jazz', 'Live jazz performance by Chris Allen', 'Live jazz performance by Chris Allen', '', '', '', '<p></p>', 'Live jazz performance by Chris Allen', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(10, 'Myles Sanko', 'Jazz', 'Live jazz performance by Myles Sanko', 'Live jazz performance by Myles Sanko', '', '', '', '<p></p>', 'Live jazz performance by Myles Sanko', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(11, 'Ilse Huizinga', 'Jazz', 'Live jazz performance by Ilse Huizinga', 'Live jazz performance by Ilse Huizinga', '', '', '', '<p></p>', 'Live jazz performance by Ilse Huizinga', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(12, 'Eric Vloeimans and Hotspot!', 'Jazz', 'Live jazz performance by Eric Vloeimans and Hotspot!', 'Live jazz performance by Eric Vloeimans and Hotspot!', '', '', '', '<p></p>', 'Live jazz performance by Eric Vloeimans and Hotspot!', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(13, 'Gare du Nord', 'Jazz', 'Live jazz performance by Gare du Nord', 'Live jazz performance by Gare du Nord', '', '', '', '<p></p>', 'Live jazz performance by Gare du Nord', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(14, 'Rilan & The Bombadiers', 'Jazz', 'Live jazz performance by Rilan & The Bombadiers', 'Live jazz performance by Rilan & The Bombadiers', '', '', '', '<p></p>', 'Live jazz performance by Rilan & The Bombadiers', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(15, 'Soul Six', 'Jazz', 'Live jazz performance by Soul Six', 'Live jazz performance by Soul Six', '', '', '', '<p></p>', 'Live jazz performance by Soul Six', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(16, 'Han Bennink', 'Jazz', 'Live jazz performance by Han Bennink', 'Live jazz performance by Han Bennink', '', '', '', '<p></p>', 'Live jazz performance by Han Bennink', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(17, 'The Nordanians', 'Jazz', 'Live jazz performance by The Nordanians', 'Live jazz performance by The Nordanians', '', '', '', '<p></p>', 'Live jazz performance by The Nordanians', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(18, 'Lilith Merlot', 'Jazz', 'Live jazz performance by Lilith Merlot', 'Live jazz performance by Lilith Merlot', '', '', '', '<p></p>', 'Live jazz performance by Lilith Merlot', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(19, 'Ruis Soundsystem', 'Jazz', 'Live jazz performance by Ruis Soundsystem', 'Live jazz performance by Ruis Soundsystem', '', '', '', '<p></p>', 'Live jazz performance by Ruis Soundsystem', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(20, 'Nicky Romero', 'Dance/EDM', '', '', '', '', '', '<p></p>', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(22, 'Tiësto', 'Dance/EDM', '', '', '', '', '', '<p></p>', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(23, 'Hardwell', 'Dance/EDM', '', '', '', '', '', '<p></p>', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(24, 'Martin Garrix', 'Dance/EDM', '', '', '', '', '', '<p></p>', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 1, '2026-02-06 15:30:32'),
(25, 'Armin van Buuren', 'Dance/EDM', '', '', '', '', '', '<p></p>', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, NULL, 0, '2026-02-06 15:30:32'),
(29, 'Test', 'test style', 'this is just test', '', '', '', '', '<p></p>', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 4, 0, 4, 1, '2026-04-04 20:32:57');

-- --------------------------------------------------------

--
-- Table structure for table `ArtistAlbum`
--

CREATE TABLE `ArtistAlbum` (
  `ArtistAlbumId` int(11) NOT NULL,
  `ArtistId` int(11) NOT NULL,
  `Title` varchar(160) NOT NULL,
  `Description` text NOT NULL DEFAULT '',
  `Year` varchar(10) NOT NULL DEFAULT '',
  `Tag` varchar(30) NOT NULL DEFAULT '',
  `ImagePath` varchar(500) NOT NULL DEFAULT '',
  `SortOrder` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `ArtistAlbum`
--

INSERT INTO `ArtistAlbum` (`ArtistAlbumId`, `ArtistId`, `Title`, `Description`, `Year`, `Tag`, `ImagePath`, `SortOrder`) VALUES
(1, 1, 'Second Line Swing', 'Their breakthrough album featuring traditional second line rhythms mixed with contemporary jazz sensibilities. The title track became a festival favorite across Europe.', '2019', 'JAZZ', '/assets/Image/Jazz/GumboKingsAlbum1.png', 1),
(2, 1, 'Big Easy', 'A love letter to New Orleans featuring reimagined classics and original compositions inspired by the city\'s rich musical heritage.', '2021', 'JAZZ', '/assets/Image/Jazz/GumboKingsAlbum2.png', 2),
(3, 1, 'Live at Paradiso', 'Recorded live at Amsterdam\'s legendary Paradiso venue, this album captures the raw energy and spontaneity of their live performances.', '2023', 'LIVE', '/assets/Image/Jazz/GumboKingsAlbum3.png', 3),
(4, 3, 'Atouba', 'Her debut album where Ntjam Rosie introduced her Afro-European blend of soul and jazz.', '2019', 'JAZZ', '/assets/Image/Jazz/Ntjamalbum1.png', 1),
(5, 3, 'At the Back of Beyond', 'At the Back of Beyond showcases Ntjam Rosie\'s soulful blend of jazz and Afro-European sounds.', '2021', 'JAZZ', '/assets/Image/Jazz/Ntjamalbum2.png', 2),
(6, 3, 'Family and Friends', 'Family and Friends highlights Ntjam Rosie\'s warm vocals and her fusion of jazz and Afro-European influences.', '2023', 'LIVE', '/assets/Image/Jazz/Ntjamalbum3.png', 3);

-- --------------------------------------------------------

--
-- Table structure for table `ArtistGalleryImage`
--

CREATE TABLE `ArtistGalleryImage` (
  `ArtistGalleryImageId` int(11) NOT NULL,
  `ArtistId` int(11) NOT NULL,
  `ImagePath` varchar(500) NOT NULL,
  `SortOrder` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `ArtistGalleryImage`
--

INSERT INTO `ArtistGalleryImage` (`ArtistGalleryImageId`, `ArtistId`, `ImagePath`, `SortOrder`) VALUES
(1, 1, '/assets/Image/Jazz/GumboGallery1.png', 1),
(2, 1, '/assets/Image/Jazz/GumboGallery2.png', 2),
(3, 1, '/assets/Image/Jazz/GumboGallery3.png', 3),
(4, 3, '/assets/Image/Jazz/Ntjamgallery1.png', 1),
(5, 3, '/assets/Image/Jazz/Ntjamgallery2.png', 2),
(6, 3, '/assets/Image/Jazz/Ntjamgallery3.png', 3);

-- --------------------------------------------------------

--
-- Table structure for table `ArtistHighlight`
--

CREATE TABLE `ArtistHighlight` (
  `ArtistHighlightId` int(11) NOT NULL,
  `ArtistId` int(11) NOT NULL,
  `HighlightText` varchar(400) NOT NULL,
  `SortOrder` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `ArtistHighlight`
--

INSERT INTO `ArtistHighlight` (`ArtistHighlightId`, `ArtistId`, `HighlightText`, `SortOrder`) VALUES
(1, 1, 'Featured performers at New Orleans Jazz and Heritage Festival 2023', 1),
(2, 1, 'Touring Europe extensively since 2019, performing at 50+ major festivals', 2),
(3, 1, 'Authentic brass band sound with modern energy and innovation', 3),
(4, 1, 'Collaborations with legendary New Orleans musicians including Trombone Shorty', 4),
(5, 1, 'Known for interactive, high-energy live shows that get audiences dancing', 5),
(6, 1, 'Winner of Best Jazz Ensemble at European Jazz Awards 2022', 6),
(7, 3, 'Featured performer at the New Orleans Jazz and Heritage Festival 2023', 1),
(8, 3, 'Toured across Europe since 2019, playing 50+ major festivals', 2),
(9, 3, 'Blends Afro-European vocals with modern jazz and soul energy', 3),
(10, 3, 'Collaborated with New Orleans artists, including Trombone Shorty', 4),
(11, 3, 'Renowned for vibrant, high-energy live shows', 5),
(12, 3, 'Winner of Best Jazz Ensemble at the European Jazz Awards 2022', 6);

-- --------------------------------------------------------

--
-- Table structure for table `ArtistLineupMember`
--

CREATE TABLE `ArtistLineupMember` (
  `ArtistLineupMemberId` int(11) NOT NULL,
  `ArtistId` int(11) NOT NULL,
  `MemberText` varchar(200) NOT NULL,
  `SortOrder` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `ArtistLineupMember`
--

INSERT INTO `ArtistLineupMember` (`ArtistLineupMemberId`, `ArtistId`, `MemberText`, `SortOrder`) VALUES
(1, 1, 'Marcus Johnson - Trumpet, Band Leader', 1),
(2, 1, 'DeShawn Williams - Trombone', 2),
(3, 1, 'Antoine Davis - Tenor Saxophone', 3),
(4, 1, 'Jerome Baptiste - Drums', 4),
(5, 1, 'Louis Carter - Upright Bass', 5),
(6, 1, 'Raymond Pierce - Piano', 6),
(7, 3, 'Ntjam Rosie - Vocals', 1),
(8, 3, 'Bart Wirtz - Tenor Saxophone', 2),
(9, 3, 'Niels Broos - Piano and Keys', 3),
(10, 3, 'Bram Hakkens - Drums', 4),
(11, 3, 'Tijn Wybenga - Bass', 5);

-- --------------------------------------------------------

--
-- Table structure for table `ArtistTrack`
--

CREATE TABLE `ArtistTrack` (
  `ArtistTrackId` int(11) NOT NULL,
  `ArtistId` int(11) NOT NULL,
  `Title` varchar(160) NOT NULL,
  `Album` varchar(160) NOT NULL DEFAULT '',
  `Description` text NOT NULL DEFAULT '',
  `Duration` varchar(10) NOT NULL DEFAULT '',
  `ImagePath` varchar(500) NOT NULL DEFAULT '',
  `ProgressClass` varchar(30) NOT NULL DEFAULT '',
  `SortOrder` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `ArtistTrack`
--

INSERT INTO `ArtistTrack` (`ArtistTrackId`, `ArtistId`, `Title`, `Album`, `Description`, `Duration`, `ImagePath`, `ProgressClass`, `SortOrder`) VALUES
(1, 1, 'All Night Long', 'Live in the Quarter', 'Classic New Orleans standard with powerful brass arrangements', '4:32', '/assets/Image/Jazz/Allnightlong.png', 'w-[5%]', 1),
(2, 1, 'Hot Damn!', 'Brass and Soul', 'Original composition featuring traditional second-line rhythms', '3:45', '/assets/Image/Jazz/Container.png', 'w-[15%]', 2),
(3, 1, 'Valenzuela', 'Big Easy Nights', 'Fast-paced instrumental showcasing virtuoso musicianship', '4:18', '/assets/Image/Jazz/Listennowsection.png', 'w-full', 3),
(4, 1, 'Here We Are', 'Live in the Quarter', 'High-energy rendition of the jazz funeral classic', '5:12', '/assets/Image/Jazz/Allnightlong.png', 'w-[60%]', 4),
(5, 3, 'What is Love?', 'Live in the Quarter', 'Classic New Orleans standard with powerful brass arrangements', '4:32', '/assets/Image/Jazz/Ntjamwhatislove.png', 'w-[5%]', 1),
(6, 3, 'Thinkin About You', 'Brass and Soul', 'Original composition featuring traditional second-line rhythms', '3:45', '/assets/Image/Jazz/Ntjamthinkinaboutyou.png', 'w-[15%]', 2),
(7, 3, 'You got this', 'Big Easy Nights', 'Fast-paced instrumental showcasing virtuoso musicianship', '4:18', '/assets/Image/Jazz/Ntjamyougotthis.png', 'w-full', 3),
(8, 3, 'In Need - Reworked', 'Live in the Quarter', 'High-energy rendition of the jazz funeral classic', '5:12', '/assets/Image/Jazz/Ntjaminneed.png', 'w-[60%]', 4);

-- --------------------------------------------------------

--
-- Table structure for table `CmsItem`
--

CREATE TABLE `CmsItem` (
  `CmsItemId` int(11) NOT NULL,
  `CmsSectionId` int(11) NOT NULL,
  `ItemKey` varchar(80) NOT NULL,
  `ItemType` varchar(20) NOT NULL,
  `TextValue` text DEFAULT NULL,
  `HtmlValue` text DEFAULT NULL,
  `MediaAssetId` int(11) DEFAULT NULL,
  `UpdatedAtUtc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `CmsItem`
--

INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1, 1, 'hero_main_title', 'HEADING', 'Haarlem Festivals', NULL, NULL, '2026-04-01 19:09:48'),
(2, 1, 'hero_subtitle', 'TEXT', 'Four July Days Bringing People Together', NULL, NULL, '2026-02-06 14:13:59'),
(3, 1, 'hero_button_primary', 'BUTTON_TEXT', 'Discover all types of events', NULL, NULL, '2026-02-06 14:13:59'),
(4, 1, 'hero_button_secondary', 'BUTTON_TEXT', 'Events schedule', NULL, NULL, '2026-02-06 14:13:59'),
(5, 2, 'banner_main_title', 'HEADING', 'Explore all upcoming events', NULL, NULL, '2026-02-06 14:13:59'),
(6, 2, 'banner_subtitle', 'TEXT', 'See every theme and activity happening during the festival weekend.', NULL, NULL, '2026-02-06 14:13:59'),
(7, 3, 'about_main_title', 'HEADING', 'What is Haarlem Festival?', NULL, NULL, '2026-02-06 14:13:59'),
(8, 3, 'about_tagline', 'TEXT', 'A celebration of culture and creativity', NULL, NULL, '2026-02-06 14:13:59'),
(9, 3, 'about_description', 'HTML', NULL, '<p>Haarlem Festival is a four-day celebration that brings together the city\'s most inspiring music, stories, food, and cultural experiences. During the last weekend of July, Haarlem transforms into a lively meeting place where residents and visitors explore jazz performances, storytelling sessions, gourmet specials, historic routes, children\'s adventures at Teylers, and energetic DJ sets.</p>\r\n<p>The festival highlights the diversity and creativity of Haarlem, offering something for every age group and interest. Whether you want to discover new artists, enjoy local flavors, learn about the city\'s history, or simply experience Haarlem in a new way, the festival invites you to take part and enjoy the atmosphere.</p>', NULL, '2026-02-10 15:04:31'),
(10, 3, 'about_button', 'BUTTON_TEXT', 'Events schedule', NULL, NULL, '2026-02-06 14:13:59'),
(11, 4, 'events_main_title', 'HEADING', 'Explore Our Events', NULL, NULL, '2026-02-06 14:13:59'),
(12, 4, 'events_subtitle', 'TEXT', 'Discover what\'s happening each day', NULL, NULL, '2026-02-06 14:13:59'),
(13, 5, 'jazz_title', 'HEADING', 'Jazz', NULL, NULL, '2026-02-06 14:13:59'),
(14, 5, 'jazz_description', 'TEXT', 'Experience world-class jazz performances at Haarlem\'s most iconic venues, where every note feels close, alive, and full of character. From intimate late night sessions to grand concerts featuring internationally acclaimed artists, the festival offers something for every jazz lover, whether you are discovering the genre or already know what you like. Expect smooth melodies, bold improvisation, and those rare moments when the whole room holds its breath together.', NULL, NULL, '2026-02-06 14:13:59'),
(15, 5, 'jazz_button', 'BUTTON_TEXT', 'Explore Jazz Events', NULL, NULL, '2026-02-06 14:13:59'),
(16, 6, 'dance_title', 'HEADING', 'Dance', NULL, NULL, '2026-02-06 14:13:59'),
(17, 6, 'dance_description', 'TEXT', 'Feel the rhythm at our dance events with electronic beats, live DJs, and vibrant club nights across multiple venues throughout the city. From pulsing dance floors to immersive light and sound, each night is designed to build energy, spark connection, and keep you moving. Come with friends or show up solo, you will be surrounded by people who are there for the same reason: great music and a real night out. Lose yourself in the vibe, discover new DJs, and celebrate Haarlem until the early hours.', NULL, NULL, '2026-02-06 14:13:59'),
(18, 6, 'dance_button', 'BUTTON_TEXT', 'Explore Dance Events', NULL, NULL, '2026-02-06 14:13:59'),
(19, 7, 'history_title', 'HEADING', 'History', NULL, NULL, '2026-02-06 14:13:59'),
(20, 7, 'history_description', 'TEXT', 'Discover Haarlem\'s rich heritage through guided walking tours, museum visits, and historical storytelling that brings the past to life. Walk in the footsteps of centuries as expert guides reveal hidden details, iconic landmarks, and the surprising moments that shaped this remarkable city. Whether you are new to Haarlem or think you already know it, you will see familiar streets with fresh eyes and leave with stories worth sharing.', NULL, NULL, '2026-02-06 14:13:59'),
(21, 7, 'history_button', 'BUTTON_TEXT', 'Explore History Events', NULL, NULL, '2026-02-06 14:13:59'),
(22, 8, 'restaurant_title', 'HEADING', 'Restaurants', NULL, NULL, '2026-02-06 14:13:59'),
(23, 8, 'restaurant_description', 'TEXT', 'Savor exceptional cuisine at Haarlem\'s finest restaurants and discover why the city is becoming a true food destination. Enjoy exclusive dining experiences, special chef collaborations, and hands-on culinary workshops designed for both curious beginners and passionate food lovers. From locally sourced ingredients to bold international inspiration, every dish is crafted to surprise and delight. Bring your appetite, treat yourself, and join fellow food lovers for unforgettable flavors, great company, and a festival experience you will be talking about long after the last bite.', NULL, NULL, '2026-02-06 14:13:59'),
(24, 8, 'restaurant_button', 'BUTTON_TEXT', 'Explore Restaurant Events', NULL, NULL, '2026-02-06 14:13:59'),
(25, 9, 'storytelling_title', 'HEADING', 'Storytelling', NULL, NULL, '2026-02-06 14:13:59'),
(26, 9, 'storytelling_description', 'TEXT', 'Immerse yourself in captivating narratives from local storytellers as they bring Haarlem\'s culture, legends, and present day life to vivid reality. Step into intimate festival settings where the art of oral tradition comes alive, and every story feels personal, surprising, and unforgettable. Come with friends or meet new people as you listen, laugh, and discover Haarlem through voices that know it best.', NULL, NULL, '2026-02-06 14:13:59'),
(27, 9, 'storytelling_button', 'BUTTON_TEXT', 'Explore Storytelling Events', NULL, NULL, '2026-02-06 14:13:59'),
(28, 10, 'venue_main_title', 'HEADING', 'Overview of event locations', NULL, NULL, '2026-02-06 14:13:59'),
(29, 10, 'venue_filter_label', 'TEXT', 'Showing all location', NULL, NULL, '2026-02-06 14:13:59'),
(30, 10, 'venue_filter_title', 'TEXT', 'Filter event:', NULL, NULL, '2026-02-06 14:13:59'),
(31, 10, 'venue_filter_all', 'BUTTON_TEXT', 'All', NULL, NULL, '2026-02-06 14:13:59'),
(32, 10, 'venue_filter_jazz', 'BUTTON_TEXT', 'Jazz', NULL, NULL, '2026-02-06 14:13:59'),
(33, 10, 'venue_filter_dance', 'BUTTON_TEXT', 'Dance', NULL, NULL, '2026-02-06 14:13:59'),
(34, 10, 'venue_filter_history', 'BUTTON_TEXT', 'History', NULL, NULL, '2026-02-06 14:13:59'),
(35, 10, 'venue_filter_restaurants', 'BUTTON_TEXT', 'Restaurants', NULL, NULL, '2026-02-06 14:13:59'),
(36, 10, 'venue_filter_stories', 'BUTTON_TEXT', 'Stories', NULL, NULL, '2026-02-06 14:13:59'),
(37, 11, 'schedule_main_title', 'HEADING', 'Events schedule', NULL, NULL, '2026-02-06 14:13:59'),
(38, 11, 'schedule_subtitle_1', 'TEXT', 'Discover what\'s happening each day', NULL, NULL, '2026-02-06 14:13:59'),
(39, 11, 'schedule_subtitle_2', 'TEXT', 'We invite you to join us for music, stories, tour, and great food.', NULL, NULL, '2026-02-06 14:13:59'),
(40, 12, 'site_name', 'TEXT', 'Haarlem Festival', NULL, NULL, '2026-02-06 19:16:03'),
(41, 12, 'nav_home', 'TEXT', 'Home', NULL, NULL, '2026-02-06 19:16:03'),
(42, 12, 'nav_jazz', 'TEXT', 'Jazz', NULL, NULL, '2026-02-06 19:16:03'),
(43, 12, 'nav_dance', 'TEXT', 'Dance', NULL, NULL, '2026-02-06 19:16:03'),
(44, 12, 'nav_history', 'TEXT', 'History', NULL, NULL, '2026-02-06 19:16:03'),
(45, 12, 'nav_restaurant', 'TEXT', 'Restaurant', NULL, NULL, '2026-02-06 19:16:03'),
(46, 12, 'nav_storytelling', 'TEXT', 'Storytelling', NULL, NULL, '2026-02-06 19:16:03'),
(47, 12, 'btn_my_program', 'BUTTON_TEXT', 'My Program', NULL, NULL, '2026-02-06 19:16:03'),
(48, 12, 'label_events_count', 'TEXT', 'events', NULL, NULL, '2026-03-14 16:25:32'),
(49, 12, 'label_no_events', 'TEXT', 'No events scheduled', NULL, NULL, '2026-03-14 16:25:32'),
(50, 12, 'btn_explore_template', 'TEXT', 'Explore {title} Events', NULL, NULL, '2026-03-14 16:25:32'),
(51, 1, 'hero_button_primary_link', 'LINK', '#events', NULL, NULL, '2026-02-08 12:17:16'),
(52, 1, 'hero_button_secondary_link', 'LINK', '#schedule', NULL, NULL, '2026-02-08 12:17:16'),
(53, 1, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/HeroImageHome.png', NULL, NULL, '2026-02-08 12:17:16'),
(54, 13, 'hero_main_title', 'HEADING', 'Stories in Haarlem hello', NULL, NULL, '2026-03-23 19:59:39'),
(55, 13, 'hero_subtitle', 'TEXT', 'Discover the art of storytelling in Haarlem', NULL, NULL, '2026-02-15 21:04:32'),
(56, 13, 'hero_button_primary', 'BUTTON_TEXT', 'Discover storytelling events', NULL, NULL, '2026-02-08 12:17:16'),
(57, 13, 'hero_button_primary_link', 'LINK', '#events', NULL, NULL, '2026-02-08 12:17:16'),
(58, 13, 'hero_button_secondary', 'BUTTON_TEXT', 'View schedule', NULL, NULL, '2026-02-08 12:17:16'),
(59, 13, 'hero_button_secondary_link', 'LINK', '#schedule', NULL, NULL, '2026-02-08 12:17:16'),
(60, 13, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/storytelling/hero-storytelling.jpg', NULL, 116, '2026-03-25 13:04:09'),
(61, 14, 'gradient_heading', 'HEADING', 'Every story carries emotion, intention, and connection beyond what we say aloud.', NULL, NULL, '2026-02-08 12:17:16'),
(62, 14, 'gradient_subheading', 'TEXT', 'The world where stories are not just told but truly experienced.', NULL, NULL, '2026-02-08 14:17:39'),
(63, 15, 'intro_heading', 'HEADING', 'Where stories come alive in Haarlem', NULL, NULL, '2026-02-08 12:17:16'),
(64, 15, 'intro_body', 'TEXT', 'Stories in Haarlem is a new part of The Festival that is quite exciting. It brings together people who tell stories to kids, create live podcasts, run businesses in the local circular economy, and historical voices from the Corrie Ten Boom home. Visitors may hear tales in Dutch and English that are made for various age groups and shared in different places across the city.   Each story has been carefully selected and prepared with great effort to offer meaningful, inspiring, and memorable experiences. The event aims to spark imagination, support local creators, and help visitors connect with Haarlem in a new and meaningful way. To make the event open to everyone, certain sessions are pay-as-you-like. Guests may also give any amount they choose to help the storytellers and the organizations they support. People that participate not only appreciate the tales, but they also help make this event happen.', NULL, NULL, '2026-02-15 21:04:17'),
(65, 15, 'intro_image', 'IMAGE_PATH', '/assets/Image/storytelling/where-stories-come-alive.jpg', NULL, NULL, '2026-02-08 14:29:20'),
(66, 16, 'masonry_heading', 'HEADING', 'Moments you are about to discover', NULL, NULL, '2026-02-08 12:17:16'),
(94, 2, 'banner_background_image', 'MEDIA', NULL, NULL, 2, '2026-02-08 13:54:33'),
(95, 3, 'about_image', 'MEDIA', NULL, NULL, 3, '2026-02-08 13:54:33'),
(96, 5, 'jazz_image', 'MEDIA', NULL, NULL, 4, '2026-02-08 13:54:33'),
(97, 6, 'dance_image', 'MEDIA', NULL, NULL, 5, '2026-02-08 13:54:33'),
(98, 7, 'history_image', 'MEDIA', NULL, NULL, 6, '2026-02-08 13:54:33'),
(99, 8, 'restaurant_image', 'MEDIA', NULL, NULL, 7, '2026-02-08 13:54:33'),
(100, 9, 'storytelling_image', 'MEDIA', NULL, NULL, 8, '2026-02-08 13:54:33'),
(113, 14, 'gradient_background_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-02-08 14:32:21'),
(114, 15, 'intro_image_alt', 'TEXT', 'Stories in Haarlem', NULL, NULL, '2026-02-08 14:34:54'),
(115, 28, 'hero_main_title', 'HEADING', 'HAARLEM JAZZ', NULL, NULL, '2026-04-04 19:04:58'),
(116, 28, 'hero_subtitle', 'TEXT', 'Experience world-class jazz performances at Haarlem\'s premier music festival. Discover our complete lineup, detailed schedules, and venue information.', NULL, NULL, '2026-02-10 14:12:56'),
(117, 28, 'hero_button_primary', 'BUTTON_TEXT', 'Discover all performances', NULL, NULL, '2026-02-10 12:02:48'),
(118, 28, 'hero_button_primary_link', 'LINK', '#artists', NULL, NULL, '2026-02-10 12:02:48'),
(119, 28, 'hero_button_secondary', 'BUTTON_TEXT', 'What is Haarlem Jazz?', NULL, NULL, '2026-02-10 12:02:48'),
(120, 28, 'hero_button_secondary_link', 'LINK', '#intro', NULL, NULL, '2026-02-10 12:02:48'),
(121, 29, 'gradient_heading', 'HEADING', 'Every note carries emotion, intention, and connection beyond what is heard.', NULL, NULL, '2026-02-10 12:02:48'),
(122, 29, 'gradient_subheading', 'TEXT', 'A place where jazz is experienced, not just played.', NULL, NULL, '2026-02-10 13:40:15'),
(123, 30, 'intro_heading', 'HEADING', 'Haarlem moves to the rhythm of jazz', NULL, NULL, '2026-02-10 12:02:48'),
(124, 30, 'intro_body', 'HTML', NULL, '<p>Welcome to Haarlem Jazz 2026, taking place during the last weekend of July. This year\'s festival features an outstanding lineup of international and local jazz artists performing across multiple venues in Haarlem\'s historic city center. From intimate club settings to free outdoor performances, our program offers something for every jazz enthusiast.</p>\r\n<p>The festival runs from Thursday through Sunday, with paid indoor performances at the Patronaat venue (featuring Main Hall, Second Hall, and Third Hall) and free outdoor concerts on Sunday at the iconic Grote Markt. All-access day passes and multi-day passes are available, offering excellent value for festival-goers wanting to experience multiple performances.</p>\r\n<p>Below you will find detailed information about our featured artists, complete performance schedules with exact times and venues, seating capacity for each show, ticket pricing, and booking information. We recommend reviewing the schedule carefully and booking early as many performances have limited seating.&nbsp;</p>', NULL, '2026-03-16 16:55:13'),
(125, 31, 'venues_heading', 'HEADING', 'Festival venues', NULL, NULL, '2026-02-10 12:02:48'),
(126, 31, 'venues_subheading', 'TEXT', 'Performance Locations', NULL, NULL, '2026-02-10 13:40:15'),
(127, 31, 'venues_description', 'TEXT', 'Haarlem Jazz 2026 takes place at two main locations in the city center. The Patronaat offers three different halls for intimate indoor performances, while the Grote Markt hosts free outdoor concerts on Sunday for all visitors.', NULL, NULL, '2026-02-10 14:12:56'),
(128, 31, 'venue_patronaat_name', 'TEXT', 'Patronaat', NULL, NULL, '2026-02-10 13:40:15'),
(129, 31, 'venue_patronaat_address1', 'TEXT', 'Zijlsingel 2', NULL, NULL, '2026-02-10 13:40:15'),
(130, 31, 'venue_patronaat_address2', 'TEXT', '2013 DN Haarlem', NULL, NULL, '2026-02-10 13:40:15'),
(131, 31, 'venue_patronaat_contact', 'TEXT', 'E-mail/reception available', NULL, NULL, '2026-02-10 13:40:15'),
(132, 31, 'venue_patronaat_hall1_name', 'TEXT', 'Main Hall', NULL, NULL, '2026-04-04 19:13:25'),
(133, 31, 'venue_patronaat_hall1_desc', 'TEXT', 'Headliner performances - €15.00 per show', NULL, NULL, '2026-04-04 19:13:25'),
(134, 31, 'venue_patronaat_hall1_capacity', 'TEXT', '300 seats', NULL, NULL, '2026-04-04 19:13:25'),
(135, 31, 'venue_patronaat_hall2_name', 'TEXT', 'Second Hall', NULL, NULL, '2026-02-10 13:40:15'),
(136, 31, 'venue_patronaat_hall2_desc', 'TEXT', 'Intimate performances - €10.00 per show', NULL, NULL, '2026-02-10 14:13:58'),
(137, 31, 'venue_patronaat_hall2_capacity', 'TEXT', '200 seats', NULL, NULL, '2026-04-04 19:13:25'),
(138, 31, 'venue_patronaat_hall3_name', 'TEXT', 'Third Hall', NULL, NULL, '2026-02-10 13:40:15'),
(139, 31, 'venue_patronaat_hall3_desc', 'TEXT', 'Intimate performances - €10.00 per show', NULL, NULL, '2026-02-10 14:13:58'),
(140, 31, 'venue_patronaat_hall3_capacity', 'TEXT', '150 seats', NULL, NULL, '2026-02-10 13:40:15'),
(141, 31, 'venue_grotemarkt_name', 'TEXT', 'Grote Markt', NULL, NULL, '2026-02-10 13:40:15'),
(142, 31, 'venue_grotemarkt_location1', 'TEXT', 'Historic Market Square', NULL, NULL, '2026-02-10 13:40:15'),
(143, 31, 'venue_grotemarkt_location2', 'TEXT', 'Haarlem City Center', NULL, NULL, '2026-02-10 13:40:15'),
(144, 31, 'venue_grotemarkt_hall_name', 'TEXT', 'Outdoor Stage', NULL, NULL, '2026-04-04 19:13:25'),
(145, 31, 'venue_grotemarkt_hall_desc', 'TEXT', 'Sunday performances are free for all visitors. No reservation needed.', NULL, NULL, '2026-02-10 13:40:15'),
(146, 31, 'venue_grotemarkt_hall_info', 'TEXT', 'The Grote Markt outdoor performances provide a wonderful opportunity to experience jazz in Haarlem\'s beautiful historic market square. Bring your family and friends for a free afternoon and evening of world-class music in the heart of the city.', NULL, NULL, '2026-02-10 14:12:56'),
(147, 31, 'venue_grotemarkt_hall_price', 'TEXT', 'FREE ENTRY', NULL, NULL, '2026-02-10 13:40:15'),
(148, 32, 'pricing_heading', 'HEADING', 'Pricing information', NULL, NULL, '2026-02-10 12:02:48'),
(149, 32, 'pricing_subheading', 'TEXT', 'Tickets & Passes', NULL, NULL, '2026-02-10 14:13:58'),
(150, 32, 'pricing_description', 'TEXT', 'We offer flexible ticketing options including individual show tickets and money-saving all-access passes. All-access passes provide unlimited entry to performances in all halls for the selected day(s).', NULL, NULL, '2026-02-10 14:12:56'),
(151, 32, 'pricing_individual_title', 'TEXT', 'Individual Show Tickets', NULL, NULL, '2026-02-10 13:40:15'),
(152, 32, 'pricing_individual_item1', 'TEXT', 'Main Hall Shows - €15.00 - 300 seats available per show', NULL, NULL, '2026-02-10 14:13:58'),
(153, 32, 'pricing_individual_item2', 'TEXT', 'Second Hall Shows - €10.00 - 200 seats available per show', NULL, NULL, '2026-02-10 14:13:58'),
(154, 32, 'pricing_individual_item3', 'TEXT', 'Third Hall Shows - €10.00 - 150 seats available per show', NULL, NULL, '2026-02-10 14:13:58'),
(155, 32, 'pricing_daypass_title', 'TEXT', 'All-Access Day Pass', NULL, NULL, '2026-02-10 13:40:15'),
(156, 32, 'pricing_daypass_price', 'TEXT', '€35.00', NULL, NULL, '2026-02-10 14:13:58'),
(157, 32, 'pricing_daypass_desc', 'TEXT', 'Per day', NULL, NULL, '2026-02-10 13:40:15'),
(158, 32, 'pricing_daypass_include1', 'TEXT', 'Unlimited access to all halls', NULL, NULL, '2026-02-10 13:40:15'),
(159, 32, 'pricing_daypass_include2', 'TEXT', 'All performances on selected day', NULL, NULL, '2026-02-10 13:40:15'),
(160, 32, 'pricing_daypass_include3', 'TEXT', 'Thursday, Friday, or Saturday', NULL, NULL, '2026-02-10 13:40:15'),
(161, 32, 'pricing_daypass_include4', 'TEXT', 'Best value for multiple shows', NULL, NULL, '2026-02-10 13:40:15'),
(162, 32, 'pricing_daypass_info', 'TEXT', 'All-Access pass for this day: €35.00. Valid for unlimited entry to Main Hall, Second Hall, and Third Hall performances on the selected day.', NULL, NULL, '2026-04-04 19:13:25'),
(163, 32, 'pricing_3day_title', 'TEXT', 'All-Access 3-Day Pass', NULL, NULL, '2026-04-04 19:13:25'),
(164, 32, 'pricing_3day_price', 'TEXT', '€80.00', NULL, NULL, '2026-02-10 14:13:58'),
(165, 32, 'pricing_3day_desc', 'TEXT', 'Thursday + Friday + Saturday', NULL, NULL, '2026-02-10 13:40:15'),
(166, 32, 'pricing_3day_include1', 'TEXT', 'Unlimited access all 3 days', NULL, NULL, '2026-02-10 13:40:15'),
(167, 32, 'pricing_3day_include2', 'TEXT', 'All venues and halls', NULL, NULL, '2026-02-10 13:40:15'),
(168, 32, 'pricing_3day_include3', 'TEXT', '18+ performances included', NULL, NULL, '2026-02-10 13:40:15'),
(169, 32, 'pricing_3day_include4', 'TEXT', 'Save €25 vs. day passes', NULL, NULL, '2026-02-10 14:13:58'),
(170, 32, 'pricing_3day_info', 'TEXT', 'All-Access pass for Thu, Fri, Sat: €80.00. Complete festival access for three full days of jazz performances.', NULL, NULL, '2026-02-10 14:13:58'),
(171, 33, 'schedule_cta_heading', 'HEADING', 'Ready to Plan Your Festival Experience?', NULL, NULL, '2026-02-10 12:02:48'),
(172, 33, 'schedule_cta_description', 'TEXT', 'Now that you\'ve explored our artists, check out the complete performance schedule below to see exact times, venues, and ticket availability. You can filter by day to plan your perfect jazz weekend.', NULL, NULL, '2026-04-04 19:05:27'),
(173, 33, 'schedule_cta_button', 'BUTTON_TEXT', 'View complete schedule', NULL, NULL, '2026-02-10 12:02:48'),
(174, 33, 'schedule_cta_button_link', 'LINK', '#jazz-schedule', NULL, NULL, '2026-04-04 19:13:25'),
(175, 34, 'artists_heading', 'HEADING', 'Discover our lineup', NULL, NULL, '2026-02-10 12:02:48'),
(176, 35, 'booking_cta_heading', 'HEADING', 'Book Your Experience', NULL, NULL, '2026-02-10 12:02:48'),
(177, 35, 'booking_cta_description', 'TEXT', 'Secure your tickets now for the last weekend of July. With limited seating at Patronaat and free performances at Grote Markt, there\'s an option for every jazz lover. Don\'t miss out on this year\'s incredible lineup.', NULL, NULL, '2026-02-10 14:12:56'),
(178, 28, 'jazz_hero_background_image', 'MEDIA', '/assets/Image/Jazz/Jazz-hero.png', NULL, 66, '2026-03-23 09:01:47'),
(179, 29, 'jazz_gradient_background_image', 'MEDIA', '/assets/Image/Jazz/Jazz-second-section.png', NULL, 67, '2026-03-23 09:01:47'),
(180, 30, 'jazz_intro_image', 'MEDIA', '/assets/Image/Jazz/Jazz-third-section.png', NULL, 68, '2026-03-23 09:01:47'),
(202, 36, 'schedule_title', 'HEADING', 'Storytelling schedule', NULL, NULL, '2026-02-10 17:17:49'),
(203, 36, 'schedule_year', 'TEXT', '2026', NULL, NULL, '2026-02-10 17:17:49'),
(204, 36, 'schedule_filters_button_text', 'BUTTON_TEXT', 'Filters', NULL, NULL, '2026-02-10 17:17:49'),
(205, 36, 'schedule_additional_info_title', 'TEXT', 'Additional Information:', NULL, NULL, '2026-02-10 17:17:49'),
(206, 36, 'schedule_additional_info_body', 'HTML', NULL, '<p>Some storytelling events are pay-what-you-like, meaning they are free to attend, with optional contributions that support the actor.<br>After reserving your spot, visit your My Program page to choose how much you would like to contribute to each pay-what-you-like event.</p>', NULL, '2026-03-23 19:59:39'),
(207, 36, 'schedule_show_filters', 'TEXT', '1', NULL, NULL, '2026-02-10 17:17:49'),
(208, 36, 'schedule_show_additional_info', 'TEXT', '1', NULL, NULL, '2026-02-10 17:17:49'),
(209, 36, 'schedule_show_story_count', 'TEXT', '1', NULL, NULL, '2026-02-10 17:17:49'),
(210, 36, 'schedule_story_count_label', 'TEXT', 'Stories', NULL, NULL, '2026-02-10 17:17:49'),
(211, 36, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Discover', NULL, NULL, '2026-03-19 12:39:55'),
(212, 36, 'schedule_pay_what_you_like_text', 'TEXT', 'Pay as you like', NULL, NULL, '2026-02-10 17:17:49'),
(213, 36, 'schedule_currency_symbol', 'TEXT', '€', NULL, NULL, '2026-02-10 17:17:49'),
(214, 37, 'schedule_title', 'HEADING', 'Jazz schedule', NULL, NULL, '2026-02-10 20:36:52'),
(215, 37, 'schedule_year', 'TEXT', '2026', NULL, NULL, '2026-02-10 20:36:52'),
(216, 37, 'schedule_filters_button_text', 'BUTTON_TEXT', 'Filters', NULL, NULL, '2026-02-10 20:36:52'),
(217, 37, 'schedule_show_filters', 'TEXT', '1', NULL, NULL, '2026-02-10 20:36:52'),
(218, 37, 'schedule_show_additional_info', 'TEXT', '0', NULL, NULL, '2026-02-10 20:36:52'),
(219, 37, 'schedule_show_event_count', 'TEXT', '1', NULL, NULL, '2026-02-10 20:36:52'),
(220, 37, 'schedule_event_count_label', 'TEXT', 'Performances', NULL, NULL, '2026-02-10 20:36:52'),
(221, 37, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Book Now', NULL, NULL, '2026-02-10 20:36:52'),
(222, 37, 'schedule_pay_what_you_like_text', 'TEXT', 'Pay as you like', NULL, NULL, '2026-02-10 20:36:52'),
(223, 37, 'schedule_currency_symbol', 'TEXT', '€', NULL, NULL, '2026-02-10 20:36:52'),
(224, 36, 'schedule_no_events_text', 'TEXT', 'No events scheduled', NULL, NULL, '2026-02-10 21:50:11'),
(225, 40, 'intro_body', 'TEXT', 'A Stroll Through History invites visitors to explore rich past of Haarlem on foot. Guided tour leads participants through historic streets and landmarks, including locations that played an important role in the city\'s cultural, social, and architectural development. The walks are offered in Dutch, English, and Chinese and are suitable for a wide audience. The route has been carefully curated and prepared by local historians and guides to ensure an engaging, informative, and memorable experience. By combining historical facts with stories from the past, the event helps visitors better understand how Haarlem grew into the city it is today. Multiple time slots are available throughout the festival, with different ticket options to keep the event accessible for individuals and families. By joining A Stroll Through History, visitors not only discover Haarlem\'s landmarks but also connect with the city through the people, places, and moments that shaped it.', NULL, NULL, '2026-02-16 17:53:02'),
(226, 42, 'hero_main_title', 'HEADING', 'A STROLL THROUGH HISTORY', NULL, NULL, '2026-02-16 12:19:16'),
(227, 42, 'hero_button_primary', 'BUTTON_TEXT', 'Explore the tour', NULL, NULL, '2026-02-16 12:19:16'),
(228, 42, 'hero_button_primary_link', 'LINK', '#route', NULL, NULL, '2026-03-09 23:06:39'),
(229, 42, 'hero_button_secondary', 'BUTTON_TEXT', 'Get tickets', NULL, NULL, '2026-02-16 17:54:19'),
(230, 42, 'hero_button_secondary_link', 'LINK', '#tickets', NULL, NULL, '2026-03-09 23:06:39'),
(231, 45, 'gradient_heading', 'HEADING', 'Every street holds echoes of the past, shaped by the people who once walked there.', NULL, NULL, '2026-02-16 12:52:54'),
(232, 45, 'gradient_subheading', 'TEXT', 'Where history comes alive through places, paths, and people.', NULL, NULL, '2026-02-16 12:52:54'),
(233, 40, 'intro_heading', 'HEADING', 'Experience the living history of Haarlem', NULL, NULL, '2026-02-16 13:01:35'),
(234, 51, 'route_location1_name', 'TEXT', 'Church of St.Bavo', NULL, NULL, '2026-02-16 13:04:58'),
(235, 51, 'route_location1_description', 'TEXT', 'A monumental Gothic church famed for its towering nave and historic Müller organ once played by Mozart.', NULL, NULL, '2026-02-16 13:04:58'),
(236, 51, 'route_location2_name', 'TEXT', 'Grote Markt', NULL, NULL, '2026-02-16 13:06:02'),
(237, 51, 'route_location2_description', 'TEXT', 'A vibrant central square surrounded by landmark buildings and lively cafés; the city\'s cultural heart.', NULL, NULL, '2026-02-16 17:53:02'),
(238, 51, 'route_location3_name', 'TEXT', 'De Hallen', NULL, NULL, '2026-02-16 13:16:21'),
(239, 51, 'route_location3_description', 'TEXT', 'A former meat hall turned into an art and photography museum space that hosts exhibitions as part of the Frans Hals Museum.', NULL, NULL, '2026-02-16 13:16:21'),
(240, 51, 'route_location4_name', 'TEXT', 'Proveniershof', NULL, NULL, '2026-02-16 13:16:21'),
(241, 51, 'route_location4_description', 'TEXT', 'A peaceful 18th-century hofje (courtyard community) offering a quiet oasis with historic almshouses.', NULL, NULL, '2026-02-16 13:16:21'),
(242, 51, 'route_location5_name', 'TEXT', 'Jopenkerk', NULL, NULL, '2026-02-16 13:16:21'),
(243, 51, 'route_location5_description', 'TEXT', 'A former church transformed into Haarlem\'s iconic craft brewery and restaurant, blending tradition with modern beer culture.', NULL, NULL, '2026-02-16 13:16:21'),
(244, 51, 'route_location6_name', 'TEXT', 'Waalse Kerk', NULL, NULL, '2026-02-16 13:16:21'),
(245, 51, 'route_location6_description', 'TEXT', 'An intimate 17th-century Walloon church known for its serene atmosphere and historic interior.', NULL, NULL, '2026-02-16 13:16:21'),
(246, 51, 'route_location7_name', 'TEXT', 'Molen de Adriaan', NULL, NULL, '2026-02-16 13:16:21'),
(247, 51, 'route_location7_description', 'TEXT', 'A reconstructed 18th-century riverside windmill offering tours and panoramic views over the Spaarne.', NULL, NULL, '2026-02-16 13:16:21'),
(248, 51, 'route_location8_name', 'TEXT', 'Amsterdamse Poort', NULL, NULL, '2026-02-16 13:16:21'),
(249, 51, 'route_location8_description', 'TEXT', 'Haarlem\'s last surviving medieval city gate, showcasing impressive brickwork and centuries of history.', NULL, NULL, '2026-02-16 13:16:21'),
(250, 51, 'route_location9_name', 'TEXT', 'Hof van Bakenes', NULL, NULL, '2026-02-16 13:16:21'),
(251, 51, 'route_location9_description', 'TEXT', 'The oldest hofje in the Netherlands, featuring charming gardens and classic courtyard architecture dating back to 1395.', NULL, NULL, '2026-02-16 13:16:21'),
(252, 51, 'route_heading', 'HEADING', 'The Route', NULL, NULL, '2026-02-16 13:18:19'),
(253, 51, 'route_map_image', 'IMAGE_PATH', '/assets/Image/History/History-RouteMap.png', NULL, NULL, '2026-03-09 23:08:47'),
(254, 38, 'history_grotemarkt_name', 'TEXT', 'Grote Markt', NULL, NULL, '2026-03-22 22:54:52'),
(255, 38, 'history_grotemarkt_description', 'TEXT', 'The heart of the historic center of Haarlem.', NULL, NULL, '2026-02-16 13:21:46'),
(256, 38, 'history_grotemarkt_image', 'IMAGE_PATH', '/assets/Image/History/History-GroteMarkt.png', NULL, NULL, '2026-03-09 23:08:47'),
(257, 38, 'history_amsterdamsepoort_name', 'TEXT', 'Amsterdamse Poort', NULL, NULL, '2026-02-16 13:31:17'),
(258, 38, 'history_amsterdamsepoort_description', 'TEXT', 'As the only remaining city gate.', NULL, NULL, '2026-02-16 13:31:17'),
(259, 38, 'history_amsterdamsepoort_image', 'IMAGE_PATH', '/assets/Image/History/History-AmsterdamsePoort.png', NULL, NULL, '2026-03-09 23:08:47'),
(260, 38, 'history_molendeadriaan_name', 'TEXT', 'Molen De Adriaan', NULL, NULL, '2026-02-16 13:31:17'),
(261, 38, 'history_molendeadriaan_description', 'TEXT', 'A striking riverside windmill.', NULL, NULL, '2026-02-16 13:31:17'),
(262, 38, 'history_molendeadriaan_image', 'IMAGE_PATH', '/assets/Image/History/History-MolenDeAdriaan.png', NULL, NULL, '2026-03-09 23:08:47'),
(263, 38, 'historical_locations_heading', 'HEADING', 'Read more about these locations', NULL, NULL, '2026-02-16 13:36:54'),
(264, 52, 'ticket_options_heading', 'HEADING', 'Your ticket options to join the experience', NULL, NULL, '2026-02-16 13:38:08'),
(265, 52, 'history_single_ticket_icon', 'LINK', '/assets/Icons/History/single-ticket-icon.svg', NULL, NULL, '2026-02-16 13:49:05'),
(266, 52, 'history_pricing_single_title', 'TEXT', 'Single Ticket', NULL, NULL, '2026-02-16 13:49:05'),
(267, 53, 'history_pricing_single_price', 'TEXT', '€17.50', NULL, NULL, '2026-02-16 13:49:05'),
(268, 53, 'history_pricing_single_include1', 'TEXT', 'Per person', NULL, NULL, '2026-02-16 13:49:05'),
(269, 53, 'history_pricing_single_include2', 'TEXT', 'Includes one complimentary drink', NULL, NULL, '2026-02-16 13:49:05'),
(270, 53, 'history_pricing_single_include3', 'TEXT', '2.5 hour guided tour', NULL, NULL, '2026-02-16 13:49:05'),
(271, 52, 'history_group_ticket_icon', 'LINK', '/assets/Icons/History/group-ticket-icon.svg', NULL, NULL, '2026-02-16 13:49:05'),
(272, 52, 'history_pricing_group_title', 'TEXT', 'Group Ticket', NULL, NULL, '2026-02-16 13:49:05'),
(273, 53, 'history_pricing_group_price', 'TEXT', '€60.00', NULL, NULL, '2026-02-16 13:49:05'),
(274, 53, 'history_pricing_group_include1', 'TEXT', 'For up to 4 people', NULL, NULL, '2026-02-16 13:49:05'),
(275, 53, 'history_pricing_group_include2', 'TEXT', 'Includes four complimentary drinks', NULL, NULL, '2026-02-16 13:49:05'),
(276, 53, 'history_pricing_group_include3', 'TEXT', 'Best value for families!', NULL, NULL, '2026-02-16 13:49:05'),
(277, 54, 'history_important_tour_info_heading', 'TEXT', 'Important information about the tour', NULL, NULL, '2026-02-16 13:45:24'),
(278, 54, 'important_info_item1', 'TEXT', 'Minimum age requirement: 12 years old', NULL, NULL, '2026-02-16 13:45:24'),
(279, 54, 'important_info_item2', 'TEXT', 'No strollers allowed due to the nature of the walking route', NULL, NULL, '2026-02-16 13:45:24'),
(280, 54, 'important_info_item3', 'TEXT', 'Tour duration: Approximately 2.5 hours including 15-minute break', NULL, NULL, '2026-02-16 13:45:24'),
(281, 54, 'important_info_item4', 'TEXT', 'Group ticket is the best value for a group of 4 or for a family', NULL, NULL, '2026-02-16 13:45:24'),
(282, 54, 'important_info_item5', 'TEXT', 'Starting point: Look for the giant flag near Church of St. Bavo at Grote Markt', NULL, NULL, '2026-02-16 13:45:24'),
(283, 54, 'important_info_item6', 'TEXT', 'Group size: Maximum 12 participants per guide', NULL, NULL, '2026-02-16 13:45:24'),
(284, 54, 'important_info_item7', 'TEXT', 'Comfortable walking shoes recommended', NULL, NULL, '2026-02-16 13:45:24'),
(285, 54, 'important_info_item8', 'TEXT', 'Tours run in light rain; severe weather cancellations will be communicated via email', NULL, NULL, '2026-02-16 13:45:24'),
(286, 55, 'hero_main_title', 'HEADING', 'Yummy Gourmet with a Twist', NULL, NULL, '2026-02-16 16:31:14'),
(287, 55, 'hero_subtitle', 'TEXT', 'Discover 7 gourmet restaurants offering exclusive festival\nmenus crafted by top local chefs.', NULL, NULL, '2026-02-16 16:31:14'),
(288, 55, 'hero_button_primary', 'BUTTON_TEXT', 'Discover restaurants', NULL, NULL, '2026-02-16 16:31:14'),
(289, 55, 'hero_button_primary_link', 'LINK', '#restaurants-grid', NULL, NULL, '2026-04-01 19:35:39'),
(290, 55, 'hero_button_secondary', 'BUTTON_TEXT', 'About Yummy', NULL, NULL, '2026-02-16 16:31:14'),
(291, 55, 'hero_button_secondary_link', 'LINK', '#about', NULL, NULL, '2026-02-16 16:31:14'),
(292, 55, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/restaurants/hero-picture.png', NULL, NULL, '2026-02-16 16:31:14'),
(293, 56, 'gradient_heading', 'HEADING', 'Good food tastes better when shared.', NULL, NULL, '2026-02-16 16:31:14'),
(294, 56, 'gradient_subheading', 'TEXT', 'Food, stories, and shared moments across Haarlem.', NULL, NULL, '2026-02-16 16:31:14'),
(295, 56, 'gradient_background_image', 'IMAGE_PATH', '/assets/Image/restaurants/chef-preparing-food.png', NULL, NULL, '2026-02-16 16:31:14'),
(296, 57, 'intro_heading', 'HEADING', 'Yummy! at the Heart of the Festival', NULL, NULL, '2026-02-16 16:31:14'),
(297, 57, 'intro_body', 'TEXT', 'Welcome to Yummy!, the food experience of the Haarlem Festival.\nFour days where some of the city\'s favorite restaurants open their doors with special menus made just for this event.\n\n## What is Yummy?\nA festival of food where each restaurant offers one unique menu, set time slots, and special prices.\n\n## Who takes part?\nLocal chefs and restaurants from all around Haarlem, prepare with their own style a great variety of dishes, such as: Dutch-French-European-Fish & Seafood-Modern Vegan.\n\n## How does it work?\nChoose a restaurant, pick a time slot, and make a reservation. Seats are limited, so booking ahead is important.\n\nCome enjoy great food, good company, and a warm festival atmosphere.', NULL, NULL, '2026-02-16 16:31:14'),
(298, 57, 'intro_image', 'IMAGE_PATH', '/assets/Image/restaurants/table-with-food-and-drink.png', NULL, NULL, '2026-02-16 16:31:14'),
(299, 57, 'intro_image_alt', 'TEXT', 'Yummy! at the Heart of the Festival', NULL, NULL, '2026-02-16 16:31:14'),
(300, 58, 'intro2_heading', 'HEADING', 'When Haarlem Becomes a Dining Room', NULL, NULL, '2026-02-16 16:31:14'),
(301, 58, 'intro2_body', 'TEXT', 'As the sun sets over Haarlem\'s historic streets, the city slowly turns into one big dining room.\n\nFrom Thursday to Sunday, each restaurant offers 2 to 3 sessions later afternoon, starting from 16:30 and lasting around 1.5 to 2 hours..\n\nJust enough time to enjoy your plate, share a toast, and wander to the next event or performance nearby.', NULL, NULL, '2026-02-16 16:31:14'),
(302, 58, 'intro2_image', 'IMAGE_PATH', '/assets/Image/restaurants/food-in-canal.png', NULL, NULL, '2026-02-16 16:31:14'),
(303, 58, 'intro2_image_alt', 'TEXT', 'When Haarlem Becomes a Dining Room', NULL, NULL, '2026-02-16 16:31:14'),
(304, 59, 'instructions_title', 'HEADING', 'How reservations work', NULL, NULL, '2026-02-16 16:31:14'),
(305, 59, 'instructions_card_1_title', 'HEADING', 'Browse', NULL, NULL, '2026-02-16 16:31:14'),
(306, 59, 'instructions_card_1_text', 'TEXT', 'Explore participating restaurants and their exclusive festival menus.', NULL, NULL, '2026-02-16 16:31:14'),
(307, 59, 'instructions_card_2_title', 'HEADING', 'Choose', NULL, NULL, '2026-02-16 16:31:14'),
(308, 59, 'instructions_card_2_text', 'TEXT', 'Pick a date and time slot that fits your schedule.', NULL, NULL, '2026-02-16 16:31:14'),
(309, 59, 'instructions_card_3_title', 'HEADING', 'Reserve', NULL, NULL, '2026-02-16 16:31:14'),
(310, 59, 'instructions_card_3_text', 'TEXT', 'Complete your booking and receive a confirmation. Done!', NULL, NULL, '2026-02-16 16:31:14'),
(311, 60, 'cards_title', 'HEADING', 'Explore the participant restaurants', NULL, NULL, '2026-02-16 16:31:14'),
(312, 60, 'cards_subtitle', 'TEXT', 'Discover all restaurants participating in Yummy! Each one offers a special festival menu, unique flavors, and limited time slots throughout the weekend.', NULL, NULL, '2026-02-16 16:31:14'),
(313, 60, 'filter_all', 'BUTTON_TEXT', 'All', NULL, NULL, '2026-02-16 16:31:14'),
(314, 60, 'filter_dutch', 'BUTTON_TEXT', 'Dutch', NULL, NULL, '2026-02-16 16:31:14'),
(315, 60, 'filter_european', 'BUTTON_TEXT', 'European', NULL, NULL, '2026-02-16 16:31:14'),
(316, 60, 'filter_french', 'BUTTON_TEXT', 'French', NULL, NULL, '2026-02-16 16:31:14'),
(317, 60, 'filter_modern', 'BUTTON_TEXT', 'Modern', NULL, NULL, '2026-02-16 16:31:14'),
(318, 60, 'filter_fish_seafood', 'BUTTON_TEXT', 'Fish & Seafood', NULL, NULL, '2026-02-16 16:31:14'),
(319, 60, 'filter_vegetarian', 'BUTTON_TEXT', 'Vegetarian', NULL, NULL, '2026-02-16 16:31:14'),
(390, 61, 'about_heading', 'HEADING', 'Winnie de Poeh (4+)', NULL, NULL, '2026-03-09 15:46:45'),
(391, 61, 'about_body', 'HTML', NULL, '<p>An enchanting storytelling experience for children aged 4 and above, bringing the beloved story of Winnie the Pooh to life in the heart of Haarlem.</p>', NULL, '2026-03-09 15:46:45'),
(408, 61, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:46:45'),
(409, 62, 'about_heading', 'HEADING', 'Omdenken Podcast', NULL, NULL, '2026-03-09 15:51:55'),
(410, 62, 'about_body', 'HTML', NULL, '<p>A live podcast recording with audience participation. Thought-provoking conversations that challenge conventional thinking and inspire new perspectives.</p>', NULL, '2026-03-09 15:51:55'),
(427, 62, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(428, 63, 'about_heading', 'HEADING', 'About Buurderij Haarlem', NULL, NULL, '2026-03-09 15:51:55'),
(429, 63, 'about_body', 'HTML', NULL, '<p>Buurderij Haarlem is a local food community where neighbours order directly from farmers and small producers in the region. What started as a simple idea grew into a place where food is not just bought, but shared as a story. Each product represents a person, a process, and a belief in fair trade and sustainability.<br><br>During this storytelling session, the audience is taken behind the scenes of Buurderij Haarlem. The story explains how local entrepreneurs, volunteers, and residents work together to create a system based on trust, transparency, and respect for nature. It highlights the human side of food, showing how everyday choices can strengthen local economies and communities.</p>', NULL, '2026-03-19 14:01:29'),
(446, 63, 'video_url', 'TEXT', 'https://www.youtube.com/embed/25Pt1AZO9EM', NULL, NULL, '2026-03-19 14:06:52'),
(447, 64, 'about_heading', 'HEADING', 'Corrie voor kinderen', NULL, NULL, '2026-03-09 15:51:55'),
(448, 64, 'about_body', 'HTML', NULL, '<p>A family storytelling experience that brings the story of Corrie to life for young audiences with engaging performances and vivid imagery.</p>', NULL, '2026-03-09 15:51:55'),
(465, 64, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(466, 65, 'about_heading', 'HEADING', 'Winnaars verhalenvertel wedstrijd', NULL, NULL, '2026-03-09 15:51:55'),
(467, 65, 'about_body', 'HTML', NULL, '<p>The winners of this year verhalenvertel competition share their winning stories about Haarlem, its people, and its spirit.</p>', NULL, '2026-03-09 15:51:55'),
(484, 65, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(485, 66, 'about_heading', 'HEADING', 'Het verhaal van de Oeserzwammerij', NULL, NULL, '2026-03-09 15:51:55'),
(486, 66, 'about_body', 'HTML', NULL, '<p>An intimate storytelling session about the Oeserzwammerij, a local initiative growing oyster mushrooms sustainably in Haarlem.</p>', NULL, '2026-03-09 15:51:55'),
(503, 66, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(504, 67, 'about_heading', 'HEADING', 'Flip Thinking Podcast', NULL, NULL, '2026-03-09 15:51:55'),
(505, 67, 'about_body', 'HTML', NULL, '<p>A live recording of the popular Flip Thinking podcast, where hosts and guests explore the art of reframing problems into opportunities.</p>', NULL, '2026-03-09 15:51:55'),
(522, 67, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(542, 69, 'about_heading', 'HEADING', 'About Mister Anansi', NULL, NULL, '2026-03-19 12:51:45'),
(543, 69, 'about_body', 'HTML', NULL, '<p>Mister Anansi is one of the most iconic figures in West African and Caribbean storytelling traditions. Known as the clever spider who uses wit and creativity to navigate challenges, Anansi has been at the heart of oral storytelling for generations. His tales have travelled across continents, carried through families, communities and cultural celebrations. <br><br>Over time, Anansi&rsquo;s stories have appeared in books, theatre performances, educational programs and festivals around the world. His role as a trickster and teacher makes him a powerful symbol of resilience and imagination. Today, Mister Anansi continues to inspire audiences with stories that explore courage, intelligence and the importance of community, ensuring his enduring relevance in modern storytelling.</p>', NULL, '2026-03-19 12:45:02'),
(560, 69, 'video_url', 'TEXT', 'https://www.youtube.com/embed/OYQNvjaxo38', NULL, NULL, '2026-03-19 14:07:49'),
(561, 70, 'about_heading', 'HEADING', 'Podcastlast Haarlem Special', NULL, NULL, '2026-03-09 15:51:55'),
(562, 70, 'about_body', 'HTML', NULL, '<p>A special live edition of the Podcastlast podcast, recorded at the Haarlem Festival with candid conversations and local insights.</p>', NULL, '2026-03-09 15:51:55'),
(579, 70, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(580, 71, 'about_heading', 'HEADING', 'De geschiedenis van familie ten Boom', NULL, NULL, '2026-03-09 15:51:55'),
(581, 71, 'about_body', 'HTML', NULL, '<p>The remarkable story of the ten Boom family, whose Haarlem home became a refuge during World War II, sheltering Jewish families at great personal risk.</p>', NULL, '2026-03-09 15:51:55'),
(598, 71, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(599, 72, 'about_heading', 'HEADING', 'The History of the Ten Boom Family', NULL, NULL, '2026-03-09 15:51:55'),
(600, 72, 'about_body', 'HTML', NULL, '<p>The story of the ten Boom family told in English. During WWII they used their Haarlem shop to hide Jewish families, leaving a lasting legacy on the city.</p>', NULL, '2026-03-09 15:51:55'),
(617, 72, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(618, 73, 'about_heading', 'HEADING', 'Winners of the Storytelling Competition', NULL, NULL, '2026-03-09 15:51:55'),
(619, 73, 'about_body', 'HTML', NULL, '<p>The best storytellers from this year competition take the stage to share their winning tales about Haarlem in English, selected for creativity, emotional depth, and unique perspective.</p>', NULL, '2026-03-09 15:51:55'),
(636, 73, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(637, 74, 'detail_hero_subtitle_template', 'TEXT', '{cuisine}\nRelax, explore, and let {name} make your evening something truly special.', NULL, NULL, '2026-03-08 12:40:41'),
(638, 74, 'detail_hero_btn_primary', 'BUTTON_TEXT', 'Make a reservation', NULL, NULL, '2026-03-08 12:40:41'),
(639, 74, 'detail_hero_btn_secondary', 'BUTTON_TEXT', 'Back to restaurant', NULL, NULL, '2026-03-08 12:40:41'),
(640, 74, 'detail_contact_title', 'HEADING', 'Contact', NULL, NULL, '2026-03-08 12:40:41'),
(641, 74, 'detail_label_address', 'TEXT', 'ADDRESS', NULL, NULL, '2026-03-08 12:40:41'),
(642, 74, 'detail_label_contact', 'TEXT', 'CONTACT', NULL, NULL, '2026-03-08 12:40:41'),
(643, 74, 'detail_label_open_hours', 'TEXT', 'OPEN HOURS FOR YUMMY', NULL, NULL, '2026-03-08 12:40:41'),
(644, 74, 'detail_practical_title', 'HEADING', 'Practical Info', NULL, NULL, '2026-03-08 12:40:41'),
(645, 74, 'detail_label_price_food', 'TEXT', 'PRICE AND FOOD', NULL, NULL, '2026-03-08 12:40:41'),
(646, 74, 'detail_label_rating', 'TEXT', 'RESTAURANT RATING', NULL, NULL, '2026-03-08 12:40:41'),
(647, 74, 'detail_label_special_requests', 'TEXT', 'SPECIAL REQUESTS', NULL, NULL, '2026-03-08 12:40:41'),
(648, 74, 'detail_gallery_title', 'HEADING', 'Restaurant Gallery', NULL, NULL, '2026-03-08 12:40:41'),
(649, 74, 'detail_about_title_prefix', 'HEADING', 'About', NULL, NULL, '2026-03-08 12:40:41'),
(650, 74, 'detail_chef_title', 'HEADING', 'Chef & Philosophy', NULL, NULL, '2026-03-08 12:40:41'),
(651, 74, 'detail_menu_title', 'HEADING', 'Menu Style', NULL, NULL, '2026-03-08 12:40:41'),
(652, 74, 'detail_menu_cuisine_label', 'TEXT', 'Cuisine type:', NULL, NULL, '2026-03-08 12:40:41'),
(653, 74, 'detail_location_title', 'HEADING', 'Location', NULL, NULL, '2026-03-08 12:40:41'),
(654, 74, 'detail_location_address_label', 'TEXT', 'Address', NULL, NULL, '2026-03-08 12:40:41'),
(655, 74, 'detail_reservation_title', 'HEADING', 'Make your Reservation', NULL, NULL, '2026-03-08 12:40:41'),
(656, 74, 'detail_reservation_description', 'TEXT', 'Choose a time slot that suits your evening. When you\'re ready to book, continue to the next screen to confirm your guests and add any special requests.', NULL, NULL, '2026-03-08 12:40:41'),
(657, 74, 'detail_reservation_slots_label', 'TEXT', 'AVAILABLE TIME SLOTS', NULL, NULL, '2026-03-08 12:40:41'),
(658, 74, 'detail_reservation_note', 'TEXT', 'To make your reservation, please continue to the next screen.', NULL, NULL, '2026-03-08 12:40:41'),
(659, 74, 'detail_reservation_btn', 'BUTTON_TEXT', 'Continue to Reservation', NULL, NULL, '2026-03-08 12:40:41'),
(660, 74, 'detail_label_duration', 'TEXT', 'Duration', NULL, NULL, '2026-03-08 12:40:41'),
(661, 74, 'detail_label_seats', 'TEXT', 'Seats', NULL, NULL, '2026-03-08 12:40:41'),
(662, 74, 'detail_label_festival_rated', 'TEXT', 'Festival-rated', NULL, NULL, '2026-03-08 12:40:41'),
(663, 74, 'detail_label_michelin', 'TEXT', 'Michelin-star', NULL, NULL, '2026-03-08 12:40:41'),
(664, 74, 'detail_map_fallback_text', 'TEXT', 'Map coming soon', NULL, NULL, '2026-03-08 12:40:41'),
(665, 76, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-02-18 14:37:27'),
(666, 76, 'schedule_currency_symbol', 'TEXT', '€', NULL, NULL, '2026-02-18 14:37:27'),
(667, 76, 'schedule_filters_button_text', 'BUTTON_TEXT', 'Filters', NULL, NULL, '2026-02-18 14:37:27'),
(668, 76, 'schedule_no_events_text', 'TEXT', 'No events scheduled', NULL, NULL, '2026-02-18 14:37:27'),
(669, 76, 'schedule_show_filters', 'TEXT', '1', NULL, NULL, '2026-02-18 14:37:27'),
(670, 76, 'schedule_start_point', 'TEXT', 'A giant flag near Church of St. Bavo at Grote Markt', NULL, NULL, '2026-03-09 20:41:13'),
(671, 76, 'schedule_title', 'HEADING', 'Tour Schedule', NULL, NULL, '2026-02-18 14:37:27'),
(855, 79, 'page_title', 'HEADING', 'My Program', NULL, NULL, '2026-03-19 00:00:00'),
(856, 79, 'selected_events_heading', 'HEADING', 'Your Selected Events', NULL, NULL, '2026-03-19 00:00:00'),
(857, 79, 'pay_what_you_like_message', 'TEXT', 'Choose the amount you want to pay for this story. Any contribution is welcome and supports the initiative sharing their story.\nYou can adjust the amount before confirming your reservation.', NULL, NULL, '2026-03-19 00:00:00'),
(858, 79, 'clear_button_text', 'BUTTON_TEXT', 'CLEAR MY PROGRAMS', NULL, NULL, '2026-03-19 00:00:00'),
(859, 79, 'continue_exploring_text', 'TEXT', 'Continue exploring events', NULL, NULL, '2026-03-19 00:00:00'),
(860, 79, 'payment_overview_heading', 'HEADING', 'Payment Overview', NULL, NULL, '2026-03-19 00:00:00'),
(861, 79, 'tax_label', 'TEXT', 'VAT (21%)', NULL, NULL, '2026-03-19 00:00:00'),
(862, 79, 'checkout_button_text', 'BUTTON_TEXT', 'Continue to Checkout', NULL, NULL, '2026-03-19 00:00:00'),
(863, 80, 'page_title', 'HEADING', 'Checkout', NULL, NULL, '2026-03-19 00:00:00'),
(864, 80, 'back_button_text', 'BUTTON_TEXT', 'Back to My Program', NULL, NULL, '2026-03-19 00:00:00'),
(865, 80, 'payment_overview_heading', 'HEADING', 'Payment Overview', NULL, NULL, '2026-03-19 00:00:00'),
(866, 80, 'personal_info_heading', 'HEADING', 'Personal Information', NULL, NULL, '2026-03-19 00:00:00'),
(867, 80, 'personal_info_subtext', 'TEXT', 'Please fill in your details to complete the reservation', NULL, NULL, '2026-03-19 00:00:00'),
(868, 80, 'first_name_label', 'TEXT', 'First Name', NULL, NULL, '2026-03-19 00:00:00'),
(869, 80, 'first_name_placeholder', 'TEXT', 'Enter your first name', NULL, NULL, '2026-03-19 00:00:00'),
(870, 80, 'last_name_label', 'TEXT', 'Last Name', NULL, NULL, '2026-03-19 00:00:00'),
(871, 80, 'last_name_placeholder', 'TEXT', 'Enter your last name', NULL, NULL, '2026-03-19 00:00:00'),
(872, 80, 'email_label', 'TEXT', 'Email Address', NULL, NULL, '2026-03-19 00:00:00'),
(873, 80, 'email_placeholder', 'TEXT', 'Enter your email address', NULL, NULL, '2026-03-19 00:00:00'),
(874, 80, 'payment_methods_heading', 'HEADING', 'Payment Method', NULL, NULL, '2026-03-19 00:00:00'),
(875, 80, 'save_details_label', 'TEXT', 'Save my details', NULL, NULL, '2026-03-19 00:00:00'),
(876, 80, 'save_details_subtext', 'TEXT', 'for faster checkout next time', NULL, NULL, '2026-03-19 00:00:00'),
(877, 80, 'pay_button_text', 'BUTTON_TEXT', 'Pay Now', NULL, NULL, '2026-03-19 00:00:00'),
(878, 80, 'tax_label', 'TEXT', 'VAT (21%)', NULL, NULL, '2026-03-19 00:00:00'),
(879, 61, 'hero_image', 'IMAGE_PATH', '', NULL, 99, '2026-03-19 12:42:58'),
(880, 62, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, '2026-03-19 12:34:01'),
(881, 63, 'hero_image', 'IMAGE_PATH', '', NULL, 109, '2026-03-19 13:59:52'),
(882, 64, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, '2026-03-19 12:34:01'),
(883, 65, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, '2026-03-19 12:34:01'),
(884, 66, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, '2026-03-19 12:34:01'),
(885, 67, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, '2026-03-19 12:34:01'),
(887, 69, 'hero_image', 'IMAGE_PATH', '', NULL, 100, '2026-03-19 12:44:06'),
(888, 70, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, '2026-03-19 12:34:01'),
(889, 71, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, '2026-03-19 12:34:01'),
(890, 72, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, '2026-03-19 12:34:01'),
(891, 73, 'hero_image', 'IMAGE_PATH', '', NULL, NULL, '2026-03-19 12:34:01'),
(892, 61, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, '2026-03-19 12:34:01'),
(893, 61, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, '2026-03-19 12:34:01');
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(894, 62, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, '2026-03-19 12:34:01'),
(895, 62, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, '2026-03-19 12:34:01'),
(896, 63, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, '2026-03-19 12:34:01'),
(897, 63, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, '2026-03-19 12:34:01'),
(898, 64, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, '2026-03-19 12:34:01'),
(899, 64, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, '2026-03-19 12:34:01'),
(900, 65, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, '2026-03-19 12:34:01'),
(901, 65, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, '2026-03-19 12:34:01'),
(902, 66, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, '2026-03-19 12:34:01'),
(903, 66, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, '2026-03-19 12:34:01'),
(904, 67, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, '2026-03-19 12:34:01'),
(905, 67, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, '2026-03-19 12:34:01'),
(908, 69, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, '2026-03-19 12:34:01'),
(909, 69, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, '2026-03-19 12:34:01'),
(910, 70, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, '2026-03-19 12:34:01'),
(911, 70, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, '2026-03-19 12:34:01'),
(912, 71, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, '2026-03-19 12:34:01'),
(913, 71, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, '2026-03-19 12:34:01'),
(914, 72, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, '2026-03-19 12:34:01'),
(915, 72, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, '2026-03-19 12:34:01'),
(916, 73, 'back_button_label', 'BUTTON_TEXT', 'Back to storytelling', NULL, NULL, '2026-03-19 12:34:01'),
(917, 73, 'reserve_button_label', 'BUTTON_TEXT', 'Reserve your spot', NULL, NULL, '2026-03-19 12:34:01'),
(957, 61, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-03-19 12:40:52'),
(958, 62, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-03-19 12:40:52'),
(959, 63, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-03-19 12:40:52'),
(960, 64, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-03-19 12:40:52'),
(961, 65, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-03-19 12:40:52'),
(962, 66, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-03-19 12:40:52'),
(963, 67, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-03-19 12:40:52'),
(965, 69, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-03-19 12:40:52'),
(966, 70, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-03-19 12:40:52'),
(967, 71, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-03-19 12:40:52'),
(968, 72, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-03-19 12:40:52'),
(969, 73, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-03-19 12:40:52'),
(970, 69, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, 101, '2026-03-19 13:01:22'),
(971, 69, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, 102, '2026-03-19 13:01:42'),
(972, 69, 'highlight_1_title', 'TEXT', 'Anansi and the Pot of Wisdom', NULL, NULL, '2026-03-19 13:03:30'),
(973, 69, 'highlight_1_description', 'TEXT', 'Anansi’s quest to gather all the world’s wisdom leads to a humorous and insightful adventure that shows how knowledge grows when it is shared.', NULL, NULL, '2026-03-19 13:03:30'),
(974, 69, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, 103, '2026-03-19 13:02:06'),
(975, 69, 'highlight_2_title', 'TEXT', 'Anansi and the Talking Melon', NULL, NULL, '2026-03-19 13:03:30'),
(976, 69, 'highlight_2_description', 'TEXT', 'A playful tale in which Anansi outsmarts animals across the kingdom, bringing laughter while subtly teaching lessons about pride and clever thinking.', NULL, NULL, '2026-03-19 13:03:30'),
(977, 69, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, 104, '2026-03-19 13:02:13'),
(978, 69, 'highlight_3_title', 'TEXT', 'Anansi and the Turtle Feast', NULL, NULL, '2026-03-19 13:03:30'),
(979, 69, 'highlight_3_description', 'TEXT', 'A warm and funny story that explores themes of fairness and friendship as Anansi learns that tricks do not always go as planned.', NULL, NULL, '2026-03-19 13:03:30'),
(980, 69, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, 105, '2026-03-19 13:02:37'),
(981, 69, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, 106, '2026-03-19 13:03:05'),
(982, 69, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, 107, '2026-03-19 13:03:15'),
(983, 69, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, '2026-03-19 12:54:51'),
(984, 69, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, '2026-03-19 12:54:51'),
(985, 69, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, 108, '2026-03-19 13:03:28'),
(986, 61, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(987, 61, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/WinnieThePoohHeader.png', NULL, NULL, '2026-03-09 15:46:45'),
(988, 61, 'highlight_1_title', 'TEXT', 'A Story for All Ages', NULL, NULL, '2026-03-09 15:46:45'),
(989, 61, 'highlight_1_description', 'TEXT', 'A classic tale reimagined for young and old alike.', NULL, NULL, '2026-03-09 15:46:45'),
(990, 61, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, '2026-03-14 16:25:32'),
(991, 61, 'highlight_2_title', 'TEXT', 'Live Storytelling', NULL, NULL, '2026-03-14 16:25:32'),
(992, 61, 'highlight_2_description', 'TEXT', 'Experience the magic of oral storytelling in an intimate setting.', NULL, NULL, '2026-03-09 15:46:45'),
(993, 61, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(994, 61, 'highlight_3_title', 'TEXT', 'Interactive & Fun', NULL, NULL, '2026-03-09 15:46:45'),
(995, 61, 'highlight_3_description', 'TEXT', 'Children are invited to participate and use their imagination.', NULL, NULL, '2026-03-09 15:46:45'),
(996, 61, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/pig.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(997, 61, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(998, 61, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/WinnieThePoohHeader.png', NULL, NULL, '2026-03-09 15:46:45'),
(999, 61, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(1000, 61, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/pig.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(1001, 61, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(1005, 62, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1006, 62, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1007, 62, 'highlight_1_title', 'TEXT', 'Live Recording', NULL, NULL, '2026-03-09 15:51:55'),
(1008, 62, 'highlight_1_description', 'TEXT', 'Be part of a live podcast recording in front of an audience.', NULL, NULL, '2026-03-09 15:51:55'),
(1009, 62, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1010, 62, 'highlight_2_title', 'TEXT', 'Audience Participation', NULL, NULL, '2026-03-09 15:51:55'),
(1011, 62, 'highlight_2_description', 'TEXT', 'Your questions and insights shape the conversation.', NULL, NULL, '2026-03-09 15:51:55'),
(1012, 62, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1013, 62, 'highlight_3_title', 'TEXT', 'New Perspectives', NULL, NULL, '2026-03-09 15:51:55'),
(1014, 62, 'highlight_3_description', 'TEXT', 'Discover how reframing challenges can lead to creative solutions.', NULL, NULL, '2026-03-09 15:51:55'),
(1015, 62, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1016, 62, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1017, 62, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1018, 62, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1019, 62, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1020, 62, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1024, 63, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, 110, '2026-03-19 14:01:47'),
(1025, 63, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, 111, '2026-03-19 14:02:23'),
(1026, 63, 'highlight_1_title', 'TEXT', 'From Farmer to Neighbour', NULL, NULL, '2026-03-09 15:51:55'),
(1027, 63, 'highlight_1_description', 'TEXT', 'A personal story about how local farmers connect directly with Haarlem residents. This highlight focuses on trust, fair pricing, and the journey food takes from the land to the table.', NULL, NULL, '2026-03-19 14:01:29'),
(1028, 63, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, 112, '2026-03-19 14:02:32'),
(1029, 63, 'highlight_2_title', 'TEXT', 'Building a Local Food Community', NULL, NULL, '2026-03-09 15:51:55'),
(1030, 63, 'highlight_2_description', 'TEXT', 'This story explains how Buurderij Haarlem became more than a marketplace. It shows how weekly meetups created friendships, conversations, and a shared sense of responsibility for local food.', NULL, NULL, '2026-03-19 14:01:29'),
(1031, 63, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, 113, '2026-03-19 14:03:02'),
(1032, 63, 'highlight_3_title', 'TEXT', 'Food with a Future', NULL, NULL, '2026-03-09 15:51:55'),
(1033, 63, 'highlight_3_description', 'TEXT', 'This highlight focuses on sustainability and circular thinking. It tells how Buurderij Haarlem supports seasonal food, reduces waste, and encourages conscious consumption for future generations.', NULL, NULL, '2026-03-19 14:01:29'),
(1034, 63, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, 114, '2026-03-19 14:03:15'),
(1035, 63, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, 115, '2026-03-19 14:03:24'),
(1036, 63, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, 116, '2026-03-19 14:03:37'),
(1037, 63, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/where-stories-come-alive.jpg', NULL, 117, '2026-03-19 14:03:45'),
(1038, 63, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, 118, '2026-03-19 14:03:56'),
(1039, 63, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, 119, '2026-03-19 14:04:03'),
(1043, 64, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1044, 64, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1045, 64, 'highlight_1_title', 'TEXT', 'Stories for Every Child', NULL, NULL, '2026-03-09 15:51:55'),
(1046, 64, 'highlight_1_description', 'TEXT', 'An accessible and joyful storytelling session designed with children in mind.', NULL, NULL, '2026-03-09 15:51:55'),
(1047, 64, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1048, 64, 'highlight_2_title', 'TEXT', 'Imagination in Action', NULL, NULL, '2026-03-09 15:51:55'),
(1049, 64, 'highlight_2_description', 'TEXT', 'Watch as the performer brings characters to life through voice, gesture, and storytelling.', NULL, NULL, '2026-03-09 15:51:55'),
(1050, 64, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1051, 64, 'highlight_3_title', 'TEXT', 'A Shared Experience', NULL, NULL, '2026-03-09 15:51:55'),
(1052, 64, 'highlight_3_description', 'TEXT', 'A session that brings families and communities together around shared stories.', NULL, NULL, '2026-03-09 15:51:55'),
(1053, 64, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1054, 64, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1055, 64, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1056, 64, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1057, 64, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1058, 64, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1062, 65, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1063, 65, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1064, 65, 'highlight_1_title', 'TEXT', 'Competition Winners', NULL, NULL, '2026-03-09 15:51:55'),
(1065, 65, 'highlight_1_description', 'TEXT', 'Hear from the storytellers who won the Haarlem storytelling competition.', NULL, NULL, '2026-03-09 15:51:55'),
(1066, 65, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1067, 65, 'highlight_2_title', 'TEXT', 'Stories About Haarlem', NULL, NULL, '2026-03-09 15:51:55'),
(1068, 65, 'highlight_2_description', 'TEXT', 'Each winner tells a story rooted in the city culture and everyday life.', NULL, NULL, '2026-03-09 15:51:55'),
(1069, 65, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1070, 65, 'highlight_3_title', 'TEXT', 'Community Voice', NULL, NULL, '2026-03-09 15:51:55'),
(1071, 65, 'highlight_3_description', 'TEXT', 'The competition celebrates storytelling as a way to strengthen community bonds.', NULL, NULL, '2026-03-09 15:51:55'),
(1072, 65, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1073, 65, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1074, 65, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1075, 65, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1076, 65, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1077, 65, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1081, 66, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1082, 66, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1083, 66, 'highlight_1_title', 'TEXT', 'Sustainable Innovation', NULL, NULL, '2026-03-09 15:51:55'),
(1084, 66, 'highlight_1_description', 'TEXT', 'Learn how local entrepreneurs are growing food sustainably in the city.', NULL, NULL, '2026-03-09 15:51:55'),
(1085, 66, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1086, 66, 'highlight_2_title', 'TEXT', 'Community Impact', NULL, NULL, '2026-03-09 15:51:55'),
(1087, 66, 'highlight_2_description', 'TEXT', 'The Oeserzwammerij connects growers, volunteers, and consumers.', NULL, NULL, '2026-03-09 15:51:55'),
(1088, 66, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1089, 66, 'highlight_3_title', 'TEXT', 'From Waste to Food', NULL, NULL, '2026-03-09 15:51:55'),
(1090, 66, 'highlight_3_description', 'TEXT', 'Using coffee grounds and organic waste to grow nutritious mushrooms.', NULL, NULL, '2026-03-09 15:51:55'),
(1091, 66, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1092, 66, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1093, 66, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1094, 66, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1095, 66, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1096, 66, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1100, 67, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1101, 67, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1102, 67, 'highlight_1_title', 'TEXT', 'Reframe Your World', NULL, NULL, '2026-03-09 15:51:55'),
(1103, 67, 'highlight_1_description', 'TEXT', 'Learn practical techniques for turning obstacles into opportunities.', NULL, NULL, '2026-03-09 15:51:55'),
(1104, 67, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1105, 67, 'highlight_2_title', 'TEXT', 'Live Podcast Recording', NULL, NULL, '2026-03-09 15:51:55'),
(1106, 67, 'highlight_2_description', 'TEXT', 'Experience the magic of a podcast recorded before a live audience.', NULL, NULL, '2026-03-09 15:51:55'),
(1107, 67, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1108, 67, 'highlight_3_title', 'TEXT', 'Q and A with Audience', NULL, NULL, '2026-03-09 15:51:55'),
(1109, 67, 'highlight_3_description', 'TEXT', 'After the recording, guests take questions from the audience.', NULL, NULL, '2026-03-09 15:51:55'),
(1110, 67, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1111, 67, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1112, 67, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1113, 67, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1114, 67, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1115, 67, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1157, 70, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1158, 70, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1159, 70, 'highlight_1_title', 'TEXT', 'Haarlem Special Edition', NULL, NULL, '2026-03-09 15:51:55'),
(1160, 70, 'highlight_1_description', 'TEXT', 'A one-of-a-kind live episode recorded exclusively at the festival.', NULL, NULL, '2026-03-09 15:51:55'),
(1161, 70, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1162, 70, 'highlight_2_title', 'TEXT', 'Local Conversations', NULL, NULL, '2026-03-09 15:51:55'),
(1163, 70, 'highlight_2_description', 'TEXT', 'Featuring guests from Haarlem creative and cultural scene.', NULL, NULL, '2026-03-09 15:51:55'),
(1164, 70, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1165, 70, 'highlight_3_title', 'TEXT', 'Live Audience Experience', NULL, NULL, '2026-03-09 15:51:55'),
(1166, 70, 'highlight_3_description', 'TEXT', 'Be part of the recording and feel the energy of a live podcast taping.', NULL, NULL, '2026-03-09 15:51:55'),
(1167, 70, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1168, 70, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1169, 70, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1170, 70, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1171, 70, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1172, 70, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1176, 71, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1177, 71, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1178, 71, 'highlight_1_title', 'TEXT', 'A Story of Courage', NULL, NULL, '2026-03-09 15:51:55'),
(1179, 71, 'highlight_1_description', 'TEXT', 'The ten Boom family risked everything to shelter Jews during the occupation.', NULL, NULL, '2026-03-09 15:51:55'),
(1180, 71, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1181, 71, 'highlight_2_title', 'TEXT', 'Haarlem in History', NULL, NULL, '2026-03-09 15:51:55'),
(1182, 71, 'highlight_2_description', 'TEXT', 'Explore how one family home became a symbol of resistance and compassion.', NULL, NULL, '2026-03-09 15:51:55'),
(1183, 71, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1184, 71, 'highlight_3_title', 'TEXT', 'Legacy and Memory', NULL, NULL, '2026-03-09 15:51:55'),
(1185, 71, 'highlight_3_description', 'TEXT', 'Their story lives on as an inspiration for future generations.', NULL, NULL, '2026-03-09 15:51:55'),
(1186, 71, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1187, 71, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1188, 71, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1189, 71, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1190, 71, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1191, 71, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1195, 72, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1196, 72, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1197, 72, 'highlight_1_title', 'TEXT', 'In English', NULL, NULL, '2026-03-09 15:51:55'),
(1198, 72, 'highlight_1_description', 'TEXT', 'The ten Boom family story told for international visitors.', NULL, NULL, '2026-03-09 15:51:55'),
(1199, 72, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1200, 72, 'highlight_2_title', 'TEXT', 'Wartime Haarlem', NULL, NULL, '2026-03-09 15:51:55'),
(1201, 72, 'highlight_2_description', 'TEXT', 'Step back in time to a Haarlem that faced unimaginable choices.', NULL, NULL, '2026-03-09 15:51:55'),
(1202, 72, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1203, 72, 'highlight_3_title', 'TEXT', 'Hope and Resilience', NULL, NULL, '2026-03-09 15:51:55'),
(1204, 72, 'highlight_3_description', 'TEXT', 'A story about human courage that resonates across generations and borders.', NULL, NULL, '2026-03-09 15:51:55'),
(1205, 72, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1206, 72, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1207, 72, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1208, 72, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1209, 72, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1210, 72, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1214, 73, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1215, 73, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1216, 73, 'highlight_1_title', 'TEXT', 'Competition Winners', NULL, NULL, '2026-03-09 15:51:55'),
(1217, 73, 'highlight_1_description', 'TEXT', 'The English-language winners of the storytelling competition share their best work.', NULL, NULL, '2026-03-09 15:51:55'),
(1218, 73, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1219, 73, 'highlight_2_title', 'TEXT', 'Stories About Haarlem', NULL, NULL, '2026-03-09 15:51:55'),
(1220, 73, 'highlight_2_description', 'TEXT', 'Each story is rooted in the life and character of the city.', NULL, NULL, '2026-03-09 15:51:55'),
(1221, 73, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1222, 73, 'highlight_3_title', 'TEXT', 'New Voices', NULL, NULL, '2026-03-09 15:51:55'),
(1223, 73, 'highlight_3_description', 'TEXT', 'Discover emerging storytelling talent from Haarlem and beyond.', NULL, NULL, '2026-03-09 15:51:55'),
(1224, 73, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1225, 73, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1226, 73, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1227, 73, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1228, 73, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1229, 73, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(1231, 16, 'masonry_image_01', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-19 13:52:24'),
(1232, 16, 'masonry_image_02', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-19 13:52:24'),
(1233, 16, 'masonry_image_03', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-19 13:52:24'),
(1234, 16, 'masonry_image_04', 'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, '2026-03-19 13:52:24'),
(1235, 16, 'masonry_image_05', 'IMAGE_PATH', '/assets/Image/storytelling/pig.jpg', NULL, NULL, '2026-03-19 13:52:24'),
(1236, 16, 'masonry_image_06', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-19 13:52:24'),
(1237, 16, 'masonry_image_07', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-19 13:52:24'),
(1238, 16, 'masonry_image_08', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-03-19 13:52:24'),
(1239, 16, 'masonry_image_09', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, '2026-03-19 13:52:24'),
(1240, 16, 'masonry_image_10', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, '2026-03-19 13:52:24'),
(1241, 16, 'masonry_image_11', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, NULL, '2026-03-19 13:52:24'),
(1242, 16, 'masonry_image_12', 'IMAGE_PATH', '/assets/Image/storytelling/WinnieThePoohHeader.png', NULL, NULL, '2026-03-19 13:52:24'),
(1244, 36, 'schedule_filter_day_label', 'TEXT', 'Day', NULL, NULL, '2026-03-19 19:07:42'),
(1245, 36, 'schedule_filter_all_label', 'TEXT', 'All', NULL, NULL, '2026-03-19 19:07:42'),
(1246, 36, 'schedule_filter_reset_text', 'TEXT', 'Reset all filters', NULL, NULL, '2026-03-19 19:07:42'),
(1247, 36, 'schedule_filter_time_range_label', 'TEXT', 'Time Range', NULL, NULL, '2026-03-19 19:07:42'),
(1248, 36, 'schedule_filter_morning_label', 'TEXT', 'Morning (before 12:00)', NULL, NULL, '2026-03-19 19:07:42'),
(1249, 36, 'schedule_filter_afternoon_label', 'TEXT', 'Afternoon (12:00 to 17:00)', NULL, NULL, '2026-03-19 19:07:42'),
(1250, 36, 'schedule_filter_evening_label', 'TEXT', 'Evening (after 17:00)', NULL, NULL, '2026-03-19 19:07:42'),
(1251, 36, 'schedule_filter_price_type_label', 'TEXT', 'Price Type', NULL, NULL, '2026-03-19 19:07:42'),
(1252, 36, 'schedule_filter_pay_as_you_like_label', 'TEXT', 'Pay as you like', NULL, NULL, '2026-03-19 19:07:42'),
(1253, 36, 'schedule_filter_fixed_price_label', 'TEXT', 'Fixed Price', NULL, NULL, '2026-03-19 19:07:42'),
(1254, 36, 'schedule_filter_language_label', 'TEXT', 'Language', NULL, NULL, '2026-03-19 19:07:42'),
(1255, 36, 'schedule_filter_english_label', 'TEXT', 'English', NULL, NULL, '2026-03-19 19:07:42'),
(1256, 36, 'schedule_filter_dutch_label', 'TEXT', 'Dutch', NULL, NULL, '2026-03-19 19:07:42'),
(1257, 36, 'schedule_filter_age_group_label', 'TEXT', 'Age Group', NULL, NULL, '2026-03-19 19:07:42'),
(1258, 36, 'schedule_filter_all_ages_label', 'TEXT', 'All ages', NULL, NULL, '2026-03-19 19:07:42'),
(1259, 37, 'schedule_filter_day_label', 'TEXT', 'Day', NULL, NULL, '2026-03-19 19:07:42'),
(1260, 37, 'schedule_filter_all_label', 'TEXT', 'All', NULL, NULL, '2026-03-19 19:07:42'),
(1261, 37, 'schedule_filter_reset_text', 'TEXT', 'Reset all filters', NULL, NULL, '2026-03-19 19:07:42'),
(1262, 37, 'schedule_filter_venue_label', 'TEXT', 'Venue', NULL, NULL, '2026-03-19 19:07:42'),
(1263, 37, 'schedule_filter_price_type_label', 'TEXT', 'Price Type', NULL, NULL, '2026-03-19 19:07:42'),
(1264, 37, 'schedule_filter_free_label', 'TEXT', 'Free', NULL, NULL, '2026-03-19 19:07:42'),
(1265, 37, 'schedule_filter_paid_label', 'TEXT', 'Paid', NULL, NULL, '2026-03-19 19:07:42'),
(1266, 81, 'hero_main_title', 'HEADING', 'GROTE MARKT', NULL, NULL, '2026-03-16 15:09:14'),
(1267, 81, 'hero_subtitle', 'TEXT', 'A vibrant central square, where centuries of culture, trade, and community life come together', NULL, NULL, '2026-03-16 15:04:56'),
(1268, 81, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/History/GroteMarkt-hero.png', NULL, NULL, '2026-03-16 15:04:56'),
(1269, 81, 'hero_map_image', 'IMAGE_PATH', '/assets/Image/History/GroteMarkt-map.png', NULL, NULL, '2026-03-16 15:04:56'),
(1270, 81, 'hero_button', 'BUTTON_TEXT', 'Back to history', NULL, NULL, '2026-03-16 15:04:56'),
(1271, 81, 'hero_button_link', 'LINK', '/history', NULL, NULL, '2026-03-16 15:04:56'),
(1272, 82, 'intro_heading', 'HEADING', 'Explore the heart of the historic center of Haarlem.', NULL, NULL, '2026-03-16 15:47:48'),
(1273, 82, 'intro_text', 'TEXT', 'Step into the beating heart of the center of Haarlem as you arrive at the iconic Grote Markt. Surrounded by centuries-old architecture, lively cafes, and landmarks that shaped the city\'s identity, this vibrant square is where Haarlem\'s past and present come together. Grote Markt is your gateway to the stories, craftsmanship, and cultural moments that have defined the city for generations. From grand celebrations to Saturday market life, every corner of this square holds a piece of Haarlem\'s enduring spirit. Begin your journey here and discover how history continues to echo through its bustling streets.', NULL, NULL, '2026-03-16 15:47:48'),
(1274, 82, 'intro_fact', 'TEXT', 'For centuries, Grote Markt has been the stage for the most important celebrations and gatherings in Haarlem. From medieval fairs and market days to modern cultural festivals, the square consistently brings the city to life. Its enduring role as a public meeting point reflects the spirit and resilience of the community of the city.', NULL, NULL, '2026-03-16 15:47:48'),
(1275, 82, 'intro_image', 'IMAGE_PATH', '/assets/Image/History/GroteMarkt-intro.png', NULL, NULL, '2026-03-16 15:47:48'),
(1276, 83, 'facts_heading', 'HEADING', 'Did you know that...', NULL, NULL, '2026-03-21 23:35:14'),
(1277, 83, 'fact1', 'TEXT', 'The Grote Markt has been Haarlem\'s main marketplace since the Middle Ages.', NULL, NULL, '2026-03-16 15:49:46'),
(1278, 83, 'fact2', 'TEXT', 'The square hosts the annual Flower Parade finale, one of the region\'s most beloved traditions.', NULL, NULL, '2026-03-16 15:49:46'),
(1279, 83, 'fact3', 'TEXT', 'Grote Markt once hosted lively weekly cheese markets, attracting traders from across the region and making Haarlem an important commercial hub.', NULL, NULL, '2026-03-16 15:49:46'),
(1280, 84, 'architectural_significance_heading', 'HEADING', 'Architectural significance', NULL, NULL, '2026-03-16 15:55:51'),
(1281, 84, 'architectural_significance_text', 'TEXT', 'The Grote Markt is surrounded by some of Haarlem\'s most defining architectural treasures, creating a visually striking and cohesive cityscape. The grandeur of St. Bavo Church contrasts beautifully with the refined Gothic elements of the City Hall. Historic merchant houses, each with their own distinct facades, illustrate centuries of urban development. Together, these buildings tell a story of Haarlem\'s architectural evolution through time. The square remains a living example of harmonious civic design in the heart of the city.', NULL, NULL, '2026-03-16 15:55:51'),
(1282, 84, 'historical_significance_heading', 'HEADING', 'Historical significance', NULL, NULL, '2026-03-16 15:55:51'),
(1283, 84, 'historical_significance_text', 'TEXT', 'As Haarlem\'s central square, the Grote Markt has witnessed pivotal moments in the city\'s economic, political, and cultural history. It served as the primary marketplace where trade fueled Haarlem\'s prosperity. The square was also a gathering place during major civic events, from public announcements to celebrations. Many surrounding landmarks played essential roles in shaping the city\'s identity. Today, the Grote Markt stands as a testament to Haarlem\'s enduring heritage and vibrant public life.', NULL, NULL, '2026-03-16 15:55:51'),
(1284, 84, 'significance_image', 'IMAGE_PATH', '/assets/Image/History/GroteMarkt-significance.png', NULL, NULL, '2026-03-16 15:55:51'),
(1285, 85, 'hero_main_title', 'HEADING', 'AMSTERDAMSE POORT', NULL, NULL, '2026-03-16 15:08:32'),
(1286, 85, 'hero_subtitle', 'TEXT', 'An iconic medieval gateway of Haarlem, standing as a striking reminder of the city\'s fortified past.', NULL, NULL, '2026-03-16 15:08:32'),
(1287, 85, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/History/AmsterdamsePoort-hero.png', NULL, NULL, '2026-03-16 15:08:32'),
(1288, 85, 'hero_map_image', 'IMAGE_PATH', '/assets/Image/History/AmsterdamsePoort-map.png', NULL, NULL, '2026-03-16 15:08:32'),
(1289, 85, 'hero_button', 'BUTTON_TEXT', 'Back to history', NULL, NULL, '2026-03-16 15:08:32'),
(1290, 85, 'hero_button_link', 'LINK', '/history', NULL, NULL, '2026-03-16 15:08:32'),
(1291, 86, 'intro_heading', 'HEADING', 'Explore the last surviving city gate of Haarlem.', NULL, NULL, '2026-03-16 15:47:48'),
(1292, 86, 'intro_text', 'TEXT', 'Amsterdamse Poort marks the threshold between historic Haarlem and the ancient road that once connected the city to Amsterdam. As the only remaining city gate, it offers a rare glimpse into the defensive structures that once protected citizens of Haarlem from outside threats. Today, its towers, arches, and brickwork tell stories of trade, travel, and transformation throughout the centuries. During the tour, this landmark invites visitors to slow down, look closer, and imagine the many journeys that began and ended at its impressive walls. Let this gateway introduce you to a chapter of the history of Haarlem where architecture, strategy, and daily life intertwined.', NULL, NULL, '2026-03-16 15:47:48'),
(1293, 86, 'intro_fact', 'TEXT', 'In the late Middle Ages, Amsterdamse Poort played a crucial role during periods of conflict, serving as both a defensive stronghold and a primary checkpoint for goods entering the city. During times of heightened tension, the gate was heavily fortified, illustrating its strategic importance to Haarlem\'s security. Its presence helped shape the flow of commerce and movement, making it central to the city\'s resilience and growth.', NULL, NULL, '2026-03-16 15:47:48'),
(1294, 86, 'intro_image', 'IMAGE_PATH', '/assets/Image/History/AmsterdamsePoort-intro.png', NULL, NULL, '2026-03-16 15:47:48'),
(1295, 87, 'facts_heading', 'HEADING', 'Did you know that...', NULL, NULL, '2026-03-21 23:35:14'),
(1296, 87, 'fact1', 'TEXT', 'The Amsterdamse Poort dates back to the 14th century and is the only surviving gate of Haarlem\'s original twelve city gates.', NULL, NULL, '2026-03-16 15:51:25'),
(1297, 87, 'fact2', 'TEXT', 'Its distinctive mix of towers and pointed arches makes it one of the city\'s most photographed historic structures.', NULL, NULL, '2026-03-16 15:51:25'),
(1298, 87, 'fact3', 'TEXT', 'The gateway once formed part of the main trade route between Haarlem and Amsterdam, influencing daily commerce for centuries.', NULL, NULL, '2026-03-16 15:51:25'),
(1299, 88, 'architectural_significance_heading', 'HEADING', 'Architectural significance', NULL, NULL, '2026-03-16 15:59:30'),
(1300, 88, 'architectural_significance_text', 'TEXT', 'Amsterdamse Poort showcases classic medieval fortification design with its twin round towers, pointed archways, and robust brick construction. Its asymmetrical layout hints at centuries of renovations and adaptations to shifting defensive needs. Decorative stonework and brick patterns add visual richness while highlighting the craftsmanship of the era. The gate\'s placement at a former moat crossing demonstrates the architectural balance between aesthetics and military function. Together, these features make Amsterdamse Poort an exceptional example of the medieval architectural heritage of Haarlem.', NULL, NULL, '2026-03-16 15:59:30'),
(1301, 88, 'historical_significance_heading', 'HEADING', 'Historical significance', NULL, NULL, '2026-03-16 15:59:30'),
(1302, 88, 'historical_significance_text', 'TEXT', 'Historically, Amsterdamse Poort served as a vital entry point for travelers, merchants, and goods arriving from Amsterdam. It played a central role in Haarlem\'s defense system, helping guard the city during numerous conflicts. As trade flourished, the gate became an economic lifeline, shaping the growth of urban landscape in Haarlem. Over time, it transitioned from a military necessity to a cherished cultural monument. Today, it stands as a powerful symbol of Haarlem\'s resilience and centuries-long connection to the wider region.', NULL, NULL, '2026-03-16 15:59:30'),
(1303, 88, 'significance_image', 'IMAGE_PATH', '/assets/Image/History/AmsterdamsePoort-significance.png', NULL, NULL, '2026-03-16 15:59:30'),
(1304, 89, 'hero_main_title', 'HEADING', 'MOLEN DE ADRIAAN', NULL, NULL, '2026-03-16 15:08:32'),
(1305, 89, 'hero_subtitle', 'TEXT', 'A striking riverside windmill that reflects rich industrial and cultural heritage of the city.', NULL, NULL, '2026-03-16 15:08:32'),
(1306, 89, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/History/MolenDeAdriaan-hero.png', NULL, NULL, '2026-03-16 15:08:32'),
(1307, 89, 'hero_map_image', 'IMAGE_PATH', '/assets/Image/History/MolenDeAdriaan-map.png', NULL, NULL, '2026-03-16 15:08:32'),
(1308, 89, 'hero_button', 'BUTTON_TEXT', 'Back to history', NULL, NULL, '2026-03-16 15:08:32'),
(1309, 89, 'hero_button_link', 'LINK', '/history', NULL, NULL, '2026-03-16 15:08:32'),
(1310, 90, 'intro_heading', 'HEADING', 'Explore one of the most iconic windmills of Haarlem.', NULL, NULL, '2026-03-16 15:47:48'),
(1311, 90, 'intro_text', 'TEXT', 'Standing proudly along the River Spaarne, Molen De Adriaan is one of the most recognizable landmarks of Haarlem. Its lofty silhouette has welcomed visitors for centuries, offering a glimpse into the city\'s craftsmanship, innovation, and relationship with the wind-powered industries that shaped the region. Today, the mill is not only a historic monument but also a vibrant learning space where history comes alive through demonstrations and storytelling. During the tour, this beloved windmill invites you to discover how technology, trade, and daily life intertwined in Haarlem\'s past. Let Molen De Adriaan show you how a simple force of nature powered an era of ingenuity.', NULL, NULL, '2026-03-16 15:47:48'),
(1312, 90, 'intro_fact', 'TEXT', 'After a devastating fire destroyed the original structure in 1932, the community of Haarlem rallied for decades to rebuild Molen De Adriaan. Their dedication culminated in its grand reopening in 2002, restoring the windmill to its former glory. This event marked not only the rebirth of a historic icon but also the strength of local pride and commitment to preserving heritage.', NULL, NULL, '2026-03-16 15:47:48'),
(1313, 90, 'intro_image', 'IMAGE_PATH', '/assets/Image/History/MolenDeAdriaan-intro.png', NULL, NULL, '2026-03-16 15:47:48'),
(1314, 91, 'facts_heading', 'HEADING', 'Did you know that...', NULL, NULL, '2026-03-21 23:35:14'),
(1315, 91, 'fact1', 'TEXT', 'Molen De Adriaan was originally built in 1779 and served as a mill for producing materials like cement, tobacco, and spices.', NULL, NULL, '2026-03-16 15:51:25'),
(1316, 91, 'fact2', 'TEXT', 'The mill\'s elevated base was designed to capture stronger winds from above nearby buildings.', NULL, NULL, '2026-03-16 15:51:25'),
(1317, 91, 'fact3', 'TEXT', 'Visitors can climb inside to see the mill\'s wooden gears and machinery in action during demonstrations.', NULL, NULL, '2026-03-16 15:51:25'),
(1318, 92, 'architectural_significance_heading', 'HEADING', 'Architectural significance', NULL, NULL, '2026-03-16 15:59:30'),
(1319, 92, 'architectural_significance_text', 'TEXT', 'Molen De Adriaan is a classic example of a Dutch tower mill, characterized by its tall brick base and elegant wooden superstructure. The elevated construction not only improves wind capture but also adds to its dramatic presence along the river. Its rotating cap, sails, and internal mechanisms demonstrate the engineering brilliance of 18th-century millwrights. The reconstruction in 2002 faithfully replicated traditional building techniques, blending historical accuracy with modern safety standards. Together, these architectural elements create a visually captivating landmark that embodies both beauty and functionality.', NULL, NULL, '2026-03-16 15:59:30'),
(1320, 92, 'historical_significance_heading', 'HEADING', 'Historical significance', NULL, NULL, '2026-03-16 15:59:30'),
(1321, 92, 'historical_significance_text', 'TEXT', 'Throughout its history, Molen De Adriaan played a crucial role in Haarlem\'s industrial development by grinding various materials essential for trade and craftsmanship. It served as a testament to the economic importance of wind power in the Netherlands. The mill\'s destruction and subsequent restoration reflect Haarlem\'s deep respect for its cultural heritage. Over time, it has transformed from an industrial site into a symbol of community identity and historical continuity. Today, Molen De Adriaan continues to share its story with visitors from around the world, preserving the legacy of working past of the city.', NULL, NULL, '2026-03-16 15:59:30'),
(1322, 92, 'significance_image', 'IMAGE_PATH', '/assets/Image/History/MolenDeAdriaan-significance.png', NULL, NULL, '2026-03-16 15:59:30'),
(1323, 38, 'history_grotemarkt_link', 'LINK', '/history/grote-markt', NULL, NULL, '2026-03-20 18:43:56'),
(1324, 38, 'history_amsterdamsepoort_link', 'LINK', '/history/amsterdamse-poort', NULL, NULL, '2026-03-20 18:43:56'),
(1325, 38, 'history_molendeadriaan_link', 'LINK', '/history/molen-de-adriaan', NULL, NULL, '2026-03-20 18:43:56'),
(1326, 42, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/History/History-hero.png', NULL, NULL, '2026-03-22 22:48:59'),
(1327, 42, 'hero_subtitle', 'TEXT', 'Explore nine centuries of turbulent history, magnificent architecture, and cultural treasures', NULL, NULL, '2026-03-22 22:48:59'),
(1328, 45, 'gradient_background_image', 'IMAGE_PATH', '/assets/Image/History/History-second-section.png', NULL, NULL, '2026-03-22 22:48:59'),
(1329, 40, 'intro_image', 'IMAGE_PATH', '/assets/Image/History/History-third-section.png', NULL, NULL, '2026-03-22 22:48:59'),
(1330, 40, 'intro_image_alt', 'TEXT', 'A corner of a historic building in Haarlem', NULL, NULL, '2026-03-22 22:48:59'),
(1331, 52, 'history_pricing_single_price', 'TEXT', '€17.50', NULL, NULL, '2026-03-22 23:03:17'),
(1332, 52, 'history_pricing_single_include1', 'TEXT', 'Per person', NULL, NULL, '2026-03-22 23:03:17'),
(1333, 52, 'history_pricing_single_include2', 'TEXT', 'Includes one complimentary drink', NULL, NULL, '2026-03-22 23:03:17'),
(1334, 52, 'history_pricing_single_include3', 'TEXT', '2.5 hour guided tour', NULL, NULL, '2026-03-22 23:03:17'),
(1335, 52, 'history_pricing_group_price', 'TEXT', '€60.00', NULL, NULL, '2026-03-22 23:03:17'),
(1336, 52, 'history_pricing_group_include1', 'TEXT', 'For up to 4 people', NULL, NULL, '2026-03-22 23:03:17'),
(1337, 52, 'history_pricing_group_include2', 'TEXT', 'Includes four complimentary drinks', NULL, NULL, '2026-03-22 23:03:17'),
(1338, 52, 'history_pricing_group_include3', 'TEXT', 'Best value for families!', NULL, NULL, '2026-03-22 23:03:17'),
(1339, 38, 'historical_locations_view_more_label', 'TEXT', 'View more', NULL, NULL, '2026-03-22 23:05:19'),
(1340, 28, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/Jazz/Jazz-hero.png', NULL, NULL, '2026-03-23 09:02:12'),
(1341, 76, 'schedule_filter_all_label', 'TEXT', 'All', NULL, NULL, '2026-03-30 12:48:39'),
(1342, 76, 'schedule_filter_day_label', 'TEXT', 'Day', NULL, NULL, '2026-03-30 12:48:39'),
(1344, 76, 'schedule_filter_start_time_label', 'TEXT', 'Start time', NULL, NULL, '2026-03-30 12:49:17'),
(1345, 76, 'schedule_filter_reset_text', 'TEXT', 'Reset all filters', NULL, NULL, '2026-03-30 12:49:17'),
(1346, 76, 'schedule_show_event_count', 'TEXT', '0', NULL, NULL, '2026-03-30 12:49:17'),
(1347, 76, 'schedule_show_additional_info', 'TEXT', '0', NULL, NULL, '2026-03-30 12:49:17'),
(1348, 76, 'schedule_year', 'TEXT', '2026', NULL, NULL, '2026-03-30 12:49:17'),
(1349, 76, 'schedule_pay_what_you_like_text', 'TEXT', 'Pay what you like', NULL, NULL, '2026-03-30 12:49:17'),
(1360, 93, 'cuisine_type', 'TEXT', 'Dutch, fish and seafood, European', NULL, NULL, '2026-04-01 12:33:53'),
(1361, 94, 'cuisine_type', 'TEXT', 'French, fish and seafood, European', NULL, NULL, '2026-04-01 12:33:53'),
(1362, 95, 'cuisine_type', 'TEXT', 'Dutch, fish and seafood, European', NULL, NULL, '2026-04-01 12:33:53'),
(1363, 96, 'cuisine_type', 'TEXT', 'Dutch, French, European', NULL, NULL, '2026-04-01 12:33:53'),
(1364, 97, 'cuisine_type', 'TEXT', 'Vegan', NULL, NULL, '2026-04-05 21:27:41'),
(1365, 98, 'cuisine_type', 'TEXT', 'Dutch, European, Modern', NULL, NULL, '2026-04-01 12:33:53'),
(1366, 99, 'cuisine_type', 'TEXT', 'Dutch, fish and seafood, European', NULL, NULL, '2026-04-01 12:33:53'),
(1367, 93, 'address_line', 'TEXT', 'Botermarkt 17, 2011 XL Haarlem', NULL, NULL, '2026-04-01 12:33:54'),
(1368, 94, 'address_line', 'TEXT', 'Spaarne 96, 2011 CL Haarlem', NULL, NULL, '2026-04-01 12:33:54'),
(1369, 95, 'address_line', 'TEXT', 'Kleine Houtstraat 70, 2011 DR Haarlem', NULL, NULL, '2026-04-01 12:33:54'),
(1370, 96, 'address_line', 'TEXT', 'Twijnderslaan 7, 2012 BG Haarlem', NULL, NULL, '2026-04-01 12:33:54'),
(1371, 97, 'address_line', 'TEXT', 'Koningstraat 5, 2011 TB Haarlem', NULL, NULL, '2026-04-05 21:27:41'),
(1372, 98, 'address_line', 'TEXT', 'Grote Markt 13, 2011 RC Haarlem', NULL, NULL, '2026-04-01 12:33:54'),
(1373, 99, 'address_line', 'TEXT', 'Oude Groenmarkt 10-12, 2011 HL Haarlem', NULL, NULL, '2026-04-01 12:33:54'),
(1374, 93, 'city', 'TEXT', 'Haarlem', NULL, NULL, '2026-04-01 12:33:54'),
(1375, 94, 'city', 'TEXT', 'Haarlem', NULL, NULL, '2026-04-01 12:33:54'),
(1376, 95, 'city', 'TEXT', 'Haarlem', NULL, NULL, '2026-04-01 12:33:54'),
(1377, 96, 'city', 'TEXT', 'Haarlem', NULL, NULL, '2026-04-01 12:33:54'),
(1378, 97, 'city', 'TEXT', 'Haarlem', NULL, NULL, '2026-04-01 12:33:54'),
(1379, 98, 'city', 'TEXT', 'Haarlem', NULL, NULL, '2026-04-01 12:33:54');
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(1380, 99, 'city', 'TEXT', 'Haarlem', NULL, NULL, '2026-04-01 12:33:54'),
(1381, 93, 'stars', 'TEXT', '4', NULL, NULL, '2026-04-01 12:33:54'),
(1382, 94, 'stars', 'TEXT', '4', NULL, NULL, '2026-04-01 12:33:54'),
(1383, 95, 'stars', 'TEXT', '4', NULL, NULL, '2026-04-01 12:33:54'),
(1384, 96, 'stars', 'TEXT', '4', NULL, NULL, '2026-04-01 12:33:54'),
(1385, 97, 'stars', 'TEXT', '3', NULL, NULL, '2026-04-05 21:27:41'),
(1386, 98, 'stars', 'TEXT', '3', NULL, NULL, '2026-04-01 12:33:54'),
(1387, 99, 'stars', 'TEXT', '3', NULL, NULL, '2026-04-01 12:33:54'),
(1388, 57, 'intro_sub1_heading', 'TEXT', 'What is Yummy?', NULL, NULL, '2026-04-01 19:35:39'),
(1389, 57, 'intro_sub1_text', 'TEXT', 'A festival of food where each restaurant offers one unique menu, set time slots, and special prices.', NULL, NULL, '2026-04-01 19:35:39'),
(1390, 57, 'intro_sub2_heading', 'TEXT', 'Who takes part?', NULL, NULL, '2026-04-01 19:35:39'),
(1391, 57, 'intro_sub2_text', 'TEXT', 'Local chefs and restaurants from all around Haarlem, prepare with their own style a great variety of dishes, such as: Dutch-French-European-Fish & Seafood-Modern Vegan.', NULL, NULL, '2026-04-01 19:35:39'),
(1392, 57, 'intro_sub3_heading', 'TEXT', 'How does it work?', NULL, NULL, '2026-04-01 19:35:39'),
(1393, 57, 'intro_sub3_text', 'TEXT', 'Choose a restaurant, pick a time slot, and make a reservation. Seats are limited, so booking ahead is important.', NULL, NULL, '2026-04-01 19:35:39'),
(1394, 57, 'intro_closing', 'TEXT', 'Come enjoy great food, good company, and a warm festival atmosphere.', NULL, NULL, '2026-04-01 19:35:39'),
(1396, 93, 'time_slots', 'TEXT', '16:30, 18:30, 20:30', NULL, NULL, '2026-04-01 19:35:39'),
(1397, 93, 'price_adult', 'TEXT', '45.00', NULL, NULL, '2026-04-01 19:35:39'),
(1398, 93, 'duration_minutes', 'TEXT', '120', NULL, NULL, '2026-04-01 19:35:39'),
(1400, 94, 'time_slots', 'TEXT', '17:00, 19:15, 21:30', NULL, NULL, '2026-04-01 19:35:39'),
(1401, 94, 'price_adult', 'TEXT', '45.00', NULL, NULL, '2026-04-01 19:35:39'),
(1402, 94, 'duration_minutes', 'TEXT', '120', NULL, NULL, '2026-04-01 19:35:39'),
(1404, 95, 'time_slots', 'TEXT', '16:30, 18:30, 20:30', NULL, NULL, '2026-04-01 19:35:39'),
(1405, 95, 'price_adult', 'TEXT', '45.00', NULL, NULL, '2026-04-01 19:35:39'),
(1406, 95, 'duration_minutes', 'TEXT', '120', NULL, NULL, '2026-04-01 19:35:39'),
(1408, 96, 'time_slots', 'TEXT', '16:30, 18:30, 20:30', NULL, NULL, '2026-04-01 19:35:39'),
(1409, 96, 'price_adult', 'TEXT', '45.00', NULL, NULL, '2026-04-01 19:35:39'),
(1410, 96, 'duration_minutes', 'TEXT', '120', NULL, NULL, '2026-04-01 19:35:39'),
(1412, 97, 'time_slots', 'TEXT', '16:30, 18:30, 20:30', NULL, NULL, '2026-04-01 19:35:39'),
(1413, 97, 'price_adult', 'TEXT', '35.00', NULL, NULL, '2026-04-01 19:35:39'),
(1414, 97, 'duration_minutes', 'TEXT', '120', NULL, NULL, '2026-04-01 19:35:39'),
(1416, 98, 'time_slots', 'TEXT', '16:30, 18:30, 20:30', NULL, NULL, '2026-04-01 19:35:39'),
(1417, 98, 'price_adult', 'TEXT', '35.00', NULL, NULL, '2026-04-01 19:35:39'),
(1418, 98, 'duration_minutes', 'TEXT', '120', NULL, NULL, '2026-04-01 19:35:39'),
(1420, 99, 'time_slots', 'TEXT', '17:30, 19:15, 21:00', NULL, NULL, '2026-04-01 19:35:39'),
(1421, 99, 'price_adult', 'TEXT', '35.00', NULL, NULL, '2026-04-01 19:35:39'),
(1422, 99, 'duration_minutes', 'TEXT', '120', NULL, NULL, '2026-04-01 19:35:39'),
(1452, 35, 'booking_contact_eyebrow', 'TEXT', 'CONTACT US', NULL, NULL, '2026-04-04 19:34:48'),
(1453, 35, 'booking_contact_title', 'TEXT', 'Get Information', NULL, NULL, '2026-04-04 19:34:48'),
(1454, 35, 'booking_contact_description', 'TEXT', 'Questions about the festival, venues, or artists? Contact our office during business hours.', NULL, NULL, '2026-04-04 19:34:48'),
(1455, 35, 'booking_contact_phone_office', 'TEXT', '023 - 517 58 50 (Office)', NULL, NULL, '2026-04-04 19:34:48'),
(1456, 35, 'booking_contact_phone_cash_desk', 'TEXT', '023 - 517 58 58 (Cash Desk)', NULL, NULL, '2026-04-04 19:34:48'),
(1457, 35, 'booking_contact_hours', 'TEXT', '10:00 - 17:00', NULL, NULL, '2026-04-04 19:34:48'),
(1458, 35, 'booking_venue_eyebrow', 'TEXT', 'VENUE DETAILS', NULL, NULL, '2026-04-04 19:34:48'),
(1459, 35, 'booking_venue_title', 'TEXT', 'Visit Patronaat', NULL, NULL, '2026-04-04 19:34:48'),
(1460, 35, 'booking_venue_description', 'TEXT', 'Learn more about our main indoor venue, accessibility options, and facilities.', NULL, NULL, '2026-04-04 19:34:48'),
(1461, 35, 'booking_tickets_eyebrow', 'TEXT', 'TICKETS', NULL, NULL, '2026-04-04 19:34:48'),
(1462, 35, 'booking_tickets_title', 'TEXT', 'Purchase Tickets', NULL, NULL, '2026-04-04 19:34:48'),
(1463, 35, 'booking_tickets_description', 'TEXT', 'Individual show tickets, day passes, and 3-day all-access passes available now. Book early for best selection.', NULL, NULL, '2026-04-04 19:34:48'),
(1467, 97, 'about_text', 'TEXT', '3-star restaurant experience during Haarlem Festival', NULL, NULL, '2026-04-05 21:27:41'),
(1472, 106, 'stars', 'TEXT', '4', NULL, NULL, '2026-04-05 21:28:15'),
(1473, 106, 'cuisine_type', 'TEXT', 'French, Modern', NULL, NULL, '2026-04-05 21:28:15'),
(1474, 106, 'address_line', 'TEXT', 'Nieuwe Straat 1, 2011 XY Haarlem', NULL, NULL, '2026-04-05 21:28:15'),
(1475, 106, 'about_text', 'TEXT', '4-star French cuisine experience', NULL, NULL, '2026-04-05 21:28:15'),
(1476, 105, 'stars', 'TEXT', '3', NULL, NULL, '2026-04-05 21:30:05'),
(1477, 105, 'cuisine_type', 'TEXT', 'italian food', NULL, NULL, '2026-04-05 21:30:05'),
(1478, 105, 'address_line', 'TEXT', 'Hoge Duin en Daalseweg 2', NULL, NULL, '2026-04-05 21:56:56'),
(1479, 105, 'about_text', 'TEXT', 'heh', NULL, NULL, '2026-04-05 21:30:05');

-- --------------------------------------------------------

--
-- Table structure for table `CmsItemKey`
--

CREATE TABLE `CmsItemKey` (
  `ItemKey` varchar(80) NOT NULL,
  `ExpectedItemType` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `CmsItemKey`
--

INSERT INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`) VALUES
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
('about_text', 'HTML'),
('chef_text', 'HTML'),
('location_description', 'HTML'),
('menu_description', 'HTML'),
('schedule_additional_info_body', 'HTML'),
('about_image_1', 'IMAGE_PATH'),
('about_image_2', 'IMAGE_PATH'),
('album_1_image', 'IMAGE_PATH'),
('album_2_image', 'IMAGE_PATH'),
('album_3_image', 'IMAGE_PATH'),
('chef_image', 'IMAGE_PATH'),
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
('menu_image_1', 'IMAGE_PATH'),
('menu_image_2', 'IMAGE_PATH'),
('reservation_image', 'IMAGE_PATH'),
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
('map_embed_url', 'LINK'),
('schedule_cta_button_link', 'LINK'),
('website', 'LINK'),
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
('address_line', 'TEXT'),
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
('back_button_label', 'TEXT'),
('back_button_text', 'TEXT'),
('banner_subtitle', 'TEXT'),
('booking_contact_description', 'TEXT'),
('booking_contact_eyebrow', 'TEXT'),
('booking_contact_hours', 'TEXT'),
('booking_contact_phone_cash_desk', 'TEXT'),
('booking_contact_phone_office', 'TEXT'),
('booking_contact_title', 'TEXT'),
('booking_cta_description', 'TEXT'),
('booking_tickets_description', 'TEXT'),
('booking_tickets_eyebrow', 'TEXT'),
('booking_tickets_title', 'TEXT'),
('booking_venue_description', 'TEXT'),
('booking_venue_eyebrow', 'TEXT'),
('booking_venue_title', 'TEXT'),
('btn_explore_template', 'TEXT'),
('cards_subtitle', 'TEXT'),
('checkout_button_text', 'TEXT'),
('chef_name', 'TEXT'),
('city', 'TEXT'),
('clear_button_text', 'TEXT'),
('continue_exploring_text', 'TEXT'),
('cuisine_type', 'TEXT'),
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
('duration_minutes', 'TEXT'),
('email', 'TEXT'),
('email_label', 'TEXT'),
('email_placeholder', 'TEXT'),
('events_subtitle', 'TEXT'),
('fact1', 'TEXT'),
('fact2', 'TEXT'),
('fact3', 'TEXT'),
('first_name_label', 'TEXT'),
('first_name_placeholder', 'TEXT'),
('formed_text', 'TEXT'),
('gradient_subheading', 'TEXT'),
('hero_back_button_text', 'TEXT'),
('hero_image', 'TEXT'),
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
('historical_locations_view_more_label', 'TEXT'),
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
('intro_closing', 'TEXT'),
('intro_fact', 'TEXT'),
('intro_image_alt', 'TEXT'),
('intro_sub1_heading', 'TEXT'),
('intro_sub1_text', 'TEXT'),
('intro_sub2_heading', 'TEXT'),
('intro_sub2_text', 'TEXT'),
('intro_sub3_heading', 'TEXT'),
('intro_sub3_text', 'TEXT'),
('intro_text', 'TEXT'),
('intro2_body', 'TEXT'),
('intro2_image_alt', 'TEXT'),
('jazz_description', 'TEXT'),
('label_events_count', 'TEXT'),
('label_no_events', 'TEXT'),
('last_name_label', 'TEXT'),
('last_name_placeholder', 'TEXT'),
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
('michelin_stars', 'TEXT'),
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
('page_title', 'TEXT'),
('pay_button_text', 'TEXT'),
('pay_what_you_like_message', 'TEXT'),
('payment_methods_heading', 'TEXT'),
('payment_overview_heading', 'TEXT'),
('performances_description', 'TEXT'),
('performances_heading', 'TEXT'),
('performances_section_id', 'TEXT'),
('performances_text', 'TEXT'),
('personal_info_heading', 'TEXT'),
('personal_info_subtext', 'TEXT'),
('phone', 'TEXT'),
('photo_gallery_description', 'TEXT'),
('photo_gallery_heading', 'TEXT'),
('price_adult', 'TEXT'),
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
('reserve_button_label', 'TEXT'),
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
('route_location7_description', 'TEXT'),
('route_location7_name', 'TEXT'),
('route_location8_description', 'TEXT'),
('route_location8_name', 'TEXT'),
('route_location9_description', 'TEXT'),
('route_location9_name', 'TEXT'),
('route_subheading', 'TEXT'),
('save_details_label', 'TEXT'),
('save_details_subtext', 'TEXT'),
('schedule_additional_info_heading', 'TEXT'),
('schedule_additional_info_title', 'TEXT'),
('schedule_cta_description', 'TEXT'),
('schedule_currency_symbol', 'TEXT'),
('schedule_event_count_label', 'TEXT'),
('schedule_filter_afternoon_label', 'TEXT'),
('schedule_filter_age_group_label', 'TEXT'),
('schedule_filter_all_ages_label', 'TEXT'),
('schedule_filter_all_label', 'TEXT'),
('schedule_filter_day_label', 'TEXT'),
('schedule_filter_dutch_label', 'TEXT'),
('schedule_filter_english_label', 'TEXT'),
('schedule_filter_evening_label', 'TEXT'),
('schedule_filter_fixed_price_label', 'TEXT'),
('schedule_filter_free_label', 'TEXT'),
('schedule_filter_language_label', 'TEXT'),
('schedule_filter_morning_label', 'TEXT'),
('schedule_filter_paid_label', 'TEXT'),
('schedule_filter_pay_as_you_like_label', 'TEXT'),
('schedule_filter_price_type_label', 'TEXT'),
('schedule_filter_reset_text', 'TEXT'),
('schedule_filter_start_time_label', 'TEXT'),
('schedule_filter_time_range_label', 'TEXT'),
('schedule_filter_venue_label', 'TEXT'),
('schedule_filters_label', 'TEXT'),
('schedule_no_events_text', 'TEXT'),
('schedule_pay_what_you_like_text', 'TEXT'),
('schedule_show_additional_info', 'TEXT'),
('schedule_show_event_count', 'TEXT'),
('schedule_show_filters', 'TEXT'),
('schedule_show_story_count', 'TEXT'),
('schedule_start_point', 'TEXT'),
('schedule_story_count_label', 'TEXT'),
('schedule_subtitle_1', 'TEXT'),
('schedule_subtitle_2', 'TEXT'),
('schedule_year', 'TEXT'),
('seats_per_session', 'TEXT'),
('selected_events_heading', 'TEXT'),
('site_name', 'TEXT'),
('special_requests_note', 'TEXT'),
('stars', 'TEXT'),
('storytelling_description', 'TEXT'),
('tax_label', 'TEXT'),
('ticket_options_subheading', 'TEXT'),
('time_slots', 'TEXT'),
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
('venue_grotemarkt_hall_desc', 'TEXT'),
('venue_grotemarkt_hall_info', 'TEXT'),
('venue_grotemarkt_hall_name', 'TEXT'),
('venue_grotemarkt_hall_price', 'TEXT'),
('venue_grotemarkt_location1', 'TEXT'),
('venue_grotemarkt_location2', 'TEXT'),
('venue_grotemarkt_name', 'TEXT'),
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

-- --------------------------------------------------------

--
-- Table structure for table `CmsItemType`
--

CREATE TABLE `CmsItemType` (
  `ItemType` varchar(20) NOT NULL,
  `Description` varchar(120) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `CmsItemType`
--

INSERT INTO `CmsItemType` (`ItemType`, `Description`) VALUES
('BUTTON_TEXT', 'Label displayed on a button or CTA'),
('HEADING', 'Section or card heading text'),
('HTML', 'Rich HTML content with markup'),
('IMAGE_PATH', 'Relative path to a static image asset'),
('LINK', 'URL or anchor link reference'),
('MEDIA', 'Reference to a MediaAsset record'),
('TEXT', 'Plain text content (paragraphs, labels, captions)');

-- --------------------------------------------------------

--
-- Table structure for table `CmsPage`
--

CREATE TABLE `CmsPage` (
  `CmsPageId` int(11) NOT NULL,
  `Slug` varchar(60) NOT NULL,
  `Title` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `CmsPage`
--

INSERT INTO `CmsPage` (`CmsPageId`, `Slug`, `Title`) VALUES
(1, 'home', 'Haarlem Festival Homepage'),
(2, 'storytelling', 'Stories in Haarlem'),
(3, 'history', 'A Stroll Through History'),
(5, 'jazz', 'Haarlem Jazz Festival'),
(6, 'restaurant', 'Yummy! Restaurant Experience'),
(7, 'storytelling-detail', 'Storytelling Detail Pages'),
(9, 'my-program', 'My Program'),
(10, 'checkout', 'Checkout'),
(11, 'grote-markt', 'Grote Markt'),
(12, 'amsterdamse-poort', 'Amsterdaamse Poort'),
(13, 'molen-de-adriaan', 'Molen De Adriaan'),
(14, 'restaurant-detail', 'Restaurant Detail Pages');

-- --------------------------------------------------------

--
-- Table structure for table `CmsSection`
--

CREATE TABLE `CmsSection` (
  `CmsSectionId` int(11) NOT NULL,
  `CmsPageId` int(11) NOT NULL,
  `SectionKey` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `CmsSection`
--

INSERT INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(3, 1, 'about_section'),
(2, 1, 'banner_section'),
(6, 1, 'event_dance'),
(7, 1, 'event_history'),
(5, 1, 'event_jazz'),
(8, 1, 'event_restaurant'),
(9, 1, 'event_storytelling'),
(4, 1, 'events_overview_header'),
(12, 1, 'global_ui'),
(1, 1, 'hero_section'),
(11, 1, 'schedule_section'),
(10, 1, 'venue_map_section'),
(14, 2, 'gradient_section'),
(13, 2, 'hero_section'),
(15, 2, 'intro_split_section'),
(16, 2, 'masonry_section'),
(36, 2, 'schedule_section'),
(45, 3, 'gradient_section'),
(42, 3, 'hero_section'),
(38, 3, 'historical_locations_section'),
(54, 3, 'history_important_tour_info_section'),
(53, 3, 'history_pricing_section'),
(40, 3, 'intro_section'),
(51, 3, 'route_section'),
(76, 3, 'schedule_section'),
(52, 3, 'ticket_options_section'),
(34, 5, 'artists_section'),
(35, 5, 'booking_cta_section'),
(29, 5, 'gradient_section'),
(28, 5, 'hero_section'),
(30, 5, 'intro_section'),
(32, 5, 'pricing_section'),
(33, 5, 'schedule_cta_section'),
(37, 5, 'schedule_section'),
(31, 5, 'venues_section'),
(74, 6, 'detail_section'),
(56, 6, 'gradient_section'),
(55, 6, 'hero_section'),
(59, 6, 'instructions_section'),
(57, 6, 'intro_split_section'),
(58, 6, 'intro_split2_section'),
(60, 6, 'restaurant_cards_section'),
(61, 7, 'event_34'),
(62, 7, 'event_35'),
(63, 7, 'event_36'),
(64, 7, 'event_37'),
(65, 7, 'event_38'),
(66, 7, 'event_39'),
(67, 7, 'event_40'),
(69, 7, 'event_42'),
(70, 7, 'event_43'),
(71, 7, 'event_44'),
(72, 7, 'event_45'),
(73, 7, 'event_46'),
(102, 7, 'event_67'),
(104, 7, 'event_69'),
(79, 9, 'main'),
(80, 10, 'main'),
(83, 11, 'facts_section'),
(81, 11, 'hero_section'),
(82, 11, 'intro_section'),
(84, 11, 'significance_section'),
(87, 12, 'facts_section'),
(85, 12, 'hero_section'),
(86, 12, 'intro_section'),
(88, 12, 'significance_section'),
(91, 13, 'facts_section'),
(89, 13, 'hero_section'),
(90, 13, 'intro_section'),
(92, 13, 'significance_section'),
(93, 14, 'event_47'),
(94, 14, 'event_48'),
(95, 14, 'event_49'),
(96, 14, 'event_50'),
(97, 14, 'event_51'),
(98, 14, 'event_52'),
(99, 14, 'event_53'),
(103, 14, 'event_68'),
(105, 14, 'event_72'),
(106, 14, 'event_73');

-- --------------------------------------------------------

--
-- Table structure for table `CuisineType`
--

CREATE TABLE `CuisineType` (
  `CuisineTypeId` int(11) NOT NULL,
  `Name` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `CuisineType`
--

INSERT INTO `CuisineType` (`CuisineTypeId`, `Name`) VALUES
(1, 'Dutch'),
(2, 'European'),
(3, 'Fish & Seafood'),
(4, 'French'),
(5, 'Modern'),
(6, 'Vegan');

-- --------------------------------------------------------

--
-- Table structure for table `EmailConfirmationToken`
--

CREATE TABLE `EmailConfirmationToken` (
  `EmailConfirmationTokenId` int(11) NOT NULL,
  `UserAccountId` int(11) NOT NULL,
  `Token` varchar(100) NOT NULL,
  `ExpiresAtUtc` datetime NOT NULL,
  `UsedAtUtc` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Event`
--

CREATE TABLE `Event` (
  `EventId` int(11) NOT NULL,
  `EventTypeId` int(11) NOT NULL,
  `Title` varchar(160) NOT NULL,
  `Slug` varchar(180) DEFAULT NULL,
  `DisplayOrder` int(11) DEFAULT NULL,
  `ShortDescription` varchar(300) NOT NULL DEFAULT '',
  `LongDescriptionHtml` text NOT NULL DEFAULT '<p></p>',
  `FeaturedImageAssetId` int(11) DEFAULT NULL,
  `VenueId` int(11) DEFAULT NULL,
  `ArtistId` int(11) DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `Event`
--

INSERT INTO `Event` (`EventId`, `EventTypeId`, `Title`, `Slug`, `DisplayOrder`, `ShortDescription`, `LongDescriptionHtml`, `FeaturedImageAssetId`, `VenueId`, `ArtistId`, `IsActive`, `CreatedAtUtc`) VALUES
(1, 1, 'Gumbo Kings', 'gumbo-kings', NULL, 'Live jazz performance by Gumbo Kings', '<p></p>', NULL, 2, 1, 1, '2026-02-06 15:30:32'),
(2, 1, 'Evolve', 'evolve', NULL, 'Live jazz performance by Evolve', '<p></p>', NULL, 2, 2, 1, '2026-02-06 15:30:32'),
(3, 1, 'Ntjam Rosie', 'ntjam-rosie', NULL, '', '<p></p>', NULL, 1, 3, 1, '2026-02-06 15:30:32'),
(4, 1, 'Wicked Jazz Sounds', 'wicked-jazz-sounds', NULL, 'Live jazz performance by Wicked Jazz Sounds', '<p></p>', NULL, 2, 4, 1, '2026-02-06 15:30:32'),
(5, 1, 'Wouter Hamel', 'wouter-hamel', NULL, 'Live jazz performance by Wouter Hamel', '<p></p>', NULL, 1, 5, 1, '2026-02-06 15:30:32'),
(6, 1, 'Jonna Frazer', 'jonna-frazer', NULL, 'Live jazz performance by Jonna Frazer', '<p></p>', NULL, 1, 6, 1, '2026-02-06 15:30:32'),
(7, 1, 'Karsu', 'karsu', NULL, 'Live jazz performance by Karsu', '<p></p>', NULL, 1, 7, 1, '2026-02-06 15:30:32'),
(8, 1, 'Uncle Sue', 'uncle-sue', NULL, 'Live jazz performance by Uncle Sue', '<p></p>', NULL, 1, 8, 1, '2026-02-06 15:30:32'),
(9, 1, 'Chris Allen', 'chris-allen', NULL, 'Live jazz performance by Chris Allen', '<p></p>', NULL, 1, 9, 1, '2026-02-06 15:30:32'),
(10, 1, 'Myles Sanko', 'myles-sanko', NULL, 'Live jazz performance by Myles Sanko', '<p></p>', NULL, 1, 10, 1, '2026-02-06 15:30:32'),
(11, 1, 'Ilse Huizinga', 'ilse-huizinga', NULL, 'Live jazz performance by Ilse Huizinga', '<p></p>', NULL, 1, 11, 1, '2026-02-06 15:30:32'),
(12, 1, 'Eric Vloeimans and Hotspot!', 'eric-vloeimans-and-hotspot', NULL, 'Live jazz performance by Eric Vloeimans and Hotspot!', '<p></p>', NULL, 1, 12, 1, '2026-02-06 15:30:32'),
(13, 1, 'Gare du Nord', 'gare-du-nord', NULL, 'Live jazz performance by Gare du Nord', '<p></p>', NULL, 2, 13, 1, '2026-02-06 15:30:32'),
(14, 1, 'Rilan & The Bombadiers', 'rilan-the-bombadiers', NULL, 'Live jazz performance by Rilan & The Bombadiers', '<p></p>', NULL, 1, 14, 1, '2026-02-06 15:30:32'),
(15, 1, 'Soul Six', 'soul-six', NULL, 'Live jazz performance by Soul Six', '<p></p>', NULL, 1, 15, 1, '2026-02-06 15:30:32'),
(16, 1, 'Han Bennink', 'han-bennink', NULL, 'Live jazz performance by Han Bennink', '<p></p>', NULL, 1, 16, 1, '2026-02-06 15:30:32'),
(17, 1, 'The Nordanians', 'the-nordanians', NULL, 'Live jazz performance by The Nordanians', '<p></p>', NULL, 2, 17, 1, '2026-02-06 15:30:32'),
(18, 1, 'Lilith Merlot', 'lilith-merlot', NULL, 'Live jazz performance by Lilith Merlot', '<p></p>', NULL, 1, 18, 1, '2026-02-06 15:30:32'),
(19, 1, 'Ruis Soundsystem', 'ruis-soundsystem', NULL, 'Live jazz performance by Ruis Soundsystem', '<p></p>', NULL, 2, 19, 1, '2026-02-06 15:30:32'),
(20, 2, 'Nicky Romero / Afrojack - Back2Back', 'nicky-romero-afrojack-back2back', NULL, 'Back2Back session featuring Nicky Romero and Afrojack', '<p></p>', NULL, 3, NULL, 1, '2026-02-06 15:30:32'),
(21, 2, 'Tiësto - Club', 'ti-sto-club', NULL, 'Club session by Tiësto', '<p></p>', NULL, 4, 22, 1, '2026-02-06 15:30:32'),
(22, 2, 'Hardwell - Club', 'hardwell-club', NULL, 'Club session by Hardwell', '<p></p>', NULL, 5, 23, 1, '2026-02-06 15:30:32'),
(23, 2, 'Armin van Buuren - Club', 'armin-van-buuren-club', NULL, 'Club session by Armin van Buuren', '<p></p>', NULL, NULL, NULL, 0, '2026-02-06 15:30:32'),
(24, 2, 'Martin Garrix - Club', 'martin-garrix-club', NULL, 'Club session by Martin Garrix', '<p></p>', NULL, 7, 24, 1, '2026-02-06 15:30:32'),
(25, 2, 'Hardwell / Martin Garrix / Armin van Buuren - Back2Back', 'hardwell-martin-garrix-armin-van-buuren-back2back', NULL, 'Back2Back session featuring Hardwell, Martin Garrix and Armin van Buuren', '<p></p>', NULL, 8, NULL, 1, '2026-02-06 15:30:32'),
(26, 2, 'Afrojack - Club', 'afrojack-club', NULL, 'Club session by Afrojack', '<p></p>', NULL, NULL, NULL, 0, '2026-02-06 15:30:32'),
(27, 2, 'Tiësto - TiëstoWorld', 'ti-sto-ti-stoworld', NULL, 'TiëstoWorld session by Tiësto', '<p></p>', NULL, 3, 22, 1, '2026-02-06 15:30:32'),
(28, 2, 'Nicky Romero - Club', 'nicky-romero-club', NULL, 'Club session by Nicky Romero', '<p></p>', NULL, 4, 20, 1, '2026-02-06 15:30:32'),
(29, 2, 'Afrojack / Tiësto / Nicky Romero - Back2Back', 'afrojack-ti-sto-nicky-romero-back2back', NULL, 'Back2Back session featuring Afrojack, Tiësto and Nicky Romero', '<p></p>', NULL, NULL, NULL, 0, '2026-02-06 15:30:32'),
(30, 2, 'Armin van Buuren - Club (Sunday)', 'armin-van-buuren-club-sunday', NULL, 'Club session by Armin van Buuren', '<p></p>', NULL, 5, 25, 1, '2026-02-06 15:30:32'),
(31, 2, 'Hardwell - Club (Sunday)', 'hardwell-club-sunday', NULL, 'Club session by Hardwell', '<p></p>', NULL, 6, 23, 1, '2026-02-06 15:30:32'),
(32, 2, 'Martin Garrix - Club (Sunday)', 'martin-garrix-club-sunday', NULL, 'Club session by Martin Garrix', '<p></p>', NULL, 4, 24, 1, '2026-02-06 15:30:32'),
(33, 3, 'A Stroll Through History', 'a-stroll-through-history', NULL, 'Guided walking tour through historic Haarlem. Due to the nature of this walk, participants must be a minimum of 12 years old and no strollers are allowed.', '<p></p>', NULL, 15, NULL, 1, '2026-02-06 15:30:32'),
(34, 4, 'Winnie de Poeh (4+)', 'winnie-de-poeh-4', NULL, 'Stories for the whole family', '<p></p>', NULL, NULL, NULL, 1, '2026-02-06 15:30:32'),
(35, 4, 'Omdenken Podcast', 'omdenken-podcast', NULL, 'Recording podcast with audience', '<p></p>', NULL, 11, NULL, 1, '2026-02-06 15:30:32'),
(36, 4, 'The story of Buurderij Haarlem', 'the-story-of-buurderij-haarlem', NULL, 'Stories with impact', '<p></p>', NULL, 12, NULL, 1, '2026-02-06 15:30:32'),
(37, 4, 'Corrie voor kinderen', 'corrie-voor-kinderen', NULL, 'Stories for the whole family', '<p></p>', NULL, 13, NULL, 1, '2026-02-06 15:30:32'),
(38, 4, 'Winnaars van verhalenvertel wedstrijd, verhalen voor Haarlem', 'winnaars-van-verhalenvertel-wedstrijd-verhalen-voor-haarlem', NULL, 'Best off - the contest will be organized in June', '<p></p>', NULL, 10, NULL, 1, '2026-02-06 15:30:32'),
(39, 4, 'Het verhaal van de Oeserzwammerij', 'het-verhaal-van-de-oeserzwammerij', NULL, 'Stories with impact', '<p></p>', NULL, 12, NULL, 1, '2026-02-06 15:30:32'),
(40, 4, 'Flip Thinking Podcast', 'flip-thinking-podcast', NULL, 'Recording podcast with audience', '<p></p>', NULL, 11, NULL, 1, '2026-02-06 15:30:32'),
(42, 4, 'Mister Anansi', 'mister-anansi', NULL, 'Stories for the whole family', '<p></p>', NULL, 14, NULL, 1, '2026-02-06 15:30:32'),
(43, 4, 'Podcastlast Haarlem Special', 'podcastlast-haarlem-special', NULL, 'Recording podcast with audience', '<p></p>', NULL, 11, NULL, 1, '2026-02-06 15:30:32'),
(44, 4, 'De geschiedenis van familie ten Boom', 'de-geschiedenis-van-familie-ten-boom', NULL, 'Stories with impact', '<p></p>', NULL, 13, NULL, 1, '2026-02-06 15:30:32'),
(45, 4, 'The history of the Ten Boom Family', 'the-history-of-the-ten-boom-family', NULL, 'Stories with impact', '<p></p>', NULL, 13, NULL, 1, '2026-02-06 15:30:32'),
(46, 4, 'Winners of story telling competition, stories for Haarlem', 'winners-of-story-telling-competition-stories-for-haarlem', NULL, 'Best off - the contest will be organized in June', '<p></p>', NULL, 10, NULL, 1, '2026-02-06 15:30:32'),
(47, 5, 'Café de Roemer - Festival Dinner', 'caf-de-roemer-festival-dinner', 3, 'Dutch, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', 75, NULL, NULL, 1, '2026-02-06 15:30:32'),
(48, 5, 'Ratatouille - Festival Dinner', 'ratatouille-festival-dinner', 1, 'French, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', 76, NULL, NULL, 1, '2026-02-06 15:30:32'),
(49, 5, 'Restaurant ML - Festival Dinner', 'restaurant-ml-festival-dinner', 7, 'Dutch, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', 77, NULL, NULL, 1, '2026-02-06 15:30:32'),
(50, 5, 'Restaurant Fris - Festival Dinner', 'restaurant-fris-festival-dinner', 6, 'Dutch, French, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', 78, NULL, NULL, 1, '2026-02-06 15:30:32'),
(51, 5, 'New Vegas - Festival Dinner', 'new-vegas-festival-dinner', 5, '', '<p></p>', NULL, NULL, NULL, 1, '2026-02-06 15:30:32'),
(52, 5, 'Grand Cafe Brinkman - Festival Dinner', 'grand-cafe-brinkman-festival-dinner', 4, 'Dutch, European, Modern cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', 80, NULL, NULL, 1, '2026-02-06 15:30:32'),
(53, 5, 'Urban Frenchy Bistro Toujours - Festival Dinner', 'urban-frenchy-bistro-toujours-festival-dinner', 2, 'Dutch, fish and seafood, European cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', 81, NULL, NULL, 1, '2026-02-06 15:30:32'),
(57, 3, 'E2E Test History Tour', NULL, NULL, 'Automated test history tour', '<p>A guided walking tour through historic Haarlem.</p>', NULL, 15, NULL, 0, '2026-03-30 17:57:20'),
(58, 4, 'E2E Test Storytelling Event', NULL, NULL, 'Automated test storytelling session', '<p>An evening of captivating stories.</p>', NULL, NULL, NULL, 0, '2026-03-30 17:57:24'),
(59, 5, 'E2E Test Restaurant Event', 'e2e-test-restaurant-event', NULL, 'Automated test restaurant dinner', '<p>Fine dining experience for the festival.</p>', NULL, NULL, NULL, 0, '2026-03-30 17:57:29'),
(66, 4, 'test', NULL, NULL, 'testing just because yest', '<p></p>', NULL, NULL, NULL, 0, '2026-04-02 17:51:22'),
(67, 4, 'Test Storytelling Event Commit3', 'test-storytelling-event-commit3', NULL, 'Test', '<p>Test</p>', NULL, NULL, NULL, 1, '2026-04-05 19:33:14'),
(68, 5, 'Test Restaurant Event Commit3', 'test-restaurant-event-commit3', NULL, 'Test', '<p>Test</p>', NULL, NULL, NULL, 1, '2026-04-05 19:33:23'),
(69, 4, 'this is test 1', 'this-is-test-1', NULL, 'this is test 1', 'this is test 1', NULL, 12, NULL, 1, '2026-04-05 20:09:38'),
(70, 1, 'this is test 2', 'this-is-test-2', NULL, 'this is test 2', '<p></p>', NULL, 6, 25, 0, '2026-04-05 20:11:55'),
(71, 3, 'this is test 3', 'this-is-test-3', NULL, 'this is test 3', 'this is test 3', NULL, 15, NULL, 1, '2026-04-05 20:13:29'),
(72, 5, 'test number 5', 'test-number-5', NULL, 'test number 5', '<p></p>', 119, 8, NULL, 1, '2026-04-05 20:16:11'),
(73, 5, 'Test CMS Create Restaurant', 'test-cms-create-restaurant', NULL, 'Test desc', '<p>Test</p>', NULL, NULL, NULL, 0, '2026-04-05 21:28:15'),
(74, 1, 'TESTING with kian', 'testing-with-kian', NULL, 'kian', 'kian', NULL, 2, 15, 0, '2026-04-06 08:26:14');

-- --------------------------------------------------------

--
-- Table structure for table `EventGalleryImage`
--

CREATE TABLE `EventGalleryImage` (
  `EventGalleryImageId` int(11) NOT NULL,
  `EventId` int(11) NOT NULL,
  `ImagePath` varchar(500) NOT NULL,
  `ImageType` varchar(20) NOT NULL DEFAULT 'gallery',
  `SortOrder` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `EventGalleryImage`
--

INSERT INTO `EventGalleryImage` (`EventGalleryImageId`, `EventId`, `ImagePath`, `ImageType`, `SortOrder`) VALUES
(1, 34, '/assets/Image/storytelling/winnie-the-pooh.jpg', 'gallery', 1),
(2, 34, '/assets/Image/storytelling/WinnieThePoohHeader.png', 'gallery', 2),
(3, 34, '/assets/Image/storytelling/d-student.jpg', 'gallery', 3),
(4, 34, '/assets/Image/storytelling/pig.jpg', 'gallery', 4),
(5, 34, '/assets/Image/storytelling/entrance-kweek.jpg', 'gallery', 5),
(6, 35, '/assets/Image/storytelling/building.jpg', 'gallery', 1),
(7, 35, '/assets/Image/storytelling/entrance-kweek.jpg', 'gallery', 2),
(8, 35, '/assets/Image/storytelling/d-student.jpg', 'gallery', 3),
(9, 35, '/assets/Image/storytelling/d-student2.jpg', 'gallery', 4),
(10, 35, '/assets/Image/storytelling/m-student.jpg', 'gallery', 5),
(11, 35, '/assets/Image/storytelling/building.jpg', 'about', 1),
(12, 35, '/assets/Image/storytelling/entrance-kweek.jpg', 'about', 2),
(13, 36, '/assets/Image/storytelling/entrance-kweek.jpg', 'gallery', 1),
(14, 36, '/assets/Image/storytelling/building.jpg', 'gallery', 2),
(15, 36, '/assets/Image/storytelling/where-stories-come-alive.jpg', 'gallery', 3),
(16, 36, '/assets/Image/storytelling/picture-looking-text.jpg', 'gallery', 4),
(17, 36, '/assets/Image/storytelling/d-student.jpg', 'gallery', 5),
(18, 36, '/assets/Image/storytelling/entrance-kweek.jpg', 'about', 1),
(19, 36, '/assets/Image/storytelling/building.jpg', 'about', 2),
(20, 37, '/assets/Image/storytelling/d-student.jpg', 'gallery', 1),
(21, 37, '/assets/Image/storytelling/d-student2.jpg', 'gallery', 2),
(22, 37, '/assets/Image/storytelling/m-student.jpg', 'gallery', 3),
(23, 37, '/assets/Image/storytelling/entrance-kweek.jpg', 'gallery', 4),
(24, 37, '/assets/Image/storytelling/building.jpg', 'gallery', 5),
(25, 37, '/assets/Image/storytelling/d-student.jpg', 'about', 1),
(26, 37, '/assets/Image/storytelling/d-student2.jpg', 'about', 2),
(27, 38, '/assets/Image/storytelling/d-student.jpg', 'gallery', 1),
(28, 38, '/assets/Image/storytelling/d-student2.jpg', 'gallery', 2),
(29, 38, '/assets/Image/storytelling/m-student.jpg', 'gallery', 3),
(30, 38, '/assets/Image/storytelling/building.jpg', 'gallery', 4),
(31, 38, '/assets/Image/storytelling/entrance-kweek.jpg', 'gallery', 5),
(32, 38, '/assets/Image/storytelling/building.jpg', 'about', 1),
(33, 38, '/assets/Image/storytelling/entrance-kweek.jpg', 'about', 2),
(34, 39, '/assets/Image/storytelling/entrance-kweek.jpg', 'gallery', 1),
(35, 39, '/assets/Image/storytelling/building.jpg', 'gallery', 2),
(36, 39, '/assets/Image/storytelling/picture-looking-text.jpg', 'gallery', 3),
(37, 39, '/assets/Image/storytelling/d-student.jpg', 'gallery', 4),
(38, 39, '/assets/Image/storytelling/m-student.jpg', 'gallery', 5),
(39, 39, '/assets/Image/storytelling/entrance-kweek.jpg', 'about', 1),
(40, 39, '/assets/Image/storytelling/building.jpg', 'about', 2),
(41, 40, '/assets/Image/storytelling/building.jpg', 'gallery', 1),
(42, 40, '/assets/Image/storytelling/entrance-kweek.jpg', 'gallery', 2),
(43, 40, '/assets/Image/storytelling/d-student.jpg', 'gallery', 3),
(44, 40, '/assets/Image/storytelling/d-student2.jpg', 'gallery', 4),
(45, 40, '/assets/Image/storytelling/m-student.jpg', 'gallery', 5),
(46, 40, '/assets/Image/storytelling/building.jpg', 'about', 1),
(47, 40, '/assets/Image/storytelling/d-student2.jpg', 'about', 2),
(55, 42, '/assets/Image/storytelling/anansi-pointing.png', 'gallery', 1),
(56, 42, '/assets/Image/storytelling/anansi-visser.jpg', 'gallery', 2),
(57, 42, '/assets/Image/storytelling/anansi-conversation.jpg', 'gallery', 3),
(58, 42, '/assets/Image/storytelling/anansi-drip.jpg', 'gallery', 4),
(59, 42, '/assets/Image/storytelling/building.jpg', 'gallery', 5),
(60, 42, '/assets/Image/storytelling/anansi-pointing.png', 'about', 1),
(61, 42, '/assets/Image/storytelling/anansi-visser.jpg', 'about', 2),
(62, 43, '/assets/Image/storytelling/building.jpg', 'gallery', 1),
(63, 43, '/assets/Image/storytelling/entrance-kweek.jpg', 'gallery', 2),
(64, 43, '/assets/Image/storytelling/d-student.jpg', 'gallery', 3),
(65, 43, '/assets/Image/storytelling/d-student2.jpg', 'gallery', 4),
(66, 43, '/assets/Image/storytelling/m-student.jpg', 'gallery', 5),
(67, 43, '/assets/Image/storytelling/building.jpg', 'about', 1),
(68, 43, '/assets/Image/storytelling/entrance-kweek.jpg', 'about', 2),
(69, 44, '/assets/Image/storytelling/building.jpg', 'gallery', 1),
(70, 44, '/assets/Image/storytelling/entrance-kweek.jpg', 'gallery', 2),
(71, 44, '/assets/Image/storytelling/picture-looking-text.jpg', 'gallery', 3),
(72, 44, '/assets/Image/storytelling/d-student.jpg', 'gallery', 4),
(73, 44, '/assets/Image/storytelling/m-student.jpg', 'gallery', 5),
(74, 44, '/assets/Image/storytelling/building.jpg', 'about', 1),
(75, 44, '/assets/Image/storytelling/entrance-kweek.jpg', 'about', 2),
(76, 45, '/assets/Image/storytelling/building.jpg', 'gallery', 1),
(77, 45, '/assets/Image/storytelling/picture-looking-text.jpg', 'gallery', 2),
(78, 45, '/assets/Image/storytelling/entrance-kweek.jpg', 'gallery', 3),
(79, 45, '/assets/Image/storytelling/d-student.jpg', 'gallery', 4),
(80, 45, '/assets/Image/storytelling/m-student.jpg', 'gallery', 5),
(81, 45, '/assets/Image/storytelling/building.jpg', 'about', 1),
(82, 45, '/assets/Image/storytelling/picture-looking-text.jpg', 'about', 2),
(83, 46, '/assets/Image/storytelling/d-student.jpg', 'gallery', 1),
(84, 46, '/assets/Image/storytelling/d-student2.jpg', 'gallery', 2),
(85, 46, '/assets/Image/storytelling/m-student.jpg', 'gallery', 3),
(86, 46, '/assets/Image/storytelling/building.jpg', 'gallery', 4),
(87, 46, '/assets/Image/storytelling/entrance-kweek.jpg', 'gallery', 5),
(88, 46, '/assets/Image/storytelling/d-student.jpg', 'about', 1),
(89, 46, '/assets/Image/storytelling/m-student.jpg', 'about', 2);

-- --------------------------------------------------------

--
-- Table structure for table `EventHighlight`
--

CREATE TABLE `EventHighlight` (
  `EventHighlightId` int(11) NOT NULL,
  `EventId` int(11) NOT NULL,
  `Title` varchar(160) NOT NULL,
  `Description` text NOT NULL DEFAULT '',
  `ImagePath` varchar(500) NOT NULL DEFAULT '',
  `SortOrder` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `EventHighlight`
--

INSERT INTO `EventHighlight` (`EventHighlightId`, `EventId`, `Title`, `Description`, `ImagePath`, `SortOrder`) VALUES
(1, 35, 'Live Recording', 'Be part of a live podcast recording in front of an audience.', '/assets/Image/storytelling/building.jpg', 1),
(2, 35, 'Audience Participation', 'Your questions and insights shape the conversation.', '/assets/Image/storytelling/d-student2.jpg', 2),
(3, 35, 'New Perspectives', 'Discover how reframing challenges can lead to creative solutions.', '/assets/Image/storytelling/m-student.jpg', 3),
(4, 36, 'From Farmer to Neighbour', 'A personal story about how local farmers connect directly with Haarlem residents, focusing on trust, fair pricing, and the journey food takes from land to table.', '/assets/Image/storytelling/entrance-kweek.jpg', 1),
(5, 36, 'Building a Local Food Community', 'How Buurderij Haarlem became more than a marketplace - weekly meetups created friendships and a shared sense of responsibility for local food.', '/assets/Image/storytelling/building.jpg', 2),
(6, 36, 'Food with a Future', 'How Buurderij Haarlem supports seasonal food, reduces waste, and encourages conscious consumption for future generations.', '/assets/Image/storytelling/picture-looking-text.jpg', 3),
(7, 37, 'Stories for Every Child', 'An accessible and joyful storytelling session designed with children in mind.', '/assets/Image/storytelling/d-student.jpg', 1),
(8, 37, 'Imagination in Action', 'Watch as the performer brings characters to life through voice, gesture, and storytelling.', '/assets/Image/storytelling/d-student2.jpg', 2),
(9, 37, 'A Shared Experience', 'A session that brings families and communities together around shared stories.', '/assets/Image/storytelling/m-student.jpg', 3),
(10, 38, 'Competition Winners', 'Hear from the storytellers who won the Haarlem storytelling competition.', '/assets/Image/storytelling/d-student.jpg', 1),
(11, 38, 'Stories About Haarlem', 'Each winner tells a story rooted in the city culture and everyday life.', '/assets/Image/storytelling/m-student.jpg', 2),
(12, 38, 'Community Voice', 'The competition celebrates storytelling as a way to strengthen community bonds.', '/assets/Image/storytelling/d-student2.jpg', 3),
(13, 39, 'Sustainable Innovation', 'Learn how local entrepreneurs are growing food sustainably in the city.', '/assets/Image/storytelling/entrance-kweek.jpg', 1),
(14, 39, 'Community Impact', 'The Oeserzwammerij connects growers, volunteers, and consumers.', '/assets/Image/storytelling/building.jpg', 2),
(15, 39, 'From Waste to Food', 'Using coffee grounds and organic waste to grow nutritious mushrooms.', '/assets/Image/storytelling/picture-looking-text.jpg', 3),
(16, 40, 'Reframe Your World', 'Learn practical techniques for turning obstacles into opportunities.', '/assets/Image/storytelling/d-student.jpg', 1),
(17, 40, 'Live Podcast Recording', 'Experience the magic of a podcast recorded before a live audience.', '/assets/Image/storytelling/building.jpg', 2),
(18, 40, 'Q and A with Audience', 'After the recording, guests take questions from the audience.', '/assets/Image/storytelling/m-student.jpg', 3),
(22, 42, 'In English', 'The Anansi story performed in English for international audiences.', '/assets/Image/storytelling/anansi-pointing.png', 1),
(23, 42, 'Universal Themes', 'Stories of cunning, community, and creativity that transcend cultures.', '/assets/Image/storytelling/anansi-conversation.jpg', 2),
(24, 42, 'World Folklore on Stage', 'Experience the rich tradition of West African oral storytelling at Haarlem Festival.', '/assets/Image/storytelling/anansi-drip.jpg', 3),
(25, 43, 'Haarlem Special Edition', 'A one-of-a-kind live episode recorded exclusively at the festival.', '/assets/Image/storytelling/building.jpg', 1),
(26, 43, 'Local Conversations', 'Featuring guests from Haarlem creative and cultural scene.', '/assets/Image/storytelling/d-student.jpg', 2),
(27, 43, 'Live Audience Experience', 'Be part of the recording and feel the energy of a live podcast taping.', '/assets/Image/storytelling/m-student.jpg', 3),
(28, 44, 'A Story of Courage', 'The ten Boom family risked everything to shelter Jews during the occupation.', '/assets/Image/storytelling/building.jpg', 1),
(29, 44, 'Haarlem in History', 'Explore how one family home became a symbol of resistance and compassion.', '/assets/Image/storytelling/picture-looking-text.jpg', 2),
(30, 44, 'Legacy and Memory', 'Their story lives on as an inspiration for future generations.', '/assets/Image/storytelling/entrance-kweek.jpg', 3),
(31, 45, 'In English', 'The ten Boom family story told for international visitors.', '/assets/Image/storytelling/building.jpg', 1),
(32, 45, 'Wartime Haarlem', 'Step back in time to a Haarlem that faced unimaginable choices.', '/assets/Image/storytelling/picture-looking-text.jpg', 2),
(33, 45, 'Hope and Resilience', 'A story about human courage that resonates across generations and borders.', '/assets/Image/storytelling/entrance-kweek.jpg', 3),
(34, 46, 'Competition Winners', 'The English-language winners of the storytelling competition share their best work.', '/assets/Image/storytelling/d-student.jpg', 1),
(35, 46, 'Stories About Haarlem', 'Each story is rooted in the life and character of the city.', '/assets/Image/storytelling/d-student2.jpg', 2),
(36, 46, 'New Voices', 'Discover emerging storytelling talent from Haarlem and beyond.', '/assets/Image/storytelling/m-student.jpg', 3);

-- --------------------------------------------------------

--
-- Table structure for table `EventSession`
--

CREATE TABLE `EventSession` (
  `EventSessionId` int(11) NOT NULL,
  `EventId` int(11) NOT NULL,
  `VenueId` int(11) DEFAULT NULL,
  `StartDateTime` datetime NOT NULL,
  `EndDateTime` datetime DEFAULT NULL,
  `CapacityTotal` int(11) NOT NULL,
  `CapacitySingleTicketLimit` int(11) NOT NULL,
  `SeatsAvailable` int(11) DEFAULT NULL,
  `SoldSingleTickets` int(11) NOT NULL DEFAULT 0,
  `SoldReservedSeats` int(11) NOT NULL DEFAULT 0,
  `HallName` varchar(80) DEFAULT NULL,
  `SessionType` varchar(40) DEFAULT NULL,
  `DurationMinutes` int(11) DEFAULT NULL,
  `LanguageCode` varchar(10) DEFAULT NULL,
  `MinAge` int(11) DEFAULT NULL,
  `MaxAge` int(11) DEFAULT NULL,
  `ReservationRequired` tinyint(1) NOT NULL DEFAULT 0,
  `IsFree` tinyint(1) NOT NULL DEFAULT 0,
  `Notes` varchar(400) NOT NULL DEFAULT '',
  `HistoryTicketLabel` varchar(120) DEFAULT NULL,
  `CtaLabel` varchar(60) DEFAULT NULL,
  `CtaUrl` varchar(255) DEFAULT NULL,
  `IsCancelled` tinyint(1) NOT NULL DEFAULT 0,
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp(),
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `EventSession`
--

INSERT INTO `EventSession` (`EventSessionId`, `EventId`, `VenueId`, `StartDateTime`, `EndDateTime`, `CapacityTotal`, `CapacitySingleTicketLimit`, `SeatsAvailable`, `SoldSingleTickets`, `SoldReservedSeats`, `HallName`, `SessionType`, `DurationMinutes`, `LanguageCode`, `MinAge`, `MaxAge`, `ReservationRequired`, `IsFree`, `Notes`, `HistoryTicketLabel`, `CtaLabel`, `CtaUrl`, `IsCancelled`, `CreatedAtUtc`, `IsActive`) VALUES
(1, 1, 1, '2026-07-23 18:00:00', '2026-07-23 19:00:00', 300, 270, NULL, 7, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €35,00, All-Access pass for Thu, Fri, Sat: €80,00.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(2, 2, 1, '2026-07-23 19:30:00', '2026-07-23 20:30:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(3, 3, 1, '2026-07-23 21:00:00', '2026-07-23 22:00:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(4, 4, 1, '2026-07-23 18:00:00', '2026-07-23 19:00:00', 200, 180, NULL, 10, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(5, 5, 1, '2026-07-23 19:30:00', '2026-07-23 20:30:00', 200, 200, NULL, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(6, 6, 1, '2026-07-23 21:00:00', '2026-07-23 22:00:00', 200, 200, NULL, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(7, 7, 1, '2026-07-24 18:00:00', '2026-07-24 19:00:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €35,00, All-Access pass for Thu, Fri, Sat: €80,00.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(8, 8, 1, '2026-07-24 19:30:00', '2026-07-24 20:30:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(9, 9, 1, '2026-07-24 21:00:00', '2026-07-24 22:00:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(10, 10, 1, '2026-07-24 18:00:00', '2026-07-24 19:00:00', 200, 200, NULL, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(11, 11, 1, '2026-07-24 19:30:00', '2026-07-24 20:30:00', 200, 200, NULL, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(12, 12, 1, '2026-07-24 21:00:00', '2026-07-24 22:00:00', 200, 200, NULL, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(13, 13, 1, '2026-07-25 18:00:00', '2026-07-25 19:00:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €35,00, All-Access pass for Thu, Fri, Sat: €80,00.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(14, 14, 1, '2026-07-25 19:30:00', '2026-07-25 20:30:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(15, 15, 1, '2026-07-25 21:00:00', '2026-07-25 22:00:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(16, 16, 1, '2026-07-25 18:00:00', '2026-07-25 19:00:00', 150, 150, NULL, 0, 0, 'Third Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(17, 17, 1, '2026-07-25 19:30:00', '2026-07-25 20:30:00', 150, 150, NULL, 0, 0, 'Third Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(18, 18, 1, '2026-07-25 21:00:00', '2026-07-25 22:00:00', 150, 150, NULL, 0, 0, 'Third Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(19, 19, 2, '2026-07-26 15:00:00', '2026-07-26 16:00:00', 300, 300, NULL, 0, 0, 'Outdoor Stage', 'Live', 60, NULL, NULL, NULL, 0, 1, 'Free for all visitors. No reservation needed.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(20, 4, 2, '2026-07-26 16:00:00', '2026-07-26 17:00:00', 300, 300, NULL, 0, 0, 'Outdoor Stage', 'Live', 60, NULL, NULL, NULL, 0, 1, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(21, 2, 2, '2026-07-26 17:00:00', '2026-07-26 18:00:00', 300, 300, NULL, 0, 0, 'Outdoor Stage', 'Live', 60, NULL, NULL, NULL, 0, 1, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(22, 17, 2, '2026-07-26 18:00:00', '2026-07-26 19:00:00', 300, 300, NULL, 0, 0, 'Outdoor Stage', 'Live', 60, NULL, NULL, NULL, 0, 1, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(23, 1, 2, '2026-07-26 19:00:00', '2026-07-26 20:00:00', 300, 300, NULL, 0, 0, 'Outdoor Stage', 'Live', 60, NULL, NULL, NULL, 0, 1, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(24, 13, 2, '2026-07-26 20:00:00', '2026-07-26 21:00:00', 300, 300, NULL, 0, 0, 'Outdoor Stage', 'Live', 60, NULL, NULL, NULL, 0, 1, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(25, 20, 3, '2026-07-24 20:00:00', '2026-07-25 02:00:00', 1500, 1350, NULL, 0, 0, NULL, 'Back2Back', 360, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €125,00, All-Access pass for Fri, Sat, Sun: €250,00.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(26, 21, 4, '2026-07-24 22:00:00', '2026-07-24 23:30:00', 200, 180, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(27, 22, 5, '2026-07-24 23:00:00', '2026-07-25 00:30:00', 300, 270, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(28, 23, NULL, '2026-07-24 22:00:00', '2026-07-24 23:30:00', 200, 180, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(29, 24, 7, '2026-07-24 22:00:00', '2026-07-24 23:30:00', 200, 180, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(30, 25, 8, '2026-07-25 14:00:00', '2026-07-25 23:00:00', 2000, 1800, NULL, 0, 0, NULL, 'Back2Back', 540, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €150,00, All-Access pass for Fri, Sat, Sun: €250,00.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(31, 26, NULL, '2026-07-25 22:00:00', '2026-07-25 23:30:00', 300, 270, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(32, 27, 3, '2026-07-25 21:00:00', '2026-07-26 01:00:00', 1500, 1350, NULL, 0, 0, NULL, 'TiëstoWorld', 240, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(33, 28, 4, '2026-07-25 23:00:00', '2026-07-26 00:30:00', 200, 180, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(34, 29, NULL, '2026-07-26 14:00:00', '2026-07-26 23:00:00', 2000, 1800, NULL, 0, 0, NULL, 'Back2Back', 540, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €150,00, All-Access pass for Fri, Sat, Sun: €250,00.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(35, 30, 5, '2026-07-26 19:00:00', '2026-07-26 20:30:00', 300, 270, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(36, 31, 6, '2026-07-26 21:00:00', '2026-07-26 22:30:00', 1500, 1350, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(37, 32, 4, '2026-07-26 18:00:00', '2026-07-26 19:30:00', 200, 180, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(38, 33, 15, '2026-07-23 10:00:00', '2026-07-23 12:00:00', 24, 21, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, 'Due to the nature of this walk participants must be a minimum of 12 years old and no strollers are allowed. Groups will consist of 12 participants + 1 guide.', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(39, 33, 15, '2026-07-23 13:00:00', '2026-07-23 15:00:00', 24, 21, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(40, 33, 15, '2026-07-23 16:00:00', '2026-07-23 18:00:00', 24, 21, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(41, 33, 15, '2026-07-24 10:00:00', '2026-07-24 12:00:00', 24, 21, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(42, 33, 15, '2026-07-24 13:00:00', '2026-07-24 15:00:00', 36, 32, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(43, 33, 15, '2026-07-24 16:00:00', '2026-07-24 18:00:00', 24, 21, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(44, 33, 15, '2026-07-25 10:00:00', '2026-07-25 12:00:00', 48, 43, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(45, 33, 15, '2026-07-25 13:00:00', '2026-07-25 15:00:00', 60, 54, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(46, 33, 15, '2026-07-25 16:00:00', '2026-07-25 18:00:00', 36, 32, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(47, 33, 15, '2026-07-26 10:00:00', '2026-07-26 12:00:00', 60, 54, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(48, 33, 15, '2026-07-26 13:00:00', '2026-07-26 15:00:00', 96, 86, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(49, 33, 15, '2026-07-26 16:00:00', '2026-07-26 18:00:00', 24, 21, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(50, 34, NULL, '2026-07-23 16:00:00', '2026-07-23 17:00:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 60, 'NL', 4, NULL, 0, 0, 'Stories for the whole family', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(51, 35, 11, '2026-07-23 19:00:00', '2026-07-23 20:15:00', 100, 90, NULL, 0, 0, NULL, 'Podcast', 75, 'NL', 16, NULL, 0, 0, 'Recording podcast with audience', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(52, 36, 12, '2026-07-23 20:30:00', '2026-07-23 21:45:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 75, 'ENG', 16, NULL, 0, 0, 'Stories with impact', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(53, 37, 13, '2026-07-24 16:00:00', '2026-07-24 17:00:00', 80, 72, NULL, 0, 0, NULL, 'Storytelling', 60, 'NL', 10, NULL, 0, 0, 'Stories for the whole family', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(54, 38, 10, '2026-07-24 19:00:00', '2026-07-24 20:30:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 90, 'NL', 12, NULL, 0, 0, 'Best off - the contest will be organized in June', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(55, 39, 12, '2026-07-24 19:00:00', '2026-07-24 20:15:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 75, 'NL', 16, NULL, 0, 0, 'Stories with impact', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(56, 40, 11, '2026-07-24 20:30:00', '2026-07-24 21:45:00', 100, 90, NULL, 0, 0, NULL, 'Podcast', 75, 'ENG', 16, NULL, 0, 0, 'Recording podcast with audience', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(57, 42, 14, '2026-07-25 10:00:00', '2026-07-25 11:00:00', 100, 90, NULL, 8, 0, NULL, 'Storytelling', 60, 'NL', 2, 102, 0, 0, 'Stories for the whole family', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(58, 42, 14, '2026-07-25 15:00:00', '2026-07-25 16:00:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 60, 'ENG', 2, 102, 0, 0, 'Stories for the whole family', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(59, 43, 11, '2026-07-25 14:00:00', '2026-07-25 15:15:00', 100, 90, NULL, 0, 0, NULL, 'Podcast', 75, 'NL', 12, NULL, 0, 0, 'Recording podcast with audience', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(60, 44, 13, '2026-07-25 13:00:00', '2026-07-25 14:30:00', 80, 72, NULL, 0, 0, NULL, 'Storytelling', 90, 'NL', 12, NULL, 0, 0, 'Stories with impact', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(61, 42, 14, '2026-07-26 10:00:00', '2026-07-26 11:00:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 60, 'ENG', 2, 102, 0, 0, 'Stories for the whole family', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(62, 42, 14, '2026-07-26 15:00:00', '2026-07-26 16:00:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 60, 'NL', 2, 102, 0, 0, 'Stories for the whole family', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(63, 45, 13, '2026-07-26 13:00:00', '2026-07-26 14:30:00', 80, 72, NULL, 0, 0, NULL, 'Storytelling', 90, 'ENG', 12, NULL, 0, 0, 'Stories with impact', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(64, 46, 10, '2026-07-26 16:00:00', '2026-07-26 17:30:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 90, 'ENG', 12, NULL, 0, 0, 'Best off - the contest will be organized in June', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(65, 47, NULL, '2026-07-24 18:00:00', '2026-07-24 19:30:00', 35, 31, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(66, 47, NULL, '2026-07-24 19:30:00', '2026-07-24 21:00:00', 35, 31, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(67, 47, NULL, '2026-07-24 21:00:00', '2026-07-24 22:30:00', 35, 31, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(68, 48, NULL, '2026-07-24 17:00:00', '2026-07-24 19:00:00', 52, 46, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(69, 48, NULL, '2026-07-24 19:00:00', '2026-07-24 21:00:00', 52, 46, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(70, 48, NULL, '2026-07-24 21:00:00', '2026-07-24 23:00:00', 52, 46, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(71, 49, NULL, '2026-07-24 17:00:00', '2026-07-24 19:00:00', 60, 54, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(72, 49, NULL, '2026-07-24 19:00:00', '2026-07-24 21:00:00', 60, 54, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(73, 50, NULL, '2026-07-24 17:30:00', '2026-07-24 19:00:00', 45, 40, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(74, 50, NULL, '2026-07-24 19:00:00', '2026-07-24 20:30:00', 45, 40, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(75, 50, NULL, '2026-07-24 20:30:00', '2026-07-24 22:00:00', 45, 40, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(76, 51, NULL, '2026-07-24 17:00:00', '2026-07-24 18:30:00', 36, 32, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(77, 51, NULL, '2026-07-24 18:30:00', '2026-07-24 20:00:00', 36, 32, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(78, 51, NULL, '2026-07-24 20:00:00', '2026-07-24 21:30:00', 36, 32, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(79, 52, NULL, '2026-07-24 16:30:00', '2026-07-24 18:00:00', 100, 90, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(80, 52, NULL, '2026-07-24 18:00:00', '2026-07-24 19:30:00', 100, 90, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(81, 52, NULL, '2026-07-24 19:30:00', '2026-07-24 21:00:00', 100, 90, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(82, 53, NULL, '2026-07-24 17:30:00', '2026-07-24 19:00:00', 48, 43, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(83, 53, NULL, '2026-07-24 19:00:00', '2026-07-24 20:30:00', 48, 43, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(84, 53, NULL, '2026-07-24 20:30:00', '2026-07-24 22:00:00', 48, 43, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(90, 33, 15, '2026-07-23 10:00:00', '2026-07-23 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 12:06:20', 1),
(91, 33, 15, '2026-07-23 13:00:00', '2026-07-23 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:11:18', 1),
(92, 33, 15, '2026-07-23 16:00:00', '2026-07-23 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:11:18', 1),
(93, 33, 15, '2026-07-24 10:00:00', '2026-07-24 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(94, 33, 15, '2026-07-24 13:00:00', '2026-07-24 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(95, 33, 15, '2026-07-24 13:00:00', '2026-07-24 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(96, 33, 15, '2026-07-24 16:00:00', '2026-07-24 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(97, 33, 15, '2026-07-25 10:00:00', '2026-07-25 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(98, 33, 15, '2026-07-25 13:00:00', '2026-07-25 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(99, 33, 15, '2026-07-25 16:00:00', '2026-07-25 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(100, 33, 15, '2026-07-25 10:00:00', '2026-07-25 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(101, 33, 15, '2026-07-25 13:00:00', '2026-07-25 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(102, 33, 15, '2026-07-25 13:00:00', '2026-07-25 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(103, 33, 15, '2026-07-25 16:00:00', '2026-07-25 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(104, 33, 15, '2026-07-25 16:00:00', '2026-07-25 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(105, 33, 15, '2026-07-26 10:00:00', '2026-07-26 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(106, 33, 15, '2026-07-26 10:00:00', '2026-07-26 12:30:00', 12, 12, 12, 3, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(107, 33, 15, '2026-07-26 13:00:00', '2026-07-26 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(108, 33, 15, '2026-07-26 13:00:00', '2026-07-26 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(109, 33, 15, '2026-07-26 16:00:00', '2026-07-26 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', 'Group ticket - best value for 4 people', NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(110, 30, 5, '2026-07-15 20:00:00', '2026-07-15 22:00:00', 100, 4, NULL, 0, 0, NULL, NULL, NULL, 'ENG', NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-30 18:13:26', 0),
(111, 47, NULL, '2026-07-23 16:30:00', '2026-07-23 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/caf-de-roemer-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(112, 47, NULL, '2026-07-23 18:30:00', '2026-07-23 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/caf-de-roemer-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(113, 47, NULL, '2026-07-23 20:30:00', '2026-07-23 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/caf-de-roemer-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(114, 47, NULL, '2026-07-24 16:30:00', '2026-07-24 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/caf-de-roemer-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(115, 47, NULL, '2026-07-24 18:30:00', '2026-07-24 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/caf-de-roemer-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(116, 47, NULL, '2026-07-24 20:30:00', '2026-07-24 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/caf-de-roemer-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(117, 47, NULL, '2026-07-25 16:30:00', '2026-07-25 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/caf-de-roemer-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(118, 47, NULL, '2026-07-25 18:30:00', '2026-07-25 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/caf-de-roemer-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(119, 47, NULL, '2026-07-25 20:30:00', '2026-07-25 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/caf-de-roemer-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(120, 47, NULL, '2026-07-26 16:30:00', '2026-07-26 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/caf-de-roemer-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(121, 47, NULL, '2026-07-26 18:30:00', '2026-07-26 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/caf-de-roemer-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(122, 47, NULL, '2026-07-26 20:30:00', '2026-07-26 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/caf-de-roemer-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(126, 48, NULL, '2026-07-23 17:00:00', '2026-07-23 19:00:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/ratatouille-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(127, 48, NULL, '2026-07-23 19:15:00', '2026-07-23 21:15:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/ratatouille-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(128, 48, NULL, '2026-07-23 21:30:00', '2026-07-23 23:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/ratatouille-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(129, 48, NULL, '2026-07-24 19:15:00', '2026-07-24 21:15:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/ratatouille-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(130, 48, NULL, '2026-07-24 21:30:00', '2026-07-24 23:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/ratatouille-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(131, 48, NULL, '2026-07-25 17:00:00', '2026-07-25 19:00:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/ratatouille-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(132, 48, NULL, '2026-07-25 19:15:00', '2026-07-25 21:15:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/ratatouille-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(133, 48, NULL, '2026-07-25 21:30:00', '2026-07-25 23:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/ratatouille-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(134, 48, NULL, '2026-07-26 17:00:00', '2026-07-26 19:00:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/ratatouille-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(135, 48, NULL, '2026-07-26 19:15:00', '2026-07-26 21:15:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/ratatouille-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(136, 48, NULL, '2026-07-26 21:30:00', '2026-07-26 23:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/ratatouille-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(141, 49, NULL, '2026-07-23 16:30:00', '2026-07-23 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-ml-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(142, 49, NULL, '2026-07-23 18:30:00', '2026-07-23 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-ml-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(143, 49, NULL, '2026-07-23 20:30:00', '2026-07-23 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-ml-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(144, 49, NULL, '2026-07-24 16:30:00', '2026-07-24 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-ml-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(145, 49, NULL, '2026-07-24 18:30:00', '2026-07-24 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-ml-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(146, 49, NULL, '2026-07-24 20:30:00', '2026-07-24 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-ml-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(147, 49, NULL, '2026-07-25 16:30:00', '2026-07-25 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-ml-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(148, 49, NULL, '2026-07-25 18:30:00', '2026-07-25 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-ml-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(149, 49, NULL, '2026-07-25 20:30:00', '2026-07-25 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-ml-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(150, 49, NULL, '2026-07-26 16:30:00', '2026-07-26 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-ml-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(151, 49, NULL, '2026-07-26 18:30:00', '2026-07-26 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-ml-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(152, 49, NULL, '2026-07-26 20:30:00', '2026-07-26 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-ml-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(156, 50, NULL, '2026-07-23 16:30:00', '2026-07-23 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-fris-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(157, 50, NULL, '2026-07-23 18:30:00', '2026-07-23 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-fris-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(158, 50, NULL, '2026-07-23 20:30:00', '2026-07-23 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-fris-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(159, 50, NULL, '2026-07-24 16:30:00', '2026-07-24 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-fris-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(160, 50, NULL, '2026-07-24 18:30:00', '2026-07-24 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-fris-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(161, 50, NULL, '2026-07-25 16:30:00', '2026-07-25 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-fris-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(162, 50, NULL, '2026-07-25 18:30:00', '2026-07-25 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-fris-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(163, 50, NULL, '2026-07-25 20:30:00', '2026-07-25 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-fris-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(164, 50, NULL, '2026-07-26 16:30:00', '2026-07-26 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-fris-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(165, 50, NULL, '2026-07-26 18:30:00', '2026-07-26 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-fris-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(166, 50, NULL, '2026-07-26 20:30:00', '2026-07-26 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/restaurant-fris-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(171, 51, NULL, '2026-07-23 16:30:00', '2026-07-23 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/new-vegas-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(172, 51, NULL, '2026-07-23 18:30:00', '2026-07-23 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/new-vegas-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(173, 51, NULL, '2026-07-23 20:30:00', '2026-07-23 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/new-vegas-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(174, 51, NULL, '2026-07-24 16:30:00', '2026-07-24 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/new-vegas-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(175, 51, NULL, '2026-07-24 20:30:00', '2026-07-24 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/new-vegas-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(176, 51, NULL, '2026-07-25 16:30:00', '2026-07-25 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/new-vegas-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(177, 51, NULL, '2026-07-25 18:30:00', '2026-07-25 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/new-vegas-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(178, 51, NULL, '2026-07-25 20:30:00', '2026-07-25 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/new-vegas-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(179, 51, NULL, '2026-07-26 16:30:00', '2026-07-26 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/new-vegas-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(180, 51, NULL, '2026-07-26 18:30:00', '2026-07-26 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/new-vegas-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(181, 51, NULL, '2026-07-26 20:30:00', '2026-07-26 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/new-vegas-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(186, 52, NULL, '2026-07-23 16:30:00', '2026-07-23 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/grand-cafe-brinkman-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(187, 52, NULL, '2026-07-23 18:30:00', '2026-07-23 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/grand-cafe-brinkman-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(188, 52, NULL, '2026-07-23 20:30:00', '2026-07-23 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/grand-cafe-brinkman-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(189, 52, NULL, '2026-07-24 18:30:00', '2026-07-24 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/grand-cafe-brinkman-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(190, 52, NULL, '2026-07-24 20:30:00', '2026-07-24 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/grand-cafe-brinkman-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(191, 52, NULL, '2026-07-25 16:30:00', '2026-07-25 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/grand-cafe-brinkman-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(192, 52, NULL, '2026-07-25 18:30:00', '2026-07-25 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/grand-cafe-brinkman-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(193, 52, NULL, '2026-07-25 20:30:00', '2026-07-25 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/grand-cafe-brinkman-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(194, 52, NULL, '2026-07-26 16:30:00', '2026-07-26 18:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/grand-cafe-brinkman-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(195, 52, NULL, '2026-07-26 18:30:00', '2026-07-26 20:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/grand-cafe-brinkman-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(196, 52, NULL, '2026-07-26 20:30:00', '2026-07-26 22:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/grand-cafe-brinkman-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(201, 53, NULL, '2026-07-23 17:30:00', '2026-07-23 19:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/urban-frenchy-bistro-toujours-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(202, 53, NULL, '2026-07-23 19:15:00', '2026-07-23 21:15:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/urban-frenchy-bistro-toujours-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(203, 53, NULL, '2026-07-23 21:00:00', '2026-07-23 23:00:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/urban-frenchy-bistro-toujours-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(204, 53, NULL, '2026-07-24 19:15:00', '2026-07-24 21:15:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/urban-frenchy-bistro-toujours-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(205, 53, NULL, '2026-07-24 21:00:00', '2026-07-24 23:00:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/urban-frenchy-bistro-toujours-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(206, 53, NULL, '2026-07-25 17:30:00', '2026-07-25 19:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/urban-frenchy-bistro-toujours-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(207, 53, NULL, '2026-07-25 19:15:00', '2026-07-25 21:15:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/urban-frenchy-bistro-toujours-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(208, 53, NULL, '2026-07-25 21:00:00', '2026-07-25 23:00:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/urban-frenchy-bistro-toujours-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(209, 53, NULL, '2026-07-26 17:30:00', '2026-07-26 19:30:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/urban-frenchy-bistro-toujours-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(210, 53, NULL, '2026-07-26 19:15:00', '2026-07-26 21:15:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/urban-frenchy-bistro-toujours-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(211, 53, NULL, '2026-07-26 21:00:00', '2026-07-26 23:00:00', 40, 40, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, 'Reserve a Table', '/restaurant/urban-frenchy-bistro-toujours-festival-dinner/reservation', 0, '2026-04-01 19:35:40', 1),
(213, 69, NULL, '2026-07-23 22:09:00', '2026-07-23 23:09:00', 100, 10, NULL, 0, 0, NULL, NULL, NULL, 'NL', NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-04-05 20:10:06', 1),
(214, 70, NULL, '2026-07-23 17:12:00', '2026-07-23 22:12:00', 100, 4, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-04-05 20:12:44', 0),
(215, 71, NULL, '2026-07-23 17:13:00', '2026-07-23 22:13:00', 100, 10, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-04-05 20:13:53', 1),
(216, 74, NULL, '2026-04-08 10:26:00', '2026-04-10 10:26:00', 100, 4, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-04-06 08:28:06', 0);

-- --------------------------------------------------------

--
-- Table structure for table `EventSessionLabel`
--

CREATE TABLE `EventSessionLabel` (
  `EventSessionLabelId` int(11) NOT NULL,
  `EventSessionId` int(11) NOT NULL,
  `LabelText` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `EventSessionLabel`
--

INSERT INTO `EventSessionLabel` (`EventSessionLabelId`, `EventSessionId`, `LabelText`) VALUES
(2, 53, 'Age 16+'),
(3, 50, 'In Dutch'),
(4, 50, 'Age 4+'),
(5, 51, 'In Dutch'),
(6, 51, 'Age 16+'),
(7, 52, 'In English'),
(8, 52, 'Age 16+'),
(9, 53, 'In Dutch'),
(10, 53, 'Age 10+'),
(11, 54, 'In Dutch'),
(12, 54, 'Age 16+'),
(13, 55, 'In Dutch'),
(14, 55, 'Age 12+'),
(15, 56, 'In English'),
(16, 56, 'Age 16+'),
(17, 57, 'In Dutch'),
(18, 57, 'Age 10+'),
(19, 58, 'In Dutch'),
(20, 58, 'Age 10+'),
(21, 62, 'In English'),
(22, 62, 'Age 10+'),
(55, 1, 'Soul'),
(56, 23, 'Soul'),
(58, 2, 'Alternative'),
(59, 21, 'Alternative'),
(61, 3, 'Soul'),
(62, 4, 'Alternative'),
(63, 20, 'Alternative'),
(65, 5, 'Pop'),
(66, 6, 'Soul'),
(67, 7, 'Folk'),
(68, 8, 'Rock'),
(69, 9, 'Alternative'),
(70, 10, 'Soul'),
(71, 11, 'Alternative'),
(72, 12, 'Alternative'),
(73, 13, 'Pop'),
(74, 24, 'Pop'),
(76, 14, 'Rock'),
(77, 15, 'Soul'),
(78, 16, 'Alternative'),
(79, 17, 'Folk'),
(80, 22, 'Folk'),
(82, 18, 'Folk'),
(83, 19, 'Alternative'),
(85, 38, 'In English'),
(86, 90, 'In Dutch'),
(88, 39, 'In English'),
(89, 91, 'In Dutch'),
(90, 40, 'In English'),
(91, 92, 'In Dutch'),
(92, 41, 'In English'),
(93, 93, 'In Dutch'),
(94, 42, 'In English'),
(95, 94, 'In Dutch'),
(96, 95, 'In Chinese'),
(97, 43, 'In English'),
(98, 99, 'In Dutch'),
(99, 44, 'In English'),
(100, 100, 'In Dutch'),
(101, 45, 'In English'),
(102, 101, 'In Dutch'),
(103, 102, 'In Chinese'),
(104, 46, 'In English'),
(105, 103, 'In Dutch'),
(106, 104, 'In Chinese'),
(107, 47, 'In English'),
(108, 105, 'In Dutch'),
(109, 106, 'In Chinese'),
(110, 48, 'In English'),
(111, 107, 'In Dutch'),
(112, 108, 'In Chinese'),
(113, 49, 'In English'),
(114, 109, 'In Dutch'),
(116, 213, 'test label');

-- --------------------------------------------------------

--
-- Table structure for table `EventSessionPrice`
--

CREATE TABLE `EventSessionPrice` (
  `EventSessionPriceId` int(11) NOT NULL,
  `EventSessionId` int(11) NOT NULL,
  `PriceTierId` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `CurrencyCode` char(3) NOT NULL DEFAULT 'EUR',
  `VatRate` decimal(5,2) NOT NULL DEFAULT 21.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `EventSessionPrice`
--

INSERT INTO `EventSessionPrice` (`EventSessionPriceId`, `EventSessionId`, `PriceTierId`, `Price`, `CurrencyCode`, `VatRate`) VALUES
(1, 1, 1, 15.00, 'EUR', 21.00),
(2, 2, 1, 15.00, 'EUR', 21.00),
(3, 3, 1, 15.00, 'EUR', 21.00),
(4, 4, 1, 10.00, 'EUR', 21.00),
(5, 5, 1, 10.00, 'EUR', 21.00),
(6, 6, 1, 10.00, 'EUR', 21.00),
(7, 7, 1, 15.00, 'EUR', 21.00),
(8, 8, 1, 15.00, 'EUR', 21.00),
(9, 9, 1, 15.00, 'EUR', 21.00),
(10, 10, 1, 10.00, 'EUR', 21.00),
(11, 11, 1, 10.00, 'EUR', 21.00),
(12, 12, 1, 10.00, 'EUR', 21.00),
(13, 13, 1, 15.00, 'EUR', 21.00),
(14, 14, 1, 15.00, 'EUR', 21.00),
(15, 15, 1, 15.00, 'EUR', 21.00),
(16, 16, 1, 10.00, 'EUR', 21.00),
(17, 17, 1, 10.00, 'EUR', 21.00),
(18, 18, 1, 10.00, 'EUR', 21.00),
(19, 19, 1, 0.00, 'EUR', 21.00),
(20, 20, 1, 0.00, 'EUR', 21.00),
(21, 21, 1, 0.00, 'EUR', 21.00),
(22, 22, 1, 0.00, 'EUR', 21.00),
(23, 23, 1, 0.00, 'EUR', 21.00),
(24, 24, 1, 0.00, 'EUR', 21.00),
(25, 25, 1, 75.00, 'EUR', 21.00),
(26, 26, 1, 60.00, 'EUR', 21.00),
(27, 27, 1, 60.00, 'EUR', 21.00),
(28, 28, 1, 60.00, 'EUR', 21.00),
(29, 29, 1, 60.00, 'EUR', 21.00),
(30, 30, 1, 110.00, 'EUR', 21.00),
(31, 31, 1, 60.00, 'EUR', 21.00),
(32, 32, 1, 75.00, 'EUR', 21.00),
(33, 33, 1, 60.00, 'EUR', 21.00),
(34, 34, 1, 110.00, 'EUR', 21.00),
(35, 35, 1, 60.00, 'EUR', 21.00),
(36, 36, 1, 90.00, 'EUR', 21.00),
(37, 37, 1, 60.00, 'EUR', 21.00),
(38, 38, 1, 17.50, 'EUR', 21.00),
(39, 38, 3, 60.00, 'EUR', 21.00),
(40, 39, 1, 17.50, 'EUR', 21.00),
(41, 39, 3, 60.00, 'EUR', 21.00),
(42, 40, 1, 17.50, 'EUR', 21.00),
(43, 40, 3, 60.00, 'EUR', 21.00),
(44, 41, 1, 17.50, 'EUR', 21.00),
(45, 41, 3, 60.00, 'EUR', 21.00),
(46, 42, 1, 17.50, 'EUR', 21.00),
(47, 42, 3, 60.00, 'EUR', 21.00),
(48, 43, 1, 17.50, 'EUR', 21.00),
(49, 43, 3, 60.00, 'EUR', 21.00),
(50, 44, 1, 17.50, 'EUR', 21.00),
(51, 44, 3, 60.00, 'EUR', 21.00),
(52, 45, 1, 17.50, 'EUR', 21.00),
(53, 45, 3, 60.00, 'EUR', 21.00),
(54, 46, 1, 17.50, 'EUR', 21.00),
(55, 46, 3, 60.00, 'EUR', 21.00),
(56, 47, 1, 17.50, 'EUR', 21.00),
(57, 47, 3, 60.00, 'EUR', 21.00),
(58, 48, 1, 17.50, 'EUR', 21.00),
(59, 48, 3, 60.00, 'EUR', 21.00),
(60, 49, 1, 17.50, 'EUR', 21.00),
(61, 49, 3, 60.00, 'EUR', 21.00),
(62, 50, 1, 6.00, 'EUR', 21.00),
(63, 51, 1, 12.50, 'EUR', 21.00),
(64, 52, 5, 0.00, 'EUR', 21.00),
(65, 53, 5, 0.00, 'EUR', 21.00),
(66, 54, 1, 12.50, 'EUR', 21.00),
(67, 55, 5, 0.00, 'EUR', 21.00),
(68, 56, 1, 12.50, 'EUR', 21.00),
(69, 57, 1, 10.00, 'EUR', 21.00),
(70, 58, 1, 10.00, 'EUR', 21.00),
(71, 59, 1, 12.50, 'EUR', 21.00),
(72, 60, 5, 0.00, 'EUR', 21.00),
(73, 61, 1, 10.00, 'EUR', 21.00),
(74, 62, 1, 10.00, 'EUR', 21.00),
(75, 63, 5, 0.00, 'EUR', 21.00),
(76, 64, 1, 12.50, 'EUR', 21.00),
(77, 65, 1, 35.00, 'EUR', 9.00),
(78, 65, 2, 17.50, 'EUR', 9.00),
(79, 66, 1, 35.00, 'EUR', 9.00),
(80, 66, 2, 17.50, 'EUR', 9.00),
(81, 67, 1, 35.00, 'EUR', 9.00),
(82, 67, 2, 17.50, 'EUR', 9.00),
(83, 68, 1, 45.00, 'EUR', 9.00),
(84, 68, 2, 22.50, 'EUR', 9.00),
(85, 69, 1, 45.00, 'EUR', 9.00),
(86, 69, 2, 22.50, 'EUR', 9.00),
(87, 70, 1, 45.00, 'EUR', 9.00),
(88, 70, 2, 22.50, 'EUR', 9.00),
(89, 71, 1, 45.00, 'EUR', 9.00),
(90, 71, 2, 22.50, 'EUR', 9.00),
(91, 72, 1, 45.00, 'EUR', 9.00),
(92, 72, 2, 22.50, 'EUR', 9.00),
(93, 73, 1, 45.00, 'EUR', 9.00),
(94, 73, 2, 22.50, 'EUR', 9.00),
(95, 74, 1, 45.00, 'EUR', 9.00),
(96, 74, 2, 22.50, 'EUR', 9.00),
(97, 75, 1, 45.00, 'EUR', 9.00),
(98, 75, 2, 22.50, 'EUR', 9.00),
(99, 76, 1, 35.00, 'EUR', 9.00),
(100, 76, 2, 17.50, 'EUR', 9.00),
(101, 77, 1, 35.00, 'EUR', 9.00),
(102, 77, 2, 17.50, 'EUR', 9.00),
(103, 78, 1, 35.00, 'EUR', 9.00),
(104, 78, 2, 17.50, 'EUR', 9.00),
(105, 79, 1, 35.00, 'EUR', 9.00),
(106, 79, 2, 17.50, 'EUR', 9.00),
(107, 80, 1, 35.00, 'EUR', 9.00),
(108, 80, 2, 17.50, 'EUR', 9.00),
(109, 81, 1, 35.00, 'EUR', 9.00),
(110, 81, 2, 17.50, 'EUR', 9.00),
(111, 82, 1, 35.00, 'EUR', 9.00),
(112, 82, 2, 17.50, 'EUR', 9.00),
(113, 83, 1, 35.00, 'EUR', 9.00),
(114, 83, 2, 17.50, 'EUR', 9.00),
(115, 84, 1, 35.00, 'EUR', 9.00),
(116, 84, 2, 17.50, 'EUR', 9.00),
(180, 90, 6, 17.50, 'EUR', 21.00),
(182, 91, 6, 17.50, 'EUR', 21.00),
(184, 92, 6, 17.50, 'EUR', 21.00),
(186, 93, 6, 17.50, 'EUR', 21.00),
(188, 94, 6, 17.50, 'EUR', 21.00),
(189, 95, 6, 17.50, 'EUR', 21.00),
(191, 96, 6, 17.50, 'EUR', 21.00),
(193, 97, 6, 17.50, 'EUR', 21.00),
(194, 100, 6, 17.50, 'EUR', 21.00),
(196, 98, 6, 17.50, 'EUR', 21.00),
(197, 101, 6, 17.50, 'EUR', 21.00),
(198, 102, 6, 17.50, 'EUR', 21.00),
(200, 99, 6, 17.50, 'EUR', 21.00),
(201, 103, 6, 17.50, 'EUR', 21.00),
(202, 104, 6, 17.50, 'EUR', 21.00),
(204, 105, 6, 17.50, 'EUR', 21.00),
(205, 106, 6, 17.50, 'EUR', 21.00),
(207, 107, 6, 17.50, 'EUR', 21.00),
(208, 108, 6, 17.50, 'EUR', 21.00),
(210, 109, 6, 17.50, 'EUR', 21.00),
(211, 111, 1, 45.00, 'EUR', 21.00),
(212, 112, 1, 45.00, 'EUR', 21.00),
(213, 113, 1, 45.00, 'EUR', 21.00),
(214, 114, 1, 45.00, 'EUR', 21.00),
(215, 115, 1, 45.00, 'EUR', 21.00),
(216, 116, 1, 45.00, 'EUR', 21.00),
(217, 117, 1, 45.00, 'EUR', 21.00),
(218, 118, 1, 45.00, 'EUR', 21.00),
(219, 119, 1, 45.00, 'EUR', 21.00),
(220, 120, 1, 45.00, 'EUR', 21.00),
(221, 121, 1, 45.00, 'EUR', 21.00),
(222, 122, 1, 45.00, 'EUR', 21.00),
(223, 126, 1, 45.00, 'EUR', 21.00),
(224, 127, 1, 45.00, 'EUR', 21.00),
(225, 128, 1, 45.00, 'EUR', 21.00),
(226, 129, 1, 45.00, 'EUR', 21.00),
(227, 130, 1, 45.00, 'EUR', 21.00),
(228, 131, 1, 45.00, 'EUR', 21.00),
(229, 132, 1, 45.00, 'EUR', 21.00),
(230, 133, 1, 45.00, 'EUR', 21.00),
(231, 134, 1, 45.00, 'EUR', 21.00),
(232, 135, 1, 45.00, 'EUR', 21.00),
(233, 136, 1, 45.00, 'EUR', 21.00),
(234, 141, 1, 45.00, 'EUR', 21.00),
(235, 142, 1, 45.00, 'EUR', 21.00),
(236, 143, 1, 45.00, 'EUR', 21.00),
(237, 144, 1, 45.00, 'EUR', 21.00),
(238, 145, 1, 45.00, 'EUR', 21.00),
(239, 146, 1, 45.00, 'EUR', 21.00),
(240, 147, 1, 45.00, 'EUR', 21.00),
(241, 148, 1, 45.00, 'EUR', 21.00),
(242, 149, 1, 45.00, 'EUR', 21.00),
(243, 150, 1, 45.00, 'EUR', 21.00),
(244, 151, 1, 45.00, 'EUR', 21.00),
(245, 152, 1, 45.00, 'EUR', 21.00),
(246, 156, 1, 45.00, 'EUR', 21.00),
(247, 157, 1, 45.00, 'EUR', 21.00),
(248, 158, 1, 45.00, 'EUR', 21.00),
(249, 159, 1, 45.00, 'EUR', 21.00),
(250, 160, 1, 45.00, 'EUR', 21.00),
(251, 161, 1, 45.00, 'EUR', 21.00),
(252, 162, 1, 45.00, 'EUR', 21.00),
(253, 163, 1, 45.00, 'EUR', 21.00),
(254, 164, 1, 45.00, 'EUR', 21.00),
(255, 165, 1, 45.00, 'EUR', 21.00),
(256, 166, 1, 45.00, 'EUR', 21.00),
(274, 111, 2, 22.50, 'EUR', 21.00),
(275, 112, 2, 22.50, 'EUR', 21.00),
(276, 113, 2, 22.50, 'EUR', 21.00),
(277, 114, 2, 22.50, 'EUR', 21.00),
(278, 115, 2, 22.50, 'EUR', 21.00),
(279, 116, 2, 22.50, 'EUR', 21.00),
(280, 117, 2, 22.50, 'EUR', 21.00),
(281, 118, 2, 22.50, 'EUR', 21.00),
(282, 119, 2, 22.50, 'EUR', 21.00),
(283, 120, 2, 22.50, 'EUR', 21.00),
(284, 121, 2, 22.50, 'EUR', 21.00),
(285, 122, 2, 22.50, 'EUR', 21.00),
(286, 126, 2, 22.50, 'EUR', 21.00),
(287, 127, 2, 22.50, 'EUR', 21.00),
(288, 128, 2, 22.50, 'EUR', 21.00),
(289, 129, 2, 22.50, 'EUR', 21.00),
(290, 130, 2, 22.50, 'EUR', 21.00),
(291, 131, 2, 22.50, 'EUR', 21.00),
(292, 132, 2, 22.50, 'EUR', 21.00),
(293, 133, 2, 22.50, 'EUR', 21.00),
(294, 134, 2, 22.50, 'EUR', 21.00),
(295, 135, 2, 22.50, 'EUR', 21.00),
(296, 136, 2, 22.50, 'EUR', 21.00),
(297, 141, 2, 22.50, 'EUR', 21.00),
(298, 142, 2, 22.50, 'EUR', 21.00),
(299, 143, 2, 22.50, 'EUR', 21.00),
(300, 144, 2, 22.50, 'EUR', 21.00),
(301, 145, 2, 22.50, 'EUR', 21.00),
(302, 146, 2, 22.50, 'EUR', 21.00),
(303, 147, 2, 22.50, 'EUR', 21.00),
(304, 148, 2, 22.50, 'EUR', 21.00),
(305, 149, 2, 22.50, 'EUR', 21.00),
(306, 150, 2, 22.50, 'EUR', 21.00),
(307, 151, 2, 22.50, 'EUR', 21.00),
(308, 152, 2, 22.50, 'EUR', 21.00),
(309, 156, 2, 22.50, 'EUR', 21.00),
(310, 157, 2, 22.50, 'EUR', 21.00),
(311, 158, 2, 22.50, 'EUR', 21.00),
(312, 159, 2, 22.50, 'EUR', 21.00),
(313, 160, 2, 22.50, 'EUR', 21.00),
(314, 161, 2, 22.50, 'EUR', 21.00),
(315, 162, 2, 22.50, 'EUR', 21.00),
(316, 163, 2, 22.50, 'EUR', 21.00),
(317, 164, 2, 22.50, 'EUR', 21.00),
(318, 165, 2, 22.50, 'EUR', 21.00),
(319, 166, 2, 22.50, 'EUR', 21.00),
(337, 171, 1, 35.00, 'EUR', 21.00),
(338, 172, 1, 35.00, 'EUR', 21.00),
(339, 173, 1, 35.00, 'EUR', 21.00),
(340, 174, 1, 35.00, 'EUR', 21.00),
(341, 175, 1, 35.00, 'EUR', 21.00),
(342, 176, 1, 35.00, 'EUR', 21.00),
(343, 177, 1, 35.00, 'EUR', 21.00),
(344, 178, 1, 35.00, 'EUR', 21.00),
(345, 179, 1, 35.00, 'EUR', 21.00),
(346, 180, 1, 35.00, 'EUR', 21.00),
(347, 181, 1, 35.00, 'EUR', 21.00),
(348, 186, 1, 35.00, 'EUR', 21.00),
(349, 187, 1, 35.00, 'EUR', 21.00),
(350, 188, 1, 35.00, 'EUR', 21.00),
(351, 189, 1, 35.00, 'EUR', 21.00),
(352, 190, 1, 35.00, 'EUR', 21.00),
(353, 191, 1, 35.00, 'EUR', 21.00),
(354, 192, 1, 35.00, 'EUR', 21.00),
(355, 193, 1, 35.00, 'EUR', 21.00),
(356, 194, 1, 35.00, 'EUR', 21.00),
(357, 195, 1, 35.00, 'EUR', 21.00),
(358, 196, 1, 35.00, 'EUR', 21.00),
(359, 201, 1, 35.00, 'EUR', 21.00),
(360, 202, 1, 35.00, 'EUR', 21.00),
(361, 203, 1, 35.00, 'EUR', 21.00),
(362, 204, 1, 35.00, 'EUR', 21.00),
(363, 205, 1, 35.00, 'EUR', 21.00),
(364, 206, 1, 35.00, 'EUR', 21.00),
(365, 207, 1, 35.00, 'EUR', 21.00),
(366, 208, 1, 35.00, 'EUR', 21.00),
(367, 209, 1, 35.00, 'EUR', 21.00),
(368, 210, 1, 35.00, 'EUR', 21.00),
(369, 211, 1, 35.00, 'EUR', 21.00),
(400, 171, 2, 17.50, 'EUR', 21.00),
(401, 172, 2, 17.50, 'EUR', 21.00),
(402, 173, 2, 17.50, 'EUR', 21.00),
(403, 174, 2, 17.50, 'EUR', 21.00),
(404, 175, 2, 17.50, 'EUR', 21.00),
(405, 176, 2, 17.50, 'EUR', 21.00),
(406, 177, 2, 17.50, 'EUR', 21.00),
(407, 178, 2, 17.50, 'EUR', 21.00),
(408, 179, 2, 17.50, 'EUR', 21.00),
(409, 180, 2, 17.50, 'EUR', 21.00),
(410, 181, 2, 17.50, 'EUR', 21.00),
(411, 186, 2, 17.50, 'EUR', 21.00),
(412, 187, 2, 17.50, 'EUR', 21.00),
(413, 188, 2, 17.50, 'EUR', 21.00),
(414, 189, 2, 17.50, 'EUR', 21.00),
(415, 190, 2, 17.50, 'EUR', 21.00),
(416, 191, 2, 17.50, 'EUR', 21.00),
(417, 192, 2, 17.50, 'EUR', 21.00),
(418, 193, 2, 17.50, 'EUR', 21.00),
(419, 194, 2, 17.50, 'EUR', 21.00),
(420, 195, 2, 17.50, 'EUR', 21.00),
(421, 196, 2, 17.50, 'EUR', 21.00),
(422, 201, 2, 17.50, 'EUR', 21.00),
(423, 202, 2, 17.50, 'EUR', 21.00),
(424, 203, 2, 17.50, 'EUR', 21.00),
(425, 204, 2, 17.50, 'EUR', 21.00),
(426, 205, 2, 17.50, 'EUR', 21.00),
(427, 206, 2, 17.50, 'EUR', 21.00),
(428, 207, 2, 17.50, 'EUR', 21.00),
(429, 208, 2, 17.50, 'EUR', 21.00),
(430, 209, 2, 17.50, 'EUR', 21.00),
(431, 210, 2, 17.50, 'EUR', 21.00),
(432, 211, 2, 17.50, 'EUR', 21.00),
(433, 90, 3, 60.00, 'EUR', 21.00),
(434, 91, 3, 60.00, 'EUR', 21.00),
(435, 92, 3, 60.00, 'EUR', 21.00),
(436, 93, 3, 60.00, 'EUR', 21.00),
(437, 94, 3, 60.00, 'EUR', 21.00),
(438, 95, 3, 60.00, 'EUR', 21.00),
(439, 96, 3, 60.00, 'EUR', 21.00),
(440, 97, 3, 60.00, 'EUR', 21.00),
(441, 98, 3, 60.00, 'EUR', 21.00),
(442, 99, 3, 60.00, 'EUR', 21.00),
(443, 100, 3, 60.00, 'EUR', 21.00),
(444, 101, 3, 60.00, 'EUR', 21.00),
(445, 102, 3, 60.00, 'EUR', 21.00),
(446, 103, 3, 60.00, 'EUR', 21.00),
(447, 104, 3, 60.00, 'EUR', 21.00),
(448, 105, 3, 60.00, 'EUR', 21.00),
(449, 106, 3, 60.00, 'EUR', 21.00),
(450, 107, 3, 60.00, 'EUR', 21.00),
(451, 108, 3, 60.00, 'EUR', 21.00),
(452, 109, 3, 60.00, 'EUR', 21.00),
(465, 213, 1, 10.00, 'EUR', 21.00);

-- --------------------------------------------------------

--
-- Table structure for table `EventType`
--

CREATE TABLE `EventType` (
  `EventTypeId` int(11) NOT NULL,
  `Name` varchar(40) NOT NULL,
  `Slug` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `EventType`
--

INSERT INTO `EventType` (`EventTypeId`, `Name`, `Slug`) VALUES
(1, 'Jazz', 'jazz'),
(2, 'Dance', 'dance'),
(3, 'History', 'history'),
(4, 'Storytelling', 'storytelling'),
(5, 'Restaurant', 'restaurant');

-- --------------------------------------------------------

--
-- Table structure for table `Guide`
--

CREATE TABLE `Guide` (
  `GuideId` int(11) NOT NULL,
  `Name` varchar(120) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `Guide`
--

INSERT INTO `Guide` (`GuideId`, `Name`, `IsActive`) VALUES
(1, 'Guide EN-1', 1),
(2, 'Guide EN-2', 1),
(3, 'Guide EN-3', 1),
(4, 'Guide NL-1', 1),
(5, 'Guide NL-2', 1),
(6, 'Guide NL-3', 1),
(7, 'Guide ZH-1', 1),
(8, 'Guide ZH-2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `HistoryTour`
--

CREATE TABLE `HistoryTour` (
  `HistoryTourId` int(11) NOT NULL,
  `EventSessionId` int(11) NOT NULL,
  `LanguageCode` varchar(10) NOT NULL,
  `GuideCount` int(11) NOT NULL,
  `SeatsPerTour` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `HistoryTour`
--

INSERT INTO `HistoryTour` (`HistoryTourId`, `EventSessionId`, `LanguageCode`, `GuideCount`, `SeatsPerTour`) VALUES
(1, 38, 'ENG', 1, 12),
(2, 38, 'NL', 1, 12),
(3, 39, 'ENG', 1, 12),
(4, 39, 'NL', 1, 12),
(5, 40, 'ENG', 1, 12),
(6, 40, 'NL', 1, 12),
(7, 41, 'ENG', 1, 12),
(8, 41, 'NL', 1, 12),
(9, 42, 'ENG', 1, 12),
(10, 42, 'NL', 1, 12),
(11, 42, 'ZH', 1, 12),
(12, 43, 'ENG', 1, 12),
(13, 43, 'NL', 1, 12),
(14, 44, 'ENG', 2, 12),
(15, 44, 'NL', 2, 12),
(16, 45, 'ENG', 2, 12),
(17, 45, 'NL', 2, 12),
(18, 45, 'ZH', 1, 12),
(19, 46, 'ENG', 1, 12),
(20, 46, 'NL', 1, 12),
(21, 46, 'ZH', 1, 12),
(22, 47, 'ENG', 2, 12),
(23, 47, 'NL', 2, 12),
(24, 47, 'ZH', 1, 12),
(25, 48, 'ENG', 3, 12),
(26, 48, 'NL', 3, 12),
(27, 48, 'ZH', 2, 12),
(28, 49, 'ENG', 1, 12),
(29, 49, 'NL', 1, 12);

-- --------------------------------------------------------

--
-- Table structure for table `HistoryTourGuide`
--

CREATE TABLE `HistoryTourGuide` (
  `HistoryTourId` int(11) NOT NULL,
  `GuideId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `HistoryTourGuide`
--

INSERT INTO `HistoryTourGuide` (`HistoryTourId`, `GuideId`) VALUES
(1, 1),
(3, 1),
(5, 1),
(7, 1),
(9, 1),
(12, 1),
(14, 1),
(16, 1),
(19, 1),
(22, 1),
(25, 1),
(28, 1),
(14, 2),
(16, 2),
(22, 2),
(25, 2),
(25, 3),
(2, 4),
(4, 4),
(6, 4),
(8, 4),
(10, 4),
(13, 4),
(15, 4),
(17, 4),
(20, 4),
(23, 4),
(26, 4),
(29, 4),
(15, 5),
(17, 5),
(23, 5),
(26, 5),
(26, 6),
(11, 7),
(18, 7),
(21, 7),
(24, 7),
(27, 7),
(27, 8);

-- --------------------------------------------------------

--
-- Table structure for table `Invoice`
--

CREATE TABLE `Invoice` (
  `InvoiceId` int(11) NOT NULL,
  `OrderId` int(11) NOT NULL,
  `InvoiceNumber` varchar(30) NOT NULL,
  `InvoiceDateUtc` datetime NOT NULL,
  `ClientName` varchar(160) NOT NULL,
  `PhoneNumber` varchar(40) NOT NULL DEFAULT '',
  `AddressLine` varchar(200) NOT NULL DEFAULT '',
  `EmailAddress` varchar(200) NOT NULL,
  `SubtotalAmount` decimal(10,2) NOT NULL,
  `TotalVatAmount` decimal(10,2) NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `PaymentDateUtc` datetime DEFAULT NULL,
  `PdfAssetId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `InvoiceLine`
--

CREATE TABLE `InvoiceLine` (
  `InvoiceLineId` int(11) NOT NULL,
  `InvoiceId` int(11) NOT NULL,
  `LineDescription` varchar(200) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `UnitPrice` decimal(10,2) NOT NULL,
  `VatRate` decimal(5,2) NOT NULL,
  `LineSubtotal` decimal(10,2) NOT NULL,
  `LineVatAmount` decimal(10,2) NOT NULL,
  `LineTotal` decimal(10,2) NOT NULL,
  `DonationAmount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `MediaAsset`
--

CREATE TABLE `MediaAsset` (
  `MediaAssetId` int(11) NOT NULL,
  `FilePath` varchar(500) NOT NULL,
  `OriginalFileName` varchar(255) NOT NULL,
  `MimeType` varchar(100) NOT NULL,
  `FileSizeBytes` bigint(20) NOT NULL,
  `AltText` varchar(200) NOT NULL DEFAULT '',
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `MediaAsset`
--

INSERT INTO `MediaAsset` (`MediaAssetId`, `FilePath`, `OriginalFileName`, `MimeType`, `FileSizeBytes`, `AltText`, `CreatedAtUtc`) VALUES
(1, '/assets/Image/HeroImageHome.png', 'HeroImageHome.png', 'image/png', 0, 'Haarlem Festival hero background', '2026-02-08 13:45:37'),
(2, '/assets/Image/explore-incoming-events.png', 'explore-incoming-events.png', 'image/png', 0, 'Explore upcoming events banner', '2026-02-08 13:45:37'),
(3, '/assets/Image/what-is-haarlem.png', 'what-is-haarlem.png', 'image/png', 0, 'Aerial view of Haarlem city center', '2026-02-08 13:45:37'),
(4, '/assets/Image/Image (Jazz).png', 'Image (Jazz).png', 'image/png', 0, 'Jazz musicians performing', '2026-02-08 13:45:37'),
(5, '/assets/Image/Image (Dance).png', 'Image (Dance).png', 'image/png', 0, 'Dancers at festival', '2026-02-08 13:45:37'),
(6, '/assets/Image/Image (History).png', 'Image (History).png', 'image/png', 0, 'Historic Haarlem buildings', '2026-02-08 13:45:37'),
(7, '/assets/Image/Image (Yummy).png', 'Image (Yummy).png', 'image/png', 0, 'Festival restaurant food', '2026-02-08 13:45:37'),
(8, '/assets/Image/Image (Story).png', 'Image (Story).png', 'image/png', 0, 'Storytelling performance', '2026-02-08 13:45:37'),
(9, '/assets/Image/HeroImageHome.png', 'HeroImageHome.png', 'image/png', 0, 'Haarlem Festival hero background', '2026-02-08 13:46:27'),
(10, '/assets/Image/explore-incoming-events.png', 'explore-incoming-events.png', 'image/png', 0, 'Explore upcoming events banner', '2026-02-08 13:46:27'),
(11, '/assets/Image/what-is-haarlem.png', 'what-is-haarlem.png', 'image/png', 0, 'Aerial view of Haarlem city center', '2026-02-08 13:46:27'),
(12, '/assets/Image/Image (Jazz).png', 'Image (Jazz).png', 'image/png', 0, 'Jazz musicians performing', '2026-02-08 13:46:27'),
(13, '/assets/Image/Image (Dance).png', 'Image (Dance).png', 'image/png', 0, 'Dancers at festival', '2026-02-08 13:46:27'),
(14, '/assets/Image/Image (History).png', 'Image (History).png', 'image/png', 0, 'Historic Haarlem buildings', '2026-02-08 13:46:27'),
(15, '/assets/Image/Image (Yummy).png', 'Image (Yummy).png', 'image/png', 0, 'Festival restaurant food', '2026-02-08 13:46:27'),
(16, '/assets/Image/Image (Story).png', 'Image (Story).png', 'image/png', 0, 'Storytelling performance', '2026-02-08 13:46:27'),
(17, '/assets/Image/HeroImageHome.png', 'HeroImageHome.png', 'image/png', 0, 'Haarlem Festival hero background', '2026-02-08 13:52:40'),
(18, '/assets/Image/explore-incoming-events.png', 'explore-incoming-events.png', 'image/png', 0, 'Explore upcoming events banner', '2026-02-08 13:52:40'),
(19, '/assets/Image/what-is-haarlem.png', 'what-is-haarlem.png', 'image/png', 0, 'Aerial view of Haarlem city center', '2026-02-08 13:52:40'),
(20, '/assets/Image/Image (Jazz).png', 'Image (Jazz).png', 'image/png', 0, 'Jazz musicians performing', '2026-02-08 13:52:40'),
(21, '/assets/Image/Image (Dance).png', 'Image (Dance).png', 'image/png', 0, 'Dancers at festival', '2026-02-08 13:52:40'),
(22, '/assets/Image/Image (History).png', 'Image (History).png', 'image/png', 0, 'Historic Haarlem buildings', '2026-02-08 13:52:40'),
(23, '/assets/Image/Image (Yummy).png', 'Image (Yummy).png', 'image/png', 0, 'Festival restaurant food', '2026-02-08 13:52:40'),
(24, '/assets/Image/Image (Story).png', 'Image (Story).png', 'image/png', 0, 'Storytelling performance', '2026-02-08 13:52:40'),
(25, '/assets/Image/HeroImageHome.png', 'HeroImageHome.png', 'image/png', 0, 'Haarlem Festival hero background', '2026-02-08 13:54:33'),
(26, '/assets/Image/explore-incoming-events.png', 'explore-incoming-events.png', 'image/png', 0, 'Explore upcoming events banner', '2026-02-08 13:54:33'),
(27, '/assets/Image/what-is-haarlem.png', 'what-is-haarlem.png', 'image/png', 0, 'Aerial view of Haarlem city center', '2026-02-08 13:54:33'),
(28, '/assets/Image/Image (Jazz).png', 'Image (Jazz).png', 'image/png', 0, 'Jazz musicians performing', '2026-02-08 13:54:33'),
(29, '/assets/Image/Image (Dance).png', 'Image (Dance).png', 'image/png', 0, 'Dancers at festival', '2026-02-08 13:54:33'),
(30, '/assets/Image/Image (History).png', 'Image (History).png', 'image/png', 0, 'Historic Haarlem buildings', '2026-02-08 13:54:33'),
(31, '/assets/Image/Image (Yummy).png', 'Image (Yummy).png', 'image/png', 0, 'Festival restaurant food', '2026-02-08 13:54:33'),
(32, '/assets/Image/Image (Story).png', 'Image (Story).png', 'image/png', 0, 'Storytelling performance', '2026-02-08 13:54:33'),
(33, '/assets/Image/storytelling/hero-storytelling.jpg', 'hero-storytelling.jpg', 'image/jpeg', 0, 'Storytelling hero background', '2026-02-08 14:11:23'),
(34, '/assets/Image/storytelling/picture-looking-text.jpg', 'picture-looking-text.jpg', 'image/jpeg', 0, 'Storytelling gradient section background', '2026-02-08 14:11:23'),
(35, '/assets/Image/storytelling/where-stories-come-alive.jpg', 'where-stories-come-alive.jpg', 'image/jpeg', 0, 'Where stories come alive in Haarlem', '2026-02-08 14:11:23'),
(36, '/assets/Image/storytelling/anansi-conversation.jpg', 'anansi-conversation.jpg', 'image/jpeg', 0, 'Anansi conversation storytelling moment', '2026-02-08 14:11:23'),
(37, '/assets/Image/storytelling/anansi-drip.jpg', 'anansi-drip.jpg', 'image/jpeg', 0, 'Anansi drip storytelling moment', '2026-02-08 14:11:23'),
(38, '/assets/Image/storytelling/anansi-pointing.png', 'anansi-pointing.png', 'image/png', 0, 'Anansi pointing storytelling moment', '2026-02-08 14:11:23'),
(39, '/assets/Image/storytelling/anansi-visser.jpg', 'anansi-visser.jpg', 'image/jpeg', 0, 'Anansi visser storytelling moment', '2026-02-08 14:11:23'),
(40, '/assets/Image/storytelling/building.jpg', 'building.jpg', 'image/jpeg', 0, 'Historic building storytelling venue', '2026-02-08 14:11:23'),
(41, '/assets/Image/storytelling/d-student.jpg', 'd-student.jpg', 'image/jpeg', 0, 'Student storytelling moment', '2026-02-08 14:11:23'),
(42, '/assets/Image/storytelling/d-student2.jpg', 'd-student2.jpg', 'image/jpeg', 0, 'Student storytelling moment 2', '2026-02-08 14:11:23'),
(43, '/assets/Image/storytelling/entrance-kweek.jpg', 'entrance-kweek.jpg', 'image/jpeg', 0, 'Kweek entrance storytelling venue', '2026-02-08 14:11:23'),
(44, '/assets/Image/storytelling/m-student.jpg', 'm-student.jpg', 'image/jpeg', 0, 'Student storytelling performance', '2026-02-08 14:11:23'),
(45, '/assets/Image/storytelling/pig.jpg', 'pig.jpg', 'image/jpeg', 0, 'Pig character storytelling', '2026-02-08 14:11:23'),
(46, '/assets/Image/storytelling/winnie-the-pooh.jpg', 'winnie-the-pooh.jpg', 'image/jpeg', 0, 'Winnie the Pooh storytelling', '2026-02-08 14:11:23'),
(47, '/assets/Image/storytelling/WinnieThePoohHeader.png', 'WinnieThePoohHeader.png', 'image/png', 0, 'Winnie the Pooh header image', '2026-02-08 14:11:23'),
(48, '/assets/Image/storytelling/hero-storytelling.jpg', 'hero-storytelling.jpg', 'image/jpeg', 0, 'Storytelling hero background', '2026-02-08 14:21:40'),
(49, '/assets/Image/storytelling/picture-looking-text.jpg', 'picture-looking-text.jpg', 'image/jpeg', 0, 'Storytelling gradient section background', '2026-02-08 14:21:40'),
(50, '/assets/Image/storytelling/where-stories-come-alive.jpg', 'where-stories-come-alive.jpg', 'image/jpeg', 0, 'Where stories come alive in Haarlem', '2026-02-08 14:21:40'),
(51, '/assets/Image/storytelling/hero-storytelling.jpg', 'hero-storytelling.jpg', 'image/jpeg', 0, 'Storytelling hero background', '2026-02-08 14:29:20'),
(52, '/assets/Image/storytelling/picture-looking-text.jpg', 'picture-looking-text.jpg', 'image/jpeg', 0, 'Storytelling gradient section background', '2026-02-08 14:29:20'),
(53, '/assets/Image/storytelling/where-stories-come-alive.jpg', 'where-stories-come-alive.jpg', 'image/jpeg', 0, 'Where stories come alive in Haarlem', '2026-02-08 14:29:20'),
(54, '/assets/Image/storytelling/d-student.jpg', 'd-student.jpg', 'image/jpeg', 0, 'Student storytelling moment', '2026-02-08 14:29:20'),
(55, '/assets/Image/storytelling/d-student2.jpg', 'd-student2.jpg', 'image/jpeg', 0, 'Student storytelling moment 2', '2026-02-08 14:29:20'),
(56, '/assets/Image/storytelling/m-student.jpg', 'm-student.jpg', 'image/jpeg', 0, 'Student storytelling performance', '2026-02-08 14:29:20'),
(57, '/assets/Image/storytelling/winnie-the-pooh.jpg', 'winnie-the-pooh.jpg', 'image/jpeg', 0, 'Winnie the Pooh storytelling', '2026-02-08 14:29:20'),
(58, '/assets/Image/storytelling/pig.jpg', 'pig.jpg', 'image/jpeg', 0, 'Pig character storytelling', '2026-02-08 14:29:20'),
(59, '/assets/Image/storytelling/entrance-kweek.jpg', 'entrance-kweek.jpg', 'image/jpeg', 0, 'Kweek entrance storytelling venue', '2026-02-08 14:29:20'),
(60, '/assets/Image/storytelling/building.jpg', 'building.jpg', 'image/jpeg', 0, 'Historic building storytelling venue', '2026-02-08 14:29:20'),
(61, '/assets/Image/storytelling/anansi-pointing.png', 'anansi-pointing.png', 'image/png', 0, 'Anansi pointing storytelling moment', '2026-02-08 14:29:20'),
(62, '/assets/Image/storytelling/anansi-conversation.jpg', 'anansi-conversation.jpg', 'image/jpeg', 0, 'Anansi conversation storytelling moment', '2026-02-08 14:29:20'),
(63, '/assets/Image/storytelling/anansi-drip.jpg', 'anansi-drip.jpg', 'image/jpeg', 0, 'Anansi drip storytelling moment', '2026-02-08 14:29:20'),
(64, '/assets/Image/storytelling/anansi-visser.jpg', 'anansi-visser.jpg', 'image/jpeg', 0, 'Anansi visser storytelling moment', '2026-02-08 14:29:20'),
(65, '/assets/Image/storytelling/WinnieThePoohHeader.png', 'WinnieThePoohHeader.png', 'image/png', 0, 'Winnie the Pooh header image', '2026-02-08 14:29:20'),
(66, '/assets/Image/Jazz/Jazz-hero.png', 'Jazz-hero.png', 'image/png', 0, 'Jazz page hero image', '2026-02-10 13:02:07'),
(67, '/assets/Image/Jazz/Jazz-second-section.png', 'Jazz-second-section.png', 'image/png', 0, 'Jazz page second section image', '2026-02-10 13:02:07'),
(68, '/assets/Image/Jazz/Jazz-third-section.png', 'Jazz-third-section.png', 'image/png', 0, 'Jazz page third section image', '2026-02-10 13:02:07'),
(72, '/assets/Image/Jazz/Jazz-Gumbokings.png', 'Jazz-Gumbokings.png', 'image/png', 0, 'Gumbo Kings lineup image', '2026-02-10 13:13:15'),
(73, '/assets/Image/Jazz/Jazz-evolve.png', 'Jazz-evolve.png', 'image/png', 0, 'Evolve lineup image', '2026-02-10 13:13:15'),
(74, '/assets/Image/Jazz/Jazz-Ntjam.png', 'Jazz-Ntjam.png', 'image/png', 0, 'Ntjam Rosie lineup image', '2026-02-10 13:13:15'),
(75, '/assets/Image/restaurants/Restaurant-CafeDeRoemer-card.png', 'Restaurant-CafeDeRoemer-card.png', 'image/png', 0, 'Café de Roemer restaurant', '2026-02-06 15:30:32'),
(76, '/assets/Image/restaurants/Restaurant-Ratatouille-card.png', 'Restaurant-Ratatouille-card.png', 'image/png', 0, 'Ratatouille restaurant', '2026-02-06 15:30:32'),
(77, '/assets/Image/restaurants/Restaurant-RestaurantML-card.png', 'Restaurant-RestaurantML-card.png', 'image/png', 0, 'Restaurant ML', '2026-02-06 15:30:32'),
(78, '/assets/Image/restaurants/Restaurant-RestaurantFris-card.png', 'Restaurant-RestaurantFris-card.png', 'image/png', 0, 'Restaurant Fris', '2026-02-06 15:30:32'),
(79, '/assets/Image/restaurants/Restaurant-NewVegas-card.jpg', 'Restaurant-NewVegas-card.jpg', 'image/jpeg', 0, 'New Vegas restaurant', '2026-02-06 15:30:32'),
(80, '/assets/Image/restaurants/Restaurant-GrandCafeBrinkman-card.png', 'Restaurant-GrandCafeBrinkman-card.png', 'image/png', 0, 'Grand Cafe Brinkman', '2026-02-06 15:30:32'),
(81, '/assets/Image/restaurants/Restaurant-UrbanFrenchyBistroToujours-card.png', 'Restaurant-UrbanFrenchyBistroToujours-card.png', 'image/png', 0, 'Urban Frenchy Bistro Toujours', '2026-02-06 15:30:32'),
(82, '/assets/Image/restaurants/ratatouille-gallery-1.png', 'ratatouille-gallery-1.png', 'image/png', 0, 'Ratatouille gallery photo 1', '2026-02-06 15:30:32'),
(83, '/assets/Image/restaurants/ratatouille-gallery-2.png', 'ratatouille-gallery-2.png', 'image/png', 0, 'Ratatouille gallery photo 2', '2026-02-06 15:30:32'),
(84, '/assets/Image/restaurants/ratatouille-gallery-3.png', 'ratatouille-gallery-3.png', 'image/png', 0, 'Ratatouille gallery photo 3', '2026-02-06 15:30:32'),
(85, '/assets/Image/restaurants/ratatouille-about.png', 'ratatouille-about.png', 'image/png', 0, 'About Ratatouille section', '2026-02-06 15:30:32'),
(86, '/assets/Image/restaurants/ratatouille-chef.png', 'ratatouille-chef.png', 'image/png', 0, 'Chef Jozua Jaring', '2026-02-06 15:30:32'),
(87, '/assets/Image/restaurants/ratatouille-menu-1.png', 'ratatouille-menu-1.png', 'image/png', 0, 'Ratatouille menu dish 1', '2026-02-06 15:30:32'),
(88, '/assets/Image/restaurants/ratatouille-menu-2.png', 'ratatouille-menu-2.png', 'image/png', 0, 'Ratatouille menu dish 2', '2026-02-06 15:30:32'),
(89, '/assets/Image/restaurants/ratatouille-reservation.jpg', 'ratatouille-reservation.jpg', 'image/jpeg', 0, 'Ratatouille reservation section', '2026-02-06 15:30:32'),
(90, '/assets/Image/restaurants/toujours-gallery-1.png', 'toujours-gallery-1.png', 'image/png', 0, 'Toujours Gallery 1', '2026-02-06 15:30:32'),
(91, '/assets/Image/restaurants/toujours-gallery-2.png', 'toujours-gallery-2.png', 'image/png', 0, 'Toujours Gallery 2', '2026-02-06 15:30:32'),
(92, '/assets/Image/restaurants/toujours-gallery-3.png', 'toujours-gallery-3.png', 'image/png', 0, 'Toujours Gallery 3', '2026-02-06 15:30:32'),
(93, '/assets/Image/restaurants/toujours-about.png', 'toujours-about.png', 'image/png', 0, 'About Toujours', '2026-02-06 15:30:32'),
(95, '/assets/Image/restaurants/toujours-menu-1.png', 'toujours-menu-1.png', 'image/png', 0, 'Toujours Menu 1', '2026-02-06 15:30:32'),
(96, '/assets/Image/restaurants/toujours-menu-2.png', 'toujours-menu-2.png', 'image/png', 0, 'Toujours Menu 2', '2026-02-06 15:30:32'),
(97, '/assets/Image/restaurants/toujours-reservation.png', 'toujours-reservation.png', 'image/png', 0, 'Toujours Reservation', '2026-03-08 16:07:48'),
(98, '/assets/Image/restaurants/toujours-chef.jpg', 'toujours-chef.jpg', 'image/jpeg', 0, 'Chef Toujours', '2026-02-06 15:30:32'),
(99, '/assets/Image/cms/img_69bbef528d9d12.96030426.jpg', '616cba41-3c94-4fbd-835d-bc0aeedfbaf4.jpg', 'image/jpeg', 174526, '', '2026-03-19 12:42:58'),
(100, '/assets/Image/cms/img_69bbef967a9f53.97810384.jpg', '616cba41-3c94-4fbd-835d-bc0aeedfbaf4.jpg', 'image/jpeg', 174526, '', '2026-03-19 12:44:06'),
(101, '/assets/Image/cms/img_69bbf3a2c96d31.98368045.jpg', 'Foto-Mister-Anansi-leert-de-wereld-lachen.jpeg', 'image/jpeg', 183316, '', '2026-03-19 13:01:22'),
(102, '/assets/Image/cms/img_69bbf3b6a31759.33298381.jpg', 'd93cd66cfe101a5b56d3da4876fbfe70.jpg', 'image/jpeg', 214509, '', '2026-03-19 13:01:42'),
(103, '/assets/Image/cms/img_69bbf3ce3ef514.64175921.jpg', '32828394.jpg', 'image/jpeg', 105042, '', '2026-03-19 13:02:06'),
(104, '/assets/Image/cms/img_69bbf3d5039f25.86763941.jpg', '71xaH6hW8oL._AC_UF1000,1000_QL80_.jpg', 'image/jpeg', 163601, '', '2026-03-19 13:02:13'),
(105, '/assets/Image/cms/img_69bbf3ed76d7a8.47499591.png', 'U111ntitled.png', 'image/png', 1693182, '', '2026-03-19 13:02:37'),
(106, '/assets/Image/cms/img_69bbf4094a8d05.53949416.jpg', '59f4d9e53514422b9d72fd70ddf9d573H3000W3000_320_320.jpg', 'image/jpeg', 21780, '', '2026-03-19 13:03:05'),
(107, '/assets/Image/cms/img_69bbf413705627.94131764.jpg', 'Mv4q5DbA.jpg', 'image/jpeg', 24875, '', '2026-03-19 13:03:15'),
(108, '/assets/Image/cms/img_69bbf420e8bbd1.00624951.jpg', 'Mister_Anansi_presentator.jpg', 'image/jpeg', 634717, '', '2026-03-19 13:03:28'),
(109, '/assets/Image/cms/img_69bc015850fe41.40561995.jpg', 'pexels-tkirkgoz-16509950.jpg', 'image/jpeg', 311876, '', '2026-03-19 13:59:52'),
(110, '/assets/Image/cms/img_69bc01cbc15b00.40333275.jpg', 'pexels-rihan-ishan-das-739500-2519332.jpg', 'image/jpeg', 1178960, '', '2026-03-19 14:01:47'),
(111, '/assets/Image/cms/img_69bc01efc85bf3.05889602.jpg', 'pexels-linafoxy-7612910.jpg', 'image/jpeg', 4393082, '', '2026-03-19 14:02:23'),
(112, '/assets/Image/cms/img_69bc01f8c3c935.16528174.jpg', 'pexels-erikscheel-95425.jpg', 'image/jpeg', 313988, '', '2026-03-19 14:02:32'),
(113, '/assets/Image/cms/img_69bc0216451550.49495262.jpg', 'pexels-pixabay-45852.jpg', 'image/jpeg', 828933, '', '2026-03-19 14:03:02'),
(114, '/assets/Image/cms/img_69bc0223648f59.66487036.jpg', 'pexels-kelly-2321837.jpg', 'image/jpeg', 1421823, '', '2026-03-19 14:03:15'),
(115, '/assets/Image/cms/img_69bc022c11ed30.99868104.jpg', 'pexels-pixabay-47862.jpg', 'image/jpeg', 378902, '', '2026-03-19 14:03:24'),
(116, '/assets/Image/cms/img_69bc02395608e9.02804662.jpg', 'pexels-quang-nguyen-vinh-222549-2158060.jpg', 'image/jpeg', 2393141, '', '2026-03-19 14:03:37'),
(117, '/assets/Image/cms/img_69bc02416dd260.59429514.jpg', 'pexels-samarth-2301903.jpg', 'image/jpeg', 2459293, '', '2026-03-19 14:03:45'),
(118, '/assets/Image/cms/img_69bc024cb16e39.78789064.jpg', 'pexels-edris-ibraheem-1697115432-31842596.jpg', 'image/jpeg', 1318696, '', '2026-03-19 14:03:56'),
(119, '/assets/Image/cms/img_69bc0253470b58.41608350.jpg', 'pexels-lekepov-2044219506-34528385.jpg', 'image/jpeg', 1221614, '', '2026-03-19 14:04:03'),
(120, '/assets/tickets/Haarlem-Festival-Ticket-HF-YZNXBU7VA34GFLTF.pdf', 'Haarlem-Festival-Ticket-HF-YZNXBU7VA34GFLTF.pdf', 'application/pdf', 14846, 'Festival ticket PDF', '2026-03-28 22:05:44'),
(121, '/assets/tickets/Haarlem-Festival-Ticket-HF-TUKL6Q52F58NFRHT.pdf', 'Haarlem-Festival-Ticket-HF-TUKL6Q52F58NFRHT.pdf', 'application/pdf', 14846, 'Festival ticket PDF', '2026-03-28 22:05:44'),
(122, '/assets/tickets/Haarlem-Festival-Ticket-HF-6NTWU8BPC9FKTKMW.pdf', 'Haarlem-Festival-Ticket-HF-6NTWU8BPC9FKTKMW.pdf', 'application/pdf', 14618, 'Festival ticket PDF', '2026-03-28 22:05:44'),
(123, '/assets/tickets/Haarlem-Festival-Ticket-HF-E7YK5KEMMPB5WNFD.pdf', 'Haarlem-Festival-Ticket-HF-E7YK5KEMMPB5WNFD.pdf', 'application/pdf', 14542, 'Festival ticket PDF', '2026-03-30 11:30:18'),
(124, '/assets/tickets/Haarlem-Festival-Ticket-HF-6APKWGDAPU3J2QNV.pdf', 'Haarlem-Festival-Ticket-HF-6APKWGDAPU3J2QNV.pdf', 'application/pdf', 14428, 'Festival ticket PDF', '2026-03-30 11:30:18'),
(125, '/assets/tickets/Haarlem-Festival-Ticket-HF-P6PCVLZPNT7L7BQ5.pdf', 'Haarlem-Festival-Ticket-HF-P6PCVLZPNT7L7BQ5.pdf', 'application/pdf', 14390, 'Festival ticket PDF', '2026-03-30 11:30:18'),
(126, '/assets/Image/cms/img_69caa874b3ab95.23757361.png', 'test-image.png', 'image/png', 69, '', '2026-03-30 16:44:36'),
(127, '/assets/Image/cms/img_69caae4f3219a3.72077547.png', 'test-image.png', 'image/png', 69, '', '2026-03-30 17:09:35'),
(128, '/assets/Image/cms/img_69cab9c7291477.91677702.png', 'test-image.png', 'image/png', 69, '', '2026-03-30 17:58:31'),
(129, '/assets/tickets/Haarlem-Festival-Ticket-HF-9TCZWTJAZY3NPWMJ.pdf', 'Haarlem-Festival-Ticket-HF-9TCZWTJAZY3NPWMJ.pdf', 'application/pdf', 14688, 'Festival ticket PDF', '2026-04-01 18:39:40'),
(130, '/assets/tickets/Haarlem-Festival-Ticket-HF-PZQWQDUKS49Q5HUT.pdf', 'Haarlem-Festival-Ticket-HF-PZQWQDUKS49Q5HUT.pdf', 'application/pdf', 14650, 'Festival ticket PDF', '2026-04-01 18:47:10'),
(131, '/assets/tickets/Haarlem-Festival-Ticket-HF-DKB3GQQKTQWKBQKF.pdf', 'Haarlem-Festival-Ticket-HF-DKB3GQQKTQWKBQKF.pdf', 'application/pdf', 14384, 'Festival ticket PDF', '2026-04-01 18:49:15'),
(132, '/assets/tickets/Haarlem-Festival-Ticket-HF-8WRJNFU4TH3MQCBS.pdf', 'Haarlem-Festival-Ticket-HF-8WRJNFU4TH3MQCBS.pdf', 'application/pdf', 24371, 'Festival ticket PDF', '2026-04-06 11:38:53'),
(133, '/assets/tickets/Haarlem-Festival-Ticket-HF-CUUD2G55SNULTJDK.pdf', 'Haarlem-Festival-Ticket-HF-CUUD2G55SNULTJDK.pdf', 'application/pdf', 23415, 'Festival ticket PDF', '2026-04-06 11:38:53'),
(134, '/assets/tickets/Haarlem-Festival-Ticket-HF-WHGCMN56H3FBYXUU.pdf', 'Haarlem-Festival-Ticket-HF-WHGCMN56H3FBYXUU.pdf', 'application/pdf', 23121, 'Festival ticket PDF', '2026-04-06 11:38:53'),
(135, '/assets/tickets/Haarlem-Festival-Ticket-HF-6XDEF8GW2KN9FNX5.pdf', 'Haarlem-Festival-Ticket-HF-6XDEF8GW2KN9FNX5.pdf', 'application/pdf', 24336, 'Festival ticket PDF', '2026-04-06 11:38:53'),
(136, '/assets/tickets/Haarlem-Festival-Ticket-HF-T29B7R664ZVGKHA3.pdf', 'Haarlem-Festival-Ticket-HF-T29B7R664ZVGKHA3.pdf', 'application/pdf', 23347, 'Festival ticket PDF', '2026-04-06 11:38:53'),
(137, '/assets/tickets/Haarlem-Festival-Ticket-HF-BSFNUM9KRTKK68UQ.pdf', 'Haarlem-Festival-Ticket-HF-BSFNUM9KRTKK68UQ.pdf', 'application/pdf', 24237, 'Festival ticket PDF', '2026-04-06 11:38:53'),
(138, '/assets/tickets/Haarlem-Festival-Ticket-HF-3CTFZ28HH92ZFR8R.pdf', 'Haarlem-Festival-Ticket-HF-3CTFZ28HH92ZFR8R.pdf', 'application/pdf', 23089, 'Festival ticket PDF', '2026-04-06 11:38:53');

-- --------------------------------------------------------

--
-- Table structure for table `Order`
--

CREATE TABLE `Order` (
  `OrderId` int(11) NOT NULL,
  `OrderNumber` varchar(30) NOT NULL,
  `UserAccountId` int(11) NOT NULL,
  `ProgramId` int(11) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp(),
  `PayBeforeUtc` datetime DEFAULT NULL,
  `Subtotal` decimal(10,2) NOT NULL,
  `VatTotal` decimal(10,2) NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `TicketRecipientFirstName` varchar(60) DEFAULT NULL,
  `TicketRecipientLastName` varchar(80) DEFAULT NULL,
  `TicketRecipientEmail` varchar(200) DEFAULT NULL,
  `TicketEmailSentAtUtc` datetime DEFAULT NULL,
  `TicketEmailLastError` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `Order`
--

INSERT INTO `Order` (`OrderId`, `OrderNumber`, `UserAccountId`, `ProgramId`, `Status`, `CreatedAtUtc`, `PayBeforeUtc`, `Subtotal`, `VatTotal`, `TotalAmount`, `TicketRecipientFirstName`, `TicketRecipientLastName`, `TicketRecipientEmail`, `TicketEmailSentAtUtc`, `TicketEmailLastError`) VALUES
(10, 'HF-20260316-174541-F1F8FD', 4, 2, 'Pending', '2026-03-16 17:45:41', '2026-03-16 18:15:41', 30.00, 6.30, 36.30, NULL, NULL, NULL, NULL, NULL),
(11, 'HF-20260316-174558-683616', 4, 2, 'Pending', '2026-03-16 17:45:58', '2026-03-16 18:15:58', 30.00, 6.30, 36.30, NULL, NULL, NULL, NULL, NULL),
(12, 'HF-20260316-174811-38ADE1', 4, 2, 'Cancelled', '2026-03-16 17:48:11', '2026-03-16 18:18:11', 30.00, 6.30, 36.30, NULL, NULL, NULL, NULL, NULL),
(13, 'HF-20260316-174828-EF8F67', 4, 2, 'Cancelled', '2026-03-16 17:48:28', '2026-03-16 18:18:28', 30.00, 6.30, 36.30, NULL, NULL, NULL, NULL, NULL),
(14, 'HF-20260316-183635-F5FFB4', 4, 2, 'Pending', '2026-03-16 18:36:35', '2026-03-16 19:06:35', 30.00, 6.30, 36.30, NULL, NULL, NULL, NULL, NULL),
(15, 'HF-20260316-221605-C9131F', 4, 2, 'Cancelled', '2026-03-16 22:16:05', '2026-03-16 22:46:05', 30.00, 6.30, 36.30, NULL, NULL, NULL, NULL, NULL),
(16, 'HF-20260316-221736-1C8AEB', 4, 2, 'Cancelled', '2026-03-16 22:17:36', '2026-03-16 22:47:36', 30.00, 6.30, 36.30, NULL, NULL, NULL, NULL, NULL),
(17, 'HF-20260316-230837-EA7D64', 4, 2, 'Cancelled', '2026-03-16 23:08:37', '2026-03-16 23:38:37', 56.00, 11.76, 67.76, NULL, NULL, NULL, NULL, NULL),
(18, 'HF-20260319-202320-F795D1', 1, 1, 'Pending', '2026-03-19 20:23:20', '2026-03-19 20:53:20', 100.00, 21.00, 121.00, NULL, NULL, NULL, NULL, NULL),
(19, 'HF-20260328-220509-D197A3', 2, 3, 'Pending', '2026-03-28 22:05:09', '2026-03-29 22:05:09', 30.00, 6.30, 36.30, 'Leandro', 'Test', 'propipplayer@gmail.com', '2026-03-28 22:05:47', NULL),
(20, 'HF-20260330-112938-A27321', 1, 1, 'Pending', '2026-03-30 11:29:38', '2026-03-31 11:29:38', 30.00, 6.30, 36.30, 'Leandro', 'Test', 'propipplayer@gmail.com', '2026-03-30 11:30:20', NULL),
(21, 'HF-20260330-120216-44AC09', 2, 3, 'Pending', '2026-03-30 12:02:16', '2026-03-31 12:02:16', 10.00, 2.10, 12.10, 'Test', 'User', 'test@example.com', NULL, NULL),
(22, 'HF-20260330-122634-B1484E', 2, 3, 'Pending', '2026-03-30 12:26:34', '2026-03-31 12:26:34', 75.50, 15.85, 91.36, 'Test', 'User', 'test@example.com', NULL, NULL),
(23, 'HF-20260330-175449-7F8448', 35, 10, 'Pending', '2026-03-30 17:54:49', '2026-03-31 17:54:49', 15.00, 3.15, 18.15, 'E2E', 'Tester_mndhmqz4', 'e2e_mndhmqz4@test.com', NULL, NULL),
(24, 'HF-20260330-180423-305204', 42, 14, 'Pending', '2026-03-30 18:04:23', '2026-03-31 18:04:23', 30.00, 6.30, 36.30, 'E2E', 'Tester_mndhyni2', 'e2e_mndhyni2@test.com', NULL, NULL),
(25, 'HF-20260330-180945-C829F1', 43, 15, 'Pending', '2026-03-30 18:09:45', '2026-03-31 18:09:45', 30.00, 6.30, 36.30, 'E2E', 'Tester_mndi5mfs', 'e2e_mndi5mfs@test.com', NULL, NULL),
(26, 'HF-20260401-183334-B37491', 2, 3, 'Pending', '2026-04-01 18:33:34', '2026-04-02 18:33:34', 10.00, 2.10, 12.10, 'Test', 'User', 'user@test.com', NULL, NULL),
(27, 'HF-20260401-183412-C016F4', 2, 3, 'Pending', '2026-04-01 18:34:12', '2026-04-02 18:34:12', 10.00, 2.10, 12.10, 'Test', 'User', 'user@test.com', NULL, NULL),
(28, 'HF-20260401-183844-805998', 2, 3, 'Pending', '2026-04-01 18:38:44', '2026-04-02 18:38:44', 10.00, 2.10, 12.10, 'Test', 'User', 'user@test.com', NULL, NULL),
(29, 'HF-20260401-183929-3BF9D8', 2, 3, 'Paid', '2026-04-01 18:39:29', '2026-04-02 18:39:29', 10.00, 2.10, 12.10, 'Test', 'User', 'user@test.com', '2026-04-01 18:39:42', NULL),
(30, 'HF-20260401-184657-E9650F', 2, 17, 'Paid', '2026-04-01 18:46:57', '2026-04-02 18:46:57', 10.00, 2.10, 12.10, 'Test', 'User', 'user@test.com', '2026-04-01 18:47:11', NULL),
(31, 'HF-20260401-184900-080019', 2, 18, 'Paid', '2026-04-01 18:49:00', '2026-04-02 18:49:00', 10.00, 2.10, 12.10, 'Test', 'User', 'user@test.com', '2026-04-01 18:49:17', NULL),
(32, 'HF-20260406-111005-C92C61', 1, 1, 'Paid', '2026-04-06 11:10:05', '2026-04-07 11:10:05', 187.50, 39.38, 226.88, 'Leandro', 'Test', 'propipplayer@gmail.com', '2026-04-06 11:38:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `OrderItem`
--

CREATE TABLE `OrderItem` (
  `OrderItemId` int(11) NOT NULL,
  `OrderId` int(11) NOT NULL,
  `EventSessionId` int(11) DEFAULT NULL,
  `HistoryTourId` int(11) DEFAULT NULL,
  `PassPurchaseId` int(11) DEFAULT NULL,
  `Quantity` int(11) NOT NULL,
  `UnitPrice` decimal(10,2) NOT NULL,
  `VatRate` decimal(5,2) NOT NULL,
  `DonationAmount` decimal(10,2) DEFAULT NULL,
  `SpecialRequest` varchar(500) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `OrderItem`
--

INSERT INTO `OrderItem` (`OrderItemId`, `OrderId`, `EventSessionId`, `HistoryTourId`, `PassPurchaseId`, `Quantity`, `UnitPrice`, `VatRate`, `DonationAmount`, `SpecialRequest`) VALUES
(18, 10, 1, NULL, NULL, 2, 15.00, 21.00, 0.00, ''),
(19, 11, 1, NULL, NULL, 2, 15.00, 21.00, 0.00, ''),
(20, 12, 1, NULL, NULL, 2, 15.00, 21.00, 0.00, ''),
(21, 13, 1, NULL, NULL, 2, 15.00, 21.00, 0.00, ''),
(22, 14, 1, NULL, NULL, 2, 15.00, 21.00, 0.00, ''),
(23, 15, 1, NULL, NULL, 2, 15.00, 21.00, 0.00, ''),
(24, 16, 1, NULL, NULL, 2, 15.00, 21.00, 0.00, ''),
(25, 17, 1, NULL, NULL, 2, 15.00, 21.00, 0.00, ''),
(26, 17, 4, NULL, NULL, 2, 10.00, 21.00, 0.00, ''),
(27, 17, 52, NULL, NULL, 2, 0.00, 21.00, 0.00, ''),
(28, 17, 50, NULL, NULL, 1, 6.00, 21.00, 0.00, ''),
(29, 18, 4, NULL, NULL, 4, 10.00, 21.00, 0.00, ''),
(30, 18, 8, NULL, NULL, 2, 15.00, 21.00, 0.00, ''),
(31, 18, 1, NULL, NULL, 2, 15.00, 21.00, 0.00, ''),
(32, 19, 57, NULL, NULL, 3, 10.00, 21.00, 0.00, ''),
(33, 20, 57, NULL, NULL, 3, 10.00, 21.00, 0.00, ''),
(34, 21, 4, NULL, NULL, 1, 10.00, 21.00, 0.00, ''),
(35, 22, 4, NULL, NULL, 3, 10.00, 21.00, 10.50, ''),
(36, 22, NULL, NULL, 1, 1, 35.00, 21.00, 0.00, ''),
(37, 23, 1, NULL, NULL, 1, 15.00, 21.00, 0.00, ''),
(38, 24, 1, NULL, NULL, 2, 15.00, 21.00, 0.00, ''),
(39, 25, 1, NULL, NULL, 2, 15.00, 21.00, 0.00, ''),
(40, 26, 4, NULL, NULL, 1, 10.00, 21.00, 0.00, ''),
(41, 27, 4, NULL, NULL, 1, 10.00, 21.00, 0.00, ''),
(42, 28, 4, NULL, NULL, 1, 10.00, 21.00, 0.00, ''),
(43, 29, 4, NULL, NULL, 1, 10.00, 21.00, 0.00, ''),
(44, 30, 4, NULL, NULL, 1, 10.00, 21.00, 0.00, ''),
(45, 31, 4, NULL, NULL, 1, 10.00, 21.00, 0.00, ''),
(46, 32, 1, NULL, NULL, 2, 15.00, 21.00, 0.00, ''),
(47, 32, 106, NULL, NULL, 1, 17.50, 21.00, 0.00, ''),
(48, 32, 106, NULL, NULL, 2, 60.00, 21.00, 0.00, ''),
(49, 32, 57, NULL, NULL, 2, 10.00, 21.00, 0.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `OrderStatus`
--

CREATE TABLE `OrderStatus` (
  `Status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `OrderStatus`
--

INSERT INTO `OrderStatus` (`Status`) VALUES
('Cancelled'),
('Paid'),
('Pending');

-- --------------------------------------------------------

--
-- Table structure for table `PageGalleryImage`
--

CREATE TABLE `PageGalleryImage` (
  `PageGalleryImageId` int(11) NOT NULL,
  `CmsPageId` int(11) NOT NULL,
  `ImagePath` varchar(500) NOT NULL,
  `ImageType` varchar(20) NOT NULL DEFAULT 'gallery',
  `SortOrder` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `PageGalleryImage`
--

INSERT INTO `PageGalleryImage` (`PageGalleryImageId`, `CmsPageId`, `ImagePath`, `ImageType`, `SortOrder`) VALUES
(1, 2, '/assets/Image/storytelling/d-student.jpg', 'masonry', 1),
(2, 2, '/assets/Image/storytelling/d-student2.jpg', 'masonry', 2),
(3, 2, '/assets/Image/storytelling/m-student.jpg', 'masonry', 3),
(4, 2, '/assets/Image/storytelling/winnie-the-pooh.jpg', 'masonry', 4),
(5, 2, '/assets/Image/storytelling/pig.jpg', 'masonry', 5),
(6, 2, '/assets/Image/storytelling/entrance-kweek.jpg', 'masonry', 6),
(7, 2, '/assets/Image/storytelling/building.jpg', 'masonry', 7),
(8, 2, '/assets/Image/storytelling/anansi-pointing.png', 'masonry', 8),
(9, 2, '/assets/Image/storytelling/anansi-conversation.jpg', 'masonry', 9),
(10, 2, '/assets/Image/storytelling/anansi-drip.jpg', 'masonry', 10),
(11, 2, '/assets/Image/storytelling/anansi-visser.jpg', 'masonry', 11),
(12, 2, '/assets/Image/storytelling/WinnieThePoohHeader.png', 'masonry', 12);

-- --------------------------------------------------------

--
-- Table structure for table `PassPurchase`
--

CREATE TABLE `PassPurchase` (
  `PassPurchaseId` int(11) NOT NULL,
  `PassTypeId` int(11) NOT NULL,
  `UserAccountId` int(11) NOT NULL,
  `ValidDate` date DEFAULT NULL,
  `ValidFromDate` date DEFAULT NULL,
  `ValidToDate` date DEFAULT NULL,
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `PassPurchase`
--

INSERT INTO `PassPurchase` (`PassPurchaseId`, `PassTypeId`, `UserAccountId`, `ValidDate`, `ValidFromDate`, `ValidToDate`, `CreatedAtUtc`) VALUES
(1, 1, 2, NULL, NULL, NULL, '2026-03-30 12:26:34');

-- --------------------------------------------------------

--
-- Table structure for table `PassScope`
--

CREATE TABLE `PassScope` (
  `PassScope` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `PassScope`
--

INSERT INTO `PassScope` (`PassScope`) VALUES
('Day'),
('Range');

-- --------------------------------------------------------

--
-- Table structure for table `PassType`
--

CREATE TABLE `PassType` (
  `PassTypeId` int(11) NOT NULL,
  `EventTypeId` int(11) NOT NULL,
  `PassName` varchar(50) NOT NULL,
  `PassScope` varchar(20) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `VatRate` decimal(5,2) NOT NULL,
  `CurrencyCode` char(3) NOT NULL DEFAULT 'EUR',
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `PassType`
--

INSERT INTO `PassType` (`PassTypeId`, `EventTypeId`, `PassName`, `PassScope`, `Price`, `VatRate`, `CurrencyCode`, `IsActive`) VALUES
(1, 1, 'DayPass', 'Day', 35.00, 21.00, 'EUR', 1),
(2, 1, 'AllAccess', 'Range', 80.00, 21.00, 'EUR', 1),
(3, 2, 'DayPass_Fri', 'Day', 125.00, 21.00, 'EUR', 1),
(4, 2, 'DayPass_SatSun', 'Day', 150.00, 21.00, 'EUR', 1),
(5, 2, 'AllAccess', 'Range', 250.00, 21.00, 'EUR', 1);

-- --------------------------------------------------------

--
-- Table structure for table `PasswordResetToken`
--

CREATE TABLE `PasswordResetToken` (
  `PasswordResetTokenId` int(11) NOT NULL,
  `UserAccountId` int(11) NOT NULL,
  `Token` varchar(100) NOT NULL,
  `ExpiresAtUtc` datetime NOT NULL,
  `UsedAtUtc` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `PasswordResetToken`
--

INSERT INTO `PasswordResetToken` (`PasswordResetTokenId`, `UserAccountId`, `Token`, `ExpiresAtUtc`, `UsedAtUtc`) VALUES
(1, 3, '9a2510646b0c120e32b913488f712f5128769f6d926e9d406168f936cab84056', '2026-02-07 21:20:01', NULL),
(2, 3, '0a18d42c4cabce219bd4fa131d2f6214d26a4bb361215bce8abc7da63e94cc18', '2026-02-07 21:21:54', NULL),
(3, 3, '2b8a0b0a7e46e9f21dfc47eff8ee81464b817747d93649351240528d4fa49ae5', '2026-02-07 21:40:53', NULL),
(4, 3, '5ee6299cc4c9aa83a0451e2e4a9a7a70ba416c88a6fcb9e5ce14119b190325b1', '2026-02-07 21:43:56', NULL),
(5, 3, '48ea3934e18988fd6e0bff5e4798ff7ef8c7907d7d9941e1d36d0c8885a6cdf0', '2026-02-07 21:48:18', NULL),
(6, 3, '0f045683ff14e8f8fe76f988f4bab8738556f127c3856097255e80ce94487b46', '2026-02-07 22:00:06', NULL),
(7, 3, '0ac70b91cadb09f2b935410d1fc98412d885ff94ad13dc0e49b6d5601f5fb0c8', '2026-02-07 22:07:42', NULL),
(8, 3, '748ca7280b26f79c90420284ebf3d15d119743485d514755a405dd91cf3ac3d6', '2026-02-07 22:22:00', NULL),
(9, 3, 'c758e083d2c74259c71a70cffc8db943f1ae2f2d92d0bf427e7d250a3dcab623', '2026-02-07 22:23:16', NULL),
(10, 3, '56639e6d7d9974d421548ddf58a71a989d47a89d92987d717a1c5905c688343f', '2026-02-07 22:25:58', NULL),
(11, 3, '6f0df28ea9328c68bc661f7f134999a67174b17f74d08462aa8c688b4da09a52', '2026-02-07 22:29:08', '2026-02-07 21:29:41');

-- --------------------------------------------------------

--
-- Table structure for table `Payment`
--

CREATE TABLE `Payment` (
  `PaymentId` int(11) NOT NULL,
  `OrderId` int(11) NOT NULL,
  `Method` varchar(20) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `ProviderRef` varchar(80) DEFAULT NULL,
  `StripeCheckoutSessionId` varchar(120) DEFAULT NULL,
  `StripePaymentIntentId` varchar(120) DEFAULT NULL,
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp(),
  `PaidAtUtc` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `Payment`
--

INSERT INTO `Payment` (`PaymentId`, `OrderId`, `Method`, `Status`, `ProviderRef`, `StripeCheckoutSessionId`, `StripePaymentIntentId`, `CreatedAtUtc`, `PaidAtUtc`) VALUES
(10, 10, 'CreditCard', 'Pending', 'cs_test_a18VmTP50PSVDhBj8FSQ3ToUBxp2UCqTsIAapu1mYl0bMmlhoNOP2XjjsI', 'cs_test_a18VmTP50PSVDhBj8FSQ3ToUBxp2UCqTsIAapu1mYl0bMmlhoNOP2XjjsI', 'pi_3TBfD0CvFyf0GBTv1xUpE5oU', '2026-03-16 17:45:41', NULL),
(11, 11, 'Ideal', 'Pending', 'cs_test_a1vaq9NLY4CpnzDVjrkrkVu9PuQa94Uh2p30iteOdtsLB9CXksjwiQmZFn', 'cs_test_a1vaq9NLY4CpnzDVjrkrkVu9PuQa94Uh2p30iteOdtsLB9CXksjwiQmZFn', 'pi_3TBfDGCvFyf0GBTv0mABvUJX', '2026-03-16 17:45:58', NULL),
(12, 12, 'CreditCard', 'Cancelled', 'cs_test_a16I3961CVTg6iabyI7dMVkneWAOyRFY0btFPldwd3banUqg4yKAlrr7vY', 'cs_test_a16I3961CVTg6iabyI7dMVkneWAOyRFY0btFPldwd3banUqg4yKAlrr7vY', 'pi_3TBfFQCvFyf0GBTv19K4HH5T', '2026-03-16 17:48:11', NULL),
(13, 13, 'Ideal', 'Cancelled', 'cs_test_a1xBEwszvFloVJGUfEHscqNwPycFqEgzyfnWed2Cyvdsl9BS1trl6wg53J', 'cs_test_a1xBEwszvFloVJGUfEHscqNwPycFqEgzyfnWed2Cyvdsl9BS1trl6wg53J', 'pi_3TBfFgCvFyf0GBTv0n9nztoh', '2026-03-16 17:48:28', NULL),
(14, 14, 'CreditCard', 'Pending', 'cs_test_a1TpxZNKwxvUeEhUCnWHSjqkHS94fgtSyJvsjDVDxuDqfwZPHVIo35FU4Y', 'cs_test_a1TpxZNKwxvUeEhUCnWHSjqkHS94fgtSyJvsjDVDxuDqfwZPHVIo35FU4Y', NULL, '2026-03-16 18:36:35', NULL),
(15, 15, 'CreditCard', 'Cancelled', 'cs_test_a1920U3ZRXuK61lBHtAJexqg98cRWEORZ0Q8t2VgoTiRBtDminTgpkt2sR', 'cs_test_a1920U3ZRXuK61lBHtAJexqg98cRWEORZ0Q8t2VgoTiRBtDminTgpkt2sR', NULL, '2026-03-16 22:16:05', NULL),
(16, 16, 'Ideal', 'Cancelled', 'cs_test_a16zshY72dDe7yQ8iXPkJ5kh3PNyac8bCI2jfr4sabmXhv2RpXpodKBQn3', 'cs_test_a16zshY72dDe7yQ8iXPkJ5kh3PNyac8bCI2jfr4sabmXhv2RpXpodKBQn3', NULL, '2026-03-16 22:17:36', NULL),
(17, 17, 'CreditCard', 'Cancelled', 'cs_test_a1lveqgI586Po3ctwFokDHAIQQslWrrp7f5r7RwJyUsDFnKjDS27DKvmw0', 'cs_test_a1lveqgI586Po3ctwFokDHAIQQslWrrp7f5r7RwJyUsDFnKjDS27DKvmw0', NULL, '2026-03-16 23:08:37', NULL),
(18, 18, 'CreditCard', 'Pending', 'cs_test_a1gES006gJ7Bb4l19v6pHQGQlnFZ2QwlhNCoje67YAvtfgNyXKu4ARPOEa', 'cs_test_a1gES006gJ7Bb4l19v6pHQGQlnFZ2QwlhNCoje67YAvtfgNyXKu4ARPOEa', NULL, '2026-03-19 20:23:20', NULL),
(19, 19, 'CreditCard', 'Pending', 'cs_test_a13pbzIzl0tXMCmP7vLbp9Ds5IpuHmg5itO7TEPgSO9nQ8wn2gEh9dssr1', 'cs_test_a13pbzIzl0tXMCmP7vLbp9Ds5IpuHmg5itO7TEPgSO9nQ8wn2gEh9dssr1', NULL, '2026-03-28 22:05:09', NULL),
(20, 20, 'CreditCard', 'Pending', 'cs_test_a1ip5swWqgmHTSnPAo7cawOKwjKKHrb9sdFRYk56rPvO3YOQy35yg1tGjQ', 'cs_test_a1ip5swWqgmHTSnPAo7cawOKwjKKHrb9sdFRYk56rPvO3YOQy35yg1tGjQ', NULL, '2026-03-30 11:29:38', NULL),
(21, 20, 'CreditCard', 'Pending', 'cs_test_a17KKUVErUfgQiudsZI0LLQM9o2xmhMUvagOjn9LsVFDYQDO8vMa8taROh', 'cs_test_a17KKUVErUfgQiudsZI0LLQM9o2xmhMUvagOjn9LsVFDYQDO8vMa8taROh', NULL, '2026-03-30 11:35:16', NULL),
(22, 20, 'CreditCard', 'Pending', 'cs_test_a1yjS3DDUdyy0CY8Lli0imq4Y7n51K1TRRjBYayZKDD6EmgMosgYvvPAkm', 'cs_test_a1yjS3DDUdyy0CY8Lli0imq4Y7n51K1TRRjBYayZKDD6EmgMosgYvvPAkm', NULL, '2026-03-30 11:35:19', NULL),
(23, 20, 'CreditCard', 'Pending', 'cs_test_a17BUzvpRPw6Y86m123x0hdmHj8FLxOgXJnSQU8BNkAn4kOKjzSXlDrA0y', 'cs_test_a17BUzvpRPw6Y86m123x0hdmHj8FLxOgXJnSQU8BNkAn4kOKjzSXlDrA0y', NULL, '2026-03-30 11:36:41', NULL),
(24, 20, 'CreditCard', 'Pending', 'cs_test_a1J81qXJA2VHe65lWfOuydOLKGhtsGs0CRJPvGv1D9CU23WzNSpg4AZylO', 'cs_test_a1J81qXJA2VHe65lWfOuydOLKGhtsGs0CRJPvGv1D9CU23WzNSpg4AZylO', NULL, '2026-03-30 11:36:44', NULL),
(25, 21, 'CreditCard', 'Pending', 'cs_test_a13QlTatU6TZBGpQs4DOzGXHZItfF4jUfnYTW6WrRo6USg31ZXsZCKtObP', 'cs_test_a13QlTatU6TZBGpQs4DOzGXHZItfF4jUfnYTW6WrRo6USg31ZXsZCKtObP', NULL, '2026-03-30 12:02:16', NULL),
(26, 21, 'CreditCard', 'Pending', 'cs_test_a1VnSCadz6RG0LMbzKxiCPQX7wiRSMreTYa1gSQi89ZJP6aoXE4CuWXOfn', 'cs_test_a1VnSCadz6RG0LMbzKxiCPQX7wiRSMreTYa1gSQi89ZJP6aoXE4CuWXOfn', NULL, '2026-03-30 12:03:11', NULL),
(27, 21, 'CreditCard', 'Pending', 'cs_test_a15lCjd04iZxp9hidF5VoYg89lpbKppDRVafDGRcnMEk8HkquqPEcakXgK', 'cs_test_a15lCjd04iZxp9hidF5VoYg89lpbKppDRVafDGRcnMEk8HkquqPEcakXgK', NULL, '2026-03-30 12:04:55', NULL),
(28, 22, 'CreditCard', 'Pending', 'cs_test_a1s0wV85ymDI0RgWZAc18PG1DUSwNSZFN0YfsJjG69dXRIn7HPlZW1HsGA', 'cs_test_a1s0wV85ymDI0RgWZAc18PG1DUSwNSZFN0YfsJjG69dXRIn7HPlZW1HsGA', NULL, '2026-03-30 12:26:34', NULL),
(29, 22, 'Ideal', 'Pending', 'cs_test_a181w5oQzAZunkAHNk4VCuS0GuZYLriwPxUYFhQyEoIDjoUXg2KnkWm17m', 'cs_test_a181w5oQzAZunkAHNk4VCuS0GuZYLriwPxUYFhQyEoIDjoUXg2KnkWm17m', NULL, '2026-03-30 12:27:14', NULL),
(30, 23, 'CreditCard', 'Pending', 'cs_test_a1RyeIwIMPXkxYmkldGnVp2hHPIeuKRkOuSU61iaTbv3cEDcnx38SGfg0y', 'cs_test_a1RyeIwIMPXkxYmkldGnVp2hHPIeuKRkOuSU61iaTbv3cEDcnx38SGfg0y', NULL, '2026-03-30 17:54:49', NULL),
(31, 24, 'CreditCard', 'Pending', 'cs_test_a1OtN1luzDcCcCUMKdpoUhntpHXedm2ehba6eRUbBeezoqrGS6i5RXpGW8', 'cs_test_a1OtN1luzDcCcCUMKdpoUhntpHXedm2ehba6eRUbBeezoqrGS6i5RXpGW8', NULL, '2026-03-30 18:04:23', NULL),
(32, 25, 'CreditCard', 'Pending', 'cs_test_a1SS2jTnYsTaLIqM8fU1YYqxKERgkFvVucLsRgEB2YHNYOH0sNs0V2zOXP', 'cs_test_a1SS2jTnYsTaLIqM8fU1YYqxKERgkFvVucLsRgEB2YHNYOH0sNs0V2zOXP', NULL, '2026-03-30 18:09:45', NULL),
(33, 26, 'CreditCard', 'Pending', 'cs_test_a1lxL4sKpHhcbZ7242dWFOKf07FwDPzjIJs8pYwyYQkvDFK9jR5psshMSY', 'cs_test_a1lxL4sKpHhcbZ7242dWFOKf07FwDPzjIJs8pYwyYQkvDFK9jR5psshMSY', NULL, '2026-04-01 18:33:34', NULL),
(34, 27, 'CreditCard', 'Pending', 'cs_test_a1zLVQ8ci6JIvnyVlLuVubr4EgDxwE3niUezYXw8Qo8VikYF7lCDDJ7SaV', 'cs_test_a1zLVQ8ci6JIvnyVlLuVubr4EgDxwE3niUezYXw8Qo8VikYF7lCDDJ7SaV', NULL, '2026-04-01 18:34:12', NULL),
(35, 28, 'CreditCard', 'Pending', 'cs_test_a1vzdZD14hoYID6ja1CurazY27FG6odHqPPEQV48hVRYPWyVvsJRk91kiR', 'cs_test_a1vzdZD14hoYID6ja1CurazY27FG6odHqPPEQV48hVRYPWyVvsJRk91kiR', NULL, '2026-04-01 18:38:44', NULL),
(36, 29, 'CreditCard', 'Paid', 'cs_test_a1lNiZbyxsCVxcNwMhsEDK7z0jx0JMi5o4ytyL5JP4qKhgUQDS697a2Ntv', 'cs_test_a1lNiZbyxsCVxcNwMhsEDK7z0jx0JMi5o4ytyL5JP4qKhgUQDS697a2Ntv', NULL, '2026-04-01 18:39:29', '2026-04-01 18:39:40'),
(37, 30, 'CreditCard', 'Paid', 'cs_test_a1ZdFaCy2JzSjyNYDwHwSSTyaKfJtSEfpxqjluSKFT7hOteqAzU2puRUIh', 'cs_test_a1ZdFaCy2JzSjyNYDwHwSSTyaKfJtSEfpxqjluSKFT7hOteqAzU2puRUIh', NULL, '2026-04-01 18:46:57', '2026-04-01 18:47:10'),
(38, 31, 'CreditCard', 'Paid', 'cs_test_a1WPzEXr6rYYmsx4CnePZ317Dj6H3i6XzUqWgFbqoXM45KaqeXpktAacgW', 'cs_test_a1WPzEXr6rYYmsx4CnePZ317Dj6H3i6XzUqWgFbqoXM45KaqeXpktAacgW', NULL, '2026-04-01 18:49:00', '2026-04-01 18:49:15'),
(39, 32, 'CreditCard', 'Paid', 'cs_test_a1Yop1EVSbG6tqThgYEUhKZTiuW8XEFy4GCiL1fDOxt1kSeiOon1gLcQqN', 'cs_test_a1Yop1EVSbG6tqThgYEUhKZTiuW8XEFy4GCiL1fDOxt1kSeiOon1gLcQqN', NULL, '2026-04-06 11:10:05', '2026-04-06 11:10:34');

--
-- Triggers `Payment`
--
DELIMITER $$
CREATE TRIGGER `Payment_Check_One_Paid_Before_Insert` BEFORE INSERT ON `Payment` FOR EACH ROW BEGIN
    IF NEW.Status = 'Paid' THEN
        IF EXISTS (SELECT 1 FROM Payment WHERE OrderId = NEW.OrderId AND Status = 'Paid') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Only one Paid payment is allowed per order';
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `Payment_Check_One_Paid_Before_Update` BEFORE UPDATE ON `Payment` FOR EACH ROW BEGIN
    IF NEW.Status = 'Paid' AND OLD.Status != 'Paid' THEN
        IF EXISTS (SELECT 1 FROM Payment WHERE OrderId = NEW.OrderId AND Status = 'Paid' AND PaymentId != NEW.PaymentId) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Only one Paid payment is allowed per order';
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `PaymentMethod`
--

CREATE TABLE `PaymentMethod` (
  `Method` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `PaymentMethod`
--

INSERT INTO `PaymentMethod` (`Method`) VALUES
('CreditCard'),
('Ideal');

-- --------------------------------------------------------

--
-- Table structure for table `PaymentStatus`
--

CREATE TABLE `PaymentStatus` (
  `Status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `PaymentStatus`
--

INSERT INTO `PaymentStatus` (`Status`) VALUES
('Cancelled'),
('Failed'),
('Paid'),
('Pending'),
('Refunded');

-- --------------------------------------------------------

--
-- Table structure for table `PriceTier`
--

CREATE TABLE `PriceTier` (
  `PriceTierId` int(11) NOT NULL,
  `Name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `PriceTier`
--

INSERT INTO `PriceTier` (`PriceTierId`, `Name`) VALUES
(1, 'Adult'),
(2, 'ChildU12'),
(3, 'Family'),
(7, 'Group'),
(5, 'PayWhatYouLike'),
(4, 'ReservationFee'),
(6, 'Single');

-- --------------------------------------------------------

--
-- Table structure for table `Program`
--

CREATE TABLE `Program` (
  `ProgramId` int(11) NOT NULL,
  `UserAccountId` int(11) DEFAULT NULL,
  `SessionKey` varchar(80) DEFAULT NULL,
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp(),
  `IsCheckedOut` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `Program`
--

INSERT INTO `Program` (`ProgramId`, `UserAccountId`, `SessionKey`, `CreatedAtUtc`, `IsCheckedOut`) VALUES
(1, 1, 'a166ed007a98ae89b75febd35432ef5f', '2026-03-16 15:04:58', 1),
(2, 4, 'aaf6d15d725c84ed94159af85e4036f5', '2026-03-16 17:40:46', 0),
(3, 2, '4a6c387b60526f45ee14d9c0f7dfdafe', '2026-03-28 22:04:42', 1),
(4, 12, 'fec4419921fa42732201c957ceaf4c4d', '2026-03-30 17:04:36', 0),
(5, 30, '463e5fcb1f7fdba341e1b9a7acec85a1', '2026-03-30 17:53:41', 0),
(6, 31, '3fcefc01a93f0b22a7b4f5ee3685e96c', '2026-03-30 17:53:50', 0),
(7, 32, '14018a1182379107ad0f7bd35ae24059', '2026-03-30 17:54:01', 0),
(8, 33, 'f63fafd749677dea372afd1a08928e05', '2026-03-30 17:54:24', 0),
(9, 34, 'a0fb5de74088bdcff8ca10c2743ee914', '2026-03-30 17:54:37', 0),
(10, 35, 'd72e39510d05a871ebe11dab1ec3a099', '2026-03-30 17:54:47', 0),
(11, 36, '7bf3aa1baeab418d2ebf820091219827', '2026-03-30 17:54:56', 0),
(12, 37, 'd1188d3234381636b0fd3e119a16c9ea', '2026-03-30 17:55:05', 0),
(13, 38, 'ab02bf3d430e4bf64a566a666d68f929', '2026-03-30 17:55:15', 0),
(14, 42, 'b1dbb65017d9d373e5ab9f28e613daaa', '2026-03-30 18:04:16', 0),
(15, 43, 'bda1a0c3a469314244e966ce3ffe1c22', '2026-03-30 18:09:36', 0),
(16, 44, 'ecd6cb7265cc7747bd49f538fe58a646', '2026-03-30 18:15:07', 0),
(17, 2, '7151f068ff6634a7345b24b370485f1a', '2026-04-01 18:46:52', 1),
(18, 2, 'aae088b32af5d9dfc8a125e222e2a6e5', '2026-04-01 18:48:55', 1),
(19, NULL, '19a9f23a5c68b6695853e909f50f7dd4', '2026-04-02 15:19:55', 0),
(21, NULL, '8679027338ca45f4f7cba8d1ca5f6a08', '2026-04-02 15:27:41', 0),
(23, NULL, 'e6b58a3268bf414a7a35a8cff3622b1d', '2026-04-02 15:42:20', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ProgramItem`
--

CREATE TABLE `ProgramItem` (
  `ProgramItemId` int(11) NOT NULL,
  `ProgramId` int(11) NOT NULL,
  `EventSessionId` int(11) DEFAULT NULL,
  `HistoryTourId` int(11) DEFAULT NULL,
  `ReservationId` int(10) UNSIGNED DEFAULT NULL,
  `PassTypeId` int(11) DEFAULT NULL,
  `PassValidDate` date DEFAULT NULL,
  `Quantity` int(11) NOT NULL,
  `PriceTierId` int(11) DEFAULT NULL,
  `DonationAmount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `ProgramItem`
--

INSERT INTO `ProgramItem` (`ProgramItemId`, `ProgramId`, `EventSessionId`, `HistoryTourId`, `ReservationId`, `PassTypeId`, `PassValidDate`, `Quantity`, `PriceTierId`, `DonationAmount`) VALUES
(6, 2, 1, NULL, NULL, NULL, NULL, 2, NULL, 0.00),
(7, 2, 4, NULL, NULL, NULL, NULL, 2, NULL, 0.00),
(10, 2, 52, NULL, NULL, NULL, NULL, 2, NULL, 0.00),
(11, 2, 50, NULL, NULL, NULL, NULL, 1, NULL, 0.00),
(34, 4, 1, NULL, NULL, NULL, NULL, 1, NULL, 0.00),
(35, 5, 1, NULL, NULL, NULL, NULL, 1, NULL, 0.00),
(36, 6, 1, NULL, NULL, NULL, NULL, 1, NULL, 0.00),
(37, 7, 1, NULL, NULL, NULL, NULL, 2, NULL, 0.00),
(39, 9, 1, NULL, NULL, NULL, NULL, 1, NULL, 0.00),
(40, 10, 1, NULL, NULL, NULL, NULL, 1, NULL, 0.00),
(41, 11, 1, NULL, NULL, NULL, NULL, 2, NULL, 0.00),
(42, 12, 1, NULL, NULL, NULL, NULL, 1, NULL, 0.00),
(43, 13, 1, NULL, NULL, NULL, NULL, 1, NULL, 0.00),
(44, 14, 1, NULL, NULL, NULL, NULL, 2, NULL, 0.00),
(45, 15, 1, NULL, NULL, NULL, NULL, 2, NULL, 0.00),
(46, 16, 1, NULL, NULL, NULL, NULL, 2, NULL, 0.00),
(50, 3, 4, NULL, NULL, NULL, NULL, 1, NULL, 0.00),
(51, 17, 4, NULL, NULL, NULL, NULL, 1, NULL, 0.00),
(52, 18, 4, NULL, NULL, NULL, NULL, 1, NULL, 0.00),
(54, 21, 38, NULL, NULL, NULL, NULL, 1, 1, 0.00),
(57, 23, 90, NULL, NULL, NULL, NULL, 1, 3, 0.00),
(62, 1, 1, NULL, NULL, NULL, NULL, 2, 1, 0.00),
(63, 1, 106, NULL, NULL, NULL, NULL, 1, 6, 0.00),
(64, 1, 106, NULL, NULL, NULL, NULL, 2, 3, 0.00),
(65, 1, 57, NULL, NULL, NULL, NULL, 2, 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `Reservation`
--

CREATE TABLE `Reservation` (
  `ReservationId` int(10) UNSIGNED NOT NULL,
  `DiningDate` varchar(20) NOT NULL COMMENT 'Thursday / Friday / Saturday / Sunday',
  `TimeSlot` varchar(20) NOT NULL COMMENT 'e.g. 16:30',
  `AdultsCount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ChildrenCount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `SpecialRequests` text DEFAULT NULL,
  `TotalFee` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '10 per person reservation deposit',
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `EventId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ScheduleDay`
--

CREATE TABLE `ScheduleDay` (
  `ScheduleDayId` int(11) NOT NULL,
  `EventTypeId` int(11) NOT NULL,
  `Date` date NOT NULL,
  `IsDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `DeletedAtUtc` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `ScheduleDay`
--

INSERT INTO `ScheduleDay` (`ScheduleDayId`, `EventTypeId`, `Date`, `IsDeleted`, `DeletedAtUtc`) VALUES
(1, 1, '2026-07-23', 0, NULL),
(2, 1, '2026-07-24', 0, NULL),
(3, 1, '2026-07-25', 0, NULL),
(4, 1, '2026-07-26', 0, NULL),
(5, 2, '2026-07-24', 0, NULL),
(6, 2, '2026-07-25', 0, NULL),
(7, 2, '2026-07-26', 0, NULL),
(8, 3, '2026-07-23', 0, NULL),
(9, 3, '2026-07-24', 0, NULL),
(10, 3, '2026-07-25', 0, NULL),
(11, 3, '2026-07-26', 0, NULL),
(12, 4, '2026-07-23', 0, NULL),
(13, 4, '2026-07-24', 0, NULL),
(14, 4, '2026-07-25', 0, NULL),
(15, 4, '2026-07-26', 0, NULL),
(16, 5, '2026-07-23', 0, NULL),
(17, 5, '2026-07-24', 0, NULL),
(18, 5, '2026-07-25', 0, NULL),
(19, 5, '2026-07-26', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ScheduleDayConfig`
--

CREATE TABLE `ScheduleDayConfig` (
  `ScheduleDayConfigId` int(11) NOT NULL,
  `EventTypeId` int(11) NOT NULL DEFAULT 0,
  `DayOfWeek` tinyint(4) NOT NULL,
  `IsVisible` tinyint(1) NOT NULL DEFAULT 1,
  `UpdatedAtUtc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `ScheduleDayConfig`
--

INSERT INTO `ScheduleDayConfig` (`ScheduleDayConfigId`, `EventTypeId`, `DayOfWeek`, `IsVisible`, `UpdatedAtUtc`) VALUES
(1, 0, 0, 1, '2026-03-30 18:01:52'),
(2, 0, 1, 1, '2026-03-19 13:44:24'),
(3, 0, 2, 1, '2026-03-19 13:44:25'),
(4, 0, 3, 1, '2026-03-19 13:44:30'),
(5, 0, 4, 1, '2026-03-19 13:45:19'),
(6, 0, 5, 1, '2026-02-11 00:19:42'),
(7, 0, 6, 1, '2026-03-30 10:19:59'),
(33, 3, 0, 1, '2026-02-16 21:10:38'),
(34, 3, 4, 1, '2026-03-19 13:41:32'),
(35, 3, 5, 1, '2026-02-16 21:10:40'),
(38, 3, 3, 1, '2026-03-19 13:44:43'),
(44, 1, 3, 1, '2026-03-19 13:41:37'),
(49, 5, 3, 1, '2026-03-19 13:41:42'),
(69, 2, 0, 1, '2026-03-30 18:02:06');

-- --------------------------------------------------------

--
-- Table structure for table `SessionDiscountRule`
--

CREATE TABLE `SessionDiscountRule` (
  `SessionDiscountRuleId` int(11) NOT NULL,
  `EventSessionId` int(11) NOT NULL,
  `RuleName` varchar(60) NOT NULL,
  `DiscountPercent` decimal(5,2) NOT NULL,
  `AppliesToPriceTierId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SessionType`
--

CREATE TABLE `SessionType` (
  `SessionType` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `SessionType`
--

INSERT INTO `SessionType` (`SessionType`) VALUES
('Back2Back'),
('Club'),
('Dinner'),
('Live'),
('Podcast'),
('Storytelling'),
('TiëstoWorld'),
('Tour');

-- --------------------------------------------------------

--
-- Table structure for table `StarRating`
--

CREATE TABLE `StarRating` (
  `Stars` int(11) NOT NULL,
  `Label` varchar(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `StarRating`
--

INSERT INTO `StarRating` (`Stars`, `Label`) VALUES
(1, '1 Star'),
(2, '2 Stars'),
(3, '3 Stars'),
(4, '4 Stars'),
(5, '5 Stars');

-- --------------------------------------------------------

--
-- Table structure for table `StripeWebhookEvent`
--

CREATE TABLE `StripeWebhookEvent` (
  `StripeWebhookEventId` int(11) NOT NULL,
  `StripeEventId` varchar(120) NOT NULL,
  `EventType` varchar(120) NOT NULL,
  `ProcessedAtUtc` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `StripeWebhookEvent`
--

INSERT INTO `StripeWebhookEvent` (`StripeWebhookEventId`, `StripeEventId`, `EventType`, `ProcessedAtUtc`) VALUES
(1, 'evt_3TBfD0CvFyf0GBTv1RncMR1y', 'payment_intent.created', '2026-03-16 17:45:43'),
(2, 'evt_3TBfDGCvFyf0GBTv0T5AghLT', 'payment_intent.created', '2026-03-16 17:45:59'),
(5, 'evt_3TBfFQCvFyf0GBTv1rRmXJg5', 'payment_intent.created', '2026-03-16 17:48:12'),
(6, 'evt_3TBfFgCvFyf0GBTv07mfOXlk', 'payment_intent.created', '2026-03-16 17:48:29');

-- --------------------------------------------------------

--
-- Table structure for table `Ticket`
--

CREATE TABLE `Ticket` (
  `TicketId` int(11) NOT NULL,
  `OrderItemId` int(11) NOT NULL,
  `TicketCode` varchar(80) NOT NULL,
  `IsScanned` tinyint(1) NOT NULL DEFAULT 0,
  `ScannedAtUtc` datetime DEFAULT NULL,
  `ScannedByUserId` int(11) DEFAULT NULL,
  `PdfAssetId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `Ticket`
--

INSERT INTO `Ticket` (`TicketId`, `OrderItemId`, `TicketCode`, `IsScanned`, `ScannedAtUtc`, `ScannedByUserId`, `PdfAssetId`) VALUES
(1, 32, 'HF-YZNXBU7VA34GFLTF', 1, '2026-03-30 12:27:12', 1, 120),
(2, 32, 'HF-TUKL6Q52F58NFRHT', 1, '2026-03-30 12:27:12', 1, 121),
(3, 32, 'HF-6NTWU8BPC9FKTKMW', 1, '2026-03-30 12:27:11', 1, 122),
(4, 33, 'HF-E7YK5KEMMPB5WNFD', 1, '2026-03-30 11:30:56', 1, 123),
(5, 33, 'HF-6APKWGDAPU3J2QNV', 1, '2026-03-30 12:01:40', 1, 124),
(6, 33, 'HF-P6PCVLZPNT7L7BQ5', 1, '2026-03-30 12:01:40', 1, 125),
(7, 43, 'HF-9TCZWTJAZY3NPWMJ', 0, NULL, NULL, 129),
(8, 44, 'HF-PZQWQDUKS49Q5HUT', 0, NULL, NULL, 130),
(9, 45, 'HF-DKB3GQQKTQWKBQKF', 0, NULL, NULL, 131),
(10, 46, 'HF-8WRJNFU4TH3MQCBS', 0, NULL, NULL, 132),
(11, 46, 'HF-CUUD2G55SNULTJDK', 0, NULL, NULL, 133),
(12, 47, 'HF-WHGCMN56H3FBYXUU', 0, NULL, NULL, 134),
(13, 48, 'HF-6XDEF8GW2KN9FNX5', 0, NULL, NULL, 135),
(14, 48, 'HF-T29B7R664ZVGKHA3', 0, NULL, NULL, 136),
(15, 49, 'HF-BSFNUM9KRTKK68UQ', 0, NULL, NULL, 137),
(16, 49, 'HF-3CTFZ28HH92ZFR8R', 0, NULL, NULL, 138);

-- --------------------------------------------------------

--
-- Table structure for table `UserAccount`
--

CREATE TABLE `UserAccount` (
  `UserAccountId` int(11) NOT NULL,
  `UserRoleId` int(11) NOT NULL,
  `Username` varchar(60) NOT NULL,
  `Email` varchar(200) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `PasswordSalt` varbinary(32) DEFAULT NULL,
  `FirstName` varchar(60) NOT NULL,
  `LastName` varchar(80) NOT NULL,
  `ProfilePictureAssetId` int(11) DEFAULT NULL,
  `IsEmailConfirmed` tinyint(1) NOT NULL DEFAULT 0,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `RegisteredAtUtc` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdatedAtUtc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `UserAccount`
--

INSERT INTO `UserAccount` (`UserAccountId`, `UserRoleId`, `Username`, `Email`, `PasswordHash`, `PasswordSalt`, `FirstName`, `LastName`, `ProfilePictureAssetId`, `IsEmailConfirmed`, `IsActive`, `RegisteredAtUtc`, `UpdatedAtUtc`) VALUES
(1, 3, 'testadmin', 'admin@test.com', '$argon2id$v=19$m=65536,t=4,p=1$SHlsUUo3eXBCY01WV1VJZQ$6n1f58bJE7i4MYphBXDuvpR4G+N261zuEVZde8BYGjY', NULL, 'Test', 'Admin', NULL, 1, 1, '2026-02-07 19:39:57', '2026-04-05 20:08:11'),
(2, 1, 'testuser', 'user@test.com', '$argon2id$v=19$m=65536,t=4,p=1$amsvVU9QNU5QL1dKM3QzeQ$xmHiIhwxaWEOu5RXiOKUc5KPFex/JL0NV2cmtM0IKhc', NULL, 'Test', 'User', NULL, 1, 1, '2026-02-07 19:39:57', '2026-02-07 19:39:57'),
(3, 2, 'leandro', 'propipplayer@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$YnBXZktSVEl6THJFUkQuLw$VDzijF0bET0wUsjprdH+3/kSW6Iit5wbErVX+ClELdE', NULL, 'Leandro', 'Nunez', NULL, 0, 1, '2026-02-07 20:19:38', '2026-03-30 10:25:37'),
(4, 1, 'kianeutron', 'kianexample@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$bnkvRXZCYmlQdmgyUU9Jaw$7ewAMpBbmPhemB/2LRncDOALwuaSH1uCmHI+AdMIlKI', NULL, 'Kian', 'Khatibi', NULL, 0, 1, '2026-03-16 17:40:26', '2026-03-16 17:40:26'),
(5, 1, 'e2euser_mndfak80', 'e2e_mndfak80@test.com', '$argon2id$v=19$m=65536,t=4,p=1$L3NFdk1DSU11WHBpZnJDaw$8H9m/n33fmclpzeVPk8FdkLv2UhkRa8pdSJSRevPixs', NULL, 'E2E', 'Tester_mndfak80', NULL, 0, 1, '2026-03-30 16:49:11', '2026-03-30 16:50:00'),
(6, 3, 'admin_e2euser_mndfao8y', 'admin_e2e_mndfao8y@test.com', '$argon2id$v=19$m=65536,t=4,p=1$WnR0UERtREo3ZXRjT2ZIbw$3kBFtoFKGK3x1t18pj8cAukagkaWPyOJGB29FaECNos', NULL, 'Admin', 'Tester_mndfao8y', NULL, 0, 0, '2026-03-30 16:49:17', '2026-04-01 19:51:53'),
(7, 1, 'e2euser_mndfstzz', 'e2e_mndfstzz@test.com', '$argon2id$v=19$m=65536,t=4,p=1$RklLUW9nZTIySTZmYy5Zcg$+VBYsfXjKmDI7zrKU+g6j1j0BE67SoyjGPy1T3/nRko', NULL, 'E2E', 'Tester_mndfstzz', NULL, 0, 1, '2026-03-30 17:03:37', '2026-03-30 17:03:37'),
(8, 1, 'e2euser_mndft6pq', 'e2e_mndft6pq@test.com', '$argon2id$v=19$m=65536,t=4,p=1$Ym10M2ZsZHRWUGsxcC80QQ$dKnF73rp7sawDAHxzE5dPR+f2jEbnYk8dBsMasyXBt8', NULL, 'E2E', 'Tester_mndft6pq', NULL, 0, 1, '2026-03-30 17:03:42', '2026-03-30 17:03:42'),
(9, 1, 'e2euser_mndftj65', 'e2e_mndftj65@test.com', '$argon2id$v=19$m=65536,t=4,p=1$eGpWOWREeXk2Q1oxM0VFYg$WbeJbSEkKr45Q0s2oSloJyJ7T3PlQF1tK/4x0//e/j0', NULL, 'E2E', 'Tester_mndftj65', NULL, 0, 1, '2026-03-30 17:03:58', '2026-03-30 17:03:58'),
(10, 1, 'e2euser_mndftn9p', 'e2e_mndftn9p@test.com', '$argon2id$v=19$m=65536,t=4,p=1$MlNxVngub3Fvc3hPUVd0QQ$YT3pDnROjf6e1BLvgNQX86yMyJSPyZAa5c4cX8hO1j4', NULL, 'E2E', 'Tester_mndftn9p', NULL, 0, 1, '2026-03-30 17:04:04', '2026-03-30 17:04:04'),
(11, 1, 'e2euser_mndftu5e', 'e2e_mndftu5e@test.com', '$argon2id$v=19$m=65536,t=4,p=1$YXpBcGV0TjZicVdrZHNaTA$f+HZINHsuhIr4F75FbbXsCNb+9ZdswLhlV1Jot1pfs4', NULL, 'E2E', 'Tester_mndftu5e', NULL, 0, 1, '2026-03-30 17:04:13', '2026-03-30 17:04:13'),
(12, 1, 'e2euser_mndfu23q', 'e2e_mndfu23q@test.com', '$argon2id$v=19$m=65536,t=4,p=1$N1JxaDgvSjdxdUEyMUdnQg$kQgh++65QljjOnD1CUMzmDC34217pQQ8fPyrIMRFhfM', NULL, 'E2E', 'Tester_mndfu23q', NULL, 0, 1, '2026-03-30 17:04:23', '2026-03-30 17:04:23'),
(13, 1, 'e2euser_mndfufjy', 'e2e_mndfufjy@test.com', '$argon2id$v=19$m=65536,t=4,p=1$UGpoOVUwTGlLVVJBL3U2ZQ$pto1RsjWr6HoONWXOGk/Gf1yboHKrepVDJx0eZz1LmU', NULL, 'E2E', 'Tester_mndfufjy', NULL, 0, 1, '2026-03-30 17:04:41', '2026-03-30 17:04:41'),
(14, 1, 'e2euser_mndfuth9', 'e2e_mndfuth9@test.com', '$argon2id$v=19$m=65536,t=4,p=1$aG1XNml6OElyNVB4d0xkMA$3cN0o7wQ7Xk+6EgN2ET0QgybrrTQL98pgirJqRBrWAg', NULL, 'E2E', 'Tester_mndfuth9', NULL, 0, 1, '2026-03-30 17:05:02', '2026-03-30 17:05:02'),
(15, 1, 'e2euser_mndfv71p', 'e2e_mndfv71p@test.com', '$argon2id$v=19$m=65536,t=4,p=1$bWlXR1R4aVYvaTNwN1RWdQ$YpvPA5rEuytSmjgOJXETUlN9gr3K8uHvFPcOmpXoHZs', NULL, 'E2E', 'Tester_mndfv71p', NULL, 0, 1, '2026-03-30 17:05:16', '2026-03-30 17:05:16'),
(16, 1, 'e2euser_mndfvgrz', 'e2e_mndfvgrz@test.com', '$argon2id$v=19$m=65536,t=4,p=1$VEFrLnFmd1RuYXVCb2RsSA$diZlwNeVvWltTLmxIOi9+VohvyOxcvWOW1elZv+QSZg', NULL, 'E2E', 'Tester_mndfvgrz', NULL, 0, 1, '2026-03-30 17:05:28', '2026-03-30 17:05:28'),
(17, 1, 'e2euser_mndfvpvg', 'e2e_mndfvpvg@test.com', '$argon2id$v=19$m=65536,t=4,p=1$bVMySVFZUTRjaGxISVZPcQ$dKytnLkCC7o/3wPTIlbqBshOHmbLFqv9HTWnFwt9FF8', NULL, 'E2E', 'Tester_mndfvpvg', NULL, 0, 1, '2026-03-30 17:05:40', '2026-03-30 17:05:40'),
(18, 1, 'e2euser_mndfvyyy', 'e2e_mndfvyyy@test.com', '$argon2id$v=19$m=65536,t=4,p=1$RFJvUkIxSW93dUI5MEhqYg$DR90gFFm+uI0XjXFsCvLdCqgNyz56U8KoQ1pH1et1S8', NULL, 'E2E', 'Tester_mndfvyyy', NULL, 0, 1, '2026-03-30 17:05:52', '2026-03-30 17:05:52'),
(19, 1, 'e2euser_mndfw8oo', 'e2e_mndfw8oo@test.com', '$argon2id$v=19$m=65536,t=4,p=1$ZW56ZUg1dDQ4dGgwTUdGQw$BfBi2SeM2yLyn8bx1LXFgY+tTEdBkwiIRG3+/2sMu+4', NULL, 'E2E', 'Tester_mndfw8oo', NULL, 0, 1, '2026-03-30 17:06:05', '2026-03-30 17:06:05'),
(20, 1, 'e2euser_mndfwivg', 'e2e_mndfwivg@test.com', '$argon2id$v=19$m=65536,t=4,p=1$SGhNSzcvcTRTY3J6MXcvLg$tBlCqms/J2RGSSvvaf4Pv5J1CTYEkLmpEKHqpgzpFNE', NULL, 'E2E', 'Tester_mndfwivg', NULL, 0, 1, '2026-03-30 17:06:18', '2026-03-30 17:06:18'),
(21, 1, 'e2euser_mndfwt0d', 'e2e_mndfwt0d@test.com', '$argon2id$v=19$m=65536,t=4,p=1$Rjdnb3FSL0J5WlFGc1c0UA$sGYZe7illiOAK4iJymZ89hTuBNXeLQxL1H1kdcbxyIM', NULL, 'E2E', 'Tester_mndfwt0d', NULL, 0, 1, '2026-03-30 17:06:32', '2026-03-30 17:06:32'),
(22, 1, 'e2euser_mndg6ml1', 'e2e_mndg6ml1@test.com', '$argon2id$v=19$m=65536,t=4,p=1$WUIwRzNwb3d4ZGJDVVd5cw$OJqs/0urEWEw7TYdjUPnr/Sd8CYa257ogRsuBCH9RqY', NULL, 'E2E', 'Tester_mndg6ml1', NULL, 0, 1, '2026-03-30 17:14:08', '2026-03-30 17:15:06'),
(23, 3, 'admin_e2euser_mndg6r2p', 'admin_e2e_mndg6r2p@test.com', '$argon2id$v=19$m=65536,t=4,p=1$MVNtN1JZd0RySFVaUHBiZA$79ZbG6u8yxvaAJprKE/WzZgKl6sS1Bslc3OEq29YXss', NULL, 'Admin', 'Tester_mndg6r2p', NULL, 0, 1, '2026-03-30 17:14:15', '2026-03-30 17:15:10'),
(24, 1, 'e2euser_mndg84zo', 'e2e_mndg84zo@test.com', '$argon2id$v=19$m=65536,t=4,p=1$UWdGa3ouNEpKampuQW94Yw$P0cNd7PM+QGrbnhFGDV6P/sy2jv73ABAxFn3SrPlfRk', NULL, 'E2E', 'Tester_mndg84zo', NULL, 0, 1, '2026-03-30 17:15:19', '2026-03-30 17:15:19'),
(25, 1, 'e2euser_mndhk4a0', 'e2e_mndhk4a0@test.com', '$argon2id$v=19$m=65536,t=4,p=1$UXlpYTdRTjQ5bWNONi9DdQ$mqHT15wOL6RGvUz/2JUVeQTjjr5mz/v0Olwm+JwzsPs', NULL, 'E2E', 'Tester_mndhk4a0', NULL, 0, 1, '2026-03-30 17:52:50', '2026-03-30 17:52:50'),
(26, 1, 'e2euser_mndhkiqr', 'e2e_mndhkiqr@test.com', '$argon2id$v=19$m=65536,t=4,p=1$SzJaUENoVTFnRWcwMXUzSw$PwBIonxp2NzM81poe4aIUMwIRY0jmIpB/XgK5HpSjfg', NULL, 'E2E', 'Tester_mndhkiqr', NULL, 0, 1, '2026-03-30 17:52:57', '2026-03-30 17:52:57'),
(27, 1, 'e2euser_mndhkta8', 'e2e_mndhkta8@test.com', '$argon2id$v=19$m=65536,t=4,p=1$L0FlbFRBcmQyRkpKZXhtQw$h5QMZe2+to084zmQ5QDB2xf4fsivJW+gRc1zSZuh9CA', NULL, 'E2E', 'Tester_mndhkta8', NULL, 0, 1, '2026-03-30 17:53:11', '2026-03-30 17:53:11'),
(28, 1, 'e2euser_mndhkyfl', 'e2e_mndhkyfl@test.com', '$argon2id$v=19$m=65536,t=4,p=1$MzhvVDdIUWZ4bERDNW1nQQ$W5WDqWMNO6QWfFIp61HjYeZVbhak1A13o9G0caCWJzs', NULL, 'E2E', 'Tester_mndhkyfl', NULL, 0, 1, '2026-03-30 17:53:18', '2026-03-30 17:53:18'),
(29, 1, 'e2euser_mndhl4xq', 'e2e_mndhl4xq@test.com', '$argon2id$v=19$m=65536,t=4,p=1$L2dCcmdSMVYyQjg5VHZWYQ$kF39ZlI0Am3LSI1LR6tIjSeLAbK4OVtgsmZPzf1lx8c', NULL, 'E2E', 'Tester_mndhl4xq', NULL, 0, 1, '2026-03-30 17:53:26', '2026-03-30 17:53:26'),
(30, 1, 'e2euser_mndhlc30', 'e2e_mndhlc30@test.com', '$argon2id$v=19$m=65536,t=4,p=1$SnZTeHJsMGxnM0xhb2lRRg$yxTzbFLEdf+8/82Y0QlCKnpgZ7IFqFDUyAB9LvLuzr4', NULL, 'E2E', 'Tester_mndhlc30', NULL, 0, 1, '2026-03-30 17:53:35', '2026-03-30 17:53:35'),
(31, 1, 'e2euser_mndhljmy', 'e2e_mndhljmy@test.com', '$argon2id$v=19$m=65536,t=4,p=1$bDJ0YTAyVHM3dEkzakY4bg$0+GZFUT5jeYNtU06c+3+7X0oQ9tip76qdCh2ZO9odOw', NULL, 'E2E', 'Tester_mndhljmy', NULL, 0, 1, '2026-03-30 17:53:45', '2026-03-30 17:53:45'),
(32, 1, 'e2euser_mndhlql4', 'e2e_mndhlql4@test.com', '$argon2id$v=19$m=65536,t=4,p=1$dm9POUFxZ0lhMlNaN25tQQ$6zcvzsoLiks5bsjZtKHTbDKmBeXHYGaetPMyGeLqQMc', NULL, 'E2E', 'Tester_mndhlql4', NULL, 0, 1, '2026-03-30 17:53:55', '2026-03-30 17:53:55'),
(33, 1, 'e2euser_mndhm0et', 'e2e_mndhm0et@test.com', '$argon2id$v=19$m=65536,t=4,p=1$OXdmaUdldzFtRkN4N09hVw$h2lFnnDBnTbnsWX6gYlb2tGejNsIw0vYjc3ZUbr/XSU', NULL, 'E2E', 'Tester_mndhm0et', NULL, 0, 1, '2026-03-30 17:54:10', '2026-03-30 17:54:10'),
(34, 1, 'e2euser_mndhmhxh', 'e2e_mndhmhxh@test.com', '$argon2id$v=19$m=65536,t=4,p=1$eHRSNXVCaEluaEdRdDZ0aQ$ynFdVO1mdCHu+TPChYdjTU/5jhFrn+eqJY5GZtJie20', NULL, 'E2E', 'Tester_mndhmhxh', NULL, 0, 1, '2026-03-30 17:54:30', '2026-03-30 17:54:30'),
(35, 1, 'e2euser_mndhmqz4', 'e2e_mndhmqz4@test.com', '$argon2id$v=19$m=65536,t=4,p=1$YnZXYkxIR1d0Tm8xQTdnaQ$Y9RoWmuIub1Fx8dLExE6ikIIcAaoEdudzyJ97bg3f2w', NULL, 'E2E', 'Tester_mndhmqz4', NULL, 0, 1, '2026-03-30 17:54:41', '2026-03-30 17:54:41'),
(36, 1, 'e2euser_mndhmyi0', 'e2e_mndhmyi0@test.com', '$argon2id$v=19$m=65536,t=4,p=1$Si96UWFHRGI4MUpsdFUuYQ$LfFLf2jPZH3zGW3Y5Qi7KYp+26ZAnJt0/A2n9AD3lPk', NULL, 'E2E', 'Tester_mndhmyi0', NULL, 0, 1, '2026-03-30 17:54:51', '2026-03-30 17:54:51'),
(37, 1, 'e2euser_mndhn5i7', 'e2e_mndhn5i7@test.com', '$argon2id$v=19$m=65536,t=4,p=1$MDRWWlk1Sms5YTlFQlI4eA$USrS9QPv++3jm1ej7cAs0j+IUkPNtO6p3n1QxhsdDhY', NULL, 'E2E', 'Tester_mndhn5i7', NULL, 0, 1, '2026-03-30 17:55:00', '2026-03-30 17:55:00'),
(38, 1, 'e2euser_mndhnchh', 'e2e_mndhnchh@test.com', '$argon2id$v=19$m=65536,t=4,p=1$VmhKTnFXVGFITHJSd2Zpdw$IR8H8BH5LsHQ3MrbzjO3MGH8jeoTMIRa7vlD3han6/g', NULL, 'E2E', 'Tester_mndhnchh', NULL, 0, 1, '2026-03-30 17:55:09', '2026-03-30 17:55:09'),
(39, 1, 'e2euser_mndhnl5a', 'e2e_mndhnl5a@test.com', '$argon2id$v=19$m=65536,t=4,p=1$Vm5RYnBic2RjeU5BV0RKRA$L1Rp54qoaO7wrLfeVl/slujI5VsCVNBMxfSGZwSxGac', NULL, 'E2E', 'Tester_mndhnl5a', NULL, 0, 1, '2026-03-30 17:55:20', '2026-03-30 17:55:20'),
(40, 1, 'e2euser_mndhxiqf', 'e2e_mndhxiqf@test.com', '$argon2id$v=19$m=65536,t=4,p=1$RlF2cUtjeGUxOTNEOEhKSA$GK3e6ZmkgRK5R/rbW51JGPrEx9oVq1lByAaOTKy4NAU', NULL, 'E2E', 'Tester_mndhxiqf', NULL, 0, 1, '2026-03-30 18:03:02', '2026-03-30 18:03:41'),
(41, 3, 'admin_e2euser_mndhxnms', 'admin_e2e_mndhxnms@test.com', '$argon2id$v=19$m=65536,t=4,p=1$Ny50N1B5RFNWNEs2eE9YZg$bKLEEYmkrR/urikTIhAsjOZCrfNgdjYgQprdnB4ckbg', NULL, 'Admin', 'Tester_mndhxnms', NULL, 0, 1, '2026-03-30 18:03:08', '2026-03-30 18:03:45'),
(42, 1, 'e2euser_mndhyni2', 'e2e_mndhyni2@test.com', '$argon2id$v=19$m=65536,t=4,p=1$SHE1RnByck9jQnJYR2d0Qw$m3pbynYm7AFTmA+Fe97+SB8crIQlSN8vhlMiu/5CZwU', NULL, 'E2E', 'Tester_mndhyni2', NULL, 0, 1, '2026-03-30 18:03:57', '2026-03-30 18:03:57'),
(43, 1, 'e2euser_mndi5mfs', 'e2e_mndi5mfs@test.com', '$argon2id$v=19$m=65536,t=4,p=1$Z2lrWWIuYUpQYmxvLlNsUQ$fe2g4KBhUd+DyghBabQx4/Cz+0WMgo4k6xFgrLPrq44', NULL, 'E2E', 'Tester_mndi5mfs', NULL, 0, 1, '2026-03-30 18:09:22', '2026-03-30 18:09:22'),
(44, 1, 'e2euser_mndickuw', 'e2e_mndickuw@test.com', '$argon2id$v=19$m=65536,t=4,p=1$dW5vUi5wMTNqRW03WUFndQ$7t04TY/MKDMd6aP+LfK0BrlDfFVjKhUQT0xo/eJeFsA', NULL, 'E2E', 'Tester_mndickuw', NULL, 0, 1, '2026-03-30 18:14:47', '2026-03-30 18:14:47'),
(45, 2, 'employee1', 'employee1@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$SEFnWHBGZTRISFI4eGJpMQ$YK2ZFjrjEwM9Pl+XpNfnkvBxVZH1QjaJtOqsu6Afb7Y', NULL, 'Employee 1', 'Employee 1', NULL, 0, 1, '2026-04-02 14:52:59', '2026-04-02 14:52:59');

-- --------------------------------------------------------

--
-- Table structure for table `UserRole`
--

CREATE TABLE `UserRole` (
  `UserRoleId` int(11) NOT NULL,
  `RoleName` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `UserRole`
--

INSERT INTO `UserRole` (`UserRoleId`, `RoleName`) VALUES
(3, 'Administrator'),
(1, 'Customer'),
(2, 'Employee');

-- --------------------------------------------------------

--
-- Table structure for table `Venue`
--

CREATE TABLE `Venue` (
  `VenueId` int(11) NOT NULL,
  `Name` varchar(120) NOT NULL,
  `AddressLine` varchar(200) NOT NULL DEFAULT '',
  `City` varchar(80) NOT NULL DEFAULT 'Haarlem',
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp(),
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `Venue`
--

INSERT INTO `Venue` (`VenueId`, `Name`, `AddressLine`, `City`, `CreatedAtUtc`, `IsActive`) VALUES
(1, 'Patronaat', 'Zijlsingel 2, 2013 DN Haarlem', 'Haarlem', '2026-02-06 15:30:32', 1),
(2, 'Grote Markt', 'Grote Markt', 'Haarlem', '2026-02-06 15:30:32', 1),
(3, 'Lichtfabriek', 'Minckelersweg 2', 'Haarlem', '2026-02-06 15:30:32', 1),
(4, 'Slachthuis', 'Rockplein 6', 'Haarlem', '2026-02-06 15:30:32', 1),
(5, 'Jopenkerk', 'Gedempte Voldersgracht 2', 'Haarlem', '2026-02-06 15:30:32', 1),
(6, 'XO the Club', 'Grote Markt 8', 'Haarlem', '2026-02-06 15:30:32', 1),
(7, 'Puncher Comedy Club', 'Grote Markt 10', 'Haarlem', '2026-02-06 15:30:32', 1),
(8, 'Caprera Openluchttheater', 'Hoge Duin en Daalseweg 2', 'Bloemendaal', '2026-02-06 15:30:32', 1),
(9, 'Bavo Church', 'Grote Markt 22', 'Haarlem', '2026-02-06 15:30:32', 1),
(10, 'Verhalenhuis Haarlem', 'van Egmondstraat 7', 'Haarlem', '2026-02-06 15:30:32', 1),
(11, 'De Schuur', 'Lange Begijnestraat 9, 2011 HH Haarlem', 'Haarlem', '2026-02-06 15:30:32', 1),
(12, 'Kweekcafé', 'Kleverlaan 9, 2023 JC Haarlem', 'Haarlem', '2026-02-06 15:30:32', 1),
(13, 'Corrie ten Boom Huis', 'Barteljorisstraat 19', 'Haarlem', '2026-02-06 15:30:32', 1),
(14, 'Theater Elswout', 'Elswoutslaan 24-a, 2051 AE Overveen', 'Overveen', '2026-02-06 15:30:32', 1),
(15, 'A giant flag near Church of St. Bavo at Grote Markt', '', 'Haarlem', '2026-03-18 11:24:34', 1),
(16, 'E2E Test Venue', '123 Test Street, Haarlem', 'Haarlem', '2026-03-30 16:44:05', 0),
(17, 'E2E Test Venue', '123 Test Street, Haarlem', 'Haarlem', '2026-03-30 17:09:01', 0),
(18, 'E2E Test Venue', '123 Test Street, Haarlem', 'Haarlem', '2026-03-30 17:58:10', 0),
(19, 'Test Venue Delete', 'Test Street', 'Haarlem', '2026-04-05 21:38:49', 1),
(20, 'Test Venue Delete', 'Test Street', 'Haarlem', '2026-04-05 21:38:54', 0),
(21, 'Test Delete Me', 'Nowhere', 'Haarlem', '2026-04-05 21:39:38', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Artist`
--
ALTER TABLE `Artist`
  ADD PRIMARY KEY (`ArtistId`),
  ADD KEY `FK_Artist_Image` (`ImageAssetId`),
  ADD KEY `IX_Artist_JazzOverview` (`ShowOnJazzOverview`,`CardSortOrder`,`Name`);

--
-- Indexes for table `ArtistAlbum`
--
ALTER TABLE `ArtistAlbum`
  ADD PRIMARY KEY (`ArtistAlbumId`),
  ADD KEY `IX_ArtistAlbum_Artist` (`ArtistId`,`SortOrder`);

--
-- Indexes for table `ArtistGalleryImage`
--
ALTER TABLE `ArtistGalleryImage`
  ADD PRIMARY KEY (`ArtistGalleryImageId`),
  ADD KEY `IX_ArtistGalleryImage_Artist` (`ArtistId`,`SortOrder`);

--
-- Indexes for table `ArtistHighlight`
--
ALTER TABLE `ArtistHighlight`
  ADD PRIMARY KEY (`ArtistHighlightId`),
  ADD KEY `IX_ArtistHighlight_Artist` (`ArtistId`,`SortOrder`);

--
-- Indexes for table `ArtistLineupMember`
--
ALTER TABLE `ArtistLineupMember`
  ADD PRIMARY KEY (`ArtistLineupMemberId`),
  ADD KEY `IX_ArtistLineupMember_Artist` (`ArtistId`,`SortOrder`);

--
-- Indexes for table `ArtistTrack`
--
ALTER TABLE `ArtistTrack`
  ADD PRIMARY KEY (`ArtistTrackId`),
  ADD KEY `IX_ArtistTrack_Artist` (`ArtistId`,`SortOrder`);

--
-- Indexes for table `CmsItem`
--
ALTER TABLE `CmsItem`
  ADD PRIMARY KEY (`CmsItemId`),
  ADD UNIQUE KEY `UQ_CmsItem` (`CmsSectionId`,`ItemKey`),
  ADD KEY `FK_CmsItem_Asset` (`MediaAssetId`),
  ADD KEY `IX_CmsItem_Section` (`CmsSectionId`),
  ADD KEY `FK_CmsItem_ItemType` (`ItemType`),
  ADD KEY `FK_CmsItem_ItemKey` (`ItemKey`);

--
-- Indexes for table `CmsItemKey`
--
ALTER TABLE `CmsItemKey`
  ADD PRIMARY KEY (`ItemKey`),
  ADD KEY `FK_CmsItemKey_Type` (`ExpectedItemType`);

--
-- Indexes for table `CmsItemType`
--
ALTER TABLE `CmsItemType`
  ADD PRIMARY KEY (`ItemType`);

--
-- Indexes for table `CmsPage`
--
ALTER TABLE `CmsPage`
  ADD PRIMARY KEY (`CmsPageId`),
  ADD UNIQUE KEY `Slug` (`Slug`);

--
-- Indexes for table `CmsSection`
--
ALTER TABLE `CmsSection`
  ADD PRIMARY KEY (`CmsSectionId`),
  ADD UNIQUE KEY `UQ_CmsSection` (`CmsPageId`,`SectionKey`),
  ADD KEY `IX_CmsSection_Page` (`CmsPageId`);

--
-- Indexes for table `CuisineType`
--
ALTER TABLE `CuisineType`
  ADD PRIMARY KEY (`CuisineTypeId`);

--
-- Indexes for table `EmailConfirmationToken`
--
ALTER TABLE `EmailConfirmationToken`
  ADD PRIMARY KEY (`EmailConfirmationTokenId`),
  ADD UNIQUE KEY `Token` (`Token`),
  ADD KEY `FK_EmailConfirmationToken_User` (`UserAccountId`);

--
-- Indexes for table `Event`
--
ALTER TABLE `Event`
  ADD PRIMARY KEY (`EventId`),
  ADD UNIQUE KEY `UNQ_Event_Slug` (`Slug`),
  ADD KEY `FK_Event_Image` (`FeaturedImageAssetId`),
  ADD KEY `FK_Event_Venue` (`VenueId`),
  ADD KEY `FK_Event_Artist` (`ArtistId`),
  ADD KEY `IX_Event_Type` (`EventTypeId`,`IsActive`),
  ADD KEY `IX_Event_Active` (`IsActive`,`EventId`);

--
-- Indexes for table `EventGalleryImage`
--
ALTER TABLE `EventGalleryImage`
  ADD PRIMARY KEY (`EventGalleryImageId`),
  ADD KEY `IX_EventGalleryImage_Event` (`EventId`,`ImageType`,`SortOrder`);

--
-- Indexes for table `EventHighlight`
--
ALTER TABLE `EventHighlight`
  ADD PRIMARY KEY (`EventHighlightId`),
  ADD KEY `IX_EventHighlight_Event` (`EventId`,`SortOrder`);

--
-- Indexes for table `EventSession`
--
ALTER TABLE `EventSession`
  ADD PRIMARY KEY (`EventSessionId`),
  ADD KEY `IX_Session_EventTime` (`EventId`,`StartDateTime`),
  ADD KEY `IX_EventSession_Age` (`MinAge`,`MaxAge`),
  ADD KEY `IX_EventSession_Capacity` (`EventId`,`StartDateTime`,`CapacityTotal`,`SoldSingleTickets`,`SoldReservedSeats`),
  ADD KEY `IX_EventSession_DateTime` (`StartDateTime`,`IsCancelled`),
  ADD KEY `IX_EventSession_Active` (`IsActive`,`EventId`),
  ADD KEY `FK_EventSession_SessionType` (`SessionType`),
  ADD KEY `IX_EventSession_Venue` (`VenueId`);

--
-- Indexes for table `EventSessionLabel`
--
ALTER TABLE `EventSessionLabel`
  ADD PRIMARY KEY (`EventSessionLabelId`),
  ADD KEY `IX_SessionLabel_Session` (`EventSessionId`);

--
-- Indexes for table `EventSessionPrice`
--
ALTER TABLE `EventSessionPrice`
  ADD PRIMARY KEY (`EventSessionPriceId`),
  ADD UNIQUE KEY `UQ_EventSessionPrice` (`EventSessionId`,`PriceTierId`),
  ADD KEY `FK_EventSessionPrice_Tier` (`PriceTierId`),
  ADD KEY `IX_EventSessionPrice_Session` (`EventSessionId`);

--
-- Indexes for table `EventType`
--
ALTER TABLE `EventType`
  ADD PRIMARY KEY (`EventTypeId`),
  ADD UNIQUE KEY `Name` (`Name`),
  ADD UNIQUE KEY `Slug` (`Slug`);

--
-- Indexes for table `Guide`
--
ALTER TABLE `Guide`
  ADD PRIMARY KEY (`GuideId`),
  ADD UNIQUE KEY `UQ_Guide_Name` (`Name`);

--
-- Indexes for table `HistoryTour`
--
ALTER TABLE `HistoryTour`
  ADD PRIMARY KEY (`HistoryTourId`),
  ADD UNIQUE KEY `UQ_HistoryTour` (`EventSessionId`,`LanguageCode`),
  ADD KEY `IX_HistoryTour_Session` (`EventSessionId`);

--
-- Indexes for table `HistoryTourGuide`
--
ALTER TABLE `HistoryTourGuide`
  ADD PRIMARY KEY (`HistoryTourId`,`GuideId`),
  ADD KEY `IX_HistoryTourGuide_Guide` (`GuideId`);

--
-- Indexes for table `Invoice`
--
ALTER TABLE `Invoice`
  ADD PRIMARY KEY (`InvoiceId`),
  ADD UNIQUE KEY `InvoiceNumber` (`InvoiceNumber`),
  ADD UNIQUE KEY `UQ_Invoice_Order` (`OrderId`),
  ADD KEY `FK_Invoice_Pdf` (`PdfAssetId`);

--
-- Indexes for table `InvoiceLine`
--
ALTER TABLE `InvoiceLine`
  ADD PRIMARY KEY (`InvoiceLineId`),
  ADD UNIQUE KEY `UQ_InvoiceLine` (`InvoiceId`,`LineDescription`),
  ADD KEY `IX_InvoiceLine_Invoice` (`InvoiceId`);

--
-- Indexes for table `MediaAsset`
--
ALTER TABLE `MediaAsset`
  ADD PRIMARY KEY (`MediaAssetId`);

--
-- Indexes for table `Order`
--
ALTER TABLE `Order`
  ADD PRIMARY KEY (`OrderId`),
  ADD UNIQUE KEY `OrderNumber` (`OrderNumber`),
  ADD KEY `FK_Order_Program` (`ProgramId`),
  ADD KEY `IX_Order_User` (`UserAccountId`,`CreatedAtUtc`),
  ADD KEY `IX_Order_Status` (`Status`,`PayBeforeUtc`),
  ADD KEY `IX_Order_UserDesc` (`UserAccountId`,`CreatedAtUtc` DESC);

--
-- Indexes for table `OrderItem`
--
ALTER TABLE `OrderItem`
  ADD PRIMARY KEY (`OrderItemId`),
  ADD KEY `FK_OrderItem_PassPurchase` (`PassPurchaseId`),
  ADD KEY `IX_OrderItem_Order` (`OrderId`),
  ADD KEY `IX_OrderItem_Session` (`EventSessionId`),
  ADD KEY `IX_OrderItem_HistoryTour` (`HistoryTourId`);

--
-- Indexes for table `OrderStatus`
--
ALTER TABLE `OrderStatus`
  ADD PRIMARY KEY (`Status`);

--
-- Indexes for table `PageGalleryImage`
--
ALTER TABLE `PageGalleryImage`
  ADD PRIMARY KEY (`PageGalleryImageId`),
  ADD KEY `IX_PageGalleryImage_Page` (`CmsPageId`,`ImageType`,`SortOrder`);

--
-- Indexes for table `PassPurchase`
--
ALTER TABLE `PassPurchase`
  ADD PRIMARY KEY (`PassPurchaseId`),
  ADD KEY `FK_PassPurchase_PassType` (`PassTypeId`),
  ADD KEY `FK_PassPurchase_User` (`UserAccountId`);

--
-- Indexes for table `PassScope`
--
ALTER TABLE `PassScope`
  ADD PRIMARY KEY (`PassScope`);

--
-- Indexes for table `PassType`
--
ALTER TABLE `PassType`
  ADD PRIMARY KEY (`PassTypeId`),
  ADD UNIQUE KEY `UQ_PassType` (`EventTypeId`,`PassName`),
  ADD KEY `FK_PassType_Scope` (`PassScope`);

--
-- Indexes for table `PasswordResetToken`
--
ALTER TABLE `PasswordResetToken`
  ADD PRIMARY KEY (`PasswordResetTokenId`),
  ADD UNIQUE KEY `Token` (`Token`),
  ADD KEY `FK_PasswordResetToken_User` (`UserAccountId`);

--
-- Indexes for table `Payment`
--
ALTER TABLE `Payment`
  ADD PRIMARY KEY (`PaymentId`),
  ADD KEY `IX_Payment_Order` (`OrderId`),
  ADD KEY `IX_Payment_StripeCheckoutSessionId` (`StripeCheckoutSessionId`),
  ADD KEY `IX_Payment_StripePaymentIntentId` (`StripePaymentIntentId`),
  ADD KEY `FK_Payment_Status` (`Status`),
  ADD KEY `FK_Payment_Method` (`Method`);

--
-- Indexes for table `PaymentMethod`
--
ALTER TABLE `PaymentMethod`
  ADD PRIMARY KEY (`Method`);

--
-- Indexes for table `PaymentStatus`
--
ALTER TABLE `PaymentStatus`
  ADD PRIMARY KEY (`Status`);

--
-- Indexes for table `PriceTier`
--
ALTER TABLE `PriceTier`
  ADD PRIMARY KEY (`PriceTierId`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- Indexes for table `Program`
--
ALTER TABLE `Program`
  ADD PRIMARY KEY (`ProgramId`),
  ADD KEY `IX_Program_User` (`UserAccountId`,`IsCheckedOut`),
  ADD KEY `IX_Program_SessionKey` (`SessionKey`,`IsCheckedOut`);

--
-- Indexes for table `ProgramItem`
--
ALTER TABLE `ProgramItem`
  ADD PRIMARY KEY (`ProgramItemId`),
  ADD KEY `FK_ProgramItem_PassType` (`PassTypeId`),
  ADD KEY `IX_ProgramItem_Program` (`ProgramId`),
  ADD KEY `IX_ProgramItem_Session` (`EventSessionId`),
  ADD KEY `IX_ProgramItem_HistoryTour` (`HistoryTourId`),
  ADD KEY `FK_ProgramItem_Reservation` (`ReservationId`),
  ADD KEY `FK_ProgramItem_PriceTier` (`PriceTierId`);

--
-- Indexes for table `Reservation`
--
ALTER TABLE `Reservation`
  ADD PRIMARY KEY (`ReservationId`),
  ADD KEY `FK_Reservation_Event` (`EventId`);

--
-- Indexes for table `ScheduleDay`
--
ALTER TABLE `ScheduleDay`
  ADD PRIMARY KEY (`ScheduleDayId`),
  ADD UNIQUE KEY `UQ_ScheduleDay` (`EventTypeId`,`Date`),
  ADD KEY `IX_ScheduleDay_Filter` (`EventTypeId`,`Date`,`IsDeleted`);

--
-- Indexes for table `ScheduleDayConfig`
--
ALTER TABLE `ScheduleDayConfig`
  ADD PRIMARY KEY (`ScheduleDayConfigId`),
  ADD UNIQUE KEY `uk_eventtype_day` (`EventTypeId`,`DayOfWeek`);

--
-- Indexes for table `SessionDiscountRule`
--
ALTER TABLE `SessionDiscountRule`
  ADD PRIMARY KEY (`SessionDiscountRuleId`),
  ADD KEY `FK_DiscountRule_Tier` (`AppliesToPriceTierId`),
  ADD KEY `IX_DiscountRule_Session` (`EventSessionId`);

--
-- Indexes for table `SessionType`
--
ALTER TABLE `SessionType`
  ADD PRIMARY KEY (`SessionType`);

--
-- Indexes for table `StarRating`
--
ALTER TABLE `StarRating`
  ADD PRIMARY KEY (`Stars`);

--
-- Indexes for table `StripeWebhookEvent`
--
ALTER TABLE `StripeWebhookEvent`
  ADD PRIMARY KEY (`StripeWebhookEventId`),
  ADD UNIQUE KEY `UQ_StripeWebhookEvent_StripeEventId` (`StripeEventId`);

--
-- Indexes for table `Ticket`
--
ALTER TABLE `Ticket`
  ADD PRIMARY KEY (`TicketId`),
  ADD UNIQUE KEY `TicketCode` (`TicketCode`),
  ADD KEY `FK_Ticket_OrderItem` (`OrderItemId`),
  ADD KEY `FK_Ticket_ScannedBy` (`ScannedByUserId`),
  ADD KEY `FK_Ticket_Pdf` (`PdfAssetId`),
  ADD KEY `IX_Ticket_Scan` (`IsScanned`,`TicketCode`);

--
-- Indexes for table `UserAccount`
--
ALTER TABLE `UserAccount`
  ADD PRIMARY KEY (`UserAccountId`),
  ADD UNIQUE KEY `UQ_UserAccount_Username` (`Username`),
  ADD UNIQUE KEY `UQ_UserAccount_Email` (`Email`),
  ADD KEY `FK_UserAccount_ProfilePic` (`ProfilePictureAssetId`),
  ADD KEY `IX_UserAccount_Role` (`UserRoleId`,`IsActive`);

--
-- Indexes for table `UserRole`
--
ALTER TABLE `UserRole`
  ADD PRIMARY KEY (`UserRoleId`),
  ADD UNIQUE KEY `RoleName` (`RoleName`);

--
-- Indexes for table `Venue`
--
ALTER TABLE `Venue`
  ADD PRIMARY KEY (`VenueId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Artist`
--
ALTER TABLE `Artist`
  MODIFY `ArtistId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `ArtistAlbum`
--
ALTER TABLE `ArtistAlbum`
  MODIFY `ArtistAlbumId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ArtistGalleryImage`
--
ALTER TABLE `ArtistGalleryImage`
  MODIFY `ArtistGalleryImageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ArtistHighlight`
--
ALTER TABLE `ArtistHighlight`
  MODIFY `ArtistHighlightId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ArtistLineupMember`
--
ALTER TABLE `ArtistLineupMember`
  MODIFY `ArtistLineupMemberId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ArtistTrack`
--
ALTER TABLE `ArtistTrack`
  MODIFY `ArtistTrackId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `CmsItem`
--
ALTER TABLE `CmsItem`
  MODIFY `CmsItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1494;

--
-- AUTO_INCREMENT for table `CmsPage`
--
ALTER TABLE `CmsPage`
  MODIFY `CmsPageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `CmsSection`
--
ALTER TABLE `CmsSection`
  MODIFY `CmsSectionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `CuisineType`
--
ALTER TABLE `CuisineType`
  MODIFY `CuisineTypeId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `EmailConfirmationToken`
--
ALTER TABLE `EmailConfirmationToken`
  MODIFY `EmailConfirmationTokenId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Event`
--
ALTER TABLE `Event`
  MODIFY `EventId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `EventGalleryImage`
--
ALTER TABLE `EventGalleryImage`
  MODIFY `EventGalleryImageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `EventHighlight`
--
ALTER TABLE `EventHighlight`
  MODIFY `EventHighlightId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `EventSession`
--
ALTER TABLE `EventSession`
  MODIFY `EventSessionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;

--
-- AUTO_INCREMENT for table `EventSessionLabel`
--
ALTER TABLE `EventSessionLabel`
  MODIFY `EventSessionLabelId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `EventSessionPrice`
--
ALTER TABLE `EventSessionPrice`
  MODIFY `EventSessionPriceId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=466;

--
-- AUTO_INCREMENT for table `EventType`
--
ALTER TABLE `EventType`
  MODIFY `EventTypeId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Guide`
--
ALTER TABLE `Guide`
  MODIFY `GuideId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `HistoryTour`
--
ALTER TABLE `HistoryTour`
  MODIFY `HistoryTourId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `Invoice`
--
ALTER TABLE `Invoice`
  MODIFY `InvoiceId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `InvoiceLine`
--
ALTER TABLE `InvoiceLine`
  MODIFY `InvoiceLineId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `MediaAsset`
--
ALTER TABLE `MediaAsset`
  MODIFY `MediaAssetId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `Order`
--
ALTER TABLE `Order`
  MODIFY `OrderId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `OrderItem`
--
ALTER TABLE `OrderItem`
  MODIFY `OrderItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `PageGalleryImage`
--
ALTER TABLE `PageGalleryImage`
  MODIFY `PageGalleryImageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `PassPurchase`
--
ALTER TABLE `PassPurchase`
  MODIFY `PassPurchaseId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `PassType`
--
ALTER TABLE `PassType`
  MODIFY `PassTypeId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `PasswordResetToken`
--
ALTER TABLE `PasswordResetToken`
  MODIFY `PasswordResetTokenId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `Payment`
--
ALTER TABLE `Payment`
  MODIFY `PaymentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `PriceTier`
--
ALTER TABLE `PriceTier`
  MODIFY `PriceTierId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Program`
--
ALTER TABLE `Program`
  MODIFY `ProgramId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `ProgramItem`
--
ALTER TABLE `ProgramItem`
  MODIFY `ProgramItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `Reservation`
--
ALTER TABLE `Reservation`
  MODIFY `ReservationId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ScheduleDay`
--
ALTER TABLE `ScheduleDay`
  MODIFY `ScheduleDayId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ScheduleDayConfig`
--
ALTER TABLE `ScheduleDayConfig`
  MODIFY `ScheduleDayConfigId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `SessionDiscountRule`
--
ALTER TABLE `SessionDiscountRule`
  MODIFY `SessionDiscountRuleId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `StripeWebhookEvent`
--
ALTER TABLE `StripeWebhookEvent`
  MODIFY `StripeWebhookEventId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Ticket`
--
ALTER TABLE `Ticket`
  MODIFY `TicketId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `UserAccount`
--
ALTER TABLE `UserAccount`
  MODIFY `UserAccountId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `UserRole`
--
ALTER TABLE `UserRole`
  MODIFY `UserRoleId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Venue`
--
ALTER TABLE `Venue`
  MODIFY `VenueId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Artist`
--
ALTER TABLE `Artist`
  ADD CONSTRAINT `FK_Artist_Image` FOREIGN KEY (`ImageAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`);

--
-- Constraints for table `ArtistAlbum`
--
ALTER TABLE `ArtistAlbum`
  ADD CONSTRAINT `FK_ArtistAlbum_Artist` FOREIGN KEY (`ArtistId`) REFERENCES `Artist` (`ArtistId`) ON DELETE CASCADE;

--
-- Constraints for table `ArtistGalleryImage`
--
ALTER TABLE `ArtistGalleryImage`
  ADD CONSTRAINT `FK_ArtistGalleryImage_Artist` FOREIGN KEY (`ArtistId`) REFERENCES `Artist` (`ArtistId`) ON DELETE CASCADE;

--
-- Constraints for table `ArtistHighlight`
--
ALTER TABLE `ArtistHighlight`
  ADD CONSTRAINT `FK_ArtistHighlight_Artist` FOREIGN KEY (`ArtistId`) REFERENCES `Artist` (`ArtistId`) ON DELETE CASCADE;

--
-- Constraints for table `ArtistLineupMember`
--
ALTER TABLE `ArtistLineupMember`
  ADD CONSTRAINT `FK_ArtistLineupMember_Artist` FOREIGN KEY (`ArtistId`) REFERENCES `Artist` (`ArtistId`) ON DELETE CASCADE;

--
-- Constraints for table `ArtistTrack`
--
ALTER TABLE `ArtistTrack`
  ADD CONSTRAINT `FK_ArtistTrack_Artist` FOREIGN KEY (`ArtistId`) REFERENCES `Artist` (`ArtistId`) ON DELETE CASCADE;

--
-- Constraints for table `CmsItem`
--
ALTER TABLE `CmsItem`
  ADD CONSTRAINT `FK_CmsItem_Asset` FOREIGN KEY (`MediaAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_CmsItem_ItemKey` FOREIGN KEY (`ItemKey`) REFERENCES `CmsItemKey` (`ItemKey`),
  ADD CONSTRAINT `FK_CmsItem_ItemType` FOREIGN KEY (`ItemType`) REFERENCES `CmsItemType` (`ItemType`),
  ADD CONSTRAINT `FK_CmsItem_Section` FOREIGN KEY (`CmsSectionId`) REFERENCES `CmsSection` (`CmsSectionId`) ON DELETE CASCADE;

--
-- Constraints for table `CmsItemKey`
--
ALTER TABLE `CmsItemKey`
  ADD CONSTRAINT `FK_CmsItemKey_Type` FOREIGN KEY (`ExpectedItemType`) REFERENCES `CmsItemType` (`ItemType`);

--
-- Constraints for table `CmsSection`
--
ALTER TABLE `CmsSection`
  ADD CONSTRAINT `FK_CmsSection_Page` FOREIGN KEY (`CmsPageId`) REFERENCES `CmsPage` (`CmsPageId`) ON DELETE CASCADE;

--
-- Constraints for table `EmailConfirmationToken`
--
ALTER TABLE `EmailConfirmationToken`
  ADD CONSTRAINT `FK_EmailConfirmationToken_User` FOREIGN KEY (`UserAccountId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `Event`
--
ALTER TABLE `Event`
  ADD CONSTRAINT `FK_Event_Artist` FOREIGN KEY (`ArtistId`) REFERENCES `Artist` (`ArtistId`),
  ADD CONSTRAINT `FK_Event_Image` FOREIGN KEY (`FeaturedImageAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_Event_Type` FOREIGN KEY (`EventTypeId`) REFERENCES `EventType` (`EventTypeId`),
  ADD CONSTRAINT `FK_Event_Venue` FOREIGN KEY (`VenueId`) REFERENCES `Venue` (`VenueId`);

--
-- Constraints for table `EventGalleryImage`
--
ALTER TABLE `EventGalleryImage`
  ADD CONSTRAINT `FK_EventGalleryImage_Event` FOREIGN KEY (`EventId`) REFERENCES `Event` (`EventId`) ON DELETE CASCADE;

--
-- Constraints for table `EventHighlight`
--
ALTER TABLE `EventHighlight`
  ADD CONSTRAINT `FK_EventHighlight_Event` FOREIGN KEY (`EventId`) REFERENCES `Event` (`EventId`) ON DELETE CASCADE;

--
-- Constraints for table `EventSession`
--
ALTER TABLE `EventSession`
  ADD CONSTRAINT `FK_EventSession_Event` FOREIGN KEY (`EventId`) REFERENCES `Event` (`EventId`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_EventSession_SessionType` FOREIGN KEY (`SessionType`) REFERENCES `SessionType` (`SessionType`),
  ADD CONSTRAINT `FK_EventSession_Venue` FOREIGN KEY (`VenueId`) REFERENCES `Venue` (`VenueId`);

--
-- Constraints for table `EventSessionLabel`
--
ALTER TABLE `EventSessionLabel`
  ADD CONSTRAINT `FK_EventSessionLabel_Session` FOREIGN KEY (`EventSessionId`) REFERENCES `EventSession` (`EventSessionId`) ON DELETE CASCADE;

--
-- Constraints for table `EventSessionPrice`
--
ALTER TABLE `EventSessionPrice`
  ADD CONSTRAINT `FK_EventSessionPrice_Session` FOREIGN KEY (`EventSessionId`) REFERENCES `EventSession` (`EventSessionId`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_EventSessionPrice_Tier` FOREIGN KEY (`PriceTierId`) REFERENCES `PriceTier` (`PriceTierId`);

--
-- Constraints for table `HistoryTour`
--
ALTER TABLE `HistoryTour`
  ADD CONSTRAINT `FK_HistoryTour_Session` FOREIGN KEY (`EventSessionId`) REFERENCES `EventSession` (`EventSessionId`) ON DELETE CASCADE;

--
-- Constraints for table `HistoryTourGuide`
--
ALTER TABLE `HistoryTourGuide`
  ADD CONSTRAINT `FK_HistoryTourGuide_Guide` FOREIGN KEY (`GuideId`) REFERENCES `Guide` (`GuideId`),
  ADD CONSTRAINT `FK_HistoryTourGuide_Tour` FOREIGN KEY (`HistoryTourId`) REFERENCES `HistoryTour` (`HistoryTourId`);

--
-- Constraints for table `Invoice`
--
ALTER TABLE `Invoice`
  ADD CONSTRAINT `FK_Invoice_Order` FOREIGN KEY (`OrderId`) REFERENCES `Order` (`OrderId`),
  ADD CONSTRAINT `FK_Invoice_Pdf` FOREIGN KEY (`PdfAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`);

--
-- Constraints for table `InvoiceLine`
--
ALTER TABLE `InvoiceLine`
  ADD CONSTRAINT `FK_InvoiceLine_Invoice` FOREIGN KEY (`InvoiceId`) REFERENCES `Invoice` (`InvoiceId`) ON DELETE CASCADE;

--
-- Constraints for table `Order`
--
ALTER TABLE `Order`
  ADD CONSTRAINT `FK_Order_Program` FOREIGN KEY (`ProgramId`) REFERENCES `Program` (`ProgramId`),
  ADD CONSTRAINT `FK_Order_Status` FOREIGN KEY (`Status`) REFERENCES `OrderStatus` (`Status`),
  ADD CONSTRAINT `FK_Order_User` FOREIGN KEY (`UserAccountId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `OrderItem`
--
ALTER TABLE `OrderItem`
  ADD CONSTRAINT `FK_OrderItem_HistoryTour` FOREIGN KEY (`HistoryTourId`) REFERENCES `HistoryTour` (`HistoryTourId`),
  ADD CONSTRAINT `FK_OrderItem_Order` FOREIGN KEY (`OrderId`) REFERENCES `Order` (`OrderId`),
  ADD CONSTRAINT `FK_OrderItem_PassPurchase` FOREIGN KEY (`PassPurchaseId`) REFERENCES `PassPurchase` (`PassPurchaseId`),
  ADD CONSTRAINT `FK_OrderItem_Session` FOREIGN KEY (`EventSessionId`) REFERENCES `EventSession` (`EventSessionId`);

--
-- Constraints for table `PageGalleryImage`
--
ALTER TABLE `PageGalleryImage`
  ADD CONSTRAINT `FK_PageGalleryImage_Page` FOREIGN KEY (`CmsPageId`) REFERENCES `CmsPage` (`CmsPageId`) ON DELETE CASCADE;

--
-- Constraints for table `PassPurchase`
--
ALTER TABLE `PassPurchase`
  ADD CONSTRAINT `FK_PassPurchase_PassType` FOREIGN KEY (`PassTypeId`) REFERENCES `PassType` (`PassTypeId`),
  ADD CONSTRAINT `FK_PassPurchase_User` FOREIGN KEY (`UserAccountId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `PassType`
--
ALTER TABLE `PassType`
  ADD CONSTRAINT `FK_PassType_EventType` FOREIGN KEY (`EventTypeId`) REFERENCES `EventType` (`EventTypeId`),
  ADD CONSTRAINT `FK_PassType_Scope` FOREIGN KEY (`PassScope`) REFERENCES `PassScope` (`PassScope`);

--
-- Constraints for table `PasswordResetToken`
--
ALTER TABLE `PasswordResetToken`
  ADD CONSTRAINT `FK_PasswordResetToken_User` FOREIGN KEY (`UserAccountId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `Payment`
--
ALTER TABLE `Payment`
  ADD CONSTRAINT `FK_Payment_Method` FOREIGN KEY (`Method`) REFERENCES `PaymentMethod` (`Method`),
  ADD CONSTRAINT `FK_Payment_Order` FOREIGN KEY (`OrderId`) REFERENCES `Order` (`OrderId`),
  ADD CONSTRAINT `FK_Payment_Status` FOREIGN KEY (`Status`) REFERENCES `PaymentStatus` (`Status`);

--
-- Constraints for table `Program`
--
ALTER TABLE `Program`
  ADD CONSTRAINT `FK_Program_User` FOREIGN KEY (`UserAccountId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `ProgramItem`
--
ALTER TABLE `ProgramItem`
  ADD CONSTRAINT `FK_ProgramItem_HistoryTour` FOREIGN KEY (`HistoryTourId`) REFERENCES `HistoryTour` (`HistoryTourId`),
  ADD CONSTRAINT `FK_ProgramItem_PassType` FOREIGN KEY (`PassTypeId`) REFERENCES `PassType` (`PassTypeId`),
  ADD CONSTRAINT `FK_ProgramItem_PriceTier` FOREIGN KEY (`PriceTierId`) REFERENCES `PriceTier` (`PriceTierId`),
  ADD CONSTRAINT `FK_ProgramItem_Program` FOREIGN KEY (`ProgramId`) REFERENCES `Program` (`ProgramId`),
  ADD CONSTRAINT `FK_ProgramItem_Reservation` FOREIGN KEY (`ReservationId`) REFERENCES `Reservation` (`ReservationId`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_ProgramItem_Session` FOREIGN KEY (`EventSessionId`) REFERENCES `EventSession` (`EventSessionId`);

--
-- Constraints for table `Reservation`
--
ALTER TABLE `Reservation`
  ADD CONSTRAINT `FK_Reservation_Event` FOREIGN KEY (`EventId`) REFERENCES `Event` (`EventId`);

--
-- Constraints for table `ScheduleDay`
--
ALTER TABLE `ScheduleDay`
  ADD CONSTRAINT `FK_ScheduleDay_Type` FOREIGN KEY (`EventTypeId`) REFERENCES `EventType` (`EventTypeId`);

--
-- Constraints for table `SessionDiscountRule`
--
ALTER TABLE `SessionDiscountRule`
  ADD CONSTRAINT `FK_DiscountRule_Session` FOREIGN KEY (`EventSessionId`) REFERENCES `EventSession` (`EventSessionId`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_DiscountRule_Tier` FOREIGN KEY (`AppliesToPriceTierId`) REFERENCES `PriceTier` (`PriceTierId`);

--
-- Constraints for table `Ticket`
--
ALTER TABLE `Ticket`
  ADD CONSTRAINT `FK_Ticket_OrderItem` FOREIGN KEY (`OrderItemId`) REFERENCES `OrderItem` (`OrderItemId`),
  ADD CONSTRAINT `FK_Ticket_Pdf` FOREIGN KEY (`PdfAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_Ticket_ScannedBy` FOREIGN KEY (`ScannedByUserId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `UserAccount`
--
ALTER TABLE `UserAccount`
  ADD CONSTRAINT `FK_UserAccount_ProfilePic` FOREIGN KEY (`ProfilePictureAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_UserAccount_Role` FOREIGN KEY (`UserRoleId`) REFERENCES `UserRole` (`UserRoleId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
