<?php

namespace App\Http\Controllers\Integrations\Dashboard\Data;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Facility\Tbl_facility;
use Illuminate\Support\Facades\Config;
use DB;
use Artisan;

Artisan::call('config:clear');
Artisan::call('config:cache');

/*
if(!defined('REPORTING_SERVER'))
	define('REPORTING_SERVER', '196.192.72.107:8080');
if(!defined('LAST_REPORTING_DATE_URL'))
	define('LAST_REPORTING_DATE_URL', REPORTING_SERVER.'/dashboard/last_reporting__date');
*/

class DashboardReportingController extends Controller
{
	//default date for last report sent to central server
	private $last_reporting_date;
	
	private $REPORTING_SERVER;
	private $LAST_REPORTING_DATE_URL;
	
	private $facility_id;
	private $facility_code;
	
	//error/success reporting
	private $dashboard_posting_success;
	private $last_reporting_date_errors;
	private $payload_computation_errors;
	private $dashboard_posting_errors;
	
    public function __construct(Request $request){
		ini_set('max_execution_time', -1);
		
		$this->REPORTING_SERVER = '196.192.72.107:8080';
		$this->LAST_REPORTING_DATE_URL = $this->REPORTING_SERVER.'/dashboard/last_reporting__date';
		
		$this->facility_id = $request->facility_id;
		
		$this->facility_code = Tbl_facility::where("id",$request->facility_id)->get()[0]->facility_code;
		//due to presence of installations that had removed the dash (-) from HFR, this logic
		//makes sure the dash is re-introduced before making request to central server
		$this->facility_code = preg_replace("/[_-]/","",$this->facility_code);
		$this->facility_code = substr_replace($this->facility_code,"-",strlen($this->facility_code)-1,0);
		
		$this->reports = [
						[
							"report_name" => "FACILITY'S NEW OPD ATTENDANCES",
							"report_code" => "FACILITY_NEW_OPD_ATTENDANCE",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/facility_new_opd_attendance",
							"computation" =>[
										"procedure" =>  "generate_facility_new_opd_attendance_report_for_dashboard",
										"datatable" => "dashboard_reporting_facility_new_opd_attendances",
									],
						],
						
						[
							"report_name" => "HMIS NEW OPD ATTENDANCES",
							"report_code" => "HMIS_NEW_OPD_ATTENDANCE",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/hmis_new_opd_attendance",
							"computation" =>[
										"procedure" =>  "generate_hmis_new_opd_attendance_report_for_dashboard",
										"datatable" => "dashboard_reporting_hmis_new_opd_attendances",
									],
						],
										
						[
							"report_name" => "FACILITY'S OPD REATTENDANCES",
							"report_code" => "FACILITY_OPD_REATTENDANCE",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/facility_opd_reattendance",
							"computation" =>[
										"procedure" =>  "generate_facility_opd_reattendance_report_for_dashboard",
										"datatable" => "dashboard_reporting_facility_opd_reattendances",
									],
						],
										
						[
							"report_name" => "HMIS OPD REATTENDANCES",
							"report_code" => "HMIS_OPD_REATTENDANCE",
							"posting_url" =>$this->REPORTING_SERVER."/dashboard/hmis_opd_reattendance",
							"computation" =>[
										"procedure" =>  "generate_hmis_opd_reattendance_report_for_dashboard",
										"datatable" => "dashboard_reporting_hmis_opd_reattendances",
									],
						],
						
						
										
						[
							"report_name" => "IPD ADMISSION",
							"report_code" => "IPD_ADMISSION",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/admission",
							"computation" =>[
										"procedure" =>  "generate_admission_report_for_dashboard",
										"datatable" => "dashboard_reporting_admissions",
									],
						],
										
						[
							"report_name" => "OUTGOING REFERRAL",
							"report_code" => "OUTGOING_REFERRAL",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/outgoing_referral",
							"computation" =>[
										"procedure" =>  "generate_outgoing_referral_report_for_dashboard",
										"datatable" => "dashboard_reporting_outgoing_referral",
									],
						],
										
						[
							"report_name" => "INCOMING REFERRAL",
							"report_code" => "INCOMING_REFERRAL",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/incoming_referral",
							"computation" =>[
										"procedure" =>  "generate_incoming_referral_report_for_dashboard",
										"datatable" => "dashboard_reporting_incoming_referral",
									],
						],
										
						[
							"report_name" => "DTC",
							"report_code" => "DTC",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/dtc",
							"computation" =>[
										"procedure" =>  "generate_dtc_report_for_dashboard",
										"datatable" => "dashboard_reporting_dtc",
									],
						],
										
						[
							"report_name" => "FINANCIAL TRANSACTION",
							"report_code" => "FINANCIAL_TRANSACTION",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/financial_transaction",
							"computation" =>[
										"procedure" =>  "generate_financial_report_for_dashboard",
										"datatable" => "dashboard_reporting_fiancial_transaction",
									],
						],
										
						[
							"report_name" => "EXEMPTION",
							"report_code" => "EXEMPTION",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/exemption",
							"computation" =>[
										"procedure" =>  "generate_exemption_report_for_dashboard",
										"datatable" => "dashboard_reporting_exemption",
									],
						],
										
						[
							"report_name" => "LABORATORY",
							"report_code" => "LABORATORY",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/laboratory",
							"computation" =>[
										"procedure" =>  "generate_laboratory_report_for_dashboard",
										"datatable" => "dashboard_reporting_laboratory",
									],
						],
										
						[
							"report_name" => "RADIOLOGY",
							"report_code" => "RADIOLOGY",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/radiology",
							"computation" =>[
										"procedure" =>  "generate_radiology_report_for_dashboard",
										"datatable" => "dashboard_reporting_radiology",
									],
						],
										
						[
							"report_name" => "PHARMACY",
							"report_code" => "PHARMACY",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/pharmacy",
							"computation" =>[
										"procedure" =>  "generate_pharmacy_report_for_dashboard",
										"datatable" => "dashboard_reporting_pharmacy",
									],
						],
										
						[
							"report_name" => "BED OCCUPANCY",
							"report_code" => "BED_OCCUPANCY",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/bed_occupancy",
							"computation" =>[
										"procedure" =>  "generate_bed_occupancy_report_for_dashboard",
										"datatable" => "dashboard_reporting_bed_occupancy",
									],
						],
						
						[
							"report_name" => "Gender Based Violence and Violence Against Children",
							"report_code" => "GBV_VAC",
							"posting_url" => $this->REPORTING_SERVER."/dashboard/gbv_vac",
							"computation" =>[
										"procedure" =>  "generate_gbv_vac_report_for_dashboard",
										"datatable" => "dashboard_reporting_gbv_vac",
									],
						],
						/*				
						[
							"report_name" => "Report and Requisition",
							"report_code" => "RnR",
							"posting_url" => $this->REPORTING_SERVER."/rnr/post_rnr",
							"computation" =>[
										"procedure" =>  "generate_rnr_for_elmis",
										"datatable" => "rnr_for_elmis",
									],
						],
										
						[
							"report_name" => "Report and Requisition Adjustiments",
							"report_code" => "RnR_ADJUSTIMENT",
							"posting_url" => $this->REPORTING_SERVER."/rnr/post_rnr_adjustiment,
							"computation" =>[
										"procedure" =>  "generate_rnr_adjustiments_for_elmis",
										"datatable" => "rnr_adjustiments_for_elmis",
									],
						],*/
					];
		
		$this->dashboard_posting_success = [];
		$this->last_reporting_date_errors = [];
		$this->payload_computation_errors = [];
		$this->dashboard_posting_errors = [];
	}

	
	/**

     * Dynamically Compute and Send all the specified reports to the gothomis central server by calling the 
	 * route on the server that will execute the corresponding create()

     *

     * @return array

     */

