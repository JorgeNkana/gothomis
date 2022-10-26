<?php
/*****************************************************************
 * This file is used specifically to carryout initial update	 *
 * activities. This file must sit on the client machine on the   *
 * initial setup for the update to be started. As updates are 	 *
 * done, this file is replace by most recent one from the server *
 *****************************************************************/
namespace App\Http\Controllers\System_Updates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use DB;
use Artisan;


class Updater_Init extends Controller
{
	/*
	 * List of system file paths that shall be watched for update
	 * The root path is got from the config so that, paths are
	 * specified from there forward. Not the use of forward slash in
	 * paths											
	 */
    private $file_paths;
    
	/*
	 * List of file/folders found in the paths of $file_paths but should not be included
	 * in the list of files that subscribers download
	 */
	private $excludes;
	private $directory_map;
	private $marked_files;
	
	/* Crypto details */
	private $encryption_key ;
	private $cipher_algorithm;
	private $hash_algorithm;
	private $hash_length;
	
	private $expected_files;
	
	/*Constructor*/
	public function __construct(){
		ini_set('max_execution_time', -1);
		/*Set the updatable file paths*/
		$this->file_paths = array(
				Config::get('updater.root_path')."app/app",
				Config::get('updater.root_path')."app/Console/Console",
				Config::get('updater.root_path')."database/database",
				Config::get('updater.root_path')."config/config",
				Config::get('updater.root_path')."routes/routes",
				Config::get('updater.root_path')."resources/views/views",
				Config::get('updater.root_path')."public/views/views",
				Config::get('updater.root_path')."public/scripts/scripts",
				Config::get('updater.root_path')."public/css/css",
				Config::get('updater.root_path')."public/svg/svg",
				Config::get('updater.root_path')."public/sync/sync",
				Config::get('updater.root_path')."public/nhif_files/nhif_files",
				Config::get('updater.root_path')."sql_updates/sql_updates",
				Config::get('updater.root_path')."documents/documents",
				Config::get('updater.root_path')."public/bower_components/sweetalert2/dist/dist",
				Config::get('updater.root_path')."public/bower_components/angular-material-data-table/dist/dist",
				Config::get('updater.root_path')."public/bower_components/angular-resource/angular-resource",
				Config::get('updater.root_path')."public/bower_components/MDBootstrap/MDBootstrap",
				Config::get('updater.root_path')."vendor/milon/barcode/src/Milon/Barcode/Barcode",
				Config::get('updater.root_path')."storage/fonts/fonts",
			);
			
		/*
		 * List of paths or files not to be included in the update list.
		 * This paths/files are found in the list of paths specified in
		 * the constructor.
		 */
		$this->excludes = array(
			Config::get('updater.root_path')."logs",
			Config::get('updater.root_path')."app/Console/Commands/GLite",
			Config::get('updater.root_path')."app/Exceptions",
			Config::get('updater.root_path')."app/Providers",
			Config::get('updater.root_path')."app/Http/Middleware",
			Config::get('updater.root_path')."app/Http/Controllers/Auth",
			Config::get('updater.root_path')."app/Http/Controllers/System_Updates/Updater.php",
			Config::get('updater.root_path')."app/Http/Controllers/Integrations/GePG/Deamon Commands",
			Config::get('updater.root_path')."app/Http/Controllers/Integrations/GePG/GLite",
			Config::get('updater.root_path')."app/Http/Controllers/Integrations/GePG/FacilityRequestsHandler.php",
			Config::get('updater.root_path')."app/Http/Controllers/Integrations/GePG/gepgBillSubRespDeamon.php",
			Config::get('updater.root_path')."app/Http/Controllers/Integrations/GePG/GePGHandler.php",
			Config::get('updater.root_path')."app/Http/Controllers/Integrations/GePG/gepgPmtSpInfoDeamon.php",
			Config::get('updater.root_path')."app/Http/Controllers/Integrations/GePG/gepgSpReconcRespDeamon.php",
			Config::get('updater.root_path')."app/Http/Controllers/Integrations/GePG/SendBillDeamon.php",
			Config::get('updater.root_path')."app/Http/Controllers/Integrations/GePG/SendReconcilliationDeamon.php",
			Config::get('updater.root_path')."app/Http/Controllers/Integrations/GePG/Utility.php",
			Config::get('updater.root_path')."app/Http/Controllers/Integrations/RabbitMessaging/Connector.php",
			Config::get('updater.root_path')."app/sms-api",
			Config::get('updater.root_path')."database/backup.bat",
			Config::get('updater.root_path')."database/factories",
			Config::get('updater.root_path')."config/app.php",
			Config::get('updater.root_path')."config/database.php",
			Config::get('updater.root_path')."config/updater.php",
			Config::get('updater.root_path')."config/sync.php",
			Config::get('updater.root_path')."config/DHIS.php",
			Config::get('updater.root_path')."config/cache.php",
			Config::get('updater.root_path')."config/session.php",
			Config::get('updater.root_path')."config/queue.php",
			Config::get('updater.root_path')."resources/views/errors",
			Config::get('updater.root_path')."resources/views/vendor",
			Config::get('updater.root_path')."public/sync/clients",
			Config::get('updater.root_path')."public/css/roboto-fontface",
		);
		/* Local variables if the sync file with the encryption details is not yet configured/available */
		$this->encryption_key = (Config::has('sync.key') ? Config::get('sync.key') : '$2y$10$CeNvJkHH209zVFsr/ZCA2OboPfsoL7i0HjNGbmuqEjztWG2xzn9L2');
		$this->cipher_algorithm = (Config::has('sync.cipher')  ? Config::get('sync.cipher') : 'AES-256-CBC');
		$this->hash_algorithm = (Config::has('sync.hash') ? Config::get('sync.hash') : 'SHA512');
		$this->hash_length = (Config::has('sync.hash_len') ? Config::get('sync.hash_len') : '64');
		
		$this->expected_files = "...";
	}
	
