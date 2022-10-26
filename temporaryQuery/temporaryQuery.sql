set foreign_key_checks = 0;
update tbl_ipd_mtuha_diagnoses set description = 'Positive(BS/mRDT)' where id = 12;
delete from tbl_ipd_mtuha_diagnoses where id = 13;
update tbl_ipd_mtuha_diagnoses set id = id - 1 where id > 12;
update tbl_ipd_mtuha_icd_blocks set ipd_mtuha_diagnosis_id = ipd_mtuha_diagnosis_id - 1 where ipd_mtuha_diagnosis_id > 12;