<?php

namespace App\Http\Controllers\Occupation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Occupation\Tbl_occupation;

class OccupationController extends Controller
{
	//Store Occupation
	public function occupation_registration(Request $request)
    {
	  Tbl_occupation::create($request->all());  
    }
	
	//Get Occupation
	public function getoccupation()
    {
        return Tbl_occupation::get();
    }
	//Updated Occupation
	public function updateoccupation(Request $request)
	{
		
		$id=$request['id'];
        return Tbl_occupation::where('id',$id)->update($request->all());
	}
	//delete Occupation
	public function deleteoccupation($id)
    {
        return Tbl_occupation::destroy($id);
    }
	
}