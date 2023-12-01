-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 28, 2023 at 07:58 AM
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
-- Database: `auction`
--

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

--
-- Dumping data for table `AuctionItem`
--

INSERT INTO `AuctionItem` (`ItemAuctionID`, `Title`, `SellerID`, `CategoryID`, `Description`, `StartingPrice`, `ReservePrice`, `EndDate`) VALUES
(6, 'Classic Book', 1, 7, 'A classic novel', 11, 20, '2023-12-31 23:59:00'),
(7, 'Toy Train Set', 2, 8, 'Electric train set for kids', 30, 50, '2023-12-31 23:59:00'),
(8, 'Football', 3, 9, 'Professional football', 15, 25, '2023-12-31 23:59:00'),
(9, 'Guitar', 4, 10, 'Acoustic guitar', 100, 150, '2023-12-31 23:59:00'),
(10, 'Designer T-Shirt', 5, 11, 'Fashionable T-shirt', 20, 40, '2023-12-31 23:59:00'),
(11, 'Dining Chair', 6, 6, 'Wooden dining chair', 45, 70, '2023-12-31 23:59:00'),
(12, 'Smartphone', 7, 13, 'Latest model smartphone', 200, 300, '2023-12-31 23:59:00'),
(13, 'Car Accessories', 8, 14, 'Various car accessories', 50, 80, '2023-12-31 23:59:00'),
(14, 'Gardening Tools', 9, 15, 'Complete set of gardening tools', 35, 55, '2023-12-31 23:59:00'),
(15, 'Notebook Set', 1, 16, 'Set of high-quality notebooks', 10, 15, '2023-12-31 23:59:00'),
(16, 'Pet Food', 2, 17, 'Premium dog food', 25, 40, '2023-12-31 23:59:00'),
(17, 'Health Supplements', 3, 18, 'Vitamin supplements', 30, 45, '2023-12-31 23:59:00'),
(18, 'Running Shoes', 4, 19, 'High-performance running shoes', 60, 90, '2023-12-31 23:59:00'),
(19, 'Silver Necklace', 5, 20, 'Elegant silver necklace', 70, 100, '2023-12-31 23:59:00'),
(20, 'Makeup Kit', 6, 15, 'Professional makeup kit', 40, 60, '2023-12-31 23:59:00'),
(21, 'Organic Tea', 7, 23, 'Assorted organic tea', 15, 25, '2023-12-31 23:59:00'),
(22, 'Craft Beer Set', 8, 23, 'Selection of craft beers', 20, 30, '2023-12-31 23:59:00'),
(23, 'Kitchenware Set', 9, 24, 'Stainless steel kitchenware', 50, 75, '2023-12-31 23:59:00'),
(24, 'Luxury Bed Linen', 1, 25, 'Egyptian cotton bed linen', 80, 120, '2023-12-31 23:59:00'),
(25, 'Wall Art', 2, 6, 'Modern wall art decor', 45, 70, '2023-12-31 23:59:00');

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

--
-- Dumping data for table `Bid`
--

INSERT INTO `Bid` (`BidID`, `BuyerID`, `ItemAuctionID`, `BidTime`, `BidAmount`) VALUES
(21, 1, 6, '2023-11-15 10:00:00', 120),
(22, 2, 7, '2023-11-15 10:15:00', 200),
(23, 3, 8, '2023-11-15 10:30:00', 150),
(24, 4, 9, '2023-11-15 10:45:00', 220),
(25, 5, 10, '2023-11-15 11:00:00', 180),
(26, 6, 11, '2023-11-15 11:15:00', 210),
(27, 7, 12, '2023-11-15 11:30:00', 250),
(28, 8, 13, '2023-11-15 11:45:00', 190),
(29, 9, 14, '2023-11-15 12:00:00', 300),
(30, 10, 15, '2023-11-15 12:15:00', 160),
(31, 1, 16, '2023-11-15 12:30:00', 140),
(32, 2, 17, '2023-11-15 12:45:00', 210),
(33, 3, 18, '2023-11-15 13:00:00', 230),
(34, 4, 19, '2023-11-15 13:15:00', 200),
(35, 5, 20, '2023-11-15 13:30:00', 170),
(36, 6, 21, '2023-11-15 13:45:00', 190),
(37, 7, 22, '2023-11-15 14:00:00', 210),
(38, 8, 23, '2023-11-15 14:15:00', 220),
(39, 9, 24, '2023-11-15 14:30:00', 180),
(40, 10, 25, '2023-11-15 14:45:00', 200);

