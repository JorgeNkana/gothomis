DELIMITER $$

DROP PROCEDURE IF EXISTS `generate_rnr_for_elmis` $$
CREATE PROCEDURE `generate_rnr_for_elmis`(INOUT message varchar(50), last_reporting_date date)
PROC:BEGIN
	DECLARE facility_id VARCHAR(50);
	DECLARE facility_code VARCHAR(50);
	DECLARE done_with_items BOOLEAN;
	DECLARE item_id INT;
	DECLARE item_code VARCHAR(50);
	
	IF CURRENT_DATE = DATE(last_reporting_date) THEN
		LEAVE PROC;
	END IF;
	
	CREATE OR REPLACE TABLE `rnr_for_elmis` (
		  `sourceOrderId` TIMESTAMP default CURRENT_TIMESTAMP,
		  `emergency` varchar(50) default 'false',
		  `program_code` varchar(50) NULL,
		  `concept_code` varchar(50) NOT NULL,
		  `quantityReceived` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `beginningBalance` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `quantityDispensed` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `stockInHand` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `stockOutDays` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `adjustment` int(10) UNSIGNED NOT NULL DEFAULT '0',
		  `quantityRequested` int(10) UNSIGNED NULL DEFAULT NULL,
		  `reasonForRequestedQuantity` VARCHAR(350) NULL DEFAULT NULL,
		  `rnr_month` int(1) UNSIGNED NOT NULL,
		  `facility_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

	TRUNCATE `rnr_for_elmis`;
	
	SET done_with_items = FALSE;
	SET facility_id = message;
	
	SET facility_code = (SELECT tbl_facilities.facility_code FROM tbl_facilities WHERE id = facility_id);
	SET facility_code = (SELECT INSERT(REGEXP_REPLACE(facility_code, '[_-]', ''), LENGTH(REGEXP_REPLACE(facility_code, '[_-]', '')), 0,'-'));
	
	BEGIN
		DECLARE items
		CURSOR FOR
			SELECT DISTINCT tbl_items.id, tbl_item_type_mappeds.item_code FROM tbl_items JOIN tbl_receiving_items ON tbl_items.msd_product AND tbl_items.id = tbl_receiving_items.item_id JOIN tbl_item_type_mappeds ON tbl_items.id = tbl_item_type_mappeds.item_id; -- need to select from rnr mapped codes
		
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
						SET @st = CONCAT("SELECT IFNULL(SUM(quantity),0) INTO @quantityReceived FROM tbl_receiving_items WHERE transaction_type_id = 1 AND item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control_in = 'r' AND MONTH(tbl_receiving_items.created_at) = MONTH(CURRENT_DATE)-1"); 
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						SET @st = CONCAT("SELECT ((SELECT IFNULL(SUM(quantity),0) FROM tbl_receiving_items WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l' AND MONTH(tbl_receiving_items.created_at) = MONTH(CURRENT_DATE)-2)+(SELECT IFNULL(SUM(quantity),0) FROM tbl_sub_stores WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l' AND MONTH(tbl_sub_stores.created_at) = MONTH(CURRENT_DATE)-2)+(SELECT IFNULL(SUM(quantity_received),0) FROM tbl_dispensers WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l' AND MONTH(tbl_dispensers.created_at) = MONTH(CURRENT_DATE)-2)) INTO @beginningBalance"); 
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						SET @st = CONCAT("SELECT IFNULL(SUM(quantity),0) INTO @quantityDispensed FROM tbl_prescriptions WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND dispensing_status = 1 AND MONTH(updated_at) = MONTH(CURRENT_DATE)-1"); 
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						SET @st = CONCAT("SELECT ((SELECT IFNULL(SUM(quantity),0) FROM tbl_receiving_items WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l' AND MONTH(tbl_receiving_items.created_at) = MONTH(CURRENT_DATE)-1)+(SELECT IFNULL(SUM(quantity),0) FROM tbl_sub_stores WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l' AND MONTH(tbl_sub_stores.created_at) = MONTH(CURRENT_DATE)-1)+(SELECT IFNULL(SUM(quantity_received),0) FROM tbl_dispensers WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l' AND MONTH(tbl_dispensers.created_at) = MONTH(CURRENT_DATE)-1)) INTO @stockInHand"); 
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						SET @st = CONCAT("SELECT IFNULL((SELECT MAX(updated_at) FROM tbl_receiving_items WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l' AND quantity = 0 AND MONTH(tbl_receiving_items.created_at) = MONTH(CURRENT_DATE)-1),0), IFNULL((SELECT MAX(updated_at) FROM tbl_sub_stores WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l' AND quantity = 0 AND MONTH(tbl_sub_stores.created_at) = MONTH(CURRENT_DATE)-1),0),IFNULL((SELECT MAX(updated_at) FROM tbl_dispensers WHERE item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND control = 'l' AND quantity_received = 0 AND MONTH(tbl_dispensers.created_at) = MONTH(CURRENT_DATE)-1),0) INTO @sinceMain, @sinceSubstore, @sinceDisp"); 
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						IF @beginningBalance THEN
							-- take the current one
							IF (@sinceMain > @sinceSubstore) AND (@sinceMain > @sinceDisp) THEN
								SET @stockOutDays = TIMESTAMPDIFF(DAY, @sinceMain, CURRENT_DATE);
							ELSEIF (@sinceSubstore > @sinceMain) AND (@sinceSubstore > @sinceDisp) THEN
								SET @stockOutDays = TIMESTAMPDIFF(DAY, @sinceSubstore, CURRENT_DATE);
							ELSEIF (@sinceDisp > @sinceMain) AND (@sinceDisp > @sinceSubstore) THEN
								SET @stockOutDays = TIMESTAMPDIFF(DAY, @sinceDisp, CURRENT_DATE);
							END IF;
						END IF;
						
						
						SET @st = CONCAT("SELECT SUM(IFNULL(tbl_receiving_items.quantity) + IFNULL(tbl_receiving_items.issued_quantity)) INTO @adjustment FROM tbl_receiving_items WHERE tbl_receiving_items.item_id=",CAST(item_id AS CHAR CHARACTER SET UTF8)," AND tbl_receiving_items.transaction_type_id <> 1 AND MONTH(tbl_receiving_items.created_at) = MONTH(CURRENT_DATE)-1");
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						
						SET @st = CONCAT("INSERT INTO rnr_for_elmis(,concept_code, quantityReceived, beginningBalance, quantityDispensed,stockInHand,stockOutDays,adjustment, rnr_month, facility_code) SELECT '",item_code,"', '",CAST(@quantityReceived AS CHAR CHARACTER SET UTF8), "','",CAST(@beginningBalance AS CHAR CHARACTER SET UTF8), "','",CAST(@quantityDispensed AS CHAR CHARACTER SET UTF8),"','",CAST(@stockInHand AS CHAR CHARACTER SET UTF8),"','",CAST(IFNULL(@stockOutDays,'') AS CHAR CHARACTER SET UTF8),"',",CAST(IFNULL(@adjustment,0) AS CHAR CHARACTER SET UTF8),",MONTH(CURRENT_DATE)-1,'",facility_code,"'");
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