-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Mar 10, 2026 at 11:52 AM
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
(1, 'Gumbo Kings', 'Soul', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(2, 'Evolve', 'Alternative', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(3, 'Ntjam Rosie', 'Soul', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(4, 'Wicked Jazz Sounds', 'Alternative', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(5, 'Wouter Hamel', 'Pop', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(6, 'Jonna Frazer', 'Soul', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(7, 'Karsu', 'Folk', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
(8, 'Uncle Sue', 'Rock', '<p></p>', NULL, 1, '2026-02-06 15:30:32'),
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
(1, 1, 'hero_main_title', 'HEADING', 'Haarlem Festivals', NULL, NULL, '2026-02-10 15:05:23'),
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
(48, 12, 'label_events_count', 'TEXT', 'events', NULL, NULL, '2026-02-06 19:16:03'),
(49, 12, 'label_no_events', 'TEXT', 'No events scheduled', NULL, NULL, '2026-02-06 19:16:03'),
(50, 12, 'btn_explore_template', 'TEXT', 'Explore {title} Events', NULL, NULL, '2026-02-06 19:16:03'),
(51, 1, 'hero_button_primary_link', 'LINK', '#events', NULL, NULL, '2026-02-08 12:17:16'),
(52, 1, 'hero_button_secondary_link', 'LINK', '#schedule', NULL, NULL, '2026-02-08 12:17:16'),
(53, 1, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/HeroImageHome.png', NULL, NULL, '2026-02-08 12:17:16'),
(54, 13, 'hero_main_title', 'HEADING', 'Stories in Haarlem', NULL, NULL, '2026-02-17 14:48:24'),
(55, 13, 'hero_subtitle', 'TEXT', 'Discover the art of storytelling in Haarlem', NULL, NULL, '2026-02-15 21:04:32'),
(56, 13, 'hero_button_primary', 'BUTTON_TEXT', 'Discover storytelling events', NULL, NULL, '2026-02-08 12:17:16'),
(57, 13, 'hero_button_primary_link', 'LINK', '#events', NULL, NULL, '2026-02-08 12:17:16'),
(58, 13, 'hero_button_secondary', 'BUTTON_TEXT', 'View schedule', NULL, NULL, '2026-02-08 12:17:16'),
(59, 13, 'hero_button_secondary_link', 'LINK', '#schedule', NULL, NULL, '2026-02-08 12:17:16'),
(60, 13, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/storytelling/hero-storytelling.jpg', NULL, NULL, '2026-02-08 14:29:20'),
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
(101, 16, 'masonry_image_01', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-02-08 14:29:20'),
(102, 16, 'masonry_image_02', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-02-08 14:29:20'),
(103, 16, 'masonry_image_03', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-02-08 14:29:20'),
(104, 16, 'masonry_image_04', 'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, '2026-02-08 14:29:20'),
(105, 16, 'masonry_image_05', 'IMAGE_PATH', '/assets/Image/storytelling/pig.jpg', NULL, NULL, '2026-02-08 14:29:20'),
(106, 16, 'masonry_image_06', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-02-08 14:29:20'),
(107, 16, 'masonry_image_07', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-02-08 14:29:20'),
(108, 16, 'masonry_image_08', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-02-08 14:29:20'),
(109, 16, 'masonry_image_09', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, '2026-02-08 14:29:20'),
(110, 16, 'masonry_image_10', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, '2026-02-08 14:29:20'),
(111, 16, 'masonry_image_11', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, NULL, '2026-02-08 14:29:20'),
(112, 16, 'masonry_image_12', 'IMAGE_PATH', '/assets/Image/storytelling/WinnieThePoohHeader.png', NULL, NULL, '2026-02-08 14:29:20'),
(113, 14, 'gradient_background_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-02-08 14:32:21'),
(114, 15, 'intro_image_alt', 'TEXT', 'Stories in Haarlem', NULL, NULL, '2026-02-08 14:34:54'),
(115, 28, 'hero_main_title', 'HEADING', 'HAARLEM JAZZ', NULL, NULL, '2026-02-10 15:02:54'),
(116, 28, 'hero_subtitle', 'TEXT', 'Experience world-class jazz performances at Haarlem\'s premier music festival. Discover our complete lineup, detailed schedules, and venue information.', NULL, NULL, '2026-02-10 14:12:56'),
(117, 28, 'hero_button_primary', 'BUTTON_TEXT', 'Discover all performances', NULL, NULL, '2026-02-10 12:02:48'),
(118, 28, 'hero_button_primary_link', 'URL', '#artists', NULL, NULL, '2026-02-10 12:02:48'),
(119, 28, 'hero_button_secondary', 'BUTTON_TEXT', 'What is Haarlem Jazz?', NULL, NULL, '2026-02-10 12:02:48'),
(120, 28, 'hero_button_secondary_link', 'URL', '#intro', NULL, NULL, '2026-02-10 12:02:48'),
(121, 29, 'gradient_heading', 'HEADING', 'Every note carries emotion, intention, and connection beyond what is heard.', NULL, NULL, '2026-02-10 12:02:48'),
(122, 29, 'gradient_subheading', 'TEXT', 'A place where jazz is experienced, not just played.', NULL, NULL, '2026-02-10 13:40:15'),
(123, 30, 'intro_heading', 'HEADING', 'Haarlem moves to the rhythm of jazz', NULL, NULL, '2026-02-10 12:02:48'),
(124, 30, 'intro_body', 'HTML', NULL, '<p>Welcome to Haarlem Jazz 2026, taking place during the last weekend of July. This year\'s festival features an outstanding lineup of international and local jazz artists performing across multiple venues in Haarlem\'s historic city center. From intimate club settings to free outdoor performances, our program offers something for every jazz enthusiast.</p>\r\n<p>The festival runs from Thursday through Sunday, with paid indoor performances at the Patronaat venue (featuring Main Hall, Second Hall, and Third Hall) and free outdoor concerts on Sunday at the iconic Grote Markt. All-access day passes and multi-day passes are available, offering excellent value for festival-goers wanting to experience multiple performances.</p>\r\n<p>Below you will find detailed information about our featured artists, complete performance schedules with exact times and venues, seating capacity for each show, ticket pricing, and booking information. We recommend reviewing the schedule carefully and booking early as many performances have limited seating.</p>', NULL, '2026-02-10 13:44:49'),
(125, 31, 'venues_heading', 'HEADING', 'Festival venues', NULL, NULL, '2026-02-10 12:02:48'),
(126, 31, 'venues_subheading', 'TEXT', 'Performance Locations', NULL, NULL, '2026-02-10 13:40:15'),
(127, 31, 'venues_description', 'TEXT', 'Haarlem Jazz 2026 takes place at two main locations in the city center. The Patronaat offers three different halls for intimate indoor performances, while the Grote Markt hosts free outdoor concerts on Sunday for all visitors.', NULL, NULL, '2026-02-10 14:12:56'),
(128, 31, 'venue_patronaat_name', 'TEXT', 'Patronaat', NULL, NULL, '2026-02-10 13:40:15'),
(129, 31, 'venue_patronaat_address1', 'TEXT', 'Zijlsingel 2', NULL, NULL, '2026-02-10 13:40:15'),
(130, 31, 'venue_patronaat_address2', 'TEXT', '2013 DN Haarlem', NULL, NULL, '2026-02-10 13:40:15'),
(131, 31, 'venue_patronaat_contact', 'TEXT', 'E-mail/reception available', NULL, NULL, '2026-02-10 13:40:15'),
(132, 31, 'venue_patronaat_hall1_name', 'TEXT', 'First Hall', NULL, NULL, '2026-02-10 13:40:15'),
(133, 31, 'venue_patronaat_hall1_desc', 'TEXT', 'Intimate performances - €10.00 per show', NULL, NULL, '2026-02-10 14:13:58'),
(134, 31, 'venue_patronaat_hall1_capacity', 'TEXT', '150 seats', NULL, NULL, '2026-02-10 13:40:15'),
(135, 31, 'venue_patronaat_hall2_name', 'TEXT', 'Second Hall', NULL, NULL, '2026-02-10 13:40:15'),
(136, 31, 'venue_patronaat_hall2_desc', 'TEXT', 'Intimate performances - €10.00 per show', NULL, NULL, '2026-02-10 14:13:58'),
(137, 31, 'venue_patronaat_hall2_capacity', 'TEXT', '150 seats', NULL, NULL, '2026-02-10 13:40:15'),
(138, 31, 'venue_patronaat_hall3_name', 'TEXT', 'Third Hall', NULL, NULL, '2026-02-10 13:40:15'),
(139, 31, 'venue_patronaat_hall3_desc', 'TEXT', 'Intimate performances - €10.00 per show', NULL, NULL, '2026-02-10 14:13:58'),
(140, 31, 'venue_patronaat_hall3_capacity', 'TEXT', '150 seats', NULL, NULL, '2026-02-10 13:40:15'),
(141, 31, 'venue_grotemarkt_name', 'TEXT', 'Grote Markt', NULL, NULL, '2026-02-10 13:40:15'),
(142, 31, 'venue_grotemarkt_location1', 'TEXT', 'Historic Market Square', NULL, NULL, '2026-02-10 13:40:15'),
(143, 31, 'venue_grotemarkt_location2', 'TEXT', 'Haarlem City Center', NULL, NULL, '2026-02-10 13:40:15'),
(144, 31, 'venue_grotemarkt_hall_name', 'TEXT', 'Open Air Stage', NULL, NULL, '2026-02-10 13:40:15'),
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
(162, 32, 'pricing_daypass_info', 'TEXT', 'All-Access pass for this day €35,00. Valid for unlimited entry to Main Hall, Second Hall, and Third Hall performances on the selected day.', NULL, NULL, '2026-02-10 14:13:58'),
(163, 32, 'pricing_3day_title', 'TEXT', 'All-Access Day Pass', NULL, NULL, '2026-02-10 13:40:15'),
(164, 32, 'pricing_3day_price', 'TEXT', '€80.00', NULL, NULL, '2026-02-10 14:13:58'),
(165, 32, 'pricing_3day_desc', 'TEXT', 'Thursday + Friday + Saturday', NULL, NULL, '2026-02-10 13:40:15'),
(166, 32, 'pricing_3day_include1', 'TEXT', 'Unlimited access all 3 days', NULL, NULL, '2026-02-10 13:40:15'),
(167, 32, 'pricing_3day_include2', 'TEXT', 'All venues and halls', NULL, NULL, '2026-02-10 13:40:15'),
(168, 32, 'pricing_3day_include3', 'TEXT', '18+ performances included', NULL, NULL, '2026-02-10 13:40:15'),
(169, 32, 'pricing_3day_include4', 'TEXT', 'Save €25 vs. day passes', NULL, NULL, '2026-02-10 14:13:58'),
(170, 32, 'pricing_3day_info', 'TEXT', 'All-Access pass for Thu, Fri, Sat: €80.00. Complete festival access for three full days of jazz performances.', NULL, NULL, '2026-02-10 14:13:58'),
(171, 33, 'schedule_cta_heading', 'HEADING', 'Ready to Plan Your Festival Experience?', NULL, NULL, '2026-02-10 12:02:48'),
(172, 33, 'schedule_cta_description', 'TEXT', 'Now that you\'ve explored our artists, check out the complete performance schedule below to see exact times, venues, and ticket availability. You can filter by day to plan your perfect jazz weekend.', NULL, NULL, '2026-02-10 14:12:56'),
(173, 33, 'schedule_cta_button', 'BUTTON_TEXT', 'View complete schedule', NULL, NULL, '2026-02-10 12:02:48'),
(174, 33, 'schedule_cta_button_link', 'URL', '#schedule', NULL, NULL, '2026-02-10 12:02:48'),
(175, 34, 'artists_heading', 'HEADING', 'Discover our lineup', NULL, NULL, '2026-02-10 12:02:48'),
(176, 35, 'booking_cta_heading', 'HEADING', 'Book Your Experience', NULL, NULL, '2026-02-10 12:02:48'),
(177, 35, 'booking_cta_description', 'TEXT', 'Secure your tickets now for the last weekend of July. With limited seating at Patronaat and free performances at Grote Markt, there\'s an option for every jazz lover. Don\'t miss out on this year\'s incredible lineup.', NULL, NULL, '2026-02-10 14:12:56'),
(178, 28, 'jazz_hero_background_image', 'MEDIA', NULL, NULL, 66, '2026-02-10 13:02:07'),
(179, 29, 'jazz_gradient_background_image', 'MEDIA', NULL, NULL, 67, '2026-02-10 13:02:07'),
(180, 30, 'jazz_intro_image', 'MEDIA', NULL, NULL, 68, '2026-02-10 13:02:07'),
(181, 34, 'artists_gumbokings_image', 'MEDIA', NULL, NULL, 72, '2026-02-10 13:27:05'),
(182, 34, 'artists_evolve_image', 'MEDIA', NULL, NULL, 73, '2026-02-10 13:27:05'),
(183, 34, 'artists_ntjam_image', 'MEDIA', NULL, NULL, 74, '2026-02-10 13:27:05'),
(184, 34, 'artists_gumbokings_name', 'TEXT', 'Gumbo Kings', NULL, NULL, '2026-02-10 13:56:11'),
(185, 34, 'artists_gumbokings_genre', 'TEXT', 'New Orleans Jazz', NULL, NULL, '2026-02-10 13:56:11'),
(186, 34, 'artists_gumbokings_description', 'TEXT', 'High-energy New Orleans style jazz band bringing authentic Big Easy sound to Haarlem. Known for infectious rhythms.', NULL, NULL, '2026-02-10 14:12:56'),
(187, 34, 'artists_gumbokings_performance_count', 'TEXT', '2', NULL, NULL, '2026-02-10 13:56:11'),
(188, 34, 'artists_gumbokings_first_performance', 'TEXT', 'Thu 18:00 - Patronaat Main Hall', NULL, NULL, '2026-02-10 13:56:11'),
(189, 34, 'artists_gumbokings_more_performances_text', 'TEXT', '+1 more', NULL, NULL, '2026-02-10 13:56:11'),
(190, 34, 'artists_evolve_name', 'TEXT', 'Evolve', NULL, NULL, '2026-02-10 13:56:11'),
(191, 34, 'artists_evolve_genre', 'TEXT', 'Contemporary Jazz', NULL, NULL, '2026-02-10 13:56:11'),
(192, 34, 'artists_evolve_description', 'TEXT', 'Progressive jazz ensemble pushing boundaries with innovative compositions. A fresh take on modern jazz traditions.', NULL, NULL, '2026-02-10 14:12:56'),
(193, 34, 'artists_evolve_performance_count', 'TEXT', '2', NULL, NULL, '2026-02-10 13:56:11'),
(194, 34, 'artists_evolve_first_performance', 'TEXT', 'Thu 18:00 - Patronaat Main Hall', NULL, NULL, '2026-02-10 13:56:11'),
(195, 34, 'artists_evolve_more_performances_text', 'TEXT', '+1 more', NULL, NULL, '2026-02-10 13:56:11'),
(196, 34, 'artists_ntjam_name', 'TEXT', 'Ntjam Rosie', NULL, NULL, '2026-02-10 13:56:11'),
(197, 34, 'artists_ntjam_genre', 'TEXT', 'Vocal Jazz', NULL, NULL, '2026-02-10 13:56:11'),
(198, 34, 'artists_ntjam_description', 'TEXT', 'Sultry vocals meet classic jazz standards. Rosie brings timeless elegance and powerful vocal performances to every show.', NULL, NULL, '2026-02-10 14:12:56'),
(199, 34, 'artists_ntjam_performance_count', 'TEXT', '2', NULL, NULL, '2026-02-10 13:56:11'),
(200, 34, 'artists_ntjam_first_performance', 'TEXT', 'Thu 21:00 - Patronaat Main Hall', NULL, NULL, '2026-02-10 13:56:11'),
(201, 34, 'artists_ntjam_more_performances_text', 'TEXT', '', NULL, NULL, '2026-02-10 13:56:11'),
(202, 36, 'schedule_title', 'HEADING', 'Storytelling schedule', NULL, NULL, '2026-02-10 17:17:49'),
(203, 36, 'schedule_year', 'TEXT', '2026', NULL, NULL, '2026-02-10 17:17:49'),
(204, 36, 'schedule_filters_button_text', 'BUTTON_TEXT', 'Filters', NULL, NULL, '2026-02-10 17:17:49'),
(205, 36, 'schedule_additional_info_title', 'TEXT', 'Additional Information:', NULL, NULL, '2026-02-10 17:17:49'),
(206, 36, 'schedule_additional_info_body', 'HTML', NULL, 'Some storytelling events are pay-what-you-like, meaning they are free to attend, with optional contributions that support the actor.<br/>After reserving your spot, visit your My Program page to choose how much you would like to contribute to each pay-what-you-like event.', NULL, '2026-02-10 17:17:49'),
(207, 36, 'schedule_show_filters', 'TEXT', '1', NULL, NULL, '2026-02-10 17:17:49'),
(208, 36, 'schedule_show_additional_info', 'TEXT', '1', NULL, NULL, '2026-02-10 17:17:49'),
(209, 36, 'schedule_show_story_count', 'TEXT', '1', NULL, NULL, '2026-02-10 17:17:49'),
(210, 36, 'schedule_story_count_label', 'TEXT', 'Stories', NULL, NULL, '2026-02-10 17:17:49'),
(211, 36, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Discover', NULL, NULL, '2026-02-10 17:17:49'),
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
(225, 40, 'intro_body', 'TEXT', 'A Stroll Through History invites visitors to explore rich past of Haarlem on foot. Guided tour leads participants through historic streets and landmarks, including locations that played an important role in the city\'s cultural, social, and architectural development. The walks are offered in Dutch, English, and Chinese and are suitable for a wide audience.\r\n\r\nThe route has been carefully curated and prepared by local historians and guides to ensure an engaging, informative, and memorable experience. By combining historical facts with stories from the past, the event helps visitors better understand how Haarlem grew into the city it is today.\r\n\r\nMultiple time slots are available throughout the festival, with different ticket options to keep the event accessible for individuals and families. By joining A Stroll Through History, visitors not only discover Haarlem\'s landmarks but also connect with the city through the people, places, and moments that shaped it.', NULL, NULL, '2026-02-13 18:05:59'),
(226, 42, 'hero_main_title', 'HEADING', 'A STROLL THROUGH HISTORY', NULL, NULL, '2026-02-16 12:19:16'),
(227, 42, 'hero_button_primary', 'BUTTON_TEXT', 'Explore the tour', NULL, NULL, '2026-02-16 12:19:16'),
(228, 42, 'hero_button_primary_link', 'URL', '#route', NULL, NULL, '2026-03-09 23:06:39'),
(229, 42, 'hero_button_secondary', 'BUTTON_TEXT', 'Get tickets', NULL, NULL, '2026-02-16 12:19:16'),
(230, 42, 'hero_button_secondary_link', 'URL', '#tickets', NULL, NULL, '2026-03-09 23:06:39'),
(231, 45, 'gradient_heading', 'HEADING', 'Every street holds echoes of the past, shaped by the people who once walked there.', NULL, NULL, '2026-02-16 12:52:54'),
(232, 45, 'gradient_subheading', 'TEXT', 'Where history comes alive through places, paths, and people.', NULL, NULL, '2026-02-16 12:52:54'),
(233, 40, 'intro_heading', 'HEADING', 'Experience the living history of Haarlem', NULL, NULL, '2026-02-16 13:01:35'),
(234, 51, 'route_location1_name', 'TEXT', 'Church of St.Bavo', NULL, NULL, '2026-02-16 13:04:58'),
(235, 51, 'route_location1_description', 'TEXT', 'A monumental Gothic church famed for its towering nave and historic Müller organ once played by Mozart.', NULL, NULL, '2026-02-16 13:04:58'),
(236, 51, 'route_location2_name', 'TEXT', 'Grote Markt', NULL, NULL, '2026-02-16 13:06:02'),
(237, 51, 'route_location2_description', 'TEXT', 'A vibrant central square surrounded by landmark buildings and lively cafés;  the city\'s cultural heart.', NULL, NULL, '2026-02-16 13:06:02'),
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
(253, 51, 'route_map_image', 'URL', '/assets/Image/History/History-RouteMap.png', NULL, NULL, '2026-03-09 23:08:47'),
(254, 38, 'history_grotemarkt_name', 'TEXT', 'Grote Markt', NULL, NULL, '2026-02-16 13:21:46'),
(255, 38, 'history_grotemarkt_description', 'TEXT', 'The heart of the historic center of Haarlem.', NULL, NULL, '2026-02-16 13:21:46'),
(256, 38, 'history_grotemarkt_image', 'URL', '/assets/Image/History/History-GroteMarkt.png', NULL, NULL, '2026-03-09 23:08:47'),
(257, 38, 'history_amsterdamsepoort_name', 'TEXT', 'Amsterdamse Poort', NULL, NULL, '2026-02-16 13:31:17'),
(258, 38, 'history_amsterdamsepoort_description', 'TEXT', 'As the only remaining city gate.', NULL, NULL, '2026-02-16 13:31:17'),
(259, 38, 'history_amsterdamsepoort_image', 'URL', '/assets/Image/History/History-AmsterdamsePoort.png', NULL, NULL, '2026-03-09 23:08:47'),
(260, 38, 'history_molendeadriaan_name', 'TEXT', 'Molen De Adriaan', NULL, NULL, '2026-02-16 13:31:17'),
(261, 38, 'history_molendeadriaan_description', 'TEXT', 'A striking riverside windmill.', NULL, NULL, '2026-02-16 13:31:17'),
(262, 38, 'history_molendeadriaan_image', 'URL', '/assets/Image/History/History-MolenDeAdriaan.png', NULL, NULL, '2026-03-09 23:08:47'),
(263, 38, 'historical_locations_heading', 'HEADING', 'Read more about these locations', NULL, NULL, '2026-02-16 13:36:54'),
(264, 52, 'ticket_options_heading', 'HEADING', 'Your ticket options to join the experience', NULL, NULL, '2026-02-16 13:38:08'),
(265, 52, 'history_single_ticket_icon', 'URL', '/assets/Icons/History/single-ticket-icon.svg', NULL, NULL, '2026-02-16 13:49:05'),
(266, 52, 'history_pricing_single_title', 'TEXT', 'Single Ticket', NULL, NULL, '2026-02-16 13:49:05'),
(267, 53, 'history_pricing_single_price', 'TEXT', '€17.50', NULL, NULL, '2026-02-16 13:49:05'),
(268, 53, 'history_pricing_single_include1', 'TEXT', 'Per person', NULL, NULL, '2026-02-16 13:49:05'),
(269, 53, 'history_pricing_single_include2', 'TEXT', 'Includes one complimentary drink', NULL, NULL, '2026-02-16 13:49:05'),
(270, 53, 'history_pricing_single_include3', 'TEXT', '2.5 hour guided tour', NULL, NULL, '2026-02-16 13:49:05'),
(271, 52, 'history_group_ticket_icon', 'URL', '/assets/Icons/History/group-ticket-icon.svg', NULL, NULL, '2026-02-16 13:49:05'),
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
(289, 55, 'hero_button_primary_link', 'LINK', '#restaurants', NULL, NULL, '2026-02-16 16:31:14'),
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
(320, 60, 'restaurant_1_name', 'HEADING', 'Ratatouille', NULL, NULL, '2026-02-16 16:31:14'),
(321, 60, 'restaurant_1_cuisine', 'TEXT', 'French, fish and seafood, European', NULL, NULL, '2026-02-16 16:31:14'),
(322, 60, 'restaurant_1_address', 'TEXT', 'Spaarne 96, 2011 CL Haarlem', NULL, NULL, '2026-02-16 16:31:14'),
(323, 60, 'restaurant_1_description', 'TEXT', 'Refined dining with a warm touch, where seasonal ingredients and creative flavors come together for an elegant experience.', NULL, NULL, '2026-02-16 16:31:14'),
(324, 60, 'restaurant_1_distance_text', 'TEXT', '5 min walk from Patronaat', NULL, NULL, '2026-02-16 16:31:14'),
(325, 60, 'restaurant_1_rating', 'TEXT', '4', NULL, NULL, '2026-02-16 16:31:14'),
(326, 60, 'restaurant_1_price', 'TEXT', '€€€', NULL, NULL, '2026-02-16 16:31:14'),
(327, 60, 'restaurant_1_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-Ratatouille-card.png', NULL, NULL, '2026-02-16 16:31:14'),
(328, 60, 'restaurant_1_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, '2026-02-16 16:31:14'),
(329, 60, 'restaurant_1_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, '2026-02-16 16:31:14'),
(330, 60, 'restaurant_2_name', 'HEADING', 'Urban Frenchy Bistro Toujours', NULL, NULL, '2026-02-16 16:31:14'),
(331, 60, 'restaurant_2_cuisine', 'TEXT', 'Dutch, fish and seafood, European', NULL, NULL, '2026-02-16 16:31:14'),
(332, 60, 'restaurant_2_address', 'TEXT', 'Oude Groenmarkt 10-12, 2011 HL, Haarlem', NULL, NULL, '2026-02-16 16:31:14'),
(333, 60, 'restaurant_2_description', 'TEXT', 'A cozy city bistro focused on seafood and comforting dishes in a lively central setting.', NULL, NULL, '2026-02-16 16:31:14'),
(334, 60, 'restaurant_2_distance_text', 'TEXT', '2 min walk from Jopenkerk', NULL, NULL, '2026-02-16 16:31:14'),
(335, 60, 'restaurant_2_rating', 'TEXT', '3', NULL, NULL, '2026-02-16 16:31:14'),
(336, 60, 'restaurant_2_price', 'TEXT', '€€', NULL, NULL, '2026-02-16 16:31:14'),
(337, 60, 'restaurant_2_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-UrbanFrenchy.png', NULL, NULL, '2026-02-16 16:31:14'),
(338, 60, 'restaurant_2_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, '2026-02-16 16:31:14'),
(339, 60, 'restaurant_2_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, '2026-02-16 16:31:14'),
(340, 60, 'restaurant_3_name', 'HEADING', 'New Vegas', NULL, NULL, '2026-02-16 16:31:14'),
(341, 60, 'restaurant_3_cuisine', 'TEXT', 'Vegan', NULL, NULL, '2026-02-16 16:31:14'),
(342, 60, 'restaurant_3_address', 'TEXT', 'Koningstraat 5, 2011 TB Haarlem', NULL, NULL, '2026-02-16 16:31:14'),
(343, 60, 'restaurant_3_description', 'TEXT', 'A casual spot with an international feel, offering familiar dishes and vegetarian options right in the city center.', NULL, NULL, '2026-02-16 16:31:14'),
(344, 60, 'restaurant_3_distance_text', 'TEXT', '5 min walk from Patronaat', NULL, NULL, '2026-02-16 16:31:14'),
(345, 60, 'restaurant_3_rating', 'TEXT', '3', NULL, NULL, '2026-02-16 16:31:14'),
(346, 60, 'restaurant_3_price', 'TEXT', '€€', NULL, NULL, '2026-02-16 16:31:14'),
(347, 60, 'restaurant_3_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-NewVegas-card.jpg', NULL, NULL, '2026-02-16 16:31:14'),
(348, 60, 'restaurant_3_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, '2026-02-16 16:31:14'),
(349, 60, 'restaurant_3_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, '2026-02-16 16:31:14'),
(350, 60, 'restaurant_4_name', 'HEADING', 'Grand Cafe Brinkman', NULL, NULL, '2026-02-16 16:31:14'),
(351, 60, 'restaurant_4_cuisine', 'TEXT', 'Dutch, European, Modern', NULL, NULL, '2026-02-16 16:31:14'),
(352, 60, 'restaurant_4_address', 'TEXT', 'Grote Markt 13, 2011 RC, Haarlem', NULL, NULL, '2026-02-16 16:31:14'),
(353, 60, 'restaurant_4_description', 'TEXT', 'A classic grand cafe on Haarlem\'s main square, serving familiar European dishes in the heart of the festival buzz.', NULL, NULL, '2026-02-16 16:31:14'),
(354, 60, 'restaurant_4_distance_text', 'TEXT', 'Located directly on Grote Markt', NULL, NULL, '2026-02-16 16:31:14'),
(355, 60, 'restaurant_4_rating', 'TEXT', '3', NULL, NULL, '2026-02-16 16:31:14'),
(356, 60, 'restaurant_4_price', 'TEXT', '€€', NULL, NULL, '2026-02-16 16:31:14'),
(357, 60, 'restaurant_4_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-CafeDeBrinkman-card.png', NULL, NULL, '2026-02-16 16:31:14'),
(358, 60, 'restaurant_4_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, '2026-02-16 16:31:14'),
(359, 60, 'restaurant_4_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, '2026-02-16 16:31:14'),
(360, 60, 'restaurant_5_name', 'HEADING', 'Restaurant ML', NULL, NULL, '2026-02-16 16:31:14'),
(361, 60, 'restaurant_5_cuisine', 'TEXT', 'Dutch, fish and seafood, European', NULL, NULL, '2026-02-16 16:31:14'),
(362, 60, 'restaurant_5_address', 'TEXT', 'Kleine Houtstraat 70, 2011 DR Haarlem', NULL, NULL, '2026-02-16 16:31:14'),
(363, 60, 'restaurant_5_description', 'TEXT', 'A modern fine-dining restaurant known for a refined yet welcoming atmosphere.', NULL, NULL, '2026-02-16 16:31:14'),
(364, 60, 'restaurant_5_distance_text', 'TEXT', '12 min walk from Slachthuis', NULL, NULL, '2026-02-16 16:31:14'),
(365, 60, 'restaurant_5_rating', 'TEXT', '4', NULL, NULL, '2026-02-16 16:31:14'),
(366, 60, 'restaurant_5_price', 'TEXT', '€€€', NULL, NULL, '2026-02-16 16:31:14'),
(367, 60, 'restaurant_5_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-ML-card.png', NULL, NULL, '2026-02-16 16:31:14'),
(368, 60, 'restaurant_5_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, '2026-02-16 16:31:14'),
(369, 60, 'restaurant_5_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, '2026-02-16 16:31:14'),
(370, 60, 'restaurant_6_name', 'HEADING', 'Cafe de Roemer', NULL, NULL, '2026-02-16 16:31:14'),
(371, 60, 'restaurant_6_cuisine', 'TEXT', 'Dutch, fish and seafood, European', NULL, NULL, '2026-02-16 16:31:14'),
(372, 60, 'restaurant_6_address', 'TEXT', 'Botermarkt 17, 2011 XL Haarlem', NULL, NULL, '2026-02-16 16:31:14'),
(373, 60, 'restaurant_6_description', 'TEXT', 'A cozy neighborhood cafe serving honest food and classic flavors in a relaxed and friendly setting.', NULL, NULL, '2026-02-16 16:31:14'),
(374, 60, 'restaurant_6_distance_text', 'TEXT', '7 min walk from Puncher Comedy Club', NULL, NULL, '2026-02-16 16:31:14'),
(375, 60, 'restaurant_6_rating', 'TEXT', '4', NULL, NULL, '2026-02-16 16:31:14'),
(376, 60, 'restaurant_6_price', 'TEXT', '€€€', NULL, NULL, '2026-02-16 16:31:14'),
(377, 60, 'restaurant_6_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-deRoemer-card.png', NULL, NULL, '2026-02-16 16:31:14'),
(378, 60, 'restaurant_6_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, '2026-02-16 16:31:14'),
(379, 60, 'restaurant_6_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, '2026-02-16 16:31:14'),
(380, 60, 'restaurant_7_name', 'HEADING', 'Restaurant Fris', NULL, NULL, '2026-02-16 16:31:14'),
(381, 60, 'restaurant_7_cuisine', 'TEXT', 'Dutch, French, European', NULL, NULL, '2026-02-16 16:31:14'),
(382, 60, 'restaurant_7_address', 'TEXT', 'Twijnderslaan 7, 2012 BG, Haarlem', NULL, NULL, '2026-02-16 16:31:14'),
(383, 60, 'restaurant_7_description', 'TEXT', 'A contemporary restaurant focused on seasonal ingredients, thoughtful cooking, and elegant flavors without the formality.', NULL, NULL, '2026-02-16 16:31:14'),
(384, 60, 'restaurant_7_distance_text', 'TEXT', '10 min walk from Patronaat', NULL, NULL, '2026-02-16 16:31:14'),
(385, 60, 'restaurant_7_rating', 'TEXT', '4', NULL, NULL, '2026-02-16 16:31:14'),
(386, 60, 'restaurant_7_price', 'TEXT', '€€€', NULL, NULL, '2026-02-16 16:31:14'),
(387, 60, 'restaurant_7_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-Fris-card.png', NULL, NULL, '2026-02-16 16:31:14'),
(388, 60, 'restaurant_7_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, '2026-02-16 16:31:14'),
(389, 60, 'restaurant_7_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, '2026-02-16 16:31:14'),
(390, 61, 'about_heading', 'HEADING', 'Winnie de Poeh (4+)', NULL, NULL, '2026-03-09 15:46:45'),
(391, 61, 'about_body', 'HTML', NULL, '<p>An enchanting storytelling experience for children aged 4 and above, bringing the beloved story of Winnie the Pooh to life in the heart of Haarlem.</p>', NULL, '2026-03-09 15:46:45'),
(392, 61, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(393, 61, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/WinnieThePoohHeader.png', NULL, NULL, '2026-03-09 15:46:45'),
(394, 61, 'highlight_1_title', 'TEXT', 'A Story for All Ages', NULL, NULL, '2026-03-09 15:46:45'),
(395, 61, 'highlight_1_description', 'TEXT', 'A classic tale reimagined for young and old alike.', NULL, NULL, '2026-03-09 15:46:45'),
(396, 61, 'highlight_1_image', 'IMAGE_PATH', 'A giant flag near Church of St. Bavo at Grote Markt', NULL, NULL, '2026-03-09 20:41:13'),
(397, 61, 'highlight_2_title', 'TEXT', '/assets/Image/History/History-hero.png', NULL, NULL, '2026-03-09 23:02:47'),
(398, 61, 'highlight_2_description', 'TEXT', 'Experience the magic of oral storytelling in an intimate setting.', NULL, NULL, '2026-03-09 15:46:45'),
(399, 61, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(400, 61, 'highlight_3_title', 'TEXT', 'Interactive & Fun', NULL, NULL, '2026-03-09 15:46:45'),
(401, 61, 'highlight_3_description', 'TEXT', 'Children are invited to participate and use their imagination.', NULL, NULL, '2026-03-09 15:46:45'),
(402, 61, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/pig.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(403, 61, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NULL, NULL, '2026-03-09 15:46:45');
INSERT INTO `CmsItem` (`CmsItemId`, `CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(404, 61, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/WinnieThePoohHeader.png', NULL, NULL, '2026-03-09 15:46:45'),
(405, 61, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(406, 61, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/pig.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(407, 61, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:46:45'),
(408, 61, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:46:45'),
(409, 62, 'about_heading', 'HEADING', 'Omdenken Podcast', NULL, NULL, '2026-03-09 15:51:55'),
(410, 62, 'about_body', 'HTML', NULL, '<p>A live podcast recording with audience participation. Thought-provoking conversations that challenge conventional thinking and inspire new perspectives.</p>', NULL, '2026-03-09 15:51:55'),
(411, 62, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(412, 62, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(413, 62, 'highlight_1_title', 'TEXT', 'Live Recording', NULL, NULL, '2026-03-09 15:51:55'),
(414, 62, 'highlight_1_description', 'TEXT', 'Be part of a live podcast recording in front of an audience.', NULL, NULL, '2026-03-09 15:51:55'),
(415, 62, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(416, 62, 'highlight_2_title', 'TEXT', 'Audience Participation', NULL, NULL, '2026-03-09 15:51:55'),
(417, 62, 'highlight_2_description', 'TEXT', 'Your questions and insights shape the conversation.', NULL, NULL, '2026-03-09 15:51:55'),
(418, 62, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(419, 62, 'highlight_3_title', 'TEXT', 'New Perspectives', NULL, NULL, '2026-03-09 15:51:55'),
(420, 62, 'highlight_3_description', 'TEXT', 'Discover how reframing challenges can lead to creative solutions.', NULL, NULL, '2026-03-09 15:51:55'),
(421, 62, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(422, 62, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(423, 62, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(424, 62, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(425, 62, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(426, 62, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(427, 62, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(428, 63, 'about_heading', 'HEADING', 'About Buurderij Haarlem', NULL, NULL, '2026-03-09 15:51:55'),
(429, 63, 'about_body', 'HTML', NULL, '<p>Buurderij Haarlem is a local food community where neighbours order directly from farmers and small producers in the region. What started as a simple idea grew into a place where food is not just bought, but shared as a story.</p><p>During this storytelling session, the audience is taken behind the scenes of Buurderij Haarlem. The story explains how local entrepreneurs, volunteers, and residents work together to create a system based on trust, transparency, and respect for nature.</p>', NULL, '2026-03-09 15:51:55'),
(430, 63, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(431, 63, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(432, 63, 'highlight_1_title', 'TEXT', 'From Farmer to Neighbour', NULL, NULL, '2026-03-09 15:51:55'),
(433, 63, 'highlight_1_description', 'TEXT', 'A personal story about how local farmers connect directly with Haarlem residents, focusing on trust, fair pricing, and the journey food takes from land to table.', NULL, NULL, '2026-03-09 15:51:55'),
(434, 63, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(435, 63, 'highlight_2_title', 'TEXT', 'Building a Local Food Community', NULL, NULL, '2026-03-09 15:51:55'),
(436, 63, 'highlight_2_description', 'TEXT', 'How Buurderij Haarlem became more than a marketplace - weekly meetups created friendships and a shared sense of responsibility for local food.', NULL, NULL, '2026-03-09 15:51:55'),
(437, 63, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(438, 63, 'highlight_3_title', 'TEXT', 'Food with a Future', NULL, NULL, '2026-03-09 15:51:55'),
(439, 63, 'highlight_3_description', 'TEXT', 'How Buurderij Haarlem supports seasonal food, reduces waste, and encourages conscious consumption for future generations.', NULL, NULL, '2026-03-09 15:51:55'),
(440, 63, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(441, 63, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(442, 63, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(443, 63, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/where-stories-come-alive.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(444, 63, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(445, 63, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(446, 63, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(447, 64, 'about_heading', 'HEADING', 'Corrie voor kinderen', NULL, NULL, '2026-03-09 15:51:55'),
(448, 64, 'about_body', 'HTML', NULL, '<p>A family storytelling experience that brings the story of Corrie to life for young audiences with engaging performances and vivid imagery.</p>', NULL, '2026-03-09 15:51:55'),
(449, 64, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(450, 64, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(451, 64, 'highlight_1_title', 'TEXT', 'Stories for Every Child', NULL, NULL, '2026-03-09 15:51:55'),
(452, 64, 'highlight_1_description', 'TEXT', 'An accessible and joyful storytelling session designed with children in mind.', NULL, NULL, '2026-03-09 15:51:55'),
(453, 64, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(454, 64, 'highlight_2_title', 'TEXT', 'Imagination in Action', NULL, NULL, '2026-03-09 15:51:55'),
(455, 64, 'highlight_2_description', 'TEXT', 'Watch as the performer brings characters to life through voice, gesture, and storytelling.', NULL, NULL, '2026-03-09 15:51:55'),
(456, 64, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(457, 64, 'highlight_3_title', 'TEXT', 'A Shared Experience', NULL, NULL, '2026-03-09 15:51:55'),
(458, 64, 'highlight_3_description', 'TEXT', 'A session that brings families and communities together around shared stories.', NULL, NULL, '2026-03-09 15:51:55'),
(459, 64, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(460, 64, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(461, 64, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(462, 64, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(463, 64, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(464, 64, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(465, 64, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(466, 65, 'about_heading', 'HEADING', 'Winnaars verhalenvertel wedstrijd', NULL, NULL, '2026-03-09 15:51:55'),
(467, 65, 'about_body', 'HTML', NULL, '<p>The winners of this year verhalenvertel competition share their winning stories about Haarlem, its people, and its spirit.</p>', NULL, '2026-03-09 15:51:55'),
(468, 65, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(469, 65, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(470, 65, 'highlight_1_title', 'TEXT', 'Competition Winners', NULL, NULL, '2026-03-09 15:51:55'),
(471, 65, 'highlight_1_description', 'TEXT', 'Hear from the storytellers who won the Haarlem storytelling competition.', NULL, NULL, '2026-03-09 15:51:55'),
(472, 65, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(473, 65, 'highlight_2_title', 'TEXT', 'Stories About Haarlem', NULL, NULL, '2026-03-09 15:51:55'),
(474, 65, 'highlight_2_description', 'TEXT', 'Each winner tells a story rooted in the city culture and everyday life.', NULL, NULL, '2026-03-09 15:51:55'),
(475, 65, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(476, 65, 'highlight_3_title', 'TEXT', 'Community Voice', NULL, NULL, '2026-03-09 15:51:55'),
(477, 65, 'highlight_3_description', 'TEXT', 'The competition celebrates storytelling as a way to strengthen community bonds.', NULL, NULL, '2026-03-09 15:51:55'),
(478, 65, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(479, 65, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(480, 65, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(481, 65, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(482, 65, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(483, 65, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(484, 65, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(485, 66, 'about_heading', 'HEADING', 'Het verhaal van de Oeserzwammerij', NULL, NULL, '2026-03-09 15:51:55'),
(486, 66, 'about_body', 'HTML', NULL, '<p>An intimate storytelling session about the Oeserzwammerij, a local initiative growing oyster mushrooms sustainably in Haarlem.</p>', NULL, '2026-03-09 15:51:55'),
(487, 66, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(488, 66, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(489, 66, 'highlight_1_title', 'TEXT', 'Sustainable Innovation', NULL, NULL, '2026-03-09 15:51:55'),
(490, 66, 'highlight_1_description', 'TEXT', 'Learn how local entrepreneurs are growing food sustainably in the city.', NULL, NULL, '2026-03-09 15:51:55'),
(491, 66, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(492, 66, 'highlight_2_title', 'TEXT', 'Community Impact', NULL, NULL, '2026-03-09 15:51:55'),
(493, 66, 'highlight_2_description', 'TEXT', 'The Oeserzwammerij connects growers, volunteers, and consumers.', NULL, NULL, '2026-03-09 15:51:55'),
(494, 66, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(495, 66, 'highlight_3_title', 'TEXT', 'From Waste to Food', NULL, NULL, '2026-03-09 15:51:55'),
(496, 66, 'highlight_3_description', 'TEXT', 'Using coffee grounds and organic waste to grow nutritious mushrooms.', NULL, NULL, '2026-03-09 15:51:55'),
(497, 66, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(498, 66, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(499, 66, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(500, 66, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(501, 66, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(502, 66, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(503, 66, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(504, 67, 'about_heading', 'HEADING', 'Flip Thinking Podcast', NULL, NULL, '2026-03-09 15:51:55'),
(505, 67, 'about_body', 'HTML', NULL, '<p>A live recording of the popular Flip Thinking podcast, where hosts and guests explore the art of reframing problems into opportunities.</p>', NULL, '2026-03-09 15:51:55'),
(506, 67, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(507, 67, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(508, 67, 'highlight_1_title', 'TEXT', 'Reframe Your World', NULL, NULL, '2026-03-09 15:51:55'),
(509, 67, 'highlight_1_description', 'TEXT', 'Learn practical techniques for turning obstacles into opportunities.', NULL, NULL, '2026-03-09 15:51:55'),
(510, 67, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(511, 67, 'highlight_2_title', 'TEXT', 'Live Podcast Recording', NULL, NULL, '2026-03-09 15:51:55'),
(512, 67, 'highlight_2_description', 'TEXT', 'Experience the magic of a podcast recorded before a live audience.', NULL, NULL, '2026-03-09 15:51:55'),
(513, 67, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(514, 67, 'highlight_3_title', 'TEXT', 'Q and A with Audience', NULL, NULL, '2026-03-09 15:51:55'),
(515, 67, 'highlight_3_description', 'TEXT', 'After the recording, guests take questions from the audience.', NULL, NULL, '2026-03-09 15:51:55'),
(516, 67, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(517, 67, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(518, 67, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(519, 67, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(520, 67, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(521, 67, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(522, 67, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(523, 68, 'about_heading', 'HEADING', 'Meneer Anansi', NULL, NULL, '2026-03-09 15:51:55'),
(524, 68, 'about_body', 'HTML', NULL, '<p>Meneer Anansi brings the West African trickster spider to life in Dutch, weaving together folklore, humour, and wisdom in a totally original way.</p>', NULL, '2026-03-09 15:51:55'),
(525, 68, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-03-09 15:51:55'),
(526, 68, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(527, 68, 'highlight_1_title', 'TEXT', 'Anansi the Trickster', NULL, NULL, '2026-03-09 15:51:55'),
(528, 68, 'highlight_1_description', 'TEXT', 'The beloved spider of West African folklore comes alive on stage.', NULL, NULL, '2026-03-09 15:51:55'),
(529, 68, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-03-09 15:51:55'),
(530, 68, 'highlight_2_title', 'TEXT', 'Dutch Folk and World Heritage', NULL, NULL, '2026-03-09 15:51:55'),
(531, 68, 'highlight_2_description', 'TEXT', 'Ancient Anansi stories told in Dutch, bridging cultures and generations.', NULL, NULL, '2026-03-09 15:51:55'),
(532, 68, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(533, 68, 'highlight_3_title', 'TEXT', 'Humour and Wisdom', NULL, NULL, '2026-03-09 15:51:55'),
(534, 68, 'highlight_3_description', 'TEXT', 'Each Anansi story carries a lesson wrapped in laughter and surprise.', NULL, NULL, '2026-03-09 15:51:55'),
(535, 68, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(536, 68, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-03-09 15:51:55'),
(537, 68, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(538, 68, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(539, 68, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(540, 68, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(541, 68, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(542, 69, 'about_heading', 'HEADING', 'Mister Anansi', NULL, NULL, '2026-03-09 15:51:55'),
(543, 69, 'about_body', 'HTML', NULL, '<p>Mister Anansi is the English-language version of the beloved spider trickster story, perfect for international visitors and English-speaking families.</p>', NULL, '2026-03-09 15:51:55'),
(544, 69, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-03-09 15:51:55'),
(545, 69, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(546, 69, 'highlight_1_title', 'TEXT', 'In English', NULL, NULL, '2026-03-09 15:51:55'),
(547, 69, 'highlight_1_description', 'TEXT', 'The Anansi story performed in English for international audiences.', NULL, NULL, '2026-03-09 15:51:55'),
(548, 69, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-03-09 15:51:55'),
(549, 69, 'highlight_2_title', 'TEXT', 'Universal Themes', NULL, NULL, '2026-03-09 15:51:55'),
(550, 69, 'highlight_2_description', 'TEXT', 'Stories of cunning, community, and creativity that transcend cultures.', NULL, NULL, '2026-03-09 15:51:55'),
(551, 69, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(552, 69, 'highlight_3_title', 'TEXT', 'World Folklore on Stage', NULL, NULL, '2026-03-09 15:51:55'),
(553, 69, 'highlight_3_description', 'TEXT', 'Experience the rich tradition of West African oral storytelling at Haarlem Festival.', NULL, NULL, '2026-03-09 15:51:55'),
(554, 69, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(555, 69, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NULL, NULL, '2026-03-09 15:51:55'),
(556, 69, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(557, 69, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(558, 69, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(559, 69, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(560, 69, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(561, 70, 'about_heading', 'HEADING', 'Podcastlast Haarlem Special', NULL, NULL, '2026-03-09 15:51:55'),
(562, 70, 'about_body', 'HTML', NULL, '<p>A special live edition of the Podcastlast podcast, recorded at the Haarlem Festival with candid conversations and local insights.</p>', NULL, '2026-03-09 15:51:55'),
(563, 70, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(564, 70, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(565, 70, 'highlight_1_title', 'TEXT', 'Haarlem Special Edition', NULL, NULL, '2026-03-09 15:51:55'),
(566, 70, 'highlight_1_description', 'TEXT', 'A one-of-a-kind live episode recorded exclusively at the festival.', NULL, NULL, '2026-03-09 15:51:55'),
(567, 70, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(568, 70, 'highlight_2_title', 'TEXT', 'Local Conversations', NULL, NULL, '2026-03-09 15:51:55'),
(569, 70, 'highlight_2_description', 'TEXT', 'Featuring guests from Haarlem creative and cultural scene.', NULL, NULL, '2026-03-09 15:51:55'),
(570, 70, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(571, 70, 'highlight_3_title', 'TEXT', 'Live Audience Experience', NULL, NULL, '2026-03-09 15:51:55'),
(572, 70, 'highlight_3_description', 'TEXT', 'Be part of the recording and feel the energy of a live podcast taping.', NULL, NULL, '2026-03-09 15:51:55'),
(573, 70, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(574, 70, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(575, 70, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(576, 70, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(577, 70, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(578, 70, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(579, 70, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(580, 71, 'about_heading', 'HEADING', 'De geschiedenis van familie ten Boom', NULL, NULL, '2026-03-09 15:51:55'),
(581, 71, 'about_body', 'HTML', NULL, '<p>The remarkable story of the ten Boom family, whose Haarlem home became a refuge during World War II, sheltering Jewish families at great personal risk.</p>', NULL, '2026-03-09 15:51:55'),
(582, 71, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(583, 71, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(584, 71, 'highlight_1_title', 'TEXT', 'A Story of Courage', NULL, NULL, '2026-03-09 15:51:55'),
(585, 71, 'highlight_1_description', 'TEXT', 'The ten Boom family risked everything to shelter Jews during the occupation.', NULL, NULL, '2026-03-09 15:51:55'),
(586, 71, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(587, 71, 'highlight_2_title', 'TEXT', 'Haarlem in History', NULL, NULL, '2026-03-09 15:51:55'),
(588, 71, 'highlight_2_description', 'TEXT', 'Explore how one family home became a symbol of resistance and compassion.', NULL, NULL, '2026-03-09 15:51:55'),
(589, 71, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(590, 71, 'highlight_3_title', 'TEXT', 'Legacy and Memory', NULL, NULL, '2026-03-09 15:51:55'),
(591, 71, 'highlight_3_description', 'TEXT', 'Their story lives on as an inspiration for future generations.', NULL, NULL, '2026-03-09 15:51:55'),
(592, 71, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(593, 71, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(594, 71, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(595, 71, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(596, 71, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(597, 71, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(598, 71, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(599, 72, 'about_heading', 'HEADING', 'The History of the Ten Boom Family', NULL, NULL, '2026-03-09 15:51:55'),
(600, 72, 'about_body', 'HTML', NULL, '<p>The story of the ten Boom family told in English. During WWII they used their Haarlem shop to hide Jewish families, leaving a lasting legacy on the city.</p>', NULL, '2026-03-09 15:51:55'),
(601, 72, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(602, 72, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(603, 72, 'highlight_1_title', 'TEXT', 'In English', NULL, NULL, '2026-03-09 15:51:55'),
(604, 72, 'highlight_1_description', 'TEXT', 'The ten Boom family story told for international visitors.', NULL, NULL, '2026-03-09 15:51:55'),
(605, 72, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(606, 72, 'highlight_2_title', 'TEXT', 'Wartime Haarlem', NULL, NULL, '2026-03-09 15:51:55'),
(607, 72, 'highlight_2_description', 'TEXT', 'Step back in time to a Haarlem that faced unimaginable choices.', NULL, NULL, '2026-03-09 15:51:55'),
(608, 72, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(609, 72, 'highlight_3_title', 'TEXT', 'Hope and Resilience', NULL, NULL, '2026-03-09 15:51:55'),
(610, 72, 'highlight_3_description', 'TEXT', 'A story about human courage that resonates across generations and borders.', NULL, NULL, '2026-03-09 15:51:55'),
(611, 72, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(612, 72, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(613, 72, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(614, 72, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(615, 72, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(616, 72, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(617, 72, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55'),
(618, 73, 'about_heading', 'HEADING', 'Winners of the Storytelling Competition', NULL, NULL, '2026-03-09 15:51:55'),
(619, 73, 'about_body', 'HTML', NULL, '<p>The best storytellers from this year competition take the stage to share their winning tales about Haarlem in English, selected for creativity, emotional depth, and unique perspective.</p>', NULL, '2026-03-09 15:51:55'),
(620, 73, 'about_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(621, 73, 'about_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(622, 73, 'highlight_1_title', 'TEXT', 'Competition Winners', NULL, NULL, '2026-03-09 15:51:55'),
(623, 73, 'highlight_1_description', 'TEXT', 'The English-language winners of the storytelling competition share their best work.', NULL, NULL, '2026-03-09 15:51:55'),
(624, 73, 'highlight_1_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(625, 73, 'highlight_2_title', 'TEXT', 'Stories About Haarlem', NULL, NULL, '2026-03-09 15:51:55'),
(626, 73, 'highlight_2_description', 'TEXT', 'Each story is rooted in the life and character of the city.', NULL, NULL, '2026-03-09 15:51:55'),
(627, 73, 'highlight_2_image', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(628, 73, 'highlight_3_title', 'TEXT', 'New Voices', NULL, NULL, '2026-03-09 15:51:55'),
(629, 73, 'highlight_3_description', 'TEXT', 'Discover emerging storytelling talent from Haarlem and beyond.', NULL, NULL, '2026-03-09 15:51:55'),
(630, 73, 'highlight_3_image', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(631, 73, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(632, 73, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(633, 73, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(634, 73, 'gallery_image_4', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(635, 73, 'gallery_image_5', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NULL, NULL, '2026-03-09 15:51:55'),
(636, 73, 'video_url', 'TEXT', '', NULL, NULL, '2026-03-09 15:51:55');

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
(1, 'home', 'Haarlem Festival Homepage'),
(2, 'storytelling', 'Stories in Haarlem'),
(3, 'history', 'A Stroll Through History'),
(5, 'jazz', 'Haarlem Jazz Festival'),
(6, 'restaurant', 'Yummy! Restaurant Experience'),
(7, 'storytelling-detail', 'Storytelling Detail Pages');

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
(68, 7, 'event_41'),
(69, 7, 'event_42'),
(70, 7, 'event_43'),
(71, 7, 'event_44'),
(72, 7, 'event_45'),
(73, 7, 'event_46');

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
(1, 1, 'Gumbo Kings', 'Live jazz performance by Gumbo Kings', '<p></p>', NULL, 2, 1, NULL, 1, '2026-02-06 15:30:32'),
(2, 1, 'Evolve', 'Live jazz performance by Evolve', '<p></p>', NULL, 2, 2, NULL, 1, '2026-02-06 15:30:32'),
(3, 1, 'Ntjam Rosie', 'Live jazz performance by Ntjam Rosie', '<p></p>', NULL, 1, 3, NULL, 1, '2026-02-06 15:30:32'),
(4, 1, 'Wicked Jazz Sounds', 'Live jazz performance by Wicked Jazz Sounds', '<p></p>', NULL, 2, 4, NULL, 1, '2026-02-06 15:30:32'),
(5, 1, 'Wouter Hamel', 'Live jazz performance by Wouter Hamel', '<p></p>', NULL, 1, 5, NULL, 1, '2026-02-06 15:30:32'),
(6, 1, 'Jonna Frazer', 'Live jazz performance by Jonna Frazer', '<p></p>', NULL, 1, 6, NULL, 1, '2026-02-06 15:30:32'),
(7, 1, 'Karsu', 'Live jazz performance by Karsu', '<p></p>', NULL, 1, 7, NULL, 1, '2026-02-06 15:30:32'),
(8, 1, 'Uncle Sue', 'Live jazz performance by Uncle Sue', '<p></p>', NULL, 1, 8, NULL, 1, '2026-02-06 15:30:32'),
(9, 1, 'Chris Allen', 'Live jazz performance by Chris Allen', '<p></p>', NULL, NULL, 9, NULL, 1, '2026-02-06 15:30:32'),
(10, 1, 'Myles Sanko', 'Live jazz performance by Myles Sanko', '<p></p>', NULL, 1, 10, NULL, 1, '2026-02-06 15:30:32'),
(11, 1, 'Ilse Huizinga', 'Live jazz performance by Ilse Huizinga', '<p></p>', NULL, 1, 11, NULL, 1, '2026-02-06 15:30:32'),
(12, 1, 'Eric Vloeimans and Hotspot!', 'Live jazz performance by Eric Vloeimans and Hotspot!', '<p></p>', NULL, 1, 12, NULL, 1, '2026-02-06 15:30:32'),
(13, 1, 'Gare du Nord', 'Live jazz performance by Gare du Nord', '<p></p>', NULL, 2, 13, NULL, 1, '2026-02-06 15:30:32'),
(14, 1, 'Rilan & The Bombadiers', 'Live jazz performance by Rilan & The Bombadiers', '<p></p>', NULL, 1, 14, NULL, 1, '2026-02-06 15:30:32'),
(15, 1, 'Soul Six', 'Live jazz performance by Soul Six', '<p></p>', NULL, 1, 15, NULL, 1, '2026-02-06 15:30:32'),
(16, 1, 'Han Bennink', 'Live jazz performance by Han Bennink', '<p></p>', NULL, 1, 16, NULL, 1, '2026-02-06 15:30:32'),
(17, 1, 'The Nordanians', 'Live jazz performance by The Nordanians', '<p></p>', NULL, 2, 17, NULL, 1, '2026-02-06 15:30:32'),
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
(33, 3, 'Haarlem History Walking Tour', 'Guided walking tour through historic Haarlem. Due to the nature of this walk, participants must be a minimum of 12 years old and no strollers are allowed.', '<p></p>', NULL, NULL, NULL, NULL, 1, '2026-02-06 15:30:32'),
(34, 4, 'Winnie de Poeh (4+)', 'Stories for the whole family', '<p></p>', NULL, NULL, NULL, NULL, 1, '2026-02-06 15:30:32'),
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
(53, 5, 'Urban Frenchy Bistro Toujours - Festival Dinner', 'Dutch, fish and seafood, European cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 7, 1, '2026-02-06 15:30:32'),
(54, 4, 'Testing', 'This is just a test', '<p></p>', NULL, NULL, NULL, NULL, 0, '2026-02-10 20:54:41'),
(55, 4, 'test', 'test', 'teste', NULL, 9, NULL, NULL, 0, '2026-02-11 00:17:47');

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

INSERT INTO `EventSession` (`EventSessionId`, `EventId`, `StartDateTime`, `EndDateTime`, `CapacityTotal`, `CapacitySingleTicketLimit`, `SeatsAvailable`, `SoldSingleTickets`, `SoldReservedSeats`, `HallName`, `SessionType`, `DurationMinutes`, `LanguageCode`, `MinAge`, `MaxAge`, `ReservationRequired`, `IsFree`, `Notes`, `HistoryTicketLabel`, `CtaLabel`, `CtaUrl`, `IsCancelled`, `CreatedAtUtc`, `IsActive`) VALUES
(1, 1, '2026-07-23 18:00:00', '2026-07-23 19:00:00', 300, 270, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €35,00, All-Access pass for Thu, Fri, Sat: €80,00.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(2, 2, '2026-07-23 19:30:00', '2026-07-23 20:30:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(3, 3, '2026-07-23 21:00:00', '2026-07-23 22:00:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(4, 4, '2026-07-23 18:00:00', '2026-07-23 19:00:00', 200, 180, NULL, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(5, 5, '2026-07-23 19:30:00', '2026-07-23 20:30:00', 200, 200, NULL, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(6, 6, '2026-07-23 21:00:00', '2026-07-23 22:00:00', 200, 200, NULL, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(7, 7, '2026-07-24 18:00:00', '2026-07-24 19:00:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €35,00, All-Access pass for Thu, Fri, Sat: €80,00.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(8, 8, '2026-07-24 19:30:00', '2026-07-24 20:30:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(9, 9, '2026-07-24 21:00:00', '2026-07-24 22:00:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(10, 10, '2026-07-24 18:00:00', '2026-07-24 19:00:00', 200, 200, NULL, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(11, 11, '2026-07-24 19:30:00', '2026-07-24 20:30:00', 200, 200, NULL, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(12, 12, '2026-07-24 21:00:00', '2026-07-24 22:00:00', 200, 200, NULL, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(13, 13, '2026-07-25 18:00:00', '2026-07-25 19:00:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €35,00, All-Access pass for Thu, Fri, Sat: €80,00.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(14, 14, '2026-07-25 19:30:00', '2026-07-25 20:30:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(15, 15, '2026-07-25 21:00:00', '2026-07-25 22:00:00', 300, 300, NULL, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(16, 16, '2026-07-25 18:00:00', '2026-07-25 19:00:00', 150, 150, NULL, 0, 0, 'Third Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(17, 17, '2026-07-25 19:30:00', '2026-07-25 20:30:00', 150, 150, NULL, 0, 0, 'Third Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(18, 18, '2026-07-25 21:00:00', '2026-07-25 22:00:00', 150, 150, NULL, 0, 0, 'Third Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(19, 19, '2026-07-26 15:00:00', '2026-07-26 16:00:00', 300, 300, NULL, 0, 0, 'Outdoor Stage', 'Live', 60, NULL, NULL, NULL, 0, 1, 'Free for all visitors. No reservation needed.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(20, 4, '2026-07-26 16:00:00', '2026-07-26 17:00:00', 300, 300, NULL, 0, 0, 'Outdoor Stage', 'Live', 60, NULL, NULL, NULL, 0, 1, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(21, 2, '2026-07-26 17:00:00', '2026-07-26 18:00:00', 300, 300, NULL, 0, 0, 'Outdoor Stage', 'Live', 60, NULL, NULL, NULL, 0, 1, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(22, 17, '2026-07-26 18:00:00', '2026-07-26 19:00:00', 300, 300, NULL, 0, 0, 'Outdoor Stage', 'Live', 60, NULL, NULL, NULL, 0, 1, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(23, 1, '2026-07-26 19:00:00', '2026-07-26 20:00:00', 300, 300, NULL, 0, 0, 'Outdoor Stage', 'Live', 60, NULL, NULL, NULL, 0, 1, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(24, 13, '2026-07-26 20:00:00', '2026-07-26 21:00:00', 300, 300, NULL, 0, 0, 'Outdoor Stage', 'Live', 60, NULL, NULL, NULL, 0, 1, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(25, 20, '2026-07-24 20:00:00', '2026-07-25 02:00:00', 1500, 1350, NULL, 0, 0, NULL, 'Back2Back', 360, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €125,00, All-Access pass for Fri, Sat, Sun: €250,00.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(26, 21, '2026-07-24 22:00:00', '2026-07-24 23:30:00', 200, 180, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(27, 22, '2026-07-24 23:00:00', '2026-07-25 00:30:00', 300, 270, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(28, 23, '2026-07-24 22:00:00', '2026-07-24 23:30:00', 200, 180, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(29, 24, '2026-07-24 22:00:00', '2026-07-24 23:30:00', 200, 180, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(30, 25, '2026-07-25 14:00:00', '2026-07-25 23:00:00', 2000, 1800, NULL, 0, 0, NULL, 'Back2Back', 540, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €150,00, All-Access pass for Fri, Sat, Sun: €250,00.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(31, 26, '2026-07-25 22:00:00', '2026-07-25 23:30:00', 300, 270, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(32, 27, '2026-07-25 21:00:00', '2026-07-26 01:00:00', 1500, 1350, NULL, 0, 0, NULL, 'TiëstoWorld', 240, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(33, 28, '2026-07-25 23:00:00', '2026-07-26 00:30:00', 200, 180, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(34, 29, '2026-07-26 14:00:00', '2026-07-26 23:00:00', 2000, 1800, NULL, 0, 0, NULL, 'Back2Back', 540, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €150,00, All-Access pass for Fri, Sat, Sun: €250,00.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(35, 30, '2026-07-26 19:00:00', '2026-07-26 20:30:00', 300, 270, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(36, 31, '2026-07-26 21:00:00', '2026-07-26 22:30:00', 1500, 1350, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(37, 32, '2026-07-26 18:00:00', '2026-07-26 19:30:00', 200, 180, NULL, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(38, 33, '2026-07-23 10:00:00', '2026-07-23 12:00:00', 24, 21, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, 'Due to the nature of this walk participants must be a minimum of 12 years old and no strollers are allowed. Groups will consist of 12 participants + 1 guide.', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(39, 33, '2026-07-23 13:00:00', '2026-07-23 15:00:00', 24, 21, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(40, 33, '2026-07-23 16:00:00', '2026-07-23 18:00:00', 24, 21, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(41, 33, '2026-07-24 10:00:00', '2026-07-24 12:00:00', 24, 21, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(42, 33, '2026-07-24 13:00:00', '2026-07-24 15:00:00', 36, 32, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(43, 33, '2026-07-24 16:00:00', '2026-07-24 18:00:00', 24, 21, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(44, 33, '2026-07-25 10:00:00', '2026-07-25 12:00:00', 48, 43, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(45, 33, '2026-07-25 13:00:00', '2026-07-25 15:00:00', 60, 54, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(46, 33, '2026-07-25 16:00:00', '2026-07-25 18:00:00', 36, 32, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(47, 33, '2026-07-26 10:00:00', '2026-07-26 12:00:00', 60, 54, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(48, 33, '2026-07-26 13:00:00', '2026-07-26 15:00:00', 96, 86, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(49, 33, '2026-07-26 16:00:00', '2026-07-26 18:00:00', 24, 21, NULL, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(50, 34, '2026-07-23 16:00:00', '2026-07-23 17:00:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 60, 'NL', 4, NULL, 0, 0, 'Stories for the whole family', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(51, 35, '2026-07-23 19:00:00', '2026-07-23 20:15:00', 100, 90, NULL, 0, 0, NULL, 'Podcast', 75, 'NL', 16, NULL, 0, 0, 'Recording podcast with audience', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(52, 36, '2026-07-23 20:30:00', '2026-07-23 21:45:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 75, 'ENG', 16, NULL, 0, 0, 'Stories with impact', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(53, 37, '2026-07-24 16:00:00', '2026-07-24 17:00:00', 80, 72, NULL, 0, 0, NULL, 'Storytelling', 60, 'NL', 10, NULL, 0, 0, 'Stories for the whole family', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(54, 38, '2026-07-24 19:00:00', '2026-07-24 20:30:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 90, 'NL', 12, NULL, 0, 0, 'Best off - the contest will be organized in June', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(55, 39, '2026-07-24 19:00:00', '2026-07-24 20:15:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 75, 'NL', 16, NULL, 0, 0, 'Stories with impact', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(56, 40, '2026-07-24 20:30:00', '2026-07-24 21:45:00', 100, 90, NULL, 0, 0, NULL, 'Podcast', 75, 'ENG', 16, NULL, 0, 0, 'Recording podcast with audience', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(57, 41, '2026-07-25 10:00:00', '2026-07-25 11:00:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 60, 'NL', 2, 102, 0, 0, 'Stories for the whole family', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(58, 42, '2026-07-25 15:00:00', '2026-07-25 16:00:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 60, 'ENG', 2, 102, 0, 0, 'Stories for the whole family', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(59, 43, '2026-07-25 14:00:00', '2026-07-25 15:15:00', 100, 90, NULL, 0, 0, NULL, 'Podcast', 75, 'NL', 12, NULL, 0, 0, 'Recording podcast with audience', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(60, 44, '2026-07-25 13:00:00', '2026-07-25 14:30:00', 80, 72, NULL, 0, 0, NULL, 'Storytelling', 90, 'NL', 12, NULL, 0, 0, 'Stories with impact', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(61, 42, '2026-07-26 10:00:00', '2026-07-26 11:00:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 60, 'ENG', 2, 102, 0, 0, 'Stories for the whole family', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(62, 41, '2026-07-26 15:00:00', '2026-07-26 16:00:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 60, 'NL', 2, 102, 0, 0, 'Stories for the whole family', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(63, 45, '2026-07-26 13:00:00', '2026-07-26 14:30:00', 80, 72, NULL, 0, 0, NULL, 'Storytelling', 90, 'ENG', 12, NULL, 0, 0, 'Stories with impact', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(64, 46, '2026-07-26 16:00:00', '2026-07-26 17:30:00', 100, 90, NULL, 0, 0, NULL, 'Storytelling', 90, 'ENG', 12, NULL, 0, 0, 'Best off - the contest will be organized in June', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(65, 47, '2026-07-24 18:00:00', '2026-07-24 19:30:00', 35, 31, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(66, 47, '2026-07-24 19:30:00', '2026-07-24 21:00:00', 35, 31, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(67, 47, '2026-07-24 21:00:00', '2026-07-24 22:30:00', 35, 31, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(68, 48, '2026-07-24 17:00:00', '2026-07-24 19:00:00', 52, 46, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(69, 48, '2026-07-24 19:00:00', '2026-07-24 21:00:00', 52, 46, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(70, 48, '2026-07-24 21:00:00', '2026-07-24 23:00:00', 52, 46, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(71, 49, '2026-07-24 17:00:00', '2026-07-24 19:00:00', 60, 54, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(72, 49, '2026-07-24 19:00:00', '2026-07-24 21:00:00', 60, 54, NULL, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(73, 50, '2026-07-24 17:30:00', '2026-07-24 19:00:00', 45, 40, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(74, 50, '2026-07-24 19:00:00', '2026-07-24 20:30:00', 45, 40, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(75, 50, '2026-07-24 20:30:00', '2026-07-24 22:00:00', 45, 40, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(76, 51, '2026-07-24 17:00:00', '2026-07-24 18:30:00', 36, 32, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(77, 51, '2026-07-24 18:30:00', '2026-07-24 20:00:00', 36, 32, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(78, 51, '2026-07-24 20:00:00', '2026-07-24 21:30:00', 36, 32, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(79, 52, '2026-07-24 16:30:00', '2026-07-24 18:00:00', 100, 90, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(80, 52, '2026-07-24 18:00:00', '2026-07-24 19:30:00', 100, 90, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(81, 52, '2026-07-24 19:30:00', '2026-07-24 21:00:00', 100, 90, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(82, 53, '2026-07-24 17:30:00', '2026-07-24 19:00:00', 48, 43, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(83, 53, '2026-07-24 19:00:00', '2026-07-24 20:30:00', 48, 43, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(84, 53, '2026-07-24 20:30:00', '2026-07-24 22:00:00', 48, 43, NULL, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', NULL, NULL, NULL, 0, '2026-02-06 15:30:32', 1),
(85, 54, '2026-02-10 21:00:00', '2026-07-30 21:54:00', 100, 100, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '', NULL, 'test', '', 0, '2026-02-10 20:55:03', 0),
(86, 55, '2026-02-06 01:17:00', '2026-02-21 01:17:00', 100, 100, NULL, 0, 0, NULL, NULL, NULL, '', NULL, NULL, 0, 0, '', NULL, '', '', 0, '2026-02-11 00:18:01', 0),
(90, 33, '2026-07-23 10:00:00', '2026-07-23 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 12:06:20', 1),
(91, 33, '2026-07-23 13:00:00', '2026-07-23 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:11:18', 1),
(92, 33, '2026-07-23 16:00:00', '2026-07-23 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:11:18', 1),
(93, 33, '2026-07-24 10:00:00', '2026-07-24 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(94, 33, '2026-07-24 13:00:00', '2026-07-24 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(95, 33, '2026-07-24 13:00:00', '2026-07-24 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(96, 33, '2026-07-24 16:00:00', '2026-07-24 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(97, 33, '2026-07-25 10:00:00', '2026-07-25 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(98, 33, '2026-07-25 13:00:00', '2026-07-25 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(99, 33, '2026-07-25 16:00:00', '2026-07-25 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(100, 33, '2026-07-25 10:00:00', '2026-07-25 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(101, 33, '2026-07-25 13:00:00', '2026-07-25 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(102, 33, '2026-07-25 13:00:00', '2026-07-25 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(103, 33, '2026-07-25 16:00:00', '2026-07-25 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(104, 33, '2026-07-25 16:00:00', '2026-07-25 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(105, 33, '2026-07-26 10:00:00', '2026-07-26 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(106, 33, '2026-07-26 10:00:00', '2026-07-26 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(107, 33, '2026-07-26 13:00:00', '2026-07-26 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(108, 33, '2026-07-26 13:00:00', '2026-07-26 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1),
(109, 33, '2026-07-26 16:00:00', '2026-07-26 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1);

-- --------------------------------------------------------

--
-- Table structure for table `EventSessionLabel`
--

CREATE TABLE `EventSessionLabel` (
  `EventSessionLabelId` int(11) NOT NULL,
  `EventSessionId` int(11) NOT NULL,
  `LabelText` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(23, 85, 'test'),
(24, 85, 'test'),
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
(84, 86, 'test'),
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
(114, 109, 'In Dutch');

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
(74, '/assets/Image/Jazz/Jazz-Ntjam.png', 'Jazz-Ntjam.png', 'image/png', 0, 'Ntjam Rosie lineup image', '2026-02-10 13:13:15');

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
(1, 0, 0, 1, '2026-02-10 22:19:41'),
(2, 0, 1, 0, '2026-02-17 10:47:24'),
(3, 0, 2, 0, '2026-02-10 22:19:27'),
(4, 0, 3, 0, '2026-02-10 22:19:27'),
(5, 0, 4, 1, '2026-02-10 22:16:18'),
(6, 0, 5, 1, '2026-02-11 00:19:42'),
(7, 0, 6, 0, '2026-02-17 14:54:07'),
(33, 3, 0, 1, '2026-02-16 21:10:38'),
(34, 3, 4, 1, '2026-02-16 21:10:39'),
(35, 3, 5, 1, '2026-02-16 21:10:40');

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
  `PasswordHash` varchar(255) NOT NULL,
  `PasswordSalt` varbinary(32) DEFAULT NULL,
  `FirstName` varchar(60) NOT NULL,
  `LastName` varchar(80) NOT NULL,
  `ProfilePictureAssetId` int(11) DEFAULT NULL,
  `IsEmailConfirmed` tinyint(1) NOT NULL DEFAULT 0,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `RegisteredAtUtc` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdatedAtUtc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `UserAccount`
--

INSERT INTO `UserAccount` (`UserAccountId`, `UserRoleId`, `Username`, `Email`, `PasswordHash`, `PasswordSalt`, `FirstName`, `LastName`, `ProfilePictureAssetId`, `IsEmailConfirmed`, `IsActive`, `RegisteredAtUtc`, `UpdatedAtUtc`) VALUES
(1, 3, 'testadmin', 'admin@test.com', '$argon2id$v=19$m=65536,t=4,p=1$eHJiQWthdmVFOHhXaFdSOQ$8wLiRCDwXY3hyC4kDM+QUkYEu6kpQhQmFxmSjh5JhiY', NULL, 'Test', 'Admin', NULL, 1, 1, '2026-02-07 19:39:57', '2026-02-07 19:39:57'),
(2, 1, 'testuser', 'user@test.com', '$argon2id$v=19$m=65536,t=4,p=1$amsvVU9QNU5QL1dKM3QzeQ$xmHiIhwxaWEOu5RXiOKUc5KPFex/JL0NV2cmtM0IKhc', NULL, 'Test', 'User', NULL, 1, 1, '2026-02-07 19:39:57', '2026-02-07 19:39:57'),
(3, 1, 'lnunez', 'propipplayer@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$YnBXZktSVEl6THJFUkQuLw$VDzijF0bET0wUsjprdH+3/kSW6Iit5wbErVX+ClELdE', NULL, 'Leandro', 'Nunez', NULL, 0, 1, '2026-02-07 20:19:38', '2026-02-07 21:29:41');

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
  MODIFY `CmsItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=637;

--
-- AUTO_INCREMENT for table `CmsPage`
--
ALTER TABLE `CmsPage`
  MODIFY `CmsPageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `CmsSection`
--
ALTER TABLE `CmsSection`
  MODIFY `CmsSectionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `EmailConfirmationToken`
--
ALTER TABLE `EmailConfirmationToken`
  MODIFY `EmailConfirmationTokenId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Event`
--
ALTER TABLE `Event`
  MODIFY `EventId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `EventSession`
--
ALTER TABLE `EventSession`
  MODIFY `EventSessionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `EventSessionLabel`
--
ALTER TABLE `EventSessionLabel`
  MODIFY `EventSessionLabelId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

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
  MODIFY `MediaAssetId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

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
  MODIFY `PasswordResetTokenId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
-- AUTO_INCREMENT for table `ScheduleDayConfig`
--
ALTER TABLE `ScheduleDayConfig`
  MODIFY `ScheduleDayConfigId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
  MODIFY `UserAccountId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
