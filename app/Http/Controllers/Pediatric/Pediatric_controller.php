<?php

namespace App\Http\Controllers\Pediatric;

use App\classes\patientRegistration;
use App\classes\ServiceManager;
use App\Paediatric\Tbl_dtc_attendance;
use App\Paediatric\Tbl_dtc_central;
use App\Pediatric\Tbl_dtcs;
use App\Pediatric\Tbl_pediatric_diatary;
use App\Pediatric\Tbl_pediatric_natal;
use App\Pediatric\Tbl_pediatric_nutritional;
use App\Pediatric\Tbl_pediatric_post_natal;
use App\Pediatric\Tbl_pediatric_pre_natal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Pediatric_controller extends Controller
{

    //
    public function pediatricNutritional(Request $request)
    {

        $client_id=$request['client_id'];
        $muac=$request['muac'];
        $whz_score=$request['whz_score'];
        if($whz_score=='"/"'){
        return response()->json([
            'data'=>'Calculate WHZ-Score',
            'status'=>0
        ]);
        }
        if($muac==''){
            return response()->json([
                'data'=>'Fill MUAC',
                'status'=>0
            ]);
        }

        if(patientRegistration::duplicate('tbl_pediatric_nutritionals',['client_id','muac','whz_score', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$client_id,$muac,$whz_score])==true) {

            return response()->json([
                'data'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            Tbl_pediatric_nutritional::create($request->all());
            return response()->json([
                'data'=>'Successful data saved',
                'status'=>1
            ]);
        }

    }

    public function pediatricDietary(Request $request)
    {

        $client_id=$request['client_id'];
        $food_intake_quality=$request['food_intake_quality'];
        $food_intake_quantity=$request['food_intake_quantity'];


        if(patientRegistration::duplicate('tbl_pediatric_diataries',['client_id','food_intake_quality','food_intake_quantity', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$client_id,$food_intake_quality,$food_intake_quantity])==true) {

            return response()->json([
                'data'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            Tbl_pediatric_diatary::create($request->all());
            return response()->json([
                'data'=>'Successful data saved',
                'status'=>1
            ]);
        }

    }
    public function pediatricPreNatal(Request $request)
    {

        $client_id=$request['client_id'];
        $preg_book_age=$request['preg_book_age'];
        $clinic_attendance=$request['clinic_attendance'];
        $prophylaxis=$request['prophylaxis'];


        if(patientRegistration::duplicate('tbl_pediatric_pre_natals',['client_id','preg_book_age','clinic_attendance','prophylaxis', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$client_id,$preg_book_age,$clinic_attendance,$prophylaxis])==true) {

            return response()->json([
                'data'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            Tbl_pediatric_pre_natal::create($request->all());
            return response()->json([
                'data'=>'Successful data saved',
                'status'=>1
            ]);
        }

    }
    public function pediatricNatal(Request $request)
    {

        $client_id=$request['client_id'];
        $delivery_mode=$request['delivery_mode'];
        $delivery_place=$request['delivery_place'];
        $baby_cry=$request['baby_cry'];



        if(patientRegistration::duplicate('tbl_pediatric_natals',['client_id','delivery_mode','delivery_place', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$client_id,$delivery_mode,$delivery_place])==true) {

            return response()->json([
                'data'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            Tbl_pediatric_natal::create($request->all());
            return response()->json([
                'data'=>'Successful data saved',
                'status'=>1
            ]);
        }

    }
    public function pediatricPostNatal(Request $request)
    {

        $client_id=$request['client_id'];
        $immunization=$request['immunization'];
        $diety=$request['diety'];
        $milestone=$request['milestone'];



        if(patientRegistration::duplicate('tbl_pediatric_post_natals',['client_id','immunization','diety','milestone', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$client_id,$immunization,$diety,$milestone])==true) {

            return response()->json([
                'data'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            Tbl_pediatric_post_natal::create($request->all());
            return response()->json([
                'data'=>'Successful data saved',
                'status'=>1
            ]);
        }

    }

    public function postDTC(Request $request)
    {
       if($request['ors_in']!='' && !is_numeric($request['ors_in'])) {
           return response()->json([
               'msg'=>'ORS solution Amount Should be A number',
               'status'=>0
           ]);
       }
        if($request['ors_out']!='' && !is_numeric($request['ors_out'])) {
           return response()->json([
               'msg'=>'ORS solution (ml) Amount Should be A number',
               'status'=>0
           ]);
       }
        if($request['zink_in']!='' && !is_numeric($request['zink_in'])) {
           return response()->json([
               'msg'=>'Zink (mg) Amount Should be A number',
               'status'=>0
           ]);
       }
        if($request['zink_out']!='' && !is_numeric($request['zink_out'])) {
           return response()->json([
               'msg'=>'Zink (mg) Amount Should be A number',
               'status'=>0
           ]);
       }
        if($request['intravesel_water']!='' && !is_numeric($request['intravesel_water'])) {
           return response()->json([
               'msg'=>'Water For Inj Amount Should be A number',
               'status'=>0
           ]);
       }

        $check=Tbl_dtcs::where('visit_id',$request['visit_id'])->get();
if(count($check)>0){
    Tbl_dtcs::where('visit_id',$request['visit_id'])->update($request->all());
    return response()->json([
        'msg'=>'DTC Information Success Full Saved',
        'status'=>1
    ]);
}
        else{
            Tbl_dtcs::create($request->all());
            return response()->json([
                'msg'=>'DTC Information Success Full Saved',
                'status'=>1
            ]);
        }

    }

     public function mtuhaDTC( Request $request)
    {
        $response = [];
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }
		
        $attendance
            = "SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) <1   then 1 ELSE  0 END ),0) as male_less_moth, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as female_less_moth,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as total_less_moth,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12   then 1 ELSE  0 END ),0) as male_moth_less_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at)>=1 AND timestampdiff(month ,dob,t2.created_at)<12  then 1 ELSE  0 END ),0) as female_moth_less_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12    then 1 ELSE  0 END ),0) as total_moth_less_year,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as male_year_five_year, ifnull(sum(CASE when gender ='FEMALE'   AND  timestampdiff(YEAR ,dob,t2.created_at)  BETWEEN 1 and 5 then 1 ELSE  0 END ),0) as female_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5    then 1 ELSE  0 END ),0) as total_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(MONTH ,dob,t2.created_at) BETWEEN 0 and 60  then 1 ELSE  0 END ),0) as total
  
from tbl_patients as t1 inner JOIN tbl_dtcs as t2 on t2.patient_id=t1.id  where t2.facility_id='".$facility_id."'";


        $response[]= DB::select($attendance)[0];
		
        $water_sugar_loss_M
            = "SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) <1   then 1 ELSE  0 END ),0) as male_less_moth, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as female_less_moth,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as total_less_moth,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12   then 1 ELSE  0 END ),0) as male_moth_less_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at)>=1 AND timestampdiff(month ,dob,t2.created_at)<12  then 1 ELSE  0 END ),0) as female_moth_less_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12    then 1 ELSE  0 END ),0) as total_moth_less_year,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as male_year_five_year, ifnull(sum(CASE when gender ='FEMALE'   AND  timestampdiff(YEAR ,dob,t2.created_at)  BETWEEN 1 and 5 then 1 ELSE  0 END ),0) as female_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5    then 1 ELSE  0 END ),0) as total_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(MONTH ,dob,t2.created_at) BETWEEN 0 and 60  then 1 ELSE  0 END ),0) as total
  
from tbl_patients as t1 inner JOIN tbl_dtcs as t2 on t2.patient_id=t1.id WHERE water_sugar_loss='M' and t2.facility_id='".$facility_id."'";

        $response[] = DB::select($water_sugar_loss_M)[0];
		
        $water_sugar_loss_K
            = "SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) <1   then 1 ELSE  0 END ),0) as male_less_moth, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as female_less_moth,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as total_less_moth,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12   then 1 ELSE  0 END ),0) as male_moth_less_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at)>=1 AND timestampdiff(month ,dob,t2.created_at)<12  then 1 ELSE  0 END ),0) as female_moth_less_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12    then 1 ELSE  0 END ),0) as total_moth_less_year,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as male_year_five_year, ifnull(sum(CASE when gender ='FEMALE'   AND  timestampdiff(YEAR ,dob,t2.created_at)  BETWEEN 1 and 5 then 1 ELSE  0 END ),0) as female_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5    then 1 ELSE  0 END ),0) as total_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(MONTH ,dob,t2.created_at) BETWEEN 0 and 60  then 1 ELSE  0 END ),0) as total
  
from tbl_patients as t1 inner JOIN tbl_dtcs as t2 on t2.patient_id=t1.id WHERE water_sugar_loss='K' and t2.facility_id='".$facility_id."'";


        $response[] = DB::select($water_sugar_loss_K)[0];

 $stool_blood
            = "SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) <1   then 1 ELSE  0 END ),0) as male_less_moth, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as female_less_moth,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as total_less_moth,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12   then 1 ELSE  0 END ),0) as male_moth_less_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at)>=1 AND timestampdiff(month ,dob,t2.created_at)<12  then 1 ELSE  0 END ),0) as female_moth_less_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12    then 1 ELSE  0 END ),0) as total_moth_less_year,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as male_year_five_year, ifnull(sum(CASE when gender ='FEMALE'   AND  timestampdiff(YEAR ,dob,t2.created_at)  BETWEEN 1 and 5 then 1 ELSE  0 END ),0) as female_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5    then 1 ELSE  0 END ),0) as total_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(MONTH ,dob,t2.created_at) BETWEEN 0 and 60  then 1 ELSE  0 END ),0) as total
  
from tbl_patients as t1 inner JOIN tbl_dtcs as t2 on t2.patient_id=t1.id WHERE stool_blood='N' and t2.facility_id='".$facility_id."'";


        $response[] = DB::select($stool_blood)[0];

        $referral
            = "SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) <1   then 1 ELSE  0 END ),0) as male_less_moth, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as female_less_moth,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as total_less_moth,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12   then 1 ELSE  0 END ),0) as male_moth_less_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at)>=1 AND timestampdiff(month ,dob,t2.created_at)<12  then 1 ELSE  0 END ),0) as female_moth_less_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12    then 1 ELSE  0 END ),0) as total_moth_less_year,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as male_year_five_year, ifnull(sum(CASE when gender ='FEMALE'   AND  timestampdiff(YEAR ,dob,t2.created_at)  BETWEEN 1 and 5 then 1 ELSE  0 END ),0) as female_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5    then 1 ELSE  0 END ),0) as total_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(MONTH ,dob,t2.created_at) BETWEEN 0 and 60  then 1 ELSE  0 END ),0) as total
  
from tbl_patients as t1 inner JOIN tbl_dtcs as t2 on t2.patient_id=t1.id WHERE output='REF' and t2.facility_id='".$facility_id."'";


        $response[] = DB::select($referral)[0];
        $zink
            = "SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) <1   then 1 ELSE  0 END ),0) as male_less_moth, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as female_less_moth,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as total_less_moth,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12   then 1 ELSE  0 END ),0) as male_moth_less_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at)>=1 AND timestampdiff(month ,dob,t2.created_at)<12  then 1 ELSE  0 END ),0) as female_moth_less_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12    then 1 ELSE  0 END ),0) as total_moth_less_year,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as male_year_five_year, ifnull(sum(CASE when gender ='FEMALE'   AND  timestampdiff(YEAR ,dob,t2.created_at)  BETWEEN 1 and 5 then 1 ELSE  0 END ),0) as female_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5    then 1 ELSE  0 END ),0) as total_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(MONTH ,dob,t2.created_at) BETWEEN 0 and 60  then 1 ELSE  0 END ),0) as total
  
from tbl_patients as t1 inner JOIN tbl_dtcs as t2 on t2.patient_id=t1.id WHERE  (zink_in IS  NOT NULL OR zink_out IS  NOT NULL) and t2.facility_id='".$facility_id."' ";


        $response[] = DB::select($zink)[0];

        $ors
            = "SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) <1   then 1 ELSE  0 END ),0) as male_less_moth, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as female_less_moth,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as total_less_moth,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12   then 1 ELSE  0 END ),0) as male_moth_less_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at)>=1 AND timestampdiff(month ,dob,t2.created_at)<12  then 1 ELSE  0 END ),0) as female_moth_less_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12    then 1 ELSE  0 END ),0) as total_moth_less_year,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as male_year_five_year, ifnull(sum(CASE when gender ='FEMALE'   AND  timestampdiff(YEAR ,dob,t2.created_at)  BETWEEN 1 and 5 then 1 ELSE  0 END ),0) as female_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5    then 1 ELSE  0 END ),0) as total_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(MONTH ,dob,t2.created_at) BETWEEN 0 and 60  then 1 ELSE  0 END ),0) as total
  
from tbl_patients as t1 inner JOIN tbl_dtcs as t2 on t2.patient_id=t1.id WHERE  (ors_in IS  NOT NULL OR ors_out IS  NOT NULL) and t2.facility_id='".$facility_id."'";
        //and  tbl_tb_patient_treatment_types.created_at BETWEEN '".$start_date."' and '".$end_date."'

        $response[] = DB::select($ors)[0];
		
        $admission
            = "SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) <1   then 1 ELSE  0 END ),0) as male_less_moth, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as female_less_moth,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as total_less_moth,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12   then 1 ELSE  0 END ),0) as male_moth_less_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at)>=1 AND timestampdiff(month ,dob,t2.created_at)<12  then 1 ELSE  0 END ),0) as female_moth_less_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12    then 1 ELSE  0 END ),0) as total_moth_less_year,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as male_year_five_year, ifnull(sum(CASE when gender ='FEMALE'   AND  timestampdiff(YEAR ,dob,t2.created_at)  BETWEEN 1 and 5 then 1 ELSE  0 END ),0) as female_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5    then 1 ELSE  0 END ),0) as total_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(MONTH ,dob,t2.created_at) BETWEEN 0 and 60  then 1 ELSE  0 END ),0) as total
  
from tbl_patients as t1 inner JOIN tbl_dtcs as t2 on t2.patient_id=t1.id WHERE output='ADM' and t2.facility_id='".$facility_id."'";


        $response[] = DB::select($admission)[0];

        $deceased
            = "SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) <1   then 1 ELSE  0 END ),0) as male_less_moth, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as female_less_moth,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) <1  then 1 ELSE  0 END ),0) as total_less_moth,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12   then 1 ELSE  0 END ),0) as male_moth_less_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,t2.created_at)>=1 AND timestampdiff(month ,dob,t2.created_at)<12  then 1 ELSE  0 END ),0) as female_moth_less_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(month ,dob,t2.created_at) >=1 AND timestampdiff(month ,dob,t2.created_at) <12    then 1 ELSE  0 END ),0) as total_moth_less_year,
  
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as male_year_five_year, ifnull(sum(CASE when gender ='FEMALE'   AND  timestampdiff(YEAR ,dob,t2.created_at)  BETWEEN 1 and 5 then 1 ELSE  0 END ),0) as female_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(YEAR ,dob,t2.created_at) BETWEEN 1 and 5    then 1 ELSE  0 END ),0) as total_year_five_year,
  ifnull(sum(CASE when (gender ='FEMALE' or gender ='MALE' )  AND timestampdiff(MONTH ,dob,t2.created_at) BETWEEN 0 and 60  then 1 ELSE  0 END ),0) as total
  
from tbl_patients as t1 inner JOIN tbl_dtcs as t2 on t2.patient_id=t1.id WHERE output='DEAD' and t2.facility_id='".$facility_id."'";


        $response[] = DB::select($deceased)[0];



        return $response;
    }

}