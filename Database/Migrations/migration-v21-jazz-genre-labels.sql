-- Migration v21: Add genre labels to Jazz event sessions
-- Each jazz event gets one genre label: Alternative, Jazz, Rock, Soul, Folk, or Pop

-- First, remove any existing labels for jazz event sessions to avoid duplicates
DELETE esl FROM EventSessionLabel esl
INNER JOIN EventSession es ON esl.EventSessionId = es.EventSessionId
INNER JOIN Event e ON es.EventId = e.EventId
WHERE e.EventTypeId = 1;

-- Now insert genre labels for each jazz event session
-- Based on artist/event genre research:

-- Gumbo Kings (EventId 1) - Soul (New Orleans funk/soul)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Soul'
FROM EventSession es WHERE es.EventId = 1;

-- Evolve (EventId 2) - Alternative (Jazz/Funk fusion)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Alternative'
FROM EventSession es WHERE es.EventId = 2;

-- Ntjam Rosie (EventId 3) - Soul (Soul/Jazz vocalist)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Soul'
FROM EventSession es WHERE es.EventId = 3;

-- Wicked Jazz Sounds (EventId 4) - Alternative (Nu-Jazz/Funk)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Alternative'
FROM EventSession es WHERE es.EventId = 4;

-- Wouter Hamel (EventId 5) - Pop (Pop/Jazz crossover)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Pop'
FROM EventSession es WHERE es.EventId = 5;

-- Jonna Frazer (EventId 6) - Soul
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Soul'
FROM EventSession es WHERE es.EventId = 6;

-- Karsu (EventId 7) - Folk (Turkish/World/Folk influences)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Folk'
FROM EventSession es WHERE es.EventId = 7;

-- Uncle Sue (EventId 8) - Rock (Blues/Rock)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Rock'
FROM EventSession es WHERE es.EventId = 8;

-- Chris Allen (EventId 9) - Alternative (Contemporary Jazz)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Alternative'
FROM EventSession es WHERE es.EventId = 9;

-- Myles Sanko (EventId 10) - Soul
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Soul'
FROM EventSession es WHERE es.EventId = 10;

-- Ilse Huizinga (EventId 11) - Alternative (Jazz vocals)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Alternative'
FROM EventSession es WHERE es.EventId = 11;

-- Eric Vloeimans and Hotspot! (EventId 12) - Alternative (Jazz/World fusion)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Alternative'
FROM EventSession es WHERE es.EventId = 12;

-- Gare du Nord (EventId 13) - Pop (Lounge/Pop-Jazz)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Pop'
FROM EventSession es WHERE es.EventId = 13;

-- Rilan & The Bombadiers (EventId 14) - Rock (Rockabilly/Rock)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Rock'
FROM EventSession es WHERE es.EventId = 14;

-- Soul Six (EventId 15) - Soul (Soul/Funk)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Soul'
FROM EventSession es WHERE es.EventId = 15;

-- Han Bennink (EventId 16) - Alternative (Free Jazz/Avant-garde)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Alternative'
FROM EventSession es WHERE es.EventId = 16;

-- The Nordanians (EventId 17) - Folk (Nordic Folk)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Folk'
FROM EventSession es WHERE es.EventId = 17;

-- Lilith Merlot (EventId 18) - Folk (Singer-songwriter/Folk)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Folk'
FROM EventSession es WHERE es.EventId = 18;

-- Ruis Soundsystem (EventId 19) - Alternative (Electronic/Jazz)
INSERT INTO EventSessionLabel (EventSessionId, LabelText)
SELECT es.EventSessionId, 'Alternative'
FROM EventSession es WHERE es.EventId = 19;

-- Summary of genre distribution:
-- Alternative: 8 events (Evolve, Wicked Jazz Sounds, Chris Allen, Ilse Huizinga, Eric Vloeimans, Han Bennink, Ruis Soundsystem)
-- Soul: 5 events (Gumbo Kings, Ntjam Rosie, Jonna Frazer, Myles Sanko, Soul Six)
-- Folk: 3 events (Karsu, The Nordanians, Lilith Merlot)
-- Rock: 2 events (Uncle Sue, Rilan & The Bombadiers)
-- Pop: 2 events (Wouter Hamel, Gare du Nord)

-- ROLLBACK:
-- DELETE esl FROM EventSessionLabel esl
-- INNER JOIN EventSession es ON esl.EventSessionId = es.EventSessionId
-- INNER JOIN Event e ON es.EventId = e.EventId
-- WHERE e.EventTypeId = 1 AND esl.LabelText IN ('Alternative', 'Soul', 'Folk', 'Rock', 'Pop');

