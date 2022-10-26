CREATE OR REPLACE VIEW  `vw_postedResults` AS
        SELECT
t5.id as patient_id,
t5.first_name,
t5.middle_name,
t5.last_name,
t5.medical_record_number,
t5.mobile_number,
t5.dob,
 CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END
AS Ages,
        (TIMESTAMPDIFF(YEAR,t5.dob, CURRENT_DATE)) AS age,
t1.order_id,
t1.description,
t1.created_at,
t1.eraser,
t1.post_time,
t1.attached_image,
t1.remarks,
t2.visit_date_id,
t3.priority,
t3.clinical_note,
t4.item_name,
t4.dept_id

    FROM 
    tbl_results t1
    join 
     tbl_requests t2 on t2.id = t1.order_id
     join
     
       tbl_orders t3 on DATE(t2.created_at) = DATE(t3.created_at) AND t3.order_id = t2.id
     join 
       tbl_items t4 on t1.item_id = t4.id
       
       join
        tbl_patients t5 on t2.patient_id = t5.id
    
    WHERE 
    dept_id = 3
    group by t1.item_id,t1.id;

CREATE OR REPLACE VIEW `vw_sub_department_summary` AS

select t2.sub_department_name,t3.item_name,t3.facility_id,t3.updated_at,t3.updated_at as created_at,t3.id,((t3.price * quantity)-t3.discount) AS resultant_pay
 from tbl_item_sub_departments t1 
 join tbl_sub_departments t2 on t1.sub_dept_id=t2.id
 join tbl_invoice_lines t3 on t3.item_id=t1.item_id
 WHERE t3.status_id=2 group by t3.id; 
 
  CREATE OR REPLACE VIEW `vw_pending_admission` AS (
        SELECT  t2.patient_id,
        t1.medical_record_number,
        t1.mobile_number,
        t4.admission_id,
        t4.ward_id,
        t9.name,
        t9.mobile_number AS doctor_mob,
        t6.nurse_id,
        t5.ward_name,
        t6.deleted,
        t4.instructions,
        t4.prescriptions,
        t2.admission_date,  
        t2.account_id AS visit_date_id, 
        (SELECT residence_name FROM tbl_residences t1 INNER JOIN tbl_patients t2 ON t2.residence_id=t1.id  GROUP BY t1.residence_id LIMIT 1) AS residence_name,
        
        (SELECT council_name 
        FROM tbl_residences t1 
        INNER JOIN tbl_patients t2 ON t2.residence_id=t1.id 
        INNER JOIN tbl_councils t3 ON t3.id=t1.council_id 
        GROUP BY t1.council_id LIMIT 1) AS council_name,
        
        t4.updated_at,      
        t4.created_at,      
        CONCAT(t1.first_name,' ',t1.middle_name,' ',t1.last_name) AS fullname,
        t2.facility_id 
        FROM tbl_admissions t2
        INNER JOIN tbl_instructions t4 ON t4.admission_id=t2.id
        INNER JOIN tbl_wards t5 ON t5.id =t4.ward_id
        INNER JOIN tbl_nurse_wards t6 ON t6.ward_id=t4.ward_id
        INNER JOIN tbl_patients t1 ON t1.id = t2.patient_id
        INNER JOIN users t9 ON t2.user_id=t9.id          
        WHERE t2.admission_status_id=1 AND t6.deleted=0);

CREATE OR REPLACE VIEW `vw_collectedSamples` AS (SELECT 
        t1.patient_id,
        t1.visit_date_id,
        t2.order_id,
        t2.order_status,
        t2.order_control,
        t2.id AS request_id,
        date(t2.created_at) as date_attended,
        t7.item_id,
        t1.visit_date_id as account_number,
        t7.first_name,
        t7.middle_name,
        t7.last_name,
        t7.gender,
        t7.dob,
        t7.age,
        t7.medical_record_number,
        t7.mobile_number,
        doctor.name AS doctor_name,
        (CASE WHEN t2.order_status=2 AND t2.result_control=1 THEN 'verified'
        WHEN t2.order_status=1 AND t2.order_validator_id IS NULL THEN 'Not Verified'    
        ELSE 'Waiting for Verification' END ) AS sample_status,
        doctor.mobile_number AS doctor_mobile_number,
        
        sample.created_at time_collected,
        
        sample_collector.name AS collected_by,
        t7.item_name,
        t2.sample_no,
        t2.priority,
        t7.sub_department_name,
        t7.sub_dept_id AS sub_department_id,
        t1.admission_id,
        CASE 
         WHEN t7.is_admitted THEN 'IPD'  ELSE 'OPD'  END as dept,        
        t2.created_at,
        t2.sample_types,
        t2.clinical_note,
        t2.created_at AS time_requested,
        t7.facility_id      
         FROM tbl_orders  t2
            INNER JOIN tbl_requests t1 ON t1.visit_date_id = t2.visit_date_id AND  DATE(t2.created_at) = DATE(t1.created_at) AND  timestampdiff(DAY,t1.created_at,now()) <= (select sum(tbl_lab_test_lives.days) from tbl_lab_test_lives) AND t2.sample_no is NOT NULL AND t1.id=t2.order_id
			INNER JOIN users doctor on t1.doctor_id = doctor.id
			INNER JOIN tbl_sample_number_controls sample on trim(leading '0' from sample.sample_no) = t2.sample_no
			INNER JOIN users sample_collector ON sample.user_id = sample_collector.id
			INNER JOIN tbl_encounter_invoices t8 ON t1.visit_date_id = t8.account_number_id
            INNER JOIN tbl_invoice_lines t7 ON t7.invoice_id = t8.id and t7.item_id = t2.test_id 
		);
 

