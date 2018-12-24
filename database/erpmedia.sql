-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 24, 2018 at 01:38 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `erpmedia`
--

-- --------------------------------------------------------

--
-- Table structure for table `adm_company`
--

CREATE TABLE `adm_company` (
  `id_adm_company` bigint(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `address` longtext,
  `city` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `adm_company`
--

INSERT INTO `adm_company` (`id_adm_company`, `name`, `address`, `city`) VALUES
(1, 'PT XYZ', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `adm_group`
--

CREATE TABLE `adm_group` (
  `id_adm_group` bigint(11) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `group_level` smallint(6) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `adm_group`
--

INSERT INTO `adm_group` (`id_adm_group`, `code`, `name`, `active`, `group_level`) VALUES
(1, 'ADMIN', 'Administrator', 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `adm_group_menu`
--

CREATE TABLE `adm_group_menu` (
  `id_adm_group` bigint(11) NOT NULL,
  `id_adm_menu` bigint(11) NOT NULL,
  `view` int(11) DEFAULT '0',
  `edit` int(11) DEFAULT '0',
  `delete` int(11) DEFAULT '0',
  `approve` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `adm_group_menu`
--

INSERT INTO `adm_group_menu` (`id_adm_group`, `id_adm_menu`, `view`, `edit`, `delete`, `approve`) VALUES
(1, 1, 1, 1, 1, 1),
(1, 2, 1, 1, 1, 1),
(1, 3, 1, 1, 1, 1),
(1, 4, 1, 1, 1, 1),
(1, 5, 1, 1, 1, 1),
(1, 6, 1, 1, 1, 1),
(1, 7, 1, 1, 1, 1),
(1, 8, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `adm_log`
--

CREATE TABLE `adm_log` (
  `log_id` bigint(20) NOT NULL,
  `log_name` varchar(100) NOT NULL,
  `log_time` datetime NOT NULL,
  `log_status` varchar(50) NOT NULL,
  `log_desc` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `adm_log`
--

INSERT INTO `adm_log` (`log_id`, `log_name`, `log_time`, `log_status`, `log_desc`) VALUES
(1, 'LOGGED_IN', '2018-12-23 16:39:07', 'admin', 'a:4:{s:5:\"creds\";a:3:{s:8:\"username\";s:5:\"admin\";s:8:\"password\";s:32:\"210d75e3516c7401f167f48237445403\";s:8:\"remember\";s:0:\"\";}s:6:\"member\";O:8:\"stdClass\":30:{s:2:\"id\";s:1:\"1\";s:8:\"username\";s:5:\"admin\";s:8:\"password\";s:88:\"xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==\";s:12:\"password_pin\";s:88:\"xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==\";s:4:\"name\";s:13:\"ADMINISTRATOR\";s:5:\"email\";s:21:\"admin@siakadmedia.com\";s:12:\"id_adm_group\";s:1:\"1\";s:14:\"id_adm_company\";s:1:\"1\";s:21:\"default_id_adm_module\";s:1:\"1\";s:4:\"type\";s:1:\"1\";s:6:\"status\";s:1:\"1\";s:10:\"last_login\";s:19:\"2018-12-23 15:47:43\";s:7:\"address\";s:22:\"JL. TOMANG RAYA NO.123\";s:4:\"city\";s:3:\"162\";s:8:\"district\";s:13:\"JAKARTA PUSAT\";s:8:\"province\";s:2:\"12\";s:5:\"phone\";s:11:\"08123465789\";s:5:\"bbpin\";s:8:\"98765432\";s:4:\"bank\";s:1:\"1\";s:4:\"bill\";s:8:\"98765432\";s:9:\"bill_name\";s:13:\"ADMINISTRATOR\";s:6:\"branch\";s:13:\"JAKARTA PUSAT\";s:6:\"idcard\";s:16:\"3210210620160006\";s:10:\"uniquecode\";s:3:\"999\";s:7:\"nominal\";s:1:\"0\";s:11:\"as_stockist\";s:1:\"0\";s:6:\"gender\";s:4:\"male\";s:6:\"avatar\";s:11:\"avatar1.png\";s:11:\"datecreated\";s:19:\"2016-07-23 00:00:00\";s:12:\"datemodified\";s:19:\"2016-08-31 17:08:27\";}s:2:\"ip\";s:9:\"127.0.0.1\";s:6:\"cookie\";a:2:{s:11:\"erp_session\";s:32:\"7c7m6b6tnd3hke310kceivfqq6okd25d\";s:32:\"393708a76c93bde34de21762630ec138\";s:12:\"1-1545579539\";}}'),
(2, 'LOGGED_IN', '2018-12-24 03:29:45', 'admin', 'a:4:{s:5:\"creds\";a:3:{s:8:\"username\";s:5:\"admin\";s:8:\"password\";s:32:\"210d75e3516c7401f167f48237445403\";s:8:\"remember\";s:0:\"\";}s:6:\"member\";O:8:\"stdClass\":30:{s:2:\"id\";s:1:\"1\";s:8:\"username\";s:5:\"admin\";s:8:\"password\";s:88:\"xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==\";s:12:\"password_pin\";s:88:\"xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==\";s:4:\"name\";s:13:\"ADMINISTRATOR\";s:5:\"email\";s:21:\"admin@siakadmedia.com\";s:12:\"id_adm_group\";s:1:\"1\";s:14:\"id_adm_company\";s:1:\"1\";s:21:\"default_id_adm_module\";s:1:\"1\";s:4:\"type\";s:1:\"1\";s:6:\"status\";s:1:\"1\";s:10:\"last_login\";s:19:\"2018-12-23 16:39:07\";s:7:\"address\";s:22:\"JL. TOMANG RAYA NO.123\";s:4:\"city\";s:3:\"162\";s:8:\"district\";s:13:\"JAKARTA PUSAT\";s:8:\"province\";s:2:\"12\";s:5:\"phone\";s:11:\"08123465789\";s:5:\"bbpin\";s:8:\"98765432\";s:4:\"bank\";s:1:\"1\";s:4:\"bill\";s:8:\"98765432\";s:9:\"bill_name\";s:13:\"ADMINISTRATOR\";s:6:\"branch\";s:13:\"JAKARTA PUSAT\";s:6:\"idcard\";s:16:\"3210210620160006\";s:10:\"uniquecode\";s:3:\"999\";s:7:\"nominal\";s:1:\"0\";s:11:\"as_stockist\";s:1:\"0\";s:6:\"gender\";s:4:\"male\";s:6:\"avatar\";s:11:\"avatar1.png\";s:11:\"datecreated\";s:19:\"2016-07-23 00:00:00\";s:12:\"datemodified\";s:19:\"2016-08-31 17:08:27\";}s:2:\"ip\";s:9:\"127.0.0.1\";s:6:\"cookie\";a:2:{s:32:\"393708a76c93bde34de21762630ec138\";s:12:\"1-1545618572\";s:11:\"erp_session\";s:32:\"c7kk2c1fqi9du7pfla284j4ck5v0g1n0\";}}'),
(3, 'LOGGED_IN', '2018-12-24 09:23:34', 'admin', 'a:4:{s:5:\"creds\";a:3:{s:8:\"username\";s:5:\"admin\";s:8:\"password\";s:32:\"210d75e3516c7401f167f48237445403\";s:8:\"remember\";s:0:\"\";}s:6:\"member\";O:8:\"stdClass\":30:{s:2:\"id\";s:1:\"1\";s:8:\"username\";s:5:\"admin\";s:8:\"password\";s:88:\"xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==\";s:12:\"password_pin\";s:88:\"xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==\";s:4:\"name\";s:13:\"ADMINISTRATOR\";s:5:\"email\";s:21:\"admin@siakadmedia.com\";s:12:\"id_adm_group\";s:1:\"1\";s:14:\"id_adm_company\";s:1:\"1\";s:21:\"default_id_adm_module\";s:1:\"1\";s:4:\"type\";s:1:\"1\";s:6:\"status\";s:1:\"1\";s:10:\"last_login\";s:19:\"2018-12-24 03:29:45\";s:7:\"address\";s:22:\"JL. TOMANG RAYA NO.123\";s:4:\"city\";s:3:\"162\";s:8:\"district\";s:13:\"JAKARTA PUSAT\";s:8:\"province\";s:2:\"12\";s:5:\"phone\";s:11:\"08123465789\";s:5:\"bbpin\";s:8:\"98765432\";s:4:\"bank\";s:1:\"1\";s:4:\"bill\";s:8:\"98765432\";s:9:\"bill_name\";s:13:\"ADMINISTRATOR\";s:6:\"branch\";s:13:\"JAKARTA PUSAT\";s:6:\"idcard\";s:16:\"3210210620160006\";s:10:\"uniquecode\";s:3:\"999\";s:7:\"nominal\";s:1:\"0\";s:11:\"as_stockist\";s:1:\"0\";s:6:\"gender\";s:4:\"male\";s:6:\"avatar\";s:11:\"avatar1.png\";s:11:\"datecreated\";s:19:\"2016-07-23 00:00:00\";s:12:\"datemodified\";s:19:\"2016-08-31 17:08:27\";}s:2:\"ip\";s:9:\"127.0.0.1\";s:6:\"cookie\";a:4:{s:32:\"393708a76c93bde34de21762630ec138\";s:12:\"1-1545639803\";s:11:\"erp_session\";s:32:\"q3dvt1i0bcri3kgte8a4u6kd3poojurk\";s:3:\"_ga\";s:26:\"GA1.2.915516821.1545625466\";s:4:\"_gid\";s:25:\"GA1.2.77719047.1545625466\";}}'),
(4, 'LOGGED_IN', '2018-12-24 09:24:57', 'admin', 'a:4:{s:5:\"creds\";a:3:{s:8:\"username\";s:5:\"admin\";s:8:\"password\";s:32:\"210d75e3516c7401f167f48237445403\";s:8:\"remember\";s:0:\"\";}s:6:\"member\";O:8:\"stdClass\":30:{s:2:\"id\";s:1:\"1\";s:8:\"username\";s:5:\"admin\";s:8:\"password\";s:88:\"xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==\";s:12:\"password_pin\";s:88:\"xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==\";s:4:\"name\";s:13:\"ADMINISTRATOR\";s:5:\"email\";s:21:\"admin@siakadmedia.com\";s:12:\"id_adm_group\";s:1:\"1\";s:14:\"id_adm_company\";s:1:\"1\";s:21:\"default_id_adm_module\";s:1:\"1\";s:4:\"type\";s:1:\"1\";s:6:\"status\";s:1:\"1\";s:10:\"last_login\";s:19:\"2018-12-24 09:23:34\";s:7:\"address\";s:22:\"JL. TOMANG RAYA NO.123\";s:4:\"city\";s:3:\"162\";s:8:\"district\";s:13:\"JAKARTA PUSAT\";s:8:\"province\";s:2:\"12\";s:5:\"phone\";s:11:\"08123465789\";s:5:\"bbpin\";s:8:\"98765432\";s:4:\"bank\";s:1:\"1\";s:4:\"bill\";s:8:\"98765432\";s:9:\"bill_name\";s:13:\"ADMINISTRATOR\";s:6:\"branch\";s:13:\"JAKARTA PUSAT\";s:6:\"idcard\";s:16:\"3210210620160006\";s:10:\"uniquecode\";s:3:\"999\";s:7:\"nominal\";s:1:\"0\";s:11:\"as_stockist\";s:1:\"0\";s:6:\"gender\";s:4:\"male\";s:6:\"avatar\";s:11:\"avatar1.png\";s:11:\"datecreated\";s:19:\"2016-07-23 00:00:00\";s:12:\"datemodified\";s:19:\"2016-08-31 17:08:27\";}s:2:\"ip\";s:9:\"127.0.0.1\";s:6:\"cookie\";a:4:{s:32:\"393708a76c93bde34de21762630ec138\";s:12:\"1-1545639828\";s:11:\"erp_session\";s:32:\"q3dvt1i0bcri3kgte8a4u6kd3poojurk\";s:3:\"_ga\";s:26:\"GA1.2.915516821.1545625466\";s:4:\"_gid\";s:25:\"GA1.2.77719047.1545625466\";}}'),
(5, 'LOGGED_IN', '2018-12-24 10:46:17', 'admin', 'a:4:{s:5:\"creds\";a:3:{s:8:\"username\";s:5:\"admin\";s:8:\"password\";s:32:\"210d75e3516c7401f167f48237445403\";s:8:\"remember\";s:0:\"\";}s:6:\"member\";O:8:\"stdClass\":30:{s:2:\"id\";s:1:\"1\";s:8:\"username\";s:5:\"admin\";s:8:\"password\";s:88:\"xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==\";s:12:\"password_pin\";s:88:\"xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==\";s:4:\"name\";s:13:\"ADMINISTRATOR\";s:5:\"email\";s:21:\"admin@siakadmedia.com\";s:12:\"id_adm_group\";s:1:\"1\";s:14:\"id_adm_company\";s:1:\"1\";s:21:\"default_id_adm_module\";s:1:\"1\";s:4:\"type\";s:1:\"1\";s:6:\"status\";s:1:\"1\";s:10:\"last_login\";s:19:\"2018-12-24 09:24:57\";s:7:\"address\";s:22:\"JL. TOMANG RAYA NO.123\";s:4:\"city\";s:3:\"162\";s:8:\"district\";s:13:\"JAKARTA PUSAT\";s:8:\"province\";s:2:\"12\";s:5:\"phone\";s:11:\"08123465789\";s:5:\"bbpin\";s:8:\"98765432\";s:4:\"bank\";s:1:\"1\";s:4:\"bill\";s:8:\"98765432\";s:9:\"bill_name\";s:13:\"ADMINISTRATOR\";s:6:\"branch\";s:13:\"JAKARTA PUSAT\";s:6:\"idcard\";s:16:\"3210210620160006\";s:10:\"uniquecode\";s:3:\"999\";s:7:\"nominal\";s:1:\"0\";s:11:\"as_stockist\";s:1:\"0\";s:6:\"gender\";s:4:\"male\";s:6:\"avatar\";s:11:\"avatar1.png\";s:11:\"datecreated\";s:19:\"2016-07-23 00:00:00\";s:12:\"datemodified\";s:19:\"2016-08-31 17:08:27\";}s:2:\"ip\";s:9:\"127.0.0.1\";s:6:\"cookie\";a:1:{s:11:\"erp_session\";s:32:\"tnb48poh3n863acjdd8md9fb8mea6n2g\";}}'),
(6, 'LOGGED_IN', '2018-12-24 13:33:22', 'admin', 'a:4:{s:5:\"creds\";a:3:{s:8:\"username\";s:5:\"admin\";s:8:\"password\";s:32:\"210d75e3516c7401f167f48237445403\";s:8:\"remember\";s:0:\"\";}s:6:\"member\";O:8:\"stdClass\":30:{s:2:\"id\";s:1:\"1\";s:8:\"username\";s:5:\"admin\";s:8:\"password\";s:88:\"xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==\";s:12:\"password_pin\";s:88:\"xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==\";s:4:\"name\";s:13:\"ADMINISTRATOR\";s:5:\"email\";s:21:\"admin@siakadmedia.com\";s:12:\"id_adm_group\";s:1:\"1\";s:14:\"id_adm_company\";s:1:\"1\";s:21:\"default_id_adm_module\";s:1:\"1\";s:4:\"type\";s:1:\"1\";s:6:\"status\";s:1:\"1\";s:10:\"last_login\";s:19:\"2018-12-24 10:46:17\";s:7:\"address\";s:22:\"JL. TOMANG RAYA NO.123\";s:4:\"city\";s:3:\"162\";s:8:\"district\";s:13:\"JAKARTA PUSAT\";s:8:\"province\";s:2:\"12\";s:5:\"phone\";s:11:\"08123465789\";s:5:\"bbpin\";s:8:\"98765432\";s:4:\"bank\";s:1:\"1\";s:4:\"bill\";s:8:\"98765432\";s:9:\"bill_name\";s:13:\"ADMINISTRATOR\";s:6:\"branch\";s:13:\"JAKARTA PUSAT\";s:6:\"idcard\";s:16:\"3210210620160006\";s:10:\"uniquecode\";s:3:\"999\";s:7:\"nominal\";s:1:\"0\";s:11:\"as_stockist\";s:1:\"0\";s:6:\"gender\";s:4:\"male\";s:6:\"avatar\";s:11:\"avatar1.png\";s:11:\"datecreated\";s:19:\"2016-07-23 00:00:00\";s:12:\"datemodified\";s:19:\"2016-08-31 17:08:27\";}s:2:\"ip\";s:9:\"127.0.0.1\";s:6:\"cookie\";a:3:{s:11:\"erp_session\";s:32:\"lh8suj1haeovfsskqag8i1vqcnms9lr7\";s:3:\"_ga\";s:26:\"GA1.2.915516821.1545625466\";s:4:\"_gid\";s:25:\"GA1.2.77719047.1545625466\";}}');

-- --------------------------------------------------------

--
-- Table structure for table `adm_member`
--

CREATE TABLE `adm_member` (
  `id` bigint(20) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_pin` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `id_adm_group` bigint(11) NOT NULL,
  `id_adm_company` bigint(11) NOT NULL,
  `default_id_adm_module` bigint(11) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '1=Administrator,2=Member',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0=Not Active,1=Active,2=Banned,3=Deleted',
  `last_login` datetime NOT NULL,
  `address` text NOT NULL,
  `city` int(11) NOT NULL,
  `district` varchar(100) NOT NULL,
  `province` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `bbpin` varchar(20) NOT NULL,
  `bank` int(1) NOT NULL,
  `bill` varchar(20) NOT NULL,
  `bill_name` varchar(255) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `idcard` varchar(50) NOT NULL,
  `uniquecode` int(11) NOT NULL,
  `nominal` bigint(20) NOT NULL,
  `as_stockist` int(11) NOT NULL COMMENT '0=Member,1=Stockist',
  `gender` varchar(10) NOT NULL,
  `avatar` varchar(100) NOT NULL,
  `datecreated` datetime NOT NULL,
  `datemodified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `adm_member`
--

INSERT INTO `adm_member` (`id`, `username`, `password`, `password_pin`, `name`, `email`, `id_adm_group`, `id_adm_company`, `default_id_adm_module`, `type`, `status`, `last_login`, `address`, `city`, `district`, `province`, `phone`, `bbpin`, `bank`, `bill`, `bill_name`, `branch`, `idcard`, `uniquecode`, `nominal`, `as_stockist`, `gender`, `avatar`, `datecreated`, `datemodified`) VALUES
(1, 'admin', 'xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==', 'xgIctBsEcJkgI9DHDrWDQzYzxPvb1GfkerT7WMcj4++mE8TsUeKLMZW0yMQoOm71Rul9sUiaE/7bRygbiuvEVQ==', 'ADMINISTRATOR', 'admin@siakadmedia.com', 1, 1, 1, 1, 1, '2018-12-24 13:33:21', 'JL. TOMANG RAYA NO.123', 162, 'JAKARTA PUSAT', 12, '08123465789', '98765432', 1, '98765432', 'ADMINISTRATOR', 'JAKARTA PUSAT', '3210210620160006', 999, 0, 0, 'male', 'avatar1.png', '2016-07-23 00:00:00', '2016-08-31 17:08:27');

-- --------------------------------------------------------

--
-- Table structure for table `adm_menu`
--

CREATE TABLE `adm_menu` (
  `id_adm_menu` bigint(11) NOT NULL,
  `name` longtext,
  `sequence_no` smallint(6) DEFAULT NULL,
  `page_name` longtext,
  `menu_level` smallint(6) DEFAULT '0',
  `note` varchar(255) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT '1',
  `id_adm_module` bigint(11) DEFAULT NULL,
  `parent_id_adm_menu` bigint(11) DEFAULT NULL,
  `php_file` longtext,
  `icon_file` longtext,
  `route` longtext,
  `icon_class` longtext COMMENT 'TRIAL'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `adm_menu`
--

INSERT INTO `adm_menu` (`id_adm_menu`, `name`, `sequence_no`, `page_name`, `menu_level`, `note`, `visible`, `id_adm_module`, `parent_id_adm_menu`, `php_file`, `icon_file`, `route`, `icon_class`) VALUES
(1, 'Developer Setting', 2, 'Developer Setting', 0, NULL, 1, 1, NULL, NULL, NULL, NULL, 'flaticon-cogwheel-1 '),
(2, 'Application Module List', 2, 'Application Module List', 1, NULL, 1, 1, 1, 'master_module.php', NULL, 'adm/module', 'flaticon-signs-1 '),
(3, 'Menu Structure', 3, 'Menu Structure', 1, NULL, 1, 1, 1, 'master_menu.php', NULL, 'adm/menu', 'flaticon-grid-menu-v2 '),
(4, 'User', 3, 'User', 0, NULL, 1, 1, NULL, NULL, NULL, NULL, 'fa-user'),
(5, 'User Group', 2, 'User Group', 1, NULL, 1, 1, 4, 'master_group.php', NULL, 'adm_user_group', 'flaticon-users '),
(6, 'User Account', 1, 'User Account', 1, NULL, 1, 1, 4, 'master_user.php', NULL, 'adm_user/user_account', 'flaticon-avatar '),
(7, 'Data Kurikulum', 5, 'Data Kurikulum', 0, NULL, 1, 2, NULL, NULL, NULL, NULL, 'fa-dedent'),
(8, 'Tahun Ajaran', 1, 'Tahun Ajaran', 1, NULL, 1, 2, 7, NULL, NULL, NULL, 'flaticon-calendar-2 ');

-- --------------------------------------------------------

--
-- Table structure for table `adm_module`
--

CREATE TABLE `adm_module` (
  `id_adm_module` bigint(11) NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `sequence_no` smallint(6) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT '1',
  `folder` varchar(255) DEFAULT NULL,
  `icon_class` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `adm_module`
--

INSERT INTO `adm_module` (`id_adm_module`, `name`, `sequence_no`, `visible`, `folder`, `icon_class`) VALUES
(1, 'ADMIN', 1, 1, 'adm', 'flaticon-imac '),
(2, 'ADMINISTRASI', NULL, 1, 'admst', 'flaticon-file-1 '),
(3, 'AKADEMIK', NULL, 1, 'akademik', 'flaticon-squares-4 ');

-- --------------------------------------------------------

--
-- Table structure for table `adm_options`
--

CREATE TABLE `adm_options` (
  `id_option` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `adm_options`
--

INSERT INTO `adm_options` (`id_option`, `name`, `value`) VALUES
(1, 'facebook_link', 'https://www.facebook.com/'),
(2, 'twitter_link', 'https://www.twitter.com/'),
(3, 'googleplus_link', 'https://plus.google.com/'),
(4, 'global_password', '123456'),
(5, 'global_limit', '25'),
(6, 'be_dashboard_member', '<p><strong>PROMO CASH REWARD Rp 20 JUTA</strong></p>\n\n<p><strong>(PERIODE 15 JANUARI - 15 MARET 2017)</strong></p>\n\n<p>&nbsp;</p>\n\n<p>Beli Paket Cash Reward (CR) senilai Rp 2.800.000 mendapatkan produk (pilih salah satu) :</p>\n\n<p>- 6 box Bkev ASC</p>\n\n<p>- 4 box Bkev Premium Inside</p>\n\n<p>- 3 box Bkev ASC + 2 box Bkev Premium Inside</p>\n\n<p>&nbsp;</p>\n\n<p>Bonus Paket CR :</p>\n\n<p>1. Bonus Sponsor Rp 500.000/paket</p>\n\n<p>2. 1 Paket CR mendapatkan 1 Cash Poin (CP), apabila terposting 22 CP kiri dan 22 CP kanan maka akan mendapatkan Cash Reward sebesar Rp 20 juta.</p>\n\n<p>&nbsp;</p>\n\n<p>Info lebih lanjut hubungi upline atau sponsor Anda.</p>\n\n<p>&nbsp;</p>\n\n<p><strong>Management Bkev Global Network</strong></p>'),
(7, 'be_dashboard_stockist', '<p><strong>PROMO CASH REWARD Rp 20 JUTA</strong></p>\n\n<p><strong>(PERIODE 15 JANUARI - 15 MARET 2017)</strong></p>\n\n<p>&nbsp;</p>\n\n<p>Beli Paket Cash Reward (CR) senilai Rp 2.800.000 mendapatkan produk (pilih salah satu) :</p>\n\n<p>- 6 box Bkev ASC</p>\n\n<p>- 4 box Bkev Premium Inside</p>\n\n<p>- 3 box Bkev ASC + 2 box Bkev Premium Inside</p>\n\n<p>&nbsp;</p>\n\n<p>Bonus Paket CR :</p>\n\n<p>1. Bonus Sponsor Rp 500.000/paket</p>\n\n<p>2. 1 Paket CR mendapatkan 1 Cash Poin (CP), apabila terposting 22 CP kiri dan 22 CP kanan maka akan mendapatkan Cash Reward sebesar Rp 20 juta.</p>\n\n<p>&nbsp;</p>\n\n<p>Info lebih lanjut hubungi upline atau sponsor Anda.</p>\n\n<p>&nbsp;</p>\n\n<p><strong>Management Bkev Global Network</strong></p>'),
(8, 'company_name', 'SIAKAD MEDIA '),
(12, 'mail_sender_admin', 'admin@siakadmedia.com'),
(13, 'comingsoon_time', '10 January 2016 12:00:00'),
(14, 'unique_number', '1'),
(15, 'send_email_down_nonactive', 'Informasi Pendaftaran\n-------------------------------------------------\n\nTerima kasih Anda sudah mendaftar sebagai anggota baru di BKEV Global Network (http://bkev-globalnetwork.com).\nBerikut adalah informasi akun anggota anda :\n\nSponsor : %sponsor_username%\nSponsor Email : %sponsor_email%\nSponsor Phone : %sponsor_phone%\n\n-------------------------------------------------\nSalam Sukses,\nManajemen BKEV Global Network'),
(16, 'send_email_down_active', 'Informasi Pendaftaran\n-------------------------------------------------\n\nTerima kasih Anda sudah mendaftar sebagai anggota baru di BKEV Global Network (http://bkev-globalnetwork.com).\nBerikut adalah informasi akun anggota anda :\n\nUsername : %username%\nPassword : %password%\nSponsor : %sponsor_username%\nSponsor Email : %sponsor_email%\nSponsor Phone : %sponsor_phone%\n\nAkun Anda sudah aktif dan Anda sudah dapat mengakses halaman member area. Silahkan klik link di bawah ini untuk login :%login_url%\n\n-------------------------------------------------\nSalam Sukses,\nManajemen BKEV Global Network'),
(17, 'send_email_down_nonactive_html', '<div style=\"width: 80%; text-align: center; margin: 0 auto 20px auto;\"><img src=\"http://bkev-globalnetwork.com/assets/img/logo_small.png\" /></div>\n\n<div style=\"width: 80%; border: 2px solid #FCB322; padding: 0; margin: 0 auto;\">\n<div style=\"background-color: #FCB322; padding: 5px; color: #FFF; text-align: center; font: bold 13px Arial;\">Informasi Pendaftaran</div>\n\n<div style=\"padding 10px; color: #666666; font: 12px/20px Arial;\">\n<p style=\"padding: 0 10px;\">Terima kasih Anda sudah mendaftar sebagai calon anggota baru di <strong><a href=\"http://ionasis.com\" style=\"text-decoration: none; color: #428BCA;\">BKEV Global Network</a></strong> (http://bkev-globalnetwork.com).<br />\nBerikut adalah informasi akun sponsor&nbsp;anda :</p>\n\n<p style=\"padding: 0 10px;\">Sponsor : %sponsor_username%<br />\nSponsor Email : %sponsor_email%<br />\nSponsor Phone : %sponsor_phone%</p>\n\n<p style=\"padding: 10px 10px 0 10px; color: #888888; font-size: 11px;\"><strong>Status ID Anda saat ini masih pending. Hubungi sponsor Anda utk mengaktifkan ID Anda.</strong><br />\n-------------------------------------------------</p>\n\n<p style=\"padding: 10px 10px 0 10px; color: #888888; font-size: 11px;\">Salam Sukses,<br />\nManajemen BKEV Global Network</p>\n\n<p style=\"text-align: center; margin: 15px 0 0 0; font: 10px Arial; color: #888888; border-top: 1px solid #EEE; padding: 15px 0; background-color: #F7F7F7;\">Copyright &copy; 2016. BKEV Global Network</p>\n</div>\n</div>'),
(18, 'send_email_down_active_html', '<div style=\"width: 80%; text-align: center; margin: 0 auto 20px auto;\"><img src=\"http://bkev-globalnetwork.com/assets/img/logo_small.png\" /></div>\n\n<div style=\"width: 80%; border: 2px solid #FCB322; padding: 0; margin: 0 auto;\">\n<div style=\"background-color: #FCB322; padding: 5px; color: #FFF; text-align: center; font: bold 13px Arial;\">Informasi Pendaftaran</div>\n\n<div style=\"padding 10px; color: #666666; font: 12px/20px Arial;\">\n<p style=\"padding: 0 10px;\">Terima kasih Anda sudah mendaftar sebagai anggota baru di <strong><a href=\"http://ionasis.com\" style=\"text-decoration: none; color: #428BCA;\">BKEV Global Network</a></strong> (http://bkev-globalnetwork.com).<br />\nBerikut adalah informasi akun anggota anda :</p>\n\n<p style=\"padding: 0 10px;\"><span style=\"color: rgb(102, 102, 102); font-family: Arial; font-size: 12px; line-height: 20px;\">Username : %username%</span><br style=\"color: rgb(102, 102, 102); font-family: Arial; font-size: 12px; line-height: 20px;\" />\n<span style=\"color: rgb(102, 102, 102); font-family: Arial; font-size: 12px; line-height: 20px;\">Password : %password%</span></p>\n\n<p style=\"padding: 0 10px;\">Akun Anda sudah aktif dan Anda sudah dapat mengakses halaman member area. Silahkan klik link di bawah ini untuk login :<br />\n<br />\n%login_url%</p>\n\n<p style=\"width: 50%; padding: 10px 10px 0 10px; color: #888888; font-size: 11px;\">-------------------------------------------------<br />\nSalam Sukses,<br />\nManajemen BKEV Global Network<br />\n&nbsp;</p>\n\n<p style=\"text-align: center; margin: 15px 0 0 0; font: 10px Arial; color: #888888; border-top: 1px solid #EEE; padding: 15px 0; background-color: #F7F7F7;\">Copyright &copy; 2016. BKEV Global Network</p>\n</div>\n</div>'),
(19, 'send_email_sponsor', 'Informasi Pendaftaran\n-------------------------------------------------\n\nSelamat! Anggota baru telah terdaftar di jaringan Anda.\nBerikut adalah informasi anggota baru jaringan anda :\n\nUsername : %username%\nNama : %name%\nEmail : %email%\nPhone : %phone%\n\nSilahkan melakukan konfirmasi atas pendaftaran anggota jaringan baru Anda.\nAnda akan mendapatkan bonus sponsorship setelah anggota jaringan baru Anda aktif.\n\n-------------------------------------------------\nSalam Sukses,\nManajemen BKEV Global Network'),
(20, 'send_email_admin', 'Informasi Pendaftaran\n-------------------------------------------------\n\nAnggota baru telah terdaftar. Berikut adalah informasi anggota baru :\n\nUsername : %username%\nPassword : %password%\n\nSponsor : %sponsor_username%\nSponsor Email : %sponsor_email%\nSponsor Phone : %sponsor_phone%\n\n-------------------------------------------------\nSalam Sukses,\nManajemen BKEV Global Network'),
(21, 'send_email_sponsor_html', '<div style=\"width: 80%; text-align: center; margin: 0 auto 20px auto;\"><img src=\"http://bkev-globalnetwork.com/assets/img/logo_small.png\" /></div>\n\n<div style=\"width: 80%; border: 2px solid #FCB322; padding: 0; margin: 0 auto;\">\n<div style=\"background-color: #FCB322; padding: 5px; color: #FFF; text-align: center; font: bold 13px Arial;\">Informasi Pendaftaran</div>\n\n<div style=\"padding 10px; color: #666666; font: 12px/20px Arial;\">\n<p style=\"padding: 0 10px;\">Selamat! Anggota baru telah terdaftar di jaringan Anda.<br />\nBerikut adalah informasi anggota baru jaringan anda :</p>\n\n<p style=\"padding: 0 10px;\">Username : %username%<br />\nNama : %name%<br />\nEmail : %email%<br />\nPhone : %phone%</p>\n\n<p style=\"padding: 0 10px;\">Mari bina dan layani dengan baik downline Anda agar mereka bisa berkembang cepat bersama Anda untuk mencapai impian di BKEV Global Network.</p>\n\n<p style=\"width: 50%; padding: 10px 10px 0 10px; color: #888888; font-size: 11px;\">Salam Sukses,<br />\nManajemen BKEV Global Network</p>\n\n<p style=\"text-align: center; margin: 15px 0 0 0; font: 10px Arial; color: #888888; border-top: 1px solid #EEE; padding: 15px 0; background-color: #F7F7F7;\">Copyright &copy; 2016. BKEV Global Network</p>\n</div>\n</div>'),
(22, 'send_email_admin_html', '<div style=\"width: 80%; text-align: center; margin: 0 auto 20px auto;\"><img src=\"http://bkev-globalnetwork.com/assets/img/logo_small.png\" /></div>\n\n<div style=\"width: 80%; border: 2px solid #FCB322; padding: 0; margin: 0 auto;\">\n<div style=\"background-color: #FCB322; padding: 5px; color: #FFF; text-align: center; font: bold 13px Arial;\">Informasi Pendaftaran</div>\n\n<div style=\"padding 10px; color: #666666; font: 12px/20px Arial;\">\n<p style=\"padding: 0 10px;\">Anggota baru telah terdaftar. Berikut adalah informasi anggota baru :</p>\n\n<p style=\"padding: 0 10px;\">Username : %username%<br />\nPassword : %password%<br />\n<br />\nSponsor : %sponsor_username%<br />\nSponsor Email : %sponsor_email%<br />\nSponsor Phone : %sponsor_phone%</p>\n\n<p style=\"width: 50%; padding: 20px 10px 0 10px; color: #888888; font-size: 11px;\">Salam Sukses,<br />\nManajemen BKEV Global Network<br />\n&nbsp;</p>\n\n<p style=\"text-align: center; margin: 15px 0 0 0; font: 10px Arial; color: #888888; border-top: 1px solid #EEE; padding: 15px 0; background-color: #F7F7F7;\">Copyright &copy; 2016. BKEV Global Network</p>\n</div>\n</div>'),
(23, 'sms_format_new_member_rep', 'Yth. %name%. Silahkan Transfer ke Rek BCA 2-7777-30-777 an. PT.Bkev Riset Internasional sebesar %nominal% untuk mengaktifkan ID Anda.'),
(24, 'sms_format_new_member_rep_sponsor', 'Informasi. %name% %phone% baru saja bergabung di PT.Ionasis sebagai calon downline Anda. Silahkan di Follow Up. Salam Sukses.'),
(25, 'sms_format_new_member', 'Selamat bergabung di Bkev Global Network. Username Anda %username%, Password %password%. Silahkan login di www.bkev-globalnetwork.com. Go Presidential Crown !!'),
(26, 'sms_format_new_member_sponsor', 'Selamat !! Downline Anda %name% telah terdaftar di BKEV Global Network dengan Username %username%. Go Presidential Crown !!'),
(27, 'sms_format_bonus', 'Selamat, Username Anda %username% mendapatkan Bonus %nominal%. Jaringan Anda saat ini %node%. Go Presidential Crown !!'),
(28, 'sms_format_withdrawal', 'Bonus Username Anda %username% sebesar %nominal% akan segera diproses transfer ke %bank% (%bill%) atas nama %name%. Go Presidential Crown !!'),
(29, 'sms_format_reward', 'Management BKEV mengucapkan selamat, username Anda %username% mendapatkan Reward Happy Point yaitu %reward%.'),
(30, 'sms_format_cpassword', 'Password username %username% telah direset menjadi %password%'),
(31, 'sms_format_cpasswordpin', 'Password PIN username %username% telah direset menjadi %password%'),
(32, 'sms_format_bonus_poin_ro', 'Selamat. ID Anda %username% berhasil mendapatkan bonus poin %poin%. Tingkatkan terus Jaringan Anda. Salam Sukses.'),
(33, 'sms_format_atm_before_qualified', 'Auto-M ID %username% Anda sd hari ini %nominal%. Silahkan RO dgn ketik: \"RO#ID Anda\" kirim ke 0816882797'),
(34, 'sms_format_atm_qualified', 'Selamat. ID %memberuid% Anda telah qualified Auto-M %nominal%. Produk segera dikirim ke alamat Anda.'),
(35, 'sms_format_respassword', 'Password username Anda : %username% telah di reset menjadi %password%. Silahkan login menggunakan password baru.'),
(36, 'sms_format_withdrawal_all', 'Bonus Username Anda %username% telah di transfer sebesar %nominal%.'),
(37, 'sms_format_reward_autoglobal', 'Management BKEV mengucapkan selamat, username Anda %username% mendapatkan Reward Auto Global yaitu %reward%.'),
(38, 'sms_format_qualified_autocbi', 'Selamat username Anda %username% sudah qualified di CBI %cbi_before% dan masuk ke CBI %cbi_after%. Anda berhak menerima bonus CBI %nominal%');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adm_company`
--
ALTER TABLE `adm_company`
  ADD PRIMARY KEY (`id_adm_company`);

--
-- Indexes for table `adm_group`
--
ALTER TABLE `adm_group`
  ADD PRIMARY KEY (`id_adm_group`);

--
-- Indexes for table `adm_group_menu`
--
ALTER TABLE `adm_group_menu`
  ADD PRIMARY KEY (`id_adm_group`,`id_adm_menu`);

--
-- Indexes for table `adm_log`
--
ALTER TABLE `adm_log`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `adm_member`
--
ALTER TABLE `adm_member`
  ADD PRIMARY KEY (`id`),
  ADD KEY `memberuid` (`username`),
  ADD KEY `email` (`email`),
  ADD KEY `status` (`status`),
  ADD KEY `datecreated` (`datecreated`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `adm_menu`
--
ALTER TABLE `adm_menu`
  ADD PRIMARY KEY (`id_adm_menu`);

--
-- Indexes for table `adm_module`
--
ALTER TABLE `adm_module`
  ADD PRIMARY KEY (`id_adm_module`);

--
-- Indexes for table `adm_options`
--
ALTER TABLE `adm_options`
  ADD PRIMARY KEY (`id_option`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adm_company`
--
ALTER TABLE `adm_company`
  MODIFY `id_adm_company` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `adm_group`
--
ALTER TABLE `adm_group`
  MODIFY `id_adm_group` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `adm_group_menu`
--
ALTER TABLE `adm_group_menu`
  MODIFY `id_adm_group` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `adm_log`
--
ALTER TABLE `adm_log`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `adm_member`
--
ALTER TABLE `adm_member`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `adm_menu`
--
ALTER TABLE `adm_menu`
  MODIFY `id_adm_menu` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `adm_module`
--
ALTER TABLE `adm_module`
  MODIFY `id_adm_module` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `adm_options`
--
ALTER TABLE `adm_options`
  MODIFY `id_option` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
