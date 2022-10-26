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
alter table `newDB`.`tbl_patients` change middle_name middle_name varchar(80) null;



TRUNCATE `newDB`.`tbl_patients`;
INSERT INTO `newDB`.`tbl_patients`(`medical_record_number`,`gender`,`first_name`,`last_name`,`mobile_number`, `dob`, `country_id`, `created_at`, `updated_at`, `facility_id`) SELECT concat('XXX',substring(account_number,(select instr(account_number, '-') from `oldDB`.`tumbi_customers` order by person_id desc limit 1))) as medical_record_number,upper(sex) as gender,upper(first_name),upper(last_name),phone_number,concat(DATE_FORMAT(`registration_date`,'%Y') - age,'-',lpad(case when `age_month` = '0' then 7 else `age_month` end,2,'0'),'-',lpad(`age_day`,2,'0')) AS dob,1 as country_id,concat(registration_date,'12:00:00') as created_at,concat(registration_date,'12:00:00') as updated_at,2 as facility_code FROM `oldDB`.`tumbi_customers` c JOIN `oldDB`.`tumbi_people` p WHERE c.person_id = p.person_id ORDER BY c.person_id;

TRUNCATE `newDB`.`tbl_accounts_numbers`;
INSERT INTO `newDB`.`tbl_accounts_numbers`(`patient_id`, `account_number`, `facility_id`,`date_attended`,`tallied`,`created_at`) SELECT  id, concat(lpad(id,6,'0'),CONCAT(LPAD(EXTRACT(MONTH FROM CURRENT_DATE),2,0), SUBSTRING(EXTRACT(YEAR FROM
CURRENT_DATE),3,2))), 2, date(`created_at`),0 ,`created_at` FROM `newDB`.`tbl_patients`;
SET FOREIGN_KEY_CHECKS=1;