-- Migration v7: Jazz Page CMS Content
-- Created: February 10, 2026
-- Purpose: Insert all Jazz page content into CMS tables
-- This migration is idempotent - safe to run multiple times

-- Step 1: Delete existing Jazz page data if it exists (clean slate)
DELETE FROM `CmsPage` WHERE `Slug` = 'jazz';

-- Step 2: Insert Jazz page (let AUTO_INCREMENT handle the ID)
INSERT INTO `CmsPage` (`Slug`, `Title`) VALUES
('jazz', 'Haarlem Jazz Festival');

-- Get the page ID for use in subsequent inserts
SET @jazzPageId = LAST_INSERT_ID();

-- Step 3: Insert Jazz sections
INSERT INTO `CmsSection` (`CmsPageId`, `SectionKey`) VALUES
(@jazzPageId, 'hero_section'),
(@jazzPageId, 'gradient_section'),
(@jazzPageId, 'intro_section'),
(@jazzPageId, 'venues_section'),
(@jazzPageId, 'pricing_section'),
(@jazzPageId, 'schedule_cta_section'),
(@jazzPageId, 'artists_section'),
(@jazzPageId, 'booking_cta_section');

-- Get section IDs
SET @heroSectionId = (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @jazzPageId AND SectionKey = 'hero_section');
SET @gradientSectionId = (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @jazzPageId AND SectionKey = 'gradient_section');
SET @introSectionId = (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @jazzPageId AND SectionKey = 'intro_section');
SET @venuesSectionId = (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @jazzPageId AND SectionKey = 'venues_section');
SET @pricingSectionId = (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @jazzPageId AND SectionKey = 'pricing_section');
SET @scheduleCtaSectionId = (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @jazzPageId AND SectionKey = 'schedule_cta_section');
SET @artistsSectionId = (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @jazzPageId AND SectionKey = 'artists_section');
SET @bookingCtaSectionId = (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @jazzPageId AND SectionKey = 'booking_cta_section');

-- Step 4: Insert Hero Section Content
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`) VALUES
(@heroSectionId, 'hero_main_title', 'HEADING', 'HAARLEM JAZZ', NULL, NULL),
(@heroSectionId, 'hero_subtitle', 'TEXT', 'Experience world-class jazz performances at Haarlem''s premier music festival. Discover our complete lineup, detailed schedules, and venue information.', NULL, NULL),
(@heroSectionId, 'hero_button_primary', 'BUTTON_TEXT', 'Discover all performances', NULL, NULL),
(@heroSectionId, 'hero_button_primary_link', 'URL', '#artists', NULL, NULL),
(@heroSectionId, 'hero_button_secondary', 'BUTTON_TEXT', 'What is Haarlem Jazz?', NULL, NULL),
(@heroSectionId, 'hero_button_secondary_link', 'URL', '#intro', NULL, NULL);

-- Step 5: Insert Gradient Section Content
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`) VALUES
(@gradientSectionId, 'gradient_heading', 'HEADING', 'Every note carries emotion, intention, and connection beyond what is heard.', NULL, NULL),
(@gradientSectionId, 'gradient_subheading', 'TEXT', 'A place where jazz is experienced, not just played.', NULL, NULL);

