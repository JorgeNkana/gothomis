<?php

namespace App\Http\Controllers\Item_setups;

use App\classes\patientRegistration;
use App\Item_setups\Tbl_item;
use App\Item_setups\Tbl_item_category;
use App\Item_setups\Tbl_item_price;
use App\Item_setups\Tbl_item_sub_department;
use App\Item_setups\Tbl_item_type_mapped;
use App\nursing_care\Tbl_registrar_service;
use App\Sub_department\Tbl_sub_department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class ItemsController extends Controller
{
    //
    public function item_registration(Request $request)
    {
        $item_name=$request['item_name'];

        $check= Tbl_item::where('item_name',$item_name)

            ->get();
        if(count($check)==1)
        {
            return  $item_name." "."Already Registered";
        }
        else{
           return Tbl_item::create($request->all());


        }
         
    }

    public function item_list()
    {

        return  DB::table('tbl_departments')
            ->join('tbl_items','tbl_items.dept_id','=','tbl_departments.id')
            ->get();
    }
    public function getsub_department_list()
    {

       return Tbl_sub_department::get();
    }

    public function load_item_list_search(Request $request)
    {
        
$item_id=$request['item_id'];
        return  DB::table('tbl_departments')
            ->join('tbl_items','tbl_items.dept_id','=','tbl_departments.id')
            ->where('tbl_items.id',$item_id)
            ->get();
    }

    public function load_sub_dept_item_list_search(Request $request)
    {

$item_id=$request['item_id'];
   return     DB::table('tbl_items')
            ->join('tbl_item_sub_departments','tbl_items.id','=','tbl_item_sub_departments.item_id')
            ->join('tbl_sub_departments','tbl_sub_departments.id','=','tbl_item_sub_departments.sub_dept_id')
            ->where('tbl_item_sub_departments.item_id',$item_id)
            ->select('tbl_item_sub_departments.*','tbl_sub_departments.sub_department_name','tbl_items.item_name')
            ->get() ;
    }

    public function item_searching($item)
    {

        return  DB::table('tbl_departments')
            ->join('tbl_items','tbl_items.dept_id','=','tbl_departments.id')
            ->where('item_name','like','%'.$item.'%')
            ->limit(10)
            ->get();
    }

    public function item_sub_department_registry(Request $request)
    {

        $serch= Tbl_item_sub_department::where('item_id',$request['item_id'])->get();
        if(count($serch)>0){
            return response()->json([
                'msg' => 'Duplication detected....',
                'status' => 0
            ]);

        }

        Tbl_item_sub_department::create($request->all());
        return response()->json([
            'msg' => 'Saved',
            'status' => 1
        ]);
    }

    public function item_delete($id)
    {

        return Tbl_item::destroy($id);

    }

    public function item_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_item::where('id',$id)->update([
              'item_name'=>  $request['item_name'],
              'dept_id'=>  $request['dept_id'],
        ]

        );
    }
    public function sub_item_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_item_sub_department::where('id',$id)->update([
              'item_id'=>  $request['item_id'],
              'sub_dept_id'=>  $request['sub_dept_id'],
        ]

        );
    }

    //item_category
    public function item_category_registration(Request $request)
    {
        $item_category_name=$request['item_category_name'];

        $check= Tbl_item_category::where('item_category_name',$item_category_name)

            ->get();
        if(count($check)==1)
        {
            return  $item_category_name." "."Already Registered";
        }
        else{
            Tbl_item_category::create($request->all());

            return  "SuccessFul!!!..";
        }

    }




    public function item_category_list()
    {

        return Tbl_item_category::get();

    }
    public function item_category_delete($id)
    {

        return Tbl_item_category::destroy($id);

    }

    public function item_category_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_item_category::where('id',$id)->update([
              'item_category_name'=>  $request['item_category_name']

        ]

        );
    }

     public function item_exemption_set(Request $request)
    {
      $status= $request->status;
       $type= $request->type;;
        $items= $request->items;
        if($type=='exempted'){
            foreach ($items as $item){
                Tbl_item_price::where('item_id',$item['item_id'])->where('facility_id',$item['facility_id'])->update([
                    'exemption_status'=>$status
                ]);
            }
            if($status==1){
                return response()->json([
                    'msg' => "Successful...! This Item(s) has no Exemption"
                    , 'status'=>1
                ]);
            }

            if($status==0){
                return response()->json([
                    'msg' => "Successful...! This Item(s) has  Exemption "
                    , 'status'=>1
                ]);
            }
        }
        elseif ($type=='onetime'){
            foreach ($items as $item){
                Tbl_item_price::where('item_id',$item['item_id'])->where('facility_id',$item['facility_id'])->update([
                    'onetime'=>$status
                ]);
            }
            if($status==1){
                return response()->json([
                    'msg' => "Successful...! This Item(s) Has Set To  a One Time Payment "
                    , 'status'=>1
                ]);
            }

            if($status==0){

                return response()->json([
                    'msg' => "Successful...! This Item(s) Has Set To a None One Time Payment"
                    , 'status'=>1
                ]);

            }
        }

        elseif ($type=='insurance'){

            foreach ($items as $item){
                if($request->has('insurances')){
					foreach($request->insurances as $insurance)
						Tbl_item_price::where('item_id',$item['item_id'])
							->where('facility_id',$item['facility_id'])
							->where('sub_category_id',$insurance['sub_category_id'])
							->update(['insurance'=>$status
						]);
				}else
					Tbl_item_price::where('item_id',$item['item_id'])->where('facility_id',$item['facility_id'])
                    ->where('sub_category_id',$item['sub_category_id'])->update([
                    'insurance'=>$status
                ]);
            }
            if($status==1){
                return response()->json([
                    'msg' => "Successful...! This Item(s) Has   Respective Insurance Beneficiary"
                    , 'status'=>1
                ]);
            }

            if($status==0){
                return response()->json([
                    'msg' => "Successful...! This Item(s) Has Not Respective Insurance Beneficiary "
                    , 'status'=>1
                ]);
            }
        }


    }

    public function item_registrar_set(Request $data)
    {

        foreach ($data->all() as $request){

            $item_id=$request['item_id'];
            $facility_id=$request['facility_id'];
           $check= Tbl_registrar_service::where('service_id',$item_id)
		                                 ->where('facility_id',$facility_id)->get();
            if(count($check)>0){
 return response()->json([
            'msg' => "Sorry,This Item(s) already Set as Reception Service"
            , 'status'=>0
        ]);
            }
            else{
                Tbl_registrar_service::create(['service_id'=>$item_id,'facility_id'=>$facility_id]);
            }

        }

        return response()->json([
            'msg' => "Successful...! This Item(s) Set as Reception Service"
            , 'status'=>1
        ]);
    }

    public function Sub_depts_items_list()
    {
      return DB::table('tbl_items')
          ->join('tbl_item_sub_departments','tbl_items.id','=','tbl_item_sub_departments.item_id')
          ->join('tbl_sub_departments','tbl_sub_departments.id','=','tbl_item_sub_departments.sub_dept_id')
          ->select('tbl_item_sub_departments.*','tbl_sub_departments.sub_department_name','tbl_items.item_name')
          ->get() ;
    }

    public function change_category(Request $request)
    {
       $id=$request['id'];
       $item_category_name=$request['item_category_name'];
        Tbl_item_type_mapped::where('item_id',$id)->update(['item_category'=>$item_category_name]);
        return response()->json([
            'msg' => "Successful...! This Item(s) Changed"
            , 'status'=>1
        ]);
    }
}