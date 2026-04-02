-- Migration: copy missing history tour family/group prices to duplicated language sessions
-- Date: 2026-04-02
-- Ensures all language variants in the same history tour timeslot can sell the shared group ticket.

INSERT INTO EventSessionPrice (EventSessionId, PriceTierId, Price, CurrencyCode, VatRate)
SELECT
    target.EventSessionId,
    sourcePrice.PriceTierId,
    sourcePrice.Price,
    sourcePrice.CurrencyCode,
    sourcePrice.VatRate
FROM EventSession AS target
INNER JOIN Event AS targetEvent
    ON targetEvent.EventId = target.EventId
INNER JOIN EventType AS targetType
    ON targetType.EventTypeId = targetEvent.EventTypeId
INNER JOIN EventSession AS sourceSession
    ON sourceSession.EventId = target.EventId
   AND sourceSession.StartDateTime = target.StartDateTime
   AND sourceSession.EventSessionId <> target.EventSessionId
INNER JOIN EventSessionPrice AS sourcePrice
    ON sourcePrice.EventSessionId = sourceSession.EventSessionId
   AND sourcePrice.PriceTierId IN (3, 7)
LEFT JOIN EventSessionPrice AS existingPrice
    ON existingPrice.EventSessionId = target.EventSessionId
   AND existingPrice.PriceTierId = sourcePrice.PriceTierId
WHERE targetType.Slug = 'history'
  AND target.SessionType = 'Tour'
  AND COALESCE(target.HistoryTicketLabel, '') <> ''
  AND existingPrice.EventSessionId IS NULL
GROUP BY
    target.EventSessionId,
    sourcePrice.PriceTierId,
    sourcePrice.Price,
    sourcePrice.CurrencyCode,
    sourcePrice.VatRate;
