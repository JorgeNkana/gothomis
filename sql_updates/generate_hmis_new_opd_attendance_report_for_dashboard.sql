DELIMITER $$

DROP PROCEDURE IF EXISTS `generate_hmis_new_opd_attendance_report_for_dashboard` $$
CREATE PROCEDURE `generate_hmis_new_opd_attendance_report_for_dashboard`(INOUT message varchar(50), last_reporting_date date)
PROC:BEGIN
	DECLARE facility_code VARCHAR(50);
	
	IF DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) = DATE(last_reporting_date) THEN
		TRUNCATE dashboard_reporting_admissions;
		LEAVE PROC;
	END IF;
	
	CREATE TEMPORARY TABLE IF NOT EXISTS `categorical_atendance`(
		Sex VARCHAR(6),
		CaseDate DATE,
		AgeGroup INT,
		HealthPlanCode INT,
		Cases INT
	);
	
	CREATE OR REPLACE TABLE  `dashboard_reporting_hmis_new_opd_attendances` (
		  `department_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `attendance_date` date NOT NULL,
		  `health_plan_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `book_row_number` VARCHAR(3) NULL  DEFAULT 1,
		  `row_description` VARCHAR(350) NULL DEFAULT 'Wagonjwa waliohudhuria kwa mara ya kwanza mwaka huo(*), kituo chochote nchini',
		  `male_under_one_month` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `female_under_one_month` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `total_under_one_month` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `male_under_one_year` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `female_under_one_year` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `total_under_one_year` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `male_under_five_year` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `female_under_five_year` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `total_under_five_year` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `male_above_five_under_sixty` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `female_above_five_under_sixty` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `total_above_five_under_sixty` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `male_above_sixty` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `female_above_sixty` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `total_above_sixty` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `total_male` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `total_female` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `grand_total` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `facility_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `reporting_date` date NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

	TRUNCATE `dashboard_reporting_hmis_new_opd_attendances`;
	
	
	SET facility_code = (SELECT (SELECT INSERT(REGEXP_REPLACE(tbl_facilities.facility_code, '[_-]', ''), LENGTH(REGEXP_REPLACE(tbl_facilities.facility_code, '[_-]', '')), 0,'-')) FROM tbl_facilities WHERE id = message);
	
	SET @st = CONCAT("INSERT INTO categorical_atendance SELECT Gender, tbl_accounts_numbers.date_attended as Date, TIMESTAMPDIFF(MONTH, dob,  tbl_accounts_numbers.date_attended) as AgeGroup, tbl_accounts_numbers.patient_category_id AS Category, COUNT(*) FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_patients.id = tbl_accounts_numbers.patient_id AND tbl_accounts_numbers.main_category_id IS NOT NULL AND tbl_accounts_numbers.date_attended >'", last_reporting_date, "' AND tbl_accounts_numbers.facility_id = ", message, " GROUP BY Gender, tbl_accounts_numbers.date_attended, tbl_accounts_numbers.patient_category_id,TIMESTAMPDIFF(MONTH, dob,  tbl_accounts_numbers.date_attended)"); 
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;

	SET last_reporting_date = (SELECT MAX(categorical_atendance.CaseDate) FROM categorical_atendance);
	
	SET last_reporting_date = CASE WHEN last_reporting_date IS NULL THEN CURRENT_DATE ELSE last_reporting_date END;
	
	SET @st = CONCAT("INSERT INTO dashboard_reporting_hmis_new_opd_attendances(facility_code, department_code, male_under_one_month, male_under_one_year, male_under_five_year, male_above_five_under_sixty, male_above_sixty, female_under_one_month, female_under_one_year, female_under_five_year, female_above_five_under_sixty, female_above_sixty, health_plan_code, attendance_date, reporting_date) SELECT '",facility_code,"','000OPD', SUM(CASE WHEN Sex = 'MALE' AND AgeGroup < 1 THEN Cases ELSE 0 END), SUM(CASE WHEN Sex = 'MALE' AND AgeGroup BETWEEN 1 AND 11 THEN Cases ELSE 0 END), SUM(CASE WHEN Sex = 'MALE' AND AgeGroup BETWEEN 12 AND 59 THEN Cases ELSE 0 END), SUM(CASE WHEN Sex = 'MALE' AND AgeGroup BETWEEN 60 AND 719 THEN Cases ELSE 0 END),SUM(CASE WHEN Sex = 'MALE' AND AgeGroup >= 720 THEN Cases ELSE 0 END), SUM(CASE WHEN Sex = 'FEMALE' AND AgeGroup < 1 THEN Cases ELSE 0 END), SUM(CASE WHEN Sex = 'FEMALE' AND AgeGroup BETWEEN 1 AND 11 THEN Cases ELSE 0 END), SUM(CASE WHEN Sex = 'FEMALE' AND AgeGroup BETWEEN 12 AND 59 THEN Cases ELSE 0 END), SUM(CASE WHEN Sex = 'FEMALE' AND AgeGroup BETWEEN 60 AND 719 THEN Cases ELSE 0 END),SUM(CASE WHEN Sex = 'FEMALE' AND AgeGroup >= 720 THEN Cases ELSE 0 END), HealthPlanCode, CaseDate, '", last_reporting_date, "' FROM categorical_atendance GROUP BY CaseDate, HealthPlanCode");
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE dashboard_reporting_hmis_new_opd_attendances set total_under_one_month = male_under_one_month+female_under_one_month where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE dashboard_reporting_hmis_new_opd_attendances set total_under_one_year = male_under_one_year+female_under_one_year where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE dashboard_reporting_hmis_new_opd_attendances set total_under_five_year = male_under_five_year+female_under_five_year where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE dashboard_reporting_hmis_new_opd_attendances set total_above_five_under_sixty = male_above_five_under_sixty+female_above_five_under_sixty where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE dashboard_reporting_hmis_new_opd_attendances set total_above_sixty = male_above_sixty+female_above_sixty where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE dashboard_reporting_hmis_new_opd_attendances set total_male = male_under_one_month+male_under_one_year+male_under_five_year+male_above_five_under_sixty+male_above_sixty where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE dashboard_reporting_hmis_new_opd_attendances set total_female=female_under_one_month+female_under_one_year+female_under_five_year+female_above_five_under_sixty+female_above_sixty where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE dashboard_reporting_hmis_new_opd_attendances set grand_total = total_male+total_female where facility_code='", facility_code,"'");
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;