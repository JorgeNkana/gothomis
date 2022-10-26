DELIMITER $$

DROP PROCEDURE IF EXISTS `generate_bed_occupancy_report_for_dashboard` $$
CREATE PROCEDURE `generate_bed_occupancy_report_for_dashboard`(INOUT message varchar(50), last_reporting_date date)
PROC:BEGIN		
	DECLARE facility_id VARCHAR(50);
	DECLARE facility_code VARCHAR(50);
	DECLARE occupancy_date DATE;
	
	IF CURRENT_DATE = DATE(last_reporting_date) THEN
		TRUNCATE dashboard_reporting_bed_occupancy;
		LEAVE PROC;
	END IF;



	
	CREATE OR REPLACE TABLE `dashboard_reporting_bed_occupancy` (
		  `occupancy_date` date NOT NULL,
		  `book_row_number` VARCHAR(3) NOT NULL,
		  `row_description` VARCHAR(350) NULL,
		  `maternity_entry` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `non_maternity_entry` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `facility_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `reporting_date` date NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
	
	TRUNCATE `dashboard_reporting_bed_occupancy`;
	
	SET facility_id = message;
	
	SET facility_code = (SELECT tbl_facilities.facility_code FROM tbl_facilities WHERE id = facility_id);
	SET facility_code = (SELECT INSERT(REGEXP_REPLACE(facility_code, '[_-]', ''), LENGTH(REGEXP_REPLACE(facility_code, '[_-]', '')), 0,'-'));
	
	BEGIN
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
		SET @st = CONCAT("INSERT INTO `dashboard_reporting_bed_occupancy` SELECT DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY),1,'Vitanda Vilivyopo',(SELECT COUNT(*) FROM tbl_wards JOIN tbl_beds ON tbl_wards.ward_type_code = 'MARTEN' AND tbl_wards.id = tbl_beds.ward_id),(SELECT COUNT(*) FROM tbl_wards JOIN tbl_beds ON tbl_wards.ward_type_code <> 'MARTEN' AND tbl_wards.id = tbl_beds.ward_id), '",facility_code,"',CURRENT_DATE"); 
		PREPARE stmt FROM @st;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		
		SET @st = CONCAT("INSERT INTO `dashboard_reporting_bed_occupancy` SELECT DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY),2,'Wagonjwa waliolazwa',(SELECT COUNT(*) FROM tbl_admissions JOIN tbl_instructions ON tbl_admissions.id = tbl_instructions.admission_id JOIN tbl_wards ON tbl_instructions.ward_id = tbl_wards.id AND tbl_wards.ward_type_code = 'MARTEN'),(SELECT COUNT(*) FROM tbl_admissions JOIN tbl_instructions ON tbl_admissions.id = tbl_instructions.admission_id JOIN tbl_wards ON tbl_instructions.ward_id = tbl_wards.id AND tbl_wards.ward_type_code <> 'MARTEN'), '",facility_code,"',CURRENT_DATE"); 
		PREPARE stmt FROM @st;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		
		SET @st = CONCAT("INSERT INTO `dashboard_reporting_bed_occupancy` SELECT DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY),3,'Waliopata kitanda',(SELECT COUNT(*) FROM tbl_admissions JOIN tbl_instructions ON tbl_admissions.id = tbl_instructions.admission_id AND tbl_admissions.admission_status_id = 2 AND tbl_admissions.admission_date < CURRENT_DATE JOIN tbl_wards ON tbl_instructions.ward_id = tbl_wards.id AND tbl_wards.ward_type_code = 'MARTEN' JOIN tbl_beds ON tbl_instructions.bed_id = tbl_beds.id AND tbl_beds.bed_type_id <> 4),(SELECT COUNT(*) FROM tbl_admissions JOIN tbl_instructions ON tbl_admissions.id = tbl_instructions.admission_id AND tbl_admissions.admission_status_id = 2 AND tbl_admissions.admission_date < CURRENT_DATE JOIN tbl_wards ON tbl_instructions.ward_id = tbl_wards.id AND tbl_wards.ward_type_code <> 'MARTEN' JOIN tbl_beds ON tbl_instructions.bed_id = tbl_beds.id AND tbl_beds.bed_type_id <> 4), '",facility_code,"',CURRENT_DATE"); 
		PREPARE stmt FROM @st;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		
		SET @st = CONCAT("INSERT INTO `dashboard_reporting_bed_occupancy` SELECT DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY),4,'Waliokosa kitanda',(SELECT COUNT(*) FROM tbl_admissions JOIN tbl_instructions ON tbl_admissions.id = tbl_instructions.admission_id AND tbl_admissions.admission_status_id = 2 AND tbl_admissions.admission_date < CURRENT_DATE JOIN tbl_wards ON tbl_instructions.ward_id = tbl_wards.id AND tbl_wards.ward_type_code = 'MARTEN' JOIN tbl_beds ON tbl_instructions.bed_id = tbl_beds.id AND tbl_beds.bed_type_id = 4),(SELECT COUNT(*) FROM tbl_admissions JOIN tbl_instructions ON tbl_admissions.id = tbl_instructions.admission_id AND tbl_admissions.admission_status_id = 2 AND tbl_admissions.admission_date < CURRENT_DATE JOIN tbl_wards ON tbl_instructions.ward_id = tbl_wards.id AND tbl_wards.ward_type_code <> 'MARTEN' JOIN tbl_beds ON tbl_instructions.bed_id = tbl_beds.id AND tbl_beds.bed_type_id = 4), '",facility_code,"',CURRENT_DATE"); 
		PREPARE stmt FROM @st;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		
	END;
END$$

DELIMITER ;