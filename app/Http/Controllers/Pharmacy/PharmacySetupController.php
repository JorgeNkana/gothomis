<?php

namespace App\Http\Controllers\Pharmacy;

use App\Item_setups\Tbl_item;
use App\Item_setups\Tbl_item_type_mapped;
use App\Pharmacy\Tbl_elmis_item_program_mapping;
use App\Pharmacy\Tbl_invoice;
use App\Pharmacy\Tbl_store_list;
use App\Pharmacy\Tbl_store_list_list;
use App\Pharmacy\Tbl_store_type;
use App\Pharmacy\Tbl_store_request_status;
use App\Pharmacy\Tbl_transaction_type;
use App\Pharmacy\Tbl_user_store_configuration;
use App\Pharmacy\Tbl_vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PharmacySetupController extends Controller
{
    
    //vendors CRUD
    public function vendor_registration(Request $request)

    {
        $vendor_name=$request['vendor_name'];
        $facility_id=$request['facility_id'];

     if($request['vendor_name']==""){

        return response()->json([
            'msg' => " Please fill Vendor Name"
            , 'status'=>0
        ]);
    }
         if($request['vendor_address']==""){

        return response()->json([
            'msg' => " Please fill Vendor Address"
            , 'status'=>0
        ]);
    }if($request['vendor_phone_number']==""){

        return response()->json([
            'msg' => " Please fill Vendor Phone Number"
            , 'status'=>0
        ]);
    }if($request['vendor_contact_person']==""){

        return response()->json([
            'msg' => " Please fill Vendor Contact Person"
            , 'status'=>0
        ]);
    }

           $code= substr( md5(rand()), 0, 7);
        $vendor_code=$facility_id.$code.$facility_id;

        $vendor=Tbl_vendor::where('vendor_name',$vendor_name)
            -> where('facility_id',$facility_id)

            ->get();
        if(count($vendor)==1){

            return response()->json([
                'msg' => $vendor_name ."  Exists...."
                , 'status'=>0
            ]);
        }
        else{
            $data=new Tbl_vendor($request->all());
            $data['vendor_code']=$vendor_code;
            $data->save();
            return response()->json([
                'msg' => $vendor_name ."  Registered"
                , 'status'=>1
            ]);
        }


    }

    public function vendor_list($facility_id)
    {
        return Tbl_vendor::where('facility_id',$facility_id)->get();
    }


    public function vendor_delete($id)
    {

        return Tbl_vendor::where('id',$id)->delete();

    }

    public function vendor_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_vendor::where('id',$id)->update($request->all());
    }




    //invoices CRUD
    public function invoice_registration(Request $request)

    {
        $invoice_number=$request['invoice_number'];
        $vendor_id=$request['vendor_id'];
        if($request['invoice_number']==""){

            return response()->json([
                'msg' => " Please fill Invoice Number"
                , 'status'=>0
            ]);
        }if($request['vendor_id']==""){

            return response()->json([
                'msg' => " Please fill Vendor Name"
                , 'status'=>0
            ]);
        }
        $invoice=Tbl_invoice::
        where('invoice_number',$invoice_number)
            -> where('vendor_id',$vendor_id)

            ->get();
        if(count($invoice)==1){

            return response()->json([
                'msg' => $invoice_number ."  Exists...."
                , 'status'=>0
            ]);
        }
        else{
            $data=Tbl_invoice::create($request->all());

            return response()->json([
                'msg' => $invoice_number ."  Registered"
                , 'status'=>1
            ]);
        }


    }

    public function invoice_list($facility)
    {
//        return DB::table('tbl_vendors')
//            ->join('tbl_invoices','tbl_vendors.id','=','tbl_invoices.vendor_id')
//            ->where('tbl_vendors.facility_id',$facility)
//            ->orderBy('tbl_invoices.id','desc')
//            ->get();


        return $invoice_list =   DB::select(DB::raw("SELECT  * from tbl_vendors
  inner join `tbl_invoices` on `tbl_vendors`.id=`tbl_invoices`.vendor_id
   WHERE tbl_vendors.facility_id='{$facility}'  AND timestampdiff(year ,tbl_invoices.updated_at,CURDATE())<8 ORDER BY tbl_invoices.id DESC "));
    }


    public function invoice_delete($id)
    {

        return Tbl_invoice::where('id',$id)->delete();

    }

    public function invoice_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_invoice::where('id',$id)->update([
                'invoice_number'=>$request['invoice_number']
            ]

        );
    }


    //stores CRUD
    public function store_registration(Request $request)

    {
        $store_number=$request['store_name'];

        $facility_id=$request['facility_id'];
        if($store_number==""){

            return response()->json([
                'msg' => $store_number ." Please fill Store name"
                , 'status'=>0
            ]);
        }
        else if($request['store_type_id']==""){

            return response()->json([
                'msg' => $store_number ." Please fill Store type"
                , 'status'=>0
            ]);
        }
        $store=Tbl_store_list::
        where('store_name',$store_number)
            -> where('facility_id',$facility_id)

            ->get();
        if(count($store)==1){

            return response()->json([
                'msg' => $store_number ."  Exists...."
                , 'status'=>0
            ]);
        }
        else{
            $data=Tbl_store_list::create($request->all());

            return response()->json([
                'msg' => $store_number ."  Registered"
                , 'status'=>1
            ]);
        }


    }

    public function store_list($user)
    {

        return DB::table('tbl_store_types')
            ->join('tbl_store_lists','tbl_store_types.id','=','tbl_store_lists.store_type_id')
            ->join('tbl_user_store_configurations','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
->select('tbl_store_lists.id','tbl_store_lists.store_name','tbl_store_lists.store_type_id','tbl_store_types.store_type_name')
            ->where('tbl_user_store_configurations.user_id',$user)
            ->where('tbl_user_store_configurations.status',1)
            ->get();
    }

 public function storesListToAsignAccess($facility)
    {

        return DB::table('tbl_store_types')
            ->join('tbl_store_lists','tbl_store_types.id','=','tbl_store_lists.store_type_id')

            ->where('tbl_store_lists.facility_id',$facility)
            ->get();
    }


    public function SelectedUserWithStroreAccess($user_id)
    {
        return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_store_types','tbl_store_types.id','=','tbl_store_lists.store_type_id')
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->where('tbl_user_store_configurations.status',1)
            ->select('tbl_store_lists.*','tbl_user_store_configurations.id as access_id')
            ->get();
    }

    public function TargetedStoreUserToReceive($store_id,$facility_id)
    {
        return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('users','tbl_user_store_configurations.user_id','=','users.id')
            ->Where('tbl_user_store_configurations.store_id',$store_id)
            ->Where('users.facility_id',$facility_id)
            ->where('tbl_user_store_configurations.status',1)
            ->select('users.name','users.id as user_targeted_id')
            ->get();
    }

    public function Remove_user_store_access($id)
    {

        return Tbl_user_store_configuration::where('id',$id)->update(['status'=>0]);
    }
    public function store_delete($id)
    {

        return Tbl_store_list::destroy($id);

    }

    public function store_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_store_list::where('id',$id)->update(
            ['store_name'=>$request['store_name'],
            'store_type_id'=>$request['store_type_id']
       ] );
    }




     //store_type CRUD
    public function store_type_registration(Request $request)

    {
        $store_type_number=$request['store_type_name'];

         
        $store_type=Tbl_store_type::
        where('store_type_name',$store_type_number)


            ->get();
        if(count($store_type)==1){

            return response()->json([
                'msg' => $store_type_number ."  Exists...."
                , 'status'=>0
            ]);
        }
        else{
            $data=Tbl_store_type::create($request->all());

            return response()->json([
                'msg' => $store_type_number ."  Registered"
                , 'status'=>1
            ]);
        }


    }

    public function store_type_list()
    {
        return Tbl_store_type::get();
    }


    public function store_type_delete($id)
    {
         
        return Tbl_store_type::destroy($id);

    }

    public function store_type_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_store_type::where('id',$id)->update($request->all());
    }

    
    
    
    //store_request_status CRUD
    public function store_request_status_registration(Request $request)

    {
        $store_request_status_number=$request['store_request_status'];

        
        $store_request_status=Tbl_store_request_status::where('store_request_status',$store_request_status_number)


            ->get();
        if(count($store_request_status)==1){

            return response()->json([
                'msg' => $store_request_status_number ."  Exists...."
               , 'status'=>0

            ]);
        }
        else{
            $data=Tbl_store_request_status::create($request->all());

            return response()->json([
                'msg' => $store_request_status_number ."  Registered"
                , 'status'=>1
            ]);
        }


    }

    public function store_request_status_list()
    {
        return Tbl_store_request_status::get();
    }


    public function store_request_status_delete($id)
    {

        return Tbl_store_request_status::destroy($id);

    }

    public function store_request_status_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_store_request_status::where('id',$id)->update($request->all());
    }


    //pharmacy_transaction_type CRUD
    public function pharmacy_transaction_type_registration(Request $request)

    {
        $pharmacy_transaction_type_number=$request['transaction_type'];


        $pharmacy_transaction_type=Tbl_transaction_type::
        where('transaction_type',$pharmacy_transaction_type_number)


            ->get();
        if(count($pharmacy_transaction_type)==1){

            return response()->json([
                'msg' => $pharmacy_transaction_type_number ."  Exists...."
                , 'status'=>0
            ]);
        }
        else{
            $id=$request['id'];
            $code=$request['code'];
            $coding=  DB::table('tbl_elmis_adjustments')->where('code',$code)->get();
            $transaction_type=$request['transaction_type'];
            $adjustment=$request['adjustment'];
            $description=$coding[0]->description;
            $additive=$coding[0]->additive;
            return Tbl_transaction_type::create(
                ['transaction_type'=>$transaction_type,
                    'adjustment'=>$adjustment,
                    'code'=>$code,
                    'description'=>$description,
                    'additive'=>$additive
                ]
            );
            return response()->json([
                'msg' => $pharmacy_transaction_type_number ."  Registered"
                , 'status'=>1
            ]);
        }


    }

    public function pharmacy_transaction_type_list()
    {
        return Tbl_transaction_type::get();
    }

 public function elmis_transaction_type_list()
    {
        return DB::table('tbl_elmis_adjustments')->get();
    }

    public function pharmacy_transaction_adjustment()
    {
        return Tbl_transaction_type::where('additive','=',true)
          ->  orWhere('additive','=',null)
            ->get();
    }


    public function pharmacy_transaction_type_delete($id)
    {

        return Tbl_transaction_type::destroy($id);

    }

    public function pharmacy_transaction_type_update(Request $request)
    {

        $id=$request['id'];
        $code=$request['code'];
        $coding=  DB::table('tbl_elmis_adjustments')->where('code',$code)->get();
        $transaction_type=$request['transaction_type'];
        $adjustment=$request['adjustment'];
        $description=$coding[0]->description;
        $additive=$coding[0]->additive;
        return Tbl_transaction_type::where('id',$id)->update(
            ['transaction_type'=>$transaction_type,
                'adjustment'=>$adjustment,
                'code'=>$code,
                'description'=>$description,
                'additive'=>$additive
            ]
        );
    }


    public function searchItem(Request $request)
    {
        $searchKey=$request['searchKey'];
       return DB::table('vw_pharmacy_items')
           ->Where('item_name','like','%'.$searchKey.'%')
           ->orWhere('item_category','like','%'.$searchKey.'%')
           ->get();
    }

    public function singleItemUomUpdate(Request $item)
    {

        if (($item['unit_of_measure'] != null)) {
            Tbl_item_type_mapped::where('item_id', $item['item_id'])->update([
                'unit_of_measure' => $item['unit_of_measure'],

            ]);
            return $item['unit_of_measure'];
        }
    }
    public function singleItemCodeUpdate(Request $item)
    {

        if (($item['item_code'] != null)) {
            Tbl_item_type_mapped::where('item_id', $item['item_id'])->update([
                'item_code' => $item['item_code'],

            ]);
            return $item['item_code'];
        }
    }
    public function singleItemMsdProductUpdate(Request $item)
    {

        if (($item['msdp'] != null)) {
            Tbl_item::where('id', $item['item_id'])->update([
                'msd_product' => $item['msdp'],

            ]);
            return $item['msdp'];
        }
    }
    public function UpdateItemDetails(Request $request)
    {
        foreach ($request->all() as $item){
if (is_numeric($item['unit_of_measure'])){

    Tbl_item_type_mapped::where('item_id',$item['item_id'])->update([
        'unit_of_measure'=>$item['unit_of_measure'],

    ]);
}
            if (($item['product_code'] != null)) {
                Tbl_item_type_mapped::where('item_id', $item['item_id'])->update([
                    'item_code' => $item['product_code'],

                ]);
            }



//            Tbl_item::where('id',$item['item_id'])->update([
//                'msd_product'=>$item['msd_product'],
//            ]);
        }
        return response()->json([
            'massage'=>'Item has successful Updated',
            'status'=>200
        ]);
    }

    public function getUserToSetStoreToAccess(Request $request)
    {
        $searchKey=$request['userKey'];
        $facility_id=$request['facility_id'];
       return DB::table('users')
           ->Where('name','like','%'.$searchKey.'%')
           ->orWhere('mobile_number','like','%'.$searchKey.'%')
           ->orWhere('email','like','%'.$searchKey.'%')
           ->Where('facility_id',$facility_id)
           ->get();
    }

    public function store_user_configure(Request $request)
    {
        $filled=0;
        foreach ($request->all() as $user_store){
            $data= Tbl_user_store_configuration::
            where('user_id',$user_store['user_id'])
                ->  where('store_id',$user_store['store_id'])
                ->  where('status',1)
                ->get();
            $scount=count($data);
            if($scount>0) {
                $filled += 1;

            }
            else{
                $data= Tbl_user_store_configuration::create([
                    'user_id'=> $user_store['user_id'],
                    'store_id'=> $user_store['store_id'],
                ]);
            }



        }
        if($filled>0){
            return response()->json([
                'msg'=>'Success full assigned . But  some  Store Assignment Duplication detected and controlled',
                'status'=>0
            ]);
        }

        else{
            return response()->json([
                'msg'=>'Success full assigned',
                'status'=>1
            ]);
        }
    }

    public function store_user_checking(Request $user_store)
    {

	   $data= Tbl_user_store_configuration::
	   where('user_id',$user_store['user_id'])
         ->  where('store_id',$user_store['store_id'])->get();
			$scount=count($data);
        if($scount>0){
            return response()->json([
                'counti'=>$scount,
                'status'=>1
            ]);
        }
        else{
            return response()->json([
                'counti'=>$scount,
                'status'=>0
            ]);
        }

    }

    


    public function Main_stores_List($user_id)
    {
        return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_store_types','tbl_store_types.id','=','tbl_store_lists.store_type_id')
            ->Where('tbl_user_store_configurations.status',1)
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->Where('tbl_store_lists.store_type_id',2)
            ->select('tbl_store_lists.*')
            
            ->get();
    }

    public function Sub_stores_List($user_id)
    {
        return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_store_types','tbl_store_types.id','=','tbl_store_lists.store_type_id')
			->Where('tbl_user_store_configurations.status',1)
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->Where('tbl_store_lists.store_type_id',3)
            ->select('tbl_store_lists.*')

            ->get();
    }

    public function Dispensing_stores_List($user_id)
    {
        return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_store_types','tbl_store_types.id','=','tbl_store_lists.store_type_id')
			->Where('tbl_user_store_configurations.status',1)
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->Where('tbl_store_lists.store_type_id',4)
            ->orWhere('tbl_store_lists.store_type_id',5)
            ->select('tbl_store_lists.*')

            ->get();
    }
    public function Sub_main_stores_List($user_id)
    {
        return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_store_types','tbl_store_types.id','=','tbl_store_lists.store_type_id')
			->Where('tbl_user_store_configurations.status',1)
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->WhereIn('tbl_store_lists.store_type_id',[2,3])
            ->select('tbl_store_lists.*')

            ->get();
    }
