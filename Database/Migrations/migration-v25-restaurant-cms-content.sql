-- =====================================================
-- Migration v25: Restaurant Page CMS Content
-- Run after migration-v24
-- Safe & Idempotent: Uses WHERE NOT EXISTS checks
-- Follows Jazz/Storytelling migration pattern
-- Date: February 14, 2026
-- =====================================================

START TRANSACTION;

-- =====================================================
-- PART 1: Ensure Restaurant page exists
-- =====================================================

INSERT INTO CmsPage (Slug, Title)
SELECT 'restaurant', 'Yummy! Restaurant Experience'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM CmsPage WHERE Slug = 'restaurant');

SET @restaurantPageId := (SELECT CmsPageId FROM CmsPage WHERE Slug = 'restaurant' LIMIT 1);

-- =====================================================
-- PART 2: Ensure all 6 sections exist
-- =====================================================

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @restaurantPageId, 'hero_section'
FROM DUAL WHERE @restaurantPageId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'hero_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @restaurantPageId, 'gradient_section'
FROM DUAL WHERE @restaurantPageId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'gradient_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @restaurantPageId, 'intro_split_section'
FROM DUAL WHERE @restaurantPageId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'intro_split_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @restaurantPageId, 'intro_split2_section'
FROM DUAL WHERE @restaurantPageId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'intro_split2_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @restaurantPageId, 'instructions_section'
FROM DUAL WHERE @restaurantPageId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'instructions_section');

INSERT INTO CmsSection (CmsPageId, SectionKey)
SELECT @restaurantPageId, 'restaurant_cards_section'
FROM DUAL WHERE @restaurantPageId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'restaurant_cards_section');

-- Get section IDs
SET @heroSectionId := (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'hero_section' LIMIT 1);
SET @gradientSectionId := (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'gradient_section' LIMIT 1);
SET @introSplitSectionId := (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'intro_split_section' LIMIT 1);
SET @introSplit2SectionId := (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'intro_split2_section' LIMIT 1);
SET @instructionsSectionId := (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'instructions_section' LIMIT 1);
SET @cardsSectionId := (SELECT CmsSectionId FROM CmsSection WHERE CmsPageId = @restaurantPageId AND SectionKey = 'restaurant_cards_section' LIMIT 1);

-- =====================================================
-- PART 3: Insert Hero Section Items
-- =====================================================

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @heroSectionId, 'hero_main_title', 'HEADING', 'Yummy Gourmet with a Twist', NULL, NULL, NOW()
FROM DUAL WHERE @heroSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @heroSectionId AND ItemKey = 'hero_main_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @heroSectionId, 'hero_subtitle', 'TEXT', 'Discover 7 gourmet restaurants offering exclusive festival\nmenus crafted by top local chefs.', NULL, NULL, NOW()
FROM DUAL WHERE @heroSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @heroSectionId AND ItemKey = 'hero_subtitle');

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

-- =====================================================
-- PART 4: Insert Gradient Section Items
-- =====================================================

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

-- =====================================================
-- PART 5: Insert Intro Split Section Items
-- =====================================================

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSplitSectionId, 'intro_heading', 'HEADING', 'Yummy! at the Heart of the Festival', NULL, NULL, NOW()
FROM DUAL WHERE @introSplitSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_heading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSplitSectionId, 'intro_body', 'TEXT', 'Welcome to Yummy!, the food experience of the Haarlem Festival.\nFour days where some of the city''s favorite restaurants open their doors with special menus made just for this event.\n\n## What is Yummy?\nA festival of food where each restaurant offers one unique menu, set time slots, and special prices.\n\n## Who takes part?\nLocal chefs and restaurants from all around Haarlem, prepare with their own style a great variety of dishes, such as: Dutch-French-European-Fish & Seafood-Modern Vegan.\n\n## How does it work?\nChoose a restaurant, pick a time slot, and make a reservation. Seats are limited, so booking ahead is important.\n\nCome enjoy great food, good company, and a warm festival atmosphere.', NULL, NULL, NOW()
FROM DUAL WHERE @introSplitSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_body');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSplitSectionId, 'intro_image', 'IMAGE_PATH', '/assets/Image/restaurants/table-with-food-and-drink.png', NULL, NULL, NOW()
FROM DUAL WHERE @introSplitSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_image');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSplitSectionId, 'intro_image_alt', 'TEXT', 'Yummy! at the Heart of the Festival', NULL, NULL, NOW()
FROM DUAL WHERE @introSplitSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplitSectionId AND ItemKey = 'intro_image_alt');

