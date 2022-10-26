<?php
/*****************************************************************
 * This file is used specifically to carryout data synchronization*
 * activities.													 *
 *****************************************************************/
namespace App\Http\Controllers\Sync;
ini_set('max_execution_time', -1);
ini_set('upload_max_filesize', '512M');
ini_set('post_max_size', '512M');
ini_set('memory_limit', '1024M');

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use DB;
use Artisan;

			
class Sync extends Controller
{
	/*
	 * List of schema tables that are synced with central database
	 */
    private $tables;
    private $last_sync_date;
    private $current_sync_date;
    private $dump_file;
	
	/*Constructor*/
	public function __construct(){
		//
	}
	
	/*Initial function called during client sync*/
	public function init($facility_id, Request $request){
		$request['facility_id'] = $facility_id;
		
		file_put_contents(Config::get('updater.root_path').'public/counter.txt','...');
		
		//default demo database account. Do not sink
		if($request->facility_id == 1 
			&& (DB::select("select max(facility_id) facility_id from tbl_accounts_numbers")[0]->facility_id != 1
			|| preg_match("/demo|test/",DB::select("select facility_name from tbl_facilities where id='".$request->facility_id."'")[0]->facility_name))){
			$message = "<span style='color:red'>You can not perform this activity using the default facility account. Please, create an actual facility admin account to proceed. This functionality should not be done on a demo database.</span><hr />Thank you.";
			return response()->json($message);
		}
		
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','Preparing configurations....');
		
		$this->makeConfig($request);
		/* Always ensure we are working with current config files */
		Artisan::call('config:clear');
		Artisan::call('config:cache');
		
		/* 
		 * This time interval is dedicated to updates on the server
		 * and therefore clients should not begin syncs within this 
		 * time window													
		 */
		$server_update_begin = new \DateTime('0800');
		$server_update_end = new \DateTime('1000');
		$config_begin = new \DateTime(Config::get("sync.server_update_begin"));
		$config_end = new \DateTime(Config::get("sync.server_update_end"));
		$now = new \DateTime();
		$timing_error = false;
		if (($config_begin != $server_update_begin || $config_end != $server_update_end) || ($now >= $server_update_begin && $now <= $server_update_end)){
			$timing_error = true;
			$message = "<span style='color:red'>Sorry. The sync can not be started between ". $server_update_begin->format("H:i")." and ".$server_update_end->format("H:i")."AM. Try again after ".$server_update_end->format("H:i")."AM.</span>";
		}
		
		
		
		/* Create the table on the client server only if doesn't exist */
		$last_sync_date = "CREATE TABLE IF NOT EXISTS sync_requests(".
					  "id int auto_increment not null primary key,".
					  "facility_id varchar(350) null,".
					  "last_sync_date timestamp not null".
					  ")";
		DB::statement($last_sync_date);
		
		/* Load the last sync date remembered */
		$last_sync_date = DB::select("select MAX(last_sync_date) as last_sync_date from `sync_requests`");
		if(is_array($last_sync_date)  && count($last_sync_date) > 0 && $last_sync_date[0]->last_sync_date != '')
			$this->last_sync_date = new \DateTime($last_sync_date[0]->last_sync_date); 
		else/* Default */
			$this->last_sync_date = new \DateTime('2010-01-01 00:00:01');
		
		$facility_name = DB::select("select facility_name from tbl_facilities where id='$facility_id'")[0]->facility_name;
		
		//record the time at which this sync is initialized */
		$this->current_sync_date = new \DateTime();
		
		$this->dump_file = Config::get('sync.local_sync_file').'sync-'.$this->last_sync_date->format("Y-m-d H-i-s").'-'.$facility_id.'.sql';
		file_put_contents(Config::get('updater.root_path').'public/counter.txt', "5&percnt;");		
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','^Preparing database backup....');
		try{
			/* Execute a CMD dump command on mysql for the current table. Note the where clause on 
			 * which the time intervals are specified. The output is directly sent to the temp_dum file
			 * from which it is re-read and concatenate to the plain text
			 */
			$command = Config::get("sync.mysqldump")
						." --user=". env('DB_USERNAME')
						." --password=" . env('DB_PASSWORD') 
						." --host=" . env('DB_HOST') 
						." " . env('DB_DATABASE')
						." > \"" . $this->dump_file ."\"";
			exec($command);
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "45&percnt;");
		 }catch(Exception $e){
			file_put_contents("dump_log.txt", $e->getMessage());
			if(!$request->has("internal_call")){
				file_put_contents(Config::get('updater.root_path').'public/counter.txt', "done");
				file_put_contents(Config::get('updater.root_path').'public/updated_files.txt', "done");
			}
			return response()->json("Sorry. An error occurred while generating latest data sets. Review public/dump_log.txt");
		 }
		 
