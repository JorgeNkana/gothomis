DELIMITER $$

DROP PROCEDURE IF EXISTS `database_maintenance_generate_opd_disease_register` $$
CREATE PROCEDURE `database_maintenance_generate_opd_disease_register`(INOUT message varchar(50))
BEGIN		
	DECLARE facility_id VARCHAR(50);
	DECLARE done_with_icd_blocks BOOLEAN;
	DECLARE done_with_dates BOOLEAN;
	DECLARE icd_block VARCHAR(150);
	DECLARE diagnosis_date VARCHAR(150);
	
	CREATE TEMPORARY TABLE IF NOT EXISTS `diagnosis_dates`(
		diagnosis_date VARCHAR(50)
	);
	
	TRUNCATE tbl_opd_diseases_registers;
	
	TRUNCATE diagnosis_dates;
	
	SET done_with_icd_blocks = FALSE;
	SET done_with_dates = FALSE;
	SET facility_id = message;

	SET @st = CONCAT("INSERT INTO `diagnosis_dates` SELECT DISTINCT DATE(tbl_diagnoses.created_at) `date` FROM tbl_diagnoses WHERE  tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_diagnoses.admission_id IS NULL  order by id asc"); 
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
					
	BEGIN
		DECLARE icd_blocks
			CURSOR FOR
				SELECT tbl_opd_mtuha_icd_blocks.icd_block FROM tbl_opd_mtuha_icd_blocks WHERE tbl_opd_mtuha_icd_blocks.icd_block IS NOT NULL;
				
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_icd_blocks = TRUE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;

		OPEN icd_blocks;

		for_each_icd_block:
		LOOP
			FETCH NEXT FROM icd_blocks INTO icd_block;
			
			IF done_with_icd_blocks THEN 
				LEAVE for_each_icd_block; 
			ELSE
				BEGIN
					DECLARE dates
					CURSOR FOR
						SELECT diagnosis_dates.diagnosis_date FROM diagnosis_dates;
					
					DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_dates = TRUE;

					OPEN dates;
					
					for_each_date:
					LOOP
						FETCH NEXT FROM dates INTO diagnosis_date;
						
					
						IF done_with_dates THEN 
							LEAVE for_each_date; 
						ELSE
							BEGIN
								SET @st = CONCAT("SELECT COUNT(*), opd_mtuha_diagnosis_id INTO @cases, @mtuha_diagnosis_id FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND  CASE WHEN (LOCATE('.','",icd_block,"') <> 0 AND (SELECT COUNT(*) FROM tbl_opd_mtuha_icd_blocks WHERE icd_block='",icd_block,"') <> 0) THEN tbl_diagnosis_descriptions.code ELSE REPLACE(SUBSTRING(tbl_diagnosis_descriptions.code,1,4),'.','')  END = '",icd_block,"' JOIN tbl_opd_mtuha_icd_blocks ON icd_block = '",icd_block,"' WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) < 1 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
									BEGIN
										SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id = ",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"'");
										PREPARE stmt FROM @st;
										EXECUTE stmt;
										DEALLOCATE PREPARE stmt;
								
										IF @reported > 0 THEN
											BEGIN
												SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET male_under_one_month = male_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id=",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										ELSE
											BEGIN
												SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, male_under_one_month,date) SELECT '",facility_id,"',",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), ",",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",diagnosis_date,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										END IF;
									END;
								END IF;
								SET @cases = 0;
								SET @reported = 0;
								
								SET @st = CONCAT("SELECT COUNT(*), opd_mtuha_diagnosis_id INTO @cases, @mtuha_diagnosis_id FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND  CASE WHEN (LOCATE('.','",icd_block,"') <> 0 AND (SELECT COUNT(*) FROM tbl_opd_mtuha_icd_blocks WHERE icd_block='",icd_block,"') <> 0) THEN tbl_diagnosis_descriptions.code ELSE REPLACE(SUBSTRING(tbl_diagnosis_descriptions.code,1,4),'.','')  END = '",icd_block,"' JOIN tbl_opd_mtuha_icd_blocks ON icd_block = '",icd_block,"' WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) < 1 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
									BEGIN
										SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id = ",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"'");
										PREPARE stmt FROM @st;
										EXECUTE stmt;
										DEALLOCATE PREPARE stmt;
								
										IF @reported > 0 THEN
											BEGIN
												SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET female_under_one_month = female_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id=",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										ELSE
											BEGIN
												SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, female_under_one_month,date) SELECT '",facility_id,"',",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), ",",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",diagnosis_date,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										END IF;
									END;
								END IF;
								SET @cases = 0;
								SET @reported = 0;
								
								SET @st = CONCAT("SELECT COUNT(*), opd_mtuha_diagnosis_id INTO @cases, @mtuha_diagnosis_id FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND  CASE WHEN (LOCATE('.','",icd_block,"') <> 0 AND (SELECT COUNT(*) FROM tbl_opd_mtuha_icd_blocks WHERE icd_block='",icd_block,"') <> 0) THEN tbl_diagnosis_descriptions.code ELSE REPLACE(SUBSTRING(tbl_diagnosis_descriptions.code,1,4),'.','')  END = '",icd_block,"' JOIN tbl_opd_mtuha_icd_blocks ON icd_block = '",icd_block,"' WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) BETWEEN 1 AND 11 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
									BEGIN
										SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id = ",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"'");
										PREPARE stmt FROM @st;
										EXECUTE stmt;
										DEALLOCATE PREPARE stmt;
								
										IF @reported > 0 THEN
											BEGIN
												SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET male_under_one_year = male_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id=",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										ELSE
											BEGIN
												SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, male_under_one_year,date) SELECT '",facility_id,"',",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), ",",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",diagnosis_date,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										END IF;
									END;
								END IF;
								SET @cases = 0;
								SET @reported = 0;
								
								SET @st = CONCAT("SELECT COUNT(*), opd_mtuha_diagnosis_id INTO @cases, @mtuha_diagnosis_id FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND  CASE WHEN (LOCATE('.','",icd_block,"') <> 0 AND (SELECT COUNT(*) FROM tbl_opd_mtuha_icd_blocks WHERE icd_block='",icd_block,"') <> 0) THEN tbl_diagnosis_descriptions.code ELSE REPLACE(SUBSTRING(tbl_diagnosis_descriptions.code,1,4),'.','')  END = '",icd_block,"' JOIN tbl_opd_mtuha_icd_blocks ON icd_block = '",icd_block,"' WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) BETWEEN 1 AND 11 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
									BEGIN
										SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id = ",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"'");
										PREPARE stmt FROM @st;
										EXECUTE stmt;
										DEALLOCATE PREPARE stmt;
								
										IF @reported > 0 THEN
											BEGIN
												SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET female_under_one_year = female_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id=",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										ELSE
											BEGIN
												SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, female_under_one_year,date) SELECT '",facility_id,"',",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), ",",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",diagnosis_date,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										END IF;
									END;
								END IF;
								SET @cases = 0;
								SET @reported = 0;
								
								SET @st = CONCAT("SELECT COUNT(*), opd_mtuha_diagnosis_id INTO @cases, @mtuha_diagnosis_id FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND  CASE WHEN (LOCATE('.','",icd_block,"') <> 0 AND (SELECT COUNT(*) FROM tbl_opd_mtuha_icd_blocks WHERE icd_block='",icd_block,"') <> 0) THEN tbl_diagnosis_descriptions.code ELSE REPLACE(SUBSTRING(tbl_diagnosis_descriptions.code,1,4),'.','')  END = '",icd_block,"' JOIN tbl_opd_mtuha_icd_blocks ON icd_block = '",icd_block,"' WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) BETWEEN 12 AND 59 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
									BEGIN
										SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id = ",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"'");
										PREPARE stmt FROM @st;
										EXECUTE stmt;
										DEALLOCATE PREPARE stmt;
								
										IF @reported > 0 THEN
											BEGIN
												SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET male_under_five_year = male_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id=",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										ELSE
											BEGIN
												SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, male_under_five_year,date) SELECT '",facility_id,"',",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), ",",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",diagnosis_date,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										END IF;
									END;
								END IF;
								SET @cases = 0;
								SET @reported = 0;
								
								SET @st = CONCAT("SELECT COUNT(*), opd_mtuha_diagnosis_id INTO @cases, @mtuha_diagnosis_id FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND  CASE WHEN (LOCATE('.','",icd_block,"') <> 0 AND (SELECT COUNT(*) FROM tbl_opd_mtuha_icd_blocks WHERE icd_block='",icd_block,"') <> 0) THEN tbl_diagnosis_descriptions.code ELSE REPLACE(SUBSTRING(tbl_diagnosis_descriptions.code,1,4),'.','')  END = '",icd_block,"' JOIN tbl_opd_mtuha_icd_blocks ON icd_block = '",icd_block,"' WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) BETWEEN 12 AND 59 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
									BEGIN
										SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id = ",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"'");
										PREPARE stmt FROM @st;
										EXECUTE stmt;
										DEALLOCATE PREPARE stmt;
								
										IF @reported > 0 THEN
											BEGIN
												SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET female_under_five_year = female_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id=",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										ELSE
											BEGIN
												SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, female_under_five_year,date) SELECT '",facility_id,"',",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), ",",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",diagnosis_date,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										END IF;
									END;
								END IF;
								SET @cases = 0;
								SET @reported = 0;
								
								SET @st = CONCAT("SELECT COUNT(*), opd_mtuha_diagnosis_id INTO @cases, @mtuha_diagnosis_id FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND  CASE WHEN (LOCATE('.','",icd_block,"') <> 0 AND (SELECT COUNT(*) FROM tbl_opd_mtuha_icd_blocks WHERE icd_block='",icd_block,"') <> 0) THEN tbl_diagnosis_descriptions.code ELSE REPLACE(SUBSTRING(tbl_diagnosis_descriptions.code,1,4),'.','')  END = '",icd_block,"' JOIN tbl_opd_mtuha_icd_blocks ON icd_block = '",icd_block,"' WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) BETWEEN 60 AND 719\t AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
									BEGIN
										SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id = ",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"'");
										PREPARE stmt FROM @st;
										EXECUTE stmt;
										DEALLOCATE PREPARE stmt;
								
										IF @reported > 0 THEN
											BEGIN
												SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET male_above_five_under_sixty = male_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id=",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										ELSE
											BEGIN
												SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, male_above_five_under_sixty,date) SELECT '",facility_id,"',",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), ",",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",diagnosis_date,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										END IF;
									END;
								END IF;
								SET @cases = 0;
								SET @reported = 0;
								
								SET @st = CONCAT("SELECT COUNT(*), opd_mtuha_diagnosis_id INTO @cases, @mtuha_diagnosis_id FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND  CASE WHEN (LOCATE('.','",icd_block,"') <> 0 AND (SELECT COUNT(*) FROM tbl_opd_mtuha_icd_blocks WHERE icd_block='",icd_block,"') <> 0) THEN tbl_diagnosis_descriptions.code ELSE REPLACE(SUBSTRING(tbl_diagnosis_descriptions.code,1,4),'.','')  END = '",icd_block,"' JOIN tbl_opd_mtuha_icd_blocks ON icd_block = '",icd_block,"' WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) BETWEEN 60 AND 719 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
									BEGIN
										SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id = ",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"'");
										PREPARE stmt FROM @st;
										EXECUTE stmt;
										DEALLOCATE PREPARE stmt;
								
										IF @reported > 0 THEN
											BEGIN
												SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET female_above_five_under_sixty = female_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id=",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										ELSE
											BEGIN
												SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, female_above_five_under_sixty,date) SELECT '",facility_id,"',",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), ",",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",diagnosis_date,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										END IF;
									END;
								END IF;
								SET @cases = 0;
								SET @reported = 0;
								
								SET @st = CONCAT("SELECT COUNT(*), opd_mtuha_diagnosis_id INTO @cases, @mtuha_diagnosis_id FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND  CASE WHEN (LOCATE('.','",icd_block,"') <> 0 AND (SELECT COUNT(*) FROM tbl_opd_mtuha_icd_blocks WHERE icd_block='",icd_block,"') <> 0) THEN tbl_diagnosis_descriptions.code ELSE REPLACE(SUBSTRING(tbl_diagnosis_descriptions.code,1,4),'.','')  END = '",icd_block,"' JOIN tbl_opd_mtuha_icd_blocks ON icd_block = '",icd_block,"' WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) >= 720 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
									BEGIN
										SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id = ",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"'");
										PREPARE stmt FROM @st;
										EXECUTE stmt;
										DEALLOCATE PREPARE stmt;
								
										IF @reported > 0 THEN
											BEGIN
												SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET male_above_sixty = male_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id=",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										ELSE
											BEGIN
												SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, male_above_sixty,date) SELECT '",facility_id,"',",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), ",",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",diagnosis_date,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										END IF;
									END;
								END IF;
								SET @cases = 0;
								SET @reported = 0;	
								
								SET @st = CONCAT("SELECT COUNT(*), opd_mtuha_diagnosis_id INTO @cases, @mtuha_diagnosis_id FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND  CASE WHEN (LOCATE('.','",icd_block,"') <> 0 AND (SELECT COUNT(*) FROM tbl_opd_mtuha_icd_blocks WHERE icd_block='",icd_block,"') <> 0) THEN tbl_diagnosis_descriptions.code ELSE REPLACE(SUBSTRING(tbl_diagnosis_descriptions.code,1,4),'.','')  END = '",icd_block,"' JOIN tbl_opd_mtuha_icd_blocks ON icd_block = '",icd_block,"' WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) >= 720 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
								PREPARE stmt FROM @st;
								EXECUTE stmt;
								DEALLOCATE PREPARE stmt;
								
								IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
									BEGIN
										SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id = ",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"'");
										PREPARE stmt FROM @st;
										EXECUTE stmt;
										DEALLOCATE PREPARE stmt;
								
										IF @reported > 0 THEN
											BEGIN
												SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET female_above_sixty = female_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id=",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), " and date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										ELSE
											BEGIN
												SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, female_above_sixty,date) SELECT '",facility_id,"',",CAST(@mtuha_diagnosis_id AS CHAR CHARACTER SET UTF8), ",",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",diagnosis_date,"'");
												PREPARE stmt FROM @st;
												EXECUTE stmt;
												DEALLOCATE PREPARE stmt;
											END;
										END IF;
									END;
								END IF;
								SET @cases = 0;
								SET @reported = 0;							
							END;
						END IF;
					END LOOP;
					CLOSE dates;
				END;
			END IF;
			SET done_with_dates = FALSE;
		END LOOP;
		CLOSE icd_blocks;
	END;
	
	SET done_with_dates = FALSE;
	
	BEGIN
		DECLARE dates
			CURSOR FOR
				SELECT diagnosis_dates.diagnosis_date FROM diagnosis_dates;
		
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_dates = TRUE;

		OPEN dates;

		for_each_date:
		LOOP
			FETCH NEXT FROM dates INTO diagnosis_date;
			IF done_with_dates THEN 
				LEAVE for_each_date; 
			ELSE
				BEGIN
					-- other
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND NOT EXISTS (SELECT icd_block FROM tbl_opd_mtuha_icd_blocks WHERE icd_block=tbl_diagnosis_descriptions.code) WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) < 1 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
					
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET male_under_one_month = male_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, male_under_one_month,date) SELECT '",facility_id,"',NULL,",@cases,",'",diagnosis_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					-- other
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND NOT EXISTS (SELECT icd_block FROM tbl_opd_mtuha_icd_blocks WHERE icd_block=tbl_diagnosis_descriptions.code) WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) < 1 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
					
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET female_under_one_month = female_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, female_under_one_month,date) SELECT '",facility_id,"',NULL,",@cases,",'",diagnosis_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					-- other
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND NOT EXISTS (SELECT icd_block FROM tbl_opd_mtuha_icd_blocks WHERE icd_block=tbl_diagnosis_descriptions.code) WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) BETWEEN 1 AND 11 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
					
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET male_under_one_year = male_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, male_under_one_year,date) SELECT '",facility_id,"',NULL,",@cases,",'",diagnosis_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					-- other
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND NOT EXISTS (SELECT icd_block FROM tbl_opd_mtuha_icd_blocks WHERE icd_block=tbl_diagnosis_descriptions.code) WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) BETWEEN 1 AND 11 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
					
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET female_under_one_year = female_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, female_under_one_year,date) SELECT '",facility_id,"',NULL,",@cases,",'",diagnosis_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					-- other
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND NOT EXISTS (SELECT icd_block FROM tbl_opd_mtuha_icd_blocks WHERE icd_block=tbl_diagnosis_descriptions.code) WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) BETWEEN 12 AND 59 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
					
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET male_under_five_year = male_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, male_under_five_year,date) SELECT '",facility_id,"',NULL,",@cases,",'",diagnosis_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					-- other
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '919' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND NOT EXISTS (SELECT icd_block FROM tbl_opd_mtuha_icd_blocks WHERE icd_block=tbl_diagnosis_descriptions.code) WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) BETWEEN 12 AND 59 AND DATE(tbl_diagnosis_details.created_at)='2018-07-18'");
					
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET female_under_five_year = female_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, female_under_five_year,date) SELECT '",facility_id,"',NULL,",@cases,",'",diagnosis_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					-- other
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND NOT EXISTS (SELECT icd_block FROM tbl_opd_mtuha_icd_blocks WHERE icd_block=tbl_diagnosis_descriptions.code) WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) BETWEEN 60 AND 719 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
					
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET male_above_five_under_sixty = male_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, male_above_five_under_sixty,date) SELECT '",facility_id,"',NULL,",@cases,",'",diagnosis_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					-- other
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND NOT EXISTS (SELECT icd_block FROM tbl_opd_mtuha_icd_blocks WHERE icd_block=tbl_diagnosis_descriptions.code) WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) BETWEEN 60 AND 719 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
					
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET female_above_five_under_sixty = female_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, female_above_five_under_sixty,date) SELECT '",facility_id,"',NULL,",@cases,",'",diagnosis_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					-- other
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND NOT EXISTS (SELECT icd_block FROM tbl_opd_mtuha_icd_blocks WHERE icd_block=tbl_diagnosis_descriptions.code) WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) >= 720 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
					
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET male_above_sixty = male_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, male_above_sixty,date) SELECT '",facility_id,"',NULL,",@cases,",'",diagnosis_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					-- other
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_diagnoses ON tbl_diagnoses.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_diagnoses.patient_id AND tbl_diagnoses.admission_id IS NULL JOIN tbl_diagnosis_details ON tbl_diagnosis_details.status='Confirmed' AND tbl_diagnosis_details.diagnosis_id = tbl_diagnoses.id JOIN tbl_diagnosis_descriptions ON  tbl_diagnosis_descriptions.id = tbl_diagnosis_details.diagnosis_description_id AND NOT EXISTS (SELECT icd_block FROM tbl_opd_mtuha_icd_blocks WHERE icd_block=tbl_diagnosis_descriptions.code) WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_diagnosis_details.created_at) >= 720 AND DATE(tbl_diagnosis_details.created_at)='",diagnosis_date,"'");
					
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 AND @mtuha_diagnosis_id IS NOT NULL THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_opd_diseases_registers WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_opd_diseases_registers SET female_above_sixty = female_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE opd_mtuha_diagnosis_id IS NULL AND date='",diagnosis_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id, female_above_sixty,date) SELECT '",facility_id,"',NULL,",@cases,",'",diagnosis_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
				END;
			END IF;
		END LOOP;
	CLOSE dates;
	END;
	
	SET @st = CONCAT("UPDATE tbl_opd_diseases_registers set total_under_one_month = male_under_one_month+female_under_one_month where facility_id=",facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_opd_diseases_registers set total_under_one_year = male_under_one_year+female_under_one_year where facility_id=", facility_id); 
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_opd_diseases_registers set total_under_five_year = male_under_five_year+female_under_five_year where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_opd_diseases_registers set total_above_five_under_sixty = male_above_five_under_sixty+female_above_five_under_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_opd_diseases_registers set total_above_sixty = male_above_sixty+female_above_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_opd_diseases_registers set total_male = male_under_one_month+male_under_one_year+male_under_five_year+male_above_five_under_sixty+male_above_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_opd_diseases_registers set total_female=female_under_one_month+female_under_one_year+female_under_five_year+female_above_five_under_sixty+female_above_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_opd_diseases_registers set grand_total = total_male+total_female where facility_id=", facility_id);
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;