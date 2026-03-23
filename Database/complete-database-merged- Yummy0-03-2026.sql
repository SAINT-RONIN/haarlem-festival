-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Mar 10, 2026 at 03:44 PM
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

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `EventSession`
--
ALTER TABLE `EventSession`
  MODIFY `EventSessionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `EventSession`
--
ALTER TABLE `EventSession`
  ADD CONSTRAINT `FK_EventSession_Event` FOREIGN KEY (`EventId`) REFERENCES `Event` (`EventId`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