-- =====================================================
-- PART 6: Insert Intro Split 2 Section Items (4th section)
-- =====================================================

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSplit2SectionId, 'intro2_heading', 'HEADING', 'When Haarlem Becomes a Dining Room', NULL, NULL, NOW()
FROM DUAL WHERE @introSplit2SectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplit2SectionId AND ItemKey = 'intro2_heading');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSplit2SectionId, 'intro2_body', 'TEXT', 'As the sun sets over Haarlem''s historic streets, the city slowly turns into one big dining room.\n\nFrom Thursday to Sunday, each restaurant offers 2 to 3 sessions later afternoon, starting from 16:30 and lasting around 1.5 to 2 hours..\n\nJust enough time to enjoy your plate, share a toast, and wander to the next event or performance nearby.', NULL, NULL, NOW()
FROM DUAL WHERE @introSplit2SectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplit2SectionId AND ItemKey = 'intro2_body');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSplit2SectionId, 'intro2_image', 'IMAGE_PATH', '/assets/Image/restaurants/food-in-canal.png', NULL, NULL, NOW()
FROM DUAL WHERE @introSplit2SectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplit2SectionId AND ItemKey = 'intro2_image');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @introSplit2SectionId, 'intro2_image_alt', 'TEXT', 'When Haarlem Becomes a Dining Room', NULL, NULL, NOW()
FROM DUAL WHERE @introSplit2SectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @introSplit2SectionId AND ItemKey = 'intro2_image_alt');

-- =====================================================
-- PART 7: Insert Instructions Section Items (5th section)
-- =====================================================

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @instructionsSectionId, 'instructions_title', 'HEADING', 'How reservations work', NULL, NULL, NOW()
FROM DUAL WHERE @instructionsSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @instructionsSectionId, 'instructions_card_1_title', 'HEADING', 'Browse', NULL, NULL, NOW()
FROM DUAL WHERE @instructionsSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_card_1_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @instructionsSectionId, 'instructions_card_1_text', 'TEXT', 'Explore participating restaurants and their exclusive festival menus.', NULL, NULL, NOW()
FROM DUAL WHERE @instructionsSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_card_1_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @instructionsSectionId, 'instructions_card_2_title', 'HEADING', 'Choose', NULL, NULL, NOW()
FROM DUAL WHERE @instructionsSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_card_2_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @instructionsSectionId, 'instructions_card_2_text', 'TEXT', 'Pick a date and time slot that fits your schedule.', NULL, NULL, NOW()
FROM DUAL WHERE @instructionsSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_card_2_text');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @instructionsSectionId, 'instructions_card_3_title', 'HEADING', 'Reserve', NULL, NULL, NOW()
FROM DUAL WHERE @instructionsSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_card_3_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @instructionsSectionId, 'instructions_card_3_text', 'TEXT', 'Complete your booking and receive a confirmation. Done!', NULL, NULL, NOW()
FROM DUAL WHERE @instructionsSectionId IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @instructionsSectionId AND ItemKey = 'instructions_card_3_text');

-- =====================================================
-- PART 8: Insert Restaurant Cards Section Items (6th section)
-- =====================================================

-- Section header and filters
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @cardsSectionId, 'cards_title', 'HEADING', 'Explore the participant restaurants', NULL, NULL, NOW()
FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'cards_title');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc)
SELECT @cardsSectionId, 'cards_subtitle', 'TEXT', 'Discover all restaurants participating in Yummy! Each one offers a special festival menu, unique flavors, and limited time slots throughout the weekend.', NULL, NULL, NOW()
FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'cards_subtitle');

INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'filter_all', 'BUTTON_TEXT', 'All', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'filter_all');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'filter_dutch', 'BUTTON_TEXT', 'Dutch', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'filter_dutch');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'filter_european', 'BUTTON_TEXT', 'European', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'filter_european');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'filter_french', 'BUTTON_TEXT', 'French', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'filter_french');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'filter_modern', 'BUTTON_TEXT', 'Modern', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'filter_modern');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'filter_fish_seafood', 'BUTTON_TEXT', 'Fish & Seafood', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'filter_fish_seafood');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'filter_vegetarian', 'BUTTON_TEXT', 'Vegetarian', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'filter_vegetarian');

-- Restaurant Card 1: Ratatouille
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_1_name', 'HEADING', 'Ratatouille', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_1_name');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_1_cuisine', 'TEXT', 'French, fish and seafood, European', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_1_cuisine');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_1_address', 'TEXT', 'Spaarne 96, 2011 CL Haarlem', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_1_address');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_1_description', 'TEXT', 'Refined dining with a warm touch, where seasonal ingredients and creative flavors come together for an elegant experience.', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_1_description');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_1_distance_text', 'TEXT', '5 min walk from Patronaat', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_1_distance_text');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_1_rating', 'TEXT', '4', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_1_rating');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_1_price', 'TEXT', '€€€', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_1_price');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_1_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-Ratatouille-card.png', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_1_image');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_1_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_1_about_label');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_1_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_1_book_label');

-- Restaurant Card 2: Urban Frenchy Bistro Toujours
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_2_name', 'HEADING', 'Urban Frenchy Bistro Toujours', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_2_name');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_2_cuisine', 'TEXT', 'Dutch, fish and seafood, European', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_2_cuisine');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_2_address', 'TEXT', 'Oude Groenmarkt 10-12, 2011 HL, Haarlem', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_2_address');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_2_description', 'TEXT', 'A cozy city bistro focused on seafood and comforting dishes in a lively central setting.', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_2_description');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_2_distance_text', 'TEXT', '2 min walk from Jopenkerk', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_2_distance_text');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_2_rating', 'TEXT', '3', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_2_rating');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_2_price', 'TEXT', '€€', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_2_price');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_2_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-UrbanFrenchy.png', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_2_image');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_2_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_2_about_label');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_2_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_2_book_label');

