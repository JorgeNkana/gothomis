-- MySQL dump 10.16  Distrib 10.1.16-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: test_seeder
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
-- Table structure for table `tbl_opd_mtuha_icd_blocks`
--

DROP TABLE IF EXISTS `tbl_opd_mtuha_icd_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_opd_mtuha_icd_blocks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `icd_block` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opd_mtuha_diagnosis_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_opd_mtuha_icd_blocks_opd_mtuha_diagnosis_id_foreign` (`opd_mtuha_diagnosis_id`),
  CONSTRAINT `tbl_opd_mtuha_icd_blocks_opd_mtuha_diagnosis_id_foreign` FOREIGN KEY (`opd_mtuha_diagnosis_id`) REFERENCES `tbl_opd_mtuha_diagnoses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_opd_mtuha_icd_blocks`
--

LOCK TABLES `tbl_opd_mtuha_icd_blocks` WRITE;
/*!40000 ALTER TABLE `tbl_opd_mtuha_icd_blocks` DISABLE KEYS */;

INSERT INTO `tbl_opd_mtuha_icd_blocks` (`id`, `icd_block`, `opd_mtuha_diagnosis_id`, `created_at`, `updated_at`) VALUES
(1, 'G81', 1, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(2, 'G82', 1, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(3, 'G83', 1, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(4, 'A00', 2, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(5, 'A09', 3, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(6, 'B05', 4, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(7, 'G03', 5, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(8, 'A33', 6, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(9, 'A20', 7, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(10, 'A68', 8, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(11, 'A95', 9, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(12, 'J11', 10, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(13, 'A01', 11, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(14, 'A82', 12, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(15, 'B66', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(16, 'B67', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(17, 'B68', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(18, 'B69', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(19, 'B70', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(20, 'B71', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(21, 'B72', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(22, 'B73', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(23, 'B74', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(24, 'B75', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(25, 'B76', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(26, 'B77', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(27, 'B78', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(28, 'B79', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(29, 'B80', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(30, 'B81', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(31, 'B83', 13, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(32, 'B56', 14, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(33, 'B57', 14, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(34, 'A90', 15, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(35, 'A91', 15, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(36, 'A92', 15, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(37, 'A93', 15, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(38, 'A94', 15, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(39, 'A96', 15, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(40, 'A97', 15, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(41, 'A98', 15, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(42, 'A99', 15, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(43, NULL, 16, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(44, 'A09.0', 17, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(45, NULL, 18, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(46, 'B65', 19, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(47, NULL, 20, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(48, 'B53', 21, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(49, 'B54', 22, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(50, NULL, 23, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(51, 'A54', 24, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(52, 'A53', 25, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(53, 'N70', 26, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(54, 'N71', 26, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(55, 'N72', 26, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(56, 'N73', 26, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(57, 'N74', 26, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(58, 'N75', 26, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(59, 'N76', 26, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(60, 'N77', 26, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(61, 'N64', 27, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(62, 'A15', 28, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(63, 'A16', 28, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(64, 'A17', 28, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(65, 'A18', 28, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(66, 'A19', 28, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(67, 'A30', 29, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(68, 'B82', 30, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(69, 'D60', 31, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(70, 'D61', 31, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(71, 'D62', 31, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(72, 'D63', 31, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(73, 'D64', 32, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(74, 'D57', 33, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(75, 'H65', 34, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(76, 'H67', 34, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(77, 'H68', 34, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(78, 'H69', 34, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(79, 'H70', 34, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(80, 'H71', 34, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(81, 'H72', 34, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(82, 'H73', 34, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(83, 'H74', 34, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(84, 'H75', 34, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(85, 'H66', 35, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(86, 'H10', 36, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(87, 'H11', 36, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(88, 'H12', 36, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(89, 'H13', 36, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(90, 'H25', 37, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(91, 'H26', 37, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(92, 'H27', 37, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(93, 'H28', 37, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(94, 'S05', 38, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(95, 'L08', 39, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(96, 'B35', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(97, 'B36', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(98, 'B37', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(99, 'B38', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(100, 'B39', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(101, 'B40', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(102, 'B41', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(103, 'B42', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(104, 'B43', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(105, 'B44', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(106, 'B45', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(107, 'B46', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(108, 'B47', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(109, 'B48', 40, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(110, 'L80', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(111, 'L81', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(112, 'L82', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(113, 'L83', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(114, 'L84', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(115, 'L85', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(116, 'L86', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(117, 'L87', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(118, 'L88', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(119, 'L89', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(120, 'L90', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(121, 'L91', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(122, 'L92', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(123, 'L93', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(124, 'L94', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(125, 'L95', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(126, 'L96', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(127, 'L97', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(128, 'L98', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(129, 'L99', 41, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(130, 'B49', 42, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(131, 'M80', 43, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(132, 'M81', 43, '2018-07-13 09:45:21', '2018-07-13 09:45:21'),
(133, 'M82', 43, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(134, 'M83', 43, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(135, 'M84', 43, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(136, 'M85', 43, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(137, 'M86', 43, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(138, 'M87', 43, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(139, 'M88', 43, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(140, 'M89', 43, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(141, 'M90', 43, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(142, 'M91', 43, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(143, 'M92', 43, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(144, 'M93', 43, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(145, 'M94', 43, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(146, 'P36', 44, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(147, 'P07', 45, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(148, 'P21', 46, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(149, 'J18', 47, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(150, 'J12', 48, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(151, 'J13', 48, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(152, 'J14', 48, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(153, 'J15', 48, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(154, 'J16', 48, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(155, 'J17', 48, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(156, 'J00', 49, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(157, 'J01', 49, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(158, 'J02', 49, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(159, 'J03', 49, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(160, 'J04', 49, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(161, 'J05', 49, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(162, 'J06', 49, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(163, 'G80', 50, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(164, 'N30', 51, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(165, 'N31', 51, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(166, 'N32', 51, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(167, 'N33', 51, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(168, 'N34', 51, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(169, 'N35', 51, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(170, 'N36', 51, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(171, 'N37', 51, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(172, 'N38', 51, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(173, 'N39', 51, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(174, 'N80', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(175, 'N81', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(176, 'N82', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(177, 'N83', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(178, 'N84', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(179, 'N85', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(180, 'N86', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(181, 'N87', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(182, 'N88', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(183, 'N89', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(184, 'N90', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(185, 'N91', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(186, 'N92', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(187, 'N93', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(188, 'N94', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(189, 'N95', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(190, 'N96', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(191, 'N97', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(192, 'N98', 52, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(193, 'E40', 53, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(194, 'E41', 54, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(195, 'E42', 55, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(196, 'E44', 56, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(197, 'E50', 57, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(198, 'E46', 58, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(199, 'K00', 59, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(200, 'K01', 59, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(201, 'K02', 59, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(202, 'K03', 59, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(203, 'K04', 59, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(204, 'K06', 59, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(205, 'K07', 59, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(206, 'K09', 59, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(207, 'K10', 59, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(208, 'K11', 59, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(209, 'K12', 59, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(210, 'K13', 59, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(211, 'K14', 59, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(212, 'K05', 60, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(213, 'S03', 61, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(214, 'K08', 62, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(215, 'T08', 63, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(216, 'T09', 63, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(217, 'T10', 63, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(218, 'T11', 63, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(219, 'T12', 63, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(220, 'T13', 63, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(221, 'T14', 63, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(222, 'T20', 64, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(223, 'T21', 64, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(224, 'T22', 64, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(225, 'T23', 64, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(226, 'T24', 64, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(227, 'T25', 64, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(228, 'T26', 64, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(229, 'T27', 64, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(230, 'T28', 64, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(231, 'T29', 64, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(232, 'T30', 64, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(233, 'T31', 64, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(234, 'T32', 64, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(235, 'T51', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(236, 'T52', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(237, 'T53', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(238, 'T54', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(239, 'T55', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(240, 'T56', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(241, 'T57', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(242, 'T58', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(243, 'T59', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(244, 'T60', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(245, 'T61', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(246, 'T62', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(247, 'T64', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(248, 'T65', 65, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(249, 'V89', 66, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(250, 'O20', 67, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(251, 'O21', 67, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(252, 'O22', 67, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(253, 'O23', 67, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(254, 'O24', 67, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(255, 'O25', 67, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(256, 'O26', 67, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(257, 'O27', 67, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(258, 'O28', 67, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(259, 'O29', 67, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(260, 'O03', 68, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(261, 'O04', 68, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(262, 'O05', 68, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(263, 'O06', 68, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(264, 'T63', 69, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(265, 'W54', 70, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(266, 'W53', 71, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(267, 'W55', 71, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(268, 'W56', 71, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(269, 'W57', 71, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(270, 'W58', 71, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(271, 'W59', 71, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(272, NULL, 72, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(273, 'N40', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(274, 'N41', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(275, 'N42', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(276, 'N43', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(277, 'N44', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(278, 'N45', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(279, 'N46', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(280, 'N47', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(281, 'N48', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(282, 'N49', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(283, 'N50', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(284, 'N51', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(285, 'K40', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(286, 'K41', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(287, 'K42', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(288, 'K43', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(289, 'K44', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(290, 'K45', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(291, 'K46', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(292, 'L03', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(293, 'K56', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(294, 'K37', 73, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(295, 'G40', 74, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(296, 'F29', 75, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(297, 'F40', 76, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(298, 'F41', 76, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(299, 'F42', 76, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(300, 'F43', 76, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(301, 'F44', 76, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(302, 'F45', 76, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(303, 'F46', 76, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(304, 'F47', 76, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(305, 'F48', 76, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(306, 'F10', 77, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(307, 'F11', 77, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(308, 'F12', 77, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(309, 'F13', 77, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(310, 'F14', 77, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(311, 'F15', 77, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(312, 'F16', 77, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(313, 'F17', 77, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(314, 'F18', 77, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(315, 'F19', 77, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(316, 'I10', 78, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(317, 'I11', 78, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(318, 'I12', 78, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(319, 'I13', 78, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(320, 'I14', 78, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(321, 'I15', 78, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(322, 'I00', 79, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(323, 'I01', 79, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(324, 'I02', 79, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(325, 'I20', 80, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(326, 'I21', 80, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(327, 'I22', 80, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(328, 'I23', 80, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(329, 'I24', 80, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(330, 'I25', 80, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(331, 'I50', 80, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(332, 'I51', 80, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(333, 'J45', 81, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(334, 'K27', 82, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(335, 'K90', 83, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(336, 'K91', 83, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(337, 'K92', 83, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(338, 'K93', 83, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(339, 'E10', 84, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(340, 'E11', 84, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(341, 'E12', 84, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(342, 'E13', 84, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(343, 'E14', 84, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(344, 'M00', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(345, 'M01', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(346, 'M02', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(347, 'M03', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(348, 'M04', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(349, 'M05', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(350, 'M06', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(351, 'M07', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(352, 'M08', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(353, 'M09', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(354, 'M10', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(355, 'M11', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(356, 'M12', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(357, 'M13', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(358, 'M14', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(359, 'M15', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(360, 'M16', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(361, 'M17', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(362, 'M18', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(363, 'M19', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(364, 'M20', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(365, 'M21', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(366, 'M22', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(367, 'M23', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(368, 'M24', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(369, 'M25', 85, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(370, 'E00', 86, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(371, 'E01', 86, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(372, 'E02', 86, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(373, 'E03', 86, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(374, 'E04', 86, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(375, 'E05', 86, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(376, 'E06', 86, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(377, 'E07', 86, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(378, 'C15', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(379, 'C16', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(380, 'C17', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(381, 'C18', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(382, 'C19', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(383, 'C20', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(384, 'C21', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(385, 'C22', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(386, 'C23', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(387, 'C24', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(388, 'C25', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(389, 'C26', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(390, 'C50', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(391, 'C51', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(392, 'C52', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(393, 'C53', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(394, 'C54', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(395, 'C55', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(396, 'C56', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(397, 'C57', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(398, 'C58', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(399, 'C60', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(400, 'C61', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(401, 'C62', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(402, 'C63', 87, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(403, NULL, 88, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(404, 'OP4', 1, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(405, 'OP6', 3, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(406, 'OP8', 5, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(407, 'OP9', 6, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(408, 'OP13', 10, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(409, 'OP14', 11, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(410, 'OP17', 14, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(411, 'OP18', 15, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(412, 'OP19', 16, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(413, 'OP20', 17, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(414, 'OP21', 18, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(415, 'OP22', 19, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(416, 'OP23.1', 20, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(417, 'OP23.2', 21, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(418, 'OP23.3', 22, '2018-07-13 09:45:22', '2018-07-13 09:45:22'),
(419, 'OP23.4', 23, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(420, 'OP24', 24, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(421, 'OP25', 25, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(422, 'OP26', 26, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(423, 'OP27', 27, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(424, 'OP28', 28, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(425, 'OP29', 29, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(426, 'OP30', 30, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(427, 'OP31', 31, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(428, 'OP32', 32, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(429, 'OP33', 33, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(430, 'OP34', 34, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(431, 'OP35', 35, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(432, 'OP36', 36, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(433, 'OP37', 37, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(434, 'OP38', 38, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(435, 'OP39', 39, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(436, 'OP40', 40, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(437, 'OP41', 41, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(438, 'OP42', 42, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(439, 'OP44', 44, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(440, 'OP45', 45, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(441, 'OP47', 47, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(442, 'OP48', 48, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(443, 'OP49', 49, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(444, 'OP50', 50, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(445, 'OP51', 51, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(446, 'OP52', 52, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(447, 'OP54', 54, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(448, 'OP56', 56, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(449, 'OP58', 58, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(450, 'OP59', 59, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(451, 'OP60', 60, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(452, 'OP61', 61, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(453, 'OP62', 62, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(454, 'OP63', 63, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(455, 'OP64', 64, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(456, 'OP65', 65, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(457, 'OP66', 66, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(458, 'OP67', 67, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(459, 'OP69', 69, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(460, 'OP70', 70, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(461, 'OP71', 71, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(462, 'OP72', 72, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(463, 'OP73', 73, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(464, 'OP75', 75, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(465, 'OP76', 76, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(466, 'OP77', 77, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(467, 'OP78', 78, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(468, 'OP79', 79, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(469, 'OP80', 80, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(470, 'OP81', 81, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(471, 'OP82', 82, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(472, 'OP83', 83, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(473, 'OP84', 84, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(474, 'OP85', 85, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(475, 'OP86', 86, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(476, 'OP87', 87, '2018-07-13 09:45:23', '2018-07-13 09:45:23'),
(477, 'OP88', 88, '2018-07-13 09:45:23', '2018-07-13 09:45:23');
/*!40000 ALTER TABLE `tbl_opd_mtuha_icd_blocks` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-12-23 18:28:11