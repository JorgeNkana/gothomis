<?php

namespace App\Http\Controllers\reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

use Dompdf\Dompdf;

class reportsController extends Controller
{
	
	public function pdfPrinting(Request $request){
		// instantiate and use the dompdf class
		$dompdf = new Dompdf();
		$dompdf->loadHtml($request->html);
		$landscape = (isset($request['orientation']) ? $request['orientation'] : 'potrait');
		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', $landscape);

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream("PDF Format.pdf", array("Attachment" => false));
	}
	
	  public function reportsDrugs(request $request)
    {
        $response = [];
        $facility_id = $request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }
        $dataQuery="SELECT *        
        FROM `vw_os_drugs` t1 WHERE t1.facility_id='{$facility_id}' AND (date_out_of_stock BETWEEN  '{$start_date}' AND '{$end_date}')";
        $response[] = DB::select($dataQuery);


        return $response;
    }


    public function reportsUnavailableTests(request $request)
    {
        $response = [];
        $facility_id = $request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }
        $dataQuery="SELECT *     
        FROM `vw_unavailable_tests` t1 WHERE t1.facility_id='{$facility_id}' AND (date_out_of_stock BETWEEN  '{$start_date}' AND '{$end_date}')";
        $response[] = DB::select($dataQuery);


        return $response;
    }


    
     public function getMahudhurioOPD(request $request){
        $response = [];
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }
		
        $sql_1="SELECT 'Wagonjwa waliohudhuria kwa mara ya kwanza mwaka huo(*), kituo chochote nchini' as description, SUM(female_under_one_month) AS female_under_one_month ,SUM(male_under_one_month) AS male_under_one_month,SUM(total_under_one_month) AS total_under_one_month 
          ,SUM(female_under_one_year) AS female_under_one_year
          ,SUM(male_under_one_year) AS 	male_under_one_year
          ,SUM(total_under_one_year) AS total_under_one_year
          
         ,SUM(female_under_five_year) AS female_under_five_year
          ,SUM(male_under_five_year) AS male_under_five_year
          ,SUM(total_under_five_year) AS total_under_five_year
          
         ,SUM(female_above_five_under_sixty) AS female_above_five_under_sixty
          ,SUM(male_above_five_under_sixty) AS male_above_five_under_sixty
          ,SUM(total_above_five_under_sixty) AS total_above_five_under_sixty
        
        ,SUM(female_above_sixty) AS female_above_sixty
          ,SUM(male_above_sixty) AS male_above_sixty
          ,SUM(total_above_sixty) AS total_above_sixty
          
        ,SUM(total_female) AS total_female
          ,SUM(total_male) AS total_male
          ,SUM(grand_total) AS grand_total
        
        
         FROM `vw_newattendance_register` WHERE facility_id='{$facility_id}' AND clinic_id = 1 AND (date BETWEEN  date('$start_date') AND date('$end_date')) GROUP BY facility_id";
        $record = DB::select($sql_1);
		if(count($record) == 0){
			$none = new \ stdClass();
			$none->male_under_one_month=0;
			$none->female_under_one_month=0;
			$none->total_under_one_month=0;
			$none->male_under_one_year=0;
			$none->female_under_one_year=0;
			$none->total_under_one_year=0;
			$none->male_under_five_year=0;
			$none->female_under_five_year=0;
			$none->total_under_five_year=0;
			$none->male_above_five_under_sixty=0;
			$none->female_above_five_under_sixty=0;
			$none->total_above_five_under_sixty=0;
			$none->male_above_sixty=0;
			$none->female_above_sixty=0;
			$none->total_above_sixty=0;
			$none->grand_total_male=0;
			$none->grand_total_female=0;
			$none->grand_total=0;
			$none->description = 'Wagonjwa waliohudhuria kwa mara ya kwanza mwaka huo(*), kituo chochote nchini';
			$response[] = $none;
		}else
			$response[] = $record[0];
		

        $sql_2="SELECT 'Mahudhurio ya kwanza/wagonjwa wapya (kwenye kituo husika kwa tatizo fulani la kiafya)' as description,
			ifnull(sum(male_under_one_month),0) as male_under_one_month,
			ifnull(sum(female_under_one_month),0) as female_under_one_month, 
			ifnull(sum(total_under_one_month),0) as total_under_one_month,
			ifnull(sum(male_under_one_year),0) as male_under_one_year,
			ifnull(sum(female_under_one_year),0) as female_under_one_year,
			ifnull(sum(total_under_one_year),0) as total_under_one_year,
			ifnull(sum(male_under_five_year),0) as male_under_five_year,
			ifnull(sum(female_under_five_year),0) as female_under_five_year,
			ifnull(sum(total_under_five_year),0) as total_under_five_year,
			ifnull(sum(male_above_five_under_sixty),0) as male_above_five_under_sixty,
			ifnull(sum(female_above_five_under_sixty),0) as female_above_five_under_sixty,
			ifnull(sum(total_above_five_under_sixty),0) as total_above_five_under_sixty,
			ifnull(sum(male_above_sixty),0) as male_above_sixty,
			ifnull(sum(female_above_sixty),0) as female_above_sixty,
			ifnull(sum(total_above_sixty),0) as total_above_sixty,
			ifnull(sum(total_male),0) as total_male,
			ifnull(sum(total_female),0) as total_female,
			ifnull(sum(grand_total),0) as grand_total 
			FROM tbl_newattendance_registers
			WHERE (date BETWEEN  date('$start_date') AND date('$end_date'))";
	   $record2 = DB::select($sql_2);
		if(count($record2) == 0){
			$none = new \ stdClass();
			$none->male_under_one_month=0;
			$none->female_under_one_month=0;
			$none->total_under_one_month=0;
			$none->male_under_one_year=0;
			$none->female_under_one_year=0;
			$none->total_under_one_year=0;
			$none->male_under_five_year=0;
			$none->female_under_five_year=0;
			$none->total_under_five_year=0;
			$none->male_above_five_under_sixty=0;
			$none->female_above_five_under_sixty=0;
			$none->total_above_five_under_sixty=0;
			$none->male_above_sixty=0;
			$none->female_above_sixty=0;
			$none->total_above_sixty=0;
			$none->total_male=0;
			$none->total_female=0;
			$none->grand_total=0;
			$none->description = 'Mahudhurio ya kwanza/wagonjwa wapya (kwenye kituo husika kwa tatizo fulani la kiafya)';
			$response[] = $none;
		}else
			$response[] = $record2[0];
		
        $sql_3="SELECT 'Mahudhurio ya Marudio' as description,
			ifnull(sum(male_under_one_month),0) as male_under_one_month,
			ifnull(sum(female_under_one_month),0) as female_under_one_month, 
			ifnull(sum(total_under_one_month),0) as total_under_one_month,
			ifnull(sum(male_under_one_year),0) as male_under_one_year,
			ifnull(sum(female_under_one_year),0) as female_under_one_year,
			ifnull(sum(total_under_one_year),0) as total_under_one_year,
			ifnull(sum(male_under_five_year),0) as male_under_five_year,
			ifnull(sum(female_under_five_year),0) as female_under_five_year,
			ifnull(sum(total_under_five_year),0) as total_under_five_year,
			ifnull(sum(male_above_five_under_sixty),0) as male_above_five_under_sixty,
			ifnull(sum(female_above_five_under_sixty),0) as female_above_five_under_sixty,
			ifnull(sum(total_above_five_under_sixty),0) as total_above_five_under_sixty,
			ifnull(sum(male_above_sixty),0) as male_above_sixty,
			ifnull(sum(female_above_sixty),0) as female_above_sixty,
			ifnull(sum(total_above_sixty),0) as total_above_sixty,
			ifnull(sum(total_male),0) as total_male,
			ifnull(sum(total_female),0) as total_female,
			ifnull(sum(grand_total),0) as grand_total 
			FROM tbl_reattendance_registers
			WHERE (date BETWEEN  date('$start_date') AND date('$end_date'))";
		$record3 = DB::select($sql_3);
		if(count($record3) == 0){
			$none = new \ stdClass();
			$none->male_under_one_month=0;
			$none->female_under_one_month=0;
			$none->total_under_one_month=0;
			$none->male_under_one_year=0;
			$none->female_under_one_year=0;
			$none->total_under_one_year=0;
			$none->male_under_five_year=0;
			$none->female_under_five_year=0;
			$none->total_under_five_year=0;
			$none->male_above_five_under_sixty=0;
			$none->female_above_five_under_sixty=0;
			$none->total_above_five_under_sixty=0;
			$none->male_above_sixty=0;
			$none->female_above_sixty=0;
			$none->total_above_sixty=0;
			$none->total_male=0;
			$none->total_female=0;
			$none->grand_total=0;
			$none->description = 'Mahudhurio ya Marudio';
			$response[] = $none;
		}else
			$response[] = $record3[0];
	
		 
		$mtuha_diagnoses = DB::select("select id, description from tbl_opd_mtuha_diagnoses ORDER BY id");
		
		foreach($mtuha_diagnoses as $diagnosis){
			$sql_4="SELECT '".$diagnosis->description."' as description,ifnull(sum(male_under_one_month),0) as male_under_one_month,ifnull(sum(female_under_one_month),0) as female_under_one_month, ifnull(sum(total_under_one_month),0) as total_under_one_month,ifnull(sum(male_under_one_year),0) as male_under_one_year,ifnull(sum(female_under_one_year),0) as female_under_one_year,ifnull(sum(total_under_one_year),0) as total_under_one_year,ifnull(sum(male_under_five_year),0) as male_under_five_year,ifnull(sum(female_under_five_year),0) as female_under_five_year,ifnull(sum(total_under_five_year),0) as total_under_five_year,ifnull(sum(male_above_five_under_sixty),0) as male_above_five_under_sixty,ifnull(sum(female_above_five_under_sixty),0) as female_above_five_under_sixty,ifnull(sum(total_above_five_under_sixty),0) as total_above_five_under_sixty,ifnull(sum(male_above_sixty),0) as male_above_sixty,ifnull(sum(female_above_sixty),0) as female_above_sixty,ifnull(sum(total_above_sixty),0) as total_above_sixty,ifnull(sum(total_male),0) as total_male,ifnull(sum(total_female),0) as total_female,ifnull(sum(grand_total),0) as grand_total FROM tbl_opd_diseases_registers  WHERE opd_mtuha_diagnosis_id = ".$diagnosis->id."  AND facility_id ='$facility_id' AND date BETWEEN  date('$start_date') AND date('$end_date')";
			$record = DB::select($sql_4);
			if(count($record) == 0){
				$none->description = $diagnosis->description;
				$response[] = $none;
			}else
				$response[] = $record[0];
		}
		
		$others = DB::select("select 'Diagnoses, Other' as description,  ifnull(sum(male_under_one_month),0) as male_under_one_month,ifnull(sum(female_under_one_month),0) as female_under_one_month, ifnull(sum(total_under_one_month),0) as total_under_one_month,ifnull(sum(male_under_one_year),0) as male_under_one_year,ifnull(sum(female_under_one_year),0) as female_under_one_year,ifnull(sum(total_under_one_year),0) as total_under_one_year,ifnull(sum(male_under_five_year),0) as male_under_five_year,ifnull(sum(female_under_five_year),0) as female_under_five_year,ifnull(sum(total_under_five_year),0) as total_under_five_year,ifnull(sum(male_above_five_under_sixty),0) as male_above_five_under_sixty,ifnull(sum(female_above_five_under_sixty),0) as female_above_five_under_sixty,ifnull(sum(total_above_five_under_sixty),0) as total_above_five_under_sixty,ifnull(sum(male_above_sixty),0) as male_above_sixty,ifnull(sum(female_above_sixty),0) as female_above_sixty,ifnull(sum(total_above_sixty),0) as total_above_sixty,ifnull(sum(total_male),0) as total_male,ifnull(sum(total_female),0) as total_female,ifnull(sum(grand_total),0) as grand_total from tbl_opd_diseases_registers where opd_mtuha_diagnosis_id IS NULL AND facility_id ='$facility_id' AND date BETWEEN  date('$start_date') AND date('$end_date')");
		if(count($others) == 0){
			$none->description = 'Diagnoses, Other';
			$response[] = $none;
		}else
			$response[] = $others[0];
		
		
		$referrals = DB::select("select 'Waliopewa Rufaa' as description,  ifnull(sum(male_under_one_month),0) as male_under_one_month,ifnull(sum(female_under_one_month),0) as female_under_one_month, ifnull(sum(total_under_one_month),0) as total_under_one_month,ifnull(sum(male_under_one_year),0) as male_under_one_year,ifnull(sum(female_under_one_year),0) as female_under_one_year,ifnull(sum(total_under_one_year),0) as total_under_one_year,ifnull(sum(male_under_five_year),0) as male_under_five_year,ifnull(sum(female_under_five_year),0) as female_under_five_year,ifnull(sum(total_under_five_year),0) as total_under_five_year,ifnull(sum(male_above_five_under_sixty),0) as male_above_five_under_sixty,ifnull(sum(female_above_five_under_sixty),0) as female_above_five_under_sixty,ifnull(sum(total_above_five_under_sixty),0) as total_above_five_under_sixty,ifnull(sum(male_above_sixty),0) as male_above_sixty,ifnull(sum(female_above_sixty),0) as female_above_sixty,ifnull(sum(total_above_sixty),0) as total_above_sixty,ifnull(sum(total_male),0) as total_male,ifnull(sum(total_female),0) as total_female,ifnull(sum(grand_total),0) as grand_total from tbl_outgoing_referral_registers where facility_id ='$facility_id' AND date BETWEEN  date('$start_date') AND date('$end_date')");
		if(count($referrals) == 0){
			$none->description = 'Waliopewa Rufaa';
			$response[] = $none;
		}else
			$response[] = $referrals[0];
		return $response;
    }
	
	
 public function getStaffPerfomance(request $request){
        $response = [];
        $facility_id=$request->facility_id;
        $user_id=$request->user_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }

        $sql_1="SELECT SUM(number_registered) AS number_registered 
 
               FROM `vw_staff_perfomances` t1 WHERE t1.user_id='{$user_id}' AND  t1.facility_id='{$facility_id}' AND (date_attended BETWEEN  '{$start_date}' AND '{$end_date}')";
        $response[] = DB::select($sql_1);

       $sql_2="SELECT SUM(number_registered) AS number_registered 
 
               FROM `vw_staff_perfomances` t1 WHERE t1.user_id <> '{$user_id}' AND t1.facility_id='{$facility_id}' AND (date_attended BETWEEN  '{$start_date}' AND '{$end_date}')";
        $response[] = DB::select($sql_2);


        $response[]=$start_date;

        $response[]=$end_date;

        return $response;

    }


    
  public function getIpdReport(request $request)
    {
        $response = [];
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }
		
		$none = new \ stdClass();
		$none->male_under_one_month=0;
		$none->female_under_one_month=0;
		$none->total_under_one_month=0;
		$none->male_under_one_year=0;
		$none->female_under_one_year=0;
		$none->total_under_one_year=0;
		$none->male_under_five_year=0;
		$none->female_under_five_year=0;
		$none->total_under_five_year=0;
		$none->male_above_five_under_sixty=0;
		$none->female_above_five_under_sixty=0;
		$none->total_above_five_under_sixty=0;
		$none->male_above_sixty=0;
		$none->female_above_sixty=0;
		$none->total_above_sixty=0;
		$none->total_male=0;
		$none->total_female=0;
		$none->grand_total=0;
		
        $sql_1="SELECT 'Waliolazwa Wodini' as description, SUM(female_under_one_month) AS female_under_one_month ,SUM(male_under_one_month) AS male_under_one_month,SUM(total_under_one_month) AS total_under_one_month 
          ,SUM(female_under_one_year) AS female_under_one_year
          ,SUM(male_under_one_year) AS 	male_under_one_year
          ,SUM(total_under_one_year) AS total_under_one_year
          
         ,SUM(female_under_five_year) AS female_under_five_year
          ,SUM(male_under_five_year) AS male_under_five_year
          ,SUM(total_under_five_year) AS total_under_five_year
          
         ,SUM(female_above_five_under_sixty) AS female_above_five_under_sixty
          ,SUM(male_above_five_under_sixty) AS male_above_five_under_sixty
          ,SUM(total_above_five_under_sixty) AS total_above_five_under_sixty
        
        ,SUM(female_above_sixty) AS female_above_sixty
          ,SUM(male_above_sixty) AS male_above_sixty
          ,SUM(total_above_sixty) AS total_above_sixty
          
        ,SUM(total_female) AS total_female
          ,SUM(total_male) AS total_male
          ,SUM(grand_total) AS grand_total
        
        
         FROM `vw_admission_register` WHERE facility_id='{$facility_id}' AND (date BETWEEN  date('$start_date') AND date('$end_date')) GROUP BY facility_id";
		$record = DB::select($sql_1);
		if(count($record) == 0){
			$none->description = 'Waliolazwa Wodini';
			$response[] = $none;
		}else
			$response[] = $record[0];
		
		
		$mtuha_diagnoses = DB::select("select id, description from tbl_ipd_mtuha_diagnoses ORDER BY id");
		
		foreach($mtuha_diagnoses as $diagnosis){
			$sql_2="SELECT '".$diagnosis->id."' as diagnosis_id,'".$diagnosis->description."' as description,ifnull(sum(male_under_one_month),0) as male_under_one_month,ifnull(sum(female_under_one_month),0) as female_under_one_month, ifnull(sum(total_under_one_month),0) as total_under_one_month,ifnull(sum(male_under_one_year),0) as male_under_one_year,ifnull(sum(female_under_one_year),0) as female_under_one_year,ifnull(sum(total_under_one_year),0) as total_under_one_year,ifnull(sum(male_under_five_year),0) as male_under_five_year,ifnull(sum(female_under_five_year),0) as female_under_five_year,ifnull(sum(total_under_five_year),0) as total_under_five_year,ifnull(sum(male_above_five_under_sixty),0) as male_above_five_under_sixty,ifnull(sum(female_above_five_under_sixty),0) as female_above_five_under_sixty,ifnull(sum(total_above_five_under_sixty),0) as total_above_five_under_sixty,ifnull(sum(male_above_sixty),0) as male_above_sixty,ifnull(sum(female_above_sixty),0) as female_above_sixty,ifnull(sum(total_above_sixty),0) as total_above_sixty,ifnull(sum(total_male),0) as total_male,ifnull(sum(total_female),0) as total_female,ifnull(sum(grand_total),0) as grand_total FROM tbl_ipd_diseases_registers  WHERE ipd_mtuha_diagnosis_id = ".$diagnosis->id."  AND facility_id ='$facility_id' AND date BETWEEN  date('$start_date') AND date('$end_date')";
			$record = DB::select($sql_2);
			if(count($record) == 0){
				$none->description = $diagnosis->description;
				$response[] = $none;
			}else
				$response[] = $record[0];
				
		}
		
		$others = DB::select("select 0 as diagnosis_id, 'Diagnoses, Other' as description,  ifnull(sum(male_under_one_month),0) as male_under_one_month,ifnull(sum(female_under_one_month),0) as female_under_one_month, ifnull(sum(total_under_one_month),0) as total_under_one_month,ifnull(sum(male_under_one_year),0) as male_under_one_year,ifnull(sum(female_under_one_year),0) as female_under_one_year,ifnull(sum(total_under_one_year),0) as total_under_one_year,ifnull(sum(male_under_five_year),0) as male_under_five_year,ifnull(sum(female_under_five_year),0) as female_under_five_year,ifnull(sum(total_under_five_year),0) as total_under_five_year,ifnull(sum(male_above_five_under_sixty),0) as male_above_five_under_sixty,ifnull(sum(female_above_five_under_sixty),0) as female_above_five_under_sixty,ifnull(sum(total_above_five_under_sixty),0) as total_above_five_under_sixty,ifnull(sum(male_above_sixty),0) as male_above_sixty,ifnull(sum(female_above_sixty),0) as female_above_sixty,ifnull(sum(total_above_sixty),0) as total_above_sixty,ifnull(sum(total_male),0) as total_male,ifnull(sum(total_female),0) as total_female,ifnull(sum(grand_total),0) as grand_total from tbl_ipd_diseases_registers where ipd_mtuha_diagnosis_id IS NULL AND facility_id ='$facility_id' AND date BETWEEN  date('$start_date') AND date('$end_date')");
		if(count($others) == 0){
			$none->description = 'Diagnoses, Other';
			$response[] = $none;
		}else
			$response[] = $others[0];
			
		return $response;
    }


