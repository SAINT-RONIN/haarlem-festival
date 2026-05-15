-- Migration: Move restaurant structured data from CMS to Event table
-- Date: 2026-05-08
-- Description:
--   1. Add restaurant-specific columns to Event table
--   2. Create Venue records for each restaurant and link them
--   3. Migrate CMS data (stars, cuisine, price, duration, time_slots) into Event columns
--   4. Update existing Reservation DiningDate from day names to real dates
--   5. Clean up migrated CmsItem rows
--   6. Fix intro_body CMS content (subsections now read from separate CMS items)
--
-- Affected tables: Event, Venue, CmsItem, Reservation
-- Reversible: Yes (see rollback section at the bottom)

START TRANSACTION;

-- =====================================================================
-- STEP 1: Add restaurant columns to Event
-- =====================================================================

ALTER TABLE `Event`
  ADD COLUMN `Stars` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `IsActive`,
  ADD COLUMN `MichelinStars` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `Stars`,
  ADD COLUMN `CuisineType` VARCHAR(200) DEFAULT NULL AFTER `MichelinStars`,
  ADD COLUMN `PriceAdult` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `CuisineType`,
  ADD COLUMN `DurationMinutes` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `PriceAdult`,
  ADD COLUMN `SeatsPerSession` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `DurationMinutes`,
  ADD COLUMN `TimeSlots` VARCHAR(200) DEFAULT NULL AFTER `SeatsPerSession`;

-- =====================================================================
-- STEP 2: Create Venue records for restaurants, link via VenueId
-- =====================================================================

INSERT INTO `Venue` (`Name`, `AddressLine`, `City`) VALUES
  ('Café de Roemer', 'Botermarkt 17, 2011 XL Haarlem', 'Haarlem'),
  ('Ratatouille', 'Spaarne 96, 2011 CL Haarlem', 'Haarlem'),
  ('Restaurant ML', 'Kleine Houtstraat 70, 2011 DR Haarlem', 'Haarlem'),
  ('Restaurant Fris', 'Twijnderslaan 7, 2012 BG Haarlem', 'Haarlem'),
  ('New Vegas', 'Koningstraat 5, 2011 TB Haarlem', 'Haarlem'),
  ('Grand Cafe Brinkman', 'Grote Markt 13, 2011 RC Haarlem', 'Haarlem'),
  ('Urban Frenchy Bistro Toujours', 'Oude Groenmarkt 10-12, 2011 HL Haarlem', 'Haarlem');

-- Link each restaurant event to its new venue.
-- Uses subquery to find VenueId by name (safe if venue names are unique).
UPDATE `Event` SET `VenueId` = (SELECT `VenueId` FROM `Venue` WHERE `Name` = 'Café de Roemer' LIMIT 1) WHERE `EventId` = 47;
UPDATE `Event` SET `VenueId` = (SELECT `VenueId` FROM `Venue` WHERE `Name` = 'Ratatouille' LIMIT 1) WHERE `EventId` = 48;
UPDATE `Event` SET `VenueId` = (SELECT `VenueId` FROM `Venue` WHERE `Name` = 'Restaurant ML' LIMIT 1) WHERE `EventId` = 49;
UPDATE `Event` SET `VenueId` = (SELECT `VenueId` FROM `Venue` WHERE `Name` = 'Restaurant Fris' LIMIT 1) WHERE `EventId` = 50;
UPDATE `Event` SET `VenueId` = (SELECT `VenueId` FROM `Venue` WHERE `Name` = 'New Vegas' LIMIT 1) WHERE `EventId` = 51;
UPDATE `Event` SET `VenueId` = (SELECT `VenueId` FROM `Venue` WHERE `Name` = 'Grand Cafe Brinkman' LIMIT 1) WHERE `EventId` = 52;
UPDATE `Event` SET `VenueId` = (SELECT `VenueId` FROM `Venue` WHERE `Name` = 'Urban Frenchy Bistro Toujours' LIMIT 1) WHERE `EventId` = 53;

-- =====================================================================
-- STEP 3: Migrate CMS data into Event columns
-- =====================================================================

