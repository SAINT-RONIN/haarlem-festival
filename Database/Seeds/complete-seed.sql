-- Complete Seed Data for Haarlem Festival 2026
-- Generated for MariaDB/MySQL - Compatible with phpMyAdmin
-- Festival Dates: Thursday July 23 - Sunday July 26, 2026

USE `haarlem_festival_db`;

START TRANSACTION;

SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- DELETE EVENT-RELATED TABLES (in FK-safe order)
-- Using DELETE instead of TRUNCATE for FK compatibility
-- =============================================
DELETE FROM `HistoryTourGuide`;
DELETE FROM `HistoryTour`;
DELETE FROM `EventSessionPrice`;
DELETE FROM `EventSessionLabel`;
DELETE FROM `EventSession`;
DELETE FROM `Event`;
DELETE FROM `ScheduleDay`;
DELETE FROM `Guide`;
DELETE FROM `Restaurant`;
DELETE FROM `Artist`;
DELETE FROM `Venue`;

-- =============================================
-- VENUES (15 venues)
-- =============================================
INSERT INTO `Venue` (`VenueId`, `Name`, `AddressLine`, `City`, `IsActive`) VALUES
-- Jazz Venues
(1, 'Patronaat', 'Zijlsingel 2, 2013 DN Haarlem', 'Haarlem', 1),
(2, 'Grote Markt', 'Grote Markt', 'Haarlem', 1),
-- Dance Venues
(3, 'Lichtfabriek', 'Minckelersweg 2', 'Haarlem', 1),
(4, 'Slachthuis', 'Rockplein 6', 'Haarlem', 1),
(5, 'Jopenkerk', 'Gedempte Voldersgracht 2', 'Haarlem', 1),
(6, 'XO the Club', 'Grote Markt 8', 'Haarlem', 1),
(7, 'Puncher Comedy Club', 'Grote Markt 10', 'Haarlem', 1),
(8, 'Caprera Openluchttheater', 'Hoge Duin en Daalseweg 2', 'Bloemendaal', 1),
-- History Venue
(9, 'Bavo Church', 'Grote Markt 22', 'Haarlem', 1),
-- Storytelling Venues
(10, 'Verhalenhuis Haarlem', 'van Egmondstraat 7', 'Haarlem', 1),
(11, 'De Schuur', 'Lange Begijnestraat 9, 2011 HH Haarlem', 'Haarlem', 1),
(12, 'Kweekcafé', 'Kleverlaan 9, 2023 JC Haarlem', 'Haarlem', 1),
(13, 'Corrie ten Boom Huis', 'Barteljorisstraat 19', 'Haarlem', 1),
(14, 'Theater Elswout', 'Elswoutslaan 24-a, 2051 AE Overveen', 'Overveen', 1);

-- =============================================
-- ARTISTS (20 unique artists)
-- =============================================
INSERT INTO `Artist` (`ArtistId`, `Name`, `Style`, `BioHtml`, `ImageAssetId`, `IsActive`) VALUES
-- Jazz Artists
(1, 'Gumbo Kings', 'Jazz', '<p></p>', NULL, 1),
(2, 'Evolve', 'Jazz', '<p></p>', NULL, 1),
(3, 'Ntjam Rosie', 'Jazz', '<p></p>', NULL, 1),
(4, 'Wicked Jazz Sounds', 'Jazz', '<p></p>', NULL, 1),
(5, 'Wouter Hamel', 'Jazz', '<p></p>', NULL, 1),
(6, 'Jonna Frazer', 'Jazz', '<p></p>', NULL, 1),
(7, 'Karsu', 'Jazz', '<p></p>', NULL, 1),
(8, 'Uncle Sue', 'Jazz', '<p></p>', NULL, 1),
(9, 'Chris Allen', 'Jazz', '<p></p>', NULL, 1),
(10, 'Myles Sanko', 'Jazz', '<p></p>', NULL, 1),
(11, 'Ilse Huizinga', 'Jazz', '<p></p>', NULL, 1),
(12, 'Eric Vloeimans and Hotspot!', 'Jazz', '<p></p>', NULL, 1),
(13, 'Gare du Nord', 'Jazz', '<p></p>', NULL, 1),
(14, 'Rilan & The Bombadiers', 'Jazz', '<p></p>', NULL, 1),
(15, 'Soul Six', 'Jazz', '<p></p>', NULL, 1),
(16, 'Han Bennink', 'Jazz', '<p></p>', NULL, 1),
(17, 'The Nordanians', 'Jazz', '<p></p>', NULL, 1),
(18, 'Lilith Merlot', 'Jazz', '<p></p>', NULL, 1),
(19, 'Ruis Soundsystem', 'Jazz', '<p></p>', NULL, 1),
-- Dance Artists
(20, 'Nicky Romero', 'Dance/EDM', '<p></p>', NULL, 1),
(21, 'Afrojack', 'Dance/EDM', '<p></p>', NULL, 1),
(22, 'Tiësto', 'Dance/EDM', '<p></p>', NULL, 1),
(23, 'Hardwell', 'Dance/EDM', '<p></p>', NULL, 1),
(24, 'Martin Garrix', 'Dance/EDM', '<p></p>', NULL, 1),
(25, 'Armin van Buuren', 'Dance/EDM', '<p></p>', NULL, 1);

-- =============================================
-- RESTAURANTS (7 restaurants)
-- =============================================
INSERT INTO `Restaurant` (`RestaurantId`, `Name`, `AddressLine`, `City`, `Stars`, `CuisineType`, `DescriptionHtml`, `ImageAssetId`, `IsActive`) VALUES
(1, 'Café de Roemer', 'Botermarkt 17, 2011 XL Haarlem', 'Haarlem', 4, 'Dutch, fish and seafood, European', '<p></p>', NULL, 1),
(2, 'Ratatouille', 'Spaarne 96, 2011 CL Haarlem', 'Haarlem', 4, 'French, fish and seafood, European', '<p></p>', NULL, 1),
(3, 'Restaurant ML', 'Kleine Houtstraat 70, 2011 DR Haarlem', 'Haarlem', 4, 'Dutch, fish and seafood, European', '<p></p>', NULL, 1),
(4, 'Restaurant Fris', 'Twijnderslaan 7, 2012 BG Haarlem', 'Haarlem', 4, 'Dutch, French, European', '<p></p>', NULL, 1),
(5, 'New Vegas', 'Koningstraat 5, 2011 TB Haarlem', 'Haarlem', 3, 'Vegan', '<p></p>', NULL, 1),
(6, 'Grand Cafe Brinkman', 'Grote Markt 13, 2011 RC Haarlem', 'Haarlem', 3, 'Dutch, European, Modern', '<p></p>', NULL, 1),
(7, 'Urban Frenchy Bistro Toujours', 'Oude Groenmarkt 10-12, 2011 HL Haarlem', 'Haarlem', 3, 'Dutch, fish and seafood, European', '<p></p>', NULL, 1);

