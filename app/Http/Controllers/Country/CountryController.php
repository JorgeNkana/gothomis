<?php

namespace App\Http\Controllers\Country;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Country\Tbl_country_zone;
use App\Country\Tbl_country;

class CountryController extends Controller
{
    //Store country_zone
	public function country_zone_registration(Request $request)
    {
	  Tbl_country_zone::create($request->all());  
    }
	
	//Get country_zones
	public function getcountry_zone()
    {
        return Tbl_country_zone::get();
    }
	
	//Updated country_zones
	public function update_country_zone(Request $request)
	{
		$id=$request['id'];
        return Tbl_country_zone::where('id',$id)->update($request->all());
	}
	
	//delete country_zones
	public function deletecountryzone($id)
    {
        return Tbl_country_zone::destroy($id);
    }
	
	//Store country
	public function country_name_registration(Request $request)
    {
	  Tbl_country::create($request->all());  
    }
	
	//Get country
	public function getcountries()
    {
        return Tbl_country::get();
    }
	
	//Updated Country 
	public function update_country_name(Request $request)
	{
		
		$id=$request['id'];
        return Tbl_country::where('id',$id)->update($request->all());
	}
	
	//delete country
	public function deletecountry($id)
    {
        return Tbl_country::destroy($id);
    }
	
}