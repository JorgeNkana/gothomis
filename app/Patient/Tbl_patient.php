<?php

namespace App\Patient;

use Illuminate\Database\Eloquent\Model;
use Emadadly\LaravelUuid\Uuids;

class Tbl_patient extends Model
{
    //
	//use \App\UuidForKey; 
	 protected  $fillable=['first_name','middle_name'
	 ,'last_name','dob','gender','medical_record_number'
	 ,'mobile_number','residence_id','marital_id','occupation_id'
	 ,'tribe_id','country_id','facility_id','user_id'];
}