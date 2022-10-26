<?php
use Illuminate\Contracts\Routing\ResponseFactory;


if (!function_exists('authenticateApplication')) {
  function authenticateApplication($facility_id)
    {


        //get credentials 
        $sql="SELECT t1.* FROM tbl_integrating_keys t1 
              WHERE t1.facility_id='".$facility_id."' 
              AND api_type=5";

              //get credentials 
        $facility ="SELECT facility_code FROM tbl_facilities t1 WHERE t1.id='".$facility_id."'";

        $facilityCoder =DB::SELECT($facility);  

            
        $credentials=DB::SELECT($sql);
        if(count($credentials) ==0){
            return "No API credentials provided TAMISEMI";
        return customApiResponse($credentials, "Provide API credentials", 400, ["error"=>"No API credentials provided TAMISEMI"]);
            
        }
        $username=$credentials[0]->public_keys;
        $password=$credentials[0]->private_keys;

        $facility_code=$facilityCoder[0]->facility_code;
        
        // Construct the body for the STS request
        $authenticationRequestBody = 'facilityCode='.$facility_code.'&email='.$username.'&password='.$password;
        
        //Using curl to post the information to STS and get back the authentication response    
        $ch = curl_init();
        // set url 
     
        curl_setopt($ch, CURLOPT_URL, DASHBOARD_TOKEN_END_POINT); 
        // Get the response back as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        // Mark as Post request
        curl_setopt($ch, CURLOPT_POST, 1);
        // Set the parameters for the request
        curl_setopt($ch, CURLOPT_POSTFIELDS,  $authenticationRequestBody);
        
        // By default, HTTPS does not work with curl.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // read the output from the post request
        $output = curl_exec($ch);         
        // close curl resource to free up system resources
        curl_close($ch);      
        // decode the response from sts using json decoder
        $tokenOutput = json_decode($output);
        if(!isset($tokenOutput)){
        return customApiResponse($tokenOutput, "No Connectivity", 400, ["error"=>"System Could not fetch Authentication Tokens due to connectivity issues, check your internet"]);
        }
 
             $output = json_decode($output,true);
      if(isset($output['error']) ){
        return $output['error'];

      }

        return 'Authorization: Bearer '.$tokenOutput->{'token'};

    }
}


if (!function_exists('createSearchRequest')) {

function createSearchRequest($searchKey) {
      $foliolist_array=array();
        $patient_profile['PATIENT_PROFILE'] = array();  
        $particulars['PATIENT_PROFILE'] = array();      
        $patient_info=array();  
        $patient_particulars['searchKey']= $searchKey;
        array_push($particulars['PATIENT_PROFILE'],$patient_particulars);
        array_push($foliolist_array,$particulars);       
        $data_string=json_encode($foliolist_array,JSON_PRETTY_PRINT);
       
        return $data_string;
}
}



