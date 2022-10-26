-- MySQL dump 10.16  Distrib 10.1.16-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: uuid
-- ------------------------------------------------------
-- Server version	10.1.16-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbl_outgoing_referral_registers`
--



/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `tbl_outgoing_referral_registers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facility_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `male_under_one_month` int(11) NOT NULL DEFAULT '0',
  `female_under_one_month` int(11) NOT NULL DEFAULT '0',
  `total_under_one_month` int(11) NOT NULL DEFAULT '0',
  `male_under_one_year` int(11) NOT NULL DEFAULT '0',
  `female_under_one_year` int(11) NOT NULL DEFAULT '0',
  `total_under_one_year` int(11) NOT NULL DEFAULT '0',
  `male_under_five_year` int(11) NOT NULL DEFAULT '0',
  `female_under_five_year` int(11) NOT NULL DEFAULT '0',
  `total_under_five_year` int(11) NOT NULL DEFAULT '0',
  `male_above_five_under_sixty` int(11) NOT NULL DEFAULT '0',
  `female_above_five_under_sixty` int(11) NOT NULL DEFAULT '0',
  `total_above_five_under_sixty` int(11) NOT NULL DEFAULT '0',
  `male_above_sixty` int(11) NOT NULL DEFAULT '0',
  `female_above_sixty` int(11) NOT NULL DEFAULT '0',
  `total_above_sixty` int(11) NOT NULL DEFAULT '0',
  `total_male` int(11) NOT NULL DEFAULT '0',
  `total_female` int(11) NOT NULL DEFAULT '0',
  `grand_total` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_outgoing_referral_registers_facility_id_foreign` (`facility_id`),
  CONSTRAINT `tbl_outgoing_referral_registers_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `tbl_facilities` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_outgoing_referral_registers`
--

LOCK TABLES `tbl_outgoing_referral_registers` WRITE;
/*!40000 ALTER TABLE `tbl_outgoing_referral_registers` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_outgoing_referral_registers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-20 15:17:32