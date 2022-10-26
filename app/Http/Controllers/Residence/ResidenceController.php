<?php

namespace App\Http\Controllers\Residence;

use App\Residence\Tbl_residence;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ResidenceController extends Controller
{
    //
     

    // CRUD
    public function residence_registration(Request $request)

    {
        $residence_name=$request['residence_name'];
        $council_id=$request['council_id'];
        $region=Tbl_residence::
        where('residence_name',$residence_name)
            -> where('council_id',$council_id)

            ->get();
        if(count($region)==1){
            return $residence_name ."  Exists...."  ;
        }
        else{
            $data=Tbl_residence::create($request->all());

            return "Successful Registered";
        }


    }

    public function residence_list()
    {
        return DB::table('vw_residences')->get();
    }


    public function residence_delete($id)
    {

        return Tbl_residence::destroy($id);

    }

    public function residence_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_residence::where('id',$id)->update($request->all());
    }
}