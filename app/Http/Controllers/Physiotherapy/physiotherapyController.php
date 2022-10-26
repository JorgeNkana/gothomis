<?php

namespace App\Http\Controllers\Physiotherapy;

use App\Clinics\Tbl_follow_up_status;
use App\Physiotherapy\Tbl_therapy_assessment;
use App\Physiotherapy\Tbl_therapy_treatment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\clinic\ctc\Tbl_clinic_attendance;
use App\Clinics\Tbl_clinic_instruction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class physiotherapyController extends Controller
{
    public function getPhysioPatients(Request $request)
    {
        $date = Carbon::yesterday();
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_special_clinics_clients` WHERE (`visit_date`>='".$date."' AND `dept_id`=40 AND `received`=0)
         AND facility_id =".$id." GROUP BY patient_id LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function postfamily(Request $request)
    {
        $family = $request['family'];
        $patient_id = $request['patient_id'];
        $user_id = $request['user_id'];
        $visit_id = $request['visit_id'];
        $facility_id = $request['facility_id'];
        Tbl_therapy_treatment::create([
                'patient_id' => $patient_id,
                'user_id' => $user_id,
                'family' => $family,
                'facility_id' => $facility_id,
                'visit_date_id' => $visit_id
            ]);
            return response()->json([
                'message' =>"FAMILY AND SOCIAL HISTORY  SUCCESSFULLY SAVED",
                'status' => 1
            ]);
        }

        public function saveSpecific(Request $request)
    {
        $specific = $request['specific'];
        $patient_id = $request['patient_id'];
        $user_id = $request['user_id'];
        $visit_id = $request['visit_id'];
        $facility_id = $request['facility_id'];
        Tbl_therapy_assessment::create([
                'patient_id' => $patient_id,
                'user_id' => $user_id,
                'specific' => $specific,
                'facility_id' => $facility_id,
                'visit_date_id' => $visit_id
            ]);
            return response()->json([
                'message' =>"SPECIFIC EXAMINATION  SUCCESSFULLY SAVED",
                'status' => 1
            ]);
        }
        public function saveNeurology(Request $request)
    {
        $specific = $request['neurology'];
        $patient_id = $request['patient_id'];
        $user_id = $request['user_id'];
        $visit_id = $request['visit_id'];
        $facility_id = $request['facility_id'];
        Tbl_therapy_assessment::create([
                'patient_id' => $patient_id,
                'user_id' => $user_id,
                'neurological' => $specific,
                'facility_id' => $facility_id,
                'visit_date_id' => $visit_id
            ]);
            return response()->json([
                'message' =>"NEUROLOGICAL EXAMINATION  SUCCESSFULLY SAVED",
                'status' => 1
            ]);
        }
        public function saveSummary(Request $request)
    {
        $specific = $request['summary'];
        $patient_id = $request['patient_id'];
        $user_id = $request['user_id'];
        $visit_id = $request['visit_id'];
        $facility_id = $request['facility_id'];
        Tbl_therapy_assessment::create([
                'patient_id' => $patient_id,
                'user_id' => $user_id,
                'summary' => $specific,
                'facility_id' => $facility_id,
                'visit_date_id' => $visit_id
            ]);
            return response()->json([
                'message' =>"GENERAL SUMMARY  SUCCESSFULLY SAVED",
                'status' => 1
            ]);
        }
        public function saveGeneral(Request $request)
    {
        $specific = $request['general'];
        $patient_id = $request['patient_id'];
        $user_id = $request['user_id'];
        $visit_id = $request['visit_id'];
        $facility_id = $request['facility_id'];
        Tbl_therapy_assessment::create([
                'patient_id' => $patient_id,
                'user_id' => $user_id,
                'general' => $specific,
                'facility_id' => $facility_id,
                'visit_date_id' => $visit_id
            ]);
            return response()->json([
                'message' =>"GENERAL EXAMINATION  SUCCESSFULLY SAVED",
                'status' => 1
            ]);
        }
        public function saveAim(Request $request)
    {
        $specific = $request['aim'];
        $patient_id = $request['patient_id'];
        $user_id = $request['user_id'];
        $visit_id = $request['visit_id'];
        $facility_id = $request['facility_id'];
        Tbl_therapy_treatment::create([
                'patient_id' => $patient_id,
                'user_id' => $user_id,
                'aim' => $specific,
                'facility_id' => $facility_id,
                'visit_date_id' => $visit_id
            ]);
            return response()->json([
                'message' =>"AIM OF TREATMENT  SUCCESSFULLY SAVED",
                'status' => 1
            ]);
        }
        public function savePlans(Request $request)
    {
        $specific = $request['plans'];
        $patient_id = $request['patient_id'];
        $user_id = $request['user_id'];
        $visit_id = $request['visit_id'];
        $facility_id = $request['facility_id'];
        Tbl_therapy_treatment::create([
                'patient_id' => $patient_id,
                'user_id' => $user_id,
                'plans' => $specific,
                'facility_id' => $facility_id,
                'visit_date_id' => $visit_id
            ]);
            return response()->json([
                'message' =>"PLANS OF TREATMENT  SUCCESSFULLY SAVED",
                'status' => 1
            ]);
        }
        public function saveTreatment(Request $request)
    {
        $specific = $request['treatment'];
        $patient_id = $request['patient_id'];
        $user_id = $request['user_id'];
        $visit_id = $request['visit_id'];
        $facility_id = $request['facility_id'];
        Tbl_therapy_treatment::create([
                'patient_id' => $patient_id,
                'user_id' => $user_id,
                'evaluation' => $specific,
                'facility_id' => $facility_id,
                'visit_date_id' => $visit_id
            ]);
            return response()->json([
                'message' =>"EVALUATION OF TREATMENT  SUCCESSFULLY SAVED",
                'status' => 1
            ]);
        }
        public function saveWorkingDiagnosis(Request $request)
    {
        $specific = $request['working'];
        $patient_id = $request['patient_id'];
        $user_id = $request['user_id'];
        $visit_id = $request['visit_id'];
        $facility_id = $request['facility_id'];
        Tbl_therapy_treatment::create([
                'patient_id' => $patient_id,
                'user_id' => $user_id,
                'working' => $specific,
                'facility_id' => $facility_id,
                'visit_date_id' => $visit_id
            ]);
            return response()->json([
                'message' =>"WORKING DIAGNOSIS  SUCCESSFULLY SAVED",
                'status' => 1
            ]);
        }

    public function getSearchPhysio(Request $request)
    {
        $id = $request->input('facility_id');
        $search = $request->input('searchKey');
        $sql = "SELECT * FROM `vw_special_clinics_clients` WHERE medical_record_number LIKE '%".$search."%' AND (`dept_id`=40)
         AND facility_id =".$id." GROUP BY patient_id LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function therapy_treatments(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_therapy_treatments where patient_id = ".$id." AND visit_date = '".$date."'
        AND aim IS NOT NULL ";
        $sql1 = "select * from vw_therapy_treatments where patient_id = ".$id." AND visit_date = '".$date."'
        AND plans IS NOT NULL ";
        $sql2 = "select * from vw_therapy_treatments where patient_id = ".$id." AND visit_date = '".$date."'
        AND evaluation IS NOT NULL ";
        $sql3 = "select * from vw_therapy_treatments where patient_id = ".$id." AND visit_date = '".$date."'
        AND working IS NOT NULL ";
        $therapy[] = DB::select(DB::raw($sql));
        $therapy[] = DB::select(DB::raw($sql1));
        $therapy[] = DB::select(DB::raw($sql2));
        $therapy[] = DB::select(DB::raw($sql3));
        return $therapy;
    }
    public function therapy_assessments(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_therapy_assessments where patient_id = ".$id." AND visit_date = '".$date."'
        AND neurological IS NOT NULL";
        $sql1 = "select * from vw_therapy_assessments where patient_id = ".$id." AND visit_date = '".$date."'
        AND summary IS NOT NULL";
        $sql2 = "select * from vw_therapy_assessments where patient_id = ".$id." AND visit_date = '".$date."'
        AND examination IS NOT NULL";
        $sql3 = "select * from vw_therapy_assessments where patient_id = ".$id." AND visit_date = '".$date."'
        AND general IS NOT NULL";
        $therapy[] = DB::select(DB::raw($sql));
        $therapy[] = DB::select(DB::raw($sql1));
        $therapy[] = DB::select(DB::raw($sql2));
        $therapy[] = DB::select(DB::raw($sql3));
        return $therapy;
    }


    public function physio_search(Request $request)
    {
        $id = $request->input('facility_id');
        $search = $request->input('name');
        $sql = "SELECT * FROM `vw_special_clinics_clients` WHERE medical_record_number LIKE '%".$search."%' AND (`dept_id`=40   AND `summary`IS NULL AND `received`=1) AND facility_id =".$id." GROUP BY patient_id ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function ongoingPhysio(Request $request)
    {
       $request->all();
        $dt = Carbon::now();
        $dt->toDateString();
        $dept_id = 40;
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_attendance_payee` WHERE (`payment_status_id`=2  OR `payment_status_id`=1 AND `main_category_id` != 1)
         AND facility_id =$id AND dept_id =$dept_id  GROUP BY patient_id LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }


    public function getReportedAppointment(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT * FROM `vw_continuation_notes` WHERE (`dept_id`=40) AND created_at BETWEEN '".$start."'  AND '".$end."'GROUP BY patient_id";
        $emergencyData = DB::select(DB::raw($sql));
        return $emergencyData;
    }
    public function reportsAppointments(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $emergency = 'MOTOR ACCIDENT';
        $users = DB::table('vw_continuation_notes')
            ->select('follow_up_status_description', DB::raw('count(follow_up_status_description) as total'))
            ->whereBetween('created_at',[$start,$end])
            ->groupBy('follow_up_status_description')
            ->get();
        return $users;
    }

    public function setAppointmentPhysio(Request $request)
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
    public function setContinuePhysio(Request $request)
    {

        $next_visit = $request['next_visit'];
        $refferal_id = $request['refferal_id'];
        $visit_id = $request['visit_id'];
        $follow = $request['follow_up_status'];
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
           $statuses = Tbl_follow_up_status::create([
                'follow_up_status_description' => $follow,
                'follow_up_status_code' => 1
            ]);
           $id = $statuses->id;
            Tbl_clinic_attendance::create([
                'next_visit' => $next_visit,
                'visit_id' => $visit_id,
                'refferal_id' => $refferal_id,
                'follow_up_status' => $id
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
    public function getPhysioPatientsFromDoctor(Request $request)
    {
        $date = Carbon::yesterday();
        $id = $request->input('facility_id');
        $sql = "SELECT * FROM `vw_special_clinics_clients` WHERE (`dept_id`=40 AND `received`=0 AND `summary`IS NOT  NULL)
         AND facility_id =".$id." GROUP BY patient_id LIMIT 20 ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

    public function appointment_referPhysio(Request $request)
    {
        $id = $request->input('facility_id');
        $search = $request->input('name');
        $sql = "SELECT * FROM `vw_special_clinics_clients` WHERE medical_record_number LIKE '%".$search."%' AND (`dept_id`=40   AND `summary`IS NOT  NULL) AND facility_id =".$id." GROUP BY patient_id ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }

}