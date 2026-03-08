-- =====================================================
-- COMPLETE Restaurant Migration DAY 08/03/2026
-- Run this ONE file to set up everything for /restaurant
--
-- What it does (in order):
--   PART A — Domain & Schema
--     1. Card images → MediaAsset + link to Restaurant
--     2. Renumber RestaurantId to alphabetical order
--     3. Add detail page columns (about, chef, menu, etc.)
--     4. Seed Ratatouille detail content
--     5. Ratatouille detail images → MediaAsset + link
--     6. Seed Toujours detail content
--     7. Toujours detail images → MediaAsset + link
--
--   PART B — CMS Content
--     8.  Create 'restaurant' CMS page + 7 sections
--     9.  Seed listing page items (hero, gradient, intro, etc.)
--     10. Seed detail page CMS labels (section titles, buttons)
--
-- Safe & Idempotent: WHERE NOT EXISTS + IF NOT EXISTS
-- Date: March 8, 2026
-- =====================================================

-- ═══════════════════════════════════════════════════════════
-- PART A: DOMAIN & SCHEMA
-- ═══════════════════════════════════════════════════════════
START TRANSACTION;
-- PART 1: Card Image MediaAssets (v26)
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/Restaurant-CafeDeRoemer-card.png', 'Restaurant-CafeDeRoemer-card.png', 'image/png', 0, 'Cafe de Roemer' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-CafeDeRoemer-card.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/Restaurant-Ratatouille-card.png', 'Restaurant-Ratatouille-card.png', 'image/png', 0, 'Ratatouille' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-Ratatouille-card.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/Restaurant-RestaurantML-card.png', 'Restaurant-RestaurantML-card.png', 'image/png', 0, 'Restaurant ML' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-RestaurantML-card.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/Restaurant-RestaurantFris-card.png', 'Restaurant-RestaurantFris-card.png', 'image/png', 0, 'Restaurant Fris' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-RestaurantFris-card.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/Restaurant-NewVegas-card.jpg', 'Restaurant-NewVegas-card.jpg', 'image/jpeg', 0, 'New Vegas' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-NewVegas-card.jpg');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/Restaurant-GrandCafeBrinkman-card.png', 'Restaurant-GrandCafeBrinkman-card.png', 'image/png', 0, 'Grand Cafe Brinkman' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-GrandCafeBrinkman-card.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/Restaurant-UrbanFrenchyBistroToujours-card.png', 'Restaurant-UrbanFrenchyBistroToujours-card.png', 'image/png', 0, 'Urban Frenchy Bistro Toujours' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/Restaurant-UrbanFrenchyBistroToujours-card.png');
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-CafeDeRoemer-card.png' SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'Cafe de Roemer';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-Ratatouille-card.png' SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'Ratatouille';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-RestaurantML-card.png' SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'Restaurant ML';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-RestaurantFris-card.png' SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'Restaurant Fris';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-NewVegas-card.jpg' SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'New Vegas';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-GrandCafeBrinkman-card.png' SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'Grand Cafe Brinkman';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/Restaurant-UrbanFrenchyBistroToujours-card.png' SET r.ImageAssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';
-- PART 2: Renumber Restaurant IDs
-- Goal: Ratatouille=1, Urban Frenchy Bistro Toujours=2, rest=3-7
-- Original seed IDs: 1=CaféDeRoemer, 2=Ratatouille, 3=RestaurantML,
--   4=RestaurantFris, 5=NewVegas, 6=GrandCafeBrinkman, 7=Toujours
ALTER TABLE Event DROP FOREIGN KEY IF EXISTS FK_Event_Restaurant;

