<?php

namespace App\Http\Controllers\Integrations\DHIS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Facility\Tbl_facility;
use Illuminate\Support\Facades\Config;
use Artisan;
use DB;

class DHIS extends Controller
{
	protected $error;/* Hold an arbitrary error message */
	protected $curl_errors;/* Holds curl_error message encountered in helper functions */
	
	public function __construct(Request $request){
		$this->curl_errors = false;
		$this->makeConfig($request);
	}
	
	/* This function calls the sendToDHIS function on the server.
     * The function is invoked by client facilities that don't send the
	 * request themselves straight from their server instead
	 * go through the central server....like the implementation of GePG.
	 * Note that if the request originates on the central server itself,
	 * the function is never called! Damn cryptic code...but necessary
	 */
	public function sendToDHISFromCentralServer(Request $request){
		$this->sendToDHIS($request);
	}
	
	/* Function to send the request to DHIS 
     * When called by a facility server and the intermediate_url is given,
	 * the function calls the central sever to process the call.
	 * Note the cryptic implementation here when the 
	 * if(Config::has("DHIS.central_server")) is tested. This same function will again 
	 * be called on the server except that the check on intermediate_url will 
	 * fail and hence proceed with the other logic
	 */
	public function sendToDHIS(Request $request){
		$payload = NULL;
		$request->start_date = date_format(new \Datetime($request->start_date),'Y-m-d');
		$request->end_date = date_format(new \Datetime($request->end_date),'Y-m-d');
		
		/* Begin: Which server (facility/central) is going to communicate with DHIS */
		if(Config::has("DHIS.intermediate_url")){//True if this block executed on facility's server
			/* In this case, the facility server will use curly to forward the 
			 *  call to the central server once it has added its payload to the request
			 * Note that the orgUnit shall be added by the server. Initially the field 
			 * holds the facility_code
			 */
			$payload = $this->getPayload($request);
			if(!$payload && $this->curl_errors)
				return $this->error;
			elseif(!$payload)
				return array("status"=>"error", "description"=>"An error occurred while generating the required datasets");
			
			if(count($payload->dataValues) == 0)
				return array("status"=>"info", "description"=>"No data found in the selected month.");
		
			$request->payload = JSON_encode($payload);
			$ch = curl_init(Config::get("DHIS.intermediate_url"));
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, JSON_encode($request));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

			$data = curl_exec($ch);
			if(curl_errno($ch)){
					$this->error = array("status"=>"error", "description"=>"<span style='color:red'>Error communicating with the Central GoT-HoMIS server. ".( strpos(curl_error($ch),"cv failure") != 0 || strpos(curl_error($ch),"to connect") != 0 || strpos(curl_error($ch), "timed out") != 0 ? "<br />Check your Internet connection.": curl_error($ch))."</span>");
					file_put_contents("dhis_log.txt", Date('Y-m-d H:i:s')."    ".curl_error($ch).PHP_EOL, FILE_APPEND);
					curl_close($ch);
					return $this->error;
			}
			curl_close($ch);
			return response($data);
		}
		
		/* The following condition tests true on the central server. This makes the function work
		 * the same either on clients or the server.
		 */
		if(!Config::has("DHIS.intermediate_url") && !isset($request->payload))
			$payload = $this->getPayload($request);
		else//this statement is hit by the client making curl request to the central server
			$payload = JSON_decode($request->payload);
		/* End: Which server (facility/central) is going to communicate with DHIS */
		
		
		if(count($payload->dataValues) == 0)
			return array("status"=>"info", "description"=>"No data found in the selected month.");
		
		/* Ask DHIS to supply the orgUnit mapped with the given facility_code */
		$orgUnit = $this->getOrgUnit($payload->orgUnit, false);		
		
		if(!$orgUnit)
			return $this->error;
		else
			$payload->orgUnit = $orgUnit;
		
		
		
		/* Send the payload to DHIS */
		$service_url = Config::get("DHIS.service_url");
		$username = Config::get("DHIS.username");
		$password = Config::get("DHIS.password");
			
