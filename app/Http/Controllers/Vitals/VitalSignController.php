<?php

namespace App\Http\Controllers\Vitals;

use App\Emergency\Tbl_vital_sign;
use App\Patient\Tbl_accounts_number;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class VitalSignController extends Controller
{
    public function vitalSignsUser($facility_id)
    {
        $date = Carbon::yesterday();
        $sql = "SELECT * FROM `vw_vital_sign_users` WHERE (`payment_status_id`=2 AND `visit_date`>='" . $date . "' OR `payment_status_id`=1 AND `visit_date`>='" . $date . "' AND `payment_filter` IS NOT NULL) AND facility_id ='" . $facility_id . "' AND account_id NOT IN (SELECT visiting_id FROM tbl_vital_signs) GROUP BY patient_id LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    //    REGISTER VITAL SIGNS
    public function VitalSignRegister(Request $request)
    {
        $date = date('Y-m-d h:i:s');
        $time = date("H:i:s");
       $accountID=Tbl_accounts_number::select("id")->where("patient_id",$request->all()[0]['patient_id'])->orderBy("id","desc")->take(1)->get();
        if (count($accountID)==0){
            return response()->json([
                'msg' => "No account number assign to this patient",
                'notification' => "error",
                'status' => 0
            ]);
        }
        else{
            $visit_id=$accountID[0]->id;
        }
       if (count($request->all()) > 0) {
            foreach ($request->all() as $data) {
                $os = Tbl_vital_sign::create([
                    'visiting_id' => $visit_id,
                    'vital_sign_id' => $data['vital_sign_id'],
                    'vital_sign_value' => $data['vital_sign_value'],
                    'registered_by' => $data['registered_by'],
                    'date_taken' => $date,
                    'time_taken' => $time
                ]);
            }
        }
        return response()->json([
            'msg' => "Successfully Registered",
            'notification' => "Success",
            'status' => 1
        ]);
    }
    //VITAL SIGN ACCOUNT
    public function getVitalsAccount($patient_id)
    {
        $accountID=Tbl_accounts_number::where("patient_id",$patient_id)->orderBy("id","desc")->get();
        return DB::select("select date(t1.created_at) as visit_date,t1.id as account_id from tbl_accounts_numbers t1 
where t1.id in (select visiting_id from tbl_vital_signs where patient_id=$patient_id)  group by t1.id order by t1.id desc limit 5");


    }
    //VITAL SIGN DATA ACCORDING TO VISIT DATE
    public function getVitalsDate($patient_id)
    {
        $view = DB::table('vw_vital_sign_output')
            ->where('account_id', $patient_id)
            ->select('vital_name', 'vital_sign_value', 'date_taken', 'time_taken', 'submited_by', 'si_unit')
            ->get();
        return $view;
    }
//    DISPLAY VITALS
    public function getVitals()
    {
        $view = DB::table('tbl_vitals')
            ->select('vital_name', 'si_unit', 'id as vital_id')
            ->get();
        return $view;

    }
    public function getVitalsPatients(Request $request)
    {
        $id = $request->input('facility_id');
        $date = Carbon::yesterday();
        $sql = "SELECT * FROM `vw_vital_sign_users` WHERE (`payment_status_id`=2  OR `payment_status_id`=1 AND `main_category_id` != 1) AND facility_id ='". $id."' GROUP BY patient_id  ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function vitalSignsUsers(Request $request)
    {
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_vital_sign_users` WHERE facility_id ='" . $id . "' AND account_id NOT IN (SELECT visiting_id FROM tbl_vital_signs) AND (timestampdiff(hour,visit_date,CURRENT_TIMESTAMP) <24) GROUP BY patient_id LIMIT 50 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function prevVitalRecord(Request $request)
    {
        $id = $request->input('visit_date_id');

        $sql = "select * from vw_vital_sign_output where account_id = '".$id."' ";
        $vital = DB::select(DB::raw($sql));
        return $vital;
    }
    public function vitalPatients(Request $request)
    {
        $name = $request['name'];
        $patients = DB::table('vw_vital_sign_users')
            ->distinct()
            ->orwhere('medical_record_number', 'like', '%' . $name . '%')
            ->groupBy('patient_id')
            ->limit(5)
            ->get();
        return $patients;
    }
    public function previousVisitsVitals(Request $request)
    {
        $id = $request->input('patient_id');
        $sql = "SELECT date_attended,patient_id,created_at FROM `tbl_accounts_numbers` WHERE patient_id ='".$id."' ORDER BY date_attended DESC LIMIT 5 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function vitalsreport(Request $request)
    {

        $facility_id=$request->facility_id;
        $start=$request->data['start_date'];
        $end=$request->data['end_date'];
        return DB::table('vw_vital_sign_output')
            ->whereBetween('date_taken',[$start,$end])

            ->get();
    }

}