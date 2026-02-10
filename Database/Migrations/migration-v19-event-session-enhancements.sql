-- Migration v19: Event Session Enhancements
-- Adds HistoryTicketLabel column for history tour ticket descriptions
-- and ensures proper tracking columns exist

-- Add HistoryTicketLabel for history events (e.g., "Group ticket - best value for 4 people")
ALTER TABLE EventSession
    ADD COLUMN IF NOT EXISTS HistoryTicketLabel VARCHAR(120) DEFAULT NULL
    AFTER Notes;

-- Create index for faster date-based queries
CREATE INDEX IF NOT EXISTS idx_eventsession_startdate ON EventSession ((DATE(StartDateTime)));

-- Verify the Event table has all needed columns
-- (EventTypeId should already exist via foreign key)

-- Update any existing history sessions to have a default ticket label if null
UPDATE EventSession es
    INNER JOIN Event e ON es.EventId = e.EventId
SET es.HistoryTicketLabel = 'Walking tour ticket'
WHERE e.EventTypeId = 2
  AND es.HistoryTicketLabel IS NULL;

-- Rollback:
-- ALTER TABLE EventSession DROP COLUMN IF EXISTS HistoryTicketLabel;
-- DROP INDEX IF EXISTS idx_eventsession_startdate ON EventSession;

