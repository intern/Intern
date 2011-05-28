-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 29, 2010 at 07:02 PM
-- Server version: 5.1.37
-- PHP Version: 5.2.10-2ubuntu6.4
-- lan_chi@foxmail.com
use intern;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `inter`
--

-- --------------------------------------------------------

--
-- Table structure for table `inter_cache`
--

CREATE TABLE IF NOT EXISTS `inter_cache` (
  `id` varchar(32) NOT NULL COMMENT 'primary key',
  `data` longblob NOT NULL,
  `expired` int(11) NOT NULL,
  `serialized` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inter_cache`
--


-- --------------------------------------------------------

--
-- Table structure for table `inter_core`
--

CREATE TABLE IF NOT EXISTS `inter_core` (
  `module` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `status` int(1) DEFAULT NULL,
  `version` varchar(50) DEFAULT NULL,
  `weight` int(4) DEFAULT NULL,
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inter_core`
--

INSERT INTO `inter_core` (`module`, `type`, `status`, `version`, `weight`) VALUES
('menu', 'core', 1, '1.0', 0),
('system', 'core', 1, '1.0.0', 0),
('user', 'core', 1, '0', 0);

-- --------------------------------------------------------

--
-- Table structure for table `inter_links_map`
--

CREATE TABLE IF NOT EXISTS `inter_links_map` (
  `id` varchar(255) NOT NULL,
  `link_path` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  UNIQUE KEY `link_path` (`link_path`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inter_links_map`
--


-- --------------------------------------------------------

--
-- Table structure for table `inter_options`
--

CREATE TABLE IF NOT EXISTS `inter_options` (
  `name` varchar(255) NOT NULL,
  `value` text,
  `autoload` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inter_options`
--

INSERT INTO `inter_options` (`name`, `value`, `autoload`) VALUES
('namedDD', 'b:1;', NULL),
('cache_clear_corn', 'i:1283068366;', NULL),
('cache_lifetime', 'i:300;', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inter_router_links`
--

CREATE TABLE IF NOT EXISTS `inter_router_links` (
  `link_type` varchar(255) NOT NULL,
  `router` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `parent_router` varchar(255) NOT NULL,
  `postion_type` smallint(6) NOT NULL,
  `title` varchar(255) NOT NULL,
  `options` text NOT NULL,
  `hidden` int(1) NOT NULL,
  `weight` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inter_router_links`
--

INSERT INTO `inter_router_links` (`link_type`, `router`, `module`, `parent_router`, `postion_type`, `title`, `options`, `hidden`, `weight`) VALUES
('navigation', 'admin/router', 'menu', '', 1, 'Router', 'a:1:{s:11:"description";s:0:"";}', 0, 0),
('navigation', 'admin', 'system', '', 1, 'Main', 'a:1:{s:11:"description";s:23:"Administrator Dashboard";}', 0, 0),
('navigation', 'admin/settings', 'system', '', 1, 'Settings', 'a:1:{s:11:"description";s:21:"Description here now!";}', 0, 0),
('navigation', 'admin/settings/theme', 'system', 'admin/settings', 2, 'The site theme', 'a:1:{s:11:"description";s:33:"Mange theme description here now!";}', 0, 0),
('navigation', 'user/login', 'user', '', 32767, 'Logout', 'a:1:{s:11:"description";s:21:"Description here now!";}', 0, 0),
('navigation', 'admin/settings/theme/home', 'system', 'admin/settings/theme', 4, 'The site home theme', 'a:1:{s:11:"description";s:20:"Mccccccccccccccccccc";}', 0, 0),
('navigation', 'admin/subadmin', 'system', 'admin', 2, 'sub main', 'a:1:{s:11:"description";s:23:"Administrator Dashboard";}', 0, -1),
('navigation', 'logout', 'user', '', 32767, 'Site theme', 'a:1:{s:11:"description";s:33:"Mange theme description here now!";}', 0, 0),
('navigation', 'admin/subadmin/task', 'system', 'admin/subadmin', 4, 'a tab menu', 'a:1:{s:11:"description";s:23:"Administrator Dashboard";}', 0, -1),
('navigation', 'admin/subadmin/%', 'system', 'admin/subadmin', 4, 'router test', 'a:1:{s:11:"description";s:23:"Administrator Dashboard";}', 0, -1),
('navigation', 'admin/user/register', 'user', 'admin/user', 2, 'Site theme', 'a:1:{s:11:"description";s:33:"Mange theme description here now!";}', 0, 0),
('navigation', 'admin/user', 'user', '', 1, 'Users', 'a:1:{s:11:"description";s:33:"Mange theme description here now!";}', 0, 0),
('navigation', 'admin/user/login', 'user', 'admin/user', 2, 'Logout', 'a:1:{s:11:"description";s:21:"Description here now!";}', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inter_routes`
--

CREATE TABLE IF NOT EXISTS `inter_routes` (
  `router` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `parent_router` varchar(255) NOT NULL,
  `count_parts` int(11) NOT NULL,
  `function` varchar(255) NOT NULL,
  `function_args` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_callback` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `weight` int(11) NOT NULL,
  `template` varchar(255) NOT NULL,
  `type` int(3) NOT NULL,
  PRIMARY KEY (`router`),
  UNIQUE KEY `router` (`router`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inter_routes`
--

INSERT INTO `inter_routes` (`router`, `module`, `parent_router`, `count_parts`, `function`, `function_args`, `title`, `title_callback`, `description`, `weight`, `template`, `type`) VALUES
('admin/router', 'menu', '', 2, 'api', '', 'Router', '', '', 0, '', 1),
('admin', 'system', '', 1, '', '', 'Main', '__', 'Administrator Dashboard', -1, 'master/modules/system/system.admin.php', 1),
('admin/subadmin', 'system', 'admin', 2, '', '', 'sub main', '__', 'Administrator Dashboard', -1, 'master/modules/system/system.admin.php', 2),
('admin/subadmin/task', 'system', 'admin/subadmin', 3, 'function_task', '', 'a tab menu', '__', 'Administrator Dashboard', -1, '', 4),
('admin/subadmin/%', 'system', 'admin/subadmin', 3, '', '', 'router test', '__', 'Administrator Dashboard', -1, 'master/modules/system/system.admin.php', 4),
('admin/settings', 'system', '', 2, '', '', 'Settings', '', 'Description here now!', 0, 'master/modules/system/system.admin.php', 1),
('admin/settings/theme', 'system', 'admin/settings', 3, '', '', 'The site theme', '', 'Mange theme description here now!', 0, 'master/modules/system/system.admin.php', 2),
('admin/settings/theme/home', 'system', 'admin/settings/theme', 4, '', '', 'The site home theme', '', 'Mccccccccccccccccccc', 0, 'master/modules/system/system.admin.php', 4),
('admin/user/login', 'user', 'admin/user', 3, 'callback_function', '', 'Logout', '', 'Description here now!', 0, '', 2),
('logout', 'user', '', 1, 'user_logout', '', 'Site theme', '', 'Mange theme description here now!', 0, '', 65536),
('admin/user/register', 'user', 'admin/user', 3, 'callback_function', '', 'Site theme', '', 'Mange theme description here now!', 0, '', 2),
('admin/user', 'user', '', 2, 'callback_function', '', 'Users', '', 'Mange theme description here now!', 0, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `inter_routes_permission`
--

CREATE TABLE IF NOT EXISTS `inter_routes_permission` (
  `router` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `role_ids` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inter_routes_permission`
--

INSERT INTO `inter_routes_permission` (`router`, `description`, `role_ids`) VALUES
('admin/router', 'aaaaaaaaaaaaaaaaaaaaaa', ''),
('admin', 'Admin Dashboard View', ''),
('admin/settings', 'Site settings', ''),
('admin/settings/theme', '', ''),
('admin/router', 'aaaaaaaaaaaaaaaaaaaaaa', ''),
('admin', 'Admin Dashboard View', ''),
('admin/settings', 'Site settings', ''),
('admin/settings/theme', '', ''),
('', 'admin/user', ''),
('', 'admin/user', ''),
('', 'admin/user', ''),
('', 'admin/user', '');

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
(1, '4ce2671724d677e72ff8788c4dce5c36', '127.0.0.1', 1280655609, 0, 'name|a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}y|a:3:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";}'),
(0, '8475857f3903f1d5de0f49f8db6430b7', '127.0.0.1', 1278769834, 0, 'eeee|s:24:"session[!@#$wer@#%@#^@D]";'),
(1, '5fc60035e71366f11acc82e2e99d958f', '127.0.0.1', 1281104181, 0, ''),
(1, '8e29fee95989e4aa46659dfde5f7c843', '127.0.0.1', 1281104183, 0, ''),
(1, 'dda4998eeb152a751ed632f357475c76', '127.0.0.1', 1281104186, 0, ''),
(1, '633ee4be5d5fa8a1d7e615429fef87fc', '127.0.0.1', 1281104187, 0, ''),
(1, '6804f19fe7cc7fbeb24e181e3a757004', '127.0.0.1', 1281104188, 0, ''),
(1, 'b6e3f2e73935a81347a886fc188950df', '127.0.0.1', 1281104189, 0, ''),
(1, '0acc56ae8a78919ce5fb00b1911034a7', '127.0.0.1', 1281104189, 0, ''),
(1, '8d44d7cef68221c490b8592662086096', '127.0.0.1', 1281104193, 0, ''),
(1, 'fdc462321b7ac3284bae91a5c801a6d7', '127.0.0.1', 1281104193, 0, ''),
(1, '7d1abcdf83646f0f2adfcf2d9a9f8e0d', '127.0.0.1', 1281104194, 0, ''),
(1, '72840d0ec567abc868796e0a21ebb837', '127.0.0.1', 1281104195, 0, ''),
(1, 'a743357a1140eb85e8b1d8325b29e4dc', '127.0.0.1', 1281104197, 0, ''),
(1, 'e22aebb9c0f6e735716fca836a6bebd0', '127.0.0.1', 1281104197, 0, ''),
(1, '8dcaf5337d85b173d9c4c341ce762ecc', '127.0.0.1', 1281104198, 0, ''),
(1, '56fd70bc5e9639fbcdf80b1d9cf984a1', '127.0.0.1', 1281104199, 0, ''),
(1, '40c1625b900acd3d97be6ce159f3f920', '127.0.0.1', 1281104199, 0, ''),
(1, '856dddef63b0d36f5e974e395a0c3c3c', '127.0.0.1', 1281104200, 0, ''),
(1, 'd637f4a896179fcb491c89ce8f0a77b3', '127.0.0.1', 1281104200, 0, ''),
(1, 'b8d0e3d761ae3d6aef66ac558f20d936', '127.0.0.1', 1281104201, 0, ''),
(1, '593bbc5a177ed6f22074cea21eb587a1', '127.0.0.1', 1281104201, 0, ''),
(1, 'f047bc69ffdfd278aecf83c2c1d0350a', '127.0.0.1', 1281104202, 0, ''),
(1, '761f199d2af56173da1d0b4e84624681', '127.0.0.1', 1281104202, 0, ''),
(1, 'e04a2a1f8255eacd2631beafed388bb9', '127.0.0.1', 1281104202, 0, ''),
(1, '1b2398dc070da341dd1895fc51be81fe', '127.0.0.1', 1281104203, 0, ''),
(1, '4d43c2c78fe9eb9a30a3b9d0fd379ffa', '127.0.0.1', 1281104203, 0, ''),
(1, 'ba303e9775bc0015914415800b29e77b', '127.0.0.1', 1281104204, 0, ''),
(1, '9fa6a010b4ed2086b10d724c4b241f42', '127.0.0.1', 1281104205, 0, ''),
(1, '44b89e7720d362e73cd75759b0387c72', '127.0.0.1', 1281104205, 0, ''),
(1, '4055f8c9aec4b51259b5c822c9d2de15', '127.0.0.1', 1281104288, 0, ''),
(1, 'ce073ac52e13ad62b9e8df60b399a27f', '127.0.0.1', 1281104289, 0, ''),
(1, '9a439bdb28f9f1fec32e9e2a80535ac5', '127.0.0.1', 1281104521, 0, ''),
(1, '7ad43eff5779799020339572923a57db', '127.0.0.1', 1281104522, 0, ''),
(1, '7828e5c375794ddf36b81041e0132d49', '127.0.0.1', 1281104522, 0, ''),
(1, 'a7fdee3ddff221ae116b373de51baceb', '127.0.0.1', 1281104522, 0, ''),
(1, '9fe766f8247e968e63f5ff12413e2244', '127.0.0.1', 1281104523, 0, ''),
(1, '73ac348ff0990c47b1737fabb5649633', '127.0.0.1', 1281108778, 0, ''),
(1, 'edb9b4834b949ecb2a853036653d1048', '127.0.0.1', 1281108782, 0, ''),
(1, '20a2deea72966fe09146eab8a807dcfe', '127.0.0.1', 1281108823, 0, ''),
(1, 'd1c36521ab1dee6206969108043b4feb', '127.0.0.1', 1281108853, 0, ''),
(1, 'b9b0a06a592b4f162930d4e61c14b8fa', '127.0.0.1', 1281135129, 0, ''),
(1, 'cb7a64df855197081dce654da5fc7120', '127.0.0.1', 1281135174, 0, ''),
(1, '51df57373b1e398601cc9c86df334809', '127.0.0.1', 1281135175, 0, ''),
(1, '9f306707e0c911f88c660ce8cf0b1dc1', '127.0.0.1', 1281135175, 0, ''),
(1, '1451e618e69e34f7b1627b4fd397a908', '127.0.0.1', 1281135176, 0, ''),
(1, 'b0e19b333135debf5cc3d31f7e558008', '127.0.0.1', 1281135176, 0, ''),
(1, '6392544f42773ae6b14dfbbbf8cb0ca4', '127.0.0.1', 1281135177, 0, ''),
(1, '2e4c3dbdfde26c059c38f18dd9f334ec', '127.0.0.1', 1281135177, 0, ''),
(1, 'eabbba1d37c9fd1b22b14dc0fd94eb12', '127.0.0.1', 1281135178, 0, ''),
(1, 'a6439320712f0be2806576bcdd3c66a7', '127.0.0.1', 1281135178, 0, ''),
(1, '9f23336d0262a628ede1bde757da9ab7', '127.0.0.1', 1281135179, 0, ''),
(1, '6d8453dc41a457d7c13a5393a84c9905', '127.0.0.1', 1281135180, 0, ''),
(1, 'c04f28ff3e8853cd07ea436aafc1283d', '127.0.0.1', 1281135209, 0, ''),
(1, '4fc3a9b3cfeb9633f1d2b2dd41943b26', '127.0.0.1', 1281135342, 0, ''),
(1, '53f3f0f1ce22cf60f70aac96162625b7', '127.0.0.1', 1281135350, 0, ''),
(1, '96d32cf75feac178bc93e446be1fa953', '127.0.0.1', 1281135406, 0, ''),
(1, 'a5d2a1843160d344b6be62326520116d', '127.0.0.1', 1281135407, 0, ''),
(1, 'f8d4524610a55d49c048ec6a3e10e05e', '127.0.0.1', 1281135430, 0, ''),
(1, 'fa0738b36b9b67417f106cb55b2b1da7', '127.0.0.1', 1281135431, 0, ''),
(1, 'a5d853c3a785dae5911cd86188c3f599', '127.0.0.1', 1281135453, 0, ''),
(1, 'b6e0ae9bcd1844c52bbb58fa1ec053ec', '127.0.0.1', 1281135497, 0, ''),
(1, '7ef8ce68d8620f375212341e0292f192', '127.0.0.1', 1281135559, 0, ''),
(1, 'fd7e0f1f8f762c4351163b4d689ebea6', '127.0.0.1', 1281135560, 0, ''),
(1, 'c5055b53af8dd0d89ee08d5c7d5360ca', '127.0.0.1', 1281135576, 0, ''),
(1, 'ce36f1e827b74c9a60320cf3cab6e7be', '127.0.0.1', 1281139080, 0, ''),
(1, '8746e3929bdeeae6d376a75b6258540d', '127.0.0.1', 1281140810, 0, ''),
(1, '2c579eb25624fdab67a24b6da016861f', '127.0.0.1', 1281140811, 0, ''),
(1, 'adff506098e1053e73e46cc32fdfc962', '127.0.0.1', 1281140811, 0, ''),
(1, '27fd53663c49c7644fd0b8f314bac388', '127.0.0.1', 1281141524, 0, ''),
(1, 'd63703d0c88fba09f0ce04cfbf61920b', '127.0.0.1', 1281141525, 0, ''),
(1, '849ba80ae9068abcca031f80210d6976', '127.0.0.1', 1281141525, 0, ''),
(1, 'aabcc615d761954f572913cecf2c6a2e', '127.0.0.1', 1281141525, 0, ''),
(1, 'a42b27c941a44872e07c64b9768e7326', '127.0.0.1', 1281264634, 0, ''),
(1, '9dbe8155be37e6d02c45baf09e681ff8', '127.0.0.1', 1281265583, 0, ''),
(1, '1d368b504400971e13a0970f7ae18a2a', '127.0.0.1', 1281265585, 0, ''),
(1, '5f5829df69af18993e90eff4ba44d751', '127.0.0.1', 1281265595, 0, ''),
(1, '492986bda31189f509227c17d38fcc61', '127.0.0.1', 1281265651, 0, ''),
(1, 'f9bd4d085ebbacfa1ecc8b8778101e08', '127.0.0.1', 1281266277, 0, ''),
(1, 'f077d400c212fdd904cc8f9ee69171d7', '127.0.0.1', 1281266286, 0, ''),
(1, '433461e67470fb61e3367ea0b841e727', '127.0.0.1', 1281266298, 0, ''),
(1, '5cb0649097ec181b5a93bd611e8677ec', '127.0.0.1', 1281266317, 0, ''),
(1, '9724e3c61b4e4314a1baf3ea0cb82777', '127.0.0.1', 1281266336, 0, ''),
(1, '6ac80316828191187635cad21eb8c0a1', '127.0.0.1', 1281266346, 0, ''),
(1, '0686255b0c991dc382269952b2dc2c21', '127.0.0.1', 1281266376, 0, ''),
(1, 'd5d17a5b215a18ec99ffe2a90dcd9309', '127.0.0.1', 1281266391, 0, ''),
(1, 'b43790bad6cf3ee096b7d4d1c830f602', '127.0.0.1', 1281266397, 0, ''),
(1, '1f6060b84c0fba9cb62aa1746ad22342', '127.0.0.1', 1281359693, 0, ''),
(1, '09e20dac8710eb7e7c0070e4446db5a0', '127.0.0.1', 1281359710, 0, ''),
(1, '0f6048b5f7e9cb57328ba7bb4262754c', '127.0.0.1', 1281359739, 0, ''),
(1, 'e214fa5517bf9b6731ed55b4689bcee6', '127.0.0.1', 1281360210, 0, ''),
(1, '73281099399dfc8aaf15c76d9d1a2291', '127.0.0.1', 1281360225, 0, ''),
(1, '1f807098393f816a0d0ac738e7d57767', '127.0.0.1', 1281360236, 0, ''),
(1, '6e441bdea31a0c77498f15453fb1909f', '127.0.0.1', 1281360311, 0, ''),
(1, '353db55cb947a46f14e7bea4a51bfc9b', '127.0.0.1', 1281360312, 0, ''),
(1, '9675aa409bef8b0aef3502819a34529d', '127.0.0.1', 1281360313, 0, ''),
(1, '079b62c6377ef3b596886ac161138855', '127.0.0.1', 1281360313, 0, ''),
(1, 'efdef2e658e5971a82ee72b2ac4b66a8', '127.0.0.1', 1281360313, 0, ''),
(1, '826a7a0e900d35957f76d468b8e0704d', '127.0.0.1', 1281360314, 0, ''),
(1, '59b85563a20305d3485db87bc4bdb815', '127.0.0.1', 1281360314, 0, ''),
(1, '060742270789656906d815626f719dfb', '127.0.0.1', 1281360934, 0, ''),
(1, '14e210a65607c9416cda5d04a1001bcf', '127.0.0.1', 1281360969, 0, ''),
(1, '151ff1745c4a68566bff982493d93490', '127.0.0.1', 1281360977, 0, ''),
(1, '312050dd11a29b884063ad2f8d4f12d6', '127.0.0.1', 1281360981, 0, ''),
(1, '105785f54570a985012f8122d1b1562f', '127.0.0.1', 1281361104, 0, ''),
(1, '2bd175aa1c930c982c908d2e37b9cd18', '127.0.0.1', 1281361270, 0, ''),
(1, '304660a0a3e1f37b92065d54752c774e', '127.0.0.1', 1281361271, 0, ''),
(1, 'c55fc4b88c8436142695eb8f5d4ea5ad', '127.0.0.1', 1281361276, 0, ''),
(1, 'dfe1d27a7bbb44f3a2050b6d0f95ab81', '127.0.0.1', 1281708786, 0, ''),
(1, 'ebe6ed3850fec2fbe77c6cd51f226735', '127.0.0.1', 1281708851, 0, ''),
(1, '08baa9f3854d15ef03b175d555ef1e86', '127.0.0.1', 1281708852, 0, ''),
(1, '028971291f7d6b497036158e27b19f26', '127.0.0.1', 1281708864, 0, ''),
(1, 'dae4e96d8d4da40a70f50f67015d8d6f', '127.0.0.1', 1281708943, 0, ''),
(1, '7ca948f86137de0c215a3eebcc3640d6', '127.0.0.1', 1281708953, 0, ''),
(1, '450bf853fcd804f7d1fed8bb3647c5b5', '127.0.0.1', 1281708954, 0, ''),
(1, '09db91512cb7a53ca442be51ba714cf8', '127.0.0.1', 1281709056, 0, ''),
(1, '0ed92db70e0b1f1f157634d67ced25ff', '127.0.0.1', 1281709100, 0, ''),
(1, '0e4c0852973c4f9805bac1aecd0e498e', '127.0.0.1', 1281709131, 0, ''),
(1, '7cc29c4286d65337e446572d5cf5be79', '127.0.0.1', 1281709154, 0, ''),
(1, '42aaaa6e681ae65586bcb3f929422836', '127.0.0.1', 1281709172, 0, ''),
(1, 'de0870fe4fc60c249b631122184e83e4', '127.0.0.1', 1281709185, 0, ''),
(1, 'afaad0fb52d804d2b8a2c7542a88b94b', '127.0.0.1', 1281709225, 0, ''),
(1, '42b3fdb87815618b4d6ea84bd52a57c8', '127.0.0.1', 1281709268, 0, ''),
(1, 'e9b7adbaa7a70e6b08b71fc6b9d0f617', '127.0.0.1', 1281709291, 0, ''),
(1, '6115723f30b8f73b9e5b60668d8dbc2f', '127.0.0.1', 1281709307, 0, ''),
(1, 'b48bab5c232849ff2d6eaad189362687', '127.0.0.1', 1281709307, 0, ''),
(1, '09bf35c67d831d656a8012682f45f6c0', '127.0.0.1', 1281709331, 0, ''),
(1, '9a305c482b9cde4cbb00294b684fac09', '127.0.0.1', 1281709355, 0, ''),
(1, '8e4e35d254a84f8b5a5b6ed5c3e93370', '127.0.0.1', 1281709388, 0, ''),
(1, '0a69935be4bebb0d944e829cdda26b07', '127.0.0.1', 1281709401, 0, ''),
(1, '05f6225ce5bb57b4a7cee69041df88c3', '127.0.0.1', 1281709438, 0, ''),
(1, '39e97f31fbfe040bc7f1105618b3c0b6', '127.0.0.1', 1281709450, 0, ''),
(1, '2c593019b7507907a1cedb6b609a6c51', '127.0.0.1', 1281709492, 0, ''),
(1, 'ae4df618538bd9d63198c402ba14f974', '127.0.0.1', 1281709512, 0, ''),
(1, '60cc537d76a2f7b96524a75c13686ba9', '127.0.0.1', 1281709604, 0, ''),
(1, '0e0a96c5063d5533cdc2a53b075676f5', '127.0.0.1', 1281709606, 0, ''),
(1, 'f1945ac52f75b70810f4f4b42aa2415a', '127.0.0.1', 1281709616, 0, ''),
(1, '6e6c9420f3fe1427bb2e22aca8660701', '127.0.0.1', 1281709643, 0, ''),
(1, '036df47b939d51610bc6a84c015f8996', '127.0.0.1', 1281709655, 0, ''),
(1, 'be36d5ba7e7c89cc1bf9bac617333ce4', '127.0.0.1', 1281709656, 0, ''),
(1, '14600332342225ecce0a7f53f26866dc', '127.0.0.1', 1281709696, 0, ''),
(1, '90550995f16af139a3fdf9cc13733a8c', '127.0.0.1', 1281710439, 0, ''),
(1, '7fad93d77e6c2db29f9637a2ed84f594', '127.0.0.1', 1281710465, 0, ''),
(1, 'b137929f1495f291ff8e5970ed75e421', '127.0.0.1', 1281710489, 0, ''),
(1, 'c624d07beb4f481d0fa9c676a2aa7f53', '127.0.0.1', 1281710490, 0, ''),
(1, 'b02615d3d24fbfb3cb9b20c73f326e34', '127.0.0.1', 1281710521, 0, ''),
(1, '052b6a9b2f54def90f28699112edba65', '127.0.0.1', 1281710531, 0, ''),
(1, '5e8f451d63b2a39e34db2ce4f919c48c', '127.0.0.1', 1281710537, 0, ''),
(1, '6f906a7dbc0618d53835f50a67da0eaa', '127.0.0.1', 1281710538, 0, ''),
(1, '49bdfd1856d80699d24574d002784b5a', '127.0.0.1', 1281710565, 0, ''),
(1, '65662f8dcac606a82644450904f04381', '127.0.0.1', 1281710566, 0, ''),
(1, 'a8bbdcf38e047197e1232014cbe2f1ce', '127.0.0.1', 1281710585, 0, ''),
(1, '78c4435ec2de3c8db4d143e9ccd0a32b', '127.0.0.1', 1281710605, 0, ''),
(1, '6c7fd405d70787e866306060c12afe53', '127.0.0.1', 1281710610, 0, ''),
(1, '6283e332e42aef2f6590a2ed05e87985', '127.0.0.1', 1281710610, 0, ''),
(1, '0c56f6215a0cfba625950f1b1d7a9ff4', '127.0.0.1', 1281710621, 0, ''),
(1, 'da57e6762f861ddb455f922f130296cf', '127.0.0.1', 1281710622, 0, ''),
(1, 'e76d1ea688bdd1040a15e42556bd24cc', '127.0.0.1', 1281710622, 0, ''),
(1, 'af61a3e7b3b4267b7f9c0e27748e4f52', '127.0.0.1', 1281710631, 0, ''),
(1, '1e3282fb856d91563b123b3ba8d82138', '127.0.0.1', 1281710648, 0, ''),
(1, 'a1d0b3c47886e3c324279fef16b78145', '127.0.0.1', 1281710649, 0, ''),
(1, 'c59376ec900f4f703ab89d68bd292929', '127.0.0.1', 1281710669, 0, ''),
(1, '9c485e1003841b397c0273a4efea8692', '127.0.0.1', 1281710669, 0, ''),
(1, 'd8f3b7feda3efa79a709497a0aab9eb9', '127.0.0.1', 1281710670, 0, ''),
(1, '520172ff8fa9d5e753bd96c1c779fd5b', '127.0.0.1', 1281710670, 0, ''),
(1, '40bec7cc9ef710f3f0bd5fb8a98e85ec', '127.0.0.1', 1281710670, 0, ''),
(1, '4a2b417ceb1ebf4c6c425a044c3a2cb3', '127.0.0.1', 1281710717, 0, ''),
(1, '159f562fb9d1587bc16c5dbefa826c43', '127.0.0.1', 1281710718, 0, ''),
(1, '1e25e9bc44b7d511c6a49fa28c1c832e', '127.0.0.1', 1281710718, 0, ''),
(1, '1d8fe9819a29736d5986555c90be3bdf', '127.0.0.1', 1281710719, 0, ''),
(1, '027c093daf2b3b7d04eba50dbfe4fa64', '127.0.0.1', 1281710724, 0, ''),
(1, '3632953c66a38a1fbcd7f6a034a45686', '127.0.0.1', 1281710725, 0, ''),
(1, '68a7549fa0531a4a4e46d66cd8248138', '127.0.0.1', 1281710725, 0, ''),
(1, '9df6afd1876763f8bfa11fc1d57f5465', '127.0.0.1', 1281710725, 0, ''),
(1, '7cd4f182e0a7a51294344fcab4398590', '127.0.0.1', 1281710743, 0, ''),
(1, '43f37a4d84068313a88b750cdf12435d', '127.0.0.1', 1281710744, 0, ''),
(1, '01023637c38e624257cf4f35628481b2', '127.0.0.1', 1281713154, 0, ''),
(1, '3de44052248d5dd2e27f934aaa2be035', '127.0.0.1', 1281713170, 0, ''),
(1, 'c90dc90239084ec990580b16b6ee5c22', '127.0.0.1', 1281713551, 0, ''),
(1, '8596c1309d5f1fa1f288fa21b8e624de', '127.0.0.1', 1281741978, 0, ''),
(1, 'cbdff073782972dd3e20bc00a7261617', '127.0.0.1', 1281742243, 0, ''),
(1, '8f41080c2c56ff9929cdbed103ac468b', '127.0.0.1', 1281742431, 0, ''),
(1, '7ff355e8a67be4fae62c60213dbc72cf', '127.0.0.1', 1281742463, 0, ''),
(1, 'f8a16ecbe379b878ee8002fc22764816', '127.0.0.1', 1281742469, 0, ''),
(1, '3285eacf52084251052505225db78429', '127.0.0.1', 1281742514, 0, ''),
(1, '6207992624f14dcb9d39a465d762987e', '127.0.0.1', 1281745968, 0, ''),
(1, 'f0fe84b9aa6f2b3f2b71771772ee40f4', '127.0.0.1', 1283053065, 0, ''),
(1, '57f44c2db581ca783e55762d4b76becf', '127.0.0.1', 1283068368, 0, ''),
(1, '9744e5fad5f8ea6c3de6c42dc9202932', '192.168.1.9', 1283054586, 0, ''),
(1, '0ec41539345a5e404e5271d6d5d2f3e9', '192.168.1.6', 1283053777, 0, ''),
(1, '73b8c26d67205c44416eebd7bcc94737', '192.168.1.9', 1283068478, 0, ''),
(1, '29f22ac6710ba86fd1eb8e612f6425cc', '192.168.1.9', 1283068543, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `inter_url_alias`
--

CREATE TABLE IF NOT EXISTS `inter_url_alias` (
  `path` varchar(255) NOT NULL,
  `path_alias` varchar(255) NOT NULL,
  `cached` int(11) NOT NULL,
  PRIMARY KEY (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inter_url_alias`
--


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
