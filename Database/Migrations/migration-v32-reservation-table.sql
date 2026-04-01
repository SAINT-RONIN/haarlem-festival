-- migration-v32-reservation-table.sql
-- Adds a TimeSlots column to Restaurant (comma-separated time strings, e.g. "16:30, 18:30, 20:30")
-- and creates the Reservation table to store form submissions from the reservation page.

-- Add TimeSlots to Restaurant so each restaurant can store its own available times.
ALTER TABLE Restaurant
    ADD COLUMN IF NOT EXISTS TimeSlots TEXT NULL AFTER SpecialRequestsNote;

-- Seed example time slots for existing active restaurants that don't have them yet.
UPDATE Restaurant SET TimeSlots = '16:30, 18:30, 20:30' WHERE IsActive = 1 AND (TimeSlots IS NULL OR TimeSlots = '');

-- Table that stores every reservation submitted through the reservation form.
CREATE TABLE IF NOT EXISTS `Reservation` (
    `ReservationId`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `RestaurantId`    INT NOT NULL,
    `DiningDate`      VARCHAR(20)    NOT NULL COMMENT 'Thursday / Friday / Saturday / Sunday',
    `TimeSlot`        VARCHAR(20)    NOT NULL COMMENT 'e.g. 16:30',
    `AdultsCount`     INT UNSIGNED   NOT NULL DEFAULT 0,
    `ChildrenCount`   INT UNSIGNED   NOT NULL DEFAULT 0,
    `SpecialRequests` TEXT           NULL,
    `TotalFee`        DECIMAL(10,2)  NOT NULL DEFAULT 0.00 COMMENT '€10 per person reservation deposit',
    `CreatedAt`       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ReservationId`),
    INDEX `idx_reservation_restaurant` (`RestaurantId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;