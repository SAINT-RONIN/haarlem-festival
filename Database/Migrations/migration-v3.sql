-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Feb 06, 2026 at 06:22 PM
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
  `BioHtml` text NOT NULL DEFAULT '<p></p>',
  `ImageAssetId` int(11) DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Artist`
--

INSERT INTO `Artist` (`ArtistId`, `Name`, `Style`, `BioHtml`, `ImageAssetId`, `IsActive`, `CreatedAtUtc`) VALUES
(1, 'Gumbo Kings', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(2, 'Evolve', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(3, 'Ntjam Rosie', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(4, 'Wicked Jazz Sounds', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(5, 'Wouter Hamel', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(6, 'Jonna Frazer', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(7, 'Karsu', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(8, 'Uncle Sue', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(9, 'Chris Allen', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(10, 'Myles Sanko', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(11, 'Ilse Huizinga', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(12, 'Eric Vloeimans and Hotspot!', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(13, 'Gare du Nord', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(14, 'Rilan & The Bombadiers', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(15, 'Soul Six', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(16, 'Han Bennink', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(17, 'The Nordanians', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(18, 'Lilith Merlot', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(19, 'Ruis Soundsystem', 'Jazz', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(20, 'Nicky Romero', 'Dance/EDM', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(21, 'Afrojack', 'Dance/EDM', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(22, 'Tiësto', 'Dance/EDM', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(23, 'Hardwell', 'Dance/EDM', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(24, 'Martin Garrix', 'Dance/EDM', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(25, 'Armin van Buuren', 'Dance/EDM', '<p></p>', NULL, 1, '2026-02-06 15:30:32');

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
(1, 1, 'hero_main_title', 'HEADING', 'Haarlem Festivals', NULL, NULL, '2026-02-06 14:13:59'),
(2, 1, 'hero_subtitle', 'TEXT', 'Four July Days Bringing People Together', NULL, NULL, '2026-02-06 14:13:59'),
(3, 1, 'hero_button_primary', 'BUTTON_TEXT', 'Discover all types of events', NULL, NULL, '2026-02-06 14:13:59'),
(4, 1, 'hero_button_secondary', 'BUTTON_TEXT', 'Events schedule', NULL, NULL, '2026-02-06 14:13:59'),
(5, 2, 'banner_main_title', 'HEADING', 'Explore all upcoming events', NULL, NULL, '2026-02-06 14:13:59'),
(6, 2, 'banner_subtitle', 'TEXT', 'See every theme and activity happening during the festival weekend.', NULL, NULL, '2026-02-06 14:13:59'),
(7, 3, 'about_main_title', 'HEADING', 'What is Haarlem Festival?', NULL, NULL, '2026-02-06 14:13:59'),
(8, 3, 'about_tagline', 'TEXT', 'A celebration of culture and creativity', NULL, NULL, '2026-02-06 14:13:59'),
(9, 3, 'about_description', 'HTML', NULL, '<p>Haarlem Festival is a four-day celebration that brings together the city\'s most inspiring music, stories, food, and cultural experiences. During the last weekend of July, Haarlem transforms into a lively meeting place where residents and visitors explore jazz performances, storytelling sessions, gourmet specials, historic routes, children\'s adventures at Teylers, and energetic DJ sets.</p><p>The festival highlights the diversity and creativity of Haarlem, offering something for every age group and interest. Whether you want to discover new artists, enjoy local flavors, learn about the city\'s history, or simply experience Haarlem in a new way, the festival invites you to take part and enjoy the atmosphere.</p>', NULL, '2026-02-06 14:13:59'),
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
(39, 11, 'schedule_subtitle_2', 'TEXT', 'We invite you to join us for music, stories, tour, and great food.', NULL, NULL, '2026-02-06 14:13:59');

-- --------------------------------------------------------

--
-- Table structure for table `CmsPage`
--

CREATE TABLE `CmsPage` (
  `CmsPageId` int(11) NOT NULL,
  `Slug` varchar(60) NOT NULL,
  `Title` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `CmsPage`
--

INSERT INTO `CmsPage` (`CmsPageId`, `Slug`, `Title`) VALUES
(1, 'home', 'Haarlem Festival Homepage');

-- --------------------------------------------------------

--
-- Table structure for table `CmsSection`
--

CREATE TABLE `CmsSection` (
  `CmsSectionId` int(11) NOT NULL,
  `CmsPageId` int(11) NOT NULL,
  `SectionKey` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 1, 'hero_section'),
(11, 1, 'schedule_section'),
(10, 1, 'venue_map_section');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Event`
--

