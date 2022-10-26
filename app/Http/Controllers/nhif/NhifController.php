<?php

namespace App\Http\Controllers\nhif;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\nhif\Tbl_insuarance_mapping_item;
use DB;
use Validator;
use ClaimSubmission;
use App\Model\nhif\Tbl_nhif_file;
use Auth;
use Storage;
use ServiceManager;
use App\Model\Patient\Tbl_accounts_number;
use App\Model\nhif\Tbl_bulk_claim;
error_reporting(E_ALL ^ E_DEPRECATED);


class NhifController extends Controller
{

	public function __construct()
	{
		// Apply the jwt.auth middleware to all methods in this controller
		// except for the authenticate method. We don't want to prevent
		// the user from retrieving their token if they don't already have it
		$this->middleware('jwt.auth', ['except' => ['authenticate']]);
    
    }
	
	
		//GET CLAIM RECONCILIATIONS
	
	 public function getNhifClaimReconciliationList(Request $request){
     	$claim_year    = $request->claim_year;
     	$claim_month= $request->claim_month;     
     		
        $sql="SELECT submission_id, date_submited,facility_code,claim_year,claim_month,folio_number,submited_by 
		      FROM tbl_nhif_claims_reconciliations t1          		 
              WHERE  t1.claim_year ='".$claim_year."'
              AND    t1.claim_month ='".$claim_month."'  GROUP BY t1.submission_id ORDER BY t1.folio_number ASC";
        
	     return DB::SELECT($sql); 
    }
	