-- Move all to temp IDs to avoid conflicts
UPDATE Event SET RestaurantId = 101 WHERE RestaurantId = 1;
UPDATE Event SET RestaurantId = 102 WHERE RestaurantId = 2;
UPDATE Event SET RestaurantId = 103 WHERE RestaurantId = 3;
UPDATE Event SET RestaurantId = 104 WHERE RestaurantId = 4;
UPDATE Event SET RestaurantId = 105 WHERE RestaurantId = 5;
UPDATE Event SET RestaurantId = 106 WHERE RestaurantId = 6;
UPDATE Event SET RestaurantId = 107 WHERE RestaurantId = 7;
UPDATE Restaurant SET RestaurantId = 101 WHERE RestaurantId = 1;
UPDATE Restaurant SET RestaurantId = 102 WHERE RestaurantId = 2;
UPDATE Restaurant SET RestaurantId = 103 WHERE RestaurantId = 3;
UPDATE Restaurant SET RestaurantId = 104 WHERE RestaurantId = 4;
UPDATE Restaurant SET RestaurantId = 105 WHERE RestaurantId = 5;
UPDATE Restaurant SET RestaurantId = 106 WHERE RestaurantId = 6;
UPDATE Restaurant SET RestaurantId = 107 WHERE RestaurantId = 7;

-- Assign final IDs
UPDATE Restaurant SET RestaurantId = 1 WHERE RestaurantId = 102;  -- Ratatouille
UPDATE Restaurant SET RestaurantId = 2 WHERE RestaurantId = 107;  -- Urban Frenchy Bistro Toujours
UPDATE Restaurant SET RestaurantId = 3 WHERE RestaurantId = 101;  -- Café de Roemer
UPDATE Restaurant SET RestaurantId = 4 WHERE RestaurantId = 106;  -- Grand Cafe Brinkman
UPDATE Restaurant SET RestaurantId = 5 WHERE RestaurantId = 105;  -- New Vegas
UPDATE Restaurant SET RestaurantId = 6 WHERE RestaurantId = 104;  -- Restaurant Fris
UPDATE Restaurant SET RestaurantId = 7 WHERE RestaurantId = 103;  -- Restaurant ML
UPDATE Event SET RestaurantId = 1 WHERE RestaurantId = 102;
UPDATE Event SET RestaurantId = 2 WHERE RestaurantId = 107;
UPDATE Event SET RestaurantId = 3 WHERE RestaurantId = 101;
UPDATE Event SET RestaurantId = 4 WHERE RestaurantId = 106;
UPDATE Event SET RestaurantId = 5 WHERE RestaurantId = 105;
UPDATE Event SET RestaurantId = 6 WHERE RestaurantId = 104;
UPDATE Event SET RestaurantId = 7 WHERE RestaurantId = 103;

