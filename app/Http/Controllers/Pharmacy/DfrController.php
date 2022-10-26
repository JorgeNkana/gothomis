<?php

namespace App\Http\Controllers\Pharmacy;

use App\classes\patientRegistration;
use App\classes\SystemTracking;
use App\Facility\Tbl_facility;
use App\Pharmacy\Tbl_dfr_order;
use App\Pharmacy\Tbl_receiving_dfr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DfrController extends Controller
{
    //

    public function dfr_item_receiving_registration(Request $items)
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
            Tbl_receiving_dfr::create([
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

    public function dfr_item_receiving_list($facility,$user_id)
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
    public function dfr_item_balances_list_in_mainstore($facility,$user_id,$report_type)
    {
        //balance


            return DB::table('tbl_receiving_dfrs')
                ->join('tbl_items','tbl_receiving_dfrs.item_id','=','tbl_items.id')
                ->join('tbl_user_store_configurations','tbl_receiving_dfrs.received_store_id','=','tbl_user_store_configurations.store_id')
                ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
                ->join('tbl_item_type_mappeds','tbl_item_type_mappeds.item_id','=','tbl_items.id')
                ->Where('tbl_user_store_configurations.status','=',1)
                ->Where('tbl_user_store_configurations.user_id',$user_id)
                ->select('tbl_items.*','tbl_receiving_dfrs.quantity','tbl_store_lists.*',DB::raw('sum(quantity) as quantity'),'item_code')
                ->where('control','=','l')
                ->groupBy('tbl_items.id')
                ->groupBy('tbl_store_lists.id')
                ->get();


    }

    public function dfr_create_invoice(Request $items)
    {
       //return  $items->all();
        foreach ($items->all() as $request){

            $item_id=$request['item_id'] ;
            $request_sender=$request['request_sender'] ;
            $request_receiver=$request['request_receiver'] ;
            $request_receiver_type=$request['request_receiver_type'] ;
            $request_sender_type=$request['request_sender_type'] ;
            $quantity=$request['quantity'] ;
            $price=$request['price'] ;
            $facility_id=$request['facility_id'] ;
            $user_id=$request['user_id'] ;
            $receiver_name=$request['receiver_name'] ;

            if (patientRegistration::duplicate('tbl_dfr_orders',['item_id','request_amount','issued_store_id','received_from_id',
                    'user_id',
                    '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                    [$item_id,$quantity, $request_sender,$request_receiver,$user_id
                    ])==true){
            }
             //$setName=substr($receiver_name,0,4);
  $order_reference_no=date('Ymd');

                $substore = Tbl_dfr_order::create([
                    'item_id' => $item_id,
                    'request_amount' => $quantity,
                    'issued_store_id' => $request_sender,
                    'received_from_id' => $request_receiver,
                    'facility_id' => $facility_id,
                    'order_reference_no' => $order_reference_no,
                    'user_id' => $user_id,
                    'price' => $price,
                    'request_status_id' => 4,
                    'control'=>'o',
                ]);
                $order_id = $substore->id;
                $oldData=null;
                $patient_id=null;
                $trackable_id=$order_id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$substore,$oldData);
                if ($order_id >= 1) {
                    $mainstore = Tbl_receiving_dfr::create([
                        'item_id' => $item_id,
                        'requested_amount' => $quantity,
                        'requesting_store_id' => $request_sender,
                        'received_store_id' => $request_receiver,
                        'facility_id' => $facility_id,
                        'user_id' => $user_id,
                        'order_no' => $order_id,
                        'price' => $price,
                        'order_reference_no' => $order_reference_no,
                        'request_status_id' => 4,
                        'control' => 'o',
                    ]);


                }
                }
        return response()->json([
            'msg' => 'Invoice has Created ' ,
            'status' => 1
        ]);

    }



    public function item_balance_list($facility,$user_id)
    {


    }

    public function dfr_pending_invoice($facility,$user_id)
    {


        return DB::table('tbl_user_store_configurations')
            ->join('vw_dfr_orders','vw_dfr_orders.store_id','=','tbl_user_store_configurations.store_id')
            ->Where('tbl_user_store_configurations.status','=',1)
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->Where('order_status','=',4)
            ->select('vw_dfr_orders.*')
            ->groupBy('order_no')
            ->get();
    }

    public function dfr_Cancel_invoice(Request $request)
    {


 $order_id=$request['order_no'];

Tbl_receiving_dfr::where('order_no',$order_id)->update([
    'request_status_id'=>5
]);

Tbl_dfr_order::where('id',$order_id)->update([
    'request_status_id'=>5
]);

        return response()->json([
            'msg' => 'Data record Deleted From Invoice ' ,
            'status' => 1
        ]);
    }
    public function searchItemReceived(Request $request)
    {

        $searchKey=$request['searchKey'];
        return DB::table('vw_receivings')
            ->Where('item_name','like','%'.$searchKey.'%')
            ->orWhere('item_category','like','%'.$searchKey.'%')
            //->groupBy('item_id')
            ->get();
    }

    public function dfr_batch_list($item_id,$user_id)
    {
//

        return $batch_list_balance =   DB::select(DB::raw("SELECT tbl_receiving_dfrs.id,expiry_date,invoice_refference as invoice_id,received_from_id as vendor_id,quantity as balance,received_store_id as store_id,batch_no,tbl_store_lists.store_name,item_id
   FROM `tbl_user_store_configurations`
  inner join `tbl_store_lists` on `tbl_store_lists`.id=`tbl_user_store_configurations`.store_id
  inner join `tbl_receiving_dfrs` on `tbl_receiving_dfrs`.received_store_id=`tbl_user_store_configurations`.store_id
   WHERE item_id='{$item_id}' AND tbl_user_store_configurations.user_id='{$user_id}'  AND tbl_user_store_configurations.status=1  AND (control='l') AND quantity>0 AND timestampdiff(month,CURDATE(),expiry_date)>0 GROUP BY tbl_receiving_dfrs.id,tbl_store_lists.id ORDER BY expiry_date ASC "));
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
   WHERE item_id='{$item_id}' AND tbl_user_store_configurations.user_id='{$user_id}'  AND (control='l') AND quantity>0 AND timestampdiff(month,CURDATE(),expiry_date)>0   GROUP  BY batch_no,quantity"));
        }
        elseif ($sender==2){

            $batch_list_balance =   DB::table('tbl_user_store_configurations')
                ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
                ->join('tbl_sub_stores','tbl_sub_stores.issued_store_id','=','tbl_store_lists.id')
                ->join('tbl_items','tbl_items.id','=','tbl_sub_stores.item_id')

                ->Where('item_id',$item_id)
                ->Where('tbl_user_store_configurations.user_id',$user_id)
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
                ->join('tbl_items','tbl_items.id','=','tbl_dispensers.item_id')
                ->Where('item_id',$item_id)
                ->Where('tbl_user_store_configurations.user_id',$user_id)
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
   AND (control='l') AND timestampdiff(month,CURDATE(),expiry_date)>0 ORDER BY expiry_date ASC "));

    }
    public function dfr_loadBatchBalance($batch_no,$store_id,$item_id)
    {
        return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_receiving_dfrs','tbl_receiving_dfrs.received_store_id','=','tbl_user_store_configurations.store_id')

            ->Where('batch_no',$batch_no)
            ->Where('tbl_receiving_dfrs.received_store_id',$store_id)
            ->Where('tbl_receiving_dfrs.item_id',$item_id)
            ->Where('tbl_user_store_configurations.status',1)
            ->Where('control','l')
            ->Where('quantity','>',0)
            ->select('tbl_receiving_dfrs.id','expiry_date','invoice_refference as invoice_id','received_from_id','quantity','received_store_id as store_id','batch_no','tbl_store_lists.store_name')
            ->orderBy('quantity','desc')

            ->get();
    }

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

    public function dfr_Order_processing(Request $request)
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

            $update=Tbl_receiving_dfr::where('id',$identifier)
                ->update(['control'=>'c']);

            $subStore=Tbl_dfr_order::where('id',$order_no)->update([
                'received_from_id'=>$requested_store_id,
                'quantity'=>$quantity_issued,
                'transaction_type_id'=>$transaction_type_id,
                'request_status_id'=>1,
                'batch_no'=>$batch_no,
                'control'=>'l',
            ]);

            $mainStore=Tbl_receiving_dfr::where('id',$request_id)->update([
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
            $newData=Tbl_receiving_dfr::where('id',$request_id)->get();
            $oldData=null;
            $patient_id=null;
            $trackable_id=$request_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,$oldData);




            return response()->json([
                'msg'=>'Order Successful Processed',
                'status'=>1
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

    public function dfr_expired($facility_code)
    {


        return $expiry_date =   DB::select(DB::raw("SELECT item_name,expiry_date,quantity,batch_no, timestampdiff(month,CURDATE(),expiry_date) as expirydate FROM `tbl_receiving_dfrs` inner join `tbl_items` on `tbl_receiving_dfrs`.item_id=`tbl_items`.id WHERE facility_id='".$facility_code."' AND (control='l' AND quantity >0) AND timestampdiff(month,CURDATE(),expiry_date)<1"));


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
    public function dfr_issue_voucher(Request $request)
    {

        $facility_id=$request['facility_id'];
        $user_id=$request['user_id'];
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];

        return DB::select(DB::raw("SELECT item_name,store_name,issued_quantity as quantity ,tbl_receiving_dfrs.created_at,(select t.store_name  from tbl_store_lists  as t where t.id= tbl_receiving_dfrs.received_store_id) as main_store_name,(select t1.name  from users  as t1 where t1.id= tbl_receiving_dfrs.user_id) as name,batch_no from tbl_receiving_dfrs INNER join tbl_store_lists on tbl_store_lists.id= tbl_receiving_dfrs.requesting_store_id 
   INNER join tbl_items on tbl_items.id= tbl_receiving_dfrs.item_id 
   INNER join tbl_user_store_configurations on tbl_user_store_configurations.store_id= tbl_receiving_dfrs.received_store_id 
   INNER join users on tbl_user_store_configurations.user_id= users.id
     WHERE issued_quantity is NOT null AND tbl_user_store_configurations.user_id='".$user_id."'  AND 
tbl_receiving_dfrs.facility_id='".$facility_id."' AND (tbl_receiving_dfrs.created_at BETWEEN '{$start_date}' AND '{$end_date}' ) group by tbl_receiving_dfrs.id "));

    }

    public function dfr_received_voucher(Request $request)
    {
        $facility_id=$request['facility_id'];
        $user_id=$request['user_id'];
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        return DB::select(DB::raw("SELECT store_name,quantity,tbl_receiving_dfrs.created_at,item_name,tbl_invoices.invoice_number,(select t1.name  from users  as t1 where t1.id= tbl_receiving_dfrs.user_id) as name,batch_no,price,item_code  from tbl_receiving_dfrs INNER join tbl_store_lists on tbl_store_lists.id= tbl_receiving_dfrs.received_store_id 
   INNER join tbl_items on tbl_items.id= tbl_receiving_dfrs.item_id  INNER join tbl_user_store_configurations on tbl_user_store_configurations.store_id= tbl_receiving_dfrs.received_store_id 
   INNER join users on tbl_user_store_configurations.user_id= users.id
    inner join tbl_invoices on tbl_invoices.id=tbl_receiving_dfrs.invoice_refference
    inner join tbl_item_type_mappeds on tbl_items.id=tbl_item_type_mappeds.item_id
     WHERE control_in ='r' AND tbl_receiving_dfrs.facility_id= '".$facility_id."' AND tbl_user_store_configurations.user_id='".$user_id."'
     AND (tbl_receiving_dfrs.created_at BETWEEN '".$start_date."' AND '".$end_date."' )  group by tbl_receiving_dfrs.id "));

    }

    public function dfr_issued_store_voucher_list($user_id)
    {

        return DB::table('tbl_receiving_dfrs')
            ->join('tbl_user_store_configurations', 'tbl_receiving_dfrs.user_id', '=', 'tbl_user_store_configurations.user_id')
            ->join('tbl_store_lists', 'tbl_receiving_dfrs.requesting_store_id', '=', 'tbl_store_lists.id')
            ->select('tbl_store_lists.id', 'tbl_store_lists.store_name')
            ->where('tbl_user_store_configurations.status', '=', 1)
            ->where('tbl_user_store_configurations.user_id', '=', $user_id)
            ->where('tbl_receiving_dfrs.issued_quantity', '!=', NULL)
            ->groupBy('tbl_store_lists.id')
            ->orderBy('tbl_receiving_dfrs.created_at', 'desc')
            ->get();
    }
    public function dfr_loadStoreVoucherDates($store_id)
    {

        return  DB::select("SELECT t3.id as store_id,DATE(t1.created_at) AS issued_date,store_name  FROM tbl_receiving_dfrs  t1
join tbl_user_store_configurations t2 on t1.user_id=t2.user_id 
join tbl_store_lists t3 on t1.requesting_store_id=t3.id where t1.issued_quantity IS NOT NULL AND requesting_store_id='".$store_id."' GROUP BY DATE(t1.created_at) ORDER BY DATE(t1.created_at) DESC");


    }
    public function dfr_ViewVoucherDetails(Request $request)
    {

        $dated=Date($request->dated);
        $store_id=$request->store_id;
        //return  DB::select("SELECT  t1.created_at as vourcher_no,t2.item_name,t1.issued_quantity as issued,t1.requested_amount as required,t3.name as issuer FROM tbl_receiving_items  t1 join tbl_items t2 on t1.item_id=t2.id join users t3 on t1.user_id=t3.id WHERE t1.issued_quantity IS NOT NULL AND t1.created_at LIKE '%$dated%' AND requesting_store_id='".$store_id."' GROUP BY t1.id");
        return  DB::select("SELECT date(t1.created_at) as due_date, t1.order_reference_no as vourcher_no,t2.item_name,sum(t1.issued_quantity) as issued,sum(t1.requested_amount) as required,t3.name as issuer FROM tbl_receiving_dfrs  t1 join tbl_items t2 on t1.item_id=t2.id join users t3 on t1.user_id=t3.id WHERE t1.issued_quantity IS NOT NULL AND t1.created_at LIKE '%$dated%' AND requesting_store_id='".$store_id."' GROUP BY t1.item_id");

    }

    public function facility_address($facility_id)
    {

        return DB::select("SELECT facility_name, address, council_name,region_name,mobile_number,t5.description as facility_type FROM tbl_facilities t1 JOIN tbl_facility_types t5 on  t1.facility_type_id=t5.id left join tbl_councils t2 on t1.council_id=t2.id join tbl_regions t3 on t3.id = t1.region_id where t1.id='".$facility_id."'");
    }

    public function dfr_LoadbinCardData(Request $request)
    {

        if (empty($request['store_id'] )){
            return null;
        }
        $store_id=$request->store_id;
        //return  DB::select("SELECT  t1.created_at as vourcher_no,t2.item_name,t1.issued_quantity as issued,t1.requested_amount as required,t3.name as issuer FROM tbl_receiving_items  t1 join tbl_items t2 on t1.item_id=t2.id join users t3 on t1.user_id=t3.id WHERE t1.issued_quantity IS NOT NULL AND t1.created_at LIKE '%$dated%' AND requesting_store_id='".$store_id."' GROUP BY t1.id");
        return  DB::select("SELECT  t2.*,t3.item_code FROM tbl_receiving_dfrs  t1 join tbl_items t2 on t1.item_id=t2.id
 join tbl_item_type_mappeds t3 on t3.item_id=t2.id
 where received_store_id=$store_id  GROUP BY t1.item_id ORDER BY t1.quantity desc");

    }
    public static function search_dfr_pharmacy_items(Request $request)
    {

        $searchKey=$request['searchKey'];
        return DB::table('vw_dfr_pharmacy_items')
            ->Where('item_name','like','%'.$searchKey.'%')
           // ->orWhere('item_category','like','%'.$searchKey.'%')
            ->get();

    }
    public function dfr_single_item_issue_voucher(Request $request)
    {
        $item_id=$request['item_id'];
        $store_id=$request['store_id'];
        return  DB::select("SELECT ifnull((CASE WHEN control='l' THEN SUM(quantity) END),0) as balanced, date(created_at) as date,quantity as received,issued_quantity as issued,quantity as balance,expiry_date,control,control_in FROM tbl_receiving_dfrs  where item_id=  $item_id AND received_store_id=$store_id AND (issued_quantity IS NOT NULL OR control_in='r') GROUP BY id order by id asc");

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

    public function elmisFolio(Request $request)
    {
        return eLMISFolioCreation($request->facility_id,$request->programCode,$request->emergency);
    }
    public function RnRSearch(Request $request)
    {

        $a=array("red","green");
        array_push($a,"blue","yellow");
        //return $a;
        $allItems= DB::select('SELECT distinct  t1.id,t2.item_code, t1.item_name FROM tbl_items t1 JOIN tbl_item_type_mappeds t2 ON t1.id=t2.item_id JOIN tbl_receiving_items t3 ON t1.id=t3.item_id AND t1.dept_id=4');

        foreach ($allItems as $single){
            $code=$single->item_code;
            $item_name=$single->item_name;
            $all[]=DB::select("SELECT  t4.item_code, item_name,  SUM(t3.quantity) as quantytyReceived FROM tbl_receiving_items t3 JOIN tbl_item_type_mappeds t4 ON t3.item_id=t4.item_id JOIN tbl_items t5 ON t3.item_id=t5.id WHERE control_in='r' 
  AND t4.item_code='".$code."'
GROUP BY t4.item_id,");

        }
        //array_push($allItems,$singleSum);
        return $all;
    }
}