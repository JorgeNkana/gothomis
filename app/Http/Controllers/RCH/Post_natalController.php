<?php

namespace App\Http\Controllers\RCH;

use App\classes\patientRegistration;
use App\RCH\Tbl_post_dehiscence_fistula_mental_status;
use App\RCH\Tbl_post_natal_additional_medication;
use App\RCH\Tbl_post_natal_attendance;
use App\RCH\Tbl_post_natal_birth_info;
use App\RCH\Tbl_post_natal_breast_status;
use App\RCH\Tbl_post_natal_child_arv_prophlaxise;
use App\RCH\Tbl_post_natal_child_attendance;
use App\RCH\Tbl_post_natal_child_feeding;
use App\RCH\Tbl_post_natal_child_infection;
use App\RCH\Tbl_post_natal_child_investigation;
use App\RCH\Tbl_post_natal_child_vaccination;
use App\RCH\Tbl_post_natal_familiy_planning;
use App\RCH\Tbl_post_natal_investigation;
use App\RCH\Tbl_post_natal_observation_check;
use App\RCH\Tbl_post_natal_observation_description;
use App\RCH\Tbl_post_natal_observation_list;
use App\RCH\Tbl_post_natal_pmtct;
use App\RCH\Tbl_post_natal_referral;
use App\RCH\tbl_post_natal_register;
use App\RCH\Tbl_post_natal_tt_given_vaccination;
use App\RCH\Tbl_post_natal_tt_vaccination;
use App\RCH\Tbl_post_natal_womb_status;
use App\RCH\Tbl_postnatal_baby_feed_hour;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Post_natalController extends Controller
{
    //
    public function Post_natal_serial_no(Request $request)
    {

        $patient_id=$request['patient_id'];
        $facility_id=$request['facility_id'];
        $user_id=$request['user_id'];
        $data= Tbl_post_natal_register::where('patient_id',$patient_id)->where('year',Date('Y'))->first();
        if (count($data)>0) {
            return $data;
        }
        else{
            return  patientRegistration::Post_Natal_Serial_Number($facility_id,$patient_id,$user_id);
        }


    }

    public function post_natal_registration_update(Request $request)
    {
      $patient_id=$request['patient_id'];
      $rch_4=$request['rch_4'];
      $post_natal_reg_date=$request['post_natal_reg_date'];

    if($request['rch_4']==""){
        return response()->json([
            'msg'=>'Please Enter RCH- Number',
            'status'=>0
        ]);
    }
    else if($request['post_natal_reg_date']==""){
        return response()->json([
            'msg'=>'Please Enter Date of Post Natal Registration',
            'status'=>0
        ]);
    }



    else   {
$data=Tbl_post_natal_register::where('patient_id',$patient_id)->update([
    'rch_4'=>$rch_4,
    'post_natal_reg_date'=>$post_natal_reg_date,
]);
        return response()->json([
            'msg'=>'Successful saved',
            'status'=>1
        ]);
    }

}

    public function post_birth_info_registration(Request $request)
    {
       $patient_id=$request['patient_id'];
       $number_of_delivery=$request['number_of_delivery'];
       $delivery_date=$request['delivery_date'];
       $place_of_delivery=$request['place_of_delivery'];
       $midwife_proffesion=$request['midwife_proffesion'];
       $mother_status=$request['mother_status'];
       $number_of_newborn=$request['number_of_newborn'];
       $number_of_newborn_alive=$request['number_of_newborn_alive'];
       $number_of_newborn_died=$request['number_of_newborn_died'];


      if(  $request['delivery_date']==""){
        return response()->json([
            'msg'=>'Please Enter Date of Delivery ',
            'status'=>0
        ]);
    }
        else if(  $request['mother_status']==""){
        return response()->json([
            'msg'=>'Please Enter Status of Mother ',
            'status'=>0
        ]);
    }
        if(!is_numeric($request['number_of_delivery']) || $request['number_of_delivery']==""){
        return response()->json([
            'msg'=>'Please Enter Number of Delivery and should a number',
            'status'=>0
        ]);
    }
  else if(!is_numeric($request['number_of_newborn']) || $request['number_of_newborn']==""){
        return response()->json([
            'msg'=>'Please Enter Number of Newborn and should a number',
            'status'=>0
        ]);
    }else if(!is_numeric($request['number_of_newborn_alive']) || $request['number_of_newborn_alive']==""){
        return response()->json([
            'msg'=>'Please Enter Number of Newborn Alive and should a number',
            'status'=>0
        ]);
    }else if(!is_numeric($request['number_of_newborn_died']) || $request['number_of_newborn_died']==""){
        return response()->json([
            'msg'=>'Please Enter Number of Newborn Died and should a number',
            'status'=>0
        ]);
    }




    else if(patientRegistration::duplicate('tbl_post_natal_birth_infos',['patient_id','number_of_delivery','delivery_date','midwife_proffesion','mother_status','number_of_newborn','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id,$number_of_delivery,$delivery_date,$midwife_proffesion,$mother_status,$number_of_newborn])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else{
        $data= Tbl_post_natal_birth_info::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);


    }

    }

 public function baby_feed_registration(Request $request)
 {
     $patient_id = $request['patient_id'];
     $baby_breastfeeding_within_hour = $request['baby_breastfeeding_within_hour'];


     if ($request['baby_breastfeeding_within_hour'] == "") {
         return response()->json([
             'msg' => 'Please Fill option ',
             'status' => 0
         ]);
     } else if (patientRegistration::duplicate('tbl_postnatal_baby_feed_hours', ['patient_id', 'baby_breastfeeding_within_hour', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
             [$patient_id, $baby_breastfeeding_within_hour,]) == true
     ) {

         return response()->json([
             'msg' => 'Duplication detected.....',
             'status' => 0
         ]);
     } else {
         $data = Tbl_postnatal_baby_feed_hour::create($request->all());

         return response()->json([
             'msg' => 'Successful',
             'status' => 1
         ]);


     }
 }

public function pmtct_post_registration(Request $request)
    {
       $patient_id=$request['patient_id'];
       $anti_natal_vvu_infection_status=$request['anti_natal_vvu_infection_status'];
       $post_natal_vvu_infection_status=$request['post_natal_vvu_infection_status'];



      if(  $request['post_natal_vvu_infection_status']==""){
        return response()->json([
            'msg'=>'Please Fill option for post natal vvu infection status ',
            'status'=>0
        ]);
    }
        else if(  $request['anti_natal_vvu_infection_status']==""){
        return response()->json([
            'msg'=>'Please Fill option for anti natal vvu infection status',
            'status'=>0
        ]);
    }


    else if(patientRegistration::duplicate('tbl_post_natal_pmtcts',['patient_id','post_natal_vvu_infection_status','post_natal_vvu_infection_status','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id,$post_natal_vvu_infection_status,$anti_natal_vvu_infection_status ])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else{
        $data= Tbl_post_natal_pmtct::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);


    }

    }

public function post_reattendance_registration(Request $request)
    {
       $patient_id=$request['patient_id'];
       $attendance_range=$request['attendance_range'];
       $date_attended=$request['date_attended'];



      if(  $request['attendance_range']==""){
        return response()->json([
            'msg'=>'Please Fill option for attendance  ',
            'status'=>0
        ]);
    }
        else if(  $request['date_attended']==""){
        return response()->json([
            'msg'=>'Please Fill option  date attended',
            'status'=>0
        ]);
    }
        else if(!is_numeric($request['temperature']) || $request['temperature']==""){
            return response()->json([
                'msg'=>'Please Enter Body Temperature and should a number',
                'status'=>0
            ]);
        }

 else if(!is_numeric($request['hb']) || $request['hb']==""){
            return response()->json([
                'msg'=>'Please Enter HB and should a number',
                'status'=>0
            ]);
        }

         else if( $request['bp']==""){
            return response()->json([
                'msg'=>'Please Enter BP ',
                'status'=>0
            ]);
        }


    else if(patientRegistration::duplicate('tbl_post_natal_attendances',['patient_id','attendance_range','date_attended','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id,$attendance_range,$date_attended])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else{
        $data= Tbl_post_natal_attendance::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);


    }

    }

    public function post_natal_inv_registration(Request $request)
    {
       $patient_id=$request['patient_id'];
       $bp=$request['bp'];
       $hb=$request['hb'];



      if(!is_numeric($request['bp']) || $request['bp']==""){
        return response()->json([
            'msg'=>'Please Fill option for HB and should be Numeric or decimal value',
            'status'=>0
        ]);
    }
        else if(!is_numeric($request['hb']) || $request['hb']==""){
        return response()->json([
            'msg'=>'Please Fill option  for HB and should be Numeric or decimal value',
            'status'=>0
        ]);
    }


    else if(patientRegistration::duplicate('tbl_post_natal_investigations',['patient_id','hb','bp','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id,$hb,$bp])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else{
        $data= Tbl_post_natal_investigation::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);


    }

    }


    public function post_natal_observation_descriptions(Request $request)
    {

       $observation_id=$request['observation_id'];
       $observation=$request['observation'];



      if( $request['observation_id']==""){
        return response()->json([
            'msg'=>'Please Choose Part of Observation',
            'status'=>0
        ]);
    }



    else if(patientRegistration::duplicate('tbl_post_natal_observation_descriptions',['observation_id','observation','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$observation_id,$observation])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else{
        $data= Tbl_post_natal_observation_description::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);


    }

    }

    public function post_natal_observation_lists_registration(Request $request)
    {






      if( $request['description']==""){
        return response()->json([
            'msg'=>'Please Fill Field first',
            'status'=>0
        ]);
    }
        

    else if(patientRegistration::duplicate('tbl_post_natal_observation_lists',['description','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$request['description']])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else{
        $data= Tbl_post_natal_observation_list::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);


    }

    }
    public function post_natal_observation_lists()
    {
        return Tbl_post_natal_observation_list::get();
    }

    public function post_natal_observation_description_list($id)
    {
        return DB::table('tbl_post_natal_observation_lists')
            ->join('tbl_post_natal_observation_descriptions','tbl_post_natal_observation_descriptions.observation_id','=','tbl_post_natal_observation_lists.id')
            ->where('tbl_post_natal_observation_descriptions.observation_id',$id)
            ->get();
    }


    public function post_natal_observation_status(Request $request)
    {



      foreach ($request->all() as $observation){
          if($observation['status'] ==""){



      }
          else{
             $data= Tbl_post_natal_observation_check::create([
                  'client_id'=>$observation['client_id'],
                  'facility_id'=>$observation['facility_id'],
                  'user_id'=>$observation['user_id'],
                  'observation_id'=>$observation['observation_id'],
                  'status'=>$observation['status'],
              ]);
          }
      }


        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);
}

    public function dehiscence_registration(Request $request)
    {

       $patient_id=$request['patient_id'];
       $dehiscence_join=$request['dehiscence_join'];
       $mental_ability=$request['mental_ability'];
       $fistula=$request['fistula'];





      if( $request['dehiscence_join']==""){
        return response()->json([
            'msg'=>'Please Fill option for Dehiscence Status',
            'status'=>0
        ]);
    }
        else if(  $request['mental_ability']==""){
        return response()->json([
            'msg'=>'Please Fill option  for Mental Ability',
            'status'=>0
        ]);
    }
        else if(  $request['fistula']==""){
        return response()->json([
            'msg'=>'Please Fill option  for Fistula Status',
            'status'=>0
        ]);
    }


    else if(patientRegistration::duplicate('tbl_post_dehiscence_fistula_mental_statuses',['patient_id','dehiscence_join','mental_ability','fistula','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id,$dehiscence_join,$mental_ability,$fistula])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else{
        $data= Tbl_post_dehiscence_fistula_mental_status::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);


    }

    }




    public function post_additional_medication_registration(Request $request)
    {

       $patient_id=$request['patient_id'];
       $ferrous_sulphate=$request['ferrous_sulphate'];
       $fs_quantity=$request['fs_quantity'];
       $folic_acid=$request['folic_acid'];
       $fa_quantity=$request['fa_quantity'];
       $vitamin_a=$request['vitamin_a'];





      if( $request['ferrous_sulphate']==""){
        return response()->json([
            'msg'=>'Please Fill option for Ferrous Sulphate',
            'status'=>0
        ]);
    }
        else if( !is_numeric($request['fs_quantity']) && $request['fs_quantity']!=""){
        return response()->json([
            'msg'=>'Please Fill Quantity of Ferrous Sulphate given as Number',
            'status'=>0
        ]);
    }
        if( $request['folic_acid']==""){
            return response()->json([
                'msg'=>'Please Fill option for Folic Acid',
                'status'=>0
            ]);
        }
        else if( !is_numeric($request['fa_quantity']) && $request['fa_quantity']!=""){
            return response()->json([
                'msg'=>'Please Fill Quantity of Folic given as Number',
                'status'=>0
            ]);
        }
        else if($request['vitamin_a']==""){
            return response()->json([
                'msg'=>'Please Fill option  for Vitamin A Given',
                'status'=>0
            ]);
    }



    else if(patientRegistration::duplicate('tbl_post_natal_additional_medications',['patient_id','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else{
        $data= Tbl_post_natal_additional_medication::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);


    }

    }

    public function post_family_planing_registration(Request $request)
    {

       $patient_id=$request['patient_id'];
       $referral_for_family_planning=$request['referral_for_family_planning'];
       $iec_material_given=$request['iec_material_given'];
       $counselling_given=$request['counselling_given'];

      if( $request['counselling_given']==""){
        return response()->json([
            'msg'=>'Please Fill option for Counselling',
            'status'=>0
        ]);
    }
        else if(   $request['iec_material_given']==""){
        return response()->json([
            'msg'=>'Please Fill Quantity of IEC Material',
            'status'=>0
        ]);
    }  else if(   $request['referral_for_family_planning']==""){
        return response()->json([
            'msg'=>'Please Fill Option For Referral',
            'status'=>0
        ]);
    }



    else if(patientRegistration::duplicate('tbl_post_natal_familiy_plannings',['patient_id','counselling_given','iec_material_given','referral_for_family_planning','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id,$counselling_given,$iec_material_given,$referral_for_family_planning])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else{
        $data= Tbl_post_natal_familiy_planning::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);


    }

    }

public function post_chilreattendance_registration(Request $request)
    {

       $patient_id=$request['patient_id'];
       $date_attended=$request['date_attended'];
       $attendance_range=$request['attendance_range'];



          if(   $request['attendance_range']==""){
        return response()->json([
            'msg'=>'Please Fill Quantity of Attendance Range',
            'status'=>0
        ]);
    }  else if(   $request['date_attended']==""){
        return response()->json([
            'msg'=>'Please Fill Date Attended',
            'status'=>0
        ]);
    }

          else if(!is_numeric($request['temperature']) || $request['temperature']==""){
              return response()->json([
                  'msg'=>'Please Enter Body Temperature and should a number',
                  'status'=>0
              ]);
          }

          else if(!is_numeric($request['hb']) || $request['hb']==""){
              return response()->json([
                  'msg'=>'Please Enter HB and should a number',
                  'status'=>0
              ]);
          }

          else if( $request['bp']==""){
              return response()->json([
                  'msg'=>'Please Enter BP  ',
                  'status'=>0
              ]);
          }

    else if(patientRegistration::duplicate('tbl_post_natal_child_attendances',['patient_id','attendance_range','date_attended','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id,$attendance_range,$date_attended])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else{
        $data= Tbl_post_natal_child_attendance::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);


    }

    }

public function post_child_inv_registration(Request $request)
    {

       $patient_id=$request['patient_id'];
       $temperature=$request['temperature'];
       $weight=$request['weight'];
       $hb=$request['hb'];
       $kmc=$request['kmc'];

      if( !is_numeric($request['temperature']) || $request['temperature']==""){
        return response()->json([
            'msg'=>'Please Fill Temperature and should a Number',
            'status'=>0
        ]);
    }
        else if( !is_numeric($request['weight']) || $request['weight']==""){
        return response()->json([
            'msg'=>'Please Fill Weight and should a Number',
            'status'=>0
        ]);
    }
        else if( !is_numeric($request['hb']) || $request['hb']==""){
        return response()->json([
            'msg'=>'Please Fill HB and should a Number',
            'status'=>0
        ]);
    } else if( $request['kmc']==""){
        return response()->json([
            'msg'=>'Please Fill KMC',
            'status'=>0
        ]);
    }




    else if(patientRegistration::duplicate('tbl_post_natal_child_investigations',['patient_id','temperature','weight','hb','kmc','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id,$temperature,$weight,$hb,$kmc])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else{
        $data= Tbl_post_natal_child_investigation::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);


    }

    }

    public function post_tt_vaccination_registration(Request $request)
    {

       $patient_id=$request['patient_id'];
       $user_id=$request['user_id'];
       $facility_id=$request['facility_id'];
       $vaccination_id=$request['vaccination_id'];

       $number_of_tt_given=$request['number_of_tt_given'];
       $enough=$request['enough'];
       $tt_name=$request['tt_name'];
       $vaccination_date=$request['vaccination_date'];





      if( $request['enough']=="NO" && $request['vaccination_date']=="" ){
        return response()->json([
            'msg'=>'Please Fill Date for '.$tt_name,
            'status'=>0
        ]);
    }

         else if( !is_numeric($request['number_of_tt_given']) || $request['number_of_tt_given']==""){
            return response()->json([
                'msg'=>'Please Fill TT  Given Quantity',
                'status'=>0
            ]);
    }

if($request['enough']=="NO")
{

 if(patientRegistration::duplicate('tbl_post_natal_tt_given_vaccinations',['patient_id','number_of_tt_given','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id,$number_of_tt_given])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else{
        $data= Tbl_post_natal_tt_given_vaccination::create([
            'patient_id'=>$patient_id,
            'user_id'=>$user_id,
            'facility_id'=>$facility_id,
            'number_of_tt_given'=>$number_of_tt_given,
        ]);

        $data= Tbl_post_natal_tt_vaccination::create([
            'patient_id'=>$patient_id,
            'user_id'=>$user_id,
            'facility_id'=>$facility_id,
            'vaccination_id'=>$vaccination_id,

            'vaccination_date'=>$vaccination_date,
        ]);

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);



    }


}
else{
    if(patientRegistration::duplicate('tbl_post_natal_tt_given_vaccinations',['patient_id','number_of_tt_given','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id,$number_of_tt_given])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }


    else{
        $data= Tbl_post_natal_tt_given_vaccination::create([
            'patient_id'=>$patient_id,
            'user_id'=>$user_id,
            'facility_id'=>$facility_id,
            'number_of_tt_given'=>$number_of_tt_given,
        ]);

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);



    }
}

    }


    public function child_vaccination_registration(Request $request)
    {

        if($request['vaccination_id']==""){
            return response()->json([
                'msg'=>'Please choose Vaccination',
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



        $dup=Tbl_post_natal_child_vaccination::where('vaccination_id',$request['vaccination_id'])
            ->where('patient_id',$request['patient_id'])->get();

        if(count($dup)>0){
            return response()->json([
                'msg'=>'Oops!.. Duplication or Double entry detected.. System detected that, you are entering a
                    Same data set more than once....',
                'status'=>0
            ]);
        }
        $data= Tbl_post_natal_child_vaccination::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);




    }

    public function child_infection_registration(Request $request)
    {
$patient_id=$request['patient_id'];
$navel=$request['navel'];
$skin=$request['skin'];
$mouth=$request['mouth'];
$eye=$request['eye'];
$jaundice=$request['jaundice'];
$high_infection=$request['high_infection'];
        if($request['navel']==""){
            return response()->json([
                'msg'=>'Please choose Option For Navel Status',
                'status'=>0
            ]);
        }

        else if($request['skin']==""){
            return response()->json([
                'msg'=>'Please choose Option  For Skin Status',
                'status'=>0
            ]);
        } else if($request['mouth']==""){
            return response()->json([
                'msg'=>'Please choose Option  For Mouth Status',
                'status'=>0
            ]);
        }else if($request['eye']==""){
            return response()->json([
                'msg'=>'Please choose Option  For Eye Status',
                'status'=>0
            ]);
        }else if($request['jaundice']==""){
            return response()->json([
                'msg'=>'Please choose Option  For Jaundice Status',
                'status'=>0
            ]);
        }else if($request['high_infection']==""){
            return response()->json([
                'msg'=>'Please choose Option  For High Infection Status',
                'status'=>0
            ]);
        }


else{
    if(patientRegistration::duplicate('tbl_post_natal_child_infections',['patient_id','navel','skin','mouth','eye','jaundice','high_infection','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id,$navel,$skin,$mouth,$eye,$jaundice,$high_infection])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }
    else{
        $data= Tbl_post_natal_child_infection::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);

    }
}

    }


    public function post_natal_arv_registration(Request $request)
    {
$patient_id=$request['patient_id'];
$arv_id=$request['arv_id'];
$time=$request['time'];

        if($request['arv_id']==""){
            return response()->json([
                'msg'=>'Please choose ARV Item Name',
                'status'=>0
            ]);
        }

        else if($request['time']=="") {
            return response()->json([
                'msg' => 'Please Enter Time For ARV Usage',
                'status' => 0
            ]);
        }


else{
    if(patientRegistration::duplicate('tbl_post_natal_child_arv_prophlaxises',['patient_id','arv_id','time','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id,$arv_id,$time])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }
    else{
        $data= Tbl_post_natal_child_arv_prophlaxise::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);

    }
}


    }

    public function post_natal_feeding_registration(Request $request)
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
    if(patientRegistration::duplicate('tbl_post_natal_child_feedings',['patient_id','feeding_type','((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
            [$patient_id,$feeding_type,])==true) {

        return response()->json([
            'msg'=>'Duplication detected.....',
            'status'=>0
        ]);
    }
    else{
        $data= Tbl_post_natal_child_feeding::create($request->all());

        return response()->json([
            'msg'=>'Successful',
            'status'=>1
        ]);

    }
}


    }


    public function post_referral_registration(Request $request)
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




        else if(patientRegistration::duplicate('tbl_post_natal_referrals',['patient_id','transfered_institution_id','date','reason',
                '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$transfered_institution_id,$date,$reason ])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            $data= Tbl_post_natal_referral::create($request->all());

            return response()->json([
                'msg'=>'Successful',
                'status'=>1
            ]);
        }


    }





}