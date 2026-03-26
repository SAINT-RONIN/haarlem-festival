-- Migration v32: Create RestaurantImage table
-- Stores per-restaurant images with a type label (about, chef, menu, gallery, reservation).
-- Follows the same pattern as ArtistGalleryImage and EventGalleryImage.
-- ImagePath stores the file path directly (e.g. /assets/Image/about.jpg).

DROP TABLE IF EXISTS RestaurantImage;

CREATE TABLE RestaurantImage (
    RestaurantImageId INT          NOT NULL AUTO_INCREMENT,
    RestaurantId      INT          NOT NULL,
    ImagePath         VARCHAR(500) NOT NULL,
    ImageType         VARCHAR(50)  NOT NULL,
    SortOrder         INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (RestaurantImageId),
    CONSTRAINT FK_RestaurantImage_Restaurant
        FOREIGN KEY (RestaurantId) REFERENCES Restaurant(RestaurantId)
        ON DELETE CASCADE
);

-- Ratatouille (RestaurantId = 1)
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder) VALUES
(1, '/assets/Image/restaurants/ratatouille-about.png',       'about',       1),
(1, '/assets/Image/restaurants/ratatouille-chef.png',        'chef',        1),
(1, '/assets/Image/restaurants/ratatouille-menu-1.png',      'menu',        1),
(1, '/assets/Image/restaurants/ratatouille-menu-2.png',      'menu',        2),
(1, '/assets/Image/restaurants/ratatouille-gallery-1.png',   'gallery',     1),
(1, '/assets/Image/restaurants/ratatouille-gallery-2.png',   'gallery',     2),
(1, '/assets/Image/restaurants/ratatouille-gallery-3.png',   'gallery',     3),
(1, '/assets/Image/restaurants/ratatouille-reservation.jpg', 'reservation', 1);

-- Urban Frenchy Bistro Toujours (RestaurantId = 2)
INSERT INTO RestaurantImage (RestaurantId, ImagePath, ImageType, SortOrder) VALUES
(2, '/assets/Image/restaurants/toujours-about.png',     'about',   1),
(2, '/assets/Image/restaurants/toujours-chef.jpg',      'chef',    1),
(2, '/assets/Image/restaurants/toujours-menu-1.png',    'menu',    1),
(2, '/assets/Image/restaurants/toujours-menu-2.png',    'menu',    2),
(2, '/assets/Image/restaurants/toujours-gallery-1.png', 'gallery', 1),
(2, '/assets/Image/restaurants/toujours-gallery-2.png', 'gallery', 2),
(2, '/assets/Image/restaurants/toujours-gallery-3.png', 'gallery', 3);