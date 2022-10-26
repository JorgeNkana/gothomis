<?php

namespace App\Http\Controllers\Marital;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Marital\Tbl_marital;

class MaritalController extends Controller
{
    //Store Marital Status
	public function marital_registration(Request $request)
    {  
     return Tbl_marital::create($request->all());  
    }
	
	//Get Marital Status
	public function getmarital_status()
    {
        return Tbl_marital::get();
    }
	//Update Marital Status
	public function updatemaritalstatus(Request $request)
	{
		
		$id=$request['id'];
        return Tbl_marital::where('id',$id)->update($request->all());
	}
	//Delete Marital Status
	public function deletemaritalstatus($id)
    {
        return Tbl_marital::destroy($id);
    }
}