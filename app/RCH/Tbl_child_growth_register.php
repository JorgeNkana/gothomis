<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_child_growth_register extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','weight','height','weightz','weightp','heightp','heightz','followup_date'];
}