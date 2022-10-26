<?php

namespace App\Http\Controllers\RCH;

use App\classes\patientRegistration;
use App\ClinicalServices\Tbl_bills_category;
use App\Clinics\Tbl_clinic_instruction;
use App\Clinics\Tbl_clinic_specialist;
use App\Patient\Tbl_accounts_number;
use App\Patient\Tbl_patient;
use App\Payments\Tbl_encounter_invoice;
use App\Payments\Tbl_invoice_line;
use App\RCH\Tbl_child_deworm_register;
use App\RCH\Tbl_child_feeding;
use App\RCH\Tbl_child_growth_register;
use App\RCH\Tbl_child_hiv_expose_register;
use App\RCH\Tbl_child_mother_detail;
use App\RCH\Tbl_child_referral;
use App\RCH\Tbl_child_register;
use App\RCH\Tbl_child_subsidized_voucher_register;
use App\RCH\Tbl_child_vaccination_register;
use App\RCH\Tbl_child_vitamin_deworm_register;
use App\RCH\Tbl_previous_pregnancy_info;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use DateTime;
class Child_controller extends Controller
{
    //


    public function searchRchAllChild(Request $request)
    {
        $searchKey = $request->input('searchKey');
        $patients_returned=DB::table('tbl_child_registers')->where('client_name','like','%'.$searchKey.'%')

            ->orWhere('mobile_number','like','%'.$searchKey.'%')
            ->orWhere('serial_no','like','%'.$searchKey.'%')

            ->get();


        return $patients_returned;
    }



