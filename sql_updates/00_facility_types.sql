INSERT INTO `tbl_facility_types` (description)
SELECT * FROM (SELECT 'CLINIC') AS tmp
WHERE NOT EXISTS (
    SELECT description FROM `tbl_facility_types` WHERE description = 'CLINIC'
) LIMIT 1;

INSERT INTO `tbl_facility_types` (description)
SELECT * FROM (SELECT 'HEALTH LABS') AS tmp
WHERE NOT EXISTS (
    SELECT description FROM `tbl_facility_types` WHERE description = 'HEALTH LABS'
) LIMIT 1;

INSERT INTO `tbl_facility_types` (description)
SELECT * FROM (SELECT 'HOSPITAL') AS tmp
WHERE NOT EXISTS (
    SELECT description FROM `tbl_facility_types` WHERE description = 'HOSPITAL'
) LIMIT 1;


INSERT INTO `tbl_facility_types` (description)
SELECT * FROM (SELECT 'NATIONAL SUPER SPECIALIST HOSPITAL') AS tmp
WHERE NOT EXISTS (
    SELECT description FROM `tbl_facility_types` WHERE description = 'NATIONAL SUPER SPECIALIST HOSPITAL'
) LIMIT 1;



INSERT INTO `tbl_facility_types` (description)
SELECT * FROM (SELECT 'REFERRAL HOSPITAL') AS tmp
WHERE NOT EXISTS (
    SELECT description FROM `tbl_facility_types` WHERE description = 'REFERRAL HOSPITAL'
) LIMIT 1;



INSERT INTO `tbl_facility_types` (description)
SELECT * FROM (SELECT 'NURSING HOME') AS tmp
WHERE NOT EXISTS (
    SELECT description FROM `tbl_facility_types` WHERE description = 'NURSING HOME'
) LIMIT 1;


INSERT INTO `tbl_facility_types` (description)
SELECT * FROM (SELECT 'MATERNITY HOME') AS tmp
WHERE NOT EXISTS (
    SELECT description FROM `tbl_facility_types` WHERE description = 'MATERNITY HOME'
) LIMIT 1;

INSERT INTO `tbl_facility_types` (description)
SELECT * FROM (SELECT 'TOWN COUNCIL HOSPITAL') AS tmp
WHERE NOT EXISTS (
    SELECT description FROM `tbl_facility_types` WHERE description = 'TOWN COUNCIL HOSPITAL'
) LIMIT 1;



UPDATE tbl_facility_types SET description = 'ZONAL SUPER SPECIALIST HOSPITAL' WHERE description = 'ZONE  HOSPITAL';
UPDATE tbl_facility_types SET description = 'DESIGNATED DISTRICT HOSPITAL' WHERE description = 'DISTRICT DESIGNATED HOSPITAL';
UPDATE tbl_facility_types SET description = 'REGIONAL REFERRAL HOSPITAL' WHERE description = 'REGIONAL REFFERAL  HOSPITAL';
UPDATE tbl_facility_types SET description = 'NATIONAL HOSPITAL' WHERE description = 'NATIONAL  HOSPITAL';
UPDATE tbl_facility_types SET description = 'OTHER HOSPITAL' WHERE description = 'SPECIAL  HOSPITAL';
UPDATE tbl_facility_types SET description = 'HEALTH CENTER' WHERE description = 'HEALTH CENTRE';

ALTER TABLE tbl_facilities DROP FOREIGN KEY IF EXISTS `tbl_facilities_region_id_foreign`;
ALTER TABLE tbl_facilities CHANGE COLUMN region_id region_id INT(10)  UNSIGNED NULL;
ALTER TABLE tbl_facilities ADD FOREIGN KEY IF NOT EXISTS `tbl_facilities_region_id_foreign`(region_id) REFERENCES tbl_regions(id);

ALTER TABLE tbl_facilities DROP FOREIGN KEY IF EXISTS `tbl_facilities_facility_type_id_foreign`;
ALTER TABLE tbl_facilities CHANGE COLUMN facility_type_id facility_type_id INT(10) UNSIGNED NULL;
ALTER TABLE tbl_facilities ADD FOREIGN KEY IF NOT EXISTS `tbl_facilities_facility_type_id_foreign`(facility_type_id) REFERENCES tbl_facility_types(id);


ALTER TABLE tbl_facilities DROP FOREIGN KEY IF EXISTS `tbl_facilities_council_id_foreign`;
ALTER TABLE tbl_facilities CHANGE COLUMN council_id council_id INT(10)  UNSIGNED NULL;
ALTER TABLE tbl_facilities ADD FOREIGN KEY IF NOT EXISTS `tbl_facilities_council_id_foreign`(council_id) REFERENCES tbl_councils(id);