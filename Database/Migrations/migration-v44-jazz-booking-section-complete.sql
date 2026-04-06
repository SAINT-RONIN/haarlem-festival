-- Completes the Jazz booking CTA section with booking-card CMS content.

INSERT INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`)
SELECT 'booking_contact_eyebrow', 'TEXT'
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItemKey` WHERE `ItemKey` = 'booking_contact_eyebrow'
);

INSERT INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`)
SELECT 'booking_contact_title', 'TEXT'
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItemKey` WHERE `ItemKey` = 'booking_contact_title'
);

INSERT INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`)
SELECT 'booking_contact_description', 'TEXT'
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItemKey` WHERE `ItemKey` = 'booking_contact_description'
);

INSERT INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`)
SELECT 'booking_contact_phone_office', 'TEXT'
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItemKey` WHERE `ItemKey` = 'booking_contact_phone_office'
);

INSERT INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`)
SELECT 'booking_contact_phone_cash_desk', 'TEXT'
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItemKey` WHERE `ItemKey` = 'booking_contact_phone_cash_desk'
);

INSERT INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`)
SELECT 'booking_contact_hours', 'TEXT'
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItemKey` WHERE `ItemKey` = 'booking_contact_hours'
);

INSERT INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`)
SELECT 'booking_venue_eyebrow', 'TEXT'
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItemKey` WHERE `ItemKey` = 'booking_venue_eyebrow'
);

INSERT INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`)
SELECT 'booking_venue_title', 'TEXT'
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItemKey` WHERE `ItemKey` = 'booking_venue_title'
);

INSERT INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`)
SELECT 'booking_venue_description', 'TEXT'
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItemKey` WHERE `ItemKey` = 'booking_venue_description'
);

INSERT INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`)
SELECT 'booking_tickets_eyebrow', 'TEXT'
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItemKey` WHERE `ItemKey` = 'booking_tickets_eyebrow'
);

INSERT INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`)
SELECT 'booking_tickets_title', 'TEXT'
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItemKey` WHERE `ItemKey` = 'booking_tickets_title'
);

INSERT INTO `CmsItemKey` (`ItemKey`, `ExpectedItemType`)
SELECT 'booking_tickets_description', 'TEXT'
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItemKey` WHERE `ItemKey` = 'booking_tickets_description'
);

UPDATE `CmsItem` i
INNER JOIN `CmsSection` s ON s.`CmsSectionId` = i.`CmsSectionId`
INNER JOIN `CmsPage` p ON p.`CmsPageId` = s.`CmsPageId`
SET i.`TextValue` = 'Book Your Experience'
WHERE p.`Slug` = 'jazz'
  AND s.`SectionKey` = 'booking_cta_section'
  AND i.`ItemKey` = 'booking_cta_heading';

UPDATE `CmsItem` i
INNER JOIN `CmsSection` s ON s.`CmsSectionId` = i.`CmsSectionId`
INNER JOIN `CmsPage` p ON p.`CmsPageId` = s.`CmsPageId`
SET i.`TextValue` = 'Secure your tickets now for the last weekend of July. With limited seating at Patronaat and free performances at Grote Markt, there''s an option for every jazz lover. Don''t miss out on this year''s incredible lineup.'
WHERE p.`Slug` = 'jazz'
  AND s.`SectionKey` = 'booking_cta_section'
  AND i.`ItemKey` = 'booking_cta_description';

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
SELECT 35, 'booking_contact_eyebrow', 'TEXT', 'CONTACT US', NULL, NULL, UTC_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem`
    WHERE `CmsSectionId` = 35 AND `ItemKey` = 'booking_contact_eyebrow'
);

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
SELECT 35, 'booking_contact_title', 'TEXT', 'Get Information', NULL, NULL, UTC_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem`
    WHERE `CmsSectionId` = 35 AND `ItemKey` = 'booking_contact_title'
);

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
SELECT 35, 'booking_contact_description', 'TEXT', 'Questions about the festival, venues, or artists? Contact our office during business hours.', NULL, NULL, UTC_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem`
    WHERE `CmsSectionId` = 35 AND `ItemKey` = 'booking_contact_description'
);

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
SELECT 35, 'booking_contact_phone_office', 'TEXT', '023 - 517 58 50 (Office)', NULL, NULL, UTC_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem`
    WHERE `CmsSectionId` = 35 AND `ItemKey` = 'booking_contact_phone_office'
);

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
SELECT 35, 'booking_contact_phone_cash_desk', 'TEXT', '023 - 517 58 58 (Cash Desk)', NULL, NULL, UTC_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem`
    WHERE `CmsSectionId` = 35 AND `ItemKey` = 'booking_contact_phone_cash_desk'
);

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
SELECT 35, 'booking_contact_hours', 'TEXT', '10:00 - 17:00', NULL, NULL, UTC_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem`
    WHERE `CmsSectionId` = 35 AND `ItemKey` = 'booking_contact_hours'
);

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
SELECT 35, 'booking_venue_eyebrow', 'TEXT', 'VENUE DETAILS', NULL, NULL, UTC_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem`
    WHERE `CmsSectionId` = 35 AND `ItemKey` = 'booking_venue_eyebrow'
);

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
SELECT 35, 'booking_venue_title', 'TEXT', 'Visit Patronaat', NULL, NULL, UTC_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem`
    WHERE `CmsSectionId` = 35 AND `ItemKey` = 'booking_venue_title'
);

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
SELECT 35, 'booking_venue_description', 'TEXT', 'Learn more about our main indoor venue, accessibility options, and facilities.', NULL, NULL, UTC_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem`
    WHERE `CmsSectionId` = 35 AND `ItemKey` = 'booking_venue_description'
);

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
SELECT 35, 'booking_tickets_eyebrow', 'TEXT', 'TICKETS', NULL, NULL, UTC_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem`
    WHERE `CmsSectionId` = 35 AND `ItemKey` = 'booking_tickets_eyebrow'
);

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
SELECT 35, 'booking_tickets_title', 'TEXT', 'Purchase Tickets', NULL, NULL, UTC_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem`
    WHERE `CmsSectionId` = 35 AND `ItemKey` = 'booking_tickets_title'
);

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`)
SELECT 35, 'booking_tickets_description', 'TEXT', 'Individual show tickets, day passes, and 3-day all-access passes available now. Book early for best selection.', NULL, NULL, UTC_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1 FROM `CmsItem`
    WHERE `CmsSectionId` = 35 AND `ItemKey` = 'booking_tickets_description'
);
