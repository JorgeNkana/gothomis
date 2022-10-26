<?php

namespace App\Http\Controllers\ClinicalServices;
use App\nursing_care\Tbl_status_ward;
use App\BloodBank\Tbl_blood_request;
use App\classes\patientRegistration;
use App\Patient\Tbl_accounts_number;
use App\ClinicalServices\Tbl_admission;
use App\ClinicalServices\Tbl_bills_category;
use App\ClinicalServices\Tbl_birth_history;
use App\ClinicalServices\Tbl_child_birth_history;
use App\ClinicalServices\Tbl_complaint;
use App\ClinicalServices\Tbl_continuation_note;
use App\ClinicalServices\Tbl_diagnosis;
use App\ClinicalServices\Tbl_diagnosis_detail;
use App\ClinicalServices\Tbl_family_history;
use App\ClinicalServices\Tbl_family_social_history;
use App\ClinicalServices\Tbl_history_examination;
use App\ClinicalServices\Tbl_icu_entry;
use App\ClinicalServices\Tbl_instruction;
use App\ClinicalServices\Tbl_obs_gyn;
use App\ClinicalServices\Tbl_obs_gyn_record;
use App\ClinicalServices\Tbl_order;
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
use App\Clinics\Tbl_clinic_instruction;
use App\Department\Tbl_department;
use App\Mortuary\Tbl_corpse_admission;
use App\Patient\Tbl_corpse;
use App\Patient\Tbl_patient;
use App\Facility\Tbl_facility;
use App\Patient\Tbl_encounter_invoice;
use App\Payments\Tbl_invoice_line;
use App\admin\Tbl_integrating_key;
use App\classes\DataSnch;
use Illuminate\Http\Request;
use App\User;
use App\classes\ServiceManager;
use App\admin\Tbl_route_key;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\classes\SystemTracking;
use App\Trackable;
class clinicalController extends Controller
{
    //previous visits
    public function previousVisits(Request $request)
    {
        $id = $request->input('patient_id');
        $sql = "SELECT date_attended,patient_id,created_at,id AS account_id FROM `tbl_accounts_numbers` WHERE patient_id ='".$id."' ORDER BY date_attended DESC LIMIT 5 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    //get patients for consultations
    public function getOpdPatients(Request $request)
    {
        return DB::select("SELECT t1.*,CONCAT('Residence:  ',residence_name) as residence_name FROM `opd_patients`  t1 join tbl_residences t2 on t1.residence_id=t2.id  WHERE facility_id ='".$request->input('facility_id')."' AND tallied IS NULL order by account_id ASC LIMIT 20 ");
    }
    public function getAllOpdPatients(Request $request)
    {
        return DB::select("SELECT t1.*,CONCAT('Residence:  ',residence_name) as residence_name FROM `opd_patients`  t1 join tbl_residences t2 on t1.residence_id=t2.id WHERE name LIKE '%".$request->input('searchKey')."%' AND facility_id ='".$request->input('facility_id')."' order by account_id ASC");
    }
	
	public function checkPatientAttendance(Request $request){
        $patient_id = $request->patient_id;
        $sql = "SELECT * FROM tbl_accounts_numbers WHERE patient_id ='".$patient_id."' AND  year(date_attended) = year(CURRENT_DATE) AND tallied IS NULL ";
        $resData = (count(DB::select(DB::raw($sql))) ? 1 : 0);
        return $resData;

    }
    public function getIpdPatients(Request $request)
    {   $id = $request->input('facility_id');
        $limit = 70;
        $sql = "select * from vw_ipd_patients where facility_id = '".$id."' GROUP BY patient_id limit ".$limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function getPatientAdmissionInfo(Request $request)
    {
        $patientInfo = [];
        $patient_id = $request['patient_id'];
        $visit_id = $request['visit_id'];
        $sql = "SELECT t1.discharge_summary,t1.created_at,t2.bed_id,t2.ward_id,t3.bed_name,
         u.name as discharged_by,u.mobile_number  FROM tbl_admissions t1
                INNER JOIN tbl_instructions t2 ON t1.id = t2.admission_id 
                INNER JOIN tbl_beds t3 ON t3.id = t2.bed_id
                left join users u  on t1.discharged_by=u.id
                 WHERE  t1.patient_id =$patient_id AND t1.account_id = $visit_id ";
       $data = DB::select(DB::raw($sql));
        $ward_id = $data[0]->ward_id;
        $sql1 = "SELECT id,ward_name FROM tbl_wards WHERE id =$ward_id ";
        $sql2 = "SELECT t2.residence_name FROM tbl_patients t1 INNER JOIN tbl_residences t2 on t2.id = t1.residence_id WHERE t1.id =$patient_id ";

        $patientInfo[] = DB::select(DB::raw($sql));
        $patientInfo[] = DB::select(DB::raw($sql1));
        $patientInfo[] = DB::select(DB::raw($sql2));
        $sql3 = "SELECT id,next_of_kin_name,mobile_number,relationship FROM tbl_next_of_kins WHERE  patient_id =$patient_id ";
        $data2 = DB::select(DB::raw($sql3));
        if(count($data2)>0){
            $kin_id = $data2[0]->id;
            $sql4 = "SELECT t2.residence_name FROM tbl_next_of_kins t1 INNER JOIN tbl_residences t2 on t2.id = t1.residence_id WHERE t1.id =$kin_id";
            $patientInfo[] = DB::select(DB::raw($sql3));
            $patientInfo[] = DB::select(DB::raw($sql4));
        }

        return $patientInfo;

    }
    public function getAllIpdPatients(Request $request)
    {   $id = $request->input('facility_id');
        $search = $request->input('searchKey');
        $sql = "select * from vw_ipd_patients where medical_record_number LIKE '%".$search."%' AND facility_id = '".$id."' AND admission_status_id = 2  GROUP BY patient_id ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function filterByWards(Request $request)
    {
        $ward_id = $request['ward_id'];
        $sql = "select * from vw_ipd_patients where  ward_id = '".$ward_id."' GROUP BY patient_id ";
        $byWard = DB::select(DB::raw($sql));
        return $byWard;
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

    public function vitalCaptions(Request $request)
    {
        $sql="SELECT * FROM tbl_vitals ";
        $vital_date = DB::select(DB::raw($sql));
        return $vital_date;
    }

    public function vitalsTime(Request $request)
    {
        $patient_id = $request->patient_id;
        $date_attended = $request->account_id;
        $sql="SELECT time_taken,account_id,created_at FROM vw_vital_sign_output WHERE patient_id='".$patient_id."' AND account_id='".$date_attended."' GROUP BY time_taken ";
        $vital_time = DB::select(DB::raw($sql));
        return $vital_time;
    }

    public function patientVitals(Request $request)
    {
        $patient_id = $request->patient_id;
        $account_id = $request->account_id;
        $time_attended = $request->time_taken;
        $sql="SELECT * FROM vw_vital_sign_output WHERE patient_id='".$patient_id."' AND created_at='".$time_attended."' AND account_id='".$account_id."' ";
        $vital_data = DB::select(DB::raw($sql));
        return $vital_data;
    }
    //transfers
    public function internalTransfer(Request $request)
    {
        $date = date('Y-m-d');
        $admission_id = $request->input('admission_id');
        $doctor_id = $request->input('doctor_id');
        $icu_status_id = $request->input('icu_status_id');
        $source = $request->input('from');
        $postData = Tbl_icu_entry::create([
            'admission_id'=>$admission_id,
            'doctor_id'=>$doctor_id,
            'icu_status_id'=>$icu_status_id,
            'from'=>$source,
            'date_admitted'=>$date,
        ]);
        $instructions = Tbl_instruction::create($request->all());
        $newData=$instructions;
        $patient_id=$newData->patient_id;
        $trackable_id=$newData->id;
        $user_id=$newData->user_id;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);
        $newData=$postData;
        $patient_id=$instructions->patient_id;
        $trackable_id=$newData->id;
        $user_id=$instructions->user_id;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

        return $instructions;
    }

    public function icuPatients(Request $request)
    {
        $id = $request->input('facility_id');
        $dept_id = $request->input('dept_id');
        $sql = "SELECT * FROM `vw_special_clinics_clients` WHERE dept_id='".$dept_id."'  AND received = 0 AND facility_id ='".$id."' AND patient_id NOT IN (SELECT patient_id FROM tbl_corpse_admissions) GROUP BY patient_id ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
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
                $newData=$os;
                $patient_id=$newData->patient_id;
                $trackable_id=$newData->id;
                $user_id=$newData->user_id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

            }
            return $os;
        }
    }
    public function icuVitals(Request $request)
    {
        $facility = $request->input('facility_id');
        $patient = $request->input('patient_id');
        $sql ="select vital_name,value,units from vw_icu_patients WHERE facility_id ='".$facility."' and patient_id ='".$patient."' group by vital_name ";
        return DB::select(DB::raw($sql));

    }
    //Allergies
    public function getAllergy(Request $request)
    {
        $patient_id = $request->input('patient_id');
        $sql = "select * from vw_allergies WHERE patient_id = '".$patient_id."' order by descriptions DESC";
        return DB::select(DB::raw($sql));
    }
    public function getAllergies(Request $request)
    {	$pastData = [];
        $patient_id = $request->input('patient_id');
        $date_attended = $request->input('date_attended');
        $id = $request->input('visit_date_id');
        $sql = "select * from vw_allergies WHERE patient_id ='".$patient_id."' AND visit_date_id = '".$id."' AND status IS NOT NULL ";

        $sql1 = "SELECT t1.*,t2.admission_id,t3.name,t4.prof_name FROM tbl_past_medical_records t1 
        INNER JOIN tbl_past_medical_histories t2 ON t2.id = t1.past_medical_history_id
        INNER JOIN users t3 ON t3.id = t2.user_id
        INNER JOIN tbl_proffesionals t4 ON t4.id = t3.proffesionals_id
        WHERE t2.patient_id ='".$patient_id."' AND t2.visit_date_id = '".$id."' AND t1.status IS NULL";
        $pastData [] = DB::select(DB::raw($sql));
        $pastData [] = DB::select(DB::raw($sql1));
        return $pastData;
    }
    //get chief complaints
    public function chiefComplaints(Request $request)
    {
        $search = $request->input('search');
        $limit = 10;
        $sql = "select * from tbl_body_systems where name like '%".$search."%' AND category !='Past Medical History'
         AND category !='Immunisation' AND category !='Admission History' limit ".$limit;
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    //review of systems
    public function reviewOfSystems(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $limit = 10;
        $sql = "select * from tbl_body_systems where name like '%".$search."%' AND category ='".$category."'
         limit ".$limit;
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function pastMedications(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $limit = 10;
        $sql = "select * from `vw_shop_items` where item_name like '%".$search."%' AND item_category ='".$category."'
         limit ".$limit;
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    //diagnosis
    public function getDiagnosis(Request $request)
    {
        $search = $request->input('search');
        $limit = 20;
        $sql = "select * from tbl_diagnosis_descriptions where CODE NOT LIKE 'OP%' AND CODE NOT LIKE 'IP%' AND (description like '%".$search."%' or code like '%".$search."%') order by length(description) asc, code limit ".$limit;
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function getSubDepts(Request $request)
    {  $id= $request->input('department_id');
        return DB::table('tbl_sub_departments')
            ->whereBetween('department_id',[2,3])
            ->get();
    }
    public function searchWards(Request $request)
    {
        $facility_id = $request->facility_id;
        return DB::table('tbl_wards')->where('facility_id',$facility_id)->get();
    }
    public function searchBeds(Request $request)
    {
        $ward_id = $request->ward_id;
        return DB::table('tbl_beds')->where('ward_id',$ward_id)->get();
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
            $admit = Tbl_admission::create([
                'admission_date' => $date,'account_id' => $account_id, 'patient_id' => $request->input('patient_id'), 'admission_status_id' => $request->input('admission_status_id'),
                'facility_id' => $request->input('facility_id'), 'user_id' => $request->input('user_id'),
            ]);
            $adm_id = $admit->id;
            $admission = Tbl_instruction::create(['instructions' => $request->input('instructions'),
                'facility_id' => $request->input('facility_id'), 'user_id' => $request->input('user_id'),
                'admission_id' => $adm_id, 'patient_id' => $request->input('patient_id'), 'ward_id' => $request->input('ward_id'),
            ]);
            $newData=$admit;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

            return $admission;
        }
    }

    public function assignIcuBed(Request $request)
    {
        $date = date('Y-m-d H:m:s');
        $patient_id = $request->patient_id;
        $facility_id = $request->facility_id;
        $user_id = $request->user_id;
        $ward_id = $request->ward_id;
        $bed_id = $request->bed_id;
        $admission_status_id = 2;
        if(patientRegistration::duplicate('tbl_admissions',array('patient_id','admission_status_id','facility_id'),
                array($patient_id,$admission_status_id,$facility_id,''))==true){

            return response()->json([
                'msg' => 'Ooops!..Patient already assigned bed...',
                'status' => '0'
            ]);
        }
        else{
            $admit = Tbl_admission::create([
                'admission_date' => $date, 'patient_id' => $patient_id,
                'admission_status_id' =>$admission_status_id,
                'facility_id' => $facility_id, 'user_id' => $user_id,
            ]);
            $adm_id = $admit->id;
            $admission = Tbl_instruction::create(['prescriptions' =>'',
                'facility_id' => $facility_id , 'user_id' => $user_id,
                'admission_id' => $adm_id, 'patient_id' => $patient_id,'ward_id' => $ward_id,'bed_id' => $bed_id,
            ]);

            $newData=$admit;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);
            return response()->json([
                'msg' => 'Great!..Patient assigned bed...',
                'status' => '1'
            ]);
        }
    }
    public function getStores(Request $request)
    {
        $facility_id = $request['facility_id'];
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

    public function getPatientCategories()
    {
        $sql = "select * from tbl_pay_cat_sub_categories";
        $cat = DB::select(DB::raw($sql));
        return $cat;
    }
    public function filterByCategory(Request $request)
    {
        $bill_id = $request['bill_id'];
        $sql = "select * from opd_patients where  bill_id = '".$bill_id."' GROUP BY patient_id order by account_id ASC";
        $byCat = DB::select(DB::raw($sql));
        return $byCat;
    }

    public function getMedicalSuppliesList(Request $request)
    {
        $id = $request['facility_id'];
        $sql = "select * from vw_shop_items where  item_category ='Medical Supplies' AND facility_id = '".$id."' ";
        $medSup = DB::select(DB::raw($sql));
        return $medSup;
    }
    public function getProceduresList(Request $request)
    {
        $id = $request['facility_id'];
        $sql = "select * from vw_shop_items where (item_category ='PROCEDURE' OR item_category ='SPECIALISED PROCEDURES' OR item_category ='MAJOR PROCEDURES' OR item_category='MINOR PROCEDURES' ) AND facility_id = '".$id."' ";
        $medSup = DB::select(DB::raw($sql));
        return $medSup;
    }

    public function dischargePatient(Request $request)
    {
        
        $patient_id = $request['patient_id'];
        $facility_id = $request['facility_id'];
        $account_id = $request['account_id'];
        $user_id = $request['user_id'];
        $admission_status_id=3;
        $admission_status=4;
        if($request->input("discharge_summary")==""){
			return response()->json([
                'data' => 'Discharge summary required',
                'status' => '0'
            ]);
        }
        if(patientRegistration::duplicate('tbl_admissions',array('patient_id','admission_status_id','account_id'),
                array($patient_id,$admission_status_id,$account_id,''))==true){

            return response()->json([
                'data' => 'Ooops!..Patient already discharged but still in pending discharge mode..Please contact nurse in-charge.',
                'status' => '0'
            ]);
        }
        else if(patientRegistration::duplicate('tbl_admissions',array('patient_id','admission_status_id','account_id'),
                array($patient_id,$admission_status,$account_id,''))==true){
            return response()->json([
                'data' => 'Patient already discharged.',
                'status' => '0'
            ]);
        }
        else {
            $oldData=Tbl_admission::where('patient_id',$patient_id)->where('account_id',$account_id)->where('facility_id',$facility_id)->where('admission_status_id',2)->get();

            $admit=Tbl_admission::where('patient_id',$patient_id)->where('account_id',$account_id)->where('facility_id',$facility_id)->where('admission_status_id',2)
                ->update([
                    'admission_status_id'=>3,

                    'discharge_summary'=>$request->input("discharge_summary"),
                    'discharged_by'=>$user_id,
                ]);
            $newData=Tbl_admission::where('patient_id',$patient_id)->where('account_id',$account_id)->where('facility_id',$facility_id)->where('admission_status_id',2)->get();



            return response()->json([
                'data' => 'Patient successfully discharged.',
                'status' => '1'
            ]);
        }
    }

    public function getTests(Request $request)
    {   $sub = $request->input('sub_dept_id');
        $facility_id = $request->input('facility_id');
        $patient_category_id = $request->input('patient_category_id');
        $sql=" SELECT * FROM `vw_investigations_tests` WHERE sub_dept_id='".$sub."'  AND patient_category_id ='".$patient_category_id."' AND facility_id ='".$facility_id."' GROUP BY item_id";
        $investigation = DB::select(DB::raw($sql));
        return $investigation;
    }

// modified function for getting lab single test only to the doctor
    public function getSingleTests(Request $request)
    {   $sub = $request->input('sub_dept_id');
        $facility_id = $request->input('facility_id');
        $patient_category_id = $request->input('patient_category_id');
        $sql=" SELECT * FROM `vw_labtests_to_doctors` WHERE sub_dept_id='".$sub."' AND patient_category_id ='".$patient_category_id."' AND facility_id = '".$facility_id."' GROUP BY item_id";
        $investigation = DB::select(DB::raw($sql));
        return $investigation;
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

    public function getPanelComponents(Request $request)
    {
        if(count($request->all())>0){
            $sub = $request->input('sub_department_id');
            $facility_id = $request->input('facility_id');
            $item_id = $request->input('item_id');
            $sql=" select * from `vw_labtests` where sub_department_id='".$sub." ' AND item_id ='".$item_id."' AND facility_id = '".$facility_id."' ";
            $panelComp = DB::select(DB::raw($sql));
            return $panelComp;
        }
    }

    public function getBeds(Request $request)
    {   $sub = $request->input('ward_id');
        $facility_id = $request->input('facility_id');
        $sql="select * from `tbl_beds` where ward_id='".$sub." ' AND facility_id = '".$facility_id."' ";
        $investigation = DB::select(DB::raw($sql));
        return $investigation;
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
        $postData2=$data2;
        $id = $data2->id;
        if(count($complaints)>0)
        {
            foreach ($complaints as $d){
                $postData2= Tbl_complaint::create([
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
            $newData=$postData2;
            $patient_id=$patient_id;
            $trackable_id=$newData->id;
            $user_id=$user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

            return $postData2;
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
            $newData=$data2;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

            return $data2;
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
                $postData2 = Tbl_review_of_system::create([
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
            $newData=$data2;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

            return $postData2;
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
                $newData=$postData;
                $patient_id=$newData->patient_id;
                $trackable_id=$newData->id;
                $user_id=$newData->user_id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

            }
        }
//
        if ($data) {
            $rec = new Tbl_past_medical_record($data);
            $rec['past_medical_history_id'] = $id;
            $rec->save();
            $newData=$rec;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

        }
        return 'Past medical history data saved';
    }

    public function birthHistory(Request $request)
    {
        $view = Tbl_birth_history::create($request->all());
        $id = $view->id;
        $data = Tbl_child_birth_history::create([
            'natal'=>$request->input('natal'),
            'post_natal'=>$request->input('post_natal'),
            'antenatal'=>$request->input('antenatal'),
            'nutrition'=>$request->input('nutrition'),
            'growth'=>$request->input('growth'),
            'development'=>$request->input('development'),
            'birth_history_id'=>$id,
        ]);
        $newData= $data;
        $patient_id=$newData->patient_id;
        $trackable_id=$newData->id;
        $user_id=$newData->user_id;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

        return $data;
    }
    public function familyHistory(Request $request)
    {
        $view = Tbl_family_history::create($request->all());
        $id = $view->id;
        $data = Tbl_family_social_history::create([
            'chronic_illness'=>$request->input('chronic_illness'),
            'substance_abuse'=>$request->input('substance_abuse'),
            'family_history_id'=>$id,
        ]);
        $newData= $data;
        $patient_id=$newData->patient_id;
        $trackable_id=$newData->id;
        $user_id=$newData->user_id;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

        return $data;
    }
    public function postObs(Request $request)
    {
        $view = Tbl_obs_gyn::create($request->all());
        $id = $view->id;
        $data = Tbl_obs_gyn_record::create([
            'menarche'=>$request->input('menarche'),
            'menopause'=>$request->input('menopause'),
            'menstrual_cycles'=>$request->input('menstrual_cycles'),
            'pad_changes'=>$request->input('pad_changes'),
            'recurrent_menstruation'=>$request->input('recurrent_menstruation'),
            'contraceptives'=>$request->input('contraceptives'),
            'pregnancy'=>$request->input('pregnancy'),
            'lnmp'=>$request->input('lnmp'),
            'gravidity'=>$request->input('gravidity'),
            'parity'=>$request->input('parity'),
            'living_children'=>$request->input('living_children'),
            'obs_gyn_id'=>$id,
        ]);
        $newData= $data;
        $patient_id=$newData->patient_id;
        $trackable_id=$newData->id;
        $user_id=$newData->user_id;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

        return $data;
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
        $newData= $data2;
        $patient_id=$newData->patient_id;
        $trackable_id=$newData->id;
        $user_id=$newData->user_id;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

        $id = $data2->id;
        foreach ($request->all() as $d){
            $postData = Tbl_physical_examination_record::create(['observation'=>$d['observation'],'category'=>$d['category'],'system'=>$d['system'],'physical_examination_id'=>$id,
            ]);

        }
        return $postData;
    }

    public function postLocalPhysical(Request $request)
    {
        if(count($request->all())>0){
            $data2 = Tbl_physical_examination ::create($request->all());
            $id = $data2->id;
            $postData = Tbl_physical_examination_record::create(['local_examination'=>$request->input('local_examination'),'physical_examination_id'=>$id,
            ]);
            $newData= $data2;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

            return $postData;
        }
    }
    public function postGenPhysical(Request $request)
    {
        if(count($request->all())>0){
            $data2 = Tbl_physical_examination ::create($request->all());
            $id = $data2->id;
            $postData = Tbl_physical_examination_record::create(['gen_examination'=>$request->input('gen_examination'),'physical_examination_id'=>$id,
            ]);
            $newData= $data2;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

            return $postData;
        }
    }
    public function postSummaryPhysical(Request $request)
    {
        if(count($request->all())>0){
            $data2 = Tbl_physical_examination ::create($request->all());
            $id = $data2->id;
            $postData = Tbl_physical_examination_record::create(['summary_examination'=>$request->input('summary_examination'),'physical_examination_id'=>$id,
            ]);
            $newData= $data2;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

            return $postData;
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
            $newData= $data2;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

            return response()->json([
                'msg' => 'Examination data successfully saved...',
                'status' => '1'
            ]);
        }
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
		$admission = Tbl_admission::where("account_id" , $account_number_id)->where("admission_status_id", 2)->orderBy("id", "desc")->get();
        $investgation = Tbl_request::create(["requesting_department_id"=>$requesting_department_id,"doctor_id"=>$user_id,"patient_id"=>$patient_id,"visit_date_id"=>$account_number_id,"eraser"=>1,"admission_id"=>(count($admission) > 0 ? $admission[0]->id : null)]);
        $newData= $investgation ;
        $patient_id=$newData->patient_id;
        $trackable_id=$newData->id;
        $user_id=$user_id;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);
        $id = $investgation->id;
        foreach ($clinicalData as $d){
            $postData = Tbl_order::create(['priority'=>$priority,'clinical_note'=>$clinical_note,'test_id'=>$d['item_id'],'order_id'=>$id,"eraser"=>1,"visit_date_id"=>$account_number_id]);
        }
        $billing = Tbl_encounter_invoice::create(["account_number_id"=>$account_number_id, "user_id"=>$user_id,"facility_id"=>$facility_id,]);
        $invoice_id = $billing->id;

        foreach ($clinicalData as $b){
            $billsData = Tbl_invoice_line::create(["invoice_id"=>$invoice_id,"item_type_id"=>$b['item_type_id'],
                "quantity"=>number_format($b['quantity'], 2, '.', ''),"item_price_id"=>$b['item_price_id'],"user_id"=>$b['user_id'],"patient_id"=>$b['patient_id'],
                "status_id"=>$b['status_id'],"facility_id"=>$b['facility_id'],"discount_by"=>$b['user_id'],"payment_filter"=>$b['payment_filter'], ]);
            $newData= $billsData ;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

        }
        return $billsData;
    }

    public function investigationList(Request $request)
    {
        $pt = $request->input('facility_id');
        $limit = 20;
        $sql = "select * from vw_investigation_results where facility_id = '".$pt."'  AND (timestampdiff(hour,created_at,CURRENT_TIMESTAMP)<=12) GROUP BY patient_id limit ".$limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function getAllInvPatients(Request $request)
    {
        $pt = $request->input('facility_id');
        $sql = "select * from vw_investigation_results where facility_id = '".$pt."'  AND (timestampdiff(hour,created_at,CURRENT_TIMESTAMP)<=120) GROUP BY patient_id ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }

    public function getInvestigationResults(Request $request)
    {
        $dept = $request->input('dept_id');
        $pt = $request->input('patient_id');
        $date = $request->input('account_id');
        $sql = "select * from vw_investigation_results where   dept_id = '".$dept."' AND account_id = '".$date."'";
        $rs = DB::select(DB::raw($sql));
        return $rs;
    }

	
    public function getPanelComponentResults(Request $request)
    {
        $item_id = $request->input('item_id');
        $order_id = $request->input('order_id');
        $sql="SELECT * FROM tbl_panel_components_results WHERE item_id='".$item_id."' AND order_id='".$order_id."' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function prevInvestigationResults(Request $request){
        $dept = $request->input('dept_id');
        $pt = $request->input('patient_id');
        $date = $request->input('visit_date_id');
        $sql = "select * from vw_investigation_results where patient_id = '".$pt."' AND dept_id = '".$dept."' AND account_id = '".$date."' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function getResults(Request $request)
    {

        $dept = $request->input('dept_id');
        $pt = $request->input('patient_id');
        $limit = 5;
        $sql = "select date_attended,account_id,patient_id,dept_id from vw_investigation_results where patient_id = '".$pt."' AND dept_id = '".$dept."'  GROUP BY account_id ORDER BY date_attended DESC  limit ".$limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
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
            $newData= $diag ;
            $patient_id= $patient_id;
            $trackable_id=$newData->id;
            $user_id=$user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);
        }
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
        $user_id = $request->input('user_id');
		
		if(count(DB::select("SELECT Id from tbl_user_store_configurations WHERE user_id = $user_id AND tbl_user_store_configurations.status = 1")) > 0){
			$sql = "select ifnull(sum(quantity_received),0) as balance from tbl_dispensers where item_id ='".$item_id."' AND control ='l' AND dispenser_id IN (select tbl_pos_dispensings.store_id from tbl_pos_dispensings join tbl_user_store_configurations ON  tbl_pos_dispensings.store_id = tbl_user_store_configurations.store_id and tbl_pos_dispensings.status=1 and tbl_user_store_configurations.status = 1 and tbl_user_store_configurations.user_id = $user_id)";
		}else{
			$sql = "select ifnull(sum(quantity_received),0) as balance from tbl_dispensers where item_id ='".$item_id."' AND control ='l' AND dispenser_id IN (select store_id from tbl_pos_dispensings where status=1)";
		}
		
        $balances = DB::select(DB::raw($sql));
        return $balances;
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
    public function getMedicalSupplies(Request $request)
    {
        $search = $request->input('search');
        $id = $request->input('facility_id');
        $category_id = $request->input('patient_category_id');
        $sql = "select * from vw_shop_items where item_name like '%".$search."%' AND item_category ='Medical Supplies' AND patient_category_id ='".$category_id."' AND facility_id = '".$id."' LIMIT 10 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function getProcedures(Request $request)
    {
        $id = $request->input('facility_id');
        $category_id = $request->input('patient_category_id');
        $limit = 30;
        $sql = "select * from vw_shop_items where (item_category ='PROCEDURE' OR item_category ='SPECIALISED PROCEDURES' OR item_category ='MAJOR PROCEDURES' OR item_category='MINOR PROCEDURES' ) AND patient_category_id ='".$category_id."' AND facility_id ='".$id."' limit ".$limit;
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
            $postData= Tbl_prescription::create($request->all());
            return response()->json([
                'msg'=>'Conservative management data successfully saved...',
                'status'=>1
            ]);
            $newData=$postData;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->prescriber_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

        }
    }

    public function postMedicines(Request $request)
    {
        $date = date('Y-m-d');
        foreach ($request->all() as $b){
            $medData2 = Tbl_prescription::create(["item_id"=>$b['item_id'],"patient_id"=>$b['patient_id'],"admission_id"=>$b['admission_id'],"visit_id"=>$b['visit_id'],
                "prescriber_id"=>$b['user_id'],"quantity"=>$b['quantity'],"frequency"=>$b['frequency'],"duration"=>$b['duration'],
                "dose"=>$b['dose'],"start_date"=>$date,"instruction"=>$b['instructions'],"out_of_stock"=>$b['out_of_stock']]);
            $newData=$medData2;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->prescriber_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

        }
        return $medData2;
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
        FROM tbl_requests t1 left JOIN tbl_orders t2 ON date(t1.created_at) = date(t2.created_at) and t1.id = t2.order_id left JOIN tbl_items t3 ON t3.id = t2.test_id 
        left JOIN tbl_results t4 ON t4.order_id = t1.id left JOIN users t5 ON t5.id = t1.doctor_id 
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
        $oldData= Tbl_prescription::where('patient_id',$patient_id)->where('visit_id',$visit_id)->where('item_id',$item_id)->get();
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

        $newData=Tbl_prescription::where('patient_id',$patient_id)->where('visit_id',$visit_id)->where('item_id',$item_id)->get();
        $patient_id=$newData[0]->patient_id;
        $trackable_id=$newData[0]->id;
        $user_id=$newData[0]->prescriber_id;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,$oldData);

        return response()->json([
            'msg' => 'Prescription updated',
            'status' => 1
        ]);
    }
    public function postMedicalSupplies(Request $request)
    {
        $date = date('Y-m-d');
        /*$dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $user_id =  $data[0]->user_id;
        $account_number_id =  $data[0]->account_number_id;
        $facility_id =  $data[0]->facility_id;
        $data = Tbl_encounter_invoice::create(["account_number_id"=>$account_number_id, "user_id"=>$user_id,"facility_id"=>$facility_id,]);
        $invoice_id=$data->id;
        foreach ($request->all() as $b){
            $medData = Tbl_invoice_line::create(["invoice_id"=>$invoice_id,"item_type_id"=>$b['item_type_id'],"payment_filter"=>$b['payment_filter'],
                "quantity"=>$b['quantity'],"item_price_id"=>$b['item_price_id'],"user_id"=>$b['user_id'],"patient_id"=>$b['patient_id'],
                "status_id"=>$b['status_id'],"facility_id"=>$b['facility_id'],"discount_by"=>$b['user_id'],"discount"=>0, ]);
        }*/

        foreach ($request->all() as $b){
            $medData2 = Tbl_prescription::create(["item_id"=>$b['item_id'],"patient_id"=>$b['patient_id'],"admission_id"=>$b['admission_id'],"visit_id"=>$b['visit_id'],
                "prescriber_id"=>$b['user_id'],"quantity"=>$b['quantity'],"start_date"=>$date,"out_of_stock"=>$b['out_of_stock']]);
            $newData=$medData2;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->prescriber_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

        }
        return $medData2;
    }
    public function getPrevMedicine(Request $request)
    {
        $id = $request->input('patient_id');
        $sql = "SELECT * FROM vw_previous_medications WHERE patient_id ='".$id."' ORDER BY start_date DESC LIMIT 30";
        $data = DB::select(DB::raw($sql));
        return $data;
    }
    public function stopMedication(Request $request)
    {


        Tbl_prescription::where("id",$request['prescription_id'])->update([
            "continuation_status"=>"STOPED",
            "stoped_by"=>$request['user_id']
        ]);
        return $this->getPrevMedicine($request);
    }
    public function getPrevMedications(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "SELECT * FROM vw_previous_medications WHERE patient_id ='".$id."' AND date_attended = '".$date."' LIMIT 30";
        $data = DB::select(DB::raw($sql));
        return $data;
    }
    public function getPastMedicine(Request $request)
    {
        $data = [];
        $id = $request->input('patient_id');
        $dt = $request->input('visit_date_id');
        $sql = "SELECT * FROM vw_previous_medications WHERE patient_id = '".$id."' AND visit_id = '".$dt."' AND conservatives IS NULL";
        $sql2 = "SELECT t1.*,t2.name,t3.prof_name FROM tbl_prescriptions t1
                 INNER JOIN users t2 ON t2.id = t1.prescriber_id INNER JOIN tbl_proffesionals t3 ON t3.id = t2.proffesionals_id WHERE patient_id = '".$id."' AND visit_id = '".$dt."'";
        $data[] = DB::select(DB::raw($sql));
        $data[] = DB::select(DB::raw($sql2));
        return $data;
    }

    public function getPrevProcedures(Request $request)
    {
        $id = $request->input('patient_id');
        $sql = "SELECT * FROM vw_previous_procedures WHERE patient_id = '".$id."' ORDER BY created_at DESC LIMIT 30  ";
        $data = DB::select(DB::raw($sql));
        return $data;
    }
    public function getPastProcedures(Request $request)
    {
        $id = $request->input('patient_id');
        $dt = $request->input('visit_date_id');
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
                $newData=$medData2;
                $patient_id=$newData->patient_id;
                $trackable_id=$newData->id;
                $user_id=$newData->prescriber_id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);


            }
            return $medData2;
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
            return $medData2;
        }
    }
    public function postPatientProcedures(Request $request)
    {   $dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $user_id =  $data[0]->user_id;
        $account_number_id =  $data[0]->account_number_id;
        $facility_id =  $data[0]->facility_id;
        $data = Tbl_encounter_invoice::create(["account_number_id"=>$account_number_id, "user_id"=>$user_id,"facility_id"=>$facility_id,]);
        $invoice_id=$data->id;
        foreach ($request->all() as $b){
            $Data = Tbl_invoice_line::create(["payment_filter"=>$b['payment_filter'],"invoice_id"=>$invoice_id,"item_type_id"=>$b['item_type_id'],
                "quantity"=>number_format($b['quantity'], 2, '.', ''),"item_price_id"=>$b['item_price_id'],"user_id"=>$b['user_id'],"patient_id"=>$b['patient_id'],
                "status_id"=>$b['status_id'],"facility_id"=>$b['facility_id'],"discount_by"=>$b['user_id'], ]);
            $newData=$Data;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);



        }

        foreach ($request->all() as $b){
            $Data2 = Tbl_patient_procedure::create(["item_id"=>$b['item_id'],"patient_id"=>$b['patient_id'],
                "user_id"=>$b['user_id'],"visit_date_id"=>$b['account_number_id'],"admission_id"=>$b['admission_id'],]);
            $newData=$Data2;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->user_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

        }
        return $Data2;
    }

    public function postNotes(Request $request)
    {
        return Tbl_continuation_note::create($request->all());
    }

    public function getNotes(Request $request)
    {   $patient = [];
        $id = $request->input('patient_id');
        $sql = "select * from vw_conti_notes where patient_id = '".$id."' AND notes_type = 1 ORDER BY created_at DESC ";
        $sql1 = "select * from vw_conti_notes where patient_id = '".$id."' AND notes_type = 2 ORDER BY created_at DESC ";
        $patient[] = DB::select(DB::raw($sql));
        $patient[] = DB::select(DB::raw($sql1));
        return $patient;
    }

    public function prevDiagnosis(Request $request)
    {   $id = $request->input('patient_id');
        $limit = 5;
        $sql = "select date_attended,patient_id from vw_prev_diagnosis where patient_id = '".$id."' GROUP BY date_attended limit ".$limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function prevFamilyHistory(Request $request)
    {   $id = $request->input('patient_id');
        $limit = 5;
        $sql = "select date_attended,patient_id from vw_family_history where patient_id = '".$id."' GROUP BY date_attended limit ".$limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function prevBirthHistory(Request $request)
    {   $id = $request->input('patient_id');
        $limit = 5;
        $sql = "select date_attended,patient_id from vw_birth_history where patient_id = '".$id."' GROUP BY date_attended limit ".$limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function prevHistoryExaminations(Request $request)
    {   $id = $request->input('patient_id');
        $limit = 5;
        $sql = "select date_attended,patient_id from vw_history_examinations where patient_id = '".$id."' GROUP BY date_attended limit ".$limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function prevObsGyn(Request $request)
    {   $id = $request->input('patient_id');
        $limit = 5;
        $sql = "select date_attended,patient_id from vw_obs_gyn where patient_id = '".$id."' GROUP BY date_attended limit ".$limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function prevRoS(Request $request)
    {   $id = $request->input('patient_id');
        $limit = 5;
        $sql = "select date_attended,patient_id from vw_review_of_systems where patient_id = ".$id." GROUP BY date_attended limit ".$limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function prevPhysicalExaminations(Request $request)
    {   $id = $request->input('patient_id');
        $limit = 5;
        $sql = "select date_attended,patient_id from vw_physical_examinations where patient_id = '".$id."' GROUP BY date_attended limit ".$limit;
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function getFacilities(Request $request)
    {   $id = $request->input('searchKey');
        $limit = 5;
        $sql = "select * from tbl_facilities where facility_name like '%".$id."%' limit ".$limit;
        $facility = DB::select(DB::raw($sql));
        return $facility;
    }


    public function postReferral(Request $request)
    {
$this->runViews1();

        $account_id=$request->input('account_id');
        $sender_id=$request->input('sender_id');
        $facility_id=$request->input('from_facility_id');
        $to_facility_id=$request->input('to_facility_id');
        $patient_id=$request->input('patient_id');
        $summary=$request->input('summary');


if($request->input('account_id')==""){
    return response()->json([
       "msg"=>"Account Number is Not defined with this patient",
      "status"=>401,
       "data"=>"",
    ]);
}

if($request->input('to_facility_id')==""){
    return response()->json([
       "msg"=>"Please Choose Destination Facility",
      "status"=>401,
       "data"=>"",
    ]);
}

$fromFacility=Tbl_facility::where("id",$facility_id)->get();
$toFacility=Tbl_facility::where("id",$to_facility_id)->get();
if($request->input('to_facility_id')==$request->input('from_facility_id')){
    return response()->json([
       "msg"=>"You can not Refer Client Form ".$toFacility[0]->facility_name."  To  ".$fromFacility[0]->facility_name,
      "status"=>401,
       "data"=>"",
    ]);
}
if($request->input('summary')==""){
    return response()->json([
       "msg"=>"Please fill Referral summary",
      "status"=>401,
       "data"=>"",
    ]);
}
     $admt=Tbl_admission::where("account_id",$account_id)->get();
     if(count($admt)>0){

   $admissionWard = Tbl_instruction::where('admission_id',$admt[0]->id)->select('ward_id')->get();
if(count( $admissionWard)>0){

    $checkDupl= Tbl_status_ward::where('admission_id',$admt[0]->id)
    ->where('ward_id',$admissionWard[0]->ward_id )
    ->where('admission_status_id',11 )
    ->get();

    if(count($checkDupl)==0){


     Tbl_status_ward::create([
      'visit_date_id'=>$account_id,
      'admission_status_id'=>11,
      'user_id'=>$sender_id,
      'admission_id'=>$admt[0]->id,
      'facility_id'=>$facility_id,
      'ward_id'=>$admissionWard[0]->ward_id,
     ]);
 }
 else{

    //return duplicate sms
     $res= response()->json([
       "msg"=>"Duplication detected",
      "status"=>401,
       "data"=>"",
    ]);
 }
 }

 }

//save referral
 $checkdup=Tbl_referral::where("visit_id",$account_id)->where('to_facility_id',$to_facility_id)->get();
 if(count($checkdup)==0){

$payl=Tbl_referral::create([
 'visit_id'=>$account_id,     
'referral_code'=>"OUTGOING",      
'referral_type'=>1,      
'status'=>1, 
'patient_id'=>$patient_id,                           
'summary'=>$summary,       
 'sender_id'=>$sender_id,                                
 'from_facility_id'=>$facility_id,     
 'to_facility_id'=>$to_facility_id,
"referral_date"=>$request->input("referral_date"),
"referral_time"=>$request->input("referral_time"),
  "diagnosis"=>$request->input("diagnosis"),
  "temperature"=>$request->input("temperature"),
  "heart_rate"=>$request->input("heart_rate"),
  "respiratory_rate"=>$request->input("respiratory_rate"),
  "bp"=>$request->input("bp"),
  "mental_status"=>$request->input("mental_status"),
  "alert"=>$request->input("alert"),
  "pertinent"=>$request->input("pertinent"),
  "history"=>$request->input("history"),
  "chronic_ediction"=>$request->input("chronic_ediction"),
  "allergy"=>$request->input("allergy"),
  "lab_result"=>$request->input("lab_result"),
  "radiology_result"=>$request->input("radiology_result"),
  "treatment"=>$request->input("treatment"),
  "contact_person"=>$request->input("contact_person"),
  "name"=>$request->input("name"),
  "gender"=>$request->input("gender"),
  "reg"=>$request->input("reg"),
  "age"=>$request->input("age"),
]);
$dataa=DB::select("SELECT r.*,f.facility_name,ft.description as facility_type from tbl_referrals r join tbl_facilities f on r.to_facility_id=f.id join tbl_facility_types ft on ft.id=f.facility_type_id where  r.id=$payl->id ");
 $res= response()->json([
       "msg"=>"Referral successfully granted",
      "status"=>200,
      "data"=>$dataa[0]
    ]);
}
else{
  //return duplicate sms
     $res= response()->json([
       "msg"=>"Duplication detected",
      "status"=>401,
      "data"=>"",
    ]);  
}
                             


 return  $res;
        // $intergratingKeys=Tbl_integrating_key::where('facility_id',$facility_id)->where('api_type',6)->get();


        // if(!isset($intergratingKeys[0])){

        // }
        // $base_urls=$intergratingKeys[0]->base_urls;
        // $private_keys=$intergratingKeys[0]->private_keys;
        // $public_keys=$intergratingKeys[0]->public_keys;
        // $refferal_systems[]=Tbl_referral::create($request->all());
        // $facilities=Tbl_facility::where('id',$facility_id)->get();
        //return $refferal_systems;
        $doctor=User::where('id',$request->sender_id)->get();

        $sql="SELECT t1.*,t2.id as visit_id FROM tbl_patients t1
		INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id WHERE t2.id='".$visit_id."' GROUP BY t2.id";
        $patient=DB::SELECT($sql);

       // $folioID=$refferal_systems[0]->id;
       // $foliolist_array=array();
/*
        $patient_infos=array();
        $diseases=array();
        $patientAllergies=array();
        $historyExminations=array();
        $patientPhysicalExaminations=array();
        $items_array =array();
        $patientInvestigationResults =array();
        //$entity_array =array();
        $entity_array["entities"]=array();


        $sqlw ="SELECT * FROM vw_prev_diagnosis WHERE visit_date_id = '".$visit_id."'  AND facility_id='".$facility_id."'";
        $diseases_diagnosis=DB::select(DB::raw($sqlw));

        $sqlh ="SELECT * FROM vw_history_examinations WHERE visit_date_id = '".$visit_id."'";
        $histories=DB::select(DB::raw($sqlh));

        $allerg ="SELECT * FROM vw_allergies WHERE visit_date_id = '".$visit_id."'";
        $alergies=DB::select(DB::raw($allerg));

        $physical_examination ="SELECT * FROM vw_physical_examinations WHERE visit_date_id = '".$visit_id."'";
        $physical_examinations=DB::select(DB::raw($physical_examination));


        $investigation_result ="SELECT * FROM vw_investigation_results WHERE account_id = '".$visit_id."' GROUP BY item_id";
        $investigation_results=DB::select(DB::raw($investigation_result));


        foreach($patient as $row) {
            $patient_infos["FolioID"]=$folioID;
            $patient_infos['patient_id']=$row->id;
            $patient_infos['visit_id']=$row->visit_id;
            $patient_infos['first_name']=$row->first_name;
            $patient_infos['middle_name']=$row->middle_name;
            $patient_infos['last_name']=$row->last_name;
            $patient_infos['gender']=$row->gender;
            $patient_infos['dob']=$row->dob;
            $patient_infos['mobile_number']=$row->mobile_number;
            $patient_infos['residence_id']=$row->residence_id;
            $patient_infos['medical_record_number']=$row->medical_record_number;

            $patient_infos['FolioDisaeses']=array();
            $patient_infos['FolioItems']=array();
            $patient_infos['patientHistory']=array();
            $patient_infos['patientAllergy']=array();
            $patient_infos['PhysicalExaminations']=array();
            $patient_infos['InvestigationResults']=array();

            foreach($diseases_diagnosis as $disease) {
                $diseases["FolioID"]=$folioID;
                $diseases['DiseaseCode']=$disease->DiseaseCode;
                $diseases['Status']=$disease->status;
                array_push($patient_infos['FolioDisaeses'],$diseases);
            }

            foreach($histories as $history) {
                $history_examinations["FolioID"]=$folioID;
                $history_examinations['date_attended']=$history->date_attended;
                $history_examinations['description']=$history->description;
                $history_examinations['duration']=$history->duration;
                $history_examinations['duration_unit']=$history->duration_unit;
                $history_examinations['other_complaints']=$history->duration_unit;
                $history_examinations['complainId']=$history->complainId;
                $history_examinations['historyExamID']=$history->historyExamID;
                $history_examinations['status']=$history->status;
                $history_examinations['hpi']=$history->hpi;
                array_push($patient_infos['patientHistory'],$history_examinations);
            }

            foreach($alergies as $alergy) {
                $patientAllergies["FolioID"]=$folioID;
                $patientAllergies['descriptions']=$alergy->descriptions;
                $patientAllergies['status']=$alergy->status;
                $patientAllergies['uuidbl_past_medical_histories']=$alergy->uuidbl_past_medical_histories;
                $patientAllergies['uuidbl_past_medical_records']=$alergy->uuidbl_past_medical_records;
                $patientAllergies['date_attended']=$alergy->date_attended;
                $patientAllergies['visit_date_id']=$alergy->visit_date_id;
                array_push($patient_infos['patientAllergy'],$patientAllergies);
            }

            foreach($investigation_results as $investigation_result) {
                $patientInvestigationResults["FolioID"]=$folioID;
                $patientInvestigationResults['item_id']=$investigation_result->item_id;
                $patientInvestigationResults['item_name']=$investigation_result->item_name;
                $patientInvestigationResults['description']=$investigation_result->description;
                $patientInvestigationResults['facility_id']=$investigation_result->	facility_id;
                $patientInvestigationResults['verify_user']=$investigation_result->verify_user;
                $patientInvestigationResults['doctor_requested_test']=$investigation_result->doctor_requested_test;
                $patientInvestigationResults['request_id']=$investigation_result->request_id;
                $patientInvestigationResults['clinical_note']=$investigation_result->clinical_note;
                $patientInvestigationResults['remarks']=$investigation_result->remarks;
                $patientInvestigationResults['visit_date_id']=$investigation_result->	visit_date_id;
                $patientInvestigationResults['resultsUuid']=$investigation_result->resultsUuid;
                $patientInvestigationResults['orderUuid']=$investigation_result->orderUuid;
                $patientInvestigationResults['sample_no']=$investigation_result->sample_no;

                array_push($patient_infos['InvestigationResults'],$patientInvestigationResults);
            }
            foreach($physical_examinations as $physical_examination) {
                $patientPhysicalExaminations["FolioID"]=$folioID;
                $patientPhysicalExaminations['observation']=$physical_examination->observation;
                $patientPhysicalExaminations['physical_examination_id']=$physical_examination->physical_examination_id;
                $patientPhysicalExaminations['uuidphysicalexams']=$physical_examination->uuidphysicalexams;
                $patientPhysicalExaminations['category']=$physical_examination->category;
                $patientPhysicalExaminations['gen_examination']=$physical_examination->gen_examination;
                $patientPhysicalExaminations['summary_examination']=$physical_examination->summary_examination;
                $patientPhysicalExaminations['local_examination']=$physical_examination->local_examination;
                $patientPhysicalExaminations['system']=$physical_examination->system;
                $patientPhysicalExaminations['date_attended']=$physical_examination->date_attended;
                $patientPhysicalExaminations['visit_date_id']=$physical_examination->visit_date_id;
                $patientPhysicalExaminations['facility_id']=$physical_examination->facility_id;
                array_push($patient_infos['PhysicalExaminations'],$patientPhysicalExaminations);
            }

            foreach($refferal_systems as $refferal) {
                $items_array["FolioID"]=$folioID;
                $items_array['summary']=$refferal->summary;
                $items_array['sender_id']=$refferal->sender_id;
                $items_array['from_facility_id']=$refferal->from_facility_id;
                $items_array['to_facility_id']=$refferal->to_facility_id;
                $items_array['request_doctor']=$doctor[0]->name;
                $items_array['doctor_mobile_number']=$doctor[0]->mobile_number;
                $items_array['doctor_gender']=$doctor[0]->gender;
                $items_array['proffesionals_id']=$doctor[0]->proffesionals_id;
                $items_array['pass_keys']=$doctor[0]->password;
                $items_array['user_key_word']=$doctor[0]->email;
                $items_array['facility_name']=$facilities[0]->facility_name;
                $items_array['facility_code']=$facilities[0]->facility_code;
                $items_array['council_id']=$facilities[0]->council_id;
                $items_array['region_id']=$facilities[0]->region_id;
                $items_array['email']=$facilities[0]->email;
                $items_array['address']=$facilities[0]->address;
                $items_array['facility_type_id']=$facilities[0]->facility_type_id;
                array_push($patient_infos['FolioItems'],$items_array);


            }
            array_push($foliolist_array,$patient_infos);


        }
        $entity_array["entities"]=$foliolist_array;
        //array_push($entity_array["entities"],$foliolist_array);
        $data_string=json_encode($entity_array,JSON_PRETTY_PRINT);

        //   return $data_string;


        //return $entity_array['entities'][0]['FolioItems'];
        //$data_string =$entity_array;

        return self::SubmitFolios($data_string,$base_urls,$public_keys,$private_keys);

        */

    }


    public static function SubmitFolios($data_string,$base_urls,$public_keys,$private_keys)
    {
        $request=$base_urls.'/api/send_folio';
        $url=$base_urls.'/api/send_folio';


        $request_method = 'POST';



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);

        curl_close($ch);
        return $response;


    }



    public function send_folio(Request $request){
        //$info = json_decod$request,true);
        //return $request->all();
        $refferal_id=$request['entities'][0]['FolioID'];
        $visit_id=$request['entities'][0]['visit_id'];
        $patient_id=$request['entities'][0]['patient_id'];
        $first_name=$request['entities'][0]['first_name'];
        $middle_name=$request['entities'][0]['middle_name'];
        $last_name=$request['entities'][0]['last_name'];
        $gender=$request['entities'][0]['gender'];
        $dob=$request['entities'][0]['dob'];
        $mobile_number=$request['entities'][0]['mobile_number'];
        $residence_id=$request['entities'][0]['residence_id'];
        $medical_record_number=$request['entities'][0]['medical_record_number'];
        $summary=$request['entities'][0]['FolioItems'][0]['summary'];
        $from_facility_id=$request['entities'][0]['FolioItems'][0]['from_facility_id'];
        $sender_id=$request['entities'][0]['FolioItems'][0]['sender_id'];
        $to_facility_id=$request['entities'][0]['FolioItems'][0]['to_facility_id'];
        $request_doctor=$request['entities'][0]['FolioItems'][0]['request_doctor'];
        $pass_keys=$request['entities'][0]['FolioItems'][0]['pass_keys'];
        $user_key_word=$request['entities'][0]['FolioItems'][0]['user_key_word'];
        $proffesionals_id=$request['entities'][0]['FolioItems'][0]['proffesionals_id'];
        $doctor_gender=$request['entities'][0]['FolioItems'][0]['doctor_gender'];
        $council_id=$request['entities'][0]['FolioItems'][0]['council_id'];
        $region_id=$request['entities'][0]['FolioItems'][0]['region_id'];
        $facility_code=$request['entities'][0]['FolioItems'][0]['facility_code'];
        $facility_name=$request['entities'][0]['FolioItems'][0]['facility_name'];
        $facility_type_id=$request['entities'][0]['FolioItems'][0]['facility_type_id'];
        $address=$request['entities'][0]['FolioItems'][0]['address'];
        $email=$request['entities'][0]['FolioItems'][0]['email'];
        $doctor_mobile_number=$request['entities'][0]['FolioItems'][0]['doctor_mobile_number'];
        if(Tbl_facility::where('id',$from_facility_id)->get()->count() == 0){
            $sql="INSERT INTO tbl_facilities SET 
		      id='".$from_facility_id."',
		      facility_code='".$facility_code."',    
		      facility_name='".$facility_name."',    
		      facility_type_id='".$facility_type_id."',    
		      address='".$address."',    
		      mobile_number='".$mobile_number."',    
		      email='".$email."',    
			  created_at=CURRENT_TIMESTAMP,    
		      updated_at=CURRENT_TIMESTAMP,   
		      region_id='".$region_id."',    
		      council_id='".$council_id."'";
            DB::statement($sql);
        }
        if(User::where('id',$sender_id)->get()->count()==0){
            $sql="INSERT INTO users SET 
		      id='".$sender_id."',
		      name='".$request_doctor."',    
		      email='".$user_key_word."',    
		      password='".$pass_keys."',    
		      gender='".$doctor_gender."',    
		      proffesionals_id='".$proffesionals_id."',    
		      mobile_number='".$doctor_mobile_number."', 
              created_at=CURRENT_TIMESTAMP,    
		      updated_at=CURRENT_TIMESTAMP,   			  
		      facility_id='".$from_facility_id."'";
            DB::statement($sql);
        }
        if(Tbl_patient::where('medical_record_number',$medical_record_number)->get()->count()==0){
            $sql="INSERT INTO tbl_patients SET 
		      id='".$patient_id."',
		      first_name='".$first_name."',    
		      middle_name='".$middle_name."',    
		      last_name='".$middle_name."',    
		      gender='".$gender."',    
		      facility_id='".$from_facility_id."',    
		      user_id='".$sender_id."',    
		      medical_record_number='".$medical_record_number."',    
		      mobile_number='".$mobile_number."',    
		      created_at=CURRENT_TIMESTAMP,    
		      updated_at=CURRENT_TIMESTAMP,    
		       dob='".$dob."'";
            DB::statement($sql);
        }

        if(Tbl_accounts_number::where('id',$visit_id)->get()->count()==0){
            $account_number=patientRegistration::patientAccountNumber($to_facility_id,$patient_id,$sender_id);
            //return $to_facility_id;

            $sql="INSERT INTO tbl_accounts_numbers SET 
		      patient_id='".$patient_id."',
		      id='".$visit_id."', 	     
		      facility_id='".$to_facility_id."',    
		      user_id='".$sender_id."',    
		      tallied=0,    
		      account_number='".$account_number."',    
		      created_at=CURRENT_TIMESTAMP,    
		      updated_at=CURRENT_TIMESTAMP
			  ";
            DB::statement($sql);
        }
        if(Tbl_referral::where('visit_id',$visit_id)->get()->count() ==0 ){
            $sql="INSERT INTO  tbl_referrals SET id='".$refferal_id."',
                              visit_id='".$visit_id."',		
                              referral_type=1,		
                              status=1,	
                          patient_id='".$patient_id."',							  
                              summary='".$summary."',		
                              sender_id='".$sender_id."',	
                              created_at=CURRENT_TIMESTAMP,		                      updated_at=CURRENT_TIMESTAMP,   							  
                              from_facility_id='".$from_facility_id."',		
                              to_facility_id='".$to_facility_id."'
		
		    ";
            DB::statement($sql);

        }
        if(isset($request['entities'][0]['InvestigationResults'][0])){
            $visit_id=$request['entities'][0]['InvestigationResults'][0]['visit_date_id'];
            $request_id=$request['entities'][0]['InvestigationResults'][0]['request_id'];

        }else{
            $visit_id=null;
            $request_id=null;

        }
        $sql="INSERT IGNORE INTO  tbl_requests SET id='".$request_id."',
                          visit_date_id='".$visit_id."',                     
                          patient_id='".$patient_id."',							  
                          requesting_department_id=1,		
                          doctor_id='".$sender_id."',                         created_at=CURRENT_TIMESTAMP,		                      updated_at=CURRENT_TIMESTAMP";
        DB::statement($sql);

        $investgations=$request['entities'][0]['InvestigationResults'];
        foreach($investgations AS $investgation ){
            $item_id=$investgation['item_id'];
            $description=$investgation['description'];
            $remarks=$investgation['remarks'];
            $request_id=$investgation['request_id'];
            $clinical_note=$investgation['clinical_note'];
            $verify_user=$investgation['verify_user'];
            $orderUuid=$investgation['orderUuid'];
            $sample_no=$investgation['sample_no'];
            $resultsUuid=$investgation['resultsUuid'];



            $sql_1="INSERT IGNORE INTO  tbl_orders SET 
			              id='".$orderUuid."',			
			              order_id='".$request_id."',
                          clinical_note='".$clinical_note."',                     
                          test_id='".$item_id."',							  
                          sample_no='".$sample_no."',                         created_at=CURRENT_TIMESTAMP,		                      updated_at=CURRENT_TIMESTAMP";
            DB::statement($sql_1);

            $sql_2="INSERT IGNORE INTO  tbl_results SET 
			              id='".$resultsUuid."',
			              order_id='".$request_id."',
                          item_id='".$item_id."',							  
                          description='".$description."',		
                          confirmation_status=1,		
                         verify_user='".$verify_user."',                         created_at=CURRENT_TIMESTAMP,		                      updated_at=CURRENT_TIMESTAMP";
            DB::statement($sql_2);







        }


        //HISTORY EXAMS

        //physical examinations
        $examinations=$request['entities'][0]['PhysicalExaminations'];$patientHistory=$request['entities'][0]['patientHistory'];
        if(isset($request['entities'][0]['PhysicalExaminations'][0])){
            $physical_examination_id=$request['entities'][0]['PhysicalExaminations'][0]['physical_examination_id'];
            $visit_date_id=$request['entities'][0]['PhysicalExaminations'][0]['visit_date_id'];
            $facility_id=$request['entities'][0]['PhysicalExaminations'][0]['facility_id'];

            $historyExamID=$request['entities'][0]['patientHistory'][0]['historyExamID'];


        }else{
            $physical_examination_id=null;
            $visit_date_id=null;
            $facility_id=null;
            $historyExamID=null;

        }

        $sql_3="INSERT IGNORE INTO  tbl_physical_examinations SET 
			              id='".$physical_examination_id."',
			              patient_id='".$patient_id."',
                          visit_date_id='".$visit_date_id."',                     
                          user_id='".$sender_id."',							  
                          facility_id='".$facility_id."',		
                      created_at=CURRENT_TIMESTAMP,		                      updated_at=CURRENT_TIMESTAMP";
        DB::statement($sql_3);

        $sql_hist="INSERT IGNORE INTO  tbl_history_examinations SET 
			              id='".$historyExamID."',
			              patient_id='".$patient_id."',
                          visit_date_id='".$visit_date_id."',                     
                          user_id='".$sender_id."',							  
                          facility_id='".$facility_id."',		
                      created_at=CURRENT_TIMESTAMP,		                      updated_at=CURRENT_TIMESTAMP";
        DB::statement($sql_hist);

        foreach($examinations AS $examination ){
            $gen_examination=$examination['gen_examination'];
            $system=$examination['system'];
            $uuidphysicalexams=$examination['uuidphysicalexams'];
            $category=$examination['category'];
            $visit_date_id=$examination['visit_date_id'];
            $physical_examination_id=$examination['physical_examination_id'];
            $local_examination=$examination['local_examination'];
            $summary_examination=$examination['summary_examination'];
            $observation=$examination['observation'];

            $sql_4="INSERT IGNORE INTO  tbl_physical_examination_records SET 
			              id='".$uuidphysicalexams."',
			              observation='".$observation."',
                          category='".$category."',                     
                          system='".$system."',							  
                          gen_examination='".$gen_examination."',		
                          summary_examination='".$summary_examination."',		
                          local_examination='".$local_examination."',		
                      created_at=CURRENT_TIMESTAMP,		                      updated_at=CURRENT_TIMESTAMP";
            DB::statement($sql_4);



        }

        $patientAllergy=$request['entities'][0]['patientAllergy'];

        if(!isset($request['entities'][0]['patientAllergy'][0])){
            $past_medical_history_id=null;
            $physical_examination_id=null;
        }else{
            $past_medical_history_id=$request['entities'][0]['patientAllergy'][0]['uuidbl_past_medical_histories'];
            $physical_examination_id=$request['entities'][0]['PhysicalExaminations'][0]['physical_examination_id'];
        }

        $sql_5="INSERT IGNORE INTO  tbl_past_medical_histories SET 
			              id='".$past_medical_history_id."',
			              patient_id='".$patient_id."',
                          visit_date_id='".$visit_date_id."',                     
                          user_id='".$sender_id."',                     
                          facility_id='".$facility_id."',                     
                         created_at=CURRENT_TIMESTAMP,		                   updated_at=CURRENT_TIMESTAMP";
        DB::statement($sql_5);

        foreach($patientAllergy AS $patientAllerg ){
            $descriptions=$patientAllerg['descriptions'];
            $status=$patientAllerg['status'];
            $past_medical_history_id=$patientAllerg['uuidbl_past_medical_histories'];
            $past_medical_records_id=$patientAllerg['uuidbl_past_medical_records'];

            $sql_6="INSERT IGNORE INTO  tbl_past_medical_records SET 
			              id='".$past_medical_records_id."',
			              descriptions='".$descriptions."',
                          status='".$status."',                     
              past_medical_history_id='".$past_medical_history_id."',	                         created_at=CURRENT_TIMESTAMP,		                   updated_at=CURRENT_TIMESTAMP";
            DB::statement($sql_6);



        }

        foreach($patientHistory AS $patientHist ){
            $description=$patientHist['description'];
            $duration=$patientHist['duration'];
            $duration_unit=$patientHist['duration_unit'];
            $other_complaints=$patientHist['other_complaints'];
            $complainId=$patientHist['complainId'];
            $historyExamID=$patientHist['historyExamID'];
            $hpi=$patientHist['hpi'];

            $sql_7="INSERT IGNORE INTO  tbl_complaints SET 
			      id='".$complainId."',
			      description='".$description."',
			      duration='".$duration."',
			      duration_unit='".$duration_unit."',
                  other_complaints='".$other_complaints."',                     
                  hpi='".$hpi."',                     
                  history_exam_id='".$historyExamID."',	                         created_at=CURRENT_TIMESTAMP,		                   updated_at=CURRENT_TIMESTAMP";
            DB::statement($sql_7);



        }

        echo $request_doctor.', your request was successfully processed';


    }

    public function incomingReferrals(Request $request)
    {

        $id = $request->input('facility_id');
        $intergratingKeys=Tbl_integrating_key::where('facility_id',$id)->where('api_type',7)->get();
        $base_urls=$intergratingKeys[0]->base_urls;
        $private_key=$intergratingKeys[0]->private_keys;
        $public_key=$intergratingKeys[0]->public_keys;
        $data_string=$id;
        $request=$base_urls.'/api/getExternalRequests/'.$id;

        //return $result;
        //  return $request;
        $ch = curl_init($request);
        $request_method = 'GET';
        $nonce=uniqid("",true);
        // calculate the hash
        $timestamp=strval(time());
        $data_hash='';
        $signature_raw_data=$public_key.$request_method.$timestamp.$nonce.$data_hash;
        $hash = hash_hmac ('sha256', $signature_raw_data, $private_key,$raw=true);
        $signature = base64_encode($hash);
        $amx=$public_key.':'.$signature.':'.$nonce.':'.$timestamp;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: amx '.$amx));
        $result = curl_exec($ch);
        $StatusCode=  curl_getinfo($ch,CURLINFO_HTTP_CODE);

        if($StatusCode == 200){
            $array_data = json_decode($result,true);
            $array_data['StatusCode'] = $StatusCode;
            $result = json_encode($array_data);
        }
        else{
            $array_data = array();
            $array_data['StatusCode'] = $StatusCode;
            $array_data['Message'] = $result;
            $result = json_encode($array_data);
        }

        curl_close($ch);

        //$result = json_decode($result);

        return $result;



    }


    public function getAssociatedDetails(Request $request)
    {

        $id = $request->input('facility_id');
        $visit_id = $request->input('visit_id');
        $intergratingKeys=Tbl_integrating_key::where('facility_id',$id)->where('api_type',7)->get();
        $base_urls=$intergratingKeys[0]->base_urls;
        $private_key=$intergratingKeys[0]->private_keys;
        $public_key=$intergratingKeys[0]->public_keys;
        $data_string=$id;
        $request=$base_urls.'/api/getExternalRequestsDetails/'.$visit_id;

        //return $result;
        //  return $request;
        $ch = curl_init($request);
        $request_method = 'GET';
        $nonce=uniqid("",true);
        // calculate the hash
        $timestamp=strval(time());
        $data_hash='';
        $signature_raw_data=$public_key.$request_method.$timestamp.$nonce.$data_hash;
        $hash = hash_hmac ('sha256', $signature_raw_data, $private_key,$raw=true);
        $signature = base64_encode($hash);
        $amx=$public_key.':'.$signature.':'.$nonce.':'.$timestamp;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: amx '.$amx));
        $result = curl_exec($ch);
        $StatusCode=  curl_getinfo($ch,CURLINFO_HTTP_CODE);

        if($StatusCode == 200){
            $array_data = json_decode($result,true);
            $array_data['StatusCode'] = $StatusCode;
            $result = json_encode($array_data);
        }
        else{
            $array_data = array();
            $array_data['StatusCode'] = $StatusCode;
            $array_data['Message'] = $result;
            $result = json_encode($array_data);
        }

        curl_close($ch);

        //$result = json_decode($result);

        return $result;



    }


    public function outgoingReferrals(Request $request){

        $id = $request->input('facility_id');
        $intergratingKeys=Tbl_integrating_key::where('facility_id',$id)->where('api_type',7)->get();
        $base_urls=$intergratingKeys[0]->base_urls;
        $private_key=$intergratingKeys[0]->private_keys;
        $public_key=$intergratingKeys[0]->public_keys;
        $data_string=$id;
        $request=$base_urls.'/api/getTransferOut/'.$id;

        //return $result;
        //  return $request;
        $ch = curl_init($request);
        $request_method = 'GET';
        $nonce=uniqid("",true);
        // calculate the hash
        $timestamp=strval(time());
        $data_hash='';
        $signature_raw_data=$public_key.$request_method.$timestamp.$nonce.$data_hash;
        $hash = hash_hmac ('sha256', $signature_raw_data, $private_key,$raw=true);
        $signature = base64_encode($hash);
        $amx=$public_key.':'.$signature.':'.$nonce.':'.$timestamp;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: amx '.$amx));
        $result = curl_exec($ch);
        $StatusCode=  curl_getinfo($ch,CURLINFO_HTTP_CODE);

        if($StatusCode == 200){
            $result="NO ANY OUTGOING REFFERAL FROM THIS FACILITY";
            $array_data = json_decode($result,true);
            $array_data['StatusCode'] = $StatusCode;
            $array_data['Message'] = $result;
            if(count($array_data['Message'])==1){
                $array_data['ResponseStatus'] = 101;
            }
            $result = json_encode($array_data);
        }
        else{
            $array_data = array();
            $array_data['StatusCode'] = $StatusCode;
            $array_data['Message'] = $result;
            $result = json_encode($array_data);
        }

        curl_close($ch);

        //$result = json_decode($result);

        return $result;



    }

    public function getExternalRequests($facility_id){
        //return $facility_id;
        $refferals=[];



        $sql = "select * from vw_referrals where facility_id = '".$facility_id."' AND referral_type =1 GROUP BY visit_id";
        $refferals[] = DB::select(DB::raw($sql));


        $sql_1 = "select * from vw_history_examinations where to_facility_id = '".$facility_id."' ";
        $refferals[] = DB::select(DB::raw($sql_1));

        $sql_2 = "select * from vw_allergies where to_facility_id = '".$facility_id."' ";
        $refferals[] = DB::select(DB::raw($sql_2));

        $sql_3 = "select * from vw_remote_investigation_results where to_facility_id = '".$facility_id."'";
        $refferals[] = DB::select(DB::raw($sql_3));


        return $refferals;

    }


    public function getExternalRequestsDetails($visit_id){
        //return $facility_id;
        $refferals=[];

        $sql_1 = "select * from vw_history_examinations where visit_date_id = '".$visit_id."' AND description IS NOT NULL";
        $refferals[] = DB::select(DB::raw($sql_1)); //CHIEF COMPLAIN


        $sql_2 = "select * from vw_allergies where visit_date_id = '".$visit_id."' ";
        $refferals[] = DB::select(DB::raw($sql_2));

        $sql_3 = "select * from vw_remote_investigation_results where visit_date_id = '".$visit_id."'";
        $refferals[] = DB::select(DB::raw($sql_3));


        $sql_4 = "select * from vw_history_examinations where visit_date_id = '".$visit_id."' AND other_complaints IS NOT NULL";
        $refferals[] = DB::select(DB::raw($sql_4)); //OTHER COMPLAIN

        $sql_5 = "select * from vw_history_examinations where visit_date_id = '".$visit_id."' AND hpi IS NOT NULL";
        $refferals[] = DB::select(DB::raw($sql_5)); //HPI


        return $refferals;

    }

    public function getTransferOut($facility_id){
        //return $facility_id;
        $sql = "select * from vw_referrals where sender_facility_id = '".$facility_id."' AND referral_type =1 GROUP BY visit_id";
        $ref = DB::select(DB::raw($sql));
        //$ref = json_decode($ref);
        return $ref;

    }






    public function getReferrals(Request $request)
    {
        $id = $request->input('sender_facility_id');
        $sql = "select * from vw_referrals where sender_facility_id = '".$id."' AND referral_type =1 AND status=1";
        $ref = DB::select(DB::raw($sql));
        return $ref;
    }

    public function reportRefferal(Request $request){
        //COPY INFORMATION TO THIS FACILITY LOCALLY

        if(!isset($request->patient_status)){
            return response()->json([
                'data' => 'Please Select Patient Status',
                'status' => 0
            ]);
        }
        else if(!isset($request->remarks)){
            return response()->json([
                'data' => 'Please Write remarks',
                'status' => 0
            ]);
        }

        else if(!isset($request->refferal_status)){
            return response()->json([
                'data' => 'Please Refferal Status',
                'status' => 0
            ]);
        }


        $patientInfo=$request->patientData;
        $first_name= $patientInfo['first_name'];
        $middle_name= $patientInfo['middle_name'];
        $last_name= $patientInfo['last_name'];
        $gender= $patientInfo['gender'];
        $medical_record_number= $patientInfo['medical_record_number'];
        $visit_id= $patientInfo['visit_id'];
        $patient_id= $patientInfo['patient_id'];
        $sender_facility_id= $patientInfo['sender_facility_id'];
        $status= $patientInfo['status'];
        $summary= $patientInfo['summary'];
        $created_at= $patientInfo['created_at'];
        $dob= $patientInfo['dob'];
        $doctor_email= $patientInfo['doctor_email'];
        $doctor_name= $patientInfo['doctor_name'];
        $doctor_number= $patientInfo['doctor_number'];
        $to_facility_id= $patientInfo['facility_id'];
        $referral_id= $patientInfo['referral_id'];
        $sender_id=$request->sender_id;

        $sql="INSERT IGNORE INTO users SET 
		      id='".$sender_id."',
		      name='".$doctor_name."',    
		      email='".$doctor_email."',    
		      password='".$sender_id."',    
		      gender='NOT SENT',    
		      proffesionals_id='1',    
		      mobile_number='".$doctor_number."', 
              created_at=CURRENT_TIMESTAMP,    
		      updated_at=CURRENT_TIMESTAMP,   			  
		      facility_id='".$sender_facility_id."'";
        DB::statement($sql);

        $sql="INSERT IGNORE  INTO tbl_patients SET 
		      id='".$patient_id."',
		      first_name='".$first_name."',    
		      middle_name='".$middle_name."',    
		      last_name='".$middle_name."',    
		      gender='".$gender."',    
		      facility_id='".$sender_facility_id."',    
		      user_id='".$sender_id."',    
		      medical_record_number='".$medical_record_number."',    
		      created_at=CURRENT_TIMESTAMP,    
		      updated_at=CURRENT_TIMESTAMP,    
		       dob='".$dob."'";
        DB::statement($sql);

        $account_number=patientRegistration::patientAccountNumber($sender_facility_id,$patient_id,$sender_id);
        //return $to_facility_id;

        $sql="INSERT IGNORE INTO tbl_accounts_numbers SET 
		      patient_id='".$patient_id."',
		      id='".$visit_id."', 	     
		      facility_id='".$sender_facility_id."',    
		      user_id='".$sender_id."',    
		      tallied=0,    
		      account_number='".$account_number."',    
		      created_at=CURRENT_TIMESTAMP,    
		      updated_at=CURRENT_TIMESTAMP
			  ";
        DB::statement($sql);

        $sql="INSERT IGNORE INTO  tbl_referrals SET id='".$referral_id."',
                              visit_id='".$visit_id."',		
                              referral_type=1,		
                              status=1,	
                          patient_id='".$patient_id."',							  
                              summary='".$summary."',		
                              sender_id='".$sender_id."',	
                              created_at=CURRENT_TIMESTAMP,		                      updated_at=CURRENT_TIMESTAMP,   							  
                              from_facility_id='".$sender_facility_id."',		
                              to_facility_id='".$to_facility_id."'
		
		    ";
        DB::statement($sql);




        $patient_status =$request->patient_status;
        $remarks=$request->remarks;
        $receiver_id=$request->receiver_id;
        $facility_id=$request->facility_id;
        $patient_id=$request->patient_id;
        $visit_id=$request->visit_id;
        $refferal_status=$request->refferal_status;
        $from_facility_id=$request->from_facility_id;
        $sender_id=$request->sender_id;
        if(isset($request->otherComplaints[0])){
            $historyExamID=$request->otherComplaints[0]['historyExamID'];
            $quer_0="INSERT IGNORE INTO tbl_history_examinations 
		            SET id='".$historyExamID."',
					    patient_id='".$patient_id."', 
					    visit_date_id='".$visit_id."', 
					    user_id='".$sender_id."', 
					    facility_id='".$facility_id."', 
					   	created_at=CURRENT_TIMESTAMP,
						updated_at=CURRENT_TIMESTAMP";

            DB::statement($quer_0);

            foreach($request->otherComplaints AS $otherComplaint){
                $complainId= $otherComplaint['complainId'];
                $date_attended= $otherComplaint['date_attended'];
                $description= $otherComplaint['description'];
                $historyExamID= $otherComplaint['historyExamID'];
                $duration= $otherComplaint['duration'];
                $duration_unit= $otherComplaint['duration_unit'];
                $status= $otherComplaint['status'];
                $hpi= $otherComplaint['hpi'];
                $other_complaints= $otherComplaint['other_complaints'];
                $patient_id= $otherComplaint['patient_id'];
                $visit_date_id= $otherComplaint['visit_date_id'];
                $quer_1="INSERT IGNORE INTO tbl_complaints 
		            SET id='".$complainId."',
					    description='".$description."', 
					    duration='".$duration."', 
					    duration_unit='".$duration_unit."', 
					    status='".$status."', 
					    other_complaints='".$other_complaints."', 
					    hpi='".$hpi."', 
					    history_exam_id='".$historyExamID."', 
						created_at=CURRENT_TIMESTAMP,
						updated_at=CURRENT_TIMESTAMP";

                DB::statement($quer_1);


            }




            foreach($request->historyExaminations AS $historyExamination){
                $complainId= $historyExamination['complainId'];
                $date_attended= $historyExamination['date_attended'];
                $description= $historyExamination['description'];
                $historyExamID= $historyExamination['historyExamID'];
                $hpi= $historyExamination['hpi'];
                $other_complaints= $historyExamination['other_complaints'];
                $patient_id= $historyExamination['patient_id'];
                $visit_date_id= $historyExamination['visit_date_id'];


            }
        }

        if(isset($request->remoteInvestigationResults[0])){
            $request_id=$request->remoteInvestigationResults[0]['request_id'];


            $sql="INSERT IGNORE INTO  tbl_requests SET id='".$request_id."',
                          visit_date_id='".$visit_id."',                     
                          patient_id='".$patient_id."',							  
                          requesting_department_id=1,		
                          doctor_id='".$sender_id."',                         created_at=CURRENT_TIMESTAMP,		                      updated_at=CURRENT_TIMESTAMP";
            DB::statement($sql);

            foreach($request->remoteInvestigationResults AS $remoteInvestigationResult){
                $item_id=$remoteInvestigationResult['item_id'];
                $account_id=$remoteInvestigationResult['account_id'];
                $clinical_note=$remoteInvestigationResult['clinical_note'];
                $confirmation_status=$remoteInvestigationResult['confirmation_status'];
                $patient_id=$remoteInvestigationResult['patient_id'];
                $receiver_id=$remoteInvestigationResult['receiver_id'];
                $remarks=$remoteInvestigationResult['remarks'];
                $description=$remoteInvestigationResult['description'];
                $request_id=$remoteInvestigationResult['request_id'];
                $resultsUuid=$remoteInvestigationResult['resultsUuid'];
                $sample_no=$remoteInvestigationResult['sample_no'];
                $to_facility_id=$remoteInvestigationResult['to_facility_id'];
                $verify_user=$remoteInvestigationResult['verify_user'];
                $visit_date=$remoteInvestigationResult['visit_date'];
                $orderUuid=$remoteInvestigationResult['orderUuid'];

                $sql_1="INSERT IGNORE INTO  tbl_orders SET 
			              id='".$orderUuid."',			
			              order_id='".$request_id."',
                          clinical_note='".$clinical_note."',                     
                          test_id='".$item_id."',							  
                          sample_no='".$sample_no."',                         created_at=CURRENT_TIMESTAMP,		                      updated_at=CURRENT_TIMESTAMP";
                DB::statement($sql_1);

                $sql_2="INSERT IGNORE INTO  tbl_results SET 
			              id='".$resultsUuid."',
			              order_id='".$request_id."',
                          item_id='".$item_id."',							  
                          description='".$description."',		
                          confirmation_status=1,		
                         verify_user='".$verify_user."',                         created_at=CURRENT_TIMESTAMP,		                      updated_at=CURRENT_TIMESTAMP";
                DB::statement($sql_2);


            }

        }

        $intergratingKeys=Tbl_integrating_key::where('facility_id',$request->facility_id)->where('api_type',1)->get();

        if(!isset($intergratingKeys[0])){
            return response()->json([
                'data' => 'BASE URL TO CENTRAL SERVER NOT SAVED TO THIS FACILITY SERVER,REGISTER NOW',
                'status' => 0
            ]);

        }

        $base_urls=$intergratingKeys[0]->base_urls;
        $private_keys=$intergratingKeys[0]->private_keys;
        $public_keys=$intergratingKeys[0]->public_keys;

        if(!isset($request->refferal_status)){
            return response()->json([
                'data' => 'REFFERAL STATUS MUST BE SELECTED',
                'status' => 0
            ]);
        }

        $foliolist_array=array();

        $patient_infos=array();
        $diseases=array();
        $items_array =array();
        //$entity_array =array();
        $entity_array["entities"]=array();
        $patient_infos['patient_status']=$request->patient_status;
        $patient_infos['remarks']=$request->remarks;
        $patient_infos['patient_id']=$request->patient_id;
        $patient_infos['visit_id']=$request->visit_id;
        $patient_infos['receiver_id']=$request->receiver_id;
        $patient_infos['receiver_id']=$request->receiver_id;
        $patient_infos['from_facility_id']=$from_facility_id;
        $patient_infos['facility_id']=$facility_id;
        $patient_infos['refferal_status']=$request->refferal_status;

        $patient_infos['FolioDisaeses']=array();
        $patient_infos['FolioItems']=array();

        array_push($foliolist_array,$patient_infos);

        $entity_array["entities"]=$foliolist_array;
        //array_push($entity_array["entities"],$foliolist_array);
        $data_string=json_encode($entity_array,JSON_PRETTY_PRINT);

        $request_url=$base_urls.'/api/updateReferals';
        $url=$base_urls.'/api/updateReferals';
        $request_method = 'POST';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function updateReferals(Request $request)
    {
        $visit_id= $request['entities'][0]['visit_id'];
        $refferal_status= $request['entities'][0]['refferal_status'];
        $facility_id =$request['entities'][0]['facility_id'];
        $remarks =$request['entities'][0]['remarks'];
        $patient_status =$request['entities'][0]['patient_status'];
        $receiver_id =$request['entities'][0]['receiver_id'];
        $sender_facility_name =$request['entities'][0]['from_facility_id'];

        $sql="UPDATE `tbl_referrals` t1 SET t1.receiver_id='".$receiver_id."',t1.patient_status='".$patient_status."',t1.remarks='".$remarks."', t1.`status` ='".$refferal_status."' where `visit_id` ='".$visit_id."' and `to_facility_id` ='".$facility_id."'";
        $update = DB::statement($sql);




        return response()->json([
            'data' => 'REFFERAL STATUS CHANGED,NOTIFICATION SENT TO '.$sender_facility_name,
            'status' => 1
        ]);
    }


    public function getPrevDiagnosis(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('visit_date_id');
        $sql = "select * from vw_prev_diagnosis where patient_id = '".$id."' AND visit_date_id = '".$date."' ORDER BY status ";
        $diag = DB::select(DB::raw($sql));
        return $diag;

    }

    public function prevHistory(Request $request)
    {
        $diag = [];
        $id = $request->input('patient_id');
        $date = $request->input('visit_date_id');
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
    public function getPrevRos(Request $request)
    {
        $diag = [];
        $id = $request->input('patient_id');
        $date = $request->input('visit_date_id');
        $sql = "select * from vw_review_of_systems where patient_id = '".$id."' AND visit_date_id = '".$date."' ";
        $sql1 = "select review_summary,name,prof_name from tbl_review_of_systems t1 
INNER JOIN tbl_review_systems t2 ON t1.review_system_id = t2.id
INNER JOIN users t3 ON t3.id = t2.user_id
INNER JOIN tbl_proffesionals t4 ON t4.id = t3.proffesionals_id
where t2.patient_id = '".$id."' AND t2.visit_date_id = '".$date."' AND review_summary IS NOT NULL ";
        $diag[] = DB::select(DB::raw($sql));
        $diag[] = DB::select(DB::raw($sql1));
        return $diag;
    }
    public function getPrevBirth(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_birth_history where patient_id = '".$id."' AND date_attended = '".$date."' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function getPrevFamily(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_family_history where patient_id = '".$id."' AND date_attended = '".$date."' ";
        $diag = DB::select(DB::raw($sql));
        return $diag;
    }
    public function getPrevPhysical(Request $request)
    {   $diag = [];
        $id = $request->input('patient_id');
        $date = $request->input('visit_date_id');
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


    public function getBillList(Request $request)
    {
        return DB::table('bills')
            ->where('facility_id', $request->input('facility_id'))
            ->groupby('patient_id')
            ->get();
    }
    public function cancelPatientBill(Request $request)
    {
        $patient_id = $request->input('patient_id');
        return DB::table('bills')
            ->where('patient_id', $patient_id)
            ->get();
    }

    public function cancelBillItem(Request $request)
    {
        $id = $request->input('id');
        $user_id = $request->input('user_id');
        $reason = $request->input('reason');
        $update = DB::table('tbl_invoice_lines')
            ->where('id', $id)
            ->update([
                'status_id' => 3,
                'user_id' => $user_id,
				'cancelling_reason' => $reason,
                'updated_at' => DB::Raw("CURRENT_TIMESTAMP"),
            ]);
        return $update;

    }
	
    // specialized clinics
    public function getSpecialClinics()
    {
        return DB::table('tbl_departments')->where('id','>',7)->get();
    }

    public function postToClinics(Request $request)
    {
        if(count($request->all())>0){
            $data = $request->all();
            Tbl_clinic_instruction::create($request->all());
            $data2 = Tbl_encounter_invoice::create($data);
            $invoice_id=$data2->id;
            return   $medData = Tbl_invoice_line::create(["invoice_id"=>$invoice_id,
                "item_type_id"=>$request->input('item_type_id'),
                "payment_filter"=>$request->input('payment_filter'),
                "quantity"=>number_format($request->input('quantity'), 2, '.', ''),
                "item_price_id"=>$request->input('item_price_id'),
                "user_id"=>$request->input('user_id'),
                "patient_id"=>$request->input('patient_id'),
                "status_id"=>$request->input('status_id'),
                "facility_id"=>$request->input('facility_id'),
                "discount_by"=>$request->input('user_id') ]);

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

        $sql = "SELECT date_clerked,name,patient_id, count(id) AS total_clients FROM vw_perfomances WHERE doctor_id = '".$user_id."' AND (time_treated BETWEEN '".$start."' AND '".$end."') AND facility_id = '".$facility_id."'  ";
        $sql2 = "SELECT date_clerked,name,patient_id, count(id) AS total_patients FROM vw_perfomances WHERE doctor_id = '".$user_id."' AND (time_treated BETWEEN '".$start_date."' AND '".$end_date."') AND facility_id = '".$facility_id."' ";
        $performance[] = DB::select(DB::raw($sql));
        $performance[] = DB::select(DB::raw($sql2));

        return $performance;

    }

    public function getCorpse(Request $request)
    {
        $search = $request->input('search');
        $facility_id = $request->input('facility_id');
        $sql = "SELECT * FROM tbl_corpses WHERE corpse_record_number LIKE '%".$search."%' OR first_name LIKE '%".$search."%' OR last_name LIKE '%".$search."%' AND (immediate_cause IS NULL) AND facility_id = '".$facility_id."' ";
        $corpse = DB::select(DB::raw($sql));
        return $corpse;
    }
    public function getCorpseList(Request $request)
    {
        $facility_id = $request->input('facility_id');
        $sql = "SELECT * FROM tbl_corpses WHERE  facility_id = '".$facility_id."' AND immediate_cause IS NULL GROUP BY corpse_record_number ";
        $corpse = DB::select(DB::raw($sql));
        return $corpse;
    }

    public function certifyCorpse(Request $request)
    {

		if($request->input("immediate_cause") == ""){
			return   $res= response()->json([
                'data' => 'immediate cause required',
                'status' => '0'
            ]);

		} 
	   
		if($request->input("underlying_cause") == ""){
			return  $res= response()->json([
                'data' => 'underlying  cause required',
                'status' => '0'
            ]);

        } 
        
		if($request->input("diagnosis_code") == ""){
			return  $res= response()->json([
                'data' => 'diagnosis code required',
                'status' => '0'
            ]);
		}
	   
		$underlying_cause= $request->input("underlying_cause");
		$immediate_cause= $request->input("immediate_cause");
		$diagnosis_code= $request->input("diagnosis_code");
		$user_id= $request->input("user_id");
        
		//kama ni mgonjwa wa ndani 
		if($request->input("patient_id") != ""){
			//kama yupo kwenye database ya corpse
			$checkDuplicate=Tbl_corpse_admission::where("patient_id",$request->input("patient_id"))->get();

			if(count($checkDuplicate)==0){
				//then msajili kwanza
				$patientDATA=Tbl_patient::where("id",$request->input("patient_id"))->first();
				$description='';
				$death_condition='';
				$corpse_properties='';
				$names='';

				$user_id=$request->input("patient_id");

				$patient_id=$request->input("patient_id");
				$facility_id=$request->input('facility_id');
				$gender=$patientDATA->gender;
				$mobile_number=$patientDATA->mobile_number;
				$first_name=$request->input('first_name');
				$middle_name=$request->input('middle_name');
				$last_name=$request->input('last_name');
				$residence_id=$patientDATA->residence_id;
				$death_condition="NILL";

				$dob=$patientDATA->dob;
				$dod=Date("Y-m-d");
				$time = date('Y-m-d H:i:s');;


				$user_id=$request->input('user_id');
				
				if (!is_numeric($residence_id)) {
					$res= response()->json([
						'data' => 'PLEASE ENTER CORPSE RESIDENCE',
						'status' => '0'
					]);
				}else {
					$mobile_number_supporter='';
					$names='';
					$relationship='';
					$storage_reason='Storage';
					$vehicle_number='';
					$registrationInfos=['gender'=>$gender,
							'dob'=>$dob,
							'dod'=>$dod,
							'first_name'=>$first_name,
							'middle_name'=>$middle_name,
							'last_name'=>$last_name,
							'residence_id'=>$residence_id,
							'residence_found'=>$residence_id,
							'country_id'=>1,
							'facility_id'=>$facility_id,
							'mobile_number'=>$mobile_number_supporter,
							'transport'=>$vehicle_number,
							'storage_reason'=>$storage_reason,
							'relationship'=>$relationship,
							'corpse_brought_by'=>$names,
							'description'=>$description,
							'corpse_conditions'=>$death_condition,
							'corpse_properties'=>$corpse_properties,
							'corpse_properties_given_to'=>$names,
							'diagnosis_id'=>$request->input("diagnosis_id"),
							'immediate_cause'=>$request->input("immediate_cause"),
							'underlying_cause'=>$request->input("underlying_cause"),
							'diagnosis_code'=>$request->input("diagnosis_code"),
							'user_id'=>$request->input("user_id")
						];
						

					// return $registrationInfos;
					$checkCorpsDup=Tbl_corpse::where('facility_id',$facility_id)
												->where('facility_id',$facility_id)
												->where("immediate_cause",$immediate_cause)
												->where("underlying_cause",$underlying_cause)
												->where("death_certifier" ,$user_id)
												->where("death_certifier" ,$request->input("user_id"))
												->where("diagnosis_code",$request->input("diagnosis_code"))
												->where("diagnosis_id" ,$request->input("diagnosis_id"))->get();
					
					if(count($checkCorpsDup)==0){
						$getCorpseData= patientRegistration::corpsesNumber($registrationInfos);
					}else{
						$getCorpseData= Tbl_corpse_admission::where("patient_id" ,$patient_id)->select("corpse_id as id")->first();
					}
					$id = $getCorpseData->id;
					$corpse_id =$getCorpseData->id;
					$immediate_cause = $request['immediate_cause'];
					$underlying_cause = $request['underlying_cause'];
					$user_id = $request['death_certifier'];
					$facility_id = $request['facility_id'];
					$time = date('Y-m-d H:i:s');
					
					$corpse = Tbl_corpse::where('id',$id)
						->where('facility_id',$facility_id)
						->update([
							"immediate_cause" =>$immediate_cause,
							"underlying_cause" =>$underlying_cause,
							"death_certifier" =>$request->input("user_id"),
							"time_of_death_certifier" =>$time,
						]);


					//angalia kama hajalazwa
					$checkDupl=Tbl_corpse_admission::where("patient_id",$patient_id)
													->where("corpse_id",'!=',null)->where("user_id",'!=',null)->get();
					if(count($checkDupl)>0){
						$Data2= $request_mortuary = Tbl_corpse_admission::where("patient_id",$patient_id)->update([
							"facility_id"=>$facility_id,
							"admission_status_id"=>1,
							"corpse_id"=>$getCorpseData->id,
							"admission_date"=>DATE('Y-m-d'),
							"user_id"=>$request->input("user_id")

						]);

						$res= response()->json([
								'data' => 'Verified',
								'status' => '1'
							]);
					}else{
						$Data2= $request_mortuary = Tbl_corpse_admission::create([
													"facility_id"=>$facility_id,
													"admission_status_id"=>1,
													"corpse_id"=>$corpse_id, 
													"patient_id"=>$patient_id,
													"admission_date"=>DATE('Y-m-d'),
													"user_id"=>$request->input("user_id")
												]);

						 $res= response()->json([
										'data' => 'Verified',
										'status' => '1'
									]);
					}

				}
			}else{
				//achana nae usimjajili tena
				$res= response()->json([
							'data' => 'Duplicate',
							'status' => '0'
						]);
			 }
		}else{
			//huyu ni maiti wa nje 
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
					"death_certifier" =>$request->input("user_id"),
					"time_of_death_certifier" =>$time,
				]);


            //angalia kama hajalazwa
            $checkDupl=Tbl_corpse_admission::where("corpse_id",$corpse_id)->get();
            
			if(count($checkDupl)>0){
				$Data2= $request_mortuary = Tbl_corpse_admission::where("corpse_id",$corpse_id)->update([
											"facility_id"=>$facility_id,
											"admission_status_id"=>1, 
											"corpse_id"=>$corpse_id,
											"admission_date"=>DATE('Y-m-d'),
											"user_id"=>$request->input("user_id")

										]);
				$res= response()->json([
									'data' => 'Done',
									'status' => '1'
								]);
			}else{
				$Data2= $request_mortuary = Tbl_corpse_admission::create([
												"facility_id"=>$facility_id,
												"admission_status_id"=>1,
												"corpse_id"=>$corpse_id, 
												"admission_date"=>DATE('Y-m-d'),
												"user_id"=>$request->input("user_id")
											]);
				$res= response()->json([
					'data' => 'Done',
					'status' => '1'
				]);
			}
		}
		return $res;
    }

    public function requestBlood(Request $request)
    {
        $newData= Tbl_blood_request::create($request->all());

        $patient_id=$newData->patient_id;
        $trackable_id=$newData->id;
        $user_id=$newData->user_id;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

        return response()->json([
            'msg' => 'Blood request successfully sent',
            'status' => 1
        ]);

    }


    public function PostReferalBill(Request $request)
    {
        $residence_id=1;
        $patient_id=$request['patient_id'];
        $id=$request['patient_id'];
        $first_name=$request['first_name'];
        $middle_name=$request['middle_name'];
        $last_name=$request['last_name'];
        $gender=$request['gender'];
        $dob=$request['dob'];
        $medical_record_number=$request['medical_record_number'];
        $facility_id=$request['facility_id'];
        $user_id=$request['user_id'];
        $main_category_id=$request['main_category_id'];
        $item_type_id=$request['item_type_id'];
        $price_id=$request['item_price_id'];
        $quantity=$request['quantity'];
        $payment_filter=$request['payment_filter'];
        $status_id=$request['status_id'];
        $bill_id=$request['bill_id'];
        //$residence_id=$request['residence_id'];
        $account_number_id=$request['visit_id'];


        if(patientRegistration::duplicate('tbl_patients',array('id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >= 0))"), array($patient_id))==false){

            $qry = "INSERT INTO tbl_patients SET medical_record_number='".$medical_record_number."',id='".$id."',first_name='".$first_name."',middle_name='".$middle_name."',last_name='".$last_name."',gender='".$gender."',dob='".$dob."',user_id='".$user_id."',facility_id='".$facility_id."',residence_id='".$residence_id."',created_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP ";
            DB::statement($qry);
        }
        $account_number = patientRegistration::patientAccountNumber($facility_id,$patient_id,$user_id);
        $sql = "INSERT INTO tbl_accounts_numbers SET account_number='".$account_number."',id='".$account_number_id."',patient_id='".$patient_id."',facility_id='".$facility_id."',created_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP";
        DB::statement($sql);

        if(patientRegistration::duplicate('tbl_invoice_lines',array('patient_id','item_type_id','quantity',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($patient_id,$item_type_id,$quantity,''))==true){

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }

        else{
            $payment_category =Tbl_bills_category::create(['patient_id'=>$patient_id,'account_id'=>$account_number_id,'user_id'=>$user_id,'bill_id'=>$bill_id,'main_category_id'=>$main_category_id]);



            $encounter =Tbl_encounter_invoice::create(array('account_number_id'=>$account_number_id,'facility_id'=>$facility_id,'user_id'=>$user_id));


            if($encounter->save()){
                $invoice_line =Tbl_invoice_line::create(array('invoice_id'=>$encounter->id,'payment_filter'=>$payment_filter,
                    'item_type_id'=>$item_type_id,'facility_id'=>$facility_id,'quantity'=>number_format($quantity, 2, '.', ''),'user_id'=>$user_id,'item_price_id'=>$price_id,'status_id'=>$status_id,'discount_by'=>$user_id,'patient_id'=>$patient_id));


                return response()->json([
                    'msg' => 'BILL SUCCESSFUL CREATED...',
                    'status' => 1
                ]);


            }
        }
        // }
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

    public function getPrevDiagnosisConfirmed(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('visit_date_id');
        $sql = "select * from vw_prev_diagnosis where patient_id = '".$id."' AND visit_date_id = '".$date."' AND status='Confirmed' limit 1 ";
        $diag = DB::select(DB::raw($sql));
        return $diag;

    }



}