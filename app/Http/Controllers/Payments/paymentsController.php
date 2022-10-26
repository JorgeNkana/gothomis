<?php

namespace App\Http\Controllers\Payments;

use App\classes\patientRegistration;
use App\classes\CHF_Settings;
use App\laboratory\Tbl_order;
use App\laboratory\Tbl_request;
use App\Patient\Tbl_encounter_invoice;
use App\Payments\Tbl_invoice_line;
use App\Payments\Tbl_xray;
use App\Pharmacy\Tbl_prescription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Item_setups\Tbl_item_price;
use App\classes\SystemTracking;
use App\Trackable;
use App\Pharmacy\Tbl_dispenser;
class paymentsController extends Controller
{
    public function getBills($facility_id)
    {
        return DB::select("SELECT * FROM bills WHERE facility_id = '$facility_id' group by receipt_number order by receipt_number desc limit 20");
    }

    public function getAllPatientBills(Request $request)
    {
        return DB::select("select * from bills where facility_id ='".$request->facility_id."' and name like '%".$request->search."%' group by receipt_number");
    }
	
    public function getPatientBill(Request $request)
    {
        return DB::select("SELECT * FROM bills WHERE ".(isset($request->patient_id) ? "patient_id='".$request->patient_id."'" : "corpse_id='".$request->corpse_id ."'"). " AND receipt_number = '".$request->receipt_number."'");
    }



    public function updateGepgUser(Request $request)
    {
        $user_id = $request['user_id'];
        $bill_id = $request['bill'];
        $update = Tbl_invoice_line::where('invoice_id',$bill_id)
            ->update([
                'user_id' => $user_id,
            ]);
        return $update;
    }

