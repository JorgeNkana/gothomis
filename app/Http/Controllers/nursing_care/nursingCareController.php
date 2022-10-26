<?php

namespace App\Http\Controllers\nursing_care;

use App\ClinicalServices\Tbl_patient_procedure;
use App\nursing_care\Tbl_death_condition;
use App\nursing_care\Tbl_pre_history_anethetic;
use App\Payment_types\Tbl_pay_cat_sub_category;
use App\nursing_care\Tbl_serious_patient;
use App\nursing_care\Tbl_status_ward;
use App\nursing_care\Tbl_turning_chart;
use App\theatre\Tbl_operation_queue;
use Illuminate\Http\Request;
use App\Patient\Tbl_accounts_number;
use App\Http\Controllers\Controller;
use App\classes\patientRegistration;
use App\nursing_care\Tbl_vital_sign;
use App\nursing_care\Tbl_wards_type;
use App\nursing_care\Tbl_input;
use App\nursing_care\Tbl_output;
use App\nursing_care\Tbl_ward;
use App\nursing_care\Tbl_bed;
use App\nursing_care\Tbl_instruction;
use App\nursing_care\Tbl_admission;
use App\nursing_care\Tbl_observation_type;
use App\nursing_care\Tbl_observation_chart;
use App\nursing_care\Tbl_intake_observation;
use App\nursing_care\Tbl_observations_output_type;
use App\nursing_care\Tbl_output_observation;
use App\nursing_care\Tbl_nursing_diagnosise;
use App\nursing_care\Tbl_nursing_care_plan;
use App\nursing_care\Tbl_treatment_chart;
use App\nursing_care\Tbl_discharge_permit;
use App\nursing_care\Tbl_theatre_wait;
use App\nursing_care\Tbl_surgery_history;
use App\nursing_care\Tbl_surgery_physical_examination;
use App\nursing_care\Tbl_surgery_family_social;
use App\nursing_care\Tbl_teeth_arrangement;
use App\nursing_care\Tbl_teeth_patient;
use App\nursing_care\Tbl_status_anaesthetic;
use App\nursing_care\Tbl_intra_operation;
use App\nursing_care\Tbl_intra_opcondition;
use App\Item_setups\Tbl_item;
use App\Item_setups\Tbl_item_type_mapped;
use App\nursing_care\Tbl_nurse_ward;
use App\Patient\Tbl_patient;
use App\nursing_care\Tbl_nursing_care;
use App\nursing_care\Tbl_ipdtreatment;
use App\ClinicalServices\Tbl_prescription;
use App\admin\Tbl_notification;
use App\nursing_care\Tbl_status_procedure;
use App\nursing_care\Tbl_informed_consent;
use App\Payments\Tbl_invoice_line;
use App\Payments\Tbl_encounter_invoice;
use App\classes\SystemTracking;
use App\Trackable;
//for mtuha
use App\Http\Controllers\reports\ReportGenerators;
use DB;

class nursingCareController extends Controller
{

public function mynotifications($user_id){
   
     return Tbl_notification::where("receiver_id",$user_id)->orderBy("id","DESC")->take(10)->get();
  
}

public function saveOperations(Request $request){
  if((time()-(60*60*24)) > strtotime($request->operation_date)){	
	return response()->json([	'data' =>'OPERATION DATE MUST START FROM TODAY',
								'status' => '0'
										]);
   } else if(Tbl_status_procedure::create($request->all())){
	 return response()->json(['data' =>$request->patientName." booked for ordered procedure.",
                'status' =>1
            ]);
}

}

public function getWardReport(Request  $request){
	$start_date=$request->start_date;
	$end_date=$request->end_date;
	$sql="SELECT t4.id as ward_id,	t4.ward_name,
    
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=2  and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}'  AND t1.facility_id='".$request->facility_id."') AS waliolazwa,
    
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=4  and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}' AND t1.facility_id='".$request->facility_id."') AS walioruhusiwa,
     
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=5 and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}' AND t1.facility_id='".$request->facility_id."') AS transfer_out,
      
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=6  and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}' AND t1.facility_id='".$request->facility_id."') AS transfer_in,
      
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=7 and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}' AND t1.facility_id='".$request->facility_id."') AS absconded,
       
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=8 and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}' AND t1.facility_id='".$request->facility_id."') AS died,
       
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=9 and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}' AND t1.facility_id='".$request->facility_id."') AS dama,
		
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=10 and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}' AND t1.facility_id='".$request->facility_id."') AS serious_patient,
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=11 and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}' AND t1.facility_id='".$request->facility_id."') AS referral,
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=12 and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}' AND t1.facility_id='".$request->facility_id."') AS maternal_death,
    
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=13 and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}' AND t1.facility_id='".$request->facility_id."') AS delivery,
    
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=14 and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}' AND t1.facility_id='".$request->facility_id."') AS fsb,
    
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=15 and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}' AND t1.facility_id='".$request->facility_id."') AS msb,
    
    (SELECT  COUNT(*) FROM tbl_status_wards t1 WHERE t1.admission_status_id=16 and ward_id = t4.id and t1.created_at BETWEEN  '{$start_date}' AND '{$end_date}' AND t1.facility_id='".$request->facility_id."') AS neonatal_death
    
    FROM 
    tbl_wards t4 where t4.facility_id='".$request->facility_id."' order by ward_name";


return DB::SELECT($sql);
}

public function saveNotes(Request $request){
  if(empty($request->description)){
	return response()->json([	'data' =>'YOU MUST WRITE JUST SUMMARY ON THIS PATIENT',
								'status' => 0
										]);
   } else if(Tbl_serious_patient::create($request->all())){
      Tbl_status_ward::create($request->all());

      return response()->json(['data' =>$request->patientName." reported as serious patient,that need special attention.",
                'status' =>1
            ]);
}

}
public function saveDeathNotes(Request $request){

  if(empty($request->description)){
	return response()->json([	'data' =>'YOU MUST WRITE JUST SUMMARY ON THIS DEATH',
								'status' => 0
										]);
   } else if(Tbl_death_condition::create($request->all())){

      Tbl_admission::WHERE("id",$request->admission_id)->update(array("admission_status_id"=>8)); //release bed.

      Tbl_bed::WHERE("id",$request->bed_id)->update(array("occupied"=>0)); //release bed.
      Tbl_status_ward::create($request->all());

 $data=Tbl_death_condition::where('visit_date_id',$request->visit_date_id)->take(1)->get();
$newData=Tbl_death_condition::where('visit_date_id',$request->visit_date_id)->take(1)->get();

                   $patient_id=$data[0]->patient_id;
                    $trackable_id=$newData[0]->id;
			$user_id=$newData[0]->user_id;
                    SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

      return response()->json(['data' =>$request->patientName." reported as STOPED and BED released for another patient.",
                'status' =>1
            ]);
}

}

//registration report
    public function getMahudhuriOPDRegistration(request $request)
    {
        $response = [];
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 00:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }

        $sql_1="SELECT SUM(female_under_one_month) AS female_under_one_month ,SUM(male_under_one_month) AS male_under_one_month,SUM(total_under_one_month) AS total_under_one_month 
          ,SUM(female_under_one_year) AS female_under_one_year
          ,SUM(male_under_one_year) AS 	male_under_one_year
          ,SUM(total_under_one_year) AS total_under_one_year
          
         ,SUM(female_under_five_year) AS female_under_five_year
          ,SUM(male_under_five_year) AS male_under_five_year
          ,SUM(total_under_five_year) AS total_under_five_year
          
         ,SUM(female_above_five_under_sixty_year) AS female_above_five_under_sixty_year
          ,SUM(male_above_five_under_sixty) AS male_above_five_under_sixty
          ,SUM(total_above_five_under_sixty) AS total_above_five_under_sixty
        
        ,SUM(female_above_sixty) AS female_above_sixty
          ,SUM(male_above_sixty) AS male_above_sixty
          ,SUM(total_above_sixty) AS total_above_sixty
          
        ,SUM(grand_total_female) AS grand_total_female
          ,SUM(grand_total_male) AS grand_total_male
          ,SUM(grand_total) AS grand_total
        
        
        FROM `vw_opd_attendaces` t1 WHERE t1.facility_id='{$facility_id}' AND (date_attended BETWEEN  '{$start_date}' AND '{$end_date}')";
        $response[] = DB::select($sql_1);


        $sql_3="SELECT SUM(female_under_one_month) AS female_under_one_month ,SUM(male_under_one_month) AS male_under_one_month,SUM(total_under_one_month) AS total_under_one_month 
          ,SUM(female_under_one_year) AS female_under_one_year
          ,SUM(male_under_one_year) AS 	male_under_one_year
          ,SUM(total_under_one_year) AS total_under_one_year
          
         ,SUM(female_under_five_year) AS female_under_five_year
          ,SUM(male_under_five_year) AS male_under_five_year
          ,SUM(total_under_five_year) AS total_under_five_year
          
         ,SUM(female_above_five_under_sixty_year) AS female_above_five_under_sixty_year
          ,SUM(male_above_five_under_sixty) AS male_above_five_under_sixty
          ,SUM(total_above_five_under_sixty) AS total_above_five_under_sixty
        
        ,SUM(female_above_sixty) AS female_above_sixty
          ,SUM(male_above_sixty) AS male_above_sixty
          ,SUM(total_above_sixty) AS total_above_sixty
          
        ,SUM(grand_total_female) AS grand_total_female
          ,SUM(grand_total_male) AS grand_total_male
          ,SUM(grand_total) AS grand_total
        
        
        FROM `vw_opd_marudio_attendaces` t1 WHERE t1.facility_id='{$facility_id}' AND (date_attended BETWEEN  '{$start_date}' AND '{$end_date}')";
        $response[] = DB::select($sql_3);


        return $response;

    }




public function wardSampleCollection($nurse_id){

	  $todayDate=date("Y-m-d");
       $sql="SELECT 
        t1.patient_id,
        t1.visit_date_id,
        t2.order_id,
        t2.id AS request_id,
        t5.date_attended,
        t3.id AS item_id,
        t5.account_number,
        t6.first_name,
        t6.middle_name,
        t6.last_name,
        t6.gender,
        t6.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, t6.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, t6.dob, CURRENT_DATE), ' Days') END END
AS age,
        t6.medical_record_number,
        t6.mobile_number,
        t7.payment_filter,
        t7.status_id AS payment_status,
        t3.item_name,
        t18.sub_department_name,
        t18.id AS sub_department_id,
        t2.sample_no,
        t1.admission_id,
        CASE 
         WHEN t1.admission_id is NULL THEN 'OPD'  ELSE 'IPD'  END as dept,        
        t2.created_at,
        t4.facility_id,
		t4.name,
		t4.mobile_number AS doctor_mob
        
         FROM tbl_orders  t2
            INNER JOIN tbl_requests t1 ON t1.id=t2.order_id AND DATE(t1.created_at) = DATE(t2.created_at)
            INNER JOIN tbl_accounts_numbers t5 ON t1.visit_date_id =t5.id
            INNER JOIN tbl_items t3 ON t3.id = t2.test_id
            INNER JOIN users t4 ON t1.doctor_id = t4.id
            INNER JOIN tbl_patients t6 ON t1.patient_id = t6.id
            INNER JOIN tbl_encounter_invoices t8 ON t5.id = t8.account_number_id
            INNER JOIN tbl_invoice_lines t7 ON t7.invoice_id = t8.id
            INNER JOIN tbl_pay_cat_sub_categories t14 ON t14.id= t7.payment_filter
            INNER JOIN tbl_payments_categories t15 ON t14.pay_cat_id= t15.id
            INNER JOIN tbl_item_prices t13 ON t2.test_id = t13.item_id          
            INNER JOIN tbl_testspanels t16 ON t13.item_id = t16.item_id          
            INNER JOIN tbl_equipments t17 ON t17.id = t16.equipment_id          
            INNER JOIN tbl_sub_departments t18 ON t18.id = t17.sub_department_id      
			INNER JOIN tbl_instructions t19 ON t1.admission_id = t19.admission_id  
			INNER JOIN tbl_nurse_wards t20 ON t19.ward_id = t20.ward_id     
           WHERE  
        t7.item_price_id = t13.id
        AND t2.test_id = t13.item_id
        AND (t7.status_id =2 OR t15.id >1)
        AND t2.sample_no IS NULL
		AND t1.admission_id IS NOT NULL
		AND t20.nurse_id='".$nurse_id."'
        AND DATE(t1.created_at)=DATE(t7.created_at)
		AND DATE(t7.updated_at)='".$todayDate."'  GROUP BY item_id";


        return DB::SELECT($sql);


	}



	public function getListNursingCare($admission_id){

		$sql="SELECT
	           t1.date_planned,
			   t1.time_planned,
			   t1.diagnosis_name,
			   t1.objective,
			   t1.implementation,
			   t1.evaluation,
			   t1.admission_id,
			   t1.user_id,
			   t1.facility_id,
			   t3.bed_name,
			   t4.name
         FROM tbl_nursing_cares  t1
             INNER JOIN tbl_instructions t2 ON t2.admission_id = t1.admission_id
			 INNER JOIN users t4 ON t4.id = t1.user_id
             INNER JOIN  tbl_beds t3 ON t3.id = t2.bed_id
			 WHERE t1.admission_id='".$admission_id."'";


	return	DB::SELECT($sql);


	}
 public function searchItemObserved(Request $request)
    {
        $searchKey=$request['item_name'];
	$sql="SELECT * FROM vw_pharmacy_items t1 WHERE t1.item_name LIKE '%".$searchKey."%' GROUP BY t1.item_name";

    return DB::SELECT($sql);
    }

	public function saveInputs(Request $request)
    {
    $newData=Tbl_input::create($request->all());
$data= Tbl_accounts_number::where('id',$newData->visit_date_id)->take(1)->get();

                    $patient_id=$data[0]->patient_id;
                    $trackable_id=$newData->id;
			$user_id=$data[0]->user_id;
                    SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);
return $newData;


    }
	public function saveOutputs(Request $request)
    {
     $newData=Tbl_output::create($request->all());
$data= Tbl_accounts_number::where('id',$newData->visit_date_id)->take(1)->get();

                    $patient_id=$data[0]->patient_id;
                    $trackable_id=$newData->id;
			$user_id=$data[0]->user_id;
                    SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);
return $newData;

    }

    public function saveTurningChart(Request $request)
    {
      $newData= Tbl_turning_chart::create($request->all());
$data= Tbl_accounts_number::where('id',$newData->visit_date_id)->take(1)->get();

                    $patient_id=$data[0]->patient_id;
                    $trackable_id=$newData->id;
			$user_id=$data[0]->user_id;
                    SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);
