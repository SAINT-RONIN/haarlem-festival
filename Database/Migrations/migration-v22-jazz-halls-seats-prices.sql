-- Migration v22: Fix Jazz events data with correct halls, seats, and prices
-- Based on Excel schedule data

-- ============================================
-- 1. Add missing venues (Patronaat and Grote Markt)
-- ============================================
INSERT INTO Venue (VenueId, Name, Address, City, IsActive)
VALUES
    (1, 'Patronaat', 'Zijlsingel 2', 'Haarlem', 1),
    (2, 'Grote Markt', 'Grote Markt', 'Haarlem', 1),
    (3, 'De Hallen', 'Verwulft 13', 'Haarlem', 1)
ON DUPLICATE KEY UPDATE Name = VALUES(Name);

-- ============================================
-- 2. Recreate missing Jazz events
-- ============================================
-- Check if events exist and insert if not
INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, VenueId, ArtistId, IsActive)
SELECT 1, 1, 'Gumbo Kings', 'New Orleans funk and soul', '<p></p>', 1,
    (SELECT ArtistId FROM Artist WHERE Name = 'Gumbo Kings' LIMIT 1), 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 1);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, VenueId, ArtistId, IsActive)
SELECT 2, 1, 'Evolve', 'Jazz/Funk fusion', '<p></p>', 1,
    (SELECT ArtistId FROM Artist WHERE Name = 'Evolve' LIMIT 1), 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 2);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, VenueId, ArtistId, IsActive)
SELECT 3, 1, 'Ntjam Rosie', 'Soul/Jazz vocalist', '<p></p>', 1,
    (SELECT ArtistId FROM Artist WHERE Name = 'Ntjam Rosie' LIMIT 1), 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 3);

INSERT INTO Event (EventId, EventTypeId, Title, ShortDescription, LongDescriptionHtml, VenueId, ArtistId, IsActive)
SELECT 4, 1, 'Wicked Jazz Sounds', 'Nu-Jazz/Funk collective', '<p></p>', 1,
    (SELECT ArtistId FROM Artist WHERE Name = 'Wicked Jazz Sounds' LIMIT 1), 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Event WHERE EventId = 4);

-- ============================================
-- 3. Create missing Thursday Gumbo Kings session (18:00)
-- ============================================
INSERT INTO EventSession (EventSessionId, EventId, StartDateTime, EndDateTime, CapacityTotal, CapacitySingleTicketLimit, HallName, Notes, IsActive)
SELECT 1, 1, '2026-07-23 18:00:00', '2026-07-23 19:00:00', 300, 300, 'Main Hall', '', 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM EventSession WHERE EventSessionId = 1);

-- Create Thursday Evolve session (19:30) if missing
INSERT INTO EventSession (EventSessionId, EventId, StartDateTime, EndDateTime, CapacityTotal, CapacitySingleTicketLimit, HallName, Notes, IsActive)
SELECT 2, 2, '2026-07-23 19:30:00', '2026-07-23 20:30:00', 300, 300, 'Main Hall', '', 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM EventSession WHERE EventSessionId = 2);

-- ============================================
-- 4. Update Sunday sessions with Outdoor Stage and correct capacity
-- ============================================
UPDATE EventSession es
JOIN Event e ON es.EventId = e.EventId
SET es.HallName = 'Outdoor Stage',
    es.CapacityTotal = 300,
    es.CapacitySingleTicketLimit = 300,
    es.IsFree = 1
WHERE e.EventTypeId = 1
  AND DATE(es.StartDateTime) = '2026-07-26';

-- Also update the Event VenueId for Sunday events to Grote Markt (VenueId = 2)
UPDATE Event e
SET e.VenueId = 2
WHERE e.EventId IN (
    SELECT DISTINCT es.EventId
    FROM EventSession es
    WHERE DATE(es.StartDateTime) = '2026-07-26'
)
AND e.EventTypeId = 1;

-- ============================================
-- 5. Update prices for all sessions based on Excel data
-- ============================================

