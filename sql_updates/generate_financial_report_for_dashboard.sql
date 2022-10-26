DELIMITER $$

DROP PROCEDURE IF EXISTS `generate_financial_report_for_dashboard` $$
CREATE PROCEDURE `generate_financial_report_for_dashboard`(INOUT message varchar(50), last_reporting_date date)
PROC:BEGIN		
	DECLARE facility_id VARCHAR(50);
	DECLARE facility_code VARCHAR(50);
	DECLARE transaction_date DATE;
	DECLARE done_with_dates BOOLEAN;
	DECLARE done_with_transactions BOOLEAN;
	DECLARE amount DOUBLE(12,2);
	DECLARE quantity DOUBLE(12,2);
	DECLARE health_plan_code VARCHAR(50);
	DECLARE concept_code VARCHAR(50);
	DECLARE department_code VARCHAR(50);
	DECLARE paid_in_cash BOOLEAN;
	
	IF DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) = DATE(last_reporting_date) THEN
		TRUNCATE dashboard_reporting_fiancial_transaction;
		LEAVE PROC;
	END IF;
					
	CREATE TEMPORARY TABLE IF NOT EXISTS `transaction_dates`(
		transaction_date VARCHAR(50)
	);
	
	
	CREATE OR REPLACE TEMPORARY TABLE `transactions` (
		  `department_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `health_plan_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `concept_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `quantity` decimal(8,2) NOT NULL,
		  `amount` decimal(12,2) NOT NULL,
		  `paid_in_cash` BOOLEAN
		);

	
	
	CREATE OR REPLACE TABLE `dashboard_reporting_fiancial_transaction` (
		  `department_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `transaction_date` date NOT NULL,
		  `health_plan_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `concept_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `quantity` decimal(8,2) NOT NULL,
		  `amount` decimal(12,2) NOT NULL,
		  `paid_in_cash` BOOLEAN DEFAULT FALSE,
		  `facility_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `reporting_date` date NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

	TRUNCATE `dashboard_reporting_fiancial_transaction`;
	TRUNCATE transaction_dates;
	
	SET done_with_dates = FALSE;
	SET done_with_transactions = FALSE;
	SET facility_id = message;
	
	SET facility_code = (SELECT tbl_facilities.facility_code FROM tbl_facilities WHERE id = facility_id);
	SET facility_code = (SELECT INSERT(REGEXP_REPLACE(facility_code, '[_-]', ''), LENGTH(REGEXP_REPLACE(facility_code, '[_-]', '')), 0,'-'));
	
	SET @st = CONCAT("INSERT INTO `transaction_dates` SELECT DISTINCT date(tbl_encounter_invoices.created_at) FROM tbl_encounter_invoices WHERE date(tbl_encounter_invoices.created_at) > '",last_reporting_date,"' AND date(tbl_encounter_invoices.created_at) < CURRENT_DATE ORDER BY tbl_encounter_invoices.created_at asc"); 
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET last_reporting_date = (SELECT MAX(transaction_dates.transaction_date) FROM transaction_dates);
	
	BEGIN
		DECLARE dates
		CURSOR FOR
			SELECT transaction_dates.transaction_date FROM transaction_dates;
		
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_dates = TRUE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
	
		OPEN dates;
		
		
		for_each_date:
		LOOP
			FETCH NEXT FROM dates INTO transaction_date;
			SET done_with_transactions = FALSE;
					
			IF done_with_dates THEN 
				LEAVE for_each_date; 
			ELSE
				BEGIN
					SET done_with_transactions = FALSE;
					TRUNCATE transactions;
					SET @st = CONCAT("INSERT INTO transactions SELECT tbl_items.dept_id, tbl_invoice_lines.payment_filter,UPPER(tbl_item_type_mappeds.item_code),SUM(tbl_invoice_lines.quantity), SUM(tbl_invoice_lines.price*tbl_invoice_lines.quantity), CASE WHEN tbl_invoice_lines.is_payable THEN TRUE ELSE FALSE END FROM tbl_encounter_invoices JOIN tbl_invoice_lines ON date(tbl_invoice_lines.created_at) = '",transaction_date,"' AND tbl_encounter_invoices.id = tbl_invoice_lines.invoice_id AND (tbl_invoice_lines.is_payable IS NOT TRUE OR (tbl_invoice_lines.is_payable AND tbl_invoice_lines.status_id = 2)) JOIN tbl_item_prices ON tbl_invoice_lines.item_price_id=tbl_item_prices.id JOIN tbl_items ON tbl_item_prices.item_id = tbl_items.id JOIN tbl_item_type_mappeds ON tbl_items.id = tbl_item_type_mappeds.item_id GROUP BY tbl_item_type_mappeds.item_code,tbl_items.dept_id,tbl_invoice_lines.payment_filter");
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
							FETCH NEXT FROM transactions INTO department_code, health_plan_code, concept_code, quantity, amount, paid_in_cash;
						
							IF done_with_transactions THEN 
								LEAVE for_each_transaction; 
							ELSE
								BEGIN
									IF concept_code IS NOT NULL AND TRIM(concept_code) <> '' AND health_plan_code IS NOT NULL AND TRIM(health_plan_code) <> '' THEN
										SET @st = CONCAT("INSERT INTO dashboard_reporting_fiancial_transaction SELECT ", department_code, ",'", transaction_date, "',", health_plan_code, ",'",concept_code,"',",quantity,",",amount,",",paid_in_cash,",'",facility_code,"','",last_reporting_date,"'");
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