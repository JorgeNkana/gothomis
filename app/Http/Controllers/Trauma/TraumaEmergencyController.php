<?php

namespace App\Http\Controllers\Trauma;

use App\ClinicalServices\Tbl_complaint;
use App\ClinicalServices\Tbl_diagnosis;
use App\ClinicalServices\Tbl_diagnosis_detail;
use App\ClinicalServices\Tbl_history_examination;
use App\ClinicalServices\Tbl_order;
use App\ClinicalServices\Tbl_past_medical_history;
use App\ClinicalServices\Tbl_past_medical_record;
use App\ClinicalServices\Tbl_physical_examination;
use App\ClinicalServices\Tbl_physical_examination_record;
use App\ClinicalServices\Tbl_request;
use App\ClinicalServices\Tbl_review_of_system;
use App\ClinicalServices\Tbl_review_system;
use App\ClinicalServices\Tbl_unavailable_test;
use App\patient\Tbl_encounter_invoice;
use App\Transactions\Tbl_invoice_line;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class TraumaEmergencyController extends Controller
{

    public function TraumaPatients(Request $request)
    {
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_opd_patients` WHERE (`payment_status_id`=2  OR `payment_status_id`=1 AND `main_category_id` != 1) AND facility_id ='".$id."' AND status =1
         AND account_id NOT IN (SELECT visit_date_id FROM tbl_history_examinations)  GROUP BY patient_id LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function TraumaDoctorsPerformance(Request $request)
    {
        $performance = [];
        $start_date=date('Y-m-01 00:00:00');
        $end_date=date("Y-m-d H:i:s");
        $start=$request->input('start');
        $end=$request->input('end');
        $facility_id=$request->input('facility_id');
        $user_id=$request->input('user_id');
        $sql = "SELECT SUM(total_clerked) AS total_clients FROM vw_doctor_perfomances WHERE doctor_id = '".$user_id."' AND (time_treated BETWEEN '".$start."' AND '".$end."') AND facility_id = '".$facility_id."' ";
        $sql2 = "SELECT SUM(total_clerked) AS total_patients FROM vw_doctor_perfomances WHERE doctor_id = ".$user_id." AND (time_treated BETWEEN '".$start_date."' AND '".$end_date."') AND facility_id = '".$facility_id."' ";
        $performance[] = DB::select(DB::raw($sql));
        $performance[] = DB::select(DB::raw($sql2));
        return $performance;

    }
    public function PatientsSearchTrauma(Request $request)
    {
        $search = $request->input('name');
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_opd_patients` WHERE medical_record_number LIKE '%".$search."%' AND (`payment_status_id`=2  OR `payment_status_id`=1 AND `main_category_id` != 1) AND status=1 AND facility_id ='".$id."' GROUP BY patient_id ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function PatientsSearchInvestigationTrauma(Request $request)
    {
        $search = $request->input('name');
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_investigation_results` WHERE medical_record_number LIKE '%".$search."%' AND account_id  IN (SELECT account_id FROM vw_opd_patients WHERE `status`=1 ) AND facility_id ='".$id."' GROUP BY patient_id ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function TraumaGetInvestigationResults(Request $request)
    {
        $dept = $request->input('dept_id');
        $pt = $request->input('patient_id');
        $date = $request->input('account_id');
        $sql = "select * from vw_investigation_results where patient_id = '".$pt."' AND dept_id = '".$dept."' AND account_id = '".$date."' GROUP BY item_id ";
        $rs = DB::select(DB::raw($sql));
        return $rs;
    }


    public function TraumaInvestigationList(Request $request)
    {
        $pt = $request->input('facility_id');
        $limit = 20;
        $sql = "select * from vw_investigation_results where facility_id = '".$pt."' AND account_id  IN (SELECT account_id FROM vw_opd_patients WHERE `status`=1 ) GROUP BY patient_id limit ".$limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
//    Start of History
    public function TraumaPrevHistory(Request $request)
    {
        $diag = [];
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_history_examinations where patient_id = '".$id."' AND date_attended = '".$date."'
        AND description IS NOT NULL AND duration IS NOT NULL AND duration_unit IS NOT NULL ";
        $sql1 = "select * from vw_history_examinations where patient_id = '".$id."' AND date_attended = '".$date."'
        AND other_complaints IS NOT NULL";
        $sql2 = "select * from vw_history_examinations where patient_id = '".$id."' AND date_attended = '".$date."'
        AND hpi IS NOT NULL";
        $diag[] = DB::select(DB::raw($sql));
        $diag[] = DB::select(DB::raw($sql1));
        $diag[] = DB::select(DB::raw($sql2));
        return $diag;
    }
    public function TraumaGetPrevDiagnosis(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_prev_diagnosis where patient_id = '".$id."' AND date_attended = '".$date."' GROUP BY status ";
        $diag = DB::select(DB::raw($sql));
        return $diag;

    }
    public function TraumaGetPrevRos(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_review_of_systems where patient_id = '".$id."' AND date_attended = '".$date."' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function TraumaGetPrevBirth(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_birth_history where patient_id = '".$id."' AND date_attended = '".$date."' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function TraumaGetPrevFamily(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_family_history where patient_id = '".$id."' AND date_attended = '".$date."' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function TraumaGetPrevPhysical(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_physical_examinations where patient_id = '".$id."' AND date_attended = '".$date."' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function traumaPrevInvestigationResults(Request $request){
        $dept = $request->input('dept_id');
        $pt = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_investigation_results where patient_id = '".$pt."' AND dept_id = '".$dept."' AND date_attended = '".$date."' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function TraumaGetPastMedicine(Request $request)
    {
        $id = $request->input('patient_id');
        $dt = $request->input('date_attended');
        $sql = "SELECT * FROM vw_previous_medications WHERE patient_id = '".$id."' AND date_attended = '".$dt."' ORDER BY date_attended DESC LIMIT 30";
        $data = DB::select(DB::raw($sql));
        return $data;
    }
    public function TraumaGetPastProcedures(Request $request)
    {
        $id = $request->input('patient_id');
        $dt = $request->input('date_attended');
        $sql = "SELECT * FROM vw_previous_procedures WHERE patient_id = '".$id."' AND date_attended ='".$dt."' ";
        $data = DB::select(DB::raw($sql));
        return $data;
    }
    public function TraumaGetAllergies(Request $request)
    {
        $patient_id = $request->input('patient_id');
        $date_attended = $request->input('date_attended');
        $sql = "select * from vw_allergies WHERE patient_id = '".$patient_id."' AND date_attended = '".$date_attended."' ";
        return DB::select(DB::raw($sql));
    }
    public function TraumaVitalsTime(Request $request)
    {
        $patient_id = $request->input('patient_id');
        $date_attended = $request->input('account_id');
        $sql="SELECT time_taken,account_id FROM vw_vital_sign_output WHERE patient_id='".$patient_id."' AND account_id='".$date_attended."' GROUP BY time_taken ";
        $vital_time = DB::select(DB::raw($sql));
        return $vital_time;
    }
//    End of History Tab
    public function TraumaChiefComplaints(Request $request)
    {
        $search = $request->input('search');
        $limit = 10;
        $sql = "select * from tbl_body_systems where name like '%".$search."%' AND category !='Past Medical History'
         AND category !='Immunisation' AND category !='Admission History' limit ".$limit;
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function TraumaPostHistory(Request $request)
    {
        $data= $request->otherData;
        $details= $request->details;
        $complaints= $request->complaints;
        $patient_id = $details['patient_id'];
        $facility_id =$details['facility_id'];
        $user_id =  $details['user_id'];
        $visit_date_id = $details['visit_date_id'];
        $admission_id =  $details['admission_id'];
        $data2 = Tbl_history_examination::create(['patient_id'=>$patient_id,'facility_id'=>$facility_id,'user_id'=>$user_id,
            'visit_date_id'=>$visit_date_id,'admission_id'=>$admission_id,
        ]);
        $id = $data2->id;
        if(count($complaints)>0)
        {
            foreach ($complaints as $d){
                $postData = Tbl_complaint::create([
                    'description'=>$d['description'],
                    'duration'=>$d['duration'],
                    'duration_unit'=>$d['duration_unit'],
                    'status'=>$d['status'],
                    'history_exam_id'=>$id,
                ]);
            }
        }
        if($data){
            $postData2 = Tbl_complaint::create([
                'other_complaints'=>$data,
                'history_exam_id'=>$id,
            ]);
            return $postData2;
        }
    }
    public function TraumaPostHpi(Request $request)
    {
        if(count($request->all())>0){
            $hpi =  $request['hpi'];
            $data2 = Tbl_history_examination::create($request->all());
            $id = $data2->id;
            $postData2 = Tbl_complaint::create([
                'hpi'=>$hpi,
                'history_exam_id'=>$id,
            ]);
            return $postData2;
        }
    }
    public function TraumaReviewOfSystems(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $limit = 10;
        $sql = "select * from tbl_body_systems where name like '%".$search."%' AND category ='".$category."'
         limit ".$limit;
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function TraumaPostRoS(Request $request)
    {
        $data= $request->otherData;
        $details= $request->details;
        $ros= $request->ros;
        $patient_id = $details['patient_id'];
        $facility_id =$details['facility_id'];
        $user_id =  $details['user_id'];
        $visit_date_id = $details['visit_date_id'];
        $admission_id =  $details['admission_id'];
        $data2 = Tbl_review_system::create([
            'patient_id'=>$patient_id,
            'facility_id'=>$facility_id,
            'user_id'=>$user_id,
            'visit_date_id'=>$visit_date_id,
            'admission_id'=>$admission_id,
        ]);
        $id = $data2->id;
        if(count($ros)>0){
            foreach ($ros as $d){
                $postData = Tbl_review_of_system::create([
                    'status'=>$d['status'],
                    'system_id'=>$d['system_id'],
                    'review_system_id'=>$id,
                ]);
            }
        }
        if($data){
            $postData2 = Tbl_review_of_system::create([
                'review_summary'=>$data,
                'review_system_id'=>$id,
            ]);
            return $postData2;
        }
    }
    public function TraumaPostPastMed(Request $request)
    {
        $data= $request->otherData;
        $details= $request->details;
        $allergy= $request->allergy;
        $patient_id = $details['patient_id'];
        $facility_id =$details['facility_id'];
        $user_id =  $details['user_id'];
        $visit_date_id = $details['visit_date_id'];
        $admission_id =  $details['admission_id'];
        $data2 = Tbl_past_medical_history::create([
            'patient_id'=>$patient_id,
            'facility_id'=>$facility_id,
            'user_id'=>$user_id,
            'visit_date_id'=>$visit_date_id,
            'admission_id'=>$admission_id,
        ]);
        $id = $data2->id;
        if(count($allergy)>0){
            foreach ($allergy as $d){
                $postData = Tbl_past_medical_record::create([
                    'status'=>$d['status'],
                    'descriptions'=>$d['name'],
                    'past_medical_history_id'=>$id,
                ]);
            }
        }
//
        if ($data) {
            $rec = new Tbl_past_medical_record($data);
            $rec['past_medical_history_id'] = $id;
            $rec->save();
        }
        return 'ok';
    }
    public function TraumaPostGenPhysical(Request $request)
    {
        if(count($request->all())>0){
            $data2 = Tbl_physical_examination ::create($request->all());
            $id = $data2->id;
            $postData = Tbl_physical_examination_record::create(['gen_examination'=>$request->input('gen_examination'),'physical_examination_id'=>$id,
            ]);
            return $postData;
        }
    }
    public function TraumaPostPhysical(Request $request)
    {
        $dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $patient_id =  $data[0]->patient_id;
        $facility_id =  $data[0]->facility_id;
        $user_id =  $data[0]->user_id;
        $visit_date_id =  $data[0]->visit_date_id;
        $admission_id =  $data[0]->admission_id;
        $data2 = Tbl_physical_examination ::create([
            'patient_id'=>$patient_id,
            'facility_id'=>$facility_id,
            'user_id'=>$user_id,
            'visit_date_id'=>$visit_date_id,
            'admission_id'=>$admission_id,
        ]);
        $id = $data2->id;
        foreach ($request->all() as $d){
            $postData = Tbl_physical_examination_record::create(['observation'=>$d['observation'],'category'=>$d['category'],'system'=>$d['system'],'physical_examination_id'=>$id,
            ]);
        }
        return $postData;
    }
    public function TraumaPostDiagnosis(Request $request)
    {
        $dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $patient_id =  $data[0]->patient_id;
        $user_id =  $data[0]->user_id;
        $visit_date_id =  $data[0]->visit_date_id;
        $facility_id =  $data[0]->facility_id;
        $admission_id =  $data[0]->admission_id;
        $diagnosis = Tbl_diagnosis::create(['patient_id'=>$patient_id,'facility_id'=>$facility_id,'user_id'=>$user_id,
            'visit_date_id'=>$visit_date_id,"admission_id"=>$admission_id]);
        $id = $diagnosis->id;
        foreach ($request->all() as $d){
            $diag = Tbl_diagnosis_detail::create([
                "diagnosis_description_id"=>$d['diagnosis_description_id'],
                "status"=>$d['status'],
                "diagnosis_id"=>$id,
            ]);
        }
        return $diag;
    }
    public function TraumaGetSubDepts(Request $request)
    {  $id= $request->input('department_id');
        return DB::table('tbl_sub_departments')
            // ->where('department_id',$id)
            ->get();
    }
    public function TraumaUnavailableInvestigations(Request $request)
    {
        if(count($request->all())>0){
            foreach ($request->all() as $data){
                $os = Tbl_unavailable_test::create([
                    'patient_id'=>$data['patient_id'],
                    'item_id'=>$data['item_id'],
                    'user_id'=>$data['user_id'],
                    'facility_id'=>$data['facility_id'],
                    'visit_date_id'=>$data['visit_date_id'],
                ]);
            }
            return $os;
        }
    }
    public function TraumaPostInvestigations(Request $request)
    {
        $clinicalData =  $request->clinicalData;
        $details =  $request->details;
        $priority=  $request->priority;
        $clinical_note=  $request->clinical_note;
        if (!$priority){
            $priority = 'Routine';
        }
        if (!$clinical_note){
            $clinical_note = 'No clinical summary written for this investigation order';
        }
        $requesting_department_id = $details['requesting_department_id'];
        $patient_id = $details['patient_id'];
        $facility_id =$details['facility_id'];
        $user_id =  $details['user_id'];
        $account_number_id = $details['visit_date_id'];
        $admission_id =  $details['admission_id'];
        $investgation = Tbl_request::create(["requesting_department_id"=>$requesting_department_id,"doctor_id"=>$user_id,"patient_id"=>$patient_id,"visit_date_id"=>$account_number_id,"eraser"=>1,"admission_id"=>$admission_id]);
        $id = $investgation->id;
        foreach ($clinicalData as $d){
            $postData = Tbl_order::create(['priority'=>$priority,'clinical_note'=>$clinical_note,'test_id'=>$d['item_id'],'order_id'=>$id,"eraser"=>1,]);
        }
        $billing = Tbl_encounter_invoice::create(["account_number_id"=>$account_number_id, "user_id"=>$user_id,"facility_id"=>$facility_id,]);
        $invoice_id = $billing->id;

        foreach ($clinicalData as $b){
            $billsData = Tbl_invoice_line::create(["invoice_id"=>$invoice_id,"item_type_id"=>$b['item_type_id'],
                "quantity"=>1,"item_price_id"=>$b['item_price_id'],"user_id"=>$b['user_id'],"patient_id"=>$b['patient_id'],
                "status_id"=>$b['status_id'],"facility_id"=>$b['facility_id'],"discount_by"=>$b['user_id'],"payment_filter"=>$b['payment_filter'],"discount"=>0, ]);
        }
        return $billsData;
    }
    public function TraumaGetPanels(Request $request)
    {
        $sub = $request->input('sub_dept_id');
        $facility_id = $request->input('facility_id');
        $patient_category_id = $request->input('patient_category_id');
        $sql=" select * from `vw_labpanels` where sub_dept_id='".$sub." ' AND patient_category_id ='".$patient_category_id."' AND facility_id = '".$facility_id."' GROUP BY item_id";
        $panel = DB::select(DB::raw($sql));
        return $panel;
    }
    public function TraumaPostLocalPhysical(Request $request)
    {
        if(count($request->all())>0){
            $data2 = Tbl_physical_examination ::create($request->all());
            $id = $data2->id;
            $postData = Tbl_physical_examination_record::create(['local_examination'=>$request->input('local_examination'),'physical_examination_id'=>$id,
            ]);
            return $postData;
        }
    }
    public function TraumaPerformance(Request $request)
    {
        $performance = [];
        $start_date=date('Y-m-01 00:00:00');
        $end_date=date("Y-m-d H:i:s");
        $start=$request->input('start');
        $end=$request->input('end');
        $facility_id=$request->input('facility_id');
        $user_id=$request->input('user_id');
        $sql = "SELECT SUM(total_clerked) AS total_clients FROM vw_doctor_perfomances WHERE doctor_id = '".$user_id."' AND (time_treated BETWEEN '".$start."' AND '".$end."') AND facility_id = '".$facility_id."' ";
        $sql2 = "SELECT SUM(total_clerked) AS total_patients FROM vw_doctor_perfomances WHERE doctor_id = ".$user_id." AND (time_treated BETWEEN '".$start_date."' AND '".$end_date."') AND facility_id = '".$facility_id."' ";
        $performance[] = DB::select(DB::raw($sql));
        $performance[] = DB::select(DB::raw($sql2));
        return $performance;

    }
    public function getCorpseTrauma(Request $request)
    {
        $search = $request->input('search');
        $facility_id = $request->input('facility_id');
        $sql = "SELECT * FROM tbl_corpses WHERE corpse_record_number LIKE '%".$search."%' OR first_name LIKE '%".$search."%' OR last_name LIKE '%".$search."%' AND (immediate_cause IS NULL) AND facility_id = '".$facility_id."' ";
        $corpse = DB::select(DB::raw($sql));
        return $corpse;
    }
}