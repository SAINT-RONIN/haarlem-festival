-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Mar 10, 2026 at 10:10 AM
-- Server version: 12.2.2-MariaDB-ubu2404
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `haarlem_festival_db`
--

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
START TRANSACTION;

SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------
-- 1) PriceTier
-- -----------------------------------------------------

UPDATE `PriceTier`
SET `Name` = 'Group'
WHERE `PriceTierId` = 3;

INSERT INTO `PriceTier` (`PriceTierId`, `Name`)
SELECT 6, 'Single'
WHERE NOT EXISTS (
    SELECT 1
    FROM `PriceTier`
    WHERE `PriceTierId` = 6
);

-- -----------------------------------------------------
-- 2) Event
-- -----------------------------------------------------

UPDATE `Event`
SET
  `EventTypeId` = 3,
  `Title` = 'A Stroll Through History',
  `ShortDescription` = 'Guided walking tour through historic Haarlem. Due to the nature of this walk, participants must be a minimum of 12 years old and no strollers are allowed.',
  `LongDescriptionHtml` = '<p></p>',
  `FeaturedImageAssetId` = NULL,
  `VenueId` = NULL,
  `ArtistId` = NULL,
  `RestaurantId` = NULL,
  `IsActive` = 1,
  `CreatedAtUtc` = '2026-02-06 15:30:32'
WHERE `EventId` = 33;

-- -----------------------------------------------------
-- 3) CmsItem
--   - first UPDATE existing (CmsSectionId, ItemKey)
--   - then INSERT 
-- -----------------------------------------------------

UPDATE `CmsItem`
SET
  `ItemType` = 'HEADING',
  `TextValue` = 'Tour Schedule',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-02-18 14:37:27'
WHERE `CmsSectionId` = 61 AND `ItemKey` = 'schedule_title';

UPDATE `CmsItem`
SET
  `ItemType` = 'BUTTON_TEXT',
  `TextValue` = 'Filters',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-02-18 14:37:27'
WHERE `CmsSectionId` = 61 AND `ItemKey` = 'schedule_filters_button_text';

UPDATE `CmsItem`
SET
  `ItemType` = 'TEXT',
  `TextValue` = '1',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-02-18 14:37:27'
WHERE `CmsSectionId` = 61 AND `ItemKey` = 'schedule_show_filters';

UPDATE `CmsItem`
SET
  `ItemType` = 'BUTTON_TEXT',
  `TextValue` = 'Add to program',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-02-18 14:37:27'
WHERE `CmsSectionId` = 61 AND `ItemKey` = 'schedule_cta_button_text';

UPDATE `CmsItem`
SET
  `ItemType` = 'TEXT',
  `TextValue` = '€',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-02-18 14:37:27'
WHERE `CmsSectionId` = 61 AND `ItemKey` = 'schedule_currency_symbol';

UPDATE `CmsItem`
SET
  `ItemType` = 'TEXT',
  `TextValue` = 'No events scheduled',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-02-18 14:37:27'
WHERE `CmsSectionId` = 61 AND `ItemKey` = 'schedule_no_events_text';

UPDATE `CmsItem`
SET
  `ItemType` = 'TEXT',
  `TextValue` = 'A giant flag near Church of St. Bavo at Grote Markt',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-03-09 20:41:13'
WHERE `CmsSectionId` = 61 AND `ItemKey` = 'schedule_start_point';

UPDATE `CmsItem`
SET
  `ItemType` = 'IMAGE_PATH',
  `TextValue` = '/assets/Image/History/History-hero.png',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-03-09 23:02:47'
WHERE `CmsSectionId` = 42 AND `ItemKey` = 'hero_background_image';

UPDATE `CmsItem`
SET
  `ItemType` = 'LINK',
  `TextValue` = '#route',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-03-09 23:06:39'
WHERE `CmsSectionId` = 42 AND `ItemKey` = 'hero_button_primary_link';

