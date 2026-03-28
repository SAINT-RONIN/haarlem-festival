-- Stores the checkout recipient and ticket email delivery status on each order.
ALTER TABLE `Order`
    ADD COLUMN `TicketRecipientFirstName` varchar(60) DEFAULT NULL AFTER `TotalAmount`,
    ADD COLUMN `TicketRecipientLastName` varchar(80) DEFAULT NULL AFTER `TicketRecipientFirstName`,
    ADD COLUMN `TicketRecipientEmail` varchar(200) DEFAULT NULL AFTER `TicketRecipientLastName`,
    ADD COLUMN `TicketEmailSentAtUtc` datetime DEFAULT NULL AFTER `TicketRecipientEmail`,
    ADD COLUMN `TicketEmailLastError` varchar(500) DEFAULT NULL AFTER `TicketEmailSentAtUtc`;
