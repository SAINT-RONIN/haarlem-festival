-- Migration: Populate HistoryTicketLabel for History tour sessions
-- Date: 2026-03-22
-- Context: Her database dump had this data but it was lost during previous merges.

UPDATE EventSession
SET HistoryTicketLabel = 'Group ticket - best value for 4 people'
WHERE EventId = 33
  AND (HistoryTicketLabel IS NULL OR HistoryTicketLabel = '');
