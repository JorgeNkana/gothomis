<?php
namespace App\Http\Controllers\installation;
ini_set('max_execution_time',-1);
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Helpers\DatabaseConnection;
use Config;
use App\classes\OTF;
use Artisan;
class installationController extends Controller
{
    /**
     * Calls the method 
     */
	 
	  /**
     * Calls the method 
     */
    public function something($db){
        // some code
        $env_update = $this->changeEnv([
            'DB_DATABASE'   => $db,
           // 'DB_USERNAME'   => 'new_db_user',
            //'DB_HOST'       => 'new_db_host'
        ]);
        if($env_update){
            // Do something
        } else {
            // Do something else
        }
        // more code
    }
    
    protected function changeEnv($data = array()){
        if(count($data) > 0){

            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\s+/', $env);;

            // Loop through given data
            foreach((array)$data as $key => $value){

                // Loop through .env-data
                foreach($env as $env_key => $env_value){

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if($entry[0] == $key){
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);
            
            return true;
        } else {
            return false;
        }
    }
	 
    public function installSystem(Request $request){
		return;
		
		$exitCode = \Artisan::call('config:cache');
				
		$db_ext = DB::connection('mysql_external');
		
	
		$sql="SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'homis_tamisemi_central'";
		$resp=$db_ext->SELECT($sql);
		//return $resp[0]->SCHEMA_NAME;
		
		
		if(count($resp)==0){
		$db_ext->statement('CREATE DATABASE  IF NOT EXISTS `homis_tamisemi_central`');
		
		//$path = base_path('.env');
		$db="homis_tamisemi_central";
            
			$this->something($db);
			$exitCode = \Artisan::call('config:cache');
		     return response()->json([
                    'data' => 'WELCOME TO SYSTEM INSTALLATION',
                    'status' => 1
                ]);
		}
		    }
			
	
	
	public function createSchema(Request $request){
		
		$exitCode =Artisan::call('config:cache');
                if(Artisan::call('migrate')){ 
				  $this->createSeeder();
                  return response()->json([
                    'data' => 'MIGRATION WAS SUCCESSFULLY DONE',
                    'status' => 1
                ]);	
				}	
				else{
					return Artisan::call('migrate');
					return response()->json([
                    'data' => 'Error in Migration',
                    'status' => 0
                ]);	
					
				}	
     }
	 public function createSeeder(){
                if(\Artisan::call('db:seed')){ 
                  return response()->json([
                    'data' => 'SET UP WAS SUCCESSFULLY CONFIGURED,USE DEFAULT ACCOUNT TO LOGIN',
                    'status' => 1
                ]);	
				}	else{
					 				
					return response()->json([
                    'data' => 'CONFIGURATION FAILED',
                    'status' => 0
                ]);	
					
				}	
     }
    
  }