	public function createClaimReconciliation(Request $request){   
            
			$claimYear  = $request->claimYear;
			$claimMonth = $request->claimMonth;
            $claim  =  new ClaimSubmission();
    	    $returned_items=$claim->claimReconciliation($claimYear,$claimMonth);
    	    $items = json_decode($returned_items, true);
			//return  $items ;
         
    	    foreach ($items as $value) {
				
			   $SubmissionID   =$value['SubmissionID'];
    	       $DateSubmitted  =$value['DateSubmitted'];
    	       $ClaimYear      =$value['ClaimYear'];
    	       $ClaimMonth     =$value['ClaimMonth'];
    	       $FolioNo        =$value['FolioNo'];
			   $FacilityCode   =$value['FacilityCode'];
    	       $SubmittedBy    =$value['SubmittedBy'];
			   
			   $sql="INSERT IGNORE INTO tbl_nhif_claims_reconciliations(submission_id,date_submited,facility_code,claim_year,claim_month,folio_number,submited_by,created_at,updated_at)
                    VALUES('".$SubmissionID."','".$DateSubmitted."','".$FacilityCode."','".$ClaimYear."','".$ClaimMonth."', '".$FolioNo."','".$SubmittedBy."',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
					
					DB::statement($sql);
    	     
    	       
		   }
    	     
    }

     
    public function store(Request $request)
    {
         //modify parameter with item_id,card_number,authorization_number
        //
       
        $item_id                =$request->item_id;
        $card_number            =$request->card_number;
        $authorization_number   =$request->authorization_number;
        


        $sql="SELECT * FROM tbl_insuarance_items t1
              INNER JOIN tbl_insuarance_item_prices t2 ON t2.item_code=t1.item_code
              WHERE is_restricted=1 AND  t1.gothomis_item_id='".$item_id."'";
        $check_restrict_status=DB::SELECT($sql);
        

        if(count($check_restrict_status)>0){
            $sql="SELECT * FROM tbl_nhif_approval_remarks t1
            INNER JOIN tbl_insuarance_items t2 ON t1.item_code=t2.item_code
            INNER JOIN tbl_insuarance_item_prices t3 ON t3.item_code=t2.item_code
            WHERE t1.authorization_number='".$authorization_number."' AND  t1.card_number='".$card_number."' AND   is_restricted=1 AND  t2.gothomis_item_id='".$item_id."'";
			$check_if_permited= DB::SELECT($sql);

			if(count( $check_if_permited)==0){
				return response()->json([
					'Message' => 'This service require pre approval from NHIF , please ask for it.',
					'status' => 404
				]);
			}

        } 
	}

	public function verifyNhifCard(Request $request)
	{
           
        $manager=new ServiceManager();
        $result="";      
        $cardNo =     $request->card_number;
        $VisitTypeID= 1;
        $facility_id= Auth::user()->facility_id;
        $ReferralNo=  0;
        $account_id =     $request->account_id;

         //$result=$manager->AuthorizeCard($cardNo);//Current
        $result=$manager->AuthorizeCard($cardNo,$VisitTypeID,$ReferralNo,$facility_id);//New implementation of the API that include new parameters
        $result= json_decode($result,true);
        $AuthorizationNo  =$result['AuthorizationNo'];
        $MembershipNo     =$result['MembershipNo'];

        Tbl_accounts_number::where('id',$account_id)->update(['authorization_number'=>$AuthorizationNo,
                                                              'membership_number'=>$MembershipNo]);

        return $result;

	}


    public function getNonCollectedCards(Request $request)
    {
		 $end_date=$request->end;
        $start_date=$request->start;
		
		if(!$start_date)
		{
         $sql="SELECT t1.* ,t2.account_number,t2.id AS account_id,card_no,authorization_number,membership_number,t2.date_attended AS attended_time,
           t4.name AS registered_by
         FROM tbl_patients t1
         INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id and scheme_id is not null
         INNER JOIN users t4 ON t4.id=t1.user_id
           WHERE t2.card_no IS NOT NULL AND visit_close=1";
		}else{
			 $sql="SELECT t1.* ,t2.account_number,t2.id AS account_id,card_no,authorization_number,membership_number,t2.date_attended AS attended_time,
           t4.name AS registered_by
         FROM tbl_patients t1
         INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id and scheme_id is not null
         INNER JOIN users t4 ON t4.id=t1.user_id
           WHERE t2.card_no IS NOT NULL AND visit_close=1 AND t2.date_attended between '$start_date' and '$end_date'";
		}
       return DB::SELECT($sql); 

         
	}

	public function getNonVerified(Request $request)
    {
        $end_date=$request->end;
        $start_date=$request->start;
        $sql="SELECT t1.* ,t2.patient_id,t3.facility_code,t2.account_number,t2.id AS account_id,card_no,authorization_number,membership_number,DATE(t2.created_at) AS attended_date,t3.facility_name,t3.address,
          TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) AS age ,t4.practioner_no,t4.name  AS created_by,t2.created_at AS time_created,t2.updated_at AS last_modified,t7.prof_name                
         FROM tbl_patients t1
         INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id
         INNER JOIN users t4 ON t4.id=t2.user_id
         INNER JOIN tbl_facilities t3 ON t3.id=t2.facility_id   
         INNER JOIN tbl_proffesionals t7 ON t4.proffesionals_id=t7.id                  
         WHERE ((t2.created_at BETWEEN '".$start_date."' AND  '".$end_date."' AND 
		 NOT EXISTS (SELECT patient_id FROM tbl_admissions t_a1 WHERE t_a1.account_id = t2.id AND t_a1.admission_status_id IN(4,5,7,8,9) 
		 AND DATE(t_a1.updated_at) > DATE('$end_date'))) 
		 OR EXISTS (SELECT patient_id FROM tbl_admissions t_a2 WHERE t_a2.account_id = t2.id AND t_a2.admission_status_id IN(4,5,7,8,9) 
		 AND DATE(t_a2.updated_at) BETWEEN DATE('$start_date') AND DATE('$end_date'))) AND t2.authorization_number IS 
		 NULL AND t2.card_no IS NOT NULL
        GROUP BY card_no";// LIMIT 35";
       return DB::SELECT($sql); 

         
    }

	public function eMLISTesting(Request $request)
	{
		return eLMISFolioCreation();
	}

	public function mapFacilityCode(Request $request)
	{
		$facility_id= Auth::user()->facility_id;
		try{
			$sql="UPDATE tbl_facilities t1 SET t1.nhif_facility_code='".$request->facility_code."' WHERE t1.id='".$facility_id."'";

			if(DB::statement($sql)){
			   return response()->json([
					'message' => 'Successfully Saved',
					'error' => 'success'
				]);
			}
		}catch(\Exception $e){
			return response()->json([
				'message' => 'Faled to Save, with error logs '.$e,
				'error' => 'error'
			]);
		}
	} 

	public function giveCards(Request $request)
	{
		$user_id= Auth::user()->id;
		$account_id= $request->account_id;
		try{
		 $sql="UPDATE tbl_accounts_numbers  set closed_by='".$user_id."', visit_close=0 
			   WHERE  id='".$account_id."'";

		 if(DB::statement($sql)){
		   return response()->json([
				'Message' => 'Card was successfully given ',
				'status' => 'success'
			]);
		 }
		}catch(\Exception $e){

		  return response()->json([
				'Message' => 'Faled to Process, with error logs '.$e,
				'status' => 'error'
			]);

		}
	} 