return $newData;

    }


    public function getTurningChart(Request $request)
    {
        $sql="SELECT t1.*,t2.name FROM tbl_turning_charts t1 
              INNER JOIN users t2 ON t2.id=t1.user_id
              WHERE t1.admission_id='".$request->admission_id."'";

      return DB::SELECT($sql);
    }


    public function getInputs(Request $request)
    {
		$sql="
		SELECT t1.date_recorded,
		       t1.time_recorded,
		       t1.amount_iv,
		       t1.amount_oral,
		       t1.visit_date_id,
		       t1.admission_id,
		
        	CASE WHEN t1.type_iv IS NOT NULL THEN (SELECT t2.item_name FROM tbl_items t2 WHERE t2.id=t1.type_iv GROUP BY t1.type_iv) END AS type_iv,
			
			CASE WHEN t1.type_oral IS NOT NULL THEN (SELECT t2.item_name FROM tbl_items t2 WHERE t2.id=t1.type_oral GROUP BY t1.type_oral) END AS type_oral,
			
		CASE WHEN t1.user_id IS NOT NULL THEN (SELECT t3.name FROM users t3 WHERE t3.id=t1.user_id GROUP BY t1.user_id) END AS nurse_name	
		
		FROM tbl_inputs t1
		
		WHERE t1.admission_id='".$request->admission_id."'";

      return DB::SELECT($sql);
    }

	public function getOutputs(Request $request)
    {
		$sql="
		SELECT t1.date_recorded,
		       t1.time_recorded,
		       t1.type_of_output,
		       t1.amount_output,
		       t1.visit_date_id,
		       t1.admission_id,
		       t1.created_at,
		    	
			
		CASE WHEN t1.user_id IS NOT NULL THEN (SELECT t3.name FROM users t3 WHERE t3.id=t1.user_id GROUP BY t1.user_id) END AS nurse_name	
		
		FROM tbl_outputs t1
		
		WHERE t1.admission_id='".$request->admission_id."'";

      return DB::SELECT($sql);
    }

	public function getTreatmentChart(Request $request){
	  $admission_id=$request->admission_id;
	   		     $sql="SELECT *  FROM `vw_treatment_charts` t1
             WHERE t1.admission_id='".$admission_id."'";

			  	          return DB::select($sql);
	}


	public function getPrescribedItems(Request $request){
	    $admission_id=$request->admission_id;
	     $templateID=$request->templateID;
	    if($templateID ==1){
            $sql="SELECT *  FROM `vw_prescribed_items` t1        
             WHERE t1.admission_id='".$admission_id."'";
          }
          else if($templateID ==2){

			    $sql="SELECT *  FROM `vw_prescribed_items` t1        
             WHERE t1.admission_id='".$admission_id."'";

			  /**
              $sql="SELECT *  FROM `vw_iv_types` t1
                    LEFT JOIN `vw_oral_types` t2 ON t2.date_given=t1.date_given
             WHERE t1.admission_id=".$admission_id;

			 **/
          }

		  else{
              $sql="SELECT *  FROM `vw_prescribed_items` t1        
             WHERE t1.admission_id='".$admission_id."'";

          }


	          return DB::select($sql);
	}


	public function addNursingCare(Request $request){

		 $admission_id =$request->admission_id;
		 $date_planned =$request->date_planned;
		 $time_planned =$request->time_planned;
		 $diagnosis_name =$request->diagnosis_name;
		 $objective =$request->objective;
		 $implementation =$request->implementation;
		 $evaluation =$request->evaluation;


    if(empty($admission_id)==true) {
            return response()->json(['data' =>'Select Patient first.',
                'status' =>0
            ]);
        }
	else if(empty($date_planned)==true){
  return response()->json(['data' =>'You must enter date of care',
                'status' =>0
            ]);
		}

			else if(empty($diagnosis_name)==true){
  return response()->json(['data' =>'You must enter diagnosis name',
                'status' =>0
            ]);
		}
		else{
    Tbl_nursing_care::create($request->all());
                return response()->json(['data' =>$diagnosis_name.' was Successfully saved.',
                'status' =>1
            ]);

		}


	}

	public function getPendingAdmissionList(Request $request)
    {
 


    //vw_pending_admission
    $nurse_id=$request->nurse_id;
	$sql="SELECT * FROM `vw_pending_admission` t1 WHERE t1.nurse_id='".$nurse_id."' GROUP BY t1.admission_id ";
	return DB::select($sql);

	}

	 public function addNurse(Request $request){
        $nurse_id=$request->nurse_id;
        $ward_id=$request->ward_id;

            Tbl_nurse_ward::create($request->all());
            return response()->json(['data' =>'Successfully saved.',
                'status' => 1
            ]);

    }

	public function getDischargedLists($facility_id){
    $sql="SELECT * FROM `vw_discharged_lists` t1 WHERE t1.facility_id='{$facility_id}' ORDER BY time_discharged DESC ";
	return DB::select($sql);
	}

	 public  function searchWardNurses(Request $request){
        $searchKey = $request->input('searchKey');
        $facility_id = $request->input('facility_id');
        $patientSearched=DB::table('vw_wards')
            ->where('facility_id',$facility_id)
            ->where('ward_full_name','like','%'.$searchKey.'%')
            ->groupBy('ward_id')
            ->get()->take(8);
        return $patientSearched;
    }

	 public  function searchNurseName(Request $request){
        $searchKey = $request->input('searchKey');
        $facility_id = $request->input('facility_id');
        $patientSearched=DB::table('vw_user_details')
            ->where('facility_id',$facility_id)
			 ->where('name','like','%'.$searchKey.'%')
			  ->orwhere('mobile_number','like','%'.$searchKey.'%')
              ->groupBy('user_id')
            ->get()->take(8);
        return $patientSearched;
    }

	public function getAprovedAdmissionList(Request $request){
     $nurse_id=$request->nurse_id;
     $sql="SELECT * FROM `vw_approved_admission` t1 WHERE t1.nurse_id='".$nurse_id."'    AND deleted=0  GROUP BY t1.admission_id ";
	return DB::select($sql);
    }

    //-----addmision search start
    public function SearchPatientAddmited(Request $request){
     $nurse_id=$request->nurse_id;
     $searchKey=$request->searchKey;
     $sql="SELECT * FROM `vw_approved_admission` t1 WHERE t1.medical_record_number like '%$searchKey%' AND t1.nurse_id='".$nurse_id."'     AND deleted=0  GROUP BY t1.admission_id";
	return DB::select($sql);
    }
    public function SearchPendingAdmissionListData(Request $request){
     $nurse_id=$request->nurse_id;
     $searchKey=$request->searchKey;
     $sql="SELECT * FROM `vw_pending_admission` t1 WHERE t1.medical_record_number like '%$searchKey%' AND t1.nurse_id='".$nurse_id."'   GROUP BY t1.admission_id";
	return DB::select($sql);
    }

    public function SearchgetPendingDischarge(Request $request){
        $nurse_id=$request->nurse_id;
        $searchKey=$request->searchKey;
        $sql="SELECT * FROM `vw_pending_discharge` t1 WHERE t1.medical_record_number like '%$searchKey%' AND t1.nurse_id='".$nurse_id."' GROUP BY admission_id ";
        return DB::select($sql);
    }
    public function LoadPendingDischargeData(Request $request){
        $nurse_id=$request->nurse_id;
        $visit_id=$request->visit_id;
        $sql="SELECT * FROM `vw_pending_discharge` t1 WHERE t1.account_id ='".$visit_id."' AND t1.nurse_id='".$nurse_id."' GROUP BY admission_id ";
        return DB::select($sql);
    }
 public function getPatientAddmitedDetail(Request $request){
     $nurse_id=$request->nurse_id;
     $visit_id=$request->visit_id;
     $sql="SELECT * FROM `vw_approved_admission` t1 WHERE t1.visit_date_id ='".$visit_id."' AND t1.nurse_id='".$nurse_id."'     AND deleted=0  GROUP BY t1.admission_id";
	return DB::select($sql);
    }
    public function SearchPendingAdmissionList(Request $request)
    {
        //vw_pending_admission
        $nurse_id=$request->nurse_id;
        $visit_id=$request->visit_id;
        $sql="SELECT * FROM `vw_pending_admission` t1 WHERE t1.visit_date_id ='".$visit_id."' AND t1.nurse_id='".$nurse_id."' GROUP BY t1.admission_id ";
        return DB::select($sql);

    }
    public function SearchdoctorNotes(Request $request){
        $nurse_id=$request->nurse_id;
        $visit_id=$request->visit_id;
        $sql="SELECT t1.*,u.name as doctor_rounded,nw.nurse_id,CONCAT(p.first_name,' ',p.middle_name,' ',p.last_name) AS fullname,p.medical_record_number FROM tbl_continuation_notes t1 join users u on u.id=t1.user_id  join tbl_nurse_wards nw on nw.nurse_id=u.id join tbl_patients p on p.id=t1.patient_id where nw.nurse_id='".$nurse_id."' group by t1.id   ORDER BY t1.created_at DESC ";
 
        return DB::SELECT($sql);
    }


    //-----addmision search end

	public function getPendingDischarge(Request $request){
     $nurse_id=$request->nurse_id;
     $sql="SELECT * FROM `vw_pending_discharge` t1 WHERE t1.nurse_id='".$nurse_id."' GROUP BY admission_id ";
	return DB::select($sql);
    }

   public function selectedNurse(Request $request){
     $nurse_id=$request->nurse_id;
     $sql="SELECT t1.*,t2.name,t3.ward_name FROM `tbl_nurse_wards` t1 
	 INNER JOIN users t2 ON  t1.nurse_id=t2.id
	 INNER JOIN tbl_wards t3 ON  t1.ward_id=t3.id
	 
	 WHERE t1.nurse_id='".$nurse_id."' group by t3.id order by t3.id desc";
	return DB::select($sql);
    }


//Change nurse Status
	public function changeNurseStatus(Request $request) {
		$status_id=0;
		if($request->on_off==0){
			$status_id=1;
		}
       return Tbl_nurse_ward::where('nurse_id',$request->nurse_id)->where('ward_id',$request->ward_id)->update(['deleted'=>$status_id]);
       }




	public function getIntakeSolutions(Request $request)
    {
    //sub_item_category must be of the type solutions..
	$sql="SELECT * FROM `vw_shop_items` t1 WHERE t1.sub_item_category='SOLUTION'";
	return DB::select($sql);

	}

	public function getVital(Request $request)
    {
	return Tbl_observation_type::all();
	}

	public function getTeethAbove(Request $request)
    {
	return Tbl_teeth_arrangement::WHERE("teeth_position",'A')->get();
	}

	public function getTeethBelow(Request $request)
    {
	return Tbl_teeth_arrangement::WHERE("teeth_position",'B')->get();
	}

	public function getTeethStatusFromPatientAbove($admission_id)
    {

        $sql="SELECT teeth_number,css_class FROM tbl_teeth_patients t1,tbl_teeth_arrangements t2 WHERE t1.dental_id=t2.id AND t1.admission_id='".$admission_id."' AND t2.teeth_position='A'";
	return DB::select($sql);
	}

	public function getTeethStatusFromPatientBelow($admission_id)
    {
	$sql="SELECT teeth_number,css_class FROM tbl_teeth_patients t1,tbl_teeth_arrangements t2 WHERE t1.dental_id=t2.id  AND t1.admission_id='".$admission_id."' AND t2.teeth_position='B'";
	return DB::select($sql);
	}



	public function saveTeethStatus(Request $request)
    {
        //return $request->all();
	 $teeth=Tbl_teeth_arrangement::all();
	 $admission_id=$request->admission_id;
	 $nurse_id=$request->nurse_id;
	 $information_category=$request->information_category;
	 $dental_status=$request->dental_status;
	 $css_class=$request->css_class;
	 $dental_id=$request->dental_id;
	if(patientRegistration::duplicate('tbl_teeth_patients',array('id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0 ))"), array( $dental_id))==false){

    foreach($teeth as $tooth){
    $arrayPassion=array("dental_id"=>$tooth->id,"dental_status"=>0,"css_class"=>'a',"admission_id"=>$admission_id,"nurse_id"=>$nurse_id,"other_information"=>'DENTAL STATUS',"erasor"=>0);
	$dentals=Tbl_teeth_patient::create($arrayPassion);

        }
	}
if(Tbl_teeth_patient::WHERE("dental_id",$dental_id)->update(array("dental_status"=>$dental_status,"css_class"=>$css_class))){

return response()->json(['data' =>'SUCCEFULLY SAVED',
								'status' => '1'
										]);
}else{
	return response()->json(['data' =>'ERROR IN UPDATING...',
								'status' => '0'
										]);

}
	}

	public function getDiagnosis(Request $request)
    {
	return Tbl_nursing_diagnosise::all();
	}

	public function getOutPutTypes(Request $request)
    {
	return Tbl_observations_output_type::all();
	}

	public function addVitals(Request $request)
    {
	$observed_amount=$request->observed_amount;
	$observation_type_id=$request->observation_type_id;
	$admission_id=$request->admission_id;
if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT ',
								'status' => '0'
										]);
}
else if(!is_numeric($observation_type_id)){

	return response()->json([	'data' =>'PLEASE SELECT OBSERVATION TYPE',
								'status' => '0'
										]);
}

else if(empty($observed_amount)){

	return response()->json([	'data' =>'PLEASE ENTER AMOUNT OBSERVED',
								'status' => '0'
										]);
}

else if(!is_numeric($observed_amount)){

	return response()->json([	'data' =>'ONLY NUMERIC VALUE IS ALLOWED',
								'status' => '0'
										]);
}

