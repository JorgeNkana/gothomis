<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_vital_sign extends Model
{
	//use \App\UuidForKey; 
     protected  $fillable=['vital_sign_id','time_taken','date_taken','vital_sign','vital_sign_value','visiting_id','registered_by'];
}