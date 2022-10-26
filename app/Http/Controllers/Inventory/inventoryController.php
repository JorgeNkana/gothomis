<?php

namespace App\Http\Controllers\Inventory;

use App\Department\Tbl_department;
use App\Inventory\Tbl_inventory_issuing;
use App\Inventory\Tbl_inventory_item;
use App\Inventory\Tbl_inventory_order;
use App\Inventory\Tbl_inventory_receiving;
use App\Inventory\Tbl_inventory_request;
use App\Inventory\Tbl_ledger;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class inventoryController extends Controller
{
    public function newLedger(Request $request)
    {
        Tbl_ledger::create($request->all());
        return response()->json([
           'msg'=>'Ledger successfully created',
            'status'=>1
        ]);
    }

    public function getLedgers(Request $request)
    {
        $facility_id =$request->facility_id;
        return Tbl_ledger::where('facility_id',$facility_id)->get();
    }

    public function getItems(Request $request)
    {
        $facility_id = $request->facility_id;
        $sql = "SELECT t2.id,t2.item_name,t2.item_code,t1.ledger_name FROM tbl_ledgers t1 INNER JOIN tbl_inventory_items t2 ON t1.id = t2.item_type_id
                WHERE t1.facility_id ='".$facility_id."' ";
        return DB::select(DB::raw($sql));
    }

    public function updateLedger(Request $request)
    {
        $id=$request->id;
        $facility_id=$request->facility_id;
        Tbl_ledger::where('id',$id)->where('facility_id',$facility_id)
            ->update([
               'ledger_name'=>$request->ledger_name,
               'ledger_code'=>$request->ledger_code,
               'description'=>$request->description,
            ]);
        return response()->json([
            'msg'=>$request->ledger_name.' successfully updated',
            'status'=>1
        ]);
    }
    public function updateItem(Request $request)
    {
        $id=$request->id;
        Tbl_inventory_item::where('id',$id)
            ->update([
               'item_name'=>$request->item_name,
               'item_code'=>$request->item_code,
            ]);
        return response()->json([
            'msg'=>$request->item_name.' successfully updated',
            'status'=>1
        ]);
    }

    public function postNewItem(Request $request)
    {
        Tbl_inventory_item::create($request->all());
        return response()->json([
            'msg'=>'Item successfully created',
            'status'=>1
        ]);
    }

    public function getDepartmentOrders(Request $request)
    {
        $data = [];
        $facility_id = $request->facility_id;
        $sql1 = "SELECT t1.quantity,t1.created_at,t2.item_name,t3.department_name FROM 
                tbl_inventory_requests t1 INNER JOIN tbl_inventory_items t2 ON t1.item_id = t2.id INNER JOIN tbl_departments t3 ON 
                t3.id = t1.department_id WHERE t1.facility_id ='".$facility_id."' AND t1.status= 0
                ";
        $sql2 = " SELECT SUM(t1.quantity) AS total_quantity,t1.item_id,t2.item_name FROM tbl_inventory_requests t1 
       INNER JOIN tbl_inventory_items t2 ON t1.item_id = t2.id WHERE t1.status= 0 GROUP BY t1.item_id  ";
        $data[] = DB::select(DB::raw($sql1));
        $data[] = DB::select(DB::raw($sql2));
         DB::select(DB::raw($sql2));
        return $data;
    }

    public function postOrderItems(Request $request)
    {
        $user_id = $request->user_id;
        $facility_id = $request->facility_id;
        $orderItems = $request->item;
        $data = Tbl_inventory_order::create([
            'facility_id'=>$facility_id,
            'user_id'=>$user_id,
        ]);
        $order_id = $data->id;
        foreach ($orderItems as $item){
           Tbl_inventory_receiving::create([
                'quantity'=>$item['total_quantity'],
                'item_id'=>$item['item_id'],
                'order_number'=>$order_id
            ]);
        }
        foreach ($orderItems as $item){
            Tbl_inventory_request::where('item_id',$item['item_id'])
                ->where('facility_id',$facility_id)
                ->update([
                    'status'=>1
                ]);
        }

        return response()->json([
            'msg'=>'Order created successfully...!',
            'status'=>1
        ]);
    }

    public function inspectOrders(Request $request)
    {   $data =[];
        $facility_id = $request->facility_id;
        $sql = "SELECT t1.order_number,t1.batch,t1.quantity,t1.cost_price,t3.item_name,t1.supplier FROM tbl_inventory_receivings t1 
                INNER JOIN tbl_inventory_orders t2 ON t2.id =t1.order_number
                INNER JOIN tbl_inventory_items t3 ON t3.id =t1.item_id
                WHERE t2.facility_id = $facility_id AND t1.order_status = 0 GROUP BY t1.order_number ";
        $sql1 = "SELECT t1.order_number,t1.batch,t1.quantity,t1.cost_price,t3.item_name,t1.supplier FROM tbl_inventory_receivings t1 
                INNER JOIN tbl_inventory_orders t2 ON t2.id =t1.order_number
                INNER JOIN tbl_inventory_items t3 ON t3.id =t1.item_id
                WHERE t2.facility_id = $facility_id AND t1.order_status = 1 GROUP BY t1.order_number ";
        $sql2 = "SELECT t1.order_number,t1.batch,t1.quantity,t1.cost_price,t3.item_name,t1.supplier FROM tbl_inventory_receivings t1 
                INNER JOIN tbl_inventory_orders t2 ON t2.id =t1.order_number
                INNER JOIN tbl_inventory_items t3 ON t3.id =t1.item_id
                WHERE t2.facility_id ='".$facility_id."' AND t1.order_status = 2 GROUP BY t1.order_number ";
      $data[] =  DB::select(DB::raw($sql));
      $data[] =  DB::select(DB::raw($sql1));
      $data[] =  DB::select(DB::raw($sql2));
        return $data;
    }

    public function getOrderItems(Request $request)
    {
        $order_number = $request->order_number;
        $sql = "SELECT t1.*,t2.item_name FROM tbl_inventory_receivings t1 INNER JOIN tbl_inventory_items t2 ON t2.id = t1.item_id
        WHERE t1.order_number ='".$order_number."'";
        return DB::select(DB::raw($sql));
    }

    public function updateOrderItem(Request $request)
    {
        $order_status = $request->order_status;
        $batch = $request->batch;
        $id = $request->id;
        $item_name = $request->item_name;
        $cost_price = $request->cost_price;
        $supplier = $request->supplier;
          $data = Tbl_inventory_receiving::where('id',$id)
                ->update([
                    'order_status'=>$order_status,
                    'batch'=>$batch,
                    'cost_price'=>$cost_price,
                    'supplier'=>$supplier,
                ]);
            if($order_status == 1){
                return response()->json([
                    'msg'=>$item_name.' successfully received...',
                    'status'=>1
                ]);
            }
            else if($order_status == 2){
                return response()->json([
                    'msg'=>$item_name.' rejected...',
                    'status'=>0
                ]);
            }

    }

    public function getUserDepartments()
    {
        return Tbl_department::get();
    }

    public function getDepartmentItems(Request $request)
    {
        $department_id = $request->department_id;
        $facility_id = $request->facility_id;
        $sql = "SELECT t1.quantity,t2.id AS item_id,t4.id AS item_received_id,t2.item_name,t3.id AS dept_id,t3.department_name FROM tbl_inventory_requests t1 
         INNER JOIN tbl_inventory_items t2 ON t1.item_id = t2.id
         INNER JOIN tbl_departments t3 ON t1.department_id = t3.id
         INNER JOIN tbl_inventory_receivings t4 ON t4.item_id = t2.id
         WHERE t1.facility_id = '".$facility_id."' AND t1.department_id ='".$department_id."' AND t1.status = 1 GROUP BY t2.id";
        return DB::select(DB::raw($sql));
    }

    public function issueInventoryItems(Request $request)
    {
        Tbl_inventory_issuing::create($request->all());
        Tbl_inventory_request::where('item_id',$request->item_id)
            ->where('facility_id',$request->facility_id)
            ->where('department_id',$request->department_id)
            ->update([
                'status'=>2
            ]);
        return response()->json([
            'msg'=>'Item successfully issued',
            'status'=>1
        ]);
    }

    public function inventoryReports(Request $request)
    {
        $data = [];
        $facility_id = $request->facility_id;
        $start = $request->start;
        $end = $request->end;
        $sql1 = "SELECT t1.item_name,t1.item_code,t2.batch,t2.quantity,t2.cost_price,t2.supplier FROM tbl_inventory_items t1 INNER JOIN tbl_inventory_receivings t2 ON t2.item_id = t1.id 
                  INNER JOIN tbl_ledgers t3 ON t1.item_type_id = t3.id
                  WHERE t2.updated_at BETWEEN '".$start."' AND '".$end."' AND t3.facility_id ='".$facility_id."'";
        $sql2 = "SELECT t1.item_name,t1.item_code,t2.batch,t4.quantity,t5.department_name FROM tbl_inventory_items t1 INNER JOIN tbl_inventory_receivings t2 ON t2.item_id = t1.id 
                  INNER JOIN tbl_ledgers t3 ON t1.item_type_id = t3.id
                  INNER JOIN tbl_inventory_issuings t4 ON t2.id = t4.item_received_id
                  INNER JOIN tbl_departments t5 ON t5.id = t4.department_id
                  WHERE t2.updated_at BETWEEN '".$start."' AND '".$end."' AND t3.facility_id ='".$facility_id."' GROUP BY t2.batch";
        $data[] = DB::select(DB::raw($sql1));
        $data[] = DB::select(DB::raw($sql2));
        return $data;

    }

    public function sendInventoryRequests(Request $request)
    {
       foreach ($request->all() as $b){
           $data = Tbl_inventory_request::create($b);
       }
        return response()->json([
            'msg'=>'Items successfully requested',
            'status'=>1
        ]);
    }

}