<?php

namespace App\Http\Controllers\Pharmacy;

use App\classes\patientRegistration;
use App\classes\SystemTracking;
use App\ClinicalServices\Tbl_bills_category;
use App\Patient\Tbl_encounter_invoice;
use App\Payment_types\Tbl_pay_cat_sub_category;
use App\Payments\Tbl_invoice_line;
use App\Pharmacy\Tbl_dispensed_groups;
use App\Pharmacy\Tbl_dispenser;
use App\Pharmacy\Tbl_prescription;
use App\Pharmacy\Tbl_receiving_item;
use App\Pharmacy\Tbl_sub_store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Trackable;
class DispensingController extends Controller
{
        //
    public function Dispensing_prescription_vefiry_queue($facility_id)
    {
        $sql = "select DISTINCT patient_id, visit_id, medical_record_number,first_name,last_name,middle_name from patients_with_unverified_prescriptions where facility_id = '".$facility_id."'  limit 25";
        return DB::select($sql);
	//
	}
	
	
	//    searching patient to verify prescription direct from DB
    public function searchPatientToverifyPrescription(Request $request)
    {
        $searchKey=$request['searchKey'];

        $sql = "select DISTINCT patient_id,visit_id, medical_record_number,first_name,last_name,middle_name from patients_with_unverified_prescriptions where search_key like '%".$searchKey."%'";


        return DB::select($sql);
	//
	}

	 public function patient_to_verify($visit_id)
    {
        $sql = "select DISTINCT sub_category_name as patient_category, tbl_prescriptions.id,patient_category_id as bill_id,tbl_prescriptions.visit_id,dose,out_of_stock,frequency,duration,tbl_prescriptions.patient_id,medical_record_number,first_name,last_name,middle_name,dob,gender,item_id,quantity,instruction,item_name,start_date,tbl_accounts_numbers.facility_id
		from tbl_prescriptions inner join tbl_patients on tbl_prescriptions.visit_id = '".$visit_id."' and verifier_id IS NULL and tbl_prescriptions.patient_id = tbl_patients.id  
		inner join tbl_accounts_numbers on tbl_accounts_numbers.id= tbl_prescriptions.visit_id  
		inner join tbl_items on tbl_items.id = tbl_prescriptions.item_id
		group by item_id,patient_id ";


		return DB::select($sql);
	//
	}
	
	/****you should have used the same function defined above by passing
	patient_id!!!!!
	*/
	public function LoadPatientTodispenseFromDBverifyprescriptions(Request $request)
    {
        $mrn=$request['mrn'];
		return $this->patient_to_verify(DB::select("select id from tbl_patients where medical_record_number='$mrn'")[0]->id);

	}
 
   
	
    public function Dispensing_queue($facility_id)
    {
        $sql = "select DISTINCT patient_id,visit_id, medical_record_number,first_name,last_name,middle_name from patients_with_pending_prescriptions where facility_id = '".$facility_id."'  limit 25";

		return DB::select($sql);
	//
	}

	//    searching patient to dispense direct from DB
    public function searchPatientTodispense(Request $request)
    {
        $searchKey=$request['searchKey'];

        $sql = "select DISTINCT patient_id, visit_id, medical_record_number,first_name,last_name,middle_name from patients_with_pending_prescriptions where search_key like '%".$searchKey."%' limit 10";


        return DB::select($sql);
	//
	}
	
	
	public function patient_to_dispense($visit_id)

    {
        $sql = "select DISTINCT tbl_prescriptions.id,dose,out_of_stock,frequency,duration,tbl_patients.id as patient_id,visit_id, tbl_prescriptions.item_id,tbl_prescriptions.quantity,instruction,
		tbl_patients.medical_record_number,tbl_patients.first_name,tbl_patients.last_name,tbl_patients.middle_name,tbl_patients.dob,tbl_patients.gender,
		users.name,
		item_name,start_date,users.facility_id
		from tbl_prescriptions join tbl_patients on tbl_prescriptions.visit_id = '".$visit_id."' and dispensing_status = 2 and tbl_prescriptions.patient_id = tbl_patients.id
		join users on users.id = tbl_prescriptions.prescriber_id
		join tbl_encounter_invoices ON tbl_prescriptions.visit_id = tbl_encounter_invoices.account_number_id
		join tbl_invoice_lines ON tbl_encounter_invoices.id = tbl_invoice_lines.invoice_id AND tbl_invoice_lines.item_id = tbl_prescriptions.item_id AND (tbl_invoice_lines.status_id = 2 OR tbl_invoice_lines.payment_filter = 3 OR tbl_invoice_lines.is_payable IS NOT TRUE)";

		return DB::select($sql);
	//
	}
	

	/****you should have used the same function defined above by passing
	patient_id!!!!!
	*/
	public function LoadPatientTodispenseFromDB(Request $request)
    {
        $mrn=$request['mrn'];
		return $this->patient_to_dispense(DB::select("select id from tbl_patients where medical_record_number='$mrn'")[0]->id);

	}

    public function item_receiving_registration(Request $request)
    {
        if($request['item_id']==""){
            return response()->json([
                'msg'=>'Choose Item First',
                'status'=>0
            ]);
        }
        else if($request['received_store_id']==""){
            return response()->json([
                'msg'=>'Choose Store',
                'status'=>0
            ]);
        }
        else if($request['invoice_refference']==""){
            return response()->json([
                'msg'=>'Choose Invoice or  Reference Number',
                'status'=>0
            ]);
        }
        else if($request['batch_no']==""){
            return response()->json([
                'msg'=>'Enter Batch Number',
                'status'=>0
            ]);
        }
        else if($request['transaction_type_id']==""){
            return response()->json([
                'msg'=>'Choose Transaction Type',
                'status'=>0
            ]);
        }
        else if($request['quantity']==""){
            return response()->json([
                'msg'=>'Enter Item Quantity received',
                'status'=>0
            ]);
        }
        else if($request['expiry_date']==""){
            return response()->json([
                'msg'=>'Enter Item Expiry Date',
                'status'=>0
            ]);
        }
        else if($request['price']==""){
            return response()->json([
                'msg'=>'Enter Item Cost Price',
                'status'=>0
            ]);
        }
        else{

            Tbl_receiving_item::create($request->all());
            return response()->json([
                'msg'=>'Item Successful Received',
                'status'=>1
            ]);

        }
    }