ALTER TABLE Event ADD CONSTRAINT FK_Event_Restaurant FOREIGN KEY (RestaurantId) REFERENCES Restaurant (RestaurantId);
ALTER TABLE Restaurant AUTO_INCREMENT = 8;
-- PART 3: Detail Page Columns (v29)
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS Phone VARCHAR(50) DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS Email VARCHAR(150) DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS Website VARCHAR(255) DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS AboutText TEXT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS ChefName VARCHAR(150) DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS ChefText TEXT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS MenuDescription TEXT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS LocationDescription TEXT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS MapEmbedUrl VARCHAR(1024) DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS MichelinStars INT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS SeatsPerSession INT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS DurationMinutes INT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS SpecialRequestsNote VARCHAR(500) DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS GalleryImage1AssetId INT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS GalleryImage2AssetId INT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS GalleryImage3AssetId INT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS AboutImageAssetId INT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS ChefImageAssetId INT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS MenuImage1AssetId INT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS MenuImage2AssetId INT DEFAULT NULL;
ALTER TABLE Restaurant ADD COLUMN IF NOT EXISTS ReservationImageAssetId INT DEFAULT NULL;
-- PART 4: Ratatouille Detail Content (v29)
UPDATE Restaurant SET Phone = '+31 (0)23 123 4567', Email = 'info@ratatouille.nl', Website = 'ratatouillefoodandwine.nl', AboutText = 'Welcome to <strong>Ratatouille</strong>, a Michelin-starred French restaurant on the Spaarne, known for turning fine dining into something <strong>warm, creative, and surprisingly accessible</strong>.\\n\\nEvery dish is prepared with <strong>seasonal ingredients, bright flavors,</strong> and a touch of <strong>French elegance.</strong> For the Yummy! festival, Ratatouille offers one special menu.\\n\\n<strong>Expect plates that feel refined,</strong> crafted with the same <strong>creativity</strong> that makes Ratatouille so loved.', ChefName = 'Jozua Jaring', ChefText = '<strong>Jozua Jaring</strong> leads the kitchen at Ratatouille. Under his leadership, the restaurant earned a <strong>Michelin star</strong>.\\n\\nHis cooking style is simple at its core. He starts with good ingredients and then adds creativity.\\n\\nFor the <strong>Yummy! festival</strong>, Chef Jaring designed a special menu.', MenuDescription = 'For the Yummy! festival, guests enjoy a set menu specially created by Ratatouille.', LocationDescription = 'Ratatouille is located by the river Spaarne, right in the center of Haarlem.\\n\\nThe <strong>Patronaat</strong> is just a 5-minute walk away.\\n\\nWhether you want to explore the city, enjoy a show, or continue your festival evening, Ratatouille is in a perfect location.', MapEmbedUrl = 'https://maps.google.com/maps?q=Spaarne+96,+2011+CL+Haarlem,+Netherlands&t=&z=16&ie=UTF8&iwloc=&output=embed', MichelinStars = 1, SeatsPerSession = 35, DurationMinutes = 120, SpecialRequestsNote = 'Dietary needs, allergies, or accessibility requests can be added during the reservation.' WHERE Name = 'Ratatouille';
-- PART 5: Ratatouille Detail Images (v30)
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/ratatouille-gallery-1.png', 'ratatouille-gallery-1.png', 'image/png', 0, 'Gallery 1' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-gallery-1.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/ratatouille-gallery-2.png', 'ratatouille-gallery-2.png', 'image/png', 0, 'Gallery 2' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-gallery-2.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/ratatouille-gallery-3.png', 'ratatouille-gallery-3.png', 'image/png', 0, 'Gallery 3' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-gallery-3.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/ratatouille-about.png', 'ratatouille-about.png', 'image/png', 0, 'About' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-about.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/ratatouille-chef.png', 'ratatouille-chef.png', 'image/png', 0, 'Chef' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-chef.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/ratatouille-menu-1.png', 'ratatouille-menu-1.png', 'image/png', 0, 'Menu 1' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-menu-1.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/ratatouille-menu-2.png', 'ratatouille-menu-2.png', 'image/png', 0, 'Menu 2' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-menu-2.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/ratatouille-reservation.jpg', 'ratatouille-reservation.jpg', 'image/jpeg', 0, 'Reservation' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/ratatouille-reservation.jpg');
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/ratatouille-gallery-1.png' SET r.GalleryImage1AssetId = ma.MediaAssetId WHERE r.Name = 'Ratatouille';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/ratatouille-gallery-2.png' SET r.GalleryImage2AssetId = ma.MediaAssetId WHERE r.Name = 'Ratatouille';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/ratatouille-gallery-3.png' SET r.GalleryImage3AssetId = ma.MediaAssetId WHERE r.Name = 'Ratatouille';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/ratatouille-about.png' SET r.AboutImageAssetId = ma.MediaAssetId WHERE r.Name = 'Ratatouille';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/ratatouille-chef.png' SET r.ChefImageAssetId = ma.MediaAssetId WHERE r.Name = 'Ratatouille';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/ratatouille-menu-1.png' SET r.MenuImage1AssetId = ma.MediaAssetId WHERE r.Name = 'Ratatouille';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/ratatouille-menu-2.png' SET r.MenuImage2AssetId = ma.MediaAssetId WHERE r.Name = 'Ratatouille';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/ratatouille-reservation.jpg' SET r.ReservationImageAssetId = ma.MediaAssetId WHERE r.Name = 'Ratatouille';

