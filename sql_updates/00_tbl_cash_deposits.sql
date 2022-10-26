-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2019 at 07:04 AM
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
-- Table structure for table `tbl_cash_deposits`
--


CREATE TABLE IF  NOT EXISTS `tbl_cash_deposits` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `transaction` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `AmountPaid` decimal(12,2) DEFAULT NULL,
  `BillId` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PayCntrNum` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PspReceiptNumber` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `cancelled` tinyint(1) DEFAULT 0,
  `Processed` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `paid_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

alter table `tbl_cash_deposits` ADD IF NOT EXISTS `AmountPaid` decimal(12,2) DEFAULT NULL AFTER `amount`;
alter table `tbl_cash_deposits` ADD IF NOT EXISTS `BillId` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `AmountPaid`;
alter table `tbl_cash_deposits` ADD IF NOT EXISTS `PayCntrNum` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `BillId`;
alter table `tbl_cash_deposits` ADD IF NOT EXISTS `PspReceiptNumber` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `PayCntrNum`;
alter table `tbl_cash_deposits` ADD IF NOT EXISTS `cancelled` tinyint(1) DEFAULT 0 AFTER `user_id`;
alter table `tbl_cash_deposits` ADD IF NOT EXISTS `Processed` tinyint(1) DEFAULT 0 AFTER `cancelled`;
alter table `tbl_cash_deposits` ADD IF NOT EXISTS `paid_at` timestamp NOT NULL AFTER `updated_at`;
alter table tbl_cash_deposits ADD COLUMN IF NOT EXISTS cancelling_reason TEXT NULL;
alter table tbl_cash_deposits change AmountPaid AmountPaid decimal(12,2);
alter table tbl_cash_deposits change amount amount decimal(12,2);
CREATE OR REPLACE VIEW vw_paid_bills AS (SELECT id,created_at, gepg_receipt AS receipt_number, gepg_receipt, facility_id, patient_id, first_name,middle_name,last_name,medical_record_number,CONCAT(ifnull(first_name,''),' ',ifnull(middle_name,''), ' ', ifnull(last_name,''), ' # ',medical_record_number) as name,quantity, discount, price, item_name, sub_category_name, main_category_id,is_payable FROM tbl_invoice_lines t1 WHERE status_id = 2 and payment_method_id = 2 and timestampdiff(day, created_at, current_timestamp) <= 7) UNION(SELECT t1.id,t1.created_at, t1.id AS receipt_number,t1.PspReceiptNumber as gepg_receipt, t1.facility_id, NULL as patient_id, NULL as first_name,NULL as middle_name,NULL as last_name,NULL as medical_record_number,CONCAT(t2.name, ' # ','') as name,  1, 0 as discount, t1.amountpaid, t1.transaction as item_name, NULL as sub_category_name, Null as main_category_id,true FROM tbl_cash_deposits t1 INNER JOIN users t2 ON t1.PspReceiptNumber IS NOT NULL and t1.cancelled IS NOT TRUE and t1.user_id = t2.id and (timestampdiff(day, t1.paid_at, current_timestamp) <= 2));