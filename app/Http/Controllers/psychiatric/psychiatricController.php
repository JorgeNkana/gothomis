<?php

namespace App\Http\Controllers\psychiatric;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ClinicalServices\Tbl_past_medical_history;
use App\ClinicalServices\Tbl_past_medical_record;
use App\psychiatric\Tbl_past_Psych_record;
use App\psychiatric\Tbl_forensic_historie;
use Illuminate\Support\Facades\DB;
class psychiatricController extends Controller
{
   public function incomingPsychPatients(Request $request)
    {
        $facility_id=$request->input('facility_id');
        $dept_id=$request->input('dept_id');
        $sql = "SELECT * FROM vw_special_clinics_clients WHERE facility_id = ".$facility_id." AND dept_id=".$dept_id." AND received = 0 LIMIT 20";
        $pts = DB::select(DB::raw($sql));
        return $pts;
    }
    public function psychAll(Request $request)
    {
        $facility_id=$request->input('facility_id');
        $dept_id=$request->input('dept_id');
        $searchKey=$request->input('searchKey');
        $sql = "SELECT * FROM vw_special_clinics_clients WHERE medical_record_number LIKE '%".$searchKey."%' AND facility_id = ".$facility_id." AND dept_id=".$dept_id." ";
        $pts = DB::select(DB::raw($sql));
        return $pts;
    }

    public function prevPsychVisitis(Request $request)
    {
        $id = $request->input('patient_id');
        $f_id = $request->input('facility_id');
        $sql = "SELECT visit_date,patient_id,account_id FROM `vw_special_clinics_clients` WHERE patient_id = ".$id." AND facility_id = ".$f_id." GROUP BY visit_date LIMIT 6  ";
        $data = DB::select(DB::raw($sql));
        return $data;
    }

    public function postPastPsych(Request $request)
    {
        if(count($request->all())>0){
            return Tbl_past_Psych_record::create($request->all());
        }
    } 
	public function forensicHistory(Request $request)
    {
        if(count($request->all())>0){
            return Tbl_forensic_historie::create($request->all());
        }
    }
}