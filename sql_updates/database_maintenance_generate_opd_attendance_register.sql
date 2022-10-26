DELIMITER $$

DROP PROCEDURE IF EXISTS `database_maintenance_generate_opd_attendance_register` $$
CREATE PROCEDURE `database_maintenance_generate_opd_attendance_register`(INOUT message varchar(50))
BEGIN		
	DECLARE facility_id VARCHAR(50);
	DECLARE facility_code VARCHAR(50);
	DECLARE attendance_date DATE;
	DECLARE done_with_dates BOOLEAN;
	
	CREATE TEMPORARY TABLE IF NOT EXISTS `attendance_dates`(
		attendance_date VARCHAR(50)
	);
	
	TRUNCATE tbl_newattendance_registers;
	TRUNCATE tbl_patient_registration_reports;
	
	TRUNCATE attendance_dates;
	
	SET done_with_dates = FALSE;
	SET facility_id = message;
	
	SET facility_code = (SELECT tbl_facilities.facility_code FROM tbl_facilities WHERE id = facility_id);
	
	SET @st = CONCAT("INSERT INTO `attendance_dates` SELECT DISTINCT DATE(created_at) FROM tbl_patients WHERE  tbl_patients.facility_id = '",facility_id,"' ORDER BY created_at asc"); 
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
					
	BEGIN
		DECLARE dates
		CURSOR FOR
			SELECT attendance_dates.attendance_date FROM attendance_dates;
		
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_with_dates = TRUE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET message = 0;
	
		OPEN dates;
		
		for_each_date:
		LOOP
			FETCH NEXT FROM dates INTO attendance_date;
			
			IF done_with_dates THEN 
				LEAVE for_each_date; 
			ELSE
				BEGIN
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='MALE' AND DATE(tbl_patients.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob, tbl_patients.created_at) < 1");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_newattendance_registers WHERE date='",attendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_newattendance_registers SET male_under_one_month = male_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_patient_registration_reports SET male_under_one_month = male_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_newattendance_registers(clinic_id,facility_id, male_under_one_month,date) SELECT 1, '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_patient_registration_reports(facility_code, male_under_one_month,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='FEMALE' AND DATE(tbl_patients.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_patients.created_at) < 1");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_newattendance_registers WHERE date='",attendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_newattendance_registers SET female_under_one_month = female_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_patient_registration_reports SET female_under_one_month = female_under_one_month+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_newattendance_registers(clinic_id,facility_id, female_under_one_month,date) SELECT 1, '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_patient_registration_reports(facility_code, female_under_one_month,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='MALE' AND DATE(tbl_patients.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_patients.created_at) BETWEEN 1 AND 11");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_newattendance_registers WHERE date='",attendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_newattendance_registers SET male_under_one_year = male_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_patient_registration_reports SET male_under_one_year = male_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_newattendance_registers(clinic_id,facility_id, male_under_one_year,date) SELECT 1, '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_patient_registration_reports(facility_code, male_under_one_year,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='FEMALE' AND DATE(tbl_patients.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_patients.created_at) BETWEEN 1 AND 11");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_newattendance_registers WHERE date='",attendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_newattendance_registers SET female_under_one_year = female_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_patient_registration_reports SET female_under_one_year = female_under_one_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_newattendance_registers(clinic_id,facility_id, female_under_one_year,date) SELECT 1, '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_patient_registration_reports(facility_code, female_under_one_year,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='MALE' AND DATE(tbl_patients.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_patients.created_at) BETWEEN 12 AND 59");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_newattendance_registers WHERE date='",attendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_newattendance_registers SET male_under_five_year = male_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_patient_registration_reports SET male_under_five_year = male_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_newattendance_registers(clinic_id,facility_id, male_under_five_year,date) SELECT 1, '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_patient_registration_reports(facility_code, male_under_five_year,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='FEMALE' AND DATE(tbl_patients.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_patients.created_at) BETWEEN 12 AND 59");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_newattendance_registers WHERE date='",attendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_newattendance_registers SET female_under_five_year = female_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_patient_registration_reports SET female_under_five_year = female_under_five_year+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_newattendance_registers(clinic_id,facility_id, female_under_five_year,date) SELECT 1, '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_patient_registration_reports(facility_code, female_under_five_year,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='MALE' AND DATE(tbl_patients.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_patients.created_at) BETWEEN 60 AND 719\t");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_newattendance_registers WHERE date='",attendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_newattendance_registers SET male_above_five_under_sixty = male_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_patient_registration_reports SET male_above_five_under_sixty = male_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_newattendance_registers(clinic_id,facility_id, male_above_five_under_sixty,date) SELECT 1, '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_patient_registration_reports(facility_code, male_above_five_under_sixty,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='FEMALE' AND DATE(tbl_patients.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_patients.created_at) BETWEEN 60 AND 719");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_newattendance_registers WHERE date='",attendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_newattendance_registers SET female_above_five_under_sixty = female_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_patient_registration_reports SET female_above_five_under_sixty = female_above_five_under_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_newattendance_registers(clinic_id,facility_id, female_above_five_under_sixty,date) SELECT 1, '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_patient_registration_reports(facility_code, female_above_five_under_sixty,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='MALE' AND DATE(tbl_patients.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_patients.created_at) >= 720");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_newattendance_registers WHERE date='",attendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_newattendance_registers SET male_above_sixty = male_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_patient_registration_reports SET male_above_sixty = male_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_newattendance_registers(clinic_id,facility_id, male_above_sixty,date) SELECT 1, '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_patient_registration_reports(facility_code, male_above_sixty,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							END IF;
						END;
					END IF;
					SET @cases = 0;
					SET @reported = 0;	
					
					SET @st = CONCAT("SELECT COUNT(*) INTO @cases FROM tbl_patients WHERE tbl_patients.facility_id = '",facility_id,"' AND gender='FEMALE' AND DATE(tbl_patients.created_at) = '",attendance_date,"' AND TIMESTAMPDIFF(MONTH, dob,  tbl_patients.created_at) >= 720");
					PREPARE stmt FROM @st;
					EXECUTE stmt;
					DEALLOCATE PREPARE stmt;
					
					IF @cases > 0 THEN
						BEGIN
							SET @st = CONCAT("SELECT COUNT(*) INTO @reported FROM tbl_newattendance_registers WHERE date='",attendance_date,"'");
							PREPARE stmt FROM @st;
							EXECUTE stmt;
							DEALLOCATE PREPARE stmt;
					
							IF @reported > 0 THEN
								BEGIN
									SET @st = CONCAT("UPDATE tbl_newattendance_registers SET female_above_sixty = female_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_id='",facility_id,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("UPDATE tbl_patient_registration_reports SET female_above_sixty = female_above_sixty+",CAST(@cases AS CHAR CHARACTER SET UTF8), " WHERE date='",attendance_date,"' AND facility_code='",facility_code,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
								END;
							ELSE
								BEGIN
									SET @st = CONCAT("INSERT INTO  tbl_newattendance_registers(clinic_id,facility_id, female_above_sixty,date) SELECT 1, '",facility_id,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
									PREPARE stmt FROM @st;
									EXECUTE stmt;
									DEALLOCATE PREPARE stmt;
									SET @st = CONCAT("INSERT INTO tbl_patient_registration_reports(facility_code, female_above_sixty,date) SELECT '",facility_code,"',",CAST(@cases AS CHAR CHARACTER SET UTF8), ",'",attendance_date,"'");
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
	
	
	SET @st = CONCAT("UPDATE tbl_newattendance_registers set total_under_one_month = male_under_one_month+female_under_one_month where facility_id=",facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_newattendance_registers set total_under_one_year = male_under_one_year+female_under_one_year where facility_id=", facility_id); 
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_newattendance_registers set total_under_five_year = male_under_five_year+female_under_five_year where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_newattendance_registers set total_above_five_under_sixty = male_above_five_under_sixty+female_above_five_under_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_newattendance_registers set total_above_sixty = male_above_sixty+female_above_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_newattendance_registers set total_male = male_under_one_month+male_under_one_year+male_under_five_year+male_above_five_under_sixty+male_above_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_newattendance_registers set total_female=female_under_one_month+female_under_one_year+female_under_five_year+female_above_five_under_sixty+female_above_sixty where facility_id=", facility_id);
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_newattendance_registers set grand_total = total_male+total_female where facility_id=", facility_id);
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	
	
	SET @st = CONCAT("UPDATE tbl_patient_registration_reports set total_under_one_month = male_under_one_month+female_under_one_month where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_patient_registration_reports set total_under_one_year = male_under_one_year+female_under_one_year where facility_code='",facility_code,"'"); 
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_patient_registration_reports set total_under_five_year = male_under_five_year+female_under_five_year where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_patient_registration_reports set total_above_five_under_sixty = male_above_five_under_sixty+female_above_five_under_sixty where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_patient_registration_reports set total_above_sixty = male_above_sixty+female_above_sixty where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_patient_registration_reports set total_male = male_under_one_month+male_under_one_year+male_under_five_year+male_above_five_under_sixty+male_above_sixty where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_patient_registration_reports set total_female=female_under_one_month+female_under_one_year+female_under_five_year+female_above_five_under_sixty+female_above_sixty where facility_code='",facility_code,"'");
	
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
	SET @st = CONCAT("UPDATE tbl_patient_registration_reports set grand_total = total_male+total_female where facility_code='",facility_code, "'");
	PREPARE stmt FROM @st;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;