else if(patientRegistration::duplicate('tbl_observation_charts',array('observation_type_id','admission_id','observed_amount',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"), array($observation_type_id,$admission_id,$observed_amount))==true){
	return response()->json(['data' =>'YOU DUPLICATE FOR THIS PATIENT',
								'status' => '0'
										]);

}else{

	if(Tbl_observation_chart::create($request->all())){


		return response()->json(['data' =>'SUCCEFULLY SAVED',
								'status' => '1'
										]);
	}

}

	//return Tbl_observation_chart::all();
	}
    /**

	public function addVitals(Request $request)
    {
	$observed_amount=$request->observed_amount;
	$observation_type_id=$request->observation_type_id;
	$admission_id=$request->admission_id;
if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT ',
								'status' => '0'
										]);
}
else if(!is_numeric($observation_type_id)){

	return response()->json([	'data' =>'PLEASE SELECT OBSERVATION TYPE',
								'status' => '0'
										]);
}

else if(empty($observed_amount)){

	return response()->json([	'data' =>'PLEASE ENTER AMOUNT OBSERVED',
								'status' => '0'
										]);
}

else if(!is_numeric($observed_amount)){

	return response()->json([	'data' =>'ONLY NUMERIC VALUE IS ALLOWED',
								'status' => '0'
										]);
}

else if(patientRegistration::duplicate('tbl_observation_charts',array('observation_type_id','admission_id','observed_amount',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"), array($observation_type_id,$admission_id,$observed_amount))==true){
	return response()->json(['data' =>'YOU DUPLICATE FOR THIS PATIENT',
								'status' => '0'
										]);

}else{

	if(Tbl_observation_chart::create($request->all())){

		return response()->json(['data' =>'SUCCEFULLY SAVED',
								'status' => '1'
										]);
	}

}

	//return Tbl_observation_chart::all();
	}

**/


	public function saveStatusAnaesthetic(Request $request)
    {
	$value_noted=$request->value_noted;
	$admission_id=$request->admission_id;
	$request_id=$request->request_id;
	$information_category=$request->information_category;
    if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT FROM LEFT PANEL',
								'status' => '0'
										]);
      }

       else if(empty($value_noted)) {

    return response()->json(['data' => 'PLEASE WRITE RESULTS FOR ' . $information_category,
        'status' => '0'
       ]);
      }
      else if(patientRegistration::duplicate('tbl_status_anaesthetics',array('request_id','information_category',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"), array($request_id,$information_category))==true){
	return response()->json(['data' =>$information_category.' ALREADY EXISTS FOR THIS PATIENT',
								'status' => '0'
										]);

       }else{

	  if(Tbl_status_anaesthetic::create($request->all())){

		Tbl_theatre_wait::WHERE("id",$request_id)->update(array("received"=>1));
		return response()->json(['data' =>$information_category.' WERE SUCCEFULLY SAVED',
								'status' => '1'
										]);
	    }

       }

	    //return Tbl_observation_chart::all();
	    }


		public function savePastHistory(Request $request)   {
	$medical=$request->medical;
	$surgical=$request->surgical;
	$admission_id=$request->admission_id;
	$anaesthetic=$request->anaesthetic;
	$request_id=$request->request_id;
     if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT FROM LEFT PANEL',
								'status' => '0'
										]);
       }

        else if(empty($medical)){

	       return response()->json([	'data' =>'PLEASE WRITE NILL IF NO MEDICATION',
								'status' => '0'
										]);
          }

        else if(empty($surgical)){

            return response()->json([	'data' =>'PLEASE WRITE NILL IF NO SURGICAL',
								'status' => '0'
										]);
}

             else if(empty($anaesthetic)){

	return response()->json([	'data' =>'PLEASE WRITE NILL IF NO ANAESTHETIC',
								'status' => '0'
										]);
                }


              else if(patientRegistration::duplicate('tbl_surgery_histories',array('admission_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"), array($admission_id))==true){
	         return response()->json(['data' =>'PAST HISTORY ALREADY EXISTS FOR THIS PATIENT',
								'status' => '0'
										]);

               }else{
	if(Tbl_surgery_history::create($request->all())){

		Tbl_theatre_wait::WHERE("id",$request_id)->update(array("received"=>1));
		return response()->json(['data' =>'SUCCEFULLY SAVED',

								  'status' => '1'
										]);
	            }
              }


	}

    public function getIntraOperations($facility_id){
        $sql="SELECT 
        t2.patient_id,
        t2.visit_date_id,
        t5.date_attended,
        t3.id AS item_id,
        t5.account_number,
        t6.first_name,
        t6.middle_name,
        t6.last_name,
        t6.gender,
        t6.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, t6.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, t6.dob, CURRENT_DATE), ' Days') END END AS age,
        t6.medical_record_number,
        t6.mobile_number,
        t7.payment_filter,
        t7.status_id AS payment_status_id,
        t3.item_name,
        t1.id AS admission_id,
        t4.name AS doctor_name,
		t22.bed_name,
        t4.mobile_number AS doctor_number,
        CASE 
         WHEN t2.admission_id is NULL THEN 'OPD'  ELSE 'IPD'  END as dept,  
		 CASE 
         WHEN t7.status_id=1 THEN 'NOT PAID'  ELSE 'PAID'  END as payment_status,      
        t2.created_at,
		t23.ward_name,
        t4.facility_id,
		t19.operation_date,
		t20.ward_id
        
         FROM tbl_patient_procedures  t2
            LEFT JOIN tbl_admissions t1 ON t1.account_id=t2.visit_date_id
			LEFT JOIN tbl_instructions t20 ON t1.id=t20.admission_id 
			INNER JOIN tbl_theatre_services t ON t.item_id =t2.item_id       
			LEFT JOIN tbl_beds t22 ON t22.id=t20.bed_id  
            INNER JOIN tbl_accounts_numbers t5 ON t2.visit_date_id =t5.id
            INNER JOIN tbl_items t3 ON t3.id = t2.item_id
            INNER JOIN users t4 ON t2.user_id = t4.id
            INNER JOIN tbl_patients t6 ON t2.patient_id = t6.id
            INNER JOIN tbl_encounter_invoices t8 ON t5.id = t8.account_number_id
            INNER JOIN tbl_invoice_lines t7 ON t7.invoice_id = t8.id
            INNER JOIN tbl_pay_cat_sub_categories t14 ON t14.id= t7.payment_filter
            INNER JOIN tbl_payments_categories t15 ON t14.pay_cat_id= t15.id
            INNER JOIN tbl_item_prices t13 ON t2.item_id = t13.item_id          
            INNER JOIN tbl_departments t18 ON t18.id = t3.dept_id  
			LEFT JOIN tbl_nurse_wards t21 ON t21.ward_id = t20.ward_id 
			LEFT JOIN tbl_wards t23 ON t23.id = t21.ward_id 	
			LEFT JOIN tbl_status_procedures t19 ON t19.admission_id=t1.id               
         WHERE  
           t2.status=1
		 AND t5.facility_id='".$facility_id."'
        AND TIMESTAMPDIFF(hour,t2.created_at, CURRENT_DATE)<=24
		  	 GROUP BY t2.visit_date_id,t3.id";


        return DB::SELECT($sql);
    }
    public function getAnaethesiaListApproved($facility_id){
        $sql="SELECT 
        t2.patient_id,
        t2.visit_date_id,
        t5.date_attended,
        t3.id AS item_id,
        t5.account_number,
        t6.first_name,
        t6.middle_name,
        t6.last_name,
        t6.gender,
        t6.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, t6.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, t6.dob, CURRENT_DATE), ' Days') END END AS age,
        t6.medical_record_number,
        t6.mobile_number,
        t7.payment_filter,
        t7.status_id AS payment_status_id,
        t3.item_name,
        t1.id AS admission_id,
        t4.name AS doctor_name,
		t22.bed_name,
        t4.mobile_number AS doctor_number,
        CASE 
         WHEN t2.admission_id is NULL THEN 'OPD'  ELSE 'IPD'  END as dept,  
		 CASE 
         WHEN t7.status_id=1 THEN 'NOT PAID'  ELSE 'PAID'  END as payment_status,      
        t2.created_at,
		t23.ward_name,
        t4.facility_id,
		t19.operation_date,
		t20.ward_id
        
         FROM tbl_patient_procedures  t2
            LEFT JOIN tbl_admissions t1 ON t1.account_id=t2.visit_date_id
			LEFT JOIN tbl_instructions t20 ON t1.id=t20.admission_id        
			LEFT JOIN tbl_beds t22 ON t22.id=t20.bed_id  
            INNER JOIN tbl_accounts_numbers t5 ON t2.visit_date_id =t5.id
            INNER JOIN tbl_items t3 ON t3.id = t2.item_id
            INNER JOIN users t4 ON t2.user_id = t4.id
            INNER JOIN tbl_patients t6 ON t2.patient_id = t6.id
            INNER JOIN tbl_encounter_invoices t8 ON t5.id = t8.account_number_id
            INNER JOIN tbl_invoice_lines t7 ON t7.invoice_id = t8.id
            INNER JOIN tbl_pay_cat_sub_categories t14 ON t14.id= t7.payment_filter
            INNER JOIN tbl_payments_categories t15 ON t14.pay_cat_id= t15.id
            INNER JOIN tbl_item_prices t13 ON t2.item_id = t13.item_id          
            INNER JOIN tbl_departments t18 ON t18.id = t3.dept_id  
            INNER JOIN tbl_theatre_services t ON t.item_id =t2.item_id 
			LEFT JOIN tbl_nurse_wards t21 ON t21.ward_id = t20.ward_id 
			LEFT JOIN tbl_wards t23 ON t23.id = t21.ward_id 	
			LEFT JOIN tbl_status_procedures t19 ON t19.admission_id=t1.id               
         WHERE  
           t2.status=1
		 AND t5.facility_id='".$facility_id."'
        AND TIMESTAMPDIFF(hour,t2.created_at, CURRENT_DATE)<=24
		   GROUP BY t2.visit_date_id,t3.id";

        return DB::SELECT($sql);
    }
    public function getListFromRecovery($facility_id){
        $sql="SELECT 
        t2.patient_id,
        t2.visit_date_id,
        t5.date_attended,
        t3.id AS item_id,
        t5.account_number,
        t6.first_name,
        t6.middle_name,
        t6.last_name,
        t6.gender,
        t6.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, t6.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, t6.dob, CURRENT_DATE), ' Days') END END AS age,
        t6.medical_record_number,
        t6.mobile_number,
        t7.payment_filter,
        t7.status_id AS payment_status_id,
        t3.item_name,
        t1.id AS admission_id,
        t4.name AS doctor_name,
		t22.bed_name,
        t4.mobile_number AS doctor_number,
        CASE 
         WHEN t2.admission_id is NULL THEN 'OPD'  ELSE 'IPD'  END as dept,  
		 CASE 
         WHEN t7.status_id=1 THEN 'NOT PAID'  ELSE 'PAID'  END as payment_status,      
        t2.created_at,
		t23.ward_name,
        t4.facility_id,
		t19.operation_date,
		t20.ward_id
        
         FROM tbl_patient_procedures  t2
            LEFT JOIN tbl_admissions t1 ON t1.account_id=t2.visit_date_id
			LEFT JOIN tbl_instructions t20 ON t1.id=t20.admission_id        
			LEFT JOIN tbl_beds t22 ON t22.id=t20.bed_id  
            INNER JOIN tbl_accounts_numbers t5 ON t2.visit_date_id =t5.id
            INNER JOIN tbl_items t3 ON t3.id = t2.item_id
            INNER JOIN users t4 ON t2.user_id = t4.id
            INNER JOIN tbl_patients t6 ON t2.patient_id = t6.id
            INNER JOIN tbl_encounter_invoices t8 ON t5.id = t8.account_number_id
            INNER JOIN tbl_invoice_lines t7 ON t7.invoice_id = t8.id
            INNER JOIN tbl_pay_cat_sub_categories t14 ON t14.id= t7.payment_filter
            INNER JOIN tbl_payments_categories t15 ON t14.pay_cat_id= t15.id
            INNER JOIN tbl_item_prices t13 ON t2.item_id = t13.item_id          
            INNER JOIN tbl_departments t18 ON t18.id = t3.dept_id  
            INNER JOIN tbl_theatre_services t ON t.item_id =t2.item_id 
			LEFT JOIN tbl_nurse_wards t21 ON t21.ward_id = t20.ward_id 
			LEFT JOIN tbl_wards t23 ON t23.id = t21.ward_id 	
			LEFT JOIN tbl_status_procedures t19 ON t19.admission_id=t1.id               
         WHERE  
           t2.status=1
		 AND t5.facility_id='".$facility_id."'
        AND TIMESTAMPDIFF(hour,t2.created_at, CURRENT_DATE)<=24
		  	 GROUP BY t2.visit_date_id,t3.id";


        return DB::SELECT($sql);
    }
  public function getListFromPostAnaesthetic($facility_id){
        $sql="SELECT 
        t2.patient_id,
        t2.visit_date_id,
        t5.date_attended,
        t3.id AS item_id,
        t5.account_number,
        t6.first_name,
        t6.middle_name,
        t6.last_name,
        t6.gender,
        t6.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, t6.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, t6.dob, CURRENT_DATE), ' Days') END END AS age,
        t6.medical_record_number,
        t6.mobile_number,
        t7.payment_filter,
        t7.status_id AS payment_status_id,
        t3.item_name,
        t1.id AS admission_id,
        t4.name AS doctor_name,
		t22.bed_name,
        t4.mobile_number AS doctor_number,
        CASE 
         WHEN t2.admission_id is NULL THEN 'OPD'  ELSE 'IPD'  END as dept,  
		 CASE 
         WHEN t7.status_id=1 THEN 'NOT PAID'  ELSE 'PAID'  END as payment_status,      
        t2.created_at,
		t23.ward_name,
        t4.facility_id,
		t19.operation_date,
		t20.ward_id
        
         FROM tbl_patient_procedures  t2
            LEFT JOIN tbl_admissions t1 ON t1.account_id=t2.visit_date_id
			LEFT JOIN tbl_instructions t20 ON t1.id=t20.admission_id        
			LEFT JOIN tbl_beds t22 ON t22.id=t20.bed_id  
            INNER JOIN tbl_accounts_numbers t5 ON t2.visit_date_id =t5.id
            INNER JOIN tbl_items t3 ON t3.id = t2.item_id
            INNER JOIN tbl_theatre_services t ON t.item_id =t2.item_id 
            INNER JOIN users t4 ON t2.user_id = t4.id
            INNER JOIN tbl_patients t6 ON t2.patient_id = t6.id
            INNER JOIN tbl_encounter_invoices t8 ON t5.id = t8.account_number_id
            INNER JOIN tbl_invoice_lines t7 ON t7.invoice_id = t8.id
            INNER JOIN tbl_pay_cat_sub_categories t14 ON t14.id= t7.payment_filter
            INNER JOIN tbl_payments_categories t15 ON t14.pay_cat_id= t15.id
            INNER JOIN tbl_item_prices t13 ON t2.item_id = t13.item_id          
            INNER JOIN tbl_departments t18 ON t18.id = t3.dept_id  
			LEFT JOIN tbl_nurse_wards t21 ON t21.ward_id = t20.ward_id 
			LEFT JOIN tbl_wards t23 ON t23.id = t21.ward_id 	
			LEFT JOIN tbl_status_procedures t19 ON t19.admission_id=t1.id               
         WHERE  
           t2.status=1
		 AND t5.facility_id='".$facility_id."'
        AND TIMESTAMPDIFF(hour,t2.created_at, CURRENT_DATE)<=24
		  	 GROUP BY t2.visit_date_id,t3.id";
        return DB::SELECT($sql);
    }

    public function getListFromTheatres($facility_id){
        $sql="SELECT 
        t2.patient_id,
        t2.visit_date_id,
        t5.date_attended,
        t3.id AS item_id,
        t5.account_number,
        t6.first_name,
        t6.middle_name,
        t6.last_name,
        t6.gender,
        t6.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, t6.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, t6.dob, CURRENT_DATE), ' Days') END END AS age,
        t6.medical_record_number,
        t6.mobile_number,
        t7.payment_filter,
        t7.status_id AS payment_status_id,
        t3.item_name,
        t2.admission_id,
        t4.name AS doctor_name,
		t22.bed_name,
        t4.mobile_number AS doctor_number,
        CASE 
         WHEN t2.admission_id is NULL THEN 'OPD'  ELSE 'IPD'  END as dept,  
		 CASE 
         WHEN t7.status_id=1 THEN 'NOT PAID'  ELSE 'PAID'  END as payment_status,      
        t2.created_at,
		t23.ward_name,
        t4.facility_id,
		t19.operation_date,
		t20.ward_id
        
         FROM tbl_patient_procedures  t2
            INNER JOIN tbl_admissions t1 ON t1.id=t2.admission_id
			INNER JOIN tbl_instructions t20 ON t1.id=t20.admission_id        
			INNER JOIN tbl_beds t22 ON t22.id=t20.bed_id  
            INNER JOIN tbl_accounts_numbers t5 ON t2.visit_date_id =t5.id
            INNER JOIN tbl_items t3 ON t3.id = t2.item_id
            INNER JOIN tbl_theatre_services t ON t.item_id =t2.item_id 
            INNER JOIN users t4 ON t2.user_id = t4.id
            INNER JOIN tbl_patients t6 ON t2.patient_id = t6.id
            INNER JOIN tbl_encounter_invoices t8 ON t5.id = t8.account_number_id
            INNER JOIN tbl_invoice_lines t7 ON t7.invoice_id = t8.id
            INNER JOIN tbl_pay_cat_sub_categories t14 ON t14.id= t7.payment_filter
            INNER JOIN tbl_payments_categories t15 ON t14.pay_cat_id= t15.id
            INNER JOIN tbl_item_prices t13 ON t2.item_id = t13.item_id          
            INNER JOIN tbl_departments t18 ON t18.id = t3.dept_id  
			INNER JOIN tbl_nurse_wards t21 ON t21.ward_id = t20.ward_id 
			INNER JOIN tbl_wards t23 ON t23.id = t21.ward_id 	
			INNER JOIN tbl_status_procedures t19 ON t19.admission_id=t1.id               
			INNER JOIN tbl_pre_history_anethetics t25 ON t25.admission_id=t1.id             
         WHERE  
         t2.item_id = t13.item_id
		 AND t1.admission_status_id=2
		 AND t21.facility_id='".$facility_id."'
         AND DATE(t2.created_at)=DATE(t7.created_at)
         AND t2.item_id=t19.item_id
         AND t25.history_type='CONFIRMATION'
		 AND t19.status=6	GROUP BY t2.admission_id";

        return DB::SELECT($sql);
    }

    public function getListFromTheatresReport(Request $request){
	    $facility_id=$request->facility_id;
	    $start=$request->start_date;
	    $end=$request->end_date;

        $sql="SELECT 
        t2.patient_id,
        t2.visit_date_id,
        t5.date_attended,
        t3.id AS item_id,
        t5.account_number,
        t6.first_name,
        t6.middle_name,
        t6.last_name,
        t6.gender,
        t6.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, t6.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, t6.dob, CURRENT_DATE), ' Days') END END AS age,
        t6.medical_record_number,
        t6.mobile_number,
        t3.item_name,
        t2.admission_id,
        t4.name AS doctor_name,
        t4.mobile_number AS doctor_number,  
        t2.created_at,
        t4.facility_id
           FROM tbl_patient_procedures  t2
            LEFT JOIN tbl_accounts_numbers t5 ON t2.visit_date_id =t5.id
            LEFT JOIN tbl_items t3 ON t3.id = t2.item_id
            LEFT JOIN users t4 ON t2.user_id = t4.id
            LEFT JOIN tbl_patients t6 ON t2.patient_id = t6.id
                        
         WHERE  
         t2.status = 6 AND t2.updated_at BETWEEN '".$start."' AND '".$end."'
         	  GROUP BY t2.visit_date_id,t3.id";

        return DB::SELECT($sql);
    }

    public function saveAssociateHistory(Request $request)   {
	$medical=$request->medical;
	$surgical=$request->surgical;
	$admission_id=$request->admission_id;
	$item_id=$request->item_id;
    $history_type=$request->history_type;
    $item_name=$request->item_name;
			 if(empty($medical) AND !isset($request->descriptions)){
	       return response()->json([	'data' =>'PLEASE WRITE NILL IF NO MEDICATION',
								'status' => '0'
										]);
          }

        else if(empty($surgical) AND !isset($request->descriptions)){

            return response()->json([	'data' =>'PLEASE WRITE NILL IF NO SURGICAL',
								'status' => '0'
										]);
}



        else if(patientRegistration::duplicate('tbl_pre_history_anethetics',array('admission_id','history_type','item_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"), array($admission_id,$history_type,$item_id))==true){
	         return response()->json(['data' =>$history_type.' ALREADY EXISTS FOR THIS PATIENT '.$item_name,
								'status' => 0
										]);

       }else{
     if(Tbl_pre_history_anethetic::create($request->all())){
        // Tbl_patient_procedure::where("item_id",$request['item_id'])->where("visit_date_id",$request['visit_date_id'])->update(["status"=>2]);

		//Tbl_theatre_wait::WHERE("id",$request_id)->update(array("received"=>1));
		return response()->json(['data' =>'SUCCEFULLY SAVED',

								  'status' => '1'
										]);
	            }
              }


	}


	public function addTimesQue(Request $request)  {

	$noted_value=$request->noted_value;
	$remarks=$request->remarks;
	$admission_id=$request->admission_id;
	$request_id=$request->request_id;
	$information_category=$request->information_category;

    if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT FROM LEFT PANEL',
								'status' => '0'
										]);
    }

    else if(empty($remarks)){
	       return response()->json([	'data' =>'PLEASE WRITE REMARKS NOTED.',
								'status' => '0'
										]);
    }

    else if(empty($noted_value)){
            return response()->json([	'data' =>'PLEASE WRITE VALUE NOTED.',
								'status' => '0'
										]);
    } else if(patientRegistration::duplicate('tbl_intra_operations',array('request_id','information_category',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"), array($request_id,$information_category))==true){
	         return response()->json(['data' =>$information_category.' INFO ALREADY EXISTS FOR THIS PATIENT',
								      'status' => '0'
										]);

    }else{
	if(Tbl_intra_operation::create($request->all())){
		Tbl_theatre_wait::WHERE("id",$request_id)->update(array("received"=>1));
		return response()->json(['data' =>'SUCCEFULLY SAVED',
								  'status' => '1'
										]);
	            }
    }


	}


	public function addPrBp(Request $request)  {

	$am_pm=$request->am_pm;
	$noted_value=$request->noted_value;
	$mins=$request->mins;
	$hrs=$request->hr;
	$time_taken=$hrs.':'.$mins;
	$admission_id=$request->admission_id;
	$request_id=$request->request_id;
	$information_category=$request->information_category;
	$nurse_id=$request->nurse_id;
	$request_id=$request->request_id;
	$minutes= (int)$mins;

    if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT FROM LEFT PANEL',
								'status' => '0'
										]);
    }

    else if(empty($am_pm)){
	       return response()->json([	'data' =>'SPECIFY IF ITS AM/PM',
								'status' => '0'
										]);
    }

    else if(empty($hrs)){
        return response()->json([	'data' =>'WE NEED HOURS !',
            'status' => '0'
        ]);
    }

    else if(strlen($mins) !=2){
	       return response()->json([	'data' =>'MINUTES MUST HAVE TWO DIGITS',
								'status' => '0'
										]);
    }

    else if(!is_numeric($mins)){
        return response()->json([	'data' =>'ENTER CORRECT MINUTES',
            'status' => '0'
        ]);
    }
  else if(!is_numeric($minutes)){

      return response()->json([	'data' =>'PLEASE SELECT PATIENT FROM LEFT PANEL',
          'status' => '0'
      ]);
  }


    else if(empty($noted_value)){
            return response()->json([	'data' =>'PLEASE WRITE VALUE NOTED.',
								'status' => '0'
										]);
    } else if(patientRegistration::duplicate('tbl_intra_opconditions',array('request_id','time_taken','am_pm','information_category',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"), array($request_id,$time_taken,$am_pm,$information_category))==true){
	         return response()->json(['data' =>$information_category.' INFO ALREADY EXISTS FOR THIS PATIENT',
								      'status' => '0'
										]);

    }else{
	if(Tbl_intra_opcondition::create(array("noted_value"=>$noted_value,"admission_id"=>$admission_id,"time_taken"=>$time_taken,"am_pm"=>$am_pm,"erasor"=>0,"request_id"=>$request_id,"information_category"=>$information_category,"nurse_id"=>$nurse_id))){
		Tbl_theatre_wait::WHERE("id",$request_id)->update(array("received"=>1));
		return response()->json(['data' =>'SUCCEFULLY SAVED',
								  'status' => '1'
										]);
	            }
    }


	}


	public function saveSocialHistory(Request $request)
    {
	$chronic_illness=$request->chronic_illness;
	$substance_abuse=$request->substance_abuse;
	$admission_id=$request->admission_id;
	$adoption=$request->adoption;
	$request_id=$request->request_id;
if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT FROM LEFT PANEL',
								'status' => '0'
										]);
}
else if(patientRegistration::duplicate('tbl_surgery_family_socials',array('admission_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"), array($admission_id))==true){
	return response()->json(['data' =>'FAMILIY & SOCIAL HISTORY ALREADY EXISTS FOR THIS PATIENT',
								'status' => '0'
										]);

}else{

	if(Tbl_surgery_family_social::create($request->all())){

		Tbl_theatre_wait::WHERE("id",$request_id)->update(array("received"=>1));
		return response()->json(['data' =>'SUCCEFULLY SAVED',
								'status' => '1'
										]);
	}

}


	//return Tbl_observation_chart::all();
	}


public function getListItemToServiceInWard(Request $request)
    {
          $search = $request->input('search');
        $id = $request->input('facility_id');
        $category_id = $request->input('patient_category_id');
       $data1=Tbl_pay_cat_sub_category::where('id',$category_id)->take(1)->get();

        $main_category_id = $data1[0]->pay_cat_id;
        if(Tbl_payments_category::find($main_category_id)->category_description == "Insurance"){
			$category_id=1;
		}
		
        $limit = 10;
        $sql = "select * from vw_shop_items where item_name like '%".$search."%' AND  patient_category_id ='".$category_id."' AND facility_id = '".$id."' limit ".$limit;
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

	public function saveResipratorySystem(Request $request)
    {
	$palpation=$request->palpation;
	$auscultation=$request->auscultation;
	$percussion=$request->percussion;
	$other_information=$request->other_information;
	$inspection=$request->inspection;
	$admission_id=$request->admission_id;
	$request_id=$request->request_id;
if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT FROM LEFT PANEL',
								'status' => '0'
										]);
}

