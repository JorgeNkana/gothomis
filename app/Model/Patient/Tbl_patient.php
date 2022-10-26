<?php

namespace App\Model\Patient;

use Illuminate\Database\Eloquent\Model;

class Tbl_patient extends Model
{
    	//use \App\UuidForKey; 
	 protected  $fillable=['first_name','middle_name'
	 ,'last_name','dob','gender','medical_record_number'
	 ,'mobile_number','residence_id','marital_id','occupation_id'
	 ,'tribe_id','country_id','facility_id','user_id','nida'];
	
	 public static $create_rules = [
        'first_name' => 'required',
        'middle_name' => 'required',
        'last_name' => 'required',
        'dob' => 'required',
        'residence_id' => 'required',
        'gender' => 'required',
        'gender' => 'required',
        
    ];
	public static $rules = [
        'email'            => 'required',
        'first_name'       => 'required|min:3',
        'mobile_number'    => 'required|min:10',
        'gender'           => 'required',
        'facility_id'      => 'required',
		'medical_record_number' =>'required|unique:tbl_patients',
    ];
}