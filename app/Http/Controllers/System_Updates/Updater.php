<?php

	$message = "";
	$background_instance = false;
	
	/*
	 * Uncomment the next if block below to prevent clients from starting 
	 * an update process. Useful in case of snap updates on server
	 * Note:  !!!This does not stop ongoing updates, use with care!!!
	 * The @-- is set as IP of the client who is updating despite the restriction..developer backdoor
	 */
	/*if(!preg_match("/-@-/",simplexml_load_string($api_request)->client)){
		file_put_contents(Config::get('updater.root_path')."public/updated_files.txt","done");
		file_put_contents(Config::get('updater.root_path')."public/counter.txt","done");
		$message = "<span style='color:red'>Sorry. The update service is not available at this moment, please try again later</span><hr />Thank you.";
		goto exit_updater;	
	}*/
	
	/* Note that this logic is implemented in the wrapper class in the
	 * makeConfig function. To account for earlier releases that does not
	 * have the function, the logic is re-implemented here.
	 * Also, having a timestamp check in this function can make the client
	 * file re-written at will anytime. */
	
	$reConfig = function(Illuminate\Http\Request $request, $force = false){
		$this_file = str_ireplace("\\","/",__FILE__);
		
		/* 
			Root path/to/htdocs/folder/
		*/
		$root_path = substr($this_file,0, strpos($this_file,"app/Http"));
		
		//Takes care of users who have copied a folder and rename it
		$folder_mismatch = Config::get("updater.root_path") != $root_path;
		
		//Anytime we want to overwrite, set the desired time in the last DateTime constructor
		$outdated = (new \Datetime(date ("Y-m-d H:i:s",filemtime($root_path."config/updater.php"))) < new \Datetime("2019-08-31 00:00:01")) ? true : false;
		
		if($folder_mismatch && file_exists(Config::get('updater.root_path').'public/updater.rem'))
			unlink(Config::get('updater.root_path').'public/updater.rem');
		
		if(!$force && !($folder_mismatch || $outdated))
			return;
		
		/* 
			Root path/to/htdocs/folder
		*/
		$root_path = substr($this_file,0, strpos($this_file,"/app/Http"));
		try{	
			//determine possible OS in use
			$OS = array("Windows"   =>   "/Windows/i",
						"Linux"     =>   "/Linux/i",
						"Unix"      =>   "/Unix/i",
						"Mac"       =>   "/Mac/i"
						);
			foreach($OS as $key => $value){
				if(preg_match($value, php_uname())){
					$OS = $key;
					break;
				}
			}
			
			$server_address = (($request->server('SERVER_ADDR')=="::1" || !preg_match("/\d{1,}/",$request->server('SERVER_ADDR'))) ? "127.0.0.1" : $request->server('SERVER_ADDR')).":".$request->server('SERVER_PORT');
			$hfr = DB::select("select facility_code from tbl_facilities where id='".$request->facility_id."'");
			$hfr = $hfr[0]->facility_code;
$template = <<<TEMPLATE
<?php

	return [
		/*Please edit this part. */
		
		/*Specify your Server IP Address and the HFR facility code. */
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
		'server_update_begin' => '0800',
		'server_update_end' => '1000',


		
		/*If you are not using xampp or you are on linux, plase specify the path to mysql directory otherwise leave unchanged*/
		mysql => ,
		mysqldump => ,

	];
TEMPLATE;
			$sys_folder = substr($root_path,strrpos($root_path,"/")+1);
			$root_path .="/";
			$local_copy = $root_path."app/Http/Controllers/System_Updates/Updater.php";
			$mysql = (
						($OS == "Windows" 
							? substr($root_path,0,strpos($root_path,"htdocs")) . "mysql/bin/mysql"
							: ($OS == "Linux" || $OS == "Unix" 
									? "mysql"
									: ($OS == "Mac" ? "/Application/xampp/mysql/bin/mysql" : "")
								)									
						)
					);
			$mysqldump = (
							($OS == "Windows" 
								? substr($root_path,0,strpos($root_path,"htdocs")) . "mysql/bin/mysqldump"
								: ($OS == "Linux" || $OS == "Unix" 
										? "mysqldump"
										: ($OS == "Mac" ? "/Application/xampp/mysql/bin/mysqldump" : "")
									)									
							)
						);
						
			$template = str_replace("myIP =>","'myIP' => '".$server_address."'",$template);
			$template = str_replace("hfr =>","'hfr' => '".$hfr."'",$template);
			$template = str_replace("root_path =>","'root_path' => '".$root_path."'",$template);
			$template = str_replace("sys_folder =>","'sys_folder' => '".$sys_folder."'",$template);
			$template = str_replace("local_copy =>","'local_copy' => '".$local_copy."'",$template);
			$template = str_replace("mysql =>","'mysql' => '".$mysql."'",$template);
			$template = str_replace("mysqldump =>","'mysqldump' => '".$mysqldump."'",$template);
			file_put_contents($root_path."config/updater.php",$template);
					
			/* Always ensure we are working with current config files */
			Artisan::call('config:clear');
			Artisan::call('config:cache');
		}catch(Exception $ex){
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
		}
		finally{
			
		}
	};
	
	$folderBackup = function(Illuminate\Http\Request $request, $force_abort = true){
		$backup = dirname(Config::get('updater.root_path').Config::get('updater.sys_folder')).'-backup-'. Date('Y-m-d-Hsi');
		$current = dirname(Config::get('updater.root_path').Config::get('updater.sys_folder'));
		try{
			file_put_contents(Config::get('updater.root_path').'public/updated_files.txt',"^Taking backup of your current files to $backup...");
			if(file_exists(Config::get('updater.root_path')."storage/logs/laravel.log"))
				try{
					unlink(Config::get('updater.root_path')."storage/logs/laravel.log");
				}catch(Exception $ex){
					file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
				}
				finally{
					
				}

			$command = "xcopy \"$current\" \"$backup\" /E /I /H /R";
			$output = array("Update_date"=>Date('Y-m-d H:i:s'));
			exec($command, $output, $result);
			if(isset($output['0']) && strpos($output['0'],'RROR') >0){
				file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .print_r($output,TRUE).PHP_EOL, FILE_APPEND);
				$message = "<span style='color:red'>An error occurred while making a backup of your system folder.<hr />Please, manually make a copy of the folder under htdocs named as \"".Config::get('updater.sys_folder').'-backup-'. Date('Y-m-d')."\" and then restart the update process without the folder backup option.</span>";
				if($force_abort)
					return false;
			}
		}catch(Exception $ex){
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s')."    ".$ex->getMessage().PHP_EOL, FILE_APPEND);
			$message = "<span style='color:red'>An error occurred while making a backup of your system folder.<hr />Please manually make a copy of the folder under htdocs named as \"".Config::get('updater.sys_folder').'-backup-'. Date('Y-m-d')."\" and then restart the update process without the backup option</span>";
			if($force_abort)
				return false;
		}
		finally{
			
		}
		return true;//success
	};

	$cleanDirectory = function($path = Null, $start = true) use(&$cleanDirectory){
		if($start){
			$this->directory_map = include(Config::get('updater.root_path')."app/Http/Controllers/System_Updates/directory_map.php");
			
			if(!is_array($this->directory_map) || count($this->directory_map) == 0)
				return;
			
			$this->excludes = [
						Config::get("updater.root_path").".env",
						Config::get("updater.root_path")."composer.json",
						Config::get("updater.root_path")."run_sql_updates_manually.bat",
						Config::get("updater.root_path")."fix not a view error.sql",
						Config::get("updater.root_path")."automated_central_backups.bat",
						Config::get("updater.root_path")."database/sync_call.bat",
						Config::get("updater.root_path")."database/sync.ps1",
						Config::get("updater.root_path")."bootstrap",
						Config::get("updater.root_path")."node_modules",
						Config::get("updater.root_path")."storage",
						Config::get("updater.root_path")."tests",
						Config::get("updater.root_path")."vendor",
						Config::get("updater.root_path")."public/bower_components",
						Config::get("updater.root_path")."public/font-awesome",
						Config::get("updater.root_path")."public/uploads",
						Config::get("updater.root_path")."public/updater.rem",
						Config::get("updater.root_path")."public/counter.txt",
						Config::get("updater.root_path")."public/updated_files.txt",
						Config::get("updater.root_path")."public/update_log.txt",
						Config::get("updater.root_path")."public/aggregation.log",
						Config::get("updater.root_path")."public/runned_scripts.txt",
						Config::get("updater.root_path")."public/auto_task_creation.log",
						Config::get("updater.root_path")."public/sync_log.txt",
						Config::get("updater.root_path")."public/dump_log.txt",
					];
			
			$path = str_ireplace("\\","/",__FILE__);
			$path = substr($path,0, strpos($path,"app/Http"));
		
			// Remove the log file that sometimes gets very big
			if(file_exists(Config::get("updater.root_path")."storage/logs/laravel.log")){
				try{
					unlink(Config::get("updater.root_path")."storage/logs/laravel.log");
				}catch(Exception $ex){
					file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
				}
				finally{
					
				}
			}
		}
		
		$directory = new \DirectoryIterator($path);
		if(!$start)
			$path = str_ireplace("\\","/",$path)."/";
		foreach($directory as $fileinfo){
			if($fileinfo->isDot())
				continue;
			
			elseif(in_array($path.$fileinfo->getFilename(), $this->excludes))
				continue;
			
			elseif($fileinfo->isFile() && !in_array(strtolower(str_replace(Config::get('updater.root_path'),"{root_path}",$path).$fileinfo->getFilename()), $this->directory_map)){
				try{
					if(file_exists($path.$fileinfo->getFilename())){
						@chmod($path.$fileinfo->getFilename(), 0777 );
						unlink($path.$fileinfo->getFilename());
					}
				}catch(Exception $ex){
					file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
				}
				finally{
					
				}
			}elseif($fileinfo->isDir()){
				$cleanDirectory($fileinfo->getPathName(), false);
				$left_overs = scandir($fileinfo->getPathName());
				try{
					if(count($left_overs) == 2 && $left_overs[0]=='.' && $left_overs[1] == '..')
						rmdir($fileinfo->getPathName());
				}catch(Exception $ex){
					file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
				}
				finally{
					
				}
			}
		}
		
		if($start){
			$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
			$contents->cleaned_directory = true;
			file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
		}
	};
	

	$createTasks = function(Illuminate\Http\Request $request){
		if(!$request->has('is_live') || $request->is_live != 1)
			return;
	
		if(file_exists(Config::get('updater.root_path').'database/sync1.ps1'))
			unlink(Config::get('updater.root_path').'database/sync1.ps1');
		
		if(file_exists(Config::get('updater.root_path').'database/sync2.ps1'))
			unlink(Config::get('updater.root_path').'database/sync2.ps1');
		
		
		//powershel script to force updates
		$ps0 = "\$HttpWebRequest0 = [system.net.WebRequest]::Create('http://".Config::get('updater.myIP')."/api/update/$request->facility_id,0,0,0,1')\r\n";
		$ps0 .= "\$HttpWebRequest0.Timeout = -1\r\n";
		$ps0 .= "\$response0 = \$HttpWebRequest0.GetResponse()\r\n\r\n";
		$ps0 .= "\r\n";
		$ps0 .= "\r\n";
		$ps0 .= "exit\r\n";
		/*
		//powershel script for sending a database dump backup to the central server
		$ps1 = "\$HttpWebRequest1 = [system.net.WebRequest]::Create('http://".Config::get('updater.myIP')."/api/sync/$request->facility_id')\r\n";
		$ps1 .= "\$HttpWebRequest1.Timeout = -1\r\n";
		$ps1 .= "\$response1 = \$HttpWebRequest1.GetResponse()\r\n\r\n";
		//$ps1 .= "exit\r\n";
		$ps1 .= "\r\n";
		$ps1 .= "\r\n";
		
		//powershel script for sending dashboard data to central server
		$ps2 = "\$HttpWebRequest2 = [system.net.WebRequest]::Create('http://".Config::get('updater.myIP')."/dashboard/reporting/$request->facility_id')\r\n";
		$ps2 .= "\$HttpWebRequest2.Timeout = -1\r\n";
		$ps2 .= "\$response2 = \$HttpWebRequest2.GetResponse()\r\n\r\n";
		$ps2 .= "exit\r\n";
		*/
		
		file_put_contents(Config::get('updater.root_path').'database/sync.ps1', $ps0.PHP_EOL);
		
		$batch = "powershell -noprofile -ExecutionPolicy bypass  -file \"".Config::get('updater.root_path')."database/sync.ps1\"";
		
		file_put_contents(Config::get('updater.root_path').'database/sync_call.bat', $batch);
		
		try{
			$command = "schtasks /f /create /tn \"GOTOHMIS_AUTO_SYNC\" /tr \"".Config::get('updater.root_path')."database\sync_call.bat\" /sc DAILY /st 0".rand(1,3).":".rand(11,59)." /ru System 2>&1";
			$output = array();
			try{
				exec($command, $output, $result);
			}catch(Exception $ex){
				file_put_contents("auto_task_creation.log", Date('Y-m-d H:i:s')."    ".$ex->getMessage().PHP_EOL, FILE_APPEND);
				
				$message = "<span style='color:green'>Locate and execute (as Win admin) the batch file named [automated_central_backups] in the system folder.</span>";
			}
			finally{
				
			}
			
			file_put_contents(Config::get("updater.root_path")."automated_central_backups.bat", $command);
			
			$contents = file_exists(Config::get('updater.root_path').'public/updater.rem') 
					? JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'))
					:  new \stdClass();
			$contents->added_automated_tasks = true;
			file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
		}catch(Exception $ex){
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s')."    ".$ex->getMessage().PHP_EOL, FILE_APPEND);
		}
		finally{  
			
		}
	};
	
	
	$reConfig($request);
	$createTasks($request);
	
	/*	
	//should not continue if db version not satisfactory
	if(preg_match('/10\.[1-10]/',DB::select('select version() as version')[0]->version) !==1){//do it only if the Database version is correct
		$message = "<hr /><span style='color:red'>Your database engine is outdated. Automatic execution of Database updates will not be possible. Upgrade the database to at least </span><b>MariaDB Ver. 10.1.16</b> <span style='color:red'>in order to continue.</span>";
		goto exit_updater;
	}
	*/
	if(!$request->has("do_on_default_account") || $request->do_on_default_account == 0){
		if($request->facility_id == 1 && preg_match("/DODOMA HQ/",DB::select("select facility_name from tbl_facilities where id='1'")[0]->facility_name) ===1){
			$message = ["message"=>"<span style='color:red'>The update process has detected that it is being performed on the default setup facility account. This may prevent useful database updates to take effect. If you are updating a live instance, please login with the facility's admin account to perform the update process.</span><hr />","status"=>0];
			goto exit_updater;
		}
	}	
	
	
	//correct keys recorded in the file
	if(file_exists(Config::get('updater.root_path').'public/updater.rem') && 
		new \DateTime(date ("Y-m-d H:i:s",filemtime(Config::get('updater.root_path').'public/updater.rem'))) < new \Datetime("2018-07-19 00:00:00")){
		unlink(Config::get('updater.root_path').'public/updater.rem');
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt', "^Warning for long process...this update is going to take a long time to finish. Extensive repair is proposed on this update. Please ensure your power supply and internet connectivity is stable as the process proceeds.");
	}
	
	/* In order to avoid skipping scripts due to copied folder,
	 * the file existance and the facility_code is tested to
	 * decide on whether to recreate the file or not
	 */
	$facility_code = DB::select("select facility_code from tbl_facilities where id=".$request->facility_id)[0]->facility_code;
	if(file_exists(Config::get('updater.root_path').'public/updater.rem')){
		$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
		
		//variable removed since it was added inadvertently
		unset($contents->created_sync_tasks);
		
		if(empty($contents->facility_code) || $contents->facility_code != $facility_code){
			$contents = new \stdClass();
			$contents->facility_code = $facility_code;
			/* Overwrite the file with only this field. 
			 * Other fields will be added as the scripts are run
			 */
			file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
		}
	}else{
		$contents = new \stdClass();
		$contents->facility_code = $facility_code;
		file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
	}
	
	
	//the -@- client IP is used for developer backdoor access to the service
	if(!preg_match("/-@-/",simplexml_load_string($api_request)->client) 
		&& !preg_match("/\d{2,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{2,}/",simplexml_load_string($api_request)->client)){
		$message = "<span style='color:red'>Incorrect configuration file was detected. A re-configuration has been performed. Please restart the update process again.</span><hr />Thank you.";
		$reConfig($request,true);
		goto exit_updater;	
	}
	
	if(!isset($curl_error))
		$curl_error = "";
	
	
	/* Hard-coded!!!! Overwrites the check performed in the Init(). 
	 * The intention here for now is to arrest those who
	 * will edit their config files and put a timeframe that 
	 * includes the server update time range 0800 - 1000
	 */
	
	$timing_error = false;
	
	/*$server_update_begin = new \DateTime('0800');
	$server_update_end = new \DateTime('1000');
	$config_begin = new \DateTime(Config::get("updater.server_update_begin"));
	$config_end = new \DateTime(Config::get("updater.server_update_end"));
	$now = new \DateTime();
	
	if (($config_begin != $server_update_begin || $config_end != $server_update_end) || ($now >= $server_update_begin && $now <= $server_update_end)){
		$timing_error = true;
		$message = "<span style='color:red'>Sorry. The update can not be started between ". $server_update_begin->format("H:i")." and ".$server_update_end->format("H:i")."AM. Try again after ".$server_update_end->format("H:i")."AM.</span>";
	}*/
	
	/* Exit the script if time mismatch detected */
	if($timing_error){
		$reConfig($request,true);
		//temporarilly disabled to allow update at any moment
		//goto exit_updater; 
	}
	
	$update_control = "CREATE TABLE IF NOT EXISTS file_updates(
						id int auto_increment not null,
						file_name varchar(350) not null,
						checksum varchar(350) not null,
						date timestamp not null,
						mark_id int null default null, -- column added on 16/11/2017
						primary key(id)
					)";
	/* Create the table on the update client only if doesn't exist*/
	DB::statement($update_control);
	
	/* For the benefit of tables created earlier before this field was added */
	DB::statement("alter table `file_updates` add column if not exists mark_id int null default null");
	
	//TEMPORARY: Positioned here to allow following tasks to have the record in place
	if(DB::select("select count(*) count from INFORMATION_SCHEMA.tables where table_schema='".env('DB_DATABASE')."' and table_name='tbl_reattendance_free_days'")[0]->count == 0){	
		DB::statement("CREATE TABLE IF NOT EXISTS `tbl_reattendance_free_days` (
						  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
						  `facility_id` int(11) UNSIGNED NOT NULL,
						  `days` tinyint NOT NULL DEFAULT 0,
						  `description` varchar(150) NULL,
						  `created_at` timestamp NOT NULL,
						  `updated_at` timestamp NOT NULL,
						   PRIMARY KEY(id),
						  FOREIGN KEY (`facility_id`) REFERENCES `tbl_facilities`(`id`) ON UPDATE CASCADE
						)");
	}else{//column dropped from definition		
		try{
			DB::statement("alter table tbl_reattendance_free_days drop FOREIGN KEY IF EXISTS `tbl_reattendance_free_days_ibfk_2`");
			DB::statement("alter table tbl_reattendance_free_days drop KEY IF EXISTS `user_id`");
			DB::statement("alter table tbl_reattendance_free_days drop column if exists `user_id`");
		}catch(Exception $ex){
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s')."    ".$ex->getMessage().PHP_EOL, FILE_APPEND);
		}
		finally{
			
		}
	}
	
	if(count(DB::select("select * from tbl_reattendance_free_days where facility_id='".$request->facility_id."'")) ===0)
		DB::statement("insert into tbl_reattendance_free_days(facility_id, days)values('".$request->facility_id."',1)");
	
	
	//set the root path of the application folder
	if(!preg_match('/root_path/',file_get_contents(Config::get('updater.root_path').'public/updater.rem'))){		
		$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
		$contents->root_path = Config::get('updater.root_path');
		file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
	}
	
	
	/* Load the last update date remembered*/
	$last_update = DB::select("select MAX(date) as date, MAX(id) as id from `file_updates` where file_name not in ('Begining Update', 'End Update')");
	
	/* If last update not available, take January 01,2017 as initial date */
	if(is_array($last_update)  && count($last_update) > 0 && $last_update[0]->date != '')
		$last_update_date = $last_update[0]->date; 
	else
		$last_update_date = '2017-01-01 00:00:01';
	
	
	if($last_update[0]->id != ''){
		//a forced refresh due to incompatibility detected in previous updates
		if($last_update_date < '2021-08-01 12:00:00'){
			$last_update_date = '2017-01-01 00:00:01';
			DB::statement("truncate file_updates");
		}
		DB::statement("update file_updates set mark_id = ".($last_update[0]->id+1)." where id > ".$last_update[0]->id);
	}
	
	/* Used to select which sql updates to run */
	$last_update_time = new \DateTime($last_update_date);
	
	/* An array to hold the list of file, checksum and timestamps of files detected on server */
	$timestamps = NULL;
	
	/*Marks the beginning of the update */
	DB::statement("insert into file_updates(file_name,checksum,date) select 'Begining Update',NULL,CURRENT_TIMESTAMP");
		
	/* 
	 * Request list of file names,checksums and last updated timestamps 
	 * in ascending order from update server. 
	 * In case the update is a continuation of a partially completed one 
	 * then the last file downloaded is used as lower mark for the request.
	 * Note that this script will try to check if there is not other
	 * instance running before it actually do the continuation else 
	 * it exits to leave the active one finish.
	 */
	$last_file = "";
	if($request->has("continue") && $request->continue == 1){
		$initial_contents_found = trim(file_get_contents(Config::get("updater.root_path")."public/updated_files.txt"));
		
		//delay a bit so that the file changes if other instance is running
		sleep(30);//30 secs
		
		$contents_found_now = trim(file_get_contents(Config::get("updater.root_path")."public/updated_files.txt"));
		
		if($initial_contents_found != $contents_found_now){
			$message = "<span style='color:red'>A background instance of the updater has been detected. Please watch for popping messages to detect it when done</span>";
			$background_instance = true;
			goto exit_updater;
		}
		$initial_contents_found =  str_replace("//","/",substr($initial_contents_found, strpos($initial_contents_found,Config::get("updater.sys_folder"))+strlen(Config::get("updater.sys_folder"))));
		
		$last_file = substr($initial_contents_found, strpos($initial_contents_found,"/")+1);
	}
	
	$api_request = "<client>
					<ip>".Config::get('updater.myIP')."</ip>
					<hfr>".Config::get('updater.hfr')."</hfr>
					<last_update>$last_update_date</last_update>
					<last_file>$last_file</last_file>
				</client>";
	
	/* Request for timestamps */
	$attempts = 0;
	while($attempts < 10 && $ch = curl_init(Config::get('updater.timestamps'))){
		$attempts++;
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $api_request);
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/xml'));
		$data = curl_exec($ch);
		if(curl_errno($ch)){
			$curl_error = curl_error($ch);
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s')."    ".curl_error($ch).PHP_EOL, FILE_APPEND);
			continue;
		}
		curl_close($ch);
		$timestamps = JSON_decode($data);
		
		/* Ends the loop on success*/
		$attempts = 10;
	}/** End timestamps request **/

	/* Something went wrong while attempting to download the 
	 * timestamps. Set the curl error generated and exit the script
	 */
	if($curl_error != ""){
		$message = $curl_error;
		goto exit_updater;
	}
	
	if(!is_array($timestamps)){
		$message = "Error on retrieving update details. Please re-try";
		goto exit_updater;
	}
	
	/* Reset if everything went fine */
	$curl_error = "";
	
	/* Counts the number of files successfully downloaded */	
	$download_success = 0;
	
	$all_files = count($timestamps);
	/* tracks if there were any .js, .html files downloaded that shall require browser cache being cleared*/
	$js_html = 0;
	
	/* If timestamps successfully downloaded and has any item*/
	if($timestamps && count($timestamps) > 0){
		file_put_contents(Config::get('updater.root_path').'public/counter.txt', count($timestamps));
		/* Local variables if the sync file with the encryption details is not yet configured */
		$encryption_key = (Config::has('sync.key') ? Config::get('sync.key') : '$2y$10$CeNvJkHH209zVFsr/ZCA2OboPfsoL7i0HjNGbmuqEjztWG2xzn9L2');
		$cipher_algorithm = (Config::has('sync.cipher')  ? Config::get('sync.cipher') : 'AES-256-CBC');
		$hash_algorithm = (Config::has('sync.hash') ? Config::get('sync.hash') : 'SHA512');
		$hash_length = (Config::has('sync.hash_len') ? Config::get('sync.hash_len') : '64');
		
		/* 
		 * Make a backup of the folder before starting update if the option was set 
		 */
		if($request->has("make_backup") && $request->make_backup == 1){
			if(!$folderBackup($request))
				goto exit_updater;
		}
		
		
		/*
		 * For each file in the timestamps, retrieve and overwrite 
		 * the local one
		 */
		foreach($timestamps as $file){
			$file_name = $file->file_name;
			
			/* Create path if not exists*/
			$directories = explode("/", $file_name);
			
			//remove the last entry which infact is the file itself
			array_pop($directories);
			
			$path = join("/", $directories);
			
			if (!file_exists(Config::get('updater.root_path').$path)) {
				mkdir(Config::get('updater.root_path').$path, 0777, true);
			}
			/* End directory creation */
			
			$local_file = Config::get('updater.root_path').$file_name;	
			$temp_file = $local_file.'.temp';
			$api_request = "<client>
							<ip>".Config::get('updater.myIP')."</ip>
							<file_name>".$file_name."</file_name>
						</client>";
			
			/* Curl failures here are tolerated to the maximum as 
			 * each file is attempted several times.
			 */
			$attempts = 0;
			while($attempts < 10 && $ch = curl_init(Config::get('updater.files'))){
				$fp = fopen ($temp_file, 'w+');
				$attempts++;
				curl_setopt($ch, CURLOPT_POSTFIELDS, $api_request);
				curl_setopt($ch, CURLOPT_TIMEOUT, 50);
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/xml'));
				curl_exec($ch);
				fclose($fp);
				
				if(curl_errno($ch)){
					file_put_contents("update_log.txt", Date('Y-m-d H:i:s')."    ".curl_error($ch).PHP_EOL, FILE_APPEND);
					$errors = true;
					continue;
				}
				/* Perform decryption and validation */
				$c = base64_decode(file_get_contents($temp_file));
				$ivlen = openssl_cipher_iv_length($cipher_algorithm);
				$iv = substr($c, 0, $ivlen);
				$hmac = substr($c, $ivlen, $hash_length);
				$ciphertext_raw = substr($c, $ivlen+$hash_length);
				$calcmac = hash_hmac($hash_algorithm, $ciphertext_raw, $encryption_key, $as_binary=true);
				try{
					unlink($temp_file);
				}catch(Exception $ex){
					file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
				}
				finally{
					
				}
				
				/*Possible error, repeat. This will also cater for failed curl as $c will be empty*/
				if(is_bool($hmac)){
					$errors = true;
					continue;
				}
				
				/* If everything is OK, carry out write to local file */
				if (hash_equals($hmac, $calcmac))
				{
					try{
						file_put_contents($local_file,openssl_decrypt($ciphertext_raw, $cipher_algorithm, $encryption_key, $options=OPENSSL_RAW_DATA, $iv)); 
						DB::statement("insert into file_updates(file_name, checksum,date) select '".str_replace('/','//',$local_file) . "','" . $file->checksum . "','" . $file->date . "'");
						file_put_contents(str_replace("\\","/",Config::get('updater.root_path')).'public/updated_files.txt',str_replace("\\","/",$local_file));
						
						/* Ends the download loop for the current file */
						$attempts = 10;
						
						/* Marks success */
						$download_success++;
						if(preg_match("/\.(js|html)/i",$local_file))
							$js_html++;
					}catch(Exception $ex){
						$messagge = $ex->getMessage();
						file_put_contents("update_log.txt", Date('Y-m-d H:i:s')."    ".$ex->getMessage().PHP_EOL, FILE_APPEND);
						$errors = true;
					}
					finally{
						
					}
				}
			}
		}
		
		
		/* Usable for a while to allow a fresh loading of residences,tribes,countries. This section shall be dropped later */
		if(!preg_match('/performed_data_corrections/',file_get_contents(Config::get('updater.root_path').'public/updater.rem'))){
			file_put_contents(Config::get('updater.root_path').'public/counter.txt','...');
			file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','Setting tribes....');
			if(count(DB::select("select * from INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".env("DB_DATABASE")."' and TABLE_NAME = 'tbl_tribes'"))){
				$tribes = DB::select("select id from tbl_tribes where tribe_name = 'AKIE'");
				if(count($tribes) != 0 && $tribes[0]->id != 1){
					DB::statement('SET FOREIGN_KEY_CHECKS=0');
					DB::statement('TRUNCATE tbl_tribes');
					DB::statement('SET FOREIGN_KEY_CHECKS=1');
				}
			}
			
			file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','Setting list of Countries....');
			if(count(DB::select("select * from INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".env("DB_DATABASE")."' and TABLE_NAME = 'tbl_countries'"))){
				$countries = DB::select("select id from tbl_countries where country_name = 'AFGHANISTAN'");
				if(count($countries) != 0 && $countries[0]->id != 228){
					DB::statement('SET FOREIGN_KEY_CHECKS=0');
					DB::statement('TRUNCATE tbl_countries');
					DB::statement('SET FOREIGN_KEY_CHECKS=1');
				}
			}
			
				
			file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','Updating list of Regions....');
			if(count(DB::select("select * from INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".env("DB_DATABASE")."' and TABLE_NAME = 'tbl_regions'"))){
				$regions = DB::select("select region_name from tbl_regions where id = '2'");
				if(count($regions) == 0 || strtolower($regions[0]->region_name != 'arusha')){
					DB::statement('SET FOREIGN_KEY_CHECKS=0');
					DB::statement('TRUNCATE tbl_regions');
					DB::statement('SET FOREIGN_KEY_CHECKS=1');
				}
			}
			
			file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','Updating list of Councils....');
			if(count(DB::select("select * from INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".env("DB_DATABASE")."' and TABLE_NAME = 'tbl_councils'"))){
				$councils = DB::select("select council_name from tbl_councils where id = '4'");
				if(count($councils) == 0 || strtolower($councils[0]->council_name != 'arusha cc')){
					DB::statement('SET FOREIGN_KEY_CHECKS=0');
					DB::statement('TRUNCATE tbl_councils');
					DB::statement('SET FOREIGN_KEY_CHECKS=1');
				}
			}
			
			file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','Updating list of Residences....');
			if(count(DB::select("select * from INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".env("DB_DATABASE")."' and TABLE_NAME = 'tbl_residences'"))){
				$residences = DB::select("select residence_name from tbl_residences where  id = '7'");
				if(count($residences) == 0 || explode(",",$residences[0]->residence_name)[0] != 'Tindigani'){
					DB::statement('SET FOREIGN_KEY_CHECKS=0');
					DB::statement('TRUNCATE tbl_residences');
					DB::statement('SET FOREIGN_KEY_CHECKS=1');
				}
			}
			
			
			/* This section is added to clean any database 
			 * that inserted the records inadvertently when 
			 * those were incorrectly included in the sql_updates
			 * file in which the insert was made everytime a 
			 * client updates
			 */
			$tbl_body_systems = [
				["name" => 'History of Dizziness' , "category" => 'Central Nervous System'],
				["name" => 'History of fits' , "category" => 'Central Nervous System'],
				["name" => 'History of convulsion' , "category" => 'Central Nervous System'],
				["name" => 'History of loss of consciousness' , "category" => 'Central Nervous System'],
				["name" => 'History of headache' , "category" => 'Central Nervous System'],
				["name" => 'History of insomnia' , "category" => 'Central Nervous System'],
				["name" => 'History of tremor' , "category" => 'Central Nervous System'],
				["name" => 'History of blurred vision' , "category" => 'Central Nervous System'],
				["name" => 'History of deafness' , "category" => 'Central Nervous System'],
				["name" => 'History of visual disturbance' , "category" => 'Central Nervous System'],
				["name" => 'History of numbness' , "category" => 'Central Nervous System'],
				["name" => 'History of fever' , "category" => 'Central Nervous System'],
				["name" => 'NAD' , "category" => 'Central Nervous System'],
				["name" => 'History of cough' , "category" => 'Respiratory'],
				["name" => 'NAD' , "category" => 'Respiratory'],
				["name" => 'History of chest pain' , "category" => 'Respiratory'],
				["name" => 'History of difficulty in breathing' , "category" => 'Respiratory'],
				["name" => 'History of wheezing' , "category" => 'Respiratory'],
				["name" => 'History of heartbeat awareness' , "category" => 'Cardiovascular'],
				["name" => 'NAD' , "category" => 'Cardiovascular'],
				["name" => 'History of central chest pain' , "category" => 'Cardiovascular'],
				["name" => 'History of paroxysmal nocturnal dyspnea' , "category" => 'Cardiovascular'],
				["name" => 'History of orthopnea' , "category" => 'Cardiovascular'],
				["name" => 'History of swelling of the limbs' , "category" => 'Cardiovascular'],
				["name" => 'History of intermittent claudication' , "category" => 'Cardiovascular'],
				["name" => 'History of easy fatigability' , "category" => 'Cardiovascular'],
				["name" => 'NAD' , "category" => 'Gastrointerstinal'],
				["name" => 'History of abdominal pain (site)' , "category" => 'Gastrointerstinal'],
				["name" => 'History of abdominal pain ( onset )' , "category" => 'Gastrointerstinal'],
				["name" => 'History of abdominal pain (nature )' , "category" => 'Gastrointerstinal'],
				["name" => 'History of abdominal pain ( worsen )' , "category" => 'Gastrointerstinal'],
				["name" => 'History of abdominal pain (radiating)' , "category" => 'Gastrointerstinal'],
				["name" => 'History of vomitting (projectile)' , "category" => 'Gastrointerstinal'],
				["name" => 'History of vomitting ( non projectile)' , "category" => 'Gastrointerstinal'],
				["name" => 'History of vomitting ( contents)' , "category" => 'Gastrointerstinal'],
				["name" => 'History of vomitting ( colored )' , "category" => 'Gastrointerstinal'],
				["name" => 'History of vomitting ( smell )' , "category" => 'Gastrointerstinal'],
				["name" => 'History of diarrhea ( frequently )' , "category" => 'Gastrointerstinal'],
				["name" => 'History of diarrhea ( blood/mucous )' , "category" => 'Gastrointerstinal'], 
				["name" => 'History of difficult in swallowing' , "category" => 'Gastrointerstinal'], 
				["name" => 'History of painfully swallowing' , "category" => 'Gastrointerstinal'], 
				["name" => 'History of swelling of abdomen' , "category" => 'Gastrointerstinal'], 
				["name" => 'History of rectal bleeding' , "category" => 'Gastrointerstinal'], 
				["name" => 'History of jaundice / yellowish color' , "category" => 'Gastrointerstinal'], 
				["name" => 'History of loss of appetite' , "category" => 'Gastrointerstinal'], 
				["name" => 'History of loss of weight' , "category" => 'Gastrointerstinal'], 
				["name" => 'History of loss of constipation' , "category" => 'Gastrointerstinal'], 
				["name" => 'History of loss of heart burn' , "category" => 'Gastrointerstinal'], 
				["name" => 'History of loss of nausea' , "category" => 'Gastrointerstinal'], 
				["name" => 'History of loss of excessive flatus' , "category" => 'Gastrointerstinal'], 
				["name" => 'History of loss of tenesmus' , "category" => 'Gastrointerstinal'], 
				["name" => 'NAD' , "category" => 'Genitourinary'], 
				["name" => 'History of lower abdominal or lumbosacral pain' , "category" => 'Genitourinary'], 
				["name" => 'History of burning sensation on micturition' , "category" => 'Genitourinary'], 
				["name" => 'History of painful micturition' , "category" => 'Genitourinary'], 
				["name" => 'History of abnormal varginal discharge' , "category" => 'Genitourinary'], 
				["name" => 'History of genital ulcer' , "category" => 'Genitourinary'], 
				["name" => 'History of genital swelling' , "category" => 'Genitourinary'], 
				["name" => 'History of urethral discharge' , "category" => 'Genitourinary'], 
				["name" => 'NAD' , "category" => 'Musculoskeletal'], 
				["name" => 'History of joint pain' , "category" => 'Musculoskeletal'], 
				["name" => 'History of joint swelling' , "category" => 'Musculoskeletal'], 
				["name" => 'History of bone pain' , "category" => 'Musculoskeletal'], 
				["name" => 'History of muscle pain' , "category" => 'Musculoskeletal'], 
				["name" => 'History of general body malaise' , "category" => 'Musculoskeletal'], 
				["name" => 'History of joint stiffness/limitation range of movement' , "category" => 'Musculoskeletal'], 
				["name" => 'NAD' , "category" => 'Endocrine'], 
				["name" => 'History of heat in tolerance' , "category" => 'Endocrine'], 
				["name" => 'History of food in tolerance' , "category" => 'Endocrine'], 
				["name" => 'History of sweating' , "category" => 'Endocrine'], 
				["name" => 'History of easily irritable' , "category" => 'Endocrine'], 
				["name" => 'History of anxiety' , "category" => 'Endocrine'], 
				["name" => 'NAD' , "category" => 'ENT'],
			];
			
			file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','Cleaning up list of body systems....');
			foreach($tbl_body_systems as $system){
				$exists = DB::select("select min(id) id from tbl_body_systems where name='".$system['name']."' and category='".$system['category']."'");
				if(count($exists) && !empty($exists[0]->id))
					DB::statement("delete from tbl_body_systems where name='".$system['name']."' and category='".$system['category']."' and id <>".$exists[0]->id);
				else
					DB::statement("insert into tbl_body_systems(name,category,created_at,updated_at) select '".$system['name']."','".$system['category']."','2017-12-11 11:39:41','2017-12-11 11:39:41'");
			}
			
			$tracer_medicines_table = DB::select("select column_name from INFORMATION_SCHEMA.columns where table_schema='".env('DB_DATABASE')."' and table_name='tbl_tracer_medicines'");
			if(count($tracer_medicines_table) > 0 && $tracer_medicines_table[1]->column_name != 'item_name'){
				DB::statement('SET FOREIGN_KEY_CHECKS=0');
				DB::statement("drop table tbl_tracer_medicines");
				DB::statement("
								CREATE TABLE IF NOT EXISTS `tbl_tracer_medicines` (
								  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
								  `item_name` varchar(250) NOT NULL,
								  `status` tinyint NOT NULL,
								  `created_at` timestamp NULL DEFAULT NULL,
								  `updated_at` timestamp NULL DEFAULT NULL,
								  PRIMARY KEY (`id`)
								);
							");
				$tracer_medicines = array(
					['id' => '1', 'item_name' => 'DPT + HepB/ HiB vaccine for immunization', 'status' => '0' ],
					['id' => '2', 'item_name' => 'Vidonge vya ALU vya kumeza', 'status' => '0' ],
					['id' => '3', 'item_name' => 'Amoxycillin/ Cotrimoxazole ya maji', 'status' => '0' ],
					['id' => '4', 'item_name' => 'Amoxycillin/ Cotrimoxazole ya vidonge', 'status' => '0' ],
					['id' => '5', 'item_name' => 'Dawa za vidonge za minyoo Albendazole au Mebendazole', 'status' => '0' ],
					['id' => '6', 'item_name' => 'Dawa ya kuhara ya kuchanganya na maji (ORS)', 'status' => '0' ],
					['id' => '7', 'item_name' => 'Sindano ya Ergometrine au Oxytocin au Vidonge vya Misoprostol', 'status' => '0' ],
					['id' => '8', 'item_name' => 'Dawa ya sindano ya Uzazi wa mapngo (Depo)', 'status' => '0' ],
					['id' => '9', 'item_name' => 'Maji ya mishipa (Dextrose 5% au Sodium chloride+Dextrose)', 'status' => '0' ],
					['id' => '10', 'item_name' => 'Mabomba ya sindano kwa matumizi ya mara moja(Disposable)', 'status' => '0' ],
					['id' => '11', 'item_name' => 'Kipimo cha malaria cha haraka (MRDT) au vifaa vya kupimia katika Hadubini', 'status' => '0' ],
					['id' => '12', 'item_name' => 'Magnesium Sulphate Sindano', 'status' => '0' ],
					['id' => '13', 'item_name' => 'Zinc sulphate Vidonge', 'status' => '0' ],
					['id' => '14', 'item_name' => 'Paracetamol Tablets', 'status' => '0' ],
					['id' => '15', 'item_name' => 'Benzyl Penicilline Injection', 'status' => '0' ],
					['id' => '16', 'item_name' => 'Ferrous + Folic Acid Tablets', 'status' => '0' ],
					['id' => '17', 'item_name' => 'Metronidazole Tablets', 'status' => '0' ],
					['id' => '18', 'item_name' => 'CombineOral Contraceptives', 'status' => '0' ],
					['id' => '19', 'item_name' => 'Catgut Sutures', 'status' => '0' ],
					['id' => '20', 'item_name' => 'Nevirapine Oral solution', 'status' => '0' ],
					['id' => '21', 'item_name' => 'Tenofovir 300mg+Lamivudine 300mg+Efavirenz 600mg Tablets', 'status' => '0' ],
					['id' => '22', 'item_name' => 'Efavirenz 600mg Tablets', 'status' => '0' ],
					['id' => '23', 'item_name' => 'Zidovudine 60mg+Lamivudine 30mg+Nevirapine 50mg Tablets', 'status' => '0' ],
					['id' => '24', 'item_name' => 'UNIGOLD HIV 1/2', 'status' => '0' ],
					['id' => '25', 'item_name' => 'Determine HIV 1&2 / Determine/SD Bioline', 'status' => '0' ],
					['id' => '26', 'item_name' => 'FACS Count reagent', 'status' => '0' ],
					['id' => '27', 'item_name' => 'DBS', 'status' => '0' ],
					['id' => '28', 'item_name' => 'RHZE Rifampicin 150mg/Isoniazide 75mg/Pyrazinamide/Etdambutol Tablets', 'status' => '0' ],
					['id' => '29', 'item_name' => 'RH Rifampicin 150MG/Isoniazide 75mg Tablets', 'status' => '0' ],
					['id' => '30', 'item_name' => 'Sulphadoxine+pyrimetdamine tablets', 'status' => '0' ]
				);
				
				foreach($tracer_medicines as $tracer)
					if(!\App\Pharmacy\Tbl_tracer_medicine::find($tracer['id']))
						\App\Pharmacy\Tbl_tracer_medicine::create($tracer);
			}
			
			$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
			$contents->performed_data_corrections = true;
			file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
		}
		
		
		//Run any new sql update files downloaded
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','^Warning for long process...Running General SQL Update scripts....');
	
		//fix not-a-view error. Not that, any real views dropped here
		//will be re-created on the create-view call made at the end
		//WARNING: any view created through sql_updates 
		//MUST ALSO BE DEFINED IN THE views class!!!
		$incorrect_tables = DB::select("SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE Table_schema = '".env('DB_DATABASE'). "' AND Table_Name LIKE 'vw_%'");
		foreach($incorrect_tables as $entry){
			DB::statement("DROP VIEW IF EXISTS `". $entry->table_name ."`");
			DB::statement("DROP TABLE IF EXISTS `". $entry->table_name ."`");
			file_put_contents('views.txt', $entry->table_name.PHP_EOL, FILE_APPEND);
		}
		
		$dir = new \DirectoryIterator(dirname(Config::get('updater.root_path').'sql_updates/sql_updates'));
		$counter = 0;
		$total_updates = iterator_count($dir);
		
		file_put_contents('runned_scripts.txt', PHP_EOL . Date("Y-m-d H:i:s").PHP_EOL . "====BEGIN====".PHP_EOL, FILE_APPEND);
		
		file_put_contents(Config::get("updater.root_path")."run_sql_updates_manually.bat", '');
		foreach ($dir as $fileinfo) {
			++$counter;
			if ($fileinfo->isFile()) {
				//skip older ones
				if(new \DateTime(date ("Y-m-d H:i:s",filemtime($fileinfo->getPathname()))) < $last_update_time)
					continue;
				
				try{
					$command = Config::get("updater.mysql")
								." --user=". env('DB_USERNAME')
								." --password=" . env('DB_PASSWORD') 
								." --host=" . env('DB_HOST') 
								." " . env('DB_DATABASE')
								." < \"" . $fileinfo->getPathname() ."\" 2>&1";
					file_put_contents(
						Config::get("updater.root_path")."run_sql_updates_manually.bat", Config::get("updater.mysql")." --user=".env('DB_USERNAME')." --password=".env('DB_PASSWORD')." --host=".env('DB_HOST')." ".env('DB_DATABASE')." < \"".$fileinfo->getPathname()."\"".PHP_EOL , FILE_APPEND);
					$output = array("Update_date"=>Date('Y-m-d H:i:s'), "file"=> $fileinfo->getPathname());
					exec($command, $output, $result);
					if(isset($output['0']) && strpos($output['0'],'RROR') >0)
						file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .print_r($output,TRUE).PHP_EOL, FILE_APPEND);
					else
						file_put_contents('runned_scripts.txt',$command.PHP_EOL, FILE_APPEND);
				}catch(Exception $ex){
					file_put_contents("update_log.txt", Date('Y-m-d H:i:s')."    ".$ex->getMessage().PHP_EOL, FILE_APPEND);
				}
				finally{
					
				}
			}
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', (int)(($counter*100)/$total_updates)."&percnt;");
		}
		file_put_contents('runned_scripts.txt', PHP_EOL . Date("Y-m-d H:i:s").PHP_EOL . "====END====".PHP_EOL , FILE_APPEND);
		
		
		//execute user views
		try{
			$instance = new \App\Http\Controllers\admin\stateController();
			$instance->userView($request->facility_id);
		}catch(Exception $ex){
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
		}
		finally{
			
		}
		
		$message .= "<hr /><br /><b>$download_success/$all_files files updated.<br /><hr />".($js_html !=0 ? "<span style='color:red'>Please remember to clear BROWSER CACHE on user PCs</span><br /><hr />" : "");
	}else
		$message = "<hr />Process completed successfully.<br /><b>No new files detected.</b><hr />";		
	
	
	$request['notification'] = 1;
	$request['tag'] = $tag;
	$end_update = $tag = JSON_decode($this->notify($request),true);
	$end_update = $end_update['date'];
	/*Marks the end of file update */
	DB::statement("insert into file_updates(file_name,checksum,date) select 'End Update',NULL,current_timestamp");
	if(isset($errors) && $download_success != $all_files){
		/*
		 * Blind assumption that none of the downloads succeeded and 
		 * therefore re-attempt will download the entire set again. 
		 * This is a lazy programming to avoid tracking success/failure of 
		 * individual files
		 */
		DB::statement("delete from file_updates where id > '".$last_update[0]->id."'");
		
		$message = "<hr /><b>$download_success/$all_files files updated.<br /><hr /><span style='color:red'>Some errors were encountered during the update process, some were successfully fixed. You may repeat the process with a good network connection if the behavior of the system is unexpected after the update.</span><br /><hr />".($js_html !=0 ? "<span style='color:red'>Please remember to clear BROWSER CACHE on user PCs</span><br /><hr />" : "");
	}		
		
		
	/**********************HOUSE KEEPING BEGINS HERE********************************/
	file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','^The following activities does not require internet connection except for the synchonization part. If your internet disconnect at this moment, remember to manually [SYNC DATABASE WITH CENTRAL SERVER]');
	
	//For some bizzare reasons, MariaDB is automatically setting 
	//"on update CURRENT_TIMESTAMP" on the "created_at" field of some tables created 
	//by direct sql scripts. The reason identified is the column ordering--should 
	//have created the updated_at column first for this effect to be OK. 
	//This may have an unintended effect on transaction tables
	//This is a reset strategy 
	/*if(!preg_match('/performed_timestamp_corrections/', file_get_contents(Config::get('updater.root_path').'public/updater.rem'))){
		try{
			file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','^Warning for long process...Fixing incorrect table timestamps....');
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "0&percnt;");
			DB::statement("SET @created_at_message = '".env('DB_DATABASE')."'");
			DB::statement("CALL database_maintenance_timestamps_created_at(@created_at_message)");
			$created_at_response = DB::select("SELECT @created_at_message");
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "67&percnt;");
			
			DB::statement("SET @updated_at_message = '".env('DB_DATABASE')."'");
			DB::statement("CALL database_maintenance_timestamps_created_at(@updated_at_message)");
			$updated_at_response = DB::select("SELECT @updated_at_message");
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "98&percnt;");
			
			if($created_at_response[0]->{'@created_at_message'} !== 0 && $updated_at_response[0]->{'@updated_at_message'} !== 0){
				$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
				$contents->performed_timestamp_corrections = true;
				file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
			}
		}catch(Exception $ex){
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
		}
	}
	*/
	//Correct item mappings that were incorrectly mapped in the initial installer releases
	if(!preg_match('/performed_item_mappings/', file_get_contents(Config::get('updater.root_path').'public/updater.rem'))){
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','^Warning for long process...Correcting default item list mappings....');
		file_put_contents(Config::get('updater.root_path').'public/counter.txt', "0&percnt;");
		try{
			for($item_id = 1; $item_id <= 882; $item_id++){
				if($item_id == 1)
					DB::statement("update tbl_item_type_mappeds set item_category='CONSULTATION' where item_id='".$item_id."'");
				elseif($item_id == 2)
					DB::statement("update tbl_item_type_mappeds set item_category='MEDICAL EXAMINATION' where item_id='".$item_id."'");
				elseif($item_id >= 3 && $item_id <= 228 || $item_id >= 590 && $item_id <= 882)
					DB::statement("update tbl_item_type_mappeds set item_category='MEDICATION' where item_id='".$item_id."'");
				elseif($item_id > 228 && $item_id < 590)
					DB::statement("update tbl_item_type_mappeds set item_category='MEDICAL SUPPLIES' where item_id='".$item_id."'");
				file_put_contents(Config::get('updater.root_path').'public/counter.txt', (int)(($item_id)*100/882)."&percnt;");
			}
			
			$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
			$contents->performed_item_mappings = true;
			file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
		}catch(Exception $ex){
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
		}
		finally{
			
		}
	}

	
	/* Update facilities list. This allows the correct HFR code to be inserted for all clients */
	/*if(!preg_match('/performed_facility_list_corrections/',file_get_contents(Config::get('updater.root_path').'public/updater.rem'))){
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','^Warning for long process...Updating facilities\' list....');
		$old_list = DB::select("select facility_name facility from tbl_facilities limit 1 offset 1");
		//aar is the first HF on the HFR list imported here. This is used for the check. Remember that 		
		if(count($old_list) > 0 && strtolower($old_list[0]->facility) <> "aar"){
			//determine the code of the facility in use
			$old_code = Config::get("updater.hfr");
			
			// Run the next code block only if the existing facility_code can be located in HFR
			if(\App\Http\Controllers\Integrations\HFR\HFR::checkFacilityExistence($old_code) === true){
				$old_facility = DB::select("select * from tbl_facilities where id='".$request->facility_id."'")[0];
								
				//Ensure the dash is ignored first to harmonize typos
				$old_code = preg_replace("/[_-]/","",$old_code);
				
				//take backup of the existing rows
				$command = Config::get("updater.mysqldump")
							." --no-create-info "
							." --user=". env('DB_USERNAME')
							." --password=" . env('DB_PASSWORD') 
							." --host=" . env('DB_HOST') 
							." " . env('DB_DATABASE')
							." tbl_facilities >  \"" . Config::get("updater.root_path")."public/sync/tbl_facilities_dump.sql\"";
				exec($command);
				//truncate the table and insert the new list
				DB::statement("SET FOREIGN_KEY_CHECKS = 0");
				DB::statement("TRUNCATE tbl_facilities");
				$command = Config::get("updater.mysql")
							." --user=". env('DB_USERNAME')
							." --password=" . env('DB_PASSWORD') 
							." --host=" . env('DB_HOST') 
							." " . env('DB_DATABASE')
							." < \"" . Config::get("updater.root_path")."public/sync/tbl_facilities.sql\"" 
							." 2>&1";
				
				$output = array("Update_date"=>Date('Y-m-d H:i:s'), "file"=> Config::get("updater.root_path")."public/sync/tbl_facilities.sql");
				exec($command, $output, $result);
				
				//On encountering error, bring back the backup to be safe
				if(isset($output['0']) && strpos($output['0'],'RROR') >0){
					file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL . PHP_EOL .print_r($output,TRUE).PHP_EOL, FILE_APPEND);
					$command = Config::get("updater.mysqldump")
							." --user=". env('DB_USERNAME')
							." --password=" . env('DB_PASSWORD') 
							." --host=" . env('DB_HOST') 
							." " . env('DB_DATABASE')
							." <  \"" . Config::get("updater.root_path")."public/sync/tbl_facilities_dump.sql\"";
					exec($command);
				}else{
					// If no errors encountered, locate the id of the old facility in the new list
					$new_id = DB::select("select id from tbl_facilities where replace(facility_code,'-','') = '$old_code'");
					
					// Critical, it must be found or else we cant continue
					if(count($new_id)>0 && $new_id[0]->id <> ''){
						$request['facility_id'] = $new_id[0]->id;
						
						// Update the facility_id column on all dependant tables
						$dependant_tables = DB::select("select  TABLE_NAME, COLUMN_NAME from INFORMATION_SCHEMA.KEY_COLUMN_USAGE where TABLE_SCHEMA='".env("DB_DATABASE")."' and REFERENCED_TABLE_SCHEMA='".env("DB_DATABASE")."' AND REFERENCED_TABLE_NAME='tbl_facilities'");
						foreach($dependant_tables as $table)
							DB::statement("update ".$table->TABLE_NAME." set ".$table->COLUMN_NAME."=".$new_id[0]->id." where ".$table->COLUMN_NAME."=".$request->facility_id);
						
						//set the original user defined attributes
						DB::table("tbl_facilities")
							->where("id",$new_id[0]->id)
							->update([
									"address"=>$old_facility->address,
									"email"=>$old_facility->email,
									"mobile_number"=>$old_facility->mobile_number,
								]);
						$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
						$contents->performed_facility_list_corrections = true;
						file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
					}else{//bring back the backup to be safe if the new facility id could not be located
						DB::statement("TRUNCATE tbl_facilities");
						$command = Config::get("updater.mysqldump")
								." --user=". env('DB_USERNAME')
								." --password=" . env('DB_PASSWORD') 
								." --host=" . env('DB_HOST') 
								." " . env('DB_DATABASE')
								." <  \"" . Config::get("updater.root_path")."public/sync/tbl_facilities_dump.sql\"";
						exec($command);
					}
				}
			}
			DB::statement('SET FOREIGN_KEY_CHECKS=1');
		}
	}*/
	
	//TEMPORARY: 
	/* Attempt to turn table keys to the partitioned key space where not yet done */
	/*if(!preg_match('/performed_key_space_assignment/',file_get_contents(Config::get('updater.root_path').'public/updater.rem'))){
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt', '^Warning for long process...Assigning partitioned table key space');
		
		//proceed if the key range for the facility is set
		$facility_code = DB::select("select facility_code from tbl_facilities where id='".$request->facility_id."'")[0]->facility_code;
		$ch = curl_init(Config::get('updater.key_space_range'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "<client><hfr>$facility_code</hfr></client>");
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/xml'));
		$data = curl_exec($ch);
		
		if(!curl_errno($ch)){
			$key_space = JSON_decode($data);
			if($key_space->has_key_space){
				//If not yet, perform the operation. 
				file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','Determining database key space....');
				
				//Firstly, a backup is taken
				$db_backup = Config::get('updater.root_path').env('DB_DATABASE').'-'.Date("Y-m-d his").'.sql';
				file_put_contents(Config::get('updater.root_path').'public/updated_files.txt',"Taking database backup to $db_backup....");
				$command = Config::get("sync.mysqldump")
								." --user=". env('DB_USERNAME')
								." --password=" . env('DB_PASSWORD') 
								." --host=" . env('DB_HOST') 
								." " . env('DB_DATABASE')
								." > \"" . $db_backup ."\" 2>&1";
					
				$output = array("Update_date"=>Date('Y-m-d H:i:s'), "file"=> $db_backup);
				exec($command, $output, $result);
				if(isset($output['0']) && strpos($output['0'],'RROR') >0){//errors, abort
					file_put_contents("update_log.txt", Date('Y-m-d H:i:s').'  ---  DATABASE KEYS CONVERSION' . PHP_EOL .print_r($output,TRUE).PHP_EOL, FILE_APPEND);
					$message .= "An error occured while attempting to backup your database to $db_backup.<br /> <span stype='color:red;'>Key space conversion can not proceed.</span>";
				}else{
					file_put_contents(Config::get('updater.root_path').'public/updated_files.txt', 'Upgrading reference options....');
					
					//first make all FOREIGN KEYS ON UPDATE CASCADE
					/*$references = DB::select("SELECT usages.TABLE_NAME, usages.COLUMN_NAME, usages.REFERENCED_TABLE_NAME, usages.REFERENCED_COLUMN_NAME, usages.CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE usages JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS constraints ON usages.TABLE_SCHEMA = constraints.CONSTRAINT_SCHEMA AND usages.CONSTRAINT_NAME = constraints.CONSTRAINT_NAME AND constraints.CONSTRAINT_TYPE = 'FOREIGN KEY' WHERE usages.TABLE_SCHEMA='".env('DB_DATABASE')."'");
					
					DB::statement("SET FOREIGN_KEY_CHECKS = 0");
					$counter = 0;
					$total_references = count($references);
					foreach($references as $reference){
						$counter++;
						DB::statement("ALTER TABLE ".$reference->TABLE_NAME." DROP FOREIGN KEY ".$reference->CONSTRAINT_NAME);
						DB::statement("ALTER TABLE ".$reference->TABLE_NAME." ADD FOREIGN KEY ".$reference->CONSTRAINT_NAME."(".$reference->COLUMN_NAME.") REFERENCES ".$reference->REFERENCED_TABLE_NAME."(".$reference->REFERENCED_COLUMN_NAME.") ON UPDATE CASCADE");
						file_put_contents(Config::get('updater.root_path').'public/counter.txt', (int)($counter*100/$total_references).'&percnt;');
					}
					DB::statement('SET FOREIGN_KEY_CHECKS=1');
					*/
					/*
					//after modifying all reference keys involved, now set the new key range
					//at this point, the references are now going to update themselves.
					//Note that here we target only specific pre-defined list of tables
					file_put_contents(Config::get('updater.root_path').'public/updated_files.txt', 'Applying key space....');
					$tables = simplexml_load_file(Config::get('updater.root_path').'public/sync/tables.xml');
					$counter = 0;
					$total_tables = count($tables);
					DB::statement("SET FOREIGN_KEY_CHECKS = 0");
					foreach($tables as $table){
						DB::statement("alter table ".$table->attributes()->table_name." add column copy_id int");
						DB::statement("update ".$table->attributes()->table_name." set copy_id = id");
						DB::statement("update ".$table->attributes()->table_name." set id = id+".$key_space->lower_id);
						$increment = DB::select("select ifnull(max(id),0)+1 as next from ".$table->attributes()->table_name)[0]->next;
						DB::statement("alter table ".$table->attributes()->table_name." auto_increment = $increment");
						$counter++;
						$dependant_tables = DB::select("select  TABLE_NAME, COLUMN_NAME,REFERENCED_COLUMN_NAME, REFERENCED_TABLE_NAME from INFORMATION_SCHEMA.KEY_COLUMN_USAGE where TABLE_SCHEMA='".env("DB_DATABASE")."' and REFERENCED_TABLE_SCHEMA='".env("DB_DATABASE")."' AND REFERENCED_TABLE_NAME='".$table->attributes()->table_name."'");
						foreach($dependant_tables as $dependant){
							If(strtolower($dependant->REFERENCED_COLUMN_NAME) != 'id')
								continue;
							DB::statement("update ".$dependant->TABLE_NAME." set ".$dependant->COLUMN_NAME."=(select id from ".$table->attributes()->table_name." where copy_id=".$dependant->TABLE_NAME.".".$dependant->COLUMN_NAME.")");
						}
						
						file_put_contents(Config::get('updater.root_path').'public/counter.txt', (int)($counter*100/$total_tables).'&percnt;');
					}
					DB::statement("SET FOREIGN_KEY_CHECKS = 1");
					
					$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
					$contents->performed_key_space_assignment = true;
					file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
				}
			}
		}
		curl_close($ch);
	}*/
	
	
	//TEMPORARY::Remove duplicate medical record numbers
	/*if(!preg_match('/performed_cleansing_of_mrn/',file_get_contents(Config::get('updater.root_path').'public/updater.rem'))){
		try{
			file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','^Warning for long process....Correcting duplicate medical record numbers....');
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "0&percnt;");
			//take backup first. This is serious
			$path = Date("Ymdhis").".sql";
			$command = Config::get("updater.mysqldump")
								." --user=". env('DB_USERNAME')
								." --password=" . env('DB_PASSWORD') 
								." --host=" . env('DB_HOST') 
								." " . env('DB_DATABASE')
								." > " . $path ." 2>&1";
			
			$output = array("Update_date"=>Date('Y-m-d H:i:s'), "file"=> $fileinfo->getPathname());
			exec($command, $output, $result);
			if(isset($output['0']) && strpos($output['0'],'RROR') >0)
				file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .print_r($output,TRUE).PHP_EOL, FILE_APPEND);
			else{
				file_put_contents(Config::get('updater.root_path').'public/counter.txt', "33&percnt;");
				DB::statement("SET @mrn_message = '".env('DB_DATABASE')."'");
				DB::statement("CALL database_maintenance_remove_duplicate_mrns(@mrn_message)");
				$mrn_response = DB::select("SELECT @mrn_message");
				file_put_contents(Config::get('updater.root_path').'public/counter.txt', "98&percnt;");
				
				if($mrn_response[0]->{'@mrn_message'} !== 0){
					$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
					$contents->performed_cleansing_of_mrn = true;
					file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
				}
				file_put_contents(Config::get('updater.root_path').'public/counter.txt', "100&percnt;");
			}
		}catch(Exception $ex){
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
		}
		finally{
			
		}
	}*/
	
	//TEMPORARY: code for updating  ICD10 MAPPINGS
	/*if(!preg_match('/applied_mtuha_diagnosis_descriptions/',file_get_contents(Config::get('updater.root_path').'public/updater.rem'))){
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','^Warning for long process...Creating PHC MTUHA diagnoses....');
		file_put_contents(Config::get('updater.root_path').'public/counter.txt', "0&percnt;");
		$phc_diagnoses = array(
				["description"=>"ACUTE FLACCID PARALYSIS", "code"=>"OP4"],
				["description"=>"DYSENTERY", "code"=>"OP6"],
				["description"=>"MENINGITIS", "code"=>"OP8"],
				["description"=>"NEONATAL TETANUS", "code"=>"OP9"],
				["description"=>"INFLUENZA", "code"=>"OP13"],
				["description"=>"TYPHOID", "code"=>"OP14"],
				["description"=>"TRYPANOSOMIASIS", "code"=>"OP17"],
				["description"=>"VIRAL HAEMORRHAGIC FEVERS", "code"=>"OP18"],
				["description"=>"DIARRHEA WITH NO DEHYDRATION", "code"=>"OP19"],
				["description"=>"DIARRHEA WITH SOME DEHYDRATION", "code"=>"OP20"],
				["description"=>"DIARRHEA WITH SEVERE DEHYDRATION", "code"=>"OP21"],
				["description"=>"SCHISTOSOMIASIS", "code"=>"OP22"],
				["description"=>"MALARIA BS POSITIVE", "code"=>"OP23.1"],
				["description"=>"MALARIA mRDT POSITIVE", "code"=>"OP23.2"],
				["description"=>"MALARIA CLINICAL (NO TEST)", "code"=>"OP23.3"],
				["description"=>"MALARIA IN PREGNANCY", "code"=>"OP23.4"],
				["description"=>"STI GENITAL DISCHARGE SYNDROME (GDS)", "code"=>"OP24"],
				["description"=>"STI GENITAL ULCER DISEASES (GUD)", "code"=>"OP25"],
				["description"=>"STI PELVIC INFLAMMATORY DISEASES (PID)", "code"=>"OP26"],
				["description"=>"SEXUALLY TRANSMITTED INFECTIONS, OTHER", "code"=>"OP27"],
				["description"=>"TUBERCULOSIS", "code"=>"OP28"],
				["description"=>"LEPROSY", "code"=>"OP29"],
				["description"=>"INTESTINAL WORMS", "code"=>"OP30"],
				["description"=>"ANAEMIA, MILD/MODERATE", "code"=>"OP31"],
				["description"=>"ANAEMIA, SEVERE", "code"=>"OP32"],
				["description"=>"SICKLE CELL DISEASE", "code"=>"OP33"],
				["description"=>"EAR INFECTION, ACUTE", "code"=>"OP34"],
				["description"=>"EAR INFECTION, CHRONIC", "code"=>"OP35"],
				["description"=>"EYE DISEASE, INFECTIOUS", "code"=>"OP36"],
				["description"=>"EYE DISEASE, NON-INFECTIOUS", "code"=>"OP37"],
				["description"=>"EYE DISEASE, INJURIES", "code"=>"OP38"],
				["description"=>"SKIN INFECTION, NON-FUNGAL", "code"=>"OP39"],
				["description"=>"SKIN INFECTION, FUNGAL", "code"=>"OP40"],
				["description"=>"SKIN DISEASES, NON-INFECTIOUS", "code"=>"OP41"],
				["description"=>"FUNGAL INFECTION, NON-SKIN", "code"=>"OP42"],
				["description"=>"NEONATAL SEPSIS", "code"=>"OP44"],
				["description"=>"LOW BIRTH WEIGHT AND PREMATURITY COMPLICATION", "code"=>"OP45"],
				["description"=>"PNEUMONIA, NON SEVERE", "code"=>"OP47"],
				["description"=>"PNEUMONIA, SEVERE", "code"=>"OP48"],
				["description"=>"UPPER RESPIRATORY INFECTIONS (PHARYNGITIS, TONSILLITIS, RHINITIS)", "code"=>"OP49"],
				["description"=>"CEREBRAL PALSY", "code"=>"OP50"],
				["description"=>"URINARY TRACT INFECTIONS (UTI)", "code"=>"OP51"],
				["description"=>"GYNAECOLOGICAL DISEASES, OTHER", "code"=>"OP52"],
				["description"=>"MARASMUS", "code"=>"OP54"],
				["description"=>"MODERATE MALNUTRITION", "code"=>"OP56"],
				["description"=>"OTHER NUTRITIONAL DISORDERS", "code"=>"OP58"],
				["description"=>"CARIES", "code"=>"OP59"],
				["description"=>"PERIODONTAL DISEASES", "code"=>"OP60"],
				["description"=>"DENTAL EMERGENCY CARE", "code"=>"OP61"],
				["description"=>"DENTAL CONDITIONS, OTHER", "code"=>"OP62"],
				["description"=>"FRACTURES/DISLOCATIONS", "code"=>"OP63"],
				["description"=>"BURN", "code"=>"OP64"],
				["description"=>"POISONING", "code"=>"OP65"],
				["description"=>"ROAD TRAFFIC ACCIDENTS", "code"=>"OP66"],
				["description"=>"PREGNANCY COMPLICATIONS", "code"=>"OP67"],
				["description"=>"SNAKE AND INSECT BITES", "code"=>"OP69"],
				["description"=>"ANIMAL BITE (SUSPECTED RABIES)", "code"=>"OP70"],
				["description"=>"ANIMAL BITE (NO SUSPECTED RABIES)", "code"=>"OP71"],
				["description"=>"EMERGENCIES, OTHER", "code"=>"OP72"],
				["description"=>"SURGICAL CONDITIONS, OTHER", "code"=>"OP73"],
				["description"=>"PSYCHOSES", "code"=>"OP75"],
				["description"=>"NEUROSIS", "code"=>"OP76"],
				["description"=>"SUBSTANCE ABUSE", "code"=>"OP77"],
				["description"=>"HYPERTENSION", "code"=>"OP78"],
				["description"=>"RHEUMATIC FEVER", "code"=>"OP79"],
				["description"=>"CARDIOVASCULAR DISEASES, OTHER", "code"=>"OP80"],
				["description"=>"BRONCHIAL ASTHMA", "code"=>"OP81"],
				["description"=>"PEPTIC ULCERS", "code"=>"OP82"],
				["description"=>"(GTI) GASTROINTESTINAL DISEASES, OTHER NON-INFECTIOUS", "code"=>"OP83"],
				["description"=>"DIABETES MELLITUS", "code"=>"OP84"],
				["description"=>"RHEUMATOID AND JOINT DISEASES", "code"=>"OP85"],
				["description"=>"THYROID DISEASES", "code"=>"OP86"],
				["description"=>"NEOPLASMS/CANCER", "code"=>"OP87"],
				["description"=>"ILL DEFINED SYMTOMS (NO DIAGNOSIS)", "code"=>"OP88"],

				["description"=>"DIARRHEA ACUTE (< 14 DAYS )", "code"=>"IP11"],
				["description"=>"DIARRHEA CHRONIC (>OR= 14 DAYS)", "code"=>"IP12"],
				["description"=>"ABORTION COMPLICATIONS", "code"=>"IP20"],
				["description"=>"HAEMATOLOGICAL DISORDER", "code"=>"IP32"],
				["description"=>"EAR DISEASES, NON-INFECTIOUS", "code"=>"IP40"],
				["description"=>"CARDIAC FAILURE", "code"=>"IP46"],
				["description"=>"HYPERTENSION, SEVERE", "code"=>"IP47"],
				["description"=>"OTHER FEBRILE ILLNESSES", "code"=>"IP48"],
				["description"=>"BRONCHIAL ASTHMA, SEVERE", "code"=>"IP50"],
				["description"=>"LIVER DISEASES, NON-INFECTIOUS", "code"=>"IP55"],
				["description"=>"ACUTE GLOMERULONEPHRITIS", "code"=>"IP59"],
				["description"=>"RENAL FAILURE", "code"=>"IP60"],
				["description"=>"SKIN INFECTIONS", "code"=>"IP61"],
				["description"=>"HIV INFECTION, SYMPTOMATIC", "code"=>"IP74"],
				["description"=>"CONGENITAL DISORDERS", "code"=>"IP75"],
				["description"=>"HEPATITIS", "code"=>"IP76"],
				["description"=>"SOIL TRANSMITTED HELMINTHES", "code"=>"IP78"],
				["description"=>"LYMPHATIC FILAIRIASIS", "code"=>"IP79"],

			);
		try{	
			$counter = 0;
			$total_diagnoses = count($phc_diagnoses);
			foreach ($phc_diagnoses as $diagnosis){
				//removes duplicates that may have sneaked in
				if(\App\ClinicalServices\Tbl_diagnosis_description::where('code',$diagnosis['code'])->count() > 1){
					$duplicates = \App\ClinicalServices\Tbl_diagnosis_description::where('code',$diagnosis['code'])->get();
					for($i = 1; $i < count($duplicates); $i++){
						\App\ClinicalServices\Tbl_diagnosis_detail::where('diagnosis_description_id',$duplicates[$i]->id)->update(["diagnosis_description_id"=>$duplicates[0]->id]);
						\App\ClinicalServices\Tbl_diagnosis_description::where('id',$duplicates[$i]->id)->delete();
					}
				}else
					\App\ClinicalServices\Tbl_diagnosis_description::create($diagnosis);
				file_put_contents(Config::get('updater.root_path').'public/counter.txt', (int)(++$counter*100/$total_diagnoses)."&percnt;");
			}
			
			$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
			$contents->applied_mtuha_diagnosis_descriptions = true;
			file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
		}catch(Exception $ex){
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
		}
		finally{
			
		}
	}*/
			
	/*
	//TEMPORARY: set backlog reports
	if(!preg_match('/performed_OPD_IPD_MTUHA_corrections/',file_get_contents(Config::get('updater.root_path').'public/updater.rem'))){
		try{
			file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','^Warning for long process....Correcting OPD/IPD MTUHA reports with new Mappings....');
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "0&percnt;");
			DB::statement("SET @opd_disease_message = '".$request->facility_id."'");
			DB::statement("CALL database_maintenance_generate_opd_disease_register(@opd_disease_message)");
			$opd_disease_response = DB::select("SELECT @opd_disease_message");
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "15&percnt;");
			
			DB::statement("SET @ipd_disease_message = '".$request->facility_id."'");
			DB::statement("CALL database_maintenance_generate_ipd_disease_register(@ipd_disease_message)");
			$ipd_disease_response = DB::select("SELECT @ipd_disease_message");
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "25&percnt;");
			
			DB::statement("SET @opd_attendance_message = '".$request->facility_id."'");
			DB::statement("CALL database_maintenance_generate_opd_attendance_register(@opd_attendance_message)");
			$opd_attendance_response = DB::select("SELECT @opd_attendance_message");
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "75&percnt;");
			
			DB::statement("SET @opd_reattendance_message = '".$request->facility_id."'");
			DB::statement("CALL database_maintenance_generate_opd_reattendance_register(@opd_reattendance_message)");
			$opd_reattendance_response = DB::select("SELECT @opd_reattendance_message");
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "98&percnt;");
			
			DB::statement("SET @ipd_admission_message = '".$request->facility_id."'");
			DB::statement("CALL database_maintenance_generate_admission_register(@ipd_admission_message)");
			$ipd_admission_response = DB::select("SELECT @ipd_admission_message");
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "98&percnt;");
			
			$response = $opd_disease_response[0]->{'@opd_disease_message'} !== 0 
						&& $ipd_disease_response[0]->{'@ipd_disease_message'} !== 0
						&& $opd_attendance_response[0]->{'@opd_attendance_message'} !== 0
						&& $opd_reattendance_response[0]->{'@opd_reattendance_message'} !== 0
						&& $ipd_admission_response[0]->{'@ipd_admission_message'} !== 0;
		
			if($response === True){
				$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
				$contents->performed_OPD_IPD_MTUHA_corrections = true;
				file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
			}
		}catch(Exception $ex){
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
		}
	}*/
	
	
	//TEMPORARY: code for updating  treacer medicine configurations
	if(!preg_match('/configured_tracer_medicines/',file_get_contents(Config::get('updater.root_path').'public/updater.rem'))){
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','^Warning for long process...Configuring Tracer Medicine lookup list....');
		file_put_contents(Config::get('updater.root_path').'public/counter.txt', "0&percnt;");
		
		$tracers = array(
					["tracer_medicine_id"=>1, "item_id"=>208],
					
					["tracer_medicine_id"=>3, "item_id"=>53],
					
					["tracer_medicine_id"=>4, "item_id"=>52],
					
					["tracer_medicine_id"=>5, "item_id"=>14],
					["tracer_medicine_id"=>5, "item_id"=>16],
					
					["tracer_medicine_id"=>6, "item_id"=>223],
					
					["tracer_medicine_id"=>7, "item_id"=>189],
					["tracer_medicine_id"=>7, "item_id"=>188],
					
					["tracer_medicine_id"=>8, "item_id"=>163],
					
					["tracer_medicine_id"=>9, "item_id"=>163],
					["tracer_medicine_id"=>9, "item_id"=>164],
					["tracer_medicine_id"=>9, "item_id"=>165],
					["tracer_medicine_id"=>9, "item_id"=>166],
					["tracer_medicine_id"=>9, "item_id"=>170],
					["tracer_medicine_id"=>9, "item_id"=>171],
					
					["tracer_medicine_id"=>10, "item_id"=>303],
					["tracer_medicine_id"=>10, "item_id"=>304],
					["tracer_medicine_id"=>10, "item_id"=>305],
										
					["tracer_medicine_id"=>12, "item_id"=>20],
					
					["tracer_medicine_id"=>13, "item_id"=>225],
					
					["tracer_medicine_id"=>14, "item_id"=>135],
					
					["tracer_medicine_id"=>15, "item_id"=>58],
					
					["tracer_medicine_id"=>16, "item_id"=>160],
					["tracer_medicine_id"=>16, "item_id"=>161],
					
					["tracer_medicine_id"=>17, "item_id"=>5],
										
					["tracer_medicine_id"=>19, "item_id"=>229],
					["tracer_medicine_id"=>19, "item_id"=>230],
					["tracer_medicine_id"=>19, "item_id"=>231],
					["tracer_medicine_id"=>19, "item_id"=>231],
					["tracer_medicine_id"=>19, "item_id"=>232],
					["tracer_medicine_id"=>19, "item_id"=>233],
					["tracer_medicine_id"=>19, "item_id"=>234],
					["tracer_medicine_id"=>19, "item_id"=>235],
					["tracer_medicine_id"=>19, "item_id"=>236],
					["tracer_medicine_id"=>19, "item_id"=>237],
					["tracer_medicine_id"=>19, "item_id"=>238],
					["tracer_medicine_id"=>19, "item_id"=>239],
					["tracer_medicine_id"=>19, "item_id"=>240],
					["tracer_medicine_id"=>19, "item_id"=>241],
					["tracer_medicine_id"=>19, "item_id"=>242],
					["tracer_medicine_id"=>19, "item_id"=>244],
					
					["tracer_medicine_id"=>30, "item_id"=>122],
				);
				
				$possible_matches = array(
					["tracer_medicine_id"=>2,"match"=>"%artemeter%LUMEFANTRIN%"],
					["tracer_medicine_id"=>3,"match"=>"%amox%lin%mg/%ml%"],
					["tracer_medicine_id"=>3,"match"=>"%COTRIMOXAZOLE%mg/%ml%"],
					["tracer_medicine_id"=>4,"match"=>"%amox%lin%cap%sule%mg%"],
					["tracer_medicine_id"=>4,"match"=>"%COTRIMOXAZOLE%mg"],
					["tracer_medicine_id"=>5,"match"=>"%bendazole%mg"],
					["tracer_medicine_id"=>6,"match"=>"%hydration%salt%ors%"],
					["tracer_medicine_id"=>7,"match"=>"%ox%tocin"],
					["tracer_medicine_id"=>7,"match"=>"%MISOPROSTOL%mg%"],
					["tracer_medicine_id"=>10,"match"=>"%syringe%10%ml%"],
					["tracer_medicine_id"=>10,"match"=>"%syringe%2%ml%"],
					["tracer_medicine_id"=>10,"match"=>"%syringe%5%ml%"],
					["tracer_medicine_id"=>12,"match"=>"%magnes%um%ml%"],
					["tracer_medicine_id"=>13,"match"=>"%zinc%phate%mg%"],
					["tracer_medicine_id"=>14,"match"=>"%paracetamol%mg%"],
					["tracer_medicine_id"=>15,"match"=>"%benz%penic%inj%"],
					["tracer_medicine_id"=>17,"match"=>"%metronidazole%mg%"],
				);
				
			foreach($possible_matches as $match){
				$found = DB::select("select t2.item_id from tbl_items t1 join tbl_item_type_mappeds t2 on t1.id = t2.item_id and t2.item_category in('medication','medical supplies') and t1.item_name like '".$match['match']."'");
				foreach($found as $t)
					array_push($tracers , array("tracer_medicine_id"=>$match['tracer_medicine_id'], "item_id"=>$t->item_id));
			}
		try{		
			DB::statement("truncate tbl_tracer_medicine_mappings");		
			foreach($tracers as $tracer){
				if(\App\Item_setups\Tbl_item::where("id",$tracer['item_id'])->count() === 0)
					continue;
				if(\App\Pharmacy\Tbl_tracer_medicine_mapping::where('tracer_medicine_id',$tracer['tracer_medicine_id'])
															->where('item_id', $tracer['item_id'])->count() == 0)
					\App\Pharmacy\Tbl_tracer_medicine_mapping::create($tracer);
			}
			
			$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
			$contents->configured_tracer_medicines = true;
			file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
		}catch(Exception $ex){
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
		}
		finally{
			
		}
	}
	
	file_put_contents(Config::get('updater.root_path').'public/updated_files.txt', "^Warning for a long process...Cleaning up your system folder...");
	file_put_contents(Config::get('updater.root_path').'public/counter.txt', "0&percnt;");
	$remember = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
	if(!isset($remember->cleaned_directory)){
		if($folderBackup($request)){//successfully made a backup copy
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "50&percnt;");
			$cleanDirectory();
		}
	}else
		$cleanDirectory();
	
	file_put_contents(Config::get('updater.root_path').'public/counter.txt', "100&percnt;");
	
	/* Finally, auto-load the app */
	try{
		//exec(Config::get('root_path').'composer dump-autoload');
	}catch(Exception $ex){
		file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
	}
	finally{
		
	}
	
	/*************************************END HOUSE KEEPING***********************/
	
	if($request->has('is_live') && $request->is_live == 1){
		//Take a backup of the client database to the central server.
		//this eventually cascades to dashboard data reporting
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','^Warning for long process...Synchronizing your database with the central server....');
		$request['internal_call'] = true;
		$request['ongoing_process'] = 'sync';
		$sync = new \App\Http\Controllers\Sync\Sync();	
		$response =  $sync->init($request->facility_id, $request);
		
		/*Reminder to set correct facility_code*/
		$response =  \App\Http\Controllers\Integrations\HFR\HFR::checkFacilityExistence(Config::get("updater.hfr"), true);
		if(is_array($response) && $response['status'] == false)
			$message .=$response['description'];
	}
	
	
	//downloads the facility gepg_configurations from the server
	file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','Downloading local gepg configuarations....');
	file_put_contents(Config::get('updater.root_path').'public/counter.txt', "...");
	$request = "<client>
					<FacilityCode>$facility_code</FacilityCode>
				</client>";
						
	$ch = curl_init("http://196.192.72.107/gepg/new/facility/configuration");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
	curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/xml'));
	$data = curl_exec($ch);
	if(!curl_errno($ch)){
		try{
			$configs = JSON_decode($data);
			if(count($configs) == 0){
				$message .= "<span style='color:red'>No GePG Configurations found</span><hr />";
			}else{
				$configs = $configs[0];
				
				$sql = "UPDATE gepg_accounts SET `GfsCode` = '$configs->GfsCode', SpCode = '$configs->SpCode', SubSpCode = '$configs->SubSpCode'";
				DB::statement($sql);
			}
		}catch(Exception $ex){
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
		}
		finally{
			
		}
	}
	curl_close($ch);
	
	/*Ensure production mode */
	/*try{
		$env = file_get_contents(Config::get('updater.root_path').".env");
		$env = str_ireplace("APP_DEBUG=true", "APP_DEBUG=false", $env);
		file_put_contents(Config::get('updater.root_path').".env", $env);
		Artisan::call('config:clear');
		Artisan::call('config:cache');
	}catch(Exception $ex){
		file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
	}
	finally{
		
	}*/
	
	/* The evil goto was implemented as a none tedious way of exiting the update script when necessary. */
	exit_updater:		
	{
		//TODO
	}
	
	///---
	$fixer = file_get_contents(Config::get('updater.root_path').'database/fix not a view error ---- open and read first -----.sql');
	$fixer = str_replace("\$pathcmd", str_replace("/", "\\", Config::get('updater.root_path')), $fixer);
	$fixer = str_replace("\$path", Config::get('updater.root_path'), $fixer);
	$fixer = str_replace("\$database", env('DB_DATABASE'), $fixer);
	$fixer = str_replace("\$password", env('DB_PASSWORD'), $fixer);
	$fixer = str_replace("\$user", env('DB_USERNAME'), $fixer);
	
	file_put_contents(Config::get('updater.root_path').'database/fix not a view error ---- open and read first -----.sql' , $fixer);
	///---
	
	//script inserted here to terminate the process if execution prematurely 
	//reaches here for any reason
	if(!$background_instance){
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','done');
		file_put_contents(Config::get('updater.root_path').'public/counter.txt','done');
	}
	
	if(!is_array($message) && !is_object($message)){
		$message .= "<span style='color:red'>GoTHOMIS v4 is now available. Please contact Mr. Baraka Samson(0762944697) for installation guide</span><hr />";
		//$message .= "<span style='color:red'>Please locate and delete these files to speed up your system [".substr(Config::get("updater.root_path"),0,strpos(Config::get("updater.root_path"),"htdocs"))."apache/logs/access.log"."],[".substr(Config::get("updater.root_path"),0,strpos(Config::get("updater.root_path"),"htdocs"))."apache/logs/error.log"."]</span><hr />";
	}