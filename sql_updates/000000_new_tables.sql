CREATE TABLE IF NOT EXISTS `tbl_lab_test_lives` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `facility_id` int(11) UNSIGNED NOT NULL,
  `days` tinyint(4) NOT NULL DEFAULT '4',
  `description` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `tbl_rnr_orders` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullSupply` tinyint(1) NOT NULL DEFAULT '1',
  `emergency` tinyint(1) NOT NULL DEFAULT '0',
  `programCode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_status` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `facilityCode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantityDispensed` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `quantityReceived` int(10) UNSIGNED NOT NULL,
  `beginningBalance` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `stockInHand` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `adjustment` int(10) NOT NULL DEFAULT '0',
  `stockOutDays` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `quantityRequested` int(10) UNSIGNED DEFAULT '0',
  `amountNeeded` int(12) NOT NULL DEFAULT '0',
  `reasonForRequestedQuantity` text COLLATE utf8mb4_unicode_ci,
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL ,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `tbl_rnr_order_controls` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `facilityCode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `tbl_elmis_item_program_mappings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `program_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `tbl_elmis_prices` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_price` double NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `tbl_stock_reconsilliations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `old_quantity` int(11) NOT NULL,
  `current_quantity` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `store_type_id` int(11) NOT NULL,
  `batch_no` varchar(11) NOT NULL,
  `reason` text NOT NULL,
  `column_id` int(11) NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 
 CREATE TABLE IF NOT EXISTS `tbl_tb_leprosy_requests` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `dtlc_email` varchar(25) DEFAULT NULL,
  `dtlc_name` varchar(25) DEFAULT NULL,
  `hiv_status` varchar(20) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `pre_tb_treatment` varchar(5) NOT NULL,
  `reason_for_examination` varchar(18) NOT NULL,
  `month_on_treatment` varchar(20) DEFAULT NULL,
  `rtlc_email` varchar(25) DEFAULT NULL,
  `rtlc_name` varchar(23) DEFAULT NULL,
  `specimen_type` varchar(15) NOT NULL,
  `test_requested` varchar(16) NOT NULL,
  `user_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `status` varchar(12) DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

 
 
CREATE TABLE IF NOT EXISTS `tbl_tb_leprosy_results` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `comment` text,
  `ear_lobe` varchar(50) DEFAULT NULL,
  `laboratory_serial_no` varchar(45) DEFAULT NULL,
  `lesion` varchar(50) DEFAULT NULL,
  `reception_date` datetime NOT NULL,
  `result` varchar(11) NOT NULL,
  `specimen` varchar(20) NOT NULL,
  `zn_fm` varchar(5) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `appearance` varchar(50) DEFAULT NULL,
  `request_id` int(11) NOT NULL,
  `status` varchar(12) DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 
 
 

CREATE TABLE IF NOT EXISTS tbl_theatre_services(
		id INT(11) NOT NULL AUTO_INCREMENT,
		service_type INT(1),
		procedure_category INT(1),
		item_id INT,
		created_at TIMESTAMP NULL,
		updated_at TIMESTAMP NULL,
		PRIMARY KEY (id)
	);
		
	CREATE  TABLE if not exists `tbl_past_diabetic_histories` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `visit_date_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `admission_id` int(10) UNSIGNED DEFAULT NULL,
  `past_diabetic` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) ;
	
	
	CREATE  TABLE if not exists `tbl_past_dermatology_histories` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `visit_date_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `admission_id` int(10) UNSIGNED DEFAULT NULL,
  `past_dermatolog` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) ;
	

CREATE TABLE if NOT EXISTS `tbl_past_urology_histories` ( `id` int(10) UNSIGNED NOT NULL, `patient_id` int(10) UNSIGNED NOT NULL, `visit_date_id` int(10) UNSIGNED NOT NULL, `user_id` int(10) UNSIGNED NOT NULL, `facility_id` int(10) UNSIGNED NOT NULL, `admission_id` int(10) UNSIGNED DEFAULT NULL, `past_urology` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL );
INSERT  IGNORE INTO `tbl_departments` (`id`, `department_name`, `created_at`, `updated_at`) VALUES ('55', 'UROLOGY', NULL, NULL);




CREATE TABLE if NOT EXISTS `tbl_dtcs` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `dir_duration` int(11) NOT NULL,
  `water_sugar_loss` varchar(11) NOT NULL,
  `stool_blood` varchar(11) NOT NULL,
  `fever` varchar(11) NOT NULL,
  `vomiting` varchar(11) NOT NULL,
  `other_sign` varchar(20) NOT NULL,
  `intravesel_water` int(11) NOT NULL,
  `other_treatment` varchar(11) NOT NULL,
  `ors_in` int(11) NOT NULL,
  `ors_out` int(11) NOT NULL,
  `zink_in` int(11) NOT NULL,
  `zink_out` int(11) NOT NULL,
  `dct_duration` int(11) NOT NULL,
  `dtc_unit` varchar(1) NOT NULL,
  `output` varchar(11) NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
);


CREATE  TABLE if NOT EXISTS `tbl_referrals` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `referral_type` int(10) UNSIGNED NOT NULL,
  `status` int(10) UNSIGNED NOT NULL,
  `summary` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `from_facility_id` int(10) UNSIGNED NOT NULL,
  `to_facility_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) ;

