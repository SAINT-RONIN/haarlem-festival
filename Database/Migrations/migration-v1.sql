-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Feb 05, 2026 at 04:09 PM
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
-- Table structure for table `Artist`
--

CREATE TABLE `Artist` (
  `ArtistId` int(11) NOT NULL,
  `Name` varchar(120) NOT NULL,
  `Style` varchar(120) NOT NULL DEFAULT '',
  `BioHtml` longtext NOT NULL DEFAULT '<p></p>',
  `ImageAssetId` int(11) DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `CmsItem`
--

CREATE TABLE `CmsItem` (
  `CmsItemId` int(11) NOT NULL,
  `CmsSectionId` int(11) NOT NULL,
  `ItemKey` varchar(80) NOT NULL,
  `ItemType` varchar(20) NOT NULL,
  `TextValue` longtext DEFAULT NULL,
  `HtmlValue` longtext DEFAULT NULL,
  `MediaAssetId` int(11) DEFAULT NULL,
  `UpdatedAtUtc` datetime NOT NULL DEFAULT utc_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `CmsPage`
--

CREATE TABLE `CmsPage` (
  `CmsPageId` int(11) NOT NULL,
  `Slug` varchar(60) NOT NULL,
  `Title` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `CmsSection`
--

CREATE TABLE `CmsSection` (
  `CmsSectionId` int(11) NOT NULL,
  `CmsPageId` int(11) NOT NULL,
  `SectionKey` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `EmailConfirmationToken`
--

CREATE TABLE `EmailConfirmationToken` (
  `EmailConfirmationTokenId` int(11) NOT NULL,
  `UserAccountId` int(11) NOT NULL,
  `Token` varchar(100) NOT NULL,
  `ExpiresAtUtc` datetime NOT NULL,
  `UsedAtUtc` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Event`
--

CREATE TABLE `Event` (
  `EventId` int(11) NOT NULL,
  `EventTypeId` int(11) NOT NULL,
  `Title` varchar(160) NOT NULL,
  `ShortDescription` varchar(300) NOT NULL DEFAULT '',
  `LongDescriptionHtml` longtext NOT NULL DEFAULT '<p></p>',
  `FeaturedImageAssetId` int(11) DEFAULT NULL,
  `VenueId` int(11) DEFAULT NULL,
  `ArtistId` int(11) DEFAULT NULL,
  `RestaurantId` int(11) DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `EventSession`
--

CREATE TABLE `EventSession` (
  `EventSessionId` int(11) NOT NULL,
  `EventId` int(11) NOT NULL,
  `StartDateTime` datetime NOT NULL,
  `EndDateTime` datetime DEFAULT NULL,
  `CapacityTotal` int(11) NOT NULL,
  `CapacitySingleTicketLimit` int(11) NOT NULL,
  `SoldSingleTickets` int(11) NOT NULL DEFAULT 0,
  `SoldReservedSeats` int(11) NOT NULL DEFAULT 0,
  `HallName` varchar(80) DEFAULT NULL,
  `SessionType` varchar(40) DEFAULT NULL,
  `DurationMinutes` int(11) DEFAULT NULL,
  `LanguageCode` varchar(10) DEFAULT NULL,
  `MinAge` int(11) DEFAULT NULL,
  `MaxAge` int(11) DEFAULT NULL,
  `ReservationRequired` tinyint(1) NOT NULL DEFAULT 0,
  `IsFree` tinyint(1) NOT NULL DEFAULT 0,
  `Notes` varchar(400) NOT NULL DEFAULT '',
  `IsCancelled` tinyint(1) NOT NULL DEFAULT 0
) ;

-- --------------------------------------------------------

--
-- Table structure for table `EventSessionLabel`
--

CREATE TABLE `EventSessionLabel` (
  `EventSessionLabelId` int(11) NOT NULL,
  `EventSessionId` int(11) NOT NULL,
  `LabelText` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `EventSessionPrice`
--

CREATE TABLE `EventSessionPrice` (
  `EventSessionPriceId` int(11) NOT NULL,
  `EventSessionId` int(11) NOT NULL,
  `PriceTierId` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `CurrencyCode` char(3) NOT NULL DEFAULT 'EUR',
  `VatRate` decimal(5,2) NOT NULL DEFAULT 21.00
) ;

-- --------------------------------------------------------

--
-- Table structure for table `EventType`
--

CREATE TABLE `EventType` (
  `EventTypeId` int(11) NOT NULL,
  `Name` varchar(40) NOT NULL,
  `Slug` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `EventType`
--

INSERT INTO `EventType` (`EventTypeId`, `Name`, `Slug`) VALUES
(1, 'Jazz', 'jazz'),
(2, 'Dance', 'dance'),
(3, 'History', 'history'),
(4, 'Storytelling', 'storytelling'),
(5, 'Restaurant', 'restaurant');

-- --------------------------------------------------------

--
-- Table structure for table `Guide`
--

CREATE TABLE `Guide` (
  `GuideId` int(11) NOT NULL,
  `Name` varchar(120) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `HistoryTour`
--

CREATE TABLE `HistoryTour` (
  `HistoryTourId` int(11) NOT NULL,
  `EventSessionId` int(11) NOT NULL,
  `LanguageCode` varchar(10) NOT NULL,
  `GuideCount` int(11) NOT NULL,
  `SeatsPerTour` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `HistoryTourGuide`
--

CREATE TABLE `HistoryTourGuide` (
  `HistoryTourId` int(11) NOT NULL,
  `GuideId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Invoice`
--

CREATE TABLE `Invoice` (
  `InvoiceId` int(11) NOT NULL,
  `OrderId` int(11) NOT NULL,
  `InvoiceNumber` varchar(30) NOT NULL,
  `InvoiceDateUtc` datetime NOT NULL,
  `ClientName` varchar(160) NOT NULL,
  `PhoneNumber` varchar(40) NOT NULL DEFAULT '',
  `AddressLine` varchar(200) NOT NULL DEFAULT '',
  `EmailAddress` varchar(200) NOT NULL,
  `SubtotalAmount` decimal(10,2) NOT NULL,
  `TotalVatAmount` decimal(10,2) NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `PaymentDateUtc` datetime DEFAULT NULL,
  `PdfAssetId` int(11) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `InvoiceLine`
--

CREATE TABLE `InvoiceLine` (
  `InvoiceLineId` int(11) NOT NULL,
  `InvoiceId` int(11) NOT NULL,
  `LineDescription` varchar(200) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `UnitPrice` decimal(10,2) NOT NULL,
  `VatRate` decimal(5,2) NOT NULL,
  `LineSubtotal` decimal(10,2) NOT NULL,
  `LineVatAmount` decimal(10,2) NOT NULL,
  `LineTotal` decimal(10,2) NOT NULL,
  `DonationAmount` decimal(10,2) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `MediaAsset`
--

CREATE TABLE `MediaAsset` (
  `MediaAssetId` int(11) NOT NULL,
  `FilePath` varchar(500) NOT NULL,
  `OriginalFileName` varchar(255) NOT NULL,
  `MimeType` varchar(100) NOT NULL,
  `FileSizeBytes` bigint(20) NOT NULL,
  `AltText` varchar(200) NOT NULL DEFAULT '',
  `CreatedAtUtc` datetime NOT NULL DEFAULT utc_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Order`
--

CREATE TABLE `Order` (
  `OrderId` int(11) NOT NULL,
  `OrderNumber` varchar(30) NOT NULL,
  `UserAccountId` int(11) NOT NULL,
  `ProgramId` int(11) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `CreatedAtUtc` datetime NOT NULL DEFAULT utc_timestamp(),
  `PayBeforeUtc` datetime DEFAULT NULL,
  `Subtotal` decimal(10,2) NOT NULL,
  `VatTotal` decimal(10,2) NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `OrderItem`
--

CREATE TABLE `OrderItem` (
  `OrderItemId` int(11) NOT NULL,
  `OrderId` int(11) NOT NULL,
  `EventSessionId` int(11) DEFAULT NULL,
  `HistoryTourId` int(11) DEFAULT NULL,
  `PassPurchaseId` int(11) DEFAULT NULL,
  `Quantity` int(11) NOT NULL,
  `UnitPrice` decimal(10,2) NOT NULL,
  `VatRate` decimal(5,2) NOT NULL,
  `DonationAmount` decimal(10,2) DEFAULT NULL,
  `SpecialRequest` varchar(500) NOT NULL DEFAULT ''
) ;

-- --------------------------------------------------------

--
-- Table structure for table `PassPurchase`
--

CREATE TABLE `PassPurchase` (
  `PassPurchaseId` int(11) NOT NULL,
  `PassTypeId` int(11) NOT NULL,
  `UserAccountId` int(11) NOT NULL,
  `ValidDate` date DEFAULT NULL,
  `ValidFromDate` date DEFAULT NULL,
  `ValidToDate` date DEFAULT NULL,
  `CreatedAtUtc` datetime NOT NULL DEFAULT utc_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `PassType`
--

CREATE TABLE `PassType` (
  `PassTypeId` int(11) NOT NULL,
  `EventTypeId` int(11) NOT NULL,
  `PassName` varchar(50) NOT NULL,
  `PassScope` varchar(20) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `VatRate` decimal(5,2) NOT NULL,
  `CurrencyCode` char(3) NOT NULL DEFAULT 'EUR',
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ;

--
-- Dumping data for table `PassType`
--

INSERT INTO `PassType` (`PassTypeId`, `EventTypeId`, `PassName`, `PassScope`, `Price`, `VatRate`, `CurrencyCode`, `IsActive`) VALUES
(1, 1, 'DayPass', 'Day', 35.00, 21.00, 'EUR', 1),
(2, 1, 'AllAccess', 'Range', 80.00, 21.00, 'EUR', 1),
(3, 2, 'DayPass_Fri', 'Day', 125.00, 21.00, 'EUR', 1),
(4, 2, 'DayPass_SatSun', 'Day', 150.00, 21.00, 'EUR', 1),
(5, 2, 'AllAccess', 'Range', 250.00, 21.00, 'EUR', 1);

-- --------------------------------------------------------

--
-- Table structure for table `PasswordResetToken`
--

CREATE TABLE `PasswordResetToken` (
  `PasswordResetTokenId` int(11) NOT NULL,
  `UserAccountId` int(11) NOT NULL,
  `Token` varchar(100) NOT NULL,
  `ExpiresAtUtc` datetime NOT NULL,
  `UsedAtUtc` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Payment`
--

CREATE TABLE `Payment` (
  `PaymentId` int(11) NOT NULL,
  `OrderId` int(11) NOT NULL,
  `Method` varchar(20) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `ProviderRef` varchar(80) DEFAULT NULL,
  `CreatedAtUtc` datetime NOT NULL DEFAULT utc_timestamp(),
  `PaidAtUtc` datetime DEFAULT NULL,
  `PaidUniqueFlag` tinyint(4) GENERATED ALWAYS AS (case when `Status` = 'Paid' then 1 else NULL end) STORED
) ;

-- --------------------------------------------------------

--
-- Table structure for table `PriceTier`
--

CREATE TABLE `PriceTier` (
  `PriceTierId` int(11) NOT NULL,
  `Name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `PriceTier`
--

INSERT INTO `PriceTier` (`PriceTierId`, `Name`) VALUES
(1, 'Adult'),
(2, 'ChildU12'),
(3, 'Family'),
(5, 'PayWhatYouLike'),
(4, 'ReservationFee');

-- --------------------------------------------------------

--
-- Table structure for table `Program`
--

CREATE TABLE `Program` (
  `ProgramId` int(11) NOT NULL,
  `UserAccountId` int(11) DEFAULT NULL,
  `SessionKey` varchar(80) DEFAULT NULL,
  `CreatedAtUtc` datetime NOT NULL DEFAULT utc_timestamp(),
  `IsCheckedOut` tinyint(1) NOT NULL DEFAULT 0
) ;

-- --------------------------------------------------------

--
-- Table structure for table `ProgramItem`
--

CREATE TABLE `ProgramItem` (
  `ProgramItemId` int(11) NOT NULL,
  `ProgramId` int(11) NOT NULL,
  `EventSessionId` int(11) DEFAULT NULL,
  `HistoryTourId` int(11) DEFAULT NULL,
  `PassTypeId` int(11) DEFAULT NULL,
  `PassValidDate` date DEFAULT NULL,
  `Quantity` int(11) NOT NULL,
  `DonationAmount` decimal(10,2) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `Restaurant`
--

CREATE TABLE `Restaurant` (
  `RestaurantId` int(11) NOT NULL,
  `Name` varchar(120) NOT NULL,
  `AddressLine` varchar(200) NOT NULL DEFAULT '',
  `City` varchar(80) NOT NULL DEFAULT 'Haarlem',
  `Stars` int(11) DEFAULT NULL,
  `CuisineType` varchar(160) NOT NULL DEFAULT '',
  `DescriptionHtml` longtext NOT NULL DEFAULT '<p></p>',
  `ImageAssetId` int(11) DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ;

-- --------------------------------------------------------

--
-- Table structure for table `ScheduleDay`
--

CREATE TABLE `ScheduleDay` (
  `ScheduleDayId` int(11) NOT NULL,
  `EventTypeId` int(11) NOT NULL,
  `Date` date NOT NULL,
  `IsDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `DeletedAtUtc` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SessionDiscountRule`
--

CREATE TABLE `SessionDiscountRule` (
  `SessionDiscountRuleId` int(11) NOT NULL,
  `EventSessionId` int(11) NOT NULL,
  `RuleName` varchar(60) NOT NULL,
  `DiscountPercent` decimal(5,2) NOT NULL,
  `AppliesToPriceTierId` int(11) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `Ticket`
--

CREATE TABLE `Ticket` (
  `TicketId` int(11) NOT NULL,
  `OrderItemId` int(11) NOT NULL,
  `TicketCode` varchar(80) NOT NULL,
  `IsScanned` tinyint(1) NOT NULL DEFAULT 0,
  `ScannedAtUtc` datetime DEFAULT NULL,
  `ScannedByUserId` int(11) DEFAULT NULL,
  `PdfAssetId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `UserAccount`
--

CREATE TABLE `UserAccount` (
  `UserAccountId` int(11) NOT NULL,
  `UserRoleId` int(11) NOT NULL,
  `Username` varchar(60) NOT NULL,
  `Email` varchar(200) NOT NULL,
  `PasswordHash` varbinary(64) NOT NULL,
  `PasswordSalt` varbinary(32) NOT NULL,
  `FirstName` varchar(60) NOT NULL,
  `LastName` varchar(80) NOT NULL,
  `ProfilePictureAssetId` int(11) DEFAULT NULL,
  `IsEmailConfirmed` tinyint(1) NOT NULL DEFAULT 0,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `RegisteredAtUtc` datetime NOT NULL DEFAULT utc_timestamp(),
  `UpdatedAtUtc` datetime NOT NULL DEFAULT utc_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `UserRole`
--

CREATE TABLE `UserRole` (
  `UserRoleId` int(11) NOT NULL,
  `RoleName` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `UserRole`
--

INSERT INTO `UserRole` (`UserRoleId`, `RoleName`) VALUES
(3, 'Administrator'),
(1, 'Customer'),
(2, 'Employee');

-- --------------------------------------------------------

--
-- Table structure for table `Venue`
--

CREATE TABLE `Venue` (
  `VenueId` int(11) NOT NULL,
  `Name` varchar(120) NOT NULL,
  `AddressLine` varchar(200) NOT NULL DEFAULT '',
  `City` varchar(80) NOT NULL DEFAULT 'Haarlem'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Artist`
--
ALTER TABLE `Artist`
  ADD PRIMARY KEY (`ArtistId`),
  ADD KEY `FK_Artist_Image` (`ImageAssetId`);

--
-- Indexes for table `CmsItem`
--
ALTER TABLE `CmsItem`
  ADD PRIMARY KEY (`CmsItemId`),
  ADD UNIQUE KEY `UQ_CmsItem` (`CmsSectionId`,`ItemKey`),
  ADD KEY `IX_CmsItem_Section` (`CmsSectionId`),
  ADD KEY `FK_CmsItem_Asset` (`MediaAssetId`);

--
-- Indexes for table `CmsPage`
--
ALTER TABLE `CmsPage`
  ADD PRIMARY KEY (`CmsPageId`),
  ADD UNIQUE KEY `UQ_CmsPage_Slug` (`Slug`);

--
-- Indexes for table `CmsSection`
--
ALTER TABLE `CmsSection`
  ADD PRIMARY KEY (`CmsSectionId`),
  ADD UNIQUE KEY `UQ_CmsSection` (`CmsPageId`,`SectionKey`),
  ADD KEY `IX_CmsSection_Page` (`CmsPageId`);

--
-- Indexes for table `EmailConfirmationToken`
--
ALTER TABLE `EmailConfirmationToken`
  ADD PRIMARY KEY (`EmailConfirmationTokenId`),
  ADD UNIQUE KEY `UQ_EmailConfirmationToken_Token` (`Token`),
  ADD KEY `IX_EmailConfirmationToken_User` (`UserAccountId`);

--
-- Indexes for table `Event`
--
ALTER TABLE `Event`
  ADD PRIMARY KEY (`EventId`),
  ADD KEY `IX_Event_Type` (`EventTypeId`,`IsActive`),
  ADD KEY `FK_Event_Image` (`FeaturedImageAssetId`),
  ADD KEY `FK_Event_Venue` (`VenueId`),
  ADD KEY `FK_Event_Artist` (`ArtistId`),
  ADD KEY `FK_Event_Restaurant` (`RestaurantId`);

--
-- Indexes for table `EventSession`
--
ALTER TABLE `EventSession`
  ADD PRIMARY KEY (`EventSessionId`),
  ADD KEY `IX_Session_EventTime` (`EventId`,`StartDateTime`),
  ADD KEY `IX_EventSession_Age` (`MinAge`,`MaxAge`);

--
-- Indexes for table `EventSessionLabel`
--
ALTER TABLE `EventSessionLabel`
  ADD PRIMARY KEY (`EventSessionLabelId`),
  ADD KEY `IX_SessionLabel_Session` (`EventSessionId`);

--
-- Indexes for table `EventSessionPrice`
--
ALTER TABLE `EventSessionPrice`
  ADD PRIMARY KEY (`EventSessionPriceId`),
  ADD UNIQUE KEY `UQ_EventSessionPrice` (`EventSessionId`,`PriceTierId`),
  ADD KEY `IX_EventSessionPrice_Session` (`EventSessionId`),
  ADD KEY `FK_EventSessionPrice_Tier` (`PriceTierId`);

--
-- Indexes for table `EventType`
--
ALTER TABLE `EventType`
  ADD PRIMARY KEY (`EventTypeId`),
  ADD UNIQUE KEY `UQ_EventType_Name` (`Name`),
  ADD UNIQUE KEY `UQ_EventType_Slug` (`Slug`);

--
-- Indexes for table `Guide`
--
ALTER TABLE `Guide`
  ADD PRIMARY KEY (`GuideId`),
  ADD UNIQUE KEY `UQ_Guide_Name` (`Name`);

--
-- Indexes for table `HistoryTour`
--
ALTER TABLE `HistoryTour`
  ADD PRIMARY KEY (`HistoryTourId`),
  ADD UNIQUE KEY `UQ_HistoryTour` (`EventSessionId`,`LanguageCode`),
  ADD KEY `IX_HistoryTour_Session` (`EventSessionId`);

--
-- Indexes for table `HistoryTourGuide`
--
ALTER TABLE `HistoryTourGuide`
  ADD PRIMARY KEY (`HistoryTourId`,`GuideId`),
  ADD KEY `IX_HistoryTourGuide_Guide` (`GuideId`);

--
-- Indexes for table `Invoice`
--
ALTER TABLE `Invoice`
  ADD PRIMARY KEY (`InvoiceId`),
  ADD UNIQUE KEY `UQ_Invoice_InvoiceNumber` (`InvoiceNumber`),
  ADD UNIQUE KEY `UQ_Invoice_Order` (`OrderId`),
  ADD KEY `FK_Invoice_Pdf` (`PdfAssetId`);

--
-- Indexes for table `InvoiceLine`
--
ALTER TABLE `InvoiceLine`
  ADD PRIMARY KEY (`InvoiceLineId`),
  ADD UNIQUE KEY `UQ_InvoiceLine` (`InvoiceId`,`LineDescription`),
  ADD KEY `IX_InvoiceLine_Invoice` (`InvoiceId`);

--
-- Indexes for table `MediaAsset`
--
ALTER TABLE `MediaAsset`
  ADD PRIMARY KEY (`MediaAssetId`);

--
-- Indexes for table `Order`
--
ALTER TABLE `Order`
  ADD PRIMARY KEY (`OrderId`),
  ADD UNIQUE KEY `UQ_Order_OrderNumber` (`OrderNumber`),
  ADD KEY `IX_Order_User` (`UserAccountId`,`CreatedAtUtc`),
  ADD KEY `IX_Order_Status` (`Status`,`PayBeforeUtc`),
  ADD KEY `FK_Order_Program` (`ProgramId`);

--
-- Indexes for table `OrderItem`
--
ALTER TABLE `OrderItem`
  ADD PRIMARY KEY (`OrderItemId`),
  ADD KEY `IX_OrderItem_Order` (`OrderId`),
  ADD KEY `IX_OrderItem_Session` (`EventSessionId`),
  ADD KEY `IX_OrderItem_HistoryTour` (`HistoryTourId`),
  ADD KEY `FK_OrderItem_PassPurchase` (`PassPurchaseId`);

--
-- Indexes for table `PassPurchase`
--
ALTER TABLE `PassPurchase`
  ADD PRIMARY KEY (`PassPurchaseId`),
  ADD KEY `FK_PassPurchase_PassType` (`PassTypeId`),
  ADD KEY `FK_PassPurchase_User` (`UserAccountId`);

--
-- Indexes for table `PassType`
--
ALTER TABLE `PassType`
  ADD PRIMARY KEY (`PassTypeId`),
  ADD UNIQUE KEY `UQ_PassType` (`EventTypeId`,`PassName`);

--
-- Indexes for table `PasswordResetToken`
--
ALTER TABLE `PasswordResetToken`
  ADD PRIMARY KEY (`PasswordResetTokenId`),
  ADD UNIQUE KEY `UQ_PasswordResetToken_Token` (`Token`),
  ADD KEY `IX_PasswordResetToken_User` (`UserAccountId`);

--
-- Indexes for table `Payment`
--
ALTER TABLE `Payment`
  ADD PRIMARY KEY (`PaymentId`),
  ADD UNIQUE KEY `UX_Payment_Order_Paid` (`OrderId`,`PaidUniqueFlag`),
  ADD KEY `IX_Payment_Order` (`OrderId`);

--
-- Indexes for table `PriceTier`
--
ALTER TABLE `PriceTier`
  ADD PRIMARY KEY (`PriceTierId`),
  ADD UNIQUE KEY `UQ_PriceTier_Name` (`Name`);

--
-- Indexes for table `Program`
--
ALTER TABLE `Program`
  ADD PRIMARY KEY (`ProgramId`),
  ADD KEY `IX_Program_User` (`UserAccountId`,`IsCheckedOut`),
  ADD KEY `IX_Program_SessionKey` (`SessionKey`,`IsCheckedOut`);

--
-- Indexes for table `ProgramItem`
--
ALTER TABLE `ProgramItem`
  ADD PRIMARY KEY (`ProgramItemId`),
  ADD KEY `IX_ProgramItem_Program` (`ProgramId`),
  ADD KEY `IX_ProgramItem_Session` (`EventSessionId`),
  ADD KEY `IX_ProgramItem_HistoryTour` (`HistoryTourId`),
  ADD KEY `FK_ProgramItem_PassType` (`PassTypeId`);

--
-- Indexes for table `Restaurant`
--
ALTER TABLE `Restaurant`
  ADD PRIMARY KEY (`RestaurantId`),
  ADD KEY `FK_Restaurant_Image` (`ImageAssetId`);

--
-- Indexes for table `ScheduleDay`
--
ALTER TABLE `ScheduleDay`
  ADD PRIMARY KEY (`ScheduleDayId`),
  ADD UNIQUE KEY `UQ_ScheduleDay` (`EventTypeId`,`Date`),
  ADD KEY `IX_ScheduleDay_Filter` (`EventTypeId`,`Date`,`IsDeleted`);

--
-- Indexes for table `SessionDiscountRule`
--
ALTER TABLE `SessionDiscountRule`
  ADD PRIMARY KEY (`SessionDiscountRuleId`),
  ADD KEY `IX_DiscountRule_Session` (`EventSessionId`),
  ADD KEY `FK_DiscountRule_Tier` (`AppliesToPriceTierId`);

--
-- Indexes for table `Ticket`
--
ALTER TABLE `Ticket`
  ADD PRIMARY KEY (`TicketId`),
  ADD UNIQUE KEY `UQ_Ticket_TicketCode` (`TicketCode`),
  ADD KEY `IX_Ticket_Scan` (`IsScanned`,`TicketCode`),
  ADD KEY `FK_Ticket_OrderItem` (`OrderItemId`),
  ADD KEY `FK_Ticket_ScannedBy` (`ScannedByUserId`),
  ADD KEY `FK_Ticket_Pdf` (`PdfAssetId`);

--
-- Indexes for table `UserAccount`
--
ALTER TABLE `UserAccount`
  ADD PRIMARY KEY (`UserAccountId`),
  ADD UNIQUE KEY `UQ_UserAccount_Username` (`Username`),
  ADD UNIQUE KEY `UQ_UserAccount_Email` (`Email`),
  ADD KEY `IX_UserAccount_Role` (`UserRoleId`,`IsActive`),
  ADD KEY `FK_UserAccount_ProfilePic` (`ProfilePictureAssetId`);

--
-- Indexes for table `UserRole`
--
ALTER TABLE `UserRole`
  ADD PRIMARY KEY (`UserRoleId`),
  ADD UNIQUE KEY `UQ_UserRole_RoleName` (`RoleName`);

--
-- Indexes for table `Venue`
--
ALTER TABLE `Venue`
  ADD PRIMARY KEY (`VenueId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Artist`
--
ALTER TABLE `Artist`
  MODIFY `ArtistId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `CmsItem`
--
ALTER TABLE `CmsItem`
  MODIFY `CmsItemId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `CmsPage`
--
ALTER TABLE `CmsPage`
  MODIFY `CmsPageId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `CmsSection`
--
ALTER TABLE `CmsSection`
  MODIFY `CmsSectionId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `EmailConfirmationToken`
--
ALTER TABLE `EmailConfirmationToken`
  MODIFY `EmailConfirmationTokenId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Event`
--
ALTER TABLE `Event`
  MODIFY `EventId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `EventSession`
--
ALTER TABLE `EventSession`
  MODIFY `EventSessionId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `EventSessionLabel`
--
ALTER TABLE `EventSessionLabel`
  MODIFY `EventSessionLabelId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `EventSessionPrice`
--
ALTER TABLE `EventSessionPrice`
  MODIFY `EventSessionPriceId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `EventType`
--
ALTER TABLE `EventType`
  MODIFY `EventTypeId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Guide`
--
ALTER TABLE `Guide`
  MODIFY `GuideId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `HistoryTour`
--
ALTER TABLE `HistoryTour`
  MODIFY `HistoryTourId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Invoice`
--
ALTER TABLE `Invoice`
  MODIFY `InvoiceId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `InvoiceLine`
--
ALTER TABLE `InvoiceLine`
  MODIFY `InvoiceLineId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `MediaAsset`
--
ALTER TABLE `MediaAsset`
  MODIFY `MediaAssetId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Order`
--
ALTER TABLE `Order`
  MODIFY `OrderId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `OrderItem`
--
ALTER TABLE `OrderItem`
  MODIFY `OrderItemId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PassPurchase`
--
ALTER TABLE `PassPurchase`
  MODIFY `PassPurchaseId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PassType`
--
ALTER TABLE `PassType`
  MODIFY `PassTypeId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PasswordResetToken`
--
ALTER TABLE `PasswordResetToken`
  MODIFY `PasswordResetTokenId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Payment`
--
ALTER TABLE `Payment`
  MODIFY `PaymentId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PriceTier`
--
ALTER TABLE `PriceTier`
  MODIFY `PriceTierId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Program`
--
ALTER TABLE `Program`
  MODIFY `ProgramId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ProgramItem`
--
ALTER TABLE `ProgramItem`
  MODIFY `ProgramItemId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Restaurant`
--
ALTER TABLE `Restaurant`
  MODIFY `RestaurantId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ScheduleDay`
--
ALTER TABLE `ScheduleDay`
  MODIFY `ScheduleDayId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SessionDiscountRule`
--
ALTER TABLE `SessionDiscountRule`
  MODIFY `SessionDiscountRuleId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ticket`
--
ALTER TABLE `Ticket`
  MODIFY `TicketId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `UserAccount`
--
ALTER TABLE `UserAccount`
  MODIFY `UserAccountId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `UserRole`
--
ALTER TABLE `UserRole`
  MODIFY `UserRoleId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Venue`
--
ALTER TABLE `Venue`
  MODIFY `VenueId` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Artist`
--
ALTER TABLE `Artist`
  ADD CONSTRAINT `FK_Artist_Image` FOREIGN KEY (`ImageAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`);

--
-- Constraints for table `CmsItem`
--
ALTER TABLE `CmsItem`
  ADD CONSTRAINT `FK_CmsItem_Asset` FOREIGN KEY (`MediaAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_CmsItem_Section` FOREIGN KEY (`CmsSectionId`) REFERENCES `CmsSection` (`CmsSectionId`);

--
-- Constraints for table `CmsSection`
--
ALTER TABLE `CmsSection`
  ADD CONSTRAINT `FK_CmsSection_Page` FOREIGN KEY (`CmsPageId`) REFERENCES `CmsPage` (`CmsPageId`);

--
-- Constraints for table `EmailConfirmationToken`
--
ALTER TABLE `EmailConfirmationToken`
  ADD CONSTRAINT `FK_EmailConfirmationToken_User` FOREIGN KEY (`UserAccountId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `Event`
--
ALTER TABLE `Event`
  ADD CONSTRAINT `FK_Event_Artist` FOREIGN KEY (`ArtistId`) REFERENCES `Artist` (`ArtistId`),
  ADD CONSTRAINT `FK_Event_Image` FOREIGN KEY (`FeaturedImageAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_Event_Restaurant` FOREIGN KEY (`RestaurantId`) REFERENCES `Restaurant` (`RestaurantId`),
  ADD CONSTRAINT `FK_Event_Type` FOREIGN KEY (`EventTypeId`) REFERENCES `EventType` (`EventTypeId`),
  ADD CONSTRAINT `FK_Event_Venue` FOREIGN KEY (`VenueId`) REFERENCES `Venue` (`VenueId`);

--
-- Constraints for table `EventSession`
--
ALTER TABLE `EventSession`
  ADD CONSTRAINT `FK_EventSession_Event` FOREIGN KEY (`EventId`) REFERENCES `Event` (`EventId`);

--
-- Constraints for table `EventSessionLabel`
--
ALTER TABLE `EventSessionLabel`
  ADD CONSTRAINT `FK_EventSessionLabel_Session` FOREIGN KEY (`EventSessionId`) REFERENCES `EventSession` (`EventSessionId`);

--
-- Constraints for table `EventSessionPrice`
--
ALTER TABLE `EventSessionPrice`
  ADD CONSTRAINT `FK_EventSessionPrice_Session` FOREIGN KEY (`EventSessionId`) REFERENCES `EventSession` (`EventSessionId`),
  ADD CONSTRAINT `FK_EventSessionPrice_Tier` FOREIGN KEY (`PriceTierId`) REFERENCES `PriceTier` (`PriceTierId`);

--
-- Constraints for table `HistoryTour`
--
ALTER TABLE `HistoryTour`
  ADD CONSTRAINT `FK_HistoryTour_Session` FOREIGN KEY (`EventSessionId`) REFERENCES `EventSession` (`EventSessionId`);

--
-- Constraints for table `HistoryTourGuide`
--
ALTER TABLE `HistoryTourGuide`
  ADD CONSTRAINT `FK_HistoryTourGuide_Guide` FOREIGN KEY (`GuideId`) REFERENCES `Guide` (`GuideId`),
  ADD CONSTRAINT `FK_HistoryTourGuide_Tour` FOREIGN KEY (`HistoryTourId`) REFERENCES `HistoryTour` (`HistoryTourId`);

--
-- Constraints for table `Invoice`
--
ALTER TABLE `Invoice`
  ADD CONSTRAINT `FK_Invoice_Order` FOREIGN KEY (`OrderId`) REFERENCES `Order` (`OrderId`),
  ADD CONSTRAINT `FK_Invoice_Pdf` FOREIGN KEY (`PdfAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`);

--
-- Constraints for table `InvoiceLine`
--
ALTER TABLE `InvoiceLine`
  ADD CONSTRAINT `FK_InvoiceLine_Invoice` FOREIGN KEY (`InvoiceId`) REFERENCES `Invoice` (`InvoiceId`) ON DELETE CASCADE;

--
-- Constraints for table `Order`
--
ALTER TABLE `Order`
  ADD CONSTRAINT `FK_Order_Program` FOREIGN KEY (`ProgramId`) REFERENCES `Program` (`ProgramId`),
  ADD CONSTRAINT `FK_Order_User` FOREIGN KEY (`UserAccountId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `OrderItem`
--
ALTER TABLE `OrderItem`
  ADD CONSTRAINT `FK_OrderItem_HistoryTour` FOREIGN KEY (`HistoryTourId`) REFERENCES `HistoryTour` (`HistoryTourId`),
  ADD CONSTRAINT `FK_OrderItem_Order` FOREIGN KEY (`OrderId`) REFERENCES `Order` (`OrderId`),
  ADD CONSTRAINT `FK_OrderItem_PassPurchase` FOREIGN KEY (`PassPurchaseId`) REFERENCES `PassPurchase` (`PassPurchaseId`),
  ADD CONSTRAINT `FK_OrderItem_Session` FOREIGN KEY (`EventSessionId`) REFERENCES `EventSession` (`EventSessionId`);

--
-- Constraints for table `PassPurchase`
--
ALTER TABLE `PassPurchase`
  ADD CONSTRAINT `FK_PassPurchase_PassType` FOREIGN KEY (`PassTypeId`) REFERENCES `PassType` (`PassTypeId`),
  ADD CONSTRAINT `FK_PassPurchase_User` FOREIGN KEY (`UserAccountId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `PassType`
--
ALTER TABLE `PassType`
  ADD CONSTRAINT `FK_PassType_EventType` FOREIGN KEY (`EventTypeId`) REFERENCES `EventType` (`EventTypeId`);

--
-- Constraints for table `PasswordResetToken`
--
ALTER TABLE `PasswordResetToken`
  ADD CONSTRAINT `FK_PasswordResetToken_User` FOREIGN KEY (`UserAccountId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `Payment`
--
ALTER TABLE `Payment`
  ADD CONSTRAINT `FK_Payment_Order` FOREIGN KEY (`OrderId`) REFERENCES `Order` (`OrderId`);

--
-- Constraints for table `Program`
--
ALTER TABLE `Program`
  ADD CONSTRAINT `FK_Program_User` FOREIGN KEY (`UserAccountId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `ProgramItem`
--
ALTER TABLE `ProgramItem`
  ADD CONSTRAINT `FK_ProgramItem_HistoryTour` FOREIGN KEY (`HistoryTourId`) REFERENCES `HistoryTour` (`HistoryTourId`),
  ADD CONSTRAINT `FK_ProgramItem_PassType` FOREIGN KEY (`PassTypeId`) REFERENCES `PassType` (`PassTypeId`),
  ADD CONSTRAINT `FK_ProgramItem_Program` FOREIGN KEY (`ProgramId`) REFERENCES `Program` (`ProgramId`),
  ADD CONSTRAINT `FK_ProgramItem_Session` FOREIGN KEY (`EventSessionId`) REFERENCES `EventSession` (`EventSessionId`);

--
-- Constraints for table `Restaurant`
--
ALTER TABLE `Restaurant`
  ADD CONSTRAINT `FK_Restaurant_Image` FOREIGN KEY (`ImageAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`);

--
-- Constraints for table `ScheduleDay`
--
ALTER TABLE `ScheduleDay`
  ADD CONSTRAINT `FK_ScheduleDay_Type` FOREIGN KEY (`EventTypeId`) REFERENCES `EventType` (`EventTypeId`);

--
-- Constraints for table `SessionDiscountRule`
--
ALTER TABLE `SessionDiscountRule`
  ADD CONSTRAINT `FK_DiscountRule_Session` FOREIGN KEY (`EventSessionId`) REFERENCES `EventSession` (`EventSessionId`),
  ADD CONSTRAINT `FK_DiscountRule_Tier` FOREIGN KEY (`AppliesToPriceTierId`) REFERENCES `PriceTier` (`PriceTierId`);

--
-- Constraints for table `Ticket`
--
ALTER TABLE `Ticket`
  ADD CONSTRAINT `FK_Ticket_OrderItem` FOREIGN KEY (`OrderItemId`) REFERENCES `OrderItem` (`OrderItemId`),
  ADD CONSTRAINT `FK_Ticket_Pdf` FOREIGN KEY (`PdfAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_Ticket_ScannedBy` FOREIGN KEY (`ScannedByUserId`) REFERENCES `UserAccount` (`UserAccountId`);

--
-- Constraints for table `UserAccount`
--
ALTER TABLE `UserAccount`
  ADD CONSTRAINT `FK_UserAccount_ProfilePic` FOREIGN KEY (`ProfilePictureAssetId`) REFERENCES `MediaAsset` (`MediaAssetId`),
  ADD CONSTRAINT `FK_UserAccount_Role` FOREIGN KEY (`UserRoleId`) REFERENCES `UserRole` (`UserRoleId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
