<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_instruction extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['instructions','prescriptions','admission_id','patient_id','bed_id','ward_id','time_flow_id','facility_id','user_id'];
}