		$ch = curl_init($service_url);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, JSON_encode($payload));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$data = curl_exec($ch);
		if(curl_errno($ch)){
				$this->error = array("status"=>"error", "description"=>"<span style='color:red'>Error communicating with the DHIS server. ".( strpos(curl_error($ch),"cv failure") != 0 || strpos(curl_error($ch),"to connect") != 0 || strpos(curl_error($ch), "timed out") != 0 ? "<br />Check your Internet connection.": curl_error($ch))."</span>");
				file_put_contents("dhis_log.txt", Date('Y-m-d H:i:s')."    ".curl_error($ch).PHP_EOL, FILE_APPEND);
				curl_close($ch);
				return $this->error;
		}
		curl_close($ch);
		return response()->json(json_decode($data));
	}
	
	private function getPayload(Request $request){
		if($request->has('all_books'))
			return $this->allBooks($request);
		switch($request->mtuha_book){
			case 'opd':
				return $this->dataset($request,Opd_summary::getSummary($request));
			case 'ipd':
				return $this->dataset($request,Ipd_summary::getSummary($request));
			case 'tracer_medicine':
				return $this->dataset($request,Tracer_medicine_summary::getSummary($request));
			case 'anc':
				return $this->dataset($request,Anc_summary::getSummary($request));
			case 'bed_occupancy':
				return $this->dataset($request,Bed_occupacy_summary::getSummary($request));
			case 'family_palnning':
				return $this->dataset($request,Family_palnning_summary::getSummary($request));
			case 'postnatal':
				return $this->dataset($request,Postnatal_summary::getSummary($request));
			case 'eye':
				return $this->dataset($request,Eye_summary::getSummary($request));
			case 'dental':
				return $this->dataset($request,Dental_summary::getSummary($request));
			case 'child_health':
				return $this->dataset($request,Child_health_summary::getSummary($request));
			case 'dtc':
				return $this->dataset($request,DTC_summary::getSummary($request));
		}
	}
	
	private function allBooks(Request $request){
		$response =[];
		array_merge($response, $this->dataset($request,Opd_summary::getSummary($request)));
		array_merge($response, $this->dataset($request,Ipd_summary::getSummary($request)));
		array_merge($response, $this->dataset($request,Tracer_medicine_summary::getSummary($request)));
		array_merge($response, $this->dataset($request,Anc_summary::getSummary($request)));
		array_merge($response, $this->dataset($request,Bed_occupacy_summary::getSummary($request)));
		array_merge($response, $this->dataset($request,Family_planning_dataset::getSummary($request)));
		array_merge($response, $this->dataset($request,Postnatal_summary::getSummary($request)));
		array_merge($response, $this->dataset($request,Eye_summary::getSummary($request)));
		array_merge($response, $this->dataset($request,Dental_summary::getSummary($request)));
		array_merge($response, $this->dataset($request,Child_health_summary::getSummary($request)));
		return $response;		
	}
	
	public static function dataset($request,$summary){
		$payload = new \stdClass();
		$payload->dataSet = Config::get("DHIS.mtuha_books")[$request->mtuha_book];
		
		$payload->period = substr(str_replace("-","",$request->end_date),0,6);
		
		//otherwise change as they use different format as complete date
		switch($request->mtuha_book){
			case 'bed_occupancy':
				$payload->period = Date("Ymd");
		}
		
		//initially the field is set with the facility code. The server forwarding the 
		//request to DHIS will use the facility code to ask for DHIS orgUnit and overwrite the field
		$payload->orgUnit = Tbl_facility::where('id',$request->facility_id)->get()[0]->facility_code;
		if($request->complete)
			$payload->completeDate = Date("Y-m-d");
		$payload->dataValues = [];
		
		switch($request->mtuha_book){
			case 'opd':case 'ipd':
				$fields = "a,b,c,d,e,f,g,h,i,j";
				$dhis_data_values = ["male_under_one_month","female_under_one_month", "male_under_one_year", "female_under_one_year", "male_under_five_year", "female_under_five_year", "male_above_five_under_sixty", "female_above_five_under_sixty", "male_above_sixty", "female_above_sixty"];
				break;
			case 'tracer_medicine':
				$fields = "a,b,c,d,e";
				$dhis_data_values = ["service_provision","status","stock_out_flag_a","stock_out_flag_b","stock_out_flag_c"];
				break;
			case 'anc': case 'postnatal': case 'labour':
				$fields = 'a,b';
				$dhis_data_values = ["less_20","above_20"];
				break;
			case 'bed_occupancy':
				$fields = 'a,b';
				$dhis_data_values = ['martenity', 'non_martenity'];
				break;
			case 'family_planning':
				$fields = 'a,b,c,d,e';
				$dhis_data_values = ["between_10_14","between_15_19","between_20_24","25_and_above","marudio"];
				break;
			case 'eye': case 'dental':
				$fields = 'a,b,c,d,e,f';
				$dhis_data_values = ["male_less_5year","female_less_5year","male_between_5_14year","female_between_5_14year","male_above_15year","female_above_15year"];
				break;
			case 'child_health':
				$fields = 'a,b';
				$dhis_data_values = ["male","female"];
				break;
			case 'dtc':
				$fields = 'a,b,c,d,e,f';
				$dhis_data_values = ["male_less_moth","female_less_moth","male_moth_less_year","female_moth_less_year","male_year_five_year","female_year_five_year"];
				break;
					
		}
			
		$dhis_book_uid_maps = DB::select("select $fields from dhis_book_uid_maps where book='".Config::get("DHIS.mtuha_books")[$request->mtuha_book]."'");
		
		for($i = 0; $i < count($summary); $i++){
			//use the first entry to tell keys present in the objects
			$map = (array)$dhis_book_uid_maps[$i];
			$map_keys = array_keys($map);
			
			$data = (array)$summary[$i];//use the first entry to tell keys present in the summary
			$data_keys = array_keys($data);
			
			/* Since fields such as totals are not sent to dhis, they need be removed
			 * from the set. This way, the same report summary can be used on the
			 * got-homis interface and also sent to dhis */
			foreach($data_keys as $key)
				if(!in_array($key,$dhis_data_values))
					unset($data[$key]);
			
			$data_keys = $dhis_data_values;//reset to the list required. 
									   //Note that regeneration of the keys from the $data array
									   //leads to unaexplained and unintended reordering
									   
			for($j=0; $j < count($data); $j++){
				if(!$map[$map_keys[$j]])//N/A cells
					continue;
					
				//empty data values need not be sent
				if(!is_bool($data[$data_keys[$j]]) && empty($data[$data_keys[$j]]))
					continue;
					
				$cell = new \stdClass();
				$cell->dataElement = explode(".",$map[$map_keys[$j]])[0];
				$cell->categoryOptionCombo = explode(".",$map[$map_keys[$j]])[1];
				$cell->value = $data[$data_keys[$j]];
				$payload->dataValues[] = $cell;
			}
		}
		return $payload;
	}
		
	public function getOrgUnit($facility_code, $return_errors = true){
		//due to presence of installations that had removed the dash (-) from HFR, this logic
		//makes sure the dash is re-introduced before making request to DHIS
		$code = preg_replace("/[_-]/","",$facility_code);
		$code = substr_replace($code,"-",strlen($code)-1,0);
		
		
		$hfr_url = Config::get("DHIS.hfr").$code;
		$username = Config::get("DHIS.username");
		$password = Config::get("DHIS.password");
		
		$ch = curl_init($hfr_url);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$data = curl_exec($ch);
		if(curl_errno($ch)){
				$this->curl_errors = true;
				$this->error = array("status"=>"error", "description"=>"<span style='color:red'>Error communicating with the DHIS server. ".(strpos(curl_error($ch),"cv failure") != 0 || strpos(curl_error($ch),"to connect") != 0 || strpos(curl_error($ch), "timed out") != 0 ? "<br />Check your Internet connection. Also make sure you have the file named DHIS.php in the config folder. ": "")."</span>");
				file_put_contents("dhis_log.txt", Date('Y-m-d H:i:s')."    ".curl_error($ch).PHP_EOL, FILE_APPEND);
				curl_close($ch);
				return false;
		}
		curl_close($ch);
		
		//failure
		if(!is_object(json_decode($data))){
			$this->error = ["status"=>-1, "description"=>"<span style='color:red'>Could not decode the response from the DHIS server</span>","flag"=>$return_errors];
			return ($return_errors ? $this->error : false);
		}
		
		//could not find the code in DHIS
		if(count(json_decode($data)->organisationUnits) == 0){
			$this->error = ["status"=>false, "description"=>"<span style='color:red'>Your facility HFR code($facility_code) is not found in HFR Portal. Please make sure you entered a correct facility code during registration. <i style='font-weight:bold'>Refer http://hfrportal.ehealth.go.tz/index.php</i></span>","flag"=>$return_errors];
			return ($return_errors ? $this->error : false);
		}
		
		//code found
		return json_decode($data)->organisationUnits[0]->id;
	}
	
	/* Constructs the updater.php config file if not existing or outdated.
	 * Additions to this file are made through the updater script by overwriting it
	 */
	private function makeConfig(Request $request){
		$this_file = str_ireplace("\\","/",__FILE__);
		$root_path = substr($this_file,0, strpos($this_file,"/app/Http"));
		if(!file_exists($root_path."/config/DHIS.php") 
			|| (new \Datetime(date ("Y-m-d H:i:s",filemtime($root_path."/config/DHIS.php"))) < new \Datetime("2018-09-27 00:00:00"))){
				$template = file_get_contents($root_path."/app/Http/Controllers/Integrations/DHIS/dhis.template.php");
			file_put_contents($root_path."/config/DHIS.php",$template);
		}
		/* Always ensure we are working with current config files */
		Artisan::call('config:clear');
	}
}