-- EventId 47 (Café de Roemer) - CmsSection 93
UPDATE `Event` SET `Stars` = 4, `CuisineType` = 'Dutch, fish and seafood, European', `PriceAdult` = 45.00, `DurationMinutes` = 120, `SeatsPerSession` = 35, `TimeSlots` = '16:30, 18:30, 20:30' WHERE `EventId` = 47;

-- EventId 48 (Ratatouille) - CmsSection 94
UPDATE `Event` SET `Stars` = 4, `MichelinStars` = 1, `CuisineType` = 'French, fish and seafood, European', `PriceAdult` = 45.00, `DurationMinutes` = 120, `SeatsPerSession` = 52, `TimeSlots` = '17:00, 19:15, 21:30' WHERE `EventId` = 48;

-- EventId 49 (Restaurant ML) - CmsSection 95
UPDATE `Event` SET `Stars` = 4, `CuisineType` = 'Dutch, fish and seafood, European', `PriceAdult` = 45.00, `DurationMinutes` = 120, `SeatsPerSession` = 60, `TimeSlots` = '16:30, 18:30, 20:30' WHERE `EventId` = 49;

-- EventId 50 (Restaurant Fris) - CmsSection 96
UPDATE `Event` SET `Stars` = 4, `CuisineType` = 'Dutch, French, European', `PriceAdult` = 45.00, `DurationMinutes` = 120, `SeatsPerSession` = 45, `TimeSlots` = '16:30, 18:30, 20:30' WHERE `EventId` = 50;

-- EventId 51 (New Vegas) - CmsSection 97
UPDATE `Event` SET `Stars` = 3, `CuisineType` = 'Vegan', `PriceAdult` = 35.00, `DurationMinutes` = 120, `SeatsPerSession` = 36, `TimeSlots` = '16:30, 18:30, 20:30' WHERE `EventId` = 51;

-- EventId 52 (Grand Cafe Brinkman) - CmsSection 98
UPDATE `Event` SET `Stars` = 3, `CuisineType` = 'Dutch, European, Modern', `PriceAdult` = 35.00, `DurationMinutes` = 120, `SeatsPerSession` = 100, `TimeSlots` = '16:30, 18:30, 20:30' WHERE `EventId` = 52;

-- EventId 53 (Urban Frenchy Bistro Toujours) - CmsSection 99
UPDATE `Event` SET `Stars` = 3, `CuisineType` = 'Dutch, fish and seafood, European', `PriceAdult` = 35.00, `DurationMinutes` = 120, `SeatsPerSession` = 48, `TimeSlots` = '17:30, 19:15, 21:00' WHERE `EventId` = 53;

-- =====================================================================
-- STEP 4: Update existing Reservation DiningDate to real dates
-- =====================================================================
-- Festival 2026: Thu=2026-07-23, Fri=2026-07-24, Sat=2026-07-25, Sun=2026-07-26
-- Only 2 existing rows, both with 'Thursday'.

UPDATE `Reservation` SET `DiningDate` = '2026-07-23' WHERE `DiningDate` = 'Thursday';
UPDATE `Reservation` SET `DiningDate` = '2026-07-24' WHERE `DiningDate` = 'Friday';
UPDATE `Reservation` SET `DiningDate` = '2026-07-25' WHERE `DiningDate` = 'Saturday';
UPDATE `Reservation` SET `DiningDate` = '2026-07-26' WHERE `DiningDate` = 'Sunday';

-- =====================================================================
-- STEP 5: Clean up migrated CmsItem rows
-- =====================================================================
-- Remove the structured-data CmsItem rows that are now on Event/Venue.
-- Only for the 7 active restaurant CmsSections (93-99).
-- Keeps CMS content fields (about_text, chef_*, menu_*, gallery_*, etc.)

DELETE FROM `CmsItem`
WHERE `CmsSectionId` IN (93, 94, 95, 96, 97, 98, 99)
  AND `ItemKey` IN (
    'stars', 'cuisine_type', 'price_adult', 'duration_minutes',
    'seats_per_session', 'time_slots', 'michelin_stars',
    'address_line', 'city', 'phone', 'email', 'website'
  );

