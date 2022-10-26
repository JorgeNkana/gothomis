<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_output_observation extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['observation_output_type_id','nurse_id','treatment_remarks','admission_id','amount','si_units'];
}