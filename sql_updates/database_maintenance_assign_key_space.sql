DELIMITER $$

DROP PROCEDURE IF EXISTS `database_maintenance_remove_unlinked_data` $$
CREATE PROCEDURE `database_maintenance_remove_unlinked_data`(database_name varchar(50), parent_table_name varchar(50))
BEGIN
	DECLARE done_with_cursor BOOLEAN;
	DECLARE table_name VARCHAR(50);
	DECLARE column_name VARCHAR(50);
	DECLARE referenced_table_name VARCHAR(50);
	DECLARE referenced_column_name VARCHAR(50);
	DECLARE constraint_name VARCHAR(50);
	SET max_sp_recursion_depth=500;
	
	CREATE TEMPORARY TABLE IF NOT EXISTS `dependant_tables`(
		table_name VARCHAR(50),
		column_name VARCHAR(50),
		referenced_table_name VARCHAR(50),
		referenced_column_name VARCHAR(50),
		constraint_name VARCHAR(50)
	);
	
	SET done_with_cursor = FALSE;
	
	SET @st = CONCAT("INSERT INTO dependant_tables SELECT usages.TABLE_NAME, usages.COLUMN_NAME, usages.REFERENCED_TABLE_NAME, usages.REFERENCED_COLUMN_NAME, usages.CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE usages JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS constraints ON usages.TABLE_SCHEMA = constraints.CONSTRAINT_SCHEMA AND usages.CONSTRAINT_NAME = constraints.CONSTRAINT_NAME AND constraints.CONSTRAINT_TYPE = 'FOREIGN KEY' WHERE usages.TABLE_SCHEMA='",database_name,"' AND usages.REFERENCED_TABLE_NAME='",parent_table_name,"'");
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	BEGIN
		DECLARE cleaner
		CURSOR FOR
			SELECT * FROM dependant_tables;

		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_cursor = TRUE;

		OPEN cleaner;

		repetition:
		LOOP
			FETCH NEXT FROM cleaner INTO table_name, column_name, referenced_table_name, referenced_column_name, constraint_name;
			IF done_with_cursor THEN 
				LEAVE repetition; 
			ELSE
				BEGIN
					SET @st = CONCAT("SELECT COUNT(*) INTO @children FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE usages JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS constraints ON usages.TABLE_SCHEMA = constraints.CONSTRAINT_SCHEMA AND usages.CONSTRAINT_NAME = constraints.CONSTRAINT_NAME AND constraints.CONSTRAINT_TYPE = 'FOREIGN KEY' WHERE usages.TABLE_SCHEMA='",database_name,"' AND usages.REFERENCED_TABLE_NAME='",table_name,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @children > 0 THEN
						CALL database_maintenance_remove_unlinked_data(database_name, table_name);
					ELSE
						SET @st = CONCAT("DELETE FROM ",table_name," WHERE ",column_name, " NOT IN (SELECT ",referenced_column_name," FROM ",referenced_table_name,")");
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
					END IF;
				END;
			END IF;
		END LOOP;
		CLOSE cleaner;
	END;
END $$

CREATE OR REPLACE PROCEDURE `database_maintenance_assign_key_space`(INOUT message varchar(50), key_space int)
MAINTENANCE: BEGIN
	DECLARE done_with_cursor BOOLEAN;
	DECLARE done_with_children_cursor BOOLEAN;
	DECLARE done_with_apply_keys_cursor BOOLEAN;
	DECLARE database_name VARCHAR(50);
	DECLARE table_on_which_conversion_failed VARCHAR(50);
	DECLARE table_name VARCHAR(50);
	DECLARE parent_table_name VARCHAR(50);
	DECLARE child_table_name VARCHAR(50);
	DECLARE column_name VARCHAR(50);
	DECLARE referenced_table_name VARCHAR(50);
	DECLARE referenced_column_name VARCHAR(50);
	DECLARE constraint_name VARCHAR(50);
	DECLARE old_key INT;
	DECLARE new_key INT;
	
	
	SET max_sp_recursion_depth=500;
	
	CREATE TEMPORARY TABLE IF NOT EXISTS `dependant_tables`(
		table_name VARCHAR(50),
		column_name VARCHAR(50),
		referenced_table_name VARCHAR(50),
		referenced_column_name VARCHAR(50),
		constraint_name VARCHAR(50)
	);
	
	CREATE TEMPORARY TABLE IF NOT EXISTS `temp_keys`(
		old_key INT,
		new_key INT
	);
	
	SET done_with_cursor = FALSE;
	SET done_with_children_cursor = FALSE;
	SET done_with_apply_keys_cursor = FALSE;
	SET database_name = message;
	SET table_on_which_conversion_failed = "";
	/* SET FOREIGN_KEY_CHECKS = 0;*/
	
	
	SET @st = CONCAT("INSERT INTO dependant_tables SELECT usages.TABLE_NAME, usages.COLUMN_NAME, usages.REFERENCED_TABLE_NAME, usages.REFERENCED_COLUMN_NAME, usages.CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE usages JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS constraints ON usages.TABLE_SCHEMA = constraints.CONSTRAINT_SCHEMA AND usages.CONSTRAINT_NAME = constraints.CONSTRAINT_NAME AND constraints.CONSTRAINT_TYPE = 'FOREIGN KEY' WHERE usages.TABLE_SCHEMA='",database_name,"'");
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	DROP TABLE IF EXISTS `database_maintenance_failed_conversion`;
	START TRANSACTION;
		BEGIN
			DECLARE table_constraints
			CURSOR FOR
				SELECT dependant_tables.table_name, dependant_tables.column_name, dependant_tables.referenced_table_name, dependant_tables.referenced_column_name, dependant_tables.constraint_name
				FROM `dependant_tables`; 
			
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_cursor = TRUE;
			DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN 
				SET message = 0;
				CREATE TABLE IF NOT EXISTS `database_maintenance_failed_conversion`(table_name VARCHAR(50), `time` TIMESTAMP);
				INSERT INTO `database_maintenance_failed_conversion` SELECT table_on_which_conversion_failed, CURRENT_TIMESTAMP;
				ROLLBACK;
				RESIGNAL;
			END;
			
			OPEN table_constraints;
			repetition:
			LOOP
				FETCH NEXT FROM table_constraints INTO table_name, column_name, referenced_table_name, referenced_column_name, constraint_name;
				IF done_with_cursor THEN 
					LEAVE repetition; 
				ELSE
					BEGIN /* MAKE ALL FOREIGN KEYS CASCADE ON UPDATE*/
						SET table_on_which_conversion_failed = table_name;
						
						SET @st = CONCAT("ALTER TABLE ",table_name," DROP FOREIGN KEY IF EXISTS `",constraint_name,"`");
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						CALL database_maintenance_remove_unlinked_data(database_name, table_name);
						
						SET @st = CONCAT("ALTER TABLE ",table_name," ADD FOREIGN KEY IF NOT EXISTS `",constraint_name,"`(",column_name,") REFERENCES ", referenced_table_name,"(",referenced_column_name,") ON UPDATE CASCADE");
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
					END;
				END IF;
			END LOOP;
			CLOSE table_constraints;
		END;
	COMMIT;
	
	SET done_with_cursor = False;
	/* SET FOREIGN_KEY_CHECKS=1;*/
	
	/* CHECK IF ANY OF THE KEY CHANGES FAILED */
	SET @st = CONCAT("SELECT CASE WHEN COUNT(*) > 0 THEN TRUE ELSE FALSE END INTO @key_conversion_failed FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='",database_name,"' AND TABLE_NAME = 'database_maintenance_failed_conversion'");
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	IF @key_conversion_failed THEN
		LEAVE MAINTENANCE;
	END IF;
	/* END CHECKING FOR FAILED CONVERSION */
	
	BEGIN
		DECLARE tables_involved
		CURSOR FOR
			SELECT tbl_involved_tables.table_name FROM tbl_involved_tables; /* PARENT TABLES THAT NEED UNIVERSAL KEYS*/

		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_cursor = TRUE;
		DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN 
			SET message = 0;
			CREATE TABLE IF NOT EXISTS `database_maintenance_failed_conversion`(table_name VARCHAR(50), `time` TIMESTAMP);
			INSERT INTO `database_maintenance_failed_conversion` SELECT table_on_which_conversion_failed, CURRENT_TIMESTAMP;
		END;
		
		OPEN tables_involved;
		START TRANSACTION;
			repetition:
			LOOP
				FETCH NEXT FROM tables_involved INTO parent_table_name;
				IF done_with_cursor THEN 
					LEAVE repetition; 
				ELSE
					BEGIN
						SET table_on_which_conversion_failed = parent_table_name;
						
						/* DETERMINE THE NEXT VALUE FOR SETTING THE ULTIMATE NEXT AUTO_INCREMENT VALUE ON THE TABLE*/
						SET @st =CONCAT("SELECT MAX(id)+1 INTO @next_value FROM ",parent_table_name);
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						/* BACKUP THE CURRENT KEY VALUES */
						SET @st =CONCAT("ALTER TABLE ",parent_table_name," ADD COLUMN IF NOT EXISTS `copy_id` INT");
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						SET @st =CONCAT("UPDATE ",parent_table_name," SET `copy_id` = id");
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						/* CONSTRUCT THE NEW_KEY - OLD_KEY VALUE PAIRS */
						SET @st =CONCAT("TRUNCATE temp_keys");
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						SET @st =CONCAT("INSERT INTO temp_keys SELECT  copy_id, id+",key_space," FROM ",parent_table_name," ORDER BY copy_id ASC");
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						SET done_with_apply_keys_cursor = False;
						
						/* UPDATE THE KEY FIELD WITH THE CORRESPONDING MATCHED NEW_KEY VALUE */
						BEGIN
							DECLARE apply_keys
							CURSOR FOR
								SELECT * FROM temp_keys ORDER BY temp_keys.old_key DESC;/* The order is very important here */
							DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_apply_keys_cursor = TRUE;
							DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN 
								SET message = 0;
								CREATE TABLE IF NOT EXISTS `database_maintenance_failed_conversion`(table_name VARCHAR(50), `time` TIMESTAMP);
								INSERT INTO `database_maintenance_failed_conversion` SELECT table_on_which_conversion_failed, CURRENT_TIMESTAMP;
								ROLLBACK;
							END;
							
							OPEN apply_keys;
								update_keys:
								LOOP
									FETCH NEXT FROM apply_keys INTO old_key, new_key;
									IF done_with_apply_keys_cursor THEN 
										LEAVE update_keys; 
									ELSE
										BEGIN 
											/* AT THIS STAGE, THE FOREIGN KEYS ATTACHED TO THIS KEY VALUES ARE SUPPOSED TO UPDATE THEMSELVES BY THE CASCADE EFFECT */
											SET @st =CONCAT("UPDATE ",parent_table_name," SET `id` = ",new_key, " WHERE copy_id=",old_key);
											PREPARE stmt FROM @st;
											EXECUTE stmt;
											DEALLOCATE PREPARE stmt;
										END;
									END IF;
								END LOOP;
							CLOSE apply_keys;
						END;
						
						/* CHECK IF ANY OF THE KEY CHANGES FAILED */
						SET @st = CONCAT("SELECT CASE WHEN COUNT(*) > 0 THEN TRUE ELSE FALSE END INTO @key_conversion_failed FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='",database_name,"' AND TABLE_NAME = 'database_maintenance_failed_conversion'");
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
						
						IF @key_conversion_failed THEN
							LEAVE MAINTENANCE;
						END IF;
						/* END CHECKING FOR FAILED CONVERSION */
						
						/* SET THE NEXT AUTO_INCREMENT VALUE ON THE TABLE */
						SET @st =CONCAT("ALTER TABLE ",parent_table_name," AUTO_INCREMENT = ",@next_value+key_space);
						PREPARE stmt FROM @st;
						EXECUTE stmt;
						DEALLOCATE PREPARE stmt;
					END;
				END IF;
			END LOOP;
		COMMIT;
		CLOSE tables_involved;
	END;
	
	SET done_with_cursor = False;
	
	BEGIN
		DECLARE tables_involved
		CURSOR FOR
			SELECT tbl_involved_tables.table_name FROM tbl_involved_tables;

		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_cursor = TRUE;
		DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN SET message = 0; END;
		
		OPEN tables_involved;
		repetition:
		LOOP
			FETCH NEXT FROM tables_involved INTO parent_table_name;
			IF done_with_cursor THEN 
				LEAVE repetition; 
			ELSE
				BEGIN
					SET @st =CONCAT("ALTER TABLE ",parent_table_name," DROP COLUMN IF EXISTS `copy_id`");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
				END;
			END IF;
		END LOOP;
		CLOSE tables_involved;
	END;
END $$

DELIMITER ;