-- =============================================
-- GUIDES (8 guides for History tours)
-- =============================================
INSERT INTO `Guide` (`GuideId`, `Name`, `IsActive`) VALUES
(1, 'Guide EN-1', 1),
(2, 'Guide EN-2', 1),
(3, 'Guide EN-3', 1),
(4, 'Guide NL-1', 1),
(5, 'Guide NL-2', 1),
(6, 'Guide NL-3', 1),
(7, 'Guide ZH-1', 1),
(8, 'Guide ZH-2', 1);

-- =============================================
-- SCHEDULE DAYS (15 days across event types)
-- =============================================
INSERT INTO `ScheduleDay` (`ScheduleDayId`, `EventTypeId`, `Date`, `IsDeleted`, `DeletedAtUtc`) VALUES
-- Jazz (EventTypeId = 1): Thu-Sun
(1, 1, '2026-07-23', 0, NULL),
(2, 1, '2026-07-24', 0, NULL),
(3, 1, '2026-07-25', 0, NULL),
(4, 1, '2026-07-26', 0, NULL),
-- Dance (EventTypeId = 2): Fri-Sun
(5, 2, '2026-07-24', 0, NULL),
(6, 2, '2026-07-25', 0, NULL),
(7, 2, '2026-07-26', 0, NULL),
-- History (EventTypeId = 3): Thu-Sun
(8, 3, '2026-07-23', 0, NULL),
(9, 3, '2026-07-24', 0, NULL),
(10, 3, '2026-07-25', 0, NULL),
(11, 3, '2026-07-26', 0, NULL),
-- Storytelling (EventTypeId = 4): Thu-Sun
(12, 4, '2026-07-23', 0, NULL),
(13, 4, '2026-07-24', 0, NULL),
(14, 4, '2026-07-25', 0, NULL),
(15, 4, '2026-07-26', 0, NULL),
-- Restaurant (EventTypeId = 5): Thu-Sun
(16, 5, '2026-07-23', 0, NULL),
(17, 5, '2026-07-24', 0, NULL),
(18, 5, '2026-07-25', 0, NULL),
(19, 5, '2026-07-26', 0, NULL);

-- =============================================
-- EVENTS
-- =============================================

-- ---------------------------------------------
-- JAZZ EVENTS (14 unique artists = 14 events)
-- EventTypeId = 1
-- ---------------------------------------------
INSERT INTO `Event` (`EventId`, `EventTypeId`, `Title`, `ShortDescription`, `LongDescriptionHtml`, `FeaturedImageAssetId`, `VenueId`, `ArtistId`, `RestaurantId`, `IsActive`) VALUES
(1, 1, 'Gumbo Kings', 'Live jazz performance by Gumbo Kings', '<p></p>', NULL, 1, 1, NULL, 1),
(2, 1, 'Evolve', 'Live jazz performance by Evolve', '<p></p>', NULL, 1, 2, NULL, 1),
(3, 1, 'Ntjam Rosie', 'Live jazz performance by Ntjam Rosie', '<p></p>', NULL, 1, 3, NULL, 1),
(4, 1, 'Wicked Jazz Sounds', 'Live jazz performance by Wicked Jazz Sounds', '<p></p>', NULL, 1, 4, NULL, 1),
(5, 1, 'Wouter Hamel', 'Live jazz performance by Wouter Hamel', '<p></p>', NULL, 1, 5, NULL, 1),
(6, 1, 'Jonna Frazer', 'Live jazz performance by Jonna Frazer', '<p></p>', NULL, 1, 6, NULL, 1),
(7, 1, 'Karsu', 'Live jazz performance by Karsu', '<p></p>', NULL, 1, 7, NULL, 1),
(8, 1, 'Uncle Sue', 'Live jazz performance by Uncle Sue', '<p></p>', NULL, 1, 8, NULL, 1),
(9, 1, 'Chris Allen', 'Live jazz performance by Chris Allen', '<p></p>', NULL, 1, 9, NULL, 1),
(10, 1, 'Myles Sanko', 'Live jazz performance by Myles Sanko', '<p></p>', NULL, 1, 10, NULL, 1),
(11, 1, 'Ilse Huizinga', 'Live jazz performance by Ilse Huizinga', '<p></p>', NULL, 1, 11, NULL, 1),
(12, 1, 'Eric Vloeimans and Hotspot!', 'Live jazz performance by Eric Vloeimans and Hotspot!', '<p></p>', NULL, 1, 12, NULL, 1),
(13, 1, 'Gare du Nord', 'Live jazz performance by Gare du Nord', '<p></p>', NULL, 1, 13, NULL, 1),
(14, 1, 'Rilan & The Bombadiers', 'Live jazz performance by Rilan & The Bombadiers', '<p></p>', NULL, 1, 14, NULL, 1),
(15, 1, 'Soul Six', 'Live jazz performance by Soul Six', '<p></p>', NULL, 1, 15, NULL, 1),
(16, 1, 'Han Bennink', 'Live jazz performance by Han Bennink', '<p></p>', NULL, 1, 16, NULL, 1),
(17, 1, 'The Nordanians', 'Live jazz performance by The Nordanians', '<p></p>', NULL, 1, 17, NULL, 1),
(18, 1, 'Lilith Merlot', 'Live jazz performance by Lilith Merlot', '<p></p>', NULL, 1, 18, NULL, 1),
(19, 1, 'Ruis Soundsystem', 'Live jazz performance by Ruis Soundsystem', '<p></p>', NULL, 2, 19, NULL, 1);

