<?php

namespace App\Orthopedic;

use Illuminate\Database\Eloquent\Model;

class Tbl_past_orthopedic_history extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','visit_date_id','user_id','past_orthopedic','admission_id','facility_id',''];
}