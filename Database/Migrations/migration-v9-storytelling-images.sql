-- =====================================================
-- Migration v9: Fix Storytelling Page Images
-- Run after migration-v7-storytelling-page.sql
-- Safe & Idempotent
-- Date: February 8, 2026
-- =====================================================

-- =====================================================
-- PART 1: Insert MediaAsset records for CMS-managed storytelling images
-- =====================================================

INSERT IGNORE INTO `MediaAsset` (`FilePath`, `OriginalFileName`, `MimeType`, `FileSizeBytes`, `AltText`) VALUES
-- Hero image
('/assets/Image/storytelling/hero-storytelling.jpg', 'hero-storytelling.jpg', 'image/jpeg', 0, 'Storytelling hero background'),
-- Gradient section background
('/assets/Image/storytelling/picture-looking-text.jpg', 'picture-looking-text.jpg', 'image/jpeg', 0, 'Storytelling gradient section background'),
-- Intro split section image
('/assets/Image/storytelling/where-stories-come-alive.jpg', 'where-stories-come-alive.jpg', 'image/jpeg', 0, 'Where stories come alive in Haarlem'),
-- Masonry gallery images
('/assets/Image/storytelling/d-student.jpg', 'd-student.jpg', 'image/jpeg', 0, 'Student storytelling moment'),
('/assets/Image/storytelling/d-student2.jpg', 'd-student2.jpg', 'image/jpeg', 0, 'Student storytelling moment 2'),
('/assets/Image/storytelling/m-student.jpg', 'm-student.jpg', 'image/jpeg', 0, 'Student storytelling performance'),
('/assets/Image/storytelling/winnie-the-pooh.jpg', 'winnie-the-pooh.jpg', 'image/jpeg', 0, 'Winnie the Pooh storytelling'),
('/assets/Image/storytelling/pig.jpg', 'pig.jpg', 'image/jpeg', 0, 'Pig character storytelling'),
('/assets/Image/storytelling/entrance-kweek.jpg', 'entrance-kweek.jpg', 'image/jpeg', 0, 'Kweek entrance storytelling venue'),
('/assets/Image/storytelling/building.jpg', 'building.jpg', 'image/jpeg', 0, 'Historic building storytelling venue'),
('/assets/Image/storytelling/anansi-pointing.png', 'anansi-pointing.png', 'image/png', 0, 'Anansi pointing storytelling moment'),
('/assets/Image/storytelling/anansi-conversation.jpg', 'anansi-conversation.jpg', 'image/jpeg', 0, 'Anansi conversation storytelling moment'),
('/assets/Image/storytelling/anansi-drip.jpg', 'anansi-drip.jpg', 'image/jpeg', 0, 'Anansi drip storytelling moment'),
('/assets/Image/storytelling/anansi-visser.jpg', 'anansi-visser.jpg', 'image/jpeg', 0, 'Anansi visser storytelling moment'),
('/assets/Image/storytelling/WinnieThePoohHeader.png', 'WinnieThePoohHeader.png', 'image/png', 0, 'Winnie the Pooh header image');

-- =====================================================
-- PART 2: Clean up any <p> tags in TEXT/HEADING fields
-- These were incorrectly added when TinyMCE was used for TEXT fields
-- =====================================================

UPDATE `CmsItem`
SET TextValue = REPLACE(REPLACE(REPLACE(REPLACE(TextValue, '<p>', ''), '</p>', ''), '\n', ' '), '  ', ' '),
    UpdatedAtUtc = NOW()
WHERE ItemType IN ('TEXT', 'HEADING', 'BUTTON_TEXT')
  AND TextValue LIKE '%<p>%';

-- =====================================================
-- PART 3: Update storytelling hero_background_image to correct path
-- =====================================================

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/hero-storytelling.jpg',
    ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling'
  AND cs.SectionKey = 'hero_section'
  AND ci.ItemKey = 'hero_background_image';

-- =====================================================
-- PART 4: Update storytelling intro_image to correct path
-- =====================================================

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/where-stories-come-alive.jpg',
    ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling'
  AND cs.SectionKey = 'intro_split_section'
  AND ci.ItemKey = 'intro_image';

-- =====================================================
-- PART 5: Update masonry images to correct paths
-- =====================================================

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/d-student.jpg', ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section' AND ci.ItemKey = 'masonry_image_01';

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/d-student2.jpg', ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section' AND ci.ItemKey = 'masonry_image_02';

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/m-student.jpg', ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section' AND ci.ItemKey = 'masonry_image_03';

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/winnie-the-pooh.jpg', ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section' AND ci.ItemKey = 'masonry_image_04';

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/pig.jpg', ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section' AND ci.ItemKey = 'masonry_image_05';

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/entrance-kweek.jpg', ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section' AND ci.ItemKey = 'masonry_image_06';

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/building.jpg', ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section' AND ci.ItemKey = 'masonry_image_07';

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/anansi-pointing.png', ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section' AND ci.ItemKey = 'masonry_image_08';

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/anansi-conversation.jpg', ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section' AND ci.ItemKey = 'masonry_image_09';

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/anansi-drip.jpg', ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section' AND ci.ItemKey = 'masonry_image_10';

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/anansi-visser.jpg', ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section' AND ci.ItemKey = 'masonry_image_11';

UPDATE `CmsItem` ci
INNER JOIN `CmsSection` cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN `CmsPage` cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/WinnieThePoohHeader.png', ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling' AND cs.SectionKey = 'masonry_section' AND ci.ItemKey = 'masonry_image_12';


