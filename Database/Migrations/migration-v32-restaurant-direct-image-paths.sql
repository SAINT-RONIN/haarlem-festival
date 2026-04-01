-- migration-v32-restaurant-direct-image-paths.sql
-- Replaces the RestaurantImage table approach with direct path columns on Restaurant.
-- Simpler: no JOIN to MediaAsset or RestaurantImage needed — path is right on the row.
--
-- Steps:
--   1. Add VARCHAR path columns to Restaurant
--   2. Populate them from the existing *AssetId columns via MediaAsset JOIN
--   3. Drop the FK constraints on the old *AssetId columns
--   4. Drop the now-redundant *AssetId columns

-- ─────────────────────────────────────────────────────────────
-- 1. Add direct image path columns
-- ─────────────────────────────────────────────────────────────
ALTER TABLE Restaurant
    ADD COLUMN IF NOT EXISTS AboutImagePath        VARCHAR(500) NULL AFTER SpecialRequestsNote,
    ADD COLUMN IF NOT EXISTS ChefImagePath         VARCHAR(500) NULL AFTER AboutImagePath,
    ADD COLUMN IF NOT EXISTS MenuImage1Path        VARCHAR(500) NULL AFTER ChefImagePath,
    ADD COLUMN IF NOT EXISTS MenuImage2Path        VARCHAR(500) NULL AFTER MenuImage1Path,
    ADD COLUMN IF NOT EXISTS GalleryImage1Path     VARCHAR(500) NULL AFTER MenuImage2Path,
    ADD COLUMN IF NOT EXISTS GalleryImage2Path     VARCHAR(500) NULL AFTER GalleryImage1Path,
    ADD COLUMN IF NOT EXISTS GalleryImage3Path     VARCHAR(500) NULL AFTER GalleryImage2Path,
    ADD COLUMN IF NOT EXISTS ReservationImagePath  VARCHAR(500) NULL AFTER GalleryImage3Path;

-- ─────────────────────────────────────────────────────────────
-- 2. Copy paths from old AssetId columns via MediaAsset JOIN
-- ─────────────────────────────────────────────────────────────
UPDATE Restaurant r
    LEFT JOIN MediaAsset aboutAsset ON r.AboutImageAssetId       = aboutAsset.MediaAssetId
    LEFT JOIN MediaAsset chefAsset  ON r.ChefImageAssetId        = chefAsset.MediaAssetId
    LEFT JOIN MediaAsset menu1Asset ON r.MenuImage1AssetId       = menu1Asset.MediaAssetId
    LEFT JOIN MediaAsset menu2Asset ON r.MenuImage2AssetId       = menu2Asset.MediaAssetId
    LEFT JOIN MediaAsset gal1Asset  ON r.GalleryImage1AssetId    = gal1Asset.MediaAssetId
    LEFT JOIN MediaAsset gal2Asset  ON r.GalleryImage2AssetId    = gal2Asset.MediaAssetId
    LEFT JOIN MediaAsset gal3Asset  ON r.GalleryImage3AssetId    = gal3Asset.MediaAssetId
    LEFT JOIN MediaAsset resAsset   ON r.ReservationImageAssetId = resAsset.MediaAssetId
SET
    r.AboutImagePath       = aboutAsset.FilePath,
    r.ChefImagePath        = chefAsset.FilePath,
    r.MenuImage1Path       = menu1Asset.FilePath,
    r.MenuImage2Path       = menu2Asset.FilePath,
    r.GalleryImage1Path    = gal1Asset.FilePath,
    r.GalleryImage2Path    = gal2Asset.FilePath,
    r.GalleryImage3Path    = gal3Asset.FilePath,
    r.ReservationImagePath = resAsset.FilePath;

-- ─────────────────────────────────────────────────────────────
-- 3. Drop FK constraints on the old AssetId columns
--    (required before columns can be dropped)
-- ─────────────────────────────────────────────────────────────
ALTER TABLE Restaurant
    DROP FOREIGN KEY IF EXISTS FK_Restaurant_AboutImage,
    DROP FOREIGN KEY IF EXISTS FK_Restaurant_ChefImage,
    DROP FOREIGN KEY IF EXISTS FK_Restaurant_GalleryImage1,
    DROP FOREIGN KEY IF EXISTS FK_Restaurant_GalleryImage2,
    DROP FOREIGN KEY IF EXISTS FK_Restaurant_GalleryImage3,
    DROP FOREIGN KEY IF EXISTS FK_Restaurant_MenuImage1,
    DROP FOREIGN KEY IF EXISTS FK_Restaurant_MenuImage2,
    DROP FOREIGN KEY IF EXISTS FK_Restaurant_ReservationImage;

-- ─────────────────────────────────────────────────────────────
-- 4. Drop the old AssetId columns (indexes are dropped automatically)
-- ─────────────────────────────────────────────────────────────
ALTER TABLE Restaurant
    DROP COLUMN IF EXISTS GalleryImage1AssetId,
    DROP COLUMN IF EXISTS GalleryImage2AssetId,
    DROP COLUMN IF EXISTS GalleryImage3AssetId,
    DROP COLUMN IF EXISTS AboutImageAssetId,
    DROP COLUMN IF EXISTS ChefImageAssetId,
    DROP COLUMN IF EXISTS MenuImage1AssetId,
    DROP COLUMN IF EXISTS MenuImage2AssetId,
    DROP COLUMN IF EXISTS ReservationImageAssetId;