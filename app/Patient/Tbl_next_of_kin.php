<?php

namespace App\patient;

use Illuminate\Database\Eloquent\Model;

class Tbl_next_of_kin extends Model
{
	//use \App\UuidForKey; 
  protected  $fillable=['patient_id','next_of_kin_name','mobile_number','residence_id','relationship'];
}