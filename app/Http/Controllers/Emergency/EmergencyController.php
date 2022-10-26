<?php

namespace App\Http\Controllers\Emergency;


use App\ClinicalServices\Tbl_bills_category;
use App\Emergency\Tbl_comma_scale;
use App\Emergency\Tbl_comma_scale_history;
use App\Emergency\Tbl_comma_scales_history;
use App\Emergency\Tbl_emergence_visit;
use App\Emergency\Tbl_emergency_patient;
use App\Emergency\Tbl_emergency_survey_history;
use App\Emergency\Tbl_emergency_type;
use App\Emergency\Tbl_patient_emergence;
use App\Emergency\Tbl_survey_history;
use App\Emergency\Tbl_vital_sign;
use App\Emergency\Tbl_vitalSign;
use App\Exemption\Tbl_exemption;
use App\Exemption\Tbl_attachment;
use App\Patient\Tbl_exemption_number;
use App\patient\Tbl_next_of_kin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\classes\patientRegistration;
use App\Patient\Tbl_invoice_line;
use App\Patient\Tbl_encounter_invoice;
use App\Patient\Tbl_accounts_number;
use App\Patient\Tbl_patient;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\ClinicalServices\Tbl_admission;
use App\ClinicalServices\Tbl_birth_history;
use App\ClinicalServices\Tbl_child_birth_history;
use App\ClinicalServices\Tbl_complaint;
use App\ClinicalServices\Tbl_continuation_note;
use App\ClinicalServices\Tbl_diagnosis;
use App\ClinicalServices\Tbl_diagnosis_detail;
use App\ClinicalServices\Tbl_family_history;
use App\ClinicalServices\Tbl_family_social_history;
use App\ClinicalServices\Tbl_history_examination;
use App\ClinicalServices\Tbl_icu_entry;
use App\ClinicalServices\Tbl_instruction;
use App\ClinicalServices\Tbl_obs_gyn;
use App\ClinicalServices\Tbl_obs_gyn_record;
use App\ClinicalServices\Tbl_order;
use App\ClinicalServices\Tbl_past_medical_history;
use App\ClinicalServices\Tbl_past_medical_record;
use App\ClinicalServices\Tbl_Patient_procedure;
use App\ClinicalServices\Tbl_physical_examination;
use App\ClinicalServices\Tbl_physical_examination_record;
use App\ClinicalServices\Tbl_prescription;
use App\ClinicalServices\Tbl_referral;
use App\ClinicalServices\Tbl_request;
use App\ClinicalServices\Tbl_review_of_system;
use App\ClinicalServices\Tbl_review_system;
use App\ClinicalServices\Tbl_unavailable_test;
use App\Clinics\Tbl_clinic_instruction;
use App\Department\Tbl_department;
use App\Mortuary\Tbl_corpse_admission;
use App\Patient\Tbl_corpse;
use App\classes\SystemTracking;
use App\Trackable;

class EmergencyController extends Controller
{
    public function registeredPatients(Request $request)
    {
        $name = $request['name'];
        $Patients = DB::table('tbl_Patients')
            ->where('first_name', 'like', '%' . $name . '%')
            ->orwhere('middle_name', 'like', '%' . $name . '%')
            ->orwhere('mobile_number', 'like', '%' . $name . '%')
            ->orwhere('medical_record_number', 'like', '%' . $name . '%')
            ->limit(10)
            ->get();
        return $Patients;
    }

    public function getReportedCasualty(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $emergency_records="
       SELECT 
        t3.first_name,
        t3.middle_name,
        t3.last_name,
        t3.dob,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END
        AS age,
        t2.date_attended,
        t3.gender,
        t3.medical_record_number,
        t3.mobile_number,
        t4.emergency_type,
        t4.emergency_name,
        t1.created_at,
        t1.updated_at
        FROM tbl_emergency_patients t1, tbl_accounts_numbers t2, tbl_patients t3,tbl_emergency_types t4
        WHERE 
            t2.id = t1.visiting_id
            AND t3.id = t2.patient_id
            AND t4.id = t1.emergency_type_id
            AND date(t1.created_at) BETWEEN '".$start."'  AND '".$end."' 
         ";
        return DB::select($emergency_records);
        // $sql = "SELECT * FROM `vw_emergency_records` WHERE created_at BETWEEN '".$start."'  AND '".$end."' ";
        // $emergencyData = DB::select(DB::raw($sql));
        // return $emergencyData;
    }
    public function reportsCasualty(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $emergency = 'MOTOR ACCIDENT';

        $users = DB::select("select count(emergency_type) as total,emergency_type from vw_emergency_records where date(created_at) BETWEEN '".$start."'  AND '".$end."' group by emergency_type ");
        return $users;
    }
    public function emergency_type(Request $request)
    {
        foreach ($request->all() as $key => $value)
            $request[$key] = strtoupper($value);
        $emergency_type = $request['emergency_type'];
        $emergency_name = $request['emergency_name'];

        $data = Tbl_emergency_type::where('emergency_name', $emergency_name)
            ->get();
        if (count($data) > 0) {
            return response()->json([
                'message' => $emergency_name . " ALREADY EXISTS",
                'status' => 0
            ]);
        } else {
            Tbl_emergency_type::create([
                'emergency_type' => $emergency_type,
                'emergency_name' => $emergency_name
            ]);
            return response()->json([
                'message' => $emergency_name . " SUCCESSFULLY REGISTERED",
                'status' => 1
            ]);
        }

    }

    public function emergency_type_list()
    {
        $view = DB::table('tbl_emergency_types')
            ->get();
        return $view;
    }

    public function quick_registrationEm(Request $request)

    {   // return $request->input('facility_id');
        foreach ($request->all() as $key => $value)
            $request[$key] = strtoupper($value);
        $genders = array('MALE', 'FEMALE');

        $facility_id = $request->input('facility_id');
        $gender = $request->input('gender');
        $mobile_number = $request->input('mobile_number');
        $residence_id = $request->input('residence_id');
        $dob = $request->input('dob');
        $mobile_pattern = '#^[0][6-7][1-9][2-9][0-9]{6}$#';


        $pattern = '#^(19[0-9][0-9])|(20[0-9][0-9])-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2][0-9])|(3[0-1]))$#';


        if (!in_array($gender, $genders)) {

            return response()->json([
                'data' => 'Please Select Gender!',
                'status' => '0'
            ]);
        } else if (0 === preg_match($mobile_pattern, $mobile_number) AND !empty($mobile_number)) {

            return response()->json([
                'data' => 'INVALID MOBILE NUMBER',
                'status' => '0'
            ]);
        } else if (!is_numeric($residence_id)) {

            return response()->json([
                'data' => 'PLEASE ENTER Patient RESIDENCE',
                'status' => '0'
            ]);
        } else if (0 === preg_match($pattern, $dob)) {

            return response()->json([
                'data' => 'Invalid Date of Birth',
                'status' => '0'
            ]);
        } else {

            return PatientRegistration::emergency_registration($request);

        }


    }

    public function Patient_edit(Request $request)
    {

 $patient_id_og=$request->patient_id;
        foreach ($request->all() as $key => $value)
            $request[$key] = strtoupper($value);
        $genders = array('MALE', 'FEMALE');
        $Patient_id = $request['Patient_id'];
        $first_name = $request['first_name'];
        $middle_name = $request['middle_name'];
        $last_name = $request['last_name'];
        $gender = $request['gender'];
        $marital_id = $request['marital_id'];
        $country_id = $request['country_id'];
        $occupation_id = $request['occupation_id'];
        $resident_id = $request['residence_id'];
        $tribe_id = $request['tribe_id'];
        $dob = $request['dob'];
        $user_id = $request['user_id'];
        $mobile_number = $request['mobile_number'];
        $mobile_pattern = '#^[0][6-7][1-9][2-9][0-9]{6}$#';
        $pattern = '#^(19[0-9][0-9])|(20[0-9][0-9])-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2][0-9])|(3[0-1]))$#';
        if (!in_array($gender, $genders)) {
            return response()->json([
                'data' => 'PLEASE SELECT GENDER!',
                'status' => '0'
            ]);
        } else if (0 === preg_match($mobile_pattern, $mobile_number) AND !empty($mobile_number)) {
            return response()->json([
                'data' => 'INVALID MOBILE NUMBER',
                'status' => '0'
            ]);
        } else if (0 === preg_match($pattern, $dob)) {
            return response()->json([
                'data' => ' INVALID DATE OF BIRTH',
                'status' => '0'
            ]);
        } else {
$oldData=Tbl_patient::where('id',$patient_id_og)->get();
            Tbl_patient::where('id',$patient_id_og)->update
            ([
                'first_name' => $first_name,
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'gender' => $gender,
                'dob' => $dob,
                'mobile_number' => $mobile_number,
                'marital_id' => $marital_id,
                'country_id' => $country_id,
                'residence_id' => $resident_id,
                'occupation_id' => $occupation_id,
                'tribe_id' => $tribe_id,
                'user_id' => $user_id

            ]);
		$newData=Tbl_patient::where('id', $patient_id_og)->get();
          	 $trackable_id=$patient_id_og;
                    SystemTracking::Tracking($user_id,$Patient_id,$trackable_id,$newData,$oldData);

            return response()->json([
                'data' => "Patient  UPDATED",
                'status' => 1
            ]);
        }

    }

