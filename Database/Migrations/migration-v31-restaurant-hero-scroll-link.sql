-- migration-v31-restaurant-hero-scroll-link.sql
-- Updates the restaurant page hero primary button link from '#restaurants'
-- to '#restaurants-grid', which is the actual id of the restaurant cards section.

UPDATE CmsItem ci
INNER JOIN CmsSection cs ON ci.CmsSectionId = cs.CmsSectionId
INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
SET ci.TextValue = '#restaurants-grid'
WHERE ci.ItemKey = 'hero_button_primary_link'
  AND ci.TextValue = '#restaurants'
  AND cp.Slug = 'restaurant'
  AND cs.SectionKey = 'hero_section';