CREATE OR REPLACE VIEW vw_perfomances AS(
      SELECT t1.id,t1.user_id,t1.created_at,t1.updated_at,t1.patient_id,t2.facility_id,t1.user_id AS doctor_id,t1.created_at
 AS time_treated,DATE(t1.created_at) AS date_clerked,t2.name,
       t2.name AS doctor_name,t3.prof_name
     FROM trackables t1 INNER JOIN users t2 ON t1.user_id=t2.id
	   INNER JOIN tbl_proffesionals t3 ON t3.id=t2.proffesionals_id group by patient_id,date_clerked,t1.user_id  );
	CREATE OR REPLACE  VIEW    `vw_prescriptions_dispensed` AS

        SELECT distinct tbl_prescriptions.id,
        tbl_prescriptions.updated_at as date,tbl_patients.id as patient_id,tbl_patients.medical_record_number,tbl_patients.first_name,
        tbl_patients.last_name,tbl_patients.middle_name,tbl_patients.dob,tbl_patients.gender,
          tbl_items.item_name, tbl_item_type_mappeds.item_category,tbl_prescriptions.item_id,
         tbl_item_type_mappeds.item_code,tbl_user_store_configurations.status as authority,
    tbl_prescriptions.frequency,tbl_prescriptions.quantity,tbl_prescriptions.duration,tbl_prescriptions.dose,tbl_prescriptions.start_date,
        tbl_prescriptions.instruction,tbl_prescriptions.dispensing_status,
         users.name as dispensed_by,tbl_prescriptions.dispenser_id,users.facility_id
        from    tbl_prescriptions left join tbl_patients on tbl_prescriptions.patient_id = tbl_patients.id
        inner join tbl_items on tbl_prescriptions.item_id = tbl_items.id
        inner join tbl_item_type_mappeds on tbl_items.id = tbl_item_type_mappeds.item_id
         join tbl_invoice_lines on tbl_item_type_mappeds.id = tbl_invoice_lines.item_type_id
          JOIN users ON tbl_prescriptions.dispenser_id = users.id
          JOIN tbl_user_store_configurations ON tbl_user_store_configurations.user_id = users.id

    where tbl_prescriptions.dispensing_status=1;   
	   
	   
CREATE OR REPLACE VIEW vw_bills_payments AS 
			(SELECT t1.id AS receipt_number,t2.status_id,t2.payment_filter,t2.updated_at,t3.onetime,t3.exemption_status,t3.insurance,t2.created_at,t7.first_name,t7.middle_name, t7.last_name,t7.medical_record_number,t1.facility_id, t2.patient_id, timestampdiff(year,t7.dob,t1.created_at) age, t7.gender, NULL as corpse_id, t2.id, t2.quantity, t2.discount, t3.price, t8.item_name, t2.sub_category_name, t2.main_category_id,t1.account_number_id as account_id
			FROM tbl_encounter_invoices t1 
			INNER JOIN tbl_invoice_lines t2 ON t2.status_id = 1 and t2.is_payable and t1.id = t2.invoice_id
			INNER JOIN tbl_reattendance_free_days t4 on t1.facility_id = t4.facility_id 
			AND (timestampdiff(day, t1.created_at, current_date) <= t4.days OR t2.is_admitted)
			INNER JOIN tbl_item_prices t3 ON t2.item_price_id = t3.id
			INNER JOIN tbl_items t8 ON t8.id = t3.item_id 
			INNER JOIN tbl_patients t7 on t7.id = t2.patient_id ORDER BY t1.id)
			
			UNION
			
			(SELECT t1.id AS receipt_number,t2.status_id,t2.payment_filter,t2.updated_at,t3.onetime,t3.exemption_status,t3.insurance,t2.created_at,t7.corpse_record_number as medical_record_number,t7.first_name,t7.middle_name, t7.last_name,t1.facility_id, NULL as patient_id, NULL age, NULL gender, t1.corpse_id, t2.id, t2.quantity, t2.discount, t3.price, t8.item_name, t2.sub_category_name, t2.main_category_id,t1.account_number_id as account_id
			FROM tbl_encounter_invoices t1 
			INNER JOIN tbl_invoice_lines t2 ON t2.status_id = 1 and t2.is_payable and t1.id = t2.invoice_id
			INNER JOIN tbl_reattendance_free_days t4 on t1.facility_id = t4.facility_id 
			AND (timestampdiff(day, t1.created_at, current_date) <= t4.days OR t2.is_admitted)
			INNER JOIN tbl_item_prices t3 ON t2.item_price_id = t3.id
			INNER JOIN tbl_items t8 ON t8.id = t3.item_id
			INNER JOIN tbl_corpses t7 on t7.id = t1.corpse_id ORDER BY t1.id);
			
