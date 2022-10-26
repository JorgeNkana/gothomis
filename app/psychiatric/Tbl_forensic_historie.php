<?php

namespace App\psychiatric;

use Illuminate\Database\Eloquent\Model;

class Tbl_forensic_historie extends Model
{
	//use \App\UuidForKey; 
     protected $fillable = ['patient_id','forensic_history','visit_date_id','user_id'];
}