-- --------------------------------------------------------

--
-- Table structure for table `Buyer`
--

CREATE TABLE `Buyer` (
  `BuyerID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Buyer`
--

INSERT INTO `Buyer` (`BuyerID`, `UserID`) VALUES
(1, 1),
(2, 3),
(3, 5),
(4, 7),
(5, 9),
(6, 11),
(7, 13),
(8, 15),
(9, 17),
(10, 19);

-- --------------------------------------------------------

--
-- Table structure for table `Categories`
--

CREATE TABLE `Categories` (
  `CategoryID` int(4) NOT NULL,
  `CategoryName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Categories`
--

INSERT INTO `Categories` (`CategoryID`, `CategoryName`) VALUES
(1, 'Fashion'),
(2, 'Electronics'),
(3, 'Beauty'),
(4, 'Home'),
(5, 'Outdoor'),
(6, 'Art'),
(7, 'Books'),
(8, 'Toys'),
(9, 'Sports'),
(10, 'Music'),
(11, 'Clothing'),
(12, 'Furniture'),
(13, 'Technology'),
(14, 'Automotive'),
(15, 'Gardening'),
(16, 'Stationery'),
(17, 'Pets'),
(18, 'Healthcare'),
(19, 'Footwear'),
(20, 'Jewelry'),
(21, 'Cosmetics'),
(22, 'Groceries'),
(23, 'Beverages'),
(24, 'Cookware'),
(25, 'Bedding'),
(26, 'Decor');

-- --------------------------------------------------------

--
-- Table structure for table `ItemImages`
--

CREATE TABLE `ItemImages` (
  `ImageID` int(11) NOT NULL,
  `ItemAuctionID` int(11) NOT NULL,
  `ImagePath` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ItemImages`
--

INSERT INTO `ItemImages` (`ImageID`, `ItemAuctionID`, `ImagePath`) VALUES
(1, 6, './uploads/1-1.png'),
(2, 15, './uploads/15-1.png'),
(3, 24, './uploads/24-1.png'),
(4, 7, './uploads/7-1.png'),
(5, 16, './uploads/16-1.png'),
(6, 25, './uploads/25-1.png'),
(7, 8, './uploads/8-1.png'),
(8, 17, './uploads/17-1.png'),
(9, 9, './uploads/9-1.png'),
(10, 18, './uploads/18-1.png'),
(13, 10, './uploads/10-1.png'),
(14, 19, './uploads/19-1.png'),
(15, 11, './uploads/11-1.png'),
(16, 20, './uploads/20-1.png'),
(17, 12, './uploads/12-1.png'),
(18, 21, './uploads/21-1.png'),
(19, 13, './uploads/13-1.png'),
(20, 22, './uploads/22-1.png'),
(21, 14, './uploads/14-1.png'),
(22, 23, './uploads/23-1.png');

-- --------------------------------------------------------

--
-- Table structure for table `Notification`
--

CREATE TABLE `Notification` (
  `NotificationID` int(4) NOT NULL,
  `UserID` int(4) NOT NULL,
  `DateTime` datetime NOT NULL,
  `Message` text NOT NULL,
  `Type` text NOT NULL,
  `Read` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Sellers`
--

CREATE TABLE `Sellers` (
  `UserID` int(4) NOT NULL,
  `SellerID` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Sellers`
--

INSERT INTO `Sellers` (`UserID`, `SellerID`) VALUES
(2, 1),
(4, 2),
(6, 3),
(8, 4),
(10, 5),
(12, 6),
(14, 7),
(16, 8),
(18, 9);

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
  `Role` text NOT NULL,
  `LastLogout` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`UserID`, `Email`, `Password`, `FirstName`, `LastName`, `Role`, `LastLogout`) VALUES
(1, 'buyer1@example.com', 'buyerPass1', 'John', 'Doe', 'buyer', NULL),
(2, 'seller1@example.com', 'sellerPass1', 'Jane', 'Smith', 'seller', '2023-11-28 07:52:48'),
(3, 'buyer2@example.com', 'buyerPass2', 'Alice', 'Johnson', 'buyer', NULL),
(4, 'seller2@example.com', 'sellerPass2', 'Bob', 'Davis', 'seller', '2023-11-28 07:54:11'),
(5, 'buyer3@example.com', 'buyerPass3', 'Charlie', 'Brown', 'buyer', NULL),
(6, 'seller3@example.com', 'sellerPass3', 'Emily', 'White', 'seller', '2023-11-28 07:55:07'),
(7, 'buyer4@example.com', 'buyerPass4', 'Michael', 'Green', 'buyer', NULL),
(8, 'seller4@example.com', 'sellerPass4', 'Sarah', 'Taylor', 'seller', '2023-11-28 07:55:44'),
(9, 'buyer5@example.com', 'buyerPass5', 'Daniel', 'Martin', 'buyer', NULL),
(10, 'seller5@example.com', 'sellerPass5', 'Laura', 'Wilson', 'seller', '2023-11-28 07:56:35'),
(11, 'buyer6@example.com', 'buyerPass6', 'David', 'Clark', 'buyer', NULL),
(12, 'seller6@example.com', 'sellerPass6', 'Nancy', 'Lewis', 'seller', '2023-11-28 07:11:09'),
(13, 'buyer7@example.com', 'buyerPass7', 'Karen', 'Walker', 'buyer', NULL),
(14, 'seller7@example.com', 'sellerPass7', 'Brian', 'Hill', 'seller', '2023-11-28 07:57:20'),
(15, 'buyer8@example.com', 'buyerPass8', 'Lisa', 'Lee', 'buyer', NULL),
(16, 'seller8@example.com', 'sellerPass8', 'Kevin', 'Hall', 'seller', '2023-11-28 07:57:50'),
(17, 'buyer9@example.com', 'buyerPass9', 'Diana', 'Adams', 'buyer', NULL),
(18, 'seller9@example.com', 'sellerPass9', 'George', 'Baker', 'seller', '2023-11-28 07:58:28'),
(19, 'buyer10@example.com', 'buyerPass10', 'Angela', 'Gonzalez', 'buyer', NULL),
(20, 'admin@example.com', 'adminPass', 'Super', 'User', 'admin', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `WatchListItems`
--

CREATE TABLE `WatchListItems` (
  `BuyerID` int(11) NOT NULL,
  `ItemAuctionID` int(11) NOT NULL
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
ALTER TABLE `Categories`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `ItemImages`
--
ALTER TABLE `ItemImages`
  ADD PRIMARY KEY (`ImageID`),
  ADD KEY `FK_ItemAuctionID` (`ItemAuctionID`);

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
-- Indexes for table `WatchListItems`
--
ALTER TABLE `WatchListItems`
  ADD KEY `FK_WatchListItems_Items` (`ItemAuctionID`),
  ADD KEY `FK_WatchListItems_Buyer` (`BuyerID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `AuctionItem`
--
ALTER TABLE `AuctionItem`
  MODIFY `ItemAuctionID` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `Bid`
--
ALTER TABLE `Bid`
  MODIFY `BidID` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `Buyer`
--
ALTER TABLE `Buyer`
  MODIFY `BuyerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Categories`
--
ALTER TABLE `Categories`
  MODIFY `CategoryID` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `ItemImages`
--
ALTER TABLE `ItemImages`
  MODIFY `ImageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `Notification`
--
ALTER TABLE `Notification`
  MODIFY `NotificationID` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Sellers`
--
ALTER TABLE `Sellers`
  MODIFY `SellerID` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Transactions`
--
ALTER TABLE `Transactions`
  MODIFY `TransactionID` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `UserID` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
-- Constraints for table `ItemImages`
--
ALTER TABLE `ItemImages`
  ADD CONSTRAINT `FK_ItemAuctionID` FOREIGN KEY (`ItemAuctionID`) REFERENCES `AuctionItem` (`ItemAuctionID`) ON DELETE CASCADE,
  ADD CONSTRAINT `itemimages_ibfk_1` FOREIGN KEY (`ItemAuctionID`) REFERENCES `AuctionItem` (`ItemAuctionID`) ON DELETE CASCADE;

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
-- Constraints for table `WatchListItems`
--
ALTER TABLE `WatchListItems`
  ADD CONSTRAINT `FK_WatchListItems_Buyer` FOREIGN KEY (`BuyerID`) REFERENCES `Buyer` (`BuyerID`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_WatchListItems_Items` FOREIGN KEY (`ItemAuctionID`) REFERENCES `AuctionItem` (`ItemAuctionID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
