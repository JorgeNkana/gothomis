INSERT INTO `tbl_occupations` (occupation_name)
SELECT * FROM (SELECT 'CHILD') AS tmp
WHERE NOT EXISTS (
    SELECT occupation_name FROM `tbl_occupations` WHERE occupation_name = 'CHILD'
) LIMIT 1;

INSERT INTO `tbl_occupations` (occupation_name)
SELECT * FROM (SELECT 'NONE') AS tmp
WHERE NOT EXISTS (
    SELECT occupation_name FROM `tbl_occupations` WHERE occupation_name = 'NONE'
) LIMIT 1;