CREATE OR REPLACE VIEW `vw_residences` AS ( select id AS residence_id, residence_name,  council_id  FROM tbl_residences);
 
 CREATE OR REPLACE VIEW `vw_investigation_results` AS (
        SELECT t1.item_id,t1.description, t1.attached_image,t1.panel,t1.remarks,t2.sample_no,t6.name,t6.mobile_number,t4.id AS account_id,
		t4.id AS visit_date_id,t4.patient_id,t4.facility_id, t7.first_name,t7.middle_name,t7.last_name,t7.residence_id,t7.medical_record_number,t7.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END AS age, (TIMESTAMPDIFF(YEAR,t7.dob, CURRENT_DATE)) AS umri,t7.gender,t4.date_attended AS visit_date,t4.main_category_id,t4.patient_category_id as bill_id,t1.confirmation_status,t5.item_name,t5.dept_id,t1.verify_user,t1.id AS resultsUuid,t2.id AS orderUuid,t1.created_at,t2.receiver_id,t3.id AS request_id, t3.doctor_id AS doctor_requested_test,t2.order_validator_id,t2.clinical_note,t4.sub_category_name,t4.date_attended 
		FROM tbl_results t1 
		INNER JOIN tbl_requests t3 ON t1.visit_date_id = t3.visit_date_id and t1.confirmation_status=1 AND t3.id = t1.order_id
		INNER JOIN tbl_orders t2 ON t2.visit_date_id = t3.visit_date_id AND  DATE(t2.created_at) = DATE(t3.created_at) AND t2.order_id = t3.id AND t2.test_id=t1.item_id
		INNER JOIN tbl_accounts_numbers t4 ON t4.id =t3.visit_date_id 
		INNER JOIN tbl_items t5 ON t5.id = t1.item_id 
		INNER JOIN users t6 ON t6.id = t1.verify_user 
		INNER JOIN tbl_patients t7 ON t7.id = t3.patient_id
		GROUP BY t1.id);

		
		
CREATE OR REPLACE VIEW `vw_patients_search` AS (SELECT distinct t1.id, t1.id AS patient_id,t1.updated_at,t1.dob,t1.residence_id,t3.residence_name,t1.gender,t1.first_name,t1.middle_name,t1.last_name,t1.medical_record_number,t1.mobile_number,CONCAT(t1.first_name,' ',ifnull(t1.middle_name,''),' ',ifnull(t1.last_name,''), ' ', t1.medical_record_number) AS fullname,t2.membership_number,t2.account_number, t2.facility_id,t4.days,case when t2.main_category_id = 1 and timestampdiff(day,t2.date_attended,current_date) <= t4.days then true else false end as qualifiesFreeReattendance  FROM tbl_patients t1 left join  tbl_accounts_numbers t2 on t1.id = t2.patient_id and (t2.id=(select max(id) from tbl_accounts_numbers t5 where t5.patient_id = t1.id and t5.main_category_id is not null and (t5.main_category_id <> 1 OR t5.paid_attendance)) or not exists(select id from tbl_accounts_numbers t6 where t6.main_category_id is not null and t6.patient_id = t2.patient_id)) join tbl_reattendance_free_days t4 on t1.facility_id = t4.facility_id left join tbl_residences t3 on t3.id = t1.residence_id );


	

