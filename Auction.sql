-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 14, 2023 at 03:22 PM
-- Server version: 5.7.39
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Auction`
--
USE Auction;
-- --------------------------------------------------------

--
-- Table structure for table `AuctionItem`
--

CREATE TABLE `AuctionItem` (
  `ItemAuctionID` int(4) NOT NULL,
  `Title` text NOT NULL,
  `SellerID` int(4) NOT NULL,
  `CategoryID` int(4) NOT NULL,
  `Description` text NOT NULL,
  `StartingPrice` int(11) NOT NULL,
  `ReservePrice` int(11) NOT NULL,
  `EndDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Bid`
--

CREATE TABLE `Bid` (
  `BidID` int(4) NOT NULL,
  `BuyerID` int(4) NOT NULL,
  `ItemAuctionID` int(4) NOT NULL,
  `BidTime` datetime NOT NULL,
  `BidAmount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Buyer`
--

CREATE TABLE `Buyer` (
  `BuyerID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Categories`
--

CREATE TABLE `Categories` (
  `CategoryID` int(4) NOT NULL AUTO_INCREMENT,
  `CategoryName` varchar(255) NOT NULL,
  PRIMARY KEY (`CategoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `Categories` (`CategoryName`) VALUES ('fashion');
INSERT INTO `Categories` (`CategoryName`) VALUES ('electronics');
INSERT INTO `Categories` (`CategoryName`) VALUES ('beauty');
INSERT INTO `Categories` (`CategoryName`) VALUES ('home');
INSERT INTO `Categories` (`CategoryName`) VALUES ('outdoor');
INSERT INTO `Categories` (`CategoryName`) VALUES ('art');

-- --------------------------------------------------------

--
-- Table structure for table `Notification`
--

CREATE TABLE `Notification` (
  `NotificationID` int(4) NOT NULL,
  `UserID` int(4) NOT NULL,
  `DateTime` datetime NOT NULL,
  `Message` text NOT NULL,
  `Type` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Sellers`
--

CREATE TABLE `Sellers` (
  `UserID` int(4) NOT NULL,
  `SellerID` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Transactions`
--

CREATE TABLE `Transactions` (
  `TransactionID` int(4) NOT NULL,
  `SellerID` int(4) NOT NULL,
  `ItemAuctionID` int(4) NOT NULL,
  `BidID` int(4) NOT NULL,
  `BuyerID` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `UserID` int(4) NOT NULL,
  `Email` text NOT NULL,
  `Password` text NOT NULL,
  `FirstName` text NOT NULL,
  `LastName` text NOT NULL,
  `Role` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Watchlist`
--

CREATE TABLE `Watchlist` (
  `WatchlistID` int(4) NOT NULL,
  `UserID` int(4) NOT NULL,
  `Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for `WatchListItems` (JOIN TABLE) ******************
-- 

CREATE TABLE `WatchListItems` (
  `WatchListID` int(11) NOT NULL,
  `ItemAuctionID` int(11) NOT NULL,
  PRIMARY KEY (`WatchListID`, `ItemAuctionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `AuctionItem`
--
ALTER TABLE `AuctionItem`
  ADD PRIMARY KEY (`ItemAuctionID`),
  ADD KEY `FK2_AuctionItem` (`CategoryID`),
  ADD KEY `FK1_AuctionItem` (`SellerID`);

--
-- Indexes for table `Bid`
--
ALTER TABLE `Bid`
  ADD PRIMARY KEY (`BidID`),
  ADD KEY `FK1_BuyerID` (`BuyerID`),
  ADD KEY `FK2_ItemAuctionID` (`ItemAuctionID`);

--
-- Indexes for table `Buyer`
--
ALTER TABLE `Buyer`
  ADD PRIMARY KEY (`BuyerID`),
  ADD KEY `FK_UserID_2` (`UserID`);

--
-- Indexes for table `Categories`
--


--
-- Indexes for table `Notification`
--
ALTER TABLE `Notification`
  ADD PRIMARY KEY (`NotificationID`);

--
-- Indexes for table `Sellers`
--
ALTER TABLE `Sellers`
  ADD PRIMARY KEY (`SellerID`),
  ADD KEY `FK_UserID` (`UserID`);

--
-- Indexes for table `Transactions`
--
ALTER TABLE `Transactions`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `FK2_Transactions` (`ItemAuctionID`),
  ADD KEY `FK3_Transactions` (`BidID`),
  ADD KEY `FK4_Transactions` (`BuyerID`),
  ADD KEY `FK1_Transactions` (`SellerID`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `Watchlist`
--
ALTER TABLE `Watchlist`
  ADD PRIMARY KEY (`WatchlistID`),
  ADD KEY `FK1_Watchlist` (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `AuctionItem`
--
ALTER TABLE `AuctionItem`
  MODIFY `ItemAuctionID` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Bid`
--
ALTER TABLE `Bid`
  MODIFY `BidID` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Buyer`
--
ALTER TABLE `Buyer`
  MODIFY `BuyerID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Notification`
--
ALTER TABLE `Notification`
  MODIFY `NotificationID` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Sellers`
--
ALTER TABLE `Sellers`
  MODIFY `SellerID` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Transactions`
--
ALTER TABLE `Transactions`
  MODIFY `TransactionID` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `UserID` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Watchlist`
--
ALTER TABLE `Watchlist`
  MODIFY `WatchlistID` int(4) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `AuctionItem`
--
ALTER TABLE `AuctionItem`
  ADD CONSTRAINT `FK1_AuctionItem` FOREIGN KEY (`SellerID`) REFERENCES `Sellers` (`SellerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK2_AuctionItem` FOREIGN KEY (`CategoryID`) REFERENCES `Categories` (`CategoryID`) ON DELETE CASCADE ON UPDATE CASCADE;
  
--
-- Constraints for table `Bid`
--
ALTER TABLE `Bid`
  ADD CONSTRAINT `FK2_ItemAuctionID` FOREIGN KEY (`ItemAuctionID`) REFERENCES `AuctionItem` (`ItemAuctionID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Buyer`
--
ALTER TABLE `Buyer`
  ADD CONSTRAINT `FK_UserID_2` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`);

--
-- Constraints for table `Sellers`
--
ALTER TABLE `Sellers`
  ADD CONSTRAINT `FK_UserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`);

--
-- Constraints for table `Transactions`
--
ALTER TABLE `Transactions`
  ADD CONSTRAINT `FK1_Transactions` FOREIGN KEY (`SellerID`) REFERENCES `Sellers` (`SellerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK2_Transactions` FOREIGN KEY (`ItemAuctionID`) REFERENCES `AuctionItem` (`ItemAuctionID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK3_Transactions` FOREIGN KEY (`BidID`) REFERENCES `Bid` (`BidID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK4_Transactions` FOREIGN KEY (`BuyerID`) REFERENCES `Buyer` (`BuyerID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `Watchlist`
-- 
ALTER TABLE `Watchlist`
  ADD CONSTRAINT `FK_WatchList_User` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE;

-- 
-- Constraints for table `WatchListItems`
-- 
ALTER TABLE `WatchListItems`
  ADD CONSTRAINT `FK_WatchListItems_WatchList` FOREIGN KEY (`WatchListID`) REFERENCES `WatchList` (`WatchListID`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_WatchListItems_Items` FOREIGN KEY (`ItemAuctionID`) REFERENCES `AuctionItem` (`ItemAuctionID`) ON DELETE CASCADE;

-- 
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
