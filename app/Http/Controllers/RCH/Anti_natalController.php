<?php

namespace App\Http\Controllers\RCH;

use App\classes\patientRegistration;
use App\Clinics\Tbl_clinic_instruction;
use App\Clinics\Tbl_clinic_specialist;
use App\Patient\Tbl_patient;
use App\RCH\Tbl_anti_natal_attendance;
use App\RCH\Tbl_anti_natal_councelling_area;
use App\RCH\Tbl_anti_natal_councelling_given;
use App\RCH\Tbl_anti_natal_deworm;
use App\RCH\Tbl_anti_natal_ifa;
use App\RCH\Tbl_anti_natal_ipt;
use App\RCH\Tbl_anti_natal_lab_test;
use App\RCH\Tbl_anti_natal_malaria;
use App\RCH\Tbl_anti_natal_partiner_pmtct;
use App\RCH\Tbl_anti_natal_partner_registers;
use App\RCH\Tbl_anti_natal_pmtct;
use App\RCH\Tbl_anti_natal_preventive;
use App\RCH\Tbl_anti_natal_reattendance;
use App\RCH\Tbl_anti_natal_referral;
use App\RCH\Tbl_anti_natal_register;
use App\RCH\Tbl_important_investigation;
use App\RCH\Tbl_partner_lab_test;
use App\RCH\Tbl_pregnancy_age;
use App\RCH\Tbl_previous_pregnancy_indicator;
use App\RCH\Tbl_previous_pregnancy_info;
use App\RCH\Tbl_std_investigation_partner_result;
use App\RCH\Tbl_std_investigation_result;
use App\RCH\Tbl_tt_vaccination;
use App\RCH\Tbl_vaccination_register;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;

class Anti_natalController extends Controller
{
    //


