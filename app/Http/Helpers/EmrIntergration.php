<?php

use Illuminate\Contracts\Routing\ResponseFactory;
/**  EMR INTEGRATION **/
if (!function_exists('emrIntegrationAPI')) {
	
   	 
   function emrIntegrationAPI($dataToEMR,$account_number_id,$patient_id,$bill_id,$user_id,$dept_id){
        
                
        $sql="SELECT * FROM tbl_next_of_kins t1 WHERE t1.patient_id='".$patient_id."'"; 
        $nextOfKins=DB::SELECT($sql);
        if(count($nextOfKins) >0 ){
        $next_of_kin_name=$nextOfKins[0]->next_of_kin_name;
        $next_of_kin_resedence_id=$nextOfKins[0]->residence_id;
        $relationship=$nextOfKins[0]->relationship;
        $mobile_number_next_kin=$nextOfKins[0]->mobile_number;
            
        }
        
        $first_name=$dataToEMR[0]->first_name;
        $middle_name=$dataToEMR[0]->middle_name;
        $last_name=$dataToEMR[0]->last_name;
        $gender=$dataToEMR[0]->gender;
        $mobile_number=$dataToEMR[0]->mobile_number;
        $residence_id=$dataToEMR[0]->residence_id;
        $dob=$dataToEMR[0]->dob;
        $marital_status=$dataToEMR[0]->marital_id;
        $occupation=$dataToEMR[0]->occupation_id;
        $tribe_id=$dataToEMR[0]->tribe_id;
        $country_id=$dataToEMR[0]->country_id;
        $user_id=$dataToEMR[0]->user_id;
      
        $account_number=$dataToEMR[0]->medical_record_number;

  
        $foliolist_array=array();                
        $patient_infos=array();
        $names=array();
        $visitTypes=array();
        $telecoms=array();
        $address=array();
        $identifications=array();
        $nextOFkin =array();
        $patient_infos['contact']=array();
        $patient_infos['telecom']=array();              
        $patient_infos['addresses']=array();   
        $entity_array =array();
                $patient_infos['identifier']=array();
               
                $entity_array["PatientResources"]=array();          
                $patient_infos["resourceType"]="Patient";           
                
                $identifications['identifierSourceUuid']=$patient_id;
                $identifications['value']=$account_number;
                
                array_push($patient_infos['identifier'],$identifications);      
                
    
                $patient_infos['name']=array();
                
                $patient_infos['visits']=array();
                
                $names["use"]="usual";
                $names["family"]="";
                $names["firstName"]=$first_name;
                $names['middleName']=$middle_name;
                $names['lastName']=$last_name;
                array_push($patient_infos['name'],$names);  
                
                $telecoms["system"]="phone";
                $telecoms["value"]=$mobile_number;
                $telecoms["use"]="work";                
               array_push($patient_infos['telecom'],$telecoms);
               
                $visitTypes["visitID"]=$account_number_id;
                $visitTypes["paymentsCategories"]=$bill_id;
                $visitTypes["senderID"]=$user_id;
                $visitTypes["clinicID"]=$dept_id;
                $visitTypes["dateAttended"]=date('Y-m-d');              
               array_push($patient_infos['visits'],$visitTypes);
                
                $patient_infos["gender"]=$gender;           
                $patient_infos["birthDate"]=$dob;           
                $patient_infos["deceasedBoolean"]=false;
                $patient_infos["maritialStatus"]=$marital_status;                   
                
                $address["use"]="home";              
                $address["street"]=$residence_id;
                array_push($patient_infos['addresses'],$address);
           if(count($nextOfKins) >0 ){
                $nextOFkin['relationship']=$relationship;
                $nextOFkin['name']=$next_of_kin_name;
                $nextOFkin['mobile']=$mobile_number_next_kin;
                $nextOFkin['address']=$next_of_kin_resedence_id;
                array_push($patient_infos['contact'],$nextOFkin);
           }

              $patient_infos["active"]=true;            
                
            array_push($foliolist_array,$patient_infos);


        
        $entity_array["PatientResources"]=$foliolist_array;
        $data_string=json_encode($entity_array,JSON_PRETTY_PRINT);
      
        $request_method = 'POST';               
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,EMR_REGISTRATION_END_POINT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response  = curl_exec($ch);
        $StatusCode=  curl_getinfo($ch,CURLINFO_HTTP_CODE);

        curl_close($ch);
        return $response;
    
        
        
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


if (!function_exists('reportElectonically')) {
//Report results electronically...
    function reportElectonically($visit_id){
        $foliolist_array=array();
        $patient_infos=array();
        $tests=array();
         $identifications=array();

         $sql="SELECT t1.* FROM tbl_remote_orders t1 
              INNER JOIN tbl_integration_permissions t2 ON t1.integration_permission_id=t2.id
              WHERE   t2.allowed=1	
                AND   t1.visit_date_id='".$visit_id."'";
              $is_remotely_posted= DB::SELECT($sql);

              if(count($is_remotely_posted) >0){
            
                $investigation_result= "select * from vw_remote_investigation_results where account_id = '".$visit_id."'";
                $investigation_results=DB::SELECT($investigation_result);
                
                
              $entity_array =array(); 
        
                $patient_infos['identifier']=array();		 
                 
          $patient_infos["resourceType"]="Investigation Report";               
          $identifications['identifierSourceUuid']=$investigation_results[0]->patient_id;
          $identifications['value']=$investigation_results[0]->account_id;
          array_push($patient_infos['identifier'],$identifications);      
                
    
                $patient_infos['test']=array();
                foreach( $investigation_results AS $report){
                
                $tests["use"]="Tests";                
                $tests["orderNumber"]=$report->emr_order_number;
                $tests["item_id"]=$report->item_id;
                $tests["item_name"]=$report->item_name;
                $tests["results"]=$report->description;
                $tests["verifiedBy"]=$report->verify_user;
                array_push($patient_infos['test'],$tests); 
                }
                
                array_push($foliolist_array,$patient_infos);        
                $entity_array["PatientResources"]=$foliolist_array;
                $data_string=json_encode($entity_array,JSON_PRETTY_PRINT);

                          
        $request_method = 'POST';				
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, EMR_LAB_RESULTS_REPORT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response  = curl_exec($ch);
        $StatusCode=  curl_getinfo($ch,CURLINFO_HTTP_CODE);
    
        curl_close($ch);
        return $response;
              }
            
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