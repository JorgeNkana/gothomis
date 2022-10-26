alter table tbl_residences change council_id council_id int(10) unsigned null;
INSERT INTO `tbl_residences` (residence_name)
SELECT * FROM (SELECT 'UNSPECIFIED') AS tmp
WHERE NOT EXISTS (
    SELECT residence_name FROM `tbl_residences` WHERE residence_name = 'UNSPECIFIED'
) LIMIT 1;
UPDATE tbl_patients set residence_id = (SELECT ID FROM `tbl_residences` WHERE residence_name = 'UNSPECIFIED' LIMIT 1) WHERE residence_id IS NULL;