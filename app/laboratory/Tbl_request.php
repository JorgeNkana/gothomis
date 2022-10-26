<?php

namespace App\laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_request extends Model
{
	//use \App\UuidForKey; 
    //
	protected  $fillable=['visit_date_id','admission_id','requesting_department_id','doctor_id','patient_id'];
}