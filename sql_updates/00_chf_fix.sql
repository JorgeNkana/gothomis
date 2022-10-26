INSERT INTO `tbl_items` (item_name,dept_id)
SELECT * FROM (SELECT 'CHF Topup', 1) AS tmp
WHERE NOT EXISTS (
    SELECT item_name FROM `tbl_items` WHERE item_name = 'CHF Topup'
) LIMIT 1;

INSERT INTO `tbl_item_prices` (item_id,price,facility_id,sub_category_id,startingFinancialYear,endingFinancialYear)
SELECT * FROM (SELECT id, 1000, '1',2,'2017-01-01','20145-12-31' from `tbl_items` where item_name='CHF Topup') AS tmp
WHERE NOT EXISTS (
    SELECT tbl_item_prices.item_id,1000, '1',2,'2017-01-01','20145-12-31' FROM `tbl_item_prices` join tbl_items on `tbl_item_prices`.item_id = `tbl_items`.id and `tbl_items`.item_name = 'CHF Topup'
) LIMIT 1;

INSERT INTO `tbl_item_type_mappeds` (item_id,item_category)
SELECT * FROM (SELECT id, 'Service' from `tbl_items` where item_name='CHF Topup') AS tmp
WHERE NOT EXISTS (
    SELECT tbl_item_type_mappeds.item_id,'Service' FROM `tbl_item_type_mappeds` join tbl_items on `tbl_item_type_mappeds`.item_id = `tbl_items`.id and `tbl_items`.item_name = 'CHF Topup'
) LIMIT 1;