CREATE TABLE `Event` (
  `EventId` int(11) NOT NULL,
  `EventTypeId` int(11) NOT NULL,
  `Title` varchar(160) NOT NULL,
  `ShortDescription` varchar(300) NOT NULL DEFAULT '',
  `LongDescriptionHtml` text NOT NULL DEFAULT '<p></p>',
  `FeaturedImageAssetId` int(11) DEFAULT NULL,
  `VenueId` int(11) DEFAULT NULL,
  `ArtistId` int(11) DEFAULT NULL,
  `RestaurantId` int(11) DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `Event`
--

INSERT INTO `Event` (`EventId`, `EventTypeId`, `Title`, `ShortDescription`, `LongDescriptionHtml`, `FeaturedImageAssetId`, `VenueId`, `ArtistId`, `RestaurantId`, `IsActive`, `CreatedAtUtc`) VALUES
(1, 1, 'Gumbo Kings', 'Live jazz performance by Gumbo Kings', '<p></p>', NULL, 1, 1, NULL, 1, '2026-02-06 15:30:32'),
(2, 1, 'Evolve', 'Live jazz performance by Evolve', '<p></p>', NULL, 1, 2, NULL, 1, '2026-02-06 15:30:32'),
(3, 1, 'Ntjam Rosie', 'Live jazz performance by Ntjam Rosie', '<p></p>', NULL, 1, 3, NULL, 1, '2026-02-06 15:30:32'),
(4, 1, 'Wicked Jazz Sounds', 'Live jazz performance by Wicked Jazz Sounds', '<p></p>', NULL, 1, 4, NULL, 1, '2026-02-06 15:30:32'),
(5, 1, 'Wouter Hamel', 'Live jazz performance by Wouter Hamel', '<p></p>', NULL, 1, 5, NULL, 1, '2026-02-06 15:30:32'),
(6, 1, 'Jonna Frazer', 'Live jazz performance by Jonna Frazer', '<p></p>', NULL, 1, 6, NULL, 1, '2026-02-06 15:30:32'),
(7, 1, 'Karsu', 'Live jazz performance by Karsu', '<p></p>', NULL, 1, 7, NULL, 1, '2026-02-06 15:30:32'),
(8, 1, 'Uncle Sue', 'Live jazz performance by Uncle Sue', '<p></p>', NULL, 1, 8, NULL, 1, '2026-02-06 15:30:32'),
(9, 1, 'Chris Allen', 'Live jazz performance by Chris Allen', '<p></p>', NULL, 1, 9, NULL, 1, '2026-02-06 15:30:32'),
(10, 1, 'Myles Sanko', 'Live jazz performance by Myles Sanko', '<p></p>', NULL, 1, 10, NULL, 1, '2026-02-06 15:30:32'),
(11, 1, 'Ilse Huizinga', 'Live jazz performance by Ilse Huizinga', '<p></p>', NULL, 1, 11, NULL, 1, '2026-02-06 15:30:32'),
(12, 1, 'Eric Vloeimans and Hotspot!', 'Live jazz performance by Eric Vloeimans and Hotspot!', '<p></p>', NULL, 1, 12, NULL, 1, '2026-02-06 15:30:32'),
(13, 1, 'Gare du Nord', 'Live jazz performance by Gare du Nord', '<p></p>', NULL, 1, 13, NULL, 1, '2026-02-06 15:30:32'),
(14, 1, 'Rilan & The Bombadiers', 'Live jazz performance by Rilan & The Bombadiers', '<p></p>', NULL, 1, 14, NULL, 1, '2026-02-06 15:30:32'),
(15, 1, 'Soul Six', 'Live jazz performance by Soul Six', '<p></p>', NULL, 1, 15, NULL, 1, '2026-02-06 15:30:32'),
(16, 1, 'Han Bennink', 'Live jazz performance by Han Bennink', '<p></p>', NULL, 1, 16, NULL, 1, '2026-02-06 15:30:32'),
(17, 1, 'The Nordanians', 'Live jazz performance by The Nordanians', '<p></p>', NULL, 1, 17, NULL, 1, '2026-02-06 15:30:32'),
(18, 1, 'Lilith Merlot', 'Live jazz performance by Lilith Merlot', '<p></p>', NULL, 1, 18, NULL, 1, '2026-02-06 15:30:32'),
(19, 1, 'Ruis Soundsystem', 'Live jazz performance by Ruis Soundsystem', '<p></p>', NULL, 2, 19, NULL, 1, '2026-02-06 15:30:32'),
(20, 2, 'Nicky Romero / Afrojack - Back2Back', 'Back2Back session featuring Nicky Romero and Afrojack', '<p></p>', NULL, 3, NULL, NULL, 1, '2026-02-06 15:30:32'),
(21, 2, 'Tiësto - Club', 'Club session by Tiësto', '<p></p>', NULL, 4, 22, NULL, 1, '2026-02-06 15:30:32'),
(22, 2, 'Hardwell - Club', 'Club session by Hardwell', '<p></p>', NULL, 5, 23, NULL, 1, '2026-02-06 15:30:32'),
(23, 2, 'Armin van Buuren - Club', 'Club session by Armin van Buuren', '<p></p>', NULL, 6, 25, NULL, 1, '2026-02-06 15:30:32'),
(24, 2, 'Martin Garrix - Club', 'Club session by Martin Garrix', '<p></p>', NULL, 7, 24, NULL, 1, '2026-02-06 15:30:32'),
(25, 2, 'Hardwell / Martin Garrix / Armin van Buuren - Back2Back', 'Back2Back session featuring Hardwell, Martin Garrix and Armin van Buuren', '<p></p>', NULL, 8, NULL, NULL, 1, '2026-02-06 15:30:32'),
(26, 2, 'Afrojack - Club', 'Club session by Afrojack', '<p></p>', NULL, 5, 21, NULL, 1, '2026-02-06 15:30:32'),
(27, 2, 'Tiësto - TiëstoWorld', 'TiëstoWorld session by Tiësto', '<p></p>', NULL, 3, 22, NULL, 1, '2026-02-06 15:30:32'),
(28, 2, 'Nicky Romero - Club', 'Club session by Nicky Romero', '<p></p>', NULL, 4, 20, NULL, 1, '2026-02-06 15:30:32'),
(29, 2, 'Afrojack / Tiësto / Nicky Romero - Back2Back', 'Back2Back session featuring Afrojack, Tiësto and Nicky Romero', '<p></p>', NULL, 8, NULL, NULL, 1, '2026-02-06 15:30:32'),
(30, 2, 'Armin van Buuren - Club (Sunday)', 'Club session by Armin van Buuren', '<p></p>', NULL, 5, 25, NULL, 1, '2026-02-06 15:30:32'),
(31, 2, 'Hardwell - Club (Sunday)', 'Club session by Hardwell', '<p></p>', NULL, 6, 23, NULL, 1, '2026-02-06 15:30:32'),
(32, 2, 'Martin Garrix - Club (Sunday)', 'Club session by Martin Garrix', '<p></p>', NULL, 4, 24, NULL, 1, '2026-02-06 15:30:32'),
(33, 3, 'Haarlem History Walking Tour', 'Guided walking tour through historic Haarlem. Due to the nature of this walk, participants must be a minimum of 12 years old and no strollers are allowed.', '<p>Groups will consist of 12 participants + 1 guide. Available in English, Dutch, and Chinese.</p>', NULL, 9, NULL, NULL, 1, '2026-02-06 15:30:32'),
(34, 4, 'Winnie de Poeh (4+)', 'Stories for the whole family', '<p></p>', NULL, 10, NULL, NULL, 1, '2026-02-06 15:30:32'),
(35, 4, 'Omdenken Podcast', 'Recording podcast with audience', '<p></p>', NULL, 11, NULL, NULL, 1, '2026-02-06 15:30:32'),
(36, 4, 'The story of Buurderij Haarlem', 'Stories with impact', '<p></p>', NULL, 12, NULL, NULL, 1, '2026-02-06 15:30:32'),
(37, 4, 'Corrie voor kinderen', 'Stories for the whole family', '<p></p>', NULL, 13, NULL, NULL, 1, '2026-02-06 15:30:32'),
(38, 4, 'Winnaars van verhalenvertel wedstrijd, verhalen voor Haarlem', 'Best off - the contest will be organized in June', '<p></p>', NULL, 10, NULL, NULL, 1, '2026-02-06 15:30:32'),
(39, 4, 'Het verhaal van de Oeserzwammerij', 'Stories with impact', '<p></p>', NULL, 12, NULL, NULL, 1, '2026-02-06 15:30:32'),
(40, 4, 'Flip Thinking Podcast', 'Recording podcast with audience', '<p></p>', NULL, 11, NULL, NULL, 1, '2026-02-06 15:30:32'),
(41, 4, 'Meneer Anansi', 'Stories for the whole family', '<p></p>', NULL, 14, NULL, NULL, 1, '2026-02-06 15:30:32'),
(42, 4, 'Mister Anansi', 'Stories for the whole family', '<p></p>', NULL, 14, NULL, NULL, 1, '2026-02-06 15:30:32'),
(43, 4, 'Podcastlast Haarlem Special', 'Recording podcast with audience', '<p></p>', NULL, 11, NULL, NULL, 1, '2026-02-06 15:30:32'),
(44, 4, 'De geschiedenis van familie ten Boom', 'Stories with impact', '<p></p>', NULL, 13, NULL, NULL, 1, '2026-02-06 15:30:32'),
(45, 4, 'The history of the Ten Boom Family', 'Stories with impact', '<p></p>', NULL, 13, NULL, NULL, 1, '2026-02-06 15:30:32'),
(46, 4, 'Winners of story telling competition, stories for Haarlem', 'Best off - the contest will be organized in June', '<p></p>', NULL, 10, NULL, NULL, 1, '2026-02-06 15:30:32'),
(47, 5, 'Café de Roemer - Festival Dinner', 'Dutch, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 1, 1, '2026-02-06 15:30:32'),
(48, 5, 'Ratatouille - Festival Dinner', 'French, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 2, 1, '2026-02-06 15:30:32'),
(49, 5, 'Restaurant ML - Festival Dinner', 'Dutch, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 3, 1, '2026-02-06 15:30:32'),
(50, 5, 'Restaurant Fris - Festival Dinner', 'Dutch, French, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 4, 1, '2026-02-06 15:30:32'),
(51, 5, 'New Vegas - Festival Dinner', 'Vegan cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 5, 1, '2026-02-06 15:30:32'),
(52, 5, 'Grand Cafe Brinkman - Festival Dinner', 'Dutch, European, Modern cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 6, 1, '2026-02-06 15:30:32'),
(53, 5, 'Urban Frenchy Bistro Toujours - Festival Dinner', 'Dutch, fish and seafood, European cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 7, 1, '2026-02-06 15:30:32');

-- --------------------------------------------------------

--
-- Table structure for table `EventSession`
--

CREATE TABLE `EventSession` (
  `EventSessionId` int(11) NOT NULL,
  `EventId` int(11) NOT NULL,
  `StartDateTime` datetime NOT NULL,
  `EndDateTime` datetime DEFAULT NULL,
  `CapacityTotal` int(11) NOT NULL,
  `CapacitySingleTicketLimit` int(11) NOT NULL,
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
  `IsCancelled` tinyint(1) NOT NULL DEFAULT 0,
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp(),
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `EventSession`
--

INSERT INTO `EventSession` (`EventSessionId`, `EventId`, `StartDateTime`, `EndDateTime`, `CapacityTotal`, `CapacitySingleTicketLimit`, `SoldSingleTickets`, `SoldReservedSeats`, `HallName`, `SessionType`, `DurationMinutes`, `LanguageCode`, `MinAge`, `MaxAge`, `ReservationRequired`, `IsFree`, `Notes`, `IsCancelled`, `CreatedAtUtc`, `IsActive`) VALUES
(1, 1, '2026-07-23 18:00:00', '2026-07-23 19:00:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €35,00, All-Access pass for Thu, Fri, Sat: €80,00.', 0, '2026-02-06 15:30:32', 1),
(2, 2, '2026-07-23 19:30:00', '2026-07-23 20:30:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(3, 3, '2026-07-23 21:00:00', '2026-07-23 22:00:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(4, 4, '2026-07-23 18:00:00', '2026-07-23 19:00:00', 200, 180, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(5, 5, '2026-07-23 19:30:00', '2026-07-23 20:30:00', 200, 180, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(6, 6, '2026-07-23 21:00:00', '2026-07-23 22:00:00', 200, 180, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(7, 7, '2026-07-24 18:00:00', '2026-07-24 19:00:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €35,00, All-Access pass for Thu, Fri, Sat: €80,00.', 0, '2026-02-06 15:30:32', 1),
(8, 8, '2026-07-24 19:30:00', '2026-07-24 20:30:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(9, 9, '2026-07-24 21:00:00', '2026-07-24 22:00:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(10, 10, '2026-07-24 18:00:00', '2026-07-24 19:00:00', 200, 180, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(11, 11, '2026-07-24 19:30:00', '2026-07-24 20:30:00', 200, 180, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(12, 12, '2026-07-24 21:00:00', '2026-07-24 22:00:00', 200, 180, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(13, 13, '2026-07-25 18:00:00', '2026-07-25 19:00:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €35,00, All-Access pass for Thu, Fri, Sat: €80,00.', 0, '2026-02-06 15:30:32', 1),
(14, 14, '2026-07-25 19:30:00', '2026-07-25 20:30:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(15, 15, '2026-07-25 21:00:00', '2026-07-25 22:00:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(16, 16, '2026-07-25 18:00:00', '2026-07-25 19:00:00', 150, 135, 0, 0, 'Third Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(17, 17, '2026-07-25 19:30:00', '2026-07-25 20:30:00', 150, 135, 0, 0, 'Third Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(18, 18, '2026-07-25 21:00:00', '2026-07-25 22:00:00', 150, 135, 0, 0, 'Third Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(19, 19, '2026-07-26 15:00:00', '2026-07-26 16:00:00', 9999, 9999, 0, 0, NULL, 'Live', 60, NULL, NULL, NULL, 0, 1, 'Free for all visitors. No reservation needed.', 0, '2026-02-06 15:30:32', 1),
(20, 4, '2026-07-26 16:00:00', '2026-07-26 17:00:00', 9999, 9999, 0, 0, NULL, 'Live', 60, NULL, NULL, NULL, 0, 1, '', 0, '2026-02-06 15:30:32', 1),
(21, 2, '2026-07-26 17:00:00', '2026-07-26 18:00:00', 9999, 9999, 0, 0, NULL, 'Live', 60, NULL, NULL, NULL, 0, 1, '', 0, '2026-02-06 15:30:32', 1),
(22, 17, '2026-07-26 18:00:00', '2026-07-26 19:00:00', 9999, 9999, 0, 0, NULL, 'Live', 60, NULL, NULL, NULL, 0, 1, '', 0, '2026-02-06 15:30:32', 1),
(23, 1, '2026-07-26 19:00:00', '2026-07-26 20:00:00', 9999, 9999, 0, 0, NULL, 'Live', 60, NULL, NULL, NULL, 0, 1, '', 0, '2026-02-06 15:30:32', 1),
(24, 13, '2026-07-26 20:00:00', '2026-07-26 21:00:00', 9999, 9999, 0, 0, NULL, 'Live', 60, NULL, NULL, NULL, 0, 1, '', 0, '2026-02-06 15:30:32', 1),
(25, 20, '2026-07-24 20:00:00', '2026-07-25 02:00:00', 1500, 1350, 0, 0, NULL, 'Back2Back', 360, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €125,00, All-Access pass for Fri, Sat, Sun: €250,00.', 0, '2026-02-06 15:30:32', 1),
(26, 21, '2026-07-24 22:00:00', '2026-07-24 23:30:00', 200, 180, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(27, 22, '2026-07-24 23:00:00', '2026-07-25 00:30:00', 300, 270, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(28, 23, '2026-07-24 22:00:00', '2026-07-24 23:30:00', 200, 180, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(29, 24, '2026-07-24 22:00:00', '2026-07-24 23:30:00', 200, 180, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(30, 25, '2026-07-25 14:00:00', '2026-07-25 23:00:00', 2000, 1800, 0, 0, NULL, 'Back2Back', 540, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €150,00, All-Access pass for Fri, Sat, Sun: €250,00.', 0, '2026-02-06 15:30:32', 1),
(31, 26, '2026-07-25 22:00:00', '2026-07-25 23:30:00', 300, 270, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(32, 27, '2026-07-25 21:00:00', '2026-07-26 01:00:00', 1500, 1350, 0, 0, NULL, 'TiëstoWorld', 240, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(33, 28, '2026-07-25 23:00:00', '2026-07-26 00:30:00', 200, 180, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(34, 29, '2026-07-26 14:00:00', '2026-07-26 23:00:00', 2000, 1800, 0, 0, NULL, 'Back2Back', 540, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €150,00, All-Access pass for Fri, Sat, Sun: €250,00.', 0, '2026-02-06 15:30:32', 1),
(35, 30, '2026-07-26 19:00:00', '2026-07-26 20:30:00', 300, 270, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(36, 31, '2026-07-26 21:00:00', '2026-07-26 22:30:00', 1500, 1350, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(37, 32, '2026-07-26 18:00:00', '2026-07-26 19:30:00', 200, 180, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, '2026-02-06 15:30:32', 1),
(38, 33, '2026-07-23 10:00:00', '2026-07-23 12:00:00', 24, 21, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, 'Due to the nature of this walk participants must be a minimum of 12 years old and no strollers are allowed. Groups will consist of 12 participants + 1 guide.', 0, '2026-02-06 15:30:32', 1),
(39, 33, '2026-07-23 13:00:00', '2026-07-23 15:00:00', 24, 21, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(40, 33, '2026-07-23 16:00:00', '2026-07-23 18:00:00', 24, 21, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(41, 33, '2026-07-24 10:00:00', '2026-07-24 12:00:00', 24, 21, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(42, 33, '2026-07-24 13:00:00', '2026-07-24 15:00:00', 36, 32, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(43, 33, '2026-07-24 16:00:00', '2026-07-24 18:00:00', 24, 21, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(44, 33, '2026-07-25 10:00:00', '2026-07-25 12:00:00', 48, 43, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(45, 33, '2026-07-25 13:00:00', '2026-07-25 15:00:00', 60, 54, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(46, 33, '2026-07-25 16:00:00', '2026-07-25 18:00:00', 36, 32, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(47, 33, '2026-07-26 10:00:00', '2026-07-26 12:00:00', 60, 54, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(48, 33, '2026-07-26 13:00:00', '2026-07-26 15:00:00', 96, 86, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(49, 33, '2026-07-26 16:00:00', '2026-07-26 18:00:00', 24, 21, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(50, 34, '2026-07-23 16:00:00', '2026-07-23 17:00:00', 100, 90, 0, 0, NULL, 'Storytelling', 60, 'NL', 4, NULL, 0, 0, 'Stories for the whole family', 0, '2026-02-06 15:30:32', 1),
(51, 35, '2026-07-23 19:00:00', '2026-07-23 20:15:00', 100, 90, 0, 0, NULL, 'Podcast', 75, 'NL', 16, NULL, 0, 0, 'Recording podcast with audience', 0, '2026-02-06 15:30:32', 1),
(52, 36, '2026-07-23 20:30:00', '2026-07-23 21:45:00', 100, 90, 0, 0, NULL, 'Storytelling', 75, 'ENG', 16, NULL, 0, 0, 'Stories with impact', 0, '2026-02-06 15:30:32', 1),
(53, 37, '2026-07-24 16:00:00', '2026-07-24 17:00:00', 80, 72, 0, 0, NULL, 'Storytelling', 60, 'NL', 10, NULL, 0, 0, 'Stories for the whole family', 0, '2026-02-06 15:30:32', 1),
(54, 38, '2026-07-24 19:00:00', '2026-07-24 20:30:00', 100, 90, 0, 0, NULL, 'Storytelling', 90, 'NL', 12, NULL, 0, 0, 'Best off - the contest will be organized in June', 0, '2026-02-06 15:30:32', 1),
(55, 39, '2026-07-24 19:00:00', '2026-07-24 20:15:00', 100, 90, 0, 0, NULL, 'Storytelling', 75, 'NL', 16, NULL, 0, 0, 'Stories with impact', 0, '2026-02-06 15:30:32', 1),
(56, 40, '2026-07-24 20:30:00', '2026-07-24 21:45:00', 100, 90, 0, 0, NULL, 'Podcast', 75, 'ENG', 16, NULL, 0, 0, 'Recording podcast with audience', 0, '2026-02-06 15:30:32', 1),
(57, 41, '2026-07-25 10:00:00', '2026-07-25 11:00:00', 100, 90, 0, 0, NULL, 'Storytelling', 60, 'NL', 2, 102, 0, 0, 'Stories for the whole family', 0, '2026-02-06 15:30:32', 1),
(58, 42, '2026-07-25 15:00:00', '2026-07-25 16:00:00', 100, 90, 0, 0, NULL, 'Storytelling', 60, 'ENG', 2, 102, 0, 0, 'Stories for the whole family', 0, '2026-02-06 15:30:32', 1),
(59, 43, '2026-07-25 14:00:00', '2026-07-25 15:15:00', 100, 90, 0, 0, NULL, 'Podcast', 75, 'NL', 12, NULL, 0, 0, 'Recording podcast with audience', 0, '2026-02-06 15:30:32', 1),
(60, 44, '2026-07-25 13:00:00', '2026-07-25 14:30:00', 80, 72, 0, 0, NULL, 'Storytelling', 90, 'NL', 12, NULL, 0, 0, 'Stories with impact', 0, '2026-02-06 15:30:32', 1),
(61, 42, '2026-07-26 10:00:00', '2026-07-26 11:00:00', 100, 90, 0, 0, NULL, 'Storytelling', 60, 'ENG', 2, 102, 0, 0, 'Stories for the whole family', 0, '2026-02-06 15:30:32', 1),
(62, 41, '2026-07-26 15:00:00', '2026-07-26 16:00:00', 100, 90, 0, 0, NULL, 'Storytelling', 60, 'NL', 2, 102, 0, 0, 'Stories for the whole family', 0, '2026-02-06 15:30:32', 1),
(63, 45, '2026-07-26 13:00:00', '2026-07-26 14:30:00', 80, 72, 0, 0, NULL, 'Storytelling', 90, 'ENG', 12, NULL, 0, 0, 'Stories with impact', 0, '2026-02-06 15:30:32', 1),
(64, 46, '2026-07-26 16:00:00', '2026-07-26 17:30:00', 100, 90, 0, 0, NULL, 'Storytelling', 90, 'ENG', 12, NULL, 0, 0, 'Best off - the contest will be organized in June', 0, '2026-02-06 15:30:32', 1),
(65, 47, '2026-07-24 18:00:00', '2026-07-24 19:30:00', 35, 31, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(66, 47, '2026-07-24 19:30:00', '2026-07-24 21:00:00', 35, 31, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(67, 47, '2026-07-24 21:00:00', '2026-07-24 22:30:00', 35, 31, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(68, 48, '2026-07-24 17:00:00', '2026-07-24 19:00:00', 52, 46, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(69, 48, '2026-07-24 19:00:00', '2026-07-24 21:00:00', 52, 46, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(70, 48, '2026-07-24 21:00:00', '2026-07-24 23:00:00', 52, 46, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(71, 49, '2026-07-24 17:00:00', '2026-07-24 19:00:00', 60, 54, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(72, 49, '2026-07-24 19:00:00', '2026-07-24 21:00:00', 60, 54, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(73, 50, '2026-07-24 17:30:00', '2026-07-24 19:00:00', 45, 40, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(74, 50, '2026-07-24 19:00:00', '2026-07-24 20:30:00', 45, 40, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(75, 50, '2026-07-24 20:30:00', '2026-07-24 22:00:00', 45, 40, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(76, 51, '2026-07-24 17:00:00', '2026-07-24 18:30:00', 36, 32, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(77, 51, '2026-07-24 18:30:00', '2026-07-24 20:00:00', 36, 32, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(78, 51, '2026-07-24 20:00:00', '2026-07-24 21:30:00', 36, 32, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(79, 52, '2026-07-24 16:30:00', '2026-07-24 18:00:00', 100, 90, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(80, 52, '2026-07-24 18:00:00', '2026-07-24 19:30:00', 100, 90, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(81, 52, '2026-07-24 19:30:00', '2026-07-24 21:00:00', 100, 90, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(82, 53, '2026-07-24 17:30:00', '2026-07-24 19:00:00', 48, 43, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(83, 53, '2026-07-24 19:00:00', '2026-07-24 20:30:00', 48, 43, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1),
(84, 53, '2026-07-24 20:30:00', '2026-07-24 22:00:00', 48, 43, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, '2026-02-06 15:30:32', 1);

-- --------------------------------------------------------

--
-- Table structure for table `EventSessionLabel`
--

CREATE TABLE `EventSessionLabel` (
  `EventSessionLabelId` int(11) NOT NULL,
  `EventSessionId` int(11) NOT NULL,
  `LabelText` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(116, 84, 2, 17.50, 'EUR', 9.00);

-- --------------------------------------------------------

--
-- Table structure for table `EventType`
--

CREATE TABLE `EventType` (
  `EventTypeId` int(11) NOT NULL,
  `Name` varchar(40) NOT NULL,
  `Slug` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `TotalAmount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp(),
  `PaidAtUtc` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

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
-- Table structure for table `PriceTier`
--

CREATE TABLE `PriceTier` (
  `PriceTierId` int(11) NOT NULL,
  `Name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `PriceTier`
--

INSERT INTO `PriceTier` (`PriceTierId`, `Name`) VALUES
(1, 'Adult'),
(2, 'ChildU12'),
(3, 'Family'),
(5, 'PayWhatYouLike'),
(4, 'ReservationFee');

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

-- --------------------------------------------------------

--
-- Table structure for table `ProgramItem`
--

CREATE TABLE `ProgramItem` (
  `ProgramItemId` int(11) NOT NULL,
  `ProgramId` int(11) NOT NULL,
  `EventSessionId` int(11) DEFAULT NULL,
  `HistoryTourId` int(11) DEFAULT NULL,
  `PassTypeId` int(11) DEFAULT NULL,
  `PassValidDate` date DEFAULT NULL,
  `Quantity` int(11) NOT NULL,
  `DonationAmount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Restaurant`
--

CREATE TABLE `Restaurant` (
  `RestaurantId` int(11) NOT NULL,
  `Name` varchar(120) NOT NULL,
  `AddressLine` varchar(200) NOT NULL DEFAULT '',
  `City` varchar(80) NOT NULL DEFAULT 'Haarlem',
  `Stars` int(11) DEFAULT NULL,
  `CuisineType` varchar(160) NOT NULL DEFAULT '',
  `DescriptionHtml` text NOT NULL DEFAULT '<p></p>',
  `ImageAssetId` int(11) DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAtUtc` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `Restaurant`
--

INSERT INTO `Restaurant` (`RestaurantId`, `Name`, `AddressLine`, `City`, `Stars`, `CuisineType`, `DescriptionHtml`, `ImageAssetId`, `IsActive`, `CreatedAtUtc`) VALUES
(1, 'Café de Roemer', 'Botermarkt 17, 2011 XL Haarlem', 'Haarlem', 4, 'Dutch, fish and seafood, European', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(2, 'Ratatouille', 'Spaarne 96, 2011 CL Haarlem', 'Haarlem', 4, 'French, fish and seafood, European', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(3, 'Restaurant ML', 'Kleine Houtstraat 70, 2011 DR Haarlem', 'Haarlem', 4, 'Dutch, fish and seafood, European', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(4, 'Restaurant Fris', 'Twijnderslaan 7, 2012 BG Haarlem', 'Haarlem', 4, 'Dutch, French, European', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(5, 'New Vegas', 'Koningstraat 5, 2011 TB Haarlem', 'Haarlem', 3, 'Vegan', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(6, 'Grand Cafe Brinkman', 'Grote Markt 13, 2011 RC Haarlem', 'Haarlem', 3, 'Dutch, European, Modern', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(7, 'Urban Frenchy Bistro Toujours', 'Oude Groenmarkt 10-12, 2011 HL Haarlem', 'Haarlem', 3, 'Dutch, fish and seafood, European', '<p></p>', NULL, 1, '2026-02-06 15:30:32');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `UserAccount`
--

CREATE TABLE `UserAccount` (
  `UserAccountId` int(11) NOT NULL,
  `UserRoleId` int(11) NOT NULL,
  `Username` varchar(60) NOT NULL,
  `Email` varchar(200) NOT NULL,
  `PasswordHash` varbinary(64) NOT NULL,
  `PasswordSalt` varbinary(32) NOT NULL,
  `FirstName` varchar(60) NOT NULL,
  `LastName` varchar(80) NOT NULL,
  `ProfilePictureAssetId` int(11) DEFAULT NULL,
  `IsEmailConfirmed` tinyint(1) NOT NULL DEFAULT 0,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `RegisteredAtUtc` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdatedAtUtc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `UserRole`
--

CREATE TABLE `UserRole` (
  `UserRoleId` int(11) NOT NULL,
  `RoleName` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(14, 'Theater Elswout', 'Elswoutslaan 24-a, 2051 AE Overveen', 'Overveen', '2026-02-06 15:30:32', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Artist`
--
ALTER TABLE `Artist`
  ADD PRIMARY KEY (`ArtistId`),
  ADD KEY `FK_Artist_Image` (`ImageAssetId`);

--
-- Indexes for table `CmsItem`
--
ALTER TABLE `CmsItem`
  ADD PRIMARY KEY (`CmsItemId`),
  ADD UNIQUE KEY `UQ_CmsItem` (`CmsSectionId`,`ItemKey`),
  ADD UNIQUE KEY `UNQ_CmsItem_SectionKey` (`CmsSectionId`,`ItemKey`),
  ADD KEY `FK_CmsItem_Asset` (`MediaAssetId`),
  ADD KEY `IX_CmsItem_Section` (`CmsSectionId`),
  ADD KEY `IDX_CmsItem_Section` (`CmsSectionId`);

--
-- Indexes for table `CmsPage`
--
ALTER TABLE `CmsPage`
  ADD PRIMARY KEY (`CmsPageId`),
  ADD UNIQUE KEY `Slug` (`Slug`),
  ADD UNIQUE KEY `UNQ_CmsPage_Slug` (`Slug`);

--
-- Indexes for table `CmsSection`
--
ALTER TABLE `CmsSection`
  ADD PRIMARY KEY (`CmsSectionId`),
  ADD UNIQUE KEY `UQ_CmsSection` (`CmsPageId`,`SectionKey`),
  ADD UNIQUE KEY `UNQ_CmsSection_PageKey` (`CmsPageId`,`SectionKey`),
  ADD KEY `IX_CmsSection_Page` (`CmsPageId`),
  ADD KEY `IDX_CmsSection_Page` (`CmsPageId`);

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
  ADD KEY `FK_Event_Image` (`FeaturedImageAssetId`),
  ADD KEY `FK_Event_Venue` (`VenueId`),
  ADD KEY `FK_Event_Artist` (`ArtistId`),
  ADD KEY `FK_Event_Restaurant` (`RestaurantId`),
  ADD KEY `IX_Event_Type` (`EventTypeId`,`IsActive`),
  ADD KEY `IDX_Event_TypeActive` (`EventTypeId`,`IsActive`),
  ADD KEY `IDX_Event_Active` (`IsActive`,`EventId`);

--
-- Indexes for table `EventSession`
--
ALTER TABLE `EventSession`
  ADD PRIMARY KEY (`EventSessionId`),
  ADD KEY `IX_Session_EventTime` (`EventId`,`StartDateTime`),
  ADD KEY `IX_EventSession_Age` (`MinAge`,`MaxAge`),
  ADD KEY `IDX_EventSession_Capacity` (`EventId`,`StartDateTime`,`CapacityTotal`,`SoldSingleTickets`,`SoldReservedSeats`),
  ADD KEY `IDX_EventSession_Event` (`EventId`,`StartDateTime`),
  ADD KEY `IDX_EventSession_DateTime` (`StartDateTime`,`IsCancelled`),
  ADD KEY `IDX_EventSession_Active` (`IsActive`,`EventId`);

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
  ADD KEY `IDX_Order_User` (`UserAccountId`,`CreatedAtUtc` DESC),
  ADD KEY `IDX_Order_UserDate` (`UserAccountId`,`CreatedAtUtc` DESC);

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
-- Indexes for table `PassPurchase`
--
ALTER TABLE `PassPurchase`
  ADD PRIMARY KEY (`PassPurchaseId`),
  ADD KEY `FK_PassPurchase_PassType` (`PassTypeId`),
  ADD KEY `FK_PassPurchase_User` (`UserAccountId`);

--
-- Indexes for table `PassType`
--
ALTER TABLE `PassType`
  ADD PRIMARY KEY (`PassTypeId`),
  ADD UNIQUE KEY `UQ_PassType` (`EventTypeId`,`PassName`);

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
  ADD KEY `IX_Payment_Order` (`OrderId`);

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
  ADD KEY `IX_ProgramItem_HistoryTour` (`HistoryTourId`);

--
-- Indexes for table `Restaurant`
--
ALTER TABLE `Restaurant`
  ADD PRIMARY KEY (`RestaurantId`),
  ADD KEY `FK_Restaurant_Image` (`ImageAssetId`);

--
-- Indexes for table `ScheduleDay`
--
ALTER TABLE `ScheduleDay`
  ADD PRIMARY KEY (`ScheduleDayId`),
  ADD UNIQUE KEY `UQ_ScheduleDay` (`EventTypeId`,`Date`),
  ADD KEY `IX_ScheduleDay_Filter` (`EventTypeId`,`Date`,`IsDeleted`);

--
-- Indexes for table `SessionDiscountRule`
--
ALTER TABLE `SessionDiscountRule`
  ADD PRIMARY KEY (`SessionDiscountRuleId`),
  ADD KEY `FK_DiscountRule_Tier` (`AppliesToPriceTierId`),
  ADD KEY `IX_DiscountRule_Session` (`EventSessionId`);

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
  MODIFY `ArtistId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `CmsItem`
--
ALTER TABLE `CmsItem`
  MODIFY `CmsItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `CmsPage`
--
ALTER TABLE `CmsPage`
  MODIFY `CmsPageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `CmsSection`
--
ALTER TABLE `CmsSection`
  MODIFY `CmsSectionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `EmailConfirmationToken`
--
ALTER TABLE `EmailConfirmationToken`
  MODIFY `EmailConfirmationTokenId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Event`
--
ALTER TABLE `Event`
  MODIFY `EventId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `EventSession`
--
ALTER TABLE `EventSession`
  MODIFY `EventSessionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `EventSessionLabel`
--
ALTER TABLE `EventSessionLabel`
  MODIFY `EventSessionLabelId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `EventSessionPrice`
--
ALTER TABLE `EventSessionPrice`
  MODIFY `EventSessionPriceId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

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
  MODIFY `MediaAssetId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Order`
--
ALTER TABLE `Order`
  MODIFY `OrderId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `OrderItem`
--
ALTER TABLE `OrderItem`
  MODIFY `OrderItemId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PassPurchase`
--
ALTER TABLE `PassPurchase`
  MODIFY `PassPurchaseId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PassType`
--
ALTER TABLE `PassType`
  MODIFY `PassTypeId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `PasswordResetToken`
--
ALTER TABLE `PasswordResetToken`
  MODIFY `PasswordResetTokenId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Payment`
--
ALTER TABLE `Payment`
  MODIFY `PaymentId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PriceTier`
--
ALTER TABLE `PriceTier`
  MODIFY `PriceTierId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Program`
--
ALTER TABLE `Program`
  MODIFY `ProgramId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ProgramItem`
--
ALTER TABLE `ProgramItem`
  MODIFY `ProgramItemId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Restaurant`
--
ALTER TABLE `Restaurant`
  MODIFY `RestaurantId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ScheduleDay`
--
ALTER TABLE `ScheduleDay`
  MODIFY `ScheduleDayId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `SessionDiscountRule`
--
ALTER TABLE `SessionDiscountRule`
  MODIFY `SessionDiscountRuleId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ticket`
--
ALTER TABLE `Ticket`
  MODIFY `TicketId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `UserAccount`
--
ALTER TABLE `UserAccount`
  MODIFY `UserAccountId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `UserRole`
--
ALTER TABLE `UserRole`
  MODIFY `UserRoleId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Venue`
--
ALTER TABLE `Venue`
  MODIFY `VenueId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Artist`
--
ALTER TABLE `Artist`
  ADD CONSTRAINT `FK_Artist_Image` FOREIGN KEY (`ImageAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`);

--
-- Constraints for table `CmsItem`
--
ALTER TABLE `CmsItem`
  ADD CONSTRAINT `FK_CmsItem_Asset` FOREIGN KEY (`MediaAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_CmsItem_Section` FOREIGN KEY (`CmsSectionId`) REFERENCES `CmsSection` (`CmsSectionId`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `FK_Event_Restaurant` FOREIGN KEY (`RestaurantId`) REFERENCES `Restaurant` (`RestaurantId`),
  ADD CONSTRAINT `FK_Event_Type` FOREIGN KEY (`EventTypeId`) REFERENCES `EventType` (`EventTypeId`),
  ADD CONSTRAINT `FK_Event_Venue` FOREIGN KEY (`VenueId`) REFERENCES `Venue` (`VenueId`);

--
-- Constraints for table `EventSession`
--
ALTER TABLE `EventSession`
  ADD CONSTRAINT `FK_EventSession_Event` FOREIGN KEY (`EventId`) REFERENCES `Event` (`EventId`) ON DELETE CASCADE;

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
-- Constraints for table `PassPurchase`
--
ALTER TABLE `PassPurchase`
  ADD CONSTRAINT `FK_PassPurchase_PassType` FOREIGN KEY (`PassTypeId`) REFERENCES `PassType` (`PassTypeId`),
  ADD CONSTRAINT `FK_PassPurchase_User` FOREIGN KEY (`UserAccountId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `PassType`
--
ALTER TABLE `PassType`
  ADD CONSTRAINT `FK_PassType_EventType` FOREIGN KEY (`EventTypeId`) REFERENCES `EventType` (`EventTypeId`);

--
-- Constraints for table `PasswordResetToken`
--
ALTER TABLE `PasswordResetToken`
  ADD CONSTRAINT `FK_PasswordResetToken_User` FOREIGN KEY (`UserAccountId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `Payment`
--
ALTER TABLE `Payment`
  ADD CONSTRAINT `FK_Payment_Order` FOREIGN KEY (`OrderId`) REFERENCES `Order` (`OrderId`);

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
  ADD CONSTRAINT `FK_ProgramItem_Program` FOREIGN KEY (`ProgramId`) REFERENCES `Program` (`ProgramId`),
  ADD CONSTRAINT `FK_ProgramItem_Session` FOREIGN KEY (`EventSessionId`) REFERENCES `EventSession` (`EventSessionId`);

--
-- Constraints for table `Restaurant`
--
ALTER TABLE `Restaurant`
  ADD CONSTRAINT `FK_Restaurant_Image` FOREIGN KEY (`ImageAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`);

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
