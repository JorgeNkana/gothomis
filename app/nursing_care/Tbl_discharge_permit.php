<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_discharge_permit extends Model
{
	//use \App\UuidForKey; 
  protected  $fillable=['confirm','followup_date','admission_id','domestic_dosage','permission_date','nurse_id'];
}