-- =====================================================
-- Force restaurant IDs by name (works from ANY state)
-- Final order:
--   1 = Ratatouille
--   2 = Urban Frenchy Bistro Toujours
--   3 = Café de Roemer
--   4 = Grand Cafe Brinkman
--   5 = New Vegas
--   6 = Restaurant Fris
--   7 = Restaurant ML
-- =====================================================

-- Check current state first
SELECT r.RestaurantId, r.Name FROM Restaurant r ORDER BY r.RestaurantId;

START TRANSACTION;

-- Drop FK so we can move IDs freely
ALTER TABLE Event DROP FOREIGN KEY IF EXISTS FK_Event_Restaurant;

-- Phase 1: Move ALL 7 restaurants + their events to temp IDs (900+)
-- Use name-based JOINs so it works regardless of current IDs
SET @id1 := (SELECT RestaurantId FROM Restaurant WHERE Name = 'Ratatouille');
SET @id2 := (SELECT RestaurantId FROM Restaurant WHERE Name = 'Urban Frenchy Bistro Toujours');
SET @id3 := (SELECT RestaurantId FROM Restaurant WHERE Name = 'Café de Roemer');
SET @id4 := (SELECT RestaurantId FROM Restaurant WHERE Name = 'Grand Cafe Brinkman');
SET @id5 := (SELECT RestaurantId FROM Restaurant WHERE Name = 'New Vegas');
SET @id6 := (SELECT RestaurantId FROM Restaurant WHERE Name = 'Restaurant Fris');
SET @id7 := (SELECT RestaurantId FROM Restaurant WHERE Name = 'Restaurant ML');

UPDATE Event SET RestaurantId = 901 WHERE RestaurantId = @id1;
UPDATE Event SET RestaurantId = 902 WHERE RestaurantId = @id2;
UPDATE Event SET RestaurantId = 903 WHERE RestaurantId = @id3;
UPDATE Event SET RestaurantId = 904 WHERE RestaurantId = @id4;
UPDATE Event SET RestaurantId = 905 WHERE RestaurantId = @id5;
UPDATE Event SET RestaurantId = 906 WHERE RestaurantId = @id6;
UPDATE Event SET RestaurantId = 907 WHERE RestaurantId = @id7;

UPDATE Restaurant SET RestaurantId = 901 WHERE RestaurantId = @id1;
UPDATE Restaurant SET RestaurantId = 902 WHERE RestaurantId = @id2;
UPDATE Restaurant SET RestaurantId = 903 WHERE RestaurantId = @id3;
UPDATE Restaurant SET RestaurantId = 904 WHERE RestaurantId = @id4;
UPDATE Restaurant SET RestaurantId = 905 WHERE RestaurantId = @id5;
UPDATE Restaurant SET RestaurantId = 906 WHERE RestaurantId = @id6;
UPDATE Restaurant SET RestaurantId = 907 WHERE RestaurantId = @id7;

-- Phase 2: Assign final IDs
UPDATE Restaurant SET RestaurantId = 1 WHERE RestaurantId = 901;
UPDATE Restaurant SET RestaurantId = 2 WHERE RestaurantId = 902;
UPDATE Restaurant SET RestaurantId = 3 WHERE RestaurantId = 903;
UPDATE Restaurant SET RestaurantId = 4 WHERE RestaurantId = 904;
UPDATE Restaurant SET RestaurantId = 5 WHERE RestaurantId = 905;
UPDATE Restaurant SET RestaurantId = 6 WHERE RestaurantId = 906;
UPDATE Restaurant SET RestaurantId = 7 WHERE RestaurantId = 907;

UPDATE Event SET RestaurantId = 1 WHERE RestaurantId = 901;
UPDATE Event SET RestaurantId = 2 WHERE RestaurantId = 902;
UPDATE Event SET RestaurantId = 3 WHERE RestaurantId = 903;
UPDATE Event SET RestaurantId = 4 WHERE RestaurantId = 904;
UPDATE Event SET RestaurantId = 5 WHERE RestaurantId = 905;
UPDATE Event SET RestaurantId = 6 WHERE RestaurantId = 906;
UPDATE Event SET RestaurantId = 7 WHERE RestaurantId = 907;

-- Restore FK
ALTER TABLE Event ADD CONSTRAINT FK_Event_Restaurant FOREIGN KEY (RestaurantId) REFERENCES Restaurant (RestaurantId);
ALTER TABLE Restaurant AUTO_INCREMENT = 8;

COMMIT;

-- Verify final state
SELECT r.RestaurantId, r.Name FROM Restaurant r ORDER BY r.RestaurantId;
