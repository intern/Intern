-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 18, 2010 at 07:26 AM
-- Server version: 5.1.37
-- PHP Version: 5.2.10-2ubuntu6.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `inter`
--

-- --------------------------------------------------------

--
-- Table structure for table `inter_options`
--

CREATE TABLE IF NOT EXISTS `inter_options` (
  `name` varchar(255) NOT NULL,
  `value` text,
  `description` text,
  `autoload` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inter_options`
--

INSERT INTO `inter_options` (`name`, `value`, `description`, `autoload`) VALUES
('namedDD', 'b:1;', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inter_sessions`
--

CREATE TABLE IF NOT EXISTS `inter_sessions` (
  `uid` int(10) unsigned NOT NULL,
  `sid` varchar(64) NOT NULL DEFAULT '',
  `hostname` varchar(128) NOT NULL DEFAULT '',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `cache` int(11) NOT NULL DEFAULT '0',
  `data` longtext,
  PRIMARY KEY (`sid`),
  KEY `timestamp` (`timestamp`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inter_sessions`
--

INSERT INTO `inter_sessions` (`uid`, `sid`, `hostname`, `timestamp`, `cache`, `data`) VALUES
(1, '4ce2671724d677e72ff8788c4dce5c36', '127.0.0.1', 1279408667, 0, 'name|a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}y|a:3:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";}'),
(0, '8475857f3903f1d5de0f49f8db6430b7', '127.0.0.1', 1278769834, 0, 'eeee|s:24:"session[!@#$wer@#%@#^@D]";');

-- --------------------------------------------------------

--
-- Table structure for table `inter_users`
--

CREATE TABLE IF NOT EXISTS `inter_users` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL,
  `logincount` smallint(6) NOT NULL DEFAULT '0',
  `loginip` varchar(16) NOT NULL DEFAULT '',
  `logintime` int(10) NOT NULL DEFAULT '0',
  `regip` varchar(16) NOT NULL,
  `data` text,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `inter_users`
--

INSERT INTO `inter_users` (`uid`, `username`, `email`, `password`, `status`, `logincount`, `loginip`, `logintime`, `regip`, `data`) VALUES
(1, 'admin', 'lan_chi@qq.com', 'e10adc3949ba59abbe56e057f20f883e', 1, 1, '192.168.1.9', 0, '192.168.1.9', '');