-- =====================================================================
-- STEP 6: Fix intro_body CMS content
-- =====================================================================
-- The intro_body had subsection text baked in. Subsections are now read
-- from their own CMS items (intro_sub1_heading, intro_sub1_text, etc.),
-- so intro_body should only contain the welcome paragraph.

UPDATE `CmsItem`
SET `TextValue` = 'Welcome to Yummy!, the food experience of the Haarlem Festival. Four days where some of the city''s favorite restaurants open their doors with special menus made just for this event.'
WHERE `CmsSectionId` = 57 AND `ItemKey` = 'intro_body';

-- =====================================================================
-- STEP 7: Add proper ShortDescription for all restaurants
-- =====================================================================
-- These are shown on the restaurant cards. Previously most had just the
-- cuisine type or a generic placeholder.

UPDATE `Event` SET `ShortDescription` = 'A cozy spot on the Botermarkt serving the best of Dutch and European cuisine, with a focus on fresh North Sea fish and seasonal seafood.'
WHERE `EventId` = 47;

UPDATE `Event` SET `ShortDescription` = 'One of Haarlem''s finest French restaurants, offering an exquisite blend of classic French technique with locally sourced North Sea seafood and European flavours.'
WHERE `EventId` = 48;

UPDATE `Event` SET `ShortDescription` = 'Located on the charming Kleine Houtstraat, Restaurant ML combines traditional Dutch recipes with modern European flair and the freshest seasonal ingredients.'
WHERE `EventId` = 49;

UPDATE `Event` SET `ShortDescription` = 'A stylish restaurant in a quiet Haarlem neighbourhood, blending Dutch and French culinary traditions into refined European dishes with a contemporary twist.'
WHERE `EventId` = 50;

UPDATE `Event` SET `ShortDescription` = 'Haarlem''s go-to destination for creative vegan cuisine, proving that plant-based dining can be bold, flavourful, and satisfying.'
WHERE `EventId` = 51;

UPDATE `Event` SET `ShortDescription` = 'A beloved grand café on the Grote Markt, offering hearty Dutch and modern European dishes in a lively atmosphere with views of the historic square.'
WHERE `EventId` = 52;

UPDATE `Event` SET `ShortDescription` = 'A French-inspired bistro on the Oude Groenmarkt, blending Dutch coastal traditions with bistro culture for fresh fish, seafood, and a warm atmosphere.'
WHERE `EventId` = 53;

-- Also fix the generic "3-star/4-star" text in about_text CMS for New Vegas
UPDATE `CmsItem`
SET `TextValue` = 'New Vegas brings a fresh perspective to Haarlem''s dining scene with its fully vegan menu. Creative dishes crafted from locally sourced ingredients prove that plant-based cuisine can be exciting, satisfying, and full of flavour.'
WHERE `CmsSectionId` = 97 AND `ItemKey` = 'about_text';

