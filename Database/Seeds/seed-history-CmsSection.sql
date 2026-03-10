-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Feb 16, 2026 at 02:14 PM
-- Server version: 12.0.2-MariaDB-ubu2404
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- Insert CmsSections for History page (use INSERT IGNORE to skip if already exists)
INSERT IGNORE INTO `CmsSection` (`CmsSectionId`, `CmsPageId`, `SectionKey`) VALUES
(38, 3, 'historical_locations_section'),
(40, 3, 'intro_section'),
(42, 3, 'hero_section'),
(45, 3, 'gradient_section'),
(51, 3, 'route_section'),
(52, 3, 'ticket_options_section'),
(53, 3, 'history_pricing_section'),
(54, 3, 'history_important_tour_info_section');
