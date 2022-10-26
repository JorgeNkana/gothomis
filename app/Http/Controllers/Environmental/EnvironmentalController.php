<?php

namespace App\Http\Controllers\Environmental;

use App\classes\patientRegistration;
use App\Environmental\Tbl_anti_rabies_registry;
use App\Environmental\Tbl_anti_rabies_vaccination;
use App\Environmental\Tbl_environmental_equipment;
use App\Environmental\Tbl_environmental_equipment_receiving;
use App\Environmental\Tbl_environmental_equipment_register;
use App\Environmental\Tbl_environmental_waste_collection;
use App\Environmental\Tbl_notifiable_disease;
use App\Environmental\Tbl_nuisance_compose;
use App\Environmental\Tbl_nuisance_register;
use App\Environmental\Tbl_waste_disposal_method;
use App\Environmental\Tbl_waste_disposition;
use App\Environmental\Tbl_waste_type;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class EnvironmentalController extends Controller
{
    //

    public function nuisance_registration(Request $request)
    {

        if(patientRegistration::duplicate('tbl_nuisance_registers',array('nuisance',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['nuisance']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        Tbl_nuisance_register::create($request->all());
        return response()->json([
            'msg' => 'Successful Saved',
            'status' => 1
        ]);
    }
    public function save_notifiable_Diagnosis(Request $item)
    {
foreach ($item->all() as $request){
    if(count(Tbl_notifiable_disease::where('diagnosis_id',$request['diagnosis_id'])->get())>0){

    }
    else{
        Tbl_notifiable_disease::create($request);

    }

}

        return response()->json([
            'msg' => 'Notifiable Diseases are Successful Saved',
            'status' => 1
        ]);
    }

    public function patient_notified_admision_status($visit_id)
    {

        $ql= "SELECT t1.patient_id,t4.id,t1.updated_at as created_at,t2.ward_name,t5.bed_name,t7.name,t7.mobile_number FROM tbl_instructions t1 INNER JOIN tbl_wards t2 on t1.ward_id=t2.id INNER JOIN tbl_patients t3 on t3.id=t1.patient_id INNER JOIN tbl_accounts_numbers t4 on t4.id=t1.patient_id INNER JOIN tbl_beds t5 on t5.id=t1.ward_id INNER JOIN tbl_admissions t6 on t6.id=t1.admission_id INNER JOIN users t7 on t7.id=t6.user_id WHERE  t4.id=$visit_id";

        return DB::select(DB::raw($ql));
    }

    public function recent_notified_disease($facility_id)
    {
        $sql=" SELECT CONCAT(t5.first_name,' ',t5.middle_name,' ',t5.last_name,' ','(',t5.medical_record_number,')') as names,t5.gender, t1.patient_id,t1.visit_date_id,t2.diagnosis_description_id as diagnosis_id,t2.status,t3.description,t2.created_at,t1.admission_id,t6.name FROM tbl_diagnoses t1 join tbl_diagnosis_details t2 on t1.id=t2.diagnosis_id join tbl_diagnosis_descriptions t3 on
  t3.id=t2.diagnosis_description_id join tbl_notifiable_diseases t4 on t4.diagnosis_id=t3.id join tbl_patients t5 on t5.id=t1.patient_id join users  t6 on t6.id=t1.user_id WHERE t1.facility_id='{$facility_id}' AND TIMESTAMPDIFF(day,t2.created_at, CURRENT_DATE)<=5 AND t2.status='Confirmed' ORDER  BY t2.created_at asc";
        return DB::select(DB::raw($sql));

    }
    public function save_notifiable_Diagnosis_list()
    {

      $sql="SELECT t1.id,t1.description FROM tbl_notifiable_diseases t2 join tbl_diagnosis_descriptions t1 on t1.id=t2.diagnosis_id";
   return DB::select(DB::raw($sql));
    }
    public function patient_notifed_Diagnosis_list(Request $request)
    { $start=$request['start_date'];
        $end=$request['end_date'];
        $facility_id=$request['facility_id'];
      $sql=" SELECT CONCAT(t5.first_name,' ',t5.middle_name,' ',t5.last_name,' ','(',t5.medical_record_number,')') as names,t5.gender, t1.patient_id,t1.visit_date_id,t2.diagnosis_description_id as diagnosis_id,t2.status,t3.description,t2.created_at,t1.admission_id,t6.name FROM tbl_diagnoses t1 join tbl_diagnosis_details t2 on t1.id=t2.diagnosis_id join tbl_diagnosis_descriptions t3 on
  t3.id=t2.diagnosis_description_id join tbl_notifiable_diseases t4 on t4.diagnosis_id=t3.id join tbl_patients t5 on t5.id=t1.patient_id join users  t6 on t6.id=t1.user_id WHERE t1.facility_id='{$facility_id}' AND (t2.created_at BETWEEN '{$start}' AND '{$end}') AND t2.status='Confirmed' ORDER  BY t2.created_at desc";
   return DB::select(DB::raw($sql));
    }

    public function patient_notifed_Diagnosis_freq(Request $request)
    { $start=$request['start_date'];
        $end=$request['end_date'];
        $facility_id=$request['facility_id'];
      $sql=" SELECT count(t5.gender) as total,t5.gender, t2.status,t3.description,t2.created_at FROM tbl_diagnoses t1 join tbl_diagnosis_details t2 on t1.id=t2.diagnosis_id join tbl_diagnosis_descriptions t3 on
  t3.id=t2.diagnosis_description_id join tbl_notifiable_diseases t4 on t4.diagnosis_id=t3.id join tbl_patients t5 on t5.id=t1.patient_id join users  t6 on t6.id=t1.user_id WHERE t1.facility_id='{$facility_id}' AND (t2.created_at BETWEEN '{$start}' AND '{$end}') AND t2.status='Confirmed' GROUP  BY t5.gender,t3.description";
   return DB::select(DB::raw($sql));
    }

 public function summary_out_break_disease_death(Request $request)
    { $start=$request['start_date'];
        $end=$request['end_date'];
        $facility_id=$request['facility_id'];
$all=[];
        $disease="SELECT  t3.description,
  ifnull(sum(CASE when t5.gender ='MALE'   AND timestampdiff(YEAR ,dob,CURDATE()) <5  then 1 ELSE  0 END ),0)as c_total_male_u_5, 
  ifnull(sum(CASE when t5.gender ='FEMALE'   AND timestampdiff(YEAR ,dob,CURDATE()) <5  then 1 ELSE  0 END ),0)as c_total_female_u_5,
  
   ifnull(sum(CASE when t5.gender ='MALE'   AND timestampdiff(YEAR ,dob,CURDATE()) >5  then 1 ELSE  0 END ),0)as c_total_male_a_5, 
  ifnull(sum(CASE when t5.gender ='FEMALE'   AND timestampdiff(YEAR ,dob,CURDATE()) >5  then 1 ELSE  0 END ),0)as c_total_female_a_5,
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,CURDATE()) >5  then 1 ELSE  0 END ),0)as c_total
   
    FROM tbl_diagnoses t1 join tbl_diagnosis_details t2 on t1.id=t2.diagnosis_id join tbl_diagnosis_descriptions t3 on
  t3.id=t2.diagnosis_description_id join tbl_notifiable_diseases t4 on t4.diagnosis_id=t3.id join tbl_patients t5 on t5.id=t1.patient_id join users  t6 on t6.id=t1.user_id WHERE t1.facility_id='{$facility_id}' AND (t2.created_at BETWEEN '{$start}' AND '{$end}') AND t2.status='Confirmed' GROUP  BY t5.gender,t3.description  ";

return DB::select(DB::raw($disease));
    }

    public function nuisance_composition(Request $request)
    {

        if(patientRegistration::duplicate('tbl_nuisance_composes',array('nuisance_id','cause','location',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['nuisance_id'],$request['cause'],$request['location']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        Tbl_nuisance_compose::create($request->all());
        return response()->json([
            'msg' => 'Successful Saved',
            'status' => 1
        ]);
    }

    public function environment_equipment_type_registration(Request $request)
    {

        if(patientRegistration::duplicate('tbl_environmental_equipments',array('equipment_type',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['equipment_type']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        Tbl_environmental_equipment::create($request->all());
        return response()->json([
            'msg' => 'Successful Saved',
            'status' => 1
        ]);
    }
    public function environment_waste_registration(Request $request)
    {

        if(patientRegistration::duplicate('tbl_waste_types',array('waste_type',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['waste_type']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        Tbl_waste_type::create($request->all());
        return response()->json([
            'msg' => 'Successful Saved',
            'status' => 1
        ]);
    }

 public function environment_equipment_registration(Request $request)
    {

        if(patientRegistration::duplicate('tbl_environmental_equipment_registers',array('equipment_name',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['equipment_name']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        Tbl_environmental_equipment_register::create($request->all());
        return response()->json([
            'msg' => 'Successful Saved',
            'status' => 1
        ]);
    }
    public function environment_waste_dispose_registration(Request $request)
    {

        if(patientRegistration::duplicate('tbl_waste_disposal_methods',array('waste_dispose',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['waste_dispose']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        Tbl_waste_disposal_method::create($request->all());
        return response()->json([
            'msg' => 'Successful Saved',
            'status' => 1
        ]);
    }

    public function environment_waste_disposal(Request $request)
    {

        if(patientRegistration::duplicate('tbl_waste_dispositions',array('waste_disposed','waste_disposal_type','waste_type_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['waste_disposed'],$request['waste_disposal_type'],$request['waste_type_id']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        Tbl_waste_disposition::create($request->all());
        return response()->json([
            'msg' => 'Successful Saved',
            'status' => 1
        ]);
    }
    public function environment_equipment_receiving(Request $request)
    {

        if(patientRegistration::duplicate('tbl_environmental_equipment_receivings',array('equipment_id','quantity',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['equipment_id'],$request['quantity']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        Tbl_environmental_equipment_receiving::create($request->all());
        return response()->json([
            'msg' => 'Successful Saved',
            'status' => 1
        ]);
    }
    public function environment_waste_collection(Request $request)
    {

        if(patientRegistration::duplicate('tbl_environmental_waste_collections',array('waste_type_id','waste_collected',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['waste_type_id'],$request['waste_collected']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        Tbl_environmental_waste_collection::create($request->all());
        return response()->json([
            'msg' => 'Successful Saved',
            'status' => 1
        ]);
    }

    //ant_rabies_vaccination_registry
    public function ant_rabies_vaccination_registry(Request $request)
    {

        if(patientRegistration::duplicate('tbl_anti_rabies_vaccinations',array('batch_no','quantity',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['batch_no'],$request['quantity']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        Tbl_anti_rabies_vaccination::create($request->all());
        return response()->json([
            'msg' => 'Successful Saved',
            'status' => 1
        ]);
    }

    public function patient_antrabies_vaccination(Request $request)
    {

        if(patientRegistration::duplicate('tbl_anti_rabies_registries',array('patient_id','vaccination_id','vacc_type','dose_type',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['patient_id'],$request['vaccination_id'],$request['vacc_type'],$request['dose_type']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        Tbl_anti_rabies_registry::create($request->all());
        return response()->json([
            'msg' => 'Successful Saved',
            'status' => 1
        ]);
    }

    public function ant_rabies_vaccination_update($id)
    {
       return Tbl_anti_rabies_vaccination::where('id',$id)->update(['status'=>0]) ;
    }
    public function ant_rabies_vaccination_list($facility_id)
    {
       return Tbl_anti_rabies_vaccination::where('facility_id',$facility_id)->orderBy('status','desc')->get() ;
    }
    public function ant_rabies_vaccination_usage($facility_id)
    {
       return Tbl_anti_rabies_vaccination::where('facility_id',$facility_id)->where('status',1)->orderBy('created_at','asc')->get() ;
    }
    public function environment_equipment_issuing(Request $request)
    {
        if($request['equipment_id']=='' && $request['issued_quantity']==''){
            return response()->json([
                'msg' => 'Please all fields',
                'status' => 0
            ]);
        }
        else if ($request['equipment_id']==''){
            return response()->json([
                'msg' => 'Please Choose Item Issuing',
                'status' => 0
            ]);
        } else if ($request['issued_quantity']==''){
            return response()->json([
                'msg' => 'Please Enter Quantity To Issue',
                'status' => 0
            ]);
        }

        else{
            $equipment_id=$request['equipment_id'];
            $issued_quantity=$request['issued_quantity'];
            $user_id=$request['user_id'];
            $facility_id=$request['facility_id'];

            if(patientRegistration::duplicate('tbl_environmental_equipment_receivings',array('equipment_id','issued_quantity','facility_id','user_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['equipment_id'],$request['issued_quantity'],$request['facility_id'],$request['user_id']))==true) {

                return response()->json([
                    'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                    'status' => '0'
                ]);
            }
            $getbalance=Tbl_environmental_equipment_receiving::where('equipment_id',$equipment_id)
                ->where('facility_id',$facility_id)
                ->where('quantity','>=',$issued_quantity)
                ->where('status','l')
                ->first();
            if (count($getbalance)>0){
                $ref_id=$getbalance->id;
                $balance_remained=$getbalance->quantity-$issued_quantity;
                if ($balance_remained >= 0) {
                    Tbl_environmental_equipment_receiving::where('id',$ref_id)->update([
                        'status'=>'c'
                    ]);
//
                    $send=new Tbl_environmental_equipment_receiving($request->all());
                    $send['quantity']=$balance_remained;
                    $send['status']='l';
                    $send['facility_id']=$facility_id;
                    $send['equipment_id']=$equipment_id;
                    $send['user_id']=$user_id;
                    $send['issued_quantity']=$request['issued_quantity'];
                    $send->save();
                    return response()->json([
                        'msg' =>  'Request Successful Processed',
                        'status' => 1
                    ]);

                } else {

                    return response()->json([
                        'msg' => 'No Balance Enough To issue  '  . '.Only ' . $getbalance->quantity . ' Items(s) Remained',
                        'status' => 0
                    ]);
                }
            }

            else{
                return response()->json([
                    'msg' => 'This Item Is not available in Your  Stock',
                    'status' => 0
                ]);
            }

        }
    }
    public function nuisance_update(Request $request)
    {
        Tbl_nuisance_register::where('id',$request['id'])->update($request->all());
        return response()->json([
            'msg' => 'Successful Updated',
            'status' => 1
        ]);
    }
    public function equipment_type_update(Request $request)
    {
        Tbl_environmental_equipment::where('id',$request['id'])->update($request->all());
        return response()->json([
            'msg' => 'Successful Updated',
            'status' => 1
        ]);
    }
    public function waste_type_update(Request $request)
    {
        Tbl_waste_type::where('id',$request['id'])->update($request->all());
        return response()->json([
            'msg' => 'Successful Updated',
            'status' => 1
        ]);
    }
    public function waste_dispose_update(Request $request)
    {
        Tbl_waste_disposal_method::where('id',$request['id'])->update($request->all());
        return response()->json([
            'msg' => 'Successful Updated',
            'status' => 1
        ]);
    }
    public function environment_equipment_update(Request $request)
    {
       $request->all();
        Tbl_environmental_equipment_register::where('id',$request['id'])->update($request->all());
        return response()->json([
            'msg' => 'Successful Updated',
            'status' => 1
        ]);
    }
    public function nuisance_list()
    {
     return   Tbl_nuisance_register::get();

    }
    public function equipment_type_list()
    {
     return   Tbl_environmental_equipment::get();

    }
    public function waste_type_list()
    {
     return   Tbl_waste_type::get();

    }
    public function waste_dispose_list()
    {
     return   Tbl_waste_disposal_method::get();

    }
    public function environment_equipment_list($facility)
    {
     return   DB::table('tbl_environmental_equipment_registers')
         ->join('tbl_environmental_equipments','tbl_environmental_equipments.id','tbl_environmental_equipment_registers.equipment_type_id')
        ->select('tbl_environmental_equipment_registers.*','tbl_environmental_equipments.equipment_type')
         ->where('facility_id',$facility)->get();

    }

 public function equipment_received_list(Request $request)
    {
        $start=$request['start_date'];
        $end=$request['end_date'];
        $facility=$request['facility_id'];
     return   DB::table('tbl_environmental_equipment_receivings')
         ->join('tbl_environmental_equipment_registers','tbl_environmental_equipment_receivings.equipment_id','tbl_environmental_equipment_registers.id')
        ->select('tbl_environmental_equipment_receivings.*','tbl_environmental_equipment_registers.equipment_name')
         ->whereBetween('tbl_environmental_equipment_receivings.created_at',[$start,$end])
         ->where('tbl_environmental_equipment_receivings.facility_id',$facility)
         ->where('tbl_environmental_equipment_receivings.status_received','=','r')
         ->get();

    }
    public function ant_rabies_monitoring(Request $request)
    {
        $start=$request['start_date'];
        $end=$request['end_date'];
        $facility=$request['facility_id'];
     return   DB::table('tbl_patients')
         ->join('tbl_anti_rabies_registries','tbl_anti_rabies_registries.patient_id','tbl_patients.id')
         ->join('tbl_anti_rabies_vaccinations','tbl_anti_rabies_registries.vaccination_id','tbl_anti_rabies_vaccinations.id')
         ->join('users','tbl_anti_rabies_registries.user_id','users.id')
        ->select('tbl_anti_rabies_registries.*','tbl_anti_rabies_vaccinations.batch_no','tbl_anti_rabies_vaccinations.ant_rabies_name','users.name','tbl_patients.first_name','tbl_patients.middle_name','tbl_patients.last_name','tbl_patients.medical_record_number','tbl_patients.gender','tbl_patients.dob','tbl_patients.mobile_number')
         ->whereBetween('tbl_anti_rabies_registries.created_at',[$start,$end])
         ->where('tbl_anti_rabies_registries.facility_id',$facility)
->orderBy('first_name','asc')
         ->get();

    }
    public function equipment_issued_list(Request $request)
    {
        $start=$request['start_date'];
        $end=$request['end_date'];
        $facility=$request['facility_id'];
     return   DB::table('tbl_environmental_equipment_receivings')
         ->join('tbl_environmental_equipment_registers','tbl_environmental_equipment_receivings.equipment_id','tbl_environmental_equipment_registers.id')
        ->select('tbl_environmental_equipment_receivings.*','tbl_environmental_equipment_registers.equipment_name')
         ->whereBetween('tbl_environmental_equipment_receivings.created_at',[$start,$end])
         ->where('tbl_environmental_equipment_receivings.facility_id',$facility)
         ->where('tbl_environmental_equipment_receivings.issued_quantity','!=',null)
         ->get();

    }

public function equipment_balances(Request $request)
    {


        $facility=$request['facility_id'];

        return   DB::table('tbl_environmental_equipment_receivings')
         ->join('tbl_environmental_equipment_registers','tbl_environmental_equipment_receivings.equipment_id','tbl_environmental_equipment_registers.id')
        ->select('tbl_environmental_equipment_receivings.*','tbl_environmental_equipment_registers.equipment_name',DB::raw('sum(quantity) as balance'))
         ->where('tbl_environmental_equipment_receivings.facility_id',$facility)
         ->where('tbl_environmental_equipment_receivings.status','=','l')
            ->groupBy('tbl_environmental_equipment_receivings.equipment_id')
            ->get();

    }

public function environment_Receiving_issuing_summary($request)
    {


        $facility=$request;
$all=[];
        $all[]=DB::table('tbl_environmental_equipment_receivings')
         ->join('tbl_environmental_equipment_registers','tbl_environmental_equipment_receivings.equipment_id','tbl_environmental_equipment_registers.id')
        ->select('tbl_environmental_equipment_receivings.*','tbl_environmental_equipment_registers.equipment_name',DB::raw('sum(quantity) as balance'))
         ->where('tbl_environmental_equipment_receivings.facility_id',$facility)
            ->where('tbl_environmental_equipment_receivings.status_received','=','r')
            ->groupBy('tbl_environmental_equipment_receivings.equipment_id')
            ->get();
        $all[]=DB::table('tbl_environmental_equipment_receivings')
         ->join('tbl_environmental_equipment_registers','tbl_environmental_equipment_receivings.equipment_id','tbl_environmental_equipment_registers.id')
        ->select('tbl_environmental_equipment_receivings.*','tbl_environmental_equipment_registers.equipment_name',DB::raw('sum(issued_quantity) as issued_quantity'))
         ->where('tbl_environmental_equipment_receivings.facility_id',$facility)
            ->where('tbl_environmental_equipment_receivings.issued_quantity','!=',null)
            ->groupBy('tbl_environmental_equipment_receivings.equipment_id')
            ->get();

        return $all;

    }

    public function nuisance_composed( Request $request)
    {
        $start=$request['start_date'];
        $end=$request['end_date'];


     return   DB::table('tbl_nuisance_registers')->join('tbl_nuisance_composes','tbl_nuisance_composes.nuisance_id','=','tbl_nuisance_registers.id')->select('tbl_nuisance_registers.nuisance','tbl_nuisance_composes.*')->where('facility_id',$request['facility_id'])->whereBetween('tbl_nuisance_composes.created_at',[$start,$end])->get();

    }

    public function wastes_collected( Request $request)
    {
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];

$ql="SELECT t1.facility_id, sum(t1.waste_collected) as waste_collected ,t1.created_at,t2.waste_type,t3.equipment_name,t4.equipment_type FROM tbl_environmental_waste_collections t1 INNER JOIN tbl_waste_types t2 on t1.waste_type_id=t2.id INNER JOIN tbl_environmental_equipment_registers t3 on t3.id=t1.equipment_used_id INNER JOIN tbl_environmental_equipments t4 on t4.id=t1.waste_type_id
 WHERE t1.facility_id='{$facility_id}' AND (t1.created_at BETWEEN '{$start_date}' AND '{$end_date}') GROUP  BY t2.waste_type ";
      return  DB::select(DB::raw($ql));

    }
    public function waste_disposal_list( Request $request)
    {
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];

        $ql="
SELECT t1.facility_id, sum(t1.waste_disposed) as waste_disposed,t2.waste_dispose,t3.waste_type from tbl_waste_dispositions t1 INNER JOIN tbl_waste_disposal_methods t2 on t1.waste_disposal_type=t2.id
INNER JOIN tbl_waste_types t3 on t3.id=t1.waste_disposal_type 
 WHERE t1.facility_id='{$facility_id}' AND (t1.created_at BETWEEN '{$start_date}' AND '{$end_date}') GROUP BY t2.waste_dispose,t3.waste_type ";
        return  DB::select(DB::raw($ql));

    }
}