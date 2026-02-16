-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Feb 16, 2026 at 02:17 PM
-- Server version: 12.0.2-MariaDB-ubu2404
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- Seed data for CmsPage - History page
-- Requires: CmsPage table to exist
-- Run after: complete-database setup

-- Insert CmsPage for History (use INSERT IGNORE to skip if already exists)
INSERT IGNORE INTO `CmsPage` (`CmsPageId`, `Slug`, `Title`) VALUES
(3, 'history', 'A Stroll Through History');
