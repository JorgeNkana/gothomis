@echo off 
set vardate=%date%

set vartime=%time:~0,2%-%time:~3,2%-%time:~6,2%


C:\xampp\mysql\bin\mysqldump --user=root --password= --result-file="YOUR_PATH\DB_NAME-%vardate%-%vartime%.sql" DB_NAME
