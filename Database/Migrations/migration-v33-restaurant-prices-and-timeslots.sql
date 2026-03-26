-- migration-v33-restaurant-prices-and-timeslots.sql
-- Adds PriceAdult and PriceChild columns to Restaurant so each restaurant
-- can store its own menu prices.
-- Also sets the correct time slots per restaurant.

ALTER TABLE Restaurant
    ADD COLUMN IF NOT EXISTS PriceAdult DECIMAL(10,2) NULL AFTER TimeSlots,
    ADD COLUMN IF NOT EXISTS PriceChild DECIMAL(10,2) NULL AFTER PriceAdult;

-- Correct time slots per restaurant
UPDATE Restaurant SET TimeSlots = '17:00, 19:15, 21:30' WHERE Name LIKE '%Ratatouille%';
UPDATE Restaurant SET TimeSlots = '17:30, 19:15, 21:00' WHERE Name LIKE '%Toujours%';

-- Seed example prices for all restaurants that don't have them yet.
-- Update per restaurant as needed via the CMS or a future migration.
UPDATE Restaurant SET PriceAdult = 45.00, PriceChild = 22.50 WHERE PriceAdult IS NULL AND IsActive = 1;