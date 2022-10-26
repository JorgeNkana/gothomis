SELECT CONCAT('<table table_name="',t.TABLE_NAME,'">',t.TABLE_NAME,'</table>'), '\N') FROM information_schema.key_column_usage kcu
RIGHT JOIN information_schema.Tables t
    ON t.TABLE_NAME=kcu.TABLE_NAME
WHERE t.TABLE_SCHEMA='IRINGA' GROUP BY t.TABLE_NAME ORDER BY t.TABLE_TYPE DESC, COUNT(TRUE) DESC;