-- ---------------------------------------------
-- DANCE EVENTS (10 events)
-- EventTypeId = 2
-- Back2Back events have ArtistId = NULL, artists in title
-- ---------------------------------------------
INSERT INTO `Event` (`EventId`, `EventTypeId`, `Title`, `ShortDescription`, `LongDescriptionHtml`, `FeaturedImageAssetId`, `VenueId`, `ArtistId`, `RestaurantId`, `IsActive`) VALUES
-- Friday Back2Back at Lichtfabriek
(20, 2, 'Nicky Romero / Afrojack - Back2Back', 'Back2Back session featuring Nicky Romero and Afrojack', '<p></p>', NULL, 3, NULL, NULL, 1),
-- Friday Club events
(21, 2, 'Tiësto - Club', 'Club session by Tiësto', '<p></p>', NULL, 4, 22, NULL, 1),
(22, 2, 'Hardwell - Club', 'Club session by Hardwell', '<p></p>', NULL, 5, 23, NULL, 1),
(23, 2, 'Armin van Buuren - Club', 'Club session by Armin van Buuren', '<p></p>', NULL, 6, 25, NULL, 1),
(24, 2, 'Martin Garrix - Club', 'Club session by Martin Garrix', '<p></p>', NULL, 7, 24, NULL, 1),
-- Saturday Back2Back at Caprera
(25, 2, 'Hardwell / Martin Garrix / Armin van Buuren - Back2Back', 'Back2Back session featuring Hardwell, Martin Garrix and Armin van Buuren', '<p></p>', NULL, 8, NULL, NULL, 1),
-- Saturday Club/World events
(26, 2, 'Afrojack - Club', 'Club session by Afrojack', '<p></p>', NULL, 5, 21, NULL, 1),
(27, 2, 'Tiësto - TiëstoWorld', 'TiëstoWorld session by Tiësto', '<p></p>', NULL, 3, 22, NULL, 1),
(28, 2, 'Nicky Romero - Club', 'Club session by Nicky Romero', '<p></p>', NULL, 4, 20, NULL, 1),
-- Sunday Back2Back at Caprera
(29, 2, 'Afrojack / Tiësto / Nicky Romero - Back2Back', 'Back2Back session featuring Afrojack, Tiësto and Nicky Romero', '<p></p>', NULL, 8, NULL, NULL, 1),
-- Sunday Club events
(30, 2, 'Armin van Buuren - Club (Sunday)', 'Club session by Armin van Buuren', '<p></p>', NULL, 5, 25, NULL, 1),
(31, 2, 'Hardwell - Club (Sunday)', 'Club session by Hardwell', '<p></p>', NULL, 6, 23, NULL, 1),
(32, 2, 'Martin Garrix - Club (Sunday)', 'Club session by Martin Garrix', '<p></p>', NULL, 4, 24, NULL, 1);

-- ---------------------------------------------
-- HISTORY EVENT (1 event with multiple sessions)
-- EventTypeId = 3
-- ---------------------------------------------
INSERT INTO `Event` (`EventId`, `EventTypeId`, `Title`, `ShortDescription`, `LongDescriptionHtml`, `FeaturedImageAssetId`, `VenueId`, `ArtistId`, `RestaurantId`, `IsActive`) VALUES
(33, 3, 'Haarlem History Walking Tour', 'Guided walking tour through historic Haarlem. Due to the nature of this walk, participants must be a minimum of 12 years old and no strollers are allowed.', '<p>Groups will consist of 12 participants + 1 guide. Available in English, Dutch, and Chinese.</p>', NULL, 9, NULL, NULL, 1);

-- ---------------------------------------------
-- STORYTELLING EVENTS (15 events)
-- EventTypeId = 4
-- ---------------------------------------------
INSERT INTO `Event` (`EventId`, `EventTypeId`, `Title`, `ShortDescription`, `LongDescriptionHtml`, `FeaturedImageAssetId`, `VenueId`, `ArtistId`, `RestaurantId`, `IsActive`) VALUES
(34, 4, 'Winnie de Poeh (4+)', 'Stories for the whole family', '<p></p>', NULL, 10, NULL, NULL, 1),
(35, 4, 'Omdenken Podcast', 'Recording podcast with audience', '<p></p>', NULL, 11, NULL, NULL, 1),
(36, 4, 'The story of Buurderij Haarlem', 'Stories with impact', '<p></p>', NULL, 12, NULL, NULL, 1),
(37, 4, 'Corrie voor kinderen', 'Stories for the whole family', '<p></p>', NULL, 13, NULL, NULL, 1),
(38, 4, 'Winnaars van verhalenvertel wedstrijd, verhalen voor Haarlem', 'Best off - the contest will be organized in June', '<p></p>', NULL, 10, NULL, NULL, 1),
(39, 4, 'Het verhaal van de Oeserzwammerij', 'Stories with impact', '<p></p>', NULL, 12, NULL, NULL, 1),
(40, 4, 'Flip Thinking Podcast', 'Recording podcast with audience', '<p></p>', NULL, 11, NULL, NULL, 1),
(41, 4, 'Meneer Anansi', 'Stories for the whole family', '<p></p>', NULL, 14, NULL, NULL, 1),
(42, 4, 'Mister Anansi', 'Stories for the whole family', '<p></p>', NULL, 14, NULL, NULL, 1),
(43, 4, 'Podcastlast Haarlem Special', 'Recording podcast with audience', '<p></p>', NULL, 11, NULL, NULL, 1),
(44, 4, 'De geschiedenis van familie ten Boom', 'Stories with impact', '<p></p>', NULL, 13, NULL, NULL, 1),
(45, 4, 'The history of the Ten Boom Family', 'Stories with impact', '<p></p>', NULL, 13, NULL, NULL, 1),
(46, 4, 'Winners of story telling competition, stories for Haarlem', 'Best off - the contest will be organized in June', '<p></p>', NULL, 10, NULL, NULL, 1);

