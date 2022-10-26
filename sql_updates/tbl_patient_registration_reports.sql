ALTER TABLE `tbl_facilities`
    ADD KEY IF NOT EXISTS `facility_codes_key` (`facility_code`);
ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `storage_reason`  VARCHAR(50) NULL;
ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `description` VARCHAR(200) NULL;
ALTER TABLE `tbl_corpses` ADD COLUMN IF NOT EXISTS `corpse_brought_by` VARCHAR(100) NULL;
CREATE TABLE IF NOT EXISTS `tbl_patient_registration_reports` (
  `facility_code` varchar(25)  NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
);

ALTER TABLE `tbl_patient_registration_reports`
  ADD KEY IF NOT EXISTS `tbl_patient_registration_reports_facility_code_foreign` (`facility_code`);

CREATE TABLE IF NOT EXISTS `tbl_reatend_patient_reports` (
   `facility_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ;

ALTER TABLE `tbl_reatend_patient_reports`
    ADD KEY IF NOT EXISTS `tbl_reatend_patiAent_reports_facility_code_foreign` (`facility_code`);