	public function getAmountsClaimed(Request $request)
	{
         $facility_id=$request->facility_id;
         $start_date = Date('Y-m-d',strtotime($request->start_time));
         $end_date   =  Date('Y-m-d',strtotime($request->end_time));
         
		createClaimSummary($facility_id, $start_date, $end_date);
	}




	public function getClaimsDetails(Request $request)
	{
		$responses=[];
		$visit_id=$request->account_id;
		
		//Consultation
		$sql="SELECT t6.id AS visit_id,t4.amount AS item_price,t3.item_name,t3.item_code, 1 as quantity
			   FROM  tbl_invoice_lines  t1
			   INNER JOIN tbl_encounter_invoices t5 ON  NOT EXISTS (SELECT patient_id FROM tbl_admissions t_a1 WHERE t_a1.account_id = $visit_id) AND t5.id = t1.invoice_id AND t5.account_number_id = $visit_id
 			   INNER JOIN tbl_insuarance_item_mapping t2 ON t2.gothomis_item_id = t1.item_id           
			   INNER JOIN tbl_insuarance_items t3 ON t3.id=t2.nhif_item_id AND t3.item_type_id=1   
			   INNER JOIN tbl_insuarance_item_prices t4 ON t4.item_code=t3.item_code 
			   INNER JOIN tbl_accounts_numbers t6 ON   t6.id = t5.account_number_id and t6.scheme_id=t4.scheme_code
			   GROUP BY t4.item_code ";
		$consultations=DB::SELECT($sql);
		
		//Investigations
		$sql  ="SELECT t3.item_name,t3.item_code,t5.amount as item_price, 1 as quantity
				FROM tbl_orders t2
				INNER JOIN tbl_results t1 ON t1.visit_date_id = $visit_id AND t2.visit_date_id = $visit_id AND t2.order_id = t1.order_id AND t1.item_id = t2.test_id AND t1.confirmation_status = 1    
			    INNER JOIN tbl_requests t4 ON t4.id=t2.order_id       
				INNER JOIN tbl_insuarance_item_mapping t7 ON t7.gothomis_item_id = t2.test_id           
				INNER JOIN tbl_insuarance_items t3 ON t3.id=t7.nhif_item_id    
				INNER JOIN tbl_insuarance_item_prices t5 ON t3.item_code=t5.item_code 
				INNER JOIN tbl_accounts_numbers t6 ON t6.id = t4.visit_date_id AND t6.scheme_id=t5.scheme_code          
				WHERE t4.visit_date_id='".$visit_id."' GROUP BY t3.item_code";
		$investigations=DB::SELECT($sql); 
		
		//Diagnoses
		$sql="SELECT t3.code, t2.status, users.name, users.practioner_no, tbl_proffesionals.prof_name as proffesion 
				FROM tbl_diagnoses t1 
				INNER JOIN tbl_diagnosis_details t2 ON   t2.diagnosis_id =t1.id
				INNER JOIN tbl_diagnosis_descriptions t3 ON   t2.diagnosis_description_id =t3.id
				INNER JOIN users ON users.id = t1.user_id
				INNER JOIN tbl_proffesionals on users.proffesionals_id = tbl_proffesionals.id
				WHERE t1.visit_date_id='".$visit_id."' and t2.status = 'confirmed'";
		$diagnoses=DB::SELECT($sql); 

		//Prescriptions
		$sql="SELECT t2.item_name,t2.item_code,t3.amount AS item_price, SUM(t1.quantity)
			   FROM tbl_prescriptions t1 
			   INNER JOIN tbl_insuarance_item_mapping t7 ON t7.gothomis_item_id = t1.item_id     
			   INNER JOIN tbl_insuarance_items t2 ON t2.id=t7.nhif_item_id    
			   INNER JOIN tbl_insuarance_item_prices t3 ON t3.item_code=t2.item_code    
			   INNER JOIN tbl_accounts_numbers t4 ON   t4.id = t1.visit_id AND t4.scheme_id=t3.scheme_code
			   WHERE t1.visit_id='".$visit_id."' AND t1.dispensing_status=1 GROUP BY t2.item_name,t2.item_code,t3.amount";
		$prescriptions=DB::SELECT($sql); 

		//Admission
		$sql="SELECT t1.admission_status_id,t3.ward_name,t4.item_code,CONCAT(t4.item_name, ' (FROM ',DATE(t1.created_at), ' TO ',DATE(t7.updated_at),')') AS item_name, DATE(t1.created_at) AS admission_date,t5.amount AS item_price, CASE WHEN abs(timestampdiff(day, t7.created_at, t1.admission_date)) = 0 THEN 1 ELSE abs(timestampdiff(day, t7.created_at, t1.admission_date)) END as quantity
				FROM  tbl_admissions t1
				INNER JOIN tbl_instructions t2 ON t2.admission_id=t1.id
				INNER JOIN tbl_wards t3 ON t2.ward_id=t3.id
				INNER JOIN tbl_insuarance_item_mapping t8 ON t8.gothomis_item_id = t3.ward_class_id     
				INNER JOIN tbl_insuarance_items t4 ON t4.id=t8.nhif_item_id    
				INNER JOIN tbl_insuarance_item_prices t5 ON t5.item_code=t4.item_code    
				INNER JOIN tbl_accounts_numbers t6 ON   t6.id = t1.account_id AND t6.scheme_id=t5.scheme_code 
				INNER JOIN  tbl_discharge_permits t7 ON t7.admission_id=t1.id AND t7.confirm=1
				WHERE t1.account_id='".$visit_id."'
			    GROUP BY t1.account_id";
		$admissions= DB::SELECT($sql);
			 
		
		//Procedures
		$sql="SELECT t2.item_name,t2.item_code,t3.amount AS item_price, COUNT(*) as quantity
				FROM  tbl_patient_procedures  t1 
				INNER JOIN tbl_insuarance_item_mapping t7 ON t7.gothomis_item_id = t1.item_id     
				INNER JOIN tbl_insuarance_items t2 ON t2.id=t7.nhif_item_id    
				INNER JOIN tbl_insuarance_item_prices t3 ON t3.item_code=t2.item_code
				INNER JOIN tbl_accounts_numbers t4 ON   t4.id = t1.visit_date_id AND t4.scheme_id=t3.scheme_code 
				WHERE t1.visit_date_id='".$visit_id."' GROUP BY t2.item_name,t2.item_code,t3.amount";
		$procedures=DB::SELECT($sql);

		$serialNo = DB::select("SELECT CONCAT(facilitycode,'/',LPAD(MONTH(t1.date_attended),2,0), '/',YEAR(t1.date_attended),'/', LPAD(COUNT(*)+1,5,0)) AS serialNo FROM (tbl_accounts_numbers t1 JOIN tbl_accounts_numbers t2 ON t2.id = $visit_id AND t1.is_submitted = 1 AND YEAR(t1.date_attended) AND YEAR(t2.date_attended) AND MONTH(t1.date_attended) = MONTH(t2.date_attended)), tbl_api_credentials")[0]->serialNo;
		
		return array("consultations"=>$consultations,
					  "investigations"=>$investigations,
					  "diagnoses"=>$diagnoses,
					  "prescriptions"=>$prescriptions,
					  "procedures"=>$procedures,
					  "admissions"=>$admissions,
					  "clinician"=>(count($diagnoses) > 0 ? $diagnoses[0] : new \stdClass()),
					  "serialNo"=>$serialNo);
	}
	
	
	public function generateFiles(Request $request)
	{
		$account_id=$request->account_id;
		
		//do not proceed for admitted patients who are not dischaged in the system
		if(COUNT(DB::select("SELECT patient_id FROM tbl_admissions WHERE account_id = $account_id")) > 0 && COUNT(DB::select("SELECT patient_id FROM tbl_admissions INNER JOIN tbl_discharge_permits ON tbl_admissions.account_id = $account_id AND tbl_discharge_permits.admission_id = tbl_admissions.id AND tbl_discharge_permits.Confirm = 1")) == 0){
			return response()->json([
						"Message"=>"Cannot proceed since the client is still admitted. Please discharge first",
						"statusCode"=>404
					]);
		}
		
		
		//do not proceed for patient categories that are not mapped with prices
		$missing_pricing = DB::select("SELECT scheme_id FROM tbl_accounts_numbers t1 JOIN tbl_encounter_invoices t2 ON t1.id = $account_id AND t1.id = t2.account_number_id JOIN tbl_invoice_lines t3
		   ON t2.id = t3.invoice_id JOIN tbl_insuarance_item_mapping t4 ON t4.gothomis_item_id = t3.item_id JOIN tbl_insuarance_items t5 ON t5.id = t4.nhif_item_id LEFT JOIN tbl_insuarance_item_prices t6 ON t5.item_code=t6.item_code AND t1.scheme_id = t6.scheme_code WHERE t6.id IS NULL");
		if(COUNT($missing_pricing) > 0){
			return response()->json([
						"Message"=>"Cannot proceed. Some of services provided have no price set for the client category ".$missing_pricing[0]->scheme_id,
						"statusCode"=>404
					]);
		}
		
		
		createPdfPatientFileToNhif($account_id);
		createClaimFolio($account_id);
		return response()->json([
									"Message"=>"Successful",
									"statusCode"=>200
								]);
	}


	public function getMappedPrices(Request $request){    	  
	  
	  $sql="SELECT DISTINCT t1.*,t3.scheme_code AS package_id,t3.amount AS unit_price,t3.is_restricted FROM  tbl_insuarance_items t1
			INNER JOIN tbl_insuarance_item_mapping t2 ON t1.id=t2.nhif_item_id 
			INNER JOIN tbl_insuarance_item_prices t3 ON t3.item_code=t1.item_code";        
	  return DB::SELECT($sql);
	}


    //.......
    public function getNHIFprices(Request $request_list){    	  
    	   $returned_items=priceItemsNhif($request_list);
    	   $items = json_decode($returned_items, true);
         
    	   foreach ($items as $value) {
    	       $ItemCode   =$value['ItemCode'];
    	       $UnitPrice  =$value['UnitPrice'];
    	       $PackageID  =$value['PackageID'];
    	       $ItemName   =$value['ItemName'];
    	       $is_restricted   =$value['IsRestricted'];
    	       $dosage   =$value['IsRestricted'];
    	       $strength   =$value['Strength'];
    	       $maximum_quantity   =$value['MaximumQuantity'];
    	      
    	       $data=['item_code'    =>$ItemCode,
    	              'unit_price'   =>$UnitPrice,
                      'package_id'   =>$PackageID,
                      'item_name'    =>$ItemName,
                      'is_restricted'=>$is_restricted,
                      'dosage'=>$dosage,
                      'strength'=>$strength,
                      'maximum_quantity'=>$maximum_quantity,           
                      
    	                ];
    	       Tbl_insuarance_mapping_item::create($data);    	     
    	      }
    	   return;
    }
	
	//GET CLAIM BY FOLIO NUMBER
	
		
	 public function getNHIFclaimByFolioNumber(Request $request){
     	$year_of_visit= $request->year_of_visit;
     	$month_of_visit= $request->month_of_visit;     
     	$folio_number= $request->folio_number;  
		
        $sql="SELECT t1.* ,t2.patient_id,t3.facility_code,t2.account_number,t2.id AS account_id,card_no,authorization_number,membership_number,DATE(t2.created_at) AS attended_date,t3.facility_name,t3.address,t6.occupation_name,
          TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) AS age ,t4.practioner_no,t4.name  AS created_by,t2.created_at AS time_created,t2.updated_at AS last_modified,t7.prof_name                
         FROM tbl_patients t1
         INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id
         INNER JOIN tbl_encounter_invoices t8 ON t2.id=t8.account_number_id
         LEFT JOIN tbl_bills_categories t5 ON t2.id=t5.account_id
         INNER JOIN users t4 ON t4.id=t2.user_id
         INNER JOIN tbl_facilities t3 ON t3.id=t2.facility_id   
         LEFT JOIN tbl_occupations t6 ON t6.id=t1.occupation_id 
         INNER JOIN tbl_proffesionals t7 ON t4.proffesionals_id=t7.id 
         INNER JOIN tbl_patient_visit_serials t9 ON t5.account_id=t9.visit_id 
          		 
         WHERE   t9.month_of_visit='".$month_of_visit."'
          AND    t9.year_of_visit ='".$year_of_visit."' 
          AND    t9.serial_number ='".$folio_number."'
        GROUP BY t2.id ORDER BY t2.date_attended DESC";
        
	     return DB::SELECT($sql); 
    }

