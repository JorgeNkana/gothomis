<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_instruction extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['instructions','ward_id','user_id','bed_id','prescriptions','patient_id','admission_id','facility_id'];
}