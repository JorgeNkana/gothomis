DELIMITER $$

DROP PROCEDURE IF EXISTS `database_maintenance_generate_admission_register` $$
CREATE PROCEDURE `database_maintenance_generate_admission_register`(INOUT message varchar(50))
BEGIN		
	DECLARE facility_id VARCHAR(50);
	DECLARE admission_date DATE;
	DECLARE done_with_dates BOOLEAN;
	
	CREATE TEMPORARY TABLE IF NOT EXISTS `admission_dates`(
		admission_date VARCHAR(50)
	);
	
	TRUNCATE tbl_admission_registers;
	
	TRUNCATE admission_dates;
	
	SET done_with_dates = FALSE;
	SET facility_id = message;

	SET @st = CONCAT("INSERT INTO `admission_dates` SELECT DISTINCT tbl_admissions.admission_date FROM tbl_admissions WHERE  tbl_admissions.facility_id = '",facility_id,"' ORDER BY admission_date asc"); 
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
					
	BEGIN
		DECLARE dates
		CURSOR FOR
			SELECT admission_dates.admission_date FROM admission_dates;
		
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_dates = TRUE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;

		OPEN dates;
		
		for_each_date:
		LOOP
			FETCH NEXT FROM dates INTO admission_date;
			
			IF done_with_dates THEN 
				LEAVE for_each_date; 
			ELSE
				BEGIN
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_admissions ON tbl_admissions.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_admissions.patient_id WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_admissions.admission_date) < 1 AND tbl_admissions.admission_date='",admission_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_admission_registers WHERE date='",admission_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_admission_registers SET male_under_one_month = male_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",admission_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_admission_registers(facility_id, male_under_one_month,date) SELECT '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",admission_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_admissions ON tbl_admissions.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_admissions.patient_id WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_admissions.admission_date) < 1 AND tbl_admissions.admission_date='",admission_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_admission_registers WHERE date='",admission_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_admission_registers SET female_under_one_month = female_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",admission_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_admission_registers(facility_id, female_under_one_month,date) SELECT '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",admission_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_admissions ON tbl_admissions.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_admissions.patient_id WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_admissions.admission_date) BETWEEN 1 AND 11 AND tbl_admissions.admission_date='",admission_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_admission_registers WHERE date='",admission_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_admission_registers SET male_under_one_year = male_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",admission_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_admission_registers(facility_id, male_under_one_year,date) SELECT '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",admission_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_admissions ON tbl_admissions.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_admissions.patient_id WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_admissions.admission_date) BETWEEN 1 AND 11 AND tbl_admissions.admission_date='",admission_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_admission_registers WHERE date='",admission_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_admission_registers SET female_under_one_year = female_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",admission_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_admission_registers(facility_id, female_under_one_year,date) SELECT '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",admission_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_admissions ON tbl_admissions.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_admissions.patient_id WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_admissions.admission_date) BETWEEN 12 AND 59 AND tbl_admissions.admission_date='",admission_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_admission_registers WHERE date='",admission_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_admission_registers SET male_under_five_year = male_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",admission_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_admission_registers(facility_id, male_under_five_year,date) SELECT '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",admission_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_admissions ON tbl_admissions.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_admissions.patient_id WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_admissions.admission_date) BETWEEN 12 AND 59 AND tbl_admissions.admission_date='",admission_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_admission_registers WHERE date='",admission_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_admission_registers SET female_under_five_year = female_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",admission_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_admission_registers(facility_id, female_under_five_year,date) SELECT '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",admission_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_admissions ON tbl_admissions.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_admissions.patient_id WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_admissions.admission_date) BETWEEN 60 AND 719 AND tbl_admissions.admission_date='",admission_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_admission_registers WHERE date='",admission_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_admission_registers SET male_above_five_under_sixty = male_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",admission_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_admission_registers(facility_id, male_above_five_under_sixty,date) SELECT '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",admission_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_admissions ON tbl_admissions.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_admissions.patient_id WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_admissions.admission_date) BETWEEN 60 AND 719 AND tbl_admissions.admission_date='",admission_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_admission_registers WHERE date='",admission_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_admission_registers SET female_above_five_under_sixty = female_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",admission_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_admission_registers(facility_id, female_above_five_under_sixty,date) SELECT '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",admission_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_admissions ON tbl_admissions.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_admissions.patient_id WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_admissions.admission_date) >= 720 AND tbl_admissions.admission_date='",admission_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_admission_registers WHERE date='",admission_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_admission_registers SET male_above_sixty = male_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",admission_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_admission_registers(facility_id, male_above_sixty,date) SELECT '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",admission_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;	
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_admissions ON tbl_admissions.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_admissions.patient_id WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_admissions.admission_date) >= 720 AND tbl_admissions.admission_date='",admission_date,"'");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_admission_registers WHERE date='",admission_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_admission_registers SET female_above_sixty = female_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",admission_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_admission_registers(facility_id, female_above_sixty,date) SELECT '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",admission_date,"'");
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
	
	
	SET @st = CONCAT("UPDATE tbl_admission_registers set total_under_one_month = male_under_one_month+female_under_one_month where facility_id=",facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_admission_registers set total_under_one_year = male_under_one_year+female_under_one_year where facility_id=", facility_id); 
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_admission_registers set total_under_five_year = male_under_five_year+female_under_five_year where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_admission_registers set total_above_five_under_sixty = male_above_five_under_sixty+female_above_five_under_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_admission_registers set total_above_sixty = male_above_sixty+female_above_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_admission_registers set total_male = male_under_one_month+male_under_one_year+male_under_five_year+male_above_five_under_sixty+male_above_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_admission_registers set total_female=female_under_one_month+female_under_one_year+female_under_five_year+female_above_five_under_sixty+female_above_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_admission_registers set grand_total = total_male+total_female where facility_id=", facility_id);
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;