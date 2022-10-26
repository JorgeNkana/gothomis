<?php

namespace App\Http\Controllers\Cardiac;

use App\Cardiac\Tbl_clinic_capacity;
use App\clinic\ctc\Tbl_clinic_attendance;
use App\Clinics\Tbl_clinic_instruction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class cardiacController extends Controller
{
    public function getloadedClinic()
    {
        $view = DB::table('tbl_departments')
            ->select('department_name as department', 'id as dept_id')
            ->where('id', '=', 33)
            ->get();
        return $view;
    }
    public function cardiacCapacity()
    {
        $view = DB::table('tbl_clinic_capacities')
            ->join('tbl_departments','tbl_departments.id','=','tbl_clinic_capacities.clinic_name_id')
            ->select('tbl_clinic_capacities.capacity as capacity', 'tbl_departments.department_name as department'
            ,'tbl_clinic_capacities.clinic_name_id')
            ->where('tbl_clinic_capacities.clinic_name_id', '=', 33)
            ->get();
        return $view;
    }
    public function saveCardioSetup(Request $request)
    {
        $capacity = $request['capacity'];
        $department = $request['department'];
        $clinic_name_id = $request['clinic_id'];
        $data = Tbl_clinic_capacity::where('clinic_name_id', $clinic_name_id)
            ->first();
        if (count($data) > 0) {
            return response()->json([
                'message' => $department . " ALREADY CONFIGURED",
                'status' => 0
            ]);
        } else {
            Tbl_clinic_capacity::create([
                'clinic_name_id' => $clinic_name_id,
                'capacity' => $capacity
            ]);
            return response()->json([
                'message' => $department . " SUCCESSFULLY REGISTERED",
                'status' => 1
            ]);
        }

    }
    public function setAppointmentCardiac(Request $request)
    {
        $next_visit = $request['next_visit'];
        $refferal_id = $request['refferal_id'];
        $visit_id = $request['visit_id'];
        $data = Tbl_clinic_attendance::
        where('next_visit', $next_visit)
        ->where('refferal_id', $refferal_id)
        ->where('visit_id', $visit_id)
            ->first();
        if (count($data) > 0) {
            return response()->json([
                'message' => " PATIENT HAS AN APPOINTMENT IN SCHEDULE",
                'status' => 0
            ]);
        } else {
            Tbl_clinic_attendance::create([
                'next_visit' => $next_visit,
                'visit_id' => $visit_id,
                'refferal_id' => $refferal_id
            ]);
            Tbl_clinic_instruction::where('visit_id',$visit_id)
                ->update([
                    'received'=>1
                ]);
            return response()->json([
                'message' => "APPOINTMENT SUCCESSFULLY SET",
                'status' => 1
            ]);
        }

    }

    public function editCardioSetup(Request $request)
    {
        $capacity = $request['capacity'];
        $department = $request['department'];
        $clinic_name_id = $request['clinic_id'];
        $data=  Tbl_clinic_capacity::where('clinic_name_id',$clinic_name_id)
            ->update([
                'clinic_name_id'=>$clinic_name_id,
                'capacity'=>$capacity
            ]);

            return response()->json([
                'message' => $department . " SUCCESSFULLY UPDATED",
                'status' => 1
            ]);
    }
    public function getCardiacPatients(Request $request)
    {
        $date = Carbon::yesterday();
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_special_clinics_clients` WHERE (`visit_date`>='".$date."' AND `dept_id`=40 AND `received`=0)
         AND facility_id ='".$id."' GROUP BY patient_id LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function getCardiacPatientsFromDoctor(Request $request)
    {
        $date = Carbon::yesterday();
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_special_clinics_clients` WHERE (`visit_date`>='".$date."' AND `dept_id`=33 AND `received`=0 AND `summary`IS NOT  NULL)
         AND facility_id ='".$id."' GROUP BY patient_id LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function ongoingCardiac(Request $request)
    {
        $date = Carbon::today();
        $dept_id = 33;
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_attendance_payee` WHERE (`payment_status_id`=2  OR `payment_status_id`=1 AND `main_category_id` != 1)
         AND facility_id ='".$id."' AND dept_id =".$dept_id." GROUP BY patient_id LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function appointment_search(Request $request)
    {
        $id = $request->input('facility_id');
        $search = $request->input('name');
        $sql = "SELECT * FROM `vw_special_clinics_clients` WHERE medical_record_number LIKE '%".$search."%' AND (`dept_id`=33   AND `summary`IS NULL AND `received`=1) AND facility_id ='".$id."' GROUP BY patient_id ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function appointment_refer(Request $request)
    {
        $id = $request->input('facility_id');
        $search = $request->input('name');
        $sql = "SELECT * FROM `vw_special_clinics_clients` WHERE medical_record_number LIKE '%".$search."%' AND (`dept_id`=33   AND `summary`IS NOT  NULL) AND facility_id ='".$id."' GROUP BY patient_id ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

}