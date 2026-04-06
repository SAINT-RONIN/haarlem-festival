START TRANSACTION;

INSERT INTO `CmsPage` (`Title`, `Slug`, `IsActive`, `CreatedAtUtc`, `UpdatedAtUtc`)
VALUES ('Yummy', 'yummy', 1, NOW(), NOW());
SET @pageId = LAST_INSERT_ID();

INSERT INTO `CmsSection` (`CmsPageId`, `SectionKey`, `CreatedAtUtc`, `UpdatedAtUtc`)
VALUES (@pageId, 'hero', NOW(), NOW());
SET @sectionId = LAST_INSERT_ID();

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `CreatedAtUtc`, `UpdatedAtUtc`)
VALUES (@sectionId, 'hero_main_title', 'HEADING', 'Yummy page works', NOW(), NOW());

COMMIT;
