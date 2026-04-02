-- Migration: align ProgramItem schema with History add-to-program flow
-- Date: 2026-04-02
-- Adds missing Group price tier and ProgramItem.PriceTierId expected by the app code.

START TRANSACTION;

INSERT INTO PriceTier (PriceTierId, Name)
SELECT 7, 'Group'
WHERE NOT EXISTS (
    SELECT 1
    FROM PriceTier
    WHERE PriceTierId = 7
);

SET @column_exists := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'ProgramItem'
      AND COLUMN_NAME = 'PriceTierId'
);

SET @sql := IF(
    @column_exists = 0,
    'ALTER TABLE ProgramItem ADD COLUMN PriceTierId INT(11) DEFAULT NULL AFTER Quantity',
    'SELECT ''ProgramItem.PriceTierId already exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'ProgramItem'
      AND INDEX_NAME = 'FK_ProgramItem_PriceTier'
);

SET @sql := IF(
    @index_exists = 0,
    'ALTER TABLE ProgramItem ADD KEY FK_ProgramItem_PriceTier (PriceTierId)',
    'SELECT ''ProgramItem.FK_ProgramItem_PriceTier already exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @fk_exists := (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'ProgramItem'
      AND CONSTRAINT_NAME = 'FK_ProgramItem_PriceTier'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);

SET @sql := IF(
    @fk_exists = 0,
    'ALTER TABLE ProgramItem ADD CONSTRAINT FK_ProgramItem_PriceTier FOREIGN KEY (PriceTierId) REFERENCES PriceTier (PriceTierId)',
    'SELECT ''ProgramItem.FK_ProgramItem_PriceTier already exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
