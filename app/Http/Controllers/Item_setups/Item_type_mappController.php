<?php

namespace App\Http\Controllers\Item_setups;

use App\Item_setups\Tbl_item;
use App\Item_setups\Tbl_item_type_mapped;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Item_type_mappController extends Controller
{
    //

    // CRUD
    public function item_type_map_registration(Request $request)
    {
        $item_id=$request['item_id'];
        $category=$request['sub_item_category'];
        $sub_item_category=$request['sub_item_category'];
        $item_code=$request['item_code'];
        $dispensing_unit=$request['dispensing_unit'];
        $unit_of_measure=$request['unit_of_measure'];
        $Dose_formulation=$request['Dose_formulation'];
       $check=Tbl_item_type_mapped::where('item_id',$item_id)
            ->where('item_code',$item_code)
            ->where('dispensing_unit',$dispensing_unit)
            ->where('sub_item_category',$sub_item_category)
            ->where('unit_of_measure',$unit_of_measure)
            ->where('Dose_formulation',$Dose_formulation)
            ->count();

        if($request['item_id']==""){

            return response()->json([
                'msg' => " Please fill Item name"
                , 'status'=>0
            ]);
        }
        if($request['item_category']==""){

            return response()->json([
                'msg' => " Please fill Item Category"
                , 'status'=>0
            ]);
        } if($check>0){

            return response()->json([
                'msg' => " Duplication ........"
                , 'status'=>0
            ]);
        }

        else{
            $data= Tbl_item_type_mapped::create($request->all());
            return response()->json([
                'msg' => " Successful Registered"
                , 'status'=>1
            ]);
        }


    }

    public function item_searching($item)
    {
      return Tbl_item::where('item_name','like','%'.$item.'%')->where('status', 1)->limit(10)->get();

    } 
    public function item_type_map_list()
    {
      return DB::table('vw_concept_dictionery')->get();

    }


    public function item_type_map_delete($id)
    {

        return Tbl_item_type_mapped::destroy($id);

    }

    public function item_type_map_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_item_type_mapped::where('id',$id)->update($request->all());
    }
}