-- PART 6: Urban Frenchy Bistro Toujours Detail Content
UPDATE Restaurant SET
    Phone = '+31 023 532 1699',
    Email = 'info@toujours.nl',
    Website = 'restauranttoujours.nl',
    AboutText = '<strong>Urban Frenchy Bistro Toujours </strong>is a stylish and welcoming spot in the heart of Haarlem. The restaurant is known for its <strong>cozy boudoir-style interior, warm lighting, and relaxed atmosphere</strong> that makes every visit feel special. Toujours brings together Dutch freshness, European comfort, and a strong focus on seafood, creating a menu that feels both modern and approachable.\\n\\nFor the Yummy! festival, Toujours offers a special menu built around the dishes they do best: <strong>bright seafood plates, familiar European flavors, </strong>and<strong> ingredients that celebrate local Dutch products</strong>. It''s a perfect stop for anyone who wants a relaxed, flavorful meal before exploring the rest of the festival.\\n\\nWhether you''re visiting with friends, family, or enjoying the evening on your own, Toujours brings together great food and a warm setting to make your festival night feel complete.',
    ChefName = 'Georgiana Viou',
    ChefText = 'Chef <strong>Georgiana Viou </strong>guides the Toujours kitchen with a focus on fresh seafood, seasonal produce, and relaxed European flavors. His cooking is warm, welcoming, and built around good ingredients.\\n\\n<strong>For Yummy!, the chef created a menu</strong> that reflects what Toujours is known for. Every dish is prepared with care, keeping the <strong>experience warm, relaxed, and enjoyable for everyone.</strong>',
    MenuDescription = 'Toujours focuses on fresh seafood, Dutch ingredients, and European bistro-style cooking. Their plates often feature fish, seasonal produce, and comforting flavors that feel both elevated and easy to enjoy.\\n\\nExpect dishes that highlight seafood, local produce, and warm European influences. It''s perfect for a relaxed and tasty festival dinner.',
    LocationDescription = 'Toujours is located at <strong>Oude Groenmarkt 10-12</strong>, a lively square in the center of Haarlem and only steps away from the <strong>Grote Markt</strong>, one of the festival''s busiest spots. Its central location makes it an easy stop before or after concerts, events, and evening activities.\\n\\nThe <strong>Jopenkerk</strong> is only 2 minutes walk away and the area is full of energy, history, and festival life. A great combination for anyone wanting to combine great food with a memorable night out.\\n\\n<strong>Address: </strong> Oude Groenmarkt 10-12, 2011 HL Haarlem, Nederland',
    MapEmbedUrl = 'https://maps.google.com/maps?q=Oude+Groenmarkt+10,+2011+HL+Haarlem,+Netherlands&t=&z=16&ie=UTF8&iwloc=&output=embed',
    MichelinStars = 0,
    SeatsPerSession = 48,
    DurationMinutes = 90,
    SpecialRequestsNote = 'Dietary needs, allergies, or accessibility requests can be added during the reservation.'
WHERE Name = 'Urban Frenchy Bistro Toujours';

