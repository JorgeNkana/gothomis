<?php

namespace App\Http\Controllers\Radiology;

use App\ClinicalServices\Tbl_order;
use App\ClinicalServices\Tbl_request;
use App\Department\Tbl_department;
use App\Equipments\Tbl_equipment;
use App\Item_setups\Tbl_item;
use App\Item_setups\Tbl_item_type_mapped;
use App\laboratory\Tbl_staff_section;
use App\Patient\Tbl_patient;
use App\Results\Tbl_result;
use App\Sub_department\Tbl_imaginguser;
use App\Sub_department\Tbl_sub_department;
use App\Equipment_status\Tbl_equipment_status;
use App\Sub_department\Tbl_subdepartment;
use App\Sub_department\Tbl_userSubdepartment;
use App\Xray\Tbl_xray;
use App\Xray_Test\Tbl_test;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\classes\SystemTracking;
use App\Trackable;
use Nexmo\Laravel\Facade\Nexmo;


class RadiologyController extends Controller

{

   


    public function FindingsSaveRegister(Request $request)

    {
        $date = date('Y-m-d h:i:s');
		$request = Tbl_request::find($data['order_id']);
        if (count($request->all()) > 0) {
            foreach ($request->all() as $data) {
                $data = Tbl_result::create([
                    'visit_date_id' => $request->visit_date_id,
                    'order_id' => $request->id,
                    'description' => $data['description'],
                    'eraser' => $data['eraser'],
                    'confirmation_status' => $data['confirmation_status'],
                    'post_time' => $date,
                    'post_user' => $data['post_user'],
                ]);
				
				$newData=$data;
				$trackable_id=$newData->id;
				$user_id=$newData->post_user;
				SystemTracking::Tracking($user_id,$request->patient_id,$trackable_id,$newData,null);
            }
        }
        return response()->json([
            'msg' => "Findings Successfully Saved",
            'notification' => "Success",
            'status' => 1
        ]);
    }

