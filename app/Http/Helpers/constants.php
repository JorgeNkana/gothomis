<?php 
    if (!defined('TOKEN_END_POINT'))
		define('TOKEN_END_POINT','https://verification.nhif.or.tz/nhifservice/Token');
	 
    if (!defined('SERVICE_END_POINT'))
		define('SERVICE_END_POINT','https://verification.nhif.or.tz/nhifservice/breeze/');
	 
    if (!defined('AUTHORIZATION_TOKEN_END_POINT'))
		define('AUTHORIZATION_TOKEN_END_POINT','https://verification.nhif.or.tz/nhifservice/Token');
     
    if (!defined('AUTHORIZATION_SERVICE_BASE_URL'))
		define('AUTHORIZATION_SERVICE_BASE_URL','https://verification.nhif.or.tz/nhifservice/breeze/'); 
     
    if (!defined('CLIENT_PHOTO_SERVICE'))
		define('CLIENT_PHOTO_SERVICE','https://verification.nhif.or.tz/Portal/breeze/Verification/GetImage?CardNo='); 
	
	 if (!defined('CLAIMS_RECONCILIATION'))
		define('CLAIMS_RECONCILIATION','https://verification.nhif.or.tz/claimsServer/api/v1/claims/'); 
    
     
    if (!defined('CLAIMS_TOKEN_END_POINT'))
		define('CLAIMS_TOKEN_END_POINT','https://verification.nhif.or.tz/claimsserver/Token');
     
    if (!defined('CLAIMS_SERVICE_BASE_URL'))
		define('CLAIMS_SERVICE_BASE_URL','https://verification.nhif.or.tz/claimsserver/api/v1/');
    
     
    if (!defined('EMR_ITEM_REGISTRATION'))
		define('EMR_ITEM_REGISTRATION','https://192.168.1.2/openmrs/ws/rest/v1/emr/addItem');

     
    if (!defined('EMR_REGISTRATION_END_POINT'))
		define('EMR_REGISTRATION_END_POINT','https://192.168.1.102/openmrs/ws/rest/v1/emr/Patient');
     
    if (!defined('EMR_SINGLE_SIGN_ON'))
		define('EMR_SINGLE_SIGN_ON','https://192.168.1.102/bahmni/home/index.html#/gothomis?');
     
    if (!defined('EMR_USER_REGISTRATION'))
		define('EMR_USER_REGISTRATION','https://192.168.1.102/openmrs/ws/rest/v1/emr/addUser');
     
    if (!defined('EMR_USER_PERMISSION_ROLE'))
		define('EMR_USER_PERMISSION_ROLE','https://192.168.1.102/openmrs/ws/rest/v1/emr/addRole');
    
    if (!defined('EMR_LAB_RESULTS_REPORT'))
		define('EMR_LAB_RESULTS_REPORT','https://192.168.1.102/openmrs/ws/rest/v1/emr/receiveLabOrderResult');
     
    if (!defined('EMR_ENCOUNTER'))
		define('EMR_ENCOUNTER','https://192.168.1.102/openmrs/ws/rest/v1/emr/encounter');

    
    if (!defined('PATIENT_BASE_URL'))
		define('PATIENT_BASE_URL','http://127.0.0.1:6520/');
    
    if (!defined('DASHBOARD_TOKEN_END_POINT'))
		define('DASHBOARD_TOKEN_END_POINT','http://127.0.0.1:6520/api/authenticate');


  
  
   
    
?>