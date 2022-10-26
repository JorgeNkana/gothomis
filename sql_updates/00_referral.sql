alter table tbl_referrals add column if not exists remarks text;
alter table tbl_referrals add column if not exists patient_status varchar(50);
alter table tbl_referrals add column if not exists visit_id int(101);