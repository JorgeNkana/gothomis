ALTER TABLE tbl_transaction_types ADD COLUMN IF NOT EXISTS adjustment_code VARCHAR(50) AFTER adjustment;
ALTER TABLE tbl_transaction_types CHANGE transaction_type transaction_type VARCHAR(150);
UPDATE tbl_transaction_types SET transaction_type = 'Transfer In', adjustment_code = 'TRANSFER_IN' WHERE id = 2;
UPDATE tbl_transaction_types SET transaction_type = 'Clinic Return', adjustment_code = 'CLINIC_RETURN' WHERE id = 3;
UPDATE tbl_transaction_types SET transaction_type = 'Transfer Out', adjustment_code = 'TRANSFER_OUT' WHERE id = 4;
UPDATE tbl_transaction_types SET transaction_type = 'Stolen', adjustment_code = 'STOLEN' WHERE id = 5;
UPDATE tbl_transaction_types SET transaction_type = 'Expired', adjustment_code = 'EXPIRED' WHERE id = 6;
UPDATE tbl_transaction_types SET transaction_type = 'Demaged', adjustment_code = 'DEMAGED' WHERE id = 7;

INSERT INTO tbl_transaction_types(transaction_type, adjustment_code,adjustment) SELECT * FROM (SELECT 'Lost' as transaction_type, 'LOST' as adjustment_code,'minus' as adjustment) AS temp
WHERE NOT EXISTS (
    SELECT transaction_type FROM `tbl_transaction_types` WHERE transaction_type = 'Lost'
) LIMIT 1;

INSERT INTO tbl_transaction_types(transaction_type, adjustment_code,adjustment) SELECT * FROM (SELECT 'Passed Open Vial Time Limit' as transaction_type, 'PASSED_OPEN_VIAL_TIME_LIMIT' as adjustment_code,'minus' as adjustment) AS temp
WHERE NOT EXISTS (
    SELECT transaction_type FROM `tbl_transaction_types` WHERE transaction_type = 'Passed Open Vial Time Limit'
) LIMIT 1;


INSERT INTO tbl_transaction_types(transaction_type, adjustment_code,adjustment) SELECT * FROM (SELECT 'Cold Chain Failure' as transaction_type, 'COLD_CHAIN_FAILURE' as adjustment_code,'minus' as adjustment) AS temp
WHERE NOT EXISTS (
    SELECT transaction_type FROM `tbl_transaction_types` WHERE transaction_type = 'Cold Chain Failure'
) LIMIT 1;