    public function computeAndSend(){
		//compute the data to send
		$counter = 0;
		$total = count($this->reports);
		file_put_contents(Config::get('updater.root_path').'public/counter.txt', "0&percnt;");
		foreach($this->reports as $report)
		{
			file_put_contents(Config::get('updater.root_path').'public/updated_files.txt', "^Uploading ".$report['report_code']."...");
			
			//find from the server the last reporting date
			$payload = [
						"facility_code"=>$this->facility_code, 
						"report_code"=>$report['report_code'],
					];
			
			$ch = curl_init($this->LAST_REPORTING_DATE_URL);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, JSON_encode($payload));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
			$data = curl_exec($ch);
			
			if (curl_errno($ch)){
				array_push($this->last_reporting_date_errors,["success"=>0,"message"=>"Error requesting last reporting date for: ".$report['report_name'],"error"=>curl_error($ch)]);
				curl_close($ch);
				continue;
			}else{
				curl_close($ch);
				
				if(strtolower($data) == 'facility_code not exists')
					return $this->facility_code."Sorry, your facility code seems wrong. It does not exist on the server. Please, check on  to confirm its correctness";
				
				//compute and fetch payload
				$last_reporting_date = $data;
				if(((new \Datetime($last_reporting_date))->diff(new \Datetime)->days) > 3){
					$last_reporting_date = date('Y-m-d',strtotime("-3 days"));
				}
				try{
					DB::statement("SET @message = '".$this->facility_id."'");
					DB::statement("SET @last_reporting_date = '$last_reporting_date'");
					DB::statement("CALL ". $report['computation']['procedure']."(@message,@last_reporting_date)");
					$response = DB::select("SELECT @message");
					if($response[0]->{'@message'} !== 0)
						$payload = DB::select("select * from ".$report['computation']['datatable']);
					else
						array_push($this->payload_computation_errors,["success"=>0,"message"=>"Error computing payload for: ".$report['report_name'],"error"=>""]);
				}catch(Exception $ex){
					array_push($this->payload_computation_errors,["success"=>0,"message"=>"Error computing payload for: ".$report['report_name'],"error"=>$ex->getMessage()]);
				}//end compute and fetch payload
			}
			
			$payload = [
							"report_code"=>$report['report_code'],
							"facility_code"=>$this->facility_code, 
							"reporting_date"=>Date('Y-m-d'), 
							"payload"=>JSON_encode($payload)
						];
			//send the payload
			$ch = curl_init($report['posting_url']);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, JSON_encode($payload));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
			$data = curl_exec($ch);
			
