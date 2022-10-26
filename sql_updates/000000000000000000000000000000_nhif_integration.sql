ALTER TABLE users add column if not exists practioner_no varchar(191) null;
ALTER TABLE tbl_accounts_numbers add  column if not exists 	is_submitted tinyint default 0;
ALTER TABLE tbl_accounts_numbers add  column if not exists 	visit_close int default 1;
ALTER TABLE tbl_accounts_numbers add  column if not exists 	scheme_id int(10) null after card_no;
ALTER TABLE tbl_accounts_numbers add  column if not exists 	visit_type tinyint default 1 after scheme_id;
ALTER TABLE tbl_accounts_numbers change  column if exists 	visit_type visit_type tinyint default 1;
ALTER TABLE tbl_accounts_numbers add  column if not exists 	closed_by int(10) null after visit_type;
ALTER TABLE tbl_facilities add  column if not exists 	nhif_facility_code VARCHAR(25) null;

-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2019 at 08:20 PM
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
set foreign_key_checks  =0;
--
-- Database: `nhif`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_nhif_approval_remarks`
--

CREATE TABLE IF NOT EXISTS `tbl_nhif_approval_remarks` (
  `id` int(10) UNSIGNED NOT NULL,
  `card_number` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refference_number` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `authorization_number` int(10) UNSIGNED DEFAULT NULL,
  `item_code` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_nhif_facility_codes`
--

CREATE TABLE IF NOT EXISTS `tbl_nhif_facility_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `nhif_code` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `facility_code` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_nhif_files`
--

CREATE TABLE IF NOT EXISTS `tbl_nhif_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `claims` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_nhif_approval_remarks`
--
ALTER TABLE `tbl_nhif_approval_remarks`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD KEY IF NOT EXISTS `tbl_nhif_approval_remarks_user_id_foreign` (`user_id`);

-- Indexes for table `tbl_nhif_facility_codes`
--
ALTER TABLE `tbl_nhif_facility_codes`
  ADD PRIMARY KEY  IF NOT EXISTS (`id`),
  ADD KEY  IF NOT EXISTS `tbl_nhif_facility_codes_facility_code_foreign` (`facility_code`);

--
-- AUTO_INCREMENT for table `tbl_nhif_facility_codes`
--
ALTER TABLE `tbl_nhif_facility_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
  

-- Indexes for table `tbl_nhif_files`
--
ALTER TABLE `tbl_nhif_files`
  ADD PRIMARY KEY  IF NOT EXISTS (`id`),
  ADD KEY  IF NOT EXISTS `tbl_nhif_files_user_id_foreign` (`user_id`),
  ADD KEY  IF NOT EXISTS `tbl_nhif_files_facility_id_foreign` (`facility_id`),
  ADD KEY  IF NOT EXISTS `tbl_nhif_files_account_id_foreign` (`account_id`);

--
-- AUTO_INCREMENT for table `tbl_nhif_files`
--
ALTER TABLE `tbl_nhif_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_nhif_approval_remarks`
--
ALTER TABLE `tbl_nhif_approval_remarks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;


--
-- Constraints for dumped tables
--


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2019 at 08:24 PM
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
-- Database: `nhif`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_api_credentials`
--