    public function getPostedResults(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT * FROM `vw_postedresults` WHERE created_at BETWEEN '".$start."'  AND '".$end."'";
        $emergencyData = DB::select(DB::raw($sql));
        return $emergencyData;
    }
    public function getPatientQueXray(Request $request)
    {
        $id = $request->input('facility_id');
        $user_id = $request->input('user_id');
        $allowed = 1;
        $sql = "SELECT * FROM `Vw_xray_orders` WHERE facility_id ='".$id."' AND order_status IS NULL
         AND sub_department_id IN (SELECT section_id FROM tbl_staff_sections  WHERE technologist_id ='".$user_id."' AND isAllowed = 1)GROUP BY request_id  LIMIT 100 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    /**,
     * Searching patients in the Digital Radiograph Queue
     * Returns Patients that are not in the queue
     */
    public function getPatientQueXrayNotInList(Request $request)
    {

        $id = $request->input('facility_id');
        $search = $request->input('name');
        $user_id = $request->input('user_id');
        $allowed = true;
        $sql = "SELECT * FROM `Vw_xray_orders` WHERE facility_id ='".$id."'
         AND medical_record_number LIKE '%".$search."%'  GROUP BY OrderId LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function prevReqRecord(Request $request)
    {
        $id = $request->input('patient_id');
        $sql = "SELECT date_attended,patient_id,created_at FROM `tbl_accounts_numbers` WHERE patient_id =".$id." ORDER BY date_attended DESC LIMIT 5 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function getRequestFormData(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "SELECT * FROM `vw_xray_que` WHERE (`status_id`=2  OR `status_id`=1 AND `main_category_id` != 1) OR `onetime`=1 AND patient_id =".$id." AND visited_date='".$date."'
           LIMIT 10 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function doctorRequest(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "SELECT * FROM `vw_xray_orders` WHERE  patient_id =".$id." AND created_at='".$date."'
           LIMIT 10 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function verifyPerPatients(Request $request)
    {
        $id = $request->input('patient_id');
        $view = DB::table('tbl_results')
            ->join('tbl_orders','tbl_orders.id','=','tbl_results.order_id')
            ->join('tbl_requests','tbl_requests.id','=','tbl_orders.order_id')
            ->join('tbl_patients','tbl_patients.id','=','tbl_requests.patient_id')
            ->select('tbl_patients.id as patient_id','tbl_patients.first_name',
                'tbl_patients.middle_name','tbl_patients.last_name',
                'tbl_patients.medical_record_number',
                'tbl_results.order_id','tbl_results.description')
            ->where ('tbl_requests.visit_date_id','tbl_orders.visit_date_id')
            ->where (DB::Raw("tbl_requests.visit_date_id"), DB::Raw("tbl_results.visit_date_id"))
            ->where ('tbl_results.eraser',1)
            ->where ('confirmation_status',0)
            ->where ('patient_id',$id)
            ->groupBy ('tbl_results.order_id')
            ->groupBy ('tbl_patients.id')
            ->groupBy ('tbl_patients.first_name')
            ->groupBy ('tbl_patients.middle_name')
            ->groupBy ('tbl_patients.last_name')
            ->groupBy ('tbl_patients.medical_record_number')
            ->get();
        return $view;

    }


    public function getRegisteredServices(Request $request)
    {
        $status=1;
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_opd_patients` WHERE (`payment_status_id`=2  OR `payment_status_id`=1 AND `payment_filter` IS NOT NULL) AND facility_id =".$id." AND status=".$status."
         GROUP BY patient_id LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function PatientsXray($id)
    {
        $sql = "SELECT * FROM `vw_xray_orders` WHERE (`payment_status_id`=2  OR `payment_status_id`=1 AND `main_category_id` != 1) AND facility_id =".$id."
         AND OrderId NOT IN (SELECT order_id FROM tbl_results)  GROUP BY patient_id LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }


    public function xrayImage(Request $request){
        //return $request->all();
        $date = date('Y-m-d h:i:s');
        $status = 0;
        $update = 3;
        $file =0;
        $user=0;
        $visit_date_id = Tbl_request::find($request->order)->visit_date_id;
		$request["visit_date_id"] = $visit_date_id;
		
        while (Input::hasFile($file)) {
            $destinationPath = 'uploads'; // upload path
            $fileName =  $request->patient_id.'-'.date('dmyhis').'-'.rand(11111,99999).'.jpg'; // renameing image

            if(Input::file($file)->move($destinationPath, $fileName)){
                $admin = new Tbl_result($request->all());
                $admin['attached_image']=$fileName;
                $admin['description']=$request->explanation;
                $admin['visit_date_id']=$visit_date_id;
                $admin['order_id']=$request->order;
                $admin['item_id']=$request->item_id;
                $admin['post_user']=$request->post_user;
                $admin['post_time']=$date;
                $admin['confirmation_status']=$status;
                $admin['eraser']=1;

                Tbl_order::where('order_id',$request->order)->where('visit_date_id', $visit_date_id)
                    ->where('test_id',$request->item_id)
                    ->update([
                        'processor_id'=> $request->post_user,
                        'order_status'=>1
                    ]);
					$newData=$admin;
                    $patient_id=$request->patient_id;
                    $trackable_id=$newData->id;
					$user_id=$request->post_user;
                    SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);


                if(!$admin->save()){
                    return response("Error Encounted: Failed to save", 101);
                }
            } else{

                return response("UNABLE TO UPLOAD FILE");
            }// uploading file to given path
            $file++;
        }
        return response("IMAGE WAS SUCCESSFULLY UPLOADED.");
    }

//    Xray User with no image
    public function SaveImage(Request $request)
    {
        $date = date('Y-m-d h:i:s');
        $visit_date_id = Tbl_request::find($request['order_id'])->visit_date_id;
		
		$data = new Tbl_result($request->all());
		$data['description']=$request->description;
		$data['visit_date_id']=$visit_date_id;
		$data['order_id']=$request->order_id;
		$data['item_id']=$request->item_id;
		$data['post_user']=$request->post_user;
		$data['post_time']=$date;
		$data['confirmation_status']=$request->confirmation_status;
		$data['eraser']=1;
				
       
	   if($data->save()){
		   Tbl_order::where('order_id',$request->order_id)->where('visit_date_id', $visit_date_id)
				->where('test_id',$request->item_id)
				->update([
					'processor_id'=> $request->post_user,
					'order_status'=>1
				]);

			$newData=$data;
			$patient=Tbl_request::where('id',$newData->order_id)->get();
			$patient_id=$patient[0]->patient_id;
			$trackable_id=$newData->id;
			$user_id=$request->post_user;
			SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

			return $data;
	   }else{
		   return response("Error Encounted: Failed to save", 101);
       }
    }

//Get xray from patients


    public function getXrayImage()
    {
        $view = DB::table('tbl_results')
            ->join('tbl_requests','tbl_requests.id','=','tbl_results.order_id')
            ->join('tbl_patients','tbl_patients.id','=','tbl_requests.patient_id')
            ->select('tbl_patients.id as patient_id','tbl_patients.first_name',
                'tbl_patients.middle_name','tbl_patients.last_name',
                'tbl_patients.medical_record_number',
                'tbl_results.order_id')
            ->where (DB::Raw("tbl_requests.visit_date_id"), DB::Raw("tbl_results.visit_date_id"))
            ->where ('tbl_results.eraser',1)
            ->where ('confirmation_status',0)
            ->groupBy ('tbl_patients.id')
            ->get();
        return $view;

    }

//Department Registration
    public function departmentRegistration(Request $request)
    {foreach ($request->all() as $key => $value)
        $request[$key] = strtoupper($value);
        $subdept=$request['sub_department_name'];
        $dept=$request['department_id'];
        if ($dept=="") {
            return response()->json([
                'msg' => "Please fill  Department",
                'status' => 0]);
        }
        if
        ($subdept==""){
            return response()->json([
                'msg'=>"Please fill  Sub-Department",
                'status'=>0
            ]) ;
        }
        $data=Tbl_sub_department::where('sub_department_name',$subdept)
            ->count();
        if($data >0)
        {
            return response()->json([
                'msg'=>$subdept." Already exist",
                'status'=>0
            ]) ;
        }
        else{

            Tbl_sub_department::create($request->all());
            return response()->json([
                'msg'=>$subdept." Successful Registered",
                'status'=>1
            ]) ;
        }
    }


    //    Register Status
    public function statusRegistration(Request $request)
    {
        $status_name=$request['status_name'];
        $data=Tbl_equipment_status::where('status_name',$status_name)
            ->first();
        if(count($data)>0)
        {
            return response()->json([
                'msg'=>$status_name." Already registered",
                'status'=>0
            ]) ;
        }
        else{

            Tbl_equipment_status::create($request->all());
            return response()->json([
                'msg'=>$status_name." Successfully Registered",
                'status'=>1
            ]) ;
        }
    }

//    INVESTIGATION REGISTRATION
    public function InvestigationRegistration(Request $request)
    {
        $item_name=$request['item_name'];
        $data=Tbl_item::where('item_name',$item_name)
            ->first();
        if(count($data)>0)
        {
            return response()->json([
                'msg'=>$item_name." Already registered",
                'status'=>0
            ]) ;
        }
        else{

            Tbl_item::create($request->all());
            return response()->json([
                'msg'=>$item_name." Successfully Registered",
                'status'=>1
            ]) ;
        }
    }



    public function InvestigationPart(Request $request)
    {
        $item_name=$request['item_name'];

        $data = Tbl_item_type_mapped::create([
            'item_id'=>$request['item_id'],
            'item_name'=>$request['item_name'],
            'item_category'=>$request['item_category'],
            'sub_item_category'=>$request['sub_item_category']
        ]);
        return response()->json([
            'msg'=>$item_name." Successfully Registered",
            'status'=>1
        ]) ;


    }
    public function getdepartments()
    {

        $view = DB::table('tbl_departments')
            ->join('tbl_sub_departments','tbl_departments.id','=','tbl_sub_departments.department_id')
            ->where ('eraser',1)
            ->where ('department_id',3)
            ->get();
        return $view;

    }
    public function getAllRadiographics(Request $request)
    {
        $date = Carbon::yesterday();
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_xray_orders` WHERE (`payment_status_id`=2 AND `visit_date`>='".$date."' OR `payment_status_id`=1 AND `visit_date`>='".$date."' AND `payment_filter` IS NOT NULL) OR `onetime`=1
        AND facility_id =".$id." LIMIT 20 ";

        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function investigationData()

    {

        $view = DB::table('tbl_items')
            ->where ('dept_id',3)
            ->get();
        return $view;

    }

//    LOAD ITEM CATEGORY
    public function getItemCategory()
    {

        $view = DB::table('tbl_item_categories')
            ->get();
        return $view;

    }
    //    Get registered departments
    public function getRegistered_departments()
    {
        // return Tbl_department::get();
        return DB::table('tbl_departments')
            ->where('id',3)
            ->get();
    }

//Equipment Registration
    public function equipmentRegistration(Request $request)
    {
        return  Tbl_equipment::create($request->all());
    }


    public function userRegistration(Request $request)
    {
        $user_id=$request['user_id'];
        $subdept_id=$request['subdept_id'];
        if ($user_id=="") {
            return response()->json([
                'msg' => "Please select  User",
                'status' => 0]);
        }
        if
        ($subdept_id==""){
            return response()->json([
                'msg'=>"Please Select  Sub-Department",
                'status'=>0
            ]) ;
        }

        else{

            Tbl_imaginguser::create($request->all());
            return response()->json([
                'msg'=>"User Successful Registered",
                'status'=>1
            ]) ;
        }

    }

    public function usersSubdepartments($facility_id)
    {
        $view = DB::table('tbl_imagingusers')
            ->join('users','users.id','=','tbl_imagingusers.user_id')
            ->join('tbl_sub_departments','tbl_sub_departments.id','=','tbl_imagingusers.subdept_id')
            ->select('users.name','users.mobile_number',
                'tbl_sub_departments.sub_department_name','tbl_imagingusers.id as id_imaging')
            ->where('users.facility_id',$facility_id)
            ->where('tbl_sub_departments.eraser',1)
            ->where('tbl_imagingusers.grant',1)

            ->get();
        return $view;
    }
//    GET REJESTA REPORT
    public function getRejestaReporti($facility_id)
    {
        $view = DB::table('vw_imaging_rejesta')
            ->where('facility_id',$facility_id)
            ->get();
        return $view;
    }

    public function getRejestaReport(Request $request)
    {
        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT SUM(quantity*price-discount) AS sub_total,SUM(quantity) AS total_items,SUM(discount) AS total_discount,created_at,receipt_number,first_name,middle_name,last_name,user_name,account_number,dob,gender,medical_record_number ,item_name,age,sub_item_category
       FROM `vw_imaging_rejesta` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND status_id = 2 GROUP BY receipt_number";
        $detailed = DB::select(DB::raw($sql));
        return $detailed;
    }

    public function requestedInvestigation(Request $request)
    {

        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT * FROM `vw_xray_orders` WHERE invoice_created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND payment_status_id = 2";
        $detailed = DB::select(DB::raw($sql));
        return $detailed;

    }

    public function skullInvestigation(Request $request)
    {

        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT * FROM `vw_xray_orders` WHERE invoice_created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND sub_item_category = 'skull'";
        $detailed = DB::select(DB::raw($sql));
        return $detailed;

    }
    public function chestInvestigation(Request $request)
    {

        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT * FROM `vw_xray_orders` WHERE invoice_created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND sub_item_category = 'chest'";
        $detailed = DB::select(DB::raw($sql));
        return $detailed;

    }
    public function abdomenInvestigation(Request $request)
    {

        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT * FROM `vw_xray_orders` WHERE invoice_created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND sub_item_category = 'abdomen'";
        $detailed = DB::select(DB::raw($sql));
        return $detailed;

    }
    public function spineInvestigation(Request $request)
    {

        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT * FROM `vw_xray_orders` WHERE invoice_created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND sub_item_category = 'spire'";
        $detailed = DB::select(DB::raw($sql));
        return $detailed;

    }
    public function pelvisInvestigation(Request $request)
    {

        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT * FROM `vw_xray_orders` WHERE invoice_created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND sub_item_category = 'pelvis'";
        $detailed = DB::select(DB::raw($sql));
        return $detailed;

    }
    public function extremitiesInvestigation(Request $request)
    {

        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT * FROM `vw_xray_orders` WHERE invoice_created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND sub_item_category = 'extremities'";
        $detailed = DB::select(DB::raw($sql));
        return $detailed;

    }
    public function HSGInvestigation(Request $request)
    {

        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT * FROM `vw_xray_orders` WHERE invoice_created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND sub_item_category = 'HSG'";
        $detailed = DB::select(DB::raw($sql));
        return $detailed;

    }

    //Service Registration
    public function serviceRegistration(Request $request)
    {
        $subdept=$request['sub_department_id'];
        $equip_id=$request['equipment_id'];
        $item_id=$request['item_id'];
        if ($subdept=="") {
            return response()->json([
                'msg' => "Please select  Sub-Department",
                'status' => 0]);
        }
        if
        ($equip_id==""){
            return response()->json([
                'msg'=>"Please Select  Device",
                'status'=>0
            ]) ;
        }
        if
        ($item_id==""){
            return response()->json([
                'msg'=>"Please Select Service",
                'status'=>0
            ]) ;
        }

        $data=Tbl_test::where('item_id',$item_id)
            -> where('equipment_id',$equip_id)
            -> where('sub_department_id',$subdept)
            -> where('eraser',1)
            ->get();
        if(count($data)>0)
        {
            return response()->json([
                'msg'=>"Service Exists",
                'status'=>0
            ]) ;
        }

        else{

            Tbl_test::create($request->all());
            return response()->json([
                'msg'=>"Service Successful Registered",
                'status'=>1
            ]) ;
        }


    }

    public function getEquipments_list()
    {
        return DB::table('tbl_equipments')
            ->join('tbl_sub_departments','tbl_sub_departments.id','=','tbl_equipments.sub_department_id')
            ->where('tbl_sub_departments.department_id',3)
            ->get();

        //return Tbl_equipment::get();
    }

    public function deviceServices($facility_id)
    {
        return DB::table('vw_equipment_status')
            //->where ('dept_id',3)
            ->where ('deleted',1)
            ->where ('service_Deleted',1)
            ->where ('facility_id',$facility_id)
            ->get();
    }
    public function OnnOffDevices($facility_id)
    {
        return DB::table('tbl_equipments')
            ->join('tbl_equipment_statuses', 'tbl_equipment_statuses.id', '=', 'tbl_equipments.equipment_status_id')
            ->join('tbl_sub_departments', 'tbl_sub_departments.id', '=', 'tbl_equipments.sub_department_id')
            ->select('tbl_equipments.equipment_name','tbl_equipments.id as equipment_id','tbl_equipments.description'
                ,'tbl_equipment_statuses.status_name','tbl_equipment_statuses.on_off','tbl_sub_departments.department_id','tbl_sub_departments.sub_department_name')
            ->where ('facility_id',$facility_id)
            ->where ('tbl_sub_departments.department_id',3)
            ->get();
    }
    public function deniedDevices($facility_id)
    {
        return DB::table('vw_equipment_status')
            // ->where('dept_id',3)
            ->where('deleted',1)
            ->where('service_Deleted',1)
            ->where('on_off',0)
            ->where('facility_id',$facility_id)
            ->get();

    }

//    Get equipment with status
    public function getEquipments_status($facility_id)
    {
        return  DB::table('tbl_equipments')

            ->join('tbl_equipment_statuses', 'tbl_equipments.equipment_status_id', '=', 'tbl_equipment_statuses.id')
            ->join('tbl_sub_departments','tbl_sub_departments.id','=','tbl_equipments.sub_department_id')
            ->select('tbl_equipments.*','tbl_equipment_statuses.id as status_id','tbl_equipment_statuses.status_name')

            ->where('tbl_equipments.facility_id',$facility_id)
            ->where('tbl_sub_departments.department_id',3)
            ->get();

    }
//    Get equipments List
    public function getEquipments()
    {
        return Tbl_sub_department::get();
    }
//    Get items List
    public function getServicedata(Request $request)
    {
        $id = $request->input('facility_id');
        $search = $request->input('search');
        $sql = "SELECT * FROM `vw_shop_items` WHERE item_name LIKE '%".$search."%' AND (`dept_id`=3 ) AND facility_id ='".$id."' GROUP BY item_id ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function getUsers(Request $request)
    {
        $id = $request->input('facility_id');
        $search = $request->input('search');
        $sql = "SELECT * FROM `users` WHERE name LIKE '%".$search."%' AND facility_id ='".$id."' ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function assignPermission(Request $request)
    {

        $section_id = $request['section_id'];
        $technologist_id = $request['technologist_id'];
        $isAllowed = $request['isAllowed'];
        $data = Tbl_staff_section::
        where('section_id', $section_id)
            ->where('technologist_id', $technologist_id)
            ->get();
        if (count($data) > 0) {
            return response()->json([
                'message' => " USER HAS PERMISSION ON SUB-DEPARTMENT",
                'status' => 0
            ]);
        } else {
            Tbl_staff_section::create([
                'section_id' => $section_id,
                'technologist_id' => $technologist_id,
                'isAllowed' => $isAllowed
            ]);

            return   response()->json([
                'message' => "PERMISSION GRANTED ",
                'status' => 1
            ]);
        }

    }

    public function permittedUsers(Request $request)
    {
        $section_id = $request->input('section_id');
        $facility_id = $request->input('facility_id');
        $sql ="select * from vw_radiology_users WHERE section_id ='".$section_id."'  and facility_id ='".$facility_id."'  ";
        return DB::select(DB::raw($sql));
    }
    public function userPermittedUpdates(Request $request)
    {

        $permission = $request['permission'];
        $userAccess = $request['userAccess'];
        $dept_id = $request['dept_id'];

        Tbl_staff_section::where('technologist_id',$userAccess)
            ->where('section_id', $dept_id)
            ->update([
                'isAllowed'=>$permission
            ]);
        return response()->json([
            'message' => "PERMISSION UPDATED",
            'status' => 1
        ]);


    }

    public function getUsersFromXrays(Request $request){
        return $request->all();


    }

    //    Get devices
    public function deviceName($facility_id)
    {
        return Tbl_equipment::where('facility_id',$facility_id)
            ->join('tbl_sub_departments','tbl_sub_departments.id','=','tbl_equipments.sub_department_id')
            ->select('tbl_equipments.id as equip_id','tbl_equipments.equipment_name','tbl_equipments.equipment_status_id')
            ->where('tbl_sub_departments.department_id',3)
            ->where('tbl_equipments.eraser',1)

            ->get();
    }


//    Get equipment Status list
    public function getEquipmentStatus()
    {
        return Tbl_equipment_status::where('id','>',5)
            ->get();
    }

//    Get equipment Diagnosed
    public function getdiagnosis($patient_id)
    {

        $view = DB::table('tbl_diagnosis_details')
            ->join('tbl_diagnoses','tbl_diagnoses.id','=','tbl_diagnosis_details.diagnosis_id')
            ->join('tbl_diagnosis_descriptions','tbl_diagnosis_descriptions.id','=','tbl_diagnosis_details.diagnosis_description_id')
            ->join('tbl_patients','tbl_patients.id','=','tbl_diagnoses.patient_id')
            ->select('tbl_diagnosis_details.status as DiagnosticStatus','tbl_patients.first_name',
                'tbl_patients.middle_name','tbl_patients.last_name',
                'tbl_diagnoses.patient_id',
                'tbl_diagnoses.visit_date_id','tbl_diagnoses.facility_id','tbl_diagnosis_descriptions.description','tbl_diagnosis_descriptions.created_at')
            ->where('tbl_diagnoses.patient_id',$patient_id)

            ->get();
        return $view;


    }


//Update equipment status
    public function statusUpdate(Request $request)
    {
        $id=$request->id;
        return Tbl_equipment_status::where('id',$id)->update($request->all());

    }

    //Update equipment status
    public function resusUser(Request $request)
    {

        $id=$request['id'];
        $del_user=$request['del_user'];
        return Tbl_imaginguser::where('id',$id)
            ->update([
                'del_user'=>$del_user,
                'grant'=>0

            ]);

    }

//            Update department
    public function departmentUpdate(Request $request)
    {
        $id=$request->id;
        return Tbl_sub_department::where('id',$id)->update($request->all());

    }
//Update status and devices
    public function RadiologyUpdate(Request $request)
    {
        $equipment_id=$request['description_id'];
        $status_id=$request['equipment_status_id'];
        $description=$request['description'];
        $condition=$request['condition'];
        $facility_id=$request['facility_id'];
        $user_id=$request['user_id'];
        if($status_id==""){
            $status_id=$request['statuses_id'];
        }
        if($description==""){
            $description=$request['descriptions'];
        }
        if($condition==""){
            $condition=$request['conditions'];
        }

        $name=$request['equipment_name'];
        $data=  Tbl_equipment::where('id',$equipment_id)
            ->update([
                'description'=>$description,
                'conditions'=>$condition,
                'equipment_name'=>$name,
                'equipment_status_id'=>$status_id,
                'facility_id'=>$facility_id,
                'user_id'=>$user_id
            ]);



        return $data;

    }



    public function SearchPatientInXray(Request $request)
    {
        $date = Carbon::yesterday();
        $search= $request['searchKey'];
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_xray_orders` WHERE (`payment_status_id`=2 AND `visit_date`>='".$date."' OR `payment_status_id`=1 AND `visit_date`>='".$date."' AND `payment_filter` IS NOT NULL) OR `onetime`=1 AND facility_id =".$id." LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function loadPatientRadiologyRequest(Request $request)
    {

        $date = date('Y-m-d');
        $search= $request['searchKey'];
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_xray_orders` WHERE (`payment_status_id`=2 AND `visit_date`='".$date."' OR `payment_status_id`=1 AND `visit_date`='".$date."' AND `payment_filter` IS NOT NULL) AND facility_id =".$id." LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;

    }

    public function departmentDelete($id)
    {
        return Tbl_sub_department::where('id',$id)->update(
            ['eraser'=>0]
        ) ;
    }

    public function DeleteXray(Request $request)
    {

        $verified=$request['verify_user'];
        $id=$request['id'];
        return Tbl_result::where('id',$id)->update
        ([
            'eraser'=>0,
            'verify_user'=>$verified

        ]);
    }

    public function statusDelete($id)
    {
        return Tbl_equipment_status::where('id',$id)->update(
            ['eraser'=>0]
        ) ;
    }

    public function imageStatus($patient_id)
    {
        $view = DB::table('tbl_results')
            ->join('tbl_requests','tbl_requests.id','=','tbl_results.order_id')
            ->join('tbl_patients','tbl_patients.id','=','tbl_requests.patient_id')
            ->select('tbl_patients.id as patient_id','tbl_patients.first_name',
                'tbl_patients.middle_name','tbl_patients.last_name',
                'tbl_patients.medical_record_number','tbl_patients.mobile_number',
                'tbl_results.order_id','tbl_results.id as resulted','tbl_results.description','tbl_results.attached_image')
            ->where (DB::Raw("tbl_requests.visit_date_id"), DB::Raw("tbl_results.visit_date_id"))
            ->where ('tbl_results.eraser',1)
            ->where ('confirmation_status',0)
            ->where ('patient_id',$patient_id)
            ->orderBy('tbl_results.created_at','desc')
            ->limit(20)
            ->get();
        return $view;
    }

    public function VerifyXrays(Request $request){
        $date = date('Y-m-d h:i:s');
        $remarks=$request['remarks'];
        $first_name=$request['first_name'];
        $middle_name=$request['middle_name'];
        $last_name=$request['last_name'];
        $mobile_number=$request['mobile_number'];
        $verify=$request['verify_user'];
        $id=$request['id'];
        $extracted=substr($mobile_number,1);
        $mobile="255".$extracted;
        /*       Nexmo::message()->send([
                   'to' => $mobile,
                    'from' => 'GoT-HoMIS',
                    'text' =>'NDUGU MTEJA '. $first_name.' '.$middle_name.' '.$last_name.' VIPIMO ULIVYOFANYA IDAYA YA MIONZI VIMEPELEKWA KWA DAKTARI. '
                ]);*/
        return Tbl_result::where('id',$id)->update
        ([
            'confirmation_status'=>1,
            'verify_time'=>$date,
            'verify_user'=>$verify,
            'remarks'=>$remarks
        ]);
    }
    public function verifyPerRequests(Request $request){
        $date = date('Y-m-d h:i:s');
        $verify=$request['verify_user'];
        $id=$request['order_id'];
        return Tbl_result::where('order_id',$id)->update
        ([
            'confirmation_status'=>1,
            'verify_time'=>$date,
            'verify_user'=>$verify
        ]);
    }

    public function equipmentOnOff(Request $request){
        $equipment=$request['equipment_name'];
        $status=$request['equipment_status'];
        $facility=$request['facility_id'];
        $user_id=$request['user_id'];
        return Tbl_equipment::where('id',$equipment)->update
        ([
            'equipment_status_id'=>$status,
            'facility_id'=>$facility,
            'user_id'=>$user_id

        ]);
    }

    public function ServiceDelete($id)
    {
        return Tbl_test::where('id',$id)->update(
            ['eraser'=>0]
        ) ;
    }

    public function getUserDepartment ($facility_id)
    {
        return DB::table('vw_user_details')
            ->where ('facility_id',$facility_id)
            ->where ('proffesionals_id',4)
            ->get();

    }


}