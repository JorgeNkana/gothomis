CREATE OR REPLACE VIEW vw_patients_with_pending_bills AS 
			(SELECT t1.id AS receipt_number,t1.facility_id, t2.patient_id, NULL as corpse_id,t7.first_name,t7.middle_name,t7.last_name,t7.medical_record_number,CONCAT(ifnull(t7.first_name,''),' 
			',ifnull(t7.middle_name,''), ' ', ifnull(t7.last_name,''), ' # ',t7.medical_record_number) as name,t2.payment_filter
			FROM tbl_encounter_invoices t1 
			INNER JOIN tbl_invoice_lines t2 ON t2.status_id = 1 and t1.id = t2.invoice_id and (timestampdiff(hour, t1.created_at, current_timestamp) <= 24 OR EXISTS (select * from tbl_admissions t8 where t8.patient_id = t2.patient_id and admission_status_id in (2,3) and timestampdiff(day, t8.updated_at, current_timestamp) <= 28))
			INNER JOIN tbl_item_prices t3 ON t2.item_price_id = t3.id
			INNER JOIN tbl_pay_cat_sub_categories t4 on t4.id = t2.payment_filter AND ((t3.onetime = 0 and t4.pay_cat_id = 1) OR (t3.insurance = 0 and t4.pay_cat_id =2) OR (t3.exemption_status = 1 and t4.pay_cat_id = 3))
			INNER JOIN tbl_patients t7 on t7.id = t2.patient_id
			GROUP BY t1.id ORDER BY t1.id desc)
			
			UNION
			
			(SELECT t1.id AS receipt_number,t1.facility_id, NULL as patient_id,t2.corpse_id,t7.first_name,t7.middle_name,t7.last_name,t7.corpse_record_number as medical_record_number,CONCAT(ifnull(t7.first_name,''),' ',ifnull(t7.middle_name,''), ' ', ifnull(t7.last_name,''), ' # ',t7.corpse_record_number) as name,t2.payment_filter
			FROM tbl_encounter_invoices t1 
			INNER JOIN tbl_invoice_lines t2 ON t2.status_id = 1 and t1.id = t2.invoice_id and timestampdiff(hour, t1.created_at, current_timestamp) <= 24
			INNER JOIN tbl_item_prices t3 ON t2.item_price_id = t3.id
			INNER JOIN tbl_corpses t7 on t7.id = t2.corpse_id
			GROUP BY t1.id ORDER BY t1.id desc);

			
-- Provides list of unpaid bills details within 48 hours
CREATE OR REPLACE VIEW vw_pending_bills AS 
			(SELECT t1.created_at, t1.id AS receipt_number,t1.facility_id, t2.patient_id, t7.first_name,t7.middle_name,t7.last_name,t7.medical_record_number,CONCAT(ifnull(t7.first_name,''),' ',ifnull(t7.middle_name,''), ' ', ifnull(t7.last_name,''), ' # ',t7.medical_record_number) as name, timestampdiff(year,t7.dob,t1.created_at) age, t7.gender, NULL as corpse_id, t2.id, t2.quantity, t2.discount, t3.price, t8.item_name, t4.sub_category_name, t4.pay_cat_id as main_category_id
			FROM tbl_encounter_invoices t1 
			INNER JOIN tbl_invoice_lines t2 ON t2.status_id = 1 and t1.id = t2.invoice_id and (timestampdiff(hour, t1.created_at, current_timestamp) <= 24 OR EXISTS (select * from tbl_admissions t8 where t8.patient_id = t2.patient_id and admission_status_id in (2,3)and timestampdiff(day, t8.updated_at, current_timestamp) <= 28))
			INNER JOIN tbl_item_prices t3 ON t2.item_price_id = t3.id
			INNER JOIN tbl_items t8 ON t8.id = t3.item_id 
			INNER JOIN tbl_pay_cat_sub_categories t4 on t4.id = t2.payment_filter AND ((t3.onetime = 0 and t4.pay_cat_id = 1) OR (t3.insurance = 0 and t4.pay_cat_id =2) OR (t3.exemption_status = 1 and t4.pay_cat_id = 3))
			INNER JOIN tbl_patients t7 on t7.id = t2.patient_id ORDER BY t1.id)
			
			UNION
			
			(SELECT t1.created_at, t1.id AS receipt_number,t1.facility_id, NULL as patient_id, t7.first_name,t7.middle_name,t7.last_name,t7.corpse_record_number as medical_record_number,CONCAT(ifnull(t7.first_name,''),' ',ifnull(t7.middle_name,''), ' ', ifnull(t7.last_name,''), ' # ',t7.corpse_record_number) as name, NULL age, NULL gender, t2.corpse_id, t2.id, t2.quantity, t2.discount, t3.price, t8.item_name, t4.sub_category_name, t4.pay_cat_id as main_category_id
			FROM tbl_encounter_invoices t1 
			INNER JOIN tbl_invoice_lines t2 ON t2.status_id = 1 and t1.id = t2.invoice_id 
			INNER JOIN tbl_item_prices t3 ON t2.item_price_id = t3.id
			INNER JOIN tbl_items t8 ON t8.id = t3.item_id 
			INNER JOIN tbl_pay_cat_sub_categories t4 on t4.id = t2.payment_filter
			INNER JOIN tbl_corpses t7 on t7.id = t2.corpse_id ORDER BY t1.id);

CREATE OR REPLACE VIEW `vw_recent_patients` AS (SELECT
		t1.id, t1.account_number, t1.facility_id, t1.patient_id from tbl_accounts_numbers t1 left join tbl_admissions t2 on t1.id = t2.account_id where (timestampdiff(hour, t1.created_at, current_timestamp) <=48 or t2.admission_status_id not in (4,5,7)) order by t1.id desc);


CREATE OR REPLACE VIEW `vw_patients_to_pos` AS (SELECT
        t1.id AS patient_id,
        t1.first_name,
        t1.middle_name,
        t1.last_name,
        t1.gender,
        t1.dob,
        t1.medical_record_number,
        t2.account_number,
        t2.id AS account_id,
        t2.facility_id,
        t3.id AS invoice_id,
        t4.main_category_id,        
        t5.sub_category_name,
        t6.category_description,
        t4.bill_id AS patient_category_id,
		concat(t1.first_name,' ',t1.middle_name, ' ',t1.last_name, '#',
        t1.medical_record_number) as name
        FROM tbl_patients t1 
		INNER JOIN vw_recent_patients as t2 on t1.id = t2.patient_id 
		INNER JOIN tbl_encounter_invoices t3 on t2.id = t3.account_number_id 
		INNER JOIN tbl_bills_categories t4 on t2.id = t4.account_id 
		INNER JOIN tbl_pay_cat_sub_categories t5 on t5.id = t4.bill_id 
		INNER JOIN tbl_payments_categories t6 on t6.id = t4.main_category_id
        GROUP BY patient_id );
		

CREATE OR REPLACE VIEW `vw_opd_patients` AS (SELECT 
        t1.id AS patient_id,
        t1.first_name,
        t1.middle_name,
        t1.last_name,
        t1.medical_record_number,
		concat(t1.first_name,' ',t1.middle_name, ' ',t1.last_name, ' #', t1.medical_record_number) name,
		t1.residence_id,
        t1.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END AS age,
        (TIMESTAMPDIFF(YEAR,t1.dob, CURRENT_DATE)) AS umri,
        t1.gender,
        t2.account_number,
        t2.status,
		t2.tallied,
        t2.id AS account_id,
        t2.date_attended AS visit_date,
        t3.payment_filter,
        t6.sub_category_name,
        t4.main_category_id,
		t4.bill_id,
        t2.facility_id
        FROM tbl_patients t1 INNER JOIN tbl_accounts_numbers t2 ON
		(timestampdiff(hour,t2.created_at,CURRENT_TIMESTAMP))<= 24 AND
		t2.patient_id = t1.id
		INNER JOIN tbl_invoice_lines t3 ON t3.patient_id = t1.id 
		INNER JOIN tbl_bills_categories t4 ON t4.account_id = t2.id
		INNER JOIN tbl_pay_cat_sub_categories t6 ON t6.id = t4.bill_id
		where ((t3.`status_id`=2)  OR (t3.`status_id`=1 AND `main_category_id` != 1)) and not exists (select patient_id from tbl_corpse_admissions where patient_id = t1.id) GROUP BY t1.id);