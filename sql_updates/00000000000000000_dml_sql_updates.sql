alter table tbl_cash_deposits change paid_at paid_at TIMESTAMP default CURRENT_TIMESTAMP;
alter table tbl_prescriptions add column if not exists `cancellation_reason` varchar(500);
alter table tbl_cash_deposits add column if not exists `GfsCode` varchar(50) null;
alter table gepg_bills add column if not exists `Amount` varchar(50) null;
alter table gepg_bills add column if not exists `GfsCode` varchar(50) null;
alter table gepg_bills add column if not exists `BillItemRef` varchar(150) null;
ALTER TABLE `tbl_corpses` ADD column if not exists `storage_reason` VARCHAR(250) NULL;
ALTER TABLE `tbl_wards` ADD column if not exists `ward_type_code` VARCHAR(250) NULL;

alter table tbl_invoice_lines ADD COLUMN IF NOT EXISTS cancelling_reason TEXT NULL;

ALTER TABLE `tbl_admissions`  ADD  column if not exists `discharge_summary` TEXT NULL  AFTER `user_id`;
ALTER TABLE `tbl_admissions`  ADD  column if not exists `discharged_by` TEXT NULL  AFTER `user_id`;

ALTER TABLE `tbl_items` DROP COLUMN IF EXISTS `nhif_mapping_id`;
ALTER TABLE `tbl_item_type_mappeds` DROP COLUMN IF EXISTS  `nhif_mapping_id`;


CREATE TABLE IF NOT EXISTS tbl_insuarance_item_mapping(
Id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
gothomis_item_id INT NOT NULL, 
nhif_item_id INT NOT NULL
);

INSERT INTO tbl_insuarance_item_mapping(gothomis_item_id, nhif_item_id) SELECT gothomis_item_id, id FROM tbl_insuarance_items t1 WHERE gothomis_item_id <> 0 AND NOT EXISTS(SELECT Id FROM tbl_insuarance_item_mapping WHERE nhif_item_id = t1.id);

CREATE TABLE IF NOT EXISTS `tbl_patient_visit_serials` (
  `id` int(10) UNSIGNED NOT NULL,
  `serial_number` int(10) UNSIGNED DEFAULT NULL,
  `month_of_visit` int(10) UNSIGNED NOT NULL,
  `year_of_visit` int(10) UNSIGNED NOT NULL,
  `visit_id` int(10) UNSIGNED NOT NULL,
  `medical_record_number` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;