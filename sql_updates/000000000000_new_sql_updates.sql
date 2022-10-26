alter table tbl_accounts_numbers add column if not exists paid_attendance bool not null default false;
ALTER table  tbl_results change description description text null;
alter table Tbl_panel_components_results add column if not exists visit_date_id int after order_id;


set foreign_key_checks=0;
truncate tbl_dispensed_group_controls;
INSERT IGNORE INTO `tbl_dispensed_group_controls` (`id`, `code`, `item_name`, `created_at`, `updated_at`) VALUES 
(1, '001', 'ALU ya 1x6', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), 
(2, '002', 'ALU ya 2x6', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), 
(3, '003', 'ALU ya 3x6', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), 
(4, '004', 'ALU ya 4x6', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), 
(5, '005', 'Co-trimoxazile ya maji', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), 
(6, '006', 'Amoxycilin DT(250mg) x 10', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (7, '007', 'Amoxycilin DT(250mg) x 5', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (8, '008', 'ORS', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (9, '009', 'Zinc Sulphate', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (10, '010', 'Mebendazole Tabs 100mg', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (11, '011', 'Mebendazole Tabs 500mg', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (12, '012', 'Albendazole Tabs 200mg', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (13, '013', 'Albendazole Tabs 400mg', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (14, '014', 'FEFO Tabs', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (15, '015', 'Folic Acid Tabs', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (16, '016', 'TLE Tabs', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (17, '017', 'Oxytocin Inj', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (18, '018', 'Deprovera Inj', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (19, '019', 'SP Tabs', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (20, '020', 'Magnesium Sulphate Inj', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
, (21, '021', 'RHZ Rifampicin 150mg/isoniazide 75mg/pyrampicin 150mg/isoniazide Tabs', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
set foreign_key_checks=1;
 
alter table tbl_items add column if not exists msd_product boolean default false after dept_id;

CREATE OR REPLACE VIEW `vw_previous_medications` AS (
        SELECT
        tbl_items.id AS item_id,
        tbl_items.item_name,
        tbl_prescriptions.patient_id,
        tbl_prescriptions.id as prescription_id,
        tbl_prescriptions.quantity,
        tbl_prescriptions.continuation_status,
        tbl_prescriptions.stoped_by,
        tbl_prescriptions.frequency,
        tbl_prescriptions.duration,
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
CREATE OR REPLACE VIEW `vw_continuation_remarks` AS (
        
	SELECT  t2.patient_id,
		t1.medical_record_number,
		t1.mobile_number,
        t2.admission_status_id,
		t4.admission_id,
		t4.ward_id,
		t6.nurse_id,
		t6.deleted,
		t11.id,
		t4.bed_id
		,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END
AS age,
		t5.ward_name,
		t4.instructions,
		t4.prescriptions,
		t2.admission_date,	
		(SELECT residence_name FROM tbl_residences t1 INNER JOIN tbl_patients t2 ON t2.residence_id=t1.id  GROUP BY t1.residence_id LIMIT 1) AS residence_name,
		
		(SELECT council_name 
		FROM tbl_residences t1 
		INNER JOIN tbl_patients t2 ON t2.residence_id=t1.id 
		INNER JOIN tbl_councils t3 ON t3.id=t1.council_id 
		GROUP BY t1.council_id LIMIT 1) AS council_name,
        t10.bed_name,
        t9.name,
        t2.account_id AS visit_date_id,
			
	CASE 
	WHEN t2.account_id IS NOT NULL THEN (SELECT t12.main_category_id FROM tbl_bills_categories t12 WHERE t12.account_id=t2.account_id GROUP BY t2.account_id 
	LIMIT 1) END AS main_category_id,		
	
	CASE 
	WHEN t2.account_id IS NOT NULL THEN (SELECT t12.bill_id FROM tbl_bills_categories t12 WHERE t12.account_id=t2.account_id GROUP BY t2.account_id 
	 LIMIT 1) END AS patient_category_id,
	
	CASE 
	WHEN t2.account_id IS NOT NULL THEN (SELECT t12.date_attended FROM tbl_accounts_numbers t12 WHERE t12.id=t2.account_id LIMIT 1
	) END AS date_attended,
	
        t1.gender,
		t9.mobile_number AS doctor_mob,		
        t4.updated_at,		
        t11.notes,		
        t11.notes_type,	
       (SELECT name FROM users t12 WHERE t11.user_id=t12.id GROUP BY t2.account_id LIMIT 1) AS doctor_rounded,		
        t4.created_at,		
        t11.created_at AS time_written,		
		CONCAT(t1.first_name,' ',t1.middle_name,' ',t1.last_name) AS fullname,
		t2.facility_id 
		
		
        FROM tbl_admissions t2
        INNER JOIN tbl_instructions t4 ON t4.admission_id=t2.id
        INNER JOIN tbl_continuation_notes t11 ON t11.visit_id=t2.account_id
        INNER JOIN tbl_beds t10 ON t10.id=t4.bed_id
        INNER JOIN tbl_wards t5 ON t5.id =t4.ward_id
        INNER JOIN tbl_nurse_wards t6 ON t6.ward_id=t4.ward_id
        INNER JOIN tbl_patients t1 ON t1.id = t2.patient_id
        INNER JOIN users t9 ON t2.user_id=t9.id  
        WHERE t2.admission_status_id=2 AND t6.deleted=0
        );

CREATE OR REPLACE VIEW `vw_exemp_sub_department_summary` AS (SELECT
        tbl_invoice_lines.id,sub_category_name, tbl_sub_departments.sub_department_name,item_name,facility_id,tbl_invoice_lines.created_at,
         ((price * quantity)-discount) AS resultant_pay,
		 count(*) as idadi
         FROM 
         tbl_invoice_lines join tbl_sub_departments on tbl_invoice_lines.dept_id=tbl_sub_departments.department_id
		 WHERE main_category_id =3
		 GROUP BY  id);
 CREATE OR REPLACE VIEW `vw_exemp_sub_department_summary` AS (SELECT
        tbl_invoice_lines.id,sub_category_name, tbl_sub_departments.sub_department_name,item_name,facility_id,tbl_invoice_lines.created_at,
         ((price * quantity)-discount) AS resultant_pay,
		 count(*) as idadi
         FROM 
         tbl_invoice_lines join tbl_sub_departments on tbl_invoice_lines.dept_id=tbl_sub_departments.department_id
		 WHERE main_category_id =3
		 GROUP BY  id);

CREATE OR REPLACE VIEW  `vw_results_get_approvedData` AS(    
        SELECT t1.*,t2.sample_no,t4.item_name,t4.dept_id,
        CONCAT(t5.first_name,' ',t5.middle_name,' ',t5.last_name) AS full_name,
        t5.mobile_number,
        t2.sample_types,
        t2.clinical_note,
        DATE(t3.created_at) AS date_requested,
        t5.medical_record_number,
        (SELECT name FROM users t10 WHERE t10.id=t1.post_user GROUP BY t10.id) AS posted_by,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END AS age,
         CASE 
         WHEN t5.residence_id IS NOT NULL THEN (SELECT CONCAT(residence_name,' ',council_name) FROM tbl_residences t6 INNER JOIN tbl_councils t7 ON t6.council_id=t7.id WHERE t6.id=t5.residence_id GROUP BY t6.id LIMIT 1) END AS residence_name,       
         CASE 
         WHEN t3.doctor_id IS NOT NULL THEN (SELECT name FROM users t8  WHERE t8.id=t3.doctor_id GROUP BY t8.name LIMIT 1) END AS doctor_name,
         CASE 
         WHEN t3.doctor_id IS NOT NULL THEN (SELECT t8.mobile_number  FROM users t8  WHERE t8.id=t3.doctor_id GROUP BY t8.mobile_number LIMIT 1) END AS doctor_mobile_number    ,
         CASE 
         WHEN t3.requesting_department_id IS NOT NULL THEN (SELECT department_name  FROM tbl_departments t9  WHERE t3.requesting_department_id=t9.id GROUP BY t9.department_name LIMIT 1) END AS requesting_department  ,
         CASE 
         WHEN t2.sample_no IS NOT NULL THEN (SELECT DATE(t10.created_at)  FROM tbl_sample_number_controls t10  
         WHERE  TRIM(LEADING '0' FROM t10.sample_no)=t2.sample_no GROUP BY t10.created_at LIMIT 1) END AS date_collected,
         CASE 
         WHEN t2.sample_no IS NOT NULL THEN (SELECT TIME(t10.created_at)  FROM tbl_sample_number_controls t10 
         
         
         WHERE  TRIM(LEADING '0' FROM t10.sample_no)=t2.sample_no GROUP BY t10.created_at LIMIT 1) END AS time_collected     ,
         CASE 
         WHEN t2.sample_no IS NOT NULL THEN (SELECT name  FROM users t11
         INNER JOIN  tbl_sample_number_controls t10  ON t10.user_id=t11.id
         WHERE t2.receiver_id=t10.user_id GROUP BY t2.receiver_id LIMIT 1) END AS collected_by
         ,(select name from users where id = t1.verify_user) as approver
		 ,t1.updated_at as result_time,
		 case when t1.verify_user is not null then true else false end as approved
         
        FROM tbl_results t1 
        INNER JOIN tbl_requests t3 ON  t3.visit_date_id = t1.visit_date_id and t3.id = t1.order_id
        INNER JOIN tbl_orders t2 ON t1.item_id = t2.test_id and  DATE(t2.created_at) = DATE(t3.created_at) AND  t3.visit_date_id = t2.visit_date_id 
        INNER JOIN tbl_items t4 ON t4.id = t1.item_id
        INNER JOIN tbl_patients t5 ON t5.id = t3.patient_id
        WHERE t4.dept_id = 2
          AND t1.confirmation_status =1 
          AND t2.order_id=t3.id 
          group by t1.item_id,t1.order_id);

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
        INNER JOIN tbl_requests request ON  `result`.visit_date_id = `request`.visit_date_id and `result`.confirmation_status = 1 AND request.id = result.order_id
        INNER JOIN tbl_orders `order` ON `order`.order_id = request.id AND `result`.item_id = `order`.test_id and  DATE(request.created_at) = DATE(`order`.created_at) AND  `result`.visit_date_id = `order`.visit_date_id 
        INNER JOIN tbl_items item ON item.dept_id = 2 AND item.id = `order`.test_id
        INNER JOIN tbl_patients patient ON patient.id = request.patient_id
		JOIN tbl_sample_number_controls sample on sample.sample_no = lpad(`order`.sample_no,10,'0')
		GROUP BY patient.id,`order`.test_id, `order`.order_id);
		
	
 CREATE OR REPLACE VIEW vw_results_get_approves 
AS (
select result.*,
 orderedtest.sample_no,
 item.item_name,
 item.dept_id,
 concat(patient.first_name,' ',patient.middle_name,' ',patient.last_name) AS full_name,
 NULL AS mobile_number,
 patient.medical_record_number,
 patient.dob,
 (case when (timestampdiff(YEAR,patient.dob,curdate()) <> 0) then concat(timestampdiff(YEAR,patient.dob,curdate()),' Years') else (case when (timestampdiff(MONTH,patient.dob,curdate()) <> 0) then concat(timestampdiff(MONTH,patient.dob,curdate()),' Months') else concat(timestampdiff(DAY,patient.dob,curdate()),' Days') end) end) AS age,
 NULL AS residence_name,
 orderedtest.sample_types,
 orderedtest.clinical_note,
 cast(request.created_at as date) AS date_requested,
 entry.name AS posted_by,
 doctor.name AS doctor_name,
 doctor.mobile_number AS doctor_mobile_number,
 NULL AS requesting_department,
 cast(sample.created_at as date) AS date_collected,
 cast(result.created_at as time) AS time_collected,
 collector.name AS collected_by,
 verifier.name AS approver,
 result.updated_at AS result_time,
 (case when isnull(result.verify_user) then 0 else 1 end) AS approved,
 orderedtest.priority
 from tbl_results result 
 join tbl_requests request on  result.visit_date_id = request.visit_date_id and isnull(result.confirmation_status) and timestampdiff(DAY,result.created_at,now()) <= (select sum(tbl_lab_test_lives.days) from tbl_lab_test_lives) and result.order_id = request.id 
 join tbl_orders orderedtest on orderedtest.order_id = request.id and DATE(request.created_at) = DATE(orderedtest.created_at) AND  orderedtest.test_id = result.item_id and  orderedtest.visit_date_id = request.visit_date_id
 join tbl_items item on item.dept_id = 2 and item.id = orderedtest.test_id 
 join tbl_sample_number_controls sample on trim(leading '0' from sample.sample_no) = orderedtest.sample_no
 join tbl_patients patient on patient.id = request.patient_id 
 join users entry on result.post_user = entry.id
 join users doctor on request.doctor_id = doctor.id
 join users collector on sample.user_id = collector.id
 left join users verifier on verifier.id =result.verify_user
 order by orderedtest.priority desc);

CREATE OR REPLACE VIEW `vw_previous_medications` AS (
        SELECT
        tbl_items.id AS item_id,
        tbl_items.item_name,
        tbl_prescriptions.patient_id,
        tbl_prescriptions.id as prescription_id,
        tbl_prescriptions.quantity,
tbl_prescriptions.continuation_status,
        tbl_prescriptions.frequency,
        tbl_prescriptions.duration,
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

CREATE OR REPLACE VIEW `vw_prescribed_items` AS (SELECT t2.id AS admission_id,t7.bed_name,t8.ward_name,t6.item_name,t1.patient_id,t2.admission_date,
                t1.item_id,t4.medical_record_number,CONCAT(t4.first_name,' ',t4.middle_name,' ',t4.last_name) AS patient_name,t5.bed_id,t5.ward_id,
                t1.continuation_status,
		(select name from users where id = stoped_by) as stoped_by,
                t2.facility_id ,t3.name AS dr_ordered,t1.quantity,t1.frequency,t1.duration,t1.dose,t1.start_date,t1.instruction
          FROM `tbl_prescriptions` t1
          INNER JOIN  `tbl_admissions` t2 ON t1.visit_id=t2.account_id
          INNER JOIN  `users` t3 ON t1.prescriber_id=t3.id 
          INNER JOIN  `tbl_patients` t4 ON t1.patient_id=t4.id 
          INNER JOIN  `tbl_instructions` t5 ON t2.id=t5.admission_id 
          INNER JOIN  `tbl_items` t6 ON t6.id=t1.item_id 
          INNER JOIN  `tbl_beds` t7 ON t7.id=t5.bed_id 
          INNER JOIN  `tbl_wards` t8 ON t7.ward_id=t8.id 
          WHERE t1.dispensing_status IS NOT NULL);
CREATE OR REPLACE VIEW `vw_continuation_remarks` AS (
        
	SELECT  t2.patient_id,
		t1.medical_record_number,
		t1.mobile_number,
        t2.admission_status_id,
		t4.admission_id,
		t4.ward_id,
		t6.nurse_id,
		t6.deleted,
		t11.id,
		t4.bed_id
		,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END
AS age,
		t5.ward_name,
		t4.instructions,
		t4.prescriptions,
		t2.admission_date,	
		(SELECT residence_name FROM tbl_residences t1 INNER JOIN tbl_patients t2 ON t2.residence_id=t1.id  GROUP BY t1.residence_id LIMIT 1) AS residence_name,
		
		(SELECT council_name 
		FROM tbl_residences t1 
		INNER JOIN tbl_patients t2 ON t2.residence_id=t1.id 
		INNER JOIN tbl_councils t3 ON t3.id=t1.council_id 
		GROUP BY t1.council_id LIMIT 1) AS council_name,
        t10.bed_name,
        t9.name,
        t2.account_id AS visit_date_id,
			
	CASE 
	WHEN t2.account_id IS NOT NULL THEN (SELECT t12.main_category_id FROM tbl_bills_categories t12 WHERE t12.account_id=t2.account_id GROUP BY t2.account_id 
	LIMIT 1) END AS main_category_id,		
	
	CASE 
	WHEN t2.account_id IS NOT NULL THEN (SELECT t12.bill_id FROM tbl_bills_categories t12 WHERE t12.account_id=t2.account_id GROUP BY t2.account_id 
	 LIMIT 1) END AS patient_category_id,
	
	CASE 
	WHEN t2.account_id IS NOT NULL THEN (SELECT t12.date_attended FROM tbl_accounts_numbers t12 WHERE t12.id=t2.account_id LIMIT 1
	) END AS date_attended,
	
        t1.gender,
		t9.mobile_number AS doctor_mob,		
        t4.updated_at,		
        t11.notes,		
        t11.notes_type,	
       (SELECT name FROM users t12 WHERE t11.user_id=t12.id GROUP BY t2.account_id LIMIT 1) AS doctor_rounded,		
        t4.created_at,		
        t11.created_at AS time_written,		
		CONCAT(t1.first_name,' ',t1.middle_name,' ',t1.last_name) AS fullname,
		t2.facility_id 
		
		
        FROM tbl_admissions t2
        INNER JOIN tbl_instructions t4 ON t4.admission_id=t2.id
        INNER JOIN tbl_continuation_notes t11 ON t11.visit_id=t2.account_id
        INNER JOIN tbl_beds t10 ON t10.id=t4.bed_id
        INNER JOIN tbl_wards t5 ON t5.id =t4.ward_id
        INNER JOIN tbl_nurse_wards t6 ON t6.ward_id=t4.ward_id
        INNER JOIN tbl_patients t1 ON t1.id = t2.patient_id
        INNER JOIN users t9 ON t2.user_id=t9.id  
        WHERE t2.admission_status_id=2 AND t6.deleted=0
        );