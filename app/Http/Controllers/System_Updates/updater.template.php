<?php

return [
	/*Please edit this part. */
	
	/*Specify your Server IP Address and the HFR facility code. Dash (-) should be replaced with underscore (_) */
	myIP => ,
	hfr => ,
	
	/*Replace GoT-HoMIS with your system folder name if different*/
	sys_folder => ,
	root_path => ,
	local_copy => ,
	
	/*End of user editable part*/
	
	'notification' => 'http://196.192.72.107/api/notification',
	'files' => 'http://196.192.72.107/api/files',
	'timestamps' => 'http://196.192.72.107/api/timestamps',
	'remote_copy' => 'http://196.192.72.107/api/updater',
	'key_space_range' => 'http://196.192.72.107/api/key_space_range',
	'server_update_begin' => '2300',
	'server_update_end' => '2359',


	
	/*If you are not using xampp or you are on linux, plase specify the path to mysql directory otherwise leave unchanged*/
	mysql => ,
	mysqldump => ,

];