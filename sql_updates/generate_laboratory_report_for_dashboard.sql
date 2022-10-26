DELIMITER $$

DROP PROCEDURE IF EXISTS `generate_laboratory_report_for_dashboard` $$
CREATE PROCEDURE `generate_laboratory_report_for_dashboard`(INOUT message varchar(50), last_reporting_date date)
PROC:BEGIN		
	DECLARE facility_id VARCHAR(50);
	DECLARE facility_code VARCHAR(50);
	DECLARE admission_date DATE;
	DECLARE done_with_dates BOOLEAN;
	DECLARE done_with_performed_tests BOOLEAN;
	DECLARE test_date DATE;
	DECLARE cases INT;
	DECLARE health_plan_code VARCHAR(50);
	DECLARE concept_code VARCHAR(50);
	DECLARE sub_department_code VARCHAR(50);
	
	IF DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) = DATE(last_reporting_date) THEN
		TRUNCATE  dashboard_reporting_laboratory;
		LEAVE PROC;
	END IF;
					
	CREATE TEMPORARY TABLE IF NOT EXISTS `test_dates`(
		test_date VARCHAR(50)
	);
	CREATE TEMPORARY TABLE IF NOT EXISTS `tests`(
		cases INT,
		concept_code VARCHAR(50),
		sub_department_code VARCHAR(50),
		health_plan_code VARCHAR(50)
	);
	
	CREATE OR REPLACE TABLE `dashboard_reporting_laboratory` (
		  `concept_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `sub_department_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `test_date` date NOT NULL,
		  `health_plan_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `number_of_investigations` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `facility_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `reporting_date` date NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

	TRUNCATE `dashboard_reporting_laboratory`;
	TRUNCATE test_dates;
	
	SET done_with_dates = FALSE;
	SET done_with_performed_tests = FALSE;
	SET facility_id = message;
	
	SET facility_code = (SELECT tbl_facilities.facility_code FROM tbl_facilities WHERE id = facility_id);
	SET facility_code = (SELECT INSERT(REGEXP_REPLACE(facility_code, '[_-]', ''), LENGTH(REGEXP_REPLACE(facility_code, '[_-]', '')), 0,'-'));
	
	SET @st = CONCAT("INSERT INTO `test_dates` SELECT DISTINCT date(tbl_requests.created_at) FROM tbl_requests JOIN tbl_orders ON tbl_orders.order_id = tbl_requests.id JOIN tbl_items ON tbl_orders.test_id = tbl_items.id JOIN tbl_results ON tbl_results.order_id = tbl_orders.id WHERE tbl_results.confirmation_status = 1 AND date(tbl_requests.created_at) > '",last_reporting_date,"' AND date(tbl_requests.created_at) < CURRENT_DATE AND tbl_items.dept_id=2 ORDER BY tbl_requests.created_at asc"); 
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET last_reporting_date = (SELECT MAX(test_dates.test_date) FROM test_dates);
	
	BEGIN
		DECLARE dates
		CURSOR FOR
			SELECT test_dates.test_date FROM test_dates;
		
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_dates = TRUE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
	
		OPEN dates;
		
		for_each_date:
		LOOP
			FETCH NEXT FROM dates INTO test_date;
					
			IF done_with_dates THEN 
				LEAVE for_each_date; 
			ELSE
				BEGIN
					TRUNCATE tests;
					SET @st = CONCAT("INSERT INTO tests SELECT COUNT(*),UPPER(tbl_item_type_mappeds.item_code)  ,tbl_equipments.sub_department_id,tbl_accounts_numbers.patient_category_id  FROM tbl_requests JOIN tbl_orders ON tbl_orders.order_id = tbl_requests.id AND date(tbl_requests.created_at) = '",test_date,"' JOIN tbl_results ON tbl_results.order_id = tbl_orders.id AND tbl_results.confirmation_status = 1 JOIN tbl_testspanels ON tbl_orders.test_id = tbl_testspanels.item_id JOIN tbl_equipments ON tbl_testspanels.equipment_id = tbl_equipments.id JOIN tbl_items ON tbl_orders.test_id = tbl_items.id JOIN tbl_item_type_mappeds ON tbl_item_type_mappeds.item_id = tbl_items.id JOIN tbl_accounts_numbers ON tbl_requests.visit_date_id = tbl_accounts_numbers.id AND tbl_accounts_numbers.main_category_id IS NOT NULL GROUP BY tbl_item_type_mappeds.item_code, tbl_equipments.sub_department_id,tbl_accounts_numbers.patient_category_id");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					SET done_with_performed_tests = FALSE;
					BEGIN
						DECLARE performed_tests
						CURSOR FOR
							SELECT * from tests;
						
						DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_performed_tests = TRUE;
						DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
					
						OPEN performed_tests;
						
						for_each_test:
						LOOP
							FETCH NEXT FROM performed_tests INTO cases , concept_code, sub_department_code,health_plan_code;
							
							IF done_with_performed_tests THEN 
								LEAVE for_each_test; 
							ELSE
								BEGIN
									IF cases > 0 AND concept_code IS NOT NULL AND TRIM(concept_code) <> '' AND health_plan_code IS NOT NULL AND TRIM(health_plan_code) <> '' THEN
										BEGIN
											SET @st = CONCAT("INSERT INTO  dashboard_reporting_laboratory(facility_code, concept_code,sub_department_code, health_plan_code, number_of_investigations, test_date, reporting_date) SELECT '",facility_code,"','",concept_code,"',",sub_department_code,",",health_plan_code,",",cases, ",'",test_date,"','",last_reporting_date,"'");
											PREPARE stmt FROM @st;
											EXECUTE stmt;
											DEALLOCATE PREPARE stmt;
										END;
									END IF;
									END;
							END IF;
						END LOOP;
					END;
				END;
			END IF;
		END LOOP;
	END;
END$$
DELIMITER ;