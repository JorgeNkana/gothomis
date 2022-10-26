<?php

namespace App\Http\Controllers\Exemption;

use App\Exemption\Tbl_exemption_status;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExemptionStatusController extends Controller
{

    public function exemption_status_registration(Request $request)
    {
        $exemption_status=$request['exemption_status'];

        $check= Tbl_exemption_status::where('exemption_status',$exemption_status)->get();
        if(count($check)==1)
        {
            return  $exemption_status." "."Already Registered";
        }
        else{
            Tbl_exemption_status::create($request->all());

            return  "SuccessFul!!!..";
        }
        return Tbl_exemption_status::create($request->all());
    }

    public function exemption_status_list()
    {
        return Tbl_exemption_status::get();
    }


    public function exemption_status_delete($id)
    {

        return Tbl_exemption_status::destroy($id);

    }

    public function exemption_status_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_exemption_status::where('id',$id)->update($request->all());
    }

}