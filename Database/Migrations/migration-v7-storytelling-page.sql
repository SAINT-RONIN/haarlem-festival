-- =====================================================
-- Migration v7: Storytelling Page CMS Content
-- Run after migration-v6-auth.sql
-- Safe & Idempotent: Uses WHERE NOT EXISTS checks
-- Date: February 8, 2026
-- =====================================================

-- =====================================================
-- PART 1: Add missing items to HOMEPAGE hero section
-- =====================================================

-- Add hero_button_primary_link if not exists
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_button_primary_link', 'LINK', '#events', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'home' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci
    WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_primary_link'
);

-- Add hero_button_secondary_link if not exists
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_button_secondary_link', 'LINK', '#schedule', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'home' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci
    WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_secondary_link'
);

-- Add hero_background_image if not exists
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/HeroImageHome.png', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'home' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci
    WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_background_image'
);

-- =====================================================
-- PART 2: Create Storytelling page if not exists
-- =====================================================

INSERT INTO CmsPage (Slug, Title)
SELECT 'storytelling', 'Stories in Haarlem'
WHERE NOT EXISTS (SELECT 1 FROM CmsPage WHERE Slug = 'storytelling');

-- =====================================================
-- PART 3: Create Storytelling sections if not exist
-- =====================================================

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'hero_section'
FROM CmsPage cp WHERE cp.Slug = 'storytelling'
AND NOT EXISTS (
    SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'hero_section'
);

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'gradient_section'
FROM CmsPage cp WHERE cp.Slug = 'storytelling'
AND NOT EXISTS (
    SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'gradient_section'
);

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'intro_split_section'
FROM CmsPage cp WHERE cp.Slug = 'storytelling'
AND NOT EXISTS (
    SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'intro_split_section'
);

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT cp.CmsPageId, 'masonry_section'
FROM CmsPage cp WHERE cp.Slug = 'storytelling'
AND NOT EXISTS (
    SELECT 1 FROM CmsSection cs WHERE cs.CmsPageId = cp.CmsPageId AND cs.SectionKey = 'masonry_section'
);

-- =====================================================
-- PART 4: Insert Storytelling Hero Section Items
-- =====================================================

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_main_title', 'HEADING', 'Stories in Haarlem', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_main_title'
);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_subtitle', 'TEXT', 'Where Every Voice Finds Its Stage', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_subtitle'
);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_button_primary', 'BUTTON_TEXT', 'Discover storytelling events', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_primary'
);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_button_primary_link', 'LINK', '#events', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_primary_link'
);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_button_secondary', 'BUTTON_TEXT', 'View schedule', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_secondary'
);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_button_secondary_link', 'LINK', '#schedule', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_button_secondary_link'
);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/storytelling/hero-storytelling.jpg', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'hero_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'hero_background_image'
);

-- =====================================================
-- PART 5: Insert Storytelling Gradient Section Items
-- =====================================================

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'gradient_heading', 'HEADING', 'Every story carries emotion, intention, and connection beyond what we say aloud.', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'gradient_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'gradient_heading'
);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'gradient_subheading', 'TEXT', 'The world where stories are not just told but truly experienced.', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'gradient_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'gradient_subheading'
);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'gradient_background_image', 'IMAGE_PATH', '/assets/Image/storytelling/picture-looking-text.jpg', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'gradient_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'gradient_background_image'
);

-- =====================================================
-- PART 6: Insert Storytelling Intro Split Section Items
-- =====================================================

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'intro_heading', 'HEADING', 'Where stories come alive in Haarlem', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_heading'
);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'intro_body', 'TEXT', 'Stories in Haarlem is a new part of The Festival that is quite exciting. It brings together people who tell stories to kids, create live podcasts, run businesses in the local circular economy, and historical voices from the Corrie Ten Boom home. Visitors may hear tales in Dutch and English that are made for various age groups and shared in different places across the city.

Each story has been carefully selected and prepared with great effort to offer meaningful, inspiring, and memorable experiences. The event aims to spark imagination, support local creators, and help visitors connect with Haarlem in a new and meaningful way.

To make the event open to everyone, certain sessions are pay-as-you-like. Guests may also give any amount they choose to help the storytellers and the organizations they support. People that participate not only appreciate the tales, but they also help make this event happen.', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_body'
);

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'intro_image', 'IMAGE_PATH', '/assets/Image/storytelling/where-stories-come-alive.jpg', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'intro_split_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'intro_image'
);


-- =====================================================
-- PART 7: Insert Storytelling Masonry Section Items
-- Masonry images are CMS-managed (12 images total)
-- =====================================================

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'masonry_heading', 'HEADING', 'Moments you are about to discover', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'masonry_heading'
);

-- Masonry Image 01
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'masonry_image_01', 'IMAGE_PATH', '/assets/Image/storytelling/d-student.jpg', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'masonry_image_01'
);

-- Masonry Image 02
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'masonry_image_02', 'IMAGE_PATH', '/assets/Image/storytelling/d-student2.jpg', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'masonry_image_02'
);

-- Masonry Image 03
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'masonry_image_03', 'IMAGE_PATH', '/assets/Image/storytelling/m-student.jpg', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'masonry_image_03'
);

-- Masonry Image 04
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'masonry_image_04', 'IMAGE_PATH', '/assets/Image/storytelling/winnie-the-pooh.jpg', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'masonry_image_04'
);

-- Masonry Image 05
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'masonry_image_05', 'IMAGE_PATH', '/assets/Image/storytelling/pig.jpg', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'masonry_image_05'
);

-- Masonry Image 06
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'masonry_image_06', 'IMAGE_PATH', '/assets/Image/storytelling/entrance-kweek.jpg', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'masonry_image_06'
);

-- Masonry Image 07
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'masonry_image_07', 'IMAGE_PATH', '/assets/Image/storytelling/building.jpg', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'masonry_image_07'
);

-- Masonry Image 08
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'masonry_image_08', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-pointing.png', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'masonry_image_08'
);

-- Masonry Image 09
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'masonry_image_09', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-conversation.jpg', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'masonry_image_09'
);

-- Masonry Image 10
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'masonry_image_10', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-drip.jpg', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'masonry_image_10'
);

-- Masonry Image 11
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'masonry_image_11', 'IMAGE_PATH', '/assets/Image/storytelling/anansi-visser.jpg', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'masonry_image_11'
);

-- Masonry Image 12
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, UpdatedAtUtc)
SELECT cs.CmsSectionId, 'masonry_image_12', 'IMAGE_PATH', '/assets/Image/storytelling/WinnieThePoohHeader.png', NOW()
FROM CmsSection cs
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section'
AND NOT EXISTS (
    SELECT 1 FROM CmsItem ci WHERE ci.CmsSectionId = cs.CmsSectionId AND ci.ItemKey = 'masonry_image_12'
);



