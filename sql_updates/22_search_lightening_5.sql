alter table tbl_patients add column if not exists search_field varchar(150) null after medical_record_number;
DELIMITER //
DROP TRIGGER IF EXISTS `create_tbl_patients_search_values`//
CREATE OR REPLACE TRIGGER create_tbl_patients_search_values BEFORE INSERT ON tbl_patients FOR EACH ROW BEGIN SET NEW.search_field = concat(NEW.medical_record_number,ifnull(trim(NEW.first_name),''),ifnull(trim(NEW.last_name),''),ifnull(trim(NEW.middle_name),''),ifnull(NEW.mobile_number,'')); END//
DELIMITER ;
update tbl_patients set first_name = trim(first_name), middle_name = trim(middle_name), last_name=trim(last_name), search_field = concat(medical_record_number,ifnull(trim(first_name),''),ifnull(trim(last_name),''),ifnull(trim(middle_name),''),ifnull(mobile_number,''));