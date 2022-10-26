-- ##########################################################################
alter table tbl_facilities add column if not exists copy_id char(36) after id;
-- alter table tbl_facilities drop foreign key if exists tbl_facilities_council_id_foreign;
-- alter table tbl_facilities drop foreign key if exists tbl_facilities_region_id_foreign;
update tbl_facilities set copy_id = uuid();

alter table users add column if not exists copy_id char(36) after id;
update users set copy_id = uuid();
alter table users drop foreign key if exists users_facility_id_foreign;
alter table users change column  facility_id facility_id char(36);
update users set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id = facility_id limit 1);

alter table tbl_patients add column if not exists copy_id char(36) after id;
update tbl_patients set copy_id = uuid();
alter table tbl_patients drop foreign key if exists tbl_patients_user_id_foreign;
alter table tbl_patients drop foreign key if exists tbl_patients_residence_id_foreign;
alter table tbl_patients drop foreign key if exists tbl_patients_country_id_foreign;
alter table tbl_patients drop foreign key if exists tbl_patients_tribe_id_foreign;
alter table tbl_patients change column  user_id user_id char(36);
update tbl_patients set user_id = (select copy_id from users where users.id = user_id limit 1);
alter table tbl_patients drop foreign key if exists tbl_patients_facility_id_foreign;
alter table tbl_patients change column  facility_id facility_id char(36);
update tbl_patients set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id = facility_id limit 1);

alter table tbl_accounts_numbers add column if not exists copy_id char(36) after id;
update tbl_accounts_numbers set copy_id = uuid();
alter table tbl_accounts_numbers drop foreign key if exists tbl_accounts_numbers_patient_id_foreign;
alter table tbl_accounts_numbers change column  patient_id patient_id char(36);
update tbl_accounts_numbers set patient_id = (select copy_id from tbl_patients where tbl_patients.id = patient_id limit 1);
alter table tbl_accounts_numbers drop foreign key if exists tbl_accounts_numbers_user_id_foreign;
alter table tbl_accounts_numbers change column  user_id user_id char(36);
update tbl_accounts_numbers set user_id = (select copy_id from users where users.id = user_id limit 1);
alter table tbl_accounts_numbers drop foreign key if exists tbl_accounts_numbers_facility_id_foreign;
alter table tbl_accounts_numbers change column  facility_id facility_id char(36);
update tbl_accounts_numbers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id = facility_id limit 1);

alter table tbl_encounter_invoices add column if not exists copy_id char(36) after id;
update tbl_encounter_invoices set copy_id = uuid();
alter table tbl_encounter_invoices drop foreign key if exists tbl_encounter_invoices_account_number_id_foreign;
alter table tbl_encounter_invoices change column  account_number_id account_number_id char(36);
update tbl_encounter_invoices set account_number_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  account_number_id limit 1);
alter table tbl_encounter_invoices drop foreign key if exists tbl_encounter_invoices_user_id_foreign;
alter table tbl_encounter_invoices change column  user_id user_id char(36);
update tbl_encounter_invoices set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_encounter_invoices drop foreign key if exists tbl_encounter_invoices_facility_id_foreign;
alter table tbl_encounter_invoices change column  facility_id facility_id char(36);
update tbl_encounter_invoices set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_encounter_invoices drop foreign key if exists tbl_encounter_invoices_corpse_id_foreign;
alter table tbl_encounter_invoices change column  corpse_id corpse_id char(36);
update tbl_encounter_invoices set corpse_id = (select copy_id from tbl_corpses where tbl_corpses.id =  corpse_id limit 1);

alter table  tbl_pay_cat_sub_categories add column if not exists copy_id char(36) after id;
update tbl_pay_cat_sub_categories set copy_id = uuid();
alter table tbl_pay_cat_sub_categories drop foreign key if exists tbl_pay_cat_sub_categories_facility_id_foreign;
alter table tbl_pay_cat_sub_categories change column  facility_id facility_id char(36);
update tbl_pay_cat_sub_categories set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_item_prices add column if not exists copy_id char(36) after id;
update tbl_item_prices set copy_id = uuid();
alter table tbl_item_prices drop foreign key if exists tbl_item_prices_facility_id_foreign;
alter table tbl_item_prices change column  facility_id facility_id char(36);
update tbl_item_prices set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_item_prices drop foreign key if exists tbl_item_prices_sub_category_id_foreign;
alter table tbl_item_prices change column  sub_category_id sub_category_id char(36);
update tbl_item_prices set sub_category_id = (select copy_id from tbl_pay_cat_sub_categories where tbl_pay_cat_sub_categories.id =  sub_category_id limit 1);

alter table tbl_discount_reasons add column if not exists copy_id char(36) after id;
update tbl_discount_reasons set copy_id = uuid();
alter table tbl_discount_reasons drop foreign key if exists tbl_discount_reasons_patient_id_foreign;
alter table tbl_discount_reasons change column  patient_id patient_id char(36);
update tbl_discount_reasons set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_discount_reasons drop foreign key if exists tbl_discount_reasons_facility_id_foreign;
alter table tbl_discount_reasons change column  facility_id facility_id char(36);
update tbl_discount_reasons set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_discount_reasons drop foreign key if exists tbl_discount_reasons_receipt_number_foreign;
alter table tbl_discount_reasons change column  receipt_number receipt_number char(36); -- data fetched after setting invoice_lines

alter table tbl_invoice_lines add column if not exists copy_id char(36) after id;
update tbl_invoice_lines set copy_id = uuid();
alter table tbl_invoice_lines drop foreign key if exists tbl_invoice_lines_user_id_foreign;
alter table tbl_invoice_lines change column  user_id user_id char(36);
update tbl_invoice_lines set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_invoice_lines drop foreign key if exists tbl_invoice_lines_invoice_id_foreign;
alter table tbl_invoice_lines change column  invoice_id invoice_id char(36);
update tbl_invoice_lines set invoice_id = (select copy_id from tbl_encounter_invoices where tbl_encounter_invoices.id =  invoice_id limit 1);

alter table tbl_invoice_lines drop foreign key if exists tbl_invoice_lines_corpse_id_foreign;
alter table tbl_invoice_lines change column  corpse_id corpse_id char(36);
update tbl_invoice_lines set corpse_id = (select copy_id from tbl_corpses where tbl_corpses.id =  corpse_id limit 1);
alter table tbl_invoice_lines drop foreign key if exists tbl_invoice_lines_item_price_id_foreign;
alter table tbl_invoice_lines change column  item_price_id item_price_id char(36);
update tbl_invoice_lines set item_price_id = (select copy_id from tbl_item_prices where tbl_item_prices.id =  item_price_id limit 1);
alter table tbl_invoice_lines drop foreign key if exists tbl_invoice_lines_patient_id_foreign;
alter table tbl_invoice_lines change column  patient_id patient_id char(36);
update tbl_invoice_lines set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_invoice_lines drop foreign key if exists tbl_invoice_lines_discount_by_foreign;
alter table tbl_invoice_lines change column  discount_by discount_by char(36);
update tbl_invoice_lines set discount_by = (select copy_id from users where users.id =  discount_by limit 1);
alter table tbl_invoice_lines drop foreign key if exists tbl_invoice_lines_facility_id_foreign;
alter table tbl_invoice_lines change column  facility_id facility_id char(36);
update tbl_invoice_lines set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_invoice_lines drop foreign key if exists tbl_invoice_lines_payment_filter_foreign;
alter table tbl_invoice_lines change column  payment_filter payment_filter char(36);
update tbl_invoice_lines set payment_filter = (select copy_id from tbl_pay_cat_sub_categories where tbl_pay_cat_sub_categories.id =  facility_id limit 1);
-- takes care of the trans-key
update tbl_discount_reasons set receipt_number = (select copy_id from tbl_invoice_lines where tbl_invoice_lines.id =  facility_id limit 1);

alter table tbl_admission_status_tracks add column if not exists copy_id char(36) after id;
update tbl_admission_status_tracks set copy_id = uuid();
alter table tbl_admission_status_tracks drop foreign key if exists tbl_admission_status_tracks_patient_id_foreign;
alter table tbl_admission_status_tracks change column  patient_id patient_id char(36);
update tbl_admission_status_tracks set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_admission_status_tracks drop foreign key if exists tbl_admission_status_tracks_user_id_foreign;
alter table tbl_admission_status_tracks change column  user_id user_id char(36);
update tbl_admission_status_tracks set user_id = (select copy_id from users where users.id =  user_id limit 1);

alter table tbl_admissions add column if not exists copy_id char(36) after id;
update tbl_admissions set copy_id = uuid();
alter table tbl_admissions drop foreign key if exists tbl_admissions_patient_id_foreign;
alter table tbl_admissions change column  patient_id patient_id char(36);
update tbl_admissions set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_admissions drop foreign key if exists tbl_admissions_account_id_foreign;
alter table tbl_admissions change column  account_id account_id char(36);
update tbl_admissions set account_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  account_id limit 1);
alter table tbl_admissions drop foreign key if exists tbl_admissions_facility_id_foreign;
alter table tbl_admissions change column  facility_id facility_id char(36);
update tbl_admissions set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_admissions drop foreign key if exists tbl_admissions_user_id_foreign;
alter table tbl_admissions change column  user_id user_id char(36);
update tbl_admissions set user_id = (select copy_id from users where users.id =  user_id limit 1);

alter table tbl_ipdtreatments add column if not exists copy_id char(36) after id;
update tbl_ipdtreatments set copy_id = uuid();
alter table tbl_ipdtreatments drop foreign key if exists tbl_ipdtreatments_patient_id_foreign;
alter table tbl_ipdtreatments change column  patient_id patient_id char(36);
update tbl_ipdtreatments set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_ipdtreatments drop foreign key if exists tbl_ipdtreatments_user_id_foreign;
alter table tbl_ipdtreatments change column  user_id user_id char(36);
update tbl_ipdtreatments set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_ipdtreatments drop foreign key if exists tbl_ipdtreatments_facility_id_foreign;
alter table tbl_ipdtreatments change column  facility_id facility_id char(36);
update tbl_ipdtreatments set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_ipdtreatments drop foreign key if exists tbl_ipdtreatments_admission_id_foreign;
alter table tbl_ipdtreatments change column  admission_id admission_id char(36);
update tbl_ipdtreatments set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_ipdtreatments change column  remarks remarks text;

alter table tbl_client_violences add column if not exists copy_id char(36) after id;
update tbl_client_violences set copy_id = uuid();
alter table tbl_client_violences drop foreign key if exists tbl_client_violences_user_id_foreign;
alter table tbl_client_violences change column  user_id user_id char(36);
update tbl_client_violences set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_client_violences drop foreign key if exists tbl_client_violences_facility_id_foreign;
alter table tbl_client_violences change column  facility_id facility_id char(36);
update tbl_client_violences set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_client_violences drop foreign key if exists tbl_client_violences_patient_id_foreign;
alter table tbl_client_violences change column  patient_id patient_id char(36);
update tbl_client_violences set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);

alter table tbl_diagnoses add column if not exists copy_id char(36) after id;
update tbl_diagnoses set copy_id = uuid();
alter table tbl_diagnoses drop foreign key if exists tbl_diagnoses_patient_id_foreign;
alter table tbl_diagnoses change column  patient_id patient_id char(36);
update tbl_diagnoses set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_diagnoses drop foreign key if exists tbl_diagnoses_visit_date_id_foreign;
alter table tbl_diagnoses change column  visit_date_id visit_date_id char(36);
update tbl_diagnoses set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_diagnoses drop foreign key if exists tbl_diagnoses_user_id_foreign;
alter table tbl_diagnoses change column  user_id user_id char(36);
update tbl_diagnoses set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_diagnoses drop foreign key if exists tbl_diagnoses_facility_id_foreign;
alter table tbl_diagnoses change column  facility_id facility_id char(36);
update tbl_diagnoses set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_diagnoses drop foreign key if exists tbl_diagnoses_admission_id_foreign;
alter table tbl_diagnoses change column  admission_id admission_id char(36);
update tbl_diagnoses set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_investigations add column if not exists copy_id char(36) after id;
update tbl_investigations set copy_id = uuid();
alter table tbl_investigations drop foreign key if exists tbl_investigations_patient_id_foreign;
alter table tbl_investigations change column  patient_id patient_id char(36);
update tbl_investigations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_investigations drop foreign key if exists tbl_investigations_user_id_foreign;
alter table tbl_investigations change column  user_id user_id char(36);
update tbl_investigations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_investigations drop foreign key if exists tbl_investigations_visit_date_id_foreign;
alter table tbl_investigations change column  visit_date_id visit_date_id char(36);
update tbl_investigations set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_investigations drop foreign key if exists tbl_investigations_facility_id_foreign;
alter table tbl_investigations change column  facility_id facility_id char(36);
update tbl_investigations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_investigations drop foreign key if exists tbl_investigations_admission_id_foreign;
alter table tbl_investigations change column  admission_id admission_id char(36);
update tbl_investigations set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_obs_gyns add column if not exists copy_id char(36) after id;
update tbl_obs_gyns set copy_id = uuid();
alter table tbl_obs_gyns drop foreign key if exists tbl_obs_gyns_patient_id_foreign;
alter table tbl_obs_gyns change column  patient_id patient_id char(36);
update tbl_obs_gyns set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_obs_gyns drop foreign key if exists tbl_obs_gyns_user_id_foreign;
alter table tbl_obs_gyns change column  user_id user_id char(36);
update tbl_obs_gyns set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_obs_gyns drop foreign key if exists tbl_obs_gyns_visit_date_id_foreign;
alter table tbl_obs_gyns change column  visit_date_id visit_date_id char(36);
update tbl_obs_gyns set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_obs_gyns drop foreign key if exists tbl_obs_gyns_facility_id_foreign;
alter table tbl_obs_gyns change column  facility_id facility_id char(36);
update tbl_obs_gyns set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_obs_gyns drop foreign key if exists tbl_obs_gyns_admission_id_foreign;
alter table tbl_obs_gyns change column  admission_id admission_id char(36);
update tbl_obs_gyns set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_patient_procedures add column if not exists copy_id char(36) after id;
update tbl_patient_procedures set copy_id = uuid();
alter table tbl_patient_procedures drop foreign key if exists tbl_patient_procedures_patient_id_foreign;
alter table tbl_patient_procedures change column  patient_id patient_id char(36);
update tbl_patient_procedures set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_patient_procedures drop foreign key if exists tbl_patient_procedures_user_id_foreign;
alter table tbl_patient_procedures change column  user_id user_id char(36);
update tbl_patient_procedures set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_patient_procedures drop foreign key if exists tbl_patient_procedures_visit_date_id_foreign;
alter table tbl_patient_procedures change column  visit_date_id visit_date_id char(36);
update tbl_patient_procedures set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_patient_procedures drop foreign key if exists tbl_patient_procedures_admission_id_foreign;
alter table tbl_patient_procedures change column  admission_id admission_id char(36);
update tbl_patient_procedures set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_survey_histories add column if not exists copy_id char(36) after id;
update tbl_survey_histories set copy_id = uuid();
alter table tbl_survey_histories drop foreign key if exists tbl_survey_histories_patient_id_foreign;
alter table tbl_survey_histories change column  patient_id patient_id char(36);
update tbl_survey_histories set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_survey_histories drop foreign key if exists tbl_survey_histories_user_id_foreign;
alter table tbl_survey_histories change column  user_id user_id char(36);
update tbl_survey_histories set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_survey_histories drop foreign key if exists tbl_survey_histories_visit_date_id_foreign;
alter table tbl_survey_histories change column  visit_date_id visit_date_id char(36);
update tbl_survey_histories set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_survey_histories drop foreign key if exists tbl_survey_histories_facility_id_foreign;
alter table tbl_survey_histories change column  facility_id facility_id char(36);
update tbl_survey_histories set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_survey_histories drop foreign key if exists tbl_survey_histories_admission_id_foreign;
alter table tbl_survey_histories change column  admission_id admission_id char(36);
update tbl_survey_histories set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_physical_examinations add column if not exists copy_id char(36) after id;
update tbl_physical_examinations set copy_id = uuid();
alter table tbl_physical_examinations drop foreign key if exists tbl_physical_examinations_patient_id_foreign;
alter table tbl_physical_examinations change column  patient_id patient_id char(36);
update tbl_physical_examinations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_physical_examinations drop foreign key if exists tbl_physical_examinations_user_id_foreign;
alter table tbl_physical_examinations change column  user_id user_id char(36);
update tbl_physical_examinations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_physical_examinations drop foreign key if exists tbl_physical_examinations_visit_date_id_foreign;
alter table tbl_physical_examinations change column  visit_date_id visit_date_id char(36);
update tbl_physical_examinations set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_physical_examinations drop foreign key if exists tbl_physical_examinations_facility_id_foreign;
alter table tbl_physical_examinations change column  facility_id facility_id char(36);
update tbl_physical_examinations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_physical_examinations drop foreign key if exists tbl_physical_examinations_admission_id_foreign;
alter table tbl_physical_examinations change column  admission_id admission_id char(36);
update tbl_physical_examinations set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_vct_registers add column if not exists copy_id char(36) after id;
update tbl_vct_registers set copy_id = uuid();
alter table tbl_vct_registers drop foreign key if exists tbl_vct_registers_client_id_foreign;
alter table tbl_vct_registers change column  client_id client_id char(36);
update tbl_vct_registers set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_vct_registers drop foreign key if exists tbl_vct_registers_user_id_foreign;
alter table tbl_vct_registers change column  user_id user_id char(36);
update tbl_vct_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_vct_registers drop foreign key if exists tbl_vct_registers_facility_id_foreign;
alter table tbl_vct_registers change column  facility_id facility_id char(36);
update tbl_vct_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_vct_registers change column  comment comment text;

alter table tbl_bills_categories add column if not exists copy_id char(36) after id;
update tbl_bills_categories set copy_id = uuid();
alter table tbl_bills_categories drop foreign key if exists tbl_bills_categories_patient_id_foreign;
alter table tbl_bills_categories change column  patient_id patient_id char(36);
update tbl_bills_categories set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_bills_categories drop foreign key if exists tbl_bills_categories_account_id_foreign;
alter table tbl_bills_categories change column  account_id account_id char(36);
update tbl_bills_categories set account_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  account_id limit 1);
alter table tbl_bills_categories drop foreign key if exists tbl_bills_categories_user_id_foreign;
alter table tbl_bills_categories change column  user_id user_id char(36);
update tbl_bills_categories set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_bills_categories drop foreign key if exists tbl_bills_categories_bill_id_foreign;
alter table tbl_bills_categories drop foreign key if exists tbl_bills_categories_bill_id_foreign;
alter table tbl_bills_categories change column  bill_id bill_id char(36);
update tbl_bills_categories set bill_id = (select copy_id from tbl_pay_cat_sub_categories where tbl_pay_cat_sub_categories.id =  bill_id limit 1);

alter table tbl_child_referrals add column if not exists copy_id char(36) after id;
update tbl_child_referrals set copy_id = uuid();
alter table tbl_child_referrals drop foreign key if exists tbl_child_referrals_patient_id_foreign;
alter table tbl_child_referrals change column  patient_id patient_id char(36);
update tbl_child_referrals set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_child_referrals drop foreign key if exists tbl_child_referrals_user_id_foreign;
alter table tbl_child_referrals change column  user_id user_id char(36);
update tbl_child_referrals set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_child_referrals drop foreign key if exists tbl_child_referrals_facility_id_foreign;
alter table tbl_child_referrals change column  facility_id facility_id char(36);
update tbl_child_referrals set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_child_referrals drop foreign key if exists tbl_child_referrals_mother_id_foreign;
alter table tbl_child_referrals change column  mother_id mother_id char(36);
update tbl_child_referrals set mother_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_child_referrals drop foreign key if exists tbl_child_referrals_transfered_institution_id_foreign;
alter table tbl_child_referrals change column  transfered_institution_id transfered_institution_id char(36);
update tbl_child_referrals set transfered_institution_id = (select copy_id from tbl_facilities where tbl_facilities.id =  transfered_institution_id limit 1);
alter table tbl_child_referrals change column  reason reason text;

alter table tbl_past_medical_histories add column if not exists copy_id char(36) after id;
update tbl_past_medical_histories set copy_id = uuid();
alter table tbl_past_medical_histories drop foreign key if exists tbl_past_medical_histories_patient_id_foreign;
alter table tbl_past_medical_histories change column  patient_id patient_id char(36);
update tbl_past_medical_histories set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_past_medical_histories drop foreign key if exists tbl_past_medical_histories_user_id_foreign;
alter table tbl_past_medical_histories change column  user_id user_id char(36);
update tbl_past_medical_histories set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_past_medical_histories drop foreign key if exists tbl_past_medical_histories_visit_date_id_foreign;
alter table tbl_past_medical_histories change column  visit_date_id visit_date_id char(36);
update tbl_past_medical_histories set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_past_medical_histories drop foreign key if exists tbl_past_medical_histories_facility_id_foreign;
alter table tbl_past_medical_histories change column  facility_id facility_id char(36);
update tbl_past_medical_histories set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_past_medical_histories drop foreign key if exists tbl_past_medical_histories_admission_id_foreign;
alter table tbl_past_medical_histories change column  admission_id admission_id char(36);
update tbl_past_medical_histories set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_child_vaccination_registers add column if not exists copy_id char(36) after id;
update tbl_child_vaccination_registers set copy_id = uuid();
alter table tbl_child_vaccination_registers drop foreign key if exists tbl_child_vaccination_registers_patient_id_foreign;
alter table tbl_child_vaccination_registers change column  patient_id patient_id char(36);
update tbl_child_vaccination_registers set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_child_vaccination_registers drop foreign key if exists tbl_child_vaccination_registers_user_id_foreign;
alter table tbl_child_vaccination_registers change column  user_id user_id char(36);
update tbl_child_vaccination_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_child_vaccination_registers drop foreign key if exists tbl_child_vaccination_registers_facility_id_foreign;
alter table tbl_child_vaccination_registers change column  facility_id facility_id char(36);
update tbl_child_vaccination_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_child_vaccination_registers drop foreign key if exists tbl_child_vaccination_registers_mother_id_foreign;
alter table tbl_child_vaccination_registers change column  mother_id mother_id char(36);
update tbl_child_vaccination_registers set mother_id = (select copy_id from tbl_patients where tbl_patients.id =  mother_id limit 1);

alter table tbl_anti_natal_registers add column if not exists copy_id char(36) after id;
update tbl_anti_natal_registers set copy_id = uuid();
alter table tbl_anti_natal_registers drop foreign key if exists tbl_anti_natal_registers_client_id_foreign;
alter table tbl_anti_natal_registers change column  client_id client_id char(36);
update tbl_anti_natal_registers set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_anti_natal_registers drop foreign key if exists tbl_anti_natal_registers_user_id_foreign;
alter table tbl_anti_natal_registers change column  user_id user_id char(36);
update tbl_anti_natal_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_registers drop foreign key if exists tbl_anti_natal_registers_facility_id_foreign;
alter table tbl_anti_natal_registers change column  facility_id facility_id char(36);
update tbl_anti_natal_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_review_systems add column if not exists copy_id char(36) after id;
update tbl_review_systems set copy_id = uuid();
alter table tbl_review_systems drop foreign key if exists tbl_review_systems_patient_id_foreign;
alter table tbl_review_systems change column  patient_id patient_id char(36);
update tbl_review_systems set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_review_systems drop foreign key if exists tbl_review_systems_user_id_foreign;
alter table tbl_review_systems change column  user_id user_id char(36);
update tbl_review_systems set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_review_systems drop foreign key if exists tbl_review_systems_visit_date_id_foreign;
alter table tbl_review_systems change column  visit_date_id visit_date_id char(36);
update tbl_review_systems set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_review_systems drop foreign key if exists tbl_review_systems_facility_id_foreign;
alter table tbl_review_systems change column  facility_id facility_id char(36);
update tbl_review_systems set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_review_systems drop foreign key if exists tbl_review_systems_admission_id_foreign;
alter table tbl_review_systems change column  admission_id admission_id char(36);
update tbl_review_systems set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_birth_histories add column if not exists copy_id char(36) after id;
update tbl_birth_histories set copy_id = uuid();
alter table tbl_birth_histories drop foreign key if exists tbl_birth_histories_patient_id_foreign;
alter table tbl_birth_histories change column  patient_id patient_id char(36);
update tbl_birth_histories set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_birth_histories drop foreign key if exists tbl_birth_histories_user_id_foreign;
alter table tbl_birth_histories change column  user_id user_id char(36);
update tbl_birth_histories set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_birth_histories drop foreign key if exists tbl_birth_histories_visit_date_id_foreign;
alter table tbl_birth_histories change column  visit_date_id visit_date_id char(36);
update tbl_birth_histories set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_birth_histories drop foreign key if exists tbl_birth_histories_facility_id_foreign;
alter table tbl_birth_histories change column  facility_id facility_id char(36);
update tbl_birth_histories set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_birth_histories drop foreign key if exists tbl_birth_histories_admission_id_foreign;
alter table tbl_birth_histories change column  admission_id admission_id char(36);
update tbl_birth_histories set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_history_examinations add column if not exists copy_id char(36) after id;
update tbl_history_examinations set copy_id = uuid();
alter table tbl_history_examinations drop foreign key if exists tbl_history_examinations_patient_id_foreign;
alter table tbl_history_examinations change column  patient_id patient_id char(36);
update tbl_history_examinations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_history_examinations drop foreign key if exists tbl_history_examinations_user_id_foreign;
alter table tbl_history_examinations change column  user_id user_id char(36);
update tbl_history_examinations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_history_examinations drop foreign key if exists tbl_history_examinations_visit_date_id_foreign;
alter table tbl_history_examinations change column  visit_date_id visit_date_id char(36);
update tbl_history_examinations set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_history_examinations drop foreign key if exists tbl_history_examinations_facility_id_foreign;
alter table tbl_history_examinations change column  facility_id facility_id char(36);
update tbl_history_examinations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_history_examinations drop foreign key if exists tbl_history_examinations_admission_id_foreign;
alter table tbl_history_examinations change column  admission_id admission_id char(36);
update tbl_history_examinations set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_eyeclinic_visits add column if not exists copy_id char(36) after id;
update tbl_eyeclinic_visits set copy_id = uuid();
alter table tbl_eyeclinic_visits drop foreign key if exists tbl_eyeclinic_visits_patient_id_foreign;
alter table tbl_eyeclinic_visits change column  patient_id patient_id char(36);
update tbl_eyeclinic_visits set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_eyeclinic_visits drop foreign key if exists tbl_eyeclinic_visits_user_id_foreign;
alter table tbl_eyeclinic_visits change column  user_id user_id char(36);
update tbl_eyeclinic_visits set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_eyeclinic_visits drop foreign key if exists tbl_eyeclinic_visits_visit_date_id_foreign;
alter table tbl_eyeclinic_visits change column  visit_date_id visit_date_id char(36);
update tbl_eyeclinic_visits set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_eyeclinic_visits drop foreign key if exists tbl_eyeclinic_visits_facility_id_foreign;
alter table tbl_eyeclinic_visits change column  facility_id facility_id char(36);
update tbl_eyeclinic_visits set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_eyeclinic_visits drop foreign key if exists tbl_eyeclinic_visits_admission_id_foreign;
alter table tbl_eyeclinic_visits change column  admission_id admission_id char(36);
update tbl_eyeclinic_visits set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_comma_scales add column if not exists copy_id char(36) after id;
update tbl_comma_scales set copy_id = uuid();
alter table tbl_comma_scales drop foreign key if exists tbl_comma_scales_patient_id_foreign;
alter table tbl_comma_scales change column  patient_id patient_id char(36);
update tbl_comma_scales set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_comma_scales drop foreign key if exists tbl_comma_scales_user_id_foreign;
alter table tbl_comma_scales change column  user_id user_id char(36);
update tbl_comma_scales set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_comma_scales drop foreign key if exists tbl_comma_scales_visit_date_id_foreign;
alter table tbl_comma_scales change column  visit_date_id visit_date_id char(36);
update tbl_comma_scales set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_comma_scales drop foreign key if exists tbl_comma_scales_facility_id_foreign;
alter table tbl_comma_scales change column  facility_id facility_id char(36);
update tbl_comma_scales set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_comma_scales drop foreign key if exists tbl_comma_scales_admission_id_foreign;
alter table tbl_comma_scales change column  admission_id admission_id char(36);
update tbl_comma_scales set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_requests add column if not exists copy_id char(36) after id;
update tbl_requests set copy_id = uuid();
alter table tbl_requests drop foreign key if exists tbl_requests_patient_id_foreign;
alter table tbl_requests change column  patient_id patient_id char(36);
update tbl_requests set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_requests drop foreign key if exists tbl_requests_doctor_id_foreign;
alter table tbl_requests change column  doctor_id doctor_id char(36);
update tbl_requests set doctor_id = (select copy_id from users where users.id =  doctor_id limit 1);
alter table tbl_requests drop foreign key if exists tbl_requests_visit_date_id_foreign;
alter table tbl_requests change column  visit_date_id visit_date_id char(36);
update tbl_requests set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_requests drop foreign key if exists tbl_requests_admission_id_foreign;
alter table tbl_requests change column  admission_id admission_id char(36);
update tbl_requests set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_clinic_instructions add column if not exists copy_id char(36) after id;
update tbl_clinic_instructions set copy_id = uuid();
alter table tbl_clinic_instructions drop foreign key if exists tbl_clinic_instructions_visit_id_foreign;
alter table tbl_clinic_instructions change column  visit_id visit_id char(36);
update tbl_clinic_instructions set visit_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_id limit 1);
alter table tbl_clinic_instructions drop foreign key if exists tbl_clinic_instructions_specialist_id_foreign;
alter table tbl_clinic_instructions change column  specialist_id specialist_id char(36);
update tbl_clinic_instructions set specialist_id = (select copy_id from users where users.id =  specialist_id limit 1);
alter table tbl_clinic_instructions drop foreign key if exists tbl_clinic_instructions_doctor_requesting_id_foreign;
alter table tbl_clinic_instructions change column  doctor_requesting_id doctor_requesting_id char(36);
update tbl_clinic_instructions set doctor_requesting_id = (select copy_id from users where users.id =  doctor_requesting_id limit 1);
alter table tbl_clinic_instructions change column  summary summary text;

alter table tbl_results add column if not exists copy_id char(36) after id;
update tbl_results set copy_id = uuid();
alter table tbl_results drop foreign key if exists tbl_results_order_id_foreign;
alter table tbl_results change column  order_id order_id char(36);
update tbl_results set order_id = (select copy_id from tbl_requests where tbl_requests.id =  order_id limit 1);
alter table tbl_results drop foreign key if exists tbl_results_post_user_foreign;
alter table tbl_results change column  post_user post_user char(36);
update tbl_results set post_user = (select copy_id from users where users.id =  post_user limit 1);
alter table tbl_results drop foreign key if exists tbl_results_verify_user_foreign;
alter table tbl_results change column  verify_user verify_user char(36);
update tbl_results set verify_user = (select copy_id from users where users.id =  verify_user limit 1);
alter table tbl_results change column  description description text;

alter table tbl_family_planning_registers add column if not exists copy_id char(36) after id;
update tbl_family_planning_registers set copy_id = uuid();
alter table tbl_family_planning_registers drop foreign key if exists tbl_family_planning_registers_client_id_foreign;
alter table tbl_family_planning_registers change column  client_id client_id char(36);
update tbl_family_planning_registers set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_family_planning_registers drop foreign key if exists tbl_family_planning_registers_user_id_foreign;
alter table tbl_family_planning_registers change column  user_id user_id char(36);
update tbl_family_planning_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_family_planning_registers drop foreign key if exists tbl_family_planning_registers_facility_id_foreign;
alter table tbl_family_planning_registers change column  facility_id facility_id char(36);
update tbl_family_planning_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_unavailable_tests add column if not exists copy_id char(36) after id;
update tbl_unavailable_tests set copy_id = uuid();
alter table tbl_unavailable_tests drop foreign key if exists tbl_unavailable_tests_visit_date_id_foreign;
alter table tbl_unavailable_tests change column  visit_date_id visit_date_id char(36);
update tbl_unavailable_tests set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_unavailable_tests drop foreign key if exists tbl_unavailable_tests_patient_id_foreign;
alter table tbl_unavailable_tests change column  patient_id patient_id char(36);
update tbl_unavailable_tests set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_unavailable_tests drop foreign key if exists tbl_unavailable_tests_user_id_foreign;
alter table tbl_unavailable_tests change column  user_id user_id char(36);
update tbl_unavailable_tests set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_unavailable_tests drop foreign key if exists tbl_unavailable_tests_facility_id_foreign;
alter table tbl_unavailable_tests change column  facility_id facility_id char(36);
update tbl_unavailable_tests set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_blood_requests add column if not exists copy_id char(36) after id;
update tbl_blood_requests set copy_id = uuid();
alter table tbl_blood_requests drop foreign key if exists tbl_blood_requests_facility_id_foreign;
alter table tbl_blood_requests change column  facility_id facility_id char(36);
update tbl_blood_requests set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_blood_requests drop foreign key if exists tbl_blood_requests_requested_by_foreign;
alter table tbl_blood_requests change column  requested_by requested_by char(36);
update tbl_blood_requests set requested_by = (select copy_id from users where users.id =  requested_by limit 1);
alter table tbl_blood_requests drop foreign key if exists tbl_blood_requests_processed_by_foreign;
alter table tbl_blood_requests change column  processed_by processed_by char(36);
update tbl_blood_requests set processed_by = (select copy_id from users where users.id =  processed_by limit 1);
alter table tbl_blood_requests drop foreign key if exists tbl_blood_requests_visit_id_foreign;
alter table tbl_blood_requests change column  visit_id visit_id char(36);
update tbl_blood_requests set visit_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_id limit 1);
alter table tbl_blood_requests drop foreign key if exists tbl_blood_requests_patient_id_foreign;
alter table tbl_blood_requests change column  patient_id patient_id char(36);
update tbl_blood_requests set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);

alter table tbl_referrals add column if not exists copy_id char(36) after id;
update tbl_referrals set copy_id = uuid();
alter table tbl_referrals drop foreign key if exists tbl_referrals_visit_id_foreign;
alter table tbl_referrals change column  visit_id visit_id char(36);
update tbl_referrals set visit_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_id limit 1);
alter table tbl_referrals drop foreign key if exists tbl_referrals_patient_id_foreign;
alter table tbl_referrals change column  patient_id patient_id char(36);
update tbl_referrals set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_referrals drop foreign key if exists tbl_referrals_sender_id_foreign;
alter table tbl_referrals change column  sender_id sender_id char(36);
update tbl_referrals set sender_id = (select copy_id from users where users.id =  sender_id limit 1);
alter table tbl_referrals drop foreign key if exists tbl_referrals_from_facility_id_foreign;
alter table tbl_referrals change column  from_facility_id from_facility_id char(36);
update tbl_referrals set from_facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  from_facility_id limit 1);
alter table tbl_referrals drop foreign key if exists tbl_referrals_to_facility_id_foreign;
alter table tbl_referrals change column  to_facility_id to_facility_id char(36);
update tbl_referrals set to_facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  to_facility_id limit 1);

alter table tbl_past_medical_historys add column if not exists copy_id char(36) after id;
update tbl_past_medical_historys set copy_id = uuid();
alter table tbl_past_medical_historys drop foreign key if exists tbl_past_medical_historys_patient_id_foreign;
alter table tbl_past_medical_historys change column  patient_id patient_id char(36);
update tbl_past_medical_historys set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_past_medical_historys drop foreign key if exists tbl_past_medical_historys_user_id_foreign;
alter table tbl_past_medical_historys change column  user_id user_id char(36);
update tbl_past_medical_historys set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_past_medical_historys drop foreign key if exists tbl_past_medical_historys_visit_date_id_foreign;
alter table tbl_past_medical_historys change column  visit_date_id visit_date_id char(36);
update tbl_past_medical_historys set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_past_medical_historys drop foreign key if exists tbl_past_medical_historys_facility_id_foreign;
alter table tbl_past_medical_historys change column  facility_id facility_id char(36);
update tbl_past_medical_historys set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_family_histories add column if not exists copy_id char(36) after id;
update tbl_family_histories set copy_id = uuid();
alter table tbl_family_histories drop foreign key if exists tbl_family_histories_patient_id_foreign;
alter table tbl_family_histories change column  patient_id patient_id char(36);
update tbl_family_histories set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_family_histories drop foreign key if exists tbl_family_histories_visit_date_id_foreign;
alter table tbl_family_histories change column  visit_date_id visit_date_id char(36);
update tbl_family_histories set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_family_histories drop foreign key if exists tbl_family_histories_user_id_foreign;
alter table tbl_family_histories change column  user_id user_id char(36);
update tbl_family_histories set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_family_histories drop foreign key if exists tbl_family_histories_facility_id_foreign;
alter table tbl_family_histories change column  facility_id facility_id char(36);
update tbl_family_histories set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_family_histories drop foreign key if exists tbl_family_histories_admission_id_foreign;
alter table tbl_family_histories change column  admission_id admission_id char(36);
update tbl_family_histories set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table  tbl_diagnosis_details add column if not exists copy_id char(36) after id;
update tbl_diagnosis_details set copy_id = uuid();
alter table tbl_diagnosis_details drop foreign key if exists tbl_diagnosis_details_diagnosis_id_foreign;
alter table tbl_diagnosis_details change column  diagnosis_id diagnosis_id char(36);
update tbl_diagnosis_details set diagnosis_id = (select copy_id from tbl_diagnoses where tbl_diagnoses.id =  diagnosis_id limit 1);

alter table  tbl_observation_charts add column if not exists copy_id char(36) after id;
update tbl_observation_charts set copy_id = uuid();
alter table tbl_observation_charts drop foreign key if exists tbl_observation_charts_admission_id_foreign;
alter table tbl_observation_charts change column  admission_id admission_id char(36);
update tbl_observation_charts set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table  tbl_status_procedures add column if not exists copy_id char(36) after id;
update tbl_status_procedures set copy_id = uuid();
alter table tbl_status_procedures drop foreign key if exists tbl_status_procedures_admission_id_foreign;
alter table tbl_status_procedures change column  admission_id admission_id char(36);
update tbl_status_procedures set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_status_procedures drop foreign key if exists tbl_status_procedures_patient_id_foreign;
alter table tbl_status_procedures change column  patient_id patient_id char(36);
update tbl_status_procedures set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_status_procedures drop foreign key if exists tbl_status_procedures_user_id_foreign;
alter table tbl_status_procedures change column  user_id user_id char(36);
update tbl_status_procedures set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_status_procedures drop foreign key if exists tbl_status_procedures_facility_id_foreign;
alter table tbl_status_procedures change column  facility_id facility_id char(36);
update tbl_status_procedures set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table  tbl_user_roles add column if not exists copy_id char(36) after id;
update tbl_user_roles set copy_id = uuid();
alter table tbl_user_roles drop foreign key if exists tbl_user_roles_user_id_foreign;
alter table tbl_user_roles change column  user_id user_id char(36);
update tbl_user_roles set user_id = (select copy_id from users where users.id =  user_id limit 1);

alter table  tbl_donor_investigations add column if not exists copy_id char(36) after id;
update tbl_donor_investigations set copy_id = uuid();
alter table tbl_donor_investigations drop foreign key if exists tbl_donor_investigations_facility_id_foreign;
alter table tbl_donor_investigations change column  facility_id facility_id char(36);
update tbl_donor_investigations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_donor_investigations drop foreign key if exists tbl_donor_investigations_patient_id_foreign;
alter table tbl_donor_investigations change column  patient_id patient_id char(36);
update tbl_donor_investigations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_donor_investigations drop foreign key if exists tbl_donor_investigations_user_id_foreign;
alter table tbl_donor_investigations change column  user_id user_id char(36);
update tbl_donor_investigations set user_id = (select copy_id from users where users.id =  user_id limit 1);

alter table  tbl_death_conditions add column if not exists copy_id char(36) after id;
update tbl_death_conditions set copy_id = uuid();
alter table tbl_death_conditions drop foreign key if exists tbl_death_conditions_facility_id_foreign;
alter table tbl_death_conditions change column  facility_id facility_id char(36);
update tbl_death_conditions set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_death_conditions drop foreign key if exists tbl_death_conditions_user_id_foreign;
alter table tbl_death_conditions change column  user_id user_id char(36);
update tbl_death_conditions set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_death_conditions drop foreign key if exists tbl_death_conditions_visit_date_id_foreign;
alter table tbl_death_conditions change column  visit_date_id visit_date_id char(36);
update tbl_death_conditions set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_death_conditions drop foreign key if exists tbl_death_conditions_admission_id_foreign;
alter table tbl_death_conditions change column  admission_id admission_id char(36);
update tbl_death_conditions set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table  tbl_outputs add column if not exists copy_id char(36) after id;
update tbl_outputs set copy_id = uuid();
alter table tbl_outputs drop foreign key if exists tbl_outputs_facility_id_foreign;
alter table tbl_outputs change column  facility_id facility_id char(36);
update tbl_outputs set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_outputs drop foreign key if exists tbl_outputs_user_id_foreign;
alter table tbl_outputs change column  user_id user_id char(36);
update tbl_outputs set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_outputs drop foreign key if exists tbl_outputs_visit_date_id_foreign;
alter table tbl_outputs change column  visit_date_id visit_date_id char(36);
update tbl_outputs set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_outputs drop foreign key if exists tbl_outputs_admission_id_foreign;
alter table tbl_outputs change column  admission_id admission_id char(36);
update tbl_outputs set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table  tbl_therapy_treatments add column if not exists copy_id char(36) after id;
update tbl_therapy_treatments set copy_id = uuid();
alter table tbl_therapy_treatments drop foreign key if exists tbl_therapy_treatments_facility_id_foreign;
alter table tbl_therapy_treatments change column  facility_id facility_id char(36);
update tbl_therapy_treatments set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_therapy_treatments drop foreign key if exists tbl_therapy_treatments_user_id_foreign;
alter table tbl_therapy_treatments change column  user_id user_id char(36);
update tbl_therapy_treatments set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_therapy_treatments drop foreign key if exists tbl_therapy_treatments_visit_date_id_foreign;
alter table tbl_therapy_treatments change column  visit_date_id visit_date_id char(36);
update tbl_therapy_treatments set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);

alter table  tbl_waste_dispositions add column if not exists copy_id char(36) after id;
update tbl_waste_dispositions set copy_id = uuid();
alter table tbl_waste_dispositions drop foreign key if exists tbl_waste_dispositions_facility_id_foreign;
alter table tbl_waste_dispositions change column  facility_id facility_id char(36);
update tbl_waste_dispositions set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_waste_dispositions drop foreign key if exists tbl_waste_dispositions_user_id_foreign;
alter table tbl_waste_dispositions change column  user_id user_id char(36);
update tbl_waste_dispositions set user_id = (select copy_id from users where users.id =  user_id limit 1);

alter table  tbl_serious_patients add column if not exists copy_id char(36) after id;
update tbl_serious_patients set copy_id = uuid();
alter table tbl_serious_patients drop foreign key if exists tbl_serious_patients_facility_id_foreign;
alter table tbl_serious_patients change column  facility_id facility_id char(36);
update tbl_serious_patients set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_serious_patients drop foreign key if exists tbl_serious_patients_user_id_foreign;
alter table tbl_serious_patients change column  user_id user_id char(36);
update tbl_serious_patients set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_serious_patients drop foreign key if exists tbl_serious_patients_visit_date_id_foreign;
alter table tbl_serious_patients change column  visit_date_id visit_date_id char(36);
update tbl_serious_patients set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_serious_patients drop foreign key if exists tbl_serious_patients_admission_id_foreign;
alter table tbl_serious_patients change column  admission_id admission_id char(36);
update tbl_serious_patients set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_environmental_equipment_registers add column if not exists copy_id char(36) after id;
update tbl_environmental_equipment_registers set copy_id = uuid();
alter table tbl_environmental_equipment_registers drop foreign key if exists tbl_environmental_equipment_registers_user_id_foreign;
alter table tbl_environmental_equipment_registers change column  user_id user_id char(36);
update tbl_environmental_equipment_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_environmental_equipment_registers drop foreign key if exists tbl_environmental_equipment_registers_facility_id_foreign;
alter table tbl_environmental_equipment_registers change column  facility_id facility_id char(36);
update tbl_environmental_equipment_registers set facility_id = (select copy_id from tbl_facilities where tbl_environmental_equipment_registers.id =  facility_id limit 1);

alter table  tbl_environmental_waste_collections add column if not exists copy_id char(36) after id;
update tbl_environmental_waste_collections set copy_id = uuid();
alter table tbl_environmental_waste_collections drop foreign key if exists tbl_environmental_waste_collections_facility_id_foreign;
alter table tbl_environmental_waste_collections change column  facility_id facility_id char(36);
update tbl_environmental_waste_collections set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_environmental_waste_collections drop foreign key if exists tbl_environmental_waste_collections_user_id_foreign;
alter table tbl_environmental_waste_collections change column  user_id user_id char(36);
update tbl_environmental_waste_collections set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_environmental_waste_collections drop foreign key if exists tbl_environmental_waste_collections_equipment_used_id_foreign;
alter table tbl_environmental_waste_collections change column  equipment_used_id equipment_used_id char(36);
update tbl_environmental_waste_collections set equipment_used_id = (select copy_id from tbl_environmental_equipment_registers where tbl_environmental_equipment_registers.id =  equipment_used_id limit 1);

alter table  tbl_post_natal_observation_descriptions add column if not exists copy_id char(36) after id;
update tbl_post_natal_observation_descriptions set copy_id = uuid();
alter table tbl_post_natal_observation_descriptions change column  observation observation text;

alter table tbl_post_natal_observation_checks add column if not exists copy_id char(36) after id;
update tbl_post_natal_observation_checks set copy_id = uuid();
alter table tbl_post_natal_observation_checks drop foreign key if exists tbl_post_natal_observation_checks_user_id_foreign;
alter table tbl_post_natal_observation_checks change column  user_id user_id char(36);
update tbl_post_natal_observation_checks set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_observation_checks drop foreign key if exists tbl_post_natal_observation_checks_facility_id_foreign;
alter table tbl_post_natal_observation_checks change column  facility_id facility_id char(36);
update tbl_post_natal_observation_checks set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_post_natal_observation_checks drop foreign key if exists tbl_post_natal_observation_checks_client_id_foreign;
alter table tbl_post_natal_observation_checks change column  client_id client_id char(36);
update tbl_post_natal_observation_checks set client_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  client_id limit 1);
alter table tbl_post_natal_observation_checks drop foreign key if exists tbl_post_natal_observation_checks_observation_id_foreign;
alter table tbl_post_natal_observation_checks change column  observation_id observation_id char(36);
update tbl_post_natal_observation_checks set observation_id = (select copy_id from tbl_post_natal_observation_descriptions where tbl_post_natal_observation_descriptions.id =  observation_id limit 1);

alter table tbl_labour_referrals add column if not exists copy_id char(36) after id;
update tbl_labour_referrals set copy_id = uuid();
alter table tbl_labour_referrals drop foreign key if exists tbl_labour_referrals_patient_id_foreign;
alter table tbl_labour_referrals change column  patient_id patient_id char(36);
update tbl_labour_referrals set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_labour_referrals drop foreign key if exists tbl_labour_referrals_user_id_foreign;
alter table tbl_labour_referrals change column  user_id user_id char(36);
update tbl_labour_referrals set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_referrals drop foreign key if exists tbl_labour_referrals_facility_id_foreign;
alter table tbl_labour_referrals change column  facility_id facility_id char(36);
update tbl_labour_referrals set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_labour_referrals drop foreign key if exists tbl_labour_referrals_transfered_institution_id_foreign;
alter table tbl_labour_referrals change column  transfered_institution_id transfered_institution_id char(36);
update tbl_labour_referrals set transfered_institution_id = (select copy_id from tbl_facilities where tbl_facilities.id =  transfered_institution_id limit 1);
alter table tbl_labour_referrals change column  reason reason text;

alter table tbl_medications add column if not exists copy_id char(36) after id;
update tbl_medications set copy_id = uuid();
alter table tbl_medications drop foreign key if exists tbl_medications_patient_id_foreign;
alter table tbl_medications change column  patient_id patient_id char(36);
update tbl_medications set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_medications drop foreign key if exists tbl_medications_user_id_foreign;
alter table tbl_medications change column  user_id user_id char(36);
update tbl_medications set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_medications drop foreign key if exists tbl_medications_visit_date_id_foreign;
alter table tbl_medications change column  visit_date_id visit_date_id char(36);
update tbl_medications set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_medications drop foreign key if exists tbl_medications_facility_id_foreign;
alter table tbl_medications change column  facility_id facility_id char(36);
update tbl_medications set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_std_investigation_results add column if not exists copy_id char(36) after id;
update tbl_std_investigation_results set copy_id = uuid();
alter table tbl_std_investigation_results drop foreign key if exists tbl_std_investigation_results_patient_id_foreign;
alter table tbl_std_investigation_results change column  patient_id patient_id char(36);
update tbl_std_investigation_results set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_std_investigation_results drop foreign key if exists tbl_std_investigation_results_user_id_foreign;
alter table tbl_std_investigation_results change column  user_id user_id char(36);
update tbl_std_investigation_results set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_std_investigation_results drop foreign key if exists tbl_std_investigation_results_facility_id_foreign;
alter table tbl_std_investigation_results change column  facility_id facility_id char(36);
update tbl_std_investigation_results set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_client_violence_outputs add column if not exists copy_id char(36) after id;
update tbl_client_violence_outputs set copy_id = uuid();
alter table tbl_client_violence_outputs drop foreign key if exists tbl_client_violence_outputs_patient_id_foreign;
alter table tbl_client_violence_outputs change column  patient_id patient_id char(36);
update tbl_client_violence_outputs set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_client_violence_outputs drop foreign key if exists tbl_client_violence_outputs_user_id_foreign;
alter table tbl_client_violence_outputs change column  user_id user_id char(36);
update tbl_client_violence_outputs set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_client_violence_outputs drop foreign key if exists tbl_client_violence_outputs_facility_id_foreign;
alter table tbl_client_violence_outputs change column  facility_id facility_id char(36);
update tbl_client_violence_outputs set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_anti_natal_referrals add column if not exists copy_id char(36) after id;
update tbl_anti_natal_referrals set copy_id = uuid();
alter table tbl_anti_natal_referrals drop foreign key if exists tbl_anti_natal_referrals_patient_id_foreign;
alter table tbl_anti_natal_referrals change column  patient_id patient_id char(36);
update tbl_anti_natal_referrals set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_anti_natal_referrals drop foreign key if exists tbl_anti_natal_referrals_user_id_foreign;
alter table tbl_anti_natal_referrals change column  user_id user_id char(36);
update tbl_anti_natal_referrals set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_referrals drop foreign key if exists tbl_anti_natal_referrals_facility_id_foreign;
alter table tbl_anti_natal_referrals change column  facility_id facility_id char(36);
update tbl_anti_natal_referrals set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_anti_natal_referrals drop foreign key if exists tbl_anti_natal_referrals_transfered_institution_id_foreign;
alter table tbl_anti_natal_referrals change column  transfered_institution_id transfered_institution_id char(36);
update tbl_anti_natal_referrals set transfered_institution_id = (select copy_id from tbl_facilities where tbl_facilities.id =  transfered_institution_id limit 1);
alter table tbl_anti_natal_referrals change column  reason reason text;

alter table tbl_child_vitamin_registers add column if not exists copy_id char(36) after id;
update tbl_child_vitamin_registers set copy_id = uuid();
alter table tbl_child_vitamin_registers drop foreign key if exists tbl_child_vitamin_registers_patient_id_foreign;
alter table tbl_child_vitamin_registers change column  patient_id patient_id char(36);
update tbl_child_vitamin_registers set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_child_vitamin_registers drop foreign key if exists tbl_child_vitamin_registers_user_id_foreign;
alter table tbl_child_vitamin_registers change column  user_id user_id char(36);
update tbl_child_vitamin_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_child_vitamin_registers drop foreign key if exists tbl_child_vitamin_registers_facility_id_foreign;
alter table tbl_child_vitamin_registers change column  facility_id facility_id char(36);
update tbl_child_vitamin_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_child_vitamin_registers drop foreign key if exists tbl_child_vitamin_registers_mother_id_foreign;
alter table tbl_child_vitamin_registers change column  mother_id mother_id char(36);
update tbl_child_vitamin_registers set mother_id = (select copy_id from tbl_patients where tbl_patients.id =  mother_id limit 1);

alter table tbl_clients_complains add column if not exists copy_id char(36) after id;
update tbl_clients_complains set copy_id = uuid();
alter table tbl_clients_complains drop foreign key if exists tbl_clients_complains_patient_id_foreign;
alter table tbl_clients_complains change column  patient_id patient_id char(36);
update tbl_clients_complains set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_clients_complains drop foreign key if exists tbl_clients_complains_user_id_foreign;
alter table tbl_clients_complains change column  user_id user_id char(36);
update tbl_clients_complains set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_clients_complains drop foreign key if exists tbl_clients_complains_facility_id_foreign;
alter table tbl_clients_complains change column  facility_id facility_id char(36);
update tbl_clients_complains set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_referrals add column if not exists copy_id char(36) after id;
update tbl_post_natal_referrals set copy_id = uuid();
alter table tbl_post_natal_referrals drop foreign key if exists tbl_post_natal_referrals_patient_id_foreign;
alter table tbl_post_natal_referrals change column  patient_id patient_id char(36);
update tbl_post_natal_referrals set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_post_natal_referrals drop foreign key if exists tbl_post_natal_referrals_user_id_foreign;
alter table tbl_post_natal_referrals change column  user_id user_id char(36);
update tbl_post_natal_referrals set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_referrals drop foreign key if exists tbl_post_natal_referrals_facility_id_foreign;
alter table tbl_post_natal_referrals change column  facility_id facility_id char(36);
update tbl_post_natal_referrals set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_post_natal_referrals drop foreign key if exists tbl_post_natal_referrals_transfered_institution_id_foreign;
alter table tbl_post_natal_referrals change column  transfered_institution_id transfered_institution_id char(36);
update tbl_post_natal_referrals set transfered_institution_id = (select copy_id from tbl_facilities where tbl_facilities.id =  transfered_institution_id limit 1);
alter table tbl_post_natal_referrals change column  reason reason text;

alter table tbl_anti_natal_councelling_givens add column if not exists copy_id char(36) after id;
update tbl_anti_natal_councelling_givens set copy_id = uuid();
alter table tbl_anti_natal_councelling_givens drop foreign key if exists tbl_anti_natal_councelling_givens_client_id_foreign;
alter table tbl_anti_natal_councelling_givens change column  client_id client_id char(36);
update tbl_anti_natal_councelling_givens set client_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  client_id limit 1);
alter table tbl_anti_natal_councelling_givens drop foreign key if exists tbl_anti_natal_councelling_givens_user_id_foreign;
alter table tbl_anti_natal_councelling_givens change column  user_id user_id char(36);
update tbl_anti_natal_councelling_givens set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_councelling_givens drop foreign key if exists tbl_anti_natal_councelling_givens_facility_id_foreign;
alter table tbl_anti_natal_councelling_givens change column  facility_id facility_id char(36);
update tbl_anti_natal_councelling_givens set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_tb_patient_treatment_types add column if not exists copy_id char(36) after id;
update tbl_tb_patient_treatment_types set copy_id = uuid();
alter table tbl_tb_patient_treatment_types drop foreign key if exists tbl_tb_patient_treatment_types_client_id_foreign;
alter table tbl_tb_patient_treatment_types change column  client_id  client_id char(36);
update tbl_tb_patient_treatment_types set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_tb_patient_treatment_types drop foreign key if exists tbl_tb_patient_treatment_types_user_id_foreign;
alter table tbl_tb_patient_treatment_types change column  user_id user_id char(36);
update tbl_tb_patient_treatment_types set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_tb_patient_treatment_types drop foreign key if exists tbl_tb_patient_treatment_types_facility_id_foreign;
alter table tbl_tb_patient_treatment_types change column  facility_id facility_id char(36);
update tbl_tb_patient_treatment_types set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_social_ward_rounds add column if not exists copy_id char(36) after id;
update tbl_social_ward_rounds set copy_id = uuid();
alter table tbl_social_ward_rounds drop foreign key if exists tbl_social_ward_rounds_patient_id_foreign;
alter table tbl_social_ward_rounds change column  patient_id patient_id char(36);
update tbl_social_ward_rounds set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_social_ward_rounds drop foreign key if exists tbl_social_ward_rounds_user_id_foreign;
alter table tbl_social_ward_rounds change column  user_id user_id char(36);
update tbl_social_ward_rounds set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_social_ward_rounds drop foreign key if exists tbl_social_ward_rounds_facility_id_foreign;
alter table tbl_social_ward_rounds change column  facility_id facility_id char(36);
update tbl_social_ward_rounds set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_social_ward_rounds change column  plan plan text;
alter table tbl_social_ward_rounds change column  output output text;
alter table tbl_social_ward_rounds change column  remarks remarks text;

alter table tbl_child_hiv_expose_registers add column if not exists copy_id char(36) after id;
update tbl_child_hiv_expose_registers set copy_id = uuid();
alter table tbl_child_hiv_expose_registers drop foreign key if exists tbl_child_hiv_expose_registers_patient_id_foreign;
alter table tbl_child_hiv_expose_registers change column  patient_id patient_id char(36);
update tbl_child_hiv_expose_registers set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_child_hiv_expose_registers drop foreign key if exists tbl_child_hiv_expose_registers_user_id_foreign;
alter table tbl_child_hiv_expose_registers change column  user_id user_id char(36);
update tbl_child_hiv_expose_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_child_hiv_expose_registers drop foreign key if exists tbl_child_hiv_expose_registers_facility_id_foreign;
alter table tbl_child_hiv_expose_registers change column  facility_id facility_id char(36);
update tbl_child_hiv_expose_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_child_hiv_expose_registers drop foreign key if exists tbl_child_hiv_expose_registers_mother_id_foreign;
alter table tbl_child_hiv_expose_registers change column  mother_id mother_id char(36);
update tbl_child_hiv_expose_registers set mother_id = (select copy_id from tbl_patients where tbl_patients.id =  mother_id limit 1);

alter table tbl_tt_vaccinations add column if not exists copy_id char(36) after id;
update tbl_tt_vaccinations set copy_id = uuid();
alter table tbl_tt_vaccinations drop foreign key if exists tbl_tt_vaccinations_patient_id_foreign;
alter table tbl_tt_vaccinations change column  patient_id patient_id char(36);
update tbl_tt_vaccinations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_tt_vaccinations drop foreign key if exists tbl_tt_vaccinations_user_id_foreign;
alter table tbl_tt_vaccinations change column  user_id user_id char(36);
update tbl_tt_vaccinations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_tt_vaccinations drop foreign key if exists tbl_tt_vaccinations_facility_id_foreign;
alter table tbl_tt_vaccinations change column  facility_id facility_id char(36);
update tbl_tt_vaccinations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table  tbl_theatre_waits add column if not exists copy_id char(36) after id;
update tbl_theatre_waits set copy_id = uuid();
alter table tbl_theatre_waits drop foreign key if exists tbl_theatre_waits_admission_id_foreign;
alter table tbl_theatre_waits change column  admission_id admission_id char(36);
update tbl_theatre_waits set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_theatre_waits drop foreign key if exists tbl_theatre_waits_nurse_id_foreign;
alter table tbl_theatre_waits change column  nurse_id nurse_id char(36);
update tbl_theatre_waits set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);

alter table tbl_intra_operations add column if not exists copy_id char(36) after id;
update tbl_intra_operations set copy_id = uuid();
alter table tbl_intra_operations drop foreign key if exists tbl_intra_operations_admission_id_foreign;
alter table tbl_intra_operations change column  admission_id admission_id char(36);
update tbl_intra_operations set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_intra_operations drop foreign key if exists tbl_intra_operations_request_id_foreign;
alter table tbl_intra_operations change column  request_id request_id char(36);
update tbl_intra_operations set request_id = (select copy_id from tbl_theatre_waits where tbl_theatre_waits.id =  request_id limit 1);
alter table tbl_intra_operations drop foreign key if exists tbl_intra_operations_nurse_id_foreign;
alter table tbl_intra_operations change column  nurse_id nurse_id char(36);
update tbl_intra_operations set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);
alter table tbl_intra_operations drop foreign key if exists tbl_intra_operations_doctor_id_foreign;
alter table tbl_intra_operations change column  doctor_id doctor_id char(36);
update tbl_intra_operations set doctor_id = (select copy_id from users where users.id =  doctor_id limit 1);
alter table tbl_intra_operations change column  remarks remarks text;

alter table tbl_family_planning_method_registers add column if not exists copy_id char(36) after id;
update tbl_family_planning_method_registers set copy_id = uuid();
alter table tbl_family_planning_method_registers drop foreign key if exists tbl_family_planning_method_registers_patient_id_foreign;
alter table tbl_family_planning_method_registers change column  patient_id patient_id char(36);
update tbl_family_planning_method_registers set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_family_planning_method_registers drop foreign key if exists tbl_family_planning_method_registers_user_id_foreign;
alter table tbl_family_planning_method_registers change column  user_id user_id char(36);
update tbl_family_planning_method_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_family_planning_method_registers drop foreign key if exists tbl_family_planning_method_registers_facility_id_foreign;
alter table tbl_family_planning_method_registers change column  facility_id facility_id char(36);
update tbl_family_planning_method_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_family_planning_method_registers change column  event_driven event_driven text;

alter table tbl_client_violence_services add column if not exists copy_id char(36) after id;
update tbl_client_violence_services set copy_id = uuid();
alter table tbl_client_violence_services drop foreign key if exists tbl_client_violence_services_patient_id_foreign;
alter table tbl_client_violence_services change column  patient_id patient_id char(36);
update tbl_client_violence_services set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_client_violence_services drop foreign key if exists tbl_client_violence_services_user_id_foreign;
alter table tbl_client_violence_services change column  user_id user_id char(36);
update tbl_client_violence_services set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_client_violence_services drop foreign key if exists tbl_client_violence_services_facility_id_foreign;
alter table tbl_client_violence_services change column  facility_id facility_id char(36);
update tbl_client_violence_services set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_anti_natal_partner_registers add column if not exists copy_id char(36) after id;
update tbl_anti_natal_partner_registers set copy_id = uuid();
alter table tbl_anti_natal_partner_registers drop foreign key if exists tbl_anti_natal_partner_registers_client_id_foreign;
alter table tbl_anti_natal_partner_registers change column  client_id client_id char(36);
update tbl_anti_natal_partner_registers set client_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  client_id limit 1);
alter table tbl_anti_natal_partner_registers drop foreign key if exists tbl_anti_natal_partner_registers_user_id_foreign;
alter table tbl_anti_natal_partner_registers change column  user_id user_id char(36);
update tbl_anti_natal_partner_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_partner_registers drop foreign key if exists tbl_anti_natal_partner_registers_facility_id_foreign;
alter table tbl_anti_natal_partner_registers change column  facility_id facility_id char(36);
update tbl_anti_natal_partner_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_child_arv_prophlaxises add column if not exists copy_id char(36) after id;
update tbl_post_natal_child_arv_prophlaxises set copy_id = uuid();
alter table tbl_post_natal_child_arv_prophlaxises drop foreign key if exists tbl_post_natal_child_arv_prophlaxises_patient_id_foreign;
alter table tbl_post_natal_child_arv_prophlaxises change column  patient_id patient_id char(36);
update tbl_post_natal_child_arv_prophlaxises set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_post_natal_child_arv_prophlaxises drop foreign key if exists tbl_post_natal_child_arv_prophlaxises_user_id_foreign;
alter table tbl_post_natal_child_arv_prophlaxises change column  user_id user_id char(36);
update tbl_post_natal_child_arv_prophlaxises set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_child_arv_prophlaxises drop foreign key if exists tbl_post_natal_child_arv_prophlaxises_facility_id_foreign;
alter table tbl_post_natal_child_arv_prophlaxises change column  facility_id facility_id char(36);
update tbl_post_natal_child_arv_prophlaxises set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_continuation_notes add column if not exists copy_id char(36) after id;
update tbl_continuation_notes set copy_id = uuid();
alter table tbl_continuation_notes drop foreign key if exists tbl_continuation_notes_patient_id_foreign;
alter table tbl_continuation_notes change column  patient_id patient_id char(36);
update tbl_continuation_notes set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_continuation_notes drop foreign key if exists tbl_continuation_notes_user_id_foreign;
alter table tbl_continuation_notes change column  user_id user_id char(36);
update tbl_continuation_notes set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_continuation_notes drop foreign key if exists tbl_continuation_notes_visit_id_foreign;
alter table tbl_continuation_notes change column  visit_id visit_id char(36);
update tbl_continuation_notes set visit_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_id limit 1);
alter table tbl_continuation_notes drop foreign key if exists tbl_continuation_notes_facility_id_foreign;
alter table tbl_continuation_notes change column  facility_id facility_id char(36);
update tbl_continuation_notes set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_general_appointments add column if not exists copy_id char(36) after id;
update tbl_general_appointments set copy_id = uuid();
alter table tbl_general_appointments drop foreign key if exists tbl_general_appointments_patient_id_foreign;
alter table tbl_general_appointments change column  patient_id patient_id char(36);
update tbl_general_appointments set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_general_appointments drop foreign key if exists tbl_general_appointments_user_id_foreign;
alter table tbl_general_appointments change column  user_id user_id char(36);
update tbl_general_appointments set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_general_appointments drop foreign key if exists tbl_general_appointments_facility_id_foreign;
alter table tbl_general_appointments change column  facility_id facility_id char(36);
update tbl_general_appointments set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_partial_payments add column if not exists copy_id char(36) after id;
update tbl_partial_payments set copy_id = uuid();
alter table tbl_partial_payments drop foreign key if exists tbl_partial_payments_patient_id_foreign;
alter table tbl_partial_payments change column  patient_id patient_id char(36);
update tbl_partial_payments set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_partial_payments drop foreign key if exists tbl_partial_payments_user_id_foreign;
alter table tbl_partial_payments change column  user_id user_id char(36);
update tbl_partial_payments set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_partial_payments drop foreign key if exists tbl_partial_payments_visit_date_id_foreign;
alter table tbl_partial_payments change column  visit_date_id visit_date_id char(36);
update tbl_partial_payments set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_partial_payments drop foreign key if exists tbl_partial_payments_facility_id_foreign;
alter table tbl_partial_payments change column  facility_id facility_id char(36);
update tbl_partial_payments set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_partial_payments drop foreign key if exists tbl_partial_payments_invoice_id_foreign;
alter table tbl_partial_payments change column  invoice_id invoice_id char(36);
update tbl_partial_payments set invoice_id = (select copy_id from tbl_encounter_invoices where tbl_encounter_invoices.id =  invoice_id limit 1);

alter table tbl_next_of_kins add column if not exists copy_id char(36) after id;
update tbl_next_of_kins set copy_id = uuid();
alter table tbl_next_of_kins drop foreign key if exists tbl_next_of_kins_patient_id_foreign;
alter table tbl_next_of_kins change column  patient_id patient_id char(36);
update tbl_next_of_kins set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);

alter table tbl_past_ent_histories add column if not exists copy_id char(36) after id;
update tbl_past_ent_histories set copy_id = uuid();
alter table tbl_past_ent_histories drop foreign key if exists tbl_past_ent_histories_patient_id_foreign;
alter table tbl_past_ent_histories change column  patient_id patient_id char(36);
update tbl_past_ent_histories set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_past_ent_histories drop foreign key if exists tbl_past_ent_histories_user_id_foreign;
alter table tbl_past_ent_histories change column  user_id user_id char(36);
update tbl_past_ent_histories set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_past_ent_histories drop foreign key if exists tbl_past_ent_histories_visit_date_id_foreign;
alter table tbl_past_ent_histories change column  visit_date_id visit_date_id char(36);
update tbl_past_ent_histories set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_past_ent_histories drop foreign key if exists tbl_past_ent_histories_facility_id_foreign;
alter table tbl_past_ent_histories change column  facility_id facility_id char(36);
update tbl_past_ent_histories set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_past_ent_histories drop foreign key if exists tbl_past_ent_histories_admission_id_foreign;
alter table tbl_past_ent_histories change column  admission_id admission_id char(36);
update tbl_past_ent_histories set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_past_psych_records add column if not exists copy_id char(36) after id;
update tbl_past_psych_records set copy_id = uuid();
alter table tbl_past_psych_records drop foreign key if exists tbl_past_psych_records_patient_id_foreign;
alter table tbl_past_psych_records change column  patient_id patient_id char(36);
update tbl_past_psych_records set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_past_psych_records drop foreign key if exists tbl_past_psych_records_user_id_foreign;
alter table tbl_past_psych_records change column  user_id user_id char(36);
update tbl_past_psych_records set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_past_psych_records drop foreign key if exists tbl_past_psych_records_visit_date_id_foreign;
alter table tbl_past_psych_records change column  visit_date_id visit_date_id char(36);
update tbl_past_psych_records set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);

alter table tbl_refferal_externals add column if not exists copy_id char(36) after id;
update tbl_refferal_externals set copy_id = uuid();
alter table tbl_refferal_externals drop foreign key if exists tbl_refferal_externals_patient_id_foreign;
alter table tbl_refferal_externals change column  patient_id patient_id char(36);
update tbl_refferal_externals set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_refferal_externals drop foreign key if exists tbl_refferal_externals_reffered_by_foreign;
alter table tbl_refferal_externals change column  reffered_by reffered_by char(36);
update tbl_refferal_externals set reffered_by = (select copy_id from users where users.id =  reffered_by limit 1);
alter table tbl_refferal_externals drop foreign key if exists tbl_refferal_externals_escorting_staff_foreign;
alter table tbl_refferal_externals change column  escorting_staff escorting_staff char(36);
update tbl_refferal_externals set escorting_staff = (select copy_id from users where users.id =  escorting_staff limit 1);
alter table tbl_refferal_externals drop foreign key if exists tbl_refferal_externals_sender_facility_id_foreign;
alter table tbl_refferal_externals change column  sender_facility_id sender_facility_id char(36);
update tbl_refferal_externals set sender_facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  sender_facility_id limit 1);

alter table tbl_therapy_treatments add column if not exists copy_id char(36) after id;
update tbl_therapy_treatments set copy_id = uuid();
alter table tbl_therapy_treatments drop foreign key if exists tbl_therapy_treatments_patient_id_foreign;
alter table tbl_therapy_treatments change column  patient_id patient_id char(36);
update tbl_therapy_treatments set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_therapy_treatments drop foreign key if exists tbl_therapy_treatments_user_id_foreign;
alter table tbl_therapy_treatments change column  user_id user_id char(36);
update tbl_therapy_treatments set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_therapy_treatments drop foreign key if exists tbl_therapy_treatments_visit_date_id_foreign;
alter table tbl_therapy_treatments change column  visit_date_id visit_date_id char(36);
update tbl_therapy_treatments set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_therapy_treatments drop foreign key if exists tbl_therapy_treatments_facility_id_foreign;
alter table tbl_therapy_treatments change column  facility_id facility_id char(36);
update tbl_therapy_treatments set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_therapy_treatments change family family text;

alter table tbl_anti_natal_preventives add column if not exists copy_id char(36) after id;
update tbl_anti_natal_preventives set copy_id = uuid();
alter table tbl_anti_natal_preventives drop foreign key if exists tbl_anti_natal_preventives_client_id_foreign;
alter table tbl_anti_natal_preventives change column  client_id client_id char(36);
update tbl_anti_natal_preventives set client_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  client_id limit 1);
alter table tbl_anti_natal_preventives drop foreign key if exists tbl_anti_natal_preventives_user_id_foreign;
alter table tbl_anti_natal_preventives change column  user_id user_id char(36);
update tbl_anti_natal_preventives set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_preventives drop foreign key if exists tbl_anti_natal_preventives_facility_id_foreign;
alter table tbl_anti_natal_preventives change column  facility_id facility_id char(36);
update tbl_anti_natal_preventives set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_configurations add column if not exists copy_id char(36) after id;
update tbl_configurations set copy_id = uuid();
alter table tbl_configurations drop foreign key if exists tbl_configurations_user_id_foreign;
alter table tbl_configurations change column  user_id user_id char(36);
update tbl_configurations set user_id = (select copy_id from users where users.id =  user_id limit 1);

alter table tbl_fplanning_placenta_cancer_investigations add column if not exists copy_id char(36) after id;
update tbl_fplanning_placenta_cancer_investigations set copy_id = uuid();
alter table tbl_fplanning_placenta_cancer_investigations drop foreign key if exists tbl_fplanning_placenta_cancer_investigations_client_id_foreign;
alter table tbl_fplanning_placenta_cancer_investigations change column  client_id client_id char(36);
update tbl_fplanning_placenta_cancer_investigations set client_id = (select copy_id from tbl_family_planning_registers where tbl_family_planning_registers.id =  client_id limit 1);
alter table tbl_fplanning_placenta_cancer_investigations drop foreign key if exists tbl_fplanning_placenta_cancer_investigations_user_id_foreign;
alter table tbl_fplanning_placenta_cancer_investigations change column  user_id user_id char(36);
update tbl_fplanning_placenta_cancer_investigations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_fplanning_placenta_cancer_investigations drop foreign key if exists tbl_fplanning_placenta_cancer_investigations_facility_id_foreign;
alter table tbl_fplanning_placenta_cancer_investigations change column  facility_id facility_id char(36);
update tbl_fplanning_placenta_cancer_investigations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_delivery_child_dispositions add column if not exists copy_id char(36) after id;
update tbl_labour_delivery_child_dispositions set copy_id = uuid();
alter table tbl_labour_delivery_child_dispositions drop foreign key if exists tbl_labour_delivery_child_dispositions_patient_id_foreign;
alter table tbl_labour_delivery_child_dispositions change column  patient_id patient_id char(36);
update tbl_labour_delivery_child_dispositions set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_labour_delivery_child_dispositions drop foreign key if exists tbl_labour_delivery_child_dispositions_user_id_foreign;
alter table tbl_labour_delivery_child_dispositions change column  user_id user_id char(36);
update tbl_labour_delivery_child_dispositions set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_delivery_child_dispositions drop foreign key if exists tbl_labour_delivery_child_dispositions_facility_id_foreign;
alter table tbl_labour_delivery_child_dispositions change column  facility_id facility_id char(36);
update tbl_labour_delivery_child_dispositions set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_output_observations add column if not exists copy_id char(36) after id;
update tbl_output_observations set copy_id = uuid();
alter table tbl_output_observations drop foreign key if exists tbl_output_observations_nurse_id_foreign;
alter table tbl_output_observations change column  nurse_id nurse_id char(36);
update tbl_output_observations set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);
alter table tbl_output_observations drop foreign key if exists tbl_output_observations_admission_id_foreign;
alter table tbl_output_observations change column  admission_id admission_id char(36);
update tbl_output_observations set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_panels add column if not exists copy_id char(36) after id;
update tbl_panels set copy_id = uuid();
alter table tbl_panels drop foreign key if exists tbl_panels_user_id_foreign;
alter table tbl_panels change column  user_id user_id char(36);
update tbl_panels set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_panels drop foreign key if exists tbl_panels_equipment_id_foreign;
alter table tbl_panels change column  equipment_id equipment_id char(36);
update tbl_panels set equipment_id = (select copy_id from tbl_equipments where tbl_equipments.id =  equipment_id limit 1);

alter table tbl_permits add column if not exists copy_id char(36) after id;
update tbl_permits set copy_id = uuid();
alter table tbl_permits drop foreign key if exists tbl_permits_user_id_foreign;
alter table tbl_permits change column  user_id user_id char(36);
update tbl_permits set user_id = (select copy_id from users where users.id =  user_id limit 1);

update tbl_permits set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_permits drop foreign key if exists tbl_permits_corpse_id_foreign;
alter table tbl_permits change column  corpse_id corpse_id char(36);
update tbl_permits set corpse_id = (select copy_id from tbl_corpses where tbl_corpses.id =  corpse_id limit 1);

update tbl_permits set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_permits drop foreign key if exists tbl_permits_facility_id_foreign;
alter table tbl_permits change column  facility_id facility_id char(36);
update tbl_permits set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_admission_registers add column if not exists copy_id char(36) after id;
update tbl_admission_registers set copy_id = uuid();
alter table tbl_admission_registers drop foreign key if exists tbl_admission_registers_facility_id_foreign;
alter table tbl_admission_registers change column  facility_id facility_id char(36);
update tbl_admission_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_newattendance_registers add column if not exists copy_id char(36) after id;
update tbl_newattendance_registers set copy_id = uuid();
alter table tbl_newattendance_registers drop foreign key if exists tbl_newattendance_registers_facility_id_foreign;
alter table tbl_newattendance_registers change column  facility_id facility_id char(36);
update tbl_newattendance_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_reattendance_registers add column if not exists copy_id char(36) after id;
update tbl_reattendance_registers set copy_id = uuid();
alter table tbl_reattendance_registers drop foreign key if exists tbl_reattendance_registers_facility_id_foreign;
alter table tbl_reattendance_registers change column  facility_id facility_id char(36);
update tbl_reattendance_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_opd_diseases_registers add column if not exists copy_id char(36) after id;
update tbl_opd_diseases_registers set copy_id = uuid();
alter table tbl_opd_diseases_registers drop foreign key if exists tbl_opd_diseases_registers_facility_id_foreign;
alter table tbl_opd_diseases_registers change column  facility_id facility_id char(36);
update tbl_opd_diseases_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_ipd_diseases_registers add column if not exists copy_id char(36) after id;
update tbl_ipd_diseases_registers set copy_id = uuid();
alter table tbl_ipd_diseases_registers drop foreign key if exists tbl_ipd_diseases_registers_facility_id_foreign;
alter table tbl_ipd_diseases_registers change column  facility_id facility_id char(36);
update tbl_ipd_diseases_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_outgoing_referral_registers add column if not exists copy_id char(36) after id;
update tbl_outgoing_referral_registers set copy_id = uuid();
alter table tbl_outgoing_referral_registers drop foreign key if exists tbl_outgoing_referral_registers_facility_id_foreign;
alter table tbl_outgoing_referral_registers change column  facility_id facility_id char(36);
update tbl_outgoing_referral_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_integrating_keys add column if not exists copy_id char(36) after id;
update tbl_integrating_keys set copy_id = uuid();
alter table tbl_integrating_keys drop foreign key if exists tbl_integrating_keys_facility_id_foreign;
alter table tbl_integrating_keys change column  facility_id facility_id char(36);
update tbl_integrating_keys set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_route_keys add column if not exists copy_id char(36) after id;
update tbl_route_keys set copy_id = uuid();
alter table tbl_route_keys drop foreign key if exists tbl_route_keys_facility_id_foreign;
alter table tbl_route_keys change column  facility_id facility_id char(36);
update tbl_route_keys set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_investigation_details add column if not exists copy_id char(36) after id;
update tbl_investigation_details set copy_id = uuid();
alter table tbl_investigation_details drop foreign key if exists tbl_investigation_details_investigation_id_foreign;
alter table tbl_investigation_details change column  investigation_id investigation_id char(36);
update tbl_investigation_details set investigation_id = (select copy_id from tbl_investigations where tbl_investigations.id =  investigation_id limit 1);

alter table tbl_comma_scales_histories add column if not exists copy_id char(36) after id;
update tbl_comma_scales_histories set copy_id = uuid();
alter table tbl_comma_scales_histories drop foreign key if exists tbl_comma_scales_histories_comma_scale_id_foreign;
alter table tbl_comma_scales_histories change column  comma_scale_id comma_scale_id char(36);
update tbl_comma_scales_histories set comma_scale_id = (select copy_id from tbl_comma_scales where tbl_comma_scales.id =  comma_scale_id limit 1);

alter table tbl_post_natal_child_vaccinations add column if not exists copy_id char(36) after id;
update tbl_post_natal_child_vaccinations set copy_id = uuid();
alter table tbl_post_natal_child_vaccinations drop foreign key if exists tbl_post_natal_child_vaccinations_patient_id_foreign;
alter table tbl_post_natal_child_vaccinations change column  patient_id patient_id char(36);
update tbl_post_natal_child_vaccinations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_post_natal_child_vaccinations drop foreign key if exists tbl_post_natal_child_vaccinations_user_id_foreign;
alter table tbl_post_natal_child_vaccinations change column  user_id user_id char(36);
update tbl_post_natal_child_vaccinations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_child_vaccinations drop foreign key if exists tbl_post_natal_child_vaccinations_facility_id_foreign;
alter table tbl_post_natal_child_vaccinations change column  facility_id facility_id char(36);
update tbl_post_natal_child_vaccinations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_exemption_tracking_statuses add column if not exists copy_id char(36) after id;
update tbl_exemption_tracking_statuses set copy_id = uuid();
alter table tbl_exemption_tracking_statuses drop foreign key if exists tbl_exemption_tracking_statuses_patient_id_foreign;
alter table tbl_exemption_tracking_statuses change column  patient_id patient_id char(36);
update tbl_exemption_tracking_statuses set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_exemption_tracking_statuses drop foreign key if exists tbl_exemption_tracking_statuses_user_id_foreign;
alter table tbl_exemption_tracking_statuses change column  user_id user_id char(36);
update tbl_exemption_tracking_statuses set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_exemption_tracking_statuses drop foreign key if exists tbl_exemption_tracking_statuses_facility_id_foreign;
alter table tbl_exemption_tracking_statuses change column  facility_id facility_id char(36);
update tbl_exemption_tracking_statuses set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_child_feedings add column if not exists copy_id char(36) after id;
update tbl_child_feedings set copy_id = uuid();
alter table tbl_child_feedings drop foreign key if exists tbl_child_feedings_patient_id_foreign;
alter table tbl_child_feedings change column  patient_id patient_id char(36);
update tbl_child_feedings set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_child_feedings drop foreign key if exists tbl_child_feedings_user_id_foreign;
alter table tbl_child_feedings change column  user_id user_id char(36);
update tbl_child_feedings set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_child_feedings drop foreign key if exists tbl_child_feedings_facility_id_foreign;
alter table tbl_child_feedings change column  facility_id facility_id char(36);
update tbl_child_feedings set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_child_feedings drop foreign key if exists tbl_child_feedings_mother_id_foreign;
alter table tbl_child_feedings change column  mother_id mother_id char(36);
update tbl_child_feedings set mother_id = (select copy_id from tbl_patients where tbl_patients.id =  mother_id limit 1);

alter table tbl_post_natal_tt_vaccinations add column if not exists copy_id char(36) after id;
update tbl_post_natal_tt_vaccinations set copy_id = uuid();
alter table tbl_post_natal_tt_vaccinations drop foreign key if exists tbl_post_natal_tt_vaccinations_patient_id_foreign;
alter table tbl_post_natal_tt_vaccinations change column  patient_id patient_id char(36);
update tbl_post_natal_tt_vaccinations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_post_natal_tt_vaccinations drop foreign key if exists tbl_post_natal_tt_vaccinations_user_id_foreign;
alter table tbl_post_natal_tt_vaccinations change column  user_id user_id char(36);
update tbl_post_natal_tt_vaccinations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_tt_vaccinations drop foreign key if exists tbl_post_natal_tt_vaccinations_facility_id_foreign;
alter table tbl_post_natal_tt_vaccinations change column  facility_id facility_id char(36);
update tbl_post_natal_tt_vaccinations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_std_investigation_partner_results add column if not exists copy_id char(36) after id;
update tbl_std_investigation_partner_results set copy_id = uuid();
alter table tbl_std_investigation_partner_results drop foreign key if exists tbl_std_investigation_partner_results_patient_id_foreign;
alter table tbl_std_investigation_partner_results change column  patient_id patient_id char(36);
update tbl_std_investigation_partner_results set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_std_investigation_partner_results drop foreign key if exists tbl_std_investigation_partner_results_user_id_foreign;
alter table tbl_std_investigation_partner_results change column  user_id user_id char(36);
update tbl_std_investigation_partner_results set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_std_investigation_partner_results drop foreign key if exists tbl_std_investigation_partner_results_std_id_foreign;
alter table tbl_std_investigation_partner_results change column  std_id std_id char(36);
update tbl_std_investigation_partner_results set std_id = (select copy_id from tbl_std_investigation_results where tbl_std_investigation_results.id =  std_id limit 1);
alter table tbl_std_investigation_partner_results drop foreign key if exists tbl_std_investigation_partner_results_facility_id_foreign;
alter table tbl_std_investigation_partner_results change column  facility_id facility_id char(36);
update tbl_std_investigation_partner_results set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_child_registers add column if not exists copy_id char(36) after id;
update tbl_child_registers set copy_id = uuid();
alter table tbl_child_registers drop foreign key if exists tbl_child_registers_patient_id_foreign;
alter table tbl_child_registers change column  patient_id patient_id char(36);
update tbl_child_registers set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_child_registers drop foreign key if exists tbl_child_registers_user_id_foreign;
alter table tbl_child_registers change column  user_id user_id char(36);
update tbl_child_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_child_registers drop foreign key if exists tbl_child_registers_facility_id_foreign;
alter table tbl_child_registers change column  facility_id facility_id char(36);
update tbl_child_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_client_violence_informants add column if not exists copy_id char(36) after id;
update tbl_client_violence_informants set copy_id = uuid();
alter table tbl_client_violence_informants drop foreign key if exists tbl_client_violence_informants_patient_id_foreign;
alter table tbl_client_violence_informants change column  patient_id patient_id char(36);
update tbl_client_violence_informants set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_client_violence_informants drop foreign key if exists tbl_client_violence_informants_user_id_foreign;
alter table tbl_client_violence_informants change column  user_id user_id char(36);
update tbl_client_violence_informants set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_client_violence_informants drop foreign key if exists tbl_client_violence_informants_facility_id_foreign;
alter table tbl_client_violence_informants change column  facility_id facility_id char(36);
update tbl_client_violence_informants set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_client_violence_informants change column  description description text;

alter table tbl_family_planning_referrals add column if not exists copy_id char(36) after id;
update tbl_family_planning_referrals set copy_id = uuid();
alter table tbl_family_planning_referrals drop foreign key if exists tbl_family_planning_referrals_patient_id_foreign;
alter table tbl_family_planning_referrals change column  patient_id patient_id char(36);
update tbl_family_planning_referrals set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_family_planning_referrals drop foreign key if exists tbl_family_planning_referrals_user_id_foreign;
alter table tbl_family_planning_referrals change column  user_id user_id char(36);
update tbl_family_planning_referrals set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_family_planning_referrals drop foreign key if exists tbl_family_planning_referrals_facility_id_foreign;
alter table tbl_family_planning_referrals change column  facility_id facility_id char(36);
update tbl_family_planning_referrals set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_family_planning_referrals drop foreign key if exists tbl_family_planning_referrals_transfered_institution_id_foreign;
alter table tbl_family_planning_referrals change column  transfered_institution_id transfered_institution_id char(36);
update tbl_family_planning_referrals set transfered_institution_id = (select copy_id from tbl_facilities where tbl_facilities.id =  transfered_institution_id limit 1);
alter table tbl_family_planning_referrals change column  reason reason text;

alter table tbl_clinic_attendances add column if not exists copy_id char(36) after id;
update tbl_clinic_attendances set copy_id = uuid();
alter table tbl_clinic_attendances drop foreign key if exists tbl_clinic_attendances_refferal_id_foreign;
alter table tbl_clinic_attendances change column  refferal_id refferal_id char(36);
update tbl_clinic_attendances set refferal_id = (select copy_id from tbl_clinic_instructions where tbl_clinic_instructions.id =  refferal_id limit 1);
alter table tbl_clinic_attendances drop foreign key if exists tbl_clinic_attendances_visit_id_foreign;
alter table tbl_clinic_attendances change column  visit_id visit_id char(36);
update tbl_clinic_attendances set visit_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_id limit 1);

alter table tbl_previous_pregnancy_infos add column if not exists copy_id char(36) after id;
update tbl_previous_pregnancy_infos set copy_id = uuid();
alter table tbl_previous_pregnancy_infos drop foreign key if exists tbl_previous_pregnancy_infos_client_id_foreign;
alter table tbl_previous_pregnancy_infos change column  client_id client_id char(36);
update tbl_previous_pregnancy_infos set client_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  client_id limit 1);
alter table tbl_previous_pregnancy_infos drop foreign key if exists tbl_previous_pregnancy_infos_user_id_foreign;
alter table tbl_previous_pregnancy_infos change column  user_id user_id char(36);
update tbl_previous_pregnancy_infos set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_previous_pregnancy_infos drop foreign key if exists tbl_previous_pregnancy_infos_facility_id_foreign;
alter table tbl_previous_pregnancy_infos change column  facility_id facility_id char(36);
update tbl_previous_pregnancy_infos set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_previous_pregnancy_infos drop foreign key if exists tbl_previous_pregnancy_infos_delivery_place_foreign;
alter table tbl_previous_pregnancy_infos change column  delivery_place delivery_place char(36);
update tbl_previous_pregnancy_infos set delivery_place = (select copy_id from tbl_facilities where tbl_facilities.id =  delivery_place limit 1);

-- done 1

alter table tbl_attachments add column if not exists copy_id char(36) after id;
update tbl_attachments set copy_id = uuid();
alter table tbl_attachments drop foreign key if exists tbl_attachments_patient_id_foreign;
alter table tbl_attachments change column  patient_id patient_id char(36);
update tbl_attachments set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);

alter table tbl_gbv_vacs add column if not exists copy_id char(36) after id;
update tbl_gbv_vacs set copy_id = uuid();
alter table tbl_gbv_vacs drop foreign key if exists tbl_gbv_vacs_user_id_foreign;
alter table tbl_gbv_vacs change column  user_id user_id char(36);
update tbl_gbv_vacs set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_gbv_vacs drop foreign key if exists tbl_gbv_vacs_facility_id_foreign;
alter table tbl_gbv_vacs change column  facility_id facility_id char(36);
update tbl_gbv_vacs set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_gbv_vacs drop foreign key if exists tbl_gbv_vacs_referral_id_foreign;
alter table tbl_gbv_vacs change column  referral_id referral_id char(36);
update tbl_gbv_vacs set referral_id = (select copy_id from tbl_facilities where tbl_facilities.id =  referral_id limit 1);
alter table tbl_gbv_vacs drop foreign key if exists tbl_gbv_vacs_patient_id_foreign;
alter table tbl_gbv_vacs change column  patient_id patient_id char(36);
update tbl_gbv_vacs set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_gbv_vacs drop foreign key if exists tbl_gbv_vacs_attachment_id_foreign;
alter table tbl_gbv_vacs change column  attachment_id attachment_id char(36);
update tbl_gbv_vacs set attachment_id = (select copy_id from tbl_attachments where tbl_attachments.id =  attachment_id limit 1);

alter table tbl_prescriptions add column if not exists copy_id char(36) after id;
update tbl_prescriptions set copy_id = uuid();
alter table tbl_prescriptions drop foreign key if exists tbl_prescriptions_patient_id_foreign;
alter table tbl_prescriptions change column  patient_id patient_id char(36);
update tbl_prescriptions set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_prescriptions drop foreign key if exists tbl_prescriptions_prescriber_id_foreign;
alter table tbl_prescriptions change column  prescriber_id prescriber_id char(36);
update tbl_prescriptions set prescriber_id = (select copy_id from users where users.id =  verifier_id limit 1);
alter table tbl_prescriptions drop foreign key if exists tbl_prescriptions_verifier_id_foreign;
alter table tbl_prescriptions change column  verifier_id verifier_id char(36);
update tbl_prescriptions set verifier_id = (select copy_id from users where users.id =  verifier_id limit 1);
alter table tbl_prescriptions drop foreign key if exists tbl_prescriptions_dispenser_id_foreign;
alter table tbl_prescriptions change column  dispenser_id dispenser_id char(36);
update tbl_prescriptions set dispenser_id = (select copy_id from users where users.id =  dispenser_id limit 1);
alter table tbl_prescriptions drop foreign key if exists tbl_prescriptions_admission_id_foreign;
alter table tbl_prescriptions change column  admission_id admission_id char(36);
update tbl_prescriptions set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_prescriptions drop foreign key if exists tbl_prescriptions_visit_id_foreign;
alter table tbl_prescriptions change column  visit_id visit_id char(36);
update tbl_prescriptions set visit_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_id limit 1);

alter table tbl_labour_delivery_events add column if not exists copy_id char(36) after id;
update tbl_labour_delivery_events set copy_id = uuid();
alter table tbl_labour_delivery_events drop foreign key if exists tbl_labour_delivery_events_client_id_foreign;
alter table tbl_labour_delivery_events change column  client_id client_id char(36);
update tbl_labour_delivery_events set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_labour_delivery_events drop foreign key if exists tbl_labour_delivery_events_user_id_foreign;
alter table tbl_labour_delivery_events change column  user_id user_id char(36);
update tbl_labour_delivery_events set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_delivery_events drop foreign key if exists tbl_labour_delivery_events_facility_id_foreign;
alter table tbl_labour_delivery_events change column  facility_id facility_id char(36);
update tbl_labour_delivery_events set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_labour_delivery_events drop foreign key if exists tbl_labour_delivery_events_tailer_id_foreign;
alter table tbl_labour_delivery_events change column  tailer_id tailer_id char(36);
update tbl_labour_delivery_events set tailer_id = (select copy_id from users where users.id =  tailer_id limit 1);

alter table tbl_teeth_patients add column if not exists copy_id char(36) after id;
alter table tbl_teeth_patients change column  id id char(36);
alter table tbl_teeth_patients drop foreign key if exists tbl_teeth_patients_admission_id_foreign;
alter table tbl_teeth_patients change column  admission_id admission_id char(36);
update tbl_teeth_patients set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_teeth_patients drop foreign key if exists tbl_teeth_patients_nurse_id_foreign;
alter table tbl_teeth_patients change column  nurse_id nurse_id char(36);
update tbl_teeth_patients set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);

alter table tbl_child_growth_registers add column if not exists copy_id char(36) after id;
update tbl_child_growth_registers set copy_id = uuid();
alter table tbl_child_growth_registers drop foreign key if exists tbl_child_growth_registers_patient_id_foreign;
alter table tbl_child_growth_registers change column  patient_id patient_id char(36);
update tbl_child_growth_registers set patient_id = (select copy_id from tbl_child_registers where tbl_child_registers.id =  patient_id limit 1);
alter table tbl_child_growth_registers drop foreign key if exists tbl_child_growth_registers_user_id_foreign;
alter table tbl_child_growth_registers change column  user_id user_id char(36);
update tbl_child_growth_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_child_growth_registers drop foreign key if exists tbl_child_growth_registers_facility_id_foreign;
alter table tbl_child_growth_registers change column  facility_id facility_id char(36);
update tbl_child_growth_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_child_subsidized_voucher_registers add column if not exists copy_id char(36) after id;
update tbl_child_subsidized_voucher_registers set copy_id = uuid();
alter table tbl_child_subsidized_voucher_registers drop foreign key if exists tbl_child_subsidized_voucher_registers_patient_id_foreign;
alter table tbl_child_subsidized_voucher_registers change column  patient_id patient_id char(36);
update tbl_child_subsidized_voucher_registers set patient_id = (select copy_id from tbl_child_registers where tbl_child_registers.id =  patient_id limit 1);
alter table tbl_child_subsidized_voucher_registers drop foreign key if exists tbl_child_subsidized_voucher_registers_user_id_foreign;
alter table tbl_child_subsidized_voucher_registers change column  user_id user_id char(36);
update tbl_child_subsidized_voucher_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_child_subsidized_voucher_registers drop foreign key if exists tbl_child_subsidized_voucher_registers_facility_id_foreign;
alter table tbl_child_subsidized_voucher_registers change column  facility_id facility_id char(36);
update tbl_child_subsidized_voucher_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_child_deworm_registers add column if not exists copy_id char(36) after id;
update tbl_child_deworm_registers set copy_id = uuid();
alter table tbl_child_deworm_registers drop foreign key if exists tbl_child_deworm_registers_patient_id_foreign;
alter table tbl_child_deworm_registers change column  patient_id patient_id char(36);
update tbl_child_deworm_registers set patient_id = (select copy_id from tbl_child_registers where tbl_child_registers.id =  patient_id limit 1);
alter table tbl_child_deworm_registers drop foreign key if exists tbl_child_deworm_registers_user_id_foreign;
alter table tbl_child_deworm_registers change column  user_id user_id char(36);
update tbl_child_deworm_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_child_deworm_registers drop foreign key if exists tbl_child_deworm_registers_facility_id_foreign;
alter table tbl_child_deworm_registers change column  facility_id facility_id char(36);
update tbl_child_deworm_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_registers add column if not exists copy_id char(36) after id;
update tbl_post_natal_registers set copy_id = uuid();
alter table tbl_post_natal_registers drop foreign key if exists tbl_post_natal_registers_patient_id_foreign;
alter table tbl_post_natal_registers change column  patient_id patient_id char(36);
update tbl_post_natal_registers set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_post_natal_registers drop foreign key if exists tbl_post_natal_registers_user_id_foreign;
alter table tbl_post_natal_registers change column  user_id user_id char(36);
update tbl_post_natal_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_registers drop foreign key if exists tbl_post_natal_registers_facility_id_foreign;
alter table tbl_post_natal_registers change column  facility_id facility_id char(36);
update tbl_post_natal_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_child_mother_details add column if not exists copy_id char(36) after id;
update tbl_child_mother_details set copy_id = uuid();
alter table tbl_child_mother_details drop foreign key if exists tbl_child_mother_details_patient_id_foreign;
alter table tbl_child_mother_details change column  patient_id patient_id char(36);
update tbl_child_mother_details set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_child_mother_details drop foreign key if exists tbl_child_mother_details_user_id_foreign;
alter table tbl_child_mother_details change column  user_id user_id char(36);
update tbl_child_mother_details set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_child_mother_details drop foreign key if exists tbl_child_mother_details_facility_id_foreign;
alter table tbl_child_mother_details change column  facility_id facility_id char(36);
update tbl_child_mother_details set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_child_vitamin_deworm_registers add column if not exists copy_id char(36) after id;
update tbl_child_vitamin_deworm_registers set copy_id = uuid();
alter table tbl_child_vitamin_deworm_registers drop foreign key if exists tbl_child_vitamin_deworm_registers_client_id_foreign;
alter table tbl_child_vitamin_deworm_registers change column  client_id client_id char(36);
update tbl_child_vitamin_deworm_registers set client_id = (select copy_id from tbl_child_registers where tbl_child_registers.id =  client_id limit 1);
alter table tbl_child_vitamin_deworm_registers drop foreign key if exists tbl_child_vitamin_deworm_registers_user_id_foreign;
alter table tbl_child_vitamin_deworm_registers change column  user_id user_id char(36);
update tbl_child_vitamin_deworm_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_child_vitamin_deworm_registers drop foreign key if exists tbl_child_vitamin_deworm_registers_facility_id_foreign;
alter table tbl_child_vitamin_deworm_registers change column  facility_id facility_id char(36);
update tbl_child_vitamin_deworm_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_pediatric_natals add column if not exists copy_id char(36) after id;
update tbl_pediatric_natals set copy_id = uuid();
alter table tbl_pediatric_natals drop foreign key if exists tbl_pediatric_natals_client_id_foreign;
alter table tbl_pediatric_natals change column  client_id client_id char(36);
update tbl_pediatric_natals set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_pediatric_natals drop foreign key if exists tbl_pediatric_natals_user_id_foreign;
alter table tbl_pediatric_natals change column  user_id user_id char(36);
update tbl_pediatric_natals set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_pediatric_natals drop foreign key if exists tbl_pediatric_natals_facility_id_foreign;
alter table tbl_pediatric_natals change column  facility_id facility_id char(36);
update tbl_pediatric_natals set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_anti_natal_ipts add column if not exists copy_id char(36) after id;
update tbl_anti_natal_ipts set copy_id = uuid();
alter table tbl_anti_natal_ipts drop foreign key if exists tbl_anti_natal_ipts_patient_id_foreign;
alter table tbl_anti_natal_ipts change column  patient_id patient_id char(36);
update tbl_anti_natal_ipts set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_anti_natal_ipts drop foreign key if exists tbl_anti_natal_ipts_user_id_foreign;
alter table tbl_anti_natal_ipts change column  user_id user_id char(36);
update tbl_anti_natal_ipts set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_ipts drop foreign key if exists tbl_anti_natal_ipts_facility_id_foreign;
alter table tbl_anti_natal_ipts change column  facility_id facility_id char(36);
update tbl_anti_natal_ipts set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_emonc_services add column if not exists copy_id char(36) after id;
update tbl_labour_emonc_services set copy_id = uuid();
alter table tbl_labour_emonc_services drop foreign key if exists tbl_labour_emonc_services_patient_id_foreign;
alter table tbl_labour_emonc_services change column  patient_id patient_id char(36);
update tbl_labour_emonc_services set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_labour_emonc_services drop foreign key if exists tbl_labour_emonc_services_user_id_foreign;
alter table tbl_labour_emonc_services change column  user_id user_id char(36);
update tbl_labour_emonc_services set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_emonc_services drop foreign key if exists tbl_labour_emonc_services_facility_id_foreign;
alter table tbl_labour_emonc_services change column  facility_id facility_id char(36);
update tbl_labour_emonc_services set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_exemption_numbers add column if not exists copy_id char(36) after id;
update tbl_exemption_numbers set copy_id = uuid();
alter table tbl_exemption_numbers drop foreign key if exists tbl_exemption_numbers_patient_id_foreign;
alter table tbl_exemption_numbers change column  patient_id patient_id char(36);
update tbl_exemption_numbers set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_exemption_numbers drop foreign key if exists tbl_exemption_numbers_user_id_foreign;
alter table tbl_exemption_numbers change column  user_id user_id char(36);
update tbl_exemption_numbers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_exemption_numbers drop foreign key if exists tbl_exemption_numbers_facility_id_foreign;
alter table tbl_exemption_numbers change column  facility_id facility_id char(36);
update tbl_exemption_numbers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_wards add column if not exists copy_id char(36) after id;
update tbl_wards set copy_id = uuid();
alter table tbl_wards drop foreign key if exists tbl_wards_facility_id_foreign;
alter table tbl_wards change column  facility_id facility_id char(36);
update tbl_wards set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_important_investigations add column if not exists copy_id char(36) after id;
update tbl_important_investigations set copy_id = uuid();
alter table tbl_important_investigations drop foreign key if exists tbl_important_investigations_patient_id_foreign;
alter table tbl_important_investigations change column  patient_id patient_id char(36);
update tbl_important_investigations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_important_investigations drop foreign key if exists tbl_important_investigations_user_id_foreign;
alter table tbl_important_investigations change column  user_id user_id char(36);
update tbl_important_investigations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_important_investigations drop foreign key if exists tbl_important_investigations_facility_id_foreign;
alter table tbl_important_investigations change column  facility_id facility_id char(36);
update tbl_important_investigations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_pregnancy_ages add column if not exists copy_id char(36) after id;
update tbl_pregnancy_ages set copy_id = uuid();
alter table tbl_pregnancy_ages drop foreign key if exists tbl_pregnancy_ages_patient_id_foreign;
alter table tbl_pregnancy_ages change column  patient_id patient_id char(36);
update tbl_pregnancy_ages set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_pregnancy_ages drop foreign key if exists tbl_pregnancy_ages_user_id_foreign;
alter table tbl_pregnancy_ages change column  user_id user_id char(36);
update tbl_pregnancy_ages set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_pregnancy_ages drop foreign key if exists tbl_pregnancy_ages_facility_id_foreign;
alter table tbl_pregnancy_ages change column  facility_id facility_id char(36);
update tbl_pregnancy_ages set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_family_planning_pregnancy_histories add column if not exists copy_id char(36) after id;
update tbl_family_planning_pregnancy_histories set copy_id = uuid();
alter table tbl_family_planning_pregnancy_histories drop foreign key if exists tbl_family_planning_pregnancy_histories_patient_id_foreign;
alter table tbl_family_planning_pregnancy_histories change column  patient_id patient_id char(36);
update tbl_family_planning_pregnancy_histories set patient_id = (select copy_id from tbl_family_planning_registers where tbl_family_planning_registers.id =  patient_id limit 1);
alter table tbl_family_planning_pregnancy_histories drop foreign key if exists tbl_family_planning_pregnancy_histories_user_id_foreign;
alter table tbl_family_planning_pregnancy_histories change column  user_id user_id char(36);
update tbl_family_planning_pregnancy_histories set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_family_planning_pregnancy_histories drop foreign key if exists tbl_family_planning_pregnancy_histories_facility_id_foreign;
alter table tbl_family_planning_pregnancy_histories change column  facility_id facility_id char(36);
update tbl_family_planning_pregnancy_histories set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_surgery_family_socials add column if not exists copy_id char(36) after id;
update tbl_surgery_family_socials set copy_id = uuid();
alter table tbl_surgery_family_socials drop foreign key if exists tbl_surgery_family_socials_admission_id_foreign;
alter table tbl_surgery_family_socials change column  admission_id admission_id char(36);
update tbl_surgery_family_socials set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_surgery_family_socials drop foreign key if exists tbl_surgery_family_socials_nurse_id_foreign;
alter table tbl_surgery_family_socials change column  nurse_id nurse_id char(36);
update tbl_surgery_family_socials set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);
alter table tbl_surgery_family_socials drop foreign key if exists tbl_surgery_family_socials_request_id_foreign;
alter table tbl_surgery_family_socials change column  request_id request_id char(36);
update tbl_surgery_family_socials set request_id = (select copy_id from tbl_theatre_waits where tbl_theatre_waits.id =  request_id limit 1);

alter table tbl_anti_natal_pmtcts add column if not exists copy_id char(36) after id;
update tbl_anti_natal_pmtcts set copy_id = uuid();
alter table tbl_anti_natal_pmtcts drop foreign key if exists tbl_anti_natal_pmtcts_patient_id_foreign;
alter table tbl_anti_natal_pmtcts change column  patient_id patient_id char(36);
update tbl_anti_natal_pmtcts set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_anti_natal_pmtcts drop foreign key if exists tbl_anti_natal_pmtcts_user_id_foreign;
alter table tbl_anti_natal_pmtcts change column  user_id user_id char(36);
update tbl_anti_natal_pmtcts set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_pmtcts drop foreign key if exists tbl_anti_natal_pmtcts_facility_id_foreign;
alter table tbl_anti_natal_pmtcts change column  facility_id facility_id char(36);
update tbl_anti_natal_pmtcts set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_tb_patient_treatment_outputs add column if not exists copy_id char(36) after id;
update tbl_tb_patient_treatment_outputs set copy_id = uuid();
alter table tbl_tb_patient_treatment_outputs drop foreign key if exists tbl_tb_patient_treatment_outputs_client_id_foreign;
alter table tbl_tb_patient_treatment_outputs change column  client_id client_id char(36);
update tbl_tb_patient_treatment_outputs set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_tb_patient_treatment_outputs drop foreign key if exists tbl_tb_patient_treatment_outputs_user_id_foreign;
alter table tbl_tb_patient_treatment_outputs change column  user_id user_id char(36);
update tbl_tb_patient_treatment_outputs set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_tb_patient_treatment_outputs drop foreign key if exists tbl_tb_patient_treatment_outputs_facility_id_foreign;
alter table tbl_tb_patient_treatment_outputs change column  facility_id facility_id char(36);
update tbl_tb_patient_treatment_outputs set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_clinic_schedules add column if not exists copy_id char(36) after id;
update tbl_clinic_schedules set copy_id = uuid();
alter table tbl_clinic_schedules drop foreign key if exists tbl_clinic_schedules_user_id_foreign;
alter table tbl_clinic_schedules change column  user_id user_id char(36);
update tbl_clinic_schedules set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_clinic_schedules drop foreign key if exists tbl_clinic_schedules_facility_id_foreign;
alter table tbl_clinic_schedules change column  facility_id facility_id char(36);
update tbl_clinic_schedules set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_additional_medications add column if not exists copy_id char(36) after id;
update tbl_post_natal_additional_medications set copy_id = uuid();
alter table tbl_post_natal_additional_medications drop foreign key if exists tbl_post_natal_additional_medications_patient_id_foreign;
alter table tbl_post_natal_additional_medications change column  patient_id patient_id char(36);
update tbl_post_natal_additional_medications set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_post_natal_additional_medications drop foreign key if exists tbl_post_natal_additional_medications_user_id_foreign;
alter table tbl_post_natal_additional_medications change column  user_id user_id char(36);
update tbl_post_natal_additional_medications set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_additional_medications drop foreign key if exists tbl_post_natal_additional_medications_facility_id_foreign;
alter table tbl_post_natal_additional_medications change column  facility_id facility_id char(36);
update tbl_post_natal_additional_medications set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_delivery_child_arvs add column if not exists copy_id char(36) after id;
update tbl_labour_delivery_child_arvs set copy_id = uuid();
alter table tbl_labour_delivery_child_arvs drop foreign key if exists tbl_labour_delivery_child_arvs_patient_id_foreign;
alter table tbl_labour_delivery_child_arvs change column  patient_id patient_id char(36);
update tbl_labour_delivery_child_arvs set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_labour_delivery_child_arvs drop foreign key if exists tbl_labour_delivery_child_arvs_user_id_foreign;
alter table tbl_labour_delivery_child_arvs change column  user_id user_id char(36);
update tbl_labour_delivery_child_arvs set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_delivery_child_arvs drop foreign key if exists tbl_labour_delivery_child_arvs_facility_id_foreign;
alter table tbl_labour_delivery_child_arvs change column  facility_id facility_id char(36);
update tbl_labour_delivery_child_arvs set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_output_observations add column if not exists copy_id char(36) after id;
update tbl_output_observations set copy_id = uuid();
alter table tbl_output_observations drop foreign key if exists tbl_output_observations_admission_id_foreign;
alter table tbl_output_observations change column  admission_id admission_id char(36);
update tbl_output_observations set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_fplanning_pitcs add column if not exists copy_id char(36) after id;
update tbl_fplanning_pitcs set copy_id = uuid();
alter table tbl_fplanning_pitcs drop foreign key if exists tbl_fplanning_pitcs_client_id_foreign;
alter table tbl_fplanning_pitcs change column  client_id client_id char(36);
update tbl_fplanning_pitcs set client_id = (select copy_id from tbl_family_planning_registers where tbl_family_planning_registers.id =  client_id limit 1);
alter table tbl_fplanning_pitcs drop foreign key if exists tbl_fplanning_pitcs_user_id_foreign;
alter table tbl_fplanning_pitcs change column  user_id user_id char(36);
update tbl_fplanning_pitcs set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_fplanning_pitcs drop foreign key if exists tbl_fplanning_pitcs_facility_id_foreign;
alter table tbl_fplanning_pitcs change column  facility_id facility_id char(36);
update tbl_fplanning_pitcs set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_tb_vvu_services add column if not exists copy_id char(36) after id;
update tbl_tb_vvu_services set copy_id = uuid();
alter table tbl_tb_vvu_services drop foreign key if exists tbl_tb_vvu_services_client_id_foreign;
alter table tbl_tb_vvu_services change column  client_id client_id char(36);
update tbl_tb_vvu_services set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_tb_vvu_services drop foreign key if exists tbl_tb_vvu_services_user_id_foreign;
alter table tbl_tb_vvu_services change column  user_id user_id char(36);
update tbl_tb_vvu_services set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_tb_vvu_services drop foreign key if exists tbl_tb_vvu_services_facility_id_foreign;
alter table tbl_tb_vvu_services change column  facility_id facility_id char(36);
update tbl_tb_vvu_services set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_child_attendances add column if not exists copy_id char(36) after id;
update tbl_post_natal_child_attendances set copy_id = uuid();
alter table tbl_post_natal_child_attendances drop foreign key if exists tbl_post_natal_child_attendances_patient_id_foreign;
alter table tbl_post_natal_child_attendances change column  patient_id patient_id char(36);
update tbl_post_natal_child_attendances set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_post_natal_child_attendances drop foreign key if exists tbl_post_natal_child_attendances_user_id_foreign;
alter table tbl_post_natal_child_attendances change column  user_id user_id char(36);
update tbl_post_natal_child_attendances set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_child_attendances drop foreign key if exists tbl_post_natal_child_attendances_facility_id_foreign;
alter table tbl_post_natal_child_attendances change column  facility_id facility_id char(36);
update tbl_post_natal_child_attendances set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_delivery_mother_dispositions add column if not exists copy_id char(36) after id;
update tbl_labour_delivery_mother_dispositions set copy_id = uuid();
alter table tbl_labour_delivery_mother_dispositions drop foreign key if exists tbl_labour_delivery_mother_dispositions_patient_id_foreign;
alter table tbl_labour_delivery_mother_dispositions change column  patient_id patient_id char(36);
update tbl_labour_delivery_mother_dispositions set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_labour_delivery_mother_dispositions drop foreign key if exists tbl_labour_delivery_mother_dispositions_user_id_foreign;
alter table tbl_labour_delivery_mother_dispositions change column  user_id user_id char(36);
update tbl_labour_delivery_mother_dispositions set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_delivery_mother_dispositions drop foreign key if exists tbl_labour_delivery_mother_dispositions_facility_id_foreign;
alter table tbl_labour_delivery_mother_dispositions change column  facility_id facility_id char(36);
update tbl_labour_delivery_mother_dispositions set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_vital_signs add column if not exists copy_id char(36) after id;
update tbl_vital_signs set copy_id = uuid();
alter table tbl_vital_signs drop foreign key if exists tbl_vital_signs_registered_by_foreign;
alter table tbl_vital_signs change column  registered_by registered_by char(36);
update tbl_vital_signs set registered_by = (select copy_id from users where users.id =  registered_by limit 1);
alter table tbl_vital_signs drop foreign key if exists tbl_vital_signs_visiting_id_foreign;
alter table tbl_vital_signs change column  visiting_id visiting_id char(36);
update tbl_vital_signs set visiting_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visiting_id limit 1);

alter table tbl_ctc_patient_visits add column if not exists copy_id char(36) after id;
update tbl_ctc_patient_visits set copy_id = uuid();
alter table tbl_ctc_patient_visits drop foreign key if exists tbl_ctc_patient_visits_visit_date_id_foreign;
alter table tbl_ctc_patient_visits change column  visit_date_id visit_date_id char(36);
update tbl_ctc_patient_visits set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_ctc_patient_visits drop foreign key if exists tbl_ctc_patient_visits_patient_id_foreign;
alter table tbl_ctc_patient_visits change column  patient_id patient_id char(36);
update tbl_ctc_patient_visits set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_ctc_patient_visits drop foreign key if exists tbl_ctc_patient_visits_user_id_foreign;
alter table tbl_ctc_patient_visits change column  user_id user_id char(36);
update tbl_ctc_patient_visits set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_ctc_patient_visits drop foreign key if exists tbl_ctc_patient_visits_weight_sign_value_id_foreign;
alter table tbl_ctc_patient_visits change column  weight_sign_value_id weight_sign_value_id char(36);
update tbl_ctc_patient_visits set weight_sign_value_id = (select copy_id from tbl_vital_signs where tbl_vital_signs.id =  weight_sign_value_id limit 1);
alter table tbl_ctc_patient_visits drop foreign key if exists tbl_ctc_patient_visits_length_sign_value_id_foreign;
alter table tbl_ctc_patient_visits change column  length_sign_value_id length_sign_value_id char(36);
update tbl_ctc_patient_visits set length_sign_value_id = (select copy_id from tbl_vital_signs where tbl_vital_signs.id =  length_sign_value_id limit 1);

alter table tbl_past_eye_records add column if not exists copy_id char(36) after id;
update tbl_past_eye_records set copy_id = uuid();
alter table tbl_past_eye_records drop foreign key if exists tbl_past_eye_records_user_id_foreign;
alter table tbl_past_eye_records change column  user_id user_id char(36);
update tbl_past_eye_records set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_past_eye_records drop foreign key if exists tbl_past_eye_records_patient_id_foreign;
alter table tbl_past_eye_records change column  patient_id patient_id char(36);
update tbl_past_eye_records set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_past_eye_records drop foreign key if exists tbl_past_eye_records_visit_date_id_foreign;
alter table tbl_past_eye_records change column  visit_date_id visit_date_id char(36);
update tbl_past_eye_records set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_past_eye_records change column  past_medical_history past_medical_history text;
alter table tbl_past_eye_records change column  past_ocular_history past_ocular_history text;

alter table tbl_fplanning_stomach_leg_investigations add column if not exists copy_id char(36) after id;
update tbl_fplanning_stomach_leg_investigations set copy_id = uuid();
alter table tbl_fplanning_stomach_leg_investigations drop foreign key if exists tbl_fplanning_stomach_leg_investigations_user_id_foreign;
alter table tbl_fplanning_stomach_leg_investigations change column  user_id user_id char(36);
update tbl_fplanning_stomach_leg_investigations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_fplanning_stomach_leg_investigations drop foreign key if exists tbl_fplanning_stomach_leg_investigations_client_id_foreign;
alter table tbl_fplanning_stomach_leg_investigations change column  client_id client_id char(36);
update tbl_fplanning_stomach_leg_investigations set client_id = (select copy_id from tbl_family_planning_method_registers where tbl_family_planning_method_registers.id =  client_id limit 1);
alter table tbl_fplanning_stomach_leg_investigations drop foreign key if exists tbl_fplanning_stomach_leg_investigations_facility_id_foreign;
alter table tbl_fplanning_stomach_leg_investigations change column  facility_id facility_id char(36);
update tbl_fplanning_stomach_leg_investigations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_familiy_plannings add column if not exists copy_id char(36) after id;
update tbl_post_natal_familiy_plannings set copy_id = uuid();
alter table tbl_post_natal_familiy_plannings drop foreign key if exists tbl_post_natal_familiy_plannings_user_id_foreign;
alter table tbl_post_natal_familiy_plannings change column  user_id user_id char(36);
update tbl_post_natal_familiy_plannings set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_familiy_plannings drop foreign key if exists tbl_post_natal_familiy_plannings_patient_id_foreign;
alter table tbl_post_natal_familiy_plannings change column  patient_id patient_id char(36);
update tbl_post_natal_familiy_plannings set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_post_natal_familiy_plannings drop foreign key if exists tbl_post_natal_familiy_plannings_facility_id_foreign;
alter table tbl_post_natal_familiy_plannings change column  facility_id facility_id char(36);
update tbl_post_natal_familiy_plannings set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_pmtcts add column if not exists copy_id char(36) after id;
update tbl_post_natal_pmtcts set copy_id = uuid();
alter table tbl_post_natal_pmtcts drop foreign key if exists tbl_post_natal_pmtcts_user_id_foreign;
alter table tbl_post_natal_pmtcts change column  user_id user_id char(36);
update tbl_post_natal_pmtcts set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_pmtcts drop foreign key if exists tbl_post_natal_pmtcts_patient_id_foreign;
alter table tbl_post_natal_pmtcts change column  patient_id patient_id char(36);
update tbl_post_natal_pmtcts set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_post_natal_pmtcts drop foreign key if exists tbl_post_natal_pmtcts_facility_id_foreign;
alter table tbl_post_natal_pmtcts change column  facility_id facility_id char(36);
update tbl_post_natal_pmtcts set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_newborns add column if not exists copy_id char(36) after id;
update tbl_labour_newborns set copy_id = uuid();
alter table tbl_labour_newborns drop foreign key if exists tbl_labour_newborns_user_id_foreign;
alter table tbl_labour_newborns change column  user_id user_id char(36);
update tbl_labour_newborns set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_newborns drop foreign key if exists tbl_labour_newborns_client_id_foreign;
alter table tbl_labour_newborns change column  client_id client_id char(36);
update tbl_labour_newborns set client_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  client_id limit 1);
alter table tbl_labour_newborns drop foreign key if exists tbl_labour_newborns_facility_id_foreign;
alter table tbl_labour_newborns change column  facility_id facility_id char(36);
update tbl_labour_newborns set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_icu_entries add column if not exists copy_id char(36) after id;
update tbl_icu_entries set copy_id = uuid();
alter table tbl_icu_entries drop foreign key if exists tbl_icu_entries_doctor_id_foreign;
alter table tbl_icu_entries change column  doctor_id doctor_id char(36);
update tbl_icu_entries set doctor_id = (select copy_id from users where users.id =  doctor_id limit 1);
alter table tbl_icu_entries drop foreign key if exists tbl_icu_entries_admission_id_foreign;
alter table tbl_icu_entries change column  admission_id admission_id char(36);
update tbl_icu_entries set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_ctc_patient_addresses add column if not exists copy_id char(36) after id;
update tbl_ctc_patient_addresses set copy_id = uuid();
alter table tbl_ctc_patient_addresses drop foreign key if exists tbl_ctc_patient_addresses_user_id_foreign;
alter table tbl_ctc_patient_addresses change column  user_id user_id char(36);
update tbl_ctc_patient_addresses set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_ctc_patient_addresses drop foreign key if exists tbl_ctc_patient_addresses_patient_id_foreign;
alter table tbl_ctc_patient_addresses change column  patient_id patient_id char(36);
update tbl_ctc_patient_addresses set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);

alter table tbl_post_natal_womb_statuses add column if not exists copy_id char(36) after id;
update tbl_post_natal_womb_statuses set copy_id = uuid();
alter table tbl_post_natal_womb_statuses drop foreign key if exists tbl_post_natal_womb_statuses_user_id_foreign;
alter table tbl_post_natal_womb_statuses change column  user_id user_id char(36);
update tbl_post_natal_womb_statuses set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_womb_statuses drop foreign key if exists tbl_post_natal_womb_statuses_patient_id_foreign;
alter table tbl_post_natal_womb_statuses change column  patient_id patient_id char(36);
update tbl_post_natal_womb_statuses set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_post_natal_womb_statuses drop foreign key if exists tbl_post_natal_womb_statuses_facility_id_foreign;
alter table tbl_post_natal_womb_statuses change column  facility_id facility_id char(36);
update tbl_post_natal_womb_statuses set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_anaethetic_records add column if not exists copy_id char(36) after id;
update tbl_anaethetic_records set copy_id = uuid();
alter table tbl_anaethetic_records drop foreign key if exists tbl_anaethetic_records_nurse_id_foreign;
alter table tbl_anaethetic_records change column  nurse_id nurse_id char(36);
update tbl_anaethetic_records set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);
alter table tbl_anaethetic_records drop foreign key if exists tbl_anaethetic_records_request_id_foreign;
alter table tbl_anaethetic_records change column  request_id request_id char(36);
update tbl_anaethetic_records set request_id = (select copy_id from tbl_theatre_waits where tbl_theatre_waits.id =  request_id limit 1);
alter table tbl_anaethetic_records drop foreign key if exists tbl_anaethetic_records_admission_id_foreign;
alter table tbl_anaethetic_records change column  admission_id admission_id char(36);
update tbl_anaethetic_records set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_intra_opconditions add column if not exists copy_id char(36) after id;
update tbl_intra_opconditions set copy_id = uuid();
alter table tbl_intra_opconditions drop foreign key if exists tbl_intra_opconditions_nurse_id_foreign;
alter table tbl_intra_opconditions change column  nurse_id nurse_id char(36);
update tbl_intra_opconditions set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);
alter table tbl_intra_opconditions drop foreign key if exists tbl_intra_opconditions_request_id_foreign;
alter table tbl_intra_opconditions change column  request_id request_id char(36);
update tbl_intra_opconditions set request_id = (select copy_id from tbl_theatre_waits where tbl_theatre_waits.id =  request_id limit 1);
alter table tbl_intra_opconditions drop foreign key if exists tbl_intra_opconditions_admission_id_foreign;
alter table tbl_intra_opconditions change column  admission_id admission_id char(36);
update tbl_intra_opconditions set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_anti_natal_followups add column if not exists copy_id char(36) after id;
update tbl_anti_natal_followups set copy_id = uuid();
alter table tbl_anti_natal_followups drop foreign key if exists tbl_anti_natal_followups_user_id_foreign;
alter table tbl_anti_natal_followups change column  user_id user_id char(36);
update tbl_anti_natal_followups set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_followups drop foreign key if exists tbl_anti_natal_followups_client_id_foreign;
alter table tbl_anti_natal_followups change column  client_id client_id char(36);
update tbl_anti_natal_followups set client_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  client_id limit 1);
alter table tbl_anti_natal_followups drop foreign key if exists tbl_anti_natal_followups_facility_id_foreign;
alter table tbl_anti_natal_followups change column  facility_id facility_id char(36);
update tbl_anti_natal_followups set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_pediatric_pre_natals add column if not exists copy_id char(36) after id;
update tbl_pediatric_pre_natals set copy_id = uuid();
alter table tbl_pediatric_pre_natals drop foreign key if exists tbl_pediatric_pre_natals_user_id_foreign;
alter table tbl_pediatric_pre_natals change column  user_id user_id char(36);
update tbl_pediatric_pre_natals set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_pediatric_pre_natals drop foreign key if exists tbl_pediatric_pre_natals_client_id_foreign;
alter table tbl_pediatric_pre_natals change column  client_id client_id char(36);
update tbl_pediatric_pre_natals set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_pediatric_pre_natals drop foreign key if exists tbl_pediatric_pre_natals_facility_id_foreign;
alter table tbl_pediatric_pre_natals change column  facility_id facility_id char(36);
update tbl_pediatric_pre_natals set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_anti_natal_partiner_pmtcts add column if not exists copy_id char(36) after id;
update tbl_anti_natal_partiner_pmtcts set copy_id = uuid();
alter table tbl_anti_natal_partiner_pmtcts drop foreign key if exists tbl_anti_natal_partiner_pmtcts_user_id_foreign;
alter table tbl_anti_natal_partiner_pmtcts change column  user_id user_id char(36);
update tbl_anti_natal_partiner_pmtcts set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_partiner_pmtcts drop foreign key if exists tbl_anti_natal_partiner_pmtcts_patient_id_foreign;
alter table tbl_anti_natal_partiner_pmtcts change column  patient_id patient_id char(36);
update tbl_anti_natal_partiner_pmtcts set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_anti_natal_partiner_pmtcts drop foreign key if exists tbl_anti_natal_partiner_pmtcts_facility_id_foreign;
alter table tbl_anti_natal_partiner_pmtcts change column  facility_id facility_id char(36);
update tbl_anti_natal_partiner_pmtcts set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_admissions add column if not exists copy_id char(36) after id;
update tbl_labour_admissions set copy_id = uuid();
alter table tbl_labour_admissions drop foreign key if exists tbl_labour_admissions_user_id_foreign;
alter table tbl_labour_admissions change column  user_id user_id char(36);
update tbl_labour_admissions set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_admissions drop foreign key if exists tbl_labour_admissions_client_id_foreign;
alter table tbl_labour_admissions change column  client_id client_id char(36);
update tbl_labour_admissions set client_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  client_id limit 1);
alter table tbl_labour_admissions drop foreign key if exists tbl_labour_admissions_facility_id_foreign;
alter table tbl_labour_admissions change column  facility_id facility_id char(36);
update tbl_labour_admissions set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_emergence_visits add column if not exists copy_id char(36) after id;
update tbl_emergence_visits set copy_id = uuid();
alter table tbl_emergence_visits drop foreign key if exists tbl_emergence_visits_registered_by_foreign;
alter table tbl_emergence_visits change column  registered_by registered_by char(36);
update tbl_emergence_visits set registered_by = (select copy_id from users where users.id =  registered_by limit 1);
alter table tbl_emergence_visits drop foreign key if exists tbl_emergence_visits_patient_id_foreign;
alter table tbl_emergence_visits change column  patient_id patient_id char(36);
update tbl_emergence_visits set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_emergence_visits drop foreign key if exists tbl_emergence_visits_facility_id_foreign;
alter table tbl_emergence_visits change column  facility_id facility_id char(36);
update tbl_emergence_visits set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_fplanning_cervix_cancer_investigations add column if not exists copy_id char(36) after id;
update tbl_fplanning_cervix_cancer_investigations set copy_id = uuid();
alter table tbl_fplanning_cervix_cancer_investigations drop foreign key if exists tbl_fplanning_cervix_cancer_investigations_user_id_foreign;
alter table tbl_fplanning_cervix_cancer_investigations change column  user_id user_id char(36);
update tbl_fplanning_cervix_cancer_investigations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_fplanning_cervix_cancer_investigations drop foreign key if exists tbl_fplanning_cervix_cancer_investigations_patient_id_foreign;
alter table tbl_fplanning_cervix_cancer_investigations change column  patient_id patient_id char(36);
update tbl_fplanning_cervix_cancer_investigations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_fplanning_cervix_cancer_investigations drop foreign key if exists tbl_fplanning_cervix_cancer_investigations_facility_id_foreign;
alter table tbl_fplanning_cervix_cancer_investigations change column  facility_id facility_id char(36);
update tbl_fplanning_cervix_cancer_investigations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_tb_sputam_test_followups add column if not exists copy_id char(36) after id;
update tbl_tb_sputam_test_followups set copy_id = uuid();
alter table tbl_tb_sputam_test_followups drop foreign key if exists tbl_tb_sputam_test_followups_user_id_foreign;
alter table tbl_tb_sputam_test_followups change column  user_id user_id char(36);
update tbl_tb_sputam_test_followups set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_tb_sputam_test_followups drop foreign key if exists tbl_tb_sputam_test_followups_client_id_foreign;
alter table tbl_tb_sputam_test_followups change column  client_id client_id char(36);
update tbl_tb_sputam_test_followups set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_tb_sputam_test_followups drop foreign key if exists tbl_tb_sputam_test_followups_facility_id_foreign;
alter table tbl_tb_sputam_test_followups change column  facility_id facility_id char(36);
update tbl_tb_sputam_test_followups set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_breast_statuses add column if not exists copy_id char(36) after id;
update tbl_post_natal_breast_statuses set copy_id = uuid();
alter table tbl_post_natal_breast_statuses drop foreign key if exists tbl_post_natal_breast_statuses_user_id_foreign;
alter table tbl_post_natal_breast_statuses change column  user_id user_id char(36);
update tbl_post_natal_breast_statuses set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_breast_statuses drop foreign key if exists tbl_post_natal_breast_statuses_patient_id_foreign;
alter table tbl_post_natal_breast_statuses change column  patient_id patient_id char(36);
update tbl_post_natal_breast_statuses set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_post_natal_breast_statuses drop foreign key if exists tbl_post_natal_breast_statuses_facility_id_foreign;
alter table tbl_post_natal_breast_statuses change column  facility_id facility_id char(36);
update tbl_post_natal_breast_statuses set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_delivery_complications add column if not exists copy_id char(36) after id;
update tbl_labour_delivery_complications set copy_id = uuid();
alter table tbl_labour_delivery_complications drop foreign key if exists tbl_labour_delivery_complications_user_id_foreign;
alter table tbl_labour_delivery_complications change column  user_id user_id char(36);
update tbl_labour_delivery_complications set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_delivery_complications drop foreign key if exists tbl_labour_delivery_complications_patient_id_foreign;
alter table tbl_labour_delivery_complications change column  patient_id patient_id char(36);
update tbl_labour_delivery_complications set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_labour_delivery_complications drop foreign key if exists tbl_labour_delivery_complications_facility_id_foreign;
alter table tbl_labour_delivery_complications change column  facility_id facility_id char(36);
update tbl_labour_delivery_complications set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_epsodes add column if not exists copy_id char(36) after id;
update tbl_epsodes set copy_id = uuid();
alter table tbl_epsodes drop foreign key if exists tbl_epsodes_user_id_foreign;
alter table tbl_epsodes change column  user_id user_id char(36);
update tbl_epsodes set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_epsodes drop foreign key if exists tbl_epsodes_patient_id_foreign;
alter table tbl_epsodes change column  patient_id patient_id char(36);
update tbl_epsodes set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);

alter table tbl_partner_lab_tests add column if not exists copy_id char(36) after id;
update tbl_partner_lab_tests set copy_id = uuid();
alter table tbl_partner_lab_tests drop foreign key if exists tbl_partner_lab_tests_user_id_foreign;
alter table tbl_partner_lab_tests change column  user_id user_id char(36);
update tbl_partner_lab_tests set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_partner_lab_tests drop foreign key if exists tbl_partner_lab_tests_client_id_foreign;
alter table tbl_partner_lab_tests change column  client_id client_id char(36);
update tbl_partner_lab_tests set client_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  client_id limit 1);
alter table tbl_partner_lab_tests drop foreign key if exists tbl_partner_lab_tests_facility_id_foreign;
alter table tbl_partner_lab_tests change column  facility_id facility_id char(36);
update tbl_partner_lab_tests set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_fplanning_previous_pregnancy_results add column if not exists copy_id char(36) after id;
update tbl_fplanning_previous_pregnancy_results set copy_id = uuid();
alter table tbl_fplanning_previous_pregnancy_results drop foreign key if exists tbl_fplanning_previous_pregnancy_results_user_id_foreign;
alter table tbl_fplanning_previous_pregnancy_results change column  user_id user_id char(36);
update tbl_fplanning_previous_pregnancy_results set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_fplanning_previous_pregnancy_results drop foreign key if exists tbl_fplanning_previous_pregnancy_results_client_id_foreign;
alter table tbl_fplanning_previous_pregnancy_results change column  client_id client_id char(36);
update tbl_fplanning_previous_pregnancy_results set client_id = (select copy_id from tbl_family_planning_registers where tbl_family_planning_registers.id =  client_id limit 1);
alter table tbl_fplanning_previous_pregnancy_results drop foreign key if exists tbl_fplanning_previous_pregnancy_results_facility_id_foreign;
alter table tbl_fplanning_previous_pregnancy_results change column  facility_id facility_id char(36);
update tbl_fplanning_previous_pregnancy_results set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_child_investigations add column if not exists copy_id char(36) after id;
update tbl_post_natal_child_investigations set copy_id = uuid();
alter table tbl_post_natal_child_investigations drop foreign key if exists tbl_post_natal_child_investigations_user_id_foreign;
alter table tbl_post_natal_child_investigations change column  user_id user_id char(36);
update tbl_post_natal_child_investigations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_child_investigations drop foreign key if exists tbl_post_natal_child_investigations_patient_id_foreign;
alter table tbl_post_natal_child_investigations change column  patient_id patient_id char(36);
update tbl_post_natal_child_investigations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_post_natal_child_investigations drop foreign key if exists tbl_post_natal_child_investigations_facility_id_foreign;
alter table tbl_post_natal_child_investigations change column  facility_id facility_id char(36);
update tbl_post_natal_child_investigations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_fgms add column if not exists copy_id char(36) after id;
update tbl_labour_fgms set copy_id = uuid();
alter table tbl_labour_fgms drop foreign key if exists tbl_labour_fgms_user_id_foreign;
alter table tbl_labour_fgms change column  user_id user_id char(36);
update tbl_labour_fgms set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_fgms drop foreign key if exists tbl_labour_fgms_patient_id_foreign;
alter table tbl_labour_fgms change column  patient_id patient_id char(36);
update tbl_labour_fgms set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_labour_fgms drop foreign key if exists tbl_labour_fgms_facility_id_foreign;
alter table tbl_labour_fgms change column  facility_id facility_id char(36);
update tbl_labour_fgms set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_tt_given_vaccinations add column if not exists copy_id char(36) after id;
update tbl_post_natal_tt_given_vaccinations set copy_id = uuid();
alter table tbl_post_natal_tt_given_vaccinations drop foreign key if exists tbl_post_natal_tt_given_vaccinations_user_id_foreign;
alter table tbl_post_natal_tt_given_vaccinations change column  user_id user_id char(36);
update tbl_post_natal_tt_given_vaccinations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_tt_given_vaccinations drop foreign key if exists tbl_post_natal_tt_given_vaccinations_patient_id_foreign;
alter table tbl_post_natal_tt_given_vaccinations change column  patient_id patient_id char(36);
update tbl_post_natal_tt_given_vaccinations set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_post_natal_tt_given_vaccinations drop foreign key if exists tbl_post_natal_tt_given_vaccinations_facility_id_foreign;
alter table tbl_post_natal_tt_given_vaccinations change column  facility_id facility_id char(36);
update tbl_post_natal_tt_given_vaccinations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_registers add column if not exists copy_id char(36) after id;
update tbl_labour_registers set copy_id = uuid();
alter table tbl_labour_registers drop foreign key if exists tbl_labour_registers_user_id_foreign;
alter table tbl_labour_registers change column  user_id user_id char(36);
update tbl_labour_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_registers drop foreign key if exists tbl_labour_registers_patient_id_foreign;
alter table tbl_labour_registers change column  patient_id patient_id char(36);
update tbl_labour_registers set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_labour_registers drop foreign key if exists tbl_labour_registers_facility_id_foreign;
alter table tbl_labour_registers change column  facility_id facility_id char(36);
update tbl_labour_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_status_anaesthetics add column if not exists copy_id char(36) after id;
update tbl_status_anaesthetics set copy_id = uuid();
alter table tbl_status_anaesthetics drop foreign key if exists tbl_status_anaesthetics_admission_id_foreign;
alter table tbl_status_anaesthetics change column  admission_id admission_id char(36);
update tbl_status_anaesthetics set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_status_anaesthetics drop foreign key if exists tbl_status_anaesthetics_request_id_foreign;
alter table tbl_status_anaesthetics change column  request_id request_id char(36);
update tbl_status_anaesthetics set request_id = (select copy_id from tbl_theatre_waits where tbl_theatre_waits.id =  request_id limit 1);
alter table tbl_status_anaesthetics drop foreign key if exists tbl_status_anaesthetics_nurse_id_foreign;
alter table tbl_status_anaesthetics change column  nurse_id nurse_id char(36);
update tbl_status_anaesthetics set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);

alter table tbl_family_planning_attendance_registers add column if not exists copy_id char(36) after id;
update tbl_family_planning_attendance_registers set copy_id = uuid();
alter table tbl_family_planning_attendance_registers drop foreign key if exists tbl_family_planning_attendance_registers_user_id_foreign;
alter table tbl_family_planning_attendance_registers change column  user_id user_id char(36);
update tbl_family_planning_attendance_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_family_planning_attendance_registers drop foreign key if exists tbl_family_planning_attendance_registers_patient_id_foreign;
alter table tbl_family_planning_attendance_registers change column  patient_id patient_id char(36);
update tbl_family_planning_attendance_registers set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_family_planning_attendance_registers drop foreign key if exists tbl_family_planning_attendance_registers_facility_id_foreign;
alter table tbl_family_planning_attendance_registers change column  facility_id facility_id char(36);
update tbl_family_planning_attendance_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_rch_general_recomendations add column if not exists copy_id char(36) after id;
update tbl_rch_general_recomendations set copy_id = uuid();
alter table tbl_rch_general_recomendations drop foreign key if exists tbl_rch_general_recomendations_user_id_foreign;
alter table tbl_rch_general_recomendations change column  user_id user_id char(36);
update tbl_rch_general_recomendations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_rch_general_recomendations drop foreign key if exists tbl_rch_general_recomendations_patient_id_foreign;
alter table tbl_rch_general_recomendations change column  patient_id patient_id char(36);
update tbl_rch_general_recomendations set patient_id = (select copy_id from tbl_family_planning_registers where tbl_family_planning_registers.id =  patient_id limit 1);
alter table tbl_rch_general_recomendations drop foreign key if exists tbl_rch_general_recomendations_facility_id_foreign;
alter table tbl_rch_general_recomendations change column  facility_id facility_id char(36);
update tbl_rch_general_recomendations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_pediatric_nutritionals add column if not exists copy_id char(36) after id;
update tbl_pediatric_nutritionals set copy_id = uuid();
alter table tbl_pediatric_nutritionals drop foreign key if exists tbl_pediatric_nutritionals_user_id_foreign;
alter table tbl_pediatric_nutritionals change column  user_id user_id char(36);
update tbl_pediatric_nutritionals set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_pediatric_nutritionals drop foreign key if exists tbl_pediatric_nutritionals_client_id_foreign;
alter table tbl_pediatric_nutritionals change column  client_id client_id char(36);
update tbl_pediatric_nutritionals set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_pediatric_nutritionals drop foreign key if exists tbl_pediatric_nutritionals_facility_id_foreign;
alter table tbl_pediatric_nutritionals change column  facility_id facility_id char(36);
update tbl_pediatric_nutritionals set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_anti_natal_lab_tests add column if not exists copy_id char(36) after id;
update tbl_anti_natal_lab_tests set copy_id = uuid();
alter table tbl_anti_natal_lab_tests drop foreign key if exists tbl_anti_natal_lab_tests_user_id_foreign;
alter table tbl_anti_natal_lab_tests change column  user_id user_id char(36);
update tbl_anti_natal_lab_tests set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_lab_tests drop foreign key if exists tbl_anti_natal_lab_tests_client_id_foreign;
alter table tbl_anti_natal_lab_tests change column  client_id client_id char(36);
update tbl_anti_natal_lab_tests set client_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  client_id limit 1);
alter table tbl_anti_natal_lab_tests drop foreign key if exists tbl_anti_natal_lab_tests_facility_id_foreign;
alter table tbl_anti_natal_lab_tests change column  facility_id facility_id char(36);
update tbl_anti_natal_lab_tests set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_family_planning_previous_healths add column if not exists copy_id char(36) after id;
update tbl_family_planning_previous_healths set copy_id = uuid();
alter table tbl_family_planning_previous_healths drop foreign key if exists tbl_family_planning_previous_healths_user_id_foreign;
alter table tbl_family_planning_previous_healths change column  user_id user_id char(36);
update tbl_family_planning_previous_healths set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_family_planning_previous_healths drop foreign key if exists tbl_family_planning_previous_healths_client_id_foreign;
alter table tbl_family_planning_previous_healths change column  client_id client_id char(36);
update tbl_family_planning_previous_healths set client_id = (select copy_id from tbl_family_planning_registers where tbl_family_planning_registers.id =  client_id limit 1);
alter table tbl_family_planning_previous_healths drop foreign key if exists tbl_family_planning_previous_healths_facility_id_foreign;
alter table tbl_family_planning_previous_healths change column  facility_id facility_id char(36);
update tbl_family_planning_previous_healths set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_surgery_histories add column if not exists copy_id char(36) after id;
update tbl_surgery_histories set copy_id = uuid();
alter table tbl_surgery_histories drop foreign key if exists tbl_surgery_histories_admission_id_foreign;
alter table tbl_surgery_histories change column  admission_id admission_id char(36);
update tbl_surgery_histories set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_surgery_histories drop foreign key if exists tbl_surgery_histories_request_id_foreign;
alter table tbl_surgery_histories change column  request_id request_id char(36);
update tbl_surgery_histories set request_id = (select copy_id from tbl_theatre_waits where tbl_theatre_waits.id =  request_id limit 1);
alter table tbl_surgery_histories drop foreign key if exists tbl_surgery_histories_nurse_id_foreign;
alter table tbl_surgery_histories change column  nurse_id nurse_id char(36);
update tbl_surgery_histories set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);

alter table tbl_post_natal_attendances add column if not exists copy_id char(36) after id;
update tbl_post_natal_attendances set copy_id = uuid();
alter table tbl_post_natal_attendances drop foreign key if exists tbl_post_natal_attendances_user_id_foreign;
alter table tbl_post_natal_attendances change column  user_id user_id char(36);
update tbl_post_natal_attendances set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_attendances drop foreign key if exists tbl_post_natal_attendances_patient_id_foreign;
alter table tbl_post_natal_attendances change column  patient_id patient_id char(36);
update tbl_post_natal_attendances set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_post_natal_attendances drop foreign key if exists tbl_post_natal_attendances_facility_id_foreign;
alter table tbl_post_natal_attendances change column  facility_id facility_id char(36);
update tbl_post_natal_attendances set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_delivery_child_dispositions add column if not exists copy_id char(36) after id;
update tbl_post_natal_attendances set copy_id = uuid();
alter table tbl_post_natal_attendances drop foreign key if exists tbl_post_natal_attendances_user_id_foreign;
alter table tbl_post_natal_attendances change column  user_id user_id char(36);
update tbl_post_natal_attendances set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_attendances drop foreign key if exists tbl_post_natal_attendances_patient_id_foreign;
alter table tbl_post_natal_attendances change column  patient_id patient_id char(36);
update tbl_post_natal_attendances set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_post_natal_attendances drop foreign key if exists tbl_post_natal_attendances_facility_id_foreign;
alter table tbl_post_natal_attendances change column  facility_id facility_id char(36);
update tbl_post_natal_attendances set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_fplanning_placenta_cancer_investigations add column if not exists copy_id char(36) after id;
update tbl_family_planning_previous_healths set copy_id = uuid();
alter table tbl_family_planning_previous_healths drop foreign key if exists tbl_family_planning_previous_healths_user_id_foreign;
alter table tbl_family_planning_previous_healths change column  user_id user_id char(36);
update tbl_family_planning_previous_healths set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_family_planning_previous_healths drop foreign key if exists tbl_family_planning_previous_healths_client_id_foreign;
alter table tbl_family_planning_previous_healths change column  client_id client_id char(36);
update tbl_family_planning_previous_healths set client_id = (select copy_id from tbl_family_planning_registers where tbl_family_planning_registers.id =  client_id limit 1);
alter table tbl_family_planning_previous_healths drop foreign key if exists tbl_family_planning_previous_healths_facility_id_foreign;
alter table tbl_family_planning_previous_healths change column  facility_id facility_id char(36);
update tbl_family_planning_previous_healths set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_child_feedings add column if not exists copy_id char(36) after id;
update tbl_post_natal_child_feedings set copy_id = uuid();
alter table tbl_post_natal_child_feedings drop foreign key if exists tbl_post_natal_child_feedings_user_id_foreign;
alter table tbl_post_natal_child_feedings change column  user_id user_id char(36);
update tbl_post_natal_child_feedings set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_child_feedings drop foreign key if exists tbl_post_natal_child_feedings_patient_id_foreign;
alter table tbl_post_natal_child_feedings change column  patient_id patient_id char(36);
update tbl_post_natal_child_feedings set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_post_natal_child_feedings drop foreign key if exists tbl_post_natal_child_feedings_facility_id_foreign;
alter table tbl_post_natal_child_feedings change column  facility_id facility_id char(36);
update tbl_post_natal_child_feedings set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_delivery_vvu_results add column if not exists copy_id char(36) after id;
update tbl_labour_delivery_vvu_results set copy_id = uuid();
alter table tbl_labour_delivery_vvu_results drop foreign key if exists tbl_labour_delivery_vvu_results_user_id_foreign;
alter table tbl_labour_delivery_vvu_results change column  user_id user_id char(36);
update tbl_labour_delivery_vvu_results set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_delivery_vvu_results drop foreign key if exists tbl_labour_delivery_vvu_results_patient_id_foreign;
alter table tbl_labour_delivery_vvu_results change column  patient_id patient_id char(36);
update tbl_labour_delivery_vvu_results set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_labour_delivery_vvu_results drop foreign key if exists tbl_labour_delivery_vvu_results_facility_id_foreign;
alter table tbl_labour_delivery_vvu_results change column  facility_id facility_id char(36);
update tbl_labour_delivery_vvu_results set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_fplanning_viginal_by_arm_investigations add column if not exists copy_id char(36) after id;
update tbl_fplanning_viginal_by_arm_investigations set copy_id = uuid();
alter table tbl_fplanning_viginal_by_arm_investigations drop foreign key if exists tbl_fplanning_viginal_by_arm_investigations_user_id_foreign;
alter table tbl_fplanning_viginal_by_arm_investigations change column  user_id user_id char(36);
update tbl_fplanning_viginal_by_arm_investigations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_fplanning_viginal_by_arm_investigations drop foreign key if exists tbl_fplanning_viginal_by_arm_investigations_client_id_foreign;
alter table tbl_fplanning_viginal_by_arm_investigations change column  client_id client_id char(36);
update tbl_fplanning_viginal_by_arm_investigations set client_id = (select copy_id from tbl_family_planning_registers where tbl_family_planning_registers.id =  client_id limit 1);
alter table tbl_fplanning_viginal_by_arm_investigations drop foreign key if exists tbl_fplanning_viginal_by_arm_investigations_facility_id_foreign;
alter table tbl_fplanning_viginal_by_arm_investigations change column  facility_id facility_id char(36);
update tbl_fplanning_viginal_by_arm_investigations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_investigations add column if not exists copy_id char(36) after id;
update tbl_post_natal_investigations set copy_id = uuid();
alter table tbl_post_natal_investigations drop foreign key if exists tbl_post_natal_investigations_user_id_foreign;
alter table tbl_post_natal_investigations change column  user_id user_id char(36);
update tbl_post_natal_investigations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_investigations drop foreign key if exists tbl_post_natal_investigations_patient_id_foreign;
alter table tbl_post_natal_investigations change column  patient_id patient_id char(36);
update tbl_post_natal_investigations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_post_natal_investigations drop foreign key if exists tbl_post_natal_investigations_facility_id_foreign;
alter table tbl_post_natal_investigations change column  facility_id facility_id char(36);
update tbl_post_natal_investigations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_observations add column if not exists copy_id char(36) after id;
update tbl_labour_observations set copy_id = uuid();
alter table tbl_labour_observations drop foreign key if exists tbl_labour_observations_user_id_foreign;
alter table tbl_labour_observations change column  user_id user_id char(36);
update tbl_labour_observations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_observations drop foreign key if exists tbl_labour_observations_client_id_foreign;
alter table tbl_labour_observations change column  client_id client_id char(36);
update tbl_labour_observations set client_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  client_id limit 1);
alter table tbl_labour_observations drop foreign key if exists tbl_labour_observations_facility_id_foreign;
alter table tbl_labour_observations change column  facility_id facility_id char(36);
update tbl_labour_observations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_patient_tb_type_registers add column if not exists copy_id char(36) after id;
update tbl_patient_tb_type_registers set copy_id = uuid();
alter table tbl_patient_tb_type_registers drop foreign key if exists tbl_patient_tb_type_registers_user_id_foreign;
alter table tbl_patient_tb_type_registers change column  user_id user_id char(36);
update tbl_patient_tb_type_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_patient_tb_type_registers drop foreign key if exists tbl_patient_tb_type_registers_client_id_foreign;
alter table tbl_patient_tb_type_registers change column  client_id client_id char(36);
update tbl_patient_tb_type_registers set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_patient_tb_type_registers drop foreign key if exists tbl_patient_tb_type_registers_facility_id_foreign;
alter table tbl_patient_tb_type_registers change column  facility_id facility_id char(36);
update tbl_patient_tb_type_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_postnatal_baby_feed_hours add column if not exists copy_id char(36) after id;
update tbl_postnatal_baby_feed_hours set copy_id = uuid();
alter table tbl_postnatal_baby_feed_hours drop foreign key if exists tbl_postnatal_baby_feed_hours_user_id_foreign;
alter table tbl_postnatal_baby_feed_hours change column  user_id user_id char(36);
update tbl_postnatal_baby_feed_hours set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_postnatal_baby_feed_hours drop foreign key if exists tbl_postnatal_baby_feed_hours_patient_id_foreign;
alter table tbl_postnatal_baby_feed_hours change column  patient_id patient_id char(36);
update tbl_postnatal_baby_feed_hours set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_postnatal_baby_feed_hours drop foreign key if exists tbl_postnatal_baby_feed_hours_facility_id_foreign;
alter table tbl_postnatal_baby_feed_hours change column  facility_id facility_id char(36);
update tbl_postnatal_baby_feed_hours set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_anti_natal_attendances add column if not exists copy_id char(36) after id;
update tbl_anti_natal_attendances set copy_id = uuid();
alter table tbl_anti_natal_attendances drop foreign key if exists tbl_anti_natal_attendances_user_id_foreign;
alter table tbl_anti_natal_attendances change column  user_id user_id char(36);
update tbl_anti_natal_attendances set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_attendances drop foreign key if exists tbl_anti_natal_attendances_client_id_foreign;
alter table tbl_anti_natal_attendances change column  client_id client_id char(36);
update tbl_anti_natal_attendances set client_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  client_id limit 1);
alter table tbl_anti_natal_attendances drop foreign key if exists tbl_anti_natal_attendances_facility_id_foreign;
alter table tbl_anti_natal_attendances change column  facility_id facility_id char(36);
update tbl_anti_natal_attendances set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_pediatric_diataries add column if not exists copy_id char(36) after id;
update tbl_pediatric_diataries set copy_id = uuid();
alter table tbl_pediatric_diataries drop foreign key if exists tbl_pediatric_diataries_user_id_foreign;
alter table tbl_pediatric_diataries change column  user_id user_id char(36);
update tbl_pediatric_diataries set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_pediatric_diataries drop foreign key if exists tbl_pediatric_diataries_client_id_foreign;
alter table tbl_pediatric_diataries change column  client_id client_id char(36);
update tbl_pediatric_diataries set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_pediatric_diataries drop foreign key if exists tbl_pediatric_diataries_facility_id_foreign;
alter table tbl_pediatric_diataries change column  facility_id facility_id char(36);
update tbl_pediatric_diataries set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_userdepartments add column if not exists copy_id char(36) after id;
update tbl_userdepartments set copy_id = uuid();
alter table tbl_userdepartments drop foreign key if exists tbl_userdepartments_user_id_foreign;
alter table tbl_userdepartments change column  user_id user_id char(36);
update tbl_userdepartments set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_userdepartments drop foreign key if exists tbl_userdepartments_registered_by_foreign;
alter table tbl_userdepartments change column  registered_by registered_by char(36);
update tbl_userdepartments set registered_by = (select copy_id from users where users.id =  registered_by limit 1);

alter table tbl_ctc_patient_supports add column if not exists copy_id char(36) after id;
update tbl_ctc_patient_supports set copy_id = uuid();
alter table tbl_ctc_patient_supports drop foreign key if exists tbl_ctc_patient_supports_user_id_foreign;
alter table tbl_ctc_patient_supports change column  user_id user_id char(36);
update tbl_ctc_patient_supports set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_ctc_patient_supports drop foreign key if exists tbl_ctc_patient_supports_patient_id_foreign;
alter table tbl_ctc_patient_supports change column  patient_id patient_id char(36);
update tbl_ctc_patient_supports set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_ctc_patient_supports drop foreign key if exists tbl_ctc_patient_supports_facility_id_foreign;
alter table tbl_ctc_patient_supports change column  facility_id facility_id char(36);
update tbl_ctc_patient_supports set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_anti_natal_ifas add column if not exists copy_id char(36) after id;
update tbl_anti_natal_ifas set copy_id = uuid();
alter table tbl_anti_natal_ifas drop foreign key if exists tbl_anti_natal_ifas_user_id_foreign;
alter table tbl_anti_natal_ifas change column  user_id user_id char(36);
update tbl_anti_natal_ifas set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_ifas drop foreign key if exists tbl_anti_natal_ifas_patient_id_foreign;
alter table tbl_anti_natal_ifas change column  patient_id patient_id char(36);
update tbl_anti_natal_ifas set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_anti_natal_ifas drop foreign key if exists tbl_anti_natal_ifas_facility_id_foreign;
alter table tbl_anti_natal_ifas change column  facility_id facility_id char(36);
update tbl_anti_natal_ifas set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_family_planning_vvu_statuses add column if not exists copy_id char(36) after id;
update tbl_family_planning_vvu_statuses set copy_id = uuid();
alter table tbl_family_planning_vvu_statuses drop foreign key if exists tbl_family_planning_vvu_statuses_user_id_foreign;
alter table tbl_family_planning_vvu_statuses change column  user_id user_id char(36);
update tbl_family_planning_vvu_statuses set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_family_planning_vvu_statuses drop foreign key if exists tbl_family_planning_vvu_statuses_patient_id_foreign;
alter table tbl_family_planning_vvu_statuses change column  patient_id patient_id char(36);
update tbl_family_planning_vvu_statuses set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_family_planning_vvu_statuses drop foreign key if exists tbl_family_planning_vvu_statuses_facility_id_foreign;
alter table tbl_family_planning_vvu_statuses change column  facility_id facility_id char(36);
update tbl_family_planning_vvu_statuses set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_tb_patient_medication_followups add column if not exists copy_id char(36) after id;
update tbl_tb_patient_medication_followups set copy_id = uuid();
alter table tbl_tb_patient_medication_followups drop foreign key if exists tbl_tb_patient_medication_followups_user_id_foreign;
alter table tbl_tb_patient_medication_followups change column  user_id user_id char(36);
update tbl_tb_patient_medication_followups set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_tb_patient_medication_followups drop foreign key if exists tbl_tb_patient_medication_followups_client_id_foreign;
alter table tbl_tb_patient_medication_followups change column  client_id client_id char(36);
update tbl_tb_patient_medication_followups set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_tb_patient_medication_followups drop foreign key if exists tbl_tb_patient_medication_followups_facility_id_foreign;
alter table tbl_tb_patient_medication_followups change column  facility_id facility_id char(36);
update tbl_tb_patient_medication_followups set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_dehiscence_fistula_mental_statuses add column if not exists copy_id char(36) after id;
update tbl_post_dehiscence_fistula_mental_statuses set copy_id = uuid();
alter table tbl_post_dehiscence_fistula_mental_statuses drop foreign key if exists tbl_post_dehiscence_fistula_mental_statuses_user_id_foreign;
alter table tbl_post_dehiscence_fistula_mental_statuses change column  user_id user_id char(36);
update tbl_post_dehiscence_fistula_mental_statuses set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_dehiscence_fistula_mental_statuses drop foreign key if exists tbl_post_dehiscence_fistula_mental_statuses_patient_id_foreign;
alter table tbl_post_dehiscence_fistula_mental_statuses change column  patient_id patient_id char(36);
update tbl_post_dehiscence_fistula_mental_statuses set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_post_dehiscence_fistula_mental_statuses drop foreign key if exists tbl_post_dehiscence_fistula_mental_statuses_facility_id_foreign;
alter table tbl_post_dehiscence_fistula_mental_statuses change column  facility_id facility_id char(36);
update tbl_post_dehiscence_fistula_mental_statuses set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_birth_infos add column if not exists copy_id char(36) after id;
update tbl_labour_birth_infos set copy_id = uuid();
alter table tbl_labour_birth_infos drop foreign key if exists tbl_labour_birth_infos_user_id_foreign;
alter table tbl_labour_birth_infos change column  user_id user_id char(36);
update tbl_labour_birth_infos set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_birth_infos drop foreign key if exists tbl_labour_birth_infos_patient_id_foreign;
alter table tbl_labour_birth_infos change column  patient_id patient_id char(36);
update tbl_labour_birth_infos set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_labour_birth_infos drop foreign key if exists tbl_labour_birth_infos_facility_id_foreign;
alter table tbl_labour_birth_infos change column  facility_id facility_id char(36);
update tbl_labour_birth_infos set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_emergency_patients add column if not exists copy_id char(36) after id;
update tbl_emergency_patients set copy_id = uuid();
alter table tbl_emergency_patients drop foreign key if exists tbl_emergency_patients_registered_by_foreign;
alter table tbl_emergency_patients change column  registered_by registered_by char(36);
update tbl_emergency_patients set registered_by = (select copy_id from users where users.id =  registered_by limit 1);
alter table tbl_emergency_patients drop foreign key if exists tbl_emergency_patients_visiting_id_foreign;
alter table tbl_emergency_patients change column  visiting_id visiting_id char(36);
update tbl_emergency_patients set visiting_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visiting_id limit 1);

alter table tbl_fplanning_lab_investigations add column if not exists copy_id char(36) after id;
update tbl_fplanning_lab_investigations set copy_id = uuid();
alter table tbl_fplanning_lab_investigations drop foreign key if exists tbl_fplanning_lab_investigations_user_id_foreign;
alter table tbl_fplanning_lab_investigations change column  user_id user_id char(36);
update tbl_fplanning_lab_investigations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_fplanning_lab_investigations drop foreign key if exists tbl_fplanning_lab_investigations_client_id_foreign;
alter table tbl_fplanning_lab_investigations change column  client_id client_id char(36);
update tbl_fplanning_lab_investigations set client_id = (select copy_id from tbl_family_planning_registers where tbl_family_planning_registers.id =  client_id limit 1);
alter table tbl_fplanning_lab_investigations drop foreign key if exists tbl_fplanning_lab_investigations_facility_id_foreign;
alter table tbl_fplanning_lab_investigations change column  facility_id facility_id char(36);
update tbl_fplanning_lab_investigations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_past_dental_records add column if not exists copy_id char(36) after id;
update tbl_past_dental_records set copy_id = uuid();
alter table tbl_past_dental_records drop foreign key if exists tbl_past_dental_records_user_id_foreign;
alter table tbl_past_dental_records change column  user_id user_id char(36);
update tbl_past_dental_records set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_past_dental_records drop foreign key if exists tbl_past_dental_records_patient_id_foreign;
alter table tbl_past_dental_records change column  patient_id patient_id char(36);
update tbl_past_dental_records set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_past_dental_records drop foreign key if exists tbl_past_dental_records_visit_date_id_foreign;
alter table tbl_past_dental_records change column  visit_date_id visit_date_id char(36);
update tbl_past_dental_records set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);

alter table tbl_fplanning_speculam_investigations add column if not exists copy_id char(36) after id;
update tbl_fplanning_speculam_investigations set copy_id = uuid();
alter table tbl_fplanning_speculam_investigations drop foreign key if exists tbl_fplanning_speculam_investigations_user_id_foreign;
alter table tbl_fplanning_speculam_investigations change column  user_id user_id char(36);
update tbl_fplanning_speculam_investigations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_fplanning_speculam_investigations drop foreign key if exists tbl_fplanning_speculam_investigations_client_id_foreign;
alter table tbl_fplanning_speculam_investigations change column  client_id client_id char(36);
update tbl_fplanning_speculam_investigations set client_id = (select copy_id from tbl_family_planning_registers where tbl_family_planning_registers.id =  client_id limit 1);
alter table tbl_fplanning_speculam_investigations drop foreign key if exists tbl_fplanning_speculam_investigations_facility_id_foreign;
alter table tbl_fplanning_speculam_investigations change column  facility_id facility_id char(36);
update tbl_fplanning_speculam_investigations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);



alter table tbl_informed_consents add column if not exists copy_id char(36) after id;
update tbl_informed_consents set copy_id = uuid();
alter table tbl_informed_consents drop foreign key if exists tbl_informed_consents_user_id_foreign;
alter table tbl_informed_consents change column  user_id user_id char(36);
update tbl_informed_consents set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_informed_consents drop foreign key if exists tbl_informed_consents_patient_id_foreign;
alter table tbl_informed_consents change column  patient_id patient_id char(36);
update tbl_informed_consents set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_informed_consents drop foreign key if exists tbl_informed_consents_facility_id_foreign;
alter table tbl_informed_consents change column  facility_id facility_id char(36);
update tbl_informed_consents set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_informed_consents drop foreign key if exists tbl_informed_consents_visit_date_id_foreign;
alter table tbl_informed_consents change column  visit_date_id visit_date_id char(36);
update tbl_informed_consents set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_informed_consents drop foreign key if exists tbl_informed_consents_admission_id_foreign;
alter table tbl_informed_consents change column  admission_id admission_id char(36);
update tbl_informed_consents set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);



-- does this table exists?
alter table tbl_past_urology_histories add column if not exists copy_id char(36) after id;
update tbl_past_urology_histories set copy_id = uuid();
alter table tbl_past_urology_histories drop foreign key if exists tbl_past_urology_histories_user_id_foreign;
alter table tbl_past_urology_histories change column  user_id user_id char(36);
update tbl_past_urology_histories set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_past_urology_histories drop foreign key if exists tbl_past_urology_histories_patient_id_foreign;
alter table tbl_past_urology_histories change column  patient_id patient_id char(36);
update tbl_past_urology_histories set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_past_urology_histories drop foreign key if exists tbl_past_urology_histories_facility_id_foreign;
alter table tbl_past_urology_histories change column  facility_id facility_id char(36);
update tbl_past_urology_histories set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_past_urology_histories drop foreign key if exists tbl_past_urology_histories_visit_date_id_foreign;
alter table tbl_past_urology_histories change column  visit_date_id visit_date_id char(36);
update tbl_past_urology_histories set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_past_urology_histories drop foreign key if exists tbl_past_urology_histories_admission_id_foreign;
alter table tbl_past_urology_histories change column  admission_id admission_id char(36);
update tbl_past_urology_histories set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_labour_fsb_msbs add column if not exists copy_id char(36) after id;
update tbl_labour_fsb_msbs set copy_id = uuid();
alter table tbl_labour_fsb_msbs drop foreign key if exists tbl_labour_fsb_msbs_user_id_foreign;
alter table tbl_labour_fsb_msbs change column  user_id user_id char(36);
update tbl_labour_fsb_msbs set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_fsb_msbs drop foreign key if exists tbl_labour_fsb_msbs_patient_id_foreign;
alter table tbl_labour_fsb_msbs change column  patient_id patient_id char(36);
update tbl_labour_fsb_msbs set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_labour_fsb_msbs drop foreign key if exists tbl_labour_fsb_msbs_facility_id_foreign;
alter table tbl_labour_fsb_msbs change column  facility_id facility_id char(36);
update tbl_labour_fsb_msbs set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_ctc_family_informations add column if not exists copy_id char(36) after id;
update tbl_ctc_family_informations set copy_id = uuid();
alter table tbl_ctc_family_informations drop foreign key if exists tbl_ctc_family_informations_user_id_foreign;
alter table tbl_ctc_family_informations change column  user_id user_id char(36);
update tbl_ctc_family_informations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_ctc_family_informations drop foreign key if exists tbl_ctc_family_informations_patient_id_foreign;
alter table tbl_ctc_family_informations change column  patient_id patient_id char(36);
update tbl_ctc_family_informations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);

alter table tbl_intake_observations add column if not exists copy_id char(36) after id;
update tbl_intake_observations set copy_id = uuid();
alter table tbl_intake_observations drop foreign key if exists tbl_intake_observations_admission_id_foreign;
alter table tbl_intake_observations change column  admission_id admission_id char(36);
update tbl_intake_observations set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_previous_pregnancy_indicators add column if not exists copy_id char(36) after id;
update tbl_previous_pregnancy_indicators set copy_id = uuid();
alter table tbl_previous_pregnancy_indicators drop foreign key if exists tbl_previous_pregnancy_indicators_user_id_foreign;
alter table tbl_previous_pregnancy_indicators change column  user_id user_id char(36);
update tbl_previous_pregnancy_indicators set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_previous_pregnancy_indicators drop foreign key if exists tbl_previous_pregnancy_indicators_client_id_foreign;
alter table tbl_previous_pregnancy_indicators change column  client_id client_id char(36);
update tbl_previous_pregnancy_indicators set client_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  client_id limit 1);
alter table tbl_previous_pregnancy_indicators drop foreign key if exists tbl_previous_pregnancy_indicators_facility_id_foreign;
alter table tbl_previous_pregnancy_indicators change column  facility_id facility_id char(36);
update tbl_previous_pregnancy_indicators set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_anti_natal_deworms add column if not exists copy_id char(36) after id;
update tbl_anti_natal_deworms set copy_id = uuid();
alter table tbl_anti_natal_deworms drop foreign key if exists tbl_anti_natal_deworms_user_id_foreign;
alter table tbl_anti_natal_deworms change column  user_id user_id char(36);
update tbl_anti_natal_deworms set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_deworms drop foreign key if exists tbl_anti_natal_deworms_patient_id_foreign;
alter table tbl_anti_natal_deworms change column  patient_id patient_id char(36);
update tbl_anti_natal_deworms set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_anti_natal_deworms drop foreign key if exists tbl_anti_natal_deworms_facility_id_foreign;
alter table tbl_anti_natal_deworms change column  facility_id facility_id char(36);
update tbl_anti_natal_deworms set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_family_planning_attendances add column if not exists copy_id char(36) after id;
update tbl_family_planning_attendances set copy_id = uuid();
alter table tbl_family_planning_attendances drop foreign key if exists tbl_family_planning_attendances_user_id_foreign;
alter table tbl_family_planning_attendances change column  user_id user_id char(36);
update tbl_family_planning_attendances set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_family_planning_attendances drop foreign key if exists tbl_family_planning_attendances_client_id_foreign;
alter table tbl_family_planning_attendances change column  client_id client_id char(36);
update tbl_family_planning_attendances set client_id = (select copy_id from tbl_family_planning_registers where tbl_family_planning_registers.id =  client_id limit 1);
alter table tbl_family_planning_attendances drop foreign key if exists tbl_family_planning_attendances_facility_id_foreign;
alter table tbl_family_planning_attendances change column  facility_id facility_id char(36);
update tbl_family_planning_attendances set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_family_planning_attendances change column  complains  complains text;
alter table tbl_family_planning_attendances change column  comment_treatment comment_treatment text;

alter table tbl_pediatric_post_natals add column if not exists copy_id char(36) after id;
update tbl_pediatric_post_natals set copy_id = uuid();
alter table tbl_pediatric_post_natals drop foreign key if exists tbl_pediatric_post_natals_user_id_foreign;
alter table tbl_pediatric_post_natals change column  user_id user_id char(36);
update tbl_pediatric_post_natals set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_pediatric_post_natals drop foreign key if exists tbl_pediatric_post_natals_client_id_foreign;
alter table tbl_pediatric_post_natals change column  client_id client_id char(36);
update tbl_pediatric_post_natals set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_pediatric_post_natals drop foreign key if exists tbl_pediatric_post_natals_facility_id_foreign;
alter table tbl_pediatric_post_natals change column  facility_id facility_id char(36);
update tbl_pediatric_post_natals set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_anti_natal_malarias add column if not exists copy_id char(36) after id;
update tbl_anti_natal_malarias set copy_id = uuid();
alter table tbl_anti_natal_malarias drop foreign key if exists tbl_anti_natal_malarias_user_id_foreign;
alter table tbl_anti_natal_malarias change column  user_id user_id char(36);
update tbl_anti_natal_malarias set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_malarias drop foreign key if exists tbl_anti_natal_malarias_patient_id_foreign;
alter table tbl_anti_natal_malarias change column  patient_id patient_id char(36);
update tbl_anti_natal_malarias set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_anti_natal_malarias drop foreign key if exists tbl_anti_natal_malarias_facility_id_foreign;
alter table tbl_anti_natal_malarias change column  facility_id facility_id char(36);
update tbl_anti_natal_malarias set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_surgery_physical_examinations add column if not exists copy_id char(36) after id;
update tbl_surgery_physical_examinations set copy_id = uuid();
alter table tbl_surgery_physical_examinations drop foreign key if exists tbl_surgery_physical_examinations_admission_id_foreign;
alter table tbl_surgery_physical_examinations change column  admission_id admission_id char(36);
update tbl_surgery_physical_examinations set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_surgery_physical_examinations drop foreign key if exists tbl_surgery_physical_examinations_request_id_foreign;
alter table tbl_surgery_physical_examinations change column  request_id request_id char(36);
update tbl_surgery_physical_examinations set request_id = (select copy_id from tbl_theatre_waits where tbl_theatre_waits.id =  request_id limit 1);
alter table tbl_surgery_physical_examinations drop foreign key if exists tbl_surgery_physical_examinations_nurse_id_foreign;
alter table tbl_surgery_physical_examinations change column  nurse_id nurse_id char(36);
update tbl_surgery_physical_examinations set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);

alter table tbl_anti_natal_reattendances add column if not exists copy_id char(36) after id;
update tbl_anti_natal_reattendances set copy_id = uuid();
alter table tbl_anti_natal_reattendances drop foreign key if exists tbl_anti_natal_reattendances_user_id_foreign;
alter table tbl_anti_natal_reattendances change column  user_id user_id char(36);
update tbl_anti_natal_reattendances set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_natal_reattendances drop foreign key if exists tbl_anti_natal_reattendances_patient_id_foreign;
alter table tbl_anti_natal_reattendances change column  patient_id patient_id char(36);
update tbl_anti_natal_reattendances set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_anti_natal_reattendances drop foreign key if exists tbl_anti_natal_reattendances_facility_id_foreign;
alter table tbl_anti_natal_reattendances change column  facility_id facility_id char(36);
update tbl_anti_natal_reattendances set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_fplanning_breast_cancer_investigations add column if not exists copy_id char(36) after id;
update tbl_fplanning_breast_cancer_investigations set copy_id = uuid();
alter table tbl_fplanning_breast_cancer_investigations drop foreign key if exists tbl_fplanning_breast_cancer_investigations_user_id_foreign;
alter table tbl_fplanning_breast_cancer_investigations change column  user_id user_id char(36);
update tbl_fplanning_breast_cancer_investigations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_fplanning_breast_cancer_investigations drop foreign key if exists tbl_fplanning_breast_cancer_investigations_patient_id_foreign;
alter table tbl_fplanning_breast_cancer_investigations change column  patient_id patient_id char(36);
update tbl_fplanning_breast_cancer_investigations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_fplanning_breast_cancer_investigations drop foreign key if exists tbl_fplanning_breast_cancer_investigations_facility_id_foreign;
alter table tbl_fplanning_breast_cancer_investigations change column  facility_id facility_id char(36);
update tbl_fplanning_breast_cancer_investigations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_tb_pre_entry_registers add column if not exists copy_id char(36) after id;
update tbl_tb_pre_entry_registers set copy_id = uuid();
alter table tbl_tb_pre_entry_registers drop foreign key if exists tbl_tb_pre_entry_registers_user_id_foreign;
alter table tbl_tb_pre_entry_registers change column  user_id user_id char(36);
update tbl_tb_pre_entry_registers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_tb_pre_entry_registers drop foreign key if exists tbl_tb_pre_entry_registers_client_id_foreign;
alter table tbl_tb_pre_entry_registers change column  client_id client_id char(36);
update tbl_tb_pre_entry_registers set client_id = (select copy_id from tbl_patients where tbl_patients.id =  client_id limit 1);
alter table tbl_tb_pre_entry_registers drop foreign key if exists tbl_tb_pre_entry_registers_facility_id_foreign;
alter table tbl_tb_pre_entry_registers change column  facility_id facility_id char(36);
update tbl_tb_pre_entry_registers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_birth_infos add column if not exists copy_id char(36) after id;
update tbl_post_natal_birth_infos set copy_id = uuid();
alter table tbl_post_natal_birth_infos drop foreign key if exists tbl_post_natal_birth_infos_user_id_foreign;
alter table tbl_post_natal_birth_infos change column  user_id user_id char(36);
update tbl_post_natal_birth_infos set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_birth_infos drop foreign key if exists tbl_post_natal_birth_infos_patient_id_foreign;
alter table tbl_post_natal_birth_infos change column  patient_id patient_id char(36);
update tbl_post_natal_birth_infos set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_post_natal_birth_infos drop foreign key if exists tbl_post_natal_birth_infos_facility_id_foreign;
alter table tbl_post_natal_birth_infos change column  facility_id facility_id char(36);
update tbl_post_natal_birth_infos set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_labour_delivery_child_feedings add column if not exists copy_id char(36) after id;
update tbl_labour_delivery_child_feedings set copy_id = uuid();
alter table tbl_labour_delivery_child_feedings drop foreign key if exists tbl_labour_delivery_child_feedings_user_id_foreign;
alter table tbl_labour_delivery_child_feedings change column  user_id user_id char(36);
update tbl_labour_delivery_child_feedings set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_labour_delivery_child_feedings drop foreign key if exists tbl_labour_delivery_child_feedings_patient_id_foreign;
alter table tbl_labour_delivery_child_feedings change column  patient_id patient_id char(36);
update tbl_labour_delivery_child_feedings set patient_id = (select copy_id from tbl_anti_natal_registers where tbl_anti_natal_registers.id =  patient_id limit 1);
alter table tbl_labour_delivery_child_feedings drop foreign key if exists tbl_labour_delivery_child_feedings_facility_id_foreign;
alter table tbl_labour_delivery_child_feedings change column  facility_id facility_id char(36);
update tbl_labour_delivery_child_feedings set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_fplanning_previous_menstrals add column if not exists copy_id char(36) after id;
update tbl_fplanning_previous_menstrals set copy_id = uuid();
alter table tbl_fplanning_previous_menstrals drop foreign key if exists tbl_fplanning_previous_menstrals_user_id_foreign;
alter table tbl_fplanning_previous_menstrals change column  user_id user_id char(36);
update tbl_fplanning_previous_menstrals set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_fplanning_previous_menstrals drop foreign key if exists tbl_fplanning_previous_menstrals_client_id_foreign;
alter table tbl_fplanning_previous_menstrals change column  client_id client_id char(36);
update tbl_fplanning_previous_menstrals set client_id = (select copy_id from tbl_family_planning_registers where tbl_family_planning_registers.id =  client_id limit 1);
alter table tbl_fplanning_previous_menstrals drop foreign key if exists tbl_fplanning_previous_menstrals_facility_id_foreign;
alter table tbl_fplanning_previous_menstrals change column  facility_id facility_id char(36);
update tbl_fplanning_previous_menstrals set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_post_natal_child_infections add column if not exists copy_id char(36) after id;
update tbl_post_natal_child_infections set copy_id = uuid();
alter table tbl_post_natal_child_infections drop foreign key if exists tbl_post_natal_child_infections_user_id_foreign;
alter table tbl_post_natal_child_infections change column  user_id user_id char(36);
update tbl_post_natal_child_infections set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_post_natal_child_infections drop foreign key if exists tbl_post_natal_child_infections_patient_id_foreign;
alter table tbl_post_natal_child_infections change column  patient_id patient_id char(36);
update tbl_post_natal_child_infections set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_post_natal_child_infections drop foreign key if exists tbl_post_natal_child_infections_facility_id_foreign;
alter table tbl_post_natal_child_infections change column  facility_id facility_id char(36);
update tbl_post_natal_child_infections set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_condoms add column if not exists copy_id char(36) after id;
update tbl_condoms set copy_id = uuid();
alter table tbl_condoms drop foreign key if exists tbl_condoms_user_id_foreign;
alter table tbl_condoms change column  user_id user_id char(36);
update tbl_condoms set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_condoms drop foreign key if exists tbl_condoms_patient_id_foreign;
alter table tbl_condoms change column  patient_id patient_id char(36);
update tbl_condoms set patient_id = (select copy_id from tbl_family_planning_registers where tbl_family_planning_registers.id =  patient_id limit 1);
alter table tbl_condoms drop foreign key if exists tbl_condoms_facility_id_foreign;
alter table tbl_condoms change column  facility_id facility_id char(36);
update tbl_condoms set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_pre_history_anethetics add column if not exists copy_id char(36) after id;
update tbl_pre_history_anethetics set copy_id = uuid();
alter table tbl_pre_history_anethetics drop foreign key if exists tbl_pre_history_anethetics_user_id_foreign;
alter table tbl_pre_history_anethetics change column  user_id user_id char(36);
update tbl_pre_history_anethetics set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_pre_history_anethetics drop foreign key if exists tbl_pre_history_anethetics_patient_id_foreign;
alter table tbl_pre_history_anethetics change column  patient_id patient_id char(36);
update tbl_pre_history_anethetics set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_pre_history_anethetics drop foreign key if exists tbl_pre_history_anethetics_facility_id_foreign;
alter table tbl_pre_history_anethetics change column  facility_id facility_id char(36);
update tbl_pre_history_anethetics set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_pre_history_anethetics drop foreign key if exists tbl_pre_history_anethetics_visit_date_id_foreign;
alter table tbl_pre_history_anethetics change column  visit_date_id visit_date_id char(36);
update tbl_pre_history_anethetics set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_pre_history_anethetics drop foreign key if exists tbl_pre_history_anethetics_admission_id_foreign;
alter table tbl_pre_history_anethetics change column  admission_id admission_id char(36);
update tbl_pre_history_anethetics set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_inputs add column if not exists copy_id char(36) after id;
update tbl_inputs set copy_id = uuid();
alter table tbl_inputs drop foreign key if exists tbl_inputs_user_id_foreign;
alter table tbl_inputs change column  user_id user_id char(36);
update tbl_inputs set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_inputs drop foreign key if exists tbl_inputs_facility_id_foreign;
alter table tbl_inputs change column  facility_id facility_id char(36);
update tbl_inputs set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_inputs drop foreign key if exists tbl_inputs_admission_id_foreign;
alter table tbl_inputs change column  admission_id admission_id char(36);
update tbl_inputs set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_inputs drop foreign key if exists tbl_inputs_visit_date_id_foreign;
alter table tbl_inputs change column  visit_date_id visit_date_id char(36);
update tbl_inputs set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);

alter table tbl_status_wards add column if not exists copy_id char(36) after id;
update tbl_status_wards set copy_id = uuid();
alter table tbl_status_wards drop foreign key if exists tbl_status_wards_user_id_foreign;
alter table tbl_status_wards change column  user_id user_id char(36);
update tbl_status_wards set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_status_wards drop foreign key if exists tbl_status_wards_ward_id_foreign;
alter table tbl_status_wards change column  ward_id ward_id char(36);
update tbl_status_wards set ward_id = (select copy_id from tbl_wards where tbl_wards.id =  ward_id limit 1);
alter table tbl_status_wards drop foreign key if exists tbl_status_wards_facility_id_foreign;
alter table tbl_status_wards change column  facility_id facility_id char(36);
update tbl_status_wards set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_status_wards drop foreign key if exists tbl_status_wards_visit_date_id_foreign;
alter table tbl_status_wards change column  visit_date_id visit_date_id char(36);
update tbl_status_wards set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_status_wards drop foreign key if exists tbl_status_wards_admission_id_foreign;
alter table tbl_status_wards change column  admission_id admission_id char(36);
update tbl_status_wards set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_nursing_cares add column if not exists copy_id char(36) after id;
update tbl_nursing_cares set copy_id = uuid();
alter table tbl_nursing_cares drop foreign key if exists tbl_nursing_cares_user_id_foreign;
alter table tbl_nursing_cares change column  user_id user_id char(36);
update tbl_nursing_cares set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_nursing_cares drop foreign key if exists tbl_nursing_cares_facility_id_foreign;
alter table tbl_nursing_cares change column  facility_id facility_id char(36);
update tbl_nursing_cares set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_nursing_cares drop foreign key if exists tbl_nursing_cares_admission_id_foreign;
alter table tbl_nursing_cares change column  admission_id admission_id char(36);
update tbl_nursing_cares set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_discharge_permits add column if not exists copy_id char(36) after id;
update tbl_discharge_permits set copy_id = uuid();
alter table tbl_discharge_permits drop foreign key if exists tbl_discharge_permits_nurse_id_foreign;
alter table tbl_discharge_permits change column  nurse_id nurse_id char(36);
update tbl_discharge_permits set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);
alter table tbl_discharge_permits drop foreign key if exists tbl_discharge_permits_admission_id_foreign;
alter table tbl_discharge_permits change column  admission_id admission_id char(36);
update tbl_discharge_permits set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_treatment_charts add column if not exists copy_id char(36) after id;
update tbl_treatment_charts set copy_id = uuid();
alter table tbl_treatment_charts drop foreign key if exists tbl_treatment_charts_admission_id_foreign;
alter table tbl_treatment_charts change column  admission_id admission_id char(36);
update tbl_treatment_charts set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_blood_stocks add column if not exists copy_id char(36) after id;
update tbl_blood_stocks set copy_id = uuid();
alter table tbl_blood_stocks drop foreign key if exists tbl_blood_stocks_user_id_foreign;
alter table tbl_blood_stocks change column  user_id user_id char(36);
update tbl_blood_stocks set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_blood_stocks drop foreign key if exists tbl_blood_stocks_patient_id_foreign;
alter table tbl_blood_stocks change column  patient_id patient_id char(36);
update tbl_blood_stocks set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_blood_stocks drop foreign key if exists tbl_blood_stocks_facility_id_foreign;
alter table tbl_blood_stocks change column  facility_id facility_id char(36);
update tbl_blood_stocks set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_nutritional_foods add column if not exists copy_id char(36) after id;
update tbl_nutritional_foods set copy_id = uuid();
alter table tbl_nutritional_foods drop foreign key if exists tbl_nutritional_foods_user_id_foreign;
alter table tbl_nutritional_foods change column  user_id user_id char(36);
update tbl_nutritional_foods set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_nutritional_foods drop foreign key if exists tbl_nutritional_foods_patient_id_foreign;
alter table tbl_nutritional_foods change column  patient_id patient_id char(36);
update tbl_nutritional_foods set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_nutritional_foods drop foreign key if exists tbl_nutritional_foods_facility_id_foreign;
alter table tbl_nutritional_foods change column  facility_id facility_id char(36);
update tbl_nutritional_foods set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_nutritional_foods drop foreign key if exists tbl_nutritional_foods_visit_id_foreign;
alter table tbl_nutritional_foods change column  visit_id visit_id char(36);
update tbl_nutritional_foods set visit_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_id limit 1);
alter table tbl_nutritional_foods change column  description description text;

alter table tbl_blood_screenings add column if not exists copy_id char(36) after id;
update tbl_blood_screenings set copy_id = uuid();
alter table tbl_blood_screenings drop foreign key if exists tbl_blood_screenings_user_id_foreign;
alter table tbl_blood_screenings change column  user_id user_id char(36);
update tbl_blood_screenings set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_blood_screenings drop foreign key if exists tbl_blood_screenings_patient_id_foreign;
alter table tbl_blood_screenings change column  patient_id patient_id char(36);
update tbl_blood_screenings set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_blood_screenings drop foreign key if exists tbl_blood_screenings_facility_id_foreign;
alter table tbl_blood_screenings change column  facility_id facility_id char(36);
update tbl_blood_screenings set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_blood_donations add column if not exists copy_id char(36) after id;
update tbl_blood_donations set copy_id = uuid();
alter table tbl_blood_donations drop foreign key if exists tbl_blood_donations_user_id_foreign;
alter table tbl_blood_donations change column  user_id user_id char(36);
update tbl_blood_donations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_blood_donations drop foreign key if exists tbl_blood_donations_patient_id_foreign;
alter table tbl_blood_donations change column  patient_id patient_id char(36);
update tbl_blood_donations set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_blood_donations drop foreign key if exists tbl_blood_donations_facility_id_foreign;
alter table tbl_blood_donations change column  facility_id facility_id char(36);
update tbl_blood_donations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_donor_infors add column if not exists copy_id char(36) after id;
update tbl_donor_infors set copy_id = uuid();
alter table tbl_donor_infors drop foreign key if exists tbl_donor_infors_user_id_foreign;
alter table tbl_donor_infors change column  user_id user_id char(36);
update tbl_donor_infors set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_donor_infors drop foreign key if exists tbl_donor_infors_patient_id_foreign;
alter table tbl_donor_infors change column  patient_id patient_id char(36);
update tbl_donor_infors set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_donor_infors drop foreign key if exists tbl_donor_infors_facility_id_foreign;
alter table tbl_donor_infors change column  facility_id facility_id char(36);
update tbl_donor_infors set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_past_orthopedic_histories add column if not exists copy_id char(36) after id;
update tbl_past_orthopedic_histories set copy_id = uuid();
alter table tbl_past_orthopedic_histories drop foreign key if exists tbl_past_orthopedic_histories_user_id_foreign;
alter table tbl_past_orthopedic_histories change column  user_id user_id char(36);
update tbl_past_orthopedic_histories set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_past_orthopedic_histories drop foreign key if exists tbl_past_orthopedic_histories_patient_id_foreign;
alter table tbl_past_orthopedic_histories change column  patient_id patient_id char(36);
update tbl_past_orthopedic_histories set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_past_orthopedic_histories drop foreign key if exists tbl_past_orthopedic_histories_facility_id_foreign;
alter table tbl_past_orthopedic_histories change column  facility_id facility_id char(36);
update tbl_past_orthopedic_histories set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_past_orthopedic_histories drop foreign key if exists tbl_past_orthopedic_histories_visit_date_id_foreign;
alter table tbl_past_orthopedic_histories change column  visit_date_id visit_date_id char(36);
update tbl_past_orthopedic_histories set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_past_orthopedic_histories drop foreign key if exists tbl_past_orthopedic_histories_admission_id_foreign;
alter table tbl_past_orthopedic_histories change column  admission_id admission_id char(36);
update tbl_past_orthopedic_histories set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_past_orthopedic_histories change column  past_orthopedic past_orthopedic text;

alter table tbl_ctc_unique_id_patients add column if not exists copy_id char(36) after id;
update tbl_ctc_unique_id_patients set copy_id = uuid();
alter table tbl_ctc_unique_id_patients drop foreign key if exists tbl_ctc_unique_id_patients_user_id_foreign;
alter table tbl_ctc_unique_id_patients change column  user_id user_id char(36);
update tbl_ctc_unique_id_patients set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_ctc_unique_id_patients drop foreign key if exists tbl_ctc_unique_id_patients_patient_id_foreign;
alter table tbl_ctc_unique_id_patients change column  patient_id patient_id char(36);
update tbl_ctc_unique_id_patients set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);

alter table tbl_turning_charts add column if not exists copy_id char(36) after id;
update tbl_turning_charts set copy_id = uuid();
alter table tbl_turning_charts drop foreign key if exists tbl_turning_charts_user_id_foreign;
alter table tbl_turning_charts change column  user_id user_id char(36);
update tbl_turning_charts set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_turning_charts drop foreign key if exists tbl_turning_charts_facility_id_foreign;
alter table tbl_turning_charts change column  facility_id facility_id char(36);
update tbl_turning_charts set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_turning_charts drop foreign key if exists tbl_turning_charts_visit_date_id_foreign;
alter table tbl_turning_charts change column  visit_date_id visit_date_id char(36);
update tbl_turning_charts set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_turning_charts drop foreign key if exists tbl_turning_charts_admission_id_foreign;
alter table tbl_turning_charts change column  admission_id admission_id char(36);
update tbl_turning_charts set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);

alter table tbl_anti_rabies_vaccinations add column if not exists copy_id char(36) after id;
update tbl_anti_rabies_vaccinations set copy_id = uuid();
alter table tbl_anti_rabies_vaccinations drop foreign key if exists tbl_anti_rabies_vaccinations_user_id_foreign;
alter table tbl_anti_rabies_vaccinations change column  user_id user_id char(36);
update tbl_anti_rabies_vaccinations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_rabies_vaccinations drop foreign key if exists tbl_anti_rabies_vaccinations_facility_id_foreign;
alter table tbl_anti_rabies_vaccinations change column  facility_id facility_id char(36);
update tbl_anti_rabies_vaccinations set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_anti_rabies_registries add column if not exists copy_id char(36) after id;
update tbl_anti_rabies_registries set copy_id = uuid();
alter table tbl_anti_rabies_registries drop foreign key if exists tbl_anti_rabies_registries_user_id_foreign;
alter table tbl_anti_rabies_registries change column  user_id user_id char(36);
update tbl_anti_rabies_registries set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_anti_rabies_registries drop foreign key if exists tbl_anti_rabies_registries_facility_id_foreign;
alter table tbl_anti_rabies_registries change column  facility_id facility_id char(36);
update tbl_anti_rabies_registries set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_anti_rabies_registries drop foreign key if exists tbl_anti_rabies_registries_patient_id_foreign;
alter table tbl_anti_rabies_registries change column  patient_id patient_id char(36);
update tbl_anti_rabies_registries set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_anti_rabies_registries drop foreign key if exists tbl_anti_rabies_registries_vaccination_id_foreign;
alter table tbl_anti_rabies_registries change column  vaccination_id vaccination_id char(36);
update tbl_anti_rabies_registries set vaccination_id = (select copy_id from tbl_anti_rabies_vaccinations where tbl_anti_rabies_vaccinations.id =  vaccination_id limit 1);

alter table tbl_therapy_assessments add column if not exists copy_id char(36) after id;
update tbl_therapy_assessments set copy_id = uuid();
alter table tbl_therapy_assessments drop foreign key if exists tbl_therapy_assessments_user_id_foreign;
alter table tbl_therapy_assessments change column  user_id user_id char(36);
update tbl_therapy_assessments set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_therapy_assessments drop foreign key if exists tbl_therapy_assessments_patient_id_foreign;
alter table tbl_therapy_assessments change column  patient_id patient_id char(36);
update tbl_therapy_assessments set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_therapy_assessments drop foreign key if exists tbl_therapy_assessments_facility_id_foreign;
alter table tbl_therapy_assessments change column  facility_id facility_id char(36);
update tbl_therapy_assessments set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_therapy_assessments drop foreign key if exists tbl_therapy_assessments_visit_date_id_foreign;
alter table tbl_therapy_assessments change column  visit_date_id visit_date_id char(36);
update tbl_therapy_assessments set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_therapy_assessments change column  `general` `general` text;
alter table tbl_therapy_assessments change column  `specific` `specific` text;
alter table tbl_therapy_assessments change column  neurological neurological text;
alter table tbl_therapy_assessments change column  summary summary text;

alter table tbl_client_nutritional_statuses add column if not exists copy_id char(36) after id;
update tbl_client_nutritional_statuses set copy_id = uuid();
alter table tbl_client_nutritional_statuses drop foreign key if exists tbl_client_nutritional_statuses_user_id_foreign;
alter table tbl_client_nutritional_statuses change column  user_id user_id char(36);
update tbl_client_nutritional_statuses set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_client_nutritional_statuses drop foreign key if exists tbl_client_nutritional_statuses_patient_id_foreign;
alter table tbl_client_nutritional_statuses change column  patient_id patient_id char(36);
update tbl_client_nutritional_statuses set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_client_nutritional_statuses drop foreign key if exists tbl_client_nutritional_statuses_facility_id_foreign;
alter table tbl_client_nutritional_statuses change column  facility_id facility_id char(36);
update tbl_client_nutritional_statuses set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_client_nutritional_statuses drop foreign key if exists tbl_client_nutritional_statuses_visit_id_foreign;
alter table tbl_client_nutritional_statuses change column  visit_id visit_id char(36);
update tbl_client_nutritional_statuses set visit_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_id limit 1);
alter table tbl_client_nutritional_statuses change column  description description text;

alter table tbl_nutritional_statuses add column if not exists copy_id char(36) after id;
update tbl_nutritional_statuses set copy_id = uuid();
alter table tbl_nutritional_statuses drop foreign key if exists tbl_nutritional_statuses_user_id_foreign;
alter table tbl_nutritional_statuses change column  user_id user_id char(36);
update tbl_nutritional_statuses set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_nutritional_statuses drop foreign key if exists tbl_nutritional_statuses_patient_id_foreign;
alter table tbl_nutritional_statuses change column  patient_id patient_id char(36);
update tbl_nutritional_statuses set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_nutritional_statuses drop foreign key if exists tbl_nutritional_statuses_facility_id_foreign;
alter table tbl_nutritional_statuses change column  facility_id facility_id char(36);
update tbl_nutritional_statuses set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_nutritional_statuses drop foreign key if exists tbl_nutritional_statuses_visit_id_foreign;
alter table tbl_nutritional_statuses change column  visit_id visit_id char(36);
update tbl_nutritional_statuses set visit_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_id limit 1);

alter table tbl_child_birth_histories add column if not exists copy_id char(36) after id;
update tbl_child_birth_histories set copy_id = uuid();
alter table tbl_child_birth_histories drop foreign key if exists tbl_child_birth_histories_birth_history_id_foreign;
alter table tbl_child_birth_histories change column  birth_history_id birth_history_id char(36);
update tbl_child_birth_histories set birth_history_id = (select copy_id from tbl_birth_histories where tbl_birth_histories.id =  birth_history_id limit 1);

alter table tbl_eye_examination_records add column if not exists copy_id char(36) after id;
update tbl_eye_examination_records set copy_id = uuid();
alter table tbl_eye_examination_records drop foreign key if exists tbl_eye_examination_records_clinic_visit_id_foreign;
alter table tbl_eye_examination_records change column  clinic_visit_id clinic_visit_id char(36);
update tbl_eye_examination_records set clinic_visit_id = (select copy_id from tbl_eyeclinic_visits where tbl_eyeclinic_visits.id =  clinic_visit_id limit 1);
alter table tbl_eye_examination_records change column  description description varchar(5000);

alter table tbl_mortuaries add column if not exists copy_id char(36) after id;
update tbl_mortuaries set copy_id = uuid();
alter table tbl_mortuaries drop foreign key if exists tbl_mortuaries_user_id_foreign;
alter table tbl_mortuaries change column  user_id user_id char(36);
update tbl_mortuaries set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_mortuaries drop foreign key if exists tbl_mortuaries_facility_id_foreign;
alter table tbl_mortuaries change column  facility_id facility_id char(36);
update tbl_mortuaries set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_ward_reports add column if not exists copy_id char(36) after id;
update tbl_ward_reports set copy_id = uuid();
alter table tbl_ward_reports drop foreign key if exists tbl_ward_reports_ward_id_foreign;
alter table tbl_ward_reports change column  ward_id ward_id char(36);
update tbl_ward_reports set ward_id = (select copy_id from tbl_wards where tbl_wards.id =  ward_id limit 1);
alter table tbl_ward_reports drop foreign key if exists tbl_ward_reports_facility_id_foreign;
alter table tbl_ward_reports change column  facility_id facility_id char(36);
update tbl_ward_reports set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_nurses_wards add column if not exists copy_id char(36) after id;
update tbl_nurses_wards set copy_id = uuid();
alter table tbl_nurses_wards drop foreign key if exists tbl_nurses_wards_ward_id_foreign;
alter table tbl_nurses_wards change column  ward_id ward_id char(36);
update tbl_nurses_wards set ward_id = (select copy_id from tbl_wards where tbl_wards.id =  ward_id limit 1);
alter table tbl_nurses_wards drop foreign key if exists tbl_nurses_wards_nurse_id_foreign;
alter table tbl_nurses_wards change column  nurse_id nurse_id char(36);
update tbl_nurses_wards set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);
alter table tbl_nurses_wards drop foreign key if exists tbl_nurses_wards_incharge_id_foreign;
alter table tbl_nurses_wards change column  incharge_id incharge_id char(36);
update tbl_nurses_wards set incharge_id = (select copy_id from users where users.id =  incharge_id limit 1);

alter table tbl_beds add column if not exists copy_id char(36) after id;
update tbl_beds set copy_id = uuid();
alter table tbl_beds drop foreign key if exists tbl_beds_ward_id_foreign;
alter table tbl_beds change column  ward_id ward_id char(36);
update tbl_beds set ward_id = (select copy_id from tbl_wards where tbl_wards.id =  ward_id limit 1);
alter table tbl_beds drop foreign key if exists tbl_beds_facility_id_foreign;
alter table tbl_beds change column  facility_id facility_id char(36);
update tbl_beds set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_instructions add column if not exists copy_id char(36) after id;
update tbl_instructions set copy_id = uuid();
alter table tbl_instructions drop foreign key if exists tbl_instructions_user_id_foreign;
alter table tbl_instructions change column  user_id user_id char(36);
update tbl_instructions set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_instructions drop foreign key if exists tbl_instructions_patient_id_foreign;
alter table tbl_instructions change column  patient_id patient_id char(36);
update tbl_instructions set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_instructions drop foreign key if exists tbl_instructions_facility_id_foreign;
alter table tbl_instructions change column  facility_id facility_id char(36);
update tbl_instructions set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_instructions drop foreign key if exists tbl_instructions_admission_id_foreign;
alter table tbl_instructions change column  admission_id admission_id char(36);
update tbl_instructions set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_instructions drop foreign key if exists tbl_instructions_ward_id_foreign;
alter table tbl_instructions change column  ward_id ward_id char(36);
update tbl_instructions set ward_id = (select copy_id from tbl_wards where tbl_wards.id =  ward_id limit 1);
alter table tbl_instructions drop foreign key if exists tbl_instructions_bed_id_foreign;
alter table tbl_instructions change column  bed_id bed_id char(36);
update tbl_instructions set bed_id = (select copy_id from tbl_beds where tbl_beds.id =  bed_id limit 1);

alter table tbl_ctc_transfer_in_particulars add column if not exists copy_id char(36) after id;
update tbl_ctc_transfer_in_particulars set copy_id = uuid();
alter table tbl_ctc_transfer_in_particulars drop foreign key if exists tbl_ctc_transfer_in_particulars_patient_id_foreign;
alter table tbl_ctc_transfer_in_particulars change column  patient_id patient_id char(36);
update tbl_ctc_transfer_in_particulars set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);

alter table tbl_past_medical_records add column if not exists copy_id char(36) after id;
update tbl_past_medical_records set copy_id = uuid();
alter table tbl_past_medical_records drop foreign key if exists tbl_past_medical_records_past_medical_history_id_foreign;
alter table tbl_past_medical_records change column  past_medical_history_id past_medical_history_id char(36);
update tbl_past_medical_records set past_medical_history_id = (select copy_id from tbl_past_medical_histories where tbl_past_medical_histories.id =  past_medical_history_id limit 1);


alter table tbl_marriage_issues add column if not exists copy_id char(36) after id;
update tbl_marriage_issues set copy_id = uuid();
alter table tbl_marriage_issues drop foreign key if exists tbl_marriage_issues_user_id_foreign;
alter table tbl_marriage_issues change column  user_id user_id char(36);
update tbl_marriage_issues set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_marriage_issues drop foreign key if exists tbl_marriage_issues_patient_id_foreign;
alter table tbl_marriage_issues change column  patient_id patient_id char(36);
update tbl_marriage_issues set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_marriage_issues drop foreign key if exists tbl_marriage_issues_facility_id_foreign;
alter table tbl_marriage_issues change column  facility_id facility_id char(36);
update tbl_marriage_issues set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_vendors add column if not exists copy_id char(36) after id;
update tbl_vendors set copy_id = uuid();
alter table tbl_vendors drop foreign key if exists tbl_vendors_facility_id_foreign;
alter table tbl_vendors change column  facility_id facility_id char(36);
update tbl_vendors set facility_id = (select copy_id from tbl_facilities where tbl_vendors.id =  facility_id limit 1);

alter table tbl_complaints add column if not exists copy_id char(36) after id;
update tbl_complaints set copy_id = uuid();
alter table tbl_complaints drop foreign key if exists tbl_complaints_history_exam_id_foreign;
alter table tbl_complaints change column  history_exam_id history_exam_id char(36);
update tbl_complaints set history_exam_id = (select copy_id from tbl_history_examinations where tbl_history_examinations.id =  history_exam_id limit 1);

alter table tbl_permits add column if not exists copy_id char(36) after id;
update tbl_permits set copy_id = uuid();
alter table tbl_permits drop foreign key if exists tbl_permits_facility_id_foreign;
alter table tbl_permits change column  facility_id facility_id char(36);
update tbl_permits set facility_id = (select copy_id from tbl_facilities where tbl_permits.id =  facility_id limit 1);
alter table tbl_permits drop foreign key if exists tbl_permits_corpse_id_foreign;
alter table tbl_permits change column  corpse_id corpse_id char(36);
update tbl_permits set corpse_id = (select copy_id from tbl_corpses where tbl_corpses.id =  corpse_id limit 1);

alter table tbl_social_referrals add column if not exists copy_id char(36) after id;
update tbl_social_referrals set copy_id = uuid();
alter table tbl_social_referrals drop foreign key if exists tbl_social_referrals_user_id_foreign;
alter table tbl_social_referrals change column  user_id user_id char(36);
update tbl_social_referrals set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_social_referrals drop foreign key if exists tbl_social_referrals_patient_id_foreign;
alter table tbl_social_referrals change column  patient_id patient_id char(36);
update tbl_social_referrals set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_social_referrals drop foreign key if exists tbl_social_referrals_facility_id_foreign;
alter table tbl_social_referrals change column  facility_id facility_id char(36);
update tbl_social_referrals set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_treatment_time_flows add column if not exists copy_id char(36) after id;
update tbl_treatment_time_flows set copy_id = uuid();
alter table tbl_treatment_time_flows drop foreign key if exists tbl_treatment_time_flows_treatment_charts_id_foreign;
alter table tbl_treatment_time_flows change column  treatment_charts_id treatment_charts_id char(36);
update tbl_treatment_time_flows set treatment_charts_id = (select copy_id from tbl_treatment_charts where tbl_treatment_charts.id =  treatment_charts_id limit 1);

alter table tbl_nuisance_composes add column if not exists copy_id char(36) after id;
update tbl_nuisance_composes set copy_id = uuid();
alter table tbl_nuisance_composes drop foreign key if exists tbl_nuisance_composes_user_id_foreign;
alter table tbl_nuisance_composes change column  user_id user_id char(36);
update tbl_nuisance_composes set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_nuisance_composes drop foreign key if exists tbl_nuisance_composes_facility_id_foreign;
alter table tbl_nuisance_composes change column  facility_id facility_id char(36);
update tbl_nuisance_composes set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_obs_gyn_records add column if not exists copy_id char(36) after id;
update tbl_obs_gyn_records set copy_id = uuid();
alter table tbl_obs_gyn_records drop foreign key if exists tbl_obs_gyn_records_obs_gyn_id_foreign;
alter table tbl_obs_gyn_records change column  obs_gyn_id obs_gyn_id char(36);
update tbl_obs_gyn_records set obs_gyn_id = (select copy_id from tbl_obs_gyns where tbl_obs_gyns.id =  obs_gyn_id limit 1);

alter table tbl_physical_examination_records add column if not exists copy_id char(36) after id;
update tbl_physical_examination_records set copy_id = uuid();
alter table tbl_physical_examination_records drop foreign key if exists tbl_physical_examination_records_physical_examination_id_foreign;
alter table tbl_physical_examination_records change column  physical_examination_id physical_examination_id char(36);
update tbl_physical_examination_records set physical_examination_id = (select copy_id from tbl_physical_examinations where tbl_physical_examinations.id =  physical_examination_id limit 1);

alter table tbl_exemptions add column if not exists copy_id char(36) after id;
update tbl_exemptions set copy_id = uuid();
alter table tbl_exemptions drop foreign key if exists tbl_exemptions_patient_id_foreign;
alter table tbl_exemptions change column  patient_id patient_id char(36);
update tbl_exemptions set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_exemptions drop foreign key if exists tbl_exemptions_attachment_id_foreign;
alter table tbl_exemptions change column  attachment_id attachment_id char(36);
update tbl_exemptions set attachment_id = (select copy_id from tbl_attachments where tbl_attachments.id =  attachment_id limit 1);
alter table tbl_exemptions drop foreign key if exists tbl_exemptions_user_id_foreign;
alter table tbl_exemptions change column  user_id user_id char(36);
update tbl_exemptions set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_exemptions drop foreign key if exists tbl_exemptions_exemption_type_id_foreign;
alter table tbl_exemptions change column  exemption_type_id exemption_type_id char(36);
update tbl_exemptions set exemption_type_id = (select copy_id from tbl_pay_cat_sub_categories where tbl_pay_cat_sub_categories.id =  user_id limit 1);

alter table tbl_inventory_orders add column if not exists copy_id char(36) after id;
update tbl_inventory_orders set copy_id = uuid();
alter table tbl_inventory_orders drop foreign key if exists tbl_inventory_orders_user_id_foreign;
alter table tbl_inventory_orders change column  user_id user_id char(36);
update tbl_inventory_orders set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_inventory_orders drop foreign key if exists tbl_inventory_orders_facility_id_foreign;
alter table tbl_inventory_orders change column  facility_id facility_id char(36);
update tbl_inventory_orders set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_staff_sections add column if not exists copy_id char(36) after id;
update tbl_staff_sections set copy_id = uuid();
alter table tbl_staff_sections drop foreign key if exists tbl_staff_sections_technologist_id_foreign;
alter table tbl_staff_sections change column  technologist_id technologist_id char(36);
update tbl_staff_sections set technologist_id = (select copy_id from users where users.id =  technologist_id limit 1);

alter table tbl_ledgers add column if not exists copy_id char(36) after id;
update tbl_ledgers set copy_id = uuid();
alter table tbl_ledgers drop foreign key if exists tbl_ledgers_facility_id_foreign;
alter table tbl_ledgers change column  facility_id facility_id char(36);
update tbl_ledgers set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_last_logins add column if not exists copy_id char(36) after id;
update tbl_last_logins set copy_id = uuid();
alter table tbl_last_logins drop foreign key if exists tbl_last_logins_user_id_foreign;
alter table tbl_last_logins change column  user_id user_id char(36);
update tbl_last_logins set user_id = (select copy_id from users where users.id =  user_id limit 1);

alter table tbl_system_perfomances add column if not exists copy_id char(36) after id;
update tbl_system_perfomances set copy_id = uuid();
alter table tbl_system_perfomances drop foreign key if exists tbl_system_perfomances_user_id_foreign;
alter table tbl_system_perfomances change column  user_id user_id char(36);
update tbl_system_perfomances set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_system_perfomances drop foreign key if exists tbl_system_perfomances_login_id_foreign;
alter table tbl_system_perfomances change column  login_id login_id char(36);
update tbl_system_perfomances set login_id = (select copy_id from tbl_last_logins where tbl_last_logins.id =  login_id limit 1);

alter table tbl_last_logins add column if not exists copy_id char(36) after id;
update tbl_last_logins set copy_id = uuid();
alter table tbl_last_logins drop foreign key if exists tbl_last_logins_user_id_foreign;
alter table tbl_last_logins change column  user_id user_id char(36);
update tbl_last_logins set user_id = (select copy_id from users where users.id =  user_id limit 1);

alter table tbl_exemptions add column if not exists copy_id char(36) after id;
update tbl_exemptions set copy_id = uuid();
alter table tbl_exemptions drop foreign key if exists tbl_exemptions_patient_id_foreign;
alter table tbl_exemptions change column  patient_id patient_id char(36);
update tbl_exemptions set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_exemptions drop foreign key if exists tbl_exemptions_attachment_id_foreign;
alter table tbl_exemptions change column  attachment_id attachment_id char(36);
update tbl_exemptions set attachment_id = (select copy_id from tbl_attachments where tbl_attachments.id =  attachment_id limit 1);
alter table tbl_exemptions drop foreign key if exists tbl_exemptions_user_id_foreign;
alter table tbl_exemptions change column  user_id user_id char(36);
update tbl_exemptions set user_id = (select copy_id from users where users.id =  user_id limit 1);

alter table tbl_store_lists add column if not exists copy_id char(36) after id;
update tbl_store_lists set copy_id = uuid();
alter table tbl_store_lists drop foreign key if exists tbl_store_lists_facility_id_foreign;
alter table tbl_store_lists change column  facility_id facility_id char(36);
update tbl_store_lists set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_forensic_histories add column if not exists copy_id char(36) after id;
update tbl_forensic_histories set copy_id = uuid();
alter table tbl_forensic_histories drop foreign key if exists tbl_forensic_histories_user_id_foreign;
alter table tbl_forensic_histories change column  user_id user_id char(36);
update tbl_forensic_histories set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_forensic_histories drop foreign key if exists tbl_forensic_histories_patient_id_foreign;
alter table tbl_forensic_histories change column  patient_id patient_id char(36);
update tbl_forensic_histories set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_forensic_histories drop foreign key if exists tbl_forensic_histories_visit_date_id_foreign;
alter table tbl_forensic_histories change column  visit_date_id visit_date_id char(36);
update tbl_forensic_histories set visit_date_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_date_id limit 1);
alter table tbl_forensic_histories change column  forensic_history forensic_history text;

alter table tbl_equipments add column if not exists copy_id char(36) after id;
update tbl_equipments set copy_id = uuid();
alter table tbl_equipments drop foreign key if exists tbl_equipments_user_id_foreign;
alter table tbl_equipments change column  user_id user_id char(36);
update tbl_equipments set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_equipments drop foreign key if exists tbl_equipments_facility_id_foreign;
alter table tbl_equipments change column  facility_id facility_id char(36);
update tbl_equipments set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_registration_statuses add column if not exists copy_id char(36) after id;
update tbl_registration_statuses set copy_id = uuid();
alter table tbl_registration_statuses drop foreign key if exists tbl_registration_statuses_patient_id_foreign;
alter table tbl_registration_statuses change column  patient_id patient_id char(36);
update tbl_registration_statuses set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);

alter table tbl_environmental_equipment_receivings add column if not exists copy_id char(36) after id;
update tbl_environmental_equipment_receivings set copy_id = uuid();
alter table tbl_environmental_equipment_receivings drop foreign key if exists tbl_environmental_equipment_receivings_user_id_foreign;
alter table tbl_environmental_equipment_receivings change column  user_id user_id char(36);
update tbl_environmental_equipment_receivings set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_environmental_equipment_receivings drop foreign key if exists tbl_environmental_equipment_receivings_facility_id_foreign;
alter table tbl_environmental_equipment_receivings change column  facility_id facility_id char(36);
update tbl_environmental_equipment_receivings set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_environmental_equipment_receivings drop foreign key if exists tbl_environmental_equipment_receivings_equipment_id_foreign;
alter table tbl_environmental_equipment_receivings change column  equipment_id equipment_id char(36);
update tbl_environmental_equipment_receivings set equipment_id = (select copy_id from tbl_equipments where tbl_equipments.id =  equipment_id limit 1);

alter table tbl_permission_users add column if not exists copy_id char(36) after id;
update tbl_permission_users set copy_id = uuid();
alter table tbl_permission_users drop foreign key if exists tbl_permission_users_user_id_foreign;
alter table tbl_permission_users change column  user_id user_id char(36);
update tbl_permission_users set user_id = (select copy_id from users where users.id =  user_id limit 1);

alter table tbl_user_store_configurations add column if not exists copy_id char(36) after id;
update tbl_user_store_configurations set copy_id = uuid();
alter table tbl_user_store_configurations drop foreign key if exists tbl_user_store_configurations_user_id_foreign;
alter table tbl_user_store_configurations change column  user_id user_id char(36);
update tbl_user_store_configurations set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_user_store_configurations drop foreign key if exists tbl_user_store_configurations_store_id_foreign;
alter table tbl_user_store_configurations change column  store_id store_id char(36);
update tbl_user_store_configurations set store_id = (select copy_id from tbl_store_lists where tbl_store_lists.id =  store_id limit 1);

alter table tbl_notifications add column if not exists copy_id char(36) after id;
update tbl_notifications set copy_id = uuid();
alter table tbl_notifications drop foreign key if exists tbl_notifications_sender_id_foreign;
alter table tbl_notifications change column  sender_id sender_id char(36);
update tbl_notifications set sender_id = (select copy_id from users where users.id =  sender_id limit 1);
alter table tbl_notifications drop foreign key if exists tbl_notifications_receiver_id_foreign;
alter table tbl_notifications change column  receiver_id receiver_id char(36);
update tbl_notifications set receiver_id = (select copy_id from users where users.id =  receiver_id limit 1);

alter table tbl_invoices add column if not exists copy_id char(36) after id;
update tbl_invoices set copy_id = uuid();
alter table tbl_invoices drop foreign key if exists tbl_invoices_vendor_id_foreign;
alter table tbl_invoices change column  vendor_id vendor_id char(36);
update tbl_invoices set vendor_id = (select copy_id from tbl_vendors where tbl_vendors.id =  vendor_id limit 1);

alter table tbl_receiving_items add column if not exists copy_id char(36) after id;
update tbl_receiving_items set copy_id = uuid();
alter table tbl_receiving_items drop foreign key if exists tbl_receiving_items_user_id_foreign;
alter table tbl_receiving_items change column  user_id user_id char(36);
update tbl_receiving_items set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_receiving_items drop foreign key if exists tbl_receiving_items_received_store_id_foreign;
alter table tbl_receiving_items change column  received_store_id received_store_id char(36);
update tbl_receiving_items set received_store_id = (select copy_id from tbl_store_lists where tbl_store_lists.id =  received_store_id limit 1);
alter table tbl_receiving_items drop foreign key if exists tbl_receiving_items_requesting_store_id_foreign;
alter table tbl_receiving_items change column  requesting_store_id requesting_store_id char(36);
update tbl_receiving_items set requesting_store_id = (select copy_id from tbl_store_lists where tbl_store_lists.id =  requesting_store_id limit 1);
alter table tbl_receiving_items drop foreign key if exists tbl_receiving_items_received_from_id_foreign;
alter table tbl_receiving_items change column  received_from_id received_from_id char(36);
update tbl_receiving_items set received_from_id = (select copy_id from tbl_vendors where tbl_vendors.id =  received_from_id limit 1);
alter table tbl_receiving_items drop foreign key if exists tbl_receiving_items_facility_id_foreign;
alter table tbl_receiving_items change column  facility_id facility_id char(36);
update tbl_receiving_items set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_receiving_items drop foreign key if exists tbl_receiving_items_attachment_id_foreign;
alter table tbl_receiving_items change column  attachment_id attachment_id char(36);
update tbl_receiving_items set attachment_id = (select copy_id from tbl_attachments where tbl_attachments.id =  attachment_id limit 1);
alter table tbl_receiving_items drop foreign key if exists tbl_receiving_items_internal_issuer_id_foreign;
alter table tbl_receiving_items change column  internal_issuer_id internal_issuer_id char(36);
update tbl_receiving_items set internal_issuer_id = (select copy_id from tbl_store_lists where tbl_store_lists.id =  internal_issuer_id limit 1);

alter table tbl_receiving_items drop foreign key if exists tbl_receiving_items_invoice_refference_foreign;
alter table tbl_receiving_items change column  invoice_refference invoice_refference char(36);
update tbl_receiving_items set invoice_refference = (select copy_id from tbl_invoices where tbl_invoices.id =  invoice_refference limit 1);

alter table tbl_sub_stores add column if not exists copy_id char(36) after id;
update tbl_sub_stores set copy_id = uuid();
alter table tbl_sub_stores drop foreign key if exists tbl_sub_stores_user_id_foreign;
alter table tbl_sub_stores change column  user_id user_id char(36);
update tbl_sub_stores set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_sub_stores drop foreign key if exists tbl_sub_stores_issued_store_id_foreign;
alter table tbl_sub_stores change column  issued_store_id issued_store_id char(36);
update tbl_sub_stores set issued_store_id = (select copy_id from tbl_store_lists where tbl_store_lists.id =  issued_store_id limit 1);
alter table tbl_sub_stores drop foreign key if exists tbl_sub_stores_requested_store_id_foreign;
alter table tbl_sub_stores change column  requested_store_id requested_store_id char(36);
update tbl_sub_stores set requested_store_id = (select copy_id from tbl_store_lists where tbl_store_lists.id =  requested_store_id limit 1);
alter table tbl_sub_stores drop foreign key if exists tbl_sub_stores_received_from_id_foreign;
alter table tbl_sub_stores change column  received_from_id received_from_id char(36);
update tbl_sub_stores set received_from_id = (select copy_id from tbl_store_lists where tbl_store_lists.id =  received_from_id limit 1);

alter table tbl_dispensers add column if not exists copy_id char(36) after id;
update tbl_dispensers set copy_id = uuid();
alter table tbl_dispensers drop foreign key if exists tbl_dispensers_user_id_foreign;
alter table tbl_dispensers change column  user_id user_id char(36);
update tbl_dispensers set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_dispensers drop foreign key if exists tbl_dispensers_dispenser_id_foreign;
alter table tbl_dispensers change column  dispenser_id dispenser_id char(36);
update tbl_dispensers set dispenser_id = (select copy_id from tbl_store_lists where tbl_store_lists.id =  dispenser_id limit 1);
alter table tbl_dispensers drop foreign key if exists tbl_dispensers_received_from_id_foreign;
alter table tbl_dispensers change column  received_from_id received_from_id char(36);
update tbl_dispensers set received_from_id = (select copy_id from tbl_store_lists where tbl_store_lists.id =  received_from_id limit 1);
alter table tbl_dispensers drop foreign key if exists tbl_dispensers_patient_id_foreign;
alter table tbl_dispensers change column  patient_id patient_id char(36);
update tbl_dispensers set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);

alter table tbl_cabinets add column if not exists copy_id char(36) after id;
update tbl_cabinets set copy_id = uuid();
alter table tbl_cabinets drop foreign key if exists tbl_cabinets_user_id_foreign;
alter table tbl_cabinets change column  user_id user_id char(36);
update tbl_cabinets set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_cabinets drop foreign key if exists tbl_cabinets_mortuary_id_foreign;
alter table tbl_cabinets change column  mortuary_id mortuary_id char(36);
update tbl_cabinets set mortuary_id = (select copy_id from tbl_mortuaries where tbl_mortuaries.id =  mortuary_id limit 1);

alter table tbl_corpse_admissions add column if not exists copy_id char(36) after id;
update tbl_corpse_admissions set copy_id = uuid();
alter table tbl_corpse_admissions drop foreign key if exists tbl_corpse_admissions_user_id_foreign;
alter table tbl_corpse_admissions change column  user_id user_id char(36);
update tbl_corpse_admissions set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_corpse_admissions drop foreign key if exists tbl_corpse_admissions_corpse_received_id_foreign;
alter table tbl_corpse_admissions change column  corpse_received_id corpse_received_id char(36);
update tbl_corpse_admissions set corpse_received_id = (select copy_id from users where users.id =  corpse_received_id limit 1);
alter table tbl_corpse_admissions drop foreign key if exists tbl_corpse_admissions_patient_id_foreign;
alter table tbl_corpse_admissions change column  patient_id patient_id char(36);
update tbl_corpse_admissions set patient_id = (select copy_id from tbl_patients where tbl_patients.id = patient_id limit 1);
alter table tbl_corpse_admissions drop foreign key if exists tbl_corpse_admissions_mortuary_id_foreign;
alter table tbl_corpse_admissions change column  mortuary_id mortuary_id char(36);
update tbl_corpse_admissions set mortuary_id = (select copy_id from tbl_mortuaries where tbl_mortuaries.id =  mortuary_id limit 1);
alter table tbl_corpse_admissions drop foreign key if exists tbl_corpse_admissions_cabinet_id_foreign;
alter table tbl_corpse_admissions change column  cabinet_id cabinet_id char(36);
update tbl_corpse_admissions set cabinet_id = (select copy_id from tbl_cabinets where tbl_cabinets.id =  cabinet_id limit 1);
alter table tbl_corpse_admissions drop foreign key if exists tbl_corpse_admissions_facility_id_foreign;
alter table tbl_corpse_admissions change column  facility_id facility_id char(36);
update tbl_corpse_admissions set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_corpse_admissions drop foreign key if exists tbl_corpse_admissions_corpse_id_foreign;
alter table tbl_corpse_admissions change column  corpse_id corpse_id char(36);
update tbl_corpse_admissions set corpse_id = (select copy_id from tbl_corpses where tbl_corpses.id =  corpse_id limit 1);

alter table tbl_corpses add column if not exists copy_id char(36) after id;
update tbl_corpses set copy_id = uuid();
alter table tbl_corpses drop foreign key if exists tbl_corpses_user_id_foreign;
alter table tbl_corpses change column  user_id user_id char(36);
update tbl_corpses set user_id = (select copy_id from users where users.id = user_id limit 1);
alter table tbl_corpses drop foreign key if exists tbl_corpses_facility_id_foreign;
alter table tbl_corpses change column  facility_id facility_id char(36);
update tbl_corpses set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id = facility_id limit 1);

alter table tbl_corpse_services add column if not exists copy_id char(36) after id;
update tbl_corpse_services set copy_id = uuid();
alter table tbl_corpse_services drop foreign key if exists tbl_corpse_services_user_id_foreign;
alter table tbl_corpse_services change column  user_id user_id char(36);
update tbl_corpse_services set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_corpse_services drop foreign key if exists tbl_corpse_services_corpse_admission_id_foreign;
alter table tbl_corpse_services change column  corpse_admission_id corpse_admission_id char(36);
update tbl_corpse_services set corpse_admission_id = (select copy_id from tbl_corpse_admissions where tbl_corpse_admissions.id = corpse_admission_id limit 1);

alter table tbl_review_of_systems add column if not exists copy_id char(36) after id;
update tbl_review_of_systems set copy_id = uuid();
alter table tbl_review_of_systems drop foreign key if exists tbl_review_of_systems_review_system_id_foreign;
alter table tbl_review_of_systems change column  review_system_id review_system_id char(36);
update tbl_review_of_systems set review_system_id = (select copy_id from tbl_review_systems where tbl_review_systems.id =  review_system_id limit 1);

alter table tbl_wards_nurses add column if not exists copy_id char(36) after id;
update tbl_wards_nurses set copy_id = uuid();
alter table tbl_wards_nurses drop foreign key if exists tbl_wards_nurses_nurse_id_foreign;
alter table tbl_wards_nurses change column  nurse_id nurse_id char(36);
update tbl_wards_nurses set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);
alter table tbl_wards_nurses drop foreign key if exists tbl_wards_nurses_ward_id_foreign;
alter table tbl_wards_nurses change column  ward_id ward_id char(36);
update tbl_wards_nurses set ward_id = (select copy_id from tbl_wards where tbl_wards.id = ward_id limit 1);

alter table tbl_registrar_services add column if not exists copy_id char(36) after id;
update tbl_registrar_services set copy_id = uuid();
alter table tbl_registrar_services drop foreign key if exists tbl_registrar_services_facility_id_foreign;
alter table tbl_registrar_services change column  facility_id facility_id char(36);
update tbl_registrar_services set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_exemption_accesses add column if not exists copy_id char(36) after id;
update tbl_exemption_accesses set copy_id = uuid();
alter table tbl_exemption_accesses drop foreign key if exists tbl_exemption_accesses_user_id_foreign;
alter table tbl_exemption_accesses change column  user_id user_id char(36);
update tbl_exemption_accesses set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_exemption_accesses drop foreign key if exists tbl_exemption_accesses_exempt_id_foreign;
alter table tbl_exemption_accesses change column  exempt_id exempt_id char(36);
update tbl_exemption_accesses set exempt_id = (select copy_id from tbl_pay_cat_sub_categories where tbl_pay_cat_sub_categories.id =  exempt_id limit 1);

alter table tbl_sample_number_controls add column if not exists copy_id char(36) after id;
update tbl_sample_number_controls set copy_id = uuid();
alter table tbl_sample_number_controls drop foreign key if exists tbl_sample_number_controls_user_id_foreign;
alter table tbl_sample_number_controls change column  user_id user_id char(36);
update tbl_sample_number_controls set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_sample_number_controls drop foreign key if exists tbl_sample_number_controls_facility_id_foreign;
alter table tbl_sample_number_controls change column  facility_id facility_id char(36);
update tbl_sample_number_controls set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_testspanels add column if not exists copy_id char(36) after id;
update tbl_testspanels set copy_id = uuid();
alter table tbl_testspanels drop foreign key if exists tbl_testspanels_user_id_foreign;
alter table tbl_testspanels change column  user_id user_id char(36);
update tbl_testspanels set user_id = (select copy_id from users where users.id =  user_id limit 1);
alter table tbl_testspanels drop foreign key if exists tbl_testspanels_equipment_id_foreign;
alter table tbl_testspanels change column  equipment_id equipment_id char(36);
update tbl_testspanels set equipment_id = (select copy_id from tbl_equipments where tbl_equipments.id =  equipment_id limit 1);

alter table tbl_tests add column if not exists copy_id char(36) after id;
update tbl_tests set copy_id = uuid();
alter table tbl_tests drop foreign key if exists tbl_tests_equipment_id_foreign;
alter table tbl_tests change column  equipment_id equipment_id char(36);
update tbl_tests set equipment_id = (select copy_id from tbl_equipments where tbl_equipments.id =  equipment_id limit 1);

alter table tbl_nurse_wards add column if not exists copy_id char(36) after id;
update tbl_nurse_wards set copy_id = uuid();
alter table tbl_nurse_wards drop foreign key if exists tbl_nurse_wards_nurse_id_foreign;
alter table tbl_nurse_wards change column  nurse_id nurse_id char(36);
update tbl_nurse_wards set nurse_id = (select copy_id from users where users.id =  nurse_id limit 1);
alter table tbl_nurse_wards drop foreign key if exists tbl_nurse_wards_ward_id_foreign;
alter table tbl_nurse_wards change column  ward_id ward_id char(36);
update tbl_nurse_wards set ward_id = (select copy_id from tbl_wards where tbl_wards.id = ward_id limit 1);
alter table tbl_nurse_wards drop foreign key if exists tbl_nurse_wards_facility_id_foreign;
alter table tbl_nurse_wards change column  facility_id facility_id char(36);
update tbl_nurse_wards set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);

alter table tbl_family_social_histories add column if not exists copy_id char(36) after id;
update tbl_family_social_histories set copy_id = uuid();
alter table tbl_family_social_histories drop foreign key if exists tbl_family_social_histories_family_history_id_foreign;
alter table tbl_family_social_histories change column  family_history_id family_history_id char(36);
update tbl_family_social_histories set family_history_id = (select copy_id from tbl_family_histories where tbl_family_histories.id =  family_history_id limit 1);

alter table tbl_emergency_survey_histories add column if not exists copy_id char(36) after id;
update tbl_emergency_survey_histories set copy_id = uuid();
alter table tbl_emergency_survey_histories drop foreign key if exists tbl_emergency_survey_histories_survey_history_id_foreign;
alter table tbl_emergency_survey_histories change column  survey_history_id survey_history_id char(36);
update tbl_emergency_survey_histories set survey_history_id = (select copy_id from tbl_survey_histories where tbl_survey_histories.id =  survey_history_id limit 1);


alter table tbl_inventory_items add column if not exists copy_id char(36) after id;
update tbl_inventory_items set copy_id = uuid();
alter table tbl_inventory_items drop foreign key if exists tbl_inventory_items_item_type_id_foreign;
alter table tbl_inventory_items change column  item_type_id item_type_id char(36);
update tbl_inventory_items set item_type_id = (select copy_id from tbl_ledgers where tbl_ledgers.id =  item_type_id limit 1);


alter table tbl_inventory_receivings add column if not exists copy_id char(36) after id;
update tbl_inventory_receivings set copy_id = uuid();
alter table tbl_inventory_receivings drop foreign key if exists tbl_inventory_receivings_order_number_foreign;
alter table tbl_inventory_receivings change column  order_number order_number char(36);
update tbl_inventory_receivings set order_number = (select copy_id from tbl_inventory_orders where tbl_inventory_orders.id =  order_number limit 1);
alter table tbl_inventory_receivings drop foreign key if exists tbl_inventory_receivings_item_id_foreign;
alter table tbl_inventory_receivings change column  item_id item_id char(36);
update tbl_inventory_receivings set item_id = (select copy_id from tbl_inventory_items where tbl_inventory_items.id =  item_id limit 1);


alter table tbl_inventory_requests add column if not exists copy_id char(36) after id;
update tbl_inventory_requests set copy_id = uuid();
alter table tbl_inventory_requests drop foreign key if exists tbl_inventory_requests_facility_id_foreign;
alter table tbl_inventory_requests change column  facility_id facility_id char(36);
update tbl_inventory_requests set facility_id = (select copy_id from tbl_facilities where tbl_inventory_requests.id =  facility_id limit 1);
alter table tbl_inventory_requests drop foreign key if exists tbl_inventory_requests_item_id_foreign;
alter table tbl_inventory_requests change column  item_id item_id char(36);
update tbl_inventory_requests set item_id = (select copy_id from tbl_inventory_items where tbl_inventory_items.id =  item_id limit 1);

alter table tbl_inventory_issuings add column if not exists copy_id char(36) after id;
update tbl_inventory_issuings set copy_id = uuid();
alter table tbl_inventory_issuings drop foreign key if exists tbl_inventory_issuings_issuing_officer_id_foreign;
alter table tbl_inventory_issuings change column  issuing_officer_id issuing_officer_id char(36);
update tbl_inventory_issuings set issuing_officer_id = (select copy_id from users where users.id =  issuing_officer_id limit 1);
alter table tbl_inventory_issuings drop foreign key if exists tbl_inventory_issuings_receiver_id_foreign;
alter table tbl_inventory_issuings change column  receiver_id receiver_id char(36);
update tbl_inventory_issuings set receiver_id = (select copy_id from tbl_equipments where tbl_equipments.id =  receiver_id limit 1);
alter table tbl_inventory_issuings drop foreign key if exists tbl_inventory_issuings_item_received_id_foreign;
alter table tbl_inventory_issuings change column  item_received_id item_received_id char(36);
update tbl_inventory_issuings set item_received_id = (select copy_id from tbl_inventory_receivings where tbl_inventory_receivings.id =  item_received_id limit 1);

alter table tbl_service_outof_stock add column if not exists copy_id char(36) after id;
update tbl_service_outof_stock set copy_id = uuid();
alter table tbl_service_outof_stock drop foreign key if exists tbl_service_outof_stock_order_id_foreign;
alter table tbl_service_outof_stock change column  order_id order_id char(36);
update tbl_service_outof_stock set order_id = (select copy_id from tbl_requests where tbl_requests.id =  order_id limit 1);
alter table tbl_service_outof_stock drop foreign key if exists tbl_service_outof_stock_test_id_foreign;
alter table tbl_service_outof_stock change column  test_id test_id char(36);
update tbl_service_outof_stock set test_id = (select copy_id from tbl_tests where tbl_tests.id =  test_id limit 1);

alter table tbl_orders add column if not exists copy_id char(36) after id;
update tbl_orders set copy_id = uuid();
alter table tbl_orders drop foreign key if exists tbl_orders_receiver_id_foreign;
alter table tbl_orders change column  receiver_id receiver_id char(36);
update tbl_orders set receiver_id = (select copy_id from users where users.id =  receiver_id limit 1);
alter table tbl_orders drop foreign key if exists tbl_orders_processor_id_foreign;
alter table tbl_orders change column  processor_id processor_id char(36);
update tbl_orders set processor_id = (select copy_id from users where users.id =  processor_id limit 1);
alter table tbl_orders drop foreign key if exists tbl_orders_order_validator_id_foreign;
alter table tbl_orders change column  order_validator_id order_validator_id char(36);
update tbl_orders set order_validator_id = (select copy_id from users where users.id =  order_validator_id limit 1);
alter table tbl_orders drop foreign key if exists tbl_orders_order_id_foreign;
alter table tbl_orders change column  order_id order_id char(36);
update tbl_orders set order_id = (select copy_id from tbl_tests where tbl_tests.id =  order_id limit 1);

alter table tbl_panel_components_results add column if not exists copy_id char(36) after id;
update tbl_panel_components_results set copy_id = uuid();
alter table tbl_panel_components_results drop foreign key if exists tbl_panel_components_results_component_id_foreign;
alter table tbl_panel_components_results change column  component_id component_id char(36);
update tbl_panel_components_results set component_id = (select copy_id from tbl_tests where tbl_tests.id =  component_id limit 1);
alter table tbl_panel_components_results drop foreign key if exists tbl_panel_components_results_order_id_foreign;
alter table tbl_panel_components_results change column  order_id order_id char(36);
update tbl_panel_components_results set order_id = (select copy_id from tbl_orders where tbl_orders.id =  order_id limit 1);

alter table tbl_patient_discharges_payments add column if not exists copy_id char(36) after id;
update tbl_patient_discharges_payments set copy_id = uuid();
alter table tbl_patient_discharges_payments drop foreign key if exists tbl_patient_discharges_payments_admission_id_foreign;
alter table tbl_patient_discharges_payments change column  admission_id admission_id char(36);
update tbl_patient_discharges_payments set admission_id = (select copy_id from tbl_admissions where tbl_admissions.id =  admission_id limit 1);
alter table tbl_patient_discharges_payments drop foreign key if exists tbl_patient_discharges_payments_item_transaction_id_foreign;
alter table tbl_patient_discharges_payments change column  item_transaction_id item_transaction_id char(36);
update tbl_patient_discharges_payments set item_transaction_id = (select copy_id from tbl_encounter_invoices where tbl_encounter_invoices.id =  item_transaction_id limit 1);


alter table tbl_cash_deposits add column if not exists copy_id char(36) after id;
update tbl_cash_deposits set copy_id = uuid();
alter table tbl_cash_deposits drop foreign key if exists tbl_cash_deposits_facility_id_foreign;
alter table tbl_cash_deposits change column  facility_id facility_id char(36);
update tbl_cash_deposits set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_cash_deposits drop foreign key if exists tbl_cash_deposits_user_id_foreign;
alter table tbl_cash_deposits change column  user_id user_id char(36);
update tbl_cash_deposits set user_id = (select copy_id from users where users.id =  user_id limit 1);


alter table tbl_dtcs add column if not exists copy_id char(36) after id;
update tbl_dtcs set copy_id = uuid();
alter table tbl_dtcs drop foreign key if exists tbl_dtcs_patient_id_foreign;
alter table tbl_dtcs change column  patient_id patient_id char(36);
update tbl_dtcs set patient_id = (select copy_id from tbl_patients where tbl_patients.id =  patient_id limit 1);
alter table tbl_dtcs drop foreign key if exists tbl_dtcs_visit_id_foreign;
alter table tbl_dtcs change column  visit_id visit_id char(36);
update tbl_dtcs set visit_id = (select copy_id from tbl_accounts_numbers where tbl_accounts_numbers.id =  visit_id limit 1);
alter table tbl_dtcs drop foreign key if exists tbl_dtcs_facility_id_foreign;
alter table tbl_dtcs change column  facility_id facility_id char(36);
update tbl_dtcs set facility_id = (select copy_id from tbl_facilities where tbl_facilities.id =  facility_id limit 1);
alter table tbl_dtcs drop foreign key if exists tbl_dtcs_user_id_foreign;
alter table tbl_dtcs change column  user_id user_id char(36);
update tbl_dtcs set user_id = (select copy_id from users where users.id =  user_id limit 1);



-- ++++++++++++++++++++++++++++++BRING BACk KEYS +++++++++++++++++++++++++++

alter table tbl_facilities change column  council_id council_id int(10) unsigned null default null;
update tbl_facilities set council_id = null where not exists (select * from tbl_councils where id=council_id);
alter table tbl_facilities change column  region_id region_id int(10) unsigned null default null;
update tbl_facilities set region_id = null where not exists (select * from tbl_regions where id=region_id);


alter table tbl_patients change column  tribe_id tribe_id int(10) unsigned null default null;
alter table tbl_patients change column  country_id country_id int(10) unsigned null default null;
alter table tbl_patients change column  residence_id residence_id int(10) unsigned null default null;
update tbl_patients set tribe_id = null where not exists (select * from tbl_tribes where id=tribe_id);
update tbl_patients set country_id = null where not exists (select * from tbl_countries where id=country_id);
update tbl_patients set residence_id = null where not exists (select * from tbl_residences where id=residence_id);


alter table tbl_facilities change column  id id char(36);
update tbl_facilities set id = copy_id;
-- alter table tbl_facilities add foreign key if not exists tbl_facilities_council_id_foreign ( council_id ) references tbl_councils(id);
-- alter table tbl_facilities add foreign key if not exists tbl_facilities_region_id_foreign ( region_id ) references tbl_regions(id);

alter table users change column  id id char(36);
update users set id = copy_id;

alter table tbl_patients change column  id id char(36);
update tbl_patients set id = copy_id;

alter table tbl_accounts_numbers change column  id id char(36);
update tbl_accounts_numbers set id = copy_id;

alter table users add foreign key if not exists users_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_patients add foreign key if not exists tbl_patients_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_patients add foreign key if not exists tbl_patients_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_accounts_numbers add foreign key if not exists tbl_accounts_numbers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_accounts_numbers add foreign key if not exists tbl_accounts_numbers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_accounts_numbers add foreign key if not exists tbl_accounts_numbers_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;

alter table tbl_corpses change id id char(36);
update tbl_corpses set id = copy_id;
alter table tbl_corpses add foreign key if not exists tbl_corpses_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_corpses add foreign key if not exists tbl_corpses_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_encounter_invoices change column  id id char(36);
update tbl_encounter_invoices set id = copy_id;
alter table tbl_encounter_invoices add foreign key if not exists tbl_encounter_invoices_account_number_id_foreign ( account_number_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_encounter_invoices add foreign key if not exists tbl_encounter_invoices_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_encounter_invoices add foreign key if not exists tbl_encounter_invoices_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_encounter_invoices add foreign key if not exists tbl_encounter_invoices_corpse_id_foreign ( corpse_id ) references tbl_corpses(id) on update cascade;

alter table tbl_pay_cat_sub_categories change column  id id char(36);
update tbl_pay_cat_sub_categories set id = copy_id;
alter table tbl_pay_cat_sub_categories add foreign key if not exists tbl_pay_cat_sub_categories_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_item_prices change column  id id char(36);
update tbl_item_prices set id = copy_id;
alter table tbl_item_prices add foreign key if not exists tbl_item_prices_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_item_prices add foreign key if not exists tbl_item_prices_sub_category_id_foreign ( sub_category_id ) references tbl_pay_cat_sub_categories(id) on update cascade;

alter table tbl_invoice_lines change column  id id char(36);
update tbl_invoice_lines set id = copy_id;
alter table tbl_invoice_lines add foreign key if not exists tbl_invoice_lines_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_invoice_lines add foreign key if not exists tbl_invoice_lines_invoice_id_foreign ( invoice_id ) references tbl_encounter_invoices(id) on update cascade;
alter table tbl_invoice_lines add foreign key if not exists tbl_invoice_lines_corpse_id_foreign ( corpse_id ) references tbl_corpses(id) on update cascade;
alter table tbl_invoice_lines add foreign key if not exists tbl_invoice_lines_item_price_id_foreign ( item_price_id ) references tbl_item_prices(id) on update cascade;


alter table tbl_invoice_lines add foreign key if not exists tbl_invoice_lines_item_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_invoice_lines add foreign key if not exists tbl_invoice_lines_discount_by_foreign ( discount_by ) references users(id) on update cascade;
alter table tbl_invoice_lines add foreign key if not exists tbl_invoice_lines_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_discount_reasons change column  id id char(36);
update tbl_discount_reasons set id = copy_id;
alter table tbl_discount_reasons add foreign key if not exists tbl_discount_reasons_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_discount_reasons add foreign key if not exists tbl_discount_reasons_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_discount_reasons add foreign key if not exists tbl_discount_reasons_receipt_number_foreign ( receipt_number ) references tbl_encounter_invoices(id) on update cascade;


alter table tbl_admissions change column  id id char(36);
update tbl_admissions set id = copy_id;
alter table tbl_admissions add foreign key if not exists tbl_admissions_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_admissions add foreign key if not exists tbl_admissions_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_admissions add foreign key if not exists tbl_admissions_account_id_foreign ( account_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_admissions add foreign key if not exists tbl_admissions_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_admission_status_tracks change column  id id char(36);
update tbl_admission_status_tracks set id = copy_id;
alter table tbl_admission_status_tracks add foreign key if not exists tbl_admission_status_tracks_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_admission_status_tracks add foreign key if not exists tbl_admission_status_tracks_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_ipdtreatments change column  id id char(36);
update tbl_ipdtreatments set id = copy_id;
alter table tbl_ipdtreatments add foreign key if not exists tbl_ipdtreatments_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_ipdtreatments add foreign key if not exists tbl_ipdtreatments_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_ipdtreatments add foreign key if not exists tbl_ipdtreatments_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_ipdtreatments add foreign key if not exists tbl_ipdtreatments_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_client_violences change column  id id char(36);
update tbl_client_violences set id = copy_id;
alter table tbl_client_violences add foreign key if not exists tbl_client_violences_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_client_violences add foreign key if not exists tbl_client_violences_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_client_violences add foreign key if not exists tbl_client_violences_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;

alter table tbl_diagnoses change column  id id char(36);
update tbl_diagnoses set id = copy_id;
alter table tbl_diagnoses add foreign key if not exists tbl_diagnoses_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_diagnoses add foreign key if not exists tbl_diagnoses_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_diagnoses add foreign key if not exists tbl_diagnoses_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_diagnoses add foreign key if not exists tbl_diagnoses_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_diagnoses add foreign key if not exists tbl_diagnoses_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_investigations change column  id id char(36);
update tbl_investigations set id = copy_id;
alter table tbl_investigations add foreign key if not exists tbl_investigations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_investigations add foreign key if not exists tbl_investigations_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_investigations add foreign key if not exists tbl_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_investigations add foreign key if not exists tbl_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_investigations add foreign key if not exists tbl_investigations_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_obs_gyns change column  id id char(36);
update tbl_obs_gyns set id = copy_id;
alter table tbl_obs_gyns add foreign key if not exists tbl_obs_gyns_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_obs_gyns add foreign key if not exists tbl_obs_gyns_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_obs_gyns add foreign key if not exists tbl_obs_gyns_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_obs_gyns add foreign key if not exists tbl_obs_gyns_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_obs_gyns add foreign key if not exists tbl_obs_gyns_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_patient_procedures change column  id id char(36);
update tbl_patient_procedures set id = copy_id;
alter table tbl_patient_procedures add foreign key if not exists tbl_patient_procedures_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_patient_procedures add foreign key if not exists tbl_patient_procedures_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_patient_procedures add foreign key if not exists tbl_patient_procedures_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_patient_procedures add foreign key if not exists tbl_patient_procedures_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_survey_histories change column  id id char(36);
update tbl_survey_histories set id = copy_id;
alter table tbl_survey_histories add foreign key if not exists tbl_survey_histories_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_survey_histories add foreign key if not exists tbl_survey_histories_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_survey_histories add foreign key if not exists tbl_survey_histories_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_survey_histories add foreign key if not exists tbl_survey_histories_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_survey_histories add foreign key if not exists tbl_survey_histories_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_physical_examinations change column  id id char(36);
update tbl_physical_examinations set id = copy_id;
alter table tbl_physical_examinations add foreign key if not exists tbl_physical_examinations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_physical_examinations add foreign key if not exists tbl_physical_examinations_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_physical_examinations add foreign key if not exists tbl_physical_examinations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_physical_examinations add foreign key if not exists tbl_physical_examinations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_physical_examinations add foreign key if not exists tbl_physical_examinations_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_vct_registers change column  id id char(36);
update tbl_vct_registers set id = copy_id;
alter table tbl_vct_registers add foreign key if not exists tbl_vct_registers_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_vct_registers add foreign key if not exists tbl_vct_registers_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_bills_categories change column  id id char(36);
update tbl_bills_categories set id = copy_id;
alter table tbl_bills_categories add foreign key if not exists tbl_bills_categories_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_bills_categories add foreign key if not exists tbl_bills_categories_account_id_foreign ( account_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_bills_categories add foreign key if not exists tbl_bills_categories_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_child_referrals change column  id id char(36);
update tbl_child_referrals set id = copy_id;
alter table tbl_child_referrals add foreign key if not exists tbl_child_referrals_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_child_referrals add foreign key if not exists tbl_child_referrals_mother_id_foreign ( mother_id ) references tbl_patients(id) on update cascade;
alter table tbl_child_referrals add foreign key if not exists tbl_child_referrals_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_child_referrals add foreign key if not exists tbl_child_referrals_transfered_institution_id_foreign ( transfered_institution_id ) references tbl_facilities(id) on update cascade;
alter table tbl_child_referrals add foreign key if not exists tbl_child_referrals_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_past_medical_histories change column  id id char(36);
update tbl_past_medical_histories set id = copy_id;
alter table tbl_past_medical_histories add foreign key if not exists tbl_past_medical_histories_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_past_medical_histories add foreign key if not exists tbl_past_medical_histories_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_past_medical_histories add foreign key if not exists tbl_past_medical_histories_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_past_medical_histories add foreign key if not exists tbl_past_medical_histories_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_past_medical_histories add foreign key if not exists tbl_past_medical_histories_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_child_vaccination_registers change column  id id char(36);
update tbl_child_vaccination_registers set id = copy_id;

alter table tbl_child_vaccination_registers add foreign key if not exists tbl_child_vaccination_registers_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_child_vaccination_registers add foreign key if not exists tbl_child_vaccination_registers_mother_id_foreign ( mother_id ) references tbl_patients(id) on update cascade;
alter table tbl_child_vaccination_registers add foreign key if not exists tbl_child_vaccination_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_child_vaccination_registers add foreign key if not exists tbl_child_vaccination_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_anti_natal_registers change column  id id char(36);
update tbl_anti_natal_registers set id = copy_id;
alter table tbl_anti_natal_registers add foreign key if not exists tbl_anti_natal_registers_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_anti_natal_registers add foreign key if not exists tbl_anti_natal_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_registers add foreign key if not exists tbl_anti_natal_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_review_systems change column  id id char(36);
update tbl_review_systems set id = copy_id;
alter table tbl_review_systems add foreign key if not exists tbl_review_systems_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_review_systems add foreign key if not exists tbl_review_systems_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_review_systems add foreign key if not exists tbl_review_systems_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_review_systems add foreign key if not exists tbl_review_systems_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_review_systems add foreign key if not exists tbl_review_systems_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_history_examinations change column  id id char(36);
update tbl_history_examinations set id = copy_id;
alter table tbl_history_examinations add foreign key if not exists tbl_history_examinations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_history_examinations add foreign key if not exists tbl_history_examinations_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_history_examinations add foreign key if not exists tbl_history_examinations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_history_examinations add foreign key if not exists tbl_history_examinations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_history_examinations add foreign key if not exists tbl_history_examinations_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_eyeclinic_visits change column  id id char(36);
update tbl_eyeclinic_visits set id = copy_id;
alter table tbl_eyeclinic_visits add foreign key if not exists tbl_eyeclinic_visits_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_eyeclinic_visits add foreign key if not exists tbl_eyeclinic_visits_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_eyeclinic_visits add foreign key if not exists tbl_eyeclinic_visits_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_eyeclinic_visits add foreign key if not exists tbl_eyeclinic_visits_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_eyeclinic_visits add foreign key if not exists tbl_eyeclinic_visits_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_comma_scales change column  id id char(36);
update tbl_comma_scales set id = copy_id;
alter table tbl_comma_scales add foreign key if not exists tbl_comma_scales_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_comma_scales add foreign key if not exists tbl_comma_scales_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_comma_scales add foreign key if not exists tbl_comma_scales_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_comma_scales add foreign key if not exists tbl_comma_scales_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_comma_scales add foreign key if not exists tbl_comma_scales_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_comma_scales change column  id id char(36);
update tbl_comma_scales set id = copy_id;
alter table tbl_comma_scales add foreign key if not exists tbl_comma_scales_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_comma_scales add foreign key if not exists tbl_comma_scales_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_comma_scales add foreign key if not exists tbl_comma_scales_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_comma_scales add foreign key if not exists tbl_comma_scales_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_comma_scales add foreign key if not exists tbl_comma_scales_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_requests change column  id id char(36);
update tbl_requests set id = copy_id;
alter table tbl_requests add foreign key if not exists tbl_requests_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_requests add foreign key if not exists tbl_requests_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_requests add foreign key if not exists tbl_requests_doctor_id_foreign ( doctor_id ) references users(id) on update cascade;
alter table tbl_requests add foreign key if not exists tbl_requests_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_clinic_instructions change column  id id char(36);
update tbl_clinic_instructions set id = copy_id;
alter table tbl_clinic_instructions add foreign key if not exists tbl_clinic_instructions_visit_id_foreign ( visit_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_clinic_instructions add foreign key if not exists tbl_clinic_instructions_specialist_id_foreign ( specialist_id ) references users(id) on update cascade;
alter table tbl_clinic_instructions add foreign key if not exists tbl_clinic_instructions_doctor_requesting_id_foreign ( doctor_requesting_id ) references users(id) on update cascade;

alter table tbl_results change column  id id char(36);
update tbl_results set id = copy_id;
alter table tbl_results add foreign key if not exists tbl_results_order_id_foreign ( order_id ) references tbl_requests(id) on update cascade;
alter table tbl_results add foreign key if not exists tbl_results_post_user_foreign ( post_user ) references users(id) on update cascade;
alter table tbl_results add foreign key if not exists tbl_results_verify_user_foreign ( verify_user ) references users(id) on update cascade;

alter table tbl_family_planning_registers change column  id id char(36);
update tbl_family_planning_registers set id = copy_id;
alter table tbl_family_planning_registers add foreign key if not exists tbl_family_planning_registers_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_family_planning_registers add foreign key if not exists tbl_family_planning_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_family_planning_registers add foreign key if not exists tbl_family_planning_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_ctc_patient_visits change column  id id char(36);
update tbl_ctc_patient_visits set id = copy_id;
alter table tbl_ctc_patient_visits add foreign key if not exists tbl_ctc_patient_visits_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_ctc_patient_visits add foreign key if not exists tbl_ctc_patient_visits_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_ctc_patient_visits add foreign key if not exists tbl_ctc_patient_visits_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_unavailable_tests change column  id id char(36);
update tbl_unavailable_tests set id = copy_id;
alter table tbl_unavailable_tests add foreign key if not exists tbl_unavailable_tests_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_unavailable_tests add foreign key if not exists tbl_unavailable_tests_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_unavailable_tests add foreign key if not exists tbl_unavailable_tests_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_unavailable_tests add foreign key if not exists tbl_unavailable_tests_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_blood_requests change column  id id char(36);
update tbl_blood_requests set id = copy_id;
alter table tbl_blood_requests add foreign key if not exists tbl_blood_requests_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_blood_requests add foreign key if not exists tbl_blood_requests_visit_id_foreign ( visit_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_blood_requests add foreign key if not exists tbl_blood_requests_requested_by_foreign ( requested_by  ) references users(id) on update cascade;
alter table tbl_blood_requests add foreign key if not exists tbl_blood_requests_processed_by_foreign ( processed_by ) references users(id) on update cascade;
alter table tbl_blood_requests add foreign key if not exists tbl_blood_requests_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_referrals change column  id id char(36);
update tbl_referrals set id = copy_id;
alter table tbl_referrals add foreign key if not exists tbl_referrals_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_referrals add foreign key if not exists tbl_referrals_visit_id_foreign ( visit_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_referrals add foreign key if not exists tbl_referrals_sender_id_foreign ( sender_id ) references users(id) on update cascade;
alter table tbl_referrals add foreign key if not exists tbl_referrals_from_facility_id_foreign ( from_facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_referrals add foreign key if not exists tbl_referrals_to_facility_id_foreign ( to_facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_past_medical_historys change column  id id char(36);
update tbl_past_medical_historys set id = copy_id;
alter table tbl_past_medical_historys add foreign key if not exists tbl_past_medical_historys_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_past_medical_historys add foreign key if not exists tbl_past_medical_historys_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_past_medical_historys add foreign key if not exists tbl_past_medical_historys_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_past_medical_historys add foreign key if not exists tbl_past_medical_historys_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_family_histories change column  id id char(36);
update tbl_family_histories set id = copy_id;
alter table tbl_family_histories add foreign key if not exists tbl_family_histories_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_family_histories add foreign key if not exists tbl_family_histories_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_family_histories add foreign key if not exists tbl_family_histories_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_family_histories add foreign key if not exists tbl_family_histories_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_family_histories add foreign key if not exists tbl_family_histories_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_diagnosis_details change column  id id char(36);
update tbl_diagnosis_details set id = copy_id;
alter table tbl_diagnosis_details add foreign key if not exists tbl_diagnosis_details_diagnosis_id_foreign ( diagnosis_id ) references tbl_diagnoses(id) on update cascade;

alter table tbl_observation_charts change column  id id char(36);
update tbl_observation_charts set id = copy_id;
alter table tbl_observation_charts add foreign key if not exists tbl_observation_charts_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_status_procedures change column  id id char(36);
update tbl_status_procedures set id = copy_id;
alter table tbl_status_procedures add foreign key if not exists tbl_status_procedures_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_status_procedures add foreign key if not exists tbl_status_procedures_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_status_procedures add foreign key if not exists tbl_status_procedures_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_status_procedures add foreign key if not exists tbl_status_procedures_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_user_roles change column  id id char(36);
update tbl_user_roles set id = copy_id;
alter table tbl_user_roles add foreign key if not exists tbl_user_roles_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_donor_investigations change column  id id char(36);
update tbl_donor_investigations set id = copy_id;
alter table tbl_donor_investigations add foreign key if not exists tbl_donor_investigations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_donor_investigations add foreign key if not exists tbl_donor_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_donor_investigations add foreign key if not exists tbl_donor_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_death_conditions change column  id id char(36);
update tbl_death_conditions set id = copy_id;
alter table tbl_death_conditions add foreign key if not exists tbl_death_conditions_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_death_conditions add foreign key if not exists tbl_death_conditions_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_death_conditions add foreign key if not exists tbl_death_conditions_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_death_conditions add foreign key if not exists tbl_death_conditions_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_outputs change column  id id char(36);
update tbl_outputs set id = copy_id;
alter table tbl_outputs add foreign key if not exists tbl_outputs_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_outputs add foreign key if not exists tbl_outputs_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_outputs add foreign key if not exists tbl_outputs_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_outputs add foreign key if not exists tbl_outputs_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_therapy_treatments change column  id id char(36);
update tbl_therapy_treatments set id = copy_id;
alter table tbl_therapy_treatments add foreign key if not exists tbl_therapy_treatments_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_therapy_treatments add foreign key if not exists tbl_therapy_treatments_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_therapy_treatments add foreign key if not exists tbl_therapy_treatments_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_waste_dispositions change column  id id char(36);
update tbl_waste_dispositions set id = copy_id;
alter table tbl_waste_dispositions add foreign key if not exists tbl_waste_dispositions_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_waste_dispositions add foreign key if not exists tbl_waste_dispositions_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_serious_patients change column  id id char(36);
update tbl_serious_patients set id = copy_id;
alter table tbl_serious_patients add foreign key if not exists tbl_serious_patients_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_serious_patients add foreign key if not exists tbl_serious_patients_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_serious_patients add foreign key if not exists tbl_serious_patients_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_serious_patients add foreign key if not exists tbl_serious_patients_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_environmental_waste_collections change column  id id char(36);
update tbl_environmental_waste_collections set id = copy_id;
alter table tbl_environmental_waste_collections add foreign key if not exists tbl_environmental_waste_collections_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_environmental_waste_collections add foreign key if not exists tbl_environmental_waste_collections_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_observation_descriptions change column  id id char(36);
update tbl_post_natal_observation_descriptions set id = copy_id;

alter table tbl_post_natal_observation_checks change column  id id char(36);
update tbl_post_natal_observation_checks set id = copy_id;
alter table tbl_post_natal_observation_checks add foreign key if not exists tbl_post_natal_observation_checks_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_observation_checks add foreign key if not exists tbl_post_natal_observation_checks_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_observation_checks add foreign key if not exists tbl_post_natal_observation_checks_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_post_natal_observation_checks add foreign key if not exists tbl_post_natal_observation_checks_observation_id_foreign ( observation_id ) references tbl_post_natal_observation_descriptions(id) on update cascade;

alter table tbl_labour_referrals change column  id id char(36);
update tbl_labour_referrals set id = copy_id;
alter table tbl_labour_referrals add foreign key if not exists tbl_labour_referrals_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_labour_referrals add foreign key if not exists tbl_labour_referrals_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_referrals add foreign key if not exists tbl_labour_referrals_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_labour_referrals add foreign key if not exists tbl_labour_referrals_transfered_institution_id_foreign ( transfered_institution_id ) references tbl_facilities(id) on update cascade;

alter table tbl_medications change column  id id char(36);
update tbl_medications set id = copy_id;
alter table tbl_medications add foreign key if not exists tbl_medications_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_medications add foreign key if not exists tbl_medications_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_medications add foreign key if not exists tbl_medications_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_medications add foreign key if not exists tbl_medications_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;

-- recursive key with no apparent meaning
alter table tbl_std_investigation_results drop foreign key if exists tbl_std_investigation_results_std_id_foreign;
alter table tbl_std_investigation_results change column  id id char(36);
update tbl_std_investigation_results set id = copy_id;
alter table tbl_std_investigation_results add foreign key if not exists tbl_std_investigation_results_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_std_investigation_results add foreign key if not exists tbl_std_investigation_results_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_std_investigation_results add foreign key if not exists tbl_std_investigation_results_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_client_violence_outputs change column  id id char(36);
update tbl_client_violence_outputs set id = copy_id;
alter table tbl_client_violence_outputs add foreign key if not exists tbl_client_violence_outputs_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_client_violence_outputs add foreign key if not exists tbl_client_violence_outputs_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_client_violence_outputs add foreign key if not exists tbl_client_violence_outputs_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_anti_natal_referrals change column  id id char(36);
update tbl_anti_natal_referrals set id = copy_id;
alter table tbl_anti_natal_referrals add foreign key if not exists tbl_anti_natal_referrals_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_anti_natal_referrals add foreign key if not exists tbl_anti_natal_referrals_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_referrals add foreign key if not exists tbl_anti_natal_referrals_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_anti_natal_referrals add foreign key if not exists tbl_anti_natal_referrals_transfered_institution_id_foreign ( transfered_institution_id ) references tbl_facilities(id) on update cascade;

alter table tbl_child_vitamin_registers change column  id id char(36);
update tbl_child_vitamin_registers set id = copy_id;
alter table tbl_child_vitamin_registers add foreign key if not exists tbl_child_vitamin_registers_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_child_vitamin_registers add foreign key if not exists tbl_child_vitamin_registers_mother_id_foreign ( mother_id ) references tbl_patients(id) on update cascade;
alter table tbl_child_vitamin_registers add foreign key if not exists tbl_child_vitamin_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_child_vitamin_registers add foreign key if not exists tbl_child_vitamin_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_clients_complains change column  id id char(36);
update tbl_clients_complains set id = copy_id;
alter table tbl_clients_complains add foreign key if not exists tbl_clients_complains_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_clients_complains add foreign key if not exists tbl_clients_complains_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_clients_complains add foreign key if not exists tbl_clients_complains_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_referrals change column  id id char(36);
update tbl_post_natal_referrals set id = copy_id;
alter table tbl_post_natal_referrals add foreign key if not exists tbl_post_natal_referrals_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_post_natal_referrals add foreign key if not exists tbl_post_natal_referrals_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_referrals add foreign key if not exists tbl_post_natal_referrals_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_post_natal_referrals add foreign key if not exists tbl_post_natal_referrals_transfered_institution_id_foreign ( transfered_institution_id ) references tbl_facilities(id) on update cascade;

alter table tbl_anti_natal_councelling_givens change column  id id char(36);
update tbl_anti_natal_councelling_givens set id = copy_id;
alter table tbl_anti_natal_councelling_givens add foreign key if not exists tbl_anti_natal_councelling_givens_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_anti_natal_councelling_givens add foreign key if not exists tbl_anti_natal_councelling_givens_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_councelling_givens add foreign key if not exists tbl_anti_natal_councelling_givens_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_tb_patient_treatment_types change column  id id char(36);
update tbl_tb_patient_treatment_types set id = copy_id;
alter table tbl_tb_patient_treatment_types add foreign key if not exists tbl_tb_patient_treatment_types_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_tb_patient_treatment_types add foreign key if not exists tbl_tb_patient_treatment_types_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_tb_patient_treatment_types add foreign key if not exists tbl_tb_patient_treatment_types_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_social_ward_rounds change column  id id char(36);
update tbl_social_ward_rounds set id = copy_id;
alter table tbl_social_ward_rounds add foreign key if not exists tbl_social_ward_rounds_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_social_ward_rounds add foreign key if not exists tbl_social_ward_rounds_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_social_ward_rounds add foreign key if not exists tbl_social_ward_rounds_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_child_hiv_expose_registers change column  id id char(36);
update tbl_child_hiv_expose_registers set id = copy_id;
alter table tbl_child_hiv_expose_registers add foreign key if not exists tbl_child_hiv_expose_registers_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_child_hiv_expose_registers add foreign key if not exists tbl_child_hiv_expose_registers_mother_id_foreign ( mother_id ) references tbl_patients(id) on update cascade;
alter table tbl_child_hiv_expose_registers add foreign key if not exists tbl_child_hiv_expose_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_child_hiv_expose_registers add foreign key if not exists tbl_child_hiv_expose_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_tt_vaccinations change column  id id char(36);
update tbl_tt_vaccinations set id = copy_id;
alter table tbl_tt_vaccinations add foreign key if not exists tbl_tt_vaccinations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_tt_vaccinations add foreign key if not exists tbl_tt_vaccinations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_tt_vaccinations add foreign key if not exists tbl_tt_vaccinations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_theatre_waits change column  id id char(36);
update tbl_theatre_waits set id = copy_id;
alter table tbl_theatre_waits add foreign key if not exists tbl_theatre_waits_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_theatre_waits add foreign key if not exists tbl_theatre_waits_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_intra_operations change column  id id char(36);
update tbl_intra_operations set id = copy_id;
alter table tbl_intra_operations add foreign key if not exists tbl_intra_operations_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;
alter table tbl_intra_operations add foreign key if not exists tbl_intra_operations_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_intra_operations add foreign key if not exists tbl_intra_operations_request_id_foreign ( request_id ) references tbl_theatre_waits(id) on update cascade;
alter table tbl_intra_operations add foreign key if not exists tbl_intra_operations_doctor_id_foreign ( doctor_id ) references users(id) on update cascade;

alter table tbl_family_planning_method_registers change column  id id char(36);
update tbl_family_planning_method_registers set id = copy_id;
alter table tbl_family_planning_method_registers add foreign key if not exists tbl_family_planning_method_registers_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_family_planning_method_registers add foreign key if not exists tbl_family_planning_method_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_family_planning_method_registers add foreign key if not exists tbl_family_planning_method_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_client_violence_services change column  id id char(36);
update tbl_client_violence_services set id = copy_id;
alter table tbl_client_violence_services add foreign key if not exists tbl_client_violence_services_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_client_violence_services add foreign key if not exists tbl_client_violence_services_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_client_violence_services add foreign key if not exists tbl_client_violence_services_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_anti_natal_partner_registers change column  id id char(36);
update tbl_anti_natal_partner_registers set id = copy_id;
alter table tbl_anti_natal_partner_registers add foreign key if not exists tbl_anti_natal_partner_registers_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_anti_natal_partner_registers add foreign key if not exists tbl_anti_natal_partner_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_partner_registers add foreign key if not exists tbl_anti_natal_partner_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_child_arv_prophlaxises change column  id id char(36);
update tbl_post_natal_child_arv_prophlaxises set id = copy_id;
alter table tbl_post_natal_child_arv_prophlaxises add foreign key if not exists tbl_post_natal_child_arv_prophlaxises_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_post_natal_child_arv_prophlaxises add foreign key if not exists tbl_post_natal_child_arv_prophlaxises_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_child_arv_prophlaxises add foreign key if not exists tbl_post_natal_child_arv_prophlaxises_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_child_vaccinations change column  id id char(36);
update tbl_post_natal_child_vaccinations set id = copy_id;
alter table tbl_post_natal_child_vaccinations add foreign key if not exists tbl_post_natal_child_vaccinations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_post_natal_child_vaccinations add foreign key if not exists tbl_post_natal_child_vaccinations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_child_vaccinations add foreign key if not exists tbl_post_natal_child_vaccinations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_exemption_tracking_statuses change column  id id char(36);
update tbl_exemption_tracking_statuses set id = copy_id;
alter table tbl_exemption_tracking_statuses add foreign key if not exists tbl_exemption_tracking_statuses_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_exemption_tracking_statuses add foreign key if not exists tbl_exemption_tracking_statuses_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_exemption_tracking_statuses add foreign key if not exists tbl_exemption_tracking_statuses_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_child_feedings change column  id id char(36);
update tbl_child_feedings set id = copy_id;
alter table tbl_child_feedings add foreign key if not exists tbl_child_feedings_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_child_feedings add foreign key if not exists tbl_child_feedings_mother_id_foreign ( mother_id ) references tbl_patients(id) on update cascade;
alter table tbl_child_feedings add foreign key if not exists tbl_child_feedings_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_child_feedings add foreign key if not exists tbl_child_feedings_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_tt_vaccinations change column  id id char(36);
update tbl_post_natal_tt_vaccinations set id = copy_id;
alter table tbl_post_natal_tt_vaccinations add foreign key if not exists tbl_post_natal_tt_vaccinations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_post_natal_tt_vaccinations add foreign key if not exists tbl_post_natal_tt_vaccinations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_tt_vaccinations add foreign key if not exists tbl_post_natal_tt_vaccinations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_std_investigation_partner_results change column  id id char(36);
update tbl_std_investigation_partner_results set id = copy_id;
alter table tbl_std_investigation_partner_results add foreign key if not exists tbl_std_investigation_partner_results_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_std_investigation_partner_results add foreign key if not exists tbl_std_investigation_partner_results_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_std_investigation_partner_results add foreign key if not exists tbl_std_investigation_partner_results_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_child_registers change column  id id char(36);
update tbl_child_registers set id = copy_id;
alter table tbl_child_registers add foreign key if not exists tbl_child_registers_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_child_registers add foreign key if not exists tbl_child_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_child_registers add foreign key if not exists tbl_child_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_client_violence_informants change column  id id char(36);
update tbl_client_violence_informants set id = copy_id;
alter table tbl_client_violence_informants add foreign key if not exists tbl_client_violence_informants_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_client_violence_informants add foreign key if not exists tbl_client_violence_informants_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_client_violence_informants add foreign key if not exists tbl_client_violence_informants_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_family_planning_referrals change column  id id char(36);
update tbl_family_planning_referrals set id = copy_id;
alter table tbl_family_planning_referrals add foreign key if not exists tbl_family_planning_referrals_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_family_planning_referrals add foreign key if not exists tbl_family_planning_referrals_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_family_planning_referrals add foreign key if not exists tbl_family_planning_referrals_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_family_planning_referrals add foreign key if not exists tbl_family_planning_referrals_transfered_institution_id_foreign ( transfered_institution_id ) references tbl_facilities(id) on update cascade;

alter table tbl_clinic_attendances change column  id id char(36);
update tbl_clinic_attendances set id = copy_id;
alter table tbl_clinic_attendances add foreign key if not exists tbl_clinic_attendances_refferal_id_foreign ( refferal_id ) references tbl_clinic_instructions(id) on update cascade;
alter table tbl_clinic_attendances add foreign key if not exists tbl_clinic_attendances_visit_id_foreign ( visit_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_previous_pregnancy_infos change column  id id char(36);
update tbl_previous_pregnancy_infos set id = copy_id;
alter table tbl_previous_pregnancy_infos add foreign key if not exists tbl_previous_pregnancy_infos_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_previous_pregnancy_infos add foreign key if not exists tbl_previous_pregnancy_infos_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_previous_pregnancy_infos add foreign key if not exists tbl_previous_pregnancy_infos_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_previous_pregnancy_infos add foreign key if not exists tbl_previous_pregnancy_infos_delivery_place_foreign ( delivery_place ) references tbl_facilities(id) on update cascade;

alter table tbl_child_growth_registers change column  id id char(36);
update tbl_child_growth_registers set id = copy_id;
alter table tbl_child_growth_registers add foreign key if not exists tbl_child_growth_registers_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_child_growth_registers add foreign key if not exists tbl_child_growth_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_child_growth_registers add foreign key if not exists tbl_child_growth_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_prescriptions change column  id id char(36);
update tbl_prescriptions set id = copy_id;
alter table tbl_prescriptions add foreign key if not exists tbl_prescriptions_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_prescriptions add foreign key if not exists tbl_prescriptions_prescriber_id_foreign ( prescriber_id ) references users(id) on update cascade;
alter table tbl_prescriptions add foreign key if not exists tbl_prescriptions_verifier_id_foreign ( verifier_id ) references users(id) on update cascade;
alter table tbl_prescriptions add foreign key if not exists tbl_prescriptions_dispenser_id_foreign ( dispenser_id ) references users(id) on update cascade;
alter table tbl_prescriptions add foreign key if not exists tbl_prescriptions_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;
alter table tbl_prescriptions add foreign key if not exists tbl_prescriptions_visit_id_foreign ( visit_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_labour_delivery_events change column  id id char(36);
update tbl_labour_delivery_events set id = copy_id;
alter table tbl_labour_delivery_events add foreign key if not exists tbl_labour_delivery_events_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_labour_delivery_events add foreign key if not exists tbl_labour_delivery_events_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_delivery_events add foreign key if not exists tbl_labour_delivery_events_tailer_id_foreign ( tailer_id ) references users(id) on update cascade;
alter table tbl_labour_delivery_events add foreign key if not exists tbl_labour_delivery_events_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_teeth_patients change column  id id char(36);
update tbl_teeth_patients set id = copy_id;
alter table tbl_teeth_patients add foreign key if not exists tbl_teeth_patients_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_teeth_patients add foreign key if not exists tbl_teeth_patients_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_child_growth_registers change column  id id char(36);
update tbl_child_growth_registers set id = copy_id;
alter table tbl_child_growth_registers add foreign key if not exists tbl_child_growth_registers_patient_id_foreign ( patient_id ) references tbl_child_registers(id) on update cascade;
alter table tbl_child_growth_registers add foreign key if not exists tbl_child_growth_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_child_growth_registers add foreign key if not exists tbl_child_growth_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_child_subsidized_voucher_registers change column  id id char(36);
update tbl_child_subsidized_voucher_registers set id = copy_id;
alter table tbl_child_subsidized_voucher_registers add foreign key if not exists tbl_child_subsidized_voucher_registers_patient_id_foreign ( patient_id ) references tbl_child_registers(id) on update cascade;
alter table tbl_child_subsidized_voucher_registers add foreign key if not exists tbl_child_subsidized_voucher_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_child_subsidized_voucher_registers add foreign key if not exists tbl_child_subsidized_voucher_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_child_deworm_registers change column  id id char(36);
update tbl_child_deworm_registers set id = copy_id;
alter table tbl_child_deworm_registers add foreign key if not exists tbl_child_deworm_registers_patient_id_foreign ( patient_id ) references tbl_child_registers(id) on update cascade;
alter table tbl_child_deworm_registers add foreign key if not exists tbl_child_deworm_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_child_deworm_registers add foreign key if not exists tbl_child_deworm_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_registers change column  id id char(36);
update tbl_post_natal_registers set id = copy_id;
alter table tbl_post_natal_registers add foreign key if not exists tbl_post_natal_registers_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_post_natal_registers add foreign key if not exists tbl_post_natal_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_registers add foreign key if not exists tbl_post_natal_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_child_mother_details change column  id id char(36);
update tbl_child_mother_details set id = copy_id;
alter table tbl_child_mother_details add foreign key if not exists tbl_child_mother_details_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_child_mother_details add foreign key if not exists tbl_child_mother_details_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_child_mother_details add foreign key if not exists tbl_child_mother_details_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_child_vitamin_deworm_registers change column  id id char(36);
update tbl_child_vitamin_deworm_registers set id = copy_id;
alter table tbl_child_vitamin_deworm_registers add foreign key if not exists tbl_child_vitamin_deworm_registers_client_id_foreign ( client_id ) references tbl_child_registers(id) on update cascade;
alter table tbl_child_vitamin_deworm_registers add foreign key if not exists tbl_child_vitamin_deworm_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_child_vitamin_deworm_registers add foreign key if not exists tbl_child_vitamin_deworm_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_pediatric_natals change column  id id char(36);
update tbl_pediatric_natals set id = copy_id;
alter table tbl_pediatric_natals add foreign key if not exists tbl_pediatric_natals_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_pediatric_natals add foreign key if not exists tbl_pediatric_natals_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_pediatric_natals add foreign key if not exists tbl_pediatric_natals_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_anti_natal_ipts change column  id id char(36);
update tbl_anti_natal_ipts set id = copy_id;
alter table tbl_anti_natal_ipts add foreign key if not exists tbl_anti_natal_ipts_patient_id_foreign ( patient_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_anti_natal_ipts add foreign key if not exists tbl_anti_natal_ipts_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_ipts add foreign key if not exists tbl_anti_natal_ipts_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_emonc_services change column  id id char(36);
update tbl_labour_emonc_services set id = copy_id;
alter table tbl_labour_emonc_services add foreign key if not exists tbl_labour_emonc_services_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_labour_emonc_services add foreign key if not exists tbl_labour_emonc_services_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_emonc_services add foreign key if not exists tbl_labour_emonc_services_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_exemption_numbers change column  id id char(36);
update tbl_exemption_numbers set id = copy_id;
alter table tbl_exemption_numbers add foreign key if not exists tbl_exemption_numbers_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_exemption_numbers add foreign key if not exists tbl_exemption_numbers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_exemption_numbers add foreign key if not exists tbl_exemption_numbers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_wards change column  id id char(36);
update tbl_wards set id = copy_id;
alter table tbl_wards add foreign key if not exists tbl_wards_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_important_investigations change column  id id char(36);
update tbl_important_investigations set id = copy_id;
alter table tbl_important_investigations add foreign key if not exists tbl_important_investigations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_important_investigations add foreign key if not exists tbl_important_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_important_investigations add foreign key if not exists tbl_important_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_pregnancy_ages change column  id id char(36);
update tbl_pregnancy_ages set id = copy_id;
alter table tbl_pregnancy_ages add foreign key if not exists tbl_pregnancy_ages_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_pregnancy_ages add foreign key if not exists tbl_pregnancy_ages_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_pregnancy_ages add foreign key if not exists tbl_pregnancy_ages_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_family_planning_pregnancy_histories change column  id id char(36);
update tbl_family_planning_pregnancy_histories set id = copy_id;
alter table tbl_family_planning_pregnancy_histories add foreign key if not exists tbl_family_planning_pregnancy_histories_patient_id_foreign ( patient_id ) references tbl_family_planning_registers(id) on update cascade;
alter table tbl_family_planning_pregnancy_histories add foreign key if not exists tbl_family_planning_pregnancy_histories_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_family_planning_pregnancy_histories add foreign key if not exists tbl_family_planning_pregnancy_histories_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_surgery_family_socials change column  id id char(36);
update tbl_surgery_family_socials set id = copy_id;
alter table tbl_surgery_family_socials add foreign key if not exists tbl_surgery_family_socials_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;
alter table tbl_surgery_family_socials add foreign key if not exists tbl_surgery_family_socials_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_surgery_family_socials add foreign key if not exists tbl_surgery_family_socials_request_id_foreign ( request_id ) references tbl_theatre_waits(id) on update cascade;

alter table tbl_anti_natal_pmtcts change column  id id char(36);
update tbl_anti_natal_pmtcts set id = copy_id;
alter table tbl_anti_natal_pmtcts add foreign key if not exists tbl_anti_natal_pmtcts_patient_id_foreign ( patient_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_anti_natal_pmtcts add foreign key if not exists tbl_anti_natal_pmtcts_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_pmtcts add foreign key if not exists tbl_anti_natal_pmtcts_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_tb_patient_treatment_outputs change column  id id char(36);
update tbl_tb_patient_treatment_outputs set id = copy_id;
alter table tbl_tb_patient_treatment_outputs add foreign key if not exists tbl_tb_patient_treatment_outputs_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_tb_patient_treatment_outputs add foreign key if not exists tbl_tb_patient_treatment_outputs_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_tb_patient_treatment_outputs add foreign key if not exists tbl_tb_patient_treatment_outputs_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_clinic_schedules change column  id id char(36);
update tbl_clinic_schedules set id = copy_id;
alter table tbl_clinic_schedules add foreign key if not exists tbl_clinic_schedules_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_clinic_schedules add foreign key if not exists tbl_clinic_schedules_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_additional_medications change column  id id char(36);
update tbl_post_natal_additional_medications set id = copy_id;
alter table tbl_post_natal_additional_medications add foreign key if not exists tbl_post_natal_additional_medications_patient_id_foreign ( patient_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_post_natal_additional_medications add foreign key if not exists tbl_post_natal_additional_medications_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_additional_medications add foreign key if not exists tbl_post_natal_additional_medications_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_delivery_child_arvs change column  id id char(36);
update tbl_labour_delivery_child_arvs set id = copy_id;
alter table tbl_labour_delivery_child_arvs add foreign key if not exists tbl_labour_delivery_child_arvs_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_labour_delivery_child_arvs add foreign key if not exists tbl_labour_delivery_child_arvs_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_delivery_child_arvs add foreign key if not exists tbl_labour_delivery_child_arvs_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_output_observations change column  id id char(36);
update tbl_output_observations set id = copy_id;
alter table tbl_output_observations add foreign key if not exists tbl_output_observations_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_fplanning_pitcs change column  id id char(36);
update tbl_fplanning_pitcs set id = copy_id;
alter table tbl_fplanning_pitcs add foreign key if not exists tbl_fplanning_pitcs_client_id_foreign ( client_id ) references tbl_family_planning_registers(id) on update cascade;
alter table tbl_fplanning_pitcs add foreign key if not exists tbl_fplanning_pitcs_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_fplanning_pitcs add foreign key if not exists tbl_fplanning_pitcs_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_tb_vvu_services change column  id id char(36);
update tbl_tb_vvu_services set id = copy_id;
alter table tbl_tb_vvu_services add foreign key if not exists tbl_tb_vvu_services_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_tb_vvu_services add foreign key if not exists tbl_tb_vvu_services_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_tb_vvu_services add foreign key if not exists tbl_tb_vvu_services_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_child_attendances change column  id id char(36);
update tbl_post_natal_child_attendances set id = copy_id;
alter table tbl_post_natal_child_attendances add foreign key if not exists tbl_post_natal_child_attendances_patient_id_foreign ( patient_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_post_natal_child_attendances add foreign key if not exists tbl_post_natal_child_attendances_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_child_attendances add foreign key if not exists tbl_post_natal_child_attendances_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_delivery_mother_dispositions change column  id id char(36);
update tbl_labour_delivery_mother_dispositions set id = copy_id;
alter table tbl_labour_delivery_mother_dispositions add foreign key if not exists tbl_labour_delivery_mother_dispositions_patient_id_foreign ( patient_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_labour_delivery_mother_dispositions add foreign key if not exists tbl_labour_delivery_mother_dispositions_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_delivery_mother_dispositions add foreign key if not exists tbl_labour_delivery_mother_dispositions_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_vital_signs change column  id id char(36);
update tbl_vital_signs set id = copy_id;
alter table tbl_vital_signs add foreign key if not exists tbl_vital_signs_registered_by_foreign ( registered_by ) references users(id) on update cascade;
alter table tbl_vital_signs add foreign key if not exists tbl_vital_signs_visiting_id_foreign ( visiting_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_past_eye_records change column  id id char(36);
update tbl_past_eye_records set id = copy_id;
alter table tbl_past_eye_records add foreign key if not exists tbl_past_eye_records_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_past_eye_records add foreign key if not exists tbl_past_eye_records_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_past_eye_records add foreign key if not exists tbl_past_eye_records_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;

alter table tbl_fplanning_stomach_leg_investigations change column  id id char(36);
update tbl_fplanning_stomach_leg_investigations set id = copy_id;
alter table tbl_fplanning_stomach_leg_investigations add foreign key if not exists tbl_fplanning_stomach_leg_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_fplanning_stomach_leg_investigations add foreign key if not exists tbl_fplanning_stomach_leg_investigations_client_id_foreign ( client_id ) references tbl_family_planning_registers(id) on update cascade;
alter table tbl_fplanning_stomach_leg_investigations add foreign key if not exists tbl_fplanning_stomach_leg_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_familiy_plannings change column  id id char(36);
update tbl_post_natal_familiy_plannings set id = copy_id;
alter table tbl_post_natal_familiy_plannings add foreign key if not exists tbl_post_natal_familiy_plannings_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_familiy_plannings add foreign key if not exists tbl_post_natal_familiy_plannings_patient_id_foreign ( patient_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_post_natal_familiy_plannings add foreign key if not exists tbl_post_natal_familiy_plannings_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_pmtcts change column  id id char(36);
update tbl_post_natal_pmtcts set id = copy_id;
alter table tbl_post_natal_pmtcts add foreign key if not exists tbl_post_natal_pmtcts_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_pmtcts add foreign key if not exists tbl_post_natal_pmtcts_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_post_natal_pmtcts add foreign key if not exists tbl_post_natal_pmtcts_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_newborns change column  id id char(36);
update tbl_labour_newborns set id = copy_id;
alter table tbl_labour_newborns add foreign key if not exists tbl_labour_newborns_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_labour_newborns add foreign key if not exists tbl_labour_newborns_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_newborns add foreign key if not exists tbl_labour_newborns_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_icu_entries change column  id id char(36);
update tbl_icu_entries set id = copy_id;
alter table tbl_icu_entries add foreign key if not exists tbl_icu_entries_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;
alter table tbl_icu_entries add foreign key if not exists tbl_icu_entries_doctor_id_foreign ( doctor_id ) references users(id) on update cascade;

alter table tbl_ctc_patient_addresses change column  id id char(36);
update tbl_ctc_patient_addresses set id = copy_id;
alter table tbl_ctc_patient_addresses add foreign key if not exists tbl_ctc_patient_addresses_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_ctc_patient_addresses add foreign key if not exists tbl_ctc_patient_addresses_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_post_natal_womb_statuses change column  id id char(36);
update tbl_post_natal_womb_statuses set id = copy_id;
alter table tbl_post_natal_womb_statuses add foreign key if not exists tbl_post_natal_womb_statuses_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_womb_statuses add foreign key if not exists tbl_post_natal_womb_statuses_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_post_natal_womb_statuses add foreign key if not exists tbl_post_natal_womb_statuses_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_anaethetic_records change column  id id char(36);
update tbl_anaethetic_records set id = copy_id;
alter table tbl_anaethetic_records add foreign key if not exists tbl_anaethetic_records_request_id_foreign ( request_id ) references tbl_theatre_waits(id) on update cascade;
alter table tbl_anaethetic_records add foreign key if not exists tbl_anaethetic_records_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_anaethetic_records add foreign key if not exists tbl_anaethetic_records_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_intra_opconditions change column  id id char(36);
update tbl_intra_opconditions set id = copy_id;
alter table tbl_intra_opconditions add foreign key if not exists tbl_intra_opconditions_request_id_foreign ( request_id ) references tbl_theatre_waits(id) on update cascade;
alter table tbl_intra_opconditions add foreign key if not exists tbl_intra_opconditions_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_intra_opconditions add foreign key if not exists tbl_intra_opconditions_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_anti_natal_followups change column  id id char(36);
update tbl_anti_natal_followups set id = copy_id;
alter table tbl_anti_natal_followups add foreign key if not exists tbl_anti_natal_followups_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_anti_natal_followups add foreign key if not exists tbl_anti_natal_followups_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_followups add foreign key if not exists tbl_anti_natal_followups_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_pediatric_pre_natals change column  id id char(36);
update tbl_pediatric_pre_natals set id = copy_id;
alter table tbl_pediatric_pre_natals add foreign key if not exists tbl_pediatric_pre_natals_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_pediatric_pre_natals add foreign key if not exists tbl_pediatric_pre_natals_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_pediatric_pre_natals add foreign key if not exists tbl_pediatric_pre_natals_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_anti_natal_partiner_pmtcts change column  id id char(36);
update tbl_anti_natal_partiner_pmtcts set id = copy_id;
alter table tbl_anti_natal_partiner_pmtcts add foreign key if not exists tbl_anti_natal_partiner_pmtcts_patient_id_foreign ( patient_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_anti_natal_partiner_pmtcts add foreign key if not exists tbl_anti_natal_partiner_pmtcts_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_partiner_pmtcts add foreign key if not exists tbl_anti_natal_partiner_pmtcts_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_admissions change column  id id char(36);
update tbl_labour_admissions set id = copy_id;
alter table tbl_labour_admissions add foreign key if not exists tbl_labour_admissions_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_labour_admissions add foreign key if not exists tbl_labour_admissions_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_admissions add foreign key if not exists tbl_labour_admissions_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_emergence_visits change column  id id char(36);
update tbl_emergence_visits set id = copy_id;
alter table tbl_emergence_visits add foreign key if not exists tbl_emergence_visits_registered_by_foreign ( registered_by ) references users(id) on update cascade;
alter table tbl_emergence_visits add foreign key if not exists tbl_emergence_visits_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_emergence_visits add foreign key if not exists tbl_emergence_visits_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_fplanning_cervix_cancer_investigations change column  id id char(36);
update tbl_fplanning_cervix_cancer_investigations set id = copy_id;
alter table tbl_fplanning_cervix_cancer_investigations add foreign key if not exists tbl_fplanning_cervix_cancer_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_fplanning_cervix_cancer_investigations add foreign key if not exists tbl_fplanning_cervix_cancer_investigations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_fplanning_cervix_cancer_investigations add foreign key if not exists tbl_fplanning_cervix_cancer_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_tb_sputam_test_followups change column  id id char(36);
update tbl_tb_sputam_test_followups set id = copy_id;
alter table tbl_tb_sputam_test_followups add foreign key if not exists tbl_tb_sputam_test_followups_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_tb_sputam_test_followups add foreign key if not exists tbl_tb_sputam_test_followups_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_tb_sputam_test_followups add foreign key if not exists tbl_tb_sputam_test_followups_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_breast_statuses change column  id id char(36);
update tbl_post_natal_breast_statuses set id = copy_id;
alter table tbl_post_natal_breast_statuses add foreign key if not exists tbl_post_natal_breast_statuses_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_breast_statuses add foreign key if not exists tbl_post_natal_breast_statuses_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_post_natal_breast_statuses add foreign key if not exists tbl_post_natal_breast_statuses_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_delivery_complications change column  id id char(36);
update tbl_labour_delivery_complications set id = copy_id;
alter table tbl_labour_delivery_complications add foreign key if not exists tbl_labour_delivery_complications_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_delivery_complications add foreign key if not exists tbl_labour_delivery_complications_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_labour_delivery_complications add foreign key if not exists tbl_labour_delivery_complications_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_epsodes change column  id id char(36);
update tbl_epsodes set id = copy_id;
alter table tbl_epsodes add foreign key if not exists tbl_epsodes_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_epsodes add foreign key if not exists tbl_epsodes_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;

alter table tbl_partner_lab_tests change column  id id char(36);
update tbl_partner_lab_tests set id = copy_id;
alter table tbl_partner_lab_tests add foreign key if not exists tbl_partner_lab_tests_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_partner_lab_tests add foreign key if not exists tbl_partner_lab_tests_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_partner_lab_tests add foreign key if not exists tbl_partner_lab_tests_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_fplanning_previous_pregnancy_results change column  id id char(36);
update tbl_fplanning_previous_pregnancy_results set id = copy_id;
alter table tbl_fplanning_previous_pregnancy_results add foreign key if not exists tbl_fplanning_previous_pregnancy_results_client_id_foreign ( client_id ) references tbl_family_planning_registers(id) on update cascade;
alter table tbl_fplanning_previous_pregnancy_results add foreign key if not exists tbl_fplanning_previous_pregnancy_results_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_fplanning_previous_pregnancy_results add foreign key if not exists tbl_fplanning_previous_pregnancy_results_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_child_investigations change column  id id char(36);
update tbl_post_natal_child_investigations set id = copy_id;
alter table tbl_post_natal_child_investigations add foreign key if not exists tbl_post_natal_child_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_child_investigations add foreign key if not exists tbl_post_natal_child_investigations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_post_natal_child_investigations add foreign key if not exists tbl_post_natal_child_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_fgms change column  id id char(36);
update tbl_labour_fgms set id = copy_id;
alter table tbl_labour_fgms add foreign key if not exists tbl_labour_fgms_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_fgms add foreign key if not exists tbl_labour_fgms_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_labour_fgms add foreign key if not exists tbl_labour_fgms_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_tt_given_vaccinations change column  id id char(36);
update tbl_post_natal_tt_given_vaccinations set id = copy_id;
alter table tbl_post_natal_tt_given_vaccinations add foreign key if not exists tbl_post_natal_tt_given_vaccinations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_tt_given_vaccinations add foreign key if not exists tbl_post_natal_tt_given_vaccinations_patient_id_foreign ( patient_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_post_natal_tt_given_vaccinations add foreign key if not exists tbl_post_natal_tt_given_vaccinations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_registers change column  id id char(36);
update tbl_labour_registers set id = copy_id;
alter table tbl_labour_registers add foreign key if not exists tbl_labour_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_registers add foreign key if not exists tbl_labour_registers_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_labour_registers add foreign key if not exists tbl_labour_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_status_anaesthetics change column  id id char(36);
update tbl_status_anaesthetics set id = copy_id;
alter table tbl_status_anaesthetics add foreign key if not exists tbl_status_anaesthetics_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_status_anaesthetics add foreign key if not exists tbl_status_anaesthetics_request_id_foreign ( request_id ) references tbl_theatre_waits(id) on update cascade;
alter table tbl_status_anaesthetics add foreign key if not exists tbl_status_anaesthetics_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_family_planning_attendance_registers change column  id id char(36);
update tbl_family_planning_attendance_registers set id = copy_id;
alter table tbl_family_planning_attendance_registers add foreign key if not exists tbl_family_planning_attendance_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_family_planning_attendance_registers add foreign key if not exists tbl_family_planning_attendance_registers_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_family_planning_attendance_registers add foreign key if not exists tbl_family_planning_attendance_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_rch_general_recomendations change column  id id char(36);
update tbl_rch_general_recomendations set id = copy_id;
alter table tbl_rch_general_recomendations add foreign key if not exists tbl_rch_general_recomendations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_rch_general_recomendations add foreign key if not exists tbl_rch_general_recomendations_patient_id_foreign ( patient_id ) references tbl_family_planning_registers(id) on update cascade;
alter table tbl_rch_general_recomendations add foreign key if not exists tbl_rch_general_recomendations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_pediatric_nutritionals change column  id id char(36);
update tbl_pediatric_nutritionals set id = copy_id;
alter table tbl_pediatric_nutritionals add foreign key if not exists tbl_pediatric_nutritionals_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_pediatric_nutritionals add foreign key if not exists tbl_pediatric_nutritionals_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_pediatric_nutritionals add foreign key if not exists tbl_pediatric_nutritionals_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_anti_natal_lab_tests change column  id id char(36);
update tbl_anti_natal_lab_tests set id = copy_id;
alter table tbl_anti_natal_lab_tests add foreign key if not exists tbl_anti_natal_lab_tests_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_lab_tests add foreign key if not exists tbl_anti_natal_lab_tests_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_anti_natal_lab_tests add foreign key if not exists tbl_anti_natal_lab_tests_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_family_planning_previous_healths change column  id id char(36);
update tbl_family_planning_previous_healths set id = copy_id;
alter table tbl_family_planning_previous_healths add foreign key if not exists tbl_family_planning_previous_healths_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_family_planning_previous_healths add foreign key if not exists tbl_family_planning_previous_healths_client_id_foreign ( client_id ) references tbl_family_planning_registers(id) on update cascade;
alter table tbl_family_planning_previous_healths add foreign key if not exists tbl_family_planning_previous_healths_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_surgery_histories change column  id id char(36);
update tbl_surgery_histories set id = copy_id;
alter table tbl_surgery_histories add foreign key if not exists tbl_surgery_histories_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_surgery_histories add foreign key if not exists tbl_surgery_histories_request_id_foreign ( request_id ) references tbl_theatre_waits(id) on update cascade;
alter table tbl_surgery_histories add foreign key if not exists tbl_surgery_histories_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_post_natal_attendances change column  id id char(36);
update tbl_post_natal_attendances set id = copy_id;
alter table tbl_post_natal_attendances add foreign key if not exists tbl_post_natal_attendances_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_attendances add foreign key if not exists tbl_post_natal_attendances_patient_id_foreign ( patient_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_post_natal_attendances add foreign key if not exists tbl_post_natal_attendances_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_fplanning_placenta_cancer_investigations change column  id id char(36);
update tbl_fplanning_placenta_cancer_investigations set id = copy_id;
alter table tbl_fplanning_placenta_cancer_investigations add foreign key if not exists tbl_fplanning_placenta_cancer_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_fplanning_placenta_cancer_investigations add foreign key if not exists tbl_fplanning_placenta_cancer_investigations_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_fplanning_placenta_cancer_investigations add foreign key if not exists tbl_fplanning_placenta_cancer_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_child_feedings change column  id id char(36);
update tbl_post_natal_child_feedings set id = copy_id;
alter table tbl_post_natal_child_feedings add foreign key if not exists tbl_post_natal_child_feedings_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_child_feedings add foreign key if not exists tbl_post_natal_child_feedings_patient_id_foreign ( patient_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_post_natal_child_feedings add foreign key if not exists tbl_post_natal_child_feedings_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_delivery_vvu_results change column  id id char(36);
update tbl_labour_delivery_vvu_results set id = copy_id;
alter table tbl_labour_delivery_vvu_results add foreign key if not exists tbl_labour_delivery_vvu_results_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_delivery_vvu_results add foreign key if not exists tbl_labour_delivery_vvu_results_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_labour_delivery_vvu_results add foreign key if not exists tbl_labour_delivery_vvu_results_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_fplanning_viginal_by_arm_investigations change column  id id char(36);
update tbl_fplanning_viginal_by_arm_investigations set id = copy_id;
alter table tbl_fplanning_viginal_by_arm_investigations add foreign key if not exists tbl_fplanning_viginal_by_arm_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_fplanning_viginal_by_arm_investigations add foreign key if not exists tbl_fplanning_viginal_by_arm_investigations_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_fplanning_viginal_by_arm_investigations add foreign key if not exists tbl_fplanning_viginal_by_arm_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_investigations change column  id id char(36);
update tbl_post_natal_investigations set id = copy_id;
alter table tbl_post_natal_investigations add foreign key if not exists tbl_post_natal_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_investigations add foreign key if not exists tbl_post_natal_investigations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_post_natal_investigations add foreign key if not exists tbl_post_natal_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_observations change column  id id char(36);
update tbl_labour_observations set id = copy_id;
alter table tbl_labour_observations add foreign key if not exists tbl_labour_observations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_observations add foreign key if not exists tbl_labour_observations_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_labour_observations add foreign key if not exists tbl_labour_observations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_patient_tb_type_registers change column  id id char(36);
update tbl_patient_tb_type_registers set id = copy_id;
alter table tbl_patient_tb_type_registers add foreign key if not exists tbl_patient_tb_type_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_patient_tb_type_registers add foreign key if not exists tbl_patient_tb_type_registers_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_patient_tb_type_registers add foreign key if not exists tbl_patient_tb_type_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_postnatal_baby_feed_hours change column  id id char(36);
update tbl_postnatal_baby_feed_hours set id = copy_id;
alter table tbl_postnatal_baby_feed_hours add foreign key if not exists tbl_postnatal_baby_feed_hours_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_postnatal_baby_feed_hours add foreign key if not exists tbl_postnatal_baby_feed_hours_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_postnatal_baby_feed_hours add foreign key if not exists tbl_postnatal_baby_feed_hours_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_anti_natal_attendances change column  id id char(36);
update tbl_anti_natal_attendances set id = copy_id;
alter table tbl_anti_natal_attendances add foreign key if not exists tbl_anti_natal_attendances_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_attendances add foreign key if not exists tbl_anti_natal_attendances_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_anti_natal_attendances add foreign key if not exists tbl_anti_natal_attendances_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_pediatric_diataries change column  id id char(36);
update tbl_pediatric_diataries set id = copy_id;
alter table tbl_pediatric_diataries add foreign key if not exists tbl_pediatric_diataries_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_pediatric_diataries add foreign key if not exists tbl_pediatric_diataries_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_pediatric_diataries add foreign key if not exists tbl_pediatric_diataries_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_userdepartments change column  id id char(36);
update tbl_userdepartments set id = copy_id;
alter table tbl_userdepartments add foreign key if not exists tbl_userdepartments_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_userdepartments add foreign key if not exists tbl_userdepartments_registered_by_foreign ( registered_by ) references users(id) on update cascade;

alter table tbl_ctc_patient_supports change column  id id char(36);
update tbl_ctc_patient_supports set id = copy_id;
alter table tbl_ctc_patient_supports add foreign key if not exists tbl_ctc_patient_supports_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_ctc_patient_supports add foreign key if not exists tbl_ctc_patient_supports_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_ctc_patient_supports add foreign key if not exists tbl_ctc_patient_supports_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_anti_natal_ifas change column  id id char(36);
update tbl_anti_natal_ifas set id = copy_id;
alter table tbl_anti_natal_ifas add foreign key if not exists tbl_anti_natal_ifas_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_ifas add foreign key if not exists tbl_anti_natal_ifas_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_anti_natal_ifas add foreign key if not exists tbl_anti_natal_ifas_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_family_planning_vvu_statuses change column  id id char(36);
update tbl_family_planning_vvu_statuses set id = copy_id;
alter table tbl_family_planning_vvu_statuses add foreign key if not exists tbl_family_planning_vvu_statuses_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_family_planning_vvu_statuses add foreign key if not exists tbl_family_planning_vvu_statuses_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_family_planning_vvu_statuses add foreign key if not exists tbl_family_planning_vvu_statuses_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_tb_patient_medication_followups change column  id id char(36);
update tbl_tb_patient_medication_followups set id = copy_id;
alter table tbl_tb_patient_medication_followups add foreign key if not exists tbl_tb_patient_medication_followups_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_tb_patient_medication_followups add foreign key if not exists tbl_tb_patient_medication_followups_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_tb_patient_medication_followups add foreign key if not exists tbl_tb_patient_medication_followups_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_dehiscence_fistula_mental_statuses change column  id id char(36);
update tbl_post_dehiscence_fistula_mental_statuses set id = copy_id;
alter table tbl_post_dehiscence_fistula_mental_statuses add foreign key if not exists tbl_post_dehiscence_fistula_mental_statuses_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_dehiscence_fistula_mental_statuses add foreign key if not exists tbl_post_dehiscence_fistula_mental_statuses_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_post_dehiscence_fistula_mental_statuses add foreign key if not exists tbl_post_dehiscence_fistula_mental_statuses_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_birth_infos change column  id id char(36);
update tbl_labour_birth_infos set id = copy_id;
alter table tbl_labour_birth_infos add foreign key if not exists tbl_labour_birth_infos_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_birth_infos add foreign key if not exists tbl_labour_birth_infos_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_labour_birth_infos add foreign key if not exists tbl_labour_birth_infos_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_emergency_patients change column  id id char(36);
update tbl_emergency_patients set id = copy_id;
alter table tbl_emergency_patients add foreign key if not exists tbl_emergency_patients_registered_by_foreign ( registered_by  ) references users(id) on update cascade;
alter table tbl_emergency_patients add foreign key if not exists tbl_emergency_patients_visiting_id_foreign ( visiting_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_fplanning_lab_investigations change column  id id char(36);
update tbl_fplanning_lab_investigations set id = copy_id;
alter table tbl_fplanning_lab_investigations add foreign key if not exists tbl_fplanning_lab_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_fplanning_lab_investigations add foreign key if not exists tbl_fplanning_lab_investigations_client_id_foreign ( client_id ) references tbl_family_planning_registers(id) on update cascade;
alter table tbl_fplanning_lab_investigations add foreign key if not exists tbl_fplanning_lab_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_past_dental_records change column  id id char(36);
update tbl_past_dental_records set id = copy_id;
alter table tbl_past_dental_records add foreign key if not exists tbl_past_dental_records_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_past_dental_records add foreign key if not exists tbl_past_dental_records_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_past_dental_records add foreign key if not exists tbl_past_dental_records_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_fplanning_speculam_investigations change column  id id char(36);
update tbl_fplanning_speculam_investigations set id = copy_id;
alter table tbl_fplanning_speculam_investigations add foreign key if not exists tbl_fplanning_speculam_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_fplanning_speculam_investigations add foreign key if not exists tbl_fplanning_speculam_investigations_client_id_foreign ( client_id ) references tbl_family_planning_registers(id) on update cascade;
alter table tbl_fplanning_speculam_investigations add foreign key if not exists tbl_fplanning_speculam_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_fplanning_speculam_investigations change column  id id char(36);
update tbl_fplanning_speculam_investigations set id = copy_id;
alter table tbl_fplanning_speculam_investigations add foreign key if not exists tbl_fplanning_speculam_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_fplanning_speculam_investigations add foreign key if not exists tbl_fplanning_speculam_investigations_client_id_foreign ( client_id ) references tbl_family_planning_registers(id) on update cascade;
alter table tbl_fplanning_speculam_investigations add foreign key if not exists tbl_fplanning_speculam_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_fsb_msbs change column  id id char(36);
update tbl_labour_fsb_msbs set id = copy_id;
alter table tbl_labour_fsb_msbs add foreign key if not exists tbl_labour_fsb_msbs_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_fsb_msbs add foreign key if not exists tbl_labour_fsb_msbs_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_labour_fsb_msbs add foreign key if not exists tbl_labour_fsb_msbs_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_ctc_family_informations change column  id id char(36);
update tbl_ctc_family_informations set id = copy_id;
alter table tbl_ctc_family_informations add foreign key if not exists tbl_ctc_family_informations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_ctc_family_informations add foreign key if not exists tbl_ctc_family_informations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;

alter table tbl_previous_pregnancy_indicators change column  id id char(36);
update tbl_previous_pregnancy_indicators set id = copy_id;
alter table tbl_previous_pregnancy_indicators add foreign key if not exists tbl_previous_pregnancy_indicators_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_previous_pregnancy_indicators add foreign key if not exists tbl_previous_pregnancy_indicators_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_previous_pregnancy_indicators add foreign key if not exists tbl_previous_pregnancy_indicators_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_anti_natal_deworms change column  id id char(36);
update tbl_anti_natal_deworms set id = copy_id;
alter table tbl_anti_natal_deworms add foreign key if not exists tbl_anti_natal_deworms_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_deworms add foreign key if not exists tbl_anti_natal_deworms_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_anti_natal_deworms add foreign key if not exists tbl_anti_natal_deworms_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_family_planning_attendances change column  id id char(36);
update tbl_family_planning_attendances set id = copy_id;
alter table tbl_family_planning_attendances add foreign key if not exists tbl_family_planning_attendances_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_family_planning_attendances add foreign key if not exists tbl_family_planning_attendances_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_family_planning_attendances add foreign key if not exists tbl_family_planning_attendances_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_family_planning_attendances change column  id id char(36);
update tbl_family_planning_attendances set id = copy_id;
alter table tbl_family_planning_attendances add foreign key if not exists tbl_family_planning_attendances_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_family_planning_attendances add foreign key if not exists tbl_family_planning_attendances_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_family_planning_attendances add foreign key if not exists tbl_family_planning_attendances_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_anti_natal_malarias change column  id id char(36);
update tbl_anti_natal_malarias set id = copy_id;
alter table tbl_anti_natal_malarias add foreign key if not exists tbl_anti_natal_malarias_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_malarias add foreign key if not exists tbl_anti_natal_malarias_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_anti_natal_malarias add foreign key if not exists tbl_anti_natal_malarias_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_surgery_physical_examinations change column  id id char(36);
update tbl_surgery_physical_examinations set id = copy_id;
alter table tbl_surgery_physical_examinations add foreign key if not exists tbl_surgery_physical_examinations_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_surgery_physical_examinations add foreign key if not exists tbl_surgery_physical_examinations_request_id_foreign ( request_id ) references tbl_theatre_waits(id) on update cascade;
alter table tbl_surgery_physical_examinations add foreign key if not exists tbl_surgery_physical_examinations_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_anti_natal_reattendances change column  id id char(36);
update tbl_anti_natal_reattendances set id = copy_id;
alter table tbl_anti_natal_reattendances add foreign key if not exists tbl_anti_natal_reattendances_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_reattendances add foreign key if not exists tbl_anti_natal_reattendances_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_anti_natal_reattendances add foreign key if not exists tbl_anti_natal_reattendances_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_fplanning_breast_cancer_investigations change column  id id char(36);
update tbl_fplanning_breast_cancer_investigations set id = copy_id;
alter table tbl_fplanning_breast_cancer_investigations add foreign key if not exists tbl_fplanning_breast_cancer_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_fplanning_breast_cancer_investigations add foreign key if not exists tbl_fplanning_breast_cancer_investigations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_fplanning_breast_cancer_investigations add foreign key if not exists tbl_fplanning_breast_cancer_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_tb_pre_entry_registers change column  id id char(36);
update tbl_tb_pre_entry_registers set id = copy_id;
alter table tbl_tb_pre_entry_registers add foreign key if not exists tbl_tb_pre_entry_registers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_tb_pre_entry_registers add foreign key if not exists tbl_tb_pre_entry_registers_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_tb_pre_entry_registers add foreign key if not exists tbl_tb_pre_entry_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_birth_infos change column  id id char(36);
update tbl_post_natal_birth_infos set id = copy_id;
alter table tbl_post_natal_birth_infos add foreign key if not exists tbl_post_natal_birth_infos_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_birth_infos add foreign key if not exists tbl_post_natal_birth_infos_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_post_natal_birth_infos add foreign key if not exists tbl_post_natal_birth_infos_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_delivery_child_feedings change column  id id char(36);
update tbl_labour_delivery_child_feedings set id = copy_id;
alter table tbl_labour_delivery_child_feedings add foreign key if not exists tbl_labour_delivery_child_feedings_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_delivery_child_feedings add foreign key if not exists tbl_labour_delivery_child_feedings_patient_id_foreign ( patient_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_labour_delivery_child_feedings add foreign key if not exists tbl_labour_delivery_child_feedings_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_fplanning_previous_menstrals change column  id id char(36);
update tbl_fplanning_previous_menstrals set id = copy_id;
alter table tbl_fplanning_previous_menstrals add foreign key if not exists tbl_fplanning_previous_menstrals_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_fplanning_previous_menstrals add foreign key if not exists tbl_fplanning_previous_menstrals_client_id_foreign ( client_id ) references tbl_family_planning_registers(id) on update cascade;
alter table tbl_fplanning_previous_menstrals add foreign key if not exists tbl_fplanning_previous_menstrals_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_post_natal_child_infections change column  id id char(36);
update tbl_post_natal_child_infections set id = copy_id;
alter table tbl_post_natal_child_infections add foreign key if not exists tbl_post_natal_child_infections_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_post_natal_child_infections add foreign key if not exists tbl_post_natal_child_infections_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_post_natal_child_infections add foreign key if not exists tbl_post_natal_child_infections_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_condoms change column  id id char(36);
update tbl_condoms set id = copy_id;
alter table tbl_condoms add foreign key if not exists tbl_condoms_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_condoms add foreign key if not exists tbl_condoms_patient_id_foreign ( patient_id ) references tbl_family_planning_registers(id) on update cascade;
alter table tbl_condoms add foreign key if not exists tbl_condoms_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_pre_history_anethetics change column  id id char(36);
update tbl_pre_history_anethetics set id = copy_id;
alter table tbl_pre_history_anethetics add foreign key if not exists tbl_pre_history_anethetics_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_pre_history_anethetics add foreign key if not exists tbl_pre_history_anethetics_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_pre_history_anethetics add foreign key if not exists tbl_pre_history_anethetics_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_pre_history_anethetics add foreign key if not exists tbl_pre_history_anethetics_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_pre_history_anethetics add foreign key if not exists tbl_pre_history_anethetics_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_inputs change column  id id char(36);
update tbl_inputs set id = copy_id;
alter table tbl_inputs add foreign key if not exists tbl_inputs_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_inputs add foreign key if not exists tbl_inputs_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_inputs add foreign key if not exists tbl_inputs_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_inputs add foreign key if not exists tbl_inputs_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_status_wards change column  id id char(36);
update tbl_status_wards set id = copy_id;
alter table tbl_status_wards add foreign key if not exists tbl_status_wards_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_status_wards add foreign key if not exists tbl_status_wards_ward_id_foreign ( ward_id ) references tbl_wards(id) on update cascade;
alter table tbl_status_wards add foreign key if not exists tbl_status_wards_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_status_wards add foreign key if not exists tbl_status_wards_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_status_wards add foreign key if not exists tbl_status_wards_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_nursing_cares change column  id id char(36);
update tbl_nursing_cares set id = copy_id;
alter table tbl_nursing_cares add foreign key if not exists tbl_nursing_cares_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_nursing_cares add foreign key if not exists tbl_nursing_cares_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_nursing_cares add foreign key if not exists tbl_nursing_cares_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_discharge_permits change column  id id char(36);
update tbl_discharge_permits set id = copy_id;
alter table tbl_discharge_permits add foreign key if not exists tbl_discharge_permits_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_discharge_permits add foreign key if not exists tbl_discharge_permits_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_treatment_charts change column  id id char(36);
update tbl_treatment_charts set id = copy_id;
alter table tbl_treatment_charts add foreign key if not exists tbl_treatment_charts_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_blood_stocks change column  id id char(36);
update tbl_blood_stocks set id = copy_id;
alter table tbl_blood_stocks add foreign key if not exists tbl_blood_stocks_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_blood_stocks add foreign key if not exists tbl_blood_stocks_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_blood_stocks add foreign key if not exists tbl_blood_stocks_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_nutritional_foods change column  id id char(36);
update tbl_nutritional_foods set id = copy_id;
alter table tbl_nutritional_foods add foreign key if not exists tbl_nutritional_foods_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_nutritional_foods add foreign key if not exists tbl_nutritional_foods_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_nutritional_foods add foreign key if not exists tbl_nutritional_foods_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_nutritional_foods add foreign key if not exists tbl_nutritional_foods_visit_id_foreign ( visit_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_blood_screenings change column  id id char(36);
update tbl_blood_screenings set id = copy_id;
alter table tbl_blood_screenings add foreign key if not exists tbl_blood_screenings_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_blood_screenings add foreign key if not exists tbl_blood_screenings_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_blood_screenings add foreign key if not exists tbl_blood_screenings_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_blood_donations change column  id id char(36);
update tbl_blood_donations set id = copy_id;
alter table tbl_blood_donations add foreign key if not exists tbl_blood_donations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_blood_donations add foreign key if not exists tbl_blood_donations_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_blood_donations add foreign key if not exists tbl_blood_donations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_donor_infors change column  id id char(36);
update tbl_donor_infors set id = copy_id;
alter table tbl_donor_infors add foreign key if not exists tbl_donor_infors_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_donor_infors add foreign key if not exists tbl_donor_infors_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_donor_infors add foreign key if not exists tbl_donor_infors_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_past_orthopedic_histories change column  id id char(36);
update tbl_past_orthopedic_histories set id = copy_id;
alter table tbl_past_orthopedic_histories add foreign key if not exists tbl_past_orthopedic_histories_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_past_orthopedic_histories add foreign key if not exists tbl_past_orthopedic_histories_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_past_orthopedic_histories add foreign key if not exists tbl_past_orthopedic_histories_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_past_orthopedic_histories add foreign key if not exists tbl_past_orthopedic_histories_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_past_orthopedic_histories add foreign key if not exists tbl_past_orthopedic_histories_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_ctc_unique_id_patients change column  id id char(36);
update tbl_ctc_unique_id_patients set id = copy_id;
alter table tbl_ctc_unique_id_patients add foreign key if not exists tbl_ctc_unique_id_patients_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_ctc_unique_id_patients add foreign key if not exists tbl_ctc_unique_id_patients_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;

alter table tbl_turning_charts change column  id id char(36);
update tbl_turning_charts set id = copy_id;
alter table tbl_turning_charts add foreign key if not exists tbl_turning_charts_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_turning_charts add foreign key if not exists tbl_turning_charts_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_turning_charts add foreign key if not exists tbl_turning_charts_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_turning_charts add foreign key if not exists tbl_turning_charts_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_therapy_assessments change column  id id char(36);
update tbl_therapy_assessments set id = copy_id;
alter table tbl_therapy_assessments add foreign key if not exists tbl_therapy_assessments_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_therapy_assessments add foreign key if not exists tbl_therapy_assessments_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_therapy_assessments add foreign key if not exists tbl_therapy_assessments_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_therapy_assessments add foreign key if not exists tbl_therapy_assessments_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_client_nutritional_statuses change column  id id char(36);
update tbl_client_nutritional_statuses set id = copy_id;
alter table tbl_client_nutritional_statuses add foreign key if not exists tbl_client_nutritional_statuses_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_client_nutritional_statuses add foreign key if not exists tbl_client_nutritional_statuses_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_client_nutritional_statuses add foreign key if not exists tbl_client_nutritional_statuses_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_client_nutritional_statuses add foreign key if not exists tbl_client_nutritional_statuses_visit_id_foreign ( visit_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_nutritional_statuses change column  id id char(36);
update tbl_nutritional_statuses set id = copy_id;
alter table tbl_nutritional_statuses add foreign key if not exists tbl_nutritional_statuses_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_nutritional_statuses add foreign key if not exists tbl_nutritional_statuses_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_nutritional_statuses add foreign key if not exists tbl_nutritional_statuses_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_nutritional_statuses add foreign key if not exists tbl_nutritional_statuses_visit_id_foreign ( visit_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_birth_histories change column  id id char(36);
update tbl_birth_histories set id = copy_id;
alter table tbl_birth_histories add foreign key if not exists tbl_birth_histories_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_birth_histories add foreign key if not exists tbl_birth_histories_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_birth_histories add foreign key if not exists tbl_birth_histories_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_birth_histories add foreign key if not exists tbl_birth_histories_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_birth_histories add foreign key if not exists tbl_birth_histories_admission_id_foreign ( visit_date_id ) references tbl_admissions(id) on update cascade;

alter table tbl_eye_examination_records change column  id id char(36);
update tbl_eye_examination_records set id = copy_id;
alter table tbl_eye_examination_records add foreign key if not exists tbl_eye_examination_records_clinic_visit_id_foreign ( clinic_visit_id ) references tbl_eyeclinic_visits(id) on update cascade;

alter table tbl_mortuaries change column  id id char(36);
update tbl_mortuaries set id = copy_id;
alter table tbl_mortuaries add foreign key if not exists tbl_mortuaries_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_mortuaries add foreign key if not exists tbl_mortuaries_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_ward_reports change column  id id char(36);
update tbl_ward_reports set id = copy_id;
alter table tbl_ward_reports add foreign key if not exists tbl_ward_reports_ward_id_foreign ( ward_id ) references tbl_wards(id) on update cascade;
alter table tbl_ward_reports add foreign key if not exists tbl_ward_reports_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_nurses_wards change column  id id char(36);
update tbl_nurses_wards set id = copy_id;
alter table tbl_nurses_wards add foreign key if not exists tbl_nurses_wards_ward_id_foreign ( ward_id ) references tbl_nurses_wards(id) on update cascade;
alter table tbl_nurses_wards add foreign key if not exists tbl_nurses_wards_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_nurses_wards add foreign key if not exists tbl_nurses_wards_incharge_id_foreign ( incharge_id ) references users(id) on update cascade;

alter table tbl_beds change column  id id char(36);
update tbl_beds set id = copy_id;
alter table tbl_beds add foreign key if not exists tbl_beds_ward_id_foreign ( ward_id ) references tbl_wards(id) on update cascade;
alter table tbl_beds add foreign key if not exists tbl_beds_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_instructions change column  id id char(36);
update tbl_instructions set id = copy_id;
alter table tbl_instructions add foreign key if not exists tbl_instructions_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_instructions add foreign key if not exists tbl_instructions_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_instructions add foreign key if not exists tbl_instructions_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_instructions add foreign key if not exists tbl_instructions_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;
alter table tbl_instructions add foreign key if not exists tbl_instructions_ward_id_foreign ( ward_id ) references tbl_wards(id) on update cascade;
alter table tbl_instructions add foreign key if not exists tbl_instructions_bed_id_foreign ( bed_id ) references tbl_beds(id) on update cascade;

alter table tbl_attachments change column  id id char(36);
update tbl_attachments set id = copy_id;
alter table tbl_attachments add foreign key if not exists tbl_attachments_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;

alter table tbl_gbv_vacs change column  id id char(36);
update tbl_gbv_vacs set id = copy_id;
alter table tbl_gbv_vacs add foreign key if not exists tbl_gbv_vacs_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_gbv_vacs add foreign key if not exists tbl_gbv_vacs_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_gbv_vacs add foreign key if not exists tbl_gbv_vacs_referral_id_foreign ( referral_id ) references tbl_facilities(id) on update cascade;
alter table tbl_gbv_vacs add foreign key if not exists tbl_gbv_vacs_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_gbv_vacs add foreign key if not exists tbl_gbv_vacs_attachment_id_foreign ( attachment_id ) references tbl_patients(id) on update cascade;

alter table tbl_ctc_transfer_in_particulars change column  id id char(36);
update tbl_ctc_transfer_in_particulars set id = copy_id;
alter table tbl_ctc_transfer_in_particulars add foreign key if not exists tbl_ctc_transfer_in_particulars_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;

alter table tbl_past_medical_records change column  id id char(36);
update tbl_past_medical_records set id = copy_id;
alter table tbl_past_medical_records add foreign key if not exists tbl_past_medical_records_past_medical_history_id_foreign ( past_medical_history_id ) references tbl_past_medical_histories(id) on update cascade;

alter table tbl_marriage_issues change column  id id char(36);
update tbl_marriage_issues set id = copy_id;
alter table tbl_marriage_issues add foreign key if not exists tbl_marriage_issues_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_marriage_issues add foreign key if not exists tbl_marriage_issues_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_marriage_issues add foreign key if not exists tbl_marriage_issues_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_vendors change column  id id char(36);
update tbl_vendors set id = copy_id;
alter table tbl_vendors add foreign key if not exists tbl_vendors_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_environmental_equipment_registers change column  id id char(36);
update tbl_environmental_equipment_registers set id = copy_id;
alter table tbl_environmental_equipment_registers add foreign key if not exists tbl_environmental_equipment_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_environmental_equipment_registers add foreign key if not exists tbl_environmental_equipment_registers_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_complaints change column  id id char(36);
update tbl_complaints set id = copy_id;
alter table tbl_complaints add foreign key if not exists tbl_complaints_history_exam_id_foreign ( history_exam_id ) references tbl_history_examinations(id) on update cascade;

alter table tbl_permits change column  id id char(36);
update tbl_permits set id = copy_id;
alter table tbl_permits add foreign key if not exists tbl_permits_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_permits add foreign key if not exists tbl_permits_corpse_id_foreign ( corpse_id ) references tbl_corpses(id) on update cascade;

alter table tbl_social_referrals change column  id id char(36);
update tbl_social_referrals set id = copy_id;
alter table tbl_social_referrals add foreign key if not exists tbl_social_referrals_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_social_referrals add foreign key if not exists tbl_social_referrals_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_social_referrals add foreign key if not exists tbl_social_referrals_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_treatment_time_flows change column  id id char(36);
update tbl_treatment_time_flows set id = copy_id;
alter table tbl_treatment_time_flows add foreign key if not exists tbl_treatment_time_flows_treatment_charts_id_foreign ( treatment_charts_id ) references tbl_treatment_charts(id) on update cascade;

alter table tbl_nuisance_composes change column  id id char(36);
update tbl_nuisance_composes set id = copy_id;
alter table tbl_nuisance_composes add foreign key if not exists tbl_nuisance_composes_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_nuisance_composes add foreign key if not exists tbl_nuisance_composes_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_obs_gyn_records change column  id id char(36);
update tbl_obs_gyn_records set id = copy_id;
alter table tbl_obs_gyn_records add foreign key if not exists tbl_obs_gyn_records_obs_gyn_id_foreign ( obs_gyn_id ) references tbl_obs_gyns(id) on update cascade;

alter table tbl_physical_examination_records change column  id id char(36);
update tbl_physical_examination_records set id = copy_id;
alter table tbl_physical_examination_records add foreign key if not exists tbl_physical_examination_records_physical_examination_id_foreign ( physical_examination_id ) references tbl_physical_examinations(id) on update cascade;

alter table tbl_past_psych_records change column  id id char(36);
update tbl_past_psych_records set id = copy_id;
alter table tbl_past_psych_records add foreign key if not exists tbl_past_psych_records_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_past_psych_records add foreign key if not exists tbl_past_psych_records_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_past_psych_records add foreign key if not exists tbl_past_psych_records_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_inventory_orders change column  id id char(36);
update tbl_inventory_orders set id = copy_id;
alter table tbl_inventory_orders add foreign key if not exists tbl_inventory_orders_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_inventory_orders add foreign key if not exists tbl_inventory_orders_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_staff_sections change column  id id char(36);
update tbl_staff_sections set id = copy_id;
alter table tbl_staff_sections add foreign key if not exists tbl_staff_sections_technologist_id_foreign ( technologist_id ) references users(id) on update cascade;

alter table tbl_anti_rabies_vaccinations change column  id id char(36);
update tbl_anti_rabies_vaccinations set id = copy_id;
alter table tbl_anti_rabies_vaccinations add foreign key if not exists tbl_anti_rabies_vaccinations_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_anti_rabies_registries change column  id id char(36);
update tbl_anti_rabies_registries set id = copy_id;
alter table tbl_anti_rabies_registries add foreign key if not exists tbl_anti_rabies_registries_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_rabies_registries add foreign key if not exists tbl_anti_rabies_registries_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_anti_rabies_registries add foreign key if not exists tbl_anti_rabies_registries_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_anti_rabies_registries add foreign key if not exists tbl_anti_rabies_registries_vaccination_id_foreign ( vaccination_id ) references tbl_anti_rabies_vaccinations(id) on update cascade;

alter table tbl_ledgers change column  id id char(36);
update tbl_ledgers set id = copy_id;
alter table tbl_ledgers add foreign key if not exists tbl_ledgers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_last_logins change column  id id char(36);
update tbl_last_logins set id = copy_id;
alter table tbl_last_logins add foreign key if not exists tbl_last_logins_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_last_logins change column  id id char(36);
update tbl_last_logins set id = copy_id;
alter table tbl_last_logins add foreign key if not exists tbl_last_logins_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_exemptions change column  id id char(36);
update tbl_exemptions set id = copy_id;
alter table tbl_exemptions add foreign key if not exists tbl_exemptions_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_exemptions add foreign key if not exists tbl_exemptions_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_exemptions add foreign key if not exists tbl_exemptions_attachment_id_foreign ( attachment_id ) references tbl_attachments(id) on update cascade;

alter table tbl_store_lists change column  id id char(36);
update tbl_store_lists set id = copy_id;
alter table tbl_store_lists add foreign key if not exists tbl_store_lists_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_forensic_histories change column  id id char(36);
update tbl_forensic_histories set id = copy_id;
alter table tbl_forensic_histories add foreign key if not exists tbl_forensic_histories_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_forensic_histories add foreign key if not exists tbl_forensic_histories_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_forensic_histories add foreign key if not exists tbl_forensic_histories_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_equipments change column  id id char(36);
update tbl_equipments set id = copy_id;
alter table tbl_equipments add foreign key if not exists tbl_equipments_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_equipments add foreign key if not exists tbl_equipments_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_registration_statuses change column  id id char(36);
update tbl_registration_statuses set id = copy_id;
alter table tbl_registration_statuses add foreign key if not exists tbl_registration_statuses_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;

alter table tbl_environmental_equipment_receivings change column  id id char(36);
update tbl_environmental_equipment_receivings set id = copy_id;
alter table tbl_environmental_equipment_receivings add foreign key if not exists tbl_environmental_equipment_receivings_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_environmental_equipment_receivings add foreign key if not exists tbl_environmental_equipment_receivings_equipment_id_foreign ( equipment_id ) references tbl_equipments(id) on update cascade;
alter table tbl_environmental_equipment_receivings add foreign key if not exists tbl_environmental_equipment_receivings_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_permission_users change column  id id char(36);
update tbl_permission_users set id = copy_id;
alter table tbl_permission_users add foreign key if not exists tbl_permission_users_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_user_store_configurations change column  id id char(36);
update tbl_user_store_configurations set id = copy_id;
alter table tbl_user_store_configurations add foreign key if not exists tbl_user_store_configurations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_user_store_configurations add foreign key if not exists tbl_user_store_configurations_store_id_foreign ( store_id ) references tbl_store_lists(id) on update cascade;

alter table tbl_notifications change column  id id char(36);
update tbl_notifications set id = copy_id;
alter table tbl_notifications add foreign key if not exists tbl_notifications_sender_id_foreign ( sender_id ) references users(id) on update cascade;
alter table tbl_notifications add foreign key if not exists tbl_notifications_receiver_id_foreign ( receiver_id ) references users(id) on update cascade;

alter table tbl_invoices change column  id id char(36);
update tbl_invoices set id = copy_id;
alter table tbl_invoices add foreign key if not exists tbl_invoices_vendor_id_foreign ( vendor_id ) references tbl_vendors(id) on update cascade;

alter table tbl_receiving_items change column  id id char(36);
update tbl_receiving_items set id = copy_id;
alter table tbl_receiving_items add foreign key if not exists tbl_receiving_items_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_receiving_items add foreign key if not exists tbl_receiving_items_received_store_id_foreign ( received_store_id ) references tbl_store_lists(id) on update cascade;
alter table tbl_receiving_items add foreign key if not exists tbl_receiving_items_requesting_store_id_foreign ( requesting_store_id ) references tbl_store_lists(id) on update cascade;
alter table tbl_receiving_items add foreign key if not exists tbl_receiving_items_internal_issuer_id_foreign ( internal_issuer_id ) references tbl_store_lists(id) on update cascade;
alter table tbl_receiving_items add foreign key if not exists tbl_receiving_items_received_from_id_foreign ( received_from_id ) references tbl_vendors(id) on update cascade;
alter table tbl_receiving_items add foreign key if not exists tbl_receiving_items_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_receiving_items add foreign key if not exists tbl_receiving_items_attachment_id_foreign ( attachment_id ) references tbl_attachments(id) on update cascade;
alter table tbl_receiving_items add foreign key if not exists tbl_receiving_items_invoice_refference_foreign ( invoice_refference ) references tbl_invoices(id) on update cascade;

alter table tbl_sub_stores change column  id id char(36);
update tbl_sub_stores set id = copy_id;
alter table tbl_sub_stores add foreign key if not exists tbl_sub_stores_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_sub_stores add foreign key if not exists tbl_sub_stores_issued_store_id_foreign ( issued_store_id ) references tbl_store_lists(id) on update cascade;
alter table tbl_sub_stores add foreign key if not exists tbl_sub_stores_requested_store_id_foreign ( requested_store_id ) references tbl_store_lists(id) on update cascade;
alter table tbl_sub_stores add foreign key if not exists tbl_sub_stores_received_from_id_foreign ( received_from_id ) references tbl_store_lists(id) on update cascade;

alter table tbl_dispensers change column  id id char(36);
update tbl_dispensers set id = copy_id;
alter table tbl_dispensers add foreign key if not exists tbl_dispensers_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_dispensers add foreign key if not exists tbl_dispensers_dispenser_id_foreign ( dispenser_id ) references tbl_store_lists(id) on update cascade;
alter table tbl_dispensers add foreign key if not exists tbl_dispensers_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_dispensers add foreign key if not exists tbl_dispensers_received_from_id_foreign ( received_from_id ) references tbl_store_lists(id) on update cascade;

alter table tbl_cabinets change column  id id char(36);
update tbl_cabinets set id = copy_id;
alter table tbl_cabinets add foreign key if not exists tbl_cabinets_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_cabinets add foreign key if not exists tbl_cabinets_mortuary_id_foreign ( mortuary_id ) references tbl_mortuaries(id) on update cascade;

alter table tbl_corpse_admissions change column  id id char(36);
update tbl_corpse_admissions set id = copy_id;
alter table tbl_corpse_admissions add foreign key if not exists tbl_corpse_admissions_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_corpse_admissions add foreign key if not exists tbl_corpse_admissions_corpse_received_id_foreign ( corpse_received_id ) references users(id) on update cascade;
alter table tbl_corpse_admissions add foreign key if not exists tbl_corpse_admissions_mortuary_id_foreign ( mortuary_id ) references tbl_mortuaries(id) on update cascade;
alter table tbl_corpse_admissions add foreign key if not exists tbl_corpse_admissions_cabinet_id_foreign ( cabinet_id ) references tbl_cabinets(id) on update cascade;
alter table tbl_corpse_admissions add foreign key if not exists tbl_corpse_admissions_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_corpse_admissions add foreign key if not exists tbl_corpse_admissions_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_corpse_services change column  id id char(36);
update tbl_corpse_services set id = copy_id;
alter table tbl_corpse_services add foreign key if not exists tbl_corpse_services_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_corpse_services add foreign key if not exists tbl_corpse_services_corpse_admission_id_foreign ( corpse_admission_id ) references tbl_corpse_admissions(id) on update cascade;

alter table tbl_review_of_systems change column  id id char(36);
update tbl_review_of_systems set id = copy_id;
alter table tbl_review_of_systems add foreign key if not exists tbl_review_of_systems_review_system_id_foreign ( review_system_id ) references tbl_review_systems(id) on update cascade;

alter table tbl_wards_nurses change column  id id char(36);
update tbl_wards_nurses set id = copy_id;
alter table tbl_wards_nurses add foreign key if not exists tbl_wards_nurses_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_wards_nurses add foreign key if not exists tbl_wards_nurses_ward_id_foreign ( ward_id ) references tbl_wards(id) on update cascade;

alter table tbl_registrar_services change column  id id char(36);
update tbl_registrar_services set id = copy_id;
alter table tbl_registrar_services add foreign key if not exists tbl_registrar_services_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_exemption_accesses change column  id id char(36);
update tbl_exemption_accesses set id = copy_id;
alter table tbl_exemption_accesses add foreign key if not exists tbl_exemption_accesses_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_sample_number_controls change column  id id char(36);
update tbl_sample_number_controls set id = copy_id;
alter table tbl_sample_number_controls add foreign key if not exists tbl_sample_number_controls_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_sample_number_controls add foreign key if not exists tbl_sample_number_controls_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_testspanels change column  id id char(36);
update tbl_testspanels set id = copy_id;
alter table tbl_testspanels add foreign key if not exists tbl_testspanels_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_testspanels add foreign key if not exists tbl_testspanels_equipment_id_foreign ( equipment_id ) references tbl_equipments(id) on update cascade;

alter table tbl_tests change column  id id char(36);
update tbl_tests set id = copy_id;
alter table tbl_tests add foreign key if not exists tbl_tests_equipment_id_foreign ( equipment_id ) references tbl_equipments(id) on update cascade;

alter table tbl_nurse_wards change column  id id char(36);
update tbl_nurse_wards set id = copy_id;
alter table tbl_nurse_wards add foreign key if not exists tbl_nurse_wards_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_nurse_wards add foreign key if not exists tbl_nurse_wards_ward_id_foreign ( ward_id ) references tbl_wards(id) on update cascade;
alter table tbl_nurse_wards add foreign key if not exists tbl_nurse_wards_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_family_social_histories change column  id id char(36);
update tbl_family_social_histories set id = copy_id;
alter table tbl_family_social_histories add foreign key if not exists tbl_family_social_histories_family_history_id_foreign ( family_history_id ) references tbl_family_histories(id) on update cascade;

alter table tbl_emergency_survey_histories change column  id id char(36);
update tbl_emergency_survey_histories set id = copy_id;
alter table tbl_emergency_survey_histories add foreign key if not exists tbl_emergency_survey_histories_survey_history_id_foreign ( survey_history_id ) references tbl_survey_histories(id) on update cascade;


alter table tbl_inventory_items change column  id id char(36);
update tbl_inventory_items set id = copy_id;
alter table tbl_inventory_items add foreign key if not exists tbl_inventory_items_item_type_id_foreign ( item_type_id ) references tbl_ledgers(id) on update cascade;


alter table tbl_inventory_receivings change column  id id char(36);
update tbl_inventory_receivings set id = copy_id;
alter table tbl_inventory_receivings add foreign key if not exists tbl_inventory_receivings_order_number_foreign ( order_number ) references tbl_inventory_orders(id) on update cascade;
alter table tbl_inventory_receivings add foreign key if not exists tbl_inventory_receivings_item_id_foreign ( item_id ) references tbl_inventory_items(id) on update cascade;


alter table tbl_inventory_requests change column  id id char(36);
update tbl_inventory_requests set id = copy_id;
alter table tbl_inventory_requests add foreign key if not exists tbl_inventory_requests_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_inventory_requests add foreign key if not exists tbl_inventory_requests_item_id_foreign ( item_id ) references tbl_inventory_items(id) on update cascade;


alter table tbl_inventory_issuings change column  id id char(36);
update tbl_inventory_issuings set id = copy_id;
alter table tbl_inventory_issuings add foreign key if not exists tbl_inventory_issuings_issuing_officer_id_foreign ( issuing_officer_id ) references users(id) on update cascade;
alter table tbl_inventory_issuings add foreign key if not exists tbl_inventory_issuings_receiver_id_foreign ( receiver_id ) references users(id) on update cascade;
alter table tbl_inventory_issuings add foreign key if not exists tbl_inventory_issuings_item_received_id_foreign ( item_received_id ) references tbl_inventory_receivings(id) on update cascade;

alter table tbl_service_outof_stock change column  id id char(36);
update tbl_service_outof_stock set id = copy_id;
alter table tbl_service_outof_stock add foreign key if not exists tbl_service_outof_stock_order_id_foreign ( order_id ) references tbl_requests(id) on update cascade;
alter table tbl_service_outof_stock add foreign key if not exists tbl_service_outof_stock_test_id_foreign ( test_id ) references tbl_requests(id) on update cascade;

alter table tbl_orders change column  id id char(36);
update tbl_orders set id = copy_id;
alter table tbl_orders add foreign key if not exists tbl_orders_processor_id_foreign ( processor_id ) references users(id) on update cascade;
alter table tbl_orders add foreign key if not exists tbl_orders_receiver_id_foreign ( receiver_id ) references users(id) on update cascade;
alter table tbl_orders add foreign key if not exists tbl_orders_order_validator_id_foreign ( order_validator_id ) references users(id) on update cascade;
alter table tbl_orders add foreign key if not exists tbl_orders_order_id_foreign ( order_id ) references tbl_orders(id) on update cascade;

alter table tbl_panel_components_results change column  id id char(36);
update tbl_panel_components_results set id = copy_id;
alter table tbl_panel_components_results add foreign key if not exists tbl_panel_components_results_component_id_foreign ( component_id ) references tbl_requests(id) on update cascade;
alter table tbl_panel_components_results add foreign key if not exists tbl_panel_components_results_order_id_foreign ( order_id ) references tbl_orders(id) on update cascade;

alter table tbl_patient_discharges_payments change column  id id char(36);
update tbl_patient_discharges_payments set id = copy_id;
alter table tbl_patient_discharges_payments add foreign key if not exists tbl_patient_discharges_payments_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;
alter table tbl_patient_discharges_payments add foreign key if not exists tbl_patient_discharges_payments_item_transaction_id_foreign ( item_transaction_id ) references tbl_encounter_invoices(id) on update cascade;

alter table tbl_continuation_notes change column  id id char(36);
update tbl_continuation_notes set id = copy_id;
alter table tbl_continuation_notes add foreign key if not exists tbl_continuation_notes_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_continuation_notes add foreign key if not exists tbl_continuation_notes_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_continuation_notes add foreign key if not exists tbl_continuation_notes_visit_id_foreign ( visit_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_general_appointments change column  id id char(36);
update tbl_general_appointments set id = copy_id;
alter table tbl_general_appointments add foreign key if not exists tbl_general_appointments_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_general_appointments add foreign key if not exists tbl_general_appointments_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_general_appointments add foreign key if not exists tbl_general_appointments_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_next_of_kins change column  id id char(36);
update tbl_next_of_kins set id = copy_id;
alter table tbl_next_of_kins add foreign key if not exists tbl_next_of_kins_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;

alter table tbl_partial_payments change column  id id char(36);
update tbl_partial_payments set id = copy_id;
alter table tbl_partial_payments add foreign key if not exists tbl_partial_payments_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_partial_payments add foreign key if not exists tbl_partial_payments_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_partial_payments add foreign key if not exists tbl_partial_payments_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_partial_payments add foreign key if not exists tbl_partial_payments_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_partial_payments add foreign key if not exists tbl_partial_payments_invoice_id_foreign ( invoice_id ) references tbl_encounter_invoices(id) on update cascade;

alter table tbl_past_ent_histories change column  id id char(36);
update tbl_past_ent_histories set id = copy_id;
alter table tbl_past_ent_histories add foreign key if not exists tbl_past_ent_histories_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_past_ent_histories add foreign key if not exists tbl_past_ent_histories_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_past_ent_histories add foreign key if not exists tbl_past_ent_histories_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_past_ent_histories add foreign key if not exists tbl_past_ent_histories_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;
alter table tbl_past_ent_histories add foreign key if not exists tbl_past_ent_histories_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_past_psych_records change column  id id char(36);
update tbl_past_psych_records set id = copy_id;
alter table tbl_past_psych_records add foreign key if not exists tbl_past_psych_records_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_past_psych_records add foreign key if not exists tbl_past_psych_records_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_past_psych_records add foreign key if not exists tbl_past_psych_records_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_refferal_externals change column  id id char(36);
update tbl_refferal_externals set id = copy_id;
alter table tbl_refferal_externals add foreign key if not exists tbl_refferal_externals_escorting_staff_foreign ( escorting_staff ) references users(id) on update cascade;
alter table tbl_refferal_externals add foreign key if not exists tbl_refferal_externals_reffered_by_foreign ( reffered_by ) references users(id) on update cascade;
alter table tbl_refferal_externals add foreign key if not exists tbl_refferal_externals_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_refferal_externals add foreign key if not exists tbl_refferal_externals_sender_facility_id_foreign ( sender_facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_therapy_treatments change column  id id char(36);
update tbl_therapy_treatments set id = copy_id;
alter table tbl_therapy_treatments add foreign key if not exists tbl_therapy_treatments_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_therapy_treatments add foreign key if not exists tbl_therapy_treatments_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_therapy_treatments add foreign key if not exists tbl_therapy_treatments_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;
alter table tbl_therapy_treatments add foreign key if not exists tbl_therapy_treatments_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_anti_natal_preventives change column  id id char(36);
update tbl_anti_natal_preventives set id = copy_id;
alter table tbl_anti_natal_preventives add foreign key if not exists tbl_anti_natal_preventives_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_anti_natal_preventives add foreign key if not exists tbl_anti_natal_preventives_client_id_foreign ( client_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_anti_natal_preventives add foreign key if not exists tbl_anti_natal_preventives_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_configurations change column  id id char(36);
update tbl_configurations set id = copy_id;
alter table tbl_configurations add foreign key if not exists tbl_configurations_user_id_foreign ( user_id ) references users(id) on update cascade;

alter table tbl_fplanning_placenta_cancer_investigations change column  id id char(36);
update tbl_fplanning_placenta_cancer_investigations set id = copy_id;
alter table tbl_fplanning_placenta_cancer_investigations add foreign key if not exists tbl_fplanning_placenta_cancer_investigations_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_fplanning_placenta_cancer_investigations add foreign key if not exists tbl_fplanning_placenta_cancer_investigations_client_id_foreign ( client_id ) references tbl_family_planning_registers(id) on update cascade;
alter table tbl_fplanning_placenta_cancer_investigations add foreign key if not exists tbl_fplanning_placenta_cancer_investigations_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_labour_delivery_child_dispositions change column  id id char(36);
update tbl_labour_delivery_child_dispositions set id = copy_id;
alter table tbl_labour_delivery_child_dispositions add foreign key if not exists tbl_labour_delivery_child_dispositions_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_labour_delivery_child_dispositions add foreign key if not exists tbl_labour_delivery_child_dispositions_patient_id_foreign ( patient_id ) references tbl_anti_natal_registers(id) on update cascade;
alter table tbl_labour_delivery_child_dispositions add foreign key if not exists tbl_labour_delivery_child_dispositions_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_output_observations change column  id id char(36);
update tbl_output_observations set id = copy_id;
alter table tbl_output_observations add foreign key if not exists tbl_output_observations_nurse_id_foreign ( nurse_id ) references users(id) on update cascade;
alter table tbl_output_observations add foreign key if not exists tbl_output_observations_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;

alter table tbl_panels change column  id id char(36);
update tbl_panels set id = copy_id;
alter table tbl_panels add foreign key if not exists tbl_panels_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_panels add foreign key if not exists tbl_panels_equipment_id_foreign ( equipment_id ) references tbl_equipments(id) on update cascade;


alter table tbl_permits change column  id id char(36);
update tbl_permits set id = copy_id;
alter table tbl_permits add foreign key if not exists tbl_permits_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_permits add foreign key if not exists tbl_permits_corpse_id_foreign ( corpse_id ) references tbl_corpses(id) on update cascade;
alter table tbl_permits add foreign key if not exists tbl_permits_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_admission_registers change column  id id char(36);
update tbl_admission_registers set id = copy_id;
alter table tbl_admission_registers add foreign key if not exists tbl_admission_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_newattendance_registers change column  id id char(36);
update tbl_newattendance_registers set id = copy_id;
alter table tbl_newattendance_registers add foreign key if not exists tbl_newattendance_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_reattendance_registers change column  id id char(36);
update tbl_reattendance_registers set id = copy_id;
alter table tbl_reattendance_registers add foreign key if not exists tbl_reattendance_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_opd_diseases_registers change column  id id char(36);
update tbl_opd_diseases_registers set id = copy_id;
alter table tbl_opd_diseases_registers add foreign key if not exists tbl_opd_diseases_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_ipd_diseases_registers change column  id id char(36);
update tbl_ipd_diseases_registers set id = copy_id;
alter table tbl_ipd_diseases_registers add foreign key if not exists tbl_ipd_diseases_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_outgoing_referral_registers change column  id id char(36);
update tbl_outgoing_referral_registers set id = copy_id;
alter table tbl_outgoing_referral_registers add foreign key if not exists tbl_outgoing_referral_registers_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_integrating_keys change column  id id char(36);
update tbl_integrating_keys set id = copy_id;
alter table tbl_integrating_keys add foreign key if not exists tbl_integrating_keys_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_route_keys change column  id id char(36);
update tbl_route_keys set id = copy_id;
alter table tbl_route_keys add foreign key if not exists tbl_route_keys_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

alter table tbl_investigation_details change column  id id char(36);
update tbl_investigation_details set id = copy_id;
alter table tbl_investigation_details add foreign key if not exists tbl_investigation_details_investigation_id_foreign ( investigation_id ) references tbl_investigations(id) on update cascade;

alter table tbl_comma_scales_histories change column  id id char(36);
update tbl_comma_scales_histories set id = copy_id;
alter table tbl_comma_scales_histories add foreign key if not exists tbl_comma_scales_histories_comma_scale_id_foreign ( comma_scale_id ) references tbl_comma_scales(id) on update cascade;

alter table tbl_informed_consents change column  id id char(36);
update tbl_informed_consents set id = copy_id;
alter table tbl_informed_consents add foreign key if not exists tbl_informed_consents_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_informed_consents add foreign key if not exists tbl_informed_consents_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_informed_consents add foreign key if not exists tbl_informed_consents_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_informed_consents add foreign key if not exists tbl_informed_consents_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;
alter table tbl_informed_consents add foreign key if not exists tbl_informed_consents_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_past_urology_histories change column  id id char(36);
update tbl_past_urology_histories set id = copy_id;
alter table tbl_past_urology_histories add foreign key if not exists tbl_past_urology_histories_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_past_urology_histories add foreign key if not exists tbl_past_urology_histories_patient_id_foreign ( patient_id ) references tbl_patients(id) on update cascade;
alter table tbl_past_urology_histories add foreign key if not exists tbl_past_urology_histories_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;
alter table tbl_past_urology_histories add foreign key if not exists tbl_past_urology_histories_admission_id_foreign ( admission_id ) references tbl_admissions(id) on update cascade;
alter table tbl_past_urology_histories add foreign key if not exists tbl_past_urology_histories_visit_date_id_foreign ( visit_date_id ) references tbl_accounts_numbers(id) on update cascade;

alter table tbl_pediatric_post_natals change column  id id char(36);
update tbl_pediatric_post_natals set id = copy_id;
alter table tbl_pediatric_post_natals add foreign key if not exists tbl_pediatric_post_natals_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_pediatric_post_natals add foreign key if not exists tbl_pediatric_post_natals_client_id_foreign ( client_id ) references tbl_patients(id) on update cascade;
alter table tbl_pediatric_post_natals add foreign key if not exists tbl_pediatric_post_natals_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;


alter table tbl_cash_deposits change column  id id char(36);
update tbl_cash_deposits set id = copy_id;
alter table tbl_cash_deposits add foreign key if not exists tbl_cash_deposits_user_id_foreign ( user_id ) references users(id) on update cascade;
alter table tbl_cash_deposits add foreign key if not exists tbl_cash_deposits_facility_id_foreign ( facility_id ) references tbl_facilities(id) on update cascade;

-- ++++++++++++++++++++++++++++++BRING BACk KEYS +++++++++++++++++++++++++++