<?php

namespace App\Http\Controllers\Integrations\HFR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HFR extends Controller
{
	protected $error;/* Hold an arbitrary error message */
	protected $curl_errors;/* Holds curl_error message encountered in helper functions */
	
	public function __construct(Request $request){
		$this->curl_errors = false;
	}
	
	/* 
	 * Contacts HFR to check for existence of the facility id
	 */
	public static function checkFacilityExistence($facility_code, $return_errors = false){
		//TODO
		
		$dhis = new \App\Http\Controllers\Integrations\DHIS\DHIS(new Request());
		$response = $dhis->getOrgUnit($facility_code,$return_errors);
		return (!$return_errors && $response === false ? false : ($return_errors && is_array($response) && $response['status'] === false ? $response : true));
	}
	
	/* 
	 * Updates the list of facilities
	 */
	public function updateLocalHFR(Request $request){
		//TODO
		
	}
}