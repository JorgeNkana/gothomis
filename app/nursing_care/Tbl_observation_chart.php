<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_observation_chart extends Model
{
	//use \App\UuidForKey; 
 protected  $fillable=['observation_type_id','admission_id','observed_amount'];
}