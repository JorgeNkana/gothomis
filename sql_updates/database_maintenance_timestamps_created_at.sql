DELIMITER $$

DROP PROCEDURE IF EXISTS `database_maintenance_timestamps_created_at` $$
CREATE PROCEDURE `database_maintenance_timestamps_created_at`(INOUT message varchar(50))
BEGIN
	
	DECLARE done_with_cursor BOOLEAN;
	DECLARE database_name VARCHAR(50);
	DECLARE table_name VARCHAR(150);
	
	CREATE TEMPORARY TABLE IF NOT EXISTS `tables`(
		table_name VARCHAR(50)
	);
	
	SET database_name = message;
	
	SET @st = CONCAT("INSERT INTO tables (SELECT INFORMATION_SCHEMA.columns.table_name FROM INFORMATION_SCHEMA.columns JOIN INFORMATION_SCHEMA.tables ON INFORMATION_SCHEMA.columns.table_name = INFORMATION_SCHEMA.tables.table_name WHERE INFORMATION_SCHEMA.tables.table_schema='",database_name,"' AND INFORMATION_SCHEMA.tables.table_type = 'BASE TABLE' AND INFORMATION_SCHEMA.columns.column_name='created_at' AND (INFORMATION_SCHEMA.columns.extra = 'on update CURRENT_TIMESTAMP' OR INFORMATION_SCHEMA.columns.column_default = '0000-00-00 00:00:00'))");
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	BEGIN
		DECLARE timestamps
		CURSOR FOR
			SELECT tables.table_name FROM tables;

		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_cursor = TRUE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;

		OPEN timestamps;

		repetition:
		LOOP
			FETCH NEXT FROM timestamps INTO table_name;
			IF done_with_cursor THEN 
				LEAVE repetition; 
			ELSE
				BEGIN
					SET @st = CONCAT("ALTER TABLE ",table_name," CHANGE created_at created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
				END;
			END IF;
		END LOOP;
	END;
END $$

DELIMITER ;