if (!function_exists('createPatientProfile')) {

function createPatientProfile($facility_id,$patient_id) {
     $responses=[];
     $sql="SELECT first_name,middle_name,last_name,gender,dob,medical_record_number,facility_code,residence_name,t1.mobile_number,residence_id     
         FROM tbl_patients t1
         INNER JOIN tbl_facilities t2 ON t1.facility_id=t2.id 
         INNER JOIN tbl_residences t3 ON t3.id=t1.residence_id
         WHERE facility_id='".$facility_id."'
           AND t1.id='".$patient_id."' LIMIT 15";  


	       $responses[]=DB::SELECT($sql); 


   $sql_1 = "select description,date_attended from vw_history_examinations where patient_id = '".$patient_id."' AND facility_id = '".$facility_id."'
        AND description IS NOT NULL AND duration IS NOT NULL AND duration_unit IS NOT NULL 
          UNION
        select  other_complaints AS description,date_attended from vw_history_examinations where patient_id = '".$patient_id."' AND facility_id = '".$facility_id."'
        AND other_complaints IS NOT NULL GROUP BY description ORDER BY date_attended DESC LIMIT 3";
    
      $responses[]=DB::SELECT($sql_1); 


       $sql_2 = "select hpi,date_attended from vw_history_examinations where patient_id = '".$patient_id."' AND facility_id = '".$facility_id."'
        AND hpi IS NOT NULL GROUP BY hpi ORDER BY date_attended DESC LIMIT 3";
    
      $responses[]=DB::SELECT($sql_2); 

       $sql_3="SELECT t1.*,t3.code AS disease_code,t4.name  AS created_by,DATE(t1.created_at) AS date_attended,t1.created_at AS time_created,t1.updated_at AS last_modified   FROM tbl_diagnoses t1 
           INNER JOIN tbl_diagnosis_details t2 ON   t2.diagnosis_id =t1.id
           INNER JOIN tbl_diagnosis_descriptions t3 ON   t2.diagnosis_description_id =t3.id 
           INNER JOIN users t4 ON t4.id=t1.user_id
           WHERE t1.patient_id='".$patient_id."' ORDER BY created_at DESC LIMIT 3";
           $responses[]=DB::SELECT($sql_3); 


   
        $foliolist_array=array();
        $patient_profile['PATIENT_PROFILE'] = array();        
        $patient_info=array();	
        $patient_particulars=array(); 	
        $particulars['PATIENT_PROFILE'] = array();  
        $clerksheet['CLERK_SHEET'] = array(); 
        $diagnosis_details['DIAGNOSIS'] = array();  
        $patient_history=array(); 
        $diagnosis=array();
        $nonFullSupplyProds=array(); 
        $hpi= array();       
        $items_array =array();
        $complains=array();
        $otherComponents['quantityRequested']=0;
        $otherComponents['reasonForRequestedQuantity']="string";


           foreach($responses[0] as $row) {                       
            $patient_particulars['MRN']=$row->medical_record_number;
            $patient_particulars['facilityCode']=$row->facility_code;
            $patient_particulars['firstName']=$row->first_name;
            $patient_particulars['middleName']=$row->middle_name;
            $patient_particulars['lastName']=$row->last_name;
            $patient_particulars['sex']=$row->gender;
            $patient_particulars['dob']= $row->dob;
            $patient_particulars['addressCode']= $row->residence_id;
            $patient_particulars['addressDescription']= $row->residence_name;   
            $patient_particulars['mobileNumber']= $row->mobile_number;
            array_push($particulars['PATIENT_PROFILE'],$patient_particulars);      
                    
        }

        foreach($responses[1] as $row) {                       
            $patient_history['chiefComplain']=$row->description;
            $patient_history['dateAttended']=$row->date_attended;
          
            array_push($clerksheet['CLERK_SHEET'],$patient_history);  
           
        }
         array_push($particulars['PATIENT_PROFILE'],$clerksheet); 

        $presentingIllnessHist['HPI']=array();
         foreach($responses[2] as $row) {                       
            $hpi['hpi']=$row->hpi;
            $hpi['dateAttended']=$row->date_attended;          
            array_push($presentingIllnessHist['HPI'],$hpi); 
        }
          array_push($particulars['PATIENT_PROFILE'],$presentingIllnessHist); 


        
         foreach($responses[3] as $row) {                       
            $diagnosis['disease_code']=$row->disease_code;
            $diagnosis['dateAttended']=$row->date_attended;
            array_push($diagnosis_details['DIAGNOSIS'],$diagnosis); 
        }
          array_push($particulars['PATIENT_PROFILE'],$diagnosis_details); 


         array_push($foliolist_array,$particulars);       
         $data_string=json_encode($foliolist_array,JSON_PRETTY_PRINT);
       
        return $data_string;
    }
}



