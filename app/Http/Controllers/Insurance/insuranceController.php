<?php

namespace App\Http\Controllers\Insurance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\classes\patientRegistration;
use App\classes\ServiceManager;
class insuranceController extends Controller
{
    public function nhifClaims(Request $request)
    {   $facility_id = $request->input('facility_id');
        $sql ="select * from vw_bills_payments where payment_filter=4 AND facility_id= '".$facility_id."' ";
        $nhif = DB::select(DB::raw($sql));
        return $nhif;
    }

    public function getNhifDates(Request $request)
    {
        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql ="select date_attended from vw_nhif_patients WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id= '".$facility_id."' GROUP BY date_attended ";
        $nhifDates = DB::select(DB::raw($sql));
        return $nhifDates;
    }
    public function getInsurancePatients(Request $request)
    {
        $facility_id = $request->input('facility_id');
        $date_attended = $request->input('date_attended');
        $sql ="select * from vw_nhif_patients WHERE date_attended = '".$date_attended."'  AND facility_id= '".$facility_id."'  ";
        $nhifDates = DB::select(DB::raw($sql));
        return $nhifDates;
    }
    public function getConfirmed(Request $request)
    {
        $id = $request->input('patient_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_prev_diagnosis where patient_id = '".$id."' AND date_attended = '".$date."' AND status='Confirmed' ";
        $cdiag = DB::select(DB::raw($sql));
        return $cdiag;
    }
    public function getConsultationFee($facility_id)
    {
        $sql = "select price from tbl_item_prices t1 
		INNER JOIN tbl_items t2 ON t2.id=t1.item_id WHERE facility_id = '".$facility_id."' AND item_name LIKE '%CONSULTATION%' GROUP BY t1.item_id LIMIT 1";
        $cdiag = DB::select(DB::raw($sql));
        return $cdiag;
    }
	
	public  function  createPatientFolio(Request $request){
      $data_string= patientRegistration::getInsurancePerPatient($request);
	  //patient particulars
      return ServiceManager::sendFolios($data_string);

  }

    public function investigationDone(Request $request)
    {
        $nhif=[];
        $id = $request->input('patient_id');
        $facility_id = $request->input('facility_id');
        $date = $request->input('date_attended');
        $sql = "select * from vw_bima_investigations where patient_id = '".$id."' AND date_attended = '".$date."' AND facility_id = '".$facility_id."'  GROUP BY inv_code";
        $cdiag = DB::select(DB::raw($sql));

        $nhif[]=$cdiag;
        $sql = "select medicine,medi_code,quantity,(quantity*medicine_price) AS sub_med_total from vw_bima_prescriptions where patient_id = '".$id."' AND start_date = '".$date."' AND facility_id = '".$facility_id."'  GROUP BY medi_code";
        $dawa = DB::select(DB::raw($sql));
        $nhif[]=$dawa;

 $procedures = "select item_name as procedure_name,item_category as procedure_category,proc_code,price as proc_price from vw_bimaProcedures where patient_id = '".$id."' AND date_attended = '".$date."' AND facility_id = '".$facility_id."'  ";
        $procedures1 = DB::select(DB::raw($procedures));
        $nhif[]=$procedures1;

        return $nhif;
    }


}