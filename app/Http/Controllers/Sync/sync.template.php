<?php

	return [
		/*Please edit this part. if necessary*/
		
		/*Specify your Server IP Address and the HFR facility code. Dash (-) should be replaced with underscore (_) */
		myIP => ,
		hfr => ,
	
		/*Replace GoT-HoMIS with your system folder name if different*/
		tables_local => ,
		local_sync_file => ,
		server_sync_file => ,
		temp_dump => ,
		
		/*If you are not using xampp or you are on linux, plase specify the path to mysql directory otherwise leave unchanged*/
		mysqldump => ,
		mysql => ,
		
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