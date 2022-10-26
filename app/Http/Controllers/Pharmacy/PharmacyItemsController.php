<?php

namespace App\Http\Controllers\Pharmacy;

use App\classes\patientRegistration;
use App\Pharmacy\Rnr_status_tracker;
use App\Pharmacy\Tbl_dispenser;
use App\Item_setups\Tbl_item_type_mapped;
use App\Facility\Tbl_facility;
use App\Pharmacy\Tbl_dispensing;
use App\Pharmacy\Tbl_pos_dispensing;
use App\Pharmacy\Tbl_elmis_item_program_mapping;
use App\Pharmacy\Tbl_receiving_item;
use App\Pharmacy\Tbl_stock_reconsilliation;
use App\Pharmacy\Tbl_sub_store;
use App\Pharmacy\Tbl_tracer_medicine;
use App\Pharmacy\Tbl_tracer_medicine_mapping;
use App\Pharmacy\Tbl_rnr_adjustiment;
use App\Pharmacy\Tbl_rnr_order;
use App\Pharmacy\Tbl_rnr_order_control;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\classes\SystemTracking;
use App\Trackable;
ini_set("max_execution_time",0);
class PharmacyItemsController extends Controller
{
    //
    public function item_receiving_registration(Request $items)
    {
        foreach ($items->all() as $request){

            $item_id = $request['item_id'];
            $received_store_id = $request['received_store_id'];
            $received_date = $request['received_date'];
            $invoice_refference = $request['invoice_refference'];
            $batch_no = $request['batch_no'];
            $transaction_type_id = $request['transaction_type_id'];
            $quantity = $request['quantity'];
            $expiry_date = $request['expiry_date'];
            $price = $request['price'];
            $facility_id = $request['facility_id'];
            $user_id = $request['user_id'];
            $received_from_id= $request['received_from_id'];
            $control = 'l';
            $control_in = 'r';
            Tbl_receiving_item::create([
                'item_id'=>$item_id,
                'received_store_id'=>$received_store_id,
                'received_date'=>$received_date,
                'invoice_refference'=>$invoice_refference,
                'batch_no'=>$batch_no,
                'transaction_type_id'=>$transaction_type_id,
                'quantity'=>$quantity,
                'expiry_date'=>$expiry_date,
                'price'=>$price,
                'control'=>$control,
                'control_in'=>$control_in,
                'facility_id'=>$facility_id,
                'user_id'=>$user_id,
                'received_from_id'=>$received_from_id,
            ]);

        }
        return response()->json([
            'msg'=>'Item Successful Received',
            'status'=>1
        ]);

    }

    public function item_receiving_list($facility,$user_id)
    {


        return DB::table('tbl_user_store_configurations')
            ->join('vw_receivings','vw_receivings.store_id','=','tbl_user_store_configurations.store_id')
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->Where('control','l')
            ->distinct('vw_receivings.*')
            //->groupBy('batch_no')
            ->groupBy('vw_receivings.store_id')
            ->get();
    }

