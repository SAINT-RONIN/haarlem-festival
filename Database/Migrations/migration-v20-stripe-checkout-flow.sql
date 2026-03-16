-- Migration v20: Stripe checkout flow support
-- Adds Stripe references to Payment and webhook idempotency table.

ALTER TABLE Payment
    ADD COLUMN IF NOT EXISTS StripeCheckoutSessionId VARCHAR(120) DEFAULT NULL AFTER ProviderRef,
    ADD COLUMN IF NOT EXISTS StripePaymentIntentId VARCHAR(120) DEFAULT NULL AFTER StripeCheckoutSessionId;

CREATE INDEX IF NOT EXISTS IX_Payment_StripeCheckoutSessionId ON Payment (StripeCheckoutSessionId);
CREATE INDEX IF NOT EXISTS IX_Payment_StripePaymentIntentId ON Payment (StripePaymentIntentId);

CREATE TABLE IF NOT EXISTS StripeWebhookEvent (
    StripeWebhookEventId INT(11) NOT NULL AUTO_INCREMENT,
    EventId VARCHAR(120) NOT NULL,
    EventType VARCHAR(120) NOT NULL,
    ProcessedAtUtc DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (StripeWebhookEventId),
    UNIQUE KEY UQ_StripeWebhookEvent_EventId (EventId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Rollback (manual):
-- DROP TABLE IF EXISTS StripeWebhookEvent;
-- DROP INDEX IX_Payment_StripeCheckoutSessionId ON Payment;
-- DROP INDEX IX_Payment_StripePaymentIntentId ON Payment;
-- ALTER TABLE Payment DROP COLUMN StripeCheckoutSessionId, DROP COLUMN StripePaymentIntentId;