else if(empty($palpation) AND empty($auscultation) AND empty($percussion) AND empty($inspection) AND empty($request_id) ){

	return response()->json([	'data' =>$auscultation.' PLEASE WRITE AT LEAST ONE INPUT',
								'status' => '0'
										]);
}


else if(patientRegistration::duplicate('tbl_surgery_physical_examinations',array('admission_id','other_information',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=2))"), array($admission_id,$other_information))==true){
	return response()->json(['data' =>'RESPIRATORY SYSTEM  ALREADY EXISTS FOR THIS PATIENT',
								'status' => '0'
										]);

}else{

	if(Tbl_surgery_physical_examination::create($request->all())){

		Tbl_theatre_wait::WHERE("id",$request_id)->update(array("received"=>1));
		return response()->json(['data' =>'SUCCEFULLY SAVED',
								'status' => '1'
										]);
	}

}

	//return Tbl_observation_chart::all();
	}


	public function getServicesGiven(Request $request)  {
  $sql="SELECT t2.*,t4.name AS requested_by,t2.created_at AS requested_on, CASE WHEN main_category_id = 3 AND status_id =1 THEN 'Exempted' ELSE CASE WHEN main_category_id = 2 AND status_id = 1 THEN 'Insured' ELSE t3.payment_status END END payment_status,(price * quantity) AS cost,((price * quantity)- discount) AS total_cost, CASE WHEN status_id=2 THEN ((price * quantity)- discount) ELSE 0 END AS total_cost_paid, CASE WHEN status_id=1 THEN ((price * quantity)- discount) ELSE 0 END AS total_cost_unpaid
 FROM tbl_invoice_lines t2 join tbl_payment_statuses t3 ON t2.patient_id = $request->patient_id and t2.status_id = t3.id INNER JOIN  users t4 ON t4.id=t2.user_id";

       return DB::SELECT($sql);

    }



	public function saveWardBill(Request $request){

		$account_number_id=$request->selectedService[0]['account_number_id'];
		$patient_id=$request->selectedService[0]['patient_id'];
		$facility_id=$request->selectedService[0]['facility_id'];
		$user_id=$request->selectedService[0]['user_id'];
		$invoice=Tbl_encounter_invoice::create([
				'corpse_id'=>NULL,
				'account_number_id'=>$account_number_id,
				'patient_id'=>$patient_id,
				'user_id'=>$user_id,
				'facility_id'=>$facility_id,
		]);		
		
		$invoice_id=$invoice->id;

		foreach($request->selectedService AS $selectedService){
			$facility_id=$selectedService['facility_id'];
			$item_price_id=$selectedService['item_price_id'];
			$item_type_id=$selectedService['item_type_id'];
			$patient_category_id=$selectedService['patient_category_id'];
			$payment_filter=$selectedService['payment_filter'];
			$quantity=$selectedService['quantity'];
			$user_id=$selectedService['user_id'];
			$item_name=$selectedService['item_name'];
			$discount_by=$selectedService['discount_by'];
			$status_id=$selectedService['status_id'];
			$discount=$selectedService['discount'];
			$patient_id=$selectedService['patient_id'];
			
			if(!is_numeric($quantity)){
				return response()->json([	'data' =>'PLEASE ENTER NUMERIC VALUE IN QUANTITY',
										'status' => 0
												]);
			}

			//check duplicate within 3 minutes
			/**
			else if(patientRegistration::duplicate('tbl_invoice_lines',array('item_type_id','facility_id','corpse_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=1))"), array($item_type_id,$facility_id,$corpse_id))==true){
				return response()->json(['data' =>$item_name.' already recorded,with the same information',
											'status' => 0
													]);

			}
			**/
			else{
				Tbl_invoice_line::create(['invoice_id'=>$invoice_id,
									 'discount_by'=>$discount_by,
									 'facility_id'=>$facility_id,
									 'item_price_id'=>$item_price_id,
									 'item_type_id'=>$item_type_id,
									 'quantity'=>number_format($quantity, 2, '.', ''),
									 'user_id'=>$user_id,
									 'patient_id'=>$patient_id,
									 'status_id'=>$status_id,
									 'discount'=>number_format($discount, 2, '.', ''),
									 'payment_filter'=>$payment_filter]
								);

			}

		}


		return response()->json(['data' =>strtoupper($item_name).' WAS SUCCEFULLY SAVED',
								'status' => 1,
								'patient_id' => $patient_id,
							]);
	}

	public function addGoals(Request $request)
    {
	$targeted_plans=$request->targeted_plans;
	$nurse_diagnosis_id=$request->nurse_diagnosis_id;
	$nursing_care_types=$request->nursing_care_types;
	$admission_id=$request->admission_id;

   if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT ',
								'status' => '0'
										]);
   }else if(empty($targeted_plans)){

	return response()->json([	'data' =>'PLEASE ENTER PLANS YOU WANT TO DEAL WITH ',
								'status' => '0'
										]);
   }
   else if(empty($nurse_diagnosis_id)){

	return response()->json([	'data' =>'SELECT NURSING DIAGNOSIS ',
								'status' => '0'
										]);
   }



