<?php
namespace App\Http\Controllers\laboratory;
use App\laboratory\Tbl_tb_leprosy_request;
use App\laboratory\Tbl_tb_leprosy_result;
use App\Residence\Tbl_residence;
use App\laboratory\Tbl_lab_reporting_control;
use App\laboratory\Tbl_lab_reporting_indictor_map;
use stdClass;
use App\User;
use App\classes\Barcode;
use App\Facility\Tbl_lab_test_live;
use App\Item_setups\Tbl_item_price;
use App\Item_setups\Tbl_item_type_mapped;
use App\laboratory\Tbl_panel;
use App\laboratory\Tbl_panel_components_result;
use App\laboratory\Tbl_sample_number_control;
use App\laboratory\Tbl_testspanel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\laboratory\Tbl_sample_status;
use App\laboratory\Tbl_equipment_status;
use App\laboratory\Tbl_lab_test_element;
use App\laboratory\Tbl_lab_test_indicator;
use App\laboratory\Tbl_lab_sample_to_collect;
use App\laboratory\Tbl_lab_request;
use App\laboratory\Tbl_lab_test_panel;
use App\laboratory\Tbl_color;
use App\laboratory\Tbl_order;
use App\laboratory\Tbl_equipment;
use App\laboratory\Tbl_item;
use App\laboratory\Tbl_unit;
use App\laboratory\Tbl_sub_department;
use App\laboratory\Tbl_result;
use App\laboratory\Tbl_staff_section;
use App\laboratory\Tbl_lab_machine_result;
use App\Patient\Tbl_patient;
use App\Patient\Tbl_accounts_number;
use App\admin\Tbl_notification;
use App\ClinicalServices\Tbl_prescription;
use App\ClinicalServices\Tbl_request;
use App\Facility\Tbl_facility;
use App\patient\Tbl_encounter_invoice;
use App\Payments\Tbl_invoice_line;
use App\admin\Tbl_integrating_key;
use App\Item_setups\Tbl_item_sub_department;
USE DB;
use App\classes\patientRegistration;
use Milon\Barcode\DNS1D;
use Illuminate\Support\Facades\Input;
use Auth;
use App\classes\SystemTracking;
use App\Trackable;
ini_set("max_execution_time",0);
class LaboratoryController extends Controller
{
    public function createLabsOrderNo(Request $request){
        $facility_id=$request->input('facility_id');
        return $orderNo = patientRegistration::labOrderNumber($facility_id);
    }

    public function processMobileNumber($mobile){
        $extracted=substr($mobile,1);
        $mobile="00255".$extracted;
        return $mobile;
    }