CREATE or REPLACE VIEW  `vw_prescriptions` AS
	SELECT  tbl_prescriptions.id,
	tbl_prescriptions.out_of_stock,tbl_prescriptions.created_at,
	tbl_patients.id as patient_id,tbl_patients.medical_record_number,
	tbl_patients.first_name,tbl_patients.last_name,
	tbl_patients.middle_name,tbl_patients.dob,tbl_patients.gender,
	tbl_items.item_name,tbl_prescriptions.frequency,tbl_prescriptions.quantity,tbl_prescriptions.duration,tbl_prescriptions.dose,tbl_prescriptions.start_date,
	tbl_prescriptions.instruction,tbl_prescriptions.dispensing_status,
	tbl_prescriptions.item_id, tbl_prescriptions.visit_id,users.name,users.facility_id,
	tbl_item_type_mappeds.item_category,payment_filter,tbl_invoice_lines.main_category_id as pay_cat_id,
	0 as onetime,status_id as payment_status_id,
	concat(tbl_patients.first_name,' ',tbl_patients.middle_name, ' ',tbl_patients.last_name, ' #', tbl_patients.medical_record_number) search_name
	from  tbl_prescriptions 
	inner join tbl_patients on dispensing_status =2 and timestampdiff(day,tbl_prescriptions.created_at,CURDATE())<=2 and tbl_prescriptions.patient_id = tbl_patients.id
	inner join tbl_items on tbl_prescriptions.item_id = tbl_items.id
	inner join tbl_item_type_mappeds on tbl_items.id = tbl_item_type_mappeds.item_id
	inner join tbl_encounter_invoices on tbl_encounter_invoices.account_number_id = tbl_prescriptions.visit_id
	inner join tbl_invoice_lines on tbl_encounter_invoices.id = tbl_invoice_lines.invoice_id and (tbl_invoice_lines.status_id=2 or tbl_invoice_lines.is_payable IS NULL)
	inner join tbl_item_prices on tbl_item_prices.item_id = tbl_items.id and tbl_item_prices.id = tbl_invoice_lines.item_price_id
	INNER JOIN users ON tbl_prescriptions.prescriber_id = users.id
	GROUP BY tbl_prescriptions.patient_id, tbl_prescriptions.item_id
	ORDER BY tbl_prescriptions.id;
	
	
	CREATE OR REPLACE VIEW vw_patients_with_pending_labrequests AS
		SELECT 
			t1.id as order_id,
			t1.admission_id,
			t1.patient_id,
			date(t1.created_at) as date_attended,
			t2.sample_no,
			t2.test_id as item_id,
			t5.first_name,
			t5.middle_name,
			t5.last_name,
			t5.medical_record_number,
			t5.gender,
			t5.dob,
t2.priority,
			t5.mobile_number,CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END AS age,
			t5.facility_id
		FROM tbl_requests t1
			JOIN tbl_orders t2 ON t1.visit_date_id = t2.visit_date_id AND DATE(t2.created_at) = DATE(t1.created_at) AND  t2.sample_no IS NULL AND timestampdiff(day, t1.created_at, current_time) <= (SELECT sum(days) FROM `tbl_lab_test_lives`) and t1.id = t2.order_id
			JOIN tbl_item_type_mappeds t7 on t7.item_id = t2.test_id
			JOIN tbl_invoice_lines t5 ON t5.patient_id = t1.patient_id and t5.item_type_id = t7.id
            JOIN tbl_testspanels t8 ON t8.item_id = t2.test_id
		WHERE
			(t5.status_id= 2 OR t5.is_payable IS NULL)
		GROUP BY
			t1.id
		ORDER BY 
			t2.priority desc;
			
			
					
CREATE OR REPLACE VIEW vw_pending_labrequests AS
		SELECT 
			t2.id as request_id,
			t2.order_id,
			t2.test_id,
			t3.item_name,
			t9.id AS sub_department_id,
			t10.sub_department_name,
t2.priority,
			t12.name as doctor
		FROM tbl_requests t1
			JOIN tbl_orders t2 ON  t1.visit_date_id = t2.visit_date_id AND DATE(t2.created_at) = DATE(t1.created_at) AND  t2.sample_no IS NULL AND timestampdiff(day, t1.created_at, current_time) <= (SELECT sum(days) FROM `tbl_lab_test_lives`) AND t1.id = t2.order_id 
			JOIN tbl_items t3 ON t2.test_id = t3.id
            JOIN tbl_testspanels t8 ON t8.item_id = t2.test_id         
            JOIN tbl_equipments t9 ON t9.id = t8.equipment_id          
            JOIN tbl_sub_departments t10 ON t10.id = t9.sub_department_id
			JOIN users t12 ON t12.id = t1.doctor_id;
			
CREATE OR REPLACE VIEW `vw_shop_items` AS ( SELECT
                    t1.id AS item_id,
                    t2.id AS item_type_id,
                    t1.item_name,
                    t1.status,
                    t1.dept_id,
                    t2.item_category,
                    t2.dose_formulation,
                    t2.strength,
                    t2.dispensing_unit,
                    t2.sub_item_category,
                    t3.exemption_status,
                    t3.onetime,
                    t3.insurance,
                    t3.id AS price_id,
                    t3.price,
                    t3.facility_id,
					t3.sub_category_id,
					t3.sub_category_id patient_category_id,
					t4.pay_cat_id as patient_main_category_id,
					t4.sub_category_name,
					t4.sub_category_name as patient_category
                    FROM  tbl_items t1 
					join tbl_item_type_mappeds t2 on t1.id = t2.item_id
                    join tbl_item_prices t3 on t3.status = 1 and t1.id = t3.item_id
                    join tbl_pay_cat_sub_categories t4 on t4.id = t3.sub_category_id);

