-- ============================================================================
-- Migration: Only the remaining missing pieces
-- Database:  haarlem_festival_db
-- Date:      2026-03-17
--
-- Already done: A1 collation, A2 duplicate indexes, A3 index renaming,
--   A4 test data cleanup, OrderStatus + FK, PaymentStatus + FK,
--   PaymentMethod + FK
-- ============================================================================

START TRANSACTION;


-- ────────────────────────────────────────────────────────────────────────────
-- SessionType lookup + FK (table may already exist from a partial run)
-- ────────────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `SessionType` (
  `SessionType` varchar(40) NOT NULL,
  PRIMARY KEY (`SessionType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT IGNORE INTO `SessionType` (`SessionType`) VALUES
('Back2Back'),
('Club'),
('Dinner'),
('Live'),
('Podcast'),
('Storytelling'),
('TiëstoWorld'),
('Tour');

ALTER TABLE `EventSession`
  ADD CONSTRAINT `FK_EventSession_SessionType`
    FOREIGN KEY (`SessionType`) REFERENCES `SessionType` (`SessionType`);


-- ────────────────────────────────────────────────────────────────────────────
-- PassScope lookup + FK (table may already exist from a partial run)
-- ────────────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `PassScope` (
  `PassScope` varchar(20) NOT NULL,
  PRIMARY KEY (`PassScope`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT IGNORE INTO `PassScope` (`PassScope`) VALUES
('Day'),
('Range');

ALTER TABLE `PassType`
  ADD CONSTRAINT `FK_PassType_Scope`
    FOREIGN KEY (`PassScope`) REFERENCES `PassScope` (`PassScope`);


-- ────────────────────────────────────────────────────────────────────────────
-- Restaurant image FK constraints
-- ────────────────────────────────────────────────────────────────────────────

ALTER TABLE `Restaurant`
  ADD CONSTRAINT `FK_Restaurant_GalleryImage1`
    FOREIGN KEY (`GalleryImage1AssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_Restaurant_GalleryImage2`
    FOREIGN KEY (`GalleryImage2AssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_Restaurant_GalleryImage3`
    FOREIGN KEY (`GalleryImage3AssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_Restaurant_AboutImage`
    FOREIGN KEY (`AboutImageAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_Restaurant_ChefImage`
    FOREIGN KEY (`ChefImageAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_Restaurant_MenuImage1`
    FOREIGN KEY (`MenuImage1AssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_Restaurant_MenuImage2`
    FOREIGN KEY (`MenuImage2AssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_Restaurant_ReservationImage`
    FOREIGN KEY (`ReservationImageAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`);


COMMIT;