    public function edit_all_data(Request $request)
    {
        foreach ($request->all() as $key => $value)
            $request[$key] = strtoupper($value);
        $genders = array('MALE', 'FEMALE');
        $Patient_id = $request['Patient_id'];
        $first_name = $request['first_name'];
        $middle_name = $request['middle_name'];
        $last_name = $request['last_name'];
        $gender = $request['gender'];
        $dob = $request['dob'];
        $user_id = $request['user_id'];
        $mobile_number = $request['mobile_number'];
        $country_id = $request['country_id'];
        $marital_id = $request['marital_id'];
        $residence_id = $request['residence_id'];
        $occupation_id = $request['occupation_id'];
        $relationship = $request['relationship'];
        $kin_resident = $request['next_residence_id'];
        $tribe_id = $request['tribe_id'];
        $next_of_kin_name = $request['next_of_kin_name'];
        $kin_mobile_number = $request['kin_mobile_number'];
        $mobile_pattern = '#^[0][6-7][1-9][2-9][0-9]{6}$#';
        $pattern = '#^(19[0-9][0-9])|(20[0-9][0-9])-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2][0-9])|(3[0-1]))$#';
        if (!in_array($gender, $genders)) {
            return response()->json([
                'data' => 'PLEASE SELECT GENDER!',
                'status' => '0'
            ]);
        } else if (0 === preg_match($mobile_pattern, $mobile_number) AND !empty($mobile_number)) {
            return response()->json([
                'data' => 'INVALID MOBILE NUMBER',
                'status' => '0'
            ]);
        } else if (0 === preg_match($pattern, $dob)) {
            return response()->json([
                'data' => ' INVALID DATE OF BIRTH',
                'status' => '0'
            ]);
        } else {
            Tbl_patient::where('id', $Patient_id)->update
            ([
                'first_name' => $first_name,
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'gender' => $gender,
                'dob' => $dob,
                'mobile_number' => $mobile_number,
                'residence_id' => $residence_id,
                'marital_id' => $marital_id,
                'occupation_id' => $occupation_id,
                'tribe_id' => $tribe_id,
                'country_id' => $country_id,
                'user_id' => $user_id

            ]);
            Tbl_next_of_kin::where('Patient_id', $Patient_id)->update
            ([
                'next_of_kin_name' => $next_of_kin_name,
                'mobile_number' => $kin_mobile_number,
                'residence_id' => $kin_resident,
                'relationship' => $relationship,
                'user_id' => $user_id
            ]);
            return response()->json([
                'message' => "SUCCESSFULLY UPDATED",
                'status' => 1
            ]);
        }

    }
    public function getCasualtyProcedures(Request $request)
    {
         $request->all();
        $data=[];
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT item_name,gender,COUNT(gender) as counted FROM `vw_emergency_procedures` WHERE gender='MALE' AND created_at BETWEEN '".$start."'  AND '".$end."'GROUP BY item_id";
        $sql1 = "SELECT item_name,gender,COUNT(gender) as counted FROM `vw_emergency_procedures` WHERE gender='FEMALE' AND created_at BETWEEN '".$start."'  AND '".$end."'GROUP BY item_id";
        $sql2 = "SELECT item_name,gender,COUNT(gender) as counts FROM `vw_emergency_procedures` WHERE  created_at BETWEEN '".$start."'  AND '".$end."'GROUP BY item_id";
        $data[] = DB::select(DB::raw($sql));
        $data[] = DB::select(DB::raw($sql1));
        $data[] = DB::select(DB::raw($sql2));
        return $data;
    }
    public function getCasualtyPatientsReport(Request $request)
    {
         $request->all();
        $data=[];
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT emergency_name,gender,emergency_type,COUNT(gender) as counted FROM `vw_emergency_records` WHERE gender='MALE' AND emergency_type='MOTOR ACCIDENT' AND created_at BETWEEN '".$start."'  AND '".$end."'GROUP BY emergency_name";
        $sql1 = "SELECT emergency_name,gender,emergency_type,COUNT(gender) as counted FROM `vw_emergency_records` WHERE gender='FEMALE' AND emergency_type='MOTOR ACCIDENT' AND  created_at BETWEEN '".$start."'  AND '".$end."'GROUP BY emergency_name";
        $sql2 = "SELECT emergency_name,gender,emergency_type,COUNT(gender) as counted FROM `vw_emergency_records` WHERE gender='MALE' AND emergency_type='NON-MOTOR ACCIDENT' AND  created_at BETWEEN '".$start."'  AND '".$end."'GROUP BY emergency_name";
        $sql3 = "SELECT emergency_name,gender,emergency_type,COUNT(gender) as counted FROM `vw_emergency_records` WHERE gender='FEMALE' AND emergency_type='NON-MOTOR ACCIDENT' AND  created_at BETWEEN '".$start."'  AND '".$end."'GROUP BY emergency_name";
        $sql4 = "SELECT emergency_name,gender,emergency_type,COUNT(gender) as counted FROM `vw_emergency_records` WHERE   created_at BETWEEN '".$start."'  AND '".$end."'GROUP BY emergency_name";
        $data[] = DB::select(DB::raw($sql));
        $data[] = DB::select(DB::raw($sql1));
        $data[] = DB::select(DB::raw($sql2));
        $data[] = DB::select(DB::raw($sql3));
        $data[] = DB::select(DB::raw($sql4));
        return $data;
    }