CREATE OR REPLACE VIEW `vw_registrar_services` AS( SELECT
                    t1.service_id,
                    t1.facility_id,       
                    t2.item_name,
                    t2.status,
                    t2.patient_category,
                    t2.patient_category_id,
                    t2.patient_main_category_id,
                    t2.price_id,
                    t2.item_type_id,
                    t2.price 
                    FROM  tbl_registrar_services t1,
                    vw_shop_items t2            
                    WHERE 
                    t1.service_id = t2.item_id       
                    );


CREATE OR REPLACE VIEW  `vw_shop_item_balance` AS
        SELECT distinct
        tbl_dispensers.item_id,tbl_items.item_name,


        tbl_payments_categories.category_description,
        tbl_payments_categories.id as main_category_id,
        tbl_pay_cat_sub_categories.id as pay_cat_id,

         tbl_store_lists.facility_id,

          sum(quantity_received) as balance
        FROM tbl_dispensers INNER JOIN tbl_items ON tbl_dispensers.item_id = tbl_items.id
        INNER JOIN tbl_store_lists ON tbl_dispensers.dispenser_id = tbl_store_lists.id
        INNER JOIN tbl_item_prices ON tbl_dispensers.item_id = tbl_item_prices.item_id
        INNER JOIN tbl_pay_cat_sub_categories ON tbl_pay_cat_sub_categories.id = tbl_item_prices.sub_category_id
        INNER JOIN tbl_payments_categories ON tbl_payments_categories.id = tbl_pay_cat_sub_categories.pay_cat_id
           where control='l' and tbl_store_lists.id=100 and tbl_pay_cat_sub_categories.id=10 GROUP BY tbl_items.id;


CREATE OR REPLACE  VIEW    `vw_prescriptions_dispensed` AS

        SELECT distinct tbl_prescriptions.id,
        tbl_prescriptions.updated_at as date,tbl_patients.id as patient_id,tbl_patients.medical_record_number,tbl_patients.first_name,
        tbl_patients.last_name,tbl_patients.middle_name,tbl_patients.dob,tbl_patients.gender,
          tbl_items.item_name, tbl_item_type_mappeds.item_category,tbl_prescriptions.item_id,
         tbl_item_type_mappeds.item_code,tbl_user_store_configurations.status as authority,
    tbl_prescriptions.frequency,tbl_prescriptions.quantity,tbl_prescriptions.duration,tbl_prescriptions.dose,tbl_prescriptions.start_date,
        tbl_prescriptions.instruction,tbl_prescriptions.dispensing_status,
         users.name as dispensed_by,tbl_prescriptions.dispenser_id,users.facility_id
        from    tbl_prescriptions inner join tbl_patients on tbl_prescriptions.patient_id = tbl_patients.id
        inner join tbl_items on tbl_prescriptions.item_id = tbl_items.id
        inner join tbl_item_type_mappeds on tbl_items.id = tbl_item_type_mappeds.item_id
        inner join tbl_invoice_lines on tbl_item_type_mappeds.id = tbl_invoice_lines.item_type_id
        INNER JOIN users ON tbl_prescriptions.dispenser_id = users.id
        INNER JOIN tbl_user_store_configurations ON tbl_user_store_configurations.user_id = users.id

    where tbl_prescriptions.dispensing_status=1;

CREATE OR REPLACE VIEW `vw_treatment_charts` AS (
SELECT t4.start_date AS date_dosage,
       t3.item_name,
       t4.dose,
       t1.created_at AS time_given,
       t1.timedosage AS time_recorded,
       t1.remarks,
       t2.id AS admission_id,
       (SELECT name from users t6 WHERE t1.user_id=t6.id GROUP BY t6.id ) AS nurse_name
       FROM tbl_ipdtreatments t1
       INNER JOIN tbl_admissions t2 ON t1.admission_id=t2.id
       INNER JOIN tbl_items t3 ON t3.id=t1.item_id
       INNER JOIN tbl_prescriptions  t4 ON t4.item_id=t3.id  group by t1.id);

	   

