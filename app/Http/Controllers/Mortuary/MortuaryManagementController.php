<?php

namespace App\Http\Controllers\nursing_care;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\classes\patientRegistration;
use App\nursing_care\Tbl_wards_type;
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
use DB;

class MortuaryManagementController extends Controller
{

    public function addMortuaryClass(Request $request)
    {
        //vw_pending_admission
        $sql="SELECT * FROM `vw_pending_admission` t1 WHERE t1.admission_id NOT IN (
    SELECT t2.admission_id 
    FROM vw_approved_admission t2 )";
        return DB::select($sql);

    }

    public function getPendingAdmissionList(Request $request)
    {
        //vw_pending_admission
        $sql="SELECT * FROM `vw_pending_admission` t1 WHERE t1.admission_id NOT IN (
    SELECT t2.admission_id 
    FROM vw_approved_admission t2 )";
        return DB::select($sql);

    }

    public function getAprovedAdmissionList(Request $request)  {
        //vw_pending_admission
        $sql="SELECT * FROM `vw_approved_admission` t1 WHERE t1.admission_id NOT IN (
    SELECT t2.admission_id 
    FROM tbl_discharge_permits t2 WHERE confirm=1)";
        return DB::select($sql);

    }

    public function getIntakeSolutions(Request $request)   {
        //sub_item_category must be of the type solutions..
        $sql="SELECT * FROM `vw_shop_items` t1 WHERE t1.sub_item_category='SOLUTION'";
        return DB::select($sql);

    }

    public function getVital(Request $request)    {
        return Tbl_observation_type::all();
    }

    public function getTeethAbove(Request $request)    {
        return Tbl_teeth_arrangement::WHERE("teeth_position",'A')->get();
    }

    public function getTeethBelow(Request $request)    {
        return Tbl_teeth_arrangement::WHERE("teeth_position",'B')->get();
    }

    public function getTeethStatusFromPatientAbove(Request $request)    {
        $request_id=$request->request_id;

        $sql="SELECT teeth_number,css_class FROM tbl_teeth_patients t1,tbl_teeth_arrangements t2 WHERE t1.dental_id=t2.id AND t1.request_id='{$request_id}' AND t2.teeth_position='A'";
        return DB::select($sql);
    }

    public function getTeethStatusFromPatientBelow(Request $request)    {
        $request_id=$request->request_id;

        $sql="SELECT teeth_number,css_class FROM tbl_teeth_patients t1,tbl_teeth_arrangements t2 WHERE t1.dental_id=t2.id AND t1.request_id='{$request_id}' AND t2.teeth_position='B'";
        return DB::select($sql);
    }



    public function saveTeethStatus(Request $request)    {
        $teeth=Tbl_teeth_arrangement::all();
        $admission_id=$request->admission_id;
        $nurse_id=$request->nurse_id;
        $request_id=$request->request_id;
        $information_category=$request->information_category;
        $dental_status=$request->dental_status;
        $css_class=$request->css_class;
        $dental_id=$request->dental_id;
        if(patientRegistration::duplicate('tbl_teeth_patients',array('request_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($request_id))==false){

            foreach($teeth as $tooth){
                $arrayPassion=array("dental_id"=>$tooth->id,"dental_status"=>0,"css_class"=>'',"admission_id"=>$admission_id,"request_id"=>$request_id,"nurse_id"=>$nurse_id,"other_information"=>'DENTAL STATUS',"erasor"=>0);
                $dentals=Tbl_teeth_patient::create($arrayPassion);
            }
        }
        if(Tbl_teeth_patient::WHERE("dental_id",$dental_id)->WHERE("request_id",$request_id)->update(array("dental_status"=>$dental_status,"css_class"=>$css_class))){

            return response()->json(['data' =>'SUCCEFULLY SAVED',
                'status' => '1'
            ]);
        }else{
            return response()->json(['data' =>'ERROR IN UPDATING...',
                'status' => '0'
            ]);

        }
    }

    public function getDiagnosis(Request $request)    {
        return Tbl_nursing_diagnosise::all();
    }

    public function getOutPutTypes(Request $request)    {
        return Tbl_observations_output_type::all();
    }

    public function addVitals(Request $request)    {
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


    public function saveStatusAnaesthetic(Request $request)    {
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

    public function saveAssociateHistory(Request $request)   {
        $medical=$request->medical;
        $surgical=$request->surgical;
        $admission_id=$request->admission_id;
        $request_id=$request->request_id;
        $information_category=$request->information_category;
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



        else if(patientRegistration::duplicate('tbl_surgery_histories',array('request_id','information_category',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=3))"), array($request_id,$information_category))==true){
            return response()->json(['data' =>'ASSOCIATE HISTORY ALREADY EXISTS FOR THIS PATIENT',
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


    public function saveSocialHistory(Request $request)    {
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

    public function saveResipratorySystem(Request $request)    {
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

    public function addGoals(Request $request)    {
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


    public function addDrugs(Request $request)    {
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


    public function addDischargeNotes(Request $request)    {
        $permission_date=$request->permission_date;
        $domestic_dosage=$request->domestic_dosage;
        $admission_id=$request->admission_id;
        $followup_date=$request->followup_date;
        $confirm=$request->confirm;
        $nurse_id=$request->nurse_id;

        //$timediff=(time()-(60*60*24)) - strtotime($followup_date);

        if(!is_numeric($admission_id)){

            return response()->json([	'data' =>'SELECT PATIENT TO DISCHARGE',
                'status' => '0'
            ]);
        }else if(empty($domestic_dosage)){

            return response()->json([	'data' =>' PLEASE PROVIDE ANY REMARKS ',
                'status' => '0'
            ]);
        }

        else if((time()-(60*60*24)) > strtotime($followup_date)){

            return response()->json([	'data' =>'PREVIOUS DATE WAS SELECTED',
                'status' => '0'
            ]);
        }




        else if(patientRegistration::duplicate('tbl_discharge_permits',array('nurse_id','admission_id','confirm',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"),
                array($nurse_id,$admission_id,$confirm))==true){
            return response()->json(['data' =>' PATIENT ALREADY DISCHARGED',
                'status' => '0'
            ]);

        }else{
            if($followup_date == $permission_date){
                $followup_date='';
            }
            if(Tbl_discharge_permit::create(array("admission_id"=>$admission_id,"confirm"=>1,"nurse_id"=>$nurse_id,"permission_date"=>$permission_date,"domestic_dosage"=>$domestic_dosage,"followup_date"=>$followup_date))){
                //then i have to release BED ...
                return response()->json(['data' =>' PATIENT WAS SUCCESSFULLY DISCHARGED',
                    'status' => '1'
                ]);
            }

        }

    }


    public function enterTheatre(Request $request)    {
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

        $patient_id=$request->patient_id;
        $ward_id=$request->ward_id;
        $getInstructions=  DB::table('tbl_instructions')
            ->where('patient_id',$patient_id)
            ->get();
        return $getInstructions;

    }

    public function getBedsWithNoPatients(Request $request)
    {
        $ward_id=$request->ward_id;
        $getBedsWithNoPatients =DB::table('vw_beds')
            ->where('ward_id',$ward_id)
            ->where('occupied',0)
            ->get();
        return $getBedsWithNoPatients;
    }
    public function giveBed(Request $request)
    {

        $ward_id=$request->ward_id;
        $bed_id=$request->bed_id;
        $admission_id=$request->admission_id;
        $getBedsWithNoPatients =DB::table('vw_beds')
            ->where('ward_id',$ward_id)
            ->where('bed_id',$bed_id)
            ->where('occupied',0)
            ->get();

        $undo_beds =DB::table('tbl_instructions')
            ->where('admission_id',$admission_id)
            ->get();


        $bedresult_id=$undo_beds[0]->bed_id;



        if(!is_numeric($bedresult_id)){

            if(count($getBedsWithNoPatients)==1){

                if(Tbl_bed::WHERE("id",$bed_id)->update(array("occupied"=>1))){

                    Tbl_admission::WHERE("id",$admission_id)->update(array("admission_status_id"=>2));
                    Tbl_instruction::WHERE("admission_id",$admission_id)->update(array("bed_id"=>$bed_id));
                }
                return response()->json([
                    'data' =>'BED WAS SUCCEFULLY GIVEN TO PATIENT',
                    'status' => '1'
                ]);
            }
        }else if (is_numeric($bedresult_id)){

            if(Tbl_bed::WHERE("id",$bed_id)->update(array("occupied"=>1))){
                Tbl_bed::WHERE("id",$bedresult_id)->update(array("occupied"=>0));

                Tbl_instruction::WHERE("admission_id",$admission_id)->update(array("bed_id"=>$bed_id));

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

    public function getWards(Request $request)
    {
        $facility_id=$request->facility_id;
        $wards=DB::table('vw_wards')
            ->where('facility_id',$facility_id)
            ->get();
        return $wards;

    }

    public function getWardOneInfo(Request $request)
    {
        $ward_id=$request->ward_id;
        $sql="SELECT ward_full_name,ward_id,count(*) AS beds_number  
			     FROM `vw_beds` WHERE ward_id='{$ward_id}' GROUP BY ward_id";
        $wards=DB::select($sql);

        if(count($wards)==0){
            $sql="SELECT ward_full_name,ward_id,(count(*)-1) AS beds_number  
			     FROM `vw_wards` WHERE ward_id='{$ward_id}' GROUP BY ward_id";
            $ward=DB::select($sql);
            return $ward;

        }else{

            return $wards;

        }

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

        }else if(!is_numeric($ward_type)){
            return response()->json([
                'data' => 'WARD TYPE MUST BE SELECTED FROM THE SUGESTION LIST',
                'status' => '0'
            ]);

        }
        else if(!is_numeric($ward_class_id)){
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




}