    public function casualtyEncounter(Request $request)
    {
        $facility_id=$request->input('facility_id');
        $Patient_id=$request->input('Patient_id');
        $price_id=$request->input('price_id');
        $service_id=$request->input('service_id');
        $item_type_id=$request->input('item_type_id');
        $user_id=$request->input('user_id');
        $dept_id=$request->input('dept_id');
        $quantity=1;
        $status_id=1;
        $payment_filter=null;
        if($request->input('main_category_id')!=1)
        {
            $status_id=1;
            $payment_filter=$request->input('payment_filter');
        }
        if(PatientRegistration::duplicate('tbl_patients',array('id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >= 60))"), array($Patient_id))==true) {

            PatientRegistration::PatientAccountNumber($facility_id, $Patient_id,$user_id);
        }
        $account=$this->getCurrentPatientAccountNumber($Patient_id,$facility_id);
        $account_number_id=$account->account_number_id;
        $bill_id=$request->input('bill_id');
        $main_category_id=$request->input('main_category_id');
        if (!isset($service_id)) {

            return response()->json([
                'data' => 'PLEASE SELECT SERVICE FROM THE SUGESTION LIST',
                'status' => '0'
            ]);
        }
        else{
            if(PatientRegistration::duplicate('tbl_invoice_lines',array('Patient_id','item_type_id','quantity',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($Patient_id,$item_type_id,$quantity,''))==true){

                return response()->json([
                    'data' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                    'status' => '0'
                ]);
            }

            else{
                $payment_category =Tbl_bills_category::create(['Patient_id'=>$Patient_id,'account_id'=>$account_number_id,'user_id'=>$user_id,'bill_id'=>$bill_id,'main_category_id'=>$main_category_id]);



                $encounter =Tbl_encounter_invoice::create(array('account_number_id'=>$account_number_id,'facility_id'=>$facility_id,'user_id'=>$user_id));


                if($encounter->save()){
                    $invoice_line =Tbl_invoice_line::create(array('invoice_id'=>$encounter->id,'payment_filter'=>$bill_id,
                        'item_type_id'=>$item_type_id,'facility_id'=>$facility_id,'quantity'=>number_format($quantity, 2, '.', ''),'user_id'=>$user_id,'item_price_id'=>$price_id,'status_id'=>$status_id,'discount_by'=>$user_id,'Patient_id'=>$Patient_id));

                    if($dept_id>7) {

                        $clinic_save = Tbl_clinic_instruction::create(array('received' => 0, 'dept_id' => $dept_id, 'doctor_requesting_id' => $user_id,
                            'consultation_id' => $service_id, 'sender_clinic_id' => 1, 'visit_id' => $account_number_id, 'on_off' => 0));
                    }
                   if($dept_id==14){
                       $account_data = Tbl_accounts_number::
                       where('id', $account_number_id)
                           ->where('Patient_id', $Patient_id)
                           ->update([
                               'status'=>1
                           ]);
                   }
                    // return dd($invoice_line);

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
    // public function enterEncounterEmergency(Request $request)
    // {
    //     // some validation may be required..
    //     $account_id = $request->input('account_id');
    //     $emergency_type_id = $request->input('emergency_type_id');
    //     $facility_id = $request->input('facility_id');
    //     $Patient_id = $request->input('Patient_id');
    //     $price_id = $request->input('price_id');
    //     $service_id = $request->input('service_id');
    //     $item_type_id = $request->input('item_type_id');
    //     $user_id = $request->input('user_id');
    //     $dept_id = $request->input('dept_id');
    //     $patient_id = $request->input('patient_id');
    //     $quantity = 1;
    //     $status_id = 1;
    //     $payment_filter = null;
    //     if ($request->input('main_category_id') != 1) {
    //         $status_id = 1;
    //         $payment_filter = $request->input('payment_filter');
    //     }

    //     if (PatientRegistration::duplicate('tbl_patients', array('id', "((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >= 60))"), array($Patient_id)) == true) {

    //         PatientRegistration::emergencyAccountNumber($facility_id, $Patient_id, $user_id);
    //     }
    //     $account=patientRegistration::patientAccountNumber($facility_id,$patient_id,$user_id);

    //             $account_number_id=$account;
    //     $bill_id = $request->input('bill_id');
    //     $main_category_id = $request->input('main_category_id');
    //     //return $getLastVisit[0]->created_at;


    //     if (!isset($service_id)) {

    //         return response()->json([
    //             'data' => 'PLEASE SELECT SERVICE FROM THE SUGESTION LIST',
    //             'status' => '0'
    //         ]);
    //     } else {

    //         if (PatientRegistration::duplicate('tbl_invoice_lines', array('Patient_id', 'item_type_id', 'quantity', "((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($Patient_id, $item_type_id, $quantity, '')) == true) {

    //             return response()->json([
    //                 'data' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
    //                 'status' => '0'
    //             ]);
    //         } else {
    //             $payment_category = Tbl_bills_category::create(['patient_id' => $patient_id, 'account_id' => $account_number_id, 'user_id' => $user_id, 'bill_id' => $bill_id, 'main_category_id' => $main_category_id]);


    //             $encounter = Tbl_encounter_invoice::create(array('account_number_id' => $account_number_id, 'facility_id' => $facility_id, 'user_id' => $user_id));


    //             if ($encounter->save()) {
    //                 $invoice_line = Tbl_invoice_line::create(array('invoice_id' => $encounter->id, 'payment_filter' => $bill_id,
    //                     'item_type_id' => $item_type_id, 'facility_id' => $facility_id, 'quantity' => $quantity, 'user_id' => $user_id, 'item_price_id' => $price_id, 'status_id' => $status_id, 'discount' => 0, 'discount_by' => $user_id, 'Patient_id' => $Patient_id));

    //                 if ($dept_id > 7) {
    //                     $clinic_save = Tbl_clinic_instruction::create(array('received' => 0, 'dept_id' => $dept_id, 'doctor_requesting_id' => $user_id,
    //                         'consultation_id' => $service_id, 'sender_clinic_id' => 1, 'visit_id' => $account_number_id, 'on_off' => 0));
    //                 }
                    // Tbl_emergency_Patient::create(array(
                    //     'visiting_id' => $account_id,
                    //     'emergency_type_id' => $emergency_type_id,
                    //     'registered_by' => $user_id
                    // ));

    //                 if ($invoice_line->save()) {
    //                     return response()->json([
    //                         'data' => 'SUCCESSFULLY SAVED AND SUBMITED TO ENCOUNTER',
    //                         'account_number' => 'Account No: ',
    //                         'status' => '1'
    //                     ]);

    //                 }
    //             }
    //         }
    //     }

    // }

     public function enterEncounterEmergency(Request $request) {
        
        if (!$request->has('free_reattendance') && !$request->has('service_id')) {
            return response()->json([
                'data' => 'PLEASE SELECT SERVICE FROM THE SUGESTION LIST',
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
        try{
            if($request->is_referral != 0){
                $referral = [
                            "patient_id"=>$patient_id,
                            "visit_id"=>$account->account_number_id,
                            "facility_id"=> $facility_id,
                            "referring_facility_id"=> $request->referring_facility_id,
                            "user_id"=> $user_id,
                        ];
                Tbl_received_referral::create($referral);
            }
        }catch(Exception $ex){}
        
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
                        'item_type_id'=>$item_type_id,'facility_id'=>$facility_id,'quantity'=>number_format($quantity, 2, '.', ''),'user_id'=>$user_id,'item_price_id'=>$price_id,'status_id'=>$status_id,'discount_by'=>$user_id,'patient_id'=>$patient_id)
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

        return response()->json([
                'data' => 'VISIT SUCCESSFULLY STARTED',
                'account_number' =>'Account No: ',
                'status' => '1'
            ]);
    }





    public function loadEmergencyCount(Request $request)
    {
       $request->all();
        $visiting_id = $request['account_id'];
        return $data = Tbl_emergency_Patient::
        where('visiting_id', $visiting_id)
            ->join('tbl_emergency_types','tbl_emergency_types.id','=','tbl_emergency_Patients.emergency_type_id')
            ->select('tbl_emergency_types.emergency_name','tbl_emergency_types.emergency_type','tbl_emergency_Patients.updated_at')
            ->get();
    }
    public function emergencyTypeSave(Request $request)
    {
         $request->all();

        $visiting_id = $request['visiting_id'];
        $emergency_type_id = $request['emergency_type'];
        $registered_by = $request['user_id'];
        $data = Tbl_emergency_Patient::
        where('visiting_id', $visiting_id)
            ->where('emergency_type_id', $emergency_type_id)
            ->first();
        if (count($data) > 0) {
            return response()->json([
                'message' => " Patient ALREADY COUNTED",
                'status' => 0
            ]);
        } else {
            Tbl_emergency_Patient::create([
                'visiting_id' => $visiting_id,
                'emergency_type_id' => $emergency_type_id,
                'registered_by' => $registered_by
            ]);

            return   response()->json([
                'message' => "Patient COUNTED ",
                'status' => 1
            ]);
        }

    }
    public function Patient_exemption_emergency(Request $request){
        $request->all();
        $account_id = $request->input('account_id');
        $emergency_type_id = $request->input('emergency_type_id');
        $facility_id = $request['facility_id'];
        $Patient_id = $request['Patient_id'];
        $user_id = $request['user_id'];
        $bill_id = $request['bill_id'];
        $exemption_reason = $request['exemption_reason'];
        $exemption_type_id = $request['exemption_type_id'];
        $filter = $request['exemption_type_id'];
        $main_category_id = $request['main_category_id'];
        $payment_filter = $request['exemption_type_id'];
        $price_id = $request['item_price_id'];
        $quantity = $request['quantity'];
        $status_id = $request['status_id'];
        $item_type_id = $request['item_type_id'];
        $change = $request['change'];

        if (PatientRegistration::duplicate('tbl_exemptions', ['user_id', 'Patient_id', 'exemption_reason', 'exemption_type_id',
                '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$user_id, $Patient_id, $exemption_reason, $exemption_type_id]) == true
        ) {
            return response()->json([
                'msg' => 'Oops!.. Duplication or Double entry detected.. System detected that, you are entering a
                    Same data set more than once....',
                'status' => 0
            ]);

        } else {
            $exemption = Tbl_exemption::where('Patient_id', $Patient_id)
                ->where('exemption_type_id', $exemption_type_id)->first();
            if ($exemption_reason == "") {
                return response()->json([
                    'msg' => 'Please Enter Reason(s) for Exemption ',
                    'status' => 0
                ]);
            }
            if ($change == 'true') {
                $category_number = Tbl_bills_category::where('Patient_id', $Patient_id)->orderBy('id', 'desc')->first();
                $update_id = $category_number->id;
                $data = Tbl_bills_category::where('id', $update_id)->update([
                    'main_category_id' => $main_category_id,
                    'bill_id' => $bill_id,

                ]);
            } else {
                if ($request->input('main_category_id') != 1) {
                    $status_id = 1;
                    $payment_filter = $request->input('payment_filter');
                }

                if (PatientRegistration::duplicate('tbl_patients', array('id', "((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >= 60))"), array($Patient_id)) == true) {

                    PatientRegistration::PatientAccountNumber($facility_id, $Patient_id, $user_id);
                }
                $account = PatientRegistration::getCurrentPatientAccountNumber($Patient_id, $facility_id);
                $account_number_id = $account->account_number_id;
                $bill_id = $request->input('bill_id');
                $main_category_id = $request->input('main_category_id');
                if (!is_numeric($price_id)) {
                     response()->json([
                        'msg' => 'PLEASE SELECT SERVICE FROM THE SUGESTION LIST',
                        'status' => '0'
                    ]);
                } else {

                    if (PatientRegistration::duplicate('tbl_invoice_lines', array('Patient_id', 'item_type_id', 'quantity', "((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($Patient_id, $item_type_id, $quantity, '')) == true) {

                         response()->json([
                            'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                            'status' => '0'
                        ]);
                    } else {
                        $payment_category = Tbl_bills_category::create(['Patient_id' => $Patient_id, 'account_id' => $account_number_id, 'user_id' => $user_id, 'bill_id' => $bill_id, 'main_category_id' => $main_category_id]);
                        $encounter = Tbl_encounter_invoice::create(array('account_number_id' => $account_number_id, 'facility_id' => $facility_id, 'user_id' => $user_id));
                        $invoice_line = Tbl_invoice_line::create(array('invoice_id' => $encounter->id, 'payment_filter' => $filter,
                            'item_type_id' => $item_type_id, 'facility_id' => $facility_id, 'quantity' => number_format($quantity, 2, '.', ''), 'user_id' => $user_id, 'item_price_id' => $price_id, 'status_id' => $status_id, 'discount_by' => $user_id, 'Patient_id' => $Patient_id));
                        Tbl_emergency_Patient::create(array(
                            'visiting_id' => $account_id,
                            'emergency_type_id' => $emergency_type_id,
                            'registered_by' => $user_id
                        ));


                    }
                }

            }
            if (count($exemption) == 1) {
                $exemption_type_id_exists = $exemption->exemption_type_id;
            } else {
                $exemption_type_id_exists = "";
            }
            $PatientExistwithPreviousExemptNumber = Tbl_exemption_number::where('Patient_id', $Patient_id)
                ->orderBy('id', 'desc')
                ->first();
            if (count($PatientExistwithPreviousExemptNumber) > 0) {
                $exemption_no_number = $PatientExistwithPreviousExemptNumber->exemption_number;
                $Patient = new Tbl_exemption($request->all());
                $Patient['exemption_no'] = $exemption_no_number;
                $Patient->save();
                $file = 0;
                return response()->json([
                    'msg' => 'EXEMPTION NUMBER ' . ' ' . $exemption_no_number,
                    'status' => 1
                ]);


            } //checking if Patient has not given exemption number create a ne number
            else {
                $exemption_no_number = PatientRegistration::PatientExemptionNumber($facility_id, $Patient_id, $user_id);
                $Patient = new Tbl_exemption($request->all());
                $Patient['exemption_no'] = $exemption_no_number;
                $Patient->save();
                $file = 0;
                return response()->json([
                    'msg' => 'EXEMPTION NUMBER ' . ' ' . $exemption_no_number,
                    'status' => 1
                ]);


            }

        }
    }

    public function registeredResidents(Request $request)
    {
        $name = $request['name'];
        $residences = DB::table('vw_residences')
            ->where('residence_name', 'like', '%' . $name . '%')
            ->get();
        return $residences;
    }

    public function emergency_report(){
            return DB::table('vw_emergency_records')

                ->get();
        }


    public function PatientsInformation($id)
    {
        return DB::table('vw_Patient_details')
            ->where('Patient_id', $id)
            ->get();

    }

//DOCTOR PORTION

//get Patients for consultations

    public function getIpdPatients(Request $request)
    {
        $id = $request->input('facility_id');
        $Patient = DB::table('vw_ipd_Patients')
            ->where('vw_ipd_Patients.facility_id', $id)
            ->where('vw_ipd_Patients.admission_status_id', 2)
            ->groupBy('vw_ipd_Patients.Patient_id')
            ->get()->take(20);
        return $Patient;
    }

    public function vitalsDateEm(Request $request)
    {
        $Patient_id = $request->input('Patient_id');
        $sql = "SELECT date_attended FROM tbl_vital_signs WHERE Patient_id='" . $Patient_id . "' GROUP BY date_attended LIMIT 5 ";
        $vital_date = DB::select(DB::raw($sql));
        return $vital_date;
    }

    public function vitalsTimeEm(Request $request)
    {
        $Patient_id = $request->input('Patient_id');
        $date_attended = $request->input('date_attended');
        $sql = "SELECT time_attended FROM tbl_vital_signs WHERE Patient_id='" . $Patient_id . "' AND date_attended='" . $date_attended . "' GROUP BY time_attended LIMIT 5 ";
        $vital_time = DB::select(DB::raw($sql));
        return $vital_time;
    }

    public function PatientVitalsEm(Request $request)
    {
        $Patient_id = $request->input('Patient_id');
        $time_attended = $request->input('time_attended');
        $sql = "SELECT * FROM tbl_vital_signs WHERE Patient_id='" .$Patient_id ."' AND time_attended='" . $time_attended . "' ";
        $vital_data = DB::select(DB::raw($sql));
        return $vital_data;
    }

    //transfers
    public function internalTransfer(Request $request)
    {
        $date = date('Y-m-d');
        $admission_id = $request->input('admission_id');
        $doctor_id = $request->input('doctor_id');
        $icu_status_id = $request->input('icu_status_id');
        $source = $request->input('from');
        $postData = Tbl_icu_entry::create([
            'admission_id' => $admission_id,
            'doctor_id' => $doctor_id,
            'icu_status_id' => $icu_status_id,
            'from' => $source,
            'date_admitted' => $date,
        ]);
        $instructions = Tbl_instruction::create($request->all());
        return $instructions;
    }

    public function icuPatients(Request $request)
    {
        return DB::table('vw_icu_Patients')
            ->where('facility_id', $request->input('facility_id'))
            ->groupBy('Patient_id')
            ->get();
    }

    public function postUnavailableInvestigationsEm(Request $request)
    {
        if (count($request->all()) > 0) {
            foreach ($request->all() as $data) {
                $os = Tbl_unavailable_test::create([
                    'Patient_id' => $data['Patient_id'],
                    'item_id' => $data['item_id'],
                    'user_id' => $data['user_id'],
                    'facility_id' => $data['facility_id'],
                    'visit_date_id' => $data['visit_date_id'],
                ]);
            }
            return $os;
        }
    }

    public function icuVitals(Request $request)
    {
        $facility = $request->input('facility_id');
        $Patient = $request->input('Patient_id');
        $sql = "select vital_name,value,units from vw_icu_Patients WHERE facility_id =" . $facility . " and Patient_id =" . $Patient . " group by vital_name ";
        return DB::select(DB::raw($sql));

    }

    //Allergies
    public function getAllergyEm(Request $request)
    {
        $Patient_id = $request->input('Patient_id');
        $sql = "select * from vw_allergies WHERE Patient_id = '" . $Patient_id . "' order by descriptions DESC";
        return DB::select(DB::raw($sql));
    }

    //get chief complaints
    public function chiefComplaintsEm(Request $request)
    {
        $search = $request->input('search');
        $limit = 10;
        $sql = "select * from tbl_body_systems where name like '%" . $search . "%' AND category !='Past Medical History'
         AND category !='Immunisation' AND category !='Admission History' limit " . $limit;
        $Patient = DB::select(DB::raw($sql));
        return $Patient;
    }

    //review of systems
    public function reviewOfSystemsEm(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $limit = 10;
        $sql = "select * from tbl_body_systems where name like '%" . $search . "%' AND category ='" . $category . "'
         limit " . $limit;
        $Patient = DB::select(DB::raw($sql));
        return $Patient;
    }

    public function pastMedications(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $limit = 10;
        $sql = "select * from `vw_shop_items` where item_name like '%" . $search . "%' AND item_category ='" . $category . "'
         limit " . $limit;
        $Patient = DB::select(DB::raw($sql));
        return $Patient;
    }

    //diagnosis
    public function getDiagnosisEm(Request $request)
    {
        $search = $request->input('search');
        $limit = 10;
        $sql = "select * from tbl_diagnosis_descriptions where CODE NOT LIKE 'OP%' AND CODE NOT LIKE 'IP%' AND (description like '%".$search."%' or code like '%".$search."%') order by length(description) asc, code limit ".$limit;
        $Patient = DB::select(DB::raw($sql));
        return $Patient;
    }

    public function getSubDeptsEm(Request $request)
    {
        $id = $request->input('department_id');
        return DB::table('tbl_sub_departments')->where('department_id', $id)->get();
    }

    public function getWardsEm($facility_id)
    {
        return DB::table('vw_wards')->where('facility_id', $facility_id)->get();
    }

    public function admitPatientEm(Request $request)
    {
        $date = date('y-m-d');
        $admit = Tbl_admission::create([
            'admission_date' => $date, 'Patient_id' => $request->input('Patient_id'), 'admission_status_id' => $request->input('admission_status_id'),
            'facility_id' => $request->input('facility_id'), 'user_id' => $request->input('user_id'),
        ]);
        $adm_id = $admit->id;
        $admission = Tbl_instruction::create([
            'instructions' => $request->input('instructions'), 'prescriptions' => $request->input('prescriptions'),
            'facility_id' => $request->input('facility_id'), 'user_id' => $request->input('user_id'),
            'admission_id' => $adm_id, 'Patient_id' => $request->input('Patient_id'), 'ward_id' => $request->input('ward_id'),
        ]);
        return $admission;

    }

    public function getTestsEm(Request $request)
    {
        $sub = $request->input('sub_dept_id');
        $facility_id = $request->input('facility_id');
        $Patient_category_id = $request->input('Patient_category_id');
        $sql = " select*from `vw_investigations_tests` where sub_dept_id='" . $sub . " ' AND Patient_category_id ='" . $Patient_category_id . "' AND facility_id = '" . $facility_id . "' GROUP BY item_id";
        $investigation = DB::select(DB::raw($sql));
        return $investigation;
    }

// modified function for getting lab single test only to the doctor
    public function getSingleTestsEm(Request $request)
    {
        $sub = $request->input('sub_dept_id');
        $facility_id = $request->input('facility_id');
        $Patient_category_id = $request->input('Patient_category_id');
        $sql = " select*from `vw_labtests_to_doctors` where sub_dept_id='" . $sub . " ' AND Patient_category_id ='" . $Patient_category_id . "' AND facility_id = '" . $facility_id . "' GROUP BY item_id";
        $investigation = DB::select(DB::raw($sql));
        return $investigation;
    }


    public function getPanelsEm(Request $request)
    {
        $sub = $request->input('sub_dept_id');
        $facility_id = $request->input('facility_id');
        $Patient_category_id = $request->input('Patient_category_id');
        $sql = " select*from `vw_labpanels` where sub_dept_id='" . $sub . " ' AND Patient_category_id ='" . $Patient_category_id . "' AND facility_id = '" . $facility_id . "' GROUP BY item_id";
        $panel = DB::select(DB::raw($sql));
        return $panel;
    }

    public function getPanelComponents(Request $request)
    {
        if (count($request->all()) > 0) {
            $sub = $request->input('sub_department_id');
            $facility_id = $request->input('facility_id');
            $item_id = $request->input('item_id');
            $sql = " select*from `vw_labtests` where sub_department_id='" . $sub . " ' AND item_id ='" . $item_id . "' AND facility_id = '" . $facility_id . "' ";
            $panelComp = DB::select(DB::raw($sql));
            return $panelComp;
        }
    }

    public function getBeds(Request $request)
    {
        $sub = $request->input('ward_id');
        $facility_id = $request->input('facility_id');
        $sql = "select*from `tbl_beds` where ward_id='" . $sub . " ' AND facility_id = '" . $facility_id . "' ";
        $investigation = DB::select(DB::raw($sql));
        return $investigation;
    }

    public function postHistoryEm(Request $request)
    {

        $dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $Patient_id = $data[0]->Patient_id;
        $facility_id = $data[0]->facility_id;
        $user_id = $data[0]->user_id;
        $visit_date_id = $data[0]->visit_date_id;
        $admission_id = $data[0]->admission_id;
        $hpi = $data[0]->hpi;
        $other_complaints = $data[0]->other_complaints;
        $data2 = Tbl_history_examination::create(['Patient_id' => $Patient_id, 'facility_id' => $facility_id, 'user_id' => $user_id,
            'visit_date_id' => $visit_date_id, 'admission_id' => $admission_id,
        ]);
        $id = $data2->id;
        foreach ($request->all() as $d) {
            $postData = Tbl_complaint::create([
                'description' => $d['description'],
                'duration' => $d['duration'],
                'duration_unit' => $d['duration_unit'],
                'status' => $d['status'],
                'history_exam_id' => $id,
            ]);
        }
        $postData2 = Tbl_complaint::create([
            'other_complaints' => $other_complaints,
            'hpi' => $hpi,
            'history_exam_id' => $id,
        ]);
        return $postData2;
    }

    public function postRoSEm(Request $request)
    {
        $dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $Patient_id = $data[0]->Patient_id;
        $facility_id = $data[0]->facility_id;
        $user_id = $data[0]->user_id;
        $visit_date_id = $data[0]->visit_date_id;
        $admission_id = $data[0]->admission_id;
        $review_summary = $data[0]->review_summary;
        $data2 = Tbl_review_system::create([
            'Patient_id' => $Patient_id,
            'facility_id' => $facility_id,
            'user_id' => $user_id,
            'visit_date_id' => $visit_date_id,
            'admission_id' => $admission_id,
        ]);
        $id = $data2->id;
        foreach ($request->all() as $d) {
            $postData = Tbl_review_of_system::create([
                'status' => $d['status'],
                'system_id' => $d['system_id'],
                'review_system_id' => $id,
            ]);
        }
        $postData2 = Tbl_review_of_system::create([
            'review_summary' => $review_summary,
            'review_system_id' => $id,
        ]);
        return $postData2;
    }

    public function postPastMed(Request $request)
    {
        $dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $Patient_id = $data[0]->Patient_id;
        $facility_id = $data[0]->facility_id;
        $user_id = $data[0]->user_id;
        $visit_date_id = $data[0]->visit_date_id;
        $admission_id = $data[0]->admission_id;
        $other_past_medicals = $data[0]->other_past_medicals;
        $data2 = Tbl_past_medical_history::create([
            'Patient_id' => $Patient_id,
            'facility_id' => $facility_id,
            'user_id' => $user_id,
            'visit_date_id' => $visit_date_id,
            'admission_id' => $admission_id,
        ]);
        $id = $data2->id;
        foreach ($request->all() as $d) {
            $postData = Tbl_past_medical_record::create([
                'status' => $d['status'],
                'descriptions' => $d['name'],
                'past_medical_history_id' => $id,
            ]);
        }
        Tbl_past_medical_record::create([
            'other_past_medicals' => $other_past_medicals,
            'past_medical_history_id' => $id,
        ]);
        return $postData;
    }

    public function birthHistoryEm(Request $request)
    {
        $view = Tbl_birth_history::create($request->all());
        $id = $view->id;
        $data = Tbl_child_birth_history::create([
            'natal' => $request->input('natal'),
            'post_natal' => $request->input('post_natal'),
            'antenatal' => $request->input('antenatal'),
            'nutrition' => $request->input('nutrition'),
            'growth' => $request->input('growth'),
            'development' => $request->input('development'),
            'birth_history_id' => $id,
        ]);
        return $data;
    }

    public function postPastMedEm(Request $request)
    {
        $dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $Patient_id = $data[0]->Patient_id;
        $facility_id = $data[0]->facility_id;
        $user_id = $data[0]->user_id;
        $visit_date_id = $data[0]->visit_date_id;
        $admission_id = $data[0]->admission_id;
        $other_past_medicals = $data[0]->other_past_medicals;
        $data2 = Tbl_past_medical_history::create([
            'Patient_id' => $Patient_id,
            'facility_id' => $facility_id,
            'user_id' => $user_id,
            'visit_date_id' => $visit_date_id,
            'admission_id' => $admission_id,
        ]);
        $id = $data2->id;
        foreach ($request->all() as $d) {
            $postData = Tbl_past_medical_record::create([
                'status' => $d['status'],
                'descriptions' => $d['name'],
                'past_medical_history_id' => $id,
            ]);
        }
        Tbl_past_medical_record::create([
            'other_past_medicals' => $other_past_medicals,
            'past_medical_history_id' => $id,
        ]);
        return $postData;
    }


    public function familyHistoryEm(Request $request)
    {
        $view = Tbl_family_history::create($request->all());
        $id = $view->id;
        $data = Tbl_family_social_history::create([
            'chronic_illness' => $request->input('chronic_illness'),
            'substance_abuse' => $request->input('substance_abuse'),
            'adoption' => $request->input('adoption'),
            'others' => $request->input('others'),
            'family_history_id' => $id,
        ]);
        return $data;
    }

    public function PatientToClinic(Request $request)
    {
        $data = Tbl_clinic_instruction::create([
            'summary' => $request['summary'],
            'priority' => $request['priority'],
            'visit_id' => $request['visit_id'],
            'dept_id' => $request['dept_id'],
            'received' => $request['received'],
            'doctor_requesting_id' => $request['doctor_requesting_id'],
            'sender_clinic_id' => $request['sender_clinic_id']
        ]);
        return $data;
    }

    public function postObsEm(Request $request)
    {
        $view = Tbl_obs_gyn::create($request->all());
        $id = $view->id;
        $data = Tbl_obs_gyn_record::create([
            'menarche' => $request->input('menarche'),
            'menopause' => $request->input('menopause'),
            'menstrual_cycles' => $request->input('menstrual_cycles'),
            'pad_changes' => $request->input('pad_changes'),
            'recurrent_menstruation' => $request->input('recurrent_menstruation'),
            'contraceptives' => $request->input('contraceptives'),
            'pregnancy' => $request->input('pregnancy'),
            'lnmp' => $request->input('lnmp'),
            'gravidity' => $request->input('gravidity'),
            'parity' => $request->input('parity'),
            'living_children' => $request->input('living_children'),
            'obs_gyn_id' => $id,
        ]);
        return $data;
    }

    public function postPhysicalEm(Request $request)
    {
        $dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $Patient_id = $data[0]->Patient_id;
        $facility_id = $data[0]->facility_id;
        $user_id = $data[0]->user_id;
        $visit_date_id = $data[0]->visit_date_id;
        $admission_id = $data[0]->admission_id;
        $data2 = Tbl_physical_examination::create([
            'Patient_id' => $Patient_id,
            'facility_id' => $facility_id,
            'user_id' => $user_id,
            'visit_date_id' => $visit_date_id,
            'admission_id' => $admission_id,
        ]);
        $id = $data2->id;
        foreach ($request->all() as $d) {
            $postData = Tbl_physical_examination_record::create(['local_examination' => $d['local_examination'], 'observation' => $d['observation'], 'category' => $d['category'], 'system' => $d['system'], 'physical_examination_id' => $id,
            ]);
        }
        return $postData;
    }

    public function emergencySurveyEm(Request $request)
    {
        $view = Tbl_survey_history::create($request->all());
        $id = $view->id;
        $data = Tbl_emergency_survey_history::create([
            'appearance' => $request->input('appearance'),
            'airway' => $request->input('airway'),
            'breathing' => $request->input('breathing'),
            'circulation' => $request->input('circulation'),
            'exposure' => $request->input('exposure'),
            'disability' => $request->input('disability'),
            'intervention' => $request->input('Intervention'),
            'survey_history_id' => $id,
        ]);
        return $data;
    }


    public function emergencySaveCommaScale(Request $request)
    {
        $view = Tbl_comma_scale::create($request->all());
        $id = $view->id;
        $data = Tbl_comma_scales_history::create([
            'eye' => $request->input('eye'),
            'verbal' => $request->input('verbal'),
            'motor' => $request->input('motor'),
            'comma_scale_id' => $id,
        ]);
        return $data;
    }

    public function postLocalPhysicalEm(Request $request)
    {
        if (count($request->all()) > 0) {
            $data2 = Tbl_physical_examination::create($request->all());
            $id = $data2->id;
            $postData = Tbl_physical_examination_record::create(['local_examination' => $request->input('local_examination'), 'physical_examination_id' => $id,
            ]);
            return $postData;
        }
    }

    public function postInvestigationsEm(Request $request)
    {
        $dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $Patient_id = $data[0]->Patient_id;
        $requesting_department_id = $data[0]->requesting_department_id;
        $user_id = $data[0]->user_id;
        $account_number_id = $data[0]->account_number_id;
        $facility_id = $data[0]->facility_id;
        $admission_id = $data[0]->admission_id;
        $investgation = Tbl_request::create(["requesting_department_id" => $requesting_department_id, "doctor_id" => $user_id, "Patient_id" => $Patient_id, "visit_date_id" => $account_number_id, "eraser" => 1, "admission_id" => $admission_id]);
        $id = $investgation->id;
        foreach ($request->all() as $d) {
            $postData = Tbl_order::create(['priority' => $d['priority'], 'clinical_note' => $d['clinical_note'], 'test_id' => $d['item_id'], 'order_id' => $id, "eraser" => 1,]);
        }
        $billing = Tbl_encounter_invoice::create(["account_number_id" => $account_number_id, "user_id" => $user_id, "facility_id" => $facility_id,]);
        $invoice_id = $billing->id;

        foreach ($request->all() as $b) {
            $billsData = Tbl_invoice_line::create(["invoice_id" => $invoice_id, "item_type_id" => $b['item_type_id'],
                "quantity" => 1, "item_price_id" => $b['item_price_id'], "user_id" => $b['user_id'], "Patient_id" => $b['Patient_id'],
                "status_id" => $b['status_id'], "facility_id" => $b['facility_id'], "discount_by" => $b['user_id'], "payment_filter" => $b['payment_filter'],]);
        }
        return $billsData;
    }

    public function investigationList(Request $request)
    {
        $pt = $request->input('facility_id');
        $limit = 15;
        $sql = "select * from vw_investigation_results where facility_id = '" . $pt . "' GROUP BY Patient_id limit " . $limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
        return $investigation;
    }

    public function getAllInvPatients(Request $request)
    {
        $pt = $request->input('facility_id');
        $sql = "select * from vw_investigation_results where facility_id = '" . $pt . "'  GROUP BY Patient_id ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
        return $investigation;
    }

    //to be continued
    public function updateInvestigationList(Request $request)
    {
        $pt = $request->input('Patient_id');
        $facility_id = $request->input('facility_id');

    }

    public function getInvestigationResultsEm(Request $request)
    {
        $dept = $request->input('dept_id');
        $pt = $request->input('Patient_id');
        $date = $request->input('date_attended');
        $investigation = DB::table('vw_investigation_results')
            ->where('Patient_id', $pt)
            ->where('dept_id', $dept)
            ->where('date_attended', $date)
            ->get()
            ->take(5);
        return $investigation;
    }

    public function getInvestigationResultedEm(Request $request)
    {
        $rs = [];
        $dept = $request->input('dept_id');
        $pt = $request->input('Patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_investigation_results where Patient_id = " . $pt . " AND dept_id = " . $dept . " AND date_attended = '" . $date . "' AND item_id NOT IN(SELECT item_id FROM tbl_panels)";
        $sql2 = "select * from vw_investigation_results where Patient_id = '" . $pt . "' AND dept_id = '" . $dept . "' AND date_attended = '" . $date . "' AND item_id=(SELECT item_id FROM tbl_panels) ";
        $rs[] = DB::select(DB::raw($sql));
        $rs[] = DB::select(DB::raw($sql2));
        return $rs;
    }

    public function getResultsEm(Request $request)
    {
        $dept = $request->input('dept_id');
        $pt = $request->input('Patient_id');
        $limit = 5;
        $sql = "select date_attended,Patient_id,dept_id from vw_investigation_results where Patient_id = '" . $pt . "' AND dept_id = '" . $dept . "' GROUP BY date_attended limit " . $limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
        return $investigation;
    }

    public function postDiagnosisEm(Request $request)
    {
        $dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $Patient_id = $data[0]->Patient_id;
        $user_id = $data[0]->user_id;
        $visit_date_id = $data[0]->visit_date_id;
        $facility_id = $data[0]->facility_id;
        $admission_id = $data[0]->admission_id;
        $diagnosis = Tbl_diagnosis::create(['Patient_id' => $Patient_id, 'facility_id' => $facility_id, 'user_id' => $user_id,
            'visit_date_id' => $visit_date_id, "admission_id" => $admission_id]);
        $id = $diagnosis->id;
        foreach ($request->all() as $d) {
            $diag = Tbl_diagnosis_detail::create([
                "diagnosis_description_id" => $d['diagnosis_description_id'],
                "status" => $d['status'],
                "diagnosis_id" => $id,
            ]);
        }
        return $diag;
    }

    public function getMedicineEm(Request $request)
    {
        $search = $request->input('search');
        $id = $request->input('facility_id');
        $category_id = $request->input('Patient_category_id');
        $limit = 10;
        $sql = "select * from vw_shop_items where item_name like '%" . $search . "%' AND dept_id= 4 AND Patient_category_id ='" . $category_id . "' AND facility_id = '" . $id . "' limit " . $limit;
        $Patient = DB::select(DB::raw($sql));
        return $Patient;
    }

    public function balanceCheckEm(Request $request)
    {
        $id = $request->input('facility_id');
        $item_id = $request->input('item_id');
        $main_category_id = $request->input('main_category_id');
        $sql = "select balance from vw_dispensing_item_balance where item_id = '" . $item_id . "' AND facility_id = '" . $id . "' AND main_category_id ='" . $main_category_id . "' ";
        $Patient = DB::select(DB::raw($sql));
        return $Patient;

    }

    public function getPatientProceduresEm(Request $request)
    {
        $search = $request->input('search');
        $id = $request->input('facility_id');
        $category_id = $request->input('Patient_category_id');
        $limit = 10;
        $sql = "select * from vw_shop_items where item_name like '%" . $search . "%' AND (item_category ='PROCEDURE' OR item_category ='SPECIALISED PROCEDURES' OR item_category ='MAJOR PROCEDURES' OR item_category='MINOR PROCEDURES' ) AND Patient_category_id ='" . $category_id . "' AND facility_id = '" . $id . "' limit " . $limit;
        $Patient = DB::select(DB::raw($sql));
        return $Patient;
    }

    public function getPatientServicesEm(Request $request)
    {
        $search = $request->input('search');
        $id = $request->input('facility_id');
        $category_id = $request->input('Patient_category_id');
        $limit = 10;
        $sql = "select * from vw_shop_items where item_name like '%" . $search . "%' AND item_category ='PHYSIOTHERAPY SERVICES' AND Patient_category_id ='" . $category_id . "' AND facility_id = '" . $id . "' limit " . $limit;
        $Patient = DB::select(DB::raw($sql));
        return $Patient;
    }

    public function getProceduresEm(Request $request)
    {
        $id = $request->input('facility_id');
        $category_id = $request->input('Patient_category_id');
        $limit = 30;
        $sql = "select * from vw_shop_items where (item_category ='PROCEDURE' OR item_category ='SPECIALISED PROCEDURES' OR item_category ='MAJOR PROCEDURES' OR item_category='MINOR PROCEDURES' ) AND Patient_category_id ='" . $category_id . "' AND facility_id = " . $id . " limit " . $limit;
        $Patient = DB::select(DB::raw($sql));
        return $Patient;
    }

    public function dosageCheckerEm(Request $request)
    {
        $Patient_id = $request->input('Patient_id');
        $item_id = $request->input('item_id');
        $sql = "SELECT * FROM `vw_previous_medications` WHERE `Patient_id`=" . $Patient_id . " AND `item_id`=" . $item_id . " AND (duration-days)>0 ORDER BY `start_date` DESC LIMIT 1 ";
        $data = DB::select(DB::raw($sql));
        return $data;

    }

    public function getVitalsEm(Request $request)
    {
        $Patient_id = $request->input('Patient_id');
        $date_attended = $request->input('date_attended');
        $sql = "SELECT   vital_sign,vital_sign_value FROM vw_vital_sign_output WHERE Patient_id=" . $Patient_id . " AND date_taken='" . $date_attended . "' group by vital_sign  LIMIT 10 ";
        $vital_time = DB::select(DB::raw($sql));
        return $vital_time;
    }

    public function postMedicinesEm(Request $request)
    {
        $dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $user_id = $data[0]->user_id;
        $date = date('Y-m-d');
        $account_number_id = $data[0]->account_number_id;
        $facility_id = $data[0]->facility_id;
        $data = Tbl_encounter_invoice::create(["account_number_id" => $account_number_id, "user_id" => $user_id, "facility_id" => $facility_id,]);
        $invoice_id = $data->id;
        foreach ($request->all() as $b) {
            $medData = Tbl_invoice_line::create(["invoice_id" => $invoice_id, "item_type_id" => $b['item_type_id'], "payment_filter" => $b['payment_filter'],
                "quantity" => number_format($b['quantity'], 2, '.', ''), "item_price_id" => $b['item_price_id'], "user_id" => $b['user_id'], "Patient_id" => $b['Patient_id'],
                "status_id" => $b['status_id'], "facility_id" => $b['facility_id'], "discount_by" => $b['user_id'],]);
        }

        foreach ($request->all() as $b) {
            $medData2 = Tbl_prescription::create(["item_id" => $b['item_id'], "Patient_id" => $b['Patient_id'],
                "prescriber_id" => $b['user_id'], "quantity" => $b['quantity'], "frequency" => $b['frequency'], "duration" => $b['duration'],
                "dose" => $b['dose'], "start_date" => $date, "instruction" => $b['instructions'], "out_of_stock" => $b['out_of_stock']]);
        }
        return $medData2;
    }

    public function getPrevMedicineEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $sql = "SELECT * FROM vw_previous_medications WHERE Patient_id = " . $id . " ORDER BY start_date DESC LIMIT 30";
        $data = DB::select(DB::raw($sql));
        return $data;
    }

    public function getPastMedicineEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $dt = $request->input('date_attended');
        $sql = "SELECT * FROM vw_previous_medications WHERE Patient_id = " . $id . " AND start_date = '" . $dt . "' ORDER BY start_date DESC LIMIT 30";
        $data = DB::select(DB::raw($sql));
        return $data;
    }

    public function getAllergiesEm(Request $request)
    {
        $Patient_id = $request->input('Patient_id');
        $date_attended = $request->input('date_attended');
        $sql = "select * from vw_allergies WHERE Patient_id = " . $Patient_id . " AND date_attended = '" . $date_attended . "' ";
        return DB::select(DB::raw($sql));
    }

    public function getPastProceduresEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $dt = $request->input('date_attended');
        $sql = "SELECT * FROM vw_previous_procedures WHERE Patient_id = " . $id . " AND created_at LIKE '" . $dt . "%' ";
        $data = DB::select(DB::raw($sql));
        return $data;
    }

    public function getPrevProceduresEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $sql = "SELECT * FROM vw_previous_procedures WHERE Patient_id = " . $id . " ORDER BY created_at DESC LIMIT 30  ";
        $data = DB::select(DB::raw($sql));
        return $data;
    }

    public function outOfStockMedicineEm(Request $request)
    {
        $date = date('Y-m-d');
        if (count($request->all()) > 0) {
            foreach ($request->all() as $b) {
                $medData2 = Tbl_prescription::create(["item_id" => $b['item_id'], "Patient_id" => $b['Patient_id'],
                    "prescriber_id" => $b['user_id'], "quantity" => $b['quantity'], "frequency" => $b['frequency'], "duration" => $b['duration'],
                    "dose" => $b['dose'], "start_date" => $date, "instruction" => $b['instructions'], "out_of_stock" => $b['out_of_stock']]);
            }
            return $medData2;
        }
    }

    public function postPatientProceduresEm(Request $request)
    {
        $dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $user_id = $data[0]->user_id;
        $account_number_id = $data[0]->account_number_id;
        $facility_id = $data[0]->facility_id;
        $data = Tbl_encounter_invoice::create(["account_number_id" => $account_number_id, "user_id" => $user_id, "facility_id" => $facility_id,]);
        $invoice_id = $data->id;
        foreach ($request->all() as $b) {
            $Data = Tbl_invoice_line::create(["payment_filter" => $b['payment_filter'], "invoice_id" => $invoice_id, "item_type_id" => $b['item_type_id'],
                "quantity" => number_format($b['quantity'], 2, '.', ''), "item_price_id" => $b['item_price_id'], "user_id" => $b['user_id'], "Patient_id" => $b['Patient_id'],
                "status_id" => $b['status_id'], "facility_id" => $b['facility_id'], "discount_by" => $b['user_id'], "discount" => 0,]);
        }

        foreach ($request->all() as $b) {
            $Data2 = Tbl_Patient_procedure::create(["item_id" => $b['item_id'], "Patient_id" => $b['Patient_id'],
                "user_id" => $b['user_id'], "visit_date_id" => $b['account_number_id'], "admission_id" => $b['admission_id'],]);
        }
        return $Data2;
    }

    public function postNotes(Request $request)
    {
        return Tbl_continuation_note::create($request->all());
    }

    public function getNotes(Request $request)
    {
        $id = $request->input('Patient_id');
        $limit = 10;
        $sql = "select * from vw_continuation_notes where Patient_id = " . $id . " limit " . $limit;
        $Patient = DB::select(DB::raw($sql));
        return $Patient;
    }

    public function prevDiagnosisEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $limit = 5;
        $sql = "select date_attended,Patient_id from vw_prev_diagnosis where Patient_id = " . $id . " GROUP BY date_attended limit " . $limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function prevFamilyHistoryEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $limit = 5;
        $sql = "select date_attended,Patient_id from vw_family_history where Patient_id = " . $id . " GROUP BY date_attended limit " . $limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function prevBirthHistoryEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $limit = 5;
        $sql = "select date_attended,Patient_id from vw_birth_history where Patient_id = " . $id . " GROUP BY date_attended limit " . $limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function prevHistoryExaminationsEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $limit = 5;
        $sql = "select date_attended,Patient_id from vw_history_examinations where Patient_id = " . $id . " GROUP BY date_attended limit " . $limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function prevObsGynEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $limit = 5;
        $sql = "select date_attended,Patient_id from vw_obs_gyn where Patient_id = " . $id . " GROUP BY date_attended limit " . $limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function prevRoSEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $limit = 5;
        $sql = "select date_attended,Patient_id from vw_review_of_systems where Patient_id = " . $id . " GROUP BY date_attended limit " . $limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function prevPhysicalExaminationsEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $limit = 5;
        $sql = "select date_attended,Patient_id from vw_physical_examinations where Patient_id = " . $id . " GROUP BY date_attended limit " . $limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function getFacilities(Request $request)
    {
        $id = $request->input('searchKey');
        $limit = 5;
        $sql = "select * from tbl_facilities where facility_name like '%" . $id . "%' limit " . $limit;
        $facility = DB::select(DB::raw($sql));
        return $facility;
    }

    public function postReferral(Request $request)
    {
        return Tbl_referral::create($request->all());
    }

    public function incomingReferrals(Request $request)
    {
        $id = $request->input('facility_id');
        $sql = "select sender_facility,sender_facility_id from vw_referrals where facility_id = " . $id . " AND referral_type =1 AND status=1 GROUP BY sender_facility ";
        $ref = DB::select(DB::raw($sql));
        return $ref;
    }

    public function getReferrals(Request $request)
    {
        $id = $request->input('sender_facility_id');
        $sql = "select * from vw_referrals where sender_facility_id = " . $id . " AND referral_type =1 AND status=1";
        $ref = DB::select(DB::raw($sql));
        return $ref;
    }

    public function updateReferals(Request $request)
    {
        $id = $request->input('Patient_id');
        $update = DB::table('tbl_referrals')
            ->where('Patient_id', $id)
            ->where('referral_type', 1)
            ->update([
                'status' => 0,
            ]);
        return $update;
    }

    public function getPrevDiagnosisEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_prev_diagnosis where Patient_id = " . $id . " AND date_attended = '" . $date . "' GROUP BY status ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function prevHistoryEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_history_examinations where Patient_id = " . $id . " AND date_attended = '" . $date . "' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function getPrevRosEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_review_of_systems where Patient_id = " . $id . " AND date_attended = '" . $date . "' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function getPrevBirthEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_birth_history where Patient_id = " . $id . " AND date_attended = '" . $date . "' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function getPrevObsEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_obs_gyn where Patient_id = " . $id . " AND date_attended = '" . $date . "' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function getPrevFamilyEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_family_history where Patient_id = " . $id . " AND date_attended = '" . $date . "' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function getPrevPhysicalEm(Request $request)
    {
        $id = $request->input('Patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_physical_examinations where Patient_id = " . $id . " AND date_attended = '" . $date . "' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function prevInvestigationResultsEm(Request $request)
    {
        $dept = $request->input('dept_id');
        $pt = $request->input('Patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_investigation_results where Patient_id = " . $pt . " AND dept_id = " . $dept . " AND date_attended = '" . $date . "' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function postDeceasedEm(Request $request)
    {
        $Patient_id = $request->input('Patient_id');
        $facility_id = $request->input('facility_id');
        $user_id = $request->input('user_id');
        if (PatientRegistration::duplicate('tbl_corpses', array('Patient_id', 'facility_id', 'user_id', "((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($Patient_id, $facility_id, $user_id)) == true) {
            return response()->json([
                'data' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
    }

    public function getBillList(Request $request)
    {
        return DB::table('vw_Patients_with_pending_bills')
            ->where('facility_id', $request->input('facility_id'))
            ->where('payment_filter', null)
            ->get();
    }

    public function cancelPatientBill(Request $request)
    {
       return DB::table('vw_Patients_with_pending_bills')
            ->where('facility_id', $request->input('facility_id'))
            ->get();
    }

    public function cancelBillItemEm(Request $request)
    {
        $id = $request->input('id');
        $user_id = $request->input('user_id');
        $update = DB::table('tbl_invoice_lines')
            ->where('id', $id)
            ->update([
                'status_id' => 3,
                'user_id' => $user_id,
            ]);
        return $update;

    }

    // specialized clinics
    public function getSpecialClinicsEm($facility_id)
    {
        return DB::table('vw_special_clinics')->where('facility_id', $facility_id)->get();
    }

    public function postToClinicsEm(Request $request)
    {
        if (count($request->all()) > 0) {
            return Tbl_clinic_instruction::create($request->all());
        }
    }

    public function Patient_emergence_registration(Request $request)
    {   // return $request->input('facility_id');

        $genders = array('Male', 'Female');
        $facility_id = $request->input('facility_id');
        $gender = $request->input('gender');
        $mobile_number = $request->input('mobile_number');
        $residence_id = $request->input('residence_id');
        $dob = $request->input('dob');
        $mobile_pattern = '#^[0][6-7][1-9][2-9][0-9]{6}$#';
        // return PatientRegistration::calculatePatientAge($request);

        $pattern = '#^(19[0-9][0-9])|(20[0-9][0-9])-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2][0-9])|(3[0-1]))$#';


        if (!in_array($gender, $genders)) {

            return response()->json([
                'data' => 'Please Select Gender!',
                'status' => '0'
            ]);
        } else if (0 === preg_match($mobile_pattern, $mobile_number) AND !empty($mobile_number)) {

            return response()->json([
                'data' => 'INVALID MOBILE NUMBER',
                'status' => '0'
            ]);
        } else if (!is_numeric($residence_id)) {

            return response()->json([
                'data' => 'PLEASE ENTER Patient RESIDENCE',
                'status' => '0'
            ]);
        } else if (0 === preg_match($pattern, $dob)) {

            return response()->json([
                'data' => 'Invalid Date of Birth',
                'status' => '0'
            ]);
        } else {

            return PatientRegistration::emergence_registration($request);

        }


    }


    public function urgency_registration(Request $request)
    {
        $genders = array('Male', 'Female');
        $facility_id = $request->input('facility_id');
        $gender = $request->input('gender');
        $mobile_number = $request->input('mobile_number');
        $residence_id = $request->input('residence_id');
        $dob = $request->input('dob');
        $mobile_pattern = '#^[0][6-7][1-9][2-9][0-9]{6}$#';
        // return PatientRegistration::calculatePatientAge($request);

        $pattern = '#^(19[0-9][0-9])|(20[0-9][0-9])-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2][0-9])|(3[0-1]))$#';


        if (!in_array($gender, $genders)) {

            return response()->json([
                'data' => 'Please Select Gender!',
                'status' => '0'
            ]);
        } else if (0 === preg_match($mobile_pattern, $mobile_number) AND !empty($mobile_number)) {

            return response()->json([
                'data' => 'INVALID MOBILE NUMBER',
                'status' => '0'
            ]);
        } else if (!is_numeric($residence_id)) {

            return response()->json([
                'data' => 'PLEASE ENTER Patient RESIDENCE',
                'status' => '0'
            ]);
        } else if (0 === preg_match($pattern, $dob)) {

            return response()->json([
                'data' => 'Invalid Date of Birth',
                'status' => '0'
            ]);
        } else {

            return PatientRegistration::urgency_registration($request);

        }


    }

//GET SEARCHED PatientS - EMERGENCY
    public function getSeachedPatientsEm(Request $request)
    {
        return PatientRegistration::seachForPatientsEm($request);

    }
//@SEARCHED PatientS-EMERGENCY


//GET RESIDENCES
    public function searchResidencesEm(Request $request)
    {
        $searchKey = $request->input('searchKey');
        $residences = DB::table('vw_residences')
            ->where('residence_name', 'like', '%' . $searchKey . '%')
            ->orWhere('council_name', 'like', '%' . $searchKey . '%')
            ->get();
        return $residences;
    }

//LAST VISIT

    public function printLastVisitEm(Request $request)
    {
        $facility_id = $request->input('facility_id');
        $Patient_id = $request->input('Patient_id');
        return PatientRegistration::getLastVisit($facility_id, $Patient_id);
    }

//    GET SERVICES
    public function searchPatientCategoryEm(Request $request)
    {
        $searchKey = $request->input('searchKey');
        $PatientCategory = DB::table('vw_registrar_services')
            ->where('Patient_category', 'like', '%' . $searchKey . '%')
            ->groupBy('Patient_category')
            ->get();
        return $PatientCategory;
    }

//    GET PRICED ITEMS

    public function getPricedItemsEm($Patient_category)
    {
        $getPricedItems = DB::table('vw_registrar_services')
            //->select('service_id','item_name','price','price_id','item_type_id')
            ->where('Patient_category', $Patient_category)
            ->get();
        return $getPricedItems;

    }

//    Services

    public function searchPatientServicesEm(Request $request)
    {
        return $searchKey = $request->all();
        $Patient_category = $request->input('Patient_category');

        $searchPatientServices = DB::table('vw_registrar_services')
            ->where('item_name', 'like', '%' . $searchKey . '%')
            ->where('Patient_category', $Patient_category)
            ->get();
        return $searchPatientServices;
    }

//GET SEARCHED PatientS CASUALTY

    public function getSearchedCasualty(Request $request)
    {
        return PatientRegistration::searchForCasualty($request);

    }

    //GET SEARCHED PatientS RESC ROOM

    public function getSearchedEmergency(Request $request)
    {
        return PatientRegistration::searchForEm($request);

    }

//    REGISTER VITAL SIGNS
    public function VitalRegister(Request $request)
    {
        $emptyTime = date("H:i:s");
        $date = date('Y-m-d h:i:s');
        $time = $request['time_attended'];
        $vital = $request['vital_data'];

        if ($time == "") {
            $time = $emptyTime;
        }
        if (!is_numeric($vital)) {
            return response()->json([
                'msg' => " Please Fill Number",
                'status' => 0
            ]);
        } else {

            $data = Tbl_vital_sign::create([
                'Patient_id' => $request['Patient_id'],
                'facility_id' => $request['facility_id'],
                'vital_sign' => $request['vital_sign'],
                'vital_data' => $request['vital_data'],
                'status' => $request['status'],
                'unit' => $request['unit'],
                'registered_by' => $request['registered_by'],
                'date_attended' => $date,
                'time_attended' => $time

            ]);
            return response()->json([
                'msg' => "Successfully Registered",
                'status' => 1
            ]);


        }


    }

//    DISPLAY VITAL SIGN PER Patient
    public function viewVitals($id)
    {
        return Tbl_vital_sign::where('Patient_id', $id)
            ->select('tbl_vital_signs.Body_weight', 'tbl_vital_signs.height_length',
                'tbl_vital_signs.Body_temperature', 'tbl_vital_signs.Systolic_pressure',
                'tbl_vital_signs.Diastolic_pressure', 'tbl_vital_signs.Oxygen_saturation',
                'tbl_vital_signs.Respiratory_rate', 'tbl_vital_signs.Pulse_rate',
                'tbl_vital_signs.time_attended', 'tbl_vital_signs.date_attended')
            ->get();
    }

    //DISPLAY Diastolic Pressure-Week
    public function viewDiastolicPressure($id)
    {
        return Tbl_vital_sign::where('Patient_id', $id)
            ->WhereNotNull('tbl_vital_signs.Diastolic_pressure')
            ->select(
                'tbl_vital_signs.Diastolic_pressure',
                'tbl_vital_signs.time_attended', 'tbl_vital_signs.date_attended')
            ->get();
    }


    //DISPLAY CURRENT VITALS

    public function recordedVitals($Patient_id)
    {


        $view = DB::table('vw_vital_sign_output')
            ->select(
                'vital_name',
                'vital_sign_value', 'si_unit')
            ->where('Patient_id', $Patient_id)
            ->whereRaw('Date(created_at) = CURDATE()')
            ->get();
        return $view;
    }

    public function viewPulseRate($id)
    {
        return Tbl_vital_sign::where('Patient_id', $id)
            ->WhereNotNull('tbl_vital_signs.Pulse_rate')
            ->select(
                'tbl_vital_signs.Pulse_rate',
                'tbl_vital_signs.time_attended', 'tbl_vital_signs.date_attended')
            ->get();
    }

//    DISPLAY VITALS
    public function getVitals()
    {
        $view = DB::table('tbl_vitals')
            ->select('vital_name', 'si_unit', 'id as vital_id')
            ->get();
        return $view;

    }

    public function getloadsClinic()
    {
        $view = DB::table('tbl_departments')
            ->select('department_name as department', 'id as dept_id')
            ->where('id', '>', 7)
            ->get();
        return $view;

    }

    //DISPLAY Temperature -Week
    public function viewTemperature($id)
    {
        return Tbl_vital_sign::where('Patient_id', $id)
            ->WhereNotNull('tbl_vital_signs.Body_temperature')
            ->select(
                'tbl_vital_signs.Body_temperature',
                'tbl_vital_signs.time_attended', 'tbl_vital_signs.date_attended')
            ->get();
    }

    //DISPLAY Systolic Pressure -Week
    public function viewSystolicPressure($id)
    {
        return Tbl_vital_sign::where('Patient_id', $id)
            ->WhereNotNull('tbl_vital_signs.Systolic_pressure')
            ->select(
                'tbl_vital_signs.Systolic_pressure',
                'tbl_vital_signs.time_attended', 'tbl_vital_signs.date_attended')
            ->get();
    }


    public function savePastSurgicalProcedure(Request $request)

    {
        foreach ($request->all() as $record) {
            $data = Tbl_emergence_visit::create([
                'mode_departure' => $record['item_name']
            ]);


        }
        return $data;
    }


    public function emergenceUsers($facility_id)
    {
        $date = date('Y-m-d');
        $status = 1;
        $sql = "SELECT * FROM `vw_opd_Patients` WHERE (`payment_status_id`=2 AND `visit_date`='" . $date . "' OR `payment_status_id`=1 AND `visit_date`='" . $date . "' AND `payment_filter` IS NOT NULL) AND facility_id =" . $facility_id . " AND status = " . $status . " GROUP BY Patient_id LIMIT 4 ";
        $Patient = DB::select(DB::raw($sql));
        return $Patient;
    }

    public function observationUsers($facility_id)
    {
        $date = date('Y-m-d');
        $status = 2;
        $sql = "SELECT * FROM `vw_opd_Patients` WHERE (`payment_status_id`=2 AND `visit_date`='" . $date . "' OR `payment_status_id`=1 AND `visit_date`='" . $date . "' AND `payment_filter` IS NOT NULL) AND facility_id =" . $facility_id . " AND status = " . $status . " GROUP BY Patient_id LIMIT 4 ";
        $Patient = DB::select(DB::raw($sql));
        return $Patient;
    }


    //SEND RES
    public function Resus(Request $request)
    {
        $id = $request['Patient_id'];
        $facility_id = $request['facility'];
        return DB::table('tbl_accounts_numbers')
            ->where('Patient_id', $id)
            ->where('facility_id', $facility_id)
            ->update([
                'status' => 1
            ]);

    }

    //SEND VISIT Summary
    public function SaveSummary(Request $request)
    {
        $time = date('Y-m-d h:i:s');
        $date = date('Y-m-d');
        $data = Tbl_emergence_visit::create([
            'Patient_id' => $request['Patient_id'],
            'mode_departure' => $request['mode_departure'],
            'emergency_arrival' => $request['emergency_arrival'],
            'referred_by' => $request['referred_by'],
            'chief_complaint' => $request['chief_complaint'],
            'disposition' => $request['disposition'],
            'condition_dispo' => $request['condition_dispo'],
            'acuity' => $request['acuity'],
            'arrival' => $request['arrival'],
            'dispo_decision' => $request['dispo_decision'],
            'departure' => $request['departure'],
            'emmergency_dispo' => $request['emmergency_dispo'],
            'rm' => $request['rm'],
            'time_left' => $request['time_left'],
            'triage_impression' => $request['triage_impression'],
            'visit_type' => $request['visit_type'],
            'registered_by' => $request['registered_by'],
            'facility_id' => $request['facility_id'],
            'time_attended' => $time,
            'date_attended' => $date,

        ]);
        return $data;


    }

    public function getAllInvPatientsEm(Request $request)
    {
        $pt = $request->input('facility_id');
        $sql = "select * from vw_investigation_results where facility_id = " . $pt . "  GROUP BY Patient_id ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
        return $investigation;
    }

    public function investigationListEm(Request $request)
    {
        $pt = $request->input('facility_id');
        $limit = 15;
        $sql = "select * from vw_investigation_results where facility_id = " . $pt . " GROUP BY Patient_id limit " . $limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
        return $investigation;
    }

    public function getAllOpdPatientsEm(Request $request)
    {
        $status = 1;
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_opd_Patients` WHERE (`payment_status_id`=2  OR `payment_status_id`=1 AND `payment_filter` IS NOT NULL) AND facility_id =" . $id . " AND status=" . $status . "
         GROUP BY Patient_id LIMIT 20 ";
        $Patient = DB::select(DB::raw($sql));
        return $Patient;
    }

    public function getOpdPatientsEm(Request $request)
    {
        $id = $request->input('facility_id');
        $status = 1;
        $sql = "SELECT * FROM `vw_opd_Patients` WHERE (`payment_status_id`=2  OR `payment_status_id`=1 AND `payment_filter` IS NOT NULL) AND facility_id =" . $id . " AND status=" . $status . "
         AND account_id NOT IN (SELECT visit_date_id FROM tbl_history_examinations) AND Patient_id NOT IN (SELECT Patient_id FROM tbl_corpse_admissions)  GROUP BY Patient_id LIMIT 20 ";
        $Patient = DB::select(DB::raw($sql));
        return $Patient;
    }


    public function getCurrentPatientAccountNumber($Patient_id, $facility_id)
    {


        $getCurrentPatientAccountNumber = Tbl_accounts_number::
        select('id as account_number_id', 'account_number')
            ->where('Patient_id', $Patient_id)
            ->where('facility_id', $facility_id)
            ->orderBy('id', 'DESC')
            ->first();
        return $getCurrentPatientAccountNumber;
    }


    public function enterEncounterEm(Request $request)
    {
        // some validation may be required..
        $facility_id = $request->input('facility_id');
        $Patient_id = $request->input('Patient_id');
        $price_id = $request->input('price_id');
        $service_id = $request->input('service_id');
        $item_type_id = $request->input('item_type_id');
        $user_id = $request->input('user_id');


        $quantity = 1;
        $status_id = 1;
        $payment_filter = null;

        if ($request->input('main_category_id') != 1) {
            $status_id = 1;
            $payment_filter = $request->input('payment_filter');
        }

        if (PatientRegistration::duplicate('tbl_Patients', array('id', "((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >= 60))"), array($Patient_id)) == true) {

            PatientRegistration::PatientAccountNumber($facility_id, $Patient_id);
        }
        $account = $this->getCurrentPatientAccountNumber($Patient_id, $facility_id);
        $account_number_id = $account->account_number_id;
        $bill_id = $request->input('bill_id');
        $main_category_id = $request->input('main_category_id');


        if (!is_numeric($service_id)) {

            return response()->json([
                'data' => 'PLEASE SELECT SERVICE FROM THE SUGESTION LIST',
                'status' => '0'
            ]);
        } else {

            if (PatientRegistration::duplicate('tbl_invoice_lines', array('Patient_id', 'item_type_id', 'quantity', "((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($Patient_id, $item_type_id, $quantity, '')) == true) {

                return response()->json([
                    'data' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                    'status' => '0'
                ]);
            } else {
                $payment_category = Tbl_bills_category::create(['Patient_id' => $Patient_id, 'account_id' => $account_number_id, 'user_id' => $user_id, 'bill_id' => $bill_id, 'main_category_id' => $main_category_id]);


                $encounter = Tbl_encounter_invoice::create(array('account_number_id' => $account_number_id, 'facility_id' => $facility_id, 'user_id' => $user_id));


                if ($encounter->save()) {
                    $invoice_line = Tbl_invoice_line::create(array('invoice_id' => $encounter->id, 'payment_filter' => $payment_filter,
                        'item_type_id' => $item_type_id, 'facility_id' => $facility_id, 'quantity' => number_format($quantity, 2, '.', ''), 'user_id' => $user_id, 'item_price_id' => $price_id, 'status_id' => $status_id, 'discount_by' => $user_id, 'Patient_id' => $Patient_id));

                    return dd($invoice_line);

                    if ($invoice_line->save()) {
                        return response()->json([
                            'data' => 'SUCCESSFULLY SAVED AND SUBMITED TO ENCOUNTER',
                            'account_number' => 'Account No: ' . $account_number,
                            'status' => '1'
                        ]);

                    }
                }
            }
        }


    }
//    Emergency Exemption


}