-- Restaurant Card 3: New Vegas
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_3_name', 'HEADING', 'New Vegas', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_3_name');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_3_cuisine', 'TEXT', 'Vegan', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_3_cuisine');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_3_address', 'TEXT', 'Koningstraat 5, 2011 TB Haarlem', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_3_address');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_3_description', 'TEXT', 'A casual spot with an international feel, offering familiar dishes and vegetarian options right in the city center.', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_3_description');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_3_distance_text', 'TEXT', '5 min walk from Patronaat', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_3_distance_text');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_3_rating', 'TEXT', '3', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_3_rating');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_3_price', 'TEXT', '€€', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_3_price');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_3_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-NewVegas-card.jpg', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_3_image');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_3_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_3_about_label');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_3_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_3_book_label');

-- Restaurant Card 4: Grand Cafe Brinkman
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_4_name', 'HEADING', 'Grand Cafe Brinkman', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_4_name');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_4_cuisine', 'TEXT', 'Dutch, European, Modern', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_4_cuisine');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_4_address', 'TEXT', 'Grote Markt 13, 2011 RC, Haarlem', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_4_address');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_4_description', 'TEXT', 'A classic grand cafe on Haarlem''s main square, serving familiar European dishes in the heart of the festival buzz.', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_4_description');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_4_distance_text', 'TEXT', 'Located directly on Grote Markt', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_4_distance_text');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_4_rating', 'TEXT', '3', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_4_rating');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_4_price', 'TEXT', '€€', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_4_price');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_4_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-CafeDeBrinkman-card.png', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_4_image');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_4_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_4_about_label');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_4_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_4_book_label');

-- Restaurant Card 5: Restaurant ML
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_5_name', 'HEADING', 'Restaurant ML', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_5_name');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_5_cuisine', 'TEXT', 'Dutch, fish and seafood, European', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_5_cuisine');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_5_address', 'TEXT', 'Kleine Houtstraat 70, 2011 DR Haarlem', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_5_address');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_5_description', 'TEXT', 'A modern fine-dining restaurant known for a refined yet welcoming atmosphere.', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_5_description');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_5_distance_text', 'TEXT', '12 min walk from Slachthuis', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_5_distance_text');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_5_rating', 'TEXT', '4', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_5_rating');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_5_price', 'TEXT', '€€€', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_5_price');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_5_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-ML-card.png', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_5_image');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_5_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_5_about_label');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_5_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_5_book_label');

-- Restaurant Card 6: Cafe de Roemer
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_6_name', 'HEADING', 'Cafe de Roemer', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_6_name');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_6_cuisine', 'TEXT', 'Dutch, fish and seafood, European', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_6_cuisine');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_6_address', 'TEXT', 'Botermarkt 17, 2011 XL Haarlem', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_6_address');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_6_description', 'TEXT', 'A cozy neighborhood cafe serving honest food and classic flavors in a relaxed and friendly setting.', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_6_description');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_6_distance_text', 'TEXT', '7 min walk from Puncher Comedy Club', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_6_distance_text');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_6_rating', 'TEXT', '4', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_6_rating');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_6_price', 'TEXT', '€€€', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_6_price');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_6_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-deRoemer-card.png', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_6_image');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_6_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_6_about_label');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_6_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_6_book_label');

-- Restaurant Card 7: Restaurant Fris
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_7_name', 'HEADING', 'Restaurant Fris', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_7_name');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_7_cuisine', 'TEXT', 'Dutch, French, European', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_7_cuisine');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_7_address', 'TEXT', 'Twijnderslaan 7, 2012 BG, Haarlem', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_7_address');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_7_description', 'TEXT', 'A contemporary restaurant focused on seasonal ingredients, thoughtful cooking, and elegant flavors without the formality.', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_7_description');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_7_distance_text', 'TEXT', '10 min walk from Patronaat', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_7_distance_text');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_7_rating', 'TEXT', '4', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_7_rating');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_7_price', 'TEXT', '€€€', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_7_price');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_7_image', 'IMAGE_PATH', '/assets/Image/restaurants/Restaurant-Fris-card.png', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_7_image');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_7_about_label', 'BUTTON_TEXT', 'About it', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_7_about_label');
INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue, HtmlValue, MediaAssetId, UpdatedAtUtc) SELECT @cardsSectionId, 'restaurant_7_book_label', 'BUTTON_TEXT', 'Book table', NULL, NULL, NOW() FROM DUAL WHERE @cardsSectionId IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CmsItem WHERE CmsSectionId = @cardsSectionId AND ItemKey = 'restaurant_7_book_label');

-- =====================================================
-- PART 9: COMMIT & SUCCESS
-- =====================================================

COMMIT;

SELECT 'Restaurant CMS content successfully inserted!' AS Status;
