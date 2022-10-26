<?php

namespace App\psychiatric;

use Illuminate\Database\Eloquent\Model;

class Tbl_past_psych_record extends Model
{
	//use \App\UuidForKey; 
     protected $fillable = ['patient_id','past_psychiatric_history','visit_date_id','user_id'];
}