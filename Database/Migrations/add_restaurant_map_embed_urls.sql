-- Migration: Add map_embed_url CmsItem rows for Ratatouille and Toujours
--
-- Context: The restaurant detail page (page 14) already supports map_embed_url (LINK type)
-- in RestaurantEventCmsData and detail-content.php, but no CmsItem rows existed for it.
-- This inserts Google Maps embed URLs for the two festival dinner restaurants.
--
-- CmsSectionId mapping (page 14 = restaurant-detail):
--   94 = event_48 = Ratatouille - Festival Dinner   (Spaarne 96, 2011 CL Haarlem)
--   99 = event_53 = Urban Frenchy Bistro Toujours   (Oude Groenmarkt 10-12, 2011 HL Haarlem)

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES
(94, 'map_embed_url', 'LINK', 'https://maps.google.com/maps?q=52.3786756,4.6376030&z=17&output=embed', NULL, NULL, NOW()),
(99, 'map_embed_url', 'LINK', 'https://maps.google.com/maps?q=52.3806823,4.6370676&z=17&output=embed', NULL, NULL, NOW());