-- ---------------------------------------------
-- RESTAURANT EVENTS (7 events)
-- EventTypeId = 5
-- ---------------------------------------------
INSERT INTO `Event` (`EventId`, `EventTypeId`, `Title`, `ShortDescription`, `LongDescriptionHtml`, `FeaturedImageAssetId`, `VenueId`, `ArtistId`, `RestaurantId`, `IsActive`) VALUES
(47, 5, 'Café de Roemer - Festival Dinner', 'Dutch, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 1, 1),
(48, 5, 'Ratatouille - Festival Dinner', 'French, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 2, 1),
(49, 5, 'Restaurant ML - Festival Dinner', 'Dutch, fish and seafood, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 3, 1),
(50, 5, 'Restaurant Fris - Festival Dinner', 'Dutch, French, European cuisine', '<p>4-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 4, 1),
(51, 5, 'New Vegas - Festival Dinner', 'Vegan cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 5, 1),
(52, 5, 'Grand Cafe Brinkman - Festival Dinner', 'Dutch, European, Modern cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 6, 1),
(53, 5, 'Urban Frenchy Bistro Toujours - Festival Dinner', 'Dutch, fish and seafood, European cuisine', '<p>3-star restaurant experience during Haarlem Festival</p>', NULL, NULL, NULL, 7, 1);

-- =============================================
-- EVENT SESSIONS
-- =============================================

-- ---------------------------------------------
-- JAZZ SESSIONS (24 sessions)
-- Patronaat: Thu-Sat with halls, paid
-- Grote Markt: Sunday, free
-- ---------------------------------------------
INSERT INTO `EventSession` (`EventSessionId`, `EventId`, `StartDateTime`, `EndDateTime`, `CapacityTotal`, `CapacitySingleTicketLimit`, `SoldSingleTickets`, `SoldReservedSeats`, `HallName`, `SessionType`, `DurationMinutes`, `LanguageCode`, `MinAge`, `MaxAge`, `ReservationRequired`, `IsFree`, `Notes`, `IsCancelled`, `IsActive`) VALUES
-- Thursday @ Patronaat Main Hall (300 seats, €15)
(1, 1, '2026-07-23 18:00:00', '2026-07-23 19:00:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €35,00, All-Access pass for Thu, Fri, Sat: €80,00.', 0, 1),
(2, 2, '2026-07-23 19:30:00', '2026-07-23 20:30:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
(3, 3, '2026-07-23 21:00:00', '2026-07-23 22:00:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
-- Thursday @ Patronaat Second Hall (200 seats, €10)
(4, 4, '2026-07-23 18:00:00', '2026-07-23 19:00:00', 200, 180, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
(5, 5, '2026-07-23 19:30:00', '2026-07-23 20:30:00', 200, 180, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
(6, 6, '2026-07-23 21:00:00', '2026-07-23 22:00:00', 200, 180, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
-- Friday @ Patronaat Main Hall (300 seats, €15)
(7, 7, '2026-07-24 18:00:00', '2026-07-24 19:00:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €35,00, All-Access pass for Thu, Fri, Sat: €80,00.', 0, 1),
(8, 8, '2026-07-24 19:30:00', '2026-07-24 20:30:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
(9, 9, '2026-07-24 21:00:00', '2026-07-24 22:00:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
-- Friday @ Patronaat Second Hall (200 seats, €10)
(10, 10, '2026-07-24 18:00:00', '2026-07-24 19:00:00', 200, 180, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
(11, 11, '2026-07-24 19:30:00', '2026-07-24 20:30:00', 200, 180, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
(12, 12, '2026-07-24 21:00:00', '2026-07-24 22:00:00', 200, 180, 0, 0, 'Second Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
-- Saturday @ Patronaat Main Hall (300 seats, €15)
(13, 13, '2026-07-25 18:00:00', '2026-07-25 19:00:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €35,00, All-Access pass for Thu, Fri, Sat: €80,00.', 0, 1),
(14, 14, '2026-07-25 19:30:00', '2026-07-25 20:30:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
(15, 15, '2026-07-25 21:00:00', '2026-07-25 22:00:00', 300, 270, 0, 0, 'Main Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
-- Saturday @ Patronaat Third Hall (150 seats, €10)
(16, 16, '2026-07-25 18:00:00', '2026-07-25 19:00:00', 150, 135, 0, 0, 'Third Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
(17, 17, '2026-07-25 19:30:00', '2026-07-25 20:30:00', 150, 135, 0, 0, 'Third Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
(18, 18, '2026-07-25 21:00:00', '2026-07-25 22:00:00', 150, 135, 0, 0, 'Third Hall', 'Live', 60, NULL, NULL, NULL, 0, 0, '', 0, 1),
-- Sunday @ Grote Markt (FREE, unlimited capacity)
(19, 19, '2026-07-26 15:00:00', '2026-07-26 16:00:00', 9999, 9999, 0, 0, NULL, 'Live', 60, NULL, NULL, NULL, 0, 1, 'Free for all visitors. No reservation needed.', 0, 1),
(20, 4, '2026-07-26 16:00:00', '2026-07-26 17:00:00', 9999, 9999, 0, 0, NULL, 'Live', 60, NULL, NULL, NULL, 0, 1, '', 0, 1),
(21, 2, '2026-07-26 17:00:00', '2026-07-26 18:00:00', 9999, 9999, 0, 0, NULL, 'Live', 60, NULL, NULL, NULL, 0, 1, '', 0, 1),
(22, 17, '2026-07-26 18:00:00', '2026-07-26 19:00:00', 9999, 9999, 0, 0, NULL, 'Live', 60, NULL, NULL, NULL, 0, 1, '', 0, 1),
(23, 1, '2026-07-26 19:00:00', '2026-07-26 20:00:00', 9999, 9999, 0, 0, NULL, 'Live', 60, NULL, NULL, NULL, 0, 1, '', 0, 1),
(24, 13, '2026-07-26 20:00:00', '2026-07-26 21:00:00', 9999, 9999, 0, 0, NULL, 'Live', 60, NULL, NULL, NULL, 0, 1, '', 0, 1);

-- ---------------------------------------------
-- DANCE SESSIONS (13 sessions)
-- ---------------------------------------------
INSERT INTO `EventSession` (`EventSessionId`, `EventId`, `StartDateTime`, `EndDateTime`, `CapacityTotal`, `CapacitySingleTicketLimit`, `SoldSingleTickets`, `SoldReservedSeats`, `HallName`, `SessionType`, `DurationMinutes`, `LanguageCode`, `MinAge`, `MaxAge`, `ReservationRequired`, `IsFree`, `Notes`, `IsCancelled`, `IsActive`) VALUES
-- Friday
(25, 20, '2026-07-24 20:00:00', '2026-07-25 02:00:00', 1500, 1350, 0, 0, NULL, 'Back2Back', 360, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €125,00, All-Access pass for Fri, Sat, Sun: €250,00.', 0, 1),
(26, 21, '2026-07-24 22:00:00', '2026-07-24 23:30:00', 200, 180, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, 1),
(27, 22, '2026-07-24 23:00:00', '2026-07-25 00:30:00', 300, 270, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, 1),
(28, 23, '2026-07-24 22:00:00', '2026-07-24 23:30:00', 200, 180, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, 1),
(29, 24, '2026-07-24 22:00:00', '2026-07-24 23:30:00', 200, 180, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, 1),
-- Saturday
(30, 25, '2026-07-25 14:00:00', '2026-07-25 23:00:00', 2000, 1800, 0, 0, NULL, 'Back2Back', 540, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €150,00, All-Access pass for Fri, Sat, Sun: €250,00.', 0, 1),
(31, 26, '2026-07-25 22:00:00', '2026-07-25 23:30:00', 300, 270, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, 1),
(32, 27, '2026-07-25 21:00:00', '2026-07-26 01:00:00', 1500, 1350, 0, 0, NULL, 'TiëstoWorld', 240, NULL, NULL, NULL, 0, 0, '', 0, 1),
(33, 28, '2026-07-25 23:00:00', '2026-07-26 00:30:00', 200, 180, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, 1),
-- Sunday
(34, 29, '2026-07-26 14:00:00', '2026-07-26 23:00:00', 2000, 1800, 0, 0, NULL, 'Back2Back', 540, NULL, NULL, NULL, 0, 0, 'All-Access pass for this day €150,00, All-Access pass for Fri, Sat, Sun: €250,00.', 0, 1),
(35, 30, '2026-07-26 19:00:00', '2026-07-26 20:30:00', 300, 270, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, 1),
(36, 31, '2026-07-26 21:00:00', '2026-07-26 22:30:00', 1500, 1350, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, 1),
(37, 32, '2026-07-26 18:00:00', '2026-07-26 19:30:00', 200, 180, 0, 0, NULL, 'Club', 90, NULL, NULL, NULL, 0, 0, '', 0, 1);

-- ---------------------------------------------
-- HISTORY SESSIONS (12 sessions)
-- Each session has multiple language tours
-- Capacity = (EN tours + NL tours + ZH tours) × 12 seats
-- ---------------------------------------------
INSERT INTO `EventSession` (`EventSessionId`, `EventId`, `StartDateTime`, `EndDateTime`, `CapacityTotal`, `CapacitySingleTicketLimit`, `SoldSingleTickets`, `SoldReservedSeats`, `HallName`, `SessionType`, `DurationMinutes`, `LanguageCode`, `MinAge`, `MaxAge`, `ReservationRequired`, `IsFree`, `Notes`, `IsCancelled`, `IsActive`) VALUES
-- Thursday (1 EN + 1 NL = 2 tours × 12 = 24 seats per session)
(38, 33, '2026-07-23 10:00:00', '2026-07-23 12:00:00', 24, 21, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, 'Due to the nature of this walk participants must be a minimum of 12 years old and no strollers are allowed. Groups will consist of 12 participants + 1 guide.', 0, 1),
(39, 33, '2026-07-23 13:00:00', '2026-07-23 15:00:00', 24, 21, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, 1),
(40, 33, '2026-07-23 16:00:00', '2026-07-23 18:00:00', 24, 21, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, 1),
-- Friday (sessions with varying Chinese tours)
(41, 33, '2026-07-24 10:00:00', '2026-07-24 12:00:00', 24, 21, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, 1),
(42, 33, '2026-07-24 13:00:00', '2026-07-24 15:00:00', 36, 32, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, 1), -- 1 EN + 1 NL + 1 ZH = 36
(43, 33, '2026-07-24 16:00:00', '2026-07-24 18:00:00', 24, 21, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, 1),
-- Saturday (more tours)
(44, 33, '2026-07-25 10:00:00', '2026-07-25 12:00:00', 48, 43, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, 1), -- 2 EN + 2 NL = 48
(45, 33, '2026-07-25 13:00:00', '2026-07-25 15:00:00', 60, 54, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, 1), -- 2 EN + 2 NL + 1 ZH = 60
(46, 33, '2026-07-25 16:00:00', '2026-07-25 18:00:00', 36, 32, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, 1), -- 1 EN + 1 NL + 1 ZH = 36
-- Sunday (most tours)
(47, 33, '2026-07-26 10:00:00', '2026-07-26 12:00:00', 60, 54, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, 1), -- 2 EN + 2 NL + 1 ZH = 60
(48, 33, '2026-07-26 13:00:00', '2026-07-26 15:00:00', 96, 86, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, 1), -- 3 EN + 3 NL + 2 ZH = 96
(49, 33, '2026-07-26 16:00:00', '2026-07-26 18:00:00', 24, 21, 0, 0, NULL, 'Tour', 120, NULL, 12, NULL, 1, 0, '', 0, 1); -- 1 EN + 1 NL = 24

-- ---------------------------------------------
-- STORYTELLING SESSIONS (15 sessions)
-- Capacity = 100 (default for indoor venues)
-- ---------------------------------------------
INSERT INTO `EventSession` (`EventSessionId`, `EventId`, `StartDateTime`, `EndDateTime`, `CapacityTotal`, `CapacitySingleTicketLimit`, `SoldSingleTickets`, `SoldReservedSeats`, `HallName`, `SessionType`, `DurationMinutes`, `LanguageCode`, `MinAge`, `MaxAge`, `ReservationRequired`, `IsFree`, `Notes`, `IsCancelled`, `IsActive`) VALUES
-- Thursday
(50, 34, '2026-07-23 16:00:00', '2026-07-23 17:00:00', 100, 90, 0, 0, NULL, 'Storytelling', 60, 'NL', 4, NULL, 0, 0, 'Stories for the whole family', 0, 1),
(51, 35, '2026-07-23 19:00:00', '2026-07-23 20:15:00', 100, 90, 0, 0, NULL, 'Podcast', 75, 'NL', 16, NULL, 0, 0, 'Recording podcast with audience', 0, 1),
(52, 36, '2026-07-23 20:30:00', '2026-07-23 21:45:00', 100, 90, 0, 0, NULL, 'Storytelling', 75, 'ENG', 16, NULL, 0, 0, 'Stories with impact', 0, 1),
-- Friday
(53, 37, '2026-07-24 16:00:00', '2026-07-24 17:00:00', 80, 72, 0, 0, NULL, 'Storytelling', 60, 'NL', 10, NULL, 0, 0, 'Stories for the whole family', 0, 1),
(54, 38, '2026-07-24 19:00:00', '2026-07-24 20:30:00', 100, 90, 0, 0, NULL, 'Storytelling', 90, 'NL', 12, NULL, 0, 0, 'Best off - the contest will be organized in June', 0, 1),
(55, 39, '2026-07-24 19:00:00', '2026-07-24 20:15:00', 100, 90, 0, 0, NULL, 'Storytelling', 75, 'NL', 16, NULL, 0, 0, 'Stories with impact', 0, 1),
(56, 40, '2026-07-24 20:30:00', '2026-07-24 21:45:00', 100, 90, 0, 0, NULL, 'Podcast', 75, 'ENG', 16, NULL, 0, 0, 'Recording podcast with audience', 0, 1),
-- Saturday
(57, 41, '2026-07-25 10:00:00', '2026-07-25 11:00:00', 100, 90, 0, 0, NULL, 'Storytelling', 60, 'NL', 2, 102, 0, 0, 'Stories for the whole family', 0, 1),
(58, 42, '2026-07-25 15:00:00', '2026-07-25 16:00:00', 100, 90, 0, 0, NULL, 'Storytelling', 60, 'ENG', 2, 102, 0, 0, 'Stories for the whole family', 0, 1),
(59, 43, '2026-07-25 14:00:00', '2026-07-25 15:15:00', 100, 90, 0, 0, NULL, 'Podcast', 75, 'NL', 12, NULL, 0, 0, 'Recording podcast with audience', 0, 1),
(60, 44, '2026-07-25 13:00:00', '2026-07-25 14:30:00', 80, 72, 0, 0, NULL, 'Storytelling', 90, 'NL', 12, NULL, 0, 0, 'Stories with impact', 0, 1),
-- Sunday
(61, 42, '2026-07-26 10:00:00', '2026-07-26 11:00:00', 100, 90, 0, 0, NULL, 'Storytelling', 60, 'ENG', 2, 102, 0, 0, 'Stories for the whole family', 0, 1),
(62, 41, '2026-07-26 15:00:00', '2026-07-26 16:00:00', 100, 90, 0, 0, NULL, 'Storytelling', 60, 'NL', 2, 102, 0, 0, 'Stories for the whole family', 0, 1),
(63, 45, '2026-07-26 13:00:00', '2026-07-26 14:30:00', 80, 72, 0, 0, NULL, 'Storytelling', 90, 'ENG', 12, NULL, 0, 0, 'Stories with impact', 0, 1),
(64, 46, '2026-07-26 16:00:00', '2026-07-26 17:30:00', 100, 90, 0, 0, NULL, 'Storytelling', 90, 'ENG', 12, NULL, 0, 0, 'Best off - the contest will be organized in June', 0, 1);

-- ---------------------------------------------
-- RESTAURANT SESSIONS (18 sessions)
-- Sessions derived: FirstSession + n × Duration
-- ---------------------------------------------
INSERT INTO `EventSession` (`EventSessionId`, `EventId`, `StartDateTime`, `EndDateTime`, `CapacityTotal`, `CapacitySingleTicketLimit`, `SoldSingleTickets`, `SoldReservedSeats`, `HallName`, `SessionType`, `DurationMinutes`, `LanguageCode`, `MinAge`, `MaxAge`, `ReservationRequired`, `IsFree`, `Notes`, `IsCancelled`, `IsActive`) VALUES
-- Café de Roemer: 3 sessions, 1.5h duration, first at 18:00, 35 seats
(65, 47, '2026-07-24 18:00:00', '2026-07-24 19:30:00', 35, 31, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
(66, 47, '2026-07-24 19:30:00', '2026-07-24 21:00:00', 35, 31, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
(67, 47, '2026-07-24 21:00:00', '2026-07-24 22:30:00', 35, 31, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
-- Ratatouille: 3 sessions, 2h duration, first at 17:00, 52 seats
(68, 48, '2026-07-24 17:00:00', '2026-07-24 19:00:00', 52, 46, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', 0, 1),
(69, 48, '2026-07-24 19:00:00', '2026-07-24 21:00:00', 52, 46, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', 0, 1),
(70, 48, '2026-07-24 21:00:00', '2026-07-24 23:00:00', 52, 46, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', 0, 1),
-- Restaurant ML: 2 sessions, 2h duration, first at 17:00, 60 seats
(71, 49, '2026-07-24 17:00:00', '2026-07-24 19:00:00', 60, 54, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', 0, 1),
(72, 49, '2026-07-24 19:00:00', '2026-07-24 21:00:00', 60, 54, 0, 0, NULL, 'Dinner', 120, NULL, NULL, NULL, 1, 0, '', 0, 1),
-- Restaurant Fris: 3 sessions, 1.5h duration, first at 17:30, 45 seats
(73, 50, '2026-07-24 17:30:00', '2026-07-24 19:00:00', 45, 40, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
(74, 50, '2026-07-24 19:00:00', '2026-07-24 20:30:00', 45, 40, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
(75, 50, '2026-07-24 20:30:00', '2026-07-24 22:00:00', 45, 40, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
-- New Vegas: 3 sessions, 1.5h duration, first at 17:00, 36 seats
(76, 51, '2026-07-24 17:00:00', '2026-07-24 18:30:00', 36, 32, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
(77, 51, '2026-07-24 18:30:00', '2026-07-24 20:00:00', 36, 32, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
(78, 51, '2026-07-24 20:00:00', '2026-07-24 21:30:00', 36, 32, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
-- Grand Cafe Brinkman: 3 sessions, 1.5h duration, first at 16:30, 100 seats
(79, 52, '2026-07-24 16:30:00', '2026-07-24 18:00:00', 100, 90, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
(80, 52, '2026-07-24 18:00:00', '2026-07-24 19:30:00', 100, 90, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
(81, 52, '2026-07-24 19:30:00', '2026-07-24 21:00:00', 100, 90, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
-- Urban Frenchy Bistro Toujours: 3 sessions, 1.5h duration, first at 17:30, 48 seats
(82, 53, '2026-07-24 17:30:00', '2026-07-24 19:00:00', 48, 43, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
(83, 53, '2026-07-24 19:00:00', '2026-07-24 20:30:00', 48, 43, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1),
(84, 53, '2026-07-24 20:30:00', '2026-07-24 22:00:00', 48, 43, 0, 0, NULL, 'Dinner', 90, NULL, NULL, NULL, 1, 0, '', 0, 1);

-- =============================================
-- EVENT SESSION PRICES
-- =============================================

-- ---------------------------------------------
-- JAZZ PRICES
-- Main Hall: €15 Adult, Second/Third Hall: €10 Adult
-- Sunday Grote Markt: Free (€0)
-- PriceTierId: 1=Adult
-- ---------------------------------------------
INSERT INTO `EventSessionPrice` (`EventSessionPriceId`, `EventSessionId`, `PriceTierId`, `Price`, `CurrencyCode`, `VatRate`) VALUES
-- Thursday Main Hall (€15)
(1, 1, 1, 15.00, 'EUR', 21.00),
(2, 2, 1, 15.00, 'EUR', 21.00),
(3, 3, 1, 15.00, 'EUR', 21.00),
-- Thursday Second Hall (€10)
(4, 4, 1, 10.00, 'EUR', 21.00),
(5, 5, 1, 10.00, 'EUR', 21.00),
(6, 6, 1, 10.00, 'EUR', 21.00),
-- Friday Main Hall (€15)
(7, 7, 1, 15.00, 'EUR', 21.00),
(8, 8, 1, 15.00, 'EUR', 21.00),
(9, 9, 1, 15.00, 'EUR', 21.00),
-- Friday Second Hall (€10)
(10, 10, 1, 10.00, 'EUR', 21.00),
(11, 11, 1, 10.00, 'EUR', 21.00),
(12, 12, 1, 10.00, 'EUR', 21.00),
-- Saturday Main Hall (€15)
(13, 13, 1, 15.00, 'EUR', 21.00),
(14, 14, 1, 15.00, 'EUR', 21.00),
(15, 15, 1, 15.00, 'EUR', 21.00),
-- Saturday Third Hall (€10)
(16, 16, 1, 10.00, 'EUR', 21.00),
(17, 17, 1, 10.00, 'EUR', 21.00),
(18, 18, 1, 10.00, 'EUR', 21.00),
-- Sunday Grote Markt (Free)
(19, 19, 1, 0.00, 'EUR', 21.00),
(20, 20, 1, 0.00, 'EUR', 21.00),
(21, 21, 1, 0.00, 'EUR', 21.00),
(22, 22, 1, 0.00, 'EUR', 21.00),
(23, 23, 1, 0.00, 'EUR', 21.00),
(24, 24, 1, 0.00, 'EUR', 21.00);

-- ---------------------------------------------
-- DANCE PRICES
-- Back2Back Lichtfabriek: €75, Club: €60, Back2Back Caprera: €110
-- TiëstoWorld: €75, Sunday XO Club: €90
-- PriceTierId: 1=Adult
-- ---------------------------------------------
INSERT INTO `EventSessionPrice` (`EventSessionPriceId`, `EventSessionId`, `PriceTierId`, `Price`, `CurrencyCode`, `VatRate`) VALUES
-- Friday
(25, 25, 1, 75.00, 'EUR', 21.00),  -- Nicky Romero / Afrojack Back2Back
(26, 26, 1, 60.00, 'EUR', 21.00),  -- Tiësto Club
(27, 27, 1, 60.00, 'EUR', 21.00),  -- Hardwell Club
(28, 28, 1, 60.00, 'EUR', 21.00),  -- Armin van Buuren Club
(29, 29, 1, 60.00, 'EUR', 21.00),  -- Martin Garrix Club
-- Saturday
(30, 30, 1, 110.00, 'EUR', 21.00), -- Hardwell/Garrix/Armin Back2Back
(31, 31, 1, 60.00, 'EUR', 21.00),  -- Afrojack Club
(32, 32, 1, 75.00, 'EUR', 21.00),  -- Tiësto TiëstoWorld
(33, 33, 1, 60.00, 'EUR', 21.00),  -- Nicky Romero Club
-- Sunday
(34, 34, 1, 110.00, 'EUR', 21.00), -- Afrojack/Tiësto/Nicky Back2Back
(35, 35, 1, 60.00, 'EUR', 21.00),  -- Armin van Buuren Club
(36, 36, 1, 90.00, 'EUR', 21.00),  -- Hardwell Club (XO, bigger venue)
(37, 37, 1, 60.00, 'EUR', 21.00);  -- Martin Garrix Club

-- ---------------------------------------------
-- HISTORY PRICES
-- Adult: €17.50, Family (4 persons max): €60.00
-- PriceTierId: 1=Adult, 3=Family
-- ---------------------------------------------
INSERT INTO `EventSessionPrice` (`EventSessionPriceId`, `EventSessionId`, `PriceTierId`, `Price`, `CurrencyCode`, `VatRate`) VALUES
-- Thursday
(38, 38, 1, 17.50, 'EUR', 21.00),
(39, 38, 3, 60.00, 'EUR', 21.00),
(40, 39, 1, 17.50, 'EUR', 21.00),
(41, 39, 3, 60.00, 'EUR', 21.00),
(42, 40, 1, 17.50, 'EUR', 21.00),
(43, 40, 3, 60.00, 'EUR', 21.00),
-- Friday
(44, 41, 1, 17.50, 'EUR', 21.00),
(45, 41, 3, 60.00, 'EUR', 21.00),
(46, 42, 1, 17.50, 'EUR', 21.00),
(47, 42, 3, 60.00, 'EUR', 21.00),
(48, 43, 1, 17.50, 'EUR', 21.00),
(49, 43, 3, 60.00, 'EUR', 21.00),
-- Saturday
(50, 44, 1, 17.50, 'EUR', 21.00),
(51, 44, 3, 60.00, 'EUR', 21.00),
(52, 45, 1, 17.50, 'EUR', 21.00),
(53, 45, 3, 60.00, 'EUR', 21.00),
(54, 46, 1, 17.50, 'EUR', 21.00),
(55, 46, 3, 60.00, 'EUR', 21.00),
-- Sunday
(56, 47, 1, 17.50, 'EUR', 21.00),
(57, 47, 3, 60.00, 'EUR', 21.00),
(58, 48, 1, 17.50, 'EUR', 21.00),
(59, 48, 3, 60.00, 'EUR', 21.00),
(60, 49, 1, 17.50, 'EUR', 21.00),
(61, 49, 3, 60.00, 'EUR', 21.00);

-- ---------------------------------------------
-- STORYTELLING PRICES
-- Various prices: €6, €10, €12.50, or Pay-as-you-like (€0)
-- PriceTierId: 1=Adult, 5=PayWhatYouLike
-- ---------------------------------------------
INSERT INTO `EventSessionPrice` (`EventSessionPriceId`, `EventSessionId`, `PriceTierId`, `Price`, `CurrencyCode`, `VatRate`) VALUES
-- Thursday
(62, 50, 1, 6.00, 'EUR', 21.00),   -- Winnie de Poeh €6
(63, 51, 1, 12.50, 'EUR', 21.00), -- Omdenken Podcast €12.50
(64, 52, 5, 0.00, 'EUR', 21.00),   -- The story of Buurderij Haarlem - pay as you like
-- Friday
(65, 53, 5, 0.00, 'EUR', 21.00),   -- Corrie voor kinderen - pay as you like
(66, 54, 1, 12.50, 'EUR', 21.00), -- Winnaars wedstrijd €12.50
(67, 55, 5, 0.00, 'EUR', 21.00),   -- Het verhaal van de Oeserzwammerij - pay as you like
(68, 56, 1, 12.50, 'EUR', 21.00), -- Flip Thinking Podcast €12.50
-- Saturday
(69, 57, 1, 10.00, 'EUR', 21.00), -- Meneer Anansi €10
(70, 58, 1, 10.00, 'EUR', 21.00), -- Mister Anansi €10
(71, 59, 1, 12.50, 'EUR', 21.00), -- Podcastlast Haarlem Special €12.50
(72, 60, 5, 0.00, 'EUR', 21.00),   -- De geschiedenis van familie ten Boom - pay as you like
-- Sunday
(73, 61, 1, 10.00, 'EUR', 21.00), -- Mister Anansi €10
(74, 62, 1, 10.00, 'EUR', 21.00), -- Meneer Anansi €10
(75, 63, 5, 0.00, 'EUR', 21.00),   -- The history of the Ten Boom Family - pay as you like
(76, 64, 1, 12.50, 'EUR', 21.00); -- Winners of story telling competition €12.50

-- ---------------------------------------------
-- RESTAURANT PRICES
-- Adult prices: €35 or €45
-- ChildU12 (reduced): €17.50 or €22.50
-- PriceTierId: 1=Adult, 2=ChildU12
-- ---------------------------------------------
INSERT INTO `EventSessionPrice` (`EventSessionPriceId`, `EventSessionId`, `PriceTierId`, `Price`, `CurrencyCode`, `VatRate`) VALUES
-- Café de Roemer: Adult €35, Child €17.50
(77, 65, 1, 35.00, 'EUR', 9.00),
(78, 65, 2, 17.50, 'EUR', 9.00),
(79, 66, 1, 35.00, 'EUR', 9.00),
(80, 66, 2, 17.50, 'EUR', 9.00),
(81, 67, 1, 35.00, 'EUR', 9.00),
(82, 67, 2, 17.50, 'EUR', 9.00),
-- Ratatouille: Adult €45, Child €22.50
(83, 68, 1, 45.00, 'EUR', 9.00),
(84, 68, 2, 22.50, 'EUR', 9.00),
(85, 69, 1, 45.00, 'EUR', 9.00),
(86, 69, 2, 22.50, 'EUR', 9.00),
(87, 70, 1, 45.00, 'EUR', 9.00),
(88, 70, 2, 22.50, 'EUR', 9.00),
-- Restaurant ML: Adult €45, Child €22.50
(89, 71, 1, 45.00, 'EUR', 9.00),
(90, 71, 2, 22.50, 'EUR', 9.00),
(91, 72, 1, 45.00, 'EUR', 9.00),
(92, 72, 2, 22.50, 'EUR', 9.00),
-- Restaurant Fris: Adult €45, Child €22.50
(93, 73, 1, 45.00, 'EUR', 9.00),
(94, 73, 2, 22.50, 'EUR', 9.00),
(95, 74, 1, 45.00, 'EUR', 9.00),
(96, 74, 2, 22.50, 'EUR', 9.00),
(97, 75, 1, 45.00, 'EUR', 9.00),
(98, 75, 2, 22.50, 'EUR', 9.00),
-- New Vegas: Adult €35, Child €17.50
(99, 76, 1, 35.00, 'EUR', 9.00),
(100, 76, 2, 17.50, 'EUR', 9.00),
(101, 77, 1, 35.00, 'EUR', 9.00),
(102, 77, 2, 17.50, 'EUR', 9.00),
(103, 78, 1, 35.00, 'EUR', 9.00),
(104, 78, 2, 17.50, 'EUR', 9.00),
-- Grand Cafe Brinkman: Adult €35, Child €17.50
(105, 79, 1, 35.00, 'EUR', 9.00),
(106, 79, 2, 17.50, 'EUR', 9.00),
(107, 80, 1, 35.00, 'EUR', 9.00),
(108, 80, 2, 17.50, 'EUR', 9.00),
(109, 81, 1, 35.00, 'EUR', 9.00),
(110, 81, 2, 17.50, 'EUR', 9.00),
-- Urban Frenchy Bistro Toujours: Adult €35, Child €17.50
(111, 82, 1, 35.00, 'EUR', 9.00),
(112, 82, 2, 17.50, 'EUR', 9.00),
(113, 83, 1, 35.00, 'EUR', 9.00),
(114, 83, 2, 17.50, 'EUR', 9.00),
(115, 84, 1, 35.00, 'EUR', 9.00),
(116, 84, 2, 17.50, 'EUR', 9.00);

-- =============================================
-- HISTORY TOURS (per session, per language)
-- =============================================
INSERT INTO `HistoryTour` (`HistoryTourId`, `EventSessionId`, `LanguageCode`, `GuideCount`, `SeatsPerTour`) VALUES
-- Thursday 10:00 (1 EN, 1 NL)
(1, 38, 'ENG', 1, 12),
(2, 38, 'NL', 1, 12),
-- Thursday 13:00 (1 EN, 1 NL)
(3, 39, 'ENG', 1, 12),
(4, 39, 'NL', 1, 12),
-- Thursday 16:00 (1 EN, 1 NL)
(5, 40, 'ENG', 1, 12),
(6, 40, 'NL', 1, 12),
-- Friday 10:00 (1 EN, 1 NL)
(7, 41, 'ENG', 1, 12),
(8, 41, 'NL', 1, 12),
-- Friday 13:00 (1 EN, 1 NL, 1 ZH)
(9, 42, 'ENG', 1, 12),
(10, 42, 'NL', 1, 12),
(11, 42, 'ZH', 1, 12),
-- Friday 16:00 (1 EN, 1 NL)
(12, 43, 'ENG', 1, 12),
(13, 43, 'NL', 1, 12),
-- Saturday 10:00 (2 EN, 2 NL)
(14, 44, 'ENG', 2, 12),
(15, 44, 'NL', 2, 12),
-- Saturday 13:00 (2 EN, 2 NL, 1 ZH)
(16, 45, 'ENG', 2, 12),
(17, 45, 'NL', 2, 12),
(18, 45, 'ZH', 1, 12),
-- Saturday 16:00 (1 EN, 1 NL, 1 ZH)
(19, 46, 'ENG', 1, 12),
(20, 46, 'NL', 1, 12),
(21, 46, 'ZH', 1, 12),
-- Sunday 10:00 (2 EN, 2 NL, 1 ZH)
(22, 47, 'ENG', 2, 12),
(23, 47, 'NL', 2, 12),
(24, 47, 'ZH', 1, 12),
-- Sunday 13:00 (3 EN, 3 NL, 2 ZH)
(25, 48, 'ENG', 3, 12),
(26, 48, 'NL', 3, 12),
(27, 48, 'ZH', 2, 12),
-- Sunday 16:00 (1 EN, 1 NL)
(28, 49, 'ENG', 1, 12),
(29, 49, 'NL', 1, 12);

-- =============================================
-- HISTORY TOUR GUIDES (link tours to guides)
-- =============================================
INSERT INTO `HistoryTourGuide` (`HistoryTourId`, `GuideId`) VALUES
-- Thursday 10:00
(1, 1),  -- EN tour -> Guide EN-1
(2, 4),  -- NL tour -> Guide NL-1
-- Thursday 13:00
(3, 1),
(4, 4),
-- Thursday 16:00
(5, 1),
(6, 4),
-- Friday 10:00
(7, 1),
(8, 4),
-- Friday 13:00 (includes Chinese)
(9, 1),
(10, 4),
(11, 7),  -- ZH tour -> Guide ZH-1
-- Friday 16:00
(12, 1),
(13, 4),
-- Saturday 10:00 (2 EN, 2 NL)
(14, 1),
(14, 2),  -- Second EN guide
(15, 4),
(15, 5),  -- Second NL guide
-- Saturday 13:00 (2 EN, 2 NL, 1 ZH)
(16, 1),
(16, 2),
(17, 4),
(17, 5),
(18, 7),
-- Saturday 16:00 (1 EN, 1 NL, 1 ZH)
(19, 1),
(20, 4),
(21, 7),
-- Sunday 10:00 (2 EN, 2 NL, 1 ZH)
(22, 1),
(22, 2),
(23, 4),
(23, 5),
(24, 7),
-- Sunday 13:00 (3 EN, 3 NL, 2 ZH)
(25, 1),
(25, 2),
(25, 3),  -- Third EN guide
(26, 4),
(26, 5),
(26, 6),  -- Third NL guide
(27, 7),
(27, 8),  -- Second ZH guide
-- Sunday 16:00
(28, 1),
(29, 4);

SET FOREIGN_KEY_CHECKS = 1;

COMMIT;

