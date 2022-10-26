<?php

namespace App\Http\Controllers\Pharmacy;

use App\classes\patientRegistration;
use App\Pharmacy\Tbl_dispenser;
use App\Pharmacy\Tbl_receiving_item;
use App\Pharmacy\Tbl_sub_store;
use App\Pharmacy\Tbl_user_store_configuration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\classes\SystemTracking;
use App\Trackable;
class SubStoreItemsController extends Controller
{
    //
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

            Tbl_sub_store::create($request->all());
             $oldData=null;
             $patient_id=null;
             $trackable_id=$newData->id;
             SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,$oldData);

            return response()->json([
                'msg'=>'Item Successful Received',
                'status'=>1
            ]);

        }
    }

    public function substore_item_receiving_list($facility,$user_id)
    {

return $user_id;
        return DB::table('tbl_user_store_configurations')
            ->join('vw_substore','vw_substore.store_id','=','tbl_user_store_configurations.store_id')
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->select('vw_substore.*')
            //->groupBy('vw_substore.created_at')
           // ->orderBy('vw_substore.updated_at')
            ->get();
    }

     public function item_balances_list_in_substore($facility,$user_id,$report_type)
    {


        if($report_type==1){
            return DB::table('tbl_sub_stores')
                ->join('tbl_items','tbl_sub_stores.item_id','=','tbl_items.id')
                ->join('tbl_user_store_configurations','tbl_sub_stores.issued_store_id','=','tbl_user_store_configurations.store_id')
                ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
                ->Where('tbl_user_store_configurations.status',1)
				->Where('tbl_user_store_configurations.user_id',$user_id)
                ->select('tbl_items.*', 'tbl_store_lists.store_name as sub_store_name',DB::raw('sum(quantity) as quantity'),'tbl_sub_stores.batch_no')
                ->where('control','l')
                ->groupBy('item_id')
				->groupBy('tbl_sub_stores.batch_no')
                ->groupBy('tbl_store_lists.id')
                ->get();
        }

        if($report_type==2)
        {
            return DB::table('tbl_user_store_configurations')
                ->join('vw_substore','vw_substore.store_id','=','tbl_user_store_configurations.store_id')
                ->Where('tbl_user_store_configurations.status',1)
				->Where('tbl_user_store_configurations.user_id',$user_id)
                ->select('vw_substore.*')
                ->get();
        }
        if($report_type==3){

        }
    }



    public function searchItemsubstoreReceived(Request $request)
    {

        $searchKey=$request['searchKey'];
        return DB::table('vw_substore')
            ->Where('item_name','like','%'.$searchKey.'%')
            ->orWhere('item_category','like','%'.$searchKey.'%')
            //->groupBy('item_id')
            ->get();
    }

    public function batchsubstore_list($item_id,$user_id)
    {


//        return DB::table('tbl_user_store_configurations')
//->join('vw_substore','vw_substore.store_id','=','tbl_user_store_configurations.store_id')
//            ->select('vw_substore.*')
//            ->Where('item_id',$item_id)
//            ->Where('tbl_user_store_configurations.user_id',$user_id)
//            ->orderBy('vw_substore.created_at','desc')
//            ->groupBy('batch_no')
//            ->groupBy('vw_substore.store_id')
//            ->get();



        return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_sub_stores','tbl_sub_stores.issued_store_id','=','tbl_store_lists.id')
->Where('tbl_user_store_configurations.status',1)
            ->Where('item_id',$item_id)
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->Where('control','l')
            ->Where('quantity','>',0)
            ->select('tbl_sub_stores.id','tbl_store_lists.id as store_id','tbl_sub_stores.item_id','tbl_sub_stores.batch_no',
                'quantity','tbl_store_lists.store_name','received_from_id')
          ->groupBy('batch_no')

            ->orderBy('quantity','desc')
            ->get();
    }

    public function batchsubstore_list_balance($item_id,$user_id)
    {


        return DB::table('tbl_user_store_configurations')
->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
->join('tbl_sub_stores','tbl_sub_stores.issued_store_id','=','tbl_store_lists.id')
->Where('tbl_user_store_configurations.status',1)
            ->Where('item_id',$item_id)
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->Where('control','l')
            
  
            ->select('tbl_sub_stores.id','tbl_store_lists.id as store_id','tbl_sub_stores.item_id','tbl_sub_stores.batch_no',
                'quantity','tbl_store_lists.store_name','received_from_id')
 
            ->orderBy('quantity','desc')

            ->get();
    }
    public function loadsubstoreBatchBalance($batch_no,$store_id)
    {
//        return Tbl_sub_store::Where('batch_no',$batch_no)
//        ->Where('issued_store_id',$store_id)
//            ->orderBy('id','desc')
//
//            ->first();

        return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_sub_stores','tbl_sub_stores.issued_store_id','=','tbl_store_lists.id')
->Where('tbl_user_store_configurations.status',1)
            ->Where('batch_no',$batch_no)
            ->Where('issued_store_id',$store_id)
            ->Where('control','l')
            ->Where('quantity','>',0)
            ->select('tbl_sub_stores.id','tbl_store_lists.id as store_id','tbl_sub_stores.item_id','tbl_sub_stores.batch_no',
                'quantity','tbl_store_lists.store_name','received_from_id')
            ->orderBy('quantity','desc')
            ->take(1)
            ->get();

    }


    public function substore_item_issuing(Request $items)
    {
          foreach ($items->all() as $request){

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
           $vendor_id='';
            $quantityInStore=$request['store_balance'];
            $expiry_date='';
            $user_id=$request['user_id'];
            //$invoice_refference='';
            $price=0;
            $store_type_id=$request['store_type_id'];
            $store_name=$request['store_name'];
            $internal_issuer_id=$request['internal_issuer_id'];
            $identifier=$request['identifier'];
            $adjustment=$request['adjustment'];
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

            if ($adjustment=='minus') {
                $update=Tbl_sub_store::where('id',$identifier)
                    ->update(['control'=>'c']);

                $data=  Tbl_sub_store::create([
                    'item_id'=>$item_id,
                    'quantity'=>$quantityInStore,
                    'quantity_issued'=>$quantity_issued,
                    'received_from_id'=>$received_from_id,
                    'issued_store_id'=>$store_sender_id,
                    'requested_store_id'=>$store_sender_id,
                    'transaction_type_id'=>$transaction_type_id,
                    'batch_no'=>$batch_no,
                    'control'=>'l',
                ]);
                $oldData=null;
                $patient_id=null;
                $trackable_id=$data->id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);

            }
//if items are issued to another main store
            if ($store_type_id==2){

                return response()->json([
                    'msg' => 'You can not send item from Sub store  to main store called...' . $store_name,
                    'status' => 0
                ]);

            }

            //if items are issued to sub store
            else if ($store_type_id==3) {
                $update=Tbl_sub_store::where('id',$identifier)
                    ->update(['control'=>'c']);

                $data= Tbl_sub_store::create([ //for receiving item within a same sub store type
                    'item_id' => $item_id,
                    'quantity' => $quantity_issued,
                    //'quantity_issued'=>$quantity_issued,
                    'received_from_id' => $store_sender_id,
                    'issued_store_id' => $store_receiver_id,
                    //'requested_store_id'=>$store_receiver_id,
                    'transaction_type_id' => $transaction_type_id,
                    'batch_no' => $batch_no,
                    'control' => 'l',
                ]);
                $oldData=null;
                $patient_id=null;
                $trackable_id=$data->id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);
if($data){


                Tbl_sub_store::create([ //issuing item within a same sub store type
                    'item_id' => $item_id,
                    'quantity' => $quantityInStore,
                    'quantity_issued' => $quantity_issued,
                    'received_from_id' => $received_from_id,
                    'issued_store_id' => $store_sender_id,
                    'requested_store_id' => $store_receiver_id,
                    'transaction_type_id' => $transaction_type_id,
                    'batch_no' => $batch_no,
                    'user_targeted_id' => $user_targeted_id,
                    'control' => 'l',

                ]);
   }

            else{

            }
            }
            //if items are issued to dispensing
            else if ($store_type_id==4){
                $update=Tbl_sub_store::where('id',$identifier)
                    ->update(['control'=>'c']);

              $data=  Tbl_sub_store::create([
                    'item_id'=>$item_id,
                    'quantity'=>$quantityInStore,
                     'quantity_issued'=>$quantity_issued,
                    'received_from_id'=>$received_from_id,
                    'issued_store_id'=>$store_sender_id,
                    'requested_store_id'=>$store_receiver_id,
                    'transaction_type_id'=>$transaction_type_id,
                    'batch_no'=>$batch_no,
                    'control'=>'l',
                ]);
                $oldData=null;
                $patient_id=null;
                $trackable_id=$data->id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);
