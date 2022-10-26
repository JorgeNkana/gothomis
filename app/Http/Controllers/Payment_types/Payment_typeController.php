<?php

namespace App\Http\Controllers\Payment_types;


use App\Payment_types\Tbl_payment_status;
use App\Payment_types\Tbl_payment_type;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class Payment_typeController extends Controller
{
    //

    // CRUD
    public function payment_type_registration(Request $request)
    {

        $payment_type_name=$request['payment_type_name'];
        $data=Tbl_payment_type::where('payment_type_name',$payment_type_name)->get();
        if(count($data)==1){
            return  $payment_type_name.' '.'Exists...';
        }
        else{
            Tbl_payment_type::create($request->all());
            return 'Send ......';
        }


    }

    public function payment_type_list()
    {
        return  Tbl_payment_type::get();
    }


    public function payment_type_delete($id)
    {

        return Tbl_payment_type::destroy($id);

    }

    public function payment_type_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_payment_type::where('id',$id)->update($request->all());
    }

    // CRUD
    public function payment_status_registration(Request $request)
    {
 
 $pstatus=$request['payment_status'];
        $data=Tbl_payment_status::where('payment_status',$pstatus)->get();
        if(count($data)==1){
           return  $pstatus.' '.'Exists...';
        }
        else{
             Tbl_payment_status::create($request->all());
            return 'Send ......';
        }

    }

    public function payment_status_list()
    {
        return  DB::table('tbl_payment_statuses')->get();
    }


    public function payment_status_delete($id)
    {

        return Tbl_payment_status::destroy($id);

    }

    public function payment_status_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_payment_status::where('id',$id)->update($request->all());
    }

}