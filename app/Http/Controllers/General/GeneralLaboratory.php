<?php

namespace App\Http\Controllers\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Validator;
class GeneralLaboratory extends Controller
{
    /**
     * LOAD PATIENTS IN THE SAMPLE COLLECTION TAB.
     * @param  Request  $request
     * @return customApiResponse
     */
    public function investigationList(Request $request)
    {
        $request->all();
        $data = $request->all();
        $facility_id = $request->input('facility_id');
        $per_page = (isset($data['per_page']) ? $data['per_page'] : 50);

        $sql = "SELECT * FROM `vw_patients_with_pending_labrequests` WHERE facility_id ='" . $facility_id . "'  ";
        $users = DB::select(DB::raw($sql));
        $users = customPaginate($users, $per_page);
        return customApiResponse($users);
    }
    /**
     * RETRIEVE ORDER LIST FROM SAMPLE COLLECTION.
     * @param  Request  $request
     * @return customApiResponse
     */
    public function orderLists(Request $request)
    {
        $data = $request->all();
        $order_id = $request->input('order_id');
        $per_page = (isset($data['per_page']) ? $data['per_page'] : 50);
        $sql = "SELECT * FROM `vw_pending_labrequests` WHERE order_id ='" . $order_id . "'group by request_id ";
        $users = DB::select(DB::raw($sql));
        $users = customPaginate($users, $per_page);
        return customApiResponse($users);
    }
    /**
     * RETRIEVE PATIENTS IN SAMPLE TESTING FOR RESPECTIVE TESTS.
     * @param  Request  $request
     * @return customApiResponse
     */
    public function orderPerPatient(Request $request)
    {
        $data = $request->all();
        $patient_id = $request->input('patient_id');
        $per_page = (isset($data['per_page']) ? $data['per_page'] : 50);
       // $sql = "SELECT * FROM `vw_collectedSamples` WHERE patient_id ='" . $patient_id  . "' ORDER BY order_id DESC ";
        $sql = "SELECT * FROM `vw_collectedSamples` WHERE patient_id ='" . $patient_id  . "' AND NOT EXISTS (SELECT NULL  FROM tbl_results t1 WHERE
          t1.sample=vw_collectedSamples.sample_no) group by request_id";
        $users = DB::select(DB::raw($sql));
        $users = customPaginate($users, $per_page);
        return customApiResponse($users);
    }
    /**
     * LOAD PATIENTS IN THE SAMPLE TESTING TAB.
     * @param  Request  $request
     * @return customApiResponse
     */
    public function testList(Request $request)
    {
        $data = $request->all();
        $per_page = (isset($data['per_page']) ? $data['per_page'] : 50);
        $sql = "SELECT * FROM `vw_collectedSamples` WHERE (order_status=1 OR order_control=1)
        AND (timestampdiff(hour,created_at,CURRENT_TIMESTAMP) <=500 ) group by patient_id ";
        $users = DB::select(DB::raw($sql));
        $users = customPaginate($users, $per_page);
        return customApiResponse($users);
    }
    /**
     * LOAD PATIENTS  PATIENTS TO BE VERIFIED.
     * @param  Request  $request
     * @return $rs
     */
    public function investigationVerify(request $request){
        $rs = [];
        $rs[]=DB::SELECT('SELECT * FROM vw_results_get_approves t1 WHERE t1.panel IS  NULL AND (timestampdiff(hour,created_at,CURRENT_TIMESTAMP) <=24 )'); //SINGLE TEST..
        $rs[]=DB::SELECT('SELECT * FROM vw_results_get_approves t1 WHERE t1.panel IS NOT NULL AND (timestampdiff(hour,created_at,CURRENT_TIMESTAMP) <=24 )'); //PANEL TEST..

        return $rs;
    }

    /**
     * SEARCH PATIENTS IN THE SAMPLE COLLECTION.
     * @param  Request  $request
     * @return customApiResponse
     */
    public function labPatientsLists(Request $request)
    {   $data = $request->all();
        $name = $request['name'];
        $Patients = DB::table('vw_patients_with_pending_labrequests')
            ->where('first_name', 'like', '%' . $name . '%')
            ->orwhere('middle_name', 'like', '%' . $name . '%')
            ->orwhere('last_name', 'like', '%' . $name . '%')
            ->orwhere('medical_record_number', 'like', '%' . $name . '%')
            ->limit(10)
            ->get();
        return $Patients;
    }
    /**
     * SEARCH PATIENTS IN THE SAMPLE TESTING TAB.
     * @param  Request  $request
     * @return customApiResponse
     */
    public function investigationPatientsLists(Request $request)
    {   $data = $request->all();
        $name = $request['name'];
        $Patients = DB::table('vw_collectedSamples')
            ->where('first_name', 'like', '%' . $name . '%')
            ->orwhere('middle_name', 'like', '%' . $name . '%')
            ->orwhere('last_name', 'like', '%' . $name . '%')
            ->orwhere('medical_record_number', 'like', '%' . $name . '%')
            ->limit(10)
            ->groupBy('patient_id')
            ->get();
        return $Patients;
    }
    public function doctorPerformance (Request $request){
        $data     =  $request->all();
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $facility_id = $request->input('facility_id');
        $per_page = (isset($data['per_page']) ? $data['per_page'] : 100);
        $sql    =  "SELECT t1.facility_id,t1.doctor_id,count(id) AS total_clerked,t1.doctor_name,t1.prof_name FROM `vw_perfomances` t1
      WHERE prof_id =4 AND t1.facility_id=".$facility_id." AND (time_treated BETWEEN '".$start_date."' AND '".$end_date."') 
      GROUP BY  t1.doctor_id,t1.doctor_name,t1.prof_name,facility_id";
        $users = DB::select(DB::raw($sql));
        $users    =  customPaginate($users, $per_page);
        return customApiResponse($users);
    }
}