    public function dispensings($user_id)
    {


        return DB::table('vw_dispensing_item_balance')
            ->join('tbl_user_store_configurations','tbl_user_store_configurations.store_id','=','tbl_user_store_configurations.store_id')
            ->Where('tbl_user_store_configurations.user_id',$user_id)
->select('vw_dispensing_item_balance.balance','item_id','facility_id','main_category_id')
            ->groupBy('vw_dispensing_item_balance.balance')
            ->groupBy('item_id')
            ->groupBy('facility_id')

            ->groupBy('main_category_id')
            ->get();

    }
	
	public function dispensing_item_receiving_list($facility,$user_id)
    {


        return DB::table('tbl_user_store_configurations')
            ->join('vw_dispensing_window','vw_dispensing_window.store_id','=','tbl_user_store_configurations.store_id')
            ->Where('tbl_user_store_configurations.user_id',$user_id)
			
            ->select('vw_dispensing_window.*')
            ->groupBy('vw_dispensing_window.created_at')
            ->orderBy('vw_dispensing_window.quantity_received','asc')
           // ->groupBy('vw_dispensing_window.item_id')
            ->get();
    }

    public function item_balances_list_in_dispensing(Request $request)
    {
		
		$facility_id=$request->facility_id;
		$user_id=$request->user_id;
		$report_type=$request->report_type;
		
        //balance
		if($report_type==1){
		 return DB::table('tbl_user_store_configurations')
				->join('tbl_dispensers','tbl_dispensers.dispenser_id','=','tbl_user_store_configurations.store_id')
				->join('tbl_items','tbl_items.id','=','tbl_dispensers.item_id')
				->join('tbl_store_lists','tbl_store_lists.id','=','tbl_dispensers.dispenser_id')
				->Where('tbl_user_store_configurations.status','=',1)
				->Where('tbl_user_store_configurations.user_id',$user_id)
				->Where('tbl_dispensers.control','=','l')
				->select(DB::raw('sum(quantity_received) as quantity_received'), 'tbl_items.item_name','tbl_store_lists.store_name', 'batch_no')
				->groupBy('tbl_dispensers.item_id')
				->groupBy('tbl_dispensers.batch_no')
				->groupBy('tbl_dispensers.dispenser_id')
				->get();
		}
		
        //detailed
        if($report_type==2){
			return $itembalance =DB::select(DB::raw("SELECT vw_dispensing_window.* FROM `tbl_user_store_configurations` inner join `vw_dispensing_window` on `tbl_user_store_configurations`.store_id=`vw_dispensing_window`.store_id WHERE tbl_user_store_configurations.user_id='".$user_id."'  AND status=1 GROUP BY created_at") );

		}
		
        //dispensed
        if($report_type==3){
			$start_date=$request->datee['start_date'];
			$end_date=$request->datee['end_date'];
			
			if($start_date == "" || $start_date == "0000-00-00")
				$start_date = date('Y-m-d');
			
			if($end_date == "" || $end_date == "0000-00-00")
				$end_date = date('Y-m-d');
			
			$sql = "select item_name, medical_record_number, item_code, dispensed_by, date,quantity from vw_prescriptions_dispensed where authority=1 AND date BETWEEN '{$start_date}' AND '{$end_date}' ";

            return DB::select($sql);
		}


    }

    public function searchItemdispensingReceived(Request $request)
    {

        $searchKey=$request['searchKey'];
        return DB::table('vw_dispensing_window')
            ->Where('item_name','like','%'.$searchKey.'%')
            ->orWhere('item_category','like','%'.$searchKey.'%')
            ->groupBy('item_id')
            ->get();
    }

    public function batchdispensing_list($item_id,$user_id)
    {
        return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_dispensers','tbl_dispensers.dispenser_id','=','tbl_store_lists.id')

            ->Where('item_id',$item_id)
			->Where('tbl_user_store_configurations.status','=',1)
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            
            ->Where('control','l')
            ->Where('quantity_received','>',0)
            ->select('tbl_dispensers.id','tbl_store_lists.id as store_id','tbl_dispensers.item_id','tbl_dispensers.batch_no',
                'quantity_received as quantity','tbl_store_lists.store_name','received_from_id')
            ->orderBy('quantity_received','desc')
			->groupBy('tbl_dispensers.batch_no')
            ->get();


    }
     
	public function loaddispensingBatchBalance($batch_no,$store_id,$item_id)
    {
        return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_dispensers','tbl_dispensers.dispenser_id','=','tbl_store_lists.id')
			->Where('tbl_user_store_configurations.status','=',1)
            ->Where('batch_no',$batch_no)
            ->Where('item_id',$item_id)
            ->Where('tbl_user_store_configurations.store_id',$store_id)
            ->Where('control','=','l')
            ->Where('quantity_received','>',0)
            ->select('control','tbl_dispensers.id','tbl_store_lists.id as store_id','tbl_dispensers.item_id','tbl_dispensers.batch_no',
                'quantity_received','tbl_store_lists.store_name','received_from_id')
            ->groupBy('quantity_received')
            ->groupBy('batch_no')
            ->orderBy('quantity_received','desc')

            ->get();
    }
    

    