//getDentalClinicReport this uses dept_id = 9
    public function getDentalClinicReport(request $request)
    {
        $response = [];
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }

        $sql_1="SELECT date_attended,dept_id,facility_id,SUM(female_under_one_month) AS female_under_one_month ,SUM(male_under_one_month) AS male_under_one_month,SUM(total_under_one_month) AS total_under_one_month 
          ,SUM(female_under_one_year) AS female_under_one_year
          ,SUM(male_under_one_year) AS 	male_under_one_year
          ,SUM(total_under_one_year) AS total_under_one_year
          
         ,SUM(female_under_five_year) AS female_under_five_year
          ,SUM(male_under_five_year) AS male_under_five_year
          ,SUM(total_under_five_year) AS total_under_five_year
          
          	,SUM(female_5_upto_15_year) AS female_5_upto_15_year
          ,SUM(male_5_upto_15_year) AS male_5_upto_15_year
          ,SUM(	total_5_upto_15_year) AS 	total_5_upto_15_year
        
          	,SUM(female_15_and_above_year) AS female_15_and_above_year
          ,SUM(male_15_and_above_year) AS male_15_and_above_year
          ,SUM(total_15_and_above_year) AS 	total_15_and_above_year
        
         ,SUM(female_above_five_under_sixty_year) AS female_above_five_under_sixty_year
          ,SUM(male_above_five_under_sixty) AS male_above_five_under_sixty
          ,SUM(total_above_five_under_sixty) AS total_above_five_under_sixty
        
        ,SUM(female_above_sixty) AS female_above_sixty
          ,SUM(male_above_sixty) AS male_above_sixty
          ,SUM(total_above_sixty) AS total_above_sixty
          
        ,SUM(grand_total_female) AS grand_total_female
          ,SUM(grand_total_male) AS grand_total_male
          ,SUM(grand_total) AS grand_total
     
        FROM `vw_clinic_attendaces` t1 WHERE t1.dept_id=9 AND  t1.facility_id='{$facility_id}' AND (date_attended BETWEEN  '{$start_date}' AND '{$end_date}') GROUP BY date_attended,dept_id,facility_id";
        $response[] = DB::select($sql_1);

        return $response;

    }

//getDentalClinicReport this uses dept_id = 9
    public function getDoctorsPerfomaces(request $request)
    {
 
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }

       // return $end_date;

       
        $sql_1="SELECT t1.facility_id,t1.doctor_id,
        count(id) AS total_clerked,        
       t1.doctor_name,t1.prof_name     
     
        FROM `vw_perfomances` t1 WHERE t1.facility_id=".$facility_id." AND (time_treated BETWEEN   '".$start_date."' AND '".$end_date."') GROUP BY  t1.doctor_id order by count(id) desc ";
        return DB::select($sql_1);

    }




//getEyeClinicReport this uses dept_id = 10
    public function getEyeClinicReport(request $request)
    {
        $response = [];
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }


        $sql_1="SELECT date_attended,dept_id,facility_id,SUM(female_under_one_month) AS female_under_one_month ,SUM(male_under_one_month) AS male_under_one_month,SUM(total_under_one_month) AS total_under_one_month 
          ,SUM(female_under_one_year) AS female_under_one_year
          ,SUM(male_under_one_year) AS 	male_under_one_year
          ,SUM(total_under_one_year) AS total_under_one_year
          
         ,SUM(female_under_five_year) AS female_under_five_year
          ,SUM(male_under_five_year) AS male_under_five_year
          ,SUM(total_under_five_year) AS total_under_five_year
          
          	,SUM(female_5_upto_15_year) AS female_5_upto_15_year
          ,SUM(male_5_upto_15_year) AS male_5_upto_15_year
          ,SUM(	total_5_upto_15_year) AS 	total_5_upto_15_year
        
          	,SUM(female_15_and_above_year) AS female_15_and_above_year
          ,SUM(male_15_and_above_year) AS male_15_and_above_year
          ,SUM(total_15_and_above_year) AS 	total_15_and_above_year
        
         ,SUM(female_above_five_under_sixty_year) AS female_above_five_under_sixty_year
          ,SUM(male_above_five_under_sixty) AS male_above_five_under_sixty
          ,SUM(total_above_five_under_sixty) AS total_above_five_under_sixty
        
        ,SUM(female_above_sixty) AS female_above_sixty
          ,SUM(male_above_sixty) AS male_above_sixty
          ,SUM(total_above_sixty) AS total_above_sixty
          
        ,SUM(grand_total_female) AS grand_total_female
          ,SUM(grand_total_male) AS grand_total_male
          ,SUM(grand_total) AS grand_total
     
        FROM `vw_clinic_attendaces` t1 WHERE t1.dept_id=10 AND  t1.facility_id='{$facility_id}' AND (date_attended BETWEEN  '{$start_date}' AND '{$end_date}') GROUP BY date_attended,dept_id,facility_id";
        $response[] = DB::select($sql_1);

        return $response;

    }



 

   //rch mtuha books and dtc
public function getChilddewormgivenReport(request $request)
    {
        $response = [];
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }


        $sql_child_dewom="SELECT
 ifnull(sum(CASE when gender='MALE' AND deworm_given='YES'   then 1 ELSE  0 END ),0) as male,ifnull(sum(CASE when  gender='FEMALE' AND deworm_given='YES'   then 1 ELSE  0 END ),0) as female,
 ifnull(sum(CASE when deworm_given='YES'   then 1 ELSE  0 END ),0) as total
  FROM tbl_child_vitamin_deworm_registers INNER  JOIN tbl_child_registers ON tbl_child_registers.id=tbl_child_vitamin_deworm_registers.client_id
  
 WHERE tbl_child_vitamin_deworm_registers.facility_id='".$facility_id."' and tbl_child_vitamin_deworm_registers.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $response[] = DB::select($sql_child_dewom)[0];

        $sql_child_voucher="SELECT
 ifnull(sum(CASE when gender='MALE' AND voucher_given='YES'   then 1 ELSE  0 END ),0) as male,ifnull(sum(CASE when  gender='FEMALE' AND voucher_given='YES'   then 1 ELSE  0 END ),0) as female,
 ifnull(sum(CASE when voucher_given='YES'   then 1 ELSE  0 END ),0) as total
  FROM tbl_child_subsidized_voucher_registers INNER  JOIN tbl_child_registers ON tbl_child_registers.id=tbl_child_subsidized_voucher_registers.patient_id
  
 WHERE tbl_child_subsidized_voucher_registers.facility_id='".$facility_id."' and tbl_child_subsidized_voucher_registers.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $response[] = DB::select($sql_child_voucher)[0];
        $sql_child_exp="SELECT
 ifnull(sum(CASE when gender='MALE' AND heid_no IS NOT  NULL  then 1 ELSE  0 END ),0) as male,ifnull(sum(CASE when  gender='FEMALE' AND heid_no IS NOT  NULL   then 1 ELSE  0 END ),0) as female,
 ifnull(sum(CASE when heid_no IS NOT  NULL   then 1 ELSE  0 END ),0) as total
  FROM tbl_child_hiv_expose_registers INNER  JOIN tbl_child_registers ON tbl_child_registers.id=tbl_child_hiv_expose_registers.patient_id
  
 WHERE tbl_child_hiv_expose_registers.facility_id='".$facility_id."' and tbl_child_hiv_expose_registers.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $response[] = DB::select($sql_child_exp)[0];
        $sql_albendazole= "SELECT
 ifnull(sum(CASE when gender='MALE' AND vitamin_given='YES' and timestampdiff(month,dob,tbl_child_vitamin_deworm_registers.created_at)=6  then 1 ELSE  0 END ),0) as six_month_male,ifnull(sum(CASE when  gender='FEMALE' AND vitamin_given='YES' AND timestampdiff(month,dob,tbl_child_vitamin_deworm_registers.created_at)=6    then 1 ELSE  0 END ),0) as six_month_female,
 ifnull(sum(CASE when vitamin_given='YES' AND timestampdiff(month,dob,tbl_child_vitamin_deworm_registers.created_at)=6   then 1 ELSE  0 END ),0) as six_month_total,
 ifnull(sum(CASE when gender='MALE' AND vitamin_given='YES' and timestampdiff(month,dob,tbl_child_vitamin_deworm_registers.created_at)<12  then 1 ELSE  0 END ),0) as one_year_male,ifnull(sum(CASE when  gender='FEMALE' AND vitamin_given='YES' AND timestampdiff(month,dob,tbl_child_vitamin_deworm_registers.created_at)<12    then 1 ELSE  0 END ),0) as one_year_female,
 ifnull(sum(CASE when vitamin_given='YES' AND timestampdiff(month,dob,tbl_child_vitamin_deworm_registers.created_at)<12   then 1 ELSE  0 END ),0) as one_year_total,
  ifnull(sum(CASE when gender='MALE' AND vitamin_given='YES' and timestampdiff(month,dob,tbl_child_vitamin_deworm_registers.created_at)>=12 and timestampdiff(month,dob,tbl_child_vitamin_deworm_registers.created_at)<=160  then 1 ELSE  0 END ),0) as one_five_year_male,ifnull(sum(CASE when  gender='FEMALE' AND vitamin_given='YES' AND   timestampdiff(month,dob,tbl_child_vitamin_deworm_registers.created_at)>=12 and timestampdiff(month,dob,tbl_child_vitamin_deworm_registers.created_at)<=160    then 1 ELSE  0 END ),0) as one_five_year_female,
 ifnull(sum(CASE when vitamin_given='YES' and timestampdiff(month,dob,tbl_child_vitamin_deworm_registers.created_at)>=12 and timestampdiff(month,dob,tbl_child_vitamin_deworm_registers.created_at)<=160   then 1 ELSE  0 END ),0) as one_five_year_total
  FROM tbl_child_vitamin_deworm_registers INNER  JOIN tbl_child_registers ON tbl_child_registers.id=tbl_child_vitamin_deworm_registers.client_id
  
 WHERE tbl_child_vitamin_deworm_registers.facility_id='".$facility_id."' and tbl_child_vitamin_deworm_registers.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $response[] = DB::select($sql_albendazole)[0];

        $sql_child_pepopunda="SELECT
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=1  then 1 ELSE  0 END ),0) as male,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=1    then 1 ELSE  0 END ),0) as female,
 ifnull(sum(CASE when vaccination_id=1  then 1 ELSE  0 END ),0) as total
  FROM tbl_child_vaccination_registers INNER  JOIN tbl_child_registers ON tbl_child_registers.id=tbl_child_vaccination_registers.patient_id
  
 WHERE tbl_child_vaccination_registers.facility_id='".$facility_id."' and tbl_child_vaccination_registers.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $response[] = DB::select($sql_child_pepopunda)[0];

        $sql_child_bcg="SELECT
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=2 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_less_year_inplace,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=2  AND  place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12 then 1 ELSE  0 END ),0) as female_less_year_inplace,
 ifnull(sum(CASE when vaccination_id=2 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12 then 1 ELSE  0 END ),0) as total_less_year_inplace,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=2 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_less_year_outplace,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=2  AND  place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12 then 1 ELSE  0 END ),0) as female_less_year_outplace,
 ifnull(sum(CASE when vaccination_id=2 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12 then 1 ELSE  0 END ),0) as total_less_year_outplace,
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=2 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as male_above_year_inplace,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=2  AND  place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11 then 1 ELSE  0 END ),0) as female_above_year_inplace,
 ifnull(sum(CASE when vaccination_id=2 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11 then 1 ELSE  0 END ),0) as total_above_year_inplace,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=2 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as male_above_year_outplace,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=2  AND  place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11 then 1 ELSE  0 END ),0) as female_above_year_outplace,
 ifnull(sum(CASE when vaccination_id=2 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11 then 1 ELSE  0 END ),0) as total_above_year_outplace
  FROM tbl_child_vaccination_registers INNER  JOIN tbl_child_registers ON tbl_child_registers.id=tbl_child_vaccination_registers.patient_id
  
 WHERE tbl_child_vaccination_registers.facility_id='".$facility_id."' and tbl_child_vaccination_registers.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $response[] = DB::select($sql_child_bcg)[0];


        $sql_child_polio="SELECT
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=4 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_dose_0,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=4  AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12   then 1 ELSE  0 END ),0) as female_dose_0,
 ifnull(sum(CASE when vaccination_id=4 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_dose_0,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=5 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_dose_1,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=5 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12    then 1 ELSE  0 END ),0) as female_dose_1,
 ifnull(sum(CASE when vaccination_id=5 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_dose_1,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=6 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_dose_2,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=6  AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12   then 1 ELSE  0 END ),0) as female_dose_2,
 ifnull(sum(CASE when vaccination_id=6 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_dose_2,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=7 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_dose_3,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=7 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12    then 1 ELSE  0 END ),0) as female_dose_3,
 ifnull(sum(CASE when vaccination_id=7 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_dose_3,
 
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=5 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_dose_1_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=5 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12    then 1 ELSE  0 END ),0) as female_dose_1_out,
 ifnull(sum(CASE when vaccination_id=5 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_dose_1_out,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=6 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_dose_2_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=6  AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12   then 1 ELSE  0 END ),0) as female_dose_2_out,
 ifnull(sum(CASE when vaccination_id=6 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_dose_2_out,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=7 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_dose_3_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=7 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12    then 1 ELSE  0 END ),0) as female_dose_3_out,
 ifnull(sum(CASE when vaccination_id=7 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_dose_3_out,
 
 
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=5 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_male_dose_1,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=5 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11    then 1 ELSE  0 END ),0) as above_year_female_dose_1,
 ifnull(sum(CASE when vaccination_id=5 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_total_dose_1,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=6 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_male_dose_2,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=6  AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11   then 1 ELSE  0 END ),0) as above_year_female_dose_2,
 ifnull(sum(CASE when vaccination_id=6 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_total_dose_2,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=7 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_male_dose_3,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=7 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11    then 1 ELSE  0 END ),0) as above_year_female_dose_3,
 ifnull(sum(CASE when vaccination_id=7 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_total_dose_3,
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=5 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_male_dose_1_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=5 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11    then 1 ELSE  0 END ),0) as above_year_female_dose_1_out,
 ifnull(sum(CASE when vaccination_id=5 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_total_dose_1_out,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=6 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_male_dose_2_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=6  AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11   then 1 ELSE  0 END ),0) as above_year_female_dose_2_out,
 ifnull(sum(CASE when vaccination_id=6 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_total_dose_2_out,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=7 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_male_dose_3_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=7 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11    then 1 ELSE  0 END ),0) as above_year_female_dose_3_out,
 ifnull(sum(CASE when vaccination_id=7 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_total_dose_3_out,
 
 
 
 
 


  ifnull(sum(CASE when gender='MALE' AND  vaccination_id=11 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_penta_dose_1,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=11 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12    then 1 ELSE  0 END ),0) as female_penta_dose_1,
 ifnull(sum(CASE when vaccination_id=11 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_penta_dose_1,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=12 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_penta_dose_2,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=12  AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12   then 1 ELSE  0 END ),0) as female_penta_dose_2,
 ifnull(sum(CASE when vaccination_id=12 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_penta_dose_2,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=13 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_penta_dose_3,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=13 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12    then 1 ELSE  0 END ),0) as female_penta_dose_3,
 ifnull(sum(CASE when vaccination_id=13 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_penta_dose_3,
 
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=11 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_penta_dose_1_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=11 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12    then 1 ELSE  0 END ),0) as female_penta_dose_1_out,
 ifnull(sum(CASE when vaccination_id=11 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_penta_dose_1_out,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=12 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_penta_dose_2_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=12  AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12   then 1 ELSE  0 END ),0) as female_penta_dose_2_out,
 ifnull(sum(CASE when vaccination_id=12 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_penta_dose_2_out,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=13 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_penta_dose_3_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=13 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12    then 1 ELSE  0 END ),0) as female_penta_dose_3_out,
 ifnull(sum(CASE when vaccination_id=13 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_penta_dose_3_out,
 
 
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=11 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_penta_male_dose_1,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=11 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11    then 1 ELSE  0 END ),0) as above_year_penta_female_dose_1,
 ifnull(sum(CASE when vaccination_id=11 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_penta_total_dose_1,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=12 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_penta_male_dose_2,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=12  AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11   then 1 ELSE  0 END ),0) as above_year_penta_female_dose_2,
 ifnull(sum(CASE when vaccination_id=12 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_penta_total_dose_2,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=13 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_penta_male_dose_3,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=13 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11    then 1 ELSE  0 END ),0) as above_year_penta_female_dose_3,
 ifnull(sum(CASE when vaccination_id=13 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_penta_total_dose_3,
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=11 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_penta_male_dose_1_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=11 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11    then 1 ELSE  0 END ),0) as above_year_penta_female_dose_1_out,
 ifnull(sum(CASE when vaccination_id=11 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_penta_total_dose_1_out,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=12 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_penta_male_dose_2_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=12  AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11   then 1 ELSE  0 END ),0) as above_year_penta_female_dose_2_out,
 ifnull(sum(CASE when vaccination_id=12 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_penta_total_dose_2_out,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=13 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_penta_male_dose_3_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=13 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11    then 1 ELSE  0 END ),0) as above_year_penta_female_dose_3_out,
 ifnull(sum(CASE when vaccination_id=13 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_penta_total_dose_3_out,
  
  ifnull(sum(CASE when gender='MALE' AND  vaccination_id=14 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_pneu_dose_1,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=14 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12    then 1 ELSE  0 END ),0) as female_pneu_dose_1,
 ifnull(sum(CASE when vaccination_id=14 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_pneu_dose_1,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=15 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_pneu_dose_2,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=15  AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12   then 1 ELSE  0 END ),0) as female_pneu_dose_2,
 ifnull(sum(CASE when vaccination_id=15 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_pneu_dose_2,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=16 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_pneu_dose_3,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=16 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12    then 1 ELSE  0 END ),0) as female_pneu_dose_3,
 ifnull(sum(CASE when vaccination_id=16 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_pneu_dose_3,
 
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=14 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_pneu_dose_1_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=14 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12    then 1 ELSE  0 END ),0) as female_pneu_dose_1_out,
 ifnull(sum(CASE when vaccination_id=14 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_pneu_dose_1_out,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=15 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_pneu_dose_2_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=15  AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12   then 1 ELSE  0 END ),0) as female_pneu_dose_2_out,
 ifnull(sum(CASE when vaccination_id=12 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_pneu_dose_2_out,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=16 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as male_pneu_dose_3_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=16 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12    then 1 ELSE  0 END ),0) as female_pneu_dose_3_out,
 ifnull(sum(CASE when vaccination_id=16 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)<12  then 1 ELSE  0 END ),0) as total_pneu_dose_3_out,
 
 
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=14 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_pneu_male_dose_1,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=14 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11    then 1 ELSE  0 END ),0) as above_year_pneu_female_dose_1,
 ifnull(sum(CASE when vaccination_id=11 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_pneu_total_dose_1,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=12 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_pneu_male_dose_2,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=15  AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11   then 1 ELSE  0 END ),0) as above_year_pneu_female_dose_2,
 ifnull(sum(CASE when vaccination_id=12 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_pneu_total_dose_2,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=13 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_pneu_male_dose_3,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=16 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11    then 1 ELSE  0 END ),0) as above_year_pneu_female_dose_3,
 ifnull(sum(CASE when vaccination_id=13 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_pneu_total_dose_3,
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=14 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_pneu_male_dose_1_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=14 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11    then 1 ELSE  0 END ),0) as above_year_pneu_female_dose_1_out,
 ifnull(sum(CASE when vaccination_id=14 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_pneu_total_dose_1_out,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=15 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_pneu_male_dose_2_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=15  AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11   then 1 ELSE  0 END ),0) as above_year_pneu_female_dose_2_out,
 ifnull(sum(CASE when vaccination_id=15 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_pneu_total_dose_2_out,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=16 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_pneu_male_dose_3_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=16 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11    then 1 ELSE  0 END ),0) as above_year_pneu_female_dose_3_out,
 ifnull(sum(CASE when vaccination_id=16 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)>11  then 1 ELSE  0 END ),0) as above_year_pneu_total_dose_3_out,
  

 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=17 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=9  then 1 ELSE  0 END ),0) as male_rubela_dose_1,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=17 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=9    then 1 ELSE  0 END ),0) as female_rubela_dose_1,
 ifnull(sum(CASE when vaccination_id=17 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=9  then 1 ELSE  0 END ),0) as total_rubela_dose_1,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=17 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=9  then 1 ELSE  0 END ),0) as male_rubela_dose_1_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=17 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=9    then 1 ELSE  0 END ),0) as female_rubela_dose_1_out,
 ifnull(sum(CASE when vaccination_id=17 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=9  then 1 ELSE  0 END ),0) as total_rubela_dose_1_out,
  