else if(patientRegistration::duplicate('tbl_nursing_care_plans',array('nurse_diagnosis_id','admission_id','nursing_care_types','targeted_plans',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"),
array($nurse_diagnosis_id,$admission_id,$nursing_care_types,$targeted_plans))==true){
	return response()->json(['data' =>'YOU DUPLICATE DIAGNOSIS  FOR THIS PATIENT',
								'status' => '0'
										]);

}else{

	if(Tbl_nursing_care_plan::create($request->all())){

		return response()->json(['data' =>'DIAGNOSIS SUCCEFULLY SAVED',
								'status' => '1'
										]);
	}

}

	//return Tbl_observation_chart::all();
	}


	public function addDrugs(Request $request)
    {
	$type_of_drugs_dosage_id=$request->type_of_drugs_dosage_id;
	$how_often=$request->how_often;
	$admission_id=$request->admission_id;

   if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT ',
								'status' => '0'
										]);
   }else if(empty($how_often)){

	return response()->json([	'data' =>'HOW OFTEN IS THIS TAKEN? ',
								'status' => '0'
										]);
   }


else if(patientRegistration::duplicate('tbl_treatment_charts',array('type_of_drugs_dosage_id','admission_id','how_often',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"),
array($type_of_drugs_dosage_id,$admission_id,$how_often))==true){
	return response()->json(['data' =>'YOU DUPLICATE TREATMENTS  FOR THIS PATIENT',
								'status' => '0'
										]);

}else{

	if(Tbl_treatment_chart::create($request->all())){

		return response()->json(['data' =>'TREATMENTS SUCCEFULLY SAVED',
								'status' => '1'
										]);
	}

}

	//return Tbl_observation_chart::all();
	}

		public function getOutStandingBills($patient_id,$facility_id){

		$isDebt=Tbl_invoice_line:: where('patient_id',$patient_id)
		                         ->where('facility_id',$facility_id)
		                         ->where('status_id',1)
								 ->get();

								 if(count($isDebt)>0){
							return true;
								 }


	      }

		public function continuationNotes(Request $request){
		$account_id=$request->account_id;
	    $sql="SELECT t1.*,t2.name AS doctor_name 
		      FROM tbl_continuation_notes t1 
		      INNER JOIN users t2 ON t1.user_id=t2.id
		      WHERE visit_id='".$account_id."' ORDER BY t1.created_at DESC ";

		return DB::SELECT($sql);
		}

		public function doctorNotes(Request $request){
			$nurse_id=$request->nurse_id;

			$sql="SELECT t1.medical_record_number,
				t6.nurse_id,	
				t1.gender,	
				t11.notes,
				t9.name as doctor_rounded,
				t11.created_at AS time_written,
				t11.updated_at,
				t5.ward_name,
				CONCAT(t1.first_name,' ',t1.middle_name,' ',t1.last_name) AS fullname
				FROM tbl_admissions t2
				INNER JOIN tbl_instructions t4 ON t4.admission_id=t2.id
				INNER JOIN tbl_continuation_notes t11 ON t11.visit_id=t2.account_id
				INNER JOIN tbl_beds t10 ON t10.id=t4.bed_id
				INNER JOIN tbl_wards t5 ON t5.id =t4.ward_id
				INNER JOIN tbl_nurse_wards t6 ON t6.ward_id=t10.ward_id
				INNER JOIN tbl_patients t1 ON t1.id = t2.patient_id
				INNER JOIN users t9 ON t2.user_id=t9.id  
				WHERE t2.admission_status_id=2 AND t6.deleted =0 AND  nurse_id=$nurse_id GROUP BY t11.id ORDER BY t11.created_at DESC";
			return DB::SELECT($sql);
		}


	  public function getPendingBills(Request $request){
        return DB::select("SELECT * FROM  tbl_accounts_numbers t1 join tbl_invoice_lines t2 on t1.id=t2.invoice_id
 WHERE t1.id='".$request->visit_id."' and t1.facility_id='".$request->facility_id."'  and is_payable=1 AND status=1");
    }

	public function addDischargeNotes(Request $request)
    {
	$permission_date=date('Y-m-d');
	$admission_id=$request->admission_id;
	$confirm=$request->confirm;
	$nurse_id=$request->nurse_id;
	$account_id=$request->account_id;
	$ward_id=$request->ward_id;
	$bed_id=$request->bed_id;
	$patient_id=$request->patient_id;
	$facility_id=$request->facility_id;
	$admission_status_id=$request->admission_status_id;
	$patient_maincategory_id=$request->patient_maincategory_id;


	if(empty($patient_maincategory_id)==true){
		return response()->json([	'data' =>'PATIENT PAYMENT CATEGORY ,NOT YET PROVIDED',
								'status' => '0'
										]);

	}

   if(!isset($admission_id)){

	return response()->json([	'data' =>'SELECT PATIENT TO DISCHARGE',
								'status' => '0'
										]);
   }


else if(patientRegistration::duplicate('tbl_discharge_permits',array('nurse_id','admission_id','confirm',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"),
array($nurse_id,$admission_id,$confirm))==true){
	return response()->json(['data' =>' PATIENT ALREADY DISCHARGED',
								'status' => 0
										]);

}
else{

	if(Tbl_discharge_permit::create(array("admission_id"=>$admission_id,"confirm"=>1,"nurse_id"=>$nurse_id,"permission_date"=>$permission_date))){
		//then i have to release from ward ...

	 Tbl_admission::WHERE("id",$admission_id)->update(array("admission_status_id"=>$admission_status_id)); //release bed.

	Tbl_bed::WHERE("id",$bed_id)->update(array("occupied"=>0)); //release bed.

$dataSets=['admission_id'=>$admission_id,'facility_id'=>$facility_id,'user_id'=>$nurse_id,'visit_date_id'=>$account_id,'ward_id'=>$ward_id,'admission_status_id'=>4];
	Tbl_status_ward::create($dataSets);


		return response()->json(['data' =>'SUCCESSFULLY DISCHARGED',
								'status' => 1
										]);
	}

}

	}


	public function enterTheatre(Request $request)
    {
	$posted_date=$request->posted_date;
	$prescriptions=$request->prescriptions;
	$admission_id=$request->admission_id;
	$received=$request->received;
	$confirm=$request->confirm;
	$operation_date=$request->operation_date;
	$nurse_id=$request->nurse_id;
	  if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'SELECT PATIENT TO BOOK FOR THEATRE',
								'status' => '0'
										]);
   }else if(empty($prescriptions)){

	return response()->json(['data' =>'PLEASE PROVIDE ANY REMARKS BEFORE REQUESTING FOR OPERATIONS',
								'status' => '0'
										]);
   }

   else if((time()-(60*60*24)) > strtotime($operation_date)){

	return response()->json([	'data' =>'OPERATION DATE MUST START FROM TODAY',
								'status' => '0'
										]);
   }




else if(patientRegistration::duplicate('tbl_theatre_waits',array('nurse_id','admission_id','posted_date',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"),
array($nurse_id,$admission_id,$posted_date))==true){
	return response()->json(['data' =>' PATIENT ALREADY IN THEATRE QUE',
								'status' => '0'
										]);

}else{

	if(Tbl_theatre_wait::create($request->all())){
		//then i have to release BED ...
		return response()->json(['data' =>' PATIENT WAS SUCCESSFULLY BOOKED FOR OPERATION',
								'status' => '1'
										]);
	}

}

	}

	public function addImplementations(Request $request)
    {
	$targeted_plans=$request->targeted_plans;
	$nurse_diagnosis_id=$request->nurse_diagnosis_id;
	$nursing_care_types=$request->nursing_care_types;
	$admission_id=$request->admission_id;

   if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT ',
								'status' => '0'
										]);
   }else if(empty($targeted_plans)){

	return response()->json([	'data' =>'PLEASE ENTER PLANS YOU WANT TO DEAL WITH ',
								'status' => '0'
										]);
   }
   else if(empty($nurse_diagnosis_id)){

	return response()->json([	'data' =>'SELECT NURSING DIAGNOSIS FOR ACTION ',
								'status' => '0'
										]);
   }



else if(patientRegistration::duplicate('tbl_nursing_care_plans',array('nurse_diagnosis_id','admission_id','nursing_care_types','targeted_plans',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"),
array($nurse_diagnosis_id,$admission_id,$nursing_care_types,$targeted_plans))==true){
	return response()->json(['data' =>'YOU DUPLICATE DIAGNOSIS  FOR THIS PATIENT',
								'status' => '0'
										]);

}else{

	if(Tbl_nursing_care_plan::create($request->all())){

		return response()->json(['data' =>'DIAGNOSIS SUCCEFULLY SAVED',
								'status' => '1'
										]);
	}

}

	//return Tbl_observation_chart::all();
	}

	public function addEvaluations(Request $request)
    {
	$targeted_plans=$request->targeted_plans;
	$nurse_diagnosis_id=$request->nurse_diagnosis_id;
	$nursing_care_types=$request->nursing_care_types;
	$admission_id=$request->admission_id;

   if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT ',
								'status' => '0'
										]);
   }else if(empty($targeted_plans)){

	return response()->json([	'data' =>'PLEASE ENTER EVALUATIONS ',
								'status' => '0'
										]);
   }
   else if(empty($nurse_diagnosis_id)){

	return response()->json([	'data' =>'SELECT NURSING DIAGNOSIS FOR EVALUATIONS ',
								'status' => '0'
										]);
   }



else if(patientRegistration::duplicate('tbl_nursing_care_plans',array('nurse_diagnosis_id','admission_id','nursing_care_types','targeted_plans',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"),
array($nurse_diagnosis_id,$admission_id,$nursing_care_types,$targeted_plans))==true){
	return response()->json(['data' =>'YOU DUPLICATE DIAGNOSIS EVALUATIONS',
								'status' => '0'
										]);

}else{

	if(Tbl_nursing_care_plan::create($request->all())){

		return response()->json(['data' =>'DIAGNOSIS EVALUATIONS SUCCEFULLY SAVED',
								'status' => '1'
										]);
	}

}

	//return Tbl_observation_chart::all();
	}

	public function addTimes(Request $request)
    {
	$targeted_plans=$request->targeted_plans;
	$nurse_diagnosis_id=$request->nurse_diagnosis_id;
	$nursing_care_types=$request->nursing_care_types;
	$daytime=$request->daytime;
	$resultsTime=$request->resultsTime;
	$admission_id=$request->admission_id;
	$nurse_id=$request->nurse_id;

   if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT ',
								'status' => '0'
										]);
   }else if(empty($targeted_plans)){

	return response()->json([	'data' =>'PLEASE ENTER TIMES ',
								'status' => '0'
										]);
   }
   else if(empty($nurse_diagnosis_id)){

	return response()->json([	'data' =>'SELECT NURSING DIAGNOSIS FOR TIMES ',
								'status' => '0'
										]);
   }

   else if(empty($daytime)){

	return response()->json([	'data' =>$daytime.' SELECT AM /PM FROM SELECTIONS',
								'status' => '0'
										]);
   }



else if(patientRegistration::duplicate('tbl_nursing_care_plans',array('nurse_diagnosis_id','admission_id','nursing_care_types','targeted_plans',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"),
array($nurse_diagnosis_id,$admission_id,$nursing_care_types,$targeted_plans))==true){
	return response()->json(['data' =>'YOU DUPLICATE DIAGNOSIS TIMING ',
								'status' => '0'
										]);

}else{

	if(Tbl_nursing_care_plan::create(array("nurse_diagnosis_id"=>$nurse_diagnosis_id,"nursing_care_types"=>$nursing_care_types,"targeted_plans"=>$targeted_plans,
	"nurse_id"=>$nurse_id,"admission_id"=>$admission_id))){

		return response()->json(['data' =>'DIAGNOSIS TIMING SUCCEFULLY SAVED',
								'status' => '1'
										]);
	}

}

	//return Tbl_observation_chart::all();
	}

	public function addOutPuts(Request $request)
    {
	$observed_amount=$request->amount;
	$observation_output_type_id=$request->observation_output_type_id;
	$si_units=$request->si_units;
	$admission_id=$request->admission_id;
if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT ',
								'status' => '0'
										]);
}
else if(empty($si_units)){

	return response()->json([	'data' =>'UNITS FOR THE AMOUNT OBSERVED',
								'status' => '0'
										]);
}
else if(!is_numeric($observation_output_type_id)){

	return response()->json([	'data' =>'SELECT OUTPUT TYPE',
								'status' => '0'
										]);
}
else if(is_numeric($si_units)){

	return response()->json([	'data' =>'PLEASE ENTER UNITS',
								'status' => '0'
										]);
}

