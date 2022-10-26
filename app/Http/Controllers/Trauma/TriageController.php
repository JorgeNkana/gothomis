<?php

namespace App\Http\Controllers\Trauma;
use App\Patient\Tbl_accounts_number;
use App\ClinicalServices\Tbl_referral;
use App\classes\patientRegistration;
use App\Facility\Tbl_facility;
use App\Emergency\Tbl_emergence_visit;
use App\Emergency\Tbl_emergency_patient;
use App\Patient\Tbl_patient;
use App\ClinicalServices\Tbl_bills_category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Trauma\TraumaClient;
use App\Model\Trauma\TraumaVitals;
use App\Trackable;
use App\Patient\Tbl_invoice_line;
use App\Patient\Tbl_encounter_invoice;
use App\Clinics\Tbl_clinic_instruction;
use App\classes\SystemTracking;
use Validator;
use DB;
ini_set("max_execution_time",0);
class TriageController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {

        $query =  DB::table("tbl_trauma_clients")
						->leftjoin("tbl_triage_categories", "tbl_triage_categories.id","=","tbl_trauma_clients.triage_category");
						
		if($request->has('searchKey')){
			$quer = $query->where(function ($query) use($request){
								$query->orwhere('surname', 'like',  '%' . $request->searchKey .'%');
								$query->orwhere('first_name', 'like',  '%' . $request->searchKey .'%');
                                $query->orwhere('mrn', 'like',  '%' . $request->searchKey .'%');
							 })
						  ->take(15);
		}else{
			$query = $query->where(DB::Raw("TIMESTAMPDIFF(HOUR, tbl_trauma_clients.created_at, CURRENT_TIMESTAMP)") , "<=", 72);
		}			
						
