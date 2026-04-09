-- Migration: Restore restaurant detail CMS content for Ratatouille and Toujours
--
-- Context: MediaAsset records for gallery, about, chef, menu, and reservation images
-- were seeded for Ratatouille (section 94, event 48) and Urban Frenchy Bistro Toujours
-- (section 99, event 53) on the restaurant-detail page (page 14), but the CmsItem rows
-- linking those assets to their sections were missing/deleted.
--
-- CmsSectionId mapping (page 14 = restaurant-detail):
--   94 = event_48 = Ratatouille - Festival Dinner
--   99 = event_53 = Urban Frenchy Bistro Toujours - Festival Dinner
--
-- MediaAsset IDs used:
--   82  ratatouille-gallery-1.png
--   83  ratatouille-gallery-2.png
--   84  ratatouille-gallery-3.png
--   85  ratatouille-about.png        (MEDIA)
--   86  ratatouille-chef.png
--   87  ratatouille-menu-1.png
--   88  ratatouille-menu-2.png
--   89  ratatouille-reservation.jpg
--   90  toujours-gallery-1.png
--   91  toujours-gallery-2.png
--   92  toujours-gallery-3.png
--   93  toujours-about.png           (MEDIA)
--   95  toujours-menu-1.png
--   96  toujours-menu-2.png
--   97  toujours-reservation.png
--   98  toujours-chef.jpg

-- ─── Ratatouille (CmsSectionId = 94) ────────────────────────────────────────

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES

-- About section
(94, 'about_image',   'MEDIA',      NULL,           NULL,                                                                                                              85,   NOW()),
(94, 'about_text',    'HTML',       NULL,           '<p>Ratatouille is one of Haarlem\'s most celebrated French restaurants, offering an exquisite blend of French technique and locally sourced North Sea seafood. With four Michelin stars and a seasonal menu crafted by Chef Jozua Jaring, every visit is a culinary journey through the finest flavours of French and European cuisine.</p>', NULL, NOW()),

-- Chef section
(94, 'chef_name',     'TEXT',       'Jozua Jaring', NULL,                                                                                                              NULL, NOW()),
(94, 'chef_image',    'IMAGE_PATH', '/assets/Image/restaurants/ratatouille-chef.png', NULL,                                                                            NULL, NOW()),
(94, 'chef_text',     'HTML',       NULL,           '<p>Chef Jozua Jaring brings decades of fine-dining experience to Ratatouille\'s kitchen. Trained in classical French cooking, he champions seasonal ingredients and elegant presentation, earning the restaurant its outstanding reputation in Haarlem\'s culinary scene.</p>', NULL, NOW()),

-- Gallery section
(94, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/restaurants/ratatouille-gallery-1.png', NULL, NULL, NOW()),
(94, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/restaurants/ratatouille-gallery-2.png', NULL, NULL, NOW()),
(94, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/restaurants/ratatouille-gallery-3.png', NULL, NULL, NOW()),

-- Menu section
(94, 'menu_description', 'HTML',    NULL,           '<p>The festival menu at Ratatouille celebrates the finest French and European flavours. Each course highlights fresh North Sea seafood and seasonal produce, presented with the elegant simplicity that defines classical French cuisine.</p>', NULL, NOW()),
(94, 'menu_image_1',  'IMAGE_PATH', '/assets/Image/restaurants/ratatouille-menu-1.png', NULL,                                                                          NULL, NOW()),
(94, 'menu_image_2',  'IMAGE_PATH', '/assets/Image/restaurants/ratatouille-menu-2.png', NULL,                                                                          NULL, NOW()),

-- Reservation section
(94, 'reservation_image', 'IMAGE_PATH', '/assets/Image/restaurants/ratatouille-reservation.jpg', NULL,                                                                 NULL, NOW());

-- ─── Urban Frenchy Bistro Toujours (CmsSectionId = 99) ──────────────────────

INSERT INTO `CmsItem` (`CmsSectionId`, `ItemKey`, `ItemType`, `TextValue`, `HtmlValue`, `MediaAssetId`, `UpdatedAtUtc`) VALUES

-- About section
(99, 'about_image',   'MEDIA',      NULL,           NULL,                                                                                                              93,   NOW()),
(99, 'about_text',    'HTML',       NULL,           '<p>Urban Frenchy Bistro Toujours brings a warm and lively atmosphere to Haarlem\'s historic Oude Groenmarkt. Drawing on Dutch coastal traditions and French bistro culture, the kitchen serves fresh fish and seafood with a modern European twist, making every meal a relaxed yet memorable experience.</p>', NULL, NOW()),

-- Chef section
(99, 'chef_image',    'IMAGE_PATH', '/assets/Image/restaurants/toujours-chef.jpg', NULL,                                                                               NULL, NOW()),
(99, 'chef_text',     'HTML',       NULL,           '<p>The culinary team at Toujours keeps it simple and honest: the best ingredients, minimal fuss, maximum flavour. The kitchen draws inspiration from French bistro classics and the freshest Dutch seafood the North Sea has to offer.</p>', NULL, NOW()),

-- Gallery section
(99, 'gallery_image_1', 'IMAGE_PATH', '/assets/Image/restaurants/toujours-gallery-1.png', NULL, NULL, NOW()),
(99, 'gallery_image_2', 'IMAGE_PATH', '/assets/Image/restaurants/toujours-gallery-2.png', NULL, NULL, NOW()),
(99, 'gallery_image_3', 'IMAGE_PATH', '/assets/Image/restaurants/toujours-gallery-3.png', NULL, NULL, NOW()),

-- Menu section
(99, 'menu_description', 'HTML',    NULL,           '<p>Toujours presents a festival menu rooted in Dutch and French bistro tradition. Expect hearty portions of seasonal fish, classic seafood preparations, and European-inspired dishes — all crafted to be shared and enjoyed in good company.</p>', NULL, NOW()),
(99, 'menu_image_1',  'IMAGE_PATH', '/assets/Image/restaurants/toujours-menu-1.png', NULL,                                                                             NULL, NOW()),
(99, 'menu_image_2',  'IMAGE_PATH', '/assets/Image/restaurants/toujours-menu-2.png', NULL,                                                                             NULL, NOW()),

-- Reservation section
(99, 'reservation_image', 'IMAGE_PATH', '/assets/Image/restaurants/toujours-reservation.png', NULL,                                                                    NULL, NOW());