    public function getNhifServices(Request $request){
       $sql="SELECT t1.* FROM tbl_insuarance_mapping_items t1 WHERE t1.item_name LIKE '%".$request->searchKey."%'
              GROUP BY t1.item_code LIMIT 8";
            return DB::SELECT($sql);
    }
    public function getPostClaim(Request $request){
		$account_id=$request->account_id;
		
		$is_visit_closed=Tbl_accounts_number::where('id',$account_id)->where('visit_close',1)->get();  

		if(count($is_visit_closed) >0){
			return response()->json([
						"Message"=>"Please close the patient visit before proceed with claiming",
						"statusCode"=>500
						]);
		}

		$success_message=array('Claim Received Successfully');

		$facility_id=$request->facility_id;
		$user_id    =$request->user_id;
		$patient_id =$request->patient_id;
		$claim  =  new ClaimSubmission();
		
		$claimResults=$claim->SubmitFolios($account_id,$facility_id,$patient_id);
		
		$claimResults=json_decode($claimResults, true); 

		//return  $claimResults['entities'][0]['FolioDiseases'][0]['DiseaseCode'];

		$message=$claimResults['Message'];
		$StatusCode=$claimResults['StatusCode'];
		if( $StatusCode==200 ||  $StatusCode==201){
			$claims=createClaimFolio($account_id,$patient_id);
			Tbl_nhif_file::create(['user_id'=>$user_id,'facility_id'=>$facility_id,'account_id'=>$account_id, 'claims'=>$claims]);
			
			Tbl_accounts_number::where('id',$account_id)->update(['is_submitted'=>1]); 
			DB:statement("UPDATE claim_summary SET date_submitted = CURRENT_TIMESTAMP WHERE visit_account_id = $account_id");
		}

		return $claimResults;
    }
    

