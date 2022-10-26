DELIMITER $$

DROP PROCEDURE IF EXISTS `database_maintenance_generate_opd_reattendance_register` $$
CREATE PROCEDURE `database_maintenance_generate_opd_reattendance_register`(INOUT message varchar(50))
BEGIN		
	DECLARE facility_id VARCHAR(50);
	DECLARE facility_code VARCHAR(50);
	DECLARE reattendance_date DATE;
	DECLARE done_with_dates BOOLEAN;
	
	CREATE TEMPORARY TABLE IF NOT EXISTS `reattendance_dates`(
		reattendance_date VARCHAR(50)
	);
	
	TRUNCATE tbl_reattendance_registers;
	TRUNCATE tbl_reatend_patient_reports;
	
	TRUNCATE reattendance_dates;
	
	SET done_with_dates = FALSE;
	SET facility_id = message;
	
	SET facility_code = (SELECT tbl_facilities.facility_code FROM tbl_facilities WHERE id = facility_id);
	
	SET @st = CONCAT("INSERT INTO `reattendance_dates` SELECT DISTINCT date_attended FROM tbl_accounts_numbers WHERE  tbl_accounts_numbers.facility_id = '",facility_id,"' ORDER BY date_attended asc"); 
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
					
	BEGIN
		DECLARE dates
		CURSOR FOR
			SELECT reattendance_dates.reattendance_date FROM reattendance_dates;
		
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_dates = TRUE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
	
		OPEN dates;
		
		
		for_each_date:
		LOOP
			FETCH NEXT FROM dates INTO reattendance_date;
			
			IF done_with_dates THEN 
				LEAVE for_each_date; 
			ELSE
				BEGIN
				
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_accounts_numbers.facility_id = '", facility_id,"'  AND tbl_patients.id = tbl_accounts_numbers.patient_id WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_accounts_numbers.date_attended) < 1 AND tbl_accounts_numbers.date_attended='",reattendance_date,"' AND EXISTS (SELECT id FROM tbl_accounts_numbers t2 WHERE t2.patient_id =  tbl_accounts_numbers.patient_id and t2.facility_id = '", facility_id,"' AND t2.date_attended<>'",reattendance_date,"')");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_reattendance_registers WHERE date='",reattendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_reattendance_registers SET male_under_one_month = male_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_reatend_patient_reports SET male_under_one_month = male_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_reattendance_registers(clinic_id,facility_id, male_under_one_month,date) SELECT 1,'",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_reatend_patient_reports(facility_code, male_under_one_month,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_accounts_numbers.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_accounts_numbers.patient_id WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_accounts_numbers.date_attended) < 1 AND tbl_accounts_numbers.date_attended='",reattendance_date,"' AND EXISTS (SELECT id FROM tbl_accounts_numbers t2 WHERE t2.patient_id =  tbl_accounts_numbers.patient_id and t2.facility_id = '", facility_id,"' AND t2.date_attended<>'",reattendance_date,"')");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_reattendance_registers WHERE date='",reattendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_reattendance_registers SET female_under_one_month = female_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_reatend_patient_reports SET female_under_one_month = female_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_reattendance_registers(clinic_id,facility_id, female_under_one_month,date) SELECT 1,'",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_reatend_patient_reports(facility_code, female_under_one_month,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_accounts_numbers.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_accounts_numbers.patient_id WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_accounts_numbers.date_attended) BETWEEN 1 AND 11 AND tbl_accounts_numbers.date_attended='",reattendance_date,"' AND EXISTS (SELECT id FROM tbl_accounts_numbers t2 WHERE t2.patient_id =  tbl_accounts_numbers.patient_id and t2.facility_id = '", facility_id,"' AND t2.date_attended<>'",reattendance_date,"')");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_reattendance_registers WHERE date='",reattendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_reattendance_registers SET male_under_one_year = male_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_reatend_patient_reports SET male_under_one_year = male_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_reattendance_registers(clinic_id,facility_id, male_under_one_year,date) SELECT 1,'",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_reatend_patient_reports(facility_code, male_under_one_year,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_accounts_numbers.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_accounts_numbers.patient_id WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_accounts_numbers.date_attended) BETWEEN 1 AND 11 AND tbl_accounts_numbers.date_attended='",reattendance_date,"' AND EXISTS (SELECT id FROM tbl_accounts_numbers t2 WHERE t2.patient_id =  tbl_accounts_numbers.patient_id and t2.facility_id = '", facility_id,"' AND t2.date_attended<>'",reattendance_date,"')");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_reattendance_registers WHERE date='",reattendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_reattendance_registers SET female_under_one_year = female_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_reatend_patient_reports SET female_under_one_year = female_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_reattendance_registers(clinic_id,facility_id, female_under_one_year,date) SELECT 1,'",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_reatend_patient_reports(facility_code, female_under_one_year,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_accounts_numbers.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_accounts_numbers.patient_id WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_accounts_numbers.date_attended) BETWEEN 12 AND 59 AND tbl_accounts_numbers.date_attended='",reattendance_date,"' AND EXISTS (SELECT id FROM tbl_accounts_numbers t2 WHERE t2.patient_id =  tbl_accounts_numbers.patient_id and t2.facility_id = '", facility_id,"' AND t2.date_attended<>'",reattendance_date,"')");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_reattendance_registers WHERE date='",reattendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_reattendance_registers SET male_under_five_year = male_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_reatend_patient_reports SET male_under_five_year = male_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_reattendance_registers(clinic_id,facility_id, male_under_five_year,date) SELECT 1,'",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_reatend_patient_reports(facility_code, male_under_five_year,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_accounts_numbers.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_accounts_numbers.patient_id WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_accounts_numbers.date_attended) BETWEEN 12 AND 59 AND tbl_accounts_numbers.date_attended='",reattendance_date,"' AND EXISTS (SELECT id FROM tbl_accounts_numbers t2 WHERE t2.patient_id =  tbl_accounts_numbers.patient_id and t2.facility_id = '", facility_id,"' AND t2.date_attended<>'",reattendance_date,"')");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_reattendance_registers WHERE date='",reattendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_reattendance_registers SET female_under_five_year = female_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_reatend_patient_reports SET female_under_five_year = female_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_reattendance_registers(clinic_id,facility_id, female_under_five_year,date) SELECT 1,'",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_reatend_patient_reports(facility_code, female_under_five_year,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_accounts_numbers.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_accounts_numbers.patient_id WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_accounts_numbers.date_attended) BETWEEN 60 AND 719\t AND tbl_accounts_numbers.date_attended='",reattendance_date,"' AND EXISTS (SELECT id FROM tbl_accounts_numbers t2 WHERE t2.patient_id =  tbl_accounts_numbers.patient_id and t2.facility_id = '", facility_id,"' AND t2.date_attended<>'",reattendance_date,"')");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_reattendance_registers WHERE date='",reattendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_reattendance_registers SET male_above_five_under_sixty = male_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_reatend_patient_reports SET male_above_five_under_sixty = male_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_reattendance_registers(clinic_id,facility_id, male_above_five_under_sixty,date) SELECT 1,'",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_reatend_patient_reports(facility_code, male_above_five_under_sixty,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_accounts_numbers.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_accounts_numbers.patient_id WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_accounts_numbers.date_attended) BETWEEN 60 AND 719 AND tbl_accounts_numbers.date_attended='",reattendance_date,"' AND EXISTS (SELECT id FROM tbl_accounts_numbers t2 WHERE t2.patient_id =  tbl_accounts_numbers.patient_id and t2.facility_id = '", facility_id,"' AND t2.date_attended<>'",reattendance_date,"')");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_reattendance_registers WHERE date='",reattendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_reattendance_registers SET female_above_five_under_sixty = female_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_reatend_patient_reports SET female_above_five_under_sixty = female_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_reattendance_registers(clinic_id,facility_id, female_above_five_under_sixty,date) SELECT 1,'",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_reatend_patient_reports(facility_code, female_above_five_under_sixty,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_accounts_numbers.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_accounts_numbers.patient_id WHERE gender='MALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_accounts_numbers.date_attended) >= 720 AND tbl_accounts_numbers.date_attended='",reattendance_date,"' AND EXISTS (SELECT id FROM tbl_accounts_numbers t2 WHERE t2.patient_id =  tbl_accounts_numbers.patient_id and t2.facility_id = '", facility_id,"' AND t2.date_attended<>'",reattendance_date,"')");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_reattendance_registers WHERE date='",reattendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_reattendance_registers SET male_above_sixty = male_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_reatend_patient_reports SET male_above_sixty = male_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_reattendance_registers(clinic_id,facility_id, male_above_sixty,date) SELECT 1,'",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_reatend_patient_reports(facility_code, male_above_sixty,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;	
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients JOIN tbl_accounts_numbers ON tbl_accounts_numbers.facility_id = '",facility_id,"' AND tbl_patients.id = tbl_accounts_numbers.patient_id WHERE gender='FEMALE' AND TIMESTAMPDIFF(MONTH, dob,  tbl_accounts_numbers.date_attended) >= 720 AND tbl_accounts_numbers.date_attended='",reattendance_date,"' AND EXISTS (SELECT id FROM tbl_accounts_numbers t2 WHERE t2.patient_id =  tbl_accounts_numbers.patient_id and t2.facility_id = '", facility_id,"' AND t2.date_attended<>'",reattendance_date,"')");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_reattendance_registers WHERE date='",reattendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_reattendance_registers SET female_above_sixty = female_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_reatend_patient_reports SET female_above_sixty = female_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",reattendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_reattendance_registers(clinic_id,facility_id, female_above_sixty,date) SELECT 1,'",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_reatend_patient_reports(facility_code, female_above_sixty,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",reattendance_date,"'");
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
	
	
	SET @st = CONCAT("UPDATE tbl_reattendance_registers set total_under_one_month = male_under_one_month+female_under_one_month where facility_id=",facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reattendance_registers set total_under_one_year = male_under_one_year+female_under_one_year where facility_id=", facility_id); 
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reattendance_registers set total_under_five_year = male_under_five_year+female_under_five_year where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reattendance_registers set total_above_five_under_sixty = male_above_five_under_sixty+female_above_five_under_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reattendance_registers set total_above_sixty = male_above_sixty+female_above_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reattendance_registers set total_male = male_under_one_month+male_under_one_year+male_under_five_year+male_above_five_under_sixty+male_above_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reattendance_registers set total_female=female_under_one_month+female_under_one_year+female_under_five_year+female_above_five_under_sixty+female_above_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reattendance_registers set grand_total = total_male+total_female where facility_id=", facility_id, ";");
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	
	
	SET @st = CONCAT("UPDATE tbl_reatend_patient_reports set total_under_one_month = male_under_one_month+female_under_one_month where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reatend_patient_reports set total_under_one_year = male_under_one_year+female_under_one_year where facility_code='",facility_code,"'"); 
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reatend_patient_reports set total_under_five_year = male_under_five_year+female_under_five_year where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reatend_patient_reports set total_above_five_under_sixty = male_above_five_under_sixty+female_above_five_under_sixty where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reatend_patient_reports set total_above_sixty = male_above_sixty+female_above_sixty where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reatend_patient_reports set total_male = male_under_one_month+male_under_one_year+male_under_five_year+male_above_five_under_sixty+male_above_sixty where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reatend_patient_reports set total_female=female_under_one_month+female_under_one_year+female_under_five_year+female_above_five_under_sixty+female_above_sixty where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_reatend_patient_reports set grand_total = total_male+total_female where facility_code='",facility_code,"'");
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;