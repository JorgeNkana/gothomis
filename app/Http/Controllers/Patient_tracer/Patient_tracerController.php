<?php

namespace App\Http\Controllers\Patient_tracer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Patient_tracerController extends Controller
{
    //
    public function Patient_tracer(Request $request)
    {

        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];
        
        $sql = "SELECT t1.medical_record_number, CONCAT(t1.first_name,' ',t1.middle_name,' ',t1.last_name) AS patient_names ,
       t1.dob,t1.gender,t1.mobile_number,t2.created_at,t2.date_attended,t2.facility_id,t2.id as visit_id from tbl_patients t1 INNER  JOIN  
       tbl_accounts_numbers t2 ON  t1.id=t2.patient_id WHERE t2.facility_id='$facility_id' AND t2.created_at BETWEEN '".$start_date."'
       AND '".$end_date."'"
        ;

return DB::select(DB::raw($sql));
    }

    public function Patient_history_printed(Request $request)
    {

        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];

        $sql = "SELECT t1.medical_record_number, CONCAT(t1.first_name,' ',t1.middle_name,' ',t1.last_name) AS patient_names ,
       t1.dob,t1.gender,t1.mobile_number,t2.created_at,t2.date_attended,t2.facility_id,t2.id as visit_id from tbl_patients t1 INNER  JOIN  
       tbl_accounts_numbers t2 ON  t1.id=t2.patient_id WHERE t2.facility_id='$facility_id' AND t2.created_at BETWEEN '".$start_date."'
       AND '".$end_date."'"
        ;