-- PART 7: Urban Frenchy Bistro Toujours Detail Images
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/toujours-gallery-1.png', 'toujours-gallery-1.png', 'image/png', 0, 'Toujours Gallery 1' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-gallery-1.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/toujours-gallery-2.png', 'toujours-gallery-2.png', 'image/png', 0, 'Toujours Gallery 2' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-gallery-2.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/toujours-gallery-3.png', 'toujours-gallery-3.png', 'image/png', 0, 'Toujours Gallery 3' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-gallery-3.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/toujours-about.png', 'toujours-about.png', 'image/png', 0, 'About Toujours' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-about.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/toujours-chef.jpg', 'toujours-chef.jpg', 'image/jpeg', 0, 'Chef Toujours' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-chef.jpg');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/toujours-menu-1.png', 'toujours-menu-1.png', 'image/png', 0, 'Toujours Menu 1' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-menu-1.png');
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) SELECT '/assets/Image/restaurants/toujours-menu-2.png', 'toujours-menu-2.png', 'image/png', 0, 'Toujours Menu 2' WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-menu-2.png');

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-gallery-1.png' SET r.GalleryImage1AssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-gallery-2.png' SET r.GalleryImage2AssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-gallery-3.png' SET r.GalleryImage3AssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-about.png' SET r.AboutImageAssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-chef.jpg' SET r.ChefImageAssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-menu-1.png' SET r.MenuImage1AssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-menu-2.png' SET r.MenuImage2AssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/ratatouille-reservation.jpg' SET r.ReservationImageAssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';
COMMIT;
SELECT 'Part A done: Restaurant domain & schema complete!' AS Status;

-- ═══════════════════════════════════════════════════════════
-- PART B: CMS CONTENT
-- ═══════════════════════════════════════════════════════════
-- Creates the 'restaurant' CMS page + 7 sections

START TRANSACTION;

-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
-- PART 1: Page + Sections
-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

INSERT INTO CmsPage (Slug, Title)
SELECT 'restaurant', 'Yummy! Restaurant Experience'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM CmsPage WHERE Slug = 'restaurant');

SET @restaurantPageId := (SELECT CmsPageId FROM CmsPage WHERE Slug = 'restaurant' LIMIT 1);

-- 7 sections: hero, gradient, intro_split, intro_split2, instructions, cards, detail
INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @restaurantPageId, 'hero_section' FROM DUAL
WHERE @restaurantPageId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'hero_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @restaurantPageId, 'gradient_section' FROM DUAL
WHERE @restaurantPageId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'gradient_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @restaurantPageId, 'intro_split_section' FROM DUAL
WHERE @restaurantPageId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'intro_split_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @restaurantPageId, 'intro_split2_section' FROM DUAL
WHERE @restaurantPageId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'intro_split2_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @restaurantPageId, 'instructions_section' FROM DUAL
WHERE @restaurantPageId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'instructions_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @restaurantPageId, 'restaurant_cards_section' FROM DUAL
WHERE @restaurantPageId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'restaurant_cards_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @restaurantPageId, 'detail_section' FROM DUAL
WHERE @restaurantPageId IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'detail_section');

-- Get all section IDs
SET @heroSectionId       := (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'hero_section' LIMIT 1);
SET @gradientSectionId   := (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'gradient_section' LIMIT 1);
SET @introSplitSectionId := (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'intro_split_section' LIMIT 1);
SET @introSplit2SectionId:= (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'intro_split2_section' LIMIT 1);
SET @instructionsSectionId:=(SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'instructions_section' LIMIT 1);
SET @cardsSectionId      := (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'restaurant_cards_section' LIMIT 1);
SET @detailSectionId     := (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'detail_section' LIMIT 1);

-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
-- PART 2: Hero Section Items
-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @heroSectionId, 'hero_main_title', 'HEADING', 'Yummy Gourmet with a Twist', NULL, NULL, NOW()
FROM DUAL WHERE @heroSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @heroSectionId AND ItemKey = 'hero_main_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @heroSectionId, 'hero_subtitle', 'TEXT', 'Discover 7 gourmet restaurants offering exclusive festival\nmenus crafted by top local chefs.', NULL, NULL, NOW()
FROM DUAL WHERE @heroSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @heroSectionId AND ItemKey = 'hero_subtitle');