else if(empty($observed_amount)){

	return response()->json([	'data' =>'PLEASE ENTER AMOUNT OBSERVED',
								'status' => '0'
										]);
}

else if(!is_numeric($observed_amount)){

	return response()->json([	'data' =>'ONLY NUMERIC VALUE IS ALLOWED',
								'status' => '0'
										]);
}

else if(patientRegistration::duplicate('tbl_output_observations',array('observation_output_type_id','admission_id','amount',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($observation_output_type_id,$admission_id,$observed_amount))==true){
	return response()->json(['data' =>'YOU DUPLICATE FOR THIS PATIENT',
								'status' => '0'
										]);

}else{

	if(Tbl_output_observation::create($request->all())){

		return response()->json(['data' =>'SUCCEFULLY SAVED',
								'status' => '1'
										]);
	}

}

	//return Tbl_observation_chart::all();
	}


	public function addIntakeObservation(Request $request)
    {
	$observed_amount=$request->intravenous_mils;
	$intravenous_types_id=$request->intravenous_types_id;
	$admission_id=$request->admission_id;
if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT ',
								'status' => '0'
										]);
}
else if(!is_numeric($intravenous_types_id)){

	return response()->json([	'data' =>'PLEASE SELECT INTRAVENOUS  TYPE',
								'status' => '0'
										]);
}

else if(empty($observed_amount)){

	return response()->json([	'data' =>'PLEASE ENTER AMOUNT OBSERVED',
								'status' => '0'
										]);
}

else if(!is_numeric($observed_amount)){

	return response()->json([	'data' =>'ONLY NUMERIC VALUE IS ALLOWED',
								'status' => '0'
										]);
}

else if(patientRegistration::duplicate('tbl_intake_observations',array('intravenous_types_id','admission_id','intravenous_mils',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <5))"), array($intravenous_types_id,$admission_id,$observed_amount))==true){
	return response()->json(['data' =>'YOU DUPLICATE FOR THIS PATIENT',
								'status' => '0'
										]);

}else{

	if(Tbl_intake_observation::create($request->all())){

		return response()->json(['data' =>'SUCCEFULLY SAVED',
								'status' => '1'
										]);
	}

}
	}

public function addIntakeFluid(Request $request){
	$observed_amount=$request->oral_mils;
	$oral_types_id=$request->oral_types_id;
	$admission_id=$request->admission_id;
if(!is_numeric($admission_id)){

	return response()->json([	'data' =>'PLEASE SELECT PATIENT ',
								'status' => '0'
										]);
}
else if(!is_numeric($oral_types_id)){

	return response()->json([	'data' =>'PLEASE SELECT INTRAVENOUS  TYPE',
								'status' => '0'
										]);
}

else if(empty($observed_amount)){

	return response()->json([	'data' =>'PLEASE ENTER AMOUNT OBSERVED',
								'status' => '0'
										]);
}

else if(!is_numeric($observed_amount)){

	return response()->json([	'data' =>'ONLY NUMERIC VALUE IS ALLOWED',
								'status' => '0'
										]);
}

else if(patientRegistration::duplicate('tbl_intake_observations',array('oral_types_id','admission_id','oral_mils',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <5))"), array($oral_types_id,$admission_id,$observed_amount))==true){
	return response()->json(['data' =>'YOU DUPLICATE FOR THIS PATIENT',
								'status' => '0'
										]);

}else{

	if(Tbl_intake_observation::create($request->all())){

		return response()->json(['data' =>'SUCCEFULLY SAVED',
								'status' => '1'
										]);
	}

}

	//return Tbl_observation_chart::all();
	}

	public function getAdmnThisBed($request)
    {
    $bed_id=$request;
	$sql="SELECT * FROM `vw_approved_admission` t1 WHERE bed_id='{$bed_id}' AND (admission_id NOT IN
    (SELECT admission_id
     FROM tbl_discharge_permits))";

	$sql=DB::select($sql);
	//$sql=json_decode($sql);

	if(count($sql)>0){
		return response()->json([
							'data' => $sql[0]->fullname.' SINCE '.$sql[0]->updated_at,
								'status' => '1'
										]);
	}else{

		return response()->json([
								'data' =>'SORRY THIS BED HAS NO PATIENT YET.',
								'status' => '0'
										]);
	}


	}

	public function getFullAdmitedPatientInfo($request)
    {
		$sql="SELECT * FROM vw_approved_admission t1,vw_beds t2,vw_wards t3 WHERE t1.ward_id=t3.ward_id AND t1.bed_id =t2.bed_id AND t1.admission_id='{$request}'";
	    return DB::select($sql);

	}

	public function getPatientSentToTheatre(Request $request)
    {

		$sql="SELECT * FROM vw_approved_admission t1,vw_beds t2,vw_wards t3,tbl_theatre_waits t4 WHERE t1.ward_id=t3.ward_id AND t1.bed_id =t2.bed_id AND t1.admission_id=t4.admission_id AND  t4.received=0";


	    return DB::select($sql);
	}

	public function attendPatientTheatre(Request $request)
    {

		$sql="SELECT * FROM vw_approved_admission t1,vw_beds t2,vw_wards t3,tbl_theatre_waits t4 WHERE t1.ward_id=t3.ward_id AND t1.bed_id =t2.bed_id AND t1.admission_id=t4.admission_id AND t4.confirm=1";


	    return DB::select($sql);
	}


	public function getInstructions(Request $request)
    {
		$response=[];
		 $patient_id=$request->patient_id;
		 $ward_id=$request->ward_id;
		    $response[]= Tbl_patient::where('id',$patient_id)->orderBy('id','DESC')->first();
		 	$response[]=  DB::table('vw_pending_admission')->where('patient_id',$patient_id)->get();

		  //get list of the beds available but NOT DUMMY BED
		   $sql="SELECT  t2.id AS bed_id,
		t1.id AS ward_id,
		t1.ward_name AS ward_full_name,
		t1.ward_type_id,
		t2.bed_name,
		CONCAT(t2.bed_name,' ',t3.bed_type) AS bed_available,
		t3.bed_type,
		t3.id AS bed_type_id,
		t2.occupied,
		t1.facility_id 
        FROM tbl_wards t1
		INNER JOIN tbl_beds t2 ON t2.ward_id=t1.id
		INNER JOIN tbl_bed_types t3  ON t2.bed_type_id=t3.id
		INNER JOIN tbl_wards_types t4  ON t4.id=t1.ward_type_id
		WHERE t2.occupied=0 
		  AND t3.id <> 4
		  AND t1.id='".$ward_id."'";


		$response[] =DB::SELECT($sql);
		  	return $response;
	}

	public function getBedsWithNoPatients(Request $request)
    {
		 $ward_id=$request->ward_id;
         $sql="SELECT  t2.id AS bed_id,
		t1.id AS ward_id,
		t1.ward_name AS ward_full_name,
		t1.ward_type_id,
		t2.bed_name,
		CONCAT(t2.bed_name,' ',t3.bed_type) AS bed_available,
		t3.bed_type,
		t3.id AS bed_type_id,
		t2.occupied,
		t1.facility_id 
        FROM tbl_wards t1
		INNER JOIN tbl_beds t2 ON t2.ward_id=t1.id
		INNER JOIN tbl_bed_types t3  ON t2.bed_type_id=t3.id
		INNER JOIN tbl_wards_types t4  ON t4.id=t1.ward_type_id
		WHERE t2.occupied=0 
		  AND t3.id <> 4
		  AND t1.id='".$ward_id."'";

		return DB::SELECT($sql);
/**
		 $getBedsWithNoPatients =DB::table('vw_beds')
						        ->where('ward_id',$ward_id)
						        ->where('occupied',0)
						        ->get();
						return $getBedsWithNoPatients;
						**/
	}


public function changePatientBed(Request $request){
    $old_bed_id=$request->old_bed_id;
	$new_bed_id=$request->new_bed_id;
	$admission_id=$request->admission_id;
	$patient_name=$request->patient_name;
    Tbl_bed::WHERE("id",$old_bed_id)->update(array("occupied"=>0)); //release old bed.

	Tbl_bed::WHERE("id",$new_bed_id)->update(array("occupied"=>1)); //occupy new bed.

	Tbl_instruction::WHERE("id",$admission_id)->update(array("bed_id"=>$new_bed_id)); //give patient  new occupied bed.

       return response()->json([
								'data' =>'BED WAS SUCCEFULLY CHANGED FOR '.$patient_name,
								'status' => '1'
										]);
			 }


			 public function getOrderedProcedures($nurse_id){

           $sql="SELECT 
        t2.patient_id,
        t2.visit_date_id,
        t5.date_attended,
        t3.id AS item_id,
        t5.account_number,
        t6.first_name,
        t6.middle_name,
        t6.last_name,
        t6.gender,
        t6.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, t6.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, t6.dob, CURRENT_DATE), ' Days') END END AS age,
        t6.medical_record_number,
        t6.mobile_number,
        t7.payment_filter,
        t7.status_id AS payment_status,
        t3.item_name,
        t2.admission_id,
        t4.name AS doctor_name,
        t4.mobile_number AS doctor_number,
        CASE 
         WHEN t2.admission_id is NULL THEN 'OPD'  ELSE 'IPD'  END as dept,        
        t2.created_at,
        t4.facility_id,
		t20.ward_id
        
         FROM tbl_patient_procedures  t2
            INNER JOIN tbl_admissions t1 ON t1.id=t2.admission_id
			INNER JOIN tbl_instructions t20 ON t1.id=t20.admission_id          
            INNER JOIN tbl_accounts_numbers t5 ON t2.visit_date_id =t5.id
            INNER JOIN tbl_items t3 ON t3.id = t2.item_id
            INNER JOIN users t4 ON t2.user_id = t4.id
            INNER JOIN tbl_patients t6 ON t2.patient_id = t6.id
            INNER JOIN tbl_encounter_invoices t8 ON t5.id = t8.account_number_id
            INNER JOIN tbl_invoice_lines t7 ON t7.invoice_id = t8.id
            INNER JOIN tbl_pay_cat_sub_categories t14 ON t14.id= t7.payment_filter
            INNER JOIN tbl_payments_categories t15 ON t14.pay_cat_id= t15.id
            INNER JOIN tbl_item_prices t13 ON t2.item_id = t13.item_id          
            INNER JOIN tbl_departments t18 ON t18.id = t3.dept_id  
			INNER JOIN tbl_nurse_wards t21 ON t21.ward_id = t20.ward_id 	               
         WHERE  
         t7.item_price_id = t13.id
         AND t2.item_id = t13.item_id
		 AND t1.admission_status_id=2
		 AND t21.nurse_id='".$nurse_id."'
         AND t2.item_id NOT IN (SELECT item_id FROM  tbl_status_procedures t19 WHERE t19.admission_id=t1.id) 	GROUP BY t2.admission_id";

        return DB::SELECT($sql);
			 }

