<?php

namespace App\Http\Controllers\Tribe;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tribe\Tbl_tribe;

class TribeController extends Controller
{
	//Store Tribe
	public function tribe_registration(Request $request)
    {
	  Tbl_tribe::create($request->all());  
    }
	
	//Get Tribe
	public function gettribe_name()
    {
        return Tbl_tribe::get();
    }
	
	//Updated Tribe 
	public function updatetribe(Request $request)
	{
		
		$id=$request['id'];
        return Tbl_tribe::where('id',$id)->update($request->all());
	}
	
	//delete tribe
	public function deletetribe($id)
    {
        return Tbl_tribe::destroy($id);
    }
}