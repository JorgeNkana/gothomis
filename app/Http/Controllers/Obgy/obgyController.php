<?php

namespace App\Http\Controllers\Obgy;

use App\ClinicalServices\Tbl_obs_gyn;
use App\ClinicalServices\Tbl_obs_gyn_record;
use App\ClinicalServices\Tbl_past_medical_history;
use App\ClinicalServices\Tbl_past_medical_record;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class obgyController extends Controller
{
    public function incomingObgyPatients(Request $request)
    {
        $facility_id=$request->input('facility_id');
        $dept_id=$request->input('dept_id');
        $sql = "SELECT * FROM vw_special_clinics_clients WHERE facility_id = ".$facility_id." AND dept_id=".$dept_id." AND received = 0 LIMIT 20";
        $pts = DB::select(DB::raw($sql));
        return $pts;
    }
    public function obgyAll(Request $request)
    {
        $facility_id=$request->input('facility_id');
        $dept_id=$request->input('dept_id');
        $searchKey=$request->input('searchKey');
        $sql = "SELECT * FROM vw_special_clinics_clients WHERE medical_record_number LIKE '%".$searchKey."%' AND facility_id = ".$facility_id." AND dept_id=".$dept_id." ";
        $pts = DB::select(DB::raw($sql));
        return $pts;
    }

    public function prevObgyVisits(Request $request)
    {
        $id = $request->input('patient_id');
        $f_id = $request->input('facility_id');
        $sql = "SELECT visit_date,patient_id,account_id FROM `vw_special_clinics_clients` WHERE patient_id = ".$id." AND facility_id = ".$f_id." GROUP BY visit_date LIMIT 6  ";
        $data = DB::select(DB::raw($sql));
        return $data;
    }

    public function getPrevObsGyna(Request $request)
    {
        $data = [];
        $sql1 = "SELECT t1.*,t2.* FROM tbl_obs_gyn_records t1 INNER JOIN tbl_obs_gyns t2 ON t1.obs_gyn_id = t2.id
        WHERE patient_id='".$request['patient_id']."' AND visit_date_id ='".$request['account_id']."' AND category = 'obstetrics' ";
        $sql2 = "SELECT t1.*,t2.* FROM tbl_obs_gyn_records t1 INNER JOIN tbl_obs_gyns t2 ON t1.obs_gyn_id = t2.id
        WHERE patient_id='".$request['patient_id']."' AND visit_date_id ='".$request['account_id']."' AND category = 'gynaecological'";
        $data[] = DB::select(DB::raw($sql1));
        $data[] = DB::select(DB::raw($sql2));
        return $data;
    }

    public function postGyna(Request $request)
    {
        $data= $request->gyna_data;
        $details= $request->details;
        $patient_id = $details['patient_id'];
        $facility_id =$details['facility_id'];
        $user_id =  $details['user_id'];
        $visit_date_id = $details['visit_date_id'];
        $admission_id =  $details['admission_id'];
        $category = 'gynaecological';
        if(count($data)>0){
            $view = Tbl_obs_gyn::create([
                'patient_id'=>$patient_id,
                'facility_id'=>$facility_id,
                'user_id'=>$user_id,
                'visit_date_id'=>$visit_date_id,
                'admission_id'=>$admission_id,
            ]);
            $id = $view->id;
            $gyn = new Tbl_obs_gyn_record($data);
            $gyn['category'] = 'gynaecological';
            $gyn['obs_gyn_id'] = $id;
            $gyn->save();
            if($gyn){
                return response()->json([
                    'msg' => $category.'  data saved successfully',
                    'status' => 1
                ]);
            }
            else {
                response()->json([
                    'msg' => 'Error occurred: Failed to save ',101,
                    'status' => 0
                ]);
            }
        }else{
            response()->json([
                'msg' => 'Error occurred: No data was saved ',101,
                'status' => 0
            ]);
        }
    }public function postObs(Request $request)
    {
        $data= $request->gyna_data;
        $details= $request->details;
        $patient_id = $details['patient_id'];
        $facility_id =$details['facility_id'];
        $user_id =  $details['user_id'];
        $visit_date_id = $details['visit_date_id'];
        $admission_id =  $details['admission_id'];
        $category = 'obstetrics';
       if(count($data)>0){
           $view = Tbl_obs_gyn::create([
               'patient_id'=>$patient_id,
               'facility_id'=>$facility_id,
               'user_id'=>$user_id,
               'visit_date_id'=>$visit_date_id,
               'admission_id'=>$admission_id,
           ]);
           $id = $view->id;
           $obs = new Tbl_obs_gyn_record($data);
           $obs['category'] = 'obstetrics';
           $obs['obs_gyn_id'] = $id;
           $obs->save();
           if($obs){
               return response()->json([
                   'msg' => $category.'  data saved successfully',
                   'status' => 1
               ]);
           }
           else {
               response()->json([
                   'msg' => 'Error occurred: Failed to save ',101,
                   'status' => 0
               ]);
           }
       }
       else {
           response()->json([
               'msg' => 'Error occurred: No data was saved ',101,
               'status' => 0
           ]);
       }
    }
}