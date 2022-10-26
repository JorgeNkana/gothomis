<?php

namespace App\Http\Controllers\Dental;

use App\ClinicalServices\Tbl_past_medical_history;
use App\ClinicalServices\Tbl_past_medical_record;
use App\Dental\Tbl_past_dental_record;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class dentalController extends Controller
{
    public function incomingDentalPatients(Request $request)
    {
        $facility_id=$request->input('facility_id');
        $dept_id=$request->input('dept_id');
        $sql = "SELECT * FROM opd_patients WHERE facility_id = '".$facility_id."' AND department_id='".$dept_id."'   GROUP BY patient_id LIMIT 20";
        $pts = DB::select(DB::raw($sql));
        return $pts;
    }
    public function dentalAll(Request $request)
    {
        $facility_id=$request->input('facility_id');
        $dept_id=$request->input('dept_id');
        $searchKey=$request->input('searchKey');
        $sql = "SELECT * FROM opd_patients WHERE medical_record_number LIKE '%".$searchKey."%' AND facility_id = '".$facility_id."' AND department_id='".$dept_id."' ";
        $pts = DB::select(DB::raw($sql));
        return $pts;
    }

    public function prevDentalVisitis(Request $request)
    {
        $id = $request->input('patient_id');
        $f_id = $request->input('facility_id');
        $sql = "SELECT visit_date,patient_id,account_id FROM `vw_special_clinics_clients` WHERE patient_id = '".$id."' AND facility_id = '".$f_id."' GROUP BY visit_date LIMIT 6  ";
        $data = DB::select(DB::raw($sql));
        return $data;
    }

    public function postPastDental(Request $request)
    {
        if(count($request->all())>0){
            return Tbl_past_dental_record::create($request->all());
        }
    }
    public function postPastMedicalSurgical(Request $request)
    {
        $patient_id =  $request->patient_id;
        $facility_id =  $request->facility_id;
        $user_id =  $request->user_id;
        $visit_date_id =  $request->visit_date_id;
        $admission_id =  $request->admission_id;
        $other_past_medicals =  $request->other_past_medicals;
        $past_surgical =  $request->past_surgical;
        if(count($request->all())>0){
            $data2 = Tbl_past_medical_history::create([
                'patient_id'=>$patient_id,
                'facility_id'=>$facility_id,
                'user_id'=>$user_id,
                'visit_date_id'=>$visit_date_id,
                'admission_id'=>$admission_id,
            ]);
            $id = $data2->id;
            Tbl_past_medical_record::create([
                'other_past_medicals'=>$other_past_medicals,
                'past_surgical'=>$past_surgical,
                'past_medical_history_id'=>$id,
            ]);;
        }
    }

}