if (!function_exists('createPatientRefferal')) {

function createPatientRefferal($facility_id,$patient_id) {
     $responses=[];
     $sql="SELECT first_name,middle_name,last_name,gender,dob,medical_record_number,facility_code,residence_name,t1.mobile_number,residence_id     
         FROM tbl_patients t1
         INNER JOIN tbl_facilities t2 ON t1.facility_id=t2.id 
         INNER JOIN tbl_residences t3 ON t3.id=t1.residence_id
         WHERE facility_id='".$facility_id."'
           AND t1.id='".$patient_id."' LIMIT 15";  


         $responses[]=DB::SELECT($sql); 


   $sql_1 = "select description,date_attended from vw_history_examinations where patient_id = '".$patient_id."' AND facility_id = '".$facility_id."'
        AND description IS NOT NULL AND duration IS NOT NULL AND duration_unit IS NOT NULL 
          UNION
        select  other_complaints AS description,date_attended from vw_history_examinations where patient_id = '".$patient_id."' AND facility_id = '".$facility_id."'
        AND other_complaints IS NOT NULL GROUP BY description ORDER BY date_attended DESC LIMIT 3";
    
      $responses[]=DB::SELECT($sql_1); 


       $sql_2 = "select hpi,date_attended from vw_history_examinations where patient_id = '".$patient_id."' AND facility_id = '".$facility_id."'
        AND hpi IS NOT NULL GROUP BY hpi ORDER BY date_attended DESC LIMIT 3";
    
      $responses[]=DB::SELECT($sql_2); 

       $sql_3="SELECT t1.*,t3.code AS disease_code,t4.name  AS created_by,DATE(t1.created_at) AS date_attended,t1.created_at AS time_created,t1.updated_at AS last_modified   FROM tbl_diagnoses t1 
           INNER JOIN tbl_diagnosis_details t2 ON   t2.diagnosis_id =t1.id
           INNER JOIN tbl_diagnosis_descriptions t3 ON   t2.diagnosis_description_id =t3.id 
           INNER JOIN users t4 ON t4.id=t1.user_id
           WHERE t1.patient_id='".$patient_id."' ORDER BY created_at DESC LIMIT 1";
           $responses[]=DB::SELECT($sql_3); 

            $sql_4="SELECT t1.*,t4.name  AS reffered_by,t5.facility_code AS receiver_facility_id, DATE(t1.created_at) AS date_attended  FROM tbl_refferal_externals t1 
                   INNER JOIN users t4 ON t4.id=t1.reffered_by
                   INNER JOIN tbl_facilities t5 ON t5.id=t1.receiver_facility_id
                   WHERE t1.patient_id='".$patient_id."' ORDER BY created_at DESC LIMIT 1";
           $responses[]=DB::SELECT($sql_4); 


   
        $foliolist_array=array();
        $patient_profile['PATIENT_PROFILE'] = array();        
        $patient_info=array();  
        $patient_particulars=array();   
        $particulars['PATIENT_PROFILE'] = array();  
        $clerksheet['CLERK_SHEET'] = array(); 
        $diagnosis_details['DIAGNOSIS'] = array();  
        $refferal['REFFERAL'] = array();  
        $refferals=array();  
        $patient_history=array(); 
        $diagnosis=array();
        $nonFullSupplyProds=array(); 
        $hpi= array();       
        $items_array =array();
        $complains=array();
        $otherComponents['quantityRequested']=0;
        $otherComponents['reasonForRequestedQuantity']="string";


           foreach($responses[0] as $row) {                       
            $patient_particulars['MRN']=$row->medical_record_number;
            $patient_particulars['facilityCode']=$row->facility_code;
            $patient_particulars['firstName']=$row->first_name;
            $patient_particulars['middleName']=$row->middle_name;
            $patient_particulars['lastName']=$row->last_name;
            $patient_particulars['sex']=$row->gender;
            $patient_particulars['dob']= $row->dob;
            $patient_particulars['addressCode']= $row->residence_id;
            $patient_particulars['addressDescription']= $row->residence_name;   
            $patient_particulars['mobileNumber']= $row->mobile_number;
            array_push($particulars['PATIENT_PROFILE'],$patient_particulars);      
                    
        }

        foreach($responses[1] as $row) {                       
            $patient_history['chiefComplain']=$row->description;
            $patient_history['dateAttended']=$row->date_attended;
          
            array_push($clerksheet['CLERK_SHEET'],$patient_history);  
           
        }
         array_push($particulars['PATIENT_PROFILE'],$clerksheet); 

        $presentingIllnessHist['HPI']=array();
         foreach($responses[2] as $row) {                       
            $hpi['hpi']=$row->hpi;
            $hpi['dateAttended']=$row->date_attended;          
            array_push($presentingIllnessHist['HPI'],$hpi); 
        }
          array_push($particulars['PATIENT_PROFILE'],$presentingIllnessHist); 


        
         foreach($responses[3] as $row) {                       
            $diagnosis['disease_code']=$row->disease_code;
            $diagnosis['dateAttended']=$row->date_attended;
            array_push($diagnosis_details['DIAGNOSIS'],$diagnosis); 
        }
         array_push($particulars['PATIENT_PROFILE'],$diagnosis_details); 

         foreach($responses[4] as $row) {                       
            $refferals['patient_condition']=$row->patient_condition;
            $refferals['preparation_needed']=$row->preparation_needed;
            $refferals['reffered_by']=$row->reffered_by;            
            $refferals['dateAttended']=$row->date_attended;
            array_push($refferal['REFFERAL'],$diagnosis); 
        }
       array_push($particulars['PATIENT_PROFILE'],$refferals); 


         array_push($foliolist_array,$particulars);       
         $data_string=json_encode($foliolist_array,JSON_PRETTY_PRINT);
       
        return $data_string;
    }
}


















?>