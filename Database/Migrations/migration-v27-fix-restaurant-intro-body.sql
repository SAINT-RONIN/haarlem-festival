-- =====================================================
-- Migration v27: Fix ALL restaurant CMS text that has
-- literal \n instead of real newlines, and split
-- intro_body into separate subsection keys.
-- Safe & Idempotent.
-- =====================================================

-- =====================================================
-- FIX 1: hero_subtitle
-- Original: '...exclusive festival\nmenus crafted...'
-- Should be two lines with a real newline.
-- =====================================================

SET @heroSectionId = (
    SELECT cs.CmsSectionId
    FROM CmsSection cs
    JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
    WHERE cp.Slug = 'restaurant'
      AND cs.SectionKey = 'hero_section'
    LIMIT 1
);

UPDATE CmsItem
SET TextValue = 'Discover 7 gourmet restaurants offering exclusive festival
menus crafted by top local chefs.'
WHERE CmsSectionId = @heroSectionId
  AND ItemKey = 'hero_subtitle';

-- =====================================================
-- FIX 2: intro_body
-- Original: one giant blob with ## markers and \n.
-- Now: only the intro paragraph. Subsections become
-- separate CMS keys (same approach as Jazz).
-- =====================================================

SET @introSectionId = (
    SELECT cs.CmsSectionId
    FROM CmsSection cs
    JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
    WHERE cp.Slug = 'restaurant'
      AND cs.SectionKey = 'intro_split_section'
    LIMIT 1
);

UPDATE CmsItem
SET TextValue = 'Welcome to Yummy!, the food experience of the Haarlem Festival.
Four days where some of the city''s favorite restaurants open their doors with special menus made just for this event.'
WHERE CmsSectionId = @introSectionId
  AND ItemKey = 'intro_body';

-- Subsection 1: What is Yummy?
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSectionId, 'intro_sub1_heading', 'TEXT', 'What is Yummy?', NULL, NULL, NOW()
FROM DUAL WHERE @introSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSectionId AND ItemKey = 'intro_sub1_heading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSectionId, 'intro_sub1_text', 'TEXT', 'A festival of food where each restaurant offers one unique menu, set time slots, and special prices.', NULL, NULL, NOW()
FROM DUAL WHERE @introSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSectionId AND ItemKey = 'intro_sub1_text');

-- Subsection 2: Who takes part?
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSectionId, 'intro_sub2_heading', 'TEXT', 'Who takes part?', NULL, NULL, NOW()
FROM DUAL WHERE @introSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSectionId AND ItemKey = 'intro_sub2_heading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSectionId, 'intro_sub2_text', 'TEXT', 'Local chefs and restaurants from all around Haarlem, prepare with their own style a great variety of dishes, such as: Dutch-French-European-Fish & Seafood-Modern Vegan.', NULL, NULL, NOW()
FROM DUAL WHERE @introSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSectionId AND ItemKey = 'intro_sub2_text');

-- Subsection 3: How does it work?
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSectionId, 'intro_sub3_heading', 'TEXT', 'How does it work?', NULL, NULL, NOW()
FROM DUAL WHERE @introSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSectionId AND ItemKey = 'intro_sub3_heading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSectionId, 'intro_sub3_text', 'TEXT', 'Choose a restaurant, pick a time slot, and make a reservation. Seats are limited, so booking ahead is important.', NULL, NULL, NOW()
FROM DUAL WHERE @introSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSectionId AND ItemKey = 'intro_sub3_text');

-- Closing line
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSectionId, 'intro_closing', 'TEXT', 'Come enjoy great food, good company, and a warm festival atmosphere.', NULL, NULL, NOW()
FROM DUAL WHERE @introSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSectionId AND ItemKey = 'intro_closing');

-- =====================================================
-- FIX 3: intro2_body ("When Haarlem Becomes a Dining Room")
-- Original: '...dining room.\n\nFrom Thursday...\n\nJust enough...'
-- Should have real newlines between paragraphs.
-- =====================================================

SET @introSplit2SectionId = (
    SELECT cs.CmsSectionId
    FROM CmsSection cs
    JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
    WHERE cp.Slug = 'restaurant'
      AND cs.SectionKey = 'intro_split2_section'
    LIMIT 1
);

UPDATE CmsItem
SET TextValue = 'As the sun sets over Haarlem''s historic streets, the city slowly turns into one big dining room.

From Thursday to Sunday, each restaurant offers 2 to 3 sessions later afternoon, starting from 16:30 and lasting around 1.5 to 2 hours..

Just enough time to enjoy your plate, share a toast, and wander to the next event or performance nearby.'
WHERE CmsSectionId = @introSplit2SectionId
  AND ItemKey = 'intro2_body';

