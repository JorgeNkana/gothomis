<?php

namespace App\Http\Controllers\TB;

use App\classes\patientRegistration;
use App\Clinics\Tbl_clinic_instruction;
use App\Ent\Tbl_past_dermatology_history;
use App\Ent\Tbl_past_diabetic_history;
use App\General_appointment\Tbl_configuration;
use App\General_appointment\Tbl_general_appointment;
use App\Nutrition\Tbl_client_nutritional_status;
use App\Nutrition\Tbl_nutritional_food;
use App\Nutrition\Tbl_nutritional_status;
use App\Nutrition\Tbl_nutritional_suppliment;
use App\Orthopedic\Tbl_past_orthopedic_history;
use App\Ent\Tbl_past_ent_history;
use App\Patient\Tbl_patient;
use App\TB\Tbl_patient_tb_type_register;
use App\TB\Tbl_tb_patient_medication_followup;
use App\TB\Tbl_tb_patient_treatment_output;
use App\TB\Tbl_tb_patient_treatment_type;
use App\TB\Tbl_tb_pre_entry_register;
use App\TB\Tbl_tb_sputam_test_followup;
use App\TB\Tbl_tb_treatment_type;
use App\TB\Tbl_tb_vvu_service;
use App\Urology\Tbl_past_urology_history;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\classes\SystemTracking;
use App\Trackable;
use DateTime;
use Illuminate\Support\Facades\DB;

