DELIMITER $$

DROP PROCEDURE IF EXISTS `generate_dtc_report_for_dashboard` $$
CREATE PROCEDURE `generate_dtc_report_for_dashboard`(INOUT message varchar(50), last_reporting_date date)
PROC:BEGIN		
	DECLARE facility_id VARCHAR(50);
	DECLARE facility_code VARCHAR(50);
	DECLARE attendance_date DATE;
	DECLARE done_with_dates BOOLEAN;
	DECLARE done_with_case_categories BOOLEAN;
	DECLARE done_with_entries BOOLEAN;
	DECLARE cases INT;
	DECLARE health_plan_code INT;
	DECLARE book_row_number INT;
	DECLARE row_description VARCHAR(350);
	DECLARE query_condition VARCHAR(50);
	
	IF DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) = DATE(last_reporting_date) THEN
		TRUNCATE dashboard_reporting_dtc;
		LEAVE PROC;
	END IF;

		
	CREATE TEMPORARY TABLE IF NOT EXISTS `attendance_dates`(
		attendance_date VARCHAR(50)
	);
	CREATE TEMPORARY TABLE IF NOT EXISTS `categorical_atendance`(
		cases INT,
		health_plan_code INT
	);
	
	CREATE TEMPORARY TABLE IF NOT EXISTS `report_entries`(
		book_row_number INT,
		row_description VARCHAR(150),
		query_condition VARCHAR(50)
	);
	
	CREATE OR REPLACE TABLE  `dashboard_reporting_dtc` (
		  `department_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `attendance_date` date NOT NULL,
		  `health_plan_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `book_row_number` VARCHAR(3) DEFAULT NULL,
		  `row_description` VARCHAR(350) NULL,
		  `male_under_one_month` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `female_under_one_month` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `total_under_one_month` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `male_under_one_year` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `female_under_one_year` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `total_under_one_year` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `male_under_five_year` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `female_under_five_year` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `total_under_five_year` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `total_male` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `total_female` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `grand_total` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `facility_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `reporting_date` date NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

	TRUNCATE `dashboard_reporting_dtc`;
	TRUNCATE attendance_dates;
	
	SET done_with_dates = FALSE;
	SET done_with_case_categories = FALSE;
	SET facility_id = message;
	
	INSERT INTO report_entries VALUES
			(1, "Idadi ya wagonjwa waliotibiwa DTC",""),
			(2, "Idadi ya wagonjwa waliotibiwa DTC walio na upungufu mkubwa wa maji na chumvichumvi mwilini","AND tbl_dtc.water_sugar_loss='M'"),
			(3, "Idadi ya wagonjwa waliotibiwa DTC walio na upungufu kiasi wa maji na chumvichumvi mwilini","AND tbl_dtc.water_sugar_loss='K'"),
			(4, "Idadi ya wagonjwa walio na damu katika kinyesi","AND tbl_dtc.stool_blood='N'"),
			(5, "Idadi ya wagonjwa waliopewa rufaa","AND tbl_dtc.output='REF'"),
			(6, "Idadi ya wagonjwa waliopatiwa zinki","AND (tbl_dtc.zink_in IS  NOT NULL OR tbl_dtc.zink_out IS  NOT NULL)"),
			(7, "Idadi ya wagonjwa waliopatiwa paketi za ORS","AND (tbl_dtc.ors_in IS  NOT NULL OR tbl_dtc.ors_out IS  NOT NULL)"),
			(8, "Idadi ya wagonjwa waliolazwa","AND tbl_dtc.output='ADM'"),
			(9, "Idadi ya wagonjwa waliofia DTC","AND tbl_dtc.output='DEAD'");
			
	SET facility_code = (SELECT tbl_facilities.facility_code FROM tbl_facilities WHERE id = facility_id);
	SET facility_code = (SELECT INSERT(REGEXP_REPLACE(facility_code, '[_-]', ''), LENGTH(REGEXP_REPLACE(facility_code, '[_-]', '')), 0,'-'));
	
	SET @st = CONCAT("INSERT INTO `attendance_dates` SELECT DISTINCT DATE(created_at) FROM tbl_dtcs WHERE  tbl_dtcs.facility_id = '",facility_id,"' AND DATE(created_at) > '",last_reporting_date,"' AND DATE(created_at) < CURRENT_DATE ORDER BY created_at asc "); 
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET last_reporting_date = (SELECT MAX(attendance_dates.attendance_date) FROM attendance_dates);
	
	BEGIN
		DECLARE dates
		CURSOR FOR
			SELECT attendance_dates.attendance_date FROM attendance_dates;
		
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_dates = TRUE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
	
		OPEN dates;
		
		for_each_date:
		LOOP
			FETCH NEXT FROM dates INTO attendance_date;
			
			IF done_with_dates THEN 
				LEAVE for_each_date; 
			ELSE
				BEGIN
					DECLARE report_entry
					CURSOR FOR
						SELECT report_entries.* FROM report_entries;
					
					DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_entries = TRUE;
					DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
				
					OPEN report_entry;
					
					for_each_entry:
					LOOP
						FETCH NEXT FROM report_entry INTO book_row_number, row_description, query_condition;
						
						IF done_with_entries THEN 
							LEAVE for_each_entry; 
						ELSE
							BEGIN
								TRUNCATE categorical_atendance;
								SET done_with_case_categories = FALSE;
								SET @st = CONCAT("INSERT INTO categorical_atendance SELECT COUNT(*),tbl_accounts_numbers.patient_category_id FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_patients.id = tbl_accounts_numbers.patient_id JOIN tbl_dtcs ON tbl_dtcs.patient_id = tbl_accounts_numbers.patient_id WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='MALE' AND DATE(tbl_dtcs.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob, tbl_dtcs.created_at) < 1 AND tbl_accounts_numbers.main_category_id IS NOT NULL ", query_condition, " GROUP BY tbl_accounts_numbers.patient_category_id");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								BEGIN
									DECLARE case_categories
									CURSOR FOR
										SELECT * from categorical_atendance;
									
									DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_case_categories = TRUE;
									DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
								
									OPEN case_categories;
									
									for_each_category:
									LOOP
										FETCH NEXT FROM case_categories INTO cases , health_plan_code;
										
										IF done_with_case_categories THEN 
											LEAVE for_each_category; 
										ELSE
											BEGIN
												IF cases > 0 AND health_plan_code IS NOT NULL AND TRIM(health_plan_code) <> '' THEN
													BEGIN
														SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM dashboard_reporting_dtc WHERE attendance_date='",attendance_date,"' and health_plan_code = '", health_plan_code ,"' and book_row_number=", book_row_number);
														PREPARE stmt FROM @st;
														EXECUTE stmt;
														DEALLOCATE PREPARE stmt;
												
														IF @reported > 0 AND health_plan_code IS NOT NULL THEN
															BEGIN
																SET @st = CONCAT("UPDATE dashboard_reporting_dtc SET male_under_one_month = male_under_one_month+",CAST(cases AS CHAR CHARACTER SET UTF8), " WHERE attendance_date='",attendance_date,"' AND facility_code='",facility_code,"' AND health_plan_code=",health_plan_code," and book_row_number=", book_row_number);
																PREPARE stmt FROM @st;
																EXECUTE stmt;
																DEALLOCATE PREPARE stmt;
															END;
														ELSE
															BEGIN
																SET @st = CONCAT("INSERT INTO  dashboard_reporting_dtc(facility_code,department_code,health_plan_code,book_row_number,row_description,male_under_one_month,attendance_date,reporting_date) SELECT '",facility_code, "','000DTC','",health_plan_code,"',",book_row_number,",'",row_description,"',",CAST(cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"','",last_reporting_date,"'");
																PREPARE stmt FROM @st;
																EXECUTE stmt;
																DEALLOCATE PREPARE stmt;
															END;
														END IF;
													END;
												END IF;
												SET cases = 0;
												SET @reported = 0;
											END;
										END IF;
									END LOOP;
								END;
								
								TRUNCATE categorical_atendance;
								SET done_with_case_categories = FALSE;
								SET @st = CONCAT("INSERT INTO categorical_atendance SELECT COUNT(*),tbl_accounts_numbers.patient_category_id FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_patients.id = tbl_accounts_numbers.patient_id JOIN tbl_dtcs ON tbl_dtcs.patient_id = tbl_accounts_numbers.patient_id WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='FEMALE' AND DATE(tbl_dtcs.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_dtcs.created_at) < 1 AND tbl_accounts_numbers.main_category_id IS NOT NULL ", query_condition, " GROUP BY tbl_accounts_numbers.patient_category_id");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								BEGIN
									DECLARE case_categories
									CURSOR FOR
										SELECT * from categorical_atendance;
									
									DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_case_categories = TRUE;
									DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
								
									OPEN case_categories;
									
									for_each_category:
									LOOP
										FETCH NEXT FROM case_categories INTO cases , health_plan_code;
										
										IF done_with_case_categories THEN 
											LEAVE for_each_category; 
										ELSE
											BEGIN
												IF cases > 0 THEN
													BEGIN
														SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM dashboard_reporting_dtc WHERE attendance_date='",attendance_date,"' and health_plan_code = '", health_plan_code ,"' and book_row_number=", book_row_number);
														PREPARE stmt FROM @st;
														EXECUTE stmt;
														DEALLOCATE PREPARE stmt;
												
														IF @reported > 0 AND health_plan_code IS NOT NULL THEN
															BEGIN
																SET @st = CONCAT("UPDATE dashboard_reporting_dtc SET female_under_one_month = female_under_one_month+",CAST(cases AS CHAR CHARACTER SET UTF8), " WHERE attendance_date='",attendance_date,"' AND facility_code='",facility_code,"' AND health_plan_code=",health_plan_code," and book_row_number=", book_row_number);
																PREPARE stmt FROM @st;
																EXECUTE stmt;
																DEALLOCATE PREPARE stmt;
															END;
														ELSE
															BEGIN
																SET @st = CONCAT("INSERT INTO  dashboard_reporting_dtc(facility_code,department_code,health_plan_code,book_row_number,row_description,female_under_one_month,attendance_date,reporting_date) SELECT '",facility_code, "','000DTC','",health_plan_code,"',",book_row_number,",'",row_description,"',",CAST(cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"','",last_reporting_date,"'");
																PREPARE stmt FROM @st;
																EXECUTE stmt;
																DEALLOCATE PREPARE stmt;
															END;
														END IF;
													END;
												END IF;
												SET cases = 0;
												SET @reported = 0;
											END;
										END IF;
									END LOOP;
								END;
								
								TRUNCATE categorical_atendance;
								SET done_with_case_categories = FALSE;
								SET @st = CONCAT("INSERT INTO categorical_atendance SELECT COUNT(*),tbl_accounts_numbers.patient_category_id FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_patients.id = tbl_accounts_numbers.patient_id JOIN tbl_dtcs ON tbl_dtcs.patient_id = tbl_accounts_numbers.patient_id WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='MALE' AND DATE(tbl_dtcs.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_dtcs.created_at) BETWEEN 1 AND 11 AND tbl_accounts_numbers.main_category_id IS NOT NULL ", query_condition, " GROUP BY tbl_accounts_numbers.patient_category_id");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								BEGIN
									DECLARE case_categories
									CURSOR FOR
										SELECT * from categorical_atendance;
									
									DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_case_categories = TRUE;
									DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
								
									OPEN case_categories;
									
									for_each_category:
									LOOP
										FETCH NEXT FROM case_categories INTO cases , health_plan_code;
										
										IF done_with_case_categories THEN 
											LEAVE for_each_category; 
										ELSE
											BEGIN
												IF cases > 0 THEN
													BEGIN
														SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM dashboard_reporting_dtc WHERE attendance_date='",attendance_date,"' and health_plan_code = '", health_plan_code ,"' and book_row_number=", book_row_number);
														PREPARE stmt FROM @st;
														EXECUTE stmt;
														DEALLOCATE PREPARE stmt;
												
														IF @reported > 0 AND health_plan_code IS NOT NULL THEN
															BEGIN
																SET @st = CONCAT("UPDATE dashboard_reporting_dtc SET male_under_one_year = male_under_one_year+",CAST(cases AS CHAR CHARACTER SET UTF8), " WHERE attendance_date='",attendance_date,"' AND facility_code='",facility_code,"' AND health_plan_code=",health_plan_code," and book_row_number=", book_row_number);
																PREPARE stmt FROM @st;
																EXECUTE stmt;
																DEALLOCATE PREPARE stmt;
															END;
														ELSE
															BEGIN
																SET @st = CONCAT("INSERT INTO  dashboard_reporting_dtc(facility_code,department_code,health_plan_code,book_row_number,row_description,male_under_one_year,attendance_date,reporting_date) SELECT '",facility_code, "','000DTC','",health_plan_code,"',",book_row_number,",'",row_description,"',",CAST(cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"','",last_reporting_date,"'");
																PREPARE stmt FROM @st;
																EXECUTE stmt;
																DEALLOCATE PREPARE stmt;
															END;
														END IF;
													END;
												END IF;
												SET cases = 0;
												SET @reported = 0;
											END;
										END IF;
									END LOOP;
								END;
								
								TRUNCATE categorical_atendance;
								SET done_with_case_categories = FALSE;
								SET @st = CONCAT("INSERT INTO categorical_atendance SELECT COUNT(*),tbl_accounts_numbers.patient_category_id FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_patients.id = tbl_accounts_numbers.patient_id JOIN tbl_dtcs ON tbl_dtcs.patient_id = tbl_accounts_numbers.patient_id WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='FEMALE' AND DATE(tbl_dtcs.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_dtcs.created_at) BETWEEN 1 AND 11 AND tbl_accounts_numbers.main_category_id IS NOT NULL ", query_condition, " GROUP BY tbl_accounts_numbers.patient_category_id");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								BEGIN
									DECLARE case_categories
									CURSOR FOR
										SELECT * from categorical_atendance;
									
									DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_case_categories = TRUE;
									DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
								
									OPEN case_categories;
									
									for_each_category:
									LOOP
										FETCH NEXT FROM case_categories INTO cases , health_plan_code;
										
										IF done_with_case_categories THEN 
											LEAVE for_each_category; 
										ELSE
											BEGIN
												IF cases > 0 THEN
													BEGIN
														SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM dashboard_reporting_dtc WHERE attendance_date='",attendance_date,"' and health_plan_code = '", health_plan_code ,"' and book_row_number=", book_row_number);
														PREPARE stmt FROM @st;
														EXECUTE stmt;
														DEALLOCATE PREPARE stmt;
												
														IF @reported > 0 AND health_plan_code IS NOT NULL THEN
															BEGIN
																SET @st = CONCAT("UPDATE dashboard_reporting_dtc SET female_under_one_year = female_under_one_year+",CAST(cases AS CHAR CHARACTER SET UTF8), " WHERE attendance_date='",attendance_date,"' AND facility_code='",facility_code,"' AND health_plan_code=",health_plan_code," and book_row_number=", book_row_number);
																PREPARE stmt FROM @st;
																EXECUTE stmt;
																DEALLOCATE PREPARE stmt;
															END;
														ELSE
															BEGIN
																SET @st = CONCAT("INSERT INTO  dashboard_reporting_dtc(facility_code,department_code,health_plan_code,book_row_number,row_description,female_under_one_year,attendance_date,reporting_date) SELECT '",facility_code, "','000DTC','",health_plan_code,"',",book_row_number,",'",row_description,"',",CAST(cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"','",last_reporting_date,"'");
																PREPARE stmt FROM @st;
																EXECUTE stmt;
																DEALLOCATE PREPARE stmt;
															END;
														END IF;
													END;
												END IF;
												SET cases = 0;
												SET @reported = 0;
											END;
										END IF;
									END LOOP;
								END;
								
								TRUNCATE categorical_atendance;
								SET done_with_case_categories = FALSE;
								SET @st = CONCAT("INSERT INTO categorical_atendance SELECT COUNT(*),tbl_accounts_numbers.patient_category_id FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_patients.id = tbl_accounts_numbers.patient_id JOIN tbl_dtcs ON tbl_dtcs.patient_id = tbl_accounts_numbers.patient_id WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='MALE' AND DATE(tbl_dtcs.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_dtcs.created_at) BETWEEN 12 AND 60 AND tbl_accounts_numbers.main_category_id IS NOT NULL ", query_condition, " GROUP BY tbl_accounts_numbers.patient_category_id");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								BEGIN
									DECLARE case_categories
									CURSOR FOR
										SELECT * from categorical_atendance;
									
									DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_case_categories = TRUE;
									DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
								
									OPEN case_categories;
									
									for_each_category:
									LOOP
										FETCH NEXT FROM case_categories INTO cases , health_plan_code;
										
										IF done_with_case_categories THEN 
											LEAVE for_each_category; 
										ELSE
											BEGIN
												IF cases > 0 THEN
													BEGIN
														SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM dashboard_reporting_dtc WHERE attendance_date='",attendance_date,"' and health_plan_code = '", health_plan_code ,"' and book_row_number=", book_row_number);
														PREPARE stmt FROM @st;
														EXECUTE stmt;
														DEALLOCATE PREPARE stmt;
												
														IF @reported > 0 AND health_plan_code IS NOT NULL THEN
															BEGIN
																SET @st = CONCAT("UPDATE dashboard_reporting_dtc SET male_under_five_year = male_under_five_year+",CAST(cases AS CHAR CHARACTER SET UTF8), " WHERE attendance_date='",attendance_date,"' AND facility_code='",facility_code,"' AND health_plan_code=",health_plan_code," and book_row_number=", book_row_number);
																PREPARE stmt FROM @st;
																EXECUTE stmt;
																DEALLOCATE PREPARE stmt;
															END;
														ELSE
															BEGIN
																SET @st = CONCAT("INSERT INTO  dashboard_reporting_dtc(facility_code,department_code,health_plan_code,book_row_number,row_description,male_under_five_year,attendance_date,reporting_date) SELECT '",facility_code, "','000DTC','",health_plan_code,"',",book_row_number,",'",row_description,"',",CAST(cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"','",last_reporting_date,"'");
																PREPARE stmt FROM @st;
																EXECUTE stmt;
																DEALLOCATE PREPARE stmt;
															END;
														END IF;
													END;
												END IF;
												SET cases = 0;
												SET @reported = 0;
											END;
										END IF;
									END LOOP;
								END;
								
								TRUNCATE categorical_atendance;
								SET done_with_case_categories = FALSE;
								SET @st = CONCAT("INSERT INTO categorical_atendance SELECT COUNT(*),tbl_accounts_numbers.patient_category_id FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_patients.id = tbl_accounts_numbers.patient_id JOIN tbl_dtcs ON tbl_dtcs.patient_id = tbl_accounts_numbers.patient_id WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='FEMALE' AND DATE(tbl_dtcs.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_dtcs.created_at) BETWEEN 12 AND 60 AND tbl_accounts_numbers.main_category_id IS NOT NULL ", query_condition, " GROUP BY tbl_accounts_numbers.patient_category_id");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								BEGIN
									DECLARE case_categories
									CURSOR FOR
										SELECT * from categorical_atendance;
									
									DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_case_categories = TRUE;
									DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
								
									OPEN case_categories;
									
									for_each_category:
									LOOP
										FETCH NEXT FROM case_categories INTO cases , health_plan_code;
										
										IF done_with_case_categories THEN 
											LEAVE for_each_category; 
										ELSE
											BEGIN
												IF cases > 0 THEN
													BEGIN
														SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM dashboard_reporting_dtc WHERE attendance_date='",attendance_date,"' and health_plan_code = '", health_plan_code ,"' and book_row_number=", book_row_number);
														PREPARE stmt FROM @st;
														EXECUTE stmt;
														DEALLOCATE PREPARE stmt;
												
														IF @reported > 0 AND health_plan_code IS NOT NULL THEN
															BEGIN
																SET @st = CONCAT("UPDATE dashboard_reporting_dtc SET female_under_five_year = female_under_five_year+",CAST(cases AS CHAR CHARACTER SET UTF8), " WHERE attendance_date='",attendance_date,"' AND facility_code='",facility_code,"' AND health_plan_code=",health_plan_code," and book_row_number=", book_row_number);
																PREPARE stmt FROM @st;
																EXECUTE stmt;
																DEALLOCATE PREPARE stmt;
															END;
														ELSE
															BEGIN
																SET @st = CONCAT("INSERT INTO  dashboard_reporting_dtc(facility_code,department_code,health_plan_code,book_row_number,row_description,female_under_five_year,attendance_date,reporting_date) SELECT '",facility_code, "','000DTC','",health_plan_code,"',",book_row_number,",'",row_description,"',",CAST(cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"','",last_reporting_date,"'");
																PREPARE stmt FROM @st;
																EXECUTE stmt;
																DEALLOCATE PREPARE stmt;
															END;
														END IF;
													END;
												END IF;
												SET cases = 0;
												SET @reported = 0;
											END;
										END IF;
									END LOOP;
								END;
							END;
						END IF;
					END LOOP;
				END;
			END IF;
		END LOOP;
		CLOSE dates;
	END;
	
	
	SET @st = CONCAT("UPDATE dashboard_reporting_dtc set total_under_one_month = male_under_one_month+female_under_one_month where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE dashboard_reporting_dtc set total_under_one_year = male_under_one_year+female_under_one_year where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE dashboard_reporting_dtc set total_under_five_year = male_under_five_year+female_under_five_year where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE dashboard_reporting_dtc set total_male=male_under_one_month+male_under_one_year+male_under_five_year  where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	
	SET @st = CONCAT("UPDATE dashboard_reporting_dtc set total_female=female_under_one_month+female_under_one_year+female_under_five_year  where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE dashboard_reporting_dtc set grand_total = total_male+total_female where facility_code='", facility_code,"'");
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;