-- Step 6: Insert Intro Section Content
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`) VALUES
(@introSectionId, 'intro_heading', 'HEADING', 'Haarlem moves to the rhythm of jazz', NULL, NULL),
(@introSectionId, 'intro_body', 'HTML', NULL, '<p>Welcome to Haarlem Jazz 2026, taking place during the last weekend of July. This year''s festival features an outstanding lineup of international and local jazz artists performing across multiple venues in Haarlem''s historic city center. From intimate club settings to free outdoor performances, our program offers something for every jazz enthusiast.</p><p>The festival runs from Thursday through Sunday, with paid indoor performances at the Patronaat venue (featuring Main Hall, Second Hall, and Third Hall) and free outdoor concerts on Sunday at the iconic Grote Markt. All-access day passes and multi-day passes are available, offering excellent value for festival-goers wanting to experience multiple performances.</p><p>Below you will find detailed information about our featured artists, complete performance schedules with exact times and venues, seating capacity for each show, ticket pricing, and booking information. We recommend reviewing the schedule carefully and booking early as many performances have limited seating.</p>', NULL);

-- Step 7: Insert Venues Section Content
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`) VALUES
(@venuesSectionId, 'venues_heading', 'HEADING', 'Festival venues', NULL, NULL),
(@venuesSectionId, 'venues_subheading', 'TEXT', 'Performance Locations', NULL, NULL),
(@venuesSectionId, 'venues_description', 'TEXT', 'Haarlem Jazz 2026 takes place at two main locations in the city center. The Patronaat offers three different halls for intimate indoor performances, while the Grote Markt hosts free outdoor concerts on Sunday for all visitors.', NULL, NULL),
-- Patronaat Venue
(@venuesSectionId, 'venue_patronaat_name', 'TEXT', 'Patronaat', NULL, NULL),
(@venuesSectionId, 'venue_patronaat_address1', 'TEXT', 'Zijlsingel 2', NULL, NULL),
(@venuesSectionId, 'venue_patronaat_address2', 'TEXT', '2013 DN Haarlem', NULL, NULL),
(@venuesSectionId, 'venue_patronaat_contact', 'TEXT', 'E-mail/reception available', NULL, NULL),
(@venuesSectionId, 'venue_patronaat_hall1_name', 'TEXT', 'First Hall', NULL, NULL),
(@venuesSectionId, 'venue_patronaat_hall1_desc', 'TEXT', 'Intimate performances - €10.00 per show', NULL, NULL),
(@venuesSectionId, 'venue_patronaat_hall1_capacity', 'TEXT', '150 seats', NULL, NULL),
(@venuesSectionId, 'venue_patronaat_hall2_name', 'TEXT', 'Second Hall', NULL, NULL),
(@venuesSectionId, 'venue_patronaat_hall2_desc', 'TEXT', 'Intimate performances - €10.00 per show', NULL, NULL),
(@venuesSectionId, 'venue_patronaat_hall2_capacity', 'TEXT', '150 seats', NULL, NULL),
(@venuesSectionId, 'venue_patronaat_hall3_name', 'TEXT', 'Third Hall', NULL, NULL),
(@venuesSectionId, 'venue_patronaat_hall3_desc', 'TEXT', 'Intimate performances - €10.00 per show', NULL, NULL),
(@venuesSectionId, 'venue_patronaat_hall3_capacity', 'TEXT', '150 seats', NULL, NULL),
-- Grote Markt Venue
(@venuesSectionId, 'venue_grotemarkt_name', 'TEXT', 'Grote Markt', NULL, NULL),
(@venuesSectionId, 'venue_grotemarkt_location1', 'TEXT', 'Historic Market Square', NULL, NULL),
(@venuesSectionId, 'venue_grotemarkt_location2', 'TEXT', 'Haarlem City Center', NULL, NULL),
(@venuesSectionId, 'venue_grotemarkt_hall_name', 'TEXT', 'Open Air Stage', NULL, NULL),
(@venuesSectionId, 'venue_grotemarkt_hall_desc', 'TEXT', 'Sunday performances are free for all visitors. No reservation needed.', NULL, NULL),
(@venuesSectionId, 'venue_grotemarkt_hall_info', 'TEXT', 'The Grote Markt outdoor performances provide a wonderful opportunity to experience jazz in Haarlem''s beautiful historic market square. Bring your family and friends for a free afternoon and evening of world-class music in the heart of the city.', NULL, NULL),
(@venuesSectionId, 'venue_grotemarkt_hall_price', 'TEXT', 'FREE ENTRY', NULL, NULL);

