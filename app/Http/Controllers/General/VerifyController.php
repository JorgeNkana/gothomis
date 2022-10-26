<?php

namespace App\Http\Controllers\General;

use App\laboratory\Tbl_order;
use App\Model\General\Tbl_result;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use DB;
class VerifyController extends Controller
{
    public function create(Request $request){
        $data      =  $request->all();
        $item_id = $request->input('item_id');
        $order_id = $request->input('order_id');
        $confirmation_status = $request->input('confirmation_status');
        $verify_user = $request->input('verify_user');
        $result =  Tbl_result::where('order_id', $order_id)
            ->where('item_id', $item_id)
            ->update(['verify_time'=>date("h:i:s"),'verify_user'=>$verify_user,'confirmation_status'=>1]);
         Tbl_order::where('test_id', $item_id)->where('order_id', $order_id)->update(['result_control'=>1]);
        if($result) {
            return customApiResponse($result, 'SUCCESSFULLY APPROVED', 201);
           // track_activity($result, Auth::user(), action_name());

        } else {
            return customApiResponse($data, 'ERROR', 500);
        }
    }



}