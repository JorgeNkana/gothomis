<?php

use Illuminate\Contracts\Routing\ResponseFactory;
/**  Search Patient **/
if (!function_exists('seachForPatients')) {
	
   	 
    function seachForPatients($request) {
		$searchKey = $request->input('searchKey');
		$patientSearched="SELECT t1.* FROM vw_patients_search t1 
		     WHERE   medical_record_number LIKE '%".$searchKey."%'
		           	 GROUP BY patient_id  LIMIT 15";	
					
		 return DB::SELECT($patientSearched);
	}
}

if (!function_exists('gen_uuid')) {
function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
 }
}







if (!function_exists('cardVerificationNHIF')) {
	
   	 
    function cardVerificationNHIF($request) {
		
	 header("Content-Type:application/json");
        $manager=new ServiceManager();
        $result="";
      
        $cardNo =     $request->cardNo;
        $VisitTypeID= $request->VisitTypeID;
        $facility_id= $request->facility_id;
        $ReferralNo=  $request->ReferralNo;

        //$result=$manager->AuthorizeCard($cardNo);//Current
        $result=$manager->AuthorizeCard($cardNo,$VisitTypeID,$ReferralNo,$facility_id);//New implementation of the API that include new parameters

        return $result;
	}
}

if (!function_exists('priceItemsNhif')) {
    
     
    function priceItemsNhif() {
        
     header("Content-Type:application/json");
        $manager=new ClaimSubmission();
        $result="pp";
      $result=$manager->GetPricePackage();
      //New implementation of the API that include new parameters

        return $result;
    }
}