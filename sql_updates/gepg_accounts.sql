-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2019 at 06:40 AM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test_env`
--

-- --------------------------------------------------------

--
-- Table structure for table `gepg_accounts`
--

CREATE TABLE IF NOT EXISTS `gepg_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `url` varchar(50) NOT NULL DEFAULT 'http://154.118.230.18/api/bill/request',
  `reconcile_bills` varchar(150) NOT NULL DEFAULT 'http://154.118.230.202/api/reconciliations/sig_sp_qrequest',
  `cancel_url` varchar(150) NOT NULL DEFAULT 'http://154.118.230.18/api/bill/request/cancel-request',
  `self_url` varchar(150) NOT NULL DEFAULT 'http://localhost/gepg/gepg_handler/',
  `intermediate_url` varchar(150) NOT NULL DEFAULT 'http://196.192.72.107/gepg/gepg_handler/',
  `default_phone` varchar(12) NOT NULL,
  `default_email` varchar(150) NOT NULL DEFAULT 'gothomis@tamisemi.go.tz',
  `SpCode` varchar(50) NOT NULL,
  `SubSpCode` varchar(50) NOT NULL,
  `GfsCode` varchar(50) NOT NULL,
  `SpSysId` varchar(50) NOT NULL DEFAULT 'GOTHOMIS',
  `Ccy` varchar(50) NOT NULL DEFAULT 'TZS',
  `RemFlag` varchar(5) NOT NULL DEFAULT 'true',
  `RtrRespFlg` varchar(5) NOT NULL DEFAULT 'true',
  `UseItemRefOnPay` varchar(5) NOT NULL DEFAULT 'N',
  `BillPayOpt` varchar(50) NOT NULL DEFAULT '1',
  `PaymentMethod` tinyint default 0,
  `ReconcOpt` tinyint(4) NOT NULL DEFAULT '1',
  `facility_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--
alter table gepg_accounts add if not exists `default_phone` varchar(12) NOT NULL;
alter table gepg_accounts add if not exists`default_email` varchar(150) NOT NULL DEFAULT 'gothomis@tamisemi.go.tz';

INSERT INTO `gepg_accounts` (`facility_code`,default_phone, `intermediate_url`) SELECT `facility_code`,default_phone, `intermediate_url` FROM (SELECT tbl_facilities.facility_code,'' as default_phone,'http://196.192.72.107/gepg/gepg_handler/' as  `intermediate_url`,'2019-07-27 10:34:13' as created_at, '2019-07-27 10:34:13' as updated_at, gepg_accounts.id as account FROM tbl_patients JOIN tbl_facilities ON tbl_patients.facility_id = tbl_facilities.id left join gepg_accounts on tbl_facilities.facility_code = gepg_accounts.facility_code ORDER BY tbl_patients.id DESC LIMIT 1) as temp where account is null;

COMMIT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Dumping data for table `gepg_accounts`
--

alter table gepg_accounts add if not exists user_id int(11) null;

alter table gepg_accounts add if not exists PaymentMethod tinyint default 0;