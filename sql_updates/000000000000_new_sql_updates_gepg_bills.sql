-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2019 at 06:55 PM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gothomis`
--

-- --------------------------------------------------------

--
-- Table structure for table `gepg_bills`
--

CREATE TABLE IF NOT EXISTS `gepg_bills` (
  `Id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Name` varchar(150) NOT NULL,
  `InvoiceId` int(11)  DEFAULT 0,
  `CashDeposit` int(11) DEFAULT 0,
  `BillAmount` decimal(18,2) NOT NULL,
  `BillId` varchar(50) NOT NULL,
  `PayCntrNum` varchar(50) NULL,
  `Paid` tinyint(1) DEFAULT 0,
  `PspReceiptNumber` varchar(100) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--
alter table gepg_bills add column if not exists PaidAt timestamp null after Paid;
truncate gepg_bills;
insert into gepg_bills(InvoiceId, CashDeposit, BillId, BillAmount, Name, PspReceiptNumber, PayCntrNum, Paid, PaidAt)
	(select invoice_id, 0, BillId, sum(price*quantity)-sum(discount), CONCAT(ifnull(first_name,''),' ',ifnull(middle_name,''), ' ', ifnull(last_name,'')), gepg_receipt, PayCntrNum, case when gepg_receipt is not null then 1 else 0 end,case when gepg_receipt is not null then tbl_invoice_lines.updated_at else null end from tbl_encounter_invoices join tbl_invoice_lines on tbl_encounter_invoices.id = tbl_invoice_lines.invoice_id and tbl_invoice_lines.payment_method_id=2 and tbl_encounter_invoices.BillId IS NOT NULL and (tbl_encounter_invoices.processed = 0 or tbl_invoice_lines.status_id=1) AND  TIMESTAMPDIFF(HOUR, tbl_encounter_invoices.created_at, CURRENT_TIMESTAMP) <= 24 group by invoice_id)UNION(select 0, tbl_cash_deposits.id, BillId, Amount, name, PspReceiptNumber, PayCntrNum, case when PspReceiptNumber is not null then 1 else 0 end, case when PspReceiptNumber is not null then tbl_cash_deposits.updated_at else null end from tbl_cash_deposits join users on tbl_cash_deposits.user_id=users.id and tbl_cash_deposits.processed =0 and cancelled=0 AND  TIMESTAMPDIFF(HOUR, tbl_cash_deposits.created_at, CURRENT_TIMESTAMP) <= 24);
	
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;