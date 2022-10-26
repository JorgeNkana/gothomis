DELIMITER $$

DROP PROCEDURE IF EXISTS `generate_outgoing_referral_report_for_dashboard` $$
CREATE PROCEDURE `generate_outgoing_referral_report_for_dashboard`(INOUT message varchar(50), last_reporting_date date)
PROC:BEGIN		
	DECLARE facility_id VARCHAR(50);
	DECLARE facility_code VARCHAR(50);
	DECLARE admission_date DATE;
	DECLARE done_with_dates BOOLEAN;
	DECLARE done_with_case_categories BOOLEAN;
	DECLARE cases INT;
	DECLARE health_plan_code INT;
	
	IF DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) = DATE(last_reporting_date) THEN
		TRUNCATE dashboard_reporting_outgoing_referral;
		LEAVE PROC;
	END IF;
					
	CREATE TEMPORARY TABLE IF NOT EXISTS `referral_dates`(
		referral_date VARCHAR(50)
	);
	CREATE TEMPORARY TABLE IF NOT EXISTS `categorical_atendance`(
		cases INT,
		health_plan_code INT
	);
	
	CREATE OR REPLACE TABLE  `dashboard_reporting_outgoing_referral` (
		  `department_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `referral_date` date NOT NULL,
		  `health_plan_code` varchar(6) COLLATE utf8mb4_unicode_ci NULL,
		  `book_row_number` VARCHAR(3) DEFAULT 90,
		  `row_description` VARCHAR(350) DEFAULT 'Waliopewa Rufaa',
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

	TRUNCATE `dashboard_reporting_outgoing_referral`;
	TRUNCATE referral_dates;
	
	SET done_with_dates = FALSE;
	SET done_with_case_categories = FALSE;
	SET facility_id = message;
	
	SET facility_code = (SELECT tbl_facilities.facility_code FROM tbl_facilities WHERE id = facility_id);
	SET facility_code = (SELECT INSERT(REGEXP_REPLACE(facility_code, '[_-]', ''), LENGTH(REGEXP_REPLACE(facility_code, '[_-]', '')), 0,'-'));
	
	-- SET last_reporting_date = (SELECT MAX(referral_dates.referral_date) FROM referral_dates);
	
	BEGIN
		SET @st = CONCAT("INSERT INTO dashboard_reporting_outgoing_referral SELECT 
							NULL,
							`date`,
							NULL,
							90,
							'Waliopewa Rufaa',
							`male_under_one_month`,
							`female_under_one_month`,
							`total_under_one_month`,
							`male_under_one_year`,
							`female_under_one_year`,
							`total_under_one_year`,
							`male_under_five_year`,
							`female_under_five_year`,
							`total_under_five_year`,
							`male_above_five_under_sixty`,
							`female_above_five_under_sixty`,
							`total_above_five_under_sixty`,
							`male_above_sixty`,
							`female_above_sixty`,
							`total_above_sixty` ,
							`total_male`,
							`total_female`,
							`grand_total`,
							 '",facility_code,"',
							CURRENT_DATE
							FROM tbl_outgoing_referral_registers 
							WHERE date > '",last_reporting_date,"' AND date < CURRENT_DATE ORDER BY date ASC");
		PREPARE stmt FROM @st;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
	END;
END$$

DELIMITER ;