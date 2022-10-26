<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_nursing_care_plan extends Model
{
	//use \App\UuidForKey; 
protected  $fillable=['nurse_diagnosis_id','admission_id','nursing_care_types','targeted_plans','nurse_id'];
}