<?php

namespace App\Http\Controllers\General;

use App\classes\SystemTracking;
use App\laboratory\Tbl_request;
use Illuminate\Http\Request;
use App\classes\patientRegistration;
use Milon\Barcode\DNS1D;
use stdClass;
use App\laboratory\Tbl_order;
use App\laboratory\Tbl_sample_number_control;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use DB;
class SampleController extends Controller
{
    public function create(Request $request){
        $data      =  $request->all();
        $facility_id = $request->input('facility_id');
        $request_number = $request->input('request_id');
        $receiver_id = $request->input('order_validator_id');
        $order_control = $request->input('order_control');
        $last_name = $request->input('last_name');
        $sub_department_name = $request->input('sub_department');
        $test_name = $request->input('request_id');
        $sample_type = $request->input('sample_type');
        if(!isset($sample_type)){
            return response()->json(
                ['data' => 'Please Enter Sample Type',
                    'status' => 0
                ]
            );

        }
        $sample_number=patientRegistration:: labOrderNumber($facility_id);
        $Barcodes = new DNS1D();
        $sample_number_barcode=ltrim($sample_number, '0');
        $sample_number_barcode=(float)$sample_number_barcode;
        $barrcode=$Barcodes->getBarcodePNG($sample_number_barcode,"C39");

        if(Tbl_order::where('id',$request_number)->UPDATE(array(
            'order_control'=>$order_control,
            'order_status'=>1,
            'sample_no'=>$sample_number_barcode,
            'sample_types'=>$sample_type,
            'receiver_id'=>$receiver_id)))
        {
            $create_sample= Tbl_sample_number_control::create([
                'sample_no'=>$sample_number,
                'facility_id'=>$facility_id,
                'user_id'=>$receiver_id]);
            $timecreatedAt=$create_sample->created_at;
            $newData=$create_sample;
            $ord=Tbl_order::where('id',$request_number)->get();
            $patient=Tbl_request::where('id',$ord[0]->order_id)->get();
            $patient_id=$patient[0]->patient_id;
            $trackable_id=$newData->id;
            $user_id= $receiver_id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,null);

            return response()->json(
                ['sample_number' => $sample_number_barcode,
                    'barcode' => $barrcode,
                    'last_name' => substr($last_name, 0, 4),
                    'sub_department_name' => substr($sub_department_name, 0, 4),
                    'test_name' => substr($test_name, 0, 4),
                    'time_generated' => date("d-m-Y h:i:s", strtotime($timecreatedAt)),
                    'status' => 1
                ]
            );

        }

    }



}