<?php

namespace App\Http\Controllers\Items;

use App\Model\Item\Tbl_item_price;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use DB;
class PriceController extends Controller
{

    public function index(Request $request)
    {
        $data     =  $request->all();
        $perPage  =  (isset($data['per_page'])? $data['per_page'] : 25);
        $name     =  (isset($data['name'])? $data['name']: "");
        $user                   = Auth::user();
        $facility = $user->facility_id;
        if (isset($name)) {
             $results = Tbl_item_price::with('item','category')
                ->where('facility_id',$facility)
                ->paginate($perPage);


        } else {
            $results = Tbl_item_price::with('item','category')
                ->where('facility_id',$facility)
                ->paginate($perPage);
        }
        return customApiResponse($results, 'success', 201, null);
    }

    public function loadPrices(){
        $results    =  Tbl_item_price::orderBy('id', 'asc')->get();
        return customApiResponse($results, 'success', 201, null);
    }
    
    public function create(Request $request){
        $data      =  $request->all();
        if(!is_array($data))
            return customApiResponse($data, "INVALID REQUEST DATA. {".GETTYPE($data)."} TYPE RECEIVED, WHEREAS AN {ARRAY} WAS EXPECTED", 402);
        
        $validator =  Validator::make($data, Tbl_item_price::$create_rules);
        if ($validator->fails()) {
            return customApiResponse($data, "VALIDATION ERRORS", 402, $validator->errors()->all());
        }
        
        if(Tbl_item_price::isDuplicate($data))
            return customApiResponse($data, "DUPLICATE! SIMILAR RECORD ALREADY EXISTS", 403);
        
        $result = Tbl_item_price::create($data);
        if($result) {
            return customApiResponse($result, 'SUCCESSFULLY REGISTERED', 201);
        } else {
            return customApiResponse($data, 'ERROR', 400);
        }
    }
    public function edit($id)
    {
        $result = Tbl_item_price::find($id);
        return customApiResponse($result, "success",200);
    }
    public function update(Request $request, $id)
    {
        $data      = $request->all();
        $validator = Validator::make($data, Tbl_item::$rules);
        if ($validator->fails()) {
            return customApiResponse($data, 'VALIDATION ERROR', 401, $validator->errors()->all());
        }
        
        if(Tbl_item_price::isDuplicate($data))
            return customApiResponse($data, "DUPLICATE! SIMILAR RECORD ALREADY EXISTS", 403);
        
        $result =  Tbl_item_price::find($id);
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
        $status = Tbl_item_price::destroy($id);
        if ($status) {
            return customApiResponse('', 'SUCCESSFULLY DELETED',201);
            
        } else {
            return customApiResponse('', 'ERROR OCCURRED DURING DELETING',400);
        }
    }
}