class Tb_Controller extends Controller
{
    //
    public function tb_pre_entry_registration(Request $request)
    {
       $transfer_id=$request['transfer_id'];
       $client_id=$request['client_id'];
       $user_id=$request['user_id'];
       $facility_id=$request['facility_id'];
       $client_type=$request['client_type'];
       $referral_type=$request['referral_type'];

        if(patientRegistration::duplicate('tbl_tb_pre_entry_registers',['client_id','client_type','referral_type', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$client_id,$client_type,$referral_type])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
            else{
               $data= Tbl_tb_pre_entry_register::create([
                    'facility_id'=>$facility_id,
                    'user_id'=>$user_id,
                    'client_id'=>$client_id,
                    'client_type'=>$client_type,
                    'referral_type'=>$referral_type,
                ]);

               if($transfer_id>0) {
                   Tbl_clinic_instruction::where('id',$transfer_id)->update(['received'=>1]);

               }

                return response()->json([
                    'msg'=>'Successful data saved',
                    'status'=>1
                ]);
    }
        
    }
    public function patient_tb_type_registration(Request $request)
    {
       $client_id=$request['client_id'];
       $tb_type=$request['tb_type'];
        if(patientRegistration::duplicate('tbl_patient_tb_type_registers',['client_id','tb_type', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$client_id,$tb_type])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
            else{
                Tbl_patient_tb_type_register::create($request->all());
                return response()->json([
                    'msg'=>'Successful data saved',
                    'status'=>1
                ]);
    }

    }
    
    public function patient_tb_sputam_registration(Request $request)
    {
       $client_id=$request['client_id'];
       $month=$request['month'];
        if(patientRegistration::duplicate('tbl_tb_sputam_test_followups',['client_id','month', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$client_id,$month])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
            else{
                Tbl_tb_sputam_test_followup::create($request->all());
                return response()->json([
                    'msg'=>'Successful data saved',
                    'status'=>1
                ]);
    }

    }

    public function patient_tb_treatment_types(Request $request)
    {
        
       $client_id=$request['client_id'];
       $treatment_type_id=$request['tb_treatment_type_id'];
       $treatment_place=$request['treatment_place'];
        if(patientRegistration::duplicate('tbl_tb_patient_treatment_types',['client_id','tb_treatment_type_id','treatment_place', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$client_id,$treatment_type_id,$treatment_place])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
            else{
                Tbl_tb_patient_treatment_type::create($request->all());
                return response()->json([
                    'msg'=>'Successful data saved',
                    'status'=>1
                ]);
    }

    }
    public function patient_tb_vvu_service(Request $request)
    {

       $client_id=$request['client_id'];
       $cpt=$request['cpt'];
       $cpt_start_date=$request['cpt_start_date'];
       $art_start_date=$request['art_start_date'];


        if(patientRegistration::duplicate('tbl_tb_vvu_services',['client_id','cpt', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$client_id,$cpt])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
            else{
                Tbl_tb_vvu_service::create($request->all());
                return response()->json([
                    'msg'=>'Successful data saved',
                    'status'=>1
                ]);
    }

    }

    public function tb_patient_medication_followups(Request $request)
    {

       $client_id=$request['client_id'];
       $month=$request['month'];
       $remark=$request['remark'];

$data=Tbl_tb_patient_medication_followup::where('month',$month)->where('client_id',$client_id)->count();

        if($data>0) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
            else{
                Tbl_tb_patient_medication_followup::create($request->all());
                return response()->json([
                    'msg'=>'Successful data saved',
                    'status'=>1
                ]);
    }

    }

    public function Tbl_tb_patient_treatment_outputs(Request $request)
    {

        $client_id=$request['client_id'];
        $output=$request['output'];
        $comment=$request['comment'];

        if(patientRegistration::duplicate('tbl_tb_patient_treatment_outputs',['client_id','output', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$client_id,$output])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        else{
            Tbl_tb_patient_treatment_output::create($request->all());
            return response()->json([
                'msg'=>'Successful data saved',
                'status'=>1
            ]);
        }

    }

    public function treatment_types()
    {
       return Tbl_tb_treatment_type::get();
    }

    public  function patientAge($patientAge){


        $last_visit=$patientAge;

        $today_date_time=date('Y-m-d');
        $bday = new DateTime($last_visit);
        $today = new DateTime($today_date_time);
        // $today = new DateTime($edd);
        $diff = $today->diff($bday);
        $month= $diff->m;
        $year= $diff->y;
        $days= $diff->d;

        return response()->json([
            'age'=>$year.'  Year(s) '.$month. '  Month(s) ',
            'day'=>$days.'  Day(s) ',
            'month'=>$month,
            'year'=>$year,


        ]);
    }

    public function searchClinicpatientFromDb($search)
    {
      return Tbl_patient::
      where('medical_record_number','like','%'.$search.'%')
          ->select('id as patient_id','first_name',
               'middle_name',
               'last_name',
              'medical_record_number',
             'gender',
              'dob')
      ->orWhere('first_name','like','%'.$search.'%')
      ->orWhere('last_name','like','%'.$search.'%')
      ->orWhere('middle_name','like','%'.$search.'%')
      ->orWhere('mobile_number','like','%'.$search.'%')
          ->take(5)->get()  ;
    }

    public function searchClinicpatient($facility_id)
    {
      return DB::table('vw_special_clinics_clients')
          ->where('facility_id',$facility_id)
          ->where('dept_id',15)
          ->where('received',0)
          ->take(10)->get()  ;
    }
	
	public function postpast_orthopedic(Request $request)
    {
        //return $request->all();
        if(patientRegistration::duplicate('tbl_past_orthopedic_histories',['patient_id','past_orthopedic', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$request['patient_id'],$request['past_orthopedic']])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
            $postData = Tbl_past_orthopedic_history ::create($request->all());

        return response()->json([
            'msg'=>'Saved',
            'status'=>1
        ]);

    }


    public function OrthHistory(Request $request)
    {


        $id = $request->input('patient_id');
        $visit_id = $request->input('visit_id');
        $sql = DB::select("select * from tbl_past_orthopedic_histories where visit_date_id = $visit_id 
        ");

        return $sql;
    }


//ent...
public function savepast_ent(Request $request)
    {
        //return $request->all();
        if(patientRegistration::duplicate('tbl_past_ent_histories',['patient_id','past_ent', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$request['patient_id'],$request['past_ent']])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
            $postData = Tbl_past_ent_history ::create($request->all());

        return response()->json([
            'msg'=>'Saved',
            'status'=>1
        ]);

    }

    public function EntHistory(Request $request)
    {


        $id = $request->input('patient_id');
        $visit_id = $request->input('visit_date_id');
        $sql = DB::select("select * from tbl_past_ent_histories where visit_date_id = '".$visit_id."' ");

        return $sql;
    }

    public function savepast_diabetic(Request $request)
    {
        //return $request->all();
        if(patientRegistration::duplicate('tbl_past_ent_histories',['patient_id','past_ent', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$request['patient_id'],$request['past_ent']])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
            $postData = Tbl_past_diabetic_history ::create($request->all());

        return response()->json([
            'msg'=>'Saved',
            'status'=>1
        ]);

    }
 public function savepast_dermatology(Request $request)
    {
        //return $request->all();
        if(patientRegistration::duplicate('tbl_past_dermatology_histories',['patient_id','past_dermatology', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$request['patient_id'],$request['past_dermatology']])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
            $postData = Tbl_past_dermatology_history ::create($request->all());

        return response()->json([
            'msg'=>'Saved',
            'status'=>1
        ]);

    }

    public function DiabeticHistory(Request $request)
    {


        $id = $request->input('patient_id');
        $visit_id = $request->input('visit_date_id');
        $sql = DB::select("select * from tbl_past_diabetic_histories where visit_date_id = '".$visit_id."' ");

        return $sql;
    }
    public function DermatologyHistory(Request $request)
    {


        $id = $request->input('patient_id');
        $visit_id = $request->input('visit_date_id');
        $sql = DB::select("select * from tbl_past_dermatology_histories where visit_date_id = '".$visit_id."' ");

        return $sql;
    }
    public function savepast_urology(Request $request)
    {
        //return $request->all();
        if(patientRegistration::duplicate('tbl_past_urology_histories',['patient_id','past_urology', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=1))'],
                [$request['patient_id'],$request['past_urology']])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
            $postData = Tbl_past_urology_history ::create($request->all());

        return response()->json([
            'msg'=>'Saved',
            'status'=>1
        ]);

    }


    public function getorthHistory(Request $request)
    {


        $id = $request->input('patient_id');
        $visit_id = $request->input('visit_date_id');
        $sql = DB::select("select * from tbl_past_orthopedic_histories where visit_date_id = '".$visit_id."' ");

        return $sql;
    } 
    public function uroloHistory(Request $request)
    {


        $id = $request->input('patient_id');
        $visit_id = $request->input('visit_id');
        $sql = DB::select("select * from tbl_past_urology_histories where visit_date_id ='".$visit_id."' 
        ");

        return $sql;
    }

    public function getpatient_tb_history(Request $request)
    {


        // $id = $request->input('patient_id');
        $visit_id = $request->input('visit_date_id');
        $all=[];
        $all[] = DB::select("select * from tbl_patient_tb_type_registers t1 join tbl_accounts_numbers t2 on t1.client_id =t2.patient_id where t2.id =  '".$visit_id."'  ");
        $all[] = DB::select("select treatment_place,type from tbl_tb_patient_treatment_types t1 join tbl_accounts_numbers t2 on t1.client_id =t2.patient_id join  tbl_tb_treatment_types t3 on t3.id =t1.tb_treatment_type_id where t2.id = ".$visit_id."  ");
        $all[] = DB::select("select * from tbl_tb_vvu_services t1 join tbl_accounts_numbers t2 on t1.client_id =t2.patient_id where t2.id='".$visit_id."'  ");
        $all[] = DB::select("select * from tbl_tb_sputam_test_followups t1 join tbl_accounts_numbers t2 on t1.client_id =t2.patient_id where t2.id=  '".$visit_id."'  ");
        $all[] = DB::select("select * from tbl_tb_patient_medication_followups t1 join tbl_accounts_numbers t2 on t1.client_id =t2.patient_id where t2.id= '".$visit_id."'  ");
        $all[] = DB::select("select * from tbl_tb_patient_treatment_outputs t1 join tbl_accounts_numbers t2 on t1.client_id =t2.patient_id where t2.id =  '".$visit_id."'  ");

        return $all;
    }






    public function NutritionHistory(Request $request)
    {


        $id = $request->input('patient_id');
        $visit_id = $request->input('visit_id');
        $sql=[];

        $sql[] = DB::select("select * from tbl_nutritional_statuses where visit_id = ".$visit_id." ");
        $sql[] = DB::select("select t2.suppliment_name from tbl_nutritional_foods t1 join tbl_nutritional_suppliments t2 on t1.suppliment_id=t2.id where visit_id = ".$visit_id." ");
        $sql[] = DB::select("select * from tbl_client_nutritional_statuses where visit_id = ".$visit_id." ");
        $sql[] = DB::select("select * from  tbl_pediatric_nutritionals where client_id = ".$id." ");

        return $sql;
    }

    public function Save_nutritional_consultations(Request $request)
    {

        if(patientRegistration::duplicate('tbl_nutritional_statuses',['patient_id','nutritional_status', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$request['patient_id'],$request['nutritional_status']])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        Tbl_nutritional_status::create($request->all());
        return response()->json([
            'msg'=>'Nutritional consultations SAVED',
            'status'=>1
        ]);
    }
    public function Save_nutritional_supplimentray(Request $request)
    {

        if(patientRegistration::duplicate('tbl_nutritional_foods',['patient_id', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$request['patient_id']])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        Tbl_nutritional_food::create($request->all());
        return response()->json([
            'msg'=>'Nutritional Suppliment SAVED',
            'status'=>1
        ]);
    }
    public function Save_general_appointments(Request $request)
    {
if(patientRegistration::duplicate('tbl_general_appointments',['patient_id','dept_id','status', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$request['patient_id'],$request['dept_id'],$request['status']])==true) {

            return response()->json([
                'msg'=>'Appointment Repetition detected.....',
                'status'=>0
            ]);
        }
       $newData= Tbl_general_appointment::create($request->all());
        //$url=(isset($_SERVER['HTTPS']) ? "https" : "http") ."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $oldData=null;
        $user_id=$newData->user_id;
        $patient_id=$newData->patient_id;
        $trackable_id=$newData->id;
SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,$oldData);

        return response()->json([
            'msg'=>'Appointment SAVED',
            'status'=>1
        ]);    }
    public function Update_general_appointment(Request $request)
    {

       $status=Tbl_general_appointment::where('id',$request['id'])->first();

        if($status['status']==1){
            return response()->json([
                'msg'=>'Appointment Done no Need to Be Updated Again ... Or Create Another Appointment',
                'status'=>0
            ]);
        }
        $oldData= Tbl_general_appointment::where('id',$request['id'])->first();
        Tbl_general_appointment::where('id',$request['id'])->update($request->all());
        $newData=$status;
        $user_id=$newData->user_id;
        $patient_id=$newData->patient_id;
        $trackable_id=$newData->id;
     
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,$oldData);
        return response()->json([
            'msg'=>'Appointment Updated.. '.$request['appoint_date'],
            'status'=>1
        ]);    }

    public function dept_user_configure(Request $request)
    {
        $filled=[];
        foreach ($request->all() as $user_store){
            $data= Tbl_configuration::
            where('user_id',$user_store['user_id'])
                ->  where('dept_id',$user_store['dept_id'])
                ->  where('status',1)
                ->first();
            $scount=count($data);
            if($scount>0) {
                $filled=response()->json([
                    'department_name'=>$data->dept_id,
                    'user_id'=>$data->user_id
                ]);

            }
            else{
                $data= Tbl_configuration::create([
                    'user_id'=> $user_store['user_id'],
                    'dept_id'=> $user_store['dept_id'],
                ]);
            }



        }

            return response()->json([
                'msg'=>'Success full assigned',
                'status'=>1
            ]);

    }
    public function SelectedUserWithDeptAccess($user_id)
    {
        return DB::table('tbl_configurations')
            ->join('tbl_departments','tbl_departments.id','=','tbl_configurations.dept_id')
            ->Where('tbl_configurations.user_id',$user_id)
            ->where('tbl_configurations.status',1)
            ->select('tbl_departments.*','tbl_configurations.id as access_id')
            ->get();
    }

    public function Remove_user_dept_access($id)
    {

        return Tbl_configuration::where('id',$id)->update(['status'=>0]);
    }
    public function appointment_list(Request $request)
    {
       return DB::table('tbl_general_appointments')
           ->join('tbl_departments','tbl_general_appointments.dept_id','=','tbl_departments.id')
           ->join('tbl_patients','tbl_general_appointments.patient_id','=','tbl_patients.id')
           ->join('tbl_configurations','tbl_general_appointments.dept_id','=','tbl_configurations.dept_id')
           ->join('users','users.id','=','tbl_configurations.user_id')
           ->select('tbl_general_appointments.*','users.name','tbl_departments.department_name','tbl_patients.first_name'
               ,'tbl_patients.middle_name'
               ,'tbl_patients.last_name'
               ,'tbl_patients.gender'
               ,'tbl_patients.dob'
               ,'tbl_patients.medical_record_number'
           )
           ->where('tbl_configurations.status',1)
           ->where('tbl_configurations.user_id',$request['user_id'])
           ->where('tbl_general_appointments.facility_id',$request['facility_id'])
           ->where('tbl_general_appointments.appoint_date',$request['appoint_date'])
           ->where('tbl_general_appointments.status','=',0)
           ->orWhere('tbl_general_appointments.status','=',3)
           ->get();
    }

    public function today_appointments(Request $request)
    {
       return DB::table('tbl_general_appointments')
           ->join('tbl_departments','tbl_general_appointments.dept_id','=','tbl_departments.id')
           ->join('tbl_patients','tbl_general_appointments.patient_id','=','tbl_patients.id')
           ->join('tbl_configurations','tbl_general_appointments.dept_id','=','tbl_configurations.dept_id')
           ->join('users','users.id','=','tbl_configurations.user_id')
           ->select('tbl_general_appointments.*','users.name','tbl_departments.department_name','tbl_patients.first_name'
               ,'tbl_patients.middle_name'
               ,'tbl_patients.last_name'
               ,'tbl_patients.gender'
               ,'tbl_patients.dob'
               ,'tbl_patients.medical_record_number'
           )
           ->where('tbl_configurations.status',1)
           ->where('tbl_configurations.user_id',$request['user_id'])
           ->where('tbl_general_appointments.facility_id',$request['facility_id'])
           ->where('tbl_general_appointments.status','=',0)
           ->where('tbl_general_appointments.appoint_date','=',Date('Y-m-d'))
           ->get();
    }
    public function appointment_dated(Request $request)
    {
       return DB::table('tbl_general_appointments')
           ->join('tbl_configurations','tbl_general_appointments.dept_id','=','tbl_configurations.dept_id')
           ->join('users','users.id','=','tbl_configurations.user_id')
           ->where('tbl_general_appointments.status',0)
           ->where('tbl_configurations.status',1)
           ->where('tbl_configurations.user_id',$request['user_id'])
           ->orWhere('tbl_general_appointments.status',3)
           ->groupBy('appoint_date')
           ->where('tbl_general_appointments.facility_id',$request['facility_id'])
           ->get();
    }
    public function appointment_stages(Request $request)
    {
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];
        $user_id=$request['user_id'];



        $appointment="SELECT department_name,
  ifnull(sum(CASE when tbl_general_appointments.status =1   then 1 ELSE  0 END ),0) as done, 
  ifnull(sum(CASE when tbl_general_appointments.status =0     then 1 ELSE  0 END ),0) as pending,
  ifnull(sum(CASE when tbl_general_appointments.status =2     then 1 ELSE  0 END ),0) as cancelled,
  ifnull(sum(CASE when tbl_general_appointments.status =4     then 1 ELSE  0 END ),0) as missed
  
   from   tbl_general_appointments  
    INNER  join tbl_departments   on tbl_general_appointments.dept_id=tbl_departments.id
    INNER  join tbl_configurations   on tbl_general_appointments.dept_id=tbl_configurations.dept_id
    INNER  join users   on users.id=tbl_configurations.user_id
 WHERE    tbl_general_appointments.facility_id='$facility_id' and tbl_configurations.status=1 and  tbl_general_appointments.created_at BETWEEN '".$start_date."' and '".$end_date."' group by tbl_departments.id ";
        return DB::select($appointment);
    }

    public function Save_client_nutritional_status(Request $request)
    {

        if(patientRegistration::duplicate('tbl_client_nutritional_statuses',['patient_id','status', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$request['patient_id'],$request['status']])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        if($request['transfer']=='YES' && $request['transfer_to']=='' ){
            return response()->json([
                'msg'=>'Please Fill Where are You Transferring This Client?',
                'status'=>0
            ]);
        }
        if($request['transfer']=='YES'  && $request['description']=='' ){
            return response()->json([
                'msg'=>'Please Fill Reasons For This Transfer?',
                'status'=>0
            ]);
        }
        Tbl_client_nutritional_status::create($request->all());
        return response()->json([
            'msg'=>'Client Nutritional Status SAVED',
            'status'=>1
        ]);
    }
    public function Suppliments_Registry(Request $request)
    {

        if(count(Tbl_nutritional_suppliment::where('suppliment_name',$request['suppliment_name'])->get())>0) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }
        Tbl_nutritional_suppliment::create($request->all());
        return response()->json([
            'msg'=>'Nutritional Suppliment SAVED',
            'status'=>1
        ]);
    }

    public function Suppliments_list()
    {
       return Tbl_nutritional_suppliment::get();
    }

    public function nutritionistPerformance(Request $request)
    {
        $performance = [];
        $start_date=date('Y-m-01 00:00:00');
        $end_date=date("Y-m-d H:i:s");
        $start=$request->input('start');
        $end=$request->input('end');
        $facility_id=$request->input('facility_id');
        $user_id=$request->input('user_id');
        $sql = "SELECT count(id) AS total_clients FROM tbl_nutritional_statuses WHERE user_id = ".$user_id." AND (created_at BETWEEN '".$start."' AND '".$end."') AND facility_id = ".$facility_id." ";
        $sql2 = "SELECT count(id) AS total_patients FROM tbl_nutritional_statuses WHERE user_id = ".$user_id." AND (created_at BETWEEN '".$start_date."' AND '".$end_date."') AND facility_id = ".$facility_id." ";
        $performance[] = DB::select(DB::raw($sql));
        $performance[] = DB::select(DB::raw($sql2));
        return $performance;

    }

    public function Nutrition_mtuha(Request $request)
    {
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];
        $all=[];
        $Attendance="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_statuses  inner JOIN tbl_patients on   tbl_nutritional_statuses.patient_id=tbl_patients.id
 WHERE    tbl_nutritional_statuses.facility_id='$facility_id' and  tbl_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($Attendance);

        $assessed="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_statuses  inner JOIN tbl_patients on   tbl_nutritional_statuses.patient_id=tbl_patients.id
 WHERE    (tbl_nutritional_statuses.facility_id='$facility_id' and action_taken='assessment') and  tbl_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($assessed);

 $counselling="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_statuses  inner JOIN tbl_patients on   tbl_nutritional_statuses.patient_id=tbl_patients.id
 WHERE    (tbl_nutritional_statuses.facility_id='$facility_id' and action_taken='counsel') and  tbl_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($counselling);
        $SAM_in="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_statuses  inner JOIN tbl_patients on   tbl_nutritional_statuses.patient_id=tbl_patients.id inner JOIN tbl_admissions on   tbl_nutritional_statuses.patient_id=tbl_admissions.patient_id
 WHERE    (tbl_nutritional_statuses.facility_id='$facility_id' and nutritional_status='SAM') and  tbl_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($SAM_in);
        $SAM_out="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_statuses  inner JOIN tbl_patients on   tbl_nutritional_statuses.patient_id=tbl_patients.id
 WHERE    (tbl_nutritional_statuses.facility_id='$facility_id' and nutritional_status='SAM' AND patient_id not in (SELECT patient_id from tbl_admissions)) and  tbl_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($SAM_out);
        $MAM="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_statuses  inner JOIN tbl_patients on   tbl_nutritional_statuses.patient_id=tbl_patients.id
 WHERE    (tbl_nutritional_statuses.facility_id='$facility_id' and nutritional_status='MAM') and  tbl_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($MAM);
        $Normal="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_statuses  inner JOIN tbl_patients on   tbl_nutritional_statuses.patient_id=tbl_patients.id
 WHERE    (tbl_nutritional_statuses.facility_id='$facility_id' and nutritional_status='Normal') and  tbl_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($Normal);
        $Obese="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_statuses  inner JOIN tbl_patients on   tbl_nutritional_statuses.patient_id=tbl_patients.id
 WHERE    (tbl_nutritional_statuses.facility_id='$facility_id' and nutritional_status='obese') and  tbl_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($Obese);

        $hiv_positive="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_statuses  inner JOIN tbl_patients on   tbl_nutritional_statuses.patient_id=tbl_patients.id
 WHERE    (tbl_nutritional_statuses.facility_id='$facility_id' and hiv_status='POSITIVE') and  tbl_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($hiv_positive);

        $hiv_negative="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_statuses  inner JOIN tbl_patients on   tbl_nutritional_statuses.patient_id=tbl_patients.id
 WHERE    (tbl_nutritional_statuses.facility_id='$facility_id' and hiv_status='NEGATIVE') and  tbl_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($hiv_negative);
        $hiv_unknown="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_statuses  inner JOIN tbl_patients on   tbl_nutritional_statuses.patient_id=tbl_patients.id
 WHERE    (tbl_nutritional_statuses.facility_id='$facility_id' and hiv_status='UNKNOWN') and  tbl_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($hiv_unknown);

        $F_75="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_foods  inner JOIN tbl_patients on   tbl_nutritional_foods.patient_id=tbl_patients.id inner JOIN tbl_nutritional_statuses on   tbl_nutritional_statuses.patient_id=tbl_nutritional_statuses.patient_id

 WHERE    (tbl_nutritional_foods.facility_id='$facility_id' and suppliment_id=1) and  tbl_nutritional_foods.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($F_75);
        $F_100="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

  from   tbl_nutritional_foods  inner JOIN tbl_patients on   tbl_nutritional_foods.patient_id=tbl_patients.id inner JOIN tbl_nutritional_statuses on   tbl_nutritional_statuses.patient_id=tbl_nutritional_statuses.patient_id

 WHERE    (tbl_nutritional_foods.facility_id='$facility_id' and tbl_nutritional_foods.suppliment_id=2) and  tbl_nutritional_foods.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($F_100);

        $F_new_RUTF="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

  from   tbl_nutritional_foods  inner JOIN tbl_patients on   tbl_nutritional_foods.patient_id=tbl_patients.id inner JOIN tbl_nutritional_statuses on   tbl_nutritional_statuses.patient_id=tbl_nutritional_statuses.patient_id

 WHERE    (tbl_nutritional_foods.facility_id='$facility_id' and tbl_nutritional_foods.suppliment_id=3) and  tbl_nutritional_foods.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($F_new_RUTF);
 $F_continuing_RUTF="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_foods  inner JOIN tbl_patients on   tbl_nutritional_foods.patient_id=tbl_patients.id inner JOIN tbl_nutritional_statuses on   tbl_nutritional_statuses.patient_id=tbl_nutritional_statuses.patient_id

 WHERE    (tbl_nutritional_foods.facility_id='$facility_id' and tbl_nutritional_foods.suppliment_id=4) and  tbl_nutritional_foods.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($F_continuing_RUTF);

        $new_RuSF="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

  from   tbl_nutritional_foods  inner JOIN tbl_patients on   tbl_nutritional_foods.patient_id=tbl_patients.id inner JOIN tbl_nutritional_statuses on   tbl_nutritional_statuses.patient_id=tbl_nutritional_statuses.patient_id

 WHERE    (tbl_nutritional_foods.facility_id='$facility_id' and tbl_nutritional_foods.suppliment_id=5) and  tbl_nutritional_foods.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($new_RuSF);
        $continuing_RuSF="SELECT
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,

  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,

  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,

  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,

  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended

   from   tbl_nutritional_foods  inner JOIN tbl_patients on   tbl_nutritional_foods.patient_id=tbl_patients.id inner JOIN tbl_nutritional_statuses on   tbl_nutritional_statuses.patient_id=tbl_nutritional_statuses.patient_id

 WHERE    (tbl_nutritional_foods.facility_id='$facility_id' and tbl_nutritional_foods.suppliment_id=6) and  tbl_nutritional_foods.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($continuing_RuSF);

        $Graduated="SELECT  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,
   
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,
 
  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,
 
  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,
   
  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended
  
    from   tbl_client_nutritional_statuses  inner JOIN tbl_patients on   tbl_client_nutritional_statuses.patient_id=tbl_patients.id inner JOIN tbl_nutritional_statuses on   tbl_client_nutritional_statuses.patient_id=tbl_nutritional_statuses.patient_id
 

 WHERE    (tbl_client_nutritional_statuses.facility_id='$facility_id' and tbl_client_nutritional_statuses.status='Graduated') and  tbl_client_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($Graduated);
        $Lost_follow_up="SELECT  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,
   
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,
 
  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,
 
  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,
   
  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended
  
  from   tbl_client_nutritional_statuses  inner JOIN tbl_patients on   tbl_client_nutritional_statuses.patient_id=tbl_patients.id inner JOIN tbl_nutritional_statuses on   tbl_client_nutritional_statuses.patient_id=tbl_nutritional_statuses.patient_id
 
 WHERE    (tbl_client_nutritional_statuses.facility_id='$facility_id' and tbl_client_nutritional_statuses.status='Lost_follow_up') and  tbl_client_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($Lost_follow_up);

 $Dead="SELECT  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,
   
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,
 
  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,
 
  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,
   
  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended
  
  from   tbl_client_nutritional_statuses  inner JOIN tbl_patients on   tbl_client_nutritional_statuses.patient_id=tbl_patients.id inner JOIN tbl_nutritional_statuses on   tbl_client_nutritional_statuses.patient_id=tbl_nutritional_statuses.patient_id
 
 WHERE    (tbl_client_nutritional_statuses.facility_id='$facility_id' and tbl_client_nutritional_statuses.status='Dead') and  tbl_client_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($Dead);
        $Treatment_fail="SELECT  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,
   
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,
 
  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,
 
  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,
   
  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended
  
  from   tbl_client_nutritional_statuses  inner JOIN tbl_patients on   tbl_client_nutritional_statuses.patient_id=tbl_patients.id inner JOIN tbl_nutritional_statuses on   tbl_client_nutritional_statuses.patient_id=tbl_nutritional_statuses.patient_id
 
 WHERE    (tbl_client_nutritional_statuses.facility_id='$facility_id' and tbl_client_nutritional_statuses.status='Fail') and  tbl_client_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($Treatment_fail);
 $Transfer="SELECT  distinct
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as male_less_6_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."') <6  then 1 ELSE  0 END ),0) as female_less_6_month,
   
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(month ,dob,'".$end_date."') between 6 and 58  then 1 ELSE  0 END ),0) as male_less_59_month, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(month ,dob,'".$end_date."')between 6 and 58  then 1 ELSE  0 END ),0) as female_less_59_month,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 5 and 14  then 1 ELSE  0 END ),0) as male_less_15_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 5 and 14  then 1 ELSE  0 END ),0) as female_less_15_year,
  
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(year ,dob,'".$end_date."') between 15 and 17  then 1 ELSE  0 END ),0) as male_less_18_year, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(year ,dob,'".$end_date."')between 15 and 17 then 1 ELSE  0 END ),0) as female_less_18_year,
 
  ifnull(sum(CASE when (gender ='MALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_preg, ifnull(sum(CASE when (gender ='FEMALE' AND preg =1)  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_preg,
 
  ifnull(sum(CASE when gender ='MALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."') >17  then 1 ELSE  0 END ),0) as male_greater_18_year_non_preg, ifnull(sum(CASE when gender ='FEMALE' AND preg is  Null  AND timestampdiff(year ,dob,'".$end_date."')> 17 then 1 ELSE  0 END ),0) as female_greater_18_year_non_preg,
   
  ifnull(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as total_male_attended,
  ifnull(sum(CASE when  gender ='FEMALE'  then 1 ELSE  0 END ),0) as total_female_attended
  
  from   tbl_client_nutritional_statuses  inner JOIN tbl_patients on   tbl_client_nutritional_statuses.patient_id=tbl_patients.id inner JOIN tbl_nutritional_statuses on   tbl_client_nutritional_statuses.visit_id=tbl_nutritional_statuses.visit_id
 
 WHERE    (tbl_client_nutritional_statuses.facility_id='$facility_id' and transfer='YES') and  tbl_client_nutritional_statuses.created_at BETWEEN '".$start_date."' and '".$end_date."' group by tbl_client_nutritional_statuses.patient_id ";
        $all[] = DB::select($Transfer);

        return $all;

    }


}