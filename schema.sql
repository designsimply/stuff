-- phpMyAdmin SQL Dump
-- version 4.1-dev
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 04, 2014 at 07:37 PM
-- Server version: 5.6.13
-- PHP Version: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `stuffdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `sf_links`
--

CREATE TABLE IF NOT EXISTS `sf_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` mediumtext,
  `cat` varchar(50) NOT NULL DEFAULT '',
  `subcat` varchar(50) DEFAULT NULL,
  `subcat1` varchar(50) DEFAULT NULL,
  `subcat2` varchar(50) DEFAULT NULL,
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `tags` tinytext NOT NULL,
  `mv` char(0) NOT NULL DEFAULT '',
  `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `sf_tag`
--

CREATE TABLE IF NOT EXISTS `sf_tag` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `sf_tagmap`
--

CREATE TABLE IF NOT EXISTS `sf_tagmap` (
  `tagmap_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `link_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`tagmap_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `sf_user`
--

CREATE TABLE IF NOT EXISTS `sf_user` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_login` varchar(64) NOT NULL DEFAULT '',
  `user_pass` varchar(64) NOT NULL DEFAULT '',
  `user_identifier` varchar(64) NOT NULL,
  `user_token` varchar(64) NOT NULL,
  `user_timeout` int(10) NOT NULL,
  `user_email` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