-- Thursday Main Hall sessions: €15.00 (Gumbo Kings, Evolve, Ntjam Rosie)
UPDATE EventSessionPrice esp
JOIN EventSession es ON esp.EventSessionId = es.EventSessionId
SET esp.Price = 15.00
WHERE es.EventSessionId IN (1, 2, 3)
AND esp.PriceTierId = 1;

-- Insert price for session 1 (Gumbo Kings Thursday) if not exists
INSERT INTO EventSessionPrice (EventSessionId, PriceTierId, Price)
SELECT 1, 1, 15.00
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM EventSessionPrice WHERE EventSessionId = 1);

-- Insert price for session 2 (Evolve Thursday) if not exists
INSERT INTO EventSessionPrice (EventSessionId, PriceTierId, Price)
SELECT 2, 1, 15.00
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM EventSessionPrice WHERE EventSessionId = 2);

-- Insert price for session 3 (Ntjam Rosie Thursday) if not exists
INSERT INTO EventSessionPrice (EventSessionId, PriceTierId, Price)
SELECT 3, 1, 15.00
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM EventSessionPrice WHERE EventSessionId = 3);

-- Sunday sessions: €0.00 (already set, but ensure IsFree = 1)
UPDATE EventSessionPrice esp
JOIN EventSession es ON esp.EventSessionId = es.EventSessionId
SET esp.Price = 0.00
WHERE DATE(es.StartDateTime) = '2026-07-26';

-- ============================================
-- 6. Add genre labels for missing sessions
-- ============================================
-- Gumbo Kings session 1 (if not already labeled)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT 1, 'Soul'
FROM DUAL WHERE NOT EXISTS (
    SELECT 1 FROM EventSessionLabel WHERE EventSessionId = 1 AND LabelText = 'Soul'
);

-- Evolve session 2 (if not already labeled)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT 2, 'Alternative'
FROM DUAL WHERE NOT EXISTS (
    SELECT 1 FROM EventSessionLabel WHERE EventSessionId = 2 AND LabelText = 'Alternative'
);

-- Ntjam Rosie session 3 (if not already labeled)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT 3, 'Soul'
FROM DUAL WHERE NOT EXISTS (
    SELECT 1 FROM EventSessionLabel WHERE EventSessionId = 3 AND LabelText = 'Soul'
);

-- ============================================
-- Summary:
-- Thursday (Jul 23) at Patronaat:
--   Main Hall (300 seats, €15): Gumbo Kings 18:00, Evolve 19:30, Ntjam Rosie 21:00
--   Second Hall (200 seats, €10): Wicked Jazz Sounds 18:00, Wouter Hamel 19:30, Jonna Frazer 21:00
--
-- Friday (Jul 24) at Patronaat:
--   Main Hall (300 seats, €15): Karsu 18:00, Uncle Sue 19:30, Chris Allen 21:00
--   Second Hall (200 seats, €10): Myles Sanko 18:00, Ilse Huizinga 19:30, Eric Vloeimans 21:00
--
-- Saturday (Jul 25) at Patronaat:
--   Main Hall (300 seats, €15): Gare du Nord 18:00, Rilan & The Bombadiers 19:30, Soul Six 21:00
--   Third Hall (150 seats, €10): Han Bennink 18:00, The Nordanians 19:30, Lilith Merlot 21:00
--
-- Sunday (Jul 26) at Grote Markt:
--   Outdoor Stage (300 seats, FREE):
--     Ruis Soundsystem 15:00, Wicked Jazz Sounds 16:00, Evolve 17:00,
--     The Nordanians 18:00, Gumbo Kings 19:00, Gare du Nord 20:00
-- ============================================

-- ROLLBACK (if needed):
-- DELETE FROM EventSessionLabel WHERE EventSessionId IN (1, 2, 3);
-- DELETE FROM EventSessionPrice WHERE EventSessionId IN (1, 2, 3);
-- DELETE FROM EventSession WHERE EventSessionId IN (1, 2);
-- DELETE FROM Event WHERE EventId IN (1, 2, 3, 4);
-- DELETE FROM Venue WHERE VenueId IN (1, 2, 3);

