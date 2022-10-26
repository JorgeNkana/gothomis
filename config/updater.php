<?php

	return [
		/*Please edit this part. */
		
		/*Specify your Server IP Address and the HFR facility code. Dash (-) should be replaced with underscore (_) */
		'myIP' => '192.168.43.97:9292' ,
		'hfr' => '107585-2' ,
		
		/*Replace GoT-HoMIS with your system folder name if different*/
		'sys_folder' => 'GoT-HoMIS' ,
		'root_path' => 'C:/xampp/htdocs/GoT-HoMIS/' ,
		'local_copy' => 'C:/xampp/htdocs/GoT-HoMIS/app/Http/Controllers/System_Updates/Updater.php' ,
		
		/*End of user editable part*/
		
		'notification' => 'http://196.192.72.107/api/notification',
		'files' => 'http://196.192.72.107/api/files',
		'timestamps' => 'http://196.192.72.107/api/timestamps',
		'remote_copy' => 'http://196.192.72.107/api/updater',
		'key_space_range' => 'http://196.192.72.107/api/key_space_range',
		'server_update_begin' => '0800',
		'server_update_end' => '1000',


		
		/*If you are not using xampp or you are on linux, plase specify the path to mysql directory otherwise leave unchanged*/
		'mysql' => 'C:/xampp/mysql/bin/mysql' ,
		'mysqldump' => 'C:/xampp/mysql/bin/mysqldump' ,

	];