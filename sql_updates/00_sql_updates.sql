drop table IF EXISTS `tbl_elmis_adjustments`;
CREATE TABLE IF NOT EXISTS `tbl_elmis_adjustments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `description` varchar(40) NOT NULL,
  `additive` tinyint(1) NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ;
INSERT IGNORE INTO `tbl_elmis_adjustments` (`id`, `code`, `description`, `additive`, `created_at`, `updated_at`) VALUES
(1, 'TRANSFER_IN', 'Transfer In', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(2, 'TRANSFER_OUT', 'Transfer Out', 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(3, 'DAMAGED', 'Damaged', 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(4, 'LOST', 'Lost', 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(5, 'STOLEN', 'Stolen', 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(6, 'EXPIRED', 'Expired', 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(7, 'PASSED_OPEN_VIAL_TIM', 'Passed Open-Vial Time Limit', 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(8, 'COLD_CHAIN_FAILURE', 'Cold Chain Failure', 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(9, 'CLINIC_RETURN', 'Clinic Return', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);


ALTER TABLE `tbl_transaction_types` ADD column if not EXISTS `description` varchar(20)  DEFAULT NULL ;
ALTER TABLE `tbl_transaction_types` ADD column if not EXISTS `adjustment` varchar(20)  DEFAULT NULL ;
ALTER TABLE `tbl_transaction_types` ADD column if not EXISTS `additive` tinyint(1)  DEFAULT NULL ;
ALTER TABLE `tbl_transaction_types` ADD column if not EXISTS `code` varchar(20)  DEFAULT NULL ;
ALTER TABLE `tbl_items` ADD column if not EXISTS `msd_product` BOOLEAN NOT NULL DEFAULT FALSE AFTER `item_name`;
ALTER TABLE tbl_trauma_vitals ADD COLUMN IF NOT EXISTs facility_id int(11) null;
ALTER TABLE tbl_trauma_clients ADD COLUMN IF NOT EXISTs facility_id int(11) null;
ALTER TABLE tbl_trauma_clients ADD COLUMN IF NOT EXISTs arrival_mode int(11) null;
ALTER TABLE tbl_trauma_clients ADD COLUMN IF NOT EXISTs arrival_date int(11) null;
ALTER TABLE tbl_trauma_clients ADD COLUMN IF NOT EXISTs next_kin_name varchar(50) null;
ALTER TABLE tbl_trauma_clients ADD COLUMN IF NOT EXISTs next_kin_phone varchar(50) null;
ALTER TABLE tbl_trauma_clients ADD COLUMN IF NOT EXISTs next_kin_relation varchar(50) null;
ALTER TABLE trauma_hpi_injury_mechanisms ADD COLUMN IF NOT EXISTs prehospital_care varchar(50) null;
ALTER TABLE  `tbl_trauma_clients` 
ADD COLUMN IF NOT EXISTS marital_status VARCHAR(12) NULL,
ADD COLUMN IF NOT EXISTS level_of_education VARCHAR(50) NULL,
ADD COLUMN IF NOT EXISTS occupation_of_patient VARCHAR(50) NULL;
  ALTER TABLE  `tbl_trauma_chief_complaints` 
ADD COLUMN IF NOT EXISTS mass_casualty boolean NULL;

ALTER TABLE `trauma_hpi_injury_mechanisms` CHANGE `road_traffic_acident` `road_traffic_acident` BOOLEAN NULL DEFAULT NULL;
ALTER TABLE `trauma_hpi_injury_mechanisms`  ADD COLUMN IF NOT  EXISTS  `driver` BOOLEAN NULL  AFTER `updated_at`,  ADD COLUMN IF NOT  EXISTS `passenger` BOOLEAN NULL  AFTER `driver`,  ADD COLUMN IF NOT  EXISTS `paedestrian` BOOLEAN NULL  AFTER `passenger`,  ADD COLUMN IF NOT  EXISTS `airbag` BOOLEAN NULL  AFTER `paedestrian`,  ADD COLUMN IF NOT  EXISTS `seat_belt` BOOLEAN NULL  AFTER `airbag`,  ADD COLUMN IF NOT  EXISTS `other_vehicle_restraint` BOOLEAN NULL  AFTER `seat_belt`,  ADD COLUMN IF NOT  EXISTS `helment` BOOLEAN NULL  AFTER `other_vehicle_restraint`,  ADD COLUMN IF NOT  EXISTS `fall` BOOLEAN NULL  AFTER `helment`;
ALTER TABLE `trackables` CHANGE `patient_id` `patient_id` INT(11) NULL;
ALTER TABLE tbl_prescriptions  ADD column if not exists continuation_status VARCHAR(12) NULL  AFTER dispensing_status;
ALTER TABLE tbl_prescriptions  ADD column if not exists stoped_by int(11) NULL  AFTER dispensing_status;
alter table tbl_continuation_notes add column if not exists visit_id int(10) after notes;
alter table tbl_continuation_notes add column if not exists notes_type int(10) after visit_id;
ALTER TABLE `tbl_prescriptions` change `quantity` `quantity` int NULL;
ALTER TABLE `tbl_prescriptions` ADD column if not exists `conservatives` TEXT NULL AFTER `cancellation_reason`;
ALTER TABLE `tbl_prescriptions` CHANGE `item_id` `item_id` INT(10) UNSIGNED NULL;
ALTER TABLE `tbl_physical_examination_records` add column if not exists  `summary_examination`   TEXT  NULL;
ALTER TABLE `tbl_physical_examination_records` ADD column if not exists `other_systems_summary` TEXT NULL AFTER `summary_examination`;
ALTER TABLE `tbl_complaints` CHANGE `other_complaints` `other_complaints` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `hpi` `hpi` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `tbl_review_of_systems` CHANGE `review_summary` `review_summary` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `tbl_physical_examination_records` CHANGE `local_examination` `local_examination` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `gen_examination` `gen_examination` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `summary_examination` `summary_examination` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `tbl_past_medical_records` CHANGE `surgeries` `surgeries` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `admissions` `admissions` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `transfusion` `transfusion` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `immunisation` `immunisation` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `tbl_obs_gyn_records` CHANGE `menarche` `menarche` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `menopause` `menopause` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `menstrual_cycles` `menstrual_cycles` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `std` `std` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `abortions` `abortions` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `contraceptives` `contraceptives` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `due_date` `due_date` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `lnmp` `lnmp` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `gravidity` `gravidity` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `parity` `parity` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `living_children` `living_children` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `gestational_age` `gestational_age` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `category` `category` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `tbl_inputs` CHANGE `type_iv` `type_iv` INT(10) UNSIGNED NULL, CHANGE `type_oral` `type_oral` INT(10) UNSIGNED NULL;
ALTER TABLE `tbl_referrals` add column if not exists  `visit_id` INT(10) UNSIGNED NULL;
ALTER TABLE `tbl_obs_gyn_records` ADD IF NOT EXISTS `cycle` TEXT NULL AFTER `abortions`, ADD IF NOT EXISTS `period` TEXT NULL AFTER `cycle`;
 
alter table tbl_corpses add column if not exists status tinyint;
alter table tbl_residences change residence_name residence_name varchar(150);



ALTER TABLE tbl_accounts_numbers DROP INDEX IF EXISTS `account_number`;

ALTER TABLE tbl_accounts_numbers DROP INDEX if exists  tbl_accounts_numbers_account_number_unique;

alter table tbl_encounter_invoices add column if not exists corpse_id int(10);
ALTER TABLE `tbl_opd_nursings` ADD  column if not exists  `duration` VARCHAR(12) NOT NULL AFTER `visit_id`;

ALTER TABLE `tbl_post_natal_child_infections` ADD column if not exists `gender` VARCHAR(7) NOT NULL AFTER `high_infection`;
ALTER TABLE `tbl_post_natal_child_feedings` ADD column if not exists `gender` VARCHAR(7) NOT NULL AFTER `feeding_type`;



ALTER TABLE `tbl_labour_newborns`  ADD COLUMN if not EXISTS `newborn_weight` VARCHAR(11) NOT NULL  AFTER `gender`;
ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `corpse_taken_by` VARCHAR(120) NULL DEFAULT NULL AFTER `mobile_number` ;

ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `corpse_brought_by` VARCHAR(120) NULL DEFAULT NULL AFTER `mobile_number` ;

ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `corpse_conditions` VARCHAR(120) NULL DEFAULT NULL AFTER `mobile_number` ;

ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `corpse_properties` VARCHAR(120) NULL DEFAULT NULL AFTER `mobile_number` ;

ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `transport_taking` VARCHAR(120) NULL DEFAULT NULL AFTER `mobile_number` ;

ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `identity_number_taker` VARCHAR(120) NULL DEFAULT NULL AFTER `mobile_number` ;

ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `identity_type_taker` VARCHAR(120) NULL DEFAULT NULL AFTER `mobile_number` ;

ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `residence_found` INT(11) NULL DEFAULT NULL AFTER `mobile_number` , ADD INDEX IF NOT EXISTS `res` (`residence_found`);

ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `discharge_info_by` INT(11) NULL DEFAULT NULL AFTER `mobile_number` , ADD INDEX IF NOT EXISTS `dischargeinfoby` (`discharge_info_by`);

ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `status` INT(1) NULL DEFAULT NULL AFTER `mobile_number` ;

ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `residence_taker` INT(11) NULL DEFAULT NULL AFTER `mobile_number` , ADD INDEX IF NOT EXISTS `residencetaker` (`residence_taker`);

ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `funeral_site_id` INT(11) NULL DEFAULT NULL AFTER `mobile_number` ;

ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `description` TEXT NULL DEFAULT NULL AFTER `mobile_number` ;
ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `diagnosis_code` TEXT NULL DEFAULT NULL AFTER `mobile_number` ;
ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `diagnosis_id` TEXT NULL DEFAULT NULL AFTER `mobile_number` ;

ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `relationship_taker` INT(11) NULL DEFAULT NULL AFTER `mobile_number` ;

UPDATE tbl_permissions t1 SET t1.module='theatre_doctor' WHERE t1.id=83;


ALTER TABLE `tbl_item_prices` ADD column if not exists`onetime` TINYINT(1) NOT NULL DEFAULT '0' AFTER `exemption_status`;

ALTER TABLE `tbl_item_prices` ADD column if not exists `insurance` TINYINT(1) NOT NULL DEFAULT '1' AFTER `exemption_status`;

-- this was intentionally fixed with ID
INSERT IGNORE INTO `tbl_departments` (`id`, `department_name`, `created_at`, `updated_at`) VALUES ('100', 'Medical Clinic', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
 
INSERT IGNORE INTO `tbl_pay_cat_sub_categories` (`id`, `sub_category_name`, `pay_cat_id`, `created_at`, `updated_at`) 
VALUES ('50', 'CHF', '2',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
ALTER TABLE `tbl_pay_cat_sub_categories` auto_increment = 51;
 

ALTER TABLE `tbl_invoice_lines` CHANGE `quantity` `quantity` DECIMAL(10,2) UNSIGNED NOT NULL;
ALTER TABLE `tbl_item_prices` CHANGE `onetime` `onetime` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0', CHANGE `insurance` `insurance` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1';

-- USE THIS APPROACH WHENEVER ADDING SOMETHING TO USER'S TABLES
-- DO NOT FIX THE ID, YOU NEVER KNOW IF ITS ALREADY USED
INSERT IGNORE INTO `tbl_store_lists` (`id`, `store_name`, `store_type_id`, `facility_id`, `created_at`, `updated_at`) select 100, 'HOSPITAL SHOP DISPENSING', '4', facility_id, NULL, NULL from tbl_patients order by id desc limit 1;


ALTER TABLE `tbl_inputs` CHANGE `type_iv` `type_iv` INT(10) UNSIGNED NULL DEFAULT NULL, CHANGE `type_oral` `type_oral` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `tbl_receiving_items` 
ADD column if not exists  `amount_issued`  INT NULL AFTER `control_in`, ADD column if not exists  `positive_adjustment`  INT NULL AFTER `amount_issued`,
ADD  column if not exists `negative_adjustment` INT NULL AFTER `positive_adjustment`, 
ADD column if not exists `amount_available`  INT NULL AFTER `negative_adjustment`;

ALTER TABLE `tbl_eye_examination_records` ADD IF NOT EXISTS `sphere` TEXT NULL AFTER `non_perception_light`, ADD IF NOT EXISTS `cylinder` TEXT NULL AFTER `sphere`, ADD IF NOT EXISTS `axis` TEXT NULL AFTER `cylinder`, ADD IF NOT EXISTS `v_a` TEXT NULL AFTER `axis`, ADD IF NOT EXISTS `p_d` TEXT NULL AFTER `v_a`, ADD IF NOT EXISTS `a_d_d` TEXT NULL AFTER `p_d`;

 UPDATE `tbl_pay_cat_sub_categories` SET `sub_category_name` = 'HOSPITAL SHOP', `pay_cat_id` = '1' WHERE `tbl_pay_cat_sub_categories`.`id` = 10;
INSERT ignore INTO `tbl_nutritional_suppliments` (`id`, `suppliment_name`, `created_at`, `updated_at`) VALUES
(1, 'F-75', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(2, 'F-100', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(3, 'new RUTF', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(4, 'continuing RUTF', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(5, 'new RuSF', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(6, 'continuing RuSF', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
ALTER TABLE `tbl_item_prices` ADD if not exists `status` INT NOT NULL DEFAULT '1' AFTER `endingFinancialYear`;
ALTER TABLE  `tbl_blood_requests`  ADD COLUMN if NOT EXISTS `bag_no` VARCHAR(45) NULL  AFTER `status`;
ALTER TABLE `tbl_anaethetic_records` CHANGE `admission_id` `admission_id` INT(10) UNSIGNED NULL;

ALTER TABLE `tbl_eye_examination_records` ADD IF NOT EXISTS `sphere` TEXT NULL AFTER `non_perception_light`;
ALTER TABLE `tbl_eye_examination_records` ADD IF NOT EXISTS `cylinder` TEXT NULL AFTER `sphere`;

ALTER TABLE `tbl_facilities`
    ADD KEY IF NOT EXISTS `facility_codes_key` (`facility_code`);
ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `storage_reason`  VARCHAR(50) NULL;
ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `description` VARCHAR(200) NULL;
ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `corpse_brought_by` VARCHAR(100) NULL;

ALTER TABLE `tbl_eye_examination_records` ADD IF NOT EXISTS `axis` TEXT NULL AFTER `cylinder`;
ALTER TABLE `tbl_eye_examination_records` ADD IF NOT EXISTS `v_a` TEXT NULL AFTER `axis`;
ALTER TABLE `tbl_eye_examination_records` ADD IF NOT EXISTS `p_d` TEXT NULL AFTER `v_a`;
ALTER TABLE `tbl_eye_examination_records` ADD IF NOT EXISTS `a_d_d` TEXT NULL AFTER `p_d`;
ALTER TABLE `users` ADD IF NOT EXISTS `new_user` INT(1) NULL DEFAULT '1' AFTER `updated_at`;
ALTER TABLE `tbl_corpses` ADD column if not exists `diagnosis_id` INT(11) NULL AFTER `storage_reason`, ADD column if not exists `diagnosis_code` VARCHAR(50) NULL AFTER `diagnosis_id`;
ALTER TABLE `tbl_patient_procedures` ADD IF NOT EXISTS `status` INT(2) DEFAULT 0;
ALTER TABLE `tbl_patient_procedures` CHANGE `admission_id` `admission_id` INT(10) UNSIGNED NULL;
ALTER TABLE `tbl_anaethetic_records` CHANGE `admission_id` `admission_id` INT(10) UNSIGNED NULL;
ALTER TABLE `tbl_informed_consents` CHANGE `admission_id` `admission_id` INT(10) UNSIGNED NULL;
ALTER TABLE `tbl_status_procedures` CHANGE `admission_id` `admission_id` INT(10) UNSIGNED NULL;
ALTER TABLE `tbl_pre_history_anethetics` CHANGE `admission_id` `admission_id` INT(10) UNSIGNED NULL;
ALTER TABLE `tbl_patient_procedures` CHANGE `status` `status` INT(1) DEFAULT 0;


INSERT IGNORE INTO `tbl_drf_categories` (`id`, `category_name`, `created_at`, `updated_at`) VALUES
(1, 'COST SHARING', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(2, 'WHOLE SALE', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

 



 ALTER TABLE `tbl_product_prices` ADD COLUMN IF NOT EXISTS `category` TEXT NOT NULL AFTER `item_name`;
ALTER TABLE `tbl_sales` ADD COLUMN IF NOT EXISTS `auth_no` TEXT NULL AFTER `batch_number`, ADD COLUMN IF NOT EXISTS `nhif_id` TEXT NULL AFTER `auth_no`;
ALTER TABLE `tbl_stocks`  ADD COLUMN IF NOT EXISTS `received_date` DATE NULL  AFTER `item_id`;

ALTER TABLE `tbl_ukatilis` 
ADD column if   not exists `incoming_referral` VARCHAR(12) NULL AFTER `updated_at`, 
ADD column if   not exists `internal_referral` VARCHAR(11) NULL AFTER `incoming_referral`,
 ADD column if   not exists `outgoing_referral` VARCHAR(11) NULL AFTER `internal_referral`,
 ADD column if   not exists `dept_name` VARCHAR(10) NULL AFTER `outgoing_referral`, 
 ADD column if  not exists `incoming_from` VARCHAR(11) NULL AFTER `dept_name`;