CREATE TABLE IF NOT EXISTS `trackables` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `trackable_id` text,
  `action` text,
  `trackable_type` text,
  `new_value` text,
  `old_value` text,
  `updated_at` timestamp null,
  `created_at` timestamp null
) ;


CREATE TABLE IF NOT EXISTS `tbl_nurse_runners` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `item_id` int(10) UNSIGNED NOT NULL,
  `material_id` int(10) UNSIGNED NOT NULL,
  `user_id` char(36)  NOT NULL,
  `visit_id` char(36)  NOT NULL,
  `given` int(10) UNSIGNED NOT NULL,
  `used` int(10) UNSIGNED NOT NULL,
  `drainage` varchar(3)  DEFAULT NULL,
  `tourniquet` varchar(3) DEFAULT NULL,
  `implants` varchar(3)  DEFAULT NULL,
  `implant_screws` varchar(40)  DEFAULT NULL,
  `pathology_specimen` varchar(3)  DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  `start_time` varchar(25)  NOT NULL,
  `end_time` varchar(25)  NOT NULL
) ;

CREATE TABLE IF NOT EXISTS `tbl_opd_nursings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `patient_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `service_type` varchar(12) NOT NULL,
`duration` varchar(12) NOT NULL,
  `status` int(11) NOT NULL,
  `periodic` int(11) NOT NULL,
  `updated_at` timestamp NULL,
  `created_at` timestamp NULL
) ;


CREATE TABLE IF NOT EXISTS `tbl_patient_registration_reports` (
  `facility_code` varchar(25) NOT NULL,
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
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `tbl_patient_registration_reports_facility_code_foreign` (`facility_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `tbl_reatend_patient_reports` (
  `facility_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `tbl_reatend_patiAent_reports_facility_code_foreign` (`facility_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `bills` (
  `id` int(11) NOT NULL,
  `receipt_number` int(11) DEFAULT NULL,
  `facility_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `corpse_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL,
  `name` varchar(150) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `age` varchar(50) DEFAULT NULL,
  `item_name` varchar(150) DEFAULT NULL,
  `price` decimal(10,0) DEFAULT NULL,
  `discount` decimal(10,0) DEFAULT NULL,
  `quantity` decimal(10,0) DEFAULT NULL,
  `main_category_id` int(11) DEFAULT NULL,
  `sub_category_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
);




CREATE TABLE IF NOT EXISTS `patients_with_unverified_prescriptions` (
	`id` int(11) NOT NULL,
	`patient_id` int(11) NOT NULL,
	`visit_id` int(11) NOT NULL,
	`medical_record_number` varchar(50) NOT NULL,
	`first_name`  varchar(50) NOT NULL,
	`last_name`  varchar(50) NOT NULL,
	`middle_name`  varchar(50) NOT NULL,
	`search_key`  varchar(150) NOT NULL,
	`facility_id` int(11) DEFAULT NULL -- ,
	-- PRIMARY KEY (`id`)
);



CREATE TABLE IF NOT EXISTS `patients_with_pending_prescriptions` (
	`id` int(11) NOT NULL,
	`patient_id` int(11) NOT NULL,
	`visit_id` int(11) NOT NULL,
	`medical_record_number` varchar(50) NOT NULL,
	`first_name`  varchar(50) NOT NULL,
	`last_name`  varchar(50) NOT NULL,
	`middle_name`  varchar(50) NOT NULL,
	`search_key`  varchar(150) NOT NULL,
	`facility_id` int(11) DEFAULT NULL -- ,
	-- PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `opd_patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `first_name` varchar(50)  DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `medical_record_number` varchar(50)  DEFAULT NULL,
  `name` varchar(150)  DEFAULT NULL,
  `residence_id` int(11) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `age` varchar(50)  DEFAULT NULL,
  `gender` varchar(50)  DEFAULT NULL,
  `account_number` varchar(50)  DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `tallied` varchar(50) DEFAULT NULL,
  `account_id` int(11)  DEFAULT NULL,
  `visit_date` date  DEFAULT NULL,
  `payment_filter` int(11)  DEFAULT NULL,
  `bill_id` int(11)  DEFAULT NULL,
  `sub_category_name` varchar(50)  DEFAULT NULL,
  `main_category_id` int(11)  DEFAULT NULL,
  `search_key` varchar(150)  DEFAULT NULL,
  `facility_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `tbl_integrating_keys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facility_id` int(10) unsigned NOT NULL,
  `base_urls` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `private_keys` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_keys` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_type` int(11) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_integrating_keys_facility_id_foreign` (`facility_id`),
  CONSTRAINT `tbl_integrating_keys_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `tbl_facilities` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

 
CREATE TABLE IF NOT EXISTS  `tbl_depositings` (
   `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `patient_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `withdraw` decimal(10,2) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT NULL,
  `control_in` varchar(11) DEFAULT NULL,
  `control` varchar(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
 `updated_at` timestamp NULL,
  `created_at` timestamp NULL
) ;

CREATE TABLE IF NOT EXISTS `tbl_pos_dispensings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `tbl_dispensed_group_controls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(11) NOT NULL,
  `item_name` text NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `tbl_dispensed_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `identifier` varchar(4) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` timestamp  NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `patients_to_pos` (
		id int auto_increment not null primary key,
        patient_id int,
        first_name varchar(50),
        middle_name varchar(50),
        last_name varchar(50),
        gender varchar(50),
        dob varchar(50),
        medical_record_number varchar(50),
        account_number varchar(50),
        account_id int,
        facility_id int,		
        main_category_id int,        
        sub_category_name varchar(50),
        category_description varchar(50),
        patient_category_id int,
		created_at timestamp,
		admission_status_id int,
		search_field  varchar(150)
	);
	

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
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
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
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) ;

ALTER TABLE `tbl_reatend_patient_reports`
    ADD KEY IF NOT EXISTS `tbl_reatend_patiAent_reports_facility_code_foreign` (`facility_code`);


CREATE TABLE IF NOT EXISTS `tbl_product_registries` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_category` text COLLATE utf8mb4_unicode_ci,
  `item_sub_category` text COLLATE utf8mb4_unicode_ci,
  `unit_of_measure` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `tbl_product_prices` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_price` double NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `item_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_product_prices_item_id_foreign` (`item_id`)
);

ALTER TABLE tbl_product_prices add column if not exists category TEXT NOT NULL AFTER item_name;


CREATE TABLE IF NOT EXISTS `tbl_stocks` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiry_date` date NOT NULL,
  `unit_price` double NOT NULL,
  `quantity` double NOT NULL,
  `balance` double NOT NULL,
  `pending_balance` double DEFAULT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `useless` double DEFAULT NULL,
  `useless_reason` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `batch_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `control_in` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `control_out` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`item_id`) REFERENCES `tbl_product_registries` (`id`)
) ;

