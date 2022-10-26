<?php

namespace App\Http\Controllers\RCH;

use App\classes\patientRegistration;
use App\Clinics\Tbl_clinic_instruction;
use App\Clinics\Tbl_clinic_specialist;
use App\Patient\Tbl_patient;
use App\RCH\Tbl_condom;
use App\RCH\Tbl_family_planning_attendance;
use App\RCH\Tbl_family_planning_attendance_register;
use App\RCH\Tbl_family_planning_method_list;
use App\RCH\Tbl_family_planning_method_register;
use App\RCH\Tbl_family_planning_pregnancy_history;
use App\RCH\Tbl_family_planning_previous_health;
use App\RCH\Tbl_family_planning_referral;
use App\RCH\Tbl_family_planning_register;
use App\RCH\Tbl_family_planning_vvu_status;
use App\RCH\Tbl_fplanning_breast_cancer_investigation;
use App\RCH\Tbl_fplanning_cervix_cancer_investigation;
use App\RCH\Tbl_fplanning_lab_investigation;
use App\RCH\Tbl_fplanning_pitc;
use App\RCH\Tbl_fplanning_placenta_cancer_investigation;
use App\RCH\Tbl_fplanning_previous_menstral;
use App\RCH\Tbl_fplanning_previous_pregnancy_result;
use App\RCH\Tbl_fplanning_speculam_investigation;
use App\RCH\Tbl_fplanning_stomach_leg_investigation;
use App\RCH\Tbl_fplanning_viginal_by_arm_investigation;
use App\RCH\Tbl_rch_general_recomendation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Family_planningController extends Controller
{
    //






    public function family_planning_registration(Request $request)
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

                $records=  patientRegistration::Family_planning_Number($facility_id,$client_id,$user_id,$client_name,$dob,$occupation_id,$residence_id,$education);
 
                return response()->json([
                    'data'=>'Successful data saved',
                    'status'=>1
                ]);
            }
        }

 
    }

    public function search_family_planing_clients(Request $request)
    {
        $searchKey = $request->input('searchKey');
        $patients_returned=DB::table('tbl_family_planning_registers')->where('client_name','like','%'.$searchKey.'%')

            ->orWhere('serial_no','like','%'.$searchKey.'%')
            
            ->get();


        return $patients_returned;
    }

    public function faimily_birth_registration(Request $request)
    {

        $patient_id=$request['patient_id'];
        $pregnancy_number=$request['pregnancy_number'];
        $miscarriage_number=$request['miscarriage_number'];
        $alive_born_number=$request['alive_born_number'];
        $child_alive_number=$request['child_alive_number'];
        $msb_number=$request['msb_number'];
      if(!is_numeric($request['pregnancy_number']) && $request['pregnancy_number']!=""){
        return response()->json([
            'msg'=>'Please Enter Number Of Pregnancy and should a number',
            'status'=>0
        ]);
    } else  if(!is_numeric($request['miscarriage_number']) && $request['miscarriage_number']!=""){
        return response()->json([
            'msg'=>'Please Enter Number Of Miscarriage and should a number',
            'status'=>0
        ]);
    }else  if(!is_numeric($request['msb_number']) && $request['msb_number']!=""){
        return response()->json([
            'msg'=>'Please Enter Number Of Maslated and should a number',
            'status'=>0
        ]);
    }else  if(!is_numeric($request['alive_born_number']) && $request['alive_born_number']!=""){
        return response()->json([
            'msg'=>'Please Enter Number Of Alive Newborn and should a number',
            'status'=>0
        ]);
    }else  if(!is_numeric($child_alive_number) && $child_alive_number !=""){
        return response()->json([
            'msg'=>'Please Enter Number Of Alive Children and should a number',
            'status'=>0
        ]);
    }

  else if(patientRegistration::duplicate('tbl_family_planning_pregnancy_histories',['patient_id','pregnancy_number','miscarriage_number','child_alive_number','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$pregnancy_number,$miscarriage_number,$child_alive_number])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_family_planning_pregnancy_history::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}
public function faimily_health_registration(Request $request)
    {

        $patient_id=$request['client_id'];

      if($request['headache_number']){
        return response()->json([
            'msg'=>'Please Fill All Required Columns',
            'status'=>0
        ]);
    }

  else if(patientRegistration::duplicate('tbl_family_planning_previous_healths',['client_id','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id ])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_family_planning_previous_health::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}
public function faimily_delivery_result_registration(Request $request)
    {

        $patient_id=$request['client_id'];

      if($request['client_id']==''){
        return response()->json([
            'msg'=>'Please Fill All Required Columns',
            'status'=>0
        ]);
    }

//  else if(patientRegistration::duplicate('tbl_fplanning_previous_pregnancy_results',['client_id','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id ])==true) {
//
//        return response()->json([
//            'msg'=>'Duplication detected.....',
//            'status'=>0
//        ]);
//    }


    else   {
        $data=Tbl_fplanning_previous_pregnancy_result::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}
    public function faimily_menstral_result_registration(Request $request)
    {

        $patient_id=$request['client_id'];
        $lnmp=$request['lnmp'];
        $menstral_day=$request['menstral_day'];
        $bleeding_quantity=$request['bleeding_quantity'];

      if($request['client_id']==''){
        return response()->json([
            'msg'=>'Please Fill All Required Columns',
            'status'=>0
        ]);
    }

  else if(patientRegistration::duplicate('tbl_fplanning_previous_menstrals',['client_id','lnmp','bleeding_quantity','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$lnmp,$bleeding_quantity ])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_fplanning_previous_menstral::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}
    public function faimily_iptc_registration(Request $request)
    {

        $patient_id=$request['client_id'];
        $iptc=$request['pitc'];
        $iptc_result=$request['pitc_result'];


      if($request['client_id']==''){
        return response()->json([
            'msg'=>'Please Fill All Required Columns',
            'status'=>0
        ]);
    }
        else if($request['pitc']==''){
        return response()->json([
            'msg'=>'Please Fill PITC Columns',
            'status'=>0
        ]);
    }
        else if($request['pitc_result']==''){
        return response()->json([
            'msg'=>'Please Fill PITC Result Columns',
            'status'=>0
        ]);
    } else if($request['result_date']==''){
        return response()->json([
            'msg'=>'Please Fill  Date of PITC ResultColumns',
            'status'=>0
        ]);
    }

  else if(patientRegistration::duplicate('tbl_fplanning_pitcs',['client_id','pitc','pitc_result','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$iptc,$iptc_result ])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_fplanning_pitc::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}

    public function faimily_cancer_registration(Request $request)
    {

        $patient_id=$request['client_id'];
        $placenta_status=$request['placenta_status'];
        $suspected_cancer=$request['suspected_cancer'];
        $cryotherapy=$request['cryotherapy'];



      if($request['client_id']==''){
        return response()->json([
            'msg'=>'Please Fill All Required Columns',
            'status'=>0
        ]);
    }
        else if($request['placenta_status']==''){
        return response()->json([
            'msg'=>'Please Fill Placenta Status Columns',
            'status'=>0
        ]);
    }


  else if(patientRegistration::duplicate('tbl_fplanning_placenta_cancer_investigations',['client_id','placenta_status','suspected_cancer','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$placenta_status,$suspected_cancer ])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_fplanning_placenta_cancer_investigation::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}
 public function faimily_lab_test_registration(Request $request)
    {

        $patient_id=$request['client_id'];
        $urine=$request['urine'];
        $sugar=$request['sugar'];
        $albumin=$request['albumin'];




      if($request['client_id']==''){
        return response()->json([
            'msg'=>'Please Fill All Required Columns',
            'status'=>0
        ]);
    }
        else if($request['urine']==''){
        return response()->json([
            'msg'=>'Please Fill Urine  Columns',
            'status'=>0
        ]);
    } else if($request['albumin']==''){
        return response()->json([
            'msg'=>'Please Fill Albumin  Columns',
            'status'=>0
        ]);
    }
        else if($request['sugar']==''){
        return response()->json([
            'msg'=>'Please Fill Urine Sugar  Columns',
            'status'=>0
        ]);
    }


  else if(patientRegistration::duplicate('tbl_fplanning_lab_investigations',['client_id','urine','sugar','albumin','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$urine,$sugar,$albumin])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_fplanning_lab_investigation::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}
public function fplanning_stomach_leg_investigation(Request $request)
    {

        $patient_id=$request['client_id'];
        $liver_inflammation=$request['liver_inflammation'];
        $leg_inflammation=$request['leg_inflammation'];
        $vericose_vein=$request['vericose_vein'];




      if($request['client_id']==''){
        return response()->json([
            'msg'=>'Please Fill All Required Columns',
            'status'=>0
        ]);
    }
        else if($request['liver_inflammation']==''){
        return response()->json([
            'msg'=>'Please Fill all  Columns',
            'status'=>0
        ]);
    }
        else if($request['leg_inflammation']==''){
        return response()->json([
            'msg'=>'Please Fill all  Columns',
            'status'=>0
        ]);
    }
        else if($request['vericose_vein']==''){
        return response()->json([
            'msg'=>'Please Fill Vericose Vein  Columns',
            'status'=>0
        ]);
    }


  else if(patientRegistration::duplicate('tbl_fplanning_stomach_leg_investigations',['client_id','leg_inflammation','liver_inflammation','vericose_vein','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$leg_inflammation,$liver_inflammation,$vericose_vein])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_fplanning_stomach_leg_investigation::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}

public function fplanning_viginal_by_arm_investigations(Request $request)
    {

        $patient_id=$request['client_id'];
        $placenta_size=$request['placenta_size'];
        $placenta_layout=$request['placenta_layout'];
        $adnexa=$request['adnexa'];




      if($request['client_id']==''){
        return response()->json([
            'msg'=>'Please Fill All Required Columns',
            'status'=>0
        ]);
    }
        else if($request['placenta_size']==''){
        return response()->json([
            'msg'=>'Please Fill placenta size  Columns',
            'status'=>0
        ]);
    }
        else if($request['placenta_layout']==''){
        return response()->json([
            'msg'=>'Please Fill placenta layout  Columns',
            'status'=>0
        ]);
    }
        else if($request['adnexa']==''){
        return response()->json([
            'msg'=>'Please Fill adnexa  Columns',
            'status'=>0
        ]);
    }


  else if(patientRegistration::duplicate('tbl_fplanning_viginal_by_arm_investigations',['client_id','placenta_size','placenta_layout','adnexa','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$placenta_size,$placenta_layout,$adnexa])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_fplanning_viginal_by_arm_investigation::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}
    public function fplanning_attendances(Request $request)
    {

        $patient_id=$request['client_id'];
        $weight=$request['weight'];
        $bp=$request['bp'];
        $lnmp=$request['lnmp'];
        $complains=$request['complains'];
        $comment_treatment=$request['comment_treatment'];
        $followup_date=$request['followup_date'];




      if($request['client_id']==''){
        return response()->json([
            'msg'=>'Please Fill All Required Columns',
            'status'=>0
        ]);
    }
        else if($request['weight']==''){
        return response()->json([
            'msg'=>'Please Fill Body Weight  Column',
            'status'=>0
        ]);
    } else if($request['bp']==''){
        return response()->json([
            'msg'=>'Please Fill BP  Column',
            'status'=>0
        ]);
    } else if($request['lnmp']==''){
        return response()->json([
            'msg'=>'Please Fill L.N.M.P  Column',
            'status'=>0
        ]);
    }


  else if(patientRegistration::duplicate('tbl_family_planning_attendances',['client_id','weight','bp','lnmp','followup_date','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$weight,$bp,$lnmp,$followup_date])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_family_planning_attendance::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}
    public function fplanning_viginal_by_spec_investigations(Request $request)
    {

        $patient_id=$request['client_id'];

      if($request['client_id']==''){
        return response()->json([
            'msg'=>'Please Fill All Required Columns',
            'status'=>0
        ]);
    }


  else if(patientRegistration::duplicate('tbl_fplanning_speculam_investigations',['client_id','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_fplanning_speculam_investigation::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}


    public function planning_method_list_registration(Request $request)
    {

        $planning_method=$request['planning_method'];
      if( $request['planning_method']==""){
        return response()->json([
            'msg'=>'Please Enter Planning Method Name',
            'status'=>0
        ]);
    }

  else if(patientRegistration::duplicate('tbl_family_planning_method_lists',['planning_method','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=5000))'],[$planning_method])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_family_planning_method_list::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}

    public function breast_cancer_registration(Request $request)
    {

        $patient_id=$request['patient_id'];
        $bunje=$request['bunje'];
        $wound=$request['wound'];
        $breast_bleeding=$request['breast_bleeding'];
        $breast_abscess=$request['breast_abscess'];
        $others=$request['others'];
      if( $request['bunje']==""){
        return response()->json([
            'msg'=>'Please Fill option For Bunje',
            'status'=>0
        ]);
    }else if( $request['wound']==""){
        return response()->json([
            'msg'=>'Please Fill option For Wound',
            'status'=>0
        ]);
    }else if( $request['breast_bleeding']==""){
        return response()->json([
            'msg'=>'Please Fill option For Breast Bleeding',
            'status'=>0
        ]);
    }else if( $request['breast_abscess']==""){
        return response()->json([
            'msg'=>'Please Fill option For Breast Abscess',
            'status'=>0
        ]);
    }

  else if(patientRegistration::duplicate('tbl_fplanning_breast_cancer_investigations',['patient_id','bunje','wound','breast_bleeding','breast_abscess','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=5000))'],[$patient_id,$bunje,$wound,$breast_bleeding,$breast_abscess])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_fplanning_breast_cancer_investigation::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}

    public function cervix_cancer_registration(Request $request)
    {

        $patient_id=$request['patient_id'];
        $virginal_discharge=$request['virginal_discharge'];
        $cervix_scratching=$request['cervix_scratching'];
        $cervix_swelling=$request['cervix_swelling'];
        $virginal_bleeding=$request['virginal_bleeding'];
        $others=$request['others'];
      if( $request['virginal_discharge']==""){
        return response()->json([
            'msg'=>'Please Fill option For virginal discharge',
            'status'=>0
        ]);
    }else if( $request['cervix_scratching']==""){
        return response()->json([
            'msg'=>'Please Fill option For cervix scratching',
            'status'=>0
        ]);
    }else if( $request['cervix_swelling']==""){
        return response()->json([
            'msg'=>'Please Fill option For cervix swelling',
            'status'=>0
        ]);
    }else if( $request['virginal_bleeding']==""){
        return response()->json([
            'msg'=>'Please Fill option For virginal bleeding',
            'status'=>0
        ]);
    }

  else if(patientRegistration::duplicate('tbl_fplanning_cervix_cancer_investigations',['patient_id','virginal_discharge','cervix_scratching','cervix_swelling','virginal_bleeding','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=5000))'],[$patient_id,$virginal_discharge,$cervix_scratching,$cervix_swelling,$virginal_bleeding])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_fplanning_cervix_cancer_investigation::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
} 
    
    public function planning_vvu_registration(Request $request)
    {

        $patient_id=$request['patient_id'];
        $current_vvu_status=$request['current_vvu_status'];
        $mother_vvu_status=$request['mother_vvu_status'];
        $partner_vvu_status=$request['partner_vvu_status'];

      if( $request['current_vvu_status']==""){
        return response()->json([
            'msg'=>'Please Fill option For VVU Status',
            'status'=>0
        ]);
    }else if( $request['mother_vvu_status']==""){
        return response()->json([
            'msg'=>'Please Fill option For Mother VVU Status',
            'status'=>0
        ]);
    }else if( $request['partner_vvu_status']==""){
        return response()->json([
            'msg'=>'Please Fill option For Partner VVU Status',
            'status'=>0
        ]);
    }
  else if(patientRegistration::duplicate('tbl_family_planning_vvu_statuses',['patient_id','current_vvu_status','mother_vvu_status','partner_vvu_status','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=5000))'],[$patient_id,$current_vvu_status,$mother_vvu_status,$partner_vvu_status])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_family_planning_vvu_status::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}
    public function mother_planning_method_registration(Request $request)
    {

        $patient_id=$request['patient_id'];
        $method_id=$request['method_id'];
        $event_driven=$request['event_driven'];
        $data=Tbl_family_planning_method_register::where('patient_id',$patient_id)
            ->where('method_id',$method_id)
            ->where('status',1)->get();
      if( $request['method_id']==""){
        return response()->json([
            'msg'=>'Please Enter Planning Method Name',
            'status'=>0
        ]);
    } else if( $request['event_driven']==""){
        return response()->json([
            'msg'=>'Please Enter Event Driven to Use This Method',
            'status'=>0
        ]);
    }else if( $request['date_attended']==""){
        return response()->json([
            'msg'=>'Please Enter Date Of Starting to Use This Method',
            'status'=>0
        ]);
    }
      else if( $request['place']==""){
        return response()->json([
            'msg'=>'Please Enter Place of This Client',
            'status'=>0
        ]);
    }

  else if(count($data)>0){
        return response()->json([
            'msg'=>'Oops!!... The Method Chosen Is Already Assigned And Is Still Running To This Selected Client',
            'status'=>0
        ]);
    }


    else   {
        $data=Tbl_family_planning_method_register::create($request->all());
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }
}

    public function family_planning_method_list_update(Request $request)
    {
       $id=$request['id'];
        if( $request['planning_method']==""){
            return response()->json([
                'msg'=>'Please Enter Planning Method Name',
                'status'=>0
            ]);
        }
         Tbl_family_planning_method_list::where('id',$id)->update($request->all());

        return response()->json([
            'msg'=>'Updated ...',
            'status'=>1
        ]);
    }


    public function family_planning_method_list()
    {
     return Tbl_family_planning_method_list::get();
    }

    public function fplanning_referral_registration(Request $request)
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
        }else  if(  $request['type']==""){
            return response()->json([
                'msg'=>'Please Choose Referral Of Who? ',
                'status'=>0
            ]);
        }




        else if(patientRegistration::duplicate('tbl_family_planning_referrals',['patient_id','transfered_institution_id','date','reason',
                '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$transfered_institution_id,$date,$reason ])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            $data= Tbl_family_planning_referral::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


    }


    public function mother_planning_method_status($patient_id)
    {
      return DB::table('tbl_family_planning_method_lists')
          ->join('tbl_family_planning_method_registers','tbl_family_planning_method_lists.id','=','tbl_family_planning_method_registers.method_id')
          ->where('patient_id',$patient_id)
          ->orderBy('status','desc')
          ->get();
    }

    public function mother_planning_method_status_update(Request $request)
    {
        $id=$request['id'];
        $patient_id=$request['patient_id'];
        $planning_method=$request['planning_method'];
       $data=Tbl_family_planning_method_register::where('id',$id)->update(['status'=>0]);
        //calling updated list of patients planning method again
        $records=$this->mother_planning_method_status($patient_id);
        return response()->json([
            'msg'=>'Successful....'. $planning_method .' Now has Stopped to be used. ',
            'status'=>1,
            'records'=>$records,
        ]);
    }

    public function RCH_recommendations_registration(Request $request)
    {

        $patient_id=$request['patient_id'];
        $opinion=$request['opinion'];
        if( $request['opinion']==""){
            return response()->json([
                'msg'=>'Please Write Recommendations First',
                'status'=>0
            ]);
        }

        else if(patientRegistration::duplicate('tbl_rch_general_recomendations',['patient_id','opinion',
                '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$opinion])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            $data= Tbl_rch_general_recomendation::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }
    }

    public function condom_usage_registration(Request $request)

    {

        $patient_id=$request['patient_id'];
        $quantity=$request['quantity'];
        $place=$request['place'];

          if(  $request['place']==""){
            return response()->json([
                'msg'=>'Please Enter Client Place',
                'status'=>0
            ]);
        }
        else  if(!is_numeric($request['quantity']) && $request['quantity']!=""){
            return response()->json([
                'msg'=>'Please Enter Number Of condoms or Packets and should a number',
                'status'=>0
            ]);
        }

        else if(patientRegistration::duplicate('tbl_condoms',['patient_id','quantity','place','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],[$patient_id,$quantity,$place])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }


        else   {
            $data=Tbl_condom::create($request->all());
            return response()->json([
                'msg'=>'Successful saved',
                'status'=>1
            ]);
        }
    }



    public function Family_incoming_referral(Request $request)
    {
        $request->all();

       $client_id = $request['patient_id'];
        $dept_id = $request['dept_id'];
        $visit_id = $request['visit_id'];

        $checkpatientExist = Tbl_family_planning_register::where('client_id', $client_id)->first();
        $checkrequestID = Tbl_clinic_specialist::where('dept_id',$dept_id)->orderBy('id','desc')->first();
        $request_id=$checkrequestID->id;
        $updateRequest=Tbl_clinic_instruction::where('dept_id',$request['dept_id'])->
        where('received',0)
            ->where('visit_id',$visit_id)
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
            $education = 'unknown';


         return   $records=  patientRegistration::Family_planning_Number($facility_id,$client_id,$user_id,$client_name,$dob,$occupation_id,$residence_id,$education);


        }
        else{
            return $checkpatientExist;
        }
    }

}