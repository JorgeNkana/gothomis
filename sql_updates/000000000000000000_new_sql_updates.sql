alter table `tbl_testspanels` ADD COLUMN IF NOT EXISTS `on_off` tinyint default 1;

CREATE OR REPLACE VIEW `vw_labtests_to_doctors` AS(SELECT
                tbl_items.item_name,
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
                tbl_testspanels.on_off
                FROM tbl_testspanels 
                INNER JOIN tbl_items ON tbl_testspanels.item_id=tbl_items.id 
                INNER JOIN tbl_equipments ON tbl_equipments.id=tbl_testspanels.equipment_id
                INNER JOIN tbl_sub_departments ON  tbl_equipments.sub_department_id=tbl_sub_departments.id
                INNER JOIN tbl_departments ON tbl_departments.id=tbl_sub_departments.department_id
                INNER JOIN tbl_item_type_mappeds ON tbl_item_type_mappeds.item_id=tbl_items.id
                INNER JOIN tbl_item_prices ON tbl_item_prices.item_id=tbl_items.id
                INNER JOIN tbl_equipment_statuses ON tbl_equipment_statuses.id=tbl_equipments.equipment_status_id
                where tbl_item_prices.status=1);
                
alter table tbl_drf_sale_stock_balances add column if not exists `batch_number` varchar(50) NULL;
alter table tbl_cash_deposits add column if not exists `drf` tinyint default 0;
alter table gepg_bills add column if not exists `drf` tinyint default 0;
alter table tbl_sales add column if not exists `gepg_receipt` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci null after batch_number;
alter table tbl_sales add column if not exists `PayCntrNum` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci null after gepg_receipt;
alter table tbl_sales add column if not exists `BillId` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci null after PayCntrNum;

alter table gepg_bills change invoiceId InvoiceId VARCHAR(150);

UPDATE tbl_drf_sale_stock_balances join tbl_drf_sale_stocks ON tbl_drf_sale_stock_balances.item_id = tbl_drf_sale_stocks.item_id SET tbl_drf_sale_stock_balances.batch_number = tbl_drf_sale_stocks.batch_number;

ALTER TABLE `tbl_sales` CHANGE `gepg_receipt` `gepg_receipt` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'utf8mb4_unicode_ci';
ALTER TABLE `tbl_sales` CHANGE `PayCntrNum` `PayCntrNum` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'utf8mb4_unicode_ci';
ALTER TABLE `tbl_sales` CHANGE `BillId` `BillId` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'utf8mb4_unicode_ci';

CREATE OR REPLACE VIEW vw_paid_bills AS (SELECT id,created_at, gepg_receipt AS receipt_number, gepg_receipt, facility_id, patient_id, first_name,middle_name,last_name,medical_record_number,CONCAT(ifnull(first_name,''),' ',ifnull(middle_name,''), ' ', ifnull(last_name,''), ' # ',medical_record_number) as name,quantity, discount, price, item_name, sub_category_name, main_category_id,is_payable FROM tbl_invoice_lines t1 WHERE status_id = 2 and payment_method_id = 2 and timestampdiff(day, created_at, current_timestamp) <= 7)UNION(SELECT t1.id,t1.created_at, t1.id AS receipt_number,t1.PspReceiptNumber as gepg_receipt, t1.facility_id, NULL as patient_id, '' as first_name,'' as middle_name,'' as last_name,'' as medical_record_number,CONCAT(t2.name, ' # ','') as name,  1, 0 as discount, t1.amountpaid, t1.transaction as item_name, NULL as sub_category_name, Null as main_category_id,true FROM tbl_cash_deposits t1 INNER JOIN users t2 ON t1.PspReceiptNumber IS NOT NULL and t1.cancelled IS NOT TRUE and t1.user_id = t2.id and (timestampdiff(day, t1.paid_at, current_timestamp) <= 2))UNION(SELECT t1.id,t1.created_at, t1.id AS receipt_number,t1.gepg_receipt, t2.facility_id, NULL as patient_id, buyer_name as first_name,'' as middle_name,'' as last_name,'' as medical_record_number,seller_name as name,  1, 0 as discount, t1.unit_price * t1.quantity as amountpaid, t1.item_name, NULL as sub_category_name, Null as main_category_id,true FROM tbl_sales t1 INNER JOIN users t2 ON t1.gepg_receipt IS NOT NULL and t1.user_id = t2.id and timestampdiff(day, t1.created_at, current_timestamp) <= 7);