return DB::select(DB::raw($sql));
    }

    public function Patient`_tracer(Request $request)
    {

        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];

        $sql = "SELECT t1.medical_record_number, CONCAT(t1.first_name,' ',t1.middle_name,' ',t1.last_name) AS patient_names ,
       t1.dob,t1.gender,t1.mobile_number,t2.created_at,t2.* from tbl_patients t1 INNER  JOIN  
       tbl_invoice_lines t2 ON  t1.id=t2.patient_id WHERE t2.facility_id='$facility_id' AND t2.created_at BETWEEN '".$start_date."'
       AND '".$end_date."' AND t2.sub_category_name='NHIF' group by t2.patient_id"
        ;

return DB::select(DB::raw($sql));
    }

    public function Patient_nhif_service_tracer(Request $request)
    {

        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['patient_id'];


 $sql = "SELECT                    
                    t1.quantity,
                    t1.sub_category_name,
                    t1.created_at,
                    t3.price,
                    t3.price AS unit_price,
                    t4.item_name, 
                    t10.department_name, 
                    t10.id as dept_id, 
                    t11.name as user_name 
					FROM tbl_invoice_lines t1
INNER JOIN tbl_item_prices t3 ON t1.item_price_id = t3.id
INNER JOIN tbl_items t4 ON t3.item_id = t4.id  
 INNER join tbl_departments t10 on t10.id=t4.dept_id
 INNER join users t11 on t11.id=t1.user_id
 WHERE t1.patient_id='$facility_id' AND t1.created_at BETWEEN '".$start_date."'
       AND '".$end_date."'    group by t1.id"
        ;

return DB::select(DB::raw($sql));
    }

    public function Patient_flow(Request $request)
    {

        $visit_id=$request['visit_id'];
$all=[];
         $ql1 = "SELECT t1.created_at,t2.name,t2.mobile_number from tbl_accounts_numbers t1 INNER JOIN users t2 on t1.user_id=t2.id WHERE t1.id='$visit_id'";
         $ql2 = "SELECT t1.invoice_id, t1.updated_at as created_at,t2.name,t2.mobile_number from tbl_invoice_lines t1 INNER JOIN users t2 on t1.user_id=t2.id
INNER join tbl_encounter_invoices t3 on t3.id=t1.invoice_id WHERE t3.account_number_id='$visit_id'";
         $ql3= "SELECT t1.created_at,t2.name,t2.mobile_number from tbl_history_examinations t1 INNER JOIN users t2 on t1.user_id=t2.id WHERE t1.visit_date_id='$visit_id'";
         $ql4= "SELECT t1.created_at,t2.name,t2.mobile_number from tbl_requests t1 INNER JOIN users t2 on t1.doctor_id=t2.id 
		 INNER JOIN tbl_orders t4 on t1.id=t4.order_id AND DATE(t1.created_at) = DATE(t4.created_at)
INNER JOIN tbl_items t5 on t4.test_id=t5.id 
WHERE t1.visit_date_id='$visit_id' and dept_id=2 GROUP by t1.visit_date_id";
         
		 $ql5= "SELECT t1.created_at,t2.name,t2.mobile_number from tbl_results t1 
INNER JOIN users t2 on t1.verify_user=t2.id 
INNER JOIN tbl_requests t3 on t1.order_id=t3.id 
INNER JOIN tbl_orders t4 on t1.id=t4.order_id AND DATE(t3.created_at) = DATE(t4.created_at)
INNER JOIN tbl_items t5 on t4.test_id=t5.id 
WHERE t3.visit_date_id='$visit_id' and dept_id=2 GROUP by t3.visit_date_id";
        $ql6= "SELECT t1.created_at,t2.name,t2.mobile_number from tbl_prescriptions t1 INNER JOIN users t2 on t1.prescriber_id=t2.id WHERE t1.visit_id='$visit_id'";
        $ql7= "SELECT t1.updated_at as created_at,t2.name,t2.mobile_number from tbl_prescriptions t1 INNER JOIN users t2 on t1.dispenser_id=t2.id WHERE t1.visit_id='$visit_id'";
        $ql8= "SELECT t4.id,t1.created_at,t2.ward_name,t5.bed_name,t7.name,t7.mobile_number FROM tbl_instructions t1 INNER JOIN tbl_wards t2 on t1.ward_id=t2.id INNER JOIN tbl_patients t3 on t3.id=t1.patient_id INNER JOIN tbl_accounts_numbers t4 on t4.id=t1.patient_id INNER JOIN tbl_beds t5 on t5.id=t1.ward_id INNER JOIN tbl_admissions t6 on t6.id=t1.admission_id INNER JOIN users t7 on t7.id=t6.user_id WHERE t4.id='$visit_id'";
        $ql9= "SELECT t1.patient_id,t4.id,t1.updated_at as created_at,t2.ward_name,t5.bed_name,t7.name,t7.mobile_number FROM tbl_instructions t1 INNER JOIN tbl_wards t2 on t1.ward_id=t2.id INNER JOIN tbl_patients t3 on t3.id=t1.patient_id INNER JOIN tbl_accounts_numbers t4 on t4.id=t1.patient_id INNER JOIN tbl_beds t5 on t5.id=t1.ward_id INNER JOIN tbl_admissions t6 on t6.id=t1.admission_id INNER JOIN users t7 on t7.id=t6.user_id WHERE t6.admission_status_id=4 AND t4.id='$visit_id'";

        $ql10= "SELECT t1.created_at,t2.name,t2.mobile_number from tbl_requests t1 INNER JOIN users t2 on t1.doctor_id=t2.id 
INNER JOIN tbl_orders t4 on t1.id=t4.order_id AND DATE(t1.created_at) = DATE(t4.created_at)
INNER JOIN tbl_items t5 on t4.test_id=t5.id 
WHERE t1.visit_date_id='$visit_id'and t5.dept_id=3 GROUP by t1.visit_date_id";
 $ql11= "SELECT t1.created_at,t2.name,t2.mobile_number from tbl_results t1 
INNER JOIN users t2 on t1.verify_user=t2.id 
INNER JOIN tbl_requests t3 on t1.order_id=t3.id 
INNER JOIN tbl_orders t4 on t1.id=t4.order_id AND DATE(t3.created_at) = DATE(t4.created_at)
INNER JOIN tbl_items t5 on t4.test_id=t5.id 
WHERE t3.visit_date_id='$visit_id' and t5.dept_id=3 GROUP by t3.visit_date_id";        

 $all[]=DB::select(DB::raw($ql1));
 $all[]=DB::select(DB::raw($ql2));
 $all[]=DB::select(DB::raw($ql3));
 $all[]=DB::select(DB::raw($ql4));
 $all[]=DB::select(DB::raw($ql5));
 $all[]=DB::select(DB::raw($ql6));
 $all[]=DB::select(DB::raw($ql7));
 $all[]=DB::select(DB::raw($ql8));
 $all[]=DB::select(DB::raw($ql9));
 $all[]=DB::select(DB::raw($ql10));
 $all[]=DB::select(DB::raw($ql11));
        return $all;
    }

    public function demographicDetails(Request $request)
    {
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        return  DB::select("SELECT p.id,p.dob,p.first_name,p.middle_name,p.last_name,p.dob,
p.gender,p.gender,p.medical_record_number,p.mobile_number,m.marital_status, r.residence_name,re.relationship,ne.next_of_kin_name,ne.mobile_number as nex_mobile_number,
c.country_name,o.occupation_name FROM tbl_patients p JOIN tbl_residences r on p.residence_id=r.id 
join tbl_countries c on p.country_id=c.id JOIN tbl_occupations o on o.id=p.occupation_id join
 tbl_maritals m on m.id=p.marital_id  left JOIN tbl_relationships re on re.id=p.residence_id
 left JOIN tbl_next_of_kins ne on ne.patient_id=p.id where p.created_at  BETWEEN '".$start_date."'
       AND '".$end_date."'");
    }

    public function DiagnosisLIst(Request $request)
    {

       $start=$request->input('start_date');
       $code=$request->input('code');
       $end=$request->input('end_date');
       if($request->input('code')){
           $sql = "SELECT
          
            t4.description,
            t4.code,
            t6.medical_record_number ,
              t5.name,
               t7.prof_name as proffesion,
          
            t2.status,
               t1.created_at
            FROM tbl_diagnoses t1
            join tbl_diagnosis_details t2 on t1.id = t2.diagnosis_id
            JOIN tbl_diagnosis_descriptions t4 ON t4.id = t2.diagnosis_description_id
            join users t5 ON t5.id=t1.user_id
            join tbl_patients t6 ON t1.patient_id=t6.id
            join tbl_proffesionals t7 ON t7.id=t5.proffesionals_id  where t4.code ='".$code."'  ";

       }
       else{
           $sql = "SELECT
          
            t4.description,
            t4.code,
            t6.medical_record_number ,
              t5.name,
               t7.prof_name as proffesion,
             
            t2.status,
               t1.created_at
            FROM tbl_diagnoses t1
            join tbl_diagnosis_details t2 on t1.id = t2.diagnosis_id
            JOIN tbl_diagnosis_descriptions t4 ON t4.id = t2.diagnosis_description_id
            join users t5 ON t5.id=t1.user_id
            join tbl_patients t6 ON t1.patient_id=t6.id
            join tbl_proffesionals t7 ON t7.id=t5.proffesionals_id where t1.created_at between '".$start."' AND '".$end."'  ";
       }

        $diag = DB::select(DB::raw($sql));
        return $diag;
    }


    public function getReferralLists(Request $request){
       $start=$request->input('start_date');
       $end=$request->input('end_date');
       $patient_id=$request->input('patient_id');
       if($request->input('patient_id')==''){
        $all[]= DB::select("SELECT r.*,f.facility_name,ft.description as facility_type from tbl_referrals r join tbl_facilities f on r.to_facility_id=f.id join tbl_facility_types ft on ft.id=f.facility_type_id where referral_code='OUTGOING' AND  r.created_at between '".$start."' AND '".$end."' ");

       }
       else{
        $all[]= DB::select("SELECT r.*,f.facility_name,ft.description as facility_type from tbl_referrals r join tbl_facilities f on r.to_facility_id=f.id join tbl_facility_types ft on ft.id=f.facility_type_id where referral_code='OUTGOING' AND  r.patient_id= '".$patient_id."' ");

       }

$all[]= DB::select("SELECT count(r.id) as total,f.facility_name,ft.description as facility_type from tbl_referrals r join tbl_facilities f on r.to_facility_id=f.id join tbl_facility_types ft on ft.id=f.facility_type_id where referral_code='OUTGOING' group by r.to_facility_id");

$all[]= DB::select("SELECT r.*,f.facility_name,ft.description as facility_type from tbl_referrals r join tbl_facilities f on r.from_facility_id=f.id join tbl_facility_types ft on ft.id=f.facility_type_id where referral_code='INCOMING' AND  r.created_at between '".$start."' AND '".$end."' ");

$all[]= DB::select("SELECT count(r.id) as total,f.facility_name,ft.description as facility_type from tbl_referrals r join tbl_facilities f on r.from_facility_id=f.id join tbl_facility_types ft on ft.id=f.facility_type_id where referral_code='INCOMING' group by r.from_facility_id");
return $all;

    }

     public function getInsurancePerformance(Request $request){
       $start=$request->input('start_date');
       $end=$request->input('end_date');
      return  DB::select("SELECT t2.name as doctor_name,t4.sub_category_name as insurance, count(t1.id) as total_clerked FROM trackables t1 join users t2 on t1.user_id =t2.id join tbl_bills_categories t3 on t3.patient_id=t1.patient_id join tbl_pay_cat_sub_categories t4 on t4.id=t3.bill_id where   t1.created_at between '".$start."' AND '".$end."' AND t4.pay_cat_id =2 group by t2.id,t3.bill_id order by t3.bill_id,t2.id asc  ");


     }

     
}