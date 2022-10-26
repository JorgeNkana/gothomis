<?php

namespace App\Http\Controllers\Region;

use App\Council\Tbl_council;
use App\Council\Tbl_council_type;
use App\Region\Tbl_region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use App\Http\Controllers\Region\RegionsController;

class RegionsController extends Controller
{
    //regions CRUD

    public function store(Request $request)
    {
        $regionname=$request['region_name'];
        $region=Tbl_region::where('region_name',$regionname)->get();
        if(count($region)==1){
          return $regionname ." Exists...."  ;
        }
        else{
            $data=Tbl_region::create($request->all());

            return "Successful Registered";
        }

        
    }

    public function index()
    {
        return Tbl_region::get();
    }
    public function delete($id)
    {

        return Tbl_region::destroy($id);

    }

    public function update_region(Request $request)
    {
        $id=$request['id'];
        return Tbl_region::where('id',$id)->update($request->all());
    }
    
    
    //council_type CRUD
    public function council_type_registration(Request $request)
    {
        $description=$request['description'];
         
        $region=Tbl_council_type::where('description',$description)->get();
        if(count($region)==1){
            return $description ." Exists...."  ;
        }
        else{
            $data=Tbl_council_type::create($request->all());

            return "Successful Registered";
        }

    }

    public function council_type_list()
    {
       return Tbl_council_type::get();
    }


    public function council_type_delete($id)
    {

        $data=Tbl_council_type::destroy($id);
        

    }

    public function council_type_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_council_type::where('id',$id)->update($request->all());
    }


    //councils CRUD
    public function council_registration(Request $request)
    {
        $council_code=$request['council_code'];
        $regionid=$request['regions_id'];
        $council_name=$request['council_name'];
        $region=Tbl_council::
        where('council_code',$council_code)
       -> where('council_name',$council_name)
       -> where('regions_id',$regionid)
            ->get();
        if(count($region)==1){
            return $council_name ." Exists...."  ;
        }
        else{
            $data=Tbl_council::create($request->all());

            return "Successful Registered";
        }

         
    }

    public function council_list()
    {
       return Tbl_council::get();
    }


    public function council_delete($id)
    {

        return Tbl_council::destroy($id);

    }

    public function council_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_council::where('id',$id)->update($request->all());
    }
}