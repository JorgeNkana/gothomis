INSERT INTO `tbl_relationships` (relationship)
SELECT * FROM (SELECT 'POLICE') AS tmp
WHERE NOT EXISTS (
    SELECT relationship FROM `tbl_relationships` WHERE relationship = 'POLICE'
) LIMIT 1;