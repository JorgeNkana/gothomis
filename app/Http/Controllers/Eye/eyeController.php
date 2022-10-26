<?php

namespace App\Http\Controllers\Eye;

use App\Eye\Tbl_eye_examination_record;
use App\Eye\Tbl_eyeclinic_visit;
use App\Eye\Tbl_past_eye_record;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class eyeController extends Controller
{
    public function incomingEyePatients(Request $request)
    {
        $facility_id=$request->input('facility_id');
        $dept_id=$request->input('dept_id');
        $sql = "SELECT * FROM opd_patients WHERE facility_id = '".$facility_id."' AND department_id='".$dept_id."'  LIMIT 20";
        $pts = DB::select(DB::raw($sql));
        return $pts;
    }
    public function eyeAll(Request $request)
    {
        $facility_id=$request->input('facility_id');
        $dept_id=$request->input('dept_id');
        $searchKey=$request->input('searchKey');
        $sql = "SELECT * FROM opd_patients WHERE medical_record_number LIKE '%".$searchKey."%' AND facility_id = '".$facility_id."' AND department_id='".$dept_id."' ";
        $pts = DB::select(DB::raw($sql));
        return $pts;
    }

    public function eyeExaminations(Request $request)
    {
        $search = $request->input('searchKey');
        $cat = $request->input('category');
        $sql = "SELECT * FROM tbl_eye_examinations WHERE description LIKE '%".$search."%' AND category ='".$cat."' LIMIT 10";
        return DB::select(DB::raw($sql));
    }
public function eyeRefractionFindings(Request $request)
    {
        $category= $request->category; 
        $details= $request->details;
        $findings= $request->refraData;
        $patient_id = $details['patient_id'];
        $facility_id =$details['facility_id'];
        $user_id =  $details['user_id'];
        $account_id = $details['visit_date_id'];
        $admission_id =  $details['admission_id'];
        $data = Tbl_eyeclinic_visit::create(["patient_id"=>$patient_id,
            "admission_id"=>$admission_id,
            "visit_date_id"=>$account_id,
            "user_id"=>$user_id,
            "facility_id"=>$facility_id,
        ]);
        $id=$data->id;
         $Data = Tbl_eye_examination_record::create([
             "sphere"=>$findings['sphere'],
             "clinic_visit_id"=>$id,
             "cylinder"=>$findings['cylinder'],
             "axis"=>$findings['axis'],
             "v_a"=>$findings['va'],
             "p_d"=>$findings['pd'],
             "a_d_d"=>$findings['add'],
             "category"=>$category,]);
        return response()->json([
           'msg' =>'Refraction data successfully saved!',
           'status' =>1
        ]);

    }

    public function eyeFindings(Request $request)
    {
        $eye_side= $request->eye_side;
        $other= $request->otherData;
        $details= $request->details;
        $findings= $request->findings;
        $patient_id = $details['patient_id'];
        $facility_id =$details['facility_id'];
        $user_id =  $details['user_id'];
        $account_id = $details['visit_date_id'];
        $admission_id =  $details['admission_id'];
        $data = Tbl_eyeclinic_visit::create(["patient_id"=>$patient_id,"admission_id"=>$admission_id,"visit_date_id"=>$account_id, "user_id"=>$user_id,"facility_id"=>$facility_id,]);
        $id=$data->id;
        if(count($findings)>0){
            foreach ($findings as $b){
                $Data = Tbl_eye_examination_record::create(["description"=>$b['description'],"clinic_visit_id"=>$id,"sub_category"=>$b['sub_category'],
                    "category"=>$b['category'],]);
            }
        }
        if ($other) {
            $rec = new Tbl_eye_examination_record($other);
            $rec['category'] = $eye_side;
            $rec['clinic_visit_id'] = $id;
            $rec->save();
            return $rec;
        }
    }

    public function postPastEye(Request $request)
    {
        return Tbl_past_eye_record::create($request->all());
    }

    public function updateClinicClient(Request $request)
    {
        $visit_id = $request->input('visit_id');
        $dept_id = $request->input('dept_id');
        $user_id = $request->input('user_id');
        $update = DB::table('tbl_clinic_instructions')
            ->where('visit_id', $visit_id)
            ->where('dept_id', $dept_id)
            ->update([
                'received' => 1,
                'specialist_id' => $user_id,
            ]);
        return $update;
    }
    public function eyeExaminationReport(Request $request)
    {
        
    $id= $request->patient_id;
    $visit_id= $request->visit_date_id;
    $data=[];
    $sql1 = "select sphere,cylinder,axis,v_a,p_d,a_d_d,category from vw_eye_exam_records where patient_id = '".$id."'
    AND visit_date_id = '".$visit_id."' AND sub_category IS NULL AND sphere IS NOT NULL";

    $sql2 = "select category,hand_movement,perception_light,non_perception_light from vw_eye_exam_records where patient_id = '".$id."' 
    AND visit_date_id = '".$visit_id."' AND sub_category IS NULL AND hand_movement IS NOT NULL ";

    $sql3 = "select category,sub_category,description from vw_eye_exam_records where patient_id = '".$id."' 
    AND visit_date_id = '".$visit_id."' AND (sub_category ='Snellen Chart/E-chart' OR sub_category ='Counting Fingers')";

    $sql4 = "select category,sub_category,description from vw_eye_exam_records where patient_id = '".$id."' 
    AND visit_date_id = '".$visit_id."' AND (sub_category !='Snellen Chart/E-chart' OR sub_category !='Counting Fingers') AND sub_category IS NOT NULL ";

    $data[]= DB::select(DB::raw($sql1));
    $data[]= DB::select(DB::raw($sql2));
    $data[]= DB::select(DB::raw($sql3));
    $data[]= DB::select(DB::raw($sql4));
    return $data;
    }
}