    public function updateBills(Request $request)
    {
		$bill= $request['bill'];
        $user_id= $request['user_id'];
		 foreach ($bill as $d){
			$oldData=Tbl_invoice_line::where('id',$d['id'])->get();

            $update = Tbl_invoice_line::where('id', $d['id'])
                ->update([
                    'status_id' => 2,
                    'user_id' => $user_id,
                    'payment_method_id' => 1,
                ]);
			$newData=Tbl_invoice_line::where('id',$d['id'])->get();
			$patient_id=$d['patient_id'];
			$trackable_id=$d['id'];
			SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,$oldData);

        }	
        return $update;
    }
	
	

    public function patientsToPoS(Request $request)
    {
        $search = $request->input('search');
        $id = $request->input('facility_id');
        $limit = 10;
        $sql = "select *,concat(first_name,' ',ifnull(middle_name,''), ' ',ifnull(last_name,''), '#',
        medical_record_number) as name from patients_to_pos where search_field like '%".$search."%' AND facility_id = '".$id."' GROUP BY patient_id LIMIT ".$limit;
        $patients_returned = DB::select(DB::raw($sql));
        return $patients_returned;
    }

    public function itemsToPoS(Request $request)
    {
        $search = $request->input('search');
        $facility_id = $request->input('facility_id');
        $main_category_id = $request->input('main_category_id');
        $patient_category_id = ($main_category_id != 3 ? $request->input('patient_category_id') : 1);
        $sub_category_name = $request->input('sub_category_name');
        $limit = 10;
        $sql = "select vw_shop_items.*
				from vw_shop_items where patient_category_id ='$patient_category_id' and facility_id='$facility_id' and item_name like '%$search%' order by length(item_name) asc limit $limit";
        $patients_returned = DB::select(DB::raw($sql));
        return $patients_returned;
    }
	
	public function getSelectedItemDetails(Request $request){
		$facility_id = $request->input('facility_id');
        $patient_category_id = $request->input('patient_category_id');
        $item_id = $request->input('item_id');
        $sql = "select t1.id AS item_id,
                    t2.id AS item_type_id,
                    t1.item_name,
                    t1.dept_id,
                    t2.item_category,
                    t2.dose_formulation,
                    t2.strength,
                    t2.dispensing_unit,
                    t2.sub_item_category,
                    t3.exemption_status,
                    t3.onetime,
                    t3.insurance,
                    t3.id AS price_id,
                    t3.price,
                    t3.facility_id ,
                    t4.id AS patient_category_id,
                    t4.pay_cat_id AS patient_main_category_id,
                    t4.sub_category_name AS patient_category
                    FROM   tbl_item_prices t3 
					join tbl_item_type_mappeds t2  on t2.item_id = '$item_id' and t3.facility_id='$facility_id' and t2.item_id = t3.item_id
                    join tbl_pay_cat_sub_categories t4 on t4.id = '$patient_category_id' and t4.id = t3.sub_category_id
					join tbl_items t1 on t1.id = t2.item_id GROUP BY item_id";
        $result = DB::select($sql);
		return (count($result)>0 ? $result : [null]);
	}
	
	 public function checkBilledItem(Request $request){
        $item_id = $request['item_id'];
        $account_id = $request['account_id'];
        $item_name = $request['item_name'];
        $sql = "SELECT t1.*  
			FROM tbl_invoice_lines t1 INNER JOIN tbl_encounter_invoices t2 ON t2.account_number_id = '".$account_id."' and t1.invoice_id=t2.id and t1.status_id = 1  and t1.is_payable
			INNER JOIN tbl_reattendance_free_days t3 ON t3.facility_id = t2.facility_id and timestampdiff(day, t1.created_at, current_date) <= t3.days
			INNER JOIN tbl_item_prices t4 ON t4.id = t1.item_price_id AND t4.item_id=$item_id ";
       $data= count(DB::select(DB::raw($sql)));
        if($data>0){
            return response()->json([
              'msg'=>'<b style="color: red"> '.$item_name. '</b>  already billed.. Clear this bill in Bills Payment Module before billing it again',
                'status'=>0
            ]);
        }

    }

     
	public function saveFromPoS(Request $request)
    {
        $date=date('Y-m-d');
        $dtz = json_encode($request->all());
        $data = json_decode($dtz);
        $user_id =  $data[0]->user_id;
        $dept_id =  $data[0]->dept_id;
        $patient_id = $data[0]->patient_id;
        $quantity = $data[0]->quantity;
        $item_type_id = $data[0]->item_type_id;
        $account_number_id =  $data[0]->account_number_id;
        $facility_id =  $data[0]->facility_id;
        $data = Tbl_encounter_invoice::create(["account_number_id"=>$account_number_id, "user_id"=>$user_id,"facility_id"=>$facility_id,]);
        $receiptNumber = $data->id;

		$data = $request->all();
		$invoice_lines = [];
		foreach ($data as $d){
			$postData = Tbl_invoice_line::create([
				'invoice_id'=>$receiptNumber,
				'item_type_id'=>$d['item_type_id'],
				'quantity'=>number_format($d['quantity'], 2, '.', ''),
				'item_price_id'=>$d['item_price_id'],
				'user_id'=>$d['user_id'],
				'patient_id'=>$d['patient_id'],
				'status_id'=>$d['status_id'],
				'facility_id'=>$d['facility_id'],
				'discount'=>number_format($d['discount'], 2, '.', ''),
				'discount_by'=>$d['discount_by'],
				'payment_filter'=>$d['payment_filter'],
				'payment_method_id'=>$d['payment_method_id'],
			]);
			$user_id=$d['user_id'];
			$newData=$postData;
			$patient_id=$d['patient_id'];
			$trackable_id=$newData->id;
			SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);
			array_push($invoice_lines, ["id"=>$postData["id"], "status_id"=>$d['status_id']]);
		}
		
		foreach ($data as $d){
			if($d['dept_id'] == 4 ){
				if ($d['hospital_shop_posting']==true){
					$presc = Tbl_prescription::create([
						'item_id'=>$d['item_id'],
						'quantity'=>number_format($d['quantity'], 2, '.', ''),
						'patient_id'=>$d['patient_id'],
						'prescriber_id'=>$d['user_id'],
						'dispenser_id'=>$d['user_id'],
						'verifier_id'=>$d['user_id'],
						'dispensing_status'=>1,
						'visit_id'=>$account_number_id,
						'start_date'=>$date,

					]);


					//...............balancing stock now
					 $item_id=$d['item_id'];
					$quantity_dispensed=$d['quantity'];

					$patient_id=$request['patient_id'];
					$request_amount=$d['quantity'];
					$received_from_id=$d['received_from_id'];
					$user_id=$d['user_id'];
					$dispensing_status_id=1;
					$store_id=$d['store_id'];
					$batch_no=$d['batch_no'];
					$order_id=$d['order_id'];
					$identifier=$d['order_id'];
					$quantity_received=($d['balance_available']-$quantity_dispensed);

					if($quantity_received<0){
						$amount_available = Tbl_dispenser::where('item_id', $item_id)
						->where('dispenser_id', $store_id)
						->where('quantity_received','>=', $quantity_dispensed)
						->where('control', 'l')
							->get();
							$quantity_received=($amount_available[0]->quantity_received-$quantity_dispensed);
							$batch_no=$amount_available[0]->batch_no;
							$order_id=$amount_available[0]->id;
						
					}
					$update = Tbl_dispenser::where('item_id', $item_id)
					->where('dispenser_id', $store_id)
					->where('batch_no', $batch_no)
					->where('control', 'l')
						->update(['control' => 'c']);
					$data = Tbl_dispenser::create([
						'item_id' => $item_id,
						'quantity_dispensed' => number_format($quantity_dispensed, 2, '.', ''),
						'patient_id' => $patient_id,
						'request_amount' => number_format($request_amount, 2, '.', ''),
						'received_from_id' => $received_from_id,
						'user_id' => $user_id,
						'dispenser_id' => $store_id,
						'batch_no' => $batch_no,
						'quantity_received' =>  number_format($quantity_received, 2, '.', ''),
						'dispensing_status_id' => $dispensing_status_id,
						'control' => 'l',
					]);
					//...............balancing stock now
				}
				else{
					$presc = Tbl_prescription::create([
						'item_id'=>$d['item_id'],
						'quantity'=>number_format($d['quantity'], 2, '.', ''),
						'patient_id'=>$d['patient_id'],
						'prescriber_id'=>$d['user_id'],
						'dispenser_id'=>$d['user_id'],
						'verifier_id'=>$d['user_id'],
						'dispensing_status'=>2,
						'visit_id'=>$account_number_id,
						'start_date'=>$date,
					]);
				}

				$user_id=$d['user_id'];
				$newData=$presc;
				$patient_id=$d['patient_id'];
				$trackable_id=$newData->id;
				SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);
			}
		}


        if($d['dept_id'] == 2 || $d['dept_id'] == 3){

            $priority = 'Routine';

            $clinical_note = 'No clinical summary written for this investigation order';

            $requesting_department_id = 1;

            $investgation = Tbl_request::create(["requesting_department_id"=>$requesting_department_id,"doctor_id"=>$user_id,"patient_id"=>$patient_id,"visit_date_id"=>$account_number_id,"eraser"=>1]);

            $id = $investgation->id;
        }
        foreach ($data as $d){
            if($d['dept_id'] == 2 || $d['dept_id'] == 3 ){
                $postData = Tbl_order::create(['priority'=>$priority,'clinical_note'=>$clinical_note,'test_id'=>$d['item_id'],'order_id'=>$id,"visit_date_id"=>$account_number_id,"eraser"=>1]);
            }
        }

		//a work around to force triggers that work on update since the Sale rows enter whole
		//as opposed to processing normal bills
		foreach ($invoice_lines as $entry){
			Tbl_invoice_line::where("id", $entry["id"])->update(["status_id"=>$entry["status_id"]]);
		}
		
        return  $receiptNumber;
    }
	
     public function getDetailedReports(Request $request)
    {   $data=[];
        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql1 = "SELECT SUM(quantity*price-discount) AS sub_total,SUM(quantity) AS total_items,SUM(discount) AS total_discount,created_at,receipt_number,gepg_receipt,first_name,middle_name,last_name,medical_record_number,user_name 
       FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND status_id = 2 AND payment_method_id = 1   AND payment_filter !=10 GROUP BY receipt_number";
        $sql2 = "SELECT SUM(quantity*price-discount) AS sub_total,SUM(quantity) AS total_items,SUM(discount) AS total_discount,created_at,receipt_number,gepg_receipt,first_name,middle_name,last_name,medical_record_number,user_name  
       FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND status_id = 2 AND payment_method_id = 2 AND payment_filter !=10 GROUP BY receipt_number";
        $data[] = DB::select(DB::raw($sql1));
        $data[] = DB::select(DB::raw($sql2));
        return $data;

    }
    public function detailedData(Request $request)
    {  $data=[];
        $receipt_number = $request->input('receipt_number');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql1 = "SELECT * FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND payment_method_id = 1  AND payment_filter !=10  AND receipt_number = '".$receipt_number."' ";
        $sql2 = "SELECT * FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND payment_method_id = 2  AND payment_filter !=10  AND  receipt_number = '".$receipt_number."' ";
        $data[] = DB::select(DB::raw($sql1));
        $data[] = DB::select(DB::raw($sql2));
        return $data;
    }

    public function getReceiptData(Request $request)
    {
        $receipt_number = $request->input('receipt_number');
        $payment_method_id = $request->input('payment_method_id');
        $sql1 = "SELECT * FROM `vw_detailed_reports` WHERE receipt_number = '".$receipt_number."' AND payment_method_id = '".$payment_method_id."' ";
        $data = DB::select(DB::raw($sql1));
        return $data;

    }
    public function getDepartmentalReports(Request $request)
    {   $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
       $sql = "SELECT SUM(`resultant_pay`) AS total,`department_name` AS department,COUNT(id) AS transactions FROM `vw_departmental_summary` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND `facility_id`='".$facility_id."' GROUP BY `department_name`";
        $dptReport = DB::select(DB::raw($sql));
        return $dptReport;
    }
    public function getSubDepartmentalReports(Request $request)
    {   $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
       $sql = "SELECT SUM(`resultant_pay`) AS total,`sub_department_name`,COUNT(id) AS transactions FROM `vw_sub_department_summary` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND `facility_id`='".$facility_id."' GROUP BY `sub_department_name`";
        $dptReport = DB::select(DB::raw($sql));
        return $dptReport;
    }

    public function getCashierReports(Request $request)
    {
        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $dptReport =[];
        $sql = "SELECT SUM(quantity*price-discount) AS sub_total,COUNT(receipt_number) AS transactions,user_name 
        FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND `facility_id`='".$facility_id."'
        AND status_id = 2 AND payment_method_id = 1 GROUP BY user_name";
        $sql1 = "SELECT SUM(quantity*price-discount) AS sub_total,COUNT(receipt_number) AS transactions,user_name 
        FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND `facility_id`='".$facility_id."'
        AND status_id = 2 AND payment_method_id = 2 GROUP BY user_name";
        $dptReport[] = DB::select(DB::raw($sql));
        $dptReport[] = DB::select(DB::raw($sql1));
        return $dptReport;
    }

    public function getCashierTransactions(Request $request)
    {   $cs = [];
        $facility_id = $request->input('facility_id');
        $user_id = $request->input('user_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT SUM(quantity*price-discount) AS sub_total,COUNT(invoice_id) AS transactions,user_name FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND payment_method_id = 1  AND `facility_id`='".$facility_id."' AND user_id = '".$user_id."' AND status_id = 2 GROUP BY user_name";
        $sql1 = "SELECT SUM(quantity*price-discount) AS sub_total,COUNT(invoice_id) AS transactions,user_name FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND payment_method_id = 2 AND `facility_id`='".$facility_id."' AND user_id = '".$user_id."' AND status_id = 2 GROUP BY user_name";
        $sql2 = "SELECT quantity*price-discount AS sub_total,quantity AS total_items,discount AS total_discount,created_at,receipt_number,gepg_receipt,first_name,middle_name,last_name,user_name,medical_record_number,item_name 
                FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND status_id = 2 AND payment_method_id = 1 AND user_id ='".$user_id."'";
        $sql3 = "SELECT quantity*price-discount AS sub_total,quantity AS total_items,discount AS total_discount,created_at,receipt_number,gepg_receipt,first_name,middle_name,last_name,user_name,medical_record_number,item_name 
                FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND status_id = 2 AND payment_method_id = 2 AND user_id ='".$user_id."'";
        $cs[] = DB::select(DB::raw($sql));
        $cs[] = DB::select(DB::raw($sql1));
        $cs[] = DB::select(DB::raw($sql2));
        $cs[] = DB::select(DB::raw($sql3));
        return $cs;
    }
	public function discountReport(Request $request)
    {
        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT t1.discount,t1.created_at,t1.updated_at,t1.facility_id,t5.name,t5.mobile_number AS staff_phone,t4.item_name,
        t2.first_name,t2.middle_name,t2.last_name,t2.medical_record_number,t2.mobile_number AS patient_phone FROM tbl_invoice_lines t1
        INNER JOIN tbl_patients t2 ON t2.id=t1.patient_id INNER JOIN tbl_item_prices t3 ON t3.id=t1.item_price_id
        INNER JOIN tbl_items t4 ON t4.id=t3.item_id INNER JOIN users t5 ON t5.id = t1.discount_by 
        WHERE t1.updated_at BETWEEN '".$start."'  AND '".$end."' AND t1.facility_id = '".$facility_id."' AND t1.discount>0 ";
        return DB::select(DB::raw($sql));
    }

    public function pendingBills(Request $request)
    {
        $facility_id = $request->facility_id;
        $sql ="SELECT SUM(quantity*price-discount) AS sub_total,SUM(quantity) AS total_items,SUM(discount) AS total_discount,created_at,receipt_number,gepg_receipt,first_name,middle_name,last_name,medical_record_number,user_name,account_number 
       FROM vw_pending_bills WHERE facility_id = '".$facility_id."' AND status_id = 1 AND (timestampdiff(HOUR,created_at,CURRENT_TIMESTAMP)>=48) GROUP BY receipt_number";
        return DB::select(DB::raw($sql));
    }

    public function pendingBillData(Request $request)
    {
        $receipt_number = $request->input('receipt_number');
        $sql1 = "SELECT * FROM `vw_pending_bills` WHERE receipt_number = '".$receipt_number."' AND status_id = 1 ";
        $data = DB::select(DB::raw($sql1));
        return $data;

    }

   public function balanceCheckShop(Request $request)
    {
        $id = $request->input('facility_id');
        $item_id = $request->input('item_id');
        $main_category_id = $request->input('pay_cat_id');
        $sql = "select balance from vw_shop_item_balance where item_id = ".$item_id." AND facility_id = '".$id."'   ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function itemsToShop(Request $request)
    {
		$search = $request->input('search');
		//$patient_category_id=10;//10 is the id for hospital shop category
$patient_category_id=$request->input('patient_category_id');//10 is the id for hospital shop category
        $id = $request->input('facility_id');
        $sql = "select * from vw_shop_items where item_name like '%".$search."%' AND dept_id = 4  AND facility_id = '".$id."' 
		AND patient_category_id='".$patient_category_id."' GROUP BY item_id limit 15";
        $patients_returned = DB::select(DB::raw($sql));
        return $patients_returned;
    }
	
	
    public function Post_partial_payment(Request $request)
    {
      $patient_id=$request['patient_id'] ;
      $user_id=$request['user_id'] ;
      $facility_id=$request['facility_id'] ;
      $invoice_id=$request['invoice_id'] ;
      $amount_billed=$request['amount_billed'] ;
      $amount_paid=$request['amount_paid'] ;
        if(patientRegistration::duplicate('tbl_partial_payments',['invoice_id','patient_id','amount_paid', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$invoice_id,$patient_id,$amount_paid
                ])==true){

                return response()->json([
                    'msg'=>'Duplication Detected....',
                    'status'=>0
                ]);

        }
        $getVisit=Tbl_encounter_invoice::where('id',$invoice_id)->first();
        $visit_date_id=$getVisit->account_number_id;
        Tbl_partial_payment::create([
            'patient_id'=>$patient_id,
            'user_id'=>$user_id,
            'facility_id'=>$facility_id,
            'visit_date_id'=>$visit_date_id,
            'invoice_id'=>$invoice_id,
            'amount_billed'=>$amount_billed,
            'amount_paid'=>$amount_paid,
            'status'=>0,
        ]);
        $sql = "select ifnull(sum(amount_paid),0) as Amount_paid from tbl_partial_payments where invoice_id =$invoice_id and patient_id='".$patient_id."' ";
        $bill= DB::select(DB::raw($sql));
        $Amount_Paid= $bill[0]->Amount_paid;
        if($Amount_Paid ==$amount_billed){
            Tbl_invoice_line::where('invoice_id',$invoice_id)->update([
                'status_id'=>2
            ]);

            Tbl_partial_payment::where('invoice_id',$invoice_id)->update([
                'status'=>1
            ]);
            return response()->json([
                'msg'=>'Thank You.. Your Partial Payments Successful Completed ....',
                'status'=>1
            ]);
        }
        else{
            Tbl_invoice_line::where('invoice_id',$invoice_id)->update([
                'status_id'=>4
            ]);
            return response()->json([
                'msg'=>' Your Partial Payments Successful Done ....',
                'status'=>1
            ]);
        }

    }


    public function GetAmountPaidPartial(Request $request)
    {

     $patient_id=$request['patient_id'];
     $invoice_id=$request['invoice_id'];
        $sql = "select ifnull(sum(amount_paid),0) as Amount_paid from tbl_partial_payments where invoice_id =$invoice_id and patient_id=$patient_id  AND  status=0";
        $bill= DB::select(DB::raw($sql));
        return $bill;

}

    public function GetPartial_list_summary(Request $request)
    {

        $facility_id = $request->input('facility_id');
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $sql = "select invoice_id, ifnull(sum(amount_paid),0) as Amount_paid,amount_billed from tbl_partial_payments WHERE updated_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."'  group by invoice_id";
        $bill= DB::select(DB::raw($sql));
        return $bill;

}
public function categoriesReport(Request $request)
    {
        $facility_id = $request['facility_id'];
        $start = $request['start'];
        $end = $request['end'];
        $category = $request['category'];
        $cat=[1,13,24];
        if(in_array($category,$cat)){
            return response()->json([
               'msg'=>'This is paid category check its report in respective report tab above',
                'status'=>0
            ]);
        }elseif (empty($start) ||empty($end)){
            return response()->json([
                'msg'=>'You must choose date range when searching',
                'status'=>0
            ]);
        }
        else{
            $sql = " SELECT * FROM vw_patients_categories WHERE status_id = 1 AND payment_filter = '".$category."' AND
             facility_id='".$facility_id."' AND updated_at BETWEEN '".$start."'  AND '".$end."' ";
            return DB::select(DB::raw($sql));
        }
    }
 
 public function chfCheckBills(Request $request)
    {
        $patient_id = $request['patient_id'];
        $account_id = $request['account_id'];
        $sql = "SELECT ifnull(SUM(quantity*price),0) AS sub_total FROM tbl_invoice_lines join tbl_encounter_invoices on account_number_id = $account_id AND tbl_invoice_lines.invoice_id = tbl_encounter_invoices.id AND tbl_invoice_lines.status_id=1";
		
        $sql2 = "SELECT tbl_item_type_mappeds.id as item_type_id,  tbl_item_type_mappeds.item_id, tbl_items.item_name, tbl_item_prices.id as item_price_id, price FROM tbl_items join tbl_item_prices on tbl_items.item_name= 'CHF Topup' and tbl_items.id = tbl_item_prices.item_id join tbl_item_type_mappeds on tbl_item_type_mappeds.item_id = tbl_items.id";
		
        $sql3 = "SELECT ifnull(SUM(quantity*price),0) AS sub_total FROM tbl_invoice_lines join tbl_encounter_invoices on account_number_id = $account_id AND tbl_invoice_lines.invoice_id = tbl_encounter_invoices.id AND tbl_invoice_lines.status_id=2";
		
        $sum1 = DB::select(DB::raw($sql));
        $sum2 = DB::select(DB::raw($sql3));
        $bill[] = $sum1[0]->sub_total - $sum2[0]->sub_total;
        $bill[] = DB::select(DB::raw($sql2));
        $bill[]=CHF_Settings::GetCHFceilling();
        return $bill;
    }
	
	
 public function paidInsuranceReports(Request $request)
    {
        $facility_id = $request['facility_id'];
        $start = $request['start'];
        $end = $request['end'];
        $category = $request['category'];
        if (empty($start) ||empty($end)){
            return response()->json([
                'msg'=>'You must choose date range when searching',
                'status'=>0
            ]);
        }
        else{
            $sql = "SELECT * FROM vw_patients_categories WHERE status_id = 2 AND payment_filter =$category AND
             facility_id=$facility_id AND updated_at BETWEEN '".$start."'  AND '".$end."' ";
            $data =  DB::select(DB::raw($sql));
            return $data;
        }
    }

public function getHospitalShopCashierReports(Request $request)
    {
        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $category = $request['category'];
        $sql = "SELECT ifnull(SUM(quantity*price),0) AS sub_total,COUNT(invoice_id) AS transactions,user_name FROM vw_detailed_reports WHERE status_id = 2 AND payment_filter =$category AND
             facility_id=$facility_id AND updated_at BETWEEN '".$start."'  AND '".$end."' group by user_name ";
        $data =  DB::select(DB::raw($sql));
        return $data;
    }
 public function detailedDataHospitalShop(Request $request)
    {  $data=[];
        $receipt_number = $request->input('receipt_number');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql1 = "SELECT * FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND payment_method_id = 1  AND payment_filter =10  AND receipt_number = '".$receipt_number."' ";
        $sql2 = "SELECT * FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND payment_method_id = 2  AND payment_filter =10  AND  receipt_number = '".$receipt_number."' ";
        $data[] = DB::select(DB::raw($sql1));
        $data[] = DB::select(DB::raw($sql2));
        return $data;
    }

    public function getDetailedReportsHospitalShop(Request $request)
    {   $data=[];
        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql1 = "SELECT SUM(quantity*price-discount) AS sub_total,SUM(quantity) AS total_items,SUM(discount) AS total_discount,created_at,receipt_number,gepg_receipt,first_name,middle_name,last_name,medical_record_number,user_name 
       FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND status_id = 2 AND payment_method_id = 1   AND payment_filter =10 GROUP BY receipt_number";
        $sql2 = "SELECT SUM(quantity*price-discount) AS sub_total,SUM(quantity) AS total_items,SUM(discount) AS total_discount,created_at,receipt_number,gepg_receipt,first_name,middle_name,last_name,medical_record_number,user_name  
       FROM `vw_detailed_reports` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND status_id = 2 AND payment_method_id = 2 AND payment_filter =10 GROUP BY receipt_number";
        $data[] = DB::select(DB::raw($sql1));
        $data[] = DB::select(DB::raw($sql2));
        return $data;

    }

 public function cancelsReport(Request $request)
    {
        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $sql = "SELECT (t1.price* t1.quantity) as amount_total,quantity,price,t1.created_at,t1.updated_at,t1.facility_id,t5.name,t5.mobile_number AS staff_phone,t1.item_name,
        t1.first_name,t1.middle_name,t1.last_name,t1.medical_record_number,t1.mobile_number AS patient_phone,prof_name as possition FROM tbl_invoice_lines t1
        INNER JOIN users t5 ON t5.id = t1.user_id 
        INNER JOIN tbl_proffesionals t6 ON t6.id = t5.proffesionals_id 
        WHERE t1.updated_at BETWEEN '".$start."'  AND '".$end."' AND t1.facility_id = '".$facility_id."' AND t1.status_id >2 ";
        return DB::select(DB::raw($sql));
    }

 public function getDetailedReportsdepartmentally(Request $request)
    {   $data=[];
        $dept_id = $request->input('dept_id');
        $facility_id = $request->input('facility_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $cash = "SELECT SUM(quantity*price-discount) AS sub_total,SUM(quantity) AS total_items,SUM(discount) AS total_discount,created_at,first_name,middle_name,last_name,medical_record_number,item_name,price,quantity
       FROM `tbl_invoice_lines` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND status_id = 2 AND dept_id='".$dept_id."' GROUP BY id";
       $insurance = "SELECT SUM(quantity*price-discount) AS sub_total,SUM(quantity) AS total_items,SUM(discount) AS total_discount,created_at,first_name,middle_name,last_name,medical_record_number,item_name,price,quantity
       FROM `tbl_invoice_lines` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND status_id = 1 AND dept_id='".$dept_id."' AND main_category_id=2 GROUP BY id";
      $exemption= "SELECT SUM(quantity*price-discount) AS sub_total,SUM(quantity) AS total_items,SUM(discount) AS total_discount,created_at,first_name,middle_name,last_name,medical_record_number,item_name,price,quantity
       FROM `tbl_invoice_lines` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND facility_id = '".$facility_id."' AND status_id = 1 AND dept_id='".$dept_id."' AND main_category_id=3 GROUP BY id";
         $data[] = DB::select(DB::raw($cash));
        $data[] = DB::select(DB::raw($insurance));
        $data[] = DB::select(DB::raw($exemption));
        return $data;

    }


    public function ipdInvoices(Request $request){
  
   $tt=Tbl_encounter_invoice::where("account_number_id",$request->account_id)->get();
         $sql="SELECT t2.*,t1.* FROM tbl_invoice_lines t1 join tbl_payment_statuses t2 on t1.status_id=t2.id
         WHERE 
             t1.facility_id='".$request->facility_id."' 
             AND  invoice_id in(select id from tbl_encounter_invoices where account_number_id=$request->account_id) AND t1.status_id <3 group by t1.id order by t1.id asc" ;

       $data[]= DB::SELECT($sql);


      $data[]= DB::select("SELECT 
       t6.bed_name as bed_number,
        t5.ward_name,
        t4.instructions,
        t2.updated_at as discharged_date,
        t2.created_at as admission_date,
        t2.admission_status_id ,
        t7.status_name as admission_status
        FROM tbl_admissions t2
        INNER JOIN tbl_instructions t4 ON t4.admission_id=t2.id
        INNER JOIN tbl_wards t5 ON t5.id =t4.ward_id
        INNER JOIN tbl_beds t6 ON t6.ward_id=t4.ward_id
        INNER JOIN tbl_admission_statuses t7 ON t7.id=t2.admission_status_id

        WHERE t2.admission_status_id !=1 AND t2.account_id=$request->account_id order by t2.id desc limit 1 ");

       $total="SELECT SUM(quantity * (price)) as total  FROM tbl_invoice_lines t1    WHERE
               invoice_id in (select id from tbl_encounter_invoices where account_number_id=$request->account_id) AND t1.status_id <3"
            ;

       $data[]= DB::SELECT($total);

        $discount="SELECT SUM(discount) as discount  FROM tbl_invoice_lines t1    WHERE
             invoice_id in(select id from tbl_encounter_invoices where account_number_id=$request->account_id) AND t1.status_id <3"
            ;

       $data[]= DB::SELECT($discount);


        $paid="SELECT SUM(quantity * (price-discount)) as paid  FROM tbl_invoice_lines t1    WHERE
               invoice_id in(select id from tbl_encounter_invoices where account_number_id=$request->account_id) AND status_id=2 "
            ;

       $data[]= DB::SELECT($paid);
return $data;

//AND t2.patient_id='".$request->patient_id."'  order by t2.id desc limit 1
    }



    public function getDischargedBillReport(Request $request){
        $nurse_id=$request->nurse_id;
$start=$request->start_date;
$end=$request->end_date;
        return DB::select("SELECT  t2.patient_id,
        t1.medical_record_number,
        t1.mobile_number,
        t1.gender,
        t2.admission_status_id,
        t2.updated_at as discharged_date,
        t4.admission_id,
        t4.ward_id,
        t6.nurse_id,
        t4.bed_id,
        t5.ward_name,
        t4.instructions,
        t4.prescriptions,
CASE WHEN ivl.status_id=1  OR ivl.status_id=2 THEN SUM(quantity * (price)) else 0 END  as total,
CASE WHEN ivl.status_id=1  THEN SUM(quantity * (price)) else 0 END  as unpaid,
CASE WHEN ivl.status_id=1 OR   ivl.status_id=2 THEN  SUM(discount)  else 0 END  as discount,
CASE WHEN ivl.status_id=2 THEN  SUM(quantity * (price-discount)) else 0 END  as paid,

        CASE WHEN TIMESTAMPDIFF(YEAR,t1.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,t1.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,t1.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, t1.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, t1.dob, CURRENT_DATE), ' Days') END END
AS umri,
        t2.admission_date,  
        (SELECT residence_name FROM tbl_residences t1 INNER JOIN tbl_patients t2 ON t2.residence_id=t1.id  GROUP BY t1.residence_id LIMIT 1) AS residence_name,
        
        (SELECT council_name 
        FROM tbl_residences t1 
        INNER JOIN tbl_patients t2 ON t2.residence_id=t1.id 
        INNER JOIN tbl_councils t3 ON t3.id=t1.council_id 
        GROUP BY t1.council_id LIMIT 1) AS council_name,
        
        t9.name,
        CASE 
        WHEN t2.account_id IS NOT NULL THEN (SELECT t12.main_category_id FROM tbl_bills_categories t12 WHERE t12.account_id=t2.account_id GROUP BY t2.account_id
         LIMIT 1) END AS main_category_id,      
        CASE 
        WHEN t2.account_id IS NOT NULL THEN (SELECT t12.bill_id FROM tbl_bills_categories t12 WHERE t12.account_id=t2.account_id GROUP BY t2.account_id
         LIMIT 1) END AS patient_category_id,   
        t9.mobile_number AS doctor_mob,     
        t4.updated_at,      
        t4.created_at,      
        CONCAT(t1.first_name,' ',t1.middle_name,' ',t1.last_name) AS fullname,

        t2.facility_id,
     CASE WHEN t2.created_at= t2.updated_at AND t2.admission_status_id=2 THEN
    timestampdiff(day, t2.created_at, CURRENT_DATE) else  timestampdiff(day, t2.created_at, t2.updated_at)  END as totaldays ,
        t2.account_id
        FROM tbl_admissions t2
        INNER JOIN tbl_instructions t4 ON t4.admission_id=t2.id
        INNER JOIN tbl_wards t5 ON t5.id =t4.ward_id
        INNER JOIN tbl_nurse_wards t6 ON t6.ward_id=t4.ward_id
        INNER JOIN tbl_patients t1 ON t1.id = t2.patient_id
        INNER JOIN users t9 ON t2.user_id=t9.id  
         join tbl_accounts_numbers acn on t2.account_id=acn.id
          join  tbl_encounter_invoices ivn on acn.id=ivn.account_number_id
          join  tbl_invoice_lines ivl on ivn.id=ivl.invoice_id
        WHERE t2.admission_status_id=4 AND t6.nurse_id='".$nurse_id."' and t2.updated_at between '".$start."' AND '".$end."' group by t2.account_id order by timestampdiff(day, t2.created_at, t2.updated_at) desc ");


    }
}