    //sample_status_registration
    public function sample_status_registration(Request $request)
    {
        $sample_name=$request['status'];
        //$equipmentstatus;
        $sampleregistration=Tbl_sample_status::
        where('status',$sample_name)->first();
        if(count($sampleregistration)==1){
            return response()->json(
                ['msg'=>$sample_name ."  Exists....",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_sample_status::create(array('status'=>$sample_name));

            return "";
            return response()->json(
                ['msg'=>$sample_name ." Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //getsample_status
    public function getsample_status()
    {
        return Tbl_sample_status::get();
    }

    //sample_status_update
    public function sample_status_update(Request $request)
    {
        $sample_status=$request['status'];
        $id=$request['id'];
        $sample_status_updates=Tbl_sample_status::where('id',$id)->update($request->all());
        if(count($sample_status_updates)==1){
            return response()->json(
                ['msg'=>$sample_status ." Updated....",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_sample_status::create(array('status'=>$sample_status_updates));

            return "";
            return response()->json(
                ['msg'=>$sample_status ." Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //sample_status_delete
    public function sample_status_delete($id)
    {

        return $sample_status_delete=Tbl_sample_status::destroy($id);

        if(count($sample_status_delete)==1){
            return response()->json(
                ['msgs'=>$sample_status_delete ." Not Deleted",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_sample_status::create(array('status'=>$sample_status_delete));
            return "";
            return response()->json(
                ['msgs'=>$sample_status_delete ." Successful Deleted",
                    'status'=>1
                ]
            )  ;
        }
    }


    //Register Equipment Status
    public function equipment_status_registration(Request $request)
    {
        $equipmentstatus=$request['status_name'];
        //$equipmentstatus;
        $equipment=Tbl_equipment_status::
        where('status_name',$equipmentstatus)->first();
        if(count($equipment)==1){
            return response()->json(
                ['msg'=>$equipmentstatus ."  Exists....",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_equipment_status::create(array('status_name'=>$equipmentstatus));

            return "";
            return response()->json(
                ['msg'=>$equipmentstatus ." Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }


    //Get Equipment Status
    public function getEquipementStatus()
    {
        return Tbl_equipment_status::get();
    }


    //Get Equipment Status
    public function getSampleStatus()
    {
        return Tbl_sample_status::where('eraser',1)->get();
    }

    //Generate Sample Number
    public function generateSampleNumber(Request $request){
        $request_number=$request->request_id;
        $visit_date_id=$request->visit_date_id;
        $facility_id=$request->facility_id;
        $receiver_id=$request->order_validator_id;
        $order_control=$request->order_control;
        $last_name=$request->last_name;
        $sub_department_name=$request->sub_department_name;
        $test_name=$request->test_name;
        $sample_type=$request->sample_type;
        if(!isset($request->sample_type)){
            return response()->json(
                ['data' => 'Please Enter Sample Type',
                    'status' => 0
                ]
            );

        }

        $sample_number=patientRegistration::labOrderNumber($facility_id);
        $Barcodes = new DNS1D();
        $sample_number_barcode=ltrim($sample_number, '0');
        $sample_number_barcode=(float)$sample_number_barcode;
        //$barrcode=$Barcodes->getBarcodeHTML($sample_number_barcode,"EAN13");
        //$barrcode=$Barcodes->getBarcodeHTML($sample_number_barcode,"C39");
        //$barrcode=$Barcodes->getBarcodePNG($sample_number_barcode,"C39");
        $barrcode=$Barcodes->getBarcodePNG($sample_number_barcode,"C39");

        if(Tbl_order::where('id',$request_number)->where('visit_date_id', $visit_date_id)->UPDATE(array('order_control'=>$order_control,'order_status'=>1,'sample_no'=>$sample_number_barcode,'sample_types'=>$sample_type,'receiver_id'=>$receiver_id))) {
            
			$create_sample= Tbl_sample_number_control::create(['sample_no'=>$sample_number,'facility_id'=>$facility_id,'user_id'=>$receiver_id]);
            
			$timecreatedAt=$create_sample->created_at;
            $newData=$create_sample;
            
			$ord=Tbl_order::where('id',$request_number)->where('visit_date_id', $visit_date_id)->get();
            
			$patient=Tbl_request::where('id',$ord[0]->order_id)->where('visit_date_id', $visit_date_id)->get();
            $patient_id=$patient[0]->patient_id;
            $trackable_id=$newData->id;
            $user_id= $receiver_id;
            
			SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);


            return response()->json(
                ['sample_number' => $sample_number_barcode,
                    'barcode' => $barrcode,
                    'last_name' => substr($last_name, 0, 4),
                    'sub_department_name' => substr($sub_department_name, 0, 4),
                    'test_name' => substr($test_name, 0, 4),
                    'time_generated' => date("d-m-Y h:i:s", strtotime($timecreatedAt)),
                    'status' => 1
                ]
            );

        }
    }



    //Generate Sample Number
    public function sampleCancel(Request $request)  {
        $request_number=$request->request_id;
        $facility_id=$request->facility_id;
        $receiver_id=$request->order_validator_id;
        $order_control=$request->order_control;
        $last_name=$request->last_name;
        $sample_no=$request->sample_no;
        $reason=$request->reason;
        $sample_collector_id=$request->sample_collector_id;
        $sample_validator=$request->sample_validator;
        $message=$request->message;

        if(empty($reason)){
            return response()->json(
                [
                    'data' => 'Please Give reason for Cancelling Sample '.$last_name.'-'.$sample_no,
                    'last_name' => $last_name,
                    'status' => 0
                ]
            );
        }

        if(Tbl_order::where('order_id',$request_number)->where('sample_no',$sample_no)->UPDATE(array('order_cancel_reason'=>$reason,'order_control'=>$order_control,'order_status'=>3,'order_validator_id'=>$receiver_id))) {

            Tbl_notification::create(["receiver_id"=>$sample_collector_id,"sender_id"=>$receiver_id,"message"=>$message]);
            return response()->json(
                [
                    'data' => $sample_no,
                    'last_name' => $last_name,
                    'status' => 1
                ]
            );
        }
    }

    //cancel lab results...
    public function resultsCancel(Request $request)  {
        $request_number=$request->request_id;
        $facility_id=$request->facility_id;
        $receiver_id=$request->order_validator_id;
        $order_control=$request->order_control;
        $last_name=$request->last_name;
        $sample_no=$request->sample_no;
        $reason=$request->cancel_reason;

        if(empty($reason)){
            return response()->json(
                [
                    'data' => 'Please Give reason for Cancelling Results '.$last_name.'-'.$sample_no,
                    'last_name' => $last_name,
                    'status' => 0
                ]
            );
        }

		$visit_date_id = Tbl_request::find($request_number)->visit_date_id;

        if(Tbl_result::where('order_id',$request_number)->where('visit_date_id', $visit_date_id)->UPDATE(array('cancel_reason'=>$reason,'confirmation_status'=>0,'verify_time'=>date('h:i:s'),'verify_user'=>$receiver_id))) {

            return response()->json(
                ['verify_user'=>$receiver_id,
                    'data' => $sample_no,
                    'last_name' => $last_name,
                    'status' => 1
                ]
            );
        }
    }


    public function saveNewDeviceStatus(Request $request)
    {
        if(Tbl_equipment::where('id',$request->id)->update(array('equipment_status_id'=>$request->equipment_status_id))){

            return response()->json(
                ['data'=>"Equipment Status",
                    'status'=>1
                ]);
        }
    }

    public function getLabTestPerMachine(Request $request)
    {
        if(isset($request->equipment_id)){
            $sql="SELECT t1.*,t3.equipment_name,t2.equipment_id,t2.item_id,t4.status_name,t2.on_off FROM tbl_items t1 
            INNER JOIN tbl_testspanels t2 ON t1.id=t2.item_id
            INNER JOIN tbl_equipments t3 ON t3.id=t2.equipment_id
            INNER JOIN tbl_equipment_statuses t4 ON t4.id=t3.equipment_status_id 
            WHERE t3.id='".$request->equipment_id."' AND t3.facility_id='".$request->facility_id."'";
        }
        else{
            $sql="SELECT t1.*,t3.equipment_name,t2.equipment_id,t2.item_id,t4.status_name,t2.on_off FROM tbl_items t1 
            INNER JOIN tbl_testspanels t2 ON t1.id=t2.item_id
            INNER JOIN tbl_equipments t3 ON t3.id=t2.equipment_id
            INNER JOIN tbl_equipment_statuses t4 ON t4.id=t3.equipment_status_id WHERE t3.facility_id='".$request->facility_id."'";
        }

        return DB::SELECT($sql);
    }

    public function getUnavailableTests(Request $request)
    {
        $start_date=$request->start_date;
        $end_date=$request->end_date;

        $sql="SELECT t1.*,t3.equipment_name,CONCAT(t7.first_name,' ',t7.middle_name,' ',t7.last_name) AS patient_name,t2.equipment_id,t2.item_id,t4.status_name,t6.name AS doctor_name,t5.created_at,t4.on_off FROM tbl_items t1 
INNER JOIN tbl_testspanels t2 ON t1.id=t2.item_id
INNER JOIN tbl_equipments t3 ON t3.id=t2.equipment_id
INNER JOIN tbl_equipment_statuses t4 ON t4.id=t3.equipment_status_id
INNER JOIN  tbl_unavailable_tests t5 ON t2.item_id=t5.item_id
INNER JOIN  users t6 ON t6.id=t5.user_id
INNER JOIN  tbl_patients t7 ON t7.id=t5.patient_id
WHERE t5.created_at BETWEEN '{$start_date}' AND '{$end_date}' AND t5.facility_id='".$request->facility_id."' GROUP BY t5.created_at";

        return DB::SELECT($sql);
    }

    //Get Equipments list
    public function getEquipementList()
    {
        return DB::table('vw_equipments')->where('eraser',0)
            //->where('on_off',1)
            ->get();
    }


    //Get Equipments list ID 2 =LABS
    public function getSubDepartments(){
        return Tbl_sub_department::where('department_id',2)->get();
    }

    public function setTestOff(Request $request){
        $equips=Tbl_equipment::where('id',$request->equipment_id)->get();
        $sub_department_id=$equips[0]->sub_department_id;
        $on_off=$request->switched;
        /*if($on_off==1){
            $equipment_status_id=5;
        }else{
            $equipment_status_id=2;
        }

        $off_equips=Tbl_equipment::where('sub_department_id',$sub_department_id)
            ->where('equipment_status_id',$equipment_status_id)->get();

        $new_equipment_id=$off_equips[0]->id;
        $dataSets=['equipment_id'=>$new_equipment_id];
        */
        Tbl_testspanel::where('item_id',$request->item_id)->where('equipment_id', $request->equipment_id)
            ->update(['on_off'=>$on_off]);

        return "Test was succesffuly turned off";
    }


    //Get Equipments list
    public function getTestPanel()
    {
        return Tbl_panel::where('erasor',0)-> get();
    }

    //Get Collected Sample 1=collected
    public function getCollectedSample()
    {
        $sql="SELECT 
        t1.patient_id,
        t1.visit_date_id,
        t2.order_id,
        t2.order_status,
        t2.order_control,
        t2.clinical_note,
        t2.id AS request_id,
        date(t1.created_at) as date_attended,
        t7.item_id,
        t8.account_number_id as account_number,
        t7.first_name,
        t7.middle_name,
        t7.last_name,
        t7.gender,
        t7.dob,
        t7.age,
        t7.medical_record_number,
        t7.mobile_number,
        t7.payment_filter,
        t7.status_id AS payment_status,
        t7.item_name,
        t7.sub_department_name,
        t7.sub_dept_id AS sub_department_id,
        t2.sample_no,
        t1.admission_id,
        CASE 
         WHEN t7.is_admitted THEN 'IPD'  ELSE 'OPD'  END as dept,        
        t2.created_at,
        t2.receiver_id AS sample_collector_id,
        t2.order_validator_id AS sample_validator,
      
        t7.facility_id
        
         FROM tbl_orders  t2
            INNER JOIN tbl_requests t1 ON t2.sample_no IS NOT NULL and t1.id=t2.order_id AND t1.visit_date_id = t2.visit_date_id
            INNER JOIN tbl_encounter_invoices t8 ON t1.visit_date_id = t8.account_number_id
            INNER JOIN tbl_invoice_lines t7 ON t7.invoice_id = t8.id and t7.item_id=t2.test_id  and (t7.status_id =2 OR t7.is_payable is not true)     
            INNER JOIN tbl_testspanels t16 ON t2.test_id = t16.item_id          
            INNER JOIN tbl_equipments t17 ON t17.id = t16.equipment_id and t17.sub_department_id = t7.sub_dept_id
       WHERE 
            (t2.order_status=1 OR t2.order_control=1)
        
        GROUP BY t2.order_id";


        return DB::SELECT($sql);
    }

    //Get Departments with Collected Samples
    public function getCollectedSampleDepartments($user_id) {

			$sql="SELECT sub_department_name, sub_department_id FROM patients_with_pending_sample_testing GROUP BY sub_department_id ORDER BY sub_department_name";

        return DB::SELECT($sql);


    }

    //Get Departments with Collected Samples
    public function getLabCollectedSample($sub_department_id){

        $sql="SELECT * FROM patients_with_pending_sample_testing WHERE sub_department_id='".$sub_department_id."' AND (timestampdiff(DAY,created_at,CURRENT_TIMESTAMP) <=(SELECT sum(days) FROM `tbl_lab_test_lives`)) order by priority desc";

        return DB::SELECT($sql);
    }


    //Get VERIFIED RESULTS
    public function getApprovedResults(){

        $sql="SELECT * FROM vw_approvedresults t1 WHERE  t1.confirmation_status=1 GROUP BY t1.sub_department_id ORDER BY t1.order_id DESC LIMIT 10";

        return DB::SELECT($sql);
        //return DB::table('vw_collectedSamples')->where('order_status',1)->orWhere('order_control',1)->where('sub_department_id',$sub_department_id)->get();
    }


    public function showResultsToVerify(request $request){
        $rs = [];
        $rs[]=DB::SELECT('SELECT * FROM vw_results_get_approves t1 WHERE t1.panel IS  NULL'); //SINGLE TEST..
        $rs[]=DB::SELECT('SELECT * FROM vw_results_get_approves t1 WHERE t1.panel IS NOT NULL'); //PANEL TEST..

        return $rs;
    }
    
	public function saveEquipChanges(request $request){
        if(!is_numeric($request->equipement_id)){

            return response()->json(
                ['data'=>" You must select equipment name first",
                    'status'=>0
                ] );
        }
        Tbl_testspanel::where('item_id',$request->item_id)->update(['equipment_id'=>$request->equipement_id]);
        return response()->json(
            ['data'=>" You have succesffuly changed the equipment for this test",
                'status'=>1
            ] );

    }

    public function saveComponentsResults(request $request){

        $components =[];
        $data = $request->all();
        $i=0;
        foreach ($data as $componentValue) {
            $visit_date_id = Tbl_request::find($componentValue['order_id'])->visit_date_id;
			$componentValue["visit_date_id"] = $visit_date_id;	
			$result = Tbl_panel_components_result::create($componentValue);
            if($i==0) {
				$result_create = Tbl_result::create(['order_id' => $componentValue['order_id'], 'visit_date_id' => $visit_date_id, 'post_time' => DB::Raw("CURRENT_TIME"), 'post_user' => $componentValue['user_id'], 'panel' => $componentValue['item_id'], 'description' => $componentValue['panel_name']]);
                $result_order = Tbl_order::Where('order_id', $componentValue['order_id'])->where('visit_date_id', $visit_date_id)->update(['order_control' => 2, 'order_status' => 2]);
            }
            $i++;
        }
        if ($result) {
            return response()->json(
                ['msg'=>" SAVED",
                    'status'=>201,
                    'data'=>$components
                ] );
        }
    }

    public function approveComponentsResults(request $request){

        $components =[];
        $data = $request->all();

        foreach ($data as $componentValue) {
             $visit_date_id = Tbl_request::find($componentValue['order_id'])->visit_date_id;
			 $result_create = Tbl_result::where('order_id',$componentValue['order_id'])->where('visit_date_id' , $visit_date_id)->update(['post_time'=>DB::Raw("CURRENT_TIME"),'verify_user'=>$componentValue['user_id'],'confirmation_status'=>1,'description'=>$componentValue['panel_name']]);
        }




        if ($result_create) {
            return response()->json(
                ['msg'=>" SAVED",
                    'status'=>201,
                    'data'=>$components
                ] );
        }
    }



    public function getPanelComponets(request $request){
        $order_id=$request->order_id;
        $panel_name=$request->panel_name;
        $sql="SELECT DISTINCT t2.id,t1.item_name AS panel,t2.panel_compoent_name,t1.sample_no,t1.item_id,t1.order_id,t2.minimum_limit,t2.maximum_limit,t2.si_units FROM vw_collectedSamples t1

    INNER JOIN tbl_testspanels t2 ON t1.item_id=t2.item_id WHERE t1.item_name='".$panel_name."' AND (order_status=1 OR order_control=1) AND t1.order_id='".$order_id."'";
        return DB::SELECT($sql);
    }


    public function getPanelComponetsResults(request $request){
        $order_id=$request->order_id;
        $item_id=$request->item_id;
        $panel_name=$request->panel_name;
        $sample_no=$request->sample_no;
        
		$sql="SELECT t1.component_name_value,t1.component_name,t1.sample_no,t1.item_id,t1.order_id,t1.minimum_limit,t1.maximum_limit,t1.si_units,panel_name AS panel FROM tbl_panel_components_results t1
              INNER JOIN tbl_panels t2 ON t1.item_id=t2.item_id WHERE  t1.order_id='".$order_id."'";


        return DB::SELECT($sql);
    }

    //Get Departments with Collected Samples
    public function getLabCollectedSamplePerOrderNumber(Request $request){
        $order_id = $request->order_id;
        $item_id = $request->item_id;
        $responses=[];

        $sql="SELECT * FROM vw_collectedSamples t1 WHERE t1.item_id NOT IN(SELECT t2.item_id FROM tbl_panels t2) AND  (order_status=1 OR order_control=1) AND t1.order_id='".$order_id."' AND t1.item_id='".$item_id."' GROUP BY t1.order_id";

        $sql_1="SELECT DISTINCT t1.item_name AS panel,t1.order_id FROM vw_collectedSamples t1

    INNER JOIN tbl_testspanels t2 ON t1.item_id=t2.item_id
     INNER JOIN tbl_panels t3 ON t3.item_id=t2.item_id WHERE (order_status=1 OR order_control=1)AND t1.item_id='".$item_id."' AND t1.order_id='".$order_id."'";

        $sql_2="SELECT * FROM vw_collectedSamples t1

    WHERE (order_status=1 OR order_control=1) AND t1.item_id='".$item_id."' AND t1.order_id='".$order_id."' Group by t1.order_id";


        $sql_3="SELECT Distinct t1.description,t2.status FROM tbl_diagnosis_descriptions t1 join tbl_diagnosis_details t2 on t1.id = t2.diagnosis_description_id join tbl_diagnoses t3 on t3.id = t2.diagnosis_id join tbl_requests t4 on t4.visit_date_id = t3.visit_date_id and t4.id = $order_id WHERE lower(t2.status) <> 'confirmed'";


        $responses[]= DB::SELECT($sql);
        $responses[]= DB::SELECT($sql_1);
        $responses[]= DB::SELECT($sql_2);
        $responses[]= DB::SELECT($sql_3);



        return $responses;
    }


//Get Departments with Collected Samples
    public function getLabCollectedSamplePerSampleNumber($sample_number){
        $responses=[];

        $sql="SELECT * FROM vw_collectedSamples t1 WHERE t1.item_id NOT IN(SELECT t2.item_id FROM tbl_panels t2) AND  (order_status=1 OR order_control=1) AND t1.sample_no='".$sample_number."' GROUP BY t1.order_id";

        $sql_1="SELECT DISTINCT t1.item_name AS panel,t1.order_id FROM vw_collectedSamples t1

    INNER JOIN tbl_testspanels t2 ON t1.item_id=t2.item_id
     INNER JOIN tbl_panels t3 ON t3.item_id=t2.item_id WHERE (order_status=1 OR order_control=1) AND t1.sample_no='".$sample_number."'";

        $sql_2="SELECT * FROM vw_collectedSamples t1

    WHERE (order_status=1 OR order_control=1) AND t1.sample_no='".$sample_number."' Group by t1.order_id";


        $responses[]= DB::SELECT($sql);
        $responses[]= DB::SELECT($sql_1);
        $responses[]= DB::SELECT($sql_2);



        return $responses;
        //return DB::table('vw_collectedSamples')->where('order_status',1)->orWhere('order_control',1)->where('sub_department_id',$sub_department_id)->get();
    }


    //Get Departments with Collected Samples
    public function getLabResults(){

        $sql="SELECT * FROM `vw_collectedSamples` t1 WHERE (order_status=2 OR order_control=2)  GROUP BY t1.sub_department_id ORDER BY t1.sample_no DESC ";
        return DB::SELECT($sql);

    }

    public function requestInvestigations(Request $request){
        //$response=patientRegistration::labRequestAPI();
        //return;
        $visitInfos=[];
        if(!isset($request['PatientResources'])){
            return response()->json([
                'Message' => 'No appropriate resource type in this JSON',
                'statusCode' => '10232'
            ]);

        }

        if (!array_key_exists('tests', $request['PatientResources'][0])) {

            return response()->json([
                'Message' => 'Contruct appropriate json data format for this API',
                'statusCode' => '10231'
            ]);

        }

        $response=$request['PatientResources'][0]['tests'];
        $priority=$request['PatientResources'][0]['priority'];
        $clinicID=$request['PatientResources'][0]['clinicID'];
        $visitDetails=$request['PatientResources'][0]['visitDetails'][0];
        $visit_id=$visitDetails['visitID'];
        $senderID=strtolower($visitDetails['senderID']);
        // return $senderID;


        $sql="SELECT * FROM tbl_accounts_numbers t1 WHERE t1.id='".$visit_id."'
                  AND  (timestampdiff(hour,t1.created_at,CURRENT_TIMESTAMP))<= 24";
        $visitInfos[]=DB::SELECT($sql);
        $visitInfos[]=Tbl_accounts_number::where('id',$visit_id)->get();


        $sqlPatientCategory="SELECT * FROM tbl_bills_categories t1 WHERE t1.account_id='".$visit_id."'";
        $visitInfos[]=DB::SELECT($sqlPatientCategory);

        $users="SELECT * FROM users t1 WHERE t1.id='".$senderID."'";
        $visitInfos[]=DB::SELECT($users);


        if(count($visitInfos[3]) ==0 ){
            return response()->json([
                'Message' => 'Request sender not recognised in the system',
                'statusCode' => '10230'
            ]);
        }

        if(count($visitInfos[1]) ==0 ){
            return response()->json([
                'Message' => 'No visit registered for this client,wrong Identifier ',
                'statusCode' => '10225'
            ]);
        }
        else if(count($visitInfos[1]) > 0 AND count($visitInfos[0])==0 ){

            return response()->json([
                'Message' => 'Patient Visit closed due to time factor ,request for openong new visit',
                'statusCode' => '10226'
            ]);
        }
        $facility_id=$visitInfos[1][0]->facility_id;
        $patient_id=$visitInfos[1][0]->patient_id;
        $bill_id=$visitInfos[2][0]->bill_id;
        $main_category_id=$visitInfos[2][0]->main_category_id;

        // return $facility_id;
        foreach($response AS $data){
            $testID=$data['testID'];
            $clinicalNotes=$data['clinicalNotes'];
            $testCode=$data['testCode'];
            //return $testID;
            $mappeds="SELECT * FROM tbl_item_type_mappeds t1 WHERE t1.item_id='".$testID."'";
            $itemMapps=DB::SELECT($mappeds);
            //return count($itemMapps);

            $sqls="SELECT * FROM tbl_requests t1            
                    INNER JOIN tbl_orders  t2 ON t1.id=t2.order_id AND t1.visit_date_id = t2.visit_date_id 
                    WHERE t1.visit_date_id='".$visit_id."'
                    AND t2.test_id='".$testID."'                  
                    AND t2.sample_no IS NULL              
                  AND  (timestampdiff(hour,t1.created_at,CURRENT_TIMESTAMP))<= 24";
            $checkIFOrdered=DB::SELECT($sqls);

            $prices="SELECT id FROM tbl_item_prices t1 WHERE t1.item_id='".$testID."'  AND t1.sub_category_id='".$bill_id."'";
            $itemPrices=DB::SELECT($prices);

            if(count($itemMapps)==0){
                return response()->json([
                    'Message' => 'No such test item in system mapping',
                    'statusCode' => '10227'
                ]);
            }
            if(count($itemPrices)==0){
                return response()->json([
                    'Message' => 'Test Price not yet set for this services',
                    'statusCode' => '10228'
                ]);
            }

            $item_type_id=$itemMapps[0]->id;
            $price_id=$itemPrices[0]->id;


            if(count($checkIFOrdered) == 0){


                $InvstReq=Tbl_request::create(['doctor_id'=>$senderID, 'patient_id'=>$patient_id, 'requesting_department_id'=>$clinicID, 'visit_date_id'=>$visit_id]);
                $orderID=$InvstReq->id;
                //return $orderID;
                Tbl_order::create(['order_id'=>$orderID, 'priority'=>$priority, 'clinical_note'=>$clinicalNotes, 'test_id'=>$testID, 'visit_date_id'=>$visit_id]);

                $encounters=Tbl_encounter_invoice::create(['account_number_id'=>$visit_id,
                    'user_id'=>$senderID,
                    'facility_id'=>$facility_id

                ]);

                $encounter_id=$encounters->id;

                Tbl_invoice_line::create(['invoice_id'=>$encounter_id,'item_type_id'=>$item_type_id,'quantity'=>1,'item_price_id'=>$price_id,'user_id'=>$senderID,'patient_id'=>$patient_id,'status_id'=>1,'facility_id'=>$facility_id,'discount'=>0,"discount_by"=>$senderID,"payment_filter"=>$bill_id]);

                return response()->json([
                    'Message' => 'Request was succesffuly sent to Laboratory',
                    'statusCode' => '200'
                ]);




            }else{

                return response()->json([
                    'Message' => 'Request is still pending at the lab waiting processing sample,order not allowed for this test',
                    'statusCode' => '10229'
                ]);

            }


        }
    }

    public function requestDrugs(Request $request){

        $visitInfos=[];
        if (!array_key_exists('dosage', $request['PatientResources'][0])) {
            return response()->json([
                'Message' => 'Contruct appropriate json data format for this API',
                'statusCode' => '10231'
            ]);

        }
        $response=$request['PatientResources'][0]['dosage'];
        $visitDetails=$request['PatientResources'][0]['visitDetails'][0];
        $visit_id=$visitDetails['visitID'];
        $senderID=$visitDetails['senderID'];

        // return $response;


        $sql="SELECT * FROM tbl_accounts_numbers t1 WHERE t1.id='".$visit_id."'
                  AND  (timestampdiff(hour,t1.created_at,CURRENT_TIMESTAMP))<= 24";
        $visitInfos[]=DB::SELECT($sql);
        $visitInfos[]=Tbl_accounts_number::where('id',$visit_id)->get();


        $sqlPatientCategory="SELECT * FROM tbl_bills_categories t1 WHERE t1.account_id='".$visit_id."'";
        $visitInfos[]=DB::SELECT($sqlPatientCategory);

        // return 'hhh: '.$visitDetails;
        $users="SELECT * FROM users t1 WHERE t1.id='".$senderID."'";
        $visitInfos[]=DB::SELECT($users);


        if(count($visitInfos[3]) ==0 ){
            return response()->json([
                'Message' => 'Request Sender not recognised in the system',
                'statusCode' => '10230'
            ]);
        }
        if(count($visitInfos[1]) ==0 ){
            return response()->json([
                'Message' => 'No visit registered for this client,wrong Identifier ',
                'statusCode' => '10225'
            ]);
        }
        else if(count($visitInfos[1]) > 0 AND count($visitInfos[0])==0 ){

            return response()->json([
                'Message' => 'Patient Visit closed due to time factor ,request for openong new visit',
                'statusCode' => '10226'
            ]);
        }
        $facility_id=$visitInfos[1][0]->facility_id;
        $patient_id=$visitInfos[1][0]->patient_id;
        $bill_id=$visitInfos[2][0]->bill_id;
        $main_category_id=$visitInfos[2][0]->main_category_id;

        $countError=0;
        foreach($response AS $data){
            $drugID=$data['drugID'];
            $dosageInstructions=$data['dosageInstructions'];
            $msdCode=$data['msdCode'];
            $frequency=$data['frequency'];
            $dose=$data['dosage'];
            $duration=$data['duration'];
            $DrugName=$data['DrugName'];
            //return $testID;
            $mappeds="SELECT * FROM tbl_item_type_mappeds t1 WHERE t1.item_id='".$drugID."'";
            $itemMapps=DB::SELECT($mappeds);

            $sqls="SELECT * FROM  tbl_prescriptions t1          
                        WHERE t1.visit_id='".$visit_id."'
                        AND t1.item_id='".$drugID."'                  
                  AND  (timestampdiff(hour,t1.created_at,CURRENT_TIMESTAMP))<= 24";
            $checkIFOrdered=DB::SELECT($sqls);

            $patient_infos["resourceType"]="Patient";

            $countError++;
            $prices="SELECT id FROM tbl_item_prices t1 WHERE t1.item_id='".$drugID."'  AND t1.sub_category_id='".$bill_id."'";
            $itemPrices=DB::SELECT($prices);

            if(count($itemMapps)==0){
                return response()->json([
                    'Message' => 'No such drug item in system mapping',
                    'statusCode' => '10227'
                ]);
            }
            if(count($itemPrices)==0){
                return response()->json([
                    'Message' => 'drug Price not yet set for this services',
                    'statusCode' => '10228'
                ]);
            }

            $item_type_id=$itemMapps[0]->id;
            $price_id=$itemPrices[0]->id;



            $dt_arr=['prescriber_id'=>$senderID,'patient_id'=>$patient_id,'item_id'=>$drugID,'visit_id'=>$visit_id,'frequency'=>$frequency,'duration'=>$duration,'dose'=>$dose,'start_date'=>date('Y-m-d'),'instruction'=>$dosageInstructions];


            if(count($checkIFOrdered) == 0){
                $InvstReq=Tbl_prescription::create($dt_arr);


                return response()->json([
                    'Message' => 'Request was succesffuly sent to Dispenser',
                    'statusCode' => '200'
                ]);
            }else{

                return response()->json([
                    'Message' => 'Request is still pending at the Dispensing window,waiting approving,order not allowed for this drug within 24 hours',
                    'statusCode' => '10229'
                ]);
            }





        }



    }



    public function getsampleReport(Request $request){

        $sql="SELECT * FROM `vw_collectedSamples` t1 WHERE  created_at BETWEEN '".$request->start_date."'  AND '".$request->end_date."'  ORDER BY t1.sample_no DESC ";
        return DB::SELECT($sql);

    }

    public function rePrintResults(Request $request){
$this->runViews1();
        $sql="SELECT * FROM `vw_getSampleReports` t1 WHERE  created_at BETWEEN '".$request->start_date."'  AND '".$request->end_date."'  ORDER BY t1.sample_no DESC ";
        return DB::SELECT($sql);

    }

    public function getPermanceAtLab(Request $request){

        if(!isset($request->post_user)){
            $sql="SELECT count(*) AS test_number,posted_by ,post_user FROM `vw_getSampleReports` t1 WHERE  created_at BETWEEN '".$request->start_date."'  AND '".$request->end_date."' GROUP BY post_user ORDER BY t1.sample_no DESC ";
        }else{
            $sql="SELECT count(*) AS test_number,posted_by ,post_user FROM `vw_getSampleReports` t1
        WHERE post_user='".$request->post_user."' AND  created_at BETWEEN '".$request->start_date."'  AND '".$request->end_date."' GROUP BY post_user ORDER BY t1.sample_no DESC ";
        }




        return DB::SELECT($sql);

    }

    //Get results awaiting for validation ..
    public function validateLabResults(){
        $sql="SELECT * FROM `vw_testresults` t1 WHERE t1.order_status=2   GROUP BY t1.sub_department_id ORDER BY t1.order_id DESC ";
        return DB::SELECT($sql);
    }


    //Get results awaiting for validation per test order ..
    public function validateLabResultsPerOrder($sub_department_id){
        $sql="SELECT * FROM `vw_testresults` t1 WHERE t1.order_status= 2 AND t1.sub_department_id= '".$sub_department_id."' GROUP BY t1.order_id,t1.visit_date_id  ORDER BY t1.order_id ASC LIMIT 10";
        return DB::SELECT($sql);
    }


    public function uploadLabResults(Request $request){
        // return $request->all();
        $date = date('Y-m-d h:i:s');
        $status = 0;
        $file =0;
        $user=0;
        //echo Input::hasFile($file);
        while (Input::hasFile($file)) {
            $destinationPath = 'labresults'; // upload path
            $fileName =  $request->sample_no.'.pdf'; // renameing image
            if(Input::file($file)->move($destinationPath, $fileName)){




            } else{

                return response("UNABLE TO UPLOAD FILE");

            }// uploading file to given path
            $file++;
        }
		
		$results = $request->all();
		$visit_date_id = Tbl_request::find($results->order_id)->visit_date_id;
		$results["visit_date_id"] = $visit_date_id;
		
        $admin = Tbl_result::create($request->all());
        if(!$admin->save()){
            return response("Error Encounted: Failed to save", 101);

        }
        Tbl_order::where('order_id',$request->order_id)->where('visit_date_id', $visit_date_id)->where('test_id',$request->item_id)
            ->update([
                'processor_id'=> $request->post_user,
                'order_control'=> 2,
                'order_status'=> 2
            ]);

        return response("RESULTS  SUCCESSFULLY SAVED.");
    }


    //Get results awaiting for validation per test request ..
    public function validateLabResultsPerRequest(Request $request){
        $order_id = $request->order_id;
        $item_id = $request->item_id;
        $rs = [];
        $rs[]=DB::SELECT("SELECT * FROM vw_results_get_approves t1 WHERE t1.panel IS  NULL AND t1.order_id= '".$order_id."' AND t1.item_id= '".$item_id."' GROUP BY t1.order_id  ORDER BY t1.order_id ASC LIMIT 10"); //SINGLE TEST..
        $rs[]=DB::SELECT("SELECT * FROM vw_results_get_approves t1 WHERE t1.panel IS NOT NULL AND t1.item_id= '".$item_id."' AND t1.order_id= '".$order_id."' GROUP BY t1.order_id  ORDER BY t1.order_id ASC LIMIT 10"); //PANEL TEST..
        $rs[]=DB::SELECT("SELECT * FROM vw_results_get_approves t1 WHERE  t1.order_id= '".$order_id."' AND t1.item_id= '".$item_id."' GROUP BY t1.order_id  ORDER BY t1.order_id ASC LIMIT 10"); //PATIENT INFO..



        $rs[]=DB::SELECT("SELECT Distinct t1.description,t2.status FROM tbl_diagnosis_descriptions t1 join tbl_diagnosis_details t2 on t1.id = t2.diagnosis_description_id join tbl_diagnoses t3 on t3.id = t2.diagnosis_id join tbl_requests t4 on t4.visit_date_id = t3.visit_date_id and t4.id = $order_id WHERE lower(t2.status) <> 'confirmed'");
        return $rs;
    }


    public function approveLabResult(Request $request){
        $results         = $request->results;
        $sample_no       = $request->sample_no;
        $order_control   = $request->order_control;
        $verified_by     = $request->verified_by;
        $order_id        =  $request->order_id;
        $ref_id        	 =  $request->ref_id;
        $item_id         =  $request->item_id;

		$visit_date_id = Tbl_request::find($order_id)->visit_date_id;
		
        Tbl_result::where('order_id', $order_id)->where('visit_date_id', $visit_date_id)
            ->where('item_id', $item_id)->update(['verify_time'=>date("h:i:s"),'verify_user'=>$verified_by,'confirmation_status'=>1
                ,'description'=>$results]);

        Tbl_order::where('test_id', $item_id)->where('order_id', $order_id)->where('visit_date_id', $visit_date_id)->update(['result_control'=>1]);

        $newData=Tbl_result::where('order_id', $order_id)->where('visit_date_id', $visit_date_id)->where('item_id', $item_id)->get();
        $patient=Tbl_request::where('id',$order_id)->get();
        $patient_id=$patient[0]->patient_id;
        $trackable_id=$newData[0]->id;
        $user_id= $newData[0]->verify_user;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);


        return response()->json(['data' => " RESULTS FOR " . $sample_no . " WERE SUCCESSFULY APRROVED .",
            'status' => 1
        ]);

    }

    //Get Collected Sample 3 =cancelled
    public function getCancelledSample()
    {
        // return DB::table('vw_collectedSamples')->where('order_status',3)->where('order_control',3)->groupBy('order_id')->get();
        $sql="SELECT 
        t1.patient_id,
        t1.visit_date_id,
        t2.order_id,
        t2.order_status,
        t2.order_control,
        t2.clinical_note,
        t2.id AS request_id,
        date(t1.created_at) as date_attended,
        t7.item_id,
        t8.account_number_id as account_number,
        t7.first_name,
        t7.middle_name,
        t7.last_name,
        t7.gender,
        t7.dob,
        t7.age,
        t7.medical_record_number,
        t7.mobile_number,
        t7.payment_filter,
        t7.status_id AS payment_status,
        t7.item_name,
        t7.sub_department_name,
        t7.sub_dept_id AS sub_department_id,
        t2.sample_no,
        t1.admission_id,
        CASE 
         WHEN t7.is_admitted THEN 'IPD'  ELSE 'OPD'  END as dept,        
        t2.created_at,
        t2.updated_at AS time_rejected,
        t2.order_cancel_reason,
        t7.facility_id
        
         FROM tbl_orders  t2
            INNER JOIN tbl_requests t1 ON t1.visit_date_id = t2.visit_date_id and t1.id=t2.order_id and t2.sample_no IS NOT NULL AND DATE(t1.created_at) = DATE(t2.created_at)
            INNER JOIN tbl_encounter_invoices t8 ON t1.visit_date_id = t8.account_number_id 
            INNER JOIN tbl_invoice_lines t7 ON t7.invoice_id = t8.id AND t7.item_id = t2.test_id and (t7.status_id = 2 or t7.is_payable is not true)      
            INNER JOIN tbl_testspanels t16 ON t7.item_id = t16.item_id          
            INNER JOIN tbl_equipments t17 ON t17.id = t16.equipment_id and t17.sub_department_id = t7.sub_dept_id
       WHERE 
            t2.order_status=3 
            AND t2.order_control=3
            GROUP BY t2.order_id";


        return DB::SELECT($sql);

    }

    //Get Equipment Status
    public function getLabDepartments() {
        return Tbl_sub_department::where('department_id',2)->get();
    }

    //Change Equipment Status
    public function changeEquipmentStatus(Request $request) {
        $status_id=2;
        if($request->on_off==1){
            $status_id=6;
        }
        return Tbl_equipment::where('id',$request->equipment_id)->update(['equipment_status_id'=>$status_id]);
    }

    //Get Lab Test ...
    public function LabTests()  {
        return DB::table('vw_labtests')->Where('eraser',0)->get();
    }
    //Get Lab Test with group by ORDER NO...
    public function LabTestRequest($facility_id)  {

        $sql="SELECT * FROM `patients_with_pending_sample_taking` WHERE  facility_id='".$facility_id."' AND timestampdiff(day, date_attended, current_time) <= (SELECT sum(days) FROM `tbl_lab_test_lives`) GROUP BY order_id order by priority desc";
        return DB::SELECT(DB::raw($sql));

    }

    //Get ADMISSION INFO PER BED/WARD...
    public function patientWardBed($admission_id)  {
        return DB::table('vw_patient_ward_beds')->Where('admission_id',$admission_id)->get();
    }


    //Get Lab Test FOR SPECIFIC  ORDER NO AND OTHER DETAILS TO POST TO MODAL...
    public function LabTestRequestPatient(Request $request)  {
        //get All panels tests


        $sql="SELECT * FROM `patients_with_pending_sample_taking` WHERE order_id='".$request->order_id."'";
        $sql1="SELECT * FROM `patients_with_pending_sample_taking` WHERE order_id='".$request->order_id."' LIMIT 1";
        $all[]=DB::SELECT($sql);
        $all[]=DB::SELECT($sql1);
        return $all;
    }

//Get Cancelled Test FOR SPECIFIC  ORDER NO AND OTHER DETAILS TO POST TO MODAL...
    public function getCanceledTest($order_id)  {
        // return DB::table('vw_collectedSamples')->where('order_status',3)->Where('order_id',$order_id)->groupBy('order_id')->get();


        // $sql="SELECT * FROM `vw_collectedSamples` t1 WHERE (t1.order_status=1 OR t1.order_control=1)";

        $sql="SELECT 
        t1.patient_id,
        t1.visit_date_id,
        t2.order_id,
        t2.order_status,
        t2.order_control,
        t2.clinical_note,
        t2.id AS request_id,
        date(t2.created_at) AS date_attended,
        t7.item_id,
        t8.account_number_id as account_number,
        t7.first_name,
        t7.middle_name,
        t7.last_name,
        t7.gender,
        t7.dob,
        t7.age,
        t7.medical_record_number,
        t7.mobile_number,
        t7.payment_filter,
        t7.status_id AS payment_status,
        t7.item_name,
        t7.sub_department_name,
        t7.sub_dept_id AS sub_department_id,
        t2.sample_no,
        t1.admission_id,
        CASE 
         WHEN t7.is_admitted THEN 'IPD'  ELSE 'OPD'  END as dept,        
        t2.created_at,
        t2.receiver_id AS sample_collector_id,
        t2.order_validator_id AS sample_validator,
        t7.facility_id
        
        FROM tbl_orders  t2
            INNER JOIN tbl_requests t1 ON t1.visit_date_id = t2.visit_date_id and t1.id=t2.order_id and t2.sample_no IS NOT NULL
            INNER JOIN tbl_encounter_invoices t8 ON t1.visit_date_id  = t8.account_number_id
            INNER JOIN tbl_invoice_lines t7 ON t7.invoice_id = t8.id and t7.item_id=t2.test_id  and (t7.status_id=2 or t7.is_payable is not true)      
            INNER JOIN tbl_testspanels t16 ON t7.item_id = t16.item_id          
            INNER JOIN tbl_equipments t17 ON t17.id = t16.equipment_id and t17.sub_department_id =t7.sub_dept_id         
        WHERE 
            t2.order_status=3
            AND t2.order_id='".$order_id."'
        GROUP BY t2.order_id";
        $sql1="  SELECT
            CONCAT(t1.first_name,' ',t1.middle_name,' ',t1.last_name) AS names,
            t1.mobile_number as mobile,
            t1.gender,
            t1.dob,
            t1.medical_record_number as mrn,
            t6.country_name as country,
            t5.residence_name as residence,
            t2.category_description as category,
            t7.id as order_id,
            t8.name as doctor_name

            FROM 


            tbl_patients t1
            JOIN tbl_accounts_numbers t2 on t1.id=t2.patient_id
            join tbl_residences t5 on t1.residence_id=t5.id
            left JOIN tbl_countries t6 on t6.id=t1.country_id
            JOIN tbl_requests t7 ON t7.visit_date_id=t2.id
            JOIN users t8 on t7.doctor_id=t8.id
            WHERE t7.id='".$order_id."'
            GROUP BY t7.visit_date_id";
        $all[]=DB::SELECT($sql);
        $all[]=DB::SELECT($sql1);
        return $all;
    }
	
    public function getUsersFromLab(Request $request){

        $sql="SELECT t1.*,t3.isAllowed,t4.id AS section_id,t4.sub_department_name FROM users t1
            INNER JOIN tbl_permission_users t2 ON t1.id=t2.user_id              
            LEFT JOIN tbl_staff_sections t3 ON t1.id=t3.technologist_id                 
            INNER JOIN tbl_sub_departments t4 ON t4.id=t3.section_id
            WHERE t2.permission_id=43               
            AND   t3.section_id='".$request->section_id."'";
        return DB::SELECT($sql);

    }

    public function saveLabTechnologists(Request $request){

        return Tbl_staff_section::create($request->all());

    }

    public function changeAccess(Request $request){
        $isAllowed=$request->isAllowed;
        return Tbl_staff_section::where('section_id',$request->section_id)
            ->where('technologist_id',$request->technologist_id)
            -> update(['isAllowed'=>$isAllowed]);
    }

    public function searchLabTechnologists(Request $request){
        $name = $request['keyWord'];

        $Technologists ="SELECT t1.* FROM users t1
            INNER JOIN tbl_permission_users t2 ON t1.id=t2.user_id              
            WHERE t2.permission_id=43
            AND name LIKE '%".$name."%'
            OR email LIKE '%".$name."%' group by t1.email LIMIT 15 ";

        return DB::SELECT($Technologists);

    }

    //update Equipment Status
    public function equipement_status_update(Request $request)
    {
        $id=$request['id'];
        $equipe_status_update=Tbl_equipment_status::where('id',$id)->update($request->all());
        if(count($equipe_status_update)==1){
            return response()->json(
                ['msg'=>$equipe_status_update ." Updated....",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_equipment_status::create(array('sample_status'=>$equipe_status_update));

            return "";
            return response()->json(
                ['msg'=>$equipe_status_update ." Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //Delete Equipment Status
    public function equipement_status_delete($id)
    {

        return $equipement_status=Tbl_equipment_status::destroy($id);

        if(count($equipement_status)==1){
            return response()->json(
                ['msgs'=>$equipement_status ." Not Deleted",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_equipment_status::create(array('status_name'=>$equipement_status));
            return "";
            return response()->json(
                ['msgs'=>$equipement_status ." Successful Deleted",
                    'status'=>1
                ]
            )  ;
        }
    }




    //Register Equipments
    public function addDevices(Request $request)
    {
        $equipment_name=$request['equipment_name'];
        $reagents=$request['reagents'];
        $sub_department_id=$request['sub_department_id'];
        $facility_id=$request['facility_id'];
        $user_id=$request['user_id'];
        $equipment_status_id=$request['equipment_status_id'];

        if(empty($equipment_name)){
            return response()->json(
                ['data'=>"Equipment name must be Filled ",
                    'status'=>0
                ]);

        }
        else  if(empty($equipment_status_id)){
            return response()->json(
                [   'data'=>"SET EQUIPMENT STATUS ",
                    'status'=>0
                ]);

        }
        else  if(!is_numeric($equipment_status_id)){
            return response()->json(
                [   'data'=>" You Must Select Sub Departments ",
                    'status'=>0
                ]);

        }
        else if(patientRegistration::duplicate('tbl_equipments',array('equipment_name','reagents',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($equipment_name,$reagents))==true){
            return response()->json(['data' =>$equipment_name.' With Reagent '.$reagents .' alredy exist',
                'status' =>0
            ]);
        }
        else{
            Tbl_equipment::create($request->all());
            return response()->json(
                ['data'=>$equipment_name.' With Reagent '.$reagents .' Successful Registered',
                    'status'=>1
                ]
            ) ;
        }
    }


    //Register Equipments
    public function addLabTest(Request $request)
    {
        $minimum_limit=null;
        $maximum_limit=null;
        $si_units=null;
        $equipment_id=$request['equipment_id'];
        $erasor=$request['erasor'];
        $panel_compoent_name=$request['panel_compoent_name'];
        if(isset($request['minimum_limit'])){
            $minimum_limit=$request['minimum_limit'];
            $maximum_limit=$request['maximum_limit'];
            $si_units=$request['si_units'];
        }

        $user_id=$request['user_id'];

        if(empty($equipment_id)){
            return response()->json(
                ['data'=>"Equipment used for this Test ",
                    'status'=>0
                ]);

        }
        else  if(empty($panel_compoent_name)){
            return response()->json(
                [   'data'=>"Enter Test Name ",
                    'status'=>0
                ]);

        }
        else if(patientRegistration::duplicate('tbl_items',array('item_name',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($panel_compoent_name))==true){
            return response()->json(['data' =>$panel_compoent_name.'  Alredy Exist',
                'status' =>0
            ]);
        }
        else{
            $item_added=Tbl_item::create(array("item_name"=>$panel_compoent_name,"dept_id"=>2));
            $item_id=$item_added->id;
            if(isset($item_id)){
                if(Tbl_item_type_mapped::create(array("unit_of_measure"=>1,"item_id"=>$item_id,"item_category"=>'TEST',"sub_item_category"=>'TEST'))) {

                    $item_added = Tbl_testspanel::create(array("erasor" =>$erasor,"si_units" => $si_units,"panel_compoent_name" => $panel_compoent_name,"item_id" => $item_id, "equipment_id" => $equipment_id,"minimum_limit" => $minimum_limit,"maximum_limit" => $maximum_limit,"user_id" =>$user_id));
                    return response()->json(
                        ['data'=>$panel_compoent_name.' Successful Registered',
                            'status'=>1
                        ]
                    ) ;
                }
            }


        }
    }


    //Register Equipments
    public function addLabTestPanel(Request $request)
    {

        $equipment_id=$request['equipment_id'];
        $item_id=$request['item_id'];
        $erasor=$request['erasor'];
        $panel_compoent_name=$request['panel_compoent_name'];
        $minimum_limit=$request['minimum_limit'];
        $maximum_limit=$request['maximum_limit'];
        $si_units=$request['si_units'];
        $user_id=$request['user_id'];
        $test_category=$request['test_category'];

        if(empty($equipment_id)){
            return response()->json(
                ['data'=>"Equipment used for this Test ",
                    'status'=>0
                ]);

        }
        else  if(empty($panel_compoent_name)){
            return response()->json(
                [   'data'=>"Enter Test Name ",
                    'status'=>0
                ]);

        }
        /**\NOT NECESSARY TO HAVE THIS FOR OTHER TESTS..
        else  if(empty($minimum_limit)){
        return response()->json(
        [   'data'=>" You Must Enter MINIMUM Limit",
        'status'=>0
        ]);

        }
        else  if(empty($maximum_limit)){
        return response()->json(
        [   'data'=>" You Must Enter MAXIMUM Limit",
        'status'=>0
        ]);

        }

        else  if(empty($si_units)){
        return response()->json(
        [   'data'=>" You Must Enter SI UNITS",
        'status'=>0
        ]);

        }
         * **/
        else if(patientRegistration::duplicate('tbl_testspanels',array('item_id','panel_compoent_name',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array(0,$panel_compoent_name))==true){
            return response()->json(['data' =>$panel_compoent_name.'  Alredy Exist',
                'status' =>0
            ]);
        }
        else{
            Tbl_testspanel::create(array("erasor" =>$erasor,"si_units" => $si_units,"panel_compoent_name" => $panel_compoent_name,"item_id" => $item_id, "equipment_id" => $equipment_id,"minimum_limit" => $minimum_limit,"maximum_limit" => $maximum_limit,"user_id" =>$user_id));
            return response()->json(
                ['data'=>$panel_compoent_name.' Successful Registered',
                    'status'=>1
                ]
            ) ;

        }
    }




//Get results awaiting for validation per test request ..
    public function validatedLabResultsPerRequest(Request $request){


        $order_id = $request->order_id;
        $item_id = $request->item_id;
        $rs = [];
        $rs[]=DB::SELECT("SELECT * FROM vw_results_get_approvedData t1 WHERE t1.panel IS  NULL AND t1.order_id= '".$order_id."' AND t1.item_id= '".$item_id."' GROUP BY t1.order_id  ORDER BY t1.order_id ASC LIMIT 10"); //SINGLE TEST..
        $rs[]=DB::SELECT("SELECT * FROM vw_results_get_approvedData t1 WHERE t1.panel IS NOT NULL AND t1.item_id= '".$item_id."' AND t1.order_id= '".$order_id."' GROUP BY t1.order_id  ORDER BY t1.order_id ASC LIMIT 10"); //PANEL TEST..
        $rs[]=DB::SELECT("SELECT * FROM vw_results_get_approvedData t1 WHERE  t1.order_id= '".$order_id."' AND t1.item_id= '".$item_id."' GROUP BY t1.order_id  ORDER BY t1.order_id ASC LIMIT 10"); //PATIENT INFO..

        return $rs;
    }
//Register Lab Panel Test..
    public function addLabPanelTest(Request $request)
    {
        $equipment_id=$request['equipment_id'];
        $item_id=$request['item_id'];
        $erasor=$request['erasor'];
        $panel_compoent_name=$request['panel_compoent_name'];
        $minimum_limit=$request['minimum_limit'];
        $maximum_limit=$request['maximum_limit'];
        $si_units=$request['si_units'];
        $user_id=$request['user_id'];

        if(empty($equipment_id)){
            return response()->json(
                ['data'=>"Equipment used for this Test ",
                    'status'=>0
                ]);

        }
        else  if(empty($panel_compoent_name)){
            return response()->json(
                [   'data'=>"Enter Test Name ",
                    'status'=>0
                ]);

        }
        else  if(empty($minimum_limit)){
            return response()->json(
                [   'data'=>" You Must Enter MINIMUM Limit",
                    'status'=>0
                ]);

        }
        else  if(empty($maximum_limit)){
            return response()->json(
                [   'data'=>" You Must Enter MAXIMUM Limit",
                    'status'=>0
                ]);

        }

        else  if(empty($si_units)){
            return response()->json(
                [   'data'=>" You Must Enter SI UNITS",
                    'status'=>0
                ]);

        }
        else if(patientRegistration::duplicate('tbl_items',array('item_name',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($panel_compoent_name))==true){
            return response()->json(['data' =>$panel_compoent_name.'  Alredy Exist',
                'status' =>0
            ]);
        }
        else{
            $item_added=Tbl_item::create(array("item_name"=>$panel_compoent_name,"dept_id"=>2));
            $item_id=$item_added->id;
            if(isset($item_id)){
                if(Tbl_item_type_mapped::create(array("unit_of_measure"=>1,"item_id"=>$item_id,"item_category"=>'TEST',"sub_item_category"=>'SINGLE TEST'))) {

                    $item_added = Tbl_testspanel::create(array("erasor" =>$erasor,"si_units" => $si_units,"panel_compoent_name" => $panel_compoent_name,"item_id" => $item_id, "equipment_id" => $equipment_id,"minimum_limit" => $minimum_limit,"maximum_limit" => $maximum_limit,"user_id" =>$user_id));
                    return response()->json(
                        ['data'=>$panel_compoent_name.' Successful Registered',
                            'status'=>1
                        ]
                    ) ;
                }
            }


        }
    }

    //Register Equipments
    public function addLabPanel(Request $request)
    {
        $equipment_id=$request['equipment_id'];
        $erasor=$request['erasor'];
        $panel_name=$request['panel_name'];
        $user_id=$request['user_id'];

        if(empty($equipment_id)){
            return response()->json(
                ['data'=>"Equipment used for this Test ",
                    'status'=>0
                ]);

        }
        else  if(empty($panel_name)){
            return response()->json(
                [   'data'=>"Enter Test Name ",
                    'status'=>0
                ]);

        }

        else if(patientRegistration::duplicate('tbl_items',array('item_name',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($panel_name))==true){
            return response()->json(['data' =>$panel_name.'  Alredy Exist',
                'status' =>0
            ]);
        }
        else{
            $item_added=Tbl_item::create(array("item_name"=>$panel_name,"dept_id"=>2));
            $item_id=$item_added->id;
            if(isset($item_id)){
                if(Tbl_item_type_mapped::create(array("unit_of_measure"=>1,"item_id"=>$item_id,"item_category"=>'TEST',"sub_item_category"=>'PANEL'))) {

                    $item_added = Tbl_panel::create(array("erasor" =>0,"panel_name" => $panel_name,"item_id" => $item_id, "equipment_id" => $equipment_id,"user_id" =>$user_id));
                    return response()->json(
                        ['data'=>$panel_name.' Successful Registered',
                            'status'=>1
                        ]
                    ) ;
                }
            }


        }
    }
    public function setDefaultMachine(Request $request){

        $machine_departments = array(
            ['id'=>1,'machine_name' => 'Haematology Machine Off'],
            ['id'=>2,'machine_name' => 'Microbiology Machine Off'],
            ['id'=>3,'machine_name'  =>'Clinical Chemistry Machine Off'],
            ['id'=>4,'machine_name' => 'Serology Machine Off'],
            ['id'=>5,'machine_name' => 'Parasitology Machine Off'],
            ['id'=>6,'machine_name' => 'TB Machine Off'],
            ['id'=>7,'machine_name' => 'Immunology Machine Off'],
            ['id'=>8,'machine_name' => 'Hormonal Profiles Machine Off'],
            ['id'=>9,'machine_name' => 'Cytology Machine Off'],
        );

        foreach($machine_departments AS $machine_department){

            $equipment_lists=Tbl_equipment::where('equipment_name',$machine_department['machine_name'])->get();
            if(count($equipment_lists)==0){

                $equipment=new Tbl_equipment(['equipment_name'=>$machine_department['machine_name'],'equipment_status_id'=>2,'facility_id'=>$request->facility_id,'user_id'=>$request->user_id,'sub_department_id'=>$machine_department['id'],'eraser'=>1]);
                // Auth::onceUsingId($request->user_id);
                $equipment->save();


            }

        }


    }

    public function quickLabSettings(Request $request){

        $machine_departments = array(
            ['id'=>1,'machine_name' => 'Haematology Machine'],
            ['id'=>2,'machine_name' => 'Microbiology Machine'],
            ['id'=>3,'machine_name'  =>'Clinical Chemistry Machine'],
            ['id'=>4,'machine_name' => 'Serology Machine'],
            ['id'=>5,'machine_name' => 'Parasitology Machine'],
            ['id'=>6,'machine_name' => 'TB Machine'],
            ['id'=>7,'machine_name' => 'Immunology Machine'],
            ['id'=>8,'machine_name' => 'Hormonal Profiles Machine'],
            ['id'=>9,'machine_name' => 'Cytology Machine'],
        );

        foreach($machine_departments AS $machine_department){

            $equipment=new Tbl_equipment(['equipment_name'=>$machine_department['machine_name'],'equipment_status_id'=>5,'facility_id'=>$request->facility_id,'user_id'=>$request->user_id,'sub_department_id'=>$machine_department['id'],'eraser'=>0]);
            //Auth::onceUsingId($request->user_id);
            $equipment->save();


        }


        $equipment_lists=Tbl_equipment::all();

        foreach($equipment_lists AS $equipment_list){
            $equipement_id=$equipment_list->id;
            $sub_department_id=$equipment_list->sub_department_id;

            $sql="SELECT t1.*,t2.item_name FROM tbl_item_sub_departments t1 
            INNER JOIN tbl_items t2 ON t1.item_id=t2.id 
            WHERE t1.sub_dept_id='".$sub_department_id."' AND t2.dept_id=2 GROUP BY t2.id";
            $sub_dept_items=DB::SELECT($sql);


            foreach($sub_dept_items AS $sub_dept_item){
                $test_name=$sub_dept_item->item_name;
                $testItem=new Tbl_testspanel(['item_id'=>$sub_dept_item->item_id,'equipment_status_id'=>$machine_department['id'],'facility_id'=>$request->facility_id,'user_id'=>$request->user_id,'equipment_id'=>$equipement_id,'panel_compoent_name'=>$test_name,'eraser'=>1]);
                //  Auth::onceUsingId($request->user_id);
                $testItem->save();

            }



        }
        return response()->json(
            ['data'=>"Laboratory tests assigned to default machine",
                'status'=>1
            ]);


    }

    //Register Equipments
    public function addSingleTest(Request $request)
    {
        $equipment_id=$request['equipment_id'];
        $erasor=$request['erasor'];
        $test_name=$request['panel_name'];
        $user_id=$request['user_id'];

        if(empty($equipment_id)){
            return response()->json(
                ['data'=>"Equipment used for this Test ",
                    'status'=>0
                ]);

        }
        else  if(empty($test_name)){
            return response()->json(
                [   'data'=>"Enter Test Name ",
                    'status'=>0
                ]);

        }

        else if(patientRegistration::duplicate('tbl_items',array('item_name',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($test_name))==true){
            return response()->json(['data' =>$test_name.'  Alredy Exist',
                'status' =>0
            ]);
        }
        else{
            $item_added=Tbl_item::create(array("item_name"=>$test_name,"dept_id"=>2));
            $item_id=$item_added->id;
            if(Tbl_item_type_mapped::create(array("unit_of_measure"=>1,"item_id"=>$item_id,"item_category"=>'TEST',"sub_item_category"=>'PANEL'))) {

                $item_added = Tbl_testspanel::create(array("erasor" =>0,"panel_compoent_name" => $test_name,"item_id" => $item_id, "equipment_id" => $equipment_id,"user_id" =>$user_id));
                return response()->json(
                    ['data'=>$test_name.' Successful Registered',
                        'status'=>1
                    ]
                ) ;
            }



        }
    }


    //Get Equipment
    public function getequipement()
    {
        //return Tbl_equipment::get();
        return DB::table('tbl_equipments')
            ->select(
                'tbl_equipments.id as equipment_id','equipment_name',
                'reagents','minimum_limit','maximum_limit','unit',
                'status_name','facility_name'
            )
            ->join('tbl_equipment_statuses',"tbl_equipments.equipment_status_id","=","Tbl_equipment_statuses.id")
            ->join('tbl_facilities',"tbl_equipments.facility_id","=","tbl_facilities.id")
            ->join('tbl_units',"tbl_equipments.si_unit","=","tbl_units.id")
            ->get();

    }

    //update Equipment
    public function equipement_update(Request $request)
    {
        $equipment_name=$request['equipment_name'];
        $reagents=$request['reagents'];
        $minimum_limit=$request['minimum_limit'];
        $maximum_limit=$request['maximum_limit'];
        $si_unit=$request['si_unit'];
        $id=$request['id'];

        $equipment_update=Tbl_equipment::
        where('id',$id)->update($request->all());
        if(count($equipment_update)==1){
            return response()->json(
                ['msg'=>$equipment_update ."  Exists....",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_equipment::create(array('equipment_name'=>$equipment_update));
            return response()->json(
                ['msg'=>$equipment_update ." Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //Delete Equipment
    public function equipement_delete($id)
    {

        return $equipementstatus=Tbl_equipment::destroy($id);

        if(count($equipementstatus)==1){
            return response()->json(
                ['msgs'=>$equipementstatus ." Not Deleted",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_equipment_status::create(array('status_name'=>$equipementstatus));

            return response()->json(
                ['msgs'=>$equipementstatus ." Successful Deleted",
                    'status'=>1
                ]
            )  ;
        }
    }


    //Register Sub Department
    public function sub_department_registration(Request $request)
    {
        $sub_department_name=$request['sub_department'];
        $department_id=$request['id'];


        $sub_department=Tbl_sub_department::where('sub_department_name',$sub_department_name)
            ->where('department_id',$department_id)
            ->first();
        if(count($sub_department)==1){
            return response()->json(
                ['msg'=>$sub_department_name."  Exists....",
                    'status'=>0
                ]
            );
        }
        else{
            $data=Tbl_sub_department::create(array(
                'sub_department_name'=>$sub_department_name,
                'department_id'=>$department_id
            ));

            return response()->json(
                ['msg'=>$sub_department_name." Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //update Sub Department
    public function sub_department_update(Request $request)
    {
        $sub_department_name=$request['sub_department_name'];
        $department_id=$request['deptmnts'];
        $sub_department_id=$request['sub_department_id'];
        $sub_department_update=Tbl_sub_department::where('id',$sub_department_id)
            ->update(['sub_department_name'=>$sub_department_name,'department_id'=>$department_id
            ]);
        if(count($sub_department_update)==1){
            return response()->json(
                ['msg'=>$sub_department_name."  Exists....",'status'=>0]);
        }
        else{
            $data=Tbl_sub_department::create(array
            (
                'sub_department_name'=>$sub_department_name,
                'department_id'=>$department_id
            ));
            return response()->json(
                ['msg'=>$sub_department_name ." Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //Delete Sub Department
    public function sub_department_delete($id)
    {
        //$id=$request['id'];
        return $subdepartmenttstatus=Tbl_sub_department::destroy($id);

        if(count($subdepartmenttstatus)==1){
            return response()->json(
                ['msgs'=>$subdepartmenttstatus ." Not Deleted",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_sub_department::create(array('status_name'=>$equipementstatus));

            return response()->json(
                ['msgs'=>$subdepartmenttstatus ." Successful Deleted",
                    'status'=>1
                ]
            )  ;
        }
    }


    //Register lab_test_registration
    public function lab_test_registration(Request $request)
    {
        $item_name=$request['item_name'];
        $item_id=$request['item_id'];
        $item_test_range=$request['item_test_range'];
        $unit=$request['unit'];
        $item_test_indicator=$request['item_test_indicator'];
        $sample_to_collect=$request['sample_to_collect'];
        $sub_department_id=$request['sub_department_id'];
        $equipment_id=$request['equipment_id'];
        $lab_test=Tbl_lab_test_element::
        where('item_id',$item_id)
            ->first();
        if(count($lab_test)==1){
            return response()->json(
                ['msg'=>$item_name."  Exists....",
                    'status'=>0
                ]
            );
        }
        else{
            $data=Tbl_lab_test_element::create(array(
                'item_id'=>$item_id,
                'item_test_range'=>$item_test_range,
                'units'=>$unit,
                'item_test_indicator'=>$item_test_indicator,
                'sample_to_collect'=>$sample_to_collect,
                'sub_department_id'=>$sub_department_id,
                'equipment_id'=>$equipment_id
            ));
            return response()->json(
                ['msg'=>$item_name." Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }


    //Get lab_test_registration
    public function get_lab_test()
    {
        return DB::table('tbl_tests')
            ->select(
                'tbl_tests.id as test_id','item_test_range',
                'unit','item_name','indicator','Tbl_lab_sample_to_collects.sample_to_collect',
                'equipment_name','sub_department_name','status_name'
            )
            ->join('tbl_items',"tbl_tests.item_id","=","tbl_items.id")
            ->join('tbl_units',"tbl_tests.units","=","tbl_units.id")
            ->join('tbl_lab_test_indicators',"tbl_tests.item_test_indicator","=","tbl_lab_test_indicators.id")
            ->join('Tbl_lab_sample_to_collects',"tbl_tests.sample_to_collect","=","Tbl_lab_sample_to_collects.id")
            ->join('Tbl_sub_departments',"tbl_tests.sub_department_id","=","Tbl_sub_departments.id")
            ->join('Tbl_equipments',"tbl_tests.equipment_id","=","Tbl_equipments.id")
            ->join('tbl_equipment_statuses',"Tbl_equipments.equipment_status_id","=","tbl_equipment_statuses.id")
            ->get();
    }


    //Get Patient
    public function getpatient(Request $request)

    {
        $search=$request['SearchValue'];
        return Tbl_patient::where('id','like','%'.$search.'%')->get();
    }


//Get Panels
    public function getPanels($searchKey){

        $panels=DB::table('tbl_panels')
            ->where('panel_name','like','%'.$searchKey.'%')
            ->get();
        return $panels;

    }



    //Get Lab Test Details
    public function getservice(Request $request)

    {
        $search=$request['SearchItem'];
        //return $search;
        return Tbl_item::where('item_name','like','%'.$search.'%')->get();
    }

    //Report results electronically...
    public function reportElectonically(Request $request){
        $foliolist_array=array();
        $patient_infos=array();
        $tests=array();
        $identifications=array();

        $entity_array =array();

        $patient_infos['identifier']=array();

        $patient_infos["resourceType"]="Investigation Report";
        $identifications['identifierSourceUuid']=$request->report['patient_id'];
        $identifications['value']=$request->report['account_id'];
        array_push($patient_infos['identifier'],$identifications);


        $patient_infos['test']=array();
        $tests["use"]="Tests";
        $tests["item_id"]=$request->report['item_id'];
        $tests["item_name"]=$request->report['item_name'];
        $tests["results"]=$request->report['description'];
        $tests["verifiedBy"]=$request->report['verify_user'];
        array_push($patient_infos['test'],$tests);


        array_push($foliolist_array,$patient_infos);
        $entity_array["PatientResources"]=$foliolist_array;
        $data_string=json_encode($entity_array,JSON_PRETTY_PRINT);

        $intergratingKeys=Tbl_integrating_key::where('api_type',9)->where('active',1)->get();

        $record_returned= count($intergratingKeys);

        if($record_returned >0){
            $base_urls=$intergratingKeys[0]->base_urls;
            $private_keys=$intergratingKeys[0]->private_keys;
            $public_keys=$intergratingKeys[0]->public_keys;
            $active=$intergratingKeys[0]->active;

            $request_method = 'POST';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $base_urls);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/json'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response  = curl_exec($ch);
            $StatusCode=  curl_getinfo($ch,CURLINFO_HTTP_CODE);

            curl_close($ch);
            return $response;
        }
        else{

            return response()->json(
                ['data'=>"Remote API Address not found in the system",
                    'status'=>0
                ]
            );
        }

    }



    public function reportResultsRemotely(Request $request){
        if(isset($request->account_id)){
            $sql= "select * from vw_getSampleReports where account_id = '".$request->account_id."'";
        }else if(isset($request->medical_record_number)){
            $sql= "select * from vw_getSampleReports where medical_record_number = '".$request->medical_record_number."'";

        }
        else if(isset($request->start_date)){
            $sql= "select * from vw_getSampleReports where created_at BETWEEN '".$request->start_date."' AND '".$request->end_date."'";

        }


        else{
            $sql= "select * from vw_getSampleReports";
        }
$sql="SELECT t1.*,t2.sample_no,t4.item_name,t4.dept_id,
        CONCAT(t5.first_name,' ',t5.middle_name,' ',t5.last_name) AS full_name,
        t5.mobile_number,
t5.gender,
        t2.sample_types,
        t2.clinical_note,
        DATE(t3.created_at) AS date_requested,
        t5.medical_record_number,
        (SELECT name FROM users t10 WHERE t10.id=t1.post_user GROUP BY t10.id) AS posted_by,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END AS age,
         CASE 
         WHEN t5.residence_id IS NOT NULL THEN (SELECT CONCAT(residence_name,' ',council_name) FROM tbl_residences t6 INNER JOIN tbl_councils t7 ON t6.council_id=t7.id WHERE t6.id=t5.residence_id GROUP BY t6.id) END AS residence_name,       
         CASE 
         WHEN t3.doctor_id IS NOT NULL THEN (SELECT name FROM users t8  WHERE t8.id=t3.doctor_id GROUP BY t8.name) END AS doctor_name,
         CASE 
         WHEN t3.doctor_id IS NOT NULL THEN (SELECT t8.mobile_number  FROM users t8  WHERE t8.id=t3.doctor_id GROUP BY t8.mobile_number) END AS doctor_mobile_number    ,
         CASE 
         WHEN t3.requesting_department_id IS NOT NULL THEN (SELECT department_name  FROM tbl_departments t9  WHERE t3.requesting_department_id=t9.id GROUP BY t9.department_name) END AS requesting_department  ,
         CASE 
         WHEN t2.sample_no IS NOT NULL THEN (SELECT DATE(t10.created_at)  FROM tbl_sample_number_controls t10  
         WHERE  TRIM(LEADING '0' FROM t10.sample_no)=t2.sample_no GROUP BY t10.created_at LIMIT 1) END AS date_collected,
         CASE 
         WHEN t2.sample_no IS NOT NULL THEN (SELECT TIME(t10.created_at)  FROM tbl_sample_number_controls t10 
         
         WHERE  TRIM(LEADING '0' FROM t10.sample_no)=t2.sample_no GROUP BY t10.created_at LIMIT 1) END AS time_collected     ,
         CASE 
         WHEN t2.sample_no IS NOT NULL THEN (SELECT name  FROM users t11
         INNER JOIN  tbl_sample_number_controls t10  ON t10.user_id=t11.id
         WHERE t2.receiver_id=t10.user_id GROUP BY t2.receiver_id LIMIT 1) END AS collected_by,
         
         CASE 
         WHEN t1.post_user IS NOT NULL THEN (SELECT  name   FROM users t11
          
         WHERE t1.post_user=t11.id GROUP BY t1.post_user LIMIT 1) END AS performedBy,
         
         CASE 
         WHEN t1.verify_user IS NOT NULL THEN (SELECT  name  FROM users t11
          
         WHERE t1.verify_user=t11.id GROUP BY t1.verify_user LIMIT 1) END AS verifiedBy
         
        FROM tbl_results t1 
        INNER JOIN tbl_requests t3 ON t3.id = t1.order_id
        INNER JOIN tbl_orders t2 ON t1.item_id = t2.test_id AND DATE(t3.created_at) = DATE(t2.created_at)
        INNER JOIN tbl_items t4 ON t4.id = t1.item_id
        INNER JOIN tbl_patients t5 ON t5.id = t3.patient_id
        WHERE t4.dept_id = 2
           AND t2.order_id=t3.id 
           AND t1.created_at BETWEEN '".$request->start_date."' AND '".$request->end_date."'
          group by t1.item_id,t1.order_id,t1.id";

        $refferals= DB::select(DB::raw($sql));


        return $refferals;

    }

    //Send Lab test to lab
    public function send_to_lab(Request $request)
    {
        $data=json_encode($request->all());
        $dec=json_decode($data);
        $doctor_id= $dec[0]->doctor_id;
        $dataa=Tbl_lab_request::create(array('doctor_id'=>$doctor_id));
        $order_id=$dataa->id;

        foreach($request->all() as $order){
            $patient_id=$order['patient_id'];
            $service_id=$order['id'];

            $orderTo=Tbl_lab_order::create([
                'patient_id'=>$patient_id,
                'lab_test_id'=>$service_id,
                'order_id'=>$order_id,
            ]);
        }
        return $orderTo;
    }

    public function get_lab_order()
    {
        return DB::table('tbl_orders')
            ->select('tbl_orders.id as tbl_order_id','tbl_patients.id as patient_id',
                'tbl_orders.order_id as orders_id','tbl_patients.*','tbl_tests.*','tbl_requests.*',
                'tbl_sub_departments.*','tbl_lab_sample_to_collects.sample_to_collect'
                ,'tbl_orders.test_id as test_id'
                ,'tbl_items.item_name as item_name')
            ->where('sample_no',"=",NULL)
            ->where(DB::Raw("tbl_requests.visit_date_id"), "=", DB::Raw("tbl_orders.visit_date_id"))
            ->join('tbl_requests',"tbl_requests.id","=","tbl_orders.order_id")
            ->join('tbl_patients',"tbl_patients.id","=","tbl_requests.patient_id")
            ->join('tbl_tests',"tbl_tests.id","=","tbl_orders.test_id")
            ->join('tbl_items',"tbl_tests.item_id","=","tbl_items.id")
            ->join('tbl_lab_sample_to_collects',"tbl_lab_sample_to_collects.id","=","tbl_tests.sample_to_collect")
            ->join('tbl_sub_departments',"tbl_sub_departments.id","=","tbl_tests.sub_department_id")
            ->get();
    }

    public function get_lab_order_collected()
    {
        return DB::table('tbl_orders')
            ->select('tbl_orders.id as tbl_order_id','tbl_patients.id as patient_id',
                'tbl_orders.order_id as orders_id','tbl_patients.*','tbl_tests.*','tbl_requests.*',
                'tbl_sub_departments.*','sample_no','tbl_lab_sample_to_collects.sample_to_collect'
                ,'tbl_orders.test_id as test_id','time_received','name'
                ,'tbl_items.item_name as item_name')
            ->where('sample_no',"!=",NULL)
            ->where(DB::Raw("tbl_requests.visit_date_id"), "=", DB::Raw("tbl_orders.visit_date_id"))
            ->join('tbl_requests',"tbl_requests.id","=","tbl_orders.order_id")
            ->join('tbl_patients',"tbl_patients.id","=","tbl_requests.patient_id")
            ->join('tbl_tests',"tbl_tests.id","=","tbl_orders.test_id")
            ->join('tbl_items',"tbl_tests.item_id","=","tbl_items.id")
            ->join('users',"users.id","=","tbl_orders.receiver_id")
            ->join('tbl_lab_sample_to_collects',"tbl_lab_sample_to_collects.id","=","tbl_tests.sample_to_collect")
            ->join('tbl_sub_departments',"tbl_sub_departments.id","=","tbl_tests.sub_department_id")
            ->get();
    }

    public function get_department()
    {
        return DB::table('tbl_departments')->get();

    }

    public function getsub_department()
    {
        return DB::table('tbl_sub_departments')
            ->select(
                'tbl_sub_departments.id as sub_department_id',
                'tbl_sub_departments.sub_department_name as sub_department_name',
                'tbl_departments.*')
            ->join('tbl_departments',"tbl_departments.id","=","tbl_sub_departments.department_id")
            ->get();

    }

    //Get Item
    public function getitem(Request $request)

    {
        $search=$request['SearchItem'];
        //return $search;
        return Tbl_item::where('item_name','like','%'.$search.'%')->get();
    }

    //Get Sub Department
    public function getsubdepartment()

    {
        return DB::table('tbl_sub_departments')
            ->select('id','sub_department_name')
            ->get();
    }


    public function getlabequipment()
    {
        return DB::table('tbl_items')->get();

    }

    public function getpatientlaborder(Request $request)
    {
        $patient_id=$request->input('patient_id');
        $order_id=$request->input('order_id');
        return DB::table('tbl_lab_orders')
            ->select('tbl_lab_orders.*','tbl_patients.*')
            ->where('patient_id','=',$patient_id)
            ->where('order_id','=',$order_id)
            ->join('tbl_patients',"tbl_lab_orders.patient_id","=","tbl_patients.id")
            //->join('tbl_patients')
            ->get();
    }

    //save patient lab order
    public function savepatientlaborder(Request $request)
    {
        $patient_firstname=$request['patient_firstname'];
        $item_name=$request['item_name'];
        $patient_id=$request['patient_id'];
        $order_id=$request['order_id'];
        $lab_test_id=$request['lab_test_id'];
        $facility_id=$request['facility_id'];
        $lab_order_id=$request['order_id'];
        $sample_no=$request['sample_no'];
        $receiver_id=$request['received_id'];

        $patientorder=Tbl_order::
        where('id',$lab_order_id)->update(array(
            //'patient_id'=>$patient_id,
            'order_id'=>$order_id,
            'test_id'=>$lab_test_id,
            'sample_no'=>$sample_no,
            'facility_id'=>$facility_id,
            'receiver_id'=>$receiver_id,
            'time_received'=>date('Y-m-d h:i:s')
        ));
        if(count($patientorder)==1){
            return response()->json(
                ['msg'=>$item_name." Sample Successful Collected",
                    'status'=>1
                ]
            )  ;
        }
        else{
            return response()->json(
                ['msg'=>$item_name." Not Saved",
                    'status'=>0
                ]
            )  ;
        }
    }


    //Post Patient Lab Orders
    public function postpatientlaborder(Request $request)
    {
        $patient_id=$request['patient_id'];
        $order_id=$request['order_id'];
        $laborderid=$request['laborderid'];
        $facility_id=$request['facility_id'];
        $sub_department_id=$request['sub_department_id'];
        $sample_no=$request['sample_no'];

        $patientlaborder = DB::table('vw_patient_lab_orders_topost')
            ->select(
                'laborderid',
                'sample_no',
                'patient_id',
                'first_name',
                'Middle_name',
                'last_name',
                'gender',
                'dob',
                'order_id',
                'clinical_note',
                'sample_collected_id',
                'item_name',
                'equipment_name',
                'equipment_id',
                'medical_record_number',
                'facility_id',
                'equipment_name',
                'item_indicator',
                'indicator_color_code',
                'sample_to_collect_id',
                'sample_to_collect',
                'item_range',
                'unit_id',
                'unit'
            )
            ->where('sample_no',"=",$sample_no)
            ->where('patient_id',"=",$patient_id)
            ->where('sub_department_id',"=",$sub_department_id)
            ->where('order_id',"=",$order_id)
            ->where('laborderid',"=",$laborderid)
            ->where('facility_id',"=",$facility_id)
            ->take(1)->get();

        if(count($patientlaborder)==1){
            return response()->json([
                'patientorder'=>$patientlaborder,
                'status'=>1
            ]);
        }
        else{
            return response()->json(
                [
                    'msg'=>"no record",
                    'status'=>0
                ]
            );
        }
    }

    //Post Patient Lab Orders
    public function getlaborderperdepartment(Request $request)
    {
        $sub_department_id=$request['sub_department_id'];
        return DB::table('vw_patient_lab_orders_topost')
            ->select('*')
            ->where('sample_no',"!=",'')
            ->where('processor_id',"=",NULL)
            ->where('sub_department_id',"=",$sub_department_id)
            ->get();
    }

    //Post Patient Lab Orders TO BE APPROVED
    public function getlaborapproves(Request $request)
    {
        $sub_department_id=$request['sub_department_id'];
        return DB::table('vw_patient_lab_orders_topost')
            ->select('*','approved_by')
            ->where('sample_no',"!=",'')
            ->where('processor_id',"!=",NULL)
            ->where('receiver_id',"!=",NULL)
            ->where('sub_department_id',"=",$sub_department_id)
            ->join('tbl_results',"vw_patient_lab_orders_topost.laborderid","=","tbl_results.order_id")
            ->where('approved_by',"=",NULL)
            ->get();
    }

    //Post Patient Lab Orders TO BE APPROVED
    public function getlaborapproved()
    {
        //$sub_department_id=$request['sub_department_id'];
        return DB::table('vw_patient_lab_orders_topost')
            ->select('*','approved_by')
            ->where('sample_no',"!=",'')
            ->where('processor_id',"!=",NULL)
            ->where('receiver_id',"!=",NULL)
            //->where('facility_id',"=",$facility_id)
            ->where('approved_by',"!=",NULL)
            ->join('tbl_results',"vw_patient_lab_orders_topost.laborderid","=","tbl_results.order_id")
            ->join('users',"tbl_results.approved_by","=","users.id")

            ->get();
    }


    //Send_patients_lab_result
    public function sendLabResult(Request $request){
        $sample_no=$request['sample_no'];
        $verified_by=$request['verified_by'];
        $request_id=$request['request_id'];
        $order_id=$request['order_id'];
        $results=$request['results'];
        $item_id=$request['item_id'];

        if(empty($results)){
            return response()->json(['data' =>'Please write Results for sample  '.$sample_no,
                'status' =>0
            ]);
        }
		
		$visit_date_id = Tbl_request::find($order_id)->visit_date_id;
		
		if($visit_date_id === 0){
            return response()->json(['data' =>'Invalid entry for Sample No.  '.$sample_no,
                'status' =>0
            ]);
        }
		
        Tbl_result::where('order_id',$order_id)->where('visit_date_id', $visit_date_id)->where('item_id',$item_id)->delete();
	
		$result = array(
            'description' => $results,
            'order_id' => $order_id,
			'visit_date_id' => $visit_date_id,
            'item_id' => $item_id,
            'post_user' => $verified_by,
            'verify_time' => date('h:i:s')
        );
		
        if(Tbl_result::create($result)) {
            Tbl_order::where('id', $request_id)->where('visit_date_id', $visit_date_id)->update(array('processor_id' => $verified_by,'result_control' => 1,'order_control' => 2,'order_status' => 2));

            return response()->json(['data' => " RESULTS FOR " . $sample_no . " WERE SUCCESSFULY SAVED",
                'status' => 1
            ]);
        }

    }



    //Post Patient Lab Orders
    public function getlabrequestperdepartment()
    {
        return DB::table('vw_patient_lab_orders_topost')
            ->select(
                'sub_department_name',
                DB::raw('count(sub_department_id)as ab')
            )
            ->groupBy('sub_department_name')
            ->get();
    }

    //Get Item Unit
    public function getunit(Request $request)
    {
        $search=$request['SearchValue'];
        return Tbl_unit::where('unit','like','%'.$search.'%')->get();
    }

    //Get Item Indicator
    public function getindicator(Request $request)
    {
        $search=$request['SearchValue'];
        return Tbl_lab_test_indicator::where('indicator','like','%'.$search.'%')
            ->select('*')
            ->join('tbl_colors',"tbl_lab_test_indicators.color_id","=","tbl_colors.id")
            ->get();
    }

    //test_panel_registration
    public function test_panel_registration(Request $request)
    {
        $panel_name=$request['panel_name'];
        $item_test_range=$request['item_test_range'];
        $item_unit=$request['item_unit'];
        $Test_indicator=$request['Test_indicator'];
        $test_panel=Tbl_lab_test_panel::
        where('panel_name',$panel_name)->first();
        if(count($test_panel)==1){
            return response()->json(
                ['msg'=>$panel_name. " Exists....",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_lab_test_panel::create(array(
                'panel_name'=>$panel_name,
                'Item_test_range'=>$item_test_range,
                'Item_unit'=>$item_unit,
                'Test_indicator'=>$Test_indicator
            ));
            return response()->json(
                ['msg'=>$panel_name. " Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //GET TEST PANEL
    public function gettest_panel()
    {
        return DB::table('tbl_lab_test_panels')
            ->select(
                'tbl_lab_test_panels.id as test_panel_id','tbl_lab_test_panels.panel_name',
                'tbl_lab_test_panels.Item_test_range',
                'tbl_units.id as unit_id','tbl_units.unit',
                'tbl_lab_test_indicators.indicator'
            )
            ->join('tbl_units',"tbl_lab_test_panels.Item_unit","=","tbl_units.id")
            ->join('tbl_lab_test_indicators',"tbl_lab_test_panels.Test_indicator","=","tbl_lab_test_indicators.id")
            ->get();
    }

    //UPDATE TEST PANEL
    public function test_panel_update(Request $request)
    {
        $panel_name=$request['panel_name'];
        $id=$request['id'];
        $Item_test_range=$request['Item_test_range'];
        $Item_unit=$request['Item_unit'];
        $Test_indicator=$request['Test_indicator'];
        $test_panel_updates=Tbl_lab_test_panel::where('id',$id)->update($request->all());
        if(count($test_panel_updates)==1){
            return response()->json(
                ['msg'=>$panel_name ." Updated....",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_lab_test_panel::create(array('panel_name'=>$panel_name));

            return "";
            return response()->json(
                ['msg'=>$panel_name ." Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //DELETE SAMPLE STATUS
    public function test_panel_delete($id,$panel_name)
    {
        $test_panel_deletes=Tbl_lab_test_panel::destroy($id);

        if(count($test_panel_deletes)==1){
            return response()->json(
                ['msgs'=>$panel_name ." Successful Deleted",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_lab_test_panel::create(
                array('panel_name'=>$test_panel_deletes));
            return "";
            return response()->json(
                ['msgs'=>$panel_name ." Successful Deleted",
                    'status'=>1
                ]
            )  ;
        }
    }

    //Get color
    public function getcolor(Request $request)
    {
        $search=$request['SearchValue'];
        return Tbl_color::where('color','like','%'.$search.'%')->get();
    }

    //test_indicator_registration
    public function test_indicator_registration(Request $request)
    {
        $indicator=$request['indicator'];
        $color_id=$request['color_id'];
        $color=Tbl_lab_test_indicator::where('indicator',$indicator)->first();
        if(count($color)==1){
            return response()->json(
                ['msg'=>$indicator. " Exists....",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_lab_test_indicator::create(array(
                'indicator'=>$indicator,
                'color_id'=>$color_id
            ));
            return response()->json(
                ['msg'=>$indicator. " Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //Get Lab Test Indicator
    public function getlab_test_indicator()

    {
        return DB::table('tbl_lab_test_indicators')
            ->select('Tbl_lab_test_indicators.id','indicator','color_code','color','tbl_colors.id as color_id')
            ->join('tbl_colors',"Tbl_lab_test_indicators.color_id","=","tbl_colors.id")
            ->get();
    }
    //lab test indicator update
    public function test_indicator_update(Request $request)
    {
        $indicator=$request['indicator'];
        $color_id=$request['color_id'];
        $id=$request['id'];
        $test_indicator_updates=Tbl_lab_test_indicator::where('id',$id)->update($request->all());
        if(count($test_indicator_updates)==1){
            return response()->json(
                ['msg'=>$indicator ." Updated....",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_lab_test_indicator::create(array('indicator'=>$indicator));

            return "";
            return response()->json(
                ['msg'=>$indicator ." Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //Delete test_indicator
    public function test_indicator_delete($id,$indicator)
    {
        $test_indicator_deletes=Tbl_lab_test_indicator::destroy($id);

        if(count($test_indicator_deletes)==1){
            return response()->json(
                ['msgs'=>$indicator ." Successful Deleted",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_lab_test_indicator::create(
                array('indicator'=>$indicator));
            return response()->json(
                ['msgs'=>$indicator ." Successful Deleted",
                    'status'=>1
                ]
            )  ;
        }
    }

    //test_sample_registration
    public function test_sample_registration(Request $request)
    {
        $sample_to_collect=$request['sample_to_collect'];
        $testsample=Tbl_lab_sample_to_collect::where('sample_to_collect',$sample_to_collect)->first();
        if(count($testsample)==1){
            return response()->json(
                ['msg'=>$sample_to_collect. " Exists....",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_lab_sample_to_collect::create(array(
                'sample_to_collect'=>$sample_to_collect
            ));
            return response()->json(
                ['msg'=>$sample_to_collect. " Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //Get lab_sample_to_collect
    public function get_test_sample()
    {
        return DB::table('tbl_lab_sample_to_collects')->get();
    }

    //lab_sample_to_collect update
    public function testsample_update(Request $request)
    {
        $id=$request['id'];
        $sample_to_collect=$request['sample_to_collect'];
        $testsample_updates=Tbl_lab_sample_to_collect::where('id',$id)->update($request->all());
        if(count($testsample_updates)==1){
            return response()->json(
                ['msg'=>$sample_to_collect ." Updated....",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_lab_sample_to_collect::create(array('sample_to_collect'=>$sample_to_collect));
            return response()->json(
                ['msg'=>$sample_to_collect ." Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //testsample_delete
    public function testsample_delete($id,$sample_to_collect)
    {
        $testsample_deletes=Tbl_lab_sample_to_collect::destroy($id);

        if(count($testsample_deletes)==1){
            return response()->json(
                ['msgs'=>$sample_to_collect ." Successful Deleted",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_lab_sample_to_collect::create(
                array('sample_to_collect'=>$sample_to_collect));
            return "";
            return response()->json(
                ['msgs'=>$sample_to_collect ." Successful Deleted",
                    'status'=>1
                ]
            )  ;
        }
    }

    //test_unit_registration
    public function test_unit_registration(Request $request)
    {
        $unit=$request['unit'];
        $testunit=Tbl_unit::where('unit',$unit)->first();
        if(count($testunit)==1){
            return response()->json(
                ['msg'=>$unit. " Exists....",
                    'status'=>0
                ]
            );
        }
        else{
            $data=Tbl_unit::create(array(
                'unit'=>$unit
            ));
            return response()->json(
                ['msg'=>$unit. " Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //Get Test Unit
    public function gettest_unit()
    {
        return DB::table('tbl_units')->get();
    }

    //test_unit_update
    public function test_unit_update(Request $request)
    {
        $id=$request['id'];
        $unit=$request['unit'];
        $testunits=Tbl_unit::where('id',$id)->update($request->all());
        if(count($testunits)==1){
            return response()->json(
                ['msg'=>$unit ." Updated....",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_unit::create(array('unit'=>$unit));
            return response()->json(
                ['msg'=>$unit ." Successful Registered",
                    'status'=>1
                ]
            )  ;
        }
    }

    //test_unit_delete
    public function test_unit_delete($id,$unit)
    {
        $testunit=Tbl_unit::destroy($id);

        if(count($testunit)==1){
            return response()->json(
                ['msgs'=>$unit ." Successful Deleted",
                    'status'=>0
                ]
            )  ;
        }
        else{
            $data=Tbl_unit::create(array('unit'=>$unit));
            return response()->json(
                ['msgs'=>$unit ." Successful Deleted",
                    'status'=>1
                ]
            )  ;
        }
    }

    //Search Sample to Collect
    public function getsample(Request $request)
    {
        $search=$request['SearchValue'];
        return Tbl_lab_sample_to_collect::
        where('sample_to_collect','like','%'.$search.'%')
            ->get();
    }

    //Service Item Equipment
    public function getequipements(Request $request)
    {
        $search=$request['SearchValue'];
        return Tbl_equipment::where('equipment_name','like','%'.$search.'%')
            ->get();
    }

    //get equipment status
    public function getequpstatus(Request $request)
    {
        $search=$request['SearchValue'];
        return Tbl_equipment_status::where('status_name','like','%'.$search.'%')
            ->get();

    }

    //Get facility
    public function getfacility(Request $request)
    {
        $search=$request['SearchValue'];
        return DB::table('tbl_facilities')->
        where('facility_name','like','%'.$search.'%')
            ->get();

    }

    //Post Approve Results
    public function approveresult(Request $request)
    {
        $item_name=$request['item_name'];
        $order_id=$request['order_id'];
        $verified_by=$request['verified_by'];
        $verified_time=$request['verified_time'];
        $result=$request['result'];
        $user_id=$request['user_id'];

		$visit_date_id = Tbl_request::find($order_id)->visit_date_id;
		
        $approvepatientlabresults=Tbl_result::where('result',$result)
            ->where('order_id',$order_id)
            ->where('visit_date_id',$visit_date_id)
            ->where('verified_by',$verified_by)
            ->where('verified_time',$verified_time)
            ->update(array('approved_by'=>$user_id,'approved_time'=>date('Y-m-d h:i:s')));

        if(count($approvepatientlabresults)==1){
            return response()->json(
                ['msg'=>$item_name. " RESULTS SUCCESSFULLY APPROVED",
                    'status'=>0
                ]);
        }
        else{
            $data=Tbl_result::create(array(
                'result'=>$result,
				'order_id'=>$order_id,
				'visit_date_id'=>$visit_date_id,
                'verified_by'=>$verified_by,'verified_time'=>$verified_time,
                'approved_by'=>$user_id,
                'approved_time'=>date('Y-m-d h:i:s')
            ));
            return response()->json(
                ['msg'=>" RESULTS FOR ".$item_name." SAVED",
                    'status'=>1
                ]);
        }

    }

    public function getlabrequest($facility_id)
    {
        return DB::table('tbl_requests')
            ->join('tbl_patients',"tbl_requests.patient_id","=","tbl_patients.id")
            ->join('tbl_facilities',"tbl_patients.facility_id","=","tbl_facilities.id")
            ->where('facility_id',$facility_id)
            ->get();

    }

    public function TaTReport(Request $request){
        $sql="SELECT * FROM `vw_tatReports` t1 WHERE  logout BETWEEN '".$request->start_date."'  AND '".$request->end_date."'";
        return DB::SELECT($sql);

    }

    public function getSampleTesttedCount(Request $request){

        $sql="SELECT count(patient_id) as clients,item_name FROM `vw_investigation_results` t1 WHERE  created_at BETWEEN '".$request->start_date."'  AND '".$request->end_date."' AND dept_id=2 Group BY t1.item_id DESC ";
        return DB::SELECT($sql);

    }

    public function getTests(Request $request)
    {
        $searchKey = $request['searchKey'];
        //  return Tbl_item::where('dept_id',2)->where('item_name','like','%'.$searchKey.'%')->get();
        return     DB::select("SELECT *FROM tbl_items where dept_id=2 and item_name like '%$searchKey%'");

    }

    public function testReports(Request $request)
    {


        $item_id = $request['item_id'];
        $start = $request['start'];
        $end = $request['end'];
        $facility_id = $request['facility_id'];
        $sql = "
    SELECT t3.description,
    IFNULL(sum(CASE when gender ='FEMALE' AND timestampdiff(MONTH ,dob,'".$end."') <1  then 1 ELSE  0 END ),0) as female_0_1_month,
    IFNULL(sum(CASE when gender ='FEMALE' AND timestampdiff(MONTH ,dob,'".$end."') BETWEEN 1 and 11  then 1 ELSE  0 END ),0) as female_0_11_month,
    IFNULL(sum(CASE when gender ='FEMALE' AND timestampdiff(YEAR ,dob,'".$end."') BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as female_1_5_year,
    IFNULL(sum(CASE when gender ='FEMALE' AND timestampdiff(YEAR ,dob,'".$end."') BETWEEN 6 and 59  then 1 ELSE  0 END ),0) as female_6_59_year,  
    IFNULL(sum(CASE when gender ='FEMALE' AND timestampdiff(YEAR ,dob,'".$end."') >60  then 1 ELSE  0 END ),0) as female_above_60_year,
    IFNULL(sum(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0) as female_total_attendance,
    IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end."') <1  then 1 ELSE  0 END ),0) as male_0_1_month,
    IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end."') BETWEEN 1 and 11  then 1 ELSE  0 END ),0) as male_0_11_month,
    IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end."') BETWEEN 1 and 5  then 1 ELSE  0 END ),0)as male_1_5_year,
    IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end."') BETWEEN 6 and 59  then 1 ELSE  0 END ),0) as male_6_59_year, 
    IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end."') >60  then 1 ELSE  0 END ),0) as male_above_60_year,
    IFNULL(sum(CASE when  timestampdiff(MONTH ,dob,'".$end."') <1  then 1 ELSE  0 END ),0) as total_0_1_month, 
    IFNULL(sum(CASE when  timestampdiff(MONTH ,dob,'".$end."') BETWEEN 1 and 11  then 1 ELSE  0 END ),0) as total_0_11_month, 
    IFNULL(sum(CASE when  timestampdiff(YEAR ,dob,'".$end."') BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as total_1_5_year, 
    IFNULL(sum(CASE when  timestampdiff(YEAR ,dob,'".$end."') BETWEEN 6 and 59  then 1 ELSE  0 END ),0) as total_6_59_year, 
    IFNULL(sum(CASE when  timestampdiff(YEAR ,dob,'".$end."') >60  then 1 ELSE  0 END ),0) as total_above_60_year, 
    IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance,
    IFNULL(sum(CASE when gender ='MALE' OR gender='FEMALE'  then 1 ELSE  0 END ),0) as grand_total_attendance
    FROM tbl_patients t1 JOIN tbl_requests t2 ON t1.id = t2.patient_id 
    JOIN tbl_results t3 ON t3.order_id=t2.id 
     WHERE item_id = '".$item_id."'  AND t3.updated_at BETWEEN '".$start."' 
     AND '".$end."' group by item_id,description ";

        return DB::select(DB::raw($sql));
    }

    public function saveTbLeprosyRequest(Request $request)

    {

        $request->all()['visit_id'];
        if(patientRegistration::duplicate('tbl_tb_leprosy_requests',array('visit_id','patient_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <5))"), array($request->all()['visit_id'],$request->all()['patient_id']))==true){
            return response()->json(['msg' =>'Duplication detected',
                'status' =>0
            ]);
        }
        Tbl_tb_leprosy_request::create($request->all());
        return response()->json(
            ['msg'=> " Request SUCCESSFULLY Send",
                'status'=>1
            ]);
    }
    public function saveTbLeprosyResult(Request $request)
    {
        if(patientRegistration::duplicate('tbl_tb_leprosy_results',array('patient_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <5))"), array($request->all()['patient_id']))==true){
            Tbl_tb_leprosy_result::where('request_id',$request->all()['request_id'])->update($request->all());
            Tbl_tb_leprosy_request::where('id',$request->all()['request_id'])->update(['status'=>0]);
            return response()->json(['msg' =>'record has changed',
                'status' =>1
            ]);
        }
        Tbl_tb_leprosy_result::create($request->all());
        Tbl_tb_leprosy_request::where('id',$request->all()['request_id'])->update(['status'=>0]);
        return response()->json(
            ['msg'=> " Result SUCCESSFULLY Send",
                'status'=>1
            ]);
    }

    public function ProveTbLeprosyResult(Request $request)
    {

        Tbl_tb_leprosy_result::where('request_id',$request->all()['request_id'])->update($request->all());
        Tbl_tb_leprosy_request::where('id',$request->all()['request_id'])->update(['status'=>1]);
        return response()->json(
            ['msg'=> " Result SUCCESSFULLY Verified",
                'status'=>1
            ]);
    }

    public function savedTbLeprosyRequestData()
    {
        $all[]= DB::select("select t1.*,residence_id,date(t1.created_at) as date_of_request,time(t1.created_at) as time_of_request,t2.first_name,t2.middle_name,t2.last_name,t2.dob as age, t2.medical_record_number,t2.mobile_number,t2.gender,u.name as requested_by from tbl_tb_leprosy_requests t1 join tbl_patients t2 on t1.patient_id=t2.id
 join users u on t1.user_id=u.id
 where t1.status is null AND timestampdiff(day, t1.created_at, current_time) <= (SELECT sum(days) FROM `tbl_lab_test_lives`) ");
        $all[]= DB::select("select t1.*,residence_id,date(t1.created_at) as date_of_request,time(t1.created_at) as time_of_request,t2.first_name,t2.middle_name,t2.last_name,t2.dob as age, t2.medical_record_number,t2.mobile_number,t2.gender,u.name as requested_by from tbl_tb_leprosy_requests t1 join tbl_patients t2 on t1.patient_id=t2.id
 join users u on t1.user_id=u.id
 where t1.status =0 AND timestampdiff(day, t1.created_at, current_time) <= (SELECT sum(days) FROM `tbl_lab_test_lives`)");
        return $all;
    }

    public function TB_leprosyResultsQueues()
    {
        return   DB::select("select t1.*,residence_id,date(t1.created_at) as date_of_request,date(t1.created_at) as date_attended,time(t1.created_at) as time_of_request,t2.first_name,t2.middle_name,t2.last_name,t2.dob as age, t2.medical_record_number,t2.mobile_number,t2.gender,u.name as requested_by from tbl_tb_leprosy_requests t1 join tbl_patients t2 on t1.patient_id=t2.id
 join users u on t1.user_id=u.id
 where t1.status=1 ");

    }

    public function TB_leprosyResultsPerRequest($request_id)
    {
        return  DB::select(" select t1.*,u.name as reviewed_by,date(t1.created_at) as reviewed_date,time(t1.created_at) as reviewed_time from tbl_tb_leprosy_results t1 join users u on t1.user_id=u.id where t1.request_id=$request_id");

    }
   
    public function gettb_leprosyResultToApprove($requestId)
    {
        return  DB::select(" select t1.*,u.name as reviewed_by,date(t1.created_at) as reviewed_date,time(t1.created_at) as reviewed_time from tbl_tb_leprosy_results t1 join users u on t1.user_id=u.id where t1.request_id=$requestId AND t1.status=0");
    }

    public function getpatientAddress($residence_id)
    {
        return Tbl_residence::where("id",$residence_id)->get();
    }
    public function labItemsList($dept)
    {
        if($dept==2){
            return DB::select("select item_name,tbl_items.id,tbl_items.status from tbl_items 
join tbl_item_prices on tbl_items.id=tbl_item_prices.item_id where tbl_items.dept_id=$dept group by tbl_items.id order by item_name asc ");

        }
        elseif ($dept==4){
            return DB::select("select item_name,tbl_items.id,tbl_items.status from tbl_items 
join tbl_item_prices on tbl_items.id=tbl_item_prices.item_id where tbl_items.dept_id=$dept group by tbl_items.id order by item_name asc ");

        }
    }
    public function activateOrDeactivateTestPrice(Request $request)
    {
        $item_id=$request->all()['item_id'];
        $status=$request->all()['status'];
        $dept=$request->all()['dept_id'];
        if ($dept==2){
            Tbl_item_price::where("item_id",$item_id)->update([
                'status'=>$status
            ]);
            return  $this->labItemsList($dept);
        }
        if ($dept==4){
            Tbl_item::where("id",$item_id)->update([
                'dept_id'=>1
            ]);
            return  $this->labItemsList($dept);
        }

    }

 public function TB_leprosyResultsPerPatient($request_id)
    {
        return  DB::select(" select t1.*,u.name as reviewed_by,date(t1.created_at) as date_attended,time(t1.created_at) as reviewed_time,p.residence_id from tbl_tb_leprosy_results t1 join users u on t1.user_id=u.id
 join tbl_patients p on p.id=t1.patient_id where t1.patient_id=$request_id order by t1.id desc limit 5 ");

    }

 public function lab_test_life(Request $request)
    {
       $daa=$request->facility_id;
        $check=Tbl_lab_test_live::where('facility_id',$daa)->count();
        if ($check>0){
            Tbl_lab_test_live::where('facility_id',$daa)->update($request->all());
            return response()->json([
                "msg"=>"modified"
            ]);
        }
        else{
            Tbl_lab_test_live::create($request->all());
            return response()->json([
                "msg"=>"saved"
            ]);
        }

    }

public function getLabReportingControlList()
    {
     return Tbl_lab_reporting_control::get();
    }


    public function labInticatorMapping(Request $request)
    {
        if($request->has('save_status')){
            foreach ($request->save_status as $tracers)
                Tbl_lab_reporting_control::where('id',$tracers['id'])
                    ->update(['status'=>$tracers['status']]);

            return response()->json([
                'msg' => 'Status successfully set',
                'status' => 1]);

        }elseif($request->has('save_mapping')){
            foreach($request->save_mapping as $mapping)
                Tbl_lab_reporting_indictor_map::create([
                    'lab_indicator_id'=>$mapping['lab_indicator_id'],
                    'item_id'=>$mapping['id'],
                ]);
            return response()->json(['msg'=>'Item(s) successfully added under the category']);
        }elseif($request->has('remove_mapping')){
            Tbl_lab_reporting_indictor_map::where('id', $request->remove_mapping['id'])->delete();
            return response()->json(['msg'=>'Item removed under the category']);
        }
    }

    public function removeFromLabIndicatorGroupMapping( Request $request)
    {
        $id = $request['id'];

        DB::statement("DELETE FROM `tbl_lab_reporting_indictor_maps` WHERE `tbl_lab_reporting_indictor_maps`.`id` = '" . $id . "'");


        return response()->json([
            'msg' => ' Removed',
            'status' => 1
        ]);
}
    public function indicator_groups()
    {
        return DB::select("SELECT t2.id, t2.lab_indicator_id as code,t1.item_name FROM tbl_items t1 Join tbl_lab_reporting_indictor_maps t2  on t2.item_id= t1.id");
    }

    public function labMonthlyReport(Request $request)
    {
    $start=$request->input('start');
    $end=$request->input('end');
        $sql_1="  SELECT  t1.code as indicator_code,t1.item_name as lab_indicator,
                (select count(*)  from tbl_orders where order_status=1 and t2.item_id=tbl_orders.test_id and t1.status=1 and tbl_orders.created_at between '".$start."' and '".$end."' ) as all_test_conducted,
                (select count(*)  from tbl_unavailable_tests where  t2.item_id=tbl_unavailable_tests.item_id and t1.status=1 and tbl_unavailable_tests.created_at between '".$start."' and '".$end."') as unavailable_test,
                (select count(*)  from tbl_orders where order_status<>1  and t2.item_id=tbl_orders.test_id and t1.status=1 and tbl_orders.created_at between '".$start."' and '".$end."') as available_test_not_conducted,
                (select ifnull(sum(price*quantity),0) from tbl_invoice_lines join tbl_orders on tbl_orders.test_id=tbl_invoice_lines.item_id where main_category_id=3 and tbl_invoice_lines.status_id<>2  and t2.item_id=tbl_orders.test_id and t1.status=1 and tbl_orders.created_at between '".$start."' and '".$end."') as gharama_misamaha,
                (select count(tbl_orders.id) from tbl_invoice_lines join tbl_orders on tbl_orders.test_id=tbl_invoice_lines.item_id where main_category_id=3   and t2.item_id=tbl_orders.test_id and t1.status=1 and tbl_orders.created_at between '".$start."' and '".$end."' ) as tests_conducted_misamaha,
                (select ifnull(sum(price*quantity),0) from tbl_invoice_lines join tbl_orders on tbl_orders.test_id=tbl_invoice_lines.item_id where main_category_id=2 and tbl_invoice_lines.status_id<>2 and order_status=1 and t1.status=1 and t2.item_id=tbl_orders.test_id  and tbl_orders.created_at between '".$start."' and '".$end."') as gharama_bima
                  from tbl_lab_reporting_controls t1
                    left join tbl_lab_reporting_indictor_maps t2 on t1.id=t2.lab_indicator_id
                    where t1.status=1

                   order by t1.id asc;";

        return   DB::select($sql_1);
    }


    public function labAPI(){
        
        return "LAB API IS RUNNING with ID Number:";
    }

     public function PostLabAPI(Request $request){

$this->runViews1();
        $destinationPath = 'uploads/lab'; // upload path
        $data1=$request->all();
        file_put_contents($destinationPath.'/lab.json', json_encode($data1));
        file_put_contents($destinationPath.'/sampleID.txt',  $data1['releaseTime']);

        if($data1['sampleId']==""){
          return response()->json(['message' =>"Sample Number Column is Empty ",
                'status' =>401
            ]);  
        }
         if($data1['result']==""){
          return response()->json(['message' =>"Result Column is Empty ",
                'status' =>401
            ]);  
        }
           $check=Tbl_lab_machine_result::where('sampleId',$data1['sampleId'])->where('result',$data1['result'])->where('status',1)->count();

        if( $check>0){
           return response()->json(['data' =>"Sample Number: ". $data1['sampleId'].' With Result '.$data1['result'] .'  was already Verified and sent',
                'status' =>403
            ]);
        }

        $check=Tbl_lab_machine_result::where('sampleId',$data1['sampleId'])->where('result',$data1['result'])->where('status',0)->count();
 $TempArray_value = $data1['releaseTime'];
          $year = $TempArray_value[0].$TempArray_value[1].$TempArray_value[2].$TempArray_value[3];
                    $month = $TempArray_value[4].$TempArray_value[5];
                    $day = $TempArray_value[6].$TempArray_value[7];
                    $time = $TempArray_value[8].$TempArray_value[9].':'.$TempArray_value[10].$TempArray_value[11].':'.$TempArray_value[12].$TempArray_value[13];

                     $releaseTime=$year.'-'.$month.'-'.$day.' '.$time; 
                     //
                      $TempArray_value = $data1['timeCompleted'];
   $year = $TempArray_value[0].$TempArray_value[1].$TempArray_value[2].$TempArray_value[3];
                    $month = $TempArray_value[4].$TempArray_value[5];
                    $day = $TempArray_value[6].$TempArray_value[7];
                    $time = $TempArray_value[8].$TempArray_value[9].':'.$TempArray_value[10].$TempArray_value[11].':'.$TempArray_value[12].$TempArray_value[13];
                     $timeCompleted=$year.'-'.$month.'-'.$day.' '.$time;
                     //get user names of operator and approver
$operator=User::where('email',$data1['operator'])->get();
if(count($operator)>0){
  $operator_id=$operator[0]->id; 
  $operatedSms=''; 
}
else{
 $operator_id=null;
 $approverSms='UNKNOWN OPERATOR'; 
}
$appover=User::where('email',$data1['releasedBy'])->get();
 if(count($appover)>0){
  $appover_id=$appover[0]->id;  
}
else{
    $appover_id=null; 
    $approverSms='UNKNOWN APPROVER';
}
                      
        if( $check>0){
           
       //update whole data from machine to a dump table   
        
     $savedata=   Tbl_lab_machine_result::where('sampleId',$data1['sampleId'])->update([
"machineName"=>$data1['machineName'],
  "version" =>$data1['version'],
  "releaseTime" =>$releaseTime,
  "patientName" =>$data1['patientName'],
  "sampleId" =>$data1['sampleId'],
  "sampleCarier" =>$data1['sampleCarier'],
  "samplePosition" =>$data1['samplePosition'],
  "assayNumber" =>$data1['assayNumber'],
  "assayName" =>$data1['assayName'],
  "dilution" =>$data1['dilution'],
  "rlu" =>$data1['rlu'],
  "reagentLot"=>$data1['reagentLot'],
  "reagentSerialNumber" =>$data1['reagentSerialNumber'],
  "result" =>$data1['result'],
  "unit"=>$data1['unit'],
  "range"=>$data1['range'],
  "flag"=>$data1['flag'],
  "operator" =>$data1['operator'],
  "releasedBy"=>$data1['releasedBy'],
  "timeCompleted"=>$timeCompleted,
  "serialNumber" =>$data1['serialNumber'],
        ]);
        }
        else{

      
       //save whole data from machine to a dump table    
     $savedata=   Tbl_lab_machine_result::create([
"machineName"=>$data1['machineName'],
  "version" =>$data1['version'],
  "releaseTime" =>$releaseTime,
  "patientName" =>$data1['patientName'],
  "sampleId" =>$data1['sampleId'],
  "sampleCarier" =>$data1['sampleCarier'],
  "samplePosition" =>$data1['samplePosition'],
  "assayNumber" =>$data1['assayNumber'],
  "assayName" =>$data1['assayName'],
  "dilution" =>$data1['dilution'],
  "rlu" =>$data1['rlu'],
  "reagentLot"=>$data1['reagentLot'],
  "reagentSerialNumber" =>$data1['reagentSerialNumber'],
  "result" =>$data1['result'],
  "unit"=>$data1['unit'],
  "range"=>$data1['range'],
  "flag"=>$data1['flag'],
  "operator" =>$data1['operator'],
  "releasedBy"=>$data1['releasedBy'],
  "timeCompleted"=>$timeCompleted,
  "serialNumber" =>$data1['serialNumber'],
        ]);

 }
        //get sample order details from orders table
$getsampleID=Tbl_order::where("sample_no",$data1['sampleId'])->get();

//check if sample number exists in database
if(count($getsampleID)==0){
    //update dump table for this failure
    Tbl_lab_machine_result::where('sampleId',$data1['sampleId'])->update([
        'status'=>0,
        'remark'=>"This Sample Number or Operator Does not Exists in GOTHOMIS database"
    ]);
   return response()->json(['message' =>"This Sample Number or Operator Does not Exists in GOTHOMIS database please crosscheck with your machine  " ,
                'status' =>401,
                'sampleID'=>$data1['sampleId']
            ]);  
}
$order_id=$getsampleID[0]->order_id;
$item_id=$getsampleID[0]->test_id;
//record sample results in results table

        Tbl_result::where("order_id",$order_id)->where("item_id",$item_id)->update([
            "description" =>$data1['result'],
            "units" =>$data1['unit'],
            "confirmation_status" =>1,
            "post_user" =>$operator_id,//TODO
            'verify_user'=>$appover_id,//TODO
            'verify_time'=>date("h:i:s")
        ]);
        Tbl_order::where('order_id', $order_id)->update(['result_control'=>1]);
  Tbl_lab_machine_result::where('sampleId',$data1['sampleId'])->delete();
        return response()->json(['message' =>"Sample Result has Successful posted!!! ",
                'status' =>200,
                'sampleID'=>$data1['sampleId']
            ]);  

}

public function rejectedResultsFromMachines(Request $request){

if($request->input('start_date')=="" && $request->input('end_date') ==""){
  
$res=DB::select("select *from tbl_lab_machine_results where timestampdiff(day, created_at, current_date) <2 group by result,sampleId");
}else{
     $start=$request->input('start_date');
$end=$request->input('end_date'); 
    

     $res= DB::select("select *from tbl_lab_machine_results where created_at between '".$start."' AND '".$end."' group by result,sampleId");
}
return $res;
}

public function runViews1(){
DB::statement("ALTER TABLE `tbl_results` ADD column if not EXISTS `units` VARCHAR(11) NULL AFTER `unit`");
DB::statement("ALTER TABLE `tbl_results` ADD column if not EXISTS `range` VARCHAR(11) NULL AFTER `units`");
DB::statement("ALTER TABLE `tbl_results` ADD column if not EXISTS `flag` VARCHAR(11) NULL AFTER `range`");

 DB::statement("CREATE TABLE IF NOT EXISTS `tbl_lab_machine_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `machineName` varchar(191) DEFAULT NULL,
  `version` varchar(10) DEFAULT NULL,
  `releaseTime` varchar(10) NOT NULL,
  `patientName` varchar(191) NOT NULL,
  `sampleId` varchar(191) NOT NULL,
  `sampleCarier` varchar(191) NOT NULL,
  `samplePosition` varchar(191) NOT NULL,
  `assayNumber` varchar(191) NOT NULL,
  `assayName` varchar(191) NOT NULL,
  `dilution` varchar(20) NOT NULL,
  `rlu` varchar(12) NOT NULL,
  `reagentLot` varchar(30) NOT NULL,
  `reagentSerialNumber` varchar(32) NOT NULL,
  `result` decimal(10,1) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `range` varchar(12) DEFAULT NULL,
  `flag` varchar(7) DEFAULT NULL,
  `operator` varchar(191) NOT NULL,
  `releasedBy` varchar(32) NOT NULL,
  `timeCompleted` varchar(23) NOT NULL,
  `serialNumber` varchar(191) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ");

  $patients="CREATE OR REPLACE VIEW  `vw_getSampleReports` AS(    
        SELECT t1.*,t2.sample_no,t4.item_name,t4.dept_id,
        CONCAT(t5.first_name,' ',t5.middle_name,' ',t5.last_name) AS full_name,
        t5.mobile_number,
t5.gender,
        t2.sample_types,
        t2.clinical_note,
        DATE(t3.created_at) AS date_requested,
        t5.medical_record_number,
        (SELECT name FROM users t10 WHERE t10.id=t1.post_user GROUP BY t10.id) AS posted_by,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END AS age,
         CASE 
         WHEN t5.residence_id IS NOT NULL THEN (SELECT CONCAT(residence_name,' ',council_name) FROM tbl_residences t6 INNER JOIN tbl_councils t7 ON t6.council_id=t7.id WHERE t6.id=t5.residence_id GROUP BY t6.id) END AS residence_name,       
         CASE 
         WHEN t3.doctor_id IS NOT NULL THEN (SELECT name FROM users t8  WHERE t8.id=t3.doctor_id GROUP BY t8.name) END AS doctor_name,
         CASE 
         WHEN t3.doctor_id IS NOT NULL THEN (SELECT t8.mobile_number  FROM users t8  WHERE t8.id=t3.doctor_id GROUP BY t8.mobile_number) END AS doctor_mobile_number    ,
         CASE 
         WHEN t3.requesting_department_id IS NOT NULL THEN (SELECT department_name  FROM tbl_departments t9  WHERE t3.requesting_department_id=t9.id GROUP BY t9.department_name) END AS requesting_department  ,
         CASE 
         WHEN t2.sample_no IS NOT NULL THEN (SELECT DATE(t10.created_at)  FROM tbl_sample_number_controls t10  
         WHERE  TRIM(LEADING '0' FROM t10.sample_no)=t2.sample_no GROUP BY t10.created_at LIMIT 1) END AS date_collected,
         CASE 
         WHEN t2.sample_no IS NOT NULL THEN (SELECT TIME(t10.created_at)  FROM tbl_sample_number_controls t10 
         
         WHERE  TRIM(LEADING '0' FROM t10.sample_no)=t2.sample_no GROUP BY t10.created_at LIMIT 1) END AS time_collected     ,
         CASE 
         WHEN t2.sample_no IS NOT NULL THEN (SELECT name  FROM users t11
         INNER JOIN  tbl_sample_number_controls t10  ON t10.user_id=t11.id
         WHERE t2.receiver_id=t10.user_id GROUP BY t2.receiver_id LIMIT 1) END AS collected_by
         
        FROM tbl_results t1 
        INNER JOIN tbl_requests t3 ON t3.id = t1.order_id
        INNER JOIN tbl_orders t2 ON t1.item_id = t2.test_id AND DATE(t1.created_at) = DATE(t2.created_at)
        INNER JOIN tbl_items t4 ON t4.id = t1.item_id
        INNER JOIN tbl_patients t5 ON t5.id = t3.patient_id
        WHERE t4.dept_id = 2
           AND t2.order_id=t3.id 
          group by t1.item_id,t1.order_id)";
 DB::statement($patients);
}
   


   //Send_patients_lab_result
    public function saveAmmendedResult(Request $request){
 
        $sample_no=$request['sample_no'];
		$verified_by=$request['verified_by'];
        $results=$request['description'];
        $item_id=$request['item_id'];
        $patient_id=$request['patient_id'];
        $OrderDetail=Tbl_order::where("sample_no",$sample_no)->get();
        $order_id=$OrderDetail[0]->order_id;
		
        $visit_date_id=Tbl_request::find($order_id)->visit_date_id;
		
		$allData= Tbl_result::where("item_id",$item_id)->where("order_id",$order_id)->where('visit_date_id', $visit_date_id)->get();
		
        $saved= Tbl_result::create(array(
            'description' => $results,
            'order_id' => $order_id,
            'visit_date_id' => $visit_date_id,
            'item_id' => $item_id,
            'post_user' => $verified_by,
           'post_time' => date('h:i:s'),
            'verify_user'=>$request['verified_by'],
            "confirmation_status"=> 1,
            'verify_time' => date('h:i:s')));
            
         
		Tbl_result::where('id', $saved->id)->update(['verify_time'=>date("h:i:s"),'verify_user'=>$verified_by,'confirmation_status'=>1
			,'description'=>$results]);
		
		return response()->json(['data' => " RESULTS FOR " . $sample_no . " WERE SUCCESSFULY AMMENDED",
			'status' => 1
		]);
    }
}