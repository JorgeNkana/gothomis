cd\

cd xampp\mysql\bin


mysql -u root -p



use newDB;

SET FOREIGN_KEY_CHECKS=0;

alter table `newDB`.`tbl_next_of_kins` change residence_id residence_id int(10) unsigned null;
alter table `newDB`.users DROP INDEX if exists   `users_email_unique`;
ALTER TABLE `newDB`.tbl_patients DROP INDEX if exists  tbl_patients_medical_record_number_unique;
alter table `newDB`.`users` change mobile_number mobile_number varchar(50) null;
alter table `newDB`.`users` change gender gender varchar(50) null;
alter table `newDB`.`users` change proffesionals_id proffesionals_id int(10) unsigned null;
alter table `newDB`.`tbl_patients` change dob dob date null;
alter table `newDB`.`tbl_patients` change user_id user_id int(10) unsigned null;

-- DELETE FROM `newDB`.`users` WHERE id > 10;
ALTER TABLE `newDB`.`users` AUTO_INCREMENT = 11;
INSERT INTO `newDB`.`users`(`name`, `email`, `password`,`facility_id`,`proffesionals_id`) SELECT  `name`, `email`, '$2y$10$qGb5SJj./27G/5uksOxj7e1HvPO2GCQSKINcm.ZWqTY2rz.oSXLZG',2,1 FROM `oldDB`.`users` where `email` not in (select `email` from `newDB`.`users`);

TRUNCATE `newDB`.`tbl_patients`;
INSERT INTO `newDB`.`tbl_patients`(id,`first_name`, `middle_name`, `last_name`, `medical_record_number`, `gender`, `dob`, `marital_id`, `mobile_number`, `country_id`, `created_at`, `updated_at`, `facility_id`, occupation_id) SELECT  id, upper(`first_name`), upper(`middle_name`), upper(`last_name`), concat('XXX',substring(medical_record_number,(select instr(medical_record_number, '-') from `oldDB`.`patients` order by id desc limit 1))) as medical_record_number, upper(`gender`), (case when date_of_birth = '' or date_of_birth is null or length(date_of_birth) <> 10 then null else date(concat(substring(date_of_birth,7,5),'-',substring(date_of_birth,4,2),'-',substring(date_of_birth,1,2))) end), case when `marital_status` = 'married' then 1 else case when `marital_status` = 'single' then 2 else case when `marital_status` = 'divorced' then 3 else case when `marital_status` = 'COHABITING' then 4 else 5 end end end end, `phone_number`,1,`created_at`, `updated_at`, 2, (select id from newDB.tbl_occupations where occupation_name = `oldDB`.`patients`.`occupation_code` limit 1) FROM `oldDB`.`patients`;

TRUNCATE `newDB`.`tbl_next_of_kins`;
INSERT INTO `newDB`.`tbl_next_of_kins`(`patient_id`, `next_of_kin_name`, `mobile_number`) SELECT  id, upper(`next_of_kin`), `kin_phone_number` FROM `oldDB`.`patients` where `next_of_kin` <> '' or `next_of_kin` is not null;

TRUNCATE `newDB`.`tbl_accounts_numbers`;
INSERT INTO `newDB`.`tbl_accounts_numbers`(`patient_id`, `account_number`, `facility_id`,`date_attended`,`tallied`,`created_at`) SELECT  id, concat(lpad(id,6,'0'),CONCAT(LPAD(EXTRACT(MONTH FROM CURRENT_DATE),2,0), SUBSTRING(EXTRACT(YEAR FROM
CURRENT_DATE),3,2))), 2, date(`created_at`),0 ,(select `created_at` from `oldDB`.`encounters` where `patient_id` = `oldDB`.`patients`.`id` order by id asc limit 1) FROM `oldDB`.`patients`;
SET FOREIGN_KEY_CHECKS=1;