public function saveConsent(Request $request){

	$signedDate= date("Y-m-d", strtotime($request->dateSigned));

	  if(!is_numeric($request->relationshipsID)){
			  return response()->json([
								'data' =>" SELECT RELATION SHIP FROM THE SUGESTIONS LIST",
								'status' => 0
										]);
	   } else if($signedDate < date('Y-m-d') ){
	     return response()->json([	'data' =>'INVALID DATE FOR SIGNED CONSENT FORM ',
								'status' => 0
										]);
       }


       Tbl_informed_consent::create($request->all());
         Tbl_status_procedure::where("admission_id",$request->admission_id)->update(["status"=>2]);
    Tbl_patient_procedure::where("item_id",$request['item_id'])->where("visit_date_id",$request['visit_date_id'])->update(["status"=>1]);

              return response()->json([	'data' =>'RELATIVE INFORMATION WAS SUCCESSFULLY SAVED.',
								        'status' => 1
										]);
       }
    public function getAnaethesiaList($facility_id){
        $sql="SELECT 
        t2.patient_id,
        t2.visit_date_id,
        t5.date_attended,
        t3.id AS item_id,
        t5.account_number,
        t6.first_name,
        t6.middle_name,
        t6.last_name,
        t6.gender,
        t6.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, t6.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, t6.dob, CURRENT_DATE), ' Days') END END AS age,
        t6.medical_record_number,
        t6.mobile_number,
        t7.payment_filter,
        t7.status_id AS payment_status_id,
        t3.item_name,
        t1.id AS admission_id,
        t4.name AS doctor_name,
		t22.bed_name,
        t4.mobile_number AS doctor_number,
        CASE 
         WHEN t2.admission_id is NULL THEN 'OPD'  ELSE 'IPD'  END as dept,  
		 CASE 
         WHEN t7.status_id=1 THEN 'NOT PAID'  ELSE 'PAID'  END as payment_status,      
        t2.created_at,
		t23.ward_name,
        t4.facility_id,
		t19.operation_date,
		t20.ward_id
        
         FROM tbl_patient_procedures  t2
            LEFT JOIN tbl_admissions t1 ON t1.account_id=t2.visit_date_id
			LEFT JOIN tbl_instructions t20 ON t1.id=t20.admission_id        
			LEFT JOIN tbl_beds t22 ON t22.id=t20.bed_id  
            INNER JOIN tbl_accounts_numbers t5 ON t2.visit_date_id =t5.id
            INNER JOIN tbl_theatre_services t ON t.item_id =t2.item_id
            INNER JOIN tbl_items t3 ON t3.id = t2.item_id
            INNER JOIN users t4 ON t2.user_id = t4.id
            INNER JOIN tbl_patients t6 ON t2.patient_id = t6.id
            INNER JOIN tbl_encounter_invoices t8 ON t5.id = t8.account_number_id
            INNER JOIN tbl_invoice_lines t7 ON t7.invoice_id = t8.id
            INNER JOIN tbl_pay_cat_sub_categories t14 ON t14.id= t7.payment_filter
            INNER JOIN tbl_payments_categories t15 ON t14.pay_cat_id= t15.id
            INNER JOIN tbl_item_prices t13 ON t2.item_id = t13.item_id          
            INNER JOIN tbl_departments t18 ON t18.id = t3.dept_id  
			LEFT JOIN tbl_nurse_wards t21 ON t21.ward_id = t20.ward_id 
			LEFT JOIN tbl_wards t23 ON t23.id = t21.ward_id 	
			LEFT JOIN tbl_status_procedures t19 ON t19.admission_id=t1.id 
			              
         WHERE  
           t2.status=0 
		 AND t5.facility_id='".$facility_id."'
        AND TIMESTAMPDIFF(hour,t2.created_at, CURRENT_DATE)<=24
		  	GROUP BY t2.visit_date_id,t3.id";

        return DB::SELECT($sql);
    }
			 public function saveGivenDrug(Request $request){
				$medical=$request->medical;
				$surgical=$request->surgical;
				$admission_id=$request->admission_id;
				$item_id=$request->item_id;
				$history_type=$request->history_type;
				$item_name=$request->item_name;
				$procedure_status=3;

				if($request->history_type=="END SESSION OPERATION"){
					$procedure_status=4;

                }
				else if($request->history_type =="END SESSION RECOVERY" || $request->history_type =="ANAESTHETIC COMPLICATIONS"|| $request->history_type =="OPERATION FINDINGS"){
					$procedure_status=5;

                }
				else if($request->history_type =="END SESSION POST OPERATION"){
					$procedure_status=6;
                    Tbl_patient_procedure::where("item_id",$request['item_id'])->where("visit_date_id",$request['visit_date_id'])->update(["status"=>6]);

                }
				else if($request->history_type =="DESCRIPTION OF PROCEDURE"){
					$procedure_status=6;
				}else if($request->history_type =="POST OPERATIVE ORDERS"){
					$procedure_status=6;
				}else if($request->history_type =="ATTENTION"){
					$procedure_status=7;
                    Tbl_patient_procedure::where("item_id",$request['item_id'])->where("visit_date_id",$request['visit_date_id'])->update(["status"=>6,
                        'user_id'=>$request['user_id']]);

                }
				if(!isset($request->visit_date_id)){
					return response()->json([	'data' =>'PLEASE SELECT PATIENT FROM LEFT PANEL',
						'status' => 0
					]);
				}

				else if(patientRegistration::duplicate('tbl_pre_history_anethetics',array('visit_date_id','history_type','item_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=2))"), array($request->visit_date_id,$history_type,$item_id))==true){
					return response()->json(['data' =>$history_type.' ALREADY EXISTS FOR THIS PATIENT '.$item_name,
						'status' => 0
					]);

				}else{
					if(Tbl_pre_history_anethetic::create($request->all())){

						Tbl_status_procedure::WHERE("admission_id",$admission_id)->update(array("status"=>$procedure_status));
						return response()->json(['data' =>$history_type.' SUCCEFULLY SAVED',

							'status' => '1'
						]);
					}
				}


			}
    public function saveVitalSigns(Request $request){
        if(empty($request->vital_sign_value)){
            return response()->json([
                'data' => 'Enter Value For Vital Signs',
                'status' => 0
            ]);
        }

        Tbl_vital_sign::create(["vital_sign_id"=>$request->vital_sign_id,"vital_sign_value"=>$request->vital_sign_value,
            "visiting_id"=>$request->visiting_id,
            "registered_by"=>$request->registered_by,
            "date_taken"=>date("Y-m-d"),
            "time_taken"=>date("h:i:s")
        ]);

        return response()->json([
            'data' => $request->vital_sign_value. ' Successfully Saved',
            'status' => 1
        ]);
    }


    public function changePatientWard(Request $request){
     $old_bed_id=$request->old_bed_id;
	$admission_id=$request->admission_id;
    $visit_date_id=$request->visit_date_id;
	$patient_name=$request->patient_name;
	$transferReason=$request->transferReason;
    $nurse_id=$request->nurse_id;
	$patient_id=$request->patient_id;
	$ward_id=$request->ward_id;
	$old_ward_id=$request->old_ward_id;
	$facility_id=$request->facility_id;
	if($request->visit_date_id==""){
		return response()->json([
								'data' =>$patient_name." FOLIO NUMBER NOT IN WARD",
								'status' => 0
										]);

	}
	 else	if(patientRegistration::duplicate('tbl_admissions',array('account_id','admission_status_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)>=0))"), array($request->visit_date_id,1))==true){
          return response()->json([
						'data' => $patient_name.' ALREADY TRANSFERED',
						'status' => 0
						]);
		 }

    Tbl_bed::WHERE("id",$old_bed_id)->update(array("occupied"=>0)); //release old bed.
	 Tbl_admission::where("id",$admission_id)->update(["admission_status_id"=>5]);
   //get transfer in record
    if(Tbl_status_ward::create(["facility_id"=>$facility_id,"user_id"=>$nurse_id,"visit_date_id"=>$visit_date_id,"admission_id"=>$admission_id,"admission_status_id"=>6,"ward_id"=>$ward_id])) {
        //get transfer out record
        Tbl_status_ward::create(["facility_id" => $facility_id, "user_id" => $nurse_id, "visit_date_id" => $visit_date_id, "admission_id" => $admission_id, "admission_status_id" => 5, "ward_id" => $old_ward_id]);
     }
    $admission=Tbl_admission::create(["admission_date"=>date("Y-m-d h:i:s"),"patient_id"=>$patient_id,"account_id"=>$visit_date_id,"admission_status_id"=>1,"user_id"=>$nurse_id,"facility_id"=>$facility_id]) ;//transfer to new ward.
    $admission_new_id=$admission->id;
	Tbl_instruction::create(["instructions"=>$transferReason,"ward_id"=>$ward_id,"patient_id"=>$patient_id,"admission_id"=>$admission_new_id,"user_id"=>$nurse_id,"facility_id"=>$facility_id]) ;//transfer with another notes new ward.
     return response()->json([
								'data' =>$patient_name." WILL NO LONGER AVAILABLE IN YOUR WARD",
								'status' => 1
										]);
			 }








	public function giveBed(Request $request) {

		 $ward_id=$request->ward_id;
		 $bed_id=$request->bed_id;
		 $admission_id=$request->admission_id;
		 if(!isset($request->visit_date_id)==true){
		return response()->json([
								'data' =>" FOLIO NUMBER NOT IN WARD",
								'status' => 0
										]);

	}
        Tbl_status_ward::create($request->all());

        $getBedsWithNoPatients =DB::table('vw_beds')
						        ->where('ward_id',$ward_id)
						        ->where('bed_id',$bed_id)
						        ->where('occupied',0)
						        ->get();



			$undo_beds =DB::table('tbl_instructions')
						        ->where('admission_id',$admission_id)
						        ->get();



		 $bedresult_id=$undo_beds[0]->bed_id;



		 if(!isset($bedresult_id)){

         if(count($getBedsWithNoPatients)==1){

			if(Tbl_bed::WHERE("id",$bed_id)->update(array("occupied"=>1))){

				Tbl_admission::WHERE("id",$admission_id)->update(array("admission_status_id"=>2));

				//mtuha tallying
				$patient = DB::select("select p.gender, p.dob, a.facility_id from tbl_patients p join tbl_admissions a on p.id=a.patient_id and a.id = '$admission_id'");
				$request['dob'] = $patient[0]->dob;
				$request['gender'] = $patient[0]->gender;
				$request['facility_id'] = $patient[0]->facility_id;
				ReportGenerators::countAdmission($request);
				//end mtuha
				Tbl_instruction::WHERE("admission_id",$admission_id)->update(array("bed_id"=>$bed_id));
			}
			 return response()->json([
								'data' =>'BED WAS SUCCEFULLY GIVEN TO PATIENT',
								'status' => '1'
										]);
		 }
		  }
		  else if (isset($bedresult_id)){

			   if(Tbl_bed::WHERE("id",$bed_id)->update(array("occupied"=>1))){
		Tbl_bed::WHERE("id",$bedresult_id)->update(array("occupied"=>0));

		Tbl_instruction::WHERE("admission_id",$admission_id)->update(array("bed_id"=>$bed_id));
		  Tbl_admission::WHERE("id",$admission_id)->update(array("admission_status_id"=>2));
		 return response()->json([
								'data' =>'BED WAS SUCCEFULLY CHANGED',
								'status' => '1'
										]);
			 }


		  }

		 else{

			return response()->json([
								'data' =>'SORRY THIS BED HAS ALREADY GIVEN TO ANOTHER PATIENT',
								'status' => '0'
										]);
		 }

	}

	public function getWardTypes(Request $request)
    {
		 	 $wardTypes=  DB::table('tbl_wards_types')
							->get();
						return $wardTypes;

	}

	public function getWardClasses(Request $request)
    {        $searchKey = $request->input('searchKey');
		 	 $getWardClass=  DB::table('vw_shop_items')
			                ->where('item_category','WARD')
			                ->where('item_name','like','%'.$searchKey.'%')
			                ->groupBy('item_name')
							->get();


						return $getWardClass;

	}

	public function getDrugs(Request $request)
    {        $getDrugs=  DB::table('vw_shop_items')
			                ->where('item_category','MEDICATION')
			                ->groupBy('item_name')
							->get();


						return $getDrugs;

	}


		public function searchDrugs($searchKey)  {
			      $getDrugs=  DB::table('vw_shop_items')
			                ->where('item_category','MEDICATION')
							->where('item_name','like','%'.$searchKey.'%')
			                ->groupBy('item_name')
							->get();


						return $getDrugs;

	}





	public function getWards(Request $request)
    {
		     $facility_id=$request->facility_id;
			 	 $wards=DB::table('vw_wards')
			                ->where('facility_id',$facility_id)
							->get();
						return $wards;

	}

public function getWardsToChange(Request $request)  {
		       $facility_id=$request->facility_id;
			   $ward_id=$request->ward_id;

			 	 $wards=DB::table('vw_wards')
			                ->where('facility_id',$facility_id)
							 ->where('ward_id',"!=",$ward_id)
							->get();
						return $wards;

	}


	public function getWardOneInfo(Request $request)
    {
		   $response=[];
		    $ward_id=$request->ward_id;
         $sql_1="SELECT  t2.id AS bed_id,
		t1.id AS ward_id,
		t1.ward_name AS ward_full_name,
		t1.ward_type_id,
		CONCAT(t2.bed_name,' ',t3.bed_type) AS bed_available,
		t3.bed_type,
		t3.id AS bed_type_id,
		t2.occupied,
		t1.facility_id 
        FROM tbl_wards t1
		INNER JOIN tbl_beds t2 ON t2.ward_id=t1.id
		INNER JOIN tbl_bed_types t3  ON t2.bed_type_id=t3.id
		INNER JOIN tbl_wards_types t4  ON t4.id=t1.ward_type_id
		WHERE  t3.id <> 4
		   AND t1.id='".$ward_id."'";

		 $response[]= DB::SELECT($sql_1);

		 $sql_2=   "SELECT count(*) AS beds_number FROM tbl_beds t1
		            WHERE   t1.bed_type_id <> 4
		            AND     t1.ward_id='".$ward_id."'";



		 $response[]= DB::SELECT($sql_2);


			return $response;

	}

	public function getBedsNumber(Request $request)
    {


		         $ward_id=$request->ward_id;
			 	 $wards=DB::table('vw_beds')
			                ->where('ward_id',$ward_id)
							->get();
						return $wards->count();

	}


	public function getBeds(Request $request)
    {


		         $ward_id=$request->ward_id;
			 	 $beds=DB::table('vw_beds')
			                ->where('ward_id',$ward_id)
							->get();
						return $beds;

	}

	public function searchWardTypes(Request $request)
    {
		  $searchKey = $request->input('searchKey');
        $ward_types=DB::table('tbl_wards_types')
		->where('ward_type_name','like','%'.$searchKey.'%')
        ->get();
        return $ward_types;

	}

	public function searchBedTypes(Request $request)
    {
		$searchKey = $request->input('searchKey');
		 $bed_types=DB::table('tbl_bed_types')
		->where('bed_type','like','%'.$searchKey.'%')
		->get();
        return $bed_types;

	}


	public function saveWardTypes(Request $request)
    {
		 $ward_type_name=strtoupper($request->ward_type_name);
		 if(empty($ward_type_name)){
			 return response()->json([
								'data' => 'WARD TYPE MUST BE FILLED',
								'status' => '0'
										]);

}else if(patientRegistration::duplicate('tbl_wards_types',array('ward_type_name',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($ward_type_name))==true){

	return response()->json([
								'data' => $ward_type_name.' ALREADY EXISTS',
								'status' => '0'
								]);

		  }

		 $save_ward_type =Tbl_wards_type::create(array('ward_type_name'=>$ward_type_name));


            if($save_ward_type->save()){

           return response()->json([
								'data' => $ward_type_name.' SUCCEFULLY SAVED.',
								'status' => '1'
										]);

	                     }else{

							return response()->json([
								'data' => 'SOMETHING WENT WRONG IN YOUR SERVER',
								'status' => '0'
										]);
						 }
}



//Register Equipments
    public function addWardGrade(Request $request)
    {
        $ward_class=$request['ward_class'];
        $facility_id=$request['facility_id'];
        $user_id=$request['user_id'];

        if(empty($ward_class)){
            return response()->json(
                ['data'=>"Please Enter Ward Grade",
                    'status'=>0
                ]);

        }

      else if(patientRegistration::duplicate('tbl_items',array('item_name','dept_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($ward_class,5))==true){
            return response()->json(['data' =>$ward_class.'  Already Exist',
                'status' =>0
            ]);
        }
        else{
            $item_added=Tbl_item::create(array("item_name"=>$ward_class,"dept_id"=>5));
            $item_id=$item_added->id;
                 if(Tbl_item_type_mapped::create(array("unit_of_measure"=>1,"item_id"=>$item_id,"item_category"=>'WARD',"sub_item_category"=>'WARD'))) {
                      return response()->json(
                        ['data'=>$ward_class.' Successful Registered',
                            'status'=>1
                        ]
                    ) ;
                }



        }
    }

public function saveWards(Request $request)
    {
		 $ward_name=strtoupper($request->ward_name);
		 $ward_type=strtoupper($request->ward_type_id);
		 $facility_id=strtoupper($request->facility_id);
		 $ward_type_name=strtoupper($request->ward_type_name);
		 $ward_class_id=strtoupper($request->ward_class_id);
		 if(empty($ward_name)){
			 return response()->json([
								'data' => 'WARD NAME MUST BE FILLED',
								'status' => '0'
										]);

         }else if(!isset($ward_type)){
			 return response()->json([
								'data' => 'WARD TYPE MUST BE SELECTED FROM THE SUGESTION LIST',
								'status' => '0'
										]);

         }
		 else if(!isset($ward_class_id)){
			 return response()->json([
								'data' => ' WARD CLASS MUST BE SELECTED FROM THE SUGESTION LIST',
								'status' => '0'
										]);

         }
else if(patientRegistration::duplicate('tbl_wards',array('ward_name','ward_type_id','facility_id','ward_class_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($ward_name,$ward_type,$facility_id,$ward_class_id))==true){

	return response()->json([
								'data' => $ward_name.' FOR '.$ward_type_name.' ALREADY EXISTS',
								'status' => '0'
								]);

		  }

	$save_wards =Tbl_ward::create(array('ward_class_id'=>$ward_class_id,'facility_id'=>$facility_id,'ward_name'=>$ward_name,'ward_type_id'=>$ward_type));


            if($save_wards->save()){

           return response()->json([
							'data' => $ward_name.' FOR '.$ward_type_name.' SUCCEFULLY SAVED.',
							'status' => '1'
										]);

	                     }else{

							return response()->json([
								'data' => 'SOMETHING WENT WRONG IN YOUR SERVER',
								'status' => '0'
										]);
						 }
}


public function prescribeNurse(Request $request){

	     $item_id=strtoupper($request->item_id);
		 $patient_id=strtoupper($request->patient_id);
		 $facility_id=strtoupper($request->facility_id);
		 $date_dosage=strtoupper($request->date_dosage);
	     $timedosage=strtoupper($request->timedosage);
         $remarks=strtoupper($request->remarks);
         $user_id=strtoupper($request->user_id);
		 $admission_id=strtoupper($request->admission_id);
         $item_name=strtoupper($request->item_name);



		 $date_dosage= date("Y-m-d", strtotime($date_dosage));
        $date_dosage_show= date("d-m-Y", strtotime($date_dosage));

		 if(empty($date_dosage)){
			 return response()->json([
						'data' => 'SELECT DATE FOR THIS DOSAGE',
						'status' => '0'
						]);

         }

		 else if (($date_dosage < date("Y-m-d")) AND empty($remarks) ){

    	 return response()->json([
						'data' => 'REMARKS FOR  '.$date_dosage_show,
						'status' => 0
						]);
		 }

		  else if (($date_dosage > date("Y-m-d"))){

    	 return response()->json([
						'data' => 'INVALID DATE  '.$date_dosage_show,
						'status' => 0
						]);
		 }

		   else if (empty($timedosage)){

    	 return response()->json([
						'data' => 'TIME DOSE GIVEN  ',
						'status' => 0
						]);
		 }


		 else if (isset($request->nursePrescriber)==true AND !isset($request->instruction)){
           	 return response()->json([
						'data' => 'REASON FOR PRESCRIBE '.$item_name.' AS NURSE '.$remarks,
						'status' => 0
						]);
		 }

		 else	if(patientRegistration::duplicate('tbl_ipdtreatments',array('admission_id','item_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) < 10))"), array($admission_id,$item_id))==true){
          return response()->json([
						'data' => $item_name.' ALREADY TAKEN,IF VOMITED WAIT FOR 10 MINS',
						'status' => 0
						]);
		 }

		   else if (isset($request->nursePrescriber)==true){
                 Tbl_prescription::create($request->all());

	       }

		Tbl_ipdtreatment::create($request->all());



		 return response()->json([
						'data' => "DISPENSED ".$item_name,
						'status' => 1
						]);

}

public function getInfoForAdmittedPatient($admission_id){
           $sql="SELECT * FROM vw_approved_admission t1 WHERE t1.admission_id='".$admission_id."' GROUP BY t1.admission_id";

           return DB::SELECT($sql);
}

public function saveDummyBed(Request $request)
    {
		 $bed_name=strtoupper($request->bed_name);
		 $ward_id=strtoupper($request->ward_id);
		 $facility_id=strtoupper($request->facility_id);
		 $bed_type_id=strtoupper($request->bed_type_id);
		 $admission_id=strtoupper($request->admission_id);
		 $eraser=strtoupper($request->eraser);
		 if(empty($bed_name)){
			 return response()->json([
						'data' => 'BED NUMBER MUST BE FILLED',
						'status' => '0'
										]);

         }else if(!is_numeric($bed_type_id)){
			 return response()->json([
						'data' => 'BED TYPE MUST BE SELECTED FROM THE SUGESTION LIST',
						'status' => '0'
										]);
         }
else if(patientRegistration::duplicate('tbl_beds',array('bed_name','ward_id','facility_id','bed_type_id','eraser',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"),array($bed_name,$ward_id,$facility_id,$bed_type_id,$eraser))==true){

return response()->json([
						'data' => $bed_name.', BED No. ALREADY EXISTS',
						'status' => '0'
						]);

		  }
	$bed_name="DUMMY_".$bed_name;
	$bed =Tbl_bed::create(array('occupied'=>1,'facility_id'=>$facility_id,'bed_name'=>$bed_name,'bed_type_id'=>$bed_type_id,'ward_id'=>$ward_id,'eraser'=>$eraser));
			Tbl_status_ward::create($request->all());
			 if(isset($request->changeBed)){

				 $bed_id=$bed->id;
				 $old_bed_id=$request->old_bed_id;

			   Tbl_bed::where("id", $old_bed_id)->update(["occupied"=>0]);
			   Tbl_instruction::where("admission_id",$request->admission_id)->update(["bed_id"=>$bed_id]);
			        return response()->json([
							'data' => $bed_name.' WAS SUCCESSFULY RESERVED.',
							'status' => 1
										]);


			 }

            if($bed->save()){
               $bed_id=$bed->id;

			   Tbl_admission::where("id", $admission_id)->update(["admission_status_id"=>2]);
			   //mtuha tallying
				$patient = DB::select("select p.gender, p.dob, a.facility_id from tbl_patients p join tbl_admissions a on p.id=a.patient_id and a.id = '$admission_id'");
				$request['dob'] = $patient[0]->dob;
				$request['gender'] = $patient[0]->gender;
				$request['facility_id'] = $patient[0]->facility_id;
				ReportGenerators::countAdmission($request);
				//end mtuha
				
			   Tbl_instruction::where("admission_id",$admission_id)->update(["bed_id"=>$bed_id]);
			   

           return response()->json([
							'data' => $bed_name.' WAS SUCCEFULLY SAVED.',
							'status' => '1'
										]);			
			
	                     }else{
							 
							return response()->json([
								'data' => 'SOMETHING WENT WRONG IN YOUR SERVER',
								'status' => '0'
										]); 							 
						 }
}




public function saveBeds(Request $request)
    {
		 $bed_name=strtoupper($request->bed_name);
		 $ward_id=strtoupper($request->ward_id);
		 $facility_id=strtoupper($request->facility_id);
		 $bed_type_id=strtoupper($request->bed_type_id);
		 $eraser=strtoupper($request->eraser);
		 if(empty($bed_name)){
			 return response()->json([
						'data' => 'BED NUMBER MUST BE FILLED',
						'status' => '0'
										]);
			 
         }else if(!is_numeric($bed_type_id)){
			 return response()->json([
						'data' => 'BED TYPE MUST BE SELECTED FROM THE SUGESTION LIST',
						'status' => '0'
										]);			 
         }
else if(patientRegistration::duplicate('tbl_beds',array('bed_name','ward_id','facility_id','bed_type_id','eraser',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"),array($bed_name,$ward_id,$facility_id,$bed_type_id,$eraser))==true){
	
return response()->json([
						'data' => $bed_name.', BED No. ALREADY EXISTS',
						'status' => '0'
						]);
			  
		  }
	$save_ward_type =Tbl_bed::create(array('occupied'=>0,'facility_id'=>$facility_id,'bed_name'=>$bed_name,'bed_type_id'=>$bed_type_id,'ward_id'=>$ward_id,'eraser'=>$eraser));
			 
			 
            if($save_ward_type->save()){ 

           return response()->json([
							'data' => $bed_name.' BED No. SUCCEFULLY SAVED.',
							'status' => '1'
										]);			
			
	                     }else{
							 
							return response()->json([
								'data' => 'SOMETHING WENT WRONG IN YOUR SERVER',
								'status' => '0'
										]); 							 
						 }
}


    public function TheatrePrintOut(Request $request)
    {
        return DB::select("Select CONCAT(first_name,'',middle_name,'',last_name,'(',medical_record_number,')') as patient_name,t4.gender,t3.name as doctor_name,t3.mobile_number as doctor_number,t2.item_name,t1.* from tbl_pre_history_anethetics t1
 Join tbl_items t2 ON t1.item_id=t2.id 
Join users t3 on t3.id=t1.user_id join tbl_patients t4 on t4.id=t1.patient_id where t1.created_at between '".$request->start_date."' AND '".$request->end_date."' AND  (t1.descriptions ='Elective' or t1.descriptions ='Emergency') group by t1.id ");
}
public function TheatrePrintOutByCategory(Request $request)
    {
        return DB::select("Select count(t1.id) as quantity,t4.gender, t2.item_name,t1.descriptions as procedure_type from tbl_pre_history_anethetics t1 Join tbl_items t2 ON t1.item_id=t2.id 
join tbl_patients t4 on t4.id=t1.patient_id WHERE t1.created_at between '".$request->start_date."' AND '".$request->end_date."'  AND (t1.descriptions ='Elective' or t1.descriptions ='Emergency') group by t1.descriptions,t4.gender ");
}
    public function TheatrePrintOutDetails(Request $request)
    {
        if ($request->all()['specificPatient']==0){
            return DB::select("Select CONCAT(first_name,'',middle_name,'',last_name,'(',medical_record_number,')') as patient_name,t4.gender,t3.name as doctor_name,t3.mobile_number as doctor_number,t2.item_name,t1.* from tbl_pre_history_anethetics t1
 Join tbl_items t2 ON t1.item_id=t2.id 
Join users t3 on t3.id=t1.user_id join tbl_patients t4 on t4.id=t1.patient_id WHERE t1.created_at between '".$request->start_date."' AND '".$request->end_date."'    group by t1.id ");

        }
        else{
            return DB::select("Select CONCAT(first_name,'',middle_name,'',last_name,'(',medical_record_number,')') as patient_name,t4.gender,t3.name as doctor_name,t3.mobile_number as doctor_number,t2.item_name,t1.* from tbl_pre_history_anethetics t1
 Join tbl_items t2 ON t1.item_id=t2.id 
Join users t3 on t3.id=t1.user_id join tbl_patients t4 on t4.id=t1.patient_id WHERE t1.visit_date_id = '".$request->visit_date_id."'    group by t1.id ");

        }

         }

    public function TheatrePatientSearch(Request $request)
    {
       $mrn= $request->all()['mrn'];
        return DB::select("Select  first_name,middle_name,last_name,medical_record_number,t1.visit_date_id,t1.* from tbl_pre_history_anethetics t1
 join tbl_patients t4 on t4.id=t1.patient_id WHERE t4.medical_record_number like '%$mrn%' group by t1.visit_date_id LIMIT 5");
    }
    public function loadVisitDates(Request $request)
    {
        $patient_id= $request->all()['patient_id'];
        return DB::select("Select  t1.visit_date_id,date(created_at) as created_at from tbl_pre_history_anethetics t1
 WHERE patient_id ='".$patient_id."' group by t1.visit_date_id ");
    }

 public function getDischargedReport(Request $request){
        $nurse_id=$request->nurse_id;
$start=$request->start_date;
$end=$request->end_date;
        return DB::select("SELECT  t2.patient_id,
		t1.medical_record_number,
		t1.mobile_number,
		t1.gender,
        t2.admission_status_id,
        t2.updated_at as discharged_date,
		t4.admission_id,
		t4.ward_id,
		t6.nurse_id,
		t4.bed_id,
		t5.ward_name,
		t4.instructions,
		t4.prescriptions,
     CASE WHEN TIMESTAMPDIFF(YEAR,t1.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,t1.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,t1.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, t1.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, t1.dob, CURRENT_DATE), ' Days') END END
AS umri,
		t2.admission_date,	
		(SELECT residence_name FROM tbl_residences t1 INNER JOIN tbl_patients t2 ON t2.residence_id=t1.id  GROUP BY t1.residence_id LIMIT 1) AS residence_name,
		
		(SELECT council_name 
		FROM tbl_residences t1 
		INNER JOIN tbl_patients t2 ON t2.residence_id=t1.id 
		INNER JOIN tbl_councils t3 ON t3.id=t1.council_id 
		GROUP BY t1.council_id LIMIT 1) AS council_name,
		
        t9.name,
		CASE 
	    WHEN t2.account_id IS NOT NULL THEN (SELECT t12.main_category_id FROM tbl_bills_categories t12 WHERE t12.account_id=t2.account_id GROUP BY t2.account_id
	     LIMIT 1) END AS main_category_id,		
	    CASE 
	    WHEN t2.account_id IS NOT NULL THEN (SELECT t12.bill_id FROM tbl_bills_categories t12 WHERE t12.account_id=t2.account_id GROUP BY t2.account_id
	     LIMIT 1) END AS patient_category_id,	
		t9.mobile_number AS doctor_mob,		
        t4.updated_at,		
        t4.created_at,		
		CONCAT(t1.first_name,' ',t1.middle_name,' ',t1.last_name) AS fullname,

		t2.facility_id,
    CASE WHEN t2.created_at= t2.updated_at THEN
    timestampdiff(day, t2.created_at, CURRENT_TIMESTAMP) else  timestampdiff(day, t2.created_at, t2.updated_at)  END as totaldays ,
		t2.account_id
        FROM tbl_admissions t2
        INNER JOIN tbl_instructions t4 ON t4.admission_id=t2.id
        INNER JOIN tbl_wards t5 ON t5.id =t4.ward_id
        INNER JOIN tbl_nurse_wards t6 ON t6.ward_id=t4.ward_id
        INNER JOIN tbl_patients t1 ON t1.id = t2.patient_id
        INNER JOIN users t9 ON t2.user_id=t9.id  
       
        WHERE t2.admission_status_id=4 AND t6.nurse_id='".$nurse_id."' and t2.updated_at between '".$start."' AND '".$end."' group by t2.account_id order by timestampdiff(day, t2.created_at, t2.updated_at) desc ");


    }

public function setIndictorsWardStatus(Request $request)
{

    $checkDupl= Tbl_status_ward::where('admission_id',$request->admission_id)
    ->where('ward_id',$request->ward_id )
    ->where('admission_status_id',$request->admission_status_id )
    ->get();

    if(count($checkDupl)==0){
     Tbl_status_ward::create([
      'visit_date_id'=>$request->visit_date_id,
      'admission_status_id'=>$request->admission_status_id,
      'user_id'=>$request->user_id,
      'admission_id'=>$request->admission_id,
      'facility_id'=>$request->facility_id,
      'ward_id'=>$request->ward_id,
     ]);
      $res= response()->json([
       "msg"=>"Successful saved",
      "status"=>200,
       "data"=>"",
    ]);
 }
 else{

    //return duplicate sms
     $res= response()->json([
       "msg"=>"Duplication detected",
      "status"=>401,
       "data"=>"",
    ]);
 }
return $res;
}
}