    public function Child_registration_update(Request $request)
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
        $father_name=$request->input('father_name');
        $mother_name=$request->input('mother_name');
        $midwife=$request->input('midwife');
        $weight=$request->input('weight');
        $delivery_place=$request->input('delivery_place');
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
            if(patientRegistration::duplicate('tbl_patients',['first_name','last_name','dob','residence_id', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                    [$first_name,$last_name,$dob,$residence_id])==true) {

                return response()->json([
                    'msg'=>'Duplication detected.....',
                    'status'=>0
                ]);
            }
            else{



                $data= patientRegistration::patient_registration($request);
                $client_id= $data[0][0]->id;

                $records=  patientRegistration::Child_Serial_Number($facility_id,$client_id,$user_id,$client_name,$dob,$residence_id,$father_name,$mobile_number,$mother_name,$midwife,$weight,$delivery_place,$gender);

                return response()->json([
                    'msg'=>'Successful data saved',
                    'status'=>1
                ]);
            }
        }


    }

    public function Child_mother_registration(Request $request)
    {
        $patient_id=$request['patient_id'];
        $tt_given=$request['tt_given'];
        $vvu_status=$request['vvu_status'];


        if($request['tt_given']==""){
            return response()->json([
                'msg'=>'Please Fill Mother TT Given Status',
                'status'=>0
            ]);
        }
        else if($request['vvu_status']==""){
            return response()->json([
                'msg'=>'Please Fill Mother VVU Status',
                'status'=>0
            ]);
        }

        else if(patientRegistration::duplicate('tbl_child_mother_details',['patient_id','tt_given','vvu_status','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$tt_given,$vvu_status])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }


        else   {
            $data=Tbl_child_mother_detail::create($request->all());
            return response()->json([
                'msg'=>'Successful saved',
                'status'=>1
            ]);
        }

    }

    public function hiv_ID_registration(Request $request)
    {
        
        $patient_id=$request['patient_id'];
        $heid_no=$request['heid_no'];



        if($request['heid_no']==""){
            return response()->json([
                'msg'=>'Please Fill HIV ID NUMBER',
                'status'=>0
            ]);
        }
         
        else if(patientRegistration::duplicate('tbl_child_hiv_expose_registers',['patient_id','heid_no','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$heid_no])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }


        else   {
            $data=Tbl_child_hiv_expose_register::create($request->all());
            return response()->json([
                'msg'=>'Successful saved',
                'status'=>1
            ]);
        }

    }



    public function Child_vaccination_registration(Request $request)
    {

        if($request['vaccination_id']==""){
            return response()->json([
                'msg'=>'Please choose Vaccination',
                'status'=>0
            ]);
        }
        
         
        else  if($request['date']==""){
            return response()->json([
                'msg'=>'Please Enter date for Selected Vaccination ',
                'status'=>0
            ]);
        } else  if($request['place']==""){
            return response()->json([
                'msg'=>'Please Enter Place of This Client ',
                'status'=>0
            ]);
        }



        $dup=Tbl_child_vaccination_register::where('vaccination_id',$request['vaccination_id'])
            ->where('patient_id',$request['patient_id'])->get();

        if(count($dup)>0){
            return response()->json([
                'msg'=>'Oops!.. Duplication or Double entry detected.. System detected that, you are entering a
                    Same data set more than once....',
                'status'=>0
            ]);
        }
        $data= Tbl_child_vaccination_register::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);




    }

    public function child_growth_registration(Request $request)
    {

        $patient_id=$request['patient_id'];
        $weight=$request['weight'];
        $weightz=$request['weightz'];
        $height=$request['height'];
        $date=$request['date'];
if($patient_id=='')
{
    return response()->json([
        'msg'=>'Choose Client first',
        'status'=>0
    ]);

}
        if($weight=='')
{
    return response()->json([
        'msg'=>'Fill Body Weight',
        'status'=>0
    ]);

}
        if($weightz=='')
{
    return response()->json([
        'msg'=>'Please make sure you have calculated Z_score before you submit',
        'status'=>0
    ]);

}
         if(patientRegistration::duplicate('tbl_child_growth_registers',['patient_id','height','weight','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$height,$weight])==true) {
             return response()->json([
                 'msg'=>'Duplication entry detected',
                 'status'=>0
             ]);
      
 }

else{
    $data= Tbl_child_growth_register::create($request->all());

    return response()->json([
        'msg'=>'Successful',
        'status'=>1
    ]);

}

    }


    public function child_deworm_registration(Request $request)
    {

        $patient_id=$request['client_id'];
        $deworm_given=$request['deworm_given'];
        $vitamin_given=$request['vitamin_given'];
       // $date=$request['date'];


        if( $request['deworm_given']=="" && $request['vitamin_given']==""){
            return response()->json([
                'msg'=>'Please Fill Either of the Options ',
                'status'=>0
            ]);
        }
        if( $request['date_attended']==""){
            return response()->json([
                'msg'=>'Please Fill Date Attended ',
                'status'=>0
            ]);
        }



 else if(patientRegistration::duplicate('tbl_child_vitamin_deworm_registers',['client_id','vitamin_given','deworm_given','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$vitamin_given,$deworm_given])==true) {

     return response()->json([
         'msg'=>'Duplication detected.....',
         'status'=>0
     ]);
 }

else{
    $data= Tbl_child_vitamin_deworm_register::create($request->all());

    return response()->json([
        'msg'=>'Successful',
        'status'=>1
    ]);

}

    }

 public function child_voucher_registration(Request $request)
    {

        $patient_id=$request['patient_id'];
        $voucher_given=$request['voucher_given'];
        $date=$request['date'];


        if( $request['voucher_given']==""){
            return response()->json([
                'msg'=>'Please Fill Options for Subsidized Voucher Given',
                'status'=>0
            ]);
        }
  if( $request['date']==""){
            return response()->json([
                'msg'=>'Please Fill Date for Subsidized Voucher Given',
                'status'=>0
            ]);
        }



 else if(patientRegistration::duplicate('tbl_child_subsidized_voucher_registers',['patient_id','voucher_given','date','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$voucher_given,$date])==true) {

     return response()->json([
         'msg'=>'Duplication detected.....',
         'status'=>0
     ]);
 }

else{
    $data= Tbl_child_subsidized_voucher_register::create($request->all());

    return response()->json([
        'msg'=>'Successful',
        'status'=>1
    ]);

}

    }

    public function child_feeding_registration(Request $request)
    {

        $patient_id=$request['patient_id'];
        $feeding_type=$request['feeding_type'];



        if( $request['feeding_type']==""){
            return response()->json([
                'msg'=>'Please Fill Options for Child Feeding',
                'status'=>0
            ]);
        }



 else if(patientRegistration::duplicate('tbl_child_feedings',['patient_id','feeding_type','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$feeding_type])==true) {

     return response()->json([
         'msg'=>'Duplication detected.....',
         'status'=>0
     ]);
 }

else{
    $data= Tbl_child_feeding::create($request->all());

    return response()->json([
        'msg'=>'Successful',
        'status'=>1
    ]);

}

    }

    public function child_referral_registration(Request $request)
    {
   $request->all();
        $patient_id=$request['patient_id'];
        $facility_id=$request['facility_id'];

        $request_id=$request['transfered_institution_id'];
         $patient_id_table=$request['patient_id_table'];
        $sender_clinic_id=$request['sender_clinic_id'];
        $reason=$request['reason'];
        $user_id=$request['user_id'];
        $item_type_id=$request['item_type_id'];
        $price_id=$request['price_id'];

          $getVisit=Tbl_accounts_number::where('patient_id',$patient_id_table)->orderBy('id','desc')->first();
          $category=Tbl_bills_category::where('patient_id',$patient_id_table)->orderBy('id','desc')->first();
$visit_id=$getVisit->id;
$bill_id=$category->bill_id;
        if($request['patient_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
        else  if( $request['transfered_institution_id']==""){
            return response()->json([
                'msg'=>'Please Choose Clinic to refer ',
                'status'=>0
            ]);
        }

        else  if(  $request['reason']==""){
            return response()->json([
                'msg'=>'Please Enter reasons for this referral ',
                'status'=>0
            ]);
        }
        else  if( $sender_clinic_id==$request_id){
            return response()->json([
                'msg'=>'You Can not Transfer Client To The same SAme   ',
                'msg'=>"You Can not Transfer Client To The  Same Department You're  Currently Working on It  ",
                'status'=>0
            ]);
        }
        

        else if(patientRegistration::duplicate('tbl_clinic_instructions',['dept_id','sender_clinic_id','visit_id',
                '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$request_id,$sender_clinic_id,$visit_id])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{

            $data= Tbl_clinic_instruction::create([
                    'visit_id'=>$visit_id,
                    'dept_id'=>$request_id,
                    'sender_clinic_id'=>$sender_clinic_id,
                    'summary'=>$reason,
                    'received'=>0,
                    'priority'=>'Routine',
                    'specialist_id'=>null,
                    'doctor_requesting_id'=>$user_id,
                    'consultation_id'=>$request['item_id'],
                ]
            );

            $payment_category =Tbl_bills_category::create(['patient_id'=>$patient_id,'account_id'=>$visit_id,'user_id'=>$user_id,'bill_id'=>$bill_id,'main_category_id'=>3]);



            $encounter =Tbl_encounter_invoice::create(array('account_number_id'=>$visit_id,'facility_id'=>$facility_id,'user_id'=>$user_id));


            if($encounter->save()){
                $invoice_line =Tbl_invoice_line::create(array('invoice_id'=>$encounter->id,'payment_filter'=>$bill_id,
                    'item_type_id'=>$item_type_id,'facility_id'=>$facility_id,'quantity'=>1,'user_id'=>$user_id,'item_price_id'=>$price_id,'status_id'=>1,'discount'=>0,'discount_by'=>$user_id,'patient_id'=>$patient_id));


                // return dd($invoice_line);


            }

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


    }




    public  function calculateWeek($client){
$data=json_decode(Tbl_previous_pregnancy_info::where('client_id',$client)->orderBy('id','desc')->take(1)->get());
        if(count($data)<1){
            return response()->json([
                'msg'=>'NO data Found',
                'status'=>0
            ]);
        }

         $last_visit=$data[0]->lnmp;
         $edd=$data[0]->edd;
        $today_date_time=date('Y-m-d');
        $bday = new DateTime($last_visit);
        $today = new DateTime($today_date_time);
       // $today = new DateTime($edd);
        $diff = $today->diff($bday);
       $week= floor($diff->d /7);
        $month= $diff->m;
        $year= $diff->y;
        $days= $diff->d %7;
        if($year>0){
            return response()->json([
                'msg'=>'No Pregnancy With Such Time Range Of Year',
                'status'=>0
            ]);
        }
        if($month>47){
            return response()->json([
                'msg'=>'No Pregnancy With Such Time Range Of Months',
                'status'=>0
            ]);
        }
        return response()->json([
             
            'week'=>($week +$month*4),

            'days'=>$days,
            'yeat'=>$year
        ]);
    }


    public function Child_incoming_referral(Request $request)
    {
        $request->all();

        $client_id = $request['patient_id'];
        $dept_id = $request['dept_id'];
        $visit_id = $request['visit_id'];

        $checkpatientExist = Tbl_child_register::where('patient_id', $client_id)->first();
        $checkrequestID = Tbl_clinic_specialist::where('dept_id',$dept_id)->orderBy('id','desc')->first();
        $request_id=$checkrequestID->id;
        $updateRequest=Tbl_clinic_instruction::where('dept_id',$request['dept_id'])->
        where('received',0)
            ->update(['received'=>1]);
        if (count($checkpatientExist) < 1) {


            $patient_info = Tbl_patient::where('id', $client_id)->first();
            $dob = $patient_info->dob;
            $gender = $patient_info->gender;
            $user_id = $patient_info->user_id;
            $client_name = $patient_info->first_name . ' ' . $patient_info->middle_name . ' ' . $patient_info->last_name;
            $facility_id = $patient_info->facility_id;
            $occupation_id = $patient_info->occupation_id;
            $residence_id = $patient_info->residence_id;
            $mother_name = 'unknown';
            $father_name = 'unknown';
            $mobile_number = 'unknown';
            $midwife = 'unknown';
            $weight =0;
            $delivery_place = 'unknown';

            $voucher_no = null;
            return   $records=   patientRegistration::Child_Serial_Number($facility_id,$client_id,$user_id,$client_name,$dob,$residence_id,$father_name,$mobile_number,$mother_name,$midwife,$weight,$delivery_place,$gender);


        }
        else{
            return $checkpatientExist;
        }
    }
}