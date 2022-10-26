<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_admission extends Model
{
	//use \App\UuidForKey; 
protected  $fillable=['account_id','admission_date','patient_id','admission_status_id','user_id','facility_id'];
}