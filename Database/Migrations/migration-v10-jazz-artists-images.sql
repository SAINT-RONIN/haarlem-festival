-- =====================================================
-- Migration v10: Add MEDIA CMS items for Jazz artist lineup images
-- Purpose: Make lineup images editable & visible in CMS edit for Jazz page.
-- Idempotent: Uses WHERE NOT EXISTS.
-- =====================================================

START TRANSACTION;

-- Ensure the required MediaAssets exist
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText, CreatedAtUtc)
SELECT '/assets/Image/Jazz/Jazz-Gumbokings.png', 'Jazz-Gumbokings.png', 'image/png', 0, 'Gumbo Kings lineup image', UTC_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/Jazz/Jazz-Gumbokings.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText, CreatedAtUtc)
SELECT '/assets/Image/Jazz/Jazz-evolve.png', 'Jazz-evolve.png', 'image/png', 0, 'Evolve lineup image', UTC_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/Jazz/Jazz-evolve.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText, CreatedAtUtc)
SELECT '/assets/Image/Jazz/Jazz-Ntjam.png', 'Jazz-Ntjam.png', 'image/png', 0, 'Ntjam Rosie lineup image', UTC_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/Jazz/Jazz-Ntjam.png');

-- Resolve section id for Jazz artists_section
SET @jazzPageId := (SELECT CmsPageId FROM CmsPage WHERE Slug = 'jazz' LIMIT 1);
SET @artistsSectionId := (
    SELECT CmsSectionId
    FROM CmsSection
    WHERE CmsPageId = @jazzPageId AND SectionKey = 'artists_section'
    LIMIT 1
);

-- Insert CMS MEDIA items for lineup images
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT
    @artistsSectionId,
    'artists_gumbokings_image',
    'MEDIA',
    NULL,
    NULL,
    (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/Jazz/Jazz-Gumbokings.png' LIMIT 1),
    UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM CmsItem WHERE CmsSectionId = @artistsSectionId AND ItemKey = 'artists_gumbokings_image'
  );

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT
    @artistsSectionId,
    'artists_evolve_image',
    'MEDIA',
    NULL,
    NULL,
    (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/Jazz/Jazz-evolve.png' LIMIT 1),
    UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM CmsItem WHERE CmsSectionId = @artistsSectionId AND ItemKey = 'artists_evolve_image'
  );

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT
    @artistsSectionId,
    'artists_ntjam_image',
    'MEDIA',
    NULL,
    NULL,
    (SELECT MediaAssetId FROM MediaAsset WHERE FilePath = '/assets/Image/Jazz/Jazz-Ntjam.png' LIMIT 1),
    UTC_TIMESTAMP()
FROM DUAL
WHERE @artistsSectionId IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM CmsItem WHERE CmsSectionId = @artistsSectionId AND ItemKey = 'artists_ntjam_image'
  );

COMMIT;