-- =====================================================================
-- STEP 8: Add location_description for all restaurants
-- =====================================================================

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`) VALUES
  (93, 'location_description', 'TEXT', 'Situated on the lively Botermarkt, Café de Roemer is right in the centre of Haarlem, just a minute''s walk from the Grote Markt. Its central location makes it an ideal stop before or after exploring the festival.'),
  (94, 'location_description', 'TEXT', 'Nestled along the scenic Spaarne river, Ratatouille enjoys one of Haarlem''s most picturesque settings. The restaurant is a short walk from the Grote Markt and easily accessible by public transport or on foot through the city centre.'),
  (95, 'location_description', 'TEXT', 'Found on the bustling Kleine Houtstraat, one of Haarlem''s favourite shopping streets, Restaurant ML is easy to reach on foot from the station or the Grote Markt.'),
  (96, 'location_description', 'TEXT', 'Tucked away on the quiet Twijnderslaan, Restaurant Fris offers a peaceful retreat just a few minutes from the city centre. A perfect escape from the festival buzz while staying close to the action.'),
  (97, 'location_description', 'TEXT', 'Located on the Koningstraat near the heart of Haarlem, New Vegas is within walking distance of the main festival areas. Its central spot makes it easy to combine dinner with other festival events.'),
  (98, 'location_description', 'TEXT', 'Right on the Grote Markt, Grand Cafe Brinkman offers front-row seats to Haarlem''s most iconic square. Watch the city come alive during the festival from one of the best terraces in town.'),
  (99, 'location_description', 'TEXT', 'Located on the charming Oude Groenmarkt, just steps from the Grote Kerk, Toujours sits at the heart of Haarlem''s historic centre. The terrace offers a lovely view of the square, perfect for a relaxed evening during the festival.');

-- =====================================================================
-- STEP 9: Add missing detail labels (remove hardcoded strings from views)
-- =====================================================================

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`) VALUES
  (74, 'detail_label_duration_unit', 'TEXT', 'hours'),
  (74, 'detail_label_seats_unit', 'TEXT', 'per session'),
  (74, 'detail_label_price_adult', 'TEXT', 'Per adult'),
  (74, 'detail_label_price_child', 'TEXT', 'Under 12'),
  (74, 'detail_reservation_fee_text', 'TEXT', 'To complete your reservation, you pay a {fee} fee per person. This amount is deducted from your final bill at the restaurant, so you simply pay the remaining amount after your meal.'),
  (74, 'detail_form_back_to_prefix', 'TEXT', 'Back to'),
  (74, 'detail_form_label_date', 'TEXT', 'Date'),
  (74, 'detail_form_placeholder_date', 'TEXT', 'Select a day'),
  (74, 'detail_form_label_time', 'TEXT', 'Time'),
  (74, 'detail_form_placeholder_time', 'TEXT', 'Select a time'),
  (74, 'detail_form_guests_title', 'TEXT', 'Number of Guests'),
  (74, 'detail_form_label_adult', 'TEXT', 'Adult'),
  (74, 'detail_form_label_children', 'TEXT', 'Children'),
  (74, 'detail_form_special_requests_title', 'TEXT', 'Special requests'),
  (74, 'detail_form_special_requests_subtitle', 'TEXT', 'Diet, allergies, accessibility needs'),
  (74, 'detail_form_special_requests_placeholder', 'TEXT', 'Let us know if you have any special requirements'),
  (74, 'detail_form_total_title', 'TEXT', 'Total to be paid'),
  (74, 'detail_form_back_to_restaurant', 'TEXT', 'Back to Restaurant');

COMMIT;

-- =====================================================================
-- ROLLBACK (run manually if needed)
-- =====================================================================
-- ALTER TABLE `Event`
--   DROP COLUMN `Stars`,
--   DROP COLUMN `MichelinStars`,
--   DROP COLUMN `CuisineType`,
--   DROP COLUMN `PriceAdult`,
--   DROP COLUMN `DurationMinutes`,
--   DROP COLUMN `SeatsPerSession`,
--   DROP COLUMN `TimeSlots`;
--
-- UPDATE `Event` SET `VenueId` = NULL WHERE `EventId` IN (47,48,49,50,51,52,53);
--
-- DELETE FROM `Venue` WHERE `Name` IN (
--   'Café de Roemer','Ratatouille','Restaurant ML','Restaurant Fris',
--   'New Vegas','Grand Cafe Brinkman','Urban Frenchy Bistro Toujours'
-- );
--
-- UPDATE `Reservation` SET `DiningDate` = 'Thursday' WHERE `DiningDate` = '2026-07-23';
-- UPDATE `Reservation` SET `DiningDate` = 'Friday'   WHERE `DiningDate` = '2026-07-24';
-- UPDATE `Reservation` SET `DiningDate` = 'Saturday'  WHERE `DiningDate` = '2026-07-25';
-- UPDATE `Reservation` SET `DiningDate` = 'Sunday'    WHERE `DiningDate` = '2026-07-26';
--
-- (Re-insert deleted CmsItem rows from backup)