CREATE TABLE IF NOT EXISTS `tbl_api_credentials` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(90) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FacilityCode` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_api_credentials`
--
ALTER TABLE `tbl_api_credentials`
  ADD PRIMARY KEY IF NOT EXISTS(`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_api_credentials`
--
ALTER TABLE `tbl_api_credentials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2019 at 09:14 PM
-- Server version: 10.1.35-MariaDB
-- PHP Version: 7.2.9

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
-- Table structure for table `tbl_integrating_keys`
--

CREATE TABLE IF NOT EXISTS `tbl_integrating_keys` (
  `id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `base_urls` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `private_keys` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_keys` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_type` int(11) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_integrating_keys`
--
ALTER TABLE `tbl_integrating_keys`
  ADD PRIMARY KEY  IF NOT EXISTS(`id`),
  ADD KEY  IF NOT EXISTS`tbl_integrating_keys_facility_id_foreign` (`facility_id`);

--
-- Dumping data for table `tbl_integrating_keys`
--

INSERT IGNORE INTO `tbl_integrating_keys` (`id`, `facility_id`, `base_urls`, `private_keys`, `public_keys`, `api_type`, `active`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'tamisemi@2017', 'tamisemi', 4, 1, '2018-08-25 13:13:23', '2018-08-25 13:26:43'),
(2, 1, NULL, '12345678', 'admin@gothomis', 5, 1, '2018-08-31 21:03:18', '2018-09-02 10:40:01'),
(3, 2, NULL, '12345678', 'admin@gothomis', 5, 1, '2018-09-07 19:15:42', '2018-09-07 19:15:42');

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_integrating_keys`
--
ALTER TABLE `tbl_integrating_keys`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_integrating_keys`
--
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2019 at 09:25 PM
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
-- Database: `nhif`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_insuarance_item_prices`
--

CREATE TABLE IF NOT EXISTS `tbl_insuarance_item_prices` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scheme_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `financial_year` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosage` varchar(230) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` double NOT NULL,
  `active` tinyint(1) NOT NULL,
  `is_restricted` tinyint(1) DEFAULT NULL,
  `maximum_quantity` tinyint(1) DEFAULT NULL,
  `strength` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Database: `nhif`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_insuarance_items`
--

CREATE TABLE IF NOT EXISTS `tbl_insuarance_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_name` varchar(230) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gothomis_item_id` int(10) UNSIGNED NOT NULL,
  `item_type_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_insuarance_items`
--
ALTER TABLE `tbl_insuarance_items`
  ADD PRIMARY KEY  IF NOT EXISTS(`id`),
  ADD KEY  IF NOT EXISTS`tbl_insuarance_items_item_code_index` (`item_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_insuarance_items`
--
ALTER TABLE `tbl_insuarance_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2019 at 09:26 PM
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

-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2019 at 09:27 PM
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
-- Database: `nhif`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_insuarance_schemes`
--

CREATE TABLE IF NOT EXISTS `tbl_insuarance_schemes` (
  `id` int(10) UNSIGNED NOT NULL,
  `scheme_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scheme_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `insuarance_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_insuarance_schemes`
--
ALTER TABLE `tbl_insuarance_schemes`
  ADD PRIMARY KEY  IF NOT EXISTS(`id`),
  ADD KEY  IF NOT EXISTS`tbl_insuarance_schemes_scheme_code_index` (`scheme_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_insuarance_schemes`
--
ALTER TABLE `tbl_insuarance_schemes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

INSERT IGNORE INTO tbl_insuarance_schemes(id,scheme_code, scheme_name,insuarance_name) values (1,'1001','NORMAL','NHIF'), (2,'2001','BUNGE','NHIF');
--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_insuarance_item_prices`
--
ALTER TABLE `tbl_insuarance_item_prices`
  ADD PRIMARY KEY  IF NOT EXISTS(`id`),
  ADD KEY  IF NOT EXISTS`tbl_insuarance_item_prices_item_code_index` (`item_code`),
  ADD KEY  IF NOT EXISTS`tbl_insuarance_item_prices_scheme_code_index` (`scheme_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_insuarance_item_prices`
--
ALTER TABLE `tbl_insuarance_item_prices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2019 at 09:34 PM
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
-- Database: `nhif`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_insuarance_mapping_items`
--

CREATE TABLE IF NOT EXISTS `tbl_insuarance_mapping_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_type_id` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `strength` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `package_id` int(10) UNSIGNED DEFAULT NULL,
  `unit_price` decimal(7,2) DEFAULT NULL,
  `maximum_quantity` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dosage` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_restricted` tinyint(1) NOT NULL,
  `item_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_insuarance_mapping_items`
--
ALTER TABLE `tbl_insuarance_mapping_items`
  ADD PRIMARY KEY  IF NOT EXISTS(`id`),
  ADD KEY  IF NOT EXISTS`tbl_insuarance_mapping_items_item_id_foreign` (`item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_insuarance_mapping_items`
--
ALTER TABLE `tbl_insuarance_mapping_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2019 at 09:42 PM
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
-- Database: `nhif`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_temp_price_lists`
--

CREATE TABLE IF NOT EXISTS `tbl_temp_price_lists` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scheme_code` int(10) UNSIGNED DEFAULT NULL,
  `item_type_id` int(10) UNSIGNED DEFAULT NULL,
  `package_id` int(10) UNSIGNED DEFAULT NULL,
  `dosage` varchar(230) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `is_restricted` tinyint(1) DEFAULT NULL,
  `maximum_quantity` tinyint(1) DEFAULT NULL,
  `strength` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_temp_price_lists`
--
ALTER TABLE `tbl_temp_price_lists`
  ADD PRIMARY KEY  IF NOT EXISTS(`id`),
  ADD KEY  IF NOT EXISTS`tbl_temp_price_lists_item_code_index` (`item_code`),
  ADD KEY  IF NOT EXISTS`tbl_temp_price_lists_scheme_code_index` (`scheme_code`),
  ADD KEY  IF NOT EXISTS`tbl_temp_price_lists_item_type_id_index` (`item_type_id`),
  ADD KEY  IF NOT EXISTS`tbl_temp_price_lists_package_id_index` (`package_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_temp_price_lists`
--
ALTER TABLE `tbl_temp_price_lists`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2019 at 10:32 AM
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
-- Database: `nhif`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bulk_claims`
--

CREATE TABLE IF NOT EXISTS `tbl_bulk_claims` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `status` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_bulk_claims`
--
ALTER TABLE `tbl_bulk_claims`
  ADD PRIMARY KEY IF NOT EXISTS (`id`),
  ADD KEY IF NOT EXISTS`tbl_bulk_claims_user_id_foreign` (`user_id`),
  ADD KEY IF NOT EXISTS `tbl_bulk_claims_facility_id_foreign` (`facility_id`),
  ADD KEY IF NOT EXISTS `tbl_bulk_claims_account_id_foreign` (`account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_bulk_claims`
--
ALTER TABLE `tbl_bulk_claims`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_bulk_claims`
--
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;