		/* Call the function to perform the upload */
		return response()->json($this->sendDump($request, $facility_id, $facility_name));
	}
	
	/* Constructs the sync.php config file if not existing or outdated.
	 * Additions to this file are made through the updater script by overwriting it
	 */
	private function makeConfig(Request $request){
		//makes sure the updater file exists before proceeding
		$config = new \App\Http\Controllers\System_Updates\Updater_Init();
		$config->makeConfig($request);
		
		$this_file = str_ireplace("\\","/",__FILE__);
		$root_path = substr($this_file,0, strpos($this_file,"app/Http"));
		if(
			!file_exists(Config::get("updater.root_path")."config/sync.php")//no config file 
			|| (new \Datetime(date ("Y-m-d H:i:s",filemtime(Config::get("updater.root_path")."config/sync.php"))) < new \Datetime("2018-05-25 14:00:00"))//old file
			|| (substr(Config::get("sync.tables_local"),0, strpos(Config::get("sync.tables_local"),"public")) != $root_path)//possible folder rename			
		){
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
				$template = trim(file_get_contents(Config::get("updater.root_path")."app/Http/Controllers/Sync/sync.template.php"));
				
				$tables_local = $root_path."public/sync/tables.xml";
				$local_sync_file = $root_path."public/sync/";
				//manually overwritten on update server to C:\gothomisv3_backups
				$server_sync_file = $root_path."public/sync/clients/";
				$temp_dump = $root_path."public/sync/temp_dump.sql";
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
				$template = str_replace("tables_local =>","'tables_local' => '".$tables_local."'",$template);
				$template = str_replace("local_sync_file =>","'local_sync_file' => '".$local_sync_file."'",$template);
				$template = str_replace("server_sync_file =>","'server_sync_file' => '".$server_sync_file."'",$template);
				$template = str_replace("temp_dump =>","'temp_dump' => '".$temp_dump."'",$template);
				$template = str_replace("mysql =>","'mysql' => '".$mysql."'",$template);
				$template = str_replace("mysqldump =>","'mysqldump' => '".$mysqldump."'",$template);
				file_put_contents($root_path."config/sync.php",$template);
			}catch(Exception $e){
				file_put_contents("sync_log.txt", Date('Y-m-d H:i:s').PHP_EOL .$e->getMessage().PHP_EOL, FILE_APPEND);
			}
		}
	}
	
	private function loadTables(){
		$api_request = "<client>
							<client>".Config::get('sync.myIP')."</client>
						</client>";
		
		/*
		 * Path to local copy
		 */
		$local_file = Config::get('sync.tables_local');
		
		/*
		 * IP address to download the server tables list version
		 */
		$remote_file = Config::get('sync.tables_server');

		/*
		 * Curl will download the server file contents and automatically
		 * write the contents to the local file
		 */
		$fp = fopen ($local_file, 'w+');
		
		/*
		 * This is a place holder msg. 
		 * Actual message will be set in the downloaded file
		*/
		$message = "<span style='color:red'>An error occurred while downloading the list of updatable tables</span>";
		
		$success = false;
		/*
		 * Try hard, at most 10 attempts to download the server version 
		 * of the update logic
		 */
		$attempts = 0;
		while($attempts++ < 10 && $ch = curl_init($remote_file)){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $api_request);
			curl_setopt($ch, CURLOPT_TIMEOUT, 50);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/xml'));
			curl_exec($ch);
			if(curl_errno($ch)){
				file_put_contents("sync_log.txt", Date('Y-m-d H:i:s')."    ".curl_error($ch).PHP_EOL, FILE_APPEND);
				continue;
			}
			curl_close($ch);
			fclose($fp);
			
			/* Perform decryption and validation */
			$c = base64_decode(file_get_contents($local_file));
			$ivlen = openssl_cipher_iv_length(Config::get('sync.cipher'));
			$iv = substr($c, 0, $ivlen);
			$hmac = substr($c, $ivlen, Config::get('sync.hash_len'));
			$ciphertext_raw = substr($c, $ivlen+Config::get('sync.hash_len'));
			$calcmac = hash_hmac(Config::get('sync.hash'), $ciphertext_raw, Config::get('sync.key'), $as_binary=true);
			
			/*Possible error, repeat*/
			if(is_bool($hmac))
				continue;
			/* If everything is OK, carry write the decrypt to local copy */
			if (hash_equals($hmac, $calcmac))
			{
				file_put_contents($local_file,openssl_decrypt($ciphertext_raw, Config::get('sync.cipher'), Config::get('sync.key'), $options=OPENSSL_RAW_DATA, $iv));
				$success = true;
				$attempts = 10;//shall end the attempts loop
			}else{
				file_put_contents("sync_log.txt", Date('Y-m-d H:i:s')."    "."Checksum missmatch".PHP_EOL, FILE_APPEND);
				continue;
			}
		}
		
		if(!$success)
			return false;
		
		
		/* Load the list of tables supposed to be synced */
		$this->tables = simplexml_load_string(trim(file_get_contents($local_file)));
		return true;
		
	}
	
	/*
	 * This function uploads the sync file to the server for processing
	 */
	private function sendDump(Request $request, $facility_id, $facility_name){
		file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','^Uploading backup to central server.....');
		//make a zip of the file
		$file_name = $this->dump_file;
		$file_name_in_zip = substr($file_name, strpos($file_name,'sync-'));
		$zip_name = Config::get('sync.local_sync_file').'sync-'.$this->last_sync_date->format("Y-m-d H-i-s").'-'.$facility_id.'.zip';
		
		try{
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "55&percnt;");		
			$zip = new \ZipArchive;
			if ($zip->open($zip_name, \ZipArchive::CREATE) === TRUE){
				$zip->addFile($file_name, $file_name_in_zip);
				$zip->close();
			}
			try{
				unlink($file_name);
			}catch(Exception $ex){}
			
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "75&percnt;");		
			
			
			$attempt = 0;
			$message = "Failure";
			while($attempt++ < 10 && $ch = curl_init(Config::get('sync.remote_server'))){/* Make at most 10 attempts */
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt(
					$ch,
					CURLOPT_POSTFIELDS,
					array(
					  'dump' => new \CURLFile($zip_name),
					  'zip_name'  => substr($zip_name, strpos($zip_name,'sync-')),
					  'file_name_in_zip'  => $file_name_in_zip,
					  'facility_name'  => $facility_name,
					  'facility_id'  => $facility_id,
					  'sync_date'  => $this->last_sync_date->format("Y-m-d H:i:s")
					));

				curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
				$response = curl_exec($ch);
				if(curl_errno($ch)){
					file_put_contents("sync_log.txt", Date('Y-m-d H:i:s')."    ".curl_error($ch).PHP_EOL, FILE_APPEND);
					continue;
				}
				$attempt=10;//success
				curl_close($ch);
			}
			file_put_contents("sync_log.txt", Date('Y-m-d H:i:s')."    ".print_r($response, true).PHP_EOL, FILE_APPEND);
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', "95&percnt;");
			if(file_exists($zip_name))
				try{
					unlink($zip_name);
				}catch(Exception $ex){}
			
			if(!is_object(JSON_decode($response))){
				if(!$request->has("internal_call")){
					file_put_contents(Config::get('updater.root_path').'public/counter.txt', "done");
					file_put_contents(Config::get('updater.root_path').'public/updated_files.txt', "done");
				}
				return "<span style='color:red'>An error occurred on the central server. Response could not be decoded</span>";
			}
			
			if(JSON_decode($response)->success == 1){
				file_put_contents(Config::get('updater.root_path').'public/counter.txt', "100&percnt;");		
				DB::statement("insert into sync_requests(facility_id,last_sync_date) select '$facility_id','".$this->current_sync_date->format("Y-m-d H:i:s")."'");
				
				//summary reports
				file_put_contents(Config::get('updater.root_path').'public/updated_files.txt','^Warning for long process...Reporting aggregate data to central server....');
				$aggregation = new \App\Http\Controllers\Integrations\Dashboard\Data\DashboardReportingController($request);	
				$aggregation->computeAndSend();
			}
			
			if(!$request->has("internal_call")){
				file_put_contents(Config::get('updater.root_path').'public/counter.txt', "done");
				file_put_contents(Config::get('updater.root_path').'public/updated_files.txt', "done");
			}
			
			file_put_contents("sync_log.txt", PHP_EOL .Date('Y-m-d H:i:s')."    ".PHP_EOL . print_r($response,true). PHP_EOL, FILE_APPEND);
			return JSON_decode($response)->message;
		}catch(Exception $ex){
			file_put_contents("sync_log.txt", PHP_EOL .Date('Y-m-d H:i:s')."    ".PHP_EOL . $ex->getMessage(). PHP_EOL, FILE_APPEND);
			return array("success"=>0, "message"=>"<hr /><br /><span style='color:red'>Sync failure, unknown reason.</span><br /><hr />");
		}
	}
	
	/*Function to process the client sync requests on the server*/
	public function sync(Request $request){
		/* Create the table on the central server only if doesn't exist */
		/*$sync = "CREATE TABLE IF NOT EXISTS sync_requests(".
					  "id int auto_increment not null primary key,".
					  "facility_id char(36) null,".
					  "last_sync_date timestamp not null".
					  ")";
		DB::statement($sync);
		*/
		
		$server_copy = Config::get('sync.server_sync_file').$request->facility_name ."/";
		if ($request->hasFile('dump') && $request->file('dump')->isValid()) {
			$file = $request->file('dump');			
			//applying Laravel storage::move here fails!!!!
			$file->move($server_copy,$request->zip_name);
			
			//extract the zip
			$zip_name = $server_copy.$request->zip_name;
			
			//delete older files
			$dir = new \DirectoryIterator(dirname($server_copy.$request->facility_name));
			foreach ($dir as $fileinfo)
				if ($fileinfo->isFile() && str_replace("\\","/",$fileinfo->getPathname()) != $zip_name)
					unlink($fileinfo->getPathname());
			//end delete 
		}else		
			return array("success"=>0, "message"=>"<hr /><br /><span style='color:red'>Sync failure, unknown reason.</span><br /><hr />");
		
		DB::statement("insert into sync_requests(facility_id,last_sync_date) select '".$request->facility_id."','".$request->sync_date."'");
		return array("success"=>1, "message"=>"<hr />Successful completed sync with central server.<hr />");
	}
	
	/*Download to client the server version of sync tables*/
	public function syncTables(){
		$plaintext = file_get_contents(Config::get('sync.tables_local'));
		
		/*Encrypt the contents */
		$ivlen = openssl_cipher_iv_length(Config::get('sync.cipher'));
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($plaintext, Config::get('sync.cipher'), Config::get('sync.key'), $options=OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac(Config::get('sync.hash'), $ciphertext_raw, Config::get('sync.key'), $as_binary=true);
		$ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
		echo $ciphertext;
	}
	
	
	public function syncProgress(Request $request){
		if(Config::has("updater.central_server"))
			return response()->json('done');
		
		if(!file_exists(Config::get('updater.root_path').'public/counter.txt')) 
			return response()->json('...');
		
		// Process already completed
		if(file_exists(Config::get('updater.root_path').'public/updated_files.txt') 
			&& trim(file_get_contents(Config::get('updater.root_path').'public/updated_files.txt')) == "done"
			&& trim(file_get_contents(Config::get('updater.root_path').'public/counter.txt')) == "done")
			return response()->json('done');
			
		elseif(file_exists(Config::get('updater.root_path').'public/counter.txt'))
			return response()->json(trim(file_get_contents(Config::get('updater.root_path').'public/counter.txt')));
	}
}