public function Sub_dispensing_stores_List($user_id)
    {
        return DB::table('tbl_user_store_configurations')
            ->join('tbl_store_lists','tbl_store_lists.id','=','tbl_user_store_configurations.store_id')
            ->join('tbl_store_types','tbl_store_types.id','=','tbl_store_lists.store_type_id')
			->Where('tbl_user_store_configurations.status',1)
            ->Where('tbl_user_store_configurations.user_id',$user_id)
            ->WhereIn('tbl_store_lists.store_type_id',[4,3])
            ->select('tbl_store_lists.*')

            ->get();
    }

    public function elmisProductProgramMapping( Request $items)
    { 
 foreach ($items->all() as $request){
            $program=$request['program_code'];
            $itemid=$request['item_id'];
            $Productcode=$request['product_code'];
            $check=Tbl_elmis_item_program_mapping::where('program_code',$program)->where('product_code',$Productcode)->get();
            if (count($check)<1){
            $data=  Tbl_elmis_item_program_mapping::create($request);
             Tbl_item::where('id',$itemid)->update(['msd_product'=>1]);
            }
            else{
            $data=  $check=Tbl_elmis_item_program_mapping::where('id',$check[0]->id)->Update([
                    'program_code' =>$program,
                    'product_code' =>$Productcode,
                ]);
                Tbl_item::where('id',$itemid)->update(['msd_product'=>1]);
            }
        }
        return $data;
    }
}