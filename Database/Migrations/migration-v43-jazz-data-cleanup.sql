-- Jazz data cleanup and session-level venue correction.

ALTER TABLE `EventSession`
    ADD COLUMN `VenueId` int(11) DEFAULT NULL AFTER `EventId`,
    ADD KEY `IX_EventSession_Venue` (`VenueId`),
    ADD CONSTRAINT `FK_EventSession_Venue` FOREIGN KEY (`VenueId`) REFERENCES `Venue` (`VenueId`);

-- Default session venue to the parent event venue when available.
UPDATE `EventSession` es
INNER JOIN `Event` e ON e.`EventId` = es.`EventId`
SET es.`VenueId` = e.`VenueId`
WHERE es.`VenueId` IS NULL;

-- Jazz venue corrections:
-- These artists have both indoor Patronaat sessions and Sunday outdoor Grote Markt sessions.
UPDATE `EventSession`
SET `VenueId` = 1
WHERE `EventId` IN (1, 2, 4, 13, 17)
  AND `HallName` <> 'Outdoor Stage';

UPDATE `Event`
SET `VenueId` = 1
WHERE `EventId` = 9;

UPDATE `EventSession`
SET `VenueId` = 1
WHERE `EventId` = 9;

-- Jazz CMS copy cleanup.
UPDATE `CmsItem` i
INNER JOIN `CmsSection` s ON s.`CmsSectionId` = i.`CmsSectionId`
INNER JOIN `CmsPage` p ON p.`CmsPageId` = s.`CmsPageId`
SET i.`TextValue` = '#jazz-schedule'
WHERE p.`Slug` = 'jazz'
  AND s.`SectionKey` = 'schedule_cta_section'
  AND i.`ItemKey` = 'schedule_cta_button_link';

UPDATE `CmsItem` i
INNER JOIN `CmsSection` s ON s.`CmsSectionId` = i.`CmsSectionId`
INNER JOIN `CmsPage` p ON p.`CmsPageId` = s.`CmsPageId`
SET i.`TextValue` = 'All-Access 3-Day Pass'
WHERE p.`Slug` = 'jazz'
  AND s.`SectionKey` = 'pricing_section'
  AND i.`ItemKey` = 'pricing_3day_title';

UPDATE `CmsItem` i
INNER JOIN `CmsSection` s ON s.`CmsSectionId` = i.`CmsSectionId`
INNER JOIN `CmsPage` p ON p.`CmsPageId` = s.`CmsPageId`
SET i.`TextValue` = 'All-Access pass for this day: €35.00. Valid for unlimited entry to Main Hall, Second Hall, and Third Hall performances on the selected day.'
WHERE p.`Slug` = 'jazz'
  AND s.`SectionKey` = 'pricing_section'
  AND i.`ItemKey` = 'pricing_daypass_info';

UPDATE `CmsItem` i
INNER JOIN `CmsSection` s ON s.`CmsSectionId` = i.`CmsSectionId`
INNER JOIN `CmsPage` p ON p.`CmsPageId` = s.`CmsPageId`
SET i.`TextValue` = 'Main Hall'
WHERE p.`Slug` = 'jazz'
  AND s.`SectionKey` = 'venues_section'
  AND i.`ItemKey` = 'venue_patronaat_hall1_name';

UPDATE `CmsItem` i
INNER JOIN `CmsSection` s ON s.`CmsSectionId` = i.`CmsSectionId`
INNER JOIN `CmsPage` p ON p.`CmsPageId` = s.`CmsPageId`
SET i.`TextValue` = 'Headliner performances - €15.00 per show'
WHERE p.`Slug` = 'jazz'
  AND s.`SectionKey` = 'venues_section'
  AND i.`ItemKey` = 'venue_patronaat_hall1_desc';

UPDATE `CmsItem` i
INNER JOIN `CmsSection` s ON s.`CmsSectionId` = i.`CmsSectionId`
INNER JOIN `CmsPage` p ON p.`CmsPageId` = s.`CmsPageId`
SET i.`TextValue` = '300 seats'
WHERE p.`Slug` = 'jazz'
  AND s.`SectionKey` = 'venues_section'
  AND i.`ItemKey` = 'venue_patronaat_hall1_capacity';

UPDATE `CmsItem` i
INNER JOIN `CmsSection` s ON s.`CmsSectionId` = i.`CmsSectionId`
INNER JOIN `CmsPage` p ON p.`CmsPageId` = s.`CmsPageId`
SET i.`TextValue` = '200 seats'
WHERE p.`Slug` = 'jazz'
  AND s.`SectionKey` = 'venues_section'
  AND i.`ItemKey` = 'venue_patronaat_hall2_capacity';

UPDATE `CmsItem` i
INNER JOIN `CmsSection` s ON s.`CmsSectionId` = i.`CmsSectionId`
INNER JOIN `CmsPage` p ON p.`CmsPageId` = s.`CmsPageId`
SET i.`TextValue` = 'Outdoor Stage'
WHERE p.`Slug` = 'jazz'
  AND s.`SectionKey` = 'venues_section'
  AND i.`ItemKey` = 'venue_grotemarkt_hall_name';

-- Remove inactive Jazz E2E/test events with no real usage.
DELETE FROM `Event`
WHERE `EventId` IN (56, 60, 61, 62, 63, 64, 65)
  AND `EventTypeId` = 1
  AND `IsActive` = 0;

-- Remove orphaned E2E/test artists after event cleanup.
DELETE FROM `Artist`
WHERE `ArtistId` IN (21, 26, 27, 28)
  AND NOT EXISTS (
      SELECT 1
      FROM `Event` e
      WHERE e.`ArtistId` = `Artist`.`ArtistId`
  );