if($data){


                Tbl_dispenser::create([
                    'item_id'=>$item_id,
                    'transaction_type_dispensed_id'=>$transaction_type_id,
                    'quantity_received'=>$quantity_issued,
                    'received_from_id'=>$store_sender_id,
                    'dispenser_id'=>$store_receiver_id,
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
            'msg' => 'Successful Issued to...' . $store_name,
            'status' => 1
        ]);


    }


    public function substore_item_ordering(Request $items)
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

            if (patientRegistration::duplicate('tbl_sub_stores',['item_id','request_amount','issued_store_id','received_from_id',
                    'user_id',
                    '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                    [$item_id,$quantity, $request_sender,$request_receiver,$user_id
                    ])==true){
 }

            if($request_sender_type==3 && $request_receiver_type==2 ) {
                //if request is to main store from sub store
     $substore = Tbl_sub_store::create([
                    'item_id' => $item_id,
                    'request_amount' => $quantity,
                    'issued_store_id' => $request_sender,
                    'received_from_id' => $request_receiver,
                    'facility_id' => $facility_id,
                    'user_id' => $user_id,
                    'request_status_id' => 4,
                    'control'=>'o',
                ]);
                 $order_id = $substore->id;
                $oldData=null;
                $patient_id=null;
                $trackable_id=$order_id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$substore,$oldData);
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
            }  if($request_sender_type==3 && $request_receiver_type==3 ) {
                //if request is to sub store from sub store


                $substore = Tbl_sub_store::create([
                    'item_id' => $item_id,
                    'request_amount' => $quantity,
                    'issued_store_id' => $request_sender,
                    'received_from_id' => $request_receiver,
                    'facility_id' => $facility_id,
                    'user_id' => $user_id,
                    'request_status_id' => 4,
                    'control'=>'o',
                ]);
                 $order_id = $substore->id;
                $oldData=null;
                $patient_id=null;
                $trackable_id=$order_id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$substore,$oldData);
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
            else{

            }
            }

        return response()->json([
            'msg' => 'Request has Successful send to ' . $receiver_name,
            'status' => 1
        ]);

        }

    public function sub_store_incoming_order($facility,$user_id)
    {


        return DB::table('tbl_user_store_configurations')
            ->join('vw_sub_store_incoming_order','vw_sub_store_incoming_order.store_id','=','tbl_user_store_configurations.store_id')
          ->Where('tbl_user_store_configurations.status',1)
		  ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->distinct('vw_sub_store_incoming_order.*')
            ->groupBy('order_no')
            ->get();
    } 
    
    public function sub_store_pending_orders($facility,$user_id)
    {


        return DB::table('tbl_user_store_configurations')
            ->join('vw_main_store_incoming_order','vw_main_store_incoming_order.store_id','=','tbl_user_store_configurations.store_id')
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->select('vw_main_store_incoming_order.*')
            ->groupBy('order_no')
            ->get();

    }

 public function dispensing_pending_orders($facility,$user_id)
    {

$all=[];
        $all[]= DB::table('tbl_user_store_configurations')
            ->join('vw_sub_store_incoming_order','vw_sub_store_incoming_order.store_id','=','tbl_user_store_configurations.store_id')
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->distinct('vw_sub_store_incoming_order.*')
             ->groupBy('order_no')
            ->get();

        $all[]= DB::table('tbl_user_store_configurations')
            ->join('vw_main_store_incoming_order','vw_main_store_incoming_order.store_id','=','tbl_user_store_configurations.store_id')
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->select('vw_main_store_incoming_order.*')
            ->groupBy('order_no')
            ->get();

        return $all;



    }



        public function sub_store_Order_processing(Request $request)
    {

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
        $identifier=$request['identifier'];
        $requested_store_type_id=$request['requested_store_type_id'];
        $requesting_store_type_id=$request['requesting_store_type_id'];

        if ($requested_store_type_id==3 && $requesting_store_type_id==4){
            //sub store processing order of dispensing
            $update=Tbl_sub_store::where('id',$identifier)
                ->update(['control'=>'c']);

            $subStore1=Tbl_sub_store::where('order_no',$order_no)->get();
            $subStore=Tbl_sub_store::where('order_no',$order_no)->update([
                'received_from_id'=>$requested_store_id,
                'quantity'=>$store_balance_remained,
                'quantity_issued'=>$quantity_issued,
                'transaction_type_id'=>$transaction_type_id,
                'request_status_id'=>1,
                'batch_no'=>$batch_no,
                'control'=>'l',
            ]);

            $dispensing=Tbl_dispenser::where('id',$order_no)->update([

                'quantity_received'=>$quantity_issued,
                'batch_no'=>$batch_no,
                'transaction_type_dispensed_id'=>$transaction_type_id,
                'user_id'=>$user_id,
                'dispensing_status_id'=>2,
                'received_from_id'=>$requested_store_id,
                'control'=>'l',
            ]);



            $oldData=null;
            $patient_id=null;
            $trackable_id=$subStore1[0]->id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$subStore1,$oldData);

            return response()->json([
                'msg'=>'Order Successful Processed and has send to '. $store_name,
                'status'=>1
            ]);
        }
        else if ($requested_store_type_id==3 && $requesting_store_type_id==3){

//sub store processing order of another sub store

            $update=Tbl_sub_store::where('id',$identifier)
                ->update(['control'=>'c']);

            $subStore=Tbl_sub_store::where('order_no',$order_no)->update([
                'received_from_id'=>$requested_store_id,
                'quantity'=>$store_balance_remained,
                'quantity_issued'=>$quantity_issued,
                'transaction_type_id'=>$transaction_type_id,
                'request_status_id'=>1,
                'batch_no'=>$batch_no,
                'control'=>'l',
            ]);

            $subStore=Tbl_sub_store::where('id',$order_no)->update([
                'received_from_id'=>$requested_store_id,
                'quantity'=>$quantity_issued,
                'transaction_type_id'=>$transaction_type_id,
                'transaction_type_id'=>$transaction_type_id,
                'request_status_id'=>1,
                'batch_no'=>$batch_no,
                'control'=>'l',
            ]);
            $subStore=Tbl_sub_store::where('id',$order_no)->get();
            $oldData=null;
            $patient_id=null;
            $trackable_id=$subStore->id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$subStore,$oldData);

            return response()->json([
                'msg'=>'Order Successful Processed and has send to '. $store_name,
                'status'=>1
            ]);
        }
        else{
            return response()->json([
                'msg'=>'Order  Processing failed to send to '. $store_name,
                'status'=>0
            ]);
        }    }

}