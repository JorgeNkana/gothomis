DELIMITER $$

DROP PROCEDURE IF EXISTS `generate_rnr_adjustiments_for_elmis` $$
CREATE PROCEDURE `generate_rnr_adjustiments_for_elmis`(INOUT message varchar(50), last_reporting_date date)
PROC:BEGIN
	DECLARE facility_id VARCHAR(50);
	DECLARE facility_code VARCHAR(50);
	DECLARE done_with_items BOOLEAN;
	DECLARE done_with_adjustiments BOOLEAN;
	DECLARE item_id INT;
	DECLARE adjustment_quantity INT;
	DECLARE item_code VARCHAR(50);
	DECLARE adjustment_code VARCHAR(50);
	
	IF CURRENT_DATE = DATE(last_reporting_date) THEN
		TRUNCATE rnr_adjustiments_for_elmis;
		LEAVE PROC;
	END IF;
	
	
	CREATE OR REPLACE TABLE `rnr_adjustiments_for_elmis` (
		  `program_code` varchar(50) NOT NULL,
		  `sourceOrderId` varchar(50)  NULL,
		  `concept_code` varchar(50) NOT NULL,
		  `adjustment_code` varchar(50) NOT NULL,
		  `adjusted_quantity` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `rnr_month` int(1) UNSIGNED NOT NULL,
		  `facility_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

	TRUNCATE `rnr_adjustiments_for_elmis`;
	
	SET done_with_items = FALSE;
	SET done_with_adjustiments = FALSE;
	SET facility_id = message;
	
	SET facility_code = (SELECT tbl_facilities.facility_code FROM tbl_facilities WHERE id = facility_id);
	SET facility_code = (SELECT INSERT(REGEXP_REPLACE(facility_code, '[_-]', ''), LENGTH(REGEXP_REPLACE(facility_code, '[_-]', '')), 0,'-'));
	
	BEGIN
		DECLARE items
		CURSOR FOR
			SELECT DISTINCT tbl_item_type_mappeds.item_id, UPPER(tbl_item_type_mappeds.item_code) FROM tbl_items JOIN tbl_receiving_items ON tbl_items.id = tbl_receiving_items.item_id JOIN tbl_item_type_mappeds ON tbl_items.msd_product AND tbl_items.id = tbl_item_type_mappeds.item_id AND tbl_item_type_mappeds.item_category = 'medication' AND tbl_receiving_items.transaction_type_id <> 1; -- need to select from rnr mapped codes
		
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
					SET done_with_adjustiments = FALSE;
					IF item_code IS NOT NULL OR TRIM(item_code) <> '' THEN
						BEGIN
							DECLARE adjustments
								CURSOR FOR 
									SELECT IFNULL(tbl_receiving_items.quantity,0) + IFNULL(tbl_receiving_items.issued_quantity,0), tbl_transaction_types.adjustment_code FROM tbl_receiving_items JOIN tbl_transaction_types ON tbl_receiving_items.transaction_type_id = tbl_transaction_types.id WHERE tbl_receiving_items.item_id = item_id AND transaction_type_id <> 1 AND MONTH(tbl_receiving_items.created_at) = MONTH(CURRENT_DATE)-1;
							
							DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_adjustiments = TRUE;
							DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
						
							OPEN adjustments;
							
							for_each_adjustiment:
							LOOP
								FETCH NEXT FROM adjustments INTO adjustment_quantity, adjustment_code;
								
								IF done_with_adjustiments THEN 
									LEAVE for_each_adjustiment; 
								ELSE
									BEGIN
										IF adjustment_code IS NOT NULL AND adjustment_quantity IS NOT NULL AND adjustment_quantity <> 0 THEN
											SET @st = CONCAT("INSERT INTO rnr_adjustiments_for_elmis(concept_code,adjustment_code,adjusted_quantity,rnr_month,facility_code)  SELECT '",item_code,"', '",adjustment_code,"','",adjustment_quantity,"',MONTH(CURRENT_DATE)-1,'",facility_code,"'");
											PREPARE stmt FROM @st;
											EXECUTE stmt;
											DEALLOCATE PREPARE stmt;
										END IF;
									END;
								END IF;
							END LOOP;
						END;
					END IF;
				END;
			END IF;
		END LOOP;
		BEGIN
			UPDATE `rnr_for_elmis` SET adjustment = (SELECT SUM(CASE WHEN adjustment_code IS NULL OR adjustment_code IN ('TRANSFER_IN','CLINIC_RETURN') THEN adjusted_quantity ELSE (-adjusted_quantity) END) FROM rnr_adjustiments_for_elmis WHERE concept_code = `rnr_for_elmis`.concept_code);
		END;
	END;
END$$

DELIMITER ;