CREATE OR REPLACE VIEW  `vw_dispensing_item_balance` AS
        SELECT distinct
        tbl_items.item_name,
        tbl_dispensers.item_id,
        tbl_dispensers.dispenser_id, 
        tbl_payments_categories.category_description,
        tbl_payments_categories.id as main_category_id,
         tbl_store_lists.facility_id,
          sum(quantity_received) as balance
        FROM tbl_dispensers INNER JOIN tbl_items ON tbl_dispensers.item_id = tbl_items.id
        INNER JOIN tbl_store_lists ON tbl_dispensers.dispenser_id = tbl_store_lists.id
        INNER JOIN tbl_pos_dispensings ON tbl_pos_dispensings.store_id = tbl_store_lists.id
        INNER JOIN tbl_item_prices ON tbl_dispensers.item_id = tbl_item_prices.item_id
        INNER JOIN tbl_pay_cat_sub_categories ON tbl_pay_cat_sub_categories.id = tbl_item_prices.sub_category_id
        INNER JOIN tbl_payments_categories ON tbl_payments_categories.id = tbl_pay_cat_sub_categories.pay_cat_id
           where control='l' AND tbl_pos_dispensings.status=1 
           group by item_id, tbl_payments_categories.id, tbl_payments_categories.category_description,  tbl_store_lists.facility_id,tbl_payments_categories.id

           ;



CREATE OR REPLACE VIEW `vw_treatment_charts` AS (
SELECT t4.start_date AS date_dosage,
       t3.item_name,
       t4.dose,
       t1.created_at AS time_given,
       t1.timedosage AS time_recorded,
       t1.remarks,
       t2.id AS admission_id,
       (SELECT name from users t6 WHERE t1.user_id=t6.id GROUP BY t6.id ) AS nurse_name
       FROM tbl_ipdtreatments t1
       INNER JOIN tbl_admissions t2 ON t1.admission_id=t2.id
       INNER JOIN tbl_items t3 ON t3.id=t1.item_id
       INNER JOIN tbl_prescriptions  t4 ON t4.item_id=t3.id  group by t1.id);
	   
	   
CREATE OR REPLACE VIEW  `vw_tatReports` AS(    
        SELECT 
        DATE(request.created_at) AS date,
		item.item_name as test_done,
		patient.medical_record_number,
		sample.created_at AS login,
		`result`.updated_at as logout,
		CONCAT(TIMESTAMPDIFF(MINUTE, sample.created_at, result.updated_at),'  Minute(s)') as tat
		,`order`.sample_no
        FROM tbl_results `result` 
        INNER JOIN tbl_requests request ON `result`.visit_date_id = request.visit_date_id and `result`.confirmation_status = 1 AND request.id = result.order_id
        INNER JOIN tbl_orders `order` ON `order`.visit_date_id = request.visit_date_id AND  DATE(request.created_at) = DATE(`order`.created_at) AND  `order`.order_id = request.id AND `result`.item_id = `order`.test_id
        INNER JOIN tbl_items item ON item.dept_id = 2 AND item.id = `order`.test_id
        INNER JOIN tbl_patients patient ON patient.id = request.patient_id
		JOIN tbl_sample_number_controls sample on sample.sample_no = lpad(`order`.sample_no,10,'0')
		GROUP BY patient.id,`order`.test_id, `order`.order_id);
		
 	


	   
	   
CREATE OR REPLACE VIEW  `vw_getSampleReports` AS(    
        SELECT t1.*,t2.sample_no,t4.item_name,t4.dept_id,
        CONCAT(t5.first_name,' ',t5.middle_name,' ',t5.last_name) AS full_name,
        t5.mobile_number,
        t5.gender,
        t2.sample_types,
        t2.clinical_note,
        DATE(t3.created_at) AS date_requested,
        t5.medical_record_number,
        (SELECT name FROM users t10 WHERE t10.id=t1.post_user GROUP BY t10.id) AS posted_by,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob,
		CURRENT_DATE), ' Days') END END AS age
         
         
        FROM tbl_results t1 
        INNER JOIN tbl_requests t3 ON t3.id = t1.order_id AND t1.visit_date_id = t3.visit_date_id
        INNER JOIN tbl_orders t2 ON t3.visit_date_id = t2.visit_date_id AND  DATE(t2.created_at) = DATE(t3.created_at) AND  t1.item_id = t2.test_id
        INNER JOIN tbl_items t4 ON t4.id = t1.item_id
        INNER JOIN tbl_patients t5 ON t5.id = t3.patient_id
        WHERE t4.dept_id = 2
           AND t2.order_id=t3.id 
          group by t1.item_id,t1.order_id);
		  
		  
