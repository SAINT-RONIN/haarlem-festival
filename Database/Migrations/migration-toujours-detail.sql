-- =====================================================
-- Urban Frenchy Bistro Toujours — Detail Page Data
-- Run this on your CURRENT DB to seed Toujours content.
-- (Already included in migration-restaurant-complete.sql
--  for classmates with fresh DBs.)
-- =====================================================

START TRANSACTION;

-- 1) Seed domain data into Restaurant table
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

-- 2) Register detail images in MediaAsset
INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-gallery-1.png', 'toujours-gallery-1.png', 'image/png', 0, 'Toujours Gallery 1'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-gallery-1.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-gallery-2.png', 'toujours-gallery-2.png', 'image/png', 0, 'Toujours Gallery 2'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-gallery-2.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-gallery-3.png', 'toujours-gallery-3.png', 'image/png', 0, 'Toujours Gallery 3'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-gallery-3.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-about.png', 'toujours-about.png', 'image/png', 0, 'About Toujours'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-about.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-chef.jpg', 'toujours-chef.jpg', 'image/jpeg', 0, 'Chef Toujours'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-chef.jpg');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-menu-1.png', 'toujours-menu-1.png', 'image/png', 0, 'Toujours Menu 1'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-menu-1.png');

INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
SELECT '/assets/Image/restaurants/toujours-menu-2.png', 'toujours-menu-2.png', 'image/png', 0, 'Toujours Menu 2'
WHERE NOT EXISTS (SELECT 1 FROM MediaAsset WHERE FilePath = '/assets/Image/restaurants/toujours-menu-2.png');

-- toujours-reservation: reuses ratatouille-reservation.jpg (same image)

-- 3) Link images to Restaurant
UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-gallery-1.png'
SET r.GalleryImage1AssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-gallery-2.png'
SET r.GalleryImage2AssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-gallery-3.png'
SET r.GalleryImage3AssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-about.png'
SET r.AboutImageAssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-chef.jpg'
SET r.ChefImageAssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-menu-1.png'
SET r.MenuImage1AssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/toujours-menu-2.png'
SET r.MenuImage2AssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';

UPDATE Restaurant r JOIN MediaAsset ma ON ma.FilePath = '/assets/Image/restaurants/ratatouille-reservation.jpg'
SET r.ReservationImageAssetId = ma.MediaAssetId WHERE r.Name = 'Urban Frenchy Bistro Toujours';

COMMIT;

SELECT 'Toujours detail data seeded!' AS Status;
