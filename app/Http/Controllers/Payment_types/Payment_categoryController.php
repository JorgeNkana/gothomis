<?php

namespace App\Http\Controllers\payment_types;

use App\Payment_types\Tbl_pay_cat_sub_category;
use App\Payment_types\Tbl_payments_category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Payment_categoryController extends Controller
{
    //
    // CRUD
    public function payment_category_registration(Request $request)
    {
        $category_description=$request['category_description'];
        $data=Tbl_payments_category::where('category_description',$category_description)->get();
        if(count($data)==1){
            return  $category_description.' '.'Exists...';
        }
        else{
            Tbl_payments_category::create($request->all());
            return 'Send ......';
        }

    }

    public function payment_category_list()
    {
        return  Tbl_payments_category::get();
    }


    public function payment_category_delete($id)
    {

        return Tbl_payments_category::destroy($id);

    }

    public function payment_category_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_payments_category::where('id',$id)->update($request->all());
    }


    // payment Sub Category CRUD
    public function payment_sub_category_registration(Request $request)
    {
        $sub_category_name=$request['sub_category_name'];
        $facility=$request['facility_id'];
        $data=Tbl_pay_cat_sub_category::where('sub_category_name',$sub_category_name)
            ->where('facility_id',$facility)
            ->get();
        if(count($data)==1){
            return  $sub_category_name.' '.'Exists...';
        }
        else{
            Tbl_pay_cat_sub_category::create($request->all());
            return 'Send ......';
        }

    }

    public function payment_sub_category_list()
    {
        return  Tbl_pay_cat_sub_category::get();
    }


    public function payment_sub_category_delete($id)
    {

        return Tbl_pay_cat_sub_category::destroy($id);

    }

    public function payment_sub_category_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_pay_cat_sub_category::where('id',$id)->update($request->all());
    }



}