CREATE TABLE IF NOT EXISTS `tbl_sales` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` double NOT NULL,
  `quantity` double NOT NULL,
  `expiry_date` date NOT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UNPAID',
  `buyer_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seller_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `batch_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`item_id`) REFERENCES `tbl_product_registries` (`id`)
) ;
CREATE TABLE IF NOT EXISTS `tbl_payments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost_amount` double NOT NULL,
  `payment_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UNPAID',
  `payer_name` text COLLATE utf8mb4_unicode_ci,
  `payment_agent_name` text COLLATE utf8mb4_unicode_ci,
  `payslip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `tbl_vulnerable_followups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `vulnerable` varchar(12) NOT NULL DEFAULT 'NO',
  `followup` varchar(12) NOT NULL DEFAULT 'NO',
  `neglect` varchar(12) NOT NULL DEFAULT 'NO',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  `remarks` text,
  PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `tbl_therapy_treatments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(10) unsigned NOT NULL,
  `visit_date_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `facility_id` int(10) unsigned NOT NULL,
  `working` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aim` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plans` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evaluation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `family` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_therapy_treatments_patient_id_foreign` (`patient_id`),
  KEY `tbl_therapy_treatments_visit_date_id_foreign` (`visit_date_id`),
  KEY `tbl_therapy_treatments_user_id_foreign` (`user_id`),
  KEY `tbl_therapy_treatments_facility_id_foreign` (`facility_id`),
  CONSTRAINT `tbl_therapy_treatments_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `tbl_facilities` (`id`),
  CONSTRAINT `tbl_therapy_treatments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `tbl_patients` (`id`),
  CONSTRAINT `tbl_therapy_treatments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `tbl_therapy_treatments_visit_date_id_foreign` FOREIGN KEY (`visit_date_id`) REFERENCES `tbl_accounts_numbers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `tbl_received_referrals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(10) unsigned NOT NULL,
  `visit_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `facility_id` int(10) unsigned NOT NULL,
  `referring_facility_id` int(10) unsigned NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_received_referrals_patient_id_foreign` (`patient_id`),
  KEY `tbl_received_referrals_visit_id_foreign` (`visit_id`),
  KEY `tbl_received_referrals_user_id_foreign` (`user_id`),
  KEY `tbl_received_referrals_facility_id_foreign` (`facility_id`),
  CONSTRAINT `tbl_received_referrals_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `tbl_facilities` (`id`),
  CONSTRAINT `tbl_received_referrals_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `tbl_patients` (`id`),
  CONSTRAINT `tbl_received_referrals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `tbl_received_referrals_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `tbl_accounts_numbers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  
CREATE TABLE IF NOT EXISTS `tbl_drf_categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `tbl_ukatilis` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT  PRIMARY KEY,
  `followup` varchar(20)   DEFAULT NULL,
  `vulnerable` varchar(20)   DEFAULT NULL,
  `screening` varchar(20)   DEFAULT NULL,
  `within_72_hrs` varchar(20)   DEFAULT NULL,
  `pt_result` varchar(20)   DEFAULT NULL,
  `hiv_result` varchar(20)   DEFAULT NULL,
  `sti_result` varchar(20)   DEFAULT NULL,
  `disability` varchar(20)   DEFAULT NULL,
  `referral` varchar(20)   DEFAULT NULL,
  `referred_to` varchar(20)   DEFAULT NULL,
  `user_id` varchar(20)   NOT NULL,
  `patient_id` varchar(20)   NOT NULL,
  `dob` varchar(20)   DEFAULT NULL,
  `residence_name` varchar(20)   DEFAULT NULL,
  `gender` varchar(20)   DEFAULT NULL,
  `client_name` varchar(20)   DEFAULT NULL,
  `medical_record_number` varchar(20)   DEFAULT NULL,
  `mobile_number` varchar(20)   DEFAULT NULL,
  `facility_id` varchar(20)   DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `pv_violence` varchar(20)   DEFAULT NULL,
  `sv_violence` varchar(20)   DEFAULT NULL,
  `ev_violence` varchar(20)   DEFAULT NULL,
  `ng_violence` varchar(20)   DEFAULT NULL,
  `fi_service` varchar(20)   DEFAULT NULL,
  `im_service` varchar(20)   DEFAULT NULL,
  `c_service` varchar(20)   DEFAULT NULL,
  `pep_service` varchar(20)   DEFAULT NULL,
  `sti_service` varchar(20)   DEFAULT NULL,
  `ec_service` varchar(20)   DEFAULT NULL,
  `fp_service` varchar(20)   DEFAULT NULL,
  `p_service` varchar(20)   DEFAULT NULL,
  `la_service` varchar(20)   DEFAULT NULL,
  `sws_service` varchar(20)   DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
 
)  ;


