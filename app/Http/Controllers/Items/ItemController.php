<?php

namespace App\Http\Controllers\Items;


use App\Model\Item\Tbl_item;
use App\Model\Department\Tbl_department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use DB;
class ItemController extends Controller
{
    public function index(Request $request)
    {
        $data     =  $request->all();
        $perPage  =  (isset($data['per_page'])? $data['per_page'] : 25);
        $name     =  ((isset($data['name'])? $data['name']: ""));
        if (isset($name)) {
            $results = Tbl_item::with('department')->where('status', 1)->where('item_name', 'like','%' . $name . '%')->paginate($perPage);
        } else {
         return   $results = Tbl_item::with('department')->where('status', 1)->paginate($perPage);
        }
        return customApiResponse($results, 'success', 201, null);
    }
    public function disabled(Request $request)
    {
        $data     =  $request->all();
        $perPage  =  (isset($data['per_page'])? $data['per_page'] : 25);
        $name     =  ((isset($data['name'])? $data['name']: ""));
        if (isset($name)) {
            $results = Tbl_item::with('department')->where('status', 0)->where('item_name', 'like','%' . $name . '%')->paginate($perPage);
        } else {
            return   $results = Tbl_item::with('department')->where('status', 0)->paginate($perPage);
        }
        return customApiResponse($results, 'success', 201, null);
    }

    public function disableNow(Request $request)
    {
         $item_id = $request->input('id');
        $results=   DB::table('Tbl_items')
            ->where('id',$item_id)
            ->update([
                'status'=>0,]);
        return customApiResponse($results, 'success', 201, null);
    }

    public function reDisableNow(Request $request)
    {
        $data = $request->all();
        $item_id = $request->input('id');
        $results=   DB::table('Tbl_items')
            ->where('id',$item_id)
            ->update([
                'status'=>1,]);
        return customApiResponse($results, 'success', 201, null);
    }


    public function getAllDepartments(Request $request)
    {
            $data     =  $request->all();
            $results = Tbl_department::get();
            return customApiResponse($results, 'success', 201, null);
    }
    
    public function loadTbl_items(){
        $results    =  Tbl_item::orderBy('id', 'asc')->get();
        return customApiResponse($results, 'success', 201, null);
    }
    
    public function create(Request $request){
        $data      =  $request->all();
        if(!is_array($data))
            return customApiResponse($data, "INVALID REQUEST DATA. {".GETTYPE($data)."} TYPE RECEIVED, WHEREAS AN {ARRAY} WAS EXPECTED", 402);
        
        $validator =  Validator::make($data, Tbl_item::$create_rules);
        if ($validator->fails()) {
            return customApiResponse($data, "VALIDATION ERRORS", 402, $validator->errors()->all());
        }
        
        if(Tbl_item::isDuplicate($data))
            return customApiResponse($data, "DUPLICATE! SIMILAR RECORD ALREADY EXISTS", 403);
        
        $result = Tbl_item::create($data);
        if($result) {
            return customApiResponse($result, 'SUCCESSFULLY REGISTERED', 201);
        } else {
            return customApiResponse($data, 'ERROR', 400);
        }
    }
    public function edit($id)
    {
        $result = Tbl_item::find($id);
        return customApiResponse($result, "success",200);
    }
    public function update(Request $request, $id)
    {
        $data      = $request->all();
        $validator = Validator::make($data, Tbl_item::$rules);
        if ($validator->fails()) {
            return customApiResponse($data, 'VALIDATION ERROR', 401, $validator->errors()->all());
        }
        
        if(Tbl_item::isDuplicate($data))
            return customApiResponse($data, "DUPLICATE! SIMILAR RECORD ALREADY EXISTS", 403);
        
        $result =  Tbl_item::find($id);
        if ($result == null) {
            return customApiResponse($result, 'DATA YOU ARE TRYING TO UPDATE  NOT FOUND', 404, 'DATA YOU ARE TRYING TO UPDATE  NOT FOUND');
        }
        $result->update($data);
        if ($result) {
            return customApiResponse($result, 'SUCCESSFULLY UPDATED', 201);
            
        } else {
            return customApiResponse($result, 'ERROR OCCURRED DURING UPDATING', 400);
        }
    }
    public function destroy($id){
        $status = Tbl_item::destroy($id);
        if ($status) {
            return customApiResponse('', 'SUCCESSFULLY DELETED',201);
            
        } else {
            return customApiResponse('', 'ERROR OCCURRED DURING DELETING',400);
        }
    }
}