-- phpMyAdmin SQL Dump
-- version 4.2.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 16, 2020 at 03:31 PM
-- Server version: 5.5.58-0+deb7u1-log
-- PHP Version: 5.6.31-1~dotdeb+7.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `unn_w17004394`
--

-- --------------------------------------------------------

--
-- Table structure for table `PropertyRecovery`
--

CREATE TABLE IF NOT EXISTS `PropertyRecovery` (
  `propertyID` int(6) NOT NULL,
  `address1` varchar(32) NOT NULL,
  `address2` varchar(32) DEFAULT NULL,
  `postcode` varchar(10) NOT NULL,
  `rent` decimal(10,0) NOT NULL,
  `bills` decimal(10,0) NOT NULL,
  `landlordID` int(6) NOT NULL,
  `capacity` int(2) NOT NULL,
`recoveryID` int(6) NOT NULL,
  `description` varchar(300) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `PropertyRecovery`
--
ALTER TABLE `PropertyRecovery`
 ADD PRIMARY KEY (`recoveryID`), ADD KEY `landlordID` (`landlordID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `PropertyRecovery`
--
ALTER TABLE `PropertyRecovery`
MODIFY `recoveryID` int(6) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=179;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
