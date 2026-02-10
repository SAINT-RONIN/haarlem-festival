-- Migration v20: Add schedule day visibility management
-- Allows hiding entire days (columns) per event type or globally

-- Create table for managing visible schedule days
CREATE TABLE IF NOT EXISTS ScheduleDayConfig (
    ScheduleDayConfigId INT AUTO_INCREMENT PRIMARY KEY,
    EventTypeId INT NULL,  -- NULL = applies to all event types
    DayOfWeek TINYINT NOT NULL,  -- 0=Sunday, 1=Monday, ..., 6=Saturday
    IsVisible TINYINT(1) NOT NULL DEFAULT 1,
    UpdatedAtUtc DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_eventtype_day (EventTypeId, DayOfWeek),
    FOREIGN KEY (EventTypeId) REFERENCES EventType(EventTypeId) ON DELETE CASCADE
);

-- Insert default visibility for all days (all visible by default)
-- Global settings (EventTypeId = NULL)
INSERT INTO ScheduleDayConfig (EventTypeId, DayOfWeek, IsVisible) VALUES
(NULL, 0, 1),  -- Sunday
(NULL, 1, 1),  -- Monday
(NULL, 2, 1),  -- Tuesday
(NULL, 3, 1),  -- Wednesday
(NULL, 4, 1),  -- Thursday
(NULL, 5, 1),  -- Friday
(NULL, 6, 1); -- Saturday

-- ROLLBACK:
-- DROP TABLE IF EXISTS ScheduleDayConfig;