			if (curl_errno($ch)){
				array_push($this->dashboard_posting_errors, ["success"=>0,"message"=>"Error posting report for: ".$report['report_name'],"error"=>curl_error($ch),"url"=>$report['posting_url']]);
				curl_close($ch);
			}elseif(!is_object(JSON_decode($data)))
				array_push($this->dashboard_posting_errors, ["success"=>0,"message"=>"Error posting report for: ".$report['report_name'],"error"=>$data,"url"=>$report['posting_url']]);
			else
				curl_close($ch);
			//end sending payload
			
			if(is_object(JSON_decode($data)) && JSON_decode($data)->success === 0)
				array_push($this->dashboard_posting_errors, JSON_decode($data));
			elseif(is_object(JSON_decode($data)))
				array_push($this->dashboard_posting_success, JSON_decode($data));
			else
				array_push($this->dashboard_posting_success, JSON_decode($data));
		
			file_put_contents(Config::get('updater.root_path').'public/counter.txt', (int)((++$counter)*100/$total)."&percnt;");
		}
		
		try{
			//report the cost sharing schemes defined on the client for analysis
			$records = DB::select("select t1.id as main_category_id,t2.id as sub_category_id,t1.category_description as main_category_description, t2.sub_category_name as sub_category_description, '$this->facility_code' as facility_code from tbl_payments_categories t1 join tbl_pay_cat_sub_categories t2 on t1.id = t2.pay_cat_id where t2.facility_id = $this->facility_id");
			
			$payload = [
							"facility_code"=>$this->facility_code,
							"payload" => JSON_encode($records),
						];
			$ch = curl_init($this->REPORTING_SERVER."/utility/cost_sharing_schemes");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, JSON_encode($payload));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
			$data = curl_exec($ch);
			
			//report the departments defined on the client for analysis
			$records = DB::select("select t1.id as department_id,t2.id as sub_department_id,t1.department_name, t2.sub_department_name, '$this->facility_code' as facility_code from tbl_departments t1 left join tbl_sub_departments t2 on t1.id = t2.department_id");
			
			$payload = [
							"facility_code"=>$this->facility_code,
							"payload" => JSON_encode($records),
						];
			$ch = curl_init($this->REPORTING_SERVER."/utility/facility_departments");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, JSON_encode($payload));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
			$data = curl_exec($ch);
			
			//report the item coding defined on the client for analysis (post id 882 user defined)
			$records = DB::select("select t1.id as item_id,t2.id as mapping_id,t1.item_name, t2.item_code, '$this->facility_code' as facility_code from tbl_items t1 left join tbl_item_type_mappeds t2 on t1.id = t2.item_id where t1.id > 882");
			
			$payload = [
							"facility_code"=>$this->facility_code,
							"payload" => JSON_encode($records),
						];
			$ch = curl_init($this->REPORTING_SERVER."/utility/facility_item_coding");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, JSON_encode($payload));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
			$data = curl_exec($ch);
		}catch(Exception $ex){}
		
		$summary = [
					"last_reporting_date_errors"=>$this->last_reporting_date_errors,
					"payload_computation_errors"=>$this->payload_computation_errors,
					"dashboard_posting_errors"=>$this->dashboard_posting_errors,
					"dashboard_posting_success"=>$this->dashboard_posting_success
				];
		file_put_contents("aggregation.log", Date("Y-m-d H:i:s").PHP_EOL.print_r($summary,true).PHP_EOL);
		
		//report all errors/success
		return response()->json($summary);
    }


	public function submit(Request $request)
	{
		try{
			$nrnID=$request->rnr_id;
			$facility_id=DB::select("select facility_code from tbl_facilities where id='".$request->facility_id."'");
			$facility_code=$facility_id[0]->facility_code;
			
			DB::statement("UPDATE rnr_adjustiments_for_elmis set sourceOrderId='".$nrnID."'");
			$RnRdataloaded=DB::select("SELECT  (CASE WHEN emergency=0 THEN 'false' ELSE 'true' END) as emergency,`programCode` as program_code ,`item_code` as concept_code,`facilityCode` as facility_code ,`quantityDispensed`,`quantityReceived`,`beginningBalance`,`stockInHand`,`stockOutDays`,`quantityRequested`,`reasonForRequestedQuantity`,`order_number` as sourceOrderId,rnr_month FROM `tbl_rnr_orders`   where facilityCode='".$facility_code."' AND order_number='".$nrnID."'  ");

			foreach ($RnRdataloaded as $code){
				DB::statement("UPDATE rnr_adjustiments_for_elmis SET program_Code= '".$code->program_code."' WHERE  concept_Code= '".$code->concept_code."'");
			}
			
			$RnRAdjustdataloaded=DB::select("SELECT * FROM `rnr_adjustiments_for_elmis`   WHERE  concept_Code IN (SELECT `item_code` as concept_Code FROM `tbl_rnr_orders`   where facilityCode='".$facility_code."' AND order_number='".$nrnID."')  ");

			$ch = curl_init($this->REPORTING_SERVER."/rnr/post_rnr");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, JSON_encode($RnRdataloaded));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
			$rnrResponse = curl_exec($ch);
			$ch = curl_init($this->REPORTING_SERVER."/rnr/post_rnr_adjustment");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, JSON_encode($RnRAdjustdataloaded));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
			$rnrAdjustResponse = curl_exec($ch);

			$ch = curl_init($this->REPORTING_SERVER."/rnr/send");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, JSON_encode($RnRAdjustdataloaded));
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
			$rnrAdjustResponse11 = curl_exec($ch);
			file_put_contents('uploads/rnrLog.text',json_encode($RnRdataloaded,JSON_PRETTY_PRINT));
			file_put_contents('uploads/rnrAdjLog.text',json_encode($RnRAdjustdataloaded,JSON_PRETTY_PRINT));
		   /* if($rnrAdjustResponse11->status==200){
				Tbl_rnr_order_control::where('order_number',$nrnID)->update(['order_status'=>$rnrAdjustResponse11->message]);
				Tbl_rnr_order::where('order_number',$nrnID)->update(['order_status'=>$rnrAdjustResponse11->message]);
			}*/

			return $rnrAdjustResponse11;

		}
		catch (Exception $ex){

		}
	}

	public function RnRstatus(Request $request)
	{

		try{

			$dataloaded=$request->all();
			 $ch = curl_init($this->REPORTING_SERVER."/rnr/status");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, JSON_encode($dataloaded));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
			return  $data = curl_exec($ch);

		}
		catch (Exception $ex) {

		}
	}
}