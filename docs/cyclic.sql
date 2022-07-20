-- phpMyAdmin SQL Dump
-- version 5.1.4deb1+focal1
-- https://www.phpmyadmin.net/
--
-- Host: 142.93.71.17
-- Generation Time: Jul 20, 2022 at 03:54 PM
-- Server version: 8.0.29
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cyclic`
--

-- --------------------------------------------------------

--
-- Table structure for table `auction_history`
--

CREATE TABLE `auction_history` (
  `itemid` mediumint UNSIGNED NOT NULL,
  `scan_ts` int UNSIGNED NOT NULL,
  `market_value` int UNSIGNED NOT NULL,
  `min_buyout` int UNSIGNED NOT NULL,
  `quantity` mediumint UNSIGNED NOT NULL,
  `scan_time` char(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `itemid` mediumint UNSIGNED NOT NULL,
  `name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vendor_price` mediumint UNSIGNED NOT NULL,
  `rarity` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_bin NOT NULL,
  `category` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auction_history`
--
ALTER TABLE `auction_history`
  ADD PRIMARY KEY (`itemid`,`scan_ts`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`itemid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