CREATE TABLE IF NOT EXISTS `tbl_trauma_clients` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `surname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `estimated_age` int(11) DEFAULT NULL,
  `estimated_age_group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arrival_mode` int(10) UNSIGNED DEFAULT NULL,
  `arrival_date` date NOT NULL,
  `mass_casuality` tinyint(1) NOT NULL DEFAULT '0',
  `next_kin_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_kin_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_kin_relation` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dead_on_arrival` tinyint(1) NOT NULL DEFAULT '0',
  `incident_location` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pregnant` tinyint(1) NOT NULL DEFAULT '0',
  `triage_category` int(10) UNSIGNED DEFAULT NULL,
  `registered_by` int(10) UNSIGNED DEFAULT NULL,
  `residence` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  `facility_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_trauma_clients_arrival_mode_foreign` (`arrival_mode`),
  KEY `tbl_trauma_clients_triage_category_foreign` (`triage_category`),
  KEY `tbl_trauma_clients_registered_by_foreign` (`registered_by`)
);


CREATE TABLE IF NOT EXISTS `tbl_trauma_airway_primary_surveys` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `normal` tinyint(1) NOT NULL DEFAULT '0',
  `angioedema` tinyint(1) NOT NULL DEFAULT '0',
  `stridor` tinyint(1) NOT NULL DEFAULT '0',
  `voice_changes` tinyint(1) NOT NULL DEFAULT '0',
  `oral_airway_burns` tinyint(1) NOT NULL DEFAULT '0',
  `tongue` tinyint(1) NOT NULL DEFAULT '0',
  `blood` tinyint(1) NOT NULL DEFAULT '0',
  `secretion` tinyint(1) NOT NULL DEFAULT '0',
  `vomit` tinyint(1) NOT NULL DEFAULT '0',
  `foreign_body` tinyint(1) NOT NULL DEFAULT '0',
  `repostioning` tinyint(1) NOT NULL DEFAULT '0',
  `suction` tinyint(1) NOT NULL DEFAULT '0',
  `opa` tinyint(1) NOT NULL DEFAULT '0',
  `npa` tinyint(1) NOT NULL DEFAULT '0',
  `lma` tinyint(1) NOT NULL DEFAULT '0',
  `bvm` tinyint(1) NOT NULL DEFAULT '0',
  `ett` tinyint(1) NOT NULL DEFAULT '0',
  `none_needed` tinyint(1) NOT NULL DEFAULT '0',
  `placed_before_arrival` tinyint(1) NOT NULL DEFAULT '0',
  `placed_in_eu` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_trauma_airway_primary_surveys_facility_id_foreign` (`facility_id`),
  KEY `tbl_trauma_airway_primary_surveys_user_id_foreign` (`user_id`),
  KEY `tbl_trauma_airway_primary_surveys_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `tbl_trauma_chief_complaints` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `complaint` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `dead_on_arrival` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_trauma_chief_complaints_facility_id_foreign` (`facility_id`),
  KEY `tbl_trauma_chief_complaints_user_id_foreign` (`user_id`),
  KEY `tbl_trauma_chief_complaints_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_assesment_plans` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `consultant` text COLLATE utf8mb4_unicode_ci,
  `other_differential` text COLLATE utf8mb4_unicode_ci,
  `imaging` text COLLATE utf8mb4_unicode_ci,
  `medication` text COLLATE utf8mb4_unicode_ci,
  `intervention` text COLLATE utf8mb4_unicode_ci,
  `consults` text COLLATE utf8mb4_unicode_ci,
  `other_plan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_assesment_plans_facility_id_foreign` (`facility_id`),
  KEY `trauma_assesment_plans_user_id_foreign` (`user_id`),
  KEY `trauma_assesment_plans_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_client_diagnoses` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `diagnosis_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_client_diagnoses_facility_id_foreign` (`facility_id`),
  KEY `trauma_client_diagnoses_user_id_foreign` (`user_id`),
  KEY `trauma_client_diagnoses_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_client_dispositions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `checklist_completed` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `impressions` text COLLATE utf8mb4_unicode_ci,
  `died_of` text COLLATE utf8mb4_unicode_ci,
  `discharge_notes` text COLLATE utf8mb4_unicode_ci,
  `number_of_serious_injury` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adminted_icu_ot` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_discussed_with_patient` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `left_without_seen` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `left_without_complete_treatment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accepting_provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admited_ward` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ed_departure_date` date DEFAULT NULL,
  `ed_departure_time` time DEFAULT NULL,
  `admited` tinyint(1) NOT NULL DEFAULT '0',
  `deceased` tinyint(1) NOT NULL DEFAULT '0',
  `transfer` tinyint(1) NOT NULL DEFAULT '0',
  `discharged` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_client_dispositions_facility_id_foreign` (`facility_id`),
  KEY `trauma_client_dispositions_user_id_foreign` (`user_id`),
  KEY `trauma_client_dispositions_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_fluid_medications` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `ivf` text COLLATE utf8mb4_unicode_ci,
  `ns` text COLLATE utf8mb4_unicode_ci,
  `lr` text COLLATE utf8mb4_unicode_ci,
  `other_fluid` text COLLATE utf8mb4_unicode_ci,
  `blood_products` tinyint(1) NOT NULL DEFAULT '0',
  `whole_blood` text COLLATE utf8mb4_unicode_ci,
  `prbc` text COLLATE utf8mb4_unicode_ci,
  `ffp` text COLLATE utf8mb4_unicode_ci,
  `platelets` text COLLATE utf8mb4_unicode_ci,
  `oploid_analgesia` text COLLATE utf8mb4_unicode_ci,
  `other_analgesia` text COLLATE utf8mb4_unicode_ci,
  `sedation_paralytics` text COLLATE utf8mb4_unicode_ci,
  `antibiotics` text COLLATE utf8mb4_unicode_ci,
  `tetanus` text COLLATE utf8mb4_unicode_ci,
  `other` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_fluid_medications_facility_id_foreign` (`facility_id`),
  KEY `trauma_fluid_medications_user_id_foreign` (`user_id`),
  KEY `trauma_fluid_medications_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_hpi_injury_mechanisms` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `road_traffic_acident` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_involved` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crashed_with` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fall_from` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_bunt_force` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hit_by_falling_object` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stab_cut` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drowning` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `intent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assaulted_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hours_since_last_meal` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `substance_six_hour_injury` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_substance` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loss_of_consciousness` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trauma` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flotation_device` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `burn_caused_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poisoning_toxic_exposure` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unknown` tinyint(1) NOT NULL DEFAULT '0',
  `other` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extricated` tinyint(1) NOT NULL DEFAULT '0',
  `suffocation_choking_hanging` tinyint(1) NOT NULL DEFAULT '0',
  `gunshot` tinyint(1) NOT NULL DEFAULT '0',
  `sexual_assault` tinyint(1) NOT NULL DEFAULT '0',
  `ejected` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_hpi_injury_mechanisms_facility_id_foreign` (`facility_id`),
  KEY `trauma_hpi_injury_mechanisms_user_id_foreign` (`user_id`),
  KEY `trauma_hpi_injury_mechanisms_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_hpis` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `date_of_injury` date DEFAULT NULL,
  `time_of_injury` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `place_of_injury` text COLLATE utf8mb4_unicode_ci,
  `prehospital_care` text COLLATE utf8mb4_unicode_ci,
  `patient_activity_injury_time` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_hpis_facility_id_foreign` (`facility_id`),
  KEY `trauma_hpis_user_id_foreign` (`user_id`),
  KEY `trauma_hpis_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_hysical_examinations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `general_normal` tinyint(1) NOT NULL DEFAULT '0',
  `general_examination` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `heent_normal` tinyint(1) NOT NULL DEFAULT '0',
  `heent_examination` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `neuro_normal` tinyint(1) NOT NULL DEFAULT '0',
  `neuro_examination` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `neck_normal` tinyint(1) NOT NULL DEFAULT '0',
  `neck_examination` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pulm_chest_normal` tinyint(1) NOT NULL DEFAULT '0',
  `pulm_chest_examination` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cardiac_normal` tinyint(1) NOT NULL DEFAULT '0',
  `cardiac_examination` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `abdominal_normal` tinyint(1) NOT NULL DEFAULT '0',
  `abdominal_examination` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `back_normal` tinyint(1) NOT NULL DEFAULT '0',
  `back_examination` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gu_rectal_normal` tinyint(1) NOT NULL DEFAULT '0',
  `gu_rectal_examination` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `msk_skin_normal` tinyint(1) NOT NULL DEFAULT '0',
  `msk_skin_examination` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_hysical_examinations_facility_id_foreign` (`facility_id`),
  KEY `trauma_hysical_examinations_user_id_foreign` (`user_id`),
  KEY `trauma_hysical_examinations_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_imaging_results` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `pneumothorax` text COLLATE utf8mb4_unicode_ci,
  `pleural_fluid` text COLLATE utf8mb4_unicode_ci,
  `rib_fracture` text COLLATE utf8mb4_unicode_ci,
  `palmonary_opacity` text COLLATE utf8mb4_unicode_ci,
  `c_spine_fracture` text COLLATE utf8mb4_unicode_ci,
  `extremity_fracture` text COLLATE utf8mb4_unicode_ci,
  `pelvic_fracture` text COLLATE utf8mb4_unicode_ci,
  `wide_mediastinum` text COLLATE utf8mb4_unicode_ci,
  `other_image_result` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_imaging_results_facility_id_foreign` (`facility_id`),
  KEY `trauma_imaging_results_user_id_foreign` (`user_id`),
  KEY `trauma_imaging_results_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_lab_results` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `upt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hgb` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blood_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_lab_result` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `result_pending` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_lab_results_facility_id_foreign` (`facility_id`),
  KEY `trauma_lab_results_user_id_foreign` (`user_id`),
  KEY `trauma_lab_results_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_past_medical_allergy_histories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `allergies` text COLLATE utf8mb4_unicode_ci,
  `last_menstrual_cycle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_menstrual_cycle_g` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_menstrual_cycle_p` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `save_home` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pregnant` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vaccination_up_to_date` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vaccination_description` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tobacco` tinyint(1) NOT NULL DEFAULT '0',
  `alcohol` tinyint(1) NOT NULL DEFAULT '0',
  `drugs` tinyint(1) NOT NULL DEFAULT '0',
  `iv_drugs` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_past_medical_allergy_histories_facility_id_foreign` (`facility_id`),
  KEY `trauma_past_medical_allergy_histories_user_id_foreign` (`user_id`),
  KEY `trauma_past_medical_allergy_histories_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_past_medical_histories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `medication` text COLLATE utf8mb4_unicode_ci,
  `past_surgeries` text COLLATE utf8mb4_unicode_ci,
  `other_past_medical` text COLLATE utf8mb4_unicode_ci,
  `past_medical_htn` tinyint(1) NOT NULL DEFAULT '0',
  `past_medical_diabetes` tinyint(1) NOT NULL DEFAULT '0',
  `past_medical_copd` tinyint(1) NOT NULL DEFAULT '0',
  `past_medical_psychiatric` tinyint(1) NOT NULL DEFAULT '0',
  `past_medical_renal_disease` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_past_medical_histories_facility_id_foreign` (`facility_id`),
  KEY `trauma_past_medical_histories_user_id_foreign` (`user_id`),
  KEY `trauma_past_medical_histories_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_primary_breathing_surveys` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `normal` tinyint(1) NOT NULL DEFAULT '0',
  `nc` tinyint(1) NOT NULL DEFAULT '0',
  `mask` tinyint(1) NOT NULL DEFAULT '0',
  `nrb` tinyint(1) NOT NULL DEFAULT '0',
  `bvm` tinyint(1) NOT NULL DEFAULT '0',
  `cpap_bipap` tinyint(1) NOT NULL DEFAULT '0',
  `ventilator` tinyint(1) NOT NULL DEFAULT '0',
  `spontaneous_prespiration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chest_rise` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trachea` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `breath_sound` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `breath_sound_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oxygen` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chest_needle_left_size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chest_needle_left_depth` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chest_needle_right_size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chest_needle_right_depth` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_primary_breathing_surveys_facility_id_foreign` (`facility_id`),
  KEY `trauma_primary_breathing_surveys_user_id_foreign` (`user_id`),
  KEY `trauma_primary_breathing_surveys_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_primary_circulation_surveys` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `normal` tinyint(1) NOT NULL DEFAULT '0',
  `warm` tinyint(1) NOT NULL DEFAULT '0',
  `dry` tinyint(1) NOT NULL DEFAULT '0',
  `pale` tinyint(1) NOT NULL DEFAULT '0',
  `cool` tinyint(1) NOT NULL DEFAULT '0',
  `moist` tinyint(1) NOT NULL DEFAULT '0',
  `cyanotic` tinyint(1) NOT NULL DEFAULT '0',
  `capillary_refill` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pulses` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asymmetric_value` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jvd` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bleeding_controlled` tinyint(1) NOT NULL DEFAULT '0',
  `iv_loc` tinyint(1) NOT NULL DEFAULT '0',
  `iv_size` tinyint(1) NOT NULL DEFAULT '0',
  `cvl_loc` tinyint(1) NOT NULL DEFAULT '0',
  `cvl_size` tinyint(1) NOT NULL DEFAULT '0',
  `ic_loc` tinyint(1) NOT NULL DEFAULT '0',
  `ic_size` tinyint(1) NOT NULL DEFAULT '0',
  `ivf` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `other` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `ns` tinyint(1) NOT NULL DEFAULT '0',
  `lr` tinyint(1) NOT NULL DEFAULT '0',
  `blood_ordered` tinyint(1) NOT NULL DEFAULT '0',
  `pelvic_blinder_placed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_primary_circulation_surveys_facility_id_foreign` (`facility_id`),
  KEY `trauma_primary_circulation_surveys_user_id_foreign` (`user_id`),
  KEY `trauma_primary_circulation_surveys_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_primary_disability_surveys` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `normal` tinyint(1) NOT NULL DEFAULT '0',
  `glucose` tinyint(1) NOT NULL DEFAULT '0',
  `responsiveness_a` tinyint(1) NOT NULL DEFAULT '0',
  `responsiveness_v` tinyint(1) NOT NULL DEFAULT '0',
  `responsiveness_p` tinyint(1) NOT NULL DEFAULT '0',
  `responsiveness_u` tinyint(1) NOT NULL DEFAULT '0',
  `responsiveness_naloxone` tinyint(1) NOT NULL DEFAULT '0',
  `lue` tinyint(1) NOT NULL DEFAULT '0',
  `rue` tinyint(1) NOT NULL DEFAULT '0',
  `lle` tinyint(1) NOT NULL DEFAULT '0',
  `rle` tinyint(1) NOT NULL DEFAULT '0',
  `naloxone` tinyint(1) NOT NULL DEFAULT '0',
  `blood_glucose_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gcs` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gcs_e` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gcs_v` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gcs_m` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pupil_l_1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pupil_l_2` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pupil_r_1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pupil_r_2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_primary_disability_surveys_facility_id_foreign` (`facility_id`),
  KEY `trauma_primary_disability_surveys_user_id_foreign` (`user_id`),
  KEY `trauma_primary_disability_surveys_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_primary_exposure_surveys` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `normal` tinyint(1) NOT NULL DEFAULT '0',
  `exposed_completely` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_primary_exposure_surveys_facility_id_foreign` (`facility_id`),
  KEY `trauma_primary_exposure_surveys_user_id_foreign` (`user_id`),
  KEY `trauma_primary_exposure_surveys_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_primary_fast_surveys` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `normal` tinyint(1) NOT NULL DEFAULT '0',
  `not_indicated` tinyint(1) NOT NULL DEFAULT '0',
  `pericardial_effusion` tinyint(1) NOT NULL DEFAULT '0',
  `peritoneum` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `free_fluid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chest` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pneumothorax` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pleural_fluid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_primary_fast_surveys_facility_id_foreign` (`facility_id`),
  KEY `trauma_primary_fast_surveys_user_id_foreign` (`user_id`),
  KEY `trauma_primary_fast_surveys_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_procedures` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `cricothyroidotomy` text COLLATE utf8mb4_unicode_ci,
  `intubation` text COLLATE utf8mb4_unicode_ci,
  `chest_tube` text COLLATE utf8mb4_unicode_ci,
  `pericardiocentesis` text COLLATE utf8mb4_unicode_ci,
  `open_thoracotomy` text COLLATE utf8mb4_unicode_ci,
  `splinting` text COLLATE utf8mb4_unicode_ci,
  `fracture_red_pelvic_stab` text COLLATE utf8mb4_unicode_ci,
  `foreign_body_removal` text COLLATE utf8mb4_unicode_ci,
  `simple_complex_lac_repair` text COLLATE utf8mb4_unicode_ci,
  `other_procedure` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_procedures_facility_id_foreign` (`facility_id`),
  KEY `trauma_procedures_user_id_foreign` (`user_id`),
  KEY `trauma_procedures_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `trauma_re_assesment_plans` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `re_assement_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `re_assement_temp` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `re_assement_bp` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `re_assement_rr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition_change_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition_change` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `re_assement_spo2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `trauma_re_assesment_plans_facility_id_foreign` (`facility_id`),
  KEY `trauma_re_assesment_plans_user_id_foreign` (`user_id`),
  KEY `trauma_re_assesment_plans_client_id_foreign` (`client_id`)
);
CREATE TABLE IF NOT EXISTS `tbl_trauma_vitals` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `facility_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `temp` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bp` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hr` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rr` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spo2` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ps` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recorded_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_trauma_vitals_recorded_by_foreign` (`recorded_by`),
  KEY `tbl_trauma_vitals_client_id_foreign` (`client_id`),
  KEY `tbl_trauma_vitals_facility_id_foreign` (`facility_id`)
) ;


ALTER TABLE tbl_prescriptions  ADD column if not exists continuation_status VARCHAR(2) NULL ;
ALTER TABLE tbl_results  ADD column if not exists sample VARCHAR(100) NULL ;


ALTER TABLE tbl_trauma_vitals ADD COLUMN IF NOT EXISTs facility_id int(11) null;
ALTER TABLE tbl_trauma_clients ADD COLUMN IF NOT EXISTs facility_id int(11) null;
ALTER TABLE tbl_trauma_clients ADD COLUMN IF NOT EXISTs arrival_mode int(11) null;
ALTER TABLE tbl_trauma_clients ADD COLUMN IF NOT EXISTs arrival_date int(11) null;

CREATE TABLE IF NOT EXISTS `tbl_lab_reporting_indictor_maps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lab_indicator_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
);
 