-- Step 8: Insert Pricing Section Content
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`) VALUES
(@pricingSectionId, 'pricing_heading', 'HEADING', 'Pricing information', NULL, NULL),
(@pricingSectionId, 'pricing_subheading', 'TEXT', 'Tickets & Passes', NULL, NULL),
(@pricingSectionId, 'pricing_description', 'TEXT', 'We offer flexible ticketing options including individual show tickets and money-saving all-access passes. All-access passes provide unlimited entry to performances in all halls for the selected day(s).', NULL, NULL),
-- Individual Tickets Card
(@pricingSectionId, 'pricing_individual_title', 'TEXT', 'Individual Show Tickets', NULL, NULL),
(@pricingSectionId, 'pricing_individual_item1', 'TEXT', 'Main Hall Shows - €15.00 - 300 seats available per show', NULL, NULL),
(@pricingSectionId, 'pricing_individual_item2', 'TEXT', 'Second Hall Shows - €10.00 - 200 seats available per show', NULL, NULL),
(@pricingSectionId, 'pricing_individual_item3', 'TEXT', 'Third Hall Shows - €10.00 - 150 seats available per show', NULL, NULL),
-- Day Pass Card
(@pricingSectionId, 'pricing_daypass_title', 'TEXT', 'All-Access Day Pass', NULL, NULL),
(@pricingSectionId, 'pricing_daypass_price', 'TEXT', '€35.00', NULL, NULL),
(@pricingSectionId, 'pricing_daypass_desc', 'TEXT', 'Per day', NULL, NULL),
(@pricingSectionId, 'pricing_daypass_include1', 'TEXT', 'Unlimited access to all halls', NULL, NULL),
(@pricingSectionId, 'pricing_daypass_include2', 'TEXT', 'All performances on selected day', NULL, NULL),
(@pricingSectionId, 'pricing_daypass_include3', 'TEXT', 'Thursday, Friday, or Saturday', NULL, NULL),
(@pricingSectionId, 'pricing_daypass_include4', 'TEXT', 'Best value for multiple shows', NULL, NULL),
(@pricingSectionId, 'pricing_daypass_info', 'TEXT', 'All-Access pass for this day €35,00. Valid for unlimited entry to Main Hall, Second Hall, and Third Hall performances on the selected day.', NULL, NULL),
-- 3-Day Pass Card
(@pricingSectionId, 'pricing_3day_title', 'TEXT', 'All-Access Day Pass', NULL, NULL),
(@pricingSectionId, 'pricing_3day_price', 'TEXT', '€80.00', NULL, NULL),
(@pricingSectionId, 'pricing_3day_desc', 'TEXT', 'Thursday + Friday + Saturday', NULL, NULL),
(@pricingSectionId, 'pricing_3day_include1', 'TEXT', 'Unlimited access all 3 days', NULL, NULL),
(@pricingSectionId, 'pricing_3day_include2', 'TEXT', 'All venues and halls', NULL, NULL),
(@pricingSectionId, 'pricing_3day_include3', 'TEXT', '18+ performances included', NULL, NULL),
(@pricingSectionId, 'pricing_3day_include4', 'TEXT', 'Save €25 vs. day passes', NULL, NULL),
(@pricingSectionId, 'pricing_3day_info', 'TEXT', 'All-Access pass for Thu, Fri, Sat: €80.00. Complete festival access for three full days of jazz performances.', NULL, NULL);

-- Step 9: Insert Schedule CTA Section Content
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`) VALUES
(@scheduleCtaSectionId, 'schedule_cta_heading', 'HEADING', 'Ready to Plan Your Festival Experience?', NULL, NULL),
(@scheduleCtaSectionId, 'schedule_cta_description', 'TEXT', 'Now that you''ve explored our artists, check out the complete performance schedule below to see exact times, venues, and ticket availability. You can filter by day to plan your perfect jazz weekend.', NULL, NULL),
(@scheduleCtaSectionId, 'schedule_cta_button', 'BUTTON_TEXT', 'View complete schedule', NULL, NULL),
(@scheduleCtaSectionId, 'schedule_cta_button_link', 'URL', '#schedule', NULL, NULL);

-- Step 10: Insert Artists Section Content
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`) VALUES
(@artistsSectionId, 'artists_heading', 'HEADING', 'Discover our lineup', NULL, NULL);

-- Step 11: Insert Booking CTA Section Content
INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`) VALUES
(@bookingCtaSectionId, 'booking_cta_heading', 'HEADING', 'Book Your Experience', NULL, NULL),
(@bookingCtaSectionId, 'booking_cta_description', 'TEXT', 'Secure your tickets now for the last weekend of July. With limited seating at Patronaat and free performances at Grote Markt, there''s an option for every jazz lover. Don''t miss out on this year''s incredible lineup.', NULL, NULL);

-- Success message
SELECT 'Jazz CMS content successfully inserted!' AS Status;
