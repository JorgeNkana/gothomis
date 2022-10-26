<?php

	return [
		/*Please edit this part. if necessary*/
		
		/*Specify your Server IP Address and the HFR facility code. Dash (-) should be replaced with underscore (_) */
		'myIP' => '192.168.43.97:9292' ,
		'hfr' => '107585-2' ,
	
		/*Replace GoT-HoMIS with your system folder name if different*/
		'tables_local' => 'C:/xampp/htdocs/GoT-HoMIS/public/sync/tables.xml' ,
		'local_sync_file' => 'C:/xampp/htdocs/GoT-HoMIS/public/sync/sync.xml' ,
		'server_sync_file' => 'C:/xampp/htdocs/GoT-HoMIS/public/sync/clients/' ,
		'temp_dump' => 'C:/xampp/htdocs/GoT-HoMIS/public/sync/temp_dump.sql' ,
		
		/*If you are not using xampp or you are on linux, plase specify the path to mysql directory otherwise leave unchanged*/
		'mysqldump' => 'C:/xampp/mysql/bin/mysqldump' ,
		'mysql' => 'C:/xampp/mysql/bin/mysql' ,
		
		/*End of user editable part*/
		
		'remote_server' => 'http://196.192.72.107:80/api/sync',
		'tables_server' =>  'http://196.192.72.107:80/api/sync_tables',
		'cipher'=>'AES-256-CBC',
		'hash'=>'SHA512',
		'hash_len'=>'64',
		'key' => '$2y$10$CeNvJkHH209zVFsr/ZCA2OboPfsoL7i0HjNGbmuqEjztWG2xzn9L2',
		'server_update_begin' => '0800',
		'server_update_end' => '1000',
		
	];