<?php

namespace App\Http\Controllers\ctc;

use App\classes\patientRegistration;
use App\clinic\ctc\Tbl_clinic_attendance;
use App\clinic\ctc\Tbl_clinic_capacity;
use App\clinic\ctc\Tbl_clinic_schedule;
use App\ClinicalServices\Tbl_bills_category;
use App\Clinics\Tbl_clinic_instruction;
use App\ctc\Tbl_ctc_family_information;
use App\ctc\Tbl_ctc_patient_addresse;
use App\ctc\Tbl_ctc_patient_support;
use App\ctc\Tbl_ctc_patient_visit;
use App\ctc\Tbl_arv_reason;
use App\ctc\Tbl_ctc_unique_id_patient;
use App\ctc\Tbl_visit_type;
use App\ctc\Tbl_ctc_sign;
use App\ctc\Tbl_arv_combination;
use App\ctc\Tbl_oi_treatment;
use App\ctc\Tbl_arv_statuse;
use App\ctc\Tbl_arv_adherense;
use App\ctc\Tbl_tb_treatment;
use App\ctc\Tbl_status_functional;
use App\ctc\Tbl_family_plan;
use App\ctc\Tbl_status_nutritional;
use App\ctc\Tbl_nutritional_supplement;
use App\ctc\Tbl_status_follow_up;
use App\ctc\Tbl_ctc_refferal;
use App\ctc\Tbl_tb_screening;
use App\Emergency\Tbl_vital_sign;
use App\Patient\Tbl_accounts_number;
use App\patient\Tbl_encounter_invoice;
use App\patient\Tbl_invoice_line;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ctcController extends Controller
{
	public function getCodesPerCTC(Request $request)
    {
		 $sql="SELECT t1.*,
		           
				   (SELECT vital_sign_value FROM tbl_vital_signs t2 WHERE t1.weight_sign_value_id=t2.id  GROUP BY t1.weight_sign_value_id) AS weight, 
				   t2.date_attended, 
				   
				   (SELECT vital_sign_value FROM tbl_vital_signs t2 WHERE t1.length_sign_value_id=t2.id  GROUP BY t1.length_sign_value_id) AS height,
				   (SELECT name FROM users t2 WHERE t1.user_id=t2.id  GROUP BY t1.user_id) AS clinician_name

        		 FROM tbl_ctc_patient_visits t1
               INNER JOIN tbl_accounts_numbers t2 ON t1.visit_date_id=t2.id
			   
			   WHERE t1.patient_id='".$request->patient_id."'";
			   
		$responses=[];
		$responses[]=Tbl_visit_type::all();
		$responses[]=Tbl_ctc_sign::all();
		$responses[]=Tbl_status_functional::all();
		$responses[]=Tbl_family_plan::all();
		$responses[]=DB::SELECT($sql);
		$responses[]=Tbl_status_nutritional::all();
		$responses[]=Tbl_nutritional_supplement::all();
		$responses[]=Tbl_ctc_refferal::all();
		$responses[]=Tbl_status_follow_up::all();
		
		 
        return $responses;
    }
	public function saveVisitType(Request $request)
    {
		$responses=[];
		$responses[]=Tbl_visit_type::all();
		$responses[]=Tbl_ctc_sign::all();
		$responses[]=Tbl_status_functional::all();
		$responses[]=Tbl_status_functional::all();
		 
        return $responses;
    }
	
	public function incomingCtcPatients(Request $request)
    {
        $facility_id=$request->input('facility_id');
        $dept_id=$request->input('dept_id');
        $sql = "SELECT * FROM vw_special_clinics_clients WHERE facility_id = '".$facility_id."' AND dept_id='".$dept_id."' AND received = 0 LIMIT 20";
        $pts = DB::select(DB::raw($sql));
        return $pts;
    }
    public function psychAll(Request $request)
    {
        $facility_id=$request->input('facility_id');
        $dept_id=$request->input('dept_id');
        $searchKey=$request->input('searchKey');
        $sql = "SELECT * FROM vw_special_clinics_clients WHERE medical_record_number LIKE '%".$searchKey."%' AND facility_id = '".$facility_id."' AND dept_id='".$dept_id."' ";
        $pts = DB::select(DB::raw($sql));
        return $pts;
    }
	
    public function ctcPendingCustomers($facility_id){

        return DB::table('vw_ctc_customers')
            ->where('received',0)
            ->where('facility_id',$facility_id)
            ->get();
    }

    //    REGISTER VITAL SIGNS_V2
    public function CtcVitalSignRegister(Request $request)
    {
//return $request->all();

        $date = date('Y-m-d h:i:s');
        $time=date("H:i:s");
        if(count($request->all())>0){
            foreach ($request->all() as $data){
                $os = Tbl_vital_sign::create([
                    'visiting_id'=>$data['patient_id'],
                    'vital_sign_id'=>$data['vital_sign_id'],
                    'vital_sign_value'=>$data['vital_sign_value'],
                    'registered_by'=>$data['registered_by'],
                    'date_taken'=>$date,
                    'time_taken'=>$time
                ]);

                $id=$os->id;

                if($id==1){
                    Tbl_ctc_patient_visit::where('visit_date_id',$data['patient_id'])->update(['weight_sign_value_id'=>$id]);
                }
                if($id==2){
                    Tbl_ctc_patient_visit::where('visit_date_id',$data['patient_id'])->update(['length_sign_value_id'=>$id]);
                }


            }

        }


        return  response()->json([
            'msg'=> "Successfully Registered",
            'notification'=> "Success",
            'status'=>1
        ]) ;

    }


    public function ctcApprovedCustomers($facility_id){

        return DB::table('vw_ctc_customers')
            ->where('received',1)
            ->where('days_ago','<=',1)
            ->where('facility_id',$facility_id)
            ->get();
    }

    public function getCtcSheduleTimeTable($facility_id){

        return Tbl_clinic_schedule::where('facility_id',$facility_id)
                             ->where('clinic_id',8)
                             ->where('on_off',1)
                            ->get();
    }

    public function getCurrentPatientAccountNumber($patient_id,$facility_id)
    {
        $getCurrentPatientAccountNumber = Tbl_accounts_number::
        select('id as account_number_id','account_number')
            ->where('patient_id',$patient_id)
            ->where('facility_id',$facility_id)
            ->orderBy('id','DESC')
            ->first();
        return $getCurrentPatientAccountNumber;
    }


    public function enterCtcEncounter(Request $request)
    {
        // some validation may be required..
        $facility_id=$request->input('facility_id');
        $patient_id=$request->input('patient_id');
        $price_id=$request->input('price_id');
        $service_id=$request->input('service_id');
        $item_type_id=$request->input('item_type_id');
        $user_id=$request->input('user_id');


        $quantity=1;
        $status_id=1;
        $payment_filter=null;

        if($request->input('main_category_id')!=1)
        {
            $status_id=1;
            $payment_filter=$request->input('payment_filter');
        }

        if(patientRegistration::duplicate('tbl_patients',array('id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >= 60))"), array($patient_id))==true) {

            patientRegistration::patientAccountNumber($facility_id, $patient_id);
        }
        $account=$this->getCurrentPatientAccountNumber($patient_id,$facility_id);
        $account_number_id=$account->account_number_id;
        $bill_id=$request->input('bill_id');
        $main_category_id=$request->input('main_category_id');
        //return $getLastVisit[0]->created_at;




        if (!is_numeric($service_id)) {

            return response()->json([
                'data' => 'PLEASE SELECT SERVICE FROM THE SUGESTION LIST',
                'status' => '0'
            ]);
        }

        else{

            if(patientRegistration::duplicate('tbl_invoice_lines',array('patient_id','item_type_id','quantity',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($patient_id,$item_type_id,$quantity,''))==true){

                return response()->json([
                    'data' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                    'status' => '0'
                ]);
            }

            else{
                $payment_category =Tbl_bills_category::create(['patient_id'=>$patient_id,'account_id'=>$account_number_id,'user_id'=>$user_id,'bill_id'=>$bill_id,'main_category_id'=>$main_category_id]);



                $encounter =Tbl_encounter_invoice::create(array('account_number_id'=>$account_number_id,'facility_id'=>$facility_id,'user_id'=>$user_id));


                if($encounter->save()){
                    $invoice_line =Tbl_invoice_line::create(array('invoice_id'=>$encounter->id,'payment_filter'=>$payment_filter,
                        'item_type_id'=>$item_type_id,'facility_id'=>$facility_id,'quantity'=>number_format($quantity, 2, '.', ''),'user_id'=>$user_id,'item_price_id'=>$price_id,'status_id'=>$status_id,'discount_by'=>$user_id,'patient_id'=>$patient_id));

                    $clinic_save =Tbl_clinic_instruction::create(array('received'=>0,'dept_id'=>8,'doctor_requesting_id'=>$user_id,
                        'consultation_id'=>$service_id,'sender_clinic_id'=>1,'visit_id'=>$account_number_id,'on_off'=>0));

                      if($invoice_line->save()){
                        return response()->json([
                            'data' => 'SUCCESSFULLY SAVED AND SUBMITED TO ENCOUNTER',
                            'account_number' =>'Account No: ',
                            'status' => '1'
                        ]);

                    }
                }
            }
        }




    }





    public function ctc_registration(Request $request)
    {   // return $request->input('facility_id');

       // return $request->all();
        foreach($request->all() as $key=>$value)
            $request[$key] = strtoupper($value);
        $genders=array('MALE','FEMALE');

        $facility_id=$request->input('facility_id');
        $maritals=$request->input('marital_status');
        $gender=$request->input('gender');
        $mobile_number=$request->input('mobile_number');
        $residence_id=$request->input('residence_id');
        $dob=$request->input('dob');
        $mobile_pattern='#^[0][6-7][1-9][2-9][0-9]{6}$#';
        // return patientRegistration::calculatePatientAge($request);

        $pattern='#^(19[0-9][0-9])|(20[0-9][0-9])-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2][0-9])|(3[0-1]))$#';


        if(!in_array($gender,$genders)){

            return response()->json([
                'data' => 'Please Select Gender!',
                'status' => '0'
            ]);
        }
        else if (0 === preg_match($mobile_pattern, $mobile_number) AND !empty($mobile_number)) {

            return response()->json([
                'data' => 'INVALID MOBILE NUMBER',
                'status' => '0'
            ]);
        }

        else if (!is_numeric($residence_id)) {

            return response()->json([
                'data' => 'PLEASE ENTER PATIENT RESIDENCE',
                'status' => '0'
            ]);
        }
        else if (0 === preg_match($pattern, $dob)) {

            return response()->json([
                'data' => 'Invalid Date of Birth',
                'status' => '0'
            ]);
        }
        else {

            return patientRegistration::patient_registration($request);

        }


    }



    public function saveFamilyInfo(request $request){
        $relation_id=$request->relation_id;
        $relative_name=$request->relative_name;
     if (patientRegistration::duplicate('tbl_ctc_family_informations', array('relative_name','relation_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($relative_name, $relation_id)) == true) {
         return response()->json([
             'data' =>$relative_name.' already registered.',
             'status' => '0'
         ]);
     }else {
         Tbl_ctc_family_information::create($request->all());

         return response()->json([
             'data' =>$relative_name.' was succesfully registered.',
             'status' => 1
         ]);
     }

    }

    public function getClinicAttendanceForPatient($refferal_id){

        return Tbl_clinic_attendance::where('refferal_id',$refferal_id)
                                    ->get();
    }

  public function deleteDayId($deleteDayId){

        Tbl_clinic_schedule::where('id',$deleteDayId)
                             ->Update(['on_off'=>0]);
      return response()->json([
          'data' =>' SUCCESSFULLY DELETED',
          'status' => 1
      ]);
    } 
	
	public function saveCtcCodes(Request $request){
             if($request->codeQuery==1){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_visit_types', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_visit_type::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   }  

			  else if($request->codeQuery==2){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_ctc_signs', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_ctc_sign::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   }

	   else if($request->codeQuery==3){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_status_functionals', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_status_functional::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   }

			   else if($request->codeQuery==4){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_family_plans', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_family_plan::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   }
			  
else if($request->codeQuery==5){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_status_nutritionals', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_status_nutritional::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   }
			   
			   
			   else if($request->codeQuery==6){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_nutritional_supplements', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_nutritional_supplement::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   }
			   

   else if($request->codeQuery==7){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_ctc_refferals', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_ctc_refferal::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
   }
	 else if($request->codeQuery==8){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_status_follow_ups', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_status_follow_up::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   }
			   
			   else if($request->codeQuery==9){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_tb_screenings', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_tb_screening::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   } 
	else if($request->codeQuery==10){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_arv_reasons', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_arv_reason::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   }	
			   
	  else if($request->codeQuery==11){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_arv_combinations', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_arv_combination::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   }

			   else if($request->codeQuery==12){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_tb_treatments', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_tb_treatment::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   } 
			   
	  else if($request->codeQuery==13){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_arv_statuses', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_arv_statuse::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   }

			   else if($request->codeQuery==14){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_arv_adherenses', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_arv_adherense::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   } 
			   
			   else if($request->codeQuery==15){
				 $code=$request->code;
				 
		 if (patientRegistration::duplicate('tbl_oi_treatments', array('code',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($code)) == true) {
         return response()->json([
             'data' =>'Code: '.$request->code.' already registered.',
             'status' => 0
         ]);
     }else {
         Tbl_oi_treatment::create($request->all());

         return response()->json([
             'data' =>'Code: '.$request->code.' was succesfully registered.',
             'status' => 1
         ]);
     }
				 
				 
			   }
  
			  
		  else{
				   
				  return response()->json([
             'data' =>'Code: '.$request->code.', System failed to allocate',
             'status' => 0
         ]);  
			   }
	
	   
       
    }

    public function addClinCapacity(request $request)
    {

        $facility_id = $request->facility_id;
        $user_id = $request->user_id;
        $clinic_name_id = $request->clinic_name_id;
        $capacity = $request->capacity;
        if (patientRegistration::duplicate('tbl_clinic_capacities', array('clinic_name_id','facility_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array(8, $facility_id)) == true) {
            return response()->json([
                'data' =>'Clinic capacity Already registered for CTC clinic',
                'status' => '0'
            ]);
        } else {
            Tbl_clinic_capacity::create($request->all());
            return response()->json([
                'data' => 'CTC CLINIC CAPACITY WAS ADDED',
                'status' => 1
            ]);
        }
    }


    public function saveCtCRegistration(request $request)
    {

        $facility_id = $request->facility_id;
        $patient_id = $request->patient_id;
        $user_id = $request->user_id;
        $unique_id = $request->unique_ctc_number;
        $address= $request->residence_id;
        $last_name= $request->last_name;
        $contact_house_hold_head= $request->contact_house_hold_head;
        if(!is_numeric($address)){
            return response()->json([
                'data' =>' Street/Village entered was not from suggestion list',
                'status' => 0
            ]);
        }
        else if(!is_numeric($contact_house_hold_head)){
            return response()->json([
                'data' =>' INVALID PHONE NUMBER FOR THE HOUSE HOLD HEAD',
                'status' => 0
            ]);
        }
       else if (patientRegistration::duplicate('tbl_ctc_unique_id_patients', array('patient_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($patient_id)) == true) {
            return response()->json([
                'data' =>$last_name.' already given CTC ID number',
                'status' => '0'
            ]);
        }
        else if (patientRegistration::duplicate('tbl_ctc_unique_id_patients', array('unique_ctc_number',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($unique_id)) == true) {
            return response()->json([
                'data' =>$unique_id.' already given to client',
                'status' => '0'
            ]);
        }

        else {
            $response=[];
            $response[]=Tbl_ctc_unique_id_patient::create($request->all());
            $response[]=Tbl_ctc_patient_addresse::create($request->all());

            return response()->json([
                'data' => $last_name.' WAS  SUCCESSFULLY ADDED',
                'status' => 1
            ]);
        }
    }

	
	 public function savePatientClinic(Request $request){
	    $visit_id = $request->visit_date_id;
        $user_id = $request->user_id;
        $column = $request->column;
        $value = $request->$column;
		if (patientRegistration::duplicate('tbl_ctc_patient_visits', array('visit_date_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($visit_id)) == true) {
            Tbl_ctc_patient_visit::where('visit_date_id',$visit_id)->update([$column=>$value]);
			
			return response()->json([
                'data' =>'Record updated succesfully',
                'status' => 1
            ]);
        }  else {
            Tbl_ctc_patient_visit::create($request->all());
			 return response()->json([
                'data' =>'New Record added succesfully',
                'status' => 1
            ]);
		}
        
		 
	 }
	 
	 public function getCTCForm(Request $request){
		 
		 $sql="SELECT t1.*,
		           
				   (SELECT vital_sign_value FROM tbl_vital_signs t2 WHERE t1.weight_sign_value_id=t2.id  GROUP BY t1.weight_sign_value_id) AS weight, 
				   (SELECT vital_sign_value FROM tbl_vital_signs t2 WHERE t1.length_sign_value_id=t2.id  GROUP BY t1.length_sign_value_id) AS height,
				   (SELECT name FROM users t2 WHERE t1.user_id=t2.id  GROUP BY t1.user_id) AS clinician_name

        		 FROM tbl_ctc_patient_visits t1
               INNER JOIN tbl_accounts_numbers t2 ON t1.visit_date_id=t2.id
			   
			   WHERE t1.patient_id='".$request->patient_id."'";
			   return DB::SELECT($sql);
	 }

    public function saveCTCPatientSupport(request $request)
    {

        $facility_id = $request->facility_id;
        $patient_id = $request->patient_id;
        $visit_id = $request->visit_date_id;
        $user_id = $request->user_id;
        $last_name= $request->last_name;
        $telephone_number= $request->telephone_number;
        $name_treatment_supporter= $request->name_treatment_supporter;

         if(!is_numeric($telephone_number)){
            return response()->json([
                'data' =>'INVALID PHONE NUMBER FOR THE PATIENT SUPPORTER',
                'status' => 0
            ]);
        }
        else if(empty($name_treatment_supporter)){
            return response()->json([
                'data' =>' ENTER NAME FOR THE SUPPORTER',
                'status' => 0
            ]);
        }

        else if (patientRegistration::duplicate('tbl_ctc_patient_visits', array('visit_date_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($visit_id)) == true) {
            return response()->json([
                'data' =>$last_name.' visit type  already registered today',
                'status' =>0
            ]);
        }

        else {
            $response=[];
            $response[]=Tbl_ctc_patient_support::create($request->all());
            $response[]=Tbl_ctc_patient_visit::create($request->all());
            $response[]=Tbl_clinic_instruction::where('visit_id',$visit_id)->update(['received'=>1]);

             return response()->json([
                'data' => $last_name.' WAS  SUCCESSFULLY ADDED',
                'status' => 1
            ]);
        }
    }

     public function ctcSheduleTimeTable(request $request){
         $date=$request->selectedDate;
         $facility_id=$request->facility_id;
         $user_id=$request->user_id;
//Convert the date string into a unix timestamp.
         $unixTimestamp = strtotime($date);
//Get the day of the week using PHP's date function.
         $dayOfWeek = date("l", $unixTimestamp);
   if(patientRegistration::duplicate('tbl_clinic_schedules',array('on_off','clinic_id','facility_id','week_day',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array(1,8,$facility_id,$dayOfWeek))==true){
   return response()->json([
                 'data' => $dayOfWeek.' Already registered for CTC clinic',
                 'status' => '0'
                 ]);
         }else{
             Tbl_clinic_schedule::create(array("on_off"=>1,"clinic_id"=>8,"week_day"=>$dayOfWeek,"clinic_date"=>$date,"facility_id"=>$facility_id,"user_id"=>$user_id));
             return response()->json([
                 'data' => $dayOfWeek.' SUCCESSFULLY REGISTERED',
                 'status' => 1
             ]);
         }
          }


     public function giveAppointmentCtc(request $request)
     {
         $date = $request->selectedDate;
         $facility_id = $request->facility_id;
         $user_id = $request->user_id;
         $visit_id= $request->visit_id;
         $refferal_id= $request->refferal_id;
//Convert the date string into a unix timestamp.
         $unixTimestamp = strtotime($date);
//Get the day of the week using PHP's date function.
         $dayOfWeek = date("l", $unixTimestamp);

         $getClinincsCapacity = Tbl_clinic_capacity::where('clinic_name_id', 8)
             ->where('facility_id', $facility_id)
             ->get();

         $capacity = $getClinincsCapacity[0]->capacity;

         $getCountAppointments =DB::table('vw_attendance_ctc_clinics')
             ->where('facility_id',$facility_id)
             ->where('next_visit',$date)
             ->where('clinic_id',8)->count();
         if ($getCountAppointments == $capacity) {
             return response()->json([
                 'data' => 'This clinic has reached its maximum number of ' . $capacity . ' patients on ' . $date,
                 'status' =>0
             ]);
         } else {
        if (patientRegistration::duplicate('tbl_clinic_attendances', array('refferal_id','visit_id',"((next_visit IS NOT NULL))"), array($refferal_id,$visit_id)) == true) {
                 return response()->json([
                     'data' =>'Next visit date already Set for this Patient',
                     'status' => '0'
                 ]);
             } else {
                 Tbl_clinic_attendance::where('refferal_id',$refferal_id)
                                     ->where('visit_id',$visit_id)
                                     ->update(['next_visit'=>$date]);
                 return response()->json([
                     'data' =>'Next visit on '.$dayOfWeek.','.$date,
                     'status' => 1
                 ]);
             }
         }
     }


}