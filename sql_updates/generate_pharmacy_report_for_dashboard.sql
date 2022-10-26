DELIMITER $$

DROP PROCEDURE IF EXISTS `generate_pharmacy_report_for_dashboard` $$
CREATE PROCEDURE `generate_pharmacy_report_for_dashboard`(INOUT message varchar(50), last_reporting_date date)
PROC:BEGIN
	DECLARE facility_id VARCHAR(50);
	DECLARE facility_code VARCHAR(50);
	DECLARE done_with_items BOOLEAN;
	DECLARE item_id INT;
	DECLARE item_code VARCHAR(50);
	
	IF CURRENT_DATE = DATE(last_reporting_date) THEN
		TRUNCATE dashboard_reporting_pharmacy;
		LEAVE PROC;
	END IF;
	
	CREATE OR REPLACE TABLE `dashboard_reporting_pharmacy` (
		  `department_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `health_plan_code` varchar(6) COLLATE utf8mb4_unicode_ci NULL,
		  `concept_code` varchar(50) NOT NULL,
		  `balance` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `quantity_used` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `OS` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `number_of_clients_dispensed` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `number_of_clients_os` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `facility_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
		  `reporting_date` date NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

	TRUNCATE `dashboard_reporting_pharmacy`;
	
	SET done_with_items = FALSE;
	SET facility_id = message;
	
	
	SET facility_code = (SELECT (SELECT INSERT(REGEXP_REPLACE(tbl_facilities.facility_code, '[_-]', ''), LENGTH(REGEXP_REPLACE(tbl_facilities.facility_code, '[_-]', '')), 0,'-')) FROM tbl_facilities WHERE id = message);
	
	BEGIN
		DECLARE items
		CURSOR FOR
			SELECT DISTINCT tbl_items.id, UPPER(tbl_item_type_mappeds.item_code) FROM tbl_items JOIN tbl_receiving_items ON tbl_items.id = tbl_receiving_items.item_id JOIN tbl_item_type_mappeds ON tbl_items.id = tbl_item_type_mappeds.item_id;
		
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_items = TRUE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
	
		OPEN items;
		
		for_each_item:
		LOOP
			FETCH NEXT FROM items INTO item_id, item_code;
			
			IF done_with_items THEN 
				LEAVE for_each_item; 
			ELSE
				BEGIN
					IF item_code IS NOT NULL OR TRIM(item_code) <> '' THEN
						SET @st = CONCAT("SELECT ((SELECT IFNULL(SUM(quantity),0) FROM tbl_receiving_items WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l')+(SELECT IFNULL(SUM(quantity),0) FROM tbl_sub_stores WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l')+(SELECT IFNULL(SUM(quantity_received),0) FROM tbl_dispensers WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l')) INTO @balance"); 
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						SET @st = CONCAT("SELECT (NOT EXISTS(SELECT * FROM tbl_receiving_items WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l' AND quantity > 0) AND NOT EXISTS(SELECT * FROM tbl_sub_stores WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l' AND quantity > 0) AND NOT EXISTS(SELECT * FROM tbl_dispensers WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l' AND quantity_received >0)) INTO @OS"); 
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						SET @st = CONCAT("SELECT IFNULL(SUM(quantity),0) INTO @quantity_used FROM tbl_prescriptions WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND dispensing_status = 1 AND DATE(updated_at) = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)"); 
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						SET @st = CONCAT("SELECT COUNT(DISTINCT patient_id) INTO @number_of_clients_dispensed FROM tbl_prescriptions WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND dispensing_status = 1 AND DATE(updated_at) = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)"); 
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						SET @st = CONCAT("SELECT COUNT(DISTINCT patient_id) INTO @number_of_clients_os FROM tbl_prescriptions WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND out_of_stock IS NOT NULL AND DATE(updated_at) = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)"); 
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						SET @st = CONCAT("INSERT INTO dashboard_reporting_pharmacy SELECT '0PHARM', NULL, '",CAST(item_code AS CHAR CHARACTER SET UTF8), "',",CAST(@balance AS CHAR CHARACTER SET UTF8),",",CAST(@quantity_used AS CHAR CHARACTER SET UTF8),",",CAST(@OS AS CHAR CHARACTER SET UTF8),",",CAST(@number_of_clients_dispensed AS CHAR CHARACTER SET UTF8),",",CAST(@number_of_clients_os AS CHAR CHARACTER SET UTF8),",'",facility_code,"', CURRENT_DATE"); 
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
					END IF;
				END;
			END IF;
		END LOOP;
	END;
END$$

DELIMITER ;