<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_obs_gyn extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['patient_id','visit_date_id','user_id','facility_id','admission_id'];
}