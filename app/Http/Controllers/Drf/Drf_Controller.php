<?php

namespace App\Http\Controllers\Drf;

use App\classes\patientRegistration;
use App\Drf\Tbl_drf_category;
use App\Drf\Tbl_payment;
use App\Drf\Tbl_drf_reconcilliation;
use App\Drf\Tbl_product_price;
use App\Drf\Tbl_product_registry;
use App\Drf\Tbl_sale;
use App\Drf\Tbl_drf_sale_stock_balance;
use App\Drf\Tbl_drf_sale_stock;
use App\Drf\Tbl_stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Drf_Controller extends Controller
{
    //

    public function SaveNewProduct(Request $request)
    {
        Tbl_product_registry::create($request->all());
        return response()->json([
            'msg'=>'Product Saved Successful',
            'status'=>1
        ]);
    }

    public function SaveNewCategory(Request $request)
    {
        Tbl_drf_category::create($request->all());
        return response()->json([
            'msg'=>'Product Saved Successful',
            'status'=>1
        ]);
    }

    public function EditCategory(Request $request)
    {
        $id=$request->all()['id'];
        Tbl_drf_category::where('id',$id)->update($request->all());
        return response()->json([
            'msg'=>'Product Updated Successful',
            'status'=>1
        ]);
    }
    public function SaveProductUpdate(Request $request)
    {
        $id=$request->all()['id'];
        Tbl_product_registry::where('id',$id)->update($request->all());
        return response()->json([
            'msg'=>'Product Updated Successful',
            'status'=>1
        ]);
    }

    public function LoadCategories(Request $request)
    {

        return Tbl_drf_category::get();

    }
    public function DrfProducts(Request $request)
    {

        return Tbl_product_registry::get();

    }
    public function DrfProductsToPriceSet(Request $request)
    {

        return DB::select('SELECT *FROM tbl_product_registries');

    }

    public function SaveProductPrice(Request $request)
    {
        foreach ($request->all() as $item){
            Tbl_product_price::create($item);
        }

        return response()->json([
            'msg'=>'Product Saved Successful',
            'status'=>1
        ]);
    }

    public function SaveProductPriceUpdate(Request $request)
    {
        $id=$request->all()['id'];
        Tbl_product_price::where('id',$id)->update(['status'=>0]);
        Tbl_product_price::create($request->all());
        return response()->json([
            'msg'=>'Product Updated Successful',
            'status'=>1
        ]);
    }

    public function DrfPrices(Request $request)
    {

        return Tbl_product_price::where('status',1)->get();

    }
    public function reloadInvoices(Request $request)
    {
DB::statement("
ALTER TABLE tbl_stocks ADD  column if not exists issued_quantity DECIMAL NULL AFTER pending_balance;
ALTER TABLE `tbl_sales`  ADD column if not exists `payment_type` INT(1) NULL  AFTER `item_name`;

CREATE TABLE if not exists `tbl_drf_reconcilliations` (
  `id` int(10) UNSIGNED NOT NULL  PRIMARY KEY AUTO_INCREMENT,
  `old_quantity` double NOT NULL,
  `current_quantity` double NOT NULL,
  `reason` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `stock_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);
 
 CREATE TABLE  if not exists `tbl_drf_sale_stocks` (
  `id` int(10) UNSIGNED NOT NULL  PRIMARY KEY AUTO_INCREMENT,
  `expiry_date` date NOT NULL,
  `quantity` double NOT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);
 
  
CREATE TABLE if not exists `tbl_drf_sale_stock_balances` (
  `id` int(10) UNSIGNED NOT NULL  PRIMARY KEY AUTO_INCREMENT,
  `balance` double NOT NULL,
  `pending_balance` decimal(10,0) DEFAULT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

 
 
 
    ");
        return Tbl_sale::where('payment_status','UNPAID')->groupBy('invoice_number')->get();

    }
    public function ViewInvoice(Request $request)
    {

        return Tbl_sale::where('invoice_number',$request->all()['invoice_number'])->where('payment_status','UNPAID')->get();

    }
    public function LoadPriceTag(Request $request)
    {
        if ($request->all()['category']=="PAID" ){
            $category="COST SHARING";
        }
        else  if ($request->all()['category']=="UNPAID"){
            $category="WHOLE SALE";
        }
        else{
            $category=$request->all()['category'];
        }


        return Tbl_product_price::where('item_id',$request->all()['id'])->where('category',$category)->where('status',1)->get();

    }
    public function LoadBatchbalance(Request $request)
    {
        return DB::select("SELECT * from  tbl_stocks WHERE item_id='".$request->all()['id']."' AND quantity>0 AND control_out='l' and (timestampdiff(day,expiry_date,CURRENT_TIMESTAMP)<=0) GROUP BY quantity,item_id,batch_number order by quantity desc");

    }
    public function SaveNewSale(Request $request)
    {
        $type= $request["type"];
        $salestatement="Cost amount";
        if ($request["data"][0]['payment_status']=="PAID" && $request["type"]==1){
            $salestatement=  "Cost Paid";
        }

        if ($request["type"]==2){
            $payment_status="UNPAID";
        }
        else if ($request["type"]==3){
            $payment_status=$request["data"][0]['category'];
        }
        else{
           $payment_status= $request["data"][0]['payment_status'];
        }
        
        $invoiceNo=str_limit($request["data"][0]['buyer_name'],6,null).Date('mYdHim');
        
        foreach ($request->data as $item){ 
            Tbl_sale::create(
                [
                    'item_id'=>$item['item_id'],
                    'nhif_id'=>$item['nhif_id'],
                    'auth_no'=>$item['auth_no'],
                    'item_name'=>$item['item_name'],
                    'unit_price'=>$item['unit_price'],
                    'quantity'=>$item['quantity'],
                    'expiry_date'=>$item['expiry_date'],
                    'invoice_number'=>$invoiceNo,
                    'payment_type'=>$type,
                    'payment_status'=> $payment_status,
                    'buyer_name'=>$item['buyer_name'],
                    'seller_name'=>$item['seller_name'],
                    'batch_number'=>$item['batch_number'],
                    'user_id'=>$item['user_id'],
                ]); 
            
            $itemBakaa=Tbl_drf_sale_stock_balance::where('item_id',$item['item_id'])->where('batch_number', $item['batch_number'])->first();
            
            if ( $payment_status=="UNPAID"){
                $pending_balance=$itemBakaa['pending_balance']+$item['quantity'];
                
                Tbl_drf_sale_stock_balance::where('item_id',$item['item_id'])->where('batch_number', $item['batch_number'])->update([
                'balance'=>$item['balance_remained'],
                'pending_balance'=>$pending_balance,
                ]);
            }
            else{
                Tbl_drf_sale_stock_balance::where('item_id',$item['item_id'])
                    ->where('batch_number', $item['batch_number'])
                    ->update(['balance'=>$item['balance_remained'],]);  
            }
        }
        
        return response()->json([
            'msg'=>'Sale Processed Successful',
            'status'=>1,
            'invoice_number'=>$invoiceNo,
            'statement'=>$salestatement
        ]);
    }

    public function CancelDrfGepgCell(Request $request)
    {
        $invoice_number= $request["invoice_number"];
        
        $sql = "update tbl_drf_sale_stock_balances join tbl_sales ON tbl_sales.invoice_number = '$invoice_number' and tbl_drf_sale_stock_balances.item_id = tbl_sales.item_id and tbl_drf_sale_stock_balances.batch_number = tbl_sales.batch_number SET balance = balance + quantity; update tbl_sales set payment_status='UNPAID' WHERE invoice_number='$invoice_number';";  
        
        DB::statement($sql);
        
        return response()->json([
            'msg'=>'Sale Successfully Cancelled',
            'status'=>1,
        ]);
    }

    public function getReceiptData(Request $request)
    {

        return Tbl_sale::where("invoice_number",$request->invoice_number)->get();

    }
    public function freezeInvoice(Request $request)
    {
        $invoiceN=$request->all()['invoice_number'];

        $invData=Tbl_sale::where('invoice_number',$invoiceN)->where('payment_status','UNPAID')->get();
        if(count($invData)>0) {


            foreach ($invData as $item) {
                $itemBakaa = Tbl_drf_sale_stock_balance::where('item_id', $item['item_id'])->first();
$quantity=$item['quantity'];
$pending=$itemBakaa['pending_balance'];
  $returned=($pending-$quantity);
  $balance=($itemBakaa['balance'] + $item['quantity']);
 $pending_balance=$returned;
                Tbl_drf_sale_stock_balance::where('item_id', $item['item_id'])->
                update([
                    'balance' => $balance,   
                    'pending_balance' => $pending_balance,   
                ]);


            }
            Tbl_sale::where('invoice_number', $invoiceN)->update([
                'payment_status' => 'FREEZED'
            ]);
           
        }
        else{


            return response()->json([
                'msg' => 'Invoice # <b style="color: red">' . $invoiceN . ' Already  Freezed</b>  ',
                'status' => 0
            ]);
        }
         return response()->json([
                'msg' => 'Invoice # <b style="color: red">' . $invoiceN . '</b> Freezed Successful',
                'status' => 1
            ]);
    }
    public function ClearBilledInvoice(Request $request)
    {
        // return $request->all();
        $invoiceN=$request->all()['invoice_number'];
        if ($request->all()['cost_amount']==null){
            return response()->json([
                'msg'=>'Please Enter Amount Paid..',
                'status'=>0
            ]);
        }

        if ($request->all()['payment_agent_name']==null){
            return response()->json([
                'msg'=>'Please Enter Payment Agent Name..',
                'status'=>0
            ]);
        } if ($request->all()['payslip']==null){
        return response()->json([
            'msg'=>'Please Enter PaySlip Number..',
            'status'=>0
        ]);
    }if ($request->all()['payer_name']==null){
        return response()->json([
            'msg'=>'Please Enter Payer Name..',
            'status'=>0
        ]);
    }
        if ($request->all()['cost_amount']< $request->all()['cost']){
            return response()->json([
                'msg'=> ' Amount Required to Pay: <b style="font-family: Tahoma">'.$request->all()['cost'].'</b> <p></p> Amount Entered as payment: <b style="font-family: Tahoma" >' .$request->all()['cost_amount'].'</b><p></p> Amount Missing : <b style="font-family: Tahoma;color: red"> '.($request->all()['cost'] - $request->all()['cost_amount']),
                'status'=>0
            ]);
        }
        if ($request->all()['cost_amount']> $request->all()['cost']){
            return response()->json([
                'msg'=> ' Amount Required to Pay: <b style="font-family: Tahoma">'.$request->all()['cost'].'</b> <p></p> Amount Entered as payment: <b style="font-family: Tahoma" >' .$request->all()['cost_amount'].'</b><p></p> Amount Exceeding : <b style="font-family: Tahoma;color: red"> '.($request->all()['cost_amount'] - $request->all()['cost']),
                'status'=>0
            ]);
        }

        $datacheck= Tbl_sale::where('invoice_number',$invoiceN)->where('payment_status','PAID')->take(2)->get();
        if(count($datacheck)==0){
            $data= Tbl_sale::where('invoice_number',$invoiceN)->update([
                'payment_status'=>'PAID'
            ]);
            if($data){
                Tbl_payment::create([
                    'invoice_number'=>$invoiceN,
                    'cost_amount'=>$request->all()['cost_amount'],
                    'payment_status'=>'PAID',
                    'payer_name'=>$request->all()['payer_name'],
                    'payslip'=>$request->all()['payslip'],
                    'payment_agent_name'=>$request->all()['payment_agent_name'],
                ]);
                return response()->json([
                    'msg'=>'Invoice Bill # <b style="color: green">'.$invoiceN.'</b> Cleared Successful',
                    'status'=>1,
                    'statement'=>"Total  Cost Paid"

                ]);
            }
        }
        else{
            return response()->json([
                'msg'=>'Invoice Bill # <b style="color: red">'.$invoiceN.'</b> Has already Cleared',
                'status'=>0
            ]);
        }


    }

    public function DeleteProduct(Request $request)
    {
        $id=$request->all()['id'];
        DB::statement("DELETE FROM Tbl_product_registries WHERE id= '".$id."'");
        return  Tbl_product_registry::get();

    }

    public function searchDrfProduct(Request $request)
    {
        $seachkey=$request->all()['searchKey'] ;
        return DB::select("SELECT id as item_id,tbl_product_registries.* FROM tbl_product_registries WHERE item_name like '%$seachkey%' LIMIT 10");
    }

    public function SalesshowSearch(Request $request)
    {
        $seachkey=$request->all()['searchKey'] ;
        return DB::select("SELECT tbl_drf_sale_stock_balances.batch_number, t1.*,t2.*,t2.item_price as unit_price FROM tbl_product_registries t1 join tbl_product_prices t2 on t1.id=t2.item_id join tbl_drf_sale_stock_balances ON t1.id = tbl_drf_sale_stock_balances.item_id WHERE t1.item_name like '%$seachkey%' AND t2.status=1 GROUP BY tbl_drf_sale_stock_balances.item_id, tbl_drf_sale_stock_balances.batch_number LIMIT 10");
    }

    public function SaveNewStock(Request $request)
    {
        // return $request->all();
        foreach ($request->all() as $item ){

            $balance=$item['quantity'];
            $batch_number=$item['batch_number'];
            $itemBakaa=Tbl_stock::where('item_id',$item['item_id'])->where('batch_number',$batch_number)->where('control_out','l')->select('id','quantity','balance')->get();

            if (count($itemBakaa)>0 && $itemBakaa[0]->balance>=0){
                $balance=($itemBakaa[0]->balance + $item['quantity']);
                $quantityIn=($itemBakaa[0]->quantity + $item['quantity']);
                Tbl_stock::where('id',$itemBakaa[0]->id)->update([
                    'balance'=>$balance,
                    'quantity'=>$quantityIn
                ]);
            }
            else{
                Tbl_stock::create([
                    'item_id'=>$item['item_id'],
                    'item_name'=>$item['item_name'],
                    'vendor_name'=>$item['vendor_name'],
                    'invoice_number'=>$item['invoice_number'],
                    'batch_number'=>$item['batch_number'],
                    'expiry_date'=>$item['expiry_date'],
                    'received_date'=>$item['received_date'],
                    'unit_price'=>$item['unit_price'],
                    'quantity'=>$item['quantity'],
                    'user_name'=>$item['user_name'],
                    'user_id'=>$item['user_id'],
                    'balance'=>$balance,
                    'control_in'=>'r',
                    'control_out'=>'l',
                ]);
            }



        }

        return response()->json([
            'msg'=>'Stock Created Successful',
            'status'=>1
        ]);
    }

    public function StockBalance(Request $request)
    {
        return DB::select("SELECT SUM(balance) as stockbalance ,t1.item_code,t2.* FROM tbl_product_registries t1 JOIN  tbl_stocks t2 ON t2.item_id=t1.id WHERE control_out='l' and (timestampdiff(DAY,expiry_date,CURRENT_TIMESTAMP)<1) GROUP BY item_id");
    }

    public function DispStockBalance(Request $request)
    {
        return DB::select("SELECT SUM(balance) as stockbalance ,t1.item_code,t1.item_name FROM tbl_product_registries t1 JOIN  tbl_drf_sale_stock_balances t2 ON t2.item_id=t1.id GROUP BY item_id");
    }

public function LoadItemDispensingbalance(Request $request)
    {
return DB::select("SELECT * FROM  tbl_drf_sale_stock_balances t2 where t2.item_id= $request->id and batch_number='$request->batch_number'");
}
    public function LoadStockExpires(Request $request)
    {


        return DB::select("SELECT t1.item_code,t2.* FROM tbl_product_registries t1 JOIN  tbl_stocks t2 ON t2.item_id=t1.id WHERE (timestampdiff(day,expiry_date,CURRENT_TIMESTAMP)>0) ");
    }
    public function LoadStockDetails(Request $request)
    {
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start=date('Y-m-01 07:00:00');
            $end=date("Y-m-d H:i:s");
        }else{
            $start=$request->start_date;
            $end=$request->end_date;
        }

        return DB::select("SELECT t1.item_code,t2.* FROM tbl_product_registries t1 JOIN  tbl_stocks t2 ON t2.item_id=t1.id WHERE t2.control_in='r' AND t2.created_at between '".$start."' AND '".$end."'");
    }

     public function LoadStockIssuedDetails(Request $request)
    {
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start=date('Y-m-01 07:00:00');
            $end=date("Y-m-d H:i:s");
        }else{
            $start=$request->start_date;
            $end=$request->end_date;
        }

        return DB::select("SELECT t1.item_code,t2.* FROM tbl_product_registries t1 JOIN  tbl_stocks t2 ON t2.item_id=t1.id WHERE t2.control_out='c'  AND t2.created_at between '".$start."' AND '".$end."'");
    }
    public function LoadFinanceDetails(Request $request)
    {
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start=date('Y-m-01 07:00:00');
            $end=date("Y-m-d H:i:s");
        }else{
            $start=$request->start_date;
            $end=$request->end_date;
        }
        $user_id=$request->user_id;
        if ($request->level==true){
            $data[]=DB::select("SELECT t1.*,date(created_at) as issue_date FROM tbl_sales t1  WHERE user_id='".$user_id."' AND created_at between '".$start."' AND '".$end."' AND payment_status='PAID'");
            $data[]= DB::select("SELECT sum(quantity * unit_price) as Amount, month (created_at) as month_value   FROM tbl_sales t1 WHERE created_at between '".$start."' AND '".$end."' AND  payment_status='PAID' group by month (created_at) order by month (created_at) ASC");
            $data[]= DB::select("SELECT t1.*  FROM tbl_sales t1 WHERE created_at between '".$start."' AND '".$end."' AND  payment_status='PAID' group by invoice_number order by invoice_number ASC");
             $data[]= DB::select("SELECT sum(quantity * unit_price) as total,u.name as name  FROM tbl_sales
          t1
          join users u on u.id=t1.user_id
           WHERE  t1.created_at between '".$start."' AND '".$end."' AND  payment_status='PAID' group by u.id order by sum(quantity * unit_price) DESC");
            return $data;
        }
        $data[]=DB::select("SELECT t1.*,date(created_at) as issue_date FROM tbl_sales t1  WHERE created_at between '".$start."' AND '".$end."' AND payment_status='PAID'");
        $data[]= DB::select("SELECT sum(quantity * unit_price) as Amount, month (created_at) as month_value   FROM tbl_sales t1 WHERE created_at between '".$start."' AND '".$end."' AND  payment_status='PAID' group by month (created_at) order by month (created_at) ASC");
        $data[]= DB::select("SELECT t1.*  FROM tbl_sales t1 WHERE created_at between '".$start."' AND '".$end."' AND  payment_status='PAID' group by invoice_number order by invoice_number ASC");
         $data[]= DB::select("SELECT sum(quantity * unit_price) as total,u.name as name  FROM tbl_sales
          t1
          join users u on u.id=t1.user_id
           WHERE t1.created_at between '".$start."' AND '".$end."' AND  payment_status='PAID' group by u.id order by sum(quantity * unit_price) DESC");

        return $data;
    }
    public function LoadFinanceDebts(Request $request)
    {
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start=date('Y-m-01 07:00:00');
            $end=date("Y-m-d H:i:s");
        }else{
            $start=$request->start_date;
            $end=$request->end_date;
        }
        return DB::select("SELECT sum(quantity * unit_price) as cost,t1.* FROM tbl_sales t1  WHERE created_at between '".$start."' AND '".$end."' AND payment_status='UNPAID' group by invoice_number order by created_at asc");

    }


    public function LoadFinanceNHIF(Request $request)
    {

        $start=$request->start_date;
        $end=$request->end_date;
        $category=$request->category;

        return DB::select("SELECT sum(quantity * unit_price) as cost,t1.* FROM tbl_sales t1  WHERE created_at between '".$start."' AND '".$end."' AND payment_status='".$category."' group by invoice_number order by created_at asc");

    }

    public function StockIssued(Request $request)
    {
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start=date('Y-m-01 07:00:00');
            $end=date("Y-m-d H:i:s");
        }else{
            $start=$request->start_date;
            $end=$request->end_date;
        }

        return DB::select("SELECT * FROM tbl_sales t1  WHERE t1.created_at between '".$start."' AND '".$end."'");

    }
    public function getMedicenes(Request $request)
    {


        return DB::select("SELECT * FROM tbl_sales t1  WHERE t1.invoice_number ='".$request->all()['invoice']."'");

    }


    public function DrfIssuing(Request $request)
    {
        foreach ($request->all() as $item ){
            $batch_number=$item['batch_number'];
            $itemBakaa=Tbl_stock::where('item_id',$item['item_id'])->where('batch_number',$batch_number)->where('control_out','l')->first();
 
            $balance=($itemBakaa['balance'] - $item['quantity']);
            $quantityIn=($itemBakaa['quantity'] - $item['quantity']);
            Tbl_stock::where('id',$itemBakaa['id'])->update([ 
                'control_out'=>'c',
                'balance'=>$balance,
                'issued_quantity'=>$item['quantity'],

            ]);
            
            Tbl_stock::create([
                'item_id'=>$item['item_id'],
                'item_name'=>$item['item_name'],
                'vendor_name'=>$itemBakaa['vendor_name'],
                'invoice_number'=>$itemBakaa['invoice_number'],
                'batch_number'=>$batch_number,
                'expiry_date'=>$itemBakaa['expiry_date'],
                'received_date'=>$itemBakaa['received_date'],
                'unit_price'=>$itemBakaa['unit_price'],
                'quantity'=>$quantityIn,
                'user_name'=>$item['user_name'],
                'user_id'=>$item['user_id'],
                'balance'=>$balance,
                'control_out'=>'l',
            ]);

            Tbl_drf_sale_stock::create([
                'item_id'=>$item['item_id'],
                'item_name'=>$item['item_name'],
                'batch_number'=>$batch_number,
                'expiry_date'=>$itemBakaa['expiry_date'],
                'quantity'=>$item['quantity'],
                'user_name'=>$item['receiver_name'],
                'user_id'=>$item['user_id'],
            ]);

            
            $drfbalance=Tbl_drf_sale_stock_balance::where('item_id',$item['item_id'])->where('batch_number',$batch_number)->get();
    
            $balance=$item['quantity'];
            if(count($drfbalance)>0){
                $balance=$item['quantity'] + $drfbalance[0]->balance;  
                Tbl_drf_sale_stock_balance::where('item_id',$item['item_id'])->where('batch_number',$batch_number)->update([
                        'item_id'=>$item['item_id'],
                        'balance'=>$balance,
                    ]);
            }
            else{
              Tbl_drf_sale_stock_balance::create([
                                'item_id'=>$item['item_id'],
                                'batch_number'=>$batch_number,
                                'balance'=>$balance,
                            ]);
            }
        }
        
        return response()->json([
                'msg'=>'Stock issued Successful',
                'status'=>1
            ]);
    }
    
    public function drfreconcilliationReturn(Request $request){
     
    
                Tbl_stock::where('id',$request->recon['stock_id'])->update([ 
                    'balance'=>$request->recon['old_quantity'], 
                ]);
                $id=$request->recon['returned_id'];
     DB::statement("Delete from tbl_drf_reconcilliations where id ='".$id."'");
                
            
//sending items to drf reconcilliation table 
             

 
     return response()->json([
            'msg'=>'Reconcilliation Reversed Successful',
            'status'=>1
        ]);
}
  
  public function drf_stock_reconsilliation(Request $request){
     
   foreach ($request->all() as $item ){
 
                Tbl_stock::where('id',$item['column_id'])->update([ 
                    'balance'=>$item['current_quantity'], 
                ]);
            
//sending items to drf reconcilliation table 
                Tbl_drf_reconcilliation::create([
                    'item_id'=>$item['item_id'],
                    'user_id'=>$item['user_id'],
                    'old_quantity'=>$item['old_quantity'],
                    'current_quantity'=>$item['current_quantity'],
                    'stock_id'=>$item['column_id'],
                    'reason'=>$item['reason'],

                     
                ]);

 
       
       
    }
     return response()->json([
            'msg'=>'Reconcilliation Successful',
            'status'=>1
        ]);
}
  

 public function drfreconcilliationReport(Request $request)
    {
        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start=date('Y-m-01 07:00:00');
            $end=date("Y-m-d H:i:s");
        }else{
            $start=$request->start_date;
            $end=$request->end_date;
        }

        return DB::select("SELECT t2.*,t1.*,t1.id as returned_id FROM tbl_drf_reconcilliations t1 join tbl_stocks t2 on t1.stock_id=t2.id  WHERE t1.created_at between '".$start."' AND '".$end."'");

    }

    public function getGepGPendings(){
        return DB::select('select invoice_number,payment_status, sum(quantity*unit_price) as cost ,buyer_name from tbl_sales where payment_status="UNPAID" and payment_type=2 group by invoice_number');
    }

    public function CancelGepGPendings(Request $request){
        $invoice_number= $request->invoice_number;
        $dataa= Tbl_sale::where("invoice_number",$invoice_number)->get();
        foreach ($dataa as $item) {
            $balance=Tbl_drf_sale_stock_balance::where("item_id",$item['item_id'])->first();
            $balance=($balance->balance + $item['quantity']);
             Tbl_drf_sale_stock_balance::where("item_id",$item['item_id'])->update([
             'balance'=>$balance]);   
        }
        Tbl_sale::where("invoice_number",$invoice_number)->update([
            'payment_status'=>"CANCELLED"]);
    }
}