    public function sendBulkClaims(Request $request){
       $success_message=array('Claim Received Successfully');
       $sql="SELECT t1.account_id,t2.patient_id,t1.facility_id FROM tbl_bulk_claims t1 
             INNER JOIN tbl_accounts_numbers t2 ON t1.account_id=t2.id 
              WHERE t1.account_id NOT IN (SELECT account_id FROM tbl_nhif_files t3)";

        $bulks=DB::SELECT($sql);
        foreach ($bulks AS $bulk) {
         $account_id=$bulk->account_id;
         $facility_id=$bulk->facility_id;
          $user_id    =Auth::user()->id;
          $patient_id =$bulk->patient_id;
         $claim  =  new ClaimSubmission();
         $claimResults=$claim->SubmitFolios($account_id,$facility_id,$patient_id);
         $claimResults=json_decode($claimResults, true); 

      //return  $claimResults['entities'][0]['FolioDiseases'][0]['DiseaseCode'];

         $message=$claimResults['Message'];
         if(in_array($message , $success_message)){
         $claims=createClaimFolio($account_id,$patient_id);
         Tbl_nhif_file::create(['user_id'=>$user_id,'facility_id'=>$facility_id,'account_id'=>$account_id,
                                     'claims'=>$claims]);
         }

      
     }
      return $claimResults;
    }

