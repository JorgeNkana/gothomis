<?php

namespace App\Http\Controllers\ClinicalServices;

use App\classes\patientRegistration;
use App\laboratory\Tbl_order;
use App\ClinicalServices\Tbl_admission;
use App\ClinicalServices\Tbl_blood_request;
use App\ClinicalServices\Tbl_clinic_instruction;
use App\ClinicalServices\Tbl_complaint;
use App\ClinicalServices\Tbl_continuation_note;
use App\ClinicalServices\Tbl_corpse;
use App\ClinicalServices\Tbl_corpse_admission;
use App\ClinicalServices\Tbl_diagnosis;
use App\ClinicalServices\Tbl_diagnosis_detail;
use App\ClinicalServices\Tbl_encounter_invoice;
use App\ClinicalServices\Tbl_history_examination;
use App\ClinicalServices\Tbl_invoice_line;
use App\ClinicalServices\Tbl_past_medical_history;
use App\ClinicalServices\Tbl_past_medical_record;
use App\ClinicalServices\Tbl_patient_procedure;
use App\ClinicalServices\Tbl_physical_examination;
use App\ClinicalServices\Tbl_physical_examination_record;
use App\ClinicalServices\Tbl_prescription;
use App\ClinicalServices\Tbl_referral;
use App\ClinicalServices\Tbl_request;
use App\ClinicalServices\Tbl_review_of_system;
use App\ClinicalServices\Tbl_review_system;
use App\ClinicalServices\Tbl_unavailable_test;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class IpdController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }
    public function previousVisits(Request $request)
    {
    $id = $request->input('patient_id');
    $sql = "SELECT date_attended,patient_id,created_at,id AS account_id FROM `tbl_accounts_numbers` WHERE patient_id ='".$id."' ORDER BY date_attended DESC LIMIT 5 ";
    $patient = DB::select(DB::raw($sql));
    return $patient;
    }

    public function prevHistory(Request $request)
    {
    $diag = [];
    $id = $request->input('patient_id');
    $date = $request->input('account_id');
    $sql = "select * from vw_history_examinations where patient_id = '".$id."' AND visit_date_id = '".$date."'
    AND description IS NOT NULL AND duration IS NOT NULL AND duration_unit IS NOT NULL ";
    $sql1 = "select * from vw_history_examinations where patient_id = '".$id."' AND visit_date_id = '".$date."'
    AND other_complaints IS NOT NULL";
    $sql2 = "select * from vw_history_examinations where patient_id = '".$id."' AND visit_date_id = '".$date."'
    AND hpi IS NOT NULL";
    $diag[] = DB::select(DB::raw($sql));
    $diag[] = DB::select(DB::raw($sql1));
    $diag[] = DB::select(DB::raw($sql2));
    return $diag;
    }

    public function getPrevDiagnosis(Request $request)
    {
    $id = $request->input('patient_id');
    $date = $request->input('account_id');
    $sql = "select * from vw_prev_diagnosis where patient_id = '".$id."' AND visit_date_id = '".$date."' ORDER BY status ";
    $diag = DB::select(DB::raw($sql));
    return $diag;

    }

    public function getPrevRos(Request $request)
    {
    $diag = [];
    $id = $request->input('patient_id');
    $date = $request->input('account_id');
    $sql = "select * from vw_review_of_systems where patient_id = '".$id."' AND visit_date_id = '".$date."' ";
    $sql1 = "select review_summary from tbl_review_of_systems t1 INNER JOIN tbl_review_systems t2 ON t1.review_system_id = t2.id
            where t2.patient_id = '".$id."' AND t2.visit_date_id = '".$date."' AND review_summary IS NOT NULL ";
    $diag[] = DB::select(DB::raw($sql));
    $diag[] = DB::select(DB::raw($sql1));
    return $diag;
    }

    public function getAllergies(Request $request)
    {
    $pastData = [];
    $patient_id = $request->input('patient_id');
    $id = $request->input('account_id');
    $sql = "select * from vw_allergies WHERE patient_id ='".$patient_id."' AND visit_date_id = '".$id."' AND status IS NOT NULL ";
    $sql1 = "SELECT * FROM tbl_past_medical_records t1 INNER JOIN tbl_past_medical_histories t2 ON t2.id = t1.past_medical_history_id WHERE t2.patient_id ='".$patient_id."' AND t2.visit_date_id = '".$id."' AND t1.status IS NULL";
    $pastData [] = DB::select(DB::raw($sql));
    $pastData [] = DB::select(DB::raw($sql1));
    return $pastData;
    }

    public function getPrevPhysical(Request $request)
    {
    $diag = [];
    $id = $request->input('patient_id');
    $date = $request->input('account_id');
    $sql = "select * from vw_physical_examinations where patient_id = '".$id."' AND visit_date_id = '".$date."' 
    AND category IS NOT NULL AND observation IS NOT NULL";
    $sql1 = "select * from vw_physical_examinations where patient_id = '".$id."' AND visit_date_id = '".$date."' 
    AND gen_examination IS NOT NULL";
    $sql2 = "select * from vw_physical_examinations where patient_id = '".$id."' AND visit_date_id = '".$date."' 
    AND local_examination IS NOT NULL";
    $sql3 = "select * from vw_physical_examinations where patient_id = '".$id."' AND visit_date_id = '".$date."' 
    AND summary_examination IS NOT NULL";
    $sql4 = "select * from vw_physical_examinations where patient_id = '".$id."' AND visit_date_id = '".$date."' 
    AND other_systems_summary IS NOT NULL";
    $diag[] = DB::select(DB::raw($sql));
    $diag[] = DB::select(DB::raw($sql1));
    $diag[] = DB::select(DB::raw($sql2));
    $diag[] = DB::select(DB::raw($sql3));
    $diag[] = DB::select(DB::raw($sql4));
    return $diag;
    }

    public function prevInvestigationResults(Request $request){
    $dept = $request->input('dept_id');
    $pt = $request->input('patient_id');
    $date = $request->input('account_id');
    $sql = "select * from vw_investigation_results where patient_id = '".$pt."' AND dept_id = '".$dept."' AND account_id = '".$date."' ";
    $diag = DB::select(DB::raw($sql));
    return $diag;
    }
    public function getPastMedicine(Request $request)
    {
    $data = [];
    $id = $request->input('patient_id');
    $dt = $request->input('account_id');
    $sql = "SELECT * FROM vw_previous_medications WHERE patient_id = '".$id."' AND visit_id = '".$dt."' AND conservatives IS NULL";
    $sql2 = "SELECT * FROM tbl_prescriptions WHERE patient_id = '".$id."' AND visit_id = '".$dt."'";
    $data[] = DB::select(DB::raw($sql));
    $data[] = DB::select(DB::raw($sql2));
    return $data;
    }

    public function vitalsTime(Request $request)
    {
    $patient_id = $request->patient_id;
    $date_attended = $request->account_id;
    $sql="SELECT patient_id,time_taken,account_id,created_at FROM vw_vital_sign_output WHERE patient_id='".$patient_id."' AND account_id='".$date_attended."' GROUP BY time_taken ";
    $vital_time = DB::select(DB::raw($sql));
    return $vital_time;
    }
    public function patientVitals(Request $request)
    {
    $patient_id = $request->patient_id;
    $account_id = $request->account_id;
    $time_attended = $request->time_taken;
    $sql="SELECT * FROM vw_vital_sign_output WHERE patient_id='".$patient_id."' AND time_taken='".$time_attended."'AND account_id='".$account_id."' ";
    $vital_data = DB::select(DB::raw($sql));
    return $vital_data;
    }

    public function getIpdPatients(Request $request)
    {

    $per_page =  (isset($request['per_page'])? $request['per_page'] : 25);
    $sql = "select * from vw_ipd_patients where facility_id = '".$request['facility_id']."' GROUP BY patient_id ORDER BY first_name ";
    $inpatients = DB::select(DB::raw($sql));
    $all =  customPaginate($inpatients, $per_page);
    return $all;
    }

    public function investigationList(Request $request)
    {
    $per_page =  (isset($request['per_page'])? $request['per_page'] : 25);
    $sql = "select * from vw_investigation_results where facility_id = '".$request['facility_id']."'  AND (timestampdiff(hour,created_at,CURRENT_TIMESTAMP)<=24) GROUP BY patient_id ";
    $inv = DB::select(DB::raw($sql));
    $all =  customPaginate($inv, $per_page);
    return $all;
    }

    public function getAllIpdPatients(Request $request)
    {
    $id = $request->input('facility_id');
    $search = $request->input('searchKey');
    $sql = "select * from vw_ipd_patients where medical_record_number LIKE '%".$search."%' AND facility_id = '".$id."' AND admission_status_id = 2  GROUP BY patient_id ";
    $diag = DB::select(DB::raw($sql));
    return $diag;
    }

    public function getOpdPatients(Request $request)
    {
    $per_page =  (isset($request['per_page'])? $request['per_page'] : 25);
    $sql = "SELECT distinct * FROM `opd_patients` WHERE facility_id ='".$request['facility_id']."' AND tallied IS NULL";
    $data = DB::select(DB::raw($sql));
    $all =  customPaginate($data, $per_page);
    return $all;
    }
	
    public function getAllOpdPatients(Request $request)
    {
        return DB::select("SELECT distinct * FROM `opd_patients` WHERE name LIKE '%".$request->input('searchKey')."%' AND facility_id ='".$request->input('facility_id')."'");
    }
    public function checkPatientAttendance(Request $request){
    $patient_id = $request->patient_id;
    $sql = "SELECT * FROM tbl_accounts_numbers WHERE patient_id ='".$patient_id."' AND  year(date_attended) = year(CURRENT_DATE) AND tallied IS NULL ";
    $resData = (count(DB::select(DB::raw($sql))) ? 1 : 0);
    return $resData;
    }

    public function getPatientCategories()
    {
        $sql = "select * from tbl_pay_cat_sub_categories";
        $cat = DB::select(DB::raw($sql));
        return $cat;
    }
    public function filterByCategory(Request $request)
    {
        $id = $request['bill_id'];
        $facility_id = $request['facility_id'];
        $per_page =  (isset($id['per_page'])? $id['per_page'] : 25);
        $sql = "select distinct * from opd_patients where  bill_id =$id AND facility_id =$facility_id GROUP BY patient_id ORDER BY first_name limit 20";
        $byCat = DB::select(DB::raw($sql));
        $all =  customPaginate($byCat, $per_page);
        return $all;
    }

    public function filterByWards($ward_id)
    {
    $id = $ward_id;
    $per_page =  (isset($id['per_page'])? $id['per_page'] : 25);
    $sql = "select * from vw_ipd_patients where  ward_id = '".$ward_id."' GROUP BY patient_id ";
    $byWard = DB::select(DB::raw($sql));
    $all =  customPaginate($byWard, $per_page);
    return $all;
    }

    public function getRegisteredWards($facility_id)
    {
    $wards=DB::table('vw_wards')->where('facility_id',$facility_id)->get();
    return $wards;
    }

    public function getSpecialClinics()
    {
    return DB::table('tbl_departments')->where('id','>',7)->get();
    }

    public function getFacilityInfo($id)
    {
    $facility=DB::table('vw_user_access_level')
    ->where('user_id',$id)
    ->get();
    return $facility;
    }
    public function getNotes($patient_id)
    {   $patient = [];
    $sql = "select * from vw_conti_notes where patient_id = '".$patient_id."' AND notes_type = 1 ORDER BY created_at DESC ";
    $sql1 = "select * from vw_conti_notes where patient_id = '".$patient_id."' AND notes_type = 2 ORDER BY created_at DESC ";
    $patient[] = DB::select(DB::raw($sql));
    $patient[] = DB::select(DB::raw($sql1));
    return $patient;
    }

    public function postNotes(Request $request)
    {
    $data      =  $request->all();
    $validator =  Validator::make($data, Tbl_continuation_note::$create_rules);

    if ($validator->fails()) {
    return customApiResponse($data, "Validation Error", 400, $validator->errors()->all());
    }

    $result = Tbl_continuation_note::create($data);

    if($result) {
    return customApiResponse($result, 'SUCCESSFULLY_CREATED', 201);
    } else {
    return customApiResponse($data, 'ERROR', 500);
    }
    }

    public function getDiagnosis(Request $request)
    {
        $search = $request->input('search');
        $limit = 20;
        $sql = "select * from tbl_diagnosis_descriptions where CODE NOT LIKE 'OP%' AND CODE NOT LIKE 'IP%' AND (description like '%".$search."%' or code like '%".$search."%') order by length(description) asc, code limit ".$limit;
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
	
    public function postDiagnosis(Request $request)
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
    if($diag) {
    return customApiResponse($diag, 'DIAGNOSIS SUCCESSFULLY SAVED', 201);
    } else {
    return customApiResponse($diag, 'ERROR WHILE SAVING', 500);
    }
    }
    public function getSubDepts()
    {
    return DB::table('tbl_sub_departments')
    ->whereBetween('department_id',[2,3])
    ->get();
    }

    public function getPanels(Request $request)
    {
    $sub = $request->input('sub_dept_id');
    $facility_id = $request->input('facility_id');
    $patient_category_id = $request->input('patient_category_id');
    $sql=" select * from `vw_labpanels` where sub_dept_id='".$sub." ' AND patient_category_id ='".$patient_category_id."' AND facility_id = '".$facility_id."' GROUP BY item_id";
    $panel = DB::select(DB::raw($sql));
    return $panel;
    }

    public function getTests(Request $request)
    {   $sub = $request->input('sub_dept_id');
    $facility_id = $request->input('facility_id');
    $patient_category_id = $request->input('patient_category_id');
    $sql=" SELECT * FROM `vw_investigations_tests` WHERE sub_dept_id='".$sub."'  AND patient_category_id ='".$patient_category_id."' AND facility_id ='".$facility_id."' GROUP BY item_id";
    $investigation = DB::select(DB::raw($sql));
    return $investigation;
    }

    public function getSingleTests(Request $request)
    {   $sub = $request->input('sub_dept_id');
    $facility_id = $request->input('facility_id');
    $patient_category_id = $request->input('patient_category_id');
    $sql=" SELECT * FROM `vw_labtests_to_doctors` WHERE sub_dept_id='".$sub."' AND patient_category_id ='".$patient_category_id."' AND facility_id = '".$facility_id."' GROUP BY item_id";
    $investigation = DB::select(DB::raw($sql));
    return $investigation;
    }

    public function postInvestigations(Request $request)
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
     $postData = Tbl_request::create(["requesting_department_id"=>$requesting_department_id,"doctor_id"=>$user_id,"patient_id"=>$patient_id,"visit_date_id"=>$account_number_id,"eraser"=>1,"admission_id"=>$admission_id]);
    $id = $postData->id;
    foreach ($clinicalData as $d){
    $postData = Tbl_order::create(['priority'=>$priority,'clinical_note'=>$clinical_note,'test_id'=>$d['item_id'],'order_id'=>$id,"eraser"=>1,]);
    }
    $postData = Tbl_encounter_invoice::create(["account_number_id"=>$account_number_id, "user_id"=>$user_id,"facility_id"=>$facility_id,]);
    $invoice_id = $postData->id;

    foreach ($clinicalData as $b){
     $postData = Tbl_invoice_line::create(["invoice_id"=>$invoice_id,"item_type_id"=>$b['item_type_id'],
    "quantity"=>$b['quantity'],"item_price_id"=>$b['item_price_id'],"user_id"=>$b['user_id'],"patient_id"=>$b['patient_id'],
    "status_id"=>$b['status_id'],"facility_id"=>$b['facility_id'],"discount_by"=>$b['user_id'],"payment_filter"=>$b['payment_filter'],"discount"=>0, ]);
    }
    if($postData) {
    return customApiResponse($postData, 'INVESTIGATIONS SUCCESSFULLY SAVED', 201);
    } else {
    return customApiResponse($postData, 'ERROR', 500);
    }
    }
    public function getInvestigationResults(Request $request)
    {
    $dept = $request->input('dept_id');
    $pt = $request->input('patient_id');
    $date = $request->input('account_id');
    $sql = "select * from vw_investigation_results where patient_id = '".$pt."' AND dept_id = '".$dept."' AND account_id = '".$date."'";
    $rs = DB::select(DB::raw($sql));
    return $rs;
    }

    public function getResults(Request $request)
    {
    $dept = $request->input('dept_id');
    $pt = $request->input('patient_id');
    $limit = 5;
    $sql = "select date_attended,account_id,patient_id,dept_id from vw_investigation_results where patient_id = '".$pt."' AND dept_id = '".$dept."' GROUP BY account_id ORDER BY date_attended DESC  limit ".$limit;
    $diag = DB::select(DB::raw($sql));
    return $diag;
    }

    public function getMedicine(Request $request)
    {
    $search = $request->input('search');
    $id = $request->input('facility_id');
    $category_id = $request->input('patient_category_id');
    $limit = 10;
    $sql = "select * from vw_shop_items where item_name like '%".$search."%' AND dept_id= 4 AND patient_category_id ='".$category_id."' AND facility_id = '".$id."' limit ".$limit;
    $patient = DB::select(DB::raw($sql));
    return $patient;
    }

    public function balanceCheck(Request $request)
    {
    $id = $request->input('facility_id');
    $item_id = $request->input('item_id');
    $main_category_id = $request->input('main_category_id');
    $sql = "select balance from vw_dispensing_item_balance where item_id ='".$item_id."' AND facility_id ='".$id."' AND main_category_id ='".$main_category_id."' ";
    $patient = DB::select(DB::raw($sql));
    return $patient;

    }

    public function dosageChecker(Request $request)
    {
    $patient_id = $request->input('patient_id');
    $item_id = $request->input('item_id');
    $sql = "SELECT * FROM `vw_previous_medications` WHERE `patient_id`='".$patient_id."' AND `item_id`='".$item_id."' AND (duration-days)>0 ORDER BY `start_date` DESC LIMIT 1 ";
    $data = DB::select(DB::raw($sql));
    return $data;

    }
    public function conservatives(Request $request)
    {
    if(count($request->all())>0){
    $billsData =   Tbl_prescription::create($request->all());
    if($billsData) {
    return customApiResponse($billsData, 'CONSERVATIVE MGT SAVED', 201);
    } else {
    return customApiResponse($billsData, 'ERROR', 500);
    }
    }
    }

    public function postMedicines(Request $request)
    {
    $date = date('Y-m-d');
    foreach ($request->all() as $b){
    $medData2 = Tbl_prescription::create(["item_id"=>$b['item_id'],"patient_id"=>$b['patient_id'],"admission_id"=>$b['admission_id'],"visit_id"=>$b['visit_id'],
    "prescriber_id"=>$b['user_id'],"quantity"=>$b['quantity'],"frequency"=>$b['frequency'],"duration"=>$b['duration'],
    "dose"=>$b['dose'],"start_date"=>$date,"instruction"=>$b['instructions'],"out_of_stock"=>$b['out_of_stock']]);
    }
    if($medData2) {
    return customApiResponse($medData2, 'PRESCRIPTION SAVED', 201);
    } else {
    return customApiResponse($medData2, 'ERROR', 500);
    }
    }
    public function rejectedMedicines(Request $request)
    {
    $patient_id = $request['patient_id'];
    $visit_id = $request['account_id'];
    $sql = "SELECT t1.patient_id,t1.prescriber_id,t1.item_id,t1.visit_id,t1.dose,t1.frequency,t1.duration,t1.cancellation_reason,t2.name,t2.mobile_number,t3.item_name
    FROM tbl_prescriptions t1 
    INNER JOIN users t2 ON t1.prescriber_id =t2.id
    INNER JOIN tbl_items t3 ON t3.id=t1.item_id WHERE patient_id ='".$patient_id."' AND visit_id ='".$visit_id."' AND dispensing_status =3";
    return DB::select(DB::raw($sql));
    }
    public function allOrderedInvestigations(Request $request)
    {
    $patient_id = $request['patient_id'];
    $visit_id = $request['account_id'];
    $sql = "SELECT t3.item_name,t2.created_at AS date_ordered,t4.description,t4.attached_image,t5.name,t5.mobile_number
    FROM tbl_requests t1 INNER JOIN tbl_orders t2 ON t1.id = t2.order_id AND DATE(t1.created_at) = DATE(t2.created_at) INNER JOIN tbl_items t3 ON t3.id = t2.test_id 
    INNER JOIN tbl_results t4 ON t4.order_id = t1.id INNER JOIN users t5 ON t5.id = t1.doctor_id 
    WHERE t1.patient_id = '".$patient_id."' ";
    return DB::select(DB::raw($sql));
    }

    public function updateMedicines(Request $request)
    {
    $patient_id = $request->patient_id;
    $visit_id = $request->visit_id;
    $item_id = $request->item_id;
    $dose = $request->dose;
    $frequency = $request->frequency;
    $duration = $request->duration;
    $prescriber_id = $request->prescriber_id;
    $data = Tbl_prescription::where('patient_id',$patient_id)->where('visit_id',$visit_id)->where('item_id',$item_id)
    ->update([
    'dose'=>$dose,
    'frequency'=>$frequency,
    'duration'=>$duration,
    'duration'=>$duration,
    'prescriber_id'=>$prescriber_id,
    'verifier_id'=>null,
    'dispensing_status'=>null,
    ]);
    if($data) {
    return customApiResponse($data, 'PRESCRIPTION SUCCESSFULLY UPDATED', 201);
    } else {
    return customApiResponse($data, 'ERROR', 500);
    }
    }
    public function postMedicalSupplies(Request $request)
    {
    $date = date('Y-m-d');
    foreach ($request->all() as $b){
    $medData2 = Tbl_prescription::create(["item_id"=>$b['item_id'],"patient_id"=>$b['patient_id'],"admission_id"=>$b['admission_id'],"visit_id"=>$b['visit_id'],
    "prescriber_id"=>$b['user_id'],"quantity"=>$b['quantity'],"start_date"=>$date,"out_of_stock"=>$b['out_of_stock']]);
    }
    if($medData2) {
    return customApiResponse($medData2, 'MEDICAL SUPPLIES SUCCESSFULLY ORDERED', 201);
    } else {
    return customApiResponse($medData2, 'ERROR', 500);
    }
    }
    public function getPrevMedicine($patient_id)
    {
    $sql = "SELECT * FROM vw_previous_medications WHERE patient_id ='".$patient_id."' ORDER BY start_date DESC LIMIT 30";
    $data = DB::select(DB::raw($sql));
    return $data;
    }
    public function getPrevMedications(Request $request)
    {
    $id = $request->input('patient_id');
    $date = $request->input('account_id');
    $sql = "SELECT * FROM vw_previous_medications WHERE patient_id ='".$id."' AND visit_id = '".$date."' LIMIT 30";
    $data = DB::select(DB::raw($sql));
    return $data;
    }

    public function getPrevProcedures($patient_id)
    {
    $sql = "SELECT * FROM vw_previous_procedures WHERE patient_id = '".$patient_id."' ORDER BY created_at DESC LIMIT 30  ";
    $data = DB::select(DB::raw($sql));
    return $data;
    }
    public function getPastProcedures(Request $request)
    {
    $id = $request->input('patient_id');
    $dt = $request->input('account_id');
    $sql = "SELECT * FROM vw_previous_procedures WHERE patient_id ='".$id."' AND visit_id ='".$dt."' ";
    $data = DB::select(DB::raw($sql));
    return $data;
    }

    public function outOfStockMedicine(Request $request)
    {
    $date = date('Y-m-d');
    if(count($request->all())>0){
    foreach ($request->all() as $b){
    $medData2 = Tbl_prescription::create(["item_id"=>$b['item_id'],"patient_id"=>$b['patient_id'],"admission_id"=>$b['admission_id'],"visit_id"=>$b['visit_id'],
    "prescriber_id"=>$b['user_id'],"quantity"=>$b['quantity'],"frequency"=>$b['frequency'],"duration"=>$b['duration'],
    "dose"=>$b['dose'],"start_date"=>$date,"instruction"=>$b['instructions'],"out_of_stock"=>$b['out_of_stock']]);
    }
        if($medData2) {
            return customApiResponse($medData2, 'SAVED UNDER OS', 201);
        } else {
            return customApiResponse($medData2, 'ERROR', 500);
        }
    }
    }
    public function outOfStockMedicalSupplies(Request $request)
    {
    $date = date('Y-m-d');
    if(count($request->all())>0){
    foreach ($request->all() as $b){
    $medData2 = Tbl_prescription::create(["item_id"=>$b['item_id'],"patient_id"=>$b['patient_id'],
    "prescriber_id"=>$b['user_id'],"quantity"=>$b['quantity'],"start_date"=>$date,"out_of_stock"=>$b['out_of_stock']]);
    }
    if($medData2) {
    return customApiResponse($medData2, 'SAVED UNDER OS', 201);
    } else {
    return customApiResponse($medData2, 'ERROR', 500);
    }
    }
    }
    public function postPatientProcedures(Request $request)
    {
    $dtz = json_encode($request->all());
    $data = json_decode($dtz);
    $user_id =  $data[0]->user_id;
    $account_number_id =  $data[0]->account_number_id;
    $facility_id =  $data[0]->facility_id;
    $data = Tbl_encounter_invoice::create(["account_number_id"=>$account_number_id, "user_id"=>$user_id,"facility_id"=>$facility_id,]);
    $invoice_id=$data->id;
    foreach ($request->all() as $b){
    $Data = Tbl_invoice_line::create(["payment_filter"=>$b['payment_filter'],"invoice_id"=>$invoice_id,"item_type_id"=>$b['item_type_id'],
    "quantity"=>$b['quantity'],"item_price_id"=>$b['item_price_id'],"user_id"=>$b['user_id'],"patient_id"=>$b['patient_id'],
    "status_id"=>$b['status_id'],"facility_id"=>$b['facility_id'],"discount_by"=>$b['user_id'],"discount"=>0, ]);
    }

    foreach ($request->all() as $b){
    $Data2 = Tbl_patient_procedure::create(["item_id"=>$b['item_id'],"patient_id"=>$b['patient_id'],
    "user_id"=>$b['user_id'],"visit_date_id"=>$b['account_number_id'],"admission_id"=>$b['admission_id'],]);
    }
    if($Data2) {
    return customApiResponse($Data2, 'PROCEDURES SUCCESSFULLY ORDERED', 201);
    } else {
    return customApiResponse($Data2, 'ERROR', 500);
    }
    }
    public function getStores($facility_id)
    {
    $sql = "select * from tbl_store_lists where  facility_id =$facility_id AND store_type_id =4 ";
    $store = DB::select(DB::raw($sql));
    return $store;
    }
    public function getMedicineByStore(Request $request)
    {
    $store = $request['store_id'];
    $sql = "SELECT * FROM vw_dispensing_item_balance WHERE dispenser_id = '".$store."' GROUP BY item_id ORDER BY item_name";
    $store = DB::select(DB::raw($sql));
    return $store;
    }
    public function getMedicalSupplies(Request $request)
    {
    $search = $request->input('search');
    $id = $request->input('facility_id');
    $category_id = $request->input('patient_category_id');
    $sql = "select * from vw_shop_items where item_name like '%".$search."%' AND item_category ='Medical Supplies' AND patient_category_id ='".$category_id."' AND facility_id = '".$id."' LIMIT 10 ";
    $patient = DB::select(DB::raw($sql));
    return $patient;
    }

    public function getPatientProcedures(Request $request)
    {
    $search = $request->input('search');
    $id = $request->input('facility_id');
    $category_id = $request->input('patient_category_id');
    $limit = 10;
    $sql = "select * from vw_shop_items where item_name like '%".$search."%' AND (item_category ='PROCEDURE' OR item_category ='SPECIALISED PROCEDURES' OR item_category ='MAJOR PROCEDURES' OR item_category='MINOR PROCEDURES' ) AND patient_category_id ='".$category_id."' AND facility_id ='".$id."' limit ".$limit;
    $patient = DB::select(DB::raw($sql));
    return $patient;
    }
    public function requestBlood(Request $request)
    {
    $Data2 =  Tbl_blood_request::create($request->all());
    if($Data2) {
    return customApiResponse($Data2, 'BLOOD REQUESTED', 201);
    } else {
    return customApiResponse($Data2, 'ERROR', 500);
    }
    }
    public function dischargerReport(Request $request)
    {
    $id= $request->patient_id;
    $visit_id= $request->visit_id;
    $data=[];
    $sql1 = "select * from vw_prev_diagnosis where patient_id = '".$id."' AND visit_date_id = '".$visit_id."' AND admission_id IS NULL ";
    $sql2 = "select * from vw_prev_diagnosis where patient_id = '".$id."' AND visit_date_id = '".$visit_id."' AND admission_id IS NOT NULL ";
    $sql3= "SELECT * FROM vw_previous_medications WHERE patient_id = '".$id."' AND visit_id = '".$visit_id."' ";
    $sql4 = "SELECT * FROM vw_previous_procedures WHERE patient_id = '".$id."' AND visit_id ='".$visit_id."' ";
    $sql5 = "select * from vw_investigation_results where patient_id = '".$id."' AND account_id = '".$visit_id."' GROUP BY item_id ";
    $data[]= DB::select(DB::raw($sql1));
    $data[]= DB::select(DB::raw($sql2));
    $data[]= DB::select(DB::raw($sql3));
    $data[]= DB::select(DB::raw($sql4));
    $data[]= DB::select(DB::raw($sql5));
    return $data;
    }

    public function dischargePatient(Request $request)
    {
    $patient_id = $request['patient_id'];
    $facility_id = $request['facility_id'];
    $account_id = $request['account_id'];
    $admission_status=4;
    if(patientRegistration::duplicate('tbl_admissions',array('patient_id','account_id','admission_status_id','facility_id'),
    array($patient_id,$account_id,$admission_status,$facility_id,''))==true){
    return response()->json([
    'data' => 'Patient already discharged.',
    'status' => '0'
    ]);
    }
    else {
     $Data2 = Tbl_admission::where('patient_id',$patient_id)->where('account_id',$account_id)->where('facility_id',$facility_id)->where('admission_status_id',2)
     ->update([
    'admission_status_id'=>3,
    ]);

    if($Data2) {
        return customApiResponse($Data2, 'PATIENT DISCHARGED SUCCESSFULLY', 201);
    } else {
        return customApiResponse($Data2, 'ERROR', 500);
    }

    }
    }
    public function getConsultation(Request $request)
    {
    $bill_id = $request->input('patient_category_id');
    $facility_id = $request->input('facility_id');
    $dept_id = $request->input('dept_id');
    $sql="SELECT * FROM vw_shop_items WHERE item_name LIKE '%consultation%'  AND patient_category_id='".$bill_id."' AND dept_id = '".$dept_id."' AND facility_id='".$facility_id."'  ";
    $con = DB::select(DB::raw($sql));
    return $con;
    }

    public function postToClinics(Request $request)
    {
    if(count($request->all())>0){
    $data = $request->all();
    $data2 = Tbl_encounter_invoice::create($data);
    $invoice_id=$data2->id;
    $medData = Tbl_invoice_line::create(["invoice_id"=>$invoice_id,
    "item_type_id"=>$request->input('item_type_id'),
    "payment_filter"=>$request->input('payment_filter'),
    "quantity"=>$request->input('quantity'),
    "item_price_id"=>$request->input('item_price_id'),
    "user_id"=>$request->input('user_id'),
    "patient_id"=>$request->input('patient_id'),
    "status_id"=>$request->input('status_id'),
    "facility_id"=>$request->input('facility_id'),
    "discount_by"=>$request->input('user_id'),
    "discount"=>0 ]);

        $Data2 = Tbl_clinic_instruction::create($request->all());

        if($Data2) {
            return customApiResponse($Data2, 'PATIENT TRANSFERRED SUCCESSFULLY', 201);
        } else {
            return customApiResponse($Data2, 'ERROR', 500);
        }
    }

    }
    public function certifyCorpse(Request $request)
    {
    $id = $request['id'];
    $corpse_id = $request['id'];
    $immediate_cause = $request['immediate_cause'];
    $underlying_cause = $request['underlying_cause'];
    $user_id = $request['death_certifier'];
    $facility_id = $request['facility_id'];
    $mortuary_id = $request['mortuary_id'];
    $mortuary_name = $request['mortuary_name'];
    $time = date('Y-m-d H:i:s');
    $corpse = Tbl_corpse::where('id',$id)
    ->where('facility_id',$facility_id)
    ->update([
    "immediate_cause" =>$immediate_cause,
    "underlying_cause" =>$underlying_cause,
    "death_certifier" =>$user_id,
    "time_of_death_certifier" =>$time,
    ]);
    $request_mortuary = Tbl_corpse_admission::create([
    "facility_id"=>$facility_id,
    "admission_status_id"=>1,
    "corpse_id"=>$corpse_id,
    "admission_date"=>DATE('Y-m-d'),
    "mortuary_id"=>$mortuary_id,
    "user_id"=>$user_id

    ]);

    if($request_mortuary) {
    return customApiResponse($request_mortuary, 'SUCCESSFULLY SAVED', 201);
    } else {
    return customApiResponse($request_mortuary, 'ERROR', 500);
    }
    }
    
	public function postDeceased(Request $request)
    {
        $date = date('Y-m-d');
        $patient_id=$request->input('patient_id');
        if(patientRegistration::duplicate('tbl_corpse_admissions',array('patient_id'),
                array($patient_id,''))==true){

            return response()->json([
                'data' => 'Cannot proceed. The same information has been saved before',
                'status' => '0'
            ]);
        }
       else{

            $dod=Date('Y-m-d');
            $dataFromDB=Tbl_patient::where('id',$request->patient_id)->take(1)->get();

            // $data = patientRegistration::corpsesNumber($request);

            $registrationInfos=['gender'=>$dataFromDB[0]->gender,
                'dob'=>$dataFromDB[0]->gender,
                'dod'=>$dod,
                'first_name'=>$dataFromDB[0]->first_name,
                'middle_name'=>$dataFromDB[0]->middle_name,
                'last_name'=>$dataFromDB[0]->last_name,
                'residence_id'=>$dataFromDB[0]->residence_id,
                'residence_found'=>$dataFromDB[0]->residence_id,
                'country_id'=>$dataFromDB[0]->country_id,
                'facility_id'=>$dataFromDB[0]->facility_id,
                'mobile_number'=>"",
                'transport'=>"",
                'storage_reason'=>"STORAGE",
                'relationship'=>"",
                'corpse_brought_by'=>"",
                'description'=>"",
                'corpse_conditions'=>"",
                'corpse_properties'=>"",
                'corpse_properties_given_to'=>"",
                'diagnosis_id'=>$request->input('diagnosis_id'),
                'underlying_cause'=>$request->input('underlying_cause'),
                'immediate_cause'=>$request->input('immediate_cause'),
                'diagnosis_code'=>$request->input('diagnosis_code'),
                'death_certifier'=>$request->input('user_id'),

                'user_id'=>$request->input('user_id')];

            //return  $registrationInfos;


            $data=patientRegistration::corpsesNumber($registrationInfos);
            $data2= $data->id;
			
			$corpse = Tbl_corpse::where('id',$data2)
            ->update([
                "immediate_cause" =>$request->input('immediate_cause'),
                "underlying_cause" =>$request->input('underlying_cause'),
                "death_certifier" =>$request->input('user_id')
            ]);
            $corpse_admission = Tbl_corpse_admission::create([
                'patient_id'=>$dataFromDB[0]->id,
                'corpse_id'=>$data2,
                'facility_id'=>$request->input('facility_id'),
                'user_id'=>$request->input('user_id'),

                'admission_date'=>$date,
                'admission_status_id'=>1,
                'mortuary_id'=>1,
            ]);
            return $corpse_admission;
        }
    }

    public function chiefComplaints($search)
    {
    $limit = 50;
    $sql = "select * from tbl_body_systems where name like '%".$search."%' AND category !='Past Medical History'
     AND category !='Immunisation' AND category !='Admission History' limit ".$limit;
    $patient = DB::select(DB::raw($sql));
    return $patient;
    }

    public function postHistory(Request $request)
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
    $postData2 = Tbl_complaint::create([
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
    }
    if( $postData2) {
    return customApiResponse($postData2, 'COMPLAINS SUCCESSFULLY SAVED', 201);
    } else {
    return customApiResponse($postData2, 'ERROR', 500);
    }
    }

    public function postHpi(Request $request)
    {
    if(count($request->all())>0){
    $hpi =  $request['hpi'];
    $data2 = Tbl_history_examination::create($request->all());
    $id = $data2->id;
    $postData2 = Tbl_complaint::create([
    'hpi'=>$hpi,
    'history_exam_id'=>$id,
    ]);
    if( $postData2) {
        return customApiResponse($postData2, 'HPI SUCCESSFULLY SAVED', 201);
    } else {
        return customApiResponse($postData2, 'ERROR', 500);
    }
    }
    }

    public function postRoS(Request $request)
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
    $postData = Tbl_review_of_system::create([
    'review_summary'=>$data,
    'review_system_id'=>$id,
    ]);
    }
    if($postData) {
        return customApiResponse($postData, 'RoS SUCCESSFULLY SAVED', 201);
    } else {
        return customApiResponse($postData, 'ERROR', 500);
    }
    }

    public function postPastMed(Request $request)
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
    if ($data) {
    $postData = new Tbl_past_medical_record($data);
    $postData['past_medical_history_id'] = $id;
    $postData->save();
    }
    if($postData) {
    return customApiResponse($postData, 'PAST MEDICAL HISTORY SUCCESSFULLY SAVED', 201);
    } else {
    return customApiResponse($postData, 'ERROR', 500);
    }
    }

    public function reviewOfSystems(Request $request)
    {
    $search = $request['search'];
    $category = $request['category'];
    $sql = "select * from tbl_body_systems where name like '%".$search."%' AND category ='".$category."'
    ";
    $patient = DB::select(DB::raw($sql));
    return $patient;
    }

    public function postLocalPhysical(Request $request)
    {
    if(count($request->all())>0){
    $data2 = Tbl_physical_examination ::create($request->all());
    $id = $data2->id;
    $postData = Tbl_physical_examination_record::create(['local_examination'=>$request['local_examination'],'physical_examination_id'=>$id,
    ]);
    if($postData) {
    return customApiResponse($postData, 'LOCAL EXAMINATION SUCCESSFULLY SAVED', 201);
    } else {
    return customApiResponse($postData, 'ERROR', 500);
    }
    }
    }
    public function postGenPhysical(Request $request)
    {

    $data2 = Tbl_physical_examination ::create($request->all());
    $id = $data2->id;
    $postData = Tbl_physical_examination_record::create(['gen_examination'=>$request['gen_examination'],'physical_examination_id'=>$id,
    ]);
    if($postData) {
    return customApiResponse($postData, 'GENERAL EXAMINATION SUCCESSFULLY SAVED', 201);
    } else {
    return customApiResponse($postData, 'ERROR', 500);
    }
    }
    public function postSummaryPhysical(Request $request)
    {
    if(count($request->all())>0){
    $data2 = Tbl_physical_examination ::create($request->all());
    $id = $data2->id;
    $postData = Tbl_physical_examination_record::create(['summary_examination'=>$request['summary_examination'],'physical_examination_id'=>$id,
    ]);
    if($postData) {
    return customApiResponse($postData, 'SUMMARY EXAMINATION SUCCESSFULLY SAVED', 201);
    } else {
    return customApiResponse($postData, 'ERROR', 500);
    }
    }
    }
    public function postOtherSummary(Request $request)
    {
    if(count($request->all())>0){
    $data2 = Tbl_physical_examination ::create($request->all());
    $id = $data2->id;
    $postData = Tbl_physical_examination_record::create(['other_systems_summary'=>$request->input('other_systems_summary'),
    'system'=>$request->input('system'),
    'physical_examination_id'=>$id,
    ]);
    if($postData) {
    return customApiResponse($postData, 'OTHER EXAMINATION NOTES SUCCESSFULLY SAVED', 201);
    } else {
    return customApiResponse($postData, 'ERROR', 500);
    }
    }
    }
    public function postPhysical(Request $request)
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
    if($postData) {
    return customApiResponse($postData, 'SYSTEMIC EXAMINATION SUCCESSFULLY SAVED', 201);
    } else {
    return customApiResponse($postData, 'ERROR', 500);
     }
    }

    public function postReferral(Request $request)
    {
    $data = Tbl_referral::create($request->all());
    if($data) {
    return customApiResponse($data, 'SUCCESSFULLY_CREATED', 201);
    } else {
    return customApiResponse($data, 'ERROR', 500);
    }
    }

    public function getCorpseList(Request $request)
    {
     $per_page =  (isset($request['per_page'])? $request['per_page'] : 25);
    $sql = "SELECT * FROM tbl_corpses WHERE  facility_id = '".$request['facility_id']."' AND immediate_cause IS NULL GROUP BY corpse_record_number ";
    $corpse = DB::select(DB::raw($sql));
    $all = customPaginate($corpse,$per_page);
    return $all;
    }

    public function getBillList(Request $request)
    {
    $per_page =  (isset($request['per_page'])? $request['per_page'] : 25);
    $bill = DB::table('vw_patients_with_pending_bills')->where('facility_id', $request->input('facility_id'))->get();
    $all = customPaginate($bill,$per_page);
    return $all;
    }
    public function cancelPatientBill(Request $request)
    {
    return DB::table('vw_pending_bills')->where('patient_id',$request->input('patient_id'))->get();
    }

    public function cancelBillItem(Request $request)
    {
    $data = DB::table('tbl_invoice_lines')->where('id', $request->input('id'))->update(['status_id' => 3,'user_id' => $request->input('user_id'),]);
    if($data) {
        return customApiResponse($data, 'SUCCESSFULLY CANCELLED', 201);
    } else {
        return customApiResponse($data, 'ERROR', 500);
    }
    }

    public function admitPatient(Request $request)
    {
    $date = date('Y-m-d H:m:s');
    $patient_id=$request->input('patient_id');
    $facility_id=$request->input('facility_id');
    $account_id=$request->input('account_id');
    $admission_status_id=1;
    $admission_status=2;
    if(patientRegistration::duplicate('tbl_admissions',array('patient_id','admission_status_id','facility_id'),
    array($patient_id,$admission_status_id,$facility_id,''))==true){
    return response()->json([
    'data' => 'Ooops!..Patient already admitted but still in pending mode..Please contact nurse in-charge.',
    'status' => '0'
    ]);
    }
    else if(patientRegistration::duplicate('tbl_admissions',array('patient_id','admission_status_id','facility_id'),
    array($patient_id,$admission_status,$facility_id,''))==true){
    return response()->json([
    'data' => 'Patient already admitted.',
    'status' => '0'
    ]);
    }
    else {
    $data = Tbl_admission::create([
    'admission_date' => $date,'account_id' => $account_id, 'patient_id' => $request->input('patient_id'), 'admission_status_id' => $request->input('admission_status_id'),
    'facility_id' => $request->input('facility_id'), 'user_id' => $request->input('user_id'),
    ]);
    $adm_id = $data->id;
    $admission = Tbl_instruction::create(['instructions' => $request->input('instructions'),
    'facility_id' => $request->input('facility_id'), 'user_id' => $request->input('user_id'),
    'admission_id' => $adm_id, 'patient_id' => $request->input('patient_id'), 'ward_id' => $request->input('ward_id'),
    ]);
    if($data) {
    return customApiResponse($data, 'PATIENT SUCCESSFULLY ADMITTED', 201);
    } else {
    return customApiResponse($data, 'ERROR', 500);
    }
    }
    }

    public function getAllInvPatients(Request $request)
    {
    $pt = $request->input('facility_id');
    $sql = "select * from vw_investigation_results where facility_id = '".$pt."'  AND (timestampdiff(hour,created_at,CURRENT_TIMESTAMP)<=120) GROUP BY patient_id ";
    $diag = DB::select(DB::raw($sql));
    return $diag;
    }
    public function postUnavailableInvestigations(Request $request)
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
    public function doctorsPerformance(Request $request)
    {
        $performance = [];
        $start_date=date('Y-m-01 00:00:00');
        $end_date=date("Y-m-d H:i:s");
        $start=$request->input('start');
        $end=$request->input('end');
        $facility_id=$request->input('facility_id');
        $user_id=$request->input('user_id');
        $sql = "SELECT SUM(total_clerked) AS total_clients FROM vw_doctor_perfomances WHERE doctor_id = '".$user_id."' AND (time_treated BETWEEN '".$start."' AND '".$end."') AND facility_id = '".$facility_id."' ";
        $sql2 = "SELECT SUM(total_clerked) AS total_patients FROM vw_doctor_perfomances WHERE doctor_id = '".$user_id."' AND (time_treated BETWEEN '".$start_date."' AND '".$end_date."') AND facility_id = '".$facility_id."' ";
        $performance[] = DB::select(DB::raw($sql));
        $performance[] = DB::select(DB::raw($sql2));
        return $performance;

    }

}