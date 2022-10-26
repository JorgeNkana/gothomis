ALTER TABLE Tbl_items ADD IF NOT EXISTS status  integer NOT NULL DEFAULT 1;

 CREATE OR REPLACE VIEW  `vw_pharmacy_items` AS
SELECT distinct tbl_items.id,tbl_items.id as item_id,tbl_item_type_mappeds.item_code,tbl_items.item_name,
tbl_items.status,
    tbl_item_type_mappeds.item_category,
    tbl_item_type_mappeds.Dose_formulation,
    tbl_item_type_mappeds.sub_item_category,
    tbl_item_type_mappeds.unit_of_measure,
    tbl_item_type_mappeds.dispensing_unit

    FROM tbl_items INNER JOIN tbl_item_type_mappeds ON tbl_item_type_mappeds.item_id = tbl_items.id
    where tbl_items.status = 1
       and tbl_item_type_mappeds.item_category ='Medication' OR tbl_item_type_mappeds.item_category ='Medical Supplies';

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




CREATE OR REPLACE VIEW `vw_investigations_tests` AS(SELECT
                tbl_items.item_name,
                tbl_items.status,
                tbl_items.id AS item_id,
                tbl_item_prices.id AS item_price_id,
                tbl_item_prices.price,
				tbl_item_prices.onetime,
                tbl_item_prices.insurance,
                tbl_item_prices.exemption_status,
                tbl_item_prices.facility_id,
                tbl_item_prices.sub_category_id AS patient_category_id,
                tbl_sub_departments.id AS sub_dept_id,
                tbl_departments.id AS dept_id,
                tbl_item_type_mappeds.id AS item_type_id,
                tbl_equipments.id AS equipment_id,
                tbl_equipment_statuses.on_off
                FROM tbl_tests
                INNER JOIN tbl_items ON tbl_tests.item_id=tbl_items.id
                INNER JOIN tbl_sub_departments ON  tbl_tests.sub_department_id=tbl_sub_departments.id
                INNER JOIN tbl_departments ON tbl_departments.id=tbl_sub_departments.department_id
                INNER JOIN tbl_item_type_mappeds ON tbl_item_type_mappeds.item_id=tbl_items.id
                INNER JOIN tbl_item_prices ON tbl_item_prices.item_id=tbl_items.id
                INNER JOIN tbl_equipments ON tbl_equipments.id=tbl_tests.equipment_id
                INNER JOIN tbl_equipment_statuses ON tbl_equipment_statuses.id=tbl_equipments.equipment_status_id
                  where tbl_item_prices.status=1
                );