CREATE OR REPLACE VIEW  `vw_dispensing_item_balance` AS
        SELECT distinct
        tbl_items.item_name,
        tbl_dispensers.item_id,
        tbl_dispensers.dispenser_id, 
        tbl_payments_categories.category_description,
        tbl_payments_categories.id as main_category_id,
         tbl_store_lists.facility_id,
          sum(quantity_received) as balance
        FROM tbl_dispensers INNER JOIN tbl_items ON tbl_dispensers.item_id = tbl_items.id
        INNER JOIN tbl_store_lists ON tbl_dispensers.dispenser_id = tbl_store_lists.id
        INNER JOIN tbl_pos_dispensings ON tbl_pos_dispensings.store_id = tbl_store_lists.id
        INNER JOIN tbl_item_prices ON tbl_dispensers.item_id = tbl_item_prices.item_id
        INNER JOIN tbl_pay_cat_sub_categories ON tbl_pay_cat_sub_categories.id = tbl_item_prices.sub_category_id
        INNER JOIN tbl_payments_categories ON tbl_payments_categories.id = tbl_pay_cat_sub_categories.pay_cat_id
           where control='l' AND tbl_pos_dispensings.status=1 
           group by item_id, tbl_payments_categories.id, tbl_payments_categories.category_description,  tbl_store_lists.facility_id,tbl_payments_categories.id

           ;

CREATE OR REPLACE VIEW `vw_exemption_service_summary` AS SELECT t1.id AS item_refference,
                    t1.invoice_id AS receipt_number,                    
                    t1.invoice_id,
                    t1.patient_id,
                    t1.medical_record_number,
                    t1.dob,
                    t1.gender,
                    t1.first_name,
                    t1.middle_name,
                    t1.last_name,
					t1.mobile_number,                   
                    t1.quantity, 
                    t1.discount, 
                    t1.status_id, 
                    t1.facility_id,
                    t1.item_type_id,
                    t1.discount_by,
                    t1.payment_filter,
                    t1.payment_filter as pay_cat_id,
                    t1.gepg_receipt,
                    t1.payment_method_id,
                    t1.sub_category_name,
                    t1.category_description,
                    t1.main_category_id,
                    t1.created_at,
                    t1.price,
                    t1.price AS unit_price,
                    t1.item_name, 
                    t1.department_name, 
                    t1.dept_id, 
                    t1.item_category
                     
                    FROM tbl_invoice_lines t1
 
 where t1.main_category_id=3 AND t1.is_payable is null      group by t1.id ;  

CREATE OR REPLACE VIEW `vw_previous_medications` AS (
        SELECT
        tbl_items.id AS item_id,
        tbl_items.item_name,
        tbl_prescriptions.patient_id,
        tbl_prescriptions.id as prescription_id,
        tbl_prescriptions.quantity,
        tbl_prescriptions.frequency,
        tbl_prescriptions.duration,
tbl_prescriptions.continuation_status,
        tbl_prescriptions.dose,
        tbl_accounts_numbers.date_attended,
        tbl_prescriptions.visit_id,
     tbl_prescriptions.conservatives,
        tbl_prescriptions.out_of_stock,
        (timestampdiff(DAY,tbl_prescriptions.start_date,CURRENT_TIMESTAMP)) AS days,
        tbl_prescriptions.start_date,
        tbl_prescriptions.instruction,
        users.name,
        tbl_proffesionals.prof_name
         FROM tbl_prescriptions 
         INNER JOIN  tbl_items ON tbl_items.id = tbl_prescriptions.item_id
         INNER JOIN  tbl_accounts_numbers ON tbl_accounts_numbers.id = tbl_prescriptions.visit_id
         INNER JOIN users ON users.id = tbl_prescriptions.prescriber_id
         INNER JOIN tbl_proffesionals ON tbl_proffesionals.id = users.proffesionals_id
         );
		 
		 
CREATE OR REPLACE VIEW `vw_ipd_patients` AS (SELECT 
        t2.id AS patient_id,
        t2.first_name,
        t2.middle_name,
        t2.last_name,
        t2.residence_id,
        t2.medical_record_number,
        t2.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END
        AS age,(TIMESTAMPDIFF(YEAR,t2.dob, CURRENT_DATE)) AS umri,
        t2.gender,
        t3.account_number,
        t3.id AS account_id,
        t3.date_attended AS visit_date,
        t1.id AS admission_id,
        t1.admission_status_id,
        t4.main_category_id,
        t4.bill_id, 
        t5.sub_category_name,
        t6.ward_id,
        t1.facility_id,
		t3.card_no
        FROM 
        tbl_admissions t1 INNER JOIN tbl_patients t2 ON t1.patient_id = t2.id 
        INNER JOIN tbl_accounts_numbers t3 ON t3.visit_close = 1 and t1.account_id = t3.id
        INNER JOIN tbl_bills_categories t4 ON t4.account_id = t3.id        
        INNER JOIN tbl_pay_cat_sub_categories t5 ON t4.bill_id = t5.id 
        INNER JOIN tbl_instructions t6 ON t1.id = t6.admission_id
        WHERE t1.admission_status_id = 2
        );
		
		
		
		