    public function anti_natal_registration(Request $request)
    {

        foreach($request->all() as $key=>$value)
            $request[$key] = strtoupper($value);
        $genders=array('MALE','FEMALE');

        $facility_id=$request->input('facility_id');
        $user_id=$request->input('user_id');
        $gender=$request->input('gender');
        $mobile_number=$request->input('mobile_number');
        $residence_id=$request->input('residence_id');
        $dob=$request->input('dob');
        $first_name=$request->input('first_name');
        $middle_name=$request->input('middle_name');
        $last_name=$request->input('last_name');
        $occupation_id=$request->input('occupation_id');
        $occupation_id1=$request->input('occupation_id1');
         $education=$request->input('education');
         $education1=$request->input('education1');
         $height=$request->input('height');
         $voucher_no=$request->input('voucher_no');
         $partner_name=$request->input('partner_name');
        $client_name=$first_name.' '.$middle_name .' '.$last_name;
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
        else if ($dob=='') {

            return response()->json([
                'msg' => 'Invalid Date of Birth',
                'status' => '0'
            ]);
        }
        else {
            if(patientRegistration::duplicate('tbl_patients',['residence_id','occupation_id','dob', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                    [$residence_id,$occupation_id,$dob])==true) {

                return response()->json([
                    'data'=>'Duplication detected.....',
                    'status'=>0
                ]);
            }
            else{



          $data= patientRegistration::patient_registration($request);
            $client_id= $data[0][0]->id;

            $records=  patientRegistration::Anti_Natal_Serial_Number($facility_id,$client_id,$user_id,$client_name,$dob,$voucher_no,
                $height,$occupation_id,$residence_id,$education);

            $client_ids=$records->id;
           Tbl_anti_natal_partner_registers::create([
                'facility_id'=>$facility_id,
                'user_id'=>$user_id,
                'client_id'=>$client_ids,
                'partner_name'=>$partner_name,
                'dob'=>$dob,
                'education'=>$education1,
                'occupation_id'=>$occupation_id1,
                ]
            );

                return response()->json([
                    'data'=>'Successful data saved',
                    'status'=>1
                ]);
        }
        }










    }


    public function searchRchpatient(Request $request)
    {
        $searchKey = $request->input('searchKey');
        $patients_returned=DB::table('tbl_anti_natal_registers')->where('client_name','like','%'.$searchKey.'%')

            ->orWhere('serial_no','like','%'.$searchKey.'%')
            ->orWhere('voucher_no','like','%'.$searchKey.'%')
             


            ->get();


        return $patients_returned;
    }

    public function SearchStds(Request $request)
    {
        $serch=$request['searchKey'];
     return DB::table('tbl_diagnosis_descriptions')
         ->where('description','like','%'.$serch.'%')
         ->orWhere('code','like','%'.$serch.'%')

         ->get()   ;
    }

    public function vaccination_registration(Request $request)
    {
        $vaccination_name=$request['vaccination_name'];
        $vaccination_type=$request['vaccination_type'];
       $dup=Tbl_vaccination_register::where('vaccination_name',$vaccination_name)
            ->where('vaccination_type',$vaccination_type)->first();
        if(count($dup)>0){
            return response()->json([
                'msg'=>'Oops!.. Duplication or Double entry detected.. System detected that, you are entering a
                    Same data set more than once....',
                'status'=>0
            ]);
        }
        else{
            $data= Tbl_vaccination_register::create($request->all());
            if($data){
                return response()->json([
                    'msg'=>'Successful',
                    'status'=>1
                ]);
            }
            else{
                return response()->json([
                    'msg'=>'Failed...',
                    'status'=>0
                ]);
            }
        }
        }

 public function tt_vaccination_registration(Request $request)
    {


        if($request['vaccination_id']==""){
            return response()->json([
                'msg'=>'Please choose Vaccination',
                'status'=>0
            ]);
        }
       else if($request['has_card']==""){
            return response()->json([
                'msg'=>'Please choose Option if patient has card or not',
                'status'=>0
            ]);
        }
       else if($request['patient_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
      else  if($request['vaccination_date']==""){
            return response()->json([
                'msg'=>'Please Enter date for Selected Vaccination ',
                'status'=>0
            ]);
        }

        

    $dup=Tbl_tt_vaccination::where('vaccination_id',$request['vaccination_id'])
            ->where('patient_id',$request['patient_id'])->get();

        if(count($dup)>0){
            return response()->json([
                'msg'=>'Oops!.. Duplication or Double entry detected.. System detected that, you are entering a
                    Same data set more than once....',
                'status'=>0
            ]);
        }
            $data= Tbl_tt_vaccination::create($request->all());

                return response()->json([
                    'msg'=>'Successful',
                    'status'=>1
                ]);




        }

public function pregnancy_age_registration(Request $request)
    {
$patient_id=$request['patient_id'];
  $week=$request['week'];
         if($request['patient_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
      else  if(!is_numeric($request['week'])){
          return response()->json([
              'msg'=>'Please Enter age of pregnancy  and should be a number',
              'status'=>0
          ]);
        } else  if(!is_numeric($week) && $week !=""){
            return response()->json([
                'msg'=>'Please Enter age of pregnancy',
                'status'=>0
            ]);
        }


      else if(patientRegistration::duplicate('tbl_pregnancy_ages',['patient_id','week','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$week])==true) {

          return response()->json([
              'msg'=>'Duplication detected.....',
              'status'=>0
          ]);
      }
        else{
            $data= Tbl_pregnancy_age::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);


        }
        }

public function reattendance_registration(Request $request)
    {
     

$patient_id=$request['client_id'];


        if(patientRegistration::duplicate('tbl_anti_natal_attendances',
              ['client_id', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id])==true) {

          return response()->json([
              'msg'=>'Duplication detected.....',
              'status'=>0
          ]);
      }
        else{
            $data= Tbl_anti_natal_attendance::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);


        }





        }


public function prev_preg_info_registration(Request $request)
    {
        $client_id= $request['client_id'];
        $number_of_pregnancy= $request['number_of_pregnancy'];
        $number_of_delivery= $request['number_of_delivery'];
        $number_alive_children= $request['number_alive_children'];
        $number_of_miscarriage= $request['number_of_miscarriage'];
         if($request['client_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
        else if($request['delivery_place']==""){
            return response()->json([
                'msg'=>'Please choose Suggested Place of Delivery',
                'status'=>0
            ]);
        }
      else  if(!is_numeric($request['number_of_pregnancy']) && ($request['number_of_pregnancy'] !="" or $request['number_of_pregnancy']=="")){
            return response()->json([
                'msg'=>'Please Enter Number of Pregnancy and should be a number ',
                'status'=>0
            ]);
        }

      else  if(!is_numeric($request['number_of_delivery']) &&  ($request['number_of_delivery'] !="" or $request['number_of_delivery']=="")){
            return response()->json([
                'msg'=>'Please Enter Number of delivery and should be a number',
                'status'=>0
            ]);
        }

 else  if(!is_numeric($request['number_alive_children']) &&  ($request['number_alive_children'] !="" or $request['number_alive_children']=="")){
            return response()->json([
                'msg'=>'Please Enter Number of Alive children and should be a number',
                'status'=>0
            ]);
        }
 else  if(!is_numeric($request['number_of_miscarriage']) &&  ($request['number_of_miscarriage'] !="" or $request['number_of_miscarriage']=="")){
            return response()->json([
                'msg'=>'Please Enter Number of Miscarriage and should be a number',
                'status'=>0
            ]);
        }





else if(patientRegistration::duplicate('tbl_previous_pregnancy_infos',['client_id','number_of_pregnancy','number_of_delivery','number_alive_children',
        'number_of_miscarriage', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$client_id,$number_of_pregnancy,$number_of_delivery,$number_alive_children,$number_of_miscarriage])==true) {

    return response()->json([
        'msg'=>'Duplication detected.....',
        'status'=>0
    ]);
}
        else{
            $data= Tbl_previous_pregnancy_info::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


        }

    public function pregnancy_indicator(Request $request)
    {
        $client_id= $request['client_id'];
        if(patientRegistration::duplicate('tbl_previous_pregnancy_indicators',['client_id' ,
                 '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$client_id])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            $data= Tbl_previous_pregnancy_indicator::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }

    }



public function pmtct_registration(Request $request)
    {
 
        $patient_id= $request['patient_id'];
        $vvu_infection=$request['vvu_infection'];
        $has_counsel_given=$request['has_counsel_given'];
        $counselling_date=$request['counselling_date'];
        $has_taken_vvu_test=$request['has_taken_vvu_test'];
        $date_of_test_taken=$request['date_of_test_taken'];
        $vvu_first_test_result=$request['vvu_first_test_result'];
        $counselling_after_vvu_test=$request['counselling_after_vvu_test'];
        $vvu_second_test_result=$request['vvu_second_test_result'];
        $baby_feeding_counsel_given=$request['baby_feeding_counsel_given'];
         if($request['patient_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
      else  if($request['vvu_infection']==""){
            return response()->json([
                'msg'=>'Please Fill Column for VVU Infection ',
                'status'=>0
            ]);
        }
      else  if($request['has_counsel_given']==""){
            return response()->json([
                'msg'=>'Please Fill Column for  Counselling Given ',
                'status'=>0
            ]);
        }
       else  if($request['has_taken_vvu_test']==""){
            return response()->json([
                'msg'=>'Please Fill Column for VVU test Taken',
                'status'=>0
            ]);
        } else  if($request['vvu_first_test_result']==""){
            return response()->json([
                'msg'=>'Please Fill Column for VVU First Test Result ',
                'status'=>0
            ]);
        }
      else  if($request['counselling_after_vvu_test']==""){
            return response()->json([
                'msg'=>'Please Fill Column for Counselling After VVU Test ',
                'status'=>0
            ]);
        }else  if($request['vvu_second_test_result']==""){
            return response()->json([
                'msg'=>'Please Fill Column for VVU Second Test Result  ',
                'status'=>0
            ]);
        }else  if($request['baby_feeding_counsel_given']==""){
            return response()->json([
                'msg'=>'Please Fill Column for Baby Feeding Counselling ',
                'status'=>0
            ]);
        }




else if(patientRegistration::duplicate('tbl_anti_natal_pmtcts',['patient_id','vvu_infection','has_counsel_given',
        'has_taken_vvu_test','vvu_first_test_result','counselling_after_vvu_test','vvu_second_test_result',
        'baby_feeding_counsel_given','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$vvu_infection,$has_counsel_given,
        $has_taken_vvu_test,$vvu_first_test_result,$counselling_after_vvu_test,$vvu_second_test_result,$baby_feeding_counsel_given])==true) {

    return response()->json([
        'msg'=>'Duplication detected.....',
        'status'=>0
    ]);
}
        else{
            $data= Tbl_anti_natal_pmtct::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


        }


    public function pmtct_partner_registration(Request $request)
    {

        $patient_id= $request['patient_id'];
        $vvu_infection=$request['vvu_infection'];
        $has_counsel_given=$request['has_counsel_given'];
        $counselling_date=$request['counselling_date'];
        $has_taken_vvu_test=$request['has_taken_vvu_test'];
        $date_of_test_taken=$request['date_of_test_taken'];
        $vvu_first_test_result=$request['vvu_first_test_result'];
        $counselling_after_vvu_test=$request['counselling_after_vvu_test'];
        $vvu_second_test_result=$request['vvu_second_test_result'];

         if($request['patient_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
      else  if($request['vvu_infection']==""){
            return response()->json([
                'msg'=>'Please Fill Column for VVU Infection ',
                'status'=>0
            ]);
        }
      else  if($request['has_counsel_given']==""){
            return response()->json([
                'msg'=>'Please Fill Column for  Counselling Given ',
                'status'=>0
            ]);
        }
       else  if($request['has_taken_vvu_test']==""){
            return response()->json([
                'msg'=>'Please Fill Column for VVU test Taken',
                'status'=>0
            ]);
        } else  if($request['vvu_first_test_result']==""){
            return response()->json([
                'msg'=>'Please Fill Column for VVU First Test Result ',
                'status'=>0
            ]);
        }
      else  if($request['counselling_after_vvu_test']==""){
            return response()->json([
                'msg'=>'Please Fill Column for Counselling After VVU Test ',
                'status'=>0
            ]);
        }else  if($request['vvu_second_test_result']==""){
            return response()->json([
                'msg'=>'Please Fill Column for VVU Second Test Result  ',
                'status'=>0
            ]);
        }




else if(patientRegistration::duplicate('tbl_anti_natal_partiner_pmtcts',['patient_id','vvu_infection','has_counsel_given',
        'has_taken_vvu_test','vvu_first_test_result','counselling_after_vvu_test','vvu_second_test_result',
        '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$vvu_infection,$has_counsel_given,
        $has_taken_vvu_test,$vvu_first_test_result,$counselling_after_vvu_test,$vvu_second_test_result])==true) {

    return response()->json([
        'msg'=>'Duplication detected.....',
        'status'=>0
    ]);
}
        else{
            $data= Tbl_anti_natal_partiner_pmtct::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


        }


public function anti_natal_lab_results(Request $request)
    {

        $patient_id=$request['client_id'];




  if(patientRegistration::duplicate('tbl_anti_natal_lab_tests',['client_id', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
        [$patient_id])==true) {

    return response()->json([
        'msg'=>'Duplication detected.....',
        'status'=>0
    ]);
}
        else{
            $data= Tbl_anti_natal_lab_test::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


        }

public function partner_lab_results(Request $request)
    {

        $patient_id=$request['client_id'];




  if(patientRegistration::duplicate('tbl_partner_lab_tests',['client_id', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
        [$patient_id])==true) {

    return response()->json([
        'msg'=>'Duplication detected.....',
        'status'=>0
    ]);
}
        else{
            $data= Tbl_partner_lab_test::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


        }


    public function preventives_registration(Request $request)
    {
        $patient_id=$request['client_id'];


  if(patientRegistration::duplicate('tbl_anti_natal_preventives',['client_id', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
        [$patient_id])==true) {

    return response()->json([
        'msg'=>'Duplication detected.....',
        'status'=>0
    ]);
}
        else{
            $data= Tbl_anti_natal_preventive::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


        }


    public function ipt_registration(Request $request)
    {
        $patient_id=$request['patient_id'];
        $ipt=$request['ipt'];
        $ipt_date=$request['ipt_date'];

         if($request['patient_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
      else  if(  $request['ipt']==""){
            return response()->json([
                'msg'=>'Please Choose IPT Number First ',
                'status'=>0
            ]);
        } else  if(  $request['ipt_date']==""){
            return response()->json([
                'msg'=>'Please Fill Date for '.$request['ipt'],
                'status'=>0
            ]);
        }




else if(patientRegistration::duplicate('tbl_anti_natal_ipts',['patient_id','ipt','ipt_date',
        '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
        [$patient_id,$ipt,$ipt_date])==true) {

    return response()->json([
        'msg'=>'Duplication detected.....',
        'status'=>0
    ]);
}
        else{
            $data= Tbl_anti_natal_ipt::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


        }


  public function counselling_area_registration(Request $request)
    {
          if(patientRegistration::duplicate('tbl_anti_natal_councelling_areas',['description',
        '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
        [$request['description']])==true) {

    return response()->json([
        'msg'=>'Duplication detected.....',
        'status'=>0
    ]);
}
        else{
            $data= Tbl_anti_natal_councelling_area ::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


        }

    public function councelling_lists()
    {
      return Tbl_anti_natal_councelling_area::get();  
    }

public function counselling_registration(Request $request)
    {
            
 foreach ($request->all() as $data){
     $data= Tbl_anti_natal_councelling_given::create([
         'description_id'=>$data['description_id'],
         'status'=>$data['status'],
         'client_id'=>$data['client_id'],
         'facility_id'=>$data['facility_id'],
         'user_id'=>$data['user_id'],
     ]);

 }

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
      


        }



    public function std_registration(Request $request)
    {
        $patient_id=$request['patient_id'];
        $facility_id=$request['facility_id'];
        $user_id=$request['user_id'];

        $std_id=$request['std_id'];
        $treated=$request['treated'];
        $result=$request['result'];
        $p_result=$request['p_result'];
        $p_treated=$request['p_treated'];



         if($request['patient_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
      else  if(  $request['std_id']==""){
            return response()->json([
                'msg'=>'Please Choose STD Diagnosis  ',
                'status'=>0
            ]);
        }
else  if(  $request['result']==""){
            return response()->json([
                'msg'=>'Please Choose Results for Selected Diagnosis ',
                'status'=>0
            ]);
        }




else if(patientRegistration::duplicate('tbl_std_investigation_results',['patient_id','std_id','result',
        '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
        [$patient_id,$std_id,$result ])==true) {

    return response()->json([
        'msg'=>'Duplication detected.....',
        'status'=>0
    ]);
}
        else{
            $data= Tbl_std_investigation_result::create([
                'patient_id'=>$patient_id,
                'facility_id'=>$facility_id,
                'user_id'=>$user_id,
                'std_id'=>$std_id,
                'std_id'=>$std_id,
                'result'=>$result,
                'treated'=>$treated,
            ]);
            if($p_result !='' && $p_treated !='')
            {

                $data= Tbl_std_investigation_partner_result::create([
                    'patient_id'=>$patient_id,
                    'facility_id'=>$facility_id,
                    'user_id'=>$user_id,
                    'std_id'=>$std_id,
                    'std_id'=>$std_id,
                    'p_result'=>$p_result,
                    'p_treated'=>$p_treated,
                ]);
                return response()->json([
                    'msg'=>'Successful',
                    'status'=>1
                ]);
            }

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


        }

    public function referral_registration(Request $request)
    {
        $patient_id=$request['patient_id'];

        $transfered_institution_id=$request['transfered_institution_id'];
        $reason=$request['reason'];
        $date=$request['date'];


         if($request['patient_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
      else  if(  $request['transfered_institution_id']==""){
            return response()->json([
                'msg'=>'Please Choose Institution to refer ',
                'status'=>0
            ]);
        }
else  if(  $request['date']==""){
            return response()->json([
                'msg'=>'Please Enter Date for Referral ',
                'status'=>0
            ]);
        }
else  if(  $request['reason']==""){
            return response()->json([
                'msg'=>'Please Enter reasons for this referral ',
                'status'=>0
            ]);
        }




else if(patientRegistration::duplicate('tbl_anti_natal_referrals',['patient_id','transfered_institution_id','date','reason',
        '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
        [$patient_id,$transfered_institution_id,$date,$reason ])==true) {

    return response()->json([
        'msg'=>'Duplication detected.....',
        'status'=>0
    ]);
}
        else{
            $data= Tbl_anti_natal_referral::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


        }



    public function vaccination_list()
    {
       return $dup=Tbl_vaccination_register::get();
    }

    public function vaccination_update(Request $request)
    {
     $id=$request['id'] ;
        $vaccination_name=$request['vaccination_name'];
        $vaccination_type=$request['vaccination_type'];
        $data=Tbl_vaccination_register::where('id',$id)->update([
            'vaccination_name'=>$vaccination_name,
            'vaccination_type'=>$vaccination_type,
        ]);
        return response()->json([
            'msg'=>'Successful Updated',
            'status'=>1
        ]);
}

    public function Anti_incoming_referral(Request $request)
    {
      // $request->all();

         $client_id = $request['patient_id'];
        $dept_id = $request['dept_id'];
        $visit_id = $request['visit_id'];

       $checkpatientExist = Tbl_anti_natal_register::where('client_id', $client_id)->first();

      $updateRequest=Tbl_clinic_instruction::where('dept_id',$request['dept_id'])
          ->where('visit_id',$request['visit_id'])
          ->where('received',0)
            ->update(['received'=>1]);
        if (count($checkpatientExist) < 1) {


            $patient_info = Tbl_patient::where('id', $client_id)->first();
            $dob = $patient_info->dob;
            $user_id = $patient_info->user_id;
            $client_name = $patient_info->first_name . ' ' . $patient_info->middle_name . ' ' . $patient_info->last_name;
            $facility_id = $patient_info->facility_id;
            $occupation_id = null;
            $residence_id = $patient_info->residence_id;
            $education = 'unknown';
            $height = 9;
            $voucher_no = null;
          return  $records = patientRegistration::Anti_Natal_Serial_Number($facility_id, $client_id, $user_id, $client_name, $dob, $voucher_no,
                $height, $occupation_id, $residence_id, $education);

        }
        else{
            return $checkpatientExist;
        }
    }





}