REM: PLEASE READ LINE 5, 23, 25, 27 IN THAT ORDER!!

EXIT

REM: -- FIRST LOGIN INTO THE SYSTEM AS ADMIN AND OPEN THE DB SETUP=>SYSTEM ACTIVATION PAGE

DEL "C:\xampp\htdocs\GoT-HoMIS\fix not a view error.sql"

CD\

CD\xampp\mysql\bin

MYSQL --user=root --password=

SELECT CONCAT('DROP VIEW IF EXISTS `' ,TABLE_NAME ,'`;\rDROP TABLE IF EXISTS `' ,TABLE_NAME ,'`;\r') INTO OUTFILE 'C:/xampp/htdocs/GoT-HoMIS/fix not a view error.sql' FROM INFORMATION_SCHEMA.TABLES WHERE Table_schema = 'sfrh_db' AND Table_Name LIKE 'vw_%';

EXIT;

MYSQL --user=root --password= sfrh_db < "C:/xampp/htdocs/GoT-HoMIS/fix not a view error.sql"

DEL "C:\xampp\htdocs\GoT-HoMIS\fix not a view error.sql"

REM: -- WHEN THIS IS DONE, GO BACK TO THE SYSTEM PAGE AND RUN SYSTEM VIEWS COMMAND BUTTON

REM: -- ENJOY

REM: -- IF YOU HAVE READ ALL INSTRUCTIONS, NOW REMOVE LINE 3

REM: THEN COPY AND PASTE THE FINAL CONTENTS ONTO A CMD WINDOW