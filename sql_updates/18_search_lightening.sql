/*DROP TRIGGER IF EXISTS `opd_patients`;
DROP TRIGGER IF EXISTS `update_searchable_bill_details`;*/
alter table tbl_invoice_lines add column if not exists is_payable bool null;
alter table tbl_invoice_lines add column if not exists is_admitted bool null;
alter table tbl_invoice_lines add column if not exists main_category_id int(10) unsigned null;
alter table tbl_invoice_lines add column if not exists sub_category_name varchar(150) null;
alter table tbl_invoice_lines add column if not exists item_name varchar(150) null;
alter table tbl_invoice_lines add column if not exists price double null;
alter table tbl_accounts_numbers add column  if not exists main_category_id INT(10) unsigned null;
alter table tbl_accounts_numbers add column  if not exists sub_category_name varchar(150) null;
alter table tbl_accounts_numbers add column  if not exists category_description varchar(150) null;
alter table tbl_accounts_numbers add column  if not exists patient_category_id INT(10) unsigned null;
/*update tbl_invoice_lines set is_payable = null;
update tbl_invoice_lines set is_payable = true where exists(select * from tbl_item_prices where tbl_item_prices.facility_id = tbl_invoice_lines.facility_id and tbl_item_prices.id = tbl_invoice_lines.item_price_id and tbl_item_prices.sub_category_id = tbl_invoice_lines.payment_filter and (exemption_status=1 or insurance=0 or exists(select * from tbl_pay_cat_sub_categories where tbl_pay_cat_sub_categories.id= tbl_item_prices.sub_category_id and tbl_pay_cat_sub_categories.pay_cat_id = 1)));
alter table tbl_admissions add column if not exists old_admission_status_id int null;
update tbl_admissions set old_admission_status_id = admission_status_id;
update tbl_admissions set admission_status_id =6 where timestampdiff(day, admission_date,current_date) > 10 and patient_id not in (select patient_id from tbl_invoice_lines where timestampdiff(day,created_at,current_date) < 10);
update tbl_invoice_lines set is_admitted = false;
update tbl_invoice_lines set is_admitted = true where exists(select * from tbl_admissions where tbl_admissions.facility_id = tbl_invoice_lines.facility_id and tbl_admissions.patient_id = tbl_invoice_lines.patient_id and tbl_admissions.admission_status_id in (1,2,3));
update tbl_invoice_lines set tbl_invoice_lines.main_category_id =(select tbl_pay_cat_sub_categories.pay_cat_id from tbl_pay_cat_sub_categories where tbl_pay_cat_sub_categories.id = tbl_invoice_lines.payment_filter);
update tbl_invoice_lines set sub_category_name =(select tbl_pay_cat_sub_categories.sub_category_name from tbl_pay_cat_sub_categories where tbl_pay_cat_sub_categories.id = tbl_invoice_lines.payment_filter);
update tbl_invoice_lines set item_name =(select tbl_items.item_name from tbl_items where tbl_items.id = (select tbl_item_prices.item_id from tbl_item_prices where tbl_item_prices.id=tbl_invoice_lines.item_price_id));
update tbl_invoice_lines set price =(select tbl_item_prices.price from tbl_item_prices where tbl_item_prices.id=tbl_invoice_lines.item_price_id);
update tbl_accounts_numbers set main_category_id =(select tbl_bills_categories.main_category_id from tbl_bills_categories where tbl_bills_categories.account_id=tbl_accounts_numbers.id limit 1);
update tbl_accounts_numbers set patient_category_id =(select tbl_bills_categories.bill_id from tbl_bills_categories where tbl_bills_categories.account_id=tbl_accounts_numbers.id limit 1);
update tbl_accounts_numbers set sub_category_name =(select tbl_pay_cat_sub_categories.sub_category_name from tbl_pay_cat_sub_categories where tbl_pay_cat_sub_categories.id=tbl_accounts_numbers.patient_category_id);
update tbl_accounts_numbers set category_description =(select tbl_payments_categories.category_description from tbl_payments_categories where tbl_payments_categories.id=tbl_accounts_numbers.main_category_id);
*/
/*DELIMITER //
CREATE OR REPLACE TRIGGER accFilters AFTER INSERT ON tbl_bills_categories FOR EACH ROW
	BEGIN
		DECLARE main_category varchar(150);
		DECLARE sub_category varchar(150);
		SELECT category_description,sub_category_name INTO main_category,sub_category FROM tbl_payments_categories t1 join tbl_pay_cat_sub_categories t2 on t1.id = t2.pay_cat_id and t2.id = NEW.bill_id;
		UPDATE tbl_accounts_numbers set category_description = main_category, sub_category_name = sub_category, main_category_id = NEW.main_category_id, patient_category_id = NEW.bill_id WHERE id = NEW.account_id;
	END//
CREATE OR REPLACE TRIGGER accFilters2 AFTER UPDATE ON tbl_bills_categories FOR EACH ROW
	BEGIN
		DECLARE main_category varchar(150);
		DECLARE sub_category varchar(150);
		SELECT category_description,sub_category_name INTO main_category,sub_category FROM tbl_payments_categories t1 join tbl_pay_cat_sub_categories t2 on t1.id = t2.pay_cat_id and t2.id = NEW.bill_id;
		UPDATE tbl_accounts_numbers set category_description = main_category, sub_category_name = sub_category, main_category_id = NEW.main_category_id, patient_category_id = NEW.bill_id WHERE id = NEW.account_id;
	END//
DELIMITER ;
*/