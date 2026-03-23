-- migration-v37-delete-meneer-anansi.sql
--
-- Permanently deletes Event 41 (Meneer Anansi) and all related data.
-- Sessions 57 and 62 were already moved to Event 42 (Mister Anansi) in migration-v36.
-- CMS section event_41 (CmsSectionId 68) is also removed.
--
-- Order matters: children first, then parents (foreign key constraints).
-- ---------------------------------------------------------------

-- Delete CMS items for event_41 section
DELETE FROM `CmsItem` WHERE `CmsSectionId` = 68;

-- Delete CMS section
DELETE FROM `CmsSection` WHERE `CmsSectionId` = 68;

-- Delete any remaining session labels for event 41 sessions
DELETE esl FROM `EventSessionLabel` esl
INNER JOIN `EventSession` es ON esl.EventSessionId = es.EventSessionId
WHERE es.EventId = 41;

-- Delete any remaining session prices for event 41 sessions
DELETE esp FROM `EventSessionPrice` esp
INNER JOIN `EventSession` es ON esp.EventSessionId = es.EventSessionId
WHERE es.EventId = 41;

-- Delete any remaining sessions (should be none after migration-v36, but just in case)
DELETE FROM `EventSession` WHERE `EventId` = 41;

-- Delete the event itself
DELETE FROM `Event` WHERE `EventId` = 41;