-- Fix: ensure subtitle has a real newline (from v27)
UPDATE CmsItem SET TextValue = 'Discover 7 gourmet restaurants offering exclusive festival
menus crafted by top local chefs.'
WHERE CmsSectionId = @heroSectionId AND ItemKey = 'hero_subtitle';

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @heroSectionId, 'hero_button_primary', 'BUTTON_TEXT', 'Discover restaurants', NULL, NULL, NOW()
FROM DUAL WHERE @heroSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @heroSectionId AND ItemKey = 'hero_button_primary');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @heroSectionId, 'hero_button_primary_link', 'LINK', '#restaurants', NULL, NULL, NOW()
FROM DUAL WHERE @heroSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @heroSectionId AND ItemKey = 'hero_button_primary_link');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @heroSectionId, 'hero_button_secondary', 'BUTTON_TEXT', 'About Yummy', NULL, NULL, NOW()
FROM DUAL WHERE @heroSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @heroSectionId AND ItemKey = 'hero_button_secondary');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @heroSectionId, 'hero_button_secondary_link', 'LINK', '#about', NULL, NULL, NOW()
FROM DUAL WHERE @heroSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @heroSectionId AND ItemKey = 'hero_button_secondary_link');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @heroSectionId, 'hero_background_image', 'IMAGE_PATH', '/assets/Image/restaurants/hero-picture.png', NULL, NULL, NOW()
FROM DUAL WHERE @heroSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @heroSectionId AND ItemKey = 'hero_background_image');

-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
-- PART 3: Gradient Section Items
-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gradientSectionId, 'gradient_heading', 'HEADING', 'Good food tastes better when shared.', NULL, NULL, NOW()
FROM DUAL WHERE @gradientSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @gradientSectionId AND ItemKey = 'gradient_heading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gradientSectionId, 'gradient_subheading', 'TEXT', 'Food, stories, and shared moments across Haarlem.', NULL, NULL, NOW()
FROM DUAL WHERE @gradientSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @gradientSectionId AND ItemKey = 'gradient_subheading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @gradientSectionId, 'gradient_background_image', 'IMAGE_PATH', '/assets/Image/restaurants/chef-preparing-food.png', NULL, NULL, NOW()
FROM DUAL WHERE @gradientSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @gradientSectionId AND ItemKey = 'gradient_background_image');

-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
-- PART 4: Intro Split Section (with subsections, from v27 fix)
-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSplitSectionId, 'intro_heading', 'HEADING', 'Yummy! at the Heart of the Festival', NULL, NULL, NOW()
FROM DUAL WHERE @introSplitSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_heading');

-- Body: only the intro paragraph (subsections are separate keys)
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSplitSectionId, 'intro_body', 'TEXT', 'Welcome to Yummy!, the food experience of the Haarlem Festival.
Four days where some of the city''s favorite restaurants open their doors with special menus made just for this event.', NULL, NULL, NOW()
FROM DUAL WHERE @introSplitSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_body');

-- Ensure body text is the clean version (overwrite if exists from old migration)
UPDATE CmsItem SET TextValue = 'Welcome to Yummy!, the food experience of the Haarlem Festival.
Four days where some of the city''s favorite restaurants open their doors with special menus made just for this event.'
WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_body';

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @introSplitSectionId, 'intro_sub1_heading', 'TEXT', 'What is Yummy?' FROM DUAL WHERE @introSplitSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_sub1_heading');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @introSplitSectionId, 'intro_sub1_text', 'TEXT', 'A festival of food where each restaurant offers one unique menu, set time slots, and special prices.' FROM DUAL WHERE @introSplitSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_sub1_text');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @introSplitSectionId, 'intro_sub2_heading', 'TEXT', 'Who takes part?' FROM DUAL WHERE @introSplitSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_sub2_heading');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @introSplitSectionId, 'intro_sub2_text', 'TEXT', 'Local chefs and restaurants from all around Haarlem, prepare with their own style a great variety of dishes, such as: Dutch-French-European-Fish & Seafood-Modern Vegan.' FROM DUAL WHERE @introSplitSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_sub2_text');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @introSplitSectionId, 'intro_sub3_heading', 'TEXT', 'How does it work?' FROM DUAL WHERE @introSplitSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_sub3_heading');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @introSplitSectionId, 'intro_sub3_text', 'TEXT', 'Choose a restaurant, pick a time slot, and make a reservation. Seats are limited, so booking ahead is important.' FROM DUAL WHERE @introSplitSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_sub3_text');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @introSplitSectionId, 'intro_closing', 'TEXT', 'Come enjoy great food, good company, and a warm festival atmosphere.' FROM DUAL WHERE @introSplitSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_closing');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT @introSplitSectionId, 'intro_image', 'IMAGE_PATH', '/assets/Image/restaurants/table-with-food-and-drink.png' FROM DUAL
WHERE @introSplitSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_image');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT @introSplitSectionId, 'intro_image_alt', 'TEXT', 'Yummy! at the Heart of the Festival' FROM DUAL
WHERE @introSplitSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_image_alt');