    public function batch_patient_dispensing_list($item_id,$user_id,$quantity)
    {
        return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_dispensers','tbl_dispensers.dispenser_id','=','tbl_user_store_configurations.store_id')
            ->select('quantity_received','received_from_id','tbl_store_lists.store_name','dispenser_id','item_id','batch_no')
            ->Where('item_id',$item_id)
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->Where('tbl_dispensers.quantity_received','>=',$quantity)
            ->groupBy('tbl_dispensers.dispenser_id')
            ->groupBy('tbl_dispensers.dispenser_id')
            ->groupBy('tbl_dispensers.item_id')
            ->groupBy('tbl_dispensers.batch_no')
            ->groupBy('tbl_dispensers.received_from_id')
            ->groupBy('tbl_store_lists.store_name')
            ->groupBy('tbl_dispensers.quantity_received')
			->orderBy('tbl_dispensers.id')
            ->get();
    }


    public function dispensing_item_issuing(Request $request)
    {

        if($request['item_id']==""){
            return response()->json([
                'msg'=>'Choose Item First',
                'status'=>0
            ]);
        }

        else if($request['store_receiver_id']==""){
            return response()->json([
                'msg'=>'Choose Store you wish to Send This item..',
                'status'=>0
            ]);
        }
        else if($request['transaction_type_id']==""){
            return response()->json([
                'msg'=>'Choose Issuing Type of This Transaction',
                'status'=>0
            ]);
        }
        else if($request['PreviusBalance']-$request["store_balance"]<0){
            return response()->json([
                'msg'=>'No Enough Quantity from this Store..','Only'.$request['PreviusBalance'].' remained',
                'status'=>0
            ]);
        }
        else{

            $item_id=$request['item_id'];
            $facility_id=$request['facility_id'];
            $quantity_issued=$request['quantity_issued'];
            $store_sender_id=$request['store_sender_id'];
            $store_receiver_id=$request['store_receiver_id'];
            $transaction_type_id=$request['transaction_type_id'];
            $requesting_store_id=$request['issued_store_id'];
            $batch_no=$request['batch_no'];
            //$requested_amount=$request['requested_amount'];
            //$request_status_id=$request['request_status_id'];
            $received_from_id=$request['received_from_id'];
            $vendor_id=$request['vendor_id'];
            $quantityInStore=$request['store_balance'];
            $expiry_date=$request['expiry_date'];
            $user_id=$request['user_id'];
            $invoice_refference=$request['invoice_number'];
            $price=0;
            $store_type_id=$request['store_type_id'];
            $store_name=$request['store_name'];
            $internal_issuer_id=$request['internal_issuer_id'];

//if items are issued to another main store
            if ($store_type_id==2){

//                Tbl_sub_store::create([ //for receiving item within a same sub store type
//                    'item_id'=>$item_id,
//                    'quantity'=>$quantity_issued,
//                    //'quantity_issued'=>$quantity_issued,
//                    'received_from_id'=>$received_from_id,
//                    'issued_store_id'=>$store_sender_id,
//                    'requested_store_id'=>$store_receiver_id,
//                    'transaction_type_id'=>$transaction_type_id,
//                    'batch_no'=>$batch_no,
//                ]);
//
//                Tbl_receiving_item::create([ //issuing item within a same sub store type
//                    'item_id'=>$item_id,
//                    'received_store_id'=>$received_from_id,
//                    'requesting_store_id'=>$store_receiver_id,
//                    'transaction_type_id'=>$transaction_type_id,
//                    'invoice_refference'=>$invoice_refference,
//                    'batch_no'=>$batch_no,
//                    //'requested_amount'=>$quantity_issued,
//                    'facility_id'=>$facility_id,
//                    'quantity'=>$quantity_issued,
//                    'user_id'=>$user_id,
//                    'received_from_id'=>$vendor_id,
//                    'internal_issuer_id'=>$internal_issuer_id,
//                    'expiry_date'=>$expiry_date,
//                    'price'=>$price,
//
//                ]);


                return response()->json([
                    'msg'=>'Successful Issued to...'.$store_name,
                    'status'=>1
                ]);


            }

            //if items are issued to sub store
            else if ($store_type_id==3){
                Tbl_sub_store::create([ //for receiving item within a same sub store type
                    'item_id'=>$item_id,
                    'quantity'=>$quantity_issued,
                    //'quantity_issued'=>$quantity_issued,
                    'received_from_id'=>$store_sender_id,
                    'issued_store_id'=>$store_receiver_id,
                    //'requested_store_id'=>$store_receiver_id,
                    'transaction_type_id'=>$transaction_type_id,
                    'batch_no'=>$batch_no,
                ]);


                Tbl_sub_store::create([ //issuing item within a same sub store type
                    'item_id'=>$item_id,
                    'quantity'=>$quantityInStore,
                    'quantity_issued'=>$quantity_issued,
                    'received_from_id'=>$received_from_id,
                    'issued_store_id'=>$store_sender_id,
                    'requested_store_id'=>$store_receiver_id,
                    'transaction_type_id'=>$transaction_type_id,
                    'batch_no'=>$batch_no,
                ]);


                return response()->json([
                    'msg'=>'Successful Issued to...'.$store_name,
                    'status'=>1
                ]);



            }
            //if items are issued to dispensing
            else if ($store_type_id==4){

                Tbl_sub_store::create([
                    'item_id'=>$item_id,
                    'quantity'=>$quantityInStore,
                    'quantity_issued'=>$quantity_issued,
                    'received_from_id'=>$received_from_id,
                    'issued_store_id'=>$store_sender_id,
                    'requested_store_id'=>$store_receiver_id,
                    'transaction_type_id'=>$transaction_type_id,
                    'batch_no'=>$batch_no,
                ]);


                Tbl_dispenser::create([
                    'item_id'=>$item_id,
                    'transaction_type_dispensed_id'=>$transaction_type_id,
                    'quantity_received'=>$quantity_issued,
                    'received_from_id'=>$store_sender_id,
                    'dispenser_id'=>$store_receiver_id,
                    'batch_no'=>$batch_no,
                ]);



                return response()->json([
                    'msg'=>'Successful Issued to...'.$store_name,
                    'status'=>1
                ]);
            }
        }

    }
    
   
    
