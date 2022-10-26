<?php

namespace App\Http\Controllers\General;

use App\Model\General\Tbl_result;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
Use Illuminate\Support\Facades\DB;
use Validator;
use Auth;
use JWTAuth;
use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;
class ResultController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }
    /**
     * CREATE RESULTS FROM LABORATORY.
     * @param  Request  $request
     * @return customApiResponse
     */

    public function create(Request $request){
        $data      =  $request->all();
        $date = date('Y-m-d h:i:s');
        $dt = Carbon::now();
        $post_time = $dt->toTimeString();
        $description = $request->input('description');
        $item_id = $request->input('item_id');
        $post_user = $request->input('post_user');
        $sample = $request->input('sample');
        $order_id = $request->input('order_id');
        $validator =  Validator::make($data, Tbl_result::$create_rules);
        if ($validator->fails()) {
            return customApiResponse($data, "Validation Error", 400, $validator->errors()->all());
        }
        $result = Tbl_result::create([
            'order_id' => $order_id,
            'item_id' => $item_id,
            'description' => $description,
            'post_user' => $post_user,
            'sample' => $sample,
            'eraser' => 0,
            'post_time' => $post_time
        ]);
        //$result = Tbl_result::create($data);
        if($result) {
            return customApiResponse($result, 'RESULTS SUCCESSFULLY POSTED', 201);
        } else {
            return customApiResponse($data, 'ERROR', 500);
        }
    }
    public function approveLabResult(Request $request){
        $results         = $request->results;
        $sample_no       = $request->sample_no;
        $order_control   = $request->order_control;
        $verified_by     = $request->verified_by;
        $order_id        =  $request->order_id;
        $ref_id        =  $request->ref_id;
        $item_id        =  $request->item_id;

        Tbl_result::where('order_id', $order_id)
            ->where('item_id', $item_id)->update(['verify_time'=>date("h:i:s"),'verify_user'=>$verified_by,'confirmation_status'=>1]);

        Tbl_order::where('test_id', $item_id)->where('order_id', $order_id)->update(['result_control'=>1]);

        $newData=Tbl_result::where('order_id', $order_id)
            ->where('item_id', $item_id)->get();
        $patient=Tbl_request::where('id',$order_id)->get();
        $patient_id=$patient[0]->patient_id;
        $trackable_id=$newData[0]->id;
        $user_id= $newData[0]->verify_user;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);


        return response()->json(['data' => " RESULTS FOR " . $sample_no . " WERE SUCCESSFULY APRROVED .",
            'status' => 1
        ]);

    }


}