alter table tbl_invoice_lines ADD COLUMN IF NOT EXISTS cancelling_reason TEXT NULL;		
alter table tbl_cash_deposits ADD COLUMN IF NOT EXISTS cancelling_reason TEXT NULL;		
CREATE OR REPLACE VIEW `vw_cancelled_bills` AS (SELECT tbl_invoice_lines.facility_id, CONCAT(first_name,' ',ifnull(middle_name,''),' ',ifnull(last_name,''), ' ', medical_record_number) AS customer, quantity, (quantity*price)-discount as amount, item_name, users.name as `user`, date(tbl_invoice_lines.updated_at) as `date`, cancelling_reason  FROM tbl_invoice_lines JOIN users ON tbl_invoice_lines.user_id = users.id AND tbl_invoice_lines.status_id  =3 ORDER BY tbl_invoice_lines.updated_at);

CREATE OR REPLACE VIEW `vw_special_clinics_clients` AS (
        SELECT
        tbl_patients.first_name,
        tbl_patients.middle_name,
        tbl_patients.last_name,
        tbl_patients.medical_record_number,
        tbl_patients.gender,
	tbl_patients.residence_id,
         tbl_patients.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,tbl_patients.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,tbl_patients.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,tbl_patients.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, tbl_patients.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, tbl_patients.dob, CURRENT_DATE), ' Days') END END
AS age,
        tbl_accounts_numbers.account_number,
        tbl_accounts_numbers.patient_id,
        tbl_accounts_numbers.id AS account_id,
        tbl_accounts_numbers.date_attended AS visit_date,
        tbl_bills_categories.main_category_id,
        tbl_bills_categories.bill_id,
        tbl_accounts_numbers.facility_id,
        tbl_clinic_instructions.id AS transfer_id,
        tbl_clinic_instructions.received,
        tbl_clinic_instructions.visit_id,
         tbl_invoice_lines.status_id as pay_status,
 	tbl_invoice_lines.payment_filter,
        tbl_clinic_instructions.dept_id,
	tbl_pay_cat_sub_categories.sub_category_name,
        tbl_clinic_instructions.summary
       FROM
        tbl_patients INNER JOIN tbl_accounts_numbers ON tbl_patients.id=tbl_accounts_numbers.patient_id
        INNER JOIN tbl_invoice_lines  ON tbl_invoice_lines.patient_id = tbl_patients.id
        INNER JOIN tbl_bills_categories ON tbl_accounts_numbers.id=tbl_bills_categories.account_id
        INNER JOIN tbl_clinic_instructions ON tbl_accounts_numbers.id = tbl_clinic_instructions.visit_id
	INNER JOIN tbl_pay_cat_sub_categories ON tbl_pay_cat_sub_categories.id = tbl_bills_categories.bill_id
        );
		
		
CREATE OR REPLACE VIEW `Vw_xray_orders` AS
SELECT 
        t1.patient_id,
        t1.visit_date_id, 
        t2.id AS request_id,
        t2.order_status,
        date(t2.created_at) as date_attended,
        t7.item_id,
        t8.account_number_id as account_number,
        t7.first_name,
        t7.middle_name,
        t7.last_name,
        t7.gender,
        t7.dob,
        t19.name as doctor_name,
        t7.age,
        t7.medical_record_number,
        t7.mobile_number,
        t7.payment_filter,
        t7.status_id AS payment_status,
        t7.item_name,
        t7.sub_department_name,
        null as onetime,
        t7.sub_dept_id AS sub_department_id,
        t2.priority,
        t2.clinical_note,
        t1.admission_id,
        t1.id AS OrderId,
        CASE 
         WHEN t7.is_admitted THEN 'IPD'  ELSE 'OPD'  END as dept,        
        t2.created_at,
        t7.facility_id
        
         FROM tbl_orders  t2
            INNER JOIN tbl_requests t1 ON t1.visit_date_id = t2.visit_date_id AND date(t2.created_at)=date(t1.created_at) and t1.id=t2.order_id
            INNER JOIN tbl_encounter_invoices t8 ON t1.visit_date_id = t8.account_number_id
            INNER JOIN tbl_invoice_lines t7 ON t7.dept_id = 3 and (t7.status_id = 2 OR (t7.is_payable AND t7.status_id = 1 AND t7.payment_filter = 3) or t7.is_payable is not true) and t7.invoice_id = t8.id and t7.item_id = t2.test_id
            INNER JOIN users t19 ON t19.id = t1.doctor_id 
           WHERE timestampdiff(DAY,t2.created_at,now()) <= (select sum(tbl_lab_test_lives.days) from tbl_lab_test_lives);