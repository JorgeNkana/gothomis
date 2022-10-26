<?php

namespace App\Http\Controllers\VCT;

use App\classes\patientRegistration;
use App\Clinics\Tbl_clinic_instruction;
use App\Clinics\Tbl_clinic_specialist;
use App\Patient\Tbl_accounts_number;
use App\VCT\Tbl_vct_register;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Vct_Controller extends Controller
{
    //
    public function searchClinicpatientFromDb($search)
    {
        return DB::table('vw_opd_patients')->
        where('medical_record_number','like','%'.$search.'%')
//            ->select('patient_id','first_name',
//                'vw_opd_patients.account_id as visit_id',
//                'middle_name',
//                'last_name',
//                'medical_record_number',
//                'gender',
//                'dob')
            ->orWhere('first_name','like','%'.$search.'%')
            ->orWhere('last_name','like','%'.$search.'%')
            ->orWhere('middle_name','like','%'.$search.'%')

            ->take(5)->get()  ;
    }

    public function searchClinicpatientQueue($facility_id)
    {
        DB::statement("CREATE  TABLE if not exists `tbl_past_dermatology_histories` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `visit_date_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `facility_id` int(10) UNSIGNED NOT NULL,
  `admission_id` int(10) UNSIGNED DEFAULT NULL,
  `past_dermatology` varchar(191)  NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ");
        $queue=[];
        $VCT= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',17)
            ->where('received',0)
            ->take(10)->get()  ;
        $queue[]=$VCT;

        $ANTINATAL= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',18)
            ->where('received',0)
            ->take(10)->get() ;
        $queue[]=$ANTINATAL;

        $CHILD= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',19)
            ->where('received',0)
            ->take(10)->get()  ;
        $queue[]=$CHILD;

        $POSTNATAL= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',20)
            ->where('received',0)
            ->take(10)->get()  ;
        $queue[]=$POSTNATAL;

        $LABOUR= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',21)
            ->where('received',0)
            ->take(10)->get()  ;
        $queue[]=$LABOUR;

        $FAMILYPLAN= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',22)
            ->where('received',0)
            ->take(10)->get()  ;
        $queue[]=$FAMILYPLAN;

        $PAEDIATRIC= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',23)
            ->where('received',0)
            ->take(10)->get()  ;
        $queue[]=$PAEDIATRIC;

        $TB= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',15)
            ->where('received',0)
            ->take(10)->get()  ;
        $queue[]=$TB;

        $social= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',24)
            ->where('received',0)
            ->take(10)->get()  ;
        $queue[]=$social;

        $nutrition= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',50)
            ->where('received',0)
            ->take(10)->get()  ;
        $queue[]=$nutrition;

        $orthopedic= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',51)
            ->where('received',0)
            ->groupBy('patient_id')
            ->take(10)->get()  ;
        $queue[]=$orthopedic;
        $ent= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',52)
            ->where('received',0)
            ->groupBy('patient_id')
            ->take(10)->get()  ;
        $queue[]=$ent;
        $urology= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',55)
            ->where('received',0)
            ->groupBy('patient_id')
            ->take(10)->get()  ;
        $queue[]=$urology;
        $medicalClinic= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',100)
            ->where('received',0)
            ->groupBy('patient_id')
            ->take(10)->get()  ;
        $queue[]=$medicalClinic;
        $diabeticClinic= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',41)
            ->where('received',0)
            ->groupBy('patient_id')
            ->take(10)->get()  ;
        $queue[]=$diabeticClinic;
        $DermatologyClinic= DB::table('vw_special_clinics_clients')
            ->where('facility_id',$facility_id)
            ->where('dept_id',43)
            ->where('received',0)
            ->groupBy('patient_id')
            ->take(10)->get()  ;
        $queue[]=$DermatologyClinic;
        return $queue;
    }


    public function vct_registration(Request $request)
    {
         $request->all();
        $transfer_id = $request['transfer_id'];
        $client_id = $request['client_id'];
        $sender_clinic_id = 17;
        $user_id = $request['user_id'];
        $facility_id = $request['facility_id'];
        $referral_to = $request['referral_to'];
        $client_from= $request['client_from'];
        $visitID=Tbl_accounts_number::where('patient_id',$client_id)->orderBy('id','desc')->first();
           $visit_id=$visitID->id;
        if(!is_numeric($referral_to)){
            $referral_to = null;
        }
        if(!is_numeric($client_from)){
            $client_from = null;
        }

        if( $referral_to==$sender_clinic_id){
            return response()->json([
                'msg'=>'You Can not Transfer Client To The same SAme   ',
                'msg'=>"You Can not Transfer Client To The  Same Department You're  Currently Working on It  ",
                'status'=>0
            ]);
        }
        $attendance_type = $request['attendance_type'];
        if(patientRegistration::duplicate('tbl_vct_registers',['client_id','attendance_type',  '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$client_id,$attendance_type])==true) {

            return response()->json([
                'msg'=>'Duplication detected.....',
                'status'=>0
            ]);
        }

        Tbl_vct_register::create(
            [
                'client_id'=>$request['client_id'],
                'user_id'=>$request['user_id'],
                'facility_id'=>$request['facility_id'],
                'attendance_type'=>$request['attendance_type'],
                'serial_no'=>$request['serial_no'],
                'client_from'=>$client_from,
                'client_from_other'=>$request['client_from_other'],
                'pregnancy_record'=>$request['pregnancy_record'],
                'referral_to'=>$referral_to,
                'referral_to_other'=>$request['referral_to_other'],
                'counselling_type'=>$request['counselling_type'],
                'counselling_after_test'=>$request['counselling_after_test'],
                'agreed_vvu_test'=>$request['agreed_vvu_test'],
                'vvu_test_result'=>$request['vvu_test_result'],
                'participatory_test_result'=>$request['participatory_test_result'],
                'tb_test_result'=>$request['tb_test_result'],
                'tb_test'=>$request['tb_test'],
                'comment'=>$request['comment'],
                'condom_given'=>$request['condom_given'],
            ]
        );
        if ($referral_to > 0) {


            $data= Tbl_clinic_instruction::create([
                    'visit_id'=>$visit_id,
                    'dept_id'=>$referral_to,
                    'sender_clinic_id'=>$sender_clinic_id,
                    'summary'=>$request['comment'],
                    'received'=>0,
                    'priority'=>'Routine',
                    'specialist_id'=>null,
                    'doctor_requesting_id'=>$user_id,
                    'consultation_id'=>null,
                ]
            );


        }
        return response()->json([
            'msg' => 'Successful data saved',
            'status' => 1
        ]);




    }




    public function update_referral_Incomming(Request $request)
    {
        $request->all();

return $data=Tbl_clinic_instruction::
where('dept_id',$request['dept_id'])
    ->where('visit_id',$request['visit_id'])
    ->where('received',0)
    ->update([
    'received'=>1
]);

             
        }




}