UPDATE `CmsItem`
SET
  `ItemType` = 'LINK',
  `TextValue` = '#tickets',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-03-09 23:06:39'
WHERE `CmsSectionId` = 42 AND `ItemKey` = 'hero_button_secondary_link';

UPDATE `CmsItem`
SET
  `ItemType` = 'IMAGE_PATH',
  `TextValue` = '/assets/Image/History/History-RouteMap.png',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-03-09 23:08:47'
WHERE `CmsSectionId` = 51 AND `ItemKey` = 'route_map_image';

UPDATE `CmsItem`
SET
  `ItemType` = 'IMAGE_PATH',
  `TextValue` = '/assets/Image/History/History-GroteMarkt.png',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-03-09 23:08:47'
WHERE `CmsSectionId` = 38 AND `ItemKey` = 'history_grotemarkt_image';

UPDATE `CmsItem`
SET
  `ItemType` = 'IMAGE_PATH',
  `TextValue` = '/assets/Image/History/History-AmsterdamsePoort.png',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-03-09 23:08:47'
WHERE `CmsSectionId` = 38 AND `ItemKey` = 'history_amsterdamsepoort_image';

UPDATE `CmsItem`
SET
  `ItemType` = 'IMAGE_PATH',
  `TextValue` = '/assets/Image/History/History-MolenDeAdriaan.png',
  `HtmlValue` = NULL,
  `MediaAssetId` = NULL,
  `UpdatedAtUtc` = '2026-03-09 23:08:47'
WHERE `CmsSectionId` = 38 AND `ItemKey` = 'history_molendeadriaan_image';

