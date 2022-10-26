alter table tbl_orders modify column clinical_note text;
alter table tbl_orders modify column order_cancel_reason text;
alter table tbl_results modify column description text;
alter table tbl_results modify column cancel_reason text;
alter table tbl_results modify column remarks text;
alter table tbl_complaints modify column description text;
alter table tbl_complaints modify column other_complaints text;
alter table tbl_complaints modify column hpi text;