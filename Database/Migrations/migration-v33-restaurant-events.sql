-- =============================================================================
-- Migration v33: Restaurant → Event table
--
-- Each active Restaurant gets an Event record (EventTypeId = 5).
-- Restaurant-specific content is seeded into per-event CMS sections keyed by
-- RestaurantDetailConstants::eventSectionKey($eventId) = 'restaurant_event_{id}'.
--
-- The Restaurant table is kept as-is; it is still managed via the CMS admin.
-- The public site now reads from Event + CmsItem only (no IRestaurantRepository).
-- =============================================================================

-- Step 1: Insert Event records for every active restaurant that doesn't already
--         have one. The slug is derived from the restaurant name: lowercase,
--         spaces to hyphens, apostrophes removed.

INSERT INTO Event (EventTypeId, Title, ShortDescription, LongDescriptionHtml, FeaturedImageAssetId, RestaurantId, IsActive, Slug, CreatedAtUtc)
SELECT
    5                                                           AS EventTypeId,
    r.Name                                                      AS Title,
    r.CuisineType                                               AS ShortDescription,
    r.DescriptionHtml                                           AS LongDescriptionHtml,
    r.ImageAssetId                                              AS FeaturedImageAssetId,
    r.RestaurantId                                              AS RestaurantId,
    1                                                           AS IsActive,
    LOWER(
        REPLACE(
            REPLACE(
                REPLACE(r.Name, ' ', '-'),
                '''', ''
            ),
            '--', '-'
        )
    )                                                           AS Slug,
    NOW()                         befo                              AS CreatedAtUtc
FROM Restaurant r
WHERE r.IsActive = 1
  AND NOT EXISTS (
      SELECT 1 FROM Event e
      WHERE e.RestaurantId = r.RestaurantId
        AND e.EventTypeId = 5
  );

-- Step 2: Insert the CMS page record for 'restaurant' detail sections if it
--         doesn't exist yet (the listing page slug already exists).

INSERT IGNORE INTO CmsPage (PageSlug, PageTitle)
VALUES ('restaurant', 'Restaurant');

-- Step 3: For each newly created Event, seed a CMS section and items with the
--         data currently in the Restaurant table columns.
--
--         Section key pattern: restaurant_event_{EventId}

-- Seed CmsSection rows (one per restaurant event)
INSERT IGNORE INTO CmsSection (PageSlug, SectionKey, SectionLabel)
SELECT
    'restaurant'                                          AS PageSlug,
    CONCAT('restaurant_event_', e.EventId)                AS SectionKey,
    CONCAT('Restaurant: ', r.Name)                        AS SectionLabel
FROM Event e
JOIN Restaurant r ON e.RestaurantId = r.RestaurantId
WHERE e.EventTypeId = 5 AND e.IsActive = 1;

-- Seed CmsItem rows for each restaurant's data fields
-- (INSERT IGNORE so re-running the migration is safe)

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'address_line',          r.AddressLine          FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.AddressLine IS NOT NULL AND r.AddressLine <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'city',                  r.City                 FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.City IS NOT NULL AND r.City <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'phone',                 r.Phone                FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.Phone IS NOT NULL AND r.Phone <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'email',                 r.Email                FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.Email IS NOT NULL AND r.Email <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'website',               r.Website              FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.Website IS NOT NULL AND r.Website <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'about_text',            r.AboutText            FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.AboutText IS NOT NULL AND r.AboutText <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'chef_name',             r.ChefName             FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.ChefName IS NOT NULL AND r.ChefName <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'chef_text',             r.ChefText             FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.ChefText IS NOT NULL AND r.ChefText <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'cuisine_type',          r.CuisineType          FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.CuisineType IS NOT NULL AND r.CuisineType <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'menu_description',      r.MenuDescription      FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.MenuDescription IS NOT NULL AND r.MenuDescription <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'location_description',  r.LocationDescription  FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.LocationDescription IS NOT NULL AND r.LocationDescription <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'map_embed_url',         r.MapEmbedUrl          FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.MapEmbedUrl IS NOT NULL AND r.MapEmbedUrl <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'stars',                 CAST(r.Stars AS CHAR)           FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.Stars IS NOT NULL;

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'michelin_stars',        CAST(r.MichelinStars AS CHAR)   FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.MichelinStars IS NOT NULL;

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'seats_per_session',     CAST(r.SeatsPerSession AS CHAR) FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.SeatsPerSession IS NOT NULL;

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'duration_minutes',      CAST(r.DurationMinutes AS CHAR) FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.DurationMinutes IS NOT NULL;

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'special_requests_note', r.SpecialRequestsNote  FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.SpecialRequestsNote IS NOT NULL AND r.SpecialRequestsNote <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'time_slots',            r.TimeSlots            FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.TimeSlots IS NOT NULL AND r.TimeSlots <> '';

INSERT IGNORE INTO CmsItem (PageSlug, SectionKey, ItemKey, ItemValue)
SELECT 'restaurant', CONCAT('restaurant_event_', e.EventId), 'price_adult',           CAST(r.PriceAdult AS CHAR)      FROM Event e JOIN Restaurant r ON e.RestaurantId = r.RestaurantId WHERE e.EventTypeId = 5 AND r.PriceAdult IS NOT NULL;