    //pharmacy reports types
    public function item_balances_list_in_mainstore($facility,$user_id,$report_type)
    {
        //balance
        if($report_type==1){
			return DB::select("select tbl_items.*, tbl_receiving_items.quantity, tbl_store_lists.*, sum(quantity) as quantity, tbl_receiving_items.batch_no 
						from tbl_receiving_items join tbl_items  ON tbl_items.id=tbl_receiving_items.item_id
						join tbl_user_store_configurations ON tbl_receiving_items.received_store_id=tbl_user_store_configurations.store_id
						join tbl_store_lists ON tbl_store_lists.id=tbl_user_store_configurations.store_id
						where tbl_user_store_configurations.status= 1 AND
						 timestampdiff(month,CURDATE(),expiry_date)>=0 AND
						tbl_user_store_configurations.user_id='".$user_id."' AND
						control= 'l' GROUP BY tbl_items.id, tbl_receiving_items.batch_no, tbl_store_lists.id");
        }

        //detailed `
        if($report_type==2){

            return DB::table('tbl_user_store_configurations')
                ->join('vw_receivings','vw_receivings.store_id','=','tbl_user_store_configurations.store_id')
                ->Where('tbl_user_store_configurations.user_id',$user_id)
                ->select('vw_receivings.*')
                ->groupBy('id')
                ->orderBy('vw_receivings.created_at','desc')
                ->get();
        }
        if($report_type==3){


        }

		//reorder level
        if($report_type==4){
            return DB::table('tbl_tracer_medicines')
                ->join('vw_receivings','vw_receivings.item_id','=','tbl_tracer_medicines.item_id')
                ->Where('vw_receivings.facility_id',$facility)
                ->Where('vw_receivings.control','l')
                ->select('vw_receivings.*')
                ->orderBy('vw_receivings.updated_at','asc')
                ->get();
        }

    }



    public function item_balance_list($facility,$user_id)
    {


    }

    public function main_store_incoming_order($facility,$user_id)
    {


        return DB::table('tbl_user_store_configurations')
            ->join('vw_main_store_incoming_order','vw_main_store_incoming_order.store_id','=','tbl_user_store_configurations.store_id')
 ->Where('tbl_user_store_configurations.status','=',1)
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->select('vw_main_store_incoming_order.*')
            ->groupBy('order_no')
            ->get();
    }
    public function searchItemReceived(Request $request)
    {

        $searchKey=$request['searchKey'];
       return DB::select("select distinct item_id,t1.* from tbl_items t1 join tbl_receiving_items t2 on t1.dept_id=4 and t1.id = t2.item_id join tbl_store_lists t3 on t2.received_store_id = t3.id join tbl_store_types t4 on t3.store_type_id = t4.id and t4.store_type_name = 'main store' where t1.item_name like '%$searchKey%' limit 10");
    }

     public function batch_list($item_id,$user_id)
    {
//

        return $batch_list_balance =   DB::select(DB::raw("SELECT tbl_receiving_items.id,expiry_date,invoice_refference as invoice_id,received_from_id as vendor_id,quantity as balance,received_store_id as store_id,batch_no,tbl_store_lists.store_name,item_id
   FROM `tbl_user_store_configurations`
  inner join `tbl_store_lists` on `tbl_store_lists`.id=`tbl_user_store_configurations`.store_id
  inner join `tbl_receiving_items` on `tbl_receiving_items`.received_store_id=`tbl_user_store_configurations`.store_id
   WHERE item_id='{$item_id}' AND tbl_user_store_configurations.user_id='{$user_id}'  AND tbl_user_store_configurations.status=1  AND (control='l') AND quantity>0 AND timestampdiff(month,CURDATE(),expiry_date)>0 GROUP BY tbl_receiving_items.id,tbl_store_lists.id ORDER BY expiry_date ASC "));
    }
	
    public function reconsiliatedBatch(Request $request)
    {
//
        $request->all();

        $item_id=$request['item_id'];
        $user_id=$request['user_id'];
        $sender=$request['sender'];
        if($sender==1) {

            $batch_list_balance = DB::select(DB::raw("SELECT tbl_receiving_items.id,tbl_receiving_items.item_id,tbl_items.item_name,quantity,received_store_id as store_id,batch_no,tbl_store_lists.store_name
   FROM `tbl_user_store_configurations`
  inner join `tbl_store_lists` on `tbl_store_lists`.id=`tbl_user_store_configurations`.store_id
  inner join `tbl_receiving_items` on `tbl_receiving_items`.received_store_id=`tbl_user_store_configurations`.store_id
  inner join `tbl_items` on `tbl_receiving_items`.item_id=`tbl_items`.id
  join tbl_store_types t4 on tbl_store_lists.store_type_id = t4.id and t4.store_type_name = 'Main Store'
   WHERE item_id='{$item_id}' AND tbl_user_store_configurations.user_id='{$user_id}'  AND (control='l') AND quantity>=0 AND timestampdiff(month,CURDATE(),expiry_date)>=0  GROUP  BY batch_no,quantity"));
        }
        elseif ($sender==2){

            $batch_list_balance =   DB::table('tbl_user_store_configurations')
                ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
                ->join('tbl_sub_stores','tbl_sub_stores.issued_store_id','=','tbl_store_lists.id')
				->join('tbl_store_types', 'tbl_store_lists.store_type_id', '=', 'tbl_store_types.id')
                ->join('tbl_items','tbl_items.id','=','tbl_sub_stores.item_id')

                ->Where('item_id',$item_id)
                ->Where('tbl_user_store_configurations.user_id',$user_id)
                ->Where('tbl_store_types.store_type_name','Sub Store')
                ->Where('control','l')
                ->Where('quantity','>',0)
                ->select('tbl_items.item_name','tbl_sub_stores.id','tbl_store_lists.id as store_id','tbl_sub_stores.item_id','tbl_sub_stores.batch_no',
                    'quantity','tbl_store_lists.store_name','received_from_id')

                ->groupBy('batch_no')
                ->groupBy('quantity')
                ->orderBy('quantity','desc')
                ->get();
        }

        elseif ($sender==3){

            $batch_list_balance =    DB::table('tbl_user_store_configurations')
                ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
                ->join('tbl_dispensers','tbl_dispensers.dispenser_id','=','tbl_store_lists.id')
				->join('tbl_store_types', 'tbl_store_lists.store_type_id', '=', 'tbl_store_types.id')
                ->join('tbl_items','tbl_items.id','=','tbl_dispensers.item_id')
                ->Where('item_id',$item_id)
                ->Where('tbl_user_store_configurations.user_id',$user_id)
                ->Where('tbl_store_types.store_type_name','Dispensing')
                ->Where('control','l')
                ->Where('quantity_received','>',0)
                ->select('tbl_dispensers.id','tbl_store_lists.id as store_id','tbl_items.item_name','tbl_dispensers.item_id','tbl_dispensers.batch_no',
                    'quantity_received as quantity','tbl_store_lists.store_name','received_from_id')
                ->orderBy('tbl_dispensers.id','desc')
                ->groupBy('batch_no')
                ->groupBy('quantity_received')

                ->get();

        }

        return $batch_list_balance;

    }

    public function SaveReconsilation(Request $request)
    {
//
        //  return $request['id'];

        if ($request['actual_amount']==''){
            return response()->json([
                'msg'=>'Please Enter Your Actual Amount First ',
                'status'=>0
            ]);
        }
        if($request['sender']==1){
            Tbl_receiving_item::where('id',$request['id'])->update(['quantity'=>$request['actual_amount']]);
        }

        if($request['sender']==2){
            Tbl_sub_store::where('id',$request['id'])->update(['quantity'=>$request['actual_amount']]);
        }

        if($request['sender']==3){
            Tbl_dispenser::where('id',$request['id'])->update(['quantity_received'=>$request['actual_amount']]);
        }

        return response()->json([
            'msg'=>'Successful Updated ',
            'status'=>1
        ]);
    }

    public function batch_list_balance($item_id,$user_id)
    {

        return $batch_list_balance =   DB::select(DB::raw("SELECT tbl_receiving_items.id,expiry_date,invoice_refference as invoice_id,received_from_id as vendor_id,quantity as balance,received_store_id as store_id,batch_no,tbl_store_lists.store_name
   FROM `tbl_user_store_configurations`
  inner join `tbl_store_lists` on `tbl_store_lists`.id=`tbl_user_store_configurations`.store_id
  inner join `tbl_receiving_items` on `tbl_receiving_items`.received_store_id=`tbl_user_store_configurations`.store_id
   WHERE item_id='{$item_id}' AND tbl_user_store_configurations.user_id='{$user_id}'
   AND tbl_user_store_configurations.status=1
   AND (control='l') AND timestampdiff(month,CURDATE(),expiry_date)>=0 ORDER BY expiry_date ASC "));

    }
    public function loadBatchBalance($batch_no,$store_id,$item_id)
    {
       return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_receiving_items','tbl_receiving_items.received_store_id','=','tbl_user_store_configurations.store_id')

            ->Where('batch_no',$batch_no)
            ->Where('tbl_receiving_items.received_store_id',$store_id)
            ->Where('tbl_receiving_items.item_id',$item_id)
             ->Where('tbl_user_store_configurations.status',1)
            ->Where('control','l')
            ->Where('quantity','>',0)
            ->select('tbl_receiving_items.id','expiry_date','invoice_refference as invoice_id','received_from_id','quantity','received_store_id as store_id','batch_no','tbl_store_lists.store_name')
            ->orderBy('quantity','desc')
           
            ->get();
    }


//     public function pharmacy_item_issuing(Request $items)
//    {
//
//
//        foreach ($items->all() as $request){
//
//
//            $item_id=$request['item_id'];
//            $facility_id=$request['facility_id'];
//            $quantity_issued=$request['quantity_issued'];
//            $issued_store_id=$request['issued_store_id'];
//            $transaction_type_id=$request['transaction_type_id'];
//            $requesting_store_id=$request['issued_store_id'];
//            $batch_no=$request['batch_no'];
//            $received_from_id=$request['received_from_id'];
//            $vendor_id=$request['vendor_id'];
//            $quantityInStore=$request['store_balance'];
//            $expiry_date=$request['expiry_date'];
//            $user_id=$request['user_id'];
//            $invoice_refference=$request['invoice_number'];
//            $price=0;
//            $store_name=$request['store_name'];
//            $internal_issuer_id=$request['internal_issuer_id'];
//            $identifier=$request['identifier'];
//            $adjustment=$request['adjustment'];
//            if ($adjustment=='plus') {
//                $store_type_id = $request['store_type_id'];
//
//            }
//            if ($adjustment=='plus' && $request['user_targeted_id']=='') {
//                return response()->json([
//                    'msg'=>'Choose A User To Receive These Items in '.$store_name,
//                    'status'=>0
//                ]);
//            }
//            if ($adjustment=='minus'){
//                $user_targeted_id=null;
//            }
//            else {
//
//                $user_targeted_id = $request['user_targeted_id'];
//            }
//
//      //checking for duplication entry
//
//
//
//
//
//                //Adjustment control codes
//                if ($adjustment=='minus'){
//
//                    $user_targeted_id = null;
//
//                    $update=Tbl_receiving_item::where('id',$identifier)
//                        ->update(['control'=>'c']);
//
////  main store dealing with item to make adjustment specifically negative adjustment
//                    $newData= Tbl_receiving_item::create([
//                        'item_id'=>$item_id,
//                        'received_store_id'=>$received_from_id,
//                        // 'requesting_store_id'=>$issued_store_id,
//                        'transaction_type_id'=>$transaction_type_id,
//                        'invoice_refference'=>$invoice_refference,
//                        'batch_no'=>$batch_no,
//                         'issued_quantity'=>$quantity_issued,
//                        'facility_id'=>$facility_id,
//                        'quantity'=>$quantityInStore,
//                        'user_id'=>$user_id,
//                        'received_from_id'=>$vendor_id,
//                        'expiry_date'=>$expiry_date,
//                        'price'=>$price,
//                        'control'=>'l',
//
//                    ]);
//                    $oldData=null;
//                    $patient_id=null;
//                    $trackable_id=$newData->id;
//                    SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,$oldData);
//
//                }
//else{
//    if ($adjustment=='whole_sale'){
//        //updating the current available balance control
//
//        $update=Tbl_receiving_item::where('id',$identifier)
//            ->update(['control'=>'c']);
//
////sender main store
//        $data= Tbl_receiving_item::create([
//            'item_id'=>$item_id,
//            'received_store_id'=>$received_from_id,
//            'requesting_store_id'=>$issued_store_id,
//            'transaction_type_id'=>$transaction_type_id,
//            'invoice_refference'=>$invoice_refference,
//            'batch_no'=>$batch_no,
//            'requested_amount'=>$quantity_issued,
//            'issued_quantity'=>$quantity_issued,
//            'facility_id'=>$facility_id,
//            'quantity'=>$quantityInStore,
//            'user_id'=>$user_id,
//            'received_from_id'=>$vendor_id,
//            'expiry_date'=>$expiry_date,
//            'price'=>$price,
//            'control'=>'l',
//            'box'=>$request['box'],
//            'uom'=>$request['pack_size'],
//            'cost'=>$request['cost'],
//            'client_name'=>$request['receiver_name'],
//
//        ]);
//        $oldData=null;
//        $patient_id=null;
//        $trackable_id=$data->id;
//        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);
//
//    }
////if items are issued to another main store
//    else if ($store_type_id==2){
//
//        //updating the current available balance control
//
//        $update=Tbl_receiving_item::where('id',$identifier)
//            ->update(['control'=>'c']);
//
////sender main store
//        $data= Tbl_receiving_item::create([
//            'item_id'=>$item_id,
//            'received_store_id'=>$received_from_id,
//            'requesting_store_id'=>$issued_store_id,
//            'transaction_type_id'=>$transaction_type_id,
//            'invoice_refference'=>$invoice_refference,
//            'batch_no'=>$batch_no,
//            'requested_amount'=>$quantity_issued,
//            'facility_id'=>$facility_id,
//            'quantity'=>$quantityInStore,
//            'user_id'=>$user_id,
//            'received_from_id'=>$vendor_id,
//            'expiry_date'=>$expiry_date,
//            'price'=>$price,
//            'control'=>'l',
//
//        ]);
//        $oldData=null;
//        $patient_id=null;
//        $trackable_id=$data->id;
//        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);
//
//        if($data) {
//
////receiver main store
//            Tbl_receiving_item::create([
//                'item_id' => $item_id,
//                'received_store_id' => $issued_store_id,
//                'requesting_store_id' => $issued_store_id,
//                'transaction_type_id' => $transaction_type_id,
//                'invoice_refference' => $invoice_refference,
//                'batch_no' => $batch_no,
//                'user_targeted_id'=>$user_targeted_id,
//                'facility_id' => $facility_id,
//                'quantity' => $quantity_issued,
//                'user_id' => $user_id,
//                'received_from_id' => $vendor_id,
//                'internal_issuer_id' => $internal_issuer_id,
//                'expiry_date' => $expiry_date,
//                'price' => $price,
//                'control'=>'l',
//            ]);
//
//
//
//        }
//
//        else
//        {
//
//        }
//    }
//
//    //if items are issued to sub store
//    else if ($store_type_id==3){
////updating the current available balance control
//
//        $update=Tbl_receiving_item::where('id',$identifier)
//            ->update(['control'=>'c']);
//
//        $data=  Tbl_receiving_item::create([
//            'item_id'=>$item_id,
//            'received_store_id'=>$received_from_id,
//            'requesting_store_id'=>$issued_store_id,
//            'transaction_type_id'=>$transaction_type_id,
//            'invoice_refference'=>$invoice_refference,
//            'batch_no'=>$batch_no,
//            'issued_quantity'=>$quantity_issued,
//            'facility_id'=>$facility_id,
//            'quantity'=>$quantityInStore,
//            'user_id'=>$user_id,
//            'received_from_id'=>$vendor_id,
//            'expiry_date'=>$expiry_date,
//            'price'=>$price,
//            'control'=>'l',
//        ]);
//        $oldData=null;
//        $patient_id=null;
//        $trackable_id=$data->id;
//        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);
//
//        if($data) {
//            Tbl_sub_store::create([
//                'item_id' => $item_id,
//                'quantity' => $quantity_issued,
//                'received_from_id' => $received_from_id,
//                'issued_store_id' => $issued_store_id,
//                'transaction_type_id' => $transaction_type_id,
//                'batch_no' => $batch_no,
//                'user_targeted_id' =>$user_targeted_id,
//                'control'=>'l',
//            ]);
//
//
//
//        }
//        else {
//
//
//        }
//
//
//    }
//    //if items are issued to dispensing
//    else if ($store_type_id==4 || $store_type_id==5){
//
//
//        //updating the current available balance control
//
//        $update=Tbl_receiving_item::where('id',$identifier)
//            ->update(['control'=>'c']);
//
//        $data= Tbl_receiving_item::create([
//            'item_id'=>$item_id,
//            'received_store_id'=>$received_from_id,
//            'requesting_store_id'=>$issued_store_id,
//            'transaction_type_id'=>$transaction_type_id,
//            'invoice_refference'=>$invoice_refference,
//            'batch_no'=>$batch_no,
//            'issued_quantity'=>$quantity_issued,
//            'facility_id'=>$facility_id,
//            'quantity'=>$quantityInStore,
//            'user_id'=>$user_id,
//            'received_from_id'=>$vendor_id,
//            'expiry_date'=>$expiry_date,
//            'price'=>$price,
//            'control'=>'l',
//
//        ]);
//        $oldData=null;
//        $patient_id=null;
//        $trackable_id=$data->id;
//        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);
//
//        if($data)
//        {
//
//            Tbl_dispenser::create([
//                'item_id'=>$item_id,
//                'transaction_type_dispensed_id'=>$transaction_type_id,
//                'quantity_received'=>$quantity_issued,
//                'received_from_id'=>$received_from_id,
//                'dispenser_id'=>$issued_store_id,
//                'batch_no'=>$batch_no,
//                'user_targeted_id'=>$user_targeted_id,
//                'control'=>'l',
//            ]);
//
//
//
//
//        }
//        else{
//
//
//
//        }
//    }
//}
//
//
//        }
//        return response()->json([
//            'msg'=>'Successful Processed and has send ',
//            'status'=>1
//        ]);
//        }
    public function pharmacy_item_issuing(Request $items)
    {


        foreach ($items->all() as $request){


            $item_id=$request['item_id'];
            $facility_id=$request['facility_id'];
            $quantity_issued=$request['quantity_issued'];
            $issued_store_id=$request['issued_store_id'];
            $transaction_type_id=$request['transaction_type_id'];
            $requesting_store_id=$request['issued_store_id'];
            $batch_no=$request['batch_no'];
            $received_from_id=$request['received_from_id'];
            $vendor_id=$request['vendor_id'];
            $quantityInStore=$request['store_balance'];
            $expiry_date=$request['expiry_date'];
            $user_id=$request['user_id'];
            $invoice_refference=$request['invoice_number'];
            $price=0;
            $store_name=$request['store_name'];
            $internal_issuer_id=$request['internal_issuer_id'];
            $identifier=$request['identifier'];
            $adjustment=$request['adjustment'];
            if ($adjustment=='plus') {
                $store_type_id = $request['store_type_id'];

            }
            if ($adjustment=='plus' && $request['user_targeted_id']=='') {
                return response()->json([
                    'msg'=>'Choose A User To Receive These Items in '.$store_name,
                    'status'=>0
                ]);
            }
            if ($adjustment=='minus'){
                $user_targeted_id=null;
            }
            else {

                $user_targeted_id = $request['user_targeted_id'];
            }

            //checking for duplication entry





            //Adjustment control codes
            if ($adjustment=='minus'){

                $user_targeted_id = null;

                $update=Tbl_receiving_item::where('id',$identifier)
                    ->update(['control'=>'c']);

//  main store dealing with item to make adjustment specifically negative adjustment
                $newData= Tbl_receiving_item::create([
                    'item_id'=>$item_id,
                    'received_store_id'=>$received_from_id,
                    // 'requesting_store_id'=>$issued_store_id,
                    'transaction_type_id'=>$transaction_type_id,
                    'invoice_refference'=>$invoice_refference,
                    'batch_no'=>$batch_no,
                    'issued_quantity'=>$quantity_issued,
                    'facility_id'=>$facility_id,
                    'quantity'=>$quantityInStore,
                    'user_id'=>$user_id,
                    'received_from_id'=>$vendor_id,
                    'expiry_date'=>$expiry_date,
                    'price'=>$price,
                    'control'=>'l',

                ]);
                $oldData=null;
                $patient_id=null;
                $trackable_id=$newData->id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,$oldData);

            }

//if items are issued to another main store
            else if ($store_type_id==2){

                //updating the current available balance control

                $update=Tbl_receiving_item::where('id',$identifier)
                    ->update(['control'=>'c']);

//sender main store
                $data= Tbl_receiving_item::create([
                    'item_id'=>$item_id,
                    'received_store_id'=>$received_from_id,
                    'requesting_store_id'=>$issued_store_id,
                    'transaction_type_id'=>$transaction_type_id,
                    'invoice_refference'=>$invoice_refference,
                    'batch_no'=>$batch_no,
                    'requested_amount'=>$quantity_issued,
                    'facility_id'=>$facility_id,
                    'quantity'=>$quantityInStore,
                    'user_id'=>$user_id,
                    'received_from_id'=>$vendor_id,
                    'expiry_date'=>$expiry_date,
                    'price'=>$price,
                    'control'=>'l',

                ]);
                $oldData=null;
                $patient_id=null;
                $trackable_id=$data->id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);

                if($data) {

//receiver main store
                    Tbl_receiving_item::create([
                        'item_id' => $item_id,
                        'received_store_id' => $issued_store_id,
                        'requesting_store_id' => $issued_store_id,
                        'transaction_type_id' => $transaction_type_id,
                        'invoice_refference' => $invoice_refference,
                        'batch_no' => $batch_no,
                        'user_targeted_id'=>$user_targeted_id,
                        'facility_id' => $facility_id,
                        'quantity' => $quantity_issued,
                        'user_id' => $user_id,
                        'received_from_id' => $vendor_id,
                        'internal_issuer_id' => $internal_issuer_id,
                        'expiry_date' => $expiry_date,
                        'price' => $price,
                        'control'=>'l',
                    ]);



                }

                else
                {

                }
            }

            //if items are issued to sub store
            else if ($store_type_id==3){
//updating the current available balance control

                $update=Tbl_receiving_item::where('id',$identifier)
                    ->update(['control'=>'c']);

                $data=  Tbl_receiving_item::create([
                    'item_id'=>$item_id,
                    'received_store_id'=>$received_from_id,
                    'requesting_store_id'=>$issued_store_id,
                    'transaction_type_id'=>$transaction_type_id,
                    'invoice_refference'=>$invoice_refference,
                    'batch_no'=>$batch_no,
                    'issued_quantity'=>$quantity_issued,
                    'facility_id'=>$facility_id,
                    'quantity'=>$quantityInStore,
                    'user_id'=>$user_id,
                    'received_from_id'=>$vendor_id,
                    'expiry_date'=>$expiry_date,
                    'price'=>$price,
                    'control'=>'l',
                ]);
                $oldData=null;
                $patient_id=null;
                $trackable_id=$data->id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);

                if($data) {
                    Tbl_sub_store::create([
                        'item_id' => $item_id,
                        'quantity' => $quantity_issued,
                        'received_from_id' => $received_from_id,
                        'issued_store_id' => $issued_store_id,
                        'transaction_type_id' => $transaction_type_id,
                        'batch_no' => $batch_no,
                        'user_targeted_id' =>$user_targeted_id,
                        'control'=>'l',
                    ]);



                }
                else {


                }


            }
            //if items are issued to dispensing
            else if ($store_type_id==4 || $store_type_id==5){


                //updating the current available balance control

                $update=Tbl_receiving_item::where('id',$identifier)
                    ->update(['control'=>'c']);

                $data= Tbl_receiving_item::create([
                    'item_id'=>$item_id,
                    'received_store_id'=>$received_from_id,
                    'requesting_store_id'=>$issued_store_id,
                    'transaction_type_id'=>$transaction_type_id,
                    'invoice_refference'=>$invoice_refference,
                    'batch_no'=>$batch_no,
                    'issued_quantity'=>$quantity_issued,
                    'facility_id'=>$facility_id,
                    'quantity'=>$quantityInStore,
                    'user_id'=>$user_id,
                    'received_from_id'=>$vendor_id,
                    'expiry_date'=>$expiry_date,
                    'price'=>$price,
                    'control'=>'l',

                ]);
                $oldData=null;
                $patient_id=null;
                $trackable_id=$data->id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);

                if($data)
                {

                    Tbl_dispenser::create([
                        'item_id'=>$item_id,
                        'transaction_type_dispensed_id'=>$transaction_type_id,
                        'quantity_received'=>$quantity_issued,
                        'received_from_id'=>$received_from_id,
                        'dispenser_id'=>$issued_store_id,
                        'batch_no'=>$batch_no,
                        'user_targeted_id'=>$user_targeted_id,
                        'control'=>'l',
                    ]);




                }
                else{



                }
            }
        }
        return response()->json([
            'msg'=>'Successful Processed and has send ',
            'status'=>1
        ]);
    }



    public function Order_processing(Request $request)
    {
//return $request->all();
        $item_id=$request['item_id'];
        $requesting_store_id=$request['issued_store_id'];
        $requested_store_id=$request['received_from_id'];
        $quantity_issued=$request['quantity_issued'];
        $store_balance_remained=$request['store_balance'];
        $transaction_type_id=$request['transaction_type_id'];
        $user_id=$request['user_id'];
        $batch_no=$request['batch_no'];
        $facility_id=$request['facility_id'];
        $store_name=$request['store_name'];
        $request_id=$request['request_id'];
        $order_no=$request['order_no'];
        $vendor=$request['vendor'];
        $expiry_date=$request['expiry_date'];
        $identifier=$request['identifier'];

        $invoice_refference=$request['invoice_refference'];
        $requested_store_type_id=$request['requested_store_type_id'];
        $requesting_store_type_id=$request['requesting_store_type_id'];

if ($requested_store_type_id==2 && $requesting_store_type_id==3){

    // processing order of request from sub store to main store
    //updating the current available balance control

    $update=Tbl_receiving_item::where('id',$identifier)
        ->update(['control'=>'c']);

    $subStore=Tbl_sub_store::where('id',$order_no)->update([
        'received_from_id'=>$requested_store_id,
        'quantity'=>$quantity_issued,
        'transaction_type_id'=>$transaction_type_id,
        'transaction_type_id'=>$transaction_type_id,
        'request_status_id'=>1,
        'batch_no'=>$batch_no,
        'control'=>'l',
    ]);

    $mainStore=Tbl_receiving_item::where('id',$request_id)->update([
        'issued_quantity'=>$quantity_issued,
        'quantity'=>$store_balance_remained,
        'batch_no'=>$batch_no,
        'expiry_date'=>$expiry_date,
        'transaction_type_id'=>$transaction_type_id,
        'user_id'=>$user_id,
        'requesting_store_id'=>$requesting_store_id,
        'request_status_id'=>1,
        'received_store_id'=>$requested_store_id,
        'invoice_refference'=>$invoice_refference,
        'received_from_id'=>$vendor,
        'control'=>'l',
    ]);
    $newData=Tbl_receiving_item::where('id',$request_id)->get();
    $oldData=null;
    $patient_id=null;
    $trackable_id=$request_id;
    SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,$oldData);




    return response()->json([
        'msg'=>'Order Successful Processed and has send to '. $store_name,
        'status'=>1
    ]);


}
else if ($requested_store_type_id==2 && $requesting_store_type_id==4){

    // processing order of request from dispensing store to main store
    $update=Tbl_receiving_item::where('id',$identifier)
        ->update(['control'=>'c']);

    //store made order
    $dispensing=Tbl_dispenser::where('id',$order_no)->update([

        'quantity_received'=>$quantity_issued,
        'batch_no'=>$batch_no,
        'transaction_type_dispensed_id'=>$transaction_type_id,
        'user_id'=>$user_id,
        'dispensing_status_id'=>4,
        'received_from_id'=>$requested_store_id,
        'control'=>'l',
    ]);

    //store receiving and responding order

    $mainStore=Tbl_receiving_item::where('id',$request_id)->update([
        'issued_quantity'=>$quantity_issued,
        'quantity'=>$store_balance_remained,
        'batch_no'=>$batch_no,
        'expiry_date'=>$expiry_date,
        'transaction_type_id'=>$transaction_type_id,
        'user_id'=>$user_id,
        'requesting_store_id'=>$requesting_store_id,
        'request_status_id'=>1,
        'received_store_id'=>$requested_store_id,
        'invoice_refference'=>$invoice_refference,
        'received_from_id'=>$vendor,
        'control'=>'l',
    ]);
    $newData=Tbl_receiving_item::where('id',$request_id)->get();
    $oldData=null;
    $patient_id=null;
    $trackable_id=$request_id;
    SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,$oldData);




    return response()->json([
        'msg'=>'Order Successful Processed and has send to '. $store_name,
        'status'=>1
    ]);


}else if ($requested_store_type_id==2 && $requesting_store_type_id==2){

    // processing order of request from main store to main store
    $update=Tbl_receiving_item::where('id',$identifier)
        ->update(['control'=>'c']);


    $mainStore=Tbl_receiving_item::where('id',$order_no)->update([
        'issued_quantity'=>$quantity_issued,
        'quantity'=>$quantity_issued,
        'batch_no'=>$batch_no,
        'expiry_date'=>$expiry_date,
        'transaction_type_id'=>$transaction_type_id,
        'user_id'=>$user_id,
       // 'requesting_store_id'=>$requesting_store_id,
        'request_status_id'=>1,
        'received_store_id'=>$requesting_store_id,
        'received_from_id'=>$vendor,
        'invoice_refference'=>$invoice_refference,
        'control'=>'l',
    ]);

    $mainStore=Tbl_receiving_item::where('id',$request_id)->update([
        'issued_quantity'=>$quantity_issued,
        'quantity'=>$store_balance_remained,
        'batch_no'=>$batch_no,
        'expiry_date'=>$expiry_date,
        'transaction_type_id'=>$transaction_type_id,
        'user_id'=>$user_id,
        'requesting_store_id'=>$requesting_store_id,
        'request_status_id'=>1,
        'received_store_id'=>$requested_store_id,
        'invoice_refference'=>$invoice_refference,
        'received_from_id'=>$vendor,
        'control'=>'l',
    ]);

    $newData=Tbl_receiving_item::where('id',$request_id)->get();
    $oldData=null;
    $patient_id=null;
    $trackable_id=$request_id;
    SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,$oldData);



    return response()->json([
        'msg'=>'Order Successful Processed and has send to '. $store_name,

        'status'=>1
    ]);


}


    }