INSERT INTO `CmsItem`
(`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
SELECT *
FROM (
    SELECT 61, 'schedule_title', 'HEADING', 'Tour Schedule', NULL, NULL, '2026-02-18 14:37:27'
    UNION ALL SELECT 61, 'schedule_filters_button_text', 'BUTTON_TEXT', 'Filters', NULL, NULL, '2026-02-18 14:37:27'
    UNION ALL SELECT 61, 'schedule_show_filters', 'TEXT', '1', NULL, NULL, '2026-02-18 14:37:27'
    UNION ALL SELECT 61, 'schedule_cta_button_text', 'BUTTON_TEXT', 'Add to program', NULL, NULL, '2026-02-18 14:37:27'
    UNION ALL SELECT 61, 'schedule_currency_symbol', 'TEXT', '€', NULL, NULL, '2026-02-18 14:37:27'
    UNION ALL SELECT 61, 'schedule_no_events_text', 'TEXT', 'No events scheduled', NULL, NULL, '2026-02-18 14:37:27'
    UNION ALL SELECT 61, 'schedule_start_point', 'TEXT', 'A giant flag near Church of St. Bavo at Grote Markt', NULL, NULL, '2026-03-09 20:41:13'
    UNION ALL SELECT 42, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/History/History-hero.png', NULL, NULL, '2026-03-09 23:02:47'
    UNION ALL SELECT 42, 'hero_button_primary_link', 'LINK', '#route', NULL, NULL, '2026-03-09 23:06:39'
    UNION ALL SELECT 42, 'hero_button_secondary_link', 'LINK', '#tickets', NULL, NULL, '2026-03-09 23:06:39'
    UNION ALL SELECT 51, 'route_map_image', 'IMAGE_PATH', '/assets/Image/History/History-RouteMap.png', NULL, NULL, '2026-03-09 23:08:47'
    UNION ALL SELECT 38, 'history_grotemarkt_image', 'IMAGE_PATH', '/assets/Image/History/History-GroteMarkt.png', NULL, NULL, '2026-03-09 23:08:47'
    UNION ALL SELECT 38, 'history_amsterdamsepoort_image', 'IMAGE_PATH', '/assets/Image/History/History-AmsterdamsePoort.png', NULL, NULL, '2026-03-09 23:08:47'
    UNION ALL SELECT 38, 'history_molendeadriaan_image', 'IMAGE_PATH', '/assets/Image/History/History-MolenDeAdriaan.png', NULL, NULL, '2026-03-09 23:08:47'
) AS src (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
WHERE NOT EXISTS (
    SELECT 1
    FROM `CmsItem` c
    WHERE c.`CmsSectionId` = src.`CmsSectionId`
      AND c.`ItemKey` = src.`ItemKey`
);

-- -----------------------------------------------------
-- 4) EventSession 38..49 (UPDATE existing)
-- -----------------------------------------------------

UPDATE `EventSession`
SET
  `EventId` = 33,
  `StartDateTime` = '2026-07-23 10:00:00',
  `EndDateTime` = '2026-07-23 12:30:00',
  `CapacityTotal` = 24,
  `CapacitySingleTicketLimit` = 21,
  `SeatsAvailable` = NULL,
  `SoldSingleTickets` = 0,
  `SoldReservedSeats` = 0,
  `HallName` = NULL,
  `SessionType` = 'Tour',
  `DurationMinutes` = 150,
  `LanguageCode` = 'EN',
  `MinAge` = 12,
  `MaxAge` = NULL,
  `ReservationRequired` = 1,
  `IsFree` = 0,
  `Notes` = '',
  `HistoryTicketLabel` = NULL,
  `CtaLabel` = NULL,
  `CtaUrl` = NULL,
  `IsCancelled` = 0,
  `CreatedAtUtc` = '2026-02-06 15:30:32',
  `IsActive` = 1
WHERE `EventSessionId` = 38;

UPDATE `EventSession`
SET
  `EventId` = 33,
  `StartDateTime` = '2026-07-23 13:00:00',
  `EndDateTime` = '2026-07-23 15:30:00',
  `CapacityTotal` = 24,
  `CapacitySingleTicketLimit` = 21,
  `SeatsAvailable` = NULL,
  `SoldSingleTickets` = 0,
  `SoldReservedSeats` = 0,
  `HallName` = NULL,
  `SessionType` = 'Tour',
  `DurationMinutes` = 150,
  `LanguageCode` = 'EN',
  `MinAge` = 12,
  `MaxAge` = NULL,
  `ReservationRequired` = 1,
  `IsFree` = 0,
  `Notes` = '',
  `HistoryTicketLabel` = NULL,
  `CtaLabel` = NULL,
  `CtaUrl` = NULL,
  `IsCancelled` = 0,
  `CreatedAtUtc` = '2026-02-06 15:30:32',
  `IsActive` = 1
WHERE `EventSessionId` = 39;

UPDATE `EventSession`
SET
  `EventId` = 33,
  `StartDateTime` = '2026-07-23 16:00:00',
  `EndDateTime` = '2026-07-23 18:30:00',
  `CapacityTotal` = 24,
  `CapacitySingleTicketLimit` = 21,
  `SeatsAvailable` = NULL,
  `SoldSingleTickets` = 0,
  `SoldReservedSeats` = 0,
  `HallName` = NULL,
  `SessionType` = 'Tour',
  `DurationMinutes` = 150,
  `LanguageCode` = 'EN',
  `MinAge` = 12,
  `MaxAge` = NULL,
  `ReservationRequired` = 1,
  `IsFree` = 0,
  `Notes` = '',
  `HistoryTicketLabel` = NULL,
  `CtaLabel` = NULL,
  `CtaUrl` = NULL,
  `IsCancelled` = 0,
  `CreatedAtUtc` = '2026-02-06 15:30:32',
  `IsActive` = 1
WHERE `EventSessionId` = 40;

UPDATE `EventSession`
SET
  `EventId` = 33,
  `StartDateTime` = '2026-07-24 10:00:00',
  `EndDateTime` = '2026-07-24 12:30:00',
  `CapacityTotal` = 24,
  `CapacitySingleTicketLimit` = 21,
  `SeatsAvailable` = NULL,
  `SoldSingleTickets` = 0,
  `SoldReservedSeats` = 0,
  `HallName` = NULL,
  `SessionType` = 'Tour',
  `DurationMinutes` = 150,
  `LanguageCode` = 'EN',
  `MinAge` = 12,
  `MaxAge` = NULL,
  `ReservationRequired` = 1,
  `IsFree` = 0,
  `Notes` = '',
  `HistoryTicketLabel` = NULL,
  `CtaLabel` = NULL,
  `CtaUrl` = NULL,
  `IsCancelled` = 0,
  `CreatedAtUtc` = '2026-02-06 15:30:32',
  `IsActive` = 1
WHERE `EventSessionId` = 41;

UPDATE `EventSession`
SET
  `EventId` = 33,
  `StartDateTime` = '2026-07-24 13:00:00',
  `EndDateTime` = '2026-07-24 15:30:00',
  `CapacityTotal` = 36,
  `CapacitySingleTicketLimit` = 32,
  `SeatsAvailable` = NULL,
  `SoldSingleTickets` = 0,
  `SoldReservedSeats` = 0,
  `HallName` = NULL,
  `SessionType` = 'Tour',
  `DurationMinutes` = 150,
  `LanguageCode` = 'EN',
  `MinAge` = 12,
  `MaxAge` = NULL,
  `ReservationRequired` = 1,
  `IsFree` = 0,
  `Notes` = '',
  `HistoryTicketLabel` = NULL,
  `CtaLabel` = NULL,
  `CtaUrl` = NULL,
  `IsCancelled` = 0,
  `CreatedAtUtc` = '2026-02-06 15:30:32',
  `IsActive` = 1
WHERE `EventSessionId` = 42;

UPDATE `EventSession`
SET
  `EventId` = 33,
  `StartDateTime` = '2026-07-24 16:00:00',
  `EndDateTime` = '2026-07-24 18:30:00',
  `CapacityTotal` = 24,
  `CapacitySingleTicketLimit` = 21,
  `SeatsAvailable` = NULL,
  `SoldSingleTickets` = 0,
  `SoldReservedSeats` = 0,
  `HallName` = NULL,
  `SessionType` = 'Tour',
  `DurationMinutes` = 150,
  `LanguageCode` = 'EN',
  `MinAge` = 12,
  `MaxAge` = NULL,
  `ReservationRequired` = 1,
  `IsFree` = 0,
  `Notes` = '',
  `HistoryTicketLabel` = NULL,
  `CtaLabel` = NULL,
  `CtaUrl` = NULL,
  `IsCancelled` = 0,
  `CreatedAtUtc` = '2026-02-06 15:30:32',
  `IsActive` = 1
WHERE `EventSessionId` = 43;

UPDATE `EventSession`
SET
  `EventId` = 33,
  `StartDateTime` = '2026-07-25 10:00:00',
  `EndDateTime` = '2026-07-25 12:30:00',
  `CapacityTotal` = 48,
  `CapacitySingleTicketLimit` = 43,
  `SeatsAvailable` = NULL,
  `SoldSingleTickets` = 0,
  `SoldReservedSeats` = 0,
  `HallName` = NULL,
  `SessionType` = 'Tour',
  `DurationMinutes` = 150,
  `LanguageCode` = 'EN',
  `MinAge` = 12,
  `MaxAge` = NULL,
  `ReservationRequired` = 1,
  `IsFree` = 0,
  `Notes` = '',
  `HistoryTicketLabel` = NULL,
  `CtaLabel` = NULL,
  `CtaUrl` = NULL,
  `IsCancelled` = 0,
  `CreatedAtUtc` = '2026-02-06 15:30:32',
  `IsActive` = 1
WHERE `EventSessionId` = 44;

UPDATE `EventSession`
SET
  `EventId` = 33,
  `StartDateTime` = '2026-07-25 13:00:00',
  `EndDateTime` = '2026-07-25 15:30:00',
  `CapacityTotal` = 60,
  `CapacitySingleTicketLimit` = 54,
  `SeatsAvailable` = NULL,
  `SoldSingleTickets` = 0,
  `SoldReservedSeats` = 0,
  `HallName` = NULL,
  `SessionType` = 'Tour',
  `DurationMinutes` = 150,
  `LanguageCode` = 'EN',
  `MinAge` = 12,
  `MaxAge` = NULL,
  `ReservationRequired` = 1,
  `IsFree` = 0,
  `Notes` = '',
  `HistoryTicketLabel` = NULL,
  `CtaLabel` = NULL,
  `CtaUrl` = NULL,
  `IsCancelled` = 0,
  `CreatedAtUtc` = '2026-02-06 15:30:32',
  `IsActive` = 1
WHERE `EventSessionId` = 45;

UPDATE `EventSession`
SET
  `EventId` = 33,
  `StartDateTime` = '2026-07-25 16:00:00',
  `EndDateTime` = '2026-07-25 18:30:00',
  `CapacityTotal` = 36,
  `CapacitySingleTicketLimit` = 32,
  `SeatsAvailable` = NULL,
  `SoldSingleTickets` = 0,
  `SoldReservedSeats` = 0,
  `HallName` = NULL,
  `SessionType` = 'Tour',
  `DurationMinutes` = 150,
  `LanguageCode` = 'EN',
  `MinAge` = 12,
  `MaxAge` = NULL,
  `ReservationRequired` = 1,
  `IsFree` = 0,
  `Notes` = '',
  `HistoryTicketLabel` = NULL,
  `CtaLabel` = NULL,
  `CtaUrl` = NULL,
  `IsCancelled` = 0,
  `CreatedAtUtc` = '2026-02-06 15:30:32',
  `IsActive` = 1
WHERE `EventSessionId` = 46;

UPDATE `EventSession`
SET
  `EventId` = 33,
  `StartDateTime` = '2026-07-26 10:00:00',
  `EndDateTime` = '2026-07-26 12:30:00',
  `CapacityTotal` = 60,
  `CapacitySingleTicketLimit` = 54,
  `SeatsAvailable` = NULL,
  `SoldSingleTickets` = 0,
  `SoldReservedSeats` = 0,
  `HallName` = NULL,
  `SessionType` = 'Tour',
  `DurationMinutes` = 150,
  `LanguageCode` = 'EN',
  `MinAge` = 12,
  `MaxAge` = NULL,
  `ReservationRequired` = 1,
  `IsFree` = 0,
  `Notes` = '',
  `HistoryTicketLabel` = NULL,
  `CtaLabel` = NULL,
  `CtaUrl` = NULL,
  `IsCancelled` = 0,
  `CreatedAtUtc` = '2026-02-06 15:30:32',
  `IsActive` = 1
WHERE `EventSessionId` = 47;

UPDATE `EventSession`
SET
  `EventId` = 33,
  `StartDateTime` = '2026-07-26 13:00:00',
  `EndDateTime` = '2026-07-26 15:30:00',
  `CapacityTotal` = 96,
  `CapacitySingleTicketLimit` = 86,
  `SeatsAvailable` = NULL,
  `SoldSingleTickets` = 0,
  `SoldReservedSeats` = 0,
  `HallName` = NULL,
  `SessionType` = 'Tour',
  `DurationMinutes` = 150,
  `LanguageCode` = 'EN',
  `MinAge` = 12,
  `MaxAge` = NULL,
  `ReservationRequired` = 1,
  `IsFree` = 0,
  `Notes` = '',
  `HistoryTicketLabel` = NULL,
  `CtaLabel` = NULL,
  `CtaUrl` = NULL,
  `IsCancelled` = 0,
  `CreatedAtUtc` = '2026-02-06 15:30:32',
  `IsActive` = 1
WHERE `EventSessionId` = 48;

UPDATE `EventSession`
SET
  `EventId` = 33,
  `StartDateTime` = '2026-07-26 16:00:00',
  `EndDateTime` = '2026-07-26 18:30:00',
  `CapacityTotal` = 24,
  `CapacitySingleTicketLimit` = 21,
  `SeatsAvailable` = NULL,
  `SoldSingleTickets` = 0,
  `SoldReservedSeats` = 0,
  `HallName` = NULL,
  `SessionType` = 'Tour',
  `DurationMinutes` = 150,
  `LanguageCode` = 'EN',
  `MinAge` = 12,
  `MaxAge` = NULL,
  `ReservationRequired` = 1,
  `IsFree` = 0,
  `Notes` = '',
  `HistoryTicketLabel` = NULL,
  `CtaLabel` = NULL,
  `CtaUrl` = NULL,
  `IsCancelled` = 0,
  `CreatedAtUtc` = '2026-02-06 15:30:32',
  `IsActive` = 1
WHERE `EventSessionId` = 49;

-- -----------------------------------------------------
-- 5) EventSession 90..109
-- keep EventSessionId because of price and label references
-- -----------------------------------------------------

INSERT INTO `EventSession`
(`EventSessionId`, `EventId`, `StartDateTime`, `EndDateTime`, `CapacityTotal`, `CapacitySingleTicketLimit`, `SeatsAvailable`, `SoldSingleTickets`, `SoldReservedSeats`, `HallName`, `SessionType`, `DurationMinutes`, `LanguageCode`, `MinAge`, `MaxAge`, `ReservationRequired`, `IsFree`, `Notes`, `HistoryTicketLabel`, `CtaLabel`, `CtaUrl`, `IsCancelled`, `CreatedAtUtc`, `IsActive`)
SELECT *
FROM (
    SELECT 90, 33, '2026-07-23 10:00:00', '2026-07-23 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 12:06:20', 1
    UNION ALL SELECT 91, 33, '2026-07-23 13:00:00', '2026-07-23 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:11:18', 1
    UNION ALL SELECT 92, 33, '2026-07-23 16:00:00', '2026-07-23 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:11:18', 1
    UNION ALL SELECT 93, 33, '2026-07-24 10:00:00', '2026-07-24 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 94, 33, '2026-07-24 13:00:00', '2026-07-24 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 95, 33, '2026-07-24 13:00:00', '2026-07-24 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 96, 33, '2026-07-24 16:00:00', '2026-07-24 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 97, 33, '2026-07-25 10:00:00', '2026-07-25 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 98, 33, '2026-07-25 13:00:00', '2026-07-25 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 99, 33, '2026-07-25 16:00:00', '2026-07-25 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 100, 33, '2026-07-25 10:00:00', '2026-07-25 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 101, 33, '2026-07-25 13:00:00', '2026-07-25 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 102, 33, '2026-07-25 13:00:00', '2026-07-25 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 103, 33, '2026-07-25 16:00:00', '2026-07-25 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 104, 33, '2026-07-25 16:00:00', '2026-07-25 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 105, 33, '2026-07-26 10:00:00', '2026-07-26 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 106, 33, '2026-07-26 10:00:00', '2026-07-26 12:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 107, 33, '2026-07-26 13:00:00', '2026-07-26 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 108, 33, '2026-07-26 13:00:00', '2026-07-26 15:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'ZH', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
    UNION ALL SELECT 109, 33, '2026-07-26 16:00:00', '2026-07-26 18:30:00', 12, 12, 12, 0, 0, NULL, 'Tour', 150, 'NL', 12, NULL, 0, 0, '', NULL, NULL, NULL, 0, '2026-03-09 19:19:22', 1
) AS src
(`EventSessionId`, `EventId`, `StartDateTime`, `EndDateTime`, `CapacityTotal`, `CapacitySingleTicketLimit`, `SeatsAvailable`, `SoldSingleTickets`, `SoldReservedSeats`, `HallName`, `SessionType`, `DurationMinutes`, `LanguageCode`, `MinAge`, `MaxAge`, `ReservationRequired`, `IsFree`, `Notes`, `HistoryTicketLabel`, `CtaLabel`, `CtaUrl`, `IsCancelled`, `CreatedAtUtc`, `IsActive`)
WHERE NOT EXISTS (
    SELECT 1
    FROM `EventSession` e
    WHERE e.`EventSessionId` = src.`EventSessionId`
);

-- -----------------------------------------------------
-- 6) EventSessionPrice
-- -----------------------------------------------------

INSERT INTO `EventSessionPrice`
(`EventSessionId`, `PriceTierId`, `Price`, `CurrencyCode`, `VatRate`)
SELECT *
FROM (
    SELECT 90, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 91, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 92, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 93, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 94, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 95, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 96, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 97, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 98, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 99, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 100, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 101, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 102, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 103, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 104, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 105, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 106, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 107, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 108, 6, 17.50, 'EUR', 21.00
    UNION ALL SELECT 109, 6, 17.50, 'EUR', 21.00
) AS src (`EventSessionId`, `PriceTierId`, `Price`, `CurrencyCode`, `VatRate`)
WHERE EXISTS (
    SELECT 1
    FROM `EventSession` es
    WHERE es.`EventSessionId` = src.`EventSessionId`
)
AND NOT EXISTS (
    SELECT 1
    FROM `EventSessionPrice` p
    WHERE p.`EventSessionId` = src.`EventSessionId`
      AND p.`PriceTierId` = src.`PriceTierId`
      AND p.`Price` = src.`Price`
      AND p.`CurrencyCode` = src.`CurrencyCode`
      AND p.`VatRate` = src.`VatRate`
);

-- -----------------------------------------------------
-- 7) EventSessionLabel
-- -----------------------------------------------------

INSERT INTO `EventSessionLabel`
(`EventSessionId`, `LabelText`)
SELECT *
FROM (
    SELECT 38, 'In English'
    UNION ALL SELECT 90, 'In Dutch'
    UNION ALL SELECT 39, 'In English'
    UNION ALL SELECT 91, 'In Dutch'
    UNION ALL SELECT 40, 'In English'
    UNION ALL SELECT 92, 'In Dutch'
    UNION ALL SELECT 41, 'In English'
    UNION ALL SELECT 93, 'In Dutch'
    UNION ALL SELECT 42, 'In English'
    UNION ALL SELECT 94, 'In Dutch'
    UNION ALL SELECT 95, 'In Chinese'
    UNION ALL SELECT 43, 'In English'
    UNION ALL SELECT 99, 'In Dutch'
    UNION ALL SELECT 44, 'In English'
    UNION ALL SELECT 100, 'In Dutch'
    UNION ALL SELECT 45, 'In English'
    UNION ALL SELECT 101, 'In Dutch'
    UNION ALL SELECT 102, 'In Chinese'
    UNION ALL SELECT 46, 'In English'
    UNION ALL SELECT 103, 'In Dutch'
    UNION ALL SELECT 104, 'In Chinese'
    UNION ALL SELECT 47, 'In English'
    UNION ALL SELECT 105, 'In Dutch'
    UNION ALL SELECT 106, 'In Chinese'
    UNION ALL SELECT 48, 'In English'
    UNION ALL SELECT 107, 'In Dutch'
    UNION ALL SELECT 108, 'In Chinese'
    UNION ALL SELECT 49, 'In English'
    UNION ALL SELECT 109, 'In Dutch'
) AS src (`EventSessionId`, `LabelText`)
WHERE EXISTS (
    SELECT 1
    FROM `EventSession` es
    WHERE es.`EventSessionId` = src.`EventSessionId`
)
AND NOT EXISTS (
    SELECT 1
    FROM `EventSessionLabel` l
    WHERE l.`EventSessionId` = src.`EventSessionId`
      AND l.`LabelText` = src.`LabelText`
);

SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS;

COMMIT;