INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'drf_sales','fa fa-dashboard fa-3x','DRF SALES',1,'$2y$10$dX2H7lUhGGJY3V2WhuKa1uxE5x5n.IzZhSA1hyJSn9lneGD33S87S') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'drf_sales'
) LIMIT 1;
INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'topup','fa fa-dashboard fa-3x','CHF TOPUP',1,'$2y$10$dX2H7lUhGGJY3V2WhuKa1uxE5x5n.IzZhSA1hyJSn9lneGD33S87S') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'topup'
) LIMIT 1;

INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'dermatology','fa fa-dashboard fa-3x','Dermatology Clinic',1,'$2y$10$dX2H7lUhGGJY3V2WhuKa1uxE5x5n.IzZhSA1hyJSn9lneGD33S87S') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'dermatology'
) LIMIT 1;


INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'surgical_clinic','fa fa-dashboard fa-3x','Surgical Clinic',1,'$2y$10$dX2H7lUhGGJY3V2WhuKa1uxE5x5n.IzZhSA1hyJSn9lneGD33S87S') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'surgical_clinic'
) LIMIT 1;


INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'mtuha_report','fa fa-dashboard fa-3x','Mtuha Reports',1,'$2y$10$9IcW/QMlNCQ8gcN/F25QMurGcjZDK0y3Bk1E9qdhB3ze2cTv9yuDm') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'mtuha_report'
) LIMIT 1;



INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'theatre_managing_list','fa fa-dashboard fa-3x','Anaesthesia',1,'$2y$10$DHpy9bzDTTG7/kre1qJVbuJW.jdx2ZloNkJGdW1J7/rKqGJVu2hBa') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'theatre_managing_list'
) LIMIT 1;

INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'doctor_theatre','fa fa-dashboard fa-3x','Surgeon',1,'$2y$10$x2pS8gkgVzMr23ij7Cu8murLo8GrBy878HLSAgi1P49Rl69jA2WQm') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'doctor_theatre'
) LIMIT 1;

INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'user_matrix','fa fa-dashboard fa-3x','User Matrix',1,'$2y$10$x2pS8gkgVzMr23ij7Cu8murLo8GrBy878HLSAgi1P49Rl69jA2WQm') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'user_matrix'
) LIMIT 1;

INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'staff_perfomance','fa fa-dashboard fa-3x','Staff Performance',1,'$2y$10$x2pS8gkgVzMr23ij7Cu8murLo8GrBy878HLSAgi1P49Rl69jA2WQm') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'staff_perfomance'
) LIMIT 1;

INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'opd_nursing','fa fa-dashboard fa-3x','Opd nursing',1,'$2y$10$9IcW/QMlNCQ8gcN/F25QMurGcjZDK0y3Bk1E9qdhB3ze2cTv9yuDm') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'opd_nursing'
) LIMIT 1;
INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'medi_suply','fa fa-dashboard fa-3x','Departmental Dispensing',1,'$2y$10$9IcW/QMlNCQ8gcN/F25QMurGcjZDK0y3Bk1E9qdhB3ze2cTv9yuDm') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'medi_suply'
) LIMIT 1;
INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'Dtc','fa fa-dashboard fa-3x','Dtc' as title,1,'$2y$10$9IcW/QMlNCQ8gcN/F25QMurGcjZDK0y3Bk1E9qdhB3ze2cTv9yuDm') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'Dtc'
) LIMIT 1;


INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'drf','fa fa-dashboard fa-3x','Drf' as title,1,'$2y$10$9IcW/QMlNCQ8gcN/F25QMurGcjZDK0y3Bk1E9qdhB3ze2cTv9yuDm') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'drf'
) LIMIT 1;


UPDATE `tbl_permissions` SET title = 'FINANCIAL CONTROLS' WHERE title = 'FINCANCIAL CONTROLS';
INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'finance_controls','fa fa-dashboard fa-3x','FINANCIAL CONTROLS' as title,1,'$2y$10$9IcW/QMlNCQ8gcN/F25QMurGcjZDK0y3Bk1E9qdhB3ze2cTv9yuDm') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'finance_controls'
) LIMIT 1;



INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'nhif_claim','fa fa-dashboard fa-3x','NHIF CLAIM' as title,1,'$2y$10$9IcW/QMlNCQ8gcN/F25QMurGcjZDK0y3Bk1E9qdhB3ze2cTv9yuDm') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'nhif_claim'
) LIMIT 1;
INSERT INTO `tbl_permissions` (module,glyphicons,title,main_menu,keyGenerated)
SELECT * FROM (SELECT 'nhif_setup','fa fa-dashboard fa-3x','NHIF SETUP' as title,1,'$2y$10$9IcW/QMlNCQ8gcN/F25QMurGcjZDK0y3Bk1E9qdhB3ze2cTv9yuDm') AS tmp
WHERE NOT EXISTS (
    SELECT module FROM `tbl_permissions` WHERE module = 'nhif_setup'
) LIMIT 1;