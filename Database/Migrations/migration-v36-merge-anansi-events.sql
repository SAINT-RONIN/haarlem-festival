-- migration-v36-merge-anansi-events.sql
--
-- Merges "Meneer Anansi" (Event 41, Dutch) into "Mister Anansi" (Event 42).
-- Both share the same detail page. After this migration, Event 42 has all 4 sessions:
--   Session 57: Jul 25, 10:00-11:00 (NL)  — was Event 41
--   Session 58: Jul 25, 15:00-16:00 (ENG) — already Event 42
--   Session 61: Jul 26, 10:00-11:00 (ENG) — already Event 42
--   Session 62: Jul 26, 15:00-16:00 (NL)  — was Event 41
--
-- Event 41 is deactivated (IsActive=0) so it no longer appears in listings.
-- ---------------------------------------------------------------

-- Move Meneer Anansi sessions to Mister Anansi
UPDATE `EventSession` SET `EventId` = 42 WHERE `EventSessionId` IN (57, 62);

-- Deactivate Meneer Anansi event
UPDATE `Event` SET `IsActive` = 0 WHERE `EventId` = 41;
