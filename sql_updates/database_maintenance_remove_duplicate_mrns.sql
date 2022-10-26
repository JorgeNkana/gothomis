DELIMITER $$

CREATE OR REPLACE PROCEDURE `database_maintenance_remove_duplicate_mrns`(INOUT message varchar(50))
BEGIN

	DECLARE done_with_cursor BOOLEAN;
	DECLARE done_with_correction_cursor BOOLEAN;
	DECLARE corrected BOOLEAN;
	DECLARE database_name VARCHAR(50);
	DECLARE table_name VARCHAR(50);
	DECLARE key_column VARCHAR(50);
	DECLARE medical_record_number VARCHAR(50);
	DECLARE medical_record_number_previously_checked VARCHAR(50);
	DECLARE patient_id INT;
	DECLARE selected_patient_id INT;
	
	
	SET medical_record_number_previously_checked = '';
	SET selected_patient_id = NULL;
	SET done_with_cursor = FALSE;
	SET done_with_correction_cursor = FALSE;
	SET corrected = FALSE;
	SET database_name = message;
	
	CREATE TEMPORARY TABLE IF NOT EXISTS `clean_ids`(
		patient_id INT
	);
			
	CREATE TEMPORARY TABLE IF NOT EXISTS `duplicate_mrns`(
		patient_id INT,
		medical_record_number VARCHAR(50)
	);
			
	CREATE TEMPORARY TABLE IF NOT EXISTS `dependant_tables`(
		table_name VARCHAR(50),
		key_column VARCHAR(50)
	);
	
	TRUNCATE `duplicate_mrns`;
	TRUNCATE `dependant_tables`;
	SET @st = "INSERT INTO `duplicate_mrns` (SELECT id, tbl_patients.medical_record_number FROM tbl_patients WHERE medical_record_number IN (SELECT medical_record_number FROM tbl_patients GROUP BY tbl_patients.medical_record_number HAVING COUNT(tbl_patients.medical_record_number) > 1) ORDER BY tbl_patients.medical_record_number)";
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("INSERT INTO dependant_tables SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA='",database_name,"' AND REFERENCED_TABLE_SCHEMA='",database_name,"' AND REFERENCED_TABLE_NAME='tbl_patients'");
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	BEGIN
		DECLARE duplicates
		CURSOR FOR
			SELECT duplicate_mrns.patient_id, duplicate_mrns.medical_record_number
			FROM `duplicate_mrns`; 

		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_cursor = TRUE;
		DECLARE CONTINUE HANDLER FOR 1061 SET message = 1;
		DECLARE CONTINUE HANDLER FOR 1062 SET message = 0;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;

		OPEN duplicates;
	
		repetition:
		LOOP
			FETCH NEXT FROM duplicates INTO patient_id, medical_record_number;
			IF done_with_cursor THEN 
				LEAVE repetition; 
			ELSE
				BEGIN
					IF medical_record_number = '' THEN
						ITERATE repetition;
					END IF;
					
					IF medical_record_number_previously_checked <> '' AND medical_record_number_previously_checked <> medical_record_number THEN
						SET corrected = FALSE;
					END IF;
					
					IF medical_record_number_previously_checked = medical_record_number AND NOT corrected THEN
						BEGIN
							SET @st = CONCAT("SELECT count(*),tbl_accounts_numbers.patient_id INTO @count, @selected_patient_id FROM tbl_accounts_numbers JOIN tbl_patients ON tbl_accounts_numbers.patient_id = tbl_patients.id WHERE tbl_patients.medical_record_number='",CAST(medical_record_number AS CHAR CHARACTER SET UTF8),"' ORDER BY tbl_accounts_numbers.id DESC LIMIT 1");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
							IF @selected_patient_id IS NULL THEN
								ITERATE repetition;
							END IF;
							
							BEGIN
								DECLARE correction
								CURSOR FOR
									SELECT dependant_tables.table_name, dependant_tables.key_column
									FROM `dependant_tables`; 

								DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_correction_cursor = TRUE;
								
								OPEN correction;
								correction_repetition:
								LOOP
									FETCH NEXT FROM correction INTO table_name, key_column;
									IF done_with_correction_cursor THEN 
										LEAVE correction_repetition; 
									ELSE
										BEGIN
											DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 1;
											SET @st =CONCAT("UPDATE ",CAST(table_name AS CHAR CHARACTER SET UTF8)," SET ",CAST(key_column AS CHAR CHARACTER SET UTF8),"=",CAST(@selected_patient_id AS CHAR CHARACTER SET UTF8)," WHERE ",key_column," = ",CAST(patient_id AS CHAR CHARACTER SET UTF8));
											PREPARE stmt FROM @st;
											EXECUTE stmt;
											DEALLOCATE PREPARE stmt;
										END;
									END IF;
								END LOOP;
								CLOSE correction;
								INSERT INTO clean_ids SELECT selected_patient_id;
								SET corrected = TRUE;
							END;
						END;
					END IF;
					SET medical_record_number_previously_checked = medical_record_number;
				END;
			END IF;
		END LOOP;
		CLOSE duplicates;
		
		IF (SELECT COUNT(*) FROM clean_ids) > 0 THEN
			DELETE FROM `duplicate_mrns` WHERE `patient_id` NOT IN (SELECT clean_ids.`patient_id` FROM `clean_ids`);
			DELETE FROM `tbl_patients` WHERE `id` IN (SELECT duplicate_mrns.`patient_id` FROM `duplicate_mrns`);
		END IF;
		
		ALTER TABLE tbl_patients ADD CONSTRAINT `medical_record_number` UNIQUE(`medical_record_number`);
	END;
END $$

DELIMITER ;