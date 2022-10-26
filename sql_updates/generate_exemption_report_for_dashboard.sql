DELIMITER $$

DROP PROCEDURE IF EXISTS `generate_exemption_report_for_dashboard` $$
CREATE PROCEDURE `generate_exemption_report_for_dashboard`(INOUT message varchar(50), last_reporting_date date)
PROC:BEGIN		
	DECLARE facility_id VARCHAR(50);
	DECLARE facility_code VARCHAR(50);
	DECLARE exemption_date DATE;
	DECLARE done_with_dates BOOLEAN;
	DECLARE done_with_transactions BOOLEAN;
	DECLARE number_of_exemptions INT;
	DECLARE exempted_value decimal(12,2);
	DECLARE concept_code varchar(50);
	DECLARE department_code varchar(50);
	DECLARE gender varchar(50);
	DECLARE health_plan_code INT;
	
	IF DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) = DATE(last_reporting_date) THEN
		TRUNCATE dashboard_reporting_exemption;
		LEAVE PROC;
	END IF;
					
	CREATE TEMPORARY TABLE IF NOT EXISTS `exemption_dates`(
		exemption_date VARCHAR(50)
	);
	
	
	CREATE OR REPLACE TEMPORARY TABLE `transactions` (
		  `number_of_exemptions` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `department_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `health_plan_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `concept_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `gender` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `exempted_value` decimal(8,2) NOT NULL
		);

	
	CREATE OR REPLACE TABLE `dashboard_reporting_exemption` (
		  `exemption_date` date NOT NULL,
		  `number_of_exemptions` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `department_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `health_plan_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `concept_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `gender` varchar(6),
		  `exempted_value` decimal(12,2) UNSIGNED NOT NULL DEFAULT '0.0',
		  `facility_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `reporting_date` date NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

	TRUNCATE `dashboard_reporting_exemption`;
	TRUNCATE exemption_dates;
	
	SET done_with_dates = FALSE;
	SET facility_id = message;
	
				
	SET facility_code = (SELECT tbl_facilities.facility_code FROM tbl_facilities WHERE id = facility_id);
	SET facility_code = (SELECT INSERT(REGEXP_REPLACE(facility_code, '[_-]', ''), LENGTH(REGEXP_REPLACE(facility_code, '[_-]', '')), 0,'-'));
	
	SET @st = CONCAT("INSERT INTO `exemption_dates` SELECT DISTINCT tbl_accounts_numbers.date_attended FROM tbl_accounts_numbers WHERE  main_category_id IS NOT NULL AND main_category_id = 3 AND tbl_accounts_numbers.facility_id = '",facility_id,"' AND date_attended > '",last_reporting_date,"' AND date_attended < CURRENT_DATE ORDER BY date_attended asc"); 
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET last_reporting_date = (SELECT MAX(exemption_dates.exemption_date) FROM exemption_dates);
	
	BEGIN
		DECLARE dates
		CURSOR FOR
			SELECT exemption_dates.exemption_date FROM exemption_dates;
		
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_dates = TRUE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;

		OPEN dates;
		
		for_each_date:
		LOOP
			FETCH NEXT FROM dates INTO exemption_date;
			
			IF done_with_dates THEN 
				LEAVE for_each_date; 
			ELSE
				BEGIN
					SET done_with_transactions = FALSE;
					TRUNCATE transactions;
					SET @st = CONCAT("INSERT INTO transactions SELECT COUNT(*) as number_of_exemptions, tbl_items.dept_id , payment_filter AS health_plan_code, UPPER(tbl_item_type_mappeds.item_code),gender, SUM(tbl_invoice_lines.quantity*tbl_invoice_lines.price) AS exempted_value FROM tbl_invoice_lines JOIN tbl_patients ON tbl_invoice_lines.main_category_id = 3 AND is_payable IS NOT TRUE AND tbl_invoice_lines.patient_id=tbl_patients.id JOIN tbl_item_prices ON tbl_invoice_lines.item_price_id=tbl_item_prices.id JOIN tbl_items ON tbl_item_prices.item_id = tbl_items.id JOIN tbl_item_type_mappeds ON tbl_items.id = tbl_item_type_mappeds.item_id WHERE date(tbl_invoice_lines.created_at) = '",exemption_date, "' GROUP BY tbl_item_type_mappeds.item_code, dept_id, health_plan_code, gender");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					BEGIN
						DECLARE transactions
							CURSOR FOR
								SELECT * FROM transactions;
								
						DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_transactions = TRUE;
						DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
					
						OPEN transactions;
						
						
						for_each_transaction:
						LOOP
							FETCH NEXT FROM transactions INTO number_of_exemptions, department_code, health_plan_code, concept_code, gender, exempted_value;
						
							IF done_with_transactions THEN 
								LEAVE for_each_transaction; 
							ELSE
								BEGIN
									IF concept_code IS NOT NULL AND TRIM(concept_code) <> '' AND health_plan_code IS NOT NULL AND TRIM(health_plan_code) <> '' THEN
										SET @st = CONCAT("INSERT INTO dashboard_reporting_exemption SELECT '",exemption_date, "',", number_of_exemptions,",",department_code, ",", health_plan_code, ",'",concept_code,"','",gender,"',",exempted_value,",'",facility_code,"','",last_reporting_date,"'");
										PREPARE stmt FROM @st;
										EXECUTE stmt;
										DEALLOCATE PREPARE stmt;
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