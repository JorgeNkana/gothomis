DELIMITER $$

CREATE OR REPLACE PROCEDURE `database_maintenance_facility_list_correction`(INOUT message varchar(50), new_facility_id int, old_facility_id int, old_email varchar(150), old_address varchar(150), old_mobile_number varchar(50))
BEGIN

	DECLARE done_with_cursor BOOLEAN;
	DECLARE database_name VARCHAR(50);
	DECLARE table_name VARCHAR(50);
	DECLARE key_column VARCHAR(50);
	
			
	CREATE TEMPORARY TABLE IF NOT EXISTS `dependant_tables`(
		table_name VARCHAR(50),
		key_column VARCHAR(50)
	);
	
	SET done_with_cursor = FALSE;
	SET database_name = message;
	
	
	SET @st = CONCAT("INSERT INTO dependant_tables SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA='",database_name,"' AND REFERENCED_TABLE_SCHEMA='",database_name,"' AND REFERENCED_TABLE_NAME='tbl_facilities'");
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	BEGIN
		DECLARE correction
		CURSOR FOR
			SELECT dependant_tables.table_name, dependant_tables.key_column
			FROM `dependant_tables`; 

		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_cursor = TRUE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
		
		OPEN correction;
		correction_repetition:
		LOOP
			FETCH NEXT FROM correction INTO table_name, key_column;
			IF done_with_cursor THEN 
				LEAVE correction_repetition; 
			ELSE
				BEGIN
					SET @st =CONCAT("UPDATE ",CAST(table_name AS CHAR CHARACTER SET UTF8)," SET ",CAST(key_column AS CHAR CHARACTER SET UTF8),"=",CAST(new_facility_id AS CHAR CHARACTER SET UTF8)," WHERE ",key_column," = ",CAST(old_facility_id AS CHAR CHARACTER SET UTF8));
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
				END;
			END IF;
		END LOOP;
		CLOSE correction;
		IF message <> '0' THEN
			BEGIN
				SET @st =CONCAT("UPDATE tbl_facilities SET email='",CAST(old_email AS CHAR CHARACTER SET UTF8),"', address='",CAST(old_address AS CHAR CHARACTER SET UTF8),"', mobile_number='",CAST(old_mobile_number AS CHAR CHARACTER SET UTF8),"' WHERE id='",CAST(new_facility_id AS CHAR CHARACTER SET UTF8),"'");
				PREPARE stmt FROM @st;
				EXECUTE stmt;
				DEALLOCATE PREPARE stmt;
			END;
		END IF;
	END;
END $$

DELIMITER ;