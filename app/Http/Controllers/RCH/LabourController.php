<?php

namespace App\Http\Controllers\RCH;

use App\classes\patientRegistration;
use App\Patient\Tbl_patient;
use App\RCH\Tbl_anti_natal_register;
use App\RCH\Tbl_labour_admission;
use App\RCH\Tbl_labour_birth_info;
use App\RCH\Tbl_labour_delivery;
use App\RCH\Tbl_labour_delivery_child_arv;
use App\RCH\Tbl_labour_delivery_child_disposition;
use App\RCH\Tbl_labour_delivery_child_feeding;
use App\RCH\Tbl_labour_delivery_complication;
use App\RCH\Tbl_labour_delivery_event;
use App\RCH\Tbl_labour_delivery_mother_disposition;
use App\RCH\Tbl_labour_delivery_vvu_result;
use App\RCH\Tbl_labour_emonc_service;
use App\RCH\Tbl_labour_fgm;
use App\RCH\Tbl_labour_fsb_msb;
use App\RCH\Tbl_labour_newborn;
use App\RCH\Tbl_labour_observation;
use App\RCH\Tbl_labour_referral;
use App\RCH\Tbl_labour_register;
use App\RCH\Tbl_previous_pregnancy_indicator;
use App\RCH\Tbl_previous_pregnancy_info;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class LabourController extends Controller
{
    //


    public function Labour_serial_no(Request $request)
    {

        $patient_id=$request['patient_id'];
        $facility_id=$request['facility_id'];
        $user_id=$request['user_id'];
        $data= Tbl_labour_register::where('patient_id',$patient_id)->where('year',Date('Y'))->first();
        if (count($data)>0) {
            return $data;
        }
        else{
            return  patientRegistration::Labour_Serial_Number($facility_id,$patient_id,$user_id);
        }
    }
 
    public function Labour_registration_update(Request $request)
    {
        $patient_id=$request['patient_id'];
        $rch_4=$request['rch_4'];
        $labour_reg_date=$request['labour_reg_date'];

        if($request['rch_4']==""){
            return response()->json([
                'msg'=>'Please Enter RCH- Number',
                'status'=>0
            ]);
        }
        else if($request['labour_reg_date']==""){
            return response()->json([
                'msg'=>'Please Enter Date of Labour Registration',
                'status'=>0
            ]);
        }



        else   {
            $data=Tbl_labour_register::where('patient_id',$patient_id)->update([
                'rch_4'=>$rch_4,
                'labour_reg_date'=>$labour_reg_date,
            ]);
            return response()->json([
                'msg'=>'Successful saved',
                'status'=>1
            ]);
        }

    }


    public function labour_birth_info_registration(Request $request)
    {

        $patient_id=$request['patient_id'];
        $number_of_pregnancy=$request['number_of_pregnancy'];
        $number_of_delivery=$request['number_of_delivery'];
        $number_alive_children=$request['number_alive_children'];





        if(!is_numeric($request['number_of_pregnancy']) || $request['number_of_pregnancy']==""){
            return response()->json([
                'msg'=>'Please Enter Number of Pregnancy and should a number',
                'status'=>0
            ]);
        }
        else if(!is_numeric($request['number_of_delivery']) || $request['number_of_delivery']==""){
            return response()->json([
                'msg'=>'Please Enter Number of Delivery and should a number',
                'status'=>0
            ]);
        }else if(!is_numeric($request['number_alive_children']) || $request['number_alive_children']==""){
            return response()->json([
                'msg'=>'Please Enter Number of   Alive Children and should a number',
                'status'=>0
            ]);
        }




        else if(patientRegistration::duplicate('tbl_labour_birth_infos',['patient_id','number_of_delivery','number_of_pregnancy','number_alive_children','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$number_of_delivery,$number_of_pregnancy,$number_alive_children])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }


        else{
            $data= Tbl_labour_birth_info::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);


        }

    }

    public function labour_admission_registration(Request $request)
    {

        $patient_id=$request['client_id'];
        $date_time=$request['admission_date'];


        if(  $request['admission_date']==""){
            return response()->json([
                'msg'=>'Please Enter Date and Time For This Admission',
                'status'=>0
            ]);
        }




        else if(patientRegistration::duplicate('tbl_labour_admissions',['client_id','admission_date', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$date_time,])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }


        else{
            $data= Tbl_labour_admission::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);


        }

    }

    public function labour_delivery_registration(Request $request)
    {

        $patient_id=$request['client_id'];


        if(  $request['delivery_date']==""){
            return response()->json([
                'msg'=>'Please Enter Date and Time Of Delivery',
                'status'=>0
            ]);
        }
        else if(  $request['place_of_delivery']==""){
            return response()->json([
                'msg'=>'Please Enter Place of Delivery',
                'status'=>0
            ]);
        }
    else if(!is_numeric($request['number_of_newborn']) || $request['number_of_newborn']==""){
        return response()->json([
            'msg'=>'Please Enter Number of   NewBorn and should a number',
            'status'=>0
        ]);
    }
    else if(  $request['method_of_delivery']==""){
        return response()->json([
            'msg'=>'Please Enter Method of Delivery',
            'status'=>0
        ]);
    }else if(  $request['vitamin_given']==""){
        return response()->json([
            'msg'=>'Please Enter vitamin given Status',
            'status'=>0
        ]);
    } else if(  $request['number_of_newborn']==""){
        return response()->json([
            'msg'=>'Please Enter  number of newborn',
            'status'=>0
        ]);
    } else if(  $request['placenter_removed']==""){
        return response()->json([
            'msg'=>'Please Enter  placenter removed status',
            'status'=>0
        ]);
    }else if(  $request['msamba']==""){
        return response()->json([
            'msg'=>'Please Enter  msamba status',
            'status'=>0
        ]);
    }else if(  $request['midwife_name']==""){
        return response()->json([
            'msg'=>'Please Enter   Midwife Name',
            'status'=>0
        ]);
    }
        else if(patientRegistration::duplicate('tbl_labour_delivery_events',['client_id',  '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }


        else{
            $data= Tbl_labour_delivery_event::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);


        }

    }



    public function labour_referral_registration(Request $request)
    {
        $patient_id=$request['patient_id'];

        $transfered_institution_id=$request['transfered_institution_id'];
        $reason=$request['reason'];
        $type=$request['type'];
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
                'msg'=>'Please Enter Referral Of Who? ',
                'status'=>0
            ]);
        }




        else if(patientRegistration::duplicate('tbl_labour_referrals',['patient_id','transfered_institution_id','date','reason',
                '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$transfered_institution_id,$date,$reason ])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            $data= Tbl_labour_referral::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


    }

public function newborn_info_registration(Request $request)
    {
        $patient_id=$request['client_id'];

        $first_minute_score=$request['first_minute_score'];
        $fifth_minute_score=$request['fifth_minute_score'];
        $gender=$request['gender'];

        $breast_feeding_within_hour=$request['breast_feeding_within_hour'];



        if($request['client_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
        else  if(  $request['gender']==""){
            return response()->json([
                'msg'=>'Please Choose Newborn Gender ',
                'status'=>0
            ]);
        }

        else if(!is_numeric($request['first_minute_score']) || $request['first_minute_score']==""){
            return response()->json([
                'msg'=>'Please Enter Number of Score',
                'status'=>0
            ]);
        }
        else if(!is_numeric($request['fifth_minute_score']) || $request['fifth_minute_score']==""){
            return response()->json([
                'msg'=>'Please Enter Number of Score',
                'status'=>0
            ]);
        }
        else  if(  $request['breast_feeding_within_hour']==""){
            return response()->json([
                'msg'=>'Please Choose Option for Breast feeding within hour ',
                'status'=>0
            ]);
        }





        else if(patientRegistration::duplicate('tbl_labour_newborns',['client_id','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            $data= Tbl_labour_newborn::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


    }

    public function labour_fsb_msb_registration(Request $request)
    {
        $patient_id=$request['patient_id'];

        $fsb_msb=$request['fsb_msb'];

        if($request['patient_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
        else  if(  $request['fsb_msb']==""){
            return response()->json([
                'msg'=>'Please Fil Field here ',
                'status'=>0
            ]);
        }


        else if(patientRegistration::duplicate('tbl_labour_fsb_msbs',['patient_id','fsb_msb','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$fsb_msb])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            $data= Tbl_labour_fsb_msb::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


    }
public function labour_fgm_registration(Request $request)
    {
        $patient_id=$request['patient_id'];

        $fgm=$request['fgm'];

        if($request['patient_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
        else  if(  $request['fgm']==""){
            return response()->json([
                'msg'=>'Please Fil Field here ',
                'status'=>0
            ]);
        }


        else if(patientRegistration::duplicate('tbl_labour_fgms',['patient_id','fgm','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$fgm])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            $data= Tbl_labour_fgm::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


    }
    public function labour_child_arv_registration(Request $request)
    {
        $patient_id=$request['patient_id'];

        $arv_given=$request['arv_given'];

        if($request['patient_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
        else  if(  $request['arv_given']==""){
            return response()->json([
                'msg'=>'Please Fil Field here ',
                'status'=>0
            ]);
        }


        else if(patientRegistration::duplicate('tbl_labour_delivery_child_arvs',['patient_id','arv_given','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$arv_given])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            $data= Tbl_labour_delivery_child_arv::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


    }

    public function labour_vvu_registration(Request $request)
    {
        $patient_id=$request['patient_id'];

        $vvu_result=$request['vvu_result'];
        $labour_delivery_result=$request['labour_delivery_result'];

        if($request['patient_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
        else  if(  $request['vvu_result']==""){
            return response()->json([
                'msg'=>'Please Fil VVU status before  ',
                'status'=>0
            ]);
        }else  if(  $request['labour_delivery_result']==""){
            return response()->json([
                'msg'=>'Please Fil VVU Status During Labour And After Delivery ',
                'status'=>0
            ]);
        }


        else if(patientRegistration::duplicate('tbl_labour_delivery_vvu_results',['patient_id','vvu_result','labour_delivery_result','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$vvu_result,$labour_delivery_result])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            $data= Tbl_labour_delivery_vvu_result::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


    }

    public function labour_complication_registration(Request $request)
    {
        //return $request->all();
        $patient_id=$request['patient_id'];

        $vaginal_bleeding=$request['vaginal_bleeding'];
        $prom=$request['prom'];
        $preeclampsia=$request['preeclampsia'];
        $eclampsia=$request['eclampsia'];
        $anaemia=$request['anaemia'];
        $sepsis=$request['sepsis'];
        $malaria=$request['malaria'];
        $hiv_p=$request['hiv_p'];
        $pph=$request['pph'];
        $fgm=$request['fgm'];
        $obstructed_labour=$request['obstructed_labour'];
        $three_tear=$request['three_tear'];
        $retained_placenta=$request['retained_placenta'];
        $chest_pain=$request['chest_pain'];
        $loss_strength=$request['loss_strength'];
        $other_complication=$request['other_complication'];

        if($request['patient_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
        else  if(  $request['vaginal_bleeding']==""){
            return response()->json([
                'msg'=>'Please Fil vaginal bleeding Option',
                'status'=>0
            ]);
        } else  if(  $request['prom']==""){
            return response()->json([
                'msg'=>'Please Fil PROM Option',
                'status'=>0
            ]);
        }
        else  if(  $request['preeclampsia']==""){
            return response()->json([
                'msg'=>'Please Fil Preeclampsia Option',
                'status'=>0
            ]);
        }else  if(  $request['eclampsia']==""){
            return response()->json([
                'msg'=>'Please Fil Eclampsia Option',
                'status'=>0
            ]);
        }else  if(  $request['anaemia']==""){
            return response()->json([
                'msg'=>'Please Fil Anaemia Option',
                'status'=>0
            ]);
        }
else  if(  $request['sepsis']==""){
            return response()->json([
                'msg'=>'Please Fil Sepsis Option',
                'status'=>0
            ]);
        }

else  if(  $request['malaria']==""){
            return response()->json([
                'msg'=>'Please Fil Malaria Option',
                'status'=>0
            ]);
        }else  if(  $request['hiv_p']==""){
            return response()->json([
                'msg'=>'Please Fil HIV + Option',
                'status'=>0
            ]);
        }else  if(  $request['pph']==""){
            return response()->json([
                'msg'=>'Please Fil PPH Option',
                'status'=>0
            ]);
        }else  if(  $request['fgm']==""){
            return response()->json([
                'msg'=>'Please Fil FGM Option',
                'status'=>0
            ]);
        }else  if(  $request['obstructed_labour']==""){
            return response()->json([
                'msg'=>'Please Fil Obstructed Labour Option',
                'status'=>0
            ]);
        }else  if(  $request['three_tear']==""){
            return response()->json([
                'msg'=>'Please Fil Three Tear Option',
                'status'=>0
            ]);
        }else  if(  $request['retained_placenta']==""){
            return response()->json([
                'msg'=>'Please Fil Retained Placenta Option',
                'status'=>0
            ]);
        }else  if(  $request['chest_pain']==""){
            return response()->json([
                'msg'=>'Please Fil Chest Pain Option',
                'status'=>0
            ]);
        }else  if(  $request['loss_strength']==""){
            return response()->json([
                'msg'=>'Please Fil Loss Strength Option',
                'status'=>0
            ]);
        }


        else if(patientRegistration::duplicate('tbl_labour_delivery_complications',['patient_id','vaginal_bleeding','prom','preeclampsia',
                'eclampsia','anaemia','sepsis','malaria','hiv_p','pph','fgm','obstructed_labour','three_tear','retained_placenta','chest_pain','loss_strength','other_complication','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$vaginal_bleeding,$prom,$preeclampsia,$eclampsia,$anaemia,$sepsis,$malaria,$hiv_p,$pph,$fgm,$obstructed_labour,$three_tear,$retained_placenta,$chest_pain,$loss_strength,$other_complication])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            $data= Tbl_labour_delivery_complication::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


    }

    public function labour_observation_registration(Request $request)
    {
        //return $request->all();
        $patient_id=$request['client_id'];
        if(!is_numeric($request['temperature']) && $request['temperature'] !=""){
            return response()->json([
                'msg'=>'Temperature Should be a Number',
                'status'=>0
            ]);
        }
        if(!is_numeric($request['hb']) && $request['hb'] !=""){
            return response()->json([
                'msg'=>'HB Should be a Number',
                'status'=>0
            ]);
        }
        if(!is_numeric($request['bp']) && $request['bp'] !=""){
            return response()->json([
                'msg'=>'BP Should be a Number',
                'status'=>0
            ]);
        }
        if($request['client_id']==""){
            return response()->json([
                'msg'=>'Please choose Client',
                'status'=>0
            ]);
        }
          if(patientRegistration::duplicate('tbl_labour_observations',['client_id','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=2))'],
                [$patient_id])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            $data= Tbl_labour_observation::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


    }


    public function labour_child_feeding_registration(Request $request)
    {
        $patient_id=$request['patient_id'];
        $feeding_type=$request['feeding_type'];
        if($request['feeding_type']=="") {
            return response()->json([
                'msg' => 'Please Choose Feeding Type',
                'status' => 0
            ]);
        }


        else{
            if(patientRegistration::duplicate('tbl_labour_delivery_child_feedings',['patient_id','feeding_type','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                    [$patient_id,$feeding_type,])==true) {

                return response()->json([
                    'msg'=>'Duplication detected.....',
                    'status'=>0
                ]);
            }
            else{
                $data= Tbl_labour_delivery_child_feeding::create($request->all());

                return response()->json([
                    'msg'=>'Successful',
                    'status'=>1
                ]);

            }
        }


    }

    public function labour_mother_disposition_registration(Request $request)
    {

        $patient_id=$request['patient_id'];

        $alive=$request['alive'];
        $disposition_date=$request['disposition_date'];
        $death_date=$request['death_date'];
        $death_reason=$request['death_reason'];
        if($request['alive']=="") {
            return response()->json([
                'msg' => 'Please Choose Alive Option',
                'status' => 0
            ]);
        }else if($request['disposition_date']=="") {
            return response()->json([
                'msg' => 'Please Fill date for Discharge',
                'status' => 0
            ]);
        }
else if($request['alive']=="NO" && $request['death_date']=="") {
            return response()->json([
                'msg' => 'Please Fill date Of Death',
                'status' => 0
            ]);
        }else if($request['alive']=="NO" && $request['death_reason']=="") {
            return response()->json([
                'msg' => 'Please Fill Reason for Death',
                'status' => 0
            ]);
        }


        else{
            if(patientRegistration::duplicate('tbl_labour_delivery_mother_dispositions',['patient_id','alive','disposition_date','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                    [$patient_id,$alive,$disposition_date])==true) {

                return response()->json([
                    'msg'=>'Duplication detected.....',
                    'status'=>0
                ]);
            }
            else{
                $data= Tbl_labour_delivery_mother_disposition::create($request->all());

                return response()->json([
                    'msg'=>'Successful',
                    'status'=>1
                ]);

            }
        }


    }
    public function labour_child_disposition_registration(Request $request)
    {

        $patient_id=$request['patient_id'];

        $alive=$request['alive'];
        $disposition_date=$request['disposition_date'];
        $death_date=$request['death_date'];
        $death_reason=$request['death_reason'];
        if($request['alive']=="") {
            return response()->json([
                'msg' => 'Please Choose Alive Option',
                'status' => 0
            ]);
        }else if($request['disposition_date']=="") {
            return response()->json([
                'msg' => 'Please Fill date for Discharge',
                'status' => 0
            ]);
        }
else if($request['alive']=="NO" && $request['death_date']=="") {
            return response()->json([
                'msg' => 'Please Fill date Of Death',
                'status' => 0
            ]);
        }else if($request['alive']=="NO" && $request['death_reason']=="") {
            return response()->json([
                'msg' => 'Please Fill Reason for Death',
                'status' => 0
            ]);
        }


        else{
            if(patientRegistration::duplicate('tbl_labour_delivery_child_dispositions',['patient_id','alive','disposition_date','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                    [$patient_id,$alive,$disposition_date])==true) {

                return response()->json([
                    'msg'=>'Duplication detected.....',
                    'status'=>0
                ]);
            }
            else{
                $data= Tbl_labour_delivery_child_disposition::create($request->all());

                return response()->json([
                    'msg'=>'Successful',
                    'status'=>1
                ]);

            }
        }


    }

    public function getprev_preg_info($client_id)
    {
 
        $client=Tbl_anti_natal_register::where('id',$client_id)->get();
        $all=[];
        $all[]=Tbl_previous_pregnancy_info::where('client_id',$client_id)->get();
        $all[]=Tbl_previous_pregnancy_indicator::where('client_id',$client_id)->get();
        $all[]=Tbl_labour_admission::where('client_id',$client_id)->get();
        $all[]=Tbl_labour_fgm::where('patient_id',$client_id)->get();
        $all[]=Tbl_labour_observation::where('client_id',$client_id)->get();
        $all[]=Tbl_labour_delivery_event::where('client_id',$client_id)->get();
        $all[]=Tbl_labour_newborn::where('client_id',$client_id)->get();
        $all[]=Tbl_labour_delivery_child_feeding::where('patient_id',$client_id)->get();
        $all[]=Tbl_labour_fsb_msb::where('patient_id',$client_id)->get();
        $all[]=Tbl_labour_delivery_complication::where('patient_id',$client_id)->get();
        $all[]=Tbl_labour_delivery_mother_disposition::where('patient_id',$client_id)->get();
        $all[]=Tbl_labour_delivery_child_disposition::where('patient_id',$client_id)->get();
        $all[]=Tbl_labour_referral::where('patient_id',$client_id)->get();
        $all[]=Tbl_patient::where('id',$client[0]->client_id)->get();

        return $all;
    }


}