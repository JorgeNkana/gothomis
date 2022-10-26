<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_intake_observation extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['oral_mils','oral_types_id','admission_id','intravenous_types_id','intravenous_mils'];
}