		return $query->orderBy("tbl_triage_categories.id", "DESC")
					->select("tbl_trauma_clients.id as client_id","surname","first_name",DB::Raw("CASE WHEN dob IS NULL AND estimated_age IS NOT NULL THEN estimated_age ELSE CASE WHEN dob IS NULL AND estimated_age_group IS NOT NULL THEN estimated_age_group ELSE CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END END END AS age"),"gender","category","triage_category","mrn","patient_id", DB::Raw("(select temp from tbl_trauma_vitals where client_id=tbl_trauma_clients.id and temp IS NOT NULL order by id desc limit 1) as temp"),DB::Raw("(select bp from tbl_trauma_vitals where client_id=tbl_trauma_clients.id and bp IS NOT NULL order by id desc limit 1) as bp"),DB::Raw("(select hr from tbl_trauma_vitals where client_id=tbl_trauma_clients.id and hr IS NOT NULL order by id desc limit 1) as hr"),DB::Raw("(select rr from tbl_trauma_vitals where client_id=tbl_trauma_clients.id and rr IS NOT NULL order by id desc limit 1) as rr"),DB::Raw("(select spo2 from tbl_trauma_vitals where client_id=tbl_trauma_clients.id and spo2 IS NOT NULL order by id desc limit 1) as spo2"),DB::Raw("(select ps from tbl_trauma_vitals where client_id=tbl_trauma_clients.id and ps IS NOT NULL order by id desc limit 1) as ps"),DB::Raw("(select weight from tbl_trauma_vitals where client_id=tbl_trauma_clients.id and weight IS NOT NULL order by id desc limit 1) as weight"),DB::Raw("(select height from tbl_trauma_vitals where client_id=tbl_trauma_clients.id and height IS NOT NULL order by id desc limit 1) as height"))
					->get();

    }

    public function getTraumaPatientEdit(Request $request)

    {

        $query =  DB::table("tbl_trauma_clients")
						->leftjoin("tbl_arrival_modes", "tbl_arrival_modes.id","=","tbl_trauma_clients.arrival_mode");

		if($request->has('searchKey')){
			$quer = $query->where(function ($query) use($request){
								$query->where('tbl_trauma_clients.id', '=',  $request->searchKey);
							 })
						  ->take(1);
		}else{
			$query = $query->where(DB::Raw("TIMESTAMPDIFF(HOUR, tbl_trauma_clients.created_at, CURRENT_TIMESTAMP)") , "<=", 72);
		}

		return $query->orderBy("tbl_trauma_clients.id", "DESC")
					->select("tbl_trauma_clients.id as client_id","tbl_trauma_clients.*","tbl_arrival_modes.*" ,DB::raw("CONCAT(surname,',',first_name) as names"))
					->get();

    }

 

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create(Request $request)

    {


       $data      =  $request->all();


		if(!is_array($data))
			return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];
		
        $validator =  Validator::make($data[0], TraumaClient::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }
		
		foreach($data as $datum){
			if(TraumaClient::isDuplicate((array)$datum))
				return ["status"=>"error", "text"=>"Possible duplicate record"];
		}
		
		$error = false;
        foreach($data as $datum){
            $responses=[];

            $id=$datum['facility_id'];
            $sql=Tbl_facility::where('id',$id)->first();
            $facility_id = preg_replace("/[_-]/","",$sql->facility_code);
            $first_name=$datum['first_name'];
            $middle_name=$datum['first_name'];
            $last_name=$datum['surname'];
            $gender=$datum['gender'];
            $mobile_number=$request->input('mobile_number');
            $user_id=$datum['registered_by'];
 $patientData=Tbl_accounts_number::where("patient_id",$datum['patient_id'])->orderBy("id",'desc')->take(1)->get();
            if(!$result = TraumaClient::create($datum))
               $result['mrn']=$datum['mrn'];
               $result['visit_id']=null;//$patientData[0]->id;
				$error = true;
                $oldData=null;
                $patient_id=$datum['patient_id'];
                $trackable_id=$result->id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$result,null);
		}
		
        if($result) {

            return ["status"=>"success", "text"=>"Record successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }

    }

	public function vitals(Request $request)

    {
		$data      =  $request->all();
		
		if(!is_array($data))
			return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];
		
        $validator =  Validator::make($data[0], TraumaVitals::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }
		
		foreach($data as $datum){
			if(TraumaVitals::isDuplicate((array)$datum))
				return ["status"=>"error", "text"=>"Possible duplicate record"];
		}
		
		$error = false;
        foreach($data as $datum){
			if(!$result = TraumaVitals::create($datum))
				$error = true;
		}
		
        if(!$error) {
            return ["status"=>"success", "text"=>"Record successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }

    }

 
	public function setAcuity(Request $request)

    {
		$result = TraumaClient::where('id', '=', $request->client_id)
						  ->update(['triage_category'=>$request->triage_category]);
		
        if($result) {
            return ["status"=>"success", "text"=>"Record successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }

    }
	public function updateClient(Request $request)

    {

		$result = TraumaClient::where('id', '=', $request->client_id)
						  ->update([
                              'arrival_date'=>$request->input("arrival_date"),
                              'arrival_mode'=>$request->input("arrival_mode"),
                              'dead_on_arrival'=>$request->input("dead_on_arrival"),
                              'estimated_age'=>$request->input("estimated_age"),
                              'estimated_age_group'=>$request->input("estimated_age_group"),
                              'facility_id'=>$request->input("facility_id"),
                              'first_name'=>$request->input("first_name"),
                              'gender'=>$request->input("gender"),
                              'dob'=>$request->input("dob"),
                              'incident_location'=>$request->input("incident_location"),
                              'residence'=>$request->input("residence"),
                              
                              'surname'=>$request->input("surname"),
                              'registered_by'=>$request->input("registered_by"),
                              'triage_category'=>$request->input("triage_category"),
                              'next_kin_name'=>$request->input("next_kin_name"),
                              'next_kin_phone'=>$request->input("next_kin_phone"),
                              'next_kin_relation'=>$request->input("next_kin_relation"),
                               
                              'marital_status'=>$request->input("marital_status"),
                              'level_of_education'=>$request->input("level_of_education"),
                              'occupation_of_patient'=>$request->input("occupation_of_patient"),
                              'pregnant'=>$request->input("pregnant") ]);

        if($result) {
            return ["status"=>"success", "text"=>"Record successfully updated"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }

    }

 

    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        //

    }

 

    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        //

    }

 

    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        //

    }

 

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */
    public function getCurrentPatientAccountNumber($patient_id,$facility_id)
    { /**
    $checkif_new_account_numberIsrequired= DB::table('tbl_accounts_numbers')
    ->where('patient_id',$patient_id)
    ->where('facility_id',$facility_id)
    ->orderBy('id','ASC')
    ->take(2)->get();
     **/
        //if(count($checkif_new_account_numberIsrequired)==2){
        //$this->addTodayAccountNumber($facility_id,$patient_id);
        //  }



        $getCurrentPatientAccountNumber = Tbl_accounts_number::
        select('id as account_number_id','account_number')
            ->where('patient_id',$patient_id)
            ->where('facility_id',$facility_id)
            ->orderBy('id','DESC')
            ->first();
        return $getCurrentPatientAccountNumber;
    }

     public function enterEncounterTriage(Request $request) {
         
         if($request->input('patient_id')==''){
            return response()->json([
                'data' => 'Patient not recognized',
                'status' => '0'
            ]);
         }
  if($request->input('is_referral')==1 && $request->input('from_referral_id')==''){
            return response()->json([
                'data' => 'Please Choose Incoming Health Facility referral',
                'status' => '0'
            ]);
         }
         $emergency_type_id = $request->input('emergency_type_id');
        $facility_id=$request->input('facility_id');
        $patient_id=$request->input('patient_id');
        $user_id=$request->input('user_id');
        $dept_id=$request->input('dept_id');
        
        //may miss on reattendance
        $price_id=$request->input('price_id');
        $service_id=$request->input('service_id');
        $item_type_id=$request->input('item_type_id');
        
        $quantity=1;
        $status_id=1;
        $payment_filter=null;

        if($request->input('main_category_id')!=1){
            $status_id=1;
            $payment_filter=$request->input('payment_filter');
        }
        
        //Melchiory: this logic wa wrongly placed..... You should have returned duplicate before creating account number!
        if(!$request->has('free_reattendance') && patientRegistration::duplicate('tbl_invoice_lines',array('patient_id','item_type_id','quantity',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=0))"), array($patient_id,$item_type_id,$quantity))==true){

            return response()->json([
                'data' => 'DUPLICATE WAS DETECTED, DO NOT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST. THE REQUEST IS ALREADY SENT',
                'status' => '0'
            ]);
        }
        
        
        //Melchiory: added for mtuha purposes that needs dob and gender
        $patient = Tbl_patient::where('id',$patient_id)->get();
        //
        
        patientRegistration::patientAccountNumber($facility_id, $patient_id,$user_id, $patient[0]->gender, $patient[0]->dob);
        
        
        
        
        $account=$this->getCurrentPatientAccountNumber($patient_id,$facility_id);
        $account_number_id=$account->account_number_id;
        $bill_id=$request->input('bill_id');
        $main_category_id=$request->input('main_category_id');
        //Melchiory:Code added to capture referral details
    
        
        if($request->has('free_reattendance')){
            $last_visit = DB::select("select main_category_id,  patient_category_id  FROM tbl_accounts_numbers where patient_id = '$patient_id' and facility_id = '$facility_id' and main_category_id IS NOT NULL order by id desc limit 1");
             $bill_id=$last_visit[0]->patient_category_id;
             $main_category_id=$last_visit[0]->main_category_id;
        }
        //section modified to accomodate free reattendances
        $payment_category =Tbl_bills_category::create(['patient_id'=>$patient_id,'account_id'=>$account_number_id,'user_id'=>$user_id,'bill_id'=>$bill_id,'main_category_id'=>$main_category_id]);
        
        if(!$request->has('free_reattendance')){    
            $encounter =Tbl_encounter_invoice::create(array('account_number_id'=>$account_number_id,'facility_id'=>$facility_id,'user_id'=>$user_id));


            if($encounter->save()){
                //Important: For user fee clients, we set this encounter as a paid-for 
                //visit so that we track it x days that the facility allow the patient 
                //to revisit without paying consultation fee again.
                //Note that the free_reattendance flag in the request ensures this
                //value is never set twice as long as the registrar accepts the
                //dialog
                Tbl_accounts_number::where("id",$account_number_id)->update(["paid_attendance"=>true]);
                
                $invoice_line =Tbl_invoice_line::create(
                        array('invoice_id'=>$encounter->id,'payment_filter'=>$bill_id,
                        'item_type_id'=>$item_type_id,'facility_id'=>$facility_id,'quantity'=>$quantity,'user_id'=>$user_id,'item_price_id'=>$price_id,'status_id'=>$status_id,'discount'=>0,'discount_by'=>$user_id,'patient_id'=>$patient_id)
                    );
Tbl_emergency_Patient::create(array(
                        'visiting_id' => $account_number_id,
                        'emergency_type_id' => $emergency_type_id,
                        'registered_by' => $user_id
                    ));
                $oldData=null;
                $patient_id=$patient_id;
                $trackable_id=$invoice_line->id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$invoice_line,$oldData);

                if($dept_id>7) {
                    $clinic_save = Tbl_clinic_instruction::create(array('received' => 0, 'dept_id' => $dept_id, 'doctor_requesting_id' => $user_id,
                    'consultation_id' => $service_id, 'sender_clinic_id' => 1, 'visit_id' => $account_number_id, 'on_off' => 0));
                }
            }
        }//END MODIFY
        
        
        $facility_code=DB::SELECT("SELECT facility_code FROM tbl_facilities t1 WHERE t1.id='".$facility_id."'");
        $facility_code=$facility_code[0]->facility_code;
        //check if reattendance to count..
        $last_created=Tbl_patient::where('id',$patient_id)->get();
        $time_created= $last_created[0]->created_at;
        $gender= $last_created[0]->gender;
        $dob= $last_created[0]->dob;
        $time_created = new \DateTime($time_created);
        $interval = (new \DateTime())->diff($time_created);
        $day = $interval->d + $interval->y*12*30;
        if ($day >0){
            patientRegistration::countReattendance($gender, $dob,$facility_code);
        }else{
            patientRegistration::countNewAttendance($gender, $dob,$facility_code);
        }

       if($dept_id==14){
               $account_data = Tbl_accounts_number::
               where('id', $account_number_id)
                   ->where('patient_id', $patient_id)
                   ->update([
                       'status'=>1
                   ]);
       }
                
        //Melchiory: only after applying GUID to all users should this code run 
        /*
        
        $department_lists=array(8,15,17,18,19,20,21,22);//special EMR Programs
            
        if(in_array($dept_id,$department_lists)){
            $sql="SELECT t1.* FROM tbl_patients t1 WHERE t1.id='".$patient_id."'";  
            $dataToEMR=DB::SELECT($sql);
                
        return patientRegistration::EMRintegrationAPI($dataToEMR,$account_number_id,$patient_id,$bill_id,$user_id,$dept_id);
                                
         }
         
         */

         if($request->is_referral != 0){
          $from_facility_id=$request->from_referral_id;
            $account_id=$account->account_number_id;
            $user_id=$user_id;
        //save referral
            $patientData=Tbl_patient::where("id",$patient_id)->first();
 $checkdup=Tbl_referral::where("visit_id",$account_id)->where('to_facility_id',$from_facility_id)->get();
 if(count($checkdup)==0){

   $aged=DB::select("select  CASE WHEN TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, t6.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, t6.dob, CURRENT_DATE), ' Days') END END
AS age from tbl_patients t6 where id=$patient_id limit 1");
$payl=Tbl_referral::create([
 'visit_id'=>$account_id,     
'referral_code'=>"INCOMING",      
'referral_type'=>1,      
'status'=>1, 
'patient_id'=>$patient_id,        
 'sender_id'=>$user_id,                                
 'from_facility_id'=>$request->from_referral_id,     
 'to_facility_id'=>$facility_id,
"referral_date"=>Date("Y-m-d"), 
  "name"=>$patientData->first_name. "".$patientData->middle_name." ".$patientData->last_name,
  "gender"=>$patientData->gender,
  "reg"=>$patientData->medical_record_number,
  "age"=>$aged[0]->age,
]);

}
}

        return response()->json([
                'data' => 'Data served',
                'account_number' =>'Account No: ',
                'status' => '1'
            ]);
    }




 

    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        //

    }

    public function triageRegisteredReport(Request $request)
    {
if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start=date('Y-m-01 07:00:00');
            $end=date("Y-m-d H:i:s");
        }else{
            $start=$request->start_date;
            $end=$request->end_date;
        }

     $all[]= DB::select("select t2.category as triage_category,t1.gender,t1.first_name,t1.surname  FROM tbl_trauma_clients t1 join  tbl_triage_categories t2 on t1.triage_category=t2.id where t1.created_at between '".$start."' and '".$end."' group by t1.id order by t1.id desc");
    /* $all[]= DB::select("select 
        t3.facility_name,t2.category as triage_category,t1.gender,
t3.* ,
t4.* ,
t5.* ,
t6.* ,
t7.* ,
t8.* ,
t9.* ,
t10.* ,
t11.* ,
t12.* ,
t13.* ,
t14.* ,
t15.* ,
t16.* ,
t17.* ,
t18.* ,
t19.* ,
t20.*
FROM tbl_trauma_clients t1 left join  tbl_triage_categories t2 on t1.triage_category=t2.id 
left join  tbl_facilities t3 on t3.id=t1.facility_id
left join  tbl_trauma_assessments t4 on t4.client_id=t1.id
left join  tbl_trauma_chief_complaints t5 on t5.client_id=t1.id
left join  tbl_trauma_dispositions t6 on t6.client_id=t1.id
left join  tbl_trauma_history_allergies t7 on t7.client_id=t1.id
left join  tbl_trauma_history_medical t8 on t8.client_id=t1.id
left join  tbl_trauma_history_medications t9 on t9.client_id=t1.id
left join  tbl_trauma_history_surgeries t10 on t10.client_id=t1.id
left join  tbl_trauma_hpi t11 on t11.client_id=t1.id
left join  tbl_trauma_imaging_results t12 on t12.client_id=t1.id
left join  tbl_trauma_injury_details t13 on t13.client_id=t1.id
left join  tbl_trauma_interventions t14 on t14.client_id=t1.id
left join  tbl_trauma_lab_results t15 on t15.client_id=t1.id
left join  tbl_trauma_physical_examms t16 on t16.client_id=t1.id
left join  tbl_trauma_primary_survey t17 on t17.client_id=t1.id
left join  tbl_trauma_reassessment_vitals t18 on t18.client_id=t1.id
left join  tbl_trauma_vitals t19 on t19.client_id=t1.id
left join  tbl_trauma_airway_primary_surveys t20 on t20.client_id=t1.id
left join  trauma_primary_breathing_surveys t21 on t21.client_id=t1.id
left join  trauma_primary_circulation_surveys t22 on t22.client_id=t1.id
left join  trauma_primary_disability_surveys t23 on t23.client_id=t1.id
left join  trauma_primary_exposure_surveys t24 on t24.client_id=t1.id
left join  trauma_primary_fast_surveys t25 on t25.client_id=t1.id
left join  trauma_past_medical_histories t26 on t26.client_id=t1.id
left join  trauma_past_medical_allergy_histories t27 on t27.client_id=t1.id
left join  trauma_hpis t28 on t28.client_id=t1.id
left join  trauma_hpi_injury_mechanisms t29 on t29.client_id=t1.id
left join  trauma_hysical_examinations t30 on t30.client_id=t1.id
left join  trauma_lab_results t31 on t31.client_id=t1.id
left join  trauma_imaging_results t32 on t32.client_id=t1.id
left join  trauma_procedures t33 on t33.client_id=t1.id
left join  trauma_fluid_medications t34 on t34.client_id=t1.id
left join  trauma_assesment_plans t35 on t35.client_id=t1.id
left join  trauma_client_diagnoses t36 on t36.client_id=t1.id
left join  trauma_re_assesment_plans t37 on t37.client_id=t1.id
left join  trauma_client_dispositions t38 on t38.client_id=t1.id
where t1.created_at between '".$start."' and '".$end."'
  order by t1.id desc limit 5");
*/
  return $all;
    }

}