   //order making codes


    public function dispensing_item_ordering(Request $items)
    {
        foreach ($items->all() as $request){
            $item_id=$request['item_id'] ;
            $request_sender=$request['request_sender'] ;
            $request_receiver=$request['request_receiver'] ;
            $request_receiver_type=$request['request_receiver_type'] ;
            $request_sender_type=$request['request_sender_type'] ;
            $quantity=$request['quantity'] ;
            $facility_id=$request['facility_id'] ;
            $user_id=$request['user_id'] ;
            $receiver_name=$request['receiver_name'] ;

            if(patientRegistration::duplicate('tbl_dispensers',['item_id','dispenser_id','received_from_id','request_amount', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                    [$item_id,$request_sender,$request_receiver,$quantity
                    ])==true){

            }

            if($request_sender_type==4 && $request_receiver_type==3 ) {
                //if request is to sub store from dispensing


                $dispense = Tbl_dispenser::create([
                    'item_id' => $item_id,
                    'request_amount' => $quantity,
                    'dispenser_id' => $request_sender,
                    'received_from_id' => $request_receiver,
                    'facility_id' => $facility_id,
                    'user_id' => $user_id,
                    'dispensing_status_id' => 4,
                    'control'=>'o',
                ]);
                $order_id = $dispense->id;
                $oldData=null;
                $patient_id=null;
                $trackable_id=$order_id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$dispense,$oldData);

                if ($order_id >= 1) {


                    $substore = Tbl_sub_store::create([
                        'item_id' => $item_id,
                        'request_amount' => $quantity,
                        'requested_store_id' => $request_sender,
                        'issued_store_id' => $request_receiver,
                        'facility_id' => $facility_id,
                        'user_id' => $user_id,
                        'order_no' => $order_id,
                        'request_status_id' => 4,
                        'control'=>'o',
                    ]);


                } else {

                }
            }


            if($request_sender_type==4 && $request_receiver_type==2 ) {
                //if request is to main from dispensing


                $dispense = Tbl_dispenser::create([
                    'item_id' => $item_id,
                    'request_amount' => $quantity,
                    'dispenser_id' => $request_sender,
                    'received_from_id' => $request_receiver,
                    'facility_id' => $facility_id,
                    'user_id' => $user_id,
                    'dispensing_status_id' => 4,
                    'control'=>'o',
                ]);
                $order_id = $dispense->id;
                $oldData=null;
                $patient_id=null;
                $trackable_id=$order_id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$dispense,$oldData);

                if ($order_id >= 1) {


                    $mainstore = Tbl_receiving_item::create([
                        'item_id' => $item_id,
                        'requested_amount' => $quantity,
                        'requesting_store_id' => $request_sender,
                        'received_store_id' => $request_receiver,
                        'facility_id' => $facility_id,
                        'user_id' => $user_id,
                        'order_no' => $order_id,
                        'request_status_id' => 4,
                        'control'=>'o',
                    ]);


                } else {

                }
            }



        }
        return response()->json([
            'msg' => 'Request has Successful  send to' . $receiver_name,
            'status' => 1
        ]);
    }

    public function save_dispensed_item(Request $request)
    {
		$os=$request['os'];
        $item_id=$request['item_id'];
        $quantity_dispensed=$request['quantity_dispensed'];

        $patient_id=$request['patient_id'];
        $request_amount=$request['request_amount'];
        $received_from_id=$request['received_from_id'];
        $user_id=$request['user_id'];
        $dispensing_status_id=$request['dispensing_status_id'];
        $store_id=$request['store_id'];
        $batch_no=$request['batch_no'];
        $order_id=$request['order_id'];

        if($os=="") {
			$last_balance = Tbl_dispenser::where("item_id", $item_id)
                                            ->where("batch_no", $batch_no)
                                            ->where("dispenser_id", $store_id)
                                            ->where("control", "l")
                                            ->sum("quantity_received");
           
			$quantity_received = $last_balance - $quantity_dispensed;
			
			if($quantity_received >= 0){
				/*$data = Tbl_dispenser::create([
					'item_id' => $item_id,
					'quantity_dispensed' => $quantity_dispensed,
					'patient_id' => $patient_id,
					'request_amount' => $request_amount,
					'received_from_id' => $received_from_id,
					'user_id' => $user_id,
					'dispenser_id' => $store_id,
					'batch_no' => $batch_no,
					'quantity_received' => $quantity_received,
					'dispensing_status_id' => $dispensing_status_id,
					'control' => 'l',
				]);
				
				$update = Tbl_dispenser::where('id', $last_balance->id)
					->update(['control' => 'c']);
				
				$oldData=null;
				$trackable_id=$order_id;
				 SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);

				if ($data) {
					Tbl_prescription::where('id', $order_id)->update([
						'dispensing_status' => 1,
						'dispenser_id' => $user_id
					]);
					return response()->json([
						'msg' => 'Done',
						'status' => 1
					]);
				} else {
					return response()->json([
						'msg' => 'Request has failed  to send to',
						'status' => 0
					]);
				}*/
                $query = " if exists(select id from tbl_dispensers where item_id=$item_id and batch_no='$batch_no' and dispenser_id=$store_id and control='l' having (SUM(quantity_received) - $quantity_dispensed) >= 0) then";
                
                $query = " insert into tbl_dispensers(item_id, quantity_dispensed, patient_id, request_amount, received_from_id, user_id, dispenser_id, batch_no, quantity_received, dispensing_status_id, control, created_at, updated_at)";
                
                $query .= " select $item_id,$quantity_dispensed,$patient_id,$request_amount,$received_from_id,$user_id,$store_id,'$batch_no',SUM(quantity_received)-$quantity_dispensed,$dispensing_status_id,'l', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP";
                
                $query .= " from tbl_dispensers where item_id=$item_id and batch_no='$batch_no' and dispenser_id=$store_id and control='l' having (SUM(quantity_received) - $quantity_dispensed) >= 0;";
                
                $query .= " SET @last_id = (select MAX(id) from tbl_dispensers where item_id=$item_id and batch_no='$batch_no' and dispenser_id=$store_id and control='l');";
                
                $query .= " update tbl_dispensers olds set control='c', updated_at = CURRENT_TIMESTAMP where item_id=$item_id and batch_no='$batch_no' and dispenser_id=$store_id and olds.id < @last_id;";
                
                $query .= " update tbl_prescriptions set dispensing_status=1, dispenser_id = '$user_id' where id = $order_id;";
                
                $query .= " end if;";
                
                DB::statement($query);
                
                return response()->json([
                            'msg' => 'Done',
                            'status' => 'success'
                        ]);
			}else{
				return response()->json([
						'msg' => 'Store balance has changed and request no longer possible. Pls select another batch or refill store',
						'status' => 'info'
					]);
			}
        }
        else{
            Tbl_prescription::where('id', $order_id)->update([
                'dispensing_status' => 4,
                'dispenser_id' => $user_id
            ]);
            return response()->json([
                'msg' => 'Done',
                'status' => 'success'
            ]);
        }
    }

    public function save_verified_item(Request $request)
    {
        $id=$request['id'];
        $item_id=$request['item_id'];
        $bill_id=$request['bill_id'];
        $quantity=$request['quantity'];
        $facility_id=$request['facility_id'];
        $user_id=$request['user_id'];
        $account_number_id=$request['account_number_id'];

        $getbalance= DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_dispensers','tbl_dispensers.dispenser_id','=','tbl_user_store_configurations.store_id')
            ->select('quantity_received','received_from_id','tbl_store_lists.store_name','dispenser_id','item_id','batch_no')
            ->Where('item_id',$item_id)
            ->Where('control','=','l')
            ->Where('quantity_received','>=',$quantity)
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->get();
        if (count($getbalance)>0) {

            $balance_remained = $getbalance[0]->quantity_received - $quantity;
            if ($balance_remained >= 0) {
                $os = null;
                //update row of verified item row prescription
                $oldData=Tbl_prescription::where('id', $id)->get();
                Tbl_prescription::where('id', $id)->update([
                    'quantity' => number_format($quantity, 2, '.', ''),
                    'dispensing_status' => 2,
                    'verifier_id' => $user_id,
                    'out_of_stock' => $os
                ]);
                $patient_id=$oldData[0]->patient_id;
                $trackable_id=$oldData[0]->id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,null,$oldData);

            } else {
                $os = 'OS';
                $oldData= Tbl_prescription::where('id', $id)->get();
                Tbl_prescription::where('id', $id)->update([
                    'quantity' =>  number_format($quantity, 2, '.', ''),
                    'dispensing_status' => 4,
                    'verifier_id' => $user_id,
                    'out_of_stock' => $os
                ]);
                $patient_id=$oldData[0]->patient_id;
                $trackable_id=$oldData[0]->id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,null,$oldData);
            }
        }
        else{
            $os = 'OS';
            $oldData=  Tbl_prescription::where('id', $id)->get();
            Tbl_prescription::where('id', $id)->update([
                'quantity' =>  number_format($quantity, 2, '.', ''),
                'dispensing_status' => 4,
                'verifier_id' => $user_id,
                'out_of_stock' => $os
            ]);
             
            $patient_id=$oldData[0]->patient_id;
            $trackable_id=$oldData[0]->id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,null,$oldData);
            return response()->json([
                'msg' => 'This Item Is not available in Your  Stock',
                'status' => 0
            ]);
        }


    }

    public function Save_pos_os(Request $request)
    {
        $date = date('Y-m-d');
       $b=$request->all();
            $medData2 = Tbl_prescription::create(["item_id"=>$b['item_id'],"patient_id"=>$b['patient_id'],"visit_id"=>$b['visit_id'],
                "prescriber_id"=>$b['user_id'],"verifier_id"=>$b['user_id'],"quantity"=> number_format($b['quantity'], 2, '.', ''),"frequency"=>$b['frequency'],"duration"=>$b['duration'],
                "dose"=>$b['dose'],"start_date"=>$date,"instruction"=>'...',"out_of_stock"=>$b['out_of_stock'],"dispensing_status"=>4]);
            $newData=$medData2;
            $patient_id=$newData->patient_id;
            $trackable_id=$newData->id;
            $user_id=$newData->prescriber_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);
        return $medData2;
    }

    public function save_cancel_prescription(Request $request)
    {

 $id=$request['id'];

        $user_id=$request['user_id'];
        $cancellation_reason=$request['cancellation_reason'];

        $oldData=Tbl_prescription::where('id',$id)->get();
        Tbl_prescription::where('id',$id)->update([

            'dispensing_status' => 3,
            'verifier_id' => $user_id,
            'cancellation_reason' => $cancellation_reason
        ]);
        $patient_id=$oldData[0]->patient_id;
        $trackable_id=$oldData[0]->id;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,null,$oldData);
    }

 public function save_dispensed_to_users(Request $data)
    {
        foreach ($data->all() as $request) {
if (empty($request['patient_id'])){
    $visit_id = null;
    $patient_id = null;
}
else{
    $visit_id = $request['visit_id'];
    $patient_id = $request['patient_id'];
}
            $item_id = $request['item_id'];
            $quantity_dispensed = $request['quantity_dispensed'];
            $received_from_id = $request['received_from_id'];
            $user_id = $request['user_id'];
            $dispensing_status_id = $request['dispensing_status_id'];
            $store_id = $request['store_id'];
            $batch_no = $request['batch_no'];
           $identifier = $request['identifier'];
            $quantity_received = $request['quantity_received'];


                $update = Tbl_dispenser::where('id', $identifier)
                    ->update(['control' => 'c']);
                $data = Tbl_dispenser::create([
                    'item_id' => $item_id,
                    'patient_id' => $patient_id,
                    'quantity_dispensed' => $quantity_dispensed,
                    'patient_id' => $patient_id,
                    'received_from_id' => $received_from_id,
                    'user_id' => $user_id,
                    'dispenser_id' => $store_id,
                    'batch_no' => $batch_no,
                    'quantity_received' => $quantity_received,
                    'dispensing_status_id' => $dispensing_status_id,
                    'control' => 'l',
                ]);
                if ($data){
                    Tbl_prescription::create([
                        'item_id' => $item_id,
                        'quantity' => $quantity_dispensed,
                        'patient_id' => $patient_id,
                        'prescriber_id' => $user_id,
                        'dispenser_id' => $user_id,
                        'verifier_id' => $user_id,
                        'dispensing_status' => 1,
                        'visit_id' => $visit_id,
                        'start_date' => Date('Y-m-d'),

                    ]);
                }
            



    }
        return response()->json([
                'msg' => 'Done',
                'status' => 1
            ]);

    }

    public function postMedicines_verified(Request $request)
    {

//send bill for verified available item prescribed  start
        $dtz = json_encode($request->all());
        $data = json_decode($dtz);

        //start posting

        $data1 = Tbl_encounter_invoice::create(["account_number_id" => $data[0]->account_number_id, "user_id" => $data[0]->user_id, "facility_id" => $data[0]->facility_id]);
        $invoice_id = $data1->id;

        foreach ($request->all() as $b) {

            $id = $b['id'];
            $item_id = $b['item_id'];
            $bill_id = $b['bill_id'];
            $quantity = $b['quantity'];
            $facility_id = $b['facility_id'];
            $user_id = $b['user_id'];
            $account_number_id = $b['account_number_id'];
            $user_id = $b['user_id'];
            $date = date('Y-m-d');
             $getbalance= DB::table('tbl_user_store_configurations')
                ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
                ->join('tbl_dispensers','tbl_dispensers.dispenser_id','=','tbl_user_store_configurations.store_id')
                ->select('quantity_received','received_from_id','tbl_store_lists.store_name','dispenser_id','item_id','batch_no')
                ->Where('item_id',$item_id)
                ->Where('control','=','l')
                ->Where('quantity_received','>=',$quantity)
                ->Where('tbl_user_store_configurations.user_id',$user_id)
                ->get();
            if (count($getbalance)>0) {

                //$item_id = $request->item_id;
                $sub_cat = Tbl_bills_category::where('account_id', $account_number_id)->orderBy('id', 'desc')->get();
                $patient_category_main = $sub_cat[0]['main_category_id'];
                if ($patient_category_main == 3) {
                    $bill_id = 1;
                   // $pay_category=1;

                }
                 $priced = DB::table('vw_shop_items')
                    ->select('item_id', 'price_id', 'item_type_id')
                    ->where('item_id', $item_id)
                    ->where('facility_id', $facility_id)
                    ->where('patient_category_id', $bill_id)
                    ->get();
                if (count($priced) > 0) {
                    $item_price_id = $priced[0]->price_id;
                    $item_type_id = $priced[0]->item_type_id;
                    $medData = Tbl_invoice_line::create(["invoice_id" => $invoice_id, "item_type_id" => $item_type_id, "payment_filter" =>$b['bill_id'],
                        "quantity" => number_format($b['quantity'], 2, '.', ''), "item_price_id" => $item_price_id, "user_id" => $b['user_id'], "patient_id" => $b['patient_id'],
                        "status_id" => 1, "facility_id" => $b['facility_id'], "discount_by" => $b['user_id'],]);
                    $oldData=null;
                    $patient_id=$medData->patient_id;
                    $trackable_id=$medData->id;
                    SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$medData,$oldData);
                    

                } else {

                    Tbl_prescription::where('id', $id)->update([
                        'quantity' => $quantity,
                        'dispensing_status' => 3,
                        'verifier_id' => $user_id,

                    ]);

                }

            }
            //}
            /*  else{
                  $jected='Rejected prescription';

              }*/







            //end posting


        }

        return response()->json([
            'msg' => 'Request Successful Processed',
            'status' => 1
        ]);


    }

    public function dispensed_item_range(Request $request)
    {
$facility_id = $request->input('facility_id');
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $all=[];
     $all[]= $patients_attended= DB::table('tbl_prescriptions')
            ->select(DB::raw('count(patient_id) as patients_attended'))
            ->whereBetween('updated_at',[$start,$end])
            ->get();
        $all[]= $patients_attended= DB::table('tbl_prescriptions')
            ->select(DB::raw('count(patient_id) as patients_with_os'))
            ->where('out_of_stock','=','OS')
            ->whereBetween('created_at',[$start,$end])
            ->get();
        $all[]= $patients_attended= DB::table('tbl_prescriptions')
             ->join('tbl_items','tbl_items.id','=','tbl_prescriptions.item_id')
            ->select(DB::raw('tbl_items.item_name as patients_with_more_5_10_os'))
            ->where('out_of_stock','=','OS')
            ->whereBetween('tbl_prescriptions.created_at',[$start,$end])
            ->groupBy('item_id')
            ->orderBY(DB::raw('count(tbl_prescriptions.item_id)'),'desc')
            ->limit(10)
            ->get();
        $all[]= $patients_attended= DB::table('tbl_prescriptions')
             ->join('tbl_items','tbl_items.id','=','tbl_prescriptions.item_id')
            ->select(DB::raw('tbl_items.item_name as patients_with_more_5_10_os_more'),DB::raw('count(tbl_prescriptions.item_id) as freq'))
            ->where('out_of_stock','=','OS')
            ->whereBetween('tbl_prescriptions.created_at',[$start,$end])
            ->groupBy('item_id')
            ->orderBY(DB::raw('count(tbl_prescriptions.item_id)'),'desc')
            ->limit(10)
            ->get();
//        $all[]=DB::table('vw_prescriptions_dispensed')
//        ->select('item_name','medical_record_number','item_code','dispensed_by','date',DB::raw('sum(quantity) as quantity'),DB::raw('count(patient_id) as patients'))
//        ->where('facility_id',$facility_id)
//        ->whereBetween('date',[$start,$end])
//  ->groupBy('item_id')
//  //->groupBy('patient_id')
//    ->get();

        $all[]=DB::select("SELECT tbl_items.item_name,tbl_patients.medical_record_number,sum(tbl_prescriptions.quantity) as quantity,count(tbl_prescriptions.patient_id) as patients

from    tbl_prescriptions 
		left join tbl_patients on tbl_prescriptions.patient_id = tbl_patients.id
        left join tbl_items on tbl_prescriptions.item_id = tbl_items.id
        left join tbl_invoice_lines on tbl_items.id = tbl_invoice_lines.item_id
        left JOIN users ON tbl_prescriptions.dispenser_id = users.id
        
where tbl_prescriptions.dispensing_status=1 AND tbl_prescriptions.created_at between '".$start."' and '".$end."'

group by tbl_prescriptions.item_id");
        return $all;
}

    public function saveGrouped(Request $data)
    {

        foreach ($data->save_mapping as $request){

$item_id=$request['item_id'];
$check=Tbl_dispensed_groups::where('item_id',$item_id)->get();
if(count($check)<1){
    Tbl_dispensed_groups::create([
        'identifier'=>$request['tracer_medicine_id'],
        'item_id'=>$item_id
    ]) ;
}
}
        return response()->json([
            'msg' => 'Successful Saved',
            'status' => 1
        ]);

}

  public function pharmacy_item_returning(Request $request)
    {
      foreach ($request->all() as $item){
           $identifier=$item['identifier'];
       $item_id=$item['item_id'];
       $received_from_id=$item['received_from_id'];
       $user_targeted_id=$item['user_targeted_id'];
       $user_id=$item['user_id'];
       $issued_store_id=$item['issued_store_id'];
       $store_type_id=$item['store_type_id'];
          $facility_id=$item['facility_id'];
          $user_id=$item['user_id'];
       $batch_no=$item['batch_no'];
       $remarks=$item['remarks'];
       $quantity_issued=$item['quantity_issued'];
       $balance_remained=$item['balance_remained'];
          $transaction_type_id=$item['transaction_type_id'];
          $dispenserRow=Tbl_dispenser::where('id', $identifier)->get();

          $miss=[];
          $update = Tbl_dispenser::where('id', $identifier)
              ->update(['control' => 'c']);
          $data = Tbl_dispenser::create([
              'item_id' => $item_id,
              'received_from_id' => $received_from_id,
              'user_id' => $user_id,
              'dispenser_id' => $dispenserRow[0]['dispenser_id'],
              'batch_no' => $batch_no,
              'quantity_received' => $balance_remained,
              'control' => 'l',
          ]);
          if ($store_type_id==2){
          $MainStoreRow=Tbl_receiving_item::where('item_id','=',$item_id)->where('batch_no','=',$batch_no)->take(1)->get();
             if (count($MainStoreRow)==0)
             {
                 array_push($miss,[
                     'store Name'=>$item['from'],
                     'itemName'=>$item['item_name'],
                     'reason'=>"Data mismatching"
                 ]);


             }
             else{
                 $expiry_date=$MainStoreRow[0]['expiry_date'];
                 $received_from_id_main_value=$MainStoreRow[0]['received_from_id'];
                 $invoice_refference=$MainStoreRow[0]['invoice_refference'];
                 Tbl_receiving_item::create([
                     'item_id'=>$item_id,
                     'received_store_id'=>$received_from_id,
                     'received_date'=>date("Y-m-d"),
                     'invoice_refference'=>$invoice_refference,
                     'batch_no'=>$batch_no,
                     'transaction_type_id'=>$transaction_type_id,
                     'quantity'=>$quantity_issued,
                     'expiry_date'=>$expiry_date,
                     'price'=>0,
                     'control'=>'l',
                     'control_in'=>'r',
                     'facility_id'=>$facility_id,
                     'user_id'=>$user_targeted_id,
                     'received_from_id'=>$received_from_id_main_value,
                     'remarks'=>$remarks.'  < '.$item['from'].'>'
                 ]);
             }

          }
          if ($store_type_id==3){
              Tbl_sub_store::create([
                  'item_id' => $item_id,
                  'quantity' => $quantity_issued,
                  'received_from_id' => $dispenserRow[0]['dispenser_id'],
                  'issued_store_id' => $issued_store_id,
                  'transaction_type_id' => $transaction_type_id,
                  'batch_no' => $batch_no,
                  'user_targeted_id' =>$user_targeted_id,
                  'control'=>'l',
              ]);

          }
          if ($store_type_id ==4){
              array_push($miss,[
                  'store Name'=>$item['from'],
                  'itemName'=>$item['item_name']]);
          }
      }



if (count($miss)>0){
return response()->json([
'msg' => 'Failed to sent to'.json_encode($miss),
'status' => 0
]);
}
        return response()->json([
            'msg' => 'Request Successful Processed',
            'status' => 1
        ]);

}

public function removeFromDispensedGroupMapping(Request $request)
{


    $id = $request['id'];

    DB::statement("DELETE FROM `tbl_dispensed_groups` WHERE `tbl_dispensed_groups`.`id` = '" . $id . "'");


    return response()->json([
        'msg' => ' Removed',
        'status' => 1
    ]);

}

public function daily_dispensed_items(Request $request)
    {

        if(!isset($request->start_date) OR !isset($request->end_date) ){
            return DB::select("SELECT t1.id, Concat(t4.first_name,' ',t4.middle_name,' ',t4.last_name) as names,t4.gender,t4.medical_record_number as mrn,t7.description as diagnosis,t6.status,
(CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END) AS age, t2.item_name,name as doctor_name,out_of_stock ,
 (select u.name  from users  as u where u.id= t1.verifier_id) as dispenser
FROM tbl_prescriptions t1 LEFT join tbl_items t2 ON t1.item_id=t2.id LEFT JOIN users t3 on t3.id=t1.prescriber_id
  LEFT Join tbl_patients t4 on t4.id=t1.patient_id left JOIN tbl_diagnoses t5 on t5.patient_id=t1.patient_id left join  tbl_diagnosis_details t6 on t5.id=t6.diagnosis_id left join tbl_diagnosis_descriptions t7 on t6.diagnosis_description_id=t7.id
WHERE `out_of_stock`='OS'   and TIMESTAMPDIFF(hour,t1.created_at, CURRENT_DATE)<=48 GROUP by t1.id ORDER by t6.id desc");
        }else{
            $start=$request->start_date;
            $end=$request->end_date;
        }

return DB::select("SELECT t1.id, Concat(t4.first_name,' ',t4.middle_name,' ',t4.last_name) as names,t4.gender,t4.medical_record_number as mrn,t7.description as diagnosis,t6.status,
(CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END) AS age, t2.item_name,name as doctor_name,out_of_stock ,
 (select u.name  from users  as u where u.id= t1.verifier_id) as dispenser
FROM tbl_prescriptions t1 LEFT join tbl_items t2 ON t1.item_id=t2.id LEFT JOIN users t3 on t3.id=t1.prescriber_id
  LEFT Join tbl_patients t4 on t4.id=t1.patient_id left JOIN tbl_diagnoses t5 on t5.patient_id=t1.patient_id left join  tbl_diagnosis_details t6 on t5.id=t6.diagnosis_id left join tbl_diagnosis_descriptions t7 on t6.diagnosis_description_id=t7.id
WHERE `out_of_stock`='OS'     AND t1.created_at between '".$start."' AND '".$end."' GROUP by t1.id ORDER by t6.id desc");
}

    public function dispensed_item_range_group(Request $request)
    {
$facility_id = $request->input('facility_id');
        $start_date =  $request->input('start_date');
        $end_date = $request->input('end_date');
        $all=[];
        $all0="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  
   inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
   
 WHERE      tbl_dispensed_groups.identifier = '001' AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}' ";

        $all[] = DB::select($all0);
        $all1="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier= '002' AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'  ";

        $all[] = DB::select($all1);
        $all2="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '003'  AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}' ";

        $all[] = DB::select($all2);
        $all3="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier= '004'  AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}' ";

        $all[] = DB::select($all3);
        $all4="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '005' AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'  ";

        $all[] = DB::select($all4);
        $all5="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '006' AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'  ";

        $all[] = DB::select($all5);
        $all6="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE     tbl_dispensed_groups.identifier = '007' AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'  ";

        $all[] = DB::select($all6);
        $all7="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '008' AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'  ";

        $all[] = DB::select($all7);
        $all8="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '009'  AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}' ";

        $all[] = DB::select($all8);
        $all9="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '010' AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'  ";

        $all[] = DB::select($all9);
        $all10="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '011' AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'  ";

        $all[] = DB::select($all10);
        $all11="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '012'  AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}' ";

        $all[] = DB::select($all11);
        $all12="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier =  '013' AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'  ";

        $all[] = DB::select($all12);
        $all13="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '014' AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'  ";

        $all[] = DB::select($all13);
        $all14="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '015'  AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}' ";

        $all[] = DB::select($all14);
        $all15="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '016'  AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}' ";

        $all[] = DB::select($all15);
        $all16="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '017' AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'  ";

        $all[] = DB::select($all16);
        $all17="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE     tbl_dispensed_groups.identifier = '018'  AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'  ";

        $all[] = DB::select($all17);
        $all18="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '019'  AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'  ";

        $all[] = DB::select($all18);
        $all19="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '020' AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'   ";

        $all[] = DB::select($all19);
        $all20="SELECT
  ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5  then 1 ELSE  0 END ),0) as below_5, 
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) between 5 AND 59 then 1 ELSE  0 END ),0) as between_5_59,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) > 59 then 1 ELSE  0 END ),0) as above_60,
   ifnull(sum(CASE when timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) <5 OR timestampdiff(YEAR ,dob,tbl_prescriptions.created_at) >5   then 1 ELSE  0 END ),0) as total
  
   from   tbl_prescriptions  inner JOIN  tbl_dispensed_groups on   tbl_prescriptions.item_id= tbl_dispensed_groups.item_id
   inner JOIN tbl_patients on   tbl_prescriptions.patient_id=tbl_patients.id
 WHERE      tbl_dispensed_groups.identifier = '021'  AND  tbl_prescriptions.created_at BETWEEN '{$start_date}' AND '{$end_date}'  ";

        $all[] = DB::select($all20);
       return $all;
}

}