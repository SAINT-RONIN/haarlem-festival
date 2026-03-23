-- v30: Persist event slugs for direct detail-page lookup

ALTER TABLE Event
    ADD COLUMN Slug VARCHAR(180) NULL AFTER Title;

-- Backfill slug values from existing titles.
UPDATE Event
SET Slug = LOWER(TRIM(BOTH '-' FROM REGEXP_REPLACE(Title, '[^a-z0-9]+', '-')))
WHERE Slug IS NULL OR Slug = '';

-- Resolve collisions by appending EventId where the generated slug is duplicated.
UPDATE Event e
INNER JOIN (
    SELECT Slug
    FROM Event
    WHERE Slug IS NOT NULL AND Slug <> ''
    GROUP BY Slug
    HAVING COUNT(*) > 1
) duplicates ON duplicates.Slug = e.Slug
SET e.Slug = CONCAT(e.Slug, '-', e.EventId);

ALTER TABLE Event
    ADD UNIQUE KEY UNQ_Event_Slug (Slug);

