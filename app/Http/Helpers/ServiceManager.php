<?php

use Illuminate\Contracts\Routing\ResponseFactory;
use App\Model\Nhif\ApiCredential;
use App\Model\Nhif\InsuaranceItem;
require("constants.php"); 

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ServiceManager
 *
 * @author arashid
 */
class ServiceManager 
{



    public function preApprovalServices($CardNo=null,$RefferenceNo=null,$ItemCode=null){
       
        $credentials=ApiCredential::where('active',1)->get();            
		if(count($credentials) ==0){
		return customApiResponse($credentials, "Provide API credentials", 400, ["error"=>"No API credentials provided by NHIF to your facility"]);
     	}
		$username=$credentials[0]->username;
		$password=$credentials[0]->password;		
		$authorizationHeader=$this->getAuthenticationHeader($username,$password);
        //echo $authorizationHeader;
        $request=SERVICE_END_POINT.'verification/GetReferenceNoStatus?CardNo='.$CardNo.'&ReferenceNo='.$RefferenceNo.'&ItemCode='.$ItemCode;
        //   return $request;
        $ch = curl_init($request);
        $request_method = 'GET';
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
           
           $authorizationHeader,
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
             ));
        $result = curl_exec($ch);
        $StatusCode=  curl_getinfo($ch,CURLINFO_HTTP_CODE);
            
        if($StatusCode == 200){
            $array_data = json_decode($result,true);
            $array_data['StatusCode'] = $StatusCode;
            $result = json_encode($array_data);
        }else{
            $array_data = array();
            $array_data['StatusCode'] = $StatusCode;
            $array_data['Message'] = $result;
            $result = json_encode($array_data);
        }

        curl_close($ch);
        return $result;
    
    
    }
    
    public function AuthorizeCard($CardNo,$VisitTypeID,$ReferralNo,$facility_id)
    {
     
       
       if($ReferralNo==''){
        $ReferralNo="0";
       }
       
		//get credentials 
				
		$credentials=ApiCredential::where('active',1)->get();            
		if(count($credentials) ==0){
		return customApiResponse($credentials, "Provide API credentials", 400, ["error"=>"No API credentials provided by NHIF to your facility"]);
     		
		}
		$username=$credentials[0]->username;
		$password=$credentials[0]->password;
		
		$authorizationHeader=$this->getAuthenticationHeader($username,$password);
        //echo $authorizationHeader;
        $request=SERVICE_END_POINT.'verification/AuthorizeCard?CardNo='.$CardNo.'&VisitTypeID='.$VisitTypeID.'&ReferralNo='.$ReferralNo;   
    //   return $request;
        $ch = curl_init($request);
        $request_method = 'GET';
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
           
           $authorizationHeader,
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
             ));
        $result = curl_exec($ch);
        $StatusCode=  curl_getinfo($ch,CURLINFO_HTTP_CODE);
            
        if($StatusCode == 200){
            $array_data = json_decode($result,true);
            $array_data['StatusCode'] = $StatusCode;
            $result = json_encode($array_data);
        }else{
            $array_data = array();
            $array_data['StatusCode'] = $StatusCode;
            $array_data['Message'] = $result;
            $result = json_encode($array_data);
        }

        curl_close($ch);
        return $result;
    }
    
    
    public function getAuthenticationHeader($username, $password)
    {
     
        // Construct the body for the STS request
        $authenticationRequestBody = 'grant_type=password&username='.$username.'&password='.$password;
        
        //Using curl to post the information to STS and get back the authentication response    
        $ch = curl_init();
        // set url 
     
        curl_setopt($ch, CURLOPT_URL, TOKEN_END_POINT); 
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
		
        return 'Authorization:' . $tokenOutput->{'token_type'}.' '.$tokenOutput->{'access_token'};

    }

    public function GetPricePackage()   {
       
        $credentials=ApiCredential::where('active',1)->get();            
		if(count($credentials) ==0){
		return customApiResponse($credentials, "Provide API credentials", 400, ["error"=>"No API credentials provided by NHIF to your facility"]);
     		
		}
        $FacilityCode = $credentials[0]->FacilityCode;
        $request=CLAIMS_SERVICE_BASE_URL.'packages/GetPricepackage?FacilityCode='.$FacilityCode;
        $username=$credentials[0]->username;
        $password=$credentials[0]->password;

        $authorizationHeader=$this->getAuthenticationHeader($username,$password);
        $ch = curl_init($request);
        $request_method = 'GET';
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
           
           $authorizationHeader,
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
             ));
        $result = curl_exec($ch);
        $StatusCode=  curl_getinfo($ch,CURLINFO_HTTP_CODE);
            
        if($StatusCode == 200){
            $array_data = json_decode($result,true);
            $array_data['StatusCode'] = $StatusCode;
            $result = json_encode($array_data);
        }else{
            $array_data = array();
            $array_data['StatusCode'] = $StatusCode;
            $array_data['Message'] = $result;
            $result = json_encode($array_data);
        }

        curl_close($ch);
        return $result;
    }  
}