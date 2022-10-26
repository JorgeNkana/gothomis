DELIMITER $$

DROP PROCEDURE IF EXISTS `generate_gbv_vac_report_for_dashboard` $$
CREATE PROCEDURE `generate_gbv_vac_report_for_dashboard`(INOUT message varchar(50), last_reporting_date date)
PROC:BEGIN		
	DECLARE facility_id VARCHAR(50);
	DECLARE facility_code VARCHAR(50);
	DECLARE reported_date DATE;
	DECLARE done_with_dates BOOLEAN;
	DECLARE book_row_number VARCHAR(5);
	DECLARE row_description VARCHAR(350);
	
	
	IF DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) = DATE(last_reporting_date) THEN
		TRUNCATE dashboard_reporting_gbv_vac;
		LEAVE PROC;
	END IF;
				
	CREATE OR REPLACE TABLE `dashboard_reporting_gbv_vac` (
		  `reported_date` date NOT NULL,
		  `book_row_number` VARCHAR(3) DEFAULT NULL,
		  `row_description` VARCHAR(350) NULL,
		  `under_five_years` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `five_to_nine` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `ten_to_forteen` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `fifteen_to_seventeen` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `eighteen_to_twenty_four` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `twenty_five_and_above` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `grand_total` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `facility_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `reporting_date` date NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
	
	
	CREATE TEMPORARY TABLE IF NOT EXISTS `reported_dates`(
		reported_date VARCHAR(50)
	);
			
	TRUNCATE `dashboard_reporting_gbv_vac`;
	
	SET facility_id = message;
	
	SET facility_code = (SELECT tbl_facilities.facility_code FROM tbl_facilities WHERE id = facility_id);
	SET facility_code = (SELECT INSERT(REGEXP_REPLACE(facility_code, '[_-]', ''), LENGTH(REGEXP_REPLACE(facility_code, '[_-]', '')), 0,'-'));
	
	SET done_with_dates = FALSE;
	
	SET @st = CONCAT("INSERT INTO `reported_dates` SELECT DISTINCT DATE(created_at) FROM tbl_client_violences WHERE facility_id = '",facility_id,"' AND DATE(created_at) > '",last_reporting_date,"' AND DATE(created_at) < CURRENT_DATE ORDER BY created_at asc"); 
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET last_reporting_date = (SELECT MAX(reported_dates.reported_date) FROM reported_dates);
	
	BEGIN
		DECLARE dates
		CURSOR FOR
			SELECT reported_dates.reported_date FROM reported_dates;
		
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_dates = TRUE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
	
		OPEN dates;
		
		for_each_date:
		LOOP
			FETCH NEXT FROM dates INTO reported_date;
			
			IF done_with_dates THEN 
				LEAVE for_each_date; 
			ELSE
				BEGIN
					SET book_row_number = '1a'; SET row_description = "Idadi ya wateja wote KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '1b'; SET row_description = "Idadi ya wateja wote ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					-- TO BE EDITED TO SPECIFY FOLLOWUP
					SET book_row_number = '2a'; SET row_description = "Idadi ya wateja Wapya KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '2b'; SET row_description = "Idadi ya wateja Wapya ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					-- END FOLLOWUP
					
					SET book_row_number = '2c'; SET row_description = "Wateja waliokuja kwa Ufuatiliaji(Folloup Visit) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
										
					SET book_row_number = '3a'; SET row_description = "Idadi ya watoto waliopo katika mazingira Hatarishi KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '3b'; SET row_description = "Wateja waliokuja kwa Ufuatiliaji(Folloup Visit) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '4a'; SET row_description = "Wateja walioulizwa maswali ya utambuzi(Screening) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '4b'; SET row_description = "Wateja walioulizwa maswali ya utambuzi (Screening) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '4c'; SET row_description = "Wazazi/Walezi walioulizwa maswali ya utambuzi kwaniaba ya watoto (Screening) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '4d'; SET row_description = "Wazazi/Walezi walioulizwa maswali ya utambuzi kwaniaba ya watoto (Screening) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '5a'; SET row_description = "Idadi ya waliopatwa na ukatili wa Kimwili(Physical Violence) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '5b'; SET row_description = "Idadi ya waliopatwa na ukatili wa Kimwili(Physical Violence) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '6a'; SET row_description = "Idadi ya waliopatwa na ukatili wa Kingono wa kubakwa/kulawitiwa KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '6b'; SET row_description = "Idadi ya waliopatwa na ukatili wa Kingono wa kulawitiwa ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '6C'; SET row_description = "Idadi ya waliopatwa na ukatili wa Kingono(Sexual Violence) wa kubakwa ambao wamepimwa ujauzito ndani ya masaa 72 baada ya tukio na kukutwa hawana ujauzito KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '7a'; SET row_description = "Idadi ya waliopatwa na ukatili wa Kihisia (Emotional Violence) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '7b'; SET row_description = "Idadi ya waliopatwa na ukatili wa Kihisia (Emotional Violence) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '8a'; SET row_description = "Idadi ya Watoto waliotelekezwa(Neglect) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '8b'; SET row_description = "Idadi ya Watoto waliotelekezwa(Neglect) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '9a'; SET row_description = "Idadi ya waliofika kituoni ndani ya masaa 72 baada ya tukio KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '9b'; SET row_description = "Idadi ya waliofika kituoni ndani ya masaa 72 baada ya tukio ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '10a'; SET row_description = "Waliofanyiwa uchunguzi wa ushahidi wa kisheria(Forensic Investigation) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '10b'; SET row_description = "Waliofanyiwa uchunguzi wa ushahidi wa kisheria(Forensic Investigation) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '11a'; SET row_description = "Wateja waliofanyiwa unasihi(Counselling) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '11b'; SET row_description = "Wateja waliofanyiwa unasihi(Counselling) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '11c'; SET row_description = "Wazazi/Walezi waliofanyiwa unasihi kwa niaba ya Watoto(Counselling) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"'  FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '11d'; SET row_description = "Wazazi/Walezi waliofanyiwa unasihi kwa niaba ya Watoto(Counselling) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '12a'; SET row_description = "Waliopimwa Virusi vya Ukimwi(HIV Testing) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '12b'; SET row_description = "Waliopimwa Virusi vya Ukimwi(HIV Testing) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '13a'; SET row_description = "Waliopewa matibabu ya kinga(Post Exposure Propylaxis) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '13b'; SET row_description = "Waliopewa matibabu ya kinga(Post Exposure Propylaxis) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '14a'; SET row_description = "Waliopewa Matibabu ya magonjwa yatokanayo na ngono(Sexual Transmitted Disease) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '14b'; SET row_description = "Waliopewa Matibabu ya magonjwa yatokanayo na ngono(Sexual Transmitted Disease) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '15'; SET row_description = "Waliopewa Njia ya Uzazi wa Mpango wa Dharura(Emergency Contraceptive) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '16a'; SET row_description = "Waliopewa Huduma ya Kipolisi KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '17a'; SET row_description = "Waliopewa Msaada wakisheria (legal Aid) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '17b'; SET row_description = "Waliopewa Msaada wakisheria (legal Aid) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '18a'; SET row_description = "Waliopata Ulemavu wa kimwili wa kudumu KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '18b'; SET row_description = "Waliopata Ulemavu wa kimwili wa kudumu ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '19a'; SET row_description = "Wateja Waliopata rufaa kuja kituoni (Referral In) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '19b'; SET row_description = "Wateja Waliopata rufaa kuja kituoni (Referral In) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '20a'; SET row_description = "Wateja Waliopata rufaa ndani ya kituo (Internal Referral) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '20b'; SET row_description = "Wateja Waliopata rufaa ndani ya kituo (Internal Referral) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '21a'; SET row_description = "Wateja Waliopata rufaa kwenda nje ya kituo ( Referral Out) KE";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					
					SET book_row_number = '21b'; SET row_description = "Wateja Waliopata rufaa kwenda nje ya kituo ( Referral Out) ME";
					SET @st = CONCAT("INSERT INTO dashboard_reporting_gbv_vac SELECT '",book_row_number,"','",row_description,"',IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) < 5  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 5 and 9  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 10 and 14  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 15 and 17  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) BETWEEN 18 and 24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,tbl_client_violences.created_at) >24  then 1 ELSE  0 END ),0),IFNULL(SUM(CASE when gender ='MALE' then 1 ELSE  0 END ),0),'",facility_code,"','",last_reporting_date,"' FROM tbl_client_violences inner JOIN tbl_patients ON tbl_client_violences.patient_id=tbl_patients.id AND tbl_client_violences.facility_id = ",facility_id," AND DATE(tbl_client_violences) = '",reported_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
				END;				
			END IF;				
		END LOOP;
	END;
END$$

DELIMITER ;