     public function main_store_item_ordering(Request $items)
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

            if(patientRegistration::duplicate('tbl_receiving_items',['item_id','requested_amount','received_store_id','user_id','request_status_id', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$item_id,$quantity,$request_receiver,$user_id,4
                ])==true){
            }


            if($request_sender_type==2 && $request_receiver_type==2 ) {
                //if request is to main store from main store


                $mainstore = Tbl_receiving_item::create([
                    'item_id' => $item_id,
                    'requested_amount' => $quantity,
                    //'requesting_store_id' => $request_sender,
                    'received_store_id' => $request_receiver,
                    'facility_id' => $facility_id,
                    'user_id' => $user_id,

                    'request_status_id' => 4,
                    'control'=>'o',
                ]);
                $order_id = $mainstore->id;
                $newData=$mainstore;
                $oldData=null;
                $patient_id=null;
                $trackable_id=$order_id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,$oldData);

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
//                    return response()->json([
//                        'msg' => 'Request has to failed send to' . $receiver_name,
//                        'status' => 0
//                    ]);
                }

            }
            else{

            }

    }
        return response()->json([
            'msg' => 'Request has send to' . $receiver_name,
            'status' => 1
        ]);
    }

    public function tracerMapping(Request $request)
    {
		if($request->has('save_status')){
			foreach ($request->save_status as $tracers)
				Tbl_tracer_medicine::where('id',$tracers['id'])
							->update(['status'=>$tracers['status']]);
			
			return response()->json([
					'msg' => 'Status successfully set',
					'status' => 1]);
				
		}elseif($request->has('save_mapping')){
			foreach($request->save_mapping as $mapping)
				Tbl_tracer_medicine_mapping::create($mapping);
			return response()->json(['msg'=>'Item(s) successfully added under the category']);
        }elseif($request->has('remove_mapping')){
			Tbl_tracer_medicine_mapping::where('id', $request->remove_mapping['id'])->delete();
			return response()->json(['msg'=>'Item removed under the category']);
		}
    }
	
	public function save_tracer_medicine_status(Request $request)
    {
		foreach ($request->tracer_medicine_status as $tracers)
			Tbl_tracer_medicine::where('id',$tracers['id'])
						->update(['status'=>$tracers['status']]);
        
		return response()->json([
                'msg' => 'Status successfully set',
                'status' => 1
            ]);

    }

    public function expired($facility_code)
    {


        return $expiry_date =   DB::select(DB::raw("SELECT item_name,expiry_date,quantity,batch_no, timestampdiff(month,CURDATE(),expiry_date) as expirydate FROM `tbl_receiving_items` inner join `tbl_items` on `tbl_receiving_items`.item_id=`tbl_items`.id WHERE facility_id='".$facility_code."' AND (control='l' AND quantity >0) AND timestampdiff(month,CURDATE(),expiry_date)<0"));


    }
    public function tracer_medicines_report(Request $request)
    {
        $facility_id=$request['facility_id'];
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }
		foreach(Tbl_tracer_medicine::all() as $tracer){
			if($tracer->status == 0){//Not applicable to facility
				$response[] = (Object)array(
									"item_name"=>$tracer->item_name,
									"service_provision"=>0,
									"status"=>'',
									"stock_out_flag"=>'',
									);
				continue;
			}
			//From here, the service is marked as provided at facility. Check other things
			//Check if any of the matched items have balance
			$matched = DB::select("select '".$tracer->item_name."' as item_name,
			'".$tracer->status."' as service_provision, quantity, timestampdiff(day,tbl_receiving_items.updated_at,'{$end_date}') as days
			FROM tbl_receiving_items INNER JOIN tbl_tracer_medicine_mappings ON tbl_tracer_medicine_mappings.tracer_medicine_id='".$tracer->id."' and tbl_tracer_medicine_mappings.item_id= tbl_receiving_items.item_id
			WHERE tbl_receiving_items.control='l' AND tbl_receiving_items.facility_id='{$facility_id}' AND date(tbl_receiving_items.updated_at) <= date('{$end_date}') order by quantity desc, days asc");
			
			if(count($matched) == 0){
				$response[] = (Object)array(
									"item_name"=>$tracer->item_name,
									"service_provision"=>1,
									"status"=>0,//no stock entry
									"stock_out_flag"=>'C',//by virtue
									);
				continue;
			}else{
				$found = false;
				foreach($matched as $pharmacy_record){
					if($pharmacy_record->quantity > 0){//there is balance
						$response[] = (Object)array(
									"item_name"=>$tracer->item_name,
									"service_provision"=>1,
									"status"=>1,
									"stock_out_flag"=>'',
									);
						$found = true;//item found and there is balance too
						break;
					}
				}
				if(!$found)//for this tracer, no balance found
					$response[] = (Object)array(
									"item_name"=>$tracer->item_name,
									"service_provision"=>1,
									"status"=>0,
									"stock_out_flag"=>($pharmacy_record->days < 7 ? 'A' : ($pharmacy_record->days < 29 ? 'B' : 'C')),
									);
				}
		}
        return $response;
    }


    public function ledger(Request $request)
    {
        $facility_id=$request['facility_id'];
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }
		$store_id=$request->store_id;
        //return DB::table('vw_ledger_book')->get();
        $ledger=[];
        $recieved=DB::table('tbl_receiving_items')-> join('tbl_transaction_types','tbl_transaction_types.id','=','tbl_receiving_items.transaction_type_id')
            ->where('control_in','r')
            ->where('facility_id',$facility_id)
             ->whereBetween('tbl_receiving_items.created_at',[$start_date,$end_date])
            ->select('tbl_receiving_items.created_at','batch_no',DB::raw('sum(quantity) as kilichopokelewa'),'tbl_receiving_items.item_id') ->groupBy('batch_no')->get();
        $issued=DB::table('tbl_receiving_items')-> join('tbl_transaction_types','tbl_transaction_types.id','=','tbl_receiving_items.transaction_type_id')
            ->where('adjustment','plus')
            ->where('facility_id',$facility_id)
            ->whereBetween('tbl_receiving_items.created_at',[$start_date,$end_date])
            ->select('batch_no', DB::raw('sum(issued_quantity) as kilichotolewa'),'tbl_receiving_items.item_id')  ->groupBy('batch_no')->get();

        $marekebisho=DB::table('tbl_receiving_items')-> join('tbl_transaction_types','tbl_transaction_types.id','=','tbl_receiving_items.transaction_type_id')
            // ->where('control_in','!=','r')
            ->where('adjustment','minus')
            ->where('facility_id',$facility_id)
            ->whereBetween('tbl_receiving_items.created_at',[$start_date,$end_date])
            ->select('batch_no',DB::raw('sum(issued_quantity) as marekebisho'),'tbl_receiving_items.item_id')  ->groupBy('batch_no')->get();
        $kilichopo=DB::table('tbl_receiving_items')-> join('tbl_transaction_types','tbl_transaction_types.id','=','tbl_receiving_items.transaction_type_id')
            ->where('control','l')
            ->where('facility_id',$facility_id)
            ->whereBetween('tbl_receiving_items.created_at',[$start_date,$end_date])
            ->select('batch_no',DB::raw('sum(quantity) as kilichopo'),'tbl_receiving_items.item_id') ->groupBy('batch_no')->get();
        $ledger[0]=$recieved;
        $ledger[1]=$issued;
        $ledger[2]=$marekebisho;
        $ledger[3]=$kilichopo;
        $ledger[1];
        foreach ($ledger[0] as $kilichopokelewa){
            Tbl_receiving_item::where('batch_no',$kilichopokelewa->batch_no)
                ->where('control_in','r')
                ->where('item_id',$kilichopokelewa->item_id)
                ->update([
                    'quantity'=>$kilichopokelewa->kilichopokelewa,
                ]);
        }
        foreach ($ledger[1] as $kilichotolewa){
            Tbl_receiving_item::where('batch_no',$kilichotolewa->batch_no)
                ->where('control_in','r')
                ->where('item_id',$kilichotolewa->item_id)
                ->update([
                    'amount_issued'=>$kilichotolewa->kilichotolewa,
                    'positive_adjustment'=>$kilichotolewa->kilichotolewa,
                ]);
        }
        foreach ($ledger[2] as $marekebisho){
            Tbl_receiving_item::where('batch_no',$marekebisho->batch_no)
                ->where('control_in','r')
				->where('item_id',$marekebisho->item_id)
                ->update([
                    'negative_adjustment'=>$marekebisho->marekebisho,
                ]);
            }
        foreach ($ledger[3] as $amount_available){
            Tbl_receiving_item::where('batch_no',$amount_available->batch_no)
                ->where('control_in','r')
				->where('item_id',$amount_available->item_id)
                ->update([
                    'amount_available'=>$amount_available->kilichopo,
                ]);
        }
        $recieved=DB::table('tbl_receiving_items')
		->join('tbl_items','tbl_items.id','=','tbl_receiving_items.item_id')
            ->where('control_in','r')
            ->where('facility_id',$facility_id)
            ->where('received_store_id',$store_id)
            ->whereBetween('tbl_receiving_items.created_at',[$start_date,$end_date])
            ->select(DB::Raw('date(tbl_receiving_items.created_at) created_at'),'item_name','batch_no','quantity as kilichopokelewa','amount_available as kilichopo','amount_issued as kilichotolewa','negative_adjustment as marekebisho','item_id')
            ->groupBy('batch_no')
            ->orderBy('tbl_receiving_items.created_at','desc')
            ->get();
        return $recieved;

    }
    public function issue_voucher(Request $request)
    {

        $facility_id=$request['facility_id'];
        $user_id=$request['user_id'];
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];

        return DB::select(DB::raw("SELECT item_name,store_name,issued_quantity as quantity ,tbl_receiving_items.created_at,(select t.store_name  from tbl_store_lists  as t where t.id= tbl_receiving_items.received_store_id) as main_store_name,(select t1.name  from users  as t1 where t1.id= tbl_receiving_items.user_id) as name from tbl_receiving_items INNER join tbl_store_lists on tbl_store_lists.id= tbl_receiving_items.requesting_store_id 
   INNER join tbl_items on tbl_items.id= tbl_receiving_items.item_id 
   INNER join tbl_user_store_configurations on tbl_user_store_configurations.store_id= tbl_receiving_items.received_store_id 
   INNER join users on tbl_user_store_configurations.user_id= users.id
     WHERE issued_quantity is NOT null AND tbl_user_store_configurations.user_id='".$user_id."'  AND 
tbl_receiving_items.facility_id='".$facility_id."' AND (tbl_receiving_items.created_at BETWEEN '{$start_date}' AND '{$end_date}' ) group by tbl_receiving_items.id "));

    }

    public function received_voucher(Request $request)
    {
        $facility_id=$request['facility_id'];
        $user_id=$request['user_id'];
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        return DB::select(DB::raw("SELECT store_name,quantity,tbl_receiving_items.created_at,item_name,tbl_invoices.invoice_number,(select t1.name  from users  as t1 where t1.id= tbl_receiving_items.user_id) as name from tbl_receiving_items INNER join tbl_store_lists on tbl_store_lists.id= tbl_receiving_items.received_store_id 
   INNER join tbl_items on tbl_items.id= tbl_receiving_items.item_id  INNER join tbl_user_store_configurations on tbl_user_store_configurations.store_id= tbl_receiving_items.received_store_id 
   INNER join users on tbl_user_store_configurations.user_id= users.id
    inner join tbl_invoices on tbl_invoices.id=tbl_receiving_items.invoice_refference
     WHERE control_in ='r' AND tbl_receiving_items.facility_id= '".$facility_id."' AND tbl_user_store_configurations.user_id='".$user_id."'
     AND (tbl_receiving_items.created_at BETWEEN '".$start_date."' AND '".$end_date."' )  group by tbl_receiving_items.id "));

    }

    public function issued_store_voucher_list($user_id)
    {

        return DB::table('tbl_receiving_items')
            ->join('tbl_user_store_configurations', 'tbl_receiving_items.user_id', '=', 'tbl_user_store_configurations.user_id')
            ->join('tbl_store_lists', 'tbl_receiving_items.requesting_store_id', '=', 'tbl_store_lists.id')
            ->select('tbl_store_lists.id', 'tbl_store_lists.store_name')
            ->where('tbl_user_store_configurations.status', '=', 1)
            //->where('tbl_user_store_configurations.user_id', '=', $user_id)
            ->where('tbl_receiving_items.issued_quantity', '!=', NULL)
            ->groupBy('tbl_store_lists.id')
            ->orderBy('tbl_receiving_items.created_at', 'desc')
            ->get();
    }
        public function loadStoreVoucherDates($store_id)
    {

//        return DB::table('tbl_receiving_items')
//          ->  join('tbl_user_store_configurations','tbl_receiving_items.user_id','=','tbl_user_store_configurations.user_id')
//          ->  join('tbl_store_lists','tbl_receiving_items.requesting_store_id','=','tbl_store_lists.id')
//            ->select('tbl_store_lists.id as store_id',('tbl_receiving_items.created_at as issued_date'),'tbl_store_lists.store_name')
//            ->where('tbl_user_store_configurations.status','=',1)
//            ->where('tbl_receiving_items.requesting_store_id','=',$store_id)
//            ->where('tbl_receiving_items.issued_quantity','!=',NULL)
//           // ->groupBy('tbl_store_lists.id')
//           ->orderBy('tbl_receiving_items.created_at','desc')
//            ->get();

            return  DB::select("SELECT t3.id as store_id,DATE(t1.created_at) AS issued_date,store_name  FROM tbl_receiving_items  t1
join tbl_user_store_configurations t2 on t1.user_id=t2.user_id 
join tbl_store_lists t3 on t1.requesting_store_id=t3.id where t1.issued_quantity IS NOT NULL AND requesting_store_id='".$store_id."' GROUP BY DATE(t1.created_at) ORDER BY DATE(t1.created_at) DESC");


        }
    public function ViewVoucherDetails(Request $request)
    {

   $dated=Date($request->dated);
        $store_id=$request->store_id;
       //return  DB::select("SELECT  t1.created_at as vourcher_no,t2.item_name,t1.issued_quantity as issued,t1.requested_amount as required,t3.name as issuer FROM tbl_receiving_items  t1 join tbl_items t2 on t1.item_id=t2.id join users t3 on t1.user_id=t3.id WHERE t1.issued_quantity IS NOT NULL AND t1.created_at LIKE '%$dated%' AND requesting_store_id='".$store_id."' GROUP BY t1.id");
        return  DB::select("SELECT  t1.created_at as vourcher_no,t2.item_name,sum(t1.issued_quantity) as issued,sum(t1.requested_amount) as required,t3.name as issuer FROM tbl_receiving_items  t1 join tbl_items t2 on t1.item_id=t2.id join users t3 on t1.user_id=t3.id WHERE t1.issued_quantity IS NOT NULL AND t1.created_at LIKE '%$dated%' AND requesting_store_id='".$store_id."' GROUP BY t1.item_id");

    }

    public function LoadbinCardData(Request $request)
    {

if (empty($request['store_id'] )){
    return null;
}
        $store_id=$request->store_id;
       //return  DB::select("SELECT  t1.created_at as vourcher_no,t2.item_name,t1.issued_quantity as issued,t1.requested_amount as required,t3.name as issuer FROM tbl_receiving_items  t1 join tbl_items t2 on t1.item_id=t2.id join users t3 on t1.user_id=t3.id WHERE t1.issued_quantity IS NOT NULL AND t1.created_at LIKE '%$dated%' AND requesting_store_id='".$store_id."' GROUP BY t1.id");
        return  DB::select("SELECT  t2.*,t3.item_code FROM tbl_receiving_items  t1 join tbl_items t2 on t1.item_id=t2.id
 join tbl_item_type_mappeds t3 on t3.item_id=t2.id
 where received_store_id=$store_id  GROUP BY t1.item_id");

    }

    public function single_item_issue_voucher(Request $request)
    {
       $item_id=$request['item_id'];
       $store_id=$request['store_id'];
        return  DB::select("SELECT ifnull((CASE WHEN control='l' THEN SUM(quantity) END),0) as balanced, date(created_at) as date,quantity as received,issued_quantity as issued,quantity as balance,expiry_date,control,control_in FROM tbl_receiving_items  where item_id=  $item_id AND received_store_id=$store_id AND (issued_quantity IS NOT NULL OR control_in='r') GROUP BY id order by id asc");

    }
    public function mark_pos_dispensing(Request $request)
    {

       $store_id=$request['store_id'];
       $mark=$request['status'];

       $check=Tbl_pos_dispensing::where('store_id',$store_id)->take(1)->get();
       if ($mark==1){// checking if action is for marking it or unmarking
           // checking if store already exist or not
           if (count($check)==0){
               Tbl_pos_dispensing::create($request->all());
               return response()->json([
                   'msg' => 'Store has marked as Point of Sale Balance Check Point' ,
                   'status' => 1
               ]);
           }
           else{

               Tbl_pos_dispensing::where('store_id',$store_id)->update([
                   'status'=>$mark
               ]);
               return response()->json([
                   'msg' => 'Store has marked as Point of Sale Balance Check Point' ,
                   'status' => 1
               ]);
           }
       }
       else{

               Tbl_pos_dispensing::where('store_id',$store_id)->update([
                   'status'=>$mark
               ]);
               return response()->json([
                   'msg' => 'Store has unmarked/disabled from OPd dispensing balance check point' ,
                   'status' => 0
               ]);


       }



    }

    public function dispensed_group_control_list()
    {
      return DB::select("SELECT code,item_name FROM tbl_dispensed_group_controls");
    }
    public function dispensed_groups()
    {
      return DB::select("SELECT t2.id, t2.identifier as code,t1.item_name FROM tbl_items t1 Join tbl_dispensed_groups t2  on t2.item_id= t1.id");
    }
	public function loadTracers(Request $request){
		return Tbl_tracer_medicine::with('mappings.item')->get();
	}

 public function RnRSearchold(Request $request)
    {
       /* $facility_id=$request['facility_id'];
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }
		$store_id=$request->store_id;
        //return DB::table('vw_ledger_book')->get();
        $ledger=[];
        $recieved=DB::table('tbl_receiving_items')-> join('tbl_transaction_types','tbl_transaction_types.id','=','tbl_receiving_items.transaction_type_id')
            ->where('control_in','r')
            ->where('facility_id',$facility_id)
             ->whereBetween('tbl_receiving_items.created_at',[$start_date,$end_date])
            ->select('tbl_receiving_items.created_at','batch_no',DB::raw('sum(quantity) as kilichopokelewa'),'tbl_receiving_items.item_id') ->groupBy('batch_no')->get();
        $issued=DB::table('tbl_receiving_items')-> join('tbl_transaction_types','tbl_transaction_types.id','=','tbl_receiving_items.transaction_type_id')
            ->where('adjustment','plus')
            ->where('facility_id',$facility_id)
            ->whereBetween('tbl_receiving_items.created_at',[$start_date,$end_date])
            ->select('batch_no', DB::raw('sum(issued_quantity) as kilichotolewa'),'tbl_receiving_items.item_id')  ->groupBy('batch_no')->get();

        $marekebisho=DB::table('tbl_receiving_items')-> join('tbl_transaction_types','tbl_transaction_types.id','=','tbl_receiving_items.transaction_type_id')
            // ->where('control_in','!=','r')
            ->where('adjustment','minus')
            ->where('facility_id',$facility_id)
            ->whereBetween('tbl_receiving_items.created_at',[$start_date,$end_date])
            ->select('batch_no',DB::raw('sum(issued_quantity) as marekebisho'),'tbl_receiving_items.item_id')  ->groupBy('batch_no')->get();
        $kilichopo=DB::table('tbl_receiving_items')-> join('tbl_transaction_types','tbl_transaction_types.id','=','tbl_receiving_items.transaction_type_id')
            ->where('control','l')
            ->where('facility_id',$facility_id)
            ->whereBetween('tbl_receiving_items.created_at',[$start_date,$end_date])
            ->select('batch_no',DB::raw('sum(quantity) as kilichopo'),'tbl_receiving_items.item_id') ->groupBy('batch_no')->get();
        $ledger[0]=$recieved;
        $ledger[1]=$issued;
        $ledger[2]=$marekebisho;
        $ledger[3]=$kilichopo;
        $ledger[1];
        foreach ($ledger[0] as $kilichopokelewa){
            Tbl_receiving_item::where('batch_no',$kilichopokelewa->batch_no)
                ->where('control_in','r')
                ->where('item_id',$kilichopokelewa->item_id)
                ->update([
                    'quantity'=>$kilichopokelewa->kilichopokelewa,
                ]);
        }
        foreach ($ledger[1] as $kilichotolewa){
            Tbl_receiving_item::where('batch_no',$kilichotolewa->batch_no)
                ->where('control_in','r')
                ->where('item_id',$kilichotolewa->item_id)
                ->update([
                    'amount_issued'=>$kilichotolewa->kilichotolewa,
                    'positive_adjustment'=>$kilichotolewa->kilichotolewa,
                ]);
        }
        foreach ($ledger[2] as $marekebisho){
            Tbl_receiving_item::where('batch_no',$marekebisho->batch_no)
                ->where('control_in','r')
				->where('item_id',$marekebisho->item_id)
                ->update([
                    'negative_adjustment'=>$marekebisho->marekebisho,
                ]);
            }
        foreach ($ledger[3] as $amount_available){
            Tbl_receiving_item::where('batch_no',$amount_available->batch_no)
                ->where('control_in','r')
				->where('item_id',$amount_available->item_id)
                ->update([
                    'amount_available'=>$amount_available->kilichopo,
                ]);
        }
        $recieved=DB::table('tbl_receiving_items')
		->join('tbl_items','tbl_items.id','=','tbl_receiving_items.item_id')
            ->where('control_in','r')
            ->where('facility_id',$facility_id)
            ->where('received_store_id',$store_id)
            ->whereBetween('tbl_receiving_items.created_at',[$start_date,$end_date])
            ->select(DB::Raw('date(tbl_receiving_items.created_at) created_at'),'item_name','batch_no','quantity as kilichopokelewa','amount_available as kilichopo','amount_issued as kilichotolewa','negative_adjustment as marekebisho','item_id')
            ->groupBy('batch_no')
            ->orderBy('tbl_receiving_items.created_at','desc')
            ->get();
        return $recieved;
*/
    }

    public function dispensingBackup()
    {

         $rec=Tbl_dispenser::where('control','=','c')->get();
        foreach ($rec as $data){
$ceheck=Tbl_dispensing::where("id",$data->id)->get();
if (count($ceheck)==0){
    $ente=  Tbl_dispensing::create(
        [
            'id'=>$data->id,
            'received_from_id'=>$data->received_from_id,
            'batch_no'=>$data->batch_no,
            'patient_id'=>$data->patient_id,
            'request_amount'=>$data->request_amount,
            'user_id'=>$data->user_id,
            'item_id'=>$data->item_id,
            'quantity_received'=>$data->quantity_received,
            'transaction_type_dispensed_id'=>$data->transaction_type_dispensed_id,
            'dispenser_id'=>$data->dispenser_id,
            'dispensing_status_id'=>$data->dispensing_status_id,
            'quantity_dispensed'=>$data->quantity_dispensed,
            'control'=>$data->control,
            'created_at'=>$data->created_at,
            'updated_at'=>$data->updated_at,
        ]
    );
}


        }
        return "<hr><h1>  <b style='align:center'> Success..</b></h1><hr>";

    }



    public function stock_reconsilliation(Request $request)
    {
      $send=$request->all()[0]['store_type_id'];
      if($send==2) {
          foreach ($request->all() as $rec){

              $item_id = $rec['item_id'];
          $column_id = $rec['column_id'];
          $old_quantity = $rec['old_quantity'];
          $current_quantity = $rec['current_quantity'];
          if (patientRegistration::duplicate('tbl_stock_reconsilliations', ['item_id', 'column_id', 'old_quantity', 'current_quantity', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=1))'],
                  [$item_id, $column_id, $old_quantity, $current_quantity,
                  ]) == true) {
          } else {
              Tbl_receiving_item::where('id', $column_id)->update([
                  'quantity' => $current_quantity
              ]);
              Tbl_stock_reconsilliation::create($rec);
          }
      }

          return response()->json([
              'msg'=>"Reconciliation Done",
              "status"=>1
          ]);
      }
      if ($send==3){
          foreach ($request->all() as $rec){

              $item_id = $rec['item_id'];
              $column_id = $rec['column_id'];
              $old_quantity = $rec['old_quantity'];
              $current_quantity = $rec['current_quantity'];
              if (patientRegistration::duplicate('tbl_stock_reconsilliations', ['item_id', 'column_id', 'old_quantity', 'current_quantity', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=1))'],
                      [$item_id, $column_id, $old_quantity, $current_quantity,
                      ]) == true) {
              } else {
                  Tbl_sub_store::where('id', $column_id)->update([
                      'quantity' => $current_quantity
                  ]);
                  Tbl_stock_reconsilliation::create($rec);
              }
          }
          return response()->json([
              'msg'=>"Reconciliation Done",
              "status"=>1
          ]);
      }
      if ($send==4){
          foreach ($request->all() as $rec){

              $item_id = $rec['item_id'];
              $column_id = $rec['column_id'];
              $old_quantity = $rec['old_quantity'];
              $current_quantity = $rec['current_quantity'];
              if (patientRegistration::duplicate('tbl_stock_reconsilliations', ['item_id', 'column_id', 'old_quantity', 'current_quantity', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=1))'],
                      [$item_id, $column_id, $old_quantity, $current_quantity,
                      ]) == true) {
              } else {
                  Tbl_dispenser::where('id', $column_id)->update([
                      'quantity_received' => $current_quantity
                  ]);
                  Tbl_stock_reconsilliation::create($rec);
              }
          }
          return response()->json([
              'msg'=>"Reconciliation Done",
              "status"=>1
          ]);
      }

    }

    public function getStockReconcilliated(Request $request)
    {
        $start=$request->input('start_date');
        $end=$request->input('end_date');
       return DB::select("SELECT t1.*,t2.item_name,t3.name as user_name,t4.store_name FROM tbl_stock_reconsilliations t1 
join tbl_items t2 on t1.item_id=t2.id
join users t3 on t1.user_id=t3.id
join tbl_store_lists t4 on t1.store_id=t4.id
where t1.created_at between '".$start."' and '".$end."' order by item_name,t1.created_at asc

 ");
    }

    public function returnStockReconcilliated(Request $request)
    {
        $send=$request->input('store_type_id');
        $column_id=$request->input('column_id');
        $quantity=$request->input('quantity');
        $reconc_id=$request->input('reconc_id');
        if($send==2) {

                    Tbl_receiving_item::where('id', $column_id)->update([
                        'quantity' => $quantity
                    ]);
DB::statement("Delete from tbl_stock_reconsilliations where id= $reconc_id");


        }

if($send==3) {


    Tbl_sub_store::where('id', $column_id)->update([
        'quantity' => $quantity
    ]);
DB::statement("Delete from tbl_stock_reconsilliations where id= $reconc_id");


        }
if($send==4) {


    Tbl_dispenser::where('id', $column_id)->update([
        'quantity_received' => $quantity
    ]);
DB::statement("Delete from tbl_stock_reconsilliations where id= $reconc_id");


        }

        return response()->json([
            'msg'=>"Reconciliation Reversed",
            "status"=>1
        ]);
    }

 public function RnRSearch(Request $request)
    {
 
 return "disabled";
 
$data=Tbl_item_type_mapped::select('item_code')->get();
 
        $dataa=$request->all();
        $program=$request->all()['program'];
        $facility_id=$dataa['facility_id'];
        try{
            DB::statement("SET @message1 = '".$facility_id."'");
            DB::statement("SET @message2 = '".$facility_id."'");
            DB::statement("SET @last_reporting_date = '2019-02-01'");
            DB::statement("CALL  generate_rnr_for_elmis (@message1,@last_reporting_date)");
            DB::statement("CALL  generate_rnr_adjustiments_for_elmis (@message2,@last_reporting_date)");
            $response = DB::select("SELECT @message1");
            if($response[0]->{'@message1'} !== 0)
                DB::statement("UPDATE `rnr_for_elmis` SET adjustment = (SELECT SUM(CONCAT(CASE WHEN adjustment_code IS NULL OR adjustment_code IN ('TRANSFER_IN','CLINIC_RETURN') THEN '+' ELSE '-' END, adjusted_quantity)) FROM rnr_adjustiments_for_elmis WHERE concept_code = `rnr_for_elmis`.concept_code)");
                return  $payload = DB::select("select
 
 product_price as product_price_I,
 (CEILING(SUM(((beginningBalance + quantityReceived + adjustment+ beginningBalance) * 2 - beginningBalance) / unit_of_measure) )*product_price) as cost_J, 
 SUM((beginningBalance -(- quantityReceived)-(-adjustment))- (-beginningBalance)) as Makadirioyamatumizi_E,
 SUM((beginningBalance -(- quantityReceived)-(-adjustment))- (-beginningBalance))*2 as Kiasichajuukinachohitajika_Y,
 (SUM((beginningBalance -(- quantityReceived)-(-adjustment))- (-beginningBalance))*2)- beginningBalance as Kiasikinachohitajika_F,
 CEILING(SUM(((beginningBalance + quantityReceived + adjustment+ beginningBalance) * 2 - beginningBalance) / unit_of_measure) ) as boxes_H,
 ((SUM((beginningBalance -(- quantityReceived)-(-adjustment))- (-beginningBalance))*2)*90)/(90-stockOutDays) as Kiasichajuukinachohitajikabaadayamarekebisho_Z,
 (((SUM((beginningBalance -(- quantityReceived)-(-adjustment))- (-beginningBalance))*2)*90)/(90-stockOutDays))- beginningBalance as Kiasichakinachohitajikabaadayamarekebisho_G,
  CEILING((((SUM((beginningBalance -(- quantityReceived)-(-adjustment))- (-beginningBalance))*2)*90)/(90-stockOutDays))/ unit_of_measure) as Kiasikilichoidhinishwa_K,
 ((((SUM((beginningBalance -(- quantityReceived)-(-adjustment))- (-beginningBalance))*2)*90)/(90-stockOutDays))/ unit_of_measure)*product_price as Gharamailiyoidhinishwa_L,
 t1.*,t2.item_code,t2.item_id,t3.item_name,t2.unit_of_measure,CONCAT(t2.item_code,t2.id) as skiped,t4.program_code as program from rnr_for_elmis t1 JOIN tbl_item_type_mappeds t2 ON t1.concept_code=t2.item_code JOIN tbl_items t3 On t2.item_id=t3.id 
JOIN tbl_elmis_item_program_mappings t4 On t4.product_code=t2.item_code 
JOIN tbl_elmis_prices t5 On t5.product_code=t1.concept_code 
WHERE t4.program_code='".$program."' GROUP BY t2.item_code ORDER BY t3.item_name asc");
            //  else
            //array_push($this->payload_computation_errors,["success"=>0,"message"=>"Error computing payload for: ".$report['report_name'],"error"=>""]);
        }catch(Exception $ex){
            //  array_push($this->payload_computation_errors,["success"=>0,"message"=>"Error computing payload for: ".$report['report_name'],"error"=>$ex->getMessage()]);
        }


    }

    public function preparernr(Request $items)
    {
		return "disabled";
        $rowsinserted=[];
        $all=$items->all();
       $facility_id=$all[0]['facility_id'];
       $order_status=$all[0]['order_status'];
       $user_id=$all[0]['user_id'];
        $facilitData=Tbl_facility::where('id',$facility_id)->select('facility_code')->take(1)->get();
         $facilityCodeNumber=$facilitData[0]->facility_code;
 $orderNumber=str_replace('-','',$facilitData[0]->facility_code).$items[0]['programCode'].date('ymdhis');

 $checkExistedNo=Tbl_rnr_order_control::where('order_number',$orderNumber)->get();
 if (count($checkExistedNo)>0){
     $orderNumber=str_replace('-','',$checkExistedNo[0]->facility_code).$items[0]['programCode'].date('ymdhis');
 }

$errors=[];
$duplicate=[];
$success=[];
    foreach ($items->all() as $request){
if ($request['quantityRequested'] !='' && $request['reasonForRequestedQuantity']==''){
    array_push($errors,['ItemCode'=>$request['item_code'],'amountRequested'=>$request['quantityRequested'],'reasonForRequestedQuantity'=>null]);
}
else{

    array_push($success,['itemCode'=>$request['item_code']]);
}

    }

    $outPutError=array($errors);
    $outPutSuccess=array($success);
    $dupliaceData=array($duplicate);
if (count($outPutError[0])>0){
    return response()->json([
        'errors'=>$errors,
        'dupliace'=>$duplicate,
        'fails'=>$errors,
        'success'=>$success,
        'info'=>'error',
        'errormsg'=>'<b>'.count($outPutError[0]) .'</b> out of <b>'. count($items->all()).'</b> <b style="color: red">row (s) Failed due to Missing Justification Reason(s)</b>',
        'status'=>0,
        'massage'=>'RnR Status',
        'data'=>'',
    ]);
}
else if (count($outPutSuccess[0])==count($items->all())){

    $rnrOrder=Tbl_rnr_order_control::create([
        'order_number'=>$orderNumber,
        'order_status'=>$order_status,
        'facilityCode'=>$facilityCodeNumber,
        'user_id'=>$user_id
    ]);
    if ($rnrOrder){
    foreach ($items->all() as $request){
//       return $amountNeede=( ((((($request['beginningBalance']+ $request['quantityReceived'])
//                        +$request['adjustment']))- $request['beginningBalance']) * 2));
        if(patientRegistration::duplicate('tbl_rnr_orders',['programCode','emergency','item_code', '((timestampdiff(day,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$request['programCode'],$request['emergency'],$request['item_code']
                ])==false){
            array_push($duplicate,['programCode'=>$request['programCode'],'ItemCode'=>$request['item_code'],'amountRequested'=>$request['quantityRequested']]);
        }
            $saved= Tbl_rnr_order::create(
                ['fullSupply'=>true,
                    'emergency'=>$request['emergency'],
                    'rnr_month'=>$request['rnr_month'],
                    'programCode'=>$request['programCode'],
                    'item_name'=>$request['item_name'],
                    'item_code'=>$request['item_code'],
                    'facilityCode'=>$facilityCodeNumber,
                    'quantityDispensed'=>$request['quantityDispensed'],
                    'quantityReceived'=>$request['quantityReceived'],
                    'beginningBalance'=>$request['beginningBalance'],
                    'stockInHand'=>$request['stockInHand'],
                    'adjustment'=>$request['adjustment'],
                    'stockOutDays'=>$request['stockOutDays'],
                    'quantityRequested'=>$request['quantityRequested'],
                    'amountNeeded'=>$request['amountNeeded'],
                    'reasonForRequestedQuantity'=>$request['reasonForRequestedQuantity'],
                    'order_number'=>$orderNumber,
                    'order_status'=>$order_status,
                    'user_id'=>$user_id
                ]);
            array_push($rowsinserted,['index'=>$saved]);


    }

    $errorsData=array($errors);
    $dupliaceData=array($duplicate);
    $rowsinsertedData=array($rowsinserted);
if (count($dupliaceData[0])==count($items->all())){
    return response()->json([
        'rows'=>$rowsinserted,
        'errors'=>$errors,
        'fails'=>$errors,
        'Duplicate'=>$duplicate,
        'success'=>$rowsinserted,
        'errormsg'=>'Success: '.count($rowsinsertedData[0]) . ' rows <p></p> Fails: '.count($errorsData[0]).' '.' <b style="color: red">Rows Fail due to Missing Justification Reason(s)</b>',
        'status'=>200,
        'info'=>'info',
        'massage'=>'RnR Status',
        'data'=>'<b style="color: red">Ooops!!! This RnR Order Item(s) has already created, if You want to update go to R&R ORDERS menu</b>' ,
    ]);
}
    return response()->json([
        'rows'=>$rowsinserted,
        'errors'=>$errors,
        'fails'=>$errors,
        'info'=>'success',
        'Duplicate'=>$duplicate,
        'success'=>$rowsinserted,
        'errormsg'=>'Success: '.count($rowsinsertedData[0]) . ' rows <p></p> Fails: '.count($errorsData[0]).' '.' <b style="color: red">Rows Fail due to Missing Justification Reason(s)</b>',
        'status'=>200,
        'massage'=>'RnR Status',
        'data'=>'RnR Processed Successful with Total Number of <b>'.count($rowsinsertedData[0]).' </b> row(s) data and <b> '.count($dupliaceData[0]).'</b> duplicate(s)' ,
    ]);
}
}


    }

        public function UpdateRnrOrderRowData(Request $request)
        {
			return "disabled";
          $identity=$request->all()['identity'];
            $detail=$request->all()['items'];
            $id=$detail['id'];

            if ($identity=='reason'){
                $FinalreasonForRequestedQuantity=$detail['FinalreasonForRequestedQuantity'];
             return   Tbl_rnr_order::where(['id'=>$id])->update([
                    'reasonForRequestedQuantity'=>$FinalreasonForRequestedQuantity
                ]);
            }
            if ($identity=='amount'){
               $FinalamountNeeded=$detail['FinalamountNeeded'];

                if (!is_numeric($FinalamountNeeded) ){

                }
                else{
                  return  Tbl_rnr_order::where(['id'=>$id])->update([
                        'quantityRequested'=>$FinalamountNeeded
                    ]);
                }

            }


        }
        public function DeleteItemOrderRow(Request $request)
        {
            $id=$request->all()['id'];
            DB::statement("DELETE FROM tbl_rnr_orders where id='".$id."'");
            return response()->json([
                'status'=>200,
                'massage'=>'RnR Row Data Deleted',
            ]);

        }
        public function  rnrSavedOrder(Request $request)
        {
			return "disabled";
            $order_number=$request['order_number'];
            $facility_id=$request['facility_id'];

         return   DB::select(
                "SELECT date(t1.updated_at) as preparedDate,t1.*,order_number   from tbl_rnr_orders t1  WHERE order_number='".$order_number."' ");
        }
        public function  LoadrnrSavedOrder_numbers(Request $request)
        {
			return "disabled";
            $facility_id=$request['facility_id'];
            $facilitData=Tbl_facility::where('id',$facility_id)->select('facility_code')->take(1)->get();
            $facilityCodeNumber=$facilitData[0]->facility_code;

         return   DB::select(
                "SELECT date(updated_at) as preparedDate,t.* from  tbl_rnr_order_controls t WHERE facilityCode='".$facilityCodeNumber."'  AND (order_status !='CANCELLED' AND order_status !='COMPLETED') ORDER BY created_at ASC "
            );
        }
    public function  LoadRnROrderStatus(Request $request)
        {
			return "disabled";
            $facility_id=$request['facility_id'];
            $facilitData=Tbl_facility::where('id',$facility_id)->select('facility_code')->take(1)->get();
            $facilityCodeNumber=$facilitData[0]->facility_code;
         $all[]=  DB::select(
                "SELECT DATE(created_at) AS period,
    IFNULL(SUM(CASE WHEN order_status='PREPARED' THEN 1 ELSE 0 END ),0) AS Totalprepared,
    IFNULL(SUM(CASE WHEN order_status='INITIATED' THEN 1 ELSE 0 END ),0) AS Totalprogress,
    IFNULL(SUM(CASE WHEN order_status='COMPLETED' THEN 1 ELSE 0 END ),0) AS Totalcompoleted,
    IFNULL(SUM(CASE WHEN order_status='CANCELLED' THEN 1 ELSE 0 END ),0) AS Totalcancelled
     from  tbl_rnr_order_controls t WHERE facilityCode='".$facilityCodeNumber."'"
            );
         $all[]=  DB::select(
                "SELECT DATE(created_at) AS period,
    IFNULL(SUM(CASE WHEN order_status='PREPARED' THEN 1 ELSE 0 END ),0) AS prepared,
    IFNULL(SUM(CASE WHEN order_status='INITIATED' THEN 1 ELSE 0 END ),0) AS progress,
    IFNULL(SUM(CASE WHEN order_status='COMPLETED' THEN 1 ELSE 0 END ),0) AS compoleted,
    IFNULL(SUM(CASE WHEN order_status='CANCELLED' THEN 1 ELSE 0 END ),0) AS cancelled
     from  tbl_rnr_order_controls t WHERE facilityCode='".$facilityCodeNumber."' GROUP BY month(created_at)"
            );
         return $all;
        }

        public function cancelpreparedrnr(Request $request)
        {
			return "disabled";
          $order_number= $request['order_number'] ;
            Tbl_rnr_order_control::where('order_number',$order_number)->update(['order_status'=>'CANCELLED']);
            Tbl_rnr_order::where('order_number',$order_number)->update(['order_status'=>'CANCELLED']);
            return response()->json([
                'message'=>'Order  number '.$order_number. ' has CANCELLED',
                'data'=>'',
                'status'=>200
            ]);
        }
        public function Deletepreparedrnr(Request $request)
        {
			return "disabled";
          $order_number= $request['order_number'] ;
            Tbl_rnr_order_control::where('order_number',$order_number)->delete();
            Tbl_rnr_order::where('order_number',$order_number)->delete();
            return response()->json([
                'msg'=>'Order  <i style="background-color: red">'.$order_number. '</i> has DELETED PERMANENTLY',
                'data'=>'',
                'status'=>200
            ]);
        }

     public function Initiatepreparedrnr(Request $request)
        {
			return "disabled";
          $order_number= $request['order_number'] ;
          $order_status= $request['status'] ;
           Tbl_rnr_order_control::where('order_number',$order_number)->update(['order_status'=>$order_status]);
           Tbl_rnr_order::where('order_number',$order_number)->update(['order_status'=>$order_status]);
           return response()->json([
               'message'=>'Order  number <i style="background-color: burlywood">'.$order_number. '</i> has '. $order_status,
               'data'=>'',
               'status'=>200
           ]);
        }
    public function Updatepreparedrnr(Request $request)
        {
			return "disabled";
          $order_number= $request['order_number'] ;
          $order_status= $request['status'] ;
           Tbl_rnr_order_control::where('order_number',$order_number)->update(['order_status'=>$order_status]);
           Tbl_rnr_order::where('order_number',$order_number)->update(['order_status'=>$order_status]);
//           Rnr_status_tracker::create([
//               'facility_id'=>$request->input("facility_id"),
//               'status'=>$request->input("rnr_status"),
//               'message'=>$request->input("message"),
//               'rnr_number'=>$request->input("order_number"),
//           ]);
           return response()->json([
               'message'=>'Order  number <i style="background-color: yellowgreen">'.$order_number. ' </i> has UPDATED',
               'data'=>'',
               'status'=>200
           ]);
        }

        public function  PharmacyLists()
        {
            return DB::table('vw_pharmacy_items')->where('item_name','!=','')->orderBy('item_name','asc')->get();
        }

        public function elmisData(Request $request)
        {
            $arrayData=[];
           $facility_id=$request['facility_id'];
            $facilitData=Tbl_facility::where('id',$facility_id)->select('facility_code')->take(1)->get();
            $facilityCodeNumber=$facilitData[0]->facility_code;
           $dataset= Tbl_rnr_order::where('facilityCode',$facilityCodeNumber)->select("fullSupply","emergency","programCode","item_code",
               "facilityCode","quantityDispensed","quantityReceived","beginningBalance",
               "stockInHand", "stockOutDays","quantityRequested","amountNeeded",
            "reasonForRequestedQuantity","order_number")->take(5)->get();

           $adjustment= DB::select("SELECT *from  rnr_adjustiments_for_elmis where facility_code='".$facilityCodeNumber."'");
           array_push($arrayData,['dataset'=>$dataset,
               'adjustment'=>$adjustment]);
           return $arrayData;
        }

}