-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
-- PART 5: Intro Split 2 (4th section - "When Haarlem Becomes a Dining Room")
-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT @introSplit2SectionId, 'intro2_heading', 'HEADING', 'When Haarlem Becomes a Dining Room' FROM DUAL
WHERE @introSplit2SectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplit2SectionId AND ItemKey = 'intro2_heading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT @introSplit2SectionId, 'intro2_body', 'TEXT', 'As the sun sets over Haarlem''s historic streets, the city slowly turns into one big dining room.

From Thursday to Sunday, each restaurant offers 2 to 3 sessions later afternoon, starting from 16:30 and lasting around 1.5 to 2 hours..

Just enough time to enjoy your plate, share a toast, and wander to the next event or performance nearby.' FROM DUAL
WHERE @introSplit2SectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplit2SectionId AND ItemKey = 'intro2_body');

-- Ensure body has real newlines
UPDATE CmsItem SET TextValue = 'As the sun sets over Haarlem''s historic streets, the city slowly turns into one big dining room.

From Thursday to Sunday, each restaurant offers 2 to 3 sessions later afternoon, starting from 16:30 and lasting around 1.5 to 2 hours..

Just enough time to enjoy your plate, share a toast, and wander to the next event or performance nearby.'
WHERE CmsSectionId = @introSplit2SectionId AND ItemKey = 'intro2_body';

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT @introSplit2SectionId, 'intro2_image', 'IMAGE_PATH', '/assets/Image/restaurants/food-in-canal.png' FROM DUAL
WHERE @introSplit2SectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplit2SectionId AND ItemKey = 'intro2_image');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
SELECT @introSplit2SectionId, 'intro2_image_alt', 'TEXT', 'When Haarlem Becomes a Dining Room' FROM DUAL
WHERE @introSplit2SectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplit2SectionId AND ItemKey = 'intro2_image_alt');

-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
-- PART 6: Instructions Section ("How reservations work")
-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @instructionsSectionId, 'instructions_title', 'HEADING', 'How reservations work' FROM DUAL WHERE @instructionsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_title');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @instructionsSectionId, 'instructions_card_1_title', 'HEADING', 'Browse' FROM DUAL WHERE @instructionsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_card_1_title');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @instructionsSectionId, 'instructions_card_1_text', 'TEXT', 'Explore participating restaurants and their exclusive festival menus.' FROM DUAL WHERE @instructionsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_card_1_text');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @instructionsSectionId, 'instructions_card_2_title', 'HEADING', 'Choose' FROM DUAL WHERE @instructionsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_card_2_title');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @instructionsSectionId, 'instructions_card_2_text', 'TEXT', 'Pick a date and time slot that fits your schedule.' FROM DUAL WHERE @instructionsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_card_2_text');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @instructionsSectionId, 'instructions_card_3_title', 'HEADING', 'Reserve' FROM DUAL WHERE @instructionsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_card_3_title');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @instructionsSectionId, 'instructions_card_3_text', 'TEXT', 'Complete your booking and receive a confirmation. Done!' FROM DUAL WHERE @instructionsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_card_3_text');

