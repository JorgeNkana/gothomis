alter table `tbl_encounter_invoices` ADD IF NOT EXISTS `BillId` varchar(250) null AFTER `id`;
alter table `tbl_encounter_invoices` ADD IF NOT EXISTS `PayCntrNum` varchar(250) null AFTER `BillId`;
alter table `tbl_encounter_invoices` ADD IF NOT EXISTS `Processed` tinyint(1) DEFAULT 0 AFTER `BillId`;

alter table `tbl_invoice_lines` ADD IF NOT EXISTS `gepg_receipt` varchar(50) null;


alter table gepg_bills add column if not exists `PyrId` varchar(50) NULL;
alter table gepg_bills add column if not exists `BillExprDt` varchar(50) NULL;
alter table gepg_bills add column if not exists `BillGenDt` varchar(50) NULL;
alter table gepg_bills add column if not exists `BillGenBy` varchar(50) NULL;
alter table gepg_bills add column if not exists `BillApprBy` varchar(50) NULL;
alter table gepg_bills add column if not exists `PyrCellNum` varchar(50) NULL;
alter table gepg_bills add column if not exists `PyrEmail` varchar(50) NULL;
alter table gepg_bills add column if not exists `BillDesc` varchar(350) NULL;
alter table gepg_bills add column if not exists `GfsCode` varchar(350) NULL;

ALTER TABLE `tbl_results` ADD column if not EXISTS  `units` VARCHAR(11) NULL;
ALTER TABLE `tbl_referrals` ADD column if not EXISTS `feedback` TEXT NULL;
ALTER TABLE `tbl_referrals` ADD column if not EXISTS `reg` TEXT NULL;
ALTER TABLE `tbl_referrals` ADD column if not EXISTS `referral_code` VARCHAR(20) NULL;

INSERT IGNORE INTO `tbl_admission_statuses` (`id`, `status_name`, `created_at`, `updated_at`) VALUES ('11', 'Referral', NULL, NULL);            
INSERT IGNORE INTO `tbl_admission_statuses` (`id`, `status_name`, `created_at`, `updated_at`) VALUES ('11', 'Referral', NULL, NULL);
INSERT IGNORE INTO `tbl_admission_statuses` (`id`, `status_name`, `created_at`, `updated_at`) VALUES ('12', 'Maternal Death', NULL, NULL);
INSERT IGNORE INTO `tbl_admission_statuses` (`id`, `status_name`, `created_at`, `updated_at`) VALUES ('13', 'Delivery', NULL, NULL);
INSERT IGNORE INTO `tbl_admission_statuses` (`id`, `status_name`, `created_at`, `updated_at`) VALUES ('14', 'FSB', NULL, NULL);
INSERT IGNORE INTO `tbl_admission_statuses` (`id`, `status_name`, `created_at`, `updated_at`) VALUES ('15', 'MSB', NULL, NULL);
INSERT IGNORE INTO `tbl_admission_statuses` (`id`, `status_name`, `created_at`, `updated_at`) VALUES ('16', 'Neonatal Death', NULL, NULL);

ALTER TABLE trauma_hpi_injury_mechanisms ADD  column if not exists none_road_trafic_incident BOOLEAN NULL ;
ALTER TABLE tbl_results ADD  column if not exists units varchar(11) NULL ;

ALTER TABLE `bills` CHANGE `price` `price` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `bills` CHANGE `discount` `discount`  DOUBLE NULL DEFAULT NULL;
ALTER TABLE `bills` CHANGE `quantity` `quantity` DOUBLE NULL DEFAULT NULL;


ALTER TABLE `tbl_orders`  ADD  column if not exists `visit_date_id` INT NOT NULL  AFTER `order_id`;
ALTER TABLE `tbl_results`  ADD  column if not exists `visit_date_id` INT NOT NULL  AFTER `order_id`;

UPDATE tbl_orders JOIN tbl_requests ON DATE(tbl_orders.created_at) = DATE(tbl_requests.created_at) AND tbl_orders.order_id = tbl_requests.id SET tbl_orders.visit_date_id = tbl_requests.visit_date_id;
UPDATE tbl_results JOIN tbl_orders ON DATE(tbl_orders.created_at) = DATE(tbl_results.created_at) AND tbl_orders.order_id = tbl_results.order_id AND tbl_results.item_id = tbl_orders.test_id SET tbl_results.visit_date_id = tbl_orders.visit_date_id;

ALTER TABLE `tbl_prescriptions` CHANGE `quantity` `quantity` DECIMAL(10,2) NULL DEFAULT NULL;
ALTER TABLE `tbl_invoice_lines` CHANGE `discount` `discount` DECIMAL(10,2) UNSIGNED NOT NULL;
ALTER TABLE `tbl_invoice_lines` CHANGE `price` `price` DECIMAL(10,2) NULL DEFAULT NULL;