CREATE TABLE IF NOT EXISTS `tbl_lab_reporting_controls` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(1) NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
);

INSERT IGNORE INTO `tbl_lab_reporting_controls` (`id`, `code`, `item_name`, `status`, `created_at`, `updated_at`) VALUES
(1, '1', 'Haemoglobin estimation', 1, '2019-04-16 21:00:00', '2019-04-17 08:48:13'),
(2, '2', 'Blood for haemoparasites', 1, '2019-04-17 21:00:00', '2019-04-17 08:48:13'),
(3, '3', 'mRDT', 1, '2019-04-18 21:00:00', '2019-04-17 08:48:13'),
(4, '4', 'Stool microscopy for parasites', 1, '2019-04-19 21:00:00', '2019-04-17 08:48:13'),
(5, '5', 'Sputum for AFB', 1, '2019-04-20 21:00:00', '2019-04-17 08:48:13'),
(6, '6', 'Gene Expert', 1, '2019-04-21 21:00:00', '2019-04-17 08:48:13'),
(7, '7', 'Urine sediment microscopy', 1, '2019-04-22 21:00:00', '2019-04-17 08:48:13'),
(8, '8', 'Urine protein', 1, '2019-04-23 21:00:00', '2019-04-17 08:48:13'),
(9, '9', 'Urine Sugar', 1, '2019-04-24 21:00:00', '2019-04-17 08:48:13'),
(10, '10', ' Blood Sugar', 1, '2019-04-25 21:00:00', '2019-04-17 08:48:13'),
(11, '11', 'Syphilis screening', 1, '2019-04-26 21:00:00', '2019-04-17 08:48:13'),
(12, '12', 'Haematology analysis', 1, '2019-04-27 21:00:00', '2019-04-17 08:48:13'),
(13, '13', 'Blood grouping ', 1, '2019-04-28 21:00:00', '2019-04-17 08:48:13'),
(14, '14', 'Units of Blood Collected', 0, '2019-04-29 21:00:00', '2019-04-17 08:48:13'),
(15, '15', 'Donor Screening', 0, '2019-04-30 21:00:00', '2019-04-17 08:48:13'),
(16, '16', 'Recipients', 0, '2019-05-01 21:00:00', '2019-04-17 08:48:13'),
(17, '17', 'Units of Blood Expired', 0, '2019-05-02 21:00:00', '2019-04-17 08:48:13'),
(18, '18', 'Skin smear for AFB ', 0, '2019-05-03 21:00:00', '2019-04-17 08:48:13'),
(19, '19', 'Urinary Pregnancy Test', 0, '2019-05-04 21:00:00', '2019-04-17 08:48:13'),
(20, '20', 'Haematology analysis', 0, '2019-05-05 21:00:00', '2019-04-17 08:48:13'),
(21, '21', 'Blood grouping ', 0, '2019-05-06 21:00:00', '2019-04-17 08:48:13'),
(22, '22', 'Units of blood Collected', 0, '2019-05-07 21:00:00', '2019-04-17 08:48:13'),
(23, '23', 'Skin smear for AFB ', 0, '2019-05-08 21:00:00', '2019-04-17 08:48:13'),
(24, '24', 'Skin snip for microfilaria', 0, '2019-05-09 21:00:00', '2019-04-17 08:48:13'),
(25, '25', 'Collection and fixation of cytological smears', 0, '2019-05-10 21:00:00', '2019-04-17 08:48:13'),
(26, '26', 'Collection and fixation of histological Specimens', 0, '2019-05-11 21:00:00', '2019-04-17 08:48:13'),
(27, '27', 'Cerebrospinal Fluid  ', 0, '2019-05-12 21:00:00', '2019-04-17 08:48:13'),
(28, '28', 'Sickle cell screen ', 0, '2019-05-13 21:00:00', '2019-04-17 08:48:13'),
(29, '29', 'Pus swabs', 0, '2019-05-14 21:00:00', '2019-04-17 08:48:13'),
(30, '30', 'Genito-urinary tract specimens', 0, '2019-05-15 21:00:00', '2019-04-17 08:48:13'),
(31, '31', 'CD4 count', 0, '2019-05-16 21:00:00', '2019-04-17 08:48:13'),
(32, '32', 'Hepatitis B.', 0, '2019-05-17 21:00:00', '2019-04-17 08:48:13'),
(33, '33', 'Serum bilirubin', 0, '2019-05-18 21:00:00', '2019-04-17 08:48:13'),
(34, '34', 'Chemistry analysis ', 0, '2019-05-19 21:00:00', '2019-04-17 08:48:13'),
(35, '35', 'Other tests -Mention', 1, '2019-05-20 21:00:00', '2019-04-17 08:48:13');

CREATE TABLE IF NOT EXISTS `trauma_accident_locations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `ward` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `common_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `road_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house_namber` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_of_household` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ;