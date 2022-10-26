<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_theatre_wait extends Model
{
	//use \App\UuidForKey; 
   protected  $fillable=['admission_id','operation_date','prescriptions','confirm','received','nurse_id','posted_date'];
}