    public function markAsOk(Request $request)
    {
       $user_id =Auth::user()->id;
       $facility_id =Auth::user()->facility_id;
       $account_id=$request->account_id;
       $pay_load =['user_id'=>$user_id,'account_id'=>$account_id,'facility_id'=>$facility_id];
       try {
         Tbl_bulk_claim::create($pay_load);
         return response()->json([
                               "Message"=>"Claim was Successfully Saved .",
                               "status"=>"success"
                             ]);
       } catch (\Exception $e) {
         return response()->json([
                               "Message"=>"Claim was not saved, ".$e,
                               "status"=>"error"
                             ]);
       }
       


    }

    

     public function verifiedClaims(Request $request)
    {
           try {
        $start_time= $request->start_time;
        $end_time= $request->end_time;     
        $sql="SELECT t1.* ,t2.patient_id,t3.facility_code,t2.account_number,t2.id AS account_id,card_no,authorization_number,membership_number,DATE(t2.created_at) AS attended_date,t3.facility_name,t3.address,t6.occupation_name,
          TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) AS age ,t4.practioner_no,t4.name  AS created_by,t2.created_at AS time_created,t2.updated_at AS last_modified,t7.prof_name,(SELECT name FROM users t10 WHERE
          t10.id=t9.user_id  LIMIT 1)  AS verified_by,t9.created_at AS time_verified              
         FROM tbl_patients t1
         INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id
         LEFT JOIN tbl_bills_categories t5 ON t2.id=t5.account_id
         INNER JOIN users t4 ON t4.id=t2.user_id
         INNER JOIN tbl_facilities t3 ON t3.id=t2.facility_id   
         LEFT JOIN tbl_occupations t6 ON t6.id=t1.occupation_id 
         INNER JOIN tbl_proffesionals t7 ON t4.proffesionals_id=t7.id   
         INNER JOIN tbl_bulk_claims t9 ON t9.account_id=t5.account_id             
         WHERE  t2.id NOT IN (SELECT t8.account_id FROM tbl_nhif_files t8 WHERE t8.account_id=t2.id)
         AND ((t2.created_at BETWEEN '".$start_time."' AND  '".$end_time."' AND NOT EXISTS (SELECT patient_id FROM tbl_admissions t_a1 WHERE t_a1.account_id = t2.id AND t_a1.admission_status_id IN(4,5,7,8,9) AND DATE(t_a1.updated_at) > DATE('$end_time'))) OR EXISTS (SELECT patient_id FROM tbl_admissions t_a2 WHERE t_a2.account_id = t2.id AND t_a2.admission_status_id IN(4,5,7,8,9) AND DATE(t_a2.updated_at) BETWEEN DATE('$start_time') AND DATE('$end_time'))) AND t2.authorization_number IS NOT NULL
        GROUP BY card_no";// LIMIT 35";
        
       return DB::SELECT($sql); 
       } catch (\Exception $e) {
         return response()->json([
                               "Message"=>"Claim was not saved, ".$e,
                               "status"=>"error"
                             ]);
       }
       


    }
		
	
	
	public function readClientsignature(Request $request){
	  try{
		  $visitId= $request->visitId;
     	  //$binaryData= $request->binaryData;
		  $fileName=$visitId.'.txt';
		  if (Storage::disk($disk)->exists($fileName)) {
          return Storage::disk($disk)->get($fileName);
          }
		  else{
			 return response()->json([
                               "Message"=>"Client signature was not found!",
                               "status"=>"success"
                             ]);  
		  }
		 
		
	  }
	  catch (\Exception $e) {
         return response()->json([
                               "Message"=>"Client signature failed to be read!, ".$e,
                               "status"=>"error"
                             ]);
       }
	}
	
	
	public function saveClientsignature(Request $request){
	  try{
		  $visitId= $request->visitId;
		  
     	  $binaryData= $request->binaryData;
		  $fileName=$visitId.'.txt';
		  $file=Storage::disk('local')->put($fileName, $binaryData);
		  if( $file){
			 //$sql="UPDATE tbl_accounts_numbers SET client_signature_path='".$fileName."' WHERE id=".$visitId;
			 //DB::statement($sql);
      return response()->json([
                               "Message"=>"Client signature was successfully captured!",
                               "status"=>"success"
                             ]);
		  }
		
	  }
	  catch (\Exception $e) {
         return response()->json([
                               "Message"=>"Client signature failed to be stored!, ".$e,
                               "status"=>"error"
                             ]);
       }
	  
	  }
	
     public function getNHIFclaims(Request $request){
		 
		// readClientSignature($visit_id);
     	$start_time= $request->start_time;
     	$end_time= $request->end_time;     
        $sql="SELECT t1.* ,t2.patient_id,t3.facility_code,t2.account_number,t2.id AS account_id,card_no,authorization_number,membership_number,DATE(t2.created_at) AS attended_date,t3.facility_name,t3.address,t6.occupation_name,
          TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) AS age ,t4.practioner_no,t4.name  AS created_by,t2.created_at AS time_created,t2.updated_at AS last_modified,t7.prof_name                
         FROM tbl_patients t1
         INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id
		 INNER JOIN tbl_diagnoses t10 ON t10.visit_date_id=t2.id
         INNER JOIN tbl_encounter_invoices t8 ON t2.id=t8.account_number_id
         LEFT JOIN tbl_bills_categories t5 ON t2.id=t5.account_id
         INNER JOIN users t4 ON t4.id=t2.user_id
         INNER JOIN tbl_facilities t3 ON t3.id=t2.facility_id   
         LEFT JOIN tbl_occupations t6 ON t6.id=t1.occupation_id 
         INNER JOIN tbl_proffesionals t7 ON t4.proffesionals_id=t7.id   
         		 
         WHERE  t2.is_submitted=0
          AND ((t2.created_at BETWEEN '".$start_time."' AND  '".$end_time."' AND NOT EXISTS (SELECT patient_id FROM tbl_admissions t_a1 WHERE t_a1.account_id = t2.id AND t_a1.admission_status_id IN(4,5,7,8,9) AND DATE(t_a1.updated_at) > DATE('$end_time'))) OR EXISTS (SELECT patient_id FROM tbl_admissions t_a2 WHERE t_a2.account_id = t2.id AND t_a2.admission_status_id IN(4,5,7,8,9) AND DATE(t_a2.updated_at) BETWEEN DATE('$start_time') AND DATE('$end_time'))) AND  t2.authorization_number IS NOT NULL
        GROUP BY t2.id ORDER BY t2.date_attended DESC";// LIMIT 35";
        
	     return DB::SELECT($sql); 
    }

     public function getSubmittedNhifClaims(Request $request){
     	$start_time= $request->start_time;
     	$end_time= $request->end_time;     
        $sql="SELECT t1.* ,t3.facility_code,t2.account_number,t2.id AS account_id,card_no,authorization_number,membership_number,DATE(t2.created_at) AS attended_date,t3.facility_name,t3.address,t6.occupation_name,
          TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) AS age ,t4.practioner_no,t4.name  AS created_by,t2.created_at AS time_created,t2.updated_at AS last_modified,t7.prof_name,
          (SELECT name FROM users t1  WHERE t8.user_id=t1.id LIMIT 1) AS submited_by             
         FROM tbl_patients t1
         INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id
         INNER JOIN tbl_bills_categories t5 ON t2.id=t5.account_id
         INNER JOIN users t4 ON t4.id=t2.user_id
         INNER JOIN tbl_facilities t3 ON t3.id=t2.facility_id 
         INNER JOIN tbl_proffesionals t7 ON t4.proffesionals_id=t7.id 
         INNER JOIN tbl_nhif_files t8 ON t8.account_id=t2.id    
         LEFT JOIN tbl_occupations t6 ON t6.id=t1.occupation_id                
         WHERE t5.bill_id=4 
         AND  ((t2.created_at BETWEEN '".$start_time."' AND  '".$end_time."' AND NOT EXISTS (SELECT patient_id FROM tbl_admissions t_a1 WHERE t_a1.account_id = t2.id AND t_a1.admission_status_id IN(4,5,7,8,9) AND DATE(t_a1.updated_at) > DATE('$end_time'))) OR EXISTS (SELECT patient_id FROM tbl_admissions t_a2 WHERE t_a2.account_id = t2.id AND t_a2.admission_status_id IN(4,5,7,8,9) AND DATE(t_a2.updated_at) BETWEEN DATE('$start_time') AND DATE('$end_time'))) GROUP BY card_no";// LIMIT 35";
	     return DB::SELECT($sql); 
    }

 public function getPatientsFiles(Request $request){
      $start_time= $request->start_time;
      $end_time= $request->end_time;     
        $sql="SELECT t1.* ,t3.facility_code,t2.account_number,t2.id AS account_id,card_no,authorization_number,membership_number,DATE(t2.created_at) AS attended_date,t3.facility_name,t3.address,t6.occupation_name,
          TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) AS age ,t4.practioner_no,t4.name  AS created_by,t2.created_at AS time_created,t2.updated_at AS last_modified,t7.prof_name            
         FROM tbl_patients t1
         INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id
         INNER JOIN tbl_bills_categories t5 ON t2.id=t5.account_id
         INNER JOIN users t4 ON t4.id=t2.user_id
         INNER JOIN tbl_facilities t3 ON t3.id=t2.facility_id 
         INNER JOIN tbl_proffesionals t7 ON t4.proffesionals_id=t7.id   
         LEFT JOIN tbl_occupations t6 ON t6.id=t1.occupation_id                        
         WHERE t5.bill_id=4 
         AND ((t2.created_at BETWEEN '".$start_time."' AND  '".$end_time."' AND NOT EXISTS (SELECT patient_id FROM tbl_admissions t_a1 WHERE t_a1.account_id = t2.id AND t_a1.admission_status_id IN(4,5,7,8,9) AND DATE(t_a1.updated_at) > DATE('$end_time'))) OR EXISTS (SELECT patient_id FROM tbl_admissions t_a2 WHERE t_a2.account_id = t2.id AND t_a2.admission_status_id IN(4,5,7,8,9) AND DATE(t_a2.updated_at) BETWEEN DATE('$start_time') AND DATE('$end_time'))) GROUP BY card_no";// LIMIT 35";
       return DB::SELECT($sql); 
    }




    

    public function getSystemServices(Request $request){
       $sql="SELECT t1.* FROM tbl_items t1 WHERE t1.item_name 
               LIKE '%".$request->searchKey."%' GROUP BY t1.item_name LIMIT 8";
              return DB::SELECT($sql);
    }

    public function mapServices(Request $request){ 
		if(!isset($request->gothomis_item_id) || !isset($request->nhif_item_id) ){
			return response()->json(['message'=>"Please enter and select services to be mapped","status"=>500],500);
		}

		If(DB::table("tbl_insuarance_item_mapping")->where("gothomis_item_id",$request->gothomis_item_id)->count() > 0){
			$sql="UPDATE tbl_insuarance_item_mapping SET nhif_item_id = $request->nhif_item_id WHERE gothomis_item_id = $request->gothomis_item_id";// AND NOT EXISTS(SELECT Id FROM tbl_insuarance_item_mapping WHERE nhif_item_id = $request->nhif_item_id AND gothomis_item_id <> $request->gothomis_item_id)";
		}
		else{
			$sql="INSERT INTO tbl_insuarance_item_mapping(gothomis_item_id, nhif_item_id) SELECT $request->gothomis_item_id, $request->nhif_item_id";
		}
		if(DB::statement($sql)){
			return response()->json(['message'=>"Successfully saved","status"=>200],200);
		}
    }

    


   

}