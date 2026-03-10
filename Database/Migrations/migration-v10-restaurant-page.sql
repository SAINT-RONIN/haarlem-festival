-- =====================================================
-- Migration v10: Restaurant Page CMS Content
-- Run after migration-v9-storytelling-images.sql
-- Safe & Idempotent: Uses WHERE NOT EXISTS checks
-- Date: February 10, 2026
-- =====================================================

-- =====================================================
-- PART 1: Create Restaurant page if not exists
-- =====================================================

INSERT INTO CmsPage (Slug, Title)
SELECT 'restaurant', 'Yummy! Restaurant Experience'
WHERE NOT EXISTS (SELECT 1 FROM CmsPage WHERE Slug = 'restaurant');

-- =====================================================
-- PART 2: Create Restaurant sections if not exist
-- =====================================================

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'hero_section'
FROM CmsPage cp WHERE cp.Slug = 'restaurant'
AND NOT EXISTS (
    SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'hero_section'
);

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'gradient_section'
FROM CmsPage cp WHERE cp.Slug = 'restaurant'
AND NOT EXISTS (
    SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'gradient_section'
);

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'intro_split_section'
FROM CmsPage cp WHERE cp.Slug = 'restaurant'
AND NOT EXISTS (
    SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'intro_split_section'
);

-- =====================================================
-- PART 3: Insert Restaurant Hero Section Items
-- =====================================================

-- Hero main title
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_main_title', 'HEADING', 'Yummy', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_main_title'
);

-- Hero subtitle
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_subtitle', 'TEXT', 'Gourmet with a Twist', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_subtitle'
);

-- Hero background image
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/restaurants/hero-picture.png', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_background_image'
);

-- Hero button primary (empty - no buttons for restaurant)
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_button_primary', 'BUTTON_TEXT', '', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_primary'
);

-- Hero button primary link (empty)
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_button_primary_link', 'LINK', '', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_primary_link'
);

-- Hero button secondary (empty)
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_button_secondary', 'BUTTON_TEXT', '', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_secondary'
);

-- Hero button secondary link (empty)
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_button_secondary_link', 'LINK', '', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_secondary_link'
);

-- =====================================================
-- PART 4: Insert Restaurant Gradient Section Items
-- =====================================================

-- Gradient heading
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'gradient_heading', 'HEADING', 'Good food tastes better when shared.', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'gradient_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'gradient_heading'
);

-- Gradient subheading
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'gradient_subheading', 'TEXT', 'Food, stories, and shared moments across Haarlem.', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'gradient_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'gradient_subheading'
);

-- Gradient background image
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'gradient_background_image', 'IMAGE_PATH', '/assets/Image/restaurants/chef-preparing-food.png', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'gradient_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'gradient_background_image'
);

-- =====================================================
-- PART 5: Insert Restaurant Intro Split Section Items
-- =====================================================

-- Intro heading
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'intro_heading', 'HEADING', 'Yummy! at the Heart of the Festival', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_heading'
);

-- Intro body (single blob with ## markers for subsections)
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'intro_body', 'TEXT', 'Welcome to Yummy!, the food experience of the Haarlem Festival. Four days where some of the city''s favorite restaurants open their doors with special menus made just for this event.

## What is Yummy?
A festival of food where each restaurant offers one unique menu, set time slots, and special prices.

## Who takes part?
Local chefs and restaurants from all around Haarlem, prepare with their own style a great variety of dishes, such as: Dutch-French-European-Fish & Seafood-Modern Vegan.

## How does it work?
Choose a restaurant, pick a time slot, and make a reservation. Seats are limited, so booking ahead is important.

## Closing
Come enjoy great food, good company, and a warm festival atmosphere.', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_body'
);

-- Intro image
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'intro_image', 'IMAGE_PATH', '/assets/Image/restaurants/table-with-food-and-drink.png', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_image'
);

-- Intro image alt text
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'intro_image_alt', 'TEXT', 'Yummy! at the Heart of the Festival', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'restaurant' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_image_alt'
);

