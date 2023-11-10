-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 05, 2023 at 08:26 PM
-- Server version: 5.7.39
-- PHP Version: 7.4.33
-- helllo
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

-- --------------------------------------------------------

-- avatest
-- Table structure for table `AuctionItem`

CREATE TABLE `AuctionItem` (
  `ItemAuctionID` varchar(4) NOT NULL,
  `UserID` varchar(4) NOT NULL,
  `CategoryID` varchar(4) NOT NULL,
  `Description` text NOT NULL,
  `StartingPrice` int(11) NOT NULL,
  `ReservePrice` int(11) NOT NULL,
  `EndDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Dumping data for table `AuctionItem`
--

INSERT INTO `AuctionItem` (`ItemAuctionID`, `UserID`, `CategoryID`, `Description`, `StartingPrice`, `ReservePrice`, `EndDate`) VALUES
('1234', 3561, '4651', 'Red Hat', 5, 20, '2023-11-05 19:18:20'),
('1286', 3971, '4752', 'Skateboard', 35, 45, '2023-11-03 10:29:07'),
('1342', 3896, '4532', 'Bike', 30, 40, '2023-11-05 19:18:20'),
('1382', 3296, '4541', 'Skateboard', 30, 45, '2023-11-03 10:43:28'),
('1534', 3571, '4951', 'Scarf', 5, 15, '2023-11-01 07:23:58');

-- --------------------------------------------------------

--
-- Table structure for table `Bid`
--

CREATE TABLE `Bid` (
  `BidID` varchar(4) NOT NULL,
  `UserID` varchar(4) NOT NULL,
  `ItemAuctionID` varchar(4) NOT NULL,
  `BidTime` time NOT NULL,
  `BidAmount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Categories`
--

CREATE TABLE `Categories` (
  `CategoryID` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Notification`
--

CREATE TABLE `Notification` (
  `NotificationID` varchar(4) NOT NULL,
  `UserID` varchar(4) NOT NULL,
  `DateTime` datetime NOT NULL,
  `Message` text NOT NULL,
  `Type` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Transactions`
--

CREATE TABLE `Transactions` (
  `TransactionID` varchar(4) NOT NULL,
  `SellerID` varchar(4) NOT NULL,
  `ItemID` varchar(4) NOT NULL,
  `BidID` varchar(4) NOT NULL,
  `BuyerID` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `UserID` varchar(4) NOT NULL,
  `Email` text NOT NULL,
  `Password` text NOT NULL,
  `FirstName` text NOT NULL,
  `LastName` text NOT NULL,
  `Role` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`UserID`, `Email`, `Password`, `FirstName`, `LastName`, `Role`) VALUES
('3296', 'avaiscool@gmail.com', '123abc', 'Ava', 'Forever', 'Seller'),
('3561', 'avalovescoding@yahoo.co.uk', 'energyultra', 'Jess', 'Fun', 'Buyer'),
('3571', 'avalovescats@gmail.com', 'beastmode123', 'Gaorren', 'Yay', 'Seller'),
('3896', 'coolkidsusesql@gmail.com', 'redbullol999', 'Jonny', 'Slays', 'Buyer'),
('3971', 'iamhungryandtired@gmail.com', 'ETphonehome', 'Luna', 'Mika', 'Buyer');

-- --------------------------------------------------------

--
-- Table structure for table `Watchlist`
--

CREATE TABLE `Watchlist` (
  `WatchlistID` varchar(4) NOT NULL,
  `UserID` varchar(4) NOT NULL,
  `ItemAuctionID` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
---- Dumping data for table `Watchlist`
--

INSERT INTO `Watchlist` (`WatchlistID`, `UserID`, `ItemAuctionID`) VALUES
('5676', '3296', '8900'),
('5781', '3561', '8901'),
('5661', '3571', '8902'),
('5923', '3896', '8903'),
('5296', '3971', '8904');
-- Indexes for dumped tables
--
--
-- Indexes for dumped tables
--

--
-- Indexes for table `AuctionItem`
--
--
ALTER TABLE `AuctionItem`
  ADD PRIMARY KEY (`ItemAuctionID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `Bid`
--
ALTER TABLE `Bid`
  ADD PRIMARY KEY (`BidID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `Categories`
--
ALTER TABLE `Categories`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `Notification`
--
ALTER TABLE `Notification`
  ADD PRIMARY KEY (`NotificationID`);

--
-- Indexes for table `Transactions`
--
ALTER TABLE `Transactions`
  ADD PRIMARY KEY (`TransactionID`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`UserID`);
  MODIFY UserID INT AUTO_INCREMENT PRIMARY KEY;

--
-- Indexes for table `Watchlist`
--
ALTER TABLE `Watchlist`
  ADD PRIMARY KEY (`WatchlistID`);
--
-- Constraints for dumped tables
--
--
-- Constraints for table `Transactions`
--
ALTER TABLE `AuctionItem`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `auctionitem_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`);
--
-- Constraints for table `AuctionItem`
ALTER TABLE `AuctionItem`
  ADD CONSTRAINT `auctionitem_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`);
-- Notice the semicolon at the end of the statement above.

-- Constraints for table `Watchlist`
ALTER TABLE `Watchlist`
  ADD CONSTRAINT `watchlist_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `watchlist_ibfk_2` FOREIGN KEY (`ItemAuctionID`) REFERENCES `AuctionItem` (`ItemAuctionID`);
-- Notice that `AuctionItemID` has been corrected to `ItemAuctionID` to match the column name.
-- Also, note that if this is intended to be part of a single transaction or script, you may need to end with a semicolon here as well.


  -- Constraints for table `Notification`
--
ALTER TABLE `Notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`);

-- Constraints for table `Bid`
--
ALTER TABLE `Bid`
  ADD CONSTRAINT `bid_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `bid_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `bid_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `bid_ibfk_4` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `bid_ibfk_5` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `bid_ibfk_6` FOREIGN KEY (`ItemAuctionID`) REFERENCES `AuctionItem` (`AuctionItemID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;