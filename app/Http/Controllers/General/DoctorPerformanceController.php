<?php

namespace App\Http\Controllers\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use DB;
class DoctorPerformanceController extends Controller
{
    public function create (Request $request){
        $data     =  $request->all();
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $facility_id = $request->input('facility_id');
        $per_page =  (isset($data['per_page'])? $data['per_page'] : 100);
        $sql    =  "SELECT t1.facility_id,t1.doctor_id,count(id) AS total_clerked,t1.doctor_name,t1.prof_name FROM `vw_perfomances` t1
      WHERE t1.facility_id=".$facility_id." AND (time_treated BETWEEN '".$start_date."' AND '".$end_date."') 
      GROUP BY  t1.doctor_id,t1.doctor_name,t1.prof_name,facility_id";
        $users = DB::select(DB::raw($sql));
        $users    =  customPaginate($users, $per_page);
        return customApiResponse($users);
    }


}