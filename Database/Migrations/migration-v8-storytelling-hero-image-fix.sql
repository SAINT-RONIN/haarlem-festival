-- =====================================================
-- Migration v8: Fix Storytelling Hero Image Path
-- Run after migration-v7-storytelling-page.sql
-- Safe & Idempotent: Only updates if old path exists
-- Date: February 8, 2026
-- =====================================================

-- Update the storytelling hero_background_image to correct path if it has the old value
UPDATE CmsItem ci
INNER JOIN CmsSection cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '/assets/Image/storytelling/hero-storytelling.jpg',
    ci.UpdatedAtUtc = NOW()
WHERE cp.Slug = 'storytelling'
  AND cs.SectionKey = 'hero_section'
  AND ci.ItemKey = 'hero_background_image'
  AND ci.TextValue = '/assets/Image/Image (Story).png';

-- =====================================================
-- Note: This migration is only needed if migration-v7-storytelling-page.sql
-- was already run with the old image path. If migration-v7 hasn't been run yet,
-- the updated v7 file will insert the correct path directly.
-- =====================================================