-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
-- PART 7: Restaurant Cards Section (title + subtitle)
-- Note: Card data now comes from Restaurant domain table.
-- These are just the section header fields.
-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @cardsSectionId, 'cards_title', 'HEADING', 'Explore the participant restaurants' FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'cards_title');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @cardsSectionId, 'cards_subtitle', 'TEXT', 'Discover all restaurants participating in Yummy! Each one offers a special festival menu, unique flavors, and limited time slots throughout the weekend.' FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'cards_subtitle');

-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
-- PART 8: Detail Page CMS Labels (from v31)
-- Admin-editable section titles, labels, button texts
-- shared across ALL restaurant detail pages.
-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

-- Hero
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_hero_subtitle_template', 'TEXT', '{cuisine}\nRelax, explore, and let {name} make your evening something truly special.' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_hero_subtitle_template');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_hero_btn_primary', 'BUTTON_TEXT', 'Make a reservation' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_hero_btn_primary');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_hero_btn_secondary', 'BUTTON_TEXT', 'Back to restaurant' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_hero_btn_secondary');

-- Contact card
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_contact_title', 'HEADING', 'Contact' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_contact_title');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_label_address', 'TEXT', 'ADDRESS' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_label_address');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_label_contact', 'TEXT', 'CONTACT' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_label_contact');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_label_open_hours', 'TEXT', 'OPEN HOURS FOR YUMMY' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_label_open_hours');

-- Practical info card
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_practical_title', 'HEADING', 'Practical Info' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_practical_title');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_label_price_food', 'TEXT', 'PRICE AND FOOD' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_label_price_food');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_label_rating', 'TEXT', 'RESTAURANT RATING' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_label_rating');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_label_special_requests', 'TEXT', 'SPECIAL REQUESTS' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_label_special_requests');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_label_festival_rated', 'TEXT', 'Festival-rated' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_label_festival_rated');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_label_michelin', 'TEXT', 'Michelin-star' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_label_michelin');

-- Section titles
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_gallery_title', 'HEADING', 'Restaurant Gallery' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_gallery_title');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_about_title_prefix', 'HEADING', 'About' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_about_title_prefix');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_chef_title', 'HEADING', 'Chef & Philosophy' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_chef_title');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_menu_title', 'HEADING', 'Menu Style' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_menu_title');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_menu_cuisine_label', 'TEXT', 'Cuisine type:' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_menu_cuisine_label');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_location_title', 'HEADING', 'Location' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_location_title');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_location_address_label', 'TEXT', 'Address' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_location_address_label');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_map_fallback_text', 'TEXT', 'Map coming soon' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_map_fallback_text');

-- Reservation section
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_reservation_title', 'HEADING', 'Make your Reservation' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_reservation_title');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_reservation_description', 'TEXT', 'Choose a time slot that suits your evening. When you''re ready to book, continue to the next screen to confirm your guests and add any special requests.' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_reservation_description');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_reservation_slots_label', 'TEXT', 'AVAILABLE TIME SLOTS' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_reservation_slots_label');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_reservation_note', 'TEXT', 'To make your reservation, please continue to the next screen.' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_reservation_note');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_reservation_btn', 'BUTTON_TEXT', 'Continue to Reservation' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_reservation_btn');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_label_duration', 'TEXT', 'Duration' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_label_duration');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue) SELECT @detailSectionId, 'detail_label_seats', 'TEXT', 'Seats' FROM DUAL WHERE @detailSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @detailSectionId AND ItemKey = 'detail_label_seats');

COMMIT;

SELECT 'ALL DONE: Restaurant migration complete — domain + CMS!' AS Status;
