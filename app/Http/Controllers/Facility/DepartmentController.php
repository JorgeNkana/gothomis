<?php

namespace App\Http\Controllers\Facility;

use App\Department\Tbl_department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{


    // CRUD
    public function department_registration(Request $request)
    {
        $department=$request['department_name'];
        $check= Tbl_department::where('department_name',$department)->get();
        if(count($check)==1)
        {
            return  $department." "."Already Registered";
        }
        else{
            Tbl_department::create($request->all());

            return  "SuccessFul!!!..";
        }
    }

    public function department_list()
    {
        return  Tbl_department::get();
    }


    public function department_delete($id)
    {

        return Tbl_department::destroy($id);

    }

    public function department_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_department::where('id',$id)->update($request->all());
    }

}