ifnull(sum(CASE when gender='MALE' AND  vaccination_id=18 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=18  then 1 ELSE  0 END ),0) as male_rubela_dose_2,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=18 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=18    then 1 ELSE  0 END ),0) as female_rubela_dose_2,
 ifnull(sum(CASE when vaccination_id=18 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=18  then 1 ELSE  0 END ),0) as total_rubela_dose_2,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=18 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=18  then 1 ELSE  0 END ),0) as male_rubela_dose_2_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=18 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=18    then 1 ELSE  0 END ),0) as female_rubela_dose_2_out,
 ifnull(sum(CASE when vaccination_id=18 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=18  then 1 ELSE  0 END ),0) as total_rubela_dose_2_out,
  
 
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=5 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=18  then 1 ELSE  0 END ),0) as male_polio_dose_1,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=5 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=18    then 1 ELSE  0 END ),0) as female_polio_dose_1,
 ifnull(sum(CASE when vaccination_id=5 AND place=1 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=18  then 1 ELSE  0 END ),0) as total_polio_dose_1,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=5 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=9  then 1 ELSE  0 END ),0) as male_polio_dose_1_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=5 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=18    then 1 ELSE  0 END ),0) as female_polio_dose_1_out,
 ifnull(sum(CASE when vaccination_id=5 AND place=2 AND timestampdiff(month,dob,tbl_child_vaccination_registers.created_at)=18  then 1 ELSE  0 END ),0) as total_polio_dose_1_out,
 
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=9 AND place=1 AND timestampdiff(week,dob,tbl_child_vaccination_registers.created_at) BETWEEN 6 and 15 then 1 ELSE  0 END ),0) as male_rota_dose_1,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=9 AND place=1 AND timestampdiff(week,dob,tbl_child_vaccination_registers.created_at)  BETWEEN 6 and 15    then 1 ELSE  0 END ),0) as female_rota_dose_1,
 ifnull(sum(CASE when vaccination_id=9 AND place=1 AND timestampdiff(week,dob,tbl_child_vaccination_registers.created_at)  BETWEEN 6 and 15  then 1 ELSE  0 END ),0) as total_rota_dose_1,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=9 AND place=2 AND timestampdiff(week,dob,tbl_child_vaccination_registers.created_at)  BETWEEN 6 and 15  then 1 ELSE  0 END ),0) as male_rota_dose_1_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=9 AND place=2 AND timestampdiff(week,dob,tbl_child_vaccination_registers.created_at)  BETWEEN 6 and 15    then 1 ELSE  0 END ),0) as female_rota_dose_1_out,
 ifnull(sum(CASE when vaccination_id=9 AND place=2 AND timestampdiff(week,dob,tbl_child_vaccination_registers.created_at)  BETWEEN 6 and 15  then 1 ELSE  0 END ),0) as total_rota_dose_1_out,
  
 
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=10 AND place=1 AND timestampdiff(week,dob,tbl_child_vaccination_registers.created_at) BETWEEN 10 and 32 then 1 ELSE  0 END ),0) as male_rota_dose_2,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=10 AND place=1 AND timestampdiff(week,dob,tbl_child_vaccination_registers.created_at)  BETWEEN 10 and 32    then 1 ELSE  0 END ),0) as female_rota_dose_2,
 ifnull(sum(CASE when vaccination_id=10 AND place=1 AND timestampdiff(week,dob,tbl_child_vaccination_registers.created_at)  BETWEEN 10 and 32  then 1 ELSE  0 END ),0) as total_rota_dose_2,
 ifnull(sum(CASE when gender='MALE' AND  vaccination_id=9 AND place=2 AND timestampdiff(week,dob,tbl_child_vaccination_registers.created_at)  BETWEEN 10 and 32  then 1 ELSE  0 END ),0) as male_rota_dose_2_out,ifnull(sum(CASE when  gender='FEMALE' AND vaccination_id=10 AND place=2 AND timestampdiff(week,dob,tbl_child_vaccination_registers.created_at)  BETWEEN 10 and 32    then 1 ELSE  0 END ),0) as female_rota_dose_2_out,
 ifnull(sum(CASE when vaccination_id=10 AND place=2 AND timestampdiff(week,dob,tbl_child_vaccination_registers.created_at)  BETWEEN 10 and 32  then 1 ELSE  0 END ),0) as total_rota_dose_2_out
  
  
 
  FROM tbl_child_vaccination_registers INNER  JOIN tbl_child_registers ON tbl_child_registers.id=tbl_child_vaccination_registers.patient_id
  INNER  JOIN tbl_vaccination_registers ON tbl_vaccination_registers.id=tbl_child_vaccination_registers.vaccination_id
  
 WHERE tbl_child_vaccination_registers.facility_id='".$facility_id."' and tbl_child_vaccination_registers.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $response[] = DB::select($sql_child_polio)[0];

        $sql_vct_ref="SELECT
 ifnull(sum(CASE when gender='MALE'   then 1 ELSE  0 END ),0) as male,ifnull(sum(CASE when  gender='FEMALE' then 1 ELSE  0 END ),0) as female,
 ifnull(sum(CASE when gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as total
   from tbl_child_registers inner JOIN tbl_accounts_numbers on tbl_accounts_numbers.patient_id=tbl_child_registers.patient_id
inner join tbl_clinic_instructions on tbl_clinic_instructions.visit_id= tbl_accounts_numbers.id 
  
 WHERE tbl_clinic_instructions.dept_id=8 AND tbl_accounts_numbers.facility_id='".$facility_id."' and tbl_clinic_instructions.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";

        $response[] = DB::select($sql_vct_ref)[0];

        $sql_uzito_umri_less_year="SELECT
 ifnull(sum(CASE when gender='MALE' AND (weightp >80 or weightz> -2)  then 1 ELSE  0 END ),0) as male_above_80,ifnull(sum(CASE when  gender='FEMALE'  AND (weightp >80 or weightz> -2)   then 1 ELSE  0 END ),0) as female_above_80,
 ifnull(sum(CASE when gender='MALE' or gender='FEMALE' AND (weightp >80 or weightz> -2)  then 1 ELSE  0 END ),0) as total_above_80,
   
    ifnull(sum(CASE when gender='MALE' AND (weightp BETWEEN 60 and 80 or weightz BETWEEN -2 and -3)  then 1 ELSE  0 END ),0) as male_between_60_80,ifnull(sum(CASE when  gender='FEMALE'  AND (weightp BETWEEN 60 and 80 or weightz BETWEEN -2 and -3)   then 1 ELSE  0 END ),0) as female_between_60_80,
 ifnull(sum(CASE when gender='MALE' or gender='FEMALE' AND (weightp BETWEEN 60 and 80 or weightz BETWEEN -2 and -3)  then 1 ELSE  0 END ),0) as total_between_60_80,
   
   ifnull(sum(CASE when gender='MALE' AND (weightp <60 or weightz< -3)  then 1 ELSE  0 END ),0) as male_less_60,ifnull(sum(CASE when  gender='FEMALE'  AND (weightp <60 or weightz< -3)   then 1 ELSE  0 END ),0) as female_less_60,
 ifnull(sum(CASE when gender='MALE' or gender='FEMALE' AND (weightp <60 or weightz< -3)  then 1 ELSE  0 END ),0) as total_less_60,
 
    ifnull(sum(CASE when gender='MALE' AND  heightp >-2  then 1 ELSE  0 END ),0) as male_height_greater_2,ifnull(sum(CASE when  gender='FEMALE'  AND heightp >-2   then 1 ELSE  0 END ),0) as female_height_greater_2,
 ifnull(sum(CASE when gender='MALE' or gender='FEMALE' AND heightp >-2  then 1 ELSE  0 END ),0) as total_height_greater_2,
 
 ifnull(sum(CASE when gender='MALE' AND  heightp BETWEEN -2 and -3  then 1 ELSE  0 END ),0) as male_height_between_2_3,ifnull(sum(CASE when  gender='FEMALE'  AND heightp BETWEEN -2 and -3   then 1 ELSE  0 END ),0) as female_height_between_2_3,
 ifnull(sum(CASE when gender='MALE' or gender='FEMALE' AND heightp BETWEEN -2 and -3   then 1 ELSE  0 END ),0) as total_height_between_2_3,
   
   ifnull(sum(CASE when gender='MALE' AND  heightp < -3  then 1 ELSE  0 END ),0) as male_height_less_3,ifnull(sum(CASE when  gender='FEMALE'  AND heightp < -3   then 1 ELSE  0 END ),0) as female_height_less_3,
 ifnull(sum(CASE when gender='MALE' or gender='FEMALE' AND heightp < -3   then 1 ELSE  0 END ),0) as total_height_less_3
   
   from tbl_child_registers inner JOIN tbl_child_growth_registers on tbl_child_growth_registers.patient_id=tbl_child_registers.patient_id

 WHERE timestampdiff(MONTH ,dob,tbl_child_growth_registers.created_at)<12 AND tbl_child_growth_registers.facility_id='".$facility_id."' and tbl_child_growth_registers.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
       
        $response[] = DB::select($sql_uzito_umri_less_year)[0];

        $sql_uzito_umri_between_1_5="SELECT
 ifnull(sum(CASE when gender='MALE' AND (weightp >80 or weightz> -2)  then 1 ELSE  0 END ),0) as male_above_80_1_5,ifnull(sum(CASE when  gender='FEMALE'  AND (weightp >80 or weightz> -2)   then 1 ELSE  0 END ),0) as female_above_80_1_5,
 ifnull(sum(CASE when gender='MALE' or gender='FEMALE' AND (weightp >80 or weightz> -2)  then 1 ELSE  0 END ),0) as total_above_80_1_5,
   
    ifnull(sum(CASE when gender='MALE' AND (weightp BETWEEN 60 and 80 or weightz BETWEEN -2 and -3)  then 1 ELSE  0 END ),0) as male_between_60_80_1_5,ifnull(sum(CASE when  gender='FEMALE'  AND (weightp BETWEEN 60 and 80 or weightz BETWEEN -2 and -3)   then 1 ELSE  0 END ),0) as female_between_60_80_1_5,
 ifnull(sum(CASE when gender='MALE' or gender='FEMALE' AND (weightp BETWEEN 60 and 80 or weightz BETWEEN -2 and -3)  then 1 ELSE  0 END ),0) as total_between_60_80_1_5,
   
   ifnull(sum(CASE when gender='MALE' AND (weightp <60 or weightz< -3)  then 1 ELSE  0 END ),0) as male_less_60_1_5,ifnull(sum(CASE when  gender='FEMALE'  AND (weightp <60 or weightz< -3)   then 1 ELSE  0 END ),0) as female_less_60_1_5,
 ifnull(sum(CASE when gender='MALE' or gender='FEMALE' AND (weightp <60 or weightz< -3)  then 1 ELSE  0 END ),0) as total_less_60_1_5,
 
    ifnull(sum(CASE when gender='MALE' AND  heightp >-2  then 1 ELSE  0 END ),0) as male_height_greater_2_1_5,ifnull(sum(CASE when  gender='FEMALE'  AND heightp >-2   then 1 ELSE  0 END ),0) as female_height_greater_2_1_5,
 ifnull(sum(CASE when gender='MALE' or gender='FEMALE' AND heightp >-2  then 1 ELSE  0 END ),0) as total_height_greater_2_1_5,
 
 ifnull(sum(CASE when gender='MALE' AND  heightp BETWEEN -2 and -3  then 1 ELSE  0 END ),0) as male_height_between_2_3_1_5,ifnull(sum(CASE when  gender='FEMALE'  AND heightp BETWEEN -2 and -3   then 1 ELSE  0 END ),0) as female_height_between_2_3_1_5,
 ifnull(sum(CASE when gender='MALE' or gender='FEMALE' AND heightp BETWEEN -2 and -3   then 1 ELSE  0 END ),0) as total_height_between_2_3_1_5,
   
   ifnull(sum(CASE when gender='MALE' AND  heightp < -3  then 1 ELSE  0 END ),0) as male_height_less_3_1_5,ifnull(sum(CASE when  gender='FEMALE'  AND heightp < -3   then 1 ELSE  0 END ),0) as female_height_less_3_1_5,
 ifnull(sum(CASE when gender='MALE' or gender='FEMALE' AND heightp < -3   then 1 ELSE  0 END ),0) as total_height_less_3_1_5
   
   from tbl_child_registers inner JOIN tbl_child_growth_registers on tbl_child_growth_registers.patient_id=tbl_child_registers.patient_id

 WHERE timestampdiff(MONTH ,dob,tbl_child_growth_registers.created_at) BETWEEN 1  and  60 AND tbl_child_growth_registers.facility_id='".$facility_id."' and tbl_child_growth_registers.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $response[] = DB::select($sql_uzito_umri_between_1_5)[0];

        $sql_child_attendance="SELECT  ifnull(SUM(CASE WHEN gender = 'male' THEN 1 ELSE 0 END),0) as total_male, ifnull(SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END),0) as total_female,
     ifnull(SUM(CASE WHEN gender = 'male' or gender='female' THEN 1 ELSE 0 END),0) as total_gender from tbl_child_registers
 WHERE facility_id='".$facility_id."' and created_at BETWEEN '".$start_date."' and '".$end_date."' ";

        $response[] = DB::select($sql_child_attendance)[0];

        $sql_child_feed="SELECT
 ifnull(sum(`ebf_male`),0) as ebf_male,ifnull(sum(`ebf_female`),0) as ebf_female,ifnull(sum(`ebf_total`),0) as ebf_total,
 ifnull(sum(`rf_male`),0) as rf_male,ifnull(sum(`rf_female`),0) as rf_female,ifnull(sum(`rf_total`),0) as rf_total
  FROM `vw_baby_feedings`
 WHERE facility_id='".$facility_id."' and created_at BETWEEN '".$start_date."' and '".$end_date."' ";

        $sql_child_feed="SELECT ifnull(sum(CASE WHEN gender='MALE' AND feeding_type='EBF' THEN 1 else 0 END),0) as ebf_male,
ifnull(sum(CASE WHEN gender='FEMALE' AND feeding_type='EBF' THEN 1 else 0 END),0) as ebf_female,
ifnull(sum(CASE WHEN  feeding_type='EBF' THEN 1 else 0 END),0) as ebf_total,
ifnull(sum(CASE WHEN gender='MALE' AND feeding_type='RF' THEN 1 else 0 END),0) as rf_male,
ifnull(sum(CASE WHEN gender='FEMALE' AND feeding_type='RF' THEN 1 else 0 END),0) as rf_female,
ifnull(sum(CASE WHEN  feeding_type='RF' THEN 1 else 0 END),0) as rf_total 
    from tbl_child_feedings INNER JOIN tbl_child_registers on 
    tbl_child_registers.id=tbl_child_feedings.patient_id  
 WHERE tbl_child_registers.facility_id='".$facility_id."' and tbl_child_registers.created_at BETWEEN '".$start_date."' and '".$end_date."' ";



        $response[] = DB::select($sql_child_feed)[0];

        $sql_child_growth="SELECT  ifnull(sum(CASE when gender='MALE' then 1 ELSE  0 END ),0) as total_male,ifnull(sum(CASE when gender='FEMALE' then 1 ELSE  0 END ),0) as total_female,ifnull(SUM(CASE when gender='FEMALE' OR gender='MALE' then 1  ELSE  0  END ),0) as total_gender FROM
 `tbl_child_growth_registers` INNER JOIN tbl_child_registers ON tbl_child_registers.id=tbl_child_growth_registers.patient_id
 WHERE  `tbl_child_growth_registers`.facility_id='".$facility_id."' AND timestampdiff(MONTH ,dob,`tbl_child_growth_registers`.created_at)<12 and `tbl_child_growth_registers`.created_at BETWEEN '".$start_date."' and '".$end_date."' ";

        $response[] = DB::select($sql_child_growth)[0];



        return $response;

    }



public function Anti_natl_mtuha(Request $request)
    {

        $antinal=[]  ;
        $facility_id=$request->facility_id;
        $start_date=$request->start;
        $end_date=$request->end;

        $sql_antinatal_less_12week="SELECT
 ifnull(sum(CASE when timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as less_20,ifnull(sum(CASE when  timestampdiff(YEAR ,dob,CURDATE()) >20 then 1 ELSE  0 END ),0) as above_20,
 ifnull(sum(CASE when timestampdiff(YEAR ,dob,CURDATE()) <20 or timestampdiff(YEAR ,dob,CURDATE()) >20  then 1 ELSE  0 END ),0) as total
   
   from tbl_previous_pregnancy_infos  inner JOIN tbl_anti_natal_registers on tbl_previous_pregnancy_infos.client_id=tbl_anti_natal_registers.id
  
 WHERE timestampdiff(week ,lnmp,CURDATE())<12 and tbl_previous_pregnancy_infos.facility_id='".$facility_id."' and tbl_previous_pregnancy_infos.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $antinal[] = DB::select($sql_antinatal_less_12week);

        $sql_antinatal_above_12week="SELECT
 ifnull(sum(CASE when timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as less_20,ifnull(sum(CASE when  timestampdiff(YEAR ,dob,CURDATE()) >20 then 1 ELSE  0 END ),0) as above_20,
 ifnull(sum(CASE when timestampdiff(YEAR ,dob,CURDATE()) <20 or timestampdiff(YEAR ,dob,CURDATE()) >20  then 1 ELSE  0 END ),0) as total
  
   from tbl_previous_pregnancy_infos  inner JOIN tbl_anti_natal_registers on tbl_previous_pregnancy_infos.client_id=tbl_anti_natal_registers.id
  
 WHERE timestampdiff(week ,lnmp,CURDATE())>12 and tbl_previous_pregnancy_infos.facility_id='".$facility_id."' and tbl_previous_pregnancy_infos.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $antinal[] = DB::select($sql_antinatal_above_12week);

        $sql_antinatal_total_less_above="SELECT
 ifnull(sum(CASE when timestampdiff(YEAR ,dob,CURDATE()) <20 AND (timestampdiff(week ,lnmp,CURDATE())<12 or timestampdiff(week ,lnmp,CURDATE())>12) then 1 ELSE  0 END ),0) as total_less_20,ifnull(sum(CASE when  timestampdiff(YEAR ,dob,CURDATE()) >20 AND (timestampdiff(week ,lnmp,CURDATE())<12 or timestampdiff(week ,lnmp,CURDATE())>12) then 1 ELSE  0 END ),0) as total_above_20,
 ifnull(sum(CASE when (timestampdiff(YEAR ,dob,CURDATE()) <20 or timestampdiff(YEAR ,dob,CURDATE()) >20) AND (timestampdiff(week ,lnmp,CURDATE())<12 or timestampdiff(week ,lnmp,CURDATE())>12)  then 1 ELSE  0 END ),0) as total_a_b
   from tbl_previous_pregnancy_infos  inner JOIN tbl_anti_natal_registers on tbl_previous_pregnancy_infos.client_id=tbl_anti_natal_registers.id
  
 WHERE timestampdiff(week ,lnmp,CURDATE())>12 or timestampdiff(week ,lnmp,CURDATE())<12 and tbl_previous_pregnancy_infos.facility_id='".$facility_id."' and tbl_previous_pregnancy_infos.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $antinal[] = DB::select($sql_antinatal_total_less_above);

        $Mimba_zaidi_ya_4="SELECT
 ifnull(sum(CASE when  timestampdiff(YEAR ,dob,CURDATE()) >20     then 1 ELSE  0 END ),0) as total
   from tbl_previous_pregnancy_infos  inner JOIN tbl_anti_natal_registers on tbl_previous_pregnancy_infos.client_id=tbl_anti_natal_registers.id
  
 WHERE  tbl_previous_pregnancy_infos.number_of_pregnancy>4 and tbl_previous_pregnancy_infos.facility_id='".$facility_id."' and tbl_previous_pregnancy_infos.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $antinal[] = DB::select($Mimba_zaidi_ya_4);
        $Umri_chini_ya_miaka_20_above_35="SELECT
 ifnull(sum(CASE when  timestampdiff(YEAR ,dob,CURDATE()) <20     then 1 ELSE  0 END ),0) as total_less_20,
 ifnull(sum(CASE when  timestampdiff(YEAR ,dob,CURDATE()) >35     then 1 ELSE  0 END ),0) as total_above_35
   from tbl_previous_pregnancy_infos  inner JOIN tbl_anti_natal_registers on tbl_previous_pregnancy_infos.client_id=tbl_anti_natal_registers.id
  
 WHERE   tbl_previous_pregnancy_infos.facility_id='".$facility_id."' and tbl_previous_pregnancy_infos.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $antinal[] = DB::select($Umri_chini_ya_miaka_20_above_35);

        $vidokezo_vya_hatari="SELECT
 ifnull(sum(CASE when  hb<8.5  AND timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as less_hb_less_20, ifnull(sum(CASE when  hb<8.5  AND timestampdiff(YEAR ,dob,CURDATE()) >20   then 1 ELSE  0 END ),0) as less_hb_above_20,
  ifnull(sum(CASE when  hb<8.5  AND (timestampdiff(YEAR ,dob,CURDATE()) >20 OR timestampdiff(YEAR ,dob,CURDATE()) <20)  then 1 ELSE  0 END ),0) as less_hb_total,
 ifnull(sum(CASE when  bp<140  AND timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as less_bp_less_20, ifnull(sum(CASE when  bp<140  AND timestampdiff(YEAR ,dob,CURDATE()) >20   then 1 ELSE  0 END ),0) as less_bp_above_20,
   ifnull(sum(CASE when  bp<140  AND (timestampdiff(YEAR ,dob,CURDATE()) >20 OR timestampdiff(YEAR ,dob,CURDATE()) <20)  then 1 ELSE  0 END ),0) as less_bp_total
   from tbl_anti_natal_attendances  inner JOIN tbl_anti_natal_registers on tbl_anti_natal_attendances.client_id=tbl_anti_natal_registers.id
  
 WHERE   tbl_anti_natal_attendances.facility_id='".$facility_id."' and tbl_anti_natal_attendances.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $antinal[] = DB::select($vidokezo_vya_hatari);

        $tb="SELECT
  
 ifnull(sum(CASE when tb='YES'  AND timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as tb_less_20, ifnull(sum(CASE when  tb='YES'  AND timestampdiff(YEAR ,dob,CURDATE()) >20   then 1 ELSE  0 END ),0) as tb_above_20,
   ifnull(sum(CASE when  tb='YES'  AND (timestampdiff(YEAR ,dob,CURDATE()) >20 OR timestampdiff(YEAR ,dob,CURDATE()) <20)  then 1 ELSE  0 END ),0) as tb_total
   from  tbl_previous_pregnancy_indicators  inner JOIN tbl_anti_natal_registers on  tbl_previous_pregnancy_indicators.client_id=tbl_anti_natal_registers.id
  
 WHERE    tbl_previous_pregnancy_indicators.facility_id='".$facility_id."' and  tbl_previous_pregnancy_indicators.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $antinal[] = DB::select($tb);

        $urine_sugar_protain="SELECT
 ifnull(sum(CASE when  urine_sugar>0  AND timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as urine_sugar_less_20, ifnull(sum(CASE when  urine_sugar>0  AND timestampdiff(YEAR ,dob,CURDATE()) >20   then 1 ELSE  0 END ),0) as urine_sugar_above_20,
  ifnull(sum(CASE when  urine_sugar>0  AND (timestampdiff(YEAR ,dob,CURDATE()) >20 OR timestampdiff(YEAR ,dob,CURDATE()) <20)  then 1 ELSE  0 END ),0) as urine_sugar_total,
 ifnull(sum(CASE when  urine_albumin>0  AND timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as urine_albumin_less_20, ifnull(sum(CASE when  urine_albumin>0  AND timestampdiff(YEAR ,dob,CURDATE()) >20   then 1 ELSE  0 END ),0) as urine_albumin_above_20,
   ifnull(sum(CASE when  urine_albumin>0  AND (timestampdiff(YEAR ,dob,CURDATE()) >20 OR timestampdiff(YEAR ,dob,CURDATE()) <20)  then 1 ELSE  0 END ),0) as urine_albumin_total
   from tbl_anti_natal_attendances  inner JOIN tbl_anti_natal_registers on tbl_anti_natal_attendances.client_id=tbl_anti_natal_registers.id
  
 WHERE   tbl_anti_natal_attendances.facility_id='".$facility_id."' and tbl_anti_natal_attendances.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $antinal[] = DB::select($urine_sugar_protain);

        $mrdt_kaswende_voucher_no="SELECT
  
 ifnull(sum(CASE when vdrl_rpr IS  not NULL  AND timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as vdrl_rpr_less_20, ifnull(sum(CASE when  vdrl_rpr IS not NULL  AND timestampdiff(YEAR ,dob,CURDATE()) >20   then 1 ELSE  0 END ),0) as vdrl_rpr_above_20,
   ifnull(sum(CASE when  vdrl_rpr IS not NULL  AND (timestampdiff(YEAR ,dob,CURDATE()) >20 OR timestampdiff(YEAR ,dob,CURDATE()) <20)  then 1 ELSE  0 END ),0) as vdrl_rpr_total,
 
  ifnull(sum(CASE when mrdt_bs ='MRDT'  AND timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as mrdt_less_20, ifnull(sum(CASE when  mrdt_bs ='MRDT'  AND timestampdiff(YEAR ,dob,CURDATE()) >20   then 1 ELSE  0 END ),0) as mrdt_above_20,
   ifnull(sum(CASE when  mrdt_bs ='MRDT'  AND (timestampdiff(YEAR ,dob,CURDATE()) >20 OR timestampdiff(YEAR ,dob,CURDATE()) <20)  then 1 ELSE  0 END ),0) as mrdt_total,
 
  ifnull(sum(CASE when result ='+ve'   AND timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as malaria_less_20, ifnull(sum(CASE when result ='+ve'  AND timestampdiff(YEAR ,dob,CURDATE()) >20   then 1 ELSE  0 END ),0) as malaria_above_20,
   ifnull(sum(CASE when  result ='+ve'  AND (timestampdiff(YEAR ,dob,CURDATE()) >20 OR timestampdiff(YEAR ,dob,CURDATE()) <20)  then 1 ELSE  0 END ),0) as malaria_total,
  
  ifnull(sum(CASE when voucher_no is not NULL   AND timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as voucher_no_less_20, ifnull(sum(CASE when voucher_no is not NULL  AND timestampdiff(YEAR ,dob,CURDATE()) >20   then 1 ELSE  0 END ),0) as voucher_no_above_20,
   ifnull(sum(CASE when  voucher_no is not NULL  AND (timestampdiff(YEAR ,dob,CURDATE()) >20 OR timestampdiff(YEAR ,dob,CURDATE()) <20)  then 1 ELSE  0 END ),0) as voucher_no_total
  
   from  tbl_anti_natal_lab_tests  inner JOIN tbl_anti_natal_registers on  tbl_anti_natal_lab_tests.client_id=tbl_anti_natal_registers.id
  
 WHERE    tbl_anti_natal_lab_tests.facility_id='".$facility_id."' and  tbl_anti_natal_lab_tests.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $antinal[] = DB::select($mrdt_kaswende_voucher_no);

        $ipts="SELECT

  ifnull(sum(CASE when ipt ='IPT-2'  AND timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as ipt2_less_20, ifnull(sum(CASE when  ipt ='IPT-2'  AND timestampdiff(YEAR ,dob,CURDATE()) >20   then 1 ELSE  0 END ),0) as ipt2_above_20,
   ifnull(sum(CASE when ipt ='IPT-2'  AND (timestampdiff(YEAR ,dob,CURDATE()) >20 OR timestampdiff(YEAR ,dob,CURDATE()) <20)  then 1 ELSE  0 END ),0) as ipt2_total,
 
  ifnull(sum(CASE when ipt ='IPT-4'   AND timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as ipt4_less_20, ifnull(sum(CASE when ipt ='IPT-4'  AND timestampdiff(YEAR ,dob,CURDATE()) >20   then 1 ELSE  0 END ),0) as ipt4_above_20,
   ifnull(sum(CASE when  ipt ='IPT-4'  AND (timestampdiff(YEAR ,dob,CURDATE()) >20 OR timestampdiff(YEAR ,dob,CURDATE()) <20)  then 1 ELSE  0 END ),0) as ipt4_total
  
   from  tbl_anti_natal_ipts  inner JOIN tbl_anti_natal_registers on  tbl_anti_natal_ipts.patient_id=tbl_anti_natal_registers.id
  
 WHERE    tbl_anti_natal_ipts.facility_id='".$facility_id."' and  tbl_anti_natal_ipts.created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $antinal[] = DB::select($ipts);

        $sql_vct_ref="SELECT
 ifnull(sum(CASE when timestampdiff(YEAR ,dob,CURDATE()) <20   then 1 ELSE  0 END ),0) as less_20,ifnull(sum(CASE when  timestampdiff(YEAR ,dob,CURDATE()) >20    then 1 ELSE  0 END ),0) as greater_20,
 ifnull(sum(CASE when timestampdiff(YEAR ,dob,CURDATE()) <20 or timestampdiff(YEAR ,dob,CURDATE()) >20  then 1 ELSE  0 END ),0) as total
   from tbl_anti_natal_registers inner JOIN tbl_accounts_numbers on tbl_accounts_numbers.patient_id=tbl_anti_natal_registers.client_id
inner join tbl_clinic_instructions on tbl_clinic_instructions.visit_id= tbl_accounts_numbers.id 
  
 WHERE tbl_clinic_instructions.dept_id=8 AND tbl_accounts_numbers.facility_id='".$facility_id."' and tbl_clinic_instructions.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";

        $antinal[] = DB::select($sql_vct_ref);

        $deworms="SELECT
  ifnull(sum(CASE when deworm ='YES'   AND timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as deworm_less_20, ifnull(sum(CASE when deworm ='YES'  AND timestampdiff(YEAR ,dob,CURDATE()) >20   then 1 ELSE  0 END ),0) as deworm_above_20,
   ifnull(sum(CASE when  deworm  ='YES'  AND (timestampdiff(YEAR ,dob,CURDATE()) >20 OR timestampdiff(YEAR ,dob,CURDATE()) <20)  then 1 ELSE  0 END ),0) as deworm_total,
  
  ifnull(sum(CASE when folic_acid ='YES'   AND timestampdiff(YEAR ,dob,CURDATE()) <20  then 1 ELSE  0 END ),0) as folic_less_20, ifnull(sum(CASE when folic_acid ='YES'  AND timestampdiff(YEAR ,dob,CURDATE()) >20   then 1 ELSE  0 END ),0) as folic_above_20,
   ifnull(sum(CASE when  folic_acid  ='YES'  AND (timestampdiff(YEAR ,dob,CURDATE()) >20 OR timestampdiff(YEAR ,dob,CURDATE()) <20)  then 1 ELSE  0 END ),0) as folic_total
  
   from  tbl_anti_natal_preventives  inner JOIN tbl_anti_natal_registers on  tbl_anti_natal_preventives.client_id=tbl_anti_natal_registers.id
  
 WHERE    tbl_anti_natal_preventives.facility_id='".$facility_id."' and  tbl_anti_natal_preventives.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($deworms);
        
 $reattendance="SELECT
  
  ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_anti_natal_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_anti_natal_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when  (timestampdiff(YEAR ,dob,tbl_anti_natal_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_anti_natal_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_anti_natal_attendances  inner JOIN tbl_anti_natal_registers on  tbl_anti_natal_attendances.client_id=tbl_anti_natal_registers.id
  
 WHERE    tbl_anti_natal_attendances.facility_id='".$facility_id."' and  tbl_anti_natal_attendances.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($reattendance);

 $forth_reattendance="SELECT
  
  ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_anti_natal_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_anti_natal_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when  (timestampdiff(YEAR ,dob,tbl_anti_natal_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_anti_natal_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_anti_natal_attendances  inner JOIN tbl_anti_natal_registers on  tbl_anti_natal_attendances.client_id=tbl_anti_natal_registers.id
  
 WHERE    tbl_anti_natal_attendances.facility_id='".$facility_id."' and  tbl_anti_natal_attendances.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($forth_reattendance);
        $blood="SELECT
  
  ifnull(sum(CASE when blood_group is not NULL  and timestampdiff(YEAR ,dob,tbl_anti_natal_lab_tests.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when blood_group is not NULL  and  timestampdiff(YEAR ,dob,tbl_anti_natal_lab_tests.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when blood_group is not NULL  and  (timestampdiff(YEAR ,dob,tbl_anti_natal_lab_tests.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_anti_natal_lab_tests.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_anti_natal_lab_tests  inner JOIN tbl_anti_natal_registers on  tbl_anti_natal_lab_tests.client_id=tbl_anti_natal_registers.id
  
 WHERE    tbl_anti_natal_lab_tests.facility_id='".$facility_id."' and  tbl_anti_natal_lab_tests.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($blood);

        $offf="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%tt%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($offf);

        

        $sql16="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql16);

        $sql17="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql17);

        $sql18="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql18);

        $sql19="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql19);

        $sql20="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql20);

        $sql21="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql21);

        $sql22="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql22);

        $sql23="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql23);

        $sql24="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql24);

        $sql25="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql25);

        $sql26="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql26);

        $sql27="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql27);

        $sql28="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql28);

        $sql29="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql29);

        $sql30="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql30);

        $sql31="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql31);

        $sql32="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql32);

        $sql33="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql33);

        $sql34="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql34);

        $sql35="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql35);

        $sql36="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql36);

        $sql37="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%offf%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql37);


        $sql38="SELECT
  
  ifnull(sum(CASE when tbl_anti_natal_councelling_givens.status='YES' and timestampdiff(YEAR ,dob,tbl_anti_natal_councelling_givens.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when tbl_anti_natal_councelling_givens.status='YES' AND timestampdiff(YEAR ,dob,tbl_anti_natal_councelling_givens.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when tbl_anti_natal_councelling_givens.status='YES' AND  (timestampdiff(YEAR ,dob,tbl_anti_natal_councelling_givens.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_anti_natal_councelling_givens.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_anti_natal_councelling_givens 
    inner JOIN tbl_anti_natal_registers on  tbl_anti_natal_councelling_givens.client_id=tbl_anti_natal_registers.id
    inner JOIN  tbl_anti_natal_councelling_areas on tbl_anti_natal_councelling_givens.description_id= tbl_anti_natal_councelling_givens.id
  
 WHERE    tbl_anti_natal_councelling_givens.facility_id='".$facility_id."' and tbl_anti_natal_councelling_areas.description like '%---%' and tbl_anti_natal_councelling_givens.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql38);

        $sql39="SELECT
  
  ifnull(sum(CASE when tbl_anti_natal_councelling_givens.status='YES' and timestampdiff(YEAR ,dob,tbl_anti_natal_councelling_givens.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when tbl_anti_natal_councelling_givens.status='YES' AND timestampdiff(YEAR ,dob,tbl_anti_natal_councelling_givens.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when tbl_anti_natal_councelling_givens.status='YES' AND  (timestampdiff(YEAR ,dob,tbl_anti_natal_councelling_givens.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_anti_natal_councelling_givens.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_anti_natal_councelling_givens 
    inner JOIN tbl_anti_natal_registers on  tbl_anti_natal_councelling_givens.client_id=tbl_anti_natal_registers.id
    inner JOIN  tbl_anti_natal_councelling_areas on tbl_anti_natal_councelling_givens.description_id= tbl_anti_natal_councelling_givens.id
  
 WHERE    tbl_anti_natal_councelling_givens.facility_id='".$facility_id."' and tbl_anti_natal_councelling_areas.description like '%---%' and tbl_anti_natal_councelling_givens.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql39);
        $sql40_feed="SELECT
  
  ifnull(sum(CASE when tbl_anti_natal_councelling_givens.status='YES' and timestampdiff(YEAR ,dob,tbl_anti_natal_councelling_givens.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when tbl_anti_natal_councelling_givens.status='YES' AND timestampdiff(YEAR ,dob,tbl_anti_natal_councelling_givens.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when tbl_anti_natal_councelling_givens.status='YES' AND  (timestampdiff(YEAR ,dob,tbl_anti_natal_councelling_givens.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_anti_natal_councelling_givens.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_anti_natal_councelling_givens 
    inner JOIN tbl_anti_natal_registers on  tbl_anti_natal_councelling_givens.client_id=tbl_anti_natal_registers.id
    inner JOIN  tbl_anti_natal_councelling_areas on tbl_anti_natal_councelling_givens.description_id= tbl_anti_natal_councelling_givens.id
  
 WHERE    tbl_anti_natal_councelling_givens.facility_id='".$facility_id."' and tbl_anti_natal_councelling_areas.description like '%feed%' and tbl_anti_natal_councelling_givens.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql40_feed);

        $sql41="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_tt_vaccinations.created_at) <20)  then 1 ELSE  0 END ),0) as total
  
   from  tbl_tt_vaccinations 
    inner JOIN tbl_anti_natal_registers on  tbl_tt_vaccinations.patient_id=tbl_anti_natal_registers.id
    inner JOIN tbl_vaccination_registers on  tbl_vaccination_registers.id=tbl_tt_vaccinations.vaccination_id
  
 WHERE    tbl_tt_vaccinations.facility_id='".$facility_id."' and tbl_vaccination_registers.vaccination_name like '%---%' and tbl_tt_vaccinations.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $antinal[] = DB::select($sql41);



        return $antinal;



    }
    public function mtuhaPost_natal(Request $request)
    {

        $postinal = [];
        $facility_id = $request->facility_id;
        $start_date = $request->start;
        $end_date = $request->end;


        $attendanceMother="SELECT
  
  ifnull(sum(CASE when attendance_range='48' AND   timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_range_48hr, ifnull(sum(CASE when attendance_range='48' AND timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_range_48hr,
   ifnull(sum(CASE when attendance_range='48' AND   (timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total_range_48hr,
 
 ifnull(sum(CASE when attendance_range='3-7' AND   timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_range_3_7day, ifnull(sum(CASE when attendance_range='3-7' AND timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_range_3_7day,
   ifnull(sum(CASE when attendance_range='3-7' AND  (timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total_range_3_7day,
  
   ifnull(sum(CASE when (attendance_range='3-7' or attendance_range='48') AND   timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_48_3_7, ifnull(sum(CASE when (attendance_range='3-7' or attendance_range='48') AND timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_48_3_7,
   ifnull(sum(CASE when(attendance_range='3-7' or attendance_range='48') AND  (timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total_48_3_7,
  
  ifnull(sum(CASE when ( attendance_range='48' AND attendance_range='3-7' AND attendance_range='8-28' AND attendance_range='29- 42') AND   timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_all_range, ifnull(sum(CASE when (attendance_range='48' AND attendance_range='3-7' AND attendance_range='8-28' AND attendance_range='29- 42') AND timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_all_range,
   ifnull(sum(CASE when(attendance_range='48' AND attendance_range='3-7' AND attendance_range='8-28' AND attendance_range='29- 42') AND  (timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total_all_range,
   
   ifnull(sum(CASE when hb <8.5 AND   timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_hb, ifnull(sum(CASE when hb <8.5 AND timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_hb,
   ifnull(sum(CASE when hb <8.5 AND  (timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total_hb
  
  
   from  tbl_post_natal_attendances  inner JOIN tbl_anti_natal_registers on  tbl_post_natal_attendances.patient_id=tbl_anti_natal_registers.id
  
 WHERE  tbl_post_natal_attendances.facility_id='".$facility_id."' and  tbl_post_natal_attendances.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $postinal[] = DB::select($attendanceMother);

//badooo
        $mental_msamba_fistula="SELECT
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_mental, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_mental,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total_mental,
   
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_msamba, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_msamba,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total_msamba,
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_fistula, ifnull(sum(CASE when  timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_fistula,
   ifnull(sum(CASE when   (timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total_fistula
  
   from  tbl_post_natal_attendances  inner JOIN tbl_anti_natal_registers on  tbl_post_natal_attendances.patient_id=tbl_anti_natal_registers.id
  
 WHERE  tbl_post_natal_attendances.facility_id='' and  tbl_post_natal_attendances.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $postinal[] = DB::select($mental_msamba_fistula);
        
 $vitamin_a="SELECT
  
  ifnull(sum(CASE when vitamin_a='YES' AND timestampdiff(YEAR ,dob,tbl_post_natal_additional_medications.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_vitamin_a, ifnull(sum(CASE when vitamin_a='YES' AND timestampdiff(YEAR ,dob,vitamin_a='YES' AND tbl_post_natal_additional_medications.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_vitamin_a,
   ifnull(sum(CASE when vitamin_a='YES' AND  (timestampdiff(YEAR ,dob,tbl_post_natal_additional_medications.created_at) >20 OR timestampdiff(YEAR ,dob,vitamin_a='YES' AND tbl_post_natal_additional_medications.created_at) <20)  then 1 ELSE  0 END ),0) as total_vitamin_a
  
   from  tbl_post_natal_additional_medications  inner JOIN tbl_anti_natal_registers on  tbl_post_natal_additional_medications.patient_id=tbl_anti_natal_registers.id
  
 WHERE  tbl_post_natal_additional_medications.facility_id='".$facility_id."' and  tbl_post_natal_additional_medications.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $postinal[] = DB::select($vitamin_a);


        $delivery_place="SELECT
  
  ifnull(sum(CASE when place_of_delivery='HF' AND  timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_HF, ifnull(sum(CASE when place_of_delivery='HF' AND timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_HF,
   ifnull(sum(CASE when  place_of_delivery='HF' AND (timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) <20)  then 1 ELSE  0 END ),0) as total_HF,
   
   
   ifnull(sum(CASE when place_of_delivery='BBA' AND   timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_BBA, ifnull(sum(CASE when place_of_delivery='BBA' AND  timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_BBA,
   ifnull(sum(CASE when place_of_delivery='BBA' AND   (timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) <20)  then 1 ELSE  0 END ),0) as total_BBA,
   
   ifnull(sum(CASE when place_of_delivery='TBA' AND   timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_TBA, ifnull(sum(CASE when  place_of_delivery='TBA' AND timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_TBA,
   ifnull(sum(CASE when   place_of_delivery='TBA' AND (timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) <20)  then 1 ELSE  0 END ),0) as total_TBA,
 
  ifnull(sum(CASE when  place_of_delivery='H' AND  timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_H, ifnull(sum(CASE when place_of_delivery='H' AND  timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_H,
   ifnull(sum(CASE when place_of_delivery='H' AND   (timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_labour_delivery_events.created_at) <20)  then 1 ELSE  0 END ),0) as total_H
  
   from  tbl_labour_delivery_events  inner JOIN tbl_anti_natal_registers on  tbl_labour_delivery_events.client_id=tbl_anti_natal_registers.id
  
 WHERE  tbl_labour_delivery_events.facility_id='".$facility_id."' and  tbl_labour_delivery_events.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $postinal[] = DB::select($delivery_place);

        $counceling="SELECT

  ifnull(sum(CASE when counselling_given='YES' AND timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_counsel_once, ifnull(sum(CASE when counselling_given='YES' AND timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_counsel_once,
   ifnull(sum(CASE when counselling_given='YES' AND  (timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) <20)  then 1 ELSE  0 END ),0) as total_counsel_once,
  
  ifnull(sum(CASE when referral_for_family_planning='YES' AND timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_ref, ifnull(sum(CASE when referral_for_family_planning='YES' AND timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_ref,
   ifnull(sum(CASE when referral_for_family_planning='YES' AND  (timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) <20)  then 1 ELSE  0 END ),0) as total_ref
  
   from  tbl_post_natal_familiy_plannings  inner JOIN tbl_anti_natal_registers on  tbl_post_natal_familiy_plannings.patient_id=tbl_anti_natal_registers.id
  
 WHERE  tbl_post_natal_familiy_plannings.facility_id='".$facility_id."' and  tbl_post_natal_familiy_plannings.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $postinal[] = DB::select($counceling);

        //badooo
        $fplaning="SELECT

  ifnull(sum(CASE when counselling_given='YES' AND timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) <20  then 1 ELSE  0 END ),0) as total_less_f_p_postinatal, ifnull(sum(CASE when counselling_given='YES' AND timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_f_p_postinatal,
   ifnull(sum(CASE when counselling_given='YES' AND  (timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) <20)  then 1 ELSE  0 END ),0) as total_f_p_postinatal,
  
  ifnull(sum(CASE when referral_for_family_planning='YES' AND timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_f_p_miscariage, ifnull(sum(CASE when referral_for_family_planning='YES' AND timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_f_p_miscariage,
   ifnull(sum(CASE when referral_for_family_planning='YES' AND  (timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_familiy_plannings.created_at) <20)  then 1 ELSE  0 END ),0) as total_f_p_miscariage
  
   from  tbl_post_natal_familiy_plannings  inner JOIN tbl_anti_natal_registers on  tbl_post_natal_familiy_plannings.patient_id=tbl_anti_natal_registers.id
  
 WHERE  tbl_post_natal_familiy_plannings.facility_id='' and  tbl_post_natal_familiy_plannings.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $postinal[] = DB::select($fplaning);



        $attendanceChild="SELECT
  
  ifnull(sum(CASE when attendance_range='48' AND   timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_range_48hr, ifnull(sum(CASE when attendance_range='48' AND timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_range_48hr,
   ifnull(sum(CASE when attendance_range='48' AND   (timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total_range_48hr,
 
 ifnull(sum(CASE when attendance_range='3-7' AND   timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_range_3_7day, ifnull(sum(CASE when attendance_range='3-7' AND timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_range_3_7day,
   ifnull(sum(CASE when attendance_range='3-7' AND  (timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total_range_3_7day,
  
   ifnull(sum(CASE when (attendance_range='3-7' or attendance_range='48') AND   timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_48_3_7, ifnull(sum(CASE when (attendance_range='3-7' or attendance_range='48') AND timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_48_3_7,
   ifnull(sum(CASE when(attendance_range='3-7' or attendance_range='48') AND  (timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total_48_3_7,
  
  ifnull(sum(CASE when ( attendance_range='48' AND attendance_range='3-7' AND attendance_range='8-28' AND attendance_range='29- 42') AND   timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_all_range, ifnull(sum(CASE when (attendance_range='48' AND attendance_range='3-7' AND attendance_range='8-28' AND attendance_range='29- 42') AND timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_all_range,
   ifnull(sum(CASE when(attendance_range='48' AND attendance_range='3-7' AND attendance_range='8-28' AND attendance_range='29- 42') AND  (timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total_all_range,
     
      ifnull(sum(CASE when hb <10 AND   timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_hb, ifnull(sum(CASE when hb <10 AND timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_hb,
   ifnull(sum(CASE when hb < 10 AND  (timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_child_attendances.created_at) <20)  then 1 ELSE  0 END ),0) as total_hb
  
   from  tbl_post_natal_child_attendances  inner JOIN tbl_anti_natal_registers on  tbl_post_natal_child_attendances.patient_id=tbl_anti_natal_registers.id
  
 WHERE  tbl_post_natal_child_attendances.facility_id='".$facility_id."' and  tbl_post_natal_child_attendances.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $postinal[] = DB::select($attendanceChild);


        $feedingChild_type="SELECT
  
  ifnull(sum(CASE when feeding_type='EBF' AND   tbl_post_natal_child_feedings.gender='MALE' then 1 ELSE  0 END ),0) as total_less_20_EBF, ifnull(sum(CASE when feeding_type='EBF' AND  tbl_post_natal_child_feedings.gender='FEMALE'   then 1 ELSE  0 END ),0) as total_above_20_EBF,
   ifnull(sum(CASE when feeding_type='EBF' AND   ( tbl_post_natal_child_feedings.gender='MALE' OR   tbl_post_natal_child_feedings.gender='FEMALE')  then 1 ELSE  0 END ),0) as total_EBF,
 
 ifnull(sum(CASE when feeding_type='RF' AND   tbl_post_natal_child_feedings.gender='MALE' then 1 ELSE  0 END ),0) as total_less_20_RF, ifnull(sum(CASE when feeding_type='RF' AND tbl_post_natal_child_feedings.gender='FEMALE'   then 1 ELSE  0 END ),0) as total_above_20_RF,
   ifnull(sum(CASE when feeding_type='RF' AND   ( tbl_post_natal_child_feedings.gender='MALE' OR   tbl_post_natal_child_feedings.gender='FEMALE') then 1 ELSE  0 END ),0) as total_RF,
  
   ifnull(sum(CASE when feeding_type='MF' AND   tbl_post_natal_child_feedings.gender='MALE'  then 1 ELSE  0 END ),0) as total_less_20_MF, ifnull(sum(CASE when feeding_type='MF' AND tbl_post_natal_child_feedings.gender='FEMALE'   then 1 ELSE  0 END ),0) as total_above_20_MF,
   ifnull(sum(CASE when  feeding_type='MF' AND   ( tbl_post_natal_child_feedings.gender='MALE' OR   tbl_post_natal_child_feedings.gender='FEMALE')  then 1 ELSE  0 END ),0) as total_MF
  
   
   from  tbl_post_natal_child_feedings  inner JOIN tbl_anti_natal_registers on  tbl_post_natal_child_feedings.patient_id=tbl_anti_natal_registers.id
  
 WHERE  tbl_post_natal_child_feedings.facility_id='".$facility_id."' and  tbl_post_natal_child_feedings.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $postinal[] = DB::select($feedingChild_type);

        $childInfection_maambukizi="SELECT
  ifnull(sum(CASE when high_infection='YES' AND   tbl_post_natal_child_infections.gender='MALE'  then 1 ELSE  0 END ),0) as total_male_makali, ifnull(sum(CASE when high_infection='YES' AND tbl_post_natal_child_infections.gender='FEMALE'  then 1 ELSE  0 END ),0) as total_female_makali,
   ifnull(sum(CASE when high_infection='YES' AND   (tbl_post_natal_child_infections.gender='MALE'  OR  tbl_post_natal_child_infections.gender='FEMALE')   then 1 ELSE  0 END ),0) as total_makali,
 
 ifnull(sum(CASE when navel='YES' AND   tbl_post_natal_child_infections.gender='MALE'  then 1 ELSE  0 END ),0) as total_male_kitovu, ifnull(sum(CASE when navel='YES' AND tbl_post_natal_child_infections.gender='FEMALE'   then 1 ELSE  0 END ),0) as total_female_kitovu,
   ifnull(sum(CASE when navel='YES' AND (tbl_post_natal_child_infections.gender='MALE'  OR  tbl_post_natal_child_infections.gender='FEMALE')  then 1 ELSE  0 END ),0) as total_kitovu,
  
  ifnull(sum(CASE when skin='YES' AND   tbl_post_natal_child_infections.gender='MALE'   then 1 ELSE  0 END ),0) as total_male_ngozi, ifnull(sum(CASE when skin='YES' AND  tbl_post_natal_child_infections.gender='FEMALE'    then 1 ELSE  0 END ),0) as total_female_ngozi,
   ifnull(sum(CASE when skin='YES' AND  (tbl_post_natal_child_infections.gender='MALE'  OR  tbl_post_natal_child_infections.gender='FEMALE')    then 1 ELSE  0 END ),0) as total_ngozi,
  
   ifnull(sum(CASE when jaundice='YES' AND   tbl_post_natal_child_infections.gender='MALE'   then 1 ELSE  0 END ),0) as total_male_jaundice, ifnull(sum(CASE when jaundice='YES' AND timestampdiff(YEAR ,dob,tbl_post_natal_child_infections.created_at) >20   then 1 ELSE  0 END ),0) as total_female_jaundice,
   ifnull(sum(CASE when  jaundice='YES' AND (tbl_post_natal_child_infections.gender='MALE'  OR  tbl_post_natal_child_infections.gender='FEMALE')   then 1 ELSE  0 END ),0) as total_jaundice
  
   
   from  tbl_post_natal_child_infections  inner JOIN tbl_anti_natal_registers on  tbl_post_natal_child_infections.patient_id=tbl_anti_natal_registers.id
  
 WHERE  tbl_post_natal_child_infections.facility_id='".$facility_id."' and  tbl_post_natal_child_infections.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $postinal[] = DB::select($childInfection_maambukizi);

        $pmtc="SELECT
  
 ifnull(sum(CASE when post_natal_vvu_infection_status='YES' AND   timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_post_natal_positive, ifnull(sum(CASE when post_natal_vvu_infection_status='YES' AND timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_post_natal_positive ,
   ifnull(sum(CASE when post_natal_vvu_infection_status='YES' AND  (timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) <20)  then 1 ELSE  0 END ),0) as total_post_natal_positive ,
  
  ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_post_natal_vvu_test_42, ifnull(sum(CASE when   timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_post_natal_vvu_test_42,
   ifnull(sum(CASE when    (timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) <20)  then 1 ELSE  0 END ),0) as total_post_natal_positive_vvu_test_42,
 
   ifnull(sum(CASE when post_natal_vvu_infection_status='YES'   AND   timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_post_natal_positive_42, ifnull(sum(CASE when  post_natal_vvu_infection_status='YES'   AND timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_post_natal_positive_42,
   ifnull(sum(CASE when post_natal_vvu_infection_status='YES'  AND  (timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) <20)  then 1 ELSE  0 END ),0) as total_post_natal_positive_42
  
  
   from  tbl_post_natal_pmtcts  inner JOIN tbl_anti_natal_registers on  tbl_post_natal_pmtcts.patient_id=tbl_anti_natal_registers.id
  
 WHERE  tbl_post_natal_pmtcts.facility_id='".$facility_id."' and  tbl_post_natal_pmtcts.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $postinal[] = DB::select($pmtc);
        
        $pmtc_feeding="SELECT
  
 ifnull(sum(CASE when post_natal_vvu_infection_status='YES' AND feeding_type='EBF' AND   timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_post_natal_positive_EBF, ifnull(sum(CASE when post_natal_vvu_infection_status='YES' AND feeding_type='EBF' AND timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_post_natal_positive_EBF ,
   ifnull(sum(CASE when post_natal_vvu_infection_status='YES' AND feeding_type='EBF' AND  (timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) <20)  then 1 ELSE  0 END ),0) as total_post_natal_positive_EBF ,
  
  ifnull(sum(CASE when post_natal_vvu_infection_status='YES' AND feeding_type='EBF' AND   timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) <20  then 1 ELSE  0 END ),0) as total_less_20_post_natal_positive_RF, ifnull(sum(CASE when post_natal_vvu_infection_status='YES' AND feeding_type='EBF' AND timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) >20   then 1 ELSE  0 END ),0) as total_above_20_post_natal_positive_RF ,
   ifnull(sum(CASE when post_natal_vvu_infection_status='YES' AND feeding_type='EBF' AND  (timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) >20 OR timestampdiff(YEAR ,dob,tbl_post_natal_pmtcts.created_at) <20)  then 1 ELSE  0 END ),0) as total_post_natal_positive_RF 
  
  
   from  tbl_post_natal_pmtcts  
   inner JOIN tbl_anti_natal_registers on  tbl_post_natal_pmtcts.patient_id=tbl_anti_natal_registers.id
   inner JOIN tbl_post_natal_child_feedings on  tbl_post_natal_pmtcts.patient_id=tbl_post_natal_child_feedings.patient_id
  
 WHERE  tbl_post_natal_pmtcts.facility_id='".$facility_id."' and  tbl_post_natal_pmtcts.created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $postinal[] = DB::select($pmtc_feeding);

        return $postinal;
    }
   


  

    
 

public function Tb_mtuha(Request $request)
    {
        $facility_id=$request->facility_id;
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        //return $request->all();

$tb=[];
        $tbAttendance="SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as male_less_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as female_less_5,
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >5  then 1 ELSE  0 END ),0) as male_above_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."')>5  then 1 ELSE  0 END ),0) as female_above_5,
  ifnull(sum(CASE when (gender ='MALE' or gender ='FEMALE') AND (timestampdiff(YEAR ,dob,'".$end_date."') <5 or timestampdiff(YEAR ,dob,'".$end_date."') >5 ) then 1 ELSE  0 END ),0) as total
   from   tbl_tb_pre_entry_registers  inner JOIN tbl_patients on   tbl_tb_pre_entry_registers.client_id=tbl_patients.id
 WHERE    tbl_tb_pre_entry_registers.facility_id='$facility_id' and  tbl_tb_pre_entry_registers.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $tb[] = DB::select($tbAttendance);

        $TBPatientTestingForHIV="SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as male_less_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as female_less_5,
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >5  then 1 ELSE  0 END ),0) as male_above_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."')>5  then 1 ELSE  0 END ),0) as female_above_5,
  ifnull(sum(CASE when (gender ='MALE' or gender ='FEMALE') AND (timestampdiff(YEAR ,dob,'".$end_date."') <5 or timestampdiff(YEAR ,dob,'".$end_date."') >5 ) then 1 ELSE  0 END ),0) as total
   from   tbl_vct_registers  inner JOIN tbl_patients on   tbl_vct_registers.client_id=tbl_patients.id
 WHERE vvu_test_result is not NULL  AND  tbl_vct_registers.facility_id='$facility_id' and  tbl_vct_registers.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $tb[] = DB::select($TBPatientTestingForHIV);

        $TBatientHIVPositive="SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as male_less_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as female_less_5,
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >5  then 1 ELSE  0 END ),0) as male_above_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."')>5  then 1 ELSE  0 END ),0) as female_above_5,
  ifnull(sum(CASE when (gender ='MALE' or gender ='FEMALE') AND (timestampdiff(YEAR ,dob,'".$end_date."') <5 or timestampdiff(YEAR ,dob,'".$end_date."') >5 ) then 1 ELSE  0 END ),0) as total
   from   tbl_vct_registers  inner JOIN tbl_tb_patient_treatment_types on   tbl_vct_registers.client_id=tbl_tb_patient_treatment_types.client_id 
   inner JOIN tbl_patients on   tbl_tb_patient_treatment_types.client_id=tbl_patients.id
 WHERE  vvu_test_result='POSITIVE' AND  tbl_vct_registers.facility_id='$facility_id' and  tbl_vct_registers.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $tb[] = DB::select($TBatientHIVPositive);
        $TBPatientReferredtoCTC="SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as male_less_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as female_less_5,
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >5  then 1 ELSE  0 END ),0) as male_above_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."')>5  then 1 ELSE  0 END ),0) as female_above_5,
  ifnull(sum(CASE when (gender ='MALE' or gender ='FEMALE') AND (timestampdiff(YEAR ,dob,'".$end_date."') <5 or timestampdiff(YEAR ,dob,'".$end_date."') >5 ) then 1 ELSE  0 END ),0) as total
   from   tbl_tb_pre_entry_registers  inner JOIN tbl_accounts_numbers on tbl_accounts_numbers.patient_id=tbl_tb_pre_entry_registers.client_id
inner join tbl_clinic_instructions on tbl_clinic_instructions.visit_id= tbl_accounts_numbers.id 
inner JOIN tbl_patients on   tbl_tb_pre_entry_registers.client_id=tbl_patients.id
 WHERE tbl_clinic_instructions.dept_id=8  AND tbl_tb_pre_entry_registers.facility_id='$facility_id' and  tbl_tb_pre_entry_registers.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";

        $tb[] = DB::select($TBPatientReferredtoCTC);

        $TBPatientRegisteredatCTC="SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as male_less_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as female_less_5,
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >5  then 1 ELSE  0 END ),0) as male_above_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."')>5  then 1 ELSE  0 END ),0) as female_above_5,
  ifnull(sum(CASE when (gender ='MALE' or gender ='FEMALE') AND (timestampdiff(YEAR ,dob,'".$end_date."') <5 or timestampdiff(YEAR ,dob,'".$end_date."') >5 ) then 1 ELSE  0 END ),0) as total
  from   tbl_tb_pre_entry_registers  inner JOIN tbl_accounts_numbers on tbl_accounts_numbers.patient_id=tbl_tb_pre_entry_registers.client_id
inner join tbl_clinic_instructions on tbl_clinic_instructions.visit_id= tbl_accounts_numbers.id 
inner JOIN tbl_patients on   tbl_tb_pre_entry_registers.client_id=tbl_patients.id
 WHERE tbl_clinic_instructions.dept_id=15  AND tbl_tb_pre_entry_registers.facility_id='$facility_id' and  tbl_tb_pre_entry_registers.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";

        $tb[] = DB::select($TBPatientRegisteredatCTC);

        $ReceivingCPT="SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as male_less_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as female_less_5,
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >5  then 1 ELSE  0 END ),0) as male_above_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."')>5  then 1 ELSE  0 END ),0) as female_above_5,
  ifnull(sum(CASE when (gender ='MALE' or gender ='FEMALE') AND (timestampdiff(YEAR ,dob,'".$end_date."') <5 or timestampdiff(YEAR ,dob,'".$end_date."') >5 ) then 1 ELSE  0 END ),0) as total
   from   tbl_tb_vvu_services  inner JOIN tbl_patients on   tbl_tb_vvu_services.client_id=tbl_patients.id
 WHERE  cpt='YES' AND  tbl_tb_vvu_services.facility_id='$facility_id' and  tbl_tb_vvu_services.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $tb[] = DB::select($ReceivingCPT);

        $ReceivingART="SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as male_less_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as female_less_5,
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >5  then 1 ELSE  0 END ),0) as male_above_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."')>5  then 1 ELSE  0 END ),0) as female_above_5,
  ifnull(sum(CASE when (gender ='MALE' or gender ='FEMALE') AND (timestampdiff(YEAR ,dob,'".$end_date."') <5 or timestampdiff(YEAR ,dob,'".$end_date."') >5 ) then 1 ELSE  0 END ),0) as total
   from   tbl_tb_vvu_services  inner JOIN tbl_patients on   tbl_tb_vvu_services.client_id=tbl_patients.id
 WHERE  art_start_date IS NOT  NULL AND  tbl_tb_vvu_services.facility_id='$facility_id' and  tbl_tb_vvu_services.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $tb[] = DB::select($ReceivingART);

        $Patientsonanti_TBfromCTC
            = "SELECT DISTINCT 
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as male_less_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as female_less_5,
  ifnull(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >5  then 1 ELSE  0 END ),0) as male_above_5, ifnull(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."')>5  then 1 ELSE  0 END ),0) as female_above_5,
  ifnull(sum(CASE when (gender ='MALE' or gender ='FEMALE') AND (timestampdiff(YEAR ,dob,'".$end_date."') <5 or timestampdiff(YEAR ,dob,'".$end_date."') >5 ) then 1 ELSE  0 END ),0) as total
  from   tbl_tb_patient_treatment_types  inner JOIN tbl_accounts_numbers on tbl_accounts_numbers.patient_id=tbl_tb_patient_treatment_types.client_id
inner join tbl_clinic_instructions on tbl_clinic_instructions.visit_id= tbl_accounts_numbers.id 
inner JOIN tbl_patients on   tbl_tb_patient_treatment_types.client_id=tbl_patients.id
 WHERE tbl_clinic_instructions.dept_id=15  AND tbl_tb_patient_treatment_types.facility_id='$facility_id' and  tbl_tb_patient_treatment_types.created_at BETWEEN '".$start_date."' and '".$end_date."'  ";

        $tb[] = DB::select($Patientsonanti_TBfromCTC);


        return $tb;
    }

	  public function mtuhaDentalReports(Request $request)
    {
        $response = [];
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start=date('Y-m-01 07:00:00');
            $end=date("Y-m-d H:i:s");
        }else{
            $start=$request->start_date;
            $end=$request->end_date;
        }
		

        $data = [];
        $sql0 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K02%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'    ";
        $sql1 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K05%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'    ";
        $sql2 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K01%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql3 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql4 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K03.1%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql5 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%S03.2%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql6 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%S00.5%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql7 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%S02.6%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql8 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%S02.4%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql9 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%M26.3%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql10 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%S01.5%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql11 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%S03.2%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql12 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%S03.2%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql13 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%T2%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql14 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K10.3%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql15 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%A69.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql16 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K04.7%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql17 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K10.3%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql18 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K12.2%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql19 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K10.2%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql20 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K04%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql21 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K05%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql22 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%D16%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql23 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%D37.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql24 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%D48.9%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql25 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K03%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql26 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K12.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql27 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%F45.8%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql28 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K00.3%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql29 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K00%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql30 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%B00.2%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql31 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K07.4%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql32 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%B26.9%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql33 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K09.1%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql34 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K09.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql35 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%B37.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql36 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%T81.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql37 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K00.6%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql38 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K11.2%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql39 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K03.8%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql40 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%S03.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'    ";
        $sql41 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%K11.7%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql42 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql43 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%V89%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql44 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%W0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql45 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%X8%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql46 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql47 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%X0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql48 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%Z09.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql49 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%T81.4%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql50 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%T81.9%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'     ";
        $sql51 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%T81.9%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'    ";


        //attendance

        $sql52 = "SELECT   ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_clinic_instructions t1 join tbl_accounts_numbers  t2 on t2.id=t1.visit_id  join tbl_patients  t3 on t3.id=t2.patient_id  where t1.dept_id=9  AND t2.facility_id='".$facility_id."' AND t1.created_at BETWEEN '".$start."' and '".$end."' ";
        $sql53 = "SELECT   ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_clinic_instructions t1 join tbl_accounts_numbers  t2 on t2.id=t1.visit_id  join tbl_patients  t3 on t3.id=t2.patient_id where t1.dept_id=9  AND t2.facility_id='".$facility_id."' AND t1.created_at BETWEEN '".$start."' and '".$end."' and not exists(select id from tbl_accounts_numbers t3 where t3.id <> t2.id and t3.patient_id = t3.patient_id)";
        
		$sql54 = "SELECT   ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year, ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total FROM tbl_clinic_instructions t1 join tbl_accounts_numbers  t2 on t2.id=t1.visit_id  join tbl_patients  t3 on t3.id=t2.patient_id  where t1.dept_id=9  AND t2.facility_id='".$facility_id."' AND t1.created_at BETWEEN '".$start."' and '".$end."' ";


        $data[] = DB::select(DB::raw($sql0))[0];
        $data[] = DB::select(DB::raw($sql1))[0];
        $data[] = DB::select(DB::raw($sql2))[0];
        $data[] = DB::select(DB::raw($sql3))[0];
        $data[] = DB::select(DB::raw($sql4))[0];
        $data[] = DB::select(DB::raw($sql5))[0];
        $data[] = DB::select(DB::raw($sql6))[0];
        $data[] = DB::select(DB::raw($sql7))[0];
        $data[] = DB::select(DB::raw($sql8))[0];
        $data[] = DB::select(DB::raw($sql9))[0];
        $data[] = DB::select(DB::raw($sql10))[0];
        $data[] = DB::select(DB::raw($sql11))[0];
        $data[] = DB::select(DB::raw($sql12))[0];
        $data[] = DB::select(DB::raw($sql13))[0];
        $data[] = DB::select(DB::raw($sql14))[0];
        $data[] = DB::select(DB::raw($sql15))[0];
        $data[] = DB::select(DB::raw($sql16))[0];
        $data[] = DB::select(DB::raw($sql17))[0];
        $data[] = DB::select(DB::raw($sql18))[0];
        $data[] = DB::select(DB::raw($sql19))[0];
        $data[] = DB::select(DB::raw($sql20))[0];
        $data[] = DB::select(DB::raw($sql21))[0];
        $data[] = DB::select(DB::raw($sql22))[0];
        $data[] = DB::select(DB::raw($sql23))[0];
        $data[] = DB::select(DB::raw($sql24))[0];
        $data[] = DB::select(DB::raw($sql25))[0];
        $data[] = DB::select(DB::raw($sql26))[0];
        $data[] = DB::select(DB::raw($sql27))[0];
        $data[] = DB::select(DB::raw($sql28))[0];
        $data[] = DB::select(DB::raw($sql29))[0];
        $data[] = DB::select(DB::raw($sql30))[0];
        $data[] = DB::select(DB::raw($sql31))[0];
        $data[] = DB::select(DB::raw($sql32))[0];
        $data[] = DB::select(DB::raw($sql33))[0];
        $data[] = DB::select(DB::raw($sql34))[0];
        $data[] = DB::select(DB::raw($sql35))[0];
        $data[] = DB::select(DB::raw($sql36))[0];
        $data[] = DB::select(DB::raw($sql37))[0];
        $data[] = DB::select(DB::raw($sql38))[0];
        $data[] = DB::select(DB::raw($sql39))[0];
        $data[] = DB::select(DB::raw($sql40))[0];
        $data[] = DB::select(DB::raw($sql41))[0];
        $data[] = DB::select(DB::raw($sql42))[0];
        $data[] = DB::select(DB::raw($sql43))[0];
        $data[] = DB::select(DB::raw($sql44))[0];
        $data[] = DB::select(DB::raw($sql45))[0];
        $data[] = DB::select(DB::raw($sql46))[0];
        $data[] = DB::select(DB::raw($sql47))[0];
        $data[] = DB::select(DB::raw($sql48))[0];
        $data[] = DB::select(DB::raw($sql49))[0];
        $data[] = DB::select(DB::raw($sql50))[0];
        $data[] = DB::select(DB::raw($sql51))[0];

        //attendance

        $data[] = DB::select(DB::raw($sql52))[0];
        $data[] = DB::select(DB::raw($sql53))[0];
        $data[] = DB::select(DB::raw($sql54))[0];
        return $data;
    }
    public function mtuhaEyeReports(Request $request)
    {

		$response = [];
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start=date('Y-m-01 07:00:00');
            $end=date("Y-m-d H:i:s");
        }else{
            $start=$request->start_date;
            $end=$request->end_date;
        }
		

        $data = [];
        $sql0 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                      FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H54.30%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql1 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H54.21%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql2 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H54.52%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql3 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H54.03%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql4 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H54.3%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql5 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H10%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql6 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%B30.9%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql7 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H10.1%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql8 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%P39.1%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql9 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%A71%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql10 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%A71%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql11 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H16.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql12 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H17.9%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql13 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H18%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql14 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H20.9%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql15 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%Q12%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql16 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H25%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql17 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H26.1%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql18 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H26%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql19 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H27.9%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql20 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H27.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql21 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%Z96.1%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql22 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%S05%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql23 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql24 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql25 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql26 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%S05.5%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql27 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%T15%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql28 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%E50.1%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql29 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%E50.4%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql30 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%E50.5%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql31 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%Q15.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql32 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H40.8%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql33 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%E14.3%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql34 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H35.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql35 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H33%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql36 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H35.3%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql37 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%C69.2%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql38 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H35.9%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql39 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H46-H46%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql40 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                           FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H04%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql41 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%Q10%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql42 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H02%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql43 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H11.9%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql44 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%D31%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql45 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H50%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql46 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%G52%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql47 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%Q15%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql48 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H57%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql49 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql50 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql51 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H52.7%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql52 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H5O.2%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql53 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H52.1%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql54 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H52.2%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql55 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H52.4%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql56 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%H53.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql57 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql58 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql59 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql60 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql61 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%Z01.0%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql62 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql63 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql64 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql65 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql66 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql67 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql68 = "SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                         FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3         on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'   ";
        $sql69 = " SELECT           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total  FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id join tbl_diagnoses t3   on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id where  t2.code like '%NULL%' AND t3.facility_id='".$facility_id."' AND t3.created_at BETWEEN '".$start."' and '".$end."'  AND t1.status='Confirmed'";

        //attendance
        $sql70 = "SELECT   ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_clinic_instructions t1 join tbl_accounts_numbers  t2 on t2.id=t1.visit_id  join tbl_patients  t3 on t3.id=t2.patient_id  where t1.dept_id=10  AND t2.facility_id='".$facility_id."' AND t1.created_at BETWEEN '".$start."' and '".$end."' ";
        $sql71 = "SELECT   ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_clinic_instructions t1 join tbl_accounts_numbers  t2 on t2.id=t1.visit_id  join tbl_patients  t3 on t3.id=t2.patient_id  where t1.dept_id=10  AND t2.facility_id='".$facility_id."' AND t1.created_at BETWEEN '".$start."' and '".$end."'  and not exists(select id from tbl_accounts_numbers t3 where t3.id <> t2.id and t3.patient_id = t3.patient_id)";
        
        $sql72 = "SELECT   ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_clinic_instructions t1 join tbl_accounts_numbers  t2 on t2.id=t1.visit_id  join tbl_patients  t3 on t3.id=t2.patient_id  where t1.dept_id=10  AND t2.facility_id='".$facility_id."' AND t1.created_at BETWEEN '".$start."' and '".$end."' ";
        $sql73 = "SELECT   ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as total_less_5year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as male_between_5_14year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0)          as female_between_5_14year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at) BETWEEN 5 and 14  then 1 ELSE  0 END ),0) as total_between_5_14year,                           ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as male_above_15year,         ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as female_above_15year,         ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=15  then 1 ELSE  0 END ),0) as total_above_15year,                       ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,         ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,         ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total                                 FROM tbl_clinic_instructions t1 join tbl_accounts_numbers  t2 on t2.id=t1.visit_id  join tbl_patients  t3 on t3.id=t2.patient_id  where t1.dept_id=10  AND t2.facility_id='".$facility_id."' AND t1.created_at BETWEEN '".$start."' and '".$end."' ";



        $data[] = DB::select(DB::raw($sql0))[0];
        $data[] = DB::select(DB::raw($sql1))[0];
        $data[] = DB::select(DB::raw($sql2))[0];
        $data[] = DB::select(DB::raw($sql3))[0];
        $data[] = DB::select(DB::raw($sql4))[0];
        $data[] = DB::select(DB::raw($sql5))[0];
        $data[] = DB::select(DB::raw($sql6))[0];
        $data[] = DB::select(DB::raw($sql7))[0];
        $data[] = DB::select(DB::raw($sql8))[0];
        $data[] = DB::select(DB::raw($sql9))[0];
        $data[] = DB::select(DB::raw($sql10))[0];
        $data[] = DB::select(DB::raw($sql11))[0];
        $data[] = DB::select(DB::raw($sql12))[0];
        $data[] = DB::select(DB::raw($sql13))[0];
        $data[] = DB::select(DB::raw($sql14))[0];
        $data[] = DB::select(DB::raw($sql15))[0];
        $data[] = DB::select(DB::raw($sql16))[0];
        $data[] = DB::select(DB::raw($sql17))[0];
        $data[] = DB::select(DB::raw($sql18))[0];
        $data[] = DB::select(DB::raw($sql19))[0];
        $data[] = DB::select(DB::raw($sql20))[0];
        $data[] = DB::select(DB::raw($sql21))[0];
        $data[] = DB::select(DB::raw($sql22))[0];
        $data[] = DB::select(DB::raw($sql23))[0];
        $data[] = DB::select(DB::raw($sql24))[0];
        $data[] = DB::select(DB::raw($sql25))[0];
        $data[] = DB::select(DB::raw($sql26))[0];
        $data[] = DB::select(DB::raw($sql27))[0];
        $data[] = DB::select(DB::raw($sql28))[0];
        $data[] = DB::select(DB::raw($sql29))[0];
        $data[] = DB::select(DB::raw($sql30))[0];
        $data[] = DB::select(DB::raw($sql31))[0];
        $data[] = DB::select(DB::raw($sql32))[0];
        $data[] = DB::select(DB::raw($sql33))[0];
        $data[] = DB::select(DB::raw($sql34))[0];
        $data[] = DB::select(DB::raw($sql35))[0];
        $data[] = DB::select(DB::raw($sql36))[0];
        $data[] = DB::select(DB::raw($sql37))[0];
        $data[] = DB::select(DB::raw($sql38))[0];
        $data[] = DB::select(DB::raw($sql39))[0];
        $data[] = DB::select(DB::raw($sql40))[0];
        $data[] = DB::select(DB::raw($sql41))[0];
        $data[] = DB::select(DB::raw($sql42))[0];
        $data[] = DB::select(DB::raw($sql43))[0];
        $data[] = DB::select(DB::raw($sql44))[0];
        $data[] = DB::select(DB::raw($sql45))[0];
        $data[] = DB::select(DB::raw($sql46))[0];
        $data[] = DB::select(DB::raw($sql47))[0];
        $data[] = DB::select(DB::raw($sql48))[0];
        $data[] = DB::select(DB::raw($sql49))[0];
        $data[] = DB::select(DB::raw($sql50))[0];
        $data[] = DB::select(DB::raw($sql51))[0];
        $data[] = DB::select(DB::raw($sql52))[0];
        $data[] = DB::select(DB::raw($sql53))[0];
        $data[] = DB::select(DB::raw($sql54))[0];
        $data[] = DB::select(DB::raw($sql55))[0];
        $data[] = DB::select(DB::raw($sql56))[0];
        $data[] = DB::select(DB::raw($sql57))[0];
        $data[] = DB::select(DB::raw($sql58))[0];
        $data[] = DB::select(DB::raw($sql59))[0];
        $data[] = DB::select(DB::raw($sql60))[0];
        $data[] = DB::select(DB::raw($sql61))[0];
        $data[] = DB::select(DB::raw($sql62))[0];
        $data[] = DB::select(DB::raw($sql63))[0];
        $data[] = DB::select(DB::raw($sql64))[0];
        $data[] = DB::select(DB::raw($sql65))[0];
        $data[] = DB::select(DB::raw($sql66))[0];
        $data[] = DB::select(DB::raw($sql67))[0];
        $data[] = DB::select(DB::raw($sql68))[0];
        $data[] = DB::select(DB::raw($sql69))[0];
        //attendance

        $data[] = DB::select(DB::raw($sql70))[0];
        $data[] = DB::select(DB::raw($sql71))[0];
        $data[] = DB::select(DB::raw($sql72))[0];
        $data[] = DB::select(DB::raw($sql73))[0];
        return $data;
    }
	
	
	public function getBedOccupancy(Request $request){
		$response = [];
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }
		
		$record = DB::select("SELECT 'a.' as entry ,'Vitanda Vilivyopo' as description, (SELECT COUNT(*) FROM tbl_wards JOIN tbl_beds ON tbl_wards.ward_type_code = 'MARTEN' AND tbl_wards.id = tbl_beds.ward_id where tbl_wards.facility_id='$facility_id') as martenity,(SELECT COUNT(*) FROM tbl_wards JOIN tbl_beds ON tbl_wards.ward_type_code IS NULL AND tbl_wards.id = tbl_beds.ward_id where tbl_wards.facility_id='$facility_id') as non_martenity");
		$response[] = $record[0];
		
		$record = DB::select("SELECT 'b.' as entry ,'Wagonjwa waliolazwa' as description,(SELECT COUNT(*) FROM tbl_admissions JOIN tbl_instructions ON tbl_admissions.id = tbl_instructions.admission_id JOIN tbl_wards ON tbl_instructions.ward_id = tbl_wards.id AND tbl_wards.ward_type_code = 'MARTEN'  where tbl_wards.facility_id='$facility_id') as martenity,(SELECT COUNT(*) FROM tbl_admissions JOIN tbl_instructions ON tbl_admissions.id = tbl_instructions.admission_id JOIN tbl_wards ON tbl_instructions.ward_id = tbl_wards.id AND tbl_wards.ward_type_code IS NULL  where tbl_wards.facility_id='$facility_id') as non_martenity");
		$response[] = $record[0];
		
		$record = DB::select("SELECT 'c.' as entry ,'Waliopata kitanda' as description,(SELECT COUNT(*) FROM tbl_admissions JOIN tbl_instructions ON tbl_admissions.id = tbl_instructions.admission_id AND tbl_admissions.admission_status_id = 2 AND timestampdiff(day,tbl_admissions.admission_date, CURRENT_DATE) =1 JOIN tbl_wards ON tbl_instructions.ward_id = tbl_wards.id AND tbl_wards.ward_type_code = 'MARTEN' JOIN tbl_beds ON tbl_instructions.bed_id = tbl_beds.id AND tbl_beds.bed_type_id <> 4 AND  tbl_wards.facility_id='$facility_id') as martenity,(SELECT COUNT(*) FROM tbl_admissions JOIN tbl_instructions ON tbl_admissions.id = tbl_instructions.admission_id AND tbl_admissions.admission_status_id = 2 AND timestampdiff(day,tbl_admissions.admission_date, CURRENT_DATE) =1 JOIN tbl_wards ON tbl_instructions.ward_id = tbl_wards.id AND tbl_wards.ward_type_code IS NULL  JOIN tbl_beds ON tbl_instructions.bed_id = tbl_beds.id AND tbl_beds.bed_type_id <> 4  AND tbl_wards.facility_id='$facility_id') as non_martenity");
		$response[] = $record[0];
		
		$record = DB::select("SELECT 'd.' as entry ,'Waliokosa kitanda' as description,(SELECT COUNT(*) FROM tbl_admissions JOIN tbl_instructions ON tbl_admissions.id = tbl_instructions.admission_id AND tbl_admissions.admission_status_id = 2 AND timestampdiff(day,tbl_admissions.admission_date ,CURRENT_DATE) =1 JOIN tbl_wards ON tbl_instructions.ward_id = tbl_wards.id AND tbl_wards.ward_type_code = 'MARTEN' JOIN tbl_beds ON tbl_instructions.bed_id = tbl_beds.id AND tbl_beds.bed_type_id = 4 AND tbl_wards.facility_id='$facility_id') as martenity,(SELECT COUNT(*) FROM tbl_admissions JOIN tbl_instructions ON tbl_admissions.id = tbl_instructions.admission_id AND tbl_admissions.admission_status_id = 2 AND timestampdiff(day,tbl_admissions.admission_date, CURRENT_DATE) =1 JOIN tbl_wards ON tbl_instructions.ward_id = tbl_wards.id AND tbl_wards.ward_type_code IS NULL  JOIN tbl_beds ON tbl_instructions.bed_id = tbl_beds.id AND tbl_beds.bed_type_id = 4 AND tbl_wards.facility_id='$facility_id') as non_martenity");
		$response[] = $record[0];
		
		return $response;
	}

 //new reports isdr and sti start
    public function isdrReports(Request $request)
    {
        $response = [];
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }

        $sql0 = " SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5year,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5year,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id 
    join tbl_diagnoses t3   on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id 
    where  t2.code like '%E4%' AND t3.facility_id='".$facility_id."' AND t3.created_at 
    BETWEEN '".$start_date."' and '".$end_date."'  AND t1.status='Confirmed'";

        $sql1 = " SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5year,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5year,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id 
    join tbl_diagnoses t3   on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id 
    where  t2.code like '%T63%' AND t3.facility_id='".$facility_id."' AND t3.created_at 
    BETWEEN '".$start_date."' and '".$end_date."'  AND t1.status='Confirmed'";

        $sql2 = " SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5year,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5year,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id 
    join tbl_diagnoses t3   on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id 
    where  t2.code like '%A09%' OR t2.code like '%K59.1%' OR t2.code like '%P78.3%' AND t3.facility_id='".$facility_id."' AND t3.created_at 
    BETWEEN '".$start_date."' and '".$end_date."'  AND t1.status='Confirmed'";

        $sql3 = " SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5year,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5year,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id 
    join tbl_diagnoses t3   on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id 
    where  t2.code like '%J1%' AND t3.facility_id='".$facility_id."' AND t3.created_at 
    BETWEEN '".$start_date."' and '".$end_date."'  AND t1.status='Confirmed'";

        $sql4 = " SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5year,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5year,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id 
    join tbl_diagnoses t3   on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id 
    where  t2.code like '%A01%' AND t3.facility_id='".$facility_id."' AND t3.created_at 
    BETWEEN '".$start_date."' and '".$end_date."'  AND t1.status='Confirmed'";

        $sql5 = " SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5year,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5year,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id 
    join tbl_diagnoses t3   on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id 
    where  t2.code like '%A68%' AND t3.facility_id='".$facility_id."' AND t3.created_at 
    BETWEEN '".$start_date."' and '".$end_date."'  AND t1.status='Confirmed'";

        $sql6 = " SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5year,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5year,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id 
    join tbl_diagnoses t3   on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id 
    where  t2.code like '%B73%' AND t3.facility_id='".$facility_id."' AND t3.created_at 
    BETWEEN '".$start_date."' and '".$end_date."'  AND t1.status='Confirmed'";

        $sql7 = " SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5year,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5year,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_15year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id 
    join tbl_diagnoses t3   on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id 
    where  t2.code like '%A71%' AND t3.facility_id='".$facility_id."' AND t3.created_at 
    BETWEEN '".$start_date."' and '".$end_date."'  AND t1.status='Confirmed'";

        $sql8 = " SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_less_5year,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_less_5year,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5year,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5year,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id 
    join tbl_diagnoses t3   on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id 
    where  t2.code like '%B56%' AND t3.facility_id='".$facility_id."' AND t3.created_at 
    BETWEEN '".$start_date."' and '".$end_date."'  AND t1.status='Confirmed' ";

        $sql9 = " SELECT IFNULL(sum(CASE when gender ='MALE' OR gender='FEMALE'  AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as grand_total_under_five,
    IFNULL(sum(CASE when gender ='MALE' OR gender='FEMALE'  AND timestampdiff(YEAR ,dob,'".$end_date."') >5  then 1 ELSE  0 END ),0) as grand_total_above_five
    FROM tbl_patients t1 JOIN tbl_requests t2 ON t1.id = t2.patient_id 
    JOIN tbl_results t3 ON t3.order_id=t2.id 
    WHERE item_id = '39'  AND t3.updated_at BETWEEN '".$start_date."' AND '".$end_date."' ";
        $sql10 = " SELECT IFNULL(sum(CASE when gender ='MALE' OR gender='FEMALE'  AND timestampdiff(YEAR ,dob,'".$end_date."') <5  then 1 ELSE  0 END ),0) as grand_total_under_five,
    IFNULL(sum(CASE when gender ='MALE' OR gender='FEMALE'  AND timestampdiff(YEAR ,dob,'".$end_date."') >5  then 1 ELSE  0 END ),0) as grand_total_above_five
    FROM tbl_patients t1 JOIN tbl_requests t2 ON t1.id = t2.patient_id 
    JOIN tbl_results t3 ON t3.order_id=t2.id 
    WHERE item_id = '39' AND t3.description LIKE '%POS%'  AND t3.updated_at BETWEEN '".$start_date."' AND '".$end_date."' ";

        $sql11 = "SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_under_5_death,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_under_5_death,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5_death,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5_death,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_corpses t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_id=t2.id   
    where  t1.diagnosis_code like '%E4%' AND t1.created_at BETWEEN '".$start_date."' AND '".$end_date."' ";

        $sql12 = "SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_under_5_death,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_under_5_death,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5_death,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5_death,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_corpses t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_id=t2.id   
    where  t1.diagnosis_code like '%T63%' AND t1.created_at BETWEEN '".$start_date."' AND '".$end_date."' ";

        $sql13 = "SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_under_5_death,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_under_5_death,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5_death,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5_death,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_corpses t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_id=t2.id   
    where  t1.diagnosis_code like '%A09%' AND t1.created_at BETWEEN '".$start_date."' AND '".$end_date."' ";

        $sql14 = "SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_under_5_death,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_under_5_death,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5_death,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5_death,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_corpses t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_id=t2.id   
    where  t1.diagnosis_code like '%J1%' AND t1.created_at BETWEEN '".$start_date."' AND '".$end_date."' ";

        $sql15 = "SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_under_5_death,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_under_5_death,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5_death,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5_death,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_corpses t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_id=t2.id   
    where  t1.diagnosis_code like '%A01%' AND t1.created_at BETWEEN '".$start_date."' AND '".$end_date."' ";

        $sql16 = "SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_under_5_death,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_under_5_death,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5_death,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5_death,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_corpses t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_id=t2.id   
    where  t1.diagnosis_code like '%A68%' AND t1.created_at BETWEEN '".$start_date."' AND '".$end_date."' ";

        $sql17 = "SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_under_5_death,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_under_5_death,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5_death,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5_death,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_corpses t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_id=t2.id   
    where  t1.diagnosis_code like '%B73%' AND t1.created_at BETWEEN '".$start_date."' AND '".$end_date."' ";

        $sql18 = "SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_under_5_death,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_under_5_death,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5_death,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5_death,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_corpses t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_id=t2.id   
    where  t1.diagnosis_code like '%A71%' AND t1.created_at BETWEEN '".$start_date."' AND '".$end_date."' ";

        $sql19 = "SELECT ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as male_under_5_death,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<5  then 1 ELSE  0 END ),0) as female_under_5_death,
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as male_above_5_death,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as female_above_5_death,
    ifnull(sum(CASE when  timestampdiff(year,dob,t1.created_at)>=5  then 1 ELSE  0 END ),0) as total_above_5year, 
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_grand_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_grand_total,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as all_grand_total 
    FROM tbl_corpses t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_id=t2.id   
    where  t1.diagnosis_code like '%B56%' AND t1.created_at BETWEEN '".$start_date."' AND '".$end_date."' ";

        $response[]= DB::select(DB::raw($sql0));
        $response[]= DB::select(DB::raw($sql1));
        $response[]= DB::select(DB::raw($sql2));
        $response[]= DB::select(DB::raw($sql3));
        $response[]= DB::select(DB::raw($sql4));
        $response[]= DB::select(DB::raw($sql5));
        $response[]= DB::select(DB::raw($sql6));
        $response[]= DB::select(DB::raw($sql7));
        $response[]= DB::select(DB::raw($sql8));
        $response[]= DB::select(DB::raw($sql9));
        $response[]= DB::select(DB::raw($sql10));

        $response[]= DB::select(DB::raw($sql11));
        $response[]= DB::select(DB::raw($sql12));
        $response[]= DB::select(DB::raw($sql13));
        $response[]= DB::select(DB::raw($sql14));
        $response[]= DB::select(DB::raw($sql15));
        $response[]= DB::select(DB::raw($sql16));
        $response[]= DB::select(DB::raw($sql17));
        $response[]= DB::select(DB::raw($sql18));
        $response[]= DB::select(DB::raw($sql19));

        return $response;
    }

    public function stiReports(Request $request)
    {
        $response = [];
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }

        $sql0 = " 
    SELECT 
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<15  then 1 ELSE  0 END ),0) as male_under_15,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<15  then 1 ELSE  0 END ),0) as female_under_15,
    
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 15 and 24  then 1 ELSE  0 END ),0) as male_15_24,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 15 and 24  then 1 ELSE  0 END ),0) as female_15_24, 
     
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 25 and 34  then 1 ELSE  0 END ),0) as male_25_34,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 25 and 34  then 1 ELSE  0 END ),0) as female_25_34,
    
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 35 and 49  then 1 ELSE  0 END ),0) as male_35_49,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 35 and 49  then 1 ELSE  0 END ),0) as female_35_49,
    
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) >=50  then 1 ELSE  0 END ),0) as male_above_50year,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) >=50 then 1 ELSE  0 END ),0) as female_above_50year,
    
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_total, 
     
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as grand_total 
    
    FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id 
    
    join tbl_diagnoses t3   on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id 
    
    where  t2.code like '%E4%' AND t3.facility_id='".$facility_id."' AND t3.created_at
     
    BETWEEN '".$start_date."' and '".$end_date."'  AND t1.status='Confirmed'";

        $sql1 = " 
    SELECT 
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<15  then 1 ELSE  0 END ),0) as male_under_15,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<15  then 1 ELSE  0 END ),0) as female_under_15,
    
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 15 and 24  then 1 ELSE  0 END ),0) as male_15_24,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 15 and 24  then 1 ELSE  0 END ),0) as female_15_24, 
     
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 25 and 34  then 1 ELSE  0 END ),0) as male_25_34,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 25 and 34  then 1 ELSE  0 END ),0) as female_25_34,
    
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 35 and 49  then 1 ELSE  0 END ),0) as male_35_49,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 35 and 49  then 1 ELSE  0 END ),0) as female_35_49,
    
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) >=50  then 1 ELSE  0 END ),0) as male_above_50year,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) >=50 then 1 ELSE  0 END ),0) as female_above_50year,
    
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_total, 
     
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as grand_total 
    
    FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id 
    
    join tbl_diagnoses t3   on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id 
    
    where  t2.code like '%E4%' AND t3.facility_id='".$facility_id."' AND t3.created_at
     
    BETWEEN '".$start_date."' and '".$end_date."'  AND t1.status='Confirmed'";

        $sql2 = " 
    SELECT 
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at)<15  then 1 ELSE  0 END ),0) as male_under_15,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at)<15  then 1 ELSE  0 END ),0) as female_under_15,
    
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 15 and 24  then 1 ELSE  0 END ),0) as male_15_24,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 15 and 24  then 1 ELSE  0 END ),0) as female_15_24, 
     
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 25 and 34  then 1 ELSE  0 END ),0) as male_25_34,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 25 and 34  then 1 ELSE  0 END ),0) as female_25_34,
    
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 35 and 49  then 1 ELSE  0 END ),0) as male_35_49,  
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) BETWEEN 35 and 49  then 1 ELSE  0 END ),0) as female_35_49,
    
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,t1.created_at) >=50  then 1 ELSE  0 END ),0) as male_above_50year,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,t1.created_at) >=50 then 1 ELSE  0 END ),0) as female_above_50year,
    
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as male_total,    
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as female_total, 
     
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as grand_total 
    
    FROM tbl_diagnosis_details t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_description_id=t2.id 
    
    join tbl_diagnoses t3   on t1.diagnosis_id=t3.id  join tbl_patients t4 on t4.id=t3.patient_id 
    
    where  t2.code='N73.5' OR t2.code='N73' AND t3.facility_id='".$facility_id."' AND t3.created_at
     
    BETWEEN '".$start_date."' and '".$end_date."'  AND t1.status='Confirmed'";


        $response[]= DB::select(DB::raw($sql0));
        $response[]= DB::select(DB::raw($sql1));
        $response[]= DB::select(DB::raw($sql2));

        return $response;

    }

    public function deathReport(Request $request)
    {

        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }
        $sql = "SELECT t2.description,ifnull(sum(CASE when gender='MALE'  AND timestampdiff(MONTH ,dob,'".$end_date."')<1  then 1 ELSE  0 END ),0) as male_under_one_month,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(MONTH,dob,'".$end_date."')<1  then 1 ELSE  0 END ),0) as female_under_one_month, 
    ifnull(sum(CASE when  timestampdiff(MONTH,dob,'".$end_date."')<1  then 1 ELSE  0 END ),0) as total_under_one_month, 
    
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(MONTH ,dob,'".$end_date."') BETWEEN 1 AND 12  then 1 ELSE  0 END ),0) as male_under_one_year,
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(MONTH,dob,'".$end_date."') BETWEEN 1 AND 12  then 1 ELSE  0 END ),0) as female_under_one_year, 
    ifnull(sum(CASE when  timestampdiff(MONTH,dob,'".$end_date."') BETWEEN 1 AND 12 then 1 ELSE  0 END ),0) as total_under_one_year, 
    
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,'".$end_date."') BETWEEN 1 and 5  then 1 ELSE  0 END ),0)          as male_under_five_year,   
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,'".$end_date."') BETWEEN 1 and 5  then 1 ELSE  0 END ),0)          as female_under_five_year, 
    ifnull(sum(CASE when  timestampdiff(year,dob,'".$end_date."') BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as total_under_five_year,
    
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,'".$end_date."') BETWEEN 6 and 59  then 1 ELSE  0 END ),0)          as male_above_five_under_sixty,   
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,'".$end_date."') BETWEEN 6 and 59  then 1 ELSE  0 END ),0)          as female_above_five_under_sixty, 
    ifnull(sum(CASE when  timestampdiff(year,dob,'".$end_date."') BETWEEN 6 and 59  then 1 ELSE  0 END ),0) as total_above_five_under_sixty,
     
    ifnull(sum(CASE when gender='MALE'  AND timestampdiff(year,dob,'".$end_date."') >60  then 1 ELSE  0 END ),0)          as male_above_sixty,   
    ifnull(sum(CASE when gender='FEMALE'  AND timestampdiff(year,dob,'".$end_date."') >60 then 1 ELSE  0 END ),0)          as female_above_sixty, 
    ifnull(sum(CASE when  timestampdiff(year,dob,'".$end_date."') >60  then 1 ELSE  0 END ),0) as total_above_sixty, 
    
            
    ifnull(sum(CASE when gender='MALE'    then 1 ELSE  0 END ),0) as total_male,   
    ifnull(sum(CASE when gender='FEMALE'  then 1 ELSE  0 END ),0) as total_female,  
    ifnull(sum(CASE when  gender='MALE' or gender='FEMALE'  then 1 ELSE  0 END ),0) as grand_total 
    FROM tbl_corpses t1 join tbl_diagnosis_descriptions t2 on  t1.diagnosis_id=t2.id 
    where  t1.updated_at BETWEEN '".$start_date."' and '".$end_date."'  AND t1.facility_id='".$facility_id."' GROUP BY t1.diagnosis_id   ";

        return DB::select(DB::raw($sql));
    }


    //new reports isdr and sti end


  public function showBookTopTen(request $request){
        $response = [];
        $facility_id=$request->facility_id;
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }

        //return DB::select(DB::raw("SELECT t1.description,t3.created_at,count(diagnosis_description_id) as count FROM tbl_diagnosis_descriptions t1 join tbl_diagnosis_details t2 on t1.id=t2.diagnosis_description_id JOIN tbl_diagnoses t3 on t3.id=t2.diagnosis_id where t2.status='Confirmed' and t3.created_at BETWEEN '".$start_date."' and '".$end_date."' GROUP BY  diagnosis_description_id order by count(diagnosis_description_id) DESC limit 10"));
         return DB::select("SELECT t1.description,t3.created_at,count(diagnosis_description_id) as count,
    IFNULL(sum(CASE when gender ='FEMALE' AND timestampdiff(MONTH ,dob,'".$end_date."') <1  then 1 ELSE  0 END ),0) as female_0_1_month,
    IFNULL(sum(CASE when gender ='FEMALE' AND timestampdiff(MONTH ,dob,'".$end_date."') BETWEEN 1 and 11  then 1 ELSE  0 END ),0) as female_0_11_month,
    IFNULL(sum(CASE when gender ='FEMALE' AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as female_1_5_year,
    IFNULL(sum(CASE when gender ='FEMALE' AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 6 and 59  then 1 ELSE  0 END ),0) as female_6_59_year,  
    IFNULL(sum(CASE when gender ='FEMALE' AND timestampdiff(YEAR ,dob,'".$end_date."') >60  then 1 ELSE  0 END ),0) as female_above_60_year,
    IFNULL(sum(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0) as female_total_attendance,
    IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <1  then 1 ELSE  0 END ),0) as male_0_1_month,
    IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') BETWEEN 1 and 11  then 1 ELSE  0 END ),0) as male_0_11_month,
    IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 1 and 5  then 1 ELSE  0 END ),0)as male_1_5_year,
    IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 6 and 59  then 1 ELSE  0 END ),0) as male_6_59_year, 
    IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >60  then 1 ELSE  0 END ),0) as male_above_60_year,
    IFNULL(sum(CASE when  timestampdiff(MONTH ,dob,'".$end_date."') <1  then 1 ELSE  0 END ),0) as total_0_1_month, 
    IFNULL(sum(CASE when  timestampdiff(MONTH ,dob,'".$end_date."') BETWEEN 1 and 11  then 1 ELSE  0 END ),0) as total_0_11_month, 
    IFNULL(sum(CASE when  timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 1 and 5  then 1 ELSE  0 END ),0) as total_1_5_year, 
    IFNULL(sum(CASE when  timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 6 and 59  then 1 ELSE  0 END ),0) as total_6_59_year, 
    IFNULL(sum(CASE when  timestampdiff(YEAR ,dob,'".$end_date."') >60  then 1 ELSE  0 END ),0) as total_above_60_year, 
    IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance,
    IFNULL(sum(CASE when gender ='MALE' OR gender='FEMALE'  then 1 ELSE  0 END ),0) as grand_total_attendance
    FROM tbl_diagnosis_descriptions t1 join tbl_diagnosis_details t2 on t1.id=t2.diagnosis_description_id JOIN tbl_diagnoses t3 on t3.id=t2.diagnosis_id
	join tbl_patients t5 on t5.id=t3.patient_id
     where t2.status='Confirmed' and t3.created_at BETWEEN '".$start_date."' and '".$end_date."' GROUP BY  diagnosis_description_id order by count(diagnosis_description_id) DESC limit 10");
          }

}