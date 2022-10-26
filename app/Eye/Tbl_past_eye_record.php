<?php

namespace App\Eye;

use Illuminate\Database\Eloquent\Model;

class Tbl_past_eye_record extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['patient_id','past_medical_history','past_ocular_history','visit_date_id','user_id'];
}