<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_teeth_patient extends Model
{
	//use \App\UuidForKey; 
  protected  $fillable=['erasor','other_information','nurse_id','request_id','teeth_number','dental_id','dental_status','css_class','admission_id'];
}