	/*Initial function called during client update*/
	public function init($facility_id, $make_backup, $continue = 0, $do_on_default_account = 0, $is_live = 0, Request $request){
		/* Always ensure we are working with current config files */
		Artisan::call('config:clear');
		Artisan::call('config:cache');
		
		if(Config::has("updater.central_server"))
			return response()->json("<span style='color:red'>Can not perform the activity on this server</span>");
		
		$request['facility_id'] = $facility_id;
		$request['make_backup'] = $make_backup;
		$request['continue'] = $continue;
		$request['do_on_default_account'] = $do_on_default_account;
		$request['is_live'] = $is_live;
		$this->makeConfig($request);		
		
		/* Repeated for the benefit of clients where the sync file may not be available */
		$this->encryption_key = (Config::has('sync.key') ? Config::get('sync.key') : '$2y$10$CeNvJkHH209zVFsr/ZCA2OboPfsoL7i0HjNGbmuqEjztWG2xzn9L2');
		$this->cipher_algorithm = (Config::has('sync.cipher')  ? Config::get('sync.cipher') : 'AES-256-CBC');
		$this->hash_algorithm = (Config::has('sync.hash') ? Config::get('sync.hash') : 'SHA512');
		$this->hash_length = (Config::has('sync.hash_len') ? Config::get('sync.hash_len') : '64');
		
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','...');
		
		file_put_contents(Config::get('updater.root_path').'public/counter.txt','...');
		
		if(!file_exists(Config::get('updater.root_path').'sql_updates/'))
			mkdir(Config::get('updater.root_path').'sql_updates');
				
		
		/* 
		 * This time interval is dedicated to updates on the server
		 * and therefore clients should not begin updates within this 
		 * time window													
		 */
		/*$server_update_begin = new \DateTime(Config::get("updater.server_update_begin"));
		$server_update_end = new \DateTime(Config::get("updater.server_update_end"));
		$now = new \DateTime();
		if ($now >= $server_update_begin && $now <= $server_update_end){
			file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','done');
			file_put_contents(Config::get('updater.root_path').'public/counter.txt','done');
			return response()->json("<span style='color:red'>Sorry. The update can not be started between ". $server_update_begin->format("H:i")." and ".$server_update_end->format("H:i")."AM Try again after ".$server_update_end->format("H:i")."AM.</span>");
		}*/
		
		/*Call to the working function*/
		return response()->json($this->downloadUpdater($request));
	}
	
	/* Constructs the updater.php config file if not existing or outdated.
	 * Additions to this file are made through the updater script by
	 * overwriting it
	 */
	public function makeConfig(Request $request){
		$this_file = str_ireplace("\\","/",__FILE__);
		$root_path = substr($this_file,0, strpos($this_file,"/app/Http"));
		if(!file_exists($root_path."/config/updater.php")){
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
				$template = trim(file_get_contents($root_path."/app/Http/Controllers/System_Updates/updater.template.php"));
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
		}
	}
	
	
		
	/*
	 * This function downloads from the update server the script that
	 * indeed carryout the update chores. Once the script is downloaded
	 * and verified, it is inserted in this function and continue the
	 * process from there
	 */
	private function downloadUpdater(Request $request){
		$make_backup = $request['make_backup'];
		$api_request = "<client>
							<client>".Config::get('updater.myIP')."</client>
						</client>";
			
		
		/*
		 * This is the name of the file that carries the update
		 * logic. The code here will download the most update from the
		 * server and make a local copy
		 */
		$local_file = Config::get('updater.local_copy');
		
		/*
		 * IP address to download the server update version
		 */
		$remote_file = Config::get('updater.remote_copy');

		/*
		 * This is a place holder message. 
		 * Actual message will be set in the downloaded file
		*/
		$message = "<span style='color:red'>An error occurred while carrying out the update. Please, try again</span>";
		
		/*
		 * Try hard, at most 10 attempts to download the server version 
		 * of the update logic or any other file
		 */
		$attempts = 0;
		$curl_error = "";
		while($attempts < 10 && $ch = curl_init($remote_file)){
			/*
			 * Curl will download the server encrypted file contents 
			 * and automatically
			 * write the contents to the local file. These are then 
			 * overwritten by the decrypt
			 */
			$fp = fopen ($local_file, 'w+');
			
			$attempts++;
			curl_setopt($ch, CURLOPT_POSTFIELDS, $api_request);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/xml'));
			curl_exec($ch);
			
			if(curl_errno($ch)){
				$curl_error = "<span style='color:red'>Error communicating with the update server.".curl_error($ch).". ".( strpos(curl_error($ch),"cv failure") != 0 || strpos(curl_error($ch),"to connect") != 0 || strpos(curl_error($ch),"outstanding") != 0|| strpos(curl_error($ch), "timed out") != 0 ? "<br />Check your Internet connection.": "")."</span>";
				file_put_contents("update_log.txt", Date('Y-m-d H:i:s')."    ".curl_error($ch).PHP_EOL, FILE_APPEND);
				
				/* Empty file may occur when curl fails. Exit attempt if this persists on consecutive four attempts */
				if($attempts < 3 )
					continue;
				else{					
					file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','done');
					file_put_contents(Config::get('updater.root_path').'public/counter.txt','done');
					return $curl_error;
				}
			}
			fclose($fp);
			curl_close($ch);
			
			/* Perform decryption and validation */
			$c = base64_decode(trim(file_get_contents($local_file)));
			
			/* Failure of the curl at some point may generate an empty
			 * file if for hell reason the code reaches here in that
			 * damn scenario
			 */
			if(trim($c) == "" && $curl_error != ""){
				file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','done');
				file_put_contents(Config::get('updater.root_path').'public/counter.txt','done');
				return $curl_error;
			}
				
			/* Reset if everything went fine */
			$curl_error = "";
			
			try{
				$ivlen = openssl_cipher_iv_length($this->cipher_algorithm);
			}catch(Exception $ex){				
				file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','done');
				file_put_contents(Config::get('updater.root_path').'public/counter.txt','done');
				file_put_contents("update_log.txt", Date('Y-m-d H:i:s')."    ".$ex->getMessage().PHP_EOL, FILE_APPEND);
				$message = "An error occurred.<br /><span style='color:red'>".$ex->getMessage()."</span>";
				return $message;
			}
			$iv = substr($c, 0, $ivlen);
			$hmac = substr($c, $ivlen, $this->hash_length);
			$ciphertext_raw = substr($c, $ivlen+$this->hash_length);
			$calcmac = hash_hmac($this->hash_algorithm, $ciphertext_raw, $this->encryption_key, $as_binary=true);
			
			/*Possible error, repeat*/
			if(is_bool($hmac))
				continue;
			/* If everything is OK, carry write to local file */
			if (hash_equals($hmac, $calcmac))
			{
				$attempts = 10;
				file_put_contents($local_file,openssl_decrypt($ciphertext_raw, $this->cipher_algorithm, $this->encryption_key, $options=OPENSSL_RAW_DATA, $iv));
				/* Tell server client is starting updates download */
				$request['notification'] = 0;
				$tag = JSON_decode($this->notify($request),true);
				if($tag != NULL){
					$begin_update = $tag['date'];
					$tag = $tag['tag'];
					/* The downloaded updater script file is included here to perform the real update */
					include $local_file;					
				}
			}
		}
		
		/*Last message written to updated_files*/
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','done');
		file_put_contents(Config::get('updater.root_path').'public/counter.txt','done');
		return $message;
	}
	
	/* 
	 *Function to download an encrypted copy of the updater to clients 
	 */
	public function getUpdater(){
		$plaintext = trim(file_get_contents(Config::get("updater.local_copy")));
		/*Encrypt the contents.*/
		$ivlen = openssl_cipher_iv_length($this->cipher_algorithm);
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($plaintext, $this->cipher_algorithm, $this->encryption_key, $options=OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac($this->hash_algorithm, $ciphertext_raw, $this->encryption_key, $as_binary=true);
		$ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
		echo $ciphertext;
	}
	
	
	/*
	 * This function is used on update server to set the list of
	 * files the clients shall download for update. The function
	 * is called directly with a URL on server as
	 * IP:port/api/setUpdatingFiles
	 */
	public function setUpdatingFiles(){
		try{
			DB::statement("delete from file_updates");
			DB::statement("truncate table file_updates");
		}catch(Exception $ex){
			
		}
		finally{
			
		}
		
		/*Ensure proper naming in path and urls */
		//$this->sanitizePathsAndDatabaseObjectNames();
		
		/* Function to populate the file_updates table with 
		 * updateableFiles file names
		 */
		$this->marked_files = 0;
		$this->updateableFiles($this->file_paths, $this->excludes);		
		
		/*Regenerate the directory map to capture new files */
		$this->generateCleanDirectoryMap();
		
		/* Message sent to browser */
		echo "<h1>$this->marked_files Files successfully marked for update</h1>";
	}
	
	/*
	 * This functions returns the list of file names on the server
	 * that are more current than the client`s and their checksums
	 */
	public function timestamps(Request $request){
		$request = simplexml_load_string(trim(file_get_contents("php://input")));
		if(isset($request->last_file) && $request->last_file <> "")
			$timestamps = DB::select("select * from file_updates where date >= (select date from file_updates where file_name='".$request->last_file."' order by date asc limit 1)");
		else
			$timestamps = DB::select("select * from file_updates where date > '".$request->last_update."' order by date asc");
		
		/* Attempt to zip and download the required files as one package
		$zip = new \ZipArchive;
		if (count($timestamps) && true === TRUE){$zip->open($request->hfr.'.zip', \ZipArchive::CREATE);
			foreach($timestamps as $file)
				$zip->addFile(Config::get("updater.root_path").$file->file_name,Config::get("updater.root_path").$file->file_name);
			$zip->close();
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="00001.zip"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . filesize('00001.zip'));
			header('Connection: close');
			readfile('0001.zip');
			exit();
		}*/
		return JSON_encode($timestamps);
	}
	
	/*
	 * This function reads out the requested file to be updated 
	 * at the client
	 */
	public function files(Request $request){
		$request = simplexml_load_string(trim(file_get_contents("php://input")));
		$plaintext = trim(file_get_contents(Config::get('updater.root_path').$request->file_name));
		
		/*Encrypt the contents. */
		$ivlen = openssl_cipher_iv_length($this->cipher_algorithm);
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($plaintext, $this->cipher_algorithm, $this->encryption_key, $options=OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac($this->hash_algorithm, $ciphertext_raw, $this->encryption_key, $as_binary=true);
		$ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
		echo $ciphertext;
	}
	
	
	/*Populates the database with files names and their checksum*/
	private function updateableFiles($paths, $excludes){
		/* Create the table on the update server only if doesnt exist*/
		try{
			$update_control = "CREATE TABLE IF NOT EXISTS file_updates(".
					  "id int auto_increment not null primary key,".
					  "file_name varchar(350) not null,".
					  "checksum varchar(350) not null,".
					  "date timestamp not null,".
					  "mark_id int null default null".
					  ")";
			//DB::statement($update_control);
		}catch(Exception $ex){
			
		}
		
		$directories = array();
		foreach($paths as $directory){
			$dir = new \DirectoryIterator(dirname($directory));
			foreach ($dir as $fileinfo) {
				if($fileinfo->isDot())
					continue;
				if(in_array(str_replace('\\','/',$fileinfo->getPathname()),$excludes))
					continue;
				
				/*
				 * This files are sometimes found on directories that
				 * were worked on a MAC
				 */
				if(strpos($fileinfo->getFilename(),'.') === 0)
					continue;
				
				/* Files names are written to database directly */
				if ($fileinfo->isFile()) {
					$this->marked_files++;
					$file_name = str_replace("\\","/",substr($fileinfo->getPathname(),(strpos($fileinfo->getPathname(),Config("updater.sys_folder"))+strlen(Config("updater.sys_folder"))+1)));
					if(count(DB::select("select * from file_updates where file_name ='$file_name'")) === 0)
						DB::statement("insert into file_updates(file_name,date,checksum)select '".$file_name."','".date ("Y-m-d H:i:s", filemtime($fileinfo->getPathname()))."','".
						hash_file($this->hash_algorithm,str_replace("\\","/",$fileinfo->getPathname()))."'");
				}
				/* Inner directories are pooled for recursive processing*/
				elseif ($fileinfo->isDir()){
					array_push($directories,$fileinfo->getPathname()."/".$fileinfo->getFilename());
				}
			}
		}
		/*Recursive call on collected inner directories*/
		if(count($directories) > 0)
			$this->updateableFiles($directories,$excludes);
	}
	
	/*Called by client browser to display fetched files names*/
	public function watchUpdate($token){
		if(Config::has("updater.central_server"))
			return 'done';
		/*
		 * Since the Init and Watch calls are fired almost
		 * simultaneously, this simple technique is used to delay the
		 * actual reading
		 */
		if($token == '0'){
			sleep(5);
		}
		
		
		/* Hack to ensure that the script ends if no change to the file
		 * is detected. This scenario may occur if the update failed
		 * before the end signal is written for any reason including
		 * power outage
		 */
		if(file_exists(Config::get('updater.root_path').'public/updated_files.txt')){
			$contents = trim(file_get_contents(Config::get('updater.root_path').'public/updated_files.txt'));
			if($contents !== "done" && !preg_match("/Taking backup|\^/i",$contents)){
				if((new \DateTime(date ("Y-m-d H:i:s",filemtime(Config::get('updater.root_path').'public/updated_files.txt'))))->diff( new \DateTime())->i > 5 && (new \DateTime(date ("Y-m-d H:i:s",filemtime(Config::get('updater.root_path').'public/counter.txt'))))->diff( new \DateTime())->i > 5){
					file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','done');
					file_put_contents(Config::get('updater.root_path').'public/counter.txt','done');
					return response()->json("^It seems the update couldn't finish the last time it was run. If the rotating wheel is still goin on, please wait, otherwise; clear the update log to revert to previous point in time and then start the process again.");
				}
			}
		}
		
		/*
		 * Read out the file name or done message from the file.
		 * The frequency at which this function is called from the 
		 * browser not necessarily catching the pace at which PHP writes
		 * to this file and hence a fewer file names may be displayed 
		 * on the browser
		 */
		if(file_exists(Config::get('updater.root_path').'public/updated_files.txt'))
			return response()->json(trim(file_get_contents(Config::get('updater.root_path').'public/updated_files.txt')));
		elseif($token == '1')
			return response()->json('done');
	}
	
	/*Function to notify server that update going on by this client*/
	public function notify(Request $request){
		$facility=DB::select("select facility_code from tbl_facilities where id='".$request['facility_id']."'");
		$payload = "<client>
						<client>".Config::get('updater.myIP')."</client>
						<hfr>".$facility[0]->facility_code."</hfr>
						<notification>".$request['notification']."</notification>
						<id>".$request['tag']."</id>
						<as_array>true</as_array>
					</client>";
		
		$attempts = 0;
		while($attempts < 10 && $ch = curl_init(Config::get('updater.notification'))){
			$attempts++;
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/xml'));
			$tag = curl_exec($ch);
			if(curl_errno($ch)){
				file_put_contents("update_log.txt", Date('Y-m-d H:i:s')."    ".curl_error($ch).PHP_EOL, FILE_APPEND);
				continue;
			}
			curl_close($ch);
			$attempts = 10;
			return $tag;
		}
		return NULL;
	}
	
	/*Function to process the notification*/
	public function notification(Request $request){
		$request = simplexml_load_string(trim(file_get_contents("php://input")));
		
		if((string)$request->notification === "0"){
			DB::statement("insert into update_watcher (client_ip,hfr,status) select '".$request->client."','".$request->hfr."',0");
			$tag = DB::select("select MAX(id) tag,current_timestamp date from update_watcher where hfr='".$request->hfr."'");
			return (isset($request->as_array) ? (array)$tag[0] : $tag[0]->tag);
		}else
			DB::statement("update update_watcher set status=1 where id='".(string)$request->id."'");
		return ["tag"=>0, "date"=>date_format(new \Datetime(), "Y-m-d H:i:s")];
	}
	
	/* Utility function to remove directory path */
	private function removeDirectory($directory){
		foreach(glob("{$directory}/*") as $file){
			if(is_dir($file))
				removeDirectory($file);
			else
				try{
					if(file_exists($file)){
						unlink($file);
					}
				}catch(Exception $ex){
					file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
				}
				finally{
					
				}
		}
		try{
			rmdir($directory);
		}catch(Exception $ex){
			file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
		}
		finally{
			
		}
	}

	/* Utility function to delete record of most current file updates recorded */
	public function resetFileUpdate(){
		Artisan::call('config:clear');
		Artisan::call('config:cache');	
		$table_existance = DB::select("SELECT count(*) count FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".env('DB_DATABASE')."' and TABLE_NAME = 'file_updates'");
		
		if($table_existance[0]->count > 0){
			$recent_update = DB::select("select max(id) AS id, max(date) AS date from file_updates where file_name = 'Begining Update'");
			if(count($recent_update) && $recent_update[0]->id != ""){
				$date = $recent_update[0]->date;
				$id = $recent_update[0]->id;
				DB::statement("delete from file_updates where id >= $id;");
				return response()->json("Update logs cleared as of $date");
			}
		}
		return response()->json("No current logs found");
	}
	
	public function countUpdatedFiles(Request $request){
		if(Config::has("updater.central_server"))
			return response()->json('done');
		
		if(!file_exists(Config::get('updater.root_path').'public/counter.txt')) 
			return response()->json('...');
		
		// Process already completed
		if(file_exists(Config::get('updater.root_path').'public/updated_files.txt') 
			&& trim(file_get_contents(Config::get('updater.root_path').'public/updated_files.txt')) == "done"
			&& trim(file_get_contents(Config::get('updater.root_path').'public/counter.txt')) == "done")
			return response()->json('done');
			
		$contents = trim(file_get_contents(Config::get('updater.root_path').'public/updated_files.txt'));
		if($contents !== "done" && preg_match("/Taking backup|\^/i",$contents)){
			return response()->json(trim(file_get_contents(Config::get('updater.root_path').'public/counter.txt')));
		}
			
		// Process performing SQL Jobs/ or ongoing activity
		elseif(file_exists(Config::get('updater.root_path').'public/updated_files.txt') 
			&& strpos(trim(file_get_contents(Config::get('updater.root_path').'public/updated_files.txt')),".."))
			return response()->json(trim(file_get_contents(Config::get('updater.root_path').'public/counter.txt')));
			
		if($request->ongoing_process === 'files'){//file downloads
			// Wait, table not yet created
			if(env('DB_DATABASE') != null && count(DB::select("select * from INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='".env('DB_DATABASE')."' AND TABLE_NAME = 'file_updates'")) == 0)
				return response()->json('...');
		
			//calculate the number of files updated so  far v.s the expected payload
			$file_count = DB::select("select ifnull(MAX(mark_id),0)+1 mark_id from file_updates");
			$file_count = DB::select("select count(*) file_count from file_updates where id >".$file_count[0]->mark_id." and file_name NOT IN  ('Begining Update','End Update')");
			if(file_exists(Config::get('updater.root_path').'public/counter.txt'))
				$this->expected_files = trim(file_get_contents(Config::get('updater.root_path').'public/counter.txt'));
			return response()->json($file_count[0]->file_count."&sol;".$this->expected_files);
		}elseif($request->ongoing_process === 'sync'){//database sync
			return response()->json(trim(file_get_contents(Config::get('updater.root_path').'public/counter.txt')));
		}
		
		return response()->json('done'); 
	}
	
	public function sanitizePathsAndDatabaseObjectNames(){
		Artisan::call('config:clear');
		Artisan::call('config:cache');		
		
		$db_object_names = DB::select("select table_name as name from information_schema.tables where table_type in('base table', 'view') and table_schema = '".env('DB_DATABASE')."'");
		/*
		 * Clean controllers to ensure database object names are named exaclty 
		 * as they are stored in the database
		 */
		$this->cleanControllers(Config::get('updater.root_path')."app/Http/Controllers/Controllers/", $db_object_names);
		
		/*
		 * Clean the path names referenced in the index file
		 */
		$this->cleanIndex(Config::get('updater.root_path')."public/scripts/scripts");
		$this->cleanIndex(Config::get('updater.root_path')."public/css/css");
		$this->cleanIndex(Config::get('updater.root_path')."public/js/js");
		
		
		/*
		 * Clean the path names referenced in webroute file
		 */
		$this->cleanWebRoute(Config::get('updater.root_path')."app/Http/Controllers/Controllers");

		/*
		 * Clean the app.js views names
		 */
		$this->cleanAppJS(Config::get('updater.root_path')."public/views/views");
		$this->cleanAppJS(Config::get('updater.root_path')."public/svg/svg");
		
		/*
		 * Enable the use of UUID for Keys in the models
		 */
		//$this->enableGUID(Config::get("updater.root_path")."app/app/");
		
		return 'Process completed';
	}
	
	private function cleanControllers($path, $db_object_names, $recursive = false){
		$dir = new \DirectoryIterator(dirname($path));
		foreach ($dir as $fileinfo) {
			if($fileinfo->isDot())
				continue;
			foreach($db_object_names as $object){
				/*if($fileinfo->isFile() && preg_match("/(from(\s*)|table\((\s*)')(".$object->name.")/i", file_get_contents($fileinfo->getPathName()))) 
					file_put_contents($fileinfo->getPathName(), preg_replace("/(from(\s*)|table\((\s*)')".$object->name."/i", "$1".$object->name,file_get_contents($fileinfo->getPathName())));
				elseif($fileinfo->isDir())
					$this->cleanControllers($fileinfo->getPathName()."/".$fileinfo->getFilename(),$db_object_names, true);*/
				
				if($fileinfo->isFile() && preg_match("/$object->name/i", file_get_contents($fileinfo->getPathName()))) 
					file_put_contents($fileinfo->getPathName(), preg_replace("/$object->name/i", "$1".$object->name,file_get_contents($fileinfo->getPathName())));
				elseif($fileinfo->isDir())
					$this->cleanControllers($fileinfo->getPathName()."/".$fileinfo->getFilename(),$db_object_names, true);
			}
		}
		
		if(!$recursive){
			$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
			$contents->performed_db_object_names_cleaning = true;
			file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
		}
	}
	
	private function cleanIndex($path, $recursive = false){
		$dir = new \DirectoryIterator(dirname($path));
		foreach ($dir as $fileinfo) {
			if($fileinfo->isDot())
				continue;
			elseif($fileinfo->isFile() && preg_match("/(".$fileinfo->getFilename().")/i", file_get_contents(Config::get("updater.root_path")."resources/views/index.php"))) 
				file_put_contents(Config::get("updater.root_path")."resources/views/index.php", preg_replace("/(".$fileinfo->getFilename().")/i",$fileinfo->getFilename(),file_get_contents(Config::get("updater.root_path")."resources/views/index.php")));
			elseif(!$fileinfo->isFile() && !$fileinfo->isDot())
				$this->cleanIndex($fileinfo->getPathName()."/".$fileinfo->getFilename(), true);
		}
		
		if(!$recursive){
			$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
			$contents->performed_index_file_cleaning = true;
			file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
		}
	}
	
	private function cleanAppJS($path, $recursive = false){
		$dir = new \DirectoryIterator(dirname($path));
		foreach ($dir as $fileinfo) {
			if($fileinfo->isDot())
				continue;
			elseif($fileinfo->isFile() && preg_match("/(".$fileinfo->getFilename().")/i", file_get_contents(Config::get("updater.root_path")."public/scripts/app.js"))) 
				file_put_contents(Config::get("updater.root_path")."public/scripts/app.js", preg_replace("/(".$fileinfo->getFilename().")/i",$fileinfo->getFilename(),file_get_contents(Config::get("updater.root_path")."public/scripts/app.js")));
			elseif(!$fileinfo->isFile() && !$fileinfo->isDot())
				$this->cleanAppJS($fileinfo->getPathName()."/".$fileinfo->getFilename(), true);
		}
		
		if(!$recursive){
			$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
			$contents->performed_appJS_file_cleaning = true;
			file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
		}
	}
	
	private function cleanWebRoute($path){
		$directory = new \DirectoryIterator(dirname($path));
		foreach($directory as $fileinfo){
			if($fileinfo->isDot())
				continue;
			elseif($fileinfo->isFile())
				file_put_contents(Config::get('updater.root_path')."routes/web.php",str_ireplace(substr($fileinfo->getFilename(),0, strpos($fileinfo->getFilename(),".")),substr($fileinfo->getFilename(),0, strpos($fileinfo->getFilename(),".")),file_get_contents(Config::get('updater.root_path')."routes/web.php")));
			elseif($fileinfo->isDir()){
				file_put_contents(Config::get('updater.root_path')."routes/web.php",str_ireplace($fileinfo->getFilename(),$fileinfo->getFilename(),file_get_contents(Config::get('updater.root_path')."routes/web.php")));
				$this->cleanWebRoute($fileinfo->getPathname()."/".$fileinfo->getFilename(), true);
			}
		}
		if(!$recursive){
			$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
			$contents->performed_web_route_file_cleaning = true;
			file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
		}
	}
	
	public function enableGUID($path, $recursive = false){
		$dir = new \DirectoryIterator(dirname($path));
		foreach ($dir as $fileinfo) {
			if($fileinfo->isFile() && preg_match("/\/\/use \\\App\\\UuidForKey;/i", file_get_contents($fileinfo->getPathName()))) 
				file_put_contents($fileinfo->getPathName(), preg_replace("/\/\/use \\\App\\\UuidForKey;/i","use \\\App\\\UuidForKey;",file_get_contents($fileinfo->getPathName())));
			elseif(!$fileinfo->isFile() && !$fileinfo->isDot())
				$this->enableGUID($fileinfo->getPathName()."/".$fileinfo->getFilename(), true);
		}
		
		if(!$recursive){
			$contents = JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'));
			$contents->performed_guid_conversion = true;
			file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
		}
	}
	
	public function generateCleanDirectoryMap($path = Null, $start = true){
		if($start){
			Artisan::call('config:clear');
			Artisan::call('config:cache');
			$path = str_ireplace("\\","/",__FILE__);
			$path = substr($path,0, strpos($path,"/app/Http"));
		}
		$this->excludes = [
						Config::get("updater.root_path").".git",
						Config::get("updater.root_path").".env",
						Config::get("updater.root_path").".idea",
						Config::get("updater.root_path").".phpintel",
						Config::get("updater.root_path")."bootstrap",
						Config::get("updater.root_path")."node_modules",
						Config::get("updater.root_path")."storage",
						Config::get("updater.root_path")."tests",
						Config::get("updater.root_path")."vendor",
						Config::get("updater.root_path")."logs",
						Config::get("updater.root_path")."clear logs.bat",
						Config::get("updater.root_path")."public/bower_components",
						Config::get("updater.root_path")."public/font-awesome",
						Config::get("updater.root_path")."public/uploads",
					];
		if($start)
			file_put_contents(Config::get('updater.root_path')."app/Http/Controllers/System_Updates/directory_map.php","<?php".PHP_EOL." return [");
		
		$directory = new \DirectoryIterator($path);
		$path = str_ireplace("\\","/",$path)."/";
		foreach($directory as $fileinfo){
			if($fileinfo->isDot())
				continue;
			
			if(in_array($path.$fileinfo->getFilename(), $this->excludes))
				continue;

			elseif($fileinfo->isFile())
				file_put_contents(Config::get('updater.root_path')."app/Http/Controllers/System_Updates/directory_map.php","'".strtolower(str_replace(Config::get('updater.root_path'),"{root_path}",$path).$fileinfo->getFilename())."',", FILE_APPEND);
			elseif($fileinfo->isDir())
				$this->generateCleanDirectoryMap($fileinfo->getPathName(), false);
		}
		if($start){
			file_put_contents(Config::get('updater.root_path')."app/Http/Controllers/System_Updates/directory_map.php","];", FILE_APPEND);
			echo '<h1>Directory map successfully created.</h1>';
		}
	}
	
	public function changeFacilityId($facility_id){		
		/* Always ensure we are working with current config files */
		Artisan::call('config:clear');
		Artisan::call('config:cache');
		
		DB::statement("set foreign_key_checks=0");
		$old_id = DB::select("select facility_id from tbl_patients order by id desc limit 1");
		if(count($old_id) > 0){
			try{
				foreach(DB::select("select table_name, column_name from information_schema.key_column_usage where referenced_table_name='tbl_facilities' and referenced_column_name='id' and table_schema='".env('DB_DATABASE')."'") AS $table)
					DB::statement("update ". $table->table_name ." set ".$table->column_name."='$facility_id'");
				
				$contents = (file_exists(Config::get('updater.root_path').'public/updater.rem') 
					? JSON_decode(file_get_contents(Config::get('updater.root_path').'public/updater.rem'))
					:  new \stdClass());
				$contents->assigned_right_facility_id = true;
				file_put_contents(Config::get('updater.root_path').'public/updater.rem', JSON_encode($contents));
				echo "Process completed successfully";
			}catch(Exception $ex){
				file_put_contents("update_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$ex->getMessage().PHP_EOL, FILE_APPEND);
				return $ex->getMessage();
			}
			finally{
				
			}
		}else
			echo "Could not determine facility_id from patients table";
	}
	
	public function schema(){
		Artisan::call('config:clear');
		Artisan::call('config:cache');
		$html = "<table border='1'><thead><th>".(env('DB_DATABASE'))." SCHEMA</th><th>UUID</td><thead><tbody><tr><td valign='top'><table border='1'>";
		
		//AUTO_INCREMENT DB
		$tables = DB::select("select table_name from information_schema.tables where table_schema='".env('DB_DATABASE')."' and table_type='base table'");
		foreach($tables as $table){
			$html .= "<th colspan='2' style='background-color:gray'>&nbsp;</th>";
			$html .= "<tr><td colspan='2' style='text-align:left;font-weight:bold'>".$table->table_name."</td></td>";
			$html .= "<tr><td style='text-align:left'><table>";
			foreach(DB::select("select column_name from information_schema.columns where table_schema='".env('DB_DATABASE')."' and table_name='".$table->table_name."' order by column_name asc") as $column)
				$html .= "<tr><td style='text-align:left'>".$column->column_name."</td></tr>";
			
			$html .= "</table></td>";
			$html .= "<td style='text-align:left'><table border='1'><thead><th colspan='2'>REFERENCES</th></thead>";
			$html .= "<tbody><tr><td  valign='top'><table border='1'><thead><th>COLUMN</th><th>TABLE</th><th>COLUMN</th></thead>";
			foreach(DB::select("select column_name,referenced_table_name, referenced_column_name from information_schema.key_column_usage where table_schema=referenced_table_schema and table_schema='".env('DB_DATABASE')."' and table_name='".$table->table_name."' order by column_name asc") as $reference){
				$html .= "<tr><td>".$reference->column_name."</td>";
				$html .= "<td>".$reference->referenced_table_name."</td>";
				$html .= "<td>".$reference->referenced_column_name."</td><tr>";
			}
			$html .= "</table></td></tr></tbody></table>";
			$html .="</td></tr>";
		}
		
		//UUID DB
		$html .= "</table></td><td style='background-color:gray'>&nbsp;</td><td  valign='top'><table border='1'>";
		$tables = DB::select("select table_name from information_schema.tables where table_schema='".env('UUID')."' and table_type='base table'");
		foreach($tables as $table){
			$html .= "<th colspan='2' style='background-color:gray'>&nbsp;</th>";
			$html .= "<tr><td colspan='2' style='text-align:left;font-weight:bold'>".$table->table_name."</td></td>";
			$html .= "<tr><td style='text-align:left'><table>";
			foreach(DB::select("select column_name from information_schema.columns where table_schema='".env('UUID')."' and table_name='".$table->table_name."' order by column_name asc") as $column)
				$html .= "<tr><td style='text-align:left'>".$column->column_name."</td></tr>";
			
			$html .= "</table></td>";
			$html .= "<td style='text-align:left'><table border='1'><thead><th colspan='2'>REFERENCES</th></thead>";
			$html .= "<tbody><tr><td  valign='top'><table border='1'><thead><th>COLUMN</th><th>TABLE</th><th>COLUMN</th></thead>";
			foreach(DB::select("select column_name,referenced_table_name, referenced_column_name from information_schema.key_column_usage where table_schema=referenced_table_schema and table_schema='".env('UUID')."' and table_name='".$table->table_name."' order by column_name asc") as $reference){
				$html .= "<tr><td>".$reference->column_name."</td>";
				$html .= "<td>".$reference->referenced_table_name."</td>";
				$html .= "<td>".$reference->referenced_column_name."</td><tr>";
			}
			$html .= "</table></td></tr></tbody></table>";
			$html .="</td></tr>";
		}
		$html .= "</table></td></tr><tbody></table>";
		echo $html;
	}
	
	public function convertToGuid(){
		/* Always ensure we are working with current config files */
		Artisan::call('config:clear');
		Artisan::call('config:cache');
		
		//check if not yet converted. Note, a single table is relied upon the check
		$uses_guid = DB::select("select COLUMN_TYPE type from INFORMATION_SCHEMA.COLUMNS where TABLE_SCHEMA='".env("DB_DATABASE")."' and TABLE_NAME = 'users' and COLUMN_NAME='id'");
		if(count($uses_guid)){
			//If not yet, perform the operation. First, a backup is taken
			if(strtolower($uses_guid[0]->type) != 'char(36)'){
				$db_backup = Config::get('updater.root_path').env('DB_DATABASE').'-'.Date("Y-m-d his").'.sql';
				file_put_contents(Config::get('updater.root_path').'public/updated_files.txt',"Taking database to $db_backup....");
				$command = Config::get("sync.mysqldump")
								." --user=". env('DB_USERNAME')
								." --password=" . env('DB_PASSWORD') 
								." --host=" . env('DB_HOST') 
								." " . env('DB_DATABASE')
								." > \"" . $db_backup ."\" 2>&1";
					
				$output = array("Update_date"=>Date('Y-m-d H:i:s'), "file"=> $db_backup);
				exec($command, $output, $result);
				file_put_contents(Config::get('updater.root_path').'public/counter.txt', '33&percnt;');
				if(isset($output['0']) && strpos($output['0'],'RROR') >0){
					file_put_contents("update_log.txt", Date('Y-m-d H:i:s').'  ---  DATABASE KEYS CONVERSION' . PHP_EOL .print_r($output,TRUE).PHP_EOL, FILE_APPEND);
					$message = "An error occurred while attempting to backup your database to $db_backup.<br /> <span style='color:red;'>Conversion to GUID can not proceed.</span>";
					exit_updater;
				}else{
					file_put_contents(Config::get('updater.root_path').'public/updated_files.txt',"Converting database Keys to GUID");
					$command = Config::get("updater.mysql")
								." --user=". env('DB_USERNAME')
								." --password=" . env('DB_PASSWORD') 
								." --host=" . env('DB_HOST') 
								." " . env('DB_DATABASE')
								." < \"" . Config::get('updater.root_path')."public/sync/conversion to guid.sql" ."\" 2>&1";
					
					$output = array("Update_date"=>Date('Y-m-d H:i:s'), "file"=>  Config::get('updater.root_path')."public/sync/conversion to guid.sql");
					exec($command, $output, $result);
					file_put_contents(Config::get('updater.root_path').'public/counter.txt', '100&percnt;');
					if(isset($output['0']) && strpos($output['0'],'RROR') >0){
						file_put_contents("update_log.txt", Date('Y-m-d H:i:s').'  ---  DATABASE KEYS CONVERSION' . PHP_EOL .print_r($output,TRUE).PHP_EOL, FILE_APPEND);
						$message = "An error occurred while converting your database keys to GUID.<hr ><span style='color:red'>Please, drop the database and manually restore the backup created in <br />".$db_backup."</span>";
						//restore the backup
						file_put_contents(Config::get('updater.root_path').'public/counter.txt', '0&percnt;');
						file_put_contents(Config::get('updater.root_path').'public/updated_files.txt',"An error occurred while performing key type conversion. Your current database is being restored...please wait");
						$db_ext = DB::connection('mysql_external');	
						$db_ext->statement("DROP DATABASE IF EXISTS ".env('DB_DATABASE'));		
						$command = Config::get("updater.mysqldump")
									." --user=". env('DB_USERNAME')
									." --password=" . env('DB_PASSWORD') 
									." --host=" . env('DB_HOST') 
									." " . env('DB_DATABASE')
									." < \"$db_backup\" 2>&1";
						
						$output = array("Update_date"=>Date('Y-m-d H:i:s'), "file" => $db_backup);
						exec($command, $output, $result);
						file_put_contents(Config::get('updater.root_path').'public/counter.txt', '100&percnt;');
						if(isset($output['0']) && strpos($output['0'],'RROR') >0){
							file_put_contents("update_log.txt", Date('Y-m-d H:i:s').'  ---  DATABASE RESTORE' . PHP_EOL .print_r($output,TRUE).PHP_EOL, FILE_APPEND);
							$message = "An error occurred while recovering your database from GUID keys conversion failure.<hr ><span style='color:red'>Please, drop the database and attempt manual restore of the backup created in <br />".$db_backup."</span>";
						}
					}
				}
				$this->enableGUID(Config::get("updater.root_path")."app/app/");
			}else
				$message = 'Your database already implements GUID keys';
		}
		if(isset($message))
			return $message;
	}

	public function key_space_range(Request $request){
		$request = simplexml_load_string(trim(file_get_contents("php://input")));
		$space = DB::select("select * from key_space_range where facility_code='".$request->hfr."'");
		if(count($space) === 1){
			return ["has_key_space"=>true, "lower_id"=>$space[0]->lower_id, "upper_id"=>$space[0]->upper_id];
		}
		return ["has_key_space"=>false];
	}
}