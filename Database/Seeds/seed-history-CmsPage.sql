-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Feb 16, 2026 at 02:17 PM
-- Server version: 12.0.2-MariaDB-ubu2404
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `haarlem_festival_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `CmsPage`
--

CREATE TABLE `CmsPage` (
  `CmsPageId` int(11) NOT NULL,
  `Slug` varchar(60) NOT NULL,
  `Title` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `CmsPage`
--

INSERT INTO `CmsPage` (`CmsPageId`, `Slug`, `Title`) VALUES
(3, 'history', 'A Stroll Through History');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `CmsPage`
--
ALTER TABLE `CmsPage`
  ADD PRIMARY KEY (`CmsPageId`),
  ADD UNIQUE KEY `Slug` (`Slug`),
  ADD UNIQUE KEY `UNQ_CmsPage_Slug` (`Slug